<?php

/**
 * 导入城市编码
 */
class Auto_city_code extends CI_Controller {
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
                $in_code = $this->is_utf8($city[0]);
                if ($in_code == 'gbk') {
                    echo('请更改文件编码为UTF-8');
                    exit;
                }
                if (empty($city[0]) || empty($city[1]) || empty($city[2]) || empty($city[3])) {
                    echo '省、市、省（全称）、市（全称）不能为空';exit;
                }
                //省份数据整合
                $province_arr = $this->add_qqtuan_region($city[0], $city[2], 1);
                if ($province_arr['error'] == 1) {
                    $error_arr[] = $province_arr['region_name'];
                    continue;
                }
                if (empty($province_arr['parent_id'])) {
                    echo "导入失败";
                    $this->db->trans_rollback();
                }
                //市区数据整合
                $city_arr = $this->add_qqtuan_region($city[1], $city[3], 2, $province_arr['parent_id']);
                if ($city_arr['error'] == 1) {
                    $error_arr[] = $city[0] . " - " . $city_arr['region_name'];
                    continue;
                }
                if (empty($city_arr['parent_id'])) {
                    $this->db->trans_rollback();
                }
                
            }
            if (!empty($error_arr) && count($error_arr) > 0) {
                echo "宝贝购系统缺少以下地区：<br><br>";
                foreach ($error_arr as $err) {
                    echo $err . "<br><br>";
                }
                exit;
            } else {
                $this->db->trans_commit();
                echo "地区匹配成功！";
            }
        } catch (Exception $e) {
            $this->db->trans_rollback();
            echo "导入失败";
        }
    }

    private function add_qqtuan_region($region, $full_region, $region_type, $parent_id = 0) {
//        self::$count = self::$count + 1;
        $sql = "SELECT region_id FROM " . $this->db->dbprefix('region_info') . " WHERE region_name = '" . $region . "' AND region_type = $region_type ;";
        $query = $this->db->query($sql);
        $result = $query->row_array();
        $ty_region_id = count($result) ? $result["region_id"] : '';
        $reslut = array();
        $reslut['error'] = 0;
        if (empty($ty_region_id)) {
            $reslut['error'] = 1;
            $reslut['region_name'] = $region;
            return $reslut;
        }
        $sql = "SELECT region_id,parent_id FROM " . $this->db->dbprefix('third_region_info') . " WHERE region_name LIKE '%" . $full_region . "%' AND parent_id = $parent_id ;";
        $query_third = $this->db->query($sql);
        $third_region = $query_third->row_array();
        if (!empty($third_region)) {
            $sql = "UPDATE " . $this->db->dbprefix('third_region_info') . " SET ty_region_id = $ty_region_id,region_name = '" . $full_region . "' WHERE region_id = " . $third_region['region_id'] . " ;";
            $this->db->query($sql);
            $reslut['parent_id'] = $third_region['region_id'];
        } else {
            $sql = "INSERT INTO " . $this->db->dbprefix('third_region_info') . " (parent_id, region_name, ty_region_id, region_type) VALUES ($parent_id, '" . $full_region . "', $ty_region_id, $region_type) ;";
            $result = $this->db->query($sql);
            $reslut['parent_id'] = $this->db->insert_id();
        }
        return $reslut;
    }

    /**
     * 判断字符串是否为utf8编码 方法三
     * @param <type> $string
     * @return <type>
     */
    private function is_utf8($string) {
        if (preg_match("/^([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}/", $string) == TRUE || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){1}$/", $string) == TRUE || preg_match("/([" . chr(228) . "-" . chr(233) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}[" . chr(128) . "-" . chr(191) . "]{1}){2,}/", $string) == TRUE
        ) {
            return 'utf-8';
        } else {

            return 'gbk';
        }
    }

}