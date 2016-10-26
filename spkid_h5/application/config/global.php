<?php

$config['best_times'] = array(
	'时间不限','只双休日、假期送货','只工作日送货'
);

$config['alipay'] = array(
	'partner' => '2088701919343818',
	'seller_email' => 'fa@oswell.com.cn',
	//'key'	 =>'wm4q0lbfr25prvg9gxp7uh8gejdxsxel',cfq9wn2fkobjc959qkxmpknnjp6mi9oy
    'key' => 'cfq9wn2fkobjc959qkxmpknnjp6mi9oy',
	'input_charset' => 'utf-8',
	'return_url' => FRONT_HOST . '/user/alipay_callback',
	'notify_url' =>'',
	'login_url' =>'',
	'sign_type' => 'MD5',
	'transport' =>'http'
);
$config['alipay_new'] = array(
    'partner' => '2088701919343818',
    'seller_email' => 'fa@oswell.com.cn',
    'seller_id' => '2088701919343818',    
    'key' => 'cfq9wn2fkobjc959qkxmpknnjp6mi9oy',
    'private_key_path' => 'alipay_pem/key/rsa_private_key.pem',
    'ali_public_key_path' => 'alipay_pem/key/alipay_public_key.pem',
    'input_charset' => 'utf-8',
    'return_url' =>'',
    'notify_url' =>'',
    'login_url' =>'',
    'sign_type' => 'RSA',
    'cacert' => 'alipay_pem/key/cacert.pem',
	'transport' =>'https',
	'show_url' => FRONT_HOST
);


$config['weixin'] = array('appid' => 'wxc25a0104ead394c1', 
                          'appsecret' => '8425d7fd75ba373b7bee6bfd758db3b5',
                          'site_url' => FRONT_HOST,
                          'callback_url' => FRONT_HOST . '/pay/wxpay_notify',
                          'notify' => FRONT_HOST . '/pay/wxpay_notify');

$config['qq'] = array(
	'0' => '2092351803',
	'1' => '1900185248',
);

$config['mobile'] = array(
	'accountId' => '10657109091403005',
	'password' => 'test123',
	'serviceId' => '10657109091403'
);

$config['bill'] = array(
	'merchantAcctId' => '',
	'bgUrl' => '',
	'gate'=>'',
	'privatekey' => '',
	'publickey' => '',
);
$config['register_voucher_campaign_ids']=array(6);

