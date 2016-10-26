<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Tmall Extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
	$this->time = date('Y-m-d H:i:s');
    }

    public function index() {
        auth('tmall_spider');
        $this->load->model('provider_model');
        $this->load->vars('all_provider', $this->provider_model->all_provider());
        $this->load->view('tmall/index.php');
    }

    public function item_list() {
        $filter = array();
        $page = max(intval($this->input->post('page')), 1);
        $rows = intval($this->input->post('rows'));
        $sync_status = trim($this->input->get_post('sync_status'));
        $check_status = trim($this->input->get_post('check_status'));
        $provider_id = intval($this->input->get_post('provider_id'));
        if ($rows < 1)
            $rows = 50;
        $filter['sync_status'] = $sync_status ? ($sync_status == 't' ? 1 : 0) : null;
        $filter['check_status'] = $check_status ? ($check_status == 't' ? 1 : 0) : null;
        $filter['provider_id'] = $provider_id;
        $this->load->model('tmall_model');
        $res = $this->tmall_model->item_list_page($page, $rows, $filter);
        foreach($res['l'] as &$row){
            // 判断是否有sku
            $row->has_sku = TRUE;
            if($row->sync_data){
                $sync_data = json_decode($row->sync_data);
                $skus = json_decode($sync_data->skus,true);
                $row->has_sku = !empty($skus);
            }            
        }
        unset($row);
        $list = array();
        print json_encode(array('total' => $res['c'], 'rows' => $res['l']));
    }
    
    /**
     * 插入待同步数据
     */
    public function insert() {
        auth('tmall_spider');
        ini_set('memory_limit', '256M');
        set_time_limit(0);
        $this->load->library('lib_tmall');
        $this->load->model('tmall_model');
        $shop = trim($this->input->get_post('shop'));
        $goods = trim($this->input->post('goods'));
        $provider_id = intval($this->input->post('provider_id'));
        $num_iids = array();
        if ($shop) {
            // 抓取页面获取
            $num_iids = $this->lib_tmall->parse_shop_num_iid($shop);
        } else {
            $arr = explode("\n", $goods);
            $num_iids = array_filter(array_map('trim', $arr));
        }
        foreach ($num_iids as $num_iid) {
            $this->tmall_model->insert_num_iid($num_iid, $provider_id);
        }  
        print json_encode(array('error'=>0));
    }
    
    /**
     * 接受同步数据
     */
    public function sync_item()
    {
        $this->load->library('lib_tmall');
        $this->load->model('tmall_model');
        $data = array();
        $data['num_iid'] = trim($this->input->post('num_iid'));
        $data['title'] = trim($this->input->post('title'));
        $data['tmall_price'] = floatval($this->input->post('price'));
        $data['reserve_price'] = floatval($this->input->post('reserve_price'));
        $data['shop_price'] = min(intval($data['tmall_price'] * 1.25), intval($data['tmall_price']+40));
        $data['sellerId'] = trim($this->input->post('sellerId'));
        $data['shopId'] = trim($this->input->post('shopId'));
        $data['nick'] = trim($this->input->post('nick'));
        $data['skus'] = $this->input->post('skus');
        $data['brand_id'] = trim($this->input->post('brand_id'));
        $data['brand_name'] = json_decode('"'.$this->input->post('brand_name').'"');
        $data['category'] = trim($this->input->post('category'));
        $data['images'] = $this->input->post('images');
        $data['desc'] = $this->input->post('desc');
        if($data['desc']) {
            $desc = file_get_contents($data['desc']);
            $desc = mb_convert_encoding($desc, 'UTF-8', 'GBK');
            $data['desc'] = mb_substr(trim($desc), 10, -2);
        }
        $item = $this->tmall_model->get_item_by_num_iid($data['num_iid']);
        if (empty($data['skus']) && !empty($item) && $item->sync_status == Tmall_model::SYNC_STATUS_INIT && $item->check_status == Tmall_model::CHECK_STATUS_INIT) {
            $this->tmall_model->delete_item($data['num_iid']);
        }else{
            $this->lib_tmall->update_item_sync_data($data['num_iid'], $data);
        }        
        print json_encode(array('error'=>0));
    }
    
    /**
     * 取下次要取的num_iid
     */
    public function next_num_iid() {
        $this->load->model('tmall_model');
        $item = $this->tmall_model->get_num_iid_to_sync();
        $data = json_encode(array('num_iid' => $item ? $item->num_iid : 0));
        print $data;
    }
    
    /**
     * 下一个待同步的记录的num_iid
     */
    public function next_stock_num_iid()
    {
        $this->load->model('tmall_model');
        $item = $this->tmall_model->get_num_iid_to_sync_stock();
        $data = json_encode(array('num_iid' => $item ? $item->num_iid : 0));
        print $data;
    }
    
    public function edit($num_iid)
    {
        auth('tmall_spider');
        $this->load->model('tmall_model');
        $this->load->model('product_type_model');
        $this->load->model('provider_model');
        $this->load->model('brand_model');
        $this->load->model('category_model');
        $this->load->library('ckeditor');
        $this->load->helper('category');
        $item = $this->tmall_model->get_item_by_num_iid($num_iid);
        if(empty($item))
        {
            print '未找到商品';
            return;
        }
        if($item->sync_status!=Tmall_model::SYNC_STATUS_SUCCESS){
            print '商品未同步';
            return;
        }
        $item->sync_data = $item->sync_data ? json_decode($item->sync_data, true) : array();
        $cat_name = $this->tmall_model->get_category_name_by_cid($item->sync_data['category']);
        $item->sync_data['category'] = $cat_name ? $cat_name : $item->sync_data['category'];
        $skus = array();
        $sku_adjust = $item->sku_adjust ? json_decode($item->sku_adjust, true) : array();
        $sku_alias = empty($sku_adjust['alias'])?array():$sku_adjust['alias'];
        $sku_del = empty($sku_adjust['del'])?array():$sku_adjust['del'];
        foreach(json_decode($item->sync_data['skus'],true) as $sku)
        {
            if (in_array($sku[5], $sku_del)) {
                continue;
            }
            if (!isset($skus[$sku[0]])) {
                $skus[$sku[0]] = array('color_code' => $sku[0], 'color_name' => $sku[1], 'color_img' => $sku[2], 'size_list' => array(),
                    'color_alias' => isset($sku_alias['color_'.$sku[1]]) ? $sku_alias['color_'.$sku[1]] : $sku[1],
                );
            }
            $skus[$sku[0]]['size_list'][] = array('size_code' => $sku[3], 'size_name' => $sku[4], 'sku_id' => $sku[5], 'stock' => $sku[6],
                'size_alias' => isset($sku_alias['size_' . $sku[4]]) ? $sku_alias['size_' . $sku[4]] : $sku[4],
            );
        }
        $item->skus = $skus;
        $item->sku_alias = $sku_alias;
        $item->sku_del = $sku_del;
        $this->load->vars('item', $item);
        $this->load->vars('all_provider', $this->provider_model->all_provider());
        $this->load->vars('all_brand', $this->brand_model->all_provider_brand($item->provider_id));
        //$this->load->vars('all_category',category_tree($this->category_model->all_category()));
        $this->load->vars('all_type', category_tree($this->product_type_model->filter(array()), 0, 'type_id'));

        $this->load->view('tmall/edit.php');
    }
    
    public function save($num_iid=0, $ruku=false)
    {
        auth('tmall_spider');
        ini_set('memory_limit','512M');
        set_time_limit(0);
        $num_iid = trim($num_iid);
        $this->load->model('tmall_model');
        $this->load->model('product_model');
        $this->load->library('lib_tmall');
        $item = $this->tmall_model->get_item_by_num_iid($num_iid);
        if(empty($item))
        {
            print json_encode(array('msg'=>'商品未找到','error'=>1));
            return;
        }
        $update = array();
        $update['title'] = trim($this->input->post('title'));
        $update['shop_price'] = floatval($this->input->post('shop_price'));
        $update['tmall_price'] = floatval($this->input->post('tmall_price'));
        $update['reserve_price'] = floatval($this->input->post('reserve_price'));
        $update['provider_id'] = intval($this->input->post('provider_id'));
        $update['brand_id'] = intval($this->input->post('brand_id'));
        $update['category_id'] = intval($this->input->post('category_id'));
        $update['sex'] = intval($this->input->post('sex'));
        $update['desc'] = trim($this->input->post('desc'));
        
        $sku_adjust = array('alias'=>array(), 'del'=>array());
        $sku_alias = $this->input->post('sku_alias');
        $sku_del = $this->input->post('sku_del');
        $sku_adjust['del'] = array_filter(array_map('trim',explode('|', $sku_del)));
        foreach(array_filter(array_map('trim', explode('|||', $sku_alias))) as $alias){
            $arr = explode('$$$', $alias);
            if(count($arr)==2){
                $sku_adjust['alias'][$arr[0]] = $arr[1];
            }
        }
        $update['sku_adjust'] = json_encode($sku_adjust);
        // 检查审核入库的商品是否已被删除
        if($item->product_id){
            $product = $this->product_model->filter(array('product_id'=>$item->product_id));
            if(empty($product)){
                $update['product_id'] = 0;
                $update['check_status'] = Tmall_model::CHECK_STATUS_INIT;
                $update['check_time'] = null;
                $item->product_id = 0;
                $item->check_status = Tmall_model::CHECK_STATUS_INIT;
            }
        }
        
        $this->tmall_model->update_item_by_num_iid($update, $num_iid);
        if(!$ruku){
            print json_encode(array('msg'=>'', 'error'=>0));
            return;
        }
        if($item->check_status == Tmall_model::CHECK_STATUS_SUCCESS){
            print json_encode(array('msg'=>'修改已保存，但商品已审核入库，不能重复操作。','error'=>1));
            return;
        }
        
        // 审核入库
        if(($product_id = $this->lib_tmall->check_item($num_iid))>0){
            $this->tmall_model->update_item_by_num_iid(array(
                'product_id'=>$product_id, 'check_status'=>Tmall_model::CHECK_STATUS_SUCCESS, 'check_time'=>$this->time), $num_iid);
            print json_encode(array('msg'=>'', 'error'=>0));
        }else{
            print json_encode(array('msg'=>'修改已保存，审核入库操作失败:'.implode(';', Error::all()),'error'=>1));
        }        
    }
    
    /**
     * 删除一条记录
     */
    function delete()
    {
        auth('tmall_spider');
        $num_iid = trim($this->input->post('num_iid'));
        $this->load->model('tmall_model');
        $item = $this->tmall_model->get_item_by_num_iid($num_iid);
        if(empty($item)){
            print json_encode(array('err'=>0));
            return;
        }
        if($item->check_status==Tmall_model::CHECK_STATUS_SUCCESS){
            print json_encode(array('err'=>1,'msg'=>'已审核入库,不能删除'));
            return;
        }
        $this->tmall_model->delete_item($num_iid);
        print json_encode(array('err'=>0));
    }
    
    
    /**
     * 重新生成缩略图
     */
    public function repare($start = 0)
    {
        $this->load->library('image_lib');
        $this->load->helper('product');
        $this->config->load('product');
        $base_dir = CREATE_IMAGE_PATH;

        $page_size = 100;
        $thumb_arr = $this->config->item('product_fields');
        while (true) {
            $sql = "select * from ty_product_gallery where image_id>$start order by image_id asc limit $page_size";
            $query = $this->db->query($sql);
            $result = $query->result();
            if (empty($result)) {
                echo 'OK';
                return;
            }
            foreach($result as $row)
            {
                $start = $row->image_id;
                print_r($row->image_id.'<br/>');
                $img = str_replace('_318318.jpg','.jpg',$row->img_318_318);
                if(!file_exists($base_dir.$img)) continue;
                foreach ($thumb_arr as $field => $thumb) {
                    $this->image_lib->initialize(array(
                        'source_image' => $base_dir . $img,
                        'quality' => 85,
                        'create_thumb' => TRUE,
                        'maintain_ratio' => FALSE,
                        'thumb_marker' => $thumb['sufix'],
                        'width' => $thumb['width'],
                        'height' => $thumb['height']
                    ));
                    if ($this->image_lib->resize()) {
                        $this->image_lib->clear();
			@unlink($base_dir.substr($img,0,-4)."_140140.jpg");
			@unlink($base_dir.substr($img,0,-4)."_175175.jpg");
			@unlink($base_dir.substr($img,0,-4)."_220220.jpg");
			@unlink($base_dir.substr($img,0,-4)."_318318.jpg");
			@unlink($base_dir.substr($img,0,-4)."_4040.jpg");
			@unlink($base_dir.substr($img,0,-4)."_418418.jpg");
			@unlink($base_dir.substr($img,0,-4)."_4848.jpg");
			@unlink($base_dir.substr($img,0,-4)."_5858.jpg");
			@unlink($base_dir.substr($img,0,-4)."_760760.jpg");
			@unlink($base_dir.substr($img,0,-4)."_850850.jpg");
			@unlink($base_dir.substr($img,0,-4)."_8585.jpg");
                    } else {
                        echo "生成缩略图失败".$img."<br/>";
                        
                    }
                }
                echo $start."<br/>";
                $this->db->update('product_gallery',array('img_url'=>$img), array('image_id'=>$row->image_id));
                
            }
        }
    }
    
    /**
     * 抓取商品描述
     */
    public function fetch_desc($life = 600) {
        ini_set('memory_limit', '512M');
        set_time_limit(0);
        $start = time();
        $this->load->model('product_model');
        $this->load->model('tmall_model');
        while (true) {
            if(time()-$start>$life){return;} //超时退出
            $item = $this->tmall_model->get_item_to_sync_desc();
            if ($item->desc_time) { // 所有都已同步完成了
                break;
            }
            $product = $this->product_model->filter(array('product_id' => $item->product_id));
            if (empty($product)) {
                $this->tmall_model->update_item_by_num_iid(array('check_status'=>  Tmall_model::CHECK_STATUS_INIT, 'product_id'=>0), $item->num_iid);
                continue;
            }
            $product_id = $product->product_id;
            // 创建图片存储目录
            $base_dir = CREATE_IMAGE_PATH; //APPPATH.'../public/data/images/product_desc/';
            $sub_dir = PRODUCT_DESC_PATH . intval(($product_id - ($product_id % 100)) / 100);
            if (!file_exists($base_dir . $sub_dir)) {
                mkdir($base_dir . $sub_dir);
            }
            if (!file_exists($base_dir . $sub_dir . '/' . $product_id)) {
                mkdir($base_dir . $sub_dir . '/' . $product_id);
            }

            $product_desc = strip_slashes($product->product_desc);
            $matches = array();
            $matches_bg = array();
            preg_match_all('/<img.*src="(.*)"/isU', $product_desc, $matches);
            preg_match_all('/background="(.*)"/isU', $product_desc, $matches_bg);
            if (empty($matches) || empty($matches[1])) {
                $matches = array(1 => array());
            }
            if(!empty($matches_bg) && !empty($matches_bg[1])){
                foreach($matches_bg[1] as $img){
                    $matches[1][] = $img;
                }
            }
            $arr_from = array();
            $arr_to = array();
            foreach ($matches[1] as $img) {
                if (strstr($img, SITE_DOMAIN) !== FALSE) {
                    continue;
                }
                try {
                    $file = file_get_contents($img);
                    if ($file === FALSE) {
                        continue;
                    }
                    $file_name_new = md5($img) . '.jpg';
                    file_put_contents($base_dir . $sub_dir . '/' . $product_id . '/' . $file_name_new, $file);
                } catch (Exception $e) {
                    continue;
                }
                usleep(250);
                $arr_from[] = $img;
                $arr_to[] = img_url($sub_dir . '/' . $product_id . '/' . $file_name_new);
            }
            if ($arr_from) {
                $product_desc = str_replace($arr_from, $arr_to, $product_desc);
                $this->product_model->update(array('product_desc' => $product_desc), $product_id);
            }
            $this->tmall_model->update_item_by_num_iid(array('desc_time'=> $this->time), $item->num_iid);
            print $product_id.'|';
        }
    }

    
    /**
     * 同步库存
     */
    public function sync_stock()
    {
        $num_iid = trim($this->input->post('num_iid'));
        $skus = $this->input->post('skus');
        $this->load->model('tmall_model');
        $this->load->model('product_model');
        $item = $this->tmall_model->get_item_by_num_iid($num_iid);
        if(empty($item) || $item->check_status!=Tmall_model::CHECK_STATUS_SUCCESS){
            print json_encode(array());
            return;
        }
        $product_id = $item->product_id;
        $tmall_skus = index_array($this->tmall_model->get_tmall_skus(array('product_id'=>$product_id)),'sku_id');
 
        /*========================================
         *  更新现有库存
         */
        foreach($skus as $sku){
            list($sku_id, $stock) = explode('|', $sku);
            if(!isset($tmall_skus[$sku_id])){
                continue;
            }
            $tmall_sku = $tmall_skus[$sku_id];
            $this->product_model->update_sub(array('consign_num' => max($stock - $tmall_sku->wait_num, 0)), $tmall_sku->sub_id);
            unset($tmall_skus[$sku_id]);
        }
        /*=========================================
         * 其它sku置为0
         */
        if($tmall_skus){
            $this->product_model->update_sub(array('consign_num' => 0), array_keys(index_array($tmall_skus, 'sub_id')));
        }        
        $this->tmall_model->update_item_by_num_iid(array('stock_time'=>$this->time), $num_iid);
        print(json_encode(array('err'=>0)));
    }

}
