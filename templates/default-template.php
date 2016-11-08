<ul>
  <?php
    $related_content = json_decode($data,true);
    foreach($related_content as $key => $content):
      $content_url = $content["url"];
      $content_label = !empty($content["label"][odm_language_manager()->get_current_language()]) ? $content["label"][odm_language_manager()->get_current_language()] : $content["label"]["en"];
    ?>
    <li><a href="<?php echo $content_url; ?>"><?php echo $content_label; ?></li>
  <?php endforeach; ?>
</ul>
