<?php
function pass_hash($pass){
    return hash('sha512', $pass.md5('yyw').'zesxW6qHePNZwcX6');
}
function db_create_in($item_list)
{
	if (empty($item_list))
	{
		return " IN ('') ";
	} else
	{
		if (!is_array($item_list))
		{
			$item_list = explode(',', $item_list);
		}
		$item_list = array_unique($item_list);
		$item_list_tmp = '';
		foreach ($item_list AS $item)
		{
			if ($item !== '')
			{
				$item = addslashes($item);
				$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
			}
		}
		if (empty($item_list_tmp))
		{
			return " IN ('') ";
		} else
		{
			return ' IN (' . $item_list_tmp . ') ';
		}
	}
}

/**
 * 将二维数组 整合为一组key/value对
 * @param array $arr
 * @param string $k
 * @param string $v
 * @return array
 */
function get_pair($arr, $k, $v, $ext=array())
{
	$result = $ext;
	foreach ($arr as $val)
	{
		if (is_object($val)) {
			$result[$val->$k] = $val->$v;
		}elseif (is_array($val)) {
			$result[$val[$k]] = $val[$v];
		}
	}
	return $result;
}

/**
 * 为数组编制索引
 * @param array $array
 * @param string $key
 * @return array
 */
function index_array($array, $key)
{
	$result = array();
	foreach ($array as $k => $v)
	{
		if (is_object($v)) {
			$result[$v->$key] = $v;
		}elseif (is_array($v)) {
			$result[$v[$key]] = $v;
		}
	}
	return $result;
}

/**
 *
 * @param string $msg_detail 信息内容，已经过翻译的内容
 * @param int $msg_type 0 是信息， 1 是警告 2 是确认
 * @param type $links
 * @param type $auto_redirect
 */
function sys_msg($msg_detail, $msg_type = 0, $links = array(), $auto_redirect = TRUE)
{
	$CI = & get_instance();
	if (count($links) == 0)
	{
		$links[0]['text'] = '返回';
		$links[0]['href'] = 'javascript:history.go(-1)';
	}
	if ($CI->input->is_ajax_request()) {
		echo json_encode(array('err' => $msg_type, 'msg' => $msg_detail, 'links' => $links));
	}else {
		$data = array(
		'msg_detail' => $msg_detail,
		'msg_type' => $msg_type,
		'links' => $links,
		'default_url' => $links[0]['href'],
		'auto_redirect' => $auto_redirect
		);
		print $CI->load->view('common/message', $data, TRUE);
	}
	// 调用exit之前，关闭数据库链接。
	if (class_exists('CI_DB') AND isset($CI->db))
	{
		$CI->db->close();
	}
	exit;
}


/** img path begin **/
// static_style_url
function static_style_url($path='')
{
    $path = str_replace('version', JSCSS_DIST_VERSION, $path);
    return static_url($path);
}

function static_url($path=''){
    eval(STATIC_HOST_CONFIG);
    $rand_index=rand(0,count($static_host_arr)-1);
    return $static_host_arr[$rand_index]."/".$path;
}

//db_img_url
function img_url( $path = "" )
{
	return get_img_host().'/'.$path;
}

if (!function_exists("get_img_host")) {
    function get_img_host( ) {
	global $img_host;
        if (empty($img_host)) {
            $CI = & get_instance();
            $img_host = $CI->config->item('IMG_HOSTS');
        }
        if (is_array($img_host)) {
            $count = count($img_host);
            $index = rand(0x0000, $count - 0x0001);
            return $img_host[$index];
        } else {
            return $img_host;
        }
    }
}
/** img path end **/

function front_url($path='')
{
	return FRONT_HOST.'/'.$path;
}

function mask_str($str,$head_len, $tail_len,$sep='...')
{
	$len = mb_strlen($str,'utf8');
	if($len<=$head_len) return $str;

	$result = '';
	$result .= mb_substr($str,0,$head_len,'utf8');
	$str = mb_substr($str,$head_len-1,$len,'utf8');
	if($len>$tail_len) $result .= $sep;
	$result .= mb_substr($str,-$tail_len,$tail_len,'utf8');

	return $result;
}
//过滤html所有的标签
function filter_html_des($des){
    if (strlen($des) <= 0) return;
    $html_reg = "/<\/?[^>]+>/i";
    $des = preg_replace( $html_reg, '', $des);
    $des = preg_replace("/&nbsp;|\r|\n|\s/i", '', $des);
    $des = mask_str($des, 40, 0);
    return $des;
}

// 是否以超级用户的方式访问
function valid_ghost()
{
	$CI = &get_instance();
	$keycode = $CI->input->get('keycode');
	if(!$keycode) return FALSE;
	$ip = $CI->input->ip_address();
	return $keycode == strtoupper(md5($ip.'eab8bcae23fc'));
}

// 对时间进行加减
function date_change($date_str,$intval)
{
	$method = $intval[0]=='-'?'sub':'add';
	$date = new DateTime($date_str);
	$date->$method(new DateInterval(str_replace('-','',$intval)));
	return $date->format('Y-m-d H:i:s');
}

// 取得cart_sn
function get_cart_sn()
{
	$CI = & get_instance();
	if(!$CI->session->userdata('cart_sn'))
		$CI->session->set_userdata('cart_sn',$CI->session->userdata('session_id'));
	return $CI->session->userdata('cart_sn');
}

function m_encode($str)
{
    return pass_hash( $str );
    $td = mcrypt_module_open(MCRYPT_DES, '', 'ecb', ''); //使用MCRYPT_DES算法,ecb模式
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    $ks = mcrypt_enc_get_key_size($td);
    $key = MCRYPT_KEY; //密钥
    $key = substr(md5($key), 0, $ks);
    $string = base64_encode($string);

    mcrypt_generic_init($td, $key, $iv); //初始处理
    //加密
    $encrypted = mcrypt_generic($td, $string);

    //结束处理
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    return base64_encode($encrypted);
}


function m_decode($string)
{
    $td = mcrypt_module_open(MCRYPT_DES, '', 'ecb', ''); //使用MCRYPT_DES算法,ecb模式
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    $ks = mcrypt_enc_get_key_size($td);
    $key = MCRYPT_KEY; //密钥
    $key = substr(md5($key), 0, $ks);

    mcrypt_generic_init($td, $key, $iv); //初始处理
    //解密
    $decrypted = mdecrypt_generic($td, base64_decode($string));

    //结束处理
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
    //解密后,可能会有后续的\0,需去掉
    return trim(base64_decode($decrypted));
}



function _smarty_modifier_truncate($string, $length = 80, $etc = '', $break_words = false)
{
    if ($length == 0)
        return '';

    $short = sysSubStr($string, $length);
    if($short != $string)
    {
        $length -= strlen($etc);
        return sysSubStr($string, $length).$etc;
    } else
        return $string;
}

function sysSubStr($String,$Length)
{
    if (strlen($String) <= $Length )
    {
        return $String;
    }
    else
    {
        $I = 0; //字节
        $S = 0; //显示上的展位，一个中文占2个英文字母的位置
        while ($S < $Length)
        {
            $StringTMP = substr($String,$I,1);
            if ( ord($StringTMP) >=224 )
            {
                $StringTMP = substr($String,$I,3);
                $I = $I + 3;
                $S = $S + 2; //在unicod里中文占3字节，但是显示上一个中文占2个英文字母的位置
            }
            elseif( ord($StringTMP) >=192 )
            {
                $StringTMP = substr($String,$I,2);
                $I = $I + 2;
                $S = $S + 2;
            }
            else
            {
                $I = $I + 1;
                $S = $S + 1;
            }
            $StringLast[] = $StringTMP;
        }

        $StringLast = implode("",$StringLast);
        return $StringLast;
    }
}

function page_and_size($filter)
{
		$filter['page_size'] = (isset($filter['page_size']) && intval($filter['page_size']) > 0) ? (int) $filter['page_size'] : 10;
		$filter['page_count'] = max(ceil($filter['record_count'] / $filter['page_size']), 1);
		$filter['page'] = (isset($filter['page']) && intval($filter['page']) > 0) ? (int) $filter['page'] : 1;
		if ($filter['page'] > $filter['page_count']) $filter['page'] = $filter['page_count'];
		return $filter;
}

function get_pager_param($filter)
{
		$CI = & get_instance();
		$page = trim($CI->input->post('page'));
		if (!empty($page)) $filter['page'] = $page;
		$page_size = trim($CI->input->post('page_size'));
		if (!empty($page_size)) $filter['page_size'] = $page_size;
		$sort_by = trim($CI->input->post('sort_by'));
		if (!empty($sort_by)) $filter['sort_by'] = $sort_by;
		$sort_order = trim($CI->input->post('sort_order'));
		if (!empty($sort_order)) $filter['sort_order'] = $sort_order;
		$template = trim($CI->input->post('template'));
		if (!empty($template)) $filter['template'] = $template;
		return $filter;
}

function adjust_path($content)
{   
    $content = str_replace('<img','<img style="width:100%;" ',$content);
	return str_replace('/public/data/images/upload/',img_url('upload/'),$content);
}

function goto_login($back_url='')
{
	if($back_url) {
		$CI=&get_instance();
		$CI->session->set_userdata('back_url',$back_url);
	}
	redirect('user/login');
}

function get_url_contents($url)
{
    if (ini_get("allow_url_fopen") == "1")
        return file_get_contents($url);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $url);
    $result =  curl_exec($ch);
    curl_close($ch);

    return $result;
}

function qq_post($url, $data)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_URL, $url);
    $ret = curl_exec($ch);

    curl_close($ch);
    return $ret;
}

function generate_css ($css)
{
	$output="";
	foreach( $css as $item )
	{
		$output.=link_tag(img_url($item));
	}
	return $output;
}

/**
 * 创建分页
 * @param array $arg
 * @return string 
 */
function create_pagination($arg) {
    if ($arg['pages'] < 2)
	return;
    $arr_list_num = array();
    $arr_list_num[] = 1;
    $end_dot = '';
    if ($arg['page'] - $arg['list_num'] > $arg['list_num']) {
	$start = $arg['page'] - $arg['list_num'];
	$arr_list_num[] = '...';
    } else {
	$start = 2;
    }
    if ($arg['page'] + $arg['list_num'] + 1 < $arg['pages']) {
	$end = $arg['page'] + $arg['list_num'];
	$end_dot = '...';
    } else {
	$end = $arg['pages'] - 1;
    }

    for ($i = $start; $i <= $end; $i++) {
	$arr_list_num[] = $i;
    }
    if ($end_dot) {
	$arr_list_num[] = $end_dot;
    }
    if ($arg['pages'] > 1)
	$arr_list_num[] = $arg['pages'];
    if ($arg['is_return']) {
	return $arr_list_num;
    }
}

/**
 * 校验清除缓存的信息是否正确.
 */
function need_clear_cache($clear_time, $sign) {
    $signKey = md5($clear_time . $this->config->item('mcrypt_key'));
    return $sign == $signKey ? true : false;
}
    /**
     * 获取静态内容
     * 优先查询memcache
     * $param: $memcache_key $static_url
     */
    function get_static_content($memcache_key,$static_url)
    {
        $ci=&get_instance();
        $content=$ci->cache->get($memcache_key);
        if($content==false)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $static_url);
            $content= curl_exec($ch);
            curl_close($ch);
            $ci->cache->save($memcache_key,$content,CACHE_TIME_NAVIGATION);
        }
        return $content;
    }

if (!function_exists('fix_price')) {
	function fix_price($value)
	{
		return round(floatval($value),2);
	}
}

/**
 * 获得用户的真实IP地址
 * @return  string
 */
if (!function_exists('real_ip')) {

    function real_ip() {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);

                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip) {
                    $ip = trim($ip);

                    if ($ip != 'unknown') {
                        $realip = $ip;

                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }

        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';

        return $realip;
    }
}
// 导航上方广告位
if (!function_exists('nav_top_ad')) {
    function nav_top_ad() {
        $CI=&get_instance();
        $CI->load->library('lib_ad');
        //导航右上角广告位
        $nav_top_ad = $CI->lib_ad->get_ad_by_position_tag('index_nav_top_ad',INDEX_NAV_TOP_TAG);
        return !empty($nav_top_ad) ? $nav_top_ad[0] : array();
    }
}
//导航
if (!function_exists('get_nav')) {
    function get_nav (){
        $CI = &get_instance();
        $CI->load->model('product_model');
        $nav_list = $CI->cache->get('navigation_cate');
        if (empty($nav_list)) {
            $nav_list = $CI->product_model->get_category();
            $CI->cache->save('navigation_cate',$nav_list,CACHE_TIME_NAVIGATION);
        }
        return $nav_list;
    }   
}
// 导航二级分类及品牌
if (!function_exists('get_nav_subtype_brand')) {
    function get_nav_subtype_brand(){
        $data = array();
        $CI = &get_instance();
        $CI->load->library('memcache');
        $nav_subtype = $CI->memcache->get('front_nav_subtype');
        $cate_topten_brand = $CI->memcache->get('cate_topten_brand');
        if (empty($nav_subtype)) return $data;
            $nav_subtype_arr = unserialize($nav_subtype);
            $cate_topten_brand_arr = unserialize($cate_topten_brand); 
            $nav_goodstype = array();
            $nav_brand = array();
                $tmp_arr = array(
                    "res_id" => "00279",
                    "slot_no" => 159446,
                    "slot_priority" => 1,
                    "image_banner_1" => "",
                    "image_banner_2" => "",
                    "image_banner_3" => "",
                    "text" => "",
                    "connect_url" => "",
                    "related_group" => "",
                    "display_config" => "category",
                    "disp_start_dt" => "\/Date(1358438400000)\/",
                    "disp_end_dt" => "\/Date(1577894400000)\/",
                    "add_all" => null,
                    "add_sold" => null,
                    "goods_item_1" => "",
                    "goods_item_2" => "",
                    "goods_item_3" => "",
                    "gi_1_nm" => "",
                    "gi_2_nm" => "",
                    "gi_1_sell_price" => 0,
                    "gi_2_sell_price" => 0, 
                    "gi_1_deal_price" => 0,
                    "gi_2_deal_price" => 0, 
                    "gi_1_delivery_fee" => 0,
                    "gi_2_delivery_fee" => 0,
                    "gi_1_auction_kind" => "",
                    "gi_2_auction_kind" => "",
                    "gi_1_succ_bid_poss_price" => 0,
                    "gi_2_succ_bid_poss_price" => 0,
                    "gi_1_min_succ_bid_price" => 0,
                    "gi_2_min_succ_bid_price" => 0,
                    "gi_1_max_succ_bid_price" => 0,
                    "gi_2_max_succ_bid_price" => 0,
                    "gi_1_auction_start_dt" => "\/Date(-62135596800000)\/",
                    "gi_2_auction_start_dt" => "\/Date(-62135596800000)\/",
                    "gi_1_auction_end_dt" => "\/Date(-62135596800000)\/",
                    "gi_2_auction_end_dt" => "\/Date(-62135596800000)\/",
                    "gi_1_remain_time" => null,
                    "gi_2_remain_time" => null,
                    "gi_1_bidding_cnt" => 0,
                    "gi_2_bidding_cnt" => 0,
                    "gi_1_discount" => 0,
                    "gi_2_discount" => 0,
                    "gi_1_auction_total_amt" => 0,
                    "gi_2_auction_total_amt" => 0,
                    "gi_1_nickname" => "",
                    "gi_2_nickname" => "",
                    "gi_1_sell_amt" => 0,
                    "gi_2_sell_amt" => 0,
                    "gi_1_contr_cnt" => 0,
                    "gi_2_contr_cnt" => 0,
                    "gi_1_contr_amt" => 0,
                    "gi_2_contr_amt" => 0,
                    "img_contents_no_1" => 0,
                    "img_contents_no_2" => 0,
                    "img_contents_no_3" => 0, 
                    "gi_1_goods_chg_dt" => "",
                    "gi_2_goods_chg_dt" => "",
                    "gi_1_cust_gr" => "",
                    "gi_2_cust_gr" => "",
                    "gi_1_charity_item_yn" => "N",
                    "gi_2_charity_item_yn" => "N",
                    "gi_1_review_cnt" => 0,
                    "gi_1_pre_review_cnt" => 0,
                    "gi_1_goods_avg_point" => 0,
                    "sell_cust_no" => "",
                    "seller_plus_point" => 0,
                    "seller_minus_point" => 0,
                    "call_id" => "",
                    "short_domain" => "",
                    "brand_nm" => "",
                    "maker_nm" => "",
                    "language" => "ZH-CN",
                    "group_price" => 0,
                    "group_retail_price" => 0,
                    "group_price_now_qty" => 0,
                    "group_price_min_qty" => 0,
                    "group_price_max_qty" => 0,
                    "group_achieve_yn" => "",
                    "add_image_cnt" => 0,
                    "dealer_logo_img1" => "",
                    "adult_yn" => "",
                    "delivery_fee_condition" => "",
                    "now_contr_cnt" => 0,
                    "day_contr_cnt" => 0,
                    "week_contr_cnt" => 0,
                    "month_contr_cnt" => 0,
                    "group_no" => "",
                    "category" => "",
                    "gender" => "ALL",
                    "age" => "ALL",
                    "trad_way" => "",
                    "global_yn" => "",
                    "blind_time" => 0,
                    "auction_benefit" => "",
                    "auction_kind" => "",
                    "origin_gd_lang_cd" => "",
                    "gd_nm_en" => "",
                    "gd_nm_id" => "",
                    "gd_nm_ja" => "",
                    "gd_nm_ko" => "",
                    "gd_nm_ms" => "",
                    "gd_nm_zh" => "",
                    "gd_nm_zh_cn" => "",
                    "gd_nm_zh_hk" => "",
                    "gd_nm_zh_tw" => "",
                    "brand_coupon_yn" => ""                  
                );
            
            foreach ($nav_subtype_arr as $cate_id => $row) {
                
                //二级分类
                $tmp_arr["title"] = $row['cat_name'];
                $html = "";
                if (!empty($row['goods_type'])) $html .= "<div class=\"group_cate\"><dl>"; 
                
                foreach ($row['goods_type'] as $type) {
                    $html .= "<dd><a href=\"/category-".$type['category_id'].".html\">".$type['category_name']."</a></dd>";
                }
                
                if (!empty($row['goods_type'])) $html .= "</dl></div>";
                $tmp_arr["html"] = $html;
                $nav_goodstype[] = $tmp_arr;
                // 导航下热销品牌
                $bhtml = "";
                if (!empty($cate_topten_brand_arr[$cate_id])) $bhtml = "<dl class='group_brand'><dt><dfn class='fb'>热卖品牌</dfn></dt>";
                foreach ($cate_topten_brand_arr[$cate_id] as $brand) {
                   $bhtml .= "<dd><a href='/brand-".$brand['brand_id'].".html'><img src='".img_url($brand['brand_logo'])."' alt='".$brand['brand_name']."' width='108' height='50'></a></dd>";                  
                }
                $nav_brand[] = array('html' => $bhtml);
            }
            
            if (!empty($nav_goodstype)) $data['nav_subtype'] = json_encode($nav_goodstype);
            if (!empty($nav_subtype_arr)) $data['goods_type'] = $nav_subtype_arr;
            if (!empty($nav_brand)) $data['nav_brand'] = json_encode($nav_brand);
        return $data;  
    }
}

if (!function_exists('get_page_value')) {
    function get_page_value(){
        $CI = &get_instance();
        $CI->config->load('global', true);
        $page_value = $CI->config->item('page_value');
        return json_encode($page_value);

    }
}

//访问量
// 返回某个产品的访问量
// 涉及到的memcache key
// @type: product/article/course
// pv_@type_@date('dH')         : 小时内的产品/文章ids
// pv_@type_@date('dH')_$id     : 小时内的产品/文章访问量
// pv_@type_$id                 : 产品/文章的访问量
// $autoAdd : 是否自动增加
    function get_page_view($type,$id, $autoAdd=true){
        $CI = &get_instance();
        $CI->load->model('product_model');
        $CI->load->model('wordpress_model');
        $CI->load->library('memcache');

        //定义某商品的key 
        $key =  'pv_'.$type.'_'.$id;

        //获取key的值
        $pv = $CI->memcache->get($key);

        //如果访问量为空，那么从数据库取得最新的值
        if(empty($pv) && $id >0){
            //如果是产品，那么从产品表取出访问量
            if($type == "product" || $type == "course"){
                // TODO  那么从产品表取出访问量
                $obj = $CI->product_model->product_info($id);
                $pv=$obj->pv_num;
            }elseif($type=='article'){
                // TODO  那么从wordpress中表取出文章的访问量
                $obj = $CI->wordpress_model->get_article_views($id);
                $pv=$obj->pv_num;
            }
        }
        //访问量+1
        if( !$autoAdd ) return $pv;
        $pv += 1;

        //将新值写进mencache
        if( $id > 0 ){
	        $CI->memcache->delete($key);
	        $CI->memcache->save($key,$pv, 7200);
        }


        //每个小时的key，记录一个小时内 有多少产品被访问到
        $hour = date('dH');
        $key_type_hour_ids = 'pv_'.$type.'_'.$hour.'_ids';

        $pv_type_hour_ids = $CI->memcache->get($key_type_hour_ids);

        //判断 将当前的产品id加到小时内
        if(empty($pv_type_hour_ids)){
            $pv_type_hour_ids = array($id);
        }else{
            array_push($pv_type_hour_ids, $id);
            $pv_type_hour_ids = array_unique($pv_type_hour_ids);
        }
        //将一个小时内的被访问的产品id，写进memcache
        $CI->memcache->delete($key_type_hour_ids);
        $CI->memcache->save($key_type_hour_ids,$pv_type_hour_ids, 7200);

        // 记录产品的小时访问量
        $key_type_hour_id = 'pv_'.$type.'_'.$hour."_".$id;

        $pv_type_hour_id = $CI->memcache->get($key_type_hour_id);

        //判断 将当前的产品id 访问量+1
        if(empty($pv_type_hour_id)){
            $pv_type_hour_id = 1;
        }else{
            $pv_type_hour_id += 1;
        }
        //将新值写进memcache
        $CI->memcache->delete($key_type_hour_id);
        $CI->memcache->save($key_type_hour_id,$pv_type_hour_id, 7200);

        return $pv;

    }

    /**
     * 用户的商品 收藏
     * @$user_id  用户id
     * 'collect_'.$user_id  用户的 收藏商品的数组
     */
    if (!function_exists('get_collect_data')) {
        function get_collect_data(){
            $CI=&get_instance();
            $user_id=$CI->session->userdata('user_id');
            return $CI->session->userdata('collect_'.$user_id);
        }
    }

    /**
     * 用户的文章 点赞
     * @$user_id  用户id
     * 'praise_'.$user_id  用户的 点赞文章的数组
     */
    if (!function_exists('get_praise_data')) {
        function get_praise_data(){
            $CI=&get_instance();
            $user_id=$CI->session->userdata('user_id');
            return $CI->session->userdata('praise_'.$user_id);
        }
    }

    /**
     * 判断值是否在二维数组中
     *
     */
    if (!function_exists('deep_in_array')) {
        function deep_in_array($value, $array) {   
        foreach($array as $item) {   
            if(!is_array($item)) {   
                if ($item == $value) {  
                    return true;  
                } else {  
                    continue;   
                }  
            }   
               
            if(in_array($value, $item)) {  
                return true;      
            } else if(deep_in_array($value, $item)) {  
                return true;      
            }  
        }   
        return false;  
    }
} 
function is_mobile_number($str){
return preg_match("/1\d{10}$/",$str);
}

/**
 * 生成随机现金券号,规则:根据年份以大写字母‘A-Z’+11随机数组成
 */
if (!function_exists('getVoucherDes')){
    function getVoucherDes(){
        srand((double)microtime()*1000000000000);
        $voucher_sn = mt_rand();
        if(strlen($voucher_sn) < 11)
        {
            $voucher_sn = str_pad($voucher_sn,11,'0',STR_PAD_LEFT);
        }
        elseif(strlen($voucher_sn) > 11)
        {
            $voucher_sn = substr($voucher_sn,0,11);
        }
        $year_diff = date('Y') - 2015;
        $alphabets =  array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        for($i = 0; $i <= $year_diff; $i++){
            $str = $alphabets[$i];
        }
        $voucher_sn = $str . $voucher_sn;
        return $voucher_sn;
    }
}
//解析js中用escape处理过的值
function js_unescape($str)
{
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++)
    {
        if ($str[$i] == '%' && $str[$i+1] == 'u')
        {
            $val = hexdec(substr($str, $i+2, 4));
            if ($val < 0x7f) $ret .= chr($val);
            else if($val < 0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
            else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f)); 
            $i += 5;
        }
        else if ($str[$i] == '%')
        {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        }
        else $ret .= $str[$i];
    }
    return $ret;
}

//该函数基于php mbstring扩展
function cutstr($str, $start, $len, $postfix = '...') {
    $str = strip_tags($str);
    if (function_exists('mb_substr')) {
        return mb_strlen($str) < $len ? $str : (mb_substr($str, $start, $len) . $postfix);
    } else {
        return $str;
    }
}
