<?php

/**
 * 
 */
class Brand extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->time = date('Y-m-d H:i:s');
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('brand_model');
        $this->load->model('rush_model');
	// flag_id所对应的洲
	$this->continent=true;

    }
    public function lists($fid = 0){
        $data = $this->brand_model->get_flag_category($this->continent);
        $data['fid'] = $fid;
        $data['index'] = 2;

        // 这里获取动态的seo
        $this->load->library('lib_seo');
        $seo = $this->lib_seo->get_seo_by_pagetag('pc_brands_index', array());
        $data = array_merge($data, $seo);
        $this->load->view('product/brands', $data);
    }

    public function index($bid) {
        $this->load->model('rush_model');
        if ($this->input->is_ajax_request()) {
            $page = $this->input->get('page');
            $data = $this->rush_model->brand_product_list($bid, $page);
            $product_list = $this->load->view('product/brand_product',array('product_list' => $data),true);
            echo $product_list;
        } else {
            $page = 1;
            $data = $this->rush_model->brand_product_list($bid, $page);

            $data['title'] = isset($data['brand']) ? $data['brand']->brand_name . '-演示站' : '演示站品牌';

            $data['keywords'] = isset($data['brand']) ? $data['brand']->brand_info . '-演示站' : '演示站品牌';

            $data['description'] = isset($data['brand']) ? strip_tags($data['brand']->brand_story) . '-演示站' : '演示站品牌';

            $this->load->view('product/brand',$data);
        }
    }
    public function comment(){
        $data = array();
        $content = $this->input->post('content');
        $data['mobile'] = $this->input->post('mobile');
        $this->load->model('liuyan_model');
        $data['comment_content'] = $content;
        $data['tag_type'] = 5;
        $data['tag_id'] = $this->input->post('brand_id');
        $data['comment_type'] = 1;
        $res = $this->liuyan_model->insert($data);
        echo json_encode(array('success' => $res));
    }

    public function brand_product($brand_id) {

        $filter = array('brand_id'=>$brand_id,'sort'=>0,'page'=>0);
        $data = $this->rush_model->product_list($filter , true , FALSE);

        $data['one_brand'] = $this->brand_model->one_brand($brand_id);
        // 这里获取动态的seo
        $this->load->library('lib_seo');
        $seo = $this->lib_seo->get_seo_by_pagetag('brand_list', 
                    array(
                        'brand_title' => $data['one_brand']->brand_name));
        $data['title'] = $seo['title'];

        $this->load->view('mobile/brand/brand_product_list',$data);
    }

    //无限下拉
    function ajax_brand_list(){
        $cat_id = $this->input->get('cid');
        $flag_id = $this->input->get('fid');
        $data = $this->brand_model->brand_list_by_category($flag_id, $cat_id,$this->continent);
        echo json_encode($data);
    }

    /**
     * 根据key或position_id获取广告
     */
    function _get_ad($cache_key,$position_tag, $size=0)
    {
        $this->load->library('lib_ad');
        return $this->lib_ad->get_ad_by_position_tag($cache_key,$position_tag, $size);
    }

    
}
