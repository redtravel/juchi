<?php

/**
 * 顺丰接口公共类
 * 2013-05-24
*/

class Package_sf_model extends CI_Model
{
	/**
	 * 顺丰下单订单列表
	 * chenxi 20130527
	 */
	public function sf_order_list($shipping)
	{
		$sql = "SELECT oi.order_id,oi.order_sn,oi.`consignee`,oi.`province`,oi.`city`,oi.`district`,oi.create_date,
				oi.`tel`,oi.`mobile`,oi.`zipcode`,p.`region_name` AS province_name,c.`region_name` AS city_name,
				d.`region_name` AS district_name,oi.`address`, oi.pay_id,oi.shipping_id,oi.order_price as goods_price, oi.order_price+oi.shipping_fee-oi.paid_price AS order_price, oi.pay_id, oi.confirm_date 
				FROM `ty_order_info` AS oi 
				LEFT JOIN `ty_region_info` AS p ON oi.`province` = p.`region_id` 
				LEFT JOIN `ty_region_info` AS c ON oi.`city` = c.`region_id` 
				LEFT JOIN `ty_region_info` AS d ON oi.`district` = d.`region_id` 
				LEFT JOIN `ty_shipping_package_interface` spi ON oi.order_id = spi.order_id 
				WHERE oi.order_status = 1 AND oi.invoice_no = '' AND oi.shipping_status = 0 
					AND oi.lock_admin = 0 AND oi.is_pick = 1 AND oi.is_qc = 0 
					AND oi.shipping_id IN (".implode(',', $shipping).") AND spi.sp_id IS NULL 
				ORDER BY oi.order_id DESC LIMIT ".SF_ORDER_NUM;
				
		/*$sql = "SELECT oi.order_id,oi.order_sn,oi.`consignee`,oi.`province`,oi.`city`,oi.`district`,oi.create_date,
				oi.`tel`,oi.`mobile`,oi.`zipcode`,p.`region_name` AS province_name,c.`region_name` AS city_name,
				d.`region_name` AS district_name,oi.`address`, oi.pay_id,oi.shipping_id,oi.order_price as goods_price, oi.order_price+oi.shipping_fee-oi.paid_price AS order_price, oi.pay_id, oi.confirm_date 
				FROM `ty_order_info` AS oi 
				LEFT JOIN `ty_region_info` AS p ON oi.`province` = p.`region_id` 
				LEFT JOIN `ty_region_info` AS c ON oi.`city` = c.`region_id` 
				LEFT JOIN `ty_region_info` AS d ON oi.`district` = d.`region_id` 
				LEFT JOIN `ty_shipping_package_interface` spi ON oi.order_id = spi.order_id 
				WHERE oi.order_status = 1 AND oi.shipping_status = 0 
					AND oi.lock_admin = 0 AND oi.is_pick = 1 AND oi.is_qc = 0 
					AND oi.shipping_id IN (".implode(',', $shipping).") AND spi.sp_id IS NULL 
				ORDER BY oi.order_id DESC";				
				echo $sql;*/
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	/**
	 * 顺丰回调订单数据处理
	 * chenxi 20130527
	 */
	function order_data_process($xml,$order) {
		$xmlObject = simplexml_load_string($xml);
		$status = $xmlObject->result->status;
		$sf_order_sn = $xmlObject->orderid;
		$remark = $mailno = $distCode = '';    
		if($status == 1) { //Success－可收派
			$mailno = $xmlObject->result->mailno;
			$distCode = $xmlObject->result->distCode;
			$sql = "UPDATE ty_order_info SET invoice_no = '".$mailno."' WHERE order_id = ".$order['order_id'];
			$this->db->query($sql);
		} else {
			$remark = $xmlObject->result->remark;
		}
		$sql = "REPLACE INTO ty_shipping_package_interface (shipping_id,order_id,order_sn,mailno,filter_status,filter_remark,dist_code,add_time) "
				."VALUES (".SF_SHIPPING_ID.",".$order['order_id'].",'".$sf_order_sn."','".$mailno."',".$status.",'".$remark."','".$distCode."','".date("Y-m-d H:i:s")."')";
		$this->db->query($sql);
		
		return true;
	}
     /*
     * 获取顺丰待确认订单信息
     *by jiang 
     */

    public function get_sf_confirm() {
        $sql = "select sp.order_sn, oi.order_weight_unreal,sp.mailno from "
                . $this->db->dbprefix('shipping_package_interface') . " as sp "
                . "left join " . $this->db->dbprefix('order_info') . " as oi on sp.order_id=oi.order_id AND oi.shipping_id = sp.shipping_id "
                . "where oi.order_status = 1 AND oi.shipping_status = 1 "
                . "and result in(0,2) AND filter_status = 1 and sp.mailno<>'' "
                . "and oi.shipping_id=" . SF_SHIPPING_ID . " limit " . ORDER_NUN;
        $query = $this->db->query($sql);
        return $query->result('array');
    }
    /*
     * 顺丰订单确认后更新接口表
     * by jiang
     */
    public function update_package_interface($update, $order_sn) {
        $this->db->update('shipping_package_interface', $update, array('order_sn' => "$order_sn"));
    }

    /*
     * 获取需要取消的订单列表（顺丰）
     * by jiang
     */
    public function get_cancel_list(){
        $sql = "select order_sn,order_id from ". $this->db->dbprefix('shipping_package_interface') ." where filter_status=5 and shipping_id=" . SF_SHIPPING_ID." limit ".ORDER_CANCEL_NUN;
        $query = $this->db->query($sql);
        return $query->result('array');
    }

    /*
     * 顺丰电商订单取消成功时，删除fc_flc_shipping_package_interface表中相对应的订单记录。
     * @return string
     * by jiang
     */

    function delete_package_interface($order_id) {
        $this->db->delete('shipping_package_interface', array('order_id' => $order_id));
	return $this->db->affected_rows();
    }
    
    function set_shipping_package($data) {  
        if(!empty($data['mailno'])) {
                $sql = "UPDATE ty_order_info SET invoice_no = '".$data['mailno']."' WHERE order_id = ".$data['order_id'];
                $this->db->query($sql);
        }
        
        $sql = "REPLACE INTO ty_shipping_package_interface (shipping_id,order_id,order_sn,mailno,filter_status,filter_remark,dist_code,add_time) "
                        ."VALUES (".$data['shipping_id'].",".$data['order_id'].",'".$data['order_sn']."','".$data['mailno']."',".$data['filter_status'].",'".$data['filter_remark']."','".$data['dist_code']."','".date("Y-m-d H:i:s")."')";
        $this->db->query($sql);

        return true;
    }
}