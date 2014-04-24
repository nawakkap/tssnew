<?php

class Film_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	/*function get_all($sort = 'coil_group_code', $sort_type = 'asc') 
	{
		$this->db->order_by($sort, $sort_type);
		$this->db->where("populate_flag", "N");
		$query = $this->db->get('vr_prd_film');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	
		return FALSE;
	}*/
	
	/*function get_history($sort = 'coil_id', $sort_type = 'asc') 
	{
		$this->db->order_by($sort, $sort_type);
		$this->db->where("populate_flag", "Y");
		$query = $this->db->get('vr_prd_film');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	
		return FALSE;
	}*/
	
	function get_all($sort = 'slit_date', $sort_type = 'asc', $theDate = FALSE)
	{
		if ($theDate === FALSE) 
		{
			$theDate = date('Y-m-d');
		}
	
		$str_sql = 'select pf.coil_group_code '
			. '	,pf.thickness '
			. '	,pf.width '
			. '	,pf.product_dtl_id '
			. '	,pf.slit_date '
			. '	,pf.unit '
			. '	,pf.weight '
			. '	,( select program_code from prd_product_produce where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id limit 1) as program_code '
			. '	,( select ppi.program_code_ext ' 
			. '		from prd_program_information ppi, prd_product_produce pp ' 
			. '		where pp.coil_group_code = pf.coil_group_code ' 
			. '			and pp.product_dtl_id = pf.product_dtl_id '
			. '			and ppi.product_dtl_id = pf.product_dtl_id '
			. '			and ppi.program_code = pp.program_code '
			. '		limit 1 ) as program_code_ext '
			. ' ,( select quantity from prd_film_quantity where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id and film_date = \'' . $theDate . '\' limit 1) as quantity '
			. ' ,( select sum(quantity) from prd_film_quantity where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id) as total_quantity '
			. ' ,( select pp.mc_id ' 
			. '		from prd_program_information ppi, prd_product_produce pp ' 
			. '		where pp.coil_group_code = pf.coil_group_code ' 
			. '			and pp.product_dtl_id = pf.product_dtl_id '
			. '			and ppi.product_dtl_id = pf.product_dtl_id '
			. '			and ppi.program_code = pp.program_code '
			. '		limit 1 ) as mc_id '
			. ' ,( select temp from prd_film_quantity where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id and film_date = \'' . $theDate . '\' limit 1) as q_index '
			. ' ,( select mc_id from mc_machine_ironing where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id limit 1 ) as ironing_machine '
			. ' from vr_prd_film pf '
			. 'where pf.populate_flag = \'N\' ';
		
		
		if (!is_null($sort) && $sort != '') {
			$str_sql .= 'order by ' . $sort . ' ' . $sort_type;
		}
		
		// echo $str_sql;
		
		db_log_message($str_sql);
		
		$query = $this->db->query($str_sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	
		return FALSE;
	}
	
	function get_history($sort = 'coil_id', $sort_type = 'asc',  $startDate, $endDate)
	{
	
		$str_sql = 'select pf.coil_group_code '
			. '	,pf.thickness '
			. '	,pf.width '
			. '	,pf.product_dtl_id '
			. '	,pf.slit_date '
			. '	,pf.unit '
			. '	,pf.weight '
			. '	,( select program_code from prd_product_produce where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id limit 1) as program_code '
			. '	,( select ppi.program_code_ext ' 
			. '		from prd_program_information ppi, prd_product_produce pp ' 
			. '		where pp.coil_group_code = pf.coil_group_code ' 
			. '			and pp.product_dtl_id = pf.product_dtl_id '
			. '			and ppi.product_dtl_id = pf.product_dtl_id '
			. '			and ppi.program_code = pp.program_code '
			. '		limit 1 ) as program_code_ext '
			. 'from vr_prd_film pf '
			. 'where pf.populate_flag = \'Y\' '
			. 'AND pf.slit_date BETWEEN \'' . $startDate . ' 00:00:00\' AND \'' . $endDate . ' 23:59:59\' ';
			
		// echo $str_sql;
		
		if (!is_null($sort) && $sort != '') {
			$str_sql .= 'order by ' . $sort . ' ' . $sort_type;
		}
		
		db_log_message($str_sql);
		
		$query = $this->db->query($str_sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
	
		return FALSE;
	}
	
	function search($searchType, $searchText, $populate_flag) 
	{
		$this->db->where($searchType, $searchText);
		$this->db->where("populate_flag", $populate_flag);
		$query = $this->db->get("vr_prd_film");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		return FALSE;
	}
	
	function get_empty_data() 
	{
		return FALSE;
	}
	
	function get_by_id($film_id)
	{
		$this->db->where('film_id', $film_id);
		$query = $this->db->get('vr_prd_film');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function get_all_by_product_id($product_dtl_id)
	{
		$this->db->distinct();
		$this->db->select('film_id');
		$this->db->order_by('film_id');
		$this->db->where('product_dtl_id', $product_dtl_id);
		$query = $this->db->get('prd_film_information');
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = array();
			
			foreach($query->result_array() as $item)
			{
				array_push($result, $item['film_id']);
			}
		
			return $result;
		}
		
		return FALSE;
	}
	
	function get_populate_date($film_id)
	{
		$this->db->where("film_id", $film_id);
		$this->db->select("populate_date");
		
		$query=  $this->db->get("prd_film_information");
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["populate_date"];
		}
		return FALSE;
	}
	
	function get_sum_weight_by_product_dtl_id($product_dtl_id)
	{
		$this->db->select("product_dtl_id");
		$this->db->select_sum("weight");
		
		$this->db->where("product_dtl_id", $product_dtl_id);
		
		$this->db->group_by("product_dtl_id");
		
		$query = $this->db->get("prd_film_information");
		if ($query->num_rows() > 0) {
			$result = $query->row_array();
			
			return $result["weight"];
		}
		return 0;
	}
	
	function get_sum_weight_by_coil_group_code_and_product_dtl_id($coil_group_code, $product_dtl_id)
	{
		$this->db->select_sum("weight");
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		
		$query = $this->db->get("vr_prd_film");
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			
			return $result["weight"];
		}
		return 0;
	}
	
	function get_sum_unit_by_coil_group_code_and_product_dtl_id($coil_group_code, $product_dtl_id)
	{
		$this->db->select_sum("unit");
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		
		$query = $this->db->get("vr_prd_film");
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			
			return $result["unit"];
		}
		return 0;
	}
	
	
	function get_all_by_group_code($coil_group_code)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$query = $this->db->get("vr_prd_film");
		
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
	
	function get_all_by_group_code_and_product_id($coil_group_code, $product_dtl_id, $program_code)
	{
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		// $this->db->where("program_code", $program_code);
		$query = $this->db->get("vr_prd_film");
		
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
	
	function get_all_by_product_id_with_film_status($product_dtl_id, $film_status)
	{
		$this->db->distinct();
		$this->db->order_by('film_id');
		$this->db->where('product_dtl_id', $product_dtl_id);
		$this->db->where('film_status', $film_status);
		$query = $this->db->get('vr_prd_film');
		
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
	
	function update_populate_flag_date($film_id, $populate_flag,  $populate_date)
	{
		$data = array(
			'populate_flag' => $populate_flag,
			'populate_date' => $populate_date
		);
		
		$result= TRUE;
		$this->db->where("film_id", $film_id);
		if ($this->db->update('prd_film_information', $data) === FALSE)
			$result = FALSE;
		
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function update_film_status($film_id, $film_status, $populate_date)
	{
		$data = array(
			'populate_flag' => $film_status,
			'populate_date' => $populate_date
		);
		
		$result = TRUE;
		$this->db->where("film_id", $film_id);
		if ($this->db->update('prd_film_information', $data) === FALSE)
			$result = FALSE;
		
		// echo $film_status." ".$film_id."aaa";	
			
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function insert(	$mode, $film_id, $thickness, $width, $weight, $unit, $film_status, $slit_date, $populate_flag, $populate_date,
							$product_dtl_id, $film_price, $record_change_by, $record_change_date) 
	{
		$data = array(
			'thickness' => $thickness,
			'width' => $width,
			'weight' => $weight,
			'unit' => $unit,
			'film_status' => $film_status,
			'slit_date' => $slit_date,
			'populate_flag' => $populate_flag,
			'populate_date' => $populate_date,
			'product_dtl_id' => $product_dtl_id,
			'film_price' => $film_price,
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date,
		);
				
		$result = TRUE;
		
		if ("ADD" == $mode) 
		{
			$data['film_id'] = $film_id;
			
			if ($this->db->insert('prd_film_information', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("film_id", $film_id);
			if ($this->db->update('prd_film_information', $data) === FALSE)
				$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function delete($film_id)
	{
		$result =  $this->db->delete('prd_film_information', array('film_id' => $film_id));
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function get_distinct_product() 
	{
		$this->db->distinct();
		$this->db->select("product_dtl_id");
		$query = $this->db->get("prd_film_information");
		if ($query->num_rows() > 0) 
		{
			$temp = $query->result_array();
			
			$result = array();
			
			for($i = 0; $i < count($temp); $i++) 
			{
				array_push($result, $temp[$i]['product_dtl_id']);
			}
			
			db_log_message($this->db->last_query());
			return $result;
			
		}
		
		db_log_message($this->db->last_query());
		return FALSE;
		
	}
	
	function get_film_by_product_id($product_id) 
	{
		$this->db->where("product_dtl_id", $product_id);
		$query = $this->db->get("vr_prd_film");
		
		db_log_message($this->db->last_query());
		
		$result = array();
		foreach($query->result_array() as $item) 
		{
			array_push($result, $item);
		}
				
		return $result;
	}
	
	function change_product_dtl_id($new_product_dtl_id, $old_product_dtl_id, $coil_group_code)
	{
		if (is_array($coil_group_code) === TRUE)
		{
		
			if (count($coil_group_code) == 0) 
			{
				return FALSE;
			}
		
			$coil_group_string = implode("','", $coil_group_code);
			$coil_group_string = "'" . $coil_group_string . "'";
			
			$sql = "UPDATE prd_film_information SET product_dtl_id = '". $new_product_dtl_id . "' WHERE film_id IN ( SELECT film_id FROM prd_coil_film_mapping WHERE coil_group_code IN (" . $coil_group_string .")) AND product_dtl_id = '" .  $old_product_dtl_id ."'";
			
			db_log_message($sql);
			
			$result = $this->db->query($sql);
			
			return $result;
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_product_in_film()
	{
		$this->db->distinct();
		$this->db->select("prd_product_detail.product_dtl_id,  prd_product_detail.product_name_en");
		$this->db->from("prd_film_information");
		$this->db->order_by("prd_product_detail.product_name_en");
		$this->db->join("prd_product_detail", "prd_film_information.product_dtl_id = prd_product_detail.product_dtl_id");
		$query = $this->db->get();

		$result = array();
		foreach($query->result_array() as $row)
		{
			$result[] = $row;
		}
		
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function edit_search($lot_no, $film_date, $thickness, $product_dtl_id)
	{
		$result = array();
		
		$this->load->helper("date_helper");
		$theDate = date_to_mysqldatetime($film_date, FALSE);
		
		$str_sql = 'select pf.coil_group_code '
			. '	,pf.thickness '
			. '	,pf.width '
			. '	,pf.product_dtl_id '
			. '	,pf.slit_date '
			. '	,pf.unit '
			. '	,pf.weight '
			. '	,( select program_code from prd_product_produce where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id limit 1) as program_code '
			. '	,( select ppi.program_code_ext ' 
			. '		from prd_program_information ppi, prd_product_produce pp ' 
			. '		where pp.coil_group_code = pf.coil_group_code ' 
			. '			and pp.product_dtl_id = pf.product_dtl_id '
			. '			and ppi.product_dtl_id = pf.product_dtl_id '
			. '			and ppi.program_code = pp.program_code '
			. '		limit 1 ) as program_code_ext '
			. ' ,( select quantity from prd_film_quantity where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id and film_date = \'' . $theDate . '\' limit 1) as quantity '
			. ' ,( select sum(quantity) from prd_film_quantity where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id) as total_quantity '
			. ' ,( select pp.mc_id ' 
			. '		from prd_program_information ppi, prd_product_produce pp ' 
			. '		where pp.coil_group_code = pf.coil_group_code ' 
			. '			and pp.product_dtl_id = pf.product_dtl_id '
			. '			and ppi.product_dtl_id = pf.product_dtl_id '
			. '			and ppi.program_code = pp.program_code '
			. '		limit 1 ) as mc_id '
			. ' ,( select temp from prd_film_quantity where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id and film_date = \'' . $theDate . '\' limit 1) as q_index '
			. ' ,( select mc_id from mc_machine_ironing where coil_group_code = pf.coil_group_code and product_dtl_id = pf.product_dtl_id limit 1 ) as ironing_machine '
			. ' from vr_prd_film pf '
			. 'where pf.populate_flag = \'N\' ';
			
		if ($lot_no)
		{
			$str_sql .= sprintf(" AND pf.coil_group_code = '%s'", mysql_real_escape_string($lot_no));
		}
		
		if ($thickness)
		{
			$str_sql .= sprintf(" AND pf.thickness = '%s'", mysql_real_escape_string($thickness));
		}
		
		if ($product_dtl_id)
		{
			$str_sql .= sprintf(" AND pf.product_dtl_id = '%s'", mysql_real_escape_string($product_dtl_id));
		}
		
		/*
		if (!is_null($sort) && $sort != '') {
			$str_sql .= 'order by ' . $sort . ' ' . $sort_type;
		}
		*/
		// echo $str_sql;
		
		db_log_message($str_sql);
		
		$query = $this->db->query($str_sql);
		return $query->result_array();
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */