<?php

class Welcome extends CI_Controller {

	function __constructor()
	{
		parent::__constructor();	
	}
	
	function index()
	{
		$data["navigation"] = FALSE;
		$this->template->write_view('content', 'welcome_message', $data);
		$this->template->render();
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */