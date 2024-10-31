<?php
/*  Copyright 2010 Juergen Schulze, 1manfactory.com

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

function ops_head_admin() {
	// is this needed?
	wp_enqueue_script('jquery-ui-tabs');
	
	$home = get_settings('siteurl');
	$base = '/'.end(explode('/', str_replace(array('\\','/OPS_admin.php'),array('/',''),__FILE__)));
	$stylesheet = $home.'/wp-content/plugins' . $base . '/css/old_post_spinner.css';
	echo('<link rel="stylesheet" href="' . $stylesheet . '" type="text/css" media="screen" />');
}

function ops_createLogFolder() {
	# create log folder
	if (!@is_dir (OPSLOGPATH)) {
		if (!@mkdir (OPSLOGPATH, 0777)) {
			$ops_on_off=0;
			update_option('ops_on_off', 0);
			$error=true;
		}
	}

	# check if log folder is writeable
	if (!@is_writable(OPSLOGPATH) ) {

		# trying to set permissions
		if (!@chmod(OPSLOGPATH, 0777)) {
			$ops_on_off=0;
			update_option('ops_on_off', 0);
			$error=true;
		}
	} else {
		# create empty index.html file to hide logs from browsing
		$emptyFile=OPSLOGPATH.'index.html';
		$fileWrite = fopen($emptyFile, 'a');
		fclose($fileWrite);
	}
}

function ops_deleteLogFolder() {
	# delete log folder and logs
	if (@is_dir(OPSLOGPATH)) {
		deltree(OPSLOGPATH);
		#@unlink(OPSLOGPATH);
	}
}

function ops_options() {
	if ( !is_admin() || !current_user_can('manage_options') ) {
		wp_die(__("You are not allowed to update options."));
	}
	
	$error=false;
	$message="";

	$error=ops_check_folder_error();


	if (!empty($_POST['ops_action']) && !$error) {

		$ops_on_off=$_POST['ops_on_off'];
		$ops_spin_on_off=$_POST['ops_spin_on_off'];
		$ops_duplicate=$_POST['ops_duplicate'];
		$ops_interval=$_POST['ops_interval'];
		$ops_interval_slop=$_POST['ops_interval_slop'];
		$ops_age_limit=$_POST['ops_age_limit'];
		$ops_pos=$_POST['ops_pos'];
		$ops_at_bottom=$_POST['ops_at_bottom'];
		$ops_at_top=$_POST['ops_at_top'];
		$ops_pupdate_string=$_POST['ops_pupdate_string'];
		$ops_omit_cats=$_POST['post_category'];	# key name defined by Wordpress (this is an array)

		$message = __("Old Post Spinner Options Updated.", 'OldPostSpinner');

		# only log if necessary
		if ($ops_on_off==1 && get_option('ops_on_off')==0) {
			ops_writelog(__('OPS goes on.', 'OldPostSpinner'), __FUNCTION__, __LINE__);
		} elseif ($ops_on_off==0 && get_option('ops_on_off')==1) {
			ops_writelog(__('OPS goes off.', 'OldPostSpinner'), __FUNCTION__, __LINE__);
		}

		# only log if necessary
		if ($ops_spin_on_off==1 && get_option('ops_spin_on_off')==0) {
			ops_writelog(__('Content and Titel Spinner goes on.', 'OldPostSpinner'), __FUNCTION__, __LINE__);
		} elseif ($ops_spin_on_off==0 && get_option('ops_spin_on_off')==1) {
			ops_writelog(__('Content and Titel Spinner goes off.', 'OldPostSpinner'), __FUNCTION__, __LINE__);
		}

		if (!preg_match('/^([1-9][0-9]{0,3})$/', $ops_interval)) {
			$error=true;
			$message=__('Please use a value between 1 and 9999 minutes as interval.', 'OldPostSpinner');
		}

		if (!preg_match('/^([1-9][0-9]{0,3})$/', $ops_interval_slop)) {
			$error=true;
			$message=__('Please use a value between 1 and 9999 minutes as random interval.', 'OldPostSpinner');
		}

		if (!is_numeric($ops_age_limit) || ( ((int)($ops_age_limit))!=$ops_age_limit )) {
			$error=true;
			$message=__('Please use a numeric value as post age.', 'OldPostSpinner');
		}

		if (!preg_match('/^[1-9]$/', $ops_pos)) {
			$error=true;
			$message=__('Please use a value between 1 and 9 as position.', 'OldPostSpinner');
		}

		if (strlen($ops_pupdate_string)>20) {
			$error=true;
			$message=__('Please not more than 20 characters for the prefix string.', 'OldPostSpinner');
		}

		if ($error) {
			$class_name="error";
		} else {
			$class_name="updated fade";
			update_option('ops_on_off', $ops_on_off);
			update_option('ops_duplicate', $ops_duplicate);
			update_option('ops_spin_on_off', $ops_spin_on_off);
			update_option('ops_interval', $ops_interval);
			update_option('ops_interval_slop', $ops_interval_slop);
			update_option('ops_age_limit', $ops_age_limit);
			update_option('ops_pos', $ops_pos);
			update_option('ops_at_bottom', $ops_at_bottom);
			update_option('ops_at_top', $ops_at_top);
			update_option('ops_pupdate_string', $ops_pupdate_string);
			if (isset($ops_omit_cats)) {
				update_option('ops_omit_cats', implode(',',$ops_omit_cats));
			} else {
				update_option('ops_omit_cats', "");
			}
		}

		print('
			<div id="message" class="'.$class_name.' fade">
				<p>'.$message.'</p>
			</div>');

	} else {
		$ops_on_off = get_option('ops_on_off');
		if (!isset($ops_on_off)) {
			$ops_on_off = 0;
		}
		$ops_spin_on_off = get_option('ops_spin_on_off');
		if (!isset($ops_spin_on_off)) {
			$ops_spin_on_off = 0;
		}
		$ops_duplicate = get_option('ops_duplicate');
		if (!isset($ops_duplicate)) {
			$ops_duplicate = 0;
		}
		$ops_omit_cats = get_option('ops_omit_cats');
		if (!isset($ops_omit_cats)) {
			$ops_omit_cats="";
		}
		$ops_omit_cats=explode(',',$ops_omit_cats);

		$ops_age_limit = get_option('ops_age_limit');
		if (!isset($ops_age_limit)) {
			$ops_age_limit = OPS_AGE_LIMIT;
		}
		$ops_at_bottom = get_option('ops_at_bottom');
		if (!isset($ops_at_bottom)) {
			$ops_at_bottom = 1;
		}
		$atTop = get_option('ops_at_top');
		if (!isset($atTop)) {
			$atTop = 0;
		}
		$ops_pos = get_option('ops_pos');
		if (!isset($ops_pos)) {
			$ops_pos = 1;
		}
		$ops_interval = get_option('ops_interval');
		if (!(isset($ops_interval) && is_numeric($ops_interval))) {
			$ops_interval = OPS_INTERVAL;
		}
		$ops_interval_slop = get_option('ops_interval_slop');
		if (!(isset($ops_interval_slop) && is_numeric($ops_interval_slop))) {
			$ops_interval_slop = OPS_INTERVAL_SLOP;
		}
		$ops_pupdate_string = get_option('ops_pupdate_string');
		if (!isset($ops_pupdate_string)) {
			$ops_pupdate_string = '';
		}
	}

	require_once('OPS_menu.php');

}

function ops_optionselected($opValue, $value) {
	if($opValue==$value) {
		return 'selected="selected"';
	}
	return '';
}

function ops_radioselected($opValue, $value) {
	if($opValue==$value) {
		return 'checked="checked"';
	}
	return '';
}

function disCheck($error) {
	if ($error) return "disabled";
}

function ops_options_setup() {
	add_options_page('Old Post Spinner '.__('Settings', 'OldPostSpinner'), 'Old Post Spinner', 10, basename(__FILE__), 'ops_options');
}

function disError($message, $error) {
	if ($error) return $message;
}

function ops_admin_notices() {
	$error=ops_check_folder_error();
	if ($error) ops_print_folder_error();
}

function ops_check_folder_error() {
	# check if log folder is writeable
	if (!@is_writable(OPSLOGPATH) ) {
		# trying to set permissions
		if (!@chmod(OPSLOGPATH, 0777)) {
			$ops_on_off=0;
			update_option('ops_on_off', 0);
			$error=true;
		}
	}
	return $error;
}

function ops_print_folder_error() {
	print '<div id="message" class="error">'.__("Old Post Spinner (OPS) Error: Can't write to log folder ", 'OldPostSpinner').OPSLOGPATH.__(". Permissions 777 needed.", 'OldPostSpinner').'</div>';
}

?>