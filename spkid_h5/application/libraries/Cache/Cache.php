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
 * Fclub Caching Class 
 *
 * @package		Fclub
 * @subpackage          Libraries
 * @category            Core
 * @author		Jasper
 * @link		
 */
class MY_Cache extends CI_Driver_Library {

    protected $valid_drivers = array(
          'my_cache_apc'
        , 'my_cache_file'
        , 'my_cache_memcached'
        , 'my_cache_memcache'
        , 'my_cache_dummy'
        , 'my_cache_fclubcache'
        , 'my_cache_sessioncache'
    );
    protected $_cache_path = NULL;  // Path of cache files (if file-based cache)
    protected $_adapter = 'fclubcache';
    protected $_backup_driver;

    // ------------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param array
     */
    public function __construct($config = array()) {
        if (!empty($config)) {
            $this->_initialize($config);
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Get 
     *
     * Look for a value in the cache.  If it exists, return the data 
     * if not, return FALSE
     *
     * @param 	string	
     * @return 	mixed		value that is stored/FALSE on failure
     */
    public function get($id) {
        return $this->{$this->_adapter}->get($id);
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Save
     *
     * @param 	string		Unique Key
     * @param 	mixed		Data to store
     * @param 	int		Length of time (in seconds) to cache the data
     *
     * @return 	boolean		true on success/false on failure
     */
    public function save($id, $data, $ttl = 60) {
        return $this->{$this->_adapter}->save($id, $data, $ttl);
    }

    // ------------------------------------------------------------------------

    /**
     * Delete from Cache
     *
     * @param 	mixed		unique identifier of the item in the cache
     * @return 	boolean		true on success/false on failure
     */
    public function delete($id) {
        return $this->{$this->_adapter}->delete($id);
    }

    // ------------------------------------------------------------------------

    /**
     * Clean the cache
     *
     * @return 	boolean		false on failure/true on success
     */
    public function clean() {
        return $this->{$this->_adapter}->clean();
    }

    // ------------------------------------------------------------------------

    /**
     * Cache Info
     *
     * @param 	string		user/filehits
     * @return 	mixed		array on success, false on failure	
     */
    public function cache_info($type = 'user') {
        return $this->{$this->_adapter}->cache_info($type);
    }

    // ------------------------------------------------------------------------

    /**
     * Get Cache Metadata
     *
     * @param 	mixed		key to get cache metadata on
     * @return 	mixed		return value from child method
     */
    public function get_metadata($id) {
        return $this->{$this->_adapter}->get_metadata($id);
    }

    // ------------------------------------------------------------------------

    /**
     * Initialize
     *
     * Initialize class properties based on the configuration array.
     *
     * @param	array 	
     * @return 	void
     */
    private function _initialize($config) {
        $default_config = array(
            'adapter'
        );

        foreach ($default_config as $key) {
            if (isset($config[$key])) {
                $param = '_' . $key;

                $this->{$param} = $config[$key];
            }
        }

        if (isset($config['backup'])) {
            if (in_array('cache_' . $config['backup'], $this->valid_drivers)) {
                $this->_backup_driver = $config['backup'];
            }
        }
    }

    // ------------------------------------------------------------------------

    /**
     * Is the requested driver supported in this environment?
     *
     * @param 	string	The driver to test.
     * @return 	array
     */
    public function is_supported($driver) {
        static $support = array();

        if (!isset($support[$driver])) {
            $support[$driver] = $this->{$driver}->is_supported();
        }

        return $support[$driver];
    }

    // ------------------------------------------------------------------------

    /**
     * __get()
     *
     * @param 	child
     * @return 	object
     */
    public function __get($child) {
        $obj = parent::__get($child);

        if (!$this->is_supported($child)) {
            $this->_adapter = $this->_backup_driver;
        }

        return $obj;
    }

    // ------------------------------------------------------------------------
}

// End Class

/* End of file Cache.php */
/* Location: ./system/libraries/Cache/Cache.php */