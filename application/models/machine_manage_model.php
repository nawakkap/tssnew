<?php

class Machine_manage_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get($manage_date) 
	{
		$this->db->where("manage_date", $manage_date);
		$query = $this->db->get("mc_machine_manage");
		
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		return FALSE;
	}
	
	function get_sum($mc_id)
	{
		$sql = "SELECT machine_config_id, IFNULL(SUM(duration), 0) as duration FROM vr_mc_machine_config_time WHERE mc_id = $mc_id GROUP BY machine_config_id";
	
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return array();
	}
	
	function get_sum_performance($mc_id, $startDate, $endDate)
	{
		$sql = "SELECT machine_config_id, machine_config_name, IFNULL(SUM(duration), 0) as duration FROM vr_mc_machine_config_time WHERE mc_id = $mc_id AND manage_date BETWEEN '$startDate' AND '$endDate' GROUP BY machine_config_id ";
		
		//echo $sql;
		
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) {
			return $query->result_array();
		}
		
		return array();
	}
	
	function insert($mc_id, $machine_config_id, $manage_date, $duration, $record_change_by, $record_change_date) 
	{
	
		// Insert New Data
		$data = array(
			"mc_id" => $mc_id,
			"machine_config_id" => $machine_config_id,
			"manage_date" => $manage_date,
			"duration" => $duration,
			"record_change_by" => $record_change_by,
			"record_change_date" => $record_change_date
		);

		$result = TRUE;
		
		if ($this->db->insert('mc_machine_manage', $data) === FALSE)
		{
			$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
		
	}
	
	function delete_all_config_by_date($mc_id, $manage_date)
	{
		$this->db->where("mc_id", $mc_id);
		$this->db->where("manage_date" , $manage_date);
		$this->db->delete('mc_machine_manage');
		
		return TRUE;
	}
}
	