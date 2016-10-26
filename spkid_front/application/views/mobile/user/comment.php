<?php include APPPATH."views/mobile/header.php"; ?>
<div class="view view-main">
<div class="pages">
<div class="page" data-page="comment">
    <div class="navbar">
                <div class="navbar-inner">
                     <div class="left"><a href="#" class="link back history-back"><i class="icon icon-back"></i></a></div>
                    <div class="center">我的评论</div>
                </div>
    </div>
    <div class="page-content public-bg">
        <div class="content-block article-video">
<div class="list-block media-list no-top" style="margin-top:0;">
			     <ul class="hu-cart-shops">
	<?php
if (!empty($comment_list)):
                    foreach ($comment_list as $liuyan):
                        ?>

			         <li class="c_rec469 hu-cart-noline">
	                         <a href="<?php echo $liuyan->url?>" class="item-link item-content external">
	                         <div class="item-media col-v-img"><img src="<?php echo $liuyan->tiny_url?>" alt="<?php echo $liuyan->product_name?>"></div>
	                         <div class="item-inner">
	                              <div class="public-text"><?php echo $liuyan->product_name ?></div>
	                              <div class="item-text hu-gwc">规格：<span class="c_size469" data-subid="554"><?php echo isset($liuyan->size_name)?$liuyan->size_name:''?><span></span></span></div>
	                              <div class="item-title-row hu-cart-nobg" style="padding-top:10px;">
	                                   <div class="guanzhu-jiage">&yen;<?php echo $liuyan->sale_price?></div>
	                                   <div class="item-after">&times;<?php echo isset($liuyan->product_num)?$liuyan->product_num:1 ?></div>
	                              </div> 
			           
	                         </div>
			        </a>
				
				           
<div class="hu-evaluation">		  
<?php if (isset($liuyan->comment_content)): ?>
					
                    <div class="juli-plick" style="word-wrap:break-word; word-break:break-all;">
		      <?php echo $liuyan->comment_content?>
		    </div>
			        
                    <?php else:?>
<div class="hu-pingjia"><a href="" data-pid="<?php echo $liuyan->product_id?>" class="btn btn-box popover-btn">评价</a></div>
                    <?php endif; ?> 
</div>
					<?php endforeach;
                endif;
                ?>
		                       </li>
	                    	     </ul>	      
	             	    </div>	    
        </div>
    </div>
</div>

    </div><!-- pages -->
<!-- comment popover -->
</div>
</div>
  <div class="popover popover-comment">
    <div class="popover-angle"></div>
    <div class="popover-inner">	
	    <div class="order-details-rr"><textarea name="content" style="width:100%; height:20em; border:none; padding-top:10px;" placeholder="请填写你的评价"></textarea>
    </div>
<div class="button-rows"><a href="" class="button button-fill color-red submit">提交</a></div>
    </div>
  </div>
<?php include APPPATH."views/mobile/common/footer-js.php"; ?>
<script>
var pid;
var popover = $$('.popover-comment');

$$('.submit').on('click', function(){
    var fl_text=$$('textarea[name="content"]').val();
 
    if ('' == fl_text) {
        myApp.addNotification({message: '评论不能为空!'});
        //myApp.alert('评论不能为空!');
        return false;
    }
    
    $.ajax({
        url:'/user/post_dianping',
        data:{is_ajax:true,product_id:pid,comment_content:fl_text},
        dataType:'json',
        type:'POST',
        success:function(result){
            if(result.error!=0){
                myApp.alert(result.msg);
                return false;
            }
            myApp.alert(result.msg);
            location.reload();
        }
    });
})
$$('.popover-btn').on('click', function (){
    myApp.popover(popover, $$(this));
    pid = $$(this).data('pid');
    
})
</script>
<?php include APPPATH."views/mobile/footer.php"?>
