<?php
  foreach($data as $key => $content):
    $post_id = url_to_postid($content["url"]);

    if ($post_id > 0):
      $post_object = get_post( $post_id );
      $content = apply_filters('translate_text', $post_object->post_content, odm_language_manager()->get_current_language());
      echo $content;
    endif;

  endforeach;
?>
