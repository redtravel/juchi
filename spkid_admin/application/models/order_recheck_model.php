<?php
#doc
#	classname:	Order_recheck_model
#	scope:		PUBLIC
#
#/doc

class Order_recheck_model extends CI_Model
{

	public function check_order_recheck($order_sn) {
		$sql = "SELECT is_pick,pick_admin,qc_admin,is_qc,odd".
				" FROM ".$this->db_r->dbprefix('order_info').
				" WHERE order_sn = ? ";
		$param = array();
		$param[] = $order_sn;
		$query = $this->db_r->query($sql, $param);
		return $query->row();
	}
	
	public function get_order_product_info ($order_sn){
		$sql = "SELECT pks.sub_id,oi.pick_sn, oi.invoice_no, pks.product_id,pks.color_id,pks.size_id,ppi.product_sn,ppi.provider_productcode,brand.brand_name,pc.color_sn,pc.color_name,ps.size_name,ps.size_sn,ppi.product_name,pks.pick_num,pks.qc_num,pks.product_number,(pks.product_number-pks.qc_num) AS unqc_num,pu.provider_barcode ".
				"FROM ".$this->db_r->dbprefix('order_info')." AS oi ".
				"LEFT JOIN ".$this->db_r->dbprefix('pick_sub')." AS pks ON oi.order_sn = pks.rel_no ".
				"LEFT JOIN ".$this->db_r->dbprefix('product_info')." AS ppi ON pks.product_id = ppi.product_id ".
				"LEFT JOIN ".$this->db_r->dbprefix('product_size')." AS ps ON pks.size_id = ps.size_id ".
				"LEFT JOIN ".$this->db_r->dbprefix('product_color')." AS pc ON pks.color_id = pc.color_id ".
				"LEFT JOIN ".$this->db_r->dbprefix('product_brand')." AS brand ON brand.brand_id = ppi.brand_id ".
                                "LEFT JOIN ".$this->db_r->dbprefix('product_sub')." AS pu ON pks.product_id = pu.product_id AND pks.color_id = pu.color_id AND pks.size_id = pu.size_id ".
				"WHERE oi.order_sn = ? ".
                                "GROUP BY pks.sub_id ";
		$param = array();
		$param[] = $order_sn;
		$query = $this->db_r->query($sql, $param);
		$reslut = $query->result();
		$query->free_result();
		return $reslut;
	}
	
	public function filter_order_product($order_sn,$provider_barcode) {
		$sql = "SELECT ps.product_id,ps.color_id,ps.size_id ".
				"FROM ".$this->db_r->dbprefix('product_sub')." AS ps ".
				"LEFT JOIN ".$this->db_r->dbprefix('order_product')." AS op ON op.product_id = ps.product_id AND op.color_id = ps.color_id AND op.size_id = ps.size_id ".
				"LEFT JOIN ".$this->db_r->dbprefix('order_info')." AS oi ON oi.order_id = op.order_id ".
				"WHERE oi.order_sn = ? AND ps.provider_barcode = ? ";
		$param = array();
		$param[] = $order_sn;
		$param[] = $provider_barcode;
		$query = $this->db_r->query($sql, $param);
		return $query->row();
	}
	
	public function update_pick_sub($update,$sub_id) {
        $this->db->update('pick_sub',$update,array('sub_id'=>$sub_id));
	}
	
	public function update_pick($pick_sn,$admin_id) {
		$param = array();
		$sql = "UPDATE ".$this->db->dbprefix('pick_info')." SET over_num = over_num + 1 ,qc_admin = ? ,qc_date = ? WHERE pick_sn = ? ";
		$param[] = $admin_id;
		$param[] = date('Y-m-d H:i:s');
		$param[] = $pick_sn;
		$query = $this->db->query($sql, $param);
	}
	
	public function get_pick_sub($filter) {
		$query = $this->db->get_where('pick_sub', $filter, 1);
		return $query->row();
	}
	
	public function update_order_info($update,$order_sn) {
		$this->db->update('order_info',$update,array('order_sn'=>$order_sn));
	}
        
        public function insert_order_advice($update) {
		$this->db->insert('order_advice', $update);
		return $this->db->insert_id();
	}
        
        public function insert_order_action($update) {
		$this->db->insert('order_action', $update);
		return $this->db->insert_id();
	}
        
        public function delete_pick_sub($pick_sn,$order_sn){
		$this->db->delete('pick_sub',array('pick_sn'=>$pick_sn,'rel_no'=>$order_sn));
	}

}
###