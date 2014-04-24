<?php

class Unperm extends CI_Controller { // Don't Have Permission

	function __construct()
	{
		parent::__construct();	
	}
	
	function index($error_message = '')
	{
		$refer = site_url("/login");
		
		if ($this->agent->is_referral())
		{
			$refer = $this->agent->referrer();
		}
		
		if (strpos($refer, "unperm") > -1)
		{
			$refer = site_url("/main");
		}
	
		$data['refer'] = $refer;
		$this->template->write_view('content', 'user_dont_have_permission', $data);
		$this->template->render();
	}
}

/* End of file login.php */
/* Location: ./system/application/controllers/login.php */