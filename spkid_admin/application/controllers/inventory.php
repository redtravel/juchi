<?php
/*
 * 盘点管理。
 */
class Inventory extends CI_Controller {
    
    function __construct () {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if(!$this->admin_id) redirect('index/login');
        $this->load->model('inventory_model');
    }
    
    /*
     * 盘点列表首页。
     */
    public function index() {
        auth('inventory_view');
        
        $this->load->vars('admin_arr', $this->doGetAllAdmins());
        $this->load->vars('depot_arr', $this->doGetAllDepots());
        
        $filter = array();
        $inventory_sn = trim($this->input->post('inventory_sn'));
        if (!empty($inventory_sn)) {
            $filter['inventory_sn'] = $inventory_sn;
        }
        $start_date = trim($this->input->post('start_date'));
        if (!empty($start_date)) {
            $filter['start_date'] = $start_date;
        }
        $end_date = trim($this->input->post('end_date'));
        if (!empty($end_date)) {
            $filter['end_date'] = $end_date;
        }
        
        $filter = get_pager_param($filter);
        $data = $this->inventory_model->inventory_list($filter);
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('depot/inventory/inventory_list', $data, TRUE);
            $data['error'] = 0;
            unset($data['list']);
            echo json_encode($data);
            return;
        }
        $data['full_page'] = TRUE;
        
        $this->load->view('depot/inventory/inventory_list', $data);
    }
    
    /*
     * RF枪扫描盘点列表页，倒序显示最近的5个已审核的盘点。
     */
    public function scan_list() {
        auth('inventory_scan');
        
        $data = $this->inventory_model->latest_inventory_list(10);
        $this->load->view('depot/inventory/scan_list', $data);
    }
    
    /*
     * 添加盘点页面。
     */
    public function add() {
        auth('inventory_add');
        
        $this->load->vars('depot_arr', $this->doGetAllDepots());
        $this->load->vars('inventory_sn', $this->doGenerateInventorySn());
        
        $this->load->view('depot/inventory/inventory_add');
    }
    
    /*
     * 添加盘点。
     */
    public function proc_add() {
        auth('inventory_add');
        
        $data = array();
        $data['depot_id'] = $this->input->post('depot_id');
        $data['inventory_sn'] = $this->input->post('inventory_sn');
        $data['inventory_type'] = $this->input->post('inventory_type');
        if ($data['inventory_type'] == 0) { // 指定货架范围盘点
            $data['shelf_from'] = strtoupper(trim($this->input->post('shelf_from')));
            $data['shelf_to'] = strtoupper(trim($this->input->post('shelf_to')));
            
            $this->doCheckShelf($data['depot_id'], $data['shelf_from'], $data['shelf_to']);
        } else if ($data['inventory_type'] == 1) { // 指定储位盘点
            $location_name = trim($this->input->post('location_name'));
            
            $data['location_id'] = $this->doCheckLocation($location_name, $data['depot_id']);
        } else {
            sys_msg('盘点类型错误！', 1);
        }
        $data['inventory_note'] = $this->input->post('inventory_note');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        
        $inventory_id = $this->inventory_model->insert_inventory($data);

        sys_msg('添加成功', 0, array(
            array('text'=>'继续编辑', 'href'=>'inventory/edit/'.$inventory_id), 
            array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /*
     * 编辑盘点页面。
     */
    public function edit($inventory_id) {
        auth(array('inventory_view', 'inventory_edit'));
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        
        $this->load->vars('row', $inventory);
        $this->load->vars('depot_arr', $this->doGetAllDepots());
        $this->load->vars('perm_edit', check_perm('inventory_edit'));
        
        $this->load->model('location_model');
        $this->load->vars('location', $this->location_model->get_location(array('location_id' => $inventory->location_id)));
        
        $this->load->view('depot/inventory/inventory_edit');
    }
    
    /*
     * 编辑盘点。
     */
    public function proc_edit() {
        auth('inventory_edit');
        
        $data = array();
        $data['depot_id'] = $this->input->post('depot_id');
        $data['inventory_note'] = $this->input->post('inventory_note');
        $data['inventory_type'] = $this->input->post('inventory_type');
        
        if ($data['inventory_type'] == 0) { // 指定货架范围盘点
            $data['location_id'] = NULL;
            $data['shelf_from'] = strtoupper(trim($this->input->post('shelf_from')));
            $data['shelf_to'] = strtoupper(trim($this->input->post('shelf_to')));
            
            $this->doCheckShelf($data['depot_id'], $data['shelf_from'], $data['shelf_to']);
        } else if ($data['inventory_type'] == 1) { // 指定批次盘点
            $data['shelf_from'] = NULL;
            $data['shelf_to'] = NULL;
            $location_name = trim($this->input->post('location_name'));
            
            $data['location_id'] = $this->doCheckLocation($location_name, $data['depot_id']);
        } else {
            sys_msg('盘点类型错误！', 1);
        }

        $inventory_id = $this->input->post('inventory_id');
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        if ($inventory->status  != 0) {
            sys_msg("只能编辑当前状态为未确认的盘点！", 1);
        }
        
        $this->db->trans_begin();
        $this->inventory_model->update_inventory($data, $inventory_id);
        $this->db->trans_commit();

        sys_msg('编辑成功', 0, array(
            array('text'=>'继续编辑', 'href'=>'inventory/edit/'.$inventory_id), 
            array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /*
     * 确认盘点。
     */
    public function check($inventory_id) {
        auth('inventory_check');
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        if ($inventory->status != 0) {
            sys_msg("只能确认当前状态为未确认的盘点！", 1);
        }
        
        // 判断盘点单是否已生成
        $generated = $this->checkInventoryDetailGenerated($inventory);
        if ($generated == false) {
            sys_msg("确认前，请先生成盘点清单！", 1);
        }
        
        $data = array();
        $data['status'] = 1;
        $data['check_admin'] = $this->admin_id;
        $data['check_date'] = date('Y-m-d H:i:s');
        
        $this->db->trans_begin();
        $this->inventory_model->update_inventory($data, $inventory_id);
        $this->db->trans_commit();
        
        sys_msg('确认成功', 0, array(array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /*
     * 财务审核。
     */
    public function financial_check($inventory_id) {
        auth('inventory_financial_check');
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        if ($inventory->status != 2) {
            sys_msg("只能审核当前状态为已结束的盘点！", 1);
        }
        
        // 财审
        $this->inventory_model->financial_check($inventory, $this->admin_id);
        
        sys_msg('财审成功', 0, array(array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /*
     * 终止一个已结束的盘点。
     */
    public function stop($inventory_id) {
        auth('inventory_stop');
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        if ($inventory->status  != 1 && $inventory->status  != 2) {
            sys_msg("只能终止当前状态为已确认/已结束的盘点！", 1);
        }
        
        $this->inventory_model->stop_inventory($inventory, $this->admin_id);
        
        sys_msg('终止成功', 0, array(array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /*
     * 删除一个未审核的盘点。
     */
    public function delete($inventory_id) {
        auth('inventory_delete');
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        if ($inventory->status  != 0) {
            sys_msg("只能删除当前状态为未确认的盘点！", 1);
        }
        
        $this->inventory_model->delete_inventory($inventory_id);
        
        sys_msg('删除成功', 0, array(array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /*
     * RF枪扫描盘点页面。
     */
    public function scanning($inventory_id) {
        auth('inventory_scan');
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        if ($inventory->status != 1) {
            sys_msg("只能扫描当前状态为已确认的盘点！", 1);
        }
        
        $this->load->vars('row', $inventory);
        $this->load->view('depot/inventory/scanning');
    }
    
    /*
     * 查看盘点详情页面。
     */
    public function detail($inventory_id, $only_show_diff = 0) {
        auth('inventory_view');
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        $this->load->vars('row', $inventory);
        $this->load->vars('depot_arr', $this->doGetAllDepots());
        $this->load->vars('admin_arr', $this->doGetAllAdmins());
        
        $filter = array();
        $filter['inventory_id'] = $inventory->inventory_id;
        $filter['only_show_diff'] = $only_show_diff;
        $filter = get_pager_param($filter);
        $data = $this->inventory_model->inventory_product_list($filter);
        
        // 获取排除储位列表
        $exclude_locations = $inventory->exclude_locations;
        if (!empty($exclude_locations)) {
            $this->load->model('location_model');
            $data['exclude_location_list'] = $this->location_model->batch_get_locations($exclude_locations);
        }
        
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('depot/inventory/inventory_detail', $data, TRUE);
            $data['error'] = 0;
            unset($data['result']);
            echo json_encode($data);
            return;
        }
        
        $data['full_page'] = TRUE;
        $this->load->view('depot/inventory/inventory_detail', $data);
    }
    
    /*
     * 复盘储位。
     */
    public function reset($location_id, $inventory_id) {
        auth('inventory_reset');
                
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        if ($inventory->status != 1) {
            sys_msg("盘点状态不是已确认，不能复盘储位！", 1);
        }
        
        $update = array();
        $update['product_number'] = 0;
        $update['update_admin'] = NULL;
        $update['update_date'] = NULL;
        $this->inventory_model->update_inventory_details($update, $inventory_id, $location_id);
        
        sys_msg('恢复成功', 0, array(
            array('text'=>'继续查看', 'href'=>'inventory/detail/'.$inventory_id), 
            array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /*
     * 查询储位商品信息。
     */
    public function get_location_products() {
        auth('inventory_scan');
        
        $location_name = trim($this->input->post('location_name'));
        if (!$location_name) {
            sys_msg('未扫描储位！', 1);
        }
        
        $inventory_id = trim($this->input->post('inventory_id'));
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        
        $this->load->model('location_model');
        $location = $this->location_model->get_location(array(
            'location_name' => $location_name, 'depot_id' => $inventory->depot_id));
        if (!$location) {
            sys_msg('储位'.$location_name.'不存在！', 1);
        }
        
        if ($inventory->inventory_type == 0) {
            $shelf = $location->location_code1 . '-' . $location->location_code2;
            if (strtoupper($shelf) < strtoupper($inventory->shelf_from) || 
                strtoupper($shelf) > strtoupper($inventory->shelf_to)) {
                sys_msg('储位'.$location_name.'不在盘点范围之内！', 1);
            }
        } else if ($inventory->inventory_type == 1) { // 按储位盘
            if ($inventory->location_id != $location->location_id) {
                sys_msg('储位'.$location_name.'不在盘点范围之内！', 1);
            }
        }
        
        // 判断一个储位只能一个人盘点
        $only_one = $this->doCheckOnlyOneInventory($inventory_id, $location->location_id, $this->admin_id);
        if ($only_one == false) {
            sys_msg('一个储位只能由一个管理员进行盘点！', 1);
        }
        
        // 判断储位是否在此次拉取的盘点单中
        $in_inventory = $this->inventory_model->checkLocationInInventory($inventory_id, $location->location_id);
        /* 
         * 如果拉取盘点单时，此储位不存在，则分为两种情况考虑：
         * 1.拉取盘点单时，储位含有待入待出，不能盘点，这种情况，仓库应保证在盘点时这种状态不变化（即盘点时仓库不再做其他操作）。
         * 2.储位在系统中无商品，这种情况，可以盘点（盘赢）。
         */
        $this->load->model('depot_model');
        if ($in_inventory != TRUE) {
            // 判断是否有待入
            $in_results = $this->depot_model->filter_transaction_infos(array('location_id' => $location->location_id, 'trans_status' => 3));
            if (!empty($in_results)) {
                sys_msg('该储位含有待入商品，不能盘点！', 1);
            }

            // 判断是否有出库单待出
            $out_results = $this->depot_model->filter_transaction_infos(array('location_id' => $location->location_id, 'trans_type' => 2, 'trans_status' => 1));
            if (!empty($out_results)) {
                sys_msg('该储位含有出库待出商品，不能盘点！', 1);
            }

            // 判断是否有订单已拣货待出
            $order_picked_out_results = $this->inventory_model->filterOrderPickedOutTransactionInfos($location->location_id);
            if (!empty($order_picked_out_results)) {
                sys_msg('该储位含有订单已拣货待出商品，不能盘点！', 1);
            }
        }
        
        $result = array();
        
        // 获取储位的商品信息
        $result['list'] = $this->inventory_model->getProductBarcodesByLocation($location->location_id);
        
        // 获取此储位的库存量
        $inventory_number = $this->inventory_model->getInventoryNumberByLocation($location->location_id);
        $result['inventory_number'] = $inventory_number;
        
        // 获取此储位的已提交盘点数量
        $scaned_number = $this->inventory_model->getScanedNumberByLocation($inventory_id, $location->location_id);
        $result['scaned_number'] = $scaned_number;
        
        echo json_encode($result);
    }
    
    /*
     * 查询储位详情。
     */
    public function location_product_details() {
        auth('inventory_scan');
        
        $location_name = trim($this->input->post('location_name'));
        if (!$location_name) {
            sys_msg('未扫描储位！', 1);
        }
        
        $inventory_id = trim($this->input->post('inventory_id'));
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        
        $this->load->model('location_model');
        $location = $this->location_model->get_location(array(
            'location_name' => $location_name, 'depot_id' => $inventory->depot_id));
        if (!$location) {
            sys_msg('储位'.$location_name.'不存在！', 1);
        }
        
        // 获取储位的商品信息，包含待入待出和实际库存等
        $this->load->model('depot_model');
        $data = $this->depot_model->location_info_scan($location->location_id);
        
        $result = array();
        $result['list'] = $this->load->view('depot/inventory/scan_data', array('products' => $data['list']), TRUE);
        
        echo json_encode($result);
    }
    
    public function checkProviderBarcode() {
        auth('inventory_scan');
        
        $provider_barcode = trim($this->input->post('provider_barcode'));
        if (!$provider_barcode) {
            sys_msg('商品条码为空！', 1);
        }
        
        $this->load->model('product_model');
        $product_sub = $this->inventory_model->check_barcode_exist($provider_barcode);
        
        if (empty($product_sub)) {
            // TODO: 如何记录日志？
            sys_msg('商品条码[' . $provider_barcode . ']不存在，请下架！', 1);
        } else {
            echo json_encode(array('err' => 0));
        }
    }
    
    /*
     * 添加盘点商品。
     */
    public function add_details() {
        auth('inventory_scan');
        
        $inventory_id = trim($this->input->post('inventory_id'));
        $location_name = trim($this->input->post('location_name'));
        $product_barcode_ary = $this->input->post('product_barcode_ary');
        $product_number_ary = $this->input->post('product_number_ary');
        $empty = $this->input->post('empty');

        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        } else if (!$location_name) {
            sys_msg('未扫描储位！', 1);
        }
        
        if ($empty != 'true') {
            if (empty($product_barcode_ary) || empty($product_number_ary)) {
                sys_msg('未扫描商品！', 1);
            }
        }
        
        if ($inventory->status != 1) {
            sys_msg("盘点状态不是已确认，不能盘点！", 1);
        }
        
        $this->load->model('location_model');
        $location = $this->location_model->get_location(array(
            'location_name' => $location_name, 'depot_id' => $inventory->depot_id));
        if (!$location) {
            sys_msg('储位'.$location_name.'不存在！', 1);
        }
        
        if ($inventory->inventory_type == 0) {
            $shelf = $location->location_code1 . '-' . $location->location_code2;
            if (strtoupper($shelf) < strtoupper($inventory->shelf_from) || 
                strtoupper($shelf) > strtoupper($inventory->shelf_to)) {
                sys_msg('储位'.$location_name.'不在盘点范围之内！', 1);
            }
        } else if ($inventory->inventory_type == 1) { // 按储位盘
            if ($inventory->location_id != $location->location_id) {
                sys_msg('储位'.$location_name.'不在盘点范围之内！', 1);
            }
        }
        
        if ($empty != 'true') { // 储位不为空，出入/更新盘点的商品。
            $this->inventory_model->insert_inventory_details(
                    $inventory, $location, $product_barcode_ary, $product_number_ary, $this->admin_id);
        } else { // 储位为空，更新此储位上的所有商品为已盘点。
            $update = array();
            $update['product_number'] = 0;
            $update['update_admin'] = $this->admin_id;
            $update['update_date'] = date('Y-m-d H:i:s');
            $this->inventory_model->update_inventory_details($update, $inventory_id, $location->location_id);
        }
        
        // 计算库存数量
        $inventory_number = $this->inventory_model->getInventoryNumberByLocation($location->location_id);
        // 计算盘点数量
        $scaned_number = $this->inventory_model->getScanedNumberByLocation($inventory_id, $location->location_id);
        
        $data = array('inventory_number'=> $inventory_number ? $inventory_number : 0, 'scaned_number' => $scaned_number);
        echo json_encode($data);
    }
    
    /*
     * 导出盘点清单。
     * UNUSED
     */
    public function export($inventory_id) {
        auth(array('inventory_export'));
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        
        $list = $this->inventory_model->all_inventory_product_list($inventory);
        
        $title = array('储位ID', '商品ID', '颜色ID', '尺寸ID', 
            '储位编码', '商品名称', '商品条码', '颜色名称', 
            '颜色编码', '尺寸名称', '尺寸编码', '库存数量', '盘点数量');
        
        $this->load->helpers('excel');
        export_excel_xml($inventory->inventory_sn, array($title, $list));
    }
    
    /*
     * 导入盘点商品。
     * UNUSED
     */
    public function import() {
        auth('inventory_import');
        
        $inventory_id = trim($this->input->post('inventory_id'));
        
        // 上传文件验证
        $this->load->library('upload');
        if (!$_FILES["inventory_file"]["name"]) {
            sys_msg("请选择要上传的文件", 1);
        }
        if($_FILES["inventory_file"]["type"] != 'text/xml') {
            sys_msg("请上传XML格式的文件", 1);
        }
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        if ($inventory->status != 1) {
            sys_msg("盘点状态不是已确认，不能导入！", 1);
        }
        
        $this->load->helpers('excelxml');
        $inventory_detail_ary = read_xml($_FILES["inventory_file"]["tmp_name"]);
        if (count($inventory_detail_ary) <= 0) {
            sys_msg('文件中无要导入的数据！', 1);
        }
        
        $this->inventory_model->import_details($inventory, array_slice($inventory_detail_ary, 1), $this->admin_id);
        
        sys_msg('导入成功', 0, array(
            array('text'=>'继续查看', 'href'=>'inventory/detail/'.$inventory_id), 
            array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /*
     * 生成盘点清单商品。
     */
    public function generate($inventory_id) {
        auth('inventory_generate');
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        if ($inventory->status != 0) {
            sys_msg("盘点状态不是未确认，不能再生成盘点清单！", 1);
        }
        
        // 拉盘点清单
        $this->inventory_model->generate_details($inventory);
        
        sys_msg('生成成功', 0, array(array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /*
     * 生成盘点结果差异商品。
     */
    public function generate_diff($inventory_id) {
        auth('inventory_generate_diff');
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        }
        if ($inventory->status != 1) {
            sys_msg("盘点状态不是已确认，不能生成差异商品！", 1);
        }
        
        $this->inventory_model->generate_diff($inventory, $this->admin_id);
        
        sys_msg('生成成功', 0, array(
            array('text'=>'继续查看', 'href'=>'inventory/detail/'.$inventory_id), 
            array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /*
     * 生成盘点差异商品的出入库单据。
     */
    public function generate_invoice($inventory_id) {
        auth('inventory_generate_invoice');
        
        $inventory = $this->inventory_model->get_inventory(array('inventory_id'=>$inventory_id));
        if (!$inventory) {
            sys_msg('记录不存在！', 1);
        } else if ($inventory->status != 1) {
            sys_msg("盘点状态不是已确认，不能生成差异出入库单据！", 1);
        } else if (empty($inventory->diff_admin)) {
            sys_msg("请先生成盘点差异商品，再生成差异出入库单据！", 1);
        }
        
        $this->inventory_model->generate_invoice($inventory, $this->admin_id);
        
        sys_msg('生成成功', 0, array(
            array('text'=>'继续查看', 'href'=>'inventory/detail/'.$inventory_id), 
            array('text'=>'返回列表','href'=>'inventory/index')));
    }
    
    /* ---- private methods ------------------------------------------------- */
    /*
     * 查询所有管理员。
     */
    private function doGetAllAdmins() {
        $this->load->model('admin_model');
        return $this->admin_model->all_admin();
    }
    
    /*
     * 查询所有仓库。
     */
    private function doGetAllDepots() {
        $this->load->model('depot_model');
        $all_depots = $this->depot_model->all_depot(null);
        $depot_arr = array();
        foreach($all_depots as $item){
            $depot_arr[$item->depot_id] = $item->depot_name;
        }
        return $depot_arr;
    }
    
    /*
     * 检查货架的合法性。
     */
    private function doCheckShelf($depot_id, $shelf_from, $shelf_to) {
        if ($shelf_from && !$shelf_to) {
            sys_msg('请设置盘点结束货架！', 1);
        } else if (!$shelf_from && $shelf_to) {
            sys_msg('请设置盘点开始货架！', 1);
        } else if ($shelf_from > $shelf_to) {
            sys_msg('盘点开始货架应小于等于结束货架！', 1);
        }
        
        $from_location_ary = explode("-", $shelf_from);
        $to_location_ary = explode("-", $shelf_to);
        if (count($from_location_ary) != 2) {
            sys_msg('开始货架格式有误！', 1);
        } else if (count($to_location_ary) != 2) {
            sys_msg('结束货架格式有误！', 1);
        }
        
        $this->load->model('location_model');
        $from_location = $this->location_model->query_locations(array(
            'location_code1' => $from_location_ary[0],'location_code2' => $from_location_ary[1],'depot_id' => $depot_id));
        if (count($from_location) <= 0) {
            sys_msg('开始货架不存在！', 1);
        }
        
        $to_location = $this->location_model->query_locations(array(
            'location_code1' => $to_location_ary[0],'location_code2' => $to_location_ary[1],'depot_id' => $depot_id));
        if (count($to_location) <= 0) {
            sys_msg('结束货架不存在！', 1);
        }
    }
    
    /*
     * 检查指定的储位是否存在。
     */
    private function doCheckLocation($location_name, $depot_id) {
        if (empty($location_name)) {
            sys_msg('请选择储位！', 1);
        }
        
        $this->load->model('location_model');
        $location = $this->location_model->get_location(array(
            'location_name' => $location_name, 'depot_id' => $depot_id));
        if (!$location) {
            sys_msg('储位'.$location_name.'在指定的仓库中不存在！', 1);
        }

        return $location->location_id;
    }
    
    /*
     * 生成一个唯一的盘点编号。
     */
    private function doGenerateInventorySn() {
        srand();
        
        $inventory_sn = $this->doGenerateSn();
        while ($this->inventory_model->get_inventory(array('inventory_sn' =>  $inventory_sn))) {
            $inventory_sn = $this->doGenerateSn();
        }
        
        return $inventory_sn;
    }
    
    /*
     * 盘点编号格式如：PD2013032012345 (PD+日期+5位随机数)
     */
    private function doGenerateSn() {
        return $inventory_sn = 'PD' . date('Ymd') . rand(10000, 99999);
    }
    
    /*
     * 检查盘点单是否已生成。
     */
    private function checkInventoryDetailGenerated($inventory) {
        if ($inventory->gen_admin > 0 && !empty($inventory->gen_date)) {
            return true;
        }
        return false;
    }
    
    /*
     * 判断一个储位是否只有一个管理员进行盘点。
     */
    private function doCheckOnlyOneInventory($inventory_id, $location_id, $admin_id) {
        $row = $this->inventory_model->doGetUpdateAdmin($inventory_id, $location_id);
        if (empty($row)) {
            return true;
        } else {
            if ($row->update_admin == $admin_id) {
                return true;
            } else {
                return false;
            }
        }
    }
    
}

?>
