<?php
/**
 * 与wordpress DB操作
 * @date 20150916
 */
class Product_access_model extends CI_Model {
    //更新文章访问量
    public function insert ($data)
    {
	    if( empty($data) ) return true;
        $this->db->insert_batch('ty_product_access', $data);
        return $this->db->insert_id();
    }
}
