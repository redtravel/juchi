<?php

/**
* Apply_return_model
*/
class Apply_return_model extends CI_Model
{
	private $_db;
	function __construct (&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}
        
        /**
        * 分页查询退货申请单
        */
       function get_apply_return_info_list($filter) {
           $where = " where ar.user_id=$filter[user_id] ";
           if (!isset($filter['apply_status']))
           {
                    $filter['apply_status'] = 1;
           }
           switch ($filter['apply_status'])
           {
                    case '1' : //所有申请单
                            break;

                    case '2' : //待处理
                            $where .= " AND ar.apply_status = '0' ";
                            break;

                    case '3' : //处理中
                            $where .= " AND ar.apply_status = '1' ";
                            break;

                    case '4' : //已处理
                            $where .= " AND ar.apply_status = '2' ";
                            break;
                    case '5' : //已取消
                            $where .= " AND ar.apply_status = '3' ";
                            break;
                    default:
                            break;
           }
           
           $result = $this->_db->query("select count(1) as recordCount from ty_apply_return_info as ar $where")->row_array();
           $filter['record_count'] = $result["recordCount"];
           $filter['sort_by'] = 'ar.apply_time';
           $filter['sort_order'] = 'DESC';
           $filter = page_and_size($filter);
           if ($filter['record_count'] <= 0)
            {
                    return array('list' => array(), 'filter' => $filter);
            }
           $sql = "select apply_id,oi.order_sn,ar.shipping_name,ar.invoice_no,apply_status,ar.provider_status
                       from ty_apply_return_info ar
                       left join ty_order_info as oi 
                       on ar.order_id=oi.order_id
                       $where
                       ORDER BY $filter[sort_by] $filter[sort_order] LIMIT " .
                       ($filter['page'] - 1) * $filter["page_size"] . ",$filter[page_size]";
           $result = $this->_db->query($sql)->result_array();
           
           foreach($result as $key=>$apply_return_info){
                if($apply_return_info['apply_status']==0&&$apply_return_info['provider_status']==0){
                    $result[$key]['can_cancel']=true;
                    $result[$key]['can_modify']=true;
                }
                else{
                    $result[$key]['can_cancel']=false;
                    $result[$key]['can_modify']=false;
                }
                $result[$key]['apply_status']=$this->convert_apply_return_status($apply_return_info['apply_status']);
            }
            return array('list' => $result, 'filter' => $filter);
       }
       
       /**
        * 更改退货申请单状态值
        */
       function convert_apply_return_status($apply_status){
           if($apply_status==0) $apply_status='待处理';
           else if($apply_status==1) $apply_status='处理中';
           else if($apply_status==2||$apply_status==4) $apply_status='已处理';
           else if($apply_status==3) $apply_status='已取消';
           return $apply_status;
       }
        
        /**
        * 添加退货申请单
        */
       function add_apply_return_info($apply_return_info,$apply_return_product){
           $this->_db->trans_begin();
           $this->_db->insert('ty_apply_return_info',$apply_return_info);
           $apply_id=$this->_db->insert_id();
           if(empty($apply_id)){
               $this->_db->tran_rollback();
               return false;
           }
           foreach($apply_return_product as $product){
               $product['apply_id']=$apply_id;
               $this->_db->insert('ty_apply_return_product',$product);
           }
           $this->_db->trans_commit();
       }
       
       function add_apply_return_product($apply_return_product,$apply_id){
            //删除原商品信息
            $sql='delete from ty_apply_return_product where apply_id=?';
            $this->_db->query($sql,array($apply_id));
            foreach($apply_return_product as $product){
                $this->_db->insert('ty_apply_return_product',$product);
            }
        }
       
       /**
        * 更新申请单信息
        */
       function update_apply_return_info($apply_return_info,$apply_return_product_arr,$apply_id,$del_product_img=array()){
            $this->_db->update('ty_apply_return_info',$apply_return_info,array('apply_id'=>$apply_id));
            $applyed_return_product=$this->get_apply_return_product_by_apply_id($apply_id);
            $temp_arr=array();
            foreach($apply_return_product_arr as $product){
                $product['apply_id']=$apply_id;
                $temp_arr[$product['product_id'].'_'.$product['color_id'].'_'.$product['size_id']]=$product;
            }
            $apply_return_product_arr=$temp_arr;
            foreach($applyed_return_product as $key=>$product){
                if(isset($apply_return_product_arr[$key])){
                    if(!empty($product['img'])){
                        //检查是否有删除图片
                        $product_img_arr=explode(';',$product['img']); //原存储的图片
                        foreach($product_img_arr as $img_key=>$img1){
                            $temp_del_product_img=$del_product_img[$key]; //提交过来的要删除的图片
                            foreach($temp_del_product_img as $del_img){
                                if($img1==$del_img){
                                    unset($product_img_arr[$img_key]);
                                    break;
                                }
                            }
                        }
                        if(count($product_img_arr)>0){
                            $temp_img=$apply_return_product_arr[$key]['img'];//新提交的图片
                            $temp_img_arr=explode(';',$temp_img);
                            $apply_return_product_arr[$key]['img']=implode(';',$product_img_arr);
                            //最多允许5张图片
                            for($i=0;$i<5-count($product_img_arr);$i++){
                                if(!empty($temp_img_arr[$i])){
                                    $apply_return_product_arr[$key]['img'].=';'.$temp_img_arr[$i];
                                }
                            }
                        }
                    }
                }
            }
            $this->add_apply_return_product($apply_return_product_arr,$apply_id);
            //删除图片
            foreach($del_product_img as $imgs){
                foreach($imgs as $img){
                    unlink('./public/applyreturn/'.substr($img,17));
                }
            }
        }
       
       function get_apply_return_product_by_apply_id($apply_id){
            $sql='select arp.* 
                    from ty_apply_return_product arp
                    where arp.apply_id=?';
            $apply_return_product = $this->_db->query($sql,array($apply_id))->result_array();
            $return_arr=array();
            foreach($apply_return_product as $product){
                $return_arr[$product['product_id'].'_'.$product['color_id'].'_'.$product['size_id']]=$product;
            }
            return $return_arr;
        }
        
        /**
        * 根据订单id查询已退货的商品
        */
       function get_apply_return_product_by_order_id($order_id,$apply_id=0){
           $return_arr=array();
           // 取出退货单商品
           $sql='select orp.product_id, orp.color_id, orp.size_id, orp.product_num as product_number
                 from ty_order_return_product orp
                 inner join ty_order_return_info ori
                 on orp.return_id = ori.return_id
                 where ori.order_id = ? and return_status <> 4';
           $has_return_product = $this->_db->query($sql,array($order_id))->result_array();
           foreach($has_return_product as $product){
                $sku=$product['product_id'].'_'.$product['color_id'].'_'.$product['size_id'];
                if(isset($return_arr[$sku]))
                    $return_arr[$sku]['product_number']+=$product['product_number'];
                else
                    $return_arr[$sku]=$product;
            }
            // 取出申请退货单商品
           $sql='select  arp.product_id, arp.color_id, arp.size_id, arp.product_number
                 from ty_apply_return_product arp
                 inner join ty_apply_return_info ari
                 on arp.apply_id=ari.apply_id and ari.order_id=?
                 where ari.apply_id<>? and apply_status = 0';
           $has_return_product = $this->_db->query($sql,array($order_id,$apply_id))->result_array();
           foreach($has_return_product as $product){
                $sku=$product['product_id'].'_'.$product['color_id'].'_'.$product['size_id'];
                if(isset($return_arr[$sku]))
                    $return_arr[$sku]['product_number']+=$product['product_number'];
                else
                    $return_arr[$sku]=$product;
            }
            return $return_arr;
       }
       
       /**
        * 根据apply_id查询退货申请单信息
        */
       function get_apply_return_info_by_apply_id($apply_id,$user_id){
           $sql='select ari.*
                  from ty_apply_return_info ari
                  where apply_id=? and user_id=?';
           return $this->_db->query($sql,array($apply_id,$user_id))->row_array();
       }
       
        /**
        * 获取退货申请单商品信息
        */
       function get_apply_return_product($apply_id,$user_id){
           $sql="select arp.*,ari.apply_status,arp.apply_id,ari.apply_time,
                    c.color_name,s.size_name,gg.img_url
                  from ty_apply_return_product arp
                  left join ty_apply_return_info ari on arp.apply_id=ari.apply_id
                  left join ty_product_color c on arp.color_id=c.color_id
                  left join ty_product_size s on arp.size_id=s.size_id
                  left join ty_product_gallery as gg on arp.product_id=gg.product_id and  arp.color_id=gg.color_id and gg.image_type='default'
                  where user_id=? and arp.apply_id=?
                ";
           $apply_return_product = $this->_db->query($sql,array($user_id,$apply_id))->result_array();
           eval(APPLY_RETURN_REASON);
           foreach($apply_return_product as $key=>$product){
               $apply_return_product[$key]['apply_status']=$this->convert_apply_return_status($product['apply_status']);
               $apply_return_product[$key]['imgs']=array();
               if(!empty($product['img'])){
                   $apply_return_product[$key]['imgs']=explode(';',$product['img']);
               }
               $apply_return_product[$key]['return_reason_desc']=$return_reason_arr[$product['return_reason']];
           }
           return $apply_return_product;
       }
       
       /**
        * 根据apply_id查询已经退货入库的商品
        */
       function get_apply_return_transaction($apply_id){
           $sql='select rp.product_id,rp.color_id,rp.size_id,rp.product_number,ri.is_ok_date,
                c.color_name,s.size_name
                from ty_apply_return_product rp  
                left join ty_product_color c on rp.color_id=c.color_id 
                left join ty_product_size s on rp.size_id=s.size_id 
                left join ty_order_return_info ri on rp.apply_id=ri.apply_id 
                where ri.is_ok=1 and ri.return_status<>4  and ri.apply_id=?
                order by ri.is_ok_date 
                ';
           $returned_product = $this->db_r->query($sql,array($apply_id))->result_array();
           $return_arr=array();
           foreach($returned_product as $product){
               $product['is_ok_date']=date('Y-m-d',strtotime($product['is_ok_date']));
               $return_arr[$product['product_id'].'_'.$product['color_id'].'_'.$product['size_id']]=$product;
           }
           return $return_arr;
       }
       
       function get_apply_return_ok_date($apply_id){
            $sql='select max(is_ok_date) is_ok_date
                    from ty_order_return_info ri
                    left join ty_apply_return_info ari
                    on ri.apply_id=ari.apply_id
                    where ari.apply_status=2 and ri.apply_id=?
                 ';
            $result = $this->db_r->query($sql,array($apply_id))->row_array();
            $is_ok_date='';
            if(!empty($result['is_ok_date'])){
                $is_ok_date=date('Y-m-d', strtotime($result['is_ok_date']));
            }
            return $is_ok_date;
        }
       
       /**
        * 取消申请
        * 待处理的申请单可取消
        */
       function cancel_apply_return_info($apply_id,$user_id){
           $sql='update ty_apply_return_info set apply_status=3,cancel_time=? 
               where apply_id=? and user_id=? and apply_status=0';
           $this->_db->query($sql,array(date('Y-m-d H:i:s'),$apply_id,$user_id));
           return $this->_db->affected_rows();
       }
}
