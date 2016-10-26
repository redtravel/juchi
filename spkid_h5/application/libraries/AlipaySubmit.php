<?php
/* *
 * 类名：AlipaySubmit
 * 功能：支付宝各接口请求提交类
 * 详细：构造支付宝各接口表单HTML文本，获取远程HTTP数据
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
 */
//require_once(APPPATH . "alipay/lib/alipay_core.function.php");
//require_once(APPPATH . "alipay/lib/alipay_rsa.function.php");

class CI_AlipaySubmit {

	var $alipay_config;
	var $alipay_gateway_new = 'https://mapi.alipay.com/gateway.do?';
	/**
     * HTTPS形式消息验证地址
     */
	var $https_verify_url = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';
	/**
     * HTTP形式消息验证地址
     */
	var $http_verify_url = 'http://notify.alipay.com/trade/notify_query.do?';

	function __construct(){
		$this->CI=&get_instance();
		$this->alipay_config = $this->CI->config->item('alipay_new');
		$this->alipay_config['return_url'] = site_url('pay/alipay/return');
		$this->alipay_config['notify_url'] = site_url('pay/alipay/notify');
		$this->alipay_config['login_url'] = site_url('user/alipay_callback');
		$this->alipay_config['recharge_return_url'] = site_url('pay/recharge/return');
		$this->alipay_config['recharge_notify_url'] = site_url('pay/recharge/notify');
	}
	
    function AlipaySubmit() {
    	$this->__construct();
    }

    /* *
     * 支付宝接口公用函数
     * 详细：该类是请求、通知返回两个文件所调用的公用函数核心处理文件
     * 版本：3.3
     * 日期：2012-07-19
     * 说明：
     * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
     * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
     */

    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    function createLinkstring($para) {
    	$arg  = "";
    	while (list ($key, $val) = each ($para)) {
    		$arg.=$key."=".$val."&";
    	}
    	//去掉最后一个&字符
    	$arg = substr($arg,0,count($arg)-2);
    	
    	//如果存在转义字符，那么去掉转义
    	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
    	
    	return $arg;
    }
    /**
     * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
     * @param $para 需要拼接的数组
     * return 拼接完成以后的字符串
     */
    function createLinkstringUrlencode($para) {
    	$arg  = "";
    	while (list ($key, $val) = each ($para)) {
    		$arg.=$key."=".urlencode($val)."&";
    	}
    	//去掉最后一个&字符
    	$arg = substr($arg,0,count($arg)-2);
    	
    	//如果存在转义字符，那么去掉转义
    	if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
    	
    	return $arg;
    }
    /**
     * 除去数组中的空值和签名参数
     * @param $para 签名参数组
     * return 去掉空值与签名参数后的新签名参数组
     */
    function paraFilter($para) {
    	$para_filter = array();
    	while (list ($key, $val) = each ($para)) {
    		if($key == "sign" || $key == "sign_type" || $val == "")continue;
    		else	$para_filter[$key] = $para[$key];
    	}
    	return $para_filter;
    }
    /**
     * 对数组排序
     * @param $para 排序前的数组
     * return 排序后的数组
     */
    function argSort($para) {
    	ksort($para);
    	reset($para);
    	return $para;
    }
    /**
     * 写日志，方便测试（看网站需求，也可以改成把记录存入数据库）
     * 注意：服务器需要开通fopen配置
     * @param $word 要写入日志里的文本内容 默认值：空值
     */
    function logResult($word='') {
    	$fp = fopen("log.txt","a");
    	flock($fp, LOCK_EX) ;
    	fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
    	flock($fp, LOCK_UN);
    	fclose($fp);
    }

    /**
     * 远程获取数据，POST模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert_url 指定当前工作目录绝对路径
     * @param $para 请求的数据
     * @param $input_charset 编码格式。默认值：空值
     * return 远程输出的数据
     */
    function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '') {

    	if (trim($input_charset) != '') {
    		$url = $url."_input_charset=".$input_charset;
    	}
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    	curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
    	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    	curl_setopt($curl,CURLOPT_POST,true); // post传输数据
    	curl_setopt($curl,CURLOPT_POSTFIELDS,$para);// post传输数据
    	$responseText = curl_exec($curl);
    	//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    	curl_close($curl);
    	
    	return $responseText;
    }

    /**
     * 远程获取数据，GET模式
     * 注意：
     * 1.使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"就行了
     * 2.文件夹中cacert.pem是SSL证书请保证其路径有效，目前默认路径是：getcwd().'\\cacert.pem'
     * @param $url 指定URL完整路径地址
     * @param $cacert_url 指定当前工作目录绝对路径
     * return 远程输出的数据
     */
    function getHttpResponseGET($url,$cacert_url) {
    	$curl = curl_init($url);
    	curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
    	curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
    	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
    	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
    	curl_setopt($curl, CURLOPT_CAINFO,$cacert_url);//证书地址
    	$responseText = curl_exec($curl);
    	//var_dump( curl_error($curl) );//如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
    	curl_close($curl);
    	
    	return $responseText;
    }

    /**
     * 实现多种字符编码方式
     * @param $input 需要编码的字符串
     * @param $_output_charset 输出的编码格式
     * @param $_input_charset 输入的编码格式
     * return 编码后的字符串
     */
    function charsetEncode($input,$_output_charset ,$_input_charset) {
    	$output = "";
    	if(!isset($_output_charset) )$_output_charset  = $_input_charset;
    	if($_input_charset == $_output_charset || $input ==null ) {
    		$output = $input;
    	} elseif (function_exists("mb_convert_encoding")) {
    		$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
    	} elseif(function_exists("iconv")) {
    		$output = iconv($_input_charset,$_output_charset,$input);
    	} else die("sorry, you have no libs support for charset change.");
    	return $output;
    }
    /**
     * 实现多种字符解码方式
     * @param $input 需要解码的字符串
     * @param $_output_charset 输出的解码格式
     * @param $_input_charset 输入的解码格式
     * return 解码后的字符串
     */
    function charsetDecode($input,$_input_charset ,$_output_charset) {
    	$output = "";
    	if(!isset($_input_charset) )$_input_charset  = $_input_charset ;
    	if($_input_charset == $_output_charset || $input ==null ) {
    		$output = $input;
    	} elseif (function_exists("mb_convert_encoding")) {
    		$output = mb_convert_encoding($input,$_output_charset,$_input_charset);
    	} elseif(function_exists("iconv")) {
    		$output = iconv($_input_charset,$_output_charset,$input);
    	} else die("sorry, you have no libs support for charset changes.");
    	return $output;
    }

    /* *
     * MD5
     * 详细：MD5加密
     * 版本：3.3
     * 日期：2012-07-19
     * 说明：
     * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
     * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
     */

    /**
     * 签名字符串
     * @param $prestr 需要签名的字符串
     * @param $key 私钥
     * return 签名结果
     */
    function md5Sign($prestr, $key) {
    	$prestr = $prestr . $key;
    	return md5($prestr);
    }

    /**
     * 验证签名
     * @param $prestr 需要签名的字符串
     * @param $sign 签名结果
     * @param $key 私钥
     * return 签名结果
     */
    function md5Verify($prestr, $sign, $key) {
    	$prestr = $prestr . $key;
    	$mysgin = md5($prestr);

    	if($mysgin == $sign) {
    		return true;
    	}
    	else {
    		return false;
    	}
    }

    /* *
     * 支付宝接口RSA函数
     * 详细：RSA签名、验签、解密
     * 版本：3.3
     * 日期：2012-07-23
     * 说明：
     * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
     * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
     */

    /**
     * RSA签名
     * @param $data 待签名数据
     * @param $private_key_path 商户私钥文件路径
     * return 签名结果
     */
    function rsaSign($data, $private_key_path) {
        $priKey = file_get_contents($private_key_path);
        $res = openssl_get_privatekey($priKey);
        openssl_sign($data, $sign, $res);
        openssl_free_key($res);
    	//base64编码
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * RSA验签
     * @param $data 待签名数据
     * @param $ali_public_key_path 支付宝的公钥文件路径
     * @param $sign 要校对的的签名结果
     * return 验证结果
     */
    function rsaVerify($data, $ali_public_key_path, $sign)  {
        $CI = &get_instance();
        $load = $CI->load;
        $load->helper('file');
        $log_name ='./cache/paylogs/'.'rsaVerify.db';

        append_write_file($log_name, 'time:' . date('Y:m:d', time()));
        append_write_file($log_name, 'file path:' . $ali_public_key_path);
        $pubKey = file_get_contents($ali_public_key_path);
        append_write_file($log_name, 'public key:' . $pubKey);
        $res = openssl_get_publickey($pubKey);
        append_write_file($log_name, 'res:' . $res);
        $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        append_write_file($log_name, 'result:' . $result);
        openssl_free_key($res);    

        return $result;
    }

    /**
     * RSA解密
     * @param $content 需要解密的内容，密文
     * @param $private_key_path 商户私钥文件路径
     * return 解密后内容，明文
     */
    function rsaDecrypt($content, $private_key_path) {
        $priKey = file_get_contents($private_key_path);
        $res = openssl_get_privatekey($priKey);
    	//用base64将内容还原成二进制
        $content = base64_decode($content);
    	//把需要解密的内容，按128位拆开解密
        $result  = '';
        for($i = 0; $i< strlen($content)/128; $i++  ) {
            $data = substr($content, $i * 128, 128);
            openssl_private_decrypt($data, $decrypt, $res);
            $result .= $decrypt;
        }
        openssl_free_key($res);
        return $result;
    }
	
	/**
	 * 生成签名结果
	 * @param $para_sort 已排序要签名的数组
	 * return 签名结果字符串
	 */
	function buildRequestMysign($para_sort) {
		//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
		$prestr = $this->createLinkstring($para_sort);
		
		$mysign = "";
		switch (strtoupper(trim($this->alipay_config['sign_type']))) {
			case "RSA" :
				$mysign = $this->rsaSign($prestr, $this->alipay_config['private_key_path']);
				break;
			default :
				$mysign = "";
		}
		
		return $mysign;
	}

	/**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
	function buildRequestPara($para_temp) {
        
		//除去待签名参数数组中的空值和签名参数
		$para_filter = $this->paraFilter($para_temp);

		//对待签名参数数组排序
		$para_sort = $this->argSort($para_filter);

		//生成签名结果
		$mysign = $this->buildRequestMysign($para_sort);
		
		//签名结果与签名方式加入请求提交参数组中
		$para_sort['sign'] = $mysign;
		$para_sort['sign_type'] = strtoupper(trim($this->alipay_config['sign_type']));
		
		return $para_sort;
	}

	/**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组字符串
     */
	function buildRequestParaToString($para_temp) {
		//待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		
		//把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
		$request_data = $this->createLinkstringUrlencode($para);
		
		return $request_data;
	}
	
    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $para_temp 请求参数数组
     * @param $method 提交方式。两个值可选：post、get
     * @param $button_name 确认按钮显示文字
     * @return 提交表单HTML文本
     */
	function buildRequestForm($para_temp, $method, $button_name) {
		//待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		
		$sHtml = "
	<form id='alipaysubmit' name='alipaysubmit' action='".$this->
		alipay_gateway_new."_input_charset=".trim(strtolower($this->alipay_config['input_charset']))."' method='".$method."'>";
		while (list ($key, $val) = each ($para)) {
            $sHtml.= "
		<input type='hidden' name='".$key."' value='".$val."'/>
		";
        }

		//submit按钮控件请不要含有name属性
        $sHtml = $sHtml."
		<input type='submit' value='".$button_name."'></form>
	";
		
		$sHtml = $sHtml."
	<script>document.forms['alipaysubmit'].submit();</script>
	";
		
		return $sHtml;
	}
	
	/**
     * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付宝的处理结果
     * @param $para_temp 请求参数数组
     * @return 支付宝处理结果
     */
	function buildRequestHttp($para_temp) {
		$sResult = '';
		
		//待请求参数数组字符串
		$request_data = $this->buildRequestPara($para_temp);

		//远程获取数据
		$sResult = $this->getHttpResponsePOST($this->alipay_gateway_new, $this->alipay_config['cacert'],$request_data,trim(strtolower($this->alipay_config['input_charset'])));

		return $sResult;
	}
	
	/**
     * 建立请求，以模拟远程HTTP的POST请求方式构造并获取支付宝的处理结果，带文件上传功能
     * @param $para_temp 请求参数数组
     * @param $file_para_name 文件类型的参数名
     * @param $file_name 文件完整绝对路径
     * @return 支付宝返回处理结果
     */
	function buildRequestHttpInFile($para_temp, $file_para_name, $file_name) {
		
		//待请求参数数组
		$para = $this->buildRequestPara($para_temp);
		$para[$file_para_name] = "@".$file_name;
		
		//远程获取数据
		$sResult = $this->getHttpResponsePOST($this->alipay_gateway_new, $this->alipay_config['cacert'],$para,trim(strtolower($this->alipay_config['input_charset'])));

		return $sResult;
	}
	
	/**
     * 用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
	 * 注意：该功能PHP5环境及以上支持，因此必须服务器、本地电脑中装有支持DOMDocument、SSL的PHP配置环境。建议本地调试时使用PHP开发软件
     * return 时间戳字符串
	 */
	function query_timestamp() {
		$url = $this->alipay_gateway_new."service=query_timestamp&partner=".trim(strtolower($this->alipay_config['partner']))."&_input_charset=".trim(strtolower($this->alipay_config['input_charset']));
		$encrypt_key = "";		

		$doc = new DOMDocument();
		$doc->load($url);
		$itemEncrypt_key = $doc->getElementsByTagName( "encrypt_key" );
		$encrypt_key = $itemEncrypt_key->item(0)->nodeValue;
		
		return $encrypt_key;
	}

        /**
         * 针对notify_url验证消息是否是支付宝发出的合法消息
         * @return 验证结果
         */


        function verify($post){
            if(empty($post)) {//判断POST来的数组是否为空
                return false;
            }
            else {
                //生成签名结果
                $isSign = $this->getSignVeryfy($post, $post["sign"]);
                //获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
                $responseTxt = 'true';
                if (! empty($post["notify_id"])) {$responseTxt = $this->getResponse($post["notify_id"]);}
                
                //写日志记录
                //if ($isSign) {
                //  $isSignStr = 'true';
                //}
                //else {
                //  $isSignStr = 'false';
                //}
                //$log_text = "responseTxt=".$responseTxt."\n notify_url_log:isSign=".$isSignStr.",";
                //$log_text = $log_text.createLinkString($_POST);
                //logResult($log_text);
                
                //验证
                //$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
                //isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
                //if (preg_match("/true$/i",$responseTxt) && $isSign) {
                if (preg_match("/true$/i",$responseTxt)) {
                    return true;
                } else {
                    return false;
                }
            }
        }

	    /**
	     * 针对notify_url验证消息是否是支付宝发出的合法消息
	     * @return 验证结果
	     */
		function verifyNotify(){
			if(empty($_POST)) {//判断POST来的数组是否为空
				return false;
			}
			else {
				//生成签名结果
				$isSign = $this->getSignVeryfy($_POST, $_POST["sign"]);
				//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
				$responseTxt = 'true';
				if (! empty($_POST["notify_id"])) {$responseTxt = $this->getResponse($_POST["notify_id"]);}
				
				//写日志记录
				//if ($isSign) {
				//	$isSignStr = 'true';
				//}
				//else {
				//	$isSignStr = 'false';
				//}
				//$log_text = "responseTxt=".$responseTxt."\n notify_url_log:isSign=".$isSignStr.",";
				//$log_text = $log_text.createLinkString($_POST);
				//logResult($log_text);
				
				//验证
				//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
				//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
				if (preg_match("/true$/i",$responseTxt) && $isSign) {
					return true;
				} else {
					return false;
				}
			}
		}

	    /**
	     * 针对return_url验证消息是否是支付宝发出的合法消息
	     * @return 验证结果
	     */
		function verifyReturn(){
			if(empty($_GET)) {//判断GET来的数组是否为空
				return false;
			}
			else {
				//生成签名结果
				$isSign = $this->getSignVeryfy($_GET, $_GET["sign"]);
				//获取支付宝远程服务器ATN结果（验证是否是支付宝发来的消息）
				$responseTxt = 'true';
				if (! empty($_GET["notify_id"])) {$responseTxt = $this->getResponse($_GET["notify_id"]);}
				
				//写日志记录
				//if ($isSign) {
				//	$isSignStr = 'true';
				//}
				//else {
				//	$isSignStr = 'false';
				//}
				//$log_text = "responseTxt=".$responseTxt."\n return_url_log:isSign=".$isSignStr.",";
				//$log_text = $log_text.createLinkString($_GET);
				//logResult($log_text);
				
				//验证
				//$responsetTxt的结果不是true，与服务器设置问题、合作身份者ID、notify_id一分钟失效有关
				//isSign的结果不是true，与安全校验码、请求时的参数格式（如：带自定义参数等）、编码格式有关
				//if (preg_match("/true$/i",$responseTxt) && $isSign) {
				if (preg_match("/true$/i",$responseTxt)) {
					return true;
				} else {
					return false;
				}
			}
		}

	    /**
	     * 获取返回时的签名验证结果
	     * @param $para_temp 通知返回来的参数数组
	     * @param $sign 返回的签名结果
	     * @return 签名验证结果
	     */
		function getSignVeryfy($para_temp, $sign) {
			//除去待签名参数数组中的空值和签名参数
			$para_filter = $this->paraFilter($para_temp);
			
			//对待签名参数数组排序
			$para_sort = $this->argSort($para_filter);
			
			//把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
			$prestr = $this->createLinkstring($para_sort);
			
			$isSgin = false;
			switch (strtoupper(trim($this->alipay_config['sign_type']))) {
				case "RSA" :
					$isSgin = $this->rsaVerify($prestr, trim($this->alipay_config['ali_public_key_path']), $sign);
					break;
				default :
					$isSgin = false;
			}
			
			return $isSgin;
		}

	    /**
	     * 获取远程服务器ATN结果,验证返回URL
	     * @param $notify_id 通知校验ID
	     * @return 服务器ATN结果
	     * 验证结果集：
	     * invalid命令参数不对 出现这个错误，请检测返回处理中partner和key是否为空 
	     * true 返回正确信息
	     * false 请检查防火墙或者是服务器阻止端口问题以及验证时间是否超过一分钟
	     */
		function getResponse($notify_id) {
            $CI = &get_instance();
            $load = $CI->load;
            $load->helper('file');
            $log_name ='./cache/paylogs/'.'getResponse.db';

            append_write_file($log_name, ' getResponse time:' . date('Y:m:d', time()));
			$transport = strtolower(trim($this->alipay_config['transport']));
			$partner = trim($this->alipay_config['partner']);
			$veryfy_url = '';
			if($transport == 'https') {
				$veryfy_url = $this->https_verify_url;
			}
			else {
				$veryfy_url = $this->http_verify_url;
			}
            append_write_file($log_name, 'veryfy_url:' . $veryfy_url);
			$veryfy_url = $veryfy_url."partner=" . $partner . "&notify_id=" . $notify_id;
			$responseTxt = $this->getHttpResponseGET($veryfy_url, $this->alipay_config['cacert']);
			append_write_file($log_name, '$responseTxt:' . $responseTxt);
			return $responseTxt;
		}

        function getAlipayHtml($order) {
	    $CI=&get_instance();
	    $alipay_config = $CI->config->item('alipay_new');
            //构造要请求的参数数组，无需改动
            $parameter = array(
                    "service" => "alipay.wap.create.direct.pay.by.user",
                    "partner" => trim($this->alipay_config['partner']),
                    "seller_id" => trim($this->alipay_config['seller_id']),
                    "payment_type"  => "1",
                    "notify_url"    => trim($this->alipay_config['notify_url']),
                    "return_url"    => trim($this->alipay_config['return_url']),
                    "out_trade_no"  => $order->track_sn,
                    "subject"   => $order->track_sn,
                    "total_fee" => number_format($order->pay_price,2,'.',''),
                    "show_url"  => $alipay_config['show_url'],
                    "body"  => $order->track_sn,
                    //"it_b_pay"  => $it_b_pay,
                    //"extern_token"  => $extern_token,
                    "_input_charset"    => trim(strtolower($this->alipay_config['input_charset']))
            );

            $html_text = $this->buildRequestForm($parameter, 'get', '正在转向支付页面，请耐心等待......');
            //echo $html_text;
            return $html_text;
        }
}
?>