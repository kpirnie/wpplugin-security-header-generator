<?php
/** 
 * Plugin Uninstaller
 * 
 * Run the plugin uninstaller.  Removes all settings created
 * and the custom post type
 * 
 * @since 7.4
 * @author Kevin Pirnie <me@kpirnie.com>
 * @package Kevin's Security Header Generator
 * 
*/

// make sure we're actually supposed to be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) || ! WP_UNINSTALL_PLUGIN ||
	dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) ) ) {
	exit;
}

// remove our settings
unregister_setting( 'kp_cspgen_settings_group', 'kp_cspgen_settings_name' );

// delete the option
delete_option( 'kp_cspgen_settings_name' );

// remove the CPT
unregister_post_type( 'kcp_csp' );

// remove the post for the CSP
array_map( fn( $id ) => wp_delete_post( $id, true ), get_posts( ['post_type' => 'kcp_csp', 'posts_per_page'=>-1, 'fields'=>'ids'] ) );
