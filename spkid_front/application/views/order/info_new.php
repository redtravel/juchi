<?php include APPPATH . 'views/common/user_header.php'?>
<link href="<?php echo static_style_url('pc/css/personal.css?v=version')?>" rel="stylesheet" type="text/css">
               
<div class="personal-center-right">                   
    <div class="order-details">
        <h1 class="order-details-bt">我的订单</h1>
        <div class="order-base">
            <div class="order-details-head">
                  <div class="order-details-zt">
                      <span class="order-no">订单编号：<?php print $order->order_sn ?></span>
                      <span class="state"><?php print order_status($order); ?></span>
                  </div>
            </div>
            <div class="order-chart">
                <?php $step = ' order-step1';
		$payment_time = '';
                $step_html = '<li>'.$order->create_date.'</li>';
                if($order->order_status != 4) {
                    if (!empty($order_payment[count($order_payment)-1]->payment_date) && $order->unpay_price <= 0){
                        $step .= ' order-step2';
			$step_html .= '<li>'.$order_payment[count($order_payment)-1]->payment_date.'</li>';
                    }
                    if (!empty($order->qc_date) && $order->qc_date != '0000-00-00 00:00:00'){
                        $step .= ' order-step3';
                        $step_html .= '<li>'.$order->qc_date.'</li>';
                    }
                    if (!empty($order->is_ok_date) && $order->is_ok_date != '0000-00-00 00:00:00'){
                        $step .= ' order-step4';
                        $step_html .= '<li>'.$order->is_ok_date.'</li>';
                    }
                }
                ?>
                 <div class="usual-flow step<?=$step?>"></div>
                 <ul class="usual-flow-time clearfix">
                 <?=$step_html?>
                 </ul>
            </div>
        </div>
    </div> 

     <div class="order-details">
          <h1 class="order-details-bt">订单跟踪</h1>
          <div class="order-base">
              <div class="order-details-head">
                    <div class="order-details-zr">
                        <span class="operat-time">物流公司</span>
                        <span class="operat-infor">运单号</span>
                    </div>
              </div>
              <div class="order-chart">
                   <ul class="order-tracking">
                   <li><?=$order->shipping_name?><span><?=$order->invoice_no?></span></li>
                   </ul>
              </div>
         </div>
                         </div> 

                         <div class="order-details">
                           <h1 class="order-details-bt">收货信息</h1>
                           <ul class="order-content">
                                   <li><span class="info-title"><em class="space-name">收货</em>人：</span><?php print $order->consignee ?></li>
                                   <li><span class="info-title">手机号码：</span><?php echo $order->mobile?></li>
                                   <li><span class="info-title fl-left">收货地址：</span><span class="addr-detail"><?php echo "{$order->province_name} {$order->city_name} {$order->district_name} {$order->address}"?></span></li>
                           </ul>
                         </div>

                         <div class="order-details">
                             <h1 class="order-details-bt">发票信息</h1>
                             <ul class="order-content">
                                 <?php if(!empty($order->invoice_title)): ?>
                                 <li><span class="info-title">发票类型：</span>普通发票</li>
                                 <li><span class="info-title">发票抬头：</span><?=$order->invoice_title?></li>
                                 <li><span class="info-title fl-left">发票内容：</span><span class="addr-detail"><?=$order->invoice_content?></span></li>
                                 <?php elseif (!empty($order_advice->advice_content)): 
                                     $order_advice_arr = explode("#", $order_advice->advice_content);
                                 ?>
                                 <li><span class="info-title">发票类型：</span>增值税发票</li>
                                 <li><span class="info-title">姓名：</span><?=str_replace('姓名：', '', $order_advice_arr[0])?></li>
                                 <li><span class="info-title">联系方式：</span><?=str_replace('手机号：', '', $order_advice_arr[1])?></li>
                                 <li><span class="info-title">开票留言：</span><?=str_replace('留言：', '', $order_advice_arr[2])?></li>
                                 <?php endif; ?>
                             </ul>
                         </div>

                         <div class="goods-details">
                                  <div class="goods-details-tit">商品信息</div>
                                  <table border="0" class="commodity-inf">
                                  <thead class="commodity-bt">
                                          <tr>
                                                 <td width="60%" align="center">商品信息</td>
                                                 <td width="14%" align="center">单价</td>
                                                 <td width="12%" align="center">数量</td>
                                                 <td width="14%" align="center">小计</td>
                                           </tr>
                                   </thead>

                                   <tfoot class="commodity-bottom">
                                       <?php foreach ($product_list as $p): ?>
                                           <tr>
                                                 <td>
                                                 <a href="/prodetail-<?=$p->product_id?>.html" target="_blank" class="my-order-of">
                                                 <div class="center-pic"><img src="<?php print img_url($p->img_url); ?>.85x85.jpg" /></div>
                                                 <div class="center-title"><h3><?php print $p->product_name ?></h3><p><span>颜色：<?php print $p->color_name ?></span><span>规格：<?php print $p->size_name ?></span></p></div>
                                                 </a>
                                                 </td>
                                                 <td width="14%" align="center">￥<?php print $p->product_price ?></td>
                                                 <td width="12%" align="center"><?php print $p->product_num ?></td>
                                                 <td width="14%" align="center">￥<?php print $p->total_price ?></td>
                                           </tr>
                                         <?php endforeach ?>
                                   </tfoot>
                                 </table>
                         </div>

                         <div class="statement clearfix">
                                 <div class="statistics fl-right">
                                           <span class="txt">商品总额：</span>
                                           <span class="money">&yen;<?php echo $order->total_fee; ?></span>
                                           <br>
                                           <span class="txt">(普通快递)运费：</span><span class="freight">&yen;<?php print $order->shipping_fee; ?></span>
                                           <br>
                                          <span class="txt amounted">应付金额：</span>
                                          <span class="payment amounted">&yen;<?php echo number_format($order->unpay_price,2,'.','')?></span>
                            </div>
                           <span class="num fl-right">共 <strong class="buy-count"><?php print $order->product_num ?></strong>件商品</span>
                         </div>
                 </div>     
        </div>    
    </div>
</div>

<?php include_once(APPPATH . "views/common/footer.php");?>