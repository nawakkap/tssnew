<?php

class Product_produce_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_all()
	{
	
		$this->db->order_by("prd_product_produce.program_code");
		$this->db->distinct();
		$this->db->select("prd_product_produce.program_code");
		$this->db->select("prd_product_produce.weight");
		$this->db->select("prd_product_produce.product_dtl_id");
		$this->db->select("prd_program_information.program_status");
		
		$this->db->join("prd_program_information", "prd_product_produce.program_code = prd_program_information.program_code" , "left");
		$this->db->where("prd_program_information.program_status", "1");
		
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result_array() as $item)
			{
				$result[] = $item;
			}
			
			return $result;
		}
		
		return FALSE;
	}
	
	function get_history()
	{
		$this->db->order_by("prd_product_produce.program_code");
		$this->db->distinct();
		$this->db->select("prd_product_produce.coil_group_code");
		$this->db->select("prd_product_produce.program_code");
		$this->db->select("prd_product_produce.slit_spec_id");
		$this->db->select("prd_product_produce.slit_sub_no");
		$this->db->select("prd_product_produce.weight");
		$this->db->select("prd_product_produce.product_dtl_id");
		$this->db->select("prd_program_information.program_status");
		
		$this->db->join("prd_program_information", "prd_product_produce.program_code = prd_program_information.program_code" , "left");
		$this->db->where("prd_program_information.program_status !=", "1");
		
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result_array() as $item)
			{
				$result[] = $item;
			}
			
			return $result;
		}
		
		return FALSE;
	}
	
	function search($searchType, $searchText, $history)
	{
		
		
		$this->db->order_by("prd_product_produce.program_code");
		$this->db->distinct();
		$this->db->select("prd_product_produce.program_code");
		$this->db->select("prd_product_produce.weight");
		$this->db->select("prd_product_produce.product_dtl_id");
		$this->db->select("prd_program_information.program_status");
		
		$this->db->join("prd_program_information", "prd_product_produce.program_code = prd_program_information.program_code" , "left");
		
		$this->db->where($searchType, $searchText);
		if ($history === TRUE)
		{
			$this->db->where("prd_program_information.program_status !=", "1");
		}
		else
		{
			$this->db->where("prd_program_information.program_status", "1");
		}
		
		$query = $this->db->get("prd_product_produce");
		
		//echo $this->db->last_query();
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result_array() as $item)
			{
				$result[] = $item;
			}
			
			return $result;
		}
		
		return FALSE;
		
		
		
		if ($query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result_array() as $item)
			{
				$result[] = $item;
			}
			
			return $result;
		}
		return FALSE;
	}
	
	function get_by_group_code_and_product_id($coil_group_code, $product_dtl_id)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			return $result[0];
		}
		
		return FALSE;
	}
	
	function get_program_code_by_product_id($coil_group_code, $product_dtl_id)
	{
		$this->db->select("program_code");
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["program_code"];
		}
		
		return FALSE;
	}
	
	function count_by_program_code($program_code)
	{
		$this->db->where("program_code", $program_code);
		$this->db->from("prd_product_produce");
		return $this->db->count_all_results();
	}
	
	function get_coil_group_code_by_product_id($product_dtl_id)
	{	
		$this->db->distinct();
		$this->db->select("coil_group_code");
		$this->db->where("product_dtl_id", $product_dtl_id);
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->result_array();
			$coil_group_code_result = array();
			for($i = 0; $i < count($result); $i++) 
			{
				$coil_group_code_result[] = $result[$i]['coil_group_code'];
			}
			
			return $coil_group_code_result;
		}
		
		return array();
	}
	
	function get_coil_group_code_by_program_code($program_code)
	{
		$this->db->distinct();
		$this->db->select("coil_group_code");
		$this->db->where("program_code", $program_code);
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->result_array();
			$coil_group_code_result = array();
			for($i = 0; $i < count($result); $i++) 
			{
				$coil_group_code_result[] = $result[$i]['coil_group_code'];
			}
			
			return $coil_group_code_result;
		}
		
		return array();
	}
	
	function get_coil_group_code_by_program_code_and_product_id($program_code, $product_dtl_id)
	{
		$this->db->select("coil_group_code");
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result_array() as $item)
			{
				$result[] = $item['coil_group_code'];
			}
			
			$result = array_unique($result);
			
			return $result;
		}
		return FALSE;
	}
	
	function get_vat_by_coil_group_code($coil_group_code, $program_code)
	{
		$this->db->select("vat_code");
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("program_code", $program_code);
		$this->db->limit(1);
		
		$query = $this->db->get("prd_product_produce");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			//echo $result["vat_code"];
			if ($result["vat_code"] == "VAT_NORMAL")
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	function get_all_by_program_code_and_product_id($program_code, $product_dtl_id)
	{
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$query = $this->db->get("prd_product_produce");
		
		//echo $this->db->last_query();
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result_array() as $item)
			{
				$result[] = $item;
			}
			
			//$result = array_unique($result);
			
			//print_r($result);
			
			return $result;
		}
		return FALSE;
	}
	
	function get_wage_by_coil_group_code_and_product_id($coil_group_code, $product_dtl_id)
	{
		$this->db->select("expense");
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result['expense'];
		}
		return FALSE;
	}
	
	function update_machine_by_by_program_code($program_code, $machine_by)
	{
		$data = array(
			"machine_by" => $machine_by
		);
	
		$this->db->where("program_code" , $program_code);
		$this->db->update("prd_product_produce", $data);
		
		db_log_message($this->db->last_query());
	}
	
	function get_machine_by_by_program_code($program_code)
	{
		$this->db->select("machine_by");
		$this->db->distinct();
		$this->db->where("program_code", $program_code);
		$this->db->limit(1);
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["machine_by"];
		}
		return FALSE;
	}
	
	function get_machine_default_by_program_code($program_code)
	{
		$this->db->select("mc_id");
		$this->db->distinct();
		$this->db->where("program_code", $program_code);
		$this->db->limit(1);
		$query = $this->db->get("prd_product_produce");
		
		/// echo $this->db->last_query();
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["mc_id"];
		}
		
		return FALSE;
	}
	
	function get_program_code_by_coil_group_code($coil_group_code)
	{
		$this->db->distinct();
		$this->db->select("program_code");
		$this->db->select("product_dtl_id");
		$this->db->order_by("product_dtl_id");
		$this->db->where("coil_group_code", $coil_group_code);
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result_array() as $item)
			{
				$result[] = $item;
			}
			return $result;
		}
		return FALSE;
	}
	
	function get_distinct_product_dtl_id_and_program_code()
	{
		$this->db->distinct();
		$this->db->order_by("product_dtl_id");
		$this->db->select("product_dtl_id");
		$this->db->select("program_code");
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result_array() as $item)
			{
				$result[] = $item;
			}
			
			return $result;
		}
		
		return FALSE;
	}
	
	function get_machine_by($slit_spec_id, $product_dtl_id, $program_code)
	{
		$this->db->select("machine_by");
		$this->db->where("slit_spec_id", $slit_spec_id);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("program_code", $program_code);
		
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["machine_by"];
		}
		
		return FALSE;
	}
	
	function get_machine($slit_spec_id, $product_dtl_id, $program_code)
	{
		$this->db->select("mc_id");
		$this->db->where("slit_spec_id", $slit_spec_id);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("program_code", $program_code);
		
		$query = $this->db->get("prd_product_produce");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["mc_id"];
		}
		
		return "";
	}
	
	function get_program_code_by_prd_id($product_dtl_id)
	{
		$this->db->select("program_code");
		$this->db->where("product_dtl_id", $product_dtl_id);
		
		$query = $this->db->get("prd_product_produce");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result_array() as $item)
			{
				$result[] = $item["program_code"];
			}
			
			return $result;
		}
		
		return array();
		
	}
	
	function update_vat_and_wage($coil_group_code, $product_dtl_id, $vat_status, $vat_value, $wage)
	{
		$data = array(
			"vat_code" => $vat_status,
			"vat_price" => $vat_value,
			"expense" => $wage
		);
		
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->update("prd_product_produce", $data);
	}
	
	function update_product_dtl_id_and_program_code_by_program_code($new_product_dtl_id, $new_program_code, $old_program_code, $coil_group_code)
	{
		$this->db->where("program_code", $old_program_code);
		$this->db->where("coil_group_code", $coil_group_code);
		
		$data = array(
			'product_dtl_id' => $new_product_dtl_id,
			'program_code' => $new_program_code
		);
		
		$this->db->update('prd_product_produce', $data);
		
		db_log_message($this->db->last_query());
		
		return TRUE;
	}
	
	function update_machine($program_code, $product_dtl_id, $machine_id)
	{
		// Update all of coil_group_code
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("program_code", $program_code);
		
		if ($machine_id === FALSE) {
			$machine_id = ""; 
		}
		
		$data = array(
			"mc_id" => $machine_id
		);
		
		
		$this->db->update('prd_product_produce' , $data);
		
		db_log_message($this->db->last_query());
		
		// echo $this->db->last_query();
		
		return TRUE;
	}

	function insert(	$mode, $coil_group_code, $machine_by, $cost_price, $slit_spec_id, $slit_sub_no, $product_dtl_id, $program_code, $weight, $expense, $vat_code, $vat_price, $record_change_by, $record_change_date)
	{
		$data = array(
			'machine_by' => $machine_by,
			'cost_price' => $cost_price,
			'slit_spec_id' => $slit_spec_id,
			'slit_sub_no' => $slit_sub_no,
			'product_dtl_id' => $product_dtl_id,
			'program_code' => $program_code,
			'weight' => $weight,
			'expense' => $expense,
			'vat_code' => $vat_code,
			'vat_price' => $vat_price,
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date,
		);
				
		$result = TRUE;
		
		if ("ADD" == $mode) 
		{
			$data['coil_group_code'] = $coil_group_code;
			
			if ($this->db->insert('prd_product_produce', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("coil_group_code", $coil_group_code);
			if ($this->db->update('prd_product_produce', $data) === FALSE)
				$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function delete($coil_group_code)
	{
		$result =  $this->db->delete('prd_product_produce', array('coil_group_code' => $coil_group_code));
		db_log_message($this->db->last_query());
		return $result;
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */