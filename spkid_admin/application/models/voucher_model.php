<?php
/**
* 
*/
class Voucher_model extends CI_Model
{
	
	public function filter($filter)
	{
		$query = $this->db->get_where('voucher_record', $filter, 1);
		return $query->row();
	}

	public function lock_voucher($voucher_sn)
	{
		$sql = "SELECT * FROM ".$this->db->dbprefix('voucher_record')." WHERE voucher_sn = ? LIMIT 1 FOR UPDATE";
		$query = $this->db->query($sql,array($voucher_sn));
		return $query->row();
	}

	public function insert($update)
	{
		$this->db->insert('voucher_record', $update);
		return $this->db->insert_id();
	}

	public function delete($voucher_id)
	{
		$this->db->delete('voucher_record', array('voucher_id'=>$voucher_id));
	}

	public function update($update, $voucher_id)
	{
		$this->db->update('voucher_record', $update, array('voucher_id'=>$voucher_id));
	}

	public function all_voucher($filter)
	{
		if(isset($filter['voucher_sn']) && is_array($filter['voucher_sn'])){
			$this->db->where_in('voucher_sn', $filter['voucher_sn']);
			unset($filter['voucher_sn']);
		}	
		$query = $this->db->get_where('voucher_record', $filter);
		return $query->result();
	}

	public function voucher_list($filter)
	{
		$CI = &get_instance();

		$from = " FROM ".$this->db->dbprefix('voucher_record')." AS v
				LEFT JOIN ".$this->db->dbprefix('voucher_release')." AS r ON v.release_id = r.release_id 
				LEFT JOIN ".$this->db->dbprefix('voucher_campaign')." AS c ON v.campaign_id = c.campaign_id ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['campaign_name']))
		{
			$where .= " AND c.campaign_name LIKE ? ";
			$param[] = '%' . $filter['campaign_name'] . '%';
		}

		if (!empty($filter['campaign_type']))
		{
			$where .= " AND c.campaign_type = ? ";
			$param[] = $filter['campaign_type'];
		}

		if (!empty($filter['voucher_name']))
		{
			$where .= " AND r.voucher_name LIKE ? ";
			$param[] = '%' . $filter['voucher_name'] . '%';
		}

		if (!empty($filter['voucher_sn']))
		{
			$where .= " AND v.voucher_sn LIKE ? ";
			$param[] = '%' . $filter['voucher_sn'] . '%';
		}

		if (!empty($filter['release_id']))
		{
			$where .= " AND v.release_id = ? ";
			$param[] = $filter['release_id'];
		}

		if (!empty($filter['voucher_status']))
		{
			switch ($filter['voucher_status']) {
				case '1':
					$where .= " AND v.used_number = 0 ";
					break;
				
				case '2':
					$where .= " AND v.used_number > 0 AND v.used_number < v.repeat_number ";
					break;

				case '3':
					$where .= " AND v.used_number = v.repeat_number ";
					break;
				
				default:
					
					break;
			}
		}

		if(!empty($filter['user_name'])){
			$this->db->where('email',$filter['user_name'])->or_where('mobile',$filter['user_name']);
			$query = $this->db->get('user_info',1);
			$user = $query->row();

			if($user){
				$where .= " AND v.user_id = ? ";
				$param[] = $user->user_id;
			}else{
				$where .= " AND 0 ";
				
			}
		}


		$filter['sort_by'] = empty($filter['sort_by']) ? 'v.voucher_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

		$sql = "SELECT COUNT(*) AS ct " . $from . $where;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT v.*, c.campaign_name, c.campaign_type, r.voucher_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}



	// campaign function
	public function filter_campaign($filter)
	{
		$query = $this->db->get_where('voucher_campaign', $filter, 1);
		return $query->row();
	}

	/**
	* 锁表
	* @param $campaign_id mix(int|array) 主键
	* @param $return 是否返回数值
	* @auth tony.liu
	*/ 
	public function lock_campaign($campaign_id, $return=TRUE)
	{
		$select = $return ? "SELECT * " : "SELECT 1 ";
		$where = is_array($campaign_id) ? ('voucher_id '.db_create_in($campaign_id)) : ('campaign_id='.intval($campaign_id));
		$sql = $select . "FROM ".$this->db->dbprefix('voucher_campaign')." WHERE " . $where . " FOR UPDATE";
		$query = $this->db->query($sql);
		return is_array($campaign_id) ? $query->result() : $query->row();
	}

	public function insert_campaign($update)
	{
		$this->db->insert('voucher_campaign', $update);
		return $this->db->insert_id();
	}

	public function update_campaign($update, $campaign_id)
	{
		$this->db->update('voucher_campaign', $update, array('campaign_id'=>$campaign_id));
	}

	public function delete_campaign($campaign_id)
	{
		$this->db->delete('voucher_campaign', array('campaign_id'=>$campaign_id));
	}

	public function campaign_list($filter)
	{
		$from = " FROM ".$this->db->dbprefix('voucher_campaign')." AS c ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['campaign_name']))
		{
			$where .= " AND c.campaign_name LIKE ? ";
			$param[] = '%' . $filter['campaign_name'] . '%';
		}

		if (!empty($filter['campaign_type']))
		{
			$where .= " AND c.campaign_type = ? ";
			$param[] = $filter['campaign_type'];
		}

		if (isset($filter['campaign_status']) && $filter['campaign_status']!=-1) {
			$where .= " AND c.campaign_status = ? ";
			$param[] = $filter['campaign_status'];
		}


		if (!empty($filter['start_time']))
		{
			$where .= " AND c.audit_date >= ? ";
			$param[] = $filter['start_time'];
		}

		if (!empty($filter['end_time']))
		{
			$where .= " AND c.audit_date <= ? ";
			$param[] = $filter['end_time'];
		}


		$filter['sort_by'] = empty($filter['sort_by']) ? 'c.campaign_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

		$sql = "SELECT COUNT(*) AS ct " . $from . $where;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT c.* "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	// release function

	public function filter_release($filter)
	{
		$query = $this->db->get_where('voucher_release', $filter, 1);
		return $query->row();
	}

	public function lock_release($release_id)
	{
		$query = $this->db->query("SELECT * FROM ".$this->db->dbprefix('voucher_release')." WHERE release_id = ?  FOR UPDATE", array($release_id));
		return $query->row();
	}

	public function insert_release($update)
	{
		$this->db->insert('voucher_release', $update);
		return $this->db->insert_id();
	}

	public function update_release($update, $release_id)
	{
		$this->db->update('voucher_release', $update, array('release_id'=>$release_id));
	}

	public function delete_release($release_id)
	{
		$this->db->delete('voucher_release', array('release_id'=>$release_id));
	}

	public function delete_release_where($filter)
	{
		$this->db->delete('voucher_release', $filter);
	}

	public function all_release($filter)
	{
		$query = $this->db->get_where('voucher_release', $filter);
		return $query->result();
	}

	public function product_search($filter)
	{
		$from = " FROM ".$this->db->dbprefix('product_info')." AS p 
				LEFT JOIN ".$this->db->dbprefix('product_category')." AS c ON p.category_id = c.category_id
				LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['product_ids']))
		{
			$where .= " AND p.product_id NOT ".db_create_in($filter['product_ids']);
		}

		if (!empty($filter['product_sn']))
		{
			$where .= " AND p.product_sn LIKE ? ";
			$param[] = '%' . $filter['product_sn'] . '%';
		}

		if (!empty($filter['category_id']))
		{
			$where .= " AND p.category_id = ? ";
			$param[] = $filter['category_id'];
		}

		if (!empty($filter['brand_id']))
		{
			$where .= " AND p.brand_id = ? ";
			$param[] = $filter['brand_id'];
		}

		if (!empty($filter['min_price']))
		{
			$where .= " AND p.shop_price >= ? ";
			$param[] = $filter['min_price'];
		}

		if (!empty($filter['max_price']))
		{
			$where .= " AND p.shop_price <= ? ";
			$param[] = $filter['max_price'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'p.product_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

		$sql = "SELECT COUNT(*) AS ct " . $from . $where;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT p.product_id,p.product_sn,p.product_name,p.provider_productcode,p.shop_price,
				c.category_name, b.brand_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function product_list($ids = array())
	{
		if(!is_array($ids)) $ids = explode(',',$ids);
		$sql = " SELECT p.product_id,p.product_sn,p.product_name,p.provider_productcode,p.shop_price,
				c.category_name, b.brand_name
				FROM ".$this->db->dbprefix('product_info')." AS p 
				LEFT JOIN ".$this->db->dbprefix('product_category')." AS c ON p.category_id = c.category_id
				LEFT JOIN ".$this->db->dbprefix('product_brand')." AS b ON p.brand_id = b.brand_id 
				WHERE p.product_id ".db_create_in($ids);
		$query = $this->db->query($sql);
		return $query->result();
	}

	function get_user_by_rule($filter)
	{
		$sql = "SELECT user_id FROM ".$this->db->dbprefix('user_info'). " AS u WHERE 1 ";
		$param = array();
		if (!empty($filter['rule_reg_date_min'])) {
			$sql .= " AND u.create_date >= ? ";
			$param[] = $filter['rule_reg_date_min'];
		}

		if (!empty($filter['rule_reg_date_max'])) {
			$sql .= " AND u.create_date <= ? ";
			$param[] = $filter['rule_reg_date_max'];
		}

		$query = $this->db->query($sql,$param);
		return $query->result();
	}

	public function do_release($release, $config)
	{
            if($config['sys']) return 0;

            $CI = & get_instance();
            $release_id = $release->release_id;
            if ($config['sys']) {
                    $start_date = $CI->time;
                    $end_date = date('Y-m-d H:i:s', strtotime($start_date) + $release->expire_days*86400);
            } else {
                    $start_date = $release->start_date;
                    $end_date = $release->end_date;
            }
            $user_arr = array();
            $release->release_rules = unserialize($release->release_rules);
            switch ($release->release_rules['rule']) {
                    case 'number':
                            $voucher_count = $release->release_rules['rule_number'];
                            break;

                    case 'list':
                            $user_arr = explode(',',$release->release_rules['rule_list']);
                            $user_arr = $this->filter_real_user_ids($user_arr);
                            $voucher_count = count($user_arr);
                            break; 

                     case 'sn':
                            $sn_arr = explode(',',$release->release_rules['rule_sn']);
                            $voucher_count = count($sn_arr);
                            break;

                     case 'rule':
                            $user_arr = array_keys(index_array($this->get_user_by_rule($release->release_rules), 'user_id'));
                            $voucher_count = count($user_arr);
                            break;

                    default:
                            sys_msg('请指定正确的发放规则!',1);
                            break;
            }
            if ($voucher_count<1) sys_msg('现金券的发放数量为0，请检查发放设置!',1);
            if ($voucher_count>599999) sys_msg('一次最多生成599,999张现金券。',1);

            switch ($release->release_rules['rule']) {
                case 'sn':
                    $rs = $this->all_voucher(array('voucher_sn'=>$sn_arr));
                    if($rs) {
                            $sns = implode(',',array_keys(index_array($rs,'voucher_sn')));
                            sys_msg("现金券券号 $sns 有重复，请修改!", 1);
                    }
                    $sql = "INSERT INTO ".$this->db->dbprefix('voucher_record')."
                    (campaign_id,release_id,voucher_sn) VALUES";
                    foreach($sn_arr as $voucher_sn) {
                        $sql .= "({$release->campaign_id},{$release->release_id},'{$voucher_sn}'),";
                    }
                    $sql = substr($sql,0,-1);
                    if(!$this->db->query($sql)) {
                        $this->db->trans_rollback();
                        sys_msg("发放失败，数据库错误！",1);
                    }
                    break;			
                default:
                    $num_per = 10000;
                    $times = ceil($voucher_count/$num_per); 
                    @set_time_limit(0);                
                    for($i=0;$i<$times;$i++) {                  
                        $start = $num_per*$i;
                        $i_count = $num_per*($i+1)>$voucher_count? $voucher_count-$num_per*$i : $num_per;
                        $sql = "INSERT INTO ".$this->db->dbprefix('voucher_record')."
                                (campaign_id,release_id,user_id,voucher_sn) VALUES";
                        $voucher_rs = array();
                        $voucher_arr = array();
                        $t_sql = "SELECT * FROM ty_voucher_log WHERE voucher_status = 0 ORDER BY voucher_id ASC LIMIT ".$i_count;
                        $result = $this->db->query($t_sql)->result_array();
                        foreach ($result as $row){ 
                            $voucher_rs[] = $row;
                            $voucher_arr[] = $row['voucher_des'];
                        }
                        if(count($voucher_rs) < $i_count) sys_msg("发放失败，现金券临时表中数据不够用，请10分钟后重试！:".count($voucher_rs),1);
                        $start_arr = reset($voucher_rs);
                        $start_id = $start_arr['voucher_id'];
                        $end_arr = end($voucher_rs);
                        $end_id = $end_arr['voucher_id'];       
                        $vkey = 0;
                        for($j=$start;$j<$start+$i_count;$j++) {
                            if(isset($user_arr[$j])) {
                                $user_id = $user_arr[$j];
                            }else {
                                $user_id = 0;
                            }
                            if($voucher_arr[$vkey]) {
                                $voucher_des = $voucher_arr[$vkey];
                                $vkey = $vkey + 1;
                            }else {
                                $voucher_des = getVoucherDes();
                            }
                            $sql .= "({$release->campaign_id},{$release->release_id},$user_id,'".$voucher_des."'),";
                        }
                        $sql = substr($sql,0,-1);
                        if(!$this->db->query($sql)) {
                            $this->db->trans_rollback();
                            sys_msg("发放失败，数据库错误！",1);
                        } else {
                            $affectedRows = $this->db->affected_rows();
                            if ($affectedRows < $i_count) {
                                $this->db->query("ROLLBACK");
                                sys_msg("发放失败，现金券临时表中数据不够用，请10分钟后重试!！".$affectedRows .':'. $i_count,1);
                            }
                        }

                        $vsql = "UPDATE ty_voucher_log SET edit_time = NOW(), voucher_status = 1 WHERE voucher_id >= $start_id AND voucher_id <= $end_id";
                        if(!$this->db->query($vsql)) {
                            $this->db->query("ROLLBACK");
                            sys_msg("发放失败，数据库错误3！",1);
                        }
                    }
      
                    break;
            }

            $sql = "UPDATE ".$this->db->dbprefix('voucher_record')." SET
            start_date = '$start_date',
            end_date = '$end_date',
            repeat_number = $release->repeat_number,
            voucher_amount = $release->voucher_amount,
            min_order = $release->min_order,
            create_date = '{$CI->time}',
            create_admin = {$CI->admin_id}
            where release_id = $release_id;";

            if(!$this->db->query($sql)) {
                 $this->db->trans_rollback();
                 sys_msg("发放失败，数据库错误！",1);
             }
     	
            return $voucher_count;
	}
	

	public function back_release($release_id)
	{
		$sql = "DELETE from ".$this->db->dbprefix('voucher_record')."
                where used_number = 0 AND release_id = ? ";
        $this->db->query($sql, array($release_id));

	    $sql = "SELECT COUNT(*) AS ct FROM ".$this->db->dbprefix('voucher_record')." WHERE release_id = ?";
	    $query = $this->db->query($sql, array($release_id));
	    $row = $query->row();
	    return intval($row->ct);
	}

	public function filter_real_user_ids($user_arr)
	{
		$sql = "SELECT user_id FROM ".$this->db->dbprefix('user_info')." WHERE user_id ".db_create_in($user_arr);
		$query = $this->db->query($sql);
		$result = array();
		foreach($query->result() as $user) $result[] = $user->user_id;
		return $result;
	}

	public function all_available_voucher($filter)
	{
		$now = date('Y-m-d H:i:s');
		$sql = "SELECT v.*, r.voucher_name FROM ".$this->db->dbprefix('voucher_record')." AS v
				LEFT JOIN ".$this->db->dbprefix('voucher_release')." AS r ON v.release_id = r.release_id
				WHERE v.used_number < v.repeat_number AND v.start_date < '{$now}' AND v.end_date > '{$now}' ";
		$param = array();
		if (isset($filter['user_id'])) {
			$sql .= " AND v.user_id = ? ";
			$param[] = intval($filter['user_id']);
		}
		$query = $this->db->query($sql, $param);
		return $query->result();
	}

        /*
         * 查询某一批次发放的所有现金券列表信息，供导出之用。
         */
        public function release_voucher_list($release_id) 
        {
                $sql = " SELECT "
                      ." v.voucher_id, v.voucher_sn, v.voucher_amount, r.voucher_name, v.create_date, "
                      ." v.used_number, v.repeat_number, "
                      ." c.campaign_name, c.campaign_type, "
                      ." u.email, u.mobile, "
                      ." v.min_order, v.start_date, v.end_date "
                      ." FROM ".$this->db->dbprefix('voucher_record')." AS v "
                      ." LEFT JOIN ".$this->db->dbprefix('voucher_release')." AS r ON v.release_id = r.release_id "
                      ." LEFT JOIN ".$this->db->dbprefix('voucher_campaign')." AS c ON v.campaign_id = c.campaign_id "
                      ." LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON u.user_id = v.user_id "
                      ." WHERE v.release_id = ".$release_id;
            
                $query = $this->db->query($sql);
                $result = $query->result_array();
                $query->free_result();

                return $result;
        }
        
}
