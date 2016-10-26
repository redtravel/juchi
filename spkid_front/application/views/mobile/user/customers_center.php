<?php include APPPATH."views/mobile/header.php"; ?>

<style>
    .content-block {
        padding:0;
    }
</style>
<!-- 留言对话框 -->
<div class="popup popup-my_wentiliuyan">
    <div class="navbar">
        <div class="navbar-inner">
            <div class="center">问题留言</div>
            <div class="right close-popup" style="margin-right:1em">关闭</div>
        </div>
    </div>
    
    <div class="toolbar tabbar">
      <div class="toolbar-inner"><a href="#tab_liuyan" class="active tab-link">我的留言</a><a href="#tab_xunjia" class="tab-link">我的询价</a><a href="#tab_pingjia" class="tab-link">我的评价</a></div>
    </div>
    <div class="tabs-animated-wrap">
      <div class="tabs">
        <div id="tab_liuyan" class="page-content tab active">
          <div class="content-block">
            <?php echo $liuyan_html?>
          </div>
        </div>
        <div id="tab_xunjia" class="page-content tab">
          <div class="content-block">
            <?php echo $xunjia_html?>
          </div>
        </div>
        <div id="tab_pingjia" class="page-content tab">
          <div class="content-block">
            <?php echo $pingjia_html?>
          </div>
        </div>
      </div>
    </div>
    
</div>

<div class="views">
<div class="view index view-main">
<div class="pages">
<div class="page" data-page="help">
    <div class="navbar">
                <div class="navbar-inner">
        <div class="left"><a href="#" class="link back"><i class="icon icon-back"></i></a></div>
                    <div class="center">帮助中心</div>
                </div>
    </div>
    <div class="page-content bg-deepblue">
        <div class="content-block" style="padding-top:10px;">
            <div class="white-area hide" id="details" >
                  <span>一、关于演示站 </span>

                <p style="text-indent:0;"><b>1、演示站是干什么的？</b><br/> 
                    答：演示站是中国首家牙科电商与产品教育平台，开启牙科行业电商新时代；</p>
                <p style="text-indent:0;"><b>2、演示站目标？ </b><br/>
                      答：帮牙科行业的厂家发展，帮中国的牙科诊所发展，帮业内与我们合作的机构以及个人发展，合作共赢，共同发展，实现价值；</p>
                  <p style="text-indent:0;"><b>3、演示站如何让人放心购买？</b> <br/>
                      答：产品证件齐全，保证正品行货, 演示站承诺产品因质量问题，自售出之日（以实际收货日期为准）起7日内可以退货，15日内可以换货, 如有任何问题可与我们客服人员联系，我们会在第一时间跟您沟通处理；</p>
                  <p style="text-indent:0;"><b>4、演示站服务比如何比经销商更好？ </b><br/>
                      答：演示站产品更齐全、价格更有优势、正品行货、发货快捷、提供技术支持（专家教授提供产品技术培训）、交流互动平台、我们将不断提供更多增值服务；</p>
                  <p style="text-indent:0;"><b>5、为何网页产品无法点击浏览，网站产品不全？ </b><br/>
                      答：演示站打造齿科一站式交易平台，各类产品将陆续上线，敬请期待！</p>

            
                  <span style="padding-top:15px; text-indent:0; ">二、关于服务</span>

                  <p style="text-indent:0;"><b>1、全场多少钱包邮？</b><br/> 
                      答：演示站全场满399元包邮； </p>
                <p style="text-indent:0;"><b>2、发票是否正规？ </b><br/>
                      答：演示站提供正规发票，可在支付时选择普票或增票；发货将于订货后14个工作日寄出；</p>
                  <p style="text-indent:0;"><b>3、是否可以货到付款？</b> <br/>
                      答：我司支持货到付款，在结算时将收取相应手续费；推荐您使用在线支付或汇款转账方式进行付款；</p>
                  <p style="text-indent:0;"><b>4、演示站货从哪里来？ </b><br/>
                      答：所有产品均通过正规渠道采购，供应商经过严格筛选，三证齐全；</p>
                  <p style="text-indent:0;"><b>5、设备类产品维修服务如何处理 ？ </b><br/>
                      答：可先行致电演示站客服中心说明情况，我们将与厂家联系进行维修服务或请您在“联系客服”相应页面进行在线提交售后服务申请单，我们的客服人员会尽快审核确认并指导您进行后续处理。</p>
              
            <span style="padding-top:15px; text-indent:0; ">三、关于积分</span>

                  <p style="text-indent:0;"><b>1.演示站积分如何获得？</b><br/> 
                      答：您在线完善个人信息可获得100分；您购买普通商品的金额会以1:1的方式计入演示站积分（1元=1分），购买n倍积分商品则以1:n的方式获得积分（1元=n分）；您参与问卷调查、意见征集（有问必答）等相关活动也将获赠相应的积分。积分
              规则如有变化，将在网站通告，不另行通知。</p>
                <p style="text-indent:0;"><b>2.演示站积分如何使用？</b><br/>
                      答：您购买普通商品时可以100:1的方式使用演示站积分（100分=1元）；您参与积分换购、付费培训等相关活动时也可以使用相应的积分。积分规则如有变化，将在网站通告，不另行通知。</p>
              
              <span style="padding-top:15px; text-indent:0; ">四、关于支付问题</span>

                  <p style="text-indent:0;"><b>1.为什么点支付没反应？</b><br/> 
                      答：这种情况通常是因为浏览器适配的原因。请问您是在什么界面点击支付没反应的呢？如果您的问题出在PC网页界面，请升级更新您当前的浏览器。另外，我们强烈建议您采用谷歌浏览器，可点击以下图标进行百度下载：<br/>
             </p>
             <p style="text-indent:0;">如果您的问题出在手机微信或QQ的浏览器界面，很抱歉！您不能在这些界面使用支付宝进行支付（众所周知的腾讯与支付宝相互屏蔽的恶招），请采用微信支付或在第三方浏览器中使用支付宝支付（如图，点击“在浏览器中打开”）。</p>
                
                  
                  
            
            
 
            </div>
        <!-- <div id="show_details" style="text-align:right; padding-right:5px;">查看更多</div> -->
            
        </div>
    </div>
</div>

<div class="page" data-page="service">
    <div class="navbar">
                <div class="navbar-inner">
        <div class="left"><a href="#" class="link back history-back"><i class="icon icon-back"></i></a></div>
                    <div class="center">客服中心</div>
                </div>
    </div>
    <div class="page-content bg-deepblue">

	<div class="list-block" style="margin-top:20px;">
            <ul class="hu-shezhen">            
            <li><a href="#help" class="item-link item-content"><div class="item-inner"><span class="bzzx-hu"><i style="padding-left:40px; font-style:normal;">帮助中心</i></span></div></a></li>
            <li style="background-color:#063142;" id="kefu"><a href="tel:4009905920" class="item-link item-content external"><div class="item-inner"><span class="hu-hots-line"><i style="padding-left:40px; font-style:normal;">客服热线</i></span>
    	    <span style="padding-left:4em; color:#95959D; font-size:1em; ">400-9905-920</span></div></a>
    	    </li>


            
            <li id="my_wentiliuyan"><a href="" class="item-link item-content"><div class="item-inner"><span class="hu-liuyans"><i style="padding-left:40px; font-style:normal;">问题留言</i></span></div></a>
            </li>
            
            </ul>
	</div>
	
    </div>
</div>
</div><!-- pages -->
</div>
</div>

<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<?php include APPPATH."views/mobile/footer.php";?>

<script>
    $$("#show_details").on('click', function(){
      $$('#details').toggleClass("hide");
    })
    $$('#kefu').on('click', function(e){
        myApp.confirm('服务时间 9:00-21:00', '现在拨打热线', function () {
            $$('<a href="tel://4009905920">拨打电话</a>').click();            
        });
    });

    $$('#my_wentiliuyan').on('click', function(e){
        myApp.popup('.popup-my_wentiliuyan');
    });
    
</script>
