<?php

/*
    方倍工作室 http://www.fangbei.org/
    CopyRight 2014 All Rights Reserved
*/

/*
	require_once('weixin.class.php');
	$weixin = new class_weixin();
*/

define('APPID',         "wxd11be5ecb1367bcf");
define('APPSECRET',		"6d05ab776fd92157d6833e2936d6f17c");

class Lib_weixin
{
	var $appid = APPID;
	var $appsecret = APPSECRET;

    //构造函数，获取Access Token
	public function __construct($appid = NULL, $appsecret = NULL)
	{
        if($appid && $appsecret){
            $this->appid = $appid;
			$this->appsecret = $appsecret;
        }

		

        //3. 本地写入
		$res = file_get_contents('access_token.json');
		$result = json_decode($res, true);
        $this->expires_time = $result["expires_time"];
        $this->access_token = $result["access_token"];

        if (time() > ($this->expires_time + 3600)){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
            $res = $this->http_request($url);
            $result = json_decode($res, true);
            $this->access_token = $result["access_token"];
            $this->expires_time = time();
			file_put_contents('access_token.json', '{"access_token": "'.$this->access_token.'", "expires_time": '.$this->expires_time.'}');
        }

	}

    /*
    *  PART4 JS SDK 签名
	*  PHP仅用于获得签名包，需要配合js一起使用
    */
	// require_once('weixin.class.php');
	// $weixin = new class_weixin();
	// $signPackage = $weixin->GetSignPackage();

	//生成长度16的随机字符串
    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

	//获得JS API的ticket
    private function getJsApiTicket() 
	{
		//2. 写文件，有改动，待测试 20150430
		$res = file_get_contents('jsapi_ticket.json');
		$result = json_decode($res, true);
		$this->jsapi_ticket = $result["jsapi_ticket"];
        $this->jsapi_expire = $result["jsapi_expire"];

        if (time() > ($this->jsapi_expire + 3600)){
            $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=".$this->access_token;
            $res = $this->http_request($url);
            $result = json_decode($res, true);
            $this->jsapi_ticket = $result["ticket"];
            $this->jsapi_expire = time();
			file_put_contents('jsapi_ticket.json', '{"jsapi_ticket": "'.$this->jsapi_ticket.'", "jsapi_expire": '.$this->jsapi_expire.'}');
        }
		return $this->jsapi_ticket;
    }

	//获得签名包
    public function getSignPackage() {
        $jsapiTicket = $this->getJsApiTicket();
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $timestamp = time();
        $nonceStr = $this->createNonceStr();
        $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
        $signature = sha1($string);
        $signPackage = array(
							"appId"     => $this->appid,
							"nonceStr"  => $nonceStr,
							"timestamp" => $timestamp,
							"url"       => $url,
							"signature" => $signature,
							"rawString" => $string
							);
        return $signPackage;
    }

    //HTTP请求（支持HTTP/HTTPS，支持GET/POST）
    protected function http_request($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

	//日志记录
    private function logger($log_content)
    {
        if(isset($_SERVER['HTTP_APPNAME'])){   //SAE
            sae_set_display_errors(false);
            sae_debug($log_content);
            sae_set_display_errors(true);
        }else if($_SERVER['REMOTE_ADDR'] != "127.0.0.1"){ //LOCAL
            $max_size = 500000;
            $log_filename = "log.xml";
            if(file_exists($log_filename) and (abs(filesize($log_filename)) > $max_size)){unlink($log_filename);}
            file_put_contents($log_filename, date('Y-m-d H:i:s').$log_content."\r\n", FILE_APPEND);
        }
    }
}
