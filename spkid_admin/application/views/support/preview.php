<?php foreach ($history as $m): ?>
	<div class="message_item <?php print $m->qora?'a':'q'?>" rel="<?php print $m->message_id ?>">
	<span class="man"><?php print $m->admin_id?$m->admin_name:($m->user_name?$m->user_name:'访客') ?></span>
	<span class="time">[<?php print substr($m->create_date,11); ?>]</span>：
	<?php print $m->content; ?>
	</div>
<?php endforeach ?>
<?php if(!$history) print '没有记录';?>