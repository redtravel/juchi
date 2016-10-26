<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class mami_tuan_model extends CI_Model
{
	//团购详情
	public function filter ($filter)
	{
		$query = $this->db->get_where('ty_mami_tuan', $filter, 1);
		return $query->row();
	}
	
	//插入数据
	public function insert ($data)
	{
		$this->db->insert('ty_mami_tuan', $data);
		return $this->db->insert_id();
	}
	
	//更新数据
	public function update ($data, $tuan_id)
	{
		$this->db->update('ty_mami_tuan', $data, array('tuan_id' => $tuan_id));
	}
	
	//团购列表
	public function tuan_list ($filter)
	{
        $param = array();
		$where = " WHERE 1 ";
		if(!empty($filter['product_sn'])){
			$where .= " AND pi.product_sn = ? ";
			$param[] = $filter['product_sn'];
        }
		if(!empty($filter['start_time'])){
			$where .= " AND mt.tuan_online_time >= '".$filter['start_time']." 00:00:00' AND mt.tuan_online_time <='".$filter['start_time']." 23:59:59' ";
		}
		$sql = "SELECT COUNT(*) AS ct FROM ty_mami_tuan AS mt" 
				. " LEFT JOIN ty_product_info AS pi ON pi.product_id = mt.product_id"
				. $where;
		$query = $this->db->query($sql, $param);
		$row = $query->row();
		$query->free_result();
		$filter['record_count'] = (int) $row->ct;
		$filter = page_and_size($filter);
		if ($filter['record_count'] <= 0)
		{
			return array('list' => array(), 'filter' => $filter);
		}
		$sql = "SELECT mt.tuan_id,mt.tuan_sort,mt.tuan_name,mt.tuan_online_time,mt.tuan_offline_time,mt.tuan_price,mt.tuan_unit,mt.product_num,mt.status,pi.product_sn,pi.market_price,pi.is_promote,pi.is_onsale FROM ty_mami_tuan AS mt"
				. " LEFT JOIN ty_product_info AS pi ON pi.product_id = mt.product_id"
				. $where
				. " ORDER BY tuan_id DESC LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
		$query = $this->db->query($sql, $param);
		$list = $query->result();
		$query->free_result();
		return array('list' => $list, 'filter' => $filter);
	}
	
	//手动上架
	public function onsale_tuan_on($tuan_id,$admin_id){
	    $this->db->trans_start();
$sql = <<< SQL
INSERT INTO ty_product_onsale_record 
(sub_id, sr_onsale, create_admin, create_date, onsale_memo) 
SELECT ps.sub_id, 1, $admin_id, now(), concat('团购手工上架,tuanid=', mt.tuan_id) 
FROM ty_product_sub ps 
  , ty_product_info pi 
  , ty_mami_tuan mt 
  , ty_admin_info ai 
WHERE 
    ps.product_id = pi.product_id 
AND 
    mt.product_id = pi.product_id 
AND 
    mt.status = 1 
AND 
    mt.op_add_aid = ai.admin_id 
AND 
    mt.tuan_id = $tuan_id
;
SQL;
$this->db->query($sql);
$sql = <<< SQL
UPDATE 
    ty_product_sub ps 
  , ty_product_info pi 
  , ty_mami_tuan mt 
  , ty_admin_info ai 
SET 
    ps.is_on_sale = 1 
  , pi.is_onsale = 1 
  , pi.promote_start_date = mt.tuan_online_time 
  , pi.promote_end_date = mt.tuan_offline_time 
  , pi.is_promote = 1 
  , pi.promote_price = mt.tuan_price 
WHERE 
    ps.product_id = pi.product_id 
AND 
    mt.product_id = pi.product_id 
AND 
    mt.status = 1 
AND 
    mt.op_add_aid = ai.admin_id 
AND 
    mt.tuan_id = $tuan_id
;
SQL;
$this->db->query($sql);  
$this->db->trans_commit();
	}
	
	//手动下架
	public function onsale_tuan_off($tuan_id,$admin_id){
	    $this->db->trans_start();
$sql = <<< SQL
INSERT INTO ty_product_onsale_record 
(sub_id, sr_onsale, create_admin, create_date, onsale_memo) 
SELECT ps.sub_id, 0, $admin_id, now(), concat('团购手工下架,tuanid=', mt.tuan_id) 
FROM ty_product_sub ps 
  , ty_product_info pi 
  , ty_mami_tuan mt 
  , ty_admin_info ai 
WHERE 
    ps.product_id = pi.product_id 
AND 
    mt.product_id = pi.product_id 
AND 
    mt.status = 1 
AND 
    mt.op_add_aid = ai.admin_id 
AND 
    mt.tuan_id = $tuan_id
;
SQL;
$this->db->query($sql);
$sql = <<< SQL
UPDATE 
    ty_product_sub ps 
  , ty_product_info pi 
  , ty_mami_tuan mt 
  , ty_admin_info ai 
SET 
    ps.is_on_sale = 0 
  , pi.is_onsale = 0 
  , pi.promote_start_date = '0000-00-00 00:00:00' 
  , pi.promote_end_date = '0000-00-00 00:00:00' 
  , pi.is_promote = 0 
  , pi.promote_price = 0 
WHERE 
    ps.product_id = pi.product_id 
AND 
    mt.product_id = pi.product_id 
AND 
    mt.status = 1 
AND 
    mt.op_add_aid = ai.admin_id 
AND 
    mt.tuan_id = $tuan_id
;
SQL;
$this->db->query($sql);  
$this->db->trans_commit();
	}
	
	//自动上架
	public function auto_tuan_on() {
$sql = <<< SQL
INSERT INTO ty_product_onsale_record 
(sub_id, sr_onsale, create_admin, create_date, onsale_memo) 
SELECT ps.sub_id, 1, -1, now(), concat('tuanid=', mt.tuan_id) 
FROM ty_product_sub ps 
  , ty_product_info pi 
  , ty_mami_tuan mt 
  , ty_admin_info ai 
WHERE 
    ps.product_id = pi.product_id 
AND 
    mt.product_id = pi.product_id 
AND 
    mt.tuan_online_time < DATE_ADD( NOW(), INTERVAL 10  MINUTE) 
AND 
    mt.tuan_online_time > NOW() 
AND 
    mt.tuan_offline_time > DATE_ADD( NOW(), INTERVAL 10  MINUTE) 
AND 
    mt.status = 1 
AND 
    mt.op_add_aid = ai.admin_id 
;
SQL;
$this->db->query($sql);
$sql = <<< SQL
UPDATE 
    ty_product_sub ps 
  , ty_product_info pi 
  , ty_mami_tuan mt 
  , ty_admin_info ai 
SET 
    ps.is_on_sale = 1 
  , pi.is_onsale = 1 
  , pi.promote_start_date = mt.tuan_online_time 
  , pi.promote_end_date = mt.tuan_offline_time 
  , pi.is_promote = 1 
  , pi.promote_price = mt.tuan_price 
WHERE 
    ps.product_id = pi.product_id 
AND 
    mt.product_id = pi.product_id 
AND 
    mt.tuan_online_time < DATE_ADD( NOW(), INTERVAL 10  MINUTE) 
AND 
    mt.tuan_online_time > NOW() 
AND 
    mt.tuan_offline_time > DATE_ADD( NOW(), INTERVAL 10  MINUTE) 
AND 
    mt.status = 1 
AND 
    mt.op_add_aid = ai.admin_id 
;
SQL;
$this->db->query($sql);
    }
		
	//自动下架
	public function auto_tuan_off() {
$sql = <<< SQL
INSERT INTO ty_product_onsale_record 
(sub_id, sr_onsale, create_admin, create_date, onsale_memo) 
SELECT ps.sub_id, 0, -1, now(), concat('tuanid=', mt.tuan_id) 
FROM ty_product_sub ps 
  , ty_product_info pi 
  , ty_mami_tuan mt 
  , ty_admin_info ai 
WHERE 
    ps.product_id = pi.product_id 
AND 
    mt.product_id = pi.product_id 
AND 
    mt.tuan_offline_time < DATE_ADD( NOW(), INTERVAL 10  MINUTE) 
AND 
    mt.tuan_offline_time > NOW() 
AND 
    mt.status = 1 
AND 
    mt.op_add_aid = ai.admin_id 
;
SQL;
$this->db->query($sql);

$sql = <<< SQL
UPDATE 
    ty_product_sub ps 
  , ty_product_info pi 
  , ty_mami_tuan mt 
  , ty_admin_info ai 
SET 
    ps.is_on_sale = 0 
  , pi.is_onsale = 0 
  , pi.promote_start_date = '0000-00-00 00:00:00' 
  , pi.promote_end_date = '0000-00-00 00:00:00' 
  , pi.is_promote = 0 
  , pi.promote_price = 0 
  , mt.status = 3 
WHERE 
    ps.product_id = pi.product_id 
AND 
    mt.product_id = pi.product_id 
AND 
    mt.tuan_offline_time < DATE_ADD( NOW(), INTERVAL 10  MINUTE) 
AND 
    mt.tuan_offline_time > NOW() 
AND 
    mt.status = 1 
AND 
    mt.op_add_aid = ai.admin_id 
;
SQL;
$this->db->query($sql);
    }
	
	//检测商品是否添加了限抢活动
	public function check_product_rush($product_id)
	{
		$sql = "SELECT product_id FROM ".$this->db->dbprefix('rush_product')." AS rp".
				" LEFT JOIN ".$this->db->dbprefix('rush_info')." AS r ON r.rush_id = rp.rush_id".
				" WHERE r.status <= 1 AND rp.product_id = ?";
		$param[] = $product_id;
		$query = $this->db->query($sql, $param);
		$result = $query->result();
		$query->free_result();
		return $result;
	}
	
	//检测商品是否添加了团购活动
	public function check_product_tuan($product_id)
	{
		$sql = "SELECT product_id FROM ".$this->db->dbprefix('mami_tuan')." AS mt".
				" WHERE mt.status <= 1 AND mt.product_id = ?";
		$param[] = $product_id;
		$query = $this->db->query($sql, $param);
		$result = $query->result();
		$query->free_result();
		return $result;
	}
	
	//团购商品是否上架
	public function get_tuan_onsale_status($tuan_id)
	{
		$result = '';
		$sql = "SELECT mt.tuan_id" . 
				" FROM ty_mami_tuan AS mt" . 
				" LEFT JOIN ty_product_info AS pi ON pi.product_id = mt.product_id" . 
				" WHERE pi.is_promote = 1 AND pi.is_onsale = 1 AND mt.tuan_id = ?";
		$param[] = $tuan_id;
		$query = $this->db->query($sql, $param);
		$result = $query->result();
		$query->free_result();
		return $result;
	}
	
	 //得到全部团购数量
    public function getTuanCount()
	{
    	$sql = "select count(*) as cot
    			FROM ".$this->db->dbprefix('mami_tuan')." 
    			WHERE status = 1 and tuan_online_time<=NOW()";
    	$query = $this->db->query($sql);
        $ret = $query->row_array();
    	return $ret['cot'];
	}
	
	//得到今日团购数量
    public function getTodayTuanCount()
	{
    	$sql = "select count(*) as cot
    			FROM ".$this->db->dbprefix('mami_tuan')." 
    			WHERE status = 1 and tuan_online_time>=CURDATE() 
				and tuan_online_time<DATE_ADD(CURDATE(),INTERVAL 1 DAY) ";
    	$query = $this->db->query($sql);
        $ret = $query->row_array();
    	return $ret['cot'];
	}
	
	//得到特卖数量
    public function getBrandNum()
	{
    	$sql = "SELECT COUNT(DISTINCT rush_brand) as cot
    			FROM ".$this->db->dbprefix('rush_info')." 
    			WHERE status = 1 AND start_date < NOW() AND end_date > NOW() ";
    	$query = $this->db->query($sql);
        $ret = $query->row_array();
    	return $ret['cot'];
	}
	
}

?>
