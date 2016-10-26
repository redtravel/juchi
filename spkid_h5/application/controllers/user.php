<?php
/**
 *
 */
class User extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->user_id = $this->session->userdata('user_id');
		$this->time = date('Y-m-d H:i:s');
		$this->load->model('user_model');
		$this->load->library('user_obj');
	}

	# 会员中心首页
	public function index() {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control:no-cache,must-revalidate");
		header("Pragma:no-cache");

		$this->load->helper('order');

		if (!$this->user_id) {
			goto_login('user');
		}

		$user_id = $this->user_id;
		$user_info = $this->user_obj->get_profile($user_id);
		$user_info->voucher_num = $this->user_model->user_voucher_num($user_id); //现金券数
		$user_info->order_num = $this->user_model->user_order_num($user_id); //订单数量

		/** 会员等级*/

		/** 留言回复条数*/
		$user_info->liuyan_return_num = $this->user_model->liuyan_num($user_id);

		/** 是否完善个人信息*/

		$user_info->arr_invite_rank_count = $this->user_model->user_point_log($user_id);

		if (empty($user_info->arr_invite_rank_count)) {
			$user_info->arr_invite_rank = $this->user_obj->get_user_rank_point($user_id);
		}

		// 验证送积分
		$user_info->registerPoint = $this->user_obj->get_user_rank_point($user_id, 'regist_point');

		/** 订单
		$this->load->model('order_model');
		$order_list = $this->order_model->order_list(array('page_size'=>3),$user_id);
		$order = $this->order_model->get_wait_pay_ing_order_num($user_id);
		 */

		$this->load->view('mobile/user/index', array(
			'user' => $user_info,
		));

	}

	private function get_comment_by_type($comment_type = 0) {
		$html = '';
		if (empty($comment_type)) {
			return $html;
		}
		$this->load->model('liuyan_model');
		$filter = array(
			'comment_type' => $comment_type,
			'user_id' => $this->user_id,
			'page' => 0,
		);
		$data = $this->liuyan_model->liuyan_list($filter);
		$html = $this->load->view('mobile/liuyan/pingjia_list', array(
			'list' => $data['list'],
			'filter' => $filter,
		), TRUE);
		return $html;
	}

	public function setup() {	
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Cache-Control:no-cache,must-revalidate");
		header("Pragma:no-cache");
		
		$user_id = $this->user_id;

		//我的留言
		$liuyan_html = $this->get_comment_by_type(1);
		//我的询价
		$xunjia_html = $this->get_comment_by_type(4);
		//我的评价
		$pingjia_html = $this->get_comment_by_type(2);

		$this->load->view('mobile/user/setup', array('user_id' => $user_id,
			'liuyan_html' => $liuyan_html,
			'xunjia_html' => $xunjia_html,
			'pingjia_html' => $pingjia_html,
		));
	}
	public function save_advar() {
		$advar = $this->input->post('advar');
		$user_id = $this->user_id;
		$this->session->set_userdata('advar', $advar);
		$this->user_model->update(array('user_advar' => $advar), $user_id);
	}

	public function customers_center() {
		$user_id = $this->user_id;

		//我的留言
		$liuyan_html = $this->get_comment_by_type(1);
		//我的询价
		$xunjia_html = $this->get_comment_by_type(4);
		//我的评价
		$pingjia_html = $this->get_comment_by_type(2);

		$this->load->view('mobile/user/customers_center', array('user_id' => $user_id,
			'liuyan_html' => $liuyan_html,
			'xunjia_html' => $xunjia_html,
			'pingjia_html' => $pingjia_html,
		));
	}

	public function profile() {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Cache-Control:no-cache,must-revalidate");
        header("Pragma:no-cache");
		$this->load->model('product_model');
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);

		/** 会员等级*/
		if ($user_info->rank_id > 0 && $user_info->rank_id < 4) {
			$arr_user_rank = $this->user_model->user_rank_page();
			$user_info->next_min_points = $arr_user_rank[$user_info->rank_id + 1]->min_points - $user_info->paid_money;
			$user_info->next_min_points = number_format($user_info->next_min_points, 2, '.', '');
			$user_info->next_min_rank_name = $arr_user_rank[$user_info->rank_id + 1]->rank_name;
			foreach ($arr_user_rank as $key => $value) {
				if ($user_info->paid_money >= $value->min_points && $user_info->paid_money <= $value->max_points) {
					$user_info->next_rank_name = $value->rank_name;
				}
			}
		}

		/** 是否完善个人信息*/
		/*
			        if (empty($user_info->user_name) || empty($user_info->real_name) || empty($user_info->sex) || empty($user_info->birthday) || empty($user_info->favorite_category))
			        {
			            $user_info->no_profile=1;
			        }
			        $all_category = index_array($this->product_model->get_category(0), 'category_id');
			        if(isset($all_category[$user_info->favorite_category])) {
			            $user_info->favorite_category_name = $all_category[$user_info->favorite_category]['category_name'];
			        }else{
			            $user_info->favorite_category_name = '';
			        }
			        $user_info->arr_invite_rank_count = $this->user_model->user_point_log($user_id);

			        $user_info->arr_invite_rank = $this->user_obj->get_user_rank_point($user_id);
			        // 验证送积分
			        $user_info->registerPoint = $this->user_obj->get_user_rank_point($user_id,'regist_point');

					if ($user_info->address_id == 0)
					{
						$this->load->model('cart_model');
			        	$checkout['shipping'] = array('address_id'=>0,'country'=>1,'province'=>0,'city'=>0,'district'=>0);
			        	list($province_list,$city_list,$district_list) = $this->cart_model->cart_region($checkout['shipping']);
					} else
					{
						list($province_list,$city_list,$district_list) = array(array(),array(),array());
					}

					$user_info->baby_list = $this->user_model->get_user_baby_list($user_id);
		*/
		$company_type = array('请选择', '医疗器械经营单位', '医疗器械使用单位', '民营口腔医疗机构', '公立口腔医疗机构 ', '牙科经销商', '牙科制造企业', '技工/加工厂', '牙科培训机构', '科研院校单位', '医科院校师生', '大众消费者', '其它');
		if (property_exists($user_info, 'company_type') && '' != $user_info->company_type) {
			$my_type = $company_type[$user_info->company_type];
			unset($company_type[0]);
			unset($company_type[$user_info->company_type]);
			$values = array_keys($company_type);
			array_unshift($values, $user_info->company_type);
			array_unshift($company_type, $my_type);
			// echo $user_info->company_type;
		} else {
			$my_type = $company_type[0];
		}

		$this->load->view('mobile/user/profile', array(
			'user' => $user_info,
			'company_type' => $company_type,
			'values' => $values,
			'my_type' => $my_type,
			/*'province_list' => $province_list,
							'city_list' => $city_list,
							'district_list' => $district_list,
				            'category_list' => $all_category,
							'left_sel' =>42,
			*/
		));

	}

	public function profile_edit() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}
		$user_info = $this->user_obj->get_profile($user_id);

		$user_modify = array();
		$address_arr = array();
		$baby_list = array();
		$user_modify['email'] = trim($this->input->post('email'));
		$user_modify['mobile'] = trim($this->input->post('mobile'));
		if (empty($user_modify['email'])) {
			unset($user_modify['email']);
		}

		if (empty($user_modify['mobile'])) {
			unset($user_modify['mobile']);
		}

		$user_modify['user_name'] = trim($this->input->post('user_name'));
		$user_modify['real_name'] = trim($this->input->post('real_name'));
		$user_modify['sex'] = trim($this->input->post('sex'));
		$user_modify['favorite_category'] = intval($this->input->post('favoriteCategory'));
		$baby_list = $this->input->post('baby_list');
//		$user_modify['baby_name'] = trim($this->input->post('baby_name'));
		//		$user_modify['baby_sex'] = trim($this->input->post('baby_sex'));

		$birthdayYear = intval(trim($this->input->post('birthdayYear')));
		$birthdayMonth = intval(trim($this->input->post('birthdayMonth')));
		$birthdayDay = intval(trim($this->input->post('birthdayDay')));
		if (checkdate($birthdayMonth, $birthdayDay, $birthdayYear)) {
			$user_modify['birthday'] = $birthdayYear . '-' . trim($this->input->post('birthdayMonth')) . '-' . trim($this->input->post('birthdayDay'));
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您的生日不存在'));
			return;
		}
		if (empty($user_modify['real_name'])) {
			echo json_encode(array('error' => 1, 'msg' => '请填写您的真实姓名'));
			return;
		}
		if (empty($user_modify['favorite_category'])) {
			echo json_encode(array('error' => 1, 'msg' => '请选择您最喜欢的分类'));
			return;
		}

/*		$baby_birthdayYear = intval(trim($this->input->post('baby_birthdayYear')));
$baby_birthdayMonth = intval(trim($this->input->post('baby_birthdayMonth')));
$baby_birthdayDay = intval(trim($this->input->post('baby_birthdayDay')));
if (checkdate($baby_birthdayMonth,$baby_birthdayDay,$baby_birthdayYear))
{
$user_modify['baby_birthday'] = $baby_birthdayYear.'-'.trim($this->input->post('baby_birthdayMonth')).'-'.trim($this->input->post('baby_birthdayDay'));
} else
{
echo json_encode(array('error'=>1,'msg'=>'您宝宝的生日不存在'));
return;
}*/
		$j = 1;
		if (empty($baby_list)) {
			$baby_list = array();
		}

		foreach ($baby_list as $t => $baby) {
			if (!checkdate($baby['baby_birthdayMonth'], $baby['baby_birthdayDay'], $baby['baby_birthdayYear'])) {
				echo json_encode(array('error' => 1, 'msg' => '您选择的第' . $j . '个宝宝的生日不存在'));
				return;
			}
			$baby_name = trim($baby['baby_name']);
			$baby_sex = trim($baby['baby_sex']);
			if (empty($baby_name) || empty($baby_sex)) {
				echo json_encode(array('error' => 1, 'msg' => '您第' . $j . '个宝宝的姓名或姓别不能为空'));
				return;
			}

			$row = array('user_id' => $user_id, 'baby_name' => $baby_name, 'baby_sex' => $baby_sex, 'birthday' => $baby['baby_birthdayYear'] . "-" . $baby['baby_birthdayMonth'] . "-" . $baby['baby_birthdayDay']);
			$this->user_model->add_user_baby_info($row);
			$j++;
		}

		if (empty($user_modify['user_name']) || empty($user_modify['real_name']) || empty($user_modify['sex'])) {
			echo json_encode(array('error' => 1, 'msg' => '错误的参数'));
			return;
		}

		if ($user_info->address_id == 0 && 0) {
			$address_arr['province'] = 1;
			$address_arr['zipcode'] = '';
			$address_arr['province'] = trim($this->input->post('province'));
			$address_arr['city'] = trim($this->input->post('city'));
			$address_arr['district'] = trim($this->input->post('district'));
			$address_arr['address'] = trim($this->input->post('address'));
			$address_arr['user_id'] = $user_id;
			$address_arr['consignee'] = $user_modify['real_name'];
			$address_arr['mobile'] = empty($user_info->mobile) ? '' : $user_info->mobile;
			$address_arr['create_date'] = date('Y-m-d H:i:s');

			$address_arr['is_used'] = 1;
			$address_id = $this->user_model->insert_address($address_arr);
			if ($address_id > 0) {
				$user_modify['address_id'] = $address_id;
			} else {
				echo json_encode(array('error' => 1, 'msg' => '添加默认地址出错'));
				return;
			}
		}
		$this->user_model->update($user_modify, $user_id);

		$user = $this->user_model->filter(array('user_id' => $user_id));
		$this->session->set_userdata('user_name', $user->user_name);
		$this->session->set_userdata('union_sina', $user->union_sina);
		$this->session->set_userdata('union_zhifubao', $user->union_zhifubao);
		$this->session->set_userdata('email', $user->email);
		$this->session->set_userdata('mobile', $user->mobile);
		$this->session->set_userdata('user_type', $user->user_type);
		$this->session->set_userdata('email_validated', $user->email_validated);
		$this->session->set_userdata('mobile_checked', $user->mobile_checked);
		$this->session->set_userdata('discount_percent', round(floatval($user->discount_percent), 2));
		$this->user_obj->update_user_info();

		$point_amount = $this->user_obj->get_user_rank_point($user_id, 'profile_point');
		$msg_extra = '';
		if (!$this->user_model->point_type_exists($user_id, 'point_detail')) {
			$this->user_model->log_account_change($user_id, 0, $point_amount, $point_amount, '完善个人资料赠送的积分', 'point_detail');
			$msg_extra = ',您的' . $point_amount . "奖励积分已经发放";
		}

		$data = array('error' => 0, 'msg' => '用户信息更新成功' . $msg_extra);
		echo json_encode($data);
		return;
	}

	public function order() {
		$this->load->helper('order');
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			goto_login('user/order');
		}
		$this->load->model('order_model');
		$list1 = $this->order_model->order_simple_list($user_id, 1);
		$list2 = $this->order_model->order_simple_list($user_id, 1, 'pending');
		$list3 = $this->order_model->order_simple_list($user_id, 1, 'wait_shipping');
		$this->load->view('mobile/user/order', array('all' => $list1, 'pending' => $list2, 'wait_shipping' => $list3));
	}
	public function course() {
		$this->load->helper('order');
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			goto_login('user/order');
		}
		$this->load->model('order_model');
		$courses = $this->order_model->course_list($user_id);
		$this->load->view('mobile/user/course', array('courses' => $courses));
	}
	public function order_() {
		$this->load->helper('order');
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			goto_login('user/order');
		}
		$user_info = $this->user_obj->get_profile($user_id);

		$status = trim($this->input->post('status'));
		$status = empty($status) ? 1 : $status;
		$filter['order_status'] = $status;

		$page = trim($this->input->post('page'));
		if (!empty($page)) {
			$filter['page'] = $page;
		}

		$filter = get_pager_param($filter);

		/** 订单*/
		$this->load->model('order_model');
		$order_list = $this->order_model->order_list($filter, $user_id);
		if ($this->input->post('is_ajax')) {

			$data['content'] = $this->load->view('user/order', array(
				'user' => $user_info,
				'order_list' => $order_list['list'],
				'filter' => $order_list['filter'],
				'full_page' => FALSE,
				'order_status' => $status,
			), TRUE);
			$data['error'] = 0;
			$data['page_count'] = $order_list['filter']['page_count'];
			$data['page'] = $order_list['filter']['page'];
			$data['order_status'] = $status;
			echo json_encode($data);
			return;
		}

		$this->load->view('user/order', array(
			'title' => '我的订单',
			'user' => $user_info,
			'order_list' => $order_list['list'],
			'filter' => $order_list['filter'],
			'left_sel' => 11,
			'full_page' => TRUE,
			'order_status' => $status,
		));
	}
	public function edit() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		$info = $this->input->post();
		if ($info['user_name'] != $this->session->userdata('user_name') && $this->user_obj->is_username_register($info['user_name'])) {
			echo json_encode(array('res' => false, 'msg' => '此昵称已存在!'));
			exit();
		}
		unset($info['company_types']);
		$res = $this->user_model->update($info, $user_id);
		//注册送积分
		if (USE_REGIST_POINT && !$this->user_model->point_type_exists($user_id, 'regist_point')) {
			$point_amount = $this->user_obj->get_user_rank_point($user_id, 'regist_point');
			if ($point_amount > 0) {
// 0为暂时取消
				$this->user_model->log_account_change($user_id, 0, $point_amount, $point_amount, '注册送积分', 'regist_point');
			}
		}

		if ($res) {
			//跳到分享页面
			// 此代码有效期至2015-11-16 15:00
			//if (time()<mktime(15, 0, 0, 11, 16))
			//$share_url  =  '/zhuanti/lottery';
			//else $share_url  =  '/';
			//, 'share_url' =>$share_url
			if (isset($point_amount) && $point_amount > 0) {
				echo json_encode(array('res' => true, 'msg' => '恭喜您' . $point_amount . '积分已经到您的个人账户!'));
			} else {
				echo json_encode(array('res' => false, 'msg' => '保存成功!'));
			}

		} else {
			echo json_encode(array('res' => false, 'msg' => '已保存!'));
		}

	}

	public function account() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);

		$status = trim($this->input->post('status'));
		$status = empty($status) ? 1 : $status;
		$filter['order_status'] = $status;

		$page = trim($this->input->post('page'));
		if (!empty($page)) {
			$filter['page'] = $page;
		}

		$filter = get_pager_param($filter);

		$account_list = $this->user_model->account_list($filter, $user_id);
		if ($this->input->post('is_ajax')) {

			$data['content'] = $this->load->view('user/account', array(
				'user' => $user_info,
				'account_list' => $account_list['list'],
				'filter' => $account_list['filter'],
				'full_page' => FALSE,
				'order_status' => $status,
			), TRUE);
			$data['error'] = 0;
			$data['page_count'] = $account_list['filter']['page_count'];
			$data['page'] = $account_list['filter']['page'];
			$data['order_status'] = $status;
			echo json_encode($data);
			return;
		}

		$this->load->view('user/account', array(
			'title' => '资金账户',
			'user' => $user_info,
			'account_list' => $account_list['list'],
			'filter' => $account_list['filter'],
			'left_sel' => 13,
			'full_page' => TRUE,
			'order_status' => $status,
		));
	}
	public function recharge($amount) {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$recharge_id = $this->user_model->create_recharge($user_id, $amount);
		if ($recharge_id > 0) {
			$this->load->library('alipay');
			$link = $this->alipay->get_recharge_link($recharge_id, $amount);
			redirect($link);
		} else {
			echo "<script type='text/javascript'>
                      alert('系统忙，请稍后充值！');
                      location.href = '/user/account';
                      </script>";
		}
	}
	public function recharge_pay($recharge_id) {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$recharge = $this->user_model->get_recharge($recharge_id, $user_id);
		if (isset($recharge)) {
			$this->load->library('alipay');
			$link = $this->alipay->get_recharge_link($recharge['recharge_id'], $recharge['amount']);
			redirect($link);
		} else {
			echo "FAIL";
		}

	}
	public function new_password() {
		$newpsw = $this->input->post('password');
		$mobile = $this->session->userdata('mobile');
		if (!empty($mobile)) {
			$res = $this->user_model->update_by_mobile(array('password' => m_encode($newpsw)), $mobile);
		} else {
			$res = false;
		}
		$this->session->sess_destroy();
		echo $res;
	}
	public function password() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);

		$is_union = 0;
		if (!empty($user_info->union_sina) || !empty($user_info->union_qq) || !empty($user_info->union_zhifubao)) {
			$is_union = 1;
		}

		$this->load->view('user/password', array(
			'title' => '修改密码',
			'user' => $user_info,
			'left_sel' => 43,
			'full_page' => TRUE,
			'is_union' => $is_union,
		));
	}
	public function helper() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);

		if (!empty($user_info->union_sina) || !empty($user_info->union_qq) || !empty($user_info->union_zhifubao)) {
			redirect('/user');
		}

		$this->load->view('user/helper', array(
			'title' => '帮助中心',
			'user' => $user_info,
			'left_sel' => 43,
			'full_page' => TRUE,

		));
	}
	public function question() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);

		if (!empty($user_info->union_sina) || !empty($user_info->union_qq) || !empty($user_info->union_zhifubao)) {
			redirect('/user');
		}

		$this->load->view('user/question', array(
			'title' => '常见问题',
			'user' => $user_info,
			'left_sel' => 43,
			'full_page' => TRUE,

		));
	}
	public function password_edit() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}

		$old_password = trim($this->input->post('old_password'));
		$new_password = trim($this->input->post('new_password'));

		if (empty($old_password) || empty($new_password)) {
			echo json_encode(array('error' => 1, 'msg' => '错误的参数'));
			return;
		}

		$user_info = $this->user_obj->get_profile($user_id);
		if (m_encode($old_password) == $user_info->password) {
			$this->user_model->update(array('password' => m_encode($new_password)), $user_id);
			$this->session->sess_destroy();
			$data = array('error' => 0, 'msg' => '密码修改成功');
			echo json_encode($data);
			return;
		} else {
			echo json_encode(array('error' => 1, 'msg' => '错误的原密码'));
			return;
		}

	}

	public function points() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);

		$status = trim($this->input->post('status'));
		$status = empty($status) ? 1 : $status;
		$filter['order_status'] = $status;

		$page = trim($this->input->post('page'));
		if (!empty($page)) {
			$filter['page'] = $page;
		}

		$filter = get_pager_param($filter);

		$points_list = $this->user_model->points_list($filter, $user_id);
		if ($this->input->post('is_ajax')) {

			$data['content'] = $this->load->view('user/points', array(
				'user' => $user_info,
				'points_list' => $points_list['list'],
				'filter' => $points_list['filter'],
				'full_page' => FALSE,
				'order_status' => $status,
			), TRUE);
			$data['error'] = 0;
			$data['page_count'] = $points_list['filter']['page_count'];
			$data['page'] = $points_list['filter']['page'];
			$data['order_status'] = $status;
			echo json_encode($data);
			return;
		}

		$this->load->view('user/points', array(
			'title' => '我的积分',
			'user' => $user_info,
			'points_list' => $points_list['list'],
			'filter' => $points_list['filter'],
			'left_sel' => 41,
			'full_page' => TRUE,
			'order_status' => $status,
		));
	}

	public function address() {

		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$this->load->model('cart_model');
		$user_info = $this->user_obj->get_profile($user_id);

		/* 获得用户所有的收货人信息 */
		$address_list = index_array($this->user_model->address_list($user_id), 'address_id');

		if (count($address_list) > 5) {
			/* 如果用户收货人信息的总数小于5 则增加一个新的收货人信息 */
			$show_address_add = 0;
		} else {
			$show_address_add = 1;
		}
		$checkout['shipping'] = array('address_id' => 0, 'country' => 1, 'province' => 0, 'city' => 0, 'district' => 0);
		list($province_list, $city_list, $district_list) = $this->cart_model->cart_region($checkout['shipping']);

		$address_id = $user_info->address_id;

		$this->load->view('user/address', array(
			'title' => '收货地址管理',
			'user' => $user_info,
			'address_list' => $address_list,
			'address_id' => $address_id,
			'show_address_add' => $show_address_add,
			'province_list' => $province_list,
			'city_list' => $city_list,
			'district_list' => $district_list,
			'left_sel' => 12,
			'full_page' => TRUE,
		));
	}

	public function address_edit() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}

		$address_id = trim($this->input->post('address_id'));

		$address = array();
		$address['consignee'] = trim($this->input->post('consignee'));
		$address['address'] = trim($this->input->post('address'));
		$address['zipcode'] = trim($this->input->post('zipcode'));
		$address['tel'] = trim($this->input->post('tel'));
		$address['mobile'] = trim($this->input->post('mobile'));
		$address['province'] = trim($this->input->post('province'));
		$address['city'] = trim($this->input->post('city'));
		$address['district'] = trim($this->input->post('district'));
		$address['user_id'] = $user_id;
		$address['create_date'] = date('Y-m-d H:i:s');
		$is_used = $this->input->post('is_used');

		if (empty($is_used) && (empty($address['consignee']) || empty($address['address']) || empty($address['zipcode']) || empty($address['province']) || empty($address['city']) || empty($address['district']))) {
			echo json_encode(array('error' => 1, 'msg' => '错误的参数'));
			return;
		}

		$user_info = $this->user_obj->get_profile($user_id);

		if ($address_id > 0) {

			if ($is_used == 1) {
				$this->user_model->update_address_used($address_id, $user_id);
				$this->user_model->update(array('address_id' => $address_id), $user_id);
				$user_info->address_id = $address_id;
			} else {
				$rs = $this->user_model->update_address($address, $address_id);
			}

			$data = array('error' => 0, 'msg' => '地址修改成功');
			/* 获得用户所有的收货人信息 */
			$address_list = index_array($this->user_model->address_list($user_id), 'address_id');

			if (count($address_list) > 5) {
				/* 如果用户收货人信息的总数小于5 则增加一个新的收货人信息 */
				$show_address_add = 0;
			} else {
				$show_address_add = 1;
			}
			$data['content'] = $this->load->view('user/address', array(
				'user' => $user_info,
				'address_list' => $address_list,
				'address_id' => $user_info->address_id,
				'full_page' => FALSE,
			), TRUE);
			$data['show_address_add'] = $show_address_add;

		} else {
			if ($user_info->address_id == 0) {
				$address['is_used'] = 1;
			} else {
				$address['is_used'] = 0;
			}
			$address_id = $this->user_model->insert_address($address);
			if ($address_id > 0 && $address['is_used'] == 1) {
				$this->user_model->update(array('address_id' => $address_id), $user_id);
				$user_info->address_id = $address_id;
			}

			if ($address_id > 0) {
				$data = array('error' => 0, 'msg' => '地址添加成功');
				/* 获得用户所有的收货人信息 */
				$address_list = index_array($this->user_model->address_list($user_id), 'address_id');

				if (count($address_list) > 5) {
					/* 如果用户收货人信息的总数小于5 则增加一个新的收货人信息 */
					$show_address_add = 0;
				} else {
					$show_address_add = 1;
				}
				$this->load->model('cart_model');
				list($province_list, $city_list, $district_list) = $this->cart_model->cart_region(array());
				$data['content'] = $this->load->view('user/address', array(
					'user' => $user_info,
					'province_list' => $province_list,
					'address_list' => $address_list,
					'address_id' => $user_info->address_id,
					'full_page' => FALSE,
				), TRUE);
				$data['show_address_add'] = $show_address_add;

			} else {
				$data = array('error' => 1, 'msg' => '地址添加失败，请重试');
			}
		}

		echo json_encode($data);
		return;
	}

	public function address_del() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}

		$address_id = trim($this->input->post('address_id'));

		if (empty($address_id)) {
			echo json_encode(array('error' => 1, 'msg' => '错误的参数'));
			return;
		}

		$user_info = $this->user_obj->get_profile($user_id);
		$rs = $this->user_model->delete_address($address_id);
		if ($rs == 1) {
			$data = array('error' => 0, 'msg' => '删除成功');
			/* 获得用户所有的收货人信息 */
			$address_list = index_array($this->user_model->address_list($user_id), 'address_id');

			if (count($address_list) > 5) {
				/* 如果用户收货人信息的总数小于5 则增加一个新的收货人信息 */
				$show_address_add = 0;
			} else {
				$show_address_add = 1;
			}
			$this->load->model('cart_model');
			list($province_list, $city_list, $district_list) = $this->cart_model->cart_region(array());
			$data['content'] = $this->load->view('user/address', array(
				'user' => $user_info,
				'province_list' => $province_list,
				'address_list' => $address_list,
				'address_id' => $user_info->address_id,
				'full_page' => FALSE,
			), TRUE);
			$data['show_address_add'] = $show_address_add;
		} else {
			$data = array('error' => 1, 'msg' => '要删除的记录不存在');
		}
		echo json_encode($data);
		return;
	}

	public function leaveword() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);

		$filter = array();
		$page = trim($this->input->post('page'));
		if (!empty($page)) {
			$filter['page'] = $page;
		}

		$filter = get_pager_param($filter);

		$liuyan_list = $this->user_model->liuyan_list($filter, $user_id);

		if ($this->input->post('is_ajax')) {

			$data['content'] = $this->load->view('user/leaveword', array(
				'user' => $user_info,
				'liuyan_list' => $liuyan_list['list'],
				'filter' => $liuyan_list['filter'],
				'full_page' => FALSE,
			), TRUE);
			$data['error'] = 0;
			$data['page_count'] = $liuyan_list['filter']['page_count'];
			$data['page'] = $liuyan_list['filter']['page'];
			echo json_encode($data);
			return;
		}

		$this->load->view('user/leaveword', array(
			'title' => '商品咨询',
			'user' => $user_info,
			'liuyan_list' => $liuyan_list['list'],
			'filter' => $liuyan_list['filter'],
			'left_sel' => 32,
			'full_page' => TRUE,
		));
	}

	public function comment() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$filter = array('page' => 1, 'page_size' => 20);
		$comment_list = $this->user_model->dianping_list($filter, $user_id);
		$this->load->view('mobile/user/comment', array('comment_list' => $comment_list['list']));
	}
	public function liuyan() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);
		$user_info->comment_point = $this->user_obj->get_user_rank_point($user_id, 'comment_point');
		$filter = array();
		$page = trim($this->input->post('page'));
		if (!empty($page)) {
			$filter['page'] = $page;
		}

		$filter = get_pager_param($filter);

		$liuyan_list = $this->user_model->dianping_list($filter, $user_id);

		if ($this->input->post('is_ajax')) {

			$data['content'] = $this->load->view('user/liuyan', array(
				'user' => $user_info,
				'liuyan_list' => $liuyan_list['list'],
				'filter' => $liuyan_list['filter'],
				'full_page' => FALSE,
			), TRUE);
			$data['error'] = 0;
			$data['page_count'] = $liuyan_list['filter']['page_count'];
			$data['page'] = $liuyan_list['filter']['page'];
			echo json_encode($data);
			return;
		}

		$this->load->view('user/liuyan', array(
			'title' => '商品点评',
			'user' => $user_info,
			'liuyan_list' => $liuyan_list['list'],
			'filter' => $liuyan_list['filter'],
			'left_sel' => 31,
			'full_page' => TRUE,
		));
	}

	public function load_dianping_panel() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆', 'need_login' => 1));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}

		$product_id = trim($this->input->post('product_id'));

		if (empty($product_id)) {
			echo json_encode(array('error' => 1, 'msg' => '商品不存在'));
			return;
		}

		if (!$this->user_model->can_dianping($product_id, $user_id)) {
			echo json_encode(array('error' => 1, 'msg' => '此商品您还没有购买，不能进行评论！'));
			return;
		}
		//  $dianping_info = $this->user_model->dianping_info($product_id,$user_id);

		$dianping_info = array();
		$user_name = $this->session->userdata('user_name');
		$data['content'] = $this->load->view('user/product_dianping_panel', array(
			'dianping_info' => $dianping_info,
			'product_id' => $product_id,
			'user_name' => $user_name,
		), TRUE);
		$data['error'] = 0;
		echo json_encode($data);
		return;
	}

	public function post_dianping() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}

		$product_id = trim($this->input->post('product_id'));
		if (empty($product_id)) {
			echo json_encode(array('error' => 1, 'msg' => '商品不存在'));
			return;
		}

		if (!$this->user_model->can_dianping($product_id, $user_id)) {
			echo json_encode(array('error' => 1, 'msg' => '您目前不能对该商品进行点评'));
			return;
		}

		$comment_content = trim($this->input->post('comment_content'));
		$size_id = intval($this->input->post('size_id'));
		$suitable = intval($this->input->post('suitable'));
		$height = floatval($this->input->post('height'));
		$weight = floatval($this->input->post('weight'));

		if (mb_strlen($comment_content) < 5) {
			echo json_encode(array('error' => 1, 'msg' => '留言内容至少为5个汉字'));
			return;
		} else if (mb_strlen($comment_content) > 200) {
			echo json_encode(array('error' => 1, 'msg' => '留言内容至多为200个汉字'));
			return;
		}

		$liuyan = array();
		$liuyan['tag_type'] = 1;
		$liuyan['tag_id'] = $product_id;
		$liuyan['comment_type'] = 2;
		$liuyan['user_id'] = $user_id;
		$liuyan['comment_content'] = $comment_content;
		$liuyan['comment_date'] = date('Y-m-d H:i:s');
		$liuyan['comment_ip'] = real_ip();
		$liuyan['height'] = $height;
		$liuyan['weight'] = $weight;
		$liuyan['size_id'] = $size_id;
		$liuyan['suitable'] = $suitable;
		$comment_id = $this->user_model->insert_liuyan($liuyan);
		if ($comment_id > 0) {
			$data = array('error' => 0, 'msg' => '发送成功,请等待审核');
			echo json_encode($data);
			return;
		} else {
			$data = array('error' => 1, 'msg' => '发送失败,请稍后再试');
			echo json_encode($data);
			return;
		}
	}

	public function token() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);

		$status = trim($this->input->post('status'));
		$status = empty($status) ? 1 : $status;
		$filter['order_status'] = $status;

		$page = trim($this->input->post('page'));
		if (!empty($page)) {
			$filter['page'] = $page;
		}

		$filter = get_pager_param($filter);

		$token_list = $this->user_model->voucher_list($filter, $user_id);
		if ($this->input->post('is_ajax')) {

			$data['content'] = $this->load->view('user/token', array(
				'user' => $user_info,
				'token_list' => $token_list['list'],
				'filter' => $token_list['filter'],
				'full_page' => FALSE,
				'order_status' => $status,
			), TRUE);
			$data['error'] = 0;
			$data['page_count'] = $token_list['filter']['page_count'];
			$data['page'] = $token_list['filter']['page'];
			$data['order_status'] = $status;
			echo json_encode($data);
			return;
		}

		$this->load->view('user/token', array(
			'title' => '我的现金劵',
			'user' => $user_info,
			'token_list' => $token_list['list'],
			'filter' => $token_list['filter'],
			'left_sel' => 14,
			'full_page' => TRUE,
			'order_status' => $status,
		));
	}

	public function token_add() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}
		$voucher_sn = trim($this->input->post('voucher_sn'));
		$data = $this->user_model->bind_user_voucher($user_id, $voucher_sn);
		echo json_encode($data);
		return;
	}

	public function collection() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);

		$status = trim($this->input->post('status'));
		$status = empty($status) ? 1 : $status;
		$filter['order_status'] = $status;

		$page = trim($this->input->post('page'));
		if (!empty($page)) {
			$filter['page'] = $page;
		}

		$filter = get_pager_param($filter);

		$collection_list = $this->user_model->collection_list($filter, $user_id);

		if ($this->input->post('is_ajax')) {

			$data['content'] = $this->load->view('user/collection', array(
				'user' => $user_info,
				'collection_list' => $collection_list['list'],
				'filter' => $collection_list['filter'],
				'full_page' => FALSE,
				'order_status' => $status,
			), TRUE);
			$data['error'] = 0;
			$data['page_count'] = $collection_list['filter']['page_count'];
			$data['page'] = $collection_list['filter']['page'];
			$data['order_status'] = $status;
			echo json_encode($data);
			return;
		}

		$this->load->view('user/collection', array(
			'title' => '我的收藏',
			'user' => $user_info,
			'collection_list' => $collection_list['list'],
			'filter' => $collection_list['filter'],
			'left_sel' => 21,
			'full_page' => TRUE,
			'order_status' => $status,
		));
	}

	public function collection_del() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}
		$rec_id = trim($this->input->post('rec_id'));
		$rs = $this->user_model->delete_collection($user_id, $rec_id);
		if ($rs == 1) {
			$data = array('error' => 0, 'msg' => '删除成功');
		} else {
			$data = array('error' => 1, 'msg' => '要删除的记录不存在');
		}
		echo json_encode($data);
		return;
	}

	public function signin($page_name) {
		$this->load->view('mobile/user/signin', array(
			'first_page' => $page_name
			, 'session_id' => $this->session->userdata('session_id'),
		));
	}

	public function login($register = FALSE) {
		if ($this->user_id) {
			$this->session->unset_userdata('back_url');
			redirect(isset($referer_url) ? $referer_url : 'index');
		}
		if ($back_url = $this->input->get('back_url')) {
			$this->session->set_userdata('back_url', $back_url);
		}
		if (!$back_url = $this->session->userdata('back_url')) {
			$back_url = $this->input->server('HTTP_REFERER');
			//if(strstr($back_url,'/user/login')===FALSE&&strstr($back_url,'/user/register')===FALSE)
			//{
			$this->session->set_userdata('referer_url', $back_url);
			//}
		}

		if (empty($back_url)) {
			$back_url = 'index';
		}

		$back_url = 'external:/' . $back_url;
		$this->load->vars(array('back_url' => $back_url));

		$this->load->view('mobile/user/login', array(
			'title' => '登录',
		));
		/*$this->load->view('mobile/user/login',array(
			'title' => '登录注册',
		));*/

	}

	public function show_online() {

		$user_info = array();
		$user_id = 0;
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
			$user_info = $this->user_obj->get_profile($user_id);
		}
		$userdata = $this->session->all_userdata();
		$session_id = $userdata['session_id'];
		$message_list = $this->user_model->online_message($session_id, $user_id);
		$cur_rec = 0;
		$cur_reply = 0;

		if (!empty($message_list)) {
			$cur_rec = $message_list[0]['message_id'];
			$cur_reply = $message_list[0]['message_id'];
			$message_list = array_reverse($message_list);
		}

		$has_man = $this->user_model->online_admin();
		$cur = time();
		if ($cur > mktime(9, 0, 0, date("m"), date("d"), date("Y")) && $cur < mktime(21, 0, 0, date("m"), date("d"), date("Y"))) {
			$has_man = TRUE;
		}

		$this->load->view('common/online', array(
			'title' => '爱童网_在线客服',
			'user_info' => $user_info,
			'message_list' => $message_list,
			'cur_rec' => $cur_rec,
			'cur_reply' => $cur_reply,
			'has_man' => $has_man,
		));

	}

	public function submit_msg() {
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}

		$user_id = 0;
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		}

		$userdata = $this->session->all_userdata();
		$session_id = $userdata['session_id'];

		$value = trim($this->input->post('value'));
		$cur_rec = trim($this->input->post('cur_rec'));
		$username = isset($userdata['user_name']) && !empty($userdata['user_name']) ? $userdata['user_name'] : '客户';

		$rs = $this->user_model->add_online_msg($user_id, $session_id, $value, $cur_rec);
		if (!empty($rs)) {
			$content = '<p><span class="' . ($rs->qora == 0 ? 'ol_cus' : 'ol_kf') . '">' . ($rs->qora == 0 ? $username : '本站客服') . ' (' . date('h:i:s', strtotime($rs->create_date)) . ') </span><span class="ol_t">' . $rs->content . '</span></p>';
			$data = array('error' => 0, 'msg' => '发送成功', 'content' => $content, 'rec_id' => $rs->message_id);
		} else {
			$data = array('error' => 1, 'msg' => '服务器异常，请重新尝试');
		}
		echo json_encode($data);
		return;
	}

	public function get_reply_msg() {
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}

		$user_id = 0;

		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');

		}

		$userdata = $this->session->all_userdata();
		$session_id = $userdata['session_id'];
		$username = isset($userdata['user_name']) && !empty($userdata['user_name']) ? $userdata['user_name'] : '客户';

		$cur_reply = trim($this->input->post('cur_reply'));

		$rs = $this->user_model->reply_msg($user_id, $session_id, $cur_reply);
		if (!empty($rs)) {
			$content = '<p><span class="' . ($rs->qora == 0 ? 'ol_cus' : 'ol_kf') . '">' . ($rs->qora == 0 ? $username : '本站客服') . ' (' . date('h:i:s', strtotime($rs->create_date)) . ') </span><span class="ol_t">' . $rs->content . '</span></p>';
			$data = array('error' => 0, 'msg' => '发送成功', 'content' => $content, 'rec_id' => $rs->message_id);
		} else {
			$data = array('error' => 1, 'msg' => '没有数据');
		}
		echo json_encode($data);
		return;
	}

	public function show_total_msg() {
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}

		$user_id = 0;
		$user_info = array();
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
			$user_info = $this->user_obj->get_profile($user_id);
		}
		$userdata = $this->session->all_userdata();
		$session_id = $userdata['session_id'];

		$page = trim($this->input->post('page'));
		$message_all = $this->user_model->all_msg($user_id, $session_id, $page);

		$data['content'] = $this->load->view('common/online_lib', array(
			'user_info' => $user_info,
			'message_all' => $message_all['list'],
			'total_page' => $message_all['total'],
			'cur_page' => $message_all['cur'],
		), TRUE);
		$data['error'] = 0;
		$data['cur_page'] = $message_all['cur'];
		$data['total_page'] = $message_all['total'];
		echo json_encode($data);
		return;

	}

	public function msg_close() {
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}
		$user_id = 0;
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		}
		$userdata = $this->session->all_userdata();
		$session_id = $userdata['session_id'];
		$this->user_model->msg_close($user_id, $session_id);
		echo json_encode(array('error' => 0, 'msg' => ''));
		return;
	}

	public function proc_login() {
		//if (!$this->input->post('is_ajax'))
		//return FALSE;
		$err_msg = '';

		if ($this->user_obj->is_login()) {
			echo json_encode(array('error' => 0, 'user_name' => $this->session->userdata('user_name'), 'rank_name' => $this->session->userdata('rank_name')));
			return;
		}

		$mobile = trim($this->input->post('username'));
		$password = trim($this->input->post('password'));
		//$remember = trim($this->input->post('remember'));
		if ('' == $password) {
			$err_msg = '密码不能为空!';
		}
		if ($err_msg == '' && $this->user_obj->login($mobile, $password) == FALSE) {
			$err_msg = "账号或密码错误,请重试";
		}
		//echo $this->session->userdata('user_id');

		if (empty($err_msg)) {
			$this->user_obj->update_user_info();
			if (!$back_url = $this->session->userdata('back_url')) {
				$back_url = $this->session->userdata('referer_url');
			}
			//		if (!$back_url)
			//		    $back_url = 'index';
			if (!empty($back_url) && substr($back_url, 0, 4) !== 'http') {
				$back_url = site_url($back_url);
				$this->session->unset_userdata('back_url');
			}

			$user_id = $this->session->userdata('user_id');

			//登录 成功 将 该用户收藏的 商品 写入session
			$collect_data = $this->user_model->get_collect_info(array('user_id' => $user_id));
			if (isset($_SESSION['collect_' . $user_id])) {
				array_push($collect_data, $_SESSION['collect_' . $user_id]);
			}
			$this->session->set_userdata('collect_' . $user_id, $collect_data);

			//登录 成功 将 该用户赞的 文章 写入session
			$this->load->model('wordpress_model');
			$praise_data = $this->wordpress_model->get_article_praise(array('user_id' => $user_id, 'type_source' => 'yyw_moblie'));
			if (isset($_SESSION['praise_' . $user_id])) {
				array_push($praise_data, $_SESSION['praise_' . $user_id]);
			}
			$this->session->set_userdata('praise_' . $user_id, $praise_data);

			die(json_encode(array('error' => 0, 'back_url' => $back_url, 'user_name' => $this->session->userdata('user_name'), 'rank_name' => $this->session->userdata('rank_name'))));
			return;
		} else {
			die(json_encode(array('error' => 1, 'message' => $err_msg)));

		}
	}

	public function valid_user() {
		$user_name = $this->input->post('user');
		if (empty($user_name)) {
			echo json_encode(array('error' => 1));
			return;
		}
		$this->load->model('user_model');
		if (strpos($user_name, '@') !== false) {
			$field = 'email';
		} else {
			$field = 'mobile';
		}

		$user = $this->user_model->filter(array(
			$field => $user_name,
		));
		if (isset($user) && count($user) > 0) {
			echo json_encode(array('error' => 1));
		} else {
			echo json_encode(array('error' => 0));
		}
	}

	public function proc_register() {
		$back_url = "/index";
		//if (!$this->input->post('is_ajax')) return FALSE;
		$err_msg = '';

		/*$auth_code = $this->session->userdata("auth_code");
			        if (strtolower($auth_code) !=strtolower(trim($this->input->post('captcha')))) {
			            $err_msg = "请输入正确的验证码！".$auth_code.'<>'.strtolower(trim($this->input->post('captcha')));
		*/
		$param['user_name'] = trim($this->input->post('user_name'));
		$param['mobile'] = $mobile = trim($this->input->post('mobile'));
		$param['password'] = trim($this->input->post('password'));
		//$param['email'] = trim($this->input->post('email'));
		if ($this->user_obj->is_username_register($param['user_name'])) {
			$err_msg = "该用户名已存在!";
		} elseif (!preg_match('/^1[0-9]{10}$/', $mobile)) {
			$err_msg = '手机号码不正确，请重新输入！';
		} elseif ($this->user_obj->is_mobile_register($mobile)) {
			$err_msg = "此手机已存在，请重新输入。";

		}

		if ($err_msg == '' && $this->user_obj->register($param) == FALSE) {
			$err_msg = "注册错误，请重试";
		}

		if ('' == $err_msg) {
			$this->user_obj->update_user_info();
			if (!$back_url = $this->session->userdata('back_url')) {
				$back_url = $this->session->userdata('referer_url');
			}
			if (!$back_url) {
				$back_url = 'user';
			}

			if (substr($back_url, 0, 4) !== 'http') {
				$back_url = site_url($back_url);
			}

			$this->session->unset_userdata('back_url');
			echo 'success请前往个人中心完善资料, 领取500积分, 并参加抽奖活动!';
		} else {
			echo $err_msg;
		}
		exit;
	}

	public function logout() {
		$this->session->sess_destroy();
		//退出时购物车数量清0
		redirect("/index");
		/*
				$this->input->set_cookie('cart_num',0,CART_SAVE_SECOND);
			        $referer_url=$this->input->server('HTTP_REFERER');
			        echo $referer_url;
			        exit();
				$front_host = $this->input->server('HTTP_HOST');
				$check_url = array("http://".$front_host."/user","http://".$front_host."/user/index");//	SERVER_NAME HTTP_HOST

				if($referer_url && (in_array($referer_url, $check_url ) || strpos($referer_url,'/cart/success') > 0)){
				    redirect("/user/login");
				}else if($referer_url && strstr($referer_url,'/user/logout')===FALSE && strpos($referer_url,'/user/fc_callback') <= 0 ){
			        	redirect($referer_url);
			        }else{
			        	redirect('index');
		*/
	}

	public function region_change() {
		$this->load->model('cart_model');
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}

		$region_id = trim($this->input->post('region_id'));
		$type_num = trim($this->input->post('type'));
		$filter = array();
		if ($type_num == 2) {
			$filter['province'] = $region_id;
		} elseif ($type_num == 3) {
			$filter['city'] = $region_id;
		}
		list($province_list, $city_list, $district_list) = $this->cart_model->cart_region($filter);

		$final = array();
		$final_final = array();
		if ($type_num == 2) {
			$final = $city_list;
		} elseif ($type_num == 3) {
			$final = $district_list;
		}
		if (!empty($final)) {
			foreach ($final as $key => $item) {
				$final_final[$key] = array('region_id' => $item->region_id, 'region_name' => $item->region_name);
			}
		}

		$data = array('error' => empty($final_final) ? 1 : 0, 'msg' => empty($final_final) ? '载入失败' : '', 'regions' => $final_final);
		echo json_encode($data);
		return;
	}

	public function send_email_valid() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}
		$email = trim($this->input->post('email'));
		if (empty($email)) {
			echo json_encode(array('error' => 1, 'msg' => '非法的email'));
			return;
		}

		$user_info = $this->user_obj->get_profile($user_id);
		if ($user_info->email_validated) {
			echo json_encode(array('error' => 1, 'msg' => '您的邮箱已经验证,无须再验证'));
			return;
		}
		if (empty($user_info->email)) {
			if ($this->user_obj->is_email_register($email)) {
				echo json_encode(array('error' => 1, 'msg' => '此邮箱已经被使用,请重新输入'));
				return;
			}
			$this->user_model->update(array('email' => $email), $user_id);
		} elseif ($user_info->email != $email) {
			echo json_encode(array('error' => 1, 'msg' => '非法的email'));
			return;
		}
		$valid_string = substr(md5($user_id . 'fevalid' . $email), 5, 15);
		$valid_string = FRONT_HOST . "/user/checkemailv/val/" . $valid_string;
		$this->user_obj->SendSyncMail(array('user_id' => $user_id, 'user_name' => $user_info->user_name, 'confirm_url' => $valid_string, 'to_email' => $email, 'email' => $email), 'register_validate');
		echo json_encode(array('error' => 0, 'msg' => '确认邮件已经发送,请检查你的邮箱'));
		return;
	}

	public function checkemailv() {
		$filter = $this->uri->uri_to_assoc();
		if (isset($filter['val']) && !empty($filter['val'])) {
			$val = trim($filter['val']);
		} else {
			redirect('/user');
		}
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			$this->session->set_userdata('back_url', FRONT_HOST . "/user/checkemailv/val/" . $val);
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);
		if ($user_info->email_validated) {
			redirect('/user/profile');
		}
		$valid_string = substr(md5($user_id . 'fevalid' . $user_info->email), 5, 15);

		if (trim($valid_string) == $val) {
			$this->user_model->update(array('email_validated' => 1), $user_id);
			$user_info = $this->user_obj->get_profile($user_id);
			if ($user_info->email_validated && empty($user_info->mobile_checked)) {
				$point_amount = $this->user_obj->get_user_rank_point($user_id, 'regist_point');
				$this->user_model->log_account_change($user_id, 0, $point_amount, $point_amount, '验证赠送的积分', 'point_register');
				//$msg_extra = ',您的'.$point_amount."奖励积分已经发放";
			}
		}
		redirect('/user/profile');
	}

	public function validate_mobile() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($user_id);
		if ($user_info->mobile_checked) {
			redirect('/user/profile');
		}

		$this->load->view('user/mobile', array(
			'title' => '会员验证',
			'user' => $user_info,
			'left_sel' => 42,
			'full_page' => TRUE,

		));
	}

	public function send_mobile_code() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}
		$mobile = trim($this->input->post('mobile'));
		if (empty($mobile)) {
			echo json_encode(array('error' => 1, 'msg' => '非法的手机号'));
			return;
		}

		$user_info = $this->user_obj->get_profile($user_id);
		if ($user_info->mobile_checked) {
			echo json_encode(array('error' => 1, 'msg' => '您的手机已经验证'));
			return;
		}

		$code = mt_rand(123123, 999999);
		if ($this->user_model->is_mobile_other_used($mobile, $user_info->user_id)) {
			echo json_encode(array('error' => 1, 'msg' => '此号码已经存在,您不能使用此号码'));
			return;
		}

		$args = array(
			"authentication" => $code,
			"mobile" => $mobile,
		);
		$result = $this->user_obj->send_sync_sms($args, 'register_validate');
		if ($result) {
			$this->session->set_userdata('sending_mobile_phone', $mobile);
			$this->session->set_userdata('sending_mobile_code', $code);
			$this->session->set_userdata('sending_mobile_code_time', time());
			echo json_encode(array('error' => 0, 'msg' => '短信发送成功，请注意查收。'));
			return;
		} else {
			echo json_encode(array('error' => 1, 'msg' => '验证码发送失败，请与管理员联系！'));
			return;
		}
	}

	public function bind_mobile() {
		if ($this->user_obj->is_login()) {
			$user_id = $this->session->userdata('user_id');
		} else {
			echo json_encode(array('error' => 1, 'msg' => '您还未登陆'));
			return;
		}
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}
		$mobile = trim($this->input->post('mobile'));
		$mobile_code = trim($this->input->post('mobile_code'));
		$user_info = $this->user_obj->get_profile($user_id);
		if (empty($mobile) || empty($mobile_code)) {
			echo json_encode(array('error' => 1, 'msg' => '非法的参数'));
			return;
		}
		if ($user_info->mobile_checked) {
			echo json_encode(array('error' => 1, 'msg' => '您的手机已经验证'));
			return;
		}
		if (!empty($user_info->mobile) && $user_info->mobile != $mobile) {
			echo json_encode(array('error' => 1, 'msg' => '非法的手机号'));
			return;
		}

		$sending_mobile_phone = $this->session->userdata('sending_mobile_phone');
		$sending_mobile_code = $this->session->userdata('sending_mobile_code');
		$sending_mobile_code_time = $this->session->userdata('sending_mobile_code_time');

		if ($mobile_code == $sending_mobile_code && $mobile == $sending_mobile_phone && (time() - $sending_mobile_code_time) < 1800) {
			$this->user_model->update(array('mobile' => $mobile, 'mobile_checked' => 1), $user_id);

			//送积分
			$user_info = $this->user_obj->get_profile($user_id);
			if ($user_info->mobile_checked && empty($user_info->email_validated)) {
				$point_amount = $this->user_obj->get_user_rank_point($user_id, 'regist_point');
				$this->user_model->log_account_change($user_id, 0, $point_amount, $point_amount, '验证赠送的积分', 'point_register');
				//$msg_extra = ',您的'.$point_amount."奖励积分已经发放";
			}
			echo json_encode(array('error' => 0, 'msg' => '验证成功'));
			return;
		} else {
			echo json_encode(array('error' => 1, 'msg' => '验证失败'));
			return;
		}
	}

	public function find_mobile_password() {
		/*if (!$this->input->post('is_ajax'))
			{
				echo json_encode(array('error'=>1,'msg'=>'错误的访问'));
				return;
		*/
		//$user_name = trim($this->input->post('user_name'));
		//$yanzhen = trim($this->input->post('yanzhen'));
		/*if (empty($user_name))
	        {
	        	echo json_encode(array('error'=>1,'msg'=>'请填写你注册的邮箱或者手机'));
				return;
	        }
	        if (empty($yanzhen))
	        {
	        	echo json_encode(array('error'=>1,'msg'=>'请填写正确的验证码'));
				return;
*/

		$mobile = $this->session->userdata('mobile');
		$user = $this->user_obj->filter_user($mobile);
		if (!empty($user)) {
			if (!empty($user->mobile) && $user->mobile == $user_name) {
				/*$this->load->library('authcode');
							if (!$this->authcode->check(strtolower($yanzhen)))
							{
								echo json_encode(array('error'=>1,'msg'=>'请填写正确的验证码'));
					        	return;
				*/
				$args = array(
					"user_name" => $user->user_name,
					"user_id" => $user->user_id,
					"password" => m_decode($user->password),
					"mobile" => $user->mobile,
				);
				$result = $this->user_obj->send_sync_sms($args, 'send_password');
				echo json_encode(array('error' => 0, 'msg' => '密码已经发往您注册的手机上，请注意查收'));
				return;
			} else {
				echo json_encode(array('error' => 1, 'msg' => '不是有效的注册手机号'));
				return;
			}
		} else {
			echo json_encode(array('error' => 1, 'msg' => '此用户未注册,请填写你注册的邮箱或者手机'));
			return;
		}

	}

	public function show_verify() {
		$this->load->library('authcode');
		$this->authcode->show();
	}
	public function verify_msgcode() {
		$mobile_code = $this->session->userdata("sending_mobile_code");
		if (strtolower($mobile_code) != strtolower(trim($this->input->get('authcode')))) {
			$err_msg = "手机验证码错误！";
			echo $err_msg;
		} else {
			echo 0;
		}
	}
	public function reg_auth() {

		$retry = $this->input->get('retry');
		if (!$retry) {

			$mobile = $this->input->get('mobile');
			//$captcha = $this->input->get('captcha');
			$session_id = $this->input->get('session_id');
			$mobile = trim($mobile);
			$is_register = $this->input->get('is_register');
			$this->load->library('authcode');
			if ($is_register) {
				if ($this->user_obj->is_login()) {
					$err_msg = "您已登陆,无法注册新账号。";
				} elseif (!preg_match('/^1[0-9]{10}$/', $mobile)) {
					$err_msg = '手机号码不正确，请重新输入！';
				} elseif ($this->user_obj->is_mobile_register($mobile)) {
					$err_msg = "此手机已存在，请重新输入。";
				} elseif ($session_id != $this->session->userdata('session_id')) {
					$err_msg = '请求出错!';
				} elseif ($this->session->userdata('sending_mobile_code_error') > 5) {
					$err_msg = '出错次数太多，请稍后再试!';
				}
				//elseif (!$this->authcode->check(strtolower($captcha))){
				//	$err_msg = '请填写正确的验证码!';
				//}
			} else {
				if (!preg_match('/^1[0-9]{10}$/', $mobile)) {
					$err_msg = '手机号码不正确，请重新输入！';
				} elseif (!$this->user_obj->is_mobile_register($mobile)) {
					$err_msg = '您不是演示站用户,请先注册!';
				}
			}

		} else {
			$mobile = $this->session->userdata('mobile');
			if (empty($mobile)) {
				$err_msg = '请求非法!';
			}
		}

		if (isset($err_msg)) {
			$n = $this->session->userdata('sending_mobile_code_error');
			if (empty($n)) {
				$n = 1;
			} else {
				$n++;
			}

			$this->session->set_userdata('sending_mobile_code_error', $n);
			echo json_encode(array('mobile_check_err' => $err_msg));
			exit();
		}

// 如果短信已经发出10分钟内不再重发
		$sending_mobile_phone = $this->session->userdata('sending_mobile_phone');
		$sending_mobile_code_time = $this->session->userdata('sending_mobile_code_time');
		$sending_mobile_code = $this->session->userdata('sending_mobile_code');

		if ($mobile == $sending_mobile_phone && (time() - $sending_mobile_code_time) < 600) {
			// 10 mins
			$msg = '';
			$smsCode = $sending_mobile_code;
		} else {
			//生成随机数
			srand(microtime(true) * 1000);
			$smsCode = rand(100000, 999999);
			$msg = $this->user_obj->send_sync_sms(array('mobile' => $mobile, 'authentication' => $smsCode), 'register_validate');
			$sending_mobile_code_time = time();
		}

		if ('' == $msg) {
			$this->session->set_userdata('sending_mobile_code', $smsCode);
			$this->session->set_userdata('sending_mobile_phone', $mobile);
			$this->session->set_userdata('mobile', $mobile);
			$this->session->set_userdata('sending_mobile_code_time', $sending_mobile_code_time);
			//$this->session->set_userdata('msg_send_reult', true);
			echo json_encode(array('msg_send_result' => 0, 'mobile_check_err' => 0));
		} else {
			echo json_encode(array('msg_send_result' => '短信发送失败，请重试!', 'mobile_check_err' => 0));
			//$this->session->set_userdata('msg_send_reult', '短信发送失败，请重试!');
		}

		//$this->load->library('mobile');
		//$this->mobile->auth($mobile);
	}

	public function find_password() {
		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}
		$user_name = trim($this->input->post('user_name'));
		if (empty($user_name)) {
			echo json_encode(array('error' => 1, 'msg' => '请填写你注册的邮箱或者手机'));
			return;
		}
		$user = $this->user_obj->filter_user($user_name);
		if (!empty($user)) {
			if (!empty($user->union_sina) || !empty($user->union_qq) || !empty($user->union_zhifubao)) {
				echo json_encode(array('error' => 1, 'msg' => '联合登录用户,请通过相应的联合登录通道登录'));
				return;
			}

			$flag = 0;
			if (!empty($user->email) && $user->email == $user_name) {
				$this->user_obj->SendSyncMail(array('user_id' => $user->user_id, 'user_name' => $user->user_name, 'to_email' => $user->email, 'email' => $user->email, 'password' => m_decode($user->password)), 'send_password');
				$flag = 1;
			}
			if (!empty($user->mobile) && $user->mobile == $user_name) {
				$need_verify = $this->user_model->need_verify($user->mobile, date('Y-m-d'));
				if ($need_verify) {
					echo json_encode(array('error' => 0, 'msg' => '', 'verify' => 1));
					return;
				}
				$args = array(
					"user_name" => $user->user_name,
					"user_id" => $user->user_id,
					"password" => m_decode($user->password),
					"mobile" => $user->mobile,
				);
				$result = $this->user_obj->send_sync_sms($args, 'send_password');
				if ($result) {
					if ($flag == 1) {
						$flag = 3;
					} else {
						$flag = 2;
					}
				}
			}
			if ($flag == 1) {
				$date = array('error' => 0, 'msg' => '密码已经发往您注册的邮箱，请注意查收');
			} elseif ($flag == 2) {
				$date = array('error' => 0, 'msg' => '密码已经发往您注册的手机上，请注意查收');
			} elseif ($flag == 3) {
				$date = array('error' => 0, 'msg' => '密码已经发往您注册的邮箱和手机上，请注意查收');
			}
			echo json_encode($date);
			return;
		} else {
			echo json_encode(array('error' => 1, 'msg' => '此用户未注册,请填写你注册的邮箱或者手机'));
			return;
		}
	}

	public function exchange_voucher() {
		if (!$this->user_id) {
			goto_login('user/exchange_voucher');
		}

//		$this->load->model('voucher_model');
		//		$this->load->library('memcache');
		//		if(($release_list=$this->memcache->get('exchange-release'))===FALSE || true ){
		//			$release_list= $this->voucher_model->all_exchange_release();
		//			$this->memcache->save('exchange-release',$release_list,CACHE_TIME_COMMON);
		//		}
		$user = $this->user_model->filter(array('user_id' => $this->user_id));
		$this->load->view('user/exchange_voucher', array(
			'title' => '积分兑换现金券',
			'user' => $user,
			'left_sel' => 15,
		));
	}

	public function proc_exchange_voucher() {
		$this->load->model('voucher_model');
		$release_id = intval($this->input->post('release_id'));
		$this->db->trans_begin();
		$user = $this->user_model->lock_user($this->user_id);
		if (!$user) {
			sys_msg('兑换前请先登录', 1);
		}

		$release = $this->voucher_model->lock_release($release_id);
		if (!$release || $release->release_status != 1) {
			sys_msg('该兑换活动当前尚未开始,敬请期待。', 1);
		}

		$campaign = $this->voucher_model->filter_campaign(array('campaign_id' => $release->campaign_id));
		if ($campaign->campaign_type != 'ex' || $campaign->campaign_status != 1 || $campaign->start_date > $this->time || $campaign->end_date < $this->time) {
			sys_msg('该兑换活动当前尚未开始,敬请期待。', 1);
		}

		if ($release->worth > $user->pay_points) {
			sys_msg('您当前的积分不足', 1);
		}

		//发放现金券
		$voucher = array(
			'campaign_id' => $release->campaign_id,
			'release_id' => $release->release_id,
			'user_id' => $this->user_id,
			'voucher_status' => 1,
			'repeat_number' => 1,
			'used_number' => 0,
			'start_date' => $this->time,
			'end_date' => date_change($this->time, 'P' . $release->expire_days . 'D'),
			'voucher_amount' => $release->voucher_amount,
			'min_order' => $release->min_order,
			'create_admin' => 0,
			'create_date' => $this->time,
		);
		$voucher_id = $this->voucher_model->insert_voucher($voucher);
		$voucher = $this->voucher_model->filter(array('voucher_id' => $voucher_id));
		$this->user_model->insert_account(array(
			'link_id' => $voucher_id,
			'user_id' => $this->user_id,
			'pay_points' => $release->worth * -1,
			'change_desc' => '积分兑换现金券',
			'change_code' => 'voucher',
			'create_admin' => 0,
			'create_date' => $this->time,
		));
		$this->user_model->update(array('pay_points' => $user->pay_points - $release->worth), $this->user_id);
		$this->voucher_model->update_release(array('voucher_count' => $release->voucher_count + 1), $release_id);
		$this->db->trans_commit();
		print json_encode(array('err' => 0, 'msg' => '', 'voucher_sn' => $voucher->voucher_sn, 'pay_points' => $user->pay_points - $release->worth));

	}

	public function qq_login() {
		$this->config->load('qq', TRUE);
		$appid = $this->config->item('appid', 'qq');
		$callback = $this->config->item('callback', 'qq');

		$scope = $this->config->item('scope', 'qq');
		$appkey = $this->config->item('appkey', 'qq');
		$state = md5(uniqid(rand(), TRUE));
		$this->session->set_userdata('qq_state', $state); //CSRF protection
		$login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
		. $appid . "&redirect_uri=" . urlencode($callback)
			. "&state=" . $state
			. "&scope=" . $scope . "&display=mobile";
		header("Location:$login_url");
	}

	public function qq_callback() {
		//QQ登录成功后的回调地址,主要保存access token
		$this->config->load('qq', TRUE);
		$state = $this->session->userdata('qq_state');
		$appid = $this->config->item('appid', 'qq');
		$callback = $this->config->item('callback', 'qq');
		$scope = $this->config->item('scope', 'qq');
		$appkey = $this->config->item('appkey', 'qq');
		$filter = $_GET;
		if (isset($filter['code']) && isset($filter['state']) && $filter['state'] == $state) //csrf
		{
			$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
			. "client_id=" . $appid . "&redirect_uri=" . urlencode($callback)
				. "&client_secret=" . $appkey . "&code=" . $filter["code"];

			$response = get_url_contents($token_url);

			if (strpos($response, "callback") !== false) {
				$lpos = strpos($response, "(");
				$rpos = strrpos($response, ")");
				$response = substr($response, $lpos + 1, $rpos - $lpos - 1);
				$msg = json_decode($response);
				if (isset($msg->error)) {
					echo "<h3>error1:</h3>" . $msg->error;
					echo "<h3>msg  :</h3>" . $msg->error_description;
					exit;
				}
			}

			$params = array();
			parse_str($response, $params);

			//debug
			//print_r($params);

			//set access token to session
			$this->session->set_userdata('qq_access_token', $params["access_token"]);
		} else {
			echo ("The state does not match. You may be a victim of CSRF.");
			exit;
		}

		//获取用户标示id
		$access_token = $this->session->userdata('qq_access_token');
		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $access_token;

		$str = get_url_contents($graph_url);

		if (strpos($str, "callback") !== false) {
			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str = substr($str, $lpos + 1, $rpos - $lpos - 1);
		}

		$user = json_decode($str);
		if (isset($user->error)) {
			echo "<h3>error2:</h3>" . $user->error;
			echo "<h3>msg  :</h3>" . $user->error_description;
			exit;
		}

		//debug
		//echo("Hello " . $user->openid);
		//set openid to session
		$openid = $user->openid;
		$this->session->set_userdata('qq_openid', $openid);

		//联合登陆
		if (!empty($user->openid)) {
			$graph_url = "https://graph.qq.com/user/get_user_info?access_token=" . $access_token . "&oauth_consumer_key=" . $appid . "&openid=" . $user->openid . "&format=json";
			$str = get_url_contents($graph_url);
			$user = json_decode($str);
			if (!isset($user->ret)) {
				echo "<h3>error:</h3>10000";
				echo "<h3>msg  :</h3>无效的json格式";
				exit;
			}
			if ($user->ret != 0) {
				echo "<h3>error3:</h3>" . $user->ret;
				echo "<h3>msg  :</h3>" . $user->msg;
				exit;
			}
			$nickname = $user->nickname;
			if (empty($nickname)) {
				$nickname = 'qq用户';
			}

			$qq_user = $this->user_obj->filter_qq($openid);
			if (!empty($qq_user)) {
				$this->user_obj->fast_login($qq_user);

			} else {
				$this->user_obj->fast_register(array('user_name' => $nickname, 'union_qq' => $openid, 'email' => "uqq_$openid@m.com"));
			}

			$this->user_obj->update_user_info();
			if (!$back_url = $this->session->userdata('back_url')) {
				$back_url = $this->session->userdata('referer_url');
			}
			if (!$back_url) {
				$back_url = '';
			}

			if (substr($back_url, 0, 4) !== 'http') {
				$back_url = base_url($back_url);
			}

			$this->session->unset_userdata('back_url');
			header("Location:$back_url");
		}
		echo "<script>window.close();</script>";
	}

	public function xinlang_login() {
		$this->config->load('xinlang', TRUE);
		$appkey = $this->config->item('WB_AKEY', 'xinlang');
		$callback = $this->config->item('WB_CALLBACK_URL', 'xinlang');
		$appsecret = $this->config->item('WB_SKEY', 'xinlang');
		$authorizeURL = $this->config->item('authorizeURL', 'xinlang');
		$accessTokenURL = $this->config->item('accessTokenURL', 'xinlang');

		$params = array();
		$params['client_id'] = $appkey;
		$params['redirect_uri'] = $callback;
		$params['response_type'] = 'code';
		$params['state'] = NULL;
		$params['display'] = 'mobile'; //默认传default，手机传mobile
		$login_url = $authorizeURL . "?" . http_build_query($params);
		header("Location:$login_url");
	}

	public function xinlang_callback() {
		$this->config->load('xinlang', TRUE);
		$appkey = $this->config->item('WB_AKEY', 'xinlang');
		$callback = $this->config->item('WB_CALLBACK_URL', 'xinlang');
		$appsecret = $this->config->item('WB_SKEY', 'xinlang');
		$authorizeURL = $this->config->item('authorizeURL', 'xinlang');
		$accessTokenURL = $this->config->item('accessTokenURL', 'xinlang');
		$host = $this->config->item('host', 'xinlang');
		$access_token = $this->session->userdata('xinlang_access_token');
		$filter = $_GET;
		$token = '';
		$uid = '';

		if (isset($filter['code'])) {
			$keys = array();
			$keys['code'] = $filter['code'];
			$keys['redirect_uri'] = $callback;
			try
			{
				$params = array();
				$params['client_id'] = $appkey;
				$params['client_secret'] = $appsecret;

				$params['grant_type'] = 'authorization_code';
				$params['code'] = $keys['code'];
				$params['redirect_uri'] = $keys['redirect_uri'];

				$url = $accessTokenURL;
				$headers = array();
				$body = http_build_query($params);

				$http_info = array();
				$ci = curl_init();
				/* Curl settings */
				curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
				curl_setopt($ci, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
				curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($ci, CURLOPT_TIMEOUT, 30);
				curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ci, CURLOPT_ENCODING, "");
				curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
				curl_setopt($ci, CURLOPT_HEADER, FALSE);

				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($body)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $body);
				}

				if (!empty($access_token)) {
					$headers[] = "Authorization: OAuth2 " . $access_token;
				}

				$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
				curl_setopt($ci, CURLOPT_URL, $url);
				curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);

				$response = curl_exec($ci);
				$http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
				$http_info = array_merge($http_info, curl_getinfo($ci));
				$debug = FALSE;
				if ($debug) {
					echo "=====post data======\r\n";
					var_dump($body);

					echo '=====info=====' . "\r\n";
					print_r(curl_getinfo($ci));

					echo '=====$response=====' . "\r\n";
					print_r($response);
				}
				curl_close($ci);

				$token = json_decode($response, true);
				if (is_array($token) && !isset($token['error'])) {
					$access_token = $token['access_token'];
					$uid = $token['uid'];
				} else {
					throw new Exception("get access token failed." . $token['error']);
				}
			} catch (Exception $e) {}
		}

		if (!empty($uid)) {
			//$this->session->set_userdata('xinlang_token',$token);
			$this->session->set_userdata('xinlang_access_token', $access_token);
			//$this->session->set_userdata('weibojs_'.$appkey,http_build_query($token));

			$params = array();
			if ($uid !== NULL) {
				if (is_float($uid)) {
					$uid = number_format($uid, 0, '', '');
				} elseif (is_string($uid)) {
					$uid = trim($uid);
				}
				$params['uid'] = $uid;
			}

			$url = $host . 'users/show.json';
			$url = $url . '?' . http_build_query($params);

			$headers = array();

			$ci = curl_init();
			/* Curl settings */
			curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
			curl_setopt($ci, CURLOPT_USERAGENT, 'Sae T OAuth2 v0.1');
			curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($ci, CURLOPT_TIMEOUT, 30);
			curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ci, CURLOPT_ENCODING, "");
			curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
			curl_setopt($ci, CURLOPT_HEADER, FALSE);

			if (isset($access_token) && $access_token) {
				$headers[] = "Authorization: OAuth2 " . $access_token;
			}

			$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
			curl_setopt($ci, CURLOPT_URL, $url);
			curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);

			$response = curl_exec($ci);
			$http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);

			if ($debug) {
				echo "=====post data======\r\n";
				var_dump($params);

				echo '=====info=====' . "\r\n";
				print_r(curl_getinfo($ci));

				echo '=====$response=====' . "\r\n";
				print_r($response);
			}
			curl_close($ci);
			$user_message = json_decode($response, true);

			$nickname = $user_message['screen_name'];
			if (empty($nickname)) {
				$nickname = '微博用户';
			}

			$xinlang_user = $this->user_obj->filter_xinlang($uid);
			if (!empty($xinlang_user)) {
				$this->user_obj->fast_login($xinlang_user);

			} else {
				$this->user_obj->fast_register(array('user_name' => $nickname, 'union_sina' => $uid, 'email' => "usina_$uid@m.com"));
			}

			$this->user_obj->update_user_info();
			if (!$back_url = $this->session->userdata('back_url')) {
				$back_url = $this->session->userdata('referer_url');
			}

			if (!$back_url) {
				$back_url = '';
			}

			if (substr($back_url, 0, 4) !== 'http') {
				$back_url = base_url($back_url);
			}

			$this->session->unset_userdata('back_url');
			header("Location:$back_url");

		} else {
			header("Content-type:text/html;charset=utf-8");
			$msg = '<p>您的新浪帐号未返回用户信息</p>
			    <p>将在 <span id="mes">5</span> 秒钟后返回</p>
			    <script language="javascript" type="text/javascript">
				    var i = 5;
				    var intervalid;
				    intervalid = setInterval("fun()", 1000);
				    function fun() {
					if (i == 0) {
					    history.go(-1);
					    clearInterval(intervalid);
					}
					document.getElementById("mes").innerHTML = i;
					i--;
				    }
				</script>';
			echo $msg;
		}

	}

	public function fc_login() {
		$this->config->load('fclub', TRUE);
		$appid = $this->config->item('appid', 'fclub');
		$callback = $this->config->item('callback', 'fclub');
		$appkey = $this->config->item('appkey', 'fclub');
		$request = $this->config->item('request', 'fclub');
		$state = md5(uniqid(rand(), TRUE));
		$this->session->set_userdata('fclub_state', $state);
		$md5_str = md5(strtolower($appid . "_" . $appkey . "_" . $state));
		$login_url = $request . "?id=" . $appid . "&callback=" . urlencode($callback)
			. "&state=" . $state . "&sign=" . $md5_str;
		header("Location:$login_url");
	}

	public function fc_callback() {
		$this->config->load('fclub', TRUE);
		$state = $this->session->userdata('fclub_state');
		$appid = $this->config->item('appid', 'fclub');
		$appkey = $this->config->item('appkey', 'fclub');
		$filter = $_GET; //$call_back = $call_back."?id=" . BAOBEIGOU_ID . "&state=" . $state."&sign=".$md5_str ."&u_id=".$data['user_id']
		//验签，验state,验参
		if (empty($filter['id']) || empty($filter['state']) || empty($filter['sign']) || empty($filter['u_id']) || empty($filter['u_name'])) {
			echo "<h3>error1:</h3>";
			echo "msg  :参数不完整";
			exit;
		}
		if ($filter['id'] != $appid || $state != $filter['state']) {
			echo "<h3>error2:</h3>";
			echo "msg  :参数不合法";
			exit;
		}
		$md5_str = md5(strtolower($appid . "_" . $appkey . "_" . $state));
		if ($md5_str != $filter['sign']) {
			echo "<h3>error3:</h3>";
			echo "msg  :验签不通过";
			exit;
		}
		//login
		$this->session->set_userdata('fclub_uid', $filter['u_id']);
		$u_id = $filter['u_id'];

		$fc_user = $this->user_obj->filter_fclub($u_id);
		if (empty($fc_user)) {
//查看是否已经注册，no,新增（u_id@f-club.cn）；yes,查看是否完善信息,执行下一步
			$fc_user['union_fclub'] = $filter['u_id'];
			$fc_user['user_name'] = $filter['u_name'];
			$fc_user['email'] = "ufc_$filter[u_id]@m.com";
			$fc_user = $this->user_obj->simple_register($fc_user); //注册送券
		}
		//记录登录
		$this->user_obj->save_user_session($fc_user);
		$this->user_obj->update_user_info();

		$back_url = $this->user_obj->get_back_url();
		//查看是否已经完善信息，no，提示完善信息，完善则更新信息；yes,直接登录
		if ($fc_user->password == '******' && $fc_user->email == "ufc_$fc_user->union_fclub@m.com") {
//未完善，登录，提示完善资料
			$data['back_url'] = $back_url;
//		$data['full_page'] = TRUE;
			$this->load->view('user/comp_info', $data);
		} else {
//完善过资料，登录并返回
			$this->session->unset_userdata('back_url');
			header("Location:$back_url");
			echo "<script>window.close();</script>";
		}
	}

	/**
	 * 聚尚联合登录完善资料，更新帐号信息
	 */
	public function comp_info() {
		$err_msg = '';
//	$auth_code = $this->session->userdata("auth_code");
		//	if (strtolower($auth_code) != strtolower(trim($this->input->post('captcha')))) {
		//	    $err_msg = "请输入正确的验证码！";
		//	}
		$param['email'] = trim($this->input->get_post('email'));
		$param['user_name'] = trim($this->input->get_post('user_name'));
		$param['password'] = trim($this->input->get_post('password'));

		if ($err_msg == '' && $this->user_obj->is_email_register($param['email'])) {
			$err_msg = "此帐号已存在，请重新输入。";
		}
		$user_id = $this->session->userdata('user_id');
		if (empty($user_id)) {
			$err_msg = '联合登录出错，请重试！';
		}

		if ($err_msg != '') {
			echo json_encode(array('error' => 1, 'message' => $err_msg));
			exit;
		} else {
			$this->user_obj->comp_info($param, $user_id);
			$back_url = $this->user_obj->get_back_url();
			$this->session->unset_userdata('back_url');
			header("Location:$back_url");
			echo "<script>window.close();</script>";
		}
	}

	public function getHeader($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}

	public function alipay_login() {
		$this->load->library('alipay');
		$html_text = $this->alipay->alipay_auth_authorize();
		echo $html_text;
	}

	public function alipay_callback() {
		//计算得出通知验证结果
		$this->load->library('alipay');
		$verify_result = $this->alipay->verifyLoginReturn();
		if ($verify_result) {
//验证成功

			$user_id = $_GET['user_id']; //支付宝用户id
			$token = $_GET['token']; //授权令牌
			$this->session->set_userdata('alipay_token', $token);

			$nickname = isset($_GET['real_name']) ? $_GET['real_name'] : '';
			if (empty($nickname)) {
				$nickname = '支付宝用户';
			}

			$alipay_user = $this->user_obj->filter_alipay($user_id);
			if (!empty($alipay_user)) {
				$this->user_obj->fast_login($alipay_user);

			} else {
				$this->user_obj->fast_register(array('user_name' => $nickname, 'union_zhifubao' => $user_id, 'email' => "ualipay_$user_id@m.com"));
			}

			$this->user_obj->update_user_info();
			if (!$back_url = $this->session->userdata('back_url')) {
				$back_url = $this->session->userdata('referer_url');
			}
			if (!$back_url) {
				$back_url = 'user';
			}

			if (substr($back_url, 0, 4) !== 'http') {
				$back_url = site_url($back_url);
			}

			$this->session->unset_userdata('back_url');
			//etao专用
			if (($target_url = $this->input->get('target_url')) != '') {
				//程序自动跳转到target_url参数指定的url去
				$back_url = $target_url;
			}
			header("Location:$back_url");

		} else {
			echo "验证失败";
		}
	}

	//微信联合登陆出口
	public function weixin_login() {
		$state = mt_rand(100000, 999999);
		$this->session->set_userdata('weixin_state', $state);
		$weixin_config = $this->config->item('weixin');
		$redirect_url = "https://open.weixin.qq.com/connect/qrconnect?appid=" . $weixin_config['appid'] . "&redirect_uri=" . $weixin_config['callback_url'] . "&response_type=CODE&scope=snsapi_login&state=" . $state;
		header("Location:$redirect_url");
	}
	//微信联合登陆回调
	public function weixin_callback() {
		$weixin_config = $this->config->item('weixin');
		$code = $_REQUEST['code'];
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $weixin_config['appid'] . '&secret=' . $weixin_config['appsecret'] . '&code=' . $code . '&grant_type=authorization_code';
		$token_result = get_url_contents($url);
		$token_array = json_decode($token_result, true);
		if (!empty($token_array['errcode'])) {
			exit($token_array['errmsg']);
		}
		$check_url = 'https://api.weixin.qq.com/sns/auth?access_token=' . $token_array['access_token'] . '&openid=' . $token_array['openid'];
		$check_result = get_url_contents($check_url);
		$check_array = json_decode($check_result, true);
		if (!empty($check_array['errcode'])) {
			$refresh_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $token_array['openid'] . '&grant_type=refresh_token&refresh_token=' . $token_array['refresh_token'];
			$refresh_result = get_url_contents($refresh_url);
		}
		$user_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $token_array['access_token'] . '&openid=' . $token_array['openid'];
		$user_result = get_url_contents($user_url);
		$user_array = json_decode($user_result, true);
		if (!empty($user_array['errcode'])) {
			exit($user_array['errmsg']);
		}

		$user_id = $user_array['unionid']; //微信开放平台用户id
		$sex = (int) $user_array['sex'];
		$nickname = isset($user_array['nickname']) ? $user_array['nickname'] : '';
		if (empty($nickname)) {
			$nickname = '微信用户';
		}

		$weixin_user = $this->user_obj->filter_weixin($user_id);
		if (!empty($weixin_user)) {
			$this->user_obj->fast_login($weixin_user);
		} else {
			$this->user_obj->fast_register(array('user_name' => $nickname, 'union_weixin' => $user_id, 'email' => "uweixin_$user_id@m.com", 'sex' => $sex));
		}

		$this->user_obj->update_user_info();
		if (!$back_url = $this->session->userdata('back_url')) {
			$back_url = $this->session->userdata('referer_url');
		}
		if (!$back_url) {
			$back_url = '';
		}

		if (substr($back_url, 0, 4) !== 'http') {
			$back_url = base_url($back_url);
		}

		$this->session->unset_userdata('back_url');
		//etao专用
		if (($target_url = $this->input->get('target_url')) != '') {
			//程序自动跳转到target_url参数指定的url去
			$back_url = $target_url;
		}
		header("Location:$back_url");
	}

	public function qq_answer() {
		$qq = $this->config->item('qq');
		if (empty($qq) || !is_array($qq)) {
			echo "<script>window.close();</script>";
			exit;
		}
		$num = rand(0, count($qq) - 1);
		$url = "http://wpa.qq.com/msgrd?v=3&uin=" . $qq[$num] . "&site=qq&menu=yes";
		header("Location:$url");
	}

	/**
	 * 检查用户是否登录
	 */
	public function check_is_login() {
		$user_id = $this->user_id;

		if ($user_id > 0) {
			$user_info = $this->user_obj->get_profile($user_id);
			echo json_encode(array('is_login' => true, 'user_nick' => $user_info->user_name, 'user_id' => $user_info->user_id));
		} else {
			echo json_encode(array('is_login' => false));
		}
	}

	/**
	 * 退货申请单列表
	 */
	function apply_return_list() {
		$this->load->model('apply_return_model');
		//获取数据
		$data = $this->get_apply_return_info_list();

		if ($this->input->post('is_ajax')) {
			$data['content'] = $this->load->view('user/apply_return_list', array(
				'list' => $data['list'],
				'filter' => $data['filter'],
				'full_page' => FALSE,
				'apply_status' => $data['filter']['apply_status'],
			), TRUE);
			$data['error'] = 0;
			$data['page_count'] = $data['filter']['page_count'];
			$data['page'] = $data['filter']['page'];
			$data['apply_status'] = $data['filter']['apply_status'];
			echo json_encode($data);
			return;
		} else {
			$this->load->view('user/apply_return_list', array(
				'list' => $data['list'],
				'filter' => $data['filter'],
				'left_sel' => 16,
				'full_page' => TRUE,
				'apply_status' => $data['filter']['apply_status'],
			));
		}
	}

	/**
	 * 获取申请单列表数据
	 */
	function get_apply_return_info_list() {
		if ($this->user_obj->is_login()) {
			$this->user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$filter['page'] = empty($_REQUEST['page']) || (intval($_REQUEST['page'])
			<= 0) ? 1 : intval($_REQUEST['page']);
		if (isset($_REQUEST['page_size']) && intval($_REQUEST['page_size']) > 0) {
			$filter['page_size'] = intval($_REQUEST['page_size']);
		} else {
			$filter['page_size'] = 10;
		}
		$filter['user_id'] = $this->user_id;
		$status = trim($this->input->post('status'));
		$status = empty($status) ? 1 : $status;
		$filter['apply_status'] = $status;
		$list = $this->apply_return_model->get_apply_return_info_list($filter);
		//如果某页(非第一页)只有一条数据 则删除后指向前一页数据
		if (empty($list["list"]) && $filter["page"] > 1) {
			$filter["page"] = $filter["page"] - 1;
			$list = $this->apply_return_model->get_apply_return_info_list($filter);
		}
		$data["action"] = "apply_return_list";
		$data["list"] = $list["list"];
		unset($filter["user_id"]);
		$data["filter"] = $list["filter"];
		return $data;
	}

	/**
	 * 自助退货
	 * @param type $order_id
	 * @param type $package_sn
	 */
	public function apply_return($order_id) {
		if ($this->user_obj->is_login()) {
			$this->user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$user_info = $this->user_obj->get_profile($this->user_id);

		$this->load->model('order_model');
		$this->load->model('apply_return_model');
		$this->load->helper('order_helper');

		$order_info_arr = $this->order_model->order_list(array('order_id' => $order_id), $this->user_id);
		if (empty($order_info_arr)) {
			redirect('/user/index');
		}
		$order_info = $order_info_arr['list'][0];
		//检查订单是否满足申请退货
		$can_apply_return = check_order_apply_return($order_info);
		if (!$can_apply_return) {
			redirect('/user/index');
		}
		//订单商品
		$order_product = $this->order_model->order_product($order_id);
		$data['order_info'] = $order_info;

		//查询已申请退货的商品
		$has_return_product = $this->apply_return_model->get_apply_return_product_by_order_id($order_id);

		//合作方式 天猫发货or第三方直发
		//if($order_info->provider_cooperation == 3 || $order_info->provider_cooperation == 4){
		$data['return_address'] = $order_info->return_address;
		$active_view = 'user/apply_return';
		//}
		//排除已申请退货的商品
		foreach ($order_product as $key => $product) {
			$sku = $product->product_id . '_' . $product->color_id . '_' . $product->size_id;
			if (isset($has_return_product[$sku])) {
				//订单商品数量大于申请退货的商品数量 则减去数量
				if ($product->product_num > $has_return_product[$sku]['product_number']) {
					$order_product[$key]->product_num -= $has_return_product[$sku]['product_number'];
				}
				//已退完 unset
				else {
					unset($order_product[$key]);
				}
			}
		}
		$data['provider_id'] = $order_info->provider_id;
		$data['order_product'] = $order_product;
		eval(APPLY_RETURN_REASON);
		$data['apply_return_reason'] = $return_reason_arr;
		$data['left_sel'] = 16;

		$this->load->view($active_view, $data);
	}

	/*
		     * 生成退货申请单
	*/
	function do_apply_return($order_id, $apply_id = 0) {
		if ($this->user_obj->is_login()) {
			$this->user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}

		$this->load->model('order_model');
		$this->load->model('apply_return_model');
		$this->load->helper('order_helper');

		$order_info_arr = $this->order_model->order_list(array('order_id' => $order_id), $this->user_id);
		if (empty($order_info_arr)) {
			redirect('/user/index');
		}
		$order_info = $order_info_arr['list'][0];
		//检查订单是否满足申请退货
		$can_apply_return = check_order_apply_return($order_info);
		if (!$can_apply_return) {
			redirect('/user/index');
		}
		//订单商品
		$order_product = $this->order_model->order_product($order_id);

		$chk_product = $this->input->post('chk_goods');
		if (empty($chk_product)) {
			return;
		}
		/*验证商品信息*/
		$temp_order_product = array();
		foreach ($order_product as $product_info) {
			$temp_order_product[$product_info->product_id . '_' . $product_info->color_id . '_' . $product_info->size_id] = $product_info;
		}
		$order_product = $temp_order_product;

		//查询已申请退货的商品
		$has_return_product = $this->apply_return_model->get_apply_return_product_by_order_id($order_id, $apply_id);

		if ($has_return_product) {
			foreach ($chk_product as $product_sku) {
				$product_sku_arr = explode('_', $product_sku);
				$check_product_flag = false;
				$product_info = $order_product[$product_sku];
				//提交的商品sku需要在订单商品中出现 并且数量小于订单中对应商品的数量
				if ($product_info->product_id == $product_sku_arr[0] && $product_info->color_id == $product_sku_arr[1]
					&& $product_info->size_id == $product_sku_arr[2]) {
					$has_return_num = $has_return_product[$product_sku]->product_num or $has_return_num = 0;
					if (($product_info->product_num - $has_return_num) >= $this->input->post('num_' . $product_sku)) {
						$check_product_flag = true;
					}
				}
				//商品验证未通过 非法商品信息
				if (!$check_product_flag) {
					exit('错误的商品信息');
				}
			}
		}

		//创建目录
		$base_path = './public/data/applyreturn/';
		$save_path = 'applyreturn/' . date('Y/m/d/');
		$img_path = $base_path . date('Y');
		if (!file_exists($img_path)) {
			mkdir($img_path, 0777);
		}

		$img_path .= '/' . date('m');
		if (!file_exists($img_path)) {
			mkdir($img_path, 0777);
		}

		$img_path .= '/' . date('d');
		if (!file_exists($img_path)) {
			mkdir($img_path, 0777);
		}

		$this->load->library('upload');
		$this->upload->initialize(array(
			'upload_path' => $img_path,
			'allowed_types' => 'jpg|png',
			'max_size' => 2048,
			'encrypt_name' => TRUE,
		));
		//存储图片
		$img_array = array();
		foreach ($chk_product as $product_sku) {
			for ($i = 1; $i <= 5; $i++) {
				$img_key = 'img_' . $product_sku . '_' . $i;
				if ($_FILES[$img_key]['size'] > 0) {
					if ($this->upload->do_upload($img_key)) {
						$upload_data = $this->upload->data();
						$img_name = $save_path . $upload_data['file_name'];
						if (empty($img_array[$product_sku])) {
							$img_array[$product_sku] = $img_name;
						} else {
							$img_array[$product_sku] .= ';' . $img_name;
						}
					} else {
						$img_flag = false;
					}
				} elseif (!empty($_FILES[$img_key]['name'])) {
					$img_flag = false;
				}
			}
		}

		//其他信息
		$shipping_name = $this->input->post('sel_shipping');
		if ($shipping_name == '-1') {
			$shipping_name = $this->input->post('shipping_name');
		}
		$shipping_num = $this->input->post('shipping_num');
		$shipping_fee = $this->input->post('shipping_fee');
		$user_name = $this->input->post('user_name');
		$mobile = $this->input->post('mobile');
		$tel = $this->input->post('tel');
		$apply_time = date('Y-m-d H:i:s');

		$apply_return_info['order_id'] = $order_id;
		$apply_return_info['user_id'] = $this->user_id;
		$apply_return_info['provider_id'] = $this->input->post('provider_id');
		//统计商品数量
		$product_num = 0;
		foreach ($chk_product as $product_sku) {
			$product_num += $this->input->post('num_' . $product_sku);
		}
		/*apply_return_info信息*/
		$apply_return_info['shipping_name'] = $shipping_name;
		$apply_return_info['invoice_no'] = $shipping_num;
		$apply_return_info['sent_user_name'] = $user_name;
		$apply_return_info['mobile'] = $mobile;
		$apply_return_info['tel'] = $tel;
		$apply_return_info['shipping_fee'] = $shipping_fee;
		$apply_return_info['back_address'] = $this->input->post('return_address');
		$apply_return_info['product_number'] = $product_num;
		$orderType = 0;
		if ($order_info->provider_cooperation == 3 || $order_info->provider_cooperation == 5) {
			$orderType = 0;
		} elseif ($order_info->provider_cooperation == 4 || $order_info->provider_cooperation == 6) {
			$orderType = 1;
		}
		$apply_return_info['order_type'] = $orderType;
		/*apply_return_goods信息*/
		$apply_return_product_arr = array();
		foreach ($chk_product as $product_sku) {
			$apply_return_product['apply_id'] = 0;
			$product_info = $order_product[$product_sku];
			$apply_return_product['product_id'] = $product_info->product_id;
			$apply_return_product['color_id'] = $product_info->color_id;
			$apply_return_product['size_id'] = $product_info->size_id;
			$apply_return_product['product_price'] = $product_info->shop_price;
			$apply_return_product['product_sn'] = $product_info->product_sn;
			$apply_return_product['product_name'] = $product_info->product_name;
			$apply_return_product['product_number'] = $this->input->post('num_' . $product_sku);
			$apply_return_product['return_reason'] = $this->input->post('reason_' . $product_sku);
			$apply_return_product['description'] = htmlspecialchars($this->input->post('desc_' . $product_sku));
			if ($apply_return_product['description'] == '请您详细的说明您有问题的商品，这样方面我们能够更加直接迅速的处理您的退货') {
				$apply_return_product['description'] = '';
			}
			$apply_return_product['img'] = $img_array[$product_sku];
			$apply_return_product_arr[] = $apply_return_product;
		}
		//添加申请单
		if ($apply_id == 0) {
			$apply_return_info['apply_time'] = $apply_time;
			$this->apply_return_model->add_apply_return_info($apply_return_info, $apply_return_product_arr);
		}
		//修改申请单
		else {
			$del_product_img = array();
			//检查是否有删除图片
			foreach ($chk_product as $product_sku) {
				for ($i = 1; $i <= 5; $i++) {
					$del_img = $this->input->post('del_img_' . $product_sku . '_' . $i);
					if (!empty($del_img)) {
						$del_product_img[$product_sku][] = $del_img;
					}
				}
			}
			$this->apply_return_model->update_apply_return_info($apply_return_info, $apply_return_product_arr, $apply_id, $del_product_img);
		}
		redirect('/user/apply_return_success/' . ($img_flag === false ? 1 : 0));
	}

	/**
	 * 申请退货成功
	 */
	function apply_return_success($type = 0) {
		$data['msg'] = ($type == 0 ? '' : '(格式不正确或过大的图片未进行保存,请重新上传)');
		$this->load->view("user/apply_return_success", $data);
	}

	/**
	 * 查看退货单信息
	 */
	function apply_return_view($apply_id) {
		if ($this->user_obj->is_login()) {
			$this->user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$this->load->model('apply_return_model');
		$apply_return_info = $this->apply_return_model->get_apply_return_info_by_apply_id($apply_id, $this->user_id);
		if (empty($apply_return_info)) {
			exit('非法查看信息');
		}
		$data['apply_return_info'] = $apply_return_info;
		//用户取消
		if ($apply_return_info['apply_status'] == 3) {
			$data['cancel_time'] = $apply_return_info['cancel_time'];
		}
		$apply_return_product = $this->apply_return_model->get_apply_return_product($apply_id, $this->user_id);
		$data['apply_return_product'] = $apply_return_product;

		//跟踪退货信息
		//已退货入库的商品
		$returned_product = $this->apply_return_model->get_apply_return_transaction($apply_id);
		$new_return_product = array();
		foreach ($apply_return_product as $key => $product) {
			$sku = $product['product_id'] . '_' . $product['color_id'] . '_' . $product['size_id'];
			if (isset($returned_product[$sku])) {
				$returned_product[$sku]['product_sn'] = $product['product_sn'];
				$new_return_product[$returned_product[$sku]['is_ok_date'] . $sku] = $returned_product[$sku];
			}
		}
		ksort($new_return_product);
		$data['returned_product'] = $new_return_product;
		$data['is_ok_date'] = $this->apply_return_model->get_apply_return_ok_date($apply_id);
		$data['apply_id'] = $apply_id;
		$data['left_sel'] = 16;

		$this->load->view("user/apply_return_view", $data);
	}

	/**
	 * 取消退货申请单
	 */
	function cancel_apply_return($apply_id) {
		if ($this->user_obj->is_login()) {
			$this->user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$this->load->model('apply_return_model');
		$apply_return_info = $this->apply_return_model->get_apply_return_info_by_apply_id($apply_id, $this->user_id);
		if ($apply_return_info['apply_status'] != 0 || $apply_return_info['provider_status'] != 0) {
			exit('can not cancel');
		}
		$this->apply_return_model->cancel_apply_return_info($apply_id, $this->user_id);
		$data['left_sel'] = 16;
		redirect('/user/apply_return_view/' . $apply_id);
	}

	/**
	 * 修改申请退货单
	 */
	function modify_apply_return_info($apply_id = 0) {
		if ($this->user_obj->is_login()) {
			$this->user_id = $this->session->userdata('user_id');
		} else {
			redirect('/user/login');
		}
		$this->load->model('order_model');
		$this->load->model('apply_return_model');
		$this->load->helper('order_helper');
		$apply_return_info = $this->apply_return_model->get_apply_return_info_by_apply_id($apply_id, $this->user_id);
		if (empty($apply_return_info)) {
			exit('wrong');
		}
		if ($apply_return_info['apply_status'] != 0) {
			exit('can not modify');
		}
		$order_id = $apply_return_info['order_id'];
		$order_info_arr = $this->order_model->order_list(array('order_id' => $order_id), $this->user_id);
		if (empty($order_info_arr)) {
			redirect('/user/index');
		}
		$order_info = $order_info_arr['list'][0];
		$order_product = $this->order_model->order_product($order_id);
		//合作方式 天猫发货or第三方直发
		//if($order_info->provider_cooperation == 3 || $order_info->provider_cooperation == 4){
		$data['return_address'] = $apply_return_info['back_address'];
		$active_view = 'user/apply_return_edit';
		//}
		$apply_return_product = $this->apply_return_model->get_apply_return_product_by_apply_id($apply_id);
		$has_return_product = $this->apply_return_model->get_apply_return_product_by_order_id($order_id, $apply_id);
		foreach ($order_product as $key => $product) {
			$sku = $product->product_id . '_' . $product->color_id . '_' . $product->size_id;
			if (isset($has_return_product[$sku])) {
				if ($product->product_num > $has_return_product[$sku]['product_number']) {
					$order_product[$key]->product_num -= $has_return_product[$sku]['product_number'];
				} else {
					unset($order_product[$key]);
				}
			}
		}
		//如果商品有退货 则将退货信息追加到数组中
		foreach ($order_product as $key => $product) {
			$sku = $product->product_id . '_' . $product->color_id . '_' . $product->size_id;
			if (isset($apply_return_product[$sku])) {
				$apply_return_product[$sku]['imgs'] = array();
				if (!empty($apply_return_product[$sku]['img'])) {
					$apply_return_product[$sku]['imgs'] = explode(';', $apply_return_product[$sku]['img']);
				}
				$order_product[$key]->return_info = $apply_return_product[$sku];
			}
		}
		$data['order_info'] = $order_info;
		$data['order_product'] = $order_product;
		$data['apply_return_info'] = $apply_return_info;
		$data['apply_id'] = $apply_id;
		$data['provider_id'] = $apply_return_info['provider_id'];
		eval(APPLY_RETURN_REASON);
		$data['apply_return_reason'] = $return_reason_arr;
		$data['left_sel'] = 16;

		$this->load->view($active_view, $data);
	}
	/**
	 * 用户中心基本信息
	 * 20150921 lichao
	 */
	function profile_data() {
		$data['user_id'] = $this->session->userdata('user_id');
		$data['user_name'] = $this->session->userdata('user_name');
		$data['email'] = $this->session->userdata('email');
		$data['mobile'] = $this->session->userdata('mobile');
		$data['advar'] = $this->session->userdata('advar');
		die(json_encode(Array('success' => 1, 'message' => 'ok', 'data' => $data)));

	}

	public function send_sms_code() {

		if (!$this->input->post('is_ajax')) {
			echo json_encode(array('error' => 1, 'msg' => '错误的访问'));
			return;
		}
		$mobile = trim($this->input->post('mobile'));
		if (empty($mobile)) {
			echo json_encode(array('error' => 1, 'msg' => '非法的手机号'));
			return;
		}

		$smsCode = mt_rand(123123, 999999);

		$args = array(
			"authentication" => $smsCode,
			"mobile" => $mobile,
		);
		$sending_mobile_code_time = time();
		$msg = $this->user_obj->send_sync_sms($args, 'register_validate');

		$retry_times = intval($this->session->userdata('sending_retry_times')) + 1;

		if ($retry_times > 5) {
			echo json_encode(array('msg_send_result' => '多次重试错误!', 'mobile_check_err' => 2));
			exit;
		}

		if ('' == $msg) {
			$this->session->set_userdata('sending_mobile_code', $smsCode);
			$this->session->set_userdata('sending_mobile_phone', $mobile);
			$this->session->set_userdata('mobile', $mobile);
			$this->session->set_userdata('sending_mobile_code_time', $sending_mobile_code_time);
			//$this->session->set_userdata('msg_send_reult', true);
			echo json_encode(array('msg_send_result' => 0, 'mobile_check_err' => 0));
		} else {
			echo json_encode(array('msg_send_result' => '短信发送失败，请重试!', 'mobile_check_err' => 1));
			//$this->session->set_userdata('msg_send_reult', '短信发送失败，请重试!');
		}

	}

	public function proc_loginAndRegister() {

		$err_msg = '';

		if ($this->user_obj->is_login()) {
			echo json_encode(array('error' => 0, 'user_name' => $this->session->userdata('user_name'), 'rank_name' => $this->session->userdata('rank_name')));
			return;
		}

		$mobile = trim($this->input->post('username'));
		$password = trim($this->input->post('password'));
		$loginType = trim($this->input->post('loginType'));
		//loginType 0为默认手机号+验证码登陆，1为用户名+密码, 2为团购活动用户注册，系统将为他们自动生成随机密码
		//$password 0为默认验证码，1为密码

		//团购活动用户
		if ($loginType == 2 && empty($password)) {
			$password = getRandChar(6);
		}

		//错误检查
		if ('' == $mobile) {
			$err_msg = '用户名或手机号不能为空!';
		} else if ('' == $password) {
			$err_msg = ($loginType == 0 ? '验证码' : '密码') . '不能为空!';
		} else if ($loginType == 0 && !is_mobile_number($mobile)) {
			$err_msg = "错误手机号码";
		}

		if ($loginType == 0) {
			if ($mobile == $this->session->userdata('mobile') && $password == $this->session->userdata('sending_mobile_code')) {
				//do nothing, validation tagged as success
			} else {
				$err_msg = '验证码错误';
			}
		}

		if ($err_msg == '') {
			$err_no = $this->user_obj->dealRegisterOrlogin($loginType, $mobile, $password);
			switch ($err_no) {
			case 2:
				$err_msg = '登陆错误';
				break;
			case 4:
				$err_msg = '注册错误';
				break;
			case 8:
				$err_msg = '账号或用户名错误';
				break;
			}

		}

		if (empty($err_msg)) {
			$this->user_obj->update_user_info();
			if (!$back_url = $this->session->userdata('back_url')) {
				$back_url = $this->session->userdata('referer_url');
			}
			//		if (!$back_url)
			//		    $back_url = 'index';
			if (!empty($back_url) && substr($back_url, 0, 4) !== 'http') {
				$back_url = site_url($back_url);
				$this->session->unset_userdata('back_url');
			}

			$user_id = $this->session->userdata('user_id');

			//登录 成功 将 该用户收藏的 商品 写入session
			$collect_data = $this->user_model->get_collect_info(array('user_id' => $user_id));
			if (isset($_SESSION['collect_' . $user_id])) {
				array_push($collect_data, $_SESSION['collect_' . $user_id]);
			}
			$this->session->set_userdata('collect_' . $user_id, $collect_data);

			//登录 成功 将 该用户赞的 文章 写入session
			$this->load->model('wordpress_model');
			$praise_data = $this->wordpress_model->get_article_praise(array('user_id' => $user_id, 'type_source' => 'yyw_moblie'));
			if (isset($_SESSION['praise_' . $user_id])) {
				array_push($praise_data, $_SESSION['praise_' . $user_id]);
			}
			$this->session->set_userdata('praise_' . $user_id, $praise_data);

			die(json_encode(array('error' => 0, 'back_url' => $back_url, 'user_name' => $this->session->userdata('user_name'), 'rank_name' => $this->session->userdata('rank_name'))));
			return;
		} else {
			die(json_encode(array('error' => 1, 'message' => $err_msg)));

		}

	}
}
