<?php

/**
 * 一淘网 增量索引
 * 数据索引文件,文件地址:/taobao/
 * 2012/09/18 V3.7
 */
class Etao_increment extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->admin_id = 0;
        $this->time = date('Y-m-d H:i:s');
        ini_set('max_execution_time', '0');
        set_time_limit(0);
    }

    function index() {
        $this->load->model('etao_model');
        //商家促销活劢信息列表
        $promotion = $this->etao_model->get_rush_list();
        $prom_act_url = ''; //商家促销活劢文件地址
        if (count($promotion) > 0) {
            $prom_act_url = '<promotion>' . API_URL . '/etao/PromotionActivities.xml</promotion>';
        }
        $pmxml = '<?xml version="1.0" encoding="utf-8" ?>';
        $pmxml.='<root>';
        $pmxml.='<version>1.0</version>';
        $pmxml.='<modified>' . $this->local_date('Y-m-d H:i:s') . '</modified>';
        $pmxml.='<seller_id>宝贝购</seller_id>';
        $pmxml.='<promotion_activities>';
        foreach ($promotion as $value) {
            $pmxml.='<activity>';
            $pmxml.='<pa_id>' . $value['rush_id'] . '</pa_id>';
            $rush_name = htmlspecialchars(str_replace(' ', '_', $value['rush_index']));
//            $rush_name = 111;
            $pmxml.='<activities_title>' . $rush_name . '</activities_title>';
            $pmxml.='<promotion_type>2</promotion_type>';
            //<!--促销类型，1=直降,2=限时贩，3=满就减，4=买就赠，5=多买多折，6=满就赠，7=买就减,参加多种活劢活劢之间用，隔开如：1,6--> 
            //$pmxml.='<activities_start>' . $this->local_date("y-m-d h:m:i", $value['start_date']) . '</activities_start>';
            $pmxml.='<activities_start>' . $value['start_date'] . '</activities_start>';
            //<!--活劢开始时间, 格式：2011-01-17 12:00:05，二十四小时制，精确到秒--> 
            //$pmxml.='<activities_end>' . $this->local_date("y-m-d h:m:i", $value['end_date']) . '</activities_end>';
            $pmxml.='<activities_end>' . $value['end_date'] . '</activities_end>';
            //<!--活劢结束时间, 格式：2011-01-17 12:00:09，二十四小时制，精确到秒--> 
            $jump_url = empty($value['jump_url']) ? FRONT_SITE_URL . 'rush-' . $value['rush_id'] . '.html' : $value['jump_url'];
            $pmxml.='<activities_url>' . $jump_url . '</activities_url>'; //<!--促销活劢链接--> 
            $pmxml.='<activities_image>' . IMG_SERVER_URL . $value['image_ing_url'] . '</activities_image>';
            $pmxml.='</activity>';
        }
        $pmxml.='</promotion_activities>';
        $pmxml.='</root>';
//header('Content-type: application/xml; charset=utf-8');
        file_put_contents(API_PATH . '/PromotionActivities.xml', $pmxml);
        $this->create_xml($prom_act_url);
    }

    /**
     * 生成增量索引文件及其商品文件
     * @param string $prom_act_url  促销信息xml文件地址 
     */
    public function create_xml($prom_act_url) {
        $this->load->model('etao_model');
        $this_outer = $this->etao_model->get_add_pro(1);

        $last_outer = @unserialize(file_get_contents(API_PATH . '/last_outer.dat'));
        $this_outer_array = array();
        $last_outer_array = array();

        foreach ($this_outer as $val) {
            $this_outer_array[] = $val['product_id'];
        }

        $need_add = array();
        $to_delete = array();
        $need_delete = array();
        if ($last_outer) {
            foreach ($last_outer as $val) {
                $last_outer_array[] = $val;
            }
            //选出需要新增的
            foreach ($this_outer as $val) {
                if (!in_array($val['product_id'], $last_outer_array)) {
                    $need_add[] = $val['product_id'];
                }
            }
            //选出需要删除的
            foreach ($last_outer as $key => $val) {
                if (!in_array($val, $this_outer_array)) {
                    $need_delete[] = $val;
                    $full_index = ceil(($key + 1) / 1000);
                    $to_delete[$full_index][] = $val;
                }
            }
        } else {
            //首次，全部为新增并且没有要删除的
            foreach ($this_outer as $val) {
                $need_add[] = $val['product_id'];
            }
        }
        /**
         * 写增量索引xml -------begin-------> 对应更新后的商品信息xml
         */
        if (!empty($need_add) || !empty($need_delete)) {
            $incre_index_xml = ''; //增量索引str
            $latest_pro_xml = ""; //上次的末尾文件
            $brand_info = $this->etao_model->getbrand();
            //1.======执行更新,生成增量索引xml
            //1.1增量索引xml begin
//        header('Content-type: application/xml; charset=utf-8');
            $incre_index_xml = '<?xml version="1.0" encoding="utf-8" ?>';
            $incre_index_xml.='<root>';
            $incre_index_xml.='<version>1.0</version>';
            $incre_index_xml.='<modified>' . $this->local_date('Y-m-d H:i:s') . '</modified>';
            $incre_index_xml.='<seller_id>宝贝购</seller_id>';
            $incre_index_xml.='<cat_url>' . API_URL . '/etao/SellerCats.xml</cat_url>';
            $incre_index_xml.=$prom_act_url; //商家促销活劢文件地址
            $incre_index_xml.='<dir>' . API_URL . '/etao/product/</dir>';
            $incre_index_xml.='<item_ids>';

            //2.======执行更新,生成商品目录
            //2.1新增begin
            if ($need_add) {
                $product_detail = $this->etao_model->getpro($need_add);
                //        $incre_begin_add = ceil(count($last_outer_array) / 1000)+1; //全量索引中最末索引的后一个，给增量索引用
                $incre_begin_add = ceil(count($last_outer_array) / 1000);
                $incre_last = count($last_outer_array) % 1000 > 0 ? count($last_outer_array) % 1000 : 0;
                $pro_arr = array();
                if ($incre_last == 0) {//上次更新结束是整数，直接往后拼接数组
                    $pro_arr = array_chunk($product_detail, 1000);
                    $incre_begin_add += 1;
                } else {//上次更新结束有余数，先凑整后分割
                    $pro_arr = array_chunk(array_slice($product_detail, 1000 - $incre_last), 1000);
                    array_unshift($pro_arr, array_slice($product_detail, 0, 1000 - $incre_last));
                }
                $upl_add_str = '';
                foreach ($pro_arr as $key => $pro) {
                    if (count($pro) > 0) {
                        $incre_index_xml.='<outer_id action="upload">' . $incre_begin_add . '</outer_id>'; //增量索引outer_id
                        //增量索引商品xml

                        $upl_add_str .= '<?xml version="1.0" encoding="utf-8"?>';
                        $upl_add_str .= '<items>';
                        foreach ($pro as $value) {
                            $upl_add_str .= $this->getproxml($incre_begin_add, $value, $brand_info);
                        }
                        if ($key == 0 && count($pro) < 1000) {//连接上次的继续,该文件会有upl和dele
                            $latest_pro_xml = $upl_add_str;
                            $upl_add_str .= '</items>';
                            file_put_contents(API_PATH . '/product/' . $incre_begin_add . '.xml', $upl_add_str);
                            break;
                        }

                        $upl_add_str .= '</items>';
                        file_put_contents(API_PATH . '/product/' . $incre_begin_add . '.xml', $upl_add_str);
                        $incre_begin_add++;
                        unset($upl_add_str);
                    }
                }
            }
            //新增end
            //2.2删除begin
            if ($need_delete) {
                $product_detail = $this->etao_model->getpro($need_delete, TRUE); //所有要delete的商品
                foreach ($to_delete as $key => $val) {
                    $upl_dele_pro = array();
                    if (count($val) > 0) {
                        foreach ($val as $product_id) {
                            foreach ($product_detail as $detail) {
                                if ($product_id == $detail["product_id"]) {
                                    array_push($upl_dele_pro, $detail);
                                }
                            }
                        }
                    }
                    if (count($upl_dele_pro) > 0) {
                        $incre_index_xml.='<outer_id action="delete">' . $key . '</outer_id>'; //增量索引outer_id=>全量索引
                        //增量索引商品xml
                        $upl_del_str = "";
                        if (!empty($latest_pro_xml) && $key == count($to_delete)) {//上次最末的文件有无库存商品，其商品文件进行拼接
                            $upl_del_str .= $latest_pro_xml;
                        } else {
                            $upl_del_str .= '<?xml version="1.0" encoding="utf-8"?>';
                            $upl_del_str .= '<items>';
                        }
                        foreach ($upl_dele_pro as $value) {
                            $upl_del_str .= $this->getproxml($key, $value, $brand_info, true);
                        }
                        $upl_del_str .= '</items>';
                        file_put_contents(API_PATH . '/product/' . $key . '.xml', $upl_del_str);
                        unset($upl_del_str);
                    }
                    unset($upl_dele_pro);
                }
            }
            //删除end
            //1.1增量索引xml continue
            $incre_index_xml.='</item_ids>';
            $incre_index_xml.='</root>';
            //header('Content-type: application/xml; charset=utf-8');
            file_put_contents(API_PATH . '/IncrementIndex.xml', $incre_index_xml);
            //增量索引xml end 
        }
        //记录所有商品id(全量和增量中包含的 )
        $all_upld_ids = array_merge($last_outer_array, $need_add);
        file_put_contents(API_PATH . '/last_outer.dat', serialize($all_upld_ids));
    }

    /**
     * 根据商品信息获取其xml
     *
     * @param string   $outer_id   增量索引中的索引
     * @param array    $value
     * @param array    $brand_info
     * @param boolean  $is_delete  是否是下线商品
     * @return string  商品xml
     */
    private function getproxml($outer_id, $value, $brand_info, $is_delete = FALSE) {
    $pxml='';
    $pxml.='<item>
                    <seller_id>宝贝购</seller_id>
                    <outer_id>' . $outer_id . '</outer_id>
                    <title><![CDATA[' . @$brand_info[$value['brand_id']] . @$value['product_name'] . ']]></title>';
    if ($is_delete) {
        $pxml .= '<available>0</available>';
    }
    if ($value['promote_start_date'] < $this->gmtime() && $value['promote_end_date'] > $this->gmtime() && $value['shop_price'] > 0
            && $value['shop_price'] < $value['market_price']) {
        @$value['drate'] = @price_format(@$value['shop_price'] / @$value['market_price']);
        $pxml.='
                        <type>discount</type>
                        <price>' . @$value['market_price'] . '</price>
                        <pa_ids>' . @$value['rush_id'] . '</pa_ids>
                        <promotion_type>2</promotion_type>';

        @$pxml.='<discount>
                         <start>' . @local_date('Y-m-d-H:i', @$value['promote_start_date']) . '</start>
                         <end>' . @local_date('Y-m-d-H:i', @$value['promote_end_date']) . '</end>
                         <dprice>' . @$value['shop_price'] . '</dprice>
                         <drate>' . @$value['drate'] . '</drate> 
                         <ddesc><![CDATA[' . @$value['goods_desc'] . ']]></ddesc>
                         </discount>';
    } else {
        @$pxml.='<type>fixed</type>';
        @$pxml.='<price>' . @$value['promote_price'] . '</price>';
    }
    $goods_desc = mb_strlen($value['product_desc']) > 1000 ? mb_strcut($value['product_desc'], 0, 1000, 'utf-8') : $value['product_desc'];
    @$pxml.='<desc><![CDATA[' . @$goods_desc . ']]></desc>
                    <brand><![CDATA[' . @$brand_info[$value['brand_id']] . ']]></brand>
                    <tags><![CDATA[' . @$brand_info[$value['brand_id']] . ']]></tags>
                    <image>' . IMG_SERVER_URL . @$value['url_222_296'] . '</image>
                    <scids>' . @$value['cat_id'] . '</scids>
                    <post_fee>10.00</post_fee>
                    <showcase>true</showcase>
                    <href>' . FRONT_SITE_URL . 'goods-' . @$value['product_id'] . '.html</href>
                    </item>';
    return $pxml;
    }

    public function fullindex() {
        $this->load->model('etao_model');
        //商家促销活劢信息列表
        $promotion = $this->etao_model->get_rush_list();
        $prom_act = ''; //商家促销活劢文件地址
        if (count($promotion) > 0) {
            $prom_act = '<promotion>' . API_URL . '/etao/PromotionActivities.xml</promotion>';
        }

        $pmxml = "";
        $pmxml = '<?xml version="1.0" encoding="utf-8" ?>';
        $pmxml.='<root>';
        $pmxml.='<version>1.0</version>';
        $pmxml.='<modified>' . $this->local_date('Y-m-d H:i:s') . '</modified>';
        $pmxml.='<seller_id>宝贝购</seller_id>';
        $pmxml.='<promotion_activities>';
        foreach ($promotion as $value) {
            $pmxml.='<activity>';
            $pmxml.='<pa_id>' . $value['rush_id'] . '</pa_id>';
            $rush_name = htmlspecialchars(str_replace(' ', '_', $value['rush_index']));
//            $rush_name = 111;
            $pmxml.='<activities_title>' . $rush_name . '</activities_title>';
            $pmxml.='<promotion_type>2</promotion_type>';
            //<!--促销类型，1=直降,2=限时贩，3=满就减，4=买就赠，5=多买多折，6=满就赠，7=买就减,参加多种活劢活劢之间用，隔开如：1,6--> 
            //$pmxml.='<activities_start>' . $this->local_date("y-m-d h:m:i", $value['start_date']) . '</activities_start>';
            $pmxml.='<activities_start>' . $value['start_date'] . '</activities_start>';
            //<!--活劢开始时间, 格式：2011-01-17 12:00:05，二十四小时制，精确到秒--> 
            //$pmxml.='<activities_end>' . $this->local_date("y-m-d h:m:i", $value['end_date']) . '</activities_end>';
            $pmxml.='<activities_end>' . $value['end_date'] . '</activities_end>';
            //<!--活劢结束时间, 格式：2011-01-17 12:00:09，二十四小时制，精确到秒--> 
            $jump_url = empty($value['jump_url']) ? FRONT_SITE_URL . 'rush-' . $value['rush_id'] . '.html' : $value['jump_url'];
            $pmxml.='<activities_url>' . $jump_url . '</activities_url>'; //<!--促销活劢链接-->
            $pmxml.='<activities_image>' . IMG_SERVER_URL . $value['image_ing_url'] . '</activities_image>';
            $pmxml.='</activity>';
        }
        $pmxml.='</promotion_activities>';
        $pmxml.='</root>';
//header('Content-type: application/xml; charset=utf-8');
        file_put_contents(API_PATH . '/PromotionActivities.xml', $pmxml);
//======执行更新，获取促销活劢=======end==============================
//======3.执行更新,生成商品目录=======begin==============================
        $cat_detail = $this->etao_model->getcat();
        $catxml = '<?xml version="1.0" encoding="utf-8"?>';
        $catxml.='<root>';
        $catxml.='<version>1.0</version>';
        $catxml.='<modified>' . $this->local_date('Y-m-d H:i:s') . '</modified>';
        $catxml.='<seller_id>宝贝购</seller_id>';
        $catxml.='<seller_cats>';
        foreach ($cat_detail as $cat) {
            $catxml.='<cat>';
            $catxml.='<scid>' . $cat['category_id'] . '</scid>';
            $catxml.='<name><![CDATA[' . $cat['category_name'] . ']]></name>';
            $catxml.='<cats>';
            $catsec = $this->etao_model->getcat($cat['category_id']);
            foreach ($catsec as $catsecvalue) {
                $catxml.='<cat>';
                $catxml.='<scid>' . $catsecvalue['category_id'] . '</scid>';
                $catxml.='<name><![CDATA[' . $catsecvalue['category_name'] . ']]></name>';
                $catxml.='</cat>';
            }
            $catxml.='</cats>	';
            $catxml.='</cat>';
            unset($catsec);
            unset($catsecvalue);
        }
        $catxml.='</seller_cats>';
        $catxml.='</root>';
//header('Content-type: application/xml; charset=utf-8');
        file_put_contents(API_PATH . '/SellerCats.xml', $catxml);
//======执行更新,生成商品目录=======end==============================
//======4.执行更新,生成商品包xml=======begin==============================
//SELECT DISTINCT(g.goods_id),goods_name,promote_start_date,promote_end_date,promote_price,shop_price,gg.middle_url,goods_desc,brand_id,goods_img,c.cat_id,c.cat_name FROM fc_goods AS g LEFT JOIN fc_flc_goods_labor AS gl ON g.goods_id = gl.goods_id LEFT JOIN fc_category as c ON g.cat_id = c.cat_id LEFT JOIN fc_goods_gallery AS gg ON g.goods_id = gg.goods_id WHERE gg.middle_url <> '' AND gl.on_sale = '1' AND g.is_delete = '0' AND g.shop_price > '0' AND gl.gl_num > 0 AND gl.on_sale > '0' GROUP BY g.goods_id DESC 
        $product_detail = $this_outer = $this->etao_model->get_add_pro();
        $brand_info = $this->etao_model->getbrand();
        $pro = array_chunk($product_detail, 1000);
//======2.执行更新,更新商品数据=======begin==============================
        $xml = '<?xml version="1.0" encoding="utf-8" ?>';
        $xml.='<root>';
        $xml.='<version>1.0</version>';
        $xml.='<modified>' . $this->local_date('Y-m-d H:i:s') . '</modified>';
        $xml.='<seller_id>宝贝购</seller_id>';
        $xml.='<cat_url>' . API_URL . '/etao/SellerCats.xml</cat_url>';
        $xml.=$prom_act; //商家促销活劢文件地址
        $xml.='<dir>' . API_URL . '/etao/product/</dir>';
        $xml.='<item_ids>';
        for ($i = 1, $j = 0; $i <= count($pro); $i++, $j++) {
            if (count($pro[$j]) > 0) {
                $xml.='<outer_id action="upload">' . $i . '</outer_id>';
                //======4执行更新,生成商品包xml=======begin==============================
                @$pxml = '<?xml version="1.0" encoding="utf-8"?>';
                @$pxml.='<items>';
                foreach (@$pro[$j] as $value) {
                    $product[] = $value['product_id'];
                    @$pxml.='<item>
                    <seller_id>宝贝购</seller_id>
                    <outer_id>' . @$i . '</outer_id>
                    <title><![CDATA[' .@$brand_info[$value['brand_id']] ."---". @$value['product_name'] . '     ]]></title>';

                    if ($value['promote_start_date'] < $this->gmtime() && $value['promote_end_date'] > $this->gmtime() && $value['shop_price'] > 0 && $value['shop_price'] < $value['market_price']) {
                        @$value['drate'] = @price_format(@$value['shop_price'] / @$value['market_price']);
                        $pxml.='
                        <type>discount</type>
                        <price>' . @$value['market_price'] . '</price>
                        <pa_ids>' . @$value['rush_id'] . '</pa_ids>
                        <promotion_type>2</promotion_type>';
                        @$pxml.='<discount>
                         <start>' . @$this->local_date('Y-m-d-H:i', @$value['promote_start_date']) . '</start>
                         <end>' . @$this->local_date('Y-m-d-H:i', @$value['promote_end_date']) . '</end>
                         <dprice>' . @$value['shop_price'] . '</dprice>
                         <drate>' . @$value['drate'] . '</drate> 
                         <ddesc><![CDATA[' . @$value['product_desc'] . ']]></ddesc>
                         </discount>';
                    } else {
                        @$pxml.='<type>fixed</type>';
                        @$pxml.='<price>' . @$value['promote_price'] . '</price>';
                    }
                    $goods_desc = mb_strlen($value['product_desc']) > 1000 ? mb_strcut($value['product_desc'], 0, 1000, 'utf-8') : $value['product_desc'];
                    @$pxml.='<desc><![CDATA[' . @$goods_desc . ']]></desc>
                    <brand><![CDATA[' . @$brand_info[$value['brand_id']] . ']]></brand>
                    <tags><![CDATA[' . @$brand_info[$value['brand_id']] . ']]></tags>
                    <image>' . IMG_SERVER_URL . @$value['url_222_296'] . '</image>
                    <scids>' . @$value['category_id'] . '</scids>
                    <post_fee>10.00</post_fee>
                    <showcase>true</showcase>
                    <href>' . FRONT_SITE_URL . 'goods-' . @$value['goods_id'] . '.html</href>
                    </item>';
                }
                $pxml.='</items>';

                file_put_contents(API_PATH . '/product/' . $i . '.xml', $pxml);
                unset($pxml);
                //======4执行更新,生成商品包xml=======end==============================          
            }
        }
        $xml.='</item_ids>';
        $xml.='</root>';
//执行：保存历史商品dat:将本次所有的商品写入last_outer.dat中保存
        file_put_contents(API_PATH . '/last_outer.dat', serialize($product));
//header('Content-type: application/xml; charset=utf-8');
        file_put_contents(API_PATH . '/FullIndex.xml', $xml);
//======2执行更新,更新商品数据=======end==============================
    }

    /**
     * 将GMT时间戳格式化为用户自定义时区日期
     * @param  string       $format
     * @param  integer      $time       该参数必须是一个GMT的时间戳
     * @return  string
     */
    private function local_date($format, $time = NULL) {
        $timezone = '8';
        if ($time === NULL) {
            $time = $this->gmtime();
        } elseif ($time <= 0) {
            return '';
        }
        $time += ($timezone * 3600);
        return date($format, $time);
    }

    /**
     * 获得当前格林威治时间的时间戳
     *
     * @return  integer
     */
    private function gmtime() {
        return (time() - date('Z'));
    }

}

?>
