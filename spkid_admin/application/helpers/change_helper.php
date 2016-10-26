<?php
function format_change_status($change,$red=FALSE)
{
    $status = array();
    if(is_array($change)) $change=(object)$change;
    if($change->change_status==0) $status[] = '未确认';
    if($change->change_status==1) $status[] = '已确认';
    if($change->change_status==3) $status[] = $red?'<font color="red">取消</font>':'取消';
    if($change->change_status==4) $status[] = $red?'<font color="red">作废</font>':'作废';    
    $status[] = $change->shipped_status?'已入库':'未入库';
    $status[] = $change->shipping_status?'已发货':'未发货';
    if (isset($change->odd)&&$change->odd) $status[] = $red?'<font color="red">问题单</font>':'问题单';
    if (!empty($change->pick_sn)&&!$change->shipping_status) $status[] = $red?'<font color="red">拣货中</font>':'拣货中';
    
    return $status;
}