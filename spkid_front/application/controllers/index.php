<?php 
/**
* Index Controller
*/
class Index extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->user_id = $this->session->userdata('user_id');
		$this->time = date('Y-m-d H:i:s');
        $this->load->vars('page_title','');//这里设置空title即可
	}
    public function error404(){
        $this->load->view('index/404');
    }
	/**
	 * 公开访问引导页面。
	 */
	public function start_page(){
		$startPage=@file_get_contents(static_style_url('index/mobile_first_page.html'));
	   if( strlen($startPage) > 200 ){
		   $time=substr($startPage,0,10);
		   //echo 
		   if (date('Y-m-d')<=$time){
			   echo substr($startPage,10);
			   exit();
		   }else{
				redirect('/index');
			}
	   }else{
		   redirect('/index');
	   }
	}
    public function course(){
        $this->load->model('product_model');
        if ($this->input->is_ajax_request()) {
            $page = $this->input->get('page');
            $expire = $this->input->get('expire');
            if (!$cid = $this->input->get('cid'))
                $cid = 0;
            $courses = $this->product_model->get_course_list($page, $expire, $cid);
            if (false == $courses){
                echo json_encode(array('course_list' => false));
            } else {
                $course_list = $this->load->view('index/course_item', array('courses' => $courses, 'expire' => $expire), true);
                echo json_encode(array('course_list' => $course_list));
            }
        } else {
            $courses = $this->product_model->get_course_list(1);
            $expire_courses = $this->product_model->get_course_list(1, true);
            $data = array('courses' => $courses, 'expire_courses' => $expire_courses, 'index' => 3);

            // 这里获取动态的seo
            $this->load->library('lib_seo');
            $seo = $this->lib_seo->get_seo_by_pagetag('pc_courses_index', array());
            $data = array_merge($data, $seo);            

            $this->load->view('index/course', $data);
        }
    }
    public function medical($cid = 113){
        $sql = 'SELECT cat_id,cat_name FROM ty_article_cat WHERE parent_id=112';
        $category = $this->db->query($sql)->result();
        //print_r($category);
        $category_list = array();
        foreach($category as $c){
            $id = $c->cat_id;
            $category_list[$id] = $c->cat_name;
        }
        $filter = array('page' => 1, 'page_size' => 5, 'cat_id' => $cid);
        $this->load->model('article_model');
        $article_list = $this->article_model->article_list($filter);
        $this->load->model('product_model');
        $name = $category_list[$cid];
        $sql = 'SELECT category_id FROM ty_product_category WHERE category_name=\''.$name.'\'';
        $cat_id = $this->db->query($sql)->first_row();
        $cat_id = $cat_id->category_id;
        $courses = $this->product_model->get_course_list(1, false, $cat_id);
        $is_login = $this->session->userdata('user_id');
        $data = array('cid' => $cid, 'category' => $category_list, 'article_list' => $article_list, 'courses' => $courses, 'is_login' => $is_login, 'pid' => $cat_id, 'index' => 3);
        $this->load->view('index/medical', $data);
    }


    /**
     * @param:导航id
     */
    public function index()
    {
        $data = array();
        $this->load->library('lib_ad');
        $this->config->load('global', true);
        $data = array();
        //pc首页轮播图  
        $data['pc_top_carousel'] = $this->lib_ad->get_focus_image_pc('pc_index_top_carousel', 4);   
        
        //pc首页广告位
        $data['pc_top_ad'] = $this->_get_ad('pc_top_ad','pc_top_ad');
        
        $this->load->model('product_model');
        $this->load->helper('product');

        //pc热销商品第一排
        $hot_sale_product1 = $this->_get_ad('pc_hot_sale_product1','pc_hot_sale_product1'); 
        if (isset($hot_sale_product1[0])) {
            $data['pc_hot_sale_product']['first']['col_name'] = $hot_sale_product1[0]->position_name;
        }
        foreach ($hot_sale_product1 as $key => $value) {
            $product_id = array();
            preg_match('/\/prodetail-(\d+)\.html/i', $value->ad_link, $product_id);
            if (empty($product_id)) {
                continue;
            }
            $p = $this->_get_cache_product_info($product_id[1]);
            $p[0]->ad_code = $value->ad_code;
            $data['pc_hot_sale_product']['first']['items'][] = $p;
        }

        //pc热销商品第二排        
        $hot_sale_product2 = $this->_get_ad('pc_hot_sale_product2','pc_hot_sale_product2');                
        if (isset($hot_sale_product2[0])) {
            $data['pc_hot_sale_product']['second']['col_name'] = $hot_sale_product2[0]->position_name;
        }        
        foreach ($hot_sale_product2 as $key => $value) {
            $product_id = array();
            preg_match('/\/prodetail-(\d+)\.html/i', $value->ad_link, $product_id);
            if (empty($product_id)) {
                continue;
            }
            $p = $this->_get_cache_product_info($product_id[1]);
            $p[0]->ad_code = $value->ad_code;
            $data['pc_hot_sale_product']['second']['items'][] = $p;
        }
        
        //pc热销课程第一排        
        $hot_sale_course1 = $this->_get_ad('pc_hot_sale_course1','pc_hot_sale_course1');
        if (isset($hot_sale_course1[0])) {
            $data['pc_hot_sale_course']['first']['col_name'] = $hot_sale_course1[0]->position_name;
        }    
        foreach ($hot_sale_course1 as $key => $value) {
            $product_id = array();
            preg_match('/\/product-(\d+)\.html/i', $value->ad_link, $product_id);
            if (empty($product_id)) {
                continue;
            }
            $p = $this->_get_cache_product_info($product_id[1]);
            $p[0]->ad_code = $value->ad_code;
            $data['pc_hot_sale_course']['first']['items'][] = $p;
        }

        //pc热销课程第二排        
        $hot_sale_course2 = $this->_get_ad('pc_hot_sale_course2','pc_hot_sale_course2');
        if (isset($hot_sale_course2[0])) {
            $data['pc_hot_sale_course']['second']['col_name'] = $hot_sale_course2[0]->position_name;
        }    
        foreach ($hot_sale_course2 as $key => $value) {
            $product_id = array();
            preg_match('/\/product-(\d+)\.html/i', $value->ad_link, $product_id);
            if (empty($product_id)) {
                continue;
            }
            $p = $this->_get_cache_product_info($product_id[1]);
            $p[0]->ad_code = $value->ad_code;
            $data['pc_hot_sale_course']['second']['items'][] = $p;
        }

        //pc推荐产品1
        $remcommand_pro = $this->_get_ad('pc_recommand_pro1','pc_recommand_pro1');

        if (isset($remcommand_pro[0])) {
           $data['pc_remcommand_pro']['first']['col_name'] = $remcommand_pro[0]->position_name;
        } 

        foreach ($remcommand_pro as $key => $value) {
           $product_id = array();
           preg_match('/\/prodetail-(\d+)\.html/i', $value->ad_link, $product_id);
           if (empty($product_id)) {
               continue;
           }
           if (isset($value->pic_url) && $value->pic_url) {
               $data['pc_remcommand_pro']['first']['col_pic_url'] = $value->pic_url;
           }
           //$p = $this->product_model->get_pc_index_product_info($product_id[1]);
           $p = $this->_get_cache_product_info($product_id[1]);
           $p[0]->ad_code = $value->ad_code;

           $data['pc_remcommand_pro']['first']['items'][] = $p;
        }
        
        //pc推荐产品2
        $remcommand_pro = $this->_get_ad('pc_recommand_pro2','pc_recommand_pro2');
        
        if (isset($remcommand_pro[0])) {
           $data['pc_remcommand_pro']['second']['col_name'] = $remcommand_pro[0]->position_name;
           $data['pc_remcommand_pro']['second']['col_pic_url'] = $remcommand_pro[0]->position_name;
        }    
        foreach ($remcommand_pro as $key => $value) {
           $product_id = array();
           preg_match('/\/prodetail-(\d+)\.html/i', $value->ad_link, $product_id);
           if (empty($product_id)) {
               continue;
           }
           if (isset($value->pic_url) && $value->pic_url) {
               $data['pc_remcommand_pro']['second']['col_pic_url'] = $value->pic_url;
           }
           //$p = $this->product_model->get_pc_index_product_info($product_id[1]);
           $p = $this->_get_cache_product_info($product_id[1]);
           $p[0]->ad_code = $value->ad_code;
           $data['pc_remcommand_pro']['second']['items'][] = $p;
        }

        //pc推荐课程1
        $remcommand_course = $this->_get_ad('pc_recommand_course1','pc_recommand_course1');

        if (isset($remcommand_course[0])) {
           $data['pc_remcommand_course']['first']['col_name'] = $remcommand_course[0]->position_name;
        }    
        foreach ($remcommand_course as $key => $value) {
           $product_id = array();
           preg_match('/\/product-(\d+)\.html/i', $value->ad_link, $product_id);
           if (empty($product_id)) {
               continue;
           }
           if (isset($value->pic_url) && $value->pic_url) {
               $data['pc_remcommand_course']['first']['col_pic_url'] = $value->pic_url;
           }

           //$p = $this->product_model->get_pc_index_product_info($product_id[1]);
           $p = $this->_get_cache_product_info($product_id[1]);
           $p[0]->collect_num = $this->product_model->get_product_collect($product_id[1]);
           $p[0]->ad_code = $value->ad_code;
           $data['pc_remcommand_course']['first']['items'][] = $p;
        }

        //pc展览商品
        $show_pro = $this->_get_ad('pc_show_pro','pc_show_pro');

        if (isset($show_pro[0])) {
           $data['pc_show_pro']['first']['col_name'] = $show_pro[0]->position_name;
        }    
        foreach ($show_pro as $key => $value) {
           $product_id = array();
           preg_match('/\/prodetail-(\d+)\.html/i', $value->ad_link, $product_id);
           if (empty($product_id)) {
               continue;
           }

           //$p = $this->product_model->get_pc_index_product_info($product_id[1]);  
           $p = $this->_get_cache_product_info($product_id[1]);
           $p[0]->ad_code = $value->ad_code;
           $data['pc_show_pro']['first']['items'][] = $p;
        }

        //牙医视频
        $this->load->model('wordpress_model');
        $cache_key_video = 'pc_index_video_list';
        $index_video_list = $this->cache->get($cache_key_video);   
        if(!isset($index_video_list) || empty($index_video_list)){
            $index_video_list = $this->wordpress_model->fetch_videos_for_index_page(223);
            $this->cache->save($cache_key_video, $index_video_list, CACHE_TIME_PC_INDEX_PRODUCT_INFO);      
        }
        $data['index_video_list'] = $index_video_list;    
		
        //品牌广告
        //品牌广告位(多个广告)
        $result = $this->_get_ad('pc_brand_list','pc_brand_list');

        foreach ($result as $k => &$v) {
            
            $v->ad_code = adjust_path($v->ad_code);            
        }
        $data['brand_list'] = $result;
       
        // 获取动态seo关键字
        $this->load->library('lib_seo');
        $seo = $this->lib_seo->get_seo_by_pagetag('pc_index');
        $data = array_merge($data, $seo);      
        $data['index'] = 0;
        
        $this->load->view('index/index',$data);
        
        
    }

    function personal_center(){
        $data = array(
            'name' => 'ddd',
            'title' => 'xxxx'
        );
        
        $this->load->view('index/personal_center',$data);
    }
    public function get_article(){
        $page = $this->input->get('page');
        $cat = $this->input->get('cat');
        $index = 'article_cat_'.$cat.'_'.$page;
        $articles = $this->cache->get($index);
        if ($articles == false){
            $this->load->model('wordpress_model');
            $articles = $this->wordpress_model->fetch_articles($cat, $page);
        //$this->memcache->delete('articles'); // delete key first
            $this->cache->save($index, $articles, 7200);
        }
        if( empty($articles) ) {
            $result['success'] = 0;
        }else {
            $result['data'] = $this->load->view('mobile/index/article',array('articles'=>$articles), true);
        }
        echo json_encode($result);
        //手动更新 memcache key 的函数方法
        //memcache_key_record('article_list','文章视频首页数据',__CLASS__,__FUNCTION__,str_replace(FCPATH,'',__FILE__));
    }

    function ajax_goods_list($page_name){
        $page = $this->input->get('page');
        // init 
        $result = array('success'=>1,'data'=>array(),'msg'=>'','img_domain'=>get_img_host());

        // exception
        if ($page > M_INDEX_PAGE_MAX){
            $result['success'] = 0;
            $result['message'] = 'all empty';
            die(json_encode($result));
        }


        // result's data
        if ($page_name == 'course'){
            $list = $this->_get_product_all(PRODUCT_COURSE_TYPE, $page);
            if( empty($list) ) {
                $result['success'] = 0;
            }else {
                $result['data'] = $this->load->view('mobile/index/course',array('courses'=>$list), true);
            }

        }else{
            $this->load->library('memcache');
            $goods_list = $this->memcache->get('index_goods_list');
            if ($goods_list && array_key_exists($page-1, $goods_list)){
                $result['data'] = $goods_list[$page];
            }else{
                $result['success'] = 0;
            }
        }

        die(json_encode($result));
    }

    //按大类获取所有的商品
    function _get_product_all($genre_id, $page=1){
        $product = $this->cache->get('product_all_'.$genre_id.'_'.$page);
        if ($product == false){
            $this->load->model('product_model');
            $product = $this->product_model->get_product_onsale($genre_id, $page);
            if(!empty($product))
            {
                $this->cache->save('product_all_'.$genre_id.'_'.$page, $product,CACHE_TIME_INDEX_PRODUCT);
            }
        }
        return $product;
    }
    /**
     * 获取预售rush
     */
    function _get_pre_rush()
    {
        $pre_rush=$this->cache->get('pre_rush');
        if($pre_rush==false)
        {
            $this->load->model('rush_model');
            $pre_rush=$this->rush_model->pre_rush();
            if(!empty($pre_rush))
            {
                $this->cache->save('pre_rush',$pre_rush,CACHE_TIME_PRE_RUSH);
            }
        }
        if(empty($pre_rush)) 
            return array('pre_rush'=>array(),'pre_title'=>array());
        $pre_rush_arr=array();
        //按日期分组
        foreach($pre_rush as $key=>$val)
        {
            $now=date('Y-m-d H:i:s');
            //如果rush已开始 则continue
            if($val->start_date<$now){
                continue;
            }
            //rush图片处理
            $temp_img_arr=explode('.',$val->image_before_url);
            $val->image_before_url1=$temp_img_arr[0].'_1.'.$temp_img_arr[1];
            $val->image_before_url2=$temp_img_arr[0].'_2.'.$temp_img_arr[1];
            $val->image_before_url3=$temp_img_arr[0].'_3.'.$temp_img_arr[1];
            $pre_rush_arr[$val->date][]=$val;
        }
        //处理title 即明天 后天等
        $today=strtotime(date('Y-m-d'));
        $pre_rush_title=array();
        $week=array('1'=>'一','2'=>'二','3'=>'三','4'=>'四','5'=>'五','6'=>'六','0'=>'日',);
        foreach($pre_rush_arr as $key=>$val)
        {
            //计算rush date与今天时间差
            $rush_date=strtotime($key);
            $date_diff=round(($rush_date-$today)/3600/24);
            $temp=array();
            $time=strtotime($key);
            if($date_diff==0)
                $temp['title']="今天";
            elseif($date_diff==1)
                $temp['title']="明天";
            else if($date_diff==2)
                $temp['title']="后天";
            else
                $temp['title']='周'.$week[date('w',$time)];
            
            $temp['date']=date('m',$time).'/'.date('d',$time);
            $pre_rush_title[]=$temp;
        }
        $pre_rush_result=array('pre_rush'=>$pre_rush_arr,'pre_title'=>$pre_rush_title);
        return $pre_rush_result;
    }

    /**
     * 获取正在进行的限抢
     */
    function _get_sale_rush($nav_id=0)
    {
        $rushes=$this->cache->get('sale_rush_'.$nav_id);
        if($rushes==false)
        {
            $this->load->model('rush_model');
            $rushes=$this->rush_model->get_sale_rush(array('nav_id'=>$nav_id));
            if(empty($rushes)) return array();
            $this->cache->save('sale_rush_'.$nav_id,$rushes,CACHE_TIME_SALE_RUSH);
        }
        foreach($rushes as $key=>$rush)
        {
            //计算还剩几天
            $time_diff=strtotime($rush->end_date)-time();
            if($time_diff<86400)
            {
                $rush->end_day=1;
            }
            else
            {
                $rush->end_day=ceil($time_diff/86400);
            }
            if(!empty($rush->image_before_url))
            {
                $img_arr=explode('.',$rush->image_before_url);
                $rush->image_before_url_1=$img_arr[0].'_1.'.$img_arr[1];
                $rush->image_before_url_2=$img_arr[0].'_2.'.$img_arr[1];
                $rush->image_before_url_3=$img_arr[0].'_3.'.$img_arr[1];
            }
        }
        return $rushes;
    }
    
    /**
     * 获取今日结束的rush
     */
    function _get_today_over_rush()
    {
        $today_over_rush=$this->cache->get('today_over_rush');
        if($today_over_rush===false)
        {
            $this->load->model('rush_model');
            $today_over_rush=$this->rush_model->get_sale_rush(array('today'=>true));
            $this->cache->save('today_over_rush',$today_over_rush,CACHE_TIME_TODAY_OVER_RUSH);
        }
        //计算距离结束时间 以最晚的rush end_date为准
        $last_end_date='1970-01-01';
        foreach($today_over_rush as $key=>$rush)
        {
            //如果结束时间不等于今天 则unset掉
            if(date('Y-m-d')!=date('Y-m-d',strtotime($rush->end_date)))
            {
                unset($today_over_rush[$key]);
                continue;
            }
            if($rush->end_date>$last_end_date)
                $last_end_date=$rush->end_date;
            if(!empty($rush->image_before_url))
            {
                $img_arr=explode('.',$rush->image_before_url);
                $rush->image_before_url_1=$img_arr[0].'_1.'.$img_arr[1];
                $rush->image_before_url_2=$img_arr[0].'_2.'.$img_arr[1];
                $rush->image_before_url_3=$img_arr[0].'_3.'.$img_arr[1];
            }
        }
        $time_diff=strtotime($last_end_date)-time();
        $time_diff=$time_diff<0?0:$time_diff;
        $this->load->vars(array(
                    'today_over_hour'=>floor($time_diff/3600),
                    'today_over_min'=>ceil(($time_diff%3600)/60)));
        return $today_over_rush;
    }

    /**
     * 检查rush时间是否有效
     */
    function _check_rush($rushes)
    {
        if(empty($rushes))
            return $rushes;
        $new_rush=array();
        foreach($rushes as $key=>$val)
        {
            if($val->end_date<date('Y-m-d H:i:s')||$val->start_date>date('Y-m-d H:i:s'))
            {
                //排除不符合时间的rush
                continue;
            }
            array_push($new_rush,$val);
        }
        return $new_rush;
    }


    /**
     * 根据key或position_id获取广告
     */
    function _get_ad($cache_key,$position_tag, $size=0)
    {
        $this->load->library('lib_ad');
        $data = $this->lib_ad->get_ad_by_position_tag($cache_key,$position_tag, $size);
        //var_export($data);exit;
        return $data;
    }  

    function _get_cache_product_info($product_id) {
        $cache_key = 'pc_index_product_info_' . $product_id;
        $is_preview = intval(trim($this->input->get('is_preview')));

        if ($is_preview == 1) {
            $product_info = false;            
        } else {
            $product_info = $this->cache->get($cache_key);    
        }    

        if (!$product_info) {
            $product_info = $this->product_model->get_pc_index_product_info($product_id);    
            $this->cache->save($cache_key, $product_info, CACHE_TIME_PC_INDEX_PRODUCT_INFO);            
        }
        return $product_info;
    }
      
}
