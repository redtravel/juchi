<div class="page cached no-toolbar">
    <!--navbar start-->
    <div class="navbar">
        <div class="navbar-inner">
            <div class="left"><a href="#" class="link icon-only back"> <i class="icon back"></i></a></div>
            <div class="center">积分兑换现金券</div>
        </div>
    </div>
    <!--navbar end-->       
    <div class="page-content public-bg no-top2 v-acc-vou">
        <div class="page-content-inner no-top">
            <div class="content-block wrap">
                <ul class="receiving-address">
                <?php if (!empty($voucher_campaign)) { foreach ($voucher_campaign as $vc_val) { ?>
                    <li>
                        <a href="#" onclick="return exchange_voucher(<?php echo $vc_val->release_id;?>,<?php echo "'".$vc_val->voucher_name."'";?>);">
                            <div class="receiving-address-list">
                                <div class="juli-plick clearfix">
                                    <div class="receiving-lb  ">
                                        <span><?php echo $vc_val->voucher_name; ?></span>
                                        <span style="display:block;">金额: <?php echo $vc_val->voucher_amount; ?><em style="padding-left:30px; font-style:normal">需付积分:<?php echo $vc_val->worth; ?></em></span>
                                    </div>
                                    
                                    <div class="address-returned"></div>
                                </div>  
                            </div>
                        </a>
                    </li>
                <?php } }else{ echo "暂无该活动！"; }?>                    
                </ul>
            </div>
        </div>
    </div>
</div>

