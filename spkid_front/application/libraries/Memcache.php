<?php
/**
* Memcache Class
*/
class CI_Memcache
{
	private $_memcache;
	private $_prefix;
	protected $_default_conf 	= array(
			'hostname'		=> '127.0.0.1',
			'port'			=> 11211,
			'weight'		=> 1,
			'persist'		=> FALSE
		);
	
	function __construct()
	{
		$this->_memcache = new Memcache();
		
		// Try to load memcache server info from the config file.
		$CI =& get_instance();
        $CI->config->load('memcache',true);
		$CI->load->helper('memcache');
		foreach ($CI->config->item('memcache') as $conf) {
			
			if(!array_key_exists('hostname', $conf)) $conf['hostname'] = $this->_default_conf['hostname'];
			
			if(!array_key_exists('port', $conf)) $conf['port'] = $this->_default_conf['port'];
			
			if(!array_key_exists('weight', $conf)) $conf['weight'] = $this->_default_conf['weight'];
			
			if(!array_key_exists('persist', $conf)) $conf['persist'] = $this->_default_conf['persist'];
			
			$this->_memcache->addServer(
				$conf['hostname'],$conf['port'],$conf['persist'],$conf['weight']
			);
		}	
		$this->_prefix = $CI->config->item('memcache_prefix');	
	}

	// ------------------------------------------------------------------------	

	/**
	 * Fetch from cache
	 *
	 * @param 	mixed		unique key id
	 * @return 	mixed		data on success/false on failure
	 */	
	public function get($id)
	{	
		$data = $this->_memcache->get($this->_prefix.$id);
		
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
	public function save($id, $data, $ttl = 60)
	{
		return $this->_memcache->add($this->_prefix.$id, array($data, time(), $ttl), FALSE, $ttl);
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Delete from Cache
	 *
	 * @param 	mixed		key to be deleted.
	 * @return 	boolean 	true on success, false on failure
	 */
	public function delete($id)
	{
		return $this->_memcache->delete($this->_prefix.$id);
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Clean the Cache
	 *
	 * @return 	boolean		false on failure/true on success
	 */
	public function clean()
	{
		return $this->_memcache->flush();
	}

	// ------------------------------------------------------------------------

	/**
	 * Cache Info
	 *
	 * @param 	null		type not supported in memcache
	 * @return 	mixed 		array on success, false on failure
	 */
	public function cache_info($type = NULL)
	{
		return $this->_memcache->getStats();
	}

	// ------------------------------------------------------------------------
	
	/**
	 * Get Cache Metadata
	 *
	 * @param 	mixed		key to get cache metadata on
	 * @return 	mixed		FALSE on failure, array on success.
	 */
	public function get_metadata($id)
	{
		$stored = $this->_memcache->get($this->_prefix.$id);

		if (count($stored) !== 3)
		{
			return FALSE;
		}

		list($data, $time, $ttl) = $stored;

		return array(
			'expire'	=> $time + $ttl,
			'mtime'		=> $time,
			'data'		=> $data
		);
	}
}