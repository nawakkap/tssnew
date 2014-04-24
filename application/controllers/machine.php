<?php

class Machine extends CI_Controller 
{
	private static $PERMISSION = "MACHINE_CONFIG";

	function __construct()
	{
		parent::__construct();	
		
		check_permission(self::$PERMISSION);
		
		$this->load->model('machine_model');
		$this->load->model('config_model');
	}
	
	function index()
	{
		check_permission(self::$PERMISSION);
		
		$result = $this->machine_model->list_all();

		$data['result'] = $result;
		
		$machine_status = $this->config_model->get_machine_status();
		$data['machine_status'] = $machine_status;
		// Navigation
		$navigator = array(
			"หน้าแรก" => "/main",
			"Machine" => "/machine"
		);
		
		$data["navigation"] = build_navigation("Machine", $navigator);
		
		$this->template->write_view('content', 'machine_list_view', $data);
		$this->template->render();
	}
	
	
	function add()
	{
	
		$username = $this->session->userdata("USERNAME");
		
		user_log_message("INFO",  $username . " calls add machine method.");
		
		check_permission(self::$PERMISSION);
		
		$machine_name = $this->input->post("machine_name");
		$machine_type = $this->input->post("machine_type");
		
		if ($machine_type == "S") // Slitter
		{
			
		}
		else if ($machine_type == "Y") // เครื่องรีด
		{
			
		}
		
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		$this->db->trans_begin();
		
		if ($machine_type) {
			$temp_machine = $this->machine_model->get_by_type($machine_type);
			
			if ($temp_machine)
			{
				$this->machine_model->update($temp_machine["mc_id"], $machine_name, $machine_type, $record_change_by, $record_change_date);
			}
			else
			{
				$this->machine_model->insert($machine_name, $machine_type, $record_change_by, $record_change_date);
			}
		}
		else
		{
			$this->machine_model->insert($machine_name, $machine_type, $record_change_by, $record_change_date);
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			user_log_message("INFO",  $username . " cannot add machine because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
			
			$data['result'] = 'ไม่สามารถเพิ่ม Machine ได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			if ($this->db->_error_number() == "1062") 
			{
				$data['result'] = 'ไม่สามารถเพิ่มข้อมูํลได้ เพราะเลข Machine Id ซ้ำแล้ว<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			}
			
			$this->db->trans_rollback();
			$data['back_page'] = "/machine";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
		else
		{
			user_log_message("INFO",  $username . " can add Machine Id");
			
			$this->db->trans_commit();
			$data['result'] = 'เพิ่มข้อมูลเรียบร้อยแล้ว';
			$data['back_page'] = "/machine";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
	}
	
	function edit() 
	{
		$username = $this->session->userdata("USERNAME");
		
		user_log_message("INFO",  $username . " calls edit machine method.");
		
		check_permission(self::$PERMISSION);

		$mc_id = $this->input->post("mc_id");
		$machine_name = $this->input->post("machine_name");
		$machine_type = $this->input->post("machine_type");
		
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		$this->db->trans_begin();
		
		$this->machine_model->update($mc_id, $machine_name, $machine_type, $record_change_by, $record_change_date);
		
		
		if ($this->db->trans_status() === FALSE)
		{
			user_log_message("INFO",  $username . " cannot edit machine because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
			
			$data['result'] = 'ไม่สามารถแก้ไข Machine ได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			if ($this->db->_error_number() == "1062") 
			{
				$data['result'] = 'ไม่สามารถแก้ไขข้อมูํลได้ เพราะเลข Machine Id ซ้ำแล้ว<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			}
			
			$this->db->trans_rollback();
			$data['back_page'] = "/machine";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
		else
		{
			user_log_message("INFO",  $username . " can edit Machine Id");
			
			$this->db->trans_commit();
			$data['result'] = 'แก้ไขข้อมูลเรียบร้อยแล้ว';
			$data['back_page'] = "/machine";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
	}
	
	function delete()
	{
		$username = $this->session->userdata("USERNAME");
		
		user_log_message("INFO",  $username . " calls delete machine method.");

		check_permission(self::$PERMISSION);

		$mc_id = $this->input->post("mc_id");
		
		$this->db->trans_begin();
		
		$result = $this->machine_model->delete($mc_id);
		
		
		if ($this->db->trans_status() === FALSE || $result === FALSE)
		{
			if ($result === FALSE) {
				$data['result'] = "ไม่สามารถลบข้อมูลได้ เนื่องจากมีข้อมูลอื่นใช้งานอยู่ด้วย";
			} else {
				user_log_message("INFO",  $username . " cannot delete machine because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
				
				$data['result'] = 'ไม่สามารถลบ Machine ได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
				
				if ($this->db->_error_number() == "1062") 
				{
					$data['result'] = 'ไม่สามารถลบได้ เพราะเลข Machine Id ซ้ำแล้ว<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
				}
			}
			
			$this->db->trans_rollback();
			$data['back_page'] = "/machine";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
		else
		{
			user_log_message("INFO",  $username . " can edit Machine Id");
			
			$this->db->trans_commit();
			$data['result'] = 'ลบข้อมูลเรียบร้อยแล้ว';
			$data['back_page'] = "/machine";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
	}
	
	
	
	function config()
	{
		$this->load->model("machine_config_model");
	
		$result = $this->machine_config_model->get_all();

		$data['result'] = $result;
	
		// Navigation
		$navigator = array(
			"หน้าแรก" => "/main",
			"Machine" => "/machine",
			"Machine Config" => "/machine/config"
		);
		
		$data["navigation"] = build_navigation("Machine Config", $navigator);
		
		$this->template->write_view('content', 'machine_config_list_view', $data);
		$this->template->render();
	}
	
	function config_add()
	{
		$username = $this->session->userdata("USERNAME");
		
		user_log_message("INFO",  $username . " calls add machine configs method.");
		
		check_permission(self::$PERMISSION);
		
		$machine_config_name = $this->input->post("machine_config_name");
		
		$this->load->model("machine_config_model");
		
		$this->db->trans_begin();
		
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		$this->machine_config_model->insert($machine_config_name, $record_change_by, $record_change_date);
		
		
		if ($this->db->trans_status() === FALSE)
		{
			user_log_message("INFO",  $username . " cannot add machine config because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
			
			$data['result'] = 'ไม่สามารถเพิ่ม Machine Config ได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			if ($this->db->_error_number() == "1062") 
			{
				$data['result'] = 'ไม่สามารถเพิ่มข้อมูํลได้ เพราะเลข Machine Config Id ซ้ำแล้ว<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			}
			
			$this->db->trans_rollback();
			$data['back_page'] = "/machine/config";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
		else
		{
			user_log_message("INFO",  $username . " can add Machine Config Id");
			
			$this->db->trans_commit();
			$data['result'] = 'เพิ่มข้อมูลเรียบร้อยแล้ว';
			$data['back_page'] = "/machine/config";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
	}
	
	function config_edit()
	{
		$username = $this->session->userdata("USERNAME");
		
		user_log_message("INFO",  $username . " calls edit machine config method.");

		check_permission(self::$PERMISSION);

		$machine_config_id = $this->input->post("machine_config_id");
		$machine_config_name = $this->input->post("machine_config_name");
		
		$this->load->model("machine_config_model");
		
		$this->db->trans_begin();

		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		$this->machine_config_model->update($machine_config_id, $machine_config_name, $record_change_by, $record_change_date);
		
		
		if ($this->db->trans_status() === FALSE)
		{
			user_log_message("INFO",  $username . " cannot edit machine config because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
			
			$data['result'] = 'ไม่สามารถแก้ไข Machine Config ได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			if ($this->db->_error_number() == "1062") 
			{
				$data['result'] = 'ไม่สามารถแก้ไขข้อมูํลได้ เพราะเลข Machine Config Id ซ้ำแล้ว<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			}
			
			$this->db->trans_rollback();
			$data['back_page'] = "/machine/config";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
		else
		{
			user_log_message("INFO",  $username . " can edit Machine Config Id");
			
			$this->db->trans_commit();
			$data['result'] = 'แก้ไขข้อมูลเรียบร้อยแล้ว';
			$data['back_page'] = "/machine/config";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
	}
	
	function config_delete() 
	{
		$username = $this->session->userdata("USERNAME");
		
		user_log_message("INFO",  $username . " calls delete machine config method.");

		check_permission(self::$PERMISSION);

		$machine_config_id = $this->input->post("machine_config_id");
		
		$this->db->trans_begin();
		
		$this->load->model("machine_config_model");
		
		$result = $this->machine_config_model->delete($machine_config_id);
		
		
		if ($this->db->trans_status() === FALSE || $result === FALSE)
		{
		
			if ($result === TRUE) 
			{
		
				user_log_message("INFO",  $username . " cannot delete machine config because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
				
				$data['result'] = 'ไม่สามารถลบ Machine Config ได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
				
				if ($this->db->_error_number() == "1062") 
				{
					$data['result'] = 'ไม่สามารถลบได้ เพราะเลข Machine Config Id ซ้ำแล้ว<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
				}
			} else {
			
				$data['result'] = "ไม่สามารถลบได้เพราะมีข้อมูลที่เกี่ยวข้องกับ Config Data ตัวนี้อยู่ในระบบ";
			
			}
			
			$this->db->trans_rollback();
			$data['back_page'] = "/machine/config";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
		else
		{
			user_log_message("INFO",  $username . " can edit Machine Config Id");
			
			$this->db->trans_commit();
			$data['result'] = 'ลบข้อมูลเรียบร้อยแล้ว';
			$data['back_page'] = "/machine/config";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
	}
	
	
	function machine_detail($mc_id = FALSE) 
	{
		
		if ($mc_id) 
		{
			$this->load->model("machine_config_model");
			$this->load->model("machine_work_hour_model");
			$this->load->model("machine_manage_model");
			$this->load->model("program_model");
			$this->load->model("film_history_model");
		
			$data = array();
			$machine_config = $this->machine_config_model->get_all();
		
			$data["machine_config"] = $machine_config;
			
			$machine_info = $this->machine_model->get_by_id($mc_id);
			$data["machine_info"] = $machine_info;
			
			$work_hour = $this->machine_work_hour_model->get_sum_work_hour_by_mc_id($mc_id);
			$data["work_hour"] = $work_hour;
			
			$manage_result_temp = $this->machine_manage_model->get_sum($mc_id);
			$manage_result = array();
			for($i = 0; $i < count($manage_result_temp); $i++) 
			{
				$manage_result[$manage_result_temp[$i]["machine_config_id"]] = $manage_result_temp[$i]["duration"];
			}
			$data["machine_manage"] = $manage_result;
			
			$total_unit = $this->program_model->get_sum_quantity_all($mc_id);
			$data["total_unit"] = $total_unit;
			
			$first_date = $this->program_model->get_first_date_of_machine($mc_id);
			if ($first_date != "") 
			{
				$first_date = mysqldatetime_to_date($first_date, "Y-m-d");
			}
			$data["first_date"] = $first_date;
			
			$film_unit = $this->film_history_model->get_unit_total($mc_id);
			$data["film_unit"] = $film_unit;
			
			// Navigation
			$navigator = array(
				"หน้าแรก" => "/main",
				"Machine" => "/machine",
				"Machine Detail" => ""
			);
			
			$data["navigation"] = build_navigation("Machine Detail", $navigator);
			
			$this->template->write_view('content', 'machine_detail_view', $data);
			$this->template->render();	
		}
		else
		{
			$data['result'] = 'ค้นหา Machine ไม่เจอ [ mc_id = ' . $mc_id . ']';
			$data['back_page'] = "/machine";
			$this->template->write_view('content', 'machine_result_view', $data);
			$this->template->render();
		}
	}
	
}

/* End of file machine.php */
/* Location: ./system/application/controllers/machine.php */