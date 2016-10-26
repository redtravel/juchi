<?php include APPPATH . 'views/common/header.php'?>
<!--<link href="<?php echo static_style_url('pc/css/bootstrap.css?v=version')?>" rel="stylesheet" type="text/css">-->
<script src="<?php echo static_style_url('pc/js/jquery-1.11.3.js?v=version')?>" type="text/javascript"></script>
<script src="<?php echo static_style_url('pc/js/bootstrap.js?v=version')?>" type="text/javascript"></script>
<!--course-bar start-->
<div class="course-bar">
     <ul class="category-container">
     <li><a href="/course_all.html">全部课程</a></li>
     <li><a href="/index/course" class="course-active">热门课程</a></li>
     <li><a href="/index/medical">医考技考</a></li>
    </ul>
     
     <div class="course-list">
          <div class="course-container tab-content">
               <ul class="filter-bar clearfix">
                    <li><a href="#latest" role="tab" data-toggle="tab">近期课程</a></li>
                    <li><a href="#expire" role="tab" data-toggle="tab">往期课程</a></li>
               </ul>
               <div id="latest" class="course-show tab-pane">
<?php
if (!empty($courses)):
foreach($courses as $product_id => $course):
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
                     <span class="baoming"><a href="/product-<?php echo $product_id;?>.html" class="btn btn-blue sign-up">马上报名</a></span>
                      </div>
                    </dd>
                    </dl>
<?php endforeach;endif;?>

            </div>
            <div id="expire" class="course-show tab-pane">
<?php foreach($expire_courses as $product_id => $course):
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
                      </div>
                    </dd>
                    </dl>               
<?php endforeach;?>
            </div>
        </div>
     
    </div>

</div>

<!--course-bar end-->




<script>
$('a[href="#latest"]').tab('show');
//$('body').css('position', 'relative').height('300').scrollspy({'offset':50});

var range = 50;
var page = 1;
$(document).on("scroll", function(){  
    var srollPos = $(window).scrollTop();
    var totalheight = parseFloat($(window).height()) + parseFloat(srollPos);
    if(($(document).height()-range) <= totalheight) {
        ++page;
        var expire;
        if ( $('#latest').hasClass('active') ){
            expire = false;
        } else {
            expire = true;
        }
        $.ajax({
        url:'/index/course',
            data:{page:page, expire:expire},
            dataType:'json',
            success:function(data){
                if (data.course_list){
                    $('.tab-pane.active').append(data.course_list);
                } else {
                    console.log('no more');
                }
            }
        })
    }
});
</script>

<?php include APPPATH . 'views/common/footer.php'?>
