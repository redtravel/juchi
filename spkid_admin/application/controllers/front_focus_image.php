<?php
/**
 * 首页焦点图controller
 */
class Front_focus_image extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('index/login');
		}
        $this->load->model('front_focus_image_model');
    }

    function index($focus_type=1)
    {
        auth('focus_image_view');
        $list=$this->front_focus_image_model->all(array('focus_type'=>$focus_type));
        $data['list']=$list;
		$data['search_type']=$focus_type;
        $data['cur_ft']=$focus_type;
        //取定义的焦点图类别并执行
        eval(FOCUS_TYPE);
        $data['focus_type']=$focus_type;
        $data['full_page']=true;
        $this->load->view('front_ad/focus_image_list',$data);
    }

    function add($search_type)
    {
        auth('focus_image_add');
        //取定义的焦点图类别并执行
        eval(FOCUS_TYPE);
        $this->load->view('front_ad/focus_image_add',array('focus_type'=>$focus_type, 'search_type' => $search_type));
    }

    function proc_add()
    {
        auth('focus_image_add');
        $focus_type=$this->input->post('focus_type');
        $focus_name=$this->input->post('focus_name');
        $focus_url=$this->input->post('focus_url');
        $start_time = $this->input->post('start_time');
        $end_time = $this->input->post('end_time');
        if(!is_numeric($focus_type)||empty($focus_name)||empty($focus_url))
        {
            sys_msg('请填写完整信息',1);
        }
        //上传图片
        $this->load->library('upload');
        $base_path = FRONT_FOCUS_IMAGE_PATH;
        $sub_dir=date('Y');//以年份分目录
        //var_export($base_path);exit();
        if(!file_exists($base_path)) mkdir($base_path, 0700, true);
        if(!file_exists($base_path.'/'.$sub_dir)) mkdir($base_path.'/'.$sub_dir, 0700, true);
        $this->upload->initialize(array(
                'upload_path' => $base_path.'/'.$sub_dir,
                'allowed_types' => 'gif|jpg|png|jpeg',
                'encrypt_name' => TRUE
        ));
        $update=array();
        if($this->upload->do_upload('small_image')){
                $file = $this->upload->data();
                $update['small_image'] = FRONT_FOCUS_IMAGE_DIR.'/'.$sub_dir.'/'.$file['file_name'];
        }
        if($this->upload->do_upload('focus_image')){
                $file = $this->upload->data();
                $update['focus_image'] = FRONT_FOCUS_IMAGE_DIR.'/'.$sub_dir.'/'.$file['file_name'];
        }

        if(!empty($update))
        {
            //添加
            $data=array('focus_name'=>$focus_name,'focus_url'=>$focus_url,'focus_img'=>$update['focus_image'],
                        'focus_order'=>0,'focus_type'=>$focus_type,'create_admin'=>$this->admin_id,
                        'create_date'=>date('Y-m-d H:i:s'),'small_img'=>$update['small_image'],
                        'start_time' => $start_time, 'end_time' => $end_time);
            $this->front_focus_image_model->insert($data);
            sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>base_url().'front_focus_image/index')));
        } 
    }

    function edit($id)
    {
        auth('focus_image_edit');
        $list=$this->front_focus_image_model->filter(array('id'=>$id));
        //print_r($list);
        //取定义的焦点图类别并执行
        eval(FOCUS_TYPE);
        $this->load->vars('list', $list[0]);
        $this->load->view('front_ad/focus_image_edit',array('focus_type'=>$focus_type));
    }


    public function proc_edit()
    {
        auth('focus_image_edit');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('focus_name', '名称', 'trim|required');
        $this->form_validation->set_rules('focus_url', '链接', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
        $this->load->library('upload');
        $base_path = FRONT_FOCUS_IMAGE_PATH;
        $sub_dir=date('Y');//以年份分目录
        if(!file_exists($base_path)) mkdir($base_path);
        if(!file_exists($base_path.'/'.$sub_dir)) mkdir($base_path.'/'.$sub_dir);
        $this->upload->initialize(array(
                'upload_path' => $base_path.'/'.$sub_dir,
                'allowed_types' => 'gif|jpg|png|jpeg',
                'encrypt_name' => TRUE
        ));
        $update = array();
        $update['focus_name'] = $this->input->post('focus_name');
        $update['focus_url'] = $this->input->post('focus_url');
        $update['focus_type'] = $this->input->post('focus_type');
        $update['create_admin'] = $this->admin_id;
        $update['create_date'] = time();

        //上传图片
        if($_FILES["focus_image"]['name']){

            if($this->upload->do_upload('focus_image')){
                    $file = $this->upload->data();
                    $update['focus_img'] = FRONT_FOCUS_IMAGE_DIR.'/'.$sub_dir.'/'.$file['file_name'];
            }
        }
        if($_FILES["small_image"]['name']){
            if($this->upload->do_upload('small_image')){
                    $file = $this->upload->data();
                    $update['small_img'] = FRONT_FOCUS_IMAGE_DIR.'/'.$sub_dir.'/'.$file['file_name'];
            }
        }

        $focus_id = intval($this->input->post('id'));

        $this->front_focus_image_model->update($update, $focus_id);
        
        sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'front_focus_image/edit/'.$focus_id), array('text'=>'返回列表','href'=>'front_focus_image/index')));
    }

    function update_focus()
    {
        auth('focus_image_edit');
        $focus_id=$this->input->post("focus_id");
        $focus_order=$this->input->post("focus_order");
        $focus_start_time=$this->input->post("focus_start_time");
        $focus_end_time=$this->input->post("focus_end_time");
        foreach($focus_id as $key=>$val)
        {
            $this->front_focus_image_model->update(array('focus_order'=>$focus_order[$key],
                            'start_time'=>$focus_start_time[$key],
                            'end_time'=>$focus_end_time[$key]),$val);
        }
        redirect('front_focus_image/index');
    }

    /**
     * 更新首页焦点图静态html
     */
    function update_focus_image_html($focus_type=1)
    {
        auth('focus_image_edit');
        $list=$this->front_focus_image_model->filter(array('focus_order >'=>0,'focus_type'=>$focus_type,
                    'end_time >'=>date('Y-m-d H:i:s')));
        $this->load->helpers('file');
        if($focus_type==1){
            $html=$this->load->view('front_ad/front_focus_image_html',array('list'=>$list),true);
            if(!write_file(FRONT_FOCUS_IMAGE_HTML,$html))
            {
                echo '生成静态页失败';
            }
            //生成成功则清除memcache缓存
            file_get_contents(FRONT_URL.'/clear/todo/2/'.FRONT_FOCUS_IMAGE_HTML_KEY);
        } elseif($focus_type==2){
            $direction=$this->input->post('direction');
            $btn_name=$this->input->post('btn_name');
            $expire_time=$this->input->post('expire_time');
            $url=$this->input->post('jump_url');
            $html=$this->load->view('front_ad/start_image_html',array('list'=>$list,'direction'=>$direction,'btn_name'=>$btn_name,'expire_time'=>$expire_time,'url'=>$url),true);
            if(!write_file(MOBILE_FIRST_PAGE_HTML,$html))
            {
                echo '生成静态页失败';
            }
        }
        else
        {
            if(!write_file(TUAN_FRONT_FOCUS_IMAGE_HTML,$html))
            {
                echo '生成静态页失败';
            }
            //生成成功则清除memcache缓存
            file_get_contents(FRONT_URL.'/clear/todo/2/'.TUAN_FRONT_FOCUS_IMAGE_HTML_KEY);
        }
        echo '操作成功';
        //sys_msg('操作成功', 0, array(array('text'=>'返回列表','href'=>base_url().'front_focus_image/index')));
    }

    function delete($id=0)
    {
        $this->front_focus_image_model->delete($id);
        redirect('/front_focus_image/index');
    }
   }
?>
