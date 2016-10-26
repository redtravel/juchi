<?php include(APPPATH.'views/common/header.php');?>
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>

<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('input[type=text][name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
	});

	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('ad_name', '请填写广告名称');
			validator.required('ad_link', '请填写广告链接');
			return validator.passed();
	}
	
	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">广告管理 >> 新增 </span><a href="front_ad/operate_index/<?php echo $position_id;?>" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('front_ad/proc_ad_add/'.$position_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">广告名称:</td>
				<td class="item_input">
                <input name="ad_name" class="textbox require" id="ad_name" type="text" /></td>
			</tr>
			<tr>
				<td class="item_title">广告链接:</td>
				<td class="item_input"><input name="ad_link" type="text"  class="textbox require" id="ad_link" />链接必须以http://开头</td>
			</tr>
			<tr>
				<td class="item_title">开始时间:</td>
				<td class="item_input"><input name="start_date" type="text"  class="textbox require" id="start_date" /></td>
			</tr>
			<tr>
			  <td class="item_title">结束时间:</td>
			  <td class="item_input"><input name="end_date" type="text"  class="textbox require" id="end_date" /></td>
		  </tr>
			<tr>
			  <td class="item_title">启用：</td>
			  <td class="item_input">
               <input name="is_use" type="radio" value="0" checked="checked" />未启用
			   <input type="radio" name="is_use" value="1" />启用
			       
	          </td>
		  </tr>
			<tr>
			  <td class="item_title">广告内容:</td>
			  <td class="item_input"><?php print $this->ckeditor->editor('ad_code');?></td>
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