<?php foreach($courses as $course):
          if (is_object($course)) $course = (array)$course;
            $product_desc_additional = (!empty($course['product_desc_additional'])) ? json_decode($course['product_desc_additional'], true) : array();
      ?>
        <dl class="edu-list clearfix">          
            <a href="/product-<?php print $course['product_id']; ?>.html" class="external">
              <dt><img src="<?php print img_url($course['img_url']); ?>" alt="<?php print $course['product_name']; ?>"></dt>
              <dd>
              <div class="edu-yb-jl">
                  
                  <h2 class="edu-biaoti"><?php echo $course['product_name'];?></h2>
                  <div class="edu-time">时间：<span><?php echo date("m月d日", strtotime($course['package_name']));?>-<?=date("m月d日", strtotime($product_desc_additional['desc_waterproof']))?></span></div>
                  <div class="edu-address">地点：<span><?php echo $product_desc_additional['desc_material'];?></span></div>
                  <div class="edu-tec clearfix">
                       <div class="edu-mc">讲师：<span><?php echo $course['subhead'];?></span></div>
                       <div class="edu-public edu-jt"></div>
                  </div>
                  
              </div>
              <div class="edu-ifo clearfix">

                   <div class="edu-ll bdr-r"><span class="edu-public edu-eye"><? echo get_page_view('product',$course['product_id'],false);?></span></div>
                   <div class="edu-baoming">已报名：<?=$course['ps_num']?></div>
              </div>
              </dd>
              <div class="edu-public <?php if(date('Y-m-d') <= $product_desc_additional['desc_waterproof']): ?>''<?php else: ?>edu-guoqi<?php endif; ?>"></div>
            </a>
        </dl>

    <?php endforeach;?>