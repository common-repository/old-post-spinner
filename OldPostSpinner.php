<?php
/*
Plugin Name: OPS Old Post Spinner
Plugin URI: http://1manfactory.com/ops
Description: Create a complete unique new post on a random old one and promote it to the top of your blog. Hence creates new posts from old ones back to front page and RSS feed. This plugin should no be used with permalink structures that include dates. <a href="options-general.php?page=OPS_admin.php">Settings</a> 
Version: 2.4.0
Author: Juergen Schulze, 1manfactory.com
Author URI: http://1manfactory.com
License: GNU GPL
*/
/*  Copyright 2010 Juergen Schulze, 1manfactory.com (email : 1manfactory@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define ("OPSDEBUG", false);		# never use debug mode on productive systems
define ("OPSLOGPATH", str_replace('\\', '/', WP_CONTENT_DIR).'/ops-logs/');
define ("OPSVERSION", "2.4.0");

require_once('OPS_core.php');
ops_set_lang_file();
require_once('OPS_admin.php');
require_once('OPS_meta.php');
require_once('OPS_spinner.php');
require_once('OPS_duplicate.php');
if (!class_exists('xmlrpcmsg')) {
	require_once('lib/xmlrpc_inc.php');
}		

# define some default/best practise values
define ('OPS_PLUGNAME', 'OPS Old Post Spinner');
define ('OPS_INTERVAL', 2000); # 2000 minutes
define ('OPS_INTERVAL_SLOP', 500); # 500 minutes
define ('OPS_AGE_LIMIT', 14); # 100 days for reconsidering
define ('OPS_PUPDATE_STRING', __('Originally posted.', 'OldPostSpinner')); 
define ('OPS_POS', 1); # post to position 1
define ('OPS_DUPLICATE_DEFAULT_STATUS', 'publish'); # the default status of every duplicated post (choose draft or publish)


define ('OPS_SPLITCHAR', "|");
define ('OPS_LEFTCHAR', "{");
define ('OPS_RIGHTCHAR', "}");

register_activation_hook(__FILE__, 'ops_activate');
register_deactivation_hook(__FILE__, 'ops_deactivate');
register_uninstall_hook(__FILE__, 'ops_uninstall');

add_action('ops_task_hook', 'ops_old_post_spinner');
if (ops_debug()) add_action('init', 'ops_old_post_spinner');	# run always when site is called for debugging
add_action('admin_menu', 'ops_options_setup');
add_action('admin_menu', 'ops_create_meta_box');
add_action('admin_notices', 'ops_admin_notices');
add_action('admin_head', 'ops_head_admin');
add_action('save_post', 'ops_save_postdata');
add_filter('the_content', 'ops_add_original_date_to_post');

# not really needed ???
//add_filter('the_content', 'ops_run_spinner');


function ops_uninstall() {
	# delete all data stored by Old Post Spinner
	delete_metadata('post', null, 'ops_original_pub_date', null, $delete_all = true);
	delete_metadata('post', null, 'ops_content_value', null, $delete_all = true);
	delete_metadata('post', null, 'ops_title_value', null, $delete_all = true);
	delete_option('ops_on_off');
	delete_option('ops_spin_on_off');
	delete_option('ops_interval');
	delete_option('ops_interval_slop');
	delete_option('ops_age_limit');
	delete_option('ops_omit_cats');
	delete_option('ops_at_bottom');
	delete_option('ops_pos');
	delete_option('ops_at_top');
	delete_option('ops_pupdate_string');
	delete_option('ops_last_update');
	delete_option('ops_duplicate');
	ops_deleteLogFolder();
}

function ops_deactivate() {
	// clean the scheduler
	wp_clear_scheduled_hook('ops_task_hook');
	// delete schedule value as well
	delete_option('ops_task_hook');	
}

function ops_activate() {
	# setting default values
	add_option('ops_on_off', 0);
	add_option('ops_spin_on_off', 0);
	add_option('ops_duplicate', 0);
	add_option('ops_interval', OPS_INTERVAL);
	add_option('ops_interval_slop', OPS_INTERVAL_SLOP);
	add_option('ops_age_limit', OPS_AGE_LIMIT);
	add_option('ops_omit_cats', OPS_OMIT_CATS);
	add_option('ops_at_top', 0);
	add_option('ops_at_bottom', 0);	
	add_option('ops_pos', OPS_POS);
	add_option('ops_pupdate_string', OPS_PUPDATE_STRING);
	ops_createLogFolder();
}

if ( !wp_next_scheduled('ops_task_hook') ) {
    wp_schedule_event( time(), 'hourly', 'ops_task_hook' ); // hourly, daily and twicedaily
}

?>