<?php foreach ($log_list as $l): ?>
	<div class="log_item">
	<span class="man"><?php print $l->admin_name ?></span>
	<span class="time">[<?php print $l->create_date; ?>]</span>：
	<?php print $l->content; ?>
	<span style="color:red;">
	<?php print $l->closed?('[已于'.$l->close_date.'关闭]'):('<a href="javascript:void(0)" style="color:red" onclick="close_log('.$l->log_id.')">[点击关闭]</a>')?>
	</span>
	</div>
<?php endforeach ?>
