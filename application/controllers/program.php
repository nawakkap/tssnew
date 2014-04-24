<?php

class Program extends CI_Controller {

	private static $PERMISSION = "PROGRAM";

	function __construct()
	{
		parent::__construct();
		
		check_permission(self::$PERMISSION);

		$this->load->model("product_produce_model", "pp_model");
		$this->load->model("coil_film_product_model", "cfp_model");
		$this->load->model("film_model");
		$this->load->model("order_model");
		$this->load->model("product_model");
		$this->load->model("program_model");
		$this->load->model("machine_model");
		$this->load->model("machine_work_hour_model", "mwh");
	}
	
	function index()
	{
		redirect('/now_page');
	}
	
	function now_page()
	{
	
		$searchType = $this->input->post("searchType");
		$searchText = $this->input->post("searchText");
		if (!isset($searchType)) $searchType = "";
		if (!isset($searchText)) $searchText = "";
		
		//$sort = $this->input->post("sort_column");
		//$sort_by= $this->input->post("sort_by");
		//if (!isset($sort) || empty($sort)) $sort = "p_program.product_dlt_id";
		//if (!isset($sort_by) || empty($sort_by)) $sort_by = "asc";
		$data["sort_column"] = "";
		$data["sort_by"] = "";
		
		$result = $this->program_model->get_current($searchType, $searchText);
		//print_r($result);
		
		if ($result === FALSE)
		{
			$result = array();
		}
		
		$data["program_result"] = $result;
		
		//print_r($result);
		
		$program_status_data = array(
			"1" => "ปกติ",
			"2" => "เสร็จสิ้น",
			"3" => "ยกเลิก"
		);
		
		$data["program_status_result"] = $program_status_data;
		
		$machine_temp = $this->machine_model->get_all();
		$machine = array();
		$machine[""] = ""; // For Init Data
		foreach($machine_temp as $item) 
		{
			$machine[$item["mc_id"]] = $item["machine_name"];
		}
		$data["machine"] = $machine;
	
		/*
		$searchType = $this->input->post("searchType");
		$searchText = $this->input->post("searchText");
		if (!isset($searchType)) $searchType = "";
		if (!isset($searchText)) $searchText = "";
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " is in now program page");
		user_log_message("INFO",  $username . " search with searchText = " . $searchText . " , searchType = " . $searchType);
		
		if ("thickness" == $searchType || "product_name" == $searchType || empty($searchText))
		{
			$program_result = $this->pp_model->get_all();
		}
		else
		{
			$searchText = $this->program_model->get_program_code_by_external_program_code($searchText);
			
			$searchTypeTemp = "prd_product_produce." . $searchType;
			$program_result = $this->pp_model->search($searchTypeTemp, $searchText, FALSE);
		}
		
		if ($program_result === FALSE)
		{
			$program_result = array();
		}
		
		$result = array();
		foreach($program_result as $item)
		{
			//print_r($item);
			if (isset($result[$item['program_code']]))
			{
				$temp = $result[$item['program_code']];
				$temp['weight'] += $item['weight'];
				$result[$item['program_code']] = $temp;
			} 
			else 
			{
				$result[$item['program_code']] = $item;
			}
		}		
		
		$product_result_temp = $this->product_model->get_all();
		if ($product_result_temp === FALSE) $product_result = array();
		$product_result = array();
		foreach($product_result_temp as $item)
		{
			$product_result[$item->product_dtl_id] = $item->product_name_th;
		}
		$data["product_result"] = $product_result;
		
		$program_result = array();
		
		//print_r($result);
		
		foreach($result as $item)
		{
			//print_r($item);
			$program_code = $item['program_code'];
			$product_dtl_id = $item['product_dtl_id'];
			
			//echo "product id : " . $product_dtl_id . "<br/>";
			
			//print_r($product_result);
			if (("product_name" == $searchType) && !empty($searchText))
			{
				if (strpos($product_result[$product_dtl_id], $searchText) === false)
				{
					continue;
				}
			}
			
			$weight = $item['weight'];
			$total_unit  = $this->program_model->get_sum_line_by_program_code_and_product_dtl_id($item['program_code'], $item['product_dtl_id']);
			$avg_weight = $this->program_model->call_function_avg_weight($item['program_code']);
			$external_program_code = $this->program_model->get_external_program_code_by_program_code_and_product_dtl_id($item['program_code'], $item['product_dtl_id']);
			$grade_b = $this->program_model->call_function_grade_b($item['program_code']);
			$coil_group_code = $this->pp_model->get_coil_group_code_by_program_code_and_product_id($item['program_code'], $item['product_dtl_id']);
			
			if ($total_unit < 0) $total_unit = 0;
			
			if (isset($program_result[$product_dtl_id]))
			{
				$temp = $program_result[$product_dtl_id];
				$temp[] = array(
					"program_code" => $program_code,
					"product_dtl_id" => $product_dtl_id,
					"external_program_code" => $external_program_code,
					"grade_b" => $grade_b,
					"total_unit" => $total_unit,
					"weight" => $weight,
					"avg_weight" => $avg_weight,
					"coil_group_code" => $coil_group_code
				);
				
				//print_r($temp);
				
				$program_result[$product_dtl_id] = $temp;
			}
			else
			{
				$program_result[$product_dtl_id][] = array(
					"program_code" => $program_code,
					"product_dtl_id" => $product_dtl_id,
					"external_program_code" => $external_program_code,
					"grade_b" => $grade_b,
					"total_unit" => $total_unit,
					"weight" => $weight,
					"avg_weight" => $avg_weight,
					"coil_group_code" => $coil_group_code
				);
			}
		}
		
		ksort($program_result);
		
		
		//print_r($program_result);
		
		// Get Film remaining
		$film_remaining = array();
		foreach($program_result as $product_dtl_id => $program_item)
		{
			for($i = 0; $i < count($program_item); $i++)
			{ 
				$program_code = $program_item[$i]["program_code"];
				$film_weight = 0;
				$film_unit = 0;
				$est_weight = $this->product_model->get_est_weight_product_id($product_dtl_id);
				foreach($program_item[$i]["coil_group_code"] as $code)
				{
					$film_result = $this->film_model->get_all_by_group_code_and_product_id($code, $product_dtl_id);
					
					foreach($film_result as $film_item)
					{
						$film_weight += round($film_item['weight'], 2);
					}
					$total_unit = $this->program_model->get_sum_line_by_program_code_and_product_dtl_id($program_code, $product_dtl_id);
				}
				
				$film_remaining_temp = $film_weight - ($total_unit * $est_weight);
				$film_remaining[$product_dtl_id] = $film_remaining_temp;
				$program_item[$i]["film_remaining"] = $film_remaining_temp;
			}
			
			$program_result[$product_dtl_id] = $program_item;
		}
		$data["film_remaining"] = $film_remaining;
		//print_r($program_result);
		$data['program_result'] = $program_result;
		*/

		// Navigation
		$selected = "โปรแกรมการผลิต";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			$selected => "/program/now_page",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'program_now_view', $data);
		$this->template->render();
	}
	
	function history()
	{
		$searchType = $this->input->post("searchType");
		$searchText = $this->input->post("searchText");
		$startDate = $this->input->post("startDate");
		$endDate = $this->input->post("endDate");
		if (!isset($searchType)) $searchType = "";
		if (!isset($searchText)) $searchText = "";
		
		// If cannot set startDate and endDate , set them one month
		if ($startDate === FALSE AND $endDate === FALSE)
		{
			$endDate = date('Y-m-d', strtotime("now"));
			$startDate = date('Y-m-d', strtotime("-30 day"));
		}
		
		$data["startDate"] = $startDate;
		$data["endDate"] = $endDate;
		$data["searchType"] = $searchType;
		$data["searchText"] = $searchText;
		
		$sort = $this->input->post("sort_column");
		$sort_by= $this->input->post("sort_by");
		if ($sort === FALSE) $sort = array("p_program.closed_date", "p_program.processing_date");
		if ($sort_by === FALSE) $sort_by = array("DESC", "DESC");
		$data["sort_column"] = $sort;
		$data["sort_by"] = $sort_by;
		
		// var_dump($sort);
		$result = $this->program_model->get_history($searchType, $searchText, $sort, $sort_by, $startDate, $endDate);

		if ($result === FALSE)
		{
			$result = array();
		}
		
		$data["program_result"] = $result;
		
		$program_status_data = array(
			"1" => "ปกติ",
			"2" => "เสร็จสิ้น",
			"3" => "ยกเลิก"
		);
		
		$data["program_status_result"] = $program_status_data;
	
	
		/*
		$searchType = $this->input->post("searchType");
		$searchText = $this->input->post("searchText");
		if (!isset($searchType)) $searchType = "";
		if (!isset($searchText)) $searchText = "";
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " is in historical program page");
		user_log_message("INFO",  $username . " search with searchText = " . $searchText . " , searchType = " . $searchType);
		
		$program_result = FALSE;
		if ("thickness" == $searchType || "product_name" == $searchType || empty($searchText))
		{
			$program_result = $this->pp_model->get_history();
		}
		else
		{
			$searchText = $this->program_model->get_program_code_by_external_program_code($searchText);
			if ($searchText !== FALSE)
			{
				$searchTypeTemp = "prd_product_produce." . $searchType;
				$program_result = $this->pp_model->search($searchTypeTemp, $searchText, TRUE);
			}
		}
		
		
		if ($program_result === FALSE)
		{
			$program_result = array();
		}
		
		//print_r($program_result);
		
		$result = array();
		foreach($program_result as $item)
		{
			if (isset($result[$item['program_code']]))
			{
				$temp = $result[$item['program_code']];
				$temp['weight'] += $item['weight'];
				$result[$item['program_code']] = $temp;
			} 
			else 
			{
				$result[$item['program_code']] = $item;
			}
		}
		
		//print_r($result);
		
		$product_result_temp = $this->product_model->get_all();
		if ($product_result_temp === FALSE) $product_result = array();
		$product_result = array();
		foreach($product_result_temp as $item)
		{
			$product_result[$item->product_dtl_id] = $item->product_name_th;
		}
		
		ksort($product_result);
		
		$data["product_result"] = $product_result;
		
		$program_result = array();
		
		foreach($result as $item)
		{
			$program_code = $item['program_code'];
			$product_dtl_id = $item['product_dtl_id'];
			$weight = $item['weight'];
			$total_unit  = $this->program_model->get_sum_line_by_program_code_and_product_dtl_id($item['program_code'], $item['product_dtl_id']);
			$avg_weight = $this->program_model->call_function_avg_weight($item['program_code']);
			$external_program_code = $this->program_model->get_external_program_code_by_program_code_and_product_dtl_id($item['program_code'], $item['product_dtl_id']);
			$grade_b = $this->program_model->call_function_grade_b($item['program_code']);
			$program_status = $item["program_status"];
			$film_id = $this->cfp_model->get_film_id_by($item['coil_group_code'], $item['slit_spec_id'], $item['slit_sub_no']);
			$populate_date = "";
			if ($film_id !== FALSE)
			{
				$temp_populate_date = $this->film_model->get_populate_date($film_id);
				if ($temp_populate_date !== FALSE)
				{
					$populate_date = mysqldatetime_to_date($temp_populate_date, 'd/m/Y');
				}
			}
			
			if (("product_name" == $searchType) && !empty($searchText))
			{
				if (isset($product_result[$product_dtl_id]) && (strpos($product_result[$product_dtl_id], $searchText) === false))
				{
					continue;
				}
			}
			
			if ($total_unit < 0) $total_unit = 0;
			
			if (isset($program_result[$product_dtl_id]))
			{
				$temp = $program_result[$product_dtl_id];
				$temp[] = array(
					"program_code" => $program_code,
					"product_dtl_id" => $product_dtl_id,
					"external_program_code" => $external_program_code,
					"grade_b" => $grade_b,
					"total_unit" => $total_unit,
					"weight" => $weight,
					"avg_weight" => $avg_weight,
					"populate_date" => $populate_date,
					"program_status" => $program_status
				);
				
				//print_r($temp);
				
				$program_result[$product_dtl_id] = $temp;
			}
			else
			{
				$program_result[$product_dtl_id][] = array(
					"program_code" => $program_code,
					"product_dtl_id" => $product_dtl_id,
					"external_program_code" => $external_program_code,
					"grade_b" => $grade_b,
					"total_unit" => $total_unit,
					"weight" => $weight,
					"avg_weight" => $avg_weight,
					"populate_date" => $populate_date,
					"program_status" => $program_status
				);
			}
		}
		
		//print_r($program_result);
		
		$program_status_data = array(
					"1" => "ปกติ",
					"2" => "เสร็จสิ้น",
					"3" => "ยกเลิก"
		);
		
		$data["program_status_result"] = $program_status_data;
		
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		$sort_list = array();
		if (!empty($sort_column))
		{
			foreach($program_result as $program_item)
			{
				foreach($program_item as $item)
				{
					$sort_list[$item[$sort_column]][] = $item;
				}
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
			foreach($program_result as $program_item)
			{
				foreach($program_item as $item)
				{
					$sort_list[$item["populate_date"]][] = $item;
				}
			}
			krsort($sort_list);
			$data["sort_column"] = "populate_date";
			$data["sort_by"] = "asc";
		}
		
		//print_r($program_result);
		//ksort($program_result);
		$data["program_result"] = $sort_list;
		
		//print_r($program_complete_result);
		
		//$data['program_complete_result'] = $program_complete_result;
		
		//$data['program_cancel_result'] = $program_cancel_result;
		
		if ($this->agent->is_referral())
		{
			$temp = $this->agent->referrer();
			if ($temp == current_url())
			{
				$data["backPage"] = "/program/now_page";
			}
			else
			{
				$data["backPage"]  = substr($temp, strpos($temp, "index.php") + strlen("index.php"));
			}
		}
		else
		{
			$data["backPage"] = "/program/now_page";
		}
		*/
		
		// Navigation
		$selected = "ข้อมูลเก่าของโปรแกรมการผลิต";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			"โปรแกรมการผลิต" => "/program/now_page",
			$selected => "/program/history",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);

		$this->template->write_view('content', 'program_history_view', $data);
		$this->template->render();
	}

	function program_detail($program_code, $product_dtl_id, $history = 0)
	{
		$program_code= $this->convert->HexToAscii($program_code);
		$product_dtl_id = $this->convert->HexToAscii($product_dtl_id);
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " is in program detail page with product id = " . $product_dtl_id . ", program_code = " . $program_code);

		$data['program_code'] = $program_code;
		$data['product_dtl_id'] = $product_dtl_id;
		
		$coil_group_code = $this->pp_model->get_coil_group_code_by_program_code_and_product_id($program_code, $product_dtl_id);
		//$data['coil_group_code'] = $coil_group_code;
		
		$film_result = array();
		if ($coil_group_code !== FALSE)
		{
			$totalfilmweight = 0;
			$totalfilmunit = 0;
			foreach($coil_group_code as $code)
			{
				$film_temp_result = $this->film_model->get_all_by_group_code_and_product_id($code, $product_dtl_id, $program_code);
				if ($film_temp_result !== FALSE)
				{
					$result = array();
					foreach($film_temp_result as $item)
					{
						if (isset($result[$coil_group_code . "$" . $item['thickness'] . "$" . $item['width'] . "$" . $product_dtl_id])) 
						{
							$temp = $result[$coil_group_code . "$" . $item['thickness'] . "$" . $item['width'] . "$" . $product_dtl_id];
							$temp[] = $item;
						}
						else
						{
							$temp = array();
							$temp[] = $item;
						}
						
						$result[$coil_group_code . "$" . $item['thickness'] . "$" . $item['width'] . "$" . $product_dtl_id] = $temp;
					}
					
					
					
					// Summary
					foreach($result as $items)
					{		
						$thickness = "";
						$width = "";
						$unit = 0;
						$weight = 0;
						$price = 0;
						
						$order = null;
						
						foreach($items as $item)
						{
							$thickness = $item['thickness'];
							$width = $item['width'];
							$unit += $item['unit'];
							$totalfilmunit += $item['unit'];
							$weight += $item['weight'];
							$totalfilmweight += $item['weight'];
							$order = $this->order_model->get_by_id($item['po_id']);
						}
						
						$price = $order['price'];
						$base_price = $order['price_base'];
						$vat = $this->pp_model->get_vat_by_coil_group_code($code, $program_code);
						$wage = $this->pp_model->get_wage_by_coil_group_code_and_product_id($code, $product_dtl_id);
						$net_price = $price;
						
						if ($vat === FALSE) // Include Vat.
						{
							$net_price *= 1.04;
						}
						$net_price += $wage;
						
						$temp = array(
							"coil_group_code" => $code,
							"thickness" => $thickness,
							"width" => $width,
							"price" => $price,
							"net_price" => $net_price,
							"base_price" => $base_price,
							"unit" => $unit,
							"weight" => $weight,
							"product_dtl_id" => $product_dtl_id,
							"wage" => $wage,
							"vat_status" => $vat
						);
						
						$film_result[] = $temp;
					}
				}
			}
		}
		if ($film_result === FALSE) $film_result = array();
		
		$data['film_result'] = $film_result;
		$data['totalfilmweight'] = $totalfilmweight;
		$data['totalfilmunit'] = $totalfilmunit;
		
		// TODO Remove this because new version is not use this field in prd_product_produce
		$machine_by = $this->pp_model->get_machine_by_by_program_code($program_code);
		if ($machine_by === FALSE) 
		{
			$machine_by = "";
		}
		$data["machine_by"] = $machine_by;
		
		$mc_id = $this->pp_model->get_machine_default_by_program_code($program_code);
		if ($mc_id === FALSE)
		{
			$mc_id = "";
		}
		$data["machine_default"] = $mc_id;
		
		$program_code_result  = $this->program_model->get_all_by_program_code_and_product_dtl_id($program_code, $product_dtl_id);
		if ($program_code_result == FALSE) $program_code_result = array();
		
		$total_unit_2 = 0;
		$total_BCD_2 = 0;
		$total_A2_2 = 0;
		foreach($program_code_result as $item) { 
			if ($item["total_unit"] < 0) continue;
		
			$total_unit_2 += $item['total_unit'];
			if($item["product_grade"]!="A"&&$item["product_grade"]!="A*") $total_BCD_2 += $item['total_unit'];
			if($item["product_grade"]=="A*") $total_A2_2 += $item['total_unit'];
		}
		
		$data["total_unit_2"] = $total_unit_2;
		$data["total_BCD_2"] = $total_BCD_2;
		$data["total_A2_2"] = $total_A2_2;
		
		$program_status = $this->program_model->get_program_status_by_program_code_and_product_dtl_id($program_code, $product_dtl_id);
		if ($program_status === FALSE) 
		{
			$program_status = 0;
		}
		$data["program_status"] = $program_status;
		
		$machine_list = $this->machine_model->get_all();
		$machine = array();
		$machine[] = "";
		for($i = 0; $i < count($machine_list); $i++)
		{
			$machine[$machine_list[$i]["mc_id"]] = $machine_list[$i]["machine_name"];
		}
		$data["machine"] = $machine;

		$external_program_code = $this->program_model->get_external_program_code_by_program_code_and_product_dtl_id($program_code, $product_dtl_id);
		if ($external_program_code === FALSE || empty($external_program_code) || $external_program_code == NULL)
		{
			$external_program_code = "XXXX";
		}
		$data["external_program_code"] = $external_program_code;
		
		$avg_weight = $this->program_model->call_function_avg_weight($program_code);
		$data["avg_weight"] = $avg_weight;
		
		$grade_b = $this->program_model->call_function_grade_b($program_code);
		$data["grade_b"] = $grade_b;
		
		$grade_b_cfu = $this->program_model->call_function_grade_b_cfu($program_code);
		$data["grade_b_cfu"] = $grade_b_cfu;
		
		$cost = $this->program_model->call_function_cost($program_code, $product_dtl_id);
		$data["cost"] = $cost;
		
		$cost_and_wage = $this->program_model->call_function_cost_and_wage($program_code, $product_dtl_id);
		$data["cost_and_wage"] = $cost_and_wage;
		
		$old_cost = $this->program_model->call_function_cost_old($program_code, $product_dtl_id);
		if ($cost == 0) {
			$data["diff"] = 0;
		} else { 
			$data["diff"] = number_format((($cost - $old_cost) / $cost), 4);
		}
		
		/*
		if ($program_status != "2")
		{
			$data["cost"] = "";
			$data["cost_and_wage"] = "";
		}
		*/
		
		
		$data['program_code_result'] = $program_code_result;
		
		$est_weight = $this->product_model->get_est_weight_product_id($product_dtl_id);
		$data["est_weight"] = $est_weight;
		
		$data["history"] = $history;
		
		$product_name = $this->product_model->get_by_id($product_dtl_id);
		if ($product_name === FALSE) 
		{
			$data["product_name"] = "";
		}
		else
		{
			$data["product_name"] = $product_name["product_name_th"];
		}
		
		// Get All Product
		$product_all = $this->product_model->get_all();
		$data["product_all"] = $product_all;
		
		
		// Navigation
		$selected = "รายละเอียดโปรแกรมการผลิต";

		$navigator = array(
			"หน้าแรก" => "/main",
			"โปรแกรมการผลิต" => "/program/now_page"
		);
		
		if ($history) {
			$navigator["ข้อมูลเก่าโปรแกรมการผลิต"] = "/program/history";
		}
		$navigator[$selected] = "";
		
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'program_detail_view', $data);
		$this->template->render();
	}
	
	function change_machine_alot()
	{
		$program_code = $this->input->post("program_code");
		$product_dtl_id = $this->input->post("product_dtl_id");
		$machine = $this->input->post("machine"); // Machine Id

		$this->db->trans_begin();
		
		for($i = 0; $i < count($program_code); $i++)
		{
			$username = $this->session->userdata("USERNAME");
			user_log_message("INFO",  $username . " want to change machine name of program code = " . $program_code[$i] . ", product_dtl_id = " . $product_dtl_id[$i]);
			
			$change_date = date_to_mysqldatetime();
			
			if ($machine[$i] !== "")
			{
				settype($machine[$i], "integer");
			}
			
			$this->pp_model->update_machine($program_code[$i], $product_dtl_id[$i], $machine[$i]);
		
			// Set to Machine_by
			$machine_item = $this->machine_model->get_by_id($machine[$i]);
			
			$machine_by = "";
			if ($machine_item) {
				$machine_by = $machine_item["machine_name"];
			}
			
			$this->pp_model->update_machine_by_by_program_code($program_code[$i], $machine_by);
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			
			user_log_message("INFO",  $data["result"]);
			
			$data['back_page'] = "/program/now_page";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
			return;
		}
		else
		{
			$this->db->trans_commit();
			
			user_log_message("INFO",  $username . " change the machine to " . $machine . " completed.");
			
			redirect("program/now_page");
			return;
		}
	}
	
	function change_machine()
	{
		$program_code = $this->input->post("program_code");
		$product_dtl_id = $this->input->post("product_dtl_id");
		$machine = $this->input->post("machine"); // Machine Id
		$history = $this->input->post("history");
		
		if ($history === FALSE) {
			$history = 0;
		}
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " want to change machine name of program code = " . $program_code . ", product_dtl_id = " . $product_dtl_id);
		
		$change_date = date_to_mysqldatetime();
		
		$this->db->trans_begin();

		if ($machine !== "")
		{
			settype($machine, "integer");
		}
		$this->pp_model->update_machine($program_code, $product_dtl_id, $machine);
		
		// Set to Machine_by
		$machine_item = $this->machine_model->get_by_id($machine);
		
		$machine_by = "";
		if ($machine_item) {
			$machine_by = $machine_item["machine_name"];
		}
		
		
		$this->pp_model->update_machine_by_by_program_code($program_code, $machine_by);
		
		$program_code = $this->convert->AsciiToHex($program_code);
		$product_dtl_id = $this->convert->AsciiToHex($product_dtl_id);
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			
			user_log_message("INFO",  $data["result"]);
			
			$data['back_page'] = "/program/program_detail/" . $program_code . "/" . $product_dtl_id . "/" . $history;
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			
			user_log_message("INFO",  $username . " change the machine to " . $machine . " completed.");
			
			$data['result'] = "เปลี่ยนแปลงเครื่องเรียบร้อยแล้ว";
			$data['back_page'] = "/program/program_detail/" . $program_code . "/" . $product_dtl_id . "/" . $history;
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	function change_machine_internal()
	{
	}
	
	function update_program_status()
	{
		$program_code = $this->input->post("program_code");
		$product_dtl_id = $this->input->post("product_dtl_id");
		$program_status = $this->input->post("program_status");
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " want to update program status of program code = " . $program_code . ", product_dtl_id = " . $product_dtl_id . ", program_status = " . $program_status);

		$populate_flag = "N";
		if ($program_status !== "1")
		{
			$populate_flag = "Y";
		}
		
		$populate_date = date_to_mysqldatetime();
		
		$this->db->trans_begin();
		// Check Program row
		
		$this->program_model->update_program_status($program_code, $product_dtl_id, $program_status);

		$coil_group_code = $this->pp_model->get_all_by_program_code_and_product_id($program_code, $product_dtl_id);
		if ($coil_group_code === FALSE)
		{
			$coil_group_code = array();
		}
		
		//print_r($coil_group_code);

		foreach($coil_group_code as $item)
		{
			$lot_no = $item["coil_group_code"];
			$slit_spec_id = $item["slit_spec_id"];
			$slit_sub_no = $item["slit_sub_no"];
			
			//echo $lot_no . "<br/>";
			
			$film_result = $this->cfp_model->get_all_by_group_code_and_slit_spec_id_and_slit_sub_no($lot_no, $slit_spec_id, $slit_sub_no);
			if ($film_result === FALSE) 
			{
				$film_result = array();
			}
			
			foreach($film_result as $film)
			{
				$this->film_model->update_film_status($film, $populate_flag, $populate_date);
			}
			
			
			
			/*
			Film Detail Status
			"1" => "ยังไม่ได้ผลิต",
			"2" => "ยืดแล้ว",
			"3" => "ขึ้นรูป finishgoods แล้ว",
			"4" => "ปิดล๊อต"
			*/
			
			/*
			Program Status
			"1" => "ปกติ",
			"2" => "เสร็จสิ้น",
			"3" => "ยกเลิก"
			*/
		
			
			$status = FALSE;
			if ($program_status == "1")
			{
			}
			else if ($program_status == "2")
			{
				$status = 4;
			}
			else if ($program_status == "3")
			{
				$status = 4;
			}
			else if ($program_status == "4")
			{
			}
			
			if ($status != FALSE)
			{
			
				$update_data = array(
					"status" => $status
				);
				
				// Update Film Detail
				$where = array(
					"film_id" => $film
				);
				
				$this->db->update("prd_film_information_detail", $update_data, $where);
			}
		}
		
		$history = 0;
		if ($program_status != "1")
		{
			$history = 1;
		}
		
		
		
		$program_code = $this->convert->AsciiToHex($program_code);
		$product_dtl_id = $this->convert->AsciiToHex($product_dtl_id);
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			
			user_log_message("INFO",  $data["result"]);
			
			$data['back_page'] = "/program/program_detail/" . $program_code . "/" . $product_dtl_id . "/" . $history;
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			
			user_log_message("INFO",  $username . " change the program status to " . $program_status . " completed.");
			
			$data['result'] = "เปลี่ยนแปลงสถานะเรียบร้อยแล้ว";
			$data['back_page'] = "/program/program_detail/" . $program_code . "/" . $product_dtl_id . "/" . $history;
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	function update_program_ext()
	{
		$program_ext = $this->input->post("external_program_code");
		$program_ext = trim($program_ext);
		
		$program_code = $this->input->post("program_code");
		$product_dtl_id = $this->input->post("product_dtl_id");
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " want to update external program code of program code = " . $program_code . ", product_dtl_id = " . $product_dtl_id . ", external program code = " . $program_ext);
		
		$history = 0;
		

		$this->db->trans_begin();
		
		$this->program_model->update_program_ext_code($program_code, $product_dtl_id, $program_ext);
		
		$program_code = $this->convert->AsciiToHex($program_code);
		$product_dtl_id = $this->convert->AsciiToHex($product_dtl_id);
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			
			user_log_message("INFO",  $data["result"]);
			
			$data['back_page'] = "/program/program_detail/" . $program_code . "/" . $product_dtl_id . "/" . $history;
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			
			user_log_message("INFO",  $username . " update the external program code to " . $program_ext . " completed.");
			
			$data['result'] = "เปลี่ยนแปลง external program code เรียบร้อยแล้ว";
			$data['back_page'] = "/program/program_detail/" . $program_code . "/" . $product_dtl_id . "/" . $history;
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		
	}
	
	function update_lot()
	{
		$program_code = $this->input->post("program_code");
		$product_dtl_id = $this->input->post("product_dtl_id");
		$coil_group_code = $this->input->post("coil_group_code");
		$history = $this->input->post("history");
		$vat_status = $this->input->post("vat_status");
		$wage = $this->input->post("wage");
		
		$vat_price = 0;
		if ($vat_status === "VAT_NORMAL")
		{
			$vat_price = 1.07;
		}
		else if ($vat_status === "VAT_PREMIUM")
		{
			$vat_price = 1.04;
		}
		
		$this->db->trans_begin();
		$this->pp_model->update_vat_and_wage($coil_group_code, $product_dtl_id, $vat_status, $vat_price, $wage);
		
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			user_log_message("INFO",  "add new program : " . $data["result"]);
			
			$this->db->trans_rollback();
			$data['back_page'] = "/program/now_page";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			$data['result'] = "จัดเก็บข้อมูลเรียบร้อย";
			
			user_log_message("INFO",  "add new program : " . $data["result"]);
			
			redirect("/program/program_detail/" . $this->convert->AsciiToHex($program_code) . "/" . $this->convert->AsciiToHex($product_dtl_id) . "/" . $history);
		}	
	}
	
	function change_product_and_program_code()
	{
		$product_dtl_id = $this->input->post("product_dtl_id");
		$program_code = $this->input->post("program_code");
		$program_ext_no = $this->input->post("program_ext_no");
		
		// Old
		$old_program_code = $this->input->post("old_program_code");
		$old_product_dtl_id = $this->input->post("old_product_dtl_id");
		$old_history = $this->input->post("old_history");
		
		$this->db->trans_begin();
		if ((strlen($product_dtl_id) > 0) AND (strlen($program_code) > 0)) 
		{
			// Check Program code is existed
			if ($this->pp_model->count_by_program_code($program_code) == 0) 
			{
				$data["result"] = "หาเลขโปรแกรมภายใน [" . $program_code ."] ไำม่เจอในระบบ";
			}
			else
			{
				// Success Case
				$isError = FALSE;
				
				$temp_program_code = $this->program_model->get_all_program_code_by_external_program_code($program_ext_no);
				for($i = 0; $i < count($temp_program_code); $i++) 
				{
					$chg_program_code = $temp_program_code[$i];
					
					$list_coil_group_code = $this->pp_model->get_coil_group_code_by_program_code($chg_program_code);
					
					// Update product_dtl_id and program_code to New value
					$result = $this->pp_model->update_product_dtl_id_and_program_code_by_program_code($product_dtl_id, $program_code, $chg_program_code);
					if ($result === FALSE) 
					{
						$data["result"] = "ไม่สามารถเปลียนแปลง Product และ Program Code ได้ (1)";
						$isError = TRUE;
						break;
					}
					
					$result = $this->film_model->change_product_dtl_id($product_dtl_id, $old_product_dtl_id,  $list_coil_group_code);
					if ($result === FALSE) 
					{
						$data["result"] = "ไม่สามารถเปลียนแปลง Product และ Program Code ได้ (2)";
						$isError = TRUE;
						break;
					}
				}
				
				
				if ($this->db->trans_status() === FALSE || $isError === TRUE)
				{
					$this->db->trans_rollback();

					user_log_message("INFO",  "Cannot Change Product : " . $data["result"]);
					$data['back_page'] = "/program/program_detail/" . $this->convert->AsciiToHex($old_program_code) . "/" . $this->convert->AsciiToHex($old_product_dtl_id) . "/" . $old_history;
					$this->template->write_view('content', 'order_result_view', $data);
					$this->template->render();
				}
				else
				{
					$this->db->trans_commit();
					
					$data['result'] = "เปลี่ยนแปลงสินค้าและเลขที่โปรแกรมภายในเรียบร้อย";
			
					user_log_message("INFO",  "add new program : " . $data["result"]);
					$data['back_page'] = "/program/program_detail/" . $this->convert->AsciiToHex($old_program_code) . "/" . $this->convert->AsciiToHex($old_product_dtl_id) . "/" . $old_history;
					$this->template->write_view('content', 'order_result_view', $data);
					$this->template->render();
				}
		
				return;
			}
		}
		else
		{
			$data["result"] = "ข้อมูลที่ใช้ในการเปลี่ยนสินค้าและเลขโปรแกรมการผลิตภายในไม่พอ";
		}
		
		
		$this->db->trans_rollback();
		$data['back_page'] = "/program/program_detail/" . $this->convert->AsciiToHex($old_program_code) . "/" . $this->convert->AsciiToHex($old_product_dtl_id) . "/" . $old_history;
		$this->template->write_view('content', 'order_result_view', $data);
		$this->template->render();
	}
	
	function add_method()
	{
		$program_code = $this->input->post("program_code");
		$program_code_ext = $this->input->post("program_code_ext");
		$product_dtl_id = $this->input->post("product_dtl_id");
		$rowCount = $this->input->post("rowCount");
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call add method with program_code = " . $program_code . ", product id = " . $product_dtl_id);
		
		$this->db->trans_begin();
		$this->program_model->delete_all($program_code, $product_dtl_id);
		
		for($i = 1; $i <= $rowCount; $i++)
		{
			$processing_date = $this->input->post("processing_date" . $i);
			$total_unit = $this->input->post("total_unit" . $i);
			$machine_id = $this->input->post("machine". $i);
			$grade = $this->input->post("grade" . $i);
			
			if (!empty($processing_date) && !empty($total_unit) && !empty($grade))
			{
				$program_status = 1;
				
				$processing_date_temp = date_to_mysqldatetime($processing_date);
				
				//echo $processing_date . "= INSERT<br/>";
				$this->program_model->insert("ADD", $program_code, $product_dtl_id, $total_unit, $grade, $processing_date_temp, $program_status, $program_code_ext, $machine_id);
			}
			
			// Update Work Hour for Machine
			if ($machine_id !== FALSE AND $machine_id != 0 AND $processing_date !== FALSE)
			{
				$work_date = date_to_mysqldatetime($processing_date, FALSE); // Date Only
				
				if (!$this->mwh->check_work_hour($machine_id, $work_date))
				{
					$change_date = date_to_mysqldatetime();
					$this->mwh->insert($machine_id, $work_date, 480, "N", "", $username, $change_date); // Insert Default Value for Work hour of Machine Data
				}
			}
		}
		
		
		
		$coil_group_code = $this->pp_model->get_coil_group_code_by_program_code_and_product_id($program_code, $product_dtl_id);
		foreach($coil_group_code as $item)
		{
			$total_unit = -1;
			$grade = "A";
			$processing_date = date_to_mysqldatetime();
			$program_status = 1;
			$machine_id = "";
	
			$this->program_model->insert("ADD", $program_code, $product_dtl_id, $total_unit, $grade, $processing_date, $program_status, $program_code_ext, $machine_id);
			// sleep(1);
		}
		
		
		$machine_by = $this->input->post("machine_by");
		if (!empty($machine_by))
		{
			$this->pp_model->update_machine_by_by_program_code($program_code, $machine_by);
		}
		
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			user_log_message("INFO",  "add new program : " . $data["result"]);
			
			$this->db->trans_rollback();
			$data['back_page'] = "/program/now_page";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			$data['result'] = "จัดเก็บข้อมูลเรียบร้อย";
			
			user_log_message("INFO",  "add new program : " . $data["result"]);
			
		
			$data['back_page'] = "/program/program_detail/" . $this->convert->AsciiToHex($program_code) . "/" . $this->convert->AsciiToHex($product_dtl_id) . "/0";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */