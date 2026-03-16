<?php

/** 
 * Header Settings
 * 
 * Controls the admin settings
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Security Header Generator
 * 
 */

// We don't want to allow direct access to this
defined('ABSPATH') || die('No direct script access allowed');

// make sure the class doesn't already exist
if (! class_exists('KCP_CSPGEN_Settings')) {

    /** 
     * Class KCP_CSPGEN_Settings
     * 
     * The actual class for generating the admin settings
     * 
     * @since 7.4
     * @access public
     * @author Kevin Pirnie <me@kpirnie.com>
     * @package Kevin's Security Header Generator
     * 
     */
    class KCP_CSPGEN_Settings
    {

        /**
         * @var \KP\WPFieldFramework\Framework|null $fw The field framework instance
         */
        protected ?\KP\WPFieldFramework\Framework $fw = null;

        /**
         * @var array $tabs An array to hold the tabs for the settings page
         */
        protected array $tabs = array();

        /**
         * Class constructor
         * 
         * Setup the object
         * 
         * @internal
         */
        public function __construct()
        {

            // load our field framework
            $this->fw = \KP\WPFieldFramework\Loader::init();
            // add our tabs
            $this->tabs = $this->add_tabs();
        }

        /** 
         * kp_cspgen_settings
         * 
         * The method is responsible for implementing the admin settings
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return void This method does not return anything
         * 
         */
        public function kp_cspgen_settings(): void
        {

            // add in the menu
            $this->kcp_cspgen_menu();

        }

        /** 
         * kcp_cspgen_menu
         * 
         * The method is responsible for building out the admin pages
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return void This method does not return anything
         * 
         */
        private function kcp_cspgen_menu(): void
        {

            // our options key
            $opts_key = 'wpsh_settings';

            // add the options page
            $options = $this->fw->addOptionsPage([
                'option_key'         => $opts_key,
                'page_title'         => __('Security Header Generator', 'security-header-generator'),
                'menu_title'         => __('Security Headers', 'security-header-generator'),
                'menu_slug'          => $opts_key,
                'icon_url'           => 'dashicons-shield',
                'position'           => 2,
                'tabs'               => $this->tabs,
                'save_button'        => __('Save Your Settings', 'security-header-generator'),
                'footer_text'        => __('Thank you for securing your site!', 'security-header-generator'),
                'show_export_import' => true,
                'autoload'           => true, // false, true, null
                'tab_layout'         => 'vertical',
            ]);

            // register the options page
            $options->register();

            // add in the sub menu items linking to the tabs
            add_submenu_page( 'wpsh_settings', '', 'CSP Headers', 'manage_options', 'admin.php?page=wpsh_settings&tab=csp', '' );
            add_submenu_page( 'wpsh_settings', '', 'Permissions Headers', 'manage_options', 'admin.php?page=wpsh_settings&tab=permissions', '' );
            add_submenu_page( 'wpsh_settings', '', 'Documentation', 'manage_options', 'admin.php?page=wpsh_settings&tab=doc', '' );

            // bold the tab in the submenu
            add_filter( 'submenu_file', function( $submenu_file ) {
                $page = sanitize_key( $_GET['page'] ?? '' );
                $tab  = sanitize_key( $_GET['tab']  ?? '' );

                if ( $page === 'wpsh_settings' && $tab !== '' ) {
                    $submenu_file = 'admin.php?page=wpsh_settings&tab=' . $tab;
                }

                return $submenu_file;
            } );
        }

        /**
         * add_tabs
         * 
         * The method is responsible for adding tabs to the settings page.  This is where we will inject our different sections of settings, such as standard security headers, content security policy headers, and permissions policy headers.
         * 
         * @since 8.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return array Returns an array of tabs to be added to the settings page
         */
        private function add_tabs(): array
        {
            // return the array of tabs with their respective fields
            return array(
                'standard' => array(
                    'title' => __('Standard Headers', 'security-header-generator'),
                    'description' => __('Configure your standard security headers here.', 'security-header-generator'),
                    'sections' => [
                        'a' => [
                            'fields' => $this->kcp_standard_security_headers(),
                        ],
                    ],
                ),
                'csp' => array(
                    'title' => __('CSP Headers', 'security-header-generator'),
                    'sections' => [
                        'b' => [
                            'fields' => $this->kcp_csp_headers(),
                        ],
                    ],
                ),
                'permissions' => array(
                    'title' => __('Permissions Headers', 'security-header-generator'),
                    'sections' => [
                        'c' => [
                            'fields' => $this->kcp_perm_headers(),
                        ],
                    ],
                ),
                'doc' => array(
                    'title' => __('Documentation', 'security-header-generator'),
                    'sections' => [
                        'd' => [
                            'fields' => [
                                [
                                    'id'      => 'shg_documentation',
                                    'type'    => 'html',
                                    'content' => $this->kcp_documentation(),
                                ],
                            ],
                        ],
                    ],
                ),
            );
        }

        /** 
         * kcp_standard_security_headers
         * 
         * The method is responsible for setting up the standard security header settings
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return array Returns an array of all the settings necessary
         * 
         */
        private function kcp_standard_security_headers(): array
        {
            $ret    = [];
            $groups = KCP_CSPGEN_Configs::get_standard_headers();

            foreach ($groups as $group) {
                foreach ($group['headers'] as $header) {

                    // Primary toggle switch for this header
                    $ret[] = array(
                        'id'          => $header['id'],
                        'type'        => 'switch',
                        'label'       => $header['label'],
                        'description' => $header['description'],
                        'on_label'    => __('Yes', 'security-header-generator'),
                        'off_label'   => __('No', 'security-header-generator'),
                        'default'     => $header['default'] ?? false,
                    );

                    // Sub-fields rendered conditionally on the toggle above
                    foreach ($header['sub_fields'] ?? [] as $sub_field) {
                        $sub_field['conditional'] = array(
                            'field'     => $header['id'],
                            'value'     => true,
                            'condition' => '==',
                        );
                        $ret[] = $sub_field;
                    }
                }
            }

            return $ret;
        }
        

        /** 
         * kcp_csp_headers
         * 
         * The method is responsible for setting up the content security policy header settings
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return array Returns an array of all the settings necessary
         * 
         */
        private function kcp_csp_headers(): array {

            // hold the return and the directives array
            $ret = [];
            $dir_arr = [];
            
            // now hold all our directives
            $dir = \KCP_CSPGEN_Configs::get_csp_directives();

            // build out the initial fields
            $ret = [
                [
                    'id' => 'generate_csp',
                    'type' => 'switch',
                    'label' => __('Generate the CSP?', 'security-header-generator'),
                    'description' => __('Setting this will set the flag for generating a Content Security Policy.  See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP</a>', 'security-header-generator'),
                    'on_label' => __('Yes', 'security-header-generator'),
                    'off_label' => __('No', 'security-header-generator'),
                    'default' => false,
                ],
                [
                    'id' => 'apply_csp_to_admin',
                    'type' => 'switch',
                    'label' => __('Apply it to the Admin?', 'security-header-generator'),
                    'description' => __('This will attempt to apply the Content Security Policy Headers to the admin side of your site in addition to the front-end.', 'security-header-generator'),
                    'on_label'  => __('Yes', 'security-header-generator'),
                    'off_label' => __('No', 'security-header-generator'),
                    'default'   => false,
                    'conditional' => [
                        'field' => 'generate_csp',
                        'value' => true,
                        'condition' => '==',
                    ],

                ],
                [
                    'id' => 'apply_csp_preset',
                    'type' => 'select',
                    'label' => __('Apply a preset?', 'security-header-generator'),
                    'description' => __('Select a preset to start with...', 'security-header-generator'),
                    'default' => ['none'],
                    'options' => $this->manage_presets(),
                    'class' => 'kpsh-full-field',
                    'conditional' => [
                        'field' => 'generate_csp',
                        'value' => true,
                        'condition' => '==',
                    ],

                ],
                [
                    'id' => 'csp_basic_auth',
                    'label' => __('Basic Authentication', 'security-header-generator'),
                    'description' => __('Enter your Basic Auth Username and Password, if your site has this protection. (aka: htaccess protection, or htpasswd)<br /><strong>NOTE:</strong> This is stored unencrypted.', 'security-header-generator'),
                    'type' => 'group',
                    'fields' => [
                        [
                            'id' => 'auth_un',
                            'type' => 'text',
                            'label'  => __( 'Username', 'security-header-generator' ),
                            'inline' => true,
                        ],
                        [
                            'id' => 'auth_pw',
                            'type' => 'password',
                            'label'  => __( 'Password', 'security-header-generator' ),
                            'inline' => true,
                        ],
                    ],
                    'conditional' => [
                        'field' => 'generate_csp',
                        'value' => true,
                        'condition' => '==',
                    ],
                ],

            ];

            // now... loop over the directives
            foreach($dir as $k => $v) {

                // check if we're at the sandbox directive
                if ($v['id'] == 'generate_csp_custom_sandbox') {

                    // add it to the array
                    $dir_arr[] = [
                        'id' => $v['id'],
                        'type' => 'checkboxes',
                        'label' => __($v['title'], 'security-header-generator'),
                        'description' => __($v['desc'], 'security-header-generator'),
                        'options' => [
                            'allow-downloads' => __('allow-downloads', 'security-header-generator'),
                            'allow-downloads-without-user-activation' => __('allow-downloads-without-user-activation', 'security-header-generator'),
                            'allow-forms' => __('allow-forms', 'security-header-generator'),
                            'allow-modals' => __('allow-modals', 'security-header-generator'),
                            'allow-orientation-lock' => __('allow-orientation-lock', 'security-header-generator'),
                            'allow-pointer-lock' => __('allow-pointer-lock', 'security-header-generator'),
                            'allow-popups' => __('allow-popups', 'security-header-generator'),
                            'allow-popups-to-escape-sandbox' => __('allow-popups-to-escape-sandbox', 'security-header-generator'),
                            'allow-presentation' => __('allow-presentation', 'security-header-generator'),
                            'allow-same-origin' => __('allow-same-origin', 'security-header-generator'),
                            'allow-scripts' => __('allow-scripts', 'security-header-generator'),
                            'allow-storage-access-by-user-activation' => __('allow-storage-access-by-user-activation', 'security-header-generator'),
                            'allow-top-navigation' => __('allow-top-navigation', 'security-header-generator'),
                            'allow-top-navigation-by-user-activation' => __('allow-top-navigation-by-user-activation', 'security-header-generator'),
                            'allow-top-navigation-to-custom-protocols' => __('allow-top-navigation-to-custom-protocols', 'security-header-generator'),
                        ],
                        'inline' => true,
                        'conditional' => [
                            'field' => 'generate_csp',
                            'value' => true,
                            'condition' => '==',
                        ],
                    ];
                    
                // check if we're at the report-to directive
                } elseif ($v['id'] == 'generate_csp_report_to') {

                    // add it to the array
                    $dir_arr[] = [
                        'id' => $v['id'],
                        'type' => 'text',
                        'label'  => __( $v['title'], 'security-header-generator' ),
                        'description' => __($v['desc'], 'security-header-generator'),
                        'conditional' => [
                            'field' => 'generate_csp',
                            'value' => true,
                            'condition' => '==',
                        ],
                    ];

                // proceed with the rest
                } else {

                    // add it to the array
                    $dir_arr[] = [
                        'id' => sprintf('csp_group_%s', $v['id']),
                        'type' => 'group',
                        'label' => __($v['title'], 'security-header-generator'),
                        'fields' => [
                            [
                                'id' => $v['id'],
                                'type' => 'text',
                                'description' => __($v['desc'], 'security-header-generator'),
                                'inline' => true,
                            ],
                            [
                                'id' => sprintf('%s_allow_unsafe', $v['id']),
                                'type' => 'checkboxes',
                                'description' => __($v['desc'], 'security-header-generator'),
                                'options' => $this->manage_extras($v['id']),
                                'inline' => true,
                            ],
                        ],
                        'conditional' => [
                            'field' => 'generate_csp',
                            'value' => true,
                            'condition' => '==',
                        ],
                    ];

                }

            }

            // return the array
            return array_merge($ret, $dir_arr);

        }

        /** 
         * kcp_permissions_policy_headers
         * 
         * The method is responsible for setting up the permissions/feature policy header settings
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return array Returns an array of all the settings necessary
         * 
         */
        private function kcp_perm_headers(): array {

            // set the fields array
            $ret = [
                [
                    'id' => 'feature_policy',
                    'type' => 'switch',
                    'label' => __('Generate a Feature Policy (aka Permissions-Policy)?', 'security-header-generator'),
                    'description' => __('Setting this will add another header to configure browser and frame permissions. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Feature-Policy" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Feature-Policy</a><br /><br />NOTE:  Some of these features are not implemented for all browsers, and/or could be experimental.  Please read through that information and decide what features you need, and what audiences you need to apply to.', 'security-header-generator'),
                    'on_label' => __('Yes', 'security-header-generator'),
                    'off_label' => __('No', 'security-header-generator'),
                    'default' => false,
                ],
                [
                    'id' => 'apply_fp_to_admin',
                    'type' => 'switch',
                    'label' => __('Apply to Admin?', 'security-header-generator'),
                    'description' => __('This will attempt to apply the Feature Policy Headers to the admin side of your site in addition to the front-end.', 'security-header-generator'),
                    'on_label'  => __('Yes', 'security-header-generator'),
                    'off_label' => __('No', 'security-header-generator'),
                    'default'   => false,
                    'conditional' => [
                        'field' => 'feature_policy',
                        'value' => true,
                        'condition' => '==',
                    ],
                ],
                [
                    'id' => 'fp_section',
                    'label' => __('Allowed Policy Directives', 'security-header-generator'),
                    'type' => 'message',
                    'message_type' => 'info', // info, success, warning, error
                    'content' => __('Select the policy directives you would like to allow, along with its origins. See here for more information: <a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Feature-Policy#directives" target="_blank">https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Feature-Policy#directives</a>', 'security-header-generator'),
                    'conditional' => [
                        'field' => 'feature_policy',
                        'value' => true,
                        'condition' => '==',
                    ],
                ],
            ];

            // return the array
            return array_merge($ret, $this->kcp_feature_policy_fields());
        }

        /** 
         * kcp_feature_policy_fields
         * 
         * The method is responsible for generating an array of feature policies available to configure
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return array Returns an array of the feature policies fields
         * 
         */
        private function kcp_feature_policy_fields(): array
        {

            // get the array of policies
            $policies = KCP_CSPGEN_Configs::get_permissions_directives();

            // setup the returnable array
            $ret = [];

            // loop over the policies array and add the approriate field
            foreach ($policies as $k => $v) {

                $ret[] = [
                    'id' => $v['id'],
                    'type' => 'group',
                    'label' => __($v['title'], 'security-header-generator'),
                    'description' => __($v['desc'], 'security-header-generator'),
                    'fields' => [
                        [
                            'id' => sprintf('fp_%s', $k),
                            'type' => 'radio',
                            'options' => array(
                                '0' => __('None', 'security-header-generator'),
                                '1' => __('Any', 'security-header-generator'),
                                '2' => __('Self', 'security-header-generator'),
                                '3' => __('Source', 'security-header-generator'),
                            ),
                            'default' => ['1'],
                            'inline' => true,
                        ],
                        [
                            'id' => sprintf('fp_%s_src_domain', $k),
                            'type' => 'text',
                            'label' => __('Source Domains', 'security-header-generator'),
                            'description' => __('Space-delimited list of allowed source URIs. Please make sure they include the http(s):// and each is enclosed in quotes.', 'security-header-generator'),
                            'inline' => true,
                        ],
                    ],
                    'conditional' => [
                        'field' => 'feature_policy',
                        'value' => true,
                        'condition' => '==',
                    ],
                ];
            }

            // return the fields
            return $ret;
        }

        /** 
         * kcp_documentation
         * 
         * The method is responsible for pulling in and rendering the "documentation" page in admin
         * 
         * @since 7.4
         * @access public
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @return string Returns string of the page
         * 
         */
        private function kcp_documentation(): string
        {

            // include our implementation... because there is PHP processing, we need to utilize output bufferring
            ob_start();

            // include the file
            include_once(WPSH_PATH . '/work/doc.php');

            // throw the contents of the buffer into the out variable
            $_out = ob_get_contents();

            // clean the output bufferring and end it
            ob_end_clean();

            // return the rendered content
            return $_out;
        }

        /** 
         * manage_extras
         * 
         * The method is responsible for properly setting up the "extras" options
         * 
         * @since 7.4
         * @access private
         * @author Kevin Pirnie <me@kpirnie.com>
         * @package Kevin's Security Header Generator
         * 
         * @param array $_extras The extras array
         * 
         * @return array Returns array of options
         * 
         */
        protected function manage_extras(string $_item): array
        {

            // setup a returnable array
            $_ret = array();

            // if the item does not need inline or eval
            if ($_item != 'generate_csp_custom_baseuri') {
                $_ret[1] = __('Inline', 'security-header-generator');
                $_ret[2] = __('Eval', 'security-header-generator');
            }

            // by default all items need these
            $_ret[0] = __('Self', 'security-header-generator');
            $_ret[3] = __('None', 'security-header-generator');

            // return the array
            return $_ret;
        }


        protected function manage_presets(): array
        {

            // hold the returnable array
            $ret['none'] = __('No Preset', 'security-header-generator');

            // get the presets
            $presets = KCP_CSPGEN_Presets::get_presets();

            // loop them
            foreach ($presets as $key => $val) {
                $ret[$key] = $val['name'];
            }

            // return the array
            return $ret;
        }
    }
}
