<?php
  $related_content = json_decode($data,true);
  foreach($related_content as $key => $content):
    $post_id = url_to_postid($content["url"]);
    if ($post_id > 0):
      echo odm_get_thumbnail($post_id,true);
    endif;
  endforeach;
?>
