    <div class="modal-dialog modify-address">
        <div class="modal-content">
            <div class="modal-header v-close">
                <button type="button" class="close triangle-topright" data-dismiss="modal" aria-label="Close" ><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title xiugai">修改地址</h4>
            </div>
            <div class="modal-body">
                <div class="modify-dizhi form clearfix">
                    <ul>
                        <form class="v_edit_info">
                            <input type="hidden" value="<?php echo $address->address_id;?>" name="address_id" id="edit-address_id">
                            <li class="clearfix">
                                <label class="text-label"><i>*</i><span class="addr-title">收货地区：</span></label>
                                <div class="fl-left">
                                    <select name="edit-province" id="edit-province" onchange="return edit_change_region(1,this.value,'edit-city')" class="combobox">
                                        <option value="" selected="selected">-省/直辖市-</option>
                                        <?php foreach ($province as $pro_val) { ?>
                                        <option value="<?php echo $pro_val->region_id;?>"  <?php if($pro_val->region_id == $address->province ) echo 'selected';?>><?php echo $pro_val->region_name;?></option>
                                        <?php } ?>
                                    </select>
                                    <select name="edit-city" id="edit-city"  onchange="return edit_change_region(2,this.value,'edit-district')"  class="combobox" >
                                        <option value="" selected="selected">-市-</option>
                                        <?php foreach ($city as $city_val) { ?>
                                        <option value="<?php echo $city_val->region_id;?>" <?php if($city_val->region_id == $address->city ) echo 'selected';?> ><?php echo $city_val->region_name;?></option>
                                        <?php } ?>
                                    </select>
                                    <select name="edit-district" id="edit-district" class="combobox last">
                                        <option value="" selected="selected">-区/县-</option>
                                        <?php foreach ($district as $dist_val) { ?>
                                        <option value="<?php echo $dist_val->region_id;?>" <?php if($dist_val->region_id == $address->district ) echo 'selected';?> ><?php echo $dist_val->region_name;?></option>
                                        <?php } ?>
                                    </select>                              
                                </div>
                                <div class="v-add">
                                    <span id="address_err" class="v-add-province">不能为空</span>
                                </div>
                            </li>
                            <li>
                                <label class="text-label"><i>*</i><span class="addr-title">详细地址：</span></label>
                                <span class="err_tip" id="address_err" style="bottom:0px;">不能为空</span>
                                <textarea rows="10" cols="30" class="addrDetail text-filed" id="edit-address" name="address"><?php echo $address->address;?></textarea>
                            </li>
                            <li>
                                <label class="text-label"><i>*</i><span class="addr-title"><em class="space-name">收货</em>人：</span></label>
                                <span class="err_tip" id="address_err">不能为空</span>
                                <input type="text" class="text-filed" id="edit-consignee" name="consignee" value="<?php echo $address->consignee;?>">
                            </li>
                          
                            <li>
                                <label class="text-label"><i>*</i><span class="addr-title">手机号码：</span></label>
                                <span class="err_tip v-add-mobile" id="address_err">格式不对</span>
                                <input type="text" class="text-filed" id="edit-mobile" name="mobile" value="<?php echo $address->mobile;?>">
                            </li>           
                            <li class="addr-default">
                                <input type="checkbox" name="is_used" <?php if($address->is_used  == 1) echo 'checked';?> value="<?php echo $address->is_used;?>">
                                <label for="setDefault">设为默认地址</label>
                            </li>
                             <div class="operate">
                                <button class="save-addr btn-primary btn-small btn" type="button" onclick="v_edit_address();">保存</button>
                            </div>
                        </form>
                    </ul>
                </div>
            </div>
        </div>
    </div>