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
  <div class="main_title"><span class="l">文章管理 >> 新增</span>  <a href="article/article_index" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('article/proc_article_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">分类名称:</td>
				<td class="item_input">
					<select name="cat_id">
						<option value="">--请选择--</option>
						<?php 
						foreach($all_cat as $item):
						?>
						<option value='<?php echo $item->level_space == '' ? '' : $item->cat_id;?>'><?php echo $item->level_space.$item->cat_name;?></option>"; 	
						<?php
                        endforeach;
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="item_title">标题:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'title','class'=> 'textbox require','size'=>'120'));?></td>
			</tr>
			<tr>
				<td class="item_title">颜色:</td>
				<td class="item_input" id="color_add">
                <input name="title_color" type="text" class="textbox" id="title_color" onclick="ColorSelecter.Show(this);" value="" />
				<div style="height:15px; width:15px; border:1px solid #000; display:inline-block; margin-left:2px;"; id="title_color_show"></div>
				</td>
			</tr>
			<tr>
			  <td class="item_title">字体样式:</td>
			  <td class="item_input">
			  <select name="title_size" id="title_size">
						<option value="">--请选择--</option>
						<option value='1'>粗体</option>
                        <option value='2'>斜体</option> 	
                        <option value='3'>下划线</option> 	
						<option value='4'>删除线</option> 	
				</select>
			  </td>
		  </tr>
			<tr>
			  <td class="item_title">作者:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'author','class' => 'textbox','value'=>$admin_name)); ?></td>
		  </tr>
			<tr>
			  <td class="item_title">关键字:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'keywords','class' => 'textbox')); ?></td>
		  </tr>
			<tr>
			  <td class="item_title">排序号:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'sort_order','class' => 'textbox')); ?></td>
		  </tr>
			<tr>
			  <td class="item_title">外站文章链接地址:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'url','class' => 'textbox')); ?></td>
		  </tr>
			<tr>
			  <td class="item_title">文章来源:</td>
			  <td class="item_input"><?php print form_input(array('name' => 'source','class' => 'textbox')); ?></td>
		  </tr>
			<tr>
				<td class="item_title">是否使用::</td>
				<td class="item_input">
					<label>禁用<?php print form_radio(array('name'=>'is_use', 'value'=>0,'checked'=>TRUE)); ?></label>
					<label>启用<?php print form_radio(array('name'=>'is_use', 'value'=>1)); ?></label>
				</td>
			</tr>
			<tr>
			  <td class="item_title">内容:</td>
			  <td class="item_input"><?php print $this->ckeditor->editor('content');?></td>
		  </tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'添加'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>