<?php
class rush_notice extends CI_Controller 
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('rush_notice_model');
    }

    /**
     * 添加订阅
     */
    function add_rush_notice()
    {
        $rush_id=$this->input->post('rush_id');
        $input=$this->input->post('param');
        if(empty($input))
        {
            echo json_encode(array('success'=>false,'msg'=>'请输入手机或邮件'));
            return;
        }
        if(empty($rush_id)) $rush_id=-1;//所有
        $type=0;
        if(preg_match('/^\d{11}$/i',$input,$matches))//手机
            $type=1;
        else if( preg_match('/^[_.0-9a-z-]+@([0-9a-z][0-9a-z-]+.)+[a-z]{2,3}$/i',$input,$matches) )//邮箱
            $type=2;

        if($type>0)
        {
            //check exists
            $result=$this->rush_notice_model->filter(array('rush_id'=>$rush_id,'account'=>$input));
            if(!empty($result))
            {
                echo json_encode(array('success'=>false,'msg'=>'成功订阅每日特卖信息','tip'=>'您已经成功订阅了开场通知，无须重复订阅。'));
                return;
            }
            $this->rush_notice_model->insert(array('rush_id'=>$rush_id,'account'=>$input,
                                            'type'=>$type,'create_date'=>date('Y-m-d H:i:s')));
            echo json_encode(array('success'=>true,'msg'=>'成功订阅每日特卖信息',
                        'tip'=>$rush_id==-1?'您已成功订阅了开场通知，我们将在活动开场前通知您。'
                            :($type==1?'您已成功定制了开售通知，我们将在开售当天以短信通知您。'
                                :'您已成功定制了开售通知，我们将在开售当天以邮件通知您。')));
        }
        else
        {
            echo json_encode(array('success'=>false,'msg'=>'请输入正确的手机或邮箱','tip'=>'请输入正确的手机或邮箱'));
        }
    }

    /**
     * 取消订阅
     */
    function cancel_rush_notice()
    {
        $rush_id=$this->input->post('rush_id');
        $phone=$this->input->post('phone');
        $mail=$this->input->post('mail');
        if(empty($phone)&&empty($mail))
        {
            echo json_encode(array('success'=>false,'msg'=>'请输入手机或邮件'));
            return;
        }
        if(empty($rush_id)) $rush_id=-1;//所有
        if(!empty($phone))
            $this->rush_notice_model->delete(array('rush_id'=>$rush_id,'account'=>$phone));
        if(!empty($mail))
            $this->rush_notice_model->delete(array('rush_id'=>$rush_id,'account'=>$mail));
        echo json_encode(array('success'=>true,'msg'=>'成功取消订阅!','tip'=>'如果需要重新订阅请在订阅处留下手机或邮箱'));
    }
}

