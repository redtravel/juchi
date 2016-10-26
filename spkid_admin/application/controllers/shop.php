<?php
/**
 * 店铺管理controller
 * @author:sean
 * @date:2013-02-20
 */
class shop extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
        $this->load->model('shop_model');
    }

    /**
     * 店铺列表
     */
    function index()
    {
        auth('shop_view');
		$filter = $this->uri->uri_to_assoc(3);
        $filter = get_pager_param($filter);
		$data = $this->shop_model->shop_list($filter);
        $this->load->vars('perm_edit',check_perm('shop_edit'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('shop/shop_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
        $this->load->view('shop/shop_list',$data);

    }

    /**
     * 添加或编辑
     */
	public function add ($shop_id=0)
	{
        $this->load->vars('shop_id',$shop_id);
        //添加
        if($shop_id==0)
        {
            auth('shop_add');
		    $this->load->view('shop/add');
        }
        else//修改
        {
            auth(array('shop_edit','shop_view'));
		    $shop = $this->shop_model->filter(array('shop_id' => $shop_id));
		    if ( empty($shop) )
		    {
			    sys_msg('记录不存在！', 1);
		    }
		    $this->load->vars('row', $shop);
		    $this->load->vars('perm_edit', check_perm('shop_edit'));
        }
		$this->load->view('shop/add');
	}

    /**
     * 执行添加或更新
     */
	public function proc_add ($shop_id=0)
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('shop_name', '店铺名称', 'trim|required');
		$this->form_validation->set_rules('shop_sn', '店铺sn', 'trim|required');
		if ( ! $this->form_validation->run() )
		{
			sys_msg(validation_errors(), 1);
		}

		$update = array();
		$update['shop_name'] = $this->input->post('shop_name');
		$update['shop_sn'] = strtoupper($this->input->post('shop_sn'));
		$update['is_cod'] = $this->input->post('is_cod') == 1 ? 1 : 0;
		$update['single_order'] = $this->input->post('single_order') == 1 ? 1 : 0;
		$update['shop_shipping'] = $this->input->post('shop_shipping') == 1 ? 1 : 0;
		$update['shop_status'] = $this->input->post('shop_status') == 1 ? 1 : 0;
		$update['create_date'] = date('Y-m-d H:i:s');
		$update['create_admin'] = $this->admin_id;
        $update['update_date'] = date('Y-m-d H:i:s');
		$update['update_admin'] = $this->admin_id;
        if($shop_id>0)//更新
        {
		    auth('shop_edit');
            $old_shop=$this->shop_model->filter(array('shop_id'=>$shop_id));
            if($old_shop->shop_name!=$update['shop_name'])
            {
                $shop = $this->shop_model->filter(array('shop_name'=>$update['shop_name']));
		        if ( $shop )
		        {
			        sys_msg('店铺名称重复', 1);
		        }
            }
            unset($update['shop_sn']);
            unset($update['create_admin']);
            unset($update['create_date']);
            $this->shop_model->update($update,$shop_id);
        }
        else//添加
        {
		    auth('shop_add');
		    $shop = $this->shop_model->filter(array('shop_name'=>$update['shop_name']));
		    if ( $shop )
		    {
			    sys_msg('店铺名称重复', 1);
		    }
		    $shop = $this->shop_model->filter(array('shop_sn'=>$update['shop_sn']));
		    if ( $shop )
		    {
			    sys_msg('店铺sn重复', 1);
		    }
		    $shop_id = $this->shop_model->insert($update);
        }
		sys_msg('操作成功！',0 , array(array('text'=>'继续编辑', 'href'=>'shop/add/'.$shop_id), array('text'=>'返回列表', 'href'=>'shop/index')));
	}
}
?>
