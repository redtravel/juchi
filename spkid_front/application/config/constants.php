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
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/**
 * 各服务器host定义
 */
define('STATIC_HOST_CONFIG','$static_host_arr=array("http://static.redtravel.cn","http://static.redtravel.cn");');
/*
 * 后台域名
 */
define("ERP_HOST", "http://b.redtravel.cn");

define('MAX_SALE_NUM', 200);//商品最大可售数
define('MIN_SALE_CUE_NUM', 2);//库存紧张数

define("BASE_URL", "/");
define('SHOP_NAME','演示站');
// 事务状态常量定义
define('TRANS_STAT_AWAIT_OUT',	1);
define('TRANS_STAT_OUT',	2);
define('TRANS_STAT_AWAIT_IN',	3);
define('TRANS_STAT_IN',		4);
define('TRANS_STAT_CANCELED',	5);
define('TRANS_STAT_EX',         6); //调仓中
define('TRANS_STAT_EXED',       7); //调仓结束

/**
 * 事务类型常量定义
 */
define('TRANS_TYPE_DIRECT_IN',		1);
define('TRANS_TYPE_DIRECT_OUT',		2);
define('TRANS_TYPE_SALE_ORDER',		3);
define('TRANS_TYPE_RETURN_ORDER',		4);
define('TRANS_TYPE_CHANGE_ORDER',		5);
define('TRANS_TYPE_PACKET_EXCHANGE',	6);
define('TRANS_TYPE_OUTSIDE_SALE_ORDER',		7);
define('TRANS_TYPE_OUTSIDE_RETURN_ORDER',		8);

// 配送方式
define('SHIPPING_ID_CAC', -1); // 自提配方式ID
define('SHIPPING_ID_PINGTAI', 2); //平台配送方式ID

// 支付方式
define('PAY_ID_COD', 1);
define('PAY_ID_BALANCE', 5);
define('PAY_ID_VOUCHER', 6);
define('PAY_ID_PAYBACK', 8);
define('PAY_ID_ALIPAY', 4);
define('PAY_ID_WXPAY', 42);
define('PAY_ID_99BILL', 0);

//支付宝银行支付：储蓄卡
$alipay_bank_list = array(
    //'ICBCB2C'    => array('pay_code'=>'ICBCB2C','pay_name'=>'中国工商银行','pay_logo'=>'img/shop_process/bank/ICBCB2C.png'),
    'BOCB2C'    => array('pay_code'=>'BOCB2C','pay_name'=>'中国银行','pay_logo'=>'img/shop_process/bank/BOCB2C.png'),
    'CMB'       => array('pay_code'=>'CMB','pay_name'=>'招商银行','pay_logo'=>'img/shop_process/bank/CMB.png'),
    'CCB'       => array('pay_code'=>'CCB','pay_name'=>'中国建设银行','pay_logo'=>'img/shop_process/bank/CCB.png'),
    //'ABC'       => array('pay_code'=>'ABC','pay_name'=>'中国农业银行','pay_logo'=>'img/shop_process/bank/ABC.jpg'),
    'SPDB'      => array('pay_code'=>'SPDB','pay_name'=>'上海浦东发展银行','pay_logo'=>'img/shop_process/bank/SPDB.png'),
    'CIB'       => array('pay_code'=>'CIB','pay_name'=>'兴业银行','pay_logo'=>'img/shop_process/bank/CIB.png'),
    'GDB'       => array('pay_code'=>'GDB','pay_name'=>'广东发展银行','pay_logo'=>'img/shop_process/bank/GDB.png'),
    'SDB'       => array('pay_code'=>'SDB','pay_name'=>'深圳发展银行','pay_logo'=>'img/shop_process/bank/SDB.png'),
    'CMBC'      => array('pay_code'=>'CMBC','pay_name'=>'中国民生银行','pay_logo'=>'img/shop_process/bank/CMBC.png'),
    'COMM'      => array('pay_code'=>'COMM','pay_name'=>'交通银行','pay_logo'=>'img/shop_process/bank/COMM.png'),
    'CITIC'     => array('pay_code'=>'CITIC','pay_name'=>'中信银行','pay_logo'=>'img/shop_process/bank/CITIC.png'),
    'HZCBB2C'   => array('pay_code'=>'HZCBB2C','pay_name'=>'杭州银行','pay_logo'=>'img/shop_process/bank/HZCBB2C.png'),
    'CEBBANK'   => array('pay_code'=>'CEBBANK','pay_name'=>'中国光大银行','pay_logo'=>'img/shop_process/bank/CEBBANK.png'),
    //'NJCB'   => array('pay_code'=>'NJCB','pay_name'=>'南京银行','pay_logo'=>'img/shop_process/bank/NJCB.jpg'),
    'PAB'   => array('pay_code'=>'PAB','pay_name'=>'中国平安','pay_logo'=>'img/shop_process/bank/PAB.png'),
    //'HXB'   => array('pay_code'=>'HXB','pay_name'=>'华夏银行','pay_logo'=>'img/shop_process/bank/HXB.jpg'),
    'BOB'   => array('pay_code'=>'BOB','pay_name'=>'北京银行','pay_logo'=>'img/shop_process/bank/BOB.png'),
    'NBCB'   => array('pay_code'=>'NBCB','pay_name'=>'宁波银行','pay_logo'=>'img/shop_process/bank/NBCB.png'),
    //'BJRCB'   => array('pay_code'=>'BJRCB','pay_name'=>'北京农村商业银行','pay_logo'=>'img/shop_process/bank/BJRCB.jpg'),
    'PSBC-DEBIT'   => array('pay_code'=>'PSBC-DEBIT','pay_name'=>'中国邮政储蓄银行','pay_logo'=>'img/shop_process/bank/POST.png'),
    //'SRCB'   => array('pay_code'=>'SRCB','pay_name'=>'上海农村商业银行','pay_logo'=>'img/shop_process/bank/SRCB.jpg'),
    //'CBHB'   => array('pay_code'=>'CBHB','pay_name'=>'渤海银行','pay_logo'=>'img/shop_process/bank/CBHB.jpg'),
    //'GZCB'   => array('pay_code'=>'GZCB','pay_name'=>'广州银行','pay_logo'=>'img/shop_process/bank/GZCB.jpg'),
    //'GZRCC'   => array('pay_code'=>'GZRCC','pay_name'=>'广州农村商业农村','pay_logo'=>'img/shop_process/bank/GZRCC.jpg'),
    //'BEA'   => array('pay_code'=>'BEA','pay_name'=>'东亚银行','pay_logo'=>'img/shop_process/bank/BEA.jpg')
);
// 订单来源
define('SOURCE_ID_WEB', 3);

// 运费
define('SHIPPING_FEE_DEFAULT', 10);
define('SHIPPING_FREE_ORDER_PRICE', 399);

/**
 *  缓存过期
 */

define('CACHE_TIME_COMMON', 1800);		//通用缓存时间
define('CACHE_TIME_PRODUCT', 1800);		//商品页缓存时间
define('CACHE_TIME_ARTICLE', 1800);		//文章页缓存时间
define('CACHE_TIME_STATIC', 1800);		//静态内容缓存时间
define('CACHE_TIME_RUSHLIST', 1800);	//列表抢购缓存时间
define('CACHE_TIME_CATLIST', 1800);		//分类商品页缓存时间
define('CACHE_TIME_BRANDLIST', 1800);	//品牌商品页缓存时间
define('CACHE_TIME_AD', 1800);			//广告缓存时间
define('CACHE_TIME_LIUYAN', 1800);		//留言缓存时间
define('CACHE_TIME_RUSH', 1800);		//rush页缓存时间
define('CACHE_TIME_CAMPAIGN', 1800 );	//全场活动缓存时间
define('CACHE_TIME_SELECT_TYPE', 1800);	//type名称缓存时间
define('CACHE_TIME_SELECT_SIZE', 1800);	//size名称缓存时间
define('CACHE_TIME_RUSH_INFO', 1800);		//rush页缓存时间
define('CACHE_TIME_PRODUCT_AD', 1800);		//product detail页ad缓存时间
define('CACHE_TIME_PRODUCT_SUB', 30);		//product detail页sub缓存时间
define('CACHE_TIME_BRAND', 1800);			//product detail页brand缓存时间
define('CACHE_TIME_BRANDS', 1800);			//品牌大全页缓存时间
define('CACHE_TIME_SHOPS', 1800);			//店铺大全页缓存时间
define('CACHE_TIME_RECOMMEND_PRO', 86400);	//product detail页商品默认推荐缓存时间
define('CACHE_TIME_REGION_SHIPPING_FEE', 86400);	//product detail页等区域运费的缓存时间

define('CACHE_TIME_NAVIGATION',3600);  //导航缓存时间
define('CACHE_TIME_INDEX_FOCUS_IMAGE',30*60);   //首页焦点图缓存时间
define('CACHE_TIME_PRE_RUSH',15*60);    //预售rush缓存时间
define('CACHE_TIME_SALE_RUSH',15*60);   //正在进行的rush缓存时间
define('CACHE_TIME_TODAY_OVER_RUSH',15*60); //今日结束的rush缓存时间
define('CACHE_TIME_PROVIDER_SHIPPING_FEE_CONFIG', 3600); //供应商运费配置key:provider_shipping_fee_config_n (n=供应商ID)
define('CACHE_TIME_INDEX_PRODUCT',3600);//首页商品缓存时间
define('CACHE_TIME_BRAND_M_CATEGORYES',86400);// 品牌M获取分类
define('CACHE_TIME_BRAND_LIST_BY_CATEGORY',86400);// 品牌列表
define('CACHE_TIME_PC_INDEX_PRODUCT_INFO',86400);// PC首页产品信息
define('CACHE_TIME_PC_SEO',86400);// PC首页SEO信息

// 购物车商品保存时间
define('CART_SAVE_TIME', 'PT1200S');
define('CART_SAVE_SECOND', 1800);
define('CART_SIZE', 200);

// mcrypt_key
define('MCRYPT_KEY', 'TONGYI');

define('ARTICLE_CAT_HELP', 1);
define('ARTICLE_CAT_SPEC', 111);

//导航-首页id
define('NAV_INDEX_ID',26);
//首页公告分类id
define('INDEX_ARTICEL_CAT_ID',25);

define('INDEX_FOCUS_IMAGE_TAG', 'index_focus_image');
define('VIDEO_FOCUS_IMAGE_TAG', 'video_focus_image');
//超值促销广告位position_tag
define('INDEX_FOOTER_PROMOTIONS_TAG','index_footer_promotions');
//define('BRAND_AD_TAG', 'm_index_brand_row');
// 首页课程顶部广告位
define('INDEX_COURSE_TOP_TAG','index_course_top_tag');
//商品分类右下角广告位position_tag
define('INDEX_CATEGORY_TOP_TAG','index_category_top');
define('INDEX_CATEGORY_FOOTER_TAG','index_category_footer');

//导航右上角广告位position_tag
define('INDEX_NAV_TOP_TAG','index_nav_top');
//导航下方广告位position_tag
define('INDEX_NAV_FOOTER_TAG','index_nav_footer');

//限抢缺省图position_tag
define('INDEX_SALE_RUSH_POSITION_TAG','sale_rush');
//预售缺省如position_tag
define('INDEX_PRE_SALE_RUSH_POSITION_TAG','pre_sale_rush');

//商品详情页广告position_tag
define('PRODUCT_LEFT_POSITION_TAG','product_left');

//导航静态页面地址
define('NAVIGATION','$navigation_html=static_style_url()."/index/navigation.html";');
//首页焦点图静态页面地址
define('INDEX_FOCUS_IMAGE_HTML','$front_focus_image_url=static_style_url()."/index/front_focus_image.html";');

//性别
define('MALE',1);   //男
define('FAMALE',2); //女

//默认加载图url
define('IMG_LOADING_URL', 'img/common/loading_1.gif');//列表页默认加载图
define('IMG_ERROR_URL','img/common/error_t.png');//错误提示图

//rush,category 分页size 
//@changed by tony 2013-08-23 将分页数由原来的30改为60 
define('LIST_PAGE_SIZE',60);
define('M_LIST_PAGE_SIZE',12);//手机端每页显示多少商品
define('M_INDEX_PAGE_MAX',3);//手机端首页最多加载几页

//seo
define('SITE_NAME', '演示站网');

define('SITE_NAME_MOBILE', '演示站网 www.redtravel.cn');
define('PAGE_KEYWORDS' , SITE_NAME.'，keywords content.');
define('PAGE_DESCRIPTION' , SITE_NAME.'description content！');
define('PAGE_TITLE_SITE_NAME' , SITE_NAME.'_site_name_here');

//预计发货时间
define('EXPECTED_SHIPPING_DATE','当天16:00点');
define('MEMCACHE_ADMIN_USERNAME','admin'); 	// Admin Username
define('MEMCACHE_ADMIN_PASSWORD','admin@redtravel.cn');  	// Admin Password

define("SEND_MAIL", "servicestatic.redtravel.cn");
define('SESS_CHANGE_SID_PERTIME', false);
define("SESS_KEEP_IP_MAX_NUM",5);

define("CACHE_HTML_INDEX", 1);
define("CACHE_HTML_RUSH", 3);
define("CACHE_HTML_LIST", 3);
define("CACHE_HTML_INFO", 5);
define('FRONT_HOST', 'http://pc.redtravel.cn');
define('STATIC_DIR', FRONT_HOST.'/static');
define('STATIC_STORE_DIR', '/alidata/www/spkid/spkid_static');
define('IMAGE_STORE_DIR',  '/alidata/www/spkid/spkid_image');
define('VIDEO_COVER_PATH', IMAGE_STORE_DIR.'/wp_img');
define('USER_AV_PATH', STATIC_STORE_DIR.'/mobile/touxiang');

define("ENCRYPT_TYPE", "HASH");

define('PRODUCT_TOOTH_TYPE', 1);//牙科大类ID
define('PRODUCT_COURSE_TYPE', 2);//课程大类ID
//公司退包地址
//define('COMPANY_RETURN_ADDRESS', '上海市<br/> ....');
//自助退货理由
define('APPLY_RETURN_REASON', '$return_reason_arr=array(5=>"商品质量有问题",0=>"尺寸偏大",1=>"尺寸偏小",2=>"款式不喜欢",
                    3=>"配送错误",4=>"其他");');
define('POST_FORMAT_VIDEO', 223);
// 使用js/css的发行版本
define('JSCSS_DIST_VERSION', '151127.1' );
// 是否注册送积分
define ('USE_REGIST_POINT', true);
// 是否完善个人资料送积分
define ('USE_DATA_POINT', true);
// sphinx server ip
define ('SPHINX_SERVER_IP', '127.0.0.1');

define('ORDER_INVALID_TIME', 259200); //3天
/* End of file constants.php */
/* Location: ./application/config/constants.php */
