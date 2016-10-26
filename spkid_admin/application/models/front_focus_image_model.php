<?php
/**
 * 首页焦点图model类
 */
class front_focus_image_model extends CI_Model
{
    public function all($filter)
    {
        // $sql="select * from ty_front_focus_image where focus_type=$filter[focus_type] and (end_time >now() or end_time is null)
        //         order by focus_order desc limit 40";
        $sql="select * from ty_front_focus_image where focus_type=$filter[focus_type] order by focus_order desc limit 40";
        $query=$this->db_r->query($sql);
        return $query->result();
    }

    public function filter($filter)
    {
        $this->db_r->order_by('focus_order asc');
        $query=$this->db_r->get_where('front_focus_image',$filter);
        return $query->result();
    }

    public function insert($data)
    {
        $this->db->insert('front_focus_image',$data);
        return $this->db->insert_id();
    }

    public function update($data,$id)
    {
        $this->db->update('front_focus_image',$data,array('id'=>$id));
    }
    
    public function delete($id)
    {
        $this->db->delete('front_focus_image',array('id'=>$id));
    }
}
?>
