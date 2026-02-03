<?php

/**
 * Configuration Definitions
 * 
 * @since 5.3.68
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Security Header Generator
 */

defined('ABSPATH') || die('No direct script access allowed');

// make sure the class doesn't already exist
if (! class_exists('KCP_CSPGEN_Configs')) {

    /** 
     * Class KCP_CSPGEN_Configs
     * 
     * This class holds the configurations for
     * the Content Security Policy and the 
     * Permissions Policy
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Security Header Generator
     * 
     */
    class KCP_CSPGEN_Configs
    {

        /**
         * Get CSP directives configuration
         * 
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return array
         */
        public static function get_csp_directives(): array
        {

            // hold the directives array
            $directives = array(
                'base-uri' => array(
                    'id' => 'generate_csp_custom_baseuri',
                    'title' => __('Base URI', 'security-header-generator'),
                    'desc' => __('Restricts the URLs which can be used in a document\'s <base> element.', 'security-header-generator'),
                ),
                'child-src' => array(
                    'id' => 'generate_csp_custom_child',
                    'title' => __('Child Source', 'security-header-generator'),
                    'desc' => __('Defines the valid sources for web workers and nested browsing contexts.', 'security-header-generator'),
                ),
                'connect-src' => array(
                    'id' => 'generate_csp_custom_connect',
                    'title' => __('Connect/Ajax/XHR Source', 'security-header-generator'),
                    'desc' => __('Restricts the URLs which can be loaded using script interfaces', 'security-header-generator'),
                ),
                'default-src' => array(
                    'id' => 'generate_csp_custom_defaults',
                    'title' => __('Default Source', 'security-header-generator'),
                    'desc' => __('Serves as a fallback for the other fetch directives.', 'security-header-generator'),
                ),
                'font-src' => array(
                    'id' => 'generate_csp_custom_fonts',
                    'title' => __('Font Source', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for fonts loaded using @font-face.', 'security-header-generator'),
                ),
                'form-action' => array(
                    'id' => 'generate_csp_custom_forms',
                    'title' => __('Form Action', 'security-header-generator'),
                    'desc' => __('Restricts the URLs which can be used as the target of form submissions.', 'security-header-generator'),
                ),
                'frame-src' => array(
                    'id' => 'generate_csp_custom_frames',
                    'title' => __('Frame Source', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for nested browsing contexts.', 'security-header-generator'),
                ),
                'frame-ancestors' => array(
                    'id' => 'generate_csp_custom_frame_ancestors',
                    'title' => __('Frame Ancestors', 'security-header-generator'),
                    'desc' => __('Specifies valid parents that may embed a page.', 'security-header-generator'),
                ),
                'img-src' => array(
                    'id' => 'generate_csp_custom_images',
                    'title' => __('Image Source', 'security-header-generator'),
                    'desc' => __('Specifies valid sources of images and favicons.', 'security-header-generator'),
                ),
                'manifest-src' => array(
                    'id' => 'generate_csp_custom_manifests',
                    'title' => __('Manifest Source', 'security-header-generator'),
                    'desc' => __('Specifies valid sources of application manifest files.', 'security-header-generator'),
                ),
                'media-src' => array(
                    'id' => 'generate_csp_custom_media',
                    'title' => __('Media Source', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for loading media.', 'security-header-generator'),
                ),
                'object-src' => array(
                    'id' => 'generate_csp_custom_objects',
                    'title' => __('Object Source', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for object, embed, and applet elements.', 'security-header-generator'),
                ),
                'report-to' => array(
                    'id' => 'generate_csp_report_to',
                    'title' => __('Report To', 'security-header-generator'),
                    'desc' => __('The Content-Security-Policy Report-To HTTP response header field instructs the user agent to store reporting endpoints for an origin.', 'security-header-generator'),
                ),
                'sandbox' => array(
                    'id' => 'generate_csp_custom_sandbox',
                    'title' => __('Sandbox', 'security-header-generator'),
                    'desc' => __('Applies restrictions to a page\'s actions.', 'security-header-generator'),
                ),
                'script-src' => array(
                    'id' => 'generate_csp_custom_scripts',
                    'title' => __('Script Source', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for JavaScript.', 'security-header-generator'),
                ),
                'script-src-elem' => array(
                    'id' => 'generate_csp_custom_scripts_elem',
                    'title' => __('Script Source Elements', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for JavaScript script elements.', 'security-header-generator'),
                ),
                'script-src-attr' => array(
                    'id' => 'generate_csp_custom_scripts_attr',
                    'title' => __('Script Source Attributes', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for JavaScript inline event handlers.', 'security-header-generator'),
                ),
                'style-src' => array(
                    'id' => 'generate_csp_custom_styles',
                    'title' => __('Style Source', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for stylesheets.', 'security-header-generator'),
                ),
                'style-src-elem' => array(
                    'id' => 'generate_csp_custom_styles_elem',
                    'title' => __('Style Source Elements', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for stylesheet elements.', 'security-header-generator'),
                ),
                'style-src-attr' => array(
                    'id' => 'generate_csp_custom_styles_attr',
                    'title' => __('Style Source Attributes', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for inline styles.', 'security-header-generator'),
                ),
                'worker-src' => array(
                    'id' => 'generate_csp_custom_workers',
                    'title' => __('Worker Source', 'security-header-generator'),
                    'desc' => __('Specifies valid sources for Worker scripts.', 'security-header-generator'),
                ),
            );

            // return a filtered array that developers can add/remove items from
            return apply_filters('wpsh_csp_directives', $directives);
        }

        /**
         * Get Permissions Policy directives configuration
         * 
         * @access public
         * @static
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return array
         */
        public static function get_permissions_directives(): array
        {

            // hold the directives array
            $directives = array(
                'accelerometer' => array(
                    'id' => 'fp_accelerometer',
                    'title' => __('Accelerometer', 'security-header-generator'),
                    'desc' => __('Controls access to the Accelerometer interface.', 'security-header-generator'),
                ),
                'ambient-light-sensor' => array(
                    'id' => 'fp_ambient-light-sensor',
                    'title' => __('Ambient Light Sensor', 'security-header-generator'),
                    'desc' => __('Controls access to the AmbientLightSensor interface.', 'security-header-generator'),
                ),
                'autoplay' => array(
                    'id' => 'fp_autoplay',
                    'title' => __('Autoplay', 'security-header-generator'),
                    'desc' => __('Controls whether media can autoplay.', 'security-header-generator'),
                ),
                'camera' => array(
                    'id' => 'fp_camera',
                    'title' => __('Camera', 'security-header-generator'),
                    'desc' => __('Controls access to video input devices.', 'security-header-generator'),
                ),
                'display-capture' => array(
                    'id' => 'fp_display-capture',
                    'title' => __('Display Capture', 'security-header-generator'),
                    'desc' => __('Controls access to screen capture.', 'security-header-generator'),
                ),
                'encrypted-media' => array(
                    'id' => 'fp_encrypted-media',
                    'title' => __('Encrypted Media', 'security-header-generator'),
                    'desc' => __('Controls access to the Encrypted Media Extensions API.', 'security-header-generator'),
                ),
                'fullscreen' => array(
                    'id' => 'fp_fullscreen',
                    'title' => __('Full Screen', 'security-header-generator'),
                    'desc' => __('Controls access to requestFullScreen.', 'security-header-generator'),
                ),
                'geolocation' => array(
                    'id' => 'fp_geolocation',
                    'title' => __('Geo Location', 'security-header-generator'),
                    'desc' => __('Controls access to the Geolocation Interface.', 'security-header-generator'),
                ),
                'gyroscope' => array(
                    'id' => 'fp_gyroscope',
                    'title' => __('Gyroscope', 'security-header-generator'),
                    'desc' => __('Controls access to the Gyroscope interface.', 'security-header-generator'),
                ),
                'hid' => array(
                    'id' => 'fp_hid',
                    'title' => __('Human Interface Device', 'security-header-generator'),
                    'desc' => __('Controls access to the WebHID API.', 'security-header-generator'),
                ),
                'identity-credentials-get' => array(
                    'id' => 'fp_icg',
                    'title' => __('Identity Credentials Get', 'security-header-generator'),
                    'desc' => __('Controls access to the Federated Credential Management API.', 'security-header-generator'),
                ),
                'idle-detection' => array(
                    'id' => 'fp_idle',
                    'title' => __('Idle Detection', 'security-header-generator'),
                    'desc' => __('Controls access to the Idle Detection API.', 'security-header-generator'),
                ),
                'magnetometer' => array(
                    'id' => 'fp_magnetometer',
                    'title' => __('Magnetometer', 'security-header-generator'),
                    'desc' => __('Controls access to the Magnetometer interface.', 'security-header-generator'),
                ),
                'microphone' => array(
                    'id' => 'fp_microphone',
                    'title' => __('Microphone', 'security-header-generator'),
                    'desc' => __('Controls access to audio input devices.', 'security-header-generator'),
                ),
                'midi' => array(
                    'id' => 'fp_midi',
                    'title' => __('MIDI', 'security-header-generator'),
                    'desc' => __('Controls access to the Web MIDI API.', 'security-header-generator'),
                ),
                'payment' => array(
                    'id' => 'fp_payment',
                    'title' => __('Payment', 'security-header-generator'),
                    'desc' => __('Controls access to the Payment Request API.', 'security-header-generator'),
                ),
                'picture-in-picture' => array(
                    'id' => 'fp_picture-in-picture',
                    'title' => __('Picture in Picture', 'security-header-generator'),
                    'desc' => __('Controls access to Picture-in-Picture mode.', 'security-header-generator'),
                ),
                'publickey-credentials-create' => array(
                    'id' => 'fp_publickey-credentials-create',
                    'title' => __('Publickey Credentials Create', 'security-header-generator'),
                    'desc' => __('Controls creation of WebAuthn credentials.', 'security-header-generator'),
                ),
                'publickey-credentials-get' => array(
                    'id' => 'fp_publickey-credentials-get',
                    'title' => __('Publickey Credentials Get', 'security-header-generator'),
                    'desc' => __('Controls retrieval of WebAuthn credentials.', 'security-header-generator'),
                ),
                'screen-wake-lock' => array(
                    'id' => 'fp_screen-wake-lock',
                    'title' => __('Screen Wake Lock', 'security-header-generator'),
                    'desc' => __('Controls access to Screen Wake Lock API.', 'security-header-generator'),
                ),
                'serial' => array(
                    'id' => 'fp_serial',
                    'title' => __('Serial', 'security-header-generator'),
                    'desc' => __('Controls access to the Web Serial API.', 'security-header-generator'),
                ),
                'sync-xhr' => array(
                    'id' => 'fp_sync-xhr',
                    'title' => __('Sync XHR', 'security-header-generator'),
                    'desc' => __('Controls synchronous XMLHttpRequest requests.', 'security-header-generator'),
                ),
                'usb' => array(
                    'id' => 'fp_usb',
                    'title' => __('USB', 'security-header-generator'),
                    'desc' => __('Controls access to the WebUSB API.', 'security-header-generator'),
                ),
                'web-share' => array(
                    'id' => 'fp_web-share',
                    'title' => __('Web Share', 'security-header-generator'),
                    'desc' => __('Controls access to the Web Share API.', 'security-header-generator'),
                ),
                'xr-spatial-tracking' => array(
                    'id' => 'fp_xr-spatial-tracking',
                    'title' => __('XR Spatial Tracking', 'security-header-generator'),
                    'desc' => __('Controls access to the WebXR Device API.', 'security-header-generator'),
                ),
            );

            // return a filtered array that developers can add/remove items from
            return apply_filters('wpsh_permissions_directives', $directives);
        }
    }
}
