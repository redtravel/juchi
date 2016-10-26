<?php 
/**
* Order_model
*/
class Order_shipping_status_model extends CI_Model
{
    function filter($filter){
        $query = $this->db->get_where('order_shipping_status', $filter, 1);
		return $query->row();
    }
    function insert($data){
        $this->db->insert('order_shipping_status', $data);
		return $this->db->insert_id();
    }
    function update($update,$id){
        $this->db->update('order_shipping_status', $update, array('id' => $id));
    }
    function get_order_shipping_list() {
        $sql = "select oi.address,oi.invoice_no,oi.shipping_id,si.shipping_company_100 from " . $this->db->dbprefix('order_info') . " oi 
            left join " . $this->db->dbprefix('shipping_info') . " si on oi.shipping_id = si.shipping_id 
            left join " . $this->db->dbprefix('order_shipping_status') . " t on oi.invoice_no = t.invoice_no and si.shipping_company_100 = t.company 
            where t.id is null and oi.shipping_id !=1 and oi.invoice_no !='' limit 100";
        $query = $this->db->query($sql);
        return $query->result();
    }
    /*
     * 获取顺丰待确认订单信息
     */

    public function get_sf_confirm() {
        $sql = "select sp.order_sn,order_weight,mailno from "
                . $this->db->dbprefix('shipping_package_interface') ." as sp "
                . "left join ".$this->db->dbprefix('order_info')." as oi on sp.order_id=oi.order_id AND oi.shipping_id = sp.shipping_id "
                . "where oi.order_status = 1 AND oi.shipping_status = 1 "
                . "and result in(0,2) AND filter_status = 1 and sp.mailno<>'' "
                . "and oi.shipping_id=" . SF_SHIPPING_ID . " limit " . ORDER_NUN;
        $query = $this->db->query($sql);
        return $query->result('array');
    }
    function update_package_interface($update,$order_sn){
        $this->db->update('order_shipping_status', $update, array('order_sn' => $order_sn));
    }

}