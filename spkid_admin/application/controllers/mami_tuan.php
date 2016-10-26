<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Mami_tuan extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        $this->time = date('Y-m-d H:i:s');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('mami_tuan_model');
        $this->status_list=array(0=>'未审核',1=>'已审核',2=>'已停止',3=>'已结束');
    }

	/* 团购列表 */
    public function index ()
    {
        auth('mami_tuan_view');
		$product_sn = $this->input->post("product_sn");
		$filter = array();
		if(!empty($product_sn)) $filter['product_sn'] = $product_sn;
		$start_time = $this->input->post("start_time");
		if(!empty($start_time)) $filter['start_time'] = $start_time;
        $filter = get_pager_param($filter);
        $data = $this->mami_tuan_model->tuan_list($filter);
		$data['is_edit'] = check_perm('mami_tuan_edit');
        if ($this->input->post('is_ajax'))
        {
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('mami_tuan/list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
        }
        $data['full_page'] = TRUE;
        $this->load->view('mami_tuan/list', $data);
    }

	/* 载入添加团购 */
    public function add()
    {
        auth('mami_tuan_add');
		$product_sn = trim($this->input->post('product_sn'));
		$product_info = '';
		if(!empty($product_sn)) {
			$this->load->model('product_model');
			$this->load->model('rush_model');
			$this->load->library('ckeditor');
			//获取商品信息
			$product_info = $this->product_model->filter(array('product_sn'=>$product_sn,'is_audit'=>1));
			//检测商品是否存在
			if(empty($product_info)) {
				sys_msg('商品未审核或不存在',1,array(array('href'=>'mami_tuan/add','text'=>'重新载入')));
			}
			/*
			//检测商品是否已添加到限抢
			if($this->mami_tuan_model->check_product_rush($product_info->product_id)) {
				sys_msg('商品已添加限抢活动中',1,array(array('href'=>'mami_tuan/add','text'=>'重新载入')));
			}
			//检测商品是否已添加到团购
			if($this->mami_tuan_model->check_product_tuan($product_info->product_id)) {
				sys_msg('商品已添加团购活动中',1,array(array('href'=>'mami_tuan/add','text'=>'重新载入')));
			}
			//排除正在限抢或团购活动中的商品
			$pro_id_arr[] = $product_info->product_id;
			if($this->rush_model->sel_pro_df($pro_id_arr)) {
				sys_msg('商品正在限抢或团购活动中',1,array(array('href'=>'mami_tuan/add','text'=>'重新载入')));
			}
			*/
		}
		$this->load->vars('product',$product_info);
        $this->load->view('mami_tuan/add');
    }
	
	/* 添加团购 */
    public function post_add()
    {
        auth('mami_tuan_add');
		$data['product_id'] = intval($this->input->post('product_id'));
		if(empty($data['product_id'])) sys_msg('商品加载失败',1);
		$data['tuan_name'] = trim($this->input->post('tuan_name'));
		$data['tuan_price'] = $this->input->post('tuan_price');
		$data['tuan_unit'] = $this->input->post('tuan_unit');
		$data['product_num'] = $this->input->post('product_num');
		$data['buy_num'] = intval($this->input->post('buy_num'));
		$data['tuan_sort'] = intval($this->input->post('tuan_sort'));
		$data['tuan_desc'] = $this->input->post('tuan_desc');
		$data['userdefine1'] = $this->input->post('userdefine1');
		$data['userdefine2'] = $this->input->post('userdefine2');
		$data['userdefine3'] = $this->input->post('userdefine3');
		// $data['userdefine4'] = $this->input->post('userdefine4');
		$start_date = trim($this->input->post('start_date'));
		$start_time = trim($this->input->post('start_time'));
		$end_date = trim($this->input->post('end_date'));
		$end_time = trim($this->input->post('end_time'));
		if(!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $start_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/i', $end_date) || ($start_time && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $start_time)) ||($end_time && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $end_time))){
			sys_msg('开始结束时间格式错误',1);
		}
        $data['tuan_online_time'] = $start_date.' '.$start_time;
		$data['tuan_offline_time'] = $end_date.' '.$end_time;
		if($data['tuan_offline_time'] < $this->time) {
			sys_msg('结束日期小于当前日期',1);
		}
        if($data['tuan_offline_time'] < $data['tuan_online_time']) {
			sys_msg('结束日期小于开始日期',1);
		}
		$data['op_add_aid'] = $this->admin_id;
		$data['op_add_time'] = date('Y-m-d H:i:s');
        $data['status'] = 0;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('tuan_name', 'tuan_name', 'trim|required');
		$this->form_validation->set_rules('tuan_price', 'tuan_price', 'trim|required');
		$this->form_validation->set_rules('tuan_unit', 'tuan_unit', 'trim|required');
		$this->form_validation->set_rules('product_num', 'product_num', 'trim|required');
		$this->form_validation->set_rules('start_date', 'start_date', 'trim|required');
        $this->form_validation->set_rules('start_time', 'start_time', 'trim|required');
		$this->form_validation->set_rules('end_date', 'end_date', 'trim|required');
        $this->form_validation->set_rules('end_time', 'end_time', 'trim|required');
        if (!$this->form_validation->run()) {
            sys_msg(validation_errors(), 1);
        }
		if (!isset($_FILES['img_315_207'])) sys_msg('请上传商品图',1);
		if (!isset($_FILES['img_500_450'])) sys_msg('请上传商品详情图',1);
		// if (!isset($_FILES['img_500_450'])) sys_msg('请上传最近浏览图',1);
		if(empty($data['tuan_desc'])) sys_msg('购买需知(等待付款)不能为空',1);
		// if(empty($data['userdefine1'])) sys_msg('头部描述(文案)不能为空',1);
		if(empty($data['userdefine2'])) sys_msg('中部描述(活动规则)不能为空',1);
		if(empty($data['userdefine3'])) sys_msg('底部描述(兔子布克)不能为空',1);
		$this->vali_length($data["tuan_name"], 96 , '团购名称');
		
		$this->load->model('product_model');
		$product_info = $this->product_model->filter(array('product_id'=>$data['product_id']));
		//计算团购价折扣
		$data['product_discount'] = 10*sprintf("%.2f", $data['tuan_price']/$product_info->market_price);
		
		//插入团购主表信息
        $tuan_id = $this->mami_tuan_model->insert($data);
        //上传图片
        $this->load->library('upload');
        $update = array();
        $sub_dir = ($tuan_id-$tuan_id%100)/100;
        if(!file_exists(UPLOAD_PATH_TUAN.UPLOAD_TAG_TUAN.$sub_dir)) mkdir(UPLOAD_PATH_TUAN.UPLOAD_TAG_TUAN.$sub_dir);
        $this->upload->initialize(array(
                'upload_path' => UPLOAD_PATH_TUAN.UPLOAD_TAG_TUAN.$sub_dir,
                'allowed_types' => 'gif|jpg|png',
                'encrypt_name' => TRUE
        ));

        if($this->upload->do_upload('img_315_207')){
			$file = $this->upload->data();
			$update['img_315_207'] = UPLOAD_TAG_TUAN.$sub_dir.'/'.$file['file_name'];
        }

        $this->load->library('myupload'); 
        $this->myupload->initialize(array(
            'upload_path' => UPLOAD_PATH_TUAN.UPLOAD_TAG_TUAN.$sub_dir,
            'allowed_types' => 'gif|jpg|png',
            'encrypt_name' => TRUE
        ));
        if ($this->myupload->do_multi_upload('img_500_450')) {
            $file1 = $this->myupload->get_multi_upload_data();
            $file_name = array();
            foreach ($file1['img_500_450'] as $key => $val) {
                $file_name[] = UPLOAD_TAG_TUAN.$sub_dir.'/'.$val['file_name'];
            }
            $file_name_encode = json_encode($file_name);
            $update['img_500_450'] =$file_name_encode;
        }

   //      if($this->upload->do_upload('img_168_110')){
			// $file = $this->upload->data();
			// $update['img_168_110'] =  UPLOAD_TAG_TUAN . $sub_dir.'/'.$file['file_name'];
   //      }
		
        if ($update) {
                $this->mami_tuan_model->update($update, $tuan_id);
        }
        sys_msg('操作成功',2,array(array('href'=>'mami_tuan/index','text'=>'返回列表页')));
    }
    
	/*载入团购编辑*/
	function edit($tuan_id){
		auth('mami_tuan_view');
		$this->load->model('product_model');
		$this->load->library('ckeditor');
        $tuan_id = intval($tuan_id);
        $tuan_info = $this->mami_tuan_model->filter(array('tuan_id' => $tuan_id));
        if(empty($tuan_info)) {
            sys_msg('记录不存在', 1);
        }
		$is_edit = false;
		if(check_perm('mami_tuan_edit') && $tuan_info->status == 0) {
			$is_edit = true;
		}
        $start_arr = explode(' ',$tuan_info->tuan_online_time);
        $end_arr = explode(' ',$tuan_info->tuan_offline_time);
		//获取商品信息
		$product_info = $this->product_model->filter(array('product_id'=>$tuan_info->product_id));
        $this->load->vars('start_arr' , $start_arr);
        $this->load->vars('end_arr' , $end_arr);
        $this->load->vars('tuan' , $tuan_info);
		$this->load->vars('is_edit' , $is_edit);
		$this->load->vars('product',$product_info);
		$this->load->view('mami_tuan/edit');
    }

	/* 编辑团购 */
    function post_edit($tuan_id){
		auth('mami_tuan_edit');
        $tuan_id = intval($tuan_id);
		$tuan = $this->mami_tuan_model->filter(array('tuan_id' => $tuan_id));
        if(empty($tuan)) {
            sys_msg('记录不存在', 1);
        }
		$data['tuan_name'] = trim($this->input->post('tuan_name'));
		$data['tuan_price'] = $this->input->post('tuan_price');
		$data['tuan_unit'] = $this->input->post('tuan_unit');
		$data['product_num'] = $this->input->post('product_num');
		$data['buy_num'] = intval($this->input->post('buy_num'));
		$data['tuan_sort'] = intval($this->input->post('tuan_sort'));
		$data['tuan_desc'] = $this->input->post('tuan_desc');
		$data['userdefine1'] = $this->input->post('userdefine1');
		$data['userdefine2'] = $this->input->post('userdefine2');
		$data['userdefine3'] = $this->input->post('userdefine3');
		//$data['userdefine4'] = $this->input->post('userdefine4');
		$start_date = trim($this->input->post('start_date'));
		$start_time = trim($this->input->post('start_time'));
		$end_date = trim($this->input->post('end_date'));
		$end_time = trim($this->input->post('end_time'));
		if(!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $start_date)
			||!preg_match('/^\d{4}-\d{2}-\d{2}$/i', $end_date)
			||($start_time && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $start_time))
			||($end_time && !preg_match('/^\d{2}:\d{2}:\d{2}$/i', $end_time))
		)
		{
			sys_msg('开始结束时间格式错误',1);
		}
        $data['tuan_online_time'] = $start_date.' '.$start_time;
		$data['tuan_offline_time'] = $end_date.' '.$end_time;
		if($data['tuan_offline_time'] < $this->time) {
			sys_msg('结束日期小于当前日期',1);
		}
        if($data['tuan_offline_time'] < $data['tuan_online_time']) {
			sys_msg('结束日期小于开始日期',1);
		}
		$data['op_update_aid'] = $this->admin_id;
		$data['op_update_time'] = date('Y-m-d H:i:s');
        $data['status'] = 0;

        $this->load->library('form_validation');
        $this->form_validation->set_rules('tuan_name', 'tuan_name', 'trim|required');
		$this->form_validation->set_rules('tuan_price', 'tuan_price', 'trim|required');
	    $this->form_validation->set_rules('tuan_unit', 'tuan_unit', 'trim|required');
		$this->form_validation->set_rules('product_num', 'product_num', 'trim|required');
		$this->form_validation->set_rules('start_date', 'start_date', 'trim|required');
        $this->form_validation->set_rules('start_time', 'start_time', 'trim|required');
		$this->form_validation->set_rules('end_date', 'end_date', 'trim|required');
        $this->form_validation->set_rules('end_time', 'end_time', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
		if(empty($data['tuan_desc'])) sys_msg('购买需知(等待付款)不能为空',1);
		// if(empty($data['userdefine1'])) sys_msg('头部描述(文案)不能为空',1);
		if(empty($data['userdefine2'])) sys_msg('中部描述(活动规则)不能为空',1);
		if(empty($data['userdefine3'])) sys_msg('底部描述(兔子布克)不能为空',1);
		//if(empty($data['userdefine4'])) sys_msg('商品详情右上角描述不能为空',1);
		$this->vali_length($data["tuan_name"], 96 , '团购名称');
		
		$this->load->model('product_model');
		$product_info = $this->product_model->filter(array('product_id'=>$tuan->product_id));
		//计算团购价折扣
		$data['product_discount'] = 10*sprintf("%.2f", $data['tuan_price']/$product_info->market_price);
		
		//更新团购主表信息
		$this->mami_tuan_model->update($data,$tuan_id);
        //上传图片
        $this->load->library('upload');
        $update = array();
        $sub_dir = ($tuan_id-$tuan_id%100)/100;
        if(!file_exists(UPLOAD_PATH_TUAN.UPLOAD_TAG_TUAN.$sub_dir)) mkdir(UPLOAD_PATH_TUAN.UPLOAD_TAG_TUAN.$sub_dir);
        $this->upload->initialize(array(
                'upload_path' => UPLOAD_PATH_TUAN.UPLOAD_TAG_TUAN.$sub_dir,
                'allowed_types' => 'gif|jpg|png',
                'encrypt_name' => TRUE
        ));
        if($this->upload->do_upload('img_315_207')){
			$file = $this->upload->data();
			if($tuan->img_315_207) @unlink(UPLOAD_PATH_TUAN.UPLOAD_TAG_TUAN.$tuan->img_315_207);
			$update['img_315_207'] = UPLOAD_TAG_TUAN.$sub_dir.'/'.$file['file_name'];
        }

		$this->load->library('myupload'); 
		$this->myupload->initialize(array(
            'upload_path' => UPLOAD_PATH_TUAN.UPLOAD_TAG_TUAN.$sub_dir,
            'allowed_types' => 'gif|jpg|png',
            'encrypt_name' => TRUE
        ));
        if ($this->myupload->do_multi_upload('img_500_450')) {
            $file1 = $this->myupload->get_multi_upload_data();
            $file_name = array();
            foreach ($file1['img_500_450'] as $key => $val) {
                $file_name[] = UPLOAD_TAG_TUAN.$sub_dir.'/'.$val['file_name'];
            }
            $file_name_encode = json_encode($file_name);
            $update['img_500_450'] =$file_name_encode;
        }

   //      if($this->upload->do_upload('img_168_110')){
			// $file = $this->upload->data();
			// if($tuan->img_168_110) @unlink(UPLOAD_PATH_TUAN.UPLOAD_TAG_TUAN.$tuan->img_168_110);
			// $update['img_168_110'] =  UPLOAD_TAG_TUAN . $sub_dir.'/'.$file['file_name'];
   //      }
        if ($update) {
                $this->mami_tuan_model->update($update, $tuan_id);
        }
        sys_msg('操作成功',2,array(array('href'=>'mami_tuan/index','text'=>'返回列表页')));
    }
	
	/* 团购 审核、反审核 */
    public function confirm() {
        auth('mami_tuan_edit');
        $tuan_id = intval($this->input->post('tuan_id'));
		$type = intval($this->input->post('type'));
		if($tuan_id < 1 || ($type!= 1 && $type != 0)){
			echo json_encode(array('error' => 1,'msg' => '获取系统值错误！'));
            exit;
	    }
        $tuan = $this->mami_tuan_model->filter(array('tuan_id'=>$tuan_id));
        if(empty($tuan)) {
            echo json_encode(array('error' => 1,'msg' => '记录不存在！'));
            exit;
        }
  //       if($tuan->tuan_offline_time < $this->time) {
		// 	echo json_encode(array('error' => 1,'msg' => '活动已过期，不能操作！'));
  //           exit;
		// }
        if($tuan->status == 2 && $tuan->status == 3){
            echo json_encode(array('error' => 1,'msg' => '团购已停止或已结束！'));
            exit;
        }
		$tuan_onsale = $this->mami_tuan_model->get_tuan_onsale_status($tuan_id);
		if(!empty($tuan_onsale)) {
			echo json_encode(array('error' => 1,'msg' => '团购已上架，不能操作！'));
			exit;
		}
		$data['status'] = $type;
        if($data['status'] == 1) {
			$data['op_check_aid'] = $this->admin_id;
            $data['op_check_time'] = date('Y-m-d H:i:s');
		} else {
			$data['op_check_aid'] = 0;
            $data['op_check_time'] = '';
		}
        $this->mami_tuan_model->update($data, $tuan_id);
        echo json_encode(array('error' => 0,'msg' => '操作成功！'));
    }

	/* 团购 上架、停止 */
	function sale() {
		auth('mami_tuan_edit');
        $tuan_id = intval($this->input->post('tuan_id'));
		$type = intval($this->input->post('type'));
		if($tuan_id < 1 || ($type!= 1 && $type != 0)){
			echo json_encode(array('error' => 1,'msg' => '获取系统值错误！'));
            exit;
	    }
		$tuan = $this->mami_tuan_model->filter(array('tuan_id'=>$tuan_id));
        if(empty($tuan)) {
            echo json_encode(array('error' => 1,'msg' => '记录不存在！'));
            exit;
        }
        if($tuan->tuan_offline_time < $this->time) {
			echo json_encode(array('error' => 1,'msg' => '活动已过期，不能操作！'));
            exit;
		}
        if($tuan->status == 2 && $tuan->status == 3){
            echo json_encode(array('error' => 1,'msg' => '团购已停止或已结束！'));
            exit;
        }
		//上架状态
		$tuan_onsale = $this->mami_tuan_model->get_tuan_onsale_status($tuan_id);
		
	    if($type == 1) {
			if(!empty($tuan_onsale)) {
				echo json_encode(array('error' => 1,'msg' => '团购已上架，不能操作！'));
				exit;
			}
			$this->mami_tuan_model->onsale_tuan_on($tuan_id,$this->admin_id);
	    } else {
			if(!empty($tuan_onsale)) {
				$this->mami_tuan_model->onsale_tuan_off($tuan_id,$this->admin_id);
			}
			$data['status'] = 2;
			$data['op_stop_aid'] = $this->admin_id;
            $data['op_stop_time'] = date('Y-m-d H:i:s');
			$this->mami_tuan_model->update($data, $tuan_id);
	    }
	    echo json_encode(array('error' => 0,'msg' => '操作成功！'));
	}
	
	/* 限制字段长度 */
	public function vali_length($str , $length , $desc) {
	    $this ->load->helper("str");
	    $tag_len = my_strlen($str);
	    if($tag_len > $length){
		sys_msg('【'.$desc.'】长度过长,最大只能输入【'. $length .'】个字符。（1个汉字=2个字符，1个字母=1个字符）', 1);
	    }
	}

}
