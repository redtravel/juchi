<?php
#doc
#	classname:	Cron
#	scope:		PUBLIC
# Usage: 在CRONTAB 中添加记录
# 	1 3 * * * 路径/index.php cron 方法名
#
#/doc

class Cron extends CI_Controller
{

	function __construct ()
	{
		parent::__construct();
		$this->admin_id=0;
		$this->time=date('Y-m-d H:i:s');
//		if(!$this->input->is_cli_request()) die('该脚本只能通过cron运行');
		ini_set('max_execution_time', '0');
        $this->load->library('memcache');
	}
    public function show_index_goods(){
        $this->load->model('product_model');
        $goods_list = $this->product_model->get_index_goods();
        $goods_list = array_chunk($goods_list, MOBILE_INDEX_PRODUCT_NUM);
        $this->memcache->delete('index_goods_list'); // delete key first
        $this->memcache->save('index_goods_list', $goods_list, 7200);

        //手动更新 memcache key 的函数方法
        memcache_key_record('index_goods_list','首页产品列表',__CLASS__,__FUNCTION__,str_replace(FCPATH,'',__FILE__));
    }
    public function get_course(){
        $this->load->model('wordpress_model');
        $course_list = $this->wordpress_model->fetch_courses();
        $this->memcache->save('course_list', $course_list, 7200);
    }
    public function mkdict(){
        $static = realpath(CREATE_HTML_PATH);
        $static = str_replace('\\', '/', $static);
        $dict = $static.'/dict.txt';
        $this->load->model('sphinx_word_model');
        //echo $modify_time, ' ';
        $last_time = $this->sphinx_word_model->lastime();
        if (!file_exists($dict) || strtotime($last_time)>filemtime($dict)){
            //生成词库
            $words = $this->sphinx_word_model->getdict();
            $lines = array();
            foreach($words as $r){
                $line = $r->name."\t".$r->level;
                array_push($lines, $line);
            }
            $lines = implode("\n", $lines);
            file_put_contents($dict, $lines);
            echo 'make success';
        } else{
            echo 'no need make';
        }
    }
	
	public function send_rush_sms ($test=FALSE)
	{
		$this->load->model('rush_model');
		$this->load->library('mobile');
		$temp=$this->rush_model->filter_rush_template(array(
			'start_date <=' => $this->time,
			'end_date >=' => $this->time
		));
		if(!$temp||!$temp->mobile_template) return;
		$content = $temp->mobile_template;
		/*
		* 抽出短信号码进行发送
		* 注册用户抽1000条
		* 非注册用户抽1000条
		*/

		$mobiles=array();
		if ( $test )
		{
			$mobiles=$this->config->item('mobile_presend');			
		}else{
			foreach($this->rush_model->fetch_rush_mobile(TRUE,1000) as $row){
				$mobiles[$row->rec_id] = $row->mobile;
			}
			
			foreach($this->rush_model->fetch_rush_mobile(FALSE,2000-count($mobiles)) as $row )
			{
				$mobiles[$row->rec_id] = $row->mobile;
			}
		}
		
		
		$total_count = count($mobiles);
		$count = 0;
		$send_mobiles=array();		
		foreach($mobiles as $k=>$v)
		{
			$send_mobiles[$k] = $v;
			$count++;
			if(count($send_mobiles)>=5 || $count>=$total_count){
				$keys = array_keys($send_mobiles);
				//发送
				//$result = $this->mobile->send($content,$send_mobiles);
				$result='';
				//更新
				if ( !$test )
				{
					$this->rush_model->update_rush_mobile(array('send_date'=>$this->time),$keys);
					//插记录
					$this->rush_model->log_rush_mobile(array(
						'content'=>$content,
						'create_date' =>$this->time,
						'send_date'=>$this->time,
						'status' => $result?2:1
					),$keys);
				}else{
					foreach( $send_mobiles as $m )
					{
						$this->db->insert('sms_log',array(
							'sms_from'=>'',
							'sms_to'=>$m,
							'template_id'=>-1,
							'template_content'=>$content,
							'create_date' =>$this->time,
							'send_date'=>$this->time,
							'status' => $result?2:1
						));
					}					
				}				
				$send_mobiles=array();
				usleep(500000);
			}
		}		
	}
	
	public function sms_sender ()
	{
		$this->load->library('mobile');
		while ( TRUE )
		{
			$sql="SELECT l.rec_id,l.sms_to,l.template_content FROM ".$this->db->dbprefix('sms_log')." AS l
				WHERE l.status=0
				AND NOT EXISTS(SELECT 1 FROM ".$this->db->dbprefix('mobile_blacklist')." AS b WHERE b.mobile=l.sms_to)
				ORDER BY l.sms_priority DESC, l.rec_id ASC
				LIMIT 5";
			$query=$this->db->query($sql);
			$rows=$query->result();
			if(!$rows) return;
			$keys=array();
			foreach($rows as $row){
				//发送
				//$result = $this->mobile->send($row->template_content,$row->sms_to);
				$result = '';
				$keys[] = $row->rec_id;
				usleep(500000);
			}
			$this->db->update('sms_log',array(
				'send_date' => $this->time,
				'status' => $result?2:1
			),'rec_id '.db_create_in($keys));
		}
	}
	
	public function mail_sender ()
	{
		$this->load->library('email');
		while ( TRUE )
		{
			$sql="SELECT l.rec_id,l.mail_from,l.mail_to,l.template_subject,l.template_content
				FROM ".$this->db->dbprefix('mail_log')." AS l
				WHERE l.status=0
				ORDER BY l.template_priority DESC, l.rec_id ASC
				LIMIT 100;";
			$query=$this->db->query($sql);
			$rows=$query->result();
			if(!$rows) return;
			$keys=array();
			foreach($rows as $row){
				//发送
				$this->email->clear();
				$this->email->from($row->mail_from, '爱童网');
				$this->email->to($row->mail_to);
				$this->email->reply_to($row->mail_to);
				$this->email->subject($row->template_subject);
				$this->email->message($row->template_content);
				$result = $this->email->send();
				$keys[] = $row->rec_id;
				usleep(1000000);
			}
			$this->db->update('mail_log',array(
				'send_date' => $this->time,
				'status' => $result?1:2
			),'rec_id '.db_create_in($keys));
		}
	}
	
	public function etao ()
	{
		//生成分类索引
		$this->load->model('category_model');
		$this->load->helper('category');
		$all_category = index_array($this->category_model->all_category(),'category_id');
		$xml=$this->load->view('cron/etaoSellerCats',array('category_tree'=>category_tree($all_category)),TRUE);
		file_put_contents(CREATE_UNION_PATH.'etao/SellerCats.xml',$xml);
		unset($xml);
		//生成全量索引
		/*取图片*/
		$sql="SELECT product_id,color_id,image_type,img_760_760 
			FROM ty_product_gallery
			WHERE image_type='part' OR image_type='default'
			ORDER BY product_id,color_id";
		$query=$this->db->query($sql);
		$result=$query->result();
		$gs=array();
		foreach( $result as $g )
		{
			$k=$g->product_id.'-'.$g->color_id;
			if(!isset($gs[$k])) $gs[$k]=array('default'=>'','part'=>array());
			if($g->image_type=='default'){
				$gs[$k]['default']=img_url('data/gallery/'.$g->img_760_760);
			}else{
				$gs[$k]['part'][]=img_url('data/gallery/'.$g->img_760_760);
			}
		}
		unset($g);
		/*取商品信息*/
		$sql="SELECT p.product_id,p.product_sn,p.product_name,p.market_price,p.shop_price,
			c.color_id,c.color_sn,c.color_name,s.size_sn,s.size_name,b.brand_name,
			cat.category_id,cat.category_name,pcat.category_name AS pcategory_name,
			p.is_promote,p.promote_start_date,p.promote_end_date
			FROM ty_product_sub AS sub
			LEFT JOIN ty_product_color AS c ON sub.color_id=c.color_id
			LEFT JOIN ty_product_size AS s ON sub.size_id=s.size_id
			LEFT JOIN ty_product_info AS p ON sub.product_id=p.product_id
			LEFT JOIN ty_product_brand AS b ON p.brand_id=b.brand_id
			LEFT JOIN ty_product_category AS cat ON p.category_id=cat.category_id
			LEFT JOIN ty_product_category AS pcat ON cat.parent_id=pcat.category_id
			WHERE sub.is_on_sale=1 AND (sub.consign_num>0 OR sub.consign_num=-2 OR sub.gl_num>sub.wait_num)
			ORDER BY sub.product_id,sub.color_id";
		$query=$this->db->query($sql);
		$result=$query->result();
		$ps=array();
		foreach( $result as $p )
		{
			$k=$p->product_id.'-'.$p->color_id;
			if(!isset($gs[$k])) continue;
			if(!isset($ps[$k])) {
				if($p->is_promote&&($p->promote_start_date>$this->time||$p->promote_end_date<$this->time)) $p->is_promote=0;
				$ps[$k] =$p;
				$ps[$k]->ss=array();
				$ps[$k]->g=$gs[$k];
			}
			$ps[$k]->ss[]=$p->size_name;
		}
		unset($p);
		$xml=$this->load->view('cron/etaoFullIndex',array('ps'=>$ps),TRUE);
		file_put_contents(CREATE_UNION_PATH.'etao/FullIndex.xml',$xml);
		
		//生成数据文件
		foreach( $ps as $p )
		{
			$xml=$this->load->view('cron/etaoItem',array('p'=>$p),TRUE);
			file_put_contents(CREATE_UNION_PATH."etao/item/".($p->product_id+100000000000).($p->color_id+100000000000).".xml",$xml);
		}
	}
	
	public function etao_increment ()
	{
		//生成增量索引
		$xml=$this->load->view('cron/etaoIncrementIndex',NULL,TRUE);
		file_put_contents(CREATE_UNION_PATH.'etao/IncrementIndex.xml',$xml);
	}
	
	public function google ()
	{
		//生成分类索引
		$this->load->model('category_model');
		$this->load->helper('category');
		$this->config->load('product');
		$gcat = $this->config->item('product_google_cat');
		
		//生成全量索引
		/*取图片*/
		$sql="SELECT product_id,color_id,image_type,img_760_760 
			FROM ty_product_gallery
			WHERE image_type='part' OR image_type='default'
			ORDER BY product_id,color_id";
		$query=$this->db->query($sql);
		$result=$query->result();
		$gs=array();
		foreach( $result as $g )
		{
			$k=$g->product_id.'-'.$g->color_id;
			if(!isset($gs[$k])) $gs[$k]=array('default'=>'','part'=>array());
			if($g->image_type=='default'){
				$gs[$k]['default']=img_url('data/gallery/'.$g->img_760_760);
			}else{
				$gs[$k]['part'][]=img_url('data/gallery/'.$g->img_760_760);
			}
		}
		unset($g);
		/*取商品信息*/
		$sql="SELECT p.product_id,p.product_sn,p.product_name,p.market_price,p.shop_price,p.provider_productcode,p.product_sex,
			c.color_id,c.color_sn,c.color_name,b.brand_name,
			cat.category_id,cat.category_name,cat.parent_id AS pcategory_id,pcat.category_name AS pcategory_name,
			p.is_promote,p.promote_start_date,p.promote_end_date
			FROM ty_product_sub AS sub
			LEFT JOIN ty_product_color AS c ON sub.color_id=c.color_id
			LEFT JOIN ty_product_info AS p ON sub.product_id=p.product_id
			LEFT JOIN ty_product_brand AS b ON p.brand_id=b.brand_id
			LEFT JOIN ty_product_category AS cat ON p.category_id=cat.category_id
			LEFT JOIN ty_product_category AS pcat ON cat.parent_id=pcat.category_id
			WHERE sub.is_on_sale=1 AND (sub.consign_num>0 OR sub.consign_num=-2 OR sub.gl_num>sub.wait_num)
			ORDER BY sub.product_id,sub.color_id";
		$query=$this->db->query($sql);
		$result=$query->result();
		$ps=array();
		foreach( $result as $p )
		{
			$k=$p->product_id.'-'.$p->color_id;
			if(!isset($gs[$k]) || isset($ps[$k])) continue;
			if(isset($gcat[$p->category_id])){
				$p->gcat=$gcat[$p->category_id];
			}elseif(isset($gcat[$p->pcategory_id])){
				$p->gcat=$gcat[$p->pcategory_id];
			}else{
				continue;
			}
			$p->g=$gs[$k];
			if($p->is_promote&&($p->promote_start_date>$this->time||$p->promote_end_date<$this->time)) $p->is_promote=0;
			if($p->product_sex==1){
				$p->gender='male';
			}elseif($p->product_sex==2){
				$p->gender='female';
			}else{
				$p->gender='unisex';
			}
			$ps[$k]=$p;
		}
		unset($p);
		$xml=$this->load->view('cron/googleFeed',array('ps'=>$ps),TRUE);
		file_put_contents(CREATE_UNION_PATH.'google/product_feed.xml',$xml);
	}

        /**
         * 生成导航
         */
        function create_nav() {
            $this->load->model('nav_model');
            $cats = $this->nav_model->get_root_cats(TRUE);
            for ($i = 0; $i < count($cats); $i++) {
                $cats[$i]["rush_data"] =  $this->nav_model->get_rush_cats($cats[$i]["nav_id"], TRUE);
                $cats[$i]["data"] = $this->nav_model->get_cats($cats[$i]["nav_id"], TRUE);
            }
            $cats[$i] = array("type_id" => FALSE, "type_code" => "BY_SIZE", "type_name" => "按尺码选购");
            $cats[$i]["male_data"] =  $this->nav_model->get_size_cats(TRUE, TRUE);
            $cats[$i]["famale_data"] =  $this->nav_model->get_size_cats(FALSE, TRUE);
            $html = $this->load->view('temp/nav.php',array('cats' => $cats),TRUE);
            file_put_contents(TEMP_INDEX_NAVIGATION, $html);
            chown(TEMP_INDEX_NAVIGATION, 'daemon');
            chgrp(TEMP_INDEX_NAVIGATION, 'daemon');
            echo '操作成功。';
        }
        
        /**
         * 抢购自动下架
         */
        function auto_finish_rush() {
            $this->load->model('rush_model');
            $this->rush_model->auto_finish_rush_off();
        }
        
        /**
         * 抢购自动上架
         */
        function auto_open_rush() {
            $this->load->model('rush_model');
            $this->rush_model->auto_finish_rush_on();
        }
        
        /**
         * 生成RUSH分类
         */
        function generate_rush_category() {
            $this->load->model('nav_model');
            $this->load->model('rush_model');
            $data = "";
            $result = array();
            $cat_arr = $this->nav_model->get_all_rush_cats(TRUE);
            foreach ($cat_arr as $key => $item) {
                $rush_id = $item["rush_id"];
                $type_id_1 = $item["type_id_1"];
                $type_id_2 = $item["type_id_2"];
                $type_id_3 = $item["type_id_3"];
                if (empty($result[$rush_id])) {
                    $result[$rush_id] = "";
                    $result[$rush_id]->cat = "";
                    $result[$rush_id]->size = "";
                }
                if (empty($result[$rush_id]->cat->$type_id_1->name)) {
				//限抢id cat 类别1 name = 类别1名称
                    $result[$rush_id]->cat->$type_id_1->name = $item["type_name_1"];
					
                    $rush_size_arr = $this->nav_model->get_rush_size_cats($rush_id, $type_id_1, NULL, NULL, TRUE);
                    foreach ($rush_size_arr as $idx => $val) {
                        if ($type_id_1 == 42) $val["product_sex"] = 2; else if ($type_id_1 == 88) $val["product_sex"] = 1;
                        // 限抢id size 类别1 性别 尺寸 = 尺寸名称
						if ($val["product_sex"] == 3) {
                            $size_id = $val["size_id"];
                            $sex = "male";
                            $result[$rush_id]->size->$type_id_1->$sex->$size_id = $val["size_name"];
                            $sex = "famale";
                            $result[$rush_id]->size->$type_id_1->$sex->$size_id = $val["size_name"];
                        } else {
                            $sex = $val["product_sex"] == 1 ? "male" : "famale";
                            $size_id = $val["size_id"];
                            $result[$rush_id]->size->$type_id_1->$sex->$size_id = $val["size_name"];
                        }
                    }
                }
				
                if (empty($result[$rush_id]->cat->$type_id_1->$type_id_2->name)) {
				    //限抢id cat 类别1 类别2 name = 类别2名称
                    $result[$rush_id]->cat->$type_id_1->$type_id_2->name = $item["type_name_2"];
                    $rush_size_arr = $this->nav_model->get_rush_size_cats($rush_id, NULL, $type_id_2, NULL, TRUE);
                    foreach ($rush_size_arr as $idx => $val) {
                        if ($type_id_1 == 42) $val["product_sex"] = 2; else if ($type_id_1 == 88) $val["product_sex"] = 1;
                        // 限抢id size 类别2 性别 尺寸 = 尺寸名称
						if ($val["product_sex"] == 3) {
                            $size_id = $val["size_id"];
                            $sex = "male";
                            $result[$rush_id]->size->$type_id_2->$sex->$size_id = $val["size_name"];
                            $sex = "famale";
                            $result[$rush_id]->size->$type_id_2->$sex->$size_id = $val["size_name"];
                            
                        } else {
                            $sex = $val["product_sex"] == 1 ? "male" : "famale";
                            $size_id = $val["size_id"];
                            $result[$rush_id]->size->$type_id_2->$sex->$size_id = $val["size_name"];
                        }
                    }
                }
				
                if (empty($result[$rush_id]->cat->$type_id_1->$type_id_2->$type_id_3->name)) {
				// 限抢id cat 类别1 类别2 类别3 name = 类别2名称
                    $result[$rush_id]->cat->$type_id_1->$type_id_2->$type_id_3->name = $item["type_name_3"];
                    $rush_size_arr = $this->nav_model->get_rush_size_cats($rush_id, NULL, NULL, $type_id_3, TRUE);
                    foreach ($rush_size_arr as $idx => $val) {
                        if ($type_id_1 == 42) $val["product_sex"] = 2; else if ($type_id_1 == 88) $val["product_sex"] = 1;
                        //// 限抢id size 类别3 性别 尺寸 = 尺寸名称
						if ($val["product_sex"] == 3) {
                            $size_id = $val["size_id"];
                            $sex = "male";
                            $result[$rush_id]->size->$type_id_3->$sex->$size_id = $val["size_name"];
                            $sex = "famale";
                            $result[$rush_id]->size->$type_id_3->$sex->$size_id = $val["size_name"];
                        } else {
                            $sex = $val["product_sex"] == 1 ? "male" : "famale";
                            $size_id = $val["size_id"];
                            $result[$rush_id]->size->$type_id_3->$sex->$size_id = $val["size_name"];
                        }
                    }
                }
				
                if (empty($result[$rush_id]->size->data)) {
                    $rush_size_arr = $this->nav_model->get_rush_size_cats($rush_id, NULL, NULL, NULL, TRUE);
                    foreach ($rush_size_arr as $idx => $val) {
                        if ($val["parent_id"] == 42) $val["product_sex"] = 2; else if ($val["parent_id"] == 88) $val["product_sex"] = 1;
                        // 限抢id size data 性别 尺寸 = 尺寸名称
						if ($val["product_sex"] == 3) {
                            $size_id = $val["size_id"];
                            $sex = "male";
                            $result[$rush_id]->size->data->$sex->$size_id = $val["size_name"];
                            $sex = "famale";
                            $result[$rush_id]->size->data->$sex->$size_id = $val["size_name"];
                        } else {
                            $sex = $val["product_sex"] == 1 ? "male" : "famale";
                            $size_id = $val["size_id"];
                            $result[$rush_id]->size->data->$sex->$size_id = $val["size_name"];
                        }
                    }
                }
            }
            foreach ($result as $key => $item) {
                $this->rush_model->comfort_rush_cat_content($key, json_encode($item));
            }
            echo '操作成功。';
        }
        /**
         * 生成分类页/品牌页的分类
         */
        function generate_list_category () {
            $this->load->model('nav_model');
            $product_arr = array();
			$brand_arr_result = array();
            $brand_arr = $this->nav_model->get_cat_brands(TRUE);
            foreach ($brand_arr as $key => $item) {
                $type_id_1 = $item["type_id_1"];
                $type_id_2 = $item["type_id_2"];
                $brand_id = $item["brand_id"];
                $unisex = $item["product_sex"] == 3 ? TRUE : FALSE;
                $product_sex = $item["product_sex"] == 1 ? "male" : "famale";
                $male = "male";
                $famale = "famale";
				// 分类
                if (empty($product_arr[$type_id_1]->cat->id)) {
                    $product_arr[$type_id_1]->cat->id = $type_id_1;
                    $product_arr[$type_id_1]->cat->name = $item["type_name_1"];
                    $product_arr[$type_id_1]->ids = array();
                }
                if (empty($product_arr[$type_id_1]->cat->$type_id_2->name)) {
                    $product_arr[$type_id_1]->cat->$type_id_2->name = $item["type_name_2"];
                }

                if ($unisex) {
				// 大类1 brand 姓别 品牌 = 品牌名称
                    if (empty($product_arr[$type_id_1]->brand->$male->$brand_id)) {
                        $product_arr[$type_id_1]->brand->$male->$brand_id = $item["brand_name"];
                    }
                    if (empty($product_arr[$type_id_1]->brand->$famale->$brand_id)) {
                        $product_arr[$type_id_1]->brand->$famale->$brand_id = $item["brand_name"];
                    }
					// 大类1 cat 大类2 brand 姓别 品牌 = 品牌名称
                    if (empty($product_arr[$type_id_1]->cat->$type_id_2->brand->$male->$brand_id)) {
                        $product_arr[$type_id_1]->cat->$type_id_2->brand->$male->$brand_id = $item["brand_name"];
                    }
                    if (empty($product_arr[$type_id_1]->cat->$type_id_2->brand->$famale->$brand_id)) {
                        $product_arr[$type_id_1]->cat->$type_id_2->brand->$famale->$brand_id = $item["brand_name"];
                    }
                } else {
                    if (empty($product_arr[$type_id_1]->brand->$product_sex->$brand_id)) {
                        $product_arr[$type_id_1]->brand->$product_sex->$brand_id = $item["brand_name"];
                    }
                    if (empty($product_arr[$type_id_1]->cat->$type_id_2->brand->$product_sex->$brand_id)) {
                        $product_arr[$type_id_1]->cat->$type_id_2->brand->$product_sex->$brand_id = $item["brand_name"];
                    }
                }
				
				// 品牌
                if (empty($brand_arr_result[$brand_id]->cat->$type_id_1->name)) {
                    $brand_arr_result[$brand_id]->cat->$type_id_1->name = $item["type_name_1"];
                }
                if (empty($brand_arr_result[$brand_id]->cat->$type_id_1->$type_id_2->name)) {
                    $brand_arr_result[$brand_id]->cat->$type_id_1->$type_id_2->name = $item["type_name_2"];
                }
				
                if ($unisex) {
				// 大类1 brand 姓别 品牌 = 品牌名称
                    if (empty($brand_arr_result[$brand_id]->cat->$type_id_1->$male)) {
                        $brand_arr_result[$brand_id]->cat->$type_id_1->$male = $male;
                    }
                    if (empty($brand_arr_result[$brand_id]->cat->$type_id_1->$famale)) {
                        $brand_arr_result[$brand_id]->cat->$type_id_1->$famale = $famale;
                    }
					
                    if (empty($brand_arr_result[$brand_id]->cat->$type_id_1->$type_id_2->$male)) {
					    $brand_arr_result[$brand_id]->cat->$type_id_1->$type_id_2->$male = $male;
                    }
					
                    if (empty($brand_arr_result[$brand_id]->cat->$type_id_1->$type_id_2->$famale)) {
					    $brand_arr_result[$brand_id]->cat->$type_id_1->$type_id_2->$famale = $famale;
                    }					

                } else {
                    if (empty($brand_arr_result[$brand_id]->cat->$type_id_1->$product_sex)) {
                        $brand_arr_result[$brand_id]->cat->$type_id_1->$product_sex = $product_sex;
                    }
                    if (empty($brand_arr_result[$brand_id]->cat->$type_id_1->$type_id_2->$product_sex)) {
                        $brand_arr_result[$brand_id]->cat->$type_id_1->$type_id_2->$product_sex = $product_sex;
                    }
                }
				
                if (empty($product_arr[$type_id_1]->ids[$type_id_1])) {
                    $product_arr[$type_id_1]->ids[$type_id_1] = $type_id_1;
                }
                if (empty($product_arr[$type_id_1]->ids[$type_id_2])) {
                    $product_arr[$type_id_1]->ids[$type_id_2] = $type_id_2;
                }
            }

			// 分类页
            foreach ($product_arr as $key => $item) {
                $this->nav_model->comfort_product_type_cat_content($item->ids, json_encode($item));
            }
			// 品牌页
	    foreach ($brand_arr_result as $key => $item) {
                $this->nav_model->comfort_brand_product_type_cat_content($key, json_encode($item));
            }
            echo '操作成功。';
        }
		
        /**
         * 生成供应商(店铺)页面分类
         */
        function generate_list_provider () {
            $this->load->model('nav_model');
            $product_arr = array();
            $provider_arr = $this->nav_model->get_cat_providers(TRUE);
            foreach ($provider_arr as $key => $item) {
			    $provider_id = $item["provider_id"];
                $type_id_1 = $item["type_id_1"];
                $type_id_2 = $item["type_id_2"];
                $brand_id = $item["brand_id"];
                $unisex = $item["product_sex"] == 3 ? TRUE : FALSE;
                $product_sex = $item["product_sex"] == 1 ? "male" : "famale";
                $male = "male";
                $famale = "famale";
                if (empty($product_arr[$provider_id])) {
                    $product_arr[$provider_id] = "";
                    $product_arr[$provider_id]->cat = "";
                }				
				
                if (empty($product_arr[$provider_id]->cat->$type_id_1->id)) {
                    $product_arr[$provider_id]->cat->$type_id_1->name = $item["type_name_1"];
                }

                if (empty($product_arr[$provider_id]->cat->$type_id_1->$type_id_2->name)) {
                    $product_arr[$provider_id]->cat->$type_id_1->$type_id_2->name = $item["type_name_2"];
                }

                if ($unisex) {
				// 大类1 brand 姓别 品牌 = 品牌名称
                    if (empty($product_arr[$provider_id]->cat->$type_id_1->brand->$male->$brand_id)) {
                        $product_arr[$provider_id]->cat->$type_id_1->brand->$male->$brand_id = $item["brand_name"];
                    }
                    if (empty($product_arr[$provider_id]->cat->$type_id_1->brand->$famale->$brand_id)) {
                        $product_arr[$provider_id]->cat->$type_id_1->brand->$famale->$brand_id = $item["brand_name"];
                    }
					// 大类1 cat 大类2 brand 姓别 品牌 = 品牌名称
                    if (empty($product_arr[$provider_id]->cat->$type_id_1->$type_id_2->brand->$male->$brand_id)) {
                        $product_arr[$provider_id]->cat->$type_id_1->$type_id_2->brand->$male->$brand_id = $item["brand_name"];
                    }
                    if (empty($product_arr[$provider_id]->cat->$type_id_1->$type_id_2->brand->$famale->$brand_id)) {
                        $product_arr[$provider_id]->cat->$type_id_1->$type_id_2->brand->$famale->$brand_id = $item["brand_name"];
                    }
                } else {
                    if (empty($product_arr[$provider_id]->cat->$type_id_1->brand->$product_sex->$brand_id)) {
                        $product_arr[$provider_id]->cat->$type_id_1->brand->$product_sex->$brand_id = $item["brand_name"];
                    }
                    if (empty($product_arr[$provider_id]->cat->$type_id_1->$type_id_2->brand->$product_sex->$brand_id)) {
                        $product_arr[$provider_id]->cat->$type_id_1->$type_id_2->brand->$product_sex->$brand_id = $item["brand_name"];
                    }
                }
            }

            foreach ($product_arr as $key => $item) {
                $this->nav_model->comfort_provider_product_type_cat_content($key, json_encode($item));
            }
            echo '操作成功。';
        }
		
        /**
         * 写结果缓存文件
         *
         * @params  string  $cache_name
         * @params  string  $caches
         *
         * @return
         */
        function auto_cat_cache() {
            $this->load->model('nav_model');
            $this->load->driver("cache");   
            $res = $this->nav_model->get_admin_cats(TRUE);
            $res2 = $this->nav_model->get_cat_goods_numbers(TRUE);
            $newres = array();
            foreach($res2 as $k=>$v)
            {
                $newres[$v['cat_id']] = $v['goods_num'];
            }
            foreach($res as $k=>$v)
            {
                $res[$k]['goods_num'] = !empty($newres[$v['cat_id']]) ? $newres[$v['cat_id']] : 0;
            }
            $this->write_cat_cache('cat_pid_releate', $res);
            $this->cat_options_cache($res);
            echo '操作成功。';
        }
        /**
         * 过滤和排序所有分类，返回一个带有缩进级别的数组
         * @param array $arr 含有所有分类的数组
         */
        private function cat_options_cache($arr) {
            $level = $last_cat_id = 0;
            $options = $cat_id_array = $level_array = array();
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $cat_id = $value['cat_id'];
                    if ($level == 0 && $last_cat_id == 0)
                    {
                        if ($value['parent_id'] > 0)
                        {
                            break;
                        }

                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['cat_name'];
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
                        $options[$cat_id]['name']  = $value['cat_name'];
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
            $this->write_cat_cache('cat_option_static', $options);
        }
        
        private function write_cat_cache($cache_name, $caches) {
            if ((DEBUG_MODE & 2) == 2)
            {
                return false;
            }
            $cache_file_path = STATIC_CACHES . $cache_name . '.php';
            $content = "<?php\r\n";
            $content .= "\$data = " . var_export($caches, true) . ";\r\n";
            $content .= "?>";
            file_put_contents($cache_file_path, $content, LOCK_EX);
        }
        
        public function auto_create_ctb_depot_inout() {
            $this->load->model('depotio_model');
            $ins = get_sys_ctb_code(DEPOT_IO_TYPE_IN);
            $outs = get_sys_ctb_code(DEPOT_IO_TYPE_OUT);
            foreach ($ins as $key => $item) {
                if (!$this->depotio_model->has_ctb_depot_in($key, $item, TRUE)) {   
                    $ctb_depot_in = array(
                        "depot_in_code" => $item,
                        "depot_depot_id" => DEPOT_IO_IN_DEPOT_ID,
                        "depot_in_type" => CTB_DEPOT_IN_TYPE,
                        "depot_in_date" => $key . " ".CTB_DEPOT_IO_TIME,
                        "depot_in_number" => 0,
                        "depot_in_amount" => 0,
                        "audit_admin" => -1,
                        "audit_date"  => $key . " ".CTB_DEPOT_IO_TIME,
                        "create_admin" => -1,
                        "create_date" => date("Y-m-d H:i:s"),
                        "in_type" => 2
                    );
                    $this->depotio_model->add_ctb_depot_in($ctb_depot_in);
                }
            }
            foreach ($outs as $key => $item) {
                if (!$this->depotio_model->has_ctb_depot_out($key, $item, TRUE)) {     
                    $ctb_depot_out = array(
                        "depot_out_code" => $item,
                        "depot_depot_id" => DEPOT_IO_OUT_DEPOT_ID,
                        "depot_out_type" => CTB_DEPOT_OUT_TYPE,
                        "depot_out_date" => $key . " ".CTB_DEPOT_IO_TIME,
                        "depot_out_number" => 0,
                        "depot_out_amount" => 0,
                        "audit_admin" => -1,
                        "audit_date"  => $key . " ".CTB_DEPOT_IO_TIME,
                        "create_admin" => -1,
                        "create_date" => date("Y-m-d H:i:s"),
                        "out_type" => 2
                    );
                    $this->depotio_model->add_ctb_depot_out($ctb_depot_out);
                }
            }
        }
        public function kuaidi100(){
            //TODO 域名
            $call_back_url = ERP_HOST.'/kuaidi100/call_back';
            $key = KUAIDI100_KEY;
            $url=KUAIDI100_URL;
            $schema = 'json';
            //查询订单快递单号
            $this->load->model('order_shipping_status_model');
            $order_shipping_list = $this->order_shipping_status_model->get_order_shipping_list();
            foreach ($order_shipping_list as $order_shipping) {
                //需要post的参数
                $invoice_no=$order_shipping->invoice_no;
                $company=$order_shipping->shipping_company_100;
                $address = $order_shipping->address;
                $shipping_id = $order_shipping->shipping_id;
                
                $post_data = array();
                $post_data["schema"] = $schema ;
                $post_data["param"] = '{"company":"'.$company.'", "number":"'.$invoice_no.'", "to":"'.$address.'", "key":"'.$key.'", "parameters":{"callbackurl":"'.$call_back_url.'"}}';
                //发送订阅请求
                $o=""; 
                foreach ($post_data as $k=>$v)
                {
                    $o.= "$k=".urlencode($v)."&";		//默认UTF-8编码格式
                }
                $post_data_str=substr($o,0,-1);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL,$url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data_str);
                $result = curl_exec($ch);
                /*
                200: 提交成功
                701: 拒绝订阅的快递公司
                700: 不支持的快递公司
                600: 您不是合法的订阅者
                500: 服务器错误
                 */
                if($result){
                    //插入数据
                    $data = array();
                    $data['shipping_id']=$shipping_id;
                    $data['invoice_no']=$invoice_no;
                    $data['company']=$company;
                    $data['create_date']=date('Y-m-d H:i:s');
                    $this->order_shipping_status_model->insert($data);
                }
            }
        }
        
     /*
     * 顺丰订单确认接口
     *by jiang 
     */

    public function sf_order_confirm() {
        $this->load->model('package_sf_model');
        $order_confirm_list = $this->package_sf_model->get_sf_confirm();
        if (empty($order_confirm_list)){
        echo '没有需要确认的订单';
        exit;
        }
        foreach ($order_confirm_list as $key => $value) {
			//生成XML
            //$data = $this->package_sf_model->sf_confirm_backdata($value);
            if ($value['order_weight_unreal'] <= 0) $value['order_weight_unreal'] = 1.000; //没有重量按1公斤确认订单
            $xml = '<?xml version = "1.0" encoding = "UTF-8"?> <updateOrderRequest> '
					. '<orderid>' . $value['order_sn'] . '</orderid>'
					. ' <weight>' . $value['order_weight_unreal'] . '</weight> '
					. '<mailno>' . $value['mailno'] . '</mailno> '
                    . '<checkword>' . SF_CHECKWORD . '</checkword></updateOrderRequest>';
			$param = array('xml' => $xml);
			$wsdl = WEB_SERVICE_URL;
			$client = new SoapClient($wsdl, array('trace' => 1, 'uri' => '', 'encoding' => 'UTF-8'));
			$out = $client->confirmOrderService($param);
            $xml_object = simplexml_load_string($out->return);
            $result = $xml_object->result;
            $orderid = $xml_object->orderid;
            $remark = '';
            if ($result == 2) {//result字段说明：1-订单修改成功，2-订单修改失败
                $remark = $xml_object->remark;
            }
            $update_data = array('result'=>$result,'result_remark'=>$remark,'finish_time'=>date('Y-m-d H:i:s'));
            $this->package_sf_model->update_package_interface($update_data,$orderid);
        }
        echo '已完成';
    }
	
	/**
	 * 顺丰下单接口
	 *chenx 2013-05-27
	 */
	function sf_order_filter() {
		$this->load->model('package_sf_model');
        $order_info = $this->package_sf_model->sf_order_list(array(SF_SHIPPING_ID));
		if(empty($order_info)) {
			echo "没有需要派送的订单";
			return false;
		}
		$client = new SoapClient(WEB_SERVICE_URL,array('trace' => 1, 'uri' => '', 'encoding'=>'UTF-8'));
		foreach ($order_info as $order) {
			$this->db->trans_begin();
			
			//生成XML报文
			//$xml_str = $this->package_sf_model->create_sf_xml($order);
			$service_check_time = date('YmdHis',strtotime($order['confirm_date']));
			//顺丰订单号生成："B-"+宝贝购订单号+订单客审时间
			$sf_orderid = 'B-'.$order['order_sn'].'-'.$service_check_time;
			$d_phone = '';
			if(!empty($order['mobile']))    $d_phone .= "<d_mobile>".$order['mobile']."</d_mobile>";
			if(!empty($order['tel']))   $d_phone .= "<d_tel>".$order['tel']."</d_tel>";
			if(empty($d_phone)) continue;
			$zipcode = '';
			if(!empty($order['zipcode']))   $zipcode = "<d_postcode>".$order['zipcode']."</d_postcode>";
			$xml_str = '<?xml version="1.0" encoding="UTF-8"?>'
					.'<tporder>'
					.'<orderid>'.$sf_orderid.'</orderid>'
					.'<j_custid>'.SF_CUST_ID.'</j_custid>'
					.'<j_company>'.BBG_COMPANY.'</j_company>'
					.'<j_contact>'.BBG_CONTACT.'</j_contact>'
					.'<j_tel>'.BBG_TEL.'</j_tel>'
					.'<j_province>'.BBG_PROVINCE.'</j_province>'
					.'<j_city>'.BBG_CITY.'</j_city>'
					.'<j_county>'.BBG_COUNTY.'</j_county>'
					.'<j_address>'.BBG_ADDRESS.'</j_address>'
					.'<d_company>个人</d_company>'
					.'<d_contact>'.$order['consignee'].'</d_contact>'
					.$d_phone
					.$zipcode
					.'<d_province>'.$order['province_name'].'</d_province>'
					.'<d_city>'.$order['city_name'].'</d_city>'
					.'<d_county>'.$order['district_name'].'</d_county>'
					.'<d_address>'.$order['address'].'</d_address>'
					.'<cargo>衣物</cargo>'
					.'<insurance_amount>0</insurance_amount>'
					.'<checkword>'.SF_CHECKWORD.'</checkword>'
					.'<filter3>JYW57201</filter3>';
			if ($order['pay_id'] == 1 && $order['order_price'] > 0) $xml_str .= '<filter5>'.$order['order_price'].'</filter5>';
			$xml_str .= '</tporder>';
			
			$param = array('xml'=>$xml_str);
			$result_object = $client->filterOrderServiceForB2C($param);
			$result_xml = $result_object->return;
			unset($param);
			if(empty($result_xml)){
				echo "数据错误";
				return false;
			}
			$this->package_sf_model->order_data_process($result_xml,$order);
			$this->db->trans_commit();
		}
		echo "已完成";
	}
		
		/**
         * 团购自动上架
         */
        function auto_tuan_on() {
            $this->load->model('mami_tuan_model');
            $this->mami_tuan_model->auto_tuan_on();
        }
		
		/**
         * 团购自动下架
         */
        function auto_tuan_off() {
            $this->load->model('mami_tuan_model');
            $this->mami_tuan_model->auto_tuan_off();
        }
	
	//得到团购和今日团购数据，并放入cache
	public function getManiTuanData ()
	{
		$this->load->model('mami_tuan_model');
		$cotTuan = $this->mami_tuan_model->getTuanCount();
		$cotTuanToday = $this->mami_tuan_model->getTodayTuanCount();
		$cotBrand = $this->mami_tuan_model->getBrandNum();
		$this->memcache->delete('tuan_all_goods_num');
		$this->memcache->delete('tuan_today_goods_num');
		$this->memcache->delete('tuan_today_brands_num');
        $this->memcache->save('tuan_all_goods_num',$cotTuan,CACHE_TIME_TUAN);
		$this->memcache->save('tuan_today_goods_num',$cotTuanToday,CACHE_TIME_TUAN);
		$this->memcache->save('tuan_today_brands_num',$cotBrand,CACHE_TIME_TUAN);
	}
	
	
	
	public function new_goods_num_init(){
		define('NGN_INSERT_NUM_EACH', 1000 );
		$this->load->model('rush_third_model');
		// 选择已经在表中的img_id
		$rs = $this->rush_third_model->get_imgid();
		
		foreach($rs as $key => $value){
			if(!empty($value['rush_ids']) && $value['rush_count'] >= 900 ){
				$temp = explode(",", $value['rush_ids']);
				$rush_ids =  array_chunk($temp, 900);
	
				foreach($rush_ids as $ids){
					$this->rush_third_model->update_new_goods($value['rush_id'],implode(",", $ids));

				}
		}elseif(!empty($value['rush_ids']) && $value['rush_count'] < 900){
		
			$this->rush_third_model->update_new_goods($value['rush_id'],$value['rush_ids']);
		}
		}
		// 选择新增的img_id（排除已经在表中的img_id）
		$result = $this->rush_third_model->get_new_imgid();
		$this->rush_third_model->insert_new_imgid($result);

	}
	public function new_ingid_increment(){
		define('NGN_UPDATE_NUM_EACH', 100 );
		$current_timestamp = date('Y-m-d H:i:s');
		$cart_expired_time=20*60;
		$this->load->model('rush_third_model');
		$result = $this->rush_third_model->increment_imgid();
		if( empty($result) ) return true;
		$imgIds = Array();
		foreach( $result AS $key=>$row ){
			array_push( $imgIds, $row['img_id'] );
		}
		// 每500个一组
		$imgIds = array_chunk( $imgIds, NGN_UPDATE_NUM_EACH );
		
		// 每次循环500个更新到新表中
		$imgIdsIn = '';
		foreach( $imgIds AS $i=>$item ){
		
			$this->rush_third_model->increment_update($item,$cart_expired_time);
			$imgIdsIn .= ' AND img_id NOT '.$this->rush_third_model->db_create_in($item);
		}
		
		// 对没有更新到新表已经上架，标识下架
		$this->rush_third_model->increment_update_new($imgIdsIn);

		
	
	
	}

    function etao_increment2() {
		$this->load->helper('common');
        $this->load->model('etao_model');
		$this->config->load('etao_setting');
        //商家促销活劢信息列表
        $promotion = $this->etao_model->get_rush_list();
        $prom_act_url = ''; //商家促销活劢文件地址
        if (count($promotion) > 0) {
            $prom_act_url = '<promotion>' . $this->config->item('api_url') . '/etao/PromotionActivities.xml</promotion>';
        }
        $pmxml = '<?xml version="1.0" encoding="utf-8" ?>';
        $pmxml.='<root>';
        $pmxml.='<version>1.0</version>';
        $pmxml.='<modified>' . local_date('Y-m-d H:i:s') . '</modified>';
        $pmxml.='<seller_id>宝贝购</seller_id>';
        $pmxml.='<promotion_activities>';
        foreach ($promotion as $value) {
            $pmxml.='<activity>';
            $pmxml.='<pa_id>' . $value['rush_id'] . '</pa_id>';
            //$rush_name = htmlspecialchars(str_replace(' ', '_', $value['rush_index']));
			$rush_name = $value['rush_index'];
//            $rush_name = 111;
            $pmxml.='<activities_title>' . $rush_name . '</activities_title>';
            $pmxml.='<promotion_type>2</promotion_type>';
            //<!--促销类型，1=直降,2=限时贩，3=满就减，4=买就赠，5=多买多折，6=满就赠，7=买就减,参加多种活劢活劢之间用，隔开如：1,6--> 
            //$pmxml.='<activities_start>' . local_date("y-m-d h:m:i", $value['start_date']) . '</activities_start>';
            $pmxml.='<activities_start>' . $value['start_date'] . '</activities_start>';
            //<!--活劢开始时间, 格式：2011-01-17 12:00:05，二十四小时制，精确到秒--> 
            //$pmxml.='<activities_end>' . local_date("y-m-d h:m:i", $value['end_date']) . '</activities_end>';
            $pmxml.='<activities_end>' . $value['end_date'] . '</activities_end>';
            //<!--活劢结束时间, 格式：2011-01-17 12:00:09，二十四小时制，精确到秒--> 
            $jump_url = empty($value['jump_url']) ? $this->config->item('front_site_url') . 'rush-' . $value['rush_id'] . '.html' : $value['jump_url'];
            $pmxml.='<activities_url>' . $jump_url . '</activities_url>'; //<!--促销活劢链接--> 
            $pmxml.='<activities_image>' . $this->config->item('img_server_url') . $value['image_ing_url'] . '</activities_image>';
            $pmxml.='</activity>';
        }
        $pmxml.='</promotion_activities>';
        $pmxml.='</root>';
//header('Content-type: application/xml; charset=utf-8');
        file_put_contents($this->config->item('etao_api_path') . '/PromotionActivities.xml', $pmxml);
        $this->create_xml($prom_act_url);
    }

    /**
     * 生成增量索引文件及其商品文件
     * @param string $prom_act_url  促销信息xml文件地址 
     */
    private function create_xml($prom_act_url) {
		$this->load->helper('common');
		$this->config->load('etao_setting');
        $this->load->model('etao_model');
        $this_outer = $this->etao_model->get_add_pro();

        $last_outer = unserialize(file_get_contents($this->config->item('etao_api_path') . '/last_outer.dat'));
        $this_outer_array = array();
        $last_outer_array = array();

        foreach ($this_outer as $val) {
            $this_outer_array[] = $val['product_id'];
        }

        $need_add = array();
        $to_delete = array();
        $need_delete = array();
        if ($last_outer) {
            foreach ($last_outer as $val) {
                $last_outer_array[] = $val;
            }
            //选出需要新增的
            foreach ($this_outer as $val) {
                if (!in_array($val['product_id'], $last_outer_array)) {
                    $need_add[] = $val['product_id'];
                }
            }
            //选出需要删除的
            foreach ($last_outer as $key => $val) {
                if (!in_array($val, $this_outer_array)) {
                    $need_delete[] = $val;
                    $full_index = ceil(($key + 1) / 1000);
                    $to_delete[$full_index][] = $val;
                }
            }
        } else {
            //首次，全部为新增并且没有要删除的
            foreach ($this_outer as $val) {
                $need_add[] = $val['product_id'];
            }
        }
        /**
         * 写增量索引xml -------begin-------> 对应更新后的商品信息xml
         */
        if (!empty($need_add) || !empty($need_delete)) {
            $incre_index_xml = ''; //增量索引str
            $latest_pro_xml = ""; //上次的末尾文件
            $brand_info = $this->etao_model->getbrand();
            //1.======执行更新,生成增量索引xml
            //1.1增量索引xml begin
//        header('Content-type: application/xml; charset=utf-8');
            $incre_index_xml = '<?xml version="1.0" encoding="utf-8" ?>';
            $incre_index_xml.='<root>';
            $incre_index_xml.='<version>1.0</version>';
            $incre_index_xml.='<modified>' . local_date('Y-m-d H:i:s') . '</modified>';
            $incre_index_xml.='<seller_id>宝贝购</seller_id>';
            $incre_index_xml.='<cat_url>' . $this->config->item('api_url') . '/etao/SellerCats.xml</cat_url>';
            $incre_index_xml.=$prom_act_url; //商家促销活劢文件地址
            $incre_index_xml.='<dir>' . $this->config->item('api_url') . '/etao/product/</dir>';
            $incre_index_xml.='<item_ids>';

            //2.======执行更新,生成商品目录
            //2.1新增begin
            if ($need_add) {
                $product_detail = $this->etao_model->getpro($need_add);
                //        $incre_begin_add = ceil(count($last_outer_array) / 1000)+1; //全量索引中最末索引的后一个，给增量索引用
                $incre_begin_add = ceil(count($last_outer_array) / 1000);
                $incre_last = count($last_outer_array) % 1000 > 0 ? count($last_outer_array) % 1000 : 0;
                $pro_arr = array();
                if ($incre_last == 0) {//上次更新结束是整数，直接往后拼接数组
                    $pro_arr = array_chunk($product_detail, 1000);
                    $incre_begin_add += 1;
                } else {//上次更新结束有余数，先凑整后分割
                    $pro_arr = array_chunk(array_slice($product_detail, 1000 - $incre_last), 1000);
                    array_unshift($pro_arr, array_slice($product_detail, 0, 1000 - $incre_last));
                }
                $upl_add_str = '';
                foreach ($pro_arr as $key => $pro) {
                    if (count($pro) > 0) {
                        $incre_index_xml.='<outer_id action="upload">' . $incre_begin_add . '</outer_id>'; //增量索引outer_id
                        //增量索引商品xml

                        $upl_add_str .= '<?xml version="1.0" encoding="utf-8"?>';
                        $upl_add_str .= '<items>';
                        foreach ($pro as $value) {
                            $upl_add_str .= $this->getproxml($incre_begin_add, $value, $brand_info);
                        }
                        if ($key == 0 && count($pro) < 1000) {//连接上次的继续,该文件会有upl和dele
                            $latest_pro_xml = $upl_add_str;
                            $upl_add_str .= '</items>';
                            file_put_contents($this->config->item('etao_api_path') . '/product/' . $incre_begin_add . '.xml', $upl_add_str);
                            break;
                        }

                        $upl_add_str .= '</items>';
                        file_put_contents($this->config->item('etao_api_path') . '/product/' . $incre_begin_add . '.xml', $upl_add_str);
                        $incre_begin_add++;
                        unset($upl_add_str);
                    }
                }
            }
            //新增end
            //2.2删除begin
            if ($need_delete) {
                $product_detail = $this->etao_model->getpro($need_delete, TRUE); //所有要delete的商品
                foreach ($to_delete as $key => $val) {
                    $upl_dele_pro = array();
                    if (count($val) > 0) {
                        foreach ($val as $product_id) {
                            foreach ($product_detail as $detail) {
                                if ($product_id == $detail["product_id"]) {
                                    array_push($upl_dele_pro, $detail);
                                }
                            }
                        }
                    }
                    if (count($upl_dele_pro) > 0) {
                        $incre_index_xml.='<outer_id action="delete">' . $key . '</outer_id>'; //增量索引outer_id=>全量索引
                        //增量索引商品xml
                        $upl_del_str = "";
                        if (!empty($latest_pro_xml) && $key == count($to_delete)) {//上次最末的文件有无库存商品，其商品文件进行拼接
                            $upl_del_str .= $latest_pro_xml;
                        } else {
                            $upl_del_str .= '<?xml version="1.0" encoding="utf-8"?>';
                            $upl_del_str .= '<items>';
                        }
                        foreach ($upl_dele_pro as $value) {
                            $upl_del_str .= $this->getproxml($key, $value, $brand_info, true);
                        }
                        $upl_del_str .= '</items>';
                        file_put_contents($this->config->item('etao_api_path') . '/product/' . $key . '.xml', $upl_del_str);
                        unset($upl_del_str);
                    }
                    unset($upl_dele_pro);
                }
            }
            //删除end
            //1.1增量索引xml continue
            $incre_index_xml.='</item_ids>';
            $incre_index_xml.='</root>';
            //header('Content-type: application/xml; charset=utf-8');
            file_put_contents($this->config->item('etao_api_path') . '/IncrementIndex.xml', $incre_index_xml);
            //增量索引xml end 
        }
        //记录所有商品id(全量和增量中包含的 )
        $all_upld_ids = array_merge($last_outer_array, $need_add);
        file_put_contents($this->config->item('etao_api_path') . '/last_outer.dat', serialize($all_upld_ids));
    }

    /**
     * 根据商品信息获取其xml
     *
     * @param string   $outer_id   增量索引中的索引
     * @param array    $value
     * @param array    $brand_info
     * @param boolean  $is_delete  是否是下线商品
     * @return string  商品xml
     */
    private function getproxml($outer_id, $value, $brand_info, $is_delete = FALSE) {
	$this->load->helper('common');
	$this->config->load('etao_setting');
    $pxml='';
    $pxml.='<item>
                    <seller_id>宝贝购</seller_id>
                    <outer_id>' . $outer_id . '</outer_id>
                    <title><![CDATA[' . $brand_info[$value['brand_id']] . $value['product_name'] . ']]></title>';
    if ($is_delete) {
        $pxml .= '<available>0</available>';
    }
    if ($value['promote_start_date'] < time() && $value['promote_end_date'] > time() && $value['shop_price'] > 0
            && $value['shop_price'] < $value['market_price']) {
        $value['drate'] = $this->etao_model->price_format($value['shop_price'] / $value['market_price']);
        $pxml.='
                        <type>discount</type>
                        <price>' . $value['market_price'] . '</price>
                        <pa_ids>' . $value['rush_id'] . '</pa_ids>
                        <promotion_type>2</promotion_type>';

        $pxml.='<discount>
                         <start>' . $value['promote_start_date'] . '</start>
                         <end>' . $value['promote_end_date'] . '</end>
                         <dprice>' . $value['shop_price'] . '</dprice>
                         <drate>' . $value['drate'] . '</drate> 
                         <ddesc><![CDATA[' . $value['goods_desc'] . ']]></ddesc>
                         </discount>';
    } else {
        $pxml.='<type>fixed</type>';
        $pxml.='<price>' . $value['promote_price'] . '</price>';
    }
	$goods_desc = $value['product_desc'];
    //$goods_desc = mb_strlen($value['product_desc']) > 1000 ? mb_strcut($value['product_desc'], 0, 1000, 'utf-8') : $value['product_desc'];
    $pxml.='<desc><![CDATA[' . $goods_desc . ']]></desc>
                    <brand><![CDATA[' . $brand_info[$value['brand_id']] . ']]></brand>
                    <tags><![CDATA[' . $brand_info[$value['brand_id']] . ']]></tags>
                    <image>' . $this->config->item('img_server_url') . $value['img_318_318'] . '</image>
                    <scids>' . $value['category_id'] . '</scids>
                    <post_fee>10.00</post_fee>
                    <showcase>true</showcase>
                    <href>' . $this->config->item('front_site_url') . 'product-' . $value['product_id'] . '.html</href>
                    </item>';
    return $pxml;
    }

    public function etao_fullindex() {
		$this->load->helper('common');
		$this->config->load('etao_setting');
        $this->load->model('etao_model');
        //商家促销活劢信息列表
        $promotion = $this->etao_model->get_rush_list();
        $prom_act = ''; //商家促销活劢文件地址
        if (count($promotion) > 0) {
            $prom_act = '<promotion>' . $this->config->item('api_url') . '/etao/PromotionActivities.xml</promotion>';
        }

        $pmxml = "";
        $pmxml = '<?xml version="1.0" encoding="utf-8" ?>';
        $pmxml.='<root>';
        $pmxml.='<version>1.0</version>';
        $pmxml.='<modified>' . local_date('Y-m-d H:i:s') . '</modified>';
        $pmxml.='<seller_id>宝贝购</seller_id>';
        $pmxml.='<promotion_activities>';
        foreach ($promotion as $value) {
            $pmxml.='<activity>';
            $pmxml.='<pa_id>' . $value['rush_id'] . '</pa_id>';
            //$rush_name = htmlspecialchars(str_replace(' ', '_', $value['rush_index']));
			$rush_name = $value['rush_index'];
//            $rush_name = 111;
            $pmxml.='<activities_title>' . $rush_name . '</activities_title>';
            $pmxml.='<promotion_type>2</promotion_type>';
            //<!--促销类型，1=直降,2=限时贩，3=满就减，4=买就赠，5=多买多折，6=满就赠，7=买就减,参加多种活劢活劢之间用，隔开如：1,6--> 
            //$pmxml.='<activities_start>' . local_date("y-m-d h:m:i", $value['start_date']) . '</activities_start>';
            $pmxml.='<activities_start>' . $value['start_date'] . '</activities_start>';
            //<!--活劢开始时间, 格式：2011-01-17 12:00:05，二十四小时制，精确到秒--> 
            //$pmxml.='<activities_end>' . local_date("y-m-d h:m:i", $value['end_date']) . '</activities_end>';
            $pmxml.='<activities_end>' . $value['end_date'] . '</activities_end>';
            //<!--活劢结束时间, 格式：2011-01-17 12:00:09，二十四小时制，精确到秒--> 
            $jump_url = empty($value['jump_url']) ? $this->config->item('front_site_url') . 'rush-' . $value['rush_id'] . '.html' : $value['jump_url'];
            $pmxml.='<activities_url>' . $jump_url . '</activities_url>'; //<!--促销活劢链接-->
            $pmxml.='<activities_image>' . $this->config->item('img_server_url') . $value['image_ing_url'] . '</activities_image>';
            $pmxml.='</activity>';
        }
        $pmxml.='</promotion_activities>';
        $pmxml.='</root>';

        file_put_contents($this->config->item('etao_api_path') . '/PromotionActivities.xml', $pmxml);
        //======执行更新，获取促销活劢=======end==============================
        //======3.执行更新,生成商品目录=======begin==============================
        $cat_detail = $this->etao_model->getcat();
        $catxml = '<?xml version="1.0" encoding="utf-8"?>';
        $catxml.='<root>';
        $catxml.='<version>1.0</version>';
        $catxml.='<modified>' . local_date('Y-m-d H:i:s') . '</modified>';
        $catxml.='<seller_id>宝贝购</seller_id>';
        $catxml.='<seller_cats>';
        foreach ($cat_detail as $cat) {
            $catxml.='<cat>';
            $catxml.='<scid>' . $cat['category_id'] . '</scid>';
            $catxml.='<name><![CDATA[' . $cat['category_name'] . ']]></name>';
            $catxml.='<cats>';
            $catsec = $this->etao_model->getcat($cat['category_id']);
            foreach ($catsec as $catsecvalue) {
                $catxml.='<cat>';
                $catxml.='<scid>' . $catsecvalue['category_id'] . '</scid>';
                $catxml.='<name><![CDATA[' . $catsecvalue['category_name'] . ']]></name>';
                $catxml.='</cat>';
            }
            $catxml.='</cats>	';
            $catxml.='</cat>';
            unset($catsec);
            unset($catsecvalue);
        }
        $catxml.='</seller_cats>';
        $catxml.='</root>';

        file_put_contents($this->config->item('etao_api_path') . '/SellerCats.xml', $catxml);
        //======执行更新,生成商品目录=======end==============================
        //======4.执行更新,生成商品包xml=======begin==============================

        $product_detail = $this_outer = $this->etao_model->get_add_pro();
        $brand_info = $this->etao_model->getbrand();
        $pro = array_chunk($product_detail, 1000);
//======2.执行更新,更新商品数据=======begin==============================
        $xml = '<?xml version="1.0" encoding="utf-8" ?>';
        $xml.='<root>';
        $xml.='<version>1.0</version>';
        $xml.='<modified>' . local_date('Y-m-d H:i:s') . '</modified>';
        $xml.='<seller_id>宝贝购</seller_id>';
        $xml.='<cat_url>' . $this->config->item('api_url') . '/etao/SellerCats.xml</cat_url>';
        $xml.=$prom_act; //商家促销活劢文件地址
        $xml.='<dir>' . $this->config->item('api_url') . '/etao/product/</dir>';
        $xml.='<item_ids>';
        for ($i = 1, $j = 0; $i <= count($pro); $i++, $j++) {
            if (count($pro[$j]) > 0) {
                $xml.='<outer_id action="upload">' . $i . '</outer_id>';
                //======4执行更新,生成商品包xml=======begin==============================
                $pxml = '<?xml version="1.0" encoding="utf-8"?>';
                $pxml.='<items>';
                foreach ($pro[$j] as $value) {
                    $product[] = $value['product_id'];
                    $pxml.='<item>
                    <seller_id>宝贝购</seller_id>
                    <outer_id>' . $i . '</outer_id>
                    <title><![CDATA[' .$brand_info[$value['brand_id']] ."---". $value['product_name'] . ']]></title>';

                    if ($value['promote_start_date'] < time() && $value['promote_end_date'] > time() && $value['shop_price'] > 0 && $value['shop_price'] < $value['market_price']) {
                        $value['drate'] = $this->etao_model->price_format($value['shop_price'] / $value['market_price']);
                        $pxml.='
                        <type>discount</type>
                        <price>' . $value['market_price'] . '</price>
                        <pa_ids>' . $value['rush_id'] . '</pa_ids>
                        <promotion_type>2</promotion_type>';
                        $pxml.='<discount>
                         <start>' . $value['promote_start_date'] . '</start>
                         <end>' . $value['promote_end_date'] . '</end>
                         <dprice>' . $value['shop_price'] . '</dprice>
                         <drate>' . $value['drate'] . '</drate> 
                         <ddesc><![CDATA[' . $value['product_desc'] . ']]></ddesc>
                         </discount>';
                    } else {
                        $pxml.='<type>fixed</type>';
                        $pxml.='<price>' . $value['promote_price'] . '</price>';
                    }
                    //$goods_desc = mb_strlen($value['product_desc']) > 1000 ? mb_strcut($value['product_desc'], 0, 1000, 'utf-8') : $value['product_desc'];
                    $goods_desc = $value['product_desc'];
					$pxml.='<desc><![CDATA[' . $goods_desc . ']]></desc>
                    <brand><![CDATA[' . $brand_info[$value['brand_id']] . ']]></brand>
                    <tags><![CDATA[' . $brand_info[$value['brand_id']] . ']]></tags>
                    <image>' . $this->config->item('img_server_url') . $value['img_318_318'] . '</image>
                    <scids>' . $value['category_id'] . '</scids>
                    <post_fee>10.00</post_fee>
                    <showcase>true</showcase>
                    <href>' . $this->config->item('front_site_url') . 'product-' . $value['product_id'] . '.html</href>
                    </item>';
                }
                $pxml.='</items>';

                file_put_contents($this->config->item('etao_api_path') . '/product/' . $i . '.xml', $pxml);
                unset($pxml);
                //======4执行更新,生成商品包xml=======end==============================          
            }
        }
        $xml.='</item_ids>';
        $xml.='</root>';
//执行：保存历史商品dat:将本次所有的商品写入last_outer.dat中保存
        file_put_contents($this->config->item('etao_api_path') . '/last_outer.dat', serialize($product));
//header('Content-type: application/xml; charset=utf-8');
        file_put_contents($this->config->item('etao_api_path') . '/FullIndex.xml', $xml);
//======2执行更新,更新商品数据=======end==============================
    }
    // 前台数据缓存
    public function webfront_data_cache(){
        $this->load->model('product_model');
        $this->load->model('order_model');
        
        $cate_list = $this->product_model->get_nav();
        
        $end_time = date("Y-m-d");
        
        $cate_topten_brand = array();
        $cate_toptwentyfive_goods = array();
        $cate_topseven_provider = array();
        $nav_subtype = array();
        //根据一级分类
        foreach ($cate_list as $cat) {
            $time = strtotime($end_time);
            //按一级分类,３０天内店铺销量(客审口径)前7的店铺 及 该店铺销量(客审口径)前5的商品
            $start_time = strtotime("-30 day", $time);
            $start_time = date("Y-m-d", $start_time);			
            $param = array('start_time' => $start_time, 'end_time' => $end_time, 'pcate_id' => $cat['category_id']);
            $provider_result = $this->order_model->get_sales_topseven_provider($param);
            $cate_topseven_provider[$cat['category_id']] = array('cat_name' => $cat['category_name'], 'provider_list' => $provider_result);
            foreach ($provider_result as $provider) {
                $param['provider_id'] = $provider['provider_id'];
                $provider_goods_result = $this->order_model->get_sales_topfive_provider_goods($param);
                $cate_topfive_provider_goods[$cat['category_id']][$provider['provider_id']] = $provider_goods_result;
            }
            
            //获取每个一级分类下，最近3天销量前10名的品牌logo的url         
            $start_time = strtotime("-3 day", $time);
			$param['start_time'] = date("Y-m-d", $start_time);
            $brand_result = $this->order_model->get_sales_topten_brand($param);
            $cate_topten_brand[$cat['category_id']] = $brand_result;
            
            // 最近3天销量(客审口径)前25的商品, 其中1-20位给原”畅销精品”使用, 21-25位给原”热卖推荐”使用          
            $cate_toptwentyfive_goods = $this->order_model->get_sales_toptwentyfive_goods($param);
            /*$i = 0;
            foreach ($goods_result as $row) {
                 if ($i < 20) {
                     $cate_toptwentyfive_goods[$cat['category_id']]['top'][] = $row;
                 } else {
                     $cate_toptwentyfive_goods[$cat['category_id']]['last'][] = $row;
                 }
                 $i++;
            }*/
			
			if (!empty($cate_toptwentyfive_goods)) {
			    $this->memcache->delete('cate_toptwentyfive_goods_'.$cat['category_id']);
			    $this->memcache->save('cate_toptwentyfive_goods_'.$cat['category_id'], serialize($cate_toptwentyfive_goods), CATE_DATA_CACHE_TIME);
            }
			// 获取前台导航的二级分类
            $nav_subtype_arr = $this->order_model->get_front_nav_subtype($cat['category_id']);
            $nav_subtype[$cat['category_id']] = array('cat_name' => $cat['category_name'], 'goods_type' => $nav_subtype_arr);
        }
        unset($param['pcate_id']);
        // 最近3天销量(客审口径)前5的商品
        $all_topfive_goods = $this->order_model->get_sales_topfive_goods($param);
        
        // 最近3天销量(客审口径)前8的品牌
        $all_topeight_brand = $this->order_model->get_sales_topeight_brand($param);
        
         // 按审核时间有库存的显示最新上线的5个商品
         $onsale_last_goods = $this->order_model->get_onsale_last_goods();
        
        // 清楚之前的KEY值
        $this->memcache->delete('cate_topten_brand');
        //$this->memcache->delete('cate_toptwentyfive_goods');
        $this->memcache->delete('cate_topseven_provider');
        $this->memcache->delete('cate_topfive_provider_goods');
        $this->memcache->delete('all_topfive_goods');
        $this->memcache->delete('all_topeight_brand');
        $this->memcache->delete('onsale_last_goods');
        $this->memcache->delete('front_nav_subtype');
        
        // 存入memcache
        $this->memcache->save('cate_topten_brand', serialize($cate_topten_brand), CATE_DATA_CACHE_TIME);
        //$this->memcache->save('cate_toptwentyfive_goods', serialize($cate_toptwentyfive_goods), CATE_DATA_CACHE_TIME);
        $this->memcache->save('cate_topseven_provider', serialize($cate_topseven_provider), CATE_DATA_CACHE_TIME);
        $this->memcache->save('cate_topfive_provider_goods', serialize($cate_topfive_provider_goods), CATE_DATA_CACHE_TIME);
        $this->memcache->save('all_topfive_goods', serialize($all_topfive_goods), CATE_DATA_CACHE_TIME);
        $this->memcache->save('all_topeight_brand', serialize($all_topeight_brand), CATE_DATA_CACHE_TIME);
        $this->memcache->save('onsale_last_goods', serialize($onsale_last_goods), CATE_DATA_CACHE_TIME);
        $this->memcache->save('front_nav_subtype', serialize($nav_subtype), CATE_DATA_CACHE_TIME);
    }

    public function proc_provider_product_num() {
	    $this->load->model('provider_model');
		$this->provider_model->proc_provider_product_num();
	}

	//将上个小时的访问量写入数据库
	// NOTICE: 此函数，需要一个小时运行一次
	// @type: product/article/course
	// pv_@type_@date('dH')         : 小时内的产品/文章ids
	// pv_@type_@date('dH')_$id     : 小时内的产品/文章访问量
	// @author: v wang, @date:20150916.1615
	public function write_hour_pv(){
		$this->load->model('product_model');
		$this->load->model('product_access_model');
		$this->load->model('wordpress_model');

        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $hour = date('H',strtotime('-1hour'));// 上一个小时
        $add_time = date('Y-m-d H:i:s');
        $types = array('product','course','article');
        $pv_data = array();

		for($j=0;$j<=2;$j++){
	        $key = 'pv_'.$types[$j].'_'.$day.$hour."_ids";
	        // 取得一个小时的IDS
	        $ids = $this->memcache->get( $key );
	        
	        if( !empty($ids) ){
	        	foreach ($ids as $i => $id) {
	        		# 获取产品的小时访问量，写入DB
	        		$key_product = 'pv_'.$types[$j].'_'.$day.$hour.'_'.$id;
	        		$pv = $this->memcache->get( $key_product );
	        		if( !empty($pv) ){
	        			if( $id > 0 ){
	        				if($types[$j] == 'article'){
	        					//TODO 将小时访问量，累加到wordpress文章的访问量表
			        			$this->wordpress_model->wordpress_num_update($id,$pv);

			        			//TODO, 将文章小时访问量写入 产品访问量统计表。新建表, type: article, name: title
			        			$title = $this->wordpress_model->wordpress_sn_name($id);
			        			$product_sn_name = $title->post_title;

	        				}else if($types[$j] == 'product' || $types[$j] == 'course'){

		        				//TODO 将小时访问量，累加到产品的访问量表
			        			$this->product_model->product_num_update($id,$pv);

			        			//TODO, 将产品小时访问量写入 产品访问量统计表。新建表，type:product, name:sn+name
			        			$sn_name = $this->product_model->product_sn_name($id);
			        			$product_sn_name = $sn_name->product_sn.'_'.$sn_name->product_name;
			        		}

	        			}else{
	        				$product_sn_name = $types[$j];
	        			}

	        			$pv_data[] = array(
	        						'product_id' => $id,
	        						'type' => $types[$j],
	        						'name' => $product_sn_name,
	        						'year' => $year,
	        						'month' => $month,
	        						'day' => $day,
	        						'hours' => $hour,
	        						'pv' => $pv,
	        						'add_time' => $add_time
	        			);
	        			
	        		}
	        		$this->memcache->delete($key_product);// 删除商品的小时id key
	        	}
	        }
	        $this->memcache->delete($key); // 删除商品的小时ids key
		}

        //批量插入访问记录
        $this->product_access_model->insert($pv_data);
	}


	/**
	 *	计算过去N个小时内的产品销量，更新到db
	 *	$n_hour = 3 过去的3小时内的产品销量 
	 *	建议定时每，$n_hour小时的49分执行一次
	 */
	public function write_hours_ps($n_hour = 3){

		$this->load->model('order_model');
		$this->load->model('product_model');
		//计算时间范围
		
		$now_time = date('Y-m-d H:00:00'); //当前时间
		$pass_time = date('Y-m-d H:00:00',strtotime('-'.$n_hour.' hours'));//过去的N小时 时间

		//获得产品的销量
		$row = $this->order_model->get_time_order($now_time,$pass_time);
		$payment_row = get_pair($row['payment_row'], 'product_id', 'num' ); // array( key=> value )
		$refund_row = get_pair($row['refund_row'], 'product_id', 'num' );

		foreach( $refund_row AS $id=>$num ){
			if( isset($payment_row[$id]) ) $payment_row[$id] -= $num;
			$payment_row[$id] = -$num;
		}

		// 组成数据
		$update_data = array();
		foreach ($payment_row as $key => $value) {
			array_push($update_data, array($key,$value));
		}

		//更新db产品的销量
		$this->product_model->ps_num_update($update_data);

	}
        //订单作废
        public function order_invalid(){
		    $this->load->model('order_model');
            $filter = array();
            $filter['date_end'] = date("Y-m-d H:i:s", time() - ORDER_INVALID_TIME);           
            $order_list = $this->order_model->get_unpay_timeout_order($filter);
            if (empty($order_list))
                exit('order_cnt:0');
            $url = ERP_HOST . '/order_api/invalid';
            $url = 'http://192.168.20.114/order_api/invalid';
            $params['auto_invalid'] = 1;
            $params['sys_user'] = 1;
            $header = array('X-Requested-With: XMLHttpRequest');
            foreach ($order_list as $order) {
                $params['order_id']		= $order['order_id'];
                $r = curl_post($url, $params, $header);
                $r2 = json_decode($r, true);
                print_r($r2);
            }           
        }
        
    /**
     * 顺丰下单接口
     */
    function db_order_filter() {
        $this->load->model('package_sf_model');
        $order_info = $this->package_sf_model->sf_order_list(array(DB_SHIPPING_ID, DBDS_SHIPPING_ID));
        if(empty($order_info)) {
            echo "没有需要派送的订单";
            return false;
        }

        foreach ($order_info as $order) {
            $this->db->trans_begin();
            $params = array();
            $post_data = array();
            $params['logisticCompanyID'] = 'DEPPON';
            $params['logisticID'] = $order['order_sn'];
            $params['orderSource'] = DB_CUST_CODE;
            $params['serviceType'] = 3;
            $params['customerCode'] = DB_CUST_ID;
            $params['customerID'] = DB_CUST_CODE;
            
            $params['sender']['name'] = '上海欧思蔚奥医疗器材有限公司';
            $params['sender']['phone'] = '400-9905-920';
            $params['sender']['province'] = '上海市';
            $params['sender']['city'] = '上海市';
            $params['sender']['county'] = '浦东新区';
            $params['sender']['address'] = '周浦镇 建韵路618号3幢楼305室';
            
            $params['receiver']['name'] = $order['consignee'];           
            if (!empty($order['mobile'])) {
                $params['receiver']['mobile'] = $order['mobile'];
            } else {
                $params['receiver']['phone'] = $order['tel'];
            }
            $params['receiver']['province'] = $order['province_name'];
            $params['receiver']['city'] = $order['city_name'];
            $params['receiver']['county'] = $order['district_name'];
            $params['receiver']['address'] = $order['address'];
            
            $params['gmtCommit'] = $order['create_date'];
            $params['cargoName'] = '医疗器械';
            $params['payType'] = 2;
            $params['transportType'] = 'PACKAGE';
	    $params['insuranceValue'] = 300;
            if ($order['goods_price'] >= 300) $params['insuranceValue'] = intval($order['goods_price']/300)*300;
            if ($order['pay_id'] == PAY_ID_COD && $order['order_price'] > 0){
                $params['transportType'] = 'RCP';
                $params['codValue'] = $order['order_price'];
                $params['codType'] = 3;
                $params['reciveLoanAccount'] = '98060154740008605';
                $params['accountName'] = '上海欧思蔚奥医疗器材有限公司';
            }
            $params['vistReceive'] = 'Y';
            $params['deliveryType'] = 3;
            $params['backSignBill'] = 0;
            
            $post_data['timestamp'] = get_microsecond();
            $post_data['companyCode'] = DB_CUST_CODE;
            $post_data['params'] = json_encode($params);
            $post_data['digest'] = base64_encode(md5($post_data['params'].DB_API_KEY.$post_data['timestamp']));
            $url = 'http://dpapi.deppon.com/dop-interface-sync/dop-standard-ewborder/expressSyncNewSieveOrder.action?'.http_build_query($post_data);

            $result = curl($url);
            $rs_arr = json_decode($result, true);
	    
            $set = array();
            $set['shipping_id'] = $order['shipping_id'];
            $set['order_id'] = $order['order_id'];
            $set['order_sn'] = $order['order_sn'];
            $set['mailno'] = $rs_arr['mailNo'];
            $set['dist_code'] = $rs_arr['sortingParam']['bigPen'];
            $set['filter_status'] = $rs_arr['resultCode'];
            $set['filter_remark'] = $rs_arr['reason'];
            $this->package_sf_model->set_shipping_package($set);
            $this->db->trans_commit();
        }
        echo "已完成";
    }
    
    function weixin_setting_cache(){
        $this->load->model('weixin_settings_model');
        $result = $this->weixin_settings_model->filter_all();
        $key_prefix = 'wechat_share_';
        foreach ($result as $data) {
            $mem_val = serialize(array('title' => $data['title'], 'describe' => $data['describe'], 'file_url' => $data['file_url']));
            $this->memcache->save($key_prefix . $data['key_code'], $mem_val, WEIXIN_CACHE_TIME);
        }
        echo "已完成";
    }
}
