<table width=100%  border="0">
	<tr>
		<td width="380">
			<div class="message_list">
			<?php foreach ($history_list as $m): ?>
				<div class="message_item <?php print $m->qora?'a':'q'?>" rel="<?php print $m->message_id ?>">
				<span class="man"><?php print $m->admin_id?$m->admin_name:($m->user_name?$m->user_name:'访客') ?></span>
				<span class="time">[<?php print substr($m->create_date,11); ?>]</span>：
				<?php print $m->content; ?>
				</div>
			<?php endforeach ?>
			</div>
		</td>
		<td width="380">
			<div class="history">
			</div>
		</td>
		<td>
			<div class="log">
				<?php include APPPATH.'views/support/log.php'?>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<textarea name="message_box" class="message_box" rows="3"></textarea><br/>						
			<input name="btn_sender" class="btn_sender" type="button" class="am-btn am-btn-secondary" value="发送"/>
			<input name="btn_assign" class="btn_assign" type="button" class="am-btn am-btn-secondary" value="转出"/>
			<input name="btn_close" class="btn_close" type="button" class="am-btn am-btn-secondary" value="关闭"/>
			<input name="btn_log" class="btn_log" type="button" class="am-btn am-btn-secondary" value="备注"/>
		</td>
		<td align="center"><span class="btn_history">查看历史记录</span><div class="page"></div></td>
		<td></td>
	</tr>
</table>