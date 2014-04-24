<?php

class Login extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		$this->load->helper("file");
	}
	
	function index($error_message = '')
	{
		$this->_delete_db_log();
		$this->_delete_server_log();
		$this->_delete_user_log();
		
		user_log_message("INFO", $this->input->ip_address() . " is stay on login page.");
	
		$data["navigation"] = FALSE;
		$data['error_message'] = $error_message;
		$this->template->write_view('content', 'login_view', $data);
		$this->template->render();
	}
	
	
	function login_method()
	{
		user_log_message("INFO", $this->input->ip_address() . " calls login_method.");
	
		$this->load->model("user_model");
	
		$username = $this->input->post("username");
		$password = $this->input->post("password");
		
		user_log_message("INFO", $this->input->ip_address() . " is " .$username);
		
		$result = $this->user_model->login($username, $password);
		
		if ($result === TRUE) 
		{
			// SET Permission
			user_log_message("INFO",  $username . " can login.");
			
			$permission= $this->user_model->get_permission($username);
			$this->session->set_userdata('PERMISSION', $permission);
			$this->session->set_userdata('USERNAME' , $username);
			
			$user_type = $this->user_model->get_user_type_by_username($username);
			$user_type = strtoupper($user_type);  
			$this->session->set_userdata('USER_TYPE', $user_type);

			redirect("/main");
			return;
		}
		else
		{
			user_log_message("INFO",  $username . " cannot login.");
		
			$this->index("Username or Password is incorrect.");
			return ;
		}
	}
	
	function logout_method()
	{
		$username = $this->session->userdata('USERNAME');
		
		user_log_message("INFO",  $username . " log out.");
	
		$this->session->unset_userdata("PERMISSION");
		$this->session->unset_userdata('USERNAME');
		$this->session->sess_destroy();
		
		redirect("/login");
	}
	
	function _delete_db_log()
	{
		// Remove the old logs (7 days ago)
		$filenames = get_dir_file_info('./system/logs/db');
		if ($filenames) 
		{
			foreach($filenames as $item)
			{
				$d = strtotime(substr($item["name"], strlen('log-'), 10));
				if ((time() - $d) >= 700000)
				{
					unlink($item["server_path"]);
				}
			}
		}
	}
	
	function _delete_server_log()
	{
		// Remove the old logs (7 days ago)
		$filenames = get_dir_file_info('./system/logs/system');
		if ($filenames) 
		{
			foreach($filenames as $item)
			{
				$d = strtotime(substr($item["name"], strlen('log-'), 10));
				if ((time() - $d) >= 700000)
				{
					unlink($item["server_path"]);
				}
			}
		}
	}
	
	function _delete_user_log()
	{
		// Remove the old logs (7 days ago)
		$filenames = get_dir_file_info('./system/logs/user');
		if ($filenames) 
		{
			foreach($filenames as $item)
			{
				$d = strtotime(substr($item["name"], strlen('log-'), 10));
				if ((time() - $d) >= 700000)
				{
					unlink($item["server_path"]);
				}
			}
		}
	}
	
	
}

/* End of file login.php */
/* Location: ./system/application/controllers/login.php */