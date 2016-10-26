<?php

class Wordpress_model extends CI_Model {
    public function get_article_views($id)
    {
        $sql = "SELECT meta_value from wp_postmeta WHERE meta_key='views' and post_id = ".$id;
        $res = $this->db_wp->query($sql)->first_row();
        $pv_num = $res->meta_value;
        return $pv_num;
    }
    public function fetch_courses(){
        $sql = "SELECT p.post_title, GROUP_CONCAT(pm.meta_key, '=', pm.`meta_value` SEPARATOR '&') meta FROM wp_posts p LEFT JOIN wp_postmeta pm ON p.`ID` = pm.`post_id` LEFT JOIN wp_term_relationships tr ON tr.`object_id` = p.`ID` WHERE tr.term_taxonomy_id IN (2, 6) AND pm.`meta_key` IN ( 'signup_start_time', 'signup_end_time', 'speaker', 'place') GROUP BY p.`ID`";
        $res = $this->db_wp->query($sql);
        $course_list = $res->result();
        $courses = array();
        foreach($course_list as $course){
            $meta = $course->meta;
            parse_str($meta, $course_meta);
            $courses[] = array('post_title' => $course->post_title, 'meta' => $course_meta);
            //array_push($courses, $course);
        }
        return $courses;
    }
    
    //更新文章访问量
    public function wordpress_num_update($id,$pv)
    {
        $sql = "UPDATE wp_postmeta SET meta_value = meta_value+".$pv." WHERE meta_key='views' and post_id = ".$id;
        $res = $this->db_wp->query($sql);
        return $res;
    }

    //获取文章名称
    public function wordpress_sn_name($id){
        $sql = "SELECT post_title FROM wp_posts WHERE ID=".$id;
        $title = $this->db_wp->query($sql)->row();
        return $title;
    }
}
