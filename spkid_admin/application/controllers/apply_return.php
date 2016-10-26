<?php
#doc
#	classname:	Exchange
#	scope:		PUBLIC
#
#/doc

class Apply_return extends CI_Controller
{
	public function __construct ()
	{
		parent::__construct();
                $this->load->model('apply_return_model');
	}

	public function index ()
	{
		$filter = $this->uri->uri_to_assoc(3);
		$all_post = $this->input->post();
		$filter['order_sn'] = isset($all_post['order_sn'])?trim($all_post['order_sn']):'';
                $filter['user_name'] = isset($all_post['user_name'])?trim($all_post['user_name']):'';
                $filter['apply_id'] = isset($all_post['apply_id'])?trim($all_post['apply_id']):'';
                $filter['start_time'] = isset($all_post['start_time'])?trim($all_post['start_time']):'';
                $filter['end_time'] = isset($all_post['end_time'])?trim($all_post['end_time']):'';
                $filter['order_type'] = $all_post['order_type'];
                $filter['provider_status'] = $all_post['provider_status'];
                $filter['invoice_no'] = isset($all_post['invoice_no'])?trim($all_post['invoice_no']):'';
                $filter['apply_status'] = $all_post['apply_status'];

		$filter = get_pager_param($filter);

		$data = $this->apply_return_model->apply_return_list($filter);
                
                if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('apply_return/list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;
                
		$this->load->view('apply_return/list', $data);
	}
        
        public function info($apply_id = 0) {
            auth('apply_return_view');
            //申请退货理由 0:尺寸偏大 1:尺寸偏小 2:款式不喜欢 3:配送错误 4:其他
            $apply_reason_list = array(
                    '0'=>'尺寸偏大',
                    '1'=>'尺寸偏小',
                    '2'=>'款式不喜欢',
                    '3'=>'配送错误',
                    '4'=>'其他问题',
                    '5'=>'商品质量问题'
            );

            //供应商审核状态
            $apply_provider_status = array(
                    '0'=>'未审核',
                    '1'=>'正常审核',
                    '2'=>'异常审核',
                    '3'=>'正常审核'
            );
            $data = array();
            //取得申请退货单
            $apply_info = $this->apply_return_model->apply_info($apply_id);
            if (empty($apply_info))
            {
                    sys_msg('申请退货单不存在！',1);
            }
            //计算已退货数量
            $return_goods_num = $this->apply_return_model->get_return_goods_num($apply_info['order_id']);
            //取得申请退货单商品
            $apply_product = $this->apply_return_model->apply_return_goods($apply_id,$apply_info['order_id']);
            foreach($apply_product as $key=>$v){
                //可退数量
                $k = $v['product_id'].' '.$v['color_id'].' '.$v['size_id'];
                if(isset($return_goods_num[$k])) {
                        $v['n_product_num'] = (int)$v['o_product_number'] - (int)$return_goods_num[$k];
                } else {
                        $v['n_product_num'] = $v['o_product_number'];
                }

                $v['reason'] = $apply_reason_list[$v['return_reason']];
                if($apply_info['order_type'] == 1&&$apply_info['provider_status']==0)
                {
                        $v['apply_provider_status'] = $apply_provider_status[$apply_info['provider_status']];
                }
                if($apply_info['order_type'] == 1&&$apply_info['provider_status']>0)
                {
                        $v['apply_provider_status'] = $apply_provider_status[$apply_info['suggest_type']];
                }
                if(!empty($v['img'])) {
                        $img_arr = explode(";",$v['img']);
                        $v['img_list'] = $img_arr;
                }
                $apply_product[$key] = $v;
            }
            //取得申请退货单意见列表
            $apply_suggest = $this->apply_return_model->apply_return_suggest($apply_id);
            
            $data['apply_info'] = $apply_info;
            $data['apply_product'] = $apply_product;
            $data['apply_suggest'] = $apply_suggest;
            
            $this->load->view('apply_return/info', $data);
        }
        
        public function suggest($apply_id = 0) {
            auth('apply_return_suggest');
            $all_post = $this->input->post();
            $suggest_type = intval($all_post['suggest_type']);
            $suggest_content = $all_post['suggest_content'];
            if(empty($suggest_content)) sys_msg('意见内容不能为空！',1);
            $suggest_data = array(
                'apply_id'=>$apply_id,
                'suggest_type'=>$suggest_type,
                'suggest_content'=>$suggest_content,
                'create_id'=>$this->session->userdata('admin_id'),
                'create_date'=>date('Y-m-d H:i:s')
            );
            //添加意见
            $res = $this->apply_return_model->add_apply_suggest($suggest_data);
            if($res) {
                $links[0]['text'] = "申请退货单详情";
                $links[0]['href'] = "apply_return/info/$apply_id";
                sys_msg("操作成功！",0,$links);
            } else {
                sys_msg('操作失败！',1);
            }
        }
        
        public function remove() {
            auth('apply_return_remove');
            $all_post = $this->input->post();
            $apply_id = intval($all_post['apply_id']);
            $cancel_reason = trim($all_post['cancel_reason']);
            $res = $this->apply_return_model->cancel_order($cancel_reason,$apply_id, $this->session->userdata('admin_id'));
            echo 0;
        }
}
###
