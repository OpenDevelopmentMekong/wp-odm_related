<?php
  foreach($data as $key => $content):
    $post_id = url_to_postid($content["url"]);

    if ($post_id > 0):
      $post_object = get_post( $post_id );
      echo $post_object->post_content;
    endif;

  endforeach;
?>
