<?php
class nav_model extends CI_Model {

    /**
     * @var CI_DB_active_record 
     */
    private $adoEx;

    function get_root_cats($slave = TRUE) {
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $sql = <<< SQL
SELECT 
    category_ids
  , nav_id
  , nav_name
  , nav_url 
FROM 
    ty_front_nav fn 
ORDER BY 
    fn.sort_order DESC;
SQL;
        $query = $this->adoEx->query($sql);
        return $query->result_array();
    }

    function get_rush_cats($nav_id, $slave = TRUE) {
        if (empty($nav_id)) return array();
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $sql = <<< SQL
SELECT 
    ri.rush_id
  , ri.rush_tag
  , ri.rush_index
  , ri.rush_brand
  , ri.`desc` AS rush_desc 
FROM 
    ty_rush_info ri
LEFT JOIN 
    ty_rush_product rp
USING (rush_id)
LEFT JOIN 
    ty_product_info pi
USING (product_id)
WHERE
    1
AND 
    ri.status = 1 
AND 
    ri.start_date <=  DATE_ADD(NOW(), INTERVAL 10 MINUTE)
AND 
    ri.end_date >= NOW()
AND
    ri.nav_id = ?
GROUP BY 
    ri.rush_id
ORDER BY 
    ri.sort_order DESC;
SQL;
        $query = $this->adoEx->query($sql, array($nav_id));
        $result = $query->result_array();
        return $result;
    }
    
    function get_cats($nav_id, $slave = TRUE) {
        if (empty($nav_id)) return array();
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $sql =<<< SQL
SELECT 
    pt2.type_id
  , pt2.type_code
  , pt2.type_name 
FROM 
    ty_rush_info ri
INNER JOIN 
    ty_rush_product rp
USING (rush_id)
INNER JOIN 
    ty_product_info pi
USING (product_id)
INNER JOIN 
    ty_product_type_link ptl
USING (product_id)
INNER JOIN 
    ty_product_type pt
USING (type_id)
INNER JOIN 
    ty_product_type pt2
ON 
    pt.parent_id2 = pt2.type_id
WHERE 
    1
AND 
    ri.status = 1 
AND 
    ri.start_date <=  DATE_ADD(NOW(), INTERVAL 10 MINUTE)
AND 
    ri.end_date >= NOW()
AND 
    pi.is_audit = 1
AND 
    ri.nav_id = ?
GROUP BY 
    pt2.type_id
ORDER BY 
    CHAR_LENGTH(pt2.type_name) ASC
  , pt2.sort_order DESC;
SQL;
        $query = $this->adoEx->query($sql, array($nav_id));
        return $query->result_array();
    }
    
    function get_size_cats($male = TRUE, $slave = TRUE) {
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $male = $male ? 88 : 42;
        $sql = <<< SQL
SELECT 
    psz.size_id
  , psz.size_sn
  , psz.size_name
  , pt.parent_id
FROM 
    ty_rush_info ri
INNER JOIN 
    ty_rush_product rp
USING (rush_id)
INNER JOIN 
    ty_product_info pi
USING (product_id)
INNER JOIN 
    ty_product_sub ps
USING (product_id)
INNER JOIN 
    ty_product_size psz
USING (size_id)
INNER JOIN
    ty_product_type_link ptl
USING (product_id)
INNER JOIN
    ty_product_type pt
USING (type_id)
WHERE 
    1
AND 
    ri.status = 1 
AND 
    ri.start_date <=  DATE_ADD(NOW(), INTERVAL 10 MINUTE)
AND 
    ri.end_date >= NOW()
AND 
    pi.is_audit = 1
AND
    pt.parent_id = ? 
GROUP BY 
    psz.size_id
ORDER BY 
    psz.sort_order ASC;
SQL;
        $query = $this->adoEx->query($sql, array($male));
        return $query->result_array();
    }
    
    function get_all_rush_cats($slave = TRUE) {
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $sql = <<< SQL
SELECT 
    ri.rush_id
  , pt3.type_id AS type_id_1
  , pt3.type_code AS type_code_1
  , pt3.type_name AS type_name_1
  , pt2.type_id AS type_id_2
  , pt2.type_code AS type_code_2
  , pt2.type_name AS type_name_2
  , pt.type_id AS type_id_3
  , pt.type_code AS type_code_3
  , pt.type_name AS type_name_3 
FROM 
    ty_rush_info ri
INNER JOIN 
    ty_rush_product rp
USING (rush_id)
INNER JOIN 
    ty_product_info pi
USING (product_id)
INNER JOIN 
    ty_product_type_link ptl
USING (product_id)
INNER JOIN 
    ty_product_type pt
USING (type_id)
INNER JOIN 
    ty_product_type pt2
ON 
    pt.parent_id2 = pt2.type_id
INNER JOIN
    ty_product_type pt3
ON 
    pt.parent_id = pt3.type_id
WHERE 
    1 
AND 
    ri.status = 1 
AND 
    ri.start_date <=  DATE_ADD(NOW(), INTERVAL 10 MINUTE)
AND 
    ri.end_date >= NOW()
AND 
    pi.is_audit = 1
GROUP BY
    ri.rush_id
  , pt3.type_id
  , pt2.type_id
  , pt.type_id
ORDER BY 
    ri.rush_id ASC
  , pt3.sort_order DESC
  , pt3.type_id
  , pt2.sort_order DESC
  , pt2.type_id
  , pt.sort_order DESC
  , pt.type_id;
SQL;
        $query = $this->adoEx->query($sql);
        return $query->result_array();
    }
    
    function get_rush_size_cats($rush_id, $type_id_1 = NULL, $type_id_2 = NULL, $type_id_3 = NULL, $slave = TRUE) {
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $where = (isset($type_id_1) ? " AND pt.parent_id = $type_id_1 " : "") . (isset($type_id_2) ? " AND pt.parent_id2 = $type_id_2 " : "") . (isset($type_id_3) ? " AND pt.type_id = $type_id_3 " : "");
        $sql = <<< SQL
SELECT
    pi.product_sex
  , pt.parent_id
  , psz.size_id
  , psz.size_sn
  , psz.size_name
FROM 
    ty_rush_product rp
INNER JOIN 
    ty_product_type_link ptl
USING (product_id)
INNER JOIN 
    ty_product_type pt
USING (type_id)
INNER JOIN 
    ty_product_info pi
USING (product_id)
INNER JOIN 
    ty_product_sub ps
USING (product_id)
INNER JOIN 
    ty_product_size psz
USING (size_id)    
WHERE 
    1 
AND 
    pi.is_audit = 1
AND 
    rp.rush_id = ?
$where
GROUP BY 
    pi.product_sex
  , psz.size_id
ORDER BY 
    pi.product_sex 
  , psz.sort_order DESC
;
SQL;
        $query = $this->adoEx->query($sql, array($rush_id));
        return $query->result_array();
    }
    
    function get_cat_brands($slave = TRUE) {
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $sql = <<< SQL
SELECT  
  pt1.type_id    AS type_id_1,
  pt1.type_name  AS type_name_1,
  pt.type_id     AS type_id_2,
  pt.type_name   AS type_name_2,
  pb.brand_id,
  pb.brand_name,
  pi.product_sex, 
  IF(SUM(GREATEST(ps.gl_num-ps.wait_num,0))+SUM(GREATEST(ps.consign_num,0)) >0,1,0) AS num  
FROM ty_product_info PI 
  INNER JOIN ty_product_type_link ptl
    USING (product_id) 
      INNER JOIN ty_product_sub ps USING (product_id) 
  INNER JOIN ty_product_type pt
    USING (type_id)
  INNER JOIN ty_product_brand pb
    USING (brand_id)
  INNER JOIN ty_product_type pt1
    ON pt.parent_id = pt1.type_id 
  INNER JOIN ty_product_provider pp 
  USING(provider_id)
WHERE 
ps.is_on_sale = 1 
AND 
pi.is_audit = 1 
AND 
pb.is_use = 1 
AND 
pp.is_use = 1 
AND 
(ps.consign_num>0 OR ps.consign_num=-2 OR ps.gl_num>ps.wait_num) 
GROUP BY 
pt1.type_id , 
pt.type_id , 
pb.brand_id , 
pi.product_sex 
HAVING 
num > 0 
ORDER BY 
pt1.sort_order , 
pt.sort_order , 
pb.sort_order , 
pi.product_sex;
SQL;
        $query = $this->adoEx->query($sql);
        return $query->result_array();
    }

    function get_cat_providers($slave = TRUE) {
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $sql = <<< SQL
SELECT 
pp.provider_id,
  pt1.type_id    AS type_id_1,
  pt1.type_name  AS type_name_1,
  pt.type_id     AS type_id_2,
  pt.type_name   AS type_name_2,
  pb.brand_id,
  pb.brand_name,
  pi.product_sex, 
  IF(SUM(GREATEST(ps.gl_num-ps.wait_num,0))+SUM(GREATEST(ps.consign_num,0)) >0,1,0) AS num  
FROM ty_product_info `pi` 
  INNER JOIN ty_product_type_link ptl
    USING (product_id) 
      INNER JOIN ty_product_sub ps USING (product_id) 
  INNER JOIN ty_product_type pt
    USING (type_id)
  INNER JOIN ty_product_brand pb
    USING (brand_id)
  INNER JOIN ty_product_type pt1
    ON pt.parent_id = pt1.type_id 
  INNER JOIN ty_product_provider pp 
  USING(provider_id)
WHERE 
ps.is_on_sale = 1 
AND 
pi.is_audit = 1 
AND 
pb.is_use = 1 
AND 
pp.is_use = 1 
AND 
(ps.consign_num>0 OR ps.consign_num=-2 OR ps.gl_num>ps.wait_num) 
GROUP BY pi.provider_id, 
pt1.type_id , 
pt.type_id , 
pb.brand_id , 
pi.product_sex 
HAVING 
num > 0 
ORDER BY 
pt1.sort_order DESC , 
pt.sort_order DESC , 
pb.sort_order DESC , 
pi.product_sex ASC ;
SQL;
        $query = $this->adoEx->query($sql);
        return $query->result_array();
    }

    
    function get_cat_sizes($slave = TRUE) {
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $sql = <<< SQL
SELECT 
    pt.parent_id AS type_id_1
  , pt.parent_id2 AS type_id_2
  , pt.type_id AS type_id_3
  , psz.size_id
  , psz.size_name
  , pi.product_sex
FROM 
    ty_rush_info ri
INNER JOIN 
    ty_rush_product rp
USING (rush_id)
INNER JOIN 
    ty_product_info pi
USING (product_id)
INNER JOIN 
    ty_product_type_link ptl
USING (product_id)
INNER JOIN 
    ty_product_type pt
USING (type_id)
INNER JOIN 
    ty_product_sub ps
USING (product_id)
INNER JOIN 
    ty_product_size psz
USING (size_id)
WHERE 
    1 
AND 
    ri.status = 1 
AND 
    ri.start_date <=  DATE_ADD(NOW(), INTERVAL 10 MINUTE)
AND 
    ri.end_date >= NOW()
AND 
    pi.is_audit = 1
GROUP BY 
    pt.parent_id
  , pt.parent_id2
  , pt.type_id
  , psz.size_id
  , pi.product_sex
ORDER BY
    pt.parent_id
  , pt.parent_id2
  , pt.type_id
  , psz.sort_order DESC
  , pi.product_sex
;
SQL;
        $query = $this->adoEx->query($sql);
        return $query->result_array();
    }
    
    function comfort_product_type_cat_content($type_ids, $cat_content) {
        $sql = <<< SQL
UPDATE 
    ty_product_type
SET
    cat_content = ?
WHERE 
   type_id IN 
SQL;
        $sql .= "('" . implode('\',\'', array_keys($type_ids)) . "')";
        $this->db->query($sql, array($cat_content));
    }
//生成供应商页分类
    function comfort_provider_product_type_cat_content($provider_id, $cat_content) {
        $sql = "UPDATE ty_product_provider SET cat_content = ? WHERE provider_id = ".$provider_id;
        $this->db->query($sql, array($cat_content));
    }
//生成品牌页分类
    function comfort_brand_product_type_cat_content($brand_id, $cat_content) {
        $sql = "UPDATE ty_product_brand SET cat_content = ? WHERE brand_id = ".$brand_id;
        $this->db->query($sql, array($cat_content));
    }
    
    function get_admin_cats($slave = TRUE) {
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $sql = <<< SQL
SELECT 
    c.category_id AS cat_id
  , concat(c.category_id) AS cat_code
  , c.category_name AS cat_name
  , '' AS measure_unit
  , c.parent_id
  , c.is_use AS is_show
  , 0 AS show_in_nav
  , 0 AS grade
  , c.sort_order
  , count(s.category_id) AS has_children
FROM 
    ty_product_category c
INNER JOIN 
    ty_product_category s
ON s.parent_id = c.category_id
GROUP BY 
    c.category_id
ORDER BY 
    c.parent_id
  , c.sort_order DESC
;
SQL;
        $query = $this->adoEx->query($sql);
        return $query->result_array();
    }
    
    function get_cat_goods_numbers($slave = TRUE) {
        $this->adoEx = $slave ? $this->db_r : $this->db;
        $sql = <<< SQL
SELECT 
    g.category_id AS cat_id
  , COUNT(1) AS goods_num
FROM
    ty_product_info AS g
GROUP BY 
    category_id
;
SQL;
        $query = $this->adoEx->query($sql);
        return $query->result_array();
    }
}