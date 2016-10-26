<?php

class rush_third_model extends CI_Model
{
	public function get_imgid(){
		$sql = "SELECT rush_id
			,(SELECT GROUP_CONCAT(product_id) FROM ty_rush_product WHERE rush_id = ri.rush_id) AS rush_ids
			,(SELECT COUNT(*) FROM ty_rush_product WHERE rush_id = ri.rush_id) AS rush_count
			FROM ty_rush_info AS ri WHERE STATUS=1 AND ri.start_date <= date_add(NOW(),INTERVAL 30 MINUTE) AND ri.end_date >= NOW()";
		$query = $this->db_r->query($sql);
		$result = $query->result_array();
		return $result;
	
	}
	public function update_new_goods($rush_id,$ids){
		$sql = "update ty_new_goods_num set rush_id = ".$rush_id . " , update_date = now()  where goods_id in (". $ids ." )";
	
		$query = $this->db->query($sql);
	}
	public function get_new_imgid(){
		$sql = "SELECT pg.image_id,ps.product_id,ps.color_id,ps.is_on_sale,pg.img_318_318,                
        IF(SUM(GREATEST(ps.gl_num-ps.wait_num,0))+SUM(GREATEST(ps.consign_num,0)) >0,1,0) AS num,
        CONCAT(',',GROUP_CONCAT(ps.size_id),',') AS size_ids,
        pi.product_sn,pi.product_name,b.brand_id,b.brand_name,IFNULL(ptl.type_id,0) AS type_id
        FROM 
        ty_product_sub AS ps
        LEFT JOIN  ty_product_info AS pi  ON pi.product_id=ps.product_id
        LEFT JOIN ty_product_brand AS b ON b.brand_id=pi.brand_id
        LEFT JOIN ty_product_gallery AS pg ON ps.product_id=pg.product_id AND ps.color_id=pg.color_id
        LEFT JOIN ty_product_type_link AS ptl ON ptl.product_id=pi.product_id
        WHERE ps.is_on_sale=1 AND pg.image_type='default' AND b.is_use=1
        GROUP BY ps.product_id,ps.color_id";
		$query = $this->db_r->query($sql);
		$result = $query->result_array();
		return $result;
	
	}
	public function insert_new_imgid($result){
		$baseInsertSql = 'INSERT IGNORE INTO ty_new_goods_num( img_id,goods_id,color_id,on_sale,img_url,num,update_date,size_ids,
			goods_sn,goods_name,brand_id,brand_name,type_id ) values ';
		$i=0; $sql = '';
		foreach( $result AS $key=>$row ){
			if( $i == NGN_INSERT_NUM_EACH ){
				$this->db->query ( $baseInsertSql . $sql );
				$sql = ''; $i=0;
			}
			if( $i > 0 ) $sql .= ",";
			$sql .= "(".$row["image_id"].",".$row["product_id"].",".$row["color_id"].",".$row["is_on_sale"].",'".$row["img_318_318"]."',".$row["num"].",CURRENT_TIMESTAMP,
				'".$row["size_ids"]."','".$row["product_sn"]."','".addslashes($row["product_name"])."',".$row["brand_id"].",'".addslashes($row["brand_name"])."',
				".$row["type_id"].")";
			$i++;
		}
		if( $i > 0 )$this->db->query ( $baseInsertSql . $sql );
	
	}
	//针对所有的新表中的img_id，计算on_sale属性，库存，size_id
	public function increment_imgid(){
		$sql = "SELECT gn.img_id,ps.product_id,ps.color_id,IF(SUM(GREATEST(ps.gl_num-ps.wait_num,0))+SUM(GREATEST(ps.consign_num,0)) >0,1,0) AS num2,
			ps.is_on_sale,CONCAT(',',GROUP_CONCAT(ps.size_id),',') AS size_ids2,gn.num,gn.size_ids
			FROM ty_new_goods_num AS gn INNER JOIN ty_product_sub AS ps ON gn.goods_id=ps.product_id AND gn.color_id=ps.color_id 
			WHERE ps.is_on_sale=1 
			GROUP BY ps.product_id,ps.color_id ";
		$query = $this->db_r->query($sql);
		$result = $query->result_array();
		return $result	;

		
	}
	public function increment_update($item,$cart_expired_time){
		$current_timestamp = date('Y-m-d H:i:s');
		$sql = "
			UPDATE ty_new_goods_num AS ngl,
			(
            SELECT ngl2.img_id,ps.product_id,ps.color_id,
            if(IFNULL(SUM(GREATEST(ps.gl_num-ps.wait_num,0))+SUM(GREATEST(ps.consign_num,0)),0)
            - (
            SELECT ifnull(sum(product_num),0) from ty_front_cart ct 
			WHERE ct.product_id=ngl2.goods_id and ct.color_id=ngl2.color_id 
			 and ct.package_id=0 and UNIX_TIMESTAMP(ct.update_date)  >=unix_timestamp()-".$cart_expired_time."
            ) > 0,1,0)
            AS num2,ps.is_on_sale,
            CONCAT(',',GROUP_CONCAT(ps.size_id),',') AS size_ids2,ngl2.num,ngl2.size_ids 
            FROM ty_new_goods_num AS ngl2 INNER JOIN ty_product_sub AS ps ON ngl2.goods_id=ps.product_id AND ngl2.color_id=ps.color_id 
            WHERE ps.is_on_sale=1  AND ngl2.img_id ".$this->db_create_in($item)."
            GROUP BY ps.product_id,ps.color_id 
            order by null
			) AS tmp_gl SET 
			ngl.num=tmp_gl.num2,ngl.on_sale=tmp_gl.is_on_sale,ngl.size_ids=tmp_gl.size_ids2,update_date='".$current_timestamp."'
			WHERE ngl.img_id=tmp_gl.img_id";
		$query = $this->db->query($sql);
		return true;	

	}
	public function increment_update_new($imgIdsIn){
		$current_timestamp = date('Y-m-d H:i:s');
		$sql = "UPDATE ty_new_goods_num set on_sale=0,update_date='".$current_timestamp."' where on_sale=1 and update_date < '".$current_timestamp."'".$imgIdsIn;
		
		$query = $this->db->query($sql);
		return true;
		
	
	}
	public function db_create_in($item_list, $field_name = ''){
		if (empty($item_list))
		{
			return $field_name . " IN ('') ";
		}else{
			if (!is_array($item_list))
			{
				$item_list = explode(',', $item_list);
			}
			$item_list = array_unique($item_list);
			$item_list_tmp = '';
			foreach ($item_list AS $item){
				if ($item !== '')
				{
					$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
				}
			}
			if (empty($item_list_tmp))
			{
				return $field_name . " IN ('') ";
			}
			else
			{
				return $field_name . ' IN (' . $item_list_tmp . ') ';
			}
		}	
}
}

?>