<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('rank_name', '请填写等级名称');
			validator.isInt('min_points', '请填写最少积分',true);
			validator.isInt('max_points', '请填写最多积分',true);
			validator.isInt('regist_point', '请填写注册送的积分',true);
			validator.isNumber('buying_point_rate', '请填写购买商品积分倍数',true);
			validator.isInt('comment_point', '请填写评论送积分数',true);
			validator.isInt('profile_point', '请填写完善信息送积分数',true);
			validator.isInt('invite_point', '请填写邀请送积分数',true);
			validator.isInt('friendby_point', '请填写被邀请人购买首次下单送积分数',true);
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">等级管理 >> 添加 </span><a href="userrank/index" class="return r">返回列表</a></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('userrank/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td width="30%" class="item_title">等级名称:</td>
				<td width="70%" class="item_input"><?php print form_input(array('name'=> 'rank_name','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">最少积分:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'min_points','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">最多积分:</td>
				<td class="item_input">
					<?php print form_input(array('name'=> 'max_points','class'=> 'textbox require'));?>
				</td>
			</tr>
			<tr>
			  <td class="item_title">注册送的积分:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'regist_point','class' => 'textbox require')); ?></td>
		  </tr>
			<tr>
			  <td class="item_title">购买商品积分倍数:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'buying_point_rate','class' => 'textbox require','value'=>'1')); ?></td>
		  </tr>
			<tr>
			  <td class="item_title">评论送积分数:</td>
			  <td class="item_input"><?php print form_input(array('name'=> 'comment_point','class'=> 'textbox require'));?></td>
		  </tr>
			<tr>
			  <td class="item_title">完善信息送积分数:</td>
			  <td class="item_input"><?php print form_input(array('name'=> 'profile_point','class'=> 'textbox require'));?></td>
		  </tr>
			<tr>
			  <td class="item_title">邀请送积分数:</td>
			  <td class="item_input"><?php print form_input(array('name'=> 'invite_point','class'=> 'textbox require'));?></td>
		  </tr>
			<tr>
			  <td class="item_title">被邀请人购买首次下单送积分数:</td>
			  <td class="item_input"><?php print form_input(array('name'=> 'friendby_point','class'=> 'textbox require'));?></td>
		  </tr>

			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>