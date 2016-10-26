<?php include(APPPATH.'views/common/header.php'); ?>
<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
<script type="text/javascript" src="/public/js/listtable.js"></script>

<script type="text/javascript" src="/public/js/support.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>

<style type="text/css">
	#issue_list{border:solid 1px #ccc; width:100%; height:120px; margin-bottom:10px;
		/*-webkit-border-radius: 5px; 
		-moz-border-radius: 5px; 
		border-radius: 5px; */
		background-color:#EEE;
		overflow-y:scroll;
	}
	.issue{float:left;margin:3px 5px; 
		border:solid 1px #ccc; padding:0 5px; cursor:pointer;
		background-color:#FFF;
	}
	.message_list{
		height:300px;
		float:left;
		border:solid 1px #ccc;background-color:white;
		margin-bottom:5px;padding:5px;
		word-wrap:break-word;
		width:380px;
		overflow:scroll;
	}
	.history{
		height:300px;
		float:left;
		border:solid 1px #ccc;background-color:white;
		margin-bottom:5px;padding:5px;
		word-wrap:break-word;
		width:380px;
		margin-left:10px;
		overflow:scroll;
	}
	.log{
		height:300px;
		float:left;
		margin-bottom:5px;padding:5px;
		word-wrap:break-word;
		width:300px;
		margin-left:10px;
	}
	
	.sender{width:100%;height:50px;clear:both;}
	.ui-tabs-panel{overflow:scroll;}
	.message_box{width:380px;height:55px;resize:none;}
	.log_box{width:260px;height:50px;resize:none;}
	
	.btn_history{cursor:pointer;line-height:60px; }
	.a .man{color:#FF6600;}
	.q .man{color:#1C94C4;}
	.page{
		line-height:60px;
		width:300px;
	}
</style>
<script type="text/javascript">
	
	var last_message_id=<?php print $last_message_id; ?>;
	var blocking=0;//当前阻塞的ID
	var blocking_content='';
	var admin_id=<?php print $this->admin_id; ?>;
</script>


<div class="main">
	<div class="main_title"><span class="l">在线客服(请双击接听会话)</span><span class="r"><a href="support/issue_list" class="return r" target="_blank">查看列表</a></span></div>
	<div class="blank5"></div>
	<div id="issue_list">
		<?php foreach ($issue_list as $issue): if($issue->status) continue; ?>
			<div class="issue" id="issue-<?php print $issue->rec_id; ?>" rel="support/preview/<?php print $issue->rec_id; ?>">
				<?php print $issue->user_name?"用户-{$issue->user_name}":"访客-{$issue->rec_id}" ?>【<?php print $issue->admin_name?>】
			</div>
		<?php endforeach ?>
	</div>
	<div id="tabs">
		<ul>
			<?php foreach ($issue_list as $issue): if(!$issue->status) continue; ?>
			<li>
				<?php
					print '<a href="javascript:void();" title="tabs-'.$issue->rec_id.'">';
					if (!empty($history[$issue->rec_id]) && $last_msg=end($history[$issue->rec_id])){
						if(!$last_msg->admin_id) print '【新消息】';
					}
					print $issue->user_name?"用户-{$issue->user_name}":"访客-{$issue->rec_id}";
					print $issue->user_close?'【离线】':'';
					print '</a>';
				?>
			<?php endforeach ?>
			</li>
		</ul>
		<?php foreach ($issue_list as $issue):if(!$issue->status) continue; ?>
		<?php
		$history_list=empty($history[$issue->rec_id])?array():$history[$issue->rec_id];
		$log_list=empty($log[$issue->rec_id])?array():$log[$issue->rec_id];
		?>
		<div id="tabs-<?php print $issue->rec_id; ?>">	
			<?php include APPPATH.'views/support/issue.php'?>
			
		</div>
		<?php endforeach ?>
	</div>
</div>
<div id="float_log" style="display:none;">
	<input type="hidden" name="log_issue_id" value="0" />
	<textarea name="log_box" class="log_box"></textarea><br/>
	<input type="button" name="btn_logsender" value="提交" onclick="log_issue(0)" />
</div>
<audio id="sound" src="public/images/song.ogg"></audio>
<?php include_once(APPPATH.'views/common/footer.php'); ?>