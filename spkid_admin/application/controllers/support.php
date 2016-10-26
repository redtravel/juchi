<?php 

/**
* Support
*/
class Support extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('index/login');
		}
		$this->time=date('Y-m-d H:i:s');
		$this->load->model('support_model');
		$this->status=array(0=>'未开始',1=>'会话中',2=>'已结束');
	}

	public function index()
	{
		auth('support');
		//取出当前属排队中的和self的issue
		$issue_list=$this->support_model->my_issue_list($this->admin_id);
		//对每一个self的issue,取出聊天记录
		$issue_ids=array();
		foreach($issue_list as $issue){
			if($issue->status==1) $issue_ids[]=$issue->rec_id;
		}
		$history=array();
		$log=array();
		$last_message_id=0;
		if($issue_ids){
			foreach ($issue_ids as $issue_id) {
				$message_list=array_reverse($this->support_model->recent_message($issue_id));				
				$history[$issue_id]=$message_list;
				$message=end($message_list);
				if($message&&$message->message_id>$last_message_id) {
					$last_message_id=$message->message_id;
				}
				$log[$issue_id]=$this->support_model->recent_log($issue_id);
			}
		}
		$this->load->vars();
		$this->load->view('support/index',array(
			'issue_list' => $issue_list,
			'history' => $history,
			'log'=>$log,
			'last_message_id' => $last_message_id
		));
	}

	public function refresh()
	{
		auth('support');
		$last_message_id=intval($this->input->post('last_message_id'));
		//取出当前属排队中的和self的issue
		$issue_list=index_array($this->support_model->my_issue_list($this->admin_id),'rec_id');
		//取出最近的历史记录
		$history=array();
		$message_list=$this->support_model->recent_user_message($this->admin_id,$last_message_id);
		foreach ($message_list as $message) {
			if(!isset($history[$message->rec_id])) $history[$message->rec_id]=array();
			$history[$message->rec_id][]=$message;
			if($message->message_id>$last_message_id) $last_message_id=$message->message_id;
		}
		print json_encode(array('err'=>0,'msg'=>'','last_message_id'=>$last_message_id,'issue_list'=>$issue_list,'history'=>$history));
	}

	public function open_issue()
	{
		auth('support');
		if(!$this->session->userdata('is_online')) sys_msg('请先上线',1);
		$issue_id=intval($this->input->post('issue_id'));
		$issue=$this->support_model->filter(array('rec_id'=>$issue_id));
		if(!$issue||$issue->status==2||($issue->status==1&&$issue->admin_id!=$this->admin_id)) sys_msg('会话不存在或会话已关闭或已被其它客服处理',1);
		$this->support_model->update(array('status'=>1,'admin_id'=>$this->admin_id),$issue_id);
		if($issue->user_id){
			$this->load->model('user_model');
			$user=$this->user_model->filter(array('user_id'=>$issue->user_id));
			$issue->user_name=$user->user_name;
		}else{
			$issue->user_name='';
		}
		$history=array_reverse($this->support_model->recent_message($issue_id));
		$log=$this->support_model->recent_log($issue_id);
		$html=$this->load->view('support/issue',array('issue'=>$issue,'history_list'=>$history,'log_list'=>$log),TRUE);
		
		$last_msg=end($history);		
		print json_encode(array(
			'issue'=>$issue,
			'html'=>$html,
			'has_new_msg' => $last_msg && $last_msg->admin_id==0
		));
	}

	public function close_issue()
	{
		auth('support');
		$issue_id = intval($this->input->post('issue_id'));
		$save = intval($this->input->post('save'));
		$issue=$this->support_model->filter(array('rec_id'=>$issue_id));
		// 如果issue已被关闭，则反回正常值
		if(!$issue||$issue->status!=1||$issue->admin_id!=$this->admin_id) sys_msg('');
		$this->support_model->update(array('status'=>$save&&$issue->status!=2 ? 0:2),$issue_id);
		print json_encode(array('err'=>0,'msg'=>''));
	}

	public function post_message()
	{
		auth('support');
		$issue_id = intval($this->input->post('issue_id'));
		$message=trim($this->input->post('message',TRUE));
		if(!$message) sys_msg('消息内容为空',1);
		$issue=$this->support_model->filter(array('rec_id'=>$issue_id));
		if(!$issue||$issue->status!=1||$issue->admin_id!=$this->admin_id) sys_msg('会话不存在或不可操作',1);
		$message=array(
			'rec_id'=>$issue_id,
			'content'=>$message,
			'qora' => 1,
			'user_id' => $issue->user_id,
			'admin_id' => $this->admin_id,
			'create_date' => $this->time
		);
		$message['message_id']=$this->support_model->insert_message($message);
		$message['admin_name'] = $this->session->userdata('admin_name');
		print json_encode(array('err'=>0,'msg'=>'','message'=>$message));
	}

	public function load_history()
	{
		auth('support');
		$filter['issue_id'] = intval($this->input->post('issue_id'));
		$filter['page'] = intval($this->input->post('page'));
		if($filter['page']==-1) $filter['page']=9999;
		$data=$this->support_model->message_list($filter);
		$this->load->vars($data);
		$html=$this->load->view('support/history','',TRUE);
		$page=$this->load->view('support/history_page','',TRUE);
		print json_encode(array('err'=>0,'msg'=>'','html'=>$html,'page'=>$page));		
	}

	public function issue_list()
	{
		auth('support');
		$filter = array();
		$filter['user_name'] = trim($this->input->post('user_name'));
		$filter['start_date'] = trim($this->input->post('start_date'));
		$filter['end_date'] = trim($this->input->post('end_date'));
		$filter['status'] =($status=$this->input->post('status'))===FALSE?-1:intval($status);

		$this->load->vars('status',$this->status);
		$filter = get_pager_param($filter);
		$data = $this->support_model->issue_list($filter);
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('support/issue_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('support/issue_list', $data);
	}

	public function message_list($issue_id)
	{
		auth('support');
		$filter = array('issue_id'=>intval($issue_id));	
		$filter['start_date'] = trim($this->input->post('start_date'));
		$filter['end_date'] = trim($this->input->post('end_date'));	
		
		$filter = get_pager_param($filter);
		$data = $this->support_model->message_list($filter);
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('support/message_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('support/message_list', $data);
	}
	
	public function log_list($issue_id=0)
	{
		auth('support');
		$filter = array('issue_id'=>intval($issue_id));	
		$issue_id=intval($this->input->post('issue_id'));
		if(!empty($issue_id)) $filter['issue_id']=$issue_id;
		$filter['start_date'] = trim($this->input->post('start_date'));
		$filter['end_date'] = trim($this->input->post('end_date'));	
		$filter['closed'] = ($closed=$this->input->post('closed'))===FALSE?-1:intval($closed);	
		$filter['admin_name'] = trim($this->input->post('admin_name'));	
		$this->load->vars('status',$this->status);
		$filter = get_pager_param($filter);
		$data = $this->support_model->log_list($filter);
		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('support/log_list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
		$this->load->view('support/log_list', $data);
	}

	public function switch_online()
	{
		auth('support');
		$this->load->model('admin_model');
		if($this->session->userdata('is_online')){
			//下线，并置手头的issue为已关闭
			$support = $this->support_model->filter(array('status'=>1,'admin_id'=>$this->admin_id));
			if($support) sys_msg('请先关闭处理中的会话',1);
			$this->admin_model->update(array('is_online'=>0),$this->admin_id);
			$this->session->set_userdata('is_online',0);
			print json_encode(array('err'=>0,'msg'=>'','status'=>0));
		}else{
			//上线
			$this->admin_model->update(array('is_online'=>1),$this->admin_id);
			$this->session->set_userdata('is_online',1);
			print json_encode(array('err'=>0,'msg'=>'','status'=>1));
		}
	}
	
	public function preview($issue_id=0){
		auth('support');
		$history=array_reverse($this->support_model->recent_message($issue_id));
		$this->load->view('support/preview',array('history'=>$history));
	}
	
	public function post_log(){
		auth('support');
		$issue_id=intval($this->input->post('issue_id'));
		$content=trim($this->input->post('content',TRUE));
		$issue=$this->support_model->filter(array('rec_id'=>$issue_id));
		if(!$issue) sys_msg('会话不存在',1);
		$this->support_model->insert_log(array(
			'rec_id'=>$issue_id,
			'content'=>$content,
			'create_admin'=>$this->admin_id,
			'create_date'=>$this->time
		));
		$log_list=$this->support_model->recent_log($issue_id);
		$html=$this->load->view('support/log',array('log_list'=>$log_list),TRUE);
		print json_encode(array('err'=>0,'msg'=>'','html'=>$html));
	}
	
	public function close_log(){
		auth('support');
		$log_id=intval($this->input->post('log_id'));
		$log=$this->support_model->filter_log(array('log_id'=>$log_id));
		if(!$log) sys_msg('记录不存在',1);
		if($log->create_admin!=$this->admin_id) sys_msg('不是您的备注，不能关闭',1);
		if($log->closed) sys_msg('备注已关闭，不能重复操作',1);
		$this->support_model->update_log(array('closed'=>1,'close_date'=>$this->time),$log_id);
		
		$log_list=$this->support_model->recent_log($log->rec_id);
		$html=$this->load->view('support/log',array('log_list'=>$log_list),TRUE);
		print json_encode(array('err'=>0,'msg'=>'','html'=>$html,'issue_id'=>$log->rec_id,'close_date'=>$this->time));
	}

}