<?php
/**
* Liuyan_model
*/
class Liuyan_model extends CI_Model
{	
	private $_db;
	function __construct (&$db = NULL)
	{
		parent::__construct();
		$this->_db = $db ? $db : $this->db;
	}

	public function insert($data)
	{
		$this->_db->insert('product_liuyan',$data);
		return $this->_db->insert_id();
	}

	public function delete($cid) {	
		//该项为逻辑删除			
		return $this->_db->update('product_liuyan', array('is_del'=>1), "comment_id = $cid");
	}

	public function newest_liuyan($filter)
	{
		$where='';
		if(!empty($filter['category_id'])){
			$where.=" AND (p.category_id='{$filter['category_id']}' OR c.parent_id='{$filter['category_id']}') ";
		}
		if(!empty($filter['brand_id'])){
			$where.=" AND p.brand_id='{$filter['brand_id']}' ";
		}
		if(!empty($filter['kw'])){
			$kw=$this->_db->escape_like_str($filter['kw']);
			$where.=" AND (p.product_name LIKE '%{$kw}%' OR p.product_sn LIKE '%{$kw}%' OR b.brand_name LIKE '%{$kw}%' OR c.category_name LIKE '%{$kw}%') ";
		}
		$sql = "SELECT l.tag_id,l.comment_content,l.user_name AS admin_user_name, u.user_name, g.img_58_58 AS img_40_53
				FROM ty_product_liuyan AS l
				LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON l.tag_id=p.product_id
				LEFT JOIN ".$this->_db->dbprefix('product_category')." AS c ON p.category_id=c.category_id
				LEFT JOIN ".$this->_db->dbprefix('product_brand')." AS b ON p.brand_id=b.brand_id
				LEFT JOIN ".$this->_db->dbprefix('user_info')." AS u ON l.user_id=u.user_id
				LEFT JOIN ".$this->_db->dbprefix('product_gallery')." AS g ON l.tag_id=g.product_id AND g.image_type='default'
				WHERE l.tag_type=1 AND l.comment_type=2
				AND l.is_audit=1 AND l.is_del=0
				AND EXISTS(SELECT 1 FROM ".$this->_db->dbprefix('product_sub')." AS sub WHERE l.tag_id=sub.product_id AND sub.is_on_sale=1 AND (sub.consign_num=-2 OR sub.consign_num>0 OR sub.gl_num>sub.wait_num))
				{$where}
				GROUP BY l.tag_id
				ORDER BY l.comment_date DESC LIMIT 10";
		$query = $this->_db->query($sql);
		return $query->result();
	}

	public function liuyan_list($filter)
	{
		$filter['page_size'] = 5;
		$filter['record_count'] = 0;
		$filter['page_count']=0;	
		
		$select = "SELECT l.user_name AS admin_user_name,l.comment_id,l.comment_title, l.at_comment_id,
				l.comment_content,l.reply_content,l.comment_date,l.reply_date,l.weight,
				l.height,l.suitable,l.user_id,u.user_name,u.rank_id,u.user_advar, r.rank_name,s.size_name";
		$from = "FROM ".$this->_db->dbprefix('product_liuyan')." AS l
				LEFT JOIN ".$this->_db->dbprefix('user_info')." AS u ON u.user_id = l.user_id
				LEFT JOIN ".$this->_db->dbprefix('user_rank')." AS r ON u.rank_id=r.rank_id
				LEFT JOIN ".$this->_db->dbprefix('product_size')." AS s ON s.size_id = l.size_id
				";
		$where = "WHERE l.is_del=0";

		if(!empty($filter['comment_type'])) $where.=" AND l.comment_type='{$filter['comment_type']}' ";
		if(!empty($filter['tag_type'])) $where.=" AND l.tag_type='{$filter['tag_type']}' ";
		if(!empty($filter['tag_id'])) $where.=" AND l.tag_id='{$filter['tag_id']}' ";
		if(!empty($filter['user_id'])) $where.=" AND l.user_id='{$filter['user_id']}' ";

		$sort = " ORDER BY comment_id DESC ";
		
		$sql = "SELECT count(*) as ct {$from} {$where} {$sort}";
		$query = $this->_db->query($sql);
		$row =  $query->row();
		if(!$row) return array('filter'=>$filter,'list'=>array());
		$filter['record_count'] = $row->ct;
		$filter['page_count']=ceil($filter['record_count']/$filter['page_size']);
		$filter['page'] = max(min($filter['page'],$filter['page_count']-1),0);
		$start=$filter['page']*$filter['page_size']; //开始条数
       	//$sql = "{$select} {$from} {$where} {$sort} LIMIT {$start},{$filter['page_size']}";
       	$sql = "{$select} {$from} {$where} {$sort}";
       	$query = $this->_db->query($sql);
       	
       	$list = $query->result();
       	$where2 = $where . ' and l.comment_id = ? ';
       	$sql_find_at = "{$select} {$from} {$where2} {$sort}";
       	foreach ($list as $k => &$v) {
       		if ($v->at_comment_id) {
       			$query = $this->_db->query($sql_find_at, array($v->at_comment_id));
       			$comment = $query->result();
       			if (count($comment)) {
       				$v->at_comment = $comment[0];
       			} else {
       				$v->at_comment = false;
       			}
       			
       		} else {
       			$v->at_comment = false;
       		}
       	}
       	//exit(json_encode($list));
       	return array('filter'=>$filter,'list'=>$list);
	}

	public function my_discussions($filter) {
		/*连表查询
			select a.comment_content, b.comment_content from ty_product_liuyan a
			inner JOIN ty_product_liuyan b 
			on a.comment_id = b.at_comment_id
			where a.user_id = 18 
		*/

		$filter['page_size'] = 5;
		$filter['record_count'] = 0;
		$filter['page_count']=0;	
		
		$select = "SELECT l.user_name AS admin_user_name,l.comment_id,l.comment_title, l.at_comment_id,p.product_name,p.product_id,p.genre_id,
				l.comment_content,l.reply_content,l.comment_date,l.reply_date,l.weight,
				l.height,l.suitable,l.user_id,u.user_name,u.rank_id,u.user_advar, r.rank_name,s.size_name";
		$from = "FROM ".$this->_db->dbprefix('product_liuyan')." AS l
				LEFT JOIN ".$this->_db->dbprefix('user_info')." AS u ON u.user_id = l.user_id
				LEFT JOIN ".$this->_db->dbprefix('user_rank')." AS r ON u.rank_id=r.rank_id
				LEFT JOIN ".$this->_db->dbprefix('product_size')." AS s ON s.size_id = l.size_id
				LEFT JOIN ".$this->_db->dbprefix('product_info')." AS p ON p.product_id=l.tag_id
				";
		$where = "WHERE  l.is_audit=1 AND l.is_del=0";

		if(!empty($filter['comment_type'])) $where.=" AND l.comment_type='{$filter['comment_type']}' ";
		if(!empty($filter['tag_type'])) $where.=" AND l.tag_type='{$filter['tag_type']}' ";
		if(!empty($filter['tag_id'])) $where.=" AND l.tag_id='{$filter['tag_id']}' ";
		
		
		if(!empty($filter['response']) && !empty($filter['user_id'])) {
			$where2 = $where;
			$where.=" AND l.at_comment_id in (select comment_id from ty_product_liuyan where user_id = '{$filter['user_id']}') ";
		} else {
			if(!empty($filter['user_id'])) $where.=" AND l.user_id='{$filter['user_id']}' ";	
			$where2 = $where;
		}
			

		$sort = " ORDER BY comment_id DESC ";
		
		$sql = "SELECT count(*) as ct {$from} {$where} {$sort}";
		$query = $this->_db->query($sql);
		$row =  $query->row();
		if(!$row) return array('filter'=>$filter,'list'=>array());
		$filter['record_count'] = $row->ct;
		$filter['page_count']=ceil($filter['record_count']/$filter['page_size']);
		$filter['page'] = max(min($filter['page'],$filter['page_count']-1),0);
		$start=$filter['page']*$filter['page_size']; //开始条数
       	//$sql = "{$select} {$from} {$where} {$sort} LIMIT {$start},{$filter['page_size']}";
       	$sql = "{$select} {$from} {$where} {$sort}";
       	$query = $this->_db->query($sql);
       	
       	$list = $query->result();
       	$where2 = $where2 . ' and l.comment_id = ? ';
       	$sql_find_at = "{$select} {$from} {$where2} {$sort}";
       	
       	foreach ($list as $k => &$v) {
       		$v->product_name2 = cutstr($v->product_name, 0, 15);
       		$v->comment_content2 = cutstr($v->comment_content, 0, 15);
       		if ($v->genre_id == 2) {
       			$v->link = 'product-' . $v->product_id;
       		}
       		if ($v->genre_id == 2) {
       			$v->link = 'product-' . $v->product_id;
       		}
       		switch ($v->genre_id) {
       			case 2:
       				//课程
       				$v->link = '/product-' . $v->product_id;
       				break;
       			case 1:
       				$v->link = '/pdetail-' . $v->product_id;
       				break;

       			default:
       				$v->link = '';
       				break;
       		}

       		if ($v->at_comment_id) {
       			$query = $this->_db->query($sql_find_at, array($v->at_comment_id));
       			$comment = $query->result();
       			if (count($comment)) {
       				$v->at_comment = $comment[0];
       			} else {
       				$v->at_comment = false;
       			}
       			
       		} else {
       			$v->at_comment = false;
       		}
       	}
       	
       	return array('filter'=>$filter,'list'=>$list);

	}
}