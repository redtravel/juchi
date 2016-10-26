<?php
/**
 * Description of purchase_model
 *
 * @author Carol
 * @date    2013-2-18
 */
class purchase_model extends CI_Model{
    //put your code here
    
    function __construct() {
        parent::__construct();
    }
    
    public function filter_purchase_batch($filter) {
        $query = $this->db_r->get_where('purchase_batch', $filter, 1);
        return $query->row();
    }

    public function filter_purchase_batch_all($filter) {
        $query = $this->db_r->get_where('purchase_batch', $filter);
        return $query->result_array();
    }
    
    /**
     * 根据供应商获取采购批次
     *
     * @param type $provider_id
     * @param type $filed
     * @param type $is_use   是否是可用批次:0=>全部,1=>可用的;default:0.
     * @return type 
     */
    function get_purchase_batch($provider_id, $filed = "*", $is_use = 0 ) {
        $where = '';
        if ( !empty($is_use ) && intval($is_use) === 1 ){
                $where = "and batch_status = 1 and batch_type = 0 ";
        }
        $sql = 'select ' . $filed . ' from ty_purchase_batch where provider_id = ? '. $where .'ORDER BY create_date DESC';
        $res = $this->db_r->query($sql, array($provider_id))->result_array();
        $tmp_batch = array();
        foreach ($res as $val) {
            $tmp_batch[$val["batch_id"]] = $val["batch_code"];
        }
        return $tmp_batch;
    }
    
    /**
     * 获取采购单商品ids
     * @param type $purchase_id
     * @return string 
     */
    function get_purchase_pro_ids ( $purchase_id ){
        $sql = "SELECT DISTINCT product_id FROM ty_purchase_sub WHERE purchase_id = ? ORDER BY product_id ASC ";
        $res = $this->db_r->query($sql, array($purchase_id ) )->result_array();
        return $res;
    }
    
    /**
     * 获取采购单中无成本价的商品总数
     * @param type $batch_id
     * @param type $product_ids
     * @return type 
     */
    function is_purchase_cost_exit ($batch_id, $product_ids ) {
        $this->load->helper("common");
        $sql = "SELECT count(1) as not_exit
                FROM ty_product_cost
                WHERE batch_id = ?
                    AND product_id ". db_create_in( $product_ids ) ."
                    AND (consign_price <= 0
                        AND cost_price <= 0
                        AND consign_rate <= 0)
                ORDER BY product_id ASC";
        $row = $this->db_r->query($sql, array($batch_id ))->row();
        return $row->not_exit;
    }
    
    /**
     * 根据供应商id和批次id/code 获取可用的采购批次
     * @param type $provider_id
     * @param type $batch_id
     * @return type 
     */
    public function get_provider_batch($provider_id ,$batch_id , $batch_code = '' ){
        $sql = "SELECT * FROM ty_purchase_batch WHERE batch_status= 1 AND batch_type = 0 AND provider_id = ? ";
        if ( empty($batch_code)) {
            $sql .= " AND batch_id = ?";
            $param = array($provider_id ,$batch_id );
        }else {
            $sql .= " AND batch_code = ?";
            $param = array($provider_id ,$batch_code );
        }
        $row = $this->db_r->query($sql , $param )->row();
        return $row;
    }
    
    /**
     * 获取供应商id,批次id，供应商合作方式
     * @param string    $batch_code     批次code
     * @param string    $provider_code  供应商code
     * @return array 
     */
    public function get_provider_batch_coop($batch_code, $provider_code ){
        $sql = "SELECT batch_id,p.provider_id,batch_type,pp.provider_cooperation FROM ty_purchase_batch p 
                    LEFT JOIN ty_product_provider pp ON pp.provider_id = p.provider_id
                    WHERE p.batch_status = 1 AND p.batch_type = 0 AND p.batch_code = ? AND pp.provider_code = ? ";
        $row = $this->db_r->query($sql , array($batch_code , $provider_code) )->row();
        return $row;
    }
    
    /**
     * 查询库存余量
     * @return array
     */
    public function search_inventory($filter)
    {
        // 获取查询结果ID集
        $from = " from ty_transaction_info ti
                left join ty_product_info pi on pi.product_id = ti.product_id
                left join ty_product_sub ps on ps.product_id = pi.product_id
                left join ty_product_brand pb on pi.brand_id = pb.brand_id and pb.is_use = 1
                left join ty_product_provider pp on pi.provider_id = pp.provider_id
                left join ty_depot_info di on ti.depot_id = di.depot_id
                left join ty_purchase_batch pba on pba.batch_id = ti.batch_id and pp.provider_id = pba.provider_id";
        $where = " where 1 ";
        if (!empty($filter['provider_id'])) {
            $where .= " AND pi.provider_id = ".$filter['provider_id'];
        }
        if (!empty($filter['brand_id'])) {
            $where .= " AND pi.brand_id = ".$filter['brand_id'];
        }
        if (!empty($filter['purchase_batch'])) {
            $where .= " AND pba.batch_id = ".$filter['purchase_batch'];
        }
        if (!empty($filter['sell_mode'])) {
            $where .= " AND pba.is_consign = ".$filter['sell_mode'];
        }
        if (!empty($filter['depot_id'])) {
            $where .= " AND di.depot_id = ".$filter['depot_id'];
        }
        if (!empty($filter['product_sn'])) {
            $where .= " AND pi.product_sn = '".$filter['product_sn']."'";
        }
        if (!empty($filter['provider_barcode'])) {
            $where .= " AND ps.provider_barcode = '".$filter['provider_barcode']."'";
        }
        $where .= " group by ti.product_id, ti.color_id, ti.size_id ";
        
        $sql = "select count(1) ct from (select ti.product_id, ti.color_id, ti.size_id " .$from .$where .") t";
        $query = $this->db->query($sql);
        $row = $query->row();
        $query->free_result();
        
        $filter['record_count'] = (int) $row->ct;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
            return array('list' => array(), 'filter' => $filter);
        }
        
        $sql = "select ti.product_id " .$from .$where . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
			       
        $res = $this->db_r->query($sql)->result();
        
        $product_id_ary = array();
        foreach ($res as $value) {
            $product_id_ary[] = $value->product_id;
        }
        
        // 根据ID集查询结果
        $sql = "select pi.product_id, pi.product_name, pi.product_sn, pi.provider_productcode, pb.brand_name, 
                       pst.style_name, pss.season_name, pi.product_sex, pi.product_year,
                       pi.product_month, pi.is_promote, pi.shop_price, pi.market_price,
                       pi.promote_start_date, pi.promote_end_date, pi.promote_price, pi.keywords,
                       ps.color_id, ps.size_id, ps.gl_num, ps.consign_num,ps.provider_barcode, t.product_number, psi.size_name, pc.color_name, pca.category_name, t.batch_codes
               from ty_product_sub ps
               left join ty_product_info pi on ps.product_id = pi.product_id
               left join ty_product_style pst on pi.style_id = pst.style_id
               left join ty_product_provider pp on pi.provider_id = pp.provider_id
               left join ty_product_brand pb on pi.brand_id = pb.brand_id
               left join ty_product_season pss on pi.season_id = pss.season_id
               left join ty_product_color pc on ps.color_id = pc.color_id
               left join ty_product_size psi on ps.size_id = psi.size_id
               left join ty_product_category pca on pca.category_id = pi.category_id
               inner join (
                       select ti.product_id, ti.color_id, ti.size_id, ti.depot_id, ti.batch_id, sum(ti.product_number) as product_number,group_concat(distinct pba.batch_code) batch_codes
                       from ty_transaction_info ti
                left join ty_purchase_batch pba on pba.batch_id = ti.batch_id
                       where trans_status IN (1,2,4) 
                       group by product_id, color_id, size_id
               ) as t on ps.product_id = t.product_id and ps.color_id = t.color_id and ps.size_id = t.size_id
                where pi.product_id ".db_create_in($product_id_ary)."";
        $res = $this->db_r->query($sql)->result();
        
        return array('list' => $res, 'filter' => $filter);
    }
public function get_export_inventory(){
        $sql = "select pi.product_id, pi.product_name, pi.product_sn, pi.provider_productcode, pb.brand_name, 
                       pst.style_name, pss.season_name, pi.product_sex, pi.product_year,
                       pi.product_month, pi.is_promote, pi.shop_price, pi.market_price,
                       pi.promote_start_date, pi.promote_end_date, pi.promote_price, pi.keywords,
                       ps.color_id, ps.size_id, ps.gl_num, ps.consign_num,ps.provider_barcode, t.product_number, psi.size_name, pc.color_name, pca.category_name, t.batch_code,t.location_name
               from ty_product_sub ps
               left join ty_product_info pi on ps.product_id = pi.product_id
               left join ty_product_style pst on pi.style_id = pst.style_id
               left join ty_product_provider pp on pi.provider_id = pp.provider_id
               left join ty_product_brand pb on pi.brand_id = pb.brand_id
               left join ty_product_season pss on pi.season_id = pss.season_id
               left join ty_product_color pc on ps.color_id = pc.color_id
               left join ty_product_size psi on ps.size_id = psi.size_id
               left join ty_product_category pca on pca.category_id = pi.category_id
               INNER JOIN (
                       select ti.product_id, ti.color_id, ti.size_id, ti.depot_id, ti.batch_id, pba.batch_code, SUM(ti.product_number) AS product_number ,li.`location_name`
                       from ty_transaction_info ti
                LEFT JOIN ty_purchase_batch pba on pba.batch_id = ti.batch_id
		LEFT JOIN ty_location_info AS li ON ti.`location_id`=li.`location_id`
                       where trans_status IN (1,2,4) 
                       group by product_id, color_id, size_id, ti.batch_id,ti.`location_id`
		       HAVING product_number >0 
               ) as t on ps.product_id = t.product_id and ps.color_id = t.color_id and ps.size_id = t.size_id
                where 1 ";
        $res = $this->db_r->query($sql)->result();
        
        return array('list' => $res, 'filter' => $filter);
}
    
    public function get_inventory_batch($provider_id)
    {
            $sql = "SELECT batch_id,batch_code,batch_name FROM ".$this->db_r->dbprefix('purchase_batch');
            $where = " where 1 ";
            if (!empty($provider_id) && $provider_id != 0)
            {
                $where .= " and provider_id = " .$provider_id;
            }
            $sql .= $where . " ORDER BY batch_id DESC";
            $query = $this->db_r->query($sql);
            $list = $query->result();
            $query->free_result();
            $rs = array();
            $rs[0] = "请选择";
            foreach ($list as $row)
            {
                    $rs[$row->batch_id] = $row->batch_code.' - '.$row->batch_name;
            }
            return $rs;
    }
    
    public function get_inventory_brand($provider_id)
    {
            $sql = "SELECT pb.brand_id, pb.brand_name FROM ".$this->db_r->dbprefix('product_brand') ." pb";
            $sql .= " left join ty_provider_brand pbr on pbr.brand_id = pb.brand_id ";
            $where = " where 1 ";
            if (!empty($provider_id) && $provider_id != 0)
            {
                $where .= " and pbr.provider_id = " .$provider_id;
            }
            $sql .= $where . " ORDER BY brand_id DESC";
            $query = $this->db_r->query($sql);
            $list = $query->result();
            $query->free_result();
            return $list;
    }
    
    public function get_product_location($product_id, $color_id, $size_id){
        $sql = "SELECT 
  di.`depot_name`,
  li.`location_name`,
pba.batch_code,
  SUM(ti.product_number) AS num,
  ti.production_batch,
  ti.expire_date 
FROM
  `ty_transaction_info` ti 
  LEFT JOIN ty_depot_info di 
    ON ti.depot_id = di.depot_id 
  LEFT JOIN ty_location_info li 
    ON ti.location_id = li.`location_id` 
left join ty_purchase_batch pba on pba.batch_id = ti.batch_id
WHERE product_id = ".$product_id." 
  AND color_id = ".$color_id." 
  AND size_id = ".$size_id." 
  AND trans_status IN (1, 2, 4) 
GROUP BY ti.batch_id,ti.location_id 
HAVING num > 0";
        $query = $this->db_r->query($sql);
        $list = $query->result();
        $query->free_result();
        return array('list' => $list);
    }
    
    public function get_purchase_sub($purchase_id, $product_id){
        $sql = "SELECT ps.* FROM ty_purchase_main pm "
                . "INNER JOIN ty_purchase_sub ps ON pm.purchase_id = ps.purchase_id "
                . "WHERE ps.purchase_id = ? AND ps.product_id = ?";
        $row = $this->db_r->query($sql , array($purchase_id , $product_id) )->row();
        return $row;
    }
    
    public function update_purchase_sub($purchase_id, $purchase_sub_id, $update){
        //$sql = "UPDATE ty_purchase_sub SET expire_date = '".$exdate."' WHERE purchase_sub_id = '".$purchase_sub_id."' AND purchase_id = '".$purchase_id."'";
        $this->db->update('purchase_sub', $update, array('purchase_id' => $purchase_id, 'purchase_sub_id' => $purchase_sub_id));    
    }
    
}
?>
