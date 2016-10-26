<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE',0666);
define('DIR_READ_MODE',  0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
define('FOPEN_READ',			     'rb');
define('FOPEN_READ_WRITE',		     'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',     'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE','w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',		     'ab');
define('FOPEN_READ_WRITE_CREATE',	     'a+b');
define('FOPEN_WRITE_CREATE_STRICT',	     'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',     'x+b');

/**
 * 事务类型常量定义
 */
define('TRANS_TYPE_DIRECT_IN',		 1); //入库单
define('TRANS_TYPE_DIRECT_OUT',		 2); //出库单
define('TRANS_TYPE_SALE_ORDER',		 3); //订单
define('TRANS_TYPE_RETURN_ORDER',	 4); //退货单
define('TRANS_TYPE_CHANGE_ORDER',	 5); //换货单
define('TRANS_TYPE_PACKET_EXCHANGE',	 6); //调仓单
define('TRANS_TYPE_OUTSIDE_SALE_ORDER',	 7); //外部订单
define('TRANS_TYPE_OUTSIDE_RETURN_ORDER',8); //外部退单

/**
 * 事务状态常量定义
 */
define('TRANS_STAT_AWAIT_OUT',1); //待出
define('TRANS_STAT_OUT',      2); //已出
define('TRANS_STAT_AWAIT_IN', 3); //待入
define('TRANS_STAT_IN',	      4); //已入
define('TRANS_STAT_CANCELED', 5); //已取消
define('TRANS_STAT_EX',       6); //调仓中
define('TRANS_STAT_EXED',     7); //调仓结束

/**
 * SQL中关键字
 */
define('SQL_OR',     'OR');
define('SQL_AND',    'AND');
define('USE_SQL_OR', TRUE);
define('USE_SQL_AND',FALSE);

/**
 * 配送方式
 */
define('SHIPPING_ID_CAC', 1);
define('SHIPPING_ID_PINGTAI', 2); //平台快递方式
define('SHIPPING_ID_DEFAULT', 9);

/**
 * 支付方式
 */
define('PAY_ID_COD',    1);
define('PAY_ID_BALANCE',5);
define('PAY_ID_VOUCHER',6);
define('PAY_ID_PAYBACK',8);

/**
 * 运费
 */
define('SHIPPING_FEE_DEFAULT',     10);
define('SHIPPING_FREE_ORDER_PRICE',168);


/**
 * 拣货数量
 */
define('PICK_NUM', 20);

/**
 * 意见类型
*/
define( 'ADVICE_ODD', 2 );

// mcrypt_key
define('MCRYPT_KEY', 'TONGYI');

/**
 * 店铺属性
 */
define('SHOP_STATUS_ACTIVE',1); // 店铺可用　
define('SHOP_STATUS_DISABLE',0); // 店铺不可用　
define('SHOP_STATUS_ALL','ALL'); // 所有店铺

/**
 * 采购类型  
 */
define('COOPERATION_TYPE_COST',      1);//买断(XXX:暂未使用)
define('COOPERATION_TYPE_CONSIGN',   2);//代销(XXX:暂未使用)
define('COOPERATION_TYPE_TMALL',     8);//天猫发货
define('COOPERATION_TYPE_FW_VIRTUAL',4);//MT服务（虚库）
define('COOPERATION_TYPE_MT_REAL',   5);//MT代销（实库）
define('COOPERATION_TYPE_MT_VIRTUAL',6);//MT代销（虚库）
define('COOPERATION_TYPE_THIRD',7);//MT代销（虚库）

/**
 * 批次类型 
 */
define('BATCH_TYPE_PURCHASE',    0);//0-采购单；
define('BATCH_TYPE_CONVERT_COST',1);//1-代转买批次；
define('BATCH_TYPE_CHECK_WIN',   2);//2-盘赢；
define('BATCH_TYPE_OTHER',       3);//3-其他

/**
 * 商品成本价类型
 */
define('CONSIGN_TYPE_COST', 0);//0为非代销
define('CONSIGN_TYPE_CONSIGN', 1);//1为固定代销价
define('CONSIGN_TYPE_CONSIGN_RATE', 2);//2为浮动代销率

/**
 * 批量导入文件路径
 */
define('IMPORT_PATH_RELATIVE','public/import/'); //导入目录的相对路径
define('IMPORT_PATH_BATH', ROOT_PATH . IMPORT_PATH_RELATIVE); //导入主目录
define('IMPORT_PATH_PURCHASE', IMPORT_PATH_BATH . 'purchase/'); //采购单目录
define('IMPORT_PATH_PRO_COST', IMPORT_PATH_BATH . 'product_cost/'); //商品成本价目录
define('IMPORT_PATH_PRO_PRICE', IMPORT_PATH_BATH . 'product_price/'); //商品销售价目录
define('IMPORT_PATH_RESULT', IMPORT_PATH_BATH . '_result/'); //导入结果文件目录
define('IMPORT_PATH_PROVIDER_BARCODE', IMPORT_PATH_BATH . 'provider_barcode/'); //修改条形码目录

// 生成目录
define('CREATE_HTML_PATH', ROOT_PATH.'public/data/static/'); 		//生成静态页面路径
define('CREATE_IMAGE_PATH', ROOT_PATH.'public/data/images/'); 		// 商品图片生成目录
define('CREATE_UNION_PATH', ROOT_PATH.'public/data/union/'); 		// CPS等

//图片路径
define('SIZE_IMAGE_PATH', CREATE_IMAGE_PATH);		// 商品尺寸图目录
define('SIZE_IMAGE_TAG', 'size_images/');		// 商品尺寸图目录
define('PRO_SIZE_IMAGE_PATH', CREATE_IMAGE_PATH); // 商品尺寸详情图目录
define('PRO_SIZE_IMAGE_TAG', 'product_size_images/'); // 商品尺寸详情图目录
define('GALLERY_PATH', 'gallery/' );			// 商品图目录
define('PRODUCT_DESC_PATH', 'product_desc/');

//头部导航
define('TEMP_INDEX_NAVIGATION', CREATE_HTML_PATH . 'index/navigation.html');
define('WWW_PATH', '/');

//广告图片上传路径
define('AD_IMAGE_PATH', CREATE_IMAGE_PATH);
define('AD_IMAGE_TAG', 'front_ad/');

//首页焦点图上传路径
define('FRONT_FOCUS_IMAGE_DIR','front_focus_image');
define('FRONT_FOCUS_IMAGE_PATH',CREATE_IMAGE_PATH.FRONT_FOCUS_IMAGE_DIR);

//定义首页焦点图类别
define('FOCUS_TYPE','$focus_type=array("1"=>"手机轮播图片", "2"=>"手机引导页图片", "3"=>"视频轮播图片", "4"=>"电脑轮播图片", "5"=>"手机课程轮播图片");');

//首页焦点图静态页面路径 相对index.php路径
define('FRONT_FOCUS_IMAGE_HTML',CREATE_HTML_PATH .'index/front_focus_image.html');
//团购首页焦点图静态页面路径 相对index.php路径
define('TUAN_FRONT_FOCUS_IMAGE_HTML',CREATE_HTML_PATH .'index/tuan_front_focus_image.html');
## mobile front page的引导页面
define('MOBILE_FIRST_PAGE_HTML',CREATE_HTML_PATH .'index/mobile_first_page.html');
//memcache首页焦点图key
define('FRONT_FOCUS_IMAGE_HTML_KEY','front_focus_image_html');
//memcache团购首页焦点图key
define('TUAN_FRONT_FOCUS_IMAGE_HTML_KEY','tuan_front_focus_image_html');

//前台url地址
define('FRONT_URL','http://pc.redtravel.cn');
define("IMG_URL", 'http://img.redtravel.cn');
define("PUBLIC_DATA_IMAGES", "public/data/images/");

/*
 * 后台域名
 */
define("ERP_HOST", "https://b.redtravel.cn");

//上传根目录
define('UPLOAD_PATH_BATH', CREATE_IMAGE_PATH );
define('STATIC_HOST_CONFIG','$static_host_arr=array("http://static.redtravel.cn","http://static.redtravel.cn");');
/**
 * 限抢图片上传目录
 */
define('UPLOAD_PATH_RUSH', UPLOAD_PATH_BATH);
define('UPLOAD_TAG_RUSH', 'rush/');
define('UPLOAD_PATH_RUSH_PRODUCT', UPLOAD_PATH_BATH);
define('UPLOAD_TAG_RUSH_PRODUCT', 'rush_product/');

//测试模式
define("DEBUG_MODE", 0);
//静态缓存页路径
define("STATIC_CACHES", CREATE_HTML_PATH . 'static_caches/');
/* End of file constants.php */
/* Location: ./application/config/constants.php */

/*
 * 自动客审
 */
define('CHECK_LOG', false);         //开启日志记录审核成功订单
define('MIN_CHECK_TIME', 60);     //下单时间大于多长时间才客审，默认30分钟：60*30
define('MAX_LIMIT_ORDER', 200);     //每次最多客审多少单
define('TIME_OUT', 604800);         //自动客审，只做下单据当前时间，默认7天：3600*24*7

// 系统仓库ID定义
define('DT_RETURN_DEPOT_ID', 5); 		//  代销正常商品退货仓ID
define('DT_RETURN_DEPOT_LOCATION_ID', 4); //  代销正常商品退货仓 储位ID
define('DT_RETURN_DEPOT_LOCATION_NAME', 'DT-01-01-01-01'); //  代销正常商品退货仓 储位ID
define('DT_RETURN_DEPOT_NAME', '周浦代销退货仓（不可售）' );

define('MT_RETURN_DEPOT_ID', 4); 		//  代销正常商品退货仓ID
//define('MT_RETURN_DEPOT_LOCATION_ID', 6); //  代销正常商品退货仓 储位ID
define('MT_RETURN_DEPOT_LOCATION_NAME', 'MT-01-01-01-01'); //  代销正常商品退货仓 储位ID
define('MT_RETURN_DEPOT_NAME', '周浦买断退货仓（不可售）' );

define('BT_RETURN_DEPOT_ID', 6); 		//  代销正常商品退货仓ID
//define('BT_RETURN_DEPOT_LOCATION_ID', 1); //  代销正常商品退货仓 储位ID
define('BT_RETURN_DEPOT_LOCATION_NAME', 'BT-01-01-01-01'); //  代销正常商品退货仓 储位ID
define('BT_RETURN_DEPOT_NAME', '第三方退货仓（不可售）' );

define('RETURN_DEPOT_ID', 3 ); 		//  代销正常商品退货仓ID
define('RETURN_DEPOT_LOCATION_ID', 3 ); //  代销正常商品退货仓 储位ID
define('RETURN_DEPOT_NAME', '代销退货仓（不可售）' );
define('MD_RETURN_DEPOT_ID', 8 ); 		//  买断正常商品退货仓ID
define('MD_RETURN_DEPOT_LOCATION_ID', 12 ); //  买断正常商品退货仓 储位ID
define('MD_RETURN_DEPOT_NAME', '买断退货仓（不可售）' );
define('CTB_RETURN_DEPOT_ID', 10 );	//  代销转买断商品退货仓ID
define('CTB_RETURN_DEPOT_LOCATION_ID', 27 );	//  代销转买断商品退货仓 储位ID
define('CTB_RETURN_DEPOT_NAME', '代转买退货仓（不可售）' );
define('THIRD_DEPOT_ID', 13 ); 		//  三方退货仓退货仓ID
define('THIRD_DEPOT_LOCATION_ID', 31 ); //  三方退货仓储位ID
define('THIRD_DEPOT_NAME', '三方退货仓' );

define('CTB_DEPOT_IO_TIME', '23:00:00' );	//  代销转买断商品出库和入库的时间
define('CTB_DEPOT_IN_TYPE', 15 );	//  系统自动生成代销转买入库类型
define('CTB_DEPOT_OUT_TYPE', 16 );	//  系统自动生成代销转买出库类型

define('CTB_RETURN_DEPOT_LOCATION_NAME', 'TH-02-01-01');   //  代销转买断退货仓 储位名称
define('MD_RETURN_DEPOT_LOCATION_NAME', 'TH-01-01-01');   //  买断正常商品退货仓 储位名称

// 系统代销转买断相关配置
define('SYS_CTB_PROVIDER_ID', 1 );	// 系统代销转买断供应商ID
define('SYS_CTB_BATCH_ID', '1,2,3,4,5,6,7,8,9,10');	// 转买断后可用批次ID
/**
 * 2013-12-13 重新定义仓库及储位
 */
define('DEPOT_ID_FW_VIRTUAL_SEND', 1); //MT服务(虚库)发货仓ID
define('DEPOT_ID_FW_VIRTUAL_RETURN', 2); //MT服务(虚库)退货仓ID
define('DEPOT_ID_TMALL_SEND', 3); //天猫虚拟发货仓ID
define('DEPOT_ID_TMALL_RETURN', 4); //天虚拟猫退货仓ID
define('DEPOT_ID_MT_REAL_SEND', 5); //MT代销(实库)发货仓ID
define('DEPOT_ID_MT_REAL_RETURN', 6); //MT代销(实库)退货仓ID
define('DEPOT_ID_MT_VIRTUAL_SEND', 7); //MT代销(虚库)发货仓ID
define('DEPOT_ID_MT_VIRTUAL_RETURN', 8); //MT代销(虚库)退货仓ID

define('DEPOT_NAME_TMALL_RETURN', '天猫虚拟退货仓');
define('DEPOT_NAME_FW_VIRTUAL_RETURN', 'MT服务(虚库)退货仓');
define('DEPOT_NAME_MT_REAL_RETURN', 'MT代销(实库)退货仓');
define('DEPOT_NAME_MT_VIRTUAL_RETURN', 'MT代销(虚库)退货仓');

define('LOCATION_ID_FW_VIRTUAL_SEND', 1); //MT服务(虚库)发货储位ID
define('LOCATION_ID_FW_VIRTUAL_RETURN', 2); //MT服务(虚库)退货储位ID
define('LOCATION_ID_TMALL_SEND', 3); //天猫发货储位ID
define('LOCATION_ID_TMALL_RETURN', 4); //天猫退货储位ID
define('LOCATION_ID_MT_REAL_RETURN', 5); //MT代销(实库)退货储位ID
define('LOCATION_ID_MT_VIRTUAL_SEND', 6); //MT代销(虚库)发货储位ID
define('LOCATION_ID_MT_VIRTUAL_RETURN', 7); //MT代销(虚库)退货储位ID

define('LOCATION_NAME_FW_VIRTUAL_SEND', 'XF-01-01-01'); //MT服务(虚库)发货储位
define('LOCATION_NAME_FW_VIRTUAL_RETURN', 'XT-01-01-01'); //MT服务(虚库)退货储位
define('LOCATION_NAME_TMALL_SEND', 'TF-01-01-01'); //天猫发货储位
define('LOCATION_NAME_TMALL_RETURN', 'TT-01-01-01'); //天猫退货储位
define('LOCATION_NAME_MT_REAL_RETURN', 'DST-01-01-01'); //MT代销(实库)退货储位
define('LOCATION_NAME_MT_VIRTUAL_SEND', 'DXF-01-01-01'); //MT代销(虚库)发货储位
define('LOCATION_NAME_MT_VIRTUAL_RETURN', 'DXT-01-01-01'); //MT代销(虚库)退货储位
define('LOCATION_NAME_THIRD_RETURN', 'A-01-01-02'); //THIRD代销(虚库)退货储位

define('DEPOT_TYPE_TMALL_IN', 'rk888'); //天猫虚拟入库类型
define('DEPOT_TYPE_FW_VIRTUAL_IN', 'rk788'); //MT服务(虚库)入库类型
define('DEPOT_TYPE_MT_VIRTUAL_IN', 'rk988'); //MT代销(虚库)入库类型


/**
 * 系统自动生成出入库单
 */
define("DEPOT_IO_AFTEER_FIX", "SYSCTB");
//已运行当天为准，生成的出入库审核时间
define("DEPOT_IO_GENERATE_MAX", 5);
//入库类型
define("DEPOT_IO_TYPE_IN", "RK");
//出库类型
define("DEPOT_IO_TYPE_OUT", "CK");
//入库默认DEPOT ID
define("DEPOT_IO_IN_DEPOT_ID", RETURN_DEPOT_ID);
//出库默认DEPOT ID
//define("DEPOT_IO_OUT_DEPOT_ID", CTB_RETURN_DEPOT_ID);

//专题存放目录
define('ZHUANTI_HTML_PATH', CREATE_HTML_PATH.'zhuanti/');

//物流对账存放目录
define('SHIPPING_HTML_PATH', CREATE_HTML_PATH.'shipping/');

//系统Log 开关
define('BY_SYSTEM_LOG', true);

define('SITE_DOMAIN', 'redtravel.cn');
/**
 * SSO
 */
define("SSO_COOKIE_USERNAME", "_username"); //第三发货
define("SSO_COOKIE_PASSWORD", "_password");
define("SSO_COOKIE_EXPRIE", 60 * 60 * 24 * 14);
define("SSO_COOKIE_DOMAIN", ".redtravel.cn");

/*
 * 快递100 
 */
define("KUAIDI100_KEY", "UjfosIBh8464");
define("KUAIDI100_URL", "http://www.kuaidi100.com/poll");

/*
 * 顺丰
 */
define('SF_SHIPPING_ID', 2);   //顺丰ID
define('SF_CHECKWORD', '24d5f7242a9644c5842832abacdb3bfd'); //顺丰的校验码
define('SF_CUST_ID', '5720310679');     //寄件方客户卡号
define('BBG_COMPANY', '演示站');    	//寄件方公司名称
define('BBG_CONTACT', '演示站');      //寄件方联系人
define('BBG_TEL', '4006 333 999');    //寄件方座机
define('BBG_PROVINCE', '浙江省');     //寄件人所在省份
define('BBG_CITY', '湖州市');         //寄件方所属城市
define('BBG_COUNTY', '吴兴区');       //寄件人所在县/区
define('BBG_ADDRESS', '浙江省湖州市吴兴区七幸璐999号 金泰科技4栋');   //寄件人所在县/区
define('WEB_SERVICE_URL', 'http://219.134.187.154:8088/bsp-oip/ws/B2CCustomizeService?wsdl');   //顺丰服务器URL
define('SF_SERVICE_URL','http://219.134.187.154:8088/bsp-oip/ws/CustomerService?wsdl'); //顺风订单取消接口URL
define('ORDER_NUN', 1000);  //顺丰确认订单数量
define('ORDER_CANCEL_NUN', 1000);  //顺丰取消订单数量
define('SF_ORDER_NUM', 100);	//顺丰下单数量

define('DB_SHIPPING_ID', 12);   //德邦ID
define('DBDS_SHIPPING_ID', 13); //德邦ID
define('DB_CUST_ID', '400811108'); //德邦ID
define('DB_API_KEY', 'd6e61c0f3a4797e97835a1952e7ac99b');
define('DB_CUST_CODE', 'EWBOSWA');
/*
 *批量用户金额 
 */
define('IMPORT_MONEY_BALANCE_DIR',IMPORT_PATH_BATH.'money_balance/');   // 批量调整用户余额目录
define('ACCOUNT_MINUS_FILE_NAME', 'account_minus.csv');//批量调整用户余额csv文件
define('ACCOUNT_MINUS_FILE_DIR', 'minus/');//批量减款历史记录目录
define('ACCOUNT_ADD_FILE_NAME', 'account_add.csv');//批量充值csv文件
define('ACCOUNT_ADD_FILE_DIR', 'add/');//批量充值历史记录目录
define('IMPORT_MONEY_BALANCE_RELATIVE_PATH',IMPORT_PATH_RELATIVE.'money_balance/');

define('ACCOUNT_FIELD', 2);
define('MONEY_FIELD', 3);
/* 帐号变动类型 */
define('ACT_SAVING',                'money_push');     // 帐户冲值
define('ACT_DRAWING',               1);     // 帐户提款
define('ACT_ADJUSTING',             'change_account');     // 调节帐户
define('ACT_OTHER',                99);     // 其他类型
define('ACT_REBATE',   'money_rebate'); //退货运费转入

/**
 * 团购图片上传目录
 */
define('UPLOAD_PATH_TUAN', UPLOAD_PATH_BATH);
define('UPLOAD_TAG_TUAN', 'tuan/');

/* 缓存时间 */
define('CACHE_TIME_TUAN', 86400);	//团购缓存时间
/** 
 * 第三方平台订单来源及支付方式对应数组定义
 * key => order source
 * value => payment info
 */
$third_parts = array(
    /*'3' => array('pay_id' => 14, 'payment_remark' => '京东代付'),
    '4' => array('pay_id' => 15, 'payment_remark' => '一号店代付'),
    '5' => array('pay_id' => 17, 'payment_remark' => '当当代付'),
    '6' => array('pay_id' => 16, 'payment_remark' => '美团代付'),
    '7' => array('pay_id' => 18, 'payment_remark' => 'QQ网购代付')*/
);

$shipping_jzh = array(310000, 330000, 320000);//江浙沪ID
define('SHIPPING_FEE_NEAR', 10);//江浙沪返还用户运费最高金额
define('SHIPPING_FEE_FAR', 20);//除江浙沪返还用户运费最高金额
// 前台数据缓存
define('CATE_DATA_CACHE_TIME', 172800);//2天

// 短信发送默认价格
define('DEFAULT_SMS_PRICE', 0.12);

define('ORDER_INVALID_TIME', 259200); //3天
define('ORDER_INVALID_LIMIT', 50); 

define('PRODUCT_TOOTH_TYPE', 1);//牙科大类ID
define('PRODUCT_COURSE_TYPE', 2);//课程大类ID

define('ORDER_SOURCE_ID', 6); 
define('WEIXIN_CACHE_TIME', 2592000); //30天
$refund = array('1' => '不退货补偿');
// 参数配置文件路径
define('SYSTEM_SETTINGS', ROOT_PATH.'application/config/settings.php');
require_once( SYSTEM_SETTINGS );
