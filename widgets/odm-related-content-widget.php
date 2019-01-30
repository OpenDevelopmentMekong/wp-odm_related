<?php

class Odm_Related_Content_Widget extends WP_Widget {

	private $types;
	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		// widget actual processes
		parent::__construct(
			'odm_list_related_content_widget',
			__('ODM Related Content Widget', 'odm'),
			array('description' => __('Displays content related to the current post with different templates', 'odm'))
		);

		$this->types = $this->generate_types();
	}

  function generate_types()
  {
    $related_type = !empty($GLOBALS['wp_odm_related_options']->get_option('related_type')) ? $GLOBALS['wp_odm_related_options']->get_option('related_type') : implode(',', get_related_types());
    $related_types = explode(",",$related_type);

    $types = array();
  	foreach ($related_types as $related_type) {
      $types[$related_type] = array();
      $types[$related_type]["label"] = $related_type;
      if (in_array($related_type,array_keys(get_supported_ckan_types()))):
        $types[$related_type]["templates"] = array( "default");
      elseif (in_array($related_type,array_keys(get_supported_profile_types()))):
        $types[$related_type]["templates"] = array( "default");
      else:
        $types[$related_type]["templates"] = array( "default", "html", "thumbnail", "grid");
      endif;
    }

		return $types;

  }

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
    global $post;

    $title = !empty($instance['title']) ? $instance['title'] : 'Related content';
    $limit = isset($instance['limit']) ? $instance['limit'] : -1;
    $type = isset($instance['type']) ? $instance['type'] : 'dataset';
		$template = isset($instance['template']) ? $instance['template'] : 'default';
    $max_height = isset($instance['max_height']) ? $instance['max_height'] : '200';
		$related_content = get_post_meta($post->ID,'related_content',true);
		$data = json_decode($related_content,true);
		$set_max_height = null;
		if($template == "html"){
			$set_max_height = " style='max-height:".$max_height."px; overflow-y:auto;'";
		}

		$typed_data = array();
		if($data):
			foreach ($data as $item):
				if ($item["type"] == $type):
					array_push($typed_data,$item);
				endif;
			endforeach;
		endif;

		if ($data !== null && !empty($typed_data)):

			echo $args['before_widget']; ?>

			<div class="container">
				<div class="sixteen columns">
					<?php
						if (!empty($instance['title'])): ?>
							<a><?php echo $args['before_title'].apply_filters('widget_title', __($instance['title'], 'odm')).$args['after_title']; ?></a>
					<?php endif; ?>
				</div>

				<div class="sixteen columns"<?php echo $set_max_height; ?>>
	        <?php
	          if ($limit > -1):
	            $typed_data = array_slice($typed_data,0,$limit);
	          endif;
	          echo render_template_for_related_content($typed_data,$type,$template);
	        ?>
				</div>

			</div>

	    <?php echo $args['after_widget']; ?>

		<?php
			wp_reset_query();
		endif;
	}


	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

    $title = !empty($instance['title']) ? $instance['title'] : 'Related content';
    $limit = isset($instance['limit']) ? $instance['limit'] : -1;
    $type = isset($instance['type']) ? $instance['type'] : 'dataset';
		$template = isset($instance['template']) ? $instance['template'] : 'default';
    $max_height = isset($instance['max_height']) ? $instance['max_height'] : '200';
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title:');?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php _e($title,'odm');?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Select content type:' ); ?></label>
			<select class='widefat type' id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" type="text">
				<?php foreach ( $this->types  as $key => $available_type ): ?>
					<option <?php if ($type == $available_type["label"]) { echo " selected"; } ?> value="<?php echo $key ?>"><?php echo $available_type["label"] ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e( 'Select layout:' ); ?></label>
			<select class='widefat template' id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" type="text">
				<?php
          $current_type = $this->types[$type];
          foreach ( $current_type["templates"]  as $key => $available_template ): ?>
					<option <?php if ($template == $available_template) { echo " selected"; } ?> value="<?php echo $available_template ?>"><?php echo $available_template ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php $limit = !empty($instance['limit']) ? $instance['limit'] : -1 ?>
		<p class="<?php echo $this->get_field_id('limit');?>">
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Select max number of posts to list (-1 to show all):' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('limit');?>" name="<?php echo $this->get_field_name('limit');?>" type="number" value="<?php echo $limit;?>">
		</p>
			<?php $max_height = !empty($instance['max_height']) ? $instance['max_height'] : '200' ?>
		<p class="<?php echo $this->get_field_id('max_height');?>">
			<label for="<?php echo $this->get_field_id( 'max_height' ); ?>"><?php _e( 'Define the max height of container:' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('max_height');?>" name="<?php echo $this->get_field_name('max_height');?>" type="number" value="<?php echo $max_height;?>">
		</p>

		<script type="text/javascript">
			jQuery(function($) {
				var $select_template = "<?php echo $this->get_field_id('template'); ?>";
				display_max_height();
				$('#'+$select_template).change(function(){
					display_max_height();
				});

				function display_max_height (){
					var $p_limit =  "<?php echo $this->get_field_id('limit');?>";
					var $p_max_height =  "<?php echo $this->get_field_id('max_height');?>";
					if($('#'+$select_template).val() == "html"){
						$('.'+$p_limit).hide();
						$('.'+$p_max_height).show();
					}else{
						$('.'+$p_limit).show();
						$('.'+$p_max_height).hide();
					}
				}
			});
	 </script>

		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? $new_instance['title'] : 'Related content';
		$instance['limit'] = (!empty($new_instance['limit'])) ? $new_instance['limit'] : -1;
		$instance['type'] = (!empty( $new_instance['type'])) ? $new_instance['type'] : 'dataset';
		$instance['template'] = (!empty( $new_instance['template'])) ? $new_instance['template'] : 'default';
		$instance['max_height'] = (!empty( $new_instance['max_height'])) ? $new_instance['max_height'] : '200';

		return $instance;
	}
}

add_action( 'widgets_init', function(){register_widget("Odm_Related_Content_Widget");});
