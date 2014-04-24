<?php

class Film_history_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function update($coil_group_code, $product_dtl_id, $theDate, $quantity, $machine_id, $index)
	{
		/*
		if (!is_int($product_dtl_id)) {
			settype($product_dtl_id, "integer");
		}
		*/
		
		if (!is_int($quantity)) {
			settype($quantity, "integer");
		}
		
		if (!is_int($index))
		{
			settype($index, "integer");
		}
		
		if ($index)
		{
			// Update
			$this->db->where("coil_group_code", $coil_group_code);
			$this->db->where("product_dtl_id", $product_dtl_id);
			$this->db->where("film_date", $theDate);
			$this->db->where("temp", $index);
			
			$data = array(
				'quantity' => $quantity,
				'mc_id' => $machine_id
			);
			
			$this->db->update("prd_film_quantity", $data);
		}
		else
		{
			// Insert
			$data = array(
				'coil_group_code' => $coil_group_code,
				'product_dtl_id' => $product_dtl_id,
				'film_date' => $theDate,
				'quantity' => $quantity,
				'mc_id' => $machine_id
			);
			
			$this->db->insert("prd_film_quantity", $data);
		}
	
		return TRUE;
	}
	
	function get_all_exclude($mc_id, $coil_group_code, $product_dtl_id, $theDate)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("mc_id <>" , $mc_id);
		$this->db->where("film_date", $theDate);
		
		$query = $this->db->get("prd_film_quantity");
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		return array();
	}
	
	function get_quantity_exclude($mc_id, $coil_group_code, $product_dtl_id, $theDate)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("mc_id" , $mc_id);
		// $this->db->where("film_date <=", $theDate);
		
		$this->db->select_sum("quantity");
		
		$query = $this->db->get("prd_film_quantity");
		
		// echo $this->db->last_query();
		
		if ($query->num_rows() > 0)
		{
			$temp = $query->row_array();
			return $temp["quantity"];
		}
		
		return 0;
	}
	
	function get_all($coil_group_code, $product_dtl_id)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->order_by("film_date", "desc");
		
		$query = $this->db->get("prd_film_quantity");
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		return array();
	}
	
	function get_unit($mc_id, $startDate, $endDate)
	{
		$this->db->where("mc_id" ,$mc_id);
		$this->db->where("film_date >= ", $startDate);
		$this->db->where("film_date <= ", $endDate);
		
		$this->db->select_sum("quantity");
		
		$query = $this->db->get("prd_film_quantity");
		
		// echo $this->db->last_query();
		
		if ($query->num_rows() > 0) 
		{
			$temp = $query->row_array();
			return $temp["quantity"];
		}
		
		return 0;
	}
	
	function get_item($theDate, $coil_group_code, $product_dtl_id)
	{
		$this->db->where("mc_id" ,$mc_id);
		$this->db->where("film_date", $theDate);
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		
		$query =  $this->db->get("prd_film_quantity");
		if ($query->num_rows() > 0)
		{
			$temp = $query->row_array();
			return $temp["mc_id"];
		}
		
		return 0;
	}
	
	function get_unit_total($mc_id)
	{
		$this->db->where("mc_id", $mc_id);
		
		$this->db->select_sum("quantity");
		
		$query = $this->db->get("prd_film_quantity");
		if ($query->num_rows() > 0) 
		{
			$temp = $query->row_array();
			return $temp["quantity"];
		}
		
		return 0;
	}
	
	function get_all_unit_by_date($mc_id, $coil_group_code, $product_dtl_id, $theDate)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("mc_id" , $mc_id);
		// $this->db->where("film_date <=", $theDate);
		// $this->db->where("temp", $index);
		
		$this->db->select_sum("quantity");
		
		$query = $this->db->get("prd_film_quantity");
		// echo $this->db->last_query();
		if ($query->num_rows() > 0)
		{
			$temp = $query->row_array();
			return $temp["quantity"];
		}
		
		return 0;
	}
	
	function get_all_unit($mc_id, $coil_group_code, $product_dtl_id)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("mc_id" , $mc_id);
		// $this->db->where("film_date <=", $theDate);
		// $this->db->where("temp", $index);
		
		$this->db->select_sum("quantity");
		
		$query = $this->db->get("prd_film_quantity");
		// echo $this->db->last_query();
		if ($query->num_rows() > 0)
		{
			$temp = $query->row_array();
			return $temp["quantity"];
		}
		
		return 0;
	}
	
	function get_all_unit_by_date_exclude($mc_id, $coil_group_code, $product_dtl_id, $theDate)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("mc_id <>" , $mc_id);
		// $this->db->where("film_date <=", $theDate);
		// $this->db->where("temp", $index);
		
		$this->db->select_sum("quantity");
		
		$query = $this->db->get("prd_film_quantity");
		
		// echo $this->db->last_query();
		if ($query->num_rows() > 0)
		{
			$temp = $query->row_array();
			return $temp["quantity"];
		}
		
		return 0;
	}
	
	function get_unit_total_index($mc_id, $coil_group_code, $product_dtl_id, $theDate, $index)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("mc_id" , $mc_id);
		$this->db->where("film_date <=", $theDate);
		$this->db->where("temp", $index);
		
		$this->db->select_sum("quantity");
		
		
		$query = $this->db->get("prd_film_quantity");
		echo $this->db->last_query();
		if ($query->num_rows() > 0)
		{
			$temp = $query->row_array();
			return $temp["quantity"];
		}
		
		return 0;
	}
	
	function get_quantity_index($mc_id, $coil_group_code, $product_dtl_id, $theDate, $index)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("mc_id" , $mc_id);
		$this->db->where("film_date", $theDate);
		$this->db->where("temp", $index);
		
		$query = $this->db->get("prd_film_quantity");
		if ($query->num_rows() > 0)
		{
			$temp = $query->row_array();
			return $temp["quantity"];
		}
		
		return 0;
	}
	
	function change_to_new_product($coil_group_code, $old_product_dtl_id, $new_product_dtl_id)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $old_product_dtl_id);
		
		$data = array(
			'product_dtl_id' => $new_product_dtl_id
		);
		
		$this->db->update("prd_film_quantity", $data);
		
		return TRUE;
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */