<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
include_once 'abstractSms.php';
include_once 'transport.php';
/**
 * 忆美短信平台 
 * @author :sean
 */
class e_mas extends abstractSms{
    var $url="http://116.213.179.149/EMAS/sms_server.jsp";
    var $username="丁晓燕";
    //加密后的密文 如果更换密码需要调用对方http接口重新生成密文
    var $password="3feM3MLZj8QD0cnE1H2g7Q==";

    function __construct() {
        $this->service_id=9;
        $this->_transport=new transport();
    }

    /**
     * 调用远端http接口对密码加密
     */
    function get_crypt_password($password){
        $params_arr=array('cmd'=>'4001','text'=>$password);
        $params='jsondata='.json_encode($params_arr);
        $result = $this->_transport->request($this->url, $params);
        var_dump($result);
    }

    /**
     * 发送信息
     * @param type $tel
     * @param type $msg
     * @param type $msg_id
     * @return boolean 
     */
    public function sendMsg($tel, $msg, $msg_id=0) {
        $tel_conf=$this->check_tel($tel);
        foreach($tel_conf as $key=>$val){
            $params_arr=array('cmd'=>'1001','username'=>$this->username,'userpassword'=>$this->password,
                              'key'=>'EMASKEYS','timestamp'=>date('YmdHis').rand(1000,9999),
                              'channel_id'=>$key,'mobiles'=>implode(',',$val),'sendtime'=>'',
                              'smscontent'=>$msg,'addserial'=>'','srccharset'=>'GBK');
            $params = json_encode($params_arr);
            $params='jsondata='.$params;
            $result = $this->_transport->request($this->url, $params);
        }
        //多次发送 只对最后一次结果验证
        if (!empty($result)){
            $result=json_decode($result['body']);
            if($result->result==0){
                return true;
            }
        }
        return false;
    }

    /**
     * 对要发送短信的号码进行处理
     * 不同的号码段需要用不同的通道进行发送
     */
    function check_tel($tel){
        /*
        <add key="CMNumbers" value="134,135,136,137,138,139,150,158,159,187,188,152,151,182,157,183" description="移动"/>
        <add key="CUNumbers" value="130,131,132,155,156,186,185" description="联通"/>
        <add key="CNNumbers" value="133,153,189,180,181" description="电信"/>
        */
        $phone[]=array('emay_id'=>'5','phone'=>explode(',','134,135,136,137,138,139,147,150,158,159,187,188,152,151,182,157,183'));//移动
        $phone[]=array('emay_id'=>'4','phone'=>explode(',','130,131,132,155,156,186,185'));//联通
        $phone[]=array('emay_id'=>'7','phone'=>explode(',','133,153,189,180,181'));//电信
        $tel_arr=explode(',',$tel);
        $return_arr=array();
        foreach($tel_arr as $tel){
            $before=substr($tel,0,3);
            foreach($phone as $ph){
                if(in_array($before,$ph['phone'])){
                    $return_arr[$ph['emay_id']][]=$tel;
                }
            }
        }
        return $return_arr;
    }

    public function logSendRecord($tel,$msg,$msg_id){}
}
?>
