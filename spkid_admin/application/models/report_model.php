<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class report_model extends CI_Model
{
	public function all_finance()
	{
		$sql = "SELECT * FROM ty_admin_action WHERE parent_id IN (SELECT action_id FROM ty_admin_action WHERE action_code = 'finance_report') ORDER BY sort_order ASC";
		$query = $this->db->query($sql);
		$list = $query->result();
		return $list;
	}

	public function all_depot()
	{
		$sql = "SELECT * FROM ty_admin_action WHERE parent_id IN (SELECT action_id FROM ty_admin_action WHERE action_code = 'depot_report') ORDER BY sort_order ASC";
		$query = $this->db->query($sql);
		$list = $query->result();
		return $list;
	}

	public function all_order()
	{
		$sql = "SELECT * FROM ty_admin_action WHERE parent_id IN (SELECT action_id FROM ty_admin_action WHERE action_code = 'order_report') ORDER BY sort_order ASC";
		$query = $this->db->query($sql);
		$list = $query->result();
		return $list;
	}

	/**
	 * 获得指定分类下的子分类的数组
	 *
	 * @access  public
	 * @param   int     $cat_id     分类的ID
	 * @param   int     $selected   当前选中分类的ID
	 * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
	 * @param   int     $level      限定返回的级数。为0时返回所有级数
	 * @param   int     $is_show_all 如果为true显示所有分类，如果为false隐藏不可见分类。
	 * @return  mix
	 */
	public function cat_list($cat_id = 0, $selected = 0, $re_type = true)
	{
		$sql = "SELECT c.category_id,c.category_name, c.parent_id, c.sort_order, COUNT(s.category_id) AS has_children ".
	                "FROM ty_product_category AS c ".
	                "LEFT JOIN ty_product_category AS s ON s.parent_id=c.category_id ".
	                "GROUP BY c.category_id ".
	                'ORDER BY c.parent_id, c.sort_order DESC ';
	    $query = $this->db->query($sql);
		$res = $query->result_array();

		$sql = "SELECT category_id, COUNT(*) AS product_num " .
	           " FROM ty_product_info AS g " .
	           " GROUP BY category_id";
	    $query = $this->db->query($sql);
		$res2 = $query->result_array();

		$newres = array();
		foreach($res2 as $k=>$v)
		{
			$newres[$v['category_id']] = $v['product_num'];
		}

		foreach($res as $k=>$v)
		{
			$res[$k]['product_num'] = !empty($newres[$v['category_id']]) ? $newres[$v['category_id']] : 0;
		}

	    if (empty($res) == true)
	    {
	        return $re_type ? '' : array();
	    }

	    $options = $this->cat_options($cat_id, $res); // 获得指定分类下的子分类的数组

	    if ($re_type == true)
	    {
	        $select = '';
	        foreach ($options AS $var)
	        {
	            $select .= '<option value="' . $var['category_id'] . '" ';
	            $select .= ($selected == $var['category_id']) ? "selected='ture'" : '';
	            $select .= '>';
	            if ($var['level'] > 0)
	            {
	                $select .= str_repeat('&nbsp;', $var['level'] * 4);
	            }
	            $select .= htmlspecialchars($var['category_name'], ENT_QUOTES) . '</option>';
	        }
	        return $select;
	    }
	    else
	    {
	        return $options;
	    }
	}

	/**
	 * 过滤和排序所有分类，返回一个带有缩进级别的数组
	 *
	 * @access  private
	 * @param   int     $cat_id     上级分类ID
	 * @param   array   $arr        含有所有分类的数组
	 * @param   int     $level      级别
	 * @return  void
	 */
	function cat_options($spec_cat_id, $arr)
	{
	        $level = $last_cat_id = 0;
	        $options = $cat_id_array = $level_array = array();
	            while (!empty($arr))
	            {
	                foreach ($arr AS $key => $value)
	                {
	                    $cat_id = $value['category_id'];
	                    if ($level == 0 && $last_cat_id == 0)
	                    {
	                        if ($value['parent_id'] > 0)
	                        {
	                            break;
	                        }

	                        $options[$cat_id]          = $value;
	                        $options[$cat_id]['level'] = $level;
	                        $options[$cat_id]['id']    = $cat_id;
	                        $options[$cat_id]['name']  = $value['category_name'];
	                        unset($arr[$key]);

	                        if ($value['has_children'] == 0)
	                        {
	                            continue;
	                        }
	                        $last_cat_id  = $cat_id;
	                        $cat_id_array = array($cat_id);
	                        $level_array[$last_cat_id] = ++$level;
	                        continue;
	                    }

	                    if ($value['parent_id'] == $last_cat_id)
	                    {
	                        $options[$cat_id]          = $value;
	                        $options[$cat_id]['level'] = $level;
	                        $options[$cat_id]['id']    = $cat_id;
	                        $options[$cat_id]['name']  = $value['category_name'];
	                        unset($arr[$key]);

	                        if ($value['has_children'] > 0)
	                        {
	                            if (end($cat_id_array) != $last_cat_id)
	                            {
	                                $cat_id_array[] = $last_cat_id;
	                            }
	                            $last_cat_id    = $cat_id;
	                            $cat_id_array[] = $cat_id;
	                            $level_array[$last_cat_id] = ++$level;
	                        }
	                    }
	                    elseif ($value['parent_id'] > $last_cat_id)
	                    {
	                        break;
	                    }
	                }

	                $count = count($cat_id_array);
	                if ($count > 1)
	                {
	                    $last_cat_id = array_pop($cat_id_array);
	                }
	                elseif ($count == 1)
	                {
	                    if ($last_cat_id != end($cat_id_array))
	                    {
	                        $last_cat_id = end($cat_id_array);
	                    }
	                    else
	                    {
	                        $level = 0;
	                        $last_cat_id = 0;
	                        $cat_id_array = array();
	                        continue;
	                    }
	                }

	                if ($last_cat_id && isset($level_array[$last_cat_id]))
	                {
	                    $level = $level_array[$last_cat_id];
	                }
	                else
	                {
	                    $level = 0;
	                }
	            }
	        $cat_options[0] = $options;

	    if (!$spec_cat_id)
	    {
	        return $options;
	    }
	    else
	    {
	        if (empty($options[$spec_cat_id]))
	        {
	            return array();
	        }

	        $spec_cat_id_level = $options[$spec_cat_id]['level'];

	        foreach ($options AS $key => $value)
	        {
	            if ($key != $spec_cat_id)
	            {
	                unset($options[$key]);
	            }
	            else
	            {
	                break;
	            }
	        }

	        $spec_cat_id_array = array();
	        foreach ($options AS $key => $value)
	        {
	            if (($spec_cat_id_level == $value['level'] && $value['category_id'] != $spec_cat_id) ||
	                ($spec_cat_id_level > $value['level']))
	            {
	                break;
	            }
	            else
	            {
	                $spec_cat_id_array[$key] = $value;
	            }
	        }
	        $cat_options[$spec_cat_id] = $spec_cat_id_array;

	        return $spec_cat_id_array;
	    }
	}
	/**
	* 供应商名称
	*/
	public function provider_list(){
		$sql="SELECT p.provider_id, p.provider_name from ty_product_provider AS p where 1 order by convert(p.provider_name using gbk) asc";
		$query = $this->db->query($sql);
		$res = $query->result();
		return $res;
	}

	public function brand_list()
	{
	    $sql = 'SELECT brand_id, brand_name, brand_initial FROM ty_product_brand ORDER BY brand_initial asc,brand_id asc';
	    $query = $this->db->query($sql);
		$res = $query->result();
	    return $res;
	}

	/**
	* 尺码
	*/
	public function size_list()
	{
	    $sql = 'SELECT size_id, size_name FROM ty_product_size ORDER BY sort_order';
	    $query = $this->db->query($sql);
		$res = $query->result();
	    return $res;
	}

	/**
	* 合作方式
	*
	*/
	public function coop_list()
	{
		$sql = 'SELECT cooperation_id, cooperation_name FROM ty_product_cooperation ORDER BY sort_order';
	    $query = $this->db->query($sql);
		$res = $query->result();
	    return $res;
	}

	/**
	* 仓库
	*
	*/
	public function depot_list($selected='')
	{
		$sql = 'SELECT depot_id,depot_name FROM ty_depot_info ORDER BY depot_type desc,depot_id asc';
	    $query = $this->db->query($sql);
		$res = $query->result();
	    return $res;
	}

	public function onsale_list(){
	    $arr_list = array('0'=>'下架','1'=>'上架');
    	return $arr_list;
	}

    public function finance_invoicing_de_report ($filter)
	{

		$category_id = isset($filter['category_id'])?$filter['category_id']:'0';
		$brand_id = isset($filter['brand_id'])?$filter['brand_id']:'0';
		$color_id = isset($filter['color_id'])?$filter['color_id']:'0';
		$size_id = isset($filter['size_id'])?$filter['size_id']:'0';
		$product_sn = isset($filter['product_sn'])?$filter['product_sn']:'';
                $product_id = isset($filter['product_id'])?$filter['product_id']:'';
		$keyword = isset($filter['keyword'])?$filter['keyword']:'';
		$cooperation_id = isset($filter['cooperation_id'])?$filter['cooperation_id']:'0';
		$depot_id = isset($filter['depot_id'])?$filter['depot_id']:'0';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';

		if(empty($starttime) || empty($endtime))
		{
			return array();
		}

		$timewhere = " AND ((tr.trans_type NOT IN ('3','4') AND tr.trans_status IN ('2','4') AND TO_DAYS(tr.update_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tr.update_date)<= TO_DAYS('" . $endtime . "')) OR (tr.trans_type IN ('3','4') AND tr.trans_status IN ('1','2','3','4') AND TO_DAYS(tr.finance_check_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tr.finance_check_date)<= TO_DAYS('" . $endtime . "')))";
		$timekey = "IF(tr.trans_type IN ('3','4'),tr.finance_check_date,tr.update_date) as checktime ";

		$whereadd = "";

		if($category_id != '0') $whereadd .= " AND (g.category_id = '" . $category_id . "' OR pc.category_id = '" . $category_id . "') ";
		if($brand_id != '0') $whereadd .= " AND g.brand_id  = '" . $brand_id . "' ";
		if($color_id != '0') $whereadd .= " AND tr.color_id  = '" . $color_id . "' ";
		if($size_id != '0') $whereadd .= " AND tr.size_id  = '" . $size_id . "' ";
		if(!empty($product_sn)) $whereadd .= " AND g.product_sn  LIKE '%" . $product_sn . "%' ";
                if(!empty($product_id)) $whereadd .= " AND g.product_id  = '" . $product_id . "' ";
		if(!empty($keyword)) $whereadd .= " AND g.product_name  LIKE '%" . $keyword . "%' ";
		if($cooperation_id != '0') $whereadd .= " AND g.cooperation_id  = '" . $cooperation_id . "' ";
		if($depot_id != '0') $whereadd .= " AND tr.depot_id  = '" . $depot_id . "' ";

		$mainsql = "SELECT DISTINCT tr.transaction_id,tr.product_id,tr.color_id,tr.size_id,tr.product_number,tr.trans_type,tr.trans_sn,tr.trans_direction,IF(g.consign_price > 0,g.consign_price,g.cost_price) as cost_price,g.category_id,g.product_sn,g.product_name,g.brand_id,g.provider_id,g.provider_productcode,g.goods_cess,b.brand_name,".
			"c.parent_id, pc.category_name AS pcatname,c.category_name AS catname,gc.color_name,gc.color_sn,gs.size_name,gs.size_sn," . $timekey .
				" FROM ty_transaction_info as tr " .
				" LEFT JOIN ty_product_info AS g ON g.product_id = tr.product_id" .
				" LEFT JOIN ty_product_brand AS b ON g.brand_id = b.brand_id" .
				" LEFT JOIN ty_product_category AS c ON g.category_id = c.category_id" .
				" LEFT JOIN ty_product_category AS pc ON c.parent_id = pc.category_id" .
				" LEFT JOIN ty_product_color AS gc ON tr.color_id = gc.color_id" .
				" LEFT JOIN ty_product_size AS gs ON tr.size_id = gs.size_id" .
				" WHERE 1 " . $timewhere . $whereadd . " ORDER BY g.category_id ASC,tr.product_id ASC ,tr.color_id ASC,tr.size_id ASC,checktime ASC";
		$query = $this->db->query($mainsql);
		$result = $query->result_array();
		//得到期初期末库存
		$sql = "SELECT tr.product_id,tr.color_id,tr.size_id,IF(g.consign_price > 0,g.consign_price,g.cost_price) as cost_price,g.goods_cess,SUM(IF((tr.trans_type NOT IN ('3','4') AND tr.trans_status IN ('2','4') AND TO_DAYS(tr.update_date)< TO_DAYS('" . $starttime . "')) " .
					"OR (tr.trans_type IN ('3','4') AND tr.trans_status IN ('1','2','3','4') AND tr.finance_check_date > 0 AND TO_DAYS(tr.finance_check_date)< TO_DAYS('" . $starttime . "')),tr.product_number,0)) AS before_product_number," .
					"SUM(IF((tr.trans_type NOT IN ('3','4') AND tr.trans_status IN ('2','4') AND TO_DAYS(tr.update_date)<= TO_DAYS('" . $endtime . "')) " .
					"OR (tr.trans_type IN ('3','4') AND tr.trans_status IN ('1','2','3','4') AND tr.finance_check_date > 0 AND TO_DAYS(tr.finance_check_date)<= TO_DAYS('" . $endtime . "')),tr.product_number,0)) AS after_product_number " .
                    "FROM ty_transaction_info as tr " .
					"LEFT JOIN ty_product_info as g ON g.product_id=tr.product_id " .
					" LEFT JOIN ty_product_category AS c ON g.category_id = c.category_id" .
					" LEFT JOIN ty_product_category AS pc ON c.parent_id = pc.category_id" .
					" WHERE 1 ";

			if($category_id != '0') $sql .= " AND (g.category_id = '" . $category_id . "' OR pc.category_id = '" . $category_id . "') ";
			if($brand_id != '0') $sql .= " AND g.brand_id  = '" . $brand_id . "' ";
			if($color_id != '0') $sql .= " AND tr.color_id  = '" . $color_id . "' ";
			if($size_id != '0') $sql .= " AND tr.size_id  = '" . $size_id . "' ";
			if(!empty($product_sn)) $sql .= " AND g.product_sn  LIKE '%" . $product_sn . "%' ";
			if(!empty($keyword)) $sql .= " AND g.product_name  LIKE '%" . $keyword . "%' ";
			if($cooperation_id != '0') $sql .= " AND g.cooperation_id  = '" . $cooperation_id . "' ";
			if($depot_id != '0') $sql .= " AND tr.depot_id  = '" . $depot_id . "' ";

		$sql .= " GROUP BY product_id,color_id,size_id";
		$query = $this->db->query($sql);
		$tmparrrs = $query->result_array();
		$beforeNum = array();
		$afterNum = array();
		$total_before_num = 0;
		$total_before_amount = 0;
		$total_after_num = 0;
		$total_after_amount = 0;
		$total_after_cess_amount = 0;
		foreach ($tmparrrs as $value)
		{
			$beforeNum[$value['product_id']][$value['color_id']][$value['size_id']] = $value['before_product_number'];
			$afterNum[$value['product_id']][$value['color_id']][$value['size_id']] = $value['after_product_number'];

			$total_after_num += $value['after_product_number'];
			$total_after_amount += number_format(($value['after_product_number']*$value['cost_price']), 2, '.', '');
			$total_before_num += $value['before_product_number'];
			$total_before_amount += number_format(($value['before_product_number']*$value['cost_price']), 2, '.', '');
			if($value['goods_cess']>0)
			{
				$total_after_cess_amount += number_format($value['after_product_number'] * $value['cost_price']/(1+$value['goods_cess']), 2, '.', '');
			}
			else
			{
				$total_after_cess_amount += number_format(($value['after_product_number']*$value['cost_price']), 2, '.', '');
			}
		}
		$total_after_amount = number_format($total_after_amount, 2, '.', '');
		$total_before_amount = number_format($total_before_amount, 2, '.', '');
		$countarr = array('total_before_num'=>$total_before_num, 'total_after_num'=>$total_after_num,
						'total_before_amount'=>$total_before_amount, 'total_after_amount'=>$total_after_amount,
						'total_after_cess_amount'=>$total_after_cess_amount
							);


		$sql = " SELECT depot_in_code,depot_in_type FROM ty_depot_in_main WHERE TO_DAYS(audit_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(audit_date)<= TO_DAYS('" . $endtime . "')";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$sn2type = array();
		foreach($tmparr as $value)
		{
			$sn2type[$value['depot_in_code']] = $value['depot_in_type'];
		}

		$sql = " SELECT depot_out_code,depot_out_type FROM ty_depot_out_main WHERE TO_DAYS(audit_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(audit_date)<= TO_DAYS('" . $endtime . "')";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		foreach($tmparr as $value)
		{
			$sn2type[$value['depot_out_code']] = $value['depot_out_type'];
		}

		$sql = "SELECT depot_type_id,depot_type_name FROM ty_depot_iotype WHERE 1";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$typescript = array();
		foreach($tmparr as $value)
		{
			$typescript[$value['depot_type_id']] = $value['depot_type_name'];
		}
		$mainscript = array('3'=>'销售订单','4'=>'退货单','5'=>'换货单','6'=>'调仓单');

		$cur_goods = 0;
		$cur_color = 0;
		$cur_size = 0;
		$rec_num_arr = array();
		$rec_color_arr = array();
		$goodslist = array();
		reset($result);
		foreach($result as $key => $value)
		{
				if($value['trans_type'] == TRANS_TYPE_DIRECT_IN or $value['trans_type'] == TRANS_TYPE_DIRECT_OUT)
				{
					$value['typesrcipt'] = $typescript[$sn2type[$value['trans_sn']]];
				}
				else
				{
					$value['typesrcipt'] = $mainscript[$value['trans_type']];
				}
				$value['afternum'] = $afterNum[$value['product_id']][$value['color_id']][$value['size_id']];
				$value['beforenum'] = $beforeNum[$value['product_id']][$value['color_id']][$value['size_id']];

				$value['beforecount'] = number_format(($value['beforenum'] * $value['cost_price']), 2, '.', '');
				$value['aftercount'] = number_format(($value['afternum'] * $value['cost_price']), 2, '.', '');
				if($value['goods_cess'] > 0)
				{
					$value['aftercesscount'] = number_format($value['afternum'] * $value['cost_price']/(1+$value['goods_cess']), 2, '.', '');
				}
				else
				{
					$value['aftercesscount'] = $value['aftercount'];
				}
				$value['productcount'] = number_format(($value['product_number'] * $value['cost_price']), 2, '.', '');
				if($cur_goods != $value['product_id'])
				{
					$value['productfirst'] =1;
					$value['kindfirst'] =1;
					$rec_num_arr[$value['product_id']] = 1;
					$rec_color_arr[$value['product_id']][$value['color_id']][$value['size_id']] = 1;
					$cur_goods = $value['product_id'];
					$cur_color = $value['color_id'];
					$cur_size = $value['size_id'];
				}
				else
				{
					if($cur_color != $value['color_id'] || $cur_size != $value['size_id'])
					{
						$value['kindfirst'] =1;
						$rec_color_arr[$value['product_id']][$value['color_id']][$value['size_id']] = 1;
						$cur_color = $value['color_id'];
						$cur_size = $value['size_id'];
					}
					else
					{
						$value['kindfirst'] =0;
						$rec_color_arr[$value['product_id']][$value['color_id']][$value['size_id']]++;
					}
					$value['productfirst'] =0;
					$rec_num_arr[$value['product_id']]++;
				}
				$goodslist[] = $value;
		}
		$num = array('0'=>0,'1'=>0); //0入库 1出库
		$amount = array('0'=>0,'1'=>0);
		for($i=0;$i<count($goodslist);$i++)
		{
			$goodslist[$i]['rec_count'] = $rec_num_arr[$goodslist[$i]['product_id']];
			$goodslist[$i]['rec_color_size_count'] = $rec_color_arr[$goodslist[$i]['product_id']][$goodslist[$i]['color_id']][$goodslist[$i]['size_id']];

			$num[$goodslist[$i]['trans_direction']] += $goodslist[$i]['product_number'];
			$amount[$goodslist[$i]['trans_direction']] += $goodslist[$i]['productcount'];
		} $amount[0] = number_format($amount[0], 2, '.', ''); $amount[1] = number_format($amount[1], 2, '.', ''); 
		$countarr['numarr'] = $num;
		$countarr['amountarr'] = $amount;
		return array('list'=>$goodslist,'count'=>$countarr);
	}

	public function finance_invoicing_su_report($filter)
	{
		$category_id = isset($filter['category_id'])?$filter['category_id']:'0';
		$brand_id = isset($filter['brand_id'])?$filter['brand_id']:'0';
		$cooperation_id = isset($filter['cooperation_id'])?$filter['cooperation_id']:'0';
		$depot_id = isset($filter['depot_id'])?$filter['depot_id']:'0';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';

		if(empty($starttime) || empty($endtime))
		{
			return array();
		}

		$timewhere = " AND ((tr.trans_type NOT IN ('3','4') AND tr.trans_status IN ('2','4') AND TO_DAYS(tr.update_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tr.update_date)<= TO_DAYS('" . $endtime . "')) OR (tr.trans_type IN ('3','4') AND tr.trans_status IN ('1','2','3','4') AND TO_DAYS(tr.finance_check_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tr.finance_check_date)<= TO_DAYS('" . $endtime . "')))";
		$whereadd = $whereadd2 = "";

		if($category_id != '0') $whereadd .= " AND (g.category_id = '" . $category_id . "' OR pc.category_id = '" . $category_id . "') ";
		if($brand_id != '0') $whereadd .= " AND g.brand_id  = '" . $brand_id . "' ";
		if($cooperation_id != '0') $whereadd .= " AND g.cooperation_id  = '" . $cooperation_id . "' ";
		if($depot_id != '0') $whereadd2 .= " AND tr.depot_id  = '" . $depot_id . "' ";

		$sql = "SELECT DISTINCT tr.transaction_id,tr.trans_status,tr.product_id,tr.color_id,tr.size_id,tr.product_number,tr.trans_type,tr.trans_sn,tr.trans_direction,IF(g.consign_price > 0,g.consign_price,g.cost_price) as cost_price,g.category_id,g.product_sn,g.product_name,g.brand_id,g.provider_id,g.provider_productcode,g.goods_cess,b.brand_name,".
			"c.parent_id, pc.category_name AS pcatname,c.category_name AS catname,gc.color_name,gc.color_sn,gs.size_name,gs.size_sn " .
				" FROM ty_transaction_info as tr " .
				" LEFT JOIN ty_product_info AS g ON g.product_id = tr.product_id" .
				" LEFT JOIN ty_product_brand AS b ON g.brand_id = b.brand_id" .
				" LEFT JOIN ty_product_category AS c ON g.category_id = c.category_id" .
				" LEFT JOIN ty_product_category AS pc ON c.parent_id = pc.category_id" .
				" LEFT JOIN ty_product_color AS gc ON tr.color_id = gc.color_id" .
				" LEFT JOIN ty_product_size AS gs ON tr.size_id = gs.size_id" .
				" WHERE 1 " . $timewhere . $whereadd . $whereadd2 . " ORDER BY g.category_id ASC,tr.product_id ASC ,tr.color_id ASC,tr.size_id ASC";
		$query = $this->db->query($sql);
		$result = $query->result_array();

		$sql = "SELECT product_id,color_id,size_id,SUM(IF((trans_type NOT IN ('3','4') AND trans_status IN ('2','4') AND TO_DAYS(update_date)< TO_DAYS('" . $starttime . "')) OR (trans_type IN ('3','4') AND trans_status IN ('1','2','3','4') AND finance_check_date > 0 AND TO_DAYS(finance_check_date)< TO_DAYS('" . $starttime . "')),product_number,0)) AS before_product_number," .
					"SUM(IF((trans_type NOT IN ('3','4') AND trans_status IN ('2','4') AND TO_DAYS(update_date)<= TO_DAYS('" . $endtime . "')) OR (trans_type IN ('3','4') AND trans_status IN ('1','2','3','4') AND finance_check_date > 0 AND TO_DAYS(finance_check_date)<= TO_DAYS('" . $endtime . "')),product_number,0)) AS after_product_number FROM ty_transaction_info WHERE 1 ";
		if($depot_id != '0') $sql .= " AND depot_id = '".$depot_id."'";

		$sql .= " GROUP BY product_id,color_id,size_id";
		$sql = "SELECT n.product_id,n.color_id,n.size_id,n.before_product_number,n.after_product_number,IF(g.consign_price > 0,g.consign_price,g.cost_price) as cost_price,g.category_id,g.product_sn,g.goods_cess," .
				" g.product_name,g.brand_id,g.provider_id,g.provider_productcode,b.brand_name,c.parent_id, pc.category_name AS pcatname,c.category_name AS catname,gc.color_name,gc.color_sn,gs.size_name,gs.size_sn" .
				" FROM (".$sql.") as n" .
				" LEFT JOIN ty_product_info AS g ON g.product_id = n.product_id" .
				" LEFT JOIN ty_product_brand AS b ON g.brand_id = b.brand_id" .
				" LEFT JOIN ty_product_category AS c ON g.category_id = c.category_id" .
				" LEFT JOIN ty_product_category AS pc ON c.parent_id = pc.category_id" .
				" LEFT JOIN ty_product_color AS gc ON n.color_id = gc.color_id" .
				" LEFT JOIN ty_product_size AS gs ON n.size_id = gs.size_id" .
				" WHERE 1 ".$whereadd." ORDER BY pc.category_id ASC,c.category_id ASC";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();

		$beforeNum = array();
		$afterNum = array();
		$account_arr = array();
		$catelist = array();
		reset($tmparr);
		foreach($tmparr as $value)
		{
			$value['before_product_count'] = $value['before_product_number'] * $value['cost_price'];
			$value['after_product_count'] = $value['after_product_number'] * $value['cost_price'];
			$value['before_product_count_format'] = number_format($value['before_product_count'], 2, '.', '');
			$value['after_product_count_format'] = number_format($value['after_product_count'], 2, '.', '');

			if($value['goods_cess'] > 0)
			{
				$value['after_product_cess_count'] = $value['after_product_number'] * $value['cost_price']/(1+$value['goods_cess']);
				$value['after_product_cess_count_format'] = number_format($value['after_product_cess_count'], 2, '.', '');
			}
			else
			{
				$value['after_product_cess_count'] = $value['after_product_count'];
				$value['after_product_cess_count_format'] = $value['after_product_count_format'];
			}
			//$beforeNum[$value['goods_id']][$value['color_id']][$value['size_id']] = $value['before_goods_number'];
			//$afterNum[$value['goods_id']][$value['color_id']][$value['size_id']] = $value['after_goods_number'];

			if(!isset($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]))
			{
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']] = $value;
			}
			else
			{
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['before_product_number'] += $value['before_product_number'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_number'] += $value['after_product_number'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['before_product_count'] += $value['before_product_count'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_count'] += $value['after_product_count'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['before_product_count_format'] = number_format($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['before_product_count'], 2, '.', '');
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_count_format'] = number_format($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_count'], 2, '.', '');

				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_cess_count'] += $value['after_product_cess_count'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_cess_count_format'] = number_format($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_cess_count'], 2, '.', '');
			}
		}

		$sql = " SELECT depot_in_code,depot_in_type FROM ty_depot_in_main WHERE TO_DAYS(audit_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(audit_date)<= TO_DAYS('" . $endtime . "')";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$sn2type = array();
		foreach($tmparr as $value)
		{
			$sn2type[$value['depot_in_code']] = $value['depot_in_type'];
		}

		$sql = " SELECT depot_out_code,depot_out_type FROM ty_depot_out_main WHERE TO_DAYS(audit_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(audit_date)<= TO_DAYS('" . $endtime . "')";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		foreach($tmparr as $value)
		{
			$sn2type[$value['depot_out_code']] = $value['depot_out_type'];
		}

		$sql = "SELECT depot_type_id,depot_type_name FROM ty_depot_iotype WHERE 1";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$typescript = array();
		foreach($tmparr as $value)
		{
			$typescript[$value['depot_type_id']] = $value['depot_type_name'];
		}
		$mainscript = array('3'=>'销售订单','4'=>'退货单','5'=>'换货单','6'=>'调仓单');

		reset($result);
		foreach($result as $key => $value)
		{
			if($value['trans_type'] == TRANS_TYPE_DIRECT_IN or $value['trans_type'] == TRANS_TYPE_DIRECT_OUT)
			{
				$value['typesrcipt'] = $typescript[$sn2type[$value['trans_sn']]];
			}
			else
			{
				$value['typesrcipt'] = $mainscript[$value['trans_type']];
			}
			$value['trans_direction_type'] = $value['trans_direction'] == 1 ? 'in' : 'out';
			if(!isset($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]))
			{
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['num'] = $value['product_number'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count'] = $value['product_number'] * $value['cost_price'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count_foramt'] = number_format($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count'], 2, '.', '');

			}
			else
			{
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['num'] += $value['product_number'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count'] += $value['product_number'] * $value['cost_price'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count_foramt'] = number_format($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count'], 2, '.', '');
			}
		}
		return $catelist;

	}

	public function finance_invoicing_su_report_count($list)
	{
		$return = array();
		reset($list);
		foreach($list as $item)
		{
				//分类统计
				if(empty($return[$item['parent_id'].'_after_num']))
					$return[$item['parent_id'].'_after_num'] = $item['after_product_number'];
				else
					$return[$item['parent_id'].'_after_num'] += $item['after_product_number'];

				if(empty($return[$item['parent_id'].'_after_amount']))
				{
					$return[$item['parent_id'].'_after_amount'] = $item['after_product_count'];
					$return[$item['parent_id'].'_after_cess_amount'] = $item['after_product_cess_count'];
				}
				else
				{
					$return[$item['parent_id'].'_after_amount'] += $item['after_product_count'];
					$return[$item['parent_id'].'_after_cess_amount'] += $item['after_product_cess_count'];
				}


				if(empty($return[$item['parent_id'].'_before_num']))
					$return[$item['parent_id'].'_before_num'] = $item['before_product_number'];
				else
					$return[$item['parent_id'].'_before_num'] += $item['before_product_number'];

				if(empty($return[$item['parent_id'].'_before_amount']))
					$return[$item['parent_id'].'_before_amount'] = $item['before_product_count'];
				else
					$return[$item['parent_id'].'_before_amount'] += $item['before_product_count'];

				$return[$item['parent_id'].'_after_amount_format'] = number_format($return[$item['parent_id'].'_after_amount'], 2, '.', '');
				$return[$item['parent_id'].'_before_amount_format'] = number_format($return[$item['parent_id'].'_before_amount'], 2, '.', '');
				$return[$item['parent_id'].'_after_cess_amount_format'] = number_format($return[$item['parent_id'].'_after_cess_amount'], 2, '.', '');
				//统合统计
				if(empty($return['total_after_num']))
					$return['total_after_num'] = $item['after_product_number'];
				else
					$return['total_after_num'] += $item['after_product_number'];

				if(empty($return['total_after_amount']))
				{
					$return['total_after_amount'] = $item['after_product_count'];
					$return['total_after_cess_amount'] = $item['after_product_cess_count'];
				}
				else
				{
					$return['total_after_amount'] += $item['after_product_count'];
					$return['total_after_cess_amount'] += $item['after_product_cess_count'];
				}
				if(empty($return['total_before_num']))
					$return['total_before_num'] = $item['before_product_number'];
				else
					$return['total_before_num'] += $item['before_product_number'];

				if(empty($return['total_before_amount']))
					$return['total_before_amount'] = $item['before_product_count'];
				else
					$return['total_before_amount'] += $item['before_product_count'];

				$return['total_before_amount_format'] = number_format($return['total_before_amount'], 2, '.', '');
				$return['total_after_amount_format'] = number_format($return['total_after_amount'], 2, '.', '');
				$return['total_after_cess_amount_format'] = number_format($return['total_after_cess_amount'], 2, '.', '');

			//分类统计
			if(isset($item['in']))
			{
				reset($item['in']);
				foreach($item['in'] as $key => $value)
				{
					if(!isset($return[$item['parent_id'].'_serial']['in'][$key]))
					{
						$return[$item['parent_id'].'_serial']['in'][$key] = $value;
					}
					else
					{
						$return[$item['parent_id'].'_serial']['in'][$key]['num'] += $value['num'];
						$return[$item['parent_id'].'_serial']['in'][$key]['count'] += $value['count'];
					}

					//$return[$item['parent_id'].'_serial']['in'][$key]['count_formats'] = number_format($return[$item['parent_id'].'_serial']['in'][$key]['count'], 2, '.', '');

					if(!isset($return['total_serial']['in'][$key]))
					{
						$return['total_serial']['in'][$key] = $value;
					}
					else
					{
						$return['total_serial']['in'][$key]['num'] += $value['num'];
						$return['total_serial']['in'][$key]['count'] += $value['count'];
					}
					//$return['total_serial']['in'][$key]['count_formats'] = number_format($return['total_serial']['in'][$key]['count'], 2, '.', '');
				}
			}
			if(isset($item['out']))
			{
				reset($item['out']);
				foreach($item['out'] as $key => $value)
				{
					if(!isset($return[$item['parent_id'].'_serial']['out'][$key]))
					{
						$return[$item['parent_id'].'_serial']['out'][$key] = $value;
					}
					else
					{
						$return[$item['parent_id'].'_serial']['out'][$key]['num'] += $value['num'];
						$return[$item['parent_id'].'_serial']['out'][$key]['count'] += $value['count'];
					}
					//$return[$item['parent_id'].'_serial']['out'][$key]['count_formats'] = number_format($return[$item['parent_id'].'_serial']['out'][$key]['count'], 2, '.', '');
					if(!isset($return['total_serial']['out'][$key]))
					{
						$return['total_serial']['out'][$key] = $value;
					}
					else
					{
						$return['total_serial']['out'][$key]['num'] += $value['num'];
						$return['total_serial']['out'][$key]['count'] += $value['count'];
					}
					//$return['total_serial']['out'][$key]['count_formats'] = number_format($return['total_serial']['out'][$key]['count'], 2, '.', '');
				}
			}

		}
		return $return;
	}

	public function invoicing_details_report ($filter)
	{

		$category_id = isset($filter['category_id'])?$filter['category_id']:'0';
		$brand_id = isset($filter['brand_id'])?$filter['brand_id']:'0';
		$color_id = isset($filter['color_id'])?$filter['color_id']:'0';
		$size_id = isset($filter['size_id'])?$filter['size_id']:'0';
		$product_sn = isset($filter['product_sn'])?$filter['product_sn']:'';
		$keyword = isset($filter['keyword'])?$filter['keyword']:'';
		$cooperation_id = isset($filter['cooperation_id'])?$filter['cooperation_id']:'0';
		$depot_id = isset($filter['depot_id'])?$filter['depot_id']:'0';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';

		if(empty($starttime) || empty($endtime))
		{
			return array();
		}

		$timewhere = " AND TO_DAYS(tr.update_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tr.update_date)<= TO_DAYS('" . $endtime . "') ";
		$timekey = "tr.update_date as checktime ";
		$whereadd = "";

		if($category_id != '0') $whereadd .= " AND (g.category_id = '" . $category_id . "' OR pc.category_id = '" . $category_id . "') ";
		if($brand_id != '0') $whereadd .= " AND g.brand_id  = '" . $brand_id . "' ";
		if($color_id != '0') $whereadd .= " AND tr.color_id  = '" . $color_id . "' ";
		if($size_id != '0') $whereadd .= " AND tr.size_id  = '" . $size_id . "' ";
		if(!empty($product_sn)) $whereadd .= " AND g.product_sn  LIKE '%" . $product_sn . "%' ";
		if(!empty($keyword)) $whereadd .= " AND g.product_name  LIKE '%" . $keyword . "%' ";
		if($cooperation_id != '0') $whereadd .= " AND g.cooperation_id  = '" . $cooperation_id . "' ";
		if($depot_id != '0') $whereadd .= " AND tr.depot_id  = '" . $depot_id . "' ";

		$mainsql = "SELECT DISTINCT tr.transaction_id,tr.product_id,tr.color_id,tr.size_id,tr.product_number,tr.trans_type,tr.trans_sn,tr.trans_direction,IF(g.consign_price > 0,g.consign_price,g.cost_price) as cost_price,g.category_id,g.product_sn,g.product_name,g.brand_id,g.provider_id,g.provider_productcode,g.goods_cess,b.brand_name,".
			"c.parent_id, pc.category_name AS pcatname,c.category_name AS catname,gc.color_name,gc.color_sn,gs.size_name,gs.size_sn," . $timekey .
				" FROM ty_transaction_info as tr " .
				" LEFT JOIN ty_product_info AS g ON g.product_id = tr.product_id" .
				" LEFT JOIN ty_product_brand AS b ON g.brand_id = b.brand_id" .
				" LEFT JOIN ty_product_category AS c ON g.category_id = c.category_id" .
				" LEFT JOIN ty_product_category AS pc ON c.parent_id = pc.category_id" .
				" LEFT JOIN ty_product_color AS gc ON tr.color_id = gc.color_id" .
				" LEFT JOIN ty_product_size AS gs ON tr.size_id = gs.size_id" .
				" WHERE 1 AND tr.trans_status IN ('2','4') " . $timewhere . $whereadd . " ORDER BY g.category_id ASC,tr.product_id ASC ,tr.color_id ASC,tr.size_id ASC,checktime ASC";
		$query = $this->db->query($mainsql);
		$result = $query->result_array();


		//得到期初期末库存

		$sql = "SELECT product_id,color_id,size_id,SUM(IF(TO_DAYS(update_date)< TO_DAYS('" . $starttime . "'),product_number,0)) AS before_product_number," .
					"SUM(IF(TO_DAYS(update_date)<= TO_DAYS('" . $endtime . "'),product_number,0)) AS after_product_number FROM ty_transaction_info WHERE trans_status IN ('2','4') ";
		if($depot_id != '0') $sql .= " AND depot_id = '".$depot_id."'";


		$sql .= " GROUP BY product_id,color_id,size_id";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$beforeNum = array();
		$afterNum = array();
		foreach($tmparr as $value)
		{
			$beforeNum[$value['product_id']][$value['color_id']][$value['size_id']] = $value['before_product_number'];
			$afterNum[$value['product_id']][$value['color_id']][$value['size_id']] = $value['after_product_number'];
		}


		$sql = " SELECT depot_in_code,depot_in_type FROM ty_depot_in_main WHERE TO_DAYS(audit_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(audit_date)<= TO_DAYS('" . $endtime . "')";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$sn2type = array();
		foreach($tmparr as $value)
		{
			$sn2type[$value['depot_in_code']] = $value['depot_in_type'];
		}

		$sql = " SELECT depot_out_code,depot_out_type FROM ty_depot_out_main WHERE TO_DAYS(audit_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(audit_date)<= TO_DAYS('" . $endtime . "')";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		foreach($tmparr as $value)
		{
			$sn2type[$value['depot_out_code']] = $value['depot_out_type'];
		}

		$sql = "SELECT depot_type_id,depot_type_name FROM ty_depot_iotype WHERE 1";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$typescript = array();
		foreach($tmparr as $value)
		{
			$typescript[$value['depot_type_id']] = $value['depot_type_name'];
		}
		$mainscript = array('3'=>'销售订单','4'=>'退货单','5'=>'换货单','6'=>'调仓单');

		$cur_goods = 0;
		$cur_color = 0;
		$cur_size = 0;
		$rec_num_arr = array();
		$rec_color_arr = array();
		$goodslist = array();
		reset($result);
		foreach($result as $key => $value)
		{
				if($value['trans_type'] == TRANS_TYPE_DIRECT_IN or $value['trans_type'] == TRANS_TYPE_DIRECT_OUT)
				{
					$value['typesrcipt'] = $typescript[$sn2type[$value['trans_sn']]];
				}
				else
				{
					$value['typesrcipt'] = $mainscript[$value['trans_type']];
				}
				$value['afternum'] = $afterNum[$value['product_id']][$value['color_id']][$value['size_id']];
				$value['beforenum'] = $beforeNum[$value['product_id']][$value['color_id']][$value['size_id']];

				$value['beforecount'] = number_format(($value['beforenum'] * $value['cost_price']), 2, '.', '');
				$value['aftercount'] = number_format(($value['afternum'] * $value['cost_price']), 2, '.', '');
				if($value['goods_cess'] > 0)
				{
					$value['aftercesscount'] = number_format($value['afternum'] * $value['cost_price']/(1+$value['goods_cess']), 2, '.', '');
				}
				else
				{
					$value['aftercesscount'] = $value['aftercount'];
				}
				$value['productcount'] = number_format(($value['product_number'] * $value['cost_price']), 2, '.', '');
				if($cur_goods != $value['product_id'])
				{
					$value['productfirst'] =1;
					$value['kindfirst'] =1;
					$rec_num_arr[$value['product_id']] = 1;
					$rec_color_arr[$value['product_id']][$value['color_id']][$value['size_id']] = 1;
					$cur_goods = $value['product_id'];
					$cur_color = $value['color_id'];
					$cur_size = $value['size_id'];
				}
				else
				{
					if($cur_color != $value['color_id'] || $cur_size != $value['size_id'])
					{
						$value['kindfirst'] =1;
						$rec_color_arr[$value['product_id']][$value['color_id']][$value['size_id']] = 1;
						$cur_color = $value['color_id'];
						$cur_size = $value['size_id'];
					}
					else
					{
						$value['kindfirst'] =0;
						$rec_color_arr[$value['product_id']][$value['color_id']][$value['size_id']]++;
					}
					$value['productfirst'] =0;
					$rec_num_arr[$value['product_id']]++;
				}
				$goodslist[] = $value;
		}
		for($i=0;$i<count($goodslist);$i++)
		{
			$goodslist[$i]['rec_count'] = $rec_num_arr[$goodslist[$i]['product_id']];
			$goodslist[$i]['rec_color_size_count'] = $rec_color_arr[$goodslist[$i]['product_id']][$goodslist[$i]['color_id']][$goodslist[$i]['size_id']];
		}

		return $goodslist;
	}

	public function invoicing_details_count($list)
	{
		if (empty($list)) return array();
		$total_before_num = 0;
		$total_before_amount = 0;
		$total_after_num = 0;
		$total_after_amount = 0;
		$num = array('0'=>0,'1'=>0); //0入库 1出库
		$amount = array('0'=>0,'1'=>0);

		foreach($list as $item)
		{
			if($item['kindfirst'] == '1')
			{
				$total_after_num += $item['afternum'];
				$total_after_amount += $item['aftercount'];
				$total_before_num += $item['beforenum'];
				$total_before_amount += $item['beforecount'];
				$total_after_cess_amount += $item['aftercesscount'];
			}

			$num[$item['trans_direction']] += $item['product_number'];
			$amount[$item['trans_direction']] += $item['productcount'];
		}

		$total_after_amount = number_format($total_after_amount, 2, '.', '');
		$total_before_amount = number_format($total_before_amount, 2, '.', '');
		$amount[0] = number_format($amount[0], 2, '.', '');
		$amount[1] = number_format($amount[1], 2, '.', '');
		return array('total_before_num'=>$total_before_num, 'total_after_num'=>$total_after_num,
						'total_before_amount'=>$total_before_amount, 'total_after_amount'=>$total_after_amount,'total_after_cess_amount'=>$total_after_cess_amount,
						'numarr'=>$num, 'amountarr'=>$amount);
	}

	public function invoicing_summary_report($filter)
	{
		$category_id = isset($filter['category_id'])?$filter['category_id']:'0';
		$brand_id = isset($filter['brand_id'])?$filter['brand_id']:'0';
		$cooperation_id = isset($filter['cooperation_id'])?$filter['cooperation_id']:'0';
		$depot_id = isset($filter['depot_id'])?$filter['depot_id']:'0';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';

		if(empty($starttime) || empty($endtime))
		{
			return array();
		}

		$timewhere = " AND TO_DAYS(tr.update_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tr.update_date)<= TO_DAYS('" . $endtime . "') ";
		$whereadd = $whereadd2 = "";

		if($category_id != '0') $whereadd .= " AND (g.category_id = '" . $category_id . "' OR pc.category_id = '" . $category_id . "') ";
		if($brand_id != '0') $whereadd .= " AND g.brand_id  = '" . $brand_id . "' ";
		if($cooperation_id != '0') $whereadd .= " AND g.cooperation_id  = '" . $cooperation_id . "' ";
		if($depot_id != '0') $whereadd2 .= " AND tr.depot_id  = '" . $depot_id . "' ";

		$sql = "SELECT DISTINCT tr.transaction_id,tr.trans_status,tr.product_id,tr.color_id,tr.size_id,tr.product_number,tr.trans_type,tr.trans_sn,tr.trans_direction,IF(g.consign_price > 0,g.consign_price,g.cost_price) as cost_price,g.category_id,g.product_sn,g.product_name,g.brand_id,g.provider_id,g.provider_productcode,g.goods_cess,b.brand_name,".
			"c.parent_id, pc.category_name AS pcatname,c.category_name AS catname,gc.color_name,gc.color_sn,gs.size_name,gs.size_sn " .
				" FROM ty_transaction_info as tr " .
				" LEFT JOIN ty_product_info AS g ON g.product_id = tr.product_id" .
				" LEFT JOIN ty_product_brand AS b ON g.brand_id = b.brand_id" .
				" LEFT JOIN ty_product_category AS c ON g.category_id = c.category_id" .
				" LEFT JOIN ty_product_category AS pc ON c.parent_id = pc.category_id" .
				" LEFT JOIN ty_product_color AS gc ON tr.color_id = gc.color_id" .
				" LEFT JOIN ty_product_size AS gs ON tr.size_id = gs.size_id" .
				" WHERE tr.trans_status IN ('2','4') " . $timewhere . $whereadd . $whereadd2 . " ORDER BY g.category_id ASC,tr.product_id ASC ,tr.color_id ASC,tr.size_id ASC";
		$query = $this->db->query($sql);
		$result = $query->result_array();

		$sql = "SELECT product_id,color_id,size_id,SUM(IF(TO_DAYS(update_date)< TO_DAYS('" . $starttime . "'),product_number,0)) AS before_product_number," .
					"SUM(IF(TO_DAYS(update_date)<= TO_DAYS('" . $endtime . "'),product_number,0)) AS after_product_number FROM ty_transaction_info WHERE trans_status IN ('2','4') ";
		if($depot_id != '0') $sql .= " AND depot_id = '".$depot_id."'";

		$sql .= " GROUP BY product_id,color_id,size_id";
		$sql = "SELECT n.product_id,n.color_id,n.size_id,n.before_product_number,n.after_product_number,IF(g.consign_price > 0,g.consign_price,g.cost_price) as cost_price,g.category_id,g.product_sn,g.goods_cess," .
				" g.product_name,g.brand_id,g.provider_id,g.provider_productcode,b.brand_name,c.parent_id, pc.category_name AS pcatname,c.category_name AS catname,gc.color_name,gc.color_sn,gs.size_name,gs.size_sn" .
				" FROM (".$sql.") as n" .
				" LEFT JOIN ty_product_info AS g ON g.product_id = n.product_id" .
				" LEFT JOIN ty_product_brand AS b ON g.brand_id = b.brand_id" .
				" LEFT JOIN ty_product_category AS c ON g.category_id = c.category_id" .
				" LEFT JOIN ty_product_category AS pc ON c.parent_id = pc.category_id" .
				" LEFT JOIN ty_product_color AS gc ON n.color_id = gc.color_id" .
				" LEFT JOIN ty_product_size AS gs ON n.size_id = gs.size_id" .
				" WHERE 1 ".$whereadd." ORDER BY pc.category_id ASC,c.category_id ASC";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();

		$beforeNum = array();
		$afterNum = array();
		$account_arr = array();
		$catelist = array();
		reset($tmparr);
		foreach($tmparr as $value)
		{
			$value['before_product_count'] = $value['before_product_number'] * $value['cost_price'];
			$value['after_product_count'] = $value['after_product_number'] * $value['cost_price'];
			$value['before_product_count_format'] = number_format($value['before_product_count'], 2, '.', '');
			$value['after_product_count_format'] = number_format($value['after_product_count'], 2, '.', '');

			if(!isset($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]))
			{
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']] = $value;
			}
			else
			{
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['before_product_number'] += $value['before_product_number'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_number'] += $value['after_product_number'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['before_product_count'] += $value['before_product_count'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_count'] += $value['after_product_count'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['before_product_count_format'] = number_format($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['before_product_count'], 2, '.', '');
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_count_format'] = number_format($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']]['after_product_count'], 2, '.', '');
			}
		}

		$sql = " SELECT depot_in_code,depot_in_type FROM ty_depot_in_main WHERE TO_DAYS(audit_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(audit_date)<= TO_DAYS('" . $endtime . "')";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$sn2type = array();
		foreach($tmparr as $value)
		{
			$sn2type[$value['depot_in_code']] = $value['depot_in_type'];
		}

		$sql = " SELECT depot_out_code,depot_out_type FROM ty_depot_out_main WHERE TO_DAYS(audit_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(audit_date)<= TO_DAYS('" . $endtime . "')";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		foreach($tmparr as $value)
		{
			$sn2type[$value['depot_out_code']] = $value['depot_out_type'];
		}

		$sql = "SELECT depot_type_id,depot_type_name FROM ty_depot_iotype WHERE 1";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$typescript = array();
		foreach($tmparr as $value)
		{
			$typescript[$value['depot_type_id']] = $value['depot_type_name'];
		}
		$mainscript = array('3'=>'销售订单','4'=>'退货单','5'=>'换货单','6'=>'调仓单');

		reset($result);
		foreach($result as $key => $value)
		{
			if($value['trans_type'] == TRANS_TYPE_DIRECT_IN or $value['trans_type'] == TRANS_TYPE_DIRECT_OUT)
			{
				$value['typesrcipt'] = $typescript[$sn2type[$value['trans_sn']]];
			}
			else
			{
				$value['typesrcipt'] = $mainscript[$value['trans_type']];
			}
			$value['trans_direction_type'] = $value['trans_direction'] == 1 ? 'in' : 'out';
			if(!isset($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]))
			{
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['num'] = $value['product_number'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count'] = $value['product_number'] * $value['cost_price'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count_foramt'] = number_format($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count'], 2, '.', '');

			}
			else
			{
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['num'] += $value['product_number'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count'] += $value['product_number'] * $value['cost_price'];
				$catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count_foramt'] = number_format($catelist[$value['parent_id']."_".$value['brand_id']."_".$value['category_id']][$value['trans_direction_type']][$value['typesrcipt']]['count'], 2, '.', '');
			}
		}
		return $catelist;

	}

	public function invoicing_summary_report_count($list)
	{
		$return = array();
		reset($list);
		foreach($list as $item)
		{
				//分类统计
				if(empty($return[$item['parent_id'].'_after_num']))
					$return[$item['parent_id'].'_after_num'] = $item['after_product_number'];
				else
					$return[$item['parent_id'].'_after_num'] += $item['after_product_number'];

				if(empty($return[$item['parent_id'].'_after_amount']))
				{
					$return[$item['parent_id'].'_after_amount'] = $item['after_product_count'];
				}
				else
				{
					$return[$item['parent_id'].'_after_amount'] += $item['after_product_count'];
				}

				if(empty($return[$item['parent_id'].'_before_num']))
					$return[$item['parent_id'].'_before_num'] = $item['before_product_number'];
				else
					$return[$item['parent_id'].'_before_num'] += $item['before_product_number'];

				if(empty($return[$item['parent_id'].'_before_amount']))
					$return[$item['parent_id'].'_before_amount'] = $item['before_product_count'];
				else
					$return[$item['parent_id'].'_before_amount'] += $item['before_product_count'];

				$return[$item['parent_id'].'_after_amount_format'] = number_format($return[$item['parent_id'].'_after_amount'], 2, '.', '');
				$return[$item['parent_id'].'_before_amount_format'] = number_format($return[$item['parent_id'].'_before_amount'], 2, '.', '');

				//统合统计
				if(empty($return['total_after_num']))
					$return['total_after_num'] = $item['after_product_number'];
				else
					$return['total_after_num'] += $item['after_product_number'];

				if(empty($return['total_after_amount']))
				{
					$return['total_after_amount'] = $item['after_product_count'];
				}
				else
				{
					$return['total_after_amount'] += $item['after_product_count'];
				}
				if(empty($return['total_before_num']))
					$return['total_before_num'] = $item['before_product_number'];
				else
					$return['total_before_num'] += $item['before_product_number'];

				if(empty($return['total_before_amount']))
					$return['total_before_amount'] = $item['before_product_count'];
				else
					$return['total_before_amount'] += $item['before_product_count'];


				$return['total_before_amount_format'] = number_format($return['total_before_amount'], 2, '.', '');
				$return['total_after_amount_format'] = number_format($return['total_after_amount'], 2, '.', '');

			//分类统计
			if(isset($item['in']))
			{
				reset($item['in']);
				foreach($item['in'] as $key => $value)
				{
					if(!isset($return[$item['parent_id'].'_serial']['in'][$key]))
					{
						$return[$item['parent_id'].'_serial']['in'][$key] = $value;
					}
					else
					{
						$return[$item['parent_id'].'_serial']['in'][$key]['num'] += $value['num'];
						$return[$item['parent_id'].'_serial']['in'][$key]['count'] += $value['count'];
					}

					$return[$item['parent_id'].'_serial']['in'][$key]['count_format'] = number_format($return[$item['parent_id'].'_serial']['in'][$key]['count'], 2, '.', '');

					if(!isset($return['total_serial']['in'][$key]))
					{
						$return['total_serial']['in'][$key] = $value;
					}
					else
					{
						$return['total_serial']['in'][$key]['num'] += $value['num'];
						$return['total_serial']['in'][$key]['count'] += $value['count'];
					}
					$return['total_serial']['in'][$key]['count_format'] = number_format($return['total_serial']['in'][$key]['count'], 2, '.', '');
				}
			}
			if(isset($item['out']))
			{
				reset($item['out']);
				foreach($item['out'] as $key => $value)
				{
					if(!isset($return[$item['parent_id'].'_serial']['out'][$key]))
					{
						$return[$item['parent_id'].'_serial']['out'][$key] = $value;
					}
					else
					{
						$return[$item['parent_id'].'_serial']['out'][$key]['num'] += $value['num'];
						$return[$item['parent_id'].'_serial']['out'][$key]['count'] += $value['count'];
					}
					$return[$item['parent_id'].'_serial']['out'][$key]['count_format'] = number_format($return[$item['parent_id'].'_serial']['out'][$key]['count'], 2, '.', '');
					if(!isset($return['total_serial']['out'][$key]))
					{
						$return['total_serial']['out'][$key] = $value;
					}
					else
					{
						$return['total_serial']['out'][$key]['num'] += $value['num'];
						$return['total_serial']['out'][$key]['count'] += $value['count'];
					}
					$return['total_serial']['out'][$key]['count_format'] = number_format($return['total_serial']['out'][$key]['count'], 2, '.', '');
				}
			}

		}
		return $return;

	}

	public function merge_gather_gross_report($filter)
	{
		$category_id = isset($filter['category_id'])?$filter['category_id']:'0';
		$brand_id = isset($filter['brand_id'])?$filter['brand_id']:'0';
		$cooperation_id = isset($filter['cooperation_id'])?$filter['cooperation_id']:'0';
		$provider_id = isset($filter['provider_id'])?$filter['provider_id']:'0';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';

		$result = array();
		//取出所有子分类
		$sql = "SELECT c.category_id, c.parent_id, c.category_name as cat_name, pc.category_name AS parent_name" .
				" FROM ty_product_category AS c" .
				" LEFT JOIN ty_product_category AS pc ON c.parent_id = pc.category_id" .
				" WHERE c.parent_id !=0";
		if ($category_id>0)
		{
			$sql .= " AND (c.category_id=$category_id OR c.parent_id = $category_id) ";
		}
		$query = $this->db->query($sql);
		$sub_cat_rs = $query->result_array();

		foreach ($sub_cat_rs as $val){
			//if($cat_id > 0 && ($val['cat_id'] == $cat_id || $val['parent_id'] == $cat_id ))
			//{
				$sub_cat_arr[$val['cat_id']] = $val;
				$sub_cat_arr[$val['cat_id']]['product_num']=0;
				$sub_cat_arr[$val['cat_id']]['merge_sale_amount']=0; //销售额
				$sub_cat_arr[$val['cat_id']]['merge_discount_amount']=0; //折扣额
				$sub_cat_arr[$val['cat_id']]['merge_netsale_amount']=0; //净销售额
				$sub_cat_arr[$val['cat_id']]['merge_norate_netsale_amount']=0; //无税销售额

				$sub_cat_arr[$val['cat_id']]['cost_amount']=0; //成本进价
				$sub_cat_arr[$val['cat_id']]['nofaxcost_amount']=0; //不含税进价
				$sub_cat_arr[$val['cat_id']]['gross_amount']=0; //销售毛利
				$sub_cat_arr[$val['cat_id']]['gross_percent']=0; //销售毛利率
				$sub_cat_arr[$val['cat_id']]['fax_percente']=0; //税率
				$sub_cat_arr[$val['cat_id']]['order_product_list']=array(); //明细
			//}

		}
		$sub_cat_arr_keys = array_keys($sub_cat_arr);

		$dbi->query('BEGIN');
                $dbi->query("select * from z_order_goods for update");
                $dbi->query("select * from z_return_goods for update");
                $dbi->query("call get_z_order_goods('$start_date','$end_date')");
                $dbi->query("call get_z_return_goods('$start_date','$end_date')");


		//处理订单

		$goods_rs_sql = "SELECT  og.order_id, og.order_sn, o.finance_check_time,g.goods_sn,g.provider_goods,
		(og.goods_price * og.goods_number) AS goods_amount,og.discount_amount,
		IF(g.coop_id = 2 and og.consign_rate > 0,og.goods_price * og.goods_number * og.consign_rate,GREATEST(og.cost_price,og.consign_price) * og.goods_number) as cost_amount,
		(og.fclub_price * og.goods_number) AS fclub_amount,
		og.provider_cess as goods_cess,
		og.goods_number,
		g.brand_id,g.cat_id,og.goods_id,g.coop_id,g.provider_id,
		c.cat_name,pc.cat_name AS parent_name,
		color.color_name,size.size_name FROM
		z_order_goods AS og
		LEFT JOIN ".$GLOBALS['ecs']->table('order_info')." AS o ON og.order_id = o.order_id
		LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON g.goods_id = og.goods_id
		LEFT JOIN ".$GLOBALS['ecs']->table('category')." AS c ON c.cat_id = g.cat_id
		LEFT JOIN ".$GLOBALS['ecs']->table('category')." AS pc ON pc.cat_id = c.parent_id
		LEFT JOIN ".$GLOBALS['ecs']->table('flc_color')." AS color ON color.color_id = og.color_id
		LEFT JOIN ".$GLOBALS['ecs']->table('flc_size')." AS size ON size.size_id = og.size_id
		ORDER BY og.order_id";

		$goods_rs = $dbi->query($goods_rs_sql);

		$order_arr = array();

		while ($goods = $goods_rs->fetch_assoc()){
			if ($brand_id>0 && $goods['brand_id']!=$brand_id) {
				continue;
			}
			if ($coop_id>0 && $goods['coop_id']!=$coop_id) {
				continue;
			}
			if ($provider_id>0 && $goods['provider_id']!=$provider_id) {
				continue;
			}
			if ($cat_id>0 && !in_array($goods['cat_id'],$sub_cat_arr_keys)) {
				continue;
			}

			$goods_number =	$goods['goods_number'];
			$sale_amount = $goods['fclub_amount'];
			$discount_amount = $goods['discount_amount'];
			$netsale_amount = $goods['fclub_amount'] - $goods['discount_amount'];
			$norate_netsale_amount = $netsale_amount /(1+0.17);
			$cost_amount = $goods['cost_amount'];
			$nofaxcost_amount = $goods['cost_amount']/(1+$goods['goods_cess']);
			$gross_amount = $norate_netsale_amount - $nofaxcost_amount;
			$gross_percent = $norate_netsale_amount!=0?$gross_amount/$norate_netsale_amount:0;

			$sub_cat_arr[$goods['cat_id']]['goods_number'] += $goods_number;
			$sub_cat_arr[$goods['cat_id']]['sale_amount'] += $sale_amount;
			$sub_cat_arr[$goods['cat_id']]['discount_amount'] += $discount_amount;
			$sub_cat_arr[$goods['cat_id']]['netsale_amount'] += $netsale_amount;
			$sub_cat_arr[$goods['cat_id']]['norate_netsale_amount'] += $norate_netsale_amount;

			$sub_cat_arr[$goods['cat_id']]['merge_goods_number'] += $goods_number;
			$sub_cat_arr[$goods['cat_id']]['merge_sale_amount'] += $sale_amount;
			$sub_cat_arr[$goods['cat_id']]['merge_discount_amount'] += $discount_amount;
			$sub_cat_arr[$goods['cat_id']]['merge_netsale_amount'] += $netsale_amount;
			$sub_cat_arr[$goods['cat_id']]['merge_norate_netsale_amount'] += $norate_netsale_amount;

			$sub_cat_arr[$goods['cat_id']]['cost_amount'] += $cost_amount;
			$sub_cat_arr[$goods['cat_id']]['nofaxcost_amount'] += $nofaxcost_amount;
			$sub_cat_arr[$goods['cat_id']]['gross_amount'] += $gross_amount;

			$sub_cat_arr[$goods['cat_id']]['order_goods_list'][]=array(
				'finance_check_time' => local_date('Y-m-d',$goods['finance_check_time']),
				'order_sn' => $goods['order_sn'],
				'cat_name' => $goods['cat_name'],
				'parent_name' => $goods['parent_name'],
				'goods_sn' => $goods['goods_sn'],
				'provider_goods' => $goods['provider_goods'],
				'color_name' => $goods['color_name'],
				'size_name' => $goods['size_name'],
				'goods_number' => $goods_number,
				'formated_sale_amount' => sprintf('%.2f',$sale_amount),
				'formated_discount_amount' => sprintf('%.2f',-$discount_amount),
				'formated_netsale_amount' => sprintf('%.2f',$netsale_amount),
				'formated_norate_netsale_amount' => sprintf('%.2f',$norate_netsale_amount),
				'outsale_goods_number' => 0,
				'formated_outsale_sale_amount' => 0,
				'formated_outsale_discount_amount' => 0,
				'formated_outsale_netsale_amount' => 0,
				'formated_outsale_norate_netsale_amount' => 0,
				'merge_goods_number' => $goods_number,
				'formated_merge_sale_amount' => sprintf('%.2f',$sale_amount),
				'formated_merge_discount_amount' => sprintf('%.2f',-$discount_amount),
				'formated_merge_netsale_amount' => sprintf('%.2f',$netsale_amount),
				'formated_merge_norate_netsale_amount' => sprintf('%.2f',$norate_netsale_amount),
				'formated_cost_amount' => sprintf('%.2f',$cost_amount),
				'formated_fax_percent' => sprintf('%.2f',$goods['goods_cess']),
				'formated_nofaxcost_amount' => sprintf('%.2f',$nofaxcost_amount),
				'formated_gross_amount' => sprintf('%.2f',$gross_amount),
				'formated_gross_percent' => sprintf('%.0f%%',$gross_percent*100)
			);
		}



		//处理退单
		$goods_rs_sql = "SELECT  o.return_id, o.return_sn, o.finance_check_time,g.goods_sn,g.provider_goods,
		(og.goods_price * og.goods_number) AS goods_amount,
		IF(g.coop_id = 2 and og.consign_rate > 0,og.goods_price * og.goods_number * og.consign_rate,GREATEST(og.cost_price,og.consign_price) * og.goods_number) as cost_amount,
		(og.fclub_price * og.goods_number) AS fclub_amount,og.discount_amount,
		og.provider_cess,
		og.goods_number,
		g.brand_id,g.coop_id,g.provider_id,
		c.cat_id,c.cat_name,pc.cat_name AS parent_name,
		color.color_name, size.size_name FROM
		z_return_goods AS og
		LEFT JOIN ".$GLOBALS['ecs']->table('flc_return_info')." AS o ON og.return_id = o.return_id
		LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON g.goods_id = og.goods_id
		LEFT JOIN ".$GLOBALS['ecs']->table('category')." AS c ON c.cat_id = g.cat_id
		LEFT JOIN ".$GLOBALS['ecs']->table('category')." AS pc ON pc.cat_id = c.parent_id
		LEFT JOIN ".$GLOBALS['ecs']->table('flc_color')." AS color ON color.color_id = og.color_id
		LEFT JOIN ".$GLOBALS['ecs']->table('flc_size')." AS size ON size.size_id = og.size_id
		ORDER BY og.return_id";
		$goods_rs = $dbi->query($goods_rs_sql);

		$return_arr = array();
		while ($goods = $goods_rs->fetch_assoc()){
			if($goods['goods_number']==0){
				continue;
			}
			if ($brand_id>0 && $goods['brand_id']!=$brand_id) {
				continue;
			}
			if ($coop_id>0 && $goods['coop_id']!=$coop_id) {
				continue;
			}
			if ($provider_id>0 && $goods['provider_id']!=$provider_id) {
				continue;
			}
			if ($cat_id>0 && !in_array($goods['cat_id'],$sub_cat_arr_keys)) {
				continue;
			}

			$goods_number =	$goods['goods_number'];
			$sale_amount = $goods['fclub_amount'];
			$discount_amount = $goods['discount_amount'];
			$netsale_amount = $goods['fclub_amount'] - $goods['discount_amount'];
			$norate_netsale_amount = $netsale_amount /(1+0.17);
			$cost_amount = $goods['cost_amount'];
			$nofaxcost_amount = $goods['cost_amount']/(1+$goods['provider_cess']);
			$gross_amount = $norate_netsale_amount - $nofaxcost_amount;
			$gross_percent = $norate_netsale_amount!=0?$gross_amount/$norate_netsale_amount*(-1):0;

			$sub_cat_arr[$goods['cat_id']]['goods_number'] -= $goods_number;
			$sub_cat_arr[$goods['cat_id']]['sale_amount'] -= $sale_amount;
			$sub_cat_arr[$goods['cat_id']]['discount_amount'] -= $discount_amount;
			$sub_cat_arr[$goods['cat_id']]['netsale_amount'] -= $netsale_amount;
			$sub_cat_arr[$goods['cat_id']]['norate_netsale_amount'] -= $norate_netsale_amount;

			$sub_cat_arr[$goods['cat_id']]['merge_goods_number'] -= $goods_number;
			$sub_cat_arr[$goods['cat_id']]['merge_sale_amount'] -= $sale_amount;
			$sub_cat_arr[$goods['cat_id']]['merge_discount_amount'] -= $discount_amount;
			$sub_cat_arr[$goods['cat_id']]['merge_netsale_amount'] -= $netsale_amount;
			$sub_cat_arr[$goods['cat_id']]['merge_norate_netsale_amount'] -= $norate_netsale_amount;

			$sub_cat_arr[$goods['cat_id']]['cost_amount'] -= $cost_amount;
			$sub_cat_arr[$goods['cat_id']]['nofaxcost_amount'] -= $nofaxcost_amount;
			$sub_cat_arr[$goods['cat_id']]['gross_amount'] -= $gross_amount;

			$sub_cat_arr[$goods['cat_id']]['order_goods_list'][]=array(
				'finance_check_time' => local_date('Y-m-d',$goods['finance_check_time']),
				'order_sn' => $goods['return_sn'],
				'cat_name' => $goods['cat_name'],
				'parent_name' => $goods['parent_name'],
				'goods_sn' => $goods['goods_sn'],
				'provider_goods' => $goods['provider_goods'],
				'color_name' => $goods['color_name'],
				'size_name' => $goods['size_name'],
				'goods_number' => -$goods_number,
				'formated_sale_amount' => sprintf('%.2f',-$sale_amount),
				'formated_discount_amount' => sprintf('%.2f',$discount_amount),
				'formated_netsale_amount' => sprintf('%.2f',-$netsale_amount),
				'formated_norate_netsale_amount' => sprintf('%.2f',-$norate_netsale_amount),
				'outsale_goods_number' => 0,
				'formated_outsale_sale_amount' => 0,
				'formated_outsale_discount_amount' => 0,
				'formated_outsale_netsale_amount' => 0,
				'formated_outsale_norate_netsale_amount' => 0,
				'merge_goods_number' => -$goods_number,
				'formated_merge_sale_amount' => sprintf('%.2f',-$sale_amount),
				'formated_merge_discount_amount' => sprintf('%.2f',$discount_amount),
				'formated_merge_netsale_amount' => sprintf('%.2f',-$netsale_amount),
				'formated_merge_norate_netsale_amount' => sprintf('%.2f',-$norate_netsale_amount),
				'formated_cost_amount' => sprintf('%.2f',-$cost_amount),
				'formated_fax_percent' => sprintf('%.2f',$goods['provider_cess']),
				'formated_nofaxcost_amount' => sprintf('%.2f',-$nofaxcost_amount),
				'formated_gross_amount' => sprintf('%.2f',-$gross_amount),
				'formated_gross_percent' => sprintf('%.0f%%',$gross_percent*100)
			);
		}

		//处理特卖
		$outsale = new Outside_Report_F();
		$arr_order=$outsale->GetAllOrderData($start_date,$end_date,0,$brand_id,$cat_id,0,$coop_id,$provider_id);		//得到所有订单数据

		foreach($arr_order as $order_key=>$order_value){

			if($order_value['is_return'] == 0)
			{
				$arr_order[$order_key]['rebate_price'] = $order_value['goods_price']-$order_value['outsale_price'];
				$arr_order[$order_key]['return_price'] = 0-($order_value['goods_price'] * $order_value['return_point']);
				$arr_order[$order_key]['rebate_return_price'] = $arr_order[$order_key]['rebate_price'] + $arr_order[$order_key]['return_price'];
				$arr_order[$order_key]['sale_price'] = $order_value['goods_price'] + $arr_order[$order_key]['return_price'];
				$arr_order[$order_key]['norate_cost_amount'] = $order_value['cost_amount'] / (1+$order_value['goods_cess']);
				//$arr_order[$order_key]['maoli_amount'] = $arr_order[$order_key]['sale_price'] - $arr_order[$order_key]['norate_cost_amount'];

				$goods_number =	$order_value['goods_number'];
				$sale_amount = $order_value['outsale_price'];
				$discount_amount = abs($arr_order[$order_key]['rebate_return_price']);
				$netsale_amount = $arr_order[$order_key]['sale_price'];
				$norate_netsale_amount = $netsale_amount /(1+0.17);
				$cost_amount = $order_value['cost_amount'];
				$nofaxcost_amount = $arr_order[$order_key]['norate_cost_amount'];
				$gross_amount = $norate_netsale_amount - $nofaxcost_amount;
				$gross_percent = $norate_netsale_amount!=0?$gross_amount/$norate_netsale_amount:0;

				$sub_cat_arr[$order_value['cat_id']]['outsale_goods_number'] += $goods_number;
				$sub_cat_arr[$order_value['cat_id']]['outsale_sale_amount'] += $sale_amount;
				$sub_cat_arr[$order_value['cat_id']]['outsale_discount_amount'] += $discount_amount;
				$sub_cat_arr[$order_value['cat_id']]['outsale_netsale_amount'] += $netsale_amount;
				$sub_cat_arr[$order_value['cat_id']]['outsale_norate_netsale_amount'] += $norate_netsale_amount;

				$sub_cat_arr[$order_value['cat_id']]['merge_goods_number'] += $goods_number;
				$sub_cat_arr[$order_value['cat_id']]['merge_sale_amount'] += $sale_amount;
				$sub_cat_arr[$order_value['cat_id']]['merge_discount_amount'] += $discount_amount;
				$sub_cat_arr[$order_value['cat_id']]['merge_netsale_amount'] += $netsale_amount;
				$sub_cat_arr[$order_value['cat_id']]['merge_norate_netsale_amount'] += $norate_netsale_amount;

				$sub_cat_arr[$order_value['cat_id']]['cost_amount'] += $cost_amount;
				$sub_cat_arr[$order_value['cat_id']]['nofaxcost_amount'] += $nofaxcost_amount;
				$sub_cat_arr[$order_value['cat_id']]['gross_amount'] += $gross_amount;

				$sub_cat_arr[$order_value['cat_id']]['order_goods_list'][]=array(
					'finance_check_time' => local_date('Y-m-d',$order_value['confirm_time']),
					'order_sn' => $order_value['order_sn'],
					'cat_name' => $order_value['catname'],
					'parent_name' => $order_value['pcatname'],
					'goods_sn' => $order_value['goods_sn'],
					'provider_goods' => $order_value['provider_goods'],
					'color_name' => $order_value['color_name'],
					'size_name' => $order_value['size_name'],
					'goods_number' => 0,
					'formated_sale_amount' => 0,
					'formated_discount_amount' => 0,
					'formated_netsale_amount' => 0,
					'formated_norate_netsale_amount' => 0,
					'outsale_goods_number' => $goods_number,
					'formated_outsale_sale_amount' => sprintf('%.2f',$sale_amount),
					'formated_outsale_discount_amount' => sprintf('%.2f',-$discount_amount),
					'formated_outsale_netsale_amount' => sprintf('%.2f',$netsale_amount),
					'formated_outsale_norate_netsale_amount' => sprintf('%.2f',$norate_netsale_amount),
					'merge_goods_number' => $goods_number,
					'formated_merge_sale_amount' => sprintf('%.2f',$sale_amount),
					'formated_merge_discount_amount' => sprintf('%.2f',-$discount_amount),
					'formated_merge_netsale_amount' => sprintf('%.2f',$netsale_amount),
					'formated_merge_norate_netsale_amount' => sprintf('%.2f',$norate_netsale_amount),
					'formated_cost_amount' => sprintf('%.2f',$cost_amount),
					'formated_fax_percent' => sprintf('%.2f',$order_value['goods_cess']),
					'formated_nofaxcost_amount' => sprintf('%.2f',$nofaxcost_amount),
					'formated_gross_amount' => sprintf('%.2f',$gross_amount),
					'formated_gross_percent' => sprintf('%.0f%%',$gross_percent*100)
				);
			}
			elseif($order_value['is_return'] == 1)
			{
				$arr_order[$order_key]['rebate_price'] = $order_value['outsale_price']-$order_value['goods_price'];
				$arr_order[$order_key]['return_price'] = $order_value['goods_price'] * $order_value['return_point'];
				$arr_order[$order_key]['rebate_return_price'] = $arr_order[$order_key]['rebate_price'] + $arr_order[$order_key]['return_price'];
				//$arr_order[$order_key]['goods_price'] = 0-$order_value['goods_price'];
				//$arr_order[$order_key]['outsale_price'] = 0-$order_value['outsale_price'];
				$arr_order[$order_key]['sale_price'] = $arr_order[$order_key]['goods_price'] - $arr_order[$order_key]['return_price'];
				//$arr_order[$order_key]['cost_amount'] = 0-$order_value['cost_amount'];
				$arr_order[$order_key]['norate_cost_amount'] = $arr_order[$order_key]['cost_amount'] / (1+$order_value['goods_cess']);
				//$arr_order[$order_key]['maoli_amount'] = $arr_order[$order_key]['sale_price'] - $arr_order[$order_key]['norate_cost_amount'];

				$goods_number =	$order_value['goods_number'];
				$sale_amount = $order_value['outsale_price'];
				$discount_amount = abs($arr_order[$order_key]['rebate_return_price']);
				$netsale_amount = $arr_order[$order_key]['sale_price'];
				$norate_netsale_amount = $netsale_amount /(1+0.17);
				$cost_amount = $order_value['cost_amount'];
				$nofaxcost_amount = $arr_order[$order_key]['norate_cost_amount'];
				$gross_amount = $norate_netsale_amount - $nofaxcost_amount;
				$gross_percent = $norate_netsale_amount!=0?$gross_amount/$norate_netsale_amount*(-1):0;

				$sub_cat_arr[$order_value['cat_id']]['outsale_goods_number'] -= $goods_number;
				$sub_cat_arr[$order_value['cat_id']]['outsale_sale_amount'] -= $sale_amount;
				$sub_cat_arr[$order_value['cat_id']]['outsale_discount_amount'] -= $discount_amount;
				$sub_cat_arr[$order_value['cat_id']]['outsale_netsale_amount'] -= $netsale_amount;
				$sub_cat_arr[$order_value['cat_id']]['outsale_norate_netsale_amount'] -= $norate_netsale_amount;

				$sub_cat_arr[$order_value['cat_id']]['merge_goods_number'] -= $goods_number;
				$sub_cat_arr[$order_value['cat_id']]['merge_sale_amount'] -= $sale_amount;
				$sub_cat_arr[$order_value['cat_id']]['merge_discount_amount'] -= $discount_amount;
				$sub_cat_arr[$order_value['cat_id']]['merge_netsale_amount'] -= $netsale_amount;
				$sub_cat_arr[$order_value['cat_id']]['merge_norate_netsale_amount'] -= $norate_netsale_amount;

				$sub_cat_arr[$order_value['cat_id']]['cost_amount'] -= $cost_amount;
				$sub_cat_arr[$order_value['cat_id']]['nofaxcost_amount'] -= $nofaxcost_amount;
				$sub_cat_arr[$order_value['cat_id']]['gross_amount'] -= $gross_amount;

				$sub_cat_arr[$order_value['cat_id']]['order_goods_list'][]=array(
					'finance_check_time' => local_date('Y-m-d',$order_value['confirm_time']),
					'order_sn' => $order_value['order_sn'],
					'cat_name' => $order_value['catname'],
					'parent_name' => $order_value['pcatname'],
					'goods_sn' => $order_value['goods_sn'],
					'provider_goods' => $order_value['provider_goods'],
					'color_name' => $order_value['color_name'],
					'size_name' => $order_value['size_name'],
					'goods_number' => 0,
					'formated_sale_amount' => 0,
					'formated_discount_amount' => 0,
					'formated_netsale_amount' => 0,
					'formated_norate_netsale_amount' => 0,
					'outsale_goods_number' => -$goods_number,
					'formated_outsale_sale_amount' => sprintf('%.2f',-$sale_amount),
					'formated_outsale_discount_amount' => sprintf('%.2f',$discount_amount),
					'formated_outsale_netsale_amount' => sprintf('%.2f',-$netsale_amount),
					'formated_outsale_norate_netsale_amount' => sprintf('%.2f',-$norate_netsale_amount),
					'merge_goods_number' => -$goods_number,
					'formated_merge_sale_amount' => sprintf('%.2f',-$sale_amount),
					'formated_merge_discount_amount' => sprintf('%.2f',$discount_amount),
					'formated_merge_netsale_amount' => sprintf('%.2f',-$netsale_amount),
					'formated_merge_norate_netsale_amount' => sprintf('%.2f',-$norate_netsale_amount),
					'formated_cost_amount' => sprintf('%.2f',-$cost_amount),
					'formated_fax_percent' => sprintf('%.2f',$order_value['goods_cess']),
					'formated_nofaxcost_amount' => sprintf('%.2f',-$nofaxcost_amount),
					'formated_gross_amount' => sprintf('%.2f',-$gross_amount),
					'formated_gross_percent' => sprintf('%.0f%%',$gross_percent*100)
				);
			}
		}
		$del_arr = array();

		foreach ($sub_cat_arr as $key=>$sub_cat){

			if(!isset($sub_cat['order_goods_list'][0]['order_sn']) || empty($sub_cat['order_goods_list'][0]['order_sn']))
			{
				$del_arr[] = $key;
			}
		}

		foreach($del_arr as $item)
		{
			unset($sub_cat_arr[$item]);
		}

		//处理数据
		foreach ($sub_cat_arr as $key=>$sub_cat){
			if (!isset($result[$sub_cat['parent_id']])) {
				$result[$sub_cat['parent_id']] = array(
					'goods_number' => 0,
					'sale_amount' => 0,
					'discount_amount' => 0,
					'netsale_amount' => 0,
					'norate_netsale_amount' => 0,
					'outsale_goods_number' => 0,
					'outsale_sale_amount' => 0,
					'outsale_discount_amount' => 0,
					'outsale_netsale_amount' => 0,
					'outsale_norate_netsale_amount' => 0,
					'merge_goods_number' => 0,
					'merge_sale_amount' => 0,
					'merge_discount_amount' => 0,
					'merge_netsale_amount' => 0,
					'merge_norate_netsale_amount' => 0,
					'cost_amount'=> 0,
					'nofaxcost_amount' => 0,
					'gross_amount' => 0,
					'gross_percent' => 0,
					'sub_cat_list' => array(),
					'cat_name' => $sub_cat['parent_name'],
					'cat_id' => $sub_cat['parent_id']
				);
			}
			$sub_cat_arr[$key]['order_goods_count'] = count($sub_cat['order_goods_list']);
			if ($sub_cat['merge_norate_netsale_amount']!=0) {
				$sub_cat_arr[$key]['gross_percent'] = $sub_cat['gross_amount']/$sub_cat['merge_norate_netsale_amount'];
			}
			if ($sub_cat['nofaxcost_amount']!=0) {
				$sub_cat_arr[$key]['fax_percent'] = $sub_cat['cost_amount']/$sub_cat['nofaxcost_amount'] - 1;
			}
			$sub_cat_arr[$key]['formated_sale_amount'] = sprintf('%.2f',$sub_cat['sale_amount']);
			$sub_cat_arr[$key]['formated_discount_amount'] = sprintf('%.2f',-$sub_cat['discount_amount']);
			$sub_cat_arr[$key]['formated_netsale_amount'] = sprintf('%.2f',$sub_cat['netsale_amount']);
			$sub_cat_arr[$key]['formated_norate_netsale_amount'] = sprintf('%.2f',$sub_cat['norate_netsale_amount']);

			$sub_cat_arr[$key]['formated_outsale_sale_amount'] = sprintf('%.2f',$sub_cat['outsale_sale_amount']);
			$sub_cat_arr[$key]['formated_outsale_discount_amount'] = sprintf('%.2f',-$sub_cat['outsale_discount_amount']);
			$sub_cat_arr[$key]['formated_outsale_netsale_amount'] = sprintf('%.2f',$sub_cat['outsale_netsale_amount']);
			$sub_cat_arr[$key]['formated_outsale_norate_netsale_amount'] = sprintf('%.2f',$sub_cat['outsale_norate_netsale_amount']);

			$sub_cat_arr[$key]['formated_merge_sale_amount'] = sprintf('%.2f',$sub_cat['merge_sale_amount']);
			$sub_cat_arr[$key]['formated_merge_discount_amount'] = sprintf('%.2f',-$sub_cat['merge_discount_amount']);
			$sub_cat_arr[$key]['formated_merge_netsale_amount'] = sprintf('%.2f',$sub_cat['merge_netsale_amount']);
			$sub_cat_arr[$key]['formated_merge_norate_netsale_amount'] = sprintf('%.2f',$sub_cat['merge_norate_netsale_amount']);

			$sub_cat_arr[$key]['formated_cost_amount'] = sprintf('%.2f',$sub_cat['cost_amount']);
			$sub_cat_arr[$key]['formated_nofaxcost_amount'] = sprintf('%.2f',$sub_cat['nofaxcost_amount']);
			$sub_cat_arr[$key]['formated_gross_amount'] = sprintf('%.2f',$sub_cat['gross_amount']);
			$sub_cat_arr[$key]['formated_gross_percent'] = sprintf('%.0f%%',$sub_cat_arr[$key]['gross_percent']*100);
			$sub_cat_arr[$key]['formated_fax_percent'] = sprintf('%.2f',$sub_cat_arr[$key]['fax_percent']);

			$result[$sub_cat['parent_id']]['goods_number'] += $sub_cat['goods_number'];
			$result[$sub_cat['parent_id']]['sale_amount'] += $sub_cat['sale_amount'];
			$result[$sub_cat['parent_id']]['discount_amount'] += $sub_cat['discount_amount'];
			$result[$sub_cat['parent_id']]['netsale_amount'] += $sub_cat['netsale_amount'];
			$result[$sub_cat['parent_id']]['norate_netsale_amount'] += $sub_cat['norate_netsale_amount'];

			$result[$sub_cat['parent_id']]['outsale_goods_number'] += $sub_cat['outsale_goods_number'];
			$result[$sub_cat['parent_id']]['outsale_sale_amount'] += $sub_cat['outsale_sale_amount'];
			$result[$sub_cat['parent_id']]['outsale_discount_amount'] += $sub_cat['outsale_discount_amount'];
			$result[$sub_cat['parent_id']]['outsale_netsale_amount'] += $sub_cat['outsale_netsale_amount'];
			$result[$sub_cat['parent_id']]['outsale_norate_netsale_amount'] += $sub_cat['outsale_norate_netsale_amount'];

			$result[$sub_cat['parent_id']]['merge_goods_number'] += $sub_cat['merge_goods_number'];
			$result[$sub_cat['parent_id']]['merge_sale_amount'] += $sub_cat['merge_sale_amount'];
			$result[$sub_cat['parent_id']]['merge_discount_amount'] += $sub_cat['merge_discount_amount'];
			$result[$sub_cat['parent_id']]['merge_netsale_amount'] += $sub_cat['merge_netsale_amount'];
			$result[$sub_cat['parent_id']]['merge_norate_netsale_amount'] += $sub_cat['merge_norate_netsale_amount'];

			$result[$sub_cat['parent_id']]['cost_amount'] += $sub_cat['cost_amount'];
			$result[$sub_cat['parent_id']]['nofaxcost_amount'] += $sub_cat['nofaxcost_amount'];
			$result[$sub_cat['parent_id']]['gross_amount'] += $sub_cat['gross_amount'];
			$result[$sub_cat['parent_id']]['sub_cat_list'][] = $sub_cat_arr[$key];
		}
		$total = array();
		foreach ($result as $key=>$val){
			if ($val['merge_norate_netsale_amount']!=0) {
				$result[$key]['gross_percent'] = $val['gross_amount']/$val['merge_norate_netsale_amount'];
			}
			if ($val['nofaxcost_amount']!=0) {
				$result[$key]['fax_percent'] = $val['cost_amount']/$val['nofaxcost_amount'] - 1;
			}

			$result[$key]['formated_sale_amount'] = sprintf('%.2f',$val['sale_amount']);
			$result[$key]['formated_discount_amount'] = sprintf('%.2f',-$val['discount_amount']);
			$result[$key]['formated_netsale_amount'] = sprintf('%.2f',$val['netsale_amount']);
			$result[$key]['formated_norate_netsale_amount'] = sprintf('%.2f',$val['norate_netsale_amount']);

			$result[$key]['formated_outsale_sale_amount'] = sprintf('%.2f',$val['outsale_sale_amount']);
			$result[$key]['formated_outsale_discount_amount'] = sprintf('%.2f',-$val['outsale_discount_amount']);
			$result[$key]['formated_outsale_netsale_amount'] = sprintf('%.2f',$val['outsale_netsale_amount']);
			$result[$key]['formated_outsale_norate_netsale_amount'] = sprintf('%.2f',$val['outsale_norate_netsale_amount']);

			$result[$key]['formated_merge_sale_amount'] = sprintf('%.2f',$val['merge_sale_amount']);
			$result[$key]['formated_merge_discount_amount'] = sprintf('%.2f',-$val['merge_discount_amount']);
			$result[$key]['formated_merge_netsale_amount'] = sprintf('%.2f',$val['merge_netsale_amount']);
			$result[$key]['formated_merge_norate_netsale_amount'] = sprintf('%.2f',$val['merge_norate_netsale_amount']);

			$result[$key]['formated_cost_amount'] = sprintf('%.2f',$val['cost_amount']);
			$result[$key]['formated_nofaxcost_amount'] = sprintf('%.2f',$val['nofaxcost_amount']);
			$result[$key]['formated_gross_amount'] = sprintf('%.2f',$val['gross_amount']);
			$result[$key]['formated_gross_percent'] = sprintf('%.0f%%',$result[$key]['gross_percent']*100);
			$result[$key]['formated_fax_percent'] = sprintf('%.2f',$result[$key]['fax_percent']);

			$result[$key]['sub_cat_count'] = count($val['sub_cat_list']);
			$total['goods_number'] += $val['goods_number'];
			$total['sale_amount'] += $val['sale_amount'];
			$total['discount_amount'] += $val['discount_amount'];
			$total['netsale_amount'] += $val['netsale_amount'];
			$total['norate_netsale_amount'] += $val['norate_netsale_amount'];

			$total['outsale_goods_number'] += $val['outsale_goods_number'];
			$total['outsale_sale_amount'] += $val['outsale_sale_amount'];
			$total['outsale_discount_amount'] += $val['outsale_discount_amount'];
			$total['outsale_netsale_amount'] += $val['outsale_netsale_amount'];
			$total['outsale_norate_netsale_amount'] += $val['outsale_norate_netsale_amount'];

			$total['merge_goods_number'] += $val['merge_goods_number'];
			$total['merge_sale_amount'] += $val['merge_sale_amount'];
			$total['merge_discount_amount'] += $val['merge_discount_amount'];
			$total['merge_netsale_amount'] += $val['merge_netsale_amount'];
			$total['merge_norate_netsale_amount'] += $val['merge_norate_netsale_amount'];

			$total['cost_amount'] += $val['cost_amount'];
			$total['nofaxcost_amount'] += $val['nofaxcost_amount'];
			$total['gross_amount'] += $val['gross_amount'];
		}
		if ($total['merge_norate_netsale_amount']!=0) {
			$total['gross_percent'] = $total['gross_amount']/$total['merge_norate_netsale_amount'];
		}else{
			$total['gross_percent'] = 0;
		}
		if ($total['nofaxcost_amount']!=0) {
				$total['fax_percent'] = $total['cost_amount']/$total['nofaxcost_amount'] - 1;
		}else{
			$total['fax_percent'] = 0;
		}
		$total['formated_sale_amount'] = sprintf('%.2f',$total['sale_amount']);
		$total['formated_discount_amount'] = sprintf('%.2f',-$total['discount_amount']);
		$total['formated_netsale_amount'] = sprintf('%.2f',$total['netsale_amount']);
		$total['formated_norate_netsale_amount'] = sprintf('%.2f',$total['norate_netsale_amount']);

		$total['formated_outsale_sale_amount'] = sprintf('%.2f',$total['outsale_sale_amount']);
		$total['formated_outsale_discount_amount'] = sprintf('%.2f',-$total['outsale_discount_amount']);
		$total['formated_outsale_netsale_amount'] = sprintf('%.2f',$total['outsale_netsale_amount']);
		$total['formated_outsale_norate_netsale_amount'] = sprintf('%.2f',$total['outsale_norate_netsale_amount']);

		$total['formated_merge_sale_amount'] = sprintf('%.2f',$total['merge_sale_amount']);
		$total['formated_merge_discount_amount'] = sprintf('%.2f',-$total['merge_discount_amount']);
		$total['formated_merge_netsale_amount'] = sprintf('%.2f',$total['merge_netsale_amount']);
		$total['formated_merge_norate_netsale_amount'] = sprintf('%.2f',$total['merge_norate_netsale_amount']);

		$total['formated_cost_amount'] = sprintf('%.2f',$total['cost_amount']);
		$total['formated_nofaxcost_amount'] = sprintf('%.2f',$total['nofaxcost_amount']);
		$total['formated_gross_amount'] = sprintf('%.2f',$total['gross_amount']);
		$total['formated_gross_percent'] = sprintf('%.0f%%',$total['gross_percent']*100);
		$total['formated_fax_percent'] = sprintf('%.2f',$total['fax_percent']);
                $dbi->query('ROLLBACK');
		return array('result'=>$result,'total'=>$total)	;
		//计算合计
	}


	public function depot_real_inventory_report ($filter)
	{
		$category_id = isset($filter['category_id'])?$filter['category_id']:'0';
		$brand_id = isset($filter['brand_id'])?$filter['brand_id']:'0';
		$product_sn = isset($filter['product_sn'])?$filter['product_sn']:'';
		$location_name = isset($filter['location_name'])?$filter['location_name']:'';
		$onsale_id = isset($filter['onsale_id'])?$filter['onsale_id']:'-1';
		$depot_id = isset($filter['depot_id'])?$filter['depot_id']:'0';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';


		if(isset($arrayadd['onsale_id']) && $arrayadd['onsale_id'] >= 0)
		{
			$whereadd .= " AND gl.on_sale  = '" . $arrayadd['onsale_id'] . "' ";
		}
		if(isset($arrayadd['photo_id']) && $arrayadd['photo_id'] >= 0)
		{
			$whereadd .= " AND gl.is_pic  = '" . $arrayadd['photo_id'] . "' ";
		}

		if(isset($arrayadd['packet_code']) && !empty($arrayadd['packet_code']))
		{
			$whereadd .= " AND p.packet_name  like '%" . $arrayadd['packet_code'] . "%' ";
		}



		$timewhere = " AND ((tr.trans_type NOT IN ('3','4') AND tr.trans_status IN ('2','4') AND TO_DAYS(tr.update_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tr.update_date)<= TO_DAYS('" . $endtime . "')) OR (tr.trans_type IN ('3','4') AND tr.trans_status IN ('1','2','3','4') AND TO_DAYS(tr.finance_check_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tr.finance_check_date)<= TO_DAYS('" . $endtime . "')))";
		$timekey = "IF(tr.trans_type IN ('3','4'),tr.finance_check_date,tr.update_date) as checktime ";

		$whereadd = "";

		if($category_id != '0') $whereadd .= " AND (g.category_id = '" . $category_id . "' OR pc.category_id = '" . $category_id . "') ";
		if($brand_id != '0') $whereadd .= " AND g.brand_id  = '" . $brand_id . "' ";
		if($onsale_id != '-1') $whereadd .= " AND g.is_onsale  = '" . $onsale_id . "' ";
		if(!empty($product_sn))
		{
			if(strpos($product_sn,',') === false)
			{
				$whereadd .= " AND g.product_sn  LIKE '%" . $product_sn . "%' ";
			}
			else
			{
				$in_str = db_create_in(explode(',',$product_sn),'g.product_sn');
				$whereadd .= ' AND ' . $in_str;
			}
		}
		if($depot_id != '0') $whereadd .= " AND tr.depot_id  = '" . $depot_id . "' ";
		if(!empty($location_name)) $whereadd .= " AND l.location_name  like '%" . $location_name . "%' ";

		$mainsql = "SELECT DISTINCT tr.transaction_id,tr.product_id,tr.color_id,tr.size_id,tr.product_number,tr.trans_type,tr.trans_sn,tr.trans_direction,IF(g.consign_price > 0,g.consign_price,g.cost_price) as cost_price,g.category_id,g.product_sn,g.product_name,g.brand_id,g.provider_id,g.provider_productcode,g.goods_cess,b.brand_name,".
			"c.parent_id, pc.category_name AS pcatname,c.category_name AS catname,gc.color_name,gc.color_sn,gs.size_name,gs.size_sn," . $timekey .
				" FROM ty_transaction_info as tr " .
				" LEFT JOIN ty_product_info AS g ON g.product_id = tr.product_id" .
				" LEFT JOIN ty_product_brand AS b ON g.brand_id = b.brand_id" .
				" LEFT JOIN ty_product_category AS c ON g.category_id = c.category_id" .
				" LEFT JOIN ty_product_category AS pc ON c.parent_id = pc.category_id" .
				" LEFT JOIN ty_product_color AS gc ON tr.color_id = gc.color_id" .
				" LEFT JOIN ty_product_size AS gs ON tr.size_id = gs.size_id" .
				" WHERE 1 " . $timewhere . $whereadd . " ORDER BY g.category_id ASC,tr.product_id ASC ,tr.color_id ASC,tr.size_id ASC,checktime ASC";
		$query = $this->db->query($mainsql);
		$result = $query->result_array();

		//得到期初期末库存
		$sql = "SELECT tr.product_id,tr.color_id,tr.size_id,IF(g.consign_price > 0,g.consign_price,g.cost_price) as cost_price,g.goods_cess,SUM(IF((tr.trans_type NOT IN ('3','4') AND tr.trans_status IN ('2','4') AND TO_DAYS(tr.update_date)< TO_DAYS('" . $starttime . "')) " .
					"OR (tr.trans_type IN ('3','4') AND tr.trans_status IN ('1','2','3','4') AND tr.finance_check_date > 0 AND TO_DAYS(tr.finance_check_date)< TO_DAYS('" . $starttime . "')),tr.product_number,0)) AS before_product_number," .
					"SUM(IF((tr.trans_type NOT IN ('3','4') AND tr.trans_status IN ('2','4') AND TO_DAYS(tr.update_date)<= TO_DAYS('" . $endtime . "')) " .
					"OR (tr.trans_type IN ('3','4') AND tr.trans_status IN ('1','2','3','4') AND tr.finance_check_date > 0 AND TO_DAYS(tr.finance_check_date)<= TO_DAYS('" . $endtime . "')),tr.product_number,0)) AS after_product_number " .
					"FROM ty_transaction_info as tr " .
					"LEFT JOIN ty_product_info as g ON g.product_id=tr.product_id " .
					" LEFT JOIN ty_product_category AS c ON g.category_id = c.category_id" .
					" LEFT JOIN ty_product_category AS pc ON c.parent_id = pc.category_id" .
					" WHERE 1 ";

			if($category_id != '0') $sql .= " AND (g.category_id = '" . $category_id . "' OR pc.category_id = '" . $category_id . "') ";
			if($brand_id != '0') $sql .= " AND g.brand_id  = '" . $brand_id . "' ";
			//if($color_id != '0') $sql .= " AND tr.color_id  = '" . $color_id . "' ";
			//if($size_id != '0') $sql .= " AND tr.size_id  = '" . $size_id . "' ";
			if(!empty($product_sn)) $sql .= " AND g.product_sn  LIKE '%" . $product_sn . "%' ";
			//if(!empty($keyword)) $sql .= " AND g.product_name  LIKE '%" . $keyword . "%' ";
			//if($cooperation_id != '0') $sql .= " AND g.cooperation_id  = '" . $cooperation_id . "' ";
			if($depot_id != '0') $sql .= " AND tr.depot_id  = '" . $depot_id . "' ";

		$sql .= " GROUP BY product_id,color_id,size_id";
		$query = $this->db->query($sql);
		$tmparrrs = $query->result_array();

		$beforeNum = array();
		$afterNum = array();
		$total_before_num = 0;
		$total_before_amount = 0;
		$total_after_num = 0;
		$total_after_amount = 0;
		$total_after_cess_amount = 0;
		foreach ($tmparrrs as $value)
		{
			$beforeNum[$value['product_id']][$value['color_id']][$value['size_id']] = $value['before_product_number'];
			$afterNum[$value['product_id']][$value['color_id']][$value['size_id']] = $value['after_product_number'];

			$total_after_num += $value['after_product_number'];
			$total_after_amount += number_format(($value['after_product_number']*$value['cost_price']), 2, '.', '');
			$total_before_num += $value['before_product_number'];
			$total_before_amount += number_format(($value['before_product_number']*$value['cost_price']), 2, '.', '');
			if($value['goods_cess']>0)
			{
				$total_after_cess_amount += number_format($value['after_product_number'] * $value['cost_price']/(1+$value['goods_cess']), 2, '.', '');
			}
			else
			{
				$total_after_cess_amount += number_format(($value['after_product_number']*$value['cost_price']), 2, '.', '');
			}
		}
		$total_after_amount = number_format($total_after_amount, 2, '.', '');
		$total_before_amount = number_format($total_before_amount, 2, '.', '');
		$countarr = array('total_before_num'=>$total_before_num, 'total_after_num'=>$total_after_num,
						'total_before_amount'=>$total_before_amount, 'total_after_amount'=>$total_after_amount,
						'total_after_cess_amount'=>$total_after_cess_amount
							);


		$sql = " SELECT depot_in_code,depot_in_type FROM ty_depot_in_main WHERE TO_DAYS(audit_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(audit_date)<= TO_DAYS('" . $endtime . "')";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$sn2type = array();
		foreach($tmparr as $value)
		{
			$sn2type[$value['depot_in_code']] = $value['depot_in_type'];
		}

		$sql = " SELECT depot_out_code,depot_out_type FROM ty_depot_out_main WHERE TO_DAYS(audit_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(audit_date)<= TO_DAYS('" . $endtime . "')";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		foreach($tmparr as $value)
		{
			$sn2type[$value['depot_out_code']] = $value['depot_out_type'];
		}

		$sql = "SELECT depot_type_id,depot_type_name FROM ty_depot_iotype WHERE 1";
		$query = $this->db->query($sql);
		$tmparr = $query->result_array();
		$typescript = array();
		foreach($tmparr as $value)
		{
			$typescript[$value['depot_type_id']] = $value['depot_type_name'];
		}
		$mainscript = array('3'=>'销售订单','4'=>'退货单','5'=>'换货单','6'=>'调仓单');

		$cur_goods = 0;
		$cur_color = 0;
		$cur_size = 0;
		$rec_num_arr = array();
		$rec_color_arr = array();
		$goodslist = array();
		reset($result);
		foreach($result as $key => $value)
		{
				if($value['trans_type'] == TRANS_TYPE_DIRECT_IN or $value['trans_type'] == TRANS_TYPE_DIRECT_OUT)
				{
					$value['typesrcipt'] = $typescript[$sn2type[$value['trans_sn']]];
				}
				else
				{
					$value['typesrcipt'] = $mainscript[$value['trans_type']];
				}
				$value['afternum'] = $afterNum[$value['product_id']][$value['color_id']][$value['size_id']];
				$value['beforenum'] = $beforeNum[$value['product_id']][$value['color_id']][$value['size_id']];

				$value['beforecount'] = number_format(($value['beforenum'] * $value['cost_price']), 2, '.', '');
				$value['aftercount'] = number_format(($value['afternum'] * $value['cost_price']), 2, '.', '');
				if($value['goods_cess'] > 0)
				{
					$value['aftercesscount'] = number_format($value['afternum'] * $value['cost_price']/(1+$value['goods_cess']), 2, '.', '');
				}
				else
				{
					$value['aftercesscount'] = $value['aftercount'];
				}
				$value['productcount'] = number_format(($value['product_number'] * $value['cost_price']), 2, '.', '');
				if($cur_goods != $value['product_id'])
				{
					$value['productfirst'] =1;
					$value['kindfirst'] =1;
					$rec_num_arr[$value['product_id']] = 1;
					$rec_color_arr[$value['product_id']][$value['color_id']][$value['size_id']] = 1;
					$cur_goods = $value['product_id'];
					$cur_color = $value['color_id'];
					$cur_size = $value['size_id'];
				}
				else
				{
					if($cur_color != $value['color_id'] || $cur_size != $value['size_id'])
					{
						$value['kindfirst'] =1;
						$rec_color_arr[$value['product_id']][$value['color_id']][$value['size_id']] = 1;
						$cur_color = $value['color_id'];
						$cur_size = $value['size_id'];
					}
					else
					{
						$value['kindfirst'] =0;
						$rec_color_arr[$value['product_id']][$value['color_id']][$value['size_id']]++;
					}
					$value['productfirst'] =0;
					$rec_num_arr[$value['product_id']]++;
				}
				$goodslist[] = $value;
		}
		$num = array('0'=>0,'1'=>0); //0入库 1出库
		$amount = array('0'=>0,'1'=>0);
		for($i=0;$i<count($goodslist);$i++)
		{
			$goodslist[$i]['rec_count'] = $rec_num_arr[$goodslist[$i]['product_id']];
			$goodslist[$i]['rec_color_size_count'] = $rec_color_arr[$goodslist[$i]['product_id']][$goodslist[$i]['color_id']][$goodslist[$i]['size_id']];

			$num[$goodslist[$i]['trans_direction']] += $goodslist[$i]['product_number'];
			$amount[$goodslist[$i]['trans_direction']] += $goodslist[$i]['productcount'];
		}
		$amount[0] = number_format($amount[0], 2, '.', '');
		$amount[1] = number_format($amount[1], 2, '.', '');

		$countarr['numarr'] = $num;
		$countarr['amountarr'] = $amount;
		return array('list'=>$goodslist,'count'=>$countarr);
	}
	public function get_hourly_pv() {
		$today = date('Y-m-d');		
		$first_day = date('Y-m-d', strtotime('-2 day'));

		$sql = "select sum(pv) hourly_pv, type, concat(year, '-', month, '-',  day, '-', hours) as pv_hour from ty_product_access where TO_DAYS('" . $today . "') >= TO_DAYS(add_time) and TO_DAYS(add_time) > TO_DAYS('" . $first_day . "')  GROUP BY pv_hour, type";
		
		$data = array();
		$data = $this->db->query($sql)->result_array();	
		foreach ($data as $key => $value) {			
			$date_arr = explode('-',$value['pv_hour']);
			$new_key = date('Y-m-d-H', mktime($date_arr[3], 0, 0, $date_arr[1], $date_arr[2], $date_arr[0]));
			$result[$new_key][$value['type']] = ($value['hourly_pv']);
		}	
		ksort($result);
		$data = null;
		$data['min_hour'] = preg_replace('/\d{4}-\d{2}-/', '', min(array_keys($result)));
		$data['max_hour'] = preg_replace('/\d{4}-\d{2}-/', '', max(array_keys($result)));

		$data['hour_list'] = '';
		$data['product_pv_list'] = '';
		$data['article_pv_list'] = '';
		$data['course_pv_list'] = '';
		foreach ($result as $key => $value) {
			$data['hour_list'] .= '"' . $key . '",';
			$data['product_pv_list'] .= (isset($value['product']) ? $value['product'] : 0) . ',';

			$data['article_pv_list'] .= (isset($value['article']) ? $value['article'] : 0) . ',';

			$data['course_pv_list'] .= (isset($value['course']) ? $value['course'] : 0) . ',';
		}
		$data['hour_list'] = preg_replace('/\d{4}-\d{2}-/', '', rtrim($data['hour_list'], ','));
		$data['product_pv_list'] = rtrim($data['product_pv_list'], ',');
		$data['article_pv_list'] = rtrim($data['article_pv_list'], ',');
		$data['course_pv_list'] = rtrim($data['course_pv_list'], ',');
		$data['max_scale'] = max(max(explode(',', $data['product_pv_list'])), max(explode(',', $data['article_pv_list'])), 
			max(explode(',', $data['course_pv_list'])));

		//var_export($data);exit();
		return $data;		
	}

	public function get_daily_pv() {
		$today = date('Y-m-d');		
		$first_day = date('Y-m-d', strtotime('-30 day'));

		$sql = "select sum(pv) daily_pv, type, date(add_time) as pv_date from ty_product_access where TO_DAYS('" . $today . "') >= TO_DAYS(add_time) and TO_DAYS(add_time) > TO_DAYS('" . $first_day . "')  GROUP BY pv_date, type";
		
		
		$data = array();
		$data = $this->db->query($sql)->result_array();	
		foreach ($data as $key => $value) {			
			$result[substr($value['pv_date'], 5)][$value['type']] = ($value['daily_pv']);
		}	
		$data = null;
		$data['min_day'] = min(array_keys($result));
		$data['max_day'] = max(array_keys($result));

		$data['day_list'] = '';
		$data['product_pv_list'] = '';
		$data['article_pv_list'] = '';
		$data['course_pv_list'] = '';
		foreach ($result as $key => $value) {
			$data['day_list'] .= '"' . $key . '",';
			$data['product_pv_list'] .= (isset($value['product']) ? $value['product'] : 0) . ',';

			$data['article_pv_list'] .= (isset($value['article']) ? $value['article'] : 0) . ',';

			$data['course_pv_list'] .= (isset($value['course']) ? $value['course'] : 0) . ',';
		}
		$data['day_list'] = rtrim($data['day_list'], ',');
		$data['product_pv_list'] = rtrim($data['product_pv_list'], ',');
		$data['article_pv_list'] = rtrim($data['article_pv_list'], ',');
		$data['course_pv_list'] = rtrim($data['course_pv_list'], ',');
		$data['max_scale'] = max(max(explode(',', $data['product_pv_list'])), max(explode(',', $data['article_pv_list'])), 
			max(explode(',', $data['course_pv_list'])));

		//var_export($data);exit();
		return $data;		
	}

	public function get_weekly_pv() {
		$today = date('Y-m-d');		
		$first_day = date('Y-m-d', strtotime('-30 week Monday')); //求前30周，每周以周日作为第一天
		$sql = "select sum(pv) weekly_pv,type, CONCAT(year, '-', week(add_time)) as week_year from ty_product_access where TO_DAYS('" . $today . "') >= TO_DAYS(add_time) and TO_DAYS(add_time) > TO_DAYS('" . $first_day . "')  GROUP BY week_year, type";
		
		$data = array();
		$data = $this->db->query($sql)->result_array();	
		foreach ($data as $key => $value) {
			$tmp = explode('-', $value['week_year']);
			$tmp = date('Y-m-d', strtotime($tmp[0].'W'.$tmp[1]));
			$result[$tmp][$value['type']] = ($value['weekly_pv']);
		}	
		$data = null;
		$data['min_week'] = min(array_keys($result));
		$data['max_week'] = max(array_keys($result));

		$data['week_list'] = '';
		$data['product_pv_list'] = '';
		$data['article_pv_list'] = '';
		$data['course_pv_list'] = '';
		foreach ($result as $key => $value) {
			$data['week_list'] .= '"' . $key . '",';
			$data['product_pv_list'] .= (isset($value['product']) ? $value['product'] : 0) . ',';

			$data['article_pv_list'] .= (isset($value['article']) ? $value['article'] : 0) . ',';

			$data['course_pv_list'] .= (isset($value['course']) ? $value['course'] : 0) . ',';
		}
		$data['week_list'] = rtrim($data['week_list'], ',');
		$data['product_pv_list'] = rtrim($data['product_pv_list'], ',');
		$data['article_pv_list'] = rtrim($data['article_pv_list'], ',');
		$data['course_pv_list'] = rtrim($data['course_pv_list'], ',');
		$data['max_scale'] = max(max(explode(',', $data['product_pv_list'])), max(explode(',', $data['article_pv_list'])), 
			max(explode(',', $data['course_pv_list'])));	
		///var_export($data);exit();
		return $data;	
	}

	public function get_monthly_pv() {
		$today = date('Y-m-d');		
		$first_day = date('Y-m-d', strtotime('-12 month')); 
		$sql = "select sum(pv) monthly_pv, type, extract(year_month from add_time) as month_year from ty_product_access where TO_DAYS('" . $today . "') >= TO_DAYS(add_time) and TO_DAYS(add_time) > TO_DAYS('" . $first_day . "') GROUP BY month_year, type";
		
		$data = array();
		$data = $this->db->query($sql)->result_array();	
		foreach ($data as $key => $value) {			
			$result[$value['month_year']][$value['type']] = ($value['monthly_pv']);
		}	
		$data = null;
		$data['min_month'] = min(array_keys($result));
		$data['max_month'] = max(array_keys($result));

		$data['month_list'] = '';
		$data['product_pv_list'] = '';
		$data['article_pv_list'] = '';
		$data['course_pv_list'] = '';
		foreach ($result as $key => $value) {
			$data['month_list'] .= '"' . $key . '",';
			$data['product_pv_list'] .= (isset($value['product']) ? $value['product'] : 0) . ',';

			$data['article_pv_list'] .= (isset($value['article']) ? $value['article'] : 0) . ',';

			$data['course_pv_list'] .= (isset($value['course']) ? $value['course'] : 0) . ',';
		}
		$data['month_list'] = rtrim($data['month_list'], ',');
		$data['product_pv_list'] = rtrim($data['product_pv_list'], ',');
		$data['article_pv_list'] = rtrim($data['article_pv_list'], ',');
		$data['course_pv_list'] = rtrim($data['course_pv_list'], ',');
		$data['max_scale'] = max(max(explode(',', $data['product_pv_list'])), max(explode(',', $data['article_pv_list'])), 
			max(explode(',', $data['course_pv_list'])));	
		//var_export($data);exit();
		
		return $data;	
	}

	/**
	 * v@2016-03-24  v@2016-05-24(修改版)
	 * 订单销售利润报表
	 */ 
	public function order_profits_detail_report ($filter)
	{	
		if(empty($filter))
		{
			return array();
		}

		$order_sn = isset($filter['order_sn'])?$filter['order_sn']:'';
		$category_id = isset($filter['category_id'])?$filter['category_id']:'0';
		$brand_id = isset($filter['brand_id'])?$filter['brand_id']:'0';
		$provider_id = isset($filter['provider_id'])?$filter['provider_id']:'0';
		$product_sn = isset($filter['product_sn'])?$filter['product_sn']:'';
        $product_id = isset($filter['product_id'])?$filter['product_id']:'';
		$keyword = isset($filter['keyword'])?$filter['keyword']:'';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';
		$is_start_time = isset($filter['is_start_time'])?$filter['is_start_time']:'';
		$is_end_time = isset($filter['is_end_time'])?$filter['is_end_time']:'';

		
		$timewhere = "";
		if(!empty($starttime) && !empty($endtime))
		{
			$timewhere = " AND TO_DAYS(tf.finance_check_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tf.finance_check_date)<= TO_DAYS('" . $endtime . "')";
		}
		if(!empty($is_start_time) && !empty($is_end_time))
		{
			$timewhere = " AND TO_DAYS(tf.update_date)>= TO_DAYS('" . $is_start_time . "') AND TO_DAYS(tf.update_date)<= TO_DAYS('" . $is_end_time . "')";
		}

		$whereadd = "";
		if($category_id != '0') $whereadd .= " AND (pf.category_id = '" . $category_id . "' OR pg.category_id = '" . $category_id . "') ";
		if($brand_id != '0') $whereadd .= " AND pf.brand_id  = '" . $brand_id . "' ";
		if($provider_id != '0') $whereadd .= " AND pp.provider_id  = '" . $provider_id . "' ";
		if(!empty($product_sn)) $whereadd .= " AND pf.product_sn  LIKE '%" . $product_sn . "%' ";
        if(!empty($product_id)) $whereadd .= " AND tf.product_id  = '" . $product_id . "' ";
		if(!empty($keyword)) $whereadd .= " AND pf.product_name  LIKE '%" . $keyword . "%' ";
		if(!empty($order_sn)) $whereadd .= " AND tf.trans_sn LIKE '%" . $order_sn . "%' ";

		//获取已经完结的订单
		$sql = "SELECT DISTINCT tf.transaction_id, tf.trans_sn,tf.shop_price,pgr.name AS genre_name,of.consignee, of.mobile, CONCAT(p.region_name,' ', c.region_name, ' ', d.region_name, ' ', of.address) AS address, op.operator,of.order_price, of.recheck_shipping_fee, of.real_shipping_fee, IF(tf.trans_type = 3,of.is_ok_date,rt.is_ok_date) is_ok_date, pf.product_sn, pf.product_name, pf.brand_name, pp.provider_name, pp.provider_code, sub.provider_barcode, ps.size_name, tf.product_number, tf.shop_price paid_price, IF(tf.consign_price > 0, tf.consign_price, tf.cost_price) cost_price, pg.category_name class_one, pg1.category_name class_two, tf.finance_check_date
				FROM ty_transaction_info AS tf
				  LEFT JOIN ty_product_size AS ps ON ps.size_id = tf.size_id
				  LEFT JOIN ty_product_info AS pf ON pf.product_id = tf.product_id
				  LEFT JOIN ty_product_sub AS sub ON sub.color_id = tf.color_id AND sub.size_id = tf.size_id AND sub.product_id = tf.product_id
				  LEFT JOIN ty_product_category AS pg1 ON pg1.category_id = pf.category_id
				  LEFT JOIN ty_product_category AS pg ON pg.category_id = pg1.parent_id 
				  LEFT JOIN ty_product_provider AS pp ON pp.provider_id = pf.provider_id 
				  LEFT JOIN ty_order_info AS of ON of.order_sn = tf.trans_sn 
                                  LEFT JOIN ty_order_product AS op ON of.order_id = op.order_id AND tf.sub_id = op.op_id AND tf.trans_type = 3  
				  LEFT JOIN ty_order_return_info AS rt ON rt.return_sn = tf.trans_sn 
                                  LEFT JOIN ty_product_genre pgr ON pgr.id = of.genre_id 
                                  LEFT JOIN ty_region_info p ON of.province = p.region_id 
                                  LEFT JOIN ty_region_info c ON of.city = c.region_id 
                                  LEFT JOIN ty_region_info d ON of.district = d.region_id 
				WHERE 
				  1 AND tf.trans_type = 3 AND trans_status != 5  AND ((of.is_ok = 1 AND of.order_status = 1 AND of.pay_status = 1 AND of.shipping_status = 1 ) OR rt.is_ok = 1 ) ".$timewhere.$whereadd."
				  ORDER BY is_ok_date DESC,tf.update_date DESC,tf.transaction_id DESC";
		$query = $this->db->query($sql);
		$order_product = $query->result();
		return array('order_product'=>$order_product);
	}

	/**
	 * v@2016-03-24  v@2016-05-25
	 * 订单销售利润汇总表  修改为退货表
	 */ 
	public function order_profits_return_report ($filter)
	{
		$order_sn = isset($filter['order_sn'])?$filter['order_sn']:'';
		$admin_name = isset($filter['admin_name'])?$filter['admin_name']:'';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';
		$is_start_time = isset($filter['is_start_time'])?$filter['is_start_time']:'';
		$is_end_time = isset($filter['is_end_time'])?$filter['is_end_time']:'';

		if((empty($starttime) || empty($endtime)) && (empty($is_start_time) || empty($is_end_time)) && empty($order_sn) && empty($admin_name))
		{
			return array();
		}

		$timewhere = "";
		if(!empty($starttime) && !empty($endtime))
		{
			$timewhere = " AND TO_DAYS(rt.finance_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(rt.finance_date)<= TO_DAYS('" . $endtime . "')";
		}
		if(!empty($is_start_time) && !empty($is_end_time))
		{
			$timewhere = " AND TO_DAYS(rt.is_ok_date)>= TO_DAYS('" . $is_start_time . "') AND TO_DAYS(rt.is_ok_date)<= TO_DAYS('" . $is_end_time . "')";
		}

		$whereadd = "";
		if(!empty($order_sn)) $whereadd .= " AND tf.trans_sn LIKE '%" . $order_sn . "%' ";
		if(!empty($admin_name)) $whereadd .= " AND ai.admin_name LIKE '%" . $admin_name . "%' ";

		//获取订单明细
		$sql = "SELECT 
				  tf.trans_sn,
				  rt.paid_price,
				  rt.finance_date,
				  rt.create_date,
				  SUM(
				    IF(
				      tf.consign_price > 0,
				      tf.consign_price,
				      tf.cost_price
				    ) * tf.product_number
				  ) cost_price,
				  op.payment_date,
				  pf.pay_name,
				  ai.admin_name order_name,
				  ai1.admin_name return_name
				FROM
				  ty_transaction_info AS tf 
				  LEFT JOIN ty_order_return_info AS rt 
				    ON rt.return_sn = tf.trans_sn 
				  LEFT JOIN ty_order_info AS of 
				    ON of.order_id = rt.order_id 
				  LEFT JOIN ty_order_payment AS op 
				    ON op.order_id = rt.order_id
				  LEFT JOIN ty_payment_info AS pf 
				    ON pf.pay_id = op.pay_id 
				  LEFT JOIN ty_admin_info AS ai 
				    ON ai.admin_id = of.create_admin
				  LEFT JOIN ty_admin_info AS ai1 
				    ON ai1.admin_id = rt.create_admin  
				WHERE 1 
				  AND tf.trans_type = 4 
				  AND tf.trans_status = 4 
				  AND rt.is_ok = 1 
				  AND rt.pay_status = 1
				  ".$timewhere.$whereadd."				
				GROUP BY tf.trans_sn 
				ORDER BY rt.is_ok_date DESC,
				  tf.update_date DESC,
				  tf.transaction_id DESC ";

		$query = $this->db->query($sql);
		$order_profits = $query->result();
		return array('order_profits'=>$order_profits);
	}

	/**
	 * v@2016-05-18
	 * 订单销售利润汇总表 修改版
	 */ 
	public function order_profits_summary_report_to ($filter)
	{
		$order_sn = isset($filter['order_sn'])?$filter['order_sn']:'';
		$admin_name = isset($filter['admin_name'])?$filter['admin_name']:'';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';
		$is_start_time = isset($filter['is_start_time'])?$filter['is_start_time']:'';
		$is_end_time = isset($filter['is_end_time'])?$filter['is_end_time']:'';

		if((empty($starttime) || empty($endtime)) && (empty($is_start_time) || empty($is_end_time)) && empty($order_sn) && empty($admin_name))
		{
			return array();
		}

		$timewhere = "";
		if(!empty($starttime) && !empty($endtime))
		{
			$timewhere = " AND TO_DAYS(tf.finance_check_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tf.finance_check_date)<= TO_DAYS('" . $endtime . "')";
		}
		if(!empty($is_start_time) && !empty($is_end_time))
		{
			$timewhere = " AND TO_DAYS(tf.update_date)>= TO_DAYS('" . $is_start_time . "') AND TO_DAYS(tf.update_date)<= TO_DAYS('" . $is_end_time . "')";
		}
		$whereadd = "";
		if(!empty($order_sn)) $whereadd .= " AND tf.trans_sn LIKE '%" . $order_sn . "%' ";
		if(!empty($admin_name)) $whereadd .= " AND ai.admin_name LIKE '%" . $admin_name . "%' ";

		//获取订单明细
		$sql = "SELECT 
  MAX(tf.finance_check_date) finance_check_date,
  MAX(tf.update_date) update_date,
  tf.trans_sn,
  zenpin.zenpin_cost,
  of.paid_price AS shop_price,
  (
    SUM(
      IF(
        tf.consign_price > 0,
        tf.consign_price,
        tf.cost_price
      ) * tf.product_number * - 1
    ) - IF(
      zenpin.zenpin_cost IS NULL,
      0,
      zenpin.zenpin_cost
    )
  ) cost_price,
  os.source_name,
  ri.region_name AS province_name, 
  rito.region_name AS city_name,
  ai.admin_name,
  of.invoice_title,
  of.consignee,
  of.order_status,
  of.payment_date,
  pf.pay_name,
  of.invoice_no,
  of.is_ok,
  of.shipping_date,
  IF(ro.order_id IS NULL, 0, 1) AS has_return,
  ro.paid_price AS tui_price,
  ro.return_cost_price 
  ,recheck_weight_unreal, recheck_shipping_fee,real_shipping_fee,saler 
FROM
  ty_transaction_info AS tf 
  INNER JOIN
    (SELECT 
	DISTINCT(oi.order_id), invoice_title,consignee,order_status,recheck_weight_unreal, recheck_shipping_fee,real_shipping_fee,saler, invoice_no,is_ok,shipping_date,IF(op.pay_id = 6,paid_price - payment_money,paid_price) paid_price,pay_status, shipping_status,order_sn,source_id,province,city,create_admin,oi.pay_id, MAX(op.payment_date) AS payment_date 
    FROM	
      ty_order_info AS oi, ty_order_payment AS op WHERE oi.order_id = op.order_id GROUP BY oi.order_id
      ) AS of 
    ON of.order_sn = tf.trans_sn
     LEFT JOIN ty_payment_info AS pf 
    ON pf.pay_id = of.pay_id  
  LEFT JOIN ty_order_source AS os 
    ON os.source_id = of.source_id 
  LEFT JOIN ty_region_info AS ri 
    ON ri.region_id = of.province 
  LEFT JOIN ty_region_info AS rito 
    ON rito.region_id = of.city 
  LEFT JOIN ty_admin_info AS ai 
    ON ai.admin_id = of.create_admin 
  LEFT JOIN 
    (SELECT  
       roi.order_id, roi.`return_sn`, 
      roi.is_ok,
      SUM(
        IF(
          ti.`consign_price` > 0,
          ti.consign_price,
          ti.cost_price
        ) * ti.product_number
      ) return_cost_price, 
      
      SUM(
        ti.`shop_price` * ti.product_number
      ) paid_price  
    FROM
      ty_order_return_info AS roi 
      LEFT JOIN ty_transaction_info AS ti 
        ON ti.`trans_sn` = roi.return_sn 
       LEFT JOIN ty_order_info i2 ON roi.`order_id` = i2.`order_id` 
    WHERE roi.return_status = 1 AND roi.pay_status = 1 AND roi.is_ok = 1 AND i2.`shipping_true` = 1 GROUP BY roi.`order_id`) ro 
    ON of.order_id = ro.order_id 
  LEFT JOIN 
    (SELECT 
      trans_sn,
      SUM(
        IF(
          consign_price > 0,
          consign_price,
          cost_price
        ) * product_number * - 1
      ) zenpin_cost 
    FROM
      ty_transaction_info 
    WHERE shop_price = 0 AND trans_type = 3 AND trans_status <> 5 
    GROUP BY trans_sn ) zenpin 
    ON tf.trans_sn = zenpin.trans_sn 
WHERE 1 
  AND tf.trans_type = 3 
  AND trans_status != 5 
  AND (
    (
      of.is_ok = 1 
      AND of.order_status = 1 
      AND of.pay_status = 1 
      AND of.shipping_status = 1
    ) 
    OR ro.is_ok = 1
  ) 
  ".$timewhere.$whereadd."	
GROUP BY tf.trans_sn 
ORDER BY tf.finance_check_date DESC,
  tf.update_date DESC,
  tf.transaction_id DESC ";
		$query = $this->db->query($sql);
		$order_profits = $query->result();
		return array('order_profits'=>$order_profits);
	}
	
	public function inventory_details_report ($filter)
	{
		$brand_id = isset($filter['brand_id'])? (int)$filter['brand_id']:'0';
		$is_expire_date = isset($filter['is_expire_date'])? (int)$filter['is_expire_date']:'0';
		$product_sn = isset($filter['product_sn'])? trim($filter['product_sn']) : '';
                $sku = isset($filter['sku'])? trim($filter['sku']) : '';
                $keyword = isset($filter['keyword'])? trim($filter['keyword']):'';
                $provider_barcode = isset($filter['provider_barcode'])? trim($filter['provider_barcode']):'';               
		//$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';                
                $e_starttime = isset($filter['e_start_time'])?$filter['e_start_time']:'';
		$e_endtime = isset($filter['e_end_time'])?$filter['e_end_time']:'';

		if(empty($endtime))
		{
			return array();
		}

		$timewhere = "((ti.trans_status IN ('2', '4') AND TO_DAYS(ti.update_date) <= TO_DAYS('".$endtime."')) 
                    OR (ti.trans_type IN ('3') AND ti.trans_status IN ('1') AND TO_DAYS(ti.create_date) <= TO_DAYS('".$endtime."')))";


                $where2 = "";
                $whereadd = "";

		if($brand_id != '0') $whereadd .= " AND p.brand_id  = '" . $brand_id . "' ";
                //没有有效期
		if($is_expire_date == 1) {
                    $whereadd .= " AND ti.expire_date = '0000-00-00' ";
                } elseif ($is_expire_date == 2) {
                    $whereadd .= " AND ti.expire_date != '0000-00-00' ";
                }

		if(!empty($product_sn)) $whereadd .= " AND p.product_sn  = '" . $product_sn . "' ";
		if(!empty($keyword)) $whereadd .= " AND p.product_name  LIKE '%" . $keyword . "%' ";
		if(!empty($provider_barcode)) $whereadd .= " AND ps.provider_barcode = '" . $provider_barcode . "' ";
                if (!empty($sku)){
                    $sku_arr = explode("_", $sku);
                    $whereadd .= " AND p.product_sn  = '" . $sku_arr[0] . "' ";
                    if (!empty($sku_arr[1])) $whereadd .= " AND s.size_sn  = '" . $sku_arr[1] . "' ";
                }
                
                if (!empty($filter['actual_stock']) && $filter['actual_stock'] == 1){
                    $where2 .= " AND a.real_num = 0";
                } elseif (!empty($filter['actual_stock']) && $filter['actual_stock'] == 2){
                    $where2 .= " AND a.real_num > 0";
                }
                
                if (!empty($filter['order_stock']) && $filter['order_stock'] == 1){
                    $where2 .= " AND a.order_num = 0";
                } elseif (!empty($filter['order_stock']) && $filter['order_stock'] == 2){
                    $where2 .= " AND a.order_num > 0";
                }
                
                if (!empty($filter['avail_stock']) && $filter['avail_stock'] == 1){
                    $where2 .= " AND a.avail_num = 0";
                } elseif (!empty($filter['avail_stock']) && $filter['avail_stock'] == 2){
                    $where2 .= " AND a.avail_num > 0";
                }

		$sql = "SELECT * FROM (SELECT p.product_id, p.brand_name, p.`product_name`, p.provider_productcode, p.product_sn, ps.`provider_barcode`, 
                    pc.`category_name`, s.size_sn, s.`size_name`, di.`depot_name`, li.`location_name`, MIN(IF(ti.trans_status = 4, ti.`update_date`, NOW())) AS rk_time ,
                    ti.`expire_date`, SUM(GREATEST(ti.`consign_price`, ti.`cost_price`)*ti.`product_number`) AS s_cost_price, 
                    SUM(IF(ti.trans_status IN ('2','4'),ti.product_number,0)) AS real_num, 
                    SUM(IF((ti.trans_type IN ('3') AND ti.trans_status IN ('1')),ti.product_number,0)) AS order_num, 
                    SUM(ti.product_number) AS avail_num  
                    FROM ty_transaction_info ti 
                    LEFT JOIN ty_product_info p ON ti.product_id = p.`product_id` 
                    LEFT JOIN ty_product_sub ps ON ti.`product_id` = ps.`product_id` AND ti.color_id = ps.color_id AND ti.size_id = ps.`size_id` 
                    LEFT JOIN `ty_product_size` s ON s.`size_id` = ti.`size_id` 
                    LEFT JOIN `ty_product_category` pc ON p.category_id = pc.`category_id` 
                    LEFT JOIN ty_depot_info di ON ti.`depot_id` = di.`depot_id` 
                    LEFT JOIN `ty_location_info` li ON ti.`location_id` = li.`location_id` 
                    WHERE 1 AND ".$timewhere.$whereadd." 
                    GROUP BY ti.product_id,ti.color_id,ti.size_id, ti.depot_id, ti.location_id) a WHERE 1".$where2;

		$query = $this->db->query($sql);
		$result = $query->result();
		return array("list" => $result);
	}

	/**
	 * v@2016-05-19
	 * 订单销售台帐（已财审，已发货/未发货）
	 */ 
	public function order_profits_sales_report ($filter)
	{
		$order_sn = isset($filter['order_sn'])?$filter['order_sn']:'';
		$admin_name = isset($filter['admin_name'])?$filter['admin_name']:'';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';

		if(empty($starttime) && empty($endtime) && empty($order_sn) && empty($admin_name))
		{
			return array();
		}

		$timewhere = "";
		if(!empty($starttime) && !empty($endtime))
		{
			$timewhere = " AND TO_DAYS(tf.finance_check_date)>= TO_DAYS('" . $starttime . "') AND TO_DAYS(tf.finance_check_date)<= TO_DAYS('" . $endtime . "')";
		}
		$whereadd = "";
		if(!empty($order_sn)) $whereadd .= " AND tf.trans_sn LIKE '%" . $order_sn . "%' ";
		if(!empty($admin_name)) $whereadd .= " AND ai.admin_name LIKE '" . $admin_name . "' ";

		//获取订单明细
		$sql = "SELECT tf.trans_sn, zenpin.zenpin_cost,
		of.paid_price as shop_price, 
		(SUM(IF(tf.consign_price > 0,tf.consign_price,tf.cost_price)* tf.product_number * -1) - if(zenpin.zenpin_cost IS NULL, 0, zenpin.zenpin_cost)) cost_price , 
		of.invoice_title, of.payment_date, pf.pay_name,of.consignee,of.order_status,os.source_name,ri.region_name as province_name,rito.region_name as city_name,ai.admin_name,of.invoice_no,tf.finance_check_date,tf.update_date,of.shipping_date,IF(ro.order_id IS NULL, 0, 1) AS has_return,
  ro.paid_price AS tui_price,
  ro.return_cost_price,of.pay_status,of.shipping_status    
				FROM ty_transaction_info AS tf
				  INNER JOIN
    (SELECT 
	DISTINCT(oi.order_id), invoice_title,consignee,order_status,recheck_weight_unreal, recheck_shipping_fee,real_shipping_fee, invoice_no,is_ok,shipping_date,IF(op.pay_id = 6,paid_price - payment_money,paid_price) paid_price,pay_status, shipping_status,order_sn,source_id,province,city,create_admin,oi.pay_id, MAX(op.payment_date) AS payment_date 
    FROM	
      ty_order_info AS oi, ty_order_payment AS op WHERE oi.order_id = op.order_id GROUP BY oi.order_id
      ) AS of  ON of.order_sn = tf.trans_sn
				  LEFT JOIN ty_payment_info AS pf ON pf.pay_id = of.pay_id
				  LEFT JOIN ty_order_source AS os ON os.source_id = of.source_id
				  LEFT JOIN ty_region_info AS ri ON ri.region_id = of.province	  
				  LEFT JOIN ty_region_info AS rito ON rito.region_id = of.city	  
				  LEFT JOIN ty_admin_info AS ai ON ai.admin_id = of.create_admin
				  LEFT JOIN 
    (SELECT  
       roi.order_id, roi.`return_sn`, 
      roi.is_ok,
      SUM(
        IF(
          ti.`consign_price` > 0,
          ti.consign_price,
          ti.cost_price
        ) * ti.product_number
      ) return_cost_price, 
      
      SUM(
        ti.`shop_price` * ti.product_number
      ) paid_price  
    FROM
      ty_order_return_info AS roi 
      LEFT JOIN ty_transaction_info AS ti 
        ON ti.`trans_sn` = roi.return_sn 
       LEFT JOIN ty_order_info i2 ON roi.`order_id` = i2.`order_id` 
    WHERE roi.return_status = 1 AND roi.pay_status = 1 AND roi.is_ok = 1 AND i2.`shipping_true` = 1 GROUP BY roi.`order_id`) ro 
    ON of.order_id = ro.order_id   
				  LEFT JOIN (SELECT trans_sn, 
							  SUM(IF(consign_price > 0,consign_price,cost_price)* product_number * -1) zenpin_cost
							FROM
							  ty_transaction_info
							WHERE shop_price = 0
							GROUP BY trans_sn) zenpin on tf.trans_sn = zenpin.trans_sn
				WHERE 1 
				  AND tf.trans_type = 3 
				  AND of.order_status = 1 
				  AND of.pay_status = 1 
				".$timewhere.$whereadd."						
				GROUP BY tf.trans_sn
				ORDER BY tf.finance_check_date DESC, tf.update_date DESC, tf.transaction_id DESC";
		$query = $this->db->query($sql);
		$order_profits = $query->result();
		return array('order_profits'=>$order_profits);
	}
        
	public function purchase_main_report ($filter)
	{
		$provider_id = isset($filter['provider_id'])?$filter['provider_id']:'';
                $purchase_code = isset($filter['purchase_code'])?$filter['purchase_code']:'';
                $batch_code = isset($filter['batch_code'])?$filter['batch_code']:'';
		$admin_name = isset($filter['admin_name'])?$filter['admin_name']:'';
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';

		if(empty($starttime) && empty($endtime))
		{
			return array();
		}

		$whereadd = "";
		if(!empty($starttime) && !empty($endtime))
		{
                    $whereadd .= "AND pm.create_date >= '".$starttime."' AND pm.create_date <= '".$endtime."'";
		}
		
                if(!empty($provider_id)) $whereadd .= " AND pm.purchase_provider = '".$provider_id."'";
		if(!empty($purchase_code)) $whereadd .= " AND pm.purchase_code = '".$purchase_code."'";
		if(!empty($admin_name)) $whereadd .= " AND pm.create_admin = '".$admin_name."'";
                if(!empty($batch_code)) $whereadd .= " AND pb.batch_code = '".$batch_code."'";

		//获取订单明细
		$sql = "SELECT pp.provider_code, pm.purchase_code,pm.create_date, pb.batch_code, pm.purchase_amount, pm.purchase_number, pm.purchase_finished_number, ai.realname 
FROM `ty_purchase_main` pm 
INNER JOIN ty_product_provider pp ON pm.purchase_provider = pp.provider_id 
INNER JOIN `ty_purchase_batch` pb ON pm.batch_id = pb.batch_id 
INNER JOIN `ty_admin_info` ai ON ai.admin_id = pm.create_admin 
WHERE pm.purchase_check_admin > 0 AND pm.purchase_finished_number > 0 ".$whereadd;
		$query = $this->db->query($sql);
		$result = $query->result();
		return array('list'=>$result);
	}

	public function purchase_main_detail_report ($filter)
	{
		$provider_id = isset($filter['provider_id'])?$filter['provider_id']:'';
                $brand_id = isset($filter['brand_id'])?$filter['brand_id']:'';
                $product_sn = isset($filter['product_sn'])?$filter['product_sn']:'';
                $product_name = isset($filter['product_name'])?$filter['product_name']:'';
                $purchase_code = isset($filter['purchase_code'])?$filter['purchase_code']:'';
                $batch_code = isset($filter['batch_code'])?$filter['batch_code']:'';
		$admin_name = isset($filter['admin_name'])?$filter['admin_name']:'';               
                $medical1 = isset($filter['medical1'])?$filter['medical1']:'';
                $product_cess = isset($filter['product_cess'])?$filter['product_cess']:'';                
		$starttime = isset($filter['start_time'])?$filter['start_time']:'';
		$endtime = isset($filter['end_time'])?$filter['end_time']:'';               
                $r_starttime = isset($filter['r_start_time'])?$filter['r_start_time']:'';
		$r_endtime = isset($filter['r_end_time'])?$filter['r_end_time']:'';

		if(empty($starttime) && empty($endtime))
		{
                    return array();
		}

		$whereadd = "";
		if(!empty($starttime) && !empty($endtime))
		{
                    $whereadd .= "AND pm.create_date >= '".$starttime."' AND pm.create_date <= '".$endtime."'";
		}
                
                if (!empty($r_starttime)){
                    $whereadd .= " AND a.depot_in_date >= '".$r_starttime."'";
                }
                
                if (!empty($r_endtime)){
                    $whereadd .= " AND a.depot_in_date <= '".$r_endtime."'";
                }
		
                if(!empty($provider_id)) $whereadd .= " AND pm.purchase_provider = '".$provider_id."'";
                if(!empty($brand_id)) $whereadd .= " AND pm.purchase_brand = '".$brand_id."'";
		if(!empty($purchase_code)) $whereadd .= " AND pm.purchase_code = '".$purchase_code."'";
		if(!empty($admin_name)) $whereadd .= " AND pm.create_admin = '".$admin_name."'";
                if(!empty($batch_code)) $whereadd .= " AND pb.batch_code = '".$batch_code."'";
                if(!empty($product_sn)) $whereadd .= " AND g.product_sn = '".$product_sn."'";
                if(!empty($product_name)) $whereadd .= " AND g.product_name LIKE '%".$product_name."%'";
                if(!empty($medical1)) $whereadd .= " AND rc.medical1 = '".$medical1."'";
                if(!empty($product_cess)) $whereadd .= " AND pc.product_cess = '".$product_cess."'";

		//获取订单明细
		$sql = "SELECT pm.purchase_code, pm.create_date, pp.provider_code, pb.batch_code, b.brand_name, s.size_name, 
                    rc.medical1, g.product_name, g.product_sn, pc.product_cess, pc.consign_price, ps.product_number, 
                    (pc.consign_price * ps.product_number) amount, ps.product_finished_number, ai.realname, a.depot_in_date 
                    FROM `ty_purchase_main` pm 
                    INNER JOIN `ty_purchase_sub` ps ON pm.purchase_id = ps.purchase_id 
                    INNER JOIN `ty_product_provider` pp ON pm.purchase_provider = pp.provider_id 
                    INNER JOIN `ty_purchase_batch` pb ON pm.batch_id = pb.batch_id 
                    INNER JOIN `ty_product_brand` b ON b.brand_id = pm.purchase_brand 
                    INNER JOIN `ty_product_size` s ON ps.size_id = s.size_id 
                    INNER JOIN ty_product_info g ON ps.product_id = g.product_id 
                    INNER JOIN `ty_product_cost` pc ON pm.batch_id = pc.batch_id AND ps.product_id = pc.product_id 
                    INNER JOIN `ty_register_code` rc ON g.register_code_id = rc.id 
                    INNER JOIN `ty_admin_info` ai ON ai.admin_id = pm.create_admin 
                    INNER JOIN (SELECT MAX(dim.depot_in_date) AS depot_in_date, dim.order_sn FROM `ty_depot_in_main` dim WHERE dim.depot_in_type = 11 GROUP BY dim.order_sn ) a ON pm.purchase_code = a.order_sn 
                    WHERE pm.purchase_check_admin > 0 AND pm.purchase_finished_number > 0 ".$whereadd;
		$query = $this->db->query($sql);
		$result = $query->result();
		return array('list'=>$result);
	}        
}

?>
