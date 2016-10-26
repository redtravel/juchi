<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Lib_tmall {

    public function parse_shop_num_iid($shop) {
        $max_page = 10;
        $page = 1;
        $num_iids = array();
        while ($page <= $max_page) {
            $matches = array();
            $url = "http://$shop.tmall.com/search.htm?search=y&pageNo=$page";
            $content = file_get_contents($url);

            preg_match_all('/<dl class="item.*" data-id="(\d+)"/', $content, $matches);
            if (empty($matches[1])) {
                break;
            }
            foreach ($matches[1] as $num_iid) {
                $num_iids[] = $num_iid;
            }
            $page ++;
            usleep(500);
        }
        return $num_iids;
    }

    /**
     * 同步记录的同步数据
     * @param type $num_iid
     * @param type $data
     */
    public function update_item_sync_data($num_iid, $data) {
        $CI = & get_instance();
        $CI->load->model('tmall_model');
        $item = $CI->tmall_model->get_item_by_num_iid($num_iid);
        if (empty($item))
            return;

        $update = array();
        if (empty($item->title)) {
            $update['title'] = $data['title'];
        }
        if ($item->shop_price <= 0) {
            $update['shop_price'] = $data['shop_price'];
        }
        if ($item->tmall_price <= 0) {
            $update['tmall_price'] = $data['tmall_price'];
        }
        if ($item->reserve_price <= 0) {
            $update['reserve_price'] = $data['reserve_price'];
        }
        if (empty($item->nick)) {
            $update['nick'] = $data['nick'];
        }
        if (empty($item->image)) {
            if ($data['images']) {
                $update['image'] = $data['images'][0];
            }
        }
        if (empty($item->desc)) {
            $update['desc'] = $data['desc'];
        }
        $data['skus'] = json_encode($data['skus']);
        $data['images'] = json_encode($data['images']);
        $update['sync_data'] = json_encode($data);
        $update['sync_status'] = Tmall_model::SYNC_STATUS_SUCCESS;
        $update['sync_time'] = date('Y-m-d H:i:s');
        $CI->tmall_model->update_item_by_num_iid($update, $num_iid);
    }

    /**
     * 商品入库
     * @param type $num_iid
     * @return boolean
     */
    public function check_item($num_iid) {
        Error::clear();
        $CI = &get_instance();
        $CI->load->model('tmall_model');
        $CI->load->model('product_model');
        $CI->load->model('product_type_model');
        $CI->load->model('color_model');
        $CI->load->model('size_model');
        $CI->load->model('product_type_model');
        $CI->load->model('provider_model');
        $CI->load->model('brand_model');
        $CI->load->model('purchase_batch_model');
        $CI->load->library('upload');
        $CI->load->library('image_lib');
        $CI->load->helper('product');
        $CI->config->load('product');
        $item = $CI->tmall_model->get_item_by_num_iid($num_iid);
        if (empty($item) || $item->check_status != Tmall_model::CHECK_STATUS_INIT) {
            Error::add('商品状态出错。');
            return false;
        }
        $type_id = $item->category_id;
        $type = $CI->product_type_model->filter(array('type_id'=>$type_id));
        if(empty($type)||empty($type[0]->category_id)){
            Error::add('所选前台分类无对应后台分类。');
            return false;
        }
        
        $product = array();
        $product['product_name'] = $item->title;
        $product['product_sn'] = '';
        $product['provider_productcode'] = $num_iid;
        $product['brand_id'] = $item->brand_id;
        $product['category_id'] = $type[0]->category_id;
        $product['provider_id'] = $item->provider_id;
        $product['shop_price'] = fix_price($item->shop_price);
        $product['market_price'] = fix_price($item->reserve_price);
        $product['product_desc'] = trim($item->desc);
        $product['create_admin'] = $CI->admin_id;
        $product['create_date'] = $CI->time;
        $product['product_sex'] = $item->sex;
        $consign_price = fix_price($item->tmall_price);
        if($product['shop_price']<=0 || $product['market_price']<=0 || $consign_price<=0){
            Error::add('价格不完整');
            return false;
        }
        if(empty($product['product_sex'])){
            Error::add('未指定性别');
            return false;
        }
        // 校验provider
        $provider = $CI->provider_model->filter(array('provider_id'=>$product['provider_id']));
        if(empty($provider)){
            Error::add('未指定供应商');
            return false;
        }
        // 校验brand
        $brand = $CI->brand_model->filter(array('brand_id'=>$product['brand_id']));
        if(empty($brand)){
            Error::add('未指定品牌');
            return false;
        }
        
        $CI->db->trans_begin();
        while (($product['product_sn'] = $this->general_product_sn($item->id)) !== FALSE) {
            $product_id = $CI->product_model->insert($product);
            $err_no = $CI->db->_error_number();
            if ($err_no == '1062')
                continue;
            if ($err_no == '0')
                break;
            $product_id = 0; // 生成错误，商品ID归零
        }
        if(empty($product_id)){
            $CI->db->trans_rollback();
            Error::add('生成商品款号失败');
            return false;
        }
        
        if($item->category_id){
            $CI->product_type_model->insert_type_link(array('product_id'=>$product_id, 'type_id'=>$type_id));
        }
        //调价记录
        log_product_price(array(), $product, $product_id);
        
        // 增加采购批次
        $purchase = array(
            'provider_id'=>$product['provider_id'],
            'brand_id' => $product['brand_id'],
            'batch_type' => 0,//类型采购
            'batch_status' => 1, //状态打开
            'is_reckoned' => 0, //未结算
            'related_id' => 0, // 无关联批次
            'is_consign' => 1, // 代销采购
        );
        $purchase_batch = $CI->purchase_batch_model->filter_batch_code($purchase);
        if(empty($purchase_batch)){
            $purchase['batch_code'] = $CI->purchase_batch_model->gen_purchase_batch_code();
            $purchase['batch_name'] = '天猫代销批次';
            $purchase['plan_num'] = 0;
            $purchase['create_admin'] = $CI->admin_id;
            $purchase['create_date'] = $CI->time;
            $purchase['update_admin'] = $CI->admin_id;
            $purchase['update_time'] = $CI->time;    
            $batch_id = $CI->purchase_batch_model->insert($purchase);
        }else{
            $batch_id = $purchase_batch->batch_id;
        }
        if(empty($batch_id)){
            $CI->db->trans_rollback();
            Error::add('生成采购批次失败');
            return false;
        }
        // 增加采购批次商品记录
        $CI->product_model->insert_product_cost(array(
            'product_id' => $product_id,
            'batch_id' => $batch_id,
            'cost_price' => 0,
            'consign_price' => $consign_price,
            'consign_type' => 1,
            'create_admin' => $CI->admin_id,
            'create_date' => $CI->time,
            'provider_id' => $product['provider_id'],
        ));

        // 增加颜色尺码,并插入sub记录
        // sku结构[color_code, color_name, color_img, size_code, size_name, stock]
        $sync_data = json_decode($item->sync_data);
        $skus = json_decode($sync_data->skus, true);
        $sku_adjust = $item->sku_adjust ? json_decode($item->sku_adjust, true) : array();
        $sku_alias = empty($sku_adjust['alias']) ? array() : $sku_adjust['alias'];
        $sku_del = empty($sku_adjust['del']) ? array() : $sku_adjust['del'];
        foreach($skus as $k=>$sku){
            if(in_array($sku[5], $sku_del)) {
                unset($skus[$k]);
                continue;
            }
            if(isset($sku_alias['color_'.$sku[1]])){
                $sku[1] = $sku_alias['color_'.$sku[1]];
            }
            if(isset($sku_alias['size_'.$sku[4]])){
                $sku[4] = $sku_alias['size_'.$sku[4]];
            }
            $skus[$k] = $sku;
        }

        if (empty($skus)) {
            Error::add('没有SKU信息');
            $CI->db->trans_rollback();
            return false;
        }
        $size_arr = array();
        $color_arr = array();
        $color_img_arr = array();
        foreach ($skus as $sku) {
            $color_code = substr($sku[0], strpos($sku[0], ':') + 1);
            $color_name = $sku[1];
            $size_code = substr($sku[3], strpos($sku[3], ':') + 1);
            $size_name = $sku[4];
            $stock = intval($sku[6]);
            $color_img_arr[$color_code] = $sku[2];
            $color_img_arr[$color_code] = ''; //因为色片图不夫则,不采色片图
            if (!isset($color_arr[$color_code])) {
                if (($color = $CI->color_model->filter(array('color_name' => $color_name))) != FALSE) {
                    $color_arr[$color_code] = $color->color_id;
                } else {
                    $color_arr[$color_code] = $CI->color_model->insert(array('color_sn' => $color_code, 'color_name' => $color_name, 'is_use' => 1));
                }
            }
            if (!isset($size_arr[$size_code])) {
                if (($size = $CI->size_model->filter(array('size_name' => $size_name))) != FALSE) {
                    $size_arr[$size_code] = $size->size_id;
                } else {
                    $size_arr[$size_code] = $CI->size_model->insert(array('size_sn' => $size_code, 'size_name' => $size_name, 'is_use' => 1));
                }
            }
            $sub_id = $CI->product_model->insert_sub(array(
                'product_id' => $product_id,
                'color_id' => $color_arr[$color_code],
                'size_id' => $size_arr[$size_code],
                'consign_num' => $stock,
                'provider_barcode' => $num_iid . $color_code . $size_code,
                'is_pic' => 1,
            ));
            $CI->tmall_model->insert_tmall_sku($num_iid, $sku[5], $product_id, $sub_id);
        }
        // 上传商品图片
        $images = json_decode($sync_data->images, true);
        $thumb_arr = $CI->config->item('product_fields');
        $base_dir = CREATE_IMAGE_PATH; //APPPATH.'../public/data/images/gallery/';
        $sub_dir = GALLERY_PATH . intval(($product_id - ($product_id % 100)) / 100);
        if (!file_exists($base_dir . $sub_dir)) {
            mkdir($base_dir . $sub_dir);
        }
        // 将远程文件存到本地
        foreach ($images as $key => $image) {
            $images[$key] = $base_dir . $sub_dir . '/' . $product_id . '_' . $key . '.jpg';
            file_put_contents($images[$key], file_get_contents($image));
            usleep(100);
        }
        foreach ($color_img_arr as $key => $image) {
            if (empty($image))
                continue;
            $color_img_arr[$key] = $base_dir . $sub_dir . '/d' . $product_id . '_' . $key . '.jpg';
            file_put_contents($color_img_arr[$key], file_get_contents($image));
            usleep(100);
        }
        foreach ($color_img_arr as $color_code => $color_image) {
            $color_images = $images;
            if ($color_image) {
                array_unshift($color_images, $color_image);
            }
            $is_default = true;
            foreach ($color_images as $image_source) {
                $update = array();
                $update['product_id'] = $product_id;
                $update['color_id'] = $color_arr[$color_code];
                $update['image_type'] = $is_default ? 'default' : 'part';

                $file_name = $product_id . '_' . $color_arr[$color_code] . '_' . substr($update['image_type'], 0, 1) . '_' . mt_rand(10000, 99999) . '.jpg';
                file_put_contents($base_dir . $sub_dir . '/' . $file_name, file_get_contents($image_source));
                $update['img_url'] = $sub_dir . '/' . $file_name;
                foreach ($thumb_arr as $field => $thumb) {
                    $CI->image_lib->initialize(array(
                        'source_image' => $base_dir . $sub_dir . '/' . $file_name,
                        'quality' => 85,
                        'create_thumb' => TRUE,
                        'maintain_ratio' => FALSE,
                        'thumb_marker' => $thumb['sufix'],
                        'width' => $thumb['width'],
                        'height' => $thumb['height']
                    ));
                    if ($CI->image_lib->resize()) {
                        $CI->image_lib->clear();
                    } else {
                        Error::add('生成缩略图失败');
                        $CI->db->trans_rollback();
                        return false;
                    }
                }

                $update['create_admin'] = $CI->admin_id;
                $update['create_date'] = $CI->time;
                $CI->product_model->insert_gallery($update);
                $is_default = false;
            }
        }
        $CI->db->trans_commit();
        return $product_id;
    }
    
    /**
     * 重新下载商品图片
     * @param type $item
     * @return boolean
     */
    public function reload_gallery($item)
    {
        $CI = & get_instance();
        $CI->config->load('product');
        $CI->load->model('product_model');
        $CI->load->model('tmall_model');
        $CI->load->library('upload');
        $CI->load->library('image_lib');
        $CI->load->helper('product');
        $sync_data = json_decode($item->sync_data);
        $product_id = $item->product_id;
        $skus = json_decode($sync_data->skus, true);
        $sku_adjust = $item->sku_adjust ? json_decode($item->sku_adjust, true) : array();
        $sku_alias = empty($sku_adjust['alias']) ? array() : $sku_adjust['alias'];
        $sku_del = empty($sku_adjust['del']) ? array() : $sku_adjust['del'];
        foreach($skus as $k=>$sku){
            if(in_array($sku[5], $sku_del)) {
                unset($skus[$k]);
                continue;
            }
            if(isset($sku_alias['color_'.$sku[1]])){
                $sku[1] = $sku_alias['color_'.$sku[1]];
            }
            if(isset($sku_alias['size_'.$sku[4]])){
                $sku[4] = $sku_alias['size_'.$sku[4]];
            }
            $skus[$k] = $sku;
        }

        if (empty($skus)) {
            Error::add('没有SKU信息');
            return false;
        }
        $gallery_arr = array();
        $color_arr = array();
        $color_img_arr = array();
        foreach ($skus as $sku) {
            $color_name = $sku[1];     
            $color_img = $sku[2];
            $color_img_arr[$color_name] = $color_img;
        }
        foreach($CI->tmall_model->get_product_color_list($product_id) as $color){
            $color_arr[$color->color_name] = $color->color_id;
        }
        foreach($CI->tmall_model->get_product_gallery_list($product_id) as $gallery){
            if(!isset($gallery_arr[$gallery->color_id])){
                $gallery_arr[$gallery->color_id] = array();
            }
            $gallery_arr[$gallery->color_id][] = $gallery;
        }
        // 上传商品图片
        $images = json_decode($sync_data->images, true);
        $thumb_arr = $CI->config->item('product_fields');
        $base_dir = CREATE_IMAGE_PATH; //APPPATH.'../public/data/images/gallery/';
        $sub_dir = GALLERY_PATH . intval(($product_id - ($product_id % 100)) / 100);
        if (!file_exists($base_dir . $sub_dir)) {
            mkdir($base_dir . $sub_dir);
        }
        // 将远程文件存到本地
        foreach ($images as $key => $image) {
            $images[$key] = $base_dir . $sub_dir . '/' . $product_id . '_' . $key . '.jpg';
            file_put_contents($images[$key], file_get_contents($image));
            usleep(100);
        }
        foreach ($color_img_arr as $key => $image) {
            if (empty($image)){
                continue;
            }                
            $color_img_arr[$key] = $base_dir . $sub_dir . '/d' . $product_id . '_' . $key . '.jpg';
            file_put_contents($color_img_arr[$key], file_get_contents($image));
            usleep(100);
        }
        $CI->db->trans_begin();
        foreach ($color_img_arr as $color_name => $color_image) {
            if(!isset($color_arr[$color_name])){
                continue;
            }
            $color_id = $color_arr[$color_name];
            $color_images = $images;
            if ($color_image) {
                array_unshift($color_images, $color_image);
            }
            // 删除原有图像
            if(isset($gallery_arr[$color_id])){
                foreach($gallery_arr[$color_id] as $gallery){
                    @unlink($base_dir.$gallery->img_url);
                    foreach($thumb_arr as $thumb){
                       @unlink($base_dir.$gallery->img_url.'.'.$thumb['width'].'x'.$thumb['height'].'.jpg'); 
                    }
                    $CI->product_model->delete_gallery($gallery->image_id);
                }
            }
            $is_default = true;
            foreach ($color_images as $image_source) {
                $update = array();
                $update['product_id'] = $product_id;
                $update['color_id'] = $color_id;
                $update['image_type'] = $is_default ? 'default' : 'part';

                $file_name = $product_id . '_' . $color_arr[$color_name] . '_' . substr($update['image_type'], 0, 1) . '_' . mt_rand(10000, 99999) . '.jpg';
                file_put_contents($base_dir . $sub_dir . '/' . $file_name, file_get_contents($image_source));
                $update['img_url'] = $sub_dir . '/' . $file_name;
                foreach ($thumb_arr as $field => $thumb) {
                    $CI->image_lib->initialize(array(
                        'source_image' => $base_dir . $sub_dir . '/' . $file_name,
                        'quality' => 85,
                        'create_thumb' => TRUE,
                        'maintain_ratio' => FALSE,
                        'thumb_marker' => $thumb['sufix'],
                        'width' => $thumb['width'],
                        'height' => $thumb['height']
                    ));
                    if ($CI->image_lib->resize()) {
                        $CI->image_lib->clear();
                    } else {
                        Error::add('生成缩略图失败');
                        $CI->db->trans_rollback();
                        return false;
                    }
                }

                $update['create_admin'] = $CI->admin_id;
                $update['create_date'] = $CI->time;
                $CI->product_model->insert_gallery($update);
                $is_default = false;
            }
        }
        $CI->db->trans_commit();
        return true;
    }

    /**
     * 生成商品款号
     * @param type $num_iid
     */
    protected function general_product_sn($id) {
        static $times = 0;
        $prefix = "TMALBCDEFGHIJKNOPQRSUVWXYZ";
        if($times>=strlen($prefix)) return false;
        $product_sn = $prefix[$times].str_pad($id, 9, '0', STR_PAD_LEFT);
        $times ++;
        return $product_sn;
    }

}
