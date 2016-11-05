  <p>
		<label for='related_contents_lable'>Label</label>
    <input id="related_contents_lable" placeholder="English" type="text" lang="en" name="related_contents_lable" value="" size="35" />
		<?php
		if(odm_language_manager()->get_the_language_by_site()){
			$localize = odm_language_manager()->languages_by_theme[odm_country_manager()->get_current_country()];	?>
			<input id="related_contents_lable" placeholder="<?php echo odm_language_manager()->get_the_language_by_site(); ?>" type="text" name="related_contents_lable" lang="<?php echo $localize;?>" value="" size="35" />
			<?php
		} ?>
	</p>
	<p>
		<label for='related_contents_url'>Link</label>
	  <input id="related_contents_url" placeholder="URL" type="text" name="related_contents_url" value="" size="52" />
		<select name="related_contents_type" id="related_contents_type">
			<option value="">Select type</option>
			<?php
			$related_types = get_related_types();
			foreach ($related_types as $type){
					echo '<option value="'.$type.'">'.$type.'</option>';
			}
			?>
		</select>
    <input id="related_add_button" class="button add" type="button" value="Add" />
  </p>
	<div class="related_error" style="color:red"></div>
	<div id="related_list_multiple_box">
		<div id="multiple-site">
			<input type="radio" id="related_list_en" class="en" name="language_site_related_list" value="en" checked />
			<label for="related_list_en"><?php _e('English', 'wp-odm_profile_pages'); ?></label> &nbsp;
			<input type="radio" id="related_list_localization" class="localization" name="language_site_related_list" value="localization" />
			<label for="related_list_localization"><?php _e(odm_language_manager()->get_the_language_by_site(), 'wp-odm_profile_pages'); ?></label>
		</div>

		<div id="related_list_box">
	  	<div class="language_settings language-en">
		  	<div id="related_list"></div>
			</div>
			<?php if (odm_language_manager()->get_the_language_by_site()) {   ?>
			<div class="language_settings language-localization">
	  		<div id="related_list_localize" class="localize_list"></div>
			</div>
			<?php } ?>
		</div>
	</div>


  <input id="related_contents" name="related_contents" type="hidden" value='<?php echo $related_contents_json; ?>'/>
