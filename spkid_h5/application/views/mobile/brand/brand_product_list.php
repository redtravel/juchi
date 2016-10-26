    <?php include APPPATH."views/mobile/header.php"; ?>
        <div class="views">
        <!-- 演示站商城-->
            <div class="view view-main" data-page="index">
                <div class="pages">
        		    <div data-page="index" class="page no-toolbar">
                        <div class="navbar">
                            <div class="navbar-inner">
                                <div class="left"><a href="#" class="link back history-back"><i class="icon icon-back"></i></a></div>
                                <div class="center c_name"><?php echo $one_brand->brand_name;?></div>
                            </div>
                        </div>
                        <div class="page-content infinite-scroll public-bg" data-template="infiniteProductTemplate" data-parent=".listb ul" data-source="/brand/ajax_brand_list/brand_product_list" data-params="brand_id=<?php echo $one_brand->brand_id;?>">
                            <div class="content-block no-top v-brand-bottom">
                                <div class="brandlist clearfix v-brand-list-top">
                                <?php if($one_brand->brand_banner){ ?>
                                    <img alt="<?php echo $one_brand->brand_name;?>" src="<?php echo img_url($one_brand->brand_banner);?>" />
                                <?php } ?>
                                <?php if($one_brand->brand_info){ ?>
                                    <h3  class="ppgs-hu">品牌简介</h3>
                                    <div class="ppgs-jiejian"><?php echo $one_brand->brand_info;?></div>
                                <?php } ?>
                                <?php if($one_brand->brand_story){ ?>
                                    <h3 class="ppgs-hu">品牌故事</h3>
                                    <div class="ppgs-jiejian"><?php echo adjust_path($one_brand->brand_story);?></div>
                                <?php } ?>
                                </div>
                                <div class="listb">
                                    <ul class="sbox clearfix">
                                    <?php if ($list): foreach($list as $abp): ?>
                                        <li>
                                            <div class="products-list clearfix">
                                                <a href="/pdetail-<?php echo $abp->product_id;?>" class="external">
                                                    <div class="img_sbox"><img class="lazy" data-src="<?php echo img_url($abp->img_url);?>"></div>
                                                    <div class="prod_name"><?php echo $abp->brand_name . ' ' . $abp->product_name;?></div>
                                                    <div class="bline clearfix">
                                                        <div class="favoheart"><span><?php echo $abp->pv_num;?></span></div>
                                                        <?php if(isset($abp->price_show) && $abp->price_show):?>
                                                            <div class="price_bar xunjia_product"><span class="prod_pprice" >询价</span></div>
                                                        <?php else:?>
                                                            <div class="price_bar" style=""><span class="prod_pprice"><?php echo $abp->product_price?></span></div>
                                                        <?endif;?>
                                                    </div>
                                                </a>
                                                <?php if ( $abp->is_hot ){ ?> 
                                                    <div class="mark mark_sale">热品</div>
                                                <?php }elseif ( $abp->is_new ){ ?> 
                                                    <div class="mark mark_new">新品</div>
                                                <?php }elseif ( $abp->is_best ){ ?> 
                                                    <div class="mark mark_new">展品</div>
                                                <?php }elseif ( $abp->is_offcode ){ ?> 
                                                    <div class="mark mark_offcode">促销</div>
                                                <?php } ?>
                                            </div>
                                        </li>
                                    <?php endforeach;endif;?>
                                    </ul>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                <?php include APPPATH."views/mobile/common/template7.php"; ?>
            </div>
            <?php include APPPATH."views/mobile/common/footer-js.php"; ?>
            <!-- template user's profile -->
<script type="text/template7" id="userTemplate">
<h3>{{user_name}}</h3>
</script>


<!-- product -->
<script type="text/template7" id="infiniteProductTemplate">
{{#each data}}
<li>
    <div class="products-list clearfix">
    <a hreff="/pdetail-{{product_id}}" class="external">
    <div class="img_sbox"><img class="lazy" data-src="{{../img_domain}}/{{img_url}}"></div>
    <div class="prod_name">{{product_name}}</div>
    <div class="bline clearfix">
    <div class="favoheart"><span>245</span></div>
    <div class="price_bar {{#js_compare "this.price_show=='1'"}}xunjia_product{{/js_compare}}"><span class="prod_pprice">{{#js_compare "this.price_show=='1'"}}询价{{/js_compare}}{{#js_compare "this.price_show=='0'"}}{{product_price}}{{/js_compare}}</span></div>
    </div>
    </a>
    {{#js_compare "this.is_hot=='1'"}} <div class="mark mark_sale">热品</div> {{/js_compare}}
    {{#js_compare "this.is_new=='1'"}} <div class="mark mark_new">新品</div> {{/js_compare}}
    {{#js_compare "this.is_offcode=='1'"}} <div class="mark mark_offcode">促销</div> {{/js_compare}}
    {{#js_compare "this.is_best=='1'"}} <div class="mark mark_show">展品</div> {{/js_compare}}
    </div>
</li>
{{/each}}
</script>
<script>
    $$(".ppgs-jiejian img").removeAttr("height");
    $$(".ppgs-jiejian img").removeAttr("width");
    $$(".ppgs-jiejian img").css("width","100%");
    $$(".ppgs-jiejian img").css("height","0%");
</script>
        </div>
    <?php include APPPATH."views/mobile/footer.php"; ?>
