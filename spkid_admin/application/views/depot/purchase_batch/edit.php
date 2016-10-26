<?php include(APPPATH.'views/common/header.php');?>
    <script type="text/javascript" src="public/js/listtable.js"></script>
    <script type="text/javascript" src="public/js/utils.js"></script>
    <script type="text/javascript" src="public/js/validator.js"></script>
    <script type="text/javascript">
        //<![CDATA[
        function check_form(){
            var validator = new Validator('mainForm');
            validator.required('batch_name', '请填写批次名称');
            validator.isInt('plan_num', '预计收货数量必须是正整数',true);
            validator.required('plan_arrive_date', '请填写预计收货日期');
            validator.required('provider_id', '请选择供应商');
            validator.required('brand_id', '请选择品牌');
            return validator.passed();
        }
	$(function(){
	    $("#provider_id").change(function(){
		var provider_id = $(this).val();
		$.post('/purchase_batch/get_provider_brand/'+provider_id,{is_ajax:1,rnd : new Date().getTime()},function(data){
		    data = jQuery.parseJSON(data);
		    if(data.result == 0){
			$("select[name='brand_id']").html("<option value=''>无可选品牌</option>");
		    }else{
			$("select[name='brand_id']").html("<option value=''>请选择</option>");
			$.each(data.list,function(i,v){
			$("select[name='brand_id']").append("<option value='"+v.brand_id+"'>["+v.brand_initial+"]-"+v.brand_name+"</option>");
			});
		    }
		});
	    });
	});
        //]]>
    </script>
<div class="main">
	<div class="main_title"><span class="l">批次管理 >> 编辑</span> <a href="purchase_batch" class="return r">返回列表</a></div>
	<div class="blank5"></div>
	<?php print form_open_multipart('purchase_batch/proc_edit',array('name'=>'mainForm','onsubmit'=>'return check_form()'));?>
		<table class="form" cellpadding=0 cellspacing=0>
			<tr>
				<td colspan=2 class="topTd"><input name="batch_id" value="<?php print $row->batch_id; ?>" type="hidden"/></td>
			</tr>
			<tr>
                <td class="item_title">批次名称:</td>
                <td class="item_input">
                    <?php print form_input('batch_name',$row->batch_name,'class="require textbox" '.($perm_edit?'':'disabled')); ?>
                </td>
			</tr>
			<tr>
                <td class="item_title">批次类型:</td>
                <td class="item_input">
                    <select name="batch_type">
                        <option value="0" <?php print $row->batch_type==0?"selected":""; ?>>采购单</option>
                        <option value="1" <?php print $row->batch_type==1?"selected":""; ?>>代转买批次</option>
                        <option value="2" <?php print $row->batch_type==2?"selected":""; ?>>盘赢</option>
                        <option value="3" <?php print $row->batch_type==3?"selected":""; ?>>其他</option>
                    </select>
                </td>
			</tr>
			<tr>
                <td class="item_title">供应商:</td>
                <td class="item_input">
                    <select id="provider_id" name="provider_id">
                        <option value="">供应商</option>
                        <?php foreach($provider_list as $provider) echo "<option value='",$provider->provider_id,"' ",($provider->provider_id == $row->provider_id? 'selected':''),">[{$provider->provider_code}]-{$provider->provider_name}</option>"; ?>
                    </select>
                </td>
			</tr>
			<tr>
			<td class="item_title">品牌:</td>
			<td class="item_input">
			     <select id="brand_id" name="brand_id">
                                    <option value="">请选择</option>
				    <?php foreach($brand_list as $brand) echo "<option value='",$brand->brand_id,"' ",($brand->brand_id == $row->brand_id? 'selected':''),">[{$brand->brand_initial}]-{$brand->brand_name}</option>"; ?>
                                </select>
			</td>
			</tr>
			<tr>
            	<td class="item_title">是否虚库销售:</td>
				<td class="item_input">
				<select name="is_consign">
					<option value="0" <?php print $row->is_consign==0?"selected":""; ?>>实库销售</option>
					<option value="1" <?php print $row->is_consign==1?"selected":""; ?>>虚库销售</option>
				</select>
				</td>
			</tr>
			<tr>
                <td class="item_title">预计收货数量:</td>
                <td class="item_input"><?php print form_input(array('name'=> 'plan_num','value'=>$row->plan_num,'class'=> 'textbox require'));?></td>
			</tr>
			<tr>
                <td class="item_title">预计收货日期:</td>
                <td class="item_input">
                    <input type="text" name="plan_arrive_date" value="<?php print $row->plan_arrive_date; ?>" class="textbox require" id="plan_arrive_date" />
                </td>
			</tr>
			<tr>
                <td class="item_title"></td>
                <td class="item_input">
                    <?php if($perm_edit): ?>
                    <?php print form_submit(array('name'=>'mysubmit','class'=>'am-btn am-btn-primary','value'=>'提交'));?>
                    <?php endif; ?>
                </td>
			</tr>
			<tr>
                <td colspan=2 class="bottomTd"></td>
			</tr>
		</table>
	<?php print form_close();?>
</div>
<script type="text/javascript">
    $(function(){
        //$('input[type=text][name=create_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
        $('input[type=text][name=plan_arrive_date]').datepicker({dateFormat: 'yy-mm-dd',changeMonth: true,changeYear: true, nextText:'', prevText:'', yearRange:'-100:+10'});
    });
</script>
<?php include(APPPATH.'views/common/footer.php');?>