<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/listtable.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/product.js"></script>
<script type="text/javascript" src="public/js/cluetip.js"></script>
<script type="text/javascript" src="public/js/lhgdialog/lhgdialog.js"></script>
<link rel="stylesheet" href="public/style/cluetip.css" type="text/css" media="all" />

<script type="text/javascript">
	//<![CDATA[
	$(function(){
		$('input[type=text][name=start_date_p]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$('input[type=text][name=end_date_p]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:''});
		$('div#upload_dialog').dialog({autoOpen:false,draggable:false,modal:true,width:450});
	});
	function check_form(){
		var validator = new Validator('mainForm');
			validator.required('rush_index', '请填写名称');
			// validator.selected('nav_id', '请选择导航分类');
			validator.required('start_date_p', '请填写开始时间');
			validator.required('end_date_p', '请填写结束时间');
			validator.required('sort_order', '请填写排序');
			return validator.passed();
	}
	function check_upload_form(){
		var validator = new Validator('uploadForm');
			validator.required('image_before_url', '请上传开始前banner');
			validator.required('image_ing_url', '请上传进行中banner');
			return validator.passed();
	}
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

		$('span.img_tip').cluetip({splitTitle: '|',showTitle:false,width:220});
	});


	listTable.filter.rush_id = <?php echo $check->rush_id;?>;
	listTable.filter.percent = 1;
	listTable.url = 'rush/link_search';
	function search(){
		var container = $('form[name=search]');
		listTable.filter['product_sn'] = $.trim($('input[type=text][name=product_sn]', container).val());
		listTable.filter['product_name'] = $.trim($('input[type=text][name=product_name]', container).val());
		listTable.filter['provider_productcode'] = $.trim($('input[type=text][name=provider_productcode]', container).val());
		listTable.filter['provider_code'] = $.trim($('input[type=text][name=provider_code]', container).val());
		listTable.filter['style_id'] = $('select[name=style_id]', container).val();
		listTable.filter['season_id'] = $('select[name=season_id]', container).val();
		listTable.filter['product_sex'] = $('select[name=product_sex]', container).val();
		listTable.filter['category_id'] = $('select[name=category]', container).val();
		listTable.filter['rush_id'] = $.trim($('input[type=hidden][name=rush_id]', container).val());
		listTable.filter['brand'] = $.trim($('select[name=brand]', container).val());
		listTable.filter['percent'] = $.trim($('input[type=text][name=percent]', container).val());
		listTable.filter['batch_code'] = $.trim($('input[type=text][name=batch_code]', container).val());
		listTable.filter['depot_id'] = $('select[name=depot_id]', container).val();
		listTable.loadList();
	}


	function add_rush_product(){
		//if(!confirm('确认添加所选商品！'))return;
		$("#button").attr("disabled",'true').val("正在处理中。。。");
		var rush_id = $(':hidden[name=rush_id]').val();
		var sel_product_checkbox_price = new Array();
		var check_flag =  true;
		$(':input:checked[name="sel_product_checkbox[]"]').each(function(){
			var id = $(this).val();
			var price = $(':input[name=promote_price_'+id+']').val();
			var category_id = $(':input[name=category_id_'+id+']').val();
			if(price == null || price == ''){
			      check_flag= false;
			      alert('请输入商品价格');
			      return;
			}
			sel_product_checkbox_price.push(id+'-'+price+'-'+category_id);
		});
		if(!check_flag)
		    return ;
		if(sel_product_checkbox_price.length <1){
		    alert('请勾选商品');
		    return false;
		}
		//sel_product_checkbox_price.join('|');
		$.ajax({
		   type: "POST",
		   url: "rush/add_rush_product",
		   data: "value="+sel_product_checkbox_price+"&rush_id="+rush_id,
		   dataType: "json",
		   success: function(msg){
			 //if(msg.msg != ''){alert(msg.msg);return false;}
			 if(msg.type == 1){
			 	alert('请正确添加限时抢购商品');
				return false;
			 }
			 location.href = '/rush/edit/'+rush_id+'/1';
		   }
		});
	}

	function remove_link(rec_id){
		if(!confirm('确定移除'))return;
		$.ajax({
		   type: "POST",
		   url: "rush/remove_link",
		   data: "value="+rec_id,
		   dataType: "json",
		   success: function(msg){
			    if(msg.msg != ''){alert('无操作权限');return false;}
			    if(msg.type == 1){alert('记录不存在');return false;}
			    if(msg.type == 2){alert('无法下架商品对应SKU');return false;}
			    $('tr#remove_tr_'+rec_id).remove();
		   }
		});
	}

	function show_upload_dialog(rec_id){
		$('#upload_dialog :hidden[name=rec_id]').val(rec_id);
		$('#upload_dialog').dialog('open');
	}
	
	function check_all () {
	if($(':checkbox[name=ck_check_all]').attr('checked'))
		$(':checkbox[flg="sel_product"]').attr('checked',true);
	else
		$(':checkbox[flg="sel_product"]').attr('checked',false);
	}
        
        function showDialog(rush_id) {
                var content = '<form name="importFrom" action="/rush/importProducts" method="POST" enctype="multipart/form-data">';
                content += '请选择文件：<input type="file" name="data_file" />';
                content += '<input type="hidden" name="rush_id" value="'+rush_id+'" />';
                content += '<input type="submit" class="am-btn am-btn-secondary" value="导入所选文件" />';
                content += '</form>';
                content += '<br>请导入Excel格式的xml文件，且第一个工作表的第一列存放商品款号信息。';
                var dialog = new $.dialog({ id:'thepanel',height:140,width:500,maxBtn:false, title:'设置',iconTitle:false,cover:true,html: content});
                dialog.ShowDialog();
        }
                
	//]]>
</script>
<div class="main">
    <div class="main_title">限时抢购 >> 编辑 <a href="rush/index" class="return r">返回列表</a></div>
	<div class="blank5"></div>
    <div class="button_row">

<ul>
 <li rel="1" class="conf_btn currentbtn"><span>基础信息</span></li>
 <li rel="2" class="conf_btn"><span>抢购商品</span></li>
</ul>
	</div>
    <div class="blank5"></div>
	<?php print form_open_multipart('rush/proc_edit/'.$check->rush_id,'name="mainForm" onsubmit="return check_form();"',array('rush_id'=>$check->rush_id));?>
		<table class="form conf_tab" cellpadding=0 cellspacing=0 rel="1">
			<tr>
				<td colspan=4 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">名称:</td>
				<td class="item_input">
				    <input name="rush_index" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?>  type="text" value="<?php echo $check->rush_index;?>" class="textbox require" />
				    最多只能输入26个字符。<span style="color:red">（温馨提示：1个汉字=2个字符，1个字母=1个字符）</span>
				</td>
			</tr>
			<tr>
			  <td class="item_title">现金券ID:</td>
			  <td class="item_input">
			    <input name="campaign_id" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?>  type="text" value="<?php echo $check->campaign_id;?>" class="textbox" />
			  </td>
			</tr>
		<!-- 		<tr>
			  <td class="item_title">分类:</td>
			  <td class="item_input">
			    <input name="rush_category" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?>  type="text" value="<?php echo $check->rush_category;?>" class="textbox" />
			    最多只能输入20个字符。
			  </td>
			</tr>
			<tr>
			  <td class="item_title">折扣:</td>
			  <td class="item_input">
			    <input name="rush_discount" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?>  type="text" value="<?php echo $check->rush_discount;?>" class="textbox" />
			    最多只能输入4个字符。
			  </td>
			</tr>
			<tr>
			  <td class="item_title">导航分类:</td>
			  <td class="item_input">
			    <?php print form_dropdown('nav_id',array(''=>'请选择导航分类')+get_pair($all_nav,'nav_id','nav_name'),$check->nav_id); ?>
			  </td>
			</tr> -->
			<tr>
				<td class="item_title">开始时间:</td>
				<td class="item_input">
                    <input name="start_date_p" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?> type="text" class="textbox require" value="<?php echo $start_arr[0];?>" />
                    <input name="start_time" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?> type="text" class="textbox require" value="<?php echo $start_arr[1];?>" />
				</td>
			</tr>
			<tr>
				<td class="item_title">结束时间:</td>
				<td class="item_input">
                    <input name="end_date_p" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?> type="text" class="textbox require" value="<?php echo $end_arr[0];?>" />
                    <input name="end_time" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?> type="text" class="textbox require" value="<?php echo $end_arr[1];?>" />
				</td>
			</tr>
			<tr>
				<td class="item_title">限抢banner:</td>
				<td class="item_input">
                    <input name="image_before_url" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?> type="file" class="textbox" />
                    <?php if(!empty($check->image_before_url)):?>
                    <img src="<?php echo  base_url()?>public/data/images/<?php echo $check->image_before_url;?>" width="80px"/>
                    <?php endif;?>请上传指定规格（750*362）图片
                </td>
			</tr>
			<!-- <tr>
				<td class="item_title">限抢logo:</td>
				<td class="item_input">
                    <input name="image_ing_url" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?> type="file" class="textbox" />
                    <?php if(!empty($check->image_ing_url)):?>
                    <img src="<?php echo  base_url()?>public/data/images/<?php echo $check->image_ing_url;?>" width="80px"/>
                    <?php endif;?>请上传指定规格（984*320）图片
				</td>
			</tr> -->
			<tr>
				<td class="item_title">排序:</td>
				<td class="item_input">
					<input name="sort_order" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?> type="text" class="textbox" size="3" value="<?php echo $check->sort_order;?>" />
					同一天开始的排序值高的在前
				</td>
			</tr>
			<tr>
			  <td class="item_title">跳转页面地址:</td>
			  <td class="item_input">
              		<input name="jump_url" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?> value="<?php echo $check->jump_url;?>" type="text" class="textbox" />
              </td>
		  </tr>
			<tr>
			  <td class="item_title">激活:</td>
			  <td class="item_input">
              		<?php if($check->status == 0):?>
                    未激活
                    <?php elseif($check->status == 1):?>
                    激活
					<?php elseif($check->status == 2):?>
			        停止
                    <?php else:?>
                    自动停止
					<?php endif;?>
                     </td>
		  </tr>
		    <!--
			<tr>
				<td class="item_title">简介:</td>
				<td class="item_input">
                <textarea name="desc" <?php echo $perms['rush_edit']? '' : 'disabled="disabled"';?> cols="60" rows="5" class="textbox"><?php echo $check->desc?></textarea>
				</td>
			</tr>
			-->
			<tr>
				<td class="item_title"></td>
				<td class="item_input" colspan=3 height=30>
                	<?php if($perms['rush_edit']):?>
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
                    <?php endif;?>
				</td>
			</tr>
			<tr>
				<td colspan=4 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
    		<div class="conf_tab" rel="2">
			<div id="link_list">
			<?php include('link_list.php');?>
			</div>
			<div class="blank5"></div>
			<div class="search_row">
				<form name="search" action="javascript:search(); ">
				商品款号：<input type="text" class="ts" name="product_sn" value="" style="width:100px;" />
				商品名称：<input type="text" class="ts" name="product_name" value="" style="width:100px;" />
				供应商货号：<input type="text" class="ts" name="provider_productcode" value="" style="width:100px;" />
				供应商编码：<input type="text" class="ts" name="provider_code" value="" style="width:100px;" />
				折扣：<input type="text" class="ts" name="percent" value="1" style="width:100px;"/>
				批次：<input type="text" class="ts" name="batch_code" value="" style="width:100px;" />
                <select name="depot_id">
                <option value="">仓位</option>
                <?php foreach($all_depot as $item):?>
                <option value="<?php echo $item->depot_id?>"><?php echo $item->depot_name;?></option>
                <?php endforeach;?>
                </select>
                <select name="category">
                <option value="">分类名称</option>
                <?php foreach($all_category as $category) print "<option value='{$category->category_id}'>{$category->level_space}{$category->category_name}</option>"?>
                </select>
                <select name="brand">
                <option value="">品牌</option>
                <?php foreach($all_brand as $item):?>
                <option value="<?php echo $item->brand_id?>"><?php echo $item->brand_name;?></option>
                <?php endforeach;?>
                </select>

                <select name="style_id">
                <option value="">风格</option>
                <?php foreach($style_arr as $item):?>
                <option value="<?php echo $item->style_id?>"><?php echo $item->style_name;?></option>
                <?php endforeach;?>
                </select>
                <select name="season_id">
                <option value="">季节</option>
                <?php foreach($season_arr as $item):?>
                <option value="<?php echo $item->season_id?>"><?php echo $item->season_name;?></option>
                <?php endforeach;?>
                </select>
				<select name="product_sex"><option value="">性别</option><option value="1">男款</option><option value="2">女款</option><option value="3">男女款</option></select>
				<input type="submit" class="am-btn am-btn-secondary" value="搜索" />
                                <input type="button" class="am-btn am-btn-primary" value="导入EXCEL商品" onclick="showDialog(<?php print $check->rush_id; ?>);" />
				</form>
			</div>
			<div class="blank5"></div>
			<div id="listDiv">
			</div>
			<div class="blank5"></div>
		</div>
</div>
<!-- 图片上传层开始 -->
<div id="upload_dialog" style="display:none;">
  <?php print form_open_multipart('rush/upload_image',array('name'=>'uploadForm','onsubmit'=>'return check_upload_form()'),array('rec_id'=>0));?>
      <table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
			<tr>
				<td class="item_title">未开始banner</td>
				<td class="item_input">
					<input type="file" name="image_before_url" class="textbox require">
				</td>
			</tr>
			<tr>
				<td class="item_title">已开始banner</td>
				<td class="item_input">
					<input type="file" name="image_ing_url" class="textbox require">
				</td>
			</tr>

			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'uploadsubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
  <?php print form_close(); ?>
</div>
<!-- 图片上传层结束 -->
<?php include(APPPATH.'views/common/footer.php');?>
