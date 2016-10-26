<?php include(APPPATH.'views/common/header.php');?>
<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />
	<script type="text/javascript" src="../../../public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>

<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript">
	//<![CDATA[
        $(function(){
		var btns = $(".conf_btn");
		var tabs = $(".conf_tab");
		$.each(btns, function(i, btn){
			$(btn).bind("click",function(){
				$(tabs).hide();
				$(tabs[i]).show();
				$(btns).removeClass("currentbtn");
				$(this).addClass("currentbtn");
			});
			$(btn).bind("focus",function(){$(this).blur();});
		});
		i=0;
        var i = Utils.request(location.href,'tab');
        if(!i) i = 0;
		$(btns[i]).click();
		$('span.img_tip').cluetip({splitTitle: '|',showTitle:false});

	});
	$(function(){
		$('input[type=text][name=start_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
		$('input[type=text][name=end_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
	});

	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('ad_name', '请填写广告名称');
			// validator.required('ad_link', '请填写广告链接');
			return validator.passed();
	}
	
	//]]>
</script>
<div class="main">
        <div class="main_title"><span class="l">广告管理 >> 编辑 </span><a href="<?php echo $type == 2 ? 'front_ad/ad_index' : 'front_ad/operate_index/'.$arr->position_id;?>" class="return r">返回列表</a></div>

  <div class="blank5"></div>
  <?php if($type == 2):?>
	<?php print form_open_multipart('front_ad/proc_ad_edit/'.$arr->ad_id.'/'.$arr->position_id.'/2',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
    <?php else:?>
    <?php print form_open_multipart('front_ad/proc_ad_edit/'.$arr->ad_id.'/'.$arr->position_id.'/1',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
    <?php endif;?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
			  <td class="item_title">广告位置名称：</td>
			  <td class="item_input">               <select <?php echo $perms['front_ad_edit'] == 1 ? '' : 'disabled="disabled"';?> name="position_id" id="position_id">
				  <option value="">--请选择--</option>
                  <?php
                  foreach($p_arr as $item):
				  ?>
			      <option <?php echo $item->position_id == $arr->position_id ? 'selected="selected"' : '';?>  value="<?php echo $item->position_id?>"><?php echo $item->position_name?></option>
		      	  <?php
                  endforeach;
				  ?>
			    </select></td>
		  </tr>
			<tr>
				<td class="item_title">广告名称:</td>
				<td class="item_input">
                <input name="ad_name" <?php echo $perms['front_ad_edit'] == 1 ? '' : 'disabled="disabled"';?> class="textbox require" id="ad_name" value="<?php echo $arr->ad_name?>" type="text" /></td>
			</tr>
			<tr>
				<td class="item_title">广告链接:</td>
				<td class="item_input"><input name="ad_link" <?php echo $perms['front_ad_edit'] == 1 ? '' : 'disabled="disabled"';?> type="text" value="<?php echo $arr->ad_link?>"  class="textbox" id="ad_link" />链接必须以http://开头</td>
			</tr>
			<tr>
				<td class="item_title">开始时间:</td>
				<td class="item_input"><input name="start_date" <?php echo $perms['front_ad_edit'] == 1 ? '' : 'disabled="disabled"';?> value="<?php echo $arr->start_date?>" type="text"  class="textbox require" id="start_date" /></td>
			</tr>
			<tr>
			  <td class="item_title">结束时间:</td>
			  <td class="item_input"><input name="end_date" type="text" <?php echo $perms['front_ad_edit'] == 1 ? '' : 'disabled="disabled"';?>  value="<?php echo $arr->end_date?>" class="textbox require" id="end_date" /></td>
		  </tr>
			<tr>
			  <td class="item_title">启用：</td>
			  <td class="item_input">
               <input <?php echo $perms['front_ad_edit'] == 1 ? '' : 'disabled="disabled"';?> name="is_use" type="radio" value="0" <?php echo $arr->is_use == 0 ? 'checked="checked"' : '';?>  />未启用
			   <input <?php echo $perms['front_ad_edit'] == 1 ? '' : 'disabled="disabled"';?> type="radio" name="is_use" value="1" <?php echo $arr->is_use == 1 ? 'checked="checked"' : '';?> />启用
			       
	          </td>
		  </tr>
			<tr>
			  <td class="item_title">广告内容:</td>
			  <td class="item_input"><?php print $this->ckeditor->editor('ad_code',$arr->ad_code);?></td>
		  </tr>
                  <tr>
				<td class="item_title">链接图片:</td>
				<td class="item_input">
					<?php print form_upload('pic_url','','class="textbox" '.($perms['front_ad_edit'] == 1?'':'disabled'));?>
					<?php print img_tip(PUBLIC_DATA_IMAGES, $arr->pic_url); ?>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
                <?php if($perms['front_ad_edit'] == 1):?>
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