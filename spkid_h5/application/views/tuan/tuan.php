<?php include APPPATH . "views/common/tuanheader.php"; ?>
<!--[if lt IE 7 ]>
    <script type="text/javascript" src="<?=static_style_url("js/dd_belatedpng.js")?>"></script>
    <script>
        DD_belatedPNG.fix("img, .png_bg");
    </script>
<![endif]-->

<!--团购内容-->
<!--顶部定制广告 开始-->
<div id="allAdBox">
	<span id="btnSwitchAllAd"></span>
    <ul id="boxBtnAllAd">
        <?foreach($top_ad as $key=>$val):?>
    	<li <?=$key==0?"class='activeBtnLiAllAd'":""?>><?=$key+1?></li>
        <?endforeach?>
    </ul>
    <ul id="allBannerUl" class="allBannerUl">
        <?
            $index=0;
            foreach($top_ad as $val):?>
            <li <?=$index==0?"class='active'":""?>>
        	    <a id="allAdUrl<?=$index+1?>" href="<?=$val->ad_link?>" target="_blank" allAdId="s<?=$index+1?>">
                    <img <?=$index==0?'src':'psrc'?>="<?= img_url($val->pic_url)?>" alt=""/>
                </a>
            </li>    
        <?
            $index++;
        endforeach?>
    </ul>
</div>
<!--团购广告图-->
<div id="tuanAdMainBox">
	<a href="<?=$main_ad[0]->ad_link?>" id="AdMainImg" target="_blank"><img src="<?= img_url($main_ad[0]->pic_url)?>" width="338px" height="345px" title="" /></a>
	<div id="tuanAdMainCont" class="main_fRight">
		<h3 class="rushTitle">今日推荐</h3>
		<ul>
			<li>
				<a href="<?=$main_ad[1]->ad_link?>" target="_blank" ><img src="<?= img_url($main_ad[1]->pic_url)?>" width="300px" height="255px" alt="" /> </a>
			</li>
			<li>
				<a href="<?=$main_ad[2]->ad_link?>" target="_blank" ><img src="<?= img_url($main_ad[2]->pic_url)?>" width="300px" height="255px" alt="" /> </a>
			</li>
		</ul>
	</div>
</div>
<!--团购主体开始 -->
<div id="tuanRush" class="tuanBox">
	<p id="rushTitle"></p>
	<div id="tuanRushTab">
		<ul id="tuanRushTabUl">
			<li <? if($sort_type==1) { ?> class="tuanRushTabOn" <? } ?> ><a href="tuan-1-0-0.html" class="sortDown">最新</a></li>
			<li <? if($sort_type==2) { ?> class="tuanRushTabOn" <? } ?> ><a href="tuan-2-0-0.html" class="sortDown">销量</a></li>
			<li id="discount"><span>折扣</span>
				<a href="tuan-3-0-0.html" id="discountUp" <? if($sort_type==3&&$sort==0) { ?> class="discountUp" <? } ?> ></a>
				<a href="tuan-3-1-0.html" id="discountDown" <? if($sort_type==3&&$sort==1) { ?> class="discountDown" <? } ?> ></a>
			</li>
		</ul>
		<div id="tuanForView">
			<p id="btnForView">最近浏览的团购</p>
			<div id="boxForView" style="display:none;">
				<div id="boxForViewContent">
                    <?php if($tuaninfoRec) { ?>
					<ul id="boxForViewUl1"  class="boxForViewUl">
                        <?php
                		foreach ($tuaninfoRec as $key => $val) {
                		?>
						<li>
							<a href="tuanDetail-<?php print $val->tuan_id ?>.html" target="_blank" class="imgForView" title="">
                                <img src="<?php print img_url($val->img_168_110 ); ?>" alt="" width="168px" height="110px" /></a>
							<h5 class="btnForView"><s>￥<?php print number_format($val->market_price, 2, '.', '') ?></s> ￥<?php print number_format($val->tuan_price, 2, '.', '') ?></h5>
							<a href="" target="_blank" title="" class=""><b><?php print $val->tuan_name ?></a>
						</li>
                        <?php
                    	}
                    	?>
					</ul>
                    <?php } else { ?>
                    最近没有浏览商品
                    <?php } ?>
				</div>
				<div id="barForView"><span id="moveForRecent" class="bWFont">< 最近</span>|<span <? if(count($tuaninfoRec)>3) { ?> id="moveForEarly" <? } ?> >更早 ></span><a href="tuan/clear_cookie" title="清除" id="">清除</a></div>
				</div>
				</div>
			</div>
			<div id="tuanRushBox">
				<ul id="ulTuanRush">
		            <?php
		    		foreach ($tuaninfo as $key => $val) {
		    		?>
					<li <? if(($key+1)%3==0){ ?> class="marginR" <? } ?>>
						<div class="greenBorderBox" onclick="window.open('tuanDetail-<?php print $val->tuan_id ?>.html')" style="display:none;"></div>
						<div class="rushBox">
							<p class="rushBoxP"><?php print $val->tuan_name ?></p>
							<div class="rushShadowLiBox">
								<div class="rushShadowLiCont">
									<s>￥<?php print number_format($val->market_price, 2, '.', '') ?></s>
									<p><b><?php print $val->buy_num ?></b>人已购买</p>
								</div>
								<a href="tuanDetail-<?php print $val->tuan_id ?>.html" target="_blank" title="">
		                            <img src="<?php print img_url($val->img_315_207 ); ?>" width="315px" height="207px" /></a>
							</div>
							<div class="rushBoxBotm">
								<h4><?php print number_format($val->tuan_price, 2, '.', '') ?></h4>
								<a href="tuanDetail-<?php print $val->tuan_id ?>.html" target="_blank" class="btnGoToSee"></a>
							</div>
						</div>
					</li>
		        	<?php
		        	}
		        	?>
				</ul>
			</div>	
			<div class="switch_block_page">
				<div>
					<a hidefocus="" href="/tuan-<?=$sort_type ?>-<?=$sort ?>-<?=$page-1 ?>.html" class="preBtn" <? if($page==0) { ?> style="display:none" <? } ?> >上一页</a>
					<a hidefocus="" class="sel"><?=$page+1 ?></a>
					<a href="/tuan-<?=$sort_type ?>-<?=$sort ?>-<?=$page+1 ?>.html" hidefocus="" <? if($page+1>=$maxpage) { ?> style="display:none" <? } ?> ><?=$page+2 ?></a>
					<a href="/tuan-<?=$sort_type ?>-<?=$sort ?>-<?=$page+2 ?>.html" hidefocus="" <? if($page+2>=$maxpage) { ?> style="display:none" <? } ?> ><?=$page+3 ?></a>
					<a href="/tuan-<?=$sort_type ?>-<?=$sort ?>-<?=$page+3 ?>.html" hidefocus="" <? if($page+3>=$maxpage) { ?> style="display:none" <? } ?> ><?=$page+4 ?></a>	
					<a class="nextBtn" href="/tuan-<?=$sort_type ?>-<?=$sort ?>-<?=$page+1 ?>.html" hidefocus="" <? if($page==$maxpage) { ?> style="display:none" <? } ?> >下一页</a>
				</div>
		  </div>
  	</div>
  </div>
</div>
<?php include APPPATH . 'views/common/tuanfooter.php'; ?>
<script type="text/javascript" src="<?=static_style_url("js/tuanRush.js")?>"></script>
