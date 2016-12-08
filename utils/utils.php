<?php

function check_requirements_related_content()
{
  return function_exists('wpckan_get_ckan_domain') && function_exists('wpckan_validate_settings_read') && wpckan_validate_settings_read();
}

function supported_post_types_option($post_type=null)
{
  $supported_posttypes = !empty($GLOBALS['wp_odm_related_options']->get_option('related_post_types')) ? $GLOBALS['wp_odm_related_options']->get_option('related_post_types') : array();
  if($post_type && in_array($post_type,$supported_posttypes)){
		return $supported_posttypes[$post_type];
	} else {
		return $supported_posttypes;
	}
}

function get_supported_ckan_types(){
  return array(
    "dataset" => "dataset",
    "library_record" => "library_record",
    "laws_record" => "laws_record"
  );
}

function get_supported_wp_types(){
  $wp_types = array();
  $wp_post_types = get_post_types(array('public' => true, '_builtin' => false));

  foreach ($wp_post_types as $key => $post_type):
    if (supported_post_types_option($post_type)):
      $wp_types[$post_type] = $post_type;
    endif;
  endforeach;

  return $wp_types;
}

function get_supported_profile_types(){
  return array(
    "od_profile" => "od_profile"
  );
}

function get_related_types()
{

	$ckan_types = get_supported_ckan_types();
	$wp_types = get_supported_wp_types();
  $profile_types = get_supported_profile_types();

  $list_related_types = $wp_types + $ckan_types + $profile_types;

  return array_keys($list_related_types);
}

function wprelated_output_template($template_url,$data,$atts){
  ob_start();
  require $template_url;
  $output = ob_get_contents();
  ob_end_clean();
  return $output;
}

function render_template_for_related_content($related_content,$type,$template){
	$type = trim($type);
  $atts = array("type" => $type);
  $component = null; 
  if (in_array($type,array_keys(get_supported_wp_types()))):
    $component = "wp";
  elseif (in_array($type,array_keys(get_supported_ckan_types()))):
    $component = "ckan";
  elseif (in_array($type,array_keys(get_supported_profile_types()))):
    $component = "profiles";
  endif;

  return wprelated_output_template( plugin_dir_path( __FILE__ ) . '../templates/'.$component."-".$template.'-template.php',$related_content,$atts);

}

?>
