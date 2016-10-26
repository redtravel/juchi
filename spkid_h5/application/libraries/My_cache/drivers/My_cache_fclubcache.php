<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Fclub
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Jasper
 * @copyright           Copyright (c) 2006 - 2011 Fclub, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource	
 */
// ------------------------------------------------------------------------
/**
 * Fclub Fclubcache Caching Class 
 *
 * @package		Fclub
 * @subpackage          Libraries
 * @category            Core
 * @author		Jasper
 * @link		
 */
class MY_Cache_fclubcache extends CI_Driver {

    private $isRandom = true;
    private $_fclubcache = array(); // Holds the memcached object
    private $_fclubcache_count = 0x0000;
    protected $_fclubcache_conf = array(
        array(
            'type' => 'memcached',
            'default' =>
            array(
                'host' => '127.0.0.1',
                'port' => 11211,
                'weight' => 1
            )
        )
    );

    // ------------------------------------------------------------------------	

    /**
     * Fetch from cache
     *
     * @param 	mixed		unique key id
     * @return 	mixed		data on success/false on failure
     */
    public function get($id) {
        $index = $this->isRandom ? rand(0x0000, $this->_fclubcache_count - 0x0001) : 0x0000;
        $data = @$this->_fclubcache[$index]->get($id);
        if (is_array($data)) {
            return $data[0x0000];
        } else {
            for ($i = 0x0000; $i < $this->_fclubcache_count; $i++) {
                if ($i == $index) {
                    continue;
                }
                $data = @$this->_fclubcache[$i]->get($id);
                if (is_array($data)) {
                    return $data[0x0000];
                }
            }
        }
        return FALSE;
    }

    // ------------------------------------------------------------------------

    /**
     * Save
     *
     * @param 	string		unique identifier
     * @param 	mixed		data being cached
     * @param 	int			time to live
     * @return 	boolean 	true on success, false on failure
     */
    public function save($id, $data, $ttl = 60) {
        $cache_state = array();
        for ($i = 0x0000; $i < $this->_fclubcache_count; $i++) {
            if (get_class($this->_fclubcache[$i]) == "Memcache") {
                $cache_state[$i] = @$this->_fclubcache[$i]->set($id, array($data, time(), $ttl), 0, $ttl);
            } else if (get_class($this->_fclubcache[$i]) == "Memcached") {
                $cache_state[$i] = @$this->_fclubcache[$i]->set($id, array($data, time(), $ttl), $ttl);
            } else {
                $cache_state[$i] = false;
            }
        }
        return $cache_state;
    }

    // ------------------------------------------------------------------------

    /**
     * Delete from Cache
     *
     * @param 	mixed		key to be deleted.
     * @return 	boolean 	true on success, false on failure
     */
    public function delete($id) {
        $cache_state = array();
        for ($i = 0x0000; $i < $this->_fclubcache_count; $i++) {
            $cache_state[$i] = @$this->_fclubcache[$i]->delete($id);
        }
        return $cache_state;
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the Cache
     *
     * @return 	boolean		false on failure/true on success
     */
    public function clean() {
        $cache_state = array();
        for ($i = 0x0000; $i < $this->_fclubcache_count; $i++) {
            $cache_state[$i] = @$this->_fclubcache[$i]->flush();
        }
        return $cache_state;
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Info
     *
     * @param 	null		type not supported in memcached
     * @return 	mixed 		array on success, false on failure
     */
    public function cache_info($type = NULL) {
        $cache_stats = array();
        for ($i = 0x0000; $i < $this->_fclubcache_count; $i++) {
            $type_ = $this->_fclubcache_conf[$i]["type"];
            if ($type_ == "memcache") {
                foreach ($this->_fclubcache_conf[$i] as $key => $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $cache = new Memcache();
                    @$cache->connect($item["host"], $item["port"]);
                    $arr = @$cache->getstats();
                    $arr["host"] = $item["host"];
                    $arr["port"] = $item["port"];
                    $arr["weight"] = $item["weight"];
                    $cache_stats[$type_ . $i][$key] = $arr;
                }
            } else {
//                $cache_stats[$i] = $this->_fclubcache[$i]->getStats();
                foreach ($this->_fclubcache_conf[$i] as $key => $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $cache = new Memcache();
                    @$cache->connect($item["host"], $item["port"]);
                    $arr = @$cache->getstats();
                    $arr["host"] = $item["host"];
                    $arr["port"] = $item["port"];
                    $arr["weight"] = $item["weight"];
                    $cache_stats[$type_ . $i][$key] = $arr;
                }
            }
        }
        return $cache_stats;
    }

    // ------------------------------------------------------------------------

    /**
     * Get Cache Metadata
     *
     * @param 	mixed		key to get cache metadata on
     * @return 	mixed		FALSE on failure, array on success.
     */
    public function get_metadata($id) {
        $index = $this->isRandom ? rand(0x0000, $this->_fclubcache_count - 0x0001) : 0x0000;
        $stored = @$this->_fclubcache[$index]->get($id);

        if (count($stored) !== 0x0003) {
            for ($i = 0x0000; $i < $this->_fclubcache_count; $i++) {
                if ($i == $index) {
                    continue;
                }
                $stored = @$this->_fclubcache[$i]->get($id);
                if (count($stored) == 0x0003) {
                    break;
                }
            }
        }
        if (count($stored) !== 0x0003) {
            return FALSE;
        }
        list($data, $time, $ttl) = $stored;

        return array(
            'expire' => $time + $ttl,
            'mtime' => $time,
            'data' => $data
        );
    }

    // ------------------------------------------------------------------------

    /**
     * Setup memcached.
     */
    private function _setup_memcached() {
        // Try to load memcached server info from the config file.
        $CI = & get_instance();
        if ($CI->config->load('fclubcache', TRUE, TRUE)) {
            if (is_array($CI->config->config['fclubcache'])) {
                $this->_fclubcache_conf = NULL;

                foreach ($CI->config->config['fclubcache'] as $name => $conf) {
                    $this->_fclubcache_conf[$name] = $conf;
                }
            }
        }

        foreach ($this->_fclubcache_conf as $key => $item) {
            if (isset($item["type"])) {
                if (strtolower($item["type"]) == "memcache") {
                    $this->_fclubcache[$key] = new Memcache();
                } else if (strtolower($item["type"]) == "memcached") {
                    $this->_fclubcache[$key] = new Memcached();
                } else {
                    $this->_fclubcache[$key] = new Memcache();
                }
            } else {
                $this->_fclubcache[$key] = new Memcache();
            }
            $this->_fclubcache_count += 1;
            foreach ($item as $name => $cache_server) {
                if (!is_array($cache_server)) {
                    continue;
                }
                $this->_fclubcache[$key]->addServer(
                        $cache_server['host'], $cache_server['port'], $cache_server['weight']
                );
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Is supported
     *
     * Returns FALSE if memcached is not supported on the system.
     * If it is, we setup the memcached object & return TRUE
     */
    public function is_supported() {
        if (!extension_loaded('memcache') && !extension_loaded("memcached")) {
            log_message('error', 'The Memcached Extension must be loaded to use Memcached Cache.');

            return FALSE;
        }

        $this->_setup_memcached();
        return TRUE;
    }

    // ------------------------------------------------------------------------
}

// End Class

/* End of file Cache_memcached.php */
/* Location: ./system/libraries/Cache/drivers/Cache_memcached.php */
