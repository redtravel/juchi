<?php
#doc
#	classname:	Exchange
#	scope:		PUBLIC
#
#/doc

class Order_change extends CI_Controller
{
	public function __construct ()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if ( ! $this->admin_id )
		{
			redirect('index/login');
		}
		$this->time=date('Y-m-d H:i:s');
		$this->load->model('depot_model');
		$this->load->model('change_model');
		$this->load->model('return_model');
	}

	public function index ()
	{
                $this->load->helper('change');
		//auth(array('order_change_view','order_change_edit','exchangeout_audit','exchangein_audit'));
		$filter = $this->uri->uri_to_assoc(3);
		$posts = $this->input->post();
		$filter['change_sn'] = isset($posts['change_sn'])?trim($this->input->post('change_sn')):'';
		$filter['order_sn'] = isset($posts['order_sn'])?trim($this->input->post('order_sn')):(isset($filter['order_sn'])?$filter['order_sn']:'');
		$filter['consignee'] = isset($posts['consignee'])?trim($this->input->post('consignee')):'';
		$filter['provider_goods'] = isset($posts['provider_goods'])?trim($this->input->post('provider_goods')):'';
		$filter['composite_status'] = isset($posts['composite_status'])?intval($this->input->post('composite_status')):'';
		$filter['invoice_status'] = isset($posts['invoice_status'])?intval($this->input->post('invoice_status')):'';
		//$filter['start_time'] = trim($this->input->post('start_time'));
		//$filter['end_time'] = trim($this->input->post('end_time'));
                $filter['odd'] = intval($this->input->post('odd'));
                $filter['pick'] = intval($this->input->post('pick'));

		$filter = get_pager_param($filter);
		$data = $this->change_model->change_list($filter);

		if ($this->input->is_ajax_request())
		{
			$data['full_page'] = FALSE;
			$data['content'] = $this->load->view('order_change/list', $data, TRUE);
			$data['error'] = 0;
			unset($data['list']);
			echo json_encode($data);
			return;
		}
		$data['full_page'] = TRUE;

		$status_list = array('-1'=>'请选择','0'=>'待确认','104'=>'待入库','101'=>'待发货','102'=>'已完成','106'=>'永久缺货','3'=>'取消','4'=>'作废');
		$data['status_list'] = $status_list;

		$data['my_id'] = $this->admin_id;
		$this->load->view('order_change/list', $data);
	}

	public function add ($order_id = 0)
	{
		auth('order_change_edit');
		$data = array();
		if (!empty($order_id))
		{
			$order_info = $this->return_model->filter_order(array('order_id'=>$order_id));
			$data['order_info'] = $order_info;

		}
		$this->load->helper('form');
		$this->load->view('order_change/add', $data);
	}

	public function get_order_data ()
	{
		$order_sn = trim($this->input->post('order_sn'));
		$order = $this->return_model->get_order_info(0,$order_sn);

		if ( empty($order) )
		{
			echo json_encode(array('error'=>1,'msg'=>'订单不存在'));
			return;
		}
		$order_id = $order['order_id'];
		$order_product = $this->return_model->order_product_can_return($order['order_id']);
		if (empty($order_product))
		{
			echo json_encode(array('error'=>1,'msg'=>'没有可退商品'));
			return;
		}
		$data=array();
	    $change=array();
	    $change['consignee'] = $order['consignee'];
	    $change['order_id'] = $order_id;
	    $change['user_name'] = $order['user_name'];

	    $product_list = $this->return_model->order_product_list($order_id);
	    /*
	    //扣除购物车中的锁定库存
	    $cart_product_number = cart_product_number($order_id);
	    foreach($product_list as $key=>$product) {
	        foreach($product as $k=>$gl) {
	            $tmp_key = $gl['product_id'].'-'.$gl['color_id'].'-'.$gl['size_id'];
	            $cart = $cart_product_number[$tmp_key];
	            if(empty($cart)||$gl['consign_num']==-2) continue;
	            $labor_consign = min($cart['product_num'],max($gl['consign_num'],0));//待销数量与购物车数量取最小值，如果负值取0
	            $labor_real = $cart['product_num']-$labor_consign;//剩下的从实有库存扣
	            $gl['consign_num'] -= $labor_consign;
	            $gl['gl_num'] -= $labor_real;
	            $gl['real_num'] = max($gl['gl_num'] - $gl['wait_num'],0);
	            $product[$k] = $gl;
	        }
	        $product_list[$key] = $product;
	    }
	    //扣除购物车中的锁定库存结束
	    */
	    //仓库select
	    $depot_rs = $this->depot_model->filter_depot_all(array('is_use'=>1,'depot_type'=>1));
	    $depot_arr = array();
	    if (!empty($depot_rs))
	    {
	    	foreach ($depot_rs as $row)
	    	{
	    		$depot_arr[$row['depot_id'].'___'.$row['depot_name']] = $row['depot_name'];
	    	}
	    }

	    //获取商品
	    $change_product = array();
	    foreach ($order_product as $product) {
	    	if (isset($product['discount_type']) && $product['discount_type'] == 4) continue;
	        $product['color_size_name'] = $product['color_name']."--".$product['size_name'];
	        $product['max_num'] = $product['product_num'] ;
	        $product['max_consign_num'] = $product['consign_num'] ;
	        $product['formated_product_price']    = number_format($product['product_price'], 2, '.', '');
	        $product['product_list'] = $product_list[$product['product_id']];
	        reset($product['product_list']);
	        $gtmp = array();
	        foreach($product['product_list'] as $gitem) {
	            if(($gitem['gl_num'] - $gitem['wait_num'] <=0 && $gitem['gl_consign_num']<=0 && $gitem['gl_consign_num']!=-2) || $product['max_num'] < 1) continue;
	            $gtmp[$product['rec_id'].'_'.$product['product_id'].'_'.$product['color_id'].'_'.$product['size_id'].'_'.$product['track_id'].'_'.$gitem['color_id'].'_'.$gitem['size_id']]
	                    =  $gitem['color_name'].'--'.$gitem['size_name'].' ['.max($gitem['gl_num'] - $gitem['wait_num'],0).'+'.(($gitem['gl_consign_num']>=0 || $gitem['gl_consign_num']==-1)?strval(max($gitem['gl_consign_num'],0)):'+').']' ;
	        }
	        $product['selarr'] = $gtmp;
	        $product['depotarr'] = $depot_arr;
	        $change_product[] = $product;
	    }
	    $change['change_product'] = $change_product;
	    $change['consignee'] = $order['consignee'];
	    $change['email'] = $order['email'];
	    $change['address'] = $order['address'];
	    $change['zipcode'] = $order['zipcode'];
	    $change['tel'] = $order['tel'];
	    $change['mobile'] = $order['mobile'];
	    $change['country'] = $order['country'];
	    $change['province'] = $order['province'];
	    $change['city'] = $order['city'];
	    $change['district'] = $order['district'];
	    $change['country']= max($change['country'],1);//默认选中国
	    if ($change['country'] > 0)
	    {
			$data['province_list'] = $this->return_model->get_regions(1, $change['country']); /* 取得省份 */
	        if ($change['province'] > 0)
	        {
	        	$data['city_list'] = $this->return_model->get_regions(2, $change['province']); /* 取得城市 */
	            if ($change['city'] > 0)
	            {
	            	$data['district_list'] = $this->return_model->get_regions(3, $change['city']); /* 取得区域 */
	            }
	        }
	    }
	    $change['product_amount'] = 0;
	    $change['formated_product_amount'] = number_format($change['product_amount'], 2, '.', '');
	    $data['change'] = $change;
	    $data['change_product'] = $change_product;
		$data['content'] = $this->load->view('order_change/add_lib', $data, TRUE);
		$data['error'] = 0;
		unset($data['change_product']);
		unset($data['change']);
		echo json_encode($data);
		return;
	}

	public function post_add ()
	{
		auth('order_change_edit');

		$product = array();
	    $source_product = array();
	    $aid_product = array();
	    $rec_arr = array();
	    $product_ids = $sub_ids = array();
	    $all_post = $this->input->post();

	    foreach ($all_post as $key=>$item)
	    {
	        if(strlen($key) > 16 && substr($key,0,4) == "num_" && intval($item) > 0)
	        {
	            $keyarr = explode("_",$key);
	            $product[] = array('op_id'=>$keyarr[1], 'product_id'=>$keyarr[2], 'src_color_id'=>$keyarr[3], 'src_size_id'=>$keyarr[4],
	                    'parent_cp_id'=>$keyarr[5], 'color_id'=>$keyarr[6], 'size_id'=>$keyarr[7],
	                    'change_num'=>intval($item));

	            $rec_arr[] = $keyarr[1];
	            $product_ids[] = $keyarr[2];
	            if (isset($source_product[$keyarr[1]."-".$keyarr[2]."-".$keyarr[3]."-".$keyarr[4]."-".$keyarr[5]]))
	                $source_product[$keyarr[1]."-".$keyarr[2]."-".$keyarr[3]."-".$keyarr[4]."-".$keyarr[5]] += intval($item);
	            else
	                $source_product[$keyarr[1]."-".$keyarr[2]."-".$keyarr[3]."-".$keyarr[4]."-".$keyarr[5]] = intval($item);

	            if (isset($aid_product[$keyarr[2]."-".$keyarr[6]."-".$keyarr[7]]))
	                $aid_product[$keyarr[2]."-".$keyarr[6]."-".$keyarr[7]] += intval($item);
	            else
	                $aid_product[$keyarr[2]."-".$keyarr[6]."-".$keyarr[7]] = intval($item);
	        }
	    }

	    $this->db->query('BEGIN');
	    $order_id = intval($all_post['order_id']);
	    $this->db->query("SELECT * FROM ".$this->db->dbprefix('order_info')." WHERE order_id= '".$order_id."' FOR UPDATE");
		$order = $this->return_model->get_order_info($order_id);/* 验证订单的有效性 */

	    if (empty($order)) sys_msg('订单不存在',1);
	    $arr = $this->return_model->order_product_can_return($order_id);

	    if (empty($arr)) sys_msg('订单无可退换货商品。',1);

	    $order_product = array();
	    foreach ($arr as $k=>$v)
	    {
	    	$order_product[$v['rec_id'].'-'.$v['product_id'].'-'.$v['color_id'].'-'.$v['size_id'].'-'.$v['track_id']] = $v;
	    }

	    $query = $this->db->query("SELECT sub_id FROM ".$this->db->dbprefix('product_sub')." WHERE product_id ".db_create_in($product_ids));
		$rs = $query->result_array();
		if (!empty($rs))
		{
			foreach ($rs as $row)
			{
				$sub_ids[] = $row['sub_id'];
			}
		}
	    $this->db->query("SELECT sub_id FROM " . $this->db->dbprefix('product_sub') . " WHERE sub_id ".db_create_in($sub_ids)." FOR UPDATE");//锁product sub表
	    foreach ($source_product as $key=>$value)
	    {
	        if(!isset($order_product[$key]) || $order_product[$key]['product_num'] < $value) {
	            $this->db->query('ROLLBACK');
	            sys_msg('出错！换货数量超过可换数量。', 1);
	            break;
	        }
	    }
	    $product_gl_num = $this->change_model->order_product_num($order_id);//库存信息
	    //$cart_product_number = cart_product_number($order_id);//购物车锁库存
	    foreach($aid_product as $key=>$value) {
	        //$cart_number = empty($cart_product_number[$key])?0:$cart_product_number[$key]['product_num'];
	        $cart_number = 0;
	        if ($product_gl_num[$key]['real_num']+max($product_gl_num[$key]['gl_consign_num'],0) < $value+$cart_number && $product_gl_num[$key]['gl_consign_num']!=-2)
	        {
	            $this->db->query('ROLLBACK');
	            sys_msg('出错！换货数量超过可换数量。', 1);
	            break;
	        }
	    }

	    /* 生成换货单 */
	    do {
	        $change['change_sn'] = $this->change_model->get_change_sn();
	        $change['order_id'] = $order_id;
	        $change_id = $this->change_model->insert($change);
	        if ($change_id > 0)
	        {
	            break;
	        }
	        else {
	            if ($this->db->errno() != 1062) {
	                die($db->error());
	            }
	        }
	    }
	    while (true); // 防止订单号重复

	    $change['change_id']        = $change_id;
	    $change['consignee'] 	= $all_post['consignee'];
	    $change['email'] 		= $all_post['email'];
	    $change['country'] 		= 1;//中国
	    $change['province'] 	= $all_post['province'];
	    $change['city']             = $all_post['city'];
	    $change['district']         = $all_post['district'];
	    $change['address'] 		= $all_post['address'];
	    $change['zipcode'] 		= $all_post['zipcode'];
	    $change['tel'] 		= $all_post['tel'];
	    $change['mobile'] 		= $all_post['mobile'];
	    $change['user_id']		= $order['user_id'];
	    $change['create_date'] 	= date('Y-m-d H:i:s');
	    $change['create_admin']          = $this->admin_id;
	    $change['change_reason']    = trim($all_post['change_reason']);
	    $change['lock_admin'] = $this->admin_id;
        $change['lock_date'] = date('Y-m-d H:i:s');
	    $this->change_model->update($change,$change_id);

	    /* 更新商品明细 */
	    foreach ($product as $key=>$value) {
	        //计算实际库存和代销库存
	        $tmp_key = $value['product_id'].'-'.$value['color_id'].'-'.$value['size_id'];
	        $real_number = min($product_gl_num[$tmp_key]['real_num'],$value['change_num']);
	        $consign_number = $value['change_num'] - $real_number;
	        $product_gl_num[$tmp_key]['real_num'] -= $real_number;

	        $tmp_key = $value['op_id'].'-'.$value['product_id'].'-'.$value['src_color_id'].'-'.$value['src_size_id'].'-'.$value['parent_cp_id'];
	        $src_consign_number = min($order_product[$tmp_key]['consign_num'],$value['change_num']);
	        $order_product[$tmp_key]['consign_num'] -= $src_consign_number;

	        $change_rec['change_id'] 	= $change_id;
	        $change_rec['op_id'] 	= $value['op_id'];
	        $change_rec['product_id'] 	= $value['product_id'];
	        $change_rec['color_id'] 	= $value['color_id'];
	        $change_rec['size_id']          = $value['size_id'];
	        $change_rec['src_color_id'] 	= $value['src_color_id'];
	        $change_rec['src_size_id'] 	= $value['src_size_id'];
	        $change_rec['parent_cp_id'] 	= $value['parent_cp_id'];
	        $change_rec['change_num']           = $value['change_num'];
	        $change_rec['consign_num'] 	= $consign_number;
	        $change_rec['src_consign_num'] 	= $src_consign_number;
	        $change_rec['change_date']          = date('Y-m-d H:i:s');
	        $change_rec['change_admin']           = $this->admin_id;
	        $change_rec['package_id'] 	= $order_product[$tmp_key]['package_id'];
	        $change_rec['extension_id'] 	= $order_product[$tmp_key]['extension_id'];
//	        $change_rec['cost_price'] 	= $order_product[$tmp_key]['cost_price'];
//	        $change_rec['consign_price'] 	= $order_product[$tmp_key]['consign_price'];
//	        $change_rec['cosnign_rate'] 	= $order_product[$tmp_key]['consign_rate'];
	        $insert_change_product_id = $this->change_model->insert_product($change_rec);

	        //更新扣库存
	        $this->db->query("UPDATE " . $this->db->dbprefix('product_sub') . "
	                SET gl_num = gl_num - '$real_number' ,
	                consign_num = consign_num - IF(consign_num>=0,$consign_number,0),
	                wait_num = wait_num + $consign_number
	                WHERE product_id = '".$value['product_id']."' AND color_id = '".$value['color_id']."' AND size_id = '".$value['size_id']."'");

	        //换货入
	        if($value['change_num']>$src_consign_number) {
	            $transaction = array();
	            $transaction['trans_status']    = TRANS_STAT_AWAIT_IN;
	            $transaction['trans_type']      = TRANS_TYPE_CHANGE_ORDER;
	            $transaction['trans_sn']        = $change['change_sn'];
	            $transaction['sub_id']          = $insert_change_product_id;
	            $transaction['product_id']        = $value['product_id'];
	            $transaction['color_id']        = $value['src_color_id'];
	            $transaction['size_id']         = $value['src_size_id'];
	            $transaction['product_number']    = $value['change_num']-$src_consign_number;
	            $transaction['depot_id']        = 0; //先不设定仓库和储位
	            $transaction['trans_direction'] = 1;
	            $transaction['location_id']       = 0;
	            $this->return_model->insert_transaction($transaction);
	        }
	    }
	    //换货出
	    $info = $this->change_model->create_change_trans($change,$order);
	    if($info['error']!=0) sys_msg($info['message'],1);

	    $this->db->query('COMMIT');
	    $links[] = array('text' => '返回继续操作该换货申请。', 'href' => '/order_change/edit/' . $change_id );
	    $links[] = array('text' => '返回换货单列表', 'href' => '/order_change' );
	    sys_msg('添加换货申请成功。', 0, $links);
	}

	public function post_save ()
	{
		auth('order_change_edit');
		$all_post = $this->input->post();
        $order_id = isset($all_post['order_id']) ? intval($all_post['order_id']) : 0;
        $change_id = isset($all_post['change_id']) ? intval($all_post['change_id']) : 0;
        $shipping_id = isset($all_post['shipping_id']) ? intval($all_post['shipping_id']) : '';  //非0空值

        $order = $this->return_model->get_order_info($order_id);
        if (empty($order))
        {
        	sys_msg('订单不存在!',1);
        }
        $change = $this->change_model->change_info($change_id);
        if (empty($change))
        {
        	sys_msg('换货单不存在!',1);
        }
        $operable_list = $this->change_model->get_change_perm($change);
        if (!$operable_list['save'])
        {
            sys_msg('无操作权限!',1);
        }
        if (empty($shipping_id)) sys_msg('请选择配送方式。',1);

        $this->db->query('BEGIN');
        $this->db->query('SELECT * FROM '.$this->db->dbprefix('order_change_info')." WHERE change_id = '".$change_id."' FOR UPDATE");
        $this->db->query('SELECT * FROM '.$this->db->dbprefix('order_info')." WHERE order_id = '".$order_id."' FOR UPDATE");

        // ：锁库存
	    $order_product = $this->return_model->order_product($order_id);
	    $gl_ids = array();
	    foreach ($order_product as $val)  $gl_ids[] = $val['product_sub_id'];
	    if(!empty($gl_ids)) $this->db->query("select * from ".$this->db->dbprefix('product_sub')." where sub_id ".db_create_in($gl_ids)." for update ");

	    $change['shipping_id'] 	= $shipping_id;
	    $change_main['shipping_id'] 	= $shipping_id;

	    /* 取得换货人信息 */
	    if ($all_post['priv_edit_consignee']&&$operable_list['edit_consignee']) {
	        $change_main['consignee'] 	= $all_post['consignee'];
	        $change_main['email'] 	= $all_post['email'];
	        $change_main['country'] 	= 1;
	        $change_main['province'] 	= intval($all_post['province']);
	        $change_main['city']         = intval($all_post['city']);
	        $change_main['district']     = intval($all_post['district']);
	        $change_main['address'] 	= $all_post['address'];
	        $change_main['zipcode'] 	= $all_post['zipcode'];
	        $change_main['tel'] 		= $all_post['tel'];
	        $change_main['mobile'] 	= $all_post['mobile'];
	        $change_main['change_reason']= $all_post['change_reason'];
	    }

	    /* 更新商品明细 */
	    if ($all_post['priv_edit_product']&&$operable_list['edit_product']) {
	        $product = array();
	        $source_product = array();
	        $aid_product = array();
	        $new_arr = array();
	        $rec_arr = array();
	        foreach ($all_post as $key=>$item)
	        {
	            $item = intval($item);
	            if (strlen($key) > 16 && substr($key,0,4) == "num_" && intval($item) > 0)
	            {
	                $keyarr = explode("_",$key);
	                $product[] = array('op_id'=>$keyarr[1], 'product_id'=>$keyarr[2], 'src_color_id'=>$keyarr[3], 'src_size_id'=>$keyarr[4],
	                        'parent_cp_id'=>$keyarr[5], 'color_id'=>$keyarr[6], 'size_id'=>$keyarr[7],
	                        'change_num'=>$item);
	                $rec_arr[] = $keyarr[1];
	                $k0 = $keyarr[1]."-".$keyarr[2]."-".$keyarr[3]."-".$keyarr[4]."-".$keyarr[5]."-".$keyarr[6]."-".$keyarr[7];
	                $k1 = $keyarr[1]."-".$keyarr[2]."-".$keyarr[3]."-".$keyarr[4]."-".$keyarr[5]; //退
	                $k2 = $keyarr[2]."-".$keyarr[6]."-".$keyarr[7];//换
	                $new_arr[$k0] = intval($item);
	                if(isset($source_product[$k1]))
	                    $source_product[$k1] += $item;
	                else
	                    $source_product[$k1] = intval($item);

	                if(isset($aid_product[$k2]))
	                    $aid_product[$k2] += $item;
	                else
	                    $aid_product[$k2] = $item;
	            }
	        }

	        $old_arr = $this->change_model->filter_product_all(array('change_id'=>$change_id));
	        if (!empty($old_arr))
	        {
	        	foreach ($old_arr as $row)
	        	{
	        		$old_arr[$row['op_id'].'-'.$row['product_id'].'-'.$row['src_color_id'].'-'.$row['src_size_id'].'-'.$row['parent_cp_id'].'-'.$row['color_id'].'-'.$row['size_id']] = $row['change_num'];
	        	}
	        }

	        $check_change_product_same = $this->change_model->check_change_product_same($old_arr,$new_arr);
	        if (!$check_change_product_same)
	        {
	            //删除原有记录时先更新商品库存
	            //更新扣库存
	            $this->db->query("UPDATE " . $this->db->dbprefix('product_sub') . " AS gl,
	                            (SELECT product_id,color_id,size_id,sum(change_num) as change_num,sum(consign_num) as consign_num, change_id
	                             FROM " . $this->db->dbprefix('order_change_product') . "
	                            WHERE change_id = '".$change_id."' GROUP BY product_id,color_id,size_id) AS c
	                            SET gl.gl_num = gl.gl_num + (c.change_num-c.consign_num),
	                            gl.consign_num = gl.consign_num + IF(gl.consign_num>=0,c.consign_num,0),
	                            gl.wait_num = gl.wait_num - c.consign_num
	                            WHERE gl.product_id = c.product_id AND gl.color_id = c.color_id AND gl.size_id = c.size_id  AND c.change_id = '".$change_id."'");

	            //删除原有的换货单商品记录
	            $this->change_model->delete_product(array('change_id'=>$change_id));

	            //无效transaction表
	            $this->return_model->update_transaction(array('trans_status'=>5,'cancel_date'=>date('Y-m-d H:i:s'),'cancel_admin'=>$this->admin_id),array('trans_sn'=>$change['change_sn'],'trans_type'=>5));

	            $arr = $this->return_model->order_product_can_return($order_id);
	            $order_product = array();
	            foreach ($arr as $v)
	            {
	                $order_product[$v['rec_id'].'-'.$v['product_id'].'-'.$v['color_id'].'-'.$v['size_id'].'-'.$v['track_id']] = $v;
	            }
	            foreach ($source_product as $key=>$value)
	            {
	                if (!isset($order_product[$key])||$order_product[$key]['product_num'] < $value)
	                {
	                    $this->db->query('ROLLBACK');
	                    sys_msg('出错！换货数量超过可换数量。', 1);
	                    break;
	                }
	            }

	            $product_gl_num = $this->change_model->order_product_num($order_id);
	            //$cart_product_number = cart_product_number($order_id);//购物车锁库存
	            foreach ($aid_product as $key=>$value)
	            {
	                //$cart_number = empty($cart_product_number[$key])?0:$cart_product_number[$key]['product_number'];
	                $cart_number = 0;
	                if (($product_gl_num[$key]['real_num']+max($product_gl_num[$key]['gl_consign_num'],0)< $value+$cart_number) && $product_gl_num[$key]['gl_consign_num']!=-2)
	                {
	                    $this->db->query('ROLLBACK');
	                    sys_msg('出错！换货数量超过可换数量。', 1);
	                    break;
	                }
	            }

	            /* 更新商品明细 */
	            foreach ($product as $key=>$value) {
	                //计算实际库存和代销库存

	                $tmp_key = $value['product_id'].'-'.$value['color_id'].'-'.$value['size_id'];
	                $real_number = min($product_gl_num[$tmp_key]['real_num'],$value['change_num']);
	                $consign_number = $value['change_num'] - $real_number;
	                $product_gl_num[$tmp_key]['real_num'] -= $real_number;

	                $tmp_key = $value['op_id'].'-'.$value['product_id'].'-'.$value['src_color_id'].'-'.$value['src_size_id'].'-'.$value['parent_cp_id'];
	                $src_consign_number = min($order_product[$tmp_key]['consign_num'],$value['change_num']);
	                $order_product[$tmp_key]['consign_num'] -= $src_consign_number;

	                $change_rec['change_id'] 	= $change_id;
	                $change_rec['op_id'] 	= $value['op_id'];
	                $change_rec['product_id'] 	= $value['product_id'];
	                $change_rec['color_id'] 	= $value['color_id'];
	                $change_rec['size_id']          = $value['size_id'];
	                $change_rec['src_color_id'] 	= $value['src_color_id'];
	                $change_rec['src_size_id'] 	= $value['src_size_id'];
	                $change_rec['parent_cp_id'] 	= $value['parent_cp_id'];
	                $change_rec['change_num']           = $value['change_num'];
	                $change_rec['src_consign_num'] 	= $src_consign_number;
	                $change_rec['consign_num']   = $consign_number;
	                $change_rec['change_date']          = date('Y-m-d H:i:s');
	                $change_rec['change_admin']           = $this->admin_id;
	                $change_rec['package_id'] 	= $order_product[$tmp_key]['package_id'];
	                $change_rec['extension_id'] 	= $order_product[$tmp_key]['extension_id'];
	                $change_rec['cost_price'] 	= $order_product[$tmp_key]['cost_price'];
	                $change_rec['consign_price'] 	= $order_product[$tmp_key]['consign_price'];
	                $change_rec['cosnign_rate'] 	= $order_product[$tmp_key]['consign_rate'];


					$insert_change_product_id = $this->change_model->insert_product($change_rec);

	                //更新扣库存
	                $this->db->query("UPDATE " . $this->db->dbprefix('product_sub') . "
	                    SET gl_num = gl_num - '$real_number',
	                    consign_num = consign_num - IF(consign_num>=0,'$consign_number',0),
	                    wait_num = wait_num + $consign_number
	                WHERE product_id = '".$value['product_id']."' AND color_id = '".$value['color_id']."' AND size_id = '".$value['size_id']."'");

	                //更新transation表
	                //换货入
	                if($value['change_num']> $src_consign_number) {
	                    $transaction = array();
	                    $transaction['trans_status'] = TRANS_STAT_AWAIT_IN;
	                    $transaction['trans_type'] = TRANS_TYPE_CHANGE_ORDER;
	                    $transaction['trans_sn'] = $change['change_sn'];
	                    $transaction['sub_id'] = $insert_change_product_id;
	                    $transaction['product_id'] = $value['product_id'];
	                    $transaction['color_id'] = $value['src_color_id'];
	                    $transaction['size_id'] = $value['src_size_id'];
	                    $transaction['product_number'] = $value['change_num'] - $src_consign_number;
	                    $transaction['depot_id'] = 0; //收货时再选择储位
	                    $transaction['trans_direction'] = 1;
	                    $transaction['location_id'] = 0;
	                    $this->return_model->insert_transaction($transaction);
	                }
	            }
	            $info = $this->change_model->create_change_trans($change,$order);
	            if ($info['error']!=0)
	            {
	                $this->db->query('ROLLBACK');
	                sys_msg($info['message'],1);
	            }
	        }
	    }
	    $this->change_model->update($change_main,$change_id);
	    $this->db->query('COMMIT');
	    $links[] = array('text' => '返回继续操作该换货货申请。', 'href' => "/order_change/edit/".$change_id);
        $links[] = array('text' => '返回换货单列表', 'href' => "/order_change");
        sys_msg('编辑换货单成功。', 0, $links);
        exit;
	}

	public function edit ($change_id = 0)
	{
            $this->load->helper('change');
		auth(array('order_change_edit','order_change_view','order_change_confirm','order_change_unconfirm'));
		$data = array();

	    $change = $this->change_model->change_info($change_id);
	    if (empty($change))
        {
        	sys_msg('换货单不存在！',1);
        }

        /* 关联订单信息 */
		$order_id = $change['order_id'];
        $order = $this->return_model->get_order_info($order_id);
        $data['order'] = $order;
        $data['change'] = $change;

        $data['suggestiontype_arr'] = $this->return_model->get_advice_type_arr(); //获取意见类型名称
        $data['suggestion_list'] = $this->change_model->get_change_advice($change_id); // 获取意见列表 @author Tony

	     /* 取得能执行的操作列表 */
	    $operable_list = $this->change_model->get_change_perm($change);
        $data['operable_list'] = $operable_list;

	    /* 关联订单信息 */
        $shipping_list = $this->change_model->available_change_shipping_list();
	    $shipping_list = get_pair($shipping_list, 'shipping_id', 'shipping_name');
		$data['shipping_list_arr'] = $shipping_list;

	    /* 取得订单商品 */
	    $change_product = $this->change_model->change_product($change_id);
	    //附加储位信息
	    $depot_out_arr = array();
	    $rs = $this->change_model->get_change_trans($change['change_sn']);
		if (!empty($rs))
		{
			foreach ($rs as $row)
			{
				if (!isset($depot_out_arr[$row['sub_id']]))
				{
					$depot_out_arr[$row['sub_id']] = array();
				}
				$depot_out_arr[$row['sub_id']][] = $row['depot_name'] . '[' . $row['location_name'] . '] ' . $row['product_number'];
			}
		}

		if (!empty($change_product))
		{
			foreach ($change_product as $key=>$product)
			{
				if (isset($depot_out_arr[$product['cp_id']]) && !empty($depot_out_arr[$product['cp_id']])) $change_product[$key]['depot_out'] = implode('<br/>',$depot_out_arr[$product['cp_id']]);
			}
		}
		$data['change_product'] = $change_product;

	    if ($operable_list['edit_product'])
	    {
	        $order_product = array();
	        $arr = $this->return_model->order_product_can_return($change['order_id']);
	        foreach ($arr as $v)
	        {
	        	if (isset($v['discount_type']) && $v['discount_type'] == 4) continue;
	            $k = $v['rec_id'].'-'.$v['product_id'].'-'.$v['color_id'].'-'.$v['size_id'].'-'.$v['track_id'];
	            $order_product[$k] = $v;
	        }
	        $product_list = $this->return_model->order_product_list($change['order_id']);
	        //扣除购物车中的锁定库存

	        //$cart_product_number = cart_product_number($change['order_id']);
	        foreach($product_list as $key=>$product) {
	        	/*
	            foreach($product as $k=>$gl) {
	                $tmp_key = $gl['product_id'].'-'.$gl['color_id'].'-'.$gl['size_id'];
	                $cart = $cart_product_number[$tmp_key];
	                if(empty($cart)||$gl['consign_num']==-2) continue;
	                $labor_consign = max(min($cart['product_number'],$gl['consign_num']),0);//待销数量与购物车数量取最小值，如果负值取0
	                $labor_real = $cart['product_number']-$labor_consign;//剩下的从实有库存扣
	                $gl['consign_num'] -= $labor_consign;
	                $gl['gl_num'] -= $labor_real;
	                $gl['real_num'] = max($gl['gl_num'] - $gl['wait_num'],0);
	                $product[$k] = $gl;
	            }
	            */
	            $product_list[$key] = $product;
	        }

	        //扣除购物车中的锁定库存结束

	        foreach ($change_product as $item)
	        {
	            $k = $item['product_id'].'-'.$item['src_color_id'].'-'.$item['src_size_id'];
	            $product_list[$item['product_id']][$k]['gl_num'] += $item['change_num']-$item['consign_num'];
	            $product_list[$item['product_id']][$k]['real_num'] += $item['change_num']-$item['consign_num'];
	            if($product_list[$item['product_id']][$k]['gl_consign_num']>=0) $product_list[$item['product_id']][$k]['gl_consign_num'] += $item['consign_num'];
	        }

	        foreach ($change_product as $item)
	        {
	            $k = $item['op_id'].'-'.$item['product_id'].'-'.$item['src_color_id'].'-'.$item['src_size_id'].'-'.$item['parent_cp_id'];
	            $k2 = $item['product_id'].'-'.$item['color_id'].'-'.$item['size_id'];
	            if(isset($order_product[$k])) {
	                $order_product[$k]['product_num'] += $item['change_num'];
	                $order_product[$k]['consign_num'] += $item['consign_num'];
	                $order_product[$k]['show_num'] = $item['change_num'];
	                $order_product[$k]['product_price'] = empty($item['product_price'])?'0.00':number_format($item['product_price'], 2, '.', '');
	                $order_product[$k]['formated_product_price']    = empty($item['product_price'])?'0.00':number_format($item['product_price'], 2, '.', '');
	                $order_product[$k]['color_size_name'] = $item['src_color_name']."--".$item['src_size_name'];
	            }else {
	                $item['product_num'] = $item['change_num'];
	                $item['color_id'] = $item['src_color_id'];
	                $item['size_id'] = $item['src_size_id'];
	                $item['color_name'] = $item['src_color_name'];
	                $item['size_name'] = $item['src_size_name'];
	                $item['show_num'] = $item['change_num'];
	                $item['track_id'] = $item['parent_cp_id'];
	                $item['color_size_name'] = $item['src_color_name']."--".$item['src_size_name'];
	                $order_product[$k] = $item;
	            }
	            $order_product[$k]['product_list'] = $product_list[$item['product_id']];
	            $order_product[$k]['product_list'][$k2]['show_num'] = $item['change_num'];
	            //$order_product[$k]['product_list'][$k2]['depotarr'] = array('depot_id'=>$item['depot_id'],'location_id'=>$item['location_id'],'location_name'=>$item['location_name']);
	        }
	        foreach ($order_product as $key=>$product)
	        {
	            if (!isset($product['product_list']))
	            {
	                $product['product_list'] = $product_list[$product['product_id']];
	                $product['color_size_name'] = $product['color_name']."--".$product['size_name'];
	            }
	            $selarr = array();
	            foreach ($product['product_list'] as $v)
	            {
	                if($v['real_num']<1&&$v['gl_consign_num']<1&&$v['gl_consign_num']!=-2) continue;
	                $selarr[$product['op_id'].'_'.$product['product_id'].'_'.$product['color_id'].'_'.$product['size_id'].'_'.$product['track_id'].'_'.$v['color_id'].'_'.$v['size_id']]
	                        =$v['color_name'].'--'.$v['size_name'].' ['.$v['real_num'].'+'.(($v['gl_consign_num']>=0 || $v['gl_consign_num']==-1)?max($v['gl_consign_num'],0):'+').']' ;
	            }
	            $product['selarr'] = $selarr;
	            $order_product[$key] = $product;
	        }

			$depot_rs = $this->depot_model->filter_depot_all(array('is_use'=>1,'depot_type'=>1));
	        $depot_arr = array();
	        if (!empty($depot_rs))
	        {
	        	foreach ($depot_rs as $row)
	        	{
	        		$depot_arr[$row['depot_id'].'___'.$row['depot_name']] = $row['depot_name'];
	        	}

	        }
			$data['depot_arr'] = $depot_arr;
			$data['change_product_detail'] = $order_product;

	    }

	    //取得配送地址省市区
	    $change['country'] = max($change['country'], 1);
	    if ($change['country'] > 0)
	    {
			$data['province_list'] = $this->return_model->get_regions(1, $change['country']); /* 取得省份 */
	        if ($change['province'] > 0)
	        {
	        	$data['city_list'] = $this->return_model->get_regions(2, $change['province']); /* 取得城市 */
	            if ($change['city'] > 0)
	            {
	            	$data['district_list'] = $this->return_model->get_regions(3, $change['city']); /* 取得区域 */
	            }
	        }
	    }

	    /* 模板赋值 */
	    $data['action_list'] = $this->change_model->get_action_list($change_id);
        $data['change_reasons'] = json_encode($this->change_model->get_change_reasons());
        $this->load->helper('form');
        $this->load->view('order_change/edit', $data);
	}

	public function operate ()
	{
		$all_post = $this->input->post();
		$data = array();
		/* 取得退货单id（可能是多个，多个sn）和操作备注（可能没有） */
        $change_id       = $all_post['change_id'];
        $change = $this->change_model->change_info($change_id);
        $action_note    = isset($all_post['action_note']) ? trim($all_post['action_note']) : '';
        $require_note = false;
        $operation = '';

        /* 锁定 */
	    if (isset($all_post['lock']))
	    {
	        $require_note   = false;
	        $action         = '锁定';
	        $operation      = 'lock';
	    }
	    /* 解锁 */
	    elseif (isset($all_post['unlock']))
	    {
	        $require_note   = false;
	        $action         = '解锁';
	        $operation      = 'unlock';
	    }
	    /* 客服审核 */
	    elseif (isset($all_post['service_confirm']))
	    {
	        $require_note   = false;
	        $action         = '客审';
	        $operation      = 'service_confirm';
	    }
	    /* 客服反审核 */
	    elseif (isset($all_post['unservice_confirm']))
	    {
	        $require_note   = false;
	        $action         = '反客审';
	        $operation      = 'unservice_confirm';
	    }

	    /* 发货 */
	    elseif (isset($all_post['shipping']))
	    {
	        $require_note   = false;
	        $action         = '换货单发货';
	        $operation      = 'shipping';
	    }

	    /* 入库 */
	    elseif (isset($all_post['shipped']))
	    {
	        $require_note   = false;
	        $action         = '入库';
	        $action_note	= '换货单入库';
	        $operation      = 'shipped';

	        $change_product = $this->change_model->change_product($change_id);
	        foreach ($change_product as $key=>$val)
	        {
	            if ($val['src_consign_num'] >= $val['change_num'])
	            {
	                unset($change_product[$key]);
	                continue;
	            }
	            $change_product[$key]['real_num'] = $val['change_num'] - $val['src_consign_num'];
	        }
	        $src_og_ids = $src_cg_ids = $src_order_depot = $src_change_depot =  array();
	        //取出原出库储位,以判断应该入哪个仓库
	        foreach ($change_product as $product)
	        {
	            if($product['parent_cp_id']==0) {
	                $src_og_ids[] = $product['op_id'];
	            }else {
	                $src_cg_ids[] = $product['parent_cp_id'];
	            }
	        }

	        if (!empty($src_og_ids))
	        {
	            $rs = $this->change_model->get_trans_out(TRANS_TYPE_SALE_ORDER,$src_og_ids);
	            if (!empty($rs))
	            {
	            	foreach ($rs as $row)
	            	{
	            		if (empty($src_order_depot[$row['sub_id']]))
	            		{
	            			$src_order_depot[$row['sub_id']] = $row['trans_sn'];
	            		}
	                	$src_order_depot[$row['sub_id']] .= '<br/>'.$row['depot_name'].'-'.$row['location_name'].' => '.$row['product_number'];
	            	}
	            }
	        };
	        if (!empty($src_cg_ids))
	        {
	        	$rs = $this->change_model->get_trans_out(TRANS_TYPE_CHANGE_ORDER,$src_cg_ids);
	        	if (!empty($rs))
	            {
	            	foreach ($rs as $row)
	            	{
	            		if (empty($src_change_depot[$row['sub_id']]))
	            		{
	            			$src_change_depot[$row['sub_id']] = $row['trans_sn'];
	            		}
	                	$src_change_depot[$row['sub_id']] .= '<br/>'.$row['depot_name'].'-'.$row['location_name'].' => '.$row['product_number'];
	            	}
	            }
	        }

	        foreach ($change_product as $key=>$product)
	        {
	            $change_product[$key]['out_depot'] = $product['parent_cp_id']==0?$src_order_depot[$product['op_id']]:$src_change_depot[$product['parent_cp_id']];
	        }
			$data['change_product'] = $change_product;


	        $depot_arr = array(3=>'退货仓 [不可售]');
			$data['depot_arr'] = $depot_arr;
	    }


	    /* 作废 */
	    elseif (isset($all_post['invalid']))
	    {
	        $require_note   = 1;
	        $action         = '作废';
	        $operation      = 'invalid';
	    }
	    /* 完结 */
	    elseif (isset($all_post['is_ok'])) {
	        $require_note   =  1;
	        $action         = '完结';
	        $operation      = 'is_ok';
	    }
	    /* 标记为问题单 */
	    elseif (isset($all_post['odd'])) {
	        $require_note   =  0;
	        $action         = '标记为问题单';
	        $operation      = 'odd';
	    }
	    /* 取消问题单标记 */
	    elseif (isset($all_post['odd_cancel'])) {
	        $require_note   =  0;
	        $action         = '取消问题单标记';
	        $operation      = 'odd_cancel';
	    }

	    /* 直接处理还是跳到详细页面 */
	    if (($require_note && $action_note == '') || $operation == 'shipped')
	    {
	        $change = $this->change_model->change_info($change_id);
	        $order = $this->return_model->get_order_info($change['order_id']);
            $shipping_list = $this->change_model->available_change_shipping_list();

	        $shipping_list_arr = array();
	        foreach ($shipping_list as $shipping_item)
	        {
	            $shipping_list_arr[$shipping_item['shipping_id']] = $shipping_item['shipping_name'];
	        }

	        $data['require_note'] = $require_note;
	        $data['action_note'] = $action_note;
	        $data['anonymous'] = true;
	        $data['change_id'] = $change_id;
	        $data['operation'] = $operation;
	        $data['change'] = $change;
	        $data['shipping_list_arr'] = $shipping_list_arr;

			$this->load->helper('form');
			$this->load->view('order_change/operate', $data);
	    }
	    else
	    {
	    	redirect('/order_change/operate_post/change_id/'.$change_id.'/operation/'.$operation.'/action_note/'.urlencode($action_note));
	    }

	}

	public function operate_post ()
	{
		$all_post = $this->uri->uri_to_assoc(3);
		$all_post_other = $this->input->post();
        $aid = $this->admin_id;
        $change_id   = isset($all_post['change_id'])?$all_post['change_id']:$all_post_other['change_id'];        // 退货单id
        $operation  = isset($all_post['operation'])?$all_post['operation']:$all_post_other['operation'];	 // 退货单操作
        $action_note = isset($all_post['action_note'])?$all_post['action_note']:(isset($all_post_other['action_note'])?$all_post_other['action_note']:'');
        $change = $this->change_model->change_info($change_id);
        $order_id = $change['order_id'];
        $order = $this->return_model->get_order_info($order_id);

        $this->db->query('BEGIN');
		$this->db->query('SELECT * FROM '.$this->db->dbprefix('order_change_info')." WHERE change_id = '".$change_id."' FOR UPDATE");

		$operable_list = $this->change_model->get_change_perm($change);
		if (!$operable_list[$operation])
        {
            $links[0]['text'] = '返回换货单明细';
            $links[0]['href'] = "/order_change/edit/".$change_id;
            sys_msg('此换货单的状态已被他人更改，您不再具有此次操作的权限，请刷新后再进行操作。',1,$links);
        }

        /* 锁定 */
	    if ('lock' == $operation)
	    {
	    	$arr['lock_admin'] = $aid;
            $arr['lock_date'] = date('Y-m-d H:i:s');
            $this->change_model->update($arr, $change_id);

            $action_note = '锁定换货单'.' '.$action_note;/* 记录log */
            $this->change_model->insert_action($change,$action_note);
	    }

	    /* 解锁 */
	    elseif ('unlock' == $operation)
	    {
	        $arr['lock_admin'] = 0;
            $arr['lock_date'] = '';
	        $this->change_model->update($arr, $change_id);
	        if ($action_note == "")
	        {
	            $action_note = '解锁换货单';
	        }
	        $this->change_model->insert_action($change,$action_note);
	    }

	    /* 客服审核 */
	    elseif ('service_confirm' == $operation)
	    {
	        /* 标记换货单为已确认 */
	        $this->change_model->update(array('change_status' => 1, 'confirm_admin' => $aid, 'confirm_date' =>date('Y-m-d H:i:s'), 'lock_admin' => 0, 'lock_date' => ''), $change_id);

	        /* 记录log */
	        if ($action_note == "") {
	            $action_note = '客服审核换货单';
	        }
            $change_copy = $change;
            $change_copy['change_status'] = 1;
            $this->change_model->insert_action($change_copy,$action_note);
	    }
	    /* 客服反审核 */
	    elseif ('unservice_confirm' == $operation)
	    {
	    	$this->change_model->update(array('change_status' => 0, 'confirm_admin' => 0, 'confirm_date' =>'', 'lock_admin' => 0, 'lock_date' => ''), $change_id);

	        /* 记录log */
	        if ($action_note == "") {
	            $action_note = '客服反审核换货单';
	        }
	        $change_copy = $change;
            $change_copy['change_status'] = 0;
            $this->change_model->insert_action($change_copy,$action_note);
	    }
	    /* 发货 */
	    elseif ('shipping' == $operation)
	    {
	    	$arr = array();
	        $arr['shipping_status'] = 1;
	        $arr['shipping_admin'] = $aid;
	        $arr['shipping_date'] = date('Y-m-d H:i:s');
	        $arr['lock_admin'] = 0;
	        $arr['lock_date'] = '';
	    	$this->change_model->update($arr, $change_id);

	        /* 记录log */
	        if ($action_note == "") {
	            $action_note = '换货单发货';
	        }
	        $change_copy = $change;
            $change_copy['shipping_status'] = 1;
            $this->change_model->insert_action($change_copy,$action_note);

            $this->return_model->update_transaction(array('trans_status'=>TRANS_STAT_OUT,'update_admin'=>$aid,'update_date'=>date('Y-m-d H:i:s')),array('trans_status'=>TRANS_STAT_AWAIT_OUT,'trans_sn'=>$change['change_sn']));
			$this->change_model->notify_shipping((object)$change_copy);

	    }
	    /* 入库 */
	    elseif ('shipped' == $operation)
	    {
	        /* 标记换货单为已入库，并记录入库时间，入库人 */
	        $tmp_links[] = array('text' => '返回换货单详情', 'href' => "/order_change/edit/".$change_id );
	        $arr = array();
	        $arr['shipped_status'] = 1;
	        $arr['shipped_admin'] = $aid;
	        $arr['shipped_date'] = date('Y-m-d H:i:s');
	        $arr['lock_admin'] = 0;
	        $arr['lock_date'] = '';
	        $this->change_model->update($arr, $change_id);

	        $depot_id = 3;
	        $trans_arr = array();
	        $location_ids = array();
	        if (!empty($all_post_other['rec_id']))
	        {
	            foreach ($all_post_other['rec_id'] as $key=>$rec_id)
	            {
	                $location_id = $all_post_other['location_id'][$key];
	                $trans_arr[$rec_id] = array('rec_id'=>$rec_id,'location_id'=>$location_id,'depot_id'=>$depot_id,'depot_type'=>0);
	                $location_ids[] = $location_id;
	            }
	        }

	        $rs = $this->return_model->filter_transaction_all(array('trans_type'=>TRANS_TYPE_CHANGE_ORDER,'trans_status'=>TRANS_STAT_AWAIT_IN,'trans_sn'=>$change['change_sn']));
	        $to_add_rec = array();
	        if (!empty($rs))
	        {
	        	foreach ($rs as $val)
	        	{
					if (!isset($trans_arr[$val['sub_id']]))
					{
		                $this->db->query('ROLLBACK');
		                sys_msg('储位信息丢失！',1,$tmp_links);
		                exit;
		            }
		            $trans = $trans_arr[$val['sub_id']];
		            $this->return_model->update_transaction(array('trans_status'=>TRANS_STAT_IN,'depot_id'=>$trans['depot_id'],'location_id'=>$location_id,'update_admin'=>$aid,'update_date'=>date('Y-m-d H:i:s')),array('transaction_id'=>$val['transaction_id']));

	        	}
	        }

	        $sql = "update ".$this->db->dbprefix('product_sub')." as gl,
	                    (select sum(src_consign_num) as src_consign_num,product_id,src_color_id,src_size_id from ".$this->db->dbprefix('order_change_product')."
	                    where change_id = '".$change_id."' group by product_id,src_color_id,src_size_id) as og
	                    set gl.consign_num = gl.consign_num + IF(gl.consign_num>=0,og.src_consign_num,0)
	                    where gl.product_id = og.product_id and gl.color_id = og.src_color_id and gl.size_id = og.src_size_id";
	        $this->db->query($sql);

	        /* 记录log */
	        if ($action_note=="")
	        {
	            $action_note = '换货单入库';
	        }
	        $change_copy = $change;
            $change_copy['shipped_status'] = 1;
            $this->change_model->insert_action($change_copy,$action_note);
			$this->change_model->notify_shipped((object)$change_copy);
	    }

	    /* 完结 */
	    elseif ('is_ok' == $operation)
	    {
	        // 标记换货单为“完结”，
	        $action_note = isset($all_post_other['action_note']) ? trim($all_post_other['action_note']) : '';
	        $arr = array(
	                'is_ok'  => 1,
	                'is_ok_date' => date('Y-m-d H:i:s'),
                    'is_ok_admin' => $aid,
                    'lock_admin' => 0,
                    'lock_date' => '',
	        );
	        $this->change_model->update($arr, $change_id);

	        /* 记录log */
	        $this->change_model->insert_action($change,$action_note);

	    }
	    /* 设为无效 */
	    elseif ('invalid' == $operation)
	    {
	        /* 标记换货单为“无效”、“未付款” */
	        $arr = array(
                    'is_ok'  => 1,
                    'is_ok_date' => date('Y-m-d H:i:s'),
                    'is_ok_admin' => $aid,
                    'lock_admin' => 0,
                    'lock_date' => '',
                    'change_status'=>4,
                    'cancel_admin'=>$aid,
                    'cancel_date' => date('Y-m-d H:i:s')
                );
            $this->change_model->update($arr, $change_id);

	        /* 记录log */
	        if ($action_note == "")
	        {
	            $action_note == '换货单作废';
	        }
	        $change_copy = $change;
            $change_copy['change_status'] = 4;
            $this->change_model->insert_action($change_copy,$action_note);

	        // 加库存

	        $this->db->query("UPDATE " . $this->db->dbprefix('product_sub') . " AS gl,
	                (select sum(change_num) as change_num, sum(consign_num) as consign_num,product_id,color_id,size_id
	                from " . $this->db->dbprefix('order_change_product') . " where change_id = '".$change_id."'
	                group by product_id,color_id,size_id) AS cg
	                SET gl.gl_num = gl.gl_num + (cg.change_num - cg.consign_num),
	                gl.consign_num = gl.consign_num + IF(gl.consign_num>=0,cg.consign_num,0),
	                gl.wait_num = gl.wait_num - cg.consign_num
	                where gl.product_id = cg.product_id and gl.color_id = cg.color_id and gl.size_id = cg.size_id ") ;

	        $this->return_model->update_transaction(array('trans_status'=>TRANS_STAT_CANCELED,'cancel_admin'=>$aid,'cancel_date'=>date('Y-m-d H:i:s')),array('trans_type'=>TRANS_TYPE_CHANGE_ORDER,'trans_sn'=>$change['change_sn']));

	    }
            /* 标记为问题单 */
            elseif ('odd' == $operation)
            {
                $this->change_model->update(array('odd'=>1),$change_id);
                $this->change_model->insert_action($change,'标记为问题单');
            }
            /* 取消问题单标记 */
            elseif ('odd_cancel' == $operation)
            {
                $this->change_model->update(array('odd'=>0),$change_id);
                $this->change_model->insert_action($change,'取消问题单标记');
            }
	    else
	    {

	        die('invalid params');
	    }
	    $this->db->query('COMMIT');

	    //*/ 操作成功
	    $links[] = array('text' => '换货明细', 'href' => "/order_change/edit/".$change_id);
        sys_msg('操作成功', 0, $links);

	}

	public function post_suggest ($change_id)
	{
		$all_post = $this->input->post();
        $suggestion_type=intval($all_post['suggestiontype_id']);
        $suggestion_content=$all_post['suggestion_content'];
        $suggestion_item=array
            (
            'type_id'=>$suggestion_type,
            'advice_content'=>$suggestion_content,
            'order_id'=>$change_id,
            'advice_admin'=>$this->admin_id,
            'advice_date'=>date('Y-m-d H:i:s'),
            'is_return'	=> 3
        );
        $advice_id = $this->return_model->insert_advice($suggestion_item);
        if ($advice_id > 0)
        {
            $links[0]['text'] = '返回换货单明细';
            $links[0]['href'] = "/order_change/edit/".$change_id;
            sys_msg('添加意见成功！',0,$links);
        }else {
            sys_msg('Post Advice Error!',1);
        }
	}

	public function depotshipsave ()
	{
		/* 检查权限 */
	    auth(array('order_change_shipped,order_change_shipping'));
	    /* 取得参数change_id */
	    $all_post = $this->input->post();
	    $change_id = isset($all_post['change_id']) ? intval($all_post['change_id']) : 0;
	    if (!($change_id > 0))
	    {
	        die('invalid params');
	    }
	    $shipping_id = isset($all_post['shipping_id']) ? intval($all_post['shipping_id']) : '';  //非0空值
	    if (empty($shipping_id)) sys_msg('请指定配送方式',1);
	    $this->db->query('BEGIN');
        $this->db->query('SELECT * FROM '.$this->db->dbprefix('order_change_info')." WHERE change_id = '".$change_id."' FOR UPDATE");

	    $change = $this->change_model->change_info($change_id);
	    if ($change['change_status']!=1) sys_msg('换货单未客审，请通过保存订单更改！',1);

	    $operable_list = $this->change_model->get_change_perm($change);
	    if(!$operable_list['depotshipsave']) sys_msg('您没有该权限',1);

	    $change['shipping_id'] 	= empty($shipping_id) ? '0' : $shipping_id;
	    $arr = array('shipping_id'=>$change['shipping_id']);
	    $this->change_model->update($arr, $change_id);

	    $this->db->query('COMMIT');
	    $links[] = array('text' => '返回继续操作该换货货申请。', 'href' => "/order_change/edit/".$change_id);
        $links[] = array('text' => '返回换货单列表', 'href' => "/order_change");
        sys_msg('编辑换货单成功。', 0, $links);
	}

	public function invoice_save ()
	{
		/* 检查权限 */
	    auth(array('order_change_shipping'));
	    /* 取得参数change_id */
	    $all_post = $this->input->post();
	    $change_id = isset($all_post['change_id']) ? intval($all_post['change_id']) : 0;
	    if (!($change_id > 0))
	    {
	        die('invalid params');
	    }

	    $invoice_no = isset($all_post['invoice_no']) ? trim($all_post['invoice_no']) : '';  //非0空值
	    if (empty($invoice_no)) sys_msg('请指定快递单号',1);
	    $this->db->query('BEGIN');
        $this->db->query('SELECT * FROM '.$this->db->dbprefix('order_change_info')." WHERE change_id = '".$change_id."' FOR UPDATE");

	    $change = $this->change_model->change_info($change_id);
	    if ($change['change_status']!=1) sys_msg('换货单未客审，请通过保存订单更改！',1);

	    $operable_list = $this->change_model->get_change_perm($change);
	    if(!$operable_list['shipping']) sys_msg('您没有该权限',1);

	    $change['invoice_no'] 	= $invoice_no;
	    $arr = array('invoice_no'=>$change['invoice_no']);
	    $this->change_model->update($arr, $change_id);

	    $this->db->query('COMMIT');
	    $links[] = array('text' => '返回继续操作该换货货申请。', 'href' => "/order_change/edit/".$change_id);
        $links[] = array('text' => '返回换货单列表', 'href' => "/order_change");
        sys_msg('编辑换货单成功。', 0, $links);
	}


}
###