<?php
function get_voucher_campaign_perm($campaign)
{
	$CI = &get_instance();
	$perms = array();
	$perms['edit'] = $campaign->campaign_status==0 && check_perm('voucher_campaign_edit');
	$perms['delete'] = $campaign->campaign_status==0 && check_perm('voucher_campaign_edit');
	$perms['audit'] = $campaign->campaign_status==0 && check_perm('voucher_campaign_audit');
	$perms['stop'] = $campaign->campaign_status==1 && check_perm('voucher_campaign_stop');
	$perms['release'] = $campaign->campaign_status==1 && check_perm('voucher_release_edit') && $campaign->start_date<=$CI->time && $campaign->end_date>=$CI->time;
	return $perms;
}

function get_voucher_release_perm($release)
{
	$perms = array();
	$perms['edit'] = $release->release_status==0 && check_perm('voucher_release_edit');
	$perms['delete'] = $release->release_status==0 && check_perm('voucher_release_edit');
	$perms['audit'] = $release->release_status==0 && check_perm('voucher_release_audit');
	$perms['back'] = $release->release_status==1 && check_perm('voucher_release_back');
	return $perms;
}

function get_voucher_release_rule($config)
{
	$CI = & get_instance();
	$result = array();
	$result['rule'] = trim($CI->input->post('rule'));
	if($config['rules']&&!in_array($result['rule'], $config['rules'])) sys_msg('请指定正确的发放规则');
	switch ($result['rule']) {
		case 'sn':
			$arr = explode(',',trim($CI->input->post('rule_sn')));
			foreach ($arr as $k=>$sn) {
				$sn = trim($sn);
				if(!$sn) unset($arr[$k]);
			}
			$arr = array_unique($arr);
			$result['rule_sn'] = implode(',', $arr);
			break;
		
		case 'list':
			$arr = explode(',',trim($CI->input->post('rule_list')));
			foreach ($arr as $k=>$id) {
				$id = intval($id);
				if($id<1) unset($arr[$k]);
			}
			$arr = array_unique($arr);
			$result['rule_list'] = implode(',', $arr);
			break;
		
		case 'number':
			$result['rule_number'] = intval($CI->input->post('rule_number'));
			if($result['rule_number']<1) sys_msg('请填写发放数量', 1);
			break;

		case 'rule':
			$result['rule_reg_date_min'] = trim($CI->input->post('rule_reg_date_min'));
			$result['rule_reg_date_max'] = trim($CI->input->post('rule_reg_date_max'));
			break;
		
		default:
			break;
	}

	return serialize($result);
}

