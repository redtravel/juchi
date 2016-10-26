<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//$CI =& get_instance();
//$CI->load->library("sms/ISms");
include_once 'ISms.php';
/**
 * 实现sms接口的抽象类
 * @author :sean
 */
abstract class abstractSms{
    
    var $service_id;
    
    /**
     * @var sms_mdl
     */
    public $sms_mdl;
    /**
     * 发送消息
     */
    public abstract function sendMsg($tel, $msg,$msg_id);

    /**
     * 日志记录
     * @param type $tel
     * @param type $msg
     * @param type $msg_id 
     */
    public abstract function logSendRecord($tel, $msg, $msg_id);
}
?>
