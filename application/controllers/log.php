<?php

class Log extends CI_Controller {

	function __construct()
	{
		parent::__construct();	
		
		$this->load->helper('file');
		$this->load->helper('directory');
		$this->load->library('zip');
		$this->load->library("email");
	}
	
	function index()
	{
		$system_log_files = get_filenames('./system/logs/system/');
		$user_log_files = get_filenames('./system/logs/user/');
		
		$this->template->write_view('content', 'log_viewer');
		$this->template->render();
	}
	
	function get_log_detail()
	{
		$log_date = $this->input->post("log_date");
		$pattern = "/^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$/";
		if (preg_match($pattern, $log_date))
		{
			$log_date = "log-" . $log_date . ".php";
			
			
			$log_type = $this->input->post("log_type");
			
			if ("USER" == $log_type)
			{
				$user_log_files = get_filenames('./system/logs/user/');
			
				if (in_array($log_date, $user_log_files))
				{
					echo read_file('./system/logs/user/' . $log_date);
				}
				else
				{
					echo '<div class="error">No logging file for this date.</div>';
				}
			}
			else if ("SYSTEM" == $log_type)
			{
				$system_log_files = get_filenames('./system/logs/system/');
			
				if (in_array($log_date, $system_log_files))
				{
					echo read_file("./system/logs/system/" . $log_date);
				}
				else
				{
					echo '<div class="error">No logging file for this date.</div>';
				}
				
			}
			else
			{
				echo "Loggin type is incorrect.";
			}
		}
		else
		{
			echo "Logging Date requested is not match with date pattern.";
		}
	}
	
	function get_log($log_date, $log_type)
	{
		$log_date = "log-" . $log_date . ".php";
		
		$file_path = "";
		if ("USER" == $log_type)
		{
			$user_log_files = get_filenames('./system/logs/user/');
		
			if (in_array($log_date, $user_log_files))
			{
				$file_path = './system/logs/user/' . $log_date;
			}
		}
		else if ("SYSTEM" == $log_type)
		{
			$system_log_files = get_filenames('./system/logs/system/');
		
			if (in_array($log_date, $system_log_files))
			{
				$file_path = "./system/logs/system/" . $log_date;
			}
		}
		
		if (!empty($file_path))
		{
			$zip_path = "./system/logs/zip/";
			$this->zip->read_file($file_path);
			$zip_path = $zip_path . "/" . $log_type . "-" . $log_date . ".zip";
			$this->zip->archive($zip_path);
			$this->zip->download($log_type . "-" .  $log_date . ".zip");
			
			$this->email->from('p.pakkawan@tstandardsteel.com', 'A');
			$this->email->to('autthapon@gmail.com');
			$this->email->cc('p.pakkawan@tstandardsteel.com');
			
			$this->email->attach($zip_path);

			$this->email->subject('Log of ' . $log_type . " " . $log_date);
			$this->email->message("Thank you");

			$this->email->send();

			echo $this->email->print_debugger();
		}
		
		//redirect("/log");
		
	}
}

/* End of file login.php */
/* Location: ./system/application/controllers/login.php */