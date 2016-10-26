<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 系统参数配置，所生成的文件。
 * 生成日期：2016-10-09
 * @author: nobody
 */


//是否支持货到付款
defined('SYS_USE_COD') || define('SYS_USE_COD', '2'); // 是否支持货到付款
defined('SYS_USE_COD_YES') || define('SYS_USE_COD_YES', '1'); // 支持
defined('SYS_USE_COD_NO') || define('SYS_USE_COD_NO', '2'); // 不支持

//可用券数
defined('COUPON_MAX_NUM') || define('COUPON_MAX_NUM', '300'); 

//商品款号生成规则
defined('PRODUCT_SN_RULE') || define('PRODUCT_SN_RULE', 'cat_rand'); // 商品款号生成规则
defined('PRODUCT_SN_RULE_BRANDYEAR') || define('PRODUCT_SN_RULE_BRANDYEAR', 'brandyear'); // 品牌缩写+年(3位)+N位随机数
defined('PRODUCT_SN_RULE_CAT_RAND') || define('PRODUCT_SN_RULE_CAT_RAND', 'cat_rand'); // 分类编号+N位随机数
defined('PRODUCT_SN_RULE_WRITE') || define('PRODUCT_SN_RULE_WRITE', 'write'); // 手工输入

//商品款号随机数长度
defined('PRODUCT_RAND_LENGTH') || define('PRODUCT_RAND_LENGTH', '7'); // 商品款号随机数长度
defined('PRODUCT_RAND_LENGTH_SEVEN') || define('PRODUCT_RAND_LENGTH_SEVEN', '7'); // 7位
defined('PRODUCT_RAND_LENGTH_SIX') || define('PRODUCT_RAND_LENGTH_SIX', '6'); // 6位
defined('PRODUCT_RAND_LENGTH_FIVE') || define('PRODUCT_RAND_LENGTH_FIVE', '5'); // 5位

//三方发货合作方式
defined('THIRD_DELIVERY_COOP_ID') || define('THIRD_DELIVERY_COOP_ID', '3'); 

//首页默认产品个数
defined('MOBILE_INDEX_PRODUCT_NUM') || define('MOBILE_INDEX_PRODUCT_NUM', '8'); 

//手机首页最多显示产品数
defined('MOBILE_INDEX_MAX_PRODUCT_NUM') || define('MOBILE_INDEX_MAX_PRODUCT_NUM', '40'); 

//打印发票天数
defined('INVOICE_PRINT_DAYS') || define('INVOICE_PRINT_DAYS', '15'); 

//商品价格不算免邮字段
defined('FREE_FEE_EXCLUDE_PRODUCT_FIELD') || define('FREE_FEE_EXCLUDE_PRODUCT_FIELD', '(is_best=1 or is_offcode=1)'); 

//自营店铺
defined('SELF_SHOP') || define('SELF_SHOP', '1'); 

//订单发放积分天数
defined('SEND_ORDER_POINT_DAYS') || define('SEND_ORDER_POINT_DAYS', '15'); 

//字典类型
$dict_types = array (
  'diy_field' => '自定义字段',
  'cust_type' => '客户类型',
  'unit' => '单位',
);
defined('DICT_TYPES_DIY_FIELD') || define('DICT_TYPES_DIY_FIELD', 'diy_field'); // 自定义字段
defined('DICT_TYPES_CUST_TYPE') || define('DICT_TYPES_CUST_TYPE', 'cust_type'); // 客户类型
defined('DICT_TYPES_UNIT') || define('DICT_TYPES_UNIT', 'unit'); // 单位

//商品表字段对照
defined('PRODUCT_INFO_FIELD_MAP') || define('PRODUCT_INFO_FIELD_MAP', 'category_id, product_sn, product_name,brand_id,subhead, package_name,pack_method,desc_material,desc_waterproof,desc_crowd,desc_expected_shipping_date,desc_composition,desc_dimensions,desc_use_explain,desc_function_exlain,desc_notes,register_id ,unit_name ,shop_id ,genre_id,flag_id ,product_weight ,size_image ,price_show ,limit_num,content_source ,is_stop ,provider_id ,provider_productcode ,brand_id'); 

//订单客户信息对照
defined('SYS_ORDER_CLIENT_MAP') || define('SYS_ORDER_CLIENT_MAP', 'name,mobile_phone,field1,field2,field3,field4,field5,field6,field7,field8,field9,field10'); 

//默认注册证号
defined('DEFAULT_REGISTER_NO') || define('DEFAULT_REGISTER_NO', '20'); 

//课程类型ID
defined('GENRE_COURSE_ID') || define('GENRE_COURSE_ID', '2'); 

//课程默认品牌
defined('DEFAULT_BRAND_ID') || define('DEFAULT_BRAND_ID', '112'); 

//留言关联类型
$liuyan_rel_types = array (
  1 => '商品',
  2 => '礼包',
  3 => '课程',
  4 => '意见反馈',
  5 => '品牌',
);
defined('LIUYAN_REL_TYPES_1') || define('LIUYAN_REL_TYPES_1', '1'); // 商品
defined('LIUYAN_REL_TYPES_2') || define('LIUYAN_REL_TYPES_2', '2'); // 礼包
defined('LIUYAN_REL_TYPES_3') || define('LIUYAN_REL_TYPES_3', '3'); // 课程
defined('LIUYAN_REL_TYPES_4') || define('LIUYAN_REL_TYPES_4', '4'); // 意见反馈
defined('LIUYAN_REL_TYPES_5') || define('LIUYAN_REL_TYPES_5', '5'); // 品牌

//留言类型
$liuyan_types = array (
  1 => '咨询/留言',
  2 => '评价',
  3 => '测评',
  4 => '询价',
);
defined('LIUYAN_TYPES_1') || define('LIUYAN_TYPES_1', '1'); // 咨询/留言
defined('LIUYAN_TYPES_2') || define('LIUYAN_TYPES_2', '2'); // 评价
defined('LIUYAN_TYPES_3') || define('LIUYAN_TYPES_3', '3'); // 测评
defined('LIUYAN_TYPES_4') || define('LIUYAN_TYPES_4', '4'); // 询价

//电话订单来源ID
defined('ORDER_SOURCE_TEL_ID') || define('ORDER_SOURCE_TEL_ID', '2'); 

//短信商
$sms_supply = array (
  'sykj_http_sms' => 
  array (
    'sn' => '004245',
    'pwd' => '76SEqTcggF7Z',
    'url' => 'http://120.26.69.248/msg/HttpSendSM',
  ),
  'e_mas' => 
  array (
    'sn' => '111',
    'pwd' => '111',
    'url' => 'http://www.111.com',
  ),
);
defined('SMS_SUPPLY_SYKJ_HTTP_SMS') || define('SMS_SUPPLY_SYKJ_HTTP_SMS', 'a:3:{s:2:"sn";s:6:"004245";s:3:"pwd";s:12:"76SEqTcggF7Z";s:3:"url";s:35:"http://120.26.69.248/msg/HttpSendSM";}'); // sykj_http_sms
defined('SMS_SUPPLY_E_MAS') || define('SMS_SUPPLY_E_MAS', 'a:3:{s:2:"sn";s:3:"111";s:3:"pwd";s:3:"111";s:3:"url";s:18:"http://www.111.com";}'); // e_mas

//正用短信商
defined('CURRENT_SMS_SUPPLY') || define('CURRENT_SMS_SUPPLY', 'sykj_http_sms'); 

//是否启用短信
defined('SMS_ENABLED') || define('SMS_ENABLED', '1'); // 是否启用短信
defined('SMS_ENABLED_YES') || define('SMS_ENABLED_YES', '1'); // 是
defined('SMS_ENABLED_NO') || define('SMS_ENABLED_NO', '2'); // 否
