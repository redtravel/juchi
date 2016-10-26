<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
<script type="text/javascript" src="public/js/depot.js"></script>
<script type="text/javascript">
	//<![CDATA[
	$(function(){
                $(document).ready(function()
                {
                    if ($(":input[name=warn_type][checked]").val() == "1")
                    {
                        $("#wt01").hide();
                        $("#wt02").hide();
                        $("#wt03").show();
                    }
                    else if ($(":input[name=warn_type][checked]").val() == "2")
                    {
                        $("#wt01").show();
                        $("#wt02").show();
                        $("#wt03").hide();
                    }
                });
                
                $(":input[type=radio][name=warn_type]").click(function(event){
                       var type = $(event.target).val();
                       if(type == "1"){
                           $("#wt01").hide();
                           $("#wt02").hide();
                           $("#wt03").show();
                       }else if (type == "2") {
                           $("#wt01").show();
                           $("#wt02").show();
                           $("#wt03").hide();
                       }

                 });  
           });
        
        function check_form(){
		var validator = new Validator('mainForm');
                        validator.requiredRadio('warn_type', '请选择预警类型');
                        if ($(':input[name=warn_type][checked]').val() == '1')
                        {
                            validator.required('product_sn', '请输入款号');
                        }
                        else if ($(':input[name=warn_type][checked]').val() == '2')
                        {
                            validator.selected('purchase_batch', '请选择批次号');
                        }
			validator.required('min_number', '请输入最小预警库存数');
                        
			return validator.passed();
	}
        
	function get_provider_batch(dom) {
		$("#purchase_batch option").remove();
                var url = '/inventory_query/get_inventory_batch/'+dom.value;
		$.get(url,function(result){
			$.each($.parseJSON(result), function(key, value) {
		        var htmlStr = '<option value="'+key+'">'+value+'</option>';
		        $("#purchase_batch").append(htmlStr);
		    });
		});
	}
	//]]>
</script>
<div class="main">
	<div class="main_title"><span class="l">仓库管理 >> 新增库存预警条目</span> <span class="r">[ <a href="/inventory_warning/view_warning_list">返回列表 </a>]</span></div>
	<div class="blank5"></div>
	<?php print form_open('/inventory_warning/proc_add',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
                        <tr>
				<td class="item_title" width="150px">预警类型:</td>
				<td class="item_input">
					<label><input name="warn_type" type="radio" value="1"/>按商品款号</label>
                                        <label><input name="warn_type" type="radio" value="2"/>按指定批次</label>
				</td>
			</tr>
			<tr id="wt01" style="display: none">
				<td class="item_title">供应商:</td>
				<td class="item_input">
					<select id="purchase_provider" name="purchase_provider" onchange="get_provider_batch(this);" >
						<?php foreach ($provider_list as $key => $val): ?>
                                                    <option value="<?php print $key; ?>"><?php print $val; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr id="wt02" style="display: none">
				<td class="item_title">批次号:</td>
				<td class="item_input">
					<select id="purchase_batch" name="purchase_batch">
                                            <?php foreach ($batch_list as $key => $val): ?>
                                                <option value="<?php print $key; ?>"><?php print $val; ?></option>
                                            <?php endforeach; ?>
                                        </select>
				</td>
			</tr>
			<tr id="wt03" style="display: none">
				<td class="item_title">款号:</td>
				<td class="item_input">
                                    <input type="text" id="product_sn" name="product_sn" class="textbox require" style="width:180px;">
				</td>
			</tr>
                        <tr id="wt03">
				<td class="item_title">最小预警库存数:</td>
				<td class="item_input">
                                    <input type="text" id="min_number" name="min_number" class="textbox require" style="width:180px;">
				</td>
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