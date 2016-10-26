      <div class="modal-dialog modify-address">
        <div class="modal-content">
          <div class="modal-header v-close">
              <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title xiugai"><?php if(empty($shipping['address_id'])): ?>新增地址<?php else: ?>修改地址<?php endif; ?></h4>
          </div>
          <div class="modal-body">
               <div class="modify-dizhi form clearfix">
                    <ul>
                    <li class="clearfix">
                    <label class="text-label"><i>*</i><span class="addr-title">收货地区：</span></label>
                    <div class="fl-left">
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
                        <br><span class="err_tip" id="region_err">请选择收货地区</span>
                    </div>
                    </li>
                    
                    <li>
                    <label class="text-label"><i>*</i><span class="addr-title">详细地址：</span></label>
                    <textarea rows="10" cols="30" class="addrDetail text-filed" name="address"><?php print isset($shipping['address'])?$shipping['address']:''; ?></textarea>
                    <br><span class="err_tip" id="address_err">不能为空</span>
                    </li>
                    
                    <li>
                    <label class="text-label"><i>*</i><span class="addr-title"><em class="space-name">收货</em>人：</span></label>
                    <input type="text"  value="<?php print isset($shipping['consignee'])?$shipping['consignee']:''; ?>" class="text-filed" name="consignee">
                    <br><span class="err_tip" id="consignee_err">不能为空</span>
                    </li>
                    
                    <li>
                    <label class="text-label"><i>*</i><span class="addr-title">手机号码：</span></label>
                    <input type="text"  value="<?php print isset($shipping['mobile'])?$shipping['mobile']:''; ?>" class="text-filed" name="mobile">
                    <br><span class="err_tip" id="mobile_err">不能为空或手机号不正确</span>
                    </li>
                    
                    <li>
                    <label class="text-label"><span class="addr-title ad-email">邮 编：</span></label>
                    <input type="text"  value="<?php print isset($shipping['zipcode'])?$shipping['zipcode']:''; ?>" class="text-filed" name="zipcode">
                    </li>
                    
                    
                    <li class="addr-default">
                    <span class=" i i-checkbox<?php print (isset($shipping['is_used']) && $shipping['is_used']) ? ' checked':''; ?>" id="iscurrent"></span>
                    <label for="setDefault">设为默认地址</label>
                    </li>                    
                    
                    <div class="operate">
                        <input type="hidden" name="address_id" value="<?=$shipping['address_id']?>">
                         <button class="btn-primary  btn" type="button" onclick="submit_address_form()">保存并使用</button>
                    </div>
                    
                    </ul>              
               </div>
          </div>
        </div>
      </div>