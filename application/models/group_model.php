<?php

class Group_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	
	
	function get_all()
	{
		$sql = 'SELECT 
				vr_prd_coil_group.coil_group_code,
				(SELECT prd_product_produce.program_code FROM prd_product_produce WHERE prd_product_produce.coil_group_code = vr_prd_coil_group.coil_group_code AND prd_product_produce.product_dtl_id = vr_prd_coil_group.product_dtl_id LIMIT 1) AS program_code,
				(SELECT prd_program_information.program_code_ext FROM prd_program_information WHERE prd_program_information.program_code = program_code AND prd_program_information.product_dtl_id = vr_prd_coil_group.product_dtl_id LIMIT 1) AS program_code_ext
				FROM vr_prd_coil_group
				WHERE vr_prd_coil_group.populate_flag = "N"
				ORDER BY vr_prd_coil_group.slit_date DESC';
				
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return array();
	}
	
	function get_summary_of_group_code($group_code)
	{
		$this->db->distinct();
		$this->db->select("coil_group_code");
		$this->db->select_sum("cost_price");
		$this->db->select_sum("weight");
		$this->db->select_sum("unit");
		$this->db->where("coil_group_code", $group_code);
		$this->db->group_by("coil_group_code");
		$query = $this->db->get("vr_prd_coil_group");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			return $result[0];
		}

		return FALSE;
	}
	
	function get_view_by_id($group_code)
	{
		$query = $this->db->get_where("vr_prd_coil_group", array("coil_group_code" => $group_code));
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		return FALSE;
	}
	
	function get_all_by_id($group_code)
	{
		$query = $this->db->get_where("vr_prd_coil_group", array("coil_group_code" => $group_code));
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		return FALSE;
	}
	
	function get_by_product_id($group_code, $product_dtl_id)
	{
		$this->db->where("coil_group_code", $group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		
		$query = $this->db->get("vr_prd_coil_group");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			return $result[0];
		}
		
		return FALSE;
	}
	
	function get_total_unit_by_id($group_code)
	{
		$this->db->select_sum('unit');
		$this->db->where('coil_group_code', $group_code);
		$query = $this->db->get('vr_prd_coil_group');
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->result_array();
			
			return $result[0]['unit'];
		}
		return FALSE;
	}
	
	function get_group_by_group_code_and_product_id($coil_group_code, $product_dtl_id)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$query = $this->db->get("vr_prd_coil_group");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			$result_array = array();
			foreach($result as $value)
			{
				$result_array[] = $value;
			}
			
			return $value;
		}
		
		return FALSE;
	}
	
	function get_distinct_coil_group()
	{
		$this->db->distinct();
		$this->db->select("coil_group_code");
		$this->db->order_by("slit_date", "desc");
		$query = $this->db->get("vr_prd_coil_group");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) {
			$result =array();
			foreach($query->result_array() as $item) {
				$result[] = $item;
			}
			
			return $result;
		}
		return FALSE;
	}
	
	function get_distinct_coil_group_pop_no()
	{
		$this->db->distinct();
		$this->db->select("coil_group_code");
		$this->db->where("populate_flag" , "N");
		$this->db->order_by("slit_date", "desc");
		$query = $this->db->get("vr_prd_coil_group");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) {
			$result =array();
			foreach($query->result_array() as $item) {
				$result[] = $item;
			}
			
			return $result;
		}
		return FALSE;
	}
	
	function update_populate_flag($group_code, $populate_flag)
	{
		$data = array(
			"populate_flag" => $populate_flag,
			"populate_date" => date_to_mysqldatetime()
		);
		
		$this->db->where('coil_group_code', $group_code);
		$this->db->update('prd_product_produce', $data);
		db_log_message($this->db->last_query());
	}
	
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */