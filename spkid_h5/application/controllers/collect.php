<?php

/**
 * 
 */
class Collect extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->time = date('Y-m-d H:i:s');
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('collect_model');
        $this->load->model('wordpress_model');
        $this->load->library('user_obj');
    }

    public function index() {
        if ($this->user_obj->is_login())
        {
            $user_id = $this->session->userdata('user_id');
        } else
        {
            redirect('/user/login');
        }
        $user_id = $this->user_id;
        $data['product'] = $this->collect_model->collect_list($user_id);    //用户收藏的 产品、 课程 列表
        $data['article'] = $this->wordpress_model->article_collect_list($user_id);    //用户收藏的 文章、视频 列表

        $data['product_num'] = 0;
        $data['course_num'] = 0;
        $data['article_num'] = count($data['article']);

        foreach ($data['product'] as $pro) {
            if($pro->product_type == 0){
                $data['product_num'] +=1; 
            }elseif($pro->product_type == 3){
                $data['course_num'] +=1; 
            }
        }

        $this->load->view('mobile/user/collect_list',$data);
    }

    
    public function collect_delete() {
        $rec_id = intval($this->input->get('rec_id'));
        $this->collect_model->delete_collect($rec_id);
    }

}
