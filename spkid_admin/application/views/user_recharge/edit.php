<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('cat_name', '请填写分类名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">编辑</span> <a href="article/cat_index" class="back_list">返回列表</a></div>
  <div class="blank5"></div>
	<?php print form_open_multipart('article/proc_cat_edit/'.$check->cat_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">上级分类:</td>
				<td class="item_input">
					<select name="parent_id">
						<option value="0">顶级分类</option>
						<?php 
						foreach($all_cat as $item):
						?>
						<option <?php echo $item->cat_id == $check->parent_id ? 'selected="selected"' : '';?> value='<?php echo $item->cat_id;?>'><?php echo $item->cat_name;?></option>"; 	
						<?php
                        endforeach;
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="item_title">分类名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'cat_name','class'=> 'textbox require','value' => $check->cat_name));?></td>
			</tr>
			<tr>
				<td class="item_title">关键字:</td>
				<td class="item_input">
					<?php print form_input(array('name' => 'keywords','class' => 'textbox','value' => $check->keywords)); ?>
				</td>
			</tr>
			<tr>
			  <td class="item_title">分类描述:</td>
			  <td class="item_input"><?php print form_textarea(array('name' => 'cat_desc','class' => 'textbox','value' => $check->cat_desc)); ?></td>
		  </tr>
			<tr>
			  <td class="item_title">排序号:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'sort_order','class' => 'textbox','value' => $check->sort_order)); ?></td>
		  </tr>
			<tr>
				<td class="item_title">是否使用::</td>
				<td class="item_input">
					<label>禁用<?php print form_radio(array('name'=>'is_use', 'value'=>0,'checked'=>$check->is_use==0)); ?></label>
					<label>启用<?php print form_radio(array('name'=>'is_use', 'value'=>1,'checked'=>$check->is_use==1)); ?></label>
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