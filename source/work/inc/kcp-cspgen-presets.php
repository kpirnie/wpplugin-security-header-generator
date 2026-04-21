<?php

/**
 * CSP Presets
 * 
 * @since 5.3.84
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Security Header Generator
 */

defined('ABSPATH') || die('No direct script access allowed');

if (! class_exists('KCP_CSPGEN_Presets')) {

    /** 
     * Class KCP_CSPGEN_Presets
     * 
     * Provides preset configurations for common use cases
     * 
     * @since 5.3.84
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Security Header Generator
     * 
     */
    class KCP_CSPGEN_Presets
    {

        /**
         * Get all available presets
         * 
         * @access public
         * @static
         * @return array
         */
        public static function get_presets(): array
        {
            return array(
                'wordpress_core' => array(
                    'name' => __('WordPress Core Only (Strict)', 'security-header-generator'),
                    'description' => __('Minimal external sources - only WordPress.org, Gravatar, and Google Fonts. Good starting point for basic sites.', 'security-header-generator'),
                    'settings' => self::wordpress_core_preset(),
                ),
                'woocommerce' => array(
                    'name' => __('WooCommerce Compatible', 'security-header-generator'),
                    'description' => __('WordPress Core plus payment gateways (PayPal, Stripe), common WooCommerce extensions, and analytics.', 'security-header-generator'),
                    'settings' => self::woocommerce_preset(),
                ),
                'page_builder' => array(
                    'name' => __('Page Builder Friendly', 'security-header-generator'),
                    'description' => __('WordPress Core plus Google Fonts, video embeds, CDN sources. Allows inline styles/scripts required for Elementor/Divi.', 'security-header-generator'),
                    'settings' => self::page_builder_preset(),
                ),
                'maximum_security' => array(
                    'name' => __('Locked Down (Maximum Security)', 'security-header-generator'),
                    'description' => __('Self only for most directives. No inline/eval. Minimal external sources. Best for static/admin-only sites.', 'security-header-generator'),
                    'settings' => self::maximum_security_preset(),
                ),
                'development' => array(
                    'name' => __('Development/Testing', 'security-header-generator'),
                    'description' => __('Permissive settings. Allows localhost and common development tools. Good for staging environments.', 'security-header-generator'),
                    'settings' => self::development_preset(),
                ),
            );
        }

        /**
         * WordPress Core Only preset
         * 
         * @access private
         * @static
         * @return array
         */
        private static function wordpress_core_preset(): array
        {
            return array(
                'generate_csp' => true,
                'apply_csp_to_admin' => false,
                'generate_csp_custom_styles' => 'https: *.googleapis.com *.gstatic.com',
                'generate_csp_custom_styles_allow_unsafe' => array(0),
                'generate_csp_custom_styles_elem' => 'https: *.googleapis.com *.gstatic.com',
                'generate_csp_custom_styles_elem_allow_unsafe' => array(0),
                'generate_csp_custom_scripts' => 'https: *.googleapis.com *.gstatic.com',
                'generate_csp_custom_scripts_allow_unsafe' => array(0),
                'generate_csp_custom_scripts_elem' => 'https: *.googleapis.com *.gstatic.com',
                'generate_csp_custom_scripts_elem_allow_unsafe' => array(0),
                'generate_csp_custom_fonts' => 'data: https: *.gstatic.com *.googleapis.com',
                'generate_csp_custom_fonts_allow_unsafe' => array(0),
                'generate_csp_custom_images' => 'data: https: *.gravatar.com *.wordpress.org s.w.org',
                'generate_csp_custom_images_allow_unsafe' => array(0),
                'generate_csp_custom_connect' => 'https:',
                'generate_csp_custom_connect_allow_unsafe' => array(0),
                'generate_csp_custom_frames' => '',
                'generate_csp_custom_frames_allow_unsafe' => array(0),
                'generate_csp_custom_media' => 'https: s.w.org',
                'generate_csp_custom_media_allow_unsafe' => array(0),
                'generate_csp_custom_workers' => '',
                'generate_csp_custom_workers_allow_unsafe' => array(0),
                'generate_csp_custom_defaults' => '',
                'generate_csp_custom_defaults_allow_unsafe' => array(0),
                'generate_csp_custom_baseuri' => '',
                'generate_csp_custom_baseuri_allow_unsafe' => array(0),
            );
        }

        /**
         * WooCommerce preset
         * 
         * @access private
         * @static
         * @return array
         */
        private static function woocommerce_preset(): array
        {
            return array(
                'generate_csp' => true,
                'apply_csp_to_admin' => false,
                'generate_csp_custom_styles' => 'https: *.googleapis.com *.gstatic.com',
                'generate_csp_custom_styles_allow_unsafe' => array(0, 1),
                'generate_csp_custom_styles_elem' => 'https: *.googleapis.com *.gstatic.com',
                'generate_csp_custom_styles_elem_allow_unsafe' => array(0, 1),
                'generate_csp_custom_scripts' => 'https: *.googleapis.com *.gstatic.com *.paypal.com *.stripe.com *.google-analytics.com *.googletagmanager.com *.facebook.net *.facebook.com',
                'generate_csp_custom_scripts_allow_unsafe' => array(0, 1),
                'generate_csp_custom_scripts_elem' => 'https: *.googleapis.com *.gstatic.com *.paypal.com *.stripe.com *.google-analytics.com *.googletagmanager.com *.facebook.net *.facebook.com',
                'generate_csp_custom_scripts_elem_allow_unsafe' => array(0, 1),
                'generate_csp_custom_fonts' => 'data: https: *.gstatic.com *.googleapis.com',
                'generate_csp_custom_fonts_allow_unsafe' => array(0),
                'generate_csp_custom_images' => 'data: https: *.gravatar.com *.wordpress.org s.w.org *.paypal.com *.stripe.com *.google-analytics.com *.facebook.com',
                'generate_csp_custom_images_allow_unsafe' => array(0),
                'generate_csp_custom_connect' => 'https: *.paypal.com *.stripe.com *.google-analytics.com *.facebook.com *.facebook.net',
                'generate_csp_custom_connect_allow_unsafe' => array(0),
                'generate_csp_custom_frames' => 'https: *.paypal.com *.stripe.com *.youtube.com *.vimeo.com',
                'generate_csp_custom_frames_allow_unsafe' => array(0),
                'generate_csp_custom_media' => 'https: s.w.org',
                'generate_csp_custom_media_allow_unsafe' => array(0),
                'generate_csp_custom_workers' => '',
                'generate_csp_custom_workers_allow_unsafe' => array(0),
                'generate_csp_custom_defaults' => '',
                'generate_csp_custom_defaults_allow_unsafe' => array(0),
                'generate_csp_custom_baseuri' => '',
                'generate_csp_custom_baseuri_allow_unsafe' => array(0),
            );
        }

        /**
         * Page Builder preset
         * 
         * @access private
         * @static
         * @return array
         */
        private static function page_builder_preset(): array
        {
            return array(
                'generate_csp' => true,
                'apply_csp_to_admin' => false,
                'generate_csp_custom_styles' => 'https: *.googleapis.com *.gstatic.com cdn.jsdelivr.net cdnjs.cloudflare.com',
                'generate_csp_custom_styles_allow_unsafe' => array(0, 1),
                'generate_csp_custom_styles_elem' => 'https: *.googleapis.com *.gstatic.com cdn.jsdelivr.net cdnjs.cloudflare.com',
                'generate_csp_custom_styles_elem_allow_unsafe' => array(0, 1),
                'generate_csp_custom_styles_attr' => 'https:',
                'generate_csp_custom_styles_attr_allow_unsafe' => array(0, 1),
                'generate_csp_custom_scripts' => 'https: *.googleapis.com *.gstatic.com cdn.jsdelivr.net cdnjs.cloudflare.com',
                'generate_csp_custom_scripts_allow_unsafe' => array(0, 1, 2),
                'generate_csp_custom_scripts_elem' => 'https: *.googleapis.com *.gstatic.com cdn.jsdelivr.net cdnjs.cloudflare.com',
                'generate_csp_custom_scripts_elem_allow_unsafe' => array(0, 1, 2),
                'generate_csp_custom_scripts_attr' => 'https:',
                'generate_csp_custom_scripts_attr_allow_unsafe' => array(0, 1),
                'generate_csp_custom_fonts' => 'data: https: *.gstatic.com *.googleapis.com cdn.jsdelivr.net cdnjs.cloudflare.com',
                'generate_csp_custom_fonts_allow_unsafe' => array(0),
                'generate_csp_custom_images' => 'data: https: *.gravatar.com *.wordpress.org s.w.org',
                'generate_csp_custom_images_allow_unsafe' => array(0),
                'generate_csp_custom_connect' => 'https:',
                'generate_csp_custom_connect_allow_unsafe' => array(0),
                'generate_csp_custom_frames' => 'https: *.youtube.com *.vimeo.com *.youtube-nocookie.com',
                'generate_csp_custom_frames_allow_unsafe' => array(0),
                'generate_csp_custom_media' => 'https: s.w.org *.youtube.com *.vimeo.com',
                'generate_csp_custom_media_allow_unsafe' => array(0),
                'generate_csp_custom_workers' => '',
                'generate_csp_custom_workers_allow_unsafe' => array(0),
                'generate_csp_custom_defaults' => '',
                'generate_csp_custom_defaults_allow_unsafe' => array(0),
                'generate_csp_custom_baseuri' => '',
                'generate_csp_custom_baseuri_allow_unsafe' => array(0),
            );
        }

        /**
         * Maximum Security preset
         * 
         * @access private
         * @static
         * @return array
         */
        private static function maximum_security_preset(): array
        {
            return array(
                'generate_csp' => true,
                'apply_csp_to_admin' => false,
                'generate_csp_custom_styles' => '',
                'generate_csp_custom_styles_allow_unsafe' => array(0),
                'generate_csp_custom_styles_elem' => '',
                'generate_csp_custom_styles_elem_allow_unsafe' => array(0),
                'generate_csp_custom_styles_attr' => '',
                'generate_csp_custom_styles_attr_allow_unsafe' => array(0),
                'generate_csp_custom_scripts' => '',
                'generate_csp_custom_scripts_allow_unsafe' => array(0),
                'generate_csp_custom_scripts_elem' => '',
                'generate_csp_custom_scripts_elem_allow_unsafe' => array(0),
                'generate_csp_custom_scripts_attr' => '',
                'generate_csp_custom_scripts_attr_allow_unsafe' => array(0),
                'generate_csp_custom_fonts' => '',
                'generate_csp_custom_fonts_allow_unsafe' => array(0),
                'generate_csp_custom_images' => 'data:',
                'generate_csp_custom_images_allow_unsafe' => array(0),
                'generate_csp_custom_connect' => '',
                'generate_csp_custom_connect_allow_unsafe' => array(0),
                'generate_csp_custom_frames' => '',
                'generate_csp_custom_frames_allow_unsafe' => array(3),
                'generate_csp_custom_media' => '',
                'generate_csp_custom_media_allow_unsafe' => array(0),
                'generate_csp_custom_workers' => '',
                'generate_csp_custom_workers_allow_unsafe' => array(0),
                'generate_csp_custom_defaults' => '',
                'generate_csp_custom_defaults_allow_unsafe' => array(0),
                'generate_csp_custom_baseuri' => '',
                'generate_csp_custom_baseuri_allow_unsafe' => array(0),
                'generate_csp_custom_objects' => '',
                'generate_csp_custom_objects_allow_unsafe' => array(3),
            );
        }

        /**
         * Development preset
         * 
         * @access private
         * @static
         * @return array
         */
        private static function development_preset(): array
        {
            return array(
                'generate_csp' => true,
                'apply_csp_to_admin' => false,
                'generate_csp_custom_styles' => 'https: http://localhost:* http://127.0.0.1:* *.googleapis.com *.gstatic.com',
                'generate_csp_custom_styles_allow_unsafe' => array(0, 1),
                'generate_csp_custom_styles_elem' => 'https: http://localhost:* http://127.0.0.1:* *.googleapis.com *.gstatic.com',
                'generate_csp_custom_styles_elem_allow_unsafe' => array(0, 1),
                'generate_csp_custom_styles_attr' => 'https: http://localhost:* http://127.0.0.1:*',
                'generate_csp_custom_styles_attr_allow_unsafe' => array(0, 1),
                'generate_csp_custom_scripts' => 'https: http://localhost:* http://127.0.0.1:* *.googleapis.com *.gstatic.com',
                'generate_csp_custom_scripts_allow_unsafe' => array(0, 1, 2),
                'generate_csp_custom_scripts_elem' => 'https: http://localhost:* http://127.0.0.1:* *.googleapis.com *.gstatic.com',
                'generate_csp_custom_scripts_elem_allow_unsafe' => array(0, 1, 2),
                'generate_csp_custom_scripts_attr' => 'https: http://localhost:* http://127.0.0.1:*',
                'generate_csp_custom_scripts_attr_allow_unsafe' => array(0, 1, 2),
                'generate_csp_custom_fonts' => 'data: https: http://localhost:* http://127.0.0.1:* *.gstatic.com *.googleapis.com',
                'generate_csp_custom_fonts_allow_unsafe' => array(0),
                'generate_csp_custom_images' => 'data: https: http://localhost:* http://127.0.0.1:* *.gravatar.com *.wordpress.org s.w.org',
                'generate_csp_custom_images_allow_unsafe' => array(0),
                'generate_csp_custom_connect' => 'https: http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*',
                'generate_csp_custom_connect_allow_unsafe' => array(0),
                'generate_csp_custom_frames' => 'https: http://localhost:* http://127.0.0.1:* *.youtube.com *.vimeo.com',
                'generate_csp_custom_frames_allow_unsafe' => array(0),
                'generate_csp_custom_media' => 'https: http://localhost:* http://127.0.0.1:* s.w.org',
                'generate_csp_custom_media_allow_unsafe' => array(0),
                'generate_csp_custom_workers' => 'http://localhost:* http://127.0.0.1:*',
                'generate_csp_custom_workers_allow_unsafe' => array(0),
                'generate_csp_custom_defaults' => '',
                'generate_csp_custom_defaults_allow_unsafe' => array(0),
                'generate_csp_custom_baseuri' => '',
                'generate_csp_custom_baseuri_allow_unsafe' => array(0),
            );
        }

        /**
         * Apply a preset to settings
         * 
         * @access public
         * @static
         * @param string $preset_key The preset key to apply
         * @return bool Success/failure
         */
        public static function apply_preset(string $preset_key): bool
        {
            $presets = self::get_presets();

            if (!isset($presets[$preset_key])) {
                return false;
            }

            $preset_settings = $presets[$preset_key]['settings'];
            $current_settings = get_option('wpsh_settings', array());

            // Merge preset with current settings (preset takes precedence)
            $new_settings = array_merge($current_settings, $preset_settings);

            update_option('wpsh_settings', $new_settings);

            // Clear cache
            clear_our_option_cache();

            return true;
        }
    }
}
