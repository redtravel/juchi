<?php include APPPATH . "views/common/header.php"; ?>
<div id="page">
    <!-- BLOCK : CONTENT -->
    <script src = "<?php print static_style_url('js/buyOrder.js'); ?>" type = "text/javascript" ></script>
    <div id="container">
        <!-- ********************** PAGE BLOCK: CONTENT ********************* -->
        <div id="content" class="w_980">

            <div id="process03" class="tit_prcss">
                <img src="<?php print static_style_url('img/shop_process/tit_step3_w980.png');?>" alt="STEP2 SHOPPING CART">
            </div>	
            <div class="infoTxt">
                <p>感谢您的使用,订购已完成.</p>
                <p>可在 <a href="/user/order">我的订单</a>中查询和确认订购及运送情况.</p>
                <?php if ($order_amount): ?>
                <p>您还需要在线支付<span style="font-weight: bold; color:#f9221d;"><?php print fix_price($order_amount);?></span>元 <a class="btnToPay external" href="/order/pay/<?php print implode('-', array_keys($order_list)); ?>">立即支付</a></p>
                <?php endif; ?>
            </div>
            <h3 class="top">订购信息 </h3>
            <dl class="orderTable">
                <dt>
                <table class="orderList" summary="This is the goods list.">
                    <colgroup>				        
                        <col width="129px" />
                        <col width="379px" />
                        <col width="120px" />
                        <col width="75px" />
                        <col width="272px" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th scope="col" colspan="2" class="first">
                                <strong>商品名</strong>
                            </th>
                            <th scope="col">价格</th>
                            <th scope="col">数量</th>
                            <th scope="col" class="last">合计</th>
                        </tr>
                    </thead>
                </table>
                </dt>
                <?php foreach ($order_list as $order): ?>
                    <dd class="co_seller">
                        <div class="sellerShop">
                            <dl>
                                <dt><a href="/provider-<?php print $order->provider_id;?>.html" class="sellername" target="_blank"><?php print $order->provider_name; ?></a></dt>

                                <dd><span class="mshop_rate"><dfn style="width:90%;"></dfn></span></dd>

                            </dl>	
                        </div>
                        <div class="seller_sum">	
                            <ul>		
                                <li>合计金额 : <?php print $order->order_price; ?>元</li>		
                                <li>运费 : <?php print fix_price($order->shipping_fee); ?>元</li>		
                                <li class="coupon">优惠 : <?php print fix_price($order->voucher); ?>元 </li>		
                                <li>  </li>		
                                <li class="last">总合计 : <?php print fix_price($order->order_price + $order->shipping_fee - $order->voucher); ?>元</li>	
                            </ul>
                        </div>	
                        <ul class="list"><li class="">
                                <table class="orderList" summary="This is the goods list.">                            
                                    <colgroup>				        
                                        <col width="129px" />
                                        <col width="379px" />
                                        <col width="120px" />
                                        <col width="75px" />
                                        <col width="272px" />
                                    </colgroup>			        
                                    <tbody>
                                        <?php foreach ($order->product_list as $product): ?>
                                            <tr>
                                                <td class="goodsImg">
                                                    <a href="#none"></a><a href="/product-<?php print $product->product_id; ?>.html" title="<?php print $product->product_name; ?>" target="_blank"><img src="<?php print img_url($product->img_url); ?>.85x85.jpg" alt="<?php print $product->product_name; ?>"  width="85" height="85"></a>
                                                </td>
                                                <td class="info">
                                                    <a class="title" href="/product-<?php print $product->product_id; ?>.html" target="_blank"><span class="btn_quick">Quick View</span><?php print $product->product_name; ?></a>
                                                    <p><label style="color:Blue">颜色 : <?php print $product->color_name; ?> / 尺码 : <?php print $product->size_name; ?></label></p>
                                                </td>
                                                <td class="option">
                                                    <p><?php print fix_price($product->product_price); ?>元</p>
                                                </td>
                                                <td class="qty">                            
                                                    <div>
                                                        <div class="edt"><?php print $product->product_num; ?></div>
                                                    </div>
                                                </td>
                                                <td class="subtotal"><strong><?php print fix_price($product->total_price); ?>元</strong></td>
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
                    <span class="allSelect">
                    </span>
                    <span class="button"></span>
                </dd>
            </dl>
            <h3>付款金额</h3>
            <div class="buyTotal">
                <div class="cal_table">
                    <table>
                        <colgroup>
                            <col width="178px" />
                            <col width="28px" />
                            <col width="163px" />
                            <col width="28px" />
                            <col width="170px" />
                            <col width="28px" />
                            <col width="214px" />				        
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
                                <td><strong><span id=""><?php print fix_price($order_price); ?>元</span></strong></td>
                                <td>
                                    <p class="sign_minus">-</p>
                                </td>
                                <td><strong><span id=""><?php print fix_price($voucher); ?>元</span></strong></td>
                                <td>
                                    <p class="sign_plus">+</p>
                                </td>
                                <td><strong><span id=""><?php print fix_price($shipping_fee); ?>元</span></strong></td>
                                <td>
                                    <p class="sign_same">=</p>
                                </td>
                                <td>
                                    <em><strong><span id=""><?php print fix_price($order_price + $shipping_fee - $voucher); ?>元</span></strong></em>

                                    <p class="totalitem">合计商品为<?php print $product_num; ?></p>
                                </td>
                            </tr>
                        </tbody>
                    </table>                    
                </div>
                <div id="CartSummaryDetail" class="details" style="display:none;">
                    <div class="detailArea discount">
                        <div class="statement">
                            <table>
                                <caption>价格及优惠</caption>
                                <tbody><tr>
                                        <th>总计个数</th>
                                        <td>1 (1 商品)</td>
                                    </tr>
                                    <tr>
                                        <th>商品价格</th>
                                        <td><span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TotalOptionPriceTxt">59元</span></td>
                                    </tr>                                
                                </tbody></table>  
                            <table class="discount">                           
                                <tbody><tr>
                                        <th>打折</th>
                                        <td>                                    
                                            <div style=" position:relative;">
                                                <span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TotalCostPriceTxt">0元</span><!--a href="javascript:openDiscountDetail();" class="icon_info">info</a-->
                                                <div style="position:absolute;"><!--iframe--></div>
                                            </div> 
                                        </td>                                   
                                    </tr>     
                                    <tr>
                                        <th>购物车优惠</th>
                                        <td>                                    
                                            <div style=" position:relative;">
                                                <span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TotalPlusCostPriceTxt">0元</span>
                                                <div style="position:absolute;"><!--iframe--></div>
                                            </div> 
                                        </td>                                   
                                    </tr>                           
                                </tbody></table>
                        </div>  
                        <div class="total">
                            <table>                           
                                <tbody><tr>
                                        <th>购买金额</th>
                                        <td><span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TotalPurchaseTxt">59元</span></td>
                                    </tr>                                
                                </tbody></table>
                        </div>
                    </div>
                    <div class="detailArea shipping">
                        <div class="statement">
                            <table>
                                <caption>运送费</caption>
                                <tbody><tr>
                                        <th>运送费</th>
                                        <td>                                    
                                            <div style=" position:relative;">
                                                <span id="ctl00_ctl00_MainContentHolder_MainContentHolder_SubTotalDelveryFeeTxt">10元</span>
                                                <div style="position:absolute;"><!--iframe--></div>
                                            </div> 
                                        </td> 
                                    </tr>
                                    <tr>
                                        <th>附加运送费</th>
                                        <td><span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TotalSubDeliveryFeeTxt">0元</span></td>
                                    </tr>
                                </tbody></table>  
                            <table class="discount">                           
                                <tbody><tr>
                                        <th>打折</th>
                                        <td><span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TotalDeliveryFeeDiscountTxt">0元</span></td>
                                    </tr>                                
                                </tbody></table>
                        </div>  
                        <div class="total">
                            <table>                           
                                <tbody><tr>
                                        <th>运送费</th>
                                        <td>
                                            <div style=" position:relative;">
                                                <span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TotalDeliveryFeeTxt">10元</span><!--a href="javascript:openShippingDetail();" class="icon_info">info</a-->
                                                <div style="position:absolute;"><!--iframe--></div>
                                            </div> 
                                        </td>
                                    </tr>                                
                                </tbody></table>
                        </div>   
                    </div>
                    <div class="detailArea totalSum">
                        <div class="statement">
                            <table>
                                <caption>Total</caption>
                                <tbody><tr>
                                        <th>运送费</th>
                                        <td><span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TotalCartDeliveryFeeTxt">10元</span></td>
                                    </tr>
                                    <tr>
                                        <th>价格及优惠</th>
                                        <td><span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TotalCartTotalPurchaseTxt">59元</span></td>
                                    </tr>                                
                                </tbody></table>                             
                        </div>  
                        <div class="total">
                            <strong><span id="ctl00_ctl00_MainContentHolder_MainContentHolder_TotalPayableAmountTxt">69元</span></strong>

                            <p class="totalitem">合计商品为1</p>
                        </div>       
                    </div>
                </div>
            </div>
        </div><!-- END BLOCK: CONTENT -->

    </div>
</div>
<?php include APPPATH . 'views/common/footer.php'; ?>