<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$config['payment'] = array(

    'alipay'=>array(
                    'pay_code'=>'alipay',
                    'pay_name'=>'支付宝/网上银行',
                    'pay_desc'=>'网上在线即时支付方式',
                    'pay_config' => array(
                        array('code'=>'account_id', 'name'=>'商户号', 'desc'=>''),
                        array('code'=>'account_pwd', 'name'=>'商户密码', 'desc'=>''),
                    ),
                ),
    'bank'=>array(
                    'pay_code'=>'bank',
                    'pay_name'=>'银行转帐',
                    'pay_desc'=>'银行名称
                                 收款人信息：全称 ××× ；帐号或地址 ××× ；开户行 ×××。
                                 注意事项：办理电汇时，请在电汇单“汇款用途”一栏处注明您的订单号。',
                    'pay_config' => array(
                        array('code'=>'account_id', 'name'=>'商户号', 'desc'=>''),
                        array('code'=>'bank_code', 'name'=>'银行账号', 'desc'=>''),
                    ),
                ),
    
    'balance'=>array(
                    'pay_code'=>'balance',
                    'pay_name'=>'余额支付',
                    'pay_desc'=>'使用帐户余额支付。只有会员才能使用，不可透支。',
                    'pay_config' => array(
                        array('code'=>'account_id', 'name'=>'商户号', 'desc'=>''),
                        array('code'=>'bank_code', 'name'=>'银行账号', 'desc'=>''),

                    ),
                )
);
?>
