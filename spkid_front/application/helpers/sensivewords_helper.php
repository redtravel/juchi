<?php
	function filter($content){
		$CI = &get_instance();
		$CI->load->config('sensivewords');
		$sensivewords = $CI->config->item('sensivewords');

        if($content=="") return false;
        
        empty($sensivewords)?$sensivewords:"";
        foreach ( $sensivewords as $row){
            if (false !== strstr($content, $row)) return false;
        }
        return true;
	}



?>