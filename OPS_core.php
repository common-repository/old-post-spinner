<?php
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


// main function called by scheduler hook every hour
function ops_old_post_spinner () {
	$ops_on_off = get_option('ops_on_off');
	if (!$ops_on_off) return;	# no spinning today
	// check if it is time for an promotion/spinning
	if (ops_update_time()) {
		update_option('ops_last_update', time());
		ops_promote_old_post();
	}
}

// chose one random old post to promote/spin and take ommited categories into account
function ops_promote_old_post () {
	global $wpdb;
	$omitCats = get_option('ops_omit_cats');

	/*
	if (!ops_debug()) {
		$ageLimit=get_option('ops_age_limit');
	} else {
		# no age limit when in debug mode
		$ageLimit=0;
	}
	*/
	$ageLimit=get_option('ops_age_limit');
	
	if (!isset($omitCats)) {
		$omitCats = OPS_OMIT_CATS;
	}
	if (!isset($ageLimit)) {
		$ageLimit = OPS_AGE_LIMIT;
	}
	
	$sql = "SELECT ID
            FROM $wpdb->posts
            WHERE post_type = 'post'
                  AND post_status = 'publish'
                  AND post_date < NOW( ) - INTERVAL ".$ageLimit." DAY
                  ";
    if ($omitCats!='') {
		$sql = $sql."AND NOT(ID IN (SELECT tr.object_id
								 FROM $wpdb->terms  t
									  inner join $wpdb->term_taxonomy tax on t.term_id=tax.term_id and tax.taxonomy='category'
									  inner join $wpdb->term_relationships tr on tr.term_taxonomy_id=tax.term_taxonomy_id
								 WHERE t.term_id IN (".$omitCats.")))";
    }
    $sql = $sql."
            ORDER BY RAND()
            LIMIT 1; ";
	$oldest_post_id = $wpdb->get_var($sql);
	#print $sql;
	// call function to update the old post with the new values
	if (isset($oldest_post_id)) {
		ops_update_old_post($oldest_post_id);
	} else {
		if (ops_debug()) {
			ops_writelog(__("No post found to promote.", 'old_post_spinner'), __FUNCTION__, __LINE__);
		}
	}
}

// update the old post to be promoted/spinned with new values
function ops_update_old_post($oldest_post_id) {
	global $wpdb;
	$post = get_post($oldest_post_id);
	// memorize original post date
	$origPubDate = get_post_meta($oldest_post_id, 'ops_original_pub_date', true);
	$origPubTitle=$post->post_title;
	if (!(isset($origPubDate) && $origPubDate!='')) {
		// no original post date given because of a never before promoted post -> new:original post date=post date
		$sql = "SELECT post_date from ".$wpdb->posts." WHERE ID = '$oldest_post_id';";
		$origPubDate=$wpdb->get_var($sql);
		// store the new created original post date
		add_post_meta($oldest_post_id, 'ops_original_pub_date', $origPubDate);
		$origPubDate=get_post_meta($oldest_post_id, 'ops_original_pub_date', true);
	}
	$ops_pos = get_option('ops_pos');
	if (!isset($ops_pos)) {
		$ops_pos = 1;
	}
	
	if ($ops_pos==1) {
		// promote post to position one with current date
		$new_date = date('Y-m-d H:i:s');
		$new_gmt_date = get_gmt_from_date($new_date);
		ops_writelog(__("Promote to top. The new date (now) is: ".$new_date.' (GMT: '.$new_gmt_date.')', 'old_post_spinner'), __FUNCTION__, __LINE__);
		$found=true;
	} else {
		# picking up the two posts to place the promoted one between
		$offset=$ops_pos-2;
		$lastposts = get_posts('&order=DESC&orderby=post_date&numberposts=2&offset='.$offset);
		#print_r($lastposts);
		if (count($lastposts)<2) {
			# no lastpost found
			ops_writelog(__("No post found to detect the proper date.", 'old_post_spinner'), __FUNCTION__, __LINE__);
		} else {
			$i=1;
			foreach ($lastposts as $lastpost) {
				$post_date[$i] = strtotime($lastpost->post_date);
				$i++;
			}
			# place the new date in between
			$new_timestamp=$post_date[2]+intval(($post_date[1]-$post_date[2])/2);
			$new_date=date('Y-m-d H:i:s', $new_timestamp);
			$new_gmt_date=get_gmt_from_date($new_date);
			ops_writelog(__("Place between: ".date('Y-m-d H:i:s',$post_date[1]).' and : '.date('Y-m-d H:i:s',$post_date[2]), 'old_post_spinner'), __FUNCTION__, __LINE__);
			ops_writelog(__("The new date is: ".$new_date.' (GMT: '.$new_gmt_date.')', 'old_post_spinner'), __FUNCTION__, __LINE__);
			$found=true;
		}
	}

	if (isset($found) && $found==true) {
		# duplicate the post if wanted and keep original date/time
		if (get_option('ops_duplicate')) {
			$duplicate_post_id=OPS_create_duplicate_from_post($post, $keep_date=false);
			ops_writelog(__("Duplicating Post ID: ", 'old_post_spinner').$oldest_post_id."->".$duplicate_post_id, __FUNCTION__, __LINE__);
		}

		# now update the post
		$sql="UPDATE $wpdb->posts SET post_date = '$new_date', post_date_gmt = '$new_gmt_date', post_modified = '$new_date', post_modified_gmt = '$new_gmt_date' ";
		
		# check if we spin title and content also
		if (get_option('ops_spin_on_off')) {
			ops_writelog(__("Content and/or Title will be spinned", 'old_post_spinner'), __FUNCTION__, __LINE__);
			$ops_title_value = get_post_meta($post->ID, 'ops_title_value', true);
			$ops_content_value = get_post_meta($post->ID, 'ops_content_value', true);
			$spinned_title=mysql_real_escape_string(ops_run_spinner($ops_title_value));
			$spinned_content=mysql_real_escape_string(ops_run_spinner($ops_content_value));
			if ($spinned_title!="") {
				$sql.=", post_title='$spinned_title' ";
				# we need a new and unique permalink
				$sanitized_title=sanitize_title($spinned_title);
				$unique_post_slug = wp_unique_post_slug($sanitized_title, $oldest_post_id, "publish", "post", null);
				$sanitized_post_name=sanitize_title($unique_post_slug);
				
				$sql.=", post_name='$sanitized_post_name' ";
				ops_writelog("Sanitized Slug: $sanitized_post_name", __FUNCTION__, __LINE__);
			}
			if ($spinned_content!="") {
				$sql.=", post_content='$spinned_content' ";
			}
		}

		$sql.="WHERE ID = '$oldest_post_id'";

		#print $sql;
		if (!$wpdb->query($sql)) {
			if (ops_debug()) {
				ops_writelog("SQL-Error: ".mysql_error()."\n".$sql, __FUNCTION__, __LINE__);
			}
		}

		ops_writelog("Updating Post No. $oldest_post_id ('$origPubTitle') to Position No. $ops_pos with date $new_date (GMT: $new_gmt_date)", __FUNCTION__, __LINE__);
		if (function_exists('wp_cache_flush')) {
			wp_cache_flush();
		}

		# check for Google XML Sitemaps Generator for WordPress
		if (class_exists("GoogleSitemapGeneratorLoader")) {
			  do_action("sm_rebuild");
			  ops_writelog(__('Creating a new sitemap.', 'old_post_spinner'), __FUNCTION__, __LINE__);
		}
						
								
		
		$permalink = get_permalink($oldest_post_id);

		//reping
		$services = get_settings('ping_sites');
		$services = preg_replace("|(\s)+|", '$1', $services);
		$services = trim($services);

		# call ping services only when not in debug mode
		if ( $services!="" && !ops_debug() ) {
			set_time_limit(300);
			$services = explode("\n", $services);
			foreach ($services as $service) {
				ops_sendXmlrpc($service,$permalink);
			}
		}


	}

}

/**
 * A modified version of WP's ping functionality "weblog_ping" in functions.php
 * Uses correct extended Ping format and logs response from service.
 * @param string $server
 * @param string $path
 */
function ops_sendXmlrpc($server, $permalink) {
	include_once (ABSPATH . WPINC . '/class-IXR.php');
	$path = '';
	// using a timeout of 3 seconds should be enough to cover slow servers
	$client = new IXR_Client($server, ((!strlen(trim($path)) || ('/' == $path)) ? false : $path));
	$client->timeout = 3;
	$client->useragent .= ' -- WordPress/OPS';

	// when set to true, this outputs debug messages by itself
	$client->debug = false;
	$home = trailingslashit(get_option('home'));

	// the extendedPing format should be "blog name", "blog url", "permalink", and "feed url",
	// but it would seem as if the standard has been mixed up. It's therefore good to repeat the feed url.
	// $this->_post_type = 2 if new post and 3 if future post
	if ( $client->query('weblogUpdates.extendedPing', get_settings('blogname'), $home, $permalink, get_bloginfo('rss2_url')) ) {
		ops_writelog("$server was successfully pinged (extended format)", __FUNCTION__, __LINE__);
	} else {
		if ( $client->query('weblogUpdates.ping', get_settings('blogname'), $home) ) {
			ops_writelog("$server was successfully pinged", __FUNCTION__, __LINE__);
		} else {
			ops_writelog($server." could not be pinged. Error message: \"".$client->error->message."\"", __FUNCTION__, __LINE__);
		}
	}
}


// adding the original date (if wanted) to every post when printing on screen
function ops_add_original_date_to_post($content) {
	global $post;
	$ops_pupdate_string=get_option('ops_pupdate_string');
	$wp_date_format=get_option('date_format');
	#$wp_time_format=get_option('time_format');
	$atBottom=get_option('ops_at_bottom');
	if (!isset($atBottom)) {
		$atBottom=false;
	}
	$atTop = get_option('ops_at_top');
	if (!isset($atTop)) {
		$atTop=false;
	}
	$origPubDate = get_post_meta($post->ID, 'ops_original_pub_date', true);

	if (isset($origPubDate) && $origPubDate!='') {
		$origPubDate=date($wp_date_format, strtotime($origPubDate));
		$dateline='<p id="ops"><small>';
		$dateline.=$ops_pupdate_string.' '.$origPubDate;
		$dateline.='</small></p>';
		if ($atTop) {
			$content = $dateline.$content;
		}
		if ($atBottom) {
			$content = $content.$dateline;
		}
	} else {
		# no original date, so return content unchanged
		return $content;
	}
	return $content;
}

// checks if it's time to spinning/promote, hence now lies between minimum intervall and the added random maximal interval
function ops_update_time() {
	$last = get_option('ops_last_update');
	$interval_seconds = get_option('ops_interval')*60;
	$slop_seconds = get_option('ops_interval_slop')*60;

	if (!(isset($interval_seconds) && is_numeric($interval_seconds))) {
		$interval_seconds = OPS_INTERVAL*60;
	}

	if (!(isset($slop_seconds) && is_numeric($slop_seconds))) {
		$slop_seconds = OPS_INTERVAL_SLOP*60;
	}

	if (false === $last) {
		$ret = 1;
	} else if (is_numeric($last)) {
		if (ops_debug()) {
			$ret = ( (time() - $last) > 10);		# always spin, debugging every 10 seconds, 10 seconds giving enough time to switch it off
		} else {
			$ret = ( (time() - $last) > ($interval_seconds+rand(0,$slop_seconds)));
		}
	}
	return $ret;
}


function ops_writelog() {
	$numargs = func_num_args();
	$arg_list = func_get_args();
	if ($numargs >2) $linenumber=func_get_arg(2); else $linenumber="";
	if ($numargs >1) $functionname=func_get_arg(1); else $functionname="";
	if ($numargs >=1) $string=func_get_arg(0);
	if (!isset($string) or $string=="") return;

	$logFile=OPSLOGPATH.'/ops-'.date("Y-m").".log";
	$timeStamp = date("d/M/Y:H:i:s O");

	$fileWrite = fopen($logFile, 'a');

	//flock($fileWrite, LOCK_SH);
	if (ops_debug()) {
		$logline="[$timeStamp] ".html_entity_decode($string)." $functionname $linenumber\r\n";	# for debug purposes
	} else {
		$logline="[$timeStamp] ".html_entity_decode($string)."\r\n";
	}
	fwrite($fileWrite, utf8_encode($logline));
	//flock($fileWrite, LOCK_UN);
	fclose($fileWrite);
}

# deletes all files and folders and subfolders in given folder
function deltree($f) {
	if (@is_dir($f)) {
		foreach(glob($f.'/*') as $sf) {
			if (@is_dir($sf) && !is_link($sf)) {
				@deltree($sf);
			} else {
				@unlink($sf);
			}
		}
	}
	if (@is_dir($f)) rmdir($f);
	return true;
}

function ops_debug() {
	# only run debug on localhost
	if ($_SERVER["HTTP_HOST"]=="localhost" && defined("OPSDEBUG") && OPSDEBUG==true) return true;
}

function ops_set_lang_file() {
	# set the language file
	$currentLocale = get_locale();
	if(!empty($currentLocale)) {
		$moFile = dirname(__FILE__) . "/lang/" . $currentLocale . ".mo";
		if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('OldPostSpinner', $moFile);
	}
}

?>