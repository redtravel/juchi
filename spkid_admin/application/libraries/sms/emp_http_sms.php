<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
include_once 'abstractSms.php';
include_once 'transport.php';
/**
 * 企信通
 * @author :sean
 */
class emp_http_sms extends abstractSms{
    var $url="http://10.1.0.212:8088/MWGate/wmgw.asmx/MongateCsSpSendSmsNew";
    var $userId="WEB000";
    var $password="emp@sms!";
    var $pszSubPort= "10037";

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
        $iMobiCount = strpos($tel, ",");
        $iMobiCount = !empty($iMobiCount) && $iMobiCount > 0 ? $iMobiCount : 1;
        $params = "userId=" .$this->userId
                . "&password=" . $this->password
                . "&pszMobis=".$tel
                . "&pszMsg=" . urlencode($msg)
                . "&pszSubPort=" . $this->pszSubPort
                . "&iMobiCount=" . $iMobiCount;
        $this->_transport->use_curl = true;
        $result = $this->_transport->request($this->url, $params, "GET");
        $val = $result["body"];
        settype($val, "integer");
        if (!empty($result) && $val == 0)
            return true;
        else
            return false;
    }

    public function logSendRecord($tel,$msg,$msg_id){}
}
?>
