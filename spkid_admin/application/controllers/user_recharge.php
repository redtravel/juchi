<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class User_recharge extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('user_recharge_model');
    }

    public function index ()
    {
        auth(array('user_recharge_view','user_recharge_edit'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_user_recharge_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $mobile = $this->input->post("mobile");
        if(!empty($mobile)) $filter['mobile'] = $mobile;
        $email = $this->input->post("email");
        if(!empty($email)) $filter['email'] = $email;
        $is_paid = $this->input->post("is_paid");
        if(!empty($is_paid)) $filter['is_paid'] = $is_paid;
        $is_audit = $this->input->post("is_audit");
        if(!empty($is_audit)) $filter['is_audit'] = $is_audit;
        
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;

        $filter = get_pager_param($filter);
        $data = $this->user_recharge_model->recharge_list($filter);
        $this->load->model('admin_model');
        $all_admin = $this->admin_model->all_admin();
        $this->load->vars('all_admin' , $all_admin);

        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('user_recharge/list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('user_recharge/list', $data);
    }

    public function del($recharge_id)
    {
        auth('user_recharge_del');
        $recharge_id = intval($recharge_id);
        $test = $this->input->post('test');
        $check = $this->user_recharge_model->filter(array('recharge_id' => $recharge_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        if($check->is_audit != 0){
            sys_msg('无法删除',1);
            return;
        }
        if($test) sys_msg('');
        $this->user_recharge_model->update(array('is_del'=>1 , 'del_admin' =>$this->admin_id , 'del_date' => date('Y-m-d H:i:s')) , $recharge_id);
        sys_msg('删除成功',2,array(array('href'=>'/user_recharge/index','text'=>'返回列表页')));
    }


    public function add()
    {
        auth('user_recharge_edit');
        $this->load->model('payment_model');
        $all_payment = $this->payment_model->all_payment();
        $this->load->vars('all_payment',$all_payment);
        $this->load->view('user_recharge/add');
    }

    public function proc_add()
    {
        auth('user_recharge_edit');
        $data['user_id'] = $this->input->post('user_id');
        $data['amount'] = round(floatval($this->input->post('amount')), 2);
        $data['is_paid'] = 1;
        $data['paid_date'] = date('Y-m-d H:i:s');
        $data['admin_note'] = $this->input->post('admin_note');
        $data['pay_id'] = $this->input->post('pay_id');
        $data['is_audit'] = $this->input->post('is_audit');
        if(!empty($data['is_audit'])){
            $data['audit_admin'] = $this->admin_id;
            $data['audit_date'] = date('Y-m-d H:i:s');
        }
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        $this->load->model('user_model');
        $che = $this->user_model->filter(array('user_id' => $data['user_id']));
        if(empty($che)){
            sys_msg('用户不存在',1);
            return;
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_id', '用户id', 'trim|required');
        $this->form_validation->set_rules('amount', '充值金额', 'trim|required');
        $this->form_validation->set_rules('pay_id', '支付方式', 'trim|required');
        $this->form_validation->set_rules('pay_id', '支付方式', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $recharge_id = $this->user_recharge_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'/user_recharge/index','text'=>'返回列表页')));
    }

    function audit(){
        auth('user_recharge_author');
        $recharge_id = intval($this->input->post('recharge_id'));
        $che = $this->user_recharge_model->filter(array('recharge_id' => $recharge_id));
        if(empty($che)){
            $arr = array('che' => 1 , 'msg'=>'');
            echo json_encode($arr);
            return;
        }
        if($che->is_paid == 0){
            $arr = array('che' => 2, 'msg'=>'');
            echo json_encode($arr);
            return;
        }
        if($che->is_audit == 1){
            $arr = array('che' => 3, 'msg'=>'');
            echo json_encode($arr);
            return;
        }
        if($che->is_del == 1){
            $arr = array('che' => 4, 'msg'=>'');
            echo json_encode($arr);
            return;
        }

        $this->load->model('user_account_log_model');
        $log_da['link_id'] = $recharge_id;
        $log_da['user_id'] = $che->user_id;
        $log_da['user_money'] = $che->amount;
        $log_da['change_desc'] = $che->admin_note;
        $log_da['change_code'] = 'recharge';
        $log_da['create_admin'] = $this->admin_id;
        $log_da['create_date'] = date('Y-m-d H:i:s');
        $this->user_account_log_model->insert($log_da);
        
        $this->load->model('user_model');
        $user_che = $this->user_model->filter(array('user_id' => $log_da['user_id']));
        $user_da['user_money'] = $user_che->user_money + $che->amount;
        $this->user_model->update($user_da , $log_da['user_id']);
        
        $data['is_audit'] = 1;
        $data['audit_admin'] = $this->admin_id;
        $data['audit_date'] = date('Y-m-d H:i:s');
        $this->user_recharge_model->update($data , $recharge_id);
        
        $this->load->model('admin_model');
        $admin_arr = $this->admin_model->filter(array('admin_id' => $data['audit_admin']));
        echo json_encode(array('che' => 5 , 'audit_admin' => $admin_arr->admin_name , 'audit_date'=>$data['audit_date'], 'msg'=>''));
    }

    function select_user_name(){
        $user_phone_email = trim($this->input->post('user_phone_email'));
        $this->load->model('user_model');
        $arr_email = $this->user_model->select_user_name(array('email' => $user_phone_email));
        $arr_mobile = $this->user_model->select_user_name(array('mobile' => $user_phone_email));
        $arr = $arr_email+$arr_mobile;
        $res = array();
        foreach($arr as $item){
            if(!in_array($item , $res)){
                $res[$item->user_id] = $item;
            }
        }
        if(empty($res)){
            echo json_encode(array('type' => 0,'res' => $res));
            exit;
        }
        echo json_encode(array('type' => 1,'res' => $res));
    }
    
    function show($recharge_id){
        auth('user_recharge_view');
        $recharge_id = intval($recharge_id);
        $check = $this->user_recharge_model->filter(array('recharge_id' => $recharge_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->model('user_model');
        $user_arr = $this->user_model->filter(array('user_id' => $check->user_id));
        $this->load->model('payment_model');
        $pay_arr = $this->payment_model->all_payment();
        $this->load->vars('pay_arr' , $pay_arr);
        $this->load->vars('user_arr' , $user_arr);
        $this->load->vars('check' , $check);
        $this->load->view('user_recharge/show');
    }
    
}