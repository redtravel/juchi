<?php
class batch_img extends CI_Controller
{
	
	function __construct()
	{
            parent::__construct();
            set_time_limit(0);
            $this->load->library('image_lib');
            $this->load->helper('file');
            $this->config->load('product');
	}
        
	private function create_thumb($sorce_img)
	{
            $thumb_arr = $this->config->item('product_fields');
            $base_dir = dirname($sorce_img);
            $sorce_img_arr = explode(".", $sorce_img);
	    $result =true;
            foreach ($thumb_arr as $field=>$thumb) {
                $gallery_thumb = $sorce_img_arr[0].$thumb['sufix'].".".$sorce_img_arr[1];


                if (file_exists($gallery_thumb)){ //echo $gallery_thumb;echo ' exists!'."\n"; 
continue; }
echo $gallery_thumb; echo  "not exists.";
                $this->image_lib->initialize(array(
                        'source_image' => $sorce_img,
                        'new_image' => $gallery_thumb,
                        'quality'=>85,
                        'maintain_ratio'=>FALSE,
                        'width'=>$thumb['width'],
                        'height'=>$thumb['height']
                ));
                $this->image_lib->resize();
                $this->image_lib->clear();
                if($thumb['wm']){
                    $this->image_lib->initialize(array(
                            'source_image' => $gallery_thumb,
                            'quality'=>85,
                            'create_thumb'=>FALSE,
                            'wm_type'=>'overlay',	
                            'wm_overlay_path'=>APPPATH.'../public/images/'.$thumb['wm_file'],
                            'wm_hor_offset'=>$thumb['wm_x'],
                            'wm_vrt_offset'=>$thumb['wm_y'],
                    ));
                    $this->image_lib->watermark();
                    $this->image_lib->clear();
                }
                if (file_exists($gallery_thumb)){ echo ' generate ok!'. "\n"; }
		else{ echo ' generate failed. '. "\n"; $result = false;}
            }
return $result;
	}
        
        function do_exe(){
            
            $path1 = '/var/www/yyw_new/images/goodsImages';
            $path1 = '/var/www/yyw_new/images/style/uploads/images';
            $path1 = '/var/www/yyw_new/images/uploads/images';
            
            if (!file_exists($path1))                                                                                                                                                                                                                     
                    exit;                                                                                                                                                                                                                               
            //$this->read_file($path1);
            $this->read_file2($path1);
            
            /*if (!file_exists($path2))                                                                                                                                                                                                                     
                    exit;                                                                                                                                                                                                                               
            change_file_name($path2);
            
            if (!file_exists($path3))                                                                                                                                                                                                                     
                    exit;                                                                                                                                                                                                                               
            change_file_name($path3);*/           
        }
    private function read_file2($directory){

        $fileSPLObjects =  new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory),
                RecursiveIteratorIterator::CHILD_FIRST
            );
        $a = read_file('/tmp/gg.php');
        eval($a);
        $thumb_arr = $this->config->item('product_fields');

        try {
            foreach( $fileSPLObjects as $fullFileName => $fileSPLObject ) {
                    if( $fileSPLObject->isDir() ){
                            if( strlen($fileSPLObject->getFilename())>2 ){
                                    array_push( $gg, $fullFileName );
                                    print $fullFileName . "\n";
                                    write_file( '/tmp/gg.php',"\$gg=".var_export($gg,true).';' );
                            }
                            else continue;
                    }elseif(strpos($fileSPLObject->getFilename(), '_') === FALSE) {                                                                                                                                                                                                  
                            if(in_array( dirname($fullFileName), $gg)) continue;
                            $size = intval($fileSPLObject->getSize()/1024);
                            if( $size > 610 ){
                                    echo $fullFileName.' size='. $size.'k'."\n";
                                    continue;
                            }


                    $base_dir = dirname($fullFileName);
                    $sorce_img_arr = explode(".", $fullFileName);

                    $i=0; $l=sizeof($thumb_arr);
                    foreach ($thumb_arr as $field=>$thumb) {
                        $gallery_thumb = $sorce_img_arr[0].$thumb['sufix'].".".$sorce_img_arr[1];
                        if (file_exists($gallery_thumb)){
                                $i++;
                        }
                    }
                    if( $i < $l ) $this->create_thumb($fullFileName);
                    }

            }
        }
        catch (UnexpectedValueException $e) {
            printf("Directory [%s] contained a directory we can not recurse into", $directory);
        }
    }
        
        private function read_file($path){
            $robj = new DirectoryIterator($path);
            foreach ($robj as $obj){
                if ($obj->isDot()) continue;
                if ($obj->isDir()){
                    $this->read_file($obj->getPathname());
                } else {                                                                                                                                                                                                                        
                    $file_n = $obj->getFilename(); 
                    if(strpos($file_n, '_') === FALSE) {                                                                                                                                                                                                  
                            //echo $file_n."\r\n";                                                                                                                                                                                                  
                            //echo $obj->getPathname()."\r\n";                                                                                                                                                                                      
                            //echo $obj->getPath();                                                                                                                                                                                                 
                            //$tmp = explode(".", $file_n);                                                                                                                                                                                           
                            //$tmp2 = explode("_", $tmp[0]);                                                                                                                                                                                          
                            //$file_n2 = $tmp2[0].".".$tmp[1].".".$tmp2[1]."x".$tmp2[1].".".$tmp[1];
                            //$file_n2 = $tmp2[0].".".$tmp[1].".".$tmp2[1]."x".$tmp2[1].".".$tmp[1];
                            //echo $file_n2."\r\n";                                                                                                                                                                                                 
                            //echo $obj->getPath()."/".$file_n2;                                                                                                                                                                                    
                            //rename($obj->getPathname(), $obj->getPath()."/".$file_n2);
                        $this->create_thumb($obj->getPathname());
                    }                                                                                                                                                                                                                           
                }                                                                                                                                                                                                                               

            }
        }       

    public function cron_for_check_img() {
        $base_path = CREATE_IMAGE_PATH;
	$this->load->model('product_model');

	$result = $this->product_model->get_check_gallery_rows(50);

        foreach ($result as $key => $value) {
            $fullFileName = $base_path . $value->img_url;
	    echo $value->product_id."##".$fullFileName."\n";
            $img_exist = 0;
            if(file_exists($fullFileName)) {
                $result = $this->create_thumb(realpath($fullFileName));
                $img_exist = $result ? 1: 3;
                
            } else {
                $img_exist = 2;
            }   
	    $this->product_model->update_check_gallery_rows(array($img_exist, $value->product_id));
        }        
        
    }
}
                                                                                                                                                                                                                                            
?>