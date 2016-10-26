var comment_type = 1;
/**
 * framework7's initialize
 *
 */
var myApp = new Framework7({
    modalTitle: '', 
    material: true,
    precompileTemplates: true,  
    modalButtonOk: '确定',
    modalButtonCancel: '取消',
    precompileTemplates: true,
    notificationCloseButtonText: '关闭',
    smartSelectBackOnSelect: false,
    pushState:true,
    pushStateRoot:'',
    ajaxStart: function () {
        myApp.showIndicator();
    },
    ajaxComplete:function(xhr){
        myApp.hideIndicator();
    },
    scroller:"native",
    preroute:function (view, options) { // index' user page
        if(typeof(options) == 'undefined'){
            return true;
        }
        if (options.pageName=='user'){ // 个人中心
                        if(checkLogin('#user',myApp)){
		    myApp.showIndicator();

    $$.getJSON('/user/profile_data', function (result) {
        myApp.hideIndicator();
        if( result.success == 1 ){
            $$('.page[data-page="user"] .page-content .content-block .user-profile').html(myApp.templates.userTemplate(
                    result.data
            ));
        }else{
            myApp.alert('获取个人信息失败。','提示');

        }
    });
	    }else return false;
        }
        if (/^\/cart/.test(options.url)){ // 首页的购物车
            return checkLogin('external:'+options.url,myApp);
        }
    }
})
var $$ = Dom7;

var mainView = myApp.addView('.view-main', {
    domCache: true,
});


var loginType = 0;//0为默认手机号+验证码登陆，1为用户名+密码
function checkInput() {

}

var InterValObj; //timer变量，控制时间
var count = 60; //间隔函数，1秒执行
var curCount;//当前剩余秒数

function sendMessage() {
  　curCount = count;
　　//设置button效果，开始计时
    $('.yazhengma').attr("disabled", "true").addClass('inactive');
    $('.yazhengma').text(curCount + "S");
    InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次    
}
function resetSendYazhengma() {
  window.clearInterval(InterValObj);//停止计时器
  $('.yazhengma').removeAttr("disabled").removeClass('inactive');//启用按钮
  $('.yazhengma').text("重新获取验证码");
  //$('input[name="authcode"]').val('');
}
//timer处理函数
function SetRemainTime() {
    if (curCount == 0) {                
        resetSendYazhengma();
    }
    else {
        if (/\d{6}/.test($('input[name="authcode"]').val())) {
          resetSendYazhengma();
          var loginBtn = $('.login-box .modal-button:nth-child(2)') || $$('.sign-in .loginbtn');
          loginBtn.removeAttr('disabled').removeClass('inactive');   
          return;
        };
        curCount--;
        $('.yazhengma').text(curCount + "S");
    }
}

function checkMobileNum(mobile) {
    var is_ok = false;
    if (!mobile || !/^1\d{10}$/.test(mobile)) {
      
    } else {
      is_ok = true;
    }
    return is_ok;
}    

function checkYazhengma(code) {
  var is_ok = false;
  if(code.length == 6 && !/^\d{4}$/.test(code)) {
      is_ok = true;
  } else {
    is_ok = false;
  }
  return is_ok;
}

function sendYanzhengma() {
  var mobile = $('.login-box input[name="username"]').val() || $('.sign-in input[name="username"]').val();
  if (!checkMobileNum(mobile)) {
        myApp.addNotification({
            message: '请填写正确手机号码',
            hold: 2500
        });
        return false;
  };
  sendMessage();
  $.ajax({
    method:'POST',
    dataType:'json',
    url:'/user/send_sms_code',
    timeout:3000,
    data:{
      is_ajax:1,
      mobile:mobile
    },
    success:function(data, status, xhr) {
      console.log(data);
      if (data.mobile_check_err == 0) {
        //resetSendYazhengma();
      } else {
        myApp.addNotification({
          message:data.msg_send_result,
          hold:2500
        });
      }
    },
    error:function(xhr, status) {
      console.log(status);
    }
  });

}

$$(document).on('ajaxStart', function (e) {
    myApp.showIndicator();
});
$$(document).on('ajaxComplete', function () {
    myApp.hideIndicator();
});

if (typeof(sessionStorage.is_logined) != "undefined" && sessionStorage.is_logined == "logined") {
    if ($$('.index-dl.login').length) {
      $$('.index-dl.login').remove();
    };
} 

function login(){

    myApp.yywLogin(false,'一键登录（免注册）',function(username, password){
        if(loginType == 0) ga('send', 'event', 'login', 'click', 'onekey-register');
        if(loginType == 1) ga('send', 'event', 'login', 'click', 'member');
            
        username = $('.login-box .text-input[name="username"]').val();
        password = (loginType == 0 ? $('.login-box .text-input[name="authcode"]').val() : $('.login-box .text-input[name="password"]').val());

        $$.ajax({
            url:'/user/proc_loginAndRegister', 
            method:'POST', 
            dataType:'json', 
            data:{
              username:username,
              password:password,
              loginType:loginType
            }, 

            success:function(data){
            if (1==data.error)
              {
                  myApp.addNotification({
                      message: data.message,
                      hold:2500
                  });
              } else {                        
                  myApp.closeModal('.login-box');
                  $$('.modal-overlay.modal-overlay-visible').remove();
                  if ($$('.index-dl.login').length) {
                    $$('.index-dl.login').remove();
                  };
                  sessionStorage.is_logined = 'logined';
              }
            },
            error:function() {
              myApp.addNotification({
                message:'网络问题，深感抱歉',
                hold:2500
              });
            }
        })
      }
      ,function(username, password){
        loginType = 1 - loginType;
        //$('.yazhengma').attr("disabled", "true").addClass('inactive');
        if (loginType == 1) {
          $('.modal-button:nth-child(1)').text('一键登录');
          $('.yazhengma-div').hide('slow');
          $('.login-box input[name="username"]').attr('placeholder', '手机/用户名');
          $('.mima-div').show('slow');
        };

        if (loginType == 0) {
          $('.modal-button:nth-child(1)').text('会员直接登录');
          $('.yazhengma-div').show('slow');
          $('.login-box input[name="username"]').attr('placeholder', '手机号');
          $('.mima-div').hide('slow');
        };    

      },'login-box');

      var loginBtn = $('.login-box .modal-button:nth-child(2)').attr('disabled', "true").addClass('inactive');

      $('.login-box input[name="authcode"]').on('input propertychange', function(){
        username = $('.login-box input[name="username"]');
        if (checkYazhengma($(this).val())) {
          if (checkMobileNum(username.val())) {
            loginBtn.attr('disabled', false).removeClass('inactive').addClass('active');
          };
        };
      })

      $('.login-box input[name="password"]').on('input propertychange', function(){
        username = $('.login-box input[name="username"]');
        password = $('.login-box input[name="password"]');
        if (username.val() && password.val()) {
          loginBtn.attr('disabled', false).removeClass('inactive').addClass('active');
        };
      })   

} 


/**
 * 如果page=false,则留在当前页面
 * // page like '#page_name' OR page like '/page.html'
 * // external page like 'external:/page.html'
 * 
 * // page like :false
 * // callback_str : function
 */
function checkLogin(page,app,callback_str){
    if( typeof app == 'undefined' ||  app == false ) app = myApp;
    var is_login;
    $$.ajax({
        async:false,
        method:'POST',
        url:'/user/check_is_login',
        dataType:'json',
    success:function(res){
        if (!res.is_login){
            
            login();
            is_login=false;

        }else{
            is_login=true;
        } 
    } 
    });
    return is_login;
}

/**
 *  点击收藏
 *	@product_id		收藏的 商品id
 *	@product_type	收藏的 商品类型  0=商品、1=礼包、2=文章、3=课程、4=视频
 *  @b              当前要收藏的 元素 （this）
 */
function add_to_collect (product_id,product_type,b,class_name) {

    if(!checkLogin(false)){
        return false;
    }

	$$.ajax({
		url:'/product_api/add_to_collect',
		data:{product_id:product_id,product_type:product_type,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {
                myApp.alert(result.msg)
            };
			if (result.err) {return false};
            var gray = class_name+'-gray';
            var red = class_name+'-red';
            $$(b).toggleClass(gray+' '+red);
            // TODO 显示收藏成功。
            // 
		}
	});
}

function add_to_collect2(product_id,product_type, callback) {

    if(!checkLogin(false)){
        return false;
    }

    $$.ajax({
        url:'/product_api/add_to_collect',
        data:{product_id:product_id,product_type:product_type,rnd:new Date().getTime()},
        dataType:'json',
        type:'POST',
        success:function(result){
            if (result.msg) {
                myApp.alert(result.msg)
            };
            if (typeof callback == 'function') {
                callback();
            };
        }
    });
}


/**
 *  点 赞
 *  @article_id     赞的 文章id
 *  @article_type   赞 的 文章类型  0=文章
 *  @s              当前要收藏的 元素 （this）
 */
function add_to_praise_article (article_id,s) {

    // if(!checkLogin(false)){
    //     return false;
    // }

	$$.ajax({
		url:'/article/add_to_praise',
		data:{article_id:article_id,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if (result.msg) {
                myApp.alert(result.msg)
            };
			if (result.err) {return false};
            $$(s).toggleClass('v-zan-click-one v-zan-click-too');
            $$('.praise').html(result.praise_num);
            $$('.v-zan-num').html('已有'+result.praise_num+'赞');
		}
	});
}

/**
* tagType 1=>'商品',2=>'礼包',3=>'课程'
* tag_id 对应tagtype的id，如tag_type=1，则tag_id 为对应的product_id
* comment_type 1=>'咨询',2=>'评价', 3=>'测评'
* container 为对应显示liuyan的html 标签，请直接传$$(选择器)
*/
function get_liuyan(tagType, tagId, comment_type, container, callback, params) {        
        if (!tagType || !tagId || !comment_type || !container) {
            myApp.alert('数据错误');
            return;
        };        
        $$.ajax({
        url : '/liuyan/liuyan_list',
        type : 'POST',
        dataType : "json",
        data : {
            comment_type : comment_type,
            tag_type : tagType,
            tag_id : tagId
        },
        success : function(data, status, xhr) {  
            if (data.err == 0) {
                if (typeof callback == 'function') {
                    callback(data.html);
                };
            } else {
                myApp.alert('未知错误');
            } 
        },

        error : function(xhr, status) {
            myApp.alert('数据请求错误');
        }

    });
}
/**
 * 无限下拉, 后台返回JSON数据前台使用t7实现
 * ajax返回数值：json_encode-- array('success'=>1,'data'=>json/html,'msg'=>'','img_domain'=>get_img_host());
 * 用法：
 *     div 的page_content 加上class: infinite-scroll且加上属性
 *     data-template="infiniteProductTemplate" ，模板名称或者html.若为html,AJAX的返回数值为html
 *     data-source="/index/ajax_goods_list" , ajax访问地址
 *     data-parent=".listb ul" , 要append的父元素selector
 *     data-params="a=b&c=1", url格式参数,会post到后台
 *     
 *
 *     参照：http://framework7.taobao.org/docs/infinite-scroll.html#.VgEGCnvK-O4
 */
var infinite_loading = false;
$$('.infinite-scroll').on('infinite', function (){
    var that = this;
    if (infinite_loading) { console.log('loading');return; }

    // dom, html tobe append
    var infinite_parent = $$(this).data('parent');

    // 如果已经加载完毕，退出
    var done = $$(infinite_parent).data('completed');
    if (  !(typeof done == 'undefined') ) { console.log('completed'); infinite_loading=false; return ;}

    // ajax' url
    var url = $$(this).data('source');
    if( typeof url == 'undefined' ) return ; 

    // dom elements' template || html
    var infinite_template = $$(this).data('template');
    if( typeof infinite_template == 'undefined' ) return ; 

    // page
    var page = $$(infinite_parent).data('page');
    page = (  typeof page == 'undefined' ) ? 2: page + 1;

    // extended params
    var params = $$(this).data('params');
    if (  typeof params == 'undefined' ){
        params = "page="+page;
    } else {
        params += "&page="+page;
    }
    console.log( params );

    var template = myApp.templates[infinite_template];

    infinite_loading  = true;
    $$.getJSON( url , params, function (result){
        if (result.success == 0){
            // TODO 消息提示，已经result.msg
            $$(infinite_parent).data('completed',1);
            console.log('success =0');
        } else {
            if ( infinite_template == 'html' ) $$(infinite_parent).append(result.data);
            else $$(infinite_parent).append(template(result));

            $$(infinite_parent).data('page',page);
            $$('.lazy').trigger('lazy');
        }
        infinite_loading  = false;
    },function(xhr,timeout){
        if(  timeout =='timeout')
        myApp.alert('网络链接超时，内容加载失败。','提示',function(){infinite_loading  = false;});
    });
});

//对于外链页面用history-back类来注明回退动作，请在回退链接中的加这个类
$$('.history-back').on('click', function(e){
    var referrer = document.referrer;
    referrer = referrer.replace(/index-\w+/, '');
    var a = document.createElement('a');
    var url = location.href; 
    a.href = url;
    var baseurl = (a.protocol ? a.protocol + '//' : '') + a.host + (a.port ? ":" + a.port : '') + '/';
    if (referrer && referrer == baseurl) {
        if (url.indexOf('pdetail-') !== -1) {
            location.href = '/';
        } else if (url.indexOf('product-') !== -1) {
            location.href = '/' + 'index-course';
        } else if (url.indexOf('article/detail') !== -1) {
            location.href = '/' + 'index-article';
        } else if (url.indexOf('user/') !== -1) {
            location.href = '/' + 'index-user';
        }else if (url.indexOf('brand/brand_product') !== -1) {
            location.href = '/#!//' + 'brand';
        } else {
            history.go(-1);            
        }
    } else {
        history.go(-1);        
    }
});

//详情页的快捷按钮事件注册
$$('.short-func-btn').on('click', function(e) {
    var box = $$('.short-func-box');
    if (box.css('display') == 'block') {
        box.removeClass('fadeInDown').addClass('fadeInUp').css('display', 'none');
    } else {
        box.removeClass('fadeInUp').addClass('fadeInDown').css('display', 'block');

    }
});



$$(document).on('click', '.short-func-box section', function(e) {
    var id = $$(this).attr('id');
    switch (id) {
        case 'shouye':
            location.href = '/index.html';
            break;
        case 'kefu':           
            break;
        case 'fenxiang':
            break;
        case 'fenleijiansuo':
            location.href = "/product/ptype_list.html";
            break;
        default:
            break;
    }

});

//咨询和留言同一种类型

$$(document).on('click', '.Iliuya, .Ixunjia', function(e){
    if(!checkLogin(false)){
        return false;
    }
    if($$(this).hasClass('Ixunjia')) {
        $$('.popup-Iliuya .navbar .center').text('我要询价');
        comment_type = 4;
    }

    if($$(this).hasClass('Iliuya')) {
        $$('.popup-Iliuya .navbar .center').text('我要留言');
        comment_type = 1;
    }

    $$('.popup-Iliuya-content textarea').trigger('focus');
    myApp.popup('.popup-Iliuya');
})


//“我要留言”和“我要询价”为同一种咨询类型
$$(document).on('click', '.button-tijiao', function(e){    

    var liuyan = $$('textarea[name="popup-liuya-content"]').val();
    var name = $$('input[name="name"]').val();
    var mobile = $$('input[name="mobile"]').val();
    if (!liuyan) {
        myApp.alert('留言不能为空');
        return;
    };

    /*if (!name) {
        myApp.alert('姓名不能为空');
        return;
    };

    if (!mobile) {
        myApp.alert('手机号不能为空');
        return;
    };*/
    
    $$.ajax({
        url : '/liuyan/proc_zixun',
        type : 'POST',
        dataType : "json",
        data : {
            comment_type : comment_type,
            tag_type : tagType,
            tag_id : tag_id,
            comment_content : liuyan,
            name : name,
            mobile : mobile
        },
        success : function(data, status, xhr) { 
            
myApp.alert(data.msg,( data.err == '0' )?'恭喜':'抱歉'); 
            data.err == '0' && myApp.closeModal('.popup-Iliuya');
            //console.log(data);
        },

        error : function(xhr, status) {
            myApp.alert('数据请求错误');
        }
    });
});

// 删除收藏(关注)的产品
function collect_delete_v(rec_id){
    $$.ajax({url: '/collect/collect_delete',async:false,dataType: "json",data: {rec_id:rec_id},success:function(msg){}});
}

function getCookie(c_name) {
	if (document.cookie.length>0) {
		c_start=document.cookie.indexOf(c_name + "=");
		if (c_start!=-1) {
			c_start=c_start + c_name.length+1;
			c_end=document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;
			return unescape(document.cookie.substring(c_start,c_end))
		}
	}
	return null;
}
function setCookie(objName,objValue,objHours){//添加cookie
    var str = objName + "=" + escape(objValue);
    if(objHours > 0){//为0时不设定过期时间，浏览器关闭时cookie自动消失
        var date = new Date();
        var ms = objHours*3600*1000;
        date.setTime(date.getTime() + ms);
        str += "; expires=" + date.toGMTString();
    }
    document.cookie = str;
}

function update_cart_num () {
    var cart_num=getCookie('cart_num');
    if(cart_num) {
        $$('#cart_num').html(cart_num);
        if (parseInt(cart_num) > 0) {
            $$('#cart_num').show();    
        };        
    }
}


//积分兑换现金券
function exchange_voucher(release_id,voucher_name){
    myApp.confirm('确认兑换 '+voucher_name+'?', function () {
        $$.ajax({
            url: '/account/exchange_voucher',
            async:false,
            dataType: "json",
            data: {release_id:release_id},
            success:function(result){
                console.log(result);
                if (result.msg_hd) {
                    myApp.alert(result.msg_hd);
                    return false;
                };
                if (result.msg_jf) {
                    myApp.alert(result.msg_jf);
                    return false;
                };
                if (result.msg_yes) {
                    myApp.alert(result.msg_yes);
                    history.go(-1);
                    location.reload();
                };
            }
        });
    });
}

//截取中英字符串 双字节字符长度为2 ASCLL字符长度为1
function cutStr(str, cutLen){
    var returnStr = '',    //返回的字符串
        reCN = /[^\x00-\xff]/,    //双字节字符
        strCNLen = str.replace(/[^\x00-\xff]/g,'**').length; 
    if(cutLen>=strCNLen){
        return str;
    }
    for(var i=0,len=0;len<cutLen;i++){
        returnStr += str.charAt(i);
        if(reCN.test(str.charAt(i))){
            len+=2;
        }else{
            len++;
        }
    }
    return returnStr;
}

// 限时抢购的 领取现金券活动
function v_special_campaign (release_id) {

    if(!checkLogin(false)){
        return false;
    }
    
    $$.ajax({
        url: '/special/special_voucher',
        async:false,
        dataType: "json",
        data: {release_id:release_id},
        success:function(result){
            // console.log(result);
            if (result.msg_hd) {
                myApp.alert(result.msg_hd);
                return false;
            };
            if (result.msg_ts || result.msg_tn || result.msg_min) {
                var modal = myApp.modal({
                    title:  '<p style="color:#F75555;margin-top:-15px;margin-left:-100px;">领取优惠券/代金券/积分</p>',
                    text:   '<p style="color:#F75555;margin-top:-15px;">恭喜您领取成功!</p>',
                    cssClass:'special-box',
                    afterText:  '<div class="swiper-container" style="width: auto; margin:5px -15px -15px">'+
                                    '<div class="swiper-wrapper">'+
                                        '<div class="swiper-slide">'+
                                            '<div class="youhuiquan-yb">有效期:'+result.msg_ts+'至'+result.msg_tn+'<br/>使用条件:单笔订单中指定商品金额满'+result.msg_min+'元</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>',
                    buttons: [
                        {
                            text:   '<p class="buttons-row">'+
                                    '<a href="#" class="button button-big button-raised" style="background-color:#F75555;color:#fff;height:35px;line-height:35px;">确定</a>'+
                                    '</p>'
                        }
                    ]
                })
                myApp.swiper($$(modal).find('.swiper-container'), {pagination: '.swiper-pagination'}); 
            };
        }
    });
}

// 指定商品优惠的 领取现金券活动
function v_product_voucher (release_id) {

    if(!checkLogin(false,false,'v_product_voucher('+release_id+');')){
        return false;
    }
    
    $$.ajax({
        url: '/special/special_voucher',
        async:false,
        dataType: "json",
        data: {release_id:release_id},
        success:function(result){
            if (result.error == 0) {
                myApp.alert(result.msg_hd);
                return false;
            };
            if (result.error == 1) {
                myApp.addNotification({
                    hold: 2000,
                    additionalClass: 'middle',
                    custom: '<div class="result-in"><img src="'+static_host+'mobile/img/fkcg.png" alt="" ><p style="padding:0 0 20px 0;text-align:center">现金券领取成功!</p></div>',
                    onClose: function(){myApp.closePanel();}
                });
            };
        }
    });
}

//login page not login modal box
var loginBtn = $('.sign-in .loginbtn').attr('disabled', "true").addClass('btn-disabled');

$('.sign-in input[name="authcode"]').on('input propertychange', function(){
  username = $('.sign-in input[name="username"]');
  if (checkYazhengma($(this).val())) {
    if (checkMobileNum(username.val())) {
      loginBtn.attr('disabled', false).removeClass('btn-disabled').addClass('active');
    };
  };
})

$('.sign-in input[name="password"]').on('input propertychange', function(){
  username = $('.sign-in input[name="username"]');
  password = $('.sign-in input[name="password"]');
  if (username.val() && password.val()) {
    loginBtn.attr('disabled', false).removeClass('btn-disabled').addClass('active');
  };
}) 


function switchLoginType() {
    loginType = 1 - loginType;
    //$('.yazhengma').attr("disabled", "true").addClass('inactive');
    if (loginType == 1) {
    $('.switchLogin').text('一键登录');
    $('.yazhengma-div').hide('slow');
    $('.sign-in input[name="username"]').attr('placeholder', '手机/用户名');
    $('.password-div').show('slow');
    };

    if (loginType == 0) {
    $('.switchLogin').text('会员直接登录');
    $('.yazhengma-div').show('slow');
    $('.sign-in input[name="username"]').attr('placeholder', '手机号');
    $('.password-div').hide('slow');
    };  
}

$('.sign-in .loginbtn').on('click', function(){
    username = $('.sign-in input[name="username"]').val();
    password = (loginType == 0 ? $('.sign-in input[name="authcode"]').val() : $('.sign-in input[name="password"]').val());

    $$.ajax({
        url:'/user/proc_loginAndRegister', 
        method:'POST', 
        dataType:'json', 
        data:{
          username:username,
          password:password,
          loginType:loginType
        }, 

        success:function(data){
        if (1==data.error)
          {
              myApp.addNotification({
                  message: data.message,
                  hold:2500
              });
          } else {                        
            sessionStorage.is_logined = 'logined';
            if (document.referrer) {
            location.href = document.referrer;
            } else {
            location.href = '/';
            }             
          }
        },
        error:function() {
          myApp.addNotification({
            message:'网络问题，深感抱歉',
            hold:2500
          });
        }
    })
});
// myApp.upscroller('回到顶部');

function GetRequest() {
    var url = location.search; //获取url中"?"符后的字串
    var theRequest = new Object();
    if (url.indexOf("?") != -1) {
        var str = url.substr(1);
        strs = str.split("&");
        for(var i = 0; i < strs.length; i ++) {
            theRequest[strs[i].split("=")[0]]=(strs[i].split("=")[1]);
        }
    }
    return theRequest;
}

//微信
function isWeiXin(){
    var ua = window.navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == 'micromessenger'){
        return true;
    }else{
        return false;
    }
}