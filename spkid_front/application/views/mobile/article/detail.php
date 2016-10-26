<?php include APPPATH."views/mobile/header.php"; ?>
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/tank.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/animate.css?v=version')?>">
<style type="text/css">
	.row.no-gutter .col-10 {
    	width: 13.33%;
	}
</style>
<!-- fenxiang start-->
<div class="popover popover-copyed">
    <div class="popover-angle"></div>
    <div class="popover-inner">复制成功!</div>
</div>

<div class="popover popover-menu">
	<div class="popover-angle"></div>
	<div class="popover-inner">
		<div class="navbar">
			<div class="navbar-inner">
				<div class="center">演示站商城</div>
			</div>
		</div>
		<div class="content-block share_modal hcenter">
           <div style="padding:0 2em;">
                <div class="jiathis_style_32x32 clearfix" style="text-align:center;">
                    <a class="jiathis_button_weixin"></a>
                    <a class="jiathis_button_qzone"></a>
                    <a class="jiathis_button_cqq"></a>
                    <a class="jiathis_button_tsina"></a>
                    <a class="jiathis_counter_style"></a>
                </div>
                
                <!--<p style="margin-top:10px;"><a href="#" class="link copy-link" style="display:block; color:#00F;">复制链接</a></p>-->
          </div>

		</div>

	</div>
</div>
<!-- fenxiang end-->

<!-- liuyan start-->
<div class="popup popup-Iliuya">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">发表评论</div>
			<div class="right close-popup" style="margin-right:1em">关闭</div>
		</div>
	</div>
	<div class="popup-Iliuya-content">
	    <div class="order-details-rr">
		 	<textarea id="liuyan-content" class="liuyan-hu" placeholder="填写你的评论" onfocus="this.style.color='#000'; this.value='';" style="color: #f00;"></textarea>
		  	<input type="hidden" id="post_id" value ="<?php echo $article->post_id?>"/> 
        </div>
	</div>	
	<div class="yywtoolbar">
        <div class="yywtoolbar-inner row no-gutter">
            <div class="col-100  payment-hu"><a class="link submit" href="javascript:void(0)" >提交</a></div>
        </div>
    </div>
</div>
<!-- liuyan end -->

<div class="views">
	<div class="view view-main" data-page="index">
	    <div class="pages">
	        <div data-page="index" class="page no-toolbar">

		  		<!-- navbar start -->         
		       	<div class="navbar">
		           	<div class="navbar-inner">
					    <div class="left">
					    	<a href="#" class="link back article-detail-back"><i class="icon icon-back"></i></a>
					    </div>
					    <div class="center">演示站文章视频频道</div>

	                </div>
		       	</div>
	          	<!-- navbar end -->

				<!-- bottom start -->         
				<div class="toolbar">
					<div class="toolbar-inner row no-gutter">
						<!-- guanzhu start -->
						<?php if( !empty($collect_data) && deep_in_array($article->post_id, $collect_data)) { ?>
		                    <div class="col-10 heart-hu-red"><a class="link" href="#"></a></div>
		                <?php }else{ ?>
		                    <div class="col-10 heart-hu-gray" onclick="add_to_collect(<?php echo $article->post_id?>,2,this,'heart-hu');"><a class="link" href="#"></a></div>
		                <?php } ?>
						<!-- guanzhu end -->
						
						<!-- fenxiang start -->
						<div class="col-10 v-share article-share open-popover" data-popover=".popover-menu"></div>
						<!-- fenxiang end -->
						
						<!-- praise start -->
						<?php if( !empty($praise_data) && deep_in_array($article->post_id, $praise_data)) { ?>
							<div class="col-10 praise2-v"><a class="link" href="#"></a></div>
						<?php }else{ ?>
							<div class="col-10 praise2" onclick="add_to_praise_article(<?php echo $article->post_id?>,this);"><a class="link" href="#"></a></div>
						<?php } ?>
						<!-- praise end -->

		               	<div class="col-60 registration-hu xunjia v-liuyan"><a class="link open-popup" data-popup=".popup-Iliuya" href="#">发表评论</a></div>
					</div>      
				</div>
				<!-- bottom end -->         

			  	<!-- page-content start -->  	
			  	<div class="page-content article-bg">
			       	<div class="content-block" style="padding-top:5px;">	       
						<!-- details-lb start -->
						<div class="details-lb">
					        <dl class="author-information clearfix">
					            <dt><img src="<?php echo $article->cover?>"></dt>
					            <dd><?php echo $article->display_name;?>(<?php echo $article->user_articles_num;?>)</dd>
					            <dd class="v-size">发布时间：<?php echo $article->post_date?></dd>
					            <dd class="v-size">关键字: <?php echo $tag?></dd>
					        </dl>

					        <div class="art-ico-hu clearfix">
					                <div class="in-eye"><?php echo get_page_view('article',$article->post_id);?></div>
					                <div class="praise"><?php echo ($article_praise_num == '') ? '0' : $article_praise_num;?></div>
						        	<div class="information"><?php echo count($article->comments);?></div>
						    </div>
							
							<h3 class="details-bt"><?php echo $article->post_title?></h3>
							<div class="details-news">
								<?php echo $article->post_content?>
							</div>
						</div>
					    <!-- details-lb end -->

				       	<!-- comment start -->  
				        <div class="comment">
				        	<a href="/index-article" class="back-article external">返回文章视频列表</a>
						</div>
				       	<!-- comment start --> 
             			
             			<!-- comment-lb start --> 
						<?php foreach ($article->comments as $comment):?>
							<div class="comment-lb clearfix">
                                <div class="comment-img">
<?php if(isset($comment['user_advar'])):?>
<img src="<?php echo static_url('mobile/touxiang/'.$comment['user_advar'])?>"/>
<?php else:?>
<img src="<?php echo static_url('mobile/touxiang/default.png')?>"/>
<?php endif;?>
</div>
							    <div class="conment-main">
							        <div class="comment-nr">
								        <span class="arrow"></span>
								        <div class="comment-xx">
									     	<div class="comment-mc"><span><?php echo ($comment['comment_author']=="") ? '匿名' : $comment['comment_author'];?></span><?php echo $comment['comment_date'];?></div>
							                <p class="comment-js"><?php echo $comment['comment_content']?></p>
										</div>
								   	</div>
							    </div>
						    </div>   
						<?php endforeach?>  
						<!-- comment-lb end -->  

					    <!-- comment-lb start -->
					    <div class="related-articles clearfix">
						    <div class="related-tit">关联文章</div>
				          	<ul class="related-lb">
							<?php if(!empty($relative_articles)):
								foreach($relative_articles as $article):?>
									<li>
							           <a class="external" href="/article/detail/<?php echo $article->post_id?>"><h3><?php echo $article->post_title?></h3></a>
							           <div class="related-write">作者：<?php echo $article->display_name?><span><?php echo substr($article->post_date, 0, -3)?></span></div>
									</li>
								<?php endforeach;?>
							<?php endif;?>
						   	</ul>						      
						</div>
					    <!-- comment-lb end -->        
                <div class="detail-wx"><img src="<?php echo static_url('mobile/img/wx.jpg')?>" width="100">
                <p>扫一扫关注演示站, 约我更容易!</p></div>
	     
			       	</div>
			  	</div>
			  	<!-- page-content end -->  	

			</div>
     	</div>
	</div>
</div>

<?php include APPPATH . "views/mobile/common/footer-js.php";?>

<script>


    $$('.submit').on('click', function(){
        var post_id = $$('#post_id').val();
        var content = $$('#liuyan-content').val();
        if (/^\s*$/.test(content)){
            myApp.alert('评论不能为空!');
            //myApp.addNotification({message: '评论不能为空!'});            
            return false;
        } else{
            $.ajax({
                url:'/article/comment',
                    data:{is_ajax:true,post_id:post_id,content:content},
                    //dataType:'json',
                    type:'POST',
                    success:function(result){
                        if(result){
                            myApp.alert('评论成功!', function(){
                                myApp.closeModal('.popup-Iliuya');
                                //mainView.reloadPage('#index');
                                location.href = location.href;
                            });
                            
                        } else{
                            myApp.alert('评论失败!');
                        }
                    }
            })
        }//endif
    })

    // 设置 图片  宽度 
	$$(".details-news img").removeAttr("height");
	$$(".details-news img").removeAttr("width");
	$$(".details-news img").css("width","100%");
	$$(".details-news table").css("width","100%");
	$$(".details-news table").attr("cellpadding",0);
	$$(".details-news table").attr("cellspacing",0);

	$$('.article-detail-back').on('click', function(e){
		location.href = '<?php echo FRONT_HOST . "/index-article"?>';
    });
    //document.execCommand('selectAll');
    /*
    $$('.copy-link').click(function(){
        $$('document').trigger('copy');
    })
    $$('document').on('copy', function(e){
        e.clipboardData.setData('text/plain', location.href);
    })
     */
    var clipboard = new Clipboard('.copy-link', {
    text: function(trigger) {
        return location.href;
    }
    });
    //alert(typeof clipboard);
    clipboard.on('success', function(e) {
        myApp.closeModal('.popover-menu');
        myApp.addNotification({
            message: '复制成功!',
            hold: 2500
    });

    })

</script>
<script type="text/javascript" src="http://v3.jiathis.com/code/jia.js" charset="utf-8"></script>
<?php include APPPATH . "views/mobile/footer.php";?>
