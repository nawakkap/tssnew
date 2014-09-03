<?php

class Report_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function insert($mode, $product_display_id, $delivery, $backlog, $inventory)
	{
		$data = array(
			"delivery" => $delivery,
			"backlog" => $backlog,
			"inventory" => $inventory
		);
		
		$result = TRUE;
		if ("ADD" == $mode)
		{
			$data["product_display_id"] = $product_display_id;
			if ($this->db->insert('imp_rpt_production', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("product_display_id", $product_display_id);
			if ($this->db->update('imp_rpt_production', $data) === FALSE)
				$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function update_stock_kg_need($product_display_id, $stock_unit, $total_kg_need)
	{
		$data = array(
			"stock_unit" => $stock_unit,
			"total_kg_need" => $total_kg_need
		);
		
		$this->db->where("product_display_id", $product_display_id);
		$this->db->update("imp_rpt_production", $data);
	}
	
	function update_actual_slitted($currentDate, $coil_group_code, $slittedCoil, $slittedWeight, $mc_id)
	{
		$data = array(
			"slitted_coil" => $slittedCoil,
			"slitted_weight" => $slittedWeight,
			"machine" => $mc_id
		);
		
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("actual_slitted_date", $currentDate);
		$this->db->where("machine", $mc_id);
		$this->db->update("prd_rpt_actual_slitted", $data);

		//echo $this->db->last_query();
	}
	
	/*function insert_actual_slitted($currentDate,$coil_group_code, $slittedCoil, $slittedWeight, $mc_id)
	{
		$data = array(
			"actual_slitted_date" => $currentDate,
			"coil_group_code" => $coil_group_code,
			"slitted_coil" => $slittedCoil,
			"slitted_weight" => $slittedWeight,
			"machine" => $mc_id
		);
		
		$this->db->insert("prd_rpt_actual_slitted", $data);
		//echo $this->db->last_query();
		
	}*/
	
	function insert_actual_slitted($currentDate,$coil_group_code, $slittedCoil, $slittedWeight, $mc_id)
	{
		$data = array(
			"actual_slitted_date" => $currentDate,
			"coil_group_code" => $coil_group_code,
			"slitted_coil" => $slittedCoil,
			"slitted_weight" => $slittedWeight,
			"machine" => $mc_id
		);
		
		$sql = 'INSERT INTO prd_rpt_actual_slitted (actual_slitted_date, coil_group_code, slitted_coil, slitted_weight, machine)
		        VALUES (?, ?, ?, ?, ?)
		        ON DUPLICATE KEY UPDATE 
		            actual_slitted_date=VALUES(actual_slitted_date), 
		            coil_group_code=VALUES(coil_group_code), 
		            slitted_coil=VALUES(slitted_coil),
								slitted_weight=VALUES(slitted_weight), 
								machine=VALUES(machine)';

		$query = $this->db->query($sql, $data);
		
		//echo $this->db->last_query();
		
	}
	
	
	function get_vr_report()
	{
		$query = $this->db->get("vr_rpt_production");
		
		db_log_message($this->db->last_query());
		$result = array();
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		
		return $result;
	}
	
	
	
	function get_vr_overall_stock_report()
	{
		$query = $this->db->get("vr_rpt_overal");
		
		db_log_message($this->db->last_query());
		$result = array();
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		
		return $result;
	}
	
	function get_all_coil_by_date($startDate, $endDate)
	{
		$this->db->where("actual_slitted_date <=" , $endDate);
		$this->db->where("actual_slitted_date >=" , $startDate);
		$this->db->select_sum("slitted_coil");
		$this->db->select_sum("slitted_weight");
		
		$query = $this->db->get("prd_rpt_actual_slitted");
		
		/// echo $this->db->last_query();
		if ($query->num_rows() > 0)
		{
			$temp = $query->row_array();
			return array("slitted_coil" => $temp["slitted_coil"] , "slitted_weight" => $temp["slitted_weight"]);
		}
		
		return array("slitted_coil" => 0 , "slitted_weight" => 0);
	}

	
	function get_vr_finishgood_detail_report($sort = 'sortDate', $sort_type = 'desc')
	{
		//$this->db->order_by($sort, $sort_type);
		//$this->db->order_by('sortDate', 'desc');
		//$this->db->order_by('machine', 'desc');		
		$query = $this->db->get("vr_rpt_finishgood_detail");
		
		//echo $this->db->last_query();
		
		db_log_message($this->db->last_query());
		$result = array();
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		
		return $result;	
	}
	
	function get_date_diff_group_by_machine($startDate, $endDate)
	{
		$sql = "SELECT DISTINCT mc_id, COUNT( * ) AS Total FROM  `mc_machine_work_hour` WHERE work_date BETWEEN '" . $startDate . "' AND '" .$endDate . "'  GROUP BY mc_id";
		
		db_log_message($this->db->last_query());
		
		$query = $this->db->query($sql);
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			return $result;
		}
		
		return array();
	}
	
	public function insert_xppdor($start_date, $end_date, $order_date, $custname, $product_dtl_id, $sono, $order, $order_bal)
	{
		$data = array(
			"start_date" => $start_date,
			"end_date" => $end_date,
			"order_date" => $order_date,
			"custname" => $custname,
			"product_dtl_id" => $product_dtl_id,
			"sono" => $sono,
			"order" => $order,
			"order_bal" => $order_bal,
		);
		
		$this->db->insert("imp_rpt_production_xppdor", $data);
	}
	
	function get_vr_finishgood_report()
	{
		$query = $this->db->get("vr_rpt_finishgood");
		
		db_log_message($this->db->last_query());
		$result = array();
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		
		return $result;	
	}
	
	function get_vr_coil_received_report($sort = 'sortReceivedDate', $sort_type = 'desc')
	{
		$this->db->order_by($sort, $sort_type);
		$this->db->order_by('sortReceivedDate', 'desc');
		$query = $this->db->get("vr_rpt_coil_received");
		
		//echo $this->db->last_query();
		
		db_log_message($this->db->last_query());
		
		$result = array();
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		
		return $result;
		
		
	}
	
	function get_vr_slit_report($sort = 'sortSlitDate', $sort_type = 'desc')
	{
		$this->db->order_by($sort, $sort_type);	
		$this->db->order_by('sortSlitDate', 'desc');	
		$query = $this->db->get("vr_rpt_slit");
		
		//echo $this->db->last_query();
		
		db_log_message($this->db->last_query());
		$result = array();
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		
		return $result;	
		
	}
	
	function get_vr_slit_report_by_date($sort = 'sortSlitDate', $sort_type = 'desc')
	{
		//$this->db->order_by($sort, $sort_type);	
		//$this->db->order_by('sortSlitDate', 'desc');	
		$query = $this->db->get("vr_rpt_actual_slit_by_date");
		
		//echo $this->db->last_query();
		
		db_log_message($this->db->last_query());
		$result = array();
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		
		return $result;	
		
	}
	
	function get_vr_delivery($sort = 'custname', $sort_type = 'desc')
	{
		$this->db->order_by($sort, $sort_type);	
		//$this->db->order_by('custname', 'desc');	
		$query = $this->db->get("vr_rpt_delivery");
		
		//echo $this->db->last_query();
		
		db_log_message($this->db->last_query());
		$result = array();
		foreach($query->result_array() as $item)
		{
			$result[] = $item;
		}
		
		return $result;	
		
	}
	
	public function get_xppdor_detail($sono,$ranking, $productid)
	{
		$sql = sprintf("SELECT 
							imp_rpt_production_xppdor.*, 
							imp_rpt_production_priority.item,
							imp_rpt_production_priority.delivery_date, 
							imp_rpt_production_priority.priority,
							imp_rpt_production_priority.user 
						FROM imp_rpt_production_xppdor LEFT JOIN imp_rpt_production_priority 
						ON imp_rpt_production_xppdor.sono = imp_rpt_production_priority.sono 
						WHERE imp_rpt_production_xppdor.sono = '%s' AND 
						imp_rpt_production_priority.ranking = '%s' AND
						imp_rpt_production_xppdor.product_dtl_id = '%s'
						ORDER BY priority ASC, delivery_date ASC",
						mysql_real_escape_string($sono),
						mysql_real_escape_string($ranking),
						mysql_real_escape_string($productid)
					);
					
		$query = $this->db->query($sql);
		// echo $this->db->last_query();
		$result = array();
		if ($query->num_rows() > 0)
		{
			$result = $query->row_array();
			return $result;
		}
		
		return FALSE;
	}
	
	public function get_delivery_report_only_so($start_date, $end_date)
	{
			// $this->db->where("order_date between");
		
		$sql = sprintf("SELECT DISTINCT sono, product_dtl_id, delivery_date, ranking FROM imp_rpt_production_priority WHERE item > 0 AND delivery_date between '%s' AND '%s'",
						mysql_real_escape_string($start_date),
						mysql_real_escape_string($end_date)
					);
		
		$query = $this->db->query($sql);
		// echo $this->db->last_query();
		$result = array();
		foreach($query->result_array() as $row)
		{
			$result[]= $row;
		}
		
		return $result;
	}
	
	public function get_delivery_report($start_date, $end_date)
	{
		$this->db->where("order_date between");
		
		$sql = sprintf("SELECT * FROM imp_rpt_production_xppdor WHERE order_date between '%s' AND '%s' ORDER BY temp",
						mysql_real_escape_string($start_date),
						mysql_real_escape_string($end_date)
					);
		
		$query = $this->db->query($sql);
		//  echo $this->db->last_query();
		$result = array();
		foreach($query->result_array() as $row)
		{
			$result[]= $row;
		}
		
		return $result;
	}
	
	public function get_so_data($product_dtl_id)
	{
		/*
		$this->db->order_by("order_date");
		$this->db->where("product_dtl_id", $product_dtl_id);
		$query = $this->db->get("imp_rpt_production_xppdor");
		*/
		
		$sql = "SELECT imp_rpt_production_xppdor.*, imp_rpt_production_priority.delivery_date, ";
		$sql .= "imp_rpt_production_priority.priority, imp_rpt_production_priority.ranking, ";
		$sql .= "imp_rpt_production_priority.item, imp_rpt_production_priority.user ";
		$sql .= "FROM imp_rpt_production_xppdor LEFT JOIN imp_rpt_production_priority ON ";
		$sql .= "imp_rpt_production_xppdor.sono = imp_rpt_production_priority.sono AND ";
		$sql .= "imp_rpt_production_xppdor.product_dtl_id = imp_rpt_production_priority.product_dtl_id ";
		$sql .= sprintf("WHERE imp_rpt_production_xppdor.product_dtl_id = '%s'", 
					mysql_real_escape_string($product_dtl_id)
				);
		$sql .= " ORDER BY order_date, ranking";
		
		$query = $this->db->query($sql);
		// echo $this->db->last_query();
	
		$result = array();
		foreach($query->result_array() as $row)
		{
			$result[] = $row;
		}
	
		return $result;
	}
	
	public function add_delivery($sono, $product_dtl_id, $delivery_date, $item, $priority, $ranking = 1, $user)
	{
		$where = array(
			"sono" => $sono,
			"product_dtl_id" => $product_dtl_id,
			"ranking" => $ranking
		);
		
		$query = $this->db->get_where("imp_rpt_production_priority", $where);
		
		if ($query->num_rows() > 0)
		{
			// Update
			// echo "Update";
			$data = array(
				"item" => $item,
				"delivery_date" => $delivery_date,
				"priority" => $priority,
				"ranking" => $ranking,
				"user" => $user
			);
			
			$this->db->update("imp_rpt_production_priority", $data, $where);
		}
		else
		{
			// Add
			// echo "Insert";
			$data = array(
				"sono" => $sono,
				"item" => $item,
				"product_dtl_id" => $product_dtl_id,
				"delivery_date" => $delivery_date,
				"priority" => $priority,
				"ranking" => $ranking,
				"user" => $user
			);
			
			$this->db->insert("imp_rpt_production_priority", $data);
		}
	}
	
	function clear($start_date, $end_date)
	{
		$this->db->truncate("imp_rpt_production");
		$this->db->truncate("imp_rpt_production_xppdor");
		/*
		$this->db->where("start_date", $start_date);
		$this->db->where("end_date", $end_date);
		$this->db->delete("imp_rpt_production_xppdor");
		*/
	}	
}