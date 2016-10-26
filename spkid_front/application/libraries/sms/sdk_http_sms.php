<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
include_once 'abstractSms.php';
include_once 'transport.php';
/**
 * sdk
 */
class sdk_http_sms extends abstractSms{
    var $url="http://sdk2.entinfo.cn:8060/webservice.asmx/gxmt";
	var $sn = "SDK-WSS-010-05809";
	var $pwd = "e2$3-59$";

    function __construct() {
        $this->_transport=new transport(0, 10);
    }

    /**
     * 发送信息
     * @param type $tel
     * @param type $msg
     * @return boolean 
     */
    public function sendMsg($tel, $msg, $msg_id=0) {
        $argv = array( 
         'sn'=> $this->sn, //提供的账号
		'pwd'=> strtoupper(md5($this->sn.$this->pwd)), //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
		 'mobile'=> $tel,//手机号 多个用英文的逗号隔开 一次小于1000个手机号
		 'content'=> $msg,//多个内容分别urlencode编码然后逗号隔开
		 'ext'=>'',//子号(可以空 ,可以是1个 可以是多个,多个的需要和内容和手机号一一对应)
		 'stime'=>'',//定时时间 格式为2011-6-29 11:09:21
		 'rrid'=>''
		 );
		 
		$params = ""; 
		//构造要post的字符串 
		foreach ($argv as $key=>$value) { 
			if (!empty($params)) { 
				$params .= "&"; 
			} 
			$params.= $key."=";
			$params.=urlencode(iconv("UTF-8", "gb2312//IGNORE", $value)); 
		}
		
        $my_header = array('Connection' => 'Close');
        $result = $this->_transport->request($this->url, $params, '', $my_header);
		$xml = simplexml_load_string($result["body"]);
		$mixArray = (array)$xml;
		$result=explode("-",$mixArray[0]);
        if(count($result)>1)
		    return false;
		//	echo '发送失败返回值为:'.$line."请查看webservice返回值";
		else
		    return true;
		//	echo '发送成功 返回值为:'.$line;
    }

    public function logSendRecord($tel,$msg,$msg_id){}
}
?>
