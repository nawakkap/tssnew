<?php

class Config_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_payment_term()
	{
		$query = $this->db->get("vr_gen_payment_term");
		$result = array();
		
		$index = 0;
		
		db_log_message($this->db->last_query());
		foreach($query->result() as $item)
		{
			$result[$item->payment_term_code] = $item->payment_term_value;
		}
		
		return $result;
	}
	
	function get_premium()
	{
		$this->db->select('vat_accounting_value');
		$query = $this->db->get_where('vr_gen_vat_accounting', array('vat_accounting_code' => 'VAT_PREMIUM'));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->row('vat_accounting_value');
		}
		else
		{
			return 1.04;
		}
	}
	
	function get_vat()
	{
		$this->db->select('vat_accounting_value');
		$query = $this->db->get_where('vr_gen_vat_accounting', array('vat_accounting_code' => 'VAT_NORMAL'));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->row('vat_accounting_value');
		}
		else
		{
			return 1.04;
		}
	}
	
		
	function get_vat_premium() 
	{
		$query = $this->db->get('vr_gen_vat_accounting');
		$result = array();
		
		db_log_message($this->db->last_query());
		$index = 0;
		foreach($query->result() as $item)
		{
			$result[$item->vat_accounting_code] = $item->vat_accounting_value;
		}
		
		return $result;
	}
	
	function get_order_status()
	{
		$this->db->order_by('CAST(order_status_code AS UNSIGNED)'); // TODO Temporary
		$query = $this->db->get('vr_gen_order_status');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() == 0) 
		{
			return array();
		}
		
		$order_status = array();
		foreach($query->result() as $item)
		{
			$order_status[$item->order_status_code] = $item->order_status_value;
		}
		
		return $order_status;
		
	}
	
	function get_coil_status() 
	{
		$this->db->order_by('CAST(coil_status_code AS UNSIGNED)'); // TODO Temporary
		$query = $this->db->get('vr_gen_coil_status');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() == 0) 
		{
			return array();
		}
		
		$coil_status = array();
		foreach($query->result() as $item) 
		{
			$coil_status[$item->coil_status_code] = $item->coil_status_value;
		}
		
		return $coil_status;
		
	}
	
	function get_film_status() 
	{
		$this->db->order_by('CAST(film_status_code AS UNSIGNED)'); // TODO Temporary
		$query = $this->db->get('vr_gen_film_status');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() == 0) 
		{
			return array();
		}
		
		$film_status = array();
		foreach($query->result() as $item) 
		{
			$film_status[$item->film_status_code] = $item->film_status_value;
		}
		
		return $film_status;
	}
	
	function get_thickness() 
	{
		$this->db->order_by('CAST(param_code AS UNSIGNED)'); // TODO Temporary
		$query = $this->db->get_where('gen_general_param', array('param_type' => 'THICKNESS'));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() == 0) 
		{
			return array();
		}
		
		$thickness = array();
		foreach($query->result() as $item) 
		{
			$thickness[$item->param_code] = $item->param_value;
		}
		
		return $thickness;
	}
	
	function get_width() 
	{
		$this->db->order_by('CAST(param_code AS UNSIGNED)'); // TODO Temporary
		$query = $this->db->get_where('gen_general_param', array('param_type' => 'WIDTH'));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() == 0) 
		{
			return array();
		}
		
		$width = array();
		foreach($query->result() as $item) 
		{
			$width[$item->param_code] = $item->param_value;
		}
		
		return $width;
	}
	
	function get_group_status()
	{
		$group_status = array(
			'Y' => "ทำการผลิตแล้ว",
			'N' => "ยังไม่ได้ทำการผลิต"
		);
		
		return $group_status;
	}
	
	function get_machine_status() 
	{
		$this->db->order_by('CAST(machine_status_code AS UNSIGNED)'); // TODO Temporary
		$query = $this->db->get('vr_gen_machine_status');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() == 0) 
		{
			return array();
		}
		
		$machine_status = array();
		foreach($query->result() as $item) 
		{
			$machine_status[$item->machine_status_code] = $item->machine_status_value;
		}
		
		return $machine_status;
	}
	
	function insert($db_mode, $data) 
	{
		$this->db->insert('gen_general_param', $data);
		
		db_log_message($this->db->last_query());
	}
	
	function delete($db_mode)
	{
		$this->db->where('param_type', $db_mode);
		$this->db->delete('gen_general_param');
		
		db_log_message($this->db->last_query());
	}
	
	function reset_all() 
	{
		$this->db->truncate("ord_order_information");
		$this->db->truncate("prd_coil_film_mapping");
		$this->db->truncate("prd_coil_information");
		$this->db->truncate("prd_film_information");
		$this->db->truncate("prd_product_produce");
		$this->db->truncate("prd_program_information");
		
		db_log_message($this->db->last_query());
	}
	
	function get_priority()
	{
		$sql = "SELECT general_param_id, param_code, param_value FROM gen_general_param WHERE param_type = 'PRIORITY' ORDER BY seq_no ASC";
	
		$query = $this->db->query($sql);
		db_log_message($this->db->last_query());
		
		$priority = array();
		foreach($query->result_array() as $item)
		{
			$priority[$item["param_code"]] = $item["param_value"];
		}
		
		return $priority;
	}
	
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */