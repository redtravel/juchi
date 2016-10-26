<?php
/**
*
*/
class Product_api extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->user_id = $this->session->userdata('user_id');
		$this->user_name = $this->session->userdata('user_name');
		$this->time = date('Y-m-d H:i:s');
	}

	public function history()
	{
	    $this->load->model("product_model");
		// 浏览历史
		if(!$history = $this->session->userdata('product_history')) $history=array();
		$h->product_id = 112;
		$h->product_id = intval($this->input->post('product_id'));
		$html='';
		if(!$history
		||(count($history)==1 && in_array($h->product_id ,get_pair($history,'product_id','product_id'))))
		{
			if(empty($history) ){//如果为空，取默认
			    $history = $this->product_model->get_cache_recommend_pro($h->product_id , 10, 15);
			}
		}
		
		$html = $this->load->view('product/history',array('history'=>$history,'product_id'=>$h->product_id ),TRUE);
		// 更新浏览历史
		if($h->product_id ){
			$h->product_name = trim($this->input->post('product_name'));
			$h->img_170_170 = trim($this->input->post('img_170_170',TRUE));
			$h->market_price = round($this->input->post('market_price'));
			$h->product_price = round($this->input->post('product_price'));
			$h->brand_name = trim($this->input->post('brand_name'));
			$h->discount_percent = round($p->product_price/max($p->market_price,0.01)*10,1);
			foreach($history as $k=>$v) {if($v->product_id==$h->product_id ) unset($history[$k]);}
			
			array_unshift($history,$h);
			$history = array_slice($history,-5);
			$history = $this->session->set_userdata('product_history',$history);
		}
		print json_encode(array('err'=>0,'msg'=>'','html'=>$html));
	}

	/**
	 * 买过还买过
	 */
	public function buy_buy()
	{
//		$this->load->library('memcache');
		$this->load->model('product_model');
		$this->load->helper('product');
		$product_id = intval($this->input->post('product_id'));
		if(($list=$this->cache->get('buy-buy-'.$product_id))===FALSE || true){
			$list = $this->product_model->buy_buy($product_id);
			$this->cache->save('buy-buy-'.$product_id,$list,CACHE_TIME_PRODUCT);
		}
		if(empty($list) ){//如果为空，取默认
		    $list = $this->product_model->get_cache_recommend_pro($product_id, 0, 10);
		}
		$html=$list?$this->load->view('product/buy_buy',array('list'=>$list),TRUE):'';
		print json_encode(array('err'=>0,'msg'=>'','html'=>$html));
	}

	/**
	 * 看过还看过
	 */
	public function link_product()
	{
//		$this->load->library('memcache');
		$this->load->model('product_model');
		$this->load->helper('product');
		$product_id = intval($this->input->post('product_id'));
		if(($list=$this->cache->get('link-product-'.$product_id))===FALSE){
			$list = $this->product_model->link_product($product_id);
			$this->cache->save('link-product-'.$product_id,$list,CACHE_TIME_PRODUCT);
		}
		if(empty($list) ){//如果为空，取默认
		    $list = $this->product_model->get_cache_recommend_pro($product_id, 10, 15);
		}
		$html=$list?$this->load->view('product/link_product',array('list'=>$list),TRUE):'';
		print json_encode(array('err'=>0,'msg'=>'','html'=>$html));
	}

	public function buy_buy_cart()
	{
		$this->load->model('cart_model');
		$this->load->helper('product');
		$cart_sn = get_cart_sn();
		$cart_list = $this->cart_model->all_cart(array('session_id'=>$cart_sn));
		$product_ids=array_keys(get_pair($cart_list,'product_id','product_id'));
		if(!$product_ids) sys_msg('没有商品',0);
		$list=$this->cart_model->buy_buy($product_ids);
		if(!$list) sys_msg('没有商品',0);
		foreach($list as &$p) format_product($p);
		$html=$list?$this->load->view('product/buy_buy_cart',array('list'=>$list),TRUE):'';
		print json_encode(array('err'=>0,'msg'=>'','html'=>$html));
	}

	//收藏
	public function add_to_collect()
	{
		$this->load->model('package_model');
		$this->load->model('product_model');
		$this->load->model('wordpress_model');

		$types = array(
					0 => '商品',
					1 => '礼包',
					2 => '文章',
					3 => '课程',
					4 => '视频'
				); 

		//判断用户是否登录
		if(!$this->user_id) {
			print json_encode(array('err'=>0,'msg'=>0));
			return;
		}

		$product_id = intval($this->input->post('product_id'));
		$product_type = intval($this->input->post('product_type'));

		//判断收藏的商品是否已收藏
		$col=$this->product_model->filter_collect(array('product_id'=>$product_id,'product_type'=>$product_type,'user_id'=>$this->user_id));
		if(!empty($col)){
			if(array_key_exists($product_type,$types)) {
				$pt_type = $types[$product_type];
				sys_msg('您已成功关注'.$pt_type,1);
			}			
		} 

		//判断收藏的商品是否存在
		if($product_type==1){
			$pkg=$this->package_model->filter(array('package_id'=>$product_id,'check_admin >'=>0));
			if(empty($pkg)) sys_msg('此'.$types[$product_type].'不存在',1);  //礼包
		}elseif($product_type==0 || $product_type==3){
			$p=$this->product_model->filter(array('product_id'=>$product_id,'is_audit'=>1));
			if(empty($p)) sys_msg('此'.$types[$product_type].'不存在',1);    // 商品和课程
		}elseif($product_type==2 || $product_type==4){
			$p=$this->wordpress_model->filter(array('ID'=>$product_id,'post_status'=>'publish'));
			if(empty($p)) sys_msg('此'.$types[$product_type].'不存在',1);    // 文章和视频
		}

		$collect = array(
			'user_id' => $this->user_id,
			'product_id' => $product_id,
			'product_type' => $product_type,
			'create_date' => $this->time
		);
		//将某个商品的 收藏记录写入db
		$this->product_model->insert_collect($collect);

		$collect_data = array();
		$collect_data[] =$collect;

		//将 用户 收藏的 某商品 写入session
		if(isset($_SESSION['collect_'.$this->user_id])){
			array_push($collect_data[],$_SESSION['collect_'.$this->user_id]);
		}
		$this->session->set_userdata('collect_'.$this->user_id, $collect_data);

		//将某商品的收藏数量写入session
		$collect_num = 0;
		if(isset($_SESSION['collect_'.$product_type.$product_id])){
			$collect_num = $_SESSION['collect_'.$product_type.$product_id] + 1;
			$this->session->set_userdata('collect_'.$product_type.$product_id, $collect_num);
		}else{
			$collect_num = 1;
			$this->session->set_userdata('collect_'.$product_type.$product_id, $collect_num);
		}

		print json_encode(array('err'=>0,'msg'=>'', 'collect_num'=>$collect_num));
	}

	public function float_cart()
	{
		$this->load->model('cart_model');
		$this->load->helper('cart');
		$cart_sn = get_cart_sn();
		$cart_list = $this->cart_model->cart_info($cart_sn,TRUE);
		$cart_summary = $cart_list?summary_cart($cart_list):array();
		$html=$this->load->view('cart/cart_float',array(
			'cart_list' => $cart_list,
			'cart_summary'=>$cart_summary
		),TRUE);
		print json_encode(array('err'=>0,'msg'=>'','html'=>$html));
	}

    /**
     * 取商品信息 
     * 供NTKF调用
     */
    public function get_product_info(){
        $product_id=$this->input->get('itemid');
        if(empty($product_id)){
            echo json_encode(array('result'=>'product_id is null'));
            return;
        }
		$this->load->model('product_model');
	    $product_info = $this->product_model->product_info($product_id);
        if(empty($product_info)){
            echo json_encode(array('result'=>'product is null'));
            return;
        }
        $product_img=$this->product_model->all_gallery(array('product_id'=>$product_id));
        $product=array(
                    'item'=>array('id'=>$product_info->product_id,
                                  'name'=>$product_info->product_name,
                                  'imageurl'=>img_url($product_img[0]->img_215_215),
                                  'url'=>'www.baobeigou.com/product-'.$product_id.'.html')
                );
        echo json_encode($product);
    }
    
    function change_region(){
        $country = $this->input->get_post('country');
        $this->load->library('lib_iplocation');
        $loc_region_shipping_fee = $this->session->userdata('local_region_shipping_fee');
        if(empty($loc_region_shipping_fee) || $country != $loc_region_shipping_fee['region_name'] ){
            $loc_region_shipping_fee = $this->lib_iplocation->get_loc_region_shipping_fee($country);
            $this->session->set_userdata('local_region_shipping_fee',$loc_region_shipping_fee);
        }
        echo json_encode($loc_region_shipping_fee);
    }
    
    function last_hotsales(){
        $this->load->library('memcache');
        $all_topfive_goods = $this->memcache->get('all_topfive_goods');
        if ($all_topfive_goods == FALSE) {
            return '';
        }
        
        $all_topfive_goods_arr = unserialize($all_topfive_goods);
        //$all_topfive_goods_obj = (object)$all_topfive_goods_arr;
	$html=$all_topfive_goods_arr ? $this->load->view('product/last_hotsales',array('list'=>$all_topfive_goods_arr),TRUE):'';
	//$this->load->view('product/last_hotsales',array('list'=>$all_topfive_goods_arr));
	print json_encode(array('err'=>0,'msg'=>'','html'=>$html));  
    }
    
    /**
     * 可售尺码
     * @param type $product_id
     * @param type $color_id
     */
    public function onsale_size($product_id, $color_id)
    {
        $this->load->model('product_model');
        $product_id = intval($product_id);
        $color_id = intval($color_id);
        $sub_list = $this->product_model->sub_list(array('product_id'=>$product_id, 'color_id'=>$color_id, 'is_on_sale'=>1));
        $color_arr = array();
        foreach($sub_list as $sub){
            if($sub->consign_num!=-2 && $sub->gl_num + max($sub->consign_num, 0) <=0) continue;
            $color_arr[] = $sub->color_name;
        }
        print json_encode($color_arr);
    }
}
