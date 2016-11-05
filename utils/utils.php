<?php
function check_requirements_related_content()
{
  return function_exists('wpckan_get_ckan_domain') && function_exists('wpckan_validate_settings_read') && wpckan_validate_settings_read();
}

function supported_post_types_option($post_type=null)
{
	$supported_posttype = get_option("related_post_types");
	if($post_type){
		return $supported_posttype[$post_type];
	} else {
		return $supported_posttype;
	}
}

function get_related_types()
{
	$wp_post_types = get_post_types(array('public' => true, '_builtin' => false));
	$ckan_types = array(
	                "odm_dataset_type_dataset" => "dataset",
	                "odm_dataset_type_library_record" => "library_record",
	                "odm_dataset_type_laws_record" => "laws_record"
	              );

	$relate_type = get_option("related_type");
	if($relate_type){
		$list_types = explode(",", $relate_type);
		foreach ($list_types as $value) {
			$value = trim($value);
			if(in_array($value, $wp_post_types)) {
				$list_related_types["odm_content_type_" . $value] = $value;
			}elseif(in_array($value, $ckan_types)) {
				$list_related_types["odm_dataset_type_" . $value] = $value;
			}else {
				$list_related_types[$value] = trim($value);
			}
		}
	}else {
		foreach ($wp_post_types as $key => $value) {
        $wp_types[ "odm_content_type_" . $key] = $key;
    }
    $list_related_types = array_merge($wp_types, $ckan_types);
  }
  sort($list_related_types);
  return $list_related_types;
}

function show_related_content_in_metabox($json_data){
/*	if(!empty($json_data)){
	$related_contents_arr = json_decode($json_data);
  foreach ($related_contents_arr as $related_key => $related_arr) {
		if(array_key_exists('index', $related_arr)){
		 	unset($related_arr->index);
		}
  }
  $related_contents_update = json_encode($related_contents_arr);
  return $related_contents_update;
}*/
	print_r($json_data);
}

?>
