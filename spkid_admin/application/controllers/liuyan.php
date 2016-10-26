<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class Liuyan extends CI_Controller
{
	public function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('index/login');
		}
        $this->time = date('Y-m-d H:i:s');
        // 系统设置->参数配置 20160123 by leoli
        global $liuyan_rel_types, $liuyan_types;
		$this->load->model('liuyan_model');
        $this->tag_type=$liuyan_rel_types;
        $this->comment_type=$liuyan_types;
	}

        public function index ()
	{
            auth(array('liuyan_view','liuyan_edit'));
            $filter = $this->uri->uri_to_assoc(3);
            $filter['comment_type']=intval($this->input->get('comment_type'));
            $filter['tag_type']=intval($this->input->get('tag_type'));
            $filter['tag_id']=intval($this->input->get('tag_id'));
            $comment_type=intval($this->input->post('comment_type'));
            $tag_type=intval($this->input->post('tag_type'));
            $tag_id=intval($this->input->post('tag_id'));
            if($comment_type) $filter['comment_type']=$comment_type;
            if($tag_type) $filter['tag_type']=$tag_type;
            if($tag_id) $filter['tag_id']=$tag_id;
            $filter['is_audit']=($v=$this->input->post("is_audit"))===FALSE?-1:intval($v);
            $filter['is_del']=($v=$this->input->post("is_del"))===FALSE?-1:intval($v);
            $filter['is_reply']=($v=$this->input->post("is_reply"))===FALSE?-1:intval($v);

            $start_time = $this->input->post("start_time");
            if(!empty($start_time)) $filter['start_time'] = $start_time;
            $end_time = $this->input->post("end_time");
            if(!empty($end_time)) $filter['end_time'] = $end_time;
            $filter = get_pager_param($filter);
            $data = $this->liuyan_model->liuyan_list($filter);
            $this->load->vars(array(
                'perm_edit'=>check_perm('liuyan_edit'),
                'perm_aurep'=>check_perm('liuyan_aurep')
            ));

            if ($this->input->post('is_ajax'))
            {
                    $data['full_page'] = FALSE;
                    $data['content'] = $this->load->view('liuyan/liuyan_list', $data, TRUE);
                    $data['error'] = 0;
                    unset($data['list']);
                    echo json_encode($data);
                    return;
            }
            $data['full_page'] = TRUE;
            $this->load->view('liuyan/liuyan_list', $data);
        }

        public function del($comment_id)
        {
            auth(array('liuyan_edit'));
            $comment_id = intval($comment_id);
            $test = $this->input->post('test');
            $check = $this->liuyan_model->filter(array('comment_id' => $comment_id));
            if(empty ($check)){
                sys_msg('记录不存在',1);
                return;
            }
            if($check->is_del == 1){
                sys_msg('记录已经被删除',1);
                return;
            }
            if($test) sys_msg('');
            $this->liuyan_model->update(array('is_del' => '1') , $comment_id);
            sys_msg('逻辑删除成功',2,array(array('href'=>'/liuyan/index','text'=>'返回列表页')));
        }

        public function replay($comment_id)
        {
            auth(array('liuyan_view','liuyan_edit'));
            $this->load->helper('perms_helper');
            $this->load->vars('perms' , get_liuyan_perm());
            $comment_id = intval($comment_id);
            $check = $this->liuyan_model->filter(array('comment_id' => $comment_id));
            if(empty ($check)){
                sys_msg('记录不存在',1);
                return;
            }
           
            $this->load->library('ckeditor');
            $liuyan = $this->liuyan_model->pro_liuyan($comment_id);

            $this->load->vars('arr',$liuyan);
            $this->load->vars('comment_id',$comment_id);
            $this->load->view('liuyan/liuyan_replay');
        }

        public function proc_replay($comment_id)
        {
            auth(array('liuyan_edit'));
            $comment_id = intval($comment_id);
            $check = $this->liuyan_model->filter(array('comment_id' => $comment_id));
            if(empty ($check)){
                sys_msg('记录不存在',1);
                return;
            }
            $data['comment_id'] = $comment_id;
            $data['reply_date'] = date('Y-m-d H:i:s' ,time());
            $data['reply_admin_id'] = $this->session->userdata('admin_id');
            $data['reply_content'] = $this->input->post('reply_content');
            $this->liuyan_model->update($data,$data['comment_id']);
            sys_msg('回复成功',2,array(array('href'=>'/liuyan/index','text'=>'返回列表页')));
        }

        public function audit(){
            auth(array('liuyan_aurep'));
            $this->load->model('user_model');
            $this->load->model('user_account_log_model');
            
            $comment_id = $this->input->post('comment_id');
            $this->db->trans_begin();
            $row = $this->liuyan_model->lock($comment_id);
            if(empty ($row)){
                sys_msg('记录不存在',1);
            }
            if($row->is_audit == 1){
                sys_msg('记录已被审核',1);
            }            
            //对于商品评价送积分
            if($row->tag_type==1&&$row->comment_type==2&&$row->user_id>0){                
                $check=$this->liuyan_model->filter(array(
                    'comment_type'=>$row->comment_type,
                    'tag_type'=>$row->tag_type,
                    'tag_id'=>$row->tag_id,
                    'user_id >' => 0,
                    'is_audit' => 0,
                    'is_del' => 0,
                    'comment_id <' => $row->comment_id
                ));
                if($check) {
                    sys_msg('用户评价请按发表顺序审核',1);
                }
                
                $user=$this->user_model->lock_user($row->user_id);
                if(!$user) sys_msg('用户数据丢失',1);
                $rank=$this->user_model->filter_rank(array('rank_id'=>$user->rank_id));
                if($rank && $rank->comment_point){
                    $point=$rank->comment_point;
                } 
                /*if($this->liuyan_model->check_first_point($row)){
                    $point*=2;
                }*/
                $this->user_account_log_model->insert(array(
                    'link_id' => $row->comment_id,
                    'user_id' => $row->user_id,
                    'pay_points' => $point,
                    'change_desc' => '发表评论送积分',
                    'change_code' => 'point_comment',
                    'create_admin' => $this->admin_id,
                    'create_date' => $this->time
                ));
                $this->user_model->update(array('pay_points'=>$user->pay_points+$point),$user->user_id);                
            }
            $this->liuyan_model->update(array('is_audit'=>1,'audit_admin_id'=>$this->session->userdata('admin_id')),$comment_id);
            $this->db->trans_commit();
            echo json_encode(array('err'=>0,'msg'=>'审核成功'));
        }

        public function add(){
            auth(array('liuyan_edit'));
            $this->load->model('size_model');
            $tag_id=intval($this->input->get('tag_id'));
            $tag_type=intval($this->input->get('tag_type'))==2?2:1;
            
            
            $this->load->vars(array(
                'size_arr' => $this->size_model->all_size(),
                'tag_id' => $tag_id,
                'tag_type' => $tag_type
            ));
            
            $this->load->view('liuyan/add');
        }

        public function proc_add(){
            auth(array('liuyan_edit'));
            $product_id = $this->uri->segment(3,0);
            $data['tag_type'] = $this->input->post('tag_type');
            $data['tag_id'] = $this->input->post('tag_id');
            $data['comment_type'] = $this->input->post('comment_type');
            $data['user_name'] = $this->input->post('user_name');
            $data['comment_content'] = $this->input->post('comment_content');
            $data['grade'] = $this->input->post('grade');
            $data['user_id'] = 0;
            $data['height'] = $this->input->post('height');
            $data['weight'] = $this->input->post('weight');
            $data['size_id'] = $this->input->post('size_id');
            $data['suitable'] = $this->input->post('suitable');
            $data['comment_date'] = $this->input->post('comment_date'); 
            $data['create_admin'] = $this->admin_id;
            $this->load->library('form_validation');
            $this->form_validation->set_rules('tag_type', '分类名称', 'required');
            $this->form_validation->set_rules('tag_id', '商品id', 'trim|required');
            $this->form_validation->set_rules('comment_type', '评论类型', 'required');
            $this->form_validation->set_rules('comment_content', '评论内容', 'required');
            if (!$this->form_validation->run()) {
                    sys_msg(validation_errors(), 1);
            }
            switch ($data['tag_type']) {
                case 2:
                    $this->load->model('package_model');
                    $pkg=$this->package_model->filter(array('package_id'=>$data['tag_id']));
                    if(!$pkg) sys_msg('没有查询到相关商品或礼包信息',1);
                    break;
                case 1:
                    $this->load->model('product_model');
                    $p=$this->product_model->filter(array('product_id'=>$data['tag_id']));
                    if(!$p) sys_msg('没有查询到相关商品或礼包信息',1);
                    break;
                
                default:
                    sys_msg('关联类型错误',1);
                    break;
            }
            $this->liuyan_model->insert($data);
            sys_msg('操作成功',2,array(array('href'=>"/liuyan/index.html?tag_id={$data['tag_id']}&tag_type={$data['tag_type']}" ,'text'=>'返回列表页')));
        }

        public function load_product()
        {
            $tag_id=intval($this->input->post('tag_id'));
            $tag_type=intval($this->input->post('tag_type'))==2?2:1;
            if(!$tag_id) sys_msg('没有查询到相关商品或礼包信息',1);
            switch ($tag_type) {
                case 2:
                    $this->load->model('package_model');
                    $pkg=$this->package_model->filter(array('package_id'=>$tag_id));
                    if(!$pkg) sys_msg('没有查询到相关商品或礼包信息',1);
                    print json_encode(array('err'=>0,'msg'=>'','html'=>$pkg->package_name));
                    break;
                
                case 1:
                    $this->load->model('product_model');
                    $p=$this->product_model->filter(array('product_id'=>$tag_id));
                    if(!$p) sys_msg('没有查询到相关商品或礼包信息',1);
                    print json_encode(array('err'=>0,'msg'=>'','html'=>"[{$p->product_sn}] {$p->product_name}"));
                    break;
                
                default:
                    sys_msg('没有查询到相关商品或礼包信息',1);
                    break;
            }
        }

}
