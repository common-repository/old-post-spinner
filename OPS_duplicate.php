<?php
/*  Part of this code is Copyright 2009-2010 by Enrico Battocchi  (email : enrico.battocchi@gmail.com)

/**
 * Escape single quotes, specialchar double quotes, and fix line endings.
 */
function OPS_js_escape($text) {
	if (function_exists('js_escape')) {
		return js_escape($text);
	} else {
		$safe_text = str_replace('&&', '&#038;&', $text);
		$safe_text = str_replace('&&', '&#038;&', $safe_text);
		$safe_text = preg_replace('/&(?:$|([^#])(?![a-z1-4]{1,8};))/', '&#038;$1', $safe_text);
		$safe_text = str_replace('<', '&lt;', $safe_text);
		$safe_text = str_replace('>', '&gt;', $safe_text);
		$safe_text = str_replace('"', '&quot;', $safe_text);
		$safe_text = str_replace('&#039;', "'", $safe_text);
		$safe_text = preg_replace("/\r?\n/", "\\n", addslashes($safe_text));
		return safe_text;
	}
}

/**
 * Get a post from the database
 */
function OPS_get_post($id) {
	global $wpdb;
	$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$id");
	if ($post->post_type == "revision"){
		$id = $post->post_parent;
		$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$id");
	}
	return $post[0];
}

/**
 * Copy the taxonomies of a post to another post
 */
function OPS_copy_post_taxonomies($id, $new_id, $post_type) {
	global $wpdb;
	if (isset($wpdb->terms)) {
		// WordPress 2.3
		$taxonomies = get_object_taxonomies($post_type); //array("category", "post_tag");
		foreach ($taxonomies as $taxonomy) {
			$post_terms = wp_get_object_terms($id, $taxonomy);
			for ($i=0; $i<count($post_terms); $i++) {
				wp_set_object_terms($new_id, $post_terms[$i]->slug, $taxonomy, true);
			}
		}
	}
}

/**
 * Copy the meta information of a post to another post
 */
function OPS_copy_post_meta_info($id, $new_id) {
	global $wpdb;
	$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$id");

	if (count($post_meta_infos)!=0) {
		$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
		#$meta_no_copy = explode(",",get_option('duplicate_post_blacklist'));
		foreach ($post_meta_infos as $meta_info) {
			$meta_key = $meta_info->meta_key;
			$meta_value = addslashes($meta_info->meta_value);
			
			#if (!in_array($meta_key,$meta_no_copy)) {
				$sql_query_sel[]= "SELECT $new_id, '$meta_key', '$meta_value'";
			#}
			
		}
		
		$sql_query.= implode(" UNION ALL ", $sql_query_sel);
		$wpdb->query($sql_query);
	}
}

/**
 * Create a duplicate from a post
 */
function OPS_create_duplicate_from_post($post, $keep_date) {
	global $wpdb;
	$new_post_author_id = $post->post_author;
	$new_post_date = ($keep_date)?  $post->post_date : current_time('mysql');
	$new_post_date_gmt = get_gmt_from_date($new_post_date);
	#$prefix = get_option('duplicate_post_title_prefix');
	#if (!empty($prefix)) $prefix.= " ";

	$new_post_type 	= $post->post_type;
	$post_content    = str_replace("'", "''", $post->post_content);
	$post_content_filtered = str_replace("'", "''", $post->post_content_filtered);
	$post_excerpt    = str_replace("'", "''", $post->post_excerpt);
	#$post_title      = $prefix.str_replace("'", "''", $post->post_title);
	$post_title      = str_replace("'", "''", $post->post_title);
	$post_status     = str_replace("'", "''", $post->post_status);
	$post_name       = str_replace("'", "''", $post->post_name);
	$comment_status  = str_replace("'", "''", $post->comment_status);
	$ping_status     = str_replace("'", "''", $post->ping_status);

	// Insert the new template in the post table
	$sql="INSERT INTO $wpdb->posts
			(post_author, post_date, post_date_gmt, post_content, post_content_filtered, post_title, post_excerpt,  post_status, post_type, comment_status, ping_status, post_password, to_ping, pinged, post_modified, post_modified_gmt, post_parent, menu_order, post_mime_type, post_name)
			VALUES
			('$new_post_author_id', '$new_post_date', '$new_post_date_gmt', '$post_content', '$post_content_filtered', '$post_title', '$post_excerpt', '".OPS_DUPLICATE_DEFAULT_STATUS."', '$new_post_type', '$comment_status', '$ping_status', '$post->post_password', '$post->to_ping', '$post->pinged', '$new_post_date', '$new_post_date_gmt', '$post->post_parent', '$post->menu_order', '$post->post_mime_type', '$post_name')";
	
	$wpdb->query($sql);
			
	$new_post_id = $wpdb->insert_id;

	// Copy the taxonomies
	OPS_copy_post_taxonomies($post->ID, $new_post_id, $post->post_type);

	// Copy the meta information
	OPS_copy_post_meta_info($post->ID, $new_post_id);

	return $new_post_id;
}

?>