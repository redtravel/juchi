<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Front_ad extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('front_ad_model');
        $this->load->model('front_ad_posion_model');
    }

    public function index ()
    {
        auth(array('front_ad_po_edit','front_ad_po_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_front_perm());
        
        $filter = $this->uri->uri_to_assoc(3);
       
        $page_name = $this->input->post("page_name");
        if(!empty($page_name)) $filter['page_name'] = $page_name;
        $position_name = $this->input->post("position_name");
        if(!empty($position_name)) $filter['position_name'] = $position_name;
        $position_tag = $this->input->post("position_tag");
        if(!empty($position_tag)) $filter['position_tag'] = $position_tag;
        $brand_id = $this->input->post("brand_id");
        if(!empty($brand_id)) $filter['brand_id'] = $brand_id;
        $category_id = $this->input->post("category_id");
        if(!empty($category_id)) $filter['category_id'] = $category_id;

        $filter = get_pager_param($filter);
        $this->load->model('brand_model');
        $all_brand = $this->brand_model->all_brand();
        $this->load->model('category_model');
        $all_category = $this->category_model->all_category();
        $this->load->vars('all_brand',$all_brand);
        $this->load->vars('all_category',$all_category);
        $data = $this->front_ad_posion_model->ad_p_list($filter);
        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('front_ad/p_list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('front_ad/p_list', $data);
    }

    public function p_edit($position_id){
        auth(array('front_ad_po_edit','front_ad_po_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_front_perm());
        $position_id = intval($position_id);
        $check = $this->front_ad_posion_model->filter(array('position_id' => $position_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->model('brand_model');
        $all_brand = $this->brand_model->all_brand();
        $this->load->model('category_model');
        $all_category = $this->category_model->all_category();
        $this->load->vars('all_brand',$all_brand);
        $this->load->vars('all_category',$all_category);
        $this->load->vars('arr',$check);
        $this->load->view('front_ad/p_edit');
    }
    
    public function proc_p_edit($position_id)
    {
        auth('front_ad_po_edit');
        $position_id = intval($position_id);
        $data['position_tag'] = trim($this->input->post('position_tag'));
        $data['position_name'] = trim($this->input->post('position_name'));
        $data['page_name'] = trim($this->input->post('page_name'));
        $data['brand_id'] = $this->input->post('brand_id');
        $data['category_id'] = $this->input->post('category_id');
        $data['ad_width'] = intval($this->input->post('ad_width'));
        $data['ad_height'] = intval($this->input->post('ad_height'));
        $data['position_style'] = trim($this->input->post('position_style'));
        $this->load->library('form_validation');
        $this->form_validation->set_rules('position_name', 'position_name', 'trim|required');
        $this->form_validation->set_rules('page_name', 'page_name', 'trim|required');
        $this->form_validation->set_rules('ad_width', 'ad_width', 'trim|required');
        $this->form_validation->set_rules('ad_height', 'ad_height', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->front_ad_posion_model->update($data , $position_id);
        sys_msg('操作成功',2,array(array('href'=>'/front_ad/index','text'=>'返回列表页')));
    }
    
    public function p_add()
    {
        auth('front_ad_po_edit');
        $this->load->model('brand_model');
        $all_brand = $this->brand_model->all_brand();
        $this->load->model('category_model');
        $all_category = $this->category_model->all_category();
        $this->load->vars('all_brand',$all_brand);
        $this->load->vars('all_category',$all_category);
        $this->load->view('front_ad/p_add');
    }

    public function proc_p_add()
    {
        auth('front_ad_po_edit');
        $data['position_tag'] = trim($this->input->post('position_tag'));
        $data['position_name'] = trim($this->input->post('position_name'));
        $data['page_name'] = trim($this->input->post('page_name'));
        $data['brand_id'] = $this->input->post('brand_id');
        $data['category_id'] = $this->input->post('category_id');
        $data['ad_width'] = intval($this->input->post('ad_width'));
        $data['ad_height'] = intval($this->input->post('ad_height'));
        $data['position_style'] = trim($this->input->post('position_style'));
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('position_name', 'position_name', 'trim|required');
        $this->form_validation->set_rules('page_name', 'page_name', 'trim|required');
        $this->form_validation->set_rules('ad_width', 'ad_width', 'trim|required');
        $this->form_validation->set_rules('ad_height', 'ad_height', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->front_ad_posion_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'/front_ad/index','text'=>'返回列表页')));
    }

    public function p_del($position_id)
    {
        auth('front_ad_po_edit');
        $position_id = intval($position_id);
        $test = $this->input->post('test');
        $check = $this->front_ad_posion_model->filter(array('position_id' => $position_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $check_ad = $this->front_ad_model->filter(array('position_id' => $position_id));
        if(!empty($check_ad)){
            sys_msg('无法删除',1);
            return; 
        }
        if($test) sys_msg('');
        $this->front_ad_posion_model->delete($position_id);
        sys_msg('操作成功',2,array(array('href'=>'/front_ad/index','text'=>'返回列表页')));
    }
    
    public function ad_index(){
        auth(array('front_ad_edit','front_ad_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_front_perm());
        $filter = $this->uri->uri_to_assoc(4);
        $position_id = $this->input->post("position_id");
        if(!empty($position_id)) $filter['position_id'] = $position_id;
        $ad_name = $this->input->post("ad_name");
        if(!empty($ad_name)) $filter['ad_name'] = $ad_name;
        $start_date = $this->input->post("start_date");
        if(!empty($start_date)) $filter['start_date'] = $start_date;
        $end_date = $this->input->post("end_date");
        if(!empty($end_date)) $filter['end_date'] = $end_date;
        $is_use = $this->input->post("is_use");
        if(!empty($is_use)) $filter['is_use'] = $is_use;
        
        $filter = get_pager_param($filter);
        $data = $this->front_ad_model->ad_index($filter);
        $p_arr = $this->front_ad_posion_model->all();
        $this->load->vars('p_arr' , $p_arr);
        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('front_ad/ad_index', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('front_ad/ad_index', $data);
    }

    public function operate_index($position_id){
        auth(array('front_ad_edit','front_ad_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_front_perm());
        $filter = $this->uri->uri_to_assoc(4);
        $filter['position_id'] = intval($position_id);
        $ad_name = $this->input->post("ad_name");
        if(!empty($ad_name)) $filter['ad_name'] = $ad_name;
        $start_date = $this->input->post("start_date");
        if(!empty($start_date)) $filter['start_date'] = $start_date;
        $end_date = $this->input->post("end_date");
        if(!empty($end_date)) $filter['end_date'] = $end_date;
        $is_use = $this->input->post("is_use");
        if(!empty($is_use)) $filter['is_use'] = $is_use;
        
        $filter = get_pager_param($filter);
        $data = $this->front_ad_model->ad_list($filter);
        $this->load->vars('position_id' , $filter['position_id']);
        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $data['content'] = $this->load->view('front_ad/ad_list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }

        $data['full_page'] = TRUE;
        $this->load->view('front_ad/ad_list', $data);
    }
    
    public function ad_add(){
        auth(array('front_ad_edit'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_front_perm());
        $position_id = intval($this->uri->segment(3, 0));
        $this->load->vars('position_id' , $position_id);
        $p_arr = $this->front_ad_posion_model->all();
        $this->load->vars('p_arr' , $p_arr);
        $this->load->library('ckeditor');
        $this->load->view('front_ad/ad_add');
    }
    
    public function proc_ad_add()
    {
        auth('front_ad_edit');
        $type = intval($this->uri->segment(3, 0));
        $data['position_id'] = intval($this->input->post('position_id'));
        $data['ad_name'] = trim($this->input->post('ad_name'));
        $data['ad_link'] = trim($this->input->post('ad_link'));
        $data['ad_code'] = $this->input->post('ad_code');
        $data['start_date'] = trim($this->input->post('start_date'));
        $data['end_date'] = trim($this->input->post('end_date'));
        $data['is_use'] = $this->input->post('is_use');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        $this->load->library('form_validation');
	$this->load->library('upload');
        $this->form_validation->set_rules('position_id', 'position_id', 'trim|required');
        $this->form_validation->set_rules('ad_name', 'ad_name', 'trim|required');
        // $this->form_validation->set_rules('ad_link', 'ad_link', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $ad_id = $this->front_ad_model->insert($data);
        // 上传图片
        $update = array();
        $this->upload->initialize(array(
            'upload_path' => AD_IMAGE_PATH . AD_IMAGE_TAG,
            'allowed_types' => 'jpg|gif|png',
            'encrypt_name' => TRUE
        ));
        if ($this->upload->do_upload('pic_url')) {
            $file = $this->upload->data();
            $update['pic_url'] = AD_IMAGE_TAG . $file['file_name'];
        }
        if ($update) {
            $this->front_ad_model->update($update, $ad_id);
        }
        if($type == 0){
            sys_msg('操作成功',2,array(array('href'=>'/front_ad/ad_index','text'=>'返回列表页')));
        }else{
            sys_msg('操作成功',2,array(array('href'=>'/front_ad/operate_index/'.$data['position_id'],'text'=>'返回列表页')));
        }
    }
    
    public function ad_edit($ad_id){
        auth(array('front_ad_edit','front_ad_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_front_perm());
        $ad_id = intval($ad_id);
        $type = intval($this->uri->segment(4, 0));
        $check = $this->front_ad_model->filter(array('ad_id' => $ad_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->library('ckeditor');
        $p_arr = $this->front_ad_posion_model->all();
        $this->load->vars('p_arr' , $p_arr);
        $this->load->vars('arr',$check);
        $this->load->vars('type',$type);
        $this->load->view('front_ad/ad_edit');
    }
    
    public function proc_ad_edit($ad_id,$position_id)
    {
        auth(array('front_ad_edit'));
        $type = intval($this->uri->segment(5, 0));
        $ad_id = intval($ad_id);
        $data['position_id'] = intval($this->input->post('position_id'));
        $data['ad_name'] = trim($this->input->post('ad_name'));
        $data['ad_link'] = trim($this->input->post('ad_link'));
        $data['ad_code'] = $this->input->post('ad_code');
        $data['start_date'] = trim($this->input->post('start_date'));
        $data['end_date'] = trim($this->input->post('end_date'));
        $data['is_use'] = $this->input->post('is_use');
        $this->load->library('form_validation');
	$this->load->library('upload');
        $this->form_validation->set_rules('ad_name', 'ad_name', 'trim|required');
        // $this->form_validation->set_rules('ad_link', 'ad_link', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        // 上传图片
        $this->upload->initialize(array(
            'upload_path' => AD_IMAGE_PATH . AD_IMAGE_TAG,
            'allowed_types' => 'jpg|gif|png',
            'encrypt_name' => TRUE
        ));
        if ($this->upload->do_upload('pic_url')) {
            $file = $this->upload->data();
            $data['pic_url'] = AD_IMAGE_TAG . $file['file_name'];
        }
        $this->front_ad_model->update($data , $ad_id);
        if($type == 2){
            sys_msg('操作成功',2,array(array('href'=>'/front_ad/ad_index','text'=>'返回列表页')));
        }else{
            sys_msg('操作成功',2,array(array('href'=>'/front_ad/operate_index/'.$position_id,'text'=>'返回列表页')));
        }
    }
    
    public function ad_del($ad_id)
    {
        auth(array('front_ad_edit'));
        $ad_id = intval($ad_id);
        $test = $this->input->post('test');
        $check = $this->front_ad_model->filter(array('ad_id' => $ad_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        if($test) sys_msg('');
        $this->front_ad_model->delete($ad_id);
        sys_msg('操作成功',2,array(array('href'=>'/front_ad/operate_index/'.$check->position_id,'text'=>'返回列表页')));
    }
    
}
