<?php

class Slit_model extends CI_Model {

	function __construct()
	{
		parent::__construct();
		
		$this->load->database();
	}
	
	function get_lastest_id()
	{
		$sql = "SELECT *
			FROM  `prd_slit_specification` 
			WHERE CONVERT( slit_spec_id, UNSIGNED ) < 10000
			ORDER BY CONVERT( slit_spec_id, UNSIGNED ) DESC ";
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0)
		{
			$item = $query->row_array();
			
			return $item["slit_spec_id"];
		}
		
		return 1;
	}
	
	function get_all($sort = 'slit_thickness', $sort_type = 'asc', $thickness = "") 
	{
		
		$this->db->order_by($sort, $sort_type);
		if (!empty($thickness))
		{
			$this->db->where("slit_thickness", $thickness);
		}
		
		$query = $this->db->get("vr_prd_slit_spec");
		
		db_log_message($this->db->last_query());
		$result = array();
		
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
			'slit_spec_id' => '',
			0 => array(
				'slit_width' => "",
				'slit_thickness' => "",
				'slit_qty' => 0,
				'product_dtl_id' => '',
				"remark" => ""
			),
			1 => array(
				'slit_width' => "",
				'slit_thickness' => "",
				'slit_qty' => 0,
				'product_dtl_id' => '',
				"remark" => ""
			),
			2 => array(
				'slit_width' => "",
				'slit_thickness' => "",
				'slit_qty' => 0,
				'product_dtl_id' => '',
				"remark" => ""
			),
			'record_change_by' => '',
			'record_change_date' => '',
		);
		
		return $data;
	}
	
	function get_by_id($slit_spec_id)
	{
	
		$query = $this->db->get_where("prd_slit_specification", array("slit_spec_id" => $slit_spec_id));
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		return FALSE;
	}
	
	function get_width_by_id_and_sub_no($slit_spec_id, $slit_sub_no)
	{
		$this->db->where("slit_spec_id", $slit_spec_id);
		$this->db->where("slit_sub_no", $slit_sub_no);
		$this->db->select("slit_width");
		
		$query = $this->db->get("prd_slit_specification");
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		
		return FALSE;
	}
	
	function get_sum_percent_film_by_product_id_and_thickness($product_dtl_id, $thickness)
	{
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->where("slit_thickness", $thickness);
		
		$this->db->select("product_dtl_id");
		$this->db->select_sum("ratio");
		
		$this->db->group_by("product_dtl_id");
		
		$this->db->limit(1);
		
		$query = $this->db->get("prd_slit_specification");
		
		if ($query->num_rows() > 0) 
		{
			$result = $query->row_array();
			return $result["ratio"];
		}
		
		return 0;
	}
	
	function get_by_thickness($thickness)
	{
		$this->db->where("slit_thickness", $thickness);
		$query = $this->db->get("prd_slit_specification");
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		return FALSE;
	}
	
	function get_view_by_id($slit_spec_id)
	{
		$query = $this->db->get_where("vr_prd_slit_spec", array("slit_spec_id" => $slit_spec_id));
		db_log_message($this->db->last_query());
		if ($query->num_rows() > 0) 
		{
			return $query->result_array();
		}
		
		return FALSE;
	}
	
	function get_by_thickness_and_product_dtl_id($thickness, $product_dtl_id, $isMoreThan300 = FALSE)
	{
		if (!empty($thickness)) 
		{
			$this->db->where("slit_thickness", $thickness);
		}
		
		if (is_array($product_dtl_id))
		{
			$this->db->where_in("product_dtl_id", $product_dtl_id);
		}
		else
		{
			if (!empty($product_dtl_id)) 
			{
				$this->db->where("product_dtl_id", $product_dtl_id);
			}
		}
		
		
		
		$this->db->group_by("slit_spec_id");
		if (is_array($product_dtl_id))
		{
			$this->db->having("count(1) >= " . count($product_dtl_id));
		}
		else
		{
			if (!empty($product_dtl_id))
			{
				$this->db->having("count(1) >= 1");
			} 
			else
			{
				$this->db->having("count(1) >= 0");
			}
		}
		
		if ($isMoreThan300 !== FALSE)
		{
			$this->db->having("CONVERT( slit_spec_id, UNSIGNED ) <", "10000");
			$this->db->having("CONVERT( slit_spec_id, UNSIGNED ) >=", "300");
		}
		
		//$this->db->distinct();
		
		$this->db->select("slit_spec_id");
		$this->db->select("slit_thickness");
		$this->db->select_min("remark");
		
		$this->db->order_by("slit_thickness", "asc");
		$query = $this->db->get("prd_slit_specification");
		
		db_log_message($this->db->last_query());
		if ($query !== FALSE && $query->num_rows() > 0)
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
	
	function get_by_product_dtl_id($product_dtl_id)
	{		
		if (is_array($product_dtl_id))
		{
			$this->db->where_in("product_dtl_id", $product_dtl_id);
		}
		else
		{
			if (!empty($product_dtl_id)) 
			{
				$this->db->where("product_dtl_id", $product_dtl_id);
			}
		}
		
		$this->db->group_by("slit_spec_id");
		if (is_array($product_dtl_id))
		{
			$this->db->having("count(1) >= " . count($product_dtl_id));
		}
		else
		{
			if (!empty($product_dtl_id))
			{
				$this->db->having("count(1) >= 1");
			} 
			else
			{
				$this->db->having("count(1) >= 0");
			}
		}
		
		//$this->db->distinct();
		
		$this->db->select("slit_spec_id");
		$this->db->select("slit_thickness");
		$this->db->select_min("remark");
		
		$this->db->order_by("slit_thickness", "asc");
		$query = $this->db->get("prd_slit_specification");
		
		db_log_message($this->db->last_query());
		if ($query !== FALSE && $query->num_rows() > 0)
		{
			$result = array();
			foreach($query->result_array() as $item)
			{
				$where = array(
					"slit_spec_id" => $item["slit_spec_id"],
					"product_dtl_id" => $product_dtl_id
				);
				
				$squery = $this->db->get_where("prd_slit_specification", $where);
				
				$ratio = 0;
				if ($squery->num_rows() > 0)
				{
					$sresult = $squery->row_array();
					$ratio = $sresult["ratio"];
				}
				$item["product_ratio"] = $ratio;
				
				$result[]= $item;
			}
			
			return $result;
		}
		return FALSE;
	}
	
	function insert(	$mode, $slit_spec_id, $slit_sub_no, $slit_thickness, $slit_qty, $slit_width, $product_dtl_id, $ratio,
							$description, $type, $record_change_by, $record_change_date) 
	{
		$data = array(
			'slit_sub_no' => $slit_sub_no,
			'slit_thickness' => $slit_thickness,
			'slit_qty' => $slit_qty,
			'slit_width' => $slit_width,
			'product_dtl_id' => $product_dtl_id,
			'ratio' => $ratio,
			'remark' => $description,
			'rim' => $type,
			'record_change_by' => $record_change_by,
			'record_change_date' => $record_change_date,
		);
				
		$result = TRUE;
		
		if ("ADD" == $mode) 
		{
			$data['slit_spec_id'] = $slit_spec_id;
			
			if ($this->db->insert('prd_slit_specification', $data) === FALSE) 
				$result = FALSE;
		}
		else if ("EDIT" == $mode)
		{
			$this->db->where("slit_spec_id", $slit_spec_id);
			$this->db->where("slit_sub_no", $slit_sub_no);
			if ($this->db->update('prd_slit_specification', $data) === FALSE)
				$result = FALSE;
		}
		
		db_log_message($this->db->last_query());
		return $result;
	}
	
	function delete($slit_spec_id)
	{
		$result =  $this->db->delete('prd_slit_specification', array('slit_spec_id' => $slit_spec_id));
		db_log_message($this->db->last_query());
		return $result;
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */