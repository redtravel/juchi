<table width="820" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td colspan="3">带 * 标记的为必填项</td>
		</tr>
	<tr>
		<td width="90"><span class="cred">*</span> <span class="c_by">收货人：</span></td>
		<td width="290"><input type="text" name="consignee" value="<?php print isset($shipping['consignee'])?$shipping['consignee']:''; ?>" class="t_w150" maxlength="10" /></td>
		<td width="440" class="c99">请输入收货人姓名，最多十个字</td>
	</tr>
	<tr>
		<td><span class="cred">*</span> <span class="c_by">所在省市：</span></td>
		<td>
			<?php print form_dropdown(
				'province',
				array(''=>'请选择省')+get_pair($province_list,'region_id','region_name'),
				empty($shipping['province'])?'':$shipping['province'],
				'onchange="load_city()" style="width:90px;"'
				);
			?>
			<?php print form_dropdown(
				'city',
				array(''=>'请选择市')+get_pair($city_list,'region_id','region_name'),
				empty($shipping['city'])?'':$shipping['city'],
				'onchange="load_district()" style="width:90px;"'
				);
			?>
			<?php print form_dropdown(
				'district',
				array(''=>'请选择区')+get_pair($district_list,'region_id','region_name'),
				empty($shipping['district'])?'':$shipping['district'],
				'style="width:90px;"'
				);
			?>
		</td>
		<td class="c99">请选择所在省市</td>
	</tr>
	<tr>
		<td><span class="cred">*</span> <span class="c_by">详细地址：</span></td>
		<td><input name="address" type="text" value="<?php print isset($shipping['address'])?$shipping['address']:''; ?>" class="t_w235" /></td>
		<td class="c99">收货地址必须包括省市信息，地址不详细可能使您无法收到购买的商品。</td>
	</tr>
	<tr>
		<td><span class="cred">*</span> <span class="c_by">邮 编：</span></td>
		<td><input name="zipcode" type="text" value="<?php print isset($shipping['zipcode'])?$shipping['zipcode']:''; ?>" class="t_w150" /></td>
		<td class="c99">请填写准确的邮编，错误的邮编可能会使您无法收到购买的商品。</td>
	</tr>
	<tr>
		<td><span class="cred">*</span> <span class="c_by">移动电话：</span></td>
		<td><input name="mobile" type="text" value="<?php print isset($shipping['mobile'])?$shipping['mobile']:''; ?>" class="t_w150" /></td>
		<td class="c99">用于发送进度通知，接受短信完全免费</td>
	</tr>
	<tr>
		<td> 　<span class="c_by">固定电话：</span></td>
		<td><input name="tel" type="text" value="<?php print isset($shipping['tel'])?$shipping['tel']:''; ?>" class="t_w150" /></td>
		<td class="c99"></td>
	</tr>
	<tr id="tr_address_action" >
		<td colspan="3">
		    <div class="address_btn_div" style="display: block; ">
			<div class="btn_sub" onclick="submit_address_form()">提交</div>
            <?php if(empty($no_cancel)):?>
			<div class="btn_cal" onclick="cancel_address_form()">取消</div>
            <?php endif;?>
		    </div>
		</td>
	</tr>
</table>