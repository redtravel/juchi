<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Lib_ad{

   function __construct(){
       $this->CI=&get_instance();
       $this->load=$this->CI->load;
   }

	/**
     * 根据cache_key和position_tag取广告
     * 优先查询memcache
     * 如果确实没有数据 则进行过滤不重复读取库
     */
   function get_ad_by_position_tag($cache_key,$position_tag,$size=0){
      
       $ad=$this->CI->cache->get($cache_key);
       if($ad=='no_db')return array();
       if(empty($ad))
       {
           $this->load->model('ad_model');
           $ad=$this->CI->ad_model->get_ad_by_position_tag($position_tag,$size);
           //如果没有广告位 则设置个标识 防止一直读取库
           if(empty($ad))
           {
               $this->CI->cache->save($cache_key,'no_db');
               return array();
           }
           $this->CI->cache->save($cache_key,$ad);
       }
       foreach($ad as $key=>$item)
       {
           //不符合时间的unset掉
           if($item->start_date>date('Y-m-d H:i:s')||$item->end_date<date('Y-m-d H:i:s'))
           {
               unset($ad[$key]);
           }
       }
       return $ad;
   }
   
   function get_focus_image($cache_key, $type = 1){
       $is_preview = isset($_GET['is_preview']) && $_GET['is_preview']== 1 ?TRUE:FALSE;
       if ($is_preview){
           $img_arr = false;
       } else {      
           $img_arr = $this->CI->cache->get($cache_key);
       }

       if (!$img_arr) {
           $this->load->model('ad_model');
           $img_arr = $this->CI->ad_model->get_focus_image($type);
           if (empty($img_arr)) {
               return array();
           }
           $this->CI->cache->save($cache_key, $img_arr,CACHE_TIME_INDEX_FOCUS_IMAGE);
       }

       return $img_arr;      
   }
}
?>
