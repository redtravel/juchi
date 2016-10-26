<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Subject_Model extends CI_Model {
    
    /* ---- subject --------------------------------------------------------- */
    public function get_subject($filter) {
        $query = $this->db->get_where('subject', $filter, 1);
        return $query->row();
    }
        
    public function subject_list($filter) {
        $from = " FROM ".$this->db->dbprefix('subject')." AS s ";
        $where = " WHERE 1 ";
        
        if (!empty($filter['start_date'])) {
            $where .= " AND s.create_date >= '" . $filter['start_date'] . "'";
        }
        if (!empty($filter['end_date'])) {
            $where .= " AND s.create_date <= '" . $filter['end_date'] . "'";
        }
        if (!empty($filter['subject_title'])) {
            $where .= " AND s.subject_title LIKE '%" . $filter['subject_title'] . "%'";
        }
        
        $filter['sort_by'] = empty($filter['sort_by']) ? 's.subject_id' : trim($filter['sort_by']);
        $filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);
        
        // count
        $sql = "SELECT COUNT(1) AS ct " . $from . $where;
        $query = $this->db->query($sql);
        $row = $query->row();
        $query->free_result();
        
        $filter['record_count'] = (int) $row->ct;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
                return array('list' => array(), 'filter' => $filter);
        }
        
        // query
        $sql = "SELECT s.* ". $from . $where 
            . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
            . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
	$query = $this->db->query($sql);
        
        $list = $query->result();
        $query->free_result();
        return array('list' => $list, 'filter' => $filter);
    }
    
    public function insert_subject ($data) {
        $this->db->insert('subject', $data);
        return $this->db->insert_id();
    }
    
    public function update_subject ($data, $subject_id) {
        $this->db->update('subject', $data, array('subject_id' => $subject_id));
    }
    
    public function delete_subject ($subject_id) {
        $this->db->delete('subject', array('subject_id' => $subject_id));
    }
    
    /* ---- subject_module -------------------------------------------------- */
    public function get_module($filter) {
        $query = $this->db->get_where('subject_module', $filter, 1);
        return $query->row();
    }
    
    public function subject_modules($subject_id) {
        $sql = "SELECT * FROM ".$this->db->dbprefix('subject_module').
                " WHERE subject_id = ".$subject_id." ORDER BY sort_order DESC";
        $query = $this->db->query($sql);
        $list = $query->result();
        $query->free_result();
	return $list;
    }

    public function insert_module ($data) {
        $this->db->insert('subject_module', $data);
        return $this->db->insert_id();
    }
    
    public function update_module ($data, $module_id) {
        $this->db->update('subject_module', $data, array('module_id' => $module_id));
    }
    
    public function delete_module ($module_id) {
        $this->db->delete('subject_module', array('module_id' => $module_id));
    }
    
    public function delete_modules ($subject_id) {
        $this->db->delete('subject_module', array('subject_id' => $subject_id));
    }
    
    /* ---- module operations ----------------------------------------------- */
    public function search_goods($filter) {
        $from = " FROM ".$this->db->dbprefix('product_info')." AS p" .
                " LEFT JOIN ".$this->db->dbprefix('product_sub'). " AS ps ON ps.product_id = p.product_id " .
                " LEFT JOIN ".$this->db->dbprefix('product_category')." AS c ON p.category_id = c.category_id ".
                " LEFT JOIN ".$this->db->dbprefix('product_category')." AS pc ON c.parent_id = pc.category_id" ;

        $where = " WHERE ps.is_on_sale = 1 ";
        
        $param = array();
        if (!empty($filter['brand_id'])) {
            $where .= " AND p.brand_id = ? ";
            $param[] = $filter['brand_id'];
        }
        if (!empty($filter['category_id'])) {
            $where .= " AND (p.category_id = ? OR pc.category_id = ?) ";
            $param[] = $filter['category_id'];
            $param[] = $filter['category_id'];
        }
        if (!empty($filter['style_id'])) {
            $where .= " AND p.style_id = ? ";
            $param[] = $filter['style_id'];
        }
        if (!empty($filter['season_id'])) {
            $where .= " AND p.season_id = ? ";
            $param[] = $filter['season_id'];
        }
        if (!empty($filter['product_sex'])) {
            $where .= " AND p.product_sex = ? ";
            $param[] = $filter['product_sex'];
        }

        if (!empty($filter['product_sn'])) {
            $where .= " AND p.product_sn LIKE ? ";
            $param[] = '%' . $filter['product_sn'] . '%';
        }
        if (!empty($filter['batch_code'])) {
            $from .= " LEFT JOIN ".$this->db->dbprefix('product_cost')." AS os ON p.product_id = os.product_id ".
                     " LEFT JOIN ".$this->db->dbprefix('purchase_batch')." AS pb ON os.batch_id = pb.batch_id ";
            $where .= " AND pb.batch_code = ? ";
            $param[] = $filter['batch_code'];
        }
        if (!empty($filter['min_price'])) {
            $where .= " AND p.shop_price >= ? ";
            $param[] = $filter['min_price'];
        }
        if (!empty($filter['max_price'])) {
            $where .= " AND p.shop_price <= ? ";
            $param[] = $filter['max_price'];
        }
        
        $group_by = ' GROUP BY p.product_id ';
        $having = null;
        if (!empty($filter['min_gl_num']) || !empty($filter['max_gl_num'])) {
            $having .= ' HAVING ';
            if (!empty($filter['min_gl_num'])) {
                $having .= ' SUM(ps.gl_num - ps.wait_num) >= ? ';
                $param[] = $filter['min_gl_num'];
            }
            if (!empty($filter['max_gl_num'])) {
                if (!empty($filter['min_gl_num'])) {
                    $having .= ' AND ';
                }
                $having .= ' SUM(ps.gl_num - ps.wait_num) <= ? ';
                $param[] = $filter['max_gl_num'];
            }
        }

        $filter['sort_by'] = empty($filter['sort_by']) ? 'p.product_id' : trim($filter['sort_by']);
        $filter['sort_order'] = empty($filter['sort_order']) ? 'DESC' : trim($filter['sort_order']);

        $sql = "SELECT COUNT(*) AS ct FROM (SELECT p.product_id " .$from .$where .$group_by .$having .") AS originSql ";
        $query = $this->db->query($sql, $param);
        $row = $query->row();
        $query->free_result();
        
        $filter['record_count'] = (int) $row->ct;
        $filter = page_and_size($filter);
        if ($filter['record_count'] <= 0) {
            return array('list' => array(), 'filter' => $filter);
        }
        
        $sql = "SELECT c.category_name,p.category_id,p.product_id,p.product_sn,
                p.product_name,p.provider_productcode,p.market_price,p.shop_price "
                . $from . $where . $group_by .$having 
                . " ORDER BY " . $filter['sort_by'] . " " . $filter['sort_order']
                . " LIMIT " . ($filter['page'] - 1) * $filter['page_size'] . ", " . $filter['page_size'];
        $query = $this->db->query($sql, $param);
        $list = $query->result();
        $query->free_result();
        
        return array('list' => $list, 'filter' => $filter);
    }
}
?>