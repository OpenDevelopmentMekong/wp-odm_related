<div class="wrap">
  <h2>Related Contents Settings</h2>
  <form method="post" action="options.php">
	  <?php @settings_fields('related-setttings'); ?>
	  <?php @do_settings_fields('related-setttings'); ?>
	  <?php

		$related_type = !empty($GLOBALS['wp_odm_related_options']->get_option('related_type')) ? $GLOBALS['wp_odm_related_options']->get_option('related_type') : implode(',', get_related_types());
		$default_related_type = implode(',', get_related_types());
		?>

		<table class="form-table">
			<tr valign="top">
					<th scope="row">
            <label for="related_type"><?php _e('Supported related content types','wp-odm_related') ?></label>
          </th>

					<td colspan="2">
						<input class="full-width" type="text" name="related_type" id="related_type" value="<?php echo $related_type ?>"/>
						<p class="description"><?php _e('Specify a list of comma-separated type names for selecting the TYPE of a related content on metabox and widget.', 'wp-odm_related') ?>
						<?php _e('The default related content types are:<br/> '.$default_related_type, 'wp-odm_related') ?>
						</p>

					</td>
			</tr>
			<tr valign="top">
				<td colspan="2">
					<?php settings_fields( 'related_setting_section' ); ?>
        	<?php do_settings_sections( 'related_post_types' );  ?>
				</td>
		  </tr>
		</table>
  	<?php @submit_button(); ?>
	</form>
</div>
