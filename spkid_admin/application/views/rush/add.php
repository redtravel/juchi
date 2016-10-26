<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('input[type=text][name=start_date_p]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$('input[type=text][name=end_date_p]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('rush_index', '请填写名称');
			// validator.selected('nav_id', '请选择导航分类');
			validator.required('start_date_p', '请填写开始时间');
			validator.required('end_date_p', '请填写结束时间');
			validator.required('sort_order', '请填写排序');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
    <div class="main_title"><span class="l">限时抢购 >> 新增</span>  <a href="rush/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('rush/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=4 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">名称:</td>
				<td class="item_input">
				    <input name="rush_index" type="text" class="textbox require" />
				        最多只能输入26个字符。<span style="color:red">（温馨提示：1个汉字=2个字符，1个字母=1个字符）</span>
				</td>
			</tr>
			 <tr>
			  <td class="item_title">现金券ID:</td>
			  <td class="item_input">
			    <input name="campaign_id" type="text" class="textbox" />
			  </td>
			</tr>
			<!--<tr>
			  <td class="item_title">分类:</td>
			  <td class="item_input">
			    <input name="rush_category" type="text" class="textbox" />
			    最多只能输入20个字符。
			  </td>
			</tr>
			<tr>
			  <td class="item_title">折扣:</td>
			  <td class="item_input">
			    <input name="rush_discount" type="text" class="textbox" />
			    最多只能输入4个字符。
			  </td>
			</tr>
			<tr>
			  <td class="item_title">导航分类:</td>
			  <td class="item_input">
			  	<?php print form_dropdown('nav_id',array(''=>'请选择导航分类')+get_pair($all_nav,'nav_id','nav_name')) ?>
		      </td>
		  </tr> -->
			<tr>
				<td class="item_title">开始时间:</td>
				<td class="item_input">
                    <input name="start_date_p" type="text" class="textbox require" />
                    <input name="start_time" type="text" value="14:00:00" class="textbox require" />
				</td>
			</tr>
			<tr>
				<td class="item_title">结束时间:</td>
				<td class="item_input">
                    <input name="end_date_p" type="text" class="textbox require" />
                    <input name="end_time" type="text" value="23:59:59" class="textbox require" />
				</td>
			</tr>
			<tr>
				<td class="item_title">限抢banner:</td>
				<td class="item_input">
                    <input name="image_before_url" type="file" class="textbox" />请上传指定规格（750*362）图片
				</td>
			</tr>
			<!-- <tr>
				<td class="item_title">限抢logo:</td>
				<td class="item_input">
                    <input name="image_ing_url" type="file" class="textbox" />请上传指定规格（984*320）图片
				</td>
			</tr> -->
			<tr>
				<td class="item_title">排序:</td>
				<td class="item_input">
					<input name="sort_order" type="text" class="textbox" size="3" value="0" />
					同一天开始的排序值高的在前
				</td>
			</tr>
			<tr>
			  <td class="item_title">跳转页面地址:</td>
			  <td class="item_input">
              		<input name="jump_url" type="text" class="textbox" />
              </td>
		  </tr>
		  	<!--
			<tr>
				<td class="item_title">简介:</td>
				<td class="item_input">
					<?php print form_textarea('desc', '',"class='textbox'");?>
				</td>
			</tr>
			-->
			<tr>
				<td class="item_title"></td>
				<td class="item_input" colspan=3>
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=4 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>