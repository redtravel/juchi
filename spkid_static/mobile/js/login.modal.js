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
    $('.yazhengma').text("请在" + curCount + "秒内输入验证码");
    InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次    
}
function resetSendYazhengma() {
  window.clearInterval(InterValObj);//停止计时器
  $('.yazhengma').removeAttr("disabled").removeClass('inactive');//启用按钮
  $('.yazhengma').text("重新发送验证码");
}
//timer处理函数
function SetRemainTime() {
    if (curCount == 0) {                
        resetSendYazhengma();
    }
    else {
        if (/\d{6}/.test($('input[name="authcode"]').val())) {
          resetSendYazhengma();
          var loginBtn = $('.login-box .modal-button:nth-child(2)').removeAttr('disabled').removeClass('inactive');   
          return;
        };
        curCount--;
        $('.yazhengma').text("请在" + curCount + "秒内输入验证码");
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
  var mobile = $('.login-box input[name="username"]').val();
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
        resetSendYazhengma();
      } else {
        myApp.addNotification({
          message:data.msg_send_result
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

function login(){

    myApp.yywLogin(false,'登录',function(username, password){
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
                      message: data.message
                  });
              } else {
                  //location.href='/index';       
                  myApp.closeModal('.login-box');
                  $$('.modal-overlay.modal-overlay-visible').remove();
              }
            },
            error:function() {
              myApp.addNotification({
                message:'网络问题，深感抱歉'
              });
            }
        })
      }
      ,function(username, password){
        loginType = 1 - loginType;
        $('.yazhengma').attr("disabled", "true").addClass('inactive');
        if (loginType == 1) {
          $('.modal-button:nth-child(1)').text('使用手机直接登陆');
          $('.yazhengma-div').hide('slow');
          $('.login-box input[name="username"]').attr('placeholder', '手机/用户名');
          $('.mima-div').show('slow');
        };

        if (loginType == 0) {
          $('.modal-button:nth-child(1)').text('使用账号密码登陆');
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