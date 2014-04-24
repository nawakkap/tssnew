<?php

class Order_model extends CI_Model 
{	
	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
		
	}
	
	function get_all($sort = 'order_received_date', $sort_type = 'asc', $searchText = "")
	{
	
		$this->db->order_by($sort, $sort_type);
		if (isset($searchText) && !empty($searchText)) {
			$this->db->where("po_id", $searchText);
		}
		$this->db->where("order_status", "1"); // Order Status is normal.
		$this->db->order_by("po_id", "asc");
		$query = $this->db->get("vr_ord_order");
		
		db_log_message($this->db->last_query());
		//echo $this->db->last_query();
		$result = array();
		foreach($query->result() as $item)
		{
			$result[] = $item;
		}
		
		return $result;
	}
	
	function get_history_all($sort = "order_received_date", $sort_type = "asc", $searchText = "", $startDate, $endDate)
	{
		$this->db->order_by($sort, $sort_type);
		if (isset($searchText) && !empty($searchText)) {
			$this->db->where("po_id", $searchText);
		}
		$this->db->where("order_status !=", "1"); // Order Status is normal.
		$this->db->where("order_received_date BETWEEN '$startDate' AND '$endDate' ");
		$this->db->order_by("po_id", "asc");
		$query = $this->db->get("vr_ord_order");
		
		db_log_message($this->db->last_query());
		
		$result = array();
		foreach($query->result() as $item)
		{
			$result[] = $item;
		}
		
		return $result;
	}
	
	function get_all_by_thickness($po_id, $thickness)
	{
		$this->db->order_by("po_id", $po_id);
		if (!empty($po_id))
		{
			$this->db->where("po_id", $po_id);
		}
		
		if (!empty($thickness))
		{
			$this->db->where("thickness", $thickness);
		}
		
		//$this->db->where("weight_remaining >", "0");
		$this->db->where("( order_status = '1' OR order_status = '2')");
		
		$query = $this->db->get("vr_ord_order");
		
		db_log_message($this->db->last_query());
		
		$result = array();
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		
		return $result;		
	}
	
	function get_total_row()
	{
		$total =  $this->db->count_all('vr_ord_order');
		
		db_log_message($this->db->last_query());
		
		return $total;
	}
	
	function get_empty_data() 
	{
		$data = array(
			'po_id' => '',
			'order_received_date' => date( 'Y-m-d H:i:s'),
			'supplier_id' => '',
			'thickness' => '0',
			'width' => '0',
			'weight' => '0',
			'payment_term' => '',
			'amt_current_invoice' => '',
			'vat_status' => '',
			'order_status' => '1',
			'price_base' => '0',
			'price' => '0',
			'record_change_by' => '',
			'record_change_date' => '',
		);
		
		return $data;
	}
	
	function get_by_id($po_id)
	{
		$query = $this->db->get_where("ord_order_information", array("po_id" => $po_id));
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function get_by_supplier_name($supplier_name) 
	{
		$query = $this->db->get_where("vr_ord_order", array('supplier_name' => $supplier_name));
		db_log_message($this->db->last_query());
		
		return $query->result_array();
	}
	
	function update_status($po_id, $order_status) 
	{
		$this->db->where('po_id', $po_id);
		
		$data = array(
			'order_status' => $order_status
		);
		
		if ($this->db->update('ord_order_information', $data) === FALSE)
		{
			db_log_message($this->db->last_query());
			return FALSE;
		}
			
		db_log_message($this->db->last_query());
		return TRUE;
	}
	
	function get_price_by_po_id($po_id) 
	{
		$this->db->select("price");
		$this->db->where("po_id", $po_id);
		$query = $this->db->get("ord_order_information");
		
		db_log_message($this->db->last_query());
		
		if ($query->num_rows() > 0) 
		{
			$result = $query->row_array();
			return $result['price'];
		}
		
		return 0;
		
	}
	
	function get_width_by_po_id($po_id)
	{
		$this->db->select("width");
		$this->db->where("po_id", $po_id);
		$query = $this->db->get("ord_order_information");
		
		db_log_message($this->db->last_query());
		
		if ($query->num_rows() > 0) 
		{
			$result = $query->row_array();
			return $result['width'];
		}
		
		return FALSE;
	}
	
	function get_thickness_by_po_id($po_id)
	{
		$this->db->select("thickness");
		$this->db->where("po_id", $po_id);
		$query = $this->db->get("ord_order_information");
		
		db_log_message($this->db->last_query());
		
		if ($query->num_rows() > 0) 
		{
			$result = $query->row_array();
			return $result['thickness'];
		}
		
		return FALSE;
	}
	
	function get_sum_weight_by_thickness($thickness)
	{	
		$this->db->where("thickness", $thickness);
		$this->db->where("order_status", "1");
		$this->db->select_sum("weight_remaining");
		
		$query = $this->db->get("vr_ord_order");
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["weight_remaining"];
		}
		return 0;
	}
	
	function insert(	$mode, $po_id, $order_received_date, $supplier_id, $thickness, $width, $weight, $payment_term, 
							$amt_current_invoice, $vat_status, $order_status, $price_base, $price, $record_change_by, $record_change_date) 
	{
		$data = array(
			'order_received_date' => $order_received_date,
			'supplier_id' => $supplier_id,
			'thickness' => $thickness,
			'width' => $width,
			'weight' => $weight,
			'payment_term' => $payment_term,
			'amt_current_invoice' => $amt_current_invoice,
			'vat_status' => $vat_status,
			'order_status' => $order_status,
			'price_base' => $price_base,
			'price' => $price,
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date,
		);
				
		$result = TRUE;
		
		if ("ADD" == $mode) 
		{
			$data['po_id'] = $po_id;
			
			if ($this->db->insert('ord_order_information', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("po_id", $po_id);
			if ($this->db->update('ord_order_information', $data) === FALSE)
				$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		
		return $result;
	}
	
	function delete($po_id)
	{
		$result =  $this->db->delete('ord_order_information', array('po_id' => $po_id));
		db_log_message($this->db->last_query());
		return $result;
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */