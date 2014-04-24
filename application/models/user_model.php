<?php

class User_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_all($sort = 'first_name', $sort_type = 'asc') 
	{
		
		$this->db->order_by($sort, $sort_type);
		$query = $this->db->get("usr_user_information");
		
		db_log_message($this->db->last_query());
		$result = array();
		
		$index = 0;
		foreach($query->result() as $item)
		{
			$result[$index++] = $item;
		}
		
		return $result;
		
	}
	
	function get_empty_data()
	{
		$data = array(
			'first_name' => '',
			'last_name' => '',
			'username' => '',
			'password' => '',
			'user_type' => '',
			'permission' => ''
		);
		
		return $data;
	}
	
	function get_by_id($username)
	{
		$query = $this->db->get_where("usr_user_information", array("username" => $username));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		return FALSE;
	}
	
	function get_permission($username)
	{
		$this->db->select('permission');
		$this->db->where('username', $username);
		$query = $this->db->get('usr_user_information');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			
			return $result[0]['permission'];
			
		}
		
		return '';
		
	}
	
	function get_user_type_by_username($username)
	{
		$this->db->select("user_type");
		$this->db->where('username' , $username);
		$query = $this->db->get('usr_user_information');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() == 1)
		{
			$result = $query->result_array();
			return $result[0]['user_type'];
		}
		return FALSE;
	}
	
	function login($username, $password)
	{
		$this->db->where('username' , $username);
		$this->db->where('password', $password);
		$query = $this->db->get('usr_user_information');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() == 1)
		{
			return TRUE;
		}
		
		return FALSE;
	}
	
	function insert(	$mode, $username, $password, $first_name, $last_name, $user_type, $permission) 
	{
		$data = array(
			'password' => $password,
			'first_name' => $first_name,
			'last_name' => $last_name,
			'user_type' => $user_type,
			'permission' => $permission,
		);
				
		$result = TRUE;
		
		if ("ADD" == $mode) 
		{
			$data['username'] = $username;
			
			if ($this->db->insert('usr_user_information', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("username", $username);
			if ($this->db->update('usr_user_information', $data) === FALSE)
				$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function delete($username)
	{
		$result =  $this->db->delete('usr_user_information', array('username' => $username));
		db_log_message($this->db->last_query());
		return $result;
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */