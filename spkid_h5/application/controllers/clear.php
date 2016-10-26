<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Class Clear
 * @author Justin
 */
class Clear extends CI_Controller
{
	function __construct() {
        parent::__construct();
    }
    function memcache() {
        $this->config->load('memcached', TRUE, TRUE);
        $config = $this->config->config['memcached'];
        
        $data = array();
        foreach($config as $index => $memcache) {
            $data["MEMCACHE_SERVERS"][] = $memcache['hostname'] . ":" . $memcache['port'];
        }
        $this->load->view('memcache/memcache', $data);
    }
    function init()
    {
    	$this->config->load('memcached', TRUE, TRUE);
    	$config = $this->config->config['memcached'];
    	$select = array();
    	foreach($config as $index => $memcache)
    	{
    		$mem = new Memcache();
    		$mem->connect($memcache['hostname'], $memcache['port']);
    		$select[$index]['mem_obj'] = $mem;
    		$select[$index]['hostname'] = $memcache['hostname'];
    		$select[$index]['port'] = $memcache['port'];
    	}
    	return $select;
    }

    function index()
    {
    	$data['options'] = $this->toDoMemCache();
    	$this->load->view('clear_memcache', $data);
    }

    function todo()
    {
    	$keyName = $this->uri->segment(4);
    	$type = $this->uri->segment(3);
    	$this->memCacheWork($keyName, $type);
    	$data['options'] = $this->toDoMemCache();
    	$data['getkey'] = $keyName;
    	$this->load->view('clear_memcache', $data);
    }

    function memCacheWork($keyName, $type)
    {
    	$options = array();
    	$select = $this->init();
    	foreach($select as $k => $v)
    	{
    		$host = $select[$k]['hostname'];
    		$port = $select[$k]['port'];
    		$items = $select[$k]['mem_obj']->getExtendedStats ('items');
	    	$items = $items["$host:$port"]['items'];
	    	$i = 0;
	    	foreach($items as $key => $values){
		        $number = $key;
		        $str = $select[$k]['mem_obj']->getExtendedStats ("cachedump",$number,0);
		        $line = $str["$host:$port"];
		        if( is_array($line) && count($line)>0)
		        {
		            foreach($line as $loop => $value)
		            {
						if($loop == $keyName)
	                	{
                    		if($type == 1)
                    		{
								echo "<font color='red'>MEMCACHE HOST: ".$host.":".$port."</font><br/>";
                        		print_r($select[$k]['mem_obj']->get($loop));
								echo "<br/>";
                    		}else{
                        		$select[$k]['mem_obj']->set($loop,null,0);
                    		}
	                	}
		            }
		        }
	    	}	
    	}
    }

    function toDoMemCache()
    {
    	$options = array();
    	$select = $this->init();
    	foreach($select as $k => $v)
    	{
    		$host = $select[$k]['hostname'];
    		$port = $select[$k]['port'];
    		$items = $select[$k]['mem_obj']->getExtendedStats ('items');
	    	$items = $items["$host:$port"]['items'];
	    	$i = 0;
	    	foreach($items as $key => $values){
		        $number = $key;
		        $str = $select[$k]['mem_obj']->getExtendedStats ("cachedump",$number,0);
		        $line = $str["$host:$port"];
		        if( is_array($line) && count($line)>0)
		        {
		            foreach($line as $loop => $value)
		            {
                                if (substr($loop, 0, 8) === "SESSION_")
                                    continue;
						$i++;
		                //$select.="<option value=".$loop.">".$loop."</option>";
		                array_push($options, $loop);
						if( $i > 10 ) break;
		            }
		        }
	    	}	
    	}
	    return $options;
    }

}
