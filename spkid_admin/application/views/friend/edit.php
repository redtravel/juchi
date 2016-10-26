<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('link_name', '请填写友情链接名称');
			validator.required('link_url', '请填写友情链接地址');
			validator.isInt('sort_order', '请正确填写排序号');
			return validator.passed();
	}

	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">友情链接管理 >> 编辑</span> <span class="r"><a href="friend/index" class="return r">返回列表</a></span></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('friend/proc_edit/'.$link->link_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">友情链接名称:</td>
				<td class="item_input">
                <input name="link_name" class="textbox require" id="link_name" value="<?php echo $link->link_name?>" /></td>
			</tr>
			<tr>
				<td class="item_title">友情链接地址:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'link_url','class'=> 'textbox require','value' => $link->link_url));?></td>
			</tr>
			<tr>
			  <td class="item_title">logo:</td>
			  <td class="item_input">
			    <input name="logo" type="file" />
                <?php if(!empty($link->link_logo)):?><img height="20" width="20" src="<?php print PUBLIC_DATA_IMAGES . $link->link_logo; ?>" /><?php endif;?>
              </td>
		  </tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input"><input name="sort_order" class="textbox require" id="sort_order" value="<?php echo $link->sort_order?>" /></td>
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