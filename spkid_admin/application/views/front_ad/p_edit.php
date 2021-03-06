<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('position_tag', '请填写广告TAG名称');
			validator.required('position_name', '请填写广告位置名称');
			validator.required('page_name', '请填写所在页面名称');
			validator.isNumber('ad_width', '请填写宽度' , true);
			validator.isNumber('ad_height', '请填写高度' , true);
			return validator.passed();
	}
	
	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">广告位置管理 >> 编辑 </span><a href="front_ad/index" class="return r">返回列表</a></div>

  <div class="blank5"></div>
	<?php print form_open_multipart('front_ad/proc_p_edit/'.$arr->position_id,array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
			  <td class="item_title">广告位置TAG:</td>
			  <td class="item_input"><input name="position_tag" <?php echo $perms['front_ad_po_edit'] == 1 ? '' : 'disabled="disabled"';?>  value="<?php echo $arr->position_tag?>" class="textbox require" id="position_tag" /></td>
		  </tr>
			<tr>
				<td class="item_title">广告位置名称:</td>
				<td class="item_input">
                <input name="position_name" <?php echo $perms['front_ad_po_edit'] == 1 ? '' : 'disabled="disabled"';?>  value="<?php echo $arr->position_name?>" class="textbox require" id="position_name" /></td>
			</tr>
			<tr>
				<td class="item_title">所在页面名称:</td>
				<td class="item_input"><input name="page_name" <?php echo $perms['front_ad_po_edit'] == 1 ? '' : 'disabled="disabled"';?> value="<?php echo $arr->page_name?>" class="textbox require" id="page_name" /></td>
			</tr>
			<tr>
			  <td class="item_title">品牌:</td>
			  <td class="item_input">
			    <select name="brand_id" id="brand_id" <?php echo $perms['front_ad_po_edit'] == 1 ? '' : 'disabled="disabled"';?>>
			      <option value="">--请选择--</option>
                  <?php
                  foreach($all_brand as $item):
				  ?>
			      <option <?php echo $item->brand_id == $arr->brand_id ? 'selected="selected"' : ''?>  value="<?php echo $item->brand_id?>"><?php echo $item->brand_name?></option>
		      	  <?php
                  endforeach;
				  ?>
                </select>
		      
              </td>
		  </tr>
			<tr>
				<td class="item_title">分类:</td>
				<td class="item_input"><select name="category_id" <?php echo $perms['front_ad_po_edit'] == 1 ? '' : 'disabled="disabled"';?> id="category_id">
				  <option value="">--请选择--</option>
                  <?php
                  foreach($all_category as $item):
				  ?>
			      <option <?php echo $item->category_id == $arr->category_id ? 'selected="selected"' : ''?> value="<?php echo $item->category_id?>"><?php echo $item->category_name?></option>
		      	  <?php
                  endforeach;
				  ?>
			    </select></td>
			</tr>
			<tr>
			  <td class="item_title">宽度:</td>
			  <td class="item_input"><input <?php echo $perms['front_ad_po_edit'] == 1 ? '' : 'disabled="disabled"';?> name="ad_width" value="<?php echo $arr->ad_width?>" class="textbox require" id="ad_width" /></td>
		  </tr>
			<tr>
			  <td class="item_title">高度:</td>
			  <td class="item_input"><input <?php echo $perms['front_ad_po_edit'] == 1 ? '' : 'disabled="disabled"';?> name="ad_height" value="<?php echo $arr->ad_height?>" class="textbox require" id="ad_height" /></td>
		  </tr>
			<tr>
			  <td class="item_title">样式:</td>
			  <td class="item_input"><textarea <?php echo $perms['front_ad_po_edit'] == 1 ? '' : 'disabled="disabled"';?> name="position_style" cols="50" rows="5" class="textbox require" id="position_style"><?php echo $arr->position_style?></textarea></td>
		  </tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
                	<?php if($perms['front_ad_po_edit'] == 1):?>
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
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