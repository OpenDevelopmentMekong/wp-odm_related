<?php
  $related_content = json_decode($data,true);
  foreach($related_content as $key => $content):
    $post_id = url_to_postid($content["url"]);
    if ($post_id > 0):
      $post = get_post($post_id);
      odm_get_template("post-grid-single-1-cols",array(
							"post" => $post,
							"show_meta" => true,
							"show_excerpt" => false,
							"show_thumbnail" => true
						),true);
    endif;
  endforeach;
?>
