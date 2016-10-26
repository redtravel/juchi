<?php

class Etao_model extends CI_Model {
    /*
     * 获取促销活动信息列表
     */

    public function get_rush_list() {
        $now = time();
        $sql = "SELECT rush_id,image_ing_url,start_date,end_date,jump_url,rush_discount,`desc`,rush_index
                FROM ty_rush_info
                WHERE STATUS = 1  AND end_date>NOW()  AND  start_date<= NOW()  AND image_ing_url != ''
                AND image_ing_url IS NOT NULL AND jump_url = '' 
                ORDER BY start_date DESC, rush_id ASC;";
        $query = $this->db_r->query($sql);
        $list = $query->result_array();
        return $list;
    }

    /**
     * 获取新增的的商品信息
     * @$nearly_update_add 最近更新或增加
     * @return type 
     */
    public function get_add_pro($nearly_update_add=0) {
        $where = " where 1=1 ";
        if($nearly_update_add==1){
            $where =$where." and unix_timestamp(p.update_time)>".(time()-30*60);
        }
        $sql = "SELECT DISTINCT(p.product_id),product_name,promote_start_date,promote_end_date,promote_price,shop_price,pg.img_318_318,
                product_desc,brand_id,size_image,c.category_name,p.category_id 
                FROM ty_product_info AS p 
				LEFT JOIN ty_product_sub AS ps ON p.product_id = ps.product_id 
                LEFT JOIN ty_product_category as c ON p.category_id = c.category_id 
                LEFT JOIN ty_product_gallery AS pg ON p.product_id = pg.product_id ". $where.
                " and pg.img_318_318 <> '' and pg.image_type = 'default'  AND p.is_onsale = 1 AND p.is_stop = 0 AND p.shop_price > 0 AND ps.gl_num > 0 AND ps.is_on_sale > 0 ".
                " GROUP By product_id";
        $query = $this->db_r->query($sql);
        $list = $query->result_array();
        return $list;
    }

    /**
     * 获取品牌信息
     * @return type 
     */
    public function getbrand() {
        $brand_sql = "select * from ty_product_brand order by brand_id";
        $query = $this->db_r->query($brand_sql);
        $list = $query->result_array();
        foreach ($list as $brandvalue) {
            $brand_info[$brandvalue['brand_id']] = $brandvalue['brand_name'];
        }
        return $brand_info;
    }

    /**
     * 根据ids获取商品信息
     * 
     * @param array   $goods_ids
     * @param boolean $is_dele   是否无库存
     * @return type 
     */
    public function getpro($goods_ids, $is_delete = false) {
        foreach ($goods_ids as $val) {
            @$need_add_where.=$dou . $val;
            $dou = ',';
        }
        $sql = "SELECT DISTINCT(p.product_id),product_name,promote_start_date,promote_end_date,promote_price,market_price,shop_price,
                pg.img_318_318,p.category_id,product_desc,brand_id,size_image,c.category_name,rp.rush_id
                FROM ty_product_info AS p
                LEFT JOIN ty_product_sub AS ps ON p.product_id = ps.product_id
                LEFT JOIN ty_product_category AS c ON p.category_id = c.category_id
                LEFT JOIN ty_product_gallery AS pg ON p.product_id = pg.product_id
                LEFT JOIN ty_rush_product AS rp ON p.product_id = rp.product_id
                WHERE ps.is_on_sale = '1'
                    AND pg.img_318_318 <> ''  and pg.image_type = 'default' 
                    AND p.is_stop = '0'
                    AND p.shop_price > '0' ";
        $sql .= $is_delete ? "  AND ps.gl_num = 0 " : "  AND ps.gl_num > 0 ";
        $sql .= "AND p.product_id IN(" . $need_add_where . ")
                GROUP BY product_id";
        $query = $this->db_r->query($sql);
        $list = $query->result_array();
        return $list;
    }

    /**
     * 根据ids获取商品信息
     * 
     * @param array   $goods_ids
     * @param boolean $is_dele   是否无库存
     * @return type 
     */
    public function getcat($parent_id=0) {
        $sql = "select * from ty_product_category where is_use=1 and parent_id=".$parent_id." order by sort_order";
        $query = $this->db_r->query($sql);
        $list = $query->result_array();
        return $list;
    }
	/**
 * 格式化商品价格
 *
 * @access  public
 * @param   float   $price  商品价格
 * @return  string
 */
public function price_format($price, $change_price = true)
{
    $price = number_format($price, 2, '.', '');
    return $price;
}

}

?>
