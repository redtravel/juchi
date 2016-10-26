<?php
/**
* 将一维数组以多维数组的形式格式化
* @param $arr 待处理的源数据
* @param $parent_id 初始父ID
* @param $f_cat_id 主键名称
* @param $f_parent_id 父节点字段名称
*/
function category_tree($arr = array(), $parent_id = 0, $f_cat_id = 'category_id', $f_parent_id = 'parent_id')
{	
	$tree = array();
	foreach ($arr as $key => $value) {
		if ($value->$f_parent_id == $parent_id) {
			$value->sub_items = category_tree($arr, $value->$f_cat_id, $f_cat_id, $f_parent_id);
			$tree[] = $value;
		}
	}
	return $tree;
}

/**
* 格式化分类名称，以空白进行缩进
* @param $arr category_array返回结果
* @param $string 缩进空白
* @return array()
*/
function category_flatten($arr = array(), $space = '&nbsp&nbsp', $level = 0)
{
	static $flatten = array();
	foreach ($arr as $key => $value) {
		$value->level = $level;
		$value->level_space = str_repeat($space, $level);
		$sub_items = $value->sub_items;
		unset($value->sub_items);
		$flatten[] = $value;
		category_flatten($sub_items, $space, $level + 1);
	}
	return $flatten;
}

function form_product_category($form_name, $arr, $selected_id=0, $ext="", $header=array())
{
	$output = "<select name=\"{$form_name}\" {$ext}>\n";
	if($header){
		foreach($header as $k=>$v){
			$output .= "\t<option value='{$k}'>{$v}</option>\n";
		}
	}
	foreach($arr as $group){
		if($group->sub_items){
			$output .= "\t<optgroup label=\"{$group->cate_code}{$group->category_name}\">\n";
				foreach ($group->sub_items as $v) {
					$selected = $v->category_id == $selected_id?'selected':'';
					$output .= "\t\t<option value=\"{$v->category_id}\" {$selected}>{$v->cate_code}{$v->category_name}</option>\n";
				}

			$output .= "\t</optgroup>\n";
		}
		
	}
	$output .= "</select>";
	return $output;
}

function form_product_type($form_name, $arr, $selected_id=0, $ext="", $header=array())
{
	$output = "<select name=\"{$form_name}\" {$ext}>\n";
	if($header){
		foreach($header as $k=>$v){
			$output .= "\t<option value='{$k}'>{$v}</option>\n";
		}
	}
	foreach($arr as $group){
		if($group->sub_items){
			$output .= "\t<optgroup label=\"{$group->type_name}\">\n";
				foreach ($group->sub_items as $v) {
					$selected = $v->type_id == $selected_id?'selected':'';
					$output .= "\t\t<option value=\"{$v->type_id}\" {$selected}>{$v->type_name}</option>\n";
				}

			$output .= "\t</optgroup>\n";
		}
		
	}
	$output .= "</select>";
	return $output;
}
