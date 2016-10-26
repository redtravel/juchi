<?php include APPPATH."views/mobile/header.php"; ?>
    <link rel="stylesheet" href="<?php echo static_style_url('mobile/css/yyw-app.css')?>">
    <link rel="stylesheet" href="<?php echo static_style_url('mobile/css/tank.css')?>">
    <div class="views">
    <!-- 演示站商城-->
        <div class="view view-main" data-page="index">
            <div class="pages">
                <div data-name="order-list" data-page="order-list" class="page no-toolbar">
                    <div class="navbar">
                        <div class="navbar-inner">
                            <div class="left"><a href="#" class="link back"><i class="icon icon-back"></i></a></div>
                            <div class="center">我的订单</div>
                        </div>
                    </div>
                    <div class="page-content article-bg2">
                        <div class="content-block article-video">
                            <div class="buttons-row">
                                <a href="#type1" class="tab-link active button button-secondary">所有</a>
                                <a href="#type2" class="tab-link button button-secondary">待付款</a>
                                <a href="#type3" class="tab-link button button-secondary">待发货</a>
                            </div>
                            <div class="tabs">
                                <div id="type1" class="tab active">
                                    <?php foreach($all as $order):?>
                                    <div class="order-list ">
                                        <div class="ov-h ph20 clearfix">
                                            <div class="fl">订单号:<a href="/order/info/<?php echo $order->order_id ?>" class="external"><?php echo $order->order_sn?></a></div>
                                    	    <div class="fr color-red"><?php echo order_status($order);?></div>
                                        </div>
                                    </div>
                                    <div class="hu-wddds">
                                        <div class="hu-ddfk">
                                            共<i><?php echo $order->product_num?></i>件商品
                                            <em>实付款:</em>
                                            <span class="guanzhu-jiage">&yen;
                                                <?php 
                                                    if ($order->can_pay){
                                                        echo number_format($order->total_fee - $order->paid_price,2,'.','');
                                                    }else {
                                                        echo number_format($order->paid_price,2,'.','');
                                                    }
                                                ?>
                                            </span> 
                                        </div>
                                        <!--<span><a href="#" class="submit-order btn btn-shine">查看物流</a></span>-->
                                        <footer class="dd-line">
                                        <!--<a href="" onclick="cancelOrder(<?php echo $order->order_id ?>)" class="cancel-order btn btn-box">取消订单</a>-->
                                        <?php if(order_status($order) !== "已作废"){ ?>                              
                                    	   <?php if ($order->can_pay): ?><a href="/order/pay/<?php print $order->order_id ?>" class="submit-order2 btn btn-shine external">付款</a><?php endif?>
                                        <?php } ?>
                                        </footer>
                                    </div>
                                    <?php endforeach?>
                                </div>
                                <div id="type2" class="tab">
                                    <?php foreach($pending as $order):?>
                                    <div class="order-list ">
                                        <div class="ov-h ph20 clearfix">
                                            <div class="fl">订单号:<a href="/order/info/<?php echo $order->order_id ?>"><?php echo $order->order_sn?></a></div>
                                    	    <div class="fr color-red"><?php echo order_status($order);?></div>
                                        </div>
                                    </div>
                                    <div class="hu-wddds">
                                        <div class="hu-ddfk">
                                            共<i><?php echo $order->product_num?></i>件商品<em>实付款:</em><span class="guanzhu-jiage">&yen;<?php echo number_format($order->total_fee - $order->paid_price,2,'.','')?></span>
                                        </div>
                                        <footer class="dd-line">
                                            <?php if ($order->is_ok): ?><a href="#" data-id="<?php echo $order->order_id ?>" class="cancel-order btn btn-box">取消订单</a><?php endif?>
                                    	    <?php if ($order->can_pay): ?><a href="/order/pay/<?php print $order->order_id ?>" class="submit-order2 btn btn-shine external">付款</a><?php endif?>
                                        </footer>
                                    </div>
                                    <?php endforeach?>
                                </div>
                                <div id="type3" class="tab">
                                    <?php foreach($wait_shipping as $order):?>
                                    <div class="order-list ">
                                        <div class="ov-h ph20 clearfix">
                                            <div class="fl">订单号:<a href="/order/info/<?php print $order->order_id ?>"><?php echo $order->order_sn?></a></div>
                                    	    <div class="fr color-red"><?php echo order_status($order);?></div>
                                        </div>
                                    </div>
                                    <div class="hu-wddds">
                                        <div class="hu-ddfk">
                                            共<i><?php echo $order->product_num?></i>件商品<em>实付款:</em><span class="guanzhu-jiage">&yen;<?php echo number_format($order->paid_price,2,'.','')?></span>
                                        </div>
                                        <footer class="dd-line">
                                            <?php if ($order->is_ok): ?><a href="" class="cancel-order btn btn-box">取消订单</a><?php endif?>
                                    	    <?php if ($order->can_pay): ?><a href="/order/pay/<?php print $order->order_id ?>" class="submit-order2 btn btn-shine external">付款</a><?php endif?>
                                        </footer>
                                    </div>
                                    <?php endforeach?>
                                </div>
                            </div><!--tabs-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
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
    </div><!--page-->
