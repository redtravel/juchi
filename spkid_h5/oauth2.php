<?php 
error_reporting(0);
require('./application/libraries/wechatauth.php');

	if(isset($_GET['code'])){
		echo $_GET['code'];
		$Wechat = new wechatauth();
		       
		$token = $Wechat->get_access_token('wxd11be5ecb1367bcf','6d05ab776fd92157d6833e2936d6f17c',$_GET['code']); //确认授权后会，根据返回的code获取token

		$user_info = $Wechat->get_user_info($token['access_token'], $token['openid']); 
		
	    //var_export($user_info);
	}else{
		echo "NO CODE";
	}
?>