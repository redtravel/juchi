<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Userrank extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('userrank_model');
    }

    public function index ()
    {
        auth(array('rank_view','rank_edit'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_user_rank_perm());
        $this->load->model('user_model');
        $dis_arr = $this->user_model->distinct_rank_id();
        $list = $this->userrank_model->userrank_list();
        $this->load->vars('list',$list);
        $this->load->vars('dis_arr',$dis_arr);
        $this->load->view('userrank/list');
    }

    public function del($rank_id)
    {
        auth('rank_edit');
        $rank_id = intval($rank_id);
        $test = $this->input->post('test');
        $check = $this->userrank_model->filter(array('rank_id' => $rank_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->model('user_model');
        $check_del = $this->user_model->filter(array('rank_id'=>$rank_id));
        if(!empty($check_del)){
            sys_msg('记录被占用',1);
            return;
        }
        if($test) sys_msg('');        
        $this->userrank_model->delete($rank_id);
        sys_msg('删除成功',2,array(array('href'=>'/userrank/index','text'=>'返回列表页')));
    }


    public function add()
    {
        auth('rank_edit');
        $this->load->view('userrank/add');
    }

    public function proc_add()
    {
        auth('rank_edit');
        $data['rank_name'] = $this->input->post('rank_name');
        $data['min_points'] = $this->input->post('min_points');
        $data['max_points'] = $this->input->post('max_points');
        $data['regist_point'] = $this->input->post('regist_point');
        $data['buying_point_rate'] = $this->input->post('buying_point_rate');
        $data['comment_point'] = $this->input->post('comment_point');
        $data['profile_point'] = $this->input->post('profile_point');
        $data['invite_point'] = $this->input->post('invite_point');
        $data['friendby_point'] = $this->input->post('friendby_point');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('rank_name', '等级名称', 'trim|required');
        $this->form_validation->set_rules('min_points', '最少积分', 'trim|required');
        $this->form_validation->set_rules('max_points', '最多积分', 'trim|required');
        $this->form_validation->set_rules('regist_point', '注册积分', 'trim|required');
        $this->form_validation->set_rules('buying_point_rate', '购买折扣', 'trim|required');
        $this->form_validation->set_rules('comment_point', '评论积分', 'trim|required');
        $this->form_validation->set_rules('profile_point', '完善信息积分', 'trim|required');
        $this->form_validation->set_rules('invite_point', '邀请送积分数', 'trim|required');
        $this->form_validation->set_rules('friendby_point', '被邀请人购买首次下单送积分数', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->userrank_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'/userrank/index','text'=>'返回列表页')));
    }

    public function edit($rank_id){
        auth(array('rank_edit'));
        $rank_id = intval($rank_id);
        $check = $this->userrank_model->filter(array('rank_id' => $rank_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->model('user_model');
        $check_del = $this->user_model->filter(array('rank_id'=>$rank_id));
        if(!empty($check_del)){
            sys_msg('记录被占用',1);
            return;
        }
        $list = $this->userrank_model->filter(array('rank_id'=> $rank_id));
        $this->load->vars('arr',$list);
        $this->load->view('userrank/edit');
    }

    public function proc_edit($rank_id){
        auth('rank_edit');
        $rank_id = intval($rank_id);
        $data['rank_name'] = $this->input->post('rank_name');
        $data['min_points'] = $this->input->post('min_points');
        $data['max_points'] = $this->input->post('max_points');
        $data['regist_point'] = $this->input->post('regist_point');
        $data['buying_point_rate'] = $this->input->post('buying_point_rate');
        $data['comment_point'] = $this->input->post('comment_point');
        $data['profile_point'] = $this->input->post('profile_point');
        $data['invite_point'] = $this->input->post('invite_point');
        $data['friendby_point'] = $this->input->post('friendby_point');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('rank_name', '等级名称', 'trim|required');
        $this->form_validation->set_rules('min_points', '最少积分', 'trim|required');
        $this->form_validation->set_rules('max_points', '最多积分', 'trim|required');
        $this->form_validation->set_rules('regist_point', '注册积分', 'trim|required');
        $this->form_validation->set_rules('buying_point_rate', '购买折扣', 'trim|required');
        $this->form_validation->set_rules('comment_point', '评论积分', 'trim|required');
        $this->form_validation->set_rules('profile_point', '完善信息积分', 'trim|required');
        $this->form_validation->set_rules('invite_point', '邀请送积分数', 'trim|required');
        $this->form_validation->set_rules('friendby_point', '被邀请人购买首次下单送积分数', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->userrank_model->update($data ,$rank_id );
        sys_msg('操作成功',2,array(array('href'=>'/userrank/index','text'=>'返回列表页')));
    }

    public function editable() {
        if( !auth('rank_edit'))  die(json_encode(Array('success'=>false,'msg'=>'操作失败，无操作权限！')));
        $pk = $this->input->post( 'pk' );
        $name = $this->input->post( 'name' );
        $value = $this->input->post( 'value' );
        $data[$name] = $value;
        $result = $this->userrank_model->update( $data, $pk );
        die(json_encode(Array('success'=>true,'msg'=>'操作成功！', 'value'=>443)));
       
    } 

}