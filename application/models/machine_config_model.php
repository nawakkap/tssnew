<?

class Machine_config_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_all()
	{
		$sql = "SELECT * FROM mc_machine_config ORDER BY machine_config_id ASC";
		
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return array();
	}
	
	function insert($machine_config_name, $record_change_by, $record_change_date) 
	{
		// Get Lastest Machine Config Id
		$this->db->select_max("machine_config_id");
		$query = $this->db->get("mc_machine_config");
		
		$last_id = 1;
		
		if ($query->num_rows() > 0) {
			// print_r($query);
			
			$last_id_temp = $query->result_array();
			
			if ($last_id_temp[0]["machine_config_id"]) {
				$last_id = $last_id_temp[0]["machine_config_id"] + 1;
			}
		}
		
		db_log_message($this->db->last_query());
		$query->free_result();
	
		// Insert New Machine to Database
		// Set Status of Machine to 1 --> Normal
		$data = array(
			'machine_config_id' => $last_id,
			'machine_config_name' => $machine_config_name,
			'status' => 1, // Normal
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date
		);
		
		$result = TRUE;
		
		if ($this->db->insert('mc_machine_config', $data) === FALSE)
		{
			$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
	}
	
	function update($machine_config_id, $machine_config_name, $record_change_by, $record_change_date) 
	{
		// Update
		// Set Status of Machine to 1 --> Normal
		$data = array(
			'machine_config_name' => $machine_config_name,
			'status' => 1, // Normal
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date
		);
		
		$result = TRUE;
		
		$this->db->where("machine_config_id", $machine_config_id);
		if ($this->db->update('mc_machine_config', $data) === FALSE)
		{
			$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
		
	}
	
	function delete($machine_config_id)
	{
		$result = TRUE;
		
		$this->db->where("machine_config_id", $machine_config_id);
		$this->db->from("mc_machine_manage");
		$count = $this->db->count_all_results();
		
		if ($count == 0) {
		
			$this->db->where("machine_config_id", $machine_config_id);
			if ($this->db->delete('mc_machine_config') === FALSE) {
				$result = FALSE;
			}
			
		} else {
			$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
	}
}