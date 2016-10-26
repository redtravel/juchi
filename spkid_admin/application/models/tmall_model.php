<?php

class Tmall_model extends CI_Model {

    
    const SYNC_STATUS_INIT = 0; // 未开始
    const SYNC_STATUS_SUCCESS = 1; // 同步成功
    
    const CHECK_STATUS_INIT = 0; // 未审核入库
    const CHECK_STATUS_SUCCESS = 1; // 已审核入库

    public function __construct() {
        parent::__construct();
    }

    public function item_list_page($page, $rows, $filter = array()) {
        $offset = ($page - 1) * $rows;
        $sql = array();
        $param = array();
        $sql[] = "select sql_calc_found_rows * from ty_tmall_item WHERE 1";
        if($filter['sync_status']!==null){
            $sql[] = "AND sync_status=?";
            $param[] = $filter['sync_status'];
        }
        if($filter['check_status']!==null){
            $sql[] = "AND check_status=?";
            $param[] = $filter['check_status'];
        }
        if($filter['provider_id']){
            $sql[] = "AND provider_id=?";
            $param[] = $filter['provider_id'];
        }
        $sql[] = "order by create_time desc";
        $sql[] = "LIMIT $offset, $rows";
        $query = $this->db->query(implode(' ', $sql), $param);
        $result = $query->result();
        $query = $this->db->query("SELECT FOUND_ROWS() AS cnt");
        $row = $query->row();
        return array('l' => $result, 'c' => $row->cnt);
    }
    
    /**
     * 根据num_iid取item记录
     * @param type $num_iid
     */
    public function get_item_by_num_iid($num_iid)
    {
        $query = $this->db->get_where('tmall_item', array('num_iid'=>$num_iid));
        return $query->row();
    }


    /**
     * 获取将要执行的一条任务
     * 
     * 按照时间的先后顺取，取最早添加的状态为0的记录
     */
    public function get_todo_job() {
        $sql = "select * from ty_tmall_fetch_job where status = 0 order by id limit 1";
        $query = $this->db->query($sql);
        return $query->row();
    }


    public function insert_num_iid($num_iid, $provider_id = 0) {
        $sql = "insert into ty_tmall_item
                set num_iid = ?, provider_id = ?, create_time=?
                on duplicate key update num_iid = num_iid";
        $param = array($num_iid, $provider_id, date('Y-m-d H:i:s'));
        if($provider_id){
            $sql  .= " , provider_id=?";
            $param[] = $provider_id;
        }
        $this->db->query($sql, $param);
    }
    
    /**
     * 取需要同步的一条记录
     * @return type
     */
    public function get_num_iid_to_sync()
    {
        $sql = "select num_iid from ty_tmall_item where sync_status=0 order by create_time limit 1";
        $query = $this->db->query($sql);
        return $query->row();
    }
    
    /**
     * 取需要同步库存的一条记录
     */
    public function get_num_iid_to_sync_stock()
    {
        $sql = "select num_iid, stock_time
		from ty_tmall_item as  t
		left join ty_product_sub as s on t.product_id = s.product_id
		where sync_status=1 and check_status=1 and s.is_on_sale=1
		order by stock_time limit 1";
        $query = $this->db->query($sql);
        return $query->row();
    }
    
    /**
     * 更新item的同步状态
     * @param type $num_iid
     * @param type $status
     * @return type
     */
    public function remark_sync_status($num_iid, $status)
    {
        $this->db->update('tmall_fetch_item', array('sync_status'=>$status), array('num_iid'=>$num_iid));
        return $this->db->affected_rows();
    }
    
    /**
     * 根据淘宝num_iid更新记录
     * @param type $data
     * @param type $num_iid
     */
    public function update_item_by_num_iid($data, $num_iid){
        $this->db->update('tmall_item', $data, array('num_iid'=>$num_iid));
    }
    
    /**
     * 根据分类编码取分类名称
     * @param type $cid
     */
    public function get_category_name_by_cid($cid)
    {
        $query = $this->db->get_where('tmall_category', array('cid'=>$cid));
        $category =  $query->row();
        return $category? $category->name : null;
    }
    
    /**
     * 删除项目
     * @param type $num_iid
     */
    public function delete_item($num_iid)
    {
        $this->db->delete('tmall_item', array('num_iid'=>$num_iid));
    }
    
    /**
     * 取天猫信息
     * @param type $product_ids
     */
    public function all_tmall_info($product_ids)
    {
        $this->db->select('num_iid, product_id')
                ->from('tmall_item')
                ->where_in('product_id', $product_ids);
        $query = $this->db->get();
        $result = $query->result();
        return $result ? $result : array();
    }
    
    /**
     * 插入天猫SKU记录
     * @param type $num_iid
     * @param type $sku_id
     * @param type $sub_id
     */
    public function insert_tmall_sku($num_iid, $sku_id, $product_id, $sub_id)
    {
        $this->db->insert('ty_tmall_sku', array(
            'num_iid' => $num_iid,
            'sku_id' => $sku_id,
            'product_id' => $product_id,
            'sub_id' => $sub_id,
        ));
    }
    
    /**
     * 删除天猫SKU记录,设置采集中间表状态
     * @param type $product_id
     */
    public function delete_product($product_id)
    {
        $this->db->delete('ty_tmall_sku', array('product_id'=>$product_id));
        $this->db->update('ty_tmall_item', array('check_status'=>self::CHECK_STATUS_INIT, 'product_id'=>0), array('product_id'=>$product_id));
        
    }
    
    /**
     * 获取商品的颜色列表
     * @param int $product_id 商品ID
     */
    public function get_product_color_list($product_id)
    {
        $sql = "select c.color_name, c.color_id
                from ty_product_sub as s
                left join ty_product_color as c on s.color_id = c.color_id
                where s.product_id = ?";
        $query = $this->db->query($sql, array($product_id));
        return $query->result();
    }
    
    /**
     * 获取商品的gallery列表
     * @param int $product_id 商品ID
     */
    public function get_product_gallery_list($product_id)
    {
        $sql = "select c.color_name, c.color_id, g.image_id, g.img_url
            from ty_product_gallery as g
            left join ty_product_color as c on g.color_id = c.color_id
            where g.product_id=?";
        $query = $this->db->query($sql, array($product_id));
        return $query->result();
    }
    
    /**
     * 取天猫SKU
     * @param array $filter 过滤条件
     */
    public function get_tmall_skus($filter)
    {
        $sql = "select sku.*, sub.gl_num ,sub.is_on_sale, sub.consign_num, sub.wait_num from ty_tmall_sku as sku
                left join ty_product_sub as sub on sku.sub_id=sub.sub_id";
        $where = array();
        $param = array();
        if($filter['product_id']){
            $where[] = 'sku.product_id=?';
            $param[] = $filter['product_id'];
        }
        $sql.=$where ? (' where ' . implode(' and ', $where)) : '';
        $query = $this->db->query($sql, $param);
        return $query->result();
    }
    
    /**
     * 取需要同步的天猫记录
     */
    public function get_item_to_sync_desc()
    {
        $sql = "select t.num_iid, t.desc_time, t.product_id
                from ty_tmall_item as t
                left join ty_product_info as p on t.product_id = p.product_id
                where t.check_status=".(self::CHECK_STATUS_SUCCESS)." and p.is_audit=1 order by t.desc_time limit 1";
        $query = $this->db->query($sql);
        return $query -> row();
    }
}
