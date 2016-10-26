<?php

function make_nonceStr()
{
	$codeSet = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for ($i = 0; $i<16; $i++) {
		$codes[$i] = $codeSet[mt_rand(0, strlen($codeSet)-1)];
	}
	$nonceStr = implode($codes);
	return $nonceStr;
}

function make_signature($nonceStr,$timestamp,$jsapi_ticket,$url)
{
	$tmpArr = array(
	'noncestr' => $nonceStr,
	'timestamp' => $timestamp,
	'jsapi_ticket' => $jsapi_ticket,
	'url' => $url
	);
	ksort($tmpArr, SORT_STRING);
	$string1 = http_build_query( $tmpArr );
	$string1 = urldecode( $string1 );
	$signature = sha1( $string1 );
	return $signature;
}

function make_ticket($appId, $appsecret)
{
	$CI = &get_instance();
	$CI->load->library('memcache');
	// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
    
	$data = json_decode($CI->memcache->get('access_token'));
	if ($data == FALSE) {
		$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$appsecret;

		$json = file_get_contents($TOKEN_URL);
		$result = json_decode($json, true);
		
		$access_token = $result['access_token'];
		if ($access_token) {
			$data->expire_time = time() + 7000;
			$data->access_token = $access_token;
            
            $CI->memcache->save('access_token', json_encode($data), 7000);
            
            //$fp = fopen("access_token.json", "w");
            //fwrite($fp, json_encode($data));
            //fclose($fp);
		}	
	} else if($data->expire_time < time()) {
		$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appId."&secret=".$appsecret;
		$json = file_get_contents($TOKEN_URL);
		$result = json_decode($json,true);
		$access_token = $result['access_token'];
		if ($access_token) {
			$data->expire_time = time() + 7000;
			$data->access_token = $access_token;
            
            $CI->memcache->save('access_token', json_encode($data), 7000);
            
            //$fp = fopen("access_token.json", "w");
            //fwrite($fp, json_encode($data));
            //fclose($fp);
		}
	}else{
		$access_token = $data->access_token;
	}

	// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
    //$data = json_decode(file_get_contents("jsapi_ticket.json"));
    
    
    $data = json_decode($CI->memcache->get('jsapi_ticket'));
    
	if ($data->expire_time < time()) {
		$ticket_URL="https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=".$access_token."&type=jsapi";
		$json = file_get_contents($ticket_URL);
		$result = json_decode($json,true);
		$ticket = $result['ticket'];
		if ($ticket) {
			$data->expire_time = time() + 7000;
			$data->jsapi_ticket = $ticket;
            //$fp = fopen("jsapi_ticket.json", "w");
            //fwrite($fp, json_encode($data));
            //fclose($fp);
            
            $CI->memcache->save('jsapi_ticket', json_encode($data), 7000);
		}
	}else{
		$ticket = $data->jsapi_ticket;
	}

	return $ticket;
}

function get_weixin_config() {    	
	$weixin_config = array(
		'wx_appId' => 'wxd11be5ecb1367bcf',
		'wx_appsecret' => '6d05ab776fd92157d6833e2936d6f17c',
		'wx_timestamp' => time()			
	);

	$weixin_config['wx_jsapi_ticket'] = make_ticket($weixin_config['wx_appId'], $weixin_config['wx_appsecret']);
	$weixin_config['wx_nonceStr'] = make_nonceStr();
	$weixin_config['wx_url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$weixin_config['wx_signature'] = make_signature($weixin_config['wx_nonceStr'],$weixin_config['wx_timestamp'], $weixin_config['wx_jsapi_ticket'], $weixin_config['wx_url']);	    
	return $weixin_config;	
}

?>

