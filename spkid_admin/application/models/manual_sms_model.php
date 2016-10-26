<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class manual_sms_model extends CI_Model
{

	public function filter ($filter)
	{
		$query = $this->db->get_where('sms_log', $filter, 1);
		return $query->row();
	}

	public function insert ($data)
	{
		$this->db->insert('sms_log', $data);
		return $this->db->insert_id();
	}

    /**
     * 添加手机号
     *  未发送手机号不添加
     */
    public function addMobileNumber($number, $source= -2)
    {
        $mobileRecord = $this->getMobileRecords($number, 0);
        if( !empty($mobileRecord) ) return true;

		$arg = array();
		$arg['sms_from'] = 1;
		$arg['sms_to'] = $number;
		$arg['template_id'] = $source;
		$arg['template_content'] = '';
		$arg['create_admin'] = $this->admin_id;
		$arg['create_date'] = date('Y-m-d H:i:s');
		$arg['send_date'] = '0000-00-00 00:00:00';

		$this->insert($arg);
        return true;
    }
    /**
     * 得到某个手机号的记录
     */
    public function getMobileRecords($mobile, $status='all')
    {
        return $this->getRecords(-1,0,$mobile,$status);
    }

    /**
     * 标识为已经发送
     */
    function flagMobileSended($userId='')
    {
        if( empty($userId) ) $userId = $this->admin_id;
        $sql = "UPDATE ty_sms_log SET status=1 WHERE status = 0 AND create_admin = ".$userId;
        $this->db->query($sql);
    }

    /**
     * 得到此次的发送的手机号
     */
    function getCurrentBatchMobiles($userId='')
    {
        if( empty($userId) ) $userId = $this->admin_id;
    	$this->removeBlackMobiles();
        $sql = "SELECT distinct(sms_to) AS mobile FROM ty_sms_log".
            " WHERE create_admin=".$userId." AND status=0";
        $query = $this->db->query($sql);
        $result = $query->result_array();
        $mobiles = Array();
        foreach( $result AS $row )array_push( $mobiles, $row['mobile'] );
        return $mobiles;
    }

    /**
     * 得到所有的手机号
     */
    public function getRecords($page, $itemsPerPage, $mobile='',$status='all')
    {
    	$sql = "SELECT s.*, u.admin_name FROM ty_sms_log s LEFT JOIN ty_admin_info u ON u.admin_id = s.create_admin";
        $sql .= " where 1";
        $sql .= " and s.template_id IN ('-2','-3') ";
        if( !empty($mobile) )
            $sql .= " and s.sms_to like '%".$mobile."%'";
        if( $status !== 'all' )
            $sql .= " and s.status = ". $status;

        $sql .= " ORDER BY rec_id DESC";
        if( $page != -1 )
            $sql .= " limit ".$itemsPerPage." offset ".($page-1)*$itemsPerPage;

        $query = $this->db->query($sql);
        $result = $query->result_array();

        $records = Array();
        foreach( $result AS $row ){
            $row['source_name'] = ($row['template_id']=='-2')?'手工':(($row['template_id']=='-3')?'EXCEL':'N/A');
            $row['status_name'] = ($row['status']==0)?'未发送':'已发送';
            array_push( $records, $row );
        }
        return $records;
    }
    function getTotalRecords($mobile='',$status='all')
    {
        $sql = "SELECT count(*) as cow FROM ty_sms_log";
        $sql .= " where 1";
        $sql .= " and template_id IN ('-2','-3') ";
        if( !empty($mobile) )
            $sql .= " and sms_to like '%".$mobile."%'";
        if( $status != 'all' )
            $sql .= " and status = ". $status;

        $query = $this->db->query($sql);
		$list = $query->row();
        return $list->cow;
    }
    /**
     * 去掉黑名单手机号
     */
    public function removeBlackMobiles()
    {
        $sql = "DELETE FROM ty_sms_log WHERE `status`=0 AND EXISTS(SELECT mobile FROM ty_mobile_blacklist WHERE ty_sms_log.sms_to = mobile)";
        $this->db->query($sql);
    }
    /**
     * 删除一条记录
     */
    public function deleteRecord($referId)
    {
        $sql = "DELETE FROM ty_sms_log WHERE status=0 AND rec_id=".$referId;
        $this->db->query($sql);
    }

    /**
     * 过滤合理手机
     */
	public function filterAvailMobiles($mobiles){
	    $newMobiles = Array();
	    foreach( $mobiles AS $mobile )
	    {
	    	$mobile = trim($mobile);
	        if( preg_match('/\d{11}/', $mobile ) ) array_push( $newMobiles, $mobile );
	    }
	    return $newMobiles;
	}

	public function isMobile($mobile){
	    if( preg_match( '/\d{11}/', $mobile ) )
	        return true;
	    else return false;
	}

}




?>
