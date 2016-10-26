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


    /**
     * @param:导航id
     */
    public function index($tab='')
    {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control:no-cache,must-revalidate");
        header("Pragma:no-cache");
       // 引导页
       if (!isset($_COOKIE['fp-started'])){
	       $startPage=@file_get_contents(static_style_url('index/mobile_first_page.html'));
	       if( strlen($startPage) > 200 ){
		       $time=substr($startPage,0,10);
		       //echo 
		       if (date('Y-m-d')<=$time){
			       echo substr($startPage,10);
			       exit();
		       }
	       }
       }
       $avail_tabs = array( 'article','course', 'index','user' );
       //$this->output->cache(CACHE_HTML_INDEX);
       if( empty($tab) || !in_array($tab,$avail_tabs)) $tab = 'index';
       
       if( $tab == 'course' ) get_page_view('course','-2');
       else if( $tab == 'article' ) get_page_view('article','-3');
       else get_page_view('product','-1');

        $data = array();
        $this->load->library('lib_ad');
        $this->config->load('global', true);
        $data = array();
        $data['hot_brand_arr'] = array();        
        $goods_list = $this->memcache->get('index_goods_list'); //这里的数据是后台定时任务跑出来的

        if (is_array($goods_list))
            $data['index_good'] = $goods_list[0];
        else $data['index_good'] = false;
        // 轮播图
        $front_focus_image = $this->lib_ad->get_focus_image(INDEX_FOCUS_IMAGE_TAG);
        $course_focus_image = $this->lib_ad->get_focus_image(INDEX_COURSE_FOCUS_IMAGE_TAG, 5);
        /*foreach ($front_focus_image as $k => $row) {
            $front_focus_image[$k]['img_src'] = img_url($row['img_src']);
        }*/
        
        
        //品牌广告位(多个广告)
        $ad = $this->_get_ad('m_index_brand_row','m_index_brand_row');
        if(!empty($ad))
            $data['ad']=$ad;
        $ad = $this->_get_ad('miaosha','miaosha');
        if(!empty($ad))
            $data['ad1']=$ad;

        $this->load->model('wordpress_model');
        $articles = array();
        $articles['cat_0'] = $this->wordpress_model->fetch_articles(0, 1);
       // $articles['cat_241'] = $this->wordpress_model->fetch_articles(241, 1);//产品，暂时去掉
        $articles['cat_3'] = $this->wordpress_model->fetch_articles(3, 1);//技术
        $articles['cat_1'] = $this->wordpress_model->fetch_articles(1, 1);// 行业
        $data['articles'] = $articles;
        //首页产品列表

        /*$ad1 = $this->_get_ad('m_index_brand_row1','m_index_brand_row1');
        if(!empty($ad1))
            $data['ad1']=$ad1[0];*/
        // 课程广告位
        $course_ad = $this->_get_ad(INDEX_COURSE_TOP_TAG,INDEX_COURSE_TOP_TAG, 1);
        $data['course_ad'] = empty($course_ad) ? array() : $course_ad;
        //$course_list = $this->memcache->get('course_list');
        $course_list = $this->_get_product_all(PRODUCT_COURSE_TYPE);
        $data['courses'] = empty($course_list) ? array() : $course_list;
        
        //商品收藏 数组
        $data['collect_data'] = get_collect_data();

        // 获取动态seo关键字
        $this->load->library('lib_seo');
        $seo = $this->lib_seo->get_seo_by_pagetag('index');
        $data = array_merge($data, $seo);      

        $this->load->vars(array(
            'active_tab'=> $tab,
            'index_focus_image' => $front_focus_image, 
            'course_focus_image' => $course_focus_image, 
        ));
 
        $this->load->view('mobile/index/index',$data);
        
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
    }

    function ajax_goods_list($page_name){
        $page = $this->input->get('page');
        // init 
        $result = array('success'=>1,'data'=>array(),'msg'=>'','img_domain'=>get_img_host());

        $m_page_max = M_INDEX_PAGE_MAX;

        if(defined("M_".strtoupper($page_name)."_PAGE_MAX")) $m_page_max = constant("M_".strtoupper($page_name)."_PAGE_MAX");
        
        // exception
        if ($page > $m_page_max){
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
                $result['data'] = $goods_list[$page - 1];
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
        return $this->lib_ad->get_ad_by_position_tag($cache_key,$position_tag, $size);
    }  

    public function err404() {
        $this->load->view('mobile/index/err404');
    } 
      
}
