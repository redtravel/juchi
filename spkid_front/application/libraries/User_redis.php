<?php
include_once 'Lib_redis_id.php';
class user_redis extends Lib_redis_id {
    
    var $keep_size = 10;//最少保持10个数据
    var $data_max_size = 100;//最大100个数据
    
    public function __construct()
    {
        $this->CI = & get_instance();
        parent::__construct();
    }
    //先获取列表中有多少个数据，低于最少数量补充数据，然后返回数据
    public function get_user_id(){
        $data_size = parent::get_size();
        if ($data_size <= $this->keep_size){
            $this->CI->load->model('user_model');
            $user_id = $this->CI->user_model->get_last_user_id();
            $start = $user_id+$data_size+1;
            $end = $this->data_max_size-$data_size+$user_id;
            $data_arr = array();
            for($i = $start; $i <= $end; $i++){
                $data_arr[] = $i;
            }
            parent::set_queue_id($data_arr);
        }
        return parent::get_next_autoincrement();
    }
}