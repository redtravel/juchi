<div id="content" style="overflow: hidden;">
    <div class="plistMain">
        <div class="pro_c">
            <ul>
                <?php $i=1;foreach ($added_products as $p): ?>
                <li <?php if($i%3==0) print 'style="margin-right:0;"' ?>>
                    <?php 
                        $class = 'img_rexiao';
                        if ($p->is_best) $class = 'img_rexiao';
                        else if ($p->is_new) $class = 'img_xinpin';
                        else if ($p->is_hot) $class = 'img_rexiao';
                        else if ($p->is_promote) $class = 'img_cuxiao';
                        else if ($p->is_offcode) $class = 'img_duanma';
                    ?>
                    <div class="<?=$class;?>"></div>
                    <div class="fenxiangDiv" style="display:none;">
                        <a href="/" class="icon_tengxun"></a>
                        <a href="/" class="icon_sina"></a>
                        <span>分享</span>
                    </div>
                    <a href="<?php print "/product-{$p->product_id}-{$p->color_id}.html" ?>" target="_blank">
                        <img class="lazy" width="318" height="318" fslazy="<?=IMG_HOST?>/<?=$p->img_318_318; ?>" src="<?=STATIC_HOST?>/img/common/loading_1.gif" />
                    </a>

                    <dl>
                        <dd>
                            <div class="l"><?php print $p->brand_name ?></div>
                            <div class="r" style="color:#666;">市场价:<font class="y_p">￥<?php print round($p->market_price,2); ?></font></div>
                        </dd>
                        <dd>
                            <div class="l"><?php print $p->product_name ?></div>
                            <div class="r" style="margin-top:5px;">(<?php print round($p->product_price/max($p->market_price,0.01)*10,1); ?>折)</div>
                            <div class="priceNum r">￥<?php print round($p->product_price,2); ?></div>
                        </dd>
                    </dl>
                </li>
                <?php $i++;endforeach ?>
            </ul>
        </div>
    </div>
</div>