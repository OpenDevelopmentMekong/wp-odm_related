<?php
/**
 * Plugin Name: ODM Related Contents
 * Plugin URI: http://github.com/OpenDevelopmentMekong/odm_related
 * Description: Internal wordpress plugin for associating related contents in posts with different templates to display on post's page through the widgets.
 * Version: 1.0.0
 * Author: Huy Eng HENG
 * Author URI: https://opendevelopmentcambodia.net
 * License: GPLv3.
 */

include_once plugin_dir_path(__FILE__).'utils/utils.php';
include_once plugin_dir_path(__FILE__).'utils/wp-odm_related-options.php';

$GLOBALS['wp_odm_related_options'] = new Wp_odm_related_Options();

if (!class_exists('Odm_Related_Contents_Plugin')) {
    class Odm_Related_Contents_Plugin
    {
				/**
				* Construct the plugin object.
				*/
		    public function __construct()
		    {
		        add_action('init', array($this, 'register_styles'));
						add_action('init', array($this, 'load_text_domain'));
		        add_action('admin_notices', array($this, 'check_requirements'));
            add_action('admin_menu', array($this, 'admin_add_menu'));
            add_action('admin_init', array($this, 'admin_init_settings'));
						add_action('add_meta_boxes', array(&$this, 'add_related_meta_boxes'));
            //add_action('edit_post', array(&$this, 'related_edit_post'));
            add_action('save_post', array(&$this, 'related_save_post'));
		    }

		    public function register_styles()
		    {
	        wp_enqueue_style('style-related-content',  plugin_dir_url(__FILE__).'css/related.css');
	        wp_enqueue_style('responsive-related-contents',  plugin_dir_url(__FILE__).'css/responsive.css');
		    }

		    public function load_text_domain()
				{
					load_plugin_textdomain( 'wp-odm_related', false,  dirname( plugin_basename( __FILE__ ) ) . '/i18n' );
				}

		    public function check_requirements(){
		      if (!check_requirements_related_content()):
		        echo '<div class="error"><p>ODM Related Contents: WPCKAN plugin is missing, deactivated or missconfigured. Please check, otherwise, related contents between Wordpress and Dataset of CKAN will not been cteated.</p></div>';
		      endif;
		    }

			 	/**
			  * custom option and settings
			  */
		    public function admin_init_settings() {
          $section_group = 'related_setting_section';

          $section_name = 'related_post_types';
          $posttypes_section = 'posttypes_section';
          add_settings_section(
            $posttypes_section,
            __( 'Post Types', 'wp-odm_related' ),
            '',
            $section_name
          );

          add_settings_field(
           'supported_post_types',
           __('Post Types that require related contents ', 'wp-odm_related' ),
           array($this, 'enable_supported_post_types'),
           $section_name,
           $posttypes_section
          );

          register_setting($section_group, "related_type");
          register_setting($section_group, $section_name);
				}

        public function enable_supported_post_types(){
          $public_post_types = get_post_types(array('public' => true, '_builtin' => false));
          sort($public_post_types);

          $post_type_option = supported_post_types_option();
          foreach ( $public_post_types as $post_type) {
               if(array_key_exists($post_type, $post_type_option)){
                 echo '<input type="checkbox" name="related_post_types['. $post_type.']" value="1" checked="checked">'.$post_type.'<br>';
             } else {
                 echo '<input type="checkbox" name="related_post_types['. $post_type.']" value="">'.$post_type.'<br>';
             }
         }
        }

				/**
         * add a menu.
         */

				public function admin_add_menu()
        {
            add_options_page('Related Content', 'Related Content', 'manage_options', 'wp-odm_related', array($this, 'plugin_settings_page'));
        }

				/**
				 * Menu Callback.
				 */
				public function plugin_settings_page()
				{
					if (!current_user_can('manage_options')) {
							wp_die(__('You do not have sufficient permissions to access this page.'));
					}

					include sprintf('%s/utils/settings.php', dirname(__FILE__));
				}

				public function add_related_meta_boxes($post_type){
        	if (in_array($post_type, get_post_types()) && supported_post_types_option($post_type)) {
            add_meta_box('related_metabox', __('Add related contents', 'wp-odm_related'), array($this, 'render_related_metabox'), $post_type, 'advanced', 'high');

            wp_register_script('related_contents_js', plugins_url('wp-odm_related/js/related-metabox.js'), array('jquery'));
            wp_enqueue_script('related_contents_js');
          }
				}

				public function render_related_metabox($post){
					$related_label = get_post_meta($post->ID, 'related_label', true);
					$related_url = get_post_meta($post->ID, 'related_url', true);
          $related_contents_json = get_post_meta( $post->ID, 'related_contents', true );
          require 'templates/related-metabox.php';
				}

        public function related_save_post($post_ID)
        {
          if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
              return $post_ID;
          }
          if (!current_user_can('edit_post', $post_ID)) {
              return $post_ID;
          }

          if($_POST['related_contents']){
            $related_contents_json = unset_index_in_related_contents( $_POST['related_contents']);
              // Update the meta field.
            update_post_meta( $post_ID, 'related_contents', $related_contents_json);
          }
        }

		    public static function activate()
		    {
          $public_post_types = get_post_types(array('public' => true, '_builtin' => false));
          foreach ( $public_post_types as $post_type) {
            $default_post_type[$post_type] = 1;
          }
          update_option("related_post_types", $public_post_types);
		    }

		    public static function deactivate()
		    {
		        // Do nothing
		    }
		}
}

if (class_exists('Odm_Related_Contents_Plugin')) {
  register_activation_hook(__FILE__, array('Odm_Related_Contents_Plugin', 'activate'));
  register_deactivation_hook(__FILE__, array('Odm_Related_Contents_Plugin', 'deactivate'));

	$GLOBALS['related'] = new Odm_Related_Contents_Plugin();

  if(isset($GLOBALS['related'])){

    // Add a link to the settings page onto the plugin page
    function add_related_settings_link($links)
    {
      $settings_link = '<a target="_blank" href="options-general.php?page=wp-odm_related">Settings</a>';
      array_unshift($links, $settings_link);

      return $links;
    }

    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_$plugin", 'add_related_settings_link');

  }

}
