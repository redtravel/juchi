<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Class Zhuanti
 * 静态专题
 * @author william
 */
class Zhuanti extends CI_Controller {
    var $_aloneFunction = Array( 'lottery', 'check_lottery' );

    function __construct() {
        parent::__construct();
    }

    //显示静态专题,从后台html/zhuanti/读取的html页面
    function index($param) {
        //$name = str_replace('.php', '.html', $param);
        $name = $param . '.html';
        if( in_array( $param, $this->_aloneFunction ) ){
            return $this->$param();
            exit();
        }

        if (function_exists('file_get_contents')) {
            $zhuanti_html = file_get_contents(static_url('zhuanti/' . $name));
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, static_url('zhuanti/' . $name));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $zhuanti_html = curl_exec($ch);
            curl_close($ch);
        }

        if (strpos($zhuanti_html, '<!--success-->') !== false) {
            echo $zhuanti_html;
        } else {
            header('location: /');
            exit;
        }
    }
    // 抽奖页面
    function lottery(){
        $cur_page = 'lottery';
        // 渠道正确且信息正确，则可显示分享页面
        if( $this->check_lottery(true) && isset($_SERVER['HTTP_REFERER']) && preg_match('/user\/profile/', $_SERVER['HTTP_REFERER']) ){
            $cur_page = 'share';

        }

        if( $cur_page  != 'share' ) {
            $tmp_page = $this->session->userdata('share_page');
            if( !empty($tmp_page) ) $cur_page  = 'share';
            $this->session->unset_userdata('share_page');
        }
        $this->load->view( 'mobile/zhuanti/lottery', array('cur_page' => $cur_page) );
    }
    function check_lottery($just_return = false){
        $msg = array();
        $user_id=$this->session->userdata('user_id');
        if($user_id>0){
            $msg['is_login'] = true;
            //检查个人资料是否完全
            $sql = 'SELECT user_name,real_name,company_name,company_position,company_type FROM ty_user_info WHERE user_id='.$user_id;
            $res = $this->db->query($sql)->result_array();$res = $res[0];
            if ('' == $res['user_name'] or '' == $res['real_name'] 
                or '' == $res['company_name'] or '' == $res['company_position'] 
                or 0 == $res['company_type']){
                //信息不全
                $msg['completed'] = false;

            } else{
                if( !$just_return ) $this->session->set_userdata('share_page','share');
                $msg['completed'] = true;
            }

        } else{
            $msg['is_login'] = false;
			if( !$just_return )$this->session->set_userdata('back_url','zhuanti/lottery');
        }
        if( $just_return )  return $msg['is_login'];
        else echo json_encode($msg);

    }
}
?>
