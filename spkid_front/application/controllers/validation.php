<?php
/**
*
*/
class Validation extends Controller{
    function __construct(){
        parent::Controller();
        //加载验证码类
        //$this->load->library('authcode');
    }
    function show(){
    	print_r('aaa');exit;
        //$this->authcode->show();
    }
    function script(){
        //$this->authcode->showScript();
    }
    function check(){
        //调用authcode类中的check方法 判断验证码输入是否正确，ajax回传...
        if ($this->authcode->check(strtolower($this->uri->segment(3)))) {
            echo 1;//成功
        } else {
            echo 2;
        }
    }
}