<?php 

/**
* Support Model
*/
class Support_model extends CI_Model
{
	public function filter($filter)
	{
		$query=$this->db->get_where('online_support_main',$filter,1);
		return $query->row();
	}

	public function update($data,$rec_id)
	{
		$this->db->update('online_support_main',$data,array('rec_id'=>$rec_id));
	}

	public function update_where($data,$filter)
	{
		$this->db->update('online_support_main',$data,$filter);
	}

	public function insert_message($data)
	{
		$this->db->insert('online_support_sub',$data);
		return $this->db->insert_id();
	}

	public function my_issue_list($admin_id)
	{
		$sql="(SELECT m.*,u.user_name,a.admin_name
			FROM ".$this->db->dbprefix('online_support_main')." AS m
			LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON m.user_id=u.user_id
			LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON m.admin_id=a.admin_id
			WHERE m.status=1 AND m.admin_id=?)
			UNION
			(SELECT m.*,u.user_name,a.admin_name
			FROM ".$this->db->dbprefix('online_support_main')." AS m
			LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON m.user_id=u.user_id
			LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON m.admin_id=a.admin_id
			WHERE m.status=0 
			ORDER BY rec_id ASC LIMIT 20)";
		$query=$this->db->query($sql,array($admin_id));
		return $query->result();
	}

	public function recent_message($rec_id)
	{
		$sql="SELECT s.*,u.user_name,a.admin_name
			FROM ".$this->db->dbprefix('online_support_sub')." AS s
			LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON s.admin_id=a.admin_id
			LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON s.user_id=u.user_id
			WHERE s.rec_id=? ORDER BY message_id DESC LIMIT 10;";
		$query=$this->db->query($sql,array($rec_id));
		return $query->result();	
	}

	public function recent_user_message($admin_id,$last_message_id=0)
	{
		$sql="SELECT s.*,u.user_name
			FROM ".$this->db->dbprefix('online_support_sub')." AS s
			LEFT JOIN ".$this->db->dbprefix('online_support_main')." AS m ON s.rec_id=m.rec_id
			LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON s.user_id=u.user_id
			WHERE m.status=1 AND m.admin_id=? AND s.message_id >? AND s.admin_id=0 
			ORDER BY message_id ASC;";
		$query=$this->db->query($sql,array($admin_id,$last_message_id));
		return $query->result();
	}

	public function message_list($filter)
	{
		$from = " FROM ".$this->db->dbprefix('online_support_sub')." AS s 
				LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON s.user_id=u.user_id
				LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON s.admin_id=a.admin_id
				 ";
		$where = " WHERE 1 ";
		$param = array();

		if ($filter['issue_id'])
		{
			$where .= " AND s.rec_id = ? ";
			$param[] = $filter['issue_id'];
		}
		if (!empty($filter['start_date']))
		{
			$where .= " AND s.create_date>=? ";
			$param[] = $filter['start_date'];
		}

		if (!empty($filter['end_date']))
		{
			$where .= " AND s.create_date<=? ";
			$param[] = $filter['end_date'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 's.message_id' : trim($filter['sort_by']);
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
		$sql = "SELECT s.*,u.user_name,a.admin_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function issue_list($filter)
	{
		$from = " FROM ".$this->db->dbprefix('online_support_main')." AS m 
				  LEFT JOIN ".$this->db->dbprefix('user_info')." AS u ON m.user_id = u.user_id
				  LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON m.admin_id = a.admin_id";
		$where = " WHERE 1 ";
		$param = array();

		if ($filter['user_name'])
		{
			$where .= " AND (u.email = ? OR u.mobile = ?) ";
			$param[] = $filter['user_name'];
			$param[] = $filter['user_name'];
		}

		if ($filter['start_date'])
		{
			$where .= " AND m.create_date>=? ";
			$param[] = $filter['start_date'];
		}

		if ($filter['end_date'])
		{
			$where .= " AND m.create_date<=? ";
			$param[] = $filter['end_date'];
		}

		if ($filter['status']!=-1)
		{
			$where .= " AND m.status=? ";
			$param[] = $filter['status'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'm.rec_id' : trim($filter['sort_by']);
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
		$sql = "SELECT m.*, u.user_name, a.admin_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function log_list($filter)
	{
		$from = " FROM ".$this->db->dbprefix('online_support_log')." AS l 
				LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON l.create_admin=a.admin_id
				 ";
		$where = " WHERE 1 ";
		$param = array();

		if ($filter['issue_id'])
		{
			$where .= " AND l.rec_id = ? ";
			$param[] = $filter['issue_id'];
		}
		if (!empty($filter['start_date']))
		{
			$where .= " AND l.create_date>=? ";
			$param[] = $filter['start_date'];
		}

		if (!empty($filter['end_date']))
		{
			$where .= " AND l.create_date<=? ";
			$param[] = $filter['end_date'];
		}
		
		if ($filter['closed']!=-1)
		{
			$where .= " AND l.closed=? ";
			$param[] = $filter['closed'];
		}
		
		if ($filter['admin_name'])
		{
			$where .= " AND a.admin_name=? ";
			$param[] = $filter['admin_name'];
		}

		$filter['sort_by'] = empty($filter['sort_by']) ? 'l.log_id' : trim($filter['sort_by']);
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
		$sql = "SELECT l.*,a.admin_name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	public function recent_log($rec_id)
	{
		$sql="SELECT l.*,a.admin_name
			FROM ".$this->db->dbprefix('online_support_log')." AS l
			LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON l.create_admin=a.admin_id
			WHERE l.rec_id=? ORDER BY l.closed ASC, log_id DESC LIMIT 5;";
		$query=$this->db->query($sql,array($rec_id));
		return $query->result();
	}
	
	public function insert_log($data){
		$this->db->insert('online_support_log',$data);
		return $this->db->insert_id();
	}
	
	public function update_log($update,$log_id){
		$this->db->update('online_support_log',$update,array('log_id'=>$log_id));
	}
	
	public function filter_log($filter){
		$query=$this->db->get_where('online_support_log',$filter,1);
		return $query->row();
	}
	
}