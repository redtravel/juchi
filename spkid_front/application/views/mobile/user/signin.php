<?php include APPPATH."views/mobile/header.php"; ?>
    <link rel="stylesheet" href="<?php echo static_style_url('mobile/css/tank.css?v=version')?>">
        <div class="view signin view-main" data-page="<?php echo $first_page;?>">
    <div class="pages">
<div class="page cached" data-page="register-step3">
<div class="navbar">
                <div class="navbar-inner">
<div class="left"><a href="#" class="link back"><i class="icon icon-back"></i></a></div>
                    <div class="center">请输入密码</div>
                </div>
            </div>
        <div class="page-content">
          <form action="/user/proc_register" method="POST" class="ajax-submit login-form">
            <div class="list-block">
              <ul>
                <li class="item-content">
                  <div class="item-inner">
                    <div class="item-input">
                      <input type="text" name="user_name" class="text-input" value="" placeholder="用户名" style="padding-left:10px;">
                      <input id="password" type="password" name="password" placeholder="密码" style="padding-left:10px;">
                    </div>
                  </div>
                </li>
              </ul>
            </div><div class="content-block">
<a href="#login" class="button button-raised button-fill color-indigo">确定</a>
            </div>
          </form>
        </div>
      </div>
      <div class="page <?php if( $first_page !=='register-step1' ) echo 'cached'; ?>" data-page="register-step1">
<div class="navbar">
                <div class="navbar-inner">
<div class="left sliding"><a href="#" class="link back history-back"><i class="icon icon-back"></i></a></div>
                    <div class="center sliding">注册演示站</div>
                </div>
            </div>
        <div class="page-content">
          <form class="login-form">
            <div class="list-block">
              <ul>
<li class="item-content">
                  <div class="item-inner">
                    <div class="item-input">
                      <input type="tel" name="mobile" class="text-input" value="" placeholder="手机号" style="padding-left:10px;">
                      <!--<input type="text" name="captcha" class="text-input" value="" placeholder="验证码" style="padding-left:10px;"><div><img id="verify" src="/user/show_verify"/></div>-->
                      <input type="hidden" name="session_id" value="<?php echo $session_id;?>" >

                    </div>
                  </div>
                </li>
              </ul>
            </div>
<div class="content-block">
<a href="#register-step2" class="button button-raised button-fill color-indigo">下一步</a>
            </div>
          </form>
        </div>
      </div>
<div class="page cached" id="send_code" data-page="send_code">
<div class="navbar">
                <div class="navbar-inner">
<div class="left sliding"><a href="#" class="link icon-only back"><i class="icon icon-back"></i></a></div>
                    <div class="center">请输入验证码</div>
                </div>
            </div>
        <div class="page-content">

          <form class="login-form">
            <div class="list-block">
              <ul>
<li class="item-content">
                  <div class="item-inner">
                    <div class="item-input">
                      <input type="tel" name="authcode" placeholder="验证码" style="padding-left:10px;"><a href="" class="button button-fill color-orange external reg_code">重新获取</a>
                    </div>
                    <div class="item-text send_res"></div>
                  </div>
                </li>
              </ul>
            </div>
<div class="content-block">
<a href="#enter_password" class="button button-raised button-fill color-indigo">下一步</a>
            </div>
          </form>
        </div>
      </div>
<div class="page cached" id="register-step2" data-page="register-step2">
<div class="navbar">
                <div class="navbar-inner">
<div class="left sliding"><a href="#" class="link icon-only back"><i class="icon icon-back"></i></a></div>
                    <div class="center">请输入验证码</div>
                </div>
            </div>
        <div class="page-content">

          <form class="login-form">
            <div class="list-block">
              <ul>
<li class="item-content">
                  <div class="item-inner">
                    <div class="item-input">
                      <input type="tel" name="authcode" placeholder="验证码"><a href="" class="button button-fill color-orange external reg_code">重新获取</a>
                    </div>
                    <div class="item-text send_res"></div>
                  </div>
                </li>
              </ul>
            </div>
<div class="content-block">
<a href="#register-step3" class="button button-raised button-fill color-indigo">下一步</a>
            </div>
          </form>
        </div>
      </div>
<div class="page <?php if( $first_page !=='find_password' ) echo 'cached'; ?>" data-page="find_password">
<div class="navbar">
                <div class="navbar-inner">
<div class="left sliding"><a href="#" class="link icon-only history-back"><i class="icon icon-back"></i></a></div>
                    <div class="center sliding">找回密码</div>
                </div>
            </div>
        <div class="page-content">
          <form class="login-form">
            <div class="list-block">
              <ul>
<li class="item-content">
                  <div class="item-inner">
                    <div class="item-input">
                      <input type="tel" name="mobile" class="text-input" value="" placeholder="手机号" style="padding-left:10px;">
                    </div>
                  </div>
                </li>
              </ul>
            </div>
<div class="content-block">
<a href="#send_code" class="button button-raised button-fill color-indigo">下一步</a>
            </div>
          </form>
        </div>
      </div>
<div class="page cached" data-page="enter_password">
<div class="navbar">
                <div class="navbar-inner">
<div class="left"><a href="#" class="link back"><i class="icon icon-back"></i></a></div>
                    <div class="center">请输入新密码</div>
                </div>
            </div>
        <div class="page-content">
          <form action="/user/proc_register" method="POST" class="ajax-submit login-form">
            <div class="list-block">
              <ul>
                <li class="item-content">
                  <div class="item-inner">
                    <div class="item-input">
                      <input id="newpsw" type="password" name="newpsw" placeholder="密码" style="padding-left:10px;">
                    </div>
                  </div>
                </li>
              </ul>
            </div><div class="content-block">
<a href="#login" class="button button-raised button-fill color-indigo">确定</a>
            </div>
          </form>
        </div>
      </div>
    </div><!-- pages -->
</div>
</div>
<script type="text/javascript" src="<?php echo static_style_url('mobile/js/framework7.js?v=version')?>"></script>
<script>
function isEmpty(dom){
    if ('' == $$(dom).val())
        return true;
    return false;
}
var $$ = Dom7;
var v_submit_flag = false;
var myApp = new Framework7({
    material: true, 
        pushState: true, 
        pushStateSeparator: '',
        //pushStateRoot: 'http://f.test.com',
        modalTitle: '', 
        activeState: false,
        modalButtonOk: '确定', 
        modalButtonCancel: '取消',
        onAjaxStart: function () {
        myApp.showIndicator();
    },
    onAjaxComplete:function(xhr){
        myApp.hideIndicator();
    },
        preroute: function (view, options) {
            if (options.url == '#') return;
            var page = options.pageName;
            var prevPage = view.activePage.name;
            //alert(page);
            var self = this;
            
            if ('register-step2' == page || 'send_code' == page){
                if (v_submit_flag) {
                    myApp.alert('请不要重复提交', '');
                    return false;
                }
                v_submit_flag = true;
                var mobile = $$('.page[data-page="'+prevPage+'"] input[name="mobile"]').val();
                //var captcha = $$('.page[data-page="'+prevPage+'"] input[name="captcha"]').val();
                var session_id = $$('.page[data-page="'+prevPage+'"] input[name="session_id"]').val();
/*
                if (4 != captcha.length){
                    myApp.alert('验证码错误。')
                    return false;
                }
*/

                var data = {};
                data.mobile = mobile;
                //data.captcha = captcha;
                data.session_id= session_id;
                if ('register-step2' == page){
                    data.is_register = true;
                }               
                
                $$.ajax({url:'/user/reg_auth',async:false,dataType:'json',data:data,  
                 success:function(data){                   
                    //手机格式正确
                    if(0 == data.mobile_check_err){
                        if (0 == data.msg_send_result){
                            $$('.send_res').html('验证码已发至: '+mobile);
                        } else{
                            $$('.send_res').html(data.msg_send_result);
                        }
                        self.go = true;
                    }else{
                        myApp.alert(null, [data.mobile_check_err]);
                        self.go = false;
                    }
                }, 
                complete:function(){
                    v_submit_flag = false;
                }
                });

            } else if('register-step3' == page || 'enter_password' == page) {
                if (v_submit_flag) {
                    myApp.alert('请不要重复提交', '');
                    return false;
                }
                v_submit_flag = true;                
                var authcode = $$('#'+prevPage+' input[name="authcode"]').val();
                $$.ajax({url:'/user/verify_msgcode', async:false, data:{authcode:authcode}, success:function(data){
                    if (0 == data){
                        self.go = true;
                    } else{
                        myApp.alert(null, [data]);
                        self.go = false;
                    }

                }, 
                complete:function(){
                    v_submit_flag = false;
                }
                })
            } else if('register-step3' == prevPage) {
                if (isEmpty('#password')){
                    myApp.alert('密码不能为空!');
                    return false;
                }
                
                if (v_submit_flag) {
                    myApp.alert('请不要重复提交');
                    return false;
                }
                v_submit_flag = true;
                
                var user_name = $$('input[name=user_name]').val();
                var password = $$('#password').val();
                
                $$.ajax({url:'/user/proc_register', method:'POST', data:{user_name:user_name, password:password}, success:function(data){
                    if ('success' == data.substr(0, 7)){
                        myApp.alert(data.substr(8), '注册成功!', function(){
                            location.href = '/user/profile';
                        });
                        //login();
                        self.go = true;
                    } else{
                        myApp.alert('', data);
                        self.go = false;
                    }

                }, 
error:function(data){
myApp.alert('网络链接出错!');
console.log(data)
v_submit_flag = false;
},
                complete:function(){
                    v_submit_flag = false;
                }
                })
            } else if('enter_password' == prevPage) {
                if (v_submit_flag) {
                    myApp.alert('请不要重复提交', '');
                    return false;
                }
                v_submit_flag = true;                
                var password = $$('#newpsw').val();
                $$.ajax({url:'/user/new_password', method:'POST', data:{password:password}, success:function(data){
                    if (data){
                        myApp.alert('密码修改成功!');
                        login();
                        self.go = true;
                    } else {
                        myApp.alert('密码修改失败!');
                        self.go = false;
                    }
                }, 
                complete:function(){
                    v_submit_flag = false;
                }
                })
            }
            return self.go;
        }//preroute
})

var signView = myApp.addView('.signin', {
    domCache: true
});
$$('.history-back').on('click', function(e){
    history.go(-1);
});
//myApp.init();
//myApp.initPage('#login-page');
    /*$$('#login-page input[type="password"]').blur(function(){
        if (isEmpty('#login-page input[type="password"]'))
    })*/
$$('#login-submit').click(function(e){
    e.preventDefault();
    $$('#login-page form.ajax-submit').trigger('submit');
})
$$('#login-page form.ajax-submit').on('submitted', function (e) {
    //alert('submitted');
    var data = e.detail.data;
    if (1==data.error){
        myApp.alert(null, [data.message]);
    } else {
        //转个人中心
        location.href = '/user/';
    }

})
    function login(){
        myApp.yywLogin(false,'登录演示站',function(username, password){

            $$.ajax({url:'/user/proc_login', method:'POST', dataType:'json', data:{username:username,password:password}, success:function(data){
                if (1==data.error)
                {
                    app.addNotification({
                        message: data.message
                    });
                } else {
                    location.href='/index';                   
                }
            }
            })}
                ,false,'login-box');

    }    
$$('#verify').click(function(e){
    $$(this).attr('src', '/user/show_verify?'+Math.random());
});
myApp.onPageInit('register-step2 send_code', function (page) {
    var btn = '#'+page.name+' .reg_code';
    function smscount(n){
        n--;
        if (n<0){
            $$(btn).html('重新获取');
            checked=false;
        } else { 
            $$(btn).html('重新获取('+n+')');
            setTimeout(function(){smscount(n)},1000);
        }
    }
    //function requestCode()
    //$$('a[href="register-step3"]').attr('href', 'enter_password');
smscount(60);
checked=true;

$$('#'+page.name).on('click', '.reg_code', function(e){
    e.preventDefault();
    //$$('a[href="register-step3"]').attr('href', 'enter_password');
    if (checked) return;
    checked=true;
    $$.getJSON('/user/reg_auth', {retry:1}, function(data){
        if (0 == data.msg_send_result){
            smscount(60);
        } else {
            $$(btn).html('重新获取');
            checked=false;
        }
    })
})
    
    $$('.page[data-page="register"] form.ajax-submit').on('submitted', function (e) {
        var data = e.detail.data;
        if (1==data.error){
            myApp.alert(data.message);
        } else {
            //转登录页面
            location.href = '/user/login';
        }

    })

})
</script>
<?php include APPPATH."views/mobile/footer.php"; ?>
