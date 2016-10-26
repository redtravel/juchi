<?php

/**
 * 验证出库类型
 * @param type $doc_type
 */
function vali_doc_type_out($doc_type) {
    if ($doc_type != 1 && $doc_type != 2 && $doc_type != 3){
	$data["err"] = 1;
	$data["msg"] = "不支持此业务类型";
	echo json_encode($data);
	exit;
    }
}

/**
 * 获取出库类型ID
 * 1.返还供应商出库单 - 
 * 2.调拨出库单
 * @param type $doc_type
 */
function get_doc_type_out_id($doc_type) {
    if($doc_type == 1){
	return 7;
    }else if($doc_type == 2){//调拔出库
	return 12;
    }else if($doc_type == 3){
	return 14;
    }else{
	return null;
    }
}

/**
 * 验证入库类型
 * @param type $doc_type
 */
function vali_doc_type_in($doc_type) {
    if ($doc_type != 11) {echo $doc_type;
	$data["err"] = 1;
	$data["msg"] = "不支持此业务类型";
	echo json_encode($data);
	exit;
    }
}

/**
 * 获取出库类型ID
 * 11.调拨入库单
 * @param type $doc_type
 */
function get_doc_type_in_id($doc_type) {
   if($doc_type == 11){
       return 13;
   }
}

?>
