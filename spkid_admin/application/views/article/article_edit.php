<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/colorselector.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.selected('cat_id', '请正确选择分类');
			validator.required('title', '请填写文章标题');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
  <div class="main_title"><span class="l">文章管理 >> 编辑</span>  <a href="article/article_index" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('article/proc_article_edit/'.$check->article_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">分类名称:</td>
				<td class="item_input">
					<select name="cat_id" <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?>>
						<option value="">--请选择--</option>
						<?php 
						foreach($all_cat as $item):
						?>
						<option <?php echo $check->cat_id == $item->cat_id ? 'selected="selected"' : '';?>  value='<?php echo $item->level_space == '' ? '' : $item->cat_id;?>'><?php echo $item->level_space.$item->cat_name;?></option>"; 	
						<?php
                        endforeach;
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="item_title">标题:</td>
				<td class="item_input">
				<input type="text" <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?> value="<?php echo $check->title?>" name="title" class="textbox require" size="120" />
				</td>
			</tr>
			<tr>
				<td class="item_title">颜色:</td>
				<td class="item_input" id="color_add">
                <input <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?> name="title_color" type="text" class="textbox" id="title_color" onclick="ColorSelecter.Show(this);" value="<?php echo $check->title_color?>" />
				<div style="height:15px; width:15px; border:1px solid #000; display:inline-block; margin-left:2px; <?php print $check->title_color?"background-color:{$check->title_color};":''?>" id="title_color_show"></div>
				</td>
			</tr>
			<tr>
			  <td class="item_title">字体大小:</td>
			  <td class="item_input">
			  <select name="title_size" id="title_size" <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?>>
						<option value="">--请选择--</option>
						<option <?php echo $check->title_size == 1 ? 'selected="selected"' : '';?> value='1'>粗体</option>
                        <option <?php echo $check->title_size == 2 ? 'selected="selected"' : '';?> value='2'>斜体</option> 	
                        <option <?php echo $check->title_size == 3 ? 'selected="selected"' : '';?> value='3'>下划线</option> 	
						<option <?php echo $check->title_size == 4 ? 'selected="selected"' : '';?> value='4'>删除线</option> 	

				</select>
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">作者:</td>
			  <td class="item_input">
			  <input type="text" <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?> value="<?php echo $check->author?>" name="author" class="textbox" />
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">关键字:</td>
			  <td class="item_input">
			  <input type="text" <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?> value="<?php echo $check->keywords?>" name="keywords" class="textbox" />
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">排序号:</td>
			  <td class="item_input">
			  <input type="text" <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?> value="<?php echo $check->sort_order?>" name="sort_order" class="textbox" />
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">外站文章链接地址:</td>
			  <td class="item_input">
			  <input type="text" <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?> value="<?php echo $check->url?>" name="url" class="textbox" />
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">文章来源:</td>
			  <td class="item_input">
			  <input type="text" <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?> value="<?php echo $check->source?>" name="source" class="textbox" />
			  </td>
		  </tr>
			<tr>
				<td class="item_title">是否使用::</td>
				<td class="item_input">
                
                	<input <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?> type="radio" name="is_use" value="0" <?php echo $check->is_use==0 ? 'checked="checked"' : '';?>  />
					      禁用
					      <input <?php echo $perms['art_edit'] == 1 ? '' : 'disabled="disabled"';?> type="radio" name="is_use" value="1" <?php echo $check->is_use==1 ? 'checked="checked"' : '';?>  />
					      启用
				</td>
			</tr>
			<tr>
			  <td class="item_title">内容:</td>
			  <td class="item_input"><?php print $this->ckeditor->editor('content',$check->content);?></td>
		  </tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
                <?php if($perms['art_edit'] == 1):?>
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