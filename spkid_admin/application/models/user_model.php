<?php
#doc
#	classname:	Product_model
#	scope:		PUBLIC
#
#/doc

class user_model extends CI_Model
{

	public function filter($filter)
	{
		$query = $this->db->get_where('user_info', $filter, 1);
		return $query->row();
	}

    public function lock_user($user_id)
    {
        $sql = "SELECT * FROM ".$this->db->dbprefix('user_info')." WHERE user_id = ? LIMIT 1 FOR UPDATE";
        $query = $this->db->query($sql, array(intval($user_id)));
        return $query -> row();
    }
        
	public function user_list ($filter)
	{
        $from = " FROM ".$this->db->dbprefix('user_info')." AS i 
                LEFT JOIN ".$this->db->dbprefix('user_rank')." AS r ON i.rank_id = r.rank_id";
		$where = " WHERE 1 ";
		$param = array();

                if(!empty($filter['mobile'])){
                    $where .= " AND i.mobile like  ? ";
                    $param[] = '%'.$filter['mobile'].'%';
                }
        if(!empty($filter['user_name'])){
            $where .= " AND i.user_name = ? ";
            $param[] = $filter['user_name'];
        }
                
                if(!empty($filter['email'])){
                    $where .= " AND i.email like  ? ";
                    $param[] = '%'.$filter['email'].'%';
                }

                if(!empty($filter['email_validated'])){
                    $where .= " AND i.email_validated =  ? ";
                    $param[] = $filter['email_validated'] - 1;
                }
                
                if(!empty($filter['is_use'])){
                    $where .= " AND i.is_use =  ? ";
                    $param[] = $filter['is_use'] - 1;
                }
                
                if(!empty($filter['user_type'])){
                    $where .= " AND i.user_type =  ? ";
                    $param[] = $filter['user_type'] - 2;
                }
                
                if(!empty($filter['mobile_checked'])){
                    $where .= " AND i.mobile_checked =  ? ";
                    $param[] = $filter['mobile_checked'] - 1;
                }

                if(!empty($filter['start_time'])){
                    $where .= " AND i.create_date >= ? ";
                    $param[] = $filter['start_time'];
                }
                if(!empty($filter['end_time'])){
                    $where .= " AND i.create_date <= ? ";
                    $param[] = $filter['end_time'];
                }

		$filter['sort_by'] = empty($filter['sort_by']) ? 'i.is_use ASC,i.user_id ' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'desc' : trim($filter['sort_order']);

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
                
		$sql = "SELECT i.*,r.rank_name"
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

	public function insert ($data)
	{
		$this->db->insert('ty_user_info', $data);
		return $this->db->insert_id();
	}

	public function update ($data, $user_id)
	{
		return $this->db->update('user_info', $data, array('user_id' => $user_id));
	}

	public function delete ($user_id)
	{
		$this->db->delete('user_info', array('user_id' => $user_id));
	}

    public function select_user_name($filter){
        $where = " WHERE 1 ";
        $param = array();
        foreach($filter as $key => $val){
            $where .= " AND `". $key ."` like ?";
            $param[] = "%" . $val . "%";
        }
        $sql = "SELECT * FROM ty_user_info ".$where;
        $query = $this->db->query($sql , $param);
        return $query->result();
    }
    
    public function distinct_rank_id(){
        $sql = "SELECT DISTINCT rank_id FROM ty_user_info";
        $query = $this->db->query($sql);
        $arr = $query->result();
        $res = array();
        foreach($arr as $item){
            $res[] = $item->rank_id;
        }
        return $res;
    }

    public function all_user($filter)
    {
        if(isset($filter['user_id']) && is_array($filter['user_id'])){
            $this->db->where_in('user_id',$filter['user_id']);
            unset($filter['user_id']);
        }
        $query = $this->db->get_where('user_info', $filter);
        return $query->result();
    }

    // address
    public function filter_address($filter)
    {
        $query = $this->db->get_where('user_address', $filter, 1);
        return $query->row();
    }

    public function all_address($filter)
    {
         $this->db->order_by("address_id", "desc"); 
         $query = $this->db->get_where('user_address', $filter);
         return $query->result();
    }

    public function insert_address ($data)
    {
        $this->db->insert('user_address', $data);
        return $this->db->insert_id();
    }

    public function update_address ($data, $address_id)
    {
        $this->db->update('user_address', $data, array('address_id' => $address_id));
    }


    public function delete_address ($address_id)
    {
        $this->db->delete('user_address', array('address_id' => $address_id));
    }

    // rank
    public function filter_rank($filter)
    {
        $query = $this->db->get_where('user_rank', $filter, 1);
        return $query->row();
    }
    public function all_rank ($filter = array())
    {   
        $query = $this->db->get_where('user_rank', $filter);
        return $query->result();
    }
    
    public function insert_rank ($data)
    {
        $this->db->insert('user_rank', $data);
        return $this->db->insert_id();
    }
    
    public function update_rank ($data, $rank_id)
    {
        $this->db->update('user_rank', $data, array('rank_id' => $rank_id));
    }
    
    public function delete_rank ($rank_id)
    {
        $this->db->delete('user_rank', array('rank_id' => $rank_id));
    }
}
###
