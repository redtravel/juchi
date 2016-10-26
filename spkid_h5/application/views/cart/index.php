<?php include APPPATH . "views/common/header.php"; ?>
<div id="page">

    <script src="<?php print static_style_url('js/cart.js'); ?>" type="text/javascript"></script>

    <!-- ********************** PAGE BLOCK: CONTAINER ********************* -->
    <div id="container" >
        <!-- ********************** PAGE BLOCK: CONTENT ********************* -->
        <div id="content" class="w_980">

            <div id="process01" class="tit_prcss"><img src="<?php print static_style_url('image/flow/tit_step1_w980.png'); ?>" alt="STEP1 SHOPPING CART"></div>
            <h3 class="top">交易情况</h3>
            <dl class="orderTable">
                <dt>
                <table class="orderList" summary="This is the goods list.">
                    <colgroup>
                        <col width="25px" />
                        <col width="120px" />
                        <col width="250px" />
                        <col width="150px" />
                        <col width="150px" />
                        <col width="120px" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col" colspan="3" class="first">
                                <strong>商品</strong>
                            </th>
                            <th scope="col">价格</th>
                            <th scope="col">数量</th>
                            <th scope="col" class="last">合计</th>
                        </tr>
                    </thead>
                </table>
                </dt>
                <?php foreach ($cart_summary['product_list'] as $provider): ?>
                    <dd class="co_seller">
                        <div class="sellerShop" data-domain="M18" seller_cust_no="<?php print $provider['provider_id']; ?>">
                            <dl>
                                <dt><a href="/provider-<?php print $provider['provider_id'];?>.html" class="sellername" target="_blank"><?php print $provider['provider_name'] ?></a></dt>
                            </dl>	
                        </div>
                        <div class="seller_sum">
                            <ul>
                                <li>合计金额 : <?php print fix_price($provider['product_price']); ?>元</li>
                                <li>&nbsp;</li>
                                <li class="discount">
                                    <?php if ($provider['voucher']): ?>
                                        现金券(<?php print $provider['voucher']->voucher_sn; ?>)
                                        抵扣 <?php print fix_price($provider['voucher']->payment_amount); ?> 元
                                        <a href="javascript:void(0);" onclick="remove_voucher('<?php print $provider['voucher']->voucher_sn; ?>');">取消</a>
                                    <?php else: ?>
                                        <a class="orderBtns btn_mycoupon" href="javascript:goMyCoupons('<?php print $provider['provider_id']; ?>')">我的优惠券</a>
                                    <?php endif; ?>
                                </li>
                                <li>&nbsp;</li><li class="last">总合计 : <?php print fix_price($provider['product_price'] - ($provider['voucher'] ? $provider['voucher']->payment_amount : 0)); ?>元</li>
                            </ul>
                        </div>
                        <ul class="list"><li class="">
                                <table class="orderList" summary="This is the goods list.">                            
                                    <colgroup>
                                        <col width="45px" />
                                        <col width="105px" />
                                        <col width="335px" />
                                        <col width="160px" />
                                        <col width="175px" />
                                        <col width="155px" />
                                    </colgroup>			        
                                    <tbody>
                                        <?php foreach ($provider['product_list'] as $product): ?>
                                            <tr id="<?php print $product->rec_id; ?>">
                                                <td class="checking" style="padding-left: 5px;">
                                                    <a href="javascript:void(0);" onclick="delete_cart('<?php print $product->rec_id; ?>');" class="bt bt10_19 gray" title="从购物车中删除"><span>删除</span></a>
                                                </td>
                                                <td class="goodsImg">
                                                    <p class="thumb"><a href="/product-<?php print $product->product_id; ?>.html" title="<?php print $product->product_name; ?>" target="_blank" >
                                                            <span class="num"><?php print $product->product_num; ?></span>
                                                            <img src="<?php print img_url($product->img_url); ?>.85x85.jpg" alt="<?php print $product->product_name; ?>"  width="85" height="85" /></a>
                                                    </p>
                                                </td>
                                                <td class="info">
                                                    <a class="title" href="/product-<?php print $product->product_id; ?>.html" target="_blank"><span class="btn_quick" >Quick View</span><?php print $product->product_name; ?></a>
                                                    <p><span class='opt'>颜色 : <?php print $product->color_name ?> / 尺码 : <?php print $product->size_name; ?></span></p>
                                                </td>
                                                <td class="option">
                                                    <p><?php print fix_price($product->shop_price); ?>元</p>
                                                </td>
                                                <td class="qty">                            
                                                    <div id="cart_qty_<?php print $product->rec_id; ?>">
                                                        <div class="edt"><?php print $product->product_num; ?></div>

                                                        <div style="display : ">
                                                            <a href="javascript:showEdit('<?php print $product->rec_id; ?>','','');" class="orderBtns btn_edit">修改</a>
                                                        </div>
                                                    </div>
                                                    <div style="display:none;" id="cart_edit_<?php print $product->rec_id; ?>">
                                                        <div class="qtyCtrl">
                                                            <input type="text" maxlength="3" class="textType" id="OrderCnt_<?php print $product->rec_id; ?>" value="<?php print $product->product_num; ?>" onclick="javascript:OrderCnt_onClick(this);" onblur="javascript:OrderCnt_onBlur(this);" >
                                                            <a href="javascript:UpQty('<?php print $product->rec_id; ?>');" class="up">up</a>
                                                            <a href="javascript:DownQty('<?php print $product->rec_id; ?>');" class="down">down</a>
                                                        </div>    
                                                        <div style="margin:0 auto;width: 80px;">
                                                            <a href="javascript:goQtyEdit('<?php print $product->rec_id; ?>', '');" class="orderBtns btn_apply">确定</a>
                                                            <a href="javascript:goQtyCancel('<?php print $product->rec_id; ?>')" class="orderBtns btn_cancel">取消</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="subtotal">
                                                    <strong><?php print fix_price($product->product_price * $product->product_num); ?>元</strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </li>
                        </ul>
                    </dd>
                <?php endforeach; ?>

                <!-- tfoot -->
                <dd class="tfoot">

                </dd>
            </dl>
            <div id="item_price" class="tplt_sum">
                <div class="cal_table">
                    <table>
                        <colgroup>
                            <col width="203px" />							
                            <col width="28px" />
                            <col width="195px" />
                            <col width="28px" />
                            <col width="239px" />
                        </colgroup>
                        <thead>
                            <tr>
                                <th scope="col">合计金额</th>
                                <th scope="col" class="cal_sign">calculate sign</th>
                                <th scope="col">购物车优惠</th>
                                <th scope="col" class="cal_sign">calculate sign</th>
                                <th scope="col"><em>总购买额</em></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong><span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TopTotalPriceTxt"><?php print fix_price($cart_summary['product_price']); ?>元</span></strong></td>
                                <td><p class="sign_minus">-</p></td>
                                <td class="cp_cart"><strong><span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TopCartDiscountTxt"><?php print fix_price($cart_summary['voucher']); ?>元</span></strong></td>
                                <td><p class="sign_same">=</p></td>
                                <td>
                                    <em>
                                        <strong><span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TopTotalPayableAmountTxt"><?php print fix_price($cart_summary['product_price'] - $cart_summary['voucher']) ?>元</span></strong>
                                    </em>
                                    <p class="totalitem">合计商品为<?php print $cart_summary['product_num']; ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>                    
                </div>
            </div>
            <p class="paymentInfo"><strong>运费根据不同的支付方法和运送范围统一支付.</strong></p>
            <div class="processBtn"><a href="/cart/checkout" class="btn_order"><span><em>购买</em></span></a></div>
        </div>
    </div>
</div> 
<script type="text/javascript">
    var curr_provider_id = 0;
    // 弹层
    function goMyCoupons(provider_id) {
        curr_provider_id = provider_id;
        $("#div_popup_1").show();
    }
    // 使用现金券
    // 后续完善ajax请求
    function use_voucher(voucher_sn) {
        if (!voucher_sn) {
            alert('请选择或输入现金券号');
            return false;
        }
        $.ajax({
            url: '/cart/pay_voucher',
            data: {voucher_sn: voucher_sn, provider_id: curr_provider_id, rnd: new Date().getTime()},
            dataType: 'json',
            type: 'POST',
            success: function(result) {
                if (result.msg)
                    alert(result.msg);
                if (result.err)
                    return false;
                location.href = location.href;
            }
        });
    }

    function remove_voucher(voucher_sn) {
        if (!voucher_sn) {
            alert('请选择要取消的现金券号');
            return false;
        }
        $.ajax({
            url: '/cart/unpay_voucher',
            data: {voucher_sn: voucher_sn, rnd: new Date().getTime()},
            dataType: 'json',
            type: 'POST',
            success: function(result) {
                if (result.msg)
                    alert(result.msg);
                if (result.err)
                    return false;
                location.href = location.href;
            }
        });
    }

    function delete_cart(rec_id)
    {
        $.ajax({
            url: '/cart/remove_from_cart',
            data: {rec_id: rec_id, rnd: new Date().getTime()},
            dataType: 'json',
            type: 'POST',
            success: function(result) {
                if (result.msg)
                    alert(result.msg);
                if (result.err)
                    return false;
                location.href = location.href;
            }
        });
    }

</script>




<script type="text/javascript">

    $(document).ready(function(e) {
        //$('#ac_layer').css({ "top": ($('#search____keyword').offset().top - 3), "left": (324) });
        if (window.gnbTop && gnbTop.EventBind)
            gnbTop.EventBind();
        if (window.gnbTop && gnbTop.GnbMenu)
            gnbTop.GnbMenu();
    });
    Util.SmartWindowInit();

    var smart_tab = "";
    var tab_idt = "";
    var tab_onoff = "";
    if (smart_tab != "" && tab_onoff == "on") {
        Util.openSmartTab(Public.convertNormalUrl("~/smarttab/default.aspx?smart_tab=") + smart_tab + (tab_idt != "" ? "&tab_idt=" + tab_idt : "") + "&mt=4&c_btn=Y");
    }

    if (smart_tab == "" && tab_onoff == "on") {
        ControlUtil.addEventHandler(window, "onload", Util.openSmartTab_Auto);
    }


    var gnbTop = {
        EventBind: function() {
            $(".util .my").click(function() {
                $(".ly_my").css({"left": $(".util .my").position().left + $(".util").position().left});
                $(".ly_my").toggle();
            });
        },
    }
</script>
<!-- 使用现金券弹层 -->
<div class="innerPopWrap ui-draggable"  id="div_popup_1" style="top: 226px; left: 50%; position: absolute; margin-left:-400px; z-index: 1001; width: 805px; display:none;">
    <div class="head" >
        <h2><span id="title_popup_1">
                我的优惠券
            </span></h2>
        <div class="closePop"><a href="javascript:Util.closeInnerPopup(null, 'div_popup_1');" style="display:block;">X</a></div>
    </div>
    <div id="mycouponbox" class="" style="top:0;left:0;">
        <div class="header" style="display: none;">
            <h2>我的优惠券</h2><i></i>
            <p class="desc"></p>
        </div>
        <div class="container">
            <!-- 现金券列表 -->
            <div id="div_voucher">
                <table cellspacing="0" cellpadding="0" border="0" style="" id="fCashTable">
                    <tbody>
                        <tr>
                            <th>现金券编号</th>
                            <th>现金券金额</th>
                            <th>有效日期</th>
                            <th>使用范围</th>
                            <th>使用情况</th>
                        </tr>                                
                        <?php foreach ($voucher_list as $v): ?>
                            <tr class="tr_v0">
                                <td><?php print $v->voucher_sn ?></td>
                                <td><?php print $v->voucher_amount; ?>元</td>
                                <td><?php print $v->end_date; ?></td>
                                <td><?php print $v->voucher_name; ?></td>
                                <td><b onclick="use_voucher('<?php print $v->voucher_sn; ?>');" class="fBtn2Gray">使用</b></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <!-- 激活现金券 -->
            <p id="p_user_voucher"><span class="main_fLeft lineheight24">激活新的现金券：</span>
                <input id="voucher_sn" class="fOtherTimeTextBox main_fLeft main_mR10" value="">
                <b class="fBtnRedNoShadow" onclick="use_voucher($('#voucher_sn').val());">立即激活</b>&nbsp;&nbsp;<a href="/user/exchange_voucher.html" class="main_red lineheight24" style="text-decoration:underline" target="_blank">积分兑换现金券</a>
            </p>
        </div>
    </div>
</div>
<?php include APPPATH . 'views/common/footer.php'; ?>
