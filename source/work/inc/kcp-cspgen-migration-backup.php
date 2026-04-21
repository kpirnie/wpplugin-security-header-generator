<?php

/**
 * Migration Backup Notice & Download
 *
 * When KCP_CSPGEN_Migration detects a pre-5.6.03 settings array it writes
 * a snapshot to `wpsh_settings_pre_migration_backup` before transforming
 * the live data.  This class:
 *
 *  • Shows a persistent admin notice (only on the plugin's own pages,
 *    only to manage_options users) while that backup option exists.
 *
 *  • Provides a nonce-protected AJAX handler that streams the raw backup
 *    as a downloadable JSON file (in the original flat format) and then
 *    deletes the option — so the database is not burdened indefinitely.
 *
 * @since   5.6.03
 * @author  Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Security Header Generator
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

if ( ! class_exists( 'KCP_CSPGEN_Migration_Backup' ) ) {

    class KCP_CSPGEN_Migration_Backup {

        // ----------------------------------------------------------------
        // Constants
        // ----------------------------------------------------------------

        private const BACKUP_OPTION = 'wpsh_settings_pre_migration_backup';
        private const AJAX_ACTION   = 'wpsh_download_migration_backup';
        private const NONCE_ACTION  = 'wpsh_migration_backup_download';

        // ----------------------------------------------------------------
        // Bootstrap
        // ----------------------------------------------------------------

        /**
         * Register all hooks.  Call once from plugins_loaded.
         */
        public static function init(): void {

            // Admin notice — only when backup option exists
            add_action( 'admin_notices',            [ self::class, 'maybe_show_notice' ] );

            // Inline JS for the notice (only on our page)
            add_action( 'admin_enqueue_scripts',    [ self::class, 'maybe_enqueue_script' ] );

            // AJAX: authenticated users only (no nopriv variant)
            add_action( 'wp_ajax_' . self::AJAX_ACTION, [ self::class, 'handle_download' ] );
        }


        // ----------------------------------------------------------------
        // Admin notice
        // ----------------------------------------------------------------

        /**
         * Render the notice.
         * Conditions: manage_options, on the plugin's own admin page, backup option exists.
         */
        public static function maybe_show_notice(): void {

            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }

            if ( ! self::is_plugin_page() ) {
                return;
            }

            if ( ! self::backup_exists() ) {
                return;
            }

            $filename = self::build_filename();

            ?>
            <div class="notice notice-warning" id="wpsh-migration-backup-notice">

                <h3><?php esc_html_e( 'Security Header Generator — Pre-Migration Backup', 'security-header-generator' ); ?></h3>

                <p>
                    <?php esc_html_e( 'Before your settings were automatically migrated to the new format, a snapshot of your original configuration was saved to the database.', 'security-header-generator' ); ?>
                </p>

                <p>
                    <?php esc_html_e( 'Please download this backup file and save it somewhere safe.', 'security-header-generator' ); ?>
                    <strong>
                        <?php esc_html_e( 'The file cannot be recovered after download — clicking the button below will permanently delete the backup from the database.', 'security-header-generator' ); ?>
                    </strong>
                </p>

                <p>
                    <button
                        type="button"
                        class="button button-primary"
                        id="wpsh-download-migration-backup"
                        data-nonce="<?php echo esc_attr( wp_create_nonce( self::NONCE_ACTION ) ); ?>"
                        data-filename="<?php echo esc_attr( $filename ); ?>"
                    >
                        <?php esc_html_e( 'Download Backup &amp; Delete', 'security-header-generator' ); ?>
                    </button>
                </p>

            </div>
            <?php
        }


        // ----------------------------------------------------------------
        // Inline script
        // ----------------------------------------------------------------

        /**
         * Enqueue a small inline script that wires up the download button.
         * Only added on the plugin's own page so we don't pollute other screens.
         */
        public static function maybe_enqueue_script( string $hook_suffix ): void {

            if ( ! self::is_plugin_page() ) {
                return;
            }

            if ( ! self::backup_exists() ) {
                return;
            }

            // Depends on nothing — pure vanilla JS, appended to the footer
            wp_register_script(
                'wpsh-migration-backup',
                false,    // inline only — no file
                [],
                null,
                true
            );

            wp_enqueue_script( 'wpsh-migration-backup' );

            $js = <<<'JS'
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {

        var btn = document.getElementById('wpsh-download-migration-backup');
        if (!btn) return;

        btn.addEventListener('click', function () {

            var confirmed = window.confirm(
                'This will download your pre-migration settings backup and permanently delete it from the database.\n\n' +
                'Make sure to save the downloaded file somewhere safe — it cannot be recovered after download.\n\n' +
                'Proceed?'
            );

            if (!confirmed) return;

            var self    = this;
            var nonce   = self.getAttribute('data-nonce');
            var filename = self.getAttribute('data-filename');

            self.disabled    = true;
            self.textContent = 'Downloading\u2026';

            var formData = new FormData();
            formData.append('action', 'wpsh_download_migration_backup');
            formData.append('nonce',  nonce);

            fetch(window.ajaxurl || '/wp-admin/admin-ajax.php', {
                method:      'POST',
                credentials: 'same-origin',
                body:        formData,
            })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Server returned ' + response.status);
                }
                return response.blob();
            })
            .then(function (blob) {
                // Trigger browser download from the blob
                var url = URL.createObjectURL(blob);
                var a   = document.createElement('a');
                a.href     = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                // Remove the notice — backup is gone from the DB
                var notice = document.getElementById('wpsh-migration-backup-notice');
                if (notice) notice.remove();
            })
            .catch(function (err) {
                self.disabled    = false;
                self.textContent = 'Download Backup & Delete';
                window.alert(
                    'Download failed: ' + err.message + '\n\nThe backup is still in the database. Please try again.'
                );
            });
        });
    });
}());
JS;

            wp_add_inline_script( 'wpsh-migration-backup', $js );
        }


        // ----------------------------------------------------------------
        // AJAX download + delete handler
        // ----------------------------------------------------------------

        /**
         * Stream the raw backup JSON as a file download, then delete the option.
         *
         * Uses output buffering to ensure the full JSON is built before any
         * headers are sent, and deletes the option only after the content is
         * ready to be flushed — so a PHP error before the echo leaves the
         * backup intact and the user can retry.
         */
        public static function handle_download(): void {

            // Capability check
            if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error( [ 'message' => 'Permission denied.' ], 403 );
            }

            // Nonce check
            if ( ! check_ajax_referer( self::NONCE_ACTION, 'nonce', false ) ) {
                wp_send_json_error( [ 'message' => 'Invalid nonce.' ], 403 );
            }

            // Fetch backup — if it's gone, tell the client gracefully
            $backup = get_option( self::BACKUP_OPTION, null );

            if ( ! is_array( $backup ) || empty( $backup ) ) {
                wp_send_json_error( [ 'message' => 'No backup found. It may have already been downloaded.' ], 404 );
            }

            // Build JSON — match the original flat format exactly (no envelope wrapper)
            $json = wp_json_encode( $backup, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

            if ( $json === false ) {
                wp_send_json_error( [ 'message' => 'Failed to encode backup data.' ], 500 );
            }

            // All data is ready — safe to delete before we send
            // (if output fails after this point the data is gone, but
            //  the user will see a network error and can contact support)
            delete_option( self::BACKUP_OPTION );

            // Clear any output buffers WordPress or the host may have opened
            while ( ob_get_level() ) {
                ob_end_clean();
            }

            // Send file download headers
            nocache_headers();
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename="' . self::build_filename() . '"' );
            header( 'Content-Length: ' . strlen( $json ) );

            echo $json;

            exit;
        }


        // ----------------------------------------------------------------
        // Helpers
        // ----------------------------------------------------------------

        /**
         * True when viewing any page under the wpsh_settings menu slug.
         */
        private static function is_plugin_page(): bool {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            return is_admin() && ( sanitize_key( $_GET['page'] ?? '' ) === 'wpsh_settings' );
        }

        /**
         * True when the backup option exists and is a non-empty array.
         */
        private static function backup_exists(): bool {
            $backup = get_option( self::BACKUP_OPTION, null );
            return is_array( $backup ) && ! empty( $backup );
        }

        /**
         * Build a datestamped filename for the download.
         */
        private static function build_filename(): string {
            return 'wpsh-pre-migration-backup-' . gmdate( 'Y-m-d-His' ) . '.json';
        }
    }
}