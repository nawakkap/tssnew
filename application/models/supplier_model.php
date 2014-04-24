<?php

class Supplier_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_all($sort = 'supplier_name', $sort_type = 'asc') 
	{

		$this->db->order_by($sort, $sort_type);
		$query = $this->db->get("vr_crm_supplier");
		
		db_log_message($this->db->last_query());
		$result = array();
		
		$index = 0;
		foreach($query->result() as $item)
		{
			$result[$index++] = $item;
		}
		
		return $result;
		
	}
		
	function get_id_by_name($name)
	{
		$this->db->select("supplier_id");
		$query = $this->db->get_where("crm_supplier_information",array('supplier_name' => $name));
		
		db_log_message($this->db->last_query());
		if ($query->num_row() > 0) 
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function get_idname_list() 
	{
		$this->db->select('supplier_id , supplier_name');
		$query = $this->db->get('crm_supplier_information');
		
		db_log_message($this->db->last_query());
		$my_result = array();
		foreach($query->result() as $item)
		{
			$my_result[$item->supplier_id] = $item->supplier_name;
		}
		
		return $my_result;
	}
	
	function get_payment_term_map() // Get Default Payment Term
	{
		$this->db->select('supplier_id, default_payment_term');
		$query = $this->db->get('crm_supplier_information');
		
		db_log_message($this->db->last_query());
		$my_result= array();
		foreach($query->result() as $item)
		{
			$my_result[$item->supplier_id] = $item->default_payment_term;
		}
		
		return $my_result;
	}
	
	function get_nameid_list() 
	{
		$this->db->select('supplier_id , supplier_name');
		$query = $this->db->get('crm_supplier_information');
		
		db_log_message($this->db->last_query());
		$my_result = array();
		foreach($query->result() as $item)
		{
			$my_result[$item->supplier_name] = $item->supplier_id;
		}
		
		return $my_result;
	}
	
	
	function get_by_id($supplier_id)
	{
		$query = $this->db->get_where("crm_supplier_information", array("supplier_id" => $supplier_id));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function get_all_by_id($supplier_id) 
	{
		$query = $this->db->get_where("crm_supplier_information", array("supplier_id" => $supplier_id));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		
		return array();
	}
	
	function get_empty_data() 
	{
		$data = array(
			'supplier_id' => '',
			'supplier_name' => '',
			'address_1' => '',
			'address_2' => '',
			'address_postcode' => '',
			'tel_phone' => '',
			'tel_mobile' => '',
			'email' => '',
			'register_date' => '',
			'default_payment_term' => '0',
			'record_change_by' => '',
			'record_change_date' => '',
		);
		
		return $data;
	}
	
	function insert(	$mode, $supplier_id, $supplier_name, $default_payment_term, $address_1, $address_2, $address_postcode,
							$tel_phone, $tel_mobile, $email, $register_date, $record_change_by, $record_change_date) 
	{
		$data = array(
			'supplier_name' => $supplier_name,
			'default_payment_term' => $default_payment_term,
			'address_1' => $address_1,
			'address_2' => $address_2,
			'address_postcode' => $address_postcode,
			'tel_phone' => $tel_phone,
			'tel_mobile' => $tel_mobile,
			'email' => $email,
			'register_date' => $register_date,
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date,
		);
				
		$result = TRUE;
		
		if ("ADD" == $mode) 
		{
			$data['supplier_id'] = $supplier_id;
			
			if ($this->db->insert('crm_supplier_information', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("supplier_id", $supplier_id);
			if ($this->db->update('crm_supplier_information', $data) === FALSE)
				$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
	}
	
	function delete($supplier_id)
	{
		$result =  $this->db->delete('crm_supplier_information', array('supplier_id' => $supplier_id));
		
		db_log_message($this->db->last_query());
		
		return $result;
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */