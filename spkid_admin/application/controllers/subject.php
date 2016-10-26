<?php
/*
 * 商品活动专题管理。
 */
class Subject extends CI_Controller {
    
    function __construct () {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if(!$this->admin_id) redirect('index/login');
        $this->load->model('subject_model');
    }
    
    public function index() {
        auth('subject_view');
        
        $filter = array();
        $subject_title = trim($this->input->post('subject_title'));
        if (!empty($subject_title)) {
            $filter['subject_title'] = $subject_title;
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
        $data = $this->subject_model->subject_list($filter);
        $this->load->vars('perm_delete', check_perm('subject_delete'));
        
        $this->load->model('admin_model');
        $admin_arr = $this->admin_model->all_admin();
        $this->load->vars('admin_arr',$admin_arr);
        
        if ($this->input->post('is_ajax')) {
            $data['full_page'] = FALSE;
            $data['content'] = $this->load->view('subject/subject_list', $data, TRUE);
            $data['error'] = 0;
            unset($data['list']);
            echo json_encode($data);
            return;
        }
        $data['full_page'] = TRUE;
        
        $this->load->view('subject/subject_list', $data);
    }
    
    public function add() {
        auth('subject_edit');
        $this->load->view('subject/subject_add');
    }
    
    public function proc_add() {
        auth('subject_edit');
        $update = array();
        $update['subject_type'] = 1; // 1-subject,2-edm
        $update['start_date'] = $this->input->post('start_date');
        $update['end_date'] = $this->input->post('end_date');
        $update['page_file'] = $this->input->post('page_file');
        $update['subject_title'] = $this->input->post('subject_title');
        $update['subject_keyword'] = $this->input->post('subject_keyword');
        $update['page_desc'] = $this->input->post('page_desc');
        $update['create_admin'] = $this->admin_id;
        $update['create_date'] = date('Y-m-d H:i:s');

        $subject = $this->subject_model->get_subject(array('page_file'=>$update['page_file']));
        if ($subject) {
            sys_msg('生成名称重复，请重新填写！', 1);
        }

        $subject_id = $this->subject_model->insert_subject($update);

        sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'subject/edit/'.$subject_id), array('text'=>'返回列表','href'=>'subject/index')));
    }
    
    public function edit($subject_id) {
        auth(array('subject_edit','subject_view'));
        $subject = $this->subject_model->get_subject(array('subject_id'=>$subject_id));
        if (!$subject) {
            sys_msg('记录不存在！', 1);
        }
        $this->load->vars('row', $subject);
        $this->load->vars('perm_edit', check_perm('subject_edit'));
        $this->load->view('subject/subject_edit');
    }
    
    public function proc_edit() {
        auth('subject_edit');
        
        $update = array();
        $update['start_date'] = $this->input->post('start_date');
        $update['end_date'] = $this->input->post('end_date');
        $update['page_file'] = $this->input->post('page_file');
        $update['subject_title'] = $this->input->post('subject_title');
        $update['subject_keyword'] = $this->input->post('subject_keyword');
        $update['page_desc'] = $this->input->post('page_desc');

        $subject_id = intval($this->input->post('subject_id'));
        $subject = $this->subject_model->get_subject(array('subject_id'=>$subject_id));
        if (!$subject) {
            sys_msg('记录不存在！', 1);
        }

        $check_subject = $this->subject_model->get_subject(array('page_file'=>$update['page_file'], 'subject_id !='=>$subject_id));
        if ($check_subject) {
            sys_msg('生成名称重复，请重新填写！', 1);
        }

        $this->subject_model->update_subject($update, $subject_id);

        sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'subject/edit/'.$subject_id), array('text'=>'返回列表','href'=>'subject/index')));
    }
    
    public function delete($subject_id) {
        auth('subject_delete');
        
        $subject = $this->subject_model->get_subject(array('subject_id'=>$subject_id));
        if (!$subject) {
            sys_msg('记录不存在！', 1);
        }
        
        $this->subject_model->delete_modules($subject_id);
        $this->subject_model->delete_subject($subject_id);
        
        sys_msg('删除成功', 0, array(array('text'=>'返回列表', 'href'=>'subject/index')));
    }
    
    public function generate_file($subject_id) {
        auth('subject_generate');
        
        $subject = $this->subject_model->get_subject(array('subject_id'=>$subject_id));
        if (!$subject) {
            sys_msg('记录不存在！', 1);
        }
        
        // get template module contents
        $template_content_list = array();
        $module_list = $this->subject_model->subject_modules($subject_id);
        foreach ($module_list as $key => $module) {
            $template_content_list[$key] = $this->do_get_template_content($module);
        }
        
        // remove source file
        $source_file = $this->do_get_subject_file($subject->page_file);
        if (file_exists($source_file)) {
            unlink($source_file);
        }
        
        // generate new file
        $this->load->helper('file');
        $generate_file = $this->do_get_subject_file($subject->page_file);
        $this->load->helper("common");
        // TODO 手机版本、PC版本的头部是不一样的。
        // 将文件名字传递过去，将来好做进一步处理
        $header = curl(FRONT_HOST.'/api/get_header/'.$subject->page_file); 
        $footer = curl(FRONT_HOST."/api/get_footer/".$subject->page_file);
        $footer .= $footer;
        $file_data = $this->load->view(
                'subject/template_global', 
                array("subject" => $subject, "template_content_list" => $template_content_list, "header" => $header, "footer" => $footer), 
                TRUE);
        if (write_file($generate_file, $file_data)) {
            // record generater
            $update = array();
            $update['gen_admin'] = $this->admin_id;
            $update['gen_date'] = date('Y-m-d H:i:s');
            $this->subject_model->update_subject($update, $subject_id);
            
             sys_msg('生成成功', 0, array(array('text'=>'返回列表', 'href'=>'subject/index')));
        } else {
             sys_msg('生成文件失败！', 1);
        }
    }
    
    public function remove_file($subject_id) {
        // TODO: not need to flush squid cache right now.
        auth('subject_remove');
        
        $subject = $this->subject_model->get_subject(array('subject_id'=>$subject_id));
        if (!$subject) {
            sys_msg('记录不存在！', 1);
        }
        
        $source_file = $this->do_get_subject_file($subject->page_file);
        if (file_exists($source_file)) {
            if(unlink($source_file)) {
                sys_msg('删除成功', 0, array(array('text'=>'返回列表', 'href'=>'subject/index')));
            } else {
                sys_msg('删除文件失败！', 1);
            }
        } else {
            sys_msg('文件已删除', 0, array(array('text'=>'返回列表', 'href'=>'subject/index')));
        }
    }
    
    public function manage($subject_id) {
        auth('subject_manage');
        
        $subject = $this->subject_model->get_subject(array('subject_id'=>$subject_id));
        if (!$subject) {
            sys_msg('记录不存在！', 1);
        }
        $module_list = $this->subject_model->subject_modules($subject_id);
        
        $this->load->vars('row', $subject);
        $this->load->vars('module_list', $module_list);
        $this->load->vars('perm_manage', check_perm('subject_manage'));
        
        $this->load->view('subject/subject_manage');
    }
    
    public function proc_add_module() {
        auth('subject_manage');
        
        $update = array();
        $module_type = trim($this->input->post('module_type'));
        if (empty($module_type) || $module_type <= 0) {
            sys_msg('请选择添加的模块类型！', 1);
        } else {
            $update['module_type'] = $module_type;
        }
        $update['subject_id'] = $this->input->post('subject_id');
        $update['module_location'] = $this->input->post('module_location');
        $update['sort_order'] = $this->input->post('sort_order');
        $update['create_admin'] = $this->admin_id;
        $update['create_date'] = date('Y-m-d H:i:s');

        $this->subject_model->insert_module($update);

        sys_msg('操作成功', 0, array(array('text'=>'继续添加','href'=>'subject/manage/'.$update['subject_id']), array('text'=>'返回列表','href'=>'subject/index')));
    }
    
    public function edit_module($module_id) {
        auth('subject_manage');
        
        $module = $this->subject_model->get_module(array('module_id'=>$module_id));
        if (!$module) {
            sys_msg('记录不存在！', 1);
        }
        
        $this->load->vars('row', $module);
        
        $view = $this->do_get_module_view($module);
        $this->load->view($view);
    }

    public function proc_edit_module() {
        auth('subject_manage');
        
        $update = array();
        $update['module_title'] = $this->input->post('module_title');
        $update['module_location'] = $this->input->post('module_location');
        $update['sort_order'] = $this->input->post('sort_order');
        $update['product_num'] = $this->input->post('product_num');
        $update['module_text'] = $this->do_get_module_text($this->input->post('module_type'));
        
        $module_id = $this->input->post('module_id');
        $this->subject_model->update_module($update, $module_id);

        sys_msg('操作成功', 0, array(array('text'=>'继续编辑','href'=>'subject/edit_module/'.$module_id), array('text'=>'返回列表','href'=>'subject/index')));
    }
    
    public function delete_module($module_id) {
        auth('subject_manage');
        
        $module = $this->subject_model->get_module(array('module_id'=>$module_id));
        if (!$module) {
            sys_msg('记录不存在！', 1);
        }
        $this->subject_model->delete_module($module_id);
        
        sys_msg('删除成功', 0, array(array('text'=>'继续管理', 'href'=>'subject/manage/'.$module->subject_id)));
    }
    
    public function search_goods() {
        auth('subject_manage');
        
        $filter = array();
        
        $brand = trim($this->input->post('brand'));
        if ($brand) $filter['brand_id'] = $brand;
        
        $category_id = intval($this->input->post('category_id'));
        if ($category_id) $filter['category_id'] = $category_id;
        
        $style_id = intval($this->input->post('style_id'));
        if ($style_id) $filter['style_id'] = $style_id;

        $season_id = intval($this->input->post('season_id'));
        if ($season_id) $filter['season_id'] = $season_id;
        
        $color_id = intval($this->input->post('color_id'));
        if ($color_id) $filter['color_id'] = $color_id;
        
        $size_id = intval($this->input->post('size_id'));
        if ($size_id) $filter['size_id'] = $size_id;
        
        $product_sex = intval($this->input->post('product_sex'));
        if ($product_sex) $filter['product_sex'] = $product_sex;
        
        $product_sn = trim($this->input->post('product_sn'));
        if ($product_sn) $filter['product_sn'] = $product_sn;

        $batch_code = trim($this->input->post('batch_code'));
        if ($batch_code) $filter['batch_code'] = $batch_code;

        $min_price = intval($this->input->post('min_price'));
        if ($min_price) $filter['min_price'] = $min_price;

        $max_price = intval($this->input->post('max_price'));
        if ($max_price) $filter['max_price'] = $max_price;

        $min_gl_num = intval($this->input->post('min_gl_num'));
        if ($min_gl_num) $filter['min_gl_num'] = $min_gl_num;

        $max_gl_num = intval($this->input->post('max_gl_num'));
        if ($max_gl_num) $filter['max_gl_num'] = $max_gl_num;
        
        $filter = get_pager_param($filter);
        $data = $this->subject_model->search_goods($filter);
        
        $data['content'] = $this->load->view('subject/result_search_goods', $data, TRUE);
        $data['error'] = 0;
        unset($data['list']);
        echo json_encode($data);
        return;
    }
    
    public function preview($subject_id) {
        $subject = $this->subject_model->get_subject(array('subject_id'=>$subject_id));
        if (!$subject) {
            sys_msg('记录不存在！', 1);
        }
        
        echo file_get_contents($this->do_get_subject_file($subject->page_file));
    }
    
    /* ---- private methods ------------------------------------------------- */
    private function do_get_subject_file($page_file) {
        return ZHUANTI_HTML_PATH.$page_file.'.html';
    }
    
    private function do_get_module_text($module_type) {
        $module_text = null;
        switch ($module_type) {
            case 4 : // 自定义内容
                $module_text = $this->input->post('module_text');
                break;
            case 6 : // 单品
                $goods_id_ary = $this->input->post('added_goods_id');
                $sort_order_ary = $this->input->post('added_sort_order');
                if (!empty($goods_id_ary) && count($goods_id_ary) > 0) {
                    $module_text_ary = array();
                    foreach ($goods_id_ary as $key => $value) {
                        $module_text_ary[$value] = $sort_order_ary[$key];
                    }
                    $module_text = serialize($module_text_ary);
                }
                break;
            case 11 : // 正在抢购活动
                $rush_id_ary = $this->input->post('rush_id');
                if (!empty($rush_id_ary) && count($rush_id_ary) > 0) {
                    $module_text = implode(",", $rush_id_ary);
                }
                break;
            default :
                sys_msg('模块类型有误，请检查！', 1);
        }
        return $module_text;
    }
    
    private function do_get_module_view($module) {
        $module_view = null;
        switch ($module->module_type) {
            case 4 :
                $this->load->library('ckeditor');
                $module_view = 'subject/module_custom';
                break;
            case 6 :
                $module_text_ary = unserialize($module->module_text);
                if (!empty($module_text_ary) && count($module_text_ary) > 0) {
                    $added_goods_id = array();
                    foreach ($module_text_ary as $key => $value) {
                        $added_goods_id[] = $key;
                    }
                    
                    $this->load->model('product_model');
                    $added_products = $this->product_model->batch_query_goods_by_ids($added_goods_id);
                    foreach ($added_products as $key => $value) {
                        $product_id = $value->product_id;
                        $value->sort_order = $module_text_ary[$product_id];
                    }
                    
                    uasort($added_products, 'product_sort'); // sort
                    $this->load->vars('added_products', $added_products);
                }
                
                $this->load->model('brand_model');
                $this->load->model('category_model');
                $this->load->model('style_model');
                $this->load->model('season_model');
                $this->load->vars('all_brand', $this->brand_model->all_brand());
                $this->load->vars('all_category', $this->category_model->all_category());
                $this->load->vars('all_style', $this->style_model->all_style());
                $this->load->vars('all_season', $this->season_model->all_season());
                $module_view = 'subject/module_goods';
                break;
            case 11 :
                $this->load->vars('selected_rushings', explode(",", $module->module_text));
                $this->load->model("rush_model");
                $this->load->vars('all_rushings', $this->rush_model->query_all_rushings());
                $module_view = 'subject/module_rushing';
                break;
            default :
                sys_msg('模块类型有误，请检查！', 1);
        }
        return $module_view;
    }
    
    private function do_get_template_content($module) {
        $template_content = null;
        switch ($module->module_type) {
            case 4 : // 自定义内容
                $template_content = $this->load->view('subject/template_custom', array("template_content" => $module->module_text), TRUE);
                break;
            case 6 : // 单品
                $module_text_ary = unserialize($module->module_text);
                if (!empty($module_text_ary) && count($module_text_ary) > 0) {
                    //TODO:这里最好先将$module_text_ary排序后再批量查数据库，再排序。
                    $added_goods_id = array();
                    foreach ($module_text_ary as $key => $value) {
                        $added_goods_id[] = $key;
                    }
                    
                    $this->load->model('product_model');
                    $added_products = $this->product_model->batch_query_goods_color_by_ids($added_goods_id);
                    foreach ($added_products as $key => $value) {
                        $product_id = $value->product_id;
                        $value->sort_order = $module_text_ary[$product_id];
                    }
                    
                    uasort($added_products, 'product_sort'); // sort
                    
                    // filter by pruduct_num
                    $product_num = $module->product_num;
                    if ($product_num > 0 && count($added_products) > $product_num) {
                        array_slice($added_products, 0, $product_num, TRUE);
                    }
                    
                    if (!empty($added_products) && count($added_products) > 0) {
                        $template_content = $this->load->view('subject/template_goods', array("added_products" => $added_products), TRUE);
                    }
                }
                break;
            case 11 : // 正在抢购活动
                $show_rush_ids = explode(",", $module->module_text);
                if (!empty($show_rush_ids) && count($show_rush_ids) > 0) {
                    $this->load->model('rush_model');
                    $show_rushings = $this->rush_model->batch_query_by_ids($show_rush_ids);
                    $this->do_set_rush_end_day($show_rushings);
                    $this->do_set_image_before_url($show_rushings);
                    $template_content = $this->load->view('subject/template_rushing', array("show_rushings" => $show_rushings), TRUE);
                }
                break;
            default :
                sys_msg('模块类型有误，请检查！', 1);
        }
        return $template_content;
    }
    
    private function do_set_rush_end_day($rushes) {
        foreach($rushes as $rush) {
            $time_diff = strtotime($rush->end_date) - time();
            $end_day = $time_diff<86400 ? 1 : ceil($time_diff/86400);
            $rush->end_day = $end_day;
        }
    }
    
    private function do_set_image_before_url($rushes) {
        foreach($rushes as $rush) {
            $image_before = $rush->image_before_url;
            $arr = explode(".", $image_before);
            if (count($arr) == 2) {
                $rush->image_before_url = $arr[0] . "_3." . $arr[1];
            }
        }
    }
    
    public function get_nav() {
        $this->load->helper("common");
        $header = curl(FRONT_HOST.'/api/get_navigation'); 
        echo $header;
    }
    
}

function product_sort($p1, $p2) {
    $sort_order1 = $p1->sort_order;
    $sort_order2 = $p2->sort_order;
    if ($sort_order1 > $sort_order2) {
        return -1;
    } else if ($sort_order1 < $sort_order2) {
        return 1;
    } else {
        $product_id1 = $p1->product_id;
        $product_id2 = $p2->product_id;
        if ($product_id1 == $product_id2) {
            return 0;
        } else {
            return $product_id1 > $product_id2 ? 1 : -1;
        }
    }
}

?>
