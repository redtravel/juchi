<?php foreach($address_list as $id => $address): ?>
<li id="address<?=$address->address_id?>" <?php if($address->address_id == $default_address->address_id):?> class="default"<?php endif; ?>>
<div class="inner">
   <div class="choose-shipping-name"><span class="name"><?php echo $address->consignee;?></span><span class="tell"><?php echo (!empty($address->mobile))? $address->mobile : $address->tel;?></span></div>
   <div class="choose-shipping-address">
        <span class="prov"><?=$address->province_name?></span>
        <span class="city"><?=$address->city_name?></span><span>
        <p class="addr-bd"><?=$address->district_name.$address->address;?></p>
   </div>
   <div class="addr-toolbar">
       <span class="modify" onclick="load_address_form(<?php print $address->address_id ?>)">修改</span>
       <span class="delete address_del" data-recid="<?php print $address->address_id; ?>">删除</span>
       
       <span class="delete address_default" data-recid="<?php print $address->address_id; ?>"<?php if($address->address_id == $default_address->address_id): ?> style="display: none;"<?php endif; ?>>设为默认</span>
       
  </div>
</div>
<?php if($address->address_id == $default_address->address_id): ?>
<ins class="deftip">默认</ins>
<?php endif; ?>
</li>
<?php endforeach; ?>
<li ><a <?php if(count($address_list) >= 20) { echo 'href="#limit-box" data-toggle="modal" data-container="body"';}else{ echo 'onclick="load_address_form(0)"';}?> >
<div class="inner xinzeng">
   <i>+</i>新增收货地址
</div>
</a>
</li>