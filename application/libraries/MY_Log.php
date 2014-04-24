<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

date_default_timezone_set('Asia/Bangkok');

class MY_Log extends CI_Log {

	var $CI;

	var $system_log_path;
	var $_system_threshold	= 1;
	var $_system_enabled	= TRUE;
	var $_system_levels	= array('ERROR' => '1', 'DEBUG' => '2',  'INFO' => '3', 'ALL' => '4');
	
	var $user_log_path;
	var $_user_threshold = 1;
	var $_user_enabled = TRUE;
	var $_user_levels = array('ERROR' => '1', 'DEBUG' => '2',  'INFO' => '3', 'ALL' => '4');
	
	var $db_log_path;
	var $_db_enabled = TRUE;
	
	var $_date_fmt	= 'Y-m-d H:i:s';

	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function MY_Log()
	{
		$config =& get_config();
		
		$this->system_log_path = ($config['system_log_path'] != '') ? $config['system_log_path'] : BASEPATH.'logs/system';
		$this->user_log_path = ($config['user_log_path'] != '') ? $config['user_log_path'] : BASEPATH.'logs/user';
		$this->db_log_path = ($config['database_log_path'] != '') ? $config['database_log_path'] : BASEPATH.'logs/db';
		
		if ( ! is_dir($this->system_log_path) OR ! is_really_writable($this->system_log_path))
		{
			$this->_system_enabled = FALSE;
		}
		
		if ( ! is_dir($this->user_log_path) OR ! is_really_writable($this->user_log_path))
		{
			$this->_user_enabled = FALSE;
		}
		
		if ( ! is_dir($this->db_log_path) OR ! is_really_writable($this->db_log_path))
		{
			$this->_db_enabled = FALSE;
		}
		
		if (is_numeric($config['system_log_threshold']))
		{
			$this->_system_threshold = $config['system_log_threshold'];
		}
		
		if (is_numeric($config['user_log_threshold']))
		{
			$this->_user_threshold = $config['user_log_threshold'];
		}
			
		if ($config['log_date_format'] != '')
		{
			$this->_date_fmt = $config['log_date_format'];
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @access	public
	 * @param	string	the error level
	 * @param	string	the error message
	 * @param	bool	whether the error is a native PHP error
	 * @return	bool
	 */		
	function write_log($level = 'error', $msg, $php_error = FALSE, $log_type = 'system')
	{		
		if ($log_type === 'system' && $this->_system_enabled === FALSE)
		{
			return FALSE;
		}
		else if ($log_type === 'user' && $this->_user_enabled === FALSE)
		{
			return FALSE;
		}
		else if ($log_type === 'db' && $this->_db_enabled === FALSE)
		{
			return FALSE;
		}
		
		// Override log level of database log to INFO only
		if ($log_type === 'db')
		{
			$level = "INFO";
		}
	
		$level = strtoupper($level);
		
		if ( ! isset($this->_levels[$level]) OR ($log_type === 'system' && $this->_levels[$level] > $this->_system_threshold))
		{
			return FALSE;
		}
		else if ( ! isset($this->_levels[$level]) OR ($log_type === 'user' && $this->_levels[$level] > $this->_user_threshold)) 
		{
			return FALSE;
		}
		
		$log_path = "";
		if ($log_type === 'system') 
		{
			$log_path = $this->system_log_path;
		}
		else if ($log_type === 'user') 
		{
			$log_path = $this->user_log_path;
		}
		else if ($log_type == "db")
		{
			$log_path = $this->db_log_path;
		}
		else
		{
			return FALSE;
		}
	
		$filepath = $log_path.'/log-'.date('Y-m-d').EXT;
		$message  = '';
		
		if ( ! file_exists($filepath))
		{
			$message .= "<"."?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
		}
			
		if ( ! $fp = @fopen($filepath, FOPEN_WRITE_CREATE))
		{
			return FALSE;
		}
		
		if ($log_type == "db")
		{
			$CI =& get_instance();
			$msg = "[" .  $CI->session->userdata("USERNAME") . " " . $CI->input->ip_address() . "] " . $msg;
		}

		$message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."<br/>\n";
		
		flock($fp, LOCK_EX);	
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);
	
		@chmod($filepath, FILE_WRITE_MODE); 		
		return TRUE;
	}

}
// END Log Class

/* End of file Log.php */
/* Location: ./system/libraries/Log.php */