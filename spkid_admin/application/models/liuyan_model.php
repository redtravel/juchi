<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Liuyan_model extends CI_Model
{
        public function filter ($filter)
	{
		$query = $this->db->get_where('ty_product_liuyan', $filter, 1);
		return $query->row();
	}
    
    public function lock ($comment_id)
    {
        $sql="SELECT * FROM ".$this->db->dbprefix('product_liuyan')." WHERE comment_id='{$comment_id}' FOR UPDATE";
        $query=$this->db->query($sql);
        return $query->row();
    }

	public function update ($data, $model_id)
	{
		$this->db->update('ty_product_liuyan', $data, array('comment_id' => $model_id));
	}

	public function insert ($data)
	{
		$this->db->insert('ty_product_liuyan', $data);
		return $this->db->insert_id();
	}

	public function liuyan_list ($filter)
	{
		$from = " FROM ty_product_liuyan AS l
                    LEFT JOIN ty_product_size AS s ON l.size_id = s.size_id
                    LEFT JOIN ty_product_info AS p ON p.product_id=l.tag_id
                    LEFT JOIN ty_package_info AS pkg ON pkg.package_id=l.tag_id
                    LEFT JOIN ty_user_info AS u ON u.user_id=l.user_id
                    ";
		$where = " WHERE 1 ";
		$param = array();
        if($filter['tag_id']){
            $where .= " AND l.tag_id = ? ";
            $param[] = $filter['tag_id'];                 
        }
        if($filter['tag_type']){
            $where .= " AND l.tag_type = ? ";
            $param[] = $filter['tag_type'];
        }
        if($filter['comment_type']){
            $where .= " AND l.comment_type = ? ";
            $param[] = $filter['comment_type'];
        }
        if($filter['is_audit']!=-1){
            $where .= " AND l.is_audit = ? ";
            $param[] = $filter['is_audit'];
        }
        if($filter['is_del']!=-1){
            $where .= " AND l.is_del = ? ";
            $param[] = $filter['is_del'];
        }
        if($filter['is_reply']!=-1){
            $where .= " AND l.reply_admin_id ".($filter['is_reply']?' >0 ':' =0 ');
        }        
        
        if(!empty($filter['start_time'])){
            $where .= " AND a.comment_date >= ? ";
            $param[] = $filter['start_time'];
        }
        if(!empty($filter['end_time'])){
            $where .= " AND a.comment_date <= ? ";
            $param[] = $filter['end_time'];
        }
                
		$filter['sort_by'] = empty($filter['sort_by']) ? 'l.comment_id' : trim($filter['sort_by']);
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
		$sql = "SELECT l.*,s.size_name,p.product_name,p.product_sn,pkg.package_name,IF(u.user_name!='',u.user_name,l.user_name) as user_name "
				. $from . $where . " ORDER BY l.is_del asc," . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}

        public function pro_liuyan($comment_id)
        {
            $sql = "SELECT t.*,a.admin_name as audit_admin_name,f.admin_name as replay_admin_name,p.size_name,IF(t.user_name!='',t.user_name,u.user_name) AS user_name
                    FROM ty_product_liuyan AS t
                    LEFT JOIN ty_user_info AS u ON t.user_id = u.user_id
                    LEFT JOIN ty_admin_info AS a ON t.audit_admin_id = a.admin_id
                    LEFT JOIN ty_admin_info AS f ON t.reply_admin_id = f.admin_id
                    LEFT JOIN ty_product_size AS p ON t.size_id = p.size_id where t.comment_id =  ".$comment_id;
            $query = $this->db->query($sql);
            $arr = $query->row_array();
            $query->free_result();
            if($arr['tag_type'] == 1){
                $sql = 'SELECT product_name as tag_name FROM ty_product_info WHERE product_id = '.$arr['tag_id'];
            }else{
                $sql = 'SELECT package_name as tag_name FROM ty_package_info WHERE package_id = '.$arr['tag_id'];
            }
            $query = $this->db->query($sql);
            $arr_tag_name = $query->row_array();
            $query->free_result();
            $arr['tag_name'] = $arr_tag_name['tag_name'];
            return $arr;
        }

       function product($product_name){
            $sql = "SELECT i.product_id,i.product_sn,i.product_name,p.provider_name FROM ty_product_info AS i 
                LEFT JOIN ty_product_provider as p ON i.provider_id = p.provider_id WHERE i.product_name like ?";
            $param = array();
            $param[] = '%'.$product_name.'%';
            $query = $this->db->query($sql , $param);
            return $query->result();
        }
        
       function productsn($product_sn){
            $sql = "SELECT i.product_id,i.product_sn,i.product_name,p.provider_name FROM ty_product_info AS i 
                LEFT JOIN ty_product_provider as p ON i.provider_id = p.provider_id WHERE i.product_sn like ?";
            $param = array();
            $param[] = '%'.$product_sn.'%';
            $query = $this->db->query($sql , $param);
            return $query->result();
        }
        
        function package($package_name){
            $sql = "SELECT package_id,package_name FROM ty_package_info WHERE package_name like ?";
            $param = array();
            $param[] = '%'.$package_name.'%';
            $query = $this->db->query($sql , $param);
            return $query->result();
        }
        
	public function all_liuyan ()
	{
		$query = $this->db->get('ty_product_liuyan');
		return $query->result();
	}
    
    public function check_first_point ($row)
    {
        $sql="SELECT COUNT(*) AS ct FROM ".$this->db->dbprefix('product_liuyan')." WHERE 
            comment_type=? AND tag_type=? AND tag_id=? 
            AND is_del=0 AND user_id>0
            AND comment_id<? ";
        $query = $this->db->query($sql,array($row->comment_type,$row->tag_type,$row->tag_id,$row->comment_id));
        $result = $query->row();
        return $result->ct < 3;
    }

}




?>
