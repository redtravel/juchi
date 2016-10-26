<?php
#doc
#	classname:	Index
#	scope:		PUBLIC
#
#/doc
class User extends CI_Controller
{
    public function __construct ()
    {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        // $this->company_type = array(0=> '其他',1=> '医疗器械经营单位',2=> '医疗器械使用单位');
        $this->company_type = array('请选择', '医疗器械经营单位', '医疗器械使用单位', '民营口腔医疗机构', '公立口腔医疗机构 ', '牙科经销商', '牙科制造企业', '技工/加工厂', '牙科培训机构', '科研院校单位', '医科院校师生', '大众消费者', '其它');
        if ( ! $this->admin_id )
        {
                redirect('index/login');
        }
        $this->load->model('user_model');
        $this->load->model('useraddress_model');

    }

    public function index ()
    {
        auth('user_view');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_user_perm());
        $filter = $this->uri->uri_to_assoc(3);
        $mobile = $this->input->post('mobile');
        if(!empty($mobile))$filter['mobile'] = $mobile;
        
        $user_name = $this->input->post('user_name');
        if(!empty($user_name))$filter['user_name'] = $user_name;
        $user_type = $this->input->post('user_type');
        if(!empty($user_type))$filter['user_type'] = $user_type;
        $email = $this->input->post('email');
        if(!empty($email))$filter['email'] = $email;
        $email_validated = $this->input->post('email_validated');
        if(!empty($email_validated))$filter['email_validated'] = $email_validated;
        $mobile_checked = $this->input->post('mobile_checked');
        if(!empty($mobile_checked))$filter['mobile_checked'] = $mobile_checked;
        $is_use = $this->input->post('is_use');
        if(!empty($is_use))$filter['is_use'] = $is_use;
        $start_time = $this->input->post('start_time');
        if(!empty($start_time))$filter['start_time'] = $start_time;
        $end_time = $this->input->post('end_time');
        if(!empty($end_time))$filter['end_time'] = $end_time;
        
        $filter = get_pager_param($filter);
        $data = $this->user_model->user_list($filter);
        $data['perm_change_discount'] = check_perm(array('discount_manage'));
        if ($this->input->post('is_ajax'))
        {
                $data['full_page'] = FALSE;
                $this->load->vars('list',$data);
                $data['content'] = $this->load->view('user/list', $data, TRUE);
                $data['error'] = 0;
                unset($data['list']);
                echo json_encode($data);
                return;
        }
        $data['full_page'] = TRUE;
        $this->load->view('user/list', $data);
    }

    public function ajax_region(){
        $type = intval($this->input->post('type'))+1;
        $parent_id = intval($this->input->post('parent_id'));
        $this->load->model('region_model');
        $arr = $this->region_model->all_region(array('region_type'=> $type , 'parent_id' => $parent_id));
        echo json_encode(array('list'=>$arr,'type'=>$type));
    }
    public function ajax_get_user_discount($uid){
        if (!check_perm(array('discount_manage'))) 
            exit('没有权限!');
        $user  = $this->user_model->filter(Array('user_id' => $uid ));
        $html = '<div style="text-align:center;margin-top:48px"><label>会员折扣:&nbsp;</label><input id="discount" type="text" class="textbox" value="'.$user->discount_percent.'" /></div>';
        echo $html;

    }
    public function ajax_change_discount(){
        if (!check_perm(array('discount_manage')))
             exit('没有权限!');
        $uid = $this->input->post('uid');
        $discount = $this->input->post('discount');
        if ($this->user_model->update(array('discount_percent'=>$discount), $uid))
            echo 1;
        else
            echo 0;
        $data['user_id'] = intval($uid);
        $data['user_money'] = 0;
        $data['change_code'] = 'change_member_discount';
        $data['change_desc'] = '会员折扣改为'.$discount;
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        $this->load->model('user_account_log_model');
        $this->user_account_log_model->insert($data);
        //echo $uid, ' ', $discount;
    }
    
    public function disable($user_id)
    {
        auth('user_edit');
        $user_id = intval($user_id);
        $check = $this->user_model->filter(array('user_id' => $user_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->user_model->update(array('is_use' => 1) , $user_id);
        sys_msg('操作成功',2,array(array('href'=>'/user/index','text'=>'返回列表页')));
    }
    
    public function able($user_id)
    {
        auth('user_edit');
        $user_id = intval($user_id);
        $check = $this->user_model->filter(array('user_id' => $user_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->user_model->update(array('is_use' => 0) , $user_id);
        sys_msg('操作成功',2,array(array('href'=>'/user/index','text'=>'返回列表页')));
    }


    public function add()
    {
        auth('user_edit');
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_user_perm());
        $this->load->model('region_model');
        $province = $this->region_model->all_region(array('region_type' => 1));
        $this->load->vars('province' , $province);
        $this->load->vars('company_type_list' ,  $this->company_type);
        $this->load->view('user/add');
    }

    public function proc_add()
    {
        auth('user_edit');
        $email = trim($this->input->post('email'));
        if(!empty($email)){
            $data['email'] = $email;
            $check = $this->user_model->filter(array('email' => $data['email']));
            if(!empty($check)){
                sys_msg('记录已经存在',1);
                return;
            }
        }
        $data['user_name'] = $this->input->post('user_name');
        $data['real_name'] = $this->input->post('real_name');
        $data['password'] = m_encode($this->input->post('password'));
        $data['sex'] = $this->input->post('sex');
        $data['birthday'] = $this->input->post('birthday');
        $mobile = trim($this->input->post('mobile'));
        if(!empty($mobile)){
            $data['mobile'] = $mobile;
            $check_moible = $this->user_model->filter(array('mobile' => $data['mobile']));
            if(!empty($check_moible)){
                sys_msg('手机号已经存在',1);
                return;
            }
        }
        $data['identity_code'] = $this->input->post('identity_code');
        $data['passport_code'] = $this->input->post('passport_code');
        $data['user_type'] = $this->input->post('user_type') - 2;
        $data['discount_percent'] = $this->input->post('discount_percent');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        $data['company_name'] = $this->input->post('company_name');
        $data['company_type'] = $this->input->post('company_type');
        $data['company_position'] = $this->input->post('company_position');
        $param['consignee'] = $this->input->post('consignee');
        $param['province'] = $this->input->post('province');
        $param['city'] = $this->input->post('city');
        $param['district'] = $this->input->post('district');
        $param['address'] = $this->input->post('address');
        $param['zipcode'] = $this->input->post('zipcode');
        $param['tel'] = $this->input->post('tel');
        $param['mobile'] = $this->input->post('mobile_address');
        $param['is_used'] = 1;
        $param['create_admin'] = $this->admin_id;
        $param['create_date'] = date('Y-m-d H:i:s');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('email', 'email', 'trim|valid_email');
        $this->form_validation->set_rules('user_name', '用户名', 'trim|required');
        $this->form_validation->set_rules('password', '密码', 'trim|required');
        $this->form_validation->set_rules('user_type', '会员类型', 'trim|required');
        $this->form_validation->set_rules('discount_percent', '会员折扣率', 'trim|required');
        if (!$this->form_validation->run()) {
           sys_msg(validation_errors(), 1);
        }
        if(empty($mobile) && empty($email)){
           sys_msg('邮箱或者手机必填一个',1);
           return;
        }
        $param['user_id'] = $this->user_model->insert($data);
        if(!empty($param['consignee']) and !empty($param['province']) and !empty($param['city']) and !empty($param['address']) and !empty($param['zipcode']) and (!empty($param['tel']) or !empty($param['mobile']))){
            $address_id = $this->useraddress_model->insert($param);
            $this->user_model->update(array('address_id' => $address_id), $param['user_id']);
        }
        sys_msg('操作成功',2,array(array('href'=>'/user/index','text'=>'返回列表页')));
    }

    public function edit($user_id)
    {
        auth(array('user_edit','user_view'));
        $this->load->helper('perms_helper');
        $this->load->vars('perms' , get_user_perm());
        $user_id = intval($user_id);
        $arr = $this->user_model->filter(array('user_id' => $user_id));
        if(empty($arr)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->model('region_model');
        $this->load->model('useraddress_model');
        $address = $this->useraddress_model->all_address(array('user_id' => $user_id));
        $province = $this->region_model->all_region(array('region_type' => 1));
        $city = $this->region_model->all_region(array('region_type' => 2));
        $district = $this->region_model->all_region(array('region_type' => 3));
        foreach($city as $item){
            $city[$item->region_id] = $item;
        }
        foreach($province as $item){
            $province[$item->region_id] = $item;
        }
        foreach($district as $item){
            $district[$item->region_id] = $item;
        }
        $this->load->vars('company_type_list' ,  $this->company_type);
        $this->load->vars('province' , $province);
        $this->load->vars('city' , $city);
        $this->load->vars('district' , $district);
        $this->load->vars('user_arr' , $arr);
        $this->load->vars('address' , $address);
        $this->load->view('user/edit');
    }

    public function proc_edit($user_id){
        auth('user_edit');
        $user_id = intval($user_id);
        $email_type = $this->input->post('email_type');
        $email = trim($this->input->post('email'));
        if($email_type == 0 && !empty($email)){
            $data['email'] = $email;
            $check = $this->user_model->filter(array('email' => $data['email']));
            if(!empty($check)){
                sys_msg('记录已经存在',1);
                return;
            }
        }
        $mobile_type = $this->input->post('mobile_type');  // 原手机号 是否为空 ,已有值不可修改
        // $mobile_type = 0;   //均可修改 v 2015-10-16
        $mobile = trim($this->input->post('mobile'));
        if($mobile_type == 0 && !empty($mobile)){
            $data['mobile'] = $mobile;
            $check_moible = $this->user_model->filter(array('mobile' => $data['mobile']));
            if(!empty($check_moible)){
                sys_msg('手机号已经存在',1);
                return;
            }
        }
        $data['user_name'] = $this->input->post('user_name');
        $data['real_name'] = $this->input->post('real_name');
        $password = $this->input->post('password');
        if(!empty($password)){
            $data['password'] = m_encode($password);
        }
        $data['sex'] = $this->input->post('sex');
        $data['birthday'] = $this->input->post('birthday');
        $data['identity_code'] = $this->input->post('identity_code');
        $data['passport_code'] = $this->input->post('passport_code');
        $data['user_type'] = $this->input->post('user_type') - 2;
        // $data['discount_percent'] = $this->input->post('discount_percent');
        $data['company_name'] = $this->input->post('company_name');
        $data['company_type'] = $this->input->post('company_type');
        $data['company_position'] = $this->input->post('company_position');
        $data['is_use'] = $this->input->post('is_use');
        $arr = $this->user_model->filter(array('user_id' => $user_id));
        if(empty($arr)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->load->library('form_validation');
        $this->form_validation->set_rules('user_name', '用户名', 'trim|required');
        // $this->form_validation->set_rules('discount_percent', '会员折扣率', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->user_model->update($data , $user_id);
        sys_msg('操作成功',2,array(array('href'=>'/user/index','text'=>'返回列表页')));
    }

    public function edit_address(){
        auth('useraddr_edit');
        $address_id = intval($this->input->post('address_id'));
        $data['consignee'] = $this->input->post('consignee');
        $data['province'] = $this->input->post('province');
        $data['city'] = $this->input->post('city');
        $data['district'] = $this->input->post('district');
        $data['address'] = $this->input->post('address');
        $data['zipcode'] = $this->input->post('zipcode');
        $data['tel'] = $this->input->post('tel');
        $data['mobile'] = $this->input->post('mobile');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('consignee', '联系人', 'trim|required');
        $this->form_validation->set_rules('province', '省', 'trim|required');
        $this->form_validation->set_rules('city', '市', 'trim|required');
        $this->form_validation->set_rules('district', '区/县', 'trim|required');
        $this->form_validation->set_rules('address', '详细地址', 'trim|required');
        $this->form_validation->set_rules('zipcode', '邮编', 'trim|required');

        if (!$this->form_validation->run()) {
                echo json_encode(array('check' => 1,'msg'=>''));
                return ;
        }
        if(empty($data['tel']) and empty($data['mobile'])){
                echo json_encode(array('check' => 1,'msg'=>''));
                return ;
        }
        $this->load->model('useraddress_model');
        $this->useraddress_model->update($data , $address_id);
        $this->load->model('region_model');
        $country = $this->region_model->all(array('parent_id' => 0));
        $province = $this->region_model->all(array('region_type' => 1));
        $city = $this->region_model->all(array('region_type' => 2));
        $district = $this->region_model->all(array('region_type' => 3));
        foreach($country as $item){
            $country_arr[$item->region_id] = $item;
        }
        foreach($city as $item){
            $city[$item->region_id] = $item;
        }
        foreach($province as $item){
            $province[$item->region_id] = $item;
        }
        foreach($district as $item){
            $district[$item->region_id] = $item;
        }
        echo json_encode(
            array(  'check' => 2 ,
                    'province' => $province[$data['province']]->region_name ,
                    'city' => $city[$data['city']]->region_name ,
                    'district' => $district[$data['district']]->region_name,
                    'msg'=>''
            )
        );
    }

    public function add_address($user_id){
        auth('useraddr_edit');
        $this->load->model('region_model');
        $province = $this->region_model->all_region(array('region_type' => 1));
        $this->load->vars('province' , $province);
        $this->load->vars('user_id' , $user_id);
        $this->load->view('user/add_address');
    }
    
    function proc_add_address($user_id){
        auth('useraddr_edit');
        $data['user_id'] = intval($user_id);
        $data['consignee'] = $this->input->post('consignee');
        $data['province'] = $this->input->post('province');
        $data['city'] = $this->input->post('city');
        $data['district'] = $this->input->post('district');
        $data['address'] = $this->input->post('address');
        $data['zipcode'] = $this->input->post('zipcode');
        $data['tel'] = $this->input->post('tel');
        $data['mobile'] = $this->input->post('mobile');
        $data['create_admin'] = $this->admin_id;
        $data['create_date'] = date('Y-m-d H:i:s');
        $this->load->library('form_validation');
        $this->form_validation->set_rules('consignee', '联系人', 'trim|required');
        $this->form_validation->set_rules('province', '省', 'trim|required');
        $this->form_validation->set_rules('city', '市', 'trim|required');
        $this->load->model('region_model');
        $arr_dis = $this->region_model->filter(array('parent_id' => $data['city']));
        if(!empty($arr_dis)){
            $this->form_validation->set_rules('district', '区/县', 'trim|required');
        }
        $this->form_validation->set_rules('address', '详细地址', 'trim|required');
        $this->form_validation->set_rules('zipcode', '邮编', 'trim|required');
        if (!$this->form_validation->run()) {
                sys_msg(validation_errors(), 1);
        }
        $this->load->model('useraddress_model');
        $is_used_a = $this->useraddress_model->filter(array('user_id'=>$data['user_id']));
        if(empty($is_used_a)){
            $data['is_used'] = 1;
        }else{
            $data['is_used'] = 0;
        }
        $this->useraddress_model->insert($data);
        sys_msg('操作成功',2,array(array('href'=>'/user/edit/'.$data['user_id'],'text'=>'返回列表页')));
    }

    function del_address($address_id,$user_id){
        auth('useraddr_edit');
        $address_id = intval($address_id);
        $test = $this->input->post('test');
        $user_id = intval($user_id);
        $this->load->model('useraddress_model');
        $check = $this->useraddress_model->filter(array('address_id' => $address_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $is_used_a = $this->useraddress_model->filter_r(array('user_id'=>$user_id));
        if(count($is_used_a) > 1 && $check->is_used == 1){
            sys_msg('此地址为默认地址',1);
            return;    
        }
        if($test) sys_msg('');
        $this->useraddress_model->delete($address_id);
        sys_msg('删除成功',2,array(array('href'=>'/user/edit/'.$user_id,'text'=>'返回列表页')));
    }

    function edit_default_address($address_id,$user_id){
        auth('useraddr_edit');
        $address_id = intval($address_id);
        $user_id = intval($user_id);
        $this->load->model('useraddress_model');
        $check = $this->useraddress_model->filter(array('address_id' => $address_id));
        if(empty($check)){
            sys_msg('记录不存在',1);
            return;
        }
        $this->useraddress_model->update_condition(array('is_used' => 0) , $user_id);
        $this->useraddress_model->update(array('is_used' => 1) , $address_id);
        sys_msg('操作成功',2,array(array('href'=>'/user/edit/'.$user_id,'text'=>'返回列表页')));
    }

    public function ajax_edit_address(){
        $address_id = intval($this->input->post('address_id'));
        $this->load->model('useraddress_model');
        $arr = $this->useraddress_model->filter_array(array('address_id' => $address_id));

        $this->load->model('region_model');
        $country = $this->region_model->all(array('parent_id' => 0));
        $province = $this->region_model->all(array('region_type' => 1));
        $city = $this->region_model->all(array('region_type' => 2));
        $district = $this->region_model->all(array('region_type' => 3));
        foreach($country as $item){
            $country_arr[$item->region_id] = $item;
        }
        foreach($city as $item){
            $city[$item->region_id] = $item;
        }
        foreach($province as $item){
            $province[$item->region_id] = $item;
        }
        foreach($district as $item){
            $district[$item->region_id] = $item;
        }
        $after = '<tr class="address_check" id="address_after_'.$address_id.'" class="row" style=" background-color:#CCC"><td></td><td colspan="10"><br />
联系人：<input name="consignee" class="textbox require" value="'.$arr[0]['consignee'].'" type="text" /> 省：
<select name="province" id="province" onchange="return change_region(1,this.value,\'city\')">
<option value="'.$arr[0]['province'].'">'.$province[$arr[0]['province']]->region_name.'</option>';
        foreach($province as $item){
            $after .= '<option value="'.$item->region_id.'">'.$item->region_name.'</option>';
        }
$after .= '</select> 市：<select name="city" id="city" onchange="return change_region(2,this.value,\'district\')"><option value="">--请选择--</option>
<option  selected="selected" value="'.$arr[0]['city'].'">'.$city[$arr[0]['city']]->region_name.'</option></select>县/区<select name="district" id="district"><option value="">--请选择--</option><option selected="selected" value="'.$arr[0]['district'].'">'.$district[$arr[0]['district']]->region_name.'</option></select>详细地址：
<input name="address" class="textbox require" type="text" value="'.$arr[0]['address'].'" /><br /><br />邮系编：<input value="'.$arr[0]['zipcode'].'" name="zipcode" class="textbox require" type="text" />
电话：<input value="'.$arr[0]['tel'].'" name="tel" class="textbox require" type="text" />&nbsp;&nbsp;手机：<input value="'.$arr[0]['mobile'].'" name="mobile" class="textbox require" type="text" />
<br /><br /></td><td><a onclick="ajax_edit_address('.$arr[0]['address_id'].');"  style="cursor:pointer;" title="确定">确定</a> | <a style="cursor:pointer;" onclick="remove('.$arr[0]['address_id'].');" title="取消">取消</a></td>
</tr>';
        echo json_encode(array('after' => $after,'msg'=>''));
    }

}
