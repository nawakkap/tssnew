<?php

class Coil_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_all($search = 'coil_id', $search_type = 'asc') 
	{
		$this->db->order_by($search, $search_type);
		$query = $this->db->get("vr_prd_coil");
		
		db_log_message($this->db->last_query());
		$result = array();
		
		$index = 0;
		foreach($query->result() as $item)
		{
			$result[$index++] = $item;
		}
		
		return $result;
		
	}
	
	function get_all_by_normal_status($search = 'coil_id', $search_type = 'asc')
	{
		$this->db->order_by($search, $search_type);
		$this->db->where('coil_status', '1');
		$query = $this->db->get("vr_prd_coil");
		
		db_log_message($this->db->last_query());
		$result = array();
		
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		return $result;
	}
	
	function get_empty_data() 
	{
		$data = array(
			'po_id' => '',
			'order_received_date' => '',
			'supplier_id' => '',
			'thickness' => '0',
			'width' => '0',
			'weight' => '0',
			'payment_term' => '',
			'amt_current_invoice' => '',
			'vat_status' => '',
			'order_status' => '',
			'price_base' => '0',
			'price' => '0',
			'po_id' => '',
			'record_change_by' => '',
			'record_change_date' => '',
		);
		
		return $data;
	}
	
	function get_by_id($coil_id, $coil_lot_no, $po_id)
	{
		$query = $this->db->get_where("vr_prd_coil", array("coil_id" => $coil_id, "coil_lot_no" => $coil_lot_no, "po_id" => $po_id));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function check_coil_by_po_id($po_id)
	{
		$this->db->where("po_id", $po_id);
		$this->db->where("coil_status", "1");
		$this->db->select("count(*) as total");
		$query = $this->db->get("prd_coil_information");
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["total"];
		}
		return FALSE;
	}
	
	function get_summary_weight_slit_by_po_id($po_id)
	{
		$this->db->where("po_id", $po_id);
		$this->db->where("coil_status in (2,3)"); // Coil Slitted
		$this->db->select_sum("weight");
		$this->db->select("po_id");
		$this->db->group_by("po_id");
		$query = $this->db->get("prd_coil_information");
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["weight"];
		}
		return 0;
	}
	
	function get_by_coil_id($coil_id, $coil_lot_no)
	{
		$this->db->where("coil_id" , $coil_id);
		$this->db->where("coil_lot_no", $coil_lot_no);
		$query = $this->db->get("vr_prd_coil");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function get_all_by_id($po_id) 
	{
		$this->db->where("po_id", $po_id);
		$query = $this->db->get("vr_prd_coil");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		
		return array();
	}
	
	function get_all_by_id_normal_status($po_id, $search = "coil_lot_no", $search_type = "asc")
	{
		$this->db->order_by($search, $search_type);
		$this->db->where("po_id", $po_id);
		$this->db->where("coil_status", "1");
		$query = $this->db->get("vr_prd_coil"); // 1 is normal status
		
		db_log_message($this->db->last_query());
		$result = array();
		
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		
		return $result;
	}
	
	function get_status($coil_id, $coil_lot_no, $po_id)
	{
		$this->db->where("coil_id", $coil_id);
		$this->db->where("coil_lot_no", $coil_lot_no);
		$this->db->where("po_id", $po_id);
		
		$this->db->select('coil_status');
		$query = $this->db->get('prd_coil_information');
		
		db_log_message($this->db->last_query());
		
		if ($query->num_rows() > 0) {
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function update_status($coil_id, $coil_lot_no, $po_id, $coil_status) 
	{
		$this->db->where("coil_id", $coil_id);
		$this->db->where("coil_lot_no", $coil_lot_no);
		$this->db->where("po_id", $po_id);
		
		$data = array(
			"coil_status" => $coil_status
		);
		
		if ($this->db->update('prd_coil_information', $data) === FALSE)
		{
			//db_log_message($this->db->last_query());
			$result = FALSE;
		}
		
		//echo $this->db->last_query();
		db_log_message($this->db->last_query());
		return TRUE;
	}
	
	
	function get_sum_coil_weight_by_thickness($thickness)
	{
		$this->db->where("thickness", $thickness);
		$this->db->where("coil_status", "1");
		$this->db->select_sum("weight");
		$query = $this->db->get("prd_coil_information");
		
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["weight"];
		}
		return 0;
	}
	
	function insert(	$mode, $coil_id, $coil_lot_no, $po_id, $thickness, $width, $weight, $coil_received_date, 
							$coil_status, $coil_price,$record_change_by, $record_change_date) 
	{
		$data = array(
			'coil_lot_no' => $coil_lot_no,
			'po_id' => $po_id,
			'thickness' => $thickness,
			'width' => $width,
			'weight' => $weight,
			'coil_received_date' => $coil_received_date,
			'coil_status' => $coil_status,
			'coil_price' => $coil_price,
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date,
		);
				
		$result = TRUE;
		
		if ("ADD" == $mode) 
		{
			$data['coil_id'] = $coil_id;
			
			if ($this->db->insert('prd_coil_information', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("coil_id", $coil_id);
			$this->db->where("coil_lot_no", $coil_lot_no);
			if ($this->db->update('prd_coil_information', $data) === FALSE)
				$result = FALSE;
		}
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function delete($po_id)
	{
		$result= $this->db->delete('prd_coil_information', array('po_id' => $po_id));
		db_log_message($this->db->last_query());
		return $result;
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */