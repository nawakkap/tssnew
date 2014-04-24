<?php

class Machine_work_hour_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get($work_date)
	{
		$this->db->where("work_date", $work_date);
		$query = $this->db->get("mc_machine_work_hour");
		
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		return FALSE;
	}
	
	function get_sum_work_hour_by_mc_id($mc_id)
	{
		$this->db->where("mc_id", $mc_id);
		$this->db->select_sum("duration");
		$query = $this->db->get("mc_machine_work_hour");
		
		if ($query->num_rows() > 0) 
		{
			$temp = $query->row_array();
			
			return $temp["duration"];
		}
		
		return 0;
	}
	
	function check_work_hour($mc_id, $work_date)
	{
		$this->db->where("mc_id", $mc_id);
		$this->db->where("work_date", $work_date);
		$query = $this->db->get("mc_machine_work_hour");
		if ($query->num_rows() > 0)
		{
			return TRUE;
		}
		
		return FALSE;
	}

	function insert($mc_id, $work_date, $duration, $ot, $comment, $record_change_by, $record_change_date) 
	{
		// Delete Old Data
		$this->db->where("mc_id", $mc_id);
		$this->db->where("work_date", $work_date);
		$this->db->delete('mc_machine_work_hour');
	
		// Insert New Data
		$data = array(
			"mc_id" => $mc_id,
			"work_date" => $work_date,
			"duration" => $duration,
			"ot" => $ot,
			"comment" => $comment,
			"record_change_by" => $record_change_by,
			"record_change_date" => $record_change_date
		);

		$result = TRUE;
		
		if ($this->db->insert('mc_machine_work_hour', $data) === FALSE)
		{
			$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
	}
	
	function delete_by_date($mc_id, $work_date)
	{
		$this->db->where("mc_id", $mc_id);
		$this->db->where("work_date", $work_date);
		$this->db->delete('mc_machine_work_hour');
		
		return TRUE;
	}
}
	