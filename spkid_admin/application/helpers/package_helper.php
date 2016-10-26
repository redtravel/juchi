<?php

/**
* 获取礼包的可执行操作
*/
function get_package_perm($package)
{
	$perms = array();
	//$perms['edit'] = ($package->package_status==0 && check_perm('package_edit')) || ($package->package_status==1 && check_perm('package_editpro'));
	$perms['edit'] = check_perm('package_edit');
	$perms['check'] = $package->package_status==0 && check_perm('package_audit');
	$perms['over'] = $package->package_status==1 && check_perm('package_stop');
	$perms['config'] = $package->package_status==0 && check_perm('package_edit');
	$perms['delete'] = $package->package_status==0 && check_perm('package_edit');
	return $perms;
}

function unpack_package_config($config)
{
	if(empty($config)) return array();
	$result = array();
	$config = explode('&&&', $config);
	foreach ($config as $item) {
		$item = explode('|||', $item);
		if (count($item)!=4) continue;
		$result[] = $item;
	}
	return $result;
}

function split_area_product($all_area, $all_product)
{
	$result = array();
	foreach ($all_area as $area) {
		if ($area->area_type==2) continue;
		$result[$area->area_id] = array('area_id'=>$area->area_id, 'area_name'=>$area->area_name, 'product_list' => array());
	}
	foreach ($all_product as $product) {
		$result[$product->area_id]['product_list'][] = $product;
	}

	return $result;
}


/**
* 获取折扣礼包的可执行操作
*/
function get_package_discount_perm($package)
{
	$perms = array();
	$perms['edit'] = check_perm('package_discount_edit');
	$perms['check'] = $package->pag_dis_status==0 && check_perm('package_discount_audit');
	$perms['over'] = $package->pag_dis_status==1 && check_perm('package_discount_stop');
	$perms['config'] = $package->pag_dis_status==0 && check_perm('package_discount_edit');
	$perms['delete'] = $package->pag_dis_status==0 && check_perm('package_discount_edit');
	return $perms;
}