<?php
/**
*
*/
class Search extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->user_id = $this->session->userdata('user_id');
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('search_model');
		$this->load->model('product_model');
		$this->load->model('wordpress_model');
	}

	public function index(){
		$data = array();
		
		//分词搜索
		$kw=trim($this->input->get('kw',true));
		if (!$kw) {
			$kw = $this->uri->segment(3);
		}
		
		$this->load->library('sphinxclient');
        $this->sphinxclient->SetServer(SPHINX_SERVER_IP,9312);
        //$this->sphinxclient->SetMatchMode(SPH_MATCH_ANY);
        $this->sphinxclient->SetSortMode(SPH_SORT_EXTENDED,'@relevance desc,@weight desc');

        $base = $this->sphinxclient->Query($kw, 'base');  		//产品
        if ($base['total_found'] != 0){
            $base_ids = implode(',', array_keys($base['matches']));      
        }else{ $base_ids = 0 ;}

        $show = $this->sphinxclient->Query($kw, 'show');  		//展品
        if ($show['total_found'] != 0){
            $show_ids = implode(',', array_keys($show['matches']));      
        }else{ $show_ids = 0 ;}

        $course = $this->sphinxclient->Query($kw, 'course');	//课程
        if ($course['total_found'] != 0){
            $course_ids = implode(',', array_keys($course['matches']));      
        }else{ $course_ids = 0 ;}

        $video = $this->sphinxclient->Query($kw, 'video'); 		//视频
        if ($video['total_found'] != 0){
            $video_ids = implode(',', array_keys($video['matches']));      
        }else{ $video_ids = 0 ;}

		$data['kw'] = $kw;  //搜索的关键字
		$data['product'] = $this->product_model->get_search_product($base_ids);  //搜索的产品
		$data['exhibit'] = $this->product_model->get_search_product($show_ids);  //搜索的展品
		$data['course'] = $this->product_model->get_search_product($course_ids);  //搜索的课程
		$data['video'] = $this->wordpress_model->get_search_video($video_ids);  //搜索的视频


		// 搜索页热搜的5个产品
        $data['search_hot'] = $this->search_model->all_hot();
   
		$this->load->view('search/index',$data);
	}

}

