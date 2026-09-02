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

// remove our settings
delete_option('wpsh_settings');

// remove the pre-migration backup
delete_option('wpsh_settings_pre_migration_backup');

// remove the schema version marker
delete_option('wpsh_settings_schema_version');
