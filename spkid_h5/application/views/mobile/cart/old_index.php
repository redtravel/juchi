<?php include APPPATH."views/mobile/header.php"; ?>
<div class="views">
<div class="view view-main" data-page="cart">
<div class="navbar">
    <div class="navbar-inner">
        <div class="left">
            <a href="#" class="link icon-only">
                <i class="icon icon-back"></i>
            </a>
        </div>
        <div class="center">购物车(<?php print $cart_summary['product_num']; ?>)</div>
        <div class="right">
            <a href="#">
                编辑
            </a>
        </div>
    </div>
</div> 
<!-- 底部工具栏开始 -->    
    <div class="toolbar">
        <div class="toolbar-inner">
            <a href="#" class="link"><input type="checkbox" name="chk_all" style="margin-right: 10px;"/>全选</a>
            <a href="#" class="link">合计：￥<?php print fix_price($cart_summary['product_price']); ?> 不含运费</a>
            <a href="#" class="link">结算(1)</a>
        </div>
    </div>
<!-- 底部工具栏结束 -->    
    <div class="pages">
        <div data-page="cart" class="page navbar-fixed">           
            <div class="page-content">
                <?php foreach ($cart_summary['product_list'] as $provider): ?>
                <div><?php print $provider['provider_name'] ?></div>
                <div class="list-block media-list">
                    <ul>
                      <?php foreach ($provider['product_list'] as $product): ?>
                      <li><a href="#" class="item-link item-content">
                           <div class="item-media"><input type="checkbox" name="sub_id[]" value="<?php print $product->rec_id; ?>" style="margin-right: 20px;"/>
                           <img src="<?php print img_url($product->img_url); ?>.85x85.jpg" alt="<?php print $product->product_name; ?>"  width="85" height="85" />
                           </div>
                          <div class="item-inner">
                            <div class="item-subtitle"><?php print $product->product_name; ?></div>
                            <div class="item-text">规格：<?php print $product->size_name; ?></div>
                            <div class="item-title-row">
                              <div class="item-title">￥<?php print fix_price($product->shop_price); ?></div>
                              <div class="item-after">X <?php print $product->product_num; ?></div>
                            </div>          
                          </div>
                          </a>
                      </li>
                      <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>                     
        </div>
    </div>
</div>
</div>
</body>
</html>