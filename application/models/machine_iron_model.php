<?php

class Machine_iron_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function check_exist($coil_group_code, $product_dtl_id)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		
		$query = $this->db->get("mc_machine_ironing");
		
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		return FALSE;
	}
	
	function insert($coil_group_code, $product_dtl_id, $machine_id)
	{
		$data = array(
			'coil_group_code' => $coil_group_code,
			'product_dtl_id' => $product_dtl_id,
			'mc_id' => $machine_id
		);
		
		$result = TRUE;
		
		if ($this->db->insert('mc_machine_ironing', $data) === FALSE)
		{
			$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
	}
	
	function update($coil_group_code, $product_dtl_id, $machine)
	{
		$result = TRUE;
		
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		
		$data = array(
			'mc_id' => $machine
		);
		
		$this->db->update("mc_machine_ironing", $data);
		
		return TRUE;
	}

	function delete($coil_group_code, $product_dtl_id)
	{
		$result = TRUE;
		
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->from("mc_machine_ironing");
		if ($this->db->delete('mc_machine_ironing') === FALSE) {
			$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
	}
	
	function change_to_new_product($coil_group_code, $old_product_dtl_id, $new_product_dtl_id)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $old_product_dtl_id);
		
		$data = array(
			'product_dtl_id' => $new_product_dtl_id
		);
		
		db_log_message($this->db->last_query());

		$this->db->update("mc_machine_ironing", $data);
		
		return TRUE;
	}
}
	