<?php
#doc
#	classname:	Provider_brand
#	scope:		PUBLIC
#
#/doc

class Shipper_brand extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
		$this->load->model('shipper_brand_model');
		$this->load->model('provider_model');
	}
	
	public function index ($provider_id,$parent_id)
	{
		if(empty($provider_id)){
		    sys_msg("无对应供应商",1);
		}

		if(!empty($parent_id)){
			//上级供应商的信息
        	$parent = $this->provider_model->filter(array('provider_id'=>$parent_id));
        	$this->load->vars('parent', $parent);
        }

		auth('provider_brand_setup');
		$filter = $this->uri->uri_to_assoc(3);
		$brand_id = trim($this->input->post('brand_id'));
		if (!empty($brand_id)) $filter['brand_id'] = $brand_id;
		$brand_name = trim($this->input->post('brand_name'));
		if (!empty($brand_name)) $filter['brand_name'] = $brand_name;
		$brand_initial = trim($this->input->post('brand_initial'));
		if (!empty($brand_initial)) $filter['brand_initial'] = $brand_initial;
		$skip_set = trim($this->input->post('skip_set'));
		if (!empty($skip_set)){
		    $filter['skip_set'] = $skip_set;
		    $filter['provider_id'] = $provider_id;
		}
		$filter['provider_id'] = $provider_id;
		$filter['parent_id'] = $parent_id;
		$filter = get_pager_param($filter);

		// if(!empty($parent_id)){
  //       	//上级供应商的的品牌
  //       	$data['list'] = $this->shipper_brand_model->provider_brand_list($parent_id);
  //       	// var_dump($data['list']);
  //       	// exit();
  //       }else{
        	//所有品牌
        	$data = $this->shipper_brand_model->brand_list($filter);
        // }


		if ($this->input->is_ajax_request())
		{
		    $data['full_page'] = FALSE;
		    $data['content'] = $this->load->view('provider/shipper_brand', $data, TRUE);
		    $data['error'] = 0;
		    unset($data['list']);
		    echo json_encode($data);
		    return;
		}
		$provider = $this->provider_model->filter(array('provider_id'=>$provider_id));
		if (!$provider) {
			sys_msg('记录不存在', 1);
		}
		$provider_brand_list = $this->shipper_brand_model->provider_brand_list($provider_id);
		$data['brand_list'] = $provider_brand_list;
		$data['full_page'] = TRUE;
		$this->load->vars('row', $provider);
		$this->load->view('provider/shipper_brand',$data);
	}
	
	public function add_brand(){
	    auth('provider_brand_setup');
	    $provider_id = trim($this->input->post('provider_id'));
	    $brand_id = trim($this->input->post('brand_id'));
	    if(empty($provider_id) || empty($brand_id)){
		echo json_encode(array("err"=>1,"msg"=>"没有发货商或者品牌。"));
		return;
	    }
	    $provider = $this->provider_model->filter(array('provider_id'=>$provider_id));
	    if (empty($provider)) {
		echo json_encode(array("err"=>1,"msg"=>"系统中不存在对应发货商。"));
		return;
	    }
	    $this->load->model('brand_model');
	    $brand = $this->brand_model->filter(array("brand_id"=>$brand_id));
	    if(empty($brand)){
		echo json_encode(array("err"=>1,"msg"=>"系统中不存在对应品牌。"));
		return;
	    }
	    $provider_brand = $this->shipper_brand_model->filter(array("provider_id"=>$provider_id,"brand_id"=>$brand_id));
	    if(!empty($provider_brand)){
		echo json_encode(array("err"=>1,"msg"=>"发货商已经关联此品牌",));
		return;
	    }
	    $update = array();
	    $update['provider_id'] = $provider_id;
	    $update['brand_id'] = $brand_id;
	    $update['is_used'] = 1;
	    $update['create_admin'] = $this->admin_id;
	    $update['create_date'] = date('Y-m-d H:i:s');
	    $update['update_admin'] = $this->admin_id;
	    $update['update_date'] = $update['create_date'];
	    $this->shipper_brand_model->insert($update);
	    echo json_encode(array("err"=>0,"msg"=>"","brand"=>$brand));
	}
	
	public function remove_brand(){
	    auth('provider_brand_setup');
	    $provider_id = trim($this->input->post('provider_id'));
	    $brand_id = trim($this->input->post('brand_id'));
	    if(empty($provider_id) || empty($brand_id)){
		echo json_encode(array("err"=>1,"msg"=>"没有发货商或者品牌。"));
		return;
	    }
	    $provider = $this->provider_model->filter(array('provider_id'=>$provider_id));
	    if (empty($provider)) {
		echo json_encode(array("err"=>1,"msg"=>"系统中不存在对应发货商。"));
		return;
	    }
	    $this->load->model('brand_model');
	    $brand = $this->brand_model->filter(array("brand_id"=>$brand_id));
	    if(empty($brand)){
		echo json_encode(array("err"=>1,"msg"=>"系统中不存在对应品牌。"));
		return;
	    }
	    $filter = array();
	    $filter['provider_id'] = $provider_id;
	    $filter['brand_id'] = $brand_id;
	    $provider_brand = $this->shipper_brand_model->filter($filter);
	    if(!empty($provider_brand)){
		$this->shipper_brand_model->delete($provider_brand->id);
	    }
	    echo json_encode(array("err"=>0,"msg"=>""));
	}
        
        public function update_commission() {
            auth('provider_brand_setup');
	    $id = trim($this->input->post('id'));
            
            $provider_brand = $this->shipper_brand_model->filter(array("id"=>$id));
	    if(empty($provider_brand)){
		echo json_encode(array("err"=>1, "msg"=>"发货商关联品牌不存在"));
		return;
	    }
            
            $update = array();
            $old_commission = $provider_brand->commission;
            if (!empty($old_commission)) {
                $histories = array();
                $histories[] = array('end_time'=>date('Y-m-d H:i:s'), 'commission'=>$old_commission, 'update_admin'=>$this->admin_id);
                $old_commission_history = $provider_brand->commission_history;
                if (!empty($old_commission_history)) {
                    $histories = array_merge($histories, json_decode($old_commission_history));
                }
                $update['commission_history'] = json_encode($histories);
            }
	    $update['commission'] = trim($this->input->post('commission'));
            
            $this->shipper_brand_model->update($update, $id);
            
            echo json_encode(array("err"=>0,"msg"=>""));
        }
        
        public function commission_history($id) {
            auth('provider_brand_setup');
            
            $provider_brand = $this->shipper_brand_model->filter(array("id"=>$id));
            if(empty($provider_brand)){
                sys_msg('发货商关联品牌不存在');
            }
            if (empty($provider_brand->commission_history)){
                sys_msg('没有历史扣点');
            } 
            $this->load->vars('histories', json_decode($provider_brand->commission_history));
            
            $this->load->model('admin_model');
            $this->load->vars('all_admin', $this->admin_model->all_admin(null));
            
            $this->load->view('provider/commission_history');
        }
        
}
###
