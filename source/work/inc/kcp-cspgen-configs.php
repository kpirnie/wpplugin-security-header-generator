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

        /**
         * Get Standard Header definitions, grouped by category
         *
         * @access public
         * @static
         * @return array
         */
        public static function get_standard_headers(): array
        {
            $groups = array(
                'application' => array(
                    'title'   => __('Application Settings', 'security-header-generator'),
                    'headers' => array(
                        array(
                            'id'          => 'apply_to_admin',
                            'label'       => __('Apply to Admin?', 'security-header-generator'),
                            'description' => __('This will attempt to apply all headers to the admin side of your site in addition to the front-end.', 'security-header-generator'),
                            'default'     => false,
                        ),
                        array(
                            'id'          => 'apply_to_rest',
                            'label'       => __('Apply to the REST API?', 'security-header-generator'),
                            'description' => __('This will attempt to apply all headers to the REST API of your site.<br /><strong>NOTE:</strong> Due to the default nature of the REST API, the headers will also be applied to the admin areas of the website. You will need to check for breakages after applying.', 'security-header-generator'),
                            'default'     => false,
                        ),
                    ),
                ),

                'transport' => array(
                    'title'   => __('Transport Security', 'security-header-generator'),
                    'headers' => array(
                        array(
                            'id'          => 'include_sts',
                            'label'       => __('Include Strict Transport Security?', 'security-header-generator'),
                            'description' => __('Setting this will add another header to force Strict Transport Security. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'     => 'sts_group',
                                    'type'   => 'group',
                                    'fields' => array(
                                        array(
                                            'id'          => 'include_sts_max_age',
                                            'type'        => 'text',
                                            'label'       => __('Cache Age', 'security-header-generator'),
                                            'description' => __('The time, in seconds, that the browser should remember that a site is only to be accessed using HTTPS.', 'security-header-generator'),
                                            'default'     => 31536000,
                                        ),
                                        array(
                                            'id'          => 'include_sts_subdomains',
                                            'type'        => 'switch',
                                            'label'       => __('Include Subdomains?', 'security-header-generator'),
                                            'description' => __('If this optional parameter is specified, this rule applies to all of the site\'s subdomains as well.', 'security-header-generator'),
                                            'on_label'    => __('Yes', 'security-header-generator'),
                                            'off_label'   => __('No', 'security-header-generator'),
                                            'default'     => false,
                                            'inline'      => true,
                                        ),
                                        array(
                                            'id'          => 'include_sts_preload',
                                            'type'        => 'switch',
                                            'label'       => __('Preload?', 'security-header-generator'),
                                            'description' => __('If you enable preload, you should change the cache age to 2 Years. (63072000)', 'security-header-generator'),
                                            'on_label'    => __('Yes', 'security-header-generator'),
                                            'off_label'   => __('No', 'security-header-generator'),
                                            'default'     => false,
                                            'inline'      => true,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                        array(
                            'id'          => 'include_upgrade_insecure',
                            'label'       => __('Do you want to upgrade insecure requests?', 'security-header-generator'),
                            'description' => __('Setting this will add another header to upgrade insecure requests. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Upgrade-Insecure-Requests" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Upgrade-Insecure-Requests</a>', 'security-header-generator'),
                            'default'     => false,
                        ),
                    ),
                ),

                'framing' => array(
                    'title'   => __('Framing', 'security-header-generator'),
                    'headers' => array(
                        array(
                            'id'          => 'include_ofs',
                            'label'       => __('Configure Frame Sources?', 'security-header-generator'),
                            'description' => __('Setting this will add another header to configure allowed frame sources. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Frame-Options</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'          => 'include_ofs_type',
                                    'type'        => 'radio',
                                    'label'       => __('Directives', 'security-header-generator'),
                                    'description' => __('Select the framing directive to apply.', 'security-header-generator'),
                                    'options'     => array(
                                        'DENY'       => __('deny all framing', 'security-header-generator'),
                                        'SAMEORIGIN' => __('deny all framing unless done from the origination domain', 'security-header-generator'),
                                    ),
                                    'inline'  => true,
                                    'default' => 'DENY',
                                ),
                            ),
                        ),
                    ),
                ),

                'cors' => array(
                    'title'   => __('CORS / Access Control', 'security-header-generator'),
                    'headers' => array(
                        array(
                            'id'          => 'include_acam',
                            'label'       => __('Do you want to configure access methods?', 'security-header-generator'),
                            'description' => __('Setting this will add another header for Access-Control-Allow-Methods. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Methods" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Methods</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'          => 'include_acam_methods',
                                    'type'        => 'checkboxes',
                                    'label'       => __('Methods', 'security-header-generator'),
                                    'description' => __('Select the methods you wish to allow.<br /><strong>NOTE:</strong> Most public websites require at least GET to be viewable online.<br /><strong>NOTE 2:</strong> This will block unselected methods.', 'security-header-generator'),
                                    'options'     => array(
                                        'GET'     => __('GET', 'security-header-generator'),
                                        'HEAD'    => __('HEAD', 'security-header-generator'),
                                        'POST'    => __('POST', 'security-header-generator'),
                                        'PUT'     => __('PUT', 'security-header-generator'),
                                        'DELETE'  => __('DELETE', 'security-header-generator'),
                                        'CONNECT' => __('CONNECT', 'security-header-generator'),
                                        'OPTIONS' => __('OPTIONS', 'security-header-generator'),
                                        'TRACE'   => __('TRACE', 'security-header-generator'),
                                        'PATCH'   => __('PATCH', 'security-header-generator'),
                                        '*'       => __('Allow All', 'security-header-generator'),
                                    ),
                                    'inline'  => true,
                                    'default' => array('GET', 'POST', 'HEAD'),
                                ),
                            ),
                        ),
                        array(
                            'id'          => 'include_acac',
                            'label'       => __('Do you want to allow access control credentials?', 'security-header-generator'),
                            'description' => __('Setting this will add another header for Access-Control-Allow-Credentials. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Credentials" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Credentials</a>', 'security-header-generator'),
                            'default'     => true,
                        ),
                        array(
                            'id'          => 'include_acao',
                            'label'       => __('Do you want to allow an access control origin?', 'security-header-generator'),
                            'description' => __('Setting this will add another header for Access-Control-Allow-Origin. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Origin</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'          => 'include_acao_origin',
                                    'type'        => 'text',
                                    'label'       => __('Access Control Allow Origin', 'security-header-generator'),
                                    'description' => __('Set the allowed access origin here. Can either be an asterisk: <code>*</code>, or a FQDN URL: <code>https://example.com</code>', 'security-header-generator'),
                                    'default'     => '*',
                                ),
                            ),
                        ),
                        array(
                            'id'          => 'include_acah',
                            'label'       => __('Do you want to configure Access-Control-Allow-Headers?', 'security-header-generator'),
                            'description' => __('Setting this will add the Access-Control-Allow-Headers header, specifying which HTTP headers can be used during the actual request. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Headers" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Allow-Headers</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'          => 'include_acah_headers',
                                    'type'        => 'text',
                                    'label'       => __('Allowed Headers', 'security-header-generator'),
                                    'description' => __('Comma-delimited list of allowed request headers. Example: <code>Content-Type, Authorization, X-Requested-With</code>', 'security-header-generator'),
                                    'default'     => 'Content-Type, Authorization',
                                ),
                            ),
                        ),
                        array(
                            'id'          => 'include_aceh',
                            'label'       => __('Do you want to configure Access-Control-Expose-Headers?', 'security-header-generator'),
                            'description' => __('Setting this will add the Access-Control-Expose-Headers header, specifying which response headers can be exposed to JavaScript. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Expose-Headers" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Expose-Headers</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'          => 'include_aceh_headers',
                                    'type'        => 'text',
                                    'label'       => __('Exposed Headers', 'security-header-generator'),
                                    'description' => __('Comma-delimited list of response headers to expose to JavaScript. Example: <code>X-Custom-Header, X-Request-Id</code>', 'security-header-generator'),
                                    'default'     => '',
                                ),
                            ),
                        ),
                        array(
                            'id'          => 'include_acma',
                            'label'       => __('Do you want to configure Access-Control-Max-Age?', 'security-header-generator'),
                            'description' => __('Setting this will add the Access-Control-Max-Age header, controlling how long preflight request results can be cached. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Max-Age" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Access-Control-Max-Age</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'          => 'include_acma_seconds',
                                    'type'        => 'text',
                                    'label'       => __('Cache Duration (seconds)', 'security-header-generator'),
                                    'description' => __('How long in seconds a preflight response can be cached. Default is 600 (10 minutes). Maximum varies by browser; Chrome caps at 7200, Firefox at 86400.', 'security-header-generator'),
                                    'default'     => 600,
                                ),
                            ),
                        ),
                    ),
                ),

                'content_protection' => array(
                    'title'   => __('Content Protection', 'security-header-generator'),
                    'headers' => array(
                        array(
                            'id'          => 'include_mimesniffing',
                            'label'       => __('Do you want to prevent mime-type sniffing?', 'security-header-generator'),
                            'description' => __('Setting this will add another header to force proper mime-types. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Content-Type-Options</a>', 'security-header-generator'),
                            'default'     => false,
                        ),
                        array(
                            'id'          => 'include_referrer_policy',
                            'label'       => __('Do you want to configure referrer policy?', 'security-header-generator'),
                            'description' => __('Setting this will add another header to configure allowed origin referrers. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Referrer-Policy</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'      => 'include_referrer_policy_setting',
                                    'type'    => 'radio',
                                    'label'   => __('Directives', 'security-header-generator'),
                                    'options' => array(
                                        'no-referrer'                     => __('no referrer', 'security-header-generator'),
                                        'no-referrer-when-downgrade'      => __('no referrer on protocol downgrade', 'security-header-generator'),
                                        'origin'                          => __('origin only', 'security-header-generator'),
                                        'origin-when-cross-origin'        => __('origin on cross-domain', 'security-header-generator'),
                                        'same-origin'                     => __('same origin', 'security-header-generator'),
                                        'strict-origin'                   => __('strict origin', 'security-header-generator'),
                                        'strict-origin-when-cross-origin' => __('strict origin on cross domain', 'security-header-generator'),
                                        'unsafe-url'                      => __('full referrer path', 'security-header-generator'),
                                    ),
                                    'inline'  => true,
                                    'default' => array('same-origin'),
                                ),
                            ),
                        ),
                        array(
                            'id'          => 'include_download_options',
                            'label'       => __('Do you want to force downloads?', 'security-header-generator'),
                            'description' => __('Setting this will add another header to downloading resources instead of directly opening them in the browser. See here for more information: <a href="https://www.nwebsec.com/HttpHeaders/SecurityHeaders/XDownloadOptions" target="_blank">https://www.nwebsec.com/HttpHeaders/SecurityHeaders/XDownloadOptions</a>', 'security-header-generator'),
                            'default'     => false,
                        ),
                        array(
                            'id'          => 'include_crossdomain',
                            'label'       => __('Do you want to block cross domain origins?', 'security-header-generator'),
                            'description' => __('Setting this will add another header to block cross domain origins. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/X-Permitted-Cross-Domain-Policies" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/X-Permitted-Cross-Domain-Policies</a>', 'security-header-generator'),
                            'default'     => false,
                        ),
                    ),
                ),

                'cross_origin_policies' => array(
                    'title'   => __('Cross-Origin Policies', 'security-header-generator'),
                    'headers' => array(
                        array(
                            'id'          => 'coep',
                            'label'       => __('Do you want to configure a Cross-Origin-Embedder-Policy?', 'security-header-generator'),
                            'description' => __('Setting this will add another header to set a Cross-Origin-Embedder-Policy. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cross-Origin-Embedder-Policy" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cross-Origin-Embedder-Policy</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'      => 'coep_setting',
                                    'type'    => 'radio',
                                    'label'   => __('Directives', 'security-header-generator'),
                                    'options' => array(
                                        'unsafe-none'  => __('unsafe-none', 'security-header-generator'),
                                        'require-corp' => __('require-corp', 'security-header-generator'),
                                    ),
                                    'inline'  => true,
                                    'default' => array('unsafe-none'),
                                ),
                            ),
                        ),
                        array(
                            'id'          => 'corp',
                            'label'       => __('Do you want to configure a Cross-Origin-Resource-Policy?', 'security-header-generator'),
                            'description' => __('Setting this will add another header to set a Cross-Origin-Resource-Policy. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Cross-Origin_Resource_Policy" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Cross-Origin_Resource_Policy</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'      => 'corp_setting',
                                    'type'    => 'radio',
                                    'label'   => __('Directives', 'security-header-generator'),
                                    'options' => array(
                                        'same-site'    => __('same-site', 'security-header-generator'),
                                        'same-origin'  => __('same-origin', 'security-header-generator'),
                                        'cross-origin' => __('cross-origin', 'security-header-generator'),
                                    ),
                                    'inline'  => true,
                                    'default' => array('same-origin'),
                                ),
                            ),
                        ),
                        array(
                            'id'          => 'coop',
                            'label'       => __('Do you want to configure a Cross-Origin-Opener-Policy?', 'security-header-generator'),
                            'description' => __('Setting this will add another header to set a Cross-Origin-Opener-Policy. See here for more information: <a href="https://owasp.org/www-project-secure-headers/#cross-origin-opener-policy" target="_blank">https://owasp.org/www-project-secure-headers/#cross-origin-opener-policy</a>', 'security-header-generator'),
                            'default'     => false,
                            'sub_fields'  => array(
                                array(
                                    'id'      => 'coop_setting',
                                    'type'    => 'radio',
                                    'label'   => __('Directives', 'security-header-generator'),
                                    'options' => array(
                                        'unsafe-none'              => __('unsafe-none', 'security-header-generator'),
                                        'same-origin-allow-popups' => __('same-origin-allow-popups', 'security-header-generator'),
                                        'same-origin'              => __('same-origin', 'security-header-generator'),
                                    ),
                                    'inline'  => true,
                                    'default' => array('unsafe-none'),
                                ),
                            ),
                        ),
                    ),
                ),
            );

            return apply_filters('wpsh_standard_headers', $groups);
        }
    }
}
