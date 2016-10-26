<?php include APPPATH."views/mobile/header.php"; ?>
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/tank.css?v=version')?>">
<link rel="stylesheet" href="<?php echo static_style_url('mobile/css/animate.css?v=version')?>">


<!-- fenxiang start you can have-->
<div class="popover popover-copyed">
    <div class="popover-angle"></div>
    <div class="popover-inner">复制成功!</div>
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

<!--广告宣传 start-->
<div class="popover popup-show-share-msg">
	<div class="navbar">
		<div class="navbar-inner">
			<div class="center">分享到微信</div>			
		</div>
	</div>
	<div class="popup-Iliuya-content">
	    <div class="order-details-rr" style="margin:20px auto">
		 	 <p style="font-weight:bold;color:black;">您可以添加我们的微信公众号</p>
		 	 <p style="font-weight:bold;color:black;">从那里分享</p>
		 	 <p style="text-alig:center;font-weight:bold;color:black;font-size:1.5em">演示站</p>
		 	 <p style="text-alig:center;">牙科行业的精品文章和专业资讯</p>
		 	 <p style="text-alig:center;">m.yueyawang.com</p>
        </div>
	</div>	
	
</div>

<!-- ends-->

<div class="views">
	<div class="view view-main" data-page="index">
	    <div class="pages">
	        <div data-page="index" class="page no-toolbar">

		  		<!-- navbar start -->         
		       	<div class="navbar">
		       		<?php if (strpos($this->input->server('HTTP_USER_AGENT'), 'MicroMessenger')):?>
						<div class="wechat_share_tip"></div>
		       		<?php endif;?>
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
						<div class="col-100 registration-v"><a class="link open-popup" data-popup=".popup-Iliuya" href="#"><input type="text" placeholder="登录或匿名评论" readonly="readonly">发表评论</a></div>
					</div>      
				</div>
				<!-- bottom end -->         

			  	<!-- page-content start -->  	
			  	<div class="page-content article-bg">       
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
							<hr style="border:solid 1px #E1E1E1;margin-top:20px;margin-bottom:10px;"></hr>
							<h3 class="details-bt"><?php echo $article->post_title?></h3>
							<div class="details-news">
								<?php echo $article->post_content?>
							</div>
							<div>
								<?php if( (!empty($praise_data) && deep_in_array($article->post_id, $praise_data)) || !empty($_COOKIE['praise_anonymous_'.$article->post_id])) { ?>
									<div class="v-zan-click-too"><a class="link" href="#"></a></div>
								<?php }else{ ?>
									<div class="v-zan-click-one" onclick="add_to_praise_article(<?php echo $article->post_id?>,this);"><a class="link" href="#"></a></div>
								<?php } ?>

								<div class="v-zan-num">已有<?php echo ($article_praise_num == '') ? '0' : $article_praise_num;?>赞</div>
				                <div class="jiathis_style_32x32 clearfix v-article-fenxiang">
				                	<span class="fxd">分享到:</span>	
				                    <a class="jiathis_button_tsina share_i2 share_i2_A3"></a>
				                    <a class="jiathis_button_qzone share_i2 share_i2_A4"></a>
				                    <a class="jiathis_button_cqq share_i2 share_i2_B1"></a>
				                    <?php if (!strpos($this->input->server('HTTP_USER_AGENT'), 'MicroMessenger')):?>
										<a class="jiathis_button_weixin1 share_i2 share_i2_A2"></a>	
					       			<?php endif; ?>	
									
				         		</div>
							</div>
							<!-- comment-lb start --> 
							<div>
							<?php if(!empty($article->comments)): ?>
								<div class="v-border-ccc v-article-ping">最新评论</div>
							<?php endif;?>
							<?php foreach ($article->comments as $comment):?>
								<div class="comment-lb clearfix v-border-ccc">
	                                <div class="comment-img">
										<?php if(isset($comment['user_advar'])):?>
										<img src="<?php echo static_url('mobile/touxiang/'.$comment['user_advar'])?>"/>
										<?php else:?>
										<img src="<?php echo static_url('mobile/touxiang/default.png')?>"/>
										<?php endif;?>
										</div>
								    <div class="conment-main">
								        <div class="comment-nr">
									        <div class="comment-xx">
										     	<div class="comment-mc"><span><?php echo ($comment['comment_author']=="") ? '匿名' : $comment['comment_author'];?></span><?php echo $comment['comment_date'];?></div>
								                <p class="comment-js"><?php echo $comment['comment_content']?></p>
											</div>
									   	</div>
								    </div>
							    </div>   
							<?php endforeach?>

							<!-- comment-lb end -->  

							<div class="v-article-jianjie">
							    <div class="tracey-v">
									<p class="wenzhang-v">文章视频频道</p>
									<p class="jianjie-v">为专家教授，一线专业牙医和其他行业人士提供一个可以分享自己学术观点、专业知识、临床经验的平台，为广大牙医和业内人士学习新知识、新技术，掌握新观点、新信息提供便利。</p>
									<div class="v-article-comment">
										<a href="/index-article" class="external article-arrow tarticle-jt">更多精彩文章</a>
									</div>
									<div class="article-yuegao">
									  	<div class="yuegao-title"><img src="<?php echo static_url('mobile/img/yuegao.gif?v=version');?>" alt=""></div>
                                      	<div class="yuegao-nr">成为演示站文章专栏作家,投稿可联系<br/>Sunny<span>QQ：3273941713</span></div>
									</div>
									<div class="v-article-product clearfix">
										<div class="rr-tracey"><a href="/index" class="article-arrow  tarticle-arrow2 external">热卖牙科产品</a></div>
										<div class="rr-tracey rr-traceys "><a href="/index-course" class="article-arrow tarticle-arrow2  external">精彩课程培训</a></div>
									</div>
								</div>					
							</div>
							<div class="v-article-yyw">
								<p class="v-article-nbsp">演示站-中国首家牙科电商与产品教育平台，致力于服务牙科人，为中国牙科行业创造价值！网站设三个频道：产品展销频道，课程培训频道，文章视频频道。最新商品，课程，技术与文章，登陆<a href="/index" class="external">yueyawang.com</a>，可一览无余。 </p>
								<p>商品、课程打折促销，最新热门技术与文章，还可以通过演示站微信公众号第一时间获悉。</p>
								<img src="<?php echo static_url('mobile/img/wx.jpg?v=version');?>" alt="">

								<?php if (strpos($this->input->server('HTTP_USER_AGENT'), 'MicroMessenger')){?>
									<p class="v-article-code">长按识别二维码,加关注</p>
					       		<?php }else{ ?>
									<p class="v-article-code">微信搜索公众号“演示站”,加关注</p>
					       		<?php }?>							
							</div>
						</div>
					    <!-- details-lb end -->    	
			  	</div>
			  	<!-- page-content end -->  	
			</div>
     	</div>
	</div>
</div>

<?php include APPPATH . "views/mobile/common/footer-js.php";?>

<script>
	
	$$('.jiathis_button_weixin1').on('click', function(e){
		myApp.popup('.popup-show-share-msg');
		return false;
	})

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
<style>

	.jiathis_style_32x32 .jtico {
		background:none;
	}
</style>
<?php include APPPATH . "views/mobile/footer.php";?>
