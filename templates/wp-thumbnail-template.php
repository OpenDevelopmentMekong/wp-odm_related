<?php
  foreach($data as $key => $content):
    $content_url = !empty($content["url"][odm_language_manager()->get_current_language()]) ? $content["url"][odm_language_manager()->get_current_language()] : $content["url"]["en"];

    $post_id = url_to_postid($content_url);
    if ($post_id > 0):
      echo odm_get_thumbnail($post_id,false, array( 300, 'auto'));
    endif;
  endforeach;
?>
