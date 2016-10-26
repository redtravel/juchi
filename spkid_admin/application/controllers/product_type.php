<?php
/**
 * 商品前台分类controller
 * @author:sean
 * @data:2012-02-25
 */
class Product_type extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if(!$this->admin_id) redirect('index/login');
        $this->load->model('product_type_model');
        //商品所属大类
        $this->load->model('product_genre_model');

    }
    /*
     * 列表
     */
    function index()
    {
        auth('product_type_view');
		$filter = $this->uri->uri_to_assoc(3);
        $parent_id=$this->input->post('parent_id');
        if(!empty($parent_id))
        {
            $filter['parent_id']=$parent_id;
        }
        else
        {
            $filter['key_word']=$this->input->post('key_word');
            $filter['first_type_id']=$this->input->post('first_type_id');
            $filter['second_type_id']=$this->input->post('second_type_id');
        }
        $filter['genre_id']=$this->input->post('genre_id');
        $filter = get_pager_param($filter);
		$data = $this->product_type_model->product_type_list($filter);
        $this->load->vars('perm_edit',check_perm('product_type_edit'));
        $this->load->vars('perm_add',check_perm('product_type_add'));
        $this->load->vars('perm_del',check_perm('product_type_del'));
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('product/product_type_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
        //一级分类
        //$first_type=$this->product_type_model->filter(array('parent_id'=>0));
        //$data['first_type']=$first_type;
        $data['all_genre'] = $this->product_genre_model->all_genre();
        $this->load->view('product/product_type_list',$data);
    }

    /**
     * 添加或编辑
     */
    function add($type_id=0)
    {
        $this->load->model('category_model');
        $this->load->helper('category');
        $data['type_id']=$type_id;
        if($type_id>0)
        {
            auth('product_type_edit');
            $type=$this->product_type_model->filter(array('type_id'=>$type_id));
            if($type[0]->parent_id>0)
            {
                $second_type=$this->product_type_model->filter(array('parent_id'=>$type[0]->parent_id));
                $data['second_type']=$second_type;
            }
            $data['row']=$type[0];
        }
        else
        {
            auth('product_type_add');
        }
        //一级分类
        $first_type=$this->product_type_model->filter(array('parent_id'=>0));
        $data['first_type']=$first_type;
        $data['all_category'] = category_tree($this->category_model->all_category());

        //商品所属大类
        $this->load->model('product_genre_model');
        $data['all_genre'] = $this->product_genre_model->all_genre();

        $this->load->view('product/product_type_add',$data);
    }

    /**
     * 执行添加或修改
     */
    function proc_add($type_id=0)
    {
        $arr['genre_id']=$this->input->post('genre_id');
        $arr['parent_id']=$this->input->post('first_type');
        $arr['parent_id2']=$this->input->post('second_type');
//        $arr['type_code']=$this->input->post('type_code');
        $arr['type_name']=$this->input->post('type_name');
        $arr['is_show_cat']=$this->input->post('is_show_cat');
        $arr['sort_order']=$this->input->post('sort_order');
        $arr['update_admin']=$this->admin_id;
        $arr['update_date']=date('Y-m-d H:i:s');
        $arr['category_id'] = intval($this->input->post('category_id'));
        if($type_id>0)//更新
        {
            auth('product_type_edit');
//            $type=$this->product_type_model->filter(array('type_id'=>$type_id));
//            $type=$type[0];
//            //编码重复验证
//            if($arr['type_code']!=$type->type_code)
//            {
//                $type=$this->product_type_model->filter(array('type_code'=>$arr['type_code']));
//                if(!empty($type))
//                {
//                    sys_msg('编码重复',1);
//                }
//            }
            $this->product_type_model->update($arr,$type_id);
        }
        else
        {
            auth('product_type_add');
	    $arr['type_code'] = $this->product_type_model->gen_product_type_sn();
//            $type=$this->product_type_model->filter(array('type_code'=>$arr['type_code']));
//            //编码重复验证
//            if(!empty($type))
//            {
//                sys_msg('编码重复',1);
//            }
            $arr['create_admin']=$this->admin_id;
            $arr['create_date']=date('Y-m-d H:i:s');
            $this->product_type_model->insert($arr);
        }
        sys_msg('操作成功！',0 , array(array('text'=>'继续编辑', 'href'=>'product_type/add/'.$type_id), array('text'=>'返回列表', 'href'=>'product_type')));
    }

    /**
     * 执行删除
     */
    function proc_del($type_id)
    {
        auth('product_type_del');
        $type_link=$this->product_type_model->filter_product_type_link(array('type_id'=>$type_id));
        if(!empty($type_link))
        {
            echo json_encode(array('error'=>1));
            exit;
        }
        $type=$this->product_type_model->filter_where(" parent_id=$type_id or parent_id2=$type_id");
        if(!empty($type))
        {
            echo json_encode(array('error'=>2));
            exit;
        }
        $this->product_type_model->delete($type_id);
        echo json_encode(array('error'=>0));
    }
    /*
     * 取一级分类
     * @param $genre_id 一级分类id
     */
    function get_first_type($genre_id)
    {
        $first_type=$this->product_type_model->filter(array('genre_id'=>$genre_id,'parent_id'=>0));
        echo json_encode($first_type);
    }
    /*
     * 取二级分类
     * @param $first_type_id 一级分类id
     */
    function get_second_type($first_type_id)
    {
        $first_type=$this->product_type_model->filter(array('parent_id'=>$first_type_id,'parent_id2'=>0));
        echo json_encode($first_type);
    }
    
    /**
     * 取三级分类
     * @param type $second_type_id 二级分类ID
     */
    function get_three_type($second_type_id){
	  $three_type = $this->product_type_model->filter(array('parent_id2'=>$second_type_id));
        echo json_encode($three_type);
    }
}
?>
