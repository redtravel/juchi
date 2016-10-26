<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {

	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		log_message('debug', "Model Class Initialized");
	}

	/**
	 * __get
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string
	 * @access private
	 */
	function __get($key)
	{
		$CI =& get_instance();
                switch ($key) {
                	case "db_wp":
                        if (empty($CI->db_wp)) {
                            $CI->db_wp = $CI->load->database('wordpress', TRUE, TRUE);
                        }
                        return $CI->db_wp;
                        break;
                    case "db_r":
                        if (empty($CI->db_r)) {
                            $CI->db_r = $CI->load->database('default_r', TRUE, TRUE);
                        }
                        return $CI->db_r;
                        break;
                    default :
                        return $CI->$key;
                        break;
                }
	}
}
// END Model Class

/* End of file Model.php */
/* Location: ./system/core/Model.php */