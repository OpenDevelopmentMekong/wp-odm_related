<?php
  $index = 1;
  foreach($data as $key => $content):
    $post_id = url_to_postid($content["url"]);
    if ($post_id > 0):
      if (should_open_row("post-grid-single-1-cols",$index)): ?>
				<div class="row">
			<?php endif;
      $post = get_post($post_id);
      odm_get_template("post-grid-single-1-cols",array(
							"post" => $post,
							"show_meta" => true,
							"show_excerpt" => false,
							"show_thumbnail" => true
						),true);
      if (should_close_row("post-grid-single-1-cols",$index)): ?>
				</div>
			<?php endif;
			$index++;
    endif;
  endforeach;
?>
