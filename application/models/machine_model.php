<?php

class Machine_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_all()
	{
		$sql = "SELECT * FROM vr_mc_machine_info WHERE machine_type <> 'Y' ORDER BY mc_id";
		
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return array();
	}
	
	function get_slitter()
	{
		$sql = "SELECT * FROM vr_mc_machine_info WHERE machine_type = 'S' ORDER BY mc_id";
		
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return array();
	}
	
	function get_special()
	{
		$sql = "SELECT * FROM vr_mc_machine_info WHERE machine_type = 'Y' ORDER BY mc_id";
		
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return array();
	}
	
	function list_all()
	{
		$sql = "SELECT * FROM vr_mc_machine_info ORDER BY mc_id";
		
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return array();
	}
	
	function get_by_id($mc_id)
	{
		$this->db->where("mc_id", $mc_id);
		$query = $this->db->get("vr_mc_machine_info");
		
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function get_by_type($machine_type)
	{
		$this->db->where("machine_type", $machine_type);
		
		$query = $this->db->get("mc_machine_info");
		
		if ($query->num_rows() > 0) {
			$result = $query->row_array();
			return $result;
		}
		
		return FALSE;
		
	}
	
	function get_sum_performance($startDate, $endDate)
	{
		$sql = "SELECT mc_id, machine_name, machine_type, IFNULL(SUM(duration), 0) as duration FROM `vr_mc_machine_work_hour` WHERE work_date BETWEEN '$startDate' AND '$endDate' GROUP BY mc_id";
		
		db_log_message($sql);
		
		// echo $sql;
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		return array();
	}
	
	
	function insert($machine_name, $machine_type, $record_change_by, $record_change_date) 
	{
		// Get Lastest Machine Id
		$this->db->select_max("mc_id");
		$query = $this->db->get("mc_machine_info");
		
		$last_id = 1;
		
		if ($query->num_rows() > 0) {
			// print_r($query);
			
			$last_id_temp = $query->result_array();
			
			if ($last_id_temp[0]["mc_id"]) {
				$last_id = $last_id_temp[0]["mc_id"] + 1;
			}
		}
		
		db_log_message($this->db->last_query());
		$query->free_result();
	
		// Insert New Machine to Database
		// Set Status of Machine to 1 --> Normal
		$data = array(
			'mc_id' => $last_id,
			'machine_name' => $machine_name,
			'machine_type' => $machine_type,
			'status' => 1, // Normal
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date
		);
		
		$result = TRUE;
		
		if ($this->db->insert('mc_machine_info', $data) === FALSE)
		{
			$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
		
	}
	
	function update($mc_id, $machine_name, $machine_type, $record_change_by, $record_change_date) 
	{
		// Update
		// Set Status of Machine to 1 --> Normal
		$data = array(
			'machine_name' => $machine_name,
			'status' => 1, // Normal
			'machine_type' => $machine_type,
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date
		);
		
		$result = TRUE;
		
		$this->db->where("mc_id", $mc_id);
		if ($this->db->update('mc_machine_info', $data) === FALSE)
		{
			$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
		
	}
	
	function delete($mc_id)
	{
		$result = TRUE;
		
		$this->db->where("mc_id", $mc_id);
		$this->db->from("mc_machine_manage");
		$count = $this->db->count_all_results();
		
		if ($count == 0) {
			$this->db->where("mc_id", $mc_id);
			if ($this->db->delete('mc_machine_info') === FALSE) {
				$result = FALSE;
			}
		} else {
			$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
	}
}
	