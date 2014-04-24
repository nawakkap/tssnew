<?php

class Program_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_current($searchType, $searchText, $order_by = "p_detail.product_display_id", $order_type = "ASC")
	{
		$sql = 	"select " .  
				"p_program.program_code, " . 
				"p_program.program_code_ext, " . 
				"p_program.product_dtl_id, " . 
				"p_program.processing_date, " .
				"p_program.program_status, " .
				"p_program.closed_date, " .
				"p_detail.product_name_th, " .
				"p_detail.est_weight_min, " . 
				"p_detail.accounting_weight, " .
				"p_detail.actual_weight, " . 
				"(SELECT DISTINCT mc_id FROM prd_product_produce WHERE prd_product_produce.program_code = p_program.program_code LIMIT 1) as mc_id, " .
				"(SELECT prd_product_detail.est_weight FROM prd_product_detail WHERE prd_product_detail.product_dtl_id = p_program.product_dtl_id LIMIT 1) AS est_weight, " .
				"(select sum(weight) from prd_product_produce where program_code = p_program.program_code) as weight, " . 
				"(select sum(total_unit) from prd_program_information where total_unit >= 0 and product_dtl_id = p_program.product_dtl_id and program_code = p_program.program_code) as total_unit, " . 
				"(select fr_prd_get_avg_weight(p_program.program_code)) as avg_weight, " .
				"(select fr_prd_get_perct_grade_b(p_program.program_code)) as grade_b " .
				"from prd_program_information as p_program " .
				"left join prd_product_detail as p_detail " .
				"on p_program.product_dtl_id = p_detail.product_dtl_id " .
				"where program_status = '1' ";
				if (!empty($searchType) && !empty($searchText))
				{
					$sql .= " and " . $searchType . " like '%" . $searchText . "%' ";
				}
				$sql .= "group by p_program.program_code, p_program.product_dtl_id ";
				
				if (is_array($order_by))
				{

					$order_temp = "";
					for($i = 0; $i < count($order_by);$i++)
					{
						if (strlen($order_temp) > 0) 
						{
							$order_temp .= ", ";
						}
						$order_temp .= $order_by[$i] . " " . $order_type[$i];
					}
					
					$sql .= " order by " . $order_temp;
				}
				else
				{
					$sql .= " order by " . $order_by . " " . $order_type;
				}
				
		//echo $sql;
				
		$query = $this->db->query($sql);
		// echo $this->db->last_query();
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		return FALSE;
	}
	
	function get_history($searchType, $searchText, $order_by = array("p_program.closed_date", "p_program.processing_date"), $order_type = array("DESC", "DESC"), $startDate, $endDate)
	{
		$sql = 	"select " .  
				"p_program.program_code, " . 
				"p_program.program_code_ext, " . 
				"p_program.product_dtl_id, " . 
				"p_program.processing_date, " .
				"p_program.program_status, " .
				// "p_program.closed_date, " .
				"p_detail.product_name_th, " .
				"p_detail.est_weight_min, " . 
				"p_detail.accounting_weight, " .
				"p_detail.actual_weight, " . 
				"(select sum(weight) from prd_product_produce where program_code = p_program.program_code) as weight, " . 
				"(select sum(total_unit) from prd_program_information where total_unit >= 0 and product_dtl_id = p_program.product_dtl_id and program_code = p_program.program_code) as total_unit, " . 
				"(select fr_prd_get_avg_weight(p_program.program_code)) as avg_weight, " .
				"(select fr_prd_get_perct_grade_b(p_program.program_code)) as grade_b " .
				"from prd_program_information as p_program " .
				"left join prd_product_detail as p_detail " .
				"on p_program.product_dtl_id = p_detail.product_dtl_id " .
				"where program_status != '1' " .
				"AND p_program.processing_date BETWEEN '$startDate' AND '$endDate' ";
				if (!empty($searchType) && !empty($searchText))
				{
					$sql .= " and " . $searchType . " like '%" . $searchText . "%' ";
				}
				$sql .= "group by p_program.program_code, p_program.product_dtl_id ";
	
				if (is_array($order_by))
				{

					$order_temp = "";
					for($i = 0; $i < count($order_by);$i++)
					{
						if (strlen($order_temp) > 0) 
						{
							$order_temp .= ", ";
						}
						$order_temp .= $order_by[$i] . " " . $order_type[$i];
					}
					
					$sql .= " order by " . $order_temp;
				}
				else
				{
					$sql .= " order by " . $order_by . " " . $order_type;
				}
		
		// echo $sql;
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		return FALSE;
	}
	
	function get_program_status_by_program_code_and_product_dtl_id($program_code, $product_dtl_id)
	{
		$this->db->select("program_status");
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->limit(1);
		$query = $this->db->get("prd_program_information");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["program_status"];
		}
		return FALSE;
	}
	
	function get_external_program_code_by_program_code_and_product_dtl_id($program_code, $product_dtl_id)
	{
		$this->db->select("program_code_ext");
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->limit(1);
		$query = $this->db->get("prd_program_information");
		
		//echo $this->db->last_query();
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			//print_r($result);
			return $result["program_code_ext"];
		}
		return FALSE;
	}
	
	function get_external_program_code_normal_status_by_program_code_and_product_dtl_id($program_code, $product_dtl_id)
	{
		$this->db->select("program_code_ext");
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("program_status", 1);
		$this->db->limit(1);
		$query = $this->db->get("prd_program_information");
		
		//echo $this->db->last_query();
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			//print_r($result);
			return $result["program_code_ext"];
		}
		return FALSE;
	}
	
	function get_sum_line_by_program_code_and_product_dtl_id($program_code, $product_dtl_id)
	{
		$this->db->select_sum("total_unit");
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("total_unit >= 0");
		$query = $this->db->get("prd_program_information");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result  = $query->row_array();
			if ($result['total_unit']) {
				return $result['total_unit'];
			} else {
				return 0;
			}
		}
		return 0;
	}
	function get_program_code_and_ext_by_product_dtl_id_with_normal_status($product_dtl_id)
	{
		$this->db->select("program_code");
		$this->db->select("program_code_ext");
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->limit(1);
		$query = $this->db->get("prd_program_information");
		db_log_message($this->db->last_query());
		if ($query->num_rows()  > 0)
		{
			$result = $query->row_array();
			return $result;
		}
	}
	
	function get_sum_line_by_product_dtl_id($product_dtl_id)
	{
		$this->db->select_sum("total_unit");
		$this->db->where("total_unit > -1");
		$this->db->where("product_dtl_id", $product_dtl_id);
		$query = $this->db->get("prd_program_information");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result  = $query->row_array();
			if ($result['total_unit']) {
				return $result['total_unit'];
			} else {
				return 0;
			}
		}
		return 0;
	}
	
	function get_all_by_program_code_and_product_dtl_id($program_code, $product_dtl_id)
	{
		$this->db->order_by("processing_date", "asc");
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$query = $this->db->get("prd_program_information");
		
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
	
	function check_program_normal_status_by_product_dtl_id($program_code, $product_dtl_id)
	{
		$this->db->select("program_status");
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->limit(1);
		$query = $this->db->get("prd_program_information");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			if ($result["program_status"] === "1")
			{
				return TRUE;
			}
		}
		
		return FALSE;
	}
	
	function check_program_code_exist($program_code, $product_dtl_id)
	{
		$this->db->select_sum("total_unit");
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("program_status", "1");
		$this->db->group_by("program_code");
		$this->db->limit(1);
		$query = $this->db->get("prd_program_information");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->row_array();
			if (!isset($result["total_unit"]) || empty($result["total_unit"]) || $result["total_unit"] <= 0)
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
	function check_program_status($program_code, $product_dtl_id)
	{
		$this->db->select("program_status");
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->group_by("program_code");
		$this->db->limit(1);
		$query = $this->db->get("prd_program_information");
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->row_array();
			if (!isset($result["program_status"]) || empty($result["program_status"]) || $result["program_status"] == "1")
			{
				return FALSE;
			}
			else
			{
				return TRUE;
			}
		}
		return FALSE;
		
	}
	
	function get_program_code_by_external_program_code($external_program_code)
	{
		$this->db->where("program_code_ext", $external_program_code);
		$this->db->select("program_code");
		$this->db->limit(1);
		
		$query = $this->db->get("prd_program_information");
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result["program_code"];
		}
		
		return FALSE;
	}
	
	function get_all_program_code_by_external_program_code($external_program_code)
	{
		$this->db->where("program_code_ext", $external_program_code);
		$this->db->select("program_code");
		$this->db->distinct();
		
		$query = $this->db->get("prd_program_information");
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			$temp_result = array();
			for($i = 0; $i < count($result); $i++) {
				$temp_result[$i] = $result[$i]["program_code"];
			}
			
			return $temp_result;
		}
		
		return FALSE;
	}
	
	function call_function_avg_weight($program_code)
	{
		$sql = "SELECT fr_prd_get_avg_weight(?) as avg_weight";
		
		$query = $this->db->query($sql, array($program_code));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->result_array();
			return $result[0]['avg_weight'];
		} 

		return 0;
	}
	
	function call_function_grade_b($program_code)
	{
		$sql = "SELECT fr_prd_get_perct_grade_b(?) as grade_b";
		
		$query = $this->db->query($sql, array($program_code));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->result_array();
			return $result[0]['grade_b'];
		} 
		return 0;
	}
	
	function call_function_grade_b_cfu($program_code)
	{
		$sql = "SELECT fr_prd_get_perct_grade_b_cfu(?) as grade_b";
		
		$query = $this->db->query($sql, array($program_code));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->result_array();
			return $result[0]['grade_b'];
		} 
		return 0;
	}
	
	function call_avg_weight_before_slit($program_code)
	{
		$avg_weight = 0;
		$sum_unit = 0;
	
		$sql = sprintf("select coil_group_code, slit_spec_id, slit_sub_no 
            from prd_product_produce
            where program_code = '%s'",
				mysql_real_escape_string($program_code)
			);
	
		$main_query = $this->db->query($sql);
		// echo $this->db->last_query();
		
		db_log_message($this->db->last_query());
		if($main_query->num_rows() > 0)
		{
			// print_r($main_query->result_array());
			$main_temp = array();
			foreach($main_query->result_array() as $row)
			{	
				$main_temp[] = array(
					"coil_group_code" => $row["coil_group_code"],
					"slit_spec_id" => $row["slit_spec_id"],
					"slit_sub_no" => $row["slit_sub_no"]
				);
			}
			
			// $temp = $result[$i];
			for($ii = 0; $ii < count($main_temp); $ii++)
			{
				$coil_group_code = $main_temp[$ii]["coil_group_code"];
				$slit_spec_id = $main_temp[$ii]["slit_spec_id"];
				$slit_sub_no = $main_temp[$ii]["slit_sub_no"];
				
				$sql = sprintf("SELECT * FROM prd_coil_film_mapping 
						WHERE coil_group_code = '%s' AND slit_spec_id = '%s' 
						AND slit_sub_no = '%s'", 
						mysql_real_escape_string($coil_group_code),
						mysql_real_escape_string($slit_spec_id),
						mysql_real_escape_string($slit_sub_no));
				$query = $this->db->query($sql);
				
				// echo $this->db->last_query();
				db_log_message($this->db->last_query());
				
				$coil_id = array();
				$coil_lot_no = array();
				foreach($query->result_array() as $row)
				{
					$coil_id[] = $row["coil_id"];
					$coil_lot_no[] = $row["coil_lot_no"];
				}
				
				$weight = 0;
				for($i = 0; $i < count($coil_id); $i++)
				{
					$where = array(
						"coil_id" => $coil_id[$i],
						"coil_lot_no" => $coil_lot_no[$i]
					);
					$query = $this->db->get_where("prd_coil_information", $where);
					db_log_message($this->db->last_query());
					if ($query->num_rows() > 0)
					{
						foreach($query->result_array() as $row)
						{
							$weight += $row["weight"];
						}
					}
				}
				
				// Slit Spec
				$where = array(
					"slit_spec_id" => $slit_spec_id
				);
				$query = $this->db->get_where("prd_slit_specification", $where);
				db_log_message($this->db->last_query());
				
				$width = 0;
				$ss_width = 0;
				$ss_qty = 0;
				foreach($query->result_array() as $row)
				{
					$slit_width = $row["slit_width"];
					$slit_qty = $row["slit_qty"];
					
					if ($row["slit_spec_id"] == $slit_spec_id AND $row["slit_sub_no"] == $slit_sub_no)
					{
						$ss_width = $slit_width;
						$ss_qty = $slit_qty;
					}
					
					$width += ($slit_width * $slit_qty);
				}
			
				$avg_weight += ((($ss_width * $ss_qty) / $width) * $weight);
			
				// echo $sum_unit;
				/*
				$sql = sprintf("select coalesce(sum(weight), 0) as sum_weight
					from prd_product_produce
					where program_code = '%s'",
					mysql_real_escape_string($program_code));
				
				$query = $this->db->query($sql);
				db_log_message($this->db->last_query());
				if ($query->num_rows() > 0)
				{
					$temp = $query->row_array();
					$sum_weight = $temp["sum_weight"];
					
					// echo $sum_weight;
					if ($sum_weight > 0) {
						return $sum_weight / $sum_unit;
					}
				}
				*/
			}
			
			$sql = sprintf("select coalesce(sum(total_unit), 0) as sum_unit
				from prd_program_information
				where program_code = '%s'
					and total_unit > 0", 
					mysql_real_escape_string($program_code)
				);	
		
				$query = $this->db->query($sql);
				db_log_message($this->db->last_query());
				// echo ($this->db->last_query());
				if($query->num_rows() > 0)
				{
					$temp = $query->row_array();
					$sum_unit += $temp["sum_unit"];
					// echo $temp["sum_unit"] . " = " . $sum_unit . "<br/>";
				}
		}
		
		if ($sum_unit != 0)
		{
			return $avg_weight / $sum_unit;
		}
		
		return 0;
	}
	
	function call_function_cost($program_code, $product_dtl_id)
	{
		$avg_weight = $this->call_avg_weight_before_slit($program_code);
	
		// echo $avg_weight;
		if ($avg_weight != 0)
		{
			$sql = "select sum(coalesce(tb.price, 0) * tb.vat_price * tb.weight) as sum_price";
			$sql .= sprintf(" from ( select distinct pp.coil_group_code
					,oi.price
					,( case pp.vat_code when 'VAT_PREMIUM' then 1.04 else 1 end ) as vat_price
					,pp.weight
				from ord_order_information oi
					,prd_coil_film_mapping mp
					,prd_coil_information ci
					,prd_product_produce pp
				where ci.coil_id = mp.coil_id
					and ci.coil_lot_no = mp.coil_lot_no
					and pp.coil_group_code = mp.coil_group_code
					and pp.slit_spec_id = mp.slit_spec_id
					and pp.slit_sub_no = mp.slit_sub_no
					and ci.po_id = oi.po_id
					and pp.program_code = '%s' ) tb;",
					mysql_real_escape_string($program_code)
				);
			$query = $this->db->query($sql);
			// echo $this->db->last_query();
			
			if ($query->num_rows() > 0)
			{
				$temp = $query->row_array();
				$sum_price = $temp["sum_price"];
				
				// echo $sum_price;
				
				$sql = "select sum(coalesce(tb.weight, 0)) as sum_weight";
				$sql .= sprintf(" from ( select distinct pp.coil_group_code
						,oi.price
						,( case pp.vat_code when 'VAT_PREMIUM' then 1.04 else 1 end ) as vat_price
						,pp.weight
					from ord_order_information oi
						,prd_coil_film_mapping mp
						,prd_coil_information ci
						,prd_product_produce pp
					 where ci.coil_id = mp.coil_id
						and ci.coil_lot_no = mp.coil_lot_no
						and pp.coil_group_code = mp.coil_group_code
						and pp.slit_spec_id = mp.slit_spec_id
						and pp.slit_sub_no = mp.slit_sub_no
						and ci.po_id = oi.po_id
						and pp.program_code = '%s' ) tb;",
						mysql_real_escape_string($program_code)
					);
				$query= $this->db->query($sql);
				// echo $this->db->last_query();
				if ($query->num_rows() > 0)
				{
					$temp = $query->row_array();
					$sum_weight = $temp["sum_weight"];
					
					// echo ($sum_price / $sum_weight) *$avg_weight;
					// echo $sum_weight;
					if ($sum_weight != 0) {
						return ($sum_price / $sum_weight) * $avg_weight;
					} else {
						return 0;
					}
				}
			}
		}
		
		return 0;
		
		/*
		//echo "Program code : " . $program_code . "<br/>";
		//echo "Product Dtl id : " . $product_dtl_id . "<br/>";
		$sql = "SELECT fr_prd_get_avg_per_unit(?, ?) as cost";
		$query = $this->db->query($sql, array($program_code, $product_dtl_id));
		
		//echo $this->db->last_query() . "<br/>";
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->result_array();
			return $result[0]['cost'];
		}
		
		return 0;
		*/
	}
	
	function call_function_cost_old($program_code, $product_dtl_id)
	{
		$sql = "SELECT fr_prd_get_avg_per_unit(?, ?) as cost";
		$query = $this->db->query($sql, array($program_code, $product_dtl_id));
		
		//echo $this->db->last_query() . "<br/>";
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->result_array();
			return $result[0]['cost'];
		}
		
		return 0;
	}
	
	function call_function_cost_and_wage($program_code, $product_dtl_id)
	{
		$avg_weight = $this->call_avg_weight_before_slit($program_code);
	
		// echo $avg_weight;
		if ($avg_weight != 0)
		{
			$sql = "select sum(((coalesce(tb.price, 0) * tb.vat_price) + tb.expense) * tb.weight) as sum_price ";
			$sql .= sprintf(" from ( select distinct pp.coil_group_code
					,oi.price
					,( case pp.vat_code when 'VAT_PREMIUM' then 1.04 else 1 end ) as vat_price
					,pp.weight
					,pp.expense 
				from ord_order_information oi
					,prd_coil_film_mapping mp
					,prd_coil_information ci
					,prd_product_produce pp
				 where ci.coil_id = mp.coil_id
					and ci.coil_lot_no = mp.coil_lot_no
					and pp.coil_group_code = mp.coil_group_code
					and pp.slit_spec_id = mp.slit_spec_id
					and pp.slit_sub_no = mp.slit_sub_no
					and ci.po_id = oi.po_id
					and pp.program_code = '%s' ) tb;",
					mysql_real_escape_string($program_code)
				);
			$query = $this->db->query($sql);
			
			if ($query->num_rows() > 0)
			{
				$temp = $query->row_array();
				$sum_price = $temp["sum_price"];
				
				// echo $sum_price;
				
				$sql = "select sum(coalesce(tb.weight, 0)) as sum_weight";
				$sql .= sprintf(" from ( select distinct pp.coil_group_code
						,oi.price
						,( case pp.vat_code when 'VAT_PREMIUM' then 1.04 else 1 end ) as vat_price
						,pp.weight
					from ord_order_information oi
						,prd_coil_film_mapping mp
						,prd_coil_information ci
						,prd_product_produce pp
					 where ci.coil_id = mp.coil_id
						and ci.coil_lot_no = mp.coil_lot_no
						and pp.coil_group_code = mp.coil_group_code
						and pp.slit_spec_id = mp.slit_spec_id
						and pp.slit_sub_no = mp.slit_sub_no
						and ci.po_id = oi.po_id
						and pp.program_code = '%s' ) tb;",
						mysql_real_escape_string($program_code)
					);
				$query= $this->db->query($sql);
				if ($query->num_rows() > 0)
				{
					$temp = $query->row_array();
					$sum_weight = $temp["sum_weight"];
					
					// echo ($sum_price / $sum_weight) *$avg_weight;
					// echo $sum_weight;
					if ($sum_weight != 0) {
						// echo $avg_weight;
						return ($sum_price / $sum_weight) * $avg_weight;
					} else {
						return 0;
					}
				}
			}
		}
		
		return 0;
	
	
		/*
		$sql = "SELECT fr_prd_get_avg_cost_per_unit(?, ?) as cost_and_wage";
		
		$query = $this->db->query($sql, array($program_code, $product_dtl_id));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->result_array();
			return $result[0]['cost_and_wage'];
		} 
		return 0;
		*/
	}
	
	function call_function_get_program_code($product_dtl_id)
	{
		$sql = "SELECT fr_prd_get_program_code(?) as program_code";
		
		$query = $this->db->query($sql, array($product_dtl_id));
		
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$result = $query->result_array();
			return $result[0]['program_code'];
		} 
		return FALSE;
	}
	
	function update_program_status($program_code, $product_dtl_id, $program_status)
	{
		$data = array(
			"program_status" => $program_status,
			"closed_date" => date("Y-m-d H:i:s")
		);
		
		/*
		// Completed
		if ($program_status == 2)
		{
			$data["completed_date"] = date("Y-m-d H:i:s");
		}
		else if ($program_status == 3)
		{
			$data["cancelled_date"] = date("Y-m-d H:i:s");
		}
		*/
		
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->update("prd_program_information", $data);
		
		db_log_message($this->db->last_query());
	}
	
	function update_program_ext_code($program_code, $product_dtl_id, $program_code_ext)
	{
		$data = array(
			"program_code_ext" => $program_code_ext
		);
		
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->update("prd_program_information", $data);
		
		db_log_message($this->db->last_query());
	}
	
	function insert($mode, $program_code, $product_dtl_id, $total_unit, $product_grade, $processing_date, $program_status, $program_code_ext, $machine_id = "") 
	{
		$data = array(
			'total_unit' => $total_unit,
			'product_grade' => $product_grade,
			'processing_date' => $processing_date,
			'program_status' => $program_status,
			'program_code_ext' => $program_code_ext,
			'mc_id' => $machine_id
		);
				
		$result = TRUE;
		
		if ("ADD" == $mode) 
		{
			$data['program_code'] = $program_code;
			$data['product_dtl_id'] = $product_dtl_id;
			
			if ($this->db->insert('prd_program_information', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("program_code", $program_code);
			$this->db->where("product_dtl_id", $product_dtl_id);
			if ($this->db->update('prd_program_information', $data) === FALSE)
				$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function delete($program_code, $product_dtl_id)
	{
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->limit(1);
		$result =  $this->db->delete("prd_program_information");
		
		db_log_message($this->db->last_query());
		
		return $result;
	}

	function delete_all($program_code, $product_dtl_id)
	{
		$this->db->where("program_code", $program_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$result =  $this->db->delete("prd_program_information");
		
		db_log_message($this->db->last_query());
		
		return $result;
	}
	
	function get_sum_quantity_all($mc_id)
	{
		$sql = "SELECT SUM(total_unit) as total_unit FROM prd_program_information WHERE total_unit >= 0 AND mc_id = $mc_id";
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			$temp = $query->row_array();
			return $temp["total_unit"];
		}
		
		return 0;
	}
	
	function get_sum_quantity_by_machine_id($mc_id, $startDate, $endDate)
	{
		$startDate .= " 00:00:00";
		$endDate .= " 23:59:59";
		
		$sql = "SELECT mc_id, SUM(total_unit) as total_unit FROM prd_program_information WHERE total_unit >= 0 AND processing_date BETWEEN '$startDate' AND '$endDate' GROUP BY mc_id HAVING mc_id = $mc_id";
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return array();
	}
	
	function get_sum_quantity_all_machine($startDate, $endDate)
	{
		$startDate .= " 00:00:00";
		$endDate .= " 23:59:59";
		
		$sql = "SELECT mc_id, SUM(total_unit) as total_unit FROM prd_program_information WHERE total_unit >= 0 AND processing_date BETWEEN '$startDate' AND '$endDate' GROUP BY mc_id";
		db_log_message($sql);
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		
		return array();
	
	}
	
	function get_first_date_of_machine($mc_id)
	{
		$this->db->where("mc_id", $mc_id);
		$this->db->select("processing_date");
		$this->db->order_by("processing_date", "ASC");
		$this->db->limit(1);
		
		$query = $this->db->get("prd_program_information");
		
		// echo 
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			$temp = $query->row_array(); 
			return $temp["processing_date"];
		}
		
		return "";
	}
	
	function get_machine($program_code)
	{
		$this->db->where("program_code", $program_code);
		$this->db->select("mc_id");
		$this->db->limit(1);
		
		$query = $this->db->get("prd_program_information");
		db_log_message($this->db->last_query());
		
		if ($query->num_rows() > 0)
		{
			$temp = $query->row_array();
			return $temp["mc_id"];
		}
		
		return 0;
		
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */