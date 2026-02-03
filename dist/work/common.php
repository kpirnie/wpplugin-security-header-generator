<?php

/** 
 * Common Functionality
 * 
 * Control and process the frameworks plugin updates from GitLab
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Security Header Generator
 * 
 */

// We don't want to allow direct access to this
defined('ABSPATH') || die('No direct script access allowed');

// Plugin Activation
register_activation_hook(WPSH_PATH . '/' . WPSH_FILENAME, function ($_network): void {

    // check the PHP version, and deny if lower than 8.1
    if (version_compare(PHP_VERSION, '8.1', '<=')) {

        // it is, so throw and error message and exit
        wp_die(
            esc_html_e('<h1>PHP To Low</h1><p>Due to the nature of this plugin, it cannot be run on lower versions of PHP.</p><p>Please contact your hosting provider to upgrade your site to at least version 8.1.</p>', 'security-header-generator'),
            esc_html_e('Cannot Activate: PHP To Low', 'security-header-generator'),
            array(
                'back_link' => true,
            )
        );
    }

    // check if we tried to network activate this plugin
    if (is_multisite() && $_network) {

        // we did, so... throw an error message and exit
        wp_die(
            esc_html_e('<h1>Cannot Network Activate</h1><p>Due to the nature of this plugin, it cannot be network activated.</p><p>Please go back, and activate inside your subsites.</p>', 'security-header-generator'),
            esc_html_e('Cannot Network Activate', 'security-header-generator'),
            array(
                'back_link' => true,
            )
        );
    }
});

// Plugin De-Activation
register_deactivation_hook(WPSH_PATH . '/' . WPSH_FILENAME, function (): void {});

// make sure this plugin is actually active before we do anything
if (in_array(WPSH_DIRNAME . '/' . WPSH_FILENAME, apply_filters('active_plugins', get_option('active_plugins')))) {

    // make sure this function doesn't already exist
    if (! function_exists('get_our_option')) {

        /** 
         * get_our_option
         * 
         * The method is responsible for getting our options
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @param string $_opt The name of the option to retrieve
         * @param bool $_refresh Force refresh the cache (useful after saving settings)
         * 
         * @return mixed Returns the value from the option, or null if not found
         * 
         */
        function get_our_option(string $_opt, bool $_refresh = false)
        {

            // static variable to cache the options array
            static $_opts_cache = null;

            // if cache is empty or refresh is requested, load from database
            if ($_opts_cache === null || $_refresh) {
                $_opts_cache = get_option('wpsh_settings') ?: array();
            }

            // return the option, or null if it does not exist
            return $_opts_cache[$_opt] ?? null;
        }

        /** 
         * clear_our_option_cache
         * 
         * Clears the cached options - call this after saving settings
         * 
         * @since 5.3.68
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return void
         * 
         */
        function clear_our_option_cache(): void
        {
            // Force refresh on next get_our_option call
            get_our_option('', true);
        }
    }

    // include our autoloader
    include WPSH_PATH . '/vendor/autoload.php';

    // hook into the plugin loaded
    add_action('kpshg_loaded', function (): void {

        // create the settings as well as the menu pages
        $_settings = new KCP_CSPGEN_Settings();

        // run it
        $_settings->kp_cspgen_settings();

        // clean it up
        unset($_settings);
    }, PHP_INT_MAX);

    // hack in some styling
    add_action('admin_enqueue_scripts', function (): void {

        // register the unminified stylesheet
        wp_register_style('kpsh_css', plugins_url('/assets/css/style.css', WPSH_PATH . '/' . WPSH_FILENAME), null, time());

        // register the unminified script
        wp_register_script('kpsh_js', plugins_url('/assets/js/script.js', WPSH_PATH . '/' . WPSH_FILENAME), null, time());

        // enqueue it
        wp_enqueue_style('kpsh_css');

        // enqueue it
        wp_enqueue_script('kpsh_js');
    }, PHP_INT_MAX);

    // we'll need a message in wp-admin for PHP 8 compatibility
    add_action('admin_notices', function (): void {

        // if the site is under PHP 8.2
        if (version_compare(PHP_VERSION, '8.2', '<=')) {

            // show this notice
?>
            <div class="notice notice-info is-dismissible">
                <h3><?php esc_html_e("PHP Upgrade Notice", 'security-header-generator'); ?></h3>
                <p><?php esc_html_e("To maintain the security standards of the Security Header Generator plugin this will be the final version that supports PHP versions lower than 8.2. Your site must be upgraded in order to update the plugin to future versions.", 'security-header-generator'); ?>
                <p><?php esc_html_e("Please see here for up to date PHP version information: https://www.php.net/supported-versions.php", 'security-header-generator'); ?></p>
            </div>
<?php
        }
    }, PHP_INT_MAX);

    // bring in our header functionality and apply them
    $_headers = new KCP_CSPGEN_Headers();

    // run it
    $_headers->kp_process_headers();

    // dump it
    unset($_headers);
}
