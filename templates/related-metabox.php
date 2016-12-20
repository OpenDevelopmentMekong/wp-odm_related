  <div class="related_content_form">
    <p>
  		<label for='related_content_label'>Label</label>
      <input id="related_content_label" placeholder="English" type="text" lang="en" name="related_content_label" value="" size="35" />
  		<?php
  		if(odm_language_manager()->get_the_language_by_site() != "English"):
  			$localize = odm_language_manager()->languages_by_theme[odm_country_manager()->get_current_country()];	?>
  			<input id="related_content_label" placeholder="<?php echo odm_language_manager()->get_the_language_by_site(); ?>" type="text" name="related_content_label" lang="<?php echo $localize;?>" value="" size="35" />
  			<?php
  		endif; ?>
  	</p>
  	<p>
  		<label for='related_content_url'>Link</label>
  	  <input id="related_content_url" placeholder="URL" type="text" name="related_content_url" value="" size="52" />
  		<select name="related_content_type" id="related_content_type">
  			<option value="">Select type</option>
  			<?php
        $related_type = $GLOBALS['wp_odm_related_options']->get_option('related_type');
  			$related_types = explode(",",$related_type);
  			foreach ($related_types as $type){
  				echo '<option value="'.$type.'">'.$type.'</option>';
  			}
  			?>
  		</select>
      <input id="related_add_button" class="button add" type="button" value="Add" />
      <input id="related_update_button" class="button update" type="button" value="Update" />
      <input id="related_cancel_button" class="button cancel" type="button" value="Cancel" />
    </p>
  </div>
	<div class="related_error" style="color:red"></div>
	<div id="related_list_multiple_box">
		<div id="multiple-site">
			<input type="radio" id="related_list_en" class="en" name="language_site_related_list" value="en" checked />
			<label for="related_list_en"><?php _e('English', 'wp-odm_profile_pages'); ?></label> &nbsp;
		  <?php if (odm_language_manager()->get_the_language_by_site() != "English"):   ?>
        <input type="radio" id="related_list_localization" class="localization" name="language_site_related_list" value="localization" />
  			<label for="related_list_localization"><?php _e(odm_language_manager()->get_the_language_by_site(), 'wp-odm_profile_pages'); ?></label>
      <?php endif; ?>
		</div>

		<div id="related_list_box">
	  	<div class="language_settings language-en">
		  	<div id="related_list" class="related_list"></div>
			</div>
			<?php if (odm_language_manager()->get_the_language_by_site() != "English"):   ?>
  			<div class="language_settings language-localization">
  	  		<div id="related_list_localize" class="localize_list related_list"></div>
  			</div>
      <?php endif; ?>
		</div>
	</div>


  <input id="related_content" name="related_content" type="hidden" value='<?php echo $related_content_json; ?>'/>
