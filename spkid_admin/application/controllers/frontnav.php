<?php
class Frontnav extends CI_Controller 
{
    public function __construct ()
    {
            parent::__construct();
            $this->admin_id = $this->session->userdata('admin_id');
            if ( ! $this->admin_id )
            {
                    redirect('index/login');
            }
            $this->load->model('frontnav_model');
    }

    public function index ()
    {
        auth(array('frontnav_edit','frontnav_view'));
        // $this->load->model('product_type_model');
        $this->load->vars(array(
            'nav_list' => $this->frontnav_model->all(),
            // 'all_category' => index_array($this->product_type_model->filter(array('parent_id'=>0)),'type_id'),
            'can_edit' => check_perm('frontnav_edit')
        ));        
        $this->load->view('frontnav/list');
    }
    
    public function add(){
        auth('frontnav_edit');
        // $this->load->model('product_type_model');
	// $this->load->vars('first_types', index_array($this->product_type_model->filter(array('parent_id'=>0)),'type_id'));
        $this->load->view('frontnav/add');
    }
    
    public function proc_add(){
        auth('frontnav_edit');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $this->form_validation->set_rules('nav_name', '导航名称', 'trim|required');
        $this->form_validation->set_rules('nav_url', '导航链接', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $data['nav_name'] = trim($this->input->post('nav_name'));
        // $data['category_ids'] = $this->input->post('category_id');
        $data['nav_url'] = trim($this->input->post('nav_url'));        
        $data['sort_order'] = intval($this->input->post('sort_order'));
        // $data['nav_ad_url'] = trim($this->input->post('nav_ad_url'));
        // if(is_array($data['category_ids'])) {
        //     foreach($data['category_ids'] as &$cat_id) $cat_id = intval($cat_id);
        //     $data['category_ids'] = implode(',',$data['category_ids']);
        // }else{
        //     $data['category_ids'] ='';
        // if(!$data['nav_url']) sys_msg('请填写导航链接');
        // }
        
        $nav_id = $this->frontnav_model->insert($data);
        sys_msg('操作成功！',0 , array(array('text'=>'查看', 'href'=>'frontnav/edit/'.$nav_id)));
    }
    
    public function edit($nav_id){
        auth('frontnav_edit');
        $nav_id = intval($nav_id);
        $nav =  $this->frontnav_model->filter(array('nav_id' => $nav_id));
        if(empty($nav)) sys_msg('记录不存在', 1);
 //        $nav->category_ids = explode(',',$nav->category_ids);
	// $this->load->model('product_type_model');
        $this->load->vars(array(
            // 'first_types'=> index_array($this->product_type_model->filter(array('parent_id'=>0)),'type_id'),
            'nav' => $nav
        ));
        $this->load->view('frontnav/edit');
    }
    
    public function proc_edit(){
        auth('hotword_edit');
        $this->load->library('form_validation');
        $this->load->library('upload');
        $nav_id = intval($this->input->post('nav_id'));
        $nav =  $this->frontnav_model->filter(array('nav_id' => $nav_id));
        if(empty($nav)) sys_msg('记录不存在', 1);
          
        $this->form_validation->set_rules('nav_name', '导航名称', 'trim|required');
        $this->form_validation->set_rules('nav_url', '导航链接', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $data['nav_name'] = trim($this->input->post('nav_name'));
        // $data['category_ids'] = $this->input->post('category_id');
        $data['nav_url'] = trim($this->input->post('nav_url'));
        // $data['nav_ad_url'] = trim($this->input->post('nav_ad_url')); 
        $data['sort_order'] = intval($this->input->post('sort_order'));
        // if(is_array($data['category_ids'])) {
        //     foreach($data['category_ids'] as &$cat_id) $cat_id = intval($cat_id);
        //     $data['category_ids'] = implode(',',$data['category_ids']);
        // }else{
        //     $data['category_ids'] ='';
            // if(!$data['nav_url']) sys_msg('请填写导航链接');
        // }
        $this->frontnav_model->update($data,$nav_id);
        sys_msg('操作成功',0,array(array('href'=>'frontnav/index','text'=>'返回列表页')));
    }
    
    public function delete($nav_id){
        auth('frontnav_edit');
        $nav_id = intval($nav_id);
        // $test = $this->input->post('test');
        $nav =  $this->frontnav_model->filter(array('nav_id' => $nav_id));
        if(empty($nav)){
            sys_msg('记录不存在', 1);
        }
        // if($test) sys_msg('');
        $this->frontnav_model->delete($nav_id);
        // if($nav->nav_ad_img) @unlink(CREATE_IMAGE_PATH.$nav->nav_ad_img);
        sys_msg('操作成功',0,array(array('href'=>'frontnav/index','text'=>'返回列表页')));
    }

    public function edit_field()
    {
        auth('frontnav_edit');
        switch (trim($this->input->post('field'))) {
            case 'sort_order':
                $val = intval($this->input->post('val'));
                break;
            
            default:
                $val = NULL;
                break;
        }
        print json_encode(proc_edit('frontnav_model','nav_id', array('sort_order'), $val));
    }

    // 生成页头
    public function static_header()
    {
        auth('frontnav_edit');
        $this->load->model('hotword_model');
        $this->load->library('memcache');        
        $all_nav = $this->frontnav_model->all();
        foreach ($all_nav as &$nav) {
            $nav->category_list = array();
            $nav->brand_list = array();
            if($nav->category_ids){
                $category_ids = array();
                foreach (explode(',',$nav->category_ids) as $cat_id) $category_ids[] = intval($cat_id);
                $nav->category_list = $this->frontnav_model->nav_category($category_ids);
                $nav->brand_list = $this->frontnav_model->nav_brand($category_ids);
            }
            if(!$nav->nav_url) $nav->nav_url = front_url("index-{$nav->nav_id}.html");
            $nav->nav_url = str_replace('[front]',FRONT_HOST,$nav->nav_url);
            $nav->nav_ad_url = str_replace('[front]',FRONT_HOST,$nav->nav_ad_url);
        }
        $html = $this->load->view('frontnav/header',array(
            'all_nav' => $all_nav,
            'all_hotword' => $this->hotword_model->all()
        ),TRUE);
        $file_path = CREATE_HTML_PATH.'header.html';
        file_put_contents($file_path,$html);
        $this->memcache->delete('static_header');//清除memcache缓存
        sys_msg('操作成功',0,array(array('href'=>'frontnav','text'=>'返回列表页')));
    }
    
}
?>
