<?php
$config['voucher_campaign_status_list'] = array(0=>'未启用',1=>'已启用',2=>'已停用');
$config['voucher_release_status_list'] = array(0=>'未发放',1=>'已发放',2=>'已撤消');
$config['voucher_record_status_list'] = array(0=>'未使用',1=>'使用中',2=>'已用完');
$config['voucher_release_rules'] = array('number'=>'指定发放数量','rule'=>'指定发放条件','list'=>'手动指定用户','sn'=>'指定现金券号');

/*
* sys:系统功能使用，不可人工派发
* num:限定发放数量
* repeat:是否可复用
* 
*/
$config['voucher_config'] = array(
	array(
		'code'=>'e',
		'name'=>'预生成线上现金券',
		'repeat'=>false,
		'rules'=>array('rule','list'),
		'link'=>true, 
		'sys'=>false,
		'worth'=>false,
		'logo'=>false
	),
	array(
		'code'=>'print',
		'name'=>'印刷现金券',
		'repeat'=>false,
		'rules'=>array('number'),
		'link'=>false,
		'sys'=>false,
		'worth'=>false,
		'logo'=>false
	),
	array(
		'code'=>'auto',
		'name'=>'自动发放现金券',
		'repeat'=>false,
		'rules'=>array(),
		'link'=>true, 
		'sys'=>true,
		'worth'=>false,
		'logo'=>false
	),
	array(
		'code'=>'repeat',
		'name'=>'可复用现金券',
		'repeat'=>true,
		'rules'=>array('number','list','sn'),
		'link'=>false,
		'sys'=>false,
		'worth'=>false,
		'logo'=>false
	),
	array(
		'code'=>'ex',
		'name'=>'兑换类现金券',
		'repeat'=>false,
		'rules'=>array(),
		'link'=>true,
		'sys'=>true,
		'worth'=>true,
		'logo'=>true
	)


);