<? if(!empty($campaign)){ ?>
<div class="tip_adv">
    <ul>
	<? foreach ($campaign as $key => $val) { ?>
    	<li>
	    <?= $val['campaign_name'] ?>
    	</li>
	<? } ?>
    </ul>
</div>
<? } ?>
<div class="tip_adv_bottom"></div>
<div class="topNotice">
    <span>发货时间：<?=$expected_shipping_date; ?>前下单并完成支付于当日发货。</span>
</div>