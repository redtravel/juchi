<?php

/**
 * 
 */
class Cart extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->time = date('Y-m-d H:i:s');
        $this->user_id = $this->session->userdata('user_id');
        $this->load->model('product_model');
        $this->load->model('cart_model');
        $this->load->helper('cart');
    }

    public function index() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control:no-cache,must-revalidate");
        header("Pragma:no-cache");
        if (!$this->user_id) {
            $this->session->set_userdata(array(
                'login_return_url' => 'cart/index',
                'login_msg' => '订单结算之前请先登录'
            ));
            goto_login('cart/index');
        }
        $this->load->model('user_model');
        $this->load->library('memcache');
        $cart_sn = get_cart_sn();
        $cart_list = $this->cart_model->cart_info($cart_sn, TRUE);
        if (!$cart_list) {
            $this->load->view('mobile/cart/blank_cart', array('title' => '购物车'));
            return;
        }
        
        $checkout = get_checkout();
	
        $cart_summary = summary_cart($cart_list, isset($checkout['payment']['voucher']['provider']) ? $checkout['payment']['voucher']['provider'] : NULL);
        list($product_list, $package_list) = split_package_product($cart_list);
        if ($this->user_id) {
            $user = $this->user_model->filter(array('user_id' => $this->user_id));
            $this->load->vars('user', $user);
        }
	
	$provider_id_arr = array_keys($cart_summary['product_list']);

	
        $this->load->vars(array(
            'title' => '购物车',
            'cart_goods_buy_num' => MAX_SALE_NUM,
            'package_list' => $package_list,
            'cart_summary' => $cart_summary,
	    'default_provider' => $provider_id_arr[0],
            'voucher_list' => $this->cart_model->available_voucher_list($this->user_id),
            'gifts_list' => memcache_get_gifts()//取赠品
        ));
	
        //$this->load->view('mobile/cart/index');
	$this->load->view('mobile/cart/index');
	//$this->load->view('mobile/cart/edit');
        //$this->load->view('cart/index');
    }

    public function add_to_cart($is_return=0) {
        $this->load->model('order_model');
        $this->load->helper('product');
        if (!$this->user_id) {
            sys_msg('您还没有登陆！', 2);
        }
        
        $cart_sn = get_cart_sn();
        $sub_id = intval($this->input->post('sub_id'));
        $num = intval($this->input->post('num'));
        $limit_sale = intval($this->input->post('limit_sale')); //是否限购
        $type = '';
        if ($this->input->post('type'))
            $type = intval($this->input->post('type'));
        if ($num < 1)
            sys_msg('请选择购买数量', 1);
        $cart_list = $this->cart_model->cart_info($cart_sn);
        $cart_summary = summary_cart($cart_list);
        if ($cart_summary['product_num'] + $num > CART_SIZE)
            sys_msg('购物车已满，您的购物车最多可以保存 ' . CART_SIZE . ' 件商品。', 1);

        $sub = $this->product_model->filter_sub(array('sub_id' => $sub_id));
        if (!$sub)
            sys_msg('请选择颜色尺码', 1);
        if (!$sub->is_on_sale)
            sys_msg('该色码当前不可售');
        format_sub($sub);
        

        // 取价格信息
        $p = $this->product_model->filter(array('product_id' => $sub->product_id));
        format_product($p);
        if (!$type)
            $type = $p->is_promote ? 1 : 0;
        $update = array(
            'user_id' => $this->user_id,
            'session_id' => $cart_sn,
            'sub_id' => $sub->sub_id,
            'product_id' => $sub->product_id,
            'color_id' => $sub->color_id,
            'size_id' => $sub->size_id,
            'shop_price' => $p->shop_price,
            'product_price' => $p->product_price,
            'product_num' => $num,
            'update_date' => $this->time,
            'discount_type' => $type, 
            'shop_id' => $p->shop_id
        );
        // 如果已有同样的商品，则不作操作，否则插入新值
        $old_cart = NULL;
        $product_num = $num; // 用于检查限购数量
        foreach ($cart_list as $c) {
            if($c->product_id==$update['product_id']){
                $product_num += $c->product_num;
            }
            if ($c->package_id || $c->discount_type != $update['discount_type'] || $c->product_id != $update['product_id'] || $c->color_id != $update['color_id'] || $c->size_id != $update['size_id'])
                continue;
            $old_cart = $c;
            break;
        }
        // fix:变态点击数量更新不及时的错误,如果有则不再添加了
        if ($old_cart) {
            print json_encode(array('err' => 0, 'msg' => ''));
            return;
        }
        // 检查库存数量
        if ($sub->sale_num != -2 && $sub->sale_num < ($old_cart ? $old_cart->product_num + $num : $num)) {
            sys_msg('对不起，商品库存不足。', 1);
        }
        
        // 检查限购数量
        if($p->limit_num && $p->limit_day){
            $bought_num = $this->order_model->get_bought_num($this->user_id, $p->product_id, $p->limit_day);
            if($bought_num + $product_num > $p->limit_num){
                sys_msg("对不起，此商品是限购商品{$p->limit_day}天内限购{$p->limit_num}件。", 1);
            }
        }

        //$limit_sale  将限购的商品写入session
        if($limit_sale == 1){
            $this->session->set_userdata('limit_user_product', 'limit_'.$this->user_id.'_'.$p->product_id);
        }

        $this->db->trans_begin(); //开始事务啦啦啦
        if ($old_cart) {
            $update['product_num'] += $old_cart->product_num;
            $this->cart_model->update($update, $old_cart->rec_id);
            $rec_id = $old_cart->rec_id;
        } else {
            $update['create_date'] = $this->time;
            $rec_id = $this->cart_model->insert($update);
        }
        // 删除现金券记录
        //remove_voucher_history();
        // 刷新购物车
        $this->cart_model->refresh_cart($update['session_id']);
        $this->db->trans_commit(); //结束事务啦啦啦
        $this->input->set_cookie('cart_num', $cart_summary['product_num'] + $num, CART_SAVE_SECOND);
        $result = array('err' => 0, 'msg' => '', 'rec_id' => $rec_id);
        if ($is_return){
            return $result;
        }
        print json_encode($result);
    }

    private function add_to_cart_2($sub_id, $num) {
            $this->load->model('order_model');
            $this->load->helper('product');


            $result = array('err' => 0, 'msg' => '');        
            $cart_sn = get_cart_sn();       
            
            $limit_sale = intval($this->input->post('limit_sale')); //是否限购
            $type = '';
            if ($this->input->post('type'))
                $type = intval($this->input->post('type'));
            if ($num < 1) {
                //sys_msg('请选择购买数量', 1);
                $result['msg'] = '请选择购买数量';
                $result['err'] = 1;
                return $result;
            }
                
            $cart_list = $this->cart_model->cart_info($cart_sn);
            $cart_summary = summary_cart($cart_list);
            if ($cart_summary['product_num'] + $num > CART_SIZE){        
                //sys_msg('购物车已满，您的购物车最多可以保存 ' . CART_SIZE . ' 件商品。', 1);
                $result['msg'] = '购物车已满，您的购物车最多可以保存 ' . CART_SIZE . ' 件商品。';
                $result['err'] = 1;
                return $result;
            }

            $sub = $this->product_model->filter_sub(array('sub_id' => $sub_id));
            if (!$sub) {        
                //sys_msg('请选择颜色尺码', 1);
                $result['msg'] = '请选择颜色尺码';
                $result['err'] = 1;
                return $result;

            }
            if (!$sub->is_on_sale) {        
                //sys_msg('该色码当前不可售');
                $result['msg'] = '该色码当前不可售';
                $result['err'] = 1;
                return $result;
            }
            format_sub($sub);
            

            // 取价格信息
            $p = $this->product_model->filter(array('product_id' => $sub->product_id));
            format_product($p);
            if (!$type)
                $type = $p->is_promote ? 1 : 0;
            $update = array(
                'user_id' => $this->user_id,
                'session_id' => $cart_sn,
                'sub_id' => $sub->sub_id,
                'product_id' => $sub->product_id,
                'color_id' => $sub->color_id,
                'size_id' => $sub->size_id,
                'shop_price' => $p->shop_price,
                'product_price' => $p->product_price,
                'product_num' => $num,
                'update_date' => $this->time,
                'discount_type' => $type, 
                'shop_id' => $p->shop_id
            );
            // 如果已有同样的商品，则不作操作，否则插入新值
            $old_cart = NULL;
            $product_num = $num; // 用于检查限购数量
            foreach ($cart_list as $c) {
                if($c->product_id==$update['product_id']){
                    $product_num += $c->product_num;
                }
                if ($c->package_id || $c->discount_type != $update['discount_type'] || $c->product_id != $update['product_id'] || $c->color_id != $update['color_id'] || $c->size_id != $update['size_id'])
                    continue;
                $old_cart = $c;
                break;
            }
            // fix:变态点击数量更新不及时的错误,如果有则不再添加了
            if ($old_cart) {
                return array('err' => 0, 'msg' => '');
            }
            // 检查库存数量
            if ($sub->sale_num != -2 && $sub->sale_num < ($old_cart ? $old_cart->product_num + $num : $num)) {
                //sys_msg('对不起，商品库存不足。', 1);
                return array('err' => 1, 'msg' => '对不起，商品库存不足。');
            }
            
            // 检查限购数量
            if($p->limit_num && $p->limit_day){
                $bought_num = $this->order_model->get_bought_num($this->user_id, $p->product_id, $p->limit_day);
                if($bought_num + $product_num > $p->limit_num){
                    //sys_msg("对不起，此商品是限购商品{$p->limit_day}天内限购{$p->limit_num}件。", 1);
                    return array('err' => 1, 'msg' => '对不起，此商品是限购商品{$p->limit_day}天内限购{$p->limit_num}件。');       
                }
            }

            //$limit_sale  将限购的商品写入session
            if($limit_sale == 1){
                $this->session->set_userdata('limit_user_product', 'limit_'.$this->user_id.'_'.$p->product_id);
            }

            $this->db->trans_begin(); //开始事务啦啦啦
            if ($old_cart) {
                $update['product_num'] += $old_cart->product_num;
                $this->cart_model->update($update, $old_cart->rec_id);
                $rec_id = $old_cart->rec_id;
            } else {
                $update['create_date'] = $this->time;
                $rec_id = $this->cart_model->insert($update);
            }
            // 删除现金券记录
            //remove_voucher_history();
            // 刷新购物车
            $this->cart_model->refresh_cart($update['session_id']);
            $this->db->trans_commit(); //结束事务啦啦啦
            $this->input->set_cookie('cart_num', $cart_summary['product_num'] + $num, CART_SAVE_SECOND);
            $result = array('err' => 0, 'msg' => '', 'rec_id' => $rec_id);
            return $result; 
    }

    public function add_mutlit_subs_to_cart() {
        if (!$this->user_id) {
            sys_msg('您还没有登陆！', 2);
        }

        $package_id = intval($this->input->post('package_id'));
        $result = array('err' => 0, 'msg'=> '');

        foreach (explode('|', trim($this->input->post('sub_ids'))) as $sub_id) {
            $tmp = explode('=', $sub_id);
            if (count($tmp) >= 2) {
                $sub_id = $tmp[0];
                $num = $tmp[1];
                $result[$sub_id] = $this->add_to_cart_2($sub_id, $num);
            }
        }
        print json_encode($result);
    }


    public function add_package_to_cart() {
        $this->load->model('package_model');
        $cart_sn = get_cart_sn();
        $package_id = intval($this->input->post('package_id'));
        $sub_ids = array();
        foreach (explode(trim($this->input->post('sub_ids')), '|') as $sub_id) {
            $sub_id = intval($sub_id);
            if ($sub_id > 0)
                $sub_ids[] = intval($sub_id);
        }
        if (!$package_id || !$sub_ids)
            sys_msg('请选择商品！', 1);
        $cart_list = $this->cart_model->cart_info();
        $cart_summary = summary_cart($cart_list);
        if ($cart_summary['product_num'] + $count($sub_ids) > CART_SIZE)
            sys_msg('购物车已满，您的购物车最多可以保存 ' . CART_SIZE . ' 件商品。', 1);

        $pkg = $this->package_model->filter(array('package_id' => $package_id));
        if (!$pkg || $pkg->package_status != 1 || $pkg->start_time > $this->time || $pkg->end_time < $this->time)
            sys_msg('该礼包当前不可售', 1);

        // 取出sub并减去购物车中的商品数量
        $cart_list = get_pair($this->cart_model->sub_num_in_cart($sub_ids), 'sub_id', 'product_num');
        $sub_list = array();
        $pids = array();
        foreach ($this->product_model->all_sub(array('sub_id' => $sub_ids)) as $sub) {
            format_sub($sub);
            if (isset($cart_list[$sub_id]) && $sub->sale_num != -2)
                $sub->sale_num = max($sub->sale_num - $cart_list[$sub_id], 0);
            $sub_list[] = $sub;
            $pids[] = $sub->product_id;
        }
        // 取礼包商品
        $pkg_ps = index_array($this->package_model->all_product(array('package_id' => $package_id, 'product_id' => $pids)), 'product_id');

        // 验证商品库存,并生成要插入的数据
        $p_list = array();
        foreach ($sub_ids as $sub_id) {
            if (!isset($sub_list[$sub_id]))
                sys_msg('商品没有库存', 1);
            $sub = $sub_list[$sub_id];
            if (!isset($pkg_ps[$sub->product_id]))
                sys_msg('商品不在礼包中');
            $pp = $pkg_ps[$sub->product_id];
            $p_list[] = array(
                'user_id' => $this->user_id,
                'session_id' => $cart_sn,
                'sub_id' => $sub->sub_id,
                'product_id' => $sub->product_id,
                'color_id' => $sub->color_id,
                'size_id' => $sub->size_id,
                'shop_price' => $pp->shop_price,
                'product_num' => 1,
                'package_id' => $package_id,
                'create_date' => $this->time,
                'update_date' => $this->time,
                'area_id' => $pp->area_id,
                'discount_type' => 2
            );
            if ($sub->sale_num != -2)
                $sub->sale_num -= 1;
            if ($sub->sal_num < 0) {
                $sub_info = $this->product_model->sub_info($sub->sub_id);
                sys_msg("{$sub_info->product_name} {$sub_info->color_name} {$sub_info->size_name} 库存不足", 1);
            }
        }

        // 判断礼包的规则
        $pkg_area = index_array($this->package_model->all_area(array('package_id' => $package_id, 'area_type' => 1)), 'area_id');
        foreach ($pkg_area as $key => $area)
            $pkg_area[$key]->product_num = 0;
        $total_num = 0;
        $shop_amount = 0;
        foreach ($p_list as $p) {
            $pkg_area[$p->area_id]->product_num += 1;
            $total_num += 1;
            $shop_amount += $p['shop_price'];
        }
        if ($shop_amount <= 0)
            sys_msg('礼包金额错误', 1);
        foreach ($pkg_area as $area) {
            if ($area->min_number > $area->product_num)
                sys_msg('商品数量不符合礼包要求', 1);
        }

        // 计算价格
        $price_config = array($pkg->package_goods_number => $pkg->package_amount);
        foreach (unpack_package_config($pkg->package_other_config) as $config) {
            $price_config[$config[0]] = $config[1];
        }

        if (!isset($price_config[$total_num]))
            sys_msg('商品数量不符合礼包要求', 1);
        $pkg_amount = round($price_config[$total_num], 2);
        $assigned_amount = 0;
        foreach ($p_list as $key => $p) {
            $p['product_price'] = round($pkg_amount / $shop_amount * $p['shop_price'], 2);
            $assigned_amount += $p['product_price'];
            unset($p['area_id']);
            $p_list[$key] = $p;
        }
        $p_list[0]['product_price'] = round($p_list[0]['product_price'] + $pkg_amount - $assigned_amount, 2);
        // 插入订单商品
        $this->db->trans_begin(); //事务开始啦啦啦
        $extension_id = 0;
        foreach ($p_list as $p) {
            $p['extension_id'] = $extension_id;
            $rec_id = $this->cart_model->insert($p);
            if (!$extension_id) {
                $extension_id = $rec_id;
                $this->cart_model->update(array('extension_id' => $extension_id), $rec_id);
            }
        }
        // 删除现金券记录
        remove_voucher_history();
        // 刷新购物车
        $this->cart_model->refresh_cart($update['session_id']);
        $this->db->trans_commit(); //事务结束啦啦啦
        $this->input->set_cookie('cart_num', $cart_summary['product_num'] + $count($sub_ids), CART_SAVE_SECOND);
        print json_encode(array('err' => 0, 'msg' => ''));
    }

    public function update_cart($is_return=0) {
        $this->load->model('order_model');
        $this->load->helper('product');
        $cart_sn = get_cart_sn();
        $rec_id = intval($this->input->post('rec_id'));
        $num = intval($this->input->post('num'));
        $links = array('rec_id' => $rec_id);
        if (!$rec_id || $num < 1)
            sys_msg('参数错误', 1, $links);

        $cart_list = index_array($this->cart_model->cart_info($cart_sn), 'rec_id');
        if (!$cart_list || !isset($cart_list[$rec_id]))
            sys_msg('由于操作时间过长，您的购物车数据已丢失。', 2, $links);
        $rec = $cart_list[$rec_id];
        if ($rec->package_id && $num != 1)
            sys_msg('礼包商品不允许更改数量', 1, $links);
        if ($rec->discount_type == 4)
            sys_msg('赠品不能更改', 1, $links);
        
        // 检查购物车尺寸
        $old_num = $cart_list[$rec_id]->product_num;
        $cart_list[$rec_id]->product_num = $num;
        $cart_summary = summary_cart($cart_list);
        if ($cart_summary['product_num'] > CART_SIZE)
            sys_msg('购物车已满，您的购物车最多可以保存 ' . CART_SIZE . ' 件商品。', 1, $links);
        
        // 检查限购
        if($cart_list[$rec_id]->limit_num && $cart_list[$rec_id]->limit_day){
            $current_num = 0;
            foreach($cart_list as $val){
                if($val->product_id == $cart_list[$rec_id]->product_id){
                    $current_num += $val->product_num;
                }
            }
            $bought_num = $this->order_model->get_bought_num($this->user_id, $cart_list[$rec_id]->product_id, $cart_list[$rec_id]->limit_day);
            if($bought_num + $current_num > $cart_list[$rec_id]->limit_num){
                sys_msg("对不起，此商品是限购商品{$cart_list[$rec_id]->limit_day}天内限购{$cart_list[$rec_id]->limit_num}件。", 1, $links);
            }
        }

        // 检查库存
        $sub = $this->product_model->filter_sub(array('sub_id' => $rec->sub_id));
        if (!$sub || !$sub->is_on_sale)
            sys_msg('该色码当前不可售', $links);
        format_sub($sub);
        // 检查库存数量
        if ($sub->sale_num != -2 && $sub->sale_num < $num)
            sys_msg('对不起，该商品库存不足,请调整购买的数量。', 1, $links);

        $this->db->trans_begin();
        // 更新数据库
        $this->cart_model->update(array('product_num' => $num), $rec_id);
        // 删除现金券记录
        // remove_voucher_history();
        // 刷新购物车
        $this->cart_model->refresh_cart($cart_sn);
        $cart_summary['left_time'] = CART_SAVE_SECOND;
        // todo:处理赠品数量
        $this->db->trans_commit();
        $this->input->set_cookie('cart_num', $cart_summary['product_num'], CART_SAVE_SECOND);
        $result = array('err' => 0, 'msg' => '', 'cart' => $cart_list[$rec_id], 'cart_summary' => $cart_summary);
        if ($is_return){
            return $result;
        }
        print json_encode($result);
    }

    public function remove_from_cart($is_return=0) {
        $cart_sn = get_cart_sn();
        $rec_id = intval($this->input->post('rec_id'));
        $rec = $this->cart_model->filter(array('rec_id' => $rec_id, 'session_id' => $cart_sn));
        if (!$rec){
            print json_encode(array('err' => 0, 'msg' => ''));
            return;
        }            
        if ($rec->discount_type == 4)
            sys_msg('赠品不能删除', 1);
        if ($rec->package_id) {
            $this->cart_model->delete_where(array('session_id' => $cart_sn, 'extension_id' => $rec->extension_id));
        } else {
            $this->cart_model->delete($rec->rec_id);
        }
        $this->cart_model->refresh_cart($cart_sn);
        $cart_list = $this->cart_model->cart_info($cart_sn, TRUE);
        $checkout = get_checkout();
        $cart_summary = summary_cart($cart_list, isset($checkout['payment']['voucher']['provider']) ? $checkout['payment']['voucher']['provider'] : NULL);
        $this->input->set_cookie('cart_num', $cart_summary['product_num'], CART_SAVE_SECOND);
        $result = array('err' => 0, 'total_price' => $cart_summary['product_price'], 'total_num' => $cart_summary['product_num']);
        if ($is_return){
            return $result;
        }
        print json_encode($result);
    }

    public function checkout($shop_id=0) {
        global $alipay_bank_list;
        $cart_sn = get_cart_sn();
        $default_address = array();
        $this->load->model('user_model');
        $this->load->model('region_model');
        $this->load->library('lib_iplocation');
        $this->config->load('provider');
        /* 购物车信息 */
        $cart_list = $this->cart_model->cart_info($cart_sn, TRUE, false, $shop_id);
        if (!$cart_list)
            redirect('cart');

        if (!$this->user_id) {
            $this->session->set_userdata(array(
                'login_return_url' => 'cart/checkout',
                'login_msg' => '订单结算之前请先登录'
            ));
            goto_login('cart/checkout');
        }

        $user = $this->user_model->filter(array('user_id' => $this->user_id));

        $checkout = get_checkout();
        
        //收货地址列表
        $address_list = index_array($this->user_model->address_list($this->user_id), 'address_id');
        //初始化配送和支付信息
        if ($address_list) {
            $arr = index_array($address_list, 'is_used');
            $default_address = isset($arr[1]) ? $arr[1] : end($address_list);
            //$checkout['shipping']['address_id'] = $default_address->address_id;
            //$checkout['shipping']['district'] = $default_address->district;
            //$checkout['shipping']['city'] = $default_address->city;
            //$checkout['shipping']['province'] = $default_address->province;
        }
        /*if (empty($checkout['shipping']) && empty($address_list)) {
            $checkout['shipping'] = array('address_id' => 0, 'country' => 1, 'province' => 0, 'city' => 0, 'district' => 0);
        }*/
        
        if (!$this->session->userdata('checkout'))
            $this->session->set_userdata('checkout', $checkout);
        // 初始化信息结束
        // 取地区信息
        /*if (empty($checkout['shipping']['address_id'])) {
            list($province_list, $city_list, $district_list) = $this->cart_model->cart_region($checkout['shipping']);
            $this->load->vars(array('province_list' => $province_list, 'city_list' => $city_list, 'district_list' => $district_list));
        }*/
        voucher_gc($cart_list, 'provider');
        $cart_summary = summary_cart(
                $cart_list, isset($checkout['payment']['voucher']['provider']) ? $checkout['payment']['voucher']['provider'] : NULL, $user->user_money
        );


        //团购统计数量增加
        $this->load->model('tuangou_model');
        foreach ($cart_list as $val) {
            if ($val->discount_type == 5) {
                $this->tuangou_model->update_tuan_buy_num($val->product_num, $val->product_id);
            }
        }
        //快递
        $shipping_list = index_array($this->cart_model->get_shipping_list(), 'shipping_id');
        
        if (!empty($checkout['shipping']['shipping_id']) && !isset($shipping_list[$checkout['shipping']['shipping_id']])){
            $checkout['shipping']['shipping_id'] = 0;
        }

        if (empty($checkout['shipping']['shipping_id'])){
            $checkout['shipping']['shipping_id'] = key($shipping_list);;           
        }
        $shipping_fee = 0;
        if (!empty($checkout['shipping']) && !empty($default_address)){
            $shipping_fee = $this->get_shipping_fee2($checkout['shipping']['shipping_id'], $default_address->address_id, $shop_id, 1);
        }
        //发票
        $invoice_list = $this->cart_model->get_user_invoice_list($this->user_id);
        $pay_list = index_array($this->cart_model->available_pay_list(), 'pay_id');
        $this->load->helper('cart');
        format_pay_list($pay_list);
        
        //券
        //$region_shipping_fee = $this->config->item('provider_shipping_config');

        //免邮
        $v_product_id_data = array();
        foreach ($cart_summary['product_list'] as $provider_id => $provider){
            foreach ($provider['product_list'] as $product_v){
                $v_product_id_data[] = array($product_v->product_id, $product_v->product_num * $product_v->product_price); //商品数组(id,price)
            }
        }

        $v_campaign_package = campaign_package_product_v($v_product_id_data);
        if(!empty($v_campaign_package)) $shipping_fee = 0;

        /**
         * v 2016.03.30 现金券抢购购买 
         */
        if(defined('CAT_CHECKOUT_VOUCHER')){
            $v_voucher = $this->session->userdata('v_voucher') ? $this->session->userdata('v_voucher') : 1;
            $cart_summary['voucher'] = $cart_summary['voucher'] * $v_voucher['product_num'];
        }

        $this->load->vars(array(
            'title' => '结算',
            'user' => $user,
            'cart_list' => $cart_list,
            'cart_summary' => $cart_summary,
            'shipping' => $checkout['shipping'],
            'payment' => $checkout['payment'],
            'pay_list' => $pay_list,
            'best_times' => $this->config->item('best_times'),
            'voucher_list' => $this->cart_model->available_voucher_list($this->user_id),
            'address_list' => $address_list,
            'default_address' => $default_address,
            'alipay_token' => $this->session->userdata('alipay_token'),
            'shipping_list' => $shipping_list,
            'invoice_list' => $invoice_list,
            'shipping_fee' => $shipping_fee, 
            'shop_id' => $shop_id
            //'alipay_bank_list' => $alipay_bank_list,
            //'region_shipping_fee' => $region_shipping_fee           
        ));
        
        $this->load->view('mobile/cart/checkout');
    }
    
    public function checkout_product($sub_id, $num) {
        global $alipay_bank_list;
        $cart_sn = get_cart_sn();
        $default_address = array();
        $this->load->model('user_model');
        $this->load->model('region_model');
        $this->load->library('lib_iplocation');
        $this->config->load('provider');
        /* 购物车信息 */
        //$cart_list = $this->cart_model->cart_info($cart_sn, TRUE, false, $shop_id);
        $sub_info = $this->product_model->sub_info($sub_id);
        if(!$sub_info || !$sub_info->is_audit || !$sub_info->is_on_sale) redirect('index');
        format_product($sub_info);
	format_sub($sub_info);
        $sub_info->sale_num = $sub_info->sale_num == -2 ? MAX_SALE_NUM : min(MAX_SALE_NUM, $sub_info->sale_num);

        if ($sub_info->sale_num <= 0 || $sub_info->sale_num < $num){
            redirect('index');
        }
        $sub_info->buy_num = $num;
        
        $g = $this->product_model->all_gallery(array('product_id' => $sub_info->product_id));
        $sub_info->img_url = $g[0]->img_url;
        
        

        if (!$this->user_id) {
            $this->session->set_userdata(array(
                'login_return_url' => 'product/info/'.$sub_info->product_id,
                'login_msg' => '订单结算之前请先登录'
            ));
            goto_login('product/info/'.$sub_info->product_id);
        }

        $user = $this->user_model->filter(array('user_id' => $this->user_id));
        voucher_gc($sub_info, 'product');
        $checkout = get_checkout();
        
        //收货地址列表
        $address_list = index_array($this->user_model->address_list($this->user_id), 'address_id');
        //初始化配送和支付信息
        if ($address_list) {
            $arr = index_array($address_list, 'is_used');
            $default_address = isset($arr[1]) ? $arr[1] : end($address_list);
            //$checkout['shipping']['address_id'] = $default_address->address_id;
            //$checkout['shipping']['district'] = $default_address->district;
            //$checkout['shipping']['city'] = $default_address->city;
            //$checkout['shipping']['province'] = $default_address->province;
        }
        /*if (empty($checkout['shipping']) && empty($address_list)) {
            $checkout['shipping'] = array('address_id' => 0, 'country' => 1, 'province' => 0, 'city' => 0, 'district' => 0);
        }*/
        
        //if (!$this->session->userdata('checkout'))
        //    $this->session->set_userdata('checkout', $checkout);
        // 初始化信息结束
        // 取地区信息
        /*if (empty($checkout['shipping']['address_id'])) {
            list($province_list, $city_list, $district_list) = $this->cart_model->cart_region($checkout['shipping']);
            $this->load->vars(array('province_list' => $province_list, 'city_list' => $city_list, 'district_list' => $district_list));
        }
        $cart_summary = summary_cart(
                $cart_list, $checkout['payment']['voucher'], $user->user_money
        );*/
        

        //团购统计数量增加
        /*$this->load->model('tuangou_model');
        foreach ($cart_list as $val) {
            if ($val->discount_type == 5) {
                $this->tuangou_model->update_tuan_buy_num($val->product_num, $val->product_id);
            }
        }*/
        //快递
        $shipping_list = index_array($this->cart_model->get_shipping_list(), 'shipping_id');
        
        /*if (!empty($checkout['shipping']['shipping_id']) && !isset($shipping_list[$checkout['shipping']['shipping_id']])){
            $checkout['shipping']['shipping_id'] = 0;
        }*/
        $shipping_id = 0;
        if (!empty($shipping_list)){
            $shipping_id = key($shipping_list);;           
        }
        $shipping_fee = 0;
        if (!empty($shipping_id) && !empty($default_address)){
            $shipping_fee = $this->get_shipping_fee3($shipping_id, $default_address->address_id, $sub_info->product_id,$num, 1);
        }
        //发票
        //$invoice_list = $this->cart_model->get_user_invoice_list($this->user_id);
        $pay_list = index_array($this->cart_model->available_pay_list(), 'pay_id');
        $this->load->helper('cart');
        format_pay_list($pay_list);
        $voucher_amount = 0;
        if (isset($checkout['payment']['voucher']['product'][$sub_info->product_id])) $voucher_amount = $checkout['payment']['voucher']['product'][$sub_info->product_id]->payment_amount;
        
        $balance = min($user->user_money, $sub_info->product_price*$sub_info->buy_num + $shipping_fee - $voucher_amount);

        /**
         * v 2016.03.30 现金券抢购购买 
         */
        if(defined('CAT_CHECKOUT_VOUCHER')){
            $v_voucher = $this->session->userdata('v_voucher_product') ? $this->session->userdata('v_voucher_product') : 1;
            $voucher_amount = $voucher_amount * $v_voucher['product_num'];
        }
        /**
         * v 2016.04.18 指定商品 购买立减活动
         */
        global $product_minus_activity;
        if(!empty($product_minus_activity) && strtotime($product_minus_activity['end_time']) >= time()){
            if( $sub_info->product_id == $product_minus_activity['product_id']){
                $sub_info->product_price -= $product_minus_activity['minus_price']; 
            }
        }
        
        $this->load->vars(array(
            'title' => '结算',
            'user' => $user,
            'sub_info' => $sub_info,
            //'cart_summary' => $cart_summary,
            'shipping_id' => $shipping_id,
            'payment' => $checkout['payment'],
            'pay_list' => $pay_list,
            //'best_times' => $this->config->item('best_times'),
            'voucher_list' => $this->cart_model->available_voucher_list($this->user_id),
            'address_list' => $address_list,
            'default_address' => $default_address,
            //'alipay_token' => $this->session->userdata('alipay_token'),
            'shipping_list' => $shipping_list,
            //'invoice_list' => $invoice_list,
            'shipping_fee' => $shipping_fee, 
            'balance' => $balance, 
            'voucher_amount' => $voucher_amount
            //'shop_id' => $shop_id
            //'alipay_bank_list' => $alipay_bank_list,
            //'region_shipping_fee' => $region_shipping_fee           
        ));
        
        $this->load->view('mobile/cart/checkout_product');
    }

    public function proc_checkout($shop_id=0) {
        //global $alipay_bank_list;
        $cart_sn = get_cart_sn();
        $this->load->model('user_model');
        $this->load->model('region_model');
        $this->load->model('order_model');
        $this->load->library('lib_iplocation');
        $this->load->helper('product');
        $this->load->config('provider');
        
        //$pay_method = trim($this->input->post('pay_method'));
        //$bank_code = trim($this->input->post('bank_code'));
        $address_id = intval($this->input->post('address_id'));
        $use_balance = intval($this->input->post('use_balance'));
        $pay_id = intval($this->input->post('pay_id'));
        $shipping_id = intval($this->input->post('shipping_id'));
        $invoice_title = trim($this->input->post('invoice'));
        $remark = trim($this->input->post('remark'));
        $balance_payment_amount = 0; // 余额支付金额
        
        if (!$this->user_id) {
            print json_encode(array('err' => 1, 'msg' => '提交购物车前请先登录', 'url' => '/user/login'));
            return false;
        }

        /* 购物车信息 */
        $cart_list = $this->cart_model->cart_info($cart_sn, false, false, $shop_id);
        if (!$cart_list) {
            print json_encode(array('err' => 1, 'msg' => '您的购物车内没有商品', 'url' => '/cart'));
            return false;
        }
        // 收集结算信息
        voucher_gc($cart_list, 'provider');
        $checkout = get_checkout();

        $cart_summary = summary_cart($cart_list, isset($checkout['payment']['voucher']['provider']) ? $checkout['payment']['voucher']['provider'] : NULL);
        
        $address = $this->user_model->filter_address(array('address_id' => $address_id));
        if (!$address) sys_msg('请选择收货地址', 1);
        $region_shipping['province'] = $address->province;

        $checkout['shipping']['shipping_id'] = $shipping_id;
        $shipping = $this->cart_model->filter_shipping(array('shipping_id' => $checkout['shipping']['shipping_id']));
        if (!$shipping) sys_msg('请选择快递公司', 1);
        //$checkout['payment']['balance'] = round($this->input->post('balance'), 2);
        //支付，bank_code
        $checkout['payment']['pay_id'] = $pay_id;
        //$checkout['payment']['pay_id'] = intval(mb_substr($pay, 0, strpos($pay, '_')));
        //$checkout['payment']['bank_code'] = mb_substr($pay, strpos($pay, '_') + 1);
        $checkout['payment']['invoice_title'] = $invoice_title;
        //$checkout['payment']['invoice_title'] = trim($this->input->post('invoice_title'));
        //$checkout['payment']['invoice_content'] = trim($this->input->post('invoice_content'));
        $this->session->set_userdata('checkout', $checkout);
        $region_shipping['pay_id'] = $checkout['payment']['pay_id'];
        
        // 验证收集的数据
        $this->db->trans_begin();
        $user = $this->user_model->lock_user($this->user_id);
        if (!$user) { // 如果后台将用户删除了，后果会这样
            $this->session->destroy_sess();
            print json_encode(array('err' => 1, 'msg' => '您需要重新登录', 'url' => 'user/login'));
            return false;
        }
        if($use_balance){
            $balance_payment_amount = $user->user_money;
        }
        
        // 验证现金券
        $arr_voucher = array();
        $arr_voucher_sn = array();
        foreach($cart_summary['product_list'] as $provider_id=>$provider)
        {
            if($provider['voucher']){
                $arr_voucher_sn[] = $provider['voucher']->voucher_sn;
            }
        }
        if($arr_voucher_sn){
            $arr_voucher = index_array($this->cart_model->lock_voucher($arr_voucher_sn), 'voucher_sn');
        }
        foreach($cart_summary['product_list'] as $provider_id=>$provider){
            if(empty($provider['voucher'])) continue;
            $voucher = $provider['voucher'];
            if(!isset($arr_voucher[$voucher->voucher_sn])){
                print json_encode(array('err'=>1, 'msg'=>'现金券' . $voucher->voucher_sn . '不存在，请取消后再提交订单'));
                return;
            }
            $voucher = $arr_voucher[$voucher->voucher_sn];
            if($voucher->repeat_number<=$voucher->used_number || $voucher->end_date < $this->time){
                print json_encode(array('err'=>1, 'msg'=>'现金券' . $voucher->voucher_sn . '已用完或已过期，请取消后再提交订单'));
                return;
            }
            $cart_summary['product_list'][$provider_id]['voucher']->used_number = $voucher->used_number;
        }

        // 计算运费
        //$default_shipping_fee_config = $this->config->item('provider_shipping_config');
        //$cart_summary['shipping_fee'] = 0;
        /*foreach($cart_summary['product_list'] as $provider_id=>$provider)
        {
            $provider_shipping_fee_config = $provider['shipping_fee_config'];
            $shipping_fee = isset($provider_shipping_fee_config[$address->province]) ? $provider_shipping_fee_config[$address->province][0] : $default_shipping_fee_config[$address->province][0];
            $free_shipping_price = isset($provider_shipping_fee_config[$address->province]) ? $provider_shipping_fee_config[$address->province][1] : $default_shipping_fee_config[$address->province][1];
            
            if($cart_summary['product_list'][$provider_id]['product_price']>=$free_shipping_price){
                $cart_summary['product_list'][$provider_id]['shipping_fee'] = 0;
            }else{
                $cart_summary['product_list'][$provider_id]['shipping_fee'] = $shipping_fee;
            }
            $cart_summary['shipping_fee'] += $cart_summary['product_list'][$provider_id]['shipping_fee'];
        }*/
        
        
        // 如果余额支付不足，必须选择其它支付方式
        $cart_amount = $cart_summary['product_price']-$cart_summary['voucher']+$cart_summary['shipping_fee'];
        if($balance_payment_amount<$cart_amount){
            if(empty($pay_id)){
                print json_encode(array('err'=>1 ,'msg'=>'请选择支付方式'));
                return;
            }
            /*if($pay_method=='unionpay'){
                if(empty($bank_code) || !isset($alipay_bank_list[$bank_code])){
                    print json_encode(array('err'=>1 ,'msg'=>'请选择支付银行'));
                    return;
                }
            }*/
        }
     
        $update = array(
            'user_id' => $this->user_id,
            'source_id' => SOURCE_ID_WEB,
            'consignee' => $address->consignee,
            'country' => $address->country,
            'province' => $address->province,
            'city' => $address->city,
            'district' => $address->district,
            'address' => $address->address,
            'zipcode' => !empty($address->zipcode) ? $address->zipcode : '',
            'mobile' => $address->mobile,
            'tel' => !empty($address->tel) ? $address->tel : '',
            'best_time' => '',
            'user_notice' => $remark,
            'invoice_title' => $invoice_title,
            'invoice_content' => '',
            'product_num' => 0,
            'order_price' => 0,
            'shipping_fee' => 0,
            'paid_price' => 0,
            'user_ip' => real_ip(),
            'shipping_id' => 0,
            'pay_id' => 0,
            'create_date' => $this->time,
            'genre_id' => PRODUCT_TOOTH_TYPE
        );
        
        // 开始检查限购数量
        $limit_product = array();
        foreach($cart_list as $key=>$cart){
            if(!$cart->limit_day || !$cart->limit_num){
                continue;
            }
            if(!isset($limit_product[$cart->product_id])){
                $limit_product[$cart->product_id] = array(
                    'product_id' => $cart->product_id,
                    'limit_num' => $cart->limit_num,
                    'limit_day' => $cart->limit_day,
                    'product_name' => $cart->product_name,
                    'product_num' => 0,
                );
                $limit_product[$cart->product_id]['product_num']+=$cart->product_num;
            }
        }
        foreach($limit_product as $product){
            $bounght_num = $this->order_model->get_bought_num($this->user_id, $product['product_id'], $product['limit_day']);
            if($bounght_num+$product['product_num']>$product['limit_num']){
                sys_msg("{$product['product_name']} 超出限购数量，{$product['limit_day']}天内限购{$product['limit_num']}件。", 1);
            }
        }
        // 限购数量检查结束
       
        // 检查库存，并分配虚库数量
        $sub_ids = array_keys(get_pair($cart_list, 'sub_id', 'sub_id'));
        $sub_list = index_array($this->product_model->lock_sub($sub_ids), 'sub_id');
        foreach ($sub_list as $key => &$sub){
           format_sub($sub);
        }
        unset($sub);
        $err_carts = array();
        foreach ($cart_list as $key => &$cart) {
            if (!isset($sub_list[$cart->sub_id]))
                sys_msg("{$cart->product_name} {$cart->color_name} {$cart->size_name} 无可售库存，请移除购物车。", 1);
            $sub = $sub_list[$cart->sub_id];
            if ($sub->sale_num != -2) {
                $sub->sale_num = $sub->sale_num - $cart->product_num;
                if ($sub->sale_num < 0)
                    $err_carts[$sub->sub_id] = $cart->product_name . ' ' . $cart->color_name . ' ' . $cart->size_name;
            }
            $gl_num = min(max($sub->gl_num - $sub->wait_num, 0), $cart->product_num);
            $cart->consign_num = $cart->product_num - $gl_num;
            $sub->gl_num -= $gl_num;
            if ($sub->consign_num > 0)
                $sub->consign_num -= $cart->consign_num;
            $sub->wait_num += $cart->consign_num;
            //更新sub表
            $this->product_model->update_sub(array('gl_num' => $sub->gl_num, 'consign_num' => $sub->consign_num, 'wait_num' => $sub->wait_num), $sub->sub_id);
        }
        unset($cart);

        if ($err_carts)
            sys_msg(implode("\n", $err_carts) . "\n库存不足", 1);
        $arr_order_id = array();
        $cart_num = $this->input->cookie("cart_num");
        foreach($cart_summary['product_list'] as $provider_id=>$provider){
            // 提交订单
            $order = $update;
            $voucher = $provider['voucher'];
            $balance = 0;
            $order['product_num'] = $provider['product_num'];
            $order['order_price'] = $provider['product_price'];  
            // 
            if(defined('CAT_CHECKOUT_VOUCHER')){
                $v_voucher = $this->session->userdata('v_voucher');
                $order['paid_price'] = $voucher ? $voucher->payment_amount*$v_voucher['product_num'] : 0;     
            }else{
                $order['paid_price'] = $voucher ? $voucher->payment_amount : 0;     
            }
            $provider['shipping_fee'] = $this->get_shipping_fee2($checkout['shipping']['shipping_id'], $address_id, $provider_id, 1);
            $order['shipping_fee'] = $provider['shipping_fee'];
            if($balance_payment_amount){
                $balance = min($balance_payment_amount, $provider['product_price']+$provider['shipping_fee']-$order['paid_price']);
                $balance_payment_amount -= $balance;
                $order['paid_price'] += $balance;
            }
            $order['pay_id'] = $order['order_price'] + $order['shipping_fee'] > $order['paid_price'] ? $pay_id : PAY_ID_BALANCE;
            //$order['bank_code'] = $order['pay_id'] == PAY_ID_ALIPAY && $pay_method == 'unionpay' ? $bank_code : '';
            while (true) {
                $order['order_sn'] = get_order_sn();
                $order_id = $this->cart_model->insert_order($order);
                $err_no = $this->db->_error_number();
                if ($err_no == '1062')
                    continue;
                if ($err_no == '0')
                    break;
                $this->db->trans_rollback();
                sys_msg('生成订单失败', 1);
            }
            $order['order_id'] = $order_id;
            // 提交订单商品
            foreach ($provider['product_list'] as $cart) {
                $op = array(
                    'order_id' => $order_id,
                    'product_id' => $cart->product_id,
                    'color_id' => $cart->color_id,
                    'size_id' => $cart->size_id,
                    'product_num' => $cart->product_num,
                    'consign_num' => $cart->consign_num,
                    'consign_mark' => $cart->consign_num,
                    'discount_type' => $cart->discount_type,
                    'market_price' => $cart->market_price,
                    'shop_price' => $cart->shop_price,
                    'product_price' => $cart->product_price,
                    'total_price' => round($cart->product_num * $cart->product_price, 2)
                );
                $op['op_id'] = $this->cart_model->insert_product($op);
                if (!$this->cart_model->assign_trans($order, $op, $cart->product_price)) {//分配储位
                    $this->db->trans_rollback();
                    sys_msg("分配储位失败库存不足.", 1);
                }
                $cart_num -= $cart->product_num;
            }

            //判断该商品是否参加 限时抢购活动
            if($cart->product_id) {
                $this->load->model('special_model');
                $limit_sale = $this->special_model->limit_sale($cart->product_id);
                $sale_num = $this->special_model->num_sale($cart->product_id);
                if($sale_num->num >= 40){
                    sys_msg("对不起，此商品是限购商品活动时间内限购40件。", 1);
                }
                if(!empty($limit_sale)) {
                    // 将订单id写入记录
                    $sale_data = array(
                        'user_id' => $this->user_id,
                        'product_id' => $cart->product_id,
                        'order_id' => $order_id,
                        'cart_date' =>$this->time
                        );
                    $this->special_model->insert_sale($sale_data);
                }
            }
            // 插入支付信息
            if ($voucher) {
                if(defined('CAT_CHECKOUT_VOUCHER')){
                    $v_voucher = $this->session->userdata('v_voucher');
                    $this->cart_model->insert_payment(array(
                        'order_id' => $order_id,
                        'is_return' => 0,
                        'pay_id' => PAY_ID_VOUCHER,
                        'bank_code' => '',
                        'payment_account' => $voucher->voucher_sn,
                        'payment_money' => $voucher->payment_amount * $v_voucher['product_num'],
                        'trade_no' => '',
                        'payment_remark' => '',
                        'payment_date' => $this->time
                    ));
                    $this->cart_model->update_voucher(array('used_number' => $voucher->used_number + $v_voucher['product_num']), $voucher->voucher_sn);
                }else{
                    $this->cart_model->insert_payment(array(
                        'order_id' => $order_id,
                        'is_return' => 0,
                        'pay_id' => PAY_ID_VOUCHER,
                        'bank_code' => '',
                        'payment_account' => $voucher->voucher_sn,
                        'payment_money' => $voucher->payment_amount,
                        'trade_no' => '',
                        'payment_remark' => '',
                        'payment_date' => $this->time
                    ));
                    $this->cart_model->update_voucher(array('used_number' => $voucher->used_number + 1), $voucher->voucher_sn);
                }
            }
            if ($balance > 0) {
                $this->cart_model->insert_payment(array(
                    'order_id' => $order_id,
                    'is_return' => 0,
                    'pay_id' => PAY_ID_BALANCE,
                    'bank_code' => '',
                    'payment_account' => '',
                    'payment_money' => $balance,
                    'trade_no' => '',
                    'payment_remark' => '',
                    'payment_date' => $this->time
                ));
                $user->user_money = round($user->user_money - $balance, 2);
                $this->user_model->update(array('user_money' => $user->user_money), $this->user_id);
                $this->user_model->insert_account(array(
                    'link_id' => $order_id,
                    'user_id' => $this->user_id,
                    'user_money' => $balance * -1,
                    'change_desc' => sprintf("订单 %s 余额支付", $order['order_sn']),
                    'change_code' => 'order_pay',
                    'create_date' => $this->time
                ));
            }
            
            // 处理CPS
            $cpstag = $this->input->cookie("cpstag");
            if (isset($cpstag)) {
                $cps_params = @json_decode($cpstag);
                if (isset($cps_params)) {
                    $this->load->model('cps_model');
                    $this->cps_model->add($order_id, $cps_params);
                }
            }
            $arr_order_id[] = $order_id; 
            $this->cart_model->delete_where(array('session_id' => $cart_sn, 'shop_id' => $provider_id));
        }
        
        // 清除购物车
        
        // 清除session
        $this->session->unset_userdata('checkout');
        $this->db->trans_commit();
        
        $this->input->set_cookie('cart_num', $cart_num, CART_SAVE_SECOND);
        print json_encode(array('err' => 0, 'msg' => '', 'order_id' => implode('-', $arr_order_id)));
    }
    public function proc_checkout_product($sub_id) {
        //global $alipay_bank_list;
        //$cart_sn = get_cart_sn();
        $this->load->model('user_model');
        $this->load->model('region_model');
        $this->load->model('order_model');
        $this->load->library('lib_iplocation');
        $this->load->helper('product');
        //$this->load->config('provider');
        
        //$pay_method = trim($this->input->post('pay_method'));
        //$bank_code = trim($this->input->post('bank_code'));
        $address_id = intval($this->input->post('address_id'));
        $num = intval($this->input->post('num'));
        $use_balance = intval($this->input->post('use_balance'));
        $pay_id = intval($this->input->post('pay_id'));
        $shipping_id = intval($this->input->post('shipping_id'));
        $invoice_title = trim($this->input->post('invoice'));
        $remark = trim($this->input->post('remark'));
        $balance_payment_amount = 0; // 余额支付金额
        
        if (!$this->user_id) {
            print json_encode(array('err' => 1, 'msg' => '提交购物车前请先登录', 'url' => '/user/login'));
            return false;
        }
        
        $sub_info = $this->product_model->sub_info($sub_id);
        if(!$sub_info || !$sub_info->is_audit || !$sub_info->is_on_sale) {
            print json_encode(array('err' => 1, 'msg' => '商品不能购买', 'url' => '/index'));
            return false;
        }
        format_product($sub_info);
	format_sub($sub_info);

        if ($sub_info->sale_num != -2 && $sub_info->sale_num-$num <= 0){
            print json_encode(array('err' => 1, 'msg' => '商品不能购买', 'url' => '/index'));
            return false;
        }
        $sub_info->buy_num = $num;
        voucher_gc($sub_info, 'product');
        // 收集结算信息

        $checkout = get_checkout();
        
        $address = $this->user_model->filter_address(array('address_id' => $address_id));
        if (!$address) sys_msg('请选择收货地址', 1);
        $region_shipping['province'] = $address->province;

        $checkout['shipping']['shipping_id'] = $shipping_id;
        $shipping = $this->cart_model->filter_shipping(array('shipping_id' => $checkout['shipping']['shipping_id']));
        if (!$shipping) sys_msg('请选择快递公司', 1);
        //$checkout['payment']['balance'] = round($this->input->post('balance'), 2);
        //支付，bank_code
        $checkout['payment']['pay_id'] = $pay_id;
        //$checkout['payment']['pay_id'] = intval(mb_substr($pay, 0, strpos($pay, '_')));
        //$checkout['payment']['bank_code'] = mb_substr($pay, strpos($pay, '_') + 1);
        $checkout['payment']['invoice_title'] = $invoice_title;
        //$checkout['payment']['invoice_title'] = trim($this->input->post('invoice_title'));
        //$checkout['payment']['invoice_content'] = trim($this->input->post('invoice_content'));
        $this->session->set_userdata('checkout', $checkout);
        $region_shipping['pay_id'] = $checkout['payment']['pay_id'];
        
        // 验证收集的数据
        $this->db->trans_begin();
        $user = $this->user_model->lock_user($this->user_id);
        if (!$user) { // 如果后台将用户删除了，后果会这样
            $this->session->destroy_sess();
            print json_encode(array('err' => 1, 'msg' => '您需要重新登录', 'url' => 'user/login'));
            return false;
        }
        if($use_balance){
            $balance_payment_amount = $user->user_money;
        }
        
        $shipping_fee = $this->get_shipping_fee3($shipping_id, $address->address_id, $sub_info->product_id,$num, 1);
        
        // 验证现金券
        $voucher_amount = 0;
        $voucher = array();
        if(isset($checkout['payment']['voucher']['product'][$sub_info->product_id])){
            $voucher = $this->cart_model->lock_voucher($checkout['payment']['voucher']['product'][$sub_info->product_id]->voucher_sn);
            if($voucher->repeat_number<=$voucher->used_number || $voucher->end_date < $this->time){
                print json_encode(array('err'=>1, 'msg'=>'现金券' . $voucher->voucher_sn . '已用完或已过期，请取消后再提交订单'));
                return;
            }
            $voucher_amount = $voucher->voucher_amount;
        }
        
        // 如果余额支付不足，必须选择其它支付方式
        $cart_amount = fix_price($sub_info->product_price*$num)-$voucher_amount+$shipping_fee;
        if($balance_payment_amount<$cart_amount){
            if(empty($pay_id)){
                print json_encode(array('err'=>1 ,'msg'=>'请选择支付方式'));
                return;
            }
        }
     
        $update = array(
            'user_id' => $this->user_id,
            'source_id' => SOURCE_ID_WEB,
            'consignee' => $address->consignee,
            'country' => $address->country,
            'province' => $address->province,
            'city' => $address->city,
            'district' => $address->district,
            'address' => $address->address,
            'zipcode' => !empty($address->zipcode) ? $address->zipcode : '',
            'mobile' => $address->mobile,
            'tel' => !empty($address->tel) ? $address->tel : '',
            'best_time' => '',
            'user_notice' => $remark,
            'invoice_title' => $invoice_title,
            'invoice_content' => '',
            'product_num' => 0,
            'order_price' => 0,
            'shipping_fee' => 0,
            'paid_price' => 0,
            'user_ip' => real_ip(),
            'shipping_id' => 0,
            'pay_id' => 0,
            'create_date' => $this->time,
            'genre_id' => PRODUCT_TOOTH_TYPE
        );
        
       
        // 检查库存，并分配虚库数量
        //$sub_ids = array_keys(get_pair($cart_list, 'sub_id', 'sub_id'));
        //$this->product_model->lock_sub($sub_info->sub_id);
        $sub_info2 = $this->product_model->lock_sub($sub_info->sub_id);
        format_sub($sub_info2);
        
        if ($sub_info2->sale_num != -2) {
            $sub_info2->sale_num = $sub_info2->sale_num - $num;
            if ($sub_info2->sale_num < 0) {
                $err_carts = $sub_info->product_name . ' ' . $sub_info->color_name . ' ' . $sub_info->size_name;
                sys_msg($err_carts . "\n库存不足", 1);
            }
        }      
                
        $gl_num = min(max($sub_info2->gl_num - $sub_info2->wait_num, 0), $num);
        $consign_num = $num - $gl_num;
        $sub_info2->gl_num -= $gl_num;
        if ($sub_info2->consign_num > 0) 
            $sub_info2->consign_num -= $consign_num;
        $sub_info2->wait_num += $consign_num;
            //更新sub表
        $this->product_model->update_sub(array('gl_num' => $sub_info2->gl_num, 'consign_num' => $sub_info2->consign_num, 'wait_num' => $sub_info2->wait_num), $sub_info2->sub_id);


        $arr_order_id = array();
        //foreach($cart_summary['product_list'] as $provider_id=>$provider){
            // 提交订单
            $order = $update;
            $balance = 0;
            $order['product_num'] = $num;
            /**
             * v 2016.04.18 指定商品 购买立减活动
             */
            global $product_minus_activity;
            if(!empty($product_minus_activity) && strtotime($product_minus_activity['end_time']) >= time()){
                if( $sub_info->product_id == $product_minus_activity['product_id']){
                    $sub_info->product_price -= $product_minus_activity['minus_price']; 
                }
            }
            $order['order_price'] = fix_price($sub_info->product_price*$num);
            // 
            if(defined('CAT_CHECKOUT_VOUCHER')){
                $v_voucher = $this->session->userdata('v_voucher_product');
                $order['paid_price'] =  $voucher_amount * $v_voucher['product_num'];     
            }else{
                $order['paid_price'] = $voucher_amount;
            }        
            //$provider['shipping_fee'] = $this->get_shipping_fee2($checkout['shipping']['shipping_id'], $address_id, $provider_id, 1);
            
            $order['shipping_fee'] = $shipping_fee;
            if($balance_payment_amount){
                $balance = min($balance_payment_amount, $order['order_price']+$shipping_fee-$order['paid_price']);
                $balance_payment_amount -= $balance;
                $order['paid_price'] += $balance;
            }
            $order['pay_id'] = $order['order_price'] + $order['shipping_fee'] > $order['paid_price'] ? $pay_id : PAY_ID_BALANCE;
            //$order['bank_code'] = $order['pay_id'] == PAY_ID_ALIPAY && $pay_method == 'unionpay' ? $bank_code : '';
            while (true) {
                $order['order_sn'] = get_order_sn();
                $order_id = $this->cart_model->insert_order($order);
                $err_no = $this->db->_error_number();
                if ($err_no == '1062')
                    continue;
                if ($err_no == '0')
                    break;
                $this->db->trans_rollback();
                sys_msg('生成订单失败', 1);
            }
            $order['order_id'] = $order_id;
            // 提交订单商品
            //foreach ($provider['product_list'] as $cart) {
                $op = array(
                    'order_id' => $order_id,
                    'product_id' => $sub_info->product_id,
                    'color_id' => $sub_info->color_id,
                    'size_id' => $sub_info->size_id,
                    'product_num' => $num,
                    'consign_num' => $consign_num,
                    'consign_mark' => $consign_num,
                    'market_price' => $sub_info->market_price,
                    'shop_price' => $sub_info->shop_price,
                    'product_price' => $sub_info->product_price,
                    'total_price' => round($num * $sub_info->product_price, 2)
                );
                $op['op_id'] = $this->cart_model->insert_product($op);
                if (!$this->cart_model->assign_trans($order, $op, $sub_info->product_price)) {//分配储位
                    $this->db->trans_rollback();
                    sys_msg("分配储位失败库存不足.", 1);
                }
            //}
            
            // 插入支付信息
            if ($voucher) {
                if(defined('CAT_CHECKOUT_VOUCHER')){
                    $v_voucher = $this->session->userdata('v_voucher_product');
                    $this->cart_model->insert_payment(array(
                        'order_id' => $order_id,
                        'is_return' => 0,
                        'pay_id' => PAY_ID_VOUCHER,
                        'bank_code' => '',
                        'payment_account' => $voucher->voucher_sn,
                        'payment_money' => $voucher->voucher_amount * $v_voucher['product_num'],
                        'trade_no' => '',
                        'payment_remark' => '',
                        'payment_date' => $this->time
                    ));
                    $this->cart_model->update_voucher(array('used_number' => $voucher->used_number + $v_voucher['product_num']), $voucher->voucher_sn);
                }else{
                    $this->cart_model->insert_payment(array(
                        'order_id' => $order_id,
                        'is_return' => 0,
                        'pay_id' => PAY_ID_VOUCHER,
                        'bank_code' => '',
                        'payment_account' => $voucher->voucher_sn,
                        'payment_money' => $voucher->voucher_amount,
                        'trade_no' => '',
                        'payment_remark' => '',
                        'payment_date' => $this->time
                    ));
                    $this->cart_model->update_voucher(array('used_number' => $voucher->used_number + 1), $voucher->voucher_sn);
                }
            }        

            if ($balance > 0) {
                $this->cart_model->insert_payment(array(
                    'order_id' => $order_id,
                    'is_return' => 0,
                    'pay_id' => PAY_ID_BALANCE,
                    'bank_code' => '',
                    'payment_account' => '',
                    'payment_money' => $balance,
                    'trade_no' => '',
                    'payment_remark' => '',
                    'payment_date' => $this->time
                ));
                $user->user_money = round($user->user_money - $balance, 2);
                $this->user_model->update(array('user_money' => $user->user_money), $this->user_id);
                $this->user_model->insert_account(array(
                    'link_id' => $order_id,
                    'user_id' => $this->user_id,
                    'user_money' => $balance * -1,
                    'change_desc' => sprintf("订单 %s 余额支付", $order['order_sn']),
                    'change_code' => 'order_pay',
                    'create_date' => $this->time
                ));
            }
            
            // 处理CPS
            $cpstag = $this->input->cookie("cpstag");
            if (isset($cpstag)) {
                $cps_params = @json_decode($cpstag);
                if (isset($cps_params)) {
                    $this->load->model('cps_model');
                    $this->cps_model->add($order_id, $cps_params);
                }
            }
            $arr_order_id[] = $order_id;
        
        // 清除购物车
        
        // 清除session
        unset($checkout['payment']['voucher']['product'][$sub_info->product_id]);
        $this->session->set_userdata('checkout', $checkout);
        $this->db->trans_commit();

        print json_encode(array('err' => 0, 'msg' => '', 'order_id' => implode('-', $arr_order_id)));
    }

    public function success($order_ids, $genre_id=PRODUCT_TOOTH_TYPE) {
        global $alipay_bank_list;
        $this->load->model('order_model');
        $order_ids = trim($order_ids);
        $arr_order_id = array_filter(array_map('intval',explode('-', $order_ids)));
        $order_list = $this->order_model->order_list_by_ids($arr_order_id, $genre_id);
        $product_list = $this->order_model->product_list_by_order_ids($arr_order_id);
        $payment_list = $this->order_model->payment_list_by_order_ids($arr_order_id);
        // 对数据进行汇总
        $arr_order = array();
        $order_amount = 0; // 待付金额
        $order_price = 0; // 订单商品总额
        $shipping_fee = 0; // 运费总额
        $voucher = 0; // 现金券总额
        $product_num = 0; // 商品总件数
        $default_order = 0;
        foreach($order_list as $order)
        {
            if($order->order_status!=0 and $order->order_status!=1) continue;
            if($order->user_id!=$this->user_id) continue;
            $order->voucher = 0;
            $order->provider_id = 0;
            $order->provider_name = '';
            $order->product_list = array();
            $order_amount += $order->order_price + $order->shipping_fee - $order->paid_price;
            $order_price += $order->order_price;
            $shipping_fee += $order->shipping_fee;
            $product_num += $order->product_num;
            $arr_order[$order->order_id] = $order;
        }
        foreach($payment_list as $payment)
        {
            if(!isset($arr_order[$payment->order_id])) continue;
            if($payment->pay_id == PAY_ID_VOUCHER)
            {
                $arr_order[$payment->order_id]->voucher+=$payment->payment_money;
                $voucher += $payment->payment_money;
            }
        }
        foreach($product_list as $product)
        {
            if(!isset($arr_order[$product->order_id])) continue;
            
            if(!isset($arr_order[$product->order_id]->shop_price)) 
                $arr_order[$product->order_id]->shop_price = 0;
            $arr_order[$product->order_id]->shop_price += $product->shop_price * $product->product_num;
            
            $arr_order[$product->order_id]->product_list[] = $product;
            if (!$default_order) $default_order = $product->order_id;

            if(empty($arr_order[$product->order_id]->provider_id)){
                $arr_order[$product->order_id]->provider_id = $product->provider_id;
                $arr_order[$product->order_id]->provider_name = $product->provider_name;
            }
            
        }
        if(empty($arr_order)){
            sys_msg('订单不存在', 1);
        }
        
       /*
        $cps_script = "";
        $cpstag = $this->input->cookie("cpstag");
        if (isset($cpstag)) {
            $cps_params = @json_decode($cpstag);
            if (isset($cps_params)) {
                $this->load->model('cps_model');
                $cps_script = $this->cps_model->script($arr_order_id, $cps_params);
            }
        }
        * 
        */
        $pay_list = index_array($this->cart_model->available_pay_list(), 'pay_id');
        $this->load->helper('cart');
        format_pay_list($pay_list);

        $this->load->vars(array(
            'title' => '订单提交成功',
            'order_list' => $arr_order,
            'order_price' => $order_price,
            'order_amount' => $order_amount,
            'voucher' => $voucher,
            'shipping_fee' => $shipping_fee,
            'product_num' => $product_num,
            'pay_list' => $pay_list, 
            'default_order' => $default_order
        ));
        if ($genre_id == PRODUCT_COURSE_TYPE) {
            $this->load->view('mobile/cart/success_course');
        } else {
            $this->load->view('cart/success');
        }
    }

    public function set_shipping_fee() {
        $this->load->model('user_model');
        $cart_sn = get_cart_sn();
        $address_id = intval($this->input->post('address_id'));
        $province_id = intval($this->input->post('province_id'));
        $pay = $this->input->post('pay_id');
        $pay_id = intval(mb_substr($pay, 0, strpos($pay, '_')));
        if (!$this->user_id)
            sys_msg('请选登录', 1);
        if (empty($address_id) && empty($province_id))
            sys_msg('请先选择收货地址', 1);
        if ($address_id) {
            $address = $this->user_model->filter_address(array('address_id' => $address_id));
            if (!$address)
                sys_msg('请选择收货地址', 1);
            $region_shipping['province'] = $address->province;
        }
        else {
            $region_shipping['province'] = $province_id;
        }
        $region_shipping['pay_id'] = $pay_id;

        //检测是否使用了现金券
        $checkout = $this->session->userdata('checkout');
        $voucher = isset($checkout['payment']['voucher']) ? $checkout['payment']['voucher'] : NULL;

        $cart_list = $this->cart_model->cart_info($cart_sn);
        $cart_summary = summary_cart($cart_list, $voucher, NULL, $region_shipping);
        print json_encode(array('err' => 0, 'msg' => '', 'cart_summary' => $cart_summary));
    }

    public function get_shipping_fee() {
        $this->load->model('user_model');
        $address_id = intval($this->input->post('address_id'));
        $province_id = intval($this->input->post('province_id'));
        $district_id = intval($this->input->post('district_id'));
        $this->load->library('lib_iplocation');
        if (empty($address_id) && empty($province_id))
            sys_msg('请先选择收货地址', 1);
        if ($address_id) {
            $address = $this->user_model->filter_address(array('address_id' => $address_id));
            if (!$address)
                sys_msg('请选择收货地址', 1);
            $province_id = $address->province;
            $city = $address->city;
            $district_id = $address->district;
        }
        //get shipping fee
        $shipping_fee_str = '';
        $region_shipping_fee = $this->lib_iplocation->get_region_shipping_fee();
        if (isset($region_shipping_fee['ids'][$province_id])) {
            $district_shipping = $this->lib_iplocation->get_district_shipping_fee($district_id, $city, $province_id);
            if ($district_shipping && $region_shipping_fee['ids'][$province_id])
                $shipping_fee_str = "配送费：在线支付￥" . $region_shipping_fee['ids'][$province_id]['online_shipping_fee'] . "元；货到付款￥" . $region_shipping_fee['ids'][$province_id]['cod_shipping_fee'] . "元";
            else
                $shipping_fee_str = "配送费：在线支付￥" . $region_shipping_fee['ids'][$province_id]['online_shipping_fee'] . "元；货到付款不支持";
        }
        print json_encode(array('err' => 0, 'msg' => '', 'shipping_fee_str' => $shipping_fee_str));
    }

    /**
     * 使用现金券
     */
    public function pay_voucher() {
        $cart_sn = get_cart_sn();
        $voucher_sn = trim($this->input->post('voucher_sn'));
        $provider_id = intval($this->input->post('provider_id'));

        if (!$this->user_id)
            sys_msg('请选登录', 1);
        /*if(!$provider_id)
        {
            sys_msg('请先指定商家');
        }*/
        
        $cart_list = $this->cart_model->cart_info($cart_sn);
        //voucher_gc($cart_list); // 回收无效的现金券
        $checkout = $this->session->userdata('checkout');       
        
        if(!empty($checkout['payment']['voucher'])){
            // 如果该券已经在其它供应商下使用过了或者当前供应商已经用过其它的券了，报错返回
            foreach ($checkout['payment']['voucher'] as $pid => $vv) {                
                foreach ($vv as $v) {
                    if($v->voucher_sn==$voucher_sn){
                        sys_msg('现金券不能重复使用，请先取消。', 1);
                    }

                    //由于每结算一次，只能使用一张券，所以这里要取消上一张券
                    //$checkout['payment']['voucher'] = array();
                    //$this->session->set_userdata('checkout', $checkout);
                    //sys_msg('您已使用了另一张现金券，请先取消。', 1);
                    //if($pid==$provider_id){
                    //if($pid==$voucher->provider){
                        //sys_msg('您已使用了另一张现金券，请先取消。', 1);
                        //$this->unpay_voucher(1);
                    //}
                }
            }
        }
        
        $this->cart_model->lock_voucher($voucher_sn);
        $voucher = $this->cart_model->voucher_info($voucher_sn);
        if (!$voucher)
            sys_msg('现金券不可用', 1);
        if ($voucher->user_id && $voucher->user_id != $this->user_id)
            sys_msg('现金券不可用', 1);
        $cart_list = $this->cart_model->cart_info($cart_sn);
        $voucher->payment_amount = calc_voucher_payment_amount($voucher, $cart_list, $provider_id);
        if (!$voucher->payment_amount)
            sys_msg('不满足现金券使用条件', 1);
        $checkout['payment']['voucher']['provider'][$voucher->provider] = $voucher;
        //$checkout['payment']['voucher'][0] = $voucher;
        $this->session->set_userdata('checkout', $checkout);
        if ($voucher->campaign_type == 'print' && !$voucher->user_id) {
            $this->cart_model->update_voucher(array('user_id' => $this->user_id), $voucher_sn);
        }

        if(defined('CAT_CHECKOUT_VOUCHER')){
        // 获取商品购买的数量 v_cart_summary
            if( $voucher->campaign_type == 'repeat' && $voucher->repeat_number > $voucher->used_number){
                foreach ($cart_list as $vou_val) {
                    if($vou_val->product_id == $voucher->product) {
                        if( $vou_val->product_num <= ($voucher->repeat_number - $voucher->used_number)){
                            $voucher_product_num = $vou_val->product_num;
                        }else{
                            $voucher_product_num = $voucher->repeat_number - $voucher->used_number;
                        }
                    }
                }                          
                $v_voucher = array('product_id' =>$voucher->product,'product_num' => $voucher_product_num, 'voucher_sn' => $voucher->voucher_sn);
                $this->session->set_userdata('v_voucher',$v_voucher);
                $voucher->voucher_amount = $voucher->voucher_amount * $voucher_product_num;
            }
        }
        print json_encode(array('err'=>0, 'msg'=>'', 'data' => $voucher));
    }
    /**
     * 单品使用现金券
     */
    public function pay_voucher_product() {
        //$cart_sn = get_cart_sn();
        $voucher_sn = trim($this->input->post('voucher_sn'));
        $product_id = intval($this->input->post('product_id'));
        $num = intval($this->input->post('num'));

        if (!$this->user_id)
            sys_msg('请选登录', 1);
        if(!$product_id)
        {
            sys_msg('请先选择商品');
        }
        
        $product = $this->product_model->product_info($product_id);
        format_product($product);
        $product->product_num = $num;
        //voucher_gc($cart_list); // 回收无效的现金券
        $checkout = $this->session->userdata('checkout');       
        
        if(!empty($checkout['payment']['voucher'])){
            // 如果该券已经在其它供应商下使用过了或者当前供应商已经用过其它的券了，报错返回
            foreach ($checkout['payment']['voucher'] as $pid => $vv) {                
                foreach ($vv as $v) {
                    if($v->voucher_sn==$voucher_sn){
                        sys_msg('现金券不能重复使用，请先取消。', 1);
                    }

                    //由于每结算一次，只能使用一张券，所以这里要取消上一张券
                    //$checkout['payment']['voucher'] = array();
                    //$this->session->set_userdata('checkout', $checkout);
                    //sys_msg('您已使用了另一张现金券，请先取消。', 1);
                    //if($pid==$provider_id){
                    //if($pid==$voucher->provider){
                        //sys_msg('您已使用了另一张现金券，请先取消。', 1);
                        //$this->unpay_voucher(1);
                    //}
                }
            }
        }
        
        $this->cart_model->lock_voucher($voucher_sn);
        $voucher = $this->cart_model->voucher_info($voucher_sn);
        if (!$voucher)
            sys_msg('现金券不可用', 1);
        if ($voucher->user_id && $voucher->user_id != $this->user_id)
            sys_msg('现金券不可用', 1);
        //$cart_list = $this->cart_model->cart_info($cart_sn);
        $voucher->payment_amount = calc_voucher_payment_amount_product($voucher, $product);

        if (!$voucher->payment_amount)
            sys_msg('不满足现金券使用条件', 1);
        $checkout['payment']['voucher']['product'][$product_id] = $voucher;
        //$checkout['payment']['voucher'][0] = $voucher;
        $this->session->set_userdata('checkout', $checkout);
        if ($voucher->campaign_type == 'print' && !$voucher->user_id) {
            $this->cart_model->update_voucher(array('user_id' => $this->user_id), $voucher_sn);
        }

        if(defined('CAT_CHECKOUT_VOUCHER')){
        // 获取商品购买的数量 v_cart_summary
            if( $voucher->campaign_type == 'repeat' && $voucher->repeat_number > $voucher->used_number){
                if($product_id == $voucher->product) {
                    if( $num <= ($voucher->repeat_number - $voucher->used_number)){
                        $voucher_product_num = $num;
                    }else{
                        $voucher_product_num = $voucher->repeat_number - $voucher->used_number;
                    }
                }                                        
                $v_voucher = array('product_id' =>$voucher->product,'product_num' => $voucher_product_num, 'voucher_sn' => $voucher->voucher_sn);
                $this->session->set_userdata('v_voucher_product',$v_voucher);
                $voucher->voucher_amount = $voucher->voucher_amount * $voucher_product_num;
            }
        }

        print json_encode(array('err'=>0, 'msg'=>'', 'data' => $voucher));
    }

    /**
     * 取消现金券
     */
    public function unpay_voucher($is_return=0) {
        $cart_sn = get_cart_sn();
        $voucher_sn = trim($this->input->post('voucher_sn'));
        $checkout = $this->session->userdata('checkout');
        if(empty($checkout['payment']['voucher'])){
            print json_encode(array('err'=>0));
        }
        foreach($checkout['payment']['voucher'] as $key => $voucher2)
        {
            foreach ($voucher2 as $k => $voucher) {
                if($voucher->voucher_sn==$voucher_sn){
                    unset($checkout['payment']['voucher'][$key][$k]);
                }
            }
        }
        $this->session->set_userdata('checkout', $checkout);
        $result = array(array('err'=>0));
        if ($is_return){
            return $result;
        }
        print json_encode($result);
    }

    public function load_address_form() {
        $this->load->model('user_model');
        $address_id = intval($this->input->post('address_id'));
        if ($address_id) {
            $address = $this->user_model->filter_address(array('address_id' => $address_id, 'user_id' => $this->user_id));
            if (!$address)
                sys_msg('记录不存在', 1);
        }else {
            $address = (object) array('address_id' => 0, 'country' => 1, 'province' => 0, 'city' => 0, 'district' => 0);
        }

        list($province_list, $city_list, $district_list) = $this->cart_model->cart_region(array(
            'country' => $address->country,
            'province' => $address->province,
            'city' => $address->city
        ));
        $html = $this->load->view('cart/address_block', array(
            'shipping' => (array) $address,
            'province_list' => $province_list,
            'city_list' => $city_list,
            'district_list' => $district_list
                ), TRUE);
        print json_encode(array('err' => 0, 'msg' => '', 'html' => $html));
    }

    public function submit_address_form() {
        $this->load->model('user_model');
        $this->load->model('region_model');
        $address_id = intval($this->input->post('address_id'));
        $update['address_id'] = trim($this->input->post('address_id'));
        $update['consignee'] = trim($this->input->post('consignee'));
        $update['address'] = trim($this->input->post('address'));
        $update['zipcode'] = trim($this->input->post('zipcode'));
        $update['mobile'] = trim($this->input->post('mobile'));
        $update['tel'] = trim($this->input->post('tel'));
        $update['country'] = 1;
        $update['province'] = intval($this->input->post('province'));
        $update['city'] = intval($this->input->post('city'));
        $update['district'] = intval($this->input->post('district'));
        if (!$update['consignee'] || !$update['zipcode'] || !$update['province'] || !$update['city'])
            sys_msg('信息填写不完整', 1);
        if (!$update['mobile'] && !$update['tel'])
            sys_msg('信息填写不完整', 1);
        if (!$update['district']) {
            $region = $this->region_model->filter(array('parent_id' => $update['city']));
            if ($region)
                sys_msg('信息填写不完整', 1);
        }
        $address = $this->user_model->filter_address(array('address_id' => $address_id));
        if (!$address) {
            $update["user_id"] = $this->user_id;
            $address_id = $this->user_model->insert_address($update);
            //sys_msg('记录不存在',1); 
        } else {
            $this->user_model->update_address($update, $address_id);
        }
        

        $checkout = $this->session->userdata('checkout');
        if (is_array($checkout) && isset($checkout['shipping'])) {
            $checkout['shipping']['address_id'] = $address_id;
            $this->session->set_userdata('checkout', $checkout);
        } else {
            $checkout['shipping']['address_id'] = $address_id;
        }
        $html = $this->load->view('cart/address_list', array(
            'shipping' => $checkout['shipping'],
            'address_list' => $this->user_model->address_list($this->user_id),
                ), TRUE);
        print json_encode(array('err' => 0, 'msg' => '', 'html' => $html));
    }

    public function info() {
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control:no-cache,must-revalidate");
        header("Pragma:no-cache");
        $this->load->model('user_model');
        $this->load->library('memcache');
        $cart_sn = get_cart_sn();
        $cart_list = $this->cart_model->cart_info($cart_sn, TRUE);
        if (!$cart_list) {
            die(json_encode(array('err' => 0, 'msg' => '', 'nil' => 1, 'html' => "购物袋中还没有商品，赶紧选购吧！&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />", 'cart_number' => 0)));
            return;
        }
        $cart_summary = summary_cart($cart_list);
        list($product_list, $package_list) = split_package_product($cart_list);
        if ($this->user_id) {
            $user = $this->user_model->filter(array('user_id' => $this->user_id));
            $this->load->vars('user', $user);
        }

        $data = array(
            'title' => '购物车',
            'product_list' => $cart_list,
            'package_list' => $package_list,
            'cart_summary' => $cart_summary,
            'gifts_list' => memcache_get_gifts()//取赠品
        );
        $this->input->set_cookie('cart_num', $cart_summary['product_num'], CART_SAVE_SECOND);
        $html = $this->load->view('cart/info', $data, TRUE);
        die(json_encode(array('err' => 0, 'msg' => '', 'nil' => 0, 'html' => $html, 'cart_number' => $cart_summary['product_num'])));
    }
    //修改购物车中商品的规格
    public function size_edit(){
        $this->load->helper('product');
        $data = array();
        $rec_id = intval($this->input->get_post('rec_id'));
        $goods = $this->cart_model->get_cart_goods_item($rec_id);
        $cart_list = get_pair($this->product_model->sub_in_cart($goods->product_id),'sub_id','product_num');
        $sub_param = array('product_id' => $goods->product_id);
        //sku在sub的库存
        foreach ($this->product_model->sub_list($sub_param) as $sub) {
            //if (!isset($g_list[$sub->color_id]))
            //    continue; //如果图片没有，略过,一般是由缓存引起的
            format_sub($sub);
            if ($sub->sale_num != -2 && isset($cart_list[$sub->sub_id])) {
                $sub->sale_num = max($sub->sale_num - $cart_list[$sub->sub_id], 0);
            }
            $sub->sale_num = $sub->sale_num == -2 ? MAX_SALE_NUM : min(MAX_SALE_NUM, $sub->sale_num);
            if (!$sub->is_on_sale)
                $sub->sale_num = 0; //如果未上架，则不能销售
            $data['sub_list'][] = $sub;
        }
        $data['goods'] = $goods;
        $html = $this->load->view('mobile/cart/edit_size', $data, true);
        die(json_encode(array('err' => 0, 'msg' => '', 'html' => $html)));
    }
    // 更换规格处理
    public function size_edit_proc(){
        $rec_id = intval($this->input->post('rec_id'));
        $sub_id = intval($this->input->post('sub_id'));
        $ret = $this->add_to_cart(1);
        $this->remove_from_cart(1);
        die(json_encode($ret));
    }
    //批量修改商品数量
    public function update_cart_batch(){        
        $rec_ids = $this->input->post('rec_ids');
        $nums = $this->input->post('nums');
        $rec_id_arr = explode("|", $rec_ids);
        $num_arr = explode("|", $nums);
        foreach ($rec_id_arr as $key => $id){
            $_POST['rec_id'] = $id;
            $_POST['num'] = $num_arr[$key];
            $this->update_cart(1);
        }
        die(json_encode(array('err' => 0, 'msg' => '')));
    }
    //发票抬头添加
    public function invoice_add(){
        $content = trim($this->input->post('content'));
        $result = $this->cart_model->user_invoice_add($this->user_id, $content);
        if (!$result){
            die(json_encode(array('err' => 1, 'msg' => '该发票抬头已存在，不可重复添加！')));
        }
        die(json_encode(array('err' => 0, 'msg' => '')));
    }
    // 基于店铺的购物车=》按重量收取运费
    public function get_shipping_fee2($shipping_id, $address_id, $shop_id=0, $is_return=0){
        $this->load->model('region_model');
        $this->load->model('user_model');
        $result = 0;
        $cart_sn = get_cart_sn();
        $cart_list = $this->cart_model->cart_info($cart_sn, TRUE, false, $shop_id);
        $checkout = get_checkout();
	
        $cart_summary = summary_cart(
            $cart_list, isset($checkout['payment']['voucher']['provider']) ? $checkout['payment']['voucher']['provider'] : NULL, isset($checkout['payment']['balance']) ? $checkout['payment']['balance'] : NULL
        );
        $address = $this->user_model->filter_address(array('address_id' => $address_id));
        
        $fee = $this->region_model->get_shipping_fee_province($shipping_id, $address->province);
        if ($cart_summary['unpay_price'] >= SHIPPING_FREE_ORDER_PRICE && $cart_summary['product_weight'] <= 10000){
            if (!$is_return){
                die(json_encode(array('err' => 0, 'msg' => '', 'data' => $result)));
            }
            return $result;
        }
        if (!$fee){
            $result = SHIPPING_FEE_DEFAULT;
        } else {
            $first_wt = ($fee->first_weight > 0) ? $fee->first_weight : 1000;
            if ($cart_summary['product_weight'] <= $first_wt){
                $result = $fee->shipping_fee1;
            } else {
                $result = $fee->shipping_fee1 + ceil($cart_summary['product_weight']-$first_wt)/1000*$fee->shipping_fee2;
            }
        }

        //免邮
        $v_product_id_data = array();
        foreach ($cart_summary['product_list'] as $provider_id => $provider){
            foreach ($provider['product_list'] as $product_v){
                $v_product_id_data[] = array($product_v->product_id, $product_v->product_num * $product_v->product_price); //商品数组(id,price)
            }
        }
        $v_campaign_package = campaign_package_product_v($v_product_id_data);
        if(!empty($v_campaign_package)) $result = 0;

        $result = fix_price($result);
        if (!$is_return){
            die(json_encode(array('err' => 0, 'msg' => '', 'data' => $result)));
        }
        return $result;
    }
    // 基于单款商品=》按重量收取运费
    public function get_shipping_fee3($shipping_id, $address_id, $product_id, $num, $is_return=0){
        $this->load->model('region_model');
        $this->load->model('user_model');
        $this->load->model('product_model');
        $result = 0;
        $checkout = get_checkout();
        /*$cart_sn = get_cart_sn();
        $cart_list = $this->cart_model->cart_info($cart_sn, TRUE, false, $shop_id);
        $checkout = get_checkout();
        $cart_summary = summary_cart(
            $cart_list, $checkout['payment']['voucher'], isset($checkout['payment']['balance']) ? $checkout['payment']['balance'] : NULL
        );*/
        $product = $this->product_model->product_info($product_id);
        format_product($product);
        $address = $this->user_model->filter_address(array('address_id' => $address_id));
        
        $fee = $this->region_model->get_shipping_fee_province($shipping_id, $address->province);
        $voucher_amount = 0;
        if (isset($checkout['payment']['voucher']['product'][$product_id])) $voucher_amount = $checkout['payment']['voucher']['product'][$product_id]->payment_amount;
        
        if ($product->product_price*$num-$voucher_amount >= SHIPPING_FREE_ORDER_PRICE && $product->product_weight <= 10000){
            if (!$is_return){
                die(json_encode(array('err' => 0, 'msg' => '', 'data' => $result)));
            }
            return $result;
        }
        if (!$fee){
            $result = SHIPPING_FEE_DEFAULT;
        } else {
            $first_wt = ($fee->first_weight > 0) ? $fee->first_weight : 1000;
            if ($product->product_weight <= $first_wt){
                $result = $fee->shipping_fee1;
            } else {
                $result = $fee->shipping_fee1 + ceil($product->product_weight-$first_wt)/1000*$fee->shipping_fee2;
            }
        }

        //免邮
        $v_product_id_data = array();
        /*foreach ($cart_summary['product_list'] as $provider_id => $provider){
            foreach ($provider['product_list'] as $product_v){
                $v_product_id_data[] = array($product_v->product_id, $product_v->product_num * $product_v->product_price); //商品数组(id,price)
            }
        }*/
        $v_product_id_data[] = array($product->product_id, $num * $product->product_price); //商品数组(id,price)
        
        $v_campaign_package = campaign_package_product_v($v_product_id_data);
        if(!empty($v_campaign_package)) $result = 0;

        $result = fix_price($result);
        if (!$is_return){
            die(json_encode(array('err' => 0, 'msg' => '', 'data' => $result)));
        }
        return $result;
    }
    
    //课程结算页
    public function checkout_course($sub_id) {
        $this->load->helper('product');
        $sub_info = $this->product_model->sub_info($sub_id);
        if(!$sub_info || !$sub_info->is_audit || !$sub_info->is_on_sale) redirect('index');
        format_product($sub_info);
	format_sub($sub_info);
        $sub_info->sale_num = $sub_info->sale_num == -2 ? MAX_SALE_NUM : min(MAX_SALE_NUM, $sub_info->sale_num);

        if ($sub_info->sale_num <= 0){
            redirect('index');
        }
        
        if (!$this->user_id) {
            $this->session->set_userdata(array(
                'login_return_url' => 'product/info/'.$sub_info->product_id,
                'login_msg' => '订单结算之前请先登录'
            ));
            goto_login('product/info/'.$sub_info->product_id);
        }

        $this->load->vars(array(
            'title' => '课程培训报名',
            'product' => $sub_info, 
            'genre_id' => PRODUCT_COURSE_TYPE
        ));

	$this->load->view('mobile/cart/checkout_course');
    }
    
    public function proc_checkout_course() {
        $cart_sn = get_cart_sn();
        $this->load->model('user_model');
        $this->load->model('region_model');
        $this->load->model('order_model');
        $this->load->helper('product');
        //$this->load->config('provider');
        
        $sub_id = intval($this->input->post('sub_id'));
        $num = intval($this->input->post('num'));
        $consignee = trim($this->input->post('consignee'));
        $mobile = trim($this->input->post('mobile'));
        $email = trim($this->input->post('email'));
        $address = trim($this->input->post('address'));
        $company = trim($this->input->post('company'));
        $remark = trim($this->input->post('remark'));
        
        if (!$this->user_id) {
            print json_encode(array('err' => 1, 'msg' => '下单前请先登录', 'url' => '/user/login'));
            return false;
        }

        $sub_info = $this->product_model->sub_info($sub_id);
        if(!$sub_info || !$sub_info->is_audit || !$sub_info->is_on_sale) {
            print json_encode(array('err' => 1, 'msg' => '商品不能购买', 'url' => '/index'));
            return false;
        }
        format_product($sub_info);
	format_sub($sub_info);

        if ($sub_info->sale_num != -2 && $sub_info->sale_num <= 0){
            print json_encode(array('err' => 1, 'msg' => '商品不能购买', 'url' => '/index'));
            return false;
        }
        
        
        // 验证收集的数据
        $this->db->trans_begin();
        $user = $this->user_model->lock_user($this->user_id);
        if (!$user) { // 如果后台将用户删除了，后果会这样
            $this->session->destroy_sess();
            print json_encode(array('err' => 1, 'msg' => '您需要重新登录', 'url' => '/user/login'));
            return false;
        }
     
        $update = array(
            'user_id' => $this->user_id,
            'source_id' => SOURCE_ID_WEB,
            'best_time' => '',
            'user_notice' => $remark,
            'invoice_title' => '',
            'invoice_content' => '',
            'product_num' => 0,
            'order_price' => 0,
            'shipping_fee' => 0,
            'paid_price' => 0,
            'user_ip' => real_ip(),
            'shipping_id' => 0,
            'pay_id' => 0,
            'create_date' => $this->time,
            'genre_id' => PRODUCT_COURSE_TYPE
        );
        
        // 开始检查限购数量
        $limit_product = array();

        if($sub_info->limit_day && $sub_info->limit_num){
        
	        if(!isset($limit_product[$sub_info->product_id])){
	            $limit_product[$sub_info->product_id] = array(
	                'product_id' => $sub_info->product_id,
	                'limit_num' => $sub_info->limit_num,
	                'limit_day' => $sub_info->limit_day,
	                'product_name' => $sub_info->product_name,
	                'product_num' => 0,
	            );
	            $limit_product[$sub_info->product_id]['product_num']+=$sub_info->product_num;
	        }
	}

        foreach($limit_product as $product){
            $bounght_num = $this->order_model->get_bought_num($this->user_id, $product['product_id'], $product['limit_day']);
            if($bounght_num+$product['product_num']>$product['limit_num']){
                sys_msg("{$product['product_name']} 超出限购数量，{$product['limit_day']}天内限购{$product['limit_num']}件。", 1);
            }
        }
        // 限购数量检查结束
       
        // 检查库存，并分配虚库数量
        if ($sub_info->sale_num != -2) {
            $sub_info->sale_num = $sub_info->sale_num - $num;
            if ($sub_info->sale_num < 0) {
                $err_carts = $sub_info->product_name . ' ' . $sub_info->color_name . ' ' . $sub_info->size_name;
                sys_msg($err_carts . "\n库存不足", 1);
            }
        }      
                
        $gl_num = min(max($sub_info->gl_num - $sub_info->wait_num, 0), $num);
        $consign_num = $num - $gl_num;
        $sub_info->gl_num -= $gl_num;
        if ($sub_info->consign_num > 0) 
            $sub_info->consign_num -= $consign_num;
        $sub_info->wait_num += $consign_num;
            //更新sub表
        $this->product_model->update_sub(array('gl_num' => $sub_info->gl_num, 'consign_num' => $sub_info->consign_num, 'wait_num' => $sub_info->wait_num), $sub_info->sub_id);

        $arr_order_id = array();

        // 提交订单
        $order = $update;
        $balance = 0;
        $order['product_num'] = $num;
        $order['order_price'] = $sub_info->product_price*$num;            
        $order['pay_id'] = PAY_ID_ALIPAY;
        while (true) {
            $order['order_sn'] = get_order_sn();
            $order_id = $this->cart_model->insert_order($order);
            $err_no = $this->db->_error_number();
            if ($err_no == '1062')
                continue;
            if ($err_no == '0')
                break;
            $this->db->trans_rollback();
            sys_msg('生成订单失败', 1);
        }
        $order['order_id'] = $order_id;
        // 提交订单商品

        $op = array(
            'order_id' => $order_id,
            'product_id' => $sub_info->product_id,
            'color_id' => $sub_info->color_id,
            'size_id' => $sub_info->size_id,
            'product_num' => $num,
            'consign_num' => $consign_num,
            'consign_mark' => $consign_num,
            'market_price' => $sub_info->market_price,
            'shop_price' => $sub_info->shop_price,
            'product_price' => $sub_info->product_price,
            'total_price' => round($num * $sub_info->product_price, 2)
        );
        $op['op_id'] = $this->cart_model->insert_product($op);
        if (!$this->cart_model->assign_trans($order, $op, $sub_info->product_price)) {//分配储位
            $this->db->trans_rollback();
            sys_msg("分配储位失败库存不足.", 1);
        }
        // 报名人信息
        $oc = array('order_id' => $order_id, 
                    'op_id' => $op['op_id'], 
                    'add_date' => $this->time, 
                    'name' => $consignee, 
                    'mobile_phone' => $mobile, 
                    'field_1' => $email, 
                    'field_2' => $address, 
                    'field_3' => $company);
        $this->order_model->insert_order_client($oc);
        // 处理CPS
        $cpstag = $this->input->cookie("cpstag");
        if (isset($cpstag)) {
            $cps_params = @json_decode($cpstag);
            if (isset($cps_params)) {
                $this->load->model('cps_model');
                $this->cps_model->add($order_id, $cps_params);
            }
        }
        $arr_order_id[] = $order_id; 

        $this->db->trans_commit();

        print json_encode(array('err' => 0, 'msg' => '', 'order_id' => implode('-', $arr_order_id)));
    }    
}
