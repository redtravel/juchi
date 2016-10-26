<?php foreach($courses as $course):
			    $product_desc_additional = (!empty($course['product_desc_additional'])) ? json_decode($course['product_desc_additional'], true) : array();
			?>
                                <li>
                                    <a href="/product-<?php print $course['product_id']; ?>.html" class="external">
                                    <div class="<?php if($product_desc_additional['desc_waterproof'] >= date("Y-m-d")): ?>edu-box1<?php else: ?>edu-box2<?php endif; ?>">
				        <div class="juli-plick">
                                        <div class="l-box1 clearfix">
                                            <div class="t-photo1"><img src="<?php print img_url($course['img_url']); ?>.85x85.jpg" alt="<?php print $course['product_name']; ?>" /></div>
                                            <div class="l-infobox">
                                            <p class="l-item1"><?php echo $course['product_name'];?></p>
                                            <p class="t-name1">讲师：<?php echo $course['subhead'];?></p>
                                                <p class="l-time1"><?php echo date("m月d日", strtotime($course['package_name']));?>-<?=date("m月d日", strtotime($product_desc_additional['desc_waterproof']))?> <span>地点：<?php echo $product_desc_additional['desc_material'];?></span></p>
                                                
                                                <?php 
$now = date('Y-m-d H:i:s');
    if($course['is_promote'] && $now >= $course['promote_start_date'] && $now <= $course['promote_end_date']):?>
                                                <p class="l-sale1">通过演示站报名 立减&nbsp;&yen;<?=$course['shop_price'] - $course['promote_price']?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="status-line2 clearfix">
                                            <div class="attention"><? echo get_page_view('product',$course['product_id'],false);?></div>
                                            <div class="signin_num">已报名:<?=$course['ps_num']?></div>
                                            <div class="signin_bar"><span class="signin"></span></div>
                                        </div>
					</div>
                                    </div>
                                </a>
                                </li>
<?php endforeach;?>
