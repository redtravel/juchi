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
                goto_login('user/my_collect');
        }
        $user_id = $this->user_id;

        $data['product'] = $this->collect_model->collect_list($user_id);    //用户收藏的 产品、 课程 列表
    $data['videos'] = $this->wordpress_model->video_collect_list($user_id);    
    if(!isset($data['videos'])) {
        $data['videos'] = array();
    }

    //用户收藏的视频 列表
        $now = time();
        $data['courses'] = array();

        foreach ($data['product'] as $k => &$p) {        

            $p->is_promote = $p->is_promote && strtotime($p->promote_start_date)<=$now && strtotime($p->promote_end_date)>=$now ;
            $p->product_price = $p->is_promote ? $p->promote_price : $p->shop_price;
            if ($p->product_type == 0) {
                $data['products'][] = $p;
            }

            if ($p->product_type == 3) {
                $data['courses'][] = $p;
            }
        }

        unset($data['product']);

        $data['product_num'] = count($data['products']);
        $data['course_num'] = count($data['courses']);
        $data['video_num'] = count($data['videos']);

        $this->load->view('user/my_collect', $data);
    }

    
    public function collect_delete() {
        $rec_id = intval($this->input->get('rec_id'));
        $this->collect_model->delete_collect($rec_id);
    }

    function my_collect() {

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
        var_export($data);exit();
        $this->load->view('user/my_collect', $data);    
    }

}
