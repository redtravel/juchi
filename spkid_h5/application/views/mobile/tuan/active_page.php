<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width" />
<title><?php print $tuan_info->tuan_name;?></title>
<meta name="keywords" content=""/> 
<meta name="description" content=""/>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="black" name="apple-mobile-web-app-status-bar-style" />
<meta name="format-detection" content="telephone=no" />
<meta content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport" />
<link rel="stylesheet" href="<?php echo static_style_url('mobile/tuan/tuan.css?v=version')?>">

</head>

<div class="loading" style="display:block" id="loading">
     <div class="loading-pic"></div>
</div>
<body onload="setTimeout(function(){var loading=document.getElementById('loading'); loading.style.display='none';}, 500)">
<script type="text/javascript" src="<?php echo static_style_url('mobile/tuan/jquery-1.4.4.min.js?v=version')?>"></script>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/jquery.lazyload.js?v=version')?>"></script>
<div class="wrap">
     <div class="wrap-space">
          <!-- <div class="tuan-tit"><h1><?php print $tuan_info->tuan_name;?></h1></div> -->
          <p class="tuan-js"><?php print adjust_path($tuan_info->userdefine1);?></p>
          <div class="tuan-scroll"><img src="<?php echo img_url($tuan_info->img_315_207);?>"></div>
          <div class="activity-rule">
              <div class="activity-tit"><span>活动规则</span></div>
              <ul class="activity-list">
                  <?php print adjust_path($tuan_info->userdefine2);?>
              </ul>
          </div>
          <div class="tuan-scroll">
              <?php $img_500_450 = json_decode($tuan_info->img_500_450);if(!empty($img_500_450)) { foreach ($img_500_450 as $key => $value) { ?>
                  <img src="<?php echo img_url($img_500_450[$key]);?>">
              <?php }} ?>
          </div>
          <div class="activity-rule complete-registration">
              <div class="activity-tit"><span><?php print $register_num;?>人已完成报名</span></div>
              <?php if(!empty($register_info)){ ?>
              <ul class="complete-list clearfix v-un-register">
                  <?php foreach ($register_info as $reg_val) { ?>
                  <li>
                      <div class="complete-pci"><img src="<?php echo (($reg_val->wechat_headimgurl == 'unknown' || empty($reg_val->wechat_headimgurl)) ? static_style_url('mobile/img/default_person.jpg?v=version') : $reg_val->wechat_headimgurl);?>"></div>
                      <div class="complete-mc">
                      <?php 
                          if( $reg_val->wechat_nickname == '' || $reg_val->wechat_nickname =='unknown')
                          {
                              echo $reg_val->register_name;
                          }else{
                              echo $reg_val->wechat_nickname;
                          }
                      ?>
                      </div>
                      <div class="complete-date"><?php echo date('m', strtotime($reg_val->register_date)).'月'.date('d', strtotime($reg_val->register_date)).'日';?></div>
                  </li>
                  <?php } ?> 
              </ul>
              <?php } ?>
              <div class="comment activity-ico zhankai un-register">展开更多报名</div>
          </div>
         
          <div class="activity-rule complete-registration">
              <div class="activity-tit clearfix"><span class="a-baoming"><?php print $comments_num;?>条评论</span><span class="write activity-ico pingjia"><a href="javascript:void(0);" class="pl">写评价</a></span></div>
              <?php if(!empty($comments_info)){ ?>
              <ul class="complete-list complete-list2 clearfix v-un-comment">
                  <?php foreach ($comments_info as $com_val) { ?>
                  <li>
                      <div class="complete-pci2"><img src="<?php echo (($com_val->wechat_headimgurl == 'unknown' || empty($com_val->wechat_headimgurl)) ? static_style_url('mobile/img/default_person.jpg?v=version') : $com_val->wechat_headimgurl);?>"></div>
                      <div class="complete-rr">
                          <div class="complete-top clearfix">
                              <div class="complete-user">
                              <?php 
                                  if( $com_val->wechat_nickname == '' || $com_val->wechat_nickname =='unknown')
                                  {
                                      echo 'Jerry';
                                  }else{
                                      echo $com_val->wechat_nickname;
                                  }
                              ?>
                              </div>
                              <div class="complete-time"><?php echo date('m', strtotime($com_val->comment_date)).'月'.date('d', strtotime($com_val->comment_date)).'日';?></div>
                          </div>
                          <div class="complete-bot"><?php echo $com_val->comment_content;?></div>
                      </div>
                  </li>
                  <?php } ?> 
              </ul>
              <?php } ?>
              <div class="comment activity-ico zhankai un-comment">展开更多评论</div>
          </div>
        
        <dl class="tooth-xx clearfix">
            <dt><img src="<?php echo static_style_url('mobile/img/tooth.png?v=version')?>"></dt>
            <dd><span>兔子布克：</span><?php print $tuan_info->userdefine3;?></dd>
        </dl>
     </div>
</div>

<div class="activity-but">
     <div class="wrap-space3">
          <a href="http://mp.weixin.qq.com/s?__biz=MzA3MDY0MDkyNw==&mid=100000226&idx=1&sn=f6e613192384f61d2b31edd212f86540#rd" class="activity-anniu attention">我要关注</a><a href="javascript:void(0);" class="activity-anniu sign login login2">我要报名</a>
     </div> 
</div>

<div class="denglu2">
	 <a class="colse"></a>
     <div class="reveal-list">
          <div class="reveall">
                <label>姓名</label>
                <input name="register_name" type="text" class="reveal-input">
          </div>
          <div class="reveall">
                <label>电话</label>
                <input name="register_mobile" type="text" class="reveal-input"><a href="javascript:void(0);" class="v_mobile"></a>
          </div>
          <div class="reveall">
                <label>购买数量</label>
                <input name="register_num" type="text" class="reveal-input"><span class="reveal-wr"><?php print $tuan_info->tuan_unit;?></span>
          </div>
          <a href="javascript:void(0);"  class="reveal-sure v-register">确定</a>
     </div>  
</div>

<div class="sucBox">
	   <a class="colse colse2"></a>
     <div class="reveal-title"></div>
     <div class="reveal-lb">
          <p class="lingdao">尊敬的<?php echo $tuan_info->wechat_nickname;?>：</p>
          <p class="gongxi">恭喜你已经报名登记成功！目前您在报名者中排名第<?php print $register_num+1;?>位。</p>
          <a href="javascript:void(0)"  class="reveal-sure reveal-sure2" onclick="$('.sucBox').hide();$('.loading').show('slow');location.href='/tuan/tuan_confirm/<?php echo $tuan_info->tuan_id;?>' + '?t='+ new Date().getTime()">等待付款团购</a>
     
     </div>
    <div class="focus-on">
        关注演示站公众号，第一时间知晓最新的优惠促销活动！
        <a href="http://mp.weixin.qq.com/s?__biz=MzA3MDY0MDkyNw==&mid=100000226&idx=1&sn=f6e613192384f61d2b31edd212f86540#rd"><img src="<?php echo static_style_url('mobile/img/guanzhu.png?v=version')?>"></a>
    </div>
</div>

<div class="pinlun">
    <div class="comment-pop">
        <textarea name="comment_content" cols="" rows="" placeholder="评论" class="pop-kuang"></textarea>
        <a href="javascript:void(0);" class="submit-but v-comment">提交</a>
    </div>
</div>
<div class="mask"></div>
<script>
			//显示评论;
      $(".pl").click(function(){
				$(".pinlun").fadeIn(300);
				$(".mask").fadeIn(300);
			});
			//显示报名;
			$(".login2").click(function(){
				$(".denglu2").fadeIn(300);
				var dlh = -($(".denglu2").height())/2;
				$(".denglu2").css({marginTop:dlh});
				$(".mask").fadeIn(300);
			});
			// //显示报名;
			// $(".suc").click(function(){
			// 	$(".sucBox").fadeIn(300);
			// 	$(".denglu2").hide();
			// 	var dlh = -($(".denglu2").height())/2;
			// 	$(".sucBox").css({marginTop:dlh});
			// 	$(".mask").fadeIn(300);
			// });
			//按钮关闭;
			$(".colse").click(function(){
				$(".pinlun").fadeOut(300);
				$(".denglu2").fadeOut(300);
				$(".sucBox").fadeOut(300);
				$(".mask").fadeOut(300);	
			});
			//点击背景层关闭;
			$(".mask").click(function(){
				$(this).fadeOut(300);
				$(".pinlun").fadeOut(300);
				$(".denglu2").fadeOut(300);
				$(".sucBox").fadeOut(300);
			});

      var default_person = "<?php echo static_style_url('mobile/img/default_person.jpg?v=version')?>";
      //展开报名
      $(".un-register").click(function(){
          $(this).remove();
          $('.activity-rule').css('padding-bottom','0px');
          $.ajax({
              url:'/tuan/un_content', 
              type:'POST', 
              dataType:'json', 
              data:{is_type:1,tuan_id:'<?php echo $tuan_info->tuan_id;?>'}, 
              success:function(result){
                  if (1 == result.error)
                  {
                      console.log('没有更多的报名');
                  }else{
                      var str = '';
                      for(i = 0; i < result.un_info.length; i++) {
                          register_date = new Date(result.un_info[i].register_date);
                          var reg_month = ((register_date.getMonth() + 1) < 10) ? '0'+(register_date.getMonth() + 1) : (register_date.getMonth() + 1);
                          var reg_date = (register_date.getDate() < 10) ? '0'+ register_date.getDate() : register_date.getDate();
                          if(result.un_info[i].wechat_nickname == '' ||result.un_info[i].wechat_nickname =='unknown'){ result.un_info[i].wechat_nickname = result.un_info[i].register_name; }
                          str += '<li><div class="complete-pci"><img src="' + ((result.un_info[i].wechat_headimgurl == 'unknown' || !result.un_info[i].wechat_headimgurl) ? default_person : result.un_info[i].wechat_headimgurl) + '"></div><div class="complete-mc">' + result.un_info[i].wechat_nickname + '</div><div class="complete-date">' + reg_month+ '月' + reg_date +'日</div></li>';   
                      }; 
                      $('.v-un-register').html(str);
                  } 
              },
              beforeSend:function(){
                  $(".loading").css("display","block");
              },
              complete:function() {
                  $(".loading").css("display","none");
              },
              error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //error tips
              }
          });
      })
      //展开评论
      $(".un-comment").click(function(){
          $(this).remove();
          $('.activity-rule').css('padding-bottom','0px');
          $.ajax({
              url:'/tuan/un_content', 
              type:'POST', 
              dataType:'json', 
              data:{is_type:2,tuan_id:'<?php echo $tuan_info->tuan_id;?>'}, 
              success:function(result){
                  if (1 == result.error)
                  {
                      console.log('没有更多的评论');
                  }else{
                      var str = '';
                      for(i = 0; i < result.un_info.length; i++) {
                          comment_date = new Date(result.un_info[i].comment_date);
                          var com_month = ((comment_date.getMonth() + 1) < 10) ? '0'+(comment_date.getMonth() + 1) : (comment_date.getMonth() + 1);
                          var com_date = (comment_date.getDate() < 10) ? '0'+ comment_date.getDate() : comment_date.getDate();
                          if(!result.un_info[i].wechat_nickname || result.un_info[i].wechat_nickname =='unknown'){ result.un_info[i].wechat_nickname = 'Jerry'; }
                          str += '<li><div class="complete-pci2"><img src="' + ((result.un_info[i].wechat_headimgurl == 'unknown' || !result.un_info[i].wechat_headimgurl) ? default_person : result.un_info[i].wechat_headimgurl) + '"></div><div class="complete-rr"><div class="complete-top clearfix"><div class="complete-user">'+ result.un_info[i].wechat_nickname +'</div><div class="complete-time">'+ com_month + '月' + com_date +'日</div></div><div class="complete-bot">'+ result.un_info[i].comment_content +'</div></div></li>';   
                      }; 
                      $('.v-un-comment').html(str);
                  } 
              },
              beforeSend:function(){
                  $(".loading").css("display","block");
              },
              complete:function() {
                  $(".loading").css("display","none");
              },
              error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //error tips
              }
          });
      })
      
      // 获取用户之前报名的信息并放入input
      var loc_tuan_name = 'loc_name' + '<?php echo $tuan_info->tuan_id;?>';
      var loc_tuan_mobile = 'loc_mobile' + '<?php echo $tuan_info->tuan_id;?>';
      var loc_tuan_num = 'loc_num' + '<?php echo $tuan_info->tuan_id;?>';
      if(localStorage.getItem(loc_tuan_name)){
          $('input[type=text][name=register_name]').val(localStorage.getItem(loc_tuan_name));
      } 
      if(localStorage.getItem(loc_tuan_mobile)){
          $('input[type=text][name=register_mobile]').val(localStorage.getItem(loc_tuan_mobile));
          $('.v_mobile').addClass("phone-public");
          $('.v_mobile').addClass("phone-blue");
          $('.v_mobile').removeClass("phone-red");
      }
      if(localStorage.getItem(loc_tuan_num)){
          $('input[type=text][name=register_num]').val(localStorage.getItem(loc_tuan_num));
          $(".reveal-wr").css("color","#333");
      }
      
</script>

</body>

<script type="text/javascript">
$(document).ready(function(){
	$('body img').lazyload({ 
		placeholder : "../img/loading.gif", 
		effect : "fadeIn" 
	}); 



});	
	
    var register = $('input[type=text][name=register_mobile]');
    var reg_num = $('input[type=text][name=register_num]');
    var tel = /^(1[0-9]{10})|(0\d{2,3}-?\d{7,8})$/;
    register.blur(function(){
        if (!tel.test(register.val())) {
            $('.v_mobile').addClass("phone-public");
            $('.v_mobile').addClass("phone-red");
            $('.v_mobile').removeClass("phone-blue");
        }else{
            $('.v_mobile').addClass("phone-public");
            $('.v_mobile').addClass("phone-blue");
            $('.v_mobile').removeClass("phone-red");
        }
    })

    reg_num.focus(function(){
        $(".reveal-wr").css("color","#333");
    })
    reg_num.blur(function(){
      if(reg_num.val() != ''){
          $(".reveal-wr").css("color","#333");
      }else{
          $(".reveal-wr").css("color","#ccc");
      }
    })

    $('.v-register').click(function(){
        var register_name = $.trim($('input[type=text][name=register_name]').val());
        var register_mobile = $.trim($('input[type=text][name=register_mobile]').val());
        var register_num = $.trim($('input[type=text][name=register_num]').val());    
        var wechat_id = '<?php echo $tuan_info->wechat_id;?>';

        //将用户填写的 input 信息写进local
        localStorage.setItem(loc_tuan_name,register_name);
        localStorage.setItem(loc_tuan_mobile,register_mobile);
        localStorage.setItem(loc_tuan_num,register_num);

        if (!tel.test(register_mobile)) {
            $('.v_mobile').addClass("phone-public");
            $('.v_mobile').removeClass("phone-blue");
            $('.v_mobile').addClass("phone-red");
        }else if (register_name == '') {
            alert('请填写姓名');
        }else if (register_num == '') {
            alert('请填写购买数量');
        }else if(tel.test(register_mobile) && register_name != '' && register_num != ''){
            $(".denglu2").hide();
            $.ajax({
                url:'/tuan/add_register',
                data:{register_name:register_name,register_mobile:register_mobile,register_num:register_num,wechat_id:wechat_id,expire:false},
                dataType:'json',
                type:'POST',
                async:true,
                success:function(data){
                  $.ajax({
                      url:'/user/proc_loginAndRegister', 
                      type:'POST', 
                      dataType:'json', 
                      data:{
                        username:register_mobile,
                        password: '',
                        loginType:2
                      }, 

                      success:function(data){
                      if (1==data.error)
                        {
                            console.log('注册错误');
                        } 
                      },
                      error:function() {
                        console.log('注册遇到问题');
                      },
                      complete:function() {
                          $(".loading").css("display","none");
                      }
                  });

                  $(".sucBox").fadeIn(300);                  
                  var dlh = -($(".sucBox").height())/2;
                  $(".sucBox").css({marginTop:dlh});
                  $(".mask").fadeIn(300);
                },
                beforeSend:function(){
                    $(".loading").css("display","block");
                },
                complete:function() {
                    $(".loading").css("display","none");
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    //error tips
                }
            })
        }
    });
    
    $('.v-comment').click(function(){
        var comment_content = $.trim($('.pop-kuang').val());
        var tuan_id = '<?php echo $tuan_info->tuan_id;?>';
        var wechat_id = '<?php echo $tuan_info->wechat_id;?>';

        if (comment_content == '') {
            alert('您还没有评论');
        }else{
            $.ajax({
                url:'/tuan/add_comment',
                data:{comment_content:comment_content,tuan_id:tuan_id,wechat_id:wechat_id,expire:false},
                dataType:'json',
                type:'POST',
                async:true,
                success:function(result){
                    $(".pinlun").fadeOut(300);
                    $(".mask").fadeOut(300);
                    if (1 == result.error){
                        console.log('未提交成功，请重新评论！');
                    }else{
                        var str = '';
                        for(i = 0; i < result.un_info.length; i++) {
                            comment_date = new Date(result.un_info[i].comment_date);
                            var com_month = ((comment_date.getMonth() + 1) < 10) ? '0'+(comment_date.getMonth() + 1) : (comment_date.getMonth() + 1);
                            var com_date = (comment_date.getDate() < 10) ? '0'+ comment_date.getDate() : comment_date.getDate();
                            str += '<li><div class="complete-pci2"><img src="' + result.un_info[i].wechat_headimgurl + '"></div><div class="complete-rr"><div class="complete-top clearfix"><div class="complete-user">'+ result.un_info[i].wechat_nickname +'</div><div class="complete-time">'+ com_month + '月' + com_date +'日</div></div><div class="complete-bot">'+ result.un_info[i].comment_content +'</div></div></li>';   
                        }; 
                        $('.v-un-comment').html(str);
                        $('.a-baoming').text(result.comments_num + '条评论');
                    } 
                }
            })
        }
    });
</script>

<?php
	$appId = 'wxd11be5ecb1367bcf';
	$appsecret = '6d05ab776fd92157d6833e2936d6f17c';

	$timestamp = time();
	$jsapi_ticket = make_ticket($appId,$appsecret);
	$nonceStr = make_nonceStr();
	$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$signature = make_signature($nonceStr,$timestamp,$jsapi_ticket,$url);

?>
<?php if(isReqFromWechat()):?>
	<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript"></script>
<script>
	wx.config({
        debug: false,
        appId: '<?=$appId?>',
        timestamp: <?=$timestamp?>,
        nonceStr: '<?=$nonceStr?>',
        signature: '<?=$signature?>',
        jsApiList: [
            'checkJsApi',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
	    'onMenuShareTimeline', 
	    'onMenuShareAppMessage'
          ]
       });
       
       wx.ready(function(){
       
		wx.onMenuShareTimeline({
		title: $('title').text(),

		link: "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>",
		imgUrl: $('.tuan-scroll img').attr('src'),
		trigger: function (res) {
		console.log('');
		},
		success: function (res) {
		alert('十分感谢您对演示站的支持！！！');
		},
		cancel: function (res) {
        
		},
		fail: function (res) {
		console.log(JSON.stringify(res));
		}
		});	   
	    
	    wx.onMenuShareAppMessage({
	      title: $('title').text(),
	      desc: $('meta[name="Description"]').length ? $('meta[name="Description"]').attr('content') :　$('title').text(),
	      link: "<?php echo 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']?>",
	      imgUrl: $('.tuan-scroll img').attr('src'),
	      trigger: function (res) {
	        console.log('');
	      },
	      success: function (res) {
	        alert('十分感谢您对演示站的支持！！！');
	      },
	      cancel: function (res) {
        
	      },
	      fail: function (res) {
	        console.log(JSON.stringify(res));
	      }
	    });
	
	});
	
	
</script>	
<?php endif;?>
<?php include(APPPATH.'views/common/tongji.php'); ?>
</html>

