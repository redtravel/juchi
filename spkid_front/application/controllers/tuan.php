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
	}

    /**
     * @param:$args
     */
    public function index( $args = null)
    {
        $this->load->model('tuangou_model');
        //$this->output->cache(CACHE_HTML_INDEX);
        $args = array_slice(array_pad(array_map('intval', explode('-', $args)), 3, 0), 0, 3);
	    $args_keys = array('sort_type', 'sort', 'page');
	    $args = array_combine($args_keys, $args);
        $data['sort_type']=$args['sort_type'];
        $data['sort']=$args['sort'];
        $data['page']=$args['page'];
        $data['pagesize']=60;
        $maxpage = intval($this->tuangou_model->getTuanCount()/$data['pagesize']);
        $data['maxpage']=$maxpage;
        $tuaninfo = $this->tuangou_model->getTuaninfo($args['sort_type'],$args['sort'],$args['page'],$data['pagesize']);
        $data['tuaninfo']=$tuaninfo;
        
        //最近浏览的商品
        if($this->user_id)  $cookiekey = 'recentPro'.$this->user_id;
        else $cookiekey = 'recentPro';
        $recentPro = $this->input->cookie($cookiekey);
        $tuaninfoRec = '';
        if($recentPro) {
            $tuaninfoRec = $this->tuangou_model->getTuaninfoByTuanIdList($recentPro);
        }
        $data['tuaninfoRec']=$tuaninfoRec;
        
        /*首页广告begin*/
        //顶部广告
        $top_ad=$this->_get_ad('tuan_index_top_focus_ad','tuan_index_top_focus');
        if(!empty($top_ad))$data['top_ad']=$top_ad;
        //主广告
        $main_ad=$this->_get_ad('tuan_index_top_ad','tuan_index_top');
        if(!empty($main_ad))$data['main_ad']=$main_ad;
        /*首页广告end*/
        
        /*团购和今日团购*/
        $this->load->library('memcache');
        $data['tuan_all_goods_num']=$this->memcache->get('tuan_all_goods_num');
        $data['tuan_all_goods_num']=$data['tuan_all_goods_num']?$data['tuan_all_goods_num']:0;
        $data['tuan_today_goods_num']=$this->memcache->get('tuan_today_goods_num');
        $data['tuan_today_goods_num']=$data['tuan_today_goods_num']?$data['tuan_today_goods_num']:0;
        $data['tuan_today_brands_num']=$this->memcache->get('tuan_today_brands_num');
        $data['tuan_today_brands_num']=$data['tuan_today_brands_num']?$data['tuan_today_brands_num']:0;
        
        $this->load->view('tuan/tuan',$data);
    }
    
    //清除最近浏览cookie
    function clear_cookie()
    {
        if($this->user_id)  $cookiekey = 'recentPro'.$this->user_id;
        else $cookiekey = 'recentPro';
        
        $this->input->set_cookie($cookiekey, '', 0);
        redirect('tuan');
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
