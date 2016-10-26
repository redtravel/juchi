<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
include_once 'abstractSms.php';
include_once 'transport.php';
/**
 * sdk
 */
class sykj_http_sms extends abstractSms{
    var $url="http://120.26.69.248/msg/HttpSendSM";
	var $sn = "004245";
	var $pwd = "76SEqTcggF7Z";

    function __construct() {
        $this->_transport=new transport(0, 30, -1, ture);
    }

    /**
     * 发送信息
     * @param type $tel
     * @param type $msg
     * @return boolean 
     */
    public function sendMsg($tel, $msg, $msg_id=0) {            
        $account = $this->sn; //用户账号 $account
        $password = $this->pwd; //用户密码 $password
        $mobile = $tel; //发送到的目标手机号码 $mobile                   
        $content = $msg;//短信内容 $content
        $content = urlencode($content);//短信内容 $content
        // 发送短信（其他方法相同）

        $url = $this->url;//POST指向的链接      
        $data = array(      
            'account'=>$account,   
            'pswd'=>$password,   
            'mobile'=>$mobile,   
            'msg'=>$content,   
            'needstatus'=>false,   
            'product'=>''  
        );
        //$my_header = array('Connection' => 'Close');
        
        $result = $this->_transport->request($this->url, $data, '', $my_header);

        $res_data = explode(',',$result['body']);
        if($res_data[1] == 0){
            return true;
            //$result = (object) ['returnstatus' => 'Success', 'message' => $res_data[1]];
        }else {
            return false;
            //$result = (object) ['returnstatus' => 'error', 'message' => $res_data[1]];
        }
    }

    public function logSendRecord($tel,$msg,$msg_id){}
}
?>
