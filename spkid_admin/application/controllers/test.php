<?php

class test extends CI_Controller{
    function __construct(){
        parent::__construct();
    }

    function export_excel()
    {
        $arr1_title=array('a','b');
        $arr1[]=array('a'=>'1','b'=>2);
        $arr2_title[]=array('a','b','c');
        $arr2[]=array('a'=>'1','b'=>2,'c'=>3);
        $arr2[]=array('a'=>'1','b'=>2,'c'=>3);
        $data['arr_data']=array($arr1_title,$arr1,$arr2_title,$arr2);
        $this->load->helpers('excel');
        export_excel_xml('aa',$data['arr_data']);
    }

}
?>
