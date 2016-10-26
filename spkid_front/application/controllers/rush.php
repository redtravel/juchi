<?php

/**
 * Description of rush
 *
 * @author Carol
 * @date    2013-3-5
 */
class Rush extends CI_Controller {
    
    function __construct() {
	parent::__construct();
    }

    /**
     * 现抢列表
     * @param type $args 
     */
    function index( $args)
    {
        //$this->output->cache(CACHE_HTML_RUSH);
    	$this->load->helper('product');
    	$this->load->helper('common');
    	$this->load->model('rush_model');
    	
        $data = array();
    	if (empty($args))
        {           
            redirect('index');
        }
    	    
    	
    	$args = array_slice(array_pad(array_map('intval', explode('-', $args)), 6, 0), 0, 6);
    	$args_keys = array('rush_id', 'type_id', 'sex_id', 'size_id', 'sort', 'page');
    	$args = array_combine($args_keys, $args);
            $arr_rush = $this->rush_model->get_rush_by_id($args['rush_id'] );
    	// 对于已经结束的抢购，转到首页
            if ( empty($arr_rush ) || strtotime( $arr_rush['end_date'] ) < time())
                redirect('index');//$arr_rush['jump_url'] = base_url();
    	
    	//取列表
    	$cache_key = 'rush-' . implode('-', $args);
    	$is_preview = isset($_GET['is_preview']) && $_GET['is_preview']== 1 ?TRUE:FALSE; 
    	if($is_preview ){
    	    $cache_data = $this->rush_model->product_list($args,FALSE,TRUE);
    	}else{
    	    if(($cache_data  = $this->cache->get($cache_key) ) === FALSE ){
    	    $cache_data = $this->rush_model->product_list($args);//array_merge($args, isset($args['age'] ) ? array('age' => $age_filter[$args['age']]['value']) : array())
    	    if(count($cache_data['list']) > 0 ){
    		$this->cache->save($cache_key, $cache_data , CACHE_TIME_RUSHLIST );
    	    }
    	    }
    	}
    	if(count($cache_data['list']) < 1 ){
    	    set_status_header(404);
    	}
    	$type = $this->rush_model->get_select_type($args ,TRUE );
    	$size = $this->rush_model->get_select_size($args ,TRUE );
    	
    	
    	$data['is_preview'] = $is_preview;
    	$data['from'] = "0_".$args['rush_id'];
    	$data['type_name'] = isset($type['type_name'])?$type['type_name']:0;
    	$data['size_name'] = isset($size['size_name'])?$size['size_name']:0;
    	$data['cat_content'] = isset($arr_rush['cat_content'] )? json_decode($arr_rush['cat_content']): '';
    	$data['campaign'] = $this->rush_model->get_campaign(); //取全场促销信息促销类型->全场
    	$data['arr_rush'] = $arr_rush;
    	$data['product_list'] = $cache_data['list'];
    	$data['filter'] = $cache_data['filter'];
    	$data['page'] =  $data['filter']['page'];
    	$data['pages'] =  $data['filter']['page_count'];
    	$data['args'] = $args;
    	$data['arr_pagelist_num'] = create_pagination(array('pages' => $data['pages'] , 'page' => $data['page'], 'list_num' => 2, 'is_return' => 1));
    	$product_desc_additional = $data['product_list'][0]->product_desc_additional ;//预计发货日期
    	$data['expected_shipping_date'] = get_expected_shipping_date($product_desc_additional );
    	$data['page_title'] = $arr_rush["rush_index"]."_";
    	
    	$this->load->view('rush/rush', $data);
    }
    
    /**
     * 获取商品尺码信息
     */
    function size_list()
    {
        $this->load->model('rush_model');
        $goods_id = intval($this->input->get('goods_id'));
        $color_id = intval($this->input->get('color_id'));
        $is_preview = intval($this->input->get('is_preview')) ? TRUE : FALSE;
        $use_cache= intval($this->input->get('cache')) ? TRUE : FALSE;
        $var_name = trim($this->input->get('var_name', true));
        $size_rows = $this->rush_model->get_size_rows($goods_id, $color_id, !$is_preview,$use_cache);
        $size_rows = !empty($size_rows) ? $size_rows : NULL;
        if ($var_name !== '') {
            exit("var {$var_name} = " . json_encode($size_rows) . ';');
        } else {
            exit(json_encode($size_rows));
        }
    }
    
}

?>
