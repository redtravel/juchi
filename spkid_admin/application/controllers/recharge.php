<?php

#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc

class Recharge extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        $this->load->model('user_recharge_model');
    }

    public function index() {
//        error_reporting(E_ERROR | E_PARSE);
//        $flag = $_GET['flag']; //1减款；2充值
        $flag = $this->input->get('flag');

        if ($flag == 1) {
            auth('recharge_minus_upload'); // 减款的上传权限
        } else {
            auth('recharge_add_upload'); // 充值的上传权限
        }
        $data['flag'] = $flag;
        $read_dir = $flag == 1 ? ACCOUNT_MINUS_FILE_DIR : ACCOUNT_ADD_FILE_DIR; //IMPORT_MONEY_BALANCE_RELATIVE_PATH 
        $create_dir = IMPORT_MONEY_BALANCE_RELATIVE_PATH . $read_dir;
        $file_cvs = $flag == 1 ? IMPORT_MONEY_BALANCE_DIR . '/account_minus.csv' : IMPORT_MONEY_BALANCE_DIR . '/account_add.csv';
        if (!file_exists($create_dir)) {
            mkdir($create_dir, 0777);
        }
//        if ($_GET['act'] == 'add') {
        if($this->input->get('act')=='add'){
            if (file_exists($file_cvs)) {
                header("Location:".ERP_HOST."/recharge?act=display&flag=" . $flag);
            }
            $data['ur_here'] = '批量修改用户账户金额';
            $archives = $this->getLatestArchive($flag);
            $data['archives'] = $archives;
//            var_dump($data);exit;

            $this->load->view('user_recharge/add_minus_list', $data);
//        } elseif ($_POST['act'] == 'upload') {
          } elseif ($this->input->post('act') == 'upload'){
            /* 检查权限 */
//            admin_priv('account_manage,upload_file');
//            $flag = $_GET['flag']; //1减款；2充值
            $flag = $this->input->get('flag');
            $file_cvs = $flag == 1 ? IMPORT_MONEY_BALANCE_DIR . 'account_minus.csv' : IMPORT_MONEY_BALANCE_DIR . 'account_add.csv';
            if (sizeof($_FILES) < 1) {
                echo "<script>alert ('请选择要上传的文件');history.go(-1);</script>";
                exit;
            }
            $unload_name = $_FILES['unload_file']['name'];
            $arr_unload_name = @explode('.', $unload_name);
            $file_type = strtolower($arr_unload_name[count($arr_unload_name) - 1]);
            if ($file_type != 'csv') {
                echo "<script>alert ('上传的文件必须是csv文件');history.go(-1);</script>";
                exit;
            }
            $rs = @copy($_FILES['unload_file']['tmp_name'], $file_cvs);
            if (!file_exists($file_cvs)) {
                echo '导入失败,没有找到文件';
                exit;
            }
            @unlink($upload_err_file);
            header("Location:".ERP_HOST."/recharge?act=display&flag=" . $flag);
//        } elseif ($_GET['act'] == 'display') {
          } elseif($this->input->get('act')=='display'){  
            $accounts_minus = Array();
            $unavail_accounts = Array();
            $avail_accounts = Array();
            $table_heads = Array();
            $avail_user_ids = Array();
            $file_cvs = $flag == 1 ? IMPORT_MONEY_BALANCE_DIR . ACCOUNT_MINUS_FILE_NAME : IMPORT_MONEY_BALANCE_DIR . ACCOUNT_ADD_FILE_NAME;
            $arr_cont = @file($file_cvs);
            $this->load->model('admin_model');
            $arr_cont = (array) $arr_cont;
            foreach ($arr_cont as $key => $value) {
                $arr_rs = @explode(",", $value);
                if (!is_array($arr_rs))
                    continue;

                $arr_rs = $this->_tmpIconv('GB2312', 'UTF-8', array_map('trim', $arr_rs));
                if ($key == 0) {
                    $table_heads = $arr_rs; //_tmpIconv('GB2312','UTF-8',$arr_rs);
                    $table_heads[] = '账户原金额';
                    continue;
                }

                //通过登录名检查用户是否存在
                if (!empty($arr_rs[ACCOUNT_FIELD]) 
                    && ($userId = $this->admin_model->check_user(trim($arr_rs[ACCOUNT_FIELD]), null)) 
                    && !in_array($userId, $avail_user_ids)) {
                    array_push($avail_user_ids, $userId);
                    $accounts_minus[$userId] = trim($arr_rs[MONEY_FIELD]);
                    array_unshift($arr_rs, $userId);
                    array_push($avail_accounts, $arr_rs);
                } else if (!empty($arr_rs[ACCOUNT_FIELD])) {
                    array_unshift($arr_rs, ' ');
                    array_push($unavail_accounts, $arr_rs);
                }
            }
            $count = count($avail_user_ids);
            $account_moneys = array();
            foreach ($avail_user_ids as $value) {
                $account_moneys[] = $this->admin_model->get_accounts_money($value);
            }
            
            foreach ($account_moneys AS $am)//$account_moneys通过id验证的用户余额（数据库中）
                foreach ($avail_accounts AS $key => $value) //$avail_accounts通过id验证的用户信息（csv中）
                    if ($value[0] == $am['user_id']) {
                        array_push($avail_accounts[$key], $am['user_money']); //向$avail_counts新增对应数据库中余额
                        if ($flag==1&&$value[MONEY_FIELD + 1] > $am['user_money']) {
                            array_push($unavail_accounts, $avail_accounts[$key]);
                            unset($avail_accounts[$key]);
                        }
                    }
            if (!empty($unavail_accounts)) {
                $this->archive_upload_error_file($table_heads, $unavail_accounts);
                $data['archive_upload_error'] = 1;
            }

            $data['avail_accounts'] = $avail_accounts;
            $data['avail_accounts_encoded'] = base64_encode(serialize($avail_accounts));
            $data['unavail_accounts'] = $unavail_accounts;
            $data['unavail_accounts_encoded'] = base64_encode(serialize($unavail_accounts));
            $data['accounts_minus'] = base64_encode(serialize($accounts_minus));
            $data['table_heads_encoded'] = base64_encode(serialize($table_heads));
            $data['table_heads'] = $table_heads;
            if (empty($unavail_accounts) && $flag == 1) {
                $data['minus_button'] = 1;
            }
            if (empty($unavail_accounts) && $flag == 2) {
                $data['minus_button'] = 1;
            }

            $data['tablunavail_accountse_heads_encoded'] = base64_encode(serialize($table_heads));
            $data['user_ids'] = base64_encode(serialize($avail_user_ids));
            $archives = $this->getLatestArchive($flag);
            $data['archives'] = $archives;
            if ($flag == 1) {
                $data['show_button'] = check_perm('recharge_minus_audit'); // 减款的上传权限
            } else {
                $data['show_button'] = check_perm('recharge_add_audit'); // 充值的上传权限
            }
            $this->load->view('user_recharge/add_minus_list', $data);
        }
    }

    public function minus() {
//        $flag = $_POST['flag']; //1减款；2充值
          $flag = $this->input->post('flag');
        
        if ($flag == 1) {
            auth('recharge_minus_audit'); // 减款的上传权限
        } else {
            auth('recharge_add_audit'); // 充值的上传权限
        }

        $avail_accounts = $this->_getEncodeParam('avail_accounts_encoded');
        $unavail_accounts = $this->_getEncodeParam('unavail_accounts_encoded');
        $accounts_minus = $this->_getEncodeParam('accounts_minus');
        $userIds = $this->_getEncodeParam('user_ids');
        $table_heads = $this->_getEncodeParam('table_heads_encoded'); //$table_heads = _tmpIconv('UTF-8','GB2312',$table_heads);
        $this->load->model('admin_model');
        $userIds_str = implode(',', $userIds);
//            var_dump($accounts_minus);
//            exit;
        $str = $flag == 1 ? '-' : '+';
        $account_minus_result = $this->admin_model->trans_minus($userIds_str, $accounts_minus, $str);

        // 写成文档
        $this->archive_account_minus($table_heads, $avail_accounts, $unavail_accounts, $account_minus_result, $flag);
        header('Location: '.ERP_HOST.'/recharge?act=add&flag=' . $flag);
    }

    /**
     * 批量减账户金额
     *
     * @access public
     * @param Array( $userId=> MONEY, ... );
     *
     * @return TRUE
     */
    public function do_op_account_money($account_op = Array(), $op = '', $reason = '批量操作用户金额') {
        if (empty($account_op))
            return true;
        $reason = 'admin_id=' . $_SESSION['admin_id'] . '：' . $reason;
        foreach ($account_op AS $userId => $money) {
            log_account_change($userId, $op . $money, 0, 0, 0, $reason, ACT_ADJUSTING);
        }
        return true;
    }

    public function _getEncodeParam($key, $defaultValue = Array()) {
        $a = @$_POST[$key];
        return empty($a) ? $defaultValue : unserialize(base64_decode($a));
    }

    public function _tmpIconv($cf, $ct, $table_heads) {
        $a1 = array();
        $a2 = array();
        for ($i = 0; $i < sizeof($table_heads); $i++) {
            array_push($a1, $cf);
            array_push($a2, $ct);
        }
        return array_map('iconv', $a1, $a2, $table_heads);
    }

    /**
     * 将数据写成文档
     */
    public function archive_account_minus($table_heads, $avail_accounts, $unavail_accounts, $account_minus_result, $flag) {
//        global $smarty, $archive_dir, $file_cvs, $upload_data;
        $c_dir = $flag == 1 ? ACCOUNT_MINUS_FILE_DIR : ACCOUNT_ADD_FILE_DIR;
        if (!empty($unavail_accounts)) {
            foreach ($unavail_accounts AS $key => $value)
                array_push($unavail_accounts[$key], '登录名不正确');
        }
        $accounts = Array();
        $ok_user_ids = array_keys($account_minus_result[0]);
        foreach ($avail_accounts AS $a) {
            $user_id = $a[0];
            if (in_array($user_id, $ok_user_ids))
                array_push($accounts, $a);
            else {
                $a[sizeof($a) - 1] = '账户金额不足：' . $a[sizeof($a) - 1];
                array_push($unavail_accounts, $a);
            }
        }
        array_unshift($table_heads, '用户ID');
        $data['table_heads'] = $table_heads;
        $data['xtag'] = '?';
        $date = date('YmdHis');
        if (sizeof($accounts) > 0) {
            $data['accounts'] = $accounts;
            $data['title'] = $date . '会员退款批量操作成功明细';
            $data['th_old_money'] = '原账户金额';
            $data['table_heads'] = $table_heads;

            $ok_content = $this->load->view('user_recharge/account_minus_output', $data, true);
            $fn = fopen(IMPORT_MONEY_BALANCE_DIR . $c_dir . $date .'-'.$this->session->userdata('admin_name'). '-ok' . '.xml', 'w');
            fwrite($fn, $ok_content, strlen($ok_content));
            fclose($fn);
        }
        if (sizeof($unavail_accounts) > 0) {
            array_push($table_heads, '失败原因');
            $data['th_old_money'] = '';
            $data['table_heads'] = $table_heads;
            $data['accounts'] = $unavail_accounts;
            $data['title'] = $date . '会员退款批量操作失败明细';
            $ok_content = $this->load->view('user_recharge/account_minus_output', $data, true);
            $fn = fopen(IMPORT_MONEY_BALANCE_DIR . $c_dir . $date . '-fail' . '.xml', 'w');
            fwrite($fn, $ok_content, strlen($ok_content));
            fclose($fn);
//            $fn = fopen($archive_dir . '/' . $date . '-' . $_SESSION['admin_name'] . '-fail' . '.xml', 'w');
//            fwrite($fn, $fail_content, strlen($fail_content));
//            fclose($fn);
        }
        $file_cvs = $flag == 1 ? 'account_minus.csv' : 'account_add.csv';
        rename(IMPORT_MONEY_BALANCE_DIR . $file_cvs, IMPORT_MONEY_BALANCE_DIR .$c_dir. 'bake-' . $date . '.csv');
    }

    public function getLatestArchive2($flag) {
        $read_dir = $flag == 1 ? ACCOUNT_MINUS_FILE_DIR : ACCOUNT_ADD_FILE_DIR;
        $data = glob(IMPORT_MONEY_BALANCE_DIR . $read_dir . date('Ym') . '*');
        if (date('j') < 10)
            $data = array_merge($data, glob(IMPORT_MONEY_BALANCE_DIR . $read_dir . date('Ym2', strtotime('-1 month')) . '*'));

        $result = Array();
        for ($i = 0; $i < sizeof($data); $i++) {
            $filename = basename($data[$i]);
            $filenameAry = explode('-', $filename);
            if (!isset($result[$filenameAry[0]])){
            $result[$filenameAry[0]] = Array($filenameAry[0], $filenameAry[0] . "-" . $filenameAry[1], '&nbsp;');
            }
            if (substr($filenameAry[2], 0, 2) == 'ok'){
                $result[$filenameAry[0]][3] = '<a href="' . (IMPORT_MONEY_BALANCE_RELATIVE_PATH . $read_dir . $filename) . '" target="_blank">下11载(右键另存为)</a>';
                $result[$filenameAry[0]][4] ='&nbsp;';
            }
            else{
                $result[$filenameAry[0]][3] = '&nbsp;';
            $result[$filenameAry[0]][4] = '<a href="' . (IMPORT_MONEY_BALANCE_RELATIVE_PATH . $read_dir . $filename) . '" target="_blank">下22载(右键另存为)</a>';
            }
        }
        asort($result);
        return array_reverse($result);
    }
    function getLatestArchive($flag) {
        $read_dir = $flag == 1 ? ACCOUNT_MINUS_FILE_DIR : ACCOUNT_ADD_FILE_DIR;
        $data = glob(IMPORT_MONEY_BALANCE_DIR . $read_dir . date('Ym') . '*');
        if (date('j') < 10)
            $data = array_merge($data, glob(IMPORT_MONEY_BALANCE_DIR . $read_dir . date('Ym2', strtotime('-1 month')) . '*'));

        $result = Array();
        for ($i = 0; $i < sizeof($data); $i++) {
            $filename = basename($data[$i]);
            $filenameAry = explode('-', $filename);
//            var_dump($filenameAry);
            if (!isset($result[$filenameAry[0]]))
            $result[$filenameAry[0]] = Array($filenameAry[0], $filenameAry[1], '&nbsp;', '&nbsp;');
            if (substr($filenameAry[2], 0, 2) == 'ok'){
                $result[$filenameAry[0]][2] = '<a href="' . IMPORT_MONEY_BALANCE_RELATIVE_PATH . $read_dir . $filename . '" target="_blank">下载(右键另存为)</a>';
            }else{
                $result[$filenameAry[0]][3] = '<a href="' . IMPORT_MONEY_BALANCE_RELATIVE_PATH . $read_dir . $filename . '" target="_blank">下载(右键另存为)</a>';
            }
        }
        asort($result);
        return array_reverse($result);
    }

    public function archive_upload_error_file($table_heads, $unavail_accounts) {
        if (empty($unavail_accounts))
            return false;
        $data['table_heads'] = $table_heads;
        $data['xtag'] = '?';
        $date = date('YmdHis');
        array_push($table_heads, '失败原因');
        $data['th_old_money'] = '';
        $data['table_heads'] = $table_heads;
        $data['accounts'] = $unavail_accounts;
        $data['title'] = $date . '会员退款批量操作失败明细';
        $fail_content = $this->load->view('user_recharge/account_minus_output', $data, true);
        array_push($table_heads, '失败原因');
        $upload_err_file = '/' . 'upload_err_file.xml';
        $fn = fopen($upload_err_file, 'w');
        fwrite($fn, $fail_content, strlen($fail_content));
        fclose($fn);
    }

}
