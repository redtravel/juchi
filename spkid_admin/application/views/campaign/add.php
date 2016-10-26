<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/brand.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
	$('input[type=text][name=start_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
	$('input[type=text][name=end_time]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});

	});
	$(function(){
		show_flag();
	});
	function check_form(){
		var validator = new Validator('mainForm');
		validator.required('campaign_name', '请填写活动名称');
		validator.required('limit_price', '请填写最小金额');
		validator.selected('tag_id', '请选择赠送商品');
		validator.required('start_time', '请填写开始时间');
		validator.required('end_time', '请填写结束时间');
		return validator.passed();
	}
	
	function sel(te){
		$('select[name=tag_id]')[0].options.length = 1;
		$.ajax({
		   type: "POST",
		   url: "campaign/sel_product",
		   data: "pro="+te,
		   dataType: "JSON",
		   success: function(msg){
			 	if(msg.type == 1){
					for(i in msg.list){
						$('select[name=tag_id]')[0].options.add(new Option(msg.list[i].product_name+' '+msg.list[i].product_sn+' '+msg.list[i].provider_name , msg.list[i].product_id));
					}
				}
			 	if(msg.type == 3){
					alert('没搜索到相关记录');
					return false;
				}
			}
		});
	}

	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">活动管理 >> 新增 </span><a href="campaign/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('campaign/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title" width="100">活动名称:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'campaign_name','class'=> 'textbox require'));?></td>
			</tr>
			<tr>
				<td class="item_title">最小金额:</td>
				<td class="item_input"><?php print form_input(array('name'=> 'limit_price','class'=> 'textbox require'));?></td>
			</tr>
			<?php if($campaign_type == '') {?>
				<tr>
					<td class="item_title">赠送商品:</td>
					<td class="item_input">                
	                <input name="pro" type="text" id="pro" value="" size="20" onblur="return sel(this.value);"  />
					<select name="tag_id" id="tag_id">
					    <option value="">--请选择--</option>
			        </select></td>
				</tr>
				<input type="hidden" name="campaign_type" value="1" />
			<?php }else if($campaign_type == 2 ){ ?>
				<tr>
					<td class="item_title">免邮商品:</td>
					<td class="item_input">                
	                <input name="pro" type="text" id="pro" value="" size="20" onblur="return sel(this.value);"  />
					<select name="tag_id" id="tag_id">
					    <option value="">--请选择--</option>
			        </select></td>
				</tr>
				<input type="hidden" name="campaign_type" value="2" />
			<?php }else if($campaign_type == 3 ){ ?>
				<tr>
					<td class="item_title">抢购商品:</td>
					<td class="item_input">                
	                <input name="pro" type="text" id="pro" value="" size="20" onblur="return sel(this.value);"  />
					<select name="tag_id" id="tag_id">
					    <option value="">--请选择--</option>
			        </select></td>
				</tr>
				<input type="hidden" name="campaign_type" value="3" />
			<?php } ?>
			
			<tr>
				<td class="item_title">开始时间:</td>
				<td class="item_input">
					<input type="text" name="start_time" id="start_time" />
				</td>
			</tr>
			<tr>
			  <td class="item_title">结束时间:</td>
			  <td class="item_input"><input type="text" name="end_time" id="end_time" /></td>
		  </tr>
			<tr>
				<td class="item_title">状态:</td>
				<td class="item_input">
					<label><?php print form_radio(array('name'=>'is_use', 'value'=>0,'checked'=>TRUE)); ?>禁用</label>
                    <label><?php print form_radio(array('name'=>'is_use', 'value'=>1)); ?>启用</label>
					
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
