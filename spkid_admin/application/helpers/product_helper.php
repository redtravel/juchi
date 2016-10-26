<?php
// 商品辅助函数
function format_product(&$p)
{
	static $now;
	if(!$now) {
		$CI = &get_instance();
		$now = time();//$CI->time
	}
	
	$p->is_promote = $p->is_promote && strtotime($p->promote_start_date)<=time() && strtotime($p->promote_end_date)>=time() ;
	$p->product_price = $p->is_promote ? $p->promote_price : $p->shop_price;
}

function log_product_price($product, $update, $product_id){
	$product = (array)$product;
	$update = (array)$update;
	if (isset($product['shop_price']) && isset($update['shop_price']) && $product['shop_price']==$update['shop_price']
		&& isset($product['market_price']) && isset($update['market_price']) && $product['market_price']==$update['market_price']
//		&& isset($product['cost_price']) && isset($update['cost_price']) && $product['cost_price']==$update['cost_price']
//		&& isset($product['consign_type']) && isset($update['consign_type']) && $product['consign_type']==$update['consign_type']
//		&& isset($product['consign_price']) && isset($update['consign_price']) && $product['consign_price']==$update['consign_price']
//		&& isset($product['consign_rate']) && isset($update['consign_rate']) && $product['consign_rate']==$update['consign_rate']
	) {
		return;
	}

	$arr = array_merge($product, $update);
	$CI = &get_instance();
	$CI->product_model->insert_price_record(array(
			'product_id' => $product_id,
			'shop_price' => $arr['shop_price'],
			'market_price' => $arr['market_price'],
//			'cost_price' => $arr['cost_price'],
//			'consign_type' => $arr['consign_type'],
//			'consign_price' => $arr['consign_price'],
//			'consign_rate' => $arr['consign_rate'],
			'create_admin' => $CI->session->userdata('admin_id'),
			'create_date' => date('Y-m-d H:i:s')
		));
	return;
}

function format_gallery_sub($all_gallery, $all_sub)
{
	$result = array();
	foreach ($all_sub as $key => $value) {
		if (!isset($result[$value->color_id])) {
			$result[$value->color_id] = array(
				'gallery_list' => array(),
				'size_list' => array(),
				'color_info' => array('color_id'=>$value->color_id,'color_name'=>$value->color_name,'sort_order'=>$value->sort_order),
			);
		}
		$result[$value->color_id]['size_list'][] = $value;
	}
	foreach ($all_gallery as $key => $value) {
		if (!isset($result[$value->color_id])) {
			$result[$value->color_id] = array(
				'gallery_list' => array(),
				'size_list' => array(),
				'color_info' => array('color_id'=>$value->color_id,'color_name'=>$value->color_name,'sort_order'=>0),
			);
		}
		$result[$value->color_id]['gallery_list'][] = $value;
	}
	return $result;
}

function attach_gallery(&$product_list)
{
	
	$product_list = index_array($product_list, 'product_id');
	$product_ids = array_keys($product_list);
	if (!$product_ids) {
		return $product_list;
	}
	$CI = &get_instance();
	$CI->load->model('product_model');
	$gallery_list = $CI->product_model->all_gallery(array('image_type'=>'default'),$product_ids);
	foreach ($gallery_list as $gallery) {
		if (isset($product_list[$gallery->product_id]->gallery)) {
			continue;
		}
		$product_list[$gallery->product_id]->gallery = $gallery;
	}
	return $product_list;
}

function attach_sub(&$product_list, $depot_id=0)
{
	$product_list = index_array($product_list, 'product_id');
	$product_ids = array();
	foreach($product_list as $product_id=>$product){
		$product_ids[] = $product_id;
		$product_list[$product_id]->sub_list = array();
		$product_list[$product_id]->cs_list = array();
		$product_list[$product_id]->sub_total = 0;
		$product_list[$product_id]->sub_gl = 0;
		$product_list[$product_id]->sub_consign = 0;
	}
	if (!$product_ids) {
		return $product_list;
	}
	$CI = &get_instance();
	$CI->load->model('product_model');
        if ($depot_id > 0){
            $sub_list = $CI->product_model->all_depot_sub($product_ids, $depot_id);
        } else {
            $sub_list = $CI->product_model->all_sub(array(),$product_ids);
        }
	foreach ($sub_list as $sub) {
		$product = $product_list[$sub->product_id];

		$product->sub_list[] = $sub;
		if (!isset($product->cs_list[$sub->color_id])) {
			$product->cs_list[$sub->color_id] = array(
				'color_id'=>$sub->color_id,
				'color_name'=>$sub->color_name,
				'is_pic'=>$sub->is_pic,
				'sub_list'=>array()
			);
		}
		$product->cs_list[$sub->color_id]['sub_list'][] = $sub;

		// 累加库存
		if ($product->sub_total!=-2) {
			$product->sub_total += max(0,$sub->gl_num-$sub->wait_num);
			$product->sub_gl += max(0,$sub->gl_num-$sub->wait_num);

			if($sub->consign_num==-2){
				$product->sub_total=-2;
				$product->sub_consign=-2;
			}elseif($sub->consign_num>0){
				$product->sub_total += $sub->consign_num;
				$product->sub_consign += $sub->consign_num;
			}
		}else{
			$product->sub_gl += max(0,$sub->gl_num-$sub->wait_num);
		}
		$product_list[$sub->product_id] = $product;
	}
}

function attach_tmall_num_iid(&$product_list)
{
    $product_list = index_array($product_list, 'product_id');
	$product_ids = array_keys($product_list);
	if (!$product_ids) {
		return $product_list;
	}
	$CI = &get_instance();
	$CI->load->model('tmall_model');

    foreach($CI->tmall_model->all_tmall_info($product_ids) as $tmall)
    {
        $product_list[$tmall->product_id]->tmall_num_iid = $tmall->num_iid;
    }
	return $product_list;
}

function attach_info(&$product_list)
{
	$info_keys = array('product_name','product_sn','provider_productcode',
	'brand_id','category_id');
	$product_list = index_array($product_list, 'product_id');
	$product_ids = array_keys($product_list);
	if (!$product_ids) return $product_list;

	foreach($product_list as $product_id=>$product){
		foreach($info_keys as $key)
		$product_list[$product_id]->$key = '';
	}
	
	$CI = &get_instance();
	$CI->load->model('product_model');
	$info_list = $this->product_model->all_product(array('product_id'=>$product_ids));
	$info_list = index_array($info_list, 'product_id');
	foreach ($product_list as $pid => $p) {
		if(!isset($info_list[$pid])) continue;
		$info = $info_list[$pid];
		foreach ($info_keys as $key) {
			$p->$key = $info->$key;
		}
		$product_list[$pid] = $p;
	}
}
/**
 * 根据商品和批次和新商品来获取新商品的批次。并添加上
 *
 */
if (!function_exists("create_product_with_product")) {

	function create_ctb_product_with_product($product_id, $row, $provider_id=SYS_CTB_PROVIDER_ID, $use_new_provider_barcode = TRUE) {
		$CI = &get_instance();
		$ctb_batch_ids = explode(',',SYS_CTB_BATCH_ID);
		$new_product = create_product_with_product($product_id, $provider_id, $use_new_provider_barcode );//生成买断商品主信息、次要信息、图片信息

		$sql = "select * from ty_product_cost where product_id=".$new_product['product_id']
			.' and provider_id='.SYS_CTB_PROVIDER_ID ;
		$result = $CI->db_r->query($sql)->row_array();
		if ( !empty($result) )
		{
			foreach( $result AS $res ) if( $row['consign_price'] == $res['consign_price'] ) 
				$new_product['batch_id'] = $res['batch_id'];
		}
		if( !isset($new_product['batch_id']) ) 
		{
			$new_product['batch_id'] = $ctb_batch_ids[0];
			$keys = Array( 'consign_price', 'cost_price', 'consign_rate', 'product_cess' );
			foreach( $keys AS $key ) $data[$key] = $row[$key];
			$data = Array('batch_id'=>$new_product['batch_id'], 'product_id'=>$new_product['product_id']
					,'provider_id'=>SYS_CTB_PROVIDER_ID ,'create_admin'=>1,'create_date'=>date('Y-m-d H:i:s'));
			$CI->load->model('product_model');
			$CI->product_model->insert_product_cost( $data );//生成成本价
		}
		return $new_product;
	}

}

function create_product_with_product($product_id, $provider_id, $use_new_provider_barcode = TRUE) {
        $result = array();
        $CI = &get_instance();
	    $CI->load->model('product_model');
        $related_id = $product_id;
        $product_id = $CI->product_model->has_product($related_id, $provider_id);//检查此商品是否已生成过新款号
        $product_sn = empty($product_sn) || empty($product_id["product_sn"]) ? NULL : $product_id["product_sn"];
        $product_id = empty($product_id) || empty($product_id["product_id"]) ? NULL : $product_id["product_id"];
		
        if (empty ($product_id)) {
            $product_info = $CI->product_model->get_product_info($related_id);//获取老商品的主信息
            $product_subs = $CI->product_model->get_product_subs($related_id);//获取老商品的次要信息
            $product_galleries = $CI->product_model->get_product_galleries($related_id);//获取老商品的图片信息
            if (empty($product_info)) return FALSE;
            unset($product_info["product_id"]);
            $product_info["related_id"] = $related_id;
            $product_sn = "ZZ" . substr($product_info["product_sn"], 0x0002);
            $product_info["product_sn"] = $product_sn;
            $product_info["provider_id"] = $provider_id;
            $product_id = $CI->product_model->add_product_info($product_info);//生成买断商品主信息
            if (empty($product_id)) return FALSE;
        } else {
            $product_subs = $CI->product_model->get_product_subs($related_id, $product_id);//检查新商品在老商品中不存在的色款码
            $product_galleries = $CI->product_model->get_product_galleries($related_id, $product_id);//检查新商品在老商品中不存在的图片
        }
		
        foreach ($product_subs as $key => $item) {
            unset($product_subs[$key]["sub_id"]);
            if ($use_new_provider_barcode) {
                $product_subs[$key]["provider_barcode"] = $product_sn . " " . $product_subs[$key]["color_sn"] . " " . $product_subs[$key]["size_sn"];
            }
            unset($product_subs[$key]["color_sn"]);
            unset($product_subs[$key]["size_sn"]);
            $product_subs[$key]["product_id"] = $product_id;
            $product_subs[$key]["gl_num"] = 0;
            $product_subs[$key]["consign_num"] = 0;
            $product_subs[$key]["wait_num"] = 0;
        }
        $result["product_id"] = $product_id;
        $result["related_id"] = $related_id;
        $result["product_sub_count"] = $CI->product_model->add_product_subs($product_subs);//插入买断商品次要信息
		// 复制图片开始
	$base_dir = CREATE_IMAGE_PATH;
	$sub_dir = GALLERY_PATH . intval(($product_id-($product_id%100))/100);//图片子目录
	if(!file_exists($base_dir.$sub_dir)) mkdir($base_dir.$sub_dir);
        foreach ($product_galleries as $key => $item) {
            unset($product_galleries[$key]["image_id"]);
            $product_galleries[$key]["product_id"] = $product_id;
            foreach ($item as $id => $val) {
                if (strpos($key, "img_") === 0 && isset($val) && file_exists($base_dir . $val)) {
                    while (True) {
                        //$ext = "_" . str_replace(substr($key, 4), "_", "")  . "_" . substr($val, -4);
						$ext = "_" . str_replace("_", "x", substr($key, 4))  . "_" . substr($val, -4);
                        $new_basename = $product_galleries['product_id'] . '_' . $product_galleries['color_id'] . '_' . substr($product_galleries['image_type'], 0, 1) . '_' . mt_rand(10000, 99999);
                        if (!file_exists($base_dir . $sub_dir . '/' . $new_basename . $ext)) {
                            break;
                        }
                    }
                    $product_galleries[$key][$id] = $base_dir . $sub_dir . '/' . $new_basename . $ext;
                    copy($base_dir . $val, $base_dir . $sub_dir . '/' . $new_basename . $ext);
                }
            }
        }
        $result["product_gallery_count"] = $CI->product_model->add_product_galleries($product_galleries);
        return $result;
}
