<?php
/**
*
*/
class TuanDetail extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->user_id = $this->session->userdata('user_id');
		$this->time = date('Y-m-d H:i:s');
        $this->load->model('tuangou_model');
		$this->load->model('product_model');
		$this->load->helper('product');
	}

	# 商品详情页
	public function info($param)
	{
        //$this->output->cache(CACHE_HTML_INFO);
		$this->load->model("rush_model");
		$from = strpos ($param ,"_") > 0 ? explode("_", substr($param, strpos($param , "_" )+1 ) ):"";//[0]=>[0->rush,1->category]
		$param = strpos ($param ,"_") > 0 ? substr($param, 0, strpos($param , "_")) : $param; 
		list($tuan_id,$color_id)=array_slice(array_pad(array_map('intval',explode('-',$param)),2,0),0,2);//product_id,color_id
        $is_preview = isset($_GET['is_preview']) && $_GET['is_preview']== 1 ?TRUE:FALSE; 
        $tuaninfo = $this->tuangou_model->tuan_info($tuan_id,$is_preview);

        if(!$tuaninfo) redirect('index');

        $product_id = $tuaninfo->product_id;
		if(!$product_id) redirect('index');
		$ghost = valid_ghost();
		$p = NULL;
		
		
		if (!$ghost) $p = $this->cache->get('p-'.$product_id);
		if (!$p || $is_preview )
		{
			$p = $this->product_model->product_info($product_id);
			$p->expected_shipping_date = get_expected_shipping_date($p->product_desc_additional );
			$p->product_desc_additional = get_product_desc_additional( $p );
			
			if(!$p||(!$p->is_audit&&!$ghost)) redirect('index');
			format_product($p);
//			$p->product_desc=adjust_path($p->product_desc);
			// 取尺码对照表
			if (!$p->size_image) {//获取公用size_image
			    $p->size_image = $this->product_model->get_size_img(array("brand_id"=> $p->brand_id ,"category_id"=> $p->category_id ,"sex" =>$p->product_sex ));//product_size_image
			}
			
			// 洗标
			$goods_carelabel=array();
			if ($p->goods_carelabel) {
				foreach($this->product_model->all_carelabel(array('carelabel_id'=>explode(',',$p->goods_carelabel))) as $carelabel){
					$goods_carelabel[] = $carelabel;
				}
			}
			$p->goods_carelabel = $goods_carelabel;
			// 取颜色
			$g_list = array();
			foreach ($this->product_model->all_gallery(array('product_id'=>$product_id)) as $g) {
				if(!isset($g_list[$g->color_id]))
					$g_list[$g->color_id] = array('default'=>NULL,'tonal'=>NULL,'part'=>array());
				if($g->image_type=='default' || $g->image_type=='tonal')
					$g_list[$g->color_id][$g->image_type] = $g;
				else
					$g_list[$g->color_id]['part'][] = $g;
			}
			foreach ($g_list as $key => $value) {
				if(!isset($value['default'])) unset($g_list[$key]);
			}
			$p->g_list = $g_list;

			if(!$ghost && !$is_preview) $this->cache->save('p-'.$product_id, $p, CACHE_TIME_PRODUCT);
		} else {
			$g_list = $p->g_list;
		}
		if(!$p->is_audit && !$ghost) redirect('index');
		
		// 取尺码
		$cart_list = get_pair($this->product_model->sub_in_cart($product_id),'sub_id','product_num');
		$sub_list = array();
		
		//product_id,color_id => 缓存库存
		$cache_key = "product_sub_" . $product_id;
		if ($is_preview || ($sub_list = $this->cache->get($cache_key)) === FALSE ) {
		    //get
		    //$sub_param = $is_preview?array('product_id' => $product_id ):array('product_id' => $product_id, 'is_on_sale' => 1);
		    $sub_param = array('product_id' => $product_id );
		    foreach ($this->product_model->sub_list($sub_param) as $sub) {
			if (!isset($g_list[$sub->color_id]))
			    continue; //如果图片没有，略过,一般是由缓存引起的
			format_sub($sub);
			if ($sub->sale_num != -2 && isset($cart_list[$sub->sub_id])) {
			    $sub->sale_num = max($sub->sale_num - $cart_list[$sub->sub_id], 0);
			}
			$sub->sale_num = $sub->sale_num == -2 ? MAX_SALE_NUM : min(MAX_SALE_NUM, $sub->sale_num);
			if (!$sub->is_on_sale)
			    $sub->sale_num = 0; //如果未上架，则不能销售
			if (!isset($sub_list[$sub->color_id])) {
			    $sub_list[$sub->color_id] = array('color_id' => $sub->color_id, 'color_name' => $sub->color_name, 'sub_list' => array(), 'has_gl' => FALSE);
			}
			$sub_list[$sub->color_id]['sub_list'][] = $sub;
			if ($sub->sale_num && !$sub_list[$sub->color_id]['has_gl']) {
			    $sub_list[$sub->color_id]['has_gl'] = TRUE;
			}
		    }
		    //save
		    if(!$is_preview) $this->cache->save($cache_key, $sub_list, CACHE_TIME_PRODUCT_SUB);
		}
        
		if(!$sub_list) sys_msg('该商品不存在',1);//已下架
		if(!$color_id && $sub_list ||!isset($sub_list[$color_id])){
			$color_id = end(array_keys($sub_list));
		}
		//详情图片
//		$gallery = $this->product_model->get_product_gallery($product_id,$color_id,"image_id,img_318_318,img_418_418,img_85_85,img_760_760,img_850_850");
		$url_map = $this->product_model->get_pro_url_map($from );
        $right_ad=$this->tuangou_model->randTuanProduct();
        
        //把商品放入近期浏览的cookie
        if($this->user_id)  $cookiekey = 'recentPro'.$this->user_id;
        else $cookiekey = 'recentPro';
        $recentPro = $this->input->cookie($cookiekey);
        $recentProKey = '';
        if($recentPro){
            $recentProArr = array_reverse(explode(',', $recentPro));
            $recentProKey = array_search($tuan_id, $recentProArr);
        } 
        else $recentProArr = null;

        if($recentProKey) unset($recentProArr[$recentProKey]);
        $recentProArr[] = $tuan_id;
        $recentPro = implode(',', array_reverse($recentProArr));
        $this->input->set_cookie($cookiekey, $recentPro, CACHE_TIME_RECOMMEND_PRO);
        
        /*团购和今日团购*/
        $this->load->library('memcache');
        $data['tuan_all_goods_num']=$this->memcache->get('tuan_all_goods_num');
        $data['tuan_all_goods_num']=$data['tuan_all_goods_num']?$data['tuan_all_goods_num']:0;
        $data['tuan_today_goods_num']=$this->memcache->get('tuan_today_goods_num');
        $data['tuan_today_goods_num']=$data['tuan_today_goods_num']?$data['tuan_today_goods_num']:0;
        $data['tuan_today_brands_num']=$this->memcache->get('tuan_today_brands_num');
        $data['tuan_today_brands_num']=$data['tuan_today_brands_num']?$data['tuan_today_brands_num']:0;
        
        //右侧广告
        $rights_ad=$this->_get_ad('tuan_detail_right_ad','tuan_detail_right');
		//右侧广告
        $bottom_ad=$this->_get_ad('tuan_detail_bottom_ad','tuan_detail_bottom');

		$this->load->view('tuan/tuanDetail',array(
			'title'		=> "{$p->product_name} {$p->brand_name}",
			'description'	=> "{$p->product_name} {$p->brand_name}",
			'keywords'	=> "{$p->product_name} {$p->brand_name}",
			'user_name'	=> "{$this->session->userdata("user_name")}",
			'rank_name'	=> "{$this->session->userdata('rank_name')}",
			'p'		=> $p,
            'is_preview'=> $is_preview,   
			'g_list' 	=> $p->g_list,
			'url_map'	=> $url_map,
			'right_ad'	=> $right_ad,
//			'gallery'	=>$gallery,
			'sub_list' 	=> $sub_list,
			'color_id'	=> $color_id,
			'page_title'	=> $p->product_name."_",
//			'css'		=> array('css/plist.css'),
			'gifts_list'	=> $this->rush_model->get_campaign(),
            'tuaninfo'  => $tuaninfo,  
            'tuan_all_goods_num'    =>   $data['tuan_all_goods_num'], 
            'tuan_today_goods_num'    =>   $data['tuan_today_goods_num'], 
            'tuan_today_brands_num'    =>   $data['tuan_today_brands_num'],
            'rights_ad'	=> $rights_ad, 
			'bottom_ad'	=> $bottom_ad 
		));
	}

	public function brand($param)
	{
		$this->load->library('memcache');
		if(empty($param)) redirect('index');
		$param = array_slice(array_pad(array_map('intval',explode('-',$param)),6,0),0,6);
		$param_keys = array('brand_id','category_id','sex','age','sort','page');
		$param = array_combine($param_keys,$param);

		// 取品牌信息
		$brand = memcache_get_brand($param['brand_id']);
		if(!$brand) redirect('index');
		
		//取size filter
		/*以岁段代之
		if(($category_size=$this->memcache->get('category-size-'.$param['brand_id']))===FALSE){
			$category_size = $this->product_model->category_size($param['brand_id']);
			$this->memcache->save('category-size-'.$param['brand_id'],$category_size,CACHE_TIME_BRANDLIST);
		}
		$size_filter = isset($category_size[$param['category_id']])?$category_size[$param['category_id']]:array();
		if($param['size_id']&&!isset($size_filter[$param['size_id']])) $param['size_id']=0;//如果没有尺码则丢掉size_id参数
		*/
		//取category filter
		if (($brand_category=$this->memcache->get('brand-category'))===FALSE) {
			$brand_category = $this->product_model->brand_category();
			$this->memcache->save('brand-category',$brand_category,CACHE_TIME_BRANDLIST);
		}
		$category_filter=isset($brand_category[$param['brand_id']])?$brand_category[$param['brand_id']]:array();
		
		//取age filter
		$age_filter=$this->config->item('age_filter');
		if(!isset($age_filter[$param['age']])) $param['age']=0;
		
		//相关brand
		$other_brand = index_array(memcache_get_brand_list(),'brand_id');
		if(isset($other_brand[$param['brand_id']])) unset($other_brand[$param['brand_id']]);

		//取列表
		$cache_key = 'brand-'.implode('-',$param);
		if(($data=$this->memcache->get($cache_key))===FALSE){
			$data = $this->product_model->product_list(array_merge($param,$param['age']?array('age'=>$age_filter[$param['age']]['value']):array()));
			$this->memcache->save($cache_key,$data,CACHE_TIME_BRANDLIST);
		}
		$this->load->view('product/brand',array(
			'title' => $brand->brand_name,
			'keywords' => $brand->brand_name,
			'description' => $brand->brand_name,
			'param' => $param,
			'brand' => $brand,
			'other_brand' => $other_brand,
			//'size_filter' => $size_filter,
			'age_filter'=>$age_filter,
			'category_filter' => $category_filter,
			'product_list' => $data['list'],
			'filter' => $data['filter']
		));
	}

	public function nav($param)
	{
		return false;
		$this->load->library('memcache');
		if(empty($param)) redirect('index');
		$param = array_slice(array_pad(array_map('intval',explode('-',$param)),6,0),0,6);
		$param_keys = array('nav_id','brand_id','sex','size_id','sort','page');
		$param = array_combine($param_keys,$param);

		// 取nav信息
		if(($nav_list=$this->memcache->get('nav-list'))===FALSE){
			$nav_list=index_array($this->product_model->all_nav(),'nav_id');
			$this->memcache->save('nav-list',$nav_list,CACHE_TIME_PRODUCT);
		}
		if(!isset($nav_list[$param['nav_id']])) redirect('index');
		$nav=$nav_list[$param['nav_id']];
		if(!$nav->category_ids) redirect('index');
		$category_ids=array_map('intval',explode(',',$nav->category_ids));

		//取子分类
		$category_number=memcache_get_category_number();
		
		$category_list=array();
		foreach ($category_ids as $category_id) {
			if(!isset($category_number[$category_id])) continue;
			$category = $category_number[$category_id];
			$category->sub_category=array();
			foreach ($category_number as $c) {
				if($c->parent_id==$category_id && $c->number) $category->sub_category[] = $c;
			}
			if($category->sub_category) $category_list[] = $category;
		}
		if(!$category_list) redirect('index');

		//取brand filter
		if(($category_brand=$this->memcache->get('category-brand'))===FALSE){
			$category_brand = $this->product_model->category_brand();
			$this->memcache->save('category-brand',$category_brand,CACHE_TIME_CATLIST);
		}
		$brand_filter = array();
		foreach ($category_list as $cat) {
			foreach ($cat->sub_category as $c) {
				if(isset($category_brand[$c->category_id])) $brand_filter+=$category_brand[$c->category_id];
			}
		}

		//取列表
		$cache_key = 'nav-'.implode('-',$param);
		if(($data=$this->memcache->get($cache_key))===FALSE){
			$data = $this->product_model->product_list($param+array('category_id'=>$category_ids));
			$this->memcache->save($cache_key,$data,CACHE_TIME_BRANDLIST);
		}
		$this->load->view('product/nav',array(
			'title' => $nav->nav_name,
			'keywords' => $nav->nav_name,
			'description' => $nav->nav_name,
			'param' => $param,
			'nav' => $nav,
			'category_list' => $category_list,
			'brand_filter' => $brand_filter,
			'product_list' => $data['list'],
			'filter' => $data['filter']
		));
	}

	public function search()
	{
		$this->load->library('memcache');
		$kw=trim($this->input->get('kw',TRUE));
		if(!$kw) sys_msg('请输入搜索关键词',1);
		$sex=intval($this->input->get('sex'));
		$sort=intval($this->input->get('sort'));
		$page=intval($this->input->get('page'));
		$age=intval($this->input->get('age'));
		$age_filter=$this->config->item('age_filter');
		if(!isset($age_filter[$age])) $age=0;
		$param=array('kw'=>$kw,'sex'=>$sex?$sex:0,'age'=>$age,'sort'=>$sort?$sort:0,'page'=>$page?$page:0);
		$cache_key = 'search-'.implode('-',$param);
		if(($data=$this->memcache->get($cache_key))===FALSE){
			$data = $this->product_model->product_list(array_merge($param,$param['age']?array('age'=>$age_filter[$param['age']]['value']):array()));
			$this->memcache->save($cache_key,$data,CACHE_TIME_CATLIST);
		}
		//取主分类列表
		$category_number = memcache_get_category_number();
		foreach ($category_number as $key=>$cat) {
			if($cat->parent_id || !$cat->number) unset($category_number[$key]);
		}
		$this->load->view('product/search',array(
			'title' => '搜索结果 '.$kw,
			'kw' => $kw,
			'param' => $param,
			'product_list' => $data['list'],
			'filter' => $data['filter'],
			'age_filter'=>$age_filter,
			'category_number' => $category_number
		));
	}

	public function brands()
	{
		$this->load->library('memcache');
		$all_brand=memcache_get_brand_list();
		$this->load->view('product/brands',array(
			'title' => '品牌大全',
			'all_brand' => $all_brand
		));
	}
	
	public function brand_story($brand_id)
	{
		$this->load->library('memcache');
		$brand_id = intval($brand_id);
		$brand = memcache_get_brand($brand_id);
		if(!$brand) redirect('index');
		$this->load->view('product/brand_story',array(
			'title' => '品牌故事-'.$brand->brand_name,
			'brand' => $brand
		));
	}
	
	
	/**
	 * 商品品牌故事
	 * @param type $brand_id 
	 */
	public function get_brand_story($brand_id )
	{
		$brand_id = intval($brand_id);
		$pro_brand_key = 'brand-'.$brand_id;
		if( ($brand = $this->cache->get($pro_brand_key )) === FALSE ){
		    $this->load->model('product_model');
		    $brand = $this->product_model->brand_info($brand_id);
		    $this->cache->save($pro_brand_key, $brand, CACHE_TIME_BRAND);
		}
		
		if(!$brand) $brand= array('brand_name' => '','brand_logo' => '','brand_story' => '');
		$html = $this->load->view('product/pro_brand_story',array(
			'brand_name' => $brand->brand_name,
			'brand_logo' => $brand->brand_logo,
			'brand_story' => $brand->brand_story
		),TRUE);
		print json_encode(array('err'=>0,'content'=>$html ));
		return;
	}
    
    /**
     * 根据key或position_id获取广告
     */
    function _get_ad($cache_key,$position_tag)
    {
        $this->load->library('lib_ad');
        return $this->lib_ad->get_ad_by_position_tag($cache_key,$position_tag);
    }
	
}