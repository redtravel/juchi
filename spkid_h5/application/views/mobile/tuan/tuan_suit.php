<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no, minimal-ui">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="theme-color" content="#2196f3">
<meta property="qc:admins" content="1606616551551171676375"/>
<title><?php print $package[0]->package_name;?></title>
<meta name="Keywords" content="牙科材料，齿科材料，口腔材料">
<meta name="Description" content="">
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/jquery-1.11.3.min.js?v=version')?>"></script>
<style>
 body, h1, h2, h3, h4, h5, h6, hr, p, blockquote, dl, dt, dd, ul, ol, li, pre, form, fieldset, legend, button, input, textarea, th, td { margin:0; padding:0;}
 body{font: 12px/1.5 arial,宋体; color:#1b1b1b;}
 *:focus {outline: none} 
.clearfix:before, .clearfix:after { content:""; display:table; }
.clearfix:after { clear:both; }
.clearfix { zoom:1; /* IE < 8 */ }
ul,li{ list-style: outside none none;}
input[type="button"], input[type="submit"], input[type="reset"] ,input[type="text"]{-webkit-appearance: none;}
textarea {  -webkit-appearance: none;}
ul,li{    -webkit-margin-before: 0; -webkit-margin-after: 0;}
.activity{ width:100%; background-color:#f7f7f7;}
.activity-img img,.commodity-img img{ width:100%; vertical-align:top;}
.activity-mian{ padding-bottom:80px;}
.related-commodity{ background-color:#fff; margin-top:5px;}
.related-biaoti{ border-bottom:solid 1px #f7f7f7; padding: 6px 0;}
.related-jl{ padding:0 10px;}
.related-bt{ font-size:13px; color:#171717; float:left; font-weight:bold;}
.related-price{ display:block; float:right;}
.related-price{ color:#e77817; font-size:13px;}
.related-price i{ font-size:10px;}
.related-price i,.activity-js i,.footer-price i{ font-family: Arial; font-style:normal;}
.glmc{ border-bottom:solid 1px #f7f7f7; overflow:hidden; padding-bottom:10px; position: relative;}
.related-lb{ padding:5px 10px 0 10px;}
.commodity-img{ float:left; width:30%; position:relative;}
.commodity{ position:relative; color:#333; font-size:12px;}
.commodity-guige{ text-align:center; overflow:hidden;}
.guige{ margin-left:14px; padding-right:106px; margin-top:5px;}

.commodity-guige strong,.commodity-sl strong{ color:#333; font-weight:normal; font-size:12px;}
.guige a{ display:block; color:#999; border-radius:1px; -moz-border-radius:1px; -webkit-border-radius:1px; -o-border-radius:1px; -ms-border-radius:1px; border:solid 1px #999; height:22px; line-height:22px; text-decoration:none; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; padding:0 5px; margin:8px 0;} 
.guige .active{ border:solid 1px #e77817; color:#e77817;}
.quantity-wrapper { height: 21px; position:absolute; right:0; top:8px;}
.quantity-wrapper2{ right:24px; top:0;}

.quantity-decrease, .quantity, .quantity-increase { float: left; font-size: 15px; text-align: center; height: 100%;}
.quantity-decrease, .quantity-increase { background: #fff; border: 1px solid #999;color: #999; display: block; line-height: 24px; width: 24px; overflow: hidden; text-indent: -200px;}
.quantity-decrease em { background:url(<?php echo static_style_url('mobile/tuan/cart-number.png?v=version')?>) no-repeat 0 -18px; background-size: 100%; height: 10px; width: 10px; display: block; margin: 7px;}
.quantity-decrease, .quantity, .quantity-increase {  float: left;  font-size: 15px; text-align: center; height: 100%;}
.quantity { color: #999; border:solid 1px #999; border-width: 1px 0 1px 0; height: 21px; width: 34px; border-radius: 0; -webkit-appearance: none;}
.quantity-increase em {background:url(<?php echo static_style_url('mobile/tuan/cart-number.png?v=version')?>) no-repeat 0 0; background-size: 100%; height: 10px; width: 10px; display: block; margin: 5px;}
.activity-js{ text-align:right; padding:10px 0; color:#1b1b1b; font-size:12px;}
.activity-js i{ padding:0 2px;}
.activity-js span{ padding-left:2px; font-family: Arial;}
.footer{ width:45px;}
.fix-foot{ background-color:#fff; border-top: solid 1px #ccc; width: 100%; height:35px; line-height:35px; bottom: 0; z-index: 900; position: fixed; padding:10px 0;}
.footer-price{ color:#313131;  width:40%; float:left;}
.footer-price span{ color:#e77817; font-size:1.5em;}
.add-cart{ display:inline-block; float:right; width:50%; background-color:#e77817; border-radius:3px; -webkit-border-radius:3px; -moz-border-radius:3px; -ms-border-radius:3px;
 -o-border-radius:3px; text-align:center; border:solid 1px #cb6811; color:#fff; font-size:0.95em; text-decoration:none;}
 .bdr-r { position: relative;}
.bdr-r:after { border-right: 1px solid #b4b4b4; content: ""; height: 100%; position: absolute; right: 12px; top: 0;  width: 1px; z-index: 10;}
.v-readonly-num { border:0px; width: 15px; }
.v-readonly-price{ border:0px; width: 50px; }
.v-shop-price{ border:0px; width: 40px; color: #e77817;}
.v-total-price{ border:0px; width: 78px; color: #e77817; font-size: 1.0em;}
</style>
</head>
<body>

<div class="activity">
    <div class="activity-img"><img src="<?php print img_url($package[0]->package_homepage_image);?>" alt="<?php print $package[0]->package_name;?>"/></div>
    <div class="activity-mian">
    <?php if(isset($suit_product) || !empty($suit_product)) { ?>
    <?php foreach ($suit_product as $val_suit) { ?>
        <div class="related-commodity">
            <div class="related-biaoti">
                <div class="related-jl clearfix">
                    <h1 class="related-bt"><?php print $val_suit['brand_name'].' '.$val_suit['product_name'];?></h1>
                    <span class="related-price"><i>¥</i><input readonly="readonly" class="v-shop-price shop-price" value="<?php print $val_suit['shop_price'];?>"></span>
                </div>
            </div>
            <div class="glmc">
                <div class="related-lb">
                    <div class="commodity-img"><a href="/pdetail-<?php echo $val_suit['product_id']?>.html" class="external"><img src="<?php print img_url($val_suit['img_url']);?>" alt="<?php print $val_suit['brand_name'].' '.$val_suit['product_name'];?>"/></a></div>
                    <div class="commodity-guige">
                        <div class="commodity">
                            <div class="guige">规格</div>
                            <div class="quantity-wrapper quantity-wrapper2">数量(<?php print $val_suit['unit_name'];?>)</div>
                        </div>
                        <?php foreach ($val_suit['size'][0] as $key => $size) { ?>
                        <div class="commodity clearfix">
                            <div class="guige">                               
                                <a ><?php print $size->size_name;?></a>
                            </div>
                            <div class="quantity-wrapper clearfix">
                                <input type="hidden" class="cur_sub_id" value="<?php print $size->sub_id;?>">
                                <input type="hidden" class="cur_sub_num" value="<?php print $size->sale_num;?>">
                                <a class="quantity-decrease btn-minus"><em>-</em></a>
                                <input type="number" class="quantity buy_num" value="0">
                                <a class="quantity-increase btn-plus"><em>+</em></a>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="activity-js v-product-sub">共选择  <input readonly="readonly" class="v-readonly-num product-num" value="0">件商品 合计：<i>¥</i><input readonly="readonly" class="v-readonly-price product-price" value="0.00"></div>
        </div>      
    <?php } ?>
    <?php }else{ ?>
        <div class="related-commodity">
            对不起！该活动暂无套装产品！
        </div>    
    <?php } ?>
    </div> 
    <div class="footer">
        <div class="fix-foot">
            <div class="related-jl">
                <div class="footer-price bdr-r">总价：<span><i>¥</i><input readonly="readonly" class="v-total-price total-price" value="0.00"></span></div>
                <a href="javascript:void(0) " onclick="add_cart()" class="add-cart">加入购物车</a>
            </div>
        </div>
    </div>
</div>
<script>
    //将用户之前选择的商品数量 自动填充
    if(sessionStorage.getItem('v_storage')){
        var sess_storage = sessionStorage.getItem('v_storage');
        sess_storage = sess_storage.split(',');
	$('.product-num').val(0);
        for (var j = 0; j < $('.quantity.buy_num').length; j++) {
            $('.quantity.buy_num').eq(j).val(sess_storage[j]);
            var product_up = parseInt($('.quantity.buy_num').eq(j).parents('.related-commodity').find('.product-num').val())+parseInt(sess_storage[j]);
            $('.quantity.buy_num').eq(j).parents('.related-commodity').find('.product-num').val(product_up);
            var storage_shop_price = parseFloat($('.quantity.buy_num').eq(j).parents('.related-commodity').find('.shop-price').val()) * product_up;
            $('.quantity.buy_num').eq(j).parents('.related-commodity').find('.product-price').val(storage_shop_price.toFixed(2));
            
        };
	
	var total = 0;
	$(".product-price").each(function (e, i) {
	if($(this).val()!=""){
	    total+= parseFloat($(this).val());
	}
	});
	$('.total-price').val(total.toFixed(2));
    }

    function check_buy_num(type,num_input,sale_input,product_sum,product_price,shop_price) {
        
        var buy_num = parseInt(num_input.val());
        var v_max = parseInt(sale_input.val());
        var product_num = parseInt(product_sum.val());
        var price = shop_price.val();

        if (v_max < 1) {
            alert('对不起，该商品已卖光！');
            return;
        }
        switch(type) {
            case '+':
                ++buy_num;
                ++product_num;
            break;
            case '-':
                --buy_num;
                --product_num;
            break;
            break;
            case '':
                product_num = buy_num;
            break;
            default:
            break;
        }


        if (isNaN(buy_num)) {
            buy_num = 1;
        }

        if (buy_num > v_max) {
            alert('对不起，已超出该商品库存！');
            buy_num = v_max;     
        }

        if (buy_num <= 0) {
            buy_num = 0;        
        }

        if (product_num <= 0) {
            product_num = 0;        
        }

        if (price <= 0) {
            price = '0.00';        
        }

        var v_price = (product_num * price).toFixed(2);
        if(isNaN(v_price)){
            alert('对不起，请重新输入');
            location.reload();
        }
        num_input.val(buy_num);
        product_sum.val(product_num);
        product_price.val(v_price);

        var total = 0;
        $(".product-price").each(function (i) {
            if($(this).val()!=""){
                total+= parseFloat($(this).val());
            }
        });

        $('.total-price').val(total.toFixed(2));

    }

    $(document).on('click', '.btn-plus', function(e) {
        var num_input = $(this).parent().find('.buy_num');
        var sale_input = $(this).parent().find('.cur_sub_num');
        var product_sum = $(this).parents('.related-commodity').find('.product-num');
        var product_price = $(this).parents('.related-commodity').find('.product-price');
        var shop_price = $(this).parents('.related-commodity').find('.shop-price');
        check_buy_num('+',num_input,sale_input,product_sum,product_price,shop_price);
    });

    $('.buy_num').on('blur', function(e) {
        var num_input = $(this).parent().find('.buy_num');
        var sale_input = $(this).parent().find('.cur_sub_num');
        var product_sum = $(this).parents('.related-commodity').find('.product-num');
        var product_price = $(this).parents('.related-commodity').find('.product-price');
        var shop_price = $(this).parents('.related-commodity').find('.shop-price');
        check_buy_num('',num_input,sale_input,product_sum,product_price,shop_price);
    });

    $(document).on('click', '.btn-minus', function(e) {
        var num_input = $(this).parent().find('.buy_num');
        var sale_input = $(this).parent().find('.cur_sub_num');
        var product_sum = $(this).parents('.related-commodity').find('.product-num');
        var product_price = $(this).parents('.related-commodity').find('.product-price');
        var shop_price = $(this).parents('.related-commodity').find('.shop-price');
        check_buy_num('-',num_input,sale_input,product_sum,product_price,shop_price);
    });

    function add_cart(){
        var storage = [];
        for(var i = 0; i < $('.quantity.buy_num').length; i++) {
            var storage_arr = ($('.quantity.buy_num').eq(i).val() == '')?0:$('.quantity.buy_num').eq(i).val();
            storage[i] = storage_arr;
        }
        sessionStorage.setItem('v_storage',storage);
        $.ajax({
            async:false,
            type:'POST',
            url:'/user/check_is_login',
            dataType:'json',
            success:function(res){
                if (!res.is_login){
                    is_login=false;
                    location.href='/user/login';
                }else{
                    is_login=true;
                } 
            } 
        });
        if(is_login){
            var sub_ids = '';
            var package_id = '<?php print $package[0]->package_id;?>';
            $('.commodity').each(function(index, ele){
                if(parseInt($(ele).find('.buy_num').val())) {
                    sub_ids += $(ele).find('.cur_sub_id').val() + '=' + $(ele).find('.buy_num').val() + '|';
                }
            });
            if(sub_ids == ''){
                alert('您还没有选择商品数量！');
                return;
            }
            $.ajax({
                    url:'/cart/add_mutlit_subs_to_cart',
                    data:{sub_ids:sub_ids.substr(0, sub_ids.length -1 ), package_id : package_id},
                    dataType:'json',
                    type:'POST',
                    success:function(result){
                        if(result.err == 2){
                            checkLogin(false);
                            return;
                        }
                        location.href = '/cart/';
                        console.log(result.msg);
                    },
                    error:function(err) {
                        console.log(err);
                    }
            });
        }
    }

</script>
<?php
    $appId = 'wxd11be5ecb1367bcf';
    $appsecret = '6d05ab776fd92157d6833e2936d6f17c';

    $timestamp = time();
    $jsapi_ticket = make_ticket($appId,$appsecret);
    $nonceStr = make_nonceStr();
    $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $signature = make_signature($nonceStr,$timestamp,$jsapi_ticket,$url);

?>
<?php if(isReqFromWechat()):?>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript"></script>
<script>
    wx.config({
        debug: false,
        appId: '<?=$appId?>',
        timestamp: <?=$timestamp?>,
        nonceStr: '<?=$nonceStr?>',
        signature: '<?=$signature?>',
        jsApiList: [
            'checkJsApi',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
        'onMenuShareTimeline', 
        'onMenuShareAppMessage'
          ]
       });
       
       wx.ready(function(){
       
        wx.onMenuShareTimeline({
        title: $('title').text(),

        link: "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>",
        imgUrl: $('.activity-img img').attr('src'),
        trigger: function (res) {
        console.log('');
        },
        success: function (res) {
        alert('十分感谢您对演示站的支持！！！');
        },
        cancel: function (res) {
        
        },
        fail: function (res) {
        console.log(JSON.stringify(res));
        }
        });    
        
        wx.onMenuShareAppMessage({
          title: $('title').text(),
          desc: '<?php print $package[0]->package_desc;?>',
          link: "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>",
          imgUrl: $('.activity-img img').attr('src'),
          trigger: function (res) {
            console.log('');
          },
          success: function (res) {
            alert('十分感谢您对演示站的支持！！！');
          },
          cancel: function (res) {
        
          },
          fail: function (res) {
            console.log(JSON.stringify(res));
          }
        });
    
    });
    
    
</script>   
<?php endif;?>
<?php //include APPPATH."views/mobile/common/footer-js.php"; ?>
<?php include APPPATH."views/mobile/footer.php"; ?>
