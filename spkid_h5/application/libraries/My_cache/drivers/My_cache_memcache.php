<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Fclub
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		Fclub
 * @author		Jasper
 * @copyright           Copyright (c) 2006 - 2011 Fclub, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 2.0
 * @filesource	
 */
// ------------------------------------------------------------------------

/**
 * Fclub Memcache Caching Class 
 *
 * @package		Fclub
 * @subpackage          Libraries
 * @category            Core
 * @author		Jasper
 * @link		
 */
class MY_Cache_memcache extends CI_Driver {

    private $_memcache; // Holds the memcached object
    protected $_memcache_conf = array(
        'default' => array(
            'default_host' => '127.0.0.1',
            'default_port' => 11211,
            'default_weight' => 1
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
        $data = $this->_memcache->get($id);

        return (is_array($data)) ? $data[0] : FALSE;
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
        return $this->_memcache->set($id, array($data, time(), $ttl), 0, $ttl);
    }

    // ------------------------------------------------------------------------

    /**
     * Delete from Cache
     *
     * @param 	mixed		key to be deleted.
     * @return 	boolean 	true on success, false on failure
     */
    public function delete($id) {
        return $this->_memcache->delete($id);
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the Cache
     *
     * @return 	boolean		false on failure/true on success
     */
    public function clean() {
        return $this->_memcache->flush();
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
        foreach ($this->_memcache_conf as $key => $item) {
            if (!is_array($item)) {
                continue;
            }
            $cache = new Memcache();
            $cache->connect($item["hostname"], $item["port"]);
            $arr = $cache->getstats();
            $arr["hostname"] = $item["hostname"];
            $arr["port"] = $item["port"];
            $arr["weight"] = $item["weight"];
            $cache_stats[$key] = $arr;
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
        $stored = $this->_memcache->get($id);

        if (count($stored) !== 3) {
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

        if ($CI->config->load('memcache', TRUE, TRUE)) {
            if (is_array($CI->config->config['memcache'])) {
                $this->_memcache_conf = NULL;

                foreach ($CI->config->config['memcache'] as $name => $conf) {
                    $this->_memcache_conf[$name] = $conf;
                }
            }
        }

        $this->_memcache = new Memcache();

        foreach ($this->_memcache_conf as $name => $cache_server) {
            if (!array_key_exists('hostname', $cache_server)) {
                $cache_server['hostname'] = $this->_default_options['default_host'];
            }
            if (!array_key_exists('port', $cache_server)) {
                $cache_server['port'] = $this->_default_options['default_port'];
            }

            if (!array_key_exists('weight', $cache_server)) {
                $cache_server['weight'] = $this->_default_options['default_weight'];
            }

            $this->_memcache->addServer(
                    $cache_server['hostname'], $cache_server['port'], $cache_server['weight']
            );
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
        if (!extension_loaded('memcache')) {
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