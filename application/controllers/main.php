<?php

class Main extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
		$permission = $this->session->userdata("PERMISSION");
		$user_type = $this->session->userdata("USER_TYPE");
		$username = $this->session->userdata("USERNAME");
		
		// $this->load->model("logging_model");
		
		// $this->logging_model->info($username . " access main page.", $username);

		if (isset($permission) && !empty($permission)) 
		{
			// user_log_message("INFO",  $username . " has permission to view this page. [" . $permission . "]");
			// $this->logging_model->info($username . " has permission. [" . $permission . "]", $username);
		
			$data['user_type']  = $user_type;
			$data["navigation"] = FALSE;
			$this->template->write_view('content', 'main_page_view', $data);
			$this->template->render();
		}
		else
		{
			// user_log_message("INFO",  $username . " doesn't has permission to view this page. [" . $permission . "]");
			// $this->logging_model->warn($username . " hasn't permission. [" . $permission . "]", $username);
			
			redirect("/login");
		}
	}
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */