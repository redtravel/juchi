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
  <div class="main_title"><span class="l">文章分类管理 >> 编辑</span>  <a href="article/cat_index" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('article/proc_cat_edit/'.$check->cat_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">上级分类:</td>
				<td class="item_input">
					<select name="parent_id" <?php echo $perms['art_cat_edit'] == 1 ? '' : 'disabled="disabled"';?>>
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
				<td class="item_input">
                <input <?php echo $perms['art_cat_edit'] == 1 ? '' : 'disabled="disabled"';?> type="text" class="textbox require" value="<?php echo $check->cat_name?>" name="cat_name" />
                </td>
			</tr>
			<tr>
				<td class="item_title">关键字:</td>
				<td class="item_input">
                <input <?php echo $perms['art_cat_edit'] == 1 ? '' : 'disabled="disabled"';?> type="text" class="textbox" name="keywords" value="<?php $check->keywords?>" />
					
				</td>
			</tr>
			<tr>
			  <td class="item_title">分类描述:</td>
			  <td class="item_input">
			        <textarea <?php echo $perms['art_cat_edit'] == 1 ? '' : 'disabled="disabled"';?> name="cat_desc" id="cat_desc" cols="45" class="textbox" rows="5"><?php echo $check->cat_desc?></textarea>
				</td>
		  </tr>
			<tr>
			  <td class="item_title">排序号:</td>
			  <td class="item_input">
			  <input <?php echo $perms['art_cat_edit'] == 1 ? '' : 'disabled="disabled"';?> type="text" class="textbox require" value="<?php echo $check->sort_order?>" name="sort_order" />
			  </td>
		  </tr>
			<tr>
				<td class="item_title">是否使用::</td>
				<td class="item_input">
                
					
					      <input <?php echo $perms['art_cat_edit'] == 1 ? '' : 'disabled="disabled"';?> type="radio" name="is_use" value="0" <?php echo $check->is_use==0 ? 'checked="checked"' : '';?>  />
					      禁用
					      <input <?php echo $perms['art_cat_edit'] == 1 ? '' : 'disabled="disabled"';?> type="radio" name="is_use" value="1" <?php echo $check->is_use==1 ? 'checked="checked"' : '';?>  />
					      启用
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
                <?php if($perms['art_cat_edit'] == 1):?>
				<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'编辑'));?>
                <?php endif;?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>