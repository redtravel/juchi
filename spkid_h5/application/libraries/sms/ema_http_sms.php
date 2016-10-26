<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
include_once 'abstractSms.php';
include_once 'transport.php';
/**
 * 企信通
 * @author :sean
 */
class ema_http_sms extends abstractSms{
    var $url="http://117.135.133.61:8080/ema/http/SendSms";
    var $username="4006333999";
    var $password="618f87666226276b03976eb727e8fb16";

    function __construct() {
        $this->service_id=9;
        $this->_transport=new transport();
    }

    /**
     * 发送信息
     * @param type $tel
     * @param type $msg
     * @param type $msg_id
     * @return boolean 
     */
    public function sendMsg($tel, $msg, $msg_id=0) {
        $params = "Account=" .$this->username
                . "&Password=" . $this->password
                . "&Phone=".$tel
                . "&Content=" . urlencode($msg);
        $result = $this->_transport->request($this->url, $params);
        if (!empty($result) && strpos($result["body"], "<response>1</response>") > 1)
            return true;
        else
            return false;
    }

    public function logSendRecord($tel,$msg,$msg_id){}
}
?>
