<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * sms接口
 * @author :sean
 */
interface Isms{
    
    
    /**
     * 发送消息 
     */
    function sendMsg($tel,$msg,$msg_id); 
    
    /**
     * 记录发送日志 
     */
    function logSendRecord($tel,$msg,$msg_id);
}
?>
