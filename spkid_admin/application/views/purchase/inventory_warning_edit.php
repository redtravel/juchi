<?php include(APPPATH.'views/common/header.php');?>
<script type="text/javascript" src="public/js/utils.js"></script>
<script type="text/javascript" src="public/js/validator.js"></script>
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
                    $(":input[name=purchase_provider]").val(<?php print $row->provider_id; ?>);
                    get_provider_batch($(":input[name=purchase_provider]")[0]);
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
                        $("#purchase_batch").val(<?php print $row->batch_id; ?>);
		    });
		});
	}
	//]]>
</script>
<div class="main">
	<div class="main_title">
	<span class="l">仓库管理 &gt;&gt; 库存预警条目编辑</span>
	 <span class="r">[ <a href="/inventory_warning/view_warning_list">返回列表 </a>]</span>
	<?php if (check_perm('warning_edit')): ?>
	 <span class="r"><a href="inventory_warning/add" class="add">新增</a></span>
	<?php endif; ?>
	</div>
	<div class="blank5"></div>
	<?php print form_open('/inventory_warning/proc_edit_warning',array('name'=>'mainForm','onsubmit'=>'return check_form()'),array('warning_id'=>$row->id));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"></td>
			</tr>
                        <tr>
				<td class="item_title" width="150px">预警类型:</td>
				<td class="item_input">
                                        <label><?php print form_radio(array('name'=>'warn_type', 'value'=>1,'checked'=>$row->warn_type==1)); ?>按商品款号</label>
					<label><?php print form_radio(array('name'=>'warn_type', 'value'=>2,'checked'=>$row->warn_type==2)); ?>按指定批次</label>
				</td>
			</tr>
			<tr id="wt01" style="display: none">
				<td class="item_title">供应商:</td>
				<td class="item_input">
					<select id="purchase_provider" name="purchase_provider" onchange="get_provider_batch(this);" >
						<?php foreach ($provider_list as $key => $val): ?>
                                                    <?php print $key; ?>
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
                                    <?php if ($can_edit): ?>
                                    <?php print form_input(array('name'=> 'product_sn','class'=> 'textbox require','value' => $row->product_sn));?>
                                    <?php else: print $row->product_sn; ?>
                                    <?php endif; ?>
				</td>
			</tr>
                        <tr id="wt03">
				<td class="item_title">最小预警库存数:</td>
				<td class="item_input">
                                    <?php if ($can_edit): ?>
                                    <?php print form_input(array('name'=> 'min_number','class'=> 'textbox require','value' => $row->min_number));?>
                                    <?php else: print $row->min_number; ?>
                                    <?php endif; ?>
				</td>
			</tr>
			<tr>
				<td class="item_title"></td>
				<td class="item_input">
					<?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'编辑'));?>
				</td>
			</tr>
			<tr>
				<td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<?php include(APPPATH.'views/common/footer.php');?>