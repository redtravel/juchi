<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>

<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('category_name', '请填写分类名称');
			return validator.passed();
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">商品分类管理 >> 编辑 </span><a href="category/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('category/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('category_id'=>$row->category_id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
                <td class="item_title">
                    商品所属大类
                </td>
                <td class="item_input">
                    <?php print form_dropdown('genre_id', get_pair($all_genre,'id','name'),array($row->genre_id),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
                </td>
            </tr>
			<tr>
				<td class="item_title">分类代号:</td>
				<td class="item_input">
					<?php print form_input('cate_code',$row->cate_code,'class="require textbox" '.($perm_edit?'':'disabled'));?>
					&nbsp;&nbsp;&nbsp;注: 分类代码填写规范 TY<span style="color:red;">xxx</span>,其中 "xxx" 是数字。例: TY001
				</td>
			</tr>
			<tr>
				<td class="item_title">父分类:</td>
				<td class="item_input">
                    <?php print form_dropdown('parent_id', 
                    get_pair($all_category,'category_id','level_space,cate_code,category_name'),array($row->parent_id),'data-am-selected="{searchBox: 1,maxHeight: 300}"');?>
				</td>
			</tr>
			<tr>
				<td class="item_title">分类名称:</td>
				<td class="item_input"><?php print form_input('category_name',$row->category_name,'class="require textbox" '.($perm_edit?'':'disabled'));?></td>
			</tr>
			
			<tr>
				<td class="item_title">排序号:</td>
				<td class="item_input">
					<?php print form_input('sort_order',$row->sort_order,'class="textbox" style="width:40px" '.($perm_edit?'':'disabled')); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
					<label><?php print form_radio('is_use',0,!$row->is_use,$perm_edit?'':'disabled'); ?>禁用</label>
					<label><?php print form_radio('is_use',1,$row->is_use,$perm_edit?'':'disabled'); ?>启用</label>
					
				</td>
			</tr>
			<?php if($perm_edit):?>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<?php endif;?>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>
