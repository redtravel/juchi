<div class="page cached no-toolbar">
    <!--navbar start-->
    <div class="navbar menu">
           <div class="navbar-inner">
	         <div class="left"><a class="link icon-only back" href="#"> <i class="icon back"></i></a></div>
                 <div class="center">我的关注</div>
            </div>
    </div>
    <!--navbar end-->  
    <div class="page-content article-bg2">
	   	<div class="edu-fot">
            <div class="guanzhu-hu">
            	<!--buttons-row start-->
              	<div class="buttons-row">
                   	<a href="#col_1" class="tab-link active button button-secondary">产品 (<?php echo $product_num;?>)</a>
                   	<a href="#col_2" class="tab-link button button-secondary">课程 (<?php echo $course_num;?>)</a>
                   	<a href="#col_3" class="tab-link button button-secondary">文章 (<?php echo $article_num;?>)</a>
              	</div>
           		<!--buttons-row start-->
             	<div class="tabs ">
                   	<div id="col_1" class="tab active list-block guanzhu-tab">
                      <?php if($product_num != 0){ ?>
                         <ul>
                          <?php foreach ($product as $col_pro) { if($col_pro->product_type == 0) { ?>                             
                          <li class="swipeout col-v-li item-content">
                               <div class="swipeout-content ">
                                     <div class="item-inner clearfix">
                                           <a href="/pdetail-<?php print $col_pro->product_id;?>.html" class="external">
                                              <div class="col-v-img"><img src="<?php print img_url($col_pro->img_url); ?>" alt="<?php echo $col_pro->product_name;?>"/></div>
                                              <div class="item-after">
                                                   <span class="public-text"><?php echo $col_pro->product_name;?></span>
                                                   <span class="<?php if($col_pro->is_onsale != 0 ) {echo 'col-v-border';}else{echo 'col-v-border2';} ?>">已下架</span>
                                                   <span class="guanzhu-jiage">￥<?php echo $col_pro->shop_price;?></span>
                                              </div>
                                           </a>
                                      </div>
                                </div>
                               <div class="swipeout-actions-right"><a href="#" class="swipeout-delete" onclick="collect_delete_v(<?php print $col_pro->rec_id;?>);">取消关注</a></div> 
                            </li> 
                        <?php } } ?>          
                        </ul>
                      <?php }else{ ?>
                          <div class="no-concern"><span>目前还没有关注</span></div>
                      <?php } ?>
                  </div>

                  <div id="col_2" class="tab list-block guanzhu-tab">
                    <?php if($course_num != 0){ ?>
                       <ul>
                       <?php foreach ($product as $col_pro) { if($col_pro->product_type == 3) { ?>                                
                       <li class="swipeout col-v-li col-v-li2 item-content">
                           <div class="swipeout-content">
                                <div class="item-inner clearfix">
                                     <a href="/product-<?php print $col_pro->product_id; ?>.html" class="external">
                                        <div class="col-v-img"><img src="<?php print img_url($col_pro->img_url); ?>" alt="<?php echo $col_pro->product_name;?>" /></div>
                                        <div class="item-after">
                                             <span class="public-text2"><?php echo $col_pro->product_name;?></span>
                                             <?php 
                                              $desc = json_decode($col_pro->product_desc_additional);?>
                                              <span>时间 : <?php echo date("Y.m.d ", strtotime($col_pro->package_name));?> ~ <?php echo date("Y.m.d ", strtotime($desc->desc_waterproof));?></span>
                                              <span>地址 : <?php echo $desc->desc_material;?></span>
                              					      <?php if(time() > strtotime($desc->desc_waterproof)) { ?>
                                                  <span class="col-v-border4">已结束</span>
                              					      <?php }else{ ?>
                              					      	  <span class="col-v-border3">报名中</span>
                              					      <?php } ?>
                                        </div>
                                     </a>
                                </div>
                           </div>
                           <div class="swipeout-actions-right"><a href="#" class="swipeout-delete" onclick="collect_delete_v(<?php print $col_pro->rec_id;?>);">取消关注</a></div>
                    </li>
                    <?php } } ?>          
                    </ul>
                  <?php }else{ ?>
                          <div class="no-concern"><span>目前还没有关注</span></div>
                      <?php } ?>
                 </div>

                 <div id="col_3" class="tab list-block guanzhu-tab">
                   <?php if($article_num != 0){ ?>
                      <ul>
                      <?php foreach ($article as $col_art) { ?>                             
                      <li class="swipeout col-v-li item-content">
                          <div class="swipeout-content">
                                <div class="item-inner clearfix">
                                     <a href="/article/detail/<?php print $col_art->ID; ?>" class="external">
                                        <div class="col-v-img"><img src="<?php echo $col_art->arc_img;?>" alt="<?php echo $col_art->post_title;?>"/></div>
                                        <div class="item-after">
                                             <div class="public-text2 "><?php echo $col_art->post_title;?><span class="<?php if($col_art->video == 1) echo 'shipin-hu';?>"></span></div>
                                             <span class="wenzhang-time"><?php echo $col_art->display_name."  ".$col_art->post_modified;?></span>                   
                                             <div class="article-ico guanzhu-rr">
                                					       <div class="attention  gz-wz-hu"><?php get_page_view('article',$col_art->ID,false);?></div>
                                						      <div class="information gz-wz-hu"><?php echo $col_art->comment_count;?></div>
                                					   </div>
                                        </div>
                                    </a>
                                </div>
                          </div>
                          <div class="swipeout-actions-right"><a href="#" class="swipeout-delete" onclick="collect_delete_v(<?php print $col_art->ID;?>);">取消关注</a></div>
                      </li>  
                      <?php } ?>           
                     </ul>
                  <?php }else{ ?>
                          <div class="no-concern"><span>目前还没有关注</span></div>
                      <?php } ?>
                 </div>
           
          		</div>
	  		</div>
	  	</div>
   	</div>
</div> 