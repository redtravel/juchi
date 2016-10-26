<?php include APPPATH."views/mobile/header.php"; ?>
    <div class="views">
        <!-- 演示站商城-->
            <div class="view view-main" data-page="index">
                <div class="pages">
                <div data-page="index" class="page no-toolbar">
                        <div class="navbar">
                            <div class="navbar-inner">
                                <div class="left"><a href="#" class="link back history-back"><i class="icon icon-back"></i></a></div>
                                <div class="center c_name"><?php echo $special->rush_index;?></div>
                                <div class="right"><a href="/product/ptype_list" data-popover=".popover-links" class="link icon-only open-popover external">
                                <i class="icon csearchico"></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="page-content infinite-scroll special-bg" data-template="infiniteProductTemplate" data-parent=".listb ul" data-source="/brand/ajax_brand_list/brand_product_list" data-params="brand_id=20">
                            <div class="special-banner"><img src="<?php echo img_url($special->image_before_url);?>"></div>
                            <?php if(!empty($campaign)) { foreach ($campaign as $amp) { ?>
                                <div class="special-activity">
                                  <div class="special-nr clearfix ">
                                       <div class="coupons"><span>&yen;<?php echo round($amp->voucher_amount,0);?></span>优惠券</div>
                                       <div class="use-rule">
                                            <div class="guize-hu">使用规则：</div>
                                            <div class="guize-write">有效期<?php echo date('Y-m-d',strtotime($amp->start_date));?> <?php echo date('Y-m-d',strtotime($amp->end_date));?><br/>满<?php echo $amp->min_order;?>元可用,每单限用一张。</div>
                                       </div>
                                       <div class="receive" onclick="return v_special_campaign('<?php echo $amp->release_id;?>');">点击领取</div>
                                  </div>
                                </div>
                            <?php } }?>                             

                            <div class="special-lb-hu clearfix">
                                <div class="special-list-hu">
                                    <ul>
                                    <?php if(!empty($special_product)) { foreach ($special_product as $spt) { ?>
                                        <li>
                                            <div class="special-rr clearfix">
                                                    <div class="special-rr-img"><a href="/pdetail-<?php echo $spt->product_id;?>.html" class="external"><img src="<?php echo img_url($spt->img_url);?>" class="lazy" data-src="<?php echo img_url($spt->img_url);?>"></a></div>
                                                    <div class="special-inf clearfix">
                                                        <div class="special-tit"><a href="/pdetail-<?php echo $spt->product_id;?>.html" class="external"><?php echo $spt->product_name;?></a></div>

                                                        <?php if( !empty($collect_data) && deep_in_array($spt->product_id, $collect_data)) { ?>
                                                            <div class="special-gz special-gz-red"></div>
                                                        <?php }else{ ?>
                                                            <div class="special-gz special-gz-gray" onclick="add_to_collect(<?php echo $spt->product_id;?>,0,this,'special-gz');"></div>
                                                        <?php } ?>     

                                                    </div>
                                                    <?php if(!empty($spt->subhead)){?>
                                                    <span class="xinghao-hu"><?php echo $spt->subhead;?></span>
                                                    <?php }?>
                                                    <!-- <p class="tedian-hu">特点：舒适纤巧，贴合手型，卓越舒适感和灵巧度颗粒袖口设计，增加强度，防止撕裂</p> -->
                                                    <!-- <span class="sheng-hu">仅剩3小时00分钟</span> -->
                                                    <div class="special-anniu clearfix">
                                                        <div class="special-anniu-left">&yen;<?php echo $spt->promote_price;?></div>
                                                        <div class="special-anniu-right"><a href="/pdetail-<?php echo $spt->product_id;?>.html" class="external">马上抢></a></div>                                      
                                                    </div>
                                            </div>
                                        </li>
                                    <?php } }?> 
                                    </ul>
                                </div>
                            </div>
                            
                            <?php if (!empty($ad)){ ?>
                                <div class="glsp-hhu clearfix">
                                    <div class="order-details-rr"><a href="javascript:void(0);" class="more-ys-hu">更多关联商品</a></div>
                                </div>
                                <?php
                                    foreach($ad as $a){
                                        echo adjust_path($a->ad_code);
                                    }
                            }?>
                        </div>
                    </div>
                </div>
            </div>
</div>
<?php
include APPPATH . "views/mobile/common/footer-js.php";
 ?>
