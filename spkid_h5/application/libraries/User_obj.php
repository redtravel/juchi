<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

//include_once 'User_redis.php';
/**
 * 管理员类
 *
 * @author tony.liu
 */
class User_obj {

	private $_user_id = 0;

	public function __construct() {
		$this->CI = &get_instance();
		$this->_user_id = (int) $this->CI->session->userdata('user_id');
	}

	public function is_login() {
		return ($this->_user_id > 0) ? TRUE : FALSE;
	}

	/**
	 * 管理员登录， 如果成功记录相关session
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return boolean
	 */
	public function login($mobile = '', $password = '', $remember = 0) {
		$this->CI->load->model('user_model');

		//if( strpos($user_name,'@') !== false) $field = 'email';
		//else $field = 'mobile';
		if (is_mobile_number($mobile)) {
			$field = 'mobile';
		} else {
			$field = 'user_name';
		}

		$user = $this->CI->user_model->filter(array(
			$field => $mobile,
			'password' => m_encode($password),
		));

		if (!empty($user) && $user->is_use == 0 && m_encode($password) == $user->password) {
			// 保存会员ID，以此ID证明会员已登录
			$this->_user_id = (int) $user->user_id;
			if ($remember == 1) {
				$this->CI->session->sess_expiration = 0;
			}
			//会员等级计算
			if ($user->rank_id > 0) {
				$row = @$this->CI->user_model->filter_user_rank(array('rank_id' => $user->rank_id));
			} else {
				$row = @$this->CI->user_model->max_user_rank($user->paid_money);
			}
			if ($row) {
				$user->rank_name = $row->rank_name;
			} else {
				$user->rank_name = '新用户';
			}
			$this->CI->session->set_userdata('rank_name', $user->rank_name);
			$this->CI->session->set_userdata('user_id', $this->_user_id);
			$this->CI->session->set_userdata('user_name', $user->user_name);
			$this->CI->session->set_userdata('union_sina', $user->union_sina);
			$this->CI->session->set_userdata('union_zhifubao', $user->union_zhifubao);
			$this->CI->session->set_userdata('union_qq', $user->union_qq);
			$this->CI->session->set_userdata('union_fclub', $user->union_fclub);
			//$this->CI->session->set_userdata('email', $user->email);
			$this->CI->session->set_userdata('mobile', $user->mobile);
			$this->CI->session->set_userdata('user_type', $user->user_type);
			$this->CI->session->set_userdata('advar', $user->user_advar);
			$this->CI->session->set_userdata('mobile_checked', $user->mobile_checked);
			$this->CI->session->set_userdata('discount_percent', round(floatval($user->discount_percent), 2));
			unset($user);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function fast_login($user) {
		if (!empty($user)) {
			// 保存会员ID，以此ID证明会员已登录
			$this->_user_id = (int) $user->user_id;
			$this->CI->session->set_userdata(array(
				'user_id' => $this->_user_id,
				'user_name' => $user->user_name,
				'union_sina' => $user->union_sina,
				'union_zhifubao' => $user->union_zhifubao,
				'union_qq' => $user->union_qq,
				'union_fclub' => $user->union_fclub,
				'email' => $user->email,
				'mobile' => $user->mobile,
				'user_type' => $user->user_type,
				'email_validated' => $user->email_validated,
				'mobile_checked' => $user->mobile_checked,
				'discount_percent' => round(floatval($user->discount_percent), 2),
			));
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function fast_register($user_input) {
		$this->CI->load->model('user_model');

		$now = date('Y-m-d H:i:s');
		$user_input['password'] = '******';
		$user_input['visit_count'] = 1;
		$user_input['create_date'] = $now;

		$this->CI->db->trans_begin(); //transaction start. dadada...
		$user_id = $this->CI->user_model->insert($user_input);
		if (empty($user_id)) {
			$this->CI->db->trans_rollback();
			return FALSE;
		}

		$user = $this->CI->user_model->filter(array('user_id' => $user_id));
		if ($user_id > 0) {
			$this->_user_id = (int) $user->user_id;
			$this->CI->session->set_userdata(array(
				'user_id' => $this->_user_id,
				'user_name' => $user->user_name,
				'union_sina' => $user->union_sina,
				'union_zhifubao' => $user->union_zhifubao,
				'union_qq' => $user->union_qq,
				'union_fclub' => $user->union_fclub,
				'email' => $user->email,
				'mobile' => $user->mobile,
				'user_type' => $user->user_type,
				'email_validated' => $user->email_validated,
				'mobile_checked' => $user->mobile_checked,
				'discount_percent' => round(floatval($user->discount_percent), 2),
			));
			//发放现金券
			$this->CI->load->model('voucher_model');
			$this->CI->voucher_model->release_register_voucher($user->user_id);

			$this->CI->db->trans_commit();
			return TRUE;
		}
		$this->CI->db->trans_rollback();
		return FALSE;
	}

	public function get_back_url() {
		if (!$back_url = $this->CI->session->userdata('back_url')) {
			$back_url = $this->CI->session->userdata('referer_url');
		}
		if (!$back_url) {
			$back_url = 'user';
		}

		if (substr($back_url, 0, 4) !== 'http') {
			$back_url = site_url($back_url);
		}

		return $back_url;
	}

	/**
	 * 完善信息
	 */
	public function comp_info($param, $user_id) {
		$this->CI->load->model('user_model');

		$user_input = array();
		if (strpos($param['email'], '@') !== false) {
			$field = 'email';
		} else {
			$field = 'mobile';
		}

		$user_input['user_name'] = $param['user_name'];
		$user_input['password'] = m_encode($param['password']);
		$user_input[$field] = $param['email'];
		$user_id = $this->CI->session->userdata('user_id');
		$this->CI->user_model->update($user_input, $user_id);

		$this->CI->session->set_userdata('user_name', $param['user_name']);
		$this->CI->session->set_userdata('email', $field == 'email' ? $param['email'] : '');
		$this->CI->session->set_userdata('mobile', $field == 'mobile' ? $param['email'] : '');
	}

	public function simple_register($user_input) {
		$this->CI->load->model('user_model');
		$user_input['password'] = '******';
		$user_input['visit_count'] = 1;
		$user_input['create_date'] = date('Y-m-d H:i:s');

		$this->CI->db->trans_begin();
		$user_id = $this->CI->user_model->insert($user_input);
//	    if (empty($user_id)) {
		//		$this->CI->db->trans_rollback();
		//		return FALSE;
		//	    }

		if ($user_id > 0) {
			//发放现金券
			$this->CI->load->model('voucher_model');
			$this->CI->voucher_model->release_register_voucher($user_id);
			$this->CI->db->trans_commit();
		}
		$this->CI->db->trans_rollback();
		$user = $this->CI->user_model->filter(array('user_id' => $user_id));
		return $user;
	}

	public function save_user_session($user) {
		$user->user_type = isset($user->user_type) && !empty($user->user_type) ? $user->user_type : 0;
		$user->email_validated = isset($user->email_validated) && !empty($user->email_validated) ? $user->email_validated : 0;
		$user->mobile_checked = isset($user->mobile_checked) && !empty($user->mobile_checked) ? $user->mobile_checked : 0;
		$user->discount_percent = isset($user->discount_percent) && !empty($user->discount_percent) ? $user->discount_percent : 1;

		$this->_user_id = (int) $user->user_id;
		$this->CI->session->set_userdata(array(
			'user_id' => $this->_user_id,
			'user_name' => $user->user_name,
			'union_sina' => $user->union_sina,
			'union_zhifubao' => $user->union_zhifubao,
			'union_qq' => $user->union_qq,
			'union_fclub' => $user->union_fclub,
			'email' => $user->email,
			'mobile' => $user->mobile,
			'user_type' => $user->user_type,
			'email_validated' => $user->email_validated,
			'mobile_checked' => $user->mobile_checked,
			'discount_percent' => round(floatval($user->discount_percent), 2),
		));
	}

	public function filter_qq($qq_id) {
		$this->CI->load->model('user_model');
		if (empty($qq_id)) {
			return array();
		}

		$user = $this->CI->user_model->filter(array(
			'union_qq' => $qq_id,
		));
		if (!empty($user) && $user->is_use == 0) {
			return $user;
		} else {
			return array();
		}
	}

	public function filter_fclub($u_id) {
		$this->CI->load->model('user_model');
		if (empty($u_id)) {
			return array();
		}

		$user = $this->CI->user_model->filter(array(
			'union_fclub' => $u_id,
		));
		if (!empty($user) && $user->is_use == 0) {
			return $user;
		} else {
			return array();
		}
	}

	public function filter_xinlang($sina_id) {
		$this->CI->load->model('user_model');
		if (empty($sina_id)) {
			return array();
		}

		$user = $this->CI->user_model->filter(array(
			'union_sina' => $sina_id,
		));
		if (!empty($user) && $user->is_use == 0) {
			return $user;
		} else {
			return array();
		}
	}

	public function filter_alipay($alipay_id) {
		$this->CI->load->model('user_model');
		if (empty($alipay_id)) {
			return array();
		}

		$user = $this->CI->user_model->filter(array(
			'union_zhifubao' => $alipay_id,
		));
		if (!empty($user) && $user->is_use == 0) {
			return $user;
		} else {
			return array();
		}
	}

	public function filter_user($user_name = '') {
		$this->CI->load->model('user_model');

		//if( strpos($user_name,'@') !== false) $field = 'email';
		$field = 'mobile';

		$user = $this->CI->user_model->filter(array(
			$field => $user_name,
		));
		if (!empty($user) && $user->is_use == 0) {
			return $user;
		} else {
			return array();
		}
	}

	/**
	 * 验证邮箱是否已注册
	 * @param string $email
	 * @return boolean
	 */
	public function is_mobile_register($mobile = '') {
		$this->CI->load->model('user_model');
		//if( strpos($user_email,'@') !== false) $field = 'email';
		//$field = 'mobile';
		$user = $this->CI->user_model->filter(array(
			'mobile' => $mobile,
		));

		return empty($user) ? FALSE : TRUE;
	}

	/**
	 * 验证用户是否已注册
	 * @param string $user_name 用户名
	 * @return boolean
	 */
	public function is_username_register($user_name = '') {
		$this->CI->load->model('user_model');
		$user = $this->CI->user_model->filter(array(
			'user_name' => $user_name,
		));
		return empty($user) ? FALSE : TRUE;
	}

	/**
	 * 用户注册， 如果成功记录相关session
	 * @param array $param 用户数据数组
	 * @return boolean
	 */
	public function register($param = array()) {
		$this->CI->load->model('user_model');
		//$my_user_redis = new user_redis();
		$user_input = array();
		$now = date('Y-m-d H:i:s');

		//if( strpos($param['email'],'@') !== false) $field = 'email';
		//else $field = 'mobile';
		$field = 'mobile';
		$user_input['user_name'] = $param['user_name'];
		$user_input['password'] = m_encode($param['password']);
		$user_input['mobile'] = $param['mobile'];
		$user_input['mobile_checked'] = 1;
		$user_input['last_ip'] = real_ip();
		$user_input['last_date'] = $now;
		$user_input['visit_count'] = 1;
		$user_input['create_date'] = $now;
		//$user_input['user_id'] = $my_user_redis->get_user_id();

		$this->CI->db->trans_begin(); //transaction start....
		$user_id = $this->CI->user_model->insert($user_input);

		if ($user_id > 0) {
			//注册后，自动登录
			$this->_user_id = (int) $user_id;
			$this->CI->session->set_userdata('user_id', $this->_user_id);
			$this->CI->session->set_userdata('user_name', $param['user_name']);
			$this->CI->session->set_userdata('union_sina', '');
			$this->CI->session->set_userdata('union_zhifubao', '');
			$this->CI->session->set_userdata('union_qq', '');
			$this->CI->session->set_userdata('union_fclub', '');
			$this->CI->session->set_userdata('email', $field == 'email' ? $param['email'] : '');
			$this->CI->session->set_userdata('mobile', $param['mobile']);
			$this->CI->session->set_userdata('user_type', 0);
			$this->CI->session->set_userdata('email_validated', 0);
			$this->CI->session->set_userdata('mobile_checked', 1);
			$this->CI->session->set_userdata('discount_percent', 1);

			if ($field == 'email') {
				$valid_string = substr(md5($this->_user_id . 'fevalid' . $param['email']), 5, 15);
				$valid_string = FRONT_HOST . "/user/checkemailv/val/" . $valid_string;
				@$this->SendSyncMail(array('user_id' => $this->_user_id, 'user_name' => $param['user_name'], 'confirm_url' => $valid_string, 'to_email' => $param['email'], 'email' => $param['email'], 'password' => $param['password']), 'register_user');
			} else {
				@$this->send_sync_sms(array('user_id' => $this->_user_id, 'user_name' => '', 'mobile' => $param['mobile'], 'password' => $param['password']), 'register_user');
			}

			unset($user_input);

			//新用户注册 发放现金券
			$this->CI->load->model('voucher_model');
			$this->CI->voucher_model->release_register_voucher($user_id);

			//注册送积分
			/*
			if ( USE_REGIST_POINT && !$this->CI->user_model->point_type_exists($user_id, 'regist_point'))
			{
				$point_amount = $this->get_user_rank_point($user_id,'regist_point');
				if( $point_amount >0 ){// 0为暂时取消
					$this->CI->user_model->log_account_change($user_id, 0, $point_amount, $point_amount, '注册送积分', 'regist_point');
				}
			}
             */

			$this->CI->db->trans_commit();
			return TRUE;
		}
		$this->CI->db->trans_rollback();
		return FALSE;
	}

	/**
	 * 返回管理员的相关属性，不指定参数则返回所有属性
	 * @param string $key
	 * @return mixed
	 */
	public function get_data($key = NULL) {
		if (!$this->is_login()) {
			return ($key === NULL) ? '' : array();
		}
		$user_data = $this->CI->session->userdata('user_data');
		if ($key === NULL) {
			return $user_data;
		}
		return isset($user_data[$key]) ? $user_data[$key] : '';
	}

	/**
	 * 更新用户SESSION,COOKIE及登录时间、登录次数。
	 *
	 * @access  public
	 * @other  Array( field=>value, ... );
	 * @return  void
	 */
	function update_user_info($other = array()) {
		if (!$this->is_login()) {
			return false;
		}

		/* 查询会员信息 */
		$time = date('Y-m-d');
		$row = $this->CI->user_model->filter(array('user_id' => $this->_user_id));
		if ($row) {
			$this->CI->session->set_userdata('last_date', $row->last_date);
			$this->CI->session->set_userdata('last_ip', $row->last_ip);
			$this->CI->session->set_userdata('login_fail', 0);
			/* 取得用户等级和折扣 */
			if ($row->rank_id == 0) {
				// 根据等级积分计算用户等级
				$rank_row = $this->CI->user_model->max_user_rank($row->paid_money);
				if ($rank_row) {
					$this->CI->session->set_userdata('rank_id', $rank_row->rank_id);
					$this->CI->session->set_userdata('rank_name', $rank_row->rank_name);
					$this->CI->session->set_userdata('rank_discount', $rank_row->discount);
				} else {
					$this->CI->session->set_userdata('rank_id', 0);
					$this->CI->session->set_userdata('rank_name', '新用户');
					$this->CI->session->set_userdata('rank_discount', 1);
				}
			} else {
				$rank_row = $this->CI->user_model->filter_user_rank(array('rank_id' => $row->rank_id));
				if ($rank_row) {
					$this->CI->session->set_userdata('rank_id', $rank_row->rank_id);
					$this->CI->session->set_userdata('rank_name', $rank_row->rank_name);
					$this->CI->session->set_userdata('rank_discount', $rank_row->discount);
				} else {
					$this->CI->session->set_userdata('rank_id', 0);
					$this->CI->session->set_userdata('rank_name', '新用户');
					$this->CI->session->set_userdata('rank_discount', 1);
				}
			}
		}

		$setter = '';
		foreach ($other AS $field => $value) {
			$setter .= ',' . $field . '=' . (is_int($value) ? '' : "'") . $value . (is_int($value) ? '' : "'");
		}

		/* 更新登录时间，登录次数及登录ip */
		$this->CI->user_model->update_login_other($setter, $this->_user_id);
		$session_id = $this->CI->session->userdata('session_id');
		$this->CI->user_model->update_login_cart($session_id, $this->_user_id);
	}

	/**
	 * 获取用户帐号信息
	 *
	 * @access  public
	 * @param   int       $user_id        用户user_id
	 *
	 * @return void
	 */
	function get_profile($user_id) {
		/* 会员帐号信息 */
		$info = new stdClass();
		$infos = $this->CI->user_model->filter(array('user_id' => $user_id));
		if (empty($infos->user_id)) {
			return array();
		}
		$infos->user_name = addslashes($infos->user_name);
		$this->CI->session->set_userdata('email', $infos->email);
		/* 会员等级 */
		if ($infos->rank_id > 0) {
			$row = $this->CI->user_model->filter_user_rank(array('rank_id' => $infos->rank_id));
		} else {
			$row = $this->CI->user_model->max_user_rank($infos->paid_money);
		}

		if ($row) {
			$info->rank_name = $row->rank_name;
		} else {
			$info->rank_name = '新用户';
		}

		$cur_date = date('Y-m-d H:i:s');
		$info->email = $infos->email;
		$info->user_name = $infos->user_name;
		//如果$_SESSION中时间无效说明用户是第一次登录。取当前登录时间。
		$last_date = $this->CI->session->userdata('last_date');
		$last_date = empty($last_date) || $last_date == '0000-00-00 00:00:00' ? $infos->last_date : $last_date;

		if (empty($last_date) || $last_date == '0000-00-00 00:00:00') {
			$last_date = $cur_date;
			$this->CI->session->set_userdata('last_date', $last_date);
		}

		$info->paid_money = isset($infos->paid_money) ? $infos->paid_money : 0;
		//$info->rank_points = isset($infos->rank_points) ? $infos->rank_points : '';
		$info->pay_points = isset($infos->pay_points) ? $infos->pay_points : 0;
		$info->user_money = isset($infos->user_money) ? $infos->user_money : 0;
		$info->user_money_single = isset($infos->user_money) ? $infos->user_money : 0;
		$info->sex = isset($infos->sex) ? $infos->sex : 0;
		$info->identity_code = !empty($infos->identity_code) ? $infos->identity_code : (!empty($infos->passport_code) ? $infos->passport_code : '');
		$info->identity_type = !empty($infos->identity_code) ? '1' : (!empty($infos->passport_code) ? '2' : '0');
		$info->real_name = empty($infos->real_name) ? '' : addslashes($infos->real_name);

		$info->birthday = isset($infos->birthday) ? $infos->birthday : '';
		$info->baby_birthday = isset($infos->baby_birthday) ? $infos->baby_birthday : '';
		$info->baby_sex = isset($infos->baby_sex) ? $infos->baby_sex : 0;
		$info->baby_name = empty($infos->baby_name) ? '' : addslashes($infos->baby_name);

		$info->rank_id = $infos->rank_id;
		$info->user_rank_img = $infos->rank_id ? '/data/user_rank/' . $infos->rank_id . '.gif' : '';
		$info->union_sina = $infos->union_sina;
		$info->union_zhifubao = $infos->union_zhifubao;
		$info->union_qq = $infos->union_qq;
		$info->union_fclub = $infos->union_fclub;

		$info->user_money = number_format($info->user_money, 2, '.', '');
		$info->pay_points_single = $info->pay_points;
		$info->pay_points = $info->pay_points;
		$info->mobile = $infos->mobile;
		$info->email_validated = $infos->email_validated;
		$info->mobile_checked = $infos->mobile_checked;
		$info->rank_id = $infos->rank_id;
		$info->address_id = $infos->address_id;
		$info->password = $infos->password;
		$info->favorite_category = @$infos->favorite_category;

		//$info['user_rank'] = '/data/user_rank/'.$info['user_rank'].'.gif';
		if ($info->email_validated == 0) {
			$info->validatestring = substr(md5($user_id . "fvalidate" . $infos->email), 5, 15);
		}

		$info->short_user_name = _smarty_modifier_truncate($infos->user_name, 12, '.');
		$info->baby_name = $infos->baby_name;
		$info->baby_sex = $infos->baby_sex;
		$info->baby_birthday = $infos->baby_birthday;
		$info->user_id = $user_id;
		$info->company_name = $infos->company_name;
		$info->company_type = $infos->company_type;
		$info->company_position = $infos->company_position;
/*
$sql = "SELECT COUNT(id) FROM " .$GLOBALS['ecs']->table('email_list') . " WHERE user_id = '$user_id' AND stat = 1";
if($GLOBALS['db']->getOne($sql) == 1)
{
$info['maillist'] = 1;
}
else
{
$info['maillist'] = 2;
}
 */
		return $info;
	}

	/**
	 * 重新计算购物车中的商品价格：目的是当用户登录时享受会员价格，当用户退出登录时不享受会员价格
	 * 如果商品有促销，价格不变
	 *
	 * @access  public
	 * @return  void
	 */
	function recalculate_price() {
		/* 如果是代理商，则更新商品的价格 */
		$user_type = $this->CI->session->userdata('user_type');
		$flow_cart = $this->CI->session->userdata('flow_cart');
		$discount_percent = $this->CI->session->userdata('discount_percent');
		if ($user_type == 1 && is_array($flow_cart)) {
			foreach ($flow_cart as $key => $cart) {
				if (empty($cart['package'])) {
					$cart['is_promote'] = 0;
					$cart['product_price'] = round($cart['shop_price'] * $discount_percent, 2);
					$cart['discount_type'] = 4;
					$flow_cart[$key] = $cart;
				}
			}
			$this->CI->session->set_userdata('flow_cart', $flow_cart);
		}
	}

	function send_sync_sms($arg = array(), $send_type = NULL) {
		if (empty($send_type)) {
			return false;
		}

		if (empty($arg['mobile'])) {
			return false;
		}

		$search = $replace = array();
		$mobile = $arg['mobile'];
		switch ($send_type) {
		case 'register_validate': //发送验证码
			$authentication = $arg['authentication'];
			$search[] = '{$authentication}';
			$replace[] = $authentication;
			break;

		case 'send_password': //取回密码
			$password = $arg['password'];
			$search[] = '{$password}';
			$replace[] = $password;
			break;
		case 'register_user': //注册成功
			return false;
			$password = $arg['password'];
			$search[] = '{$password}';
			$replace[] = $password;
			break;
		default:
			return false;
			break;
		}
		$template = $this->CI->user_model->mail_template($send_type);
		$search[] = '{$user_name}';
		$search[] = '{$shop_name}';
		$search[] = '{$mobile}';
		$replace[] = isset($arg['user_name']) ? $arg['user_name'] : '';
		$replace[] = SHOP_NAME;
		$replace[] = $mobile;

		$content = str_replace($search, $replace, $template->sms_content);
		$content = str_replace("'", "\'", $content);

		$msgId = "mmt_phone_" . time();
		$insert_arr = array();
		$insert_arr['sms_from'] = 1;
		$insert_arr['sms_to'] = $mobile;
		$insert_arr['template_id'] = $template->template_id;
		$insert_arr['template_content'] = "[" . $msgId . "]" . $content;
		$insert_arr['sms_priority'] = 1;
		$insert_arr['create_date'] = date('Y-m-d H:i:s');
		$insert_arr['send_date'] = date('Y-m-d H:i:s');
                
                $url = ERP_HOST.'/api/do_sms';
                $pdata = array('msg' => $content, 'mob' => $mobile);
                $smscallback = curl_post($url, $pdata);
                if ($smscallback) {
			$insert_arr['status'] = 1;
			$msg = '';
		} else {
			$insert_arr['status'] = 2;
			//$msg = $smscallback->message;
                        $msg = 'error';
		}
		//$this->CI->load->library("mobile");
		//$smscallback = $this->CI->mobile->send($content, $mobile);
		/*if ($smscallback->returnstatus == 'Success') {
			$insert_arr['status'] = 1;
			$msg = '';
		} else {
			$insert_arr['status'] = 2;
			$msg = $smscallback->message;
		}*/

		$this->send_msg($insert_arr);
		return $msg;
	}

	public function send_msg($arg = array()) {
		$this->CI->user_model->insert_sms_log($arg);
	}

	/**
	 * 设置发送类型
	密码找回 send_password
	邮件验证 register_validate
	注册成功 register_user
	 * @arg (user_id 会员id,user_name 发送人id,shop_name 网店名,order_sn 订单号,password 密码,order_id 订单号,
	order_sn 订单编号,consignee 收货人,add_time 订单添加时间,invoice_no 发货单号,send_time 发货时间
	$confirm_url 链接地址,content 发送内容,send_email=>array())
	 */
	public function SendSyncMail($arg = array(), $send_type = NULL, &$err_msg = '') {
		if (empty($send_type)) {
			return FALSE;
		}

		if (empty($arg['to_email'])) {
			return FALSE;
		}

		$weekname = array('0' => '星期日', '1' => '星期一', '2' => '星期二', '3' => '星期三', '4' => '星期四', '5' => '星期五', '6' => '星期六');
		$send_date = date('Y年m月d日') . $weekname[date('w')];
		$search = $replace = array();
		$this->CI->load->model('user_model');
		switch ($send_type) {
		case 'send_password': //密码找回
			$email = $arg['email'];
			$password = $arg['password'];
			$search[] = '{$email}';
			$search[] = '{$password}';
			$replace[] = $email;
			$replace[] = $password;
			break;

		case 'register_validate': //邮件验证
			$email = $arg['email'];
			$confirm_url = $arg['confirm_url'];
			$send_points = $this->get_user_rank_point($arg['user_id'], 'regist_point');
			$send_cash = $send_points / 100;
			$search[] = '{$email}';
			$search[] = '{$confirm_url}';
			$search[] = '{$send_points}';
			$replace[] = $email;
			$replace[] = $confirm_url;
			$replace[] = $send_points;
			break;

		case 'register_user': //注册成功
			$email = $arg['email'];
			$password = $arg['password'];
			$confirm_url = $arg['confirm_url'];
			$send_points = $this->get_user_rank_point($arg['user_id'], 'regist_point');
			$send_detail_points = $this->get_user_rank_point($arg['user_id'], 'profile_point');
			$search[] = '{$email}';
			$search[] = '{$password}';
			$search[] = '{$confirm_url}';
			$search[] = '{$send_points}';
			$search[] = '{$send_detail_points}';
			$replace[] = $email;
			$replace[] = $password;
			$replace[] = $confirm_url;
			$replace[] = $send_points;
			$replace[] = $send_detail_points;
			break;

		}
		$main_template = $this->CI->user_model->mail_template('mail_frame');
		$main_content = $main_template->template_content;
		$template = $this->CI->user_model->mail_template($send_type);
		$search[] = '{$user_name}';
		$search[] = '{$shop_name}';
		$search[] = '{$send_time}';
		$replace[] = $arg['user_name'];
		$replace[] = '52kid';
		$replace[] = $send_date;

		//$head_template  = $this->CI->user_model->mail_template('mail_header');
		//$foot_template  = $this->CI->user_model->mail_template('mail_foot');
		//$content = str_replace($search, $replace, $head_template['template_content'].$template['template_content'].$foot_template['template_content']);
		$content = str_replace($search, $replace, $template->template_content);
		$title = str_replace($search, $replace, $template->template_subject);
		$search[] = '{$content}';
		$replace[] = $content;
		$content = str_replace($search, $replace, $main_content);
		$content = adjust_path($content);
		$content = str_replace("'", "\'", $content);

		if ($send_type == "register_user") {
			$this->CI->user_model->add_register_email(SEND_MAIL, $arg['to_email'], $title, $content);
			return true;
		}
		/* 发送确认重置密码的确认邮件 */
		if ($this->send_mail(array('to_email' => $arg['to_email'], 'subject' => $title, 'content' => $content))) {
			return true;
		} else {
			return false;
		}
	}

	public function send_mail($arg) {
		require_once dirname(__FILE__) . '/mail/class.phpmailer.php';
		$this->CI->load->config('email');
		$mail = new PHPMailer();
		$mail->Charset = 'UTF-8';
		$mail->IsSMTP();
		$mail->Host = $this->CI->config->item('smtp_host');
		$mail->SMTPAuth = true; // 启用SMTP验证功能
		$mail->Username = $this->CI->config->item('smtp_user');
		$mail->Password = $this->CI->config->item('smtp_pass');
		$mail->Port = $this->CI->config->item('smtp_port');
		$mail->From = $this->CI->config->item('smtp_from');
		$mail->FromName = "=?utf-8?B?" . base64_encode(SITE_NAME . '客服中心') . "?=";
		$mail->AddAddress("$arg[to_email]", $arg['to_email']); //收件人地址
		//$mail->AddReplyTo("", "");

		//$mail->AddAttachment("/var/tmp/file.tar.gz"); // 添加附件
		$mail->IsHTML(true); //是否使用HTML格式

		$mail->Subject = "=?utf-8?B?" . base64_encode($arg['subject']) . "?=";
		$mail->Body = $arg['content']; //邮件内容
		if (!$mail->Send()) {
			return false;
		}
		return true;
		/*
			    	$setting = array('from_email'=>SEND_MAIL,
										'from_name'=>SITE_NAME,
										'to_email'=>$arg['to_email'],
										'reply_to'=> $arg['to_email'],
										'subject'=>$arg['subject'],
										'content'=>$arg['content']);
					$this->CI->load->library('email');
					$this->CI->email->from($setting['from_email'], $setting['from_name']);
					$this->CI->email->to($setting['to_email']);
					$this->CI->email->reply_to($setting['reply_to']);
					$this->CI->email->subject($setting['subject']);
					$this->CI->email->message($setting['content']);
					$rs = $this->CI->email->send();
					//$err_msg = $this->CI->email->print_debugger();
					return $rs;
		*/
	}

	/**
	 * 按照会员和发送积分条件获得积分值
	 * @param   int     $user_id        用户id
	 * @param   string  $point_type    变动说明
	 */
	function get_user_rank_point($user_id, $point_type = '') {
		$this->CI->load->model('user_model');
		$rs = $this->CI->user_model->user_rank_point($user_id);
		if ($rs) {
			if (isset($rs->$point_type)) {
				return $rs->$point_type;
			} else {
				return $rs;
			}
		} else {
			return array();
		}
	}

	/**
	 * 获取当前地区
	 */
	public function get_current_region() {
		$CI = &get_instance();
		if (FALSE !== ($curr_region = $CI->input->cookie('curr_region'))) {
			return $curr_region;
		}
		$CI->load->model('region_model');
		$region_id = $CI->region_model->get_current_region(real_ip(), 890);
		$CI->input->set_cookie('curr_region', $region_id, 0, $CI->config->item('cookie_domain'));
		return $region_id;
	}

	/**
	 * 管理员登录， 如果成功记录相关session
	 * 该方法只用于手机号+验证码登陆
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return boolean
	 */
	public function loginWithoutPassword($mobile = '', $remember = 0) {
		$this->CI->load->model('user_model');
		if (is_mobile_number($mobile)) {
			$field = 'mobile';
		} else {
			return FALSE;
		}

		$user = $this->CI->user_model->filter(array(
			$field => $mobile,
		));

		if (!empty($user) && $user->is_use == 0) {
			// 保存会员ID，以此ID证明会员已登录
			$this->_user_id = (int) $user->user_id;
			if ($remember == 1) {
				$this->CI->session->sess_expiration = 0;
			}
			//会员等级计算
			if ($user->rank_id > 0) {
				$row = @$this->CI->user_model->filter_user_rank(array('rank_id' => $user->rank_id));
			} else {
				$row = @$this->CI->user_model->max_user_rank($user->paid_money);
			}
			if ($row) {
				$user->rank_name = $row->rank_name;
			} else {
				$user->rank_name = '新用户';
			}
			$this->CI->session->set_userdata('rank_name', $user->rank_name);
			$this->CI->session->set_userdata('user_id', $this->_user_id);
			$this->CI->session->set_userdata('user_name', $user->user_name);
			$this->CI->session->set_userdata('union_sina', $user->union_sina);
			$this->CI->session->set_userdata('union_zhifubao', $user->union_zhifubao);
			$this->CI->session->set_userdata('union_qq', $user->union_qq);
			$this->CI->session->set_userdata('union_fclub', $user->union_fclub);
			//$this->CI->session->set_userdata('email', $user->email);
			$this->CI->session->set_userdata('mobile', $user->mobile);
			$this->CI->session->set_userdata('user_type', $user->user_type);
			$this->CI->session->set_userdata('advar', $user->user_advar);
			$this->CI->session->set_userdata('mobile_checked', $user->mobile_checked);
			$this->CI->session->set_userdata('discount_percent', round(floatval($user->discount_percent), 2));
			unset($user);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function dealRegisterOrlogin($loginType = 0, $mobile = '', $password = '') {
		$error = 0;
		if ($loginType == 0 || $loginType == 2) {
			if ($this->is_mobile_register($mobile)) {
				$error = $this->loginWithoutPassword($mobile) ? 0 : 2;
			} else {
				$user_input = array(
					'user_name' => $mobile,
					'real_name' => $mobile,
					'mobile' => $mobile,
				);

				$error = $this->fast_register_without_password($user_input) ? 0 : 4;
			}
		} else {
			//loginType=1使用用户名和密码登陆
			$error = $this->login($mobile, $password) ? 0 : 8;
		}

		return $error;
	}

	public function fast_register_without_password($user_input) {
		$this->CI->load->model('user_model');

		$now = date('Y-m-d H:i:s');
		$user_input['password'] = '';
		$user_input['visit_count'] = 1;
		$user_input['create_date'] = $now;
		//$user_input['email_validated'] = 1;//默认通过用户验证
		$user_input['mobile_checked'] = 1;//默认通过用户验证

		$this->CI->db->trans_begin(); //transaction start. dadada...
		$user_id = $this->CI->user_model->insert($user_input);
		if (empty($user_id)) {
			$this->CI->db->trans_rollback();
			return FALSE;
		}

		$user = $this->CI->user_model->filter(array('user_id' => $user_id));
		if ($user_id > 0) {
			$this->_user_id = (int) $user->user_id;
			$this->CI->session->set_userdata(array(
				'user_id' => $this->_user_id,
				'user_name' => $user->user_name,
				'union_sina' => $user->union_sina,
				'union_zhifubao' => $user->union_zhifubao,
				'union_qq' => $user->union_qq,
				'union_fclub' => $user->union_fclub,
				'email' => $user->email,
				'mobile' => $user->mobile,
				'user_type' => $user->user_type,
				'email_validated' => $user->email_validated,
				'mobile_checked' => $user->mobile_checked,
				'discount_percent' => round(floatval($user->discount_percent), 2),
			));
			//发放现金券
			$this->CI->load->model('voucher_model');
			$this->CI->voucher_model->release_register_voucher($user->user_id);

			$this->CI->db->trans_commit();
			return TRUE;
		}
		$this->CI->db->trans_rollback();
		return FALSE;
	}

	/**
	 * 微信登录， 如果成功记录相关session
	 * @param string $nickname 用户名
	 * @param string $password 密码
	 * @return boolean
	 */
	public function wechat_login_user($user_wechat) {
		$this->CI->load->model('user_model');
		$this->CI->db->trans_begin(); //transaction start. dadada...

		$now = date('Y-m-d H:i:s');
		$user_input = array();
		$user_input['password'] = '';
		$user_input['user_name'] = $user_wechat['nickname'];
		$user_input['real_name'] = $user_wechat['nickname'];
		$user_input['wechat_openid'] = $user_wechat['openid'];
		$user_input['wechat_nickname'] = $user_wechat['nickname'];
		$user_input['wechat_headimgurl'] = $user_wechat['headimgurl'];
		$user_input['create_date'] = $now;
		$user_input['mobile_checked'] = 1;//默认通过用户验证

		$user = $this->CI->user_model->filter(array('wechat_openid' => $user_wechat['openid']));
		if(empty($user)){
			$user_id = $this->CI->user_model->insert($user_input);
			if (empty($user_id)) {
				$this->CI->db->trans_rollback();
				return FALSE;
			}
			$user = $this->CI->user_model->filter(array('user_id' => $user_id));
		}
		
		if ($user_id > 0) {
			$this->_user_id = (int) $user->user_id;
			$this->CI->session->set_userdata(array(
				'user_id' => $this->_user_id,
				'user_name' => $user->user_name,
				'union_sina' => $user->union_sina,
				'union_zhifubao' => $user->union_zhifubao,
				'union_qq' => $user->union_qq,
				'union_fclub' => $user->union_fclub,
				'email' => $user->email,
				'mobile' => $user->mobile,
				'user_type' => $user->user_type,
				'email_validated' => $user->email_validated,
				'mobile_checked' => $user->mobile_checked,
				'user_advar' => $user->user_advar,
				'discount_percent' => round(floatval($user->discount_percent), 2),
			));
			//发放现金券
			$this->CI->load->model('voucher_model');
			$this->CI->voucher_model->release_register_voucher($user->user_id);

			$this->CI->db->trans_commit();
			return TRUE;
		}
		$this->CI->db->trans_rollback();
		return FALSE;
	}

}
