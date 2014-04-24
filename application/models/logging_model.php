<?php

class Logging_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database("logging");
	}
	
	
	function info($message, $username)
	{
		$log_time = date_to_mysqldatetime();
		$log_level = "INFO";
		
		if ($message) 
		{
			$data = array(
				'log_time' => $log_time,
				'log_level' => $log_level,
				'message' => $message,
				'username' => $username
			);
			
			$this->db->insert("logging", $data);
		}
	}
	
	function warning($message, $username)
	{
		$log_time = date_to_mysqldatetime();
		$log_level = "WARNING";
		
		if ($message) 
		{
			$data = array(
				'log_time' => $log_time,
				'log_level' => $log_level,
				'message' => $message,
				'username' => $username
			);
			
			$this->db->insert("logging", $data);
		}
	}
	
	function error($message, $username)
	{
		$log_time = date_to_mysqldatetime();
		$log_level = "ERROR";
		
		if ($message) 
		{
			$data = array(
				'log_time' => $log_time,
				'log_level' => $log_level,
				'message' => $message,
				'username' => $username
			);
			
			$this->db->insert("logging", $data);
		}
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */