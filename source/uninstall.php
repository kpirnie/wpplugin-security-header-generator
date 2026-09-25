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
if (
	! defined('WP_UNINSTALL_PLUGIN') || ! WP_UNINSTALL_PLUGIN ||
	dirname(WP_UNINSTALL_PLUGIN) != dirname(plugin_basename(__FILE__))
) {
	exit;
}

// the options we need to remove
$_wpsh_options = array(
	'wpsh_settings',
	'wpsh_settings_pre_migration_backup',
	'wpsh_settings_schema_version',
);

// multisite activates per subsite, so every site may hold its own copy
if (is_multisite()) {

	// loop over every site in the network
	foreach (get_sites(array('fields' => 'ids', 'number' => 0)) as $_wpsh_site_id) {

		// switch to the site
		switch_to_blog($_wpsh_site_id);

		// remove our options
		foreach ($_wpsh_options as $_wpsh_option) {
			delete_option($_wpsh_option);
		}

		// switch back
		restore_current_blog();
	}
} else {

	// remove our options
	foreach ($_wpsh_options as $_wpsh_option) {
		delete_option($_wpsh_option);
	}
}
