<?php
  foreach($data as $key => $content):
    $post_id = url_to_postid($content["url"]);
    if ($post_id > 0):
      echo odm_get_thumbnail($post_id,false, array( 300, 'auto'));
    endif;
  endforeach;
?>
