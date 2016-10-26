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
    }

    public function index() {

        //品牌广告位(多个广告)
        $brand_1 = $this->_get_ad('m_brand_1','m_brand_1');
        $brand_2 = $this->_get_ad('m_brand_2','m_brand_2');
        $brand_3 = $this->_get_ad('m_brand_3','m_brand_3');
        if(!empty($brand_1))
            $data['brand_1']=$brand_1;
        if(!empty($brand_2))
            $data['brand_2']=$brand_2;
        if(!empty($brand_3))
            $data['brand_3']=$brand_3;

        //所有品牌,按首字母排序
        $all_brand = $this->brand_model->all_brand_list();
        $data['brand_list'] = array();
        foreach ($all_brand as $value) {
            $bn = explode(",",$value['brand_name']);
            $data['brand_list'][$value['brand_initial']] = $bn;
        }

        $this->load->view('mobile/brand/index',$data);
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
    function ajax_brand_list($page_name){
        $page = $this->input->get('page');
        $brand_id = $this->input->get('brand_id');
        $sort=intval($this->input->get('sort'));
        $param=array('brand_id'=>$brand_id,'sort'=>$sort?$sort:0,'page'=>$page?$page:0);

        // init 
        $result = array('success'=>1,'data'=>array(),'msg'=>'','img_domain'=>get_img_host());

        // exception
        if ($page > M_INDEX_PAGE_MAX){
            $result['success'] = 0;
            $result['message'] = 'all empty';
            die(json_encode($result));
        }

        if (!$brand_id) {
            $result['success'] = 0;
            $result['message'] = 'keywords must not be null!';
            die(json_encode($result));  
        }       
        
        // result's data
        if ($page_name == 'brand_product_list'){
            // $this->load->library('memcache');
            // $cache_key = 'search-'.implode('-',$param);                 
            
            // if(($data=$this->memcache->get($cache_key))===FALSE) {
                $data = $this->rush_model->product_list($param);                                
                // $this->memcache->save($cache_key, $data, CACHE_TIME_CATLIST);
            // }       

            $result['data'] = $data['list'];
            
        } else {
            $result['success'] = 0;
            $result['message'] = 'all empty';
        }

        die(json_encode($result));
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
