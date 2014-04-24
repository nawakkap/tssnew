<?php

class Product_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_all_dont_care($sort = 'product_display_id', $sort_type = 'asc') 
	{
		$this->db->order_by($sort, $sort_type);

		$query = $this->db->get("prd_product_detail");
		
		db_log_message($this->db->last_query());
		//echo $this->db->last_query();
		$result = array();
		
		$index = 0;
		foreach($query->result() as $item)
		{
			$result[$index++] = $item;
		}
		
		return $result;
	}
	
	function get_all($sort = 'product_display_id', $sort_type = 'asc', $active = "Y") 
	{
		
		$this->db->order_by($sort, $sort_type);
		if ($active == "Y" OR $active == "N")
		{
			$this->db->where("in_production", $active);
		}
		$query = $this->db->get("prd_product_detail");
		
		db_log_message($this->db->last_query());
		//echo $this->db->last_query();
		$result = array();
		
		$index = 0;
		foreach($query->result() as $item)
		{
			$result[$index++] = $item;
		}
		
		return $result;
		
	}
	
	function get_all_by_thickness($thickness)
	{
		$this->db->where("thickness_rep", $thickness);
		$this->db->where("in_production", "Y");
		$this->db->order_by("product_display_id", "asc");
		$query = $this->db->get("prd_product_detail");
		
		db_log_message($this->db->last_query());
		$result = array();
		
		// echo $this->db->last_query();
		
		$index = 0;
		foreach($query->result() as $item)
		{
			$result[$index++] = $item;
		}
		
		return $result;
	}
	
	function get_empty_data() 
	{
		$data = array(
			'product_dtl_id' => '',
			'product_display_id' => '',
			'product_name_th' => '',
			'product_name_en' => '',
			'product_name_initial' => '',
			'color' => '',
			'thickness' => 0,
			'thickness_rep' => 0,
			'thickness_min' => 0,
			'size_detail' => '',
			'film_size' => 0,
			'est_weight' => 0,
			'est_weight_min' => 0,
			'actual_weight' => 0,
			'accounting_weight' => 0,
			'wage_per_kilo' => 0,
			'weight_display' => '',
			'piece_per_pack' => 0,
			'piece_per_truck' => 0,
			'perct_of_films' => 0,
			'in_production' => ''
		);
		
		return $data;
	}
	
	function get_by_id($product_dtl_id)
	{
		$query = $this->db->get_where("prd_product_detail", array("product_dtl_id" => $product_dtl_id));
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function get_unit_by_product_id($product_dtl_id)
	{
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->select("total_unit");
		$query = $this->db->get("vr_prd_product_stock");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			return $result[0]['total_unit'];
		}
		
		return 0;
	}
	
	function get_wage_by_product_id($product_dtl_id)
	{
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->select("wage_per_kilo");
		$query = $this->db->get("prd_product_detail");
		
		db_log_message($this->db->last_query());
		
		if ($query->num_rows() > 0) 
		{
			$result = $query->row_array();
			return $result['wage_per_kilo'];
		}
		
		return 0;
	}
	
	function get_est_weight_product_id($product_dtl_id)
	{
		$this->db->select("est_weight");
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->limit(1);
		$query = $this->db->get("prd_product_detail");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["est_weight"];
		}
		return 0;
	}
	
	public function get_product_by_display_id($product_display_id)
	{
		$query = $this->db->get_where("prd_product_detail", array("product_display_id" => $product_display_id));
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	public function get_all_product_display_id()
	{
		$this->db->distinct();
		$this->db->select("product_display_id");
		$query = $this->db->get("prd_product_detail");
		
		$item = array();
		foreach($query->result_array() as $row)
		{
			$item[] = $row["product_display_id"];
		}
		
		return $item;
	}
	
	function get_product_by_width_minmax5($width)
	{
		// $width = settype($width, "double");
		
		$this->db->where("CAST(film_size AS DECIMAL) < " , ($width + 5));
		$this->db->where("CAST(film_size AS DECIMAL) > " , ($width - 5));
		
		$query = $this->db->get("prd_product_detail");
		
		// echo $this->db->last_query();
		
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		return array();
	}
	
	function insert(	$mode, $product_dtl_id, $product_display_id, $product_name_th, $product_name_en, $color, $thickness, $thickness_rep, $thickness_min, 
							$film_size, $size_detail, $est_weight, $est_weight_min, $actual_weight, $accounting_weight, $weight_display, $piece_per_pack,
							$piece_per_truck, $wage_per_kilo, $perct_of_films, $in_production, $record_change_by, $record_change_date, $product_name_initial) 
	{
		$data = array(
			'product_display_id' => $product_display_id,
			'product_name_th' => $product_name_th,
			'product_name_en' => $product_name_en,
			'product_name_initial' => $product_name_initial,
			'color' => $color,
			'thickness' => $thickness,
			'thickness_rep' => $thickness_rep,
			'thickness_min' => $thickness_min,
			'film_size' => $film_size,
			'size_detail' => $size_detail,
			'est_weight' => $est_weight,
			'est_weight_min' => $est_weight_min,
			'actual_weight' => $actual_weight,
			'accounting_weight' => $accounting_weight,
			'weight_display' => $weight_display,
			'piece_per_pack' => $piece_per_pack,
			'piece_per_truck' => $piece_per_truck,
			'wage_per_kilo' => $wage_per_kilo,
			'perct_of_films' => $perct_of_films,
			'in_production' => $in_production,
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date,
		);
				
		$result = TRUE;
		
		if ("ADD" == $mode) 
		{
			$data['product_dtl_id'] = $product_dtl_id;
			
			if ($this->db->insert('prd_product_detail', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("product_dtl_id", $product_dtl_id);
			if ($this->db->update('prd_product_detail', $data) === FALSE)
				$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function delete($product_dtl_id)
	{
		$result =  $this->db->delete('prd_product_detail', array('product_dtl_id' => $product_dtl_id));
		db_log_message($this->db->last_query());
		return $result;
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */