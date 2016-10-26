<?php
/**
*
*/
class User_model extends CI_Model
{
	private $_db;
	public function __construct(&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	public function filter($filter = array())
	{
		$query = $this->_db->get_where('user_info',$filter,1);
		return $query->row();
	}
	
	public function is_mobile_other_used($mobile,$user_id)
	{
		$sql = "SELECT COUNT(*) as ct FROM ty_user_info WHERE user_id <> '".$user_id."' AND mobile = '".$mobile."'";
		$query = $this->_db->query($sql);
        $row = $query->row();
		return $row->ct > 0?TRUE:FALSE;
	}

	public function user_account_log_kind($filter = array())
	{
		$query = $this->_db->get_where('user_account_log_kind',$filter);
		return $query->result();
	}


    public function lock_user($user_id)
    {
        $sql = "SELECT * FROM ".$this->_db->dbprefix('user_info')." WHERE user_id = ? FOR UPDATE";
        $query = $this->_db->query($sql,array($user_id));
        return $query->row();
    }

	public function filter_user_rank($filter = array())
	{
		$query = $this->_db->get_where('user_rank',$filter,1);
		return $query->row();
	}

	public function max_user_rank($paid_money)
	{
		$sql = "SELECT * FROM " . $this->_db->dbprefix('user_rank') . " WHERE min_points<= " . intval($paid_money) . " ORDER BY min_points DESC LIMIT 1";
		$query = $this->_db->query($sql);
        return $query->row();
	}

	public function user_data($user_id)

    {
    	$sql = "SELECT user_id, user_name, password, email,user_type,discount_percent,mobile,mobile_checked,email_validated" .
    			" FROM " . $this->_db->dbprefix('user_info') . " WHERE user_id='".$user_id."' LIMIT 1";
        $query = $this->_db->query($sql);
        $row = $query->row();
        if ($row)
        {
        	$row->discount_percent = round(floatval($row->discount_percent),2);
        }
        return $row;
    }

    public function user_rank_point($user_id)
	{
		$sql = "SELECT ur.* FROM " . $this->_db->dbprefix('user_rank') . " AS ur LEFT JOIN ".$this->_db->dbprefix('user_info')." AS u ON u.rank_id = ur.rank_id WHERE u.user_id = '".$user_id."'";
		$query = $this->_db->query($sql);
        return $query->row();
	}

	public function mail_template($tpl_name)
	{
	    $sql = 'SELECT * FROM ' . $this->_db->dbprefix('mail_templates') . " WHERE template_code = '$tpl_name' LIMIT 1";
	    $query = $this->_db->query($sql);
        return $query->row();
	}

	public function user_voucher_num($user_id)
	{

	 	$now = date('Y-m-d H:i:s');
        $from = " from ".$this->_db->dbprefix('voucher_record')." as v
                left join ".$this->_db->dbprefix('voucher_release')." as r on r.release_id = v.release_id and r.campaign_id = v.campaign_id
                left join ".$this->_db->dbprefix('voucher_campaign')." as a on a.campaign_id = v.campaign_id
                where UNIX_TIMESTAMP(v.start_date) < UNIX_TIMESTAMP('".$now."')
                and  UNIX_TIMESTAMP(v.end_date) > UNIX_TIMESTAMP('".$now."')
                and v.used_number<v.repeat_number
                and v.user_id = $user_id ";

        $sql = "SELECT count(*) as total ".$from;
        $query = $this->_db->query($sql);
		$record_count = $query->row();
		return $record_count->total;
	}

	public function user_order_num($user_id)
	{
		$sql = "SELECT COUNT(*) AS total FROM ".$this->_db->dbprefix('order_info')." WHERE user_id='$user_id' AND order_status=1 AND is_ok=1 ";
		$query = $this->_db->query($sql);
		$record_count = $query->row();
		return $record_count->total;
	}

	/**
	* 会员等级
	*/
	public function user_rank_page()
	{
		$sql = "SELECT * FROM ".$this->_db->dbprefix('user_rank');
		$query = $this->_db->query($sql);
		$rs = $query->result();
		$arr_rs = array();
		if (!empty($rs))
		{
			foreach ($rs as $row)
			{
				$arr_rs[$row->rank_id]=$row;
			}
		}
		return $arr_rs;
	}

	/** 留言被回复数量*/
	public function liuyan_num($user_id)
	{
		$sql="SELECT count(*) AS total from ". $this->_db->dbprefix('product_liuyan') ." WHERE reply_content !='' AND user_id='$user_id' AND is_audit=1 AND is_del = 0 ";
		$query = $this->_db->query($sql);
		$record_count = $query->row();
		return $record_count->total;
	}

	public function user_point_log($user_id)
	{
		$sql="SELECT count(*) AS total FROM ".$this->_db->dbprefix('user_account_log')." WHERE user_id='$user_id' AND change_code='point_detail' ";
		$query = $this->_db->query($sql);
		$record_count = $query->row();
		return $record_count->total;
	}

    public function update($data, $user_id)
    {
        $this->_db->update('user_info', $data, "user_id = $user_id");
        return $this->_db->affected_rows();
    }
    public function update_by_mobile($data, $mobile)
    {
        $this->_db->update('user_info', $data, "mobile = '$mobile'");
        return $this->_db->affected_rows();
    }

    public function update_address($data, $address_id)
    {
        $this->_db->update('user_address', $data, "address_id = $address_id");
        return $this->_db->affected_rows();
    }

    public function update_address_used($address_id,$user_id)
    {
		$this->_db->update('user_address', array('is_used'=>0), "user_id = $user_id");
		$this->_db->update('user_address', array('is_used'=>1), "address_id = $address_id");
    }

    public function update_login_other($other, $user_id)
    {
    	$sql = "UPDATE " .$this->_db->dbprefix('user_info'). " SET".
	           " visit_count = visit_count + 1, ".
	           " last_ip = '" .real_ip(). "',".
	           " last_date = '" .date('Y-m-d H:i:s'). "'".$other.
	           " WHERE user_id = '" . $user_id . "'";
        $this->_db->query($sql);
        return $this->_db->affected_rows();
    }

    public function update_login_cart($session_id,$user_id)
    {
		$sql = "UPDATE " .$this->_db->dbprefix('front_cart'). " SET".
	           " user_id = '" . $user_id . "'".
	           " WHERE session_id = '" . $session_id . "'";
	    $this->_db->query($sql);
        return $this->_db->affected_rows();
    }

    public function insert($data)
    {
        $this->_db->insert('user_info', $data);
        return $this->_db->insert_id();
    }

    public function insert_address($data)
    {
        $this->_db->insert('user_address', $data);
        return $this->_db->insert_id();
    }

    public function insert_sms_log($data)
    {
    	$this->_db->insert('sms_log', $data);
        return $this->_db->insert_id();
    }

    public function insert_liuyan($data)
    {
        $this->_db->insert('product_liuyan', $data);
        return $this->_db->insert_id();
    }

    public function insert_account($data)
    {
        $this->_db->insert('user_account_log', $data);
        return $this->_db->insert_id();
    }

    public function filter_address($filter)
    {
    	$query = $this->_db->get_where('user_address',$filter,1);
    	return $query->row();
    }

    public function address_list($user_id)
    {
        $sql = "SELECT a.*,p.region_name AS province_name, c.region_name AS city_name, d.region_name AS district_name
        FROM ".$this->_db->dbprefix('user_address')." AS a
        LEFT JOIN ".$this->_db->dbprefix('region_info')." AS p ON a.province = p.region_id
        LEFT JOIN ".$this->_db->dbprefix('region_info')." AS c ON a.city = c.region_id
        LEFT JOIN ".$this->_db->dbprefix('region_info')." AS d ON a.district = d.region_id
        WHERE user_id = ?";
        $query = $this->_db->query($sql, array(intval($user_id)));
        return $query->result();
    }

    function account_list($filter,$user_id)
	{
		$point_rows = $this->user_account_log_kind(array('change_type'=>0,'is_use'=>1));
		foreach($point_rows as $key => $value)
		{
			$point_info[$value->change_code] = $value;
			$point_code_arr[] = $value->change_code;
		}
		$now = date('Y-m-d H:i:s');
		$where = " WHERE user_id = '" . $user_id . "' ";

		$point_xiaofei = array('order_pay','money_return_payback','order_payback');
		$point_chongzhi = array('recharge','money_return_cancel');
		$point_tiaojie = array('change_account');

		if (!isset($filter['order_status']))
		{
			$filter['order_status'] = 1;
		}
		switch ($filter['order_status'])
		{
				case '1' : //最近交易
					$where .= " AND user_money <> 0 AND change_code " . db_create_in($point_code_arr);
					break;

				case '2' : //充值明细
					$where .= " AND user_money <> 0 AND change_code " . db_create_in($point_chongzhi);
					break;

				case '3' : //消费明细
					$where .= " AND user_money <> 0 AND change_code " . db_create_in($point_xiaofei);
					break;

				case '4' : //余额调节
					$where .= " AND user_money <> 0 AND change_code " . db_create_in($point_tiaojie);
					break;
				default:
					$where .= " AND user_money <> 0 AND change_code " . db_create_in($point_code_arr);
					break;
		}

		$filter['sort_by'] = 'create_date';
		$filter['sort_order'] = 'DESC';

		$sql = "SELECT COUNT(*) AS ct FROM ". $this->_db->dbprefix('user_account_log') . $where;
		$query = $this->_db->query($sql);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

		/* 查询记录 */
	    $sql = "SELECT * FROM ".$this->_db->dbprefix('user_account_log').
	             $where ." ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

		$query = $this->_db->query($sql);
		$rows = $query->result();

		//得到第一条记录的帐户余额
		if(count($rows) > 0)
		{
			$sql = "SELECT SUM(user_money) AS surplus FROM " .$this->_db->dbprefix('user_account_log').
	           	" WHERE user_id = '$user_id' AND UNIX_TIMESTAMP(create_date) < UNIX_TIMESTAMP('".$now."')";
	        $query = $this->_db->query($sql);
	        $row = $query->row();
	        $cur_page_surplus = number_format($row->surplus, 2, '.', '');
		}

		for($i=0; $i<count($rows); $i++)
		{
			$rows[$i]->format_create_date = date("Y-m-d",strtotime($rows[$i]->create_date));
			if(in_array($rows[$i]->change_code, $point_code_arr))
			{
				$rows[$i]->change_code_value = $point_info[$rows[$i]->change_code]->change_name;
			}
			else
			{
				$rows[$i]->change_code_value = '其他';
			}
			$rows[$i]->short_change_desc = _smarty_modifier_truncate($rows[$i]->change_desc, 40, '...');
			$rows[$i]->cur_surplus = number_format($cur_page_surplus, 2, '.', '');
			$cur_page_surplus = $cur_page_surplus - $rows[$i]->user_money;
		}

	    return $arr = array('list' => $rows, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}

	function points_list($filter,$user_id)
	{
		$point_rows = $this->user_account_log_kind(array('change_type'=>1,'is_use'=>1));
		foreach($point_rows as $key => $value)
		{
			$point_info[$value->change_code] = array('change_code'=>$value->change_code,'change_name'=>$value->change_name);
			$point_code_arr[] = $value->change_code;
		}

		$point_info['change_account'] = array('change_code'=>'change_account','change_name'=>'调节账户');
		$point_code_arr[] = 'change_account';
		$now = date('Y-m-d H:i:s');
		$where = " WHERE user_id = '" . $user_id . "' ";

		if (!isset($filter['order_status']))
		{
			$filter['order_status'] = 1;
		}
		switch ($filter['order_status'])
		{
				case '1' : //最近交易
					$where .= " AND pay_points <>0 AND change_code " . db_create_in($point_code_arr);
					break;

				case '2' : //已获积分
					$where .= " AND pay_points >0 AND change_code " . db_create_in($point_code_arr);
					break;

				case '3' : //已用积分
					$where .= " AND pay_points <0 AND change_code " . db_create_in($point_code_arr);
					break;

				default:
					$where .= " AND pay_points <>0 AND change_code " . db_create_in($point_code_arr);
					break;
		}

		$filter['sort_by'] = 'create_date';
		$filter['sort_order'] = 'DESC';

		$sql = "SELECT COUNT(*) AS ct FROM ". $this->_db->dbprefix('user_account_log') . $where;
		$query = $this->_db->query($sql);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

		/* 查询记录 */
	    $sql = "SELECT * FROM ".$this->_db->dbprefix('user_account_log').
	             $where ." ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

		$query = $this->_db->query($sql);
		$rows = $query->result();

		//得到第一条记录的帐户余额
		if(count($rows) > 0)
		{
			$sql = "SELECT SUM(pay_points) AS surplus FROM " .$this->_db->dbprefix('user_account_log').
	           	" WHERE user_id = '$user_id' AND UNIX_TIMESTAMP(create_date) < UNIX_TIMESTAMP('".$now."')";
	        $query = $this->_db->query($sql);
	        $row = $query->row();
	        $cur_page_surplus = number_format($row->surplus, 2, '.', '');
		}

		for($i=0; $i<count($rows); $i++)
		{
			$rows[$i]->format_create_date = date("Y-m-d",strtotime($rows[$i]->create_date));
			if(in_array($rows[$i]->change_code, $point_code_arr))
			{
				$rows[$i]->change_code_value = $point_info[$rows[$i]->change_code]['change_name'];
			}
			else
			{
				$rows[$i]->change_code_value = '其他';
			}
			$rows[$i]->short_change_desc = _smarty_modifier_truncate($rows[$i]->change_desc, 40, '...');
			$rows[$i]->cur_surplus = number_format($cur_page_surplus, 0, '.', '');
			$cur_page_surplus = $cur_page_surplus - $rows[$i]->pay_points;
		}

	    return $arr = array('list' => $rows, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}

	public function liuyan_list($filter,$user_id)
	{
		$now = date('Y-m-d H:i:s');
		$where = " WHERE a.user_id = '" . $user_id . "' AND a.tag_type=1 AND a.comment_type=1 AND a.is_del=0 ";
		$filter['sort_by'] = 'a.comment_date';
		$filter['sort_order'] = 'DESC';

		$sql = "SELECT COUNT(*) AS ct FROM ". $this->_db->dbprefix('product_liuyan') . " as a ". $where;
		$query = $this->_db->query($sql);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

		/* 查询记录 */
	    $sql = "SELECT g.product_id,g.product_sn, g.product_name, g.market_price, g.shop_price AS org_price, g.brand_id, IFNULL(b.brand_name, '') AS brand_name, ".
	            	"g.shop_price, p.img_48_48 AS img_30_40, p.img_58_58 AS img_40_53,p.img_85_85 AS img_63_84,p.img_170_170 AS img_130_173, p.img_url, ".
	            	"g.promote_price, g.promote_start_date,g.promote_end_date, a.*" .
	            " FROM ".$this->_db->dbprefix('product_liuyan') . " as a ".
	    		" LEFT JOIN " . $this->_db->dbprefix('product_info') . " AS g ON g.product_id = a.tag_id ".
	            " LEFT JOIN " . $this->_db->dbprefix('product_brand') . " AS b ON g.brand_id = b.brand_id " .
	            " LEFT JOIN " . $this->_db->dbprefix('product_gallery') . " AS p ON g.product_id = p.product_id AND p.image_type = 'default' " .
	             $where ." ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

		$query = $this->_db->query($sql);
		$rows = $query->result();

			$product_list = array();
			$now_int = strtotime($now);
	        for($i=0; $i<count($rows); $i++) {
	            if ($rows[$i]->promote_price > 0 && strtotime($rows[$i]->promote_start_date) < $now_int && strtotime($rows[$i]->promote_end_date) > $now_int)
	            {
	                $promote_price = $rows[$i]->promote_price;
	            }
	            else {
	                $promote_price = 0;
	            }
	            $product_list[$rows[$i]->comment_id] = $rows[$i];
	            $product_list[$rows[$i]->comment_id]->short_product_name    = _smarty_modifier_truncate($rows[$i]->product_name,26,'...');
	            $product_list[$rows[$i]->comment_id]->small_url        = img_url($rows[$i]->img_url.".140x140.jpg");
	            $product_list[$rows[$i]->comment_id]->teeny_url        = img_url($rows[$i]->img_url.".85x85.jpg");
	            $product_list[$rows[$i]->comment_id]->market_price  = number_format($rows[$i]->market_price, 2, '.', '');
	            $product_list[$rows[$i]->comment_id]->shop_price    = number_format($rows[$i]->shop_price, 2, '.', '');
	            $product_list[$rows[$i]->comment_id]->promote_price = ($promote_price > 0) ? number_format($promote_price, 2, '.', '') : '';
	            $product_list[$rows[$i]->comment_id]->url           = '/product-'.$rows[$i]->product_id.'.html';
	        }
	    return $arr = array('list' => $product_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}

	public function voucher_list($filter,$user_id)
	{
		$now = date('Y-m-d H:i:s');
		$where = " WHERE user_id = '" . $user_id . "' ";
		if (!isset($filter['order_status']))
		{
			$filter['order_status'] = 1;
		}
		switch ($filter['order_status'])
		{
				case '1' : //可使用
					$where .= " AND UNIX_TIMESTAMP(v.end_date) > UNIX_TIMESTAMP('".$now."') AND v.used_number < v.repeat_number ";
					break;

				case '2' : //已使用
					$where .= " AND v.used_number > 0 ";
					break;

				case '3' : //已过期
					$where .= " AND UNIX_TIMESTAMP(v.end_date) <= UNIX_TIMESTAMP('".$now."') AND v.used_number < v.repeat_number ";
					break;

				default:
					$where .= " AND UNIX_TIMESTAMP(v.end_date) > UNIX_TIMESTAMP('".$now."') AND v.used_number < v.repeat_number ";
					break;
		}

		$filter['sort_by'] = 'v.create_date';
		$filter['sort_order'] = 'DESC';

		$sql = "SELECT COUNT(*) AS ct FROM ". $this->_db->dbprefix('voucher_record') . " as v ". $where;
		$query = $this->_db->query($sql);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

		/* 查询记录 */
	    $sql = "SELECT v.*,r.voucher_name,r.release_note,r.voucher_name as display_name FROM ".$this->_db->dbprefix('voucher_record') . " as v ".
	    		"LEFT JOIN " . $this->_db->dbprefix('voucher_release') . " as r ON v.release_id = r.release_id " .
				"LEFT JOIN " . $this->_db->dbprefix('voucher_campaign') . " as c ON c.campaign_id = r.campaign_id " .
	             $where ." ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

		$query = $this->_db->query($sql);
		$rows = $query->result();

		for($i=0; $i<count($rows); $i++) {
            $rows[$i]->start_end = ($rows[$i]->start_date != "0000-00-00 00:00:00"?$rows[$i]->start_date:'?')." 至 ".($rows[$i]->end_date!= "0000-00-00 00:00:00"?$rows[$i]->end_date:'?');
            $rows[$i]->voucher_amount = number_format($rows[$i]->voucher_amount, 2, '.', '');
            if($filter['order_status'] == 3) {
                $rows[$i]->voucher_status = '已过期';
            }
            else {
                $rows[$i]->voucher_status = ($rows[$i]->used_number==0) ? '未使用':(($rows[$i]->used_number>=$rows[$i]->repeat_number)?'已用完':'使用中');
            }

           $rows[$i]->short_voucher_name = _smarty_modifier_truncate($rows[$i]->voucher_name, 35, '...');
        }

		return $arr = array('list' => $rows, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    }

    public function dianping_list($filter,$user_id)
    {
		$sql = "SELECT COUNT(DISTINCT og.product_id) AS ct FROM ". $this->_db->dbprefix('order_product') . " as og, " .
				$this->_db->dbprefix('order_info')." AS o WHERE og.order_id = o.order_id AND o.shipping_status=1 AND o.user_id= '".$user_id."' ";
		$query = $this->_db->query($sql);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

        $sql = "SELECT g.product_id,g.product_name, MIN(o.shipping_date) AS order_shipping_date,gg.img_48_48 AS img_30_40,gg.img_170_170 AS img_130_173, gg.img_url, pb.brand_name, if(g.is_promote, g.promote_price, g.shop_price) as sale_price, og.product_num " .
        		" FROM ".$this->_db->dbprefix('order_product')." AS og " .
        		" LEFT JOIN ".$this->_db->dbprefix('order_info')." AS o ON og.order_id=o.order_id" .
        		" LEFT JOIN ".$this->_db->dbprefix('product_info')." AS g ON g.product_id=og.product_id".
        		" LEFT JOIN ".$this->_db->dbprefix('product_gallery')." AS gg ON gg.product_id = og.product_id AND gg.color_id = og.color_id AND gg.image_type='default'" .
                "LEFT JOIN ty_product_brand pb ON g.brand_id = pb.brand_id ".
                "WHERE  o.shipping_status=1 AND o.user_id= '".$user_id."' GROUP BY g.product_id ORDER BY order_shipping_date DESC LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ",".$filter['page_size']." ";
        $query = $this->_db->query($sql);
		$rows = $query->result();
        $product_list = array();
        $product_ids = array();

		foreach($rows as $row)
		{
            $row->url = '/product-'.$row->product_id.'.html';
			$row->tiny_url = img_url($row->img_url.".85x85.jpg");
			$row->small_url = img_url($row->img_url.".140x140.jpg");
            //$row->teeny_url = img_url($row->img_30_40);
			//$row->small_url = img_url($row->img_130_173);
            $product_list[$row->product_id] = $row;
            $product_ids[] = $row->product_id;
        }
        //附加点评数据 AND l.comment_type=2 
        $sql = "SELECT l.*,s.size_name, if(pi.is_promote, pi.promote_price, pi.shop_price) as sale_price, usl.pay_points, pb.brand_name FROM ".$this->_db->dbprefix('product_liuyan')." AS l
                LEFT JOIN ".$this->_db->dbprefix('product_size')." AS s ON l.size_id = s.size_id
                LEFT JOIN ty_product_info pi ON pi.product_id = l.tag_id
                LEFT JOIN ty_product_brand pb ON pi.brand_id = pb.brand_id
                LEFT JOIN ty_user_account_log usl ON usl.change_code = 'point_comment' AND usl.link_id = l.comment_id
                WHERE l.tag_type=1 AND l.is_del=0 AND l.user_id='".$user_id."' AND l.tag_id ".db_create_in($product_ids);
        $query = $this->_db->query($sql);
		$rows = $query->result();
		$product_list2 = array();
		foreach($rows as $row)
		{
			$row->product_id = $product_list[$row->tag_id]->product_id;
			$row->product_name = $product_list[$row->tag_id]->product_name;
			$row->order_shipping_date = $product_list[$row->tag_id]->order_shipping_date;
			$row->tiny_url = $product_list[$row->tag_id]->tiny_url;
			$row->small_url = $product_list[$row->tag_id]->small_url;
			$row->url = $product_list[$row->tag_id]->url;
			if (isset($product_list[$row->tag_id]))
				unset($product_list[$row->tag_id]);
			$product_list2[$row->tag_id] = $row;
        }
        $product_list = array_merge($product_list,$product_list2);
        return $arr = array('list' => $product_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
    }


    public function collection_list($filter,$user_id)
    {
    	$now = date('Y-m-d H:i:s');
		$where = " WHERE c.user_id = '" . $user_id . "' ";
		if (!isset($filter['order_status']))
		{
			$filter['order_status'] = 1;
		}
		switch ($filter['order_status'])
		{
				case '1' : //商品
					$where .= " AND c.product_type = 0 ";
					break;

				case '2' : //礼包
					$where .= " AND c.product_type = 1 ";
					break;

				default:
					$where .= " AND c.product_type = 0 ";
					break;
		}

		$filter['sort_by'] = 'c.create_date';
		$filter['sort_order'] = 'DESC';

		$sql = "SELECT COUNT(*) AS ct FROM ". $this->_db->dbprefix('front_collect_product') . " as c ". $where;
		$query = $this->_db->query($sql);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}

		if ($filter['order_status'] == 1)
		{
			/* 查询记录 */
	    	$sql = "SELECT DISTINCT g.product_id,g.product_sn, g.product_name, g.market_price, g.shop_price AS org_price, g.brand_id, IFNULL(b.brand_name, '') AS brand_name, ".
	            	"g.shop_price
                        , p.img_318_318, p.img_418_418, p.img_85_85, p.img_760_760, p.img_850_850, p.img_215_215, p.img_58_58, p.img_48_48, p.img_40_40, p.img_40_40 AS img_30_40, p.img_85_85 AS img_63_84, 
                        p.img_215_215 AS img_130_173, p.img_url, ".
	            	"g.promote_price, g.promote_start_date,g.promote_end_date, c.rec_id " .
	            	" FROM ".$this->_db->dbprefix('front_collect_product') . " as c ".
		    		" LEFT JOIN " . $this->_db->dbprefix('product_info') . " AS g ON g.product_id = c.product_id ".
	            	" LEFT JOIN " . $this->_db->dbprefix('product_brand') . " AS b ON g.brand_id = b.brand_id " .
	            	" LEFT JOIN " . $this->_db->dbprefix('product_gallery') . " AS p ON g.product_id = p.product_id AND p.image_type = 'default' " .
		             $where ." GROUP BY g.product_id ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
					. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

			$query = $this->_db->query($sql);
			$rows = $query->result();

			$product_list = array();
			$now_int = strtotime($now);
	        for($i=0; $i<count($rows); $i++) {
	            if ($rows[$i]->promote_price > 0 && strtotime($rows[$i]->promote_start_date) < $now_int && strtotime($rows[$i]->promote_end_date) > $now_int)
	            {
	                $promote_price = $rows[$i]->promote_price;
	            }
	            else {
	                $promote_price = 0;
	            }
	            $product_list[$rows[$i]->product_id] = $rows[$i];
	            $product_list[$rows[$i]->product_id]->short_product_name    = _smarty_modifier_truncate($rows[$i]->product_name,26,'...');
	            $product_list[$rows[$i]->product_id]->small_url        = img_url($rows[$i]->img_url.".85x85.jpg");
	            $product_list[$rows[$i]->product_id]->teeny_url        = img_url($rows[$i]->img_url.".85x85.jpg");
	            $product_list[$rows[$i]->product_id]->market_price  = number_format($rows[$i]->market_price, 2, '.', '');
	            $product_list[$rows[$i]->product_id]->shop_price    = number_format($rows[$i]->shop_price, 2, '.', '');
	            $product_list[$rows[$i]->product_id]->promote_price = ($promote_price > 0) ? number_format($promote_price, 2, '.', '') : '';
	            $product_list[$rows[$i]->product_id]->url = 'product-'.$rows[$i]->product_id.'.html';
	        }
		} else
		{
			/* 查询记录 */
	    	$sql = "SELECT g.package_id,g.package_name, g.package_amount,g.package_status, g.own_price AS org_price,g.package_image, c.rec_id " .
	            	" FROM ".$this->_db->dbprefix('front_collect_product') . " as c ".
		    		" LEFT JOIN " . $this->_db->dbprefix('package_info') . " AS g ON g.package_id = c.product_id ".
		             $where ." ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
					. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];

			$query = $this->_db->query($sql);
			$rows = $query->result();
			$product_list = array();
			for($i=0; $i<count($rows); $i++) {
				$product_list[$rows[$i]->product_id] = $rows[$i];
	            $product_list[$rows[$i]->package_id]->short_package_name    = _smarty_modifier_truncate($rows[$i]->package_name,26,'...');
	            $product_list[$rows[$i]->package_id]->small_url        = img_url('data/package/'.$rows[$i]->package_image);
	            $product_list[$rows[$i]->package_id]->teeny_url        = img_url('data/package/'.$rows[$i]->package_image);
	            $product_list[$rows[$i]->package_id]->formated_package_amount  = number_format($rows[$i]->package_amount, 2, '.', '');
	            $product_list[$rows[$i]->package_id]->url = 'package-'.$rows[$i]->package_id.'.html';
	        }
		}

		return $arr = array('list' => $product_list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}

	public function delete_collection($user_id, $rec_id)
    {
        $this->_db->delete('front_collect_product', array('rec_id' => $rec_id, 'user_id' => $user_id));
        return $this->_db->affected_rows();
    }

    public function delete_address($address_id)
    {
        $this->_db->delete('user_address', array('address_id' => $address_id));
        return $this->_db->affected_rows();
    }

    public function bind_user_voucher($user_id, $voucher_sn)
    {
        $this->_db->query('BEGIN');
        $sql = "SELECT * FROM " .$this->_db->dbprefix('voucher_record'). " WHERE user_id = '0' AND repeat_number = '1' AND voucher_sn = '$voucher_sn' FOR UPDATE";
        $this->_db->query($sql);

        $sql = "SELECT COUNT(*) as total FROM " .$this->_db->dbprefix('voucher_record') . " as v".
            " LEFT JOIN " . $this->_db->dbprefix('voucher_campaign') . " as a ON a.campaign_id = v.campaign_id " .
            " WHERE v.user_id = '0' AND v.repeat_number = '1' AND a.campaign_type <> 'repeatable'  AND v.voucher_sn = '$voucher_sn' ";
		$query = $this->_db->query($sql);
		$row = $query->row();

        if($row->total == 1) {
            $sql = "UPDATE " . $this->_db->dbprefix('voucher_record') . " SET user_id = '$user_id' WHERE user_id = '0' AND repeat_number = '1' AND voucher_sn = '$voucher_sn' ";
            $this->_db->query($sql);
            if($this->_db->affected_rows() == 1) {
                $this->_db->query('COMMIT');
                $result = array('error' => 0, 'msg' => '券号：'.$voucher_sn."添加成功");
            }
            else {
                $this->_db->query('ROLLBACK');
                $result = array('error' => 1, 'msg' => '券号：'.$voucher_sn."已被绑定，不可重复绑定");
            }
        }
        else {
            $this->_db->query('ROLLBACK');
            $result = array('error' => 1, 'msg' => '无效的券号：'.$voucher_sn);
        }
        return $result;
    }

    public function can_dianping($product_id,$user_id)
    {
        $sql = "select og.op_id from ".$this->_db->dbprefix('order_product')." as og
                    left join ".$this->_db->dbprefix('order_info')." as o on o.order_id = og.order_id
                    left join ".$this->_db->dbprefix('product_info')." as g on og.product_id = g.product_id
                    where o.shipping_status=1 and o.user_id='".$user_id."' and og.product_id='".$product_id."'
                    and not exists(select 1 from ".$this->_db->dbprefix('product_liuyan')." as l
                        where l.comment_type=2 and l.tag_type=1 and user_id = '".$user_id."' and l.tag_id='".$product_id."' and l.is_del=0 limit 1)
                    limit 1";
        $query = $this->_db->query($sql);
		$row = $query->row();
        $op_id = isset($row->op_id)?$row->op_id:'';
        return !empty($op_id);
    }

    public function dianping_info($product_id,$user_id) {

        //点评的颜色
        $sql = "select s.size_id,s.size_name,og.color_id from ".$this->_db->dbprefix('order_product')." as og
                left join ".$this->_db->dbprefix('order_info')." as o on o.order_id = og.order_id
                left join ".$this->_db->dbprefix('product_size')." as s on og.size_id = s.size_id
                where o.shipping_status=1 and o.user_id='".$user_id."' and og.product_id='".$product_id."'
                group by og.product_id,og.size_id";
        $query = $this->_db->query($sql);
		$result['size_arr'] = $query->result_array();

        //默认的身高
        $sql = "select height,weight from ".$this->_db->dbprefix('product_liuyan')."
                where user_id='".$user_id."' and comment_type=2 order by comment_id desc limit 1";
        $query = $this->_db->query($sql);
		$row = $query->row_array();
        if(!empty($row)){
            $result = array_merge($result,$row);
        }else{
            $result['height'] = $result['weight'] = '0.00';
        }
        //商品的图片
        $default_color_id = $result['size_arr'][0]['color_id'];
        $sql = "select img_url from ".$this->_db->dbprefix('product_gallery')." where product_id = '".$product_id."' and color_id='".$default_color_id."' and image_type='default' limit 1";
        $query = $this->_db->query($sql);
        $row = $query->row_array();
        $result['product_img'] = isset($row['img_url'])?$row['img_url'].".85x85.jpg":'';
        return $result;
    }

	public function point_type_exists($user_id, $point_kind, $n=1 )
	{
	    $sql = "SELECT count(user_id) AS num FROM ". $this->_db->dbprefix('user_account_log') .
	        "  WHERE user_id = '$user_id' AND change_code ='".$point_kind."'";
	    $query = $this->_db->query($sql);
	    $row = $query->row();
	    return ($row->num>=$n);
	}

	/**
	 * 记录帐户变动
	 * @param   int     $user_id        用户id
	 * @param   float   $user_money     可用余额变动
	 * @param   float   $frozen_money   冻结余额变动
	 * @param   int     $rank_points    等级积分变动
	 * @param   int     $pay_points     消费积分变动
	 * @param   string  $change_desc    变动说明
	 * @param   int     $change_type    变动类型：参见常量文件
	 * @return  void
	 */
	public function log_account_change($user_id, $user_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_code = 99, $link_id='')
	{
		if ($user_money != 0)
		{
			/* 插入帐户变动记录 */
		    $account_log = array(
		        'user_id'       => $user_id,
		        'user_money'    => $user_money,
		      //  'rank_points'   => 0,
		        'pay_points'    => 0,
		        'create_date'   => date('Y-m-d H:i:s'),
		        'change_desc'   => $change_desc,
		        'change_code'   => $change_code,
				'link_id'		=>$link_id
		    );
			$this->insert_account($account_log);

		    /* 更新用户信息 */
		    $sql = "UPDATE " . $this->_db->dbprefix('user_info') .
		            " SET user_money = user_money + ('$user_money')," .
		       //     " rank_points = rank_points + ('0')," .
		            " pay_points = pay_points + ('0')" .
		            " WHERE user_id = '$user_id' LIMIT 1";
		    $this->_db->query($sql);
		}
		if($rank_points != 0 || $pay_points != 0)
		{
			if($change_code == 'change_account') $change_type = 'point_balance';
			/* 插入帐户变动记录 */
		    $account_log = array(
		        'user_id'       => $user_id,
		        'user_money'    => 0,
		       // 'rank_points'   => $rank_points,
		        'pay_points'    => $pay_points,
		        'create_date'   => date('Y-m-d H:i:s'),
		        'change_desc'   => $change_desc,
		        'change_code'   => $change_code,
				'link_id'		=>$link_id
		    );
		    $this->insert_account($account_log);

		    /* 更新用户信息 */
		    $sql = "UPDATE " . $this->_db->dbprefix('user_info') .
		            " SET user_money = user_money + ('0')," .
		           // " rank_points = rank_points + ('$rank_points')," .
		            " pay_points = pay_points + ('$pay_points')" .
		            " WHERE user_id = '$user_id' LIMIT 1";
		    $this->_db->query($sql);
		}
	}

	public function online_message($session_id,$user_id)
	{
		$sql = "SELECT a.*,b.session_id,b.status,b.user_close FROM ty_online_support_sub a LEFT JOIN ty_online_support_main b ON a.rec_id = b.rec_id WHERE b.session_id = '".$session_id."' ORDER BY a.message_id DESC LIMIT 10";
		$query = $this->_db->query($sql);
		$rs = $query->result_array();
		return $rs;
	}

	public function online_admin()
	{
		$sql = "SELECT COUNT(*) as ct FROM ty_admin_info WHERE is_online = 1";
		$query = $this->_db->query($sql);
		$rs = $query->row();
		return $rs->ct > 0?TRUE:FALSE;
	}

	public function add_online_msg($user_id,$session_id,$value,$cur_rec)
	{
		$sql = "SELECT * FROM ty_online_support_main WHERE session_id = '".$session_id."' ORDER BY rec_id DESC LIMIT 1";
		$query = $this->_db->query($sql);
		$row = $query->row();
		if (!empty($row))
		{
			$rec_id = $row->rec_id;
			$this->_db->query("UPDATE ty_online_support_main SET status = 0 WHERE status = 2 AND rec_id = '".$rec_id."'");
			$this->_db->query("UPDATE ty_online_support_main SET user_close = 0 WHERE user_close = 1 AND rec_id = '".$rec_id."'");
		} else
		{
			$this->_db->insert('online_support_main', array('user_id'=>$user_id,'session_id'=>$session_id,'create_date'=>date('Y-m-d H:i:s'),'status'=>0,'ipaddress'=>real_ip()));
        	$rec_id = $this->_db->insert_id();
		}
		if ($rec_id > 0)
		{
			$this->_db->insert('online_support_sub', array('rec_id'=>$rec_id,'content'=>$value,'qora'=>0,'user_id'=>$user_id,'create_date'=>date('Y-m-d H:i:s')));
			$message_id = $this->_db->insert_id();

			if (empty($message_id))
			{
				return false;
			}
			$sql = "SELECT * FROM ty_online_support_sub WHERE message_id = '".$message_id."'";
			$query = $this->_db->query($sql);
			$rs = $query->row();

			if (empty($rs) || $rs->content != $value)
			{
				return false;
			} else
			{
				return $rs;
			}
		} else
		{
			return false;
		}
	}

	public function reply_msg($user_id,$session_id,$cur_rec=0)
	{
		if (empty($cur_rec))
		{
			$cur_rec = 0;
		}
		$sql = "SELECT * FROM ty_online_support_main WHERE session_id = '".$session_id."' ORDER BY rec_id DESC LIMIT 1";
		$query = $this->_db->query($sql);
		$row = $query->row();
		if (!empty($row))
		{
			$rec_id = $row->rec_id;
			$sql = "SELECT * FROM ty_online_support_sub WHERE rec_id = '".$rec_id."' AND message_id > ".$cur_rec." AND qora = 1 ORDER BY message_id ASC LIMIT 1";
			$query = $this->_db->query($sql);
			$rs = $query->row();
			if (empty($rs))
			{
				return false;
			} else
			{
				return $rs;
			}
		} else
		{
			return false;
		}
	}

	public function all_msg($user_id,$session_id,$page=0)
	{
		if ($page > 1 == false)
			$page = 1;

		$sql = "SELECT * FROM ty_online_support_main WHERE session_id = '".$session_id."' ORDER BY rec_id DESC LIMIT 1";
		$query = $this->_db->query($sql);
		$row = $query->row();
		if (!empty($row))
		{
			$rec_id = $row->rec_id;
			$sql = "SELECT COUNT(*) as ct FROM ty_online_support_sub WHERE rec_id = '".$rec_id."'";
			$query = $this->_db->query($sql);
			$trow = $query->row();
			$ct = (int) $trow->ct;
			if ($ct <= 0)
			{
				return array('list'=>array(),'total'=>1,'cur'=>1);
			}
			$page_count = max(ceil($ct / 10), 1);
			if ($page > $page_count) $page = $page_count;

			$sql = "SELECT * FROM ty_online_support_sub WHERE rec_id = '".$rec_id."' ORDER BY message_id ASC LIMIT ".(($page-1)*10).", 10";
			$query = $this->_db->query($sql);
			$rs = $query->result_array();
			return array('list'=>$rs,'total'=>$page_count,'cur'=>$page);

		} else
		{
			return array('list'=>array(),'total'=>1,'cur'=>1);
		}

	}

	public function msg_close($user_id,$session_id)
	{
		$this->_db->query("UPDATE ty_online_support_main SET user_close = 1 WHERE session_id = '".$session_id."'");
	}

	public function need_verify($mobile,$day)
	{
		$sql = "SELECT COUNT(*) AS row FROM ty_sms_log WHERE sms_to = '".$mobile."' AND TO_DAYS(create_date) = TO_DAYS('".$day."')";
		$query = $this->_db->query($sql);
		$row = $query->row();
		return $row->row>=3 ? TRUE:FALSE;
	}

        public function create_recharge($user_id, $amount) {
            $sql = "INSERT INTO 
                    ty_user_recharge
                    (user_id, amount, is_paid, paid_date, admin_note, user_note, pay_id, is_audit, audit_admin, audit_date, create_admin, create_date, is_del, del_admin, del_date)
                    VALUES
                    (?, ?, 0, '0000-00-00 00:00:00', '', '支付宝充值', 4, 0, 0, '0000-00-00 00:00:00', -1, now(), 0, 0, '0000-00-00 00:00:00');";
            $this->db->query($sql, array($user_id, $amount));
            return $this->db->insert_id();
        }
        public function get_recharge($recharge_id, $user_id) {
            $sql = "SELECT * FROM ty_user_recharge WHERE recharge_id = ? AND user_id = ?;";
            return $this->db->query($sql, array($recharge_id, $user_id))->row_array();
        }
        public function recharge_success($recharge_id) {
            $sql = "SELECT user_id, amount, is_paid FROM ty_user_recharge WHERE recharge_id = ?";
            $row = $this->db->query($sql, array($recharge_id))->row_array();
            if (empty($row)) {
                return false;
            }
            if ($row["is_paid"] == 1) {
                return true;
            }
            $this->db->trans_begin();
            $sql = "UPDATE ty_user_recharge SET is_paid = 1, paid_date = now() WHERE recharge_id = ? ";
            $this->db->query($sql, array($recharge_id));
            if ($this->db->affected_rows() < 1) {
                $this->db->trans_rollback();
                return false;
            }
            $sql = "INSERT INTO 
                    ty_user_account_log
                    (link_id, user_id, user_money, pay_points, change_desc, change_code, create_admin, create_date)
                    VALUES
                    (?, ?, ?, 0, '支付宝充值', 'recharge', 0, now());";
            $this->db->query($sql, array($recharge_id, $row["user_id"], $row["amount"]));
            if ($this->db->affected_rows() < 1) {
                $this->db->trans_rollback();
                return false;
            }
            $sql = "UPDATE ty_user_info SET user_money = user_money + ? WHERE user_id = ?";
            $this->db->query($sql, array($row["amount"], $row["user_id"]));
            if ($this->db->affected_rows() < 1) {
                $this->db->trans_rollback();
                return false;
            }
            $this->db->trans_commit();
            return true;
        }
        public function add_register_email($mail_from, $mail_to, $template_subject, $template_content) {
            $sql = <<<SQL
INSERT INTO ty_mail_log 
(mail_from, mail_to, template_id, template_subject, template_content, template_priority, create_admin, create_date, send_date, status)
VALUES
(?, ?, 1, ?, ?, 10, -1, now(), '0000-00-00 00:00:00', 0);
SQL;
            $this->db->query($sql, array($mail_from, $mail_to, $template_subject, $template_content));
        }
		
		public function get_user_baby_list($user_id) {
		    $sql = "SELECT baby_name, baby_sex, birthday AS baby_birthday FROM ty_user_baby_info WHERE user_id = ".$user_id;
			$query = $this->db->query($sql);
			return $query->result_array();
		}
		
		public function add_user_baby_info($data) {
            $this->_db->insert('user_baby_info', $data);
            return $this->_db->insert_id();		
		}

		//获取 用户 所收藏的 商品信息
		public function get_collect_info ($filter){
			$query = $this->_db->get_where('front_collect_product',$filter);
			return $query->result_array();
		}
        //获取最后一个user_id的值        
        public function get_last_user_id(){
            $sql = "SELECT user_id FROM ty_user_info ORDER BY user_id DESC LIMIT 1";
            $result = $this->db->query($sql)->row();
            return !empty($result) ? $result->user_id : 0;
        }
}                
