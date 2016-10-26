<?php

/**
 * 导入城市编码
 */
class Region_match extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->admin_id = $this->session->userdata('admin_id');
        if (!$this->admin_id) {
            redirect('index/login');
        }
        ini_set('max_execution_time', '0');
    }

    public function index() {
        $file = APPPATH . '../public/import/city_code/city_code.xml';
        if (!file_exists($file)) {
            sys_msg('导入文件不存在', 1);
        }
        $content = file_get_contents($file);
        $dom = new SimpleXMLElement($content);
        $dom->registerXPathNamespace('c', 'urn:schemas-microsoft-com:office:spreadsheet');
        $rows = $dom->xpath('//c:Workbook//c:Worksheet//c:Table//c:Row');
        try {
            $this->db->trans_begin();
            //$data = array();
            $data_res = array();
            foreach ($rows as $key => $row) {
                if ($key == 0)
                    continue;
                $city = array();
                foreach ($row as $cell) {
                    $city[] = trim(strval($cell->Data));
                }
//                    var_dump($city);
//                    echo "#####";
                //jiang start
                $province = trim($city[0]);
                $city = trim($city[1]);
                $full_province = trim($city[2]);
                $full_city = trim($city[3]);
                if (empty($province) || empty($city) || empty($full_province) || empty($full_city)) {
//                    sys_msg('省、市、省（全称）、市（全称）不能为空', 1);
                }
                //省份数据整合
                $province_arr = $this->add_qqtuan_region($province, $full_province, 1);
                if ($province_arr['error'] == 1) {
                    $error_arr[] = $province_arr['region_name'];
                    continue;
                }
                if (empty($province_arr['parent_id'])) {
                    $this->db->trans_rollback();
                }
                //市区数据整合
                $city_arr = $this->add_qqtuan_region($city, $full_city, 2, $province_arr['parent_id']);
                if ($city_arr['error'] == 1) {
                    $error_arr[] = $province . " - " . $city_arr['region_name'];
                    continue;
                }
                if (empty($city_arr['parent_id'])) {
                    $this->db->trans_rollback();
                }
                //jiang end

                $data_res[$city[0]][$city[1]]['city_code'] = $city[2];
                $data_res[$city[0]][$city[1]]['province_name'] = $city[3];
//					$data_res[$city[0]][$city[1]]['city_name'] = $city[4];
                //$data[] = $city_data;
            }
            if (!empty($error_arr) && count($error_arr) > 0) {
                echo "FCLUB系统缺少以下地区：<br><br>";
                foreach ($error_arr as $err) {
                    echo $err . "<br><br>";
                }
                exit;
            } else {
                //$sql = "UPDATE `fc_flc_qqtuan_region` SET region_name2 = NULL WHERE region_name = region_name2 ;";
                //$GLOBALS['db']->query($sql);
//                $GLOBALS['db']->query('COMMIT');
                $this->db->trans_commit();
                echo "地区匹配成功！";
            }
            /*
              foreach ($data as $val) {
              foreach ($val as $p=>$va) {
              foreach ($va as $c=>$v) {
              $data_res[$p][$c] = $v;
              }
              }
              }
             */
            /* 		
              $sql = "SELECT p.region_id AS province_id,p.region_name AS province_name,c.region_id AS city_id,c.region_name AS city_name
              FROM ty_region_info AS p
              LEFT JOIN ty_region_info AS c ON c.`parent_id` = p.`region_id`
              WHERE p.region_type = 1 AND c.region_type = 2 ;";
              $query = $this->db->query($sql);
              $region_list = $query->result_array();
              $error_regions = array();
              $sql = "INSERT INTO ty_city_code (city_code,province_id,city_id,shipping_id) VALUES ";
              foreach ($region_list as $region) {
              if(!empty($data_res[$region['province_name']][$region['city_name']])) {
              $city_code = $data_res[$region['province_name']][$region['city_name']];
              $sql .= "('$city_code',".$region['province_id'].",".$region['city_id'].",8),";
              } else {
              $error_regions[] = $region['province_name']." - ".$region['city_name'];
              continue;
              }
              }
             */
            //$sql = substr($sql,0,-1);
            //$this->db->query($sql);
            //$this->db->trans_commit();
        } catch (Exception $e) {
            $this->db->trans_rollback();
            //sys_msg("导入失败",1);
            echo "导入失败";
        }
        if (!empty($error_regions) || count($error_regions) > 0) {
            echo "不能匹配地区：<br><br>";
            foreach ($error_regions as $region) {
                echo $region . "<br><br>";
            }
        } else {
            //sys_msg("导入成功",0);
            echo "导入成功";
        }
    }

    private function add_qqtuan_region($region, $full_region, $region_type, $parent_id = 0) {
        $sql = "SELECT region_id FROM ".$this->db->dbprefix('region_info')." WHERE region_name = '" . $region . "' AND region_type = $region_type ;";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $flc_region_id = $result['region_id'];
        $reslut = array();
        $reslut['error'] = 0;
        if (empty($flc_region_id)) {
            $reslut['error'] = 1;
            $reslut['region_name'] = $region;
            return $reslut;
        }
        $sql = "SELECT region_id,parent_id FROM ".$this->db->dbprefix('third_region_info')." WHERE region_name LIKE '%" . $region . "%' AND parent_id = $parent_id ;";
        $query_third = $this->db->query($sql);
        $qqtuan_region = $query_third->row_array($sql);
        if (!empty($qqtuan_region)) {
            $sql = "UPDATE ".$this->db->dbprefix('third_region_info')." SET flc_region_id = $flc_region_id,region_name2 = '" . $full_region . "' WHERE region_id = " . $qqtuan_region['region_id'] . " ;";
            $this->db->query($sql);
            $reslut['parent_id'] = $qqtuan_region['region_id'];
        } else {
            $sql = "INSERT INTO ".$this->db->dbprefix('third_region_info')." (parent_id, region_name, ty_region_id, region_type) VALUES ($parent_id, '" . $full_region . "', $flc_region_id, $region_type) ;";
            $this->db->query($sql);
            $reslut['parent_id'] = $this->db->insert_id();
        }
        return $reslut;
    }

}