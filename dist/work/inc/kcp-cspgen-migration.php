<?php

/**
 * Settings Migration
 *
 * Handles automatic one-time conversion from the pre-5.6.03 flat
 * wpsh_settings layout to the grouped layout expected by the
 * current headers engine and field framework.
 *
 * Old schema (pre-5.6.03)
 * ─────────────────────────────────────────────────────────────────────
 *  • Every CSP directive source string and its _allow_unsafe array are
 *    stored as top-level keys inside wpsh_settings, e.g.
 *      wpsh_settings['generate_csp_custom_scripts']          = "cdn.example.com"
 *      wpsh_settings['generate_csp_custom_scripts_allow_unsafe'] = ["1","0"]
 *
 *  • All Permissions-Policy directive values are nested under a single
 *    'feature_policies' key, e.g.
 *      wpsh_settings['feature_policies']['fp_accelerometer']             = "1"
 *      wpsh_settings['feature_policies']['fp_accelerometer_src_domain']  = ""
 *
 *  • _allow_unsafe arrays contain stringified integers ("0","1","2","3").
 *
 * New schema (5.6.03+)
 * ─────────────────────────────────────────────────────────────────────
 *  • Each CSP directive pair is wrapped in a sub-array keyed by
 *    'csp_group_{id}', e.g.
 *      wpsh_settings['csp_group_generate_csp_custom_scripts'] = [
 *          'generate_csp_custom_scripts'               => "cdn.example.com",
 *          'generate_csp_custom_scripts_allow_unsafe'  => [1, 0],   // integers
 *      ]
 *
 *  • Each Permissions-Policy directive is its own sub-array keyed by
 *    the directive's 'id' value (fp_accelerometer, fp_icg, …), with
 *    sub-keys using the full directive name, e.g.
 *      wpsh_settings['fp_accelerometer'] = [
 *          'fp_accelerometer'             => "1",
 *          'fp_accelerometer_src_domain'  => "",
 *      ]
 *      wpsh_settings['fp_icg'] = [
 *          'fp_identity-credentials-get'            => "1",
 *          'fp_identity-credentials-get_src_domain' => "",
 *      ]
 *
 *  • Standard-header switches remain as flat boolean keys.
 *  • _allow_unsafe arrays now contain proper integers, not strings.
 *
 * @since   5.6.03
 * @author  Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Security Header Generator
 */

// We don't want to allow direct access to this
defined( 'ABSPATH' ) || die( 'No direct script access allowed' );

if ( ! class_exists( 'KCP_CSPGEN_Migration' ) ) {

    class KCP_CSPGEN_Migration {

        // ----------------------------------------------------------------
        // Constants
        // ----------------------------------------------------------------

        /**
         * wp_options key that records the last completed migration version.
         */
        private const VERSION_OPTION = 'wpsh_settings_schema_version';

        /**
         * The schema version this migration targets.
         * Bump this whenever a future migration is added.
         */
        private const TARGET_VERSION = '6.0.23';


        // ----------------------------------------------------------------
        // Public entry point
        // ----------------------------------------------------------------

        /**
         * Run migration only if the stored schema version is below TARGET_VERSION.
         * Safe to call on every request – work is skipped once the option matches.
         *
         * @return void
         */
        public static function maybe_migrate(): void {

            $stored = (string) get_option( self::VERSION_OPTION, '0' );

            if ( version_compare( $stored, self::TARGET_VERSION, '>=' ) ) {
                return; // Already at current schema – nothing to do.
            }

            $settings = get_option( 'wpsh_settings', [] );

            if ( ! empty( $settings ) && is_array( $settings ) && self::is_old_format( $settings ) ) {

                // Snapshot before we touch anything
                update_option( 'wpsh_settings_pre_migration_backup', $settings, false );

                // migrate to the new format and update the setting
                $migrated = self::transform( $settings );
                update_option( 'wpsh_settings', $migrated );

                // Bust the per-request static cache so the rest of this
                // request sees the migrated data immediately.
                if ( function_exists( 'clear_our_option_cache' ) ) {
                    clear_our_option_cache();
                }
            }

            // Mark schema as current whether or not there was anything to migrate
            // (e.g. fresh install, or already-current data).
            update_option( self::VERSION_OPTION, self::TARGET_VERSION, false );
        }


        // ----------------------------------------------------------------
        // Detection
        // ----------------------------------------------------------------

        /**
         * Returns true when the settings array looks like the pre-5.6.03
         * flat format that needs migration.
         *
         * Markers of the old format:
         *   • Flat top-level CSP directive keys (generate_csp_custom_*)
         *   • 'feature_policies' as a nested array
         */
        private static function is_old_format( array $settings ): bool {

            // Any flat CSP directive key indicates old format
            foreach ( self::grouped_csp_ids() as $id ) {
                if ( array_key_exists( $id, $settings ) ) {
                    return true;
                }
            }

            // Old-style nested feature policies
            if ( isset( $settings['feature_policies'] ) && is_array( $settings['feature_policies'] ) ) {
                return true;
            }

            return false;
        }


        // ----------------------------------------------------------------
        // Orchestration
        // ----------------------------------------------------------------

        /**
         * Full transformation pipeline.
         */
        private static function transform( array $old ): array {

            $new = [];

            // 1. Standard-header fields — keep flat, normalise types
            $new = array_merge( $new, self::migrate_standard_headers( $old ) );

            // 2. CSP directive pairs → csp_group_{id} sub-arrays
            $new = array_merge( $new, self::migrate_csp_directives( $old ) );

            // 3. Flat CSP-adjacent fields (sandbox tokens, report-to)
            $new = array_merge( $new, self::migrate_flat_csp_fields( $old ) );

            // 4. Feature-policy directives → {fp_id} sub-arrays
            $new = array_merge( $new, self::migrate_feature_policies( $old ) );

            return $new;
        }


        // ----------------------------------------------------------------
        // Step 1 – Standard headers
        // ----------------------------------------------------------------

        /**
         * Migrate all non-CSP, non-FP settings.
         * All values that the headers engine reads via get_our_option()
         * as flat keys must remain flat.
         */
        private static function migrate_standard_headers( array $old ): array {

            $new = [];

            // Boolean toggle/switch fields
            foreach ( self::bool_field_keys() as $key ) {
                if ( array_key_exists( $key, $old ) ) {
                    $new[ $key ] = filter_var( $old[ $key ], FILTER_VALIDATE_BOOLEAN );
                }
            }

            // Plain string fields
            foreach ( self::string_field_keys() as $key ) {
                if ( array_key_exists( $key, $old ) ) {
                    $new[ $key ] = (string) $old[ $key ];
                }
            }

            // Access-control allow-methods array
            if ( array_key_exists( 'include_acam_methods', $old ) ) {
                $new['include_acam_methods'] = is_array( $old['include_acam_methods'] )
                    ? $old['include_acam_methods']
                    : [];
            }

            return $new;
        }

        /** All fields whose value must be stored as a PHP bool. */
        private static function bool_field_keys(): array {
            return [
                'apply_to_admin',
                'apply_to_rest',
                'include_sts',
                'include_sts_subdomains',
                'include_sts_preload',
                'include_ofs',
                'include_acam',
                'include_acac',
                'include_acao',
                'include_acah',             // Added in 5.6.03 – may be absent in old data
                'include_aceh',             // Added in 5.6.03
                'include_acma',             // Added in 5.6.03
                'include_mimesniffing',
                'include_referrer_policy',
                'include_download_options',
                'include_crossdomain',
                'include_upgrade_insecure',
                'coep',
                'corp',
                'coop',
                'generate_csp',
                'apply_csp_to_admin',
                'feature_policy',
                'apply_fp_to_admin',
            ];
        }

        /** All fields whose value must be stored as a plain string. */
        private static function string_field_keys(): array {
            return [
                'include_sts_max_age',
                'include_ofs_type',
                'include_acao_origin',
                'include_referrer_policy_setting',
                'coep_setting',
                'corp_setting',
                'coop_setting',
                'auth_un',
                'auth_pw',
                'include_acah_headers',     // Added in 5.6.03
                'include_aceh_headers',     // Added in 5.6.03
                'include_acma_seconds',     // Added in 5.6.03
            ];
        }


        // ----------------------------------------------------------------
        // Step 2 – CSP directives
        // ----------------------------------------------------------------

        /**
         * Wrap each CSP directive's source string + allow_unsafe array
         * inside a 'csp_group_{id}' sub-array.
         *
         * Old:
         *   wpsh_settings['generate_csp_custom_scripts']               = "cdn.example.com"
         *   wpsh_settings['generate_csp_custom_scripts_allow_unsafe']  = ["1","0"]
         *
         * New:
         *   wpsh_settings['csp_group_generate_csp_custom_scripts'] = [
         *       'generate_csp_custom_scripts'              => "cdn.example.com",
         *       'generate_csp_custom_scripts_allow_unsafe' => [1, 0],
         *   ]
         */
        private static function migrate_csp_directives( array $old ): array {

            $new = [];

            foreach ( self::grouped_csp_ids() as $id ) {

                $unsafe_id = $id . '_allow_unsafe';

                $new[ 'csp_group_' . $id ] = [
                    $id        => isset( $old[ $id ] ) ? (string) $old[ $id ] : '',
                    $unsafe_id => self::coerce_allow_unsafe( $old[ $unsafe_id ] ?? [] ),
                ];
            }

            return $new;
        }

        /**
         * IDs of all CSP directives that must be wrapped in a group.
         * (Sandbox and report-to are stored flat and are handled separately.)
         */
        private static function grouped_csp_ids(): array {
            return [
                'generate_csp_custom_baseuri',
                'generate_csp_custom_child',
                'generate_csp_custom_connect',
                'generate_csp_custom_defaults',
                'generate_csp_custom_fonts',
                'generate_csp_custom_forms',
                'generate_csp_custom_frames',
                'generate_csp_custom_frame_ancestors',
                'generate_csp_custom_images',
                'generate_csp_custom_manifests',
                'generate_csp_custom_media',
                'generate_csp_custom_objects',
                'generate_csp_custom_scripts',
                'generate_csp_custom_scripts_elem',
                'generate_csp_custom_scripts_attr',
                'generate_csp_custom_styles',
                'generate_csp_custom_styles_elem',
                'generate_csp_custom_styles_attr',
                'generate_csp_custom_workers',
            ];
        }

        /**
         * Normalise allow_unsafe from an old array-of-strings to array-of-ints.
         *
         * Mapping (unchanged from original design):
         *   0 → 'self'
         *   1 → 'unsafe-inline'
         *   2 → 'unsafe-eval'
         *   3 → 'none'
         *
         * Old storage: ["1", "0"]  (self + inline checked)
         * New storage: [1, 0]
         *
         * @param  mixed $value Raw value from old settings.
         * @return int[]
         */
        private static function coerce_allow_unsafe( mixed $value ): array {

            if ( ! is_array( $value ) || empty( $value ) ) {
                return [];
            }

            return array_values( array_map( 'intval', $value ) );
        }


        // ----------------------------------------------------------------
        // Step 3 – Flat CSP-adjacent fields
        // ----------------------------------------------------------------

        /**
         * Sandbox and report-to are not wrapped in groups — the headers
         * engine reads them as plain flat keys.  Just carry them forward.
         */
        private static function migrate_flat_csp_fields( array $old ): array {

            $new = [];

            // Sandbox: old = "" (nothing selected) or array of token strings
            if ( array_key_exists( 'generate_csp_custom_sandbox', $old ) ) {
                $raw = $old['generate_csp_custom_sandbox'];
                $new['generate_csp_custom_sandbox'] = is_array( $raw ) ? $raw : (string) $raw;
            }

            // Report-to: plain text value
            if ( array_key_exists( 'generate_csp_report_to', $old ) ) {
                $new['generate_csp_report_to'] = (string) $old['generate_csp_report_to'];
            }

            return $new;
        }


        // ----------------------------------------------------------------
        // Step 4 – Feature / Permissions Policy directives
        // ----------------------------------------------------------------

        /**
         * Convert the old nested 'feature_policies' array into the new
         * per-directive sub-array structure.
         *
         * Old:
         *   wpsh_settings['feature_policies'] = [
         *       'fp_accelerometer'                     => "1",
         *       'fp_accelerometer_src_domain'          => "",
         *       'fp_icg'                               => "1",   // abbreviated radio key
         *       'fp_identity-credentials-get_src_domain' => "",  // full directive name
         *       …
         *   ]
         *
         * New:
         *   wpsh_settings['fp_accelerometer'] = [
         *       'fp_accelerometer'            => "1",
         *       'fp_accelerometer_src_domain' => "",
         *   ]
         *   wpsh_settings['fp_icg'] = [
         *       'fp_identity-credentials-get'            => "1",   // full directive name as field id
         *       'fp_identity-credentials-get_src_domain' => "",
         *   ]
         *
         * Key insight: the group is stored under $val['id'] (abbreviated),
         * but the radio sub-field and src-domain sub-field both use the
         * full directive key (fp_{$directive_key}).
         */
        private static function migrate_feature_policies( array $old ): array {

            $new = [];

            // Gather old fp_ values from the nested array (primary source)
            // and fall back to any stray root-level fp_ scalars for resilience.
            $fp_flat = [];

            if ( isset( $old['feature_policies'] ) && is_array( $old['feature_policies'] ) ) {
                $fp_flat = $old['feature_policies'];
            }

            // Absorb root-level fp_ scalars (some very early versions may have
            // stored them flat instead of nested under feature_policies).
            foreach ( $old as $k => $v ) {
                if ( strncmp( $k, 'fp_', 3 ) === 0 && ! is_array( $v ) && ! isset( $fp_flat[ $k ] ) ) {
                    $fp_flat[ $k ] = $v;
                }
            }

            if ( ! class_exists( 'KCP_CSPGEN_Configs' ) ) {
                // Configs class not yet available — skip FP migration.
                // This is a safety net; in normal operation the autoloader
                // will have resolved the class before this method runs.
                return $new;
            }

            $directives = KCP_CSPGEN_Configs::get_permissions_directives();
            foreach ( $directives as $directive_key => $val ) {
                /*
                 * $directive_key  = full directive name, e.g.
                 *                   'accelerometer', 'identity-credentials-get'
                 * $val['id']      = abbreviated group key, e.g.
                 *                   'fp_accelerometer', 'fp_icg'
                 *
                 * New group stored at wp_settings[$val['id']] and contains:
                 *   'fp_{$directive_key}'            → radio value  (0–3)
                 *   'fp_{$directive_key}_src_domain' → source URL text
                 *
                 * Old radio  stored at feature_policies[$val['id']]          (abbreviated key)
                 * Old src    stored at feature_policies[fp_{directive_key}_src_domain] (full key)
                 */
                $group_key = $val['id'];                                 // e.g. 'fp_accelerometer', 'fp_icg'
                $radio_key = 'fp_' . $directive_key;                     // e.g. 'fp_accelerometer', 'fp_identity-credentials-get'
                $src_key   = 'fp_' . $directive_key . '_src_domain';     // e.g. 'fp_accelerometer_src_domain', 'fp_identity-credentials-get_src_domain'

                $new[ $group_key ] = [
                    $radio_key => (string) ( $fp_flat[ $val['id'] ] ?? '1' ),
                    $src_key   => (string) ( $fp_flat[ $src_key ]   ?? '' ),
                ];
            }

            return $new;
        }
    }
}
