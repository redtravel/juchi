<div class="basicBox">
    <div class="iContainXQ iContainXQAll">
        <ul>
            <?php foreach ($show_rushings as $key => $rush): ?>
            <li class="hotArea <?php if($key % 3 == 2): ?>noMrgRight<?php endif; ?> ">
                <div class="iXQShadowBoxBlack"></div>
                <div class="iXQShadowBoxRed"></div>
                <!--滚动提示信息-->
                <p class="newsBoxForMove" style="display:none"><span class="iconNewsFovMove"><?=$rush->rush_prompt;?></span></p>
                <div class="iXQShadowBoxText">
                    <div class="iXQTitle"><b><?=$rush->rush_brand;?></b><p><?=$rush->rush_category;?></p></div>
                    <p class="iXQOff"><?=$rush->rush_discount?><span class="font12">折起</span></p>
                    <p class="iXQTodayOff"><?=$rush->end_day?>天后结束</p>
                    <!--<div class="clearBoth"></div>-->
                </div>
                <p class="signFreeSent" imgpath="<?=STATIC_HOST?>/img/temp/hotSale.gif"></p>
                <a href="/rush-<?=$rush->rush_id?>.html" target="_self" >
                    <img src="<?=IMG_HOST?>/<?=$rush->image_before_url;?>" fslazy="<?=IMG_HOST?>/<?=$rush->image_before_url;?>" width="318px" height="368px;" />
                </a>
            </li>
            <?php endforeach; ?>
          </ul>
    </div>

</div>
<script type="text/javascript" src="<?=STATIC_HOST?>/js/scrollBar.js"></script>
<script type="text/javascript">
    //限抢部分交互效果
    $(".hotArea").hover(function () {
            if ($(this).find('.iXQShadowBoxRed').css('opacity')=='0') {
                    $(this).find('p.newsBoxForMove').stop().fadeTo(100,0.85);
                    $(this).find('.iXQShadowBoxRed').stop().fadeTo(100,1);
            }
    },function () {
            $(this).find('.newsBoxForMove').fadeTo(100,0);
            $(this).find('.iXQShadowBoxRed').fadeTo(100,0);
    });
</script>