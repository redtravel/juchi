<?php
class Product_type_model extends CI_Model
{

    /**
     * 前台分类列表
     */
    function product_type_list($filter)
    {
        $from = " FROM ".$this->db_r->dbprefix('product_type')." AS pt
                    left join ".$this->db_r->dbprefix('product_type')." AS pt1
                        on pt.parent_id=pt1.type_id
                    left join ".$this->db_r->dbprefix('product_type')." AS pt2
                        on pt.parent_id2=pt2.type_id
                    left join ".$this->db_r->dbprefix('product_category')." AS c
                        on pt.category_id=c.category_id
                    left join ".$this->db_r->dbprefix('product_genre')." AS g
                        on pt.genre_id=g.id                         
                    ";
		$where = " WHERE 1 ";
		$param = array();
        if(!empty($filter['key_word']))
        {
            $where.="and (pt.type_code like '%$filter[key_word]%' or pt.type_name like '%$filter[key_word]%')";
        }
        if(!empty($filter['genre_id']))
        {
            $where.=" and pt.genre_id=$filter[genre_id]";
        }
        if(!empty($filter['first_type_id']))
        {
            $where.=" and pt.parent_id=$filter[first_type_id]";
        }
        if(!empty($filter['second_type_id']))
        {
            $where.=" and pt.parent_id2=$filter[second_type_id]";
        }
        if(!empty($filter['parent_id']))
        {
            $where.=" and (pt.parent_id=$filter[parent_id] or pt.parent_id2=$filter[parent_id])";
        }
		$filter['sort_by'] = empty($filter['sort_by']) ? 'pt.type_id' : trim($filter['sort_by']);
		$filter['sort_order'] = empty($filter['sort_order']) ? 'ASC' : trim($filter['sort_order']);
		
		$sql = "SELECT COUNT(*) AS ct " . $from . $where;
		$query = $this->db_r->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT pt.type_id,pt.type_code,pt.type_name,pt.parent_id,pt.parent_id2,
                    pt.is_show_cat,pt.sort_order,pt1.type_name as p1_type_name,
                    pt2.type_name as p2_type_name, c.category_name,g.name "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db_r->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
    }

    function filter($filter)
    {
        $query=$this->db_r->get_where('product_type',$filter);
        return $query->result();
    }

    function filter_where($where)
    {
        if(!empty($where))
        {
            $sql="select * from ty_product_type where $where";
            $query=$this->db_r->query($sql); 
            return $query->result();
        }
        return array();
    }
    
    public function update ($data,$type_id)
	{
		$this->db->update('product_type', $data, array('type_id' => $type_id));
	}

	public function insert ($data)
	{
		$this->db->insert('product_type', $data);
		return $this->db->insert_id();
	}

    public function delete($type_id)
    {
        $this->db->delete('product_type',array('type_id'=>$type_id));
    }

    public function filter_product_type_link($filter)
    {
        $query=$this->db_r->get_where('product_type_link',$filter);
        return $query->result();
    }
    
    public function gen_product_type_sn(){
	$subfix="MMT_";
	$sql =" SELECT type_code FROM ty_product_type ORDER BY type_id DESC LIMIT 1";
	$query = $this->db->query($sql);
	$last_sn = $query->row();
	if(empty($last_sn)){
	    return $subfix."1";
	}else{
	    $last_fix = substr($last_sn->type_code, 4);
	    $now_fix = intval($last_fix)+1;
	    if($now_fix >= 10000){
		sys_msg("前台分类数量超出系统边界，请联系管理员",1);
	    }
	    return $subfix.$now_fix;
	}
    }
    
    public function insert_type_link($data)
    {
        $this->db->insert('product_type_link', $data);
        return $this->db->insert_id();
    }

}
?>
