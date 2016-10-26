<?php
class CI_Mobile{
    function __construct(){
        $this->CI=&get_instance();
    }	
    /**
     * @param msg 短信内容
     * @param $mobs 以分号分隔的手机号码,或是手机号码组成的数组
     * @return error message if fail. blank string if susccess
     */

    /**
     * v 2016.3.14 老版的运营商渠道 
     */

    // public function send($msg,$mob){
    //     $userid = '1111'; //企业ID $userid
    //     $account = 'jksc013'; //用户账号 $account
    //     $password = 'ywSMS@88291093'; //用户密码 $password
    //     $mobile = $mob; //发送到的目标手机号码 $mobile
    //     $content = $msg;//短信内容 $content
    //     $content = urlencode($content);//短信内容 $content
    //     //            $content = addslashes($content);//短信内容 $content
    //     //发送短信（其他方法相同）
    //     $gateway = "http://sh2.ipyy.com/sms.aspx?action=send&userid={$userid}&account={$account}&password={$password}&mobile={$mobile}&content=".$content."&sendTime=&extno=";
    //     $result = @file_get_contents($gateway);
    //     //$result = @curl_get($gateway);
    //     $xml = @simplexml_load_string($result);
    //     return $xml;
    // }

    /**
     * v 2016.3.14 新的的运营商渠道 
     */

    public function send($msg,$mob){
        $account = '004245'; //用户账号 $account
        $password = 'fWm2z461'; //用户密码 $password
        $mobile = $mob; //发送到的目标手机号码 $mobile                   
        $content = $msg;//短信内容 $content
        $content = urlencode($content);//短信内容 $content
        // $content = addslashes($content);//短信内容 $content
        // 发送短信（其他方法相同）
        $gateway = "http://120.26.69.248/msg/HttpSendSM?account={$account}&pswd={$password}&mobile={$mobile}&msg=".$content."&needstatus=false&product=";
        $result = @file_get_contents($gateway);  //返回的是字符串
        $res_data = explode(',',$result);
        if($res_data[1] == 0){
            $result = (object) ['returnstatus' => 'Success', 'message' => $res_data[1]];
        }else {
            $result = (object) ['returnstatus' => 'error', 'message' => $res_data[1]];
        }
        return $result;
    }
}
