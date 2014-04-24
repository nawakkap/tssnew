<?php

class Customer_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	public function add($customer_name)
	{
		$this->db->where("customer_name", $customer_name);
		$query = $this->db->get("crm_customer_information");
		
		if ($query && $query->num_rows() ==0)
		{
			$data = array(
				"customer_name" => $customer_name
			);
			
			$this->db->insert("crm_customer_information", $data);
		}
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */