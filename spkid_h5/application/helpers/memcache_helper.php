<?php
// 如果某memcache变量被多处所用，则定义成帮助函数

// 取所有分类，附带该分类的数量
function memcache_get_category_number()
{
	$CI=&get_instance();
	if(($category_number=$CI->memcache->get('category-number'))===FALSE){
		$category_number = $CI->product_model->category_number();
		$CI->memcache->save('category-number',$category_number,CACHE_TIME_CATLIST);
	}
	return $category_number;
}

// 取某一品牌记录
function memcache_get_brand($brand_id)
{
	$CI=&get_instance();
	if (($brand=$CI->memcache->get('brand-'.$brand_id))===FALSE) {
		$brand = $CI->product_model->brand_info($brand_id);
		if($brand) $CI->memcache->save('brand-'.$brand_id,$brand,CACHE_TIME_BRANDLIST);
	}
	return $brand;
}

// 所有品牌
function memcache_get_brand_list()
{
	$CI=&get_instance();
	if(($brand_list = $CI->memcache->get('brand-list'))===FALSE){
		$brand_list = $CI->product_model->brand_list();
		$CI->memcache->save('brand-list',$brand_list,CACHE_TIME_BRANDLIST);
	}
	return $brand_list;
}

// 所有赠品
function memcache_get_gifts ()
{
	$CI=&get_instance();
	if(($gifts_list=$CI->memcache->get('gifts-list'))===FALSE){
		$CI->load->model('cart_model');
		$gifts_list=$CI->cart_model->all_gifts(array('is_use'=>1,'end_date >='=>$CI->time,'start_date <='=>$CI->time));
		$CI->memcache->save('gifts-list',$gifts_list,CACHE_TIME_COMMON);
	}
	foreach($gifts_list as $key=>$g){
		if($g->start_date>$CI->time||$g->end_date<$CI->time) unset($key);
	}
	return $gifts_list;
}