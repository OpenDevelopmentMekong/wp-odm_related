<?php

function check_requirements_related_content()
{
  return function_exists('wpckan_get_ckan_domain') && function_exists('wpckan_validate_settings_read') && wpckan_validate_settings_read();
}

function supported_post_types_option($post_type=null)
{
	$supported_posttypes = $GLOBALS['wp_odm_related_options']->get_option("related_post_types");
	if($post_type){
		return $supported_posttypes[$post_type];
	} else {
		return $supported_posttypes;
	}
}

function get_related_types()
{
	$wp_post_types = get_post_types(array('public' => true, '_builtin' => false));
	$ckan_types = array(
    "dataset" => "dataset",
    "library_record" => "library_record",
    "laws_record" => "laws_record"
  );

	foreach ($wp_post_types as $key => $value) {
      $wp_types[$key] = $key;
  }
  $list_related_types = array_merge($wp_types, $ckan_types);

  sort($list_related_types);
  return $list_related_types;
}

function unset_index_in_related_content($json_data){
  if($json_data){
    $related_content_arr = json_decode(stripslashes($json_data), true);
    if($related_content_arr){
      foreach ($related_content_arr as $related_key => $related_arr) {
        if(isset($related_arr['index'])){
          unset($related_arr['index']);
        }
        $related_content_no_index[] = $related_arr;
      }

      if($related_content_no_index){
        $related_content = json_encode($related_content_no_index);
        return $related_content;
      }
    }

    return $json_data;
  }
}

function get_post_id_from_url($url){
  return ;
}

function wprelated_output_template($template_url,$data,$atts){
  ob_start();
  require $template_url;
  $output = ob_get_contents();
  ob_end_clean();
  return $output;
}

function render_template_for_related_content($related_content,$template){

  $atts = array();
  echo wprelated_output_template( plugin_dir_path( __FILE__ ) . '../templates/'.$template.'-template.php',$related_content,$atts);

}

?>
