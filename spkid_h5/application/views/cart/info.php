<ul>
    <?php foreach ($product_list as $p): ?>
        <li id="cart_rec_<?php print $p->rec_id; ?>">
            <div class="main_fLeft"> <a class="" target="_blank" href="<?php print "/product-{$p->product_id}-{$p->color_id}.html" ?>"><img alt="" src="<?php print img_url($p->img_58_58); ?>" width="55px;" height="55px;"></a> </div>
            <div class="main_fLeft main_mL10 mainCartGoodInfo"> <a title="" class="main_black main_fLeft" target="_blank" href="<?php print "/product-{$p->product_id}-{$p->color_id}.html"; ?>"><?php print $p->brand_name ?></a><br />
                <a title="" class="main_black" target="_blank" href="<?php print "/product-{$p->product_id}-{$p->color_id}.html"; ?>"><?php print $p->product_name ?></a><br />
                <b>[<?php print $p->color_name ?>]</b>&nbsp;<b>[<?php print $p->size_name ?>]</b> </div>
            <div class="main_fLeft main_mL10 mainCartGoods"> <b class="main_red">￥<?php print $p->product_price; ?>&nbsp;<?php /* print $p->product_num; */ ?></b></div>
            <div class="mainCartOptions">
                <b><s id="cartOption_1" class="subOprate1" onclick='update_num_pro(<?php print $p->rec_id; ?>,-1)'></s><span id="cartNum_1" class="mainCartNum"><?php print $p->product_num; ?></span><s onclick='update_num_pro(<?php print $p->rec_id; ?>,1)' id="cartOption_1" class="addOprate1"></s></b>
                <a href="javascript:;" onclick="remove_cart_pro(<?php print $p->rec_id ?>);" class="main_black main_mR20">删除</a>
            </div>
        </li>
    <?php endforeach ?> 
    <li id="cartTotalBox">
        <div id="cartTotalDivBox" class="main_mR10 main_fRight main_mT10">
            <p class="pfont12 main_mR10" style="width:auto">共有<strong id="_summary_product_num" class="main_red"><?php print $cart_summary['product_num']; ?></strong>件商品&nbsp;&nbsp;总价格:<strong class="main_red" id="_summary_product_price">￥<?php print number_format($cart_summary['product_price'], 2, '.', ''); ?>元</strong></p>
            <a target="_self" class="main_btnRed4 main_fRight" href="/cart">去结算</a> </div>
    </li>
</ul>
