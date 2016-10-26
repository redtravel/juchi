<?php include APPPATH."views/common/user_header.php"; ?>
<script src="<?= static_style_url('pc/js/comm_tool.js?v=version')?>"></script>

               <div class="personal-center-right">
                    <h1 class="page-title">我的关注</h1>
                    <ul class="my_collect_bar">
                         <li><a href="javascript:void(0)" class="active">商品(<?= $product_num;?>)</a></li>
                         <li><a href="javascript:void(0)">课程(<?= $course_num;?>)</a></li>
                         <li><a href="javascript:void(0)">视频(<?= $video_num?>)</a></li>                         
                    </ul>
                    <ul class="my_collect_tabcontent">
                         <li class="collect_product">
                              <ul class="i_list">
                              <?php foreach ($products as $k => $v):?>
                                   <li>
                                        <a href="/pdetail-<?=$v->product_id;?>">
                                             <div class="detail">
                                                  <img src="<?php echo img_url($v->img_url)?>" alt="">
                                                  <p class="i_title">
                                                       <?=cutstr($v->product_name, 0, 14);?>
                                                  </p>
                                                  <p class="i_desc">
                                                  <?=cutstr($v->product_desc, 0, 14);?>
                                                  </p>
                                                  <p class="i_price">&#65509;<?=$v->product_price?><span style="margin-left:1em;color:gray"><s><?=$v->market_price;?></s></span></p>

                                             </div>
                                        </a>
                                   </li>
                              <?endforeach;?>
                                   
                              </ul>
                              <br style="clear:both">
                         </li>
                         <!-- 我关注的课程 starts-->
                         <li class="my_course" style="display:none"> 
                              <ul class="i_list">
                              <?php foreach($courses as $c):?>
                                   <li>
                                        

                                        <a href="/product-<?=$c->product_id;?>.html">
                                             <div>
                                                  <img src="<?= img_url($c->img_url)?>" alt="">
                                                  <p class="i_title"><?= cutstr($c->product_name, 0, 14);?></p>
                                                  <p class="i_teacher">讲师：<?=$c->subhead?></p>
                                                  <p class="i_price">&#65509;<?=$c->product_price?></p>

                                             </div>
                                        </a>
                                   </li>
                              <?php endforeach;?>
                              </ul>
                         </li>
                         <!-- 我关注的课程 ends-->
                         <li class="my_video" style="display:none">
                              <ul class="i_list">
                              <?php foreach($videos as $v):?>
                                   <li>
                                        <a href="/video/detail/<?=$v->ID?>">
                                             <div>
                                                  <img src="<?php echo $v->cover?>" alt="">
                                                  <p class="i_title"><?= cutstr($v->post_title, 0, 13)?></p>
                                                  <p class="i_author">作者：<?=$v->display_name?></p>                                                  

                                             </div>
                                        </a>
                                   </li>
                              <?php endforeach;?>
                              </ul>
                         </li>
                    </ul>
                    
               </div>
          </div>
     </div>
</div>
`

<script>
     $(function(){
          
          $('.my_collect_bar li').click(function(){
               $('.my_collect_bar li a.active').removeClass('active');
               $(this).find('a').addClass('active');
               var index = $(this).index();
               $('.my_collect_tabcontent > li').hide();
               $('.my_collect_tabcontent > li').eq(index).show();
          });


     });
</script>
<?php include APPPATH . "views/common/footer.php";?>
