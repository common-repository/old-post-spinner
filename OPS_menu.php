<div class="wrap">


	<h2><?php print OPS_PLUGNAME.' '.OPSVERSION.' '.__('Settings', 'OldPostSpinner'); ?></h2>
	<div class="postbox-container" style="width:65%;">
					<div class="metabox-holder">
						<div class="meta-box-sortables">
<form id="ops" name="oldpostspinner" action="<?php print get_bloginfo('wpurl'); ?>/wp-admin/options-general.php?page=OPS_admin.php" method="post">

<input type="hidden" name="ops_action" value="ops_update_settings" />

<div class="postbox">
			<div class="handlediv"><br /></div>
			<h3 class="hndle"><span><?php print OPS_PLUGNAME.' '.__('Options', 'OldPostSpinner'); ?></span></h3>
			<div class="inside">


					<fieldset class="options">
						<div class="option">
							<label><?php print __('Switch OPS (Old Post Spinner) ', 'OldPostSpinner'); ?></label>
							<input <?php print disCheck($error); ?> type="radio" name="ops_on_off" id="ops_on_off_on" value="1" <?php print ops_radioselected(1,$ops_on_off); ?> /><?php print __('On', 'OldPostSpinner'); ?>
							<input <?php print disCheck($error); ?> type="radio" name="ops_on_off" id="ops_on_off_off" value="0" <?php print ops_radioselected(0,$ops_on_off); ?> /><?php print __('Off', 'OldPostSpinner'); ?>
							&nbsp;<?php print disError(__('Please correct errors before switching OPS on.', 'OldPostSpinner'), $error); ?>
						</div>
						<div class="option">
							<label><?php print __('Minimum interval between old post promotions: ', 'OldPostSpinner'); ?></label>
							<input type="text" id="ops_interval" name="ops_interval" value="<?php print $ops_interval; ?>" />&nbsp;<?php print __('Minutes', 'OldPostSpinner'); ?>
						</div>
						<div class="option">
							<label><?php print __('Random maximum interval (added to minimum interval): ', 'OldPostSpinner'); ?></label>
							<input type="text" id="ops_interval_slop" name="ops_interval_slop" value="<?php print $ops_interval_slop; ?>" />&nbsp;<?php print __('Minutes', 'OldPostSpinner'); ?>
						</div>
						<div class="option">
							<label><?php print __('Post age before considered for promotion: ', 'OldPostSpinner'); ?></label>
							<input type="text" name="ops_age_limit" id="ops_age_limit" value="<?php print $ops_age_limit; ?>" />&nbsp;<?php print __('Days', 'OldPostSpinner'); ?>
						</div>
						<div class="option">
							<label><?php print __('Promote post to position: ', 'OldPostSpinner'); ?></label>
							<input type="text" id="ops_pos" name="ops_pos" value="<?php print $ops_pos; ?>" />
						</div>
						<div class="option">
							<label><?php print __('Show Original Publication Date At Top of Post? ', 'OldPostSpinner'); ?></label>
							<input type="radio" name="ops_at_top" id="ops_at_top_1" value="1" <?php print ops_radioselected(1,$ops_at_top); ?> /><?php print __('Yes', 'OldPostSpinner'); ?>
							<input type="radio" name="ops_at_top" id="ops_at_top_0" value="0" <?php print ops_radioselected(0,$ops_at_top); ?> /><?php print __('No', 'OldPostSpinner'); ?>
						</div>
						<div class="option">
							<label><?php print __('Show Original Publication Date at Post End? ', 'OldPostSpinner'); ?></label>
							<input type="radio" name="ops_at_bottom" id="ops_at_bottom_1" value="1" <?php print ops_radioselected(1,$ops_at_bottom); ?> /><?php print __('Yes', 'OldPostSpinner'); ?>
							<input type="radio" name="ops_at_bottom" id="ops_at_bottom_0" value="0" <?php print ops_radioselected(0,$ops_at_bottom); ?> /><?php print __('No', 'OldPostSpinner'); ?>
						</div>
						<div class="option">
							<label><?php print __('Prefix string for Original Publication Date: ', 'OldPostSpinner'); ?></label>
							<input type="text" id="ops_pupdate_string" name="ops_pupdate_string" value="<?php print $ops_pupdate_string; ?>" />
						</div>
						<div class="option">
							<label><?php print __('Spin content and title?', 'OldPostSpinner'); ?></label>
							<input type="radio" name="ops_spin_on_off" id="ops_spin_on_1" value="1" <?php print ops_radioselected(1,$ops_spin_on_off); ?> /><?php print __('On', 'OldPostSpinner'); ?>
							<input type="radio" name="ops_spin_on_off" id="ops_spin_on_0" value="0" <?php print ops_radioselected(0,$ops_spin_on_off); ?> /><?php print __('Off', 'OldPostSpinner'); ?>
							<br /><?php print __('Use the spinning characters &#x01C0;, &#123; and &#125; to spin your text. Check FAQs for help.', 'OldPostSpinner'); ?>
						</div>

						<div class="option">
							<label><?php print __('Duplicate the post?', 'OldPostSpinner');?></label>
							<input type="radio" name="ops_duplicate" id="ops_duplicate_1" value="1" <?php print ops_radioselected(1,$ops_duplicate); ?> /><?php print __('Yes', 'OldPostSpinner'); ?>
							<input type="radio" name="ops_duplicate" id="ops_duplicate_0" value="0" <?php print ops_radioselected(0,$ops_duplicate); ?> /><?php print __('No', 'OldPostSpinner'); ?>
							<br /><?php print __('This will create a new duplicate of the post. Use only when spinning is switched on.', 'OldPostSpinner'); ?>
						</div>

							<p>
        						<?php print __('Categories to ignore from promotion: ', 'OldPostSpinner'); ?>
							</p>
						    	<div id="categories-all" class="ui-tabs-panel">
						    		<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
								<?php
								wp_category_checklist($post_id=0, $descendants_and_self=0, $selected_cats=$ops_omit_cats, $popular_cats=null, $walker=null, $checked_ontop=false);
								?>
								</ul>
								</div>
							
							
					</fieldset>
								
					<p class="submit" style="margin:0; padding-top:.5em; padding-left:10px;">
						<input type="submit" class="button-primary" name="save" value="<?php _e('Save Changes') ?>" />
					</p>
								</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="postbox-container" style="width:34%;">

					<div class="metabox-holder">
						<div class="meta-box-sortables">
							<div class="postbox">
								<div class="handlediv"><br /></div>
									<h3 class="hndle"><?php _e('Infobox ', 'OldPostSpinner'); ?></h3>
									<div class="inside" style="padding:10px; padding-top:0;">
									<?php require_once('whatsup.php'); ?>
								</div>
							</div>
						</div>
					</div>
					
				</div>

		</div>


	</div>







<?php
	if (!$error) {
		print'<h3>Log</h3><a href="javascript:document.getElementById(\'logIFrame\').contentWindow.location.reload(true);">Refresh</a><br />
			<iframe src="'.WP_PLUGIN_URL. '/old-post-spinner/logview.php?ops_file='.urlencode(OPSLOGPATH.'ops-'.date("Y-m")).'" style="border:1px #DDDDDD none;" id="logIFrame" name="logIFrame" scrolling="yes" frameborder="1" marginheight="0px" marginwidth="0px" height="200" width="900"></iframe>';
	}
?>

</div>

<div>
<div>