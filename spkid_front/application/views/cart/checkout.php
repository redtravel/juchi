<?php include APPPATH . "views/common/header.php"; ?>
<script type="text/javascript">
    var cart_summary = <?php print json_encode($cart_summary); ?>;
    var region_shipping_fee = <?php print json_encode($region_shipping_fee); ?>;
    var base_url = '<?php print base_url(); ?>';
    $(function() {
        init_price();
        init_payment();
    });
</script>
<div id="page">
    <!-- BLOCK : CONTENT -->
    <form name="aspnetForm" method="post" action="javascript:void(0);" id="aspnetForm">
        <div>
            <input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="/wEPDwULLTE3NDYyOTA4NTJkGAQFOmN0bDAwJGN0bDAwJE1haW5Db250ZW50SG9sZGVyJG1hc3Rlcl9xdWlja19pbmZvJHFib3gyQ291bnQPD2RmZAU5Y3RsMDAkY3RsMDAkTWFpbkNvbnRlbnRIb2xkZXIkbWFzdGVyX3F1aWNrX2luZm8kcWJveENvdW50Dw9kZmQFLWN0bDAwJGN0bDAwJE1haW5Db250ZW50SG9sZGVyJGxvZ2luX211bHRpdmlldw8PZAIBZAUfY3RsMDAkY3RsMDAkaGVhZCRjc3NfbXVpbHRpdmlldw8PZAIBZF2iRho7OpxMqBkrkyecZbee8aAE" />
        </div>


        <script src="<?php print static_style_url('js/buyOrder.js'); ?>" type="text/javascript"></script>

        <div id="container">
            <!-- ********************** PAGE BLOCK: CONTENT ********************* -->


            <div id="content" class="w_980">

                <div id="process02" class="tit_prcss"><img src="<?php print static_style_url('image/flow/tit_step2_w980.png'); ?>" alt="STEP2 SHOPPING CART"></div>	
                <!-- [[ AREA Goods Cart ]] -->


                <h3  class="top">订购信息 </h3>
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
                                    <li id="product_price_<?php print $provider['provider_id'] ?>">合计金额 : <?php print fix_price($provider['product_price']); ?>元</li>
                                    <li id="shipping_fee_<?php print $provider['provider_id'] ?>">运费 : 0 元</li>
                                    <li class="discount" id="voucher_<?php print $provider['provider_id'] ?>">
                                        <?php if ($provider['voucher']): ?>
                                            现金券(<?php print $provider['voucher']->voucher_sn; ?>)
                                            抵扣 <?php print fix_price($provider['voucher']->payment_amount); ?> 元
                                        <?php else: ?>

                                        <?php endif; ?>
                                    </li>
                                    <li>&nbsp;</li>
                                    <li class="last" id="subtotal_<?php print $provider['provider_id'] ?>">总合计 : <?php print fix_price($provider['product_price'] - ($provider['voucher'] ? $provider['voucher']->payment_amount : 0)); ?>元</li>
                                </ul>
                            </div>
                            <ul class="list"><li class="">
                                    <table class="orderList" summary="This is the goods list.">                            
                                        <colgroup>
                                            <col width="105px" />
                                            <col width="381px" />
                                            <col width="160px" />
                                            <col width="175px" />
                                            <col width="155px" />
                                        </colgroup>			        
                                        <tbody>
                                            <?php foreach ($provider['product_list'] as $product): ?>
                                                <tr id="<?php print $product->rec_id; ?>">
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

                <h3>
                    预计付款金额
                </h3>
                <div class="buyTotal">

                    <div class="cal_table">
                        <table>
                            <colgroup>
                                <col width="178px"/>
                                <col width="28px"/>
                                <col width="163px"/>
                                <col width="28px"/>
                                <col width="170px"/>
                                <col width="28px"/>
                                <col width="214px"/>				        
                            </colgroup>
                            <thead>
                                <tr>
                                    <th scope="col">商品价格</th>
                                    <th scope="col" class="cal_sign">calculate sign</th>
                                    <th scope="col">优惠</th>
                                    <th scope="col" class="cal_sign">calculate sign</th>
                                    <th scope="col">运费</th>
                                    <th scope="col" class="cal_sign">calculate sign</th>
                                    <th scope="col"><em>总购买额</em></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong><span id="product_price"><?php print fix_price($cart_summary['product_price']); ?>元</span></strong></td>
                                    <td>
                                        <p class="sign_minus">-</p>
                                    </td>
                                    <td><strong><span id="voucher_payment_amount"><?php print fix_price($cart_summary['voucher']); ?>元</span></strong></td>
                                    <td>
                                        <p class="sign_plus">+</p>
                                    </td>
                                    <td><strong><span id="shipping_fee"><?php print fix_price($cart_summary['shipping_fee']); ?>元</span></strong></td>
                                    <td>
                                        <p class="sign_same">=</p>
                                    </td>
                                    <td>
                                        <em><strong><span id="product_total"><?php print fix_price($cart_summary['product_price'] + $cart_summary['shipping_fee'] - $cart_summary['voucher']); ?>元</span></strong></em>

                                        <p class="totalitem">合计商品为<?php print fix_price($cart_summary['product_num']); ?></p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>                    
                    </div>

                </div>

                <p class="paymentInfo"><strong>根据不同的支付方法和运送范围可能产生附加费用.</strong></p>
                <!-- 运送信息开始    -->
                <h3 style="display:">运送信息</h3>

                <div class="car_b_c mes_c">
                    <ul class="address">
                        <?php include(APPPATH . 'views/cart/address_list.php'); ?>
                    </ul>
                    <div class="address_block" id="address_block" style="display:<?php print $address_list ? 'none' : "''"; ?>;">
                        <?php if (!$address_list):$no_cancel=TRUE; ?>
                            <?php include(APPPATH . 'views/cart/address_block.php'); ?>
                        <?php endif ?>
                    </div>
                </div>
                <!-- 贝购运送信息结束 -->

                <!-- [[ AREA Payment info ]] -->
                <h3 class="type1" id="payment_info">付款信息</h3>

                <div class="infoView pay" >

                    <div class="cn">
                        <div class="inf_box">


                        </div>
                        <ul class="methodList">
                            <li>
                                <input type="checkbox" id="chk_use_balance" name="chk_use_balance" value="<?php print fix_price($user->user_money); ?>" class="radioType" onclick="javascript:use_balance();"  />
                                <label for="chk_use_balance">账户余额(元) [ 剩余金额  : <strong id="remainGAccount"><?php print fix_price($user->user_money); ?>元</strong>]  </label>

                            </li>

                            <li id="tr_alipay">
                                <input type="radio" id="buy_method_group_alipay" name="buy_method_chk" value="alipay" class="radioType" onclick="javascript:use_alipay();" />
                                <label for="buy_method_group_alipay"><img src="<?php print static_style_url('img/shop_process/img_alipay.png'); ?>" alt="alipay" /></label>
                            </li>

                        </ul>

                        <div class="pay_way">

                            <ul class="methodList">
                                <li id="tr_unionpay">
                                    <input type="radio" id="buy_method_group_unionpay" name="buy_method_chk" value="unionpay" class="radioType" onclick="javascript:use_unionpay();" />
                                    <label for="buy_method_group_unionpay"><img src="<?php print static_style_url('img/shop_process/img_unionpay.png'); ?>" alt="unionpay" /></label>
                                </li>
                            </ul>


                            <input type="hidden" id="last_payway_online_tab" value="0" />

                            <!-- Tab3 Section -->

                            <!-- Tab2 Section -->
                            <div class="section deactive" id="bank_list">
                                <div class="alipay">
                                    <div class="selected">
                                        <label>选择银行</label><span class="current" id="current_bank_code" style="display: none;"></span>
                                    </div>
                                    <ul class="methodList2">
                                        <?php foreach ($alipay_bank_list as $bank): ?>
                                            <li>
                                                <input type="radio" id="bank_code_<?php print $bank['pay_code'] ?>" name="bank_code" class="radioType" value="<?php print $bank['pay_code'] ?>" onclick="select_bank();" />
                                                <label for="bank_code_<?php print $bank['pay_code'] ?>"><img src="<?php print static_style_url($bank['pay_logo']); ?>" /></label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                            <!-- Tab1 Section -->

                            <!-- Tab4 Section -->

                        </div>
                        <ul class="methodList1" style="margin-top:11px;">
                            <li id="tr_cod">
                                <input type="radio" name="cod_method" value="cod" class="radioType" disabled="true" />
                                <label for="buy_method_group_alipay"><img src="<?php print static_style_url('img/shop_process/cod.png'); ?>" alt="alipay" /></label>
                                <span style="color: red; font-size: 14px; vertical-align: bottom;"> 暂未开放，敬请期待。</span>
                            </li>
                            <li id="tr_invoice" style="margin-top: 20px;">
                                <span style="color: red; font-size: 12px; "> 发票系统升级中，暂时不能提供发票，敬请谅解。</span>
                            </li>

                        </ul>
                        <div class="amount">
                            <div class="section">
                                <dl id="dl_transaction_fee" style="display:none">
                                    <dt id="dt_transaction_fee" style="display:none"><em class="colOra">(交易手续费)</em></dt>
                                    <dd id="dd_transaction_fee" style="display:none"><strong></strong></dd>
                                    <dt id="dt_pg_discount_fee" style="display:none">支付优惠</dt>
                                    <dd id="dd_pg_discount_fee" style="display:none"><strong></strong></dd>
                                </dl>
                            </div>
                            <div class="section">
                                <dl class="fee">
                                    <dt>待付金额</dt>
                                    <dd>
                                        <strong><strong><span><em id="cart_amount"></em>元</span></strong></strong>
                                    </dd>
                                </dl>
                            </div>
                            <a class="btn_order" href="javascript:void(0);" onclick="submit_cart();" id="goOrder">提交订单</a>
                            <a class="btn_previous" href="/cart" title="Previous Page">前页</a>
                        </div>
                    </div>



                </div>


            </div><!-- END BLOCK: CONTENT -->
        </div>
    </form>
</div>   
<?php include APPPATH . 'views/common/footer.php'; ?>