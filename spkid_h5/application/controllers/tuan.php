<?php 
/**
* Tuan Controller
*/
class Tuan extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->user_id = $this->session->userdata('user_id');
		$this->time = date('Y-m-d H:i:s');
        $this->load->vars('page_title','');//这里设置空title即可
        $this->load->model('tuangou_model');
        $this->load->model('product_model');
        $this->load->model('package_model');
        $this->load->library('wechatauth');
        $this->load->helper('product');
	}

    /**
     * @param:$str
     */
    public function index($str)
    {
    	$key = 'v_tuan_list_' . $str;
    	$is_preview = isset($_GET['is_preview']) && $_GET['is_preview'] == 1 ? TRUE : FALSE;

	$data['title'] = '演示站牙科精品团购热销';
	$data['keywords'] = '演示站牙科精品团购热销，牙科 牙医首选，实惠，品质';
	$data['description'] = '演示站牙科精品团购热销，牙科 牙医首选，实惠，品质';
        if($str == 'product'){
        	$product_type = 1;
        	$data['tuan_product'] = $this->tuangou_model->tuan_list($product_type);
        	if($is_preview){ 
	            $data['tuan_product'] = $this->tuangou_model->tuan_list($product_type);
	            $this->cache->save($key, $data['tuan_product'], 7200);		
	        }else{
	            $data['tuan_product'] = $this->cache->get($key);
	            if( $data['tuan_product'] === FALSE){
	                $data['tuan_product'] = $this->tuangou_model->tuan_list($product_type);
	                $this->cache->save($key, $data['tuan_product'], 7200);		
	            }
	        }

        	$this->load->view('mobile/tuan/tuan_product',$data);
        }elseif($str == 'course'){
        	$product_type = 2;
        	$data['tuan_course'] = $this->tuangou_model->tuan_list($product_type);
        	if($is_preview){ 
	            $data['tuan_course'] = $this->tuangou_model->tuan_list($product_type);
	            $this->cache->save($key, $data['tuan_course'], 7200);		
	        }else{
	            $data['tuan_course'] = $this->cache->get($key);
	            if( $data['tuan_course'] === FALSE){
	                $data['tuan_course'] = $this->tuangou_model->tuan_list($product_type);
	                $this->cache->save($key, $data['tuan_course'], 7200);		
	            }
	        }

        	$this->load->view('mobile/tuan/tuan_course',$data);
        }

    }
    
    function yueyatuan($tuan_id) {  
    	/*
        $is_authed = $this->session->userdata('is_authed');

        if (!$is_authed) {		
            $url = $this->wechatauth->get_authorize_url("http://m.yueyawang.com/tuan/tuan_auth?type=tuan&id=$tuan_id","1");		
            header("Location:".$url);    
        }
	*/
        $data = array();

        $key = 'v_tuan_info' . $tuan_id;
        $is_preview = isset($_GET['is_preview']) && $_GET['is_preview'] == 1 ? TRUE : FALSE;
        if($is_preview){ 
            $data['tuan_info'] = $this->tuangou_model->tuan_info($tuan_id);
            $this->cache->save($key, $data['tuan_info'], 3600 * 10);		
        }else{
            $data['tuan_info'] = $this->cache->get($key);

            if( $data['tuan_info'] === FALSE){
                //判断该活动是否存在/过期,并获取活动信息
                $data['tuan_info'] = $this->tuangou_model->tuan_info($tuan_id);
                $this->cache->save($key, $data['tuan_info'], 3600 * 10);		
            }
        }
        if(!isset($data['tuan_info']) || empty($data['tuan_info'])) redirect('index');


        //获取活动报名人数信息
        $register_info = $this->tuangou_model->register_info($tuan_id,0);
        $data['register_num'] = count($register_info);
        $data['register_info'] = array_slice($register_info,0,5);
        //获取活动评论人数信息
        $comments_info = $this->tuangou_model->comments_info($tuan_id,0);
        $data['comments_num'] = count($comments_info);
        $data['comments_info'] = array_slice($comments_info,0,5);

        //
        $wechat_info = $this->session->userdata('wechat_info');
        $data['tuan_info']->wechat_id = $wechat_info['id'];
        $data['tuan_info']->wechat_nickname = $wechat_info['nickname'];
        
        $this->load->view('mobile/tuan/active_page', $data);
	
	
    }

    function add_register(){
        $param = array();
        $param['register_name'] = trim($this->input->post('register_name'));
        $param['register_mobile']  =  trim($this->input->post('register_mobile'));
        $param['register_num']  =  trim($this->input->post('register_num'));
        $param['register_date']  =  date('Y-m-d H:i:s');
        $wechat_id  =  trim($this->input->post('wechat_id'));
        $register = $this->tuangou_model->add_register($param,$wechat_id);
        return json_encode(array('error' => 1));
    }

    function tuan_confirm($tuan_id) {
        $data = array();
        //
        $wechat_info = $this->session->userdata('wechat_info');
        $data['wechat_nickname'] = $wechat_info['nickname'];
        //$mami_tuan = $this->tuangou_model->filter( array('tuan_id' => $wechat_info['tuan_id']));
	$mami_tuan = $this->tuangou_model->filter( array('tuan_id' => $tuan_id));
        $data['product_id'] = $mami_tuan->product_id;
        $data['product_type'] = $mami_tuan->product_type;
        $data['product_num'] = $mami_tuan->product_num;
	
        $this->load->view('mobile/tuan/tuan_confirm', $data);
	//var_export($wechat_info);//exit;
    }

    function add_comment(){
        $param = array();
        $param['comment_content'] = trim($this->input->post('comment_content'));
        $param['comment_date']  =  date('Y-m-d H:i:s');
        $param['tuan_id']  =  trim($this->input->post('tuan_id'));
        $param['wechat_id']  =  trim($this->input->post('wechat_id'));
        $comment = $this->tuangou_model->add_comment($param);
        if($comment > 0) { 
            $comments_info = $this->tuangou_model->comments_info($param['tuan_id'],0);
            $comments_num = count($comments_info);
            $un_info = array_slice($comments_info,0,5);
            echo json_encode(array('un_info' => $un_info,'comments_num' => $comments_num,'error' => 0));
        }else{
            echo json_encode(array('error' => 1));
        }
    }


    function tuan_auth() {    		
    	   set_time_limit(0);
	    if(isset($_GET['code'])){		  
    		$token = $this->wechatauth->get_access_token('wxd11be5ecb1367bcf','6d05ab776fd92157d6833e2936d6f17c',$_GET['code']); //确认授权后会，根据返回的code获取token
    	    $wechat_user =  $this->wechatauth->get_user_info($token['access_token'], $token['openid']);

            $wechat = array();
            $wechat['tuan_id'] = $_GET['id'];
	    
	   
	   if(!isset($wechat_user['openid'])) {
	    	$inser_we['wechat_openid'] = isset($wechat_user['errcode']) ? $wechat_user['errcode'] . time() : time();
                $inser_we['wechat_nickname'] = 'unknown';//isset($wechat_user['errmsg']) ? $wechat_user['errmsg'] : 'invalid nickname';
                $inser_we['wechat_sex'] = 0;
                $inser_we['wechat_city'] = 'unknown';
                $inser_we['wechat_province'] = 'unknown';
                $inser_we['wechat_country'] = 'unknown';;
                $inser_we['wechat_headimgurl'] = 'unknown';;
                $inser_we['tuan_id'] = $wechat['tuan_id'];
                $inser_we['wechat_date'] = date('Y-m-d H:i:s');
		$inser_we['exception'] = var_export($wechat_user, true);
                $insert_id = $this->tuangou_model->wechat_insert($inser_we);

                $wechat['id'] =  $insert_id;
                $wechat['nickname'] = $wechat_user['nickname'];
	    } else {

	            //查看数据是否已经存库
	            $openid = $this->tuangou_model->filter_wechat(array('wechat_openid' => $wechat_user['openid'],'tuan_id' => $wechat['tuan_id']));
	            if(empty($openid)){
	                $inser_we['wechat_openid'] = $wechat_user['openid'];
	                $inser_we['wechat_nickname'] = $wechat_user['nickname'];
	                $inser_we['wechat_sex'] = $wechat_user['sex'];
	                $inser_we['wechat_city'] = $wechat_user['city'];
	                $inser_we['wechat_province'] = $wechat_user['province'];
	                $inser_we['wechat_country'] = $wechat_user['country'];
	                $inser_we['wechat_headimgurl'] = $wechat_user['headimgurl'];
	                $inser_we['tuan_id'] = $wechat['tuan_id'];
	                $inser_we['wechat_date'] = date('Y-m-d H:i:s');

	                $insert_id = $this->tuangou_model->wechat_insert($inser_we);

	                $wechat['id'] =  $insert_id;
	                $wechat['nickname'] = $wechat_user['nickname'];
	            } else {
	                $wechat['nickname'] = $openid->wechat_nickname;
	                $wechat['id'] = $openid->wechat_id;
	            }
	    }

            

            header("Location:http://m.yueyawang.com/tuan/yueyatuan/".$wechat['tuan_id']);  

	    }else{
		    $wechat['nickname'] = 'error';
	            $wechat['id'] = '0';
	    }
	    
	    $is_authed = 1;

            $this->session->set_userdata('is_authed',$is_authed);
            $this->session->set_userdata('wechat_info',$wechat);
    }
	
	
    function yueyatuan2($tuan_id) {  
    	
         $is_authed = $this->session->userdata('is_authed');

         if (!$is_authed) {		
            $url = $this->wechatauth->get_authorize_url("http://m.yueyawang.com/tuan/tuan_auth2?type=tuan&id=$tuan_id",time());		
            header("Location:".$url);    
         }

        $data = array();

        $key = 'v_tuan_info' . $tuan_id;
        $is_preview = isset($_GET['is_preview']) && $_GET['is_preview'] == 1 ? TRUE : FALSE;
        if($is_preview){ 
            $data['tuan_info'] = $this->tuangou_model->tuan_info($tuan_id);
	    //var_export($data);exit;
            $this->cache->save($key, $data['tuan_info'], 3600 * 10);		
        }else{
            $data['tuan_info'] = $this->cache->get($key);

            if( $data['tuan_info'] === FALSE){
                //判断该活动是否存在/过期,并获取活动信息
                $data['tuan_info'] = $this->tuangou_model->tuan_info($tuan_id);
                $this->cache->save($key, $data['tuan_info'], 3600 * 10);		
            }
        }
	var_export($data);exit;
        if(!isset($data['tuan_info']) || empty($data['tuan_info'])) redirect('index');


        //获取活动报名人数信息
        $register_info = $this->tuangou_model->register_info($tuan_id,0);
        $data['register_num'] = count($register_info);
        $data['register_info'] = array_slice($register_info,0,5);
        //获取活动评论人数信息
        $comments_info = $this->tuangou_model->comments_info($tuan_id,0);
        $data['comments_num'] = count($comments_info);
        $data['comments_info'] = array_slice($comments_info,0,5);

        //
        $wechat_info = $this->session->userdata('wechat_info');
        $data['tuan_info']->wechat_id = $wechat_info['id'];
        $data['tuan_info']->wechat_nickname = $wechat_info['nickname'];
        
        $this->load->view('mobile/tuan/active_page', $data);
	
	
    }
    
    function tuan_auth2() {    		
    	    
	    if(isset($_GET['code'])){	
         //var_dump($_GET['code']);
         //exit;	  
    		$token = $this->wechatauth->get_access_token('wxd11be5ecb1367bcf','6d05ab776fd92157d6833e2936d6f17c',$_GET['code']); //确认授权后会，根据返回的code获取token
		var_export($GET['code']);
		echo '<br/>';
		var_export($token);
		echo '<br/>';
    	    $wechat_user =  $this->wechatauth->get_user_info($token['access_token'], $token['openid']); //获取用户信息
            $check_access_token =  $this->wechatauth->check_access_token($token['access_token'], $token['openid']);  //检查access_token,验证授权

            var_export($wechat_user);
            var_export($check_access_token);exit;
            $wechat = array();
            $wechat['tuan_id'] = $_GET['id'];
	    
	        if(!isset($wechat_user['openid'])) {
	    	    $inser_we['wechat_openid'] = isset($wechat_user['errcode']) ? $wechat_user['errcode'] . time() : time();
                $inser_we['wechat_nickname'] = 'unknown';//isset($wechat_user['errmsg']) ? $wechat_user['errmsg'] : 'invalid nickname';
                $inser_we['wechat_sex'] = 0;
                $inser_we['wechat_city'] = 'unknown';
                $inser_we['wechat_province'] = 'unknown';
                $inser_we['wechat_country'] = 'unknown';;
                $inser_we['wechat_headimgurl'] = 'unknown';;
                $inser_we['tuan_id'] = $wechat['tuan_id'];
                $inser_we['wechat_date'] = date('Y-m-d H:i:s');

                $insert_id = $this->tuangou_model->wechat_insert($inser_we);

                $wechat['id'] =  $insert_id;
                $wechat['nickname'] = $wechat_user['nickname'];
	        } else {
	            //查看数据是否已经存库
	            $openid = $this->tuangou_model->filter_wechat(array('wechat_openid' => $wechat_user['openid'],'tuan_id' => $wechat['tuan_id']));
	            if(empty($openid)){
	                $inser_we['wechat_openid'] = $wechat_user['openid'];
	                $inser_we['wechat_nickname'] = $wechat_user['nickname'];
	                $inser_we['wechat_sex'] = $wechat_user['sex'];
	                $inser_we['wechat_city'] = $wechat_user['city'];
	                $inser_we['wechat_province'] = $wechat_user['province'];
	                $inser_we['wechat_country'] = $wechat_user['country'];
	                $inser_we['wechat_headimgurl'] = $wechat_user['headimgurl'];
	                $inser_we['tuan_id'] = $wechat['tuan_id'];
	                $inser_we['wechat_date'] = date('Y-m-d H:i:s');

	                $insert_id = $this->tuangou_model->wechat_insert($inser_we);

	                $wechat['id'] =  $insert_id;
	                $wechat['nickname'] = $wechat_user['nickname'];
	            } else {
	                $wechat['nickname'] = $openid->wechat_nickname;
	                $wechat['id'] = $openid->wechat_id;
	            }
	        }

            $is_authed = 1;

            $this->session->set_userdata('is_authed',$is_authed);
            $this->session->set_userdata('wechat_info',$wechat);

            header("Location:http://m.yueyawang.com/tuan/yueyatuan2/".$wechat['tuan_id']);  

	    }else{
		    echo "NO CODE";
	    }
    }

    function un_content(){
        $is_type = trim($this->input->post('is_type'));
        $tuan_id = trim($this->input->post('tuan_id'));
        if($is_type == 1) {
            //获取活动报名人数信息
            $un_info = $this->tuangou_model->register_info($tuan_id,0);
        }else if($is_type == 2){
            //获取活动评论人数信息
            $un_info = $this->tuangou_model->comments_info($tuan_id,0);
        }else{
            echo json_encode(array('error' => 1));
        }

        echo json_encode(array('un_info' => $un_info,'error' => 0));
    }

    function tuan_suit($tuan_id){

        $tuan_id = intval($tuan_id);
        if($tuan_id <= 0) {
            sys_msg('参数错误', 1);
        }

        $is_preview = isset($_GET['is_preview']) && $_GET['is_preview'] == 1 ? TRUE : FALSE;
        $key_one = 'v_tuan_suit' . $tuan_id;
        $key_to = 'v_tuanpackage_suit' . $tuan_id;

        if($is_preview){

            $package = $this->package_model->filter(array('package_id' => $tuan_id));
            $all_product = $this->package_model->all_product(array('package_id' => $tuan_id));
            $product_ids = '';
            foreach ($all_product as $all_pro) {
                $product_ids .= $all_pro->product_id.',';
            }
            $product_ids = rtrim($product_ids, ",");
            $suit_product = $this->tuangou_model->tuan_suit_product($product_ids);
            foreach ($suit_product as $key => $val_pro) {
                $suit_product_sub = $this->tuangou_model->pro_sub_size($val_pro['product_id']);
                $cart_list = get_pair($this->product_model->sub_in_cart(array('product_id' => $val_pro['product_id'])), 'sub_id', 'product_num');
                $sub_list = array();
                foreach ($suit_product_sub as $var => $sub) {
                    format_sub($sub);
                    if ($sub->sale_num != -2 && isset($cart_list[$sub->sub_id])) {
                        $sub->sale_num = max($sub->sale_num - $cart_list[$sub->sub_id], 0);
                    }
                    $sub->sale_num = $sub->sale_num == -2 ? MAX_SALE_NUM : min(MAX_SALE_NUM, $sub->sale_num);
                    if (!$sub->is_on_sale) {
                        $sub->sale_num = 0;
                    }
                    $suit_product_sub[$var]->sale_num = $sub->sale_num;
                }
                $suit_product[$key]['size'][0] = $suit_product_sub;
            }

            $this->cache->save($key_one, $suit_product, 3600 * 2);
            $this->cache->save($key_to, $package, 3600 * 2);

        }else{
            $suit_product = $this->cache->get($key_one);
            $package = $this->cache->get($key_to);

            if( $suit_product === FALSE || $package === FALSE ){
                $package = $this->package_model->filter(array('package_id' => $tuan_id));
                $all_product = $this->package_model->all_product(array('package_id' => $tuan_id));
                $product_ids = '';
                foreach ($all_product as $all_pro) {
                    $product_ids .= $all_pro->product_id.',';
                }
                $product_ids = rtrim($product_ids, ",");
                $suit_product = $this->tuangou_model->tuan_suit_product($product_ids);
                foreach ($suit_product as $key => $val_pro) {
                    $suit_product_sub = $this->tuangou_model->pro_sub_size($val_pro['product_id']);
                    $cart_list = get_pair($this->product_model->sub_in_cart(array('product_id' => $val_pro['product_id'])), 'sub_id', 'product_num');
                    $sub_list = array();
                    foreach ($suit_product_sub as $var => $sub) {
                        format_sub($sub);
                        if ($sub->sale_num != -2 && isset($cart_list[$sub->sub_id])) {
                            $sub->sale_num = max($sub->sale_num - $cart_list[$sub->sub_id], 0);
                        }
                        $sub->sale_num = $sub->sale_num == -2 ? MAX_SALE_NUM : min(MAX_SALE_NUM, $sub->sale_num);
                        if (!$sub->is_on_sale) {
                            $sub->sale_num = 0;
                        }
                        $suit_product_sub[$var]->sale_num = $sub->sale_num;
                    }
                    $suit_product[$key]['size'][$var] = $suit_product_sub;
                }

                $this->cache->save($key_one, $suit_product, 3600 * 2);
                $this->cache->save($key_to, $package, 3600 * 2);        
            }
        }
        $this->load->view('mobile/tuan/tuan_suit',array(
            'suit_product' => $suit_product,
            'package' => $package
        ));
    }
}
