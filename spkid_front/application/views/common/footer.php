<!--copyright-start-->
<div class="copyright clearfix">
    <div class="f-product-info clearfix">
        <div class="f-product-left">
            <div class="copyright-tel"><img src="<?php echo static_style_url('pc/images/tel.png')?>"></div>
            <div class="f-info-inner">
                <a target="_blank" href="/about_us/about_us">关于演示站</a>
                <a target="_blank" href="/about_us/service">服务条款</a>
                <a target="_blank" href="/about_us/feedback">意见反馈</a>
                <a target="_blank" href="/about_us/sales_policy">售后政策</a>
                <a target="_blank" href="/about_us/team_work">合作咨询</a>
                <a target="_blank" href="/about_us/join_us">加入我们</a>
            </div>
        </div>
        <div class="f-focus-us">
            <div class="f-focus-icon">
                <span>关注我们:</span>
                <img id="wechat_qrcode" style="display:none; position: absolute; width: 100px; z-index: 99999; top:30px; right:320px;" src="<?php echo static_style_url('pc/images/wechat_qrcode.jpg')?>">
                <a class="c-weibo" target="_blank" href="http://weibo.com/oswelldental"></a>
                <a class="c-weixin" target="_blank" href="javascript:void(0)" onmouseover="var wechat_qrcode = document.getElementById('wechat_qrcode'); wechat_qrcode.style.display='block';" onmouseout="var wechat_qrcode = document.getElementById('wechat_qrcode'); wechat_qrcode.style.display='none';"></a>                    
            </div>
        </div>
    </div>  
</div>    
</body>
<script>
$(function(){  
    var cookies = document.cookie ? document.cookie.split('; ') : [];
    var v_user_name = '';
    var user_advar = '<?php echo static_style_url("mobile/touxiang/")?>';
    for (var i = cookies.length - 1; i >= 0; i--) {
        var item = cookies[i].split('=');
        if (item[0]=='v_user_name') {
            v_user_name = decodeURIComponent(item[1]);
        };
        if (item[0]=='v_advar') {
            user_advar = user_advar + decodeURIComponent(item[1]);
        };
    };

    if (!v_user_name) {
      $('.nav-left').html('<span>您好，欢迎来演示站  (互联网交易许可证：沪B20140002) </span><a href="/user/login" class="yy-login">请登录</a><a href="/user/signin" class="yy-register">免费注册</a>');
      $('.nav-func').html('<a class="nav-gg nav-cart" href="/cart">购物车</a>');
    } else {
        var str = '<img src="' + user_advar + '" height="25">';
        str += '<span class="nav-center"><a href="javascript:void(0);" class="index-user">' + v_user_name + '</a></span>';
        str += '<ul class="center-drop-down" style="display:none">';
        str += '<li><a href="/user/index.html" data-status="1">个人中心</a></li>';
        str += '<li><a href="/user/order_list.html" data-status="2">我的订单</a></li>';
        str += '<li><a href="/collect/index.html" data-status="3">我的关注</a></li>';
        str += '<li><a href="/user/my_response.html" data-status="4">我的回复</a></li>';
        str += '<li><a href="/account/privilege.html" data-status="5">我的优惠</a></li>';    
        str += '<li><a href="/user/logout.html" data-status="6">退出</a></li></ul>';
        $.ajax({
            url:'/cart/get_cart_num',
            dataType:'json',
            type:'POST',
            async: false,
            success:function(result){
                str += '<a class="nav-gg nav-cart" href="/cart">购物车'+result.cart_num+'件</a>';
            }          
        });
        $('.nav-func').html(str);
        $('.nav-left').html('<span>您好，欢迎来演示站  (互联网交易许可证：沪B20140002) </span>');
        $(".index-user").mouseover(function(){
            $(".center-drop-down").show();
            $(this).addClass("test");
        });
          
        $(".index-user").mouseout(function(){
            $(".center-drop-down").hide();
            $(this).removeClass("test");
        });
          
        $(".center-drop-down").mouseover(function(){
            $(".center-drop-down").show();
            $(".index-user").addClass("test");
        });
        $(".center-drop-down").mouseout(function(){
            $(".center-drop-down").hide();
            $(".index-user").removeClass("test");
        });
    }
});
</script>
<?php include_once(APPPATH . "views/common/tongji.php");?>
</html>

