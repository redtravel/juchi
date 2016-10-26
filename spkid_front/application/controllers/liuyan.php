<?php
/**
* Liuyan
*/
class Liuyan extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->user_id = intval($this->session->userdata('user_id'));
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('liuyan_model');
	}

	public function newest()
	{
//		$this->load->library('Memcache');
		$category_id=intval($this->input->post('category_id'));
		$brand_id=intval($this->input->post('brand_id'));
		$kw=trim($this->input->post('kw',TRUE));
		$filter=array('category_id'=>$category_id?$category_id:0,'brand_id'=>$brand_id?$brand_id:0,'kw'=>$kw?$kw:'');
		$cache_key='liuyan-newest-'.implode('-',$filter);
		if(($liuyan_list=$this->cache->get($cache_key))===FALSE){
			$liuyan_list=$this->liuyan_model->newest_liuyan($filter);
			$this->cache->save($cache_key,$liuyan_list,CACHE_TIME_LIUYAN);
		}
		if(!$liuyan_list){
			print json_encode(array('err'=>1,'msg'=>'没有留言'));
		}else{
			print json_encode(array('err'=>0,'msg'=>'','html'=>$this->load->view('liuyan/newest',array('liuyan_list'=>$liuyan_list),TRUE)));
		}
	}

	public function liuyan_list()
	{
		$comment_type=intval($this->input->post('comment_type'));		
		//$com_type = ($comment_type == 2 ? 0 : $comment_type);

		$com_type = $comment_type;
		$tag_type=intval($this->input->post('tag_type'));
		$tag_id=intval($this->input->post('tag_id'));
		$page=intval($this->input->post('page'));
		$user_id = intval($this->input->post('user_id'));
		$param=array(
			'tag_id'=>$tag_id,
			'tag_type'=>$tag_type,
			'comment_type'=>$com_type,
			'page'=>$page?$page:0,
			'user_id' => $user_id ? $user_id : 0
			);		
		
		$data = $this->liuyan_model->liuyan_list($param);
		
		switch ($comment_type) {
			case 1:			
			case 3:
			case 4:			
				$view = 'liuyan_list';
				break;	
			case 2:
				$view = 'pingjia_list';
				break;		
			default:
				$view = 'error';
				break;
		}

		$html = $this->load->view('liuyan/'.$view,array(
			'param' => $param,
			'list' => $data['list'],
			'filter' => $data['filter']
		),TRUE);

		print json_encode(array('err'=>0,'msg'=>'','html'=>$html,'count'=>$data['filter']['record_count']));
	}

	public function proc_zixun() {
	    $this->load->library("user_obj");	    
	    $update['comment_type'] = intval($this->input->post('comment_type'));
	    $update['tag_type'] = intval($this->input->post('tag_type'));
	    $update['tag_id'] = intval($this->input->post('tag_id'));
	    $update['comment_content'] = trim($this->input->post('comment_content', TRUE));
	    $user_id = $this->session->userdata('user_id');
	    $update['user_id'] = $user_id;
	    $update['comment_date'] = $this->time;
	    $update['comment_title'] = '';
	    $update['comment_ip'] =real_ip();
	    $update['reply_content'] = '';
	    $update['name'] = trim($this->input->post('name', TRUE));
	    $update['mobile'] = trim($this->input->post('mobile', TRUE));
	    $update['at_comment_id'] = trim($this->input->post('at_comment_id', TRUE));

//	    $user_name = trim($this->input->post('user_name'));
//	    $password = trim($this->input->post('pwd'));
//	    if (!empty($user_name ) && !empty($password ) ){
//		if ( $this->user_obj->login($user_name, $password ) == FALSE) {
//		    sys_msg('用户名或密码错误,请重试', 1);
//		}
//	    }
	    if(!preg_match("/[\x7f-\xff]/", $update['comment_content'])){
	    		sys_msg('咨询内容应包含汉字', 1);
	    }
	    
	    if(false && mb_strlen($update['comment_content']) < 5 ){
		sys_msg('咨询内容至少为5个汉字', 1);
	    }else if( mb_strlen($update['comment_content']) > 200 ) {
		sys_msg('咨询内容至多为200个汉字', 1);
	    }
	    
	    if (!in_array($update['comment_type'], array(1, 2, 3, 4)))
		sys_msg('参数错误', 1);
	    if (!in_array($update['tag_type'], array(1, 2, 3, 4))) //4表示视频
		sys_msg('参数错误', 1);
	    if (!$update['comment_content'])
		sys_msg('请填写咨询内容', 1);
		$this->load->helper('sensivewords');
		if (!filter($update['comment_content'])) {
			sys_msg('内容中还有非法词汇', 1);
		} else {
			$update['is_audit'] = 1;
		}

	    switch ($update['tag_type']) {
		case 1:
		case 3:		
		case 4:
		    # 商品
		    $this->load->model('product_model');
		    $p = $this->product_model->filter(array('product_id' => $update['tag_id']));
		    if (!$p)
			sys_msg('发表失败，参数错误', 1);
		    $this->liuyan_model->insert($update);{
			sys_msg('发表成功，谢谢您的留言', 0);
		    }
		    break;

		default:
		    
		    break;
	    }
    }

    public function delete_liuyan() {
    	$res = array('res' => 0, 'msg'=>'');
    	
    	if (!intval($this->user_id)) {
    		$res['res'] = 1;
    		$res['msg'] = '未登录';
    		echo json_encode($res);exit();
    	}
    	$cid = trim($this->input->post('cid', true));
    	
    	$is_ok = $this->liuyan_model->delete($cid);

    	if ($is_ok) {
    		$res['res'] = 0;//成功删除
    		$res['msg'] = '成功删除';
    	} else {
    		$res['res'] = 2;//数据库错误
    		$res['msg'] = '数据库错误';
    	}

    	echo json_encode($res);exit();
    }
}