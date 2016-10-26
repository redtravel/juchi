<?php
/**
* 批量导入
*/
class Import extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->admin_id = $this->session->userdata('admin_id');
		if (!$this->admin_id) {
			redirect('index/login');
		}
		$this->time = date('Y-m-d H:i:s');
		ini_set('max_execution_time', '0');
	}

	public function index()
	{
		auth(array('import_img','import_sub','import_pro','import_purchase','import_pro_cost','import_pro_price'));
		$this->load->view('import/index');
	}

	public function upload()
	{
		$this->load->library('upload');
		$data_type = trim($this->input->post('data_type'));
		//添加文件上传格式限制
		if($_FILES["data_file"]["type"] != 'text/xml') {
			sys_msg("请上传XML格式的文件", 1 ,array() ,FALSE);
		}
		$upload_path="";
		$file_name = "";
		switch ($data_type) {
			case 'product':
				auth('import_pro');
				$upload_path = APPPATH."../public/import/product/";
				$file_name = 'product.xml';
				break;
			case 'color_size':
				auth('import_sub');
				$upload_path = APPPATH."../public/import/color_size/";
				$file_name = 'color_size.xml';
				break;
			case 'product_sub':
				auth('import_pro_sub');
				$upload_path = APPPATH."../public/import/product_sub/";
				$file_name = 'product_sub.xml';
				break;
			case 'purchase':
				auth('import_purchase');
				$upload_path = IMPORT_PATH_PURCHASE;
				$file_name = 'purchase.xml';
				break;
			case 'consign':
				auth('import_consign');
				$upload_path = APPPATH."../public/import/consign/";
				$file_name = 'consign.xml';
				break;
			case 'product_cost':
				auth('import_pro_cost');
				$upload_path = IMPORT_PATH_PRO_COST;
				$file_name = 'product_cost.xml';
				break;

			case 'product_price':
				auth('import_pro_price');
				$upload_path = IMPORT_PATH_PRO_PRICE;
				$file_name = 'product_price.xml';
				break;
			    

                        case 'provider_barcode':
				auth('import_provider_barcode');
				$upload_path = IMPORT_PATH_PROVIDER_BARCODE;
				$file_name = 'provider_barcode.xml';
				break;

			default:
				sys_msg('请选择数据类型', 1 ,array() ,FALSE);;
				break;
		}
		$this->upload->initialize(array(
			'upload_path' => $upload_path,
			'file_name' => $file_name,
			'allowed_types' => '*',
			'overwrite' => TRUE
			));
		if (!$this->upload->do_upload('data_file')) {
			sys_msg($this->upload->display_errors(), 1 ,array() ,FALSE);
		}

		sys_msg('上传成功,请执行导入操作', 0, array(array('text'=>'返回', 'href'=>'import'),FALSE));
	}
        
	public function product()
	{
		auth('import_pro');
		$this->load->model('product_model');
		$this->load->model('category_model');
		$this->load->model('brand_model');
		$this->load->model('register_model');
		$this->load->model('style_model');
		$this->load->model('season_model');
		$this->load->model('flag_model');
		$this->load->model('cooperation_model');
		$this->load->model('provider_model');
		$this->load->model('shop_model');
		$this->load->model('carelabel_model');
		$this->load->model('product_imp_list_model');
		$this->load->helper('category');
		$this->load->helper('product');
		$this->config->load('product');

		$file = APPPATH.'../public/import/product/product.xml';
		if (!file_exists($file)) {
			sys_msg('数据库文件不存在', 1 ,array() ,FALSE);
		}
		$content = file_get_contents($file);
		//$content = preg_replace('/&.*;/','',$content);
		$dom = new SimpleXMLElement($content);
		$dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
		$rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
		//'product_name','parent_category_name','category_name','cost_price','consign_price','consign_type_name','consign_rate','cooperation_name','provider_name','unit_name','goods_carelabel',
		$keys = array('product_sn','cate_code','brand_name','market_price','shop_price','flag_name','provider_code','register_no','shop_code');
		$success_records = array();
		$error_records = array();

		// $category_rs = category_tree($this->category_model->all_category());
		$all_brand = index_array($this->brand_model->all_brand(),'brand_name');
		//$all_style = index_array($this->style_model->all_style(),'style_name');
		// $all_season = index_array($this->season_model->all_season(), 'season_name');
		$all_flag = index_array($this->flag_model->all_flag(), 'flag_name');
		//$all_cooperation = index_array($this->cooperation_model->all_cooperation(),'cooperation_name');
		$all_provider = index_array($this->provider_model->all_provider(), 'provider_code');

		// $shop_code = index_array($this->provider_model->all_provider(), 'shop_code');

		//$all_carelabel = index_array($this->carelabel_model->all_carelabel(),'carelabel_id');
		$all_category = index_array($this->category_model->all_category(array('is_use'=>1)),'cate_code');

		$all_register = index_array($this->register_model->all_register(),'register_no');
//		$all_category = array();
//		$all_category_1 = array();
//		$all_category_2 = array();
//		foreach ($category_rs as $l1) {
//			$all_category_1[$l1->category_name] = $l1;
//			foreach ($l1->sub_items as $l2) {
//				$all_category_2[$l2->category_name] = $l2;
//				$all_category[$l1->category_name.'|'.$l2->category_name] = $l2;
//			}
//		}
		//$all_consign_type = array('买断'=>0, '固定'=>1, '浮动'=>2);
		$all_product_ids = array();
		$all_product_sns = array();
		$all_age = $this->config->item('product_age');
		$product_id_list = "";
		$imp_list_id = $this->product_imp_list_model->insert(array("product_id_list"=>$product_id_list,"status" =>"02","create_admin"=>$this->admin_id,"create_date"=>$this->time));
			
		try{
		$this->db->trans_begin();
					// var_dump($product);
           
		$rows = array_filter($rows);

		foreach ($rows as $key => $row) {
			// if ($key == 0 || $key == 1) continue;
			if ($key == 0 ) continue;
			$product = array();
			$result = array();
			foreach ($row as $cell) {
                preg_match("/<Cell ss:Index=\"(\d+)\"/i", $cell->asXML(), $match);
                if (isset($match[1])) {
                    $product[intval($match[1])-1] = strval($cell->Data);
                    $result[intval($match[1])-1] = strval($cell->Data);
                } else {
                    $product[] = trim(strval($cell->Data));
                    $result[] = trim(strval($cell->Data));

                }
				//$product[] = trim(strval($cell->Data));
				//$result[] = trim(strval($cell->Data));
			}
            //if (!isset($product[0]) || empty($product[0])) continue;
            // $product[0] = '';
            // $result[0] = '';

            ksort($product);
            ksort($result);
			$product = array_pad($product, count($keys), '');
			$product = array_slice($product, 0, count($keys));
			$product = array_combine($keys,$product);
			
			$result = array_pad($result, count($keys), '');
			$result = array_slice($result, 0, count($keys));
			$result = array_combine($keys,$result);

			// //分类
			if (!isset($all_category[$product['cate_code']])) {
				$this->_record_error($error_records, $result, '分类不存在');
				continue;
			}
			
			if($all_category[$product['cate_code']]->parent_id == 0) {
				$this->_record_error($error_records, $result, '不是二级分类');
				continue;
			}
			$product['category_id'] = $all_category[$product['cate_code']]->category_id;
			unset($product['cate_code']);
                        
			// if (empty($product['provider_productcode'])) {
			// 	$this->_record_error($error_records, $result, '供应商货号未填写');
			// 	continue;
			// }
			

//			if(empty($product['parent_category_name']) || !isset($all_category_1[$product['parent_category_name']])) {
//				$this->_record_error($error_records, $result, '一级分类不存在');
//				continue;
//			}
//			if(empty($product['category_name']) || !isset($all_category_2[$product['category_name']])) {
//				$this->_record_error($error_records, $result, '二级分类不存在');
//				continue;
//			}
//			if (!isset($all_category[$product['parent_category_name'].'|'.$product['category_name']])) {
//				$this->_record_error($error_records, $result, '分类不存在');
//				continue;
//			}
//			$category = $all_category[$product['parent_category_name'].'|'.$product['category_name']];
//			$product['category_id'] = $category->category_id;
//			unset($product['parent_category_name'],$product['category_name']);			

			//品牌
			if (!isset($all_brand[$product['brand_name']])) {
				$this->_record_error($error_records, $result, '品牌不存在');
				continue;
			}
			$product['brand_id'] = $all_brand[$product['brand_name']]->brand_id;
			unset($product['brand_name']);
			
			//价格
			if(!is_numeric($product['market_price'])) {
				$this->_record_error($error_records, $result, '市场价必须为数字');
				continue;
			}
			if(!preg_match('/^\\d+$/',$product['shop_price'])){
				$this->_record_error($error_records, $result, '售价必须为正整数或0');
				continue;
			}

			//风格
//			if (!isset($all_style[$product['style_name']])) {
//				$this->_record_error($error_records, $result, '风格名称不存在');
//				continue;
//			}
//			$product['style_id'] = $all_style[$product['style_name']]->style_id;
//			unset($product['style_name']);

			//季节
			// if (!isset($all_season[$product['season_name']])) {
			// 	$this->_record_error($error_records, $result, '季节不存在');
			// 	continue;
			// }
			// $product['season_id'] = $all_season[$product['season_name']]->season_id;
			// unset($product['season_name']);

			//国旗			
			if (!isset($all_flag[$product['flag_name']])) {
				$this->_record_error($error_records, $result, '国家不存在');
				continue;
			}
			$product['flag_id'] = $all_flag[$product['flag_name']]->flag_id;
			unset($product['flag_name']);

			//店铺SN @baolm
			/*if (!isset($all_shop[$product['shop_sn']])) {
				$this->_record_error($error_records, $product, '店铺SN不存在');
				continue;
			}
			$product['shop_id'] = $all_shop[$product['shop_sn']]->shop_id;
			unset($product['shop_sn']);
			*/

			//合作方式			
			/*if (!isset($all_cooperation[$product['cooperation_name']])) {
				$this->_record_error($error_records, $product, '合作方式不存在');
				continue;
			}
			$product['cooperation_id'] = $all_cooperation[$product['cooperation_name']]->cooperation_id;
			unset($product['cooperation_name']);
			*/

			//供应商			
			if (!isset($all_provider[$product['provider_code']])) {
				$this->_record_error($error_records, $result, '供应商不存在');
				continue;
			}
			$product['provider_id'] = $all_provider[$product['provider_code']]->provider_id;
			unset($product['provider_code']);	

			//注册证号	
			if (!isset($all_register[$product['register_no']])) {
				$this->_record_error($error_records, $result, '注册证号不存在');
				continue;
			}
			$product['register_code_id'] = $all_register[$product['register_no']]->id;
			unset($product['register_no']);

			//店铺			
			if (!isset($all_provider[$product['shop_code']])) {
				$this->_record_error($error_records, $result, '店铺不存在');
				continue;
			}
			$product['shop_id'] = $all_provider[$product['shop_code']]->provider_id;
			unset($product['shop_code']);

			//代销方式
			/*if (!isset($all_consign_type[$product['consign_type_name']])) {
				$this->_record_error($error_records, $product, '代销方式不存在');
				continue;
			}
			$product['consign_type'] = $all_consign_type[$product['consign_type_name']];
			unset($product['consign_type_name']);
			*/
//			if (empty($product['product_name'])) {
//				$this->_record_error($error_records, $result, '产品名称未填写');
//				continue;
//			}

			//$product['goods_carelabel'] = str_replace('|',',',$product['goods_carelabel']);
			// $product['product_year'] = intval($product['product_year']);
			// $product['product_month'] = intval($product['product_month']);
			
			//日期
			// if(strlen($product['product_year']) != 4 || $product['product_year'] > date('Y')) {
			// 	$this->_record_error($error_records, $result, '年份有误');
			// 	continue;
			// }
			// if($product['product_month'] < 1 || $product['product_month'] > 12) {
			// 	$this->_record_error($error_records, $result, '月份有误');
			// 	continue;
			// }

			if(empty($product['product_sn'])){
			    /*$tmp_sn = gen_product_sn();
			    for($i=0;$i<100;$i++){
				if(in_array($tmp_sn,$all_product_sns)){
				    $tmp_sn = gen_product_sn();
				    continue;
				}
				break;
			    }*/
                            
                $tmp_sn = $this->product_model->gen_p_sn($all_brand[$product['brand_name']]->brand_initial, $all_category[$product['category_id']]->cate_code);
                if (empty($tmp_sn)){
                    $this->_record_error($error_records, $result, '没有可用商品款号，请联系技术部');
                    break;
                }
			    $product['product_sn'] = $tmp_sn;
			    if(in_array($tmp_sn,$all_product_sns)){
				    $this->_record_error($error_records, $result, '商品款号重复');
				    continue;
			    }
			}else{
			    $old_product = $this->product_model->filter(array('product_sn'=>$product['product_sn']));
			    if ($old_product) {
				    $this->_record_error($error_records, $result, '商品款号重复');
				    continue;
			    }
			}
			$all_product_sns[] = $product['product_sn'];
                        
			//保养ID
//			$carelabel_ids = explode(',',$product['goods_carelabel']);
//			$flag = false;
//			if(!empty($carelabel_ids)) {
//				foreach ( $carelabel_ids as $id ) {
//					if (!isset($all_carelabel[$id])) {
//						$flag = true;
//						break;
//					}
//				}
//			}
//			if($flag) {
//				$this->_record_error($error_records, $result, '保养ID不存在');
//				continue;
//			}
			$product['market_price'] = max(fix_price($product['market_price']),0);
			$product['shop_price'] = max(fix_price($product['shop_price']),0);
			//$product['cost_price'] = max(fix_price($product['cost_price']),0);
			//$product['consign_price'] = max(fix_price($product['consign_price']),0);
			//$product['consign_type'] = max(fix_price($product['consign_type']),0);
			//$product['consign_rate'] = max(fix_price($product['consign_rate']),0);
			$product['create_admin'] = $this->admin_id;
			$product['create_date'] = $this->time;
			/*if($product['cooperation_id']==1){
				if($product['consign_type']!=0){
					$this->_record_error($error_records, $product, '代销方式与合作方式不相符');
					continue;
				}
				if($product['consign_price']>0 || $product['consign_rate']>0){
					$this->_record_error($error_records, $product, '买断方式下代销价和代销率都应为0');
					continue;
				}
			}else{
				if($product['consign_type']==0){
					$this->_record_error($error_records, $product, '代销方式与合作方式不相符');
					continue;
				}
				if($product['cost_price']>0){
					$this->_record_error($error_records, $product, '代销方式下成本价应为0');
					continue;
				}
			}*/
			//年龄段
			// $product['min_month'] = intval($product['min_month']);
			// $product['max_month'] = intval($product['max_month']);
			// if(!isset($all_age[$product['min_month']])||!isset($all_age[$product['max_month']])){
			// 	$this->_record_error($error_records, $result, '年龄段不正确');
			// 	continue;
			// }
			try {
				$product_id = $this->product_model->insert($product);
				$result['product_id'] = $product_id;
				$all_product_ids[] = $product_id;
				$this->_record_success($success_records, $result);
			} catch (Exception $e) {
				$this->_record_error($error_records, $product, $e->getMessage());
				continue;
			}
			log_product_price(array(), $product, $product_id);
		}
		file_put_contents(APPPATH.'../public/import/_result/product_success', serialize($success_records));
		file_put_contents(APPPATH.'../public/import/_result/product_error', serialize($error_records));
		if(count($error_records)>0){
		    throw new Exception("存在错误记录");
		}
		$product_id_list = implode(',',$all_product_ids);
		$this->product_imp_list_model->update(array("status"=>'06',"product_id_list"=>$product_id_list),$imp_list_id);
		$this->db->trans_commit();
		}catch (Exception $e) {
		    $this->db->trans_rollback();
		    $this->product_imp_list_model->update(array("status"=>'03'),$imp_list_id);
		    sys_msg("导入失败请修改后再次导入",1, array(array('text'=>'查看结果','href'=>'import/product_result'), array('text'=>'返回','href'=>'import')),FALSE);
		}
		sys_msg('成功导入 '.count($success_records).' 条记录， 失败 '.count($error_records).' 条记录',0, array(array('text'=>'查看结果','href'=>'import/product_result'), array('text'=>'返回','href'=>'import')),FALSE);
	}

	public function product_result()
	{
		auth('import_pro');
		$success_records = @file_get_contents(APPPATH.'../public/import/_result/product_success');
		$error_records = @file_get_contents(APPPATH.'../public/import/_result/product_error');
		$success_records = @unserialize($success_records);
		if(!is_array($success_records)) $success_records = array();
		$error_records = @unserialize($error_records);
		if(!is_array($error_records)) $error_records = array();
		$this->load->view('import/product_result', array('success_records'=>$success_records, 'error_records'=>$error_records));
	}

	public function product_result_export()
	{
		auth('import_pro');
		sys_msg('暂未开发');
	}
        
        /**
         * 导入商品成本价
         */
        public function product_cost(){
            auth('import_pro_cost');
            $this->load->model("depot_model");
            $this->load->model("provider_model");
            $this->load->model("purchase_model");
            $this->load->model("product_model");
            $this->load->helper("excelxml");
            
            //1.1.获取excel中批次信息
            $exc_data = array();
            $exc_batch = array();
            //1.2.获取excel中商品成本价信息
            $products_sn = array();
            $products = array();

            $file = IMPORT_PATH_PRO_COST.'product_cost.xml';//$file = APPPATH.'../public/import/product_cost/product_cost.xml';
            $exc_data = read_xml($file, 8);
            foreach ($exc_data as $k => $v) {
                if ($k == 1) {
                    //批次主要信息
                    $exc_batch['batch_code'] = $v[0];
                    $exc_batch['provider_code'] = $v[1];
                } else if ($k >= 3) {//商品和价格
                    $products_sn[] = $v[0];
                    $tmp = array();
                    $tmp['product_sn'] = $v[0];
                    $tmp['provider_goods_sn'] = $v[1];
                    $tmp['product_name'] = $v[2];
                    $tmp['consign_price'] = $v[3];
                    $tmp['cost_price'] = $v[4];
                    $tmp['consign_rate'] = $v[5];
                    $tmp['product_cess'] = $v[6];
                    $tmp['product_income_cess'] = $v[7];
                    
                    if( $tmp['product_cess'] < 0 || $tmp['product_cess'] > 1 || strlen( $tmp['product_cess']) != 4 ){//批次成本价格导入税率（两位小数，位数多了不行，少了也不行；数值大于等于0.00，小于等于1.00）
                        sys_msg("$tmp[product_sn]：$tmp[product_cess],税率值必须为两位小数，且大于等于0.00，小于等于1.00",1 ,array() ,FALSE);
                        return;
                    }
                    $tmp['product_cess'] = number_format( $v[6] ,2 ) ;
                    $products[] = $tmp;
                }
            }
	    
	    //2.检查批次，供应商，获取其合作类型
            $batch_check = $this->purchase_model->get_provider_batch_coop($exc_batch['batch_code'], $exc_batch['provider_code']);
            if (empty($batch_check)){
                sys_msg("请检查批次号和供应商是否存在",1 ,array() ,FALSE);
                return;
            }
            $batch_id = $batch_check->batch_id;
            $provider_id = $batch_check->provider_id;
            $cooperation_id = $batch_check->provider_cooperation;

            if ($cooperation_id == COOPERATION_TYPE_COST) {//根据供应商合作类型判断价格唯一
                foreach ($products as $key => $val) {
                    if ($val['cost_price'] == 0 || $val['consign_price'] != 0 || $val['consign_rate'] != 0) {
                        sys_msg("买断商品 $val[pro_sn]：买断价不能为0.00，并且代销价，代销比率应为0.00",0 ,array() ,FALSE);
                        return;
                    } else {
                        $products[$key]['consign_type'] = CONSIGN_TYPE_COST;
                    }
                }
            } else if ($cooperation_id == COOPERATION_TYPE_CONSIGN) {
                foreach ($products as $key => $val) {
                    if ($val['cost_price'] != 0 || ( $val['consign_price'] != 0 && $val['consign_rate'] != 0 )) {
                        sys_msg("代销商品 $val[pro_sn]：代销价/代销比率 只能有一个不能为0.00，并且买断价应为0.00",0 ,array() ,FALSE);
                        return;
                    } else if ($val['consign_price'] != 0 && $val['consign_rate'] == 0) {
                        $products[$key]['consign_type'] = CONSIGN_TYPE_CONSIGN;
                    } else if ($val['consign_price'] == 0 && $val['consign_rate'] != 0) {
                        $products[$key]['consign_type'] = CONSIGN_TYPE_CONSIGN_RATE;
                    }
                }
            }
            $product_ids = $this->product_model->get_product_ids( $products_sn );
            if( empty($product_ids ) || count($product_ids ) != count($products ) ){
                sys_msg("请检查所有的商品是否存在",0 ,array() ,FALSE);
                return;
            }
	    foreach ($product_ids as $val ) {
		if($val['provider_id'] != $provider_id){
		    sys_msg("该批次对应的供应商和导入商品的不一致,不可导入商品价格,product_sn【".$val['product_sn']."】",0 ,array() ,FALSE);
		    return;
		}
	    }
            
            //batch_id下的采购单
            $is_has_purchase = $this->depot_model->filter_purchase(array('batch_id'=> $batch_id, 'purchase_provider'=> $provider_id ) );
            $is_upd = empty( $is_has_purchase )?true:false;//价格可覆盖，否则，不可更新
            if (!$is_upd ) {//有采购单，检查是否存在成本价
                $pro_ids = array();
                foreach ($product_ids as $val ) {
                    $pro_ids[] = $val['product_id'];
                }
                $res = $this->product_model->get_pro_cost( $batch_id , $provider_id, $pro_ids );
                if(empty( $res )){//该批次，供应商下无有成本价商品
                    $is_upd = TRUE;
                }else {
                    sys_msg("该批次已添加了采购单并导入过价格，不可再导入商品价格",0 ,array() ,FALSE);
                    return;
                }
            }
            //3.导入成本价
            $success_pro = array();
            $fail_pro = array();
	    $msg = '成功导入 '. count($success_pro) .' 条记录， 失败 '.count($fail_pro ).' 条记录';
            if ( $is_upd ){
                $this->db->trans_begin();
                foreach ($products as $key => $val ) {
                    foreach ($product_ids as $key => $id_val) {
                        if ( $val['product_sn'] == $id_val['product_sn'] ){
                            $val['product_id'] = $id_val['product_id'];
                            break;
                        }
                    }
                    if(empty( $val['product_id'] ) ){
                        $val['msg'] = "product_id：$val[product_sn]不存在";
                        $fail_pro[] = $val;
                    }else {
                        $val['batch_id'] = $batch_id ;
                        $val['provider_id'] = $provider_id;
                        $upd_arr = $val;
                        unset($upd_arr['product_sn']);
                        unset($upd_arr['provider_goods_sn']);
                        unset($upd_arr['product_name']);
                        $this->product_model->insert_pro_cost($upd_arr , $this->admin_id );
                        $success_pro[] = $val;
                    }
                }
                file_put_contents(IMPORT_PATH_RESULT.'product_cost_success', serialize($success_pro));
		file_put_contents(IMPORT_PATH_RESULT.'product_cost_error', serialize($fail_pro));
		if(count($fail_pro)>0){
		    $this->db->trans_rollback();
		    $msg = "导入出错，数据回滚对数据库无任何影响，请根据结果修改后再次导入";
		}else{
		    $this->db->trans_commit();
		    $msg = "全部导入成功,共".count($success_pro)." 条记录";
		}
            }
            sys_msg($msg,0, array(array('text'=>'查看结果','href'=>'import/product_cost_result'), array('text'=>'返回','href'=>'import')),FALSE);
            return;
        }
        
        public function product_cost_result(){
            auth('import_pro_cost');
            $success_records = @file_get_contents(IMPORT_PATH_RESULT . 'product_cost_success');
            $error_records = @file_get_contents(IMPORT_PATH_RESULT . 'product_cost_error');
            $success_records = @unserialize($success_records);
            if (!is_array($success_records))
                $success_records = array();
            $error_records = @unserialize($error_records);
            if (!is_array($error_records))
                $error_records = array();
            $this->load->view('import/product_cost_result', array('success_records' => $success_records, 'error_records' => $error_records));
        }

	public function color_size()
	{
		auth('import_sub');
		$this->load->model('product_model');
		$this->load->model('color_model');
		$this->load->model('size_model');

		$file = APPPATH.'../public/import/color_size/color_size.xml';
		if (!file_exists($file)) {
			sys_msg('数据库文件不存在', 1 ,array() ,FALSE);
		}
		$content = file_get_contents($file);
		$content = preg_replace('/&.*;/','',$content);
		$dom = new SimpleXMLElement($content);
		$dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
		$rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
		$keys = array('product_sn','color_name','size_name','provider_barcode');
		$success_records = array();
		$error_records = array();

		$all_color = index_array($this->color_model->all_color(), 'color_name');
		$all_size = index_array($this->size_model->all_size(), 'size_name');

		$this->db->trans_begin();
		foreach ($rows as $key => $row) {
			if ($key == 0)  continue;
			$color_size = array();
			foreach ($row as $cell) $color_size[] = trim(strval($cell->Data));
			if (!isset($color_size[0]) || empty($color_size[0])) continue;
			$color_size = array_pad($color_size, count($keys), '');
			$color_size = array_slice($color_size, 0, count($keys));
			$color_size = array_combine($keys, $color_size);
			
			if (!isset($all_color[$color_size['color_name']])) {
				$this->_record_error($error_records, $color_size, '颜色不存在');
				continue;
			}
			$color_size['color_id'] = $all_color[$color_size['color_name']]->color_id;

			if (!isset($all_size[$color_size['size_name']])) {
				$this->_record_error($error_records, $color_size, '尺码不存在');
				continue;
			}
			$color_size['size_id'] = $all_size[$color_size['size_name']]->size_id;

			$product = $this->product_model->filter(array('product_sn'=>$color_size['product_sn']));
			if (!$product) {
				$this->_record_error($error_records, $color_size, '商品不存在');
				continue;
			}
			$color_size['product_id'] = $product->product_id;

			$sub = $this->product_model->filter_sub(array('product_id'=>$color_size['product_id'], 'color_id'=>$color_size['color_id'], 'size_id'=>$color_size['size_id']));
			if ($sub) {
				$this->_record_error($error_records, $color_size, '记录重复');
				continue;
			}

			//颜色尺寸导入增加条形码 @baolm
			if(empty($color_size['provider_barcode'])) {
				//$this->_record_error($error_records, $color_size, '商品条形码为空');
				//continue;
				$color_size['provider_barcode'] = $product->product_sn . " " . $all_color[$color_size['color_name']]->color_sn . " " . $all_size[$color_size['size_name']]->size_sn;
			}
			//echo $color_size['provider_barcode'];
			$barcode_list = $this->product_model->filter_barcode_product($color_size['provider_barcode']);
			$flag = false;
			if(!empty($barcode_list)) {
				foreach ($barcode_list as $sub_barcode) {
					if($sub_barcode->provider_id == $product->provider_id) {
						$this->_record_error($error_records, $color_size, '商品条形码重复');
						$flag = true;
						break;
					}
					//|| $sub_barcode->category_id != $product->category_id
					if(/*$sub_barcode->provider_productcode != $product->provider_productcode 
							|| */$sub_barcode->brand_id != $product->brand_id 
							|| $sub_barcode->color_id != $color_size['color_id']
							|| $sub_barcode->size_id != $color_size['size_id']) {
						$this->_record_error($error_records, $color_size, '不是同一货品条形码不能重复');
						$flag = true;
						break;
					}
				}
			}
			if($flag) {
				continue;
			}
			//die;
			try {
				$insert = array(
						'product_id'=>$color_size['product_id'], 
						'color_id'=>$color_size['color_id'], 
						'size_id'=>$color_size['size_id'], 
						'provider_barcode'=>$color_size['provider_barcode'],
						'create_admin'=>$this->admin_id,
						'create_date'=>$this->time
					);
				$sub_id = $this->product_model->insert_sub($insert);
				$color_size['sub_id'] = $sub_id;
				$this->_record_success($success_records, $color_size);
			} catch (Exception $e) {
				$this->_record_error($error_records, $color_size, $e->getMessage());
				continue;
			}
		}

		file_put_contents(APPPATH.'../public/import/_result/color_size_success', serialize($success_records));
		file_put_contents(APPPATH.'../public/import/_result/color_size_error', serialize($error_records));
		$msg = "";
		if(count($error_records)>0){
		    $this->db->trans_rollback();
		    $msg = "导入出错，数据回滚对数据库无任何影响，请根据结果修改后再次导入";
		}else{
		    $this->db->trans_commit();
		     $msg = "全部导入成功,共".count($success_records)." 条记录";
		}
		sys_msg($msg,0, array(array('text'=>'查看结果','href'=>'import/color_size_result'), array('text'=>'返回','href'=>'import')),FALSE);

	}

	public function color_size_result()
	{
		auth('import_sub');
		$success_records = @file_get_contents(APPPATH.'../public/import/_result/color_size_success');
		$error_records = @file_get_contents(APPPATH.'../public/import/_result/color_size_error');
		$success_records = @unserialize($success_records);
		if(!is_array($success_records)) $success_records = array();
		$error_records = @unserialize($error_records);
		if(!is_array($error_records)) $error_records = array();
		$this->load->view('import/color_size_result', array('success_records'=>$success_records, 'error_records'=>$error_records));

	}

	public function color_size_result_export()
	{
		auth('import_sub');
		sys_msg('未完成', 1);
	}

	public function gallery()
	{
            set_time_limit(0);
            auth('import_img');
            $this->load->model('product_model');
            $this->load->model('color_model');
            $this->load->model('model_model');
            $this->load->library('image_lib');
            $this->config->load('product');
            $all_color = index_array($this->color_model->all_color(), 'color_sn');

            $path = APPPATH.'../public/import/gallery/';
            if (!file_exists($path)) sys_msg('未发现上传目录', 1 ,array() ,FALSE);
            $dirs = new DirectoryIterator($path);
            $error_records = array();
            $success_records = array();

            $this->db->trans_begin();
            $has_dir = false;
            foreach ($dirs as $product_dir) {
                if ($product_dir->isDot()) continue;
                if (!$product_dir->isDir()) continue;
                $tag = mb_convert_encoding($product_dir->getFilename(),'utf-8','utf-8,gb2312,gbk');
                $tag = explode('_',$tag);
                if(count($tag)<2) continue;
                $has_dir = true;
                $product_sn=$tag[0];
                $color_sn=$tag[1];
                $record = array();
                $record['path'] = $product_dir->getPathname();
                $product = $this->product_model->filter(array('product_sn'=>$product_sn));
                if (!$product) {
                        $this->_record_error($error_records, $record, '商品不存在');
                        continue;
                }
                if (!isset($all_color[$color_sn])) {
                    $this->_record_error($error_records, $record, '颜色不存在');
                    continue;
                }

                $color = $all_color[$color_sn];
                $all_gallery = $this->product_model->all_gallery(array('product_id'=>$product->product_id, 'color_id'=>$color->color_id));
                $all_gallery = index_array($all_gallery, 'image_type');

                $color_dir = new DirectoryIterator($record['path']);
                foreach ($color_dir as $img) {
                    if ($img->isDot()) continue;
                    if (!$img->isFile()) continue;
                    $record['path'] = $color_dir->getPathname();
                    if(substr($img->getFilename(),0,5)=='model' && substr($img->getFilename(),-4) == '.txt') {
                        $model_id = mb_substr($img->getFilename(), 5, -4);
                        $model = $this->model_model->filter(array('model_id'=>$model_id));
                        if(empty($model)) {
                                $this->_record_error($error_records, $record, '模特编号不存在');
                                continue;
                        }
                        $this->product_model->update(array('model_id'=>$model_id), $product->product_id);
                        $this->_record_success($success_records, $record);
                        continue;
                    }
                    if (!in_array(strtolower(substr($img->getFilename(),-4)), array('.jpg','.png','.gif'))) continue;

                    $gallery = $this->_upload_gallery($img, $product->product_id, $color->color_id);

                    if ($gallery['image_type'] == 'default' && isset($all_gallery['default'])) {
                        $this->_delete_gallery($all_gallery['default']);
                    }
                    if ($gallery['image_type'] == 'tonal' && isset($all_gallery['tonal'])) {
                            $this->_delete_gallery($all_gallery['tonal']);
                    }
                    $gallery['create_admin'] = $this->admin_id;
                    $gallery['create_date'] = $this->time;			
                    $this->product_model->insert_gallery($gallery);
                    $this->_record_success($success_records, $record);
                }
            }
            if(!$has_dir) {
                sys_msg('未找到图片文件，请将图片放在public/import/gallery/目录下', 0 ,array() ,FALSE);
            }
            file_put_contents(APPPATH.'../public/import/_result/gallery_success', serialize($success_records));
            file_put_contents(APPPATH.'../public/import/_result/gallery_error', serialize($error_records));
            $this->db->trans_commit();
            sys_msg('成功导入 '.count($success_records).' 条记录， 失败 '.count($error_records).' 条记录',0, array(array('text'=>'查看结果','href'=>'import/gallery_result'), array('text'=>'返回','href'=>'import')),FALSE);
	}

	public function gallery_result()
	{
		auth('import_img');
		$success_records = @file_get_contents(APPPATH.'../public/import/_result/gallery_success');
		$error_records = @file_get_contents(APPPATH.'../public/import/_result/gallery_error');
		$success_records = @unserialize($success_records);
		if(!is_array($success_records)) $success_records = array();
		$error_records = @unserialize($error_records);
		if(!is_array($error_records)) $error_records = array();
		$this->load->view('import/gallery_result', array('success_records'=>$success_records, 'error_records'=>$error_records));

	}

	// private function

	private function _record_error(&$collect, $row, $message)
	{
		$row['err'] = 1;
		$row['msg'] = $message;
		$collect[] = $row;
	}

	private function _record_success(&$collect, $row)
	{
		$collect[] = $row;
	}

	private function _upload_gallery($img, $product_id, $color_id)
	{
		$this->load->library('image_lib');
		$this->config->load('product');

		$gallery = array('product_id'=>$product_id, 'color_id'=>$color_id);
		$base_dir = CREATE_IMAGE_PATH ;
		$sub_dir = GALLERY_PATH . intval(($product_id-($product_id%100))/100);
		if(!file_exists($base_dir.$sub_dir)) mkdir($base_dir.$sub_dir);
		$filename = $img->getBasename();
		$ext = strtolower(substr($filename,-4));
		$basename = substr($filename, 0, -4);
		switch ($basename) {
			case '1':
				$gallery['image_type'] = 'default';
				break;

			case '2':
				$gallery['image_type'] = 'tonal';
				break;
			
			default:
				$gallery['image_type'] = 'part';
				break;
		}


		$new_basename = '';
		while (True) {
			$new_basename = $product_id.'_'.$color_id.'_'.substr($gallery['image_type'],0,1).'_'.mt_rand (10000,99999);
			if (!file_exists($base_dir.$sub_dir.'/'.$new_basename.$ext)) {
				break;
			}
		}
                
                if (!copy($img->getPathname(), $base_dir.$sub_dir.'/'.$new_basename.$ext)){
                    return array();
                }
		$thumb_arr = $this->config->item('product_fields');
		foreach ($thumb_arr as $field=>$thumb) {
			$gallery_thumb = $sub_dir.'/'.$new_basename.$thumb['sufix'].$ext;
			//TODO 第一版本就有过滤掉这个规格的图片，待确认 if($field=='img_850_850') continue;
			$this->image_lib->initialize(array(
				'source_image' => $img->getPathname(),
				'new_image' => $base_dir.$gallery_thumb,
				'quality'=>85,
				'maintain_ratio'=>FALSE,
				'width'=>$thumb['width'],
				'height'=>$thumb['height']
			));
			$this->image_lib->resize();
			$this->image_lib->clear();
			if($thumb['wm']){
				$this->image_lib->initialize(array(
					'source_image' => $base_dir.$gallery_thumb,
					'quality'=>85,
					'create_thumb'=>FALSE,
					'wm_type'=>'overlay',	
					'wm_overlay_path'=>APPPATH.'../public/images/'.$thumb['wm_file'],
					'wm_hor_offset'=>$thumb['wm_x'],
					'wm_vrt_offset'=>$thumb['wm_y'],
				));
				$this->image_lib->watermark();
				$this->image_lib->clear();
			}
		}
                $gallery['img_url'] = $sub_dir.'/'.$new_basename.$ext;
		$gallery['img_desc'] = '';
		$gallery['sort_order'] = intval(preg_replace('/[a-zA-Z\_\.]/','',$basename));
		return $gallery;
	}

	private function _delete_gallery($gallery)
	{
		$this->config->load('product');

		$base_dir = CREATE_IMAGE_PATH;//APPPATH.'../public/data/images/gallery/';
		$thumb_arr = $this->config->item('product_fields');
		foreach ($thumb_arr as $field => $thumb) {
			if ($gallery->$field && file_exists($base_dir.$gallery->$field)) {
				@unlink($base_dir.$gallery->$field);
			}
		}
		$this->product_model->delete_gallery($gallery->image_id);
	}
        
	/**
     * 导入采购单 
     */
    public function purchase() {
	auth('import_purchase');
	$this->load->model("depot_model");
	$this->load->model("provider_model");
	$this->load->model("purchase_model");
	$this->load->model("product_model");
	$this->load->model("brand_model");
	$this->load->helper("excelxml");

	//1.获取excecl中purchase的主要信息
	$update = array();
	//2.获取excecl中purchase的product_id和数量
	$products = array(); //product_sn,color,size,num
	$products_sn = array();

	$file = IMPORT_PATH_PURCHASE . 'purchase.xml'; //$file = APPPATH.'../public/import/purchase/purchase.xml';
	$data_arr = read_xml($file, 8); //对上传的Excel数据进行处理生成编程数据

	foreach ($data_arr as $k => $v) {
	    if ($k == 1) {
		//批次主要信息
		$update['batch_code'] = trim($v[0]);
		$update['provider_code'] = trim($v[1]);
		$update['purchase_type'] = $v[2]; //合作方式ID
		$update['purchase_order_date'] = trim($v[3]);
		$update['purchase_delivery'] = trim($v[4]);
		$update['purchase_remark'] = trim($v[5]);
	    } else if ($k >= 3) {//商品和数量
		$products_sn[] = trim($v[0]);
		$tmp = array();
		$tmp['pro_sn'] = trim($v[0]);
		$tmp['color_sn'] = trim($v[4]);
		$tmp['size_sn'] = trim($v[6]);
		$tmp['num'] = trim($v[7]);
		//数量判断
		if ( $tmp['num'] <= 0 || !is_numeric($tmp['num']) || strpos($tmp['num'], '.') == true ) {
		    sys_msg("$tmp[pro_sn]:$tmp[num],数量必须为正整数",0 ,array() ,FALSE);
		    return;
		}else {
		    $tmp['num'] = intval(trim($v[7]));
		}
		
		$products[] = $tmp;
	    }
	}
	$products_sn = array_unique($products_sn);

	$update['purchase_brand'] = 0;
	$update['create_date'] = date('Y-m-d H:i:s');
	$update['create_admin'] = $this->admin_id;
	$update['purchase_code'] = $this->depot_model->get_purchase_code();
	$update['lock_date'] = date('Y-m-d H:i:s');
	$update['lock_admin'] = $this->admin_id;
	
	//check合作方式:日期验证，db验证
	if (empty($update['purchase_order_date'])) {
	    sys_msg("请填写采购发起时间", 1 ,array() ,FALSE);
	    return;
	}
	if (!is_date_str_check($update['purchase_order_date'])) {//^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/s
	    sys_msg("采购发起时间错误，必须为yyyy-mm-dd格式：如2013-02-01", 1 ,array() ,FALSE);
	    return;
	}
	if (!empty($update['purchase_delivery'])) {
	    if (!is_date_str_check($update['purchase_delivery'])) {
		sys_msg("预期交货时间错误，必须为yyyy-mm-dd格式：如2013-02-01", 1 ,array() ,FALSE);
		return;
	    }
	}
	if (strtotime($update['purchase_order_date']) > strtotime($update['purchase_delivery'])) {
	    sys_msg("采购发起时间必须早于交货时间", 1 ,array() ,FALSE);
	    return;
	}

	//验证合作方式，获取provider_id
	$provider = $this->provider_model->filter(array('provider_code' => $update['provider_code']));
	unset($update['provider_code']);
	if (empty($provider)) {
	    sys_msg("供应商不存在", 1 ,array() ,FALSE);
	    return;
	} else {
	    $update['purchase_provider'] = $provider->provider_id;
	    $purchase_type = $provider->provider_cooperation;
	    if (empty($update['purchase_type'])) {
		sys_msg("请填写采购单中的合作方式", 1 ,array() ,FALSE);
		return;
	    } else if ($update['purchase_type'] != $purchase_type) {
		sys_msg("导入的采购单中合作方式:$update[purchase_type] 错误，请确认", 1 ,array() ,FALSE);
		return;
	    }
	}

	//check供应商和批次是否可用
	$batch = $this->purchase_model->get_provider_batch($update['purchase_provider'], '', $update['batch_code']);
	if (empty($batch)) {
	    sys_msg("在该采购单批次下不存在 该供应商不 [可用/存在]", 1 ,array() ,FALSE);
	    return;
	} else {
	    unset($update["batch_code"]);
	}
	if($batch ->is_reckoned ==1){
	    sys_msg("批次已经结算，无法新增采购单。", 1 ,array() ,FALSE);
	    return;
	}
	if($batch->brand_id == 0) {
		sys_msg("该批次未设置品牌，无法新增采购单。", 1 ,array() ,FALSE);
	}
	//$brand = $this->brand_model->filter(array('brand_id'=>$batch->brand_id));
	$update['purchase_brand'] = $batch->brand_id;

	//检查 导入的批次下product_sn是否存在并审核通过：每500条检查
	$products_sn = array_chunk($products_sn, 500);
	$non_exist_product_sn = array();
	$audit_products = array();
	$brand_mismatch_product = array();
	foreach ($products_sn as $val) {
		
		//TODO 校验品牌一致性
		foreach ($val as $psn) {
			$product = $this->product_model->filter(array('product_sn'=>$psn));
			if($product->brand_id != $batch->brand_id) {
				$brand_mismatch_product[] = $psn;
			}
		}
		
	    $audit_products = $this->product_model->get_product_ids_by_batch($val, $batch->batch_id);
	    if (count($val) != count($audit_products)) {
			$tmp_sn = array();
			foreach ($audit_products as $key => $ck_val) {
			    $tmp_sn[] = $ck_val['product_sn'];
			}
			foreach ($val as $get_val) {
			    if (!in_array($get_val, $tmp_sn)) {
				$non_exist_product_sn[] = $get_val;
			    }
			}
	    }
	}
	if (!empty($non_exist_product_sn)) {
	    $pro_sn = implode(",", $non_exist_product_sn);
	    sys_msg("该批次下有  不存在/没有经过审核 的商品,请检查：$pro_sn", 1 ,array() ,FALSE);
	    return;
	}
	if (!empty($brand_mismatch_product)) {
	    $pro_sn = implode(",", $brand_mismatch_product);
	    sys_msg("导入的商品的品牌与该批次的品牌不一致,请检查：$pro_sn", 1 ,array() ,FALSE);
	    return;
	}

	//开启事务，批量操作
	$this->db->trans_begin();
	//3.插入purchase_main
	$update["batch_id"] = $batch->batch_id;
	$purchase_id = $this->depot_model->insert_purchase($update);
	$non_exists_product_sub = array();
	//检查商品、颜色和尺寸是否存在,存在->插入，否则->记录下来，插入完成后，提示其不存在
	foreach ($products as $key => $val) {
	    $sub_id = $this->product_model->get_product_sku($val['pro_sn'], $val['size_sn'], $val['color_sn']);
	    if (empty($sub_id)) {//如不存在，将商品、颜色和尺寸存入数组 $non_exists_product_sub 中
		$non_exists_product_sub[]["pro_sn"] = $val['pro_sn'];
		$non_exists_product_sub[]["pro_sn"] = $val['color_sn'];
		$non_exists_product_sub[]["pro_sn"] = $val['size_sn'];
	    } else {//如存在，将数据写入采购单
		$rs = $this->depot_model->insert_purchase_pro_single($sub_id, $val['num'], $purchase_id, $this->admin_id);
		if (empty($rs)) {
		    sys_msg("添加商品失败，调试信息：product_sn : $val[pro_sn],num $val[num]",0 ,array() ,FALSE);
		    return;
		} elseif ($rs == -1) {
		    sys_msg("要添加的商品已存在采购单：sub_id : $sub_id",0 ,array() ,FALSE);
		    return;
		}
	    }
	}
	$this->depot_model->update_purchase_total($purchase_id);
	$this->db->trans_commit();
	if (!empty($non_exists_product_sub)) {
	    $msg_str = "[product_sn, color_sn, size_sn]:";
	    foreach ($non_exists_product_sub as $key => $val) {
		$msg_str .= "[" . implode(',', $val) . "]";
	    }
	    sys_msg("以下商品sku由于不存在未插入->$msg_str ",0 ,array() ,FALSE);
	    return;
	}
	$success_num = count($products) - count($non_exists_product_sub);
	sys_msg('成功导入 ' . $success_num . ' 条记录， 失败 ' . count($non_exists_product_sub) . ' 条记录', 0, array(array('text' => '查看结果', 'href' => 'purchase'), array('text' => '返回', 'href' => 'import')),FALSE);
    }
    
    public function consign(){
	auth('import_consign');
	$this->load->model("product_model");
	$this->load->model("purchase_model");
        $products = array();//product_sn,color,size,num
        $file = APPPATH.'../public/import/consign/consign.xml';
	if (!file_exists($file)) {
		sys_msg('数据库文件不存在', 1 ,array() ,FALSE);
	}
	$content = file_get_contents($file);
	$content = preg_replace('/&.*;/','',$content);
	$dom = new SimpleXMLElement($content);
	$dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
	$rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
	$keys = array('product_sn','color_code','size_code','consign_num');
	$success_records = array();
	$error_records = array();
        //开启事务，批量操作
        $this->db->trans_begin();
	foreach ($rows as $key => $row) {
	    if ($key == 0)  continue;
	    $product = array();
	    foreach ($row as $cell) $product[] = trim(strval($cell->Data));
	    if (!isset($product[0]) || empty($product[0])) continue;
	    $product = array_pad($product, count($keys), '');
	    $product = array_slice($product, 0, count($keys));
	    $product = array_combine($keys, $product);
	    
	    $product_sn = $product["product_sn"];
	    $color_code = $product["color_code"];
	    $size_code = $product["size_code"];
	    $db_product = $this ->product_model->filter(array("product_sn"=>$product_sn));
	    if(empty($db_product)){
		$this->_record_error($error_records, $product, '不存在对应商品');
		continue;
	    }
	    $db_product_sub = $this ->product_model->all_sub(array("product_id"=>$db_product->product_id,"color_sn"=>$color_code,"size_sn"=>$size_code));
	     if(empty($db_product_sub)){
		$this->_record_error($error_records, $product, '对应商品不存在指定规格');
		continue;
	    }
	    if(empty($product["consign_num"])){
		$this->_record_error($error_records, $product, '请设置商品虚库数值');
		continue;
	    }
	    $consign_num = intval($product["consign_num"]);
	    if($consign_num < -2){
		$this->_record_error($error_records, $product, '商品虚库设置数值不合法');
		continue;
	    }
	    //校验是否存在实库批次，如存在则此次操作有误。
	    $res = $this->product_model->query_product_cost(array("product_id"=>$db_product->product_id));
	    $vali_b_flag = TRUE;
	    foreach ($res as $rs){
		$batch = $this->purchase_model->filter_purchase_batch(array("batch_id"=>$rs->batch_id));
		if(!empty($batch)){
		   if($batch->batch_type == 0 && $batch->is_consign == 0 && $batch->is_reckoned == 0){
		       $this->_record_error($error_records, $product, '商品不属于虚库批次，对应实库销售批次号为['.$batch->batch_code.']，不可导入虚库库存。');
		       $vali_b_flag = FALSE;
		       break;
		   }
		}
	    }
	    if(!$vali_b_flag)
		continue;
	    try{
		$sub_product_id = $db_product_sub[0]->sub_id;
		$this ->product_model->update_sub(array("consign_num"=>$consign_num),$sub_product_id);
		$this->_record_success($success_records, $product);
	    } catch (Exception $e) {
		$this->_record_error($error_records, $product, $e->getMessage());
		continue;
	    }
	}
	file_put_contents(APPPATH.'../public/import/_result/consign_success', serialize($success_records));
	file_put_contents(APPPATH.'../public/import/_result/consign_error', serialize($error_records));
	$msg = "";
	if(count($error_records)>0){
	    $this->db->trans_rollback();
	    $msg = "导入出错，数据回滚对数据库无任何影响，请根据结果修改后再次导入";
	}else{
	    $this->db->trans_commit();
	     $msg = "全部导入成功,共".count($success_records)." 条记录";
	}
        sys_msg($msg,0, array(array('text'=>'查看结果','href'=>'import/consign_result'), array('text'=>'返回','href'=>'import')),FALSE);
    }

    public function consign_result(){
	    auth('import_consign');
	    $success_records = @file_get_contents(APPPATH.'../public/import/_result/consign_success');
	    $error_records = @file_get_contents(APPPATH.'../public/import/_result/consign_error');
	    $success_records = @unserialize($success_records);
	    if(!is_array($success_records)) $success_records = array();
	    $error_records = @unserialize($error_records);
	    if(!is_array($error_records)) $error_records = array();
	    $this->load->view('import/consign_result', array('success_records'=>$success_records, 'error_records'=>$error_records));
    }
    
    public function product_sub(){
	    auth('import_pro_sub');
	    $this->load->model("product_model");
	    $this->load->model('style_model');
	    $this->load->model('model_model');
	    $this->load->model('provider_model');
	    $this->load->model('carelabel_model');
	    $this->load->model('product_type_model');
	    $file = APPPATH.'../public/import/product_sub/product_sub.xml';
	    if (!file_exists($file)) {
		    sys_msg('数据库文件不存在', 1 ,array() ,FALSE);
	    }
	    $content = file_get_contents($file);
	    //$content = preg_replace('/&.*;/','',$content);
	    $dom = new SimpleXMLElement($content);
	    $dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
	    $rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
	    // $keys = array('product_sn','provider_productcode','product_name','style_id','product_sex_name',
			  //  'unit_name','type_code','goods_carelabel','model_id',
			  //  'desc_composition','desc_dimensions','desc_material','desc_waterproof','desc_crowd','desc_notes','desc_expected_shipping_date','desc_use_explain','desc_function_explain');

	    $keys = array('product_sn','provider_productcode','product_name','subhead','product_weight','unit_name','type_code','provider_name','package_name', 'pack_method');

	    $success_records = array();
	    $error_records = array();
	    
	    // $all_style = index_array($this->style_model->all_style(),'style_name');
	    // $all_model = index_array($this->model_model->all_model(), 'model_id');
	    $all_provider = index_array($this->provider_model->all_provider(), 'provider_name');
	    $all_type = index_array($this->product_type_model->filter(array('parent_id <>'=>0,'parent_id2 <>'=>0)), 'type_code');

	    //开启事务，批量操作
	    $this->db->trans_begin();
	    foreach ($rows as $key => $row) {

			if ($key == 0)  continue;

			$product = array();

			foreach ($row as $cell) $product[] = trim(strval($cell->Data));

			if (!isset($product[0]) || empty($product[0])) continue;

			$product = array_pad($product, count($keys), '');
			$product = array_slice($product, 0, count($keys));
			$product = array_combine($keys, $product);
		
			$update = array();
			
			$update["product_name"] = $product['product_name'];

			$db_product = $this ->product_model->filter(array("product_sn"=>$product['product_sn']));

			if(empty($db_product)){
			    $this->_record_error($error_records, $product, '不存在对应商品');
			    continue;
			}

			//供应商名称			
			if (!isset($all_provider[$product['provider_name']])) {
				$this->_record_error($error_records, $product, '供应商不存在');
				continue;
			}
			$update['provider_id'] = $all_provider[$product['provider_name']]->provider_id;

			//unit_name
			$update["provider_productcode"] = $product['provider_productcode'];
			$update["subhead"] = $product['subhead'];
			$update["product_weight"] = $product['product_weight'];
			$update["package_name"] = $product['package_name'];
                        $update["pack_method"] = $product['pack_method'];
			$update["unit_name"] = $product['unit_name'];
			//type_code
			$type_codes = $product["type_code"];
			if(!empty($type_codes)){
			    $type_code_array = explode('|', $type_codes);
			    $type_check_flag = true;
			    $type_id_array = array();
			    foreach ($type_code_array as $type_code){
					if (!isset($all_type[$type_code])) {
					    $this->_record_error($error_records, $product, '前台分类不存在[' . $type_code . ']');
					    $type_check_flag = false;
					    break;
					}
					$type_id_array[] = $all_type[$type_code]->type_id;
			    }
			    if($type_check_flag)
					$this ->product_model->set_product_type(array("product_id"=>$db_product ->product_id,"type_ids"=>$type_id_array));
			    else
				continue;
			}

			try {
			    $this->product_model->update($update,$db_product ->product_id);
			    $this->_record_success($success_records, $product);
			} catch (Exception $e) {
				$this->_record_error($error_records, $product, $e->getMessage());
				continue;
			}
	    }
	    file_put_contents(APPPATH.'../public/import/_result/product_sub_success', serialize($success_records));
	    file_put_contents(APPPATH.'../public/import/_result/product_sub_error', serialize($error_records));
	    $msg = "";
	    if(count($error_records)>0){
			$this->db->trans_rollback();
			$msg = "导入出错，数据回滚对数据库无任何影响，请根据结果修改后再次导入";
	    }else{
			$this->db->trans_commit();
			$msg = "全部导入成功,共".count($success_records)." 条记录";
	    }
	    
	    sys_msg($msg,0, array(array('text'=>'查看结果','href'=>'import/product_sub_result'), array('text'=>'返回','href'=>'import')),FALSE);

    }
    
     public function product_sub_result(){
	    auth('import_pro_sub');
	    $success_records = @file_get_contents(APPPATH.'../public/import/_result/product_sub_success');
	    $error_records = @file_get_contents(APPPATH.'../public/import/_result/product_sub_error');
	    $success_records = @unserialize($success_records);
	    if(!is_array($success_records)) $success_records = array();
	    $error_records = @unserialize($error_records);
	    if(!is_array($error_records)) $error_records = array();
	    $this->load->view('import/product_sub_result', array('success_records'=>$success_records, 'error_records'=>$error_records));
    }
    
    public function history(){
	$this->load->model('product_imp_list_model');
	$filter = $this->uri->uri_to_assoc(3);
	$filter["create_admin"] = $this->input->get('create_admin');
	$filter["start_date"] = $this->input->get('start_date');
	$filter["end_date"] = $this->input->get('end_date');
	$filter = get_pager_param($filter);
	$data = $this->product_imp_list_model->all_filter($filter);
	$all_admin = $this->product_imp_list_model->all_import_admin();
	$this->load->vars('all_import_admin',$all_admin );
	$this->load->vars('show_import_admin',index_array($all_admin,'create_admin') );
	if ($this->input->post('is_ajax')){
		$data['full_page'] = FALSE;
		$data['content'] = $this->load->view('import/history', $data, TRUE);
		$data['error'] = 0;
		unset($data['list']);
		echo json_encode($data);
		return;
	 }
	$data['full_page'] = TRUE;
	$this->load->view('import/history', $data);
    }
    
    /**
     * 根据主要导入记录，统一审核
     */
    public function batch_conform($import_list_id){
	if(empty($import_list_id)){
	    sys_msg('无法选定对应记录', 1 ,array() ,FALSE);
	}
	$this->load->model('product_model');
	$this->load->model('product_imp_list_model');
	$data = $this->product_imp_list_model->filter(array("id"=>$import_list_id));
	if(empty($data)){
	    sys_msg('无法查询对应记录', 1 ,array() ,FALSE);
	}
	$product_ids = $data ->product_id_list ;
	$count = 0 ;
	$skip_count = 0;
	$this->db->trans_begin();
	foreach (explode(',', $product_ids) as $product_id) {
		$count += 1;
		$product_id = intval($product_id);
		$product = $this->product_model->filter(array('product_id'=>$product_id));
		if(!$product || $product->is_audit) { $skip_count += 1 ; continue;}
		$sub = $this->product_model->filter_sub(array('product_id'=>$product_id));
		if(!empty($sub)){ $skip_count += 1 ; continue;}
		$this->product_model->update(array('is_audit'=>1,'audit_admin'=>$this->admin_id, 'audit_date'=>$this->time), $product_id);
	}
	if($skip_count>0){
	    $this->db->trans_rollback();
	    sys_msg("审核失败，共审核[".$count."]件商品，其中[".$skip_count."]件审核不能完成。", 0 ,array() ,FALSE);
	}else{
	    $this->product_imp_list_model->update(array("confirm_admin"=>$this->admin_id,"confirm_date"=>$this->time),$import_list_id);
	    $this->db->trans_commit();
	    sys_msg("审核完成，共审核[".$count."]件商品。", 0 ,array() ,FALSE);
	}
    }
    
    public function download_color_size_template($import_list_id){
	if(empty($import_list_id)){
	    sys_msg('无法选定对应记录', 1 ,array() ,FALSE);
	}
	$this->load->model('product_imp_list_model');
	$data = $this->product_imp_list_model->filter(array("id"=>$import_list_id));
	if(empty($data)){
	    sys_msg('无法查询对应记录', 1 ,array() ,FALSE);
	}
	$product_ids = $data ->product_id_list ;
	$exlval = array();
	if(!empty($product_ids)){
	    $product_id_array = explode(',', $product_ids);
	    $exlval = $this->product_imp_list_model->query_product_color_size($product_id_array);
	}
	$info[]=array('product_sn'=>'商品款号','provider_productcode'=>'供应商货号','color_name'=>'颜色名称','size_name'=>'尺寸名称','provider_barcode'=>'商品条形码');
	$this->load->helper('excel');
	export_excel_xml($import_list_id.'_颜色尺寸',array($info,$exlval));
    }
    
    public function download_product_sub_template($import_list_id){
	if(empty($import_list_id)){
	    sys_msg('无法选定对应记录', 1 ,array() ,FALSE);
	}
	$this->load->model('product_imp_list_model');
	$this->load->model('product_type_model');
	$all_type = index_array($this->product_type_model->filter(array('parent_id <>'=>0,'parent_id2 <>'=>0)), 'type_id');
	$data = $this->product_imp_list_model->filter(array("id"=>$import_list_id));
	if(empty($data)){
	    sys_msg('无法查询对应记录', 1 ,array() ,FALSE);
	}
	$product_ids = $data ->product_id_list ;
	$exlval = array();
	if(!empty($product_ids)){
	    $product_id_array = explode(',', $product_ids);
	    $product_sub_array = $this->product_imp_list_model->query_product_sub($product_id_array);
	    foreach ($product_sub_array as $info){
		$sub = array();
		$sub["product_sn"] = $info["product_sn"];
		$sub["provider_productcode"] = $info["provider_productcode"];
		$sub["product_name"] = $info["product_name"];
		$sub["style_name"] = $info["style_id"];
		$sub["product_sex"] = $info["product_sex"];
		$sub["unit_name"] = $info["unit_name"];
		$type_link_array = $this->product_type_model->filter_product_type_link(array("product_id"=>$info["product_id"]));
		$type_array = array();
		foreach ($type_link_array as $type_link){
		    $type_array[] =$all_type[$type_link->type_id]->type_code;
		}
		$sub["type_code"] = implode('|', $type_array);
		$sub["goods_carelabel"] = str_replace(',','|',$info['goods_carelabel']);
		$sub["model_id"] = $info["model_id"];
		$product_desc_additional = $info["product_desc_additional"];
		if(!empty($product_desc_additional)){
		    $desc_array = unserialize($product_desc_additional);
		    $sub["desc_composition"] = $desc_array["desc_composition"];
		    $sub["desc_dimensions"] = $desc_array["desc_dimensions"];
		    $sub["desc_material"] = $desc_array["desc_material"];
		    $sub["desc_waterproof"] = $desc_array["desc_waterproof"];
		    $sub["desc_crowd"] = $desc_array["desc_crowd"];
		    $sub["desc_notes"] = $desc_array["desc_notes"];
		    $sub["desc_expected_shipping_date"] = $desc_array["desc_expected_shipping_date"]; 
		}else{
		    $sub["desc_composition"] = $info[""];
		    $sub["desc_dimensions"] = $info[""];
		    $sub["desc_material"] = $info[""];
		    $sub["desc_waterproof"] = $info[""];
		    $sub["desc_crowd"] = $info[""];
		    $sub["desc_notes"] = $info[""];
		    $sub["desc_expected_shipping_date"] = $info[""]; 
		}
		$exlval[] = $sub;
	    }
	}
	
	$info_title = array('product_sn'=>'商品款号','provider_productcode'=>'供应商货号','product_name'=>'商品名称','style_name'=>'风格编号'
	    ,'product_sex'=>'性别','unit_name'=>'计量单位','product_type'=>'三级分类编码','goods_carelabel'=>'新商品保养编码(|线分割)','model_id'=>'模特编码（暂时保留）'
	    ,'desc_composition'=>'成分','desc_dimensions'=>'尺寸规格','desc_material'=>'材质','desc_waterproof'=>'防水性','desc_crowd'=>'适合人群','desc_notes'=>'温馨提示','desc_expected_shipping_date'=>'预计发货日期');
	$this->load->helper('excel');
	export_excel_xml($import_list_id.'_次要信息',array($info_title,$exlval));
    }
    

        /**
         * 导入商品成本价
         */
        public function product_price(){
            auth('import_pro_price');
            $this->load->model("product_model");
            $this->load->helper("excelxml");
            
            //1.1.获取excel中价格信息
            $exc_data = array();
	    $products = array();

            $file = IMPORT_PATH_PRO_PRICE.'product_price.xml';//$file = APPPATH.'../public/import/product_price/product_price.xml';
            $exc_data = read_xml($file, 3);
            foreach ($exc_data as $k => $v) {
		    if($k == 0){
			continue;
		    }
                    //批次主要信息
		    $exc_price = array();
                    $exc_price['product_sn'] = $v[0];
                    $exc_price['shop_price'] = $v[1];
		    $exc_price['market_price'] = $v[2];
                    
                    if( $exc_price['shop_price'] < 0){
                        sys_msg($exc_price['product_sn']." ,商品售价不能小于0",1 ,array() ,FALSE);
                        return;
                    }
                    $products[] = $exc_price;
            }
	    
            //3.导入成本价
            $success_pro = array();
            $fail_pro = array();
	    $product_ids = array();
	    $this->db->trans_begin();
	    foreach ($products as $key => $val ) {
		
		$product = $this->product_model->filter(array("product_sn"=>$val['product_sn']));
		if(empty($product) ){
		    $val['msg'] = "product_sn：".$val['product_sn']."不存在";
		    $fail_pro[] = $val;
		}else {
		    $up_arr = array();
		    $up_arr["shop_price"]=$val['shop_price'];
		    if(!empty($val['market_price']) && $val['market_price'] > $val['shop_price'])
			$up_arr["market_price"]=$val['market_price'];
		    $this->product_model->update($up_arr , $product->product_id );
		    $success_pro[] = $val;
		}
	    }
	    file_put_contents(IMPORT_PATH_RESULT.'product_price_success', serialize($success_pro));
	    file_put_contents(IMPORT_PATH_RESULT.'product_price_error', serialize($fail_pro));
	    $this->db->trans_commit();
	    $msg = "导入成功,共导入成功".count($success_pro)." 条记录，失败".count($fail_pro)." 条记录";
            sys_msg($msg,0, array(array('text'=>'查看结果','href'=>'import/product_price_result'), array('text'=>'返回','href'=>'import')),FALSE);
            return;
        }
        
        public function product_price_result(){
            auth('import_pro_price');
            $success_records = @file_get_contents(IMPORT_PATH_RESULT . 'product_price_success');
            $error_records = @file_get_contents(IMPORT_PATH_RESULT . 'product_price_error');
            $success_records = @unserialize($success_records);
            if (!is_array($success_records))
                $success_records = array();
            $error_records = @unserialize($error_records);
            if (!is_array($error_records))
                $error_records = array();
            $this->load->view('import/product_price_result', array('success_records' => $success_records, 'error_records' => $error_records));
        }

    

    public function provider_barcode(){
	auth('import_provider_barcode');
	$this->load->model("product_model");
        $file = APPPATH.'../public/import/provider_barcode/provider_barcode.xml';
	if (!file_exists($file)) {
		sys_msg('修改条形码文件不存在', 1 ,array() ,FALSE);
	}
	$content = file_get_contents($file);
	$content = preg_replace('/&.*;/','',$content);
	$dom = new SimpleXMLElement($content);
	$dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
	$rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
	$keys = array('product_sn','color_name','size_name','provider_barcode_old', 'provider_barcode_new');
	$success_records = array();
	$error_records = array();
        //开启事务，批量操作
        $this->db->trans_begin();
	foreach ($rows as $key => $row) {
	    if ($key == 0)  continue;
	    $product = array();
	    foreach ($row as $cell) $product[] = trim(strval($cell->Data));
	    if (!isset($product[0]) || empty($product[0])) continue;
	    $product = array_pad($product, count($keys), '');
	    $product = array_slice($product, 0, count($keys));
	    $product = array_combine($keys, $product);
	    
	    $product_sn = $product["product_sn"];
	    $color_name = $product["color_name"];
	    $size_name = $product["size_name"];
            $provider_barcode_old = $product["provider_barcode_old"];
            $provider_barcode_new = $product["provider_barcode_new"];
            
            if(empty($product_sn)){
		$this->_record_error($error_records, $product, '请设置商品款号');
		continue;
	    }
            if(empty($color_name)){
		$this->_record_error($error_records, $product, '请设置颜色名称');
		continue;
	    }
            if(empty($size_name)){
		$this->_record_error($error_records, $product, '请设置尺寸名称');
		continue;
	    }
            if(empty($provider_barcode_old)){
		$this->_record_error($error_records, $product, '请设置商品条形码(老)');
		continue;
	    }
            if(empty($provider_barcode_new)){
		$this->_record_error($error_records, $product, '请设置商品条形码(新)');
		continue;
	    }
            
            if(strlen($provider_barcode_new) > 16){
		$this->_record_error($error_records, $product, '商品条形码(新)长度不能超过17位');
		continue;
	    }           
            
	    $db_product = $this ->product_model->filter(array("product_sn"=>$product_sn));
	    if(empty($db_product)){
		$this->_record_error($error_records, $product, '不存在对应商品');
		continue;
	    }
	    $db_product_sub = $this ->product_model->select_sub_by_SKU(array("ps.product_id"=>$db_product->product_id,"color_name"=>$color_name,"size_name"=>$size_name));
            if(empty($db_product_sub)){
		$this->_record_error($error_records, $product, '对应商品不存在指定规格');
		continue;
	    }
	    if ($db_product_sub[0]->provider_barcode != $provider_barcode_old) {
                $this->_record_error($error_records, $product, '对应商品条形码不正确');
		continue;
            }
            if (!empty($db_product_sub[0]->box_sub_id)) {
                $this->_record_error($error_records, $product, '对应采购单已收货');
		continue;
            }
	    
	    try{
		$sub_product_id = $db_product_sub[0]->sub_id;
		$this ->product_model->update_sub(array("provider_barcode"=>$provider_barcode_new),$sub_product_id);
		$this->_record_success($success_records, $product);
	    } catch (Exception $e) {
		$this->_record_error($error_records, $product, $e->getMessage());
		continue;
	    }
	}
	file_put_contents(APPPATH.'../public/import/_result/provider_barcode_success', serialize($success_records));
	file_put_contents(APPPATH.'../public/import/_result/provider_barcode_error', serialize($error_records));
	$msg = "";
	if(count($error_records)>0){
	    $this->db->trans_rollback();
	    $msg = "导入出错，数据回滚对数据库无任何影响，请根据结果修改后再次导入";
	}else{
	    $this->db->trans_commit();
	     $msg = "全部导入成功,共".count($success_records)." 条记录";
	}
        sys_msg($msg,0, array(array('text'=>'查看结果','href'=>'import/provider_barcode_result'), array('text'=>'返回','href'=>'import')),FALSE);
    }

    public function provider_barcode_result(){
	    auth('import_provider_barcode');
	    $success_records = @file_get_contents(APPPATH.'../public/import/_result/provider_barcode_success');
	    $error_records = @file_get_contents(APPPATH.'../public/import/_result/provider_barcode_error');
	    $success_records = @unserialize($success_records);
	    if(!is_array($success_records)) $success_records = array();
	    $error_records = @unserialize($error_records);
	    if(!is_array($error_records)) $error_records = array();
	    $this->load->view('import/provider_barcode_result', array('success_records'=>$success_records, 'error_records'=>$error_records));
    }
    

}
