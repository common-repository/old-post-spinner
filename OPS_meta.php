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


$ops_meta_boxes =
	array(	 	
		"ops_title" => array(
		"name" => "ops_title",
			"type" => "text",
		"default" => "",
		"title" => __('Title','OldPostSpinner'),
		"description" => __("Please enter a title. Don't forget the spinning characters.",'OldPostSpinner')),
		
		"ops_content" => array(
		"name" => "ops_content",
			"type" => "area",
		"default" => "",
		"title" => __('Content','OldPostSpinner'),
		"description" => __("Please enter the text. Don't forget the spinning characters.",'OldPostSpinner'))
	);

function ops_create_meta_box() {
	global $post, $ops_meta_boxes;
	if ( function_exists('add_meta_box') ) {
		add_meta_box( 'ops-meta-box', __('Old Post Spinner Data','OldPostSpinner'), 'ops_meta_boxes', 'post', 'normal', 'high' );
	}
}

function ops_meta_boxes() {
	global $post, $ops_meta_boxes;
	foreach($ops_meta_boxes as $meta_box) {
		$meta_box_value = get_post_meta($post->ID, $meta_box['name'].'_value', true);
		if ($meta_box_value=="") $meta_box_value=$meta_box['default'];
		echo'<div>';
		echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
		echo'<p><strong>'.$meta_box['title'].'</strong></p>';
		if($meta_box['type']=='text') {
			echo '<input style="margin:0;width:98%;" size="80" type="text" name="'.$meta_box['name'].'_value" id="'.$meta_box['name'].'_value" value="'.$meta_box_value.'" /><br />';
			echo '<p><label for="'.$meta_box['name'].'_value">'.$meta_box['description'].'</label></p>';
		} elseif ($meta_box['type']=='area') {
			print '<textarea style="margin:0;height:14em;width:98%;" rows="10" class="Editor" cols="80" name="'.$meta_box['name'].'_value" tabindex="3" id="'.$meta_box['name'].'_value">'.$meta_box_value.'</textarea>';
			echo '<p><label for="'.$meta_box['name'].'_value">'.$meta_box['description'].'</label></p>';
		}
		echo'</div>';
	} // end foreach
	echo'<br style="clear:both" />';
} // end ops_meta_boxes


function ops_save_postdata($post_id) {
	global $post, $ops_meta_boxes;
	foreach($ops_meta_boxes as $meta_box) {
		// Verify
		if (!wp_verify_nonce($_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__))) {
			return $post_id;
		}
		if ('page'==$_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) return $post_id;
		} else {
			if (!current_user_can('edit_post', $post_id)) return $post_id;
		}

		$data=$_POST[$meta_box['name'].'_value'];

		if(get_post_meta($post_id, $meta_box['name'].'_value') == "") 
			add_post_meta($post_id, $meta_box['name'].'_value', $data, true);
		elseif($data != get_post_meta($post_id, $meta_box['name'].'_value', true))
			update_post_meta($post_id, $meta_box['name'].'_value', $data);
		elseif($data=="" || $data==$meta_box['default'] )
			delete_post_meta($post_id, $meta_box['name'].'_value', get_post_meta($post_id, $meta_box['name'].'_value', true));
	} // end foreach
} // end save_postdata
?>