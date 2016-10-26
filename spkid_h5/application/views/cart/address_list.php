<?php foreach ($address_list as $add): ?>
<li id="address_<?php print $add->address_id ?>">
	<label>
		<?php print form_radio('address_id',$add->address_id,$add->address_id == $shipping['address_id'],'onclick="cancel_address_form()" province_id="'.$add->province.'"'); ?>
		<span class="buyer_name"><?php print $add->consignee ?></span><span class="buyer_adress"><?php print "{$add->province_name} {$add->city_name} {$add->district_name} {$add->address} {$add->mobile} {$add->tel}" ?> </span>
		<span class="modify">
		<a href="javascript:void(0)" onclick="load_address_form(<?php print $add->address_id ?>)" class=" c_o">修改</a>
		</span>
	</label>
</li>
<?php endforeach ?>
<?php if ($address_list): ?>
<li id="address_id">
	<label>
		<input type="radio" name="address_id" value="" onclick="load_address_form(0);" />
		<span class="buyer_name address_new">新增收货地址</span>
	</label>
</li>
<?php endif ?>