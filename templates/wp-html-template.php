<?php
  foreach($data as $key => $content):
    $content_url = !empty($content["url"][odm_language_manager()->get_current_language()]) ? $content["url"][odm_language_manager()->get_current_language()] : $content["url"]["en"];

    $post_id = url_to_postid($content_url);

    if ($post_id > 0):
      $post_object = get_post( $post_id );
      $content = apply_filters('translate_text', $post_object->post_content, odm_language_manager()->get_current_language());
      echo $content;
    endif;

  endforeach;
?>
