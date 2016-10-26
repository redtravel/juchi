<?php include APPPATH . 'views/common/header.php'?>
<div class="about-us">
    <div class="about-con">
        <p class="course-tit">首页>售后政策</p>
        <ul class="about-list clearfix">
            <li><a href="/about_us/about_us">关于演示站</a></li>
            <li><a href="/about_us/service">服务条款</a></li>
            <li><a href="/about_us/feedback">意见反馈</a></li>
            <li><a href="/about_us/sales_policy" class="about-currt">售后政策</a></li>
            <li><a href="/about_us/team_work">合作咨询</a></li>
            <li><a href="/about_us/join_us">加入我们</a></li>
        </ul>
        <div class="about-lb">
            <div class="sales-pilicy-left">
                <ul class="pilicy-lb">
                    <li data-value="0" class="pilicy-currt">退款说明</li>
                    <li data-value="1">退换货流程</li>
                    <li data-value="2">退换货政策</li>
                    <li data-value="3">投诉处理流程</li>
                    <li data-value="4">资料下载</li>
                </ul> 
            </div>
            <div class="sales-pilicy-right">
                <h3>退款说明</h3>  
                <p>1.所退还的商品返回物流中心，验收合格后，方可进行退款。</p>
                <p>2.退款支持银行转账或返还到您的演示站账户，不支持现金退款。</p>
                <p>3.如订单使用了演示站优惠券，按实际交易额退款。</p>
                <p>4.如订单使用了积分抵扣，则退款时使用的积分返回积分帐户。</p>
                <p>5.折扣券不退还。如果含折扣券的订单，进行了部分退货，则将折扣券的优惠部分均摊到每件商品，根据优惠后的金额进行退款。</p>
                <p>6.发生退货时，所购商品所赠送的积分，演示站将于退款时同步从您的积分账户里扣除。</p>
                <p>7.公司转账或支票支付的订单，需与客服人员确认公司相关信息后进行公司转账,目前只支持原路退回至客户原支付的公司账户中，给您带来的不便请您谅解。</p>
            </div>  
            <div class="sales-pilicy-right" style="display:none;">     
                <h3>退换货流程</h3>
                <img src="<?php echo static_style_url('pc/images/tuihuo.png')?>" alt="退换货流程">
                <p>注意事项：</p>
                <p>1.请您于每天9：00-21：00致电400 990 5920客服热线申请退换货，我们的客服人员会指导您进行正确的退换货操作；</p>
                <p>2.如发生退货，请将相关促销赠品等一并退回；演示站将同时收回您购买此商品所获的相应积分；</p>
                <p>3.所有退换货须经演示站客服中心确认后方可退回；</p>
                <p>4.在不违反国家相关法律法规的前提下，演示站对商品退换原则保留最终解释权，感谢您对演示站的支持！</p>
            </div>  
            <div class="sales-pilicy-right" style="display:none;">     
                <h3>退换货政策</h3>  
                <p>退换货原则：</p>
                <p>如您在演示站购买的商品出现质量问题，请自售出之日起的15天内，致电演示站客服中心说明情况，并准备好发票、退换货登记表等单据，并确保商品原件、配件、包装、说明书等完整无缺。</p>
                <p></p>
                <p>退换货规则：</p>
                <img src="<?php echo static_style_url('pc/images/tuihuan.png')?>" alt="退换货规则">
                <p></p>
                <p>以下情况恕不提供退换货：</p>
                <p>1.  超过退换货有效期商品；</p>
                <p>2.  如商品确有质量问题，否则我们销售的商品包装一经拆封将不予退换；</p>
                <p> 3.  如商品并非演示站提供或所退商品批号、型号与售出时不符将不予退换；</p>
                <p></p>
                <p>温馨提示：</p>
                <p> 1.  为了保护您的权益，建议您不要委托他人代为签收；</p>
                <p> 2.  如您验收商品时发现商品短缺、配送错误、包装破损、商品破损、存在表面质量等问题，您可以拒收全部商品。相关的赠品，配件或捆绑商品请一起当场拒收。</p>
                <p>3.  目前物流中心暂不接收平邮；</p>
            </div> 
            <div class="sales-pilicy-right" style="display:none;">     
                <h3>投诉处理流程</h3>
                <img src="<?php echo static_style_url('pc/images/tousu.png')?>" alt="投诉处理流程">
            </div>  
            <div class="sales-pilicy-right" style="display:none;">     
                <h3>资料下载</h3>  
                <p><span>马尼宣传册</span> 百度云链接: <a href="http://pan.baidu.com/s/1qWI4w0G" target="_blank">http://pan.baidu.com/s/1qWI4w0G</a></p>
                <p><span>马尼车针图谱及针锉图谱</span> 百度云链接: <a href="http://pan.baidu.com/s/1jGkQCAi" target="_blank">http://pan.baidu.com/s/1jGkQCAi</a></p>
                <p><span>上海康桥齿科医械厂产品目录</span> 百度云链接: <a href="http://pan.baidu.com/s/1bn7z41h" target="_blank">http://pan.baidu.com/s/1bn7z41h</a></p>
            </div> 
        </div>
    </div>
</div>

<script>
    $(".pilicy-lb li").bind("click", function () {
        $(".pilicy-lb li").removeClass("pilicy-currt");
        $(this).addClass("pilicy-currt");
        var i = $(this).attr("data-value");
        $(".sales-pilicy-right").hide();
        $(".sales-pilicy-right:eq(" + i + ")").show();
    });
</script>
<?php include APPPATH . 'views/common/footer.php'?>
