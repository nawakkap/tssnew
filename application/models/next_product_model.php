<?php

class Next_product_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_next_name($po_id)
	{
		$this->db->select("coil_group_code");
		$this->db->where("po_id", $po_id);
		$query = $this->db->get("vr_prd_next_po_id");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->row_array();
			if ($result['coil_group_code']) {
				return $result['coil_group_code'];
			}
		}
		
		return FALSE;
	}
	
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */