<h2 class="bg_x">最新评论</h2>
<div class="other_b">
  <?php foreach ($liuyan_list as $liuyan): ?>
   <dl>
     <dt><a href="product-<?php print $liuyan->tag_id ?>.html" target="_blank"><img src="<?php print img_url( $liuyan->img_40_53); ?>" width="40" height="53" /></a></dt>
     <dd class="b"><?php print mask_str($liuyan->admin_user_name?$liuyan->admin_user_name:$liuyan->user_name,3,0,'***') ?></dd>
     <dd><a href="product-<?php print $liuyan->tag_id ?>.html" target="_blank"><?php print mask_str($liuyan->comment_content,20,0,'...') ?></a></dd>
   </dl>
   <?php endforeach ?>
</div>       