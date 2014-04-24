<?php

class Machine_manage extends CI_Controller 
{
	private static $PERMISSION = "MACHINE_MANAGE";

	function __construct()
	{
		parent::__construct();	
		
		check_permission(self::$PERMISSION);
		
		$this->load->model('machine_model');
		$this->load->model('machine_config_model');
		$this->load->model('machine_manage_model');
		$this->load->model('machine_work_hour_model');
	}
	
	function index()
	{
	
		$username = $this->session->userdata("USERNAME");
		
		check_permission(self::$PERMISSION);
		
		$machines = $this->machine_model->list_all();

		$data['machine'] = $machines;
		
		$machine_configs = $this->machine_config_model->get_all();
		
		$data['machine_config'] = $machine_configs;
		
		$user_date = $this->input->post("date_input");
		
		$selectedDate = date('Y-m-d', strtotime("-1 day"));
		
		if ($user_date) {
			$selectedDate = $user_date;
			
			user_log_message("INFO",  $username . " select date. [" . $user_date . "]");
		} else {
			user_log_message("INFO",  "Use today date [" . $selectedDate . "]");
		}
		
		$data["select_date"] = $selectedDate;
		
		// Get Work Date
		$work_hour = $this->machine_work_hour_model->get($selectedDate);

		$work_hour_result = array();
		if ($work_hour) 
		{
			// Populate Field
			for($i = 0; $i < count($work_hour); $i++) 
			{
				$theMcId = $work_hour[$i]["mc_id"];
				$theDuration = $work_hour[$i]["duration"];
				$theOt = $work_hour[$i]["ot"];
				$theComment = $work_hour[$i]["comment"];
				
				$work_hour_result[$theMcId] = array(
					'duration' => $theDuration,
					'ot' => $theOt,
					'comment' => $theComment
				);
			}
		}
		$data["work_hour"] = $work_hour_result;
		
		// Machine Config
		$machine_config_hour = $this->machine_manage_model->get($selectedDate);
		$machine_config_hour_result = array();
		
		if ($machine_config_hour) 
		{
			for($i = 0; $i < count($machine_config_hour); $i++) 
			{
				$theMcId = $machine_config_hour[$i]["mc_id"];
				$theDuration = $machine_config_hour[$i]["duration"];
				$theConfigId = $machine_config_hour[$i]["machine_config_id"];
				
				$machine_config_hour_result[$theMcId][$theConfigId] = $theDuration;
			}
		}

		$data["machine_config_hour"] = $machine_config_hour_result;
		
		// Navigation
		$navigator = array(
			"หน้าแรก" => "/main",
			"ส่วนจัดการเวลาการทำงาน" => "/machine_manage"
		);
		
		$data["navigation"] = build_navigation("ส่วนจัดการเวลาการทำงาน", $navigator);
		
		$this->template->write_view('content', 'machine_manage_view', $data);
		$this->template->render();
	}
	
	function save() 
	{
		$username = $this->session->userdata("USERNAME");
		
		user_log_message("INFO",  $username . " calls save machine duration method.");

		check_permission(self::$PERMISSION);
	
		$machines = $this->machine_model->list_all();
		$machine_configs = $this->machine_config_model->get_all();
		
		$date_input = $this->input->post("date_input");
		
		$this->db->trans_begin();
		for($i = 0; $i < count($machines); $i++) 
		{
			$mc_id = $machines[$i]["mc_id"];
			$all_work = $this->input->post("all_work_" . $mc_id); // It's machine_config_id = 0 always.
			
			$ot = $this->input->post("ot_" . $mc_id);
			$comment = $this->input->post("comment_" . $mc_id);
			
			$configs = array();
			for($j = 0; $j < count($machine_configs); $j++) 
			{
				$config_id = $machine_configs[$j]["machine_config_id"];
				$configs[$config_id] = $this->input->post("config_" . $mc_id . "_" . $config_id);
			}
			
			// Remove Comment if you don't want to insert empty data to db.
			// Check Data Input
			/*
			$isInserted = FALSE;
			foreach($configs as $key => $value)
			{
				if ($value) {
					$isInserted = TRUE;
					break;
				}
			}
			*/

			// Insert into database
			// if ($isInserted) 
			// {
				$record_change_by = $this->session->userdata("USERNAME");
				$record_change_date = date_to_mysqldatetime();

				$this->machine_work_hour_model->insert($mc_id, $date_input, $all_work, (($ot === "Y") ? "Y" : "N"), $comment, $record_change_by, $record_change_date);
				
				// Delete Old Data
				$this->machine_manage_model->delete_all_config_by_date($mc_id, $date_input);
				
				foreach($configs as $key => $value)
				{
					if ($value === FALSE) {
						$value = 0;
					}
					
					//if ($value) 
					//{
						$this->machine_manage_model->insert($mc_id, $key, $date_input, $value, $record_change_by, $record_change_date);
					//}
				}
			/*
			}
			else
			{
				$this->machine_work_hour_model->delete_by_date($mc_id, $date_input);
				$this->machine_manage_model->delete_all_config_by_date($mc_id, $date_input);
			}
			*/
		}

		if ($this->db->trans_status() === FALSE)
		{
			user_log_message("INFO",  $username . " cannot save machine duration because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
			
			$data['result'] = 'ไม่สามารถบันทึกข้อมูลในการจัดการ Machine ได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			if ($this->db->_error_number() == "1062") 
			{
				$data['result'] = 'ไม่สามารถบันทึกข้อมูลในการจัดการ Machine เพราะมี Id ซ้ำแล้ว<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			}
			
			$this->db->trans_rollback();
			$data['back_page'] = "/machine_manage";
			$data['user_date'] = $date_input;
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
		else
		{
			user_log_message("INFO",  $username . " can save machine duration");
			
			$this->db->trans_commit();
			$data['result'] = 'บันทึกข้อมูลเรียบร้อยแล้ว';
			$data['back_page'] = "/machine_manage";
			$data['user_date'] = $date_input;
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
	}
}



/* End of file machine_manage.php */
/* Location: ./system/application/controllers/machine_manage.php */