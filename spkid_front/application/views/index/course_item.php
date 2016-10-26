<?php foreach($courses as $product_id => $course):
$product_desc_additional = (!empty($course['product_desc_additional'])) ? json_decode($course['product_desc_additional'], true) : false;?>
<dl class="course-outer clearfix">

                    <dt><a href="/product-<?php echo $product_id;?>.html"><img src="<?php echo img_url($course['img_url'])?>.175x175.jpg" alt="<?php echo $course['product_name']?>" /></a></dt>


                    <dd>
                       <p class="device-title text-overflow"><a href="/product-<?php echo $product_id;?>.html"><?php echo $course['product_name'];?></a></p>
                       <p class="device-desc text-overflow">开课: <?php echo date("Y-m-d", strtotime($course['package_name']));?></p>
                       <p class="device-desc text-overflow">收藏: <?php if(isset($course['total']))
                       echo $course['total'];
                        else
                            echo 0;?></p>
                       <p class="device-desc text-overflow">讲师: <?php echo $course['subhead'];?></p>
                       <p class="device-desc text-overflow">报名: <?=$course['ps_num']?>人</p>
<div class="course-money clearfix">
<span class="course-price">&yen;<?php echo $course['shop_price']?></span>

<?php if ($expire=='false'):?><span class="baoming"><a href="/product-<?php echo $product_id;?>.html" class="btn btn-blue sign-up">马上报名</a></span><?php endif;?>
</div>
</dd>
</dl>
<?php endforeach;?>
