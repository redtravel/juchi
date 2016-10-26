<div class="cl"></div>

<!--添加购物车-->
<div id="add_to_cart_msg" style="display:none;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tan_c">
		<tr>
			<td height="20" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td height="25" colspan="2" align="center" class="font14b"><img src="<?php print static_style_url('img/common/t_png_red.gif') ?>" width="36" height="36" align="absmiddle"/>&nbsp;&nbsp;&nbsp;&nbsp;商品已成功添加到购物袋！</td>
		</tr>
		<tr>
			<td colspan="2" height="70" align="center">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="cart" class="btn_r_82">去结算</a>&nbsp;&nbsp;
				<a href="javascript:void(0)" onclick="lhgDG.cancel();" class="btn_g_78">再逛逛</a>
			</td>
		</tr>
		<tr>
			<td style="font-size:12px;" height="20" colspan="2" align="center" valign="bottom" class="c66">温馨提示：购物袋内商品只能保留20分钟，请尽快支付订单。</td>
		</tr>
	</table>
</div>

<!--添加收藏-->
<div id="add_to_collect_msg" style="display:none;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tan_c">
		<tr><td height="40" colspan="2">&nbsp;</td></tr>
		<tr>
			<td align="center" class="font14b"><img src="<?php print static_style_url('img/common/t_png.gif') ?>" width="36" height="36" align="absmiddle"/>&nbsp;&nbsp;&nbsp;&nbsp;商品已成功添加到收藏夹！</td>
		</tr>
		<tr><td height="56" colspan="2">&nbsp;</td></tr>
		<tr>
			<td align="right" height="20" valign="bottom">
				<a href="user/collection" class="und black">查看收藏夹>></a>
			</td>
		</tr>
	</table>
</div>

<div id="footer">
   <div class="foot">
   	 <div id="weixin">
     	<p></p>
        <span title="<?php print SITE_NAME;?>微信二维码"><?php print SITE_NAME;?>微信二维码</span>
        <h3 title="<?php print SITE_NAME;?>客服热线：4000-021-6161"></h3>
     </div>
     <div></div>
     <div class="foot_left hideText">
     <!--<a hidefocus="true" href="javascript:void(0);" onclick="window.open('/user/show_online', 'onlinewindow', 'height=450, width=600, top=200, left=400, toolbar=no, menubar=no, scrollbars=yes, resizable=yes,location=no, status=no');return false;" target="_blank">
       <img src="" width="228" height="55"/>
       </a>-->
       
     </div>
     <div class="foot_center">
     	<dl id="buyTips">
           <dd><a href="/help-2.html" target="_blank">购买流程</a></dd>
           <dd><a href="/help-431.html" target="_blank">积分说明</a></dd>
           <dd><a href="/help-436.html" target="_blank">会员制度</a></dd>
           <dd><a href="/help-54.html" target="_blank">正品保障</a></dd>
        </dl>
        <dl id="sentWay">
           <dd><a href="/help-355.html" target="_blank">配送规范</a></dd>
           <dd><a href="/help-354.html" target="_blank">商品签收</a></dd>
           <dd><a href="/help-22.html" target="_blank">配送费用</a></dd>
        </dl>
        <dl id="payWay">
           <dd><a href="/help-730.html" target="_blank">网上支付</a></dd>
           <dd><a href="/help-732.html" target="_blank">现金券支付</a></dd>
           <dd><a href="/help-733.html" target="_blank">账户支付</a></dd>
        </dl>
        <dl id="saleSev">
           <dd><a href="/help-369.html" target="_blank">退货政策</a></dd>
           <dd><a href="/help-13.html" target="_blank">退货流程</a></dd>
        </dl>
        <dl id="selfSev">
           <dd><a href="/help-734.html" target="_blank">绑定手机/邮箱</a></dd>
           <dd><a href="/help-432.html" target="_blank">联系客服</a></dd>
           <dd><a href="/help-434.html" target="_blank">订单管理</a></dd>
           <dd><a href="/help-9.html" target="_blank">常见问题</a></dd>
        </dl>	
     </div>
     <div class="foot_right">
        <ul id="rightList">
        	<!--<li id="voteFriend"><a href="#" class="lBlack" target="_blank">邀请好友</a></li>-->
            <li id="tweibo"><a target="_blank" class="lBlack" href="http://weibo.com/baobeigou">官方微博</a></li>
            <li id="qqMama"><a target="_blank" class="lBlack" href="/zhuanti/mama.html">妈妈交流群</a></li>
            <li style="height:18px; line-height:18px; margin-top:23px;" class="caaa">想了解特卖信息</li>
            <li><input type="text" value="请输入邮箱或手机" class="mainFInput" name="" id="foot_notice_input"><span class="mainFOrder" id="mainFOrder">订阅</span></li>
            <li id="msgForOrderLi"><span style="display:none" class="fred" id="footInputInfo">请输入有效的手机或邮箱地址</span><a target="_self" class="lBlack" id="mainBtnCancel" href="javascript:;">取消订阅</a></li>
        </ul>
     </div>
     
   </div>
   <div class="icon">
         <ul>
            <li><span title="正品保障" id="zpbz"></span></li>
            <li><span title="开箱验货" id="kxyh"></span></li>
            <li><span title="7天保障" id="qidays"></span></li>
            <li><span title="货到付款" id="kdfk"></span></li>
            <li><span title="免运费" id="freeSent"></span></li>
            <li><span title="积分抵现金" id="jfdxj"></span></li>
         </ul>
   </div>
   <div class="f_us lBlack">
    <a target="_blank" href="/article-39.html"><?php print SITE_NAME;?>简介</a> | <a target="_blank" href="/article-48.html">友情链接</a> | <a target="_blank" href="/article-72.html">联系我们</a> | <a target="_blank" href="/article-41.html">招贤纳士</a>  | <a target="_blank" href="/help-70.html">帮助中心</a>
   </div>
     <div class="f_us">
        <span class="caaa">Copyright <font style="font-family:Arial;">&copy;</font> redtravel.cn 2011-2013 </span><a target="_blank" href="http://www.miibeian.gov.cn" class="lGray">沪ICP备XXXX号-1</a>
     </div>
     <div style="display:none;" class="basicTail">
     	<a href=""><img src="<?=static_style_url('/img/toptail/bWeb.png')?>"></a>
        <a href=""><img src="<?=static_style_url('/img/toptail/eWeb.png')?>"></a>
        <a href=""><img src="<?=static_style_url('/img/toptail/payShop.png')?>"></a>
        <a href=""><img src="<?=static_style_url('/img/toptail/shgs.png')?>"></a>
        <a href=""><img src="<?=static_style_url('/img/toptail/360Web.png')?>"></a>
        <a href=""><img src="<?=static_style_url('/img/toptail/etaoWeb.png')?>"></a>
     </div>
     <div style="height:35px; line-height:35px;" class="basicTail">
        <a title="上海网络警察" target="_blank" href="http://sh.cyberpolice.cn/infoCategoryListAction.do?act=initjpg"><img border="0" title="上海网络警察" src="<?=static_style_url('/img/toptail/shNetCap.jpg')?>"></a>
        <a target="_blank" title="网络社会诚信网" href="http://www.zx110.org/"><img title="网络社会诚信网" src="<?=static_style_url('/img/toptail/netSciHone.jpg')?>"></a>
        <a target="_blank" title="互联网违法和不良信息举报平台" href="http://www.net.china.com.cn/index.htm"><img title="互联网违法和不良信息举报平台" src="<?=static_style_url('/img/toptail/3wInfo.jpg')?>"></a>
        <a target="_blank" href="https://search.szfw.org/cert/l/CX20130509002870002586" id="___szfw_logo___"><img title="诚信网站" src="<?=static_style_url('/img/toptail/honest.jpg')?>"></a>
        <a title="一淘合作商家" target="_blank" href="http://s.etao.com/search?spm=1002.8.0.89.qyyBFd&amp;q=%B1%A6%B1%B4%B9%BA%B9%D9%CD%F8&amp;initiative_id=setao_20130428&amp;usearch=yes&amp;fseller=%B1%A6%B1%B4%B9%BA"><img alt="一淘合作商家" src="<?=static_style_url('/img/toptail/etaoWeb.png')?>"></a>
        <a title="放心消费网站" target="_blank" href="http://kuaitousu.com/union/76.html"><img border="0" src="<?=static_style_url('/img/toptail/fangxinxiaofei.png')?>"></a>
    
     </div>
</div>

<!--返回顶部-->
<div id="top_lay">
    <a class="toTop_800" href="javascript:return false;" onclick="NTKF.im_openInPageChat('','');"></a>
    <a class="toTop_time">客服时间<br />周一~周五<br />(9:00-19:00)<br />周六~周日<br />(10:00-19:00)</a>
    <a class="toTop_btn"></a>
</div>
<!--取消订阅弹窗 开始 -->
  <div id="cancelOrderMsg" class="greenBox" style="display:none;">
    <div class="greenBoxTop">
    	<h3 id="greenBoxTitle">取消订阅</h3>
        <span id="closeCancelOrderMsg" class="closeGreenWindow"></span>
    </div>
    <div class="mainAlertBody">
      <form>
        <div class="mainNoticeBox">
          <p> <b>取消手机订阅：</b>
            <input id="cancelInputCall" class="orderInput" type="text" value='请输入手机号码'>
          </p>
          <p> <b>取消邮件订阅：</b>
            <input id="cancelInputMail" class="orderInput" type="text" value='请输入您的邮箱'>
          </p>
          <input type='hidden' id="notice_rush_id" value="0">
        </div>
        <input type="button" id="cancel_notice" value="取消订阅" class="mainBtnCancel">
      </form>
      <p class='mainNoticeWindow'> 温馨提醒：取消订阅后，您将不再收到<?php print SITE_NAME;?>每日限购信息。 </p>
    </div>
  </div>


<!--短信邮件订阅及成功弹窗-->
<div id="add_notice_msg" class="greenBox" style="display:none;">
	<div class="greenBoxTop">
    	<h3 id="greenBoxTitle">订阅成功</h3>
        <span id="closeSucOrderMsg" class="closeGreenWindow"></span>
    </div>
    <div class="greenBoxCon">
    	<div class="iconMsgBox">
        	<b class="iconMsgSuc" id="iconFace"></b>
        	<span id="mainIconMsg" class="mainIconMsg">成功订阅每日限抢信息！</span>
        </div>
    	<span class="tipsInfo" id='notice_tip'>温馨提示：您已经成功订阅了开场通知，无须重复订阅。</span>
    </div>
</div> 

<!--判断载入toTop.js-->
<script stype="text/javascript">
  (function(){
    var localUrl=window.location.href;
    var localStr=localUrl.indexOf("zhuanti");
    if(localStr == -1){
        var newJs=document.createElement("script");
        newJs.type='text/javascript';
        newJs.src="<?=static_style_url("js/toTop.js")?>";
        document.body.appendChild(newJs);
    }
  }());
</script>
<script src="<?=static_style_url("js/validate.js")?>" type="text/javascript"></script>
</body>
</html>
