<?php

/**
 * Order
 */
class Order_user_shipping_fee extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id)
            redirect('index/login');
        $this->time = date('Y-m-d H:i:s');
        $this->load->model('return_user_shipping_fee_model');
        $this->load->helper('order');
    }

    public function index() {
        auth('order_user_shipping_fee_view');
        $filter = $this->uri->uri_to_assoc(3);
        if ($this->input->is_ajax_request()) {
            $filter['return_sn'] = trim($this->input->post('return_sn'));
            $filter['order_sn'] = trim($this->input->post('order_sn'));
            $filter['finance_status'] = intval($this->input->post('finance_status'));
            $filter['sort_by'] = 't.return_id';
            $filter['sort_order'] = 'desc';
            $filter = get_pager_param($filter);
            $data = $this->return_user_shipping_fee_model->query($filter);
            $data['full_page'] = FALSE;
            $data['perm_delete'] = check_perm('order_user_shipping_fee_delete');
            $data['perm_check'] = check_perm('order_user_shipping_fee_check');
            $data['perm_edit'] = check_perm('order_user_shipping_fee_edit');
            $data['content'] = $this->load->view('order/user_shipping_fee/index', $data, TRUE);
            $data['error'] = 0;
            unset($data['list']);
            echo json_encode($data);
            return;
        }
        $data['perm_check'] = check_perm('order_user_shipping_fee_check');
        $data['full_page'] = TRUE;
        $this->load->view('order/user_shipping_fee/index', $data);
    }

    public function edit($return_id) {
        auth('order_user_shipping_fee_edit');
        $data = $this->return_user_shipping_fee_model->get($return_id);
        if (!$data) {
            sys_msg('记录不存在', 1);
        }
        $shipping_name_list = $this->return_user_shipping_fee_model->get_all_shipping();
        $this->load->vars('shipping_name_list', $shipping_name_list);
        $this->load->vars('perm_edit', check_perm('order_user_shipping_fee_edit'));
        $this->load->vars('uncheck', $data->finance_admin == 0 ? true : false);
        $this->load->vars('row', $data);
        $this->load->view('order/user_shipping_fee/edit');
    }

    public function proc_edit() {
        auth('order_user_shipping_fee_edit');
        $return_id = intval($this->input->post('return_id'));
        $data = $this->return_user_shipping_fee_model->filter(array('return_id' => $return_id));
        if (!$data) {
            sys_msg('记录不存在', 1);
        }
        if ($data->finance_admin != 0) {
            sys_msg('已经财审,不能修改!', 1);
        }
        $update['user_shipping_fee'] = intval($this->input->post('user_shipping_fee'));
        ;
        $this->return_user_shipping_fee_model->update($update, $return_id);
        sys_msg('操作成功', 0, array(array('text' => '继续编辑', 'href' => 'order_user_shipping_fee/edit/' . $return_id), array('text' => '返回列表', 'href' => 'user_shipping_fee')));
    }

    //未完成
    public function delete($return_id) {
        auth('order_user_shipping_fee_delete');
        $data = $this->return_user_shipping_fee_model->filter(array('return_id' => $return_id));
        if (!$data) {
            sys_msg('记录不存在', 1);
        }
        $this->return_user_shipping_fee_model->delete($return_id);
        sys_msg('操作成功', 0, array(array('text' => '返回列表', 'href' => 'user_shipping_fee')));
    }

    public function batch_check() {
        if (!check_perm('order_user_shipping_fee_check')) {
            $result['err'] = 1;
            $result['msg'] = '没有权限!';
            echo json_encode($result);
            return;
        }
        $return_ids = $this->input->post('return_ids');
        $this->db->query('BEGIN');
        foreach ($return_ids as $return_id) {
            $user_shipping_fee = $this->return_user_shipping_fee_model->filter(array('return_id' => $return_id));
            $this->load->model('return_model');
            $return_info = $this->return_model->filter(array('return_id' => $return_id));
            if (!$return_info) {
                $result['err'] = 1;
                $result['msg'] = '有个退货单未找到,请刷新列表重试!';
                $this->db->query('ROLLBACK');
                echo json_encode($result);
                return;
            }
            //退货单未财审不能继续
            if ($return_info->finance_admin == 0) {
                $result['err'] = 1;
                $result['msg'] = '有个退货单未财审,请刷新列表重试!';
                $this->db->query('ROLLBACK');
                echo json_encode($result);
                return;
            }
            if (!$user_shipping_fee) {
                $result['err'] = 1;
                $result['msg'] = '有条记录未找到,请刷新列表重试!';
                $this->db->query('ROLLBACK');
                echo json_encode($result);
                return;
            }
            if ($user_shipping_fee->finance_admin != 0) {
                $result['err'] = 1;
                $result['msg'] = '有条记录已经被财审,请刷新列表重试!';
                $this->db->query('ROLLBACK');
                echo json_encode($result);
                return;
            }
            //定义需要修改的属性
            $update = array();
            $update['finance_admin'] = intval($this->admin_id);
            $update['finance_date'] = date('Y-m-d H:i:s');
            //为用户充值余额,user_account_log插入一条，update用户表
            //查询订单用户
            $this->load->model('order_model');
            $order = $this->order_model->filter(array('order_id' => $user_shipping_fee->order_id));
            if (empty($order)) {
                $result['err'] = 1;
                $result['msg'] = '有订单不存在,请刷新列表重试!';
                $this->db->query('ROLLBACK');
                echo json_encode($result);
                return;
            }
            //user_account_log插入一条
            $data['user_id'] = intval($order->user_id);
            $data['user_money'] = round(floatval($user_shipping_fee->user_shipping_fee), 2);
            $data['change_code'] = 'shipping_fee_back';
            $data['change_desc'] = '退还用户退货运费';
            $data['create_admin'] = $this->admin_id;
            $data['create_date'] = date('Y-m-d H:i:s');
            //更新用户表
            $this->load->model('user_model');
            $check_user = $this->user_model->filter(array('user_id' => $data['user_id']));
            if (empty($check_user)) {
                $result['err'] = 1;
                $result['msg'] = '有用户不存在,请刷新列表重试!';
                $this->db->query('ROLLBACK');
                echo json_encode($result);
                return;
            }
            $param['user_money'] = $check_user->user_money + $data['user_money'];
            $this->load->model('user_account_log_model');
            //插入order_action 
            $order_action['return_id']=$return_info->return_id;
            $order_action['return_status']=$return_info->return_status;
            $order_action['shipping_status']=$return_info->shipping_status;
            $order_action['pay_status']=$return_info->pay_status;
            $this->return_model->insert_action($order_action,'返还用户退货运费');
            $this->return_user_shipping_fee_model->update($update, $return_id);
            $this->user_model->update($param, $data['user_id']);
            $this->user_account_log_model->insert($data);
        }
        $this->db->query('COMMIT');
        $result['err'] = 0;
        echo json_encode($result);
    }

    //单条财审,废弃使用批量财审
    public function check($return_id) {
        if (!check_perm('order_user_shipping_fee_check')) {
            $result['err'] = 1;
            $result['msg'] = '没有权限!';
            echo json_encode($result);
            return;
        }
        $data = $this->return_user_shipping_fee_model->filter(array('return_id' => $return_id));
        if (!$data) {
            $result['err'] = 1;
            $result['msg'] = '找不到这条记录!';
            echo json_encode($result);
            return;
        }
        if ($data->finance_admin != 0) {
            $result['err'] = 1;
            $result['msg'] = '已经被财审!';
            echo json_encode($result);
            return;
        }
        //定义需要修改的属性
        $update = array();
        $update['finance_admin'] = intval($this->admin_id);
        $update['finance_date'] = date('Y-m-d H:i:s');
        $this->return_user_shipping_fee_model->update($update, $return_id);
        $result['err'] = 0;
        $this->load->model('admin_model');
        $admin_user = $this->admin_model->filter(array('admin_id' => intval($this->admin_id)));
        $result['admin_name'] = $admin_user->admin_name;
        $result['finance_date'] = $update['finance_date'];
        echo json_encode($result);
    }

}