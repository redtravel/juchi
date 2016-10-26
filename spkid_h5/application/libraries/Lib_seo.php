<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Lib_seo {
	function __construct(){
		$this->CI = &get_instance();
		$this->load = $this->CI->load;
	}

	private function format_seo(&$seo, $parms = array()) {

        if( empty($parms) ) $parms = array();
		$parms['site_name'] = SITE_NAME_MOBILE;
		if (isset($seo) && !empty($seo) && !empty($parms)) {
			
				foreach ($seo as $key => &$value) {
					
					$value = str_replace(array_keys($parms), array_values($parms), $value);
				}			
		}
	}

	function get_seo_by_pagetag($pagetag, $parms = array()) {
		$cache_key = $pagetag . '_seo';		
		$this->load->model('seo_model');			
		$is_preview = isset($_GET['is_preview']) && $_GET['is_preview']== 1 ?TRUE:FALSE; 
		if ($is_preview) {
			$seo = $this->CI->seo_model->get_page_seo($pagetag);
			$this->CI->cache->save($cache_key, $seo);
			$this->format_seo($seo, $parms);
			return $seo;
		}

		$seo = $this->CI->cache->get($cache_key);
		if($seo == FALSE) {			
			$seo = $this->CI->seo_model->get_page_seo($pagetag);
			$this->CI->cache->save($cache_key, $seo);
		}
		$this->format_seo($seo, $parms);
		return $seo;
	}
}
?>
