<?php

#doc
#	classname:	Article
#	scope:		PUBLIC
#
#/doc

class Article extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->time = date('Y-m-d H:i:s');
		$this->user_id = $this->session->userdata('user_id');
		$this->load->model('article_model');
	}
	public function index()
	{
		redirect('index');
	}

	public function info($article_id)
	{
		$this->load->library('memcache');
		$article_id = intval($article_id);
		if(($article=$this->memcache->get('artilce_'.$article_id))==FALSE){
			$article = $this->article_model->filter(array('article_id'=>$article_id));
			if(!$article) sys_msg('文章不存在',1);
			$article->cat=$this->article_model->filter_cat(array('cat_id'=>$article->cat_id));
			//路径替换
			$article->content = adjust_path($article->content);
			$this->memcache->save('artilce_'.$article_id,$article,CACHE_TIME_ARTICLE);
		}
		if($article->url) {
			redirect($article->url);
			return;
		}
                if ( ($all_cat = $this->memcache->get('all_article_cat'))==FALSE )
		{
			$all_cat = $this->article_model->all_cat(array('parent_id'=>ARTICLE_CAT_SPEC,'is_use'=>1));
			$this->memcache->save('all_article_cat',$all_cat,CACHE_TIME_ARTICLE);
		}		
		$cat_ids = array();
		foreach( $all_cat as &$cat )
		{
			$cat_ids[] = $cat->cat_id;
			$cat->article_list = array();
		}
		$all_cat = index_array($all_cat,'cat_id');		
		if(!in_array($article->cat_id,$cat_ids)) sys_msg('文章不存在',1);
		
		if ( ($all_article = $this->memcache->get('all_article'))==FALSE )
		{
			$all_article = $this->article_model->all_article(array('cat_id'=>$cat_ids,'is_use'=>1));
			$this->memcache->save('all_article',$all_article,CACHE_TIME_ARTICLE);
		}
		
		foreach($all_article as $a)
		{
			if(!isset($all_cat[$a->cat_id])) continue;
			$all_cat[$a->cat_id]->article_list[] = $a;
		}
		$this->load->vars(array(
			'article' => $article,
			'all_cat' => $all_cat,
			'title' => $article->title,
			'keywords' => $article->keywords,
			'description' => "{$article->title} {$article->keywords}"
		));
                $this->load->view('article/index');       
//		if($article->cat->parent_id==ARTICLE_CAT_SPEC){
//			$this->load->view('article/info_spec');
//		}elseif($article->cat->parent_id==ARTICLE_CAT_HELP){
//			redirect("help-{$article_id}");
//		}else{
//			$this->load->view('article/info');
//		}
		
	}
    public function comment(){
        $is_ajax = $this->input->post('is_ajax');
        if (!$is_ajax)
            return false;
        $data['comment_post_ID'] = $this->input->post('post_id');
        $data['comment_content'] = $this->input->post('content');
        $data['comment_parent'] = $this->input->post('comment_parent');
        $data['yyw_user_id'] = $this->user_id;
        $this->load->model('wordpress_model');
        $res = $this->wordpress_model->comment_article($data);
        echo $res;
    }

    public function search(){
        $this->load->model('wordpress_model');
        //$vars = get_class_vars(get_class($this->wordpress_model));
        $kw = $this->input->get('kw');
        if(!empty($kw)){
            $kw = urldecode($kw);
            $article_list = $this->wordpress_model->search_article($kw);
            if(!empty($article_list))
                $this->load->view('mobile/article/search_ajax', array('list' => $article_list));
            else
                echo 'empty';
            //echo $html;
        } else{
            $this->load->view('mobile/article/search');
        }
        
    }
    public function detail($id){
        $this->load->model('wordpress_model');
        $article_detail = $this->wordpress_model->get_article_detail($id);
        if (false === $article_detail)
        die('文章不存在!');

    	//获取文章的点赞数量
		$article_praise_num = $this->wordpress_model->article_praise_num($id);

        //print_r($article_detail);
        $tags = $article_detail->tags;
        $tagArr = explode('&', $tags);
        $arr = array();
        foreach($tagArr as $tag){
            $tag = explode('=', $tag);
            list($name, $value, $cid) = $tag;
            //echo $name, $cid, ' ';
            /*if (!isset($$name)){
                $$name = array($cid => $value);
            } else*/
                $arr[$name][$cid] = $value;
        }
        foreach($arr as $name => $value){
            $$name = $value;
        }
        if (empty($post_tag)){
            $post_tag = '';
        } else{
            $post_tag = implode('&nbsp;', $post_tag);
        }
        $views = $this->wordpress_model->get_article_views($id);
        $prev = $this->wordpress_model->get_sibing_id($id, '<');
        $next = $this->wordpress_model->get_sibing_id($id, '>');
        
        if (isset($category)){
            $cids = array_keys($category);
            $relative_articles = $this->wordpress_model->get_relative_articles($cids, $id);
        } else{
            $relative_articles = false;
        }
        $category = implode('&nbsp;', $category);
        //$sql = "SELECT term_id FROM wp_terms WHERE name = '$category'";

        //$tags = parse_str($tags, $tagArr);
        //print_r($post_tag);
        $this->load->library('lib_seo');
        $seo = $this->lib_seo->get_seo_by_pagetag('article_detail', array(
								'post_title' => $article_detail->post_title							
								));
        $this->load->vars(array('article' => $article_detail, 'tag' => $post_tag, 'category' => $category, 'views' => $views, 'prev' => $prev, 'next' => $next, 'relative_articles' => $relative_articles,
        	'title' => $seo['title'],'collect_data'=>get_collect_data(),'praise_data'=>get_praise_data(),'article_praise_num'=>$article_praise_num->praise_num));
        $this->load->view('mobile/article/detail');
    }
	
	public function help ($article_id)
	{
		$this->load->library('memcache');
		$article_id = intval($article_id);
		if(($article=$this->memcache->get('artilce_'.$article_id))==FALSE){
			$article = $this->article_model->filter(array('article_id'=>$article_id));

			if(!$article||!$article->is_use) sys_msg('文章不存在',1);
			//路径替换
			$article->content = adjust_path($article->content);
			$this->memcache->save('artilce_'.$article_id,$article,CACHE_TIME_ARTICLE);
		}
		if ( ($all_cat = $this->memcache->get('all_help_cat'))==FALSE )
		{
			$all_cat = $this->article_model->all_cat(array('parent_id'=>ARTICLE_CAT_HELP,'is_use'=>1));
			$this->memcache->save('all_help_cat',$all_cat,CACHE_TIME_ARTICLE);
		}		
		$cat_ids = array();
		foreach( $all_cat as &$cat )
		{
			$cat_ids[] = $cat->cat_id;
			$cat->article_list = array();
		}
		$all_cat = index_array($all_cat,'cat_id');		
		if(!in_array($article->cat_id,$cat_ids)) sys_msg('文章不存在',1);

		if ( ($all_article = $this->memcache->get('all_help_article'))==FALSE )
		{
			$all_article = $this->article_model->all_article(array('cat_id'=>$cat_ids,'is_use'=>1));
			$this->memcache->save('all_help_article',$all_article,CACHE_TIME_ARTICLE);
		}
		
		foreach($all_article as $a)
		{
			if(!isset($all_cat[$a->cat_id])) continue;
			$all_cat[$a->cat_id]->article_list[] = $a;
		}
		$this->load->vars(array(
			'article' => $article,
			'all_cat' => $all_cat,
			'title' => $article->title,
			'keywords' => $article->keywords,
			'description' => "{$article->title} {$article->keywords}"
		));
		$this->load->view('article/help');
	}

	// 点赞
	public function add_to_praise()
	{
		$this->load->model('wordpress_model');

		//判断用户是否登录
		if(!$this->user_id) {
			print json_encode(array('err'=>0,'msg'=>0));
			return;
		}

		$article_id = intval($this->input->post('article_id'));

		//判断用户是否点赞
		$col=$this->wordpress_model->filter_praise(array('post_id'=>$article_id,'user_id'=>$this->user_id));
		if(!empty($col)){
			sys_msg('已经点过赞咯！',1);
		} 

		//判断 点赞的文章 是否存在
		$p=$this->wordpress_model->filter(array('ID'=>$article_id,'post_status'=>'publish'));
		if(empty($p)) sys_msg('此文章不存在',1);    // 文章

		$praise = array(
			'post_id' => $article_id,
			'user_id' => $this->user_id,
			'ip_address' => $_SERVER["REMOTE_ADDR"],
			'type_source' => 'yyw_moblie'
		);

		//将某个 文章的 点赞记录写入db
		$this->wordpress_model->insert_praise($praise);

		$praise_data = array();
		$praise_data[] =$praise;

		//将 用户 点赞 的文章 写入session
		if(isset($_SESSION['praise_'.$this->user_id])){
			array_push($praise_data[],$_SESSION['praise_'.$this->user_id]);
		}
		$this->session->set_userdata('praise_'.$this->user_id, $praise_data);

		//获取文章的点赞数量
		$article_praise_num = $this->wordpress_model->article_praise_num($article_id);

		print json_encode(array('err'=>0,'msg'=>'', 'praise_num'=>$article_praise_num->praise_num));
	}

}
