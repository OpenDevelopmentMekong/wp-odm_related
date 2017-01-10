<ul>
  <?php
    foreach($data as $key => $content):
      $content_url = !empty($content["url"][odm_language_manager()->get_current_language()]) ? $content["url"][odm_language_manager()->get_current_language()] : $content["url"]["en"];
			
      $content_label = !empty($content["label"][odm_language_manager()->get_current_language()]) ? $content["label"][odm_language_manager()->get_current_language()] : $content["label"]["en"];
    ?>
    <li><a href="<?php echo $content_url; ?>"><?php echo $content_label; ?></a></li>
  <?php endforeach; ?>
</ul>
