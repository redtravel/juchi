<div data-name="course-list" data-page="course-list" class="page no-toolbar">
<style>
.list-block{ margin:0;}
</style>
<div class="navbar">
     <div class="navbar-inner">
          <div class="left"><a href="#" class="link back"><i class="icon icon-back"></i></a></div>
          <div class="center">我的课程</div>
     </div>
</div>

<div class="page-content article-bg2">
    <div class="content-block article-video">

    <!--order-list start--> 
      
 <?php foreach($courses as $order):
        $desc = json_decode($order->product_desc_additional, true);
        unset($order->product_desc_additional);
?>
<div class="hu-kc-list">	  
	  <div class="order-lists">
               <div class="ov-h ph20 clearfix">
                   <div class="fl color-block"><a class="external" href="/order/course_info/<?php print $order->order_id ?>">课程编号: <?php echo $order->order_sn?></a></div>
	          <div class="fr color-red"><?php echo order_status($order);?></div>
               </div>
          </div>
   <!--order-list end--> 
      <div class="list-block">       
      <ul>
   
       <li class="swipeout coll-hu item-content">
            <div class="swipeout-content">
                 <div class="item-inner clearfix">
                   <a href="/product-<?=$order->product_id?>.html" class="external">
                      <div class="col-v-img"><img src="<?php echo img_url($order->img_url);?>" /></div>
                      <div class="item-after">
	                   <span class="public-text2"><?php echo $order->brand_name . ' ' . $order->product_name;?></span>
	                   <span><?php echo $order->subhead;?></span>
	                   <?php if(!empty($desc['desc_waterproof'])):?>
			   <span>
			   <?php echo date("Y-m-d", strtotime($order->package_name));?> - <?php echo $desc['desc_waterproof']?></span>
			   <?php endif;?>
	                   <span><?php echo $desc['desc_crowd'];?></span>
                           <span class="guanzhu-jiage" style="text-align:right;">&yen;<?php echo $order->shop_price?>/人</span>
                      </div>
                   </a>
                 </div>
          </div>
       </li>
      </ul>
      </div>
      
      <div class="hu-wddds">
     <div class="hu-ddfk">
          共报名<i><?php echo $order->product_num?></i>人<em>实付款:</em><span class="guanzhu-jiage">&yen;<?php echo $order->order_price?></span>
     </div>
     <footer class="dd-line" style="padding-right:5px;">
            <?php if (0 == $order->pay_status && 0 == $order->order_status): ?><a href="/order/pay/<?php print $order->order_id ?>" class="submit-order2 btn btn-shine external">付款</a>
	    <a href="" onclick="cancelOrder(<?php echo $order->order_id ?>)" class="cancel-order btn btn-box">取消报名</a>
	    <?php else:?>
	    <?php if(time() <= strtotime($desc['desc_waterproof'])):?>
	    <a href="" class="btn btn-box" style="width:180px; border:solid 2px #BF5964; background-color:#fff; color:#BF5964;">培训正在进行中</a>
	    <!--<a href="" class="cancel-order btn btn-box">删除培训</a>-->
	    <?php endif?>
	    <?php endif?>
     
     </footer>
</div>
 </div>  
<?php endforeach?>
    </div>
</div>
<script type="text/javascript">
//$$('.cancel-order').on('click', function(){
function cancelOrder(order_id){
    //var order_id = $$(self).data('id');

    myApp.confirm('确认取消订单?', function(){
        $$.getJSON('/order/invalid/'+order_id, null, function(data){
            myApp.alert(data.msg, function(){
                if (data.redirect_url){
                    location.reload();
                    //mainView.reloadPage('/user/course');
                }
            });
            //location.href = data.redirect_url;
        })
    }, function(){});
}
</script>
</div>
