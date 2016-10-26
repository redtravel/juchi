<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>
<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
<script type="text/javascript">
	$(function(){
		$('span.img_tip').cluetip({splitTitle: '|',showTitle:false});
	});
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('nav_name', '请正确填写导航名称');
		if($(':checkbox[name="category_id[]"]:checked').length<1){
			validator.required('nav_url', '请正确填写导航链接');
		}
		if($(':checkbox[name="category_id[]"]:checked').length>1){
			validator.addErrorMsg('商品分类最多只能选择一个');
		}
		return validator.passed();
	}
</script>
<div class="main">
    <div class="main_title"><span class="l"><span class="l">导航管理 >> 编辑</span></span> <span class="r"><a href="frontnav/index" class="return r">返回列表</a></span></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('frontnav/proc_edit/',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('nav_id'=>$nav->nav_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">导航名称:</td>
				<td class="item_input">
				<input name="nav_name" class="textbox require" type="text" id="nav_name" value="<?php print $nav->nav_name ?>" />
				</td>
			</tr>
			<tr>
			  <td class="item_title">商品分类</td>
			  <td class="item_input">
			  <?php foreach ($first_types as $cat): ?>
			  	<label>
			  		<?php print form_checkbox('category_id[]',$cat->type_id,in_array($cat->type_id,$nav->category_ids));?>
			  		<?php print $cat->type_name ?>
			  	</label>
			  <?php endforeach ?>
			  
              </td>
		  	</tr>
		  	<tr>
			  <td class="item_title">导航链接:</td>
			  <td class="item_input">
			  <input name="nav_url" class="textbox" id="nav_url" value="<?php print $nav->nav_url; ?>" />
			  未选择商品分类时，链接地址为必填项。前台网址请用 [front] 代替。
			  </td>
		  </tr>
			<tr>
				<td class="item_title">广告链接:</td>
				<td class="item_input">
				<input name="nav_ad_url" class="textbox" id="nav_ad_url" value="<?php print $nav->nav_ad_url; ?>" />
				前台网址请用 [front] 代替。
				</td>
			</tr>
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input"><input name="sort_order" class="textbox" id="sort_order" value="<?php print $nav->sort_order; ?>" /></td>
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