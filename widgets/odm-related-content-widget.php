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
      $types[$related_type]["templates"] = array( "default", "html", "thumbnail", "grid", "type_specific", "ckan_timeline");
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

		echo $args['before_widget']; ?>

		<div class="container">
			<div class="eight columns">
				<?php
					if (!empty($instance['title'])): ?>
						<a href="/<?php echo $post_type_slug?>"><?php echo $args['before_title'].apply_filters('widget_title', __($instance['title'], 'odm')).$args['after_title']; ?></a>
				<?php endif; ?>
			</div>

			<div class="sixteen columns">
        <?php
          $related_content = get_post_meta($post->ID,'related_content',true);
          render_template_for_related_content($related_content,$template) ?>
			</div>

			<?php echo $args['after_widget']; ?>
		</div>

	<?php
	wp_reset_query();
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
		$template = isset($instance['template']) ? $instance['template'] : 'default';?>
		<p>
			<label for="<?php echo $this->get_field_id('title');?>"><?php _e('Title:');?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title');?>" name="<?php echo $this->get_field_name('title');?>" type="text" value="<?php _e($title,'odm');?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Select content type:' ); ?></label>
			<select class='widefat type' id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>" type="text">
				<?php foreach ( $this->types  as $key => $availabel_type ): ?>
					<option <?php if ($type == $availabel_type["label"]) { echo " selected"; } ?> value="<?php echo $key ?>"><?php echo $availabel_type["label"] ?></option>
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
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Select max number of posts to list (-1 to show all):' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('limit');?>" name="<?php echo $this->get_field_name('limit');?>" type="number" value="<?php echo $limit;?>">
		</p>

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

		return $instance;
	}
}

add_action( 'widgets_init', create_function('', 'register_widget("Odm_Related_Content_Widget");'));
