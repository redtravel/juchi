<?php
#doc
#	classname:	Purchase_batch_model
#	scope:		PUBLIC
#
#/doc

class Cost_price_model extends CI_Model
{
   public function filter($filter)
	{

		$query = $this->db->get_where('product_cost', $filter, 1);

		return $query->row();
	}
    
    /**
     * 
     * @param type $filter
     * @return type
     */
         public function product_cost_price_list($filter)
	{
	     $this->load->helper("product");
		$from = " FROM ".$this->db->dbprefix('product_info')." AS pi 
				LEFT JOIN ".$this->db->dbprefix('product_cost')." AS pc ON pi.product_id=pc.product_id
				LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS pb ON pb.batch_id=pc.batch_id
				LEFT JOIN ".$this->db->dbprefix('product_provider')." AS pp ON pp.provider_id=pb.provider_id
                LEFT JOIN ".$this->db->dbprefix('product_cooperation')." AS tpc ON tpc.cooperation_id = pp.provider_cooperation
                LEFT JOIN ".$this->db->dbprefix('admin_info')." AS ai ON ai.admin_id = pc.update_admin    
                LEFT JOIN ".$this->db->dbprefix('admin_info')." AS a ON a.admin_id = pc.create_admin  
				";
		$where = " WHERE 1 ";
		$param = array();
		if (!empty($filter['product_sn']))
		{
			$where .= " AND pi.product_sn LIKE ? ";
			$param[] = '%' . $filter['product_sn'] . '%';
		}

		if (!empty($filter['batch_code']))
		{
			$where .= " AND pb.batch_code = ? ";
			$param[] = $filter['batch_code'];
		}

		if (!empty($filter['brand_id']))
		{
			$where .= " AND pi.brand_id = ? ";
			$param[] = $filter['brand_id'];
		}		
		
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
		$sql = "SELECT pi.product_id,pi.product_sn,pi.product_name,pb.batch_code,pi.provider_productcode,
		    pi.shop_price,pi.market_price,pi.is_promote,pi.promote_price,pi.promote_start_date,pi.promote_end_date,
		    pp.provider_name,pc.id as product_cost_id,pc.cost_price,pc.consign_price,pc.consign_rate,pb.create_admin,pb.create_date,a.admin_name as creat_name,ai.admin_name as update_name,pc.update_time,tpc.cooperation_name,pc.product_cess "
				. $from . $where . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
				. " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
                
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		foreach ($list as $p) {
		    format_product($p);
		}
		
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
       
         /**
         * 在流水表中查看商品是否有出入库记录
         * @param type $product_id 商品id
         * @return type 1->使用(不可编辑) 0->未使用(可编辑)
         */
        function get_product_record($filter) {
        $sql = 'select count(*) ct from ty_transaction_info where product_id = ? and batch_id= ? ';
        $query = $this->db->query($sql,$filter);
        $row = $query->row();
        $query->free_result();
        return  (int) $row->ct;
    }
        
        
        function  update($update,$id){
            $this->db->update('product_cost', $update, array('id' => $id));
        }
        
        function  insert($insert){
            $this->db->insert('product_cost_record', $insert);
        }
        
        
}
###