<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('focus_name', '请填写名称');
			validator.required('focus_url', '请填写url');
			validator.required('focus_image', '请选择图片');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title">
        <span class="l">首页焦点图>> 新增</span>
        <a href="front_focus_image/index/<?php echo $search_type?>" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('front_focus_image/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">类别:</td>
				<td class="item_input">
                    <select name="focus_type">
                        <?foreach($focus_type as $key=>$val):?>
<?php if ($key == $search_type):?>
<option value="<?=$key?>" selected="selected"><?=$val?></option>
<?php else:?>
                            <option value="<?=$key?>"><?=$val?></option>
<?php endif;?>
                        <?endforeach?>                        
                    </select>
                </td>
			</tr>
			<tr>
				<td class="item_title">名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'focus_name',
                            'class'=> 'textbox require'));?>
                </td>
			</tr>
			<tr>
				<td class="item_title">链接:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'focus_url',
                            'class'=> 'textbox require'));?>链接必须以http://开头
                </td>
				
            </tr>
            <tr>
				<td class="item_title">小图:</td>
				<td class="item_input">
                    <input type="file" name="small_image">
                </td>
			</tr>
            <tr>
				<td class="item_title">图片:</td>
				<td class="item_input">
                    <input type="file" name="focus_image">
                </td>
            </tr>
            <tr>
				<td class="item_title">开始时间:</td>
				<td class="item_input">
                    <input type="text" name="start_time" class="am-form-field" placeholder="开始时间" data-am-datepicker readonly/>
                </td>
            </tr>
            <tr>
<td class="item_title">结束时间:</td>
<td class="item_input">
                    <input type="text" name="end_time" class="am-form-field" placeholder="结束时间" data-am-datepicker readonly/>
                </td>
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
