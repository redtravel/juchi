<?php
/**
 * 与wordpress DB操作
 * @date 20150916
 */
class Wordpress_model extends CI_Model {

    const FORMAT_VIDEO_TYPE = POST_FORMAT_VIDEO;
    //更新文章访问量
    public function get_article_views($id)
    {
        $sql = "SELECT meta_value AS pv_num from wp_postmeta WHERE meta_key='views' and post_id = ".$id." order by meta_id DESC limit 1";
        $res = $this->db_wp->query($sql)->first_row();
        return $res;
    }
    public function get_sibing_id($id, $con){
        
        if ('<' == $con){
            $direction = 'DESC';
        } else{
            $direction = 'ASC';
        }
        $con .= $id;
        $sql = "SELECT p.ID FROM wp_posts p LEFT JOIN wp_postmeta pm ON p.`ID`=pm.`post_id` WHERE p.ID $con AND p.post_type='post' AND p.post_status='publish' AND pm.`meta_key`='cover' ORDER BY p.ID $direction";
        $res = $this->db_wp->query($sql)->first_row();
        if (!empty($res)){
            return $res->ID;
        }
        return false;
    }
    public function get_relative_articles($cids, $id){
        $cids = implode(',', $cids);
        $sql = "SELECT distinct p.ID post_id,p.post_title,p.post_date,u.`display_name`, tr.term_taxonomy_id,pm2.`meta_value` 
FROM wp_posts p LEFT JOIN wp_term_relationships tr ON tr.`object_id` = p.ID 
LEFT JOIN wp_postmeta pm ON p.`ID`=pm.`post_id` 
LEFT JOIN wp_postmeta pm2 ON p.`ID`=pm2.`post_id` 
LEFT JOIN wp_users u ON u.ID=p.`post_author` 
WHERE p.post_type='post' AND p.post_status='publish' AND pm.`meta_key`='cover' AND pm2.`meta_key`='views' AND p.ID != $id  
ORDER BY FIELD(tr.term_taxonomy_id,$cids) DESC, CONVERT(pm2.`meta_value`,UNSIGNED) DESC 
limit 5";
        $rows = $this->db_wp->query($sql)->result();
        return $rows;
    }
    public function get_article_detail($id){
        if (!is_numeric($id))
        return false;
        $sql = "SELECT post_id, post_author, post_title, `post_content`, post_date, `display_name`, GROUP_CONCAT(`taxonomy`,'=',`name`,'=',term_id SEPARATOR '&') tags, cover FROM (SELECT p.ID post_id,p.post_author,p.post_title,p.`post_content`,p.post_date,u.`display_name`,tx.`taxonomy`,t.`name`,t.term_id,pm.`meta_value` cover FROM wp_posts p LEFT JOIN wp_term_relationships tr ON tr.`object_id` = p.`ID` LEFT JOIN wp_users u ON u.ID=p.`post_author` LEFT JOIN wp_term_taxonomy tx USING(term_taxonomy_id) LEFT JOIN wp_terms t ON tr.`term_taxonomy_id`=t.`term_id` LEFT JOIN wp_postmeta pm ON p.`ID`=pm.`post_id` AND pm.`meta_key`='cover' WHERE p.`ID`=$id AND p.post_type='post' AND
    p.post_status='publish' ORDER BY tx.`parent`) p";
        //echo $sql;

        $detail=$this->db_wp->query($sql)->first_row();
        
        //$arr=(array) $detail;
//        print_r($arr);
        if (empty($detail->post_id)){
            return false;
        }
        //$names = array($detail->meta_value => 'avatar', $detail->cover => 'cover');
        $detail->cover = $this->get_cover($detail->cover);
        $detail->post_date = current(explode(' ', $detail->post_date));
        //unset($detail->meta_value);
        //评论
        $sql = "SELECT a.comment_ID,a.comment_author,a.`comment_content`,b.comment_content parent_content,a.comment_date,a.yyw_user_id,b.yyw_user_id parent_id FROM wp_comments a LEFT JOIN wp_comments b ON b.`comment_ID`=a.`comment_parent` WHERE a.comment_post_ID=$id AND a.comment_approved = 1";
        $comments = $this->db_wp->query($sql)->result_array();
        $ids = array();
        foreach($comments as $comment){
            $user_id = $comment['yyw_user_id'];
            $parent_id = $comment['parent_id'];
            if ($user_id){
                array_push($ids, $user_id);
            }
            if ($parent_id){
                array_push($ids, $parent_id);
            }
            /*$response = explode('&', $comment['response']);
            $response1 = array();
            foreach($response as $r){
                $r = explode(',', $r);
                $user_id = $r[0];
                if ($user_id){
                    array_push($ids, $user_id);
                }
                $response1[] = array('user_id' => $user_id, 'content' => $r[1]);
            }
            $comment['response'] = $response1;*/
        }
        if (!empty($ids)){
            $user_ids = implode(',', $ids);
            $sql = "SELECT user_id,user_name,user_advar FROM ty_user_info WHERE user_id IN ($user_ids)";
            $res = $this->db->query($sql)->result();
            $ids = array();
            foreach($res as $r){
                $ids[$r->user_id] = array($r->user_advar, $r->user_name);
            }

        }
        foreach($comments as &$comment){
            $user_id = $comment['yyw_user_id'];
            if ($user_id){
                $comment['user_advar'] = $ids[$user_id][0];
                $comment['comment_author'] = $ids[$user_id][1];
            }
            unset($comment['yyw_user_id']);
            $parent_id = $comment['parent_id'];
            if ($parent_id){
                $comment['parent_author'] = $ids[$parent_id][1];
            }
            unset($comment['parent_id']);
           
        }
        $detail->comments = $comments;

        //作者发布文章数量
        $sql = 'SELECT COUNT(ID) articles_num FROM wp_posts WHERE `post_author` = '.$detail->post_author;
        $articles_num = $this->db_wp->query($sql)->row();
        $detail->user_articles_num = $articles_num->articles_num;
        
	// replace video
	$detail->post_content = $this->adjust_content($detail->post_content);
        
        return $detail; 
        
    }
    public function search_article($kw, $page = 1){
        $page_size = M_LIST_PAGE_SIZE;
        $page = ($page < 1) ? 1 : intval($page);
        $start = ($page-1)*$page_size;       
        
        // LIMIT $start, $page_size
        if (!$rows = $this->cache->get('keyword_'.$kw)){
        $sql = "SELECT p.ID id,p.post_title title,p.post_date,u.`display_name` author,pm.meta_value cover FROM wp_posts p LEFT JOIN wp_postmeta pm ON p.`ID`=pm.`post_id` AND pm.`meta_key`='cover' LEFT JOIN wp_users u ON u.ID=p.`post_author` WHERE p.post_type='post' AND p.post_status='publish' AND p.post_title like '%$kw%'";
        //echo $sql;
        $rows = $this->db_wp->query($sql)->result_array();
        //tag
        $sql = "SELECT p.ID id,p.post_title title,p.post_date,u.`display_name` author,pm.meta_value cover FROM wp_posts p LEFT JOIN wp_postmeta pm ON p.`ID`=pm.`post_id` AND pm.`meta_key`='cover' LEFT JOIN wp_users u ON u.ID=p.`post_author` LEFT JOIN wp_term_relationships tr ON tr.`object_id` = p.`ID` LEFT JOIN wp_terms t ON tr.`term_taxonomy_id`=t.`term_id` WHERE p.post_type='post' AND p.post_status='publish' AND t.`name`='$kw'";
        $rows1 = $this->db_wp->query($sql)->result_array();
        $rows = array_merge($rows, $rows1);
        
        foreach($rows as &$row){
            //echo $row['cover'];
            $row['cover'] = $this->get_cover($row['cover']);
        }

        $this->cache->save('keyword_'.$kw, $rows, 1800);
        }
        $result_rows = array();
        foreach ($rows as $key => $value) {
            $result_rows[$value['id']] = $value;
        }
        $result_rows = array_slice($result_rows, $start, $page_size, true);
        
        return $result_rows;
    }
    public function hot2new_videos($type,$limit = 10){
        if ('hot' == $type){
            $videos = array();
            // 周排行
            $date = date('Y-m-d 00:00', strtotime('-15 day'));
            $sql = "SELECT p.ID,p.post_title,pm.meta_value views FROM wp_posts p INNER JOIN wp_term_relationships tr0 ON tr0.`object_id` = p.`ID` AND tr0.term_taxonomy_id = " .Wordpress_model::FORMAT_VIDEO_TYPE . " LEFT JOIN wp_postmeta pm ON pm.`post_id`=p.`ID` WHERE p.post_type='post' AND p.post_status='publish' AND pm.meta_key='views' AND p.post_date > '$date' GROUP BY p.ID ORDER BY views*1000000 DESC LIMIT ".$limit;
            $videos['week'] = $this->db_wp->query($sql)->result_array();
            // 月排行
            $date = date('Y-m-d 00:00', strtotime('-60 day'));
            $sql = "SELECT p.ID,p.post_title,pm.meta_value views FROM wp_posts p INNER JOIN wp_term_relationships tr0 ON tr0.`object_id` = p.`ID` AND tr0.term_taxonomy_id = ". Wordpress_model::FORMAT_VIDEO_TYPE . " LEFT JOIN wp_postmeta pm ON pm.`post_id`=p.`ID` WHERE p.post_type='post' AND p.post_status='publish' AND pm.meta_key='views' AND p.post_date > '$date' GROUP BY p.ID ORDER BY views*1000000 DESC LIMIT ".$limit;
            $videos['month'] = $this->db_wp->query($sql)->result_array();
            
        } else {
            $sql = "SELECT p.ID,p.post_title FROM wp_posts p INNER JOIN wp_term_relationships tr0 ON tr0.`object_id` = p.`ID` AND tr0.term_taxonomy_id = ". Wordpress_model::FORMAT_VIDEO_TYPE . " WHERE p.post_type='post' AND p.post_status='publish' ORDER BY p.post_date DESC LIMIT ".$limit;
            $videos = $this->db_wp->query($sql)->result_array();
        }

        return $videos;
    }
    public function insert_video($data, $extra){
        $result = false;
        $date = date('Y-m-d H:i:s');
        date_default_timezone_set('UTC');
        $gmdate = date('Y-m-d H:i:s');
        $data['post_date'] = $date;
        $data['post_date_gmt'] = $gmdate;
        $data['comment_status'] = 'open';
        $data['ping_status'] = 'open';
        $data['post_status'] = 'pending';
		$this->db_wp->insert('wp_posts',$data);
		$id = $this->db_wp->insert_id();
        $sql = "INSERT INTO wp_term_relationships values($id, 1, 0), ($id, 10, 0)";
        $this->db_wp->query($sql);

        extract($extra);
        //$cover = $extra['cover'];
        $name = basename($cover);
        //$mime_type = mime_content_type($name);
        $data = array('post_title' => $name, 'post_author' => 1, 'post_status' => 'publish', 'post_type' => 'attachment', 'post_parent' => $id, 'post_mime_type' => $mime_type, 'guid' => $cover, 'post_date' => $date, 'post_date_gmt' => $gmdate);
		$this->db_wp->insert('wp_posts',$data);
		$aid = $this->db_wp->insert_id();

        $sql = "INSERT INTO wp_postmeta values(null, $id, 'intro', '$intro'), (null, $id, 'cover', $aid)";
        if ($this->db_wp->query($sql)){
            $result = true;
        }
        return $result;

    }
    public function fetch_videos($cid){
        //".POST_FORMAT_VIDEO."
        $sql = "SELECT p.ID,p.post_title,p.post_date,p.comment_count,u.display_name,GROUP_CONCAT(pm.meta_key, '=', pm.`meta_value` SEPARATOR '&') meta FROM wp_posts p INNER JOIN wp_term_relationships tr0 ON tr0.`object_id` = p.`ID` AND tr0.term_taxonomy_id = ". Wordpress_model::FORMAT_VIDEO_TYPE . " LEFT JOIN wp_term_relationships tr ON tr.`object_id` = p.`ID` LEFT JOIN wp_users u ON u.ID=p.`post_author`
	LEFT JOIN wp_postmeta pm ON pm.`post_id`=p.`ID` WHERE p.post_type='post' AND
    p.post_status='publish' AND tr.term_taxonomy_id = $cid AND pm.meta_key IN ('views','cover') GROUP BY p.ID ORDER BY p.post_date DESC";
        //echo $sql;
        $res = $this->db_wp->query($sql);
        $articles = $res->result();
        foreach( $articles as &$article ){
            $meta = $article->meta;
            unset($article->meta);
            parse_str($meta, $article_meta);         
            $cover = isset($article_meta['cover'])?$article_meta['cover']:null;
            $article->cover = $this->get_cover($cover);
            $article->views = $article_meta['views'];
        }
        return $articles;
    }
    public function fetch_videos_for_index_page($cid){
        //".POST_FORMAT_VIDEO."
        $sql = "SELECT p.ID,p.post_title,p.post_date,p.comment_count,u.display_name,GROUP_CONCAT(pm.meta_key, '=', pm.`meta_value` SEPARATOR '&') meta FROM wp_posts p INNER JOIN wp_term_relationships tr0 ON tr0.`object_id` = p.`ID` AND tr0.term_taxonomy_id = ". Wordpress_model::FORMAT_VIDEO_TYPE . " LEFT JOIN wp_term_relationships tr ON tr.`object_id` = p.`ID` LEFT JOIN wp_users u ON u.ID=p.`post_author`
    LEFT JOIN wp_postmeta pm ON pm.`post_id`=p.`ID` WHERE p.post_type='post' AND
    p.post_status='publish' AND tr.term_taxonomy_id = $cid AND pm.meta_key IN ('views','cover') GROUP BY p.ID ORDER BY p.post_date DESC limit 4";
        //echo $sql;
        $res = $this->db_wp->query($sql);
        $articles = $res->result();
        foreach( $articles as &$article ){
            $meta = $article->meta;
            unset($article->meta);
            parse_str($meta, $article_meta);         
            $cover = isset($article_meta['cover'])?$article_meta['cover']:null;
            $article->cover = $this->get_cover($cover);
            $article->views = $article_meta['views'];
        }
        return $articles;
    }
    public function fetch_articles($cid, $page){
        $page_size = M_LIST_PAGE_SIZE;
        $page = ($page < 1) ? 1 : intval($page);
        $start = ($page-1)*$page_size;
        if ($cid){
        $ids = $this->get_children_term($cid);
        array_push($ids, $cid);
        $ids = implode(',', $ids);
        $sql = "SELECT p.`ID`,p.post_title,p.post_date,p.comment_count,u.`display_name`,GROUP_CONCAT(pm.meta_key, '=', pm.`meta_value` SEPARATOR '&') meta 
	FROM wp_posts p LEFT JOIN wp_term_relationships tr ON tr.`object_id` = p.`ID` LEFT JOIN wp_users u ON u.ID=p.`post_author` 
	LEFT JOIN wp_postmeta pm ON pm.`post_id`=p.`ID` WHERE p.post_type='post' and
    p.post_status='publish' and tr.term_taxonomy_id IN ($ids) AND pm.meta_key IN ('intro','cover') GROUP BY p.`ID` ORDER BY p.post_date DESC LIMIT $start, $page_size";
        } else{//人气排序
            $sql = "SELECT p.`ID`,p.post_title,p.post_date,p.comment_count,u.`display_name`,pm.`meta_value` views 
	FROM wp_posts p LEFT JOIN wp_term_relationships tr ON tr.`object_id` = p.`ID` LEFT JOIN wp_users u ON u.ID=p.`post_author` 
	LEFT JOIN wp_postmeta pm ON pm.`post_id`=p.`ID` WHERE p.post_type='post' AND
    p.post_status='publish' AND pm.meta_key='views' GROUP BY p.ID ORDER BY views DESC LIMIT $start, $page_size";
        }
    
        $res = $this->db_wp->query($sql);
        $articles = $res->result();
        $id2index = $pids = $article_list = array();
        foreach($articles as $index => $article){
            $id = $article->ID;
            $id2index[$id] = $index;
            if ($cid){
                $meta = $article->meta;
                parse_str($meta, $article_meta);         
                $cover = isset($article_meta['cover'])?$article_meta['cover']:null;
                $cover = $this->get_cover($cover);
                $intro = $article_meta['intro'];
            } else{
                $cover = $intro = '';
            }
            array_push($pids, $id);
            //$sql = 'SELECT COUNT(*) total FROM wp_comments WHERE comment_post_ID='.$id;
            //$total = $this->db_wp->query($sql)->first_row()->total;
            //$views = $this->get_article_views($article->ID);
            $article_list[] = array('id' => $id, 'date' => $article->post_date, 'title' => $article->post_title, 'author' => $article->display_name, 'cover' => $cover, 'intro' => $intro, 'total' => $article->comment_count);
        }
        if( !empty($pids) ){
            $pids = implode(',', $pids);
            $sql = "SELECT object_id FROM wp_term_relationships WHERE object_id IN ($pids) AND term_taxonomy_id=".POST_FORMAT_VIDEO;
            $rows = $this->db_wp->query($sql)->result();
            if (!empty($rows)){
                foreach ($rows as $row){
                    $index = $id2index[$row->object_id];
                    $article_list[$index]['video'] = true;
                }
            }

            if (!$cid){
                $sql = "SELECT post_id,meta_key,meta_value FROM wp_postmeta WHERE post_id IN ($pids) AND meta_key IN ('intro','cover')"; 
                $res = $this->db_wp->query($sql);
                $rows = $res->result();
                foreach ($rows as $row){
                    $index = $id2index[$row->post_id];
                    $key = $row->meta_key;
                    if ('cover' == $key)
                        $article_list[$index][$key] = $this->get_cover($row->meta_value);
                    else
                        $article_list[$index][$key] = $row->meta_value;
                }

            }
        }
        return $article_list;
    }
    public function comment_article($data){
        $data['comment_author_IP'] = $_SERVER["REMOTE_ADDR"];
        $data['comment_date'] = date('Y-m-d H:i');
        

        $this->db_wp->insert('comments', $data);
        return $this->db_wp->insert_id();

    }
    public function get_children_term($cid){
        $sql = 'SELECT term_taxonomy_id FROM wp_term_taxonomy WHERE parent='.$cid;
        $res = $this->db_wp->query($sql)->result_array();
        $ids = array();
        foreach ($res as $r){
            array_push($ids, $r['term_taxonomy_id']);
        }
        return $ids;
    }
    

    public function filter($filter = array())
    {
        $query = $this->db_wp->get_where('posts',$filter,1);
        return $query->row();
    }

    //获取文章和视频信息
    public function article_collect_list($user_id)
    {
        $sql = " SELECT product_id FROM " .$this->db->dbprefix('front_collect_product')." AS fcp ";
        $sql .= " WHERE fcp.`user_id` = '".$user_id."'  AND (fcp.`product_type` = 2 OR fcp.`product_type` = 4) ORDER BY fcp.`create_date` DESC";
        $query = $this->db->query($sql);
        $acl = $query->result_array();
        if(!empty($acl)){
            foreach ($acl as $val) {
                $article[] = $val['product_id'];
            }

            $this->db_wp->select('posts.*,users.display_name,postmeta.meta_value cover');
            $this->db_wp->join('users', 'users.ID = posts.post_author', 'left');
            $this->db_wp->join('postmeta', 'postmeta.post_id = posts.ID', 'left');
            $this->db_wp->where('postmeta.meta_key','cover');
            $this->db_wp->where_in('posts.ID', $article);
            $detail = $this->db_wp->get('posts')->result();

            //判断 是否(文章与视频)
            $sql = " SELECT tr.object_id FROM wp_terms AS tm ";
            $sql .= " LEFT JOIN wp_term_taxonomy AS tt ON tt.term_id= tm.term_id";
            $sql .= " LEFT JOIN wp_term_relationships AS tr ON tr.term_taxonomy_id= tt.term_taxonomy_id";
            $sql .= " WHERE tm.`name` = 'post-format-video'";
            $query = $this->db_wp->query($sql);
            $video = $query->result_array();

            //文章与视频的 封面图片
            foreach ($detail as $key => $det_val) {
                $sql = 'SELECT ID, guid FROM wp_posts WHERE ID = '.$det_val->cover;
                $query = $this->db_wp->query($sql);
                $arc_img = $query->row();
                $detail[$key]->arc_img = $arc_img->guid;

                //判断 是否(文章与视频)
                if(deep_in_array($det_val->ID,$video)){
                    $detail[$key]->video = 1;  // 视频
                }else {
                    $detail[$key]->video = 0;  // 文章
                }
            }
           
            return $detail;
        }else{
            return;
        }

    }
    /**
     * 替换文章内的视频为手机可用播放
     * 2015-10-18 lichao
     */

     public function adjust_content($content=''){

if( preg_match_all( '/<p>\r\n?.*embed.*vid=(.*)".*\r\n?<\/p>/iU', $content, $matches ) ){
foreach($matches[1] AS $key=>$video_id){
     $new_content=<<<EOD
     <p style="margin: 0px; padding: 0px; max-width: 100%; clear: both; min-height: 1em; color: rgb(62, 62, 62); box-sizing: border-box !important; word-wrap: break-word !important;">
	<iframe allowfullscreen="" class="video_iframe" data-src="https://v.qq.com/iframe/preview.html?vid=${video_id}&amp;width=100%&amp;height=100%&amp;auto=0" frameborder="0"  scrolling="no" src="http://v.qq.com/iframe/player.html?vid=${video_id}&amp;auto=0" style="margin: 0px; padding: 0px; max-width: 100%; box-sizing: border-box !important; word-wrap: break-word !important; display: block; z-index: 1; width: 100% !important;  overflow: hidden;min-height:260px;" ></iframe></p>
EOD;
$content= str_replace( $matches[0][$key], $new_content, $content);
}

}
return $content;

     }


    /**
     *   查 用户是否 给 文章 点赞
     *
     */
    public function filter_praise($filter){
        $query = $this->db_wp->get_where('zan',$filter,1);
        return $query->row();
    }
    
    /**
     *   添加 用户 文章点赞
     *
     */
    public function insert_praise($data)
    {
        $this->db_wp->insert('zan',$data);
        return $this->db_wp->insert_id();
    }

    /**
     *   查看 文章点赞数量
     *
     */
    public function article_praise_num($article_id){
        $sql = " SELECT count(id) praise_num FROM wp_zan WHERE post_id = $article_id";
        $query = $this->db_wp->query($sql);
        return $query->row();

    }


    //获取 用户 点赞 的文章信息
    public function get_article_praise ($filter){
        $query = $this->db_wp->get_where('wp_zan',$filter);
        return $query->result_array();
    }


    // PC 分词搜索的视频
    public function get_search_video($ids){
        $sql = "SELECT wpt.`ID`,wpt.`post_title`, wpm.`meta_value` img FROM wp_posts AS wpt LEFT JOIN wp_postmeta AS wpm ON wpt.`ID` = wpm.`post_id` WHERE wpm.`meta_key` = 'cover' AND wpt.`ID` in (".$ids.")  LIMIT 8 ";
        $query=$this->db_wp->query($sql);     
        $result=$query->result();

        foreach ($result as $key => $row){
            $result[$key]->img = $this->get_cover($row->img);
        }
        return $result;
    }


    // PC 视频封面图片
    private function get_cover($cover){
        if( !empty($cover) ){               
                $sql = 'SELECT guid FROM wp_posts WHERE id='.$cover.' AND post_type = \'attachment\'';
                $res = $this->db_wp->query($sql);
                $post = $res->first_row();
                if (isset($post->guid))
                    $cover_img = $post->guid;
                else
            $cover_img = static_url('mobile/img/hs-pic.png');
        }
        else
            $cover_img = static_url('mobile/img/hs-pic.png');
        return $cover_img;
    }

    public function get_hot_videos() {
        $sql = "SELECT
                p.ID,
                p.post_title,   
                pm.meta_value views,
                p.comment_count
            FROM
                wp_posts p
            INNER JOIN wp_term_relationships tr0 ON tr0.`object_id` = p.`ID`

            LEFT JOIN wp_postmeta pm ON pm.`post_id` = p.`ID`
            WHERE
                p.post_type = 'post'
            AND p.post_status = 'publish'
            AND pm.meta_key = 'views'
            AND tr0.term_taxonomy_id = " . Wordpress_model::FORMAT_VIDEO_TYPE . 
            "
            GROUP BY
                p.ID
            ORDER BY
                views DESC
            LIMIT 3";

        $query = $this->db_wp->query($sql);
        $res = $query->result();
        // 去相关的封面

        $sql = "select guid from wp_posts p, wp_postmeta pm
                where p.ID = pm.meta_value
                and pm.meta_key = 'cover'
                and pm.post_id = ?
                ";
        $sql_comments = "";
        foreach ($res as $k => &$v) {
            $query = $this->db_wp->query($sql, array(intval($v->ID)));
            if ($query->num_rows() > 0) {
                $v->cover = $query->result()[0]->guid;
            } else {
                $v->cover = static_url('mobile/img/hs-pic.png');
            }
        }
        return $res;
    }

    //获取视频信息
    public function video_collect_list($user_id)
    {
        $sql = " SELECT product_id FROM " .$this->db->dbprefix('front_collect_product')." AS fcp ";
        $sql .= " WHERE fcp.`user_id` = '".$user_id."'  AND ( fcp.`product_type` = 4) ORDER BY fcp.`create_date` DESC";
        $query = $this->db->query($sql);
        $acl = $query->result_array();
        if(!empty($acl)){
            foreach ($acl as $val) {
                $article[] = $val['product_id'];
            }

            $this->db_wp->select('posts.*,users.display_name,postmeta.meta_value cover');
            $this->db_wp->join('users', 'users.ID = posts.post_author', 'left');
            $this->db_wp->join('postmeta', 'postmeta.post_id = posts.ID', 'left');
            $this->db_wp->where('postmeta.meta_key','cover');
            $this->db_wp->where_in('posts.ID', $article);
            $detail = $this->db_wp->get('posts')->result();

            //判断 是否(文章与视频)
            $sql = " SELECT tr.object_id FROM wp_terms AS tm ";
            $sql .= " LEFT JOIN wp_term_taxonomy AS tt ON tt.term_id= tm.term_id";
            $sql .= " LEFT JOIN wp_term_relationships AS tr ON tr.term_taxonomy_id= tt.term_taxonomy_id";
            $sql .= " WHERE tm.`name` = 'post-format-video'";
            $query = $this->db_wp->query($sql);
            $video = $query->result_array();

            //文章与视频的 封面图片
            foreach ($detail as $key => $det_val) {
                $detail[$key]->cover = $this->get_cover($det_val->cover);
                //判断 是否(文章与视频)
                if(deep_in_array($det_val->ID,$video)){
                    $detail[$key]->video = 1;  // 视频
                }else {
                    $detail[$key]->video = 0;  // 文章
                }
            }
           
            return $detail;
        }else{
            return;
        }

    }

}
