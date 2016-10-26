<div class="picker-modal modal-in" style="height:auto;">
    <div class="toolbar">
      <div class="toolbar-inner" style="background-color:#fff;">
        
        <div class="center" id="err_tip"></div>
        <div class="right"><a href="#" class="close-picker hu-guanbi"></a></div>
      </div>
    </div>
    <div class="picker-modal-inner">
      
        <!-- 自定义开始 -->
            <div class="list-block media-list" style="margin:0 0;">
                <ul class="cart-tcks-hu">
                  <li class="item-content">
                    <div class="item-media col-v-img2"><img src="<?php print img_url($goods->img_url); ?>.85x85.jpg" alt="<?php print $goods->brand_name . ' ' . $goods->product_name; ?>" /></div>
                    <div class="item-inner">
                      
                      <div class="item-subtitle tckjg-hu" style="font-size:1.2em;">￥<?=$goods->product_price?></div>
                      <div class="item-subtitle" style="color:#1a3745;">已选规格：<span class="sel_size"><?=$goods->size_name?></span></div>
                    </div>
                  </li>
                </ul>
                <div class="hu-guige">选规格：</div>
                <div class="list-block-label">
                  <?php foreach($sub_list as $sub): ?>
                  <!-- 
                  .sel_size_yes 表示有库存可以选择的规格/具体样式还没定义，这里这样写是逻辑需要
                  .sel_size_no 表示无库存不可选择的规格/具体样式还没定义，这里这样写是逻辑需要
                  -->
                  <span id="rec_id<?=$sub->sub_id?>" onclick="sel_size_yes(<?=$sub->sub_id?>)"  data-subid="<?=$sub->sub_id?>" <?php if($sub->sale_num): ?>class="sel_size_yes sale-no2 "<?php if($sub->sub_id == $goods->sub_id): ?> <?php endif;?><?php else:?>class="sel_size_no"<?php endif; ?> ><?=$sub->size_name?></span>
                  <?php endforeach; ?>
                </div>
                <div>
                    <input type="hidden" id="sel_sub_id" value="">
                    <input type="hidden" id="c_rec_id" value="<?=$goods->rec_id?>">
                    <input type="hidden" id="c_sub_id" value="<?=$goods->sub_id?>">
                    <!--
                    <div class="size_edit_cfm">确定</div>
                    -->
                </div>
            </div>  
        <!-- 自定义结束 -->
      
    </div>
    <div class="size_edit_cfm" style="height:40px; line-height: 40px; text-align: center;background-color: #f9221d;color:#ffffff; font-size:18px; cursor: pointer;" onclick="size_edit_cfm()">确定</div>
  </div>
