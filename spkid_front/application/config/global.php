<?php

$config['best_times'] = array(
	'时间不限','只双休日、假期送货','只工作日送货'
);

$config['alipay'] = array(
	'partner' => '2088701919343818',
	'seller_email' => 'fa@oswell.com.cn',
	//'key'	 =>'wm4q0lbfr25prvg9gxp7uh8gejdxsxel',cfq9wn2fkobjc959qkxmpknnjp6mi9oy
    'key' => 'cfq9wn2fkobjc959qkxmpknnjp6mi9oy',
	'input_charset' => 'utf-8',
	'return_url' => FRONT_HOST . '/user/alipay_callback',
	'notify_url' =>'',
	'login_url' =>'',
	'sign_type' => 'MD5',
	'transport' =>'http'
);
$config['alipay_new'] = array(
    'partner' => '2088701919343818',
    'seller_email' => 'fa@oswell.com.cn',
    'seller_id' => '2088701919343818',    
    'key' => 'cfq9wn2fkobjc959qkxmpknnjp6mi9oy',
    'private_key_path' => 'alipay_pem/key/rsa_private_key.pem',
    'ali_public_key_path' => 'alipay_pem/key/alipay_public_key.pem',
    'input_charset' => 'utf-8',
    'return_url' =>'',
    'notify_url' =>'',
    'login_url' =>'',
    'sign_type' => 'RSA',
    'cacert' => 'alipay_pem/key/cacert.pem',
	'transport' =>'https',
	'show_url' => FRONT_HOST
);


$config['weixin'] = array('appid' => 'wxc25a0104ead394c1', 
                          'appsecret' => '8425d7fd75ba373b7bee6bfd758db3b5',
                          'site_url' => FRONT_HOST,
                          'callback_url' => FRONT_HOST . '/user/weixin_callback',
                          'notify' => FRONT_HOST . '/pay/wxpay_notify');

$config['qq'] = array(
	'0' => '2092351803',
	'1' => '1900185248',
);

$config['mobile'] = array(
	'accountId' => '10657109091403005',
	'password' => 'test123',
	'serviceId' => '10657109091403'
);

$config['bill'] = array(
	'merchantAcctId' => '',
	'bgUrl' => '',
	'gate'=>'',
	'privatekey' => '',
	'publickey' => '',
);
$config['register_voucher_campaign_ids']=array(6);

$config['product_age'] = array(
	0=>'新生儿',
	6=>'6个月',
	12=>'1岁',
	24=>'2岁',	
	36=>'3岁',
	48=>'4岁',
	60=>'5岁',
	72=>'6岁',
	84=>'7岁',
	96=>'8岁',
	108=>'9岁',
	120=>'10岁',
	132=>'11岁',
	144=>'12岁及以上'
);
$config['age_filter']=array(
	1=>array('value'=>array(0,12),'name'=>'新生儿(0-1岁)'),
	2=>array('value'=>array(12,36),'name'=>'婴童(1-3岁)'),
	3=>array('value'=>array(48,72),'name'=>'小童(4-6岁)'),
	4=>array('value'=>array(84,108),'name'=>'中童(7-9岁)'),
	5=>array('value'=>array(120,144),'name'=>'大童(10岁以上)')
);

// 导航别名 
$config['nav_alias_list'] = array(
    array(
    "sid" => 1, // category_id
        "gid" => 0,
        "title" => "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "start_dt" => "2013/11/01 00:00:00",
        "end_dt" =>  "2014/11/01 00:00:00",
        "link" =>  "",
        "featured_event_img" => "http://dp.image-qoo10.cn/CN/GMKT.IMG/special/2013/11/05/b797da98-d9f8-4924-8288-31ad6cef7a2f.jpg",
        "priority" =>  1,
        "top_html" =>  "",
        "contents_type" =>  "special",
        "minishop_seller_qid" =>  0,
        "minishop_title" => "",
        "minishop_thumbnail" =>  "",
        "minishop_sell_cust_no" =>  "",
        "minishop_shop_img" =>  "",
        "meta_title" =>  "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "meta_description" =>  "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "banner_img" =>  "",
        "icon_img" =>  "http://dp.image-gmkt.com/CN/GMKT.IMG/special/2013/11/05/8cd6e5c9-2eb6-4e5d-8928-f314e647d7a2.jpg"
),
    array(
    "sid" => 26, // category_id
        "gid" => 0,
        "title" => "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "start_dt" => "2013/11/01 00:00:00",
        "end_dt" =>  "2014/11/01 00:00:00",
        "link" =>  "",
        "featured_event_img" => "http://dp.image-qoo10.cn/CN/GMKT.IMG/special/2013/11/05/b797da98-d9f8-4924-8288-31ad6cef7a2f.jpg",
        "priority" =>  1,
        "top_html" =>  "",
        "contents_type" =>  "special",
        "minishop_seller_qid" =>  0,
        "minishop_title" => "",
        "minishop_thumbnail" =>  "",
        "minishop_sell_cust_no" =>  "",
        "minishop_shop_img" =>  "",
        "meta_title" =>  "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "meta_description" =>  "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "banner_img" =>  "",
        "icon_img" =>  "http://dp.image-gmkt.com/CN/GMKT.IMG/special/2013/11/05/8cd6e5c9-2eb6-4e5d-8928-f314e647d7a2.jpg"
),
    array(
    "sid" => 55, // category_id
        "gid" => 0,
        "title" => "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "start_dt" => "2013/11/01 00:00:00",
        "end_dt" =>  "2014/11/01 00:00:00",
        "link" =>  "",
        "featured_event_img" => "http://dp.image-qoo10.cn/CN/GMKT.IMG/special/2013/11/05/b797da98-d9f8-4924-8288-31ad6cef7a2f.jpg",
        "priority" =>  1,
        "top_html" =>  "",
        "contents_type" =>  "special",
        "minishop_seller_qid" =>  0,
        "minishop_title" => "",
        "minishop_thumbnail" =>  "",
        "minishop_sell_cust_no" =>  "",
        "minishop_shop_img" =>  "",
        "meta_title" =>  "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "meta_description" =>  "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "banner_img" =>  "",
        "icon_img" =>  "http://dp.image-gmkt.com/CN/GMKT.IMG/special/2013/11/05/8cd6e5c9-2eb6-4e5d-8928-f314e647d7a2.jpg"
),
    array(
    "sid" => 73, // category_id
        "gid" => 0,
        "title" => "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "start_dt" => "2013/11/01 00:00:00",
        "end_dt" =>  "2014/11/01 00:00:00",
        "link" =>  "",
        "featured_event_img" => "http://dp.image-qoo10.cn/CN/GMKT.IMG/special/2013/11/05/b797da98-d9f8-4924-8288-31ad6cef7a2f.jpg",
        "priority" =>  1,
        "top_html" =>  "",
        "contents_type" =>  "special",
        "minishop_seller_qid" =>  0,
        "minishop_title" => "",
        "minishop_thumbnail" =>  "",
        "minishop_sell_cust_no" =>  "",
        "minishop_shop_img" =>  "",
        "meta_title" =>  "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "meta_description" =>  "暖意秋冬 麦考林时尚羽绒服&棉服 拒绝臃肿5折起",
        "banner_img" =>  "",
        "icon_img" =>  "http://dp.image-gmkt.com/CN/GMKT.IMG/special/2013/11/05/8cd6e5c9-2eb6-4e5d-8928-f314e647d7a2.jpg"
)
);
$config['page_value'] = array(
    "COOKE_DOMAIN"=>"tspkid.cn",
    "ROOT_PATH"=>"",
    "EVENT_ROOT_PATH"=>"/gmkt.inc.event",
    "SERVICE_PATH"=>"",
    "SERVER_NAME"=>"M18WWW5",
    "SERVER_IP"=>"211.172.255.85",
    "CLIENT_IP"=>"58.246.144.115",
    "APP_NO"=>5,
    "WWW_SERVER"=>"http://www.tspkid.cn",
    "MEMBER_SERVER"=>"http://www.tspkid.cn",
    "LOGIN_SERVER"=>"http://www.tspkid.cn",
    "CATEGORY_SERVER"=>"http://www.tspkid.cn",
    "SEARCH_SERVER"=>"http://www.tspkid.cn",
    "MY_SERVER"=>"http://www.tspkid.cn",
    "ORDER_SERVER"=>"http://www.tspkid.cn",
    "GOODS_SERVER"=>"http://www.tspkid.cn",
    "COUPON_SERVER"=>"http://www.tspkid.cn",
    "EVENT_SERVER"=>"http://www.tspkid.cn",
    "EVENT_CONTENT_SERVER"=>"http://www.tspkid.cn",
    "OPENAPI_SERVER"=>"http://www.tspkid.cn",
    "DP_IMAGE_PATH"=>"http://dp.image-qoo10.cn",
    "DP_SSL_IMAGE_PATH"=>"https://staticssl.image-qoo10.cn",
    "STATIC_IMAGE_PATH"=>"http://static.image-qoo10.cn",
    "STATIC_SSL_IMAGE_PATH"=>"https://staticssl.image-qoo10.cn",
    "GOODS_IMAGE_PATH"=>"http://gd.image-qoo10.cn",
    "GOODS_SSL_IMAGE_PATH"=>"https://gdssl.image-qoo10.cn",
    "OPENAPI_PATH"=>"/GMKT.INC.Front.OpenApiService",
    //"IS_LOCAL_SERVER"=>true,
    "PG_SERVER"=>"http://pg.qoo10.com/gmkt.inc.pgservice.service",
    "IS_LOCAL_SERVER_ORG"=>false,
    "FRONT_STILL_IMAGE"=>false,
    "IS_OTHERSITE"=>false,
    "IS_MULTISITE"=>true,
    "SITEID"=>"DEFAULT",
    "VIEW_SITEID"=>"m18",
    "QOO10_GOODS_SERVER"=>"http://list.qoo10.com",
    "QOO10_SERVER"=>"http://www.qoo10.com",
    "PAGE_NO"=>70,
    "PAGE_CONTEXT_ID"=>"836dfd50-7886-4772-893a-62d60efb26ab",
    "USE_COMMONSSL"=>false,
    "COMMON_DOMAIN"=>"",
    "COMMON_SSL_DOMAIN"=>"",
    "SHOP_DOMAIN"=>"",
    "QOO10_DEFAULT_GOODS_SERVER"=>"http://list.qoo10.cn/gmkt.inc",
    "IS_LOGIN"=>false
);


/*
$config['page_value'] = array(
    "COOKE_DOMAIN"=>"m18.com",
    "ROOT_PATH"=>"/gmkt.inc",
    "EVENT_ROOT_PATH"=>"/gmkt.inc.event",
    "SERVICE_PATH"=>"/gmkt.inc/",
    "SERVER_NAME"=>"M18WWW5",
    "SERVER_IP"=>"211.172.255.85",
    "CLIENT_IP"=>"58.246.144.115",
    "APP_NO"=>5,
    "WWW_SERVER"=>"http://www.m18.com",
    "MEMBER_SERVER"=>"http://my.m18.com",
    "LOGIN_SERVER"=>"http://my.m18.com",
    "CATEGORY_SERVER"=>"http://www.tspkid.cn",
    "SEARCH_SERVER"=>"http://list.m18.com",
    "MY_SERVER"=>"http://my.m18.com",
    "ORDER_SERVER"=>"http://my.m18.com",
    "GOODS_SERVER"=>"http://list.m18.com",
    "COUPON_SERVER"=>"http://list.m18.com",
    "EVENT_SERVER"=>"http://www.m18.com",
    "EVENT_CONTENT_SERVER"=>"http://event.m18.com",
    "OPENAPI_SERVER"=>"http://www.m18.com",
    "DP_IMAGE_PATH"=>"http://dp.image-qoo10.cn",
    "DP_SSL_IMAGE_PATH"=>"https://staticssl.image-qoo10.cn",
    "STATIC_IMAGE_PATH"=>"http://static.image-qoo10.cn",
    "STATIC_SSL_IMAGE_PATH"=>"https://staticssl.image-qoo10.cn",
    "GOODS_IMAGE_PATH"=>"http://gd.image-qoo10.cn",
    "GOODS_SSL_IMAGE_PATH"=>"https://gdssl.image-qoo10.cn",
    "OPENAPI_PATH"=>"/GMKT.INC.Front.OpenApiService",
    "IS_LOCAL_SERVER"=>true,
    "PG_SERVER"=>"http://pg.qoo10.com/gmkt.inc.pgservice.service",
    "IS_LOCAL_SERVER_ORG"=>false,
    "FRONT_STILL_IMAGE"=>false,
    "IS_OTHERSITE"=>false,
    "IS_MULTISITE"=>true,
    "SITEID"=>"DEFAULT",
    "VIEW_SITEID"=>"m18",
    "QOO10_GOODS_SERVER"=>"http://list.qoo10.com",
    "QOO10_SERVER"=>"http://www.qoo10.com",
    "PAGE_NO"=>70,
    "PAGE_CONTEXT_ID"=>"836dfd50-7886-4772-893a-62d60efb26ab",
    "USE_COMMONSSL"=>false,
    "COMMON_DOMAIN"=>"",
    "COMMON_SSL_DOMAIN"=>"",
    "SHOP_DOMAIN"=>"",
    "QOO10_DEFAULT_GOODS_SERVER"=>"http://list.qoo10.cn/gmkt.inc",
    "IS_LOGIN"=>false
);
*/