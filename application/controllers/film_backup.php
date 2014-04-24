<?php

class Film extends CI_Controller {

	private static $PERMISSION = "FILM";

	function __construct()
	{
		parent::__construct();	
		
		check_permission(self::$PERMISSION);
		
		$this->load->model("film_model");
		$this->load->model("product_model");
		$this->load->model("coil_film_product_model", "cfp_model");
		$this->load->model("program_model");
		$this->load->model("product_produce_model", "pp_model");
	}
	
	function index()
	{
		$searchText = $this->input->post("searchText");
		$searchType = $this->input->post("searchType");
		if (!isset($searchType)) {
			$searchType = "";
		}
		if (!isset($searchText)) {
			$searchText = "";
		}
		
		$is_filter = FALSE;
		
		$film_result = FALSE;
		if (("external_program_code" == $searchType || "product_name" == $searchType) || empty($searchType) || empty($searchText)) 
		{
			$film_result = $this->film_model->get_all("slit_date", "desc");
			if (("external_program_code" == $searchType || "product_name" == $searchType) && !empty($searchText))
			{
				$is_filter = TRUE;
			}
			else
			{
				$is_filter = FALSE;
			}
		}
		else
		{
			$film_result = $this->film_model->search($searchType, $searchText, "N");
			$is_filter = FALSE;
		}
		
		if ($film_result !== FALSE) 
		{
			$result = array();
			foreach($film_result as $item)
			{
				if (isset($result[$item['coil_group_code'] . "$" . $item['thickness'] . "$" . $item['width'] . "$" . $item['product_dtl_id']])) 
				{
					$temp = $result[$item['coil_group_code'] . "$" . $item['thickness'] . "$" . $item['width'] . "$" . $item['product_dtl_id']];
					$temp[] = $item;
				}
				else
				{
					$temp = array();
					$temp[] = $item;
				}
				
				$result[$item['coil_group_code'] . "$" . $item['thickness'] . "$" . $item['width'] . "$" . $item['product_dtl_id']] = $temp;
			}
			
			$product_result_temp = $this->product_model->get_all();
			$product_result = array();
			if ($product_result_temp !== FALSE) {
				foreach($product_result_temp as $item) {
					$product_result[$item->product_dtl_id] = $item->product_name_th;
				}
			}
			
			$data["product_result"] = $product_result;
			
			$film_result = array();
			// Summary
			foreach($result as $items)
			{		
				$coil_group_code = "";
				$thickness = "";
				$width = "";
				$unit = 0;
				$weight = 0;
				$program_code = "";
				$product_dtl_id = "";
				$slit_date = "";
				
				foreach($items as $item)
				{
					$coil_group_code = $item['coil_group_code'];
					$thickness = $item['thickness'];
					$width = $item['width'];
					$product_dtl_id = $item['product_dtl_id'];
					$program_code = $this->pp_model->get_program_code_by_product_id($coil_group_code, $product_dtl_id);
					$slit_date = $item['slit_date'];
					$unit += $item['unit'];
					$weight += $item['weight'];

				}
				
				$external_program_code = $this->program_model->get_external_program_code_by_program_code_and_product_dtl_id($program_code, $product_dtl_id);
				
				$temp = array(
					"coil_group_code" => $coil_group_code,
					"thickness" => $thickness,
					"width" => $width,
					"slit_date" => $slit_date,
					"program_code" => $program_code,
					"unit" => $unit,
					"weight" => $weight,
					"external_program_code" => $external_program_code,
					"product_dtl_id" => $product_dtl_id
				);
				
				if ($is_filter)
				{
					if ("external_program_code" == $searchType)
					{
						if ($external_program_code !== $searchText)
						{
							continue;
						}
					}
					else if ("product_name" == $searchType)
					{
						$product = $product_result[$product_dtl_id];
						if (strpos($product, $searchText) == false)
						{
							continue;
						}
					}
					
				}
				
				$film_result[] = $temp;
			}
		}
		if ($film_result === FALSE) $film_result = array();
		
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		$sort_list = array();
		if (!empty($sort_column))
		{
			foreach($film_result as $item)
			{
				$sort_list[$item[$sort_column]][] = $item;
			}
			
			if ($sort_by == "asc")
			{
				ksort($sort_list);
			}
			else
			{
				krsort($sort_list);
			}
			$data["sort_column"] = $sort_column;
			$data["sort_by"] = ("asc" == $sort_by) ? "desc" : "asc";
		}
		else
		{
			foreach($film_result as $item)
			{
				$sort_list[$item["slit_date"]][] = $item;
			}
			krsort($sort_list);
			$data["sort_column"] = "slit_date";
			$data["sort_by"] = "desc";
		}
		
		
		
		
		
		$data["film_result"] = $sort_list;
		
		
		// Navigation
		$selected = "Film Summary";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			$selected => "/film",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'film_list_view', $data);
		$this->template->render();
	}
	
	function history()
	{
		$starttime = time();
		$searchText = $this->input->post("searchText");
		$searchType = $this->input->post("searchType");
		if (!isset($searchType)) {
			$searchType = "";
		}
		if (!isset($searchText)) {
			$searchText = "";
		}
		
		$is_filter = FALSE;
		
		$film_result = FALSE;
		if (("external_program_code" == $searchType || "product_name" == $searchType) || empty($searchType) || empty($searchText)) 
		{
			$film_result = $this->film_model->get_history("slit_date", "desc");
			if (("external_program_code" == $searchType || "product_name" == $searchType) && !empty($searchText))
			{
				$is_filter = TRUE;
			}
			else
			{
				$is_filter = FALSE;
			}
		}
		else
		{
			$film_result = $this->film_model->search($searchType, $searchText, "Y");
			$is_filter = FALSE;
		}
		$endtime = time();
		echo "Get History : " . ($endtime - $starttime) . "<br/>";
		
		
		if ($film_result !== FALSE) 
		{
			$result = array();
			$starttime = time();
			foreach($film_result as $item)
			{
				if (isset($result[$item['coil_group_code'] . "$" . $item['thickness'] . "$" . $item['width'] . "$" . $item['product_dtl_id']])) 
				{
					$temp = $result[$item['coil_group_code'] . "$" . $item['thickness'] . "$" . $item['width'] . "$" . $item['product_dtl_id']];
					$temp[] = $item;
				}
				else
				{
					$temp = array();
					$temp[] = $item;
				}
				
				$result[$item['coil_group_code'] . "$" . $item['thickness'] . "$" . $item['width'] . "$" . $item['product_dtl_id']] = $temp;
			}
			$endtime = time();
			echo "Group Film : " . ($endtime - $starttime) . "<br/>";
			$startime= time();
			$product_result_temp = $this->product_model->get_all();
			$product_result = array();
			if ($product_result_temp !== FALSE) {
				foreach($product_result_temp as $item) {
					$product_result[$item->product_dtl_id] = $item->product_name_th;
				}
			}
			
			$data["product_result"] = $product_result;
			$endtime = time();
			echo "Get Product : " . ($endtime - $starttime) . "<br/>";
			$starttime = time();
			$film_result = array();
			// Summary
			foreach($result as $items)
			{		
				$coil_group_code = "";
				$thickness = "";
				$width = "";
				$unit = 0;
				$weight = 0;
				$program_code = "";
				$product_dtl_id = "";
				$slit_date = "";
				
				foreach($items as $item)
				{
					$coil_group_code = $item['coil_group_code'];
					$thickness = $item['thickness'];
					$width = $item['width'];
					$product_dtl_id = $item['product_dtl_id'];
					$program_code = $this->pp_model->get_program_code_by_product_id($coil_group_code, $product_dtl_id);
					$slit_date = $item['slit_date'];
					$unit += $item['unit'];
					$weight += $item['weight'];

				}
				
				$external_program_code = $this->program_model->get_external_program_code_by_program_code_and_product_dtl_id($program_code, $product_dtl_id);
				
				$temp = array(
					"coil_group_code" => $coil_group_code,
					"thickness" => $thickness,
					"width" => $width,
					"slit_date" => $slit_date,
					"program_code" => $program_code,
					"unit" => $unit,
					"weight" => $weight,
					"external_program_code" => $external_program_code,
					"product_dtl_id" => $product_dtl_id
				);
				
				if ($is_filter)
				{
					if ("external_program_code" == $searchType)
					{
						if ($external_program_code !== $searchText)
						{
							continue;
						}
					}
					else if ("product_name" == $searchType)
					{
						$product = $product_result[$product_dtl_id];
						if (strpos($product, $searchText) == false)
						{
							continue;
						}
					}
					
				}
				
				$film_result[] = $temp;
					
			}
			$endtime = time();
			echo "Get Film Information and populate : " . ($endtime - $starttime) . "<br/>";
		}
		
		if ($film_result === FALSE) $film_result = array();
		
		$startime = time();
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		$sort_list = array();
		if (!empty($sort_column))
		{
			foreach($film_result as $item)
			{
				$sort_list[$item[$sort_column]][] = $item;
			}
			
			if ($sort_by == "asc")
			{
				ksort($sort_list);
			}
			else
			{
				krsort($sort_list);
			}
			$data["sort_column"] = $sort_column;
			$data["sort_by"] = ("asc" == $sort_by) ? "desc" : "asc";
		}
		else
		{
			foreach($film_result as $item)
			{
				$sort_list[$item["coil_group_code"]][] = $item;
			}
			ksort($sort_list);
			$data["sort_column"] = "coil_group_code";
			$data["sort_by"] = "desc";
		}
		$endtime = time();
		echo "Sort Film Information : " . ($endtime - $starttime) . "<br/>";
		$data["film_result"] = $sort_list;
		
		
		// Navigation
		$selected = "ข้อมูลเก่าของ Film Summary";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			"Film Summary" => "/film",
			$selected => "/film/history",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'film_history_view', $data);
		$this->template->render();
	}
	
	
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */