<?
/**
 * rush_notice model
 */
class Rush_notice_model extends CI_Model
{
    function __construct ()
	{
		parent::__construct();
	}

    function insert($data)
    {
        $this->db->insert('rush_notice',$data);
        return $this->db->insert_id();
    }

    function filter($filter)
    {
        $query=$this->db_r->get_where('rush_notice',$filter);
        return $query->result();
    }
    
    function delete($filter)
    {
        $this->db->delete('rush_notice',array('rush_id'=>$filter['rush_id'],'account'=>$filter['account']));
    }
}
?>
