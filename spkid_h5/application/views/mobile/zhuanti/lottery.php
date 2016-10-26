<?php include APPPATH."views/mobile/header.php"; ?>
<style>
.clearfix:before, .clearfix:after { content:""; display:table; }
.clearfix:after { clear:both; }
.clearfix { zoom:1; /* IE < 8 */ }
.draw-details img{ vertical-align:middle;}

/*活动详情说明样式*/
.draw-public{ width:100%;}
.common{ padding:0 10px;}


.draw-list li{ border-bottom:solid 1px #a8a7a8; padding:10px 0;}
.draw-list strong{ color:#13387b; font-size:1em; padding-right:10px;}
.draw-list span{ font-size:0.8em; color:#5d5a58;}

/*按钮样式*/
.cjxq-but{ padding-top:20px;}
.cjxq-but li{ width:50%; float:left; position:relative; font-size:1.2em;  }
.but-lb{ margin-right:10px; margin-bottom:10px; }
.gongyong{ display:inline-block; border-radius:10px; -webkit-border-radius:10px; -moz-border-radius:10px; color:#fff;  box-shadow:inset 0px 0px 1px #fff; text-align: center; width:100%; height:2.5em; line-height:2.5em;} 
.zcyl{  filter:alpha(opacity=100 finishopacity=50 style=1 startx=0,starty=0,finishx=0,finishy=150) progid:DXImageTransform.Microsoft.gradient(startcolorstr=red,endcolorstr=blue,gradientType=0);	
    -ms-filter:alpha(opacity=100 finishopacity=50 style=1 startx=0,starty=0,finishx=0,finishy=150) progid:DXImageTransform.Microsoft.gradient(startcolorstr=red,endcolorstr=blue,gradientType=0);	
	background:#52bbde; /* 一些不支持背景渐变的浏览器 */  
    background:-moz-linear-gradient(top, #38a4d1, rgba(56, 164, 209, 0.6));  
    background:-webkit-gradient(linear, 0 0, 0 bottom, from(#49aae8), to(rgba(73, 170, 232, 0.6)));  	border:solid 1px #0e7fc6;}

.zcyl-li{ border:solid 1px #01813e; 

filter:alpha(opacity=100 finishopacity=50 style=1 startx=0,starty=0,finishx=0,finishy=150) progid:DXImageTransform.Microsoft.gradient(startcolorstr=red,endcolorstr=blue,gradientType=0);	
    -ms-filter:alpha(opacity=100 finishopacity=50 style=1 startx=0,starty=0,finishx=0,finishy=150) progid:DXImageTransform.Microsoft.gradient(startcolorstr=red,endcolorstr=blue,gradientType=0);	
	background:#72bc4d; /* 一些不支持背景渐变的浏览器 */  
    background:-moz-linear-gradient(top, #55a634, rgba(85, 166, 52, 0.6));  
    background:-webkit-gradient(linear, 0 0, 0 bottom, from(#7ebf41), to(rgba(126, 191, 65, 0.6))); 


}

.zjym{  border:solid 1px #9e71aa; 

filter:alpha(opacity=100 finishopacity=50 style=1 startx=0,starty=0,finishx=0,finishy=150) progid:DXImageTransform.Microsoft.gradient(startcolorstr=red,endcolorstr=blue,gradientType=0);	
    -ms-filter:alpha(opacity=100 finishopacity=50 style=1 startx=0,starty=0,finishx=0,finishy=150) progid:DXImageTransform.Microsoft.gradient(startcolorstr=red,endcolorstr=blue,gradientType=0);	
	background:#a674b0; /* 一些不支持背景渐变的浏览器 */  
    background:-moz-linear-gradient(top, #8b5797, rgba(139, 87, 151, 0.5));  
    background:-webkit-gradient(linear, 0 0, 0 bottom, from(#9e71aa), to(rgba(158, 113, 170, 0.5))); 

}

.gongyong span{ padding-left:20px;}

.gg-xin{ height:25px; width:25px; position:absolute; top:12px; left:8px; background-size:25px auto; }
.public-xin{ background:url(<?php echo static_style_url('mobile/img/zcyl-xin.png')?>) no-repeat center center; }
.public-xin2{ background:url(<?php echo static_style_url('mobile/img/zcyl-xin2.png')?>) no-repeat center center;  }
.zjym-xin{ background:url(<?php echo static_style_url('mobile/img/zjym-xin.png')?>) no-repeat center center;}
.ldrb-xin{ background:url(<?php echo static_style_url('mobile/img/ldrb-xin.png')?>) no-repeat center center;}

.ldrb{  border:solid 1px #e77400; display:inline-block; border-radius:10px; -webkit-border-radius:10px; -moz-border-radius:10px; color:#fff;  box-shadow:inset 0px 0px 1px #fff; text-align: center; width:100%; line-height:4em;  font-size:0.6em; 

filter:alpha(opacity=100 finishopacity=50 style=1 startx=0,starty=0,finishx=0,finishy=150) progid:DXImageTransform.Microsoft.gradient(startcolorstr=red,endcolorstr=blue,gradientType=0);	
    -ms-filter:alpha(opacity=100 finishopacity=50 style=1 startx=0,starty=0,finishx=0,finishy=150) progid:DXImageTransform.Microsoft.gradient(startcolorstr=red,endcolorstr=blue,gradientType=0);	
	background:#f18d00; /* 一些不支持背景渐变的浏览器 */  
    background:-moz-linear-gradient(top, #e96800, rgba(233, 104, 0, 0.7));  
    background:-webkit-gradient(linear, 0 0, 0 bottom, from(#f4a100), to(rgba(244, 161, 0, 0.7)));
}


/*活动规则样式*/
.hdgz,.saosao, .zhoangjiang{ text-align:center;}
.hd-but{ width:100px; background-color:#eef7fc; border-raidus:100px; -webkit-border-radius:100px; -moz-border-radius:100px; border:solid 6px #aed6e6; height:75px; box-shadow:inset 0px 0px 1px #84abc2; display:inline-block; color:#000; font-size:1.5em; padding-top:25px; line-height:25px;} 

.cjhd{ background:url(<?php echo static_style_url('mobile/img/huodong.jpg')?>) no-repeat center center; background-size:190px auto; width:230px; height:84px; display:inline-block; }

.code li{ width:25%; float:left; font-size:0.5em; text-align:center;}
.code span{ display:block;}
.share_modal{ Padding-top:10px; margin-bottom:10px; }

.fxyl{ background:url(<?php echo static_style_url('mobile/img/fxyl.jpg')?>) no-repeat center center; background-size:190px auto; height:100px; text-align:center;}
.jiang-lb strong{ color:#474342; font-size:1em; display:block;}
.jiang-lb span{ color:#de2221; font-size:1.2em; font-weight:bold; color:#524e4dl; padding:0 3px;}
.code2{ margin-top:20px;}
.saosao{ background:url(<?php echo static_style_url('mobile/img/fwh.jpg')?>) no-repeat center center; background-size:240px auto; height:25px; margin-top:50px; }
.saosao p{ font-size:1em; color:#3b3738;}
.code2 li{ float:left; width:50%;}
.code2 p{ text-align: center;}
.tck-public{ padding:10px;}

.fanhui-hu{ position:absolute; top:20px; right:10px;}

</style>
<div class="views">
<div class="view view-main" data-page="index">
     <div class="pages">
<!-- 抽奖详情页 -->
<div class="page <?php if('lottery' != $cur_page) echo 'cached'?>" data-page="lottery">
    <!--navbar start-->
    <div class="navbar item-hide">
        <div class="navbar-inner">
            <div class="left"><a class="link icon-only back" href="#"> <i class="icon back"></i></a></div>
            <div class="center">&nbsp;</div>
        </div>
    </div>
    <!--navbar end-->
    <div class="page-content" style="padding-top:0;">
         <div class="draw-details"><img src="<?php echo static_style_url('mobile/img/hdxqy-pic.jpg')?>" width="100%" /></div>
         <div class="draw-public">
              <div class="common">
                   <ul class="draw-list">
                   <li><strong>参与人员</strong><span>11月16日15:00之前在演示站移动端注册，并完善个人信息的所有新老会员。</span></li>
                   <li><strong>开奖时间</strong><span>11月16日下午16：00在演示站牙医朋友QQ 群（群号：463540166），全程直播抽奖，记得到时来围观哦。<span></li>
                   <li><strong>兑奖方式</strong><span>奖品在三个工作日内寄出。同等级奖项的奖品将随机寄送。<span></li>
                   </ul>
                   
                   <div class="cjxq-but clearfix">
                       <ul>
                       <li>
                           <div class="but-lb open-popover" data-popover=".popover-gift">
                               <a href="#" class="zcyl gongyong"><span>注册有"礼"</span></a><div class="public-xin  gg-xin"></div>
                           </div>
                        </li>
                        <li>
                             <div class="but-lb open-popover" data-popover=".popover-profit">
                             <a href="#" class="zcyl-li gongyong"><span>注册有"利"</span></a><div class="public-xin2 gg-xin"></div>
                             </div>
                        </li>
                        <li>
                             <div class="but-lb open-popover" data-popover=".popover-face">
                             <a href="#" class="zjym gongyong"><span>中奖有"面"</span></a><div class="zjym-xin  gg-xin"></div>
                             </div>
                        </li>
                        <li>
                               <div class="but-lb open-popover" data-popover=".popover-yule">
                               <a href="#" class="ldrb"><span style="padding:12px 0 12px 30px">礼多人不怪授人以"娱"</span></a><div class="ldrb-xin gg-xin"></div>
                               </div>
                        </li>
                       </ul>
                   </div>
                   
                   <div class="hdgz"><a href="#" class="hd-but open-popover"  data-popover=".popover-rule">活动<br/>规则</a></div>
                   <div class="hdgz"><a href="#" class="cjhd share-btn"></a></div>
                   
                   <ul class="code clearfix">
                   <li><span>演示站服务</span><img src="<?php echo static_style_url('mobile/img/wechat-fwh.jpg')?>" width="100%" /></li>
                   <li><span>萌妹子悦悦微信</span><img src="<?php echo static_style_url('mobile/img/mmz-yy.jpg')?>" width="100%" /></li>
                   <li><span>牙医QQ群</span><img src="<?php echo static_style_url('mobile/img/QQ-group.png')?>" width="100%" /></li>
                   <li><span>微博演示站</span><img src="<?php echo static_style_url('mobile/img/weibo-ewm.png')?>" width="100%" /></li>
                   </ul>
                   
                   
                   
              
              </div>
         
         </div>
         
         
  </div>
</div>


<!-- 抽奖页面 -->
<div class="page <?php if('share' !== $cur_page) echo 'cached'?>" data-page="share">
    <!--navbar start-->
    <div class="navbar item-hide">
        <div class="navbar-inner">
            <div class="left"><a class="link icon-only back" href="#"> <i class="icon back"></i></a></div>
            <div class="center">&nbsp;</div>
        </div>
    </div>
    <!--navbar end-->
    <div class="page-content" style="padding-top:0">
         <div class="draw-details">
             <img src="<?php echo static_style_url('mobile/img/jiang-img.jpg')?>" width="100%" />
             <div class="hu-fanhuis fanhui-hu"><a href="/" class="hongme-ico external"></a></div>
         </div>
         <div class="draw-public">
              <div class="common">
                   <div class="fxyl"></div>
                   <div class="jiang-lb"><strong>好东西要和朋友分享哦！朋友中奖，截图发到（萌妹子悦悦微信号yueyawang_V）</strong>演示站再送你<span>5000</span>积分，到你的个人中心账号！</div>
<div class="share_modal hcenter">
			<div class="jiathis_style_32x32">
				<a class="jiathis_button_weixin"></a>
				<a class="jiathis_button_qzone"></a>
				<a class="jiathis_button_cqq"></a>
				<a class="jiathis_button_tsina"></a>
			</div>
		</div>

                   <div class="saosao"></div>
                   <p class = "zhoangjiang">中奖信息早知道，更多精彩等你来！</p>
                   <ul class="code2">
                   <li><img src="<?php echo static_style_url('mobile/img/wechat-fwh.jpg')?>" width="100%" /><p>演示站服务号</p></li>
                   <li><img src="<?php echo static_style_url('mobile/img/mmz-yy.jpg')?>" width="100%" /><p>萌妹子 悦悦微信号</p></li>
                   </ul>
              </div>
         
         
         </div>
         
    </div>

</div><!--page-->
     </div>
	</div>

</div>
<div class="popover popover-gift">
<div class="popover-angle"></div>
<div class="popover-inner tck-public">
     送个人中心专属空间，当天注册，当天拥有。世界很大，但我只想要一个属于我自己的温暖空间。送500积分，可以在演示站上消费使用，当天注册，当天送礼。
</div>
</div>
<div class="popover popover-rule">
<div class="popover-angle"></div>
<div class="popover-inner tck-public">
     <ul>
     <li>1、抽奖方式：电脑随机抽奖，允许从三等奖一直中到特等奖为止，如果这个月运气不错，赶紧注册啊。万一都中了呢? </li>
     <li>2、抽奖时间：11月16日，时间好特殊哟，是“要要一路发”的意思吗？” </li>
     <li>3、抽奖直播①：11月16日下午16：00在演示站牙医朋友QQ 群（群号：463540166），全程直播抽奖，记得到时来围观哦。</li>
     <li>4、抽奖直播②：11月16日下午16点在演示站新浪微博上直播（演示站微博号：演示站），如果加了关注，第一时间就可以通过新浪微博知道抽奖结果啦。 </li>
     <li>5、抽奖结果通知：演示站微信服务号，演示站微博，萌妹子悦悦都会通知中奖用户。 </li>
     <li>6、奖品将在抽奖后三个工作日内快递。</li>
     </ul>
</div>
</div>

<div class="popover popover-yule">
<div class="popover-angle"></div>
<div class="popover-inner tck-public">
 <p>好东西要和朋友分享哦！朋友中奖，你也有奖。</p> 
 <p>把本条信息或页面链接，发到你的朋友圈或者某个特定朋友，如果你朋友中奖了，无论任何等级的产品，凭你分享至微信朋友圈或朋友个人的截图私信发给我们的萌妹子（悦悦微信号：yueyawang_V），演示站再送你5000积分，到你的个人中心的账户！</p>

</div>
</div>
<div class="popover popover-face">
<div class="popover-angle"></div>
<div class="popover-inner tck-public">
<p>1、官方中奖公告将于11月16日晚上8点，在手机演示站上，演示站微信公众号（演示站）上公布。赶紧关注演示站公众号哦，中奖消息早知道。 </p>
<p>2、中奖公告中会加每个中奖人的头像，姓名，手机号末四位，你一看就知道自己中奖。可以做分享到朋友圈财运好运转转！</p>
</div>
</div>
<div class="popover popover-profit">
<div class="popover-angle"></div>
<div class="popover-inner tck-public">
     <img src="<?php echo static_style_url('mobile/img/gift.jpg')?>" width="100%" />
</div>
</div>
<?php include APPPATH . "views/mobile/common/footer-js.php";?>
<script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script>
<script>
$$('.share-btn').on('click', function(){
    $$.getJSON('/zhuanti/check_lottery', null, function(data){
        if (data.is_login){
            //跳到分享
            if (data.completed){
                location.reload();
            } else{//个人编辑
                location.href = '/user/profile';
            }
        } else{
            location.href = '/user/login';
            //var url = data.completed?'#share':'/user/profile';
        }
    })
})
</script>
<?php include APPPATH."views/mobile/footer.php"; ?>
