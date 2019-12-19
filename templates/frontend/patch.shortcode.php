<section
  class="cablespatch" id="cablespatch_<?php echo $context['patch']->getId(); ?>"
  style="white-space: normal; width: <?php echo $context['style']['width']; ?>;<?php if($context['style']['height']):?> height: <?php echo $context['style']['height']; ?>;<?php endif;?><?php echo " " . $context['style']['containerStyle']; ?>">
  <?php include(__DIR__ . '/../_partials/patch.php'); ?>
</section>


