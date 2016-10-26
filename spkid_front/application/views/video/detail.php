<?php include_once(APPPATH . "views/common/header.php");?>
<link rel="stylesheet" type="text/css" href="<?php echo static_style_url('pc/css/pdetail.css?v=version')?>">
<link rel="stylesheet" type="text/css" href="<?php echo static_style_url('pc/css/tank.css?v=version')?>">
<link rel="stylesheet" type="text/css" href="<?php echo static_style_url('pc/css/signin.css?v=version')?>">
<script src="<?php echo static_style_url('pc/js/comm_tool.js?v=version')?>"></script>
<link href="<?php echo static_style_url('pc/css/bootstrap.css?v=version')?>" rel="stylesheet" type="text/css" media="all">

<script src="<?php echo static_style_url('pc/js/bootstrap.min.js?v=version')?>" type="text/javascript">
</script>


<script>
	window.hostId = '3179';
</script>

<style>

a.bds_more {
	background: none !important;
}

.play-biaoti{ background-color:#000;}
.play-cotent{ width: 1000px;margin: 0 auto; }

h1, .h1, h2, .h2, h3, .h3 { margin-bottom:0; margin-top:0;}
	.product-wrapper {
		margin-top: -10px;
	}

	.video-play {
		width:100%;
		background:#242321;
	}
	.video-player {
		width:80%;
		margin:10px auto;
		text-align:center;		
	}

	.video-player > p {
		display:inline-block;
	}

	.video-info {
		padding-left:20px;
		padding-top:10px;
		color:gray;
		width: 900px;
		margin: 10px auto;
	}

	.video-info h5 {
		color:black;
	}

	.mgl1em {
		margin-left:1em;
	}


	.video-wrapper {
		border: 1px solid #E0DCDC;
		border-top: none;
	}

	.author_avator {
		display:inline-block;
		width:50px;
		height:50px;
		border-radius:50px;
		-moz-border-radius:50px;
		-o-border-radius:50px;
		-webkit-border-radius:50px;
		-ms-border-radius:50px;
		-khtml-border-radius:50px;
		background:black;
	}

	.video-info-detail > span {
		vertical-align:middle;
	}

	.product-icon-box {
		vertical-align:middle;
	}
	.hot-video ul li {
		margin-top:10px;
    }


</style>
<div class="play-biaoti">
    <div class="play-cotent"><?php echo $article->post_content?></div>
     
     
</div>
<div class="play-con">
          <div class="play-title"><?php echo $article->post_title?></div>
          <div class="play-xx clearfix">
               <span class="play-mc"><?php echo $article->display_name;?></span>
               <span class="play-mc"><?php echo $article->post_date?>上传</span>
               <div class="video-tubiao video-tubiao2 clearfix"><span class="video-liul"><?php echo get_page_view('article',$article->post_id);?></span><span class="video-jt"><?php echo count($article->comments);?></span></div>
               <div class="play-ico clearfix"><a href="#" class="play-heart" onclick="add_to_collect(<?php echo $article->post_id;?>,4,this);"></a>
               <!-- <em>
               <span class="bdsharebuttonbox" data-bd-bind=""><a href="#" class="bds_more" data-cmd="more"></a></span>
               </em> -->

               <div class="video-detail share-icon">
               	<div class="bdsharebuttonbox"><a href="javascript:void(0)" class="bds_more" data-cmd="more"><i></i>分享</a></div>
               </div>
               </div>
          </div>
          
     </div>
<div class="wrap-mian wrap-min2">
     <div class="play-con play-question">
          <div class="int-evaluation">
               <form data-hosttype="2" data-committype="" data-hostid="3179" name="ct-form" class="ct-form" method="POST " action="/api/comment/add">
                     <div class="clearfix"><textarea placeholder="您怎么看？" aria-required="true" name="comment" style="height:90px;"></textarea></div>
                     <div class="ct-submit" style="display: none;">
                          <span class="ct-count">还能输入<em>150</em>字</span>
                          <button class="btn btn-liuyan btn-blue" type="submit">提交</button>
                    </div>
               </form>
               <ul id="ct-list-full" class="ct-list">
                   <?php foreach ($article->comments as $comment):?> 
                   <li class="clearfix">
                       <div class="avatar">
<?php if(isset($comment['user_advar'])):?>
<img src="<?php echo static_url('mobile/touxiang/'.$comment['user_advar'])?>"/>
<?php else:?>
<img src="<?php echo static_url('mobile/touxiang/default.png')?>"/>
<?php endif;?></div>
                       <div class="cont">
                            <div class="ut"><span class="uname text-overflow "><?php echo ($comment['comment_author']=="") ? '匿名' : $comment['comment_author'];?></span><span class="date"><?php echo $comment['comment_date'];?></span></div>
<?php if ('' != $comment['parent_content']):?>
                            <div class="quote">
                                <div class="uname"><span>@<?php echo ($comment['parent_author']=="") ? '匿名' : $comment['parent_author']?></span></div>
                               <div class="qct"><?php echo $comment['parent_content']?></div>
                            </div>
<?php endif;?>
                           <div class="ct"><?php echo $comment['comment_content']?></div>
                           <div class="tb"><a at_comment_id="<?php echo $comment['comment_ID']?>" href="#">回复</a></div>
                      </div>
                 </li>
<?php endforeach?>
             </ul>
           
           
         </div>
         
       <div class="hot-video">
              <div class="hot-title">热门视频</div>
              <ul id="videoHotBox">
<?php foreach ($hotvideos as $k => $v):?>                        
            <li class="hot-video-list clearfix">
                  <a href="/video/detail/<?=$v->ID?>" data-id="273" class="hot-video-play">
                  <img src="<?php echo $v->cover?>">
                  <div class="time">02:54</div>
                  <div class="scan-linear-mask"></div>
                  <div class="play-mask">
                       <div class="play-btn"></div>
                  </div>
                 </a>
                <div class="hot-video-info">
                     <h3><?php echo $v->post_title?></h3>
                     <div class="video-tubiao video-tubiao3 clearfix"><span class="video-liul"><?php echo get_page_view('article',$v->ID);?></span><span class="video-jt"><?php echo $v->comment_count?></span></div>
               </div>
            </li>
<?php endforeach;?>
           </ul>        
      </div>
     </div>
</div>

<script>
	var post_id = '<?php echo $article->post_id?>';
$(function(){
    $('.ct-form').find("textarea").css('height', '42px');    
    //$('.ct-submit').hide();
		
    $('.ct-form').find("textarea").focus(function() {
                        "" === $(this).val() && ($(this).stop().animate({height: "90px"}), $(this).parent().next().show())
                    });
		        $('.ct-form').find("textarea").blur(function() {
		            var t = $(this);
		            setTimeout(function() {
		                "" === t.val() && (t.stop().animate({height: "42px"}), t.parent().next().hide())
		            }, 300)
		        });
$('.ct-form textarea').on('input propertychange', function(){
    var count = 150-$(this).val().length;
    if (0 == count){
        $(this).attr('readonly', 'readonly');
    }
    $('.ct-count>em').html(count);
})

        $(".ct-list").delegate(".tb a", "click", function(t) {
            t.preventDefault(), t.stopPropagation();
            var i = $(this), n = i.closest("li");
            if (n.siblings().find(".ct-form textarea").val(""), n.siblings().find(".ct-form").slideUp("fast"), i.data("form"))
                i.data("form").slideToggle("fast");
            else {
                var a = $(".ct-form").first().clone(true).hide();
                a.find("textarea").css({height: "42px"}), a.find(".ct-submit").hide(), a.find("textarea").val(""), i.data("form", a), a.appendTo(n).slideDown("fast");
            }
    	})

    	$('.btn-liuyan').click(function(e){
    		var at_comment_id = 0;
    		var is_at = $(this).parents('form.ct-form').prev().find('.tb').length > 0;
    		if (is_at) {
    			at_comment_id = $(this).parents('form.ct-form').prev().find('.tb a').attr('at_comment_id');
    		};
    		
    		var form = $(this).closest('form');
    		var textarea = form.find('textarea');
    		var content, liuyan, content = liuyan = textarea.val();
    		var is_main_form = !!form.next('.ct-list').length;
		    if (!liuyan) {
		        alert('留言不能为空');
		        return false;
		    };
            $.ajax({
            url:'/article/comment',
                data:{is_ajax:true,post_id:post_id,content:content,comment_parent:at_comment_id},
                //dataType:'json',
                type:'POST',
                success:function(result){
                    if(result){
                        location.reload();
                    }
                }
            })
		    textarea.stop().animate({height: "42px"}), textarea.parent().next().hide();
		    textarea.val('');
		    e.preventDefault();
    		e.stopPropagation();

    	});
	});
</script>

<!-- 登陆弹层开始 -->
<div id="login-box" class="modal fade pop-box in" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">登录</h4>
            </div>
            <div class="modal-body">
            <div class="form-container">

      <form name="loginForm" class="form-signin">
        <p class="error"></p>
        <input type="tel" name="username" class="form-control" placeholder="请输入手机号码">
        <p class="error"></p>
        <input type="password" id="password" name="password" class="form-control" placeholder="请输入密码" required="">
        <div class="checkbox">
          <label for="auto_login" class="grey-text"><input class="input-checkbox" type="checkbox" value="1" name="checkout" id="auto_login">下次自动登录</label> <a class="pull-right" href="/user/forgot">忘记密码</a>
        </div>
        <div class="btn-block clearfix">
        <button class="btn btn-lg btn-blue btn-block" type="submit"><i class="fa fa-lock left"></i>登录</button>
        <p class="pull-right grey-text">还没账号?<a href="/user/register">注册</a></p>
        </div>


        <div class="horizontal"><span>可以使用以下方式登录</span></div>
        <div class="other">
          <a href="/user/qq_login" class="qq"></a>                  
          <a href="/user/weixin_login" class="weixin"></a>		  
          <a href="/user/alipay_login" class="alipay"></a>                 
          <a href="/user/xinlang_login" class="sina"></a>
        </div>
      </form>

    </div>
            </div>

        </div>
    </div>
</div>  
<script>
	$(function(){

		$('#password').on('input propertychange', function(){
		    var username = $('input[name="username"]'), psw = $('#password');
		    if (0 < username.length && 0 < psw.length){
		        $('button.disabled').removeClass('disabled').removeAttr('disabled');        
		    }
		})
		var username = $('input[name="username"]');
		username.blur(function(){
		    if ('' == username.val()){
		        username.prev().text('请输入账号');
		        $('button.disabled').attr('disabled', 'disabled');
		    } else {
		        username.prev().text('');
		    }
		})
		$('form[name="loginForm"]').on('submit', function(e){
		    e.preventDefault();
		    var psw = $('#password');    
		        
		    if ('' == psw.val()){
		        psw.prev().text('请输入密码');
		        $('button.disabled').attr('disabled', 'disabled');
		    } else if ('' != $('input[name="username"]').val()) {
		        $('button.disabled').removeClass('disabled').removeAttr('disabled');
		        //alert($(this).serialize());
		        $.ajax({url:'/user/proc_login', data:$(this).serialize(), method:'POST', dataType:'json', success:function(data){
		            if (1 == data.error){
		                //alert(data.name);
		                $('input[name='+data.name+']').prev().text(data.message);
		            } else {
		                location.reload();
		            }
		        }
		        })
		    }
		    //alert(username.val()+' '+psw.val());
		    return false;
		})
	});
</script>
<!-- 登陆弹层结束 -->

<script>
	window._bd_share_config = {
		"common": {
			"bdSnsKey": {},
			"bdText": "",
			"bdMini": "1",
			"bdMiniList": ["tsina", "qzone", "weixin", "renren", "tqq", "douban", "sqq"],
			"bdPic": "",
			"bdStyle": "0",
			"bdSize": "32",
		},
		"share": {}
	};
	with(document) 0[(getElementsByTagName('head')[0] || body).appendChild(createElement('script')).src = 'http://bdimg.share.baidu.com/static/api/js/share.js?v=86835285.js?cdnversion=' + ~(-new Date() / 36e5)];
</script>



<?php include_once(APPPATH . "views/common/footer.php");?>
