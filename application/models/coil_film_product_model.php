<?php

class Coil_Film_Product_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_all($sort = 'slit_thickness', $sort_type = 'asc') 
	{
		return FALSE;
	}
	
	function get_empty_data() 
	{
		return FALSE;
	}
	
	function get_by_id($coil_id)
	{
		$this->db->where('coil_id', $coil_id);
		$query = $this->db->get("prd_coil_film_mapping");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		return FALSE;
	}
	
	function get_group_name_by_id($coil_id, $coil_lot_no)
	{
		$this->db->select("coil_group_code");
		$this->db->where('coil_id', $coil_id);
		$this->db->where("coil_lot_no", $coil_lot_no);
		$query = $this->db->get("prd_coil_film_mapping");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function get_by_group_code($group_code)
	{
		$this->db->where('coil_group_code', $group_code);
		$query = $this->db->get("prd_coil_film_mapping");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		return FALSE;
	}
	
	function get_group_code_by_film_id($film_id)
	{
		$this->db->select("coil_group_code");
		$this->db->where("film_id", $film_id);
		$query = $this->db->get('prd_coil_film_mapping');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			return $result['coil_group_code'];
		}
		
		return FALSE;
	}
	
	function get_group_code_by_slit_spec_id($slit_spec_id)
	{
		$this->db->distinct();
		$this->db->select("coil_group_code");
		$this->db->where("slit_spec_id", $slit_spec_id);
		$query = $this->db->get("prd_coil_film_mapping");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$temp = $query->result_array();
			$result = array();
			foreach($temp as $item)
			{
				$result[] = $item;
			}
			
			return $result;
		}
		
		return FALSE;
	}
	
	function get_group_code_in_film_id($film_id = array())
	{
		if (count($film_id) == 0)
		{
			return FALSE;
		}
		
		$film_sql = "";
		for($i = 0; $i < count($film_id); $i++) 
		{
			if ($i != count($film_id) - 1)
			{
				$film_sql .= $film_id[$i] . ",";
			}
			else
			{
				$film_sql .= $film_id[$i];
			}
		}
		
		
		$this->db->distinct();
		$this->db->select("coil_group_code");
		$this->db->where("film_id IN (" . $film_sql . ")");
		$query = $this->db->get('prd_coil_film_mapping');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result_array() as $item)
			{
				array_push($result, $item['coil_group_code']);
			}
			
			return $result;
		}
		
		return FALSE;
		
	}
	
		
	function get_all_by_group_code_and_slit_spec_id_and_slit_sub_no($coil_group_code, $slit_spec_id, $slit_sub_no)
	{
		$this->db->select("film_id");
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("slit_spec_id", $slit_spec_id);
		$this->db->where("slit_sub_no", $slit_sub_no);
		$query = $this->db->get("prd_coil_film_mapping");
		
		// echo $coil_group_code." ".$slit_spec_id." ".$slit_sub_no;
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result_array() as $item)
			{
				$result[] = $item["film_id"];
			}
			
			return $result;
		}
		return FALSE;
		
	}
	
	function get_film_id_by($coil_group_code, $slit_spec_id, $slit_sub_no)
	{
		$this->db->select("film_id");
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("slit_spec_id", $slit_spec_id);
		$this->db->where("slit_sub_no", $slit_sub_no);
		$this->db->limit(1);
		
		$query = $this->db->get("prd_coil_film_mapping");
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["film_id"];
		}
		return FALSE;
		
	}
	
	function insert(	$mode, $coil_id, $coil_lot_no, $po_id, $film_id, $slit_spec_id, $slit_sub_no, $coil_group_code, $record_change_by, $record_change_date) 
	{
		$data = array(
			'coil_group_code' => $coil_group_code,
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date,
		);
				
		$result = TRUE;
		
		if ("ADD" == $mode) 
		{
			$data['coil_id'] = $coil_id;
			$data['coil_lot_no'] = $coil_lot_no;
			$data['film_id'] = $film_id;
			$data["po_id"] = $po_id;
			$data['slit_spec_id'] = $slit_spec_id;
			$data['slit_sub_no'] = $slit_sub_no;
			
			if ($this->db->insert('prd_coil_film_mapping', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("coil_id", $coil_id);
			$this->db->where("coil_lot_no", $coil_lot_no);
			$this->db->where("po_id", $po_id);
			$this->db->where("film_id", $film_id);
			$this->db->where("slit_spec_id", $slit_spec_id);
			$this->db->where("slit_sub_no", $slit_sub_no);
			if ($this->db->update('prd_coil_film_mapping', $data) === FALSE)
				$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function delete($coil_id, $film_id, $slit_spec_id, $slit_sub_no)
	{
		$result =  $this->db->delete('prd_coil_film_mapping', array('coil_id' => $coil_id, "film_id" => $film_id, "slit_spec_id" => $slit_spec_id,  "slit_sub_no" => $slit_sub_no));
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function delete_by_coil_group_code($coil_group_code)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$result = $this->db->delete("prd_coil_film_mapping");
		db_log_message($this->db->last_query());
		return $result;
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */