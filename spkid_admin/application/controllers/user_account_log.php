<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class User_account_log extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('user_account_log_model');
    }


    public function index ()
    {
        auth(array('uaccount_l_view','uaccount_l_edit'));
        $this->load->vars('dis' , check_perm('uaccount_l_edit') ? '1' : '2');
        $filter = $this->uri->uri_to_assoc(4);
        $filter['user_id'] = intval($this->uri->segment(3));
        $change_code = $this->input->post("change_code");
        if(!empty($change_code)) $filter['change_code'] = $change_code;
        $start_time = $this->input->post("start_time");
        if(!empty($start_time)) $filter['start_time'] = $start_time;
        $end_time = $this->input->post("end_time");
        if(!empty($end_time)) $filter['end_time'] = $end_time;

        $filter = get_pager_param($filter);
        $data = $this->user_account_log_model->log_list($filter);
        $this->load->model('user_model');
        $user_arr = $this->user_model->filter(array('user_id' => $filter['user_id']));
        if (empty($user_arr)){
            $user_arr = new stdclass();
            $user_arr->user_id = 0;
            $user_arr->user_name = '';
            $user_arr->user_money = 0;
            $user_arr->paid_money = 0;
            $user_arr->pay_points = 0;
        } 
        $this->load->vars('user_arr' , $user_arr);
        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('user_account_log/list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }
        $this->load->model('user_account_log_kind_model');
        $arr = $this->user_account_log_kind_model->all_kind();
        $this->load->vars('all_kind' , $arr);
        $data['full_page'] = TRUE;
        $this->load->view('user_account_log/list', $data);
    }


    public function add($user_id)
    {
        auth('uaccount_l_edit');
        $this->load->model('user_model');
        $check = $this->user_model->filter(array('user_id' => $user_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->vars('check' , $check);
        $this->load->view('user_account_log/add');
    }

    public function proc_add($user_id)
    {
        auth('uaccount_l_edit');
        $data['user_id'] = intval($user_id);
        $data['user_money'] = round(floatval($this->input->post('user_money')), 2);
        $data['change_code'] = 'change_account';
        $data['change_desc'] = trim($this->input->post('change_desc'));
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');


        if($data['user_money']==0) sys_msg('请填写变动金额',1);
        if(!$data['change_desc'])sys_msg('请填写变动原因',1);
        $this->load->model('user_model');
        $check_user = $this->user_model->filter(array('user_id' => $data['user_id']));
        if(empty($check_user)){
            sys_msg('用户不存在',1);
            return;
        }
        $param['user_money'] = $check_user->user_money + $data['user_money'];
        

        $this->user_model->update($param , $data['user_id']);
        $this->user_account_log_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'/user_account_log/index/'.$data['user_id'],'text'=>'返回列表页')));
    }

    

}
