<?php include APPPATH."views/mobile/header.php"; ?>


<div class="views">
<div class="view view-main" data-page="index">
     <div class="pages">
     
         <div data-page="courseResult"  class="page">
	 
          <div class="navbar">
                <div class="navbar-inner">
                    <div class="left"><a href="#" class="link back article-detail-back history-back"><i class="icon icon-back"></i></a></div>
                    <div class="center"><?=$kw?></div>
                    <div class="item-hide"><H2>牙科材料，齿科材料产品使用、牙科技术培训班</H2></div>
                </div>
            </div>
         
      <div class="page-content article-bg infinite-scroll" style="margin-top:60px;" data-template="html" data-parent=".forum-list ul" data-source="/product/ajax_product_list/searchResult" data-params="
						<?php 
							if(isset($searchByType) && $searchByType) {
								echo "stype=1&kw=$kw&type_id=$type_id&sort=10";
							} else {						
								echo "stype=1&kw=$kw" . (isset($ids) ? ('&ids='.$ids) : '' )."&sort=10";
							}
						?>">
           
           <div class="forum-list">
                     <div class="edupage">
                         <ul>
                            <?php foreach($product_list as $course):
			    $course = (array)$course;
			    $product_desc_additional = (!empty($course['product_desc_additional'])) ? json_decode($course['product_desc_additional'], true) : array();
			?>
                                <li>
                                    <a href="/product-<?php print $course['product_id']; ?>.html" class="external">
                                    <div class="<?php if($product_desc_additional['desc_waterproof'] >= date("Y-m-d")): ?>edu-box1<?php else: ?>edu-box2<?php endif; ?>">
				        <div class="juli-plick">
                                        <div class="l-box1 clearfix">
                                            <div class="t-photo1"><img class="lazy" data-src="<?php print img_url($course['img_url']); ?>.85x85.jpg" /></div>
                                            <div class="l-infobox">
                                            <p class="l-item1"><?php echo $course['product_name'];?></p>
                                            <p class="t-name1">讲师：<?php echo $course['subhead'];?></p>
                                                <p class="l-time1"><?php echo date("m月d日", strtotime($course['package_name']));?>-<?=date("m月d日", strtotime($product_desc_additional['desc_waterproof']))?> <span>地点：<?php echo $product_desc_additional['desc_material'];?></span></p>
<div class="item-hide">课程类别：牙科材料、技术线下培训班</div>
                                                
                                                <?php 
$now = date('Y-m-d H:i:s');
    if($course['is_promote'] && $now >= $course['promote_start_date'] && $now <= $course['promote_end_date']):?>
                                                <p class="l-sale1">通过演示站报名 立减&nbsp;&yen;<?=$course['shop_price'] - $course['promote_price']?></p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="status-line2 clearfix">
                                            <div class="attention"><? echo get_page_view('course',$course['product_id'],false);?></div>
                                            <div class="signin_num">已报名:<?=$course['ps_num']?></div>
                                            <div class="signin_bar"><span class="<?php if(date('Y-m-d') <= $product_desc_additional['desc_waterproof']): ?>signin<?php else: ?>yibaoming<?php endif; ?>"></span></div>
                                        </div>
					</div>

                                    </div>

                                </a>
                                </li>
<?php endforeach;?>

   
                            </ul>

                        

                    </div>

                </div> 
         
       
  </div>
  
         </div>          
          
         
     </div>
</div>

</div>
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/search.js?v=version')?>"></script>	
	</body>
</html>
