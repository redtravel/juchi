<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * yunxingtong sms
 * @author :sean
 */
//$CI =& get_instance();
//$CI->load->library("sms/abstractSms");
include_once 'abstractSms.php';
include_once 'transport.php';

class Yxt_sms extends abstractSms{
    
    var $url="http://yunxintong.com/smsComputer/smsComputersend.asp";
    var $username="fclub";
    var $password="123456";

    function __construct() {
        parent::__construct();
        $this->service_id=8;
        $this->_transport=new transport();
    }
    
    /**
     * 发送消息
     * @param type $tel
     * @param type $msg 
     */
    public function sendMsg($tel, $msg,$msg_id) {
        $params = "zh=" . $this->username
                . "&mm=" . $this->password
                . "&hm=" .$tel
                . "&dxlbid=5"
                . "&nr=" . urlencode(iconv('UTF-8', 'GB2312', $msg));
        $result = $this->_transport->request($this->url, $params);
        if (!empty($result) && $result['body'] == 0) {
            parent::logSendRecord($tel, $msg,$msg_id);
            return true;
        } else
            return false;
    }
}
?>
