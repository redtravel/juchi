<?php

/**
 * Tuangou_model
 */
class Tuangou_model extends CI_Model {

    private $_db;

    public function __construct(&$db = NULL) {
	parent::__construct();
	$this->_db = $db ? $db : $this->db;
    }
	
	public function filter($filter = array()) {
		$query = $this->_db->get_where('ty_mami_tuan',$filter,1);
		return $query->row();
	}

	public function filter_wechat($filter = array()) {
		$query = $this->_db->get_where('ty_wechat_info',$filter,1);
		return $query->row();
	}

	public function wechat_insert($wechat_data){
		$this->_db->insert('wechat_info',$wechat_data);
		return $this->_db->insert_id();
	}
	
	//活动信息
	public function tuan_info($tuan_id, $is_preview=0) {
		$sql = "SELECT mt.tuan_id, mt.product_id, mt.tuan_name, mt.userdefine1, mt.userdefine2, mt.userdefine3, mt.img_315_207, mt.img_500_450, mt.tuan_unit FROM ty_mami_tuan as mt ";
        if($is_preview) $sql .= " WHERE tuan_id = ? ";
        else $sql .= " WHERE status = 1 AND tuan_online_time<=NOW() AND tuan_offline_time>=NOW() and tuan_id = ? ";
		$query = $this->_db->query($sql,array(intval($tuan_id)));
		return $query->row();
	}

	//报名人数
	public function register_info($tuan_id,$limit = 5){
		$sql = " SELECT wechat_id,wechat_headimgurl,wechat_nickname,register_date,register_name FROM ty_wechat_info WHERE (register_name != '' OR register_mobile != '' OR register_num != '' OR register_date != '') AND tuan_id = ? ORDER BY register_date DESC";
		if($limit == 5 ) $sql .= " LIMIT ".$limit;
		$query = $this->_db->query($sql,array(intval($tuan_id)));
		return $query->result();
	}

	//获取活动评论人数信息
	public function comments_info($tuan_id,$limit = 5){
		$sql = "SELECT mtc.comment_id,wi.wechat_nickname,wi.wechat_headimgurl,mtc.comment_content,mtc.comment_date FROM ty_mami_tuan_comment AS mtc LEFT JOIN ty_wechat_info AS wi ON wi.wechat_id = mtc.wechat_id WHERE mtc.tuan_id = ? ORDER BY mtc.comment_date DESC ";
		if($limit == 5 ) $sql .= " LIMIT ".$limit;
		$query = $this->_db->query($sql,array(intval($tuan_id)));
		return $query->result();
	}

	public function add_register($param,$wechat_id){
		$this->_db->update('wechat_info',$param,array('wechat_id'=>$wechat_id));
	}

	public function add_comment($param){
		$this->_db->insert('mami_tuan_comment',$param);
		return $this->_db->insert_id();
	}

	//获取活动list
	public function tuan_list($str){
		$sql = " SELECT mt.tuan_id,mt.product_id,mt.tuan_name,mt.tuan_online_time,mt.tuan_offline_time,mt.img_315_207,pv.pv_num,tr.tr_num
				 FROM ty_mami_tuan AS mt
   				 LEFT JOIN ( SELECT COUNT(wf.wechat_id) pv_num,wf.tuan_id FROM ty_wechat_info AS wf WHERE 1 GROUP BY wf.tuan_id ) AS pv ON pv.tuan_id = mt.tuan_id 
   				 LEFT JOIN ( SELECT COUNT(we.wechat_id) tr_num,we.tuan_id FROM ty_wechat_info AS we WHERE we.register_name != '' OR we.register_mobile != '' OR we.register_num != '' OR we.register_date != '' GROUP BY we.tuan_id ) AS tr ON mt.tuan_id = tr.tuan_id 
				 WHERE mt.status = 1 AND mt.tuan_online_time <= NOW() AND mt.tuan_offline_time >= NOW() AND mt.product_type = ? 
				 ORDER BY mt.tuan_sort DESC,mt.tuan_offline_time DESC ";
		$query = $this->_db->query($sql,array(intval($str)));
		return $query->result();
	}

	//获取套装活动的产品
	public function tuan_suit_product($product_id){
		$sql = " SELECT p.`product_id`,p.`product_name`,p.`brand_id`,p.`brand_name`,p.`shop_price`,p.`unit_name`,gey.`img_url`
  				 FROM ty_product_info AS p
				 LEFT JOIN ty_product_gallery AS gey ON gey.`product_id` = p.`product_id`  
				 WHERE 1 AND p.`is_stop` = 0 AND p.`price_show` = 0 AND gey.`image_type` = 'default' AND p.source_id IN (0,1)
				 AND p.product_id IN ($product_id) ";
		return $this->_db->query($sql)->result_array();
	}

	//获取商品的规格
	public function pro_sub_size($product_id){
		$sql = " SELECT sub.*,siz.`size_sn`,siz.`size_name` 
				 FROM ty_product_sub AS sub
				 LEFT JOIN ty_product_size AS siz ON siz.`size_id` = sub.`size_id`
				 WHERE 1 AND sub.`is_on_sale` = 1 AND (sub.consign_num =- 2 OR sub.consign_num > 0 OR sub.gl_num > sub.wait_num) 
				 AND sub.`product_id` = ".$product_id;
		return $this->_db->query($sql)->result();	 
	}
}
