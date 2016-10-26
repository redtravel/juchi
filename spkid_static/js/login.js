$(window).ready(function () {
	$('.hide').eq(0).remove();
});

var location_href = window.location.href;

function login_focus(el)
{
	var username = el;

	if(username.value=='Email地址或手机号码' )
	{
	username.value='';
	el.className='t_w217_sel black'
	}
	//username.className='normal_focus';

}

function login_blur(el)
{
	var username = el;
	if(username.value=='')
	{
		el.className='t_w217_sel gray'
		el.value='Email地址或手机号码';
	//username.value=defaultStr;
	//username.className='normal';
	}
}

function register_focus(el,show_text)
{	
	var id = el.id;
	var newid = id + '_error';
	var val = el.value;
	if (id=='r_password') {
		$('#passwordText').hide();
	}
	var text = show_text == null || show_text == "" ?'Email地址或手机号码':show_text;
	switch (val) {
		case text:
			el.value='';
			el.className = 't_w217_sel black';
			break;
	}
	if (location_href.indexOf('register')>0) {
		document.getElementById(newid).style.display = 'block';
	};
}

$(function () {
	//密码块的文字显示
	$('#passwordText').focus(function () {
		$(this).hide();
		$('#r_password').focus();
	});
	$('#passwordText2').focus(function () {
		$(this).hide();
		$('input[name=password]').focus();
	});
	$('input[name=password]').blur(function () {
		if ($(this).val()=='') {
			$('#passwordText2').show();
		};
	});
});

function register_blur(el,show_text)
{
	var id = el.id;
	var newid = id + '_error';
	var val = el.value;
	var text = show_text == null || show_text == "" ?'Email地址或手机号码':show_text;
	
	if (id=='r_email'&&el.value=='') {
		el.className = 't_w217_sel gray';
		el.value=text;
	} else if (id=='r_password'&&el.value=='') {
		el.className = 't_w217_sel gray';
		$('#passwordText').show();
	}
	if(id == 'r_password' && el.value != '') {
		$('#passwordText').hide();
	}
	//document.getElementById(newid).style.display = 'none';
}

function KeyDown(is_float)
{
	if(window.ActiveXObject)
	{
	if (event.keyCode == 13)
	{
		check_login_form();
	}
	}
}

function check_login_form()
{
	var remember = $('input[type=checkbox][name=remember]:checked').val();
	if(remember != 1){
		remember = 0;
	}
	var user_name = $('input[type=text][name=user_name]').val();
	var password = $('input[type=password][name=password]').val();

	$('input[type=text][name=user_name]').css("border-color","");
	$('input[type=password][name=password]').css("border-color","");

	var err_msg = '';
	if($.trim(user_name) =='' || $.trim(user_name) == 'Email地址或手机号码'){
		err_msg = "请输入用户账号";
		obj = $('input[type=text][name=user_name]');
	}

	if(err_msg == '' && $.trim(password) ==''){
		err_msg = "请输入用户密码";
		obj = $('input[type=password][name=password]');
	}

	if(err_msg != ''){
		if(obj)
		{
		obj.css("border-color","red");
		}
		$('#message').show();
		$('#message_inner').html(err_msg);
		return false;
	}
	else
	{
		$('#message').hide();
		$('#message_inner').html('');
	}

	$('input[type=button][name=submitlogin]').attr("disabled", "disabled");
	//var def_url = window.location.href;
		$.ajax({
		url:'/user/proc_login',
		data:{is_ajax:true,user_name:user_name,password:password,
			remember:remember,back_url:window.location.href,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.error==0){
				//if(result.back_url != '')
				//{
				//	window.location.href = result.back_url;
				//}else{
				//	window.location.href = def_url;
				//}
				change_login_satus(user_name);
				$('.lhgdg_xbtn_default').click();
			}else{
				$('#message').show();
				$('#message').html(result.message);
				$('input[type=button][name=submitlogin]').removeAttr("disabled");
			}
		},
                error: function(err){
                    location.reload();
                }
		});
	return false;
}

function check_register_form()
{
	var agreement = $('input[type=checkbox][name=agreement]:checked').val();
	if(agreement != 1)
	{
		agreement = 0;
	}
	var email = $('input[type=text][name=r_email]').val();
	var user_name = isMobile(email) ? email : email.substr(0, email.indexOf("@") > 12 ? 12 : email.indexOf("@")); // $('input[type=text][name=r_user_name]').val();
	var password = $('input[type=password][name=r_password]').val();
	var c_password = $('input[type=password][name=r_cpassword]').val();
        var captcha = $("#r_captcha").val();
	$('input[type=text][name=r_email]').css("border-color","");
	$('input[type=text][name=r_user_name]').css("border-color","");
	$('input[type=password][name=r_password]').css("border-color","");
	$('input[type=password][name=r_cpassword]').css("border-color","");

	var user_name_len = user_name.replace(/[^\x00-\xff]/g, "**").length;

	var obj = null;
	var regu = "^[0-9a-zA-Z]+$";
	var re = new RegExp(regu);
	var err_msg = '';

	if($.trim(email) =='' || $.trim(email) == 'Email地址或手机号码')
	{
		err_msg = "请输入注册账号";
		obj = $('input[type=text][name=r_email]');
                _error("r_email");
	}

	if(err_msg == '' && isEmail(email) == false)
	{
		if( /^ *$/.test(email) || !/^ *(\d){11} *$/.test(email) )
		{
		err_msg = '请正确输入Email地址或者手机号码';
		obj = $('input[type=text][name=r_email]');
                _error("r_email");
		}
	}

	if(err_msg == '' && $.trim(user_name) =='')
	{
		err_msg = "请输入昵称";
		obj = $('input[type=text][name=r_user_name]');
	}

	if(err_msg == '' && user_name_len < 3)
	{
		err_msg = '您的昵称过短，不能少于3个字符';
		obj = $('input[type=text][name=user_name]');
	}

	if(err_msg == '' && user_name_len > 12)
	{
		err_msg = '您的昵称过长，不能超过12个字符';
		obj = $('input[type=text][name=user_name]');
	}

	if(err_msg == '' && $.trim(password) =='')
	{
		err_msg = "请输入密码";
		obj = $('input[type=password][name=r_password]');
                _error("r_password");
	}

	if(err_msg == '')
	{
		if (!re.test(password)){
		err_msg = "无效的密码,只允许6-16位的字母或数字";
		obj = $('input[type=password][name=r_password]');
                _error("r_password");
		}
		if(password.length < 6 || password.length > 16)
		{
		err_msg = "无效的密码,只允许6-16位的字母或数字";
		obj = $('input[type=password][name=r_password]');
                _error("r_password");
		}
	}

	if(err_msg == '' && $.trim(c_password) =='')
	{
		err_msg = "请输入确认密码";
		obj = $('input[type=password][name=r_cpassword]');
                _error("r_cpassword");
	}

	if(err_msg == '' && $.trim(password) != $.trim(c_password))
	{
		err_msg = "两次输入密码不一致";
		obj = $('input[type=password][name=r_cpassword]');
                _error("r_cpassword");
	}

        if (err_msg == '' && (captcha.length != 4 || !captcha.match(/^[0-9a-zA-Z]+$/))) {
            err_msg = "请输入验证码";
            obj = $('input[type=password][name=r_captcha]');
            _error("r_captcha");
        }
        
	if(err_msg == '' && agreement != 1)
	{
		err_msg = "您没有接受协议";
	}

	if(err_msg != '')
	{
		if(obj)
		{
		obj.css("border-color","red");
		}
		$('#r_message').show();
		$('#r_message_inner').html(err_msg);
		return false;
	}
	else
	{
		$('#r_message').hide();
		$('#r_message_inner').html('');
	}

	$('input[type=button][name=submitregister]').attr("disabled", "disabled");


		$.ajax({
		url:'/user/proc_register',
		data:{is_ajax:true,email:email,user_name:user_name,password:password,rnd:new Date().getTime(),captcha:captcha},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.error==0){
			if(result.back_url != '')
			{
				window.location.href = result.back_url;
			}else
			{
				window.location.href = '/user';
			}

			}else{
			$('#r_message').show();
			$('#r_message').html(result.message);
			$('input[type=button][name=submitregister]').removeAttr("disabled");
			}
		},
                error: function(err){
                    location.reload();
                }
		});

	return false;
}


function check_comp_info()
{
	$('#r_message_').hide();
	
	var email = $('input[type=text][name=r_email]').val();
	var user_name = isMobile(email) ? email : email.substr(0, email.indexOf("@") > 12 ? 12 : email.indexOf("@")); // $('input[type=text][name=r_user_name]').val();
	var password = $('input[type=password][name=r_password]').val();
	var c_password = $('input[type=password][name=r_cpassword]').val();
        var captcha = $("#r_captcha").val();
	$('input[type=text][name=r_email]').css("border-color","");
	$('input[type=text][name=r_user_name]').css("border-color","");
	$('input[type=password][name=r_password]').css("border-color","");
	$('input[type=password][name=r_cpassword]').css("border-color","");

	var user_name_len = user_name.replace(/[^\x00-\xff]/g, "**").length;

	var obj = null;
	var regu = "^[0-9a-zA-Z]+$";
	var re = new RegExp(regu);
	var err_msg = '';

	if($.trim(email) =='' || $.trim(email) == '此账号可直接登录演示站!')
	{
		err_msg = "请输入注册账号";
		obj = $('input[type=text][name=r_email]');
                _error("r_email");
	}

	if(err_msg == '' && isEmail(email) == false)
	{
		if( /^ *$/.test(email) || !/^ *(\d){11} *$/.test(email) )
		{
		err_msg = '请正确输入Email地址或者手机号码';
		obj = $('input[type=text][name=r_email]');
                _error("r_email");
		}
	}

	if(err_msg == '' && $.trim(user_name) =='')
	{
		err_msg = "请输入昵称";
		obj = $('input[type=text][name=r_user_name]');
	}

	if(err_msg == '' && user_name_len < 3)
	{
		err_msg = '您的昵称过短，不能少于3个字符';
		obj = $('input[type=text][name=user_name]');
	}

	if(err_msg == '' && user_name_len > 12)
	{
		err_msg = '您的昵称过长，不能超过12个字符';
		obj = $('input[type=text][name=user_name]');
	}

	if(err_msg == '' && $.trim(password) =='')
	{
		err_msg = "请输入密码";
		obj = $('input[type=password][name=r_password]');
                _error("r_password");
	}

	if(err_msg == '')
	{
		if (!re.test(password)){
		err_msg = "无效的密码,只允许6-16位的字母或数字";
		obj = $('input[type=password][name=r_password]');
                _error("r_password");
		}
		if(password.length < 6 || password.length > 16)
		{
		err_msg = "无效的密码,只允许6-16位的字母或数字";
		obj = $('input[type=password][name=r_password]');
                _error("r_password");
		}
	}

	if(err_msg == '' && $.trim(c_password) =='')
	{
		err_msg = "请输入确认密码";
		obj = $('input[type=password][name=r_cpassword]');
                _error("r_cpassword");
	}

	if(err_msg == '' && $.trim(password) != $.trim(c_password))
	{
		err_msg = "两次输入密码不一致";
		obj = $('input[type=password][name=r_cpassword]');
                _error("r_cpassword");
	}

	if(err_msg != '')
	{
		if(obj)
		{
		obj.css("border-color","red");
		}
		$('#r_message').show();
		$('#r_message_inner').html(err_msg);
		return false;
	}
	else
	{
		$('#r_message').hide();
		$('#r_message_inner').html('');
	}

	$('div[type=button][name=submitregister]').removeAttr("onclick");
		$.ajax({
		url:'/user/comp_info',
		data:{is_ajax:true,email:email,user_name:user_name,password:password,rnd:new Date().getTime(),captcha:captcha},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.error==0){
				if(result.back_url != '')
				{
					window.location.href = result.back_url;
				}else
				{
					window.location.href = '/user';
				}
			}else{
				//alert(result.message);
				$('#r_email_error_r').show();
				$('#r_email_error_r').html(result.message);
				//$('div[type=button][name=submitregister]').removeAttr("disabled");
				$('div[type=button][name=submitregister]').attr("onclick", "check_comp_info();");
			}
		},
                error: function(err){
                    location.reload();
                }
		});

	return false;
}


function referToUsername(el)
{
	var value = el.value.split('@')[0];
	value = value.substring(0,12);
	$('input[type=text][name=r_user_name]').val(value);
}

function isEmail(email)
{
	var reg1 = /([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)/;
	if (reg1.test( email ) && email.length<51)
	{
	return true;
	} else
	{
	return false;
	}
}

function isMobile(mobile) {
        var reg1 = /^1[3|4|5|8][0-9]\d{4,8}/;
	if (reg1.test( mobile ) && mobile.length == 11)
	{
	return true;
	} else
	{
	return false;
	}
}

function get_password()
{
	if (document.getElementById('mobile_area').style.display == 'block')
	{
	return false;
	}

	var user_name = $('input[type=text][name=user_name]').val();
	if($.trim(user_name) == '' || user_name == 'Email地址或手机号码')
	{
	alert('请填写你注册的邮箱或者手机');
	return false;
	}
	$.ajax({
		url:'/user/find_password',
		data:{is_ajax:true,user_name:user_name,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
                        if (result == '') return;
			if(result.error==0){
			if (result.msg == '')
			{
				//var parent_dom = $("div#mobile_area");
				//parent_dom.show();
				//parent_dom.html(result.content);
				var dg = new $.dialog({ id:'thepanel',maxBtn:false, title:'找回密码',iconTitle:false,cover:true,html:$('#mobile_area')[0] });
				dg.ShowDialog();
				document.getElementById('validation').src = img_src + Math.random();
				document.getElementById('yanzhen').value = '';
				dg.addBtn('ok','确定',function(){get_phone_password(dg)});

			} else
			{
				alert(result.msg);
			}

			}else{
			alert(result.msg);
			}
		}
		});
	return false;
}

function get_phone_password(dg)
{
	var user_name = $('input[type=text][name=user_name]').val();
	var yanzhen = $('input[type=text][id=yanzhen]').val();
	if($.trim(user_name) == '' || user_name == 'Email地址或手机号码')
	{
	alert('请填写你注册的邮箱或者手机');
	return false;
	}
	if($.trim(yanzhen) == '')
	{
	alert('请填写正确的验证码');
	return false;
	}
	$.ajax({
		url:'/user/find_mobile_password',
		data:{is_ajax:true,user_name:user_name,yanzhen:yanzhen,rnd:new Date().getTime()},
		dataType:'json',
		type:'POST',
		success:function(result){
			if(result.error==0){
			alert(result.msg);
			dg.cancel();

			}else{
			alert(result.msg);
			}
		}
		});
	return false;
}

function wrap_phone()
{
	document.getElementById('mobile_area').style.display = 'none';
}

function toQzoneLogin()
{
	location.href = '/user/qq_login';
	//var A=window.open("/user/qq_login","TencentLogin","width=450,height=320,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1");
}

function toQzoneLogin()
{
	location.href = '/user/qq_login';
	//var A=window.open("/user/qq_login","TencentLogin","width=450,height=320,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1");
}

var img_src = '/user/show_verify/';
function _onFocus(obj) {
    var id = obj.id;
    $("#" + id + "_success").css("display", "none");
    $("#" + id + "_error").css("display", "none");
}
function _onBlur(obj) {
    var id = obj.id;
    if (_valid(id)) {
        _success(id);
    } else {
        _error(id);
    }
}
function _error(id) {
    $("#" + id + "_success").css("display", "none");
    $("#" + id + "_error").css("display", "");
}
function _success(id) {
    $("#" + id + "_success").css("display", "");
    $("#" + id + "_error").css("display", "none");
}
function _valid(id) {
    switch(id) {
        case "r_email":
            return isEmail($("#" + id).val()) || isMobile($("#" + id).val());
            break;
        case "r_password":
            var pwd = $.trim($("#" + id).val());
            if (pwd.length < 6 || pwd.length > 16 || !pwd.match(/^[0-9a-zA-Z]+$/)) return false;
            return true;
            break;
        case "r_cpassword":
            var pwd = $.trim($("#r_password").val());
            var cpwd = $.trim($("#" + id).val());
            return pwd == cpwd;
            break;
        case "r_captcha":
            var captcha = $.trim($("#" + id).val());
            if (captcha.length != 4 || !captcha.match(/^[0-9a-zA-Z]+$/)) return false;
            return true;
            break;
    }
    return true;
}
