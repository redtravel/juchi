<?php
#doc
#	classname:	Admin_model
#	scope:		PUBLIC
#
#/doc

class Admin_model extends CI_Model
{
	
	public function filter ($filter)
	{
		$query = $this->db->get_where('admin_info', $filter, 1);
		return $query->row();
	}

    public function all_admin ($filter = array())
	{
		if(isset($filter['admin_id']) && is_array($filter['admin_id'])){
			$this->db->where_in('admin_id', $filter['admin_id']);
			unset($filter['admin_id']);
		}
                
                if (isset($filter['user_status'])) {
                    $this->db->where('user_status', intval($filter['user_status']));
                }
		
		$query = $this->db->get_where('admin_info', $filter);
                $arr = $query->result();
                $res = array();
                foreach($arr as $item){
                    $res[$item->admin_id] = $item;
                }
		return $res;
	}
	
	public function update ($data, $admin_id)
	{
		$this->db->update('admin_info', $data, array('admin_id' => $admin_id));
	}
	
	public function insert ($data)
	{
		$this->db->insert('admin_info', $data);
		return $this->db->insert_id();
	}
	
	public function admin_list ($filter)
	{
		$from = " FROM ".$this->db->dbprefix('admin_info')." AS a ";
		$where = " WHERE 1 ";
		$param = array();

		if (!empty($filter['admin_name']))
		{
			$where .= " AND a.admin_name LIKE ? ";
			$param[] = '%' . $filter['admin_name'] . '%';
		}
		
		$filter['sort_by'] = empty($filter['sort_by']) ? 'a.admin_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'ASC' : trim($filter['sort_order']);

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
		$sql = "SELECT a.* "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function all_action ($filter=array())
	{
		$this->db->order_by('sort_order', 'asc');
		$query = $this->db->get_where('admin_action',$filter);
		return $query->result();
	}

	public function filter_action($filter)
	{
		$query = $this->db->get_where('admin_action', $filter, 1);
		return $query->row();
	}
        public function check_user($email, $password = null) {

        if (strpos($email, '@') !== false)
            $field = 'email';
        else
            $field = 'mobile';

//        if ($this->charset != 'UTF8') {
//            $post_email = ecs_iconv('UTF8', $this->charset, $email);
//        } else {
//            $post_email = $email;
//        }

        if ($password === null) {
            $sql = "SELECT user_id" .
                    " FROM ty_user_info" .
                    " WHERE " . $field . "='" . $email . "'";
//            echo $sql;
            $query = $this->db_r->query($sql);
            $list = $query->result();
            $query->free_result();
            return @$list[0]->user_id;
        }
    }

    /**
     * 批量获得账号金额
     *
     * @access public
     * @param array ( userIds )
     * @return Array( userId=> money )
     */
    public function get_accounts_money($userIds) {
        $sql = "SELECT user_id, user_money FROM ty_user_info WHERE user_id =$userIds";
//        $result = $GLOBALS['db']->getAll($sql);
//        return $result;
        $query = $this->db_r->query($sql);
        $list = $query->row_array(); 
        return $list;
    }
    public function trans_minus($userIds_str,$accounts_minus,$flag) {
        $this->db_r->query('BEGIN');
        // LOCK user's money TO BE MINUS
        $this->db_r->query("select user_id, user_money from ty_user_info where user_id in ('" . $userIds_str . "') for update");
//        echo "select user_id, user_money from ty_user_info where user_id in ('" . $userIds_str . "') for update";
//        exit;
//        var_dump($accounts_minus);
        $account_minus_result = $this->check_account_minus($accounts_minus,$flag);

        $this->do_op_account_money($account_minus_result[0], $flag);
        $this->db_r->query('COMMIT');
        return $account_minus_result;

//        $GLOBALS['db']->query('COMMIT');
    }

    /**
     * 批量检查用户账号资金的可<b>减</b>性
     *
     * @access public
     * @param array 用户登录账号 Array ( user_id=>待减金额 , .... );
     * @return Array( Array(user_id=>待减金额,... ), Array(user_id=>待减金额, ... ) ); array[0]=可减， array[1]=不可减
     */
    function check_account_minus($account_minus = Array(),$flag="-") {
        $result = Array(Array(), Array());
        if (empty($account_minus))
            return $result;
        $userIds = Array_keys($account_minus);
        // userId=>money
//        $userIds_str = implode(',', $userIds);
        foreach ($userIds as $value) {
                $account_moneys[] = $this->get_accounts_money($value);
            }
//            var_dump($account_moneys);
//            echo "<br/>";
//        $account_moneys = $this->get_accounts_money($userIds_str);
        foreach ($account_moneys AS $account) {
            $userId = $account['user_id'];
            if ($flag=="+"||$account_minus[$userId] <= $account['user_money'])
                $result[0][$userId] = $account_minus[$userId];
            else
                $result[1][$userId] = $account_minus[$userId];
        }
        return $result;
    }

    /**
     * 批量减账户金额
     *
     * @access public
     * @param Array( $userId=> MONEY, ... );
     *
     * @return TRUE
     */
    public function do_op_account_money($account_op = Array(), $op = '', $reason = '批量操作用户金额') {
        if (empty($account_op)){
        return true;}
        $reason = 'admin_id=' . $this->session->userdata('admin_id') . '：' . $reason;
        foreach ($account_op AS $userId => $money) {
            $this->log_account_change($userId, $op . $money, 0, 0, 0, $reason, ACT_ADJUSTING);
        }
        return true;
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
    function log_account_change($user_id, $user_money = 0, $frozen_money = 0, $rank_points = 0, $pay_points = 0, $change_desc = '', $change_type = ACT_ADJUSTING, $link_id = '', $time_type = 0) {
        if ($time_type == 1) {
            $change_time = '';
        } else {
            $change_time = time();
        }
        if ($user_money != 0 || $frozen_money != 0) {
            /* 插入帐户变动记录 */
            $account_log = array(
                'user_id' => $user_id,
                'user_money' => $user_money,
                'change_desc' => $change_desc,
                'create_admin' => $this->session->userdata('admin_id'),
                'create_date' => date('Y-m-d H:i:s'),
                'change_code' =>$change_type
            );
            $this->db->insert('ty_user_account_log', $account_log);
//            $log_id = $this->db->insert_id();
//            echo $this->db->last_query();
            /* 更新用户信息 */
            $sql = "UPDATE ty_user_info SET user_money = user_money + ($user_money)" .
                    " WHERE user_id = '$user_id' LIMIT 1";
            $this->db_r->query($sql);
        }
        return $log_id;
    }
}
###
