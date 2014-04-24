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
		$this->load->model("film_history_model", "fh_model");
	}
	
	function main()
	{
		// Navigation
		$selected = "Film Summary Menu";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			$selected => "/film/main",
		);
		
		$data =  array(
			"navigation" => build_navigation($selected, $navigator)
		);
		
		$this->template->write_view('content', 'film_main_menu', $data);
		$this->template->render();
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
		$data["searchType"] = $searchType;
		$data["searchText"] = $searchText;
		
		$theDate = $this->input->post("theDate");
		if ($theDate === FALSE) {
			$theDate = date('Y-m-d', strtotime("-1 day"));
		}
		$data["theDate"] = $theDate;
		
		$is_filter = FALSE;
		
		$film_result = FALSE;
		if (("thickness" == $searchType || "coil_group_code" == $searchType || "external_program_code" == $searchType || "product_name" == $searchType) || empty($searchType) || empty($searchText)) 
		{
			$film_result = $this->film_model->get_all("slit_date", "desc", $theDate);
			if (("thickness" == $searchType || "coil_group_code" == $searchType || "external_program_code" == $searchType || "product_name" == $searchType) && !empty($searchText))
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
			$film_result = $this->film_model->get_all("slit_date", "asc", $theDate);
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
			
			$product_result_temp = $this->product_model->get_all_dont_care();
			$product_result = array();
			if ($product_result_temp !== FALSE) {
				foreach($product_result_temp as $item) {
					$product_result[$item->product_dtl_id] = $item->product_name_th;
				}
			}
			
			//print_r($result);
			
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
				$quantity = 0;
				$expanded = 0;
				$total_quantity = 0;
				$closed = 0;
				$mc_id = 0;
				$ironing_machine = 0;
				$q_index = 0;
				
				foreach($items as $item)
				{
					$coil_group_code = $item['coil_group_code'];
					$thickness = $item['thickness'];
					$width = $item['width'];
					$product_dtl_id = $item['product_dtl_id'];
					$program_code = $item['program_code'];
					$external_program_code = $item['program_code_ext'];
					$slit_date = $item['slit_date'];
					$unit += $item['unit'];
					$weight += $item['weight'];
					$quantity = $item["quantity"];
					
					// Expand
					$this->db->where("coil_group_code", $coil_group_code);
					$this->db->where("product_dtl_id", $product_dtl_id);
					$this->db->where_in("status", array("2")); // "ยืดแล้ว"
					$detail_query = $this->db->get("prd_film_information_detail");
					$expanded = $detail_query->num_rows();
					
					// Get Total Quantity from Film Detail
					$this->db->where("coil_group_code", $coil_group_code);
					$this->db->where("product_dtl_id", $product_dtl_id);
					$this->db->where_in("status", array("3")); // "ขึ้นรูป finishgoods แล้ว"
					$detail_query = $this->db->get("prd_film_information_detail");
					$total_quantity = $detail_query->num_rows();
					
					// Get Total Quantity from Film Detail
					$this->db->where("coil_group_code", $coil_group_code);
					$this->db->where("product_dtl_id", $product_dtl_id);
					$this->db->where_in("status", array("4")); // "ปิดล๊อต
					$detail_query = $this->db->get("prd_film_information_detail");
					$closed = $detail_query->num_rows();
					
					$total_quantity = $total_quantity; // $item["total_quantity"];
					$mc_id = $item["mc_id"];
					$ironing_machine = $item["ironing_machine"];
					$q_index = $item["q_index"];
				}
				
				if ($ironing_machine)
				{
					// $total_quantity = $this->fh_model->get_all_unit_by_date($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);
				
					// Get Total Quantity from Film Detail
					/*
					$this->db->where("coil_group_code", $coil_group_code);
					$this->db->where("product_dtl_id", $product_dtl_id);
					$this->db->where_in("status", array("2")); // "ยืดแล้ว"
					$detail_query = $this->db->get("prd_film_information_detail");
					$expanded = $detail_query->num_rows();
					*/
				}
				
				$temp = array(
					"coil_group_code" => $coil_group_code,
					"thickness" => $thickness,
					"width" => $width,
					"slit_date" => $slit_date,
					"program_code" => $program_code,
					"unit" => $unit,
					"weight" => $weight,
					"external_program_code" => $external_program_code,
					"product_dtl_id" => $product_dtl_id,
					"quantity" => $quantity,
					"expanded" => $expanded,
					"total_quantity" => $total_quantity,
					"closed" => $closed,
					"remaining_quantity" => ($unit - ($total_quantity + $expanded + $closed)),
					"mc_id" => $mc_id,
					"ironing_machine" => $ironing_machine,
					"q_index" => $q_index
				);
				
				
				if ($is_filter)
				{
					//echo "Search Type = " . $searchType . "<br/>";
					//echo "Search Text = " . $searchText . "<br/>";
					
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
					else if ("coil_group_code" == $searchType)
					{
						if ($coil_group_code !== $searchText)
						{
							continue;
						}
					}
					else if ("thickness" == $searchType)
					{
						if (floatval($thickness) !== floatval($searchText))
						{
							continue;
						}
					}
				}

				$film_result[] = $temp;

				if ($ironing_machine) 
				{
					
					// GET DATA FROM DB IF EXIST
					$exist_list = $this->fh_model->get_all_exclude($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);

					// echo count($exist_list);
					if (count($exist_list) == 0)
					{
						$unit_remaining = $this->fh_model->get_quantity_exclude($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);
						if ($unit_remaining > 0)
						{
							// $total_quantity = $this->fh_model->get_all_unit_by_date_exclude($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);
							$mc_id = $this->pp_model->get_machine_default_by_program_code($program_code);
							// echo $program_code . " = " . $mc_id;
							
							// Get Total Quantity from Film Detail
							$this->db->where("coil_group_code", $coil_group_code);
							$this->db->where("product_dtl_id", $product_dtl_id);
							$this->db->where_in("status", array("2")); // "ยืดแล้ว"
							$detail_query = $this->db->get("prd_film_information_detail");
							$expanded = $detail_query->num_rows();
							
							// Get Total Quantity from Film Detail
							$this->db->where("coil_group_code", $coil_group_code);
							$this->db->where("product_dtl_id", $product_dtl_id);
							$this->db->where_in("status", array("3")); // "Finish Good"
							$detail_query = $this->db->get("prd_film_information_detail");
							$total_quantity = $detail_query->num_rows();
							
							// Get Closed  from Film Detail
							$this->db->where("coil_group_code", $coil_group_code);
							$this->db->where("product_dtl_id", $product_dtl_id);
							$this->db->where_in("status", array("4")); // "Closed"
							$detail_query = $this->db->get("prd_film_information_detail");
							$closed = $detail_query->num_rows();
							
							$temp = array(
								"coil_group_code" => $coil_group_code,
								"thickness" => $thickness,
								"width" => $width,
								"slit_date" => $slit_date,
								"program_code" => $program_code,
								"unit" => $unit_remaining,
								"weight" => $weight,
								"external_program_code" => $external_program_code,
								"product_dtl_id" => $product_dtl_id,
								"quantity" => 0,
								"expanded" => $expanded,
								"total_quantity" => $total_quantity,
								"closed" => $closed,
								"remaining_quantity" => ($unit_remaining - ($total_quantity + $expanded + $closed)),
								"mc_id" => $mc_id,
								"ironing_machine" => 0,
								"q_index" => 0
							);
							
							$film_result[] = $temp;
						}
					}
					else
					{
						for($i = 0; $i < count($exist_list); $i++)
						{
							// print_r($exist_list[$i]);
							$unit_remaining = $this->fh_model->get_quantity_exclude($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);
							$quantity = $this->fh_model->get_quantity_index($exist_list[$i]["mc_id"], $coil_group_code, $product_dtl_id, $theDate, $exist_list[$i]["temp"]);
							// $total_quantity = $this->fh_model->get_all_unit_by_date_exclude($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);
							
							
							// Get Total Quantity from Film Detail
							$this->db->where("coil_group_code", $coil_group_code);
							$this->db->where("product_dtl_id", $product_dtl_id);
							$this->db->where_in("status", array("2")); // "ยืดแล้ว"
							$detail_query = $this->db->get("prd_film_information_detail");
							$expanded = $detail_query->num_rows();
							
							// Get Total Quantity from Film Detail
							$this->db->where("coil_group_code", $coil_group_code);
							$this->db->where("product_dtl_id", $product_dtl_id);
							$this->db->where_in("status", array("3")); // "Finish Good"
							$detail_query = $this->db->get("prd_film_information_detail");
							$total_quantity = $detail_query->num_rows();
							
							// Get Closed  from Film Detail
							$this->db->where("coil_group_code", $coil_group_code);
							$this->db->where("product_dtl_id", $product_dtl_id);
							$this->db->where_in("status", array("4")); // "Closed"
							$detail_query = $this->db->get("prd_film_information_detail");
							$closed = $detail_query->num_rows();
							
							if ($unit_remaining > 0) 
							{
								$temp = array(
									"coil_group_code" => $coil_group_code,
									"thickness" => $thickness,
									"width" => $width,
									"slit_date" => $slit_date,
									"program_code" => $program_code,
									"unit" => $unit_remaining,
									"weight" => $weight,
									"external_program_code" => $external_program_code,
									"product_dtl_id" => $product_dtl_id,
									"quantity" => $quantity,
									"expanded" => $expanded,
									"total_quantity" => $total_quantity,
									"closed" => $closed,
									"remaining_quantity" => ($unit_remaining - ($total_quantity + $expanded + $closed)),
									"mc_id" => $exist_list[$i]["mc_id"],
									"ironing_machine" => 0,
									"q_index" => $exist_list[$i]["temp"]
								);
								
								$film_result[] = $temp;
							}
						}
					}
				}
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
		
		$this->load->model("machine_model");
		$machine_temp = $this->machine_model->get_all();
		$machine = array();
		for($i = 0; $i < count($machine_temp); $i++)
		{
			$machine[$machine_temp[$i]["mc_id"]] = $machine_temp[$i]["machine_name"];
		}
		$data["machine"] = $machine;
		
		$machine_special_temp = $this->machine_model->get_special();
		$machine_special = array();
		for($i = 0; $i < count($machine_special_temp); $i++)
		{
			$machine_special[$machine_special_temp[$i]["mc_id"]] = $machine_special_temp[$i]["machine_name"];
		}
		$data["machine_special"] = $machine_special;
		
		// Navigation
		$selected = "Film Summary";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			/* "Menu" => "/film/main", */
			$selected => "/film",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'film_list_view', $data);
		$this->template->render();
	}
	
	public function item_detail_save()
	{
		$coil_group_code = $this->input->post("coil_group_code");
		$product_dtl_id = $this->input->post("product_dtl_id");
		
		$temp = $this->input->post("temp");
		
		if ($temp !== FALSE)
		{
			$this->load->helper("date");
			// print_r($temp);
			for($i = 0; $i < count($temp); $i++)
			{
				$old_film_id = FALSE;
				$old_duration_iron = FALSE;
				$old_start_date_iron = FALSE;
				$old_duration = FALSE;
				$old_start_date = FALSE;
				$old_status = FALSE;
				$old_mc = FALSE;
				$old_iron_mc = FALSE;
				
				$new_film_id = FALSE;
				$new_duration_iron = FALSE;
				$new_start_date_iron = FALSE;
				$new_duration = FALSE;
				$new_start_date = FALSE;
				$new_status = FALSE;
				$new_mc = FALSE;
				$new_iron_mc = FALSE;
				
				// Get Previous Data
				$this->db->where("temp", $temp[$i]);
				$pquery = $this->db->get("prd_film_information_detail");
				
				if ($pquery->num_rows() > 0)
				{
					$pitem = $pquery->row_array();
					
					$old_film_id = $pitem["new_film_id"];
					$old_duration_iron = $pitem["duration_iron"];
					$old_start_date_iron = $pitem["start_date_iron"];
					$old_duration = $pitem["duration"];
					$old_start_date = $pitem["start_date"];
					$old_status = $pitem["status"];
					$old_mc = $pitem["mc"];
					$old_iron_mc = $pitem["iron_mc"];
				}
			
			
				$start_date = $this->input->post("start_date_" . $temp[$i]);
				$duration = $this->input->post("duration_" . $temp[$i]);
				$mc = $this->input->post("mc_" . $temp[$i]);
				
				$start_date_iron = $this->input->post("start_date_iron_" . $temp[$i]);
				$duration_iron = $this->input->post("duration_iron_" . $temp[$i]);
				$iron_mc = $this->input->post("iron_mc_" . $temp[$i]);
				
				$status = $this->input->post("status_" . $temp[$i]);
				
				$update_data = array(
					"duration" => $duration,
					"duration_iron" => $duration_iron,
					"mc" => $mc,
					"iron_mc" => $iron_mc,
					"status" => $status
				);
				
				if ($start_date)
				{
					$update_data["start_date"] = date_to_mysqldatetime($start_date, FALSE);
				}
				else
				{
					$update_data["start_date"] = NULL;
					// $this->db->set("start_date", NULL);
				}
				
				if ($start_date_iron)
				{
					$update_data["start_date_iron"] = date_to_mysqldatetime($start_date_iron, FALSE);
				}
				else
				{
					$update_data["start_date_iron"] = NULL;
				}
				
				$where = array(
					"temp" => $temp[$i]
				);
				
				$this->db->update("prd_film_information_detail", $update_data, $where);
				
				
				// Update if IRON Completed
				if ($status == 2)
				{	
					$this->db->where("temp", $temp[$i]);
					$this->db->set("new_film_id", "CONCAT(SUBSTRING(new_film_id, 1, CHAR_LENGTH(new_film_id) - 1), '" . IRON_YES . "')", FALSE);
					$this->db->update("prd_film_information_detail");
					// echo $this->db->last_query();
					// End Update 1.4
				}
				
				// Get Current Data
				$this->db->where("temp", $temp[$i]);
				$pquery = $this->db->get("prd_film_information_detail");
				
				if ($pquery->num_rows() > 0)
				{
					$pitem = $pquery->row_array();
					
					$new_film_id = $pitem["new_film_id"];
					$new_duration_iron = $pitem["duration_iron"];
					$new_start_date_iron = $pitem["start_date_iron"];
					$new_duration = $pitem["duration"];
					$new_start_date = $pitem["start_date"];
					$new_status = $pitem["status"];
					$new_mc = $pitem["mc"];
					$new_iron_mc = $pitem["iron_mc"];
				}

				if (($new_film_id !== FAlSE && $new_duration_iron !== FAlSE
					&& $new_start_date_iron !== FALSE && $new_duration !== FAlSE
					&& $new_start_date !== FALSE && $new_status !== FALSE
					&& $new_mc !== FALSE && $new_iron_mc !== FALSE
					&& $old_film_id !== FAlSE && $old_duration_iron !== FAlSE
					&& $old_start_date_iron !== FAlSE && $old_duration !== FALSE
					&& $old_start_date !== FALSE && $old_status !== FALSE
					&& $old_mc !== FALSE && $old_iron_mc !== FALSE)
					&& ( ($new_film_id != $old_film_id) 
					|| ($new_duration_iron != $old_duration_iron) 
					|| ($new_start_date_iron != $old_start_date_iron) 
					|| ($new_duration != $old_duration) 
					|| ($new_start_date != $old_start_date) 
					|| ($new_status != $old_status)
					|| ($new_mc != $old_mc)
					|| ($new_iron_mc != $old_iron_mc) ) )
				{
					$update_film_id = ($new_film_id != $old_film_id) ? $new_film_id : "";
					$update_start_date_iron = ($new_start_date_iron != $old_start_date_iron) ? $new_start_date_iron : NULL;
					$update_duration = ($new_duration != $old_duration) ? $new_duration : "";
					$update_duration_iron = ($new_duration_iron != $old_duration_iron) ? $new_duration_iron : "";
					$update_start_date = ($new_start_date != $old_start_date) ? $new_start_date : NULL;
					$update_status = ($new_status != $old_status) ? $new_status : "";
					$update_mc = ($new_mc != $old_mc) ? $new_mc : "";
					$update_iron_mc = ($new_iron_mc != $old_iron_mc) ? $new_iron_mc : "";
				
				
					// Add to Logging
					$insert_data = array(
						"new_film_temp" => $temp[$i],
						/* "old_film_id" => $old_film_id,
						"old_duration_iron" => $old_duration_iron,
						"old_start_date_iron" => $old_start_date_iron,
						"old_duration" => $old_duration,
						"old_start_date" => $old_start_date,
						"old_status" => $old_status, */
						"new_film_id" => $update_film_id,
						"new_duration_iron" => $update_duration_iron,
						"new_start_date_iron" => $update_start_date_iron,
						"new_duration" => $update_duration,
						"new_start_date" => $update_start_date,
						"new_status" => $update_status,
						"new_mc" => $update_mc,
						"new_iron_mc" => $update_iron_mc,
						"update_time" => date("Y-m-d H:i:s")
					);
					
					$this->db->insert("prd_film_information_detail_logging", $insert_data);
					// echo $this->db->last_query();
				}
			}
		}
		
		$coil_group_code = $this->convert->AsciiToHex($coil_group_code);
		$product_dtl_id = $this->convert->AsciiToHex($product_dtl_id);
		
		redirect("film/item_detail/" . $coil_group_code . "/" . $product_dtl_id);
		return;
	}
	
	public function item_detail($coil_group_code = FALSE, $product_dtl_id = FALSE)
	{
		if ($coil_group_code === FALSE || $product_dtl_id == FALSE)
		{
			redirect("film/index");
			return;
		}
		
		$coil_group_code = $this->convert->HexToAscii($coil_group_code);
		$product_dtl_id = $this->convert->HexToAscii($product_dtl_id);
		
		$this->db->where("coil_group_code", $coil_group_code);
		$this->db->where("product_dtl_id", $product_dtl_id);
		$this->db->order_by("update_date");
		$query = $this->db->get("prd_film_information_detail");
		
		$item = array();
		foreach($query->result_array() as $row)
		{
			$item[] = $row;
		}
		
		// Machine
		$iron = array("Y");
		$this->db->where("machine_type", "");
		$query = $this->db->get("mc_machine_info");
		
		$machine = array();
		foreach($query->result_array() as $row)
		{
			$machine[] = $row;
		}
		
		// Iron Machine
		$this->db->where_in("machine_type", $iron);
		$query = $this->db->get("mc_machine_info");
		
		$iron_machine = array();
		foreach($query->result_array() as $row)
		{
			$iron_machine[] = $row;
		}
		
		
		
		$selected = "Film Detail Summary";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Film" => "/film",
			$selected => "/film/item" . $this->convert->AsciiToHex($coil_group_code) . "/" . $this->convert->AsciiToHex($product_dtl_id),
		);
		
		
		$data = array(
			"item" => $item,
			"coil_group_code" => $coil_group_code,
			"product_dtl_id" => $product_dtl_id,
			"machine" => $machine,
			"iron_machine" => $iron_machine,
			"navigation" => build_navigation($selected, $navigator)
		);
		
		$this->template->write_view('content', 'film_detail_list_view', $data);
		$this->template->render();
	}
	
	public function edit_film()
	{
		$date = $this->input->post("date");
		$product_dtl_id = $this->input->post("product_dtl_id");
		$thickness = $this->input->post("thickness");
		$lot_no = $this->input->post("lot_no");
		
		$selected = "แก้ไข";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			/* "Film Summary Menu" => "/film/main", */
			$selected => "",
		);
		
		$this->load->model("film_model");
		
		$products = $this->film_model->get_product_in_film();
		$data["products"] = $products;
		$data["slit_date"] = ($date) ? $date : date("d/m/Y");
		$data["product_dtl_id"] = $product_dtl_id;
		$data["thickness"] = $thickness;
		$data["lot_no"] = $lot_no;
		
		$this->load->model("config_model");
		
		$thickness_query = $this->config_model->get_thickness();
		
		$thickness_result = array();
		$thickness_result[""] = "";
		foreach($thickness_query as $value) {
			$thickness_result[$value] = $value;
		}
		
		$data['thickness_result'] = $thickness_result;
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'film_edit_view', $data);
		$this->template->render();
	}
	
	function edit_search()
	{
		$product_dtl_id = $this->input->post("product_dtl_id");
		$lot_no = $this->input->post("lot_no");
		$film_date = $this->input->post("film_date");
		$thickness = $this->input->post("thickness");
		
		$this->load->model("film_model");
		$film_result = $this->film_model->edit_search($lot_no, $film_date, $thickness, $product_dtl_id);

		// Navigation
		$selected = "Film Summary";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			/* "Menu" => "/film/main", */
			$selected => "/film",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		$data["theDate"] = $film_date;
		// $data["slit_date"] = ($date) ? $date : date("d/m/Y");
		$data["product_dtl_id"] = $product_dtl_id;
		$data["thickness"] = $thickness;
		$data["lot_no"] = $lot_no;
		
		$this->load->helper("date_helper");
		$theDate = date_to_mysqldatetime($film_date, FALSE);
		
		// $film_result = FALSE;
		/*
		if (("thickness" == $searchType || "coil_group_code" == $searchType || "external_program_code" == $searchType || "product_name" == $searchType) || empty($searchType) || empty($searchText)) 
		{
			$film_result = $this->film_model->get_all("slit_date", "desc", $theDate);
			if (("thickness" == $searchType || "coil_group_code" == $searchType || "external_program_code" == $searchType || "product_name" == $searchType) && !empty($searchText))
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
			$film_result = $this->film_model->get_all("slit_date", "asc", $theDate);
			$is_filter = FALSE;
		}
		*/
		
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
			
			$product_result_temp = $this->product_model->get_all_dont_care();
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
				$quantity = 0;
				$total_quantity = 0;
				$mc_id = 0;
				$ironing_machine = 0;
				$q_index = 0;
				
				foreach($items as $item)
				{
					$coil_group_code = $item['coil_group_code'];
					$thickness = $item['thickness'];
					$width = $item['width'];
					$product_dtl_id = $item['product_dtl_id'];
					$program_code = $item['program_code'];
					$external_program_code = $item['program_code_ext'];
					$slit_date = $item['slit_date'];
					$unit += $item['unit'];
					$weight += $item['weight'];
					$quantity = $item["quantity"];
					$total_quantity = $item["total_quantity"];
					$mc_id = $item["mc_id"];
					$ironing_machine = $item["ironing_machine"];
					$q_index = $item["q_index"];
				}
				
				if ($ironing_machine)
				{
					$total_quantity = $this->fh_model->get_all_unit_by_date($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);
				}
				
				$temp = array(
					"coil_group_code" => $coil_group_code,
					"thickness" => $thickness,
					"width" => $width,
					"slit_date" => $slit_date,
					"program_code" => $program_code,
					"unit" => $unit,
					"weight" => $weight,
					"external_program_code" => $external_program_code,
					"product_dtl_id" => $product_dtl_id,
					"quantity" => $quantity,
					"total_quantity" => $total_quantity,
					"remaining_quantity" => ($unit - $total_quantity),
					"mc_id" => $mc_id,
					"ironing_machine" => $ironing_machine,
					"q_index" => $q_index
				);
				
				$is_filter = FALSE;
				if ($is_filter)
				{
					//echo "Search Type = " . $searchType . "<br/>";
					//echo "Search Text = " . $searchText . "<br/>";
					
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
					else if ("coil_group_code" == $searchType)
					{
						if ($coil_group_code !== $searchText)
						{
							continue;
						}
					}
					else if ("thickness" == $searchType)
					{
						if (floatval($thickness) !== floatval($searchText))
						{
							continue;
						}
					}
				}

				$film_result[] = $temp;

				if ($ironing_machine) {
					
					// GET DATA FROM DB IF EXIST
					$exist_list = $this->fh_model->get_all_exclude($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);

					// echo count($exist_list);
					if (count($exist_list) == 0)
					{
						$unit_remaining = $this->fh_model->get_quantity_exclude($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);
						if ($unit_remaining > 0)
						{
							$total_quantity = $this->fh_model->get_all_unit_by_date_exclude($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);
							$mc_id = $this->pp_model->get_machine_default_by_program_code($program_code);
							// echo $program_code . " = " . $mc_id;
							
							

							
							$temp = array(
								"coil_group_code" => $coil_group_code,
								"thickness" => $thickness,
								"width" => $width,
								"slit_date" => $slit_date,
								"program_code" => $program_code,
								"unit" => $unit_remaining,
								"weight" => $weight,
								"external_program_code" => $external_program_code,
								"product_dtl_id" => $product_dtl_id,
								"quantity" => 0,
								"total_quantity" => $total_quantity,
								"remaining_quantity" => ($unit_remaining - $total_quantity),
								"mc_id" => $mc_id,
								"ironing_machine" => 0,
								"q_index" => 0
							);
							
							$film_result[] = $temp;
						}
					}
					else
					{
						for($i = 0; $i < count($exist_list); $i++)
						{
							// print_r($exist_list[$i]);
							$unit_remaining = $this->fh_model->get_quantity_exclude($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);
							$quantity = $this->fh_model->get_quantity_index($exist_list[$i]["mc_id"], $coil_group_code, $product_dtl_id, $theDate, $exist_list[$i]["temp"]);
							$total_quantity = $this->fh_model->get_all_unit_by_date_exclude($ironing_machine, $coil_group_code, $product_dtl_id, $theDate);
							
							if ($unit_remaining > 0) 
							{
								$temp = array(
									"coil_group_code" => $coil_group_code,
									"thickness" => $thickness,
									"width" => $width,
									"slit_date" => $slit_date,
									"program_code" => $program_code,
									"unit" => $unit_remaining,
									"weight" => $weight,
									"external_program_code" => $external_program_code,
									"product_dtl_id" => $product_dtl_id,
									"quantity" => $quantity,
									"total_quantity" => $total_quantity,
									"remaining_quantity" => ($unit_remaining - $total_quantity),
									"mc_id" => $exist_list[$i]["mc_id"],
									"ironing_machine" => 0,
									"q_index" => $exist_list[$i]["temp"]
								);
								
								$film_result[] = $temp;
							}
						}
					}
				}
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
		$data["searchType"] =  "";
		$data["searchText"] = "";
		
		$this->load->model("machine_model");
		$machine_temp = $this->machine_model->get_all();
		$machine = array();
		for($i = 0; $i < count($machine_temp); $i++)
		{
			$machine[$machine_temp[$i]["mc_id"]] = $machine_temp[$i]["machine_name"];
		}
		$data["machine"] = $machine;
		
		$machine_special_temp = $this->machine_model->get_special();
		$machine_special = array();
		for($i = 0; $i < count($machine_special_temp); $i++)
		{
			$machine_special[$machine_special_temp[$i]["mc_id"]] = $machine_special_temp[$i]["machine_name"];
		}
		$data["machine_special"] = $machine_special;

		$this->template->write_view('content', 'film_search_view', $data);
		$this->template->render();
	}
	
	function film_planning()
	{
		$searchText = $this->input->post("searchText");
		$searchType = $this->input->post("searchType");
		if (!isset($searchType)) {
			$searchType = "";
		}
		if (!isset($searchText)) {
			$searchText = "";
		}
		$data["searchType"] = $searchType;
		$data["searchText"] = $searchText;
		
		$is_filter = FALSE;
		
		$film_result = FALSE;
		if (("thickness" == $searchType || "coil_group_code" == $searchType || "external_program_code" == $searchType || "product_name" == $searchType) || empty($searchType) || empty($searchText)) 
		{
			$film_result = $this->film_model->get_all("slit_date", "desc");
			if (("thickness" == $searchType || "coil_group_code" == $searchType || "external_program_code" == $searchType || "product_name" == $searchType) && !empty($searchText))
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
			$film_result = $this->film_model->get_all("slit_date", "asc");
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
			
			//print_r($result);
			
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
					//$program_code = $this->pp_model->get_program_code_by_product_id($coil_group_code, $product_dtl_id);
					$program_code = $item['program_code'];
					$external_program_code = $item['program_code_ext'];
					$slit_date = $item['slit_date'];
					$unit += $item['unit'];
					$weight += $item['weight'];

				}
				
				//$external_program_code = $this->program_model->get_external_program_code_by_program_code_and_product_dtl_id($program_code, $product_dtl_id);
				
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
					//echo "Search Type = " . $searchType . "<br/>";
					//echo "Search Text = " . $searchText . "<br/>";
					
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
					else if ("coil_group_code" == $searchType)
					{
						//echo "TESTSETET";
						if ($coil_group_code !== $searchText)
						{
							continue;
						}
					}
					else if ("thickness" == $searchType)
					{
						//echo "TESTEST";
						if (floatval($thickness) !== floatval($searchText))
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
		
		$this->template->write_view('content', 'film_planning_view', $data);
		$this->template->render();
	}
	
	function history()
	{
		//$starttime = time();
		$searchText = $this->input->post("searchText");
		$searchType = $this->input->post("searchType");
		$startDate = $this->input->post("startDate");
		$endDate = $this->input->post("endDate");
		
		if (!isset($searchType)) {
			$searchType = "";
		}
		if (!isset($searchText)) {
			$searchText = "";
		}
		
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
		
		$is_filter = FALSE;
		
		$film_result = FALSE;
		if (("thickness" == $searchType || "coil_group_code" == $searchType || "external_program_code" == $searchType || "product_name" == $searchType) || empty($searchType) || empty($searchText)) 
		{
			$film_result = $this->film_model->get_history("slit_date", "desc", $startDate, $endDate);
			if (("thickness" == $searchType || "coil_group_code" == $searchType || "external_program_code" == $searchType || "product_name" == $searchType) && !empty($searchText))
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
		//$endtime = time();
		//echo "Get History : " . ($endtime - $starttime) . "<br/>";
		
		
		if ($film_result !== FALSE) 
		{
			$result = array();
			//$starttime = time();
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
			//$endtime = time();
			//echo "Group Film : " . ($endtime - $starttime) . "<br/>";
			//$startime= time();
			$product_result_temp = $this->product_model->get_all();
			$product_result = array();
			if ($product_result_temp !== FALSE) {
				foreach($product_result_temp as $item) {
					$product_result[$item->product_dtl_id] = $item->product_name_th;
				}
			}
			
			$data["product_result"] = $product_result;
			//$endtime = time();
			//echo "Get Product : " . ($endtime - $starttime) . "<br/>";
			//$starttime = time();
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
					//$program_code = $this->pp_model->get_program_code_by_product_id($coil_group_code, $product_dtl_id);
					$program_code = $item['program_code'];
					$external_program_code = $item['program_code_ext'];
					$slit_date = $item['slit_date'];
					$unit += $item['unit'];
					$weight += $item['weight'];

				}
				
				//$external_program_code = $this->program_model->get_external_program_code_by_program_code_and_product_dtl_id($program_code, $product_dtl_id);
				
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
						if (isset($product_result[$product_dtl_id]))
						{
					
							$product = $product_result[$product_dtl_id];
							if (strpos($product, $searchText) == false)
							{
								continue;
							}
						} 
						else
						{
							continue;
						}
					}
					else if ("coil_group_code" == $searchType)
					{
						if ($coil_group_code !== $searchText)
						{
							continue;
						}
					}
					else if ("thickness" == $searchType)
					{

						if (floatval($thickness) !== floatval($searchText))
						{
							continue;
						}
					}
					
				}
				
				$film_result[] = $temp;
					
			}
			//$endtime = time();
			//echo "Get Film Information and populate : " . ($endtime - $starttime) . "<br/>";
		}
		
		if ($film_result === FALSE) $film_result = array();
		
		//$startime = time();
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
		//$endtime = time();
		//echo "Sort Film Information : " . ($endtime - $starttime) . "<br/>";
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
	
	
	function update_quantity()
	{
		$username = $this->session->userdata("USERNAME");
		$temp_code = $this->input->post("temp_code");
		$theDate = $this->input->post("theDate");
		$fromSearch = $this->input->post("fromSearch");
		
		$sproduct_dtl_id = $this->input->post("product_dtl_id");
		$slot_no = $this->input->post("lot_no");
		$sthickness = $this->input->post("thickness");
		
		$this->db->trans_begin();
		
		for($i = 0; $i < count($temp_code); $i++) 
		{
			$quantity = $this->input->post("quantity_" . $temp_code[$i]);
			$machine_id = $this->input->post("machine_" . $temp_code[$i]);
			
			$temp_str = $this->convert->HexToAscii($temp_code[$i]);
			list($coil_group_code, $product_dtl_id, $index) = split("#", $temp_str);
			
			// echo $coil_group_code . " = " . $product_dtl_id . " = " . $index . " = " . $theDate . " = " . $machine_id . " = " . $quantity . "<br/>";
			
			$this->fh_model->update($coil_group_code, $product_dtl_id, $theDate, $quantity, $machine_id, $index);
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			
			user_log_message("INFO",  $data["result"]);
			
			$data['back_page'] = ($fromSearch == "true") ? "/film/edit_search" : "/film";
			
			$data['param'] = array(
				"user_date" => $theDate,
				"product_dtl_id" => $sproduct_dtl_id,
				"lot_no" => $slot_no,
				"film_date" => $theDate,
				"thickness" => $sthickness
			);
			
			$this->template->write_view('content', 'film_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			
			user_log_message("INFO",  $username . " update quantity completed.");
			
			$data['result'] = "บันทึกข้อมูลเรียบร้อยแล้ว";
			$data['back_page'] = ($fromSearch == "true") ? "/film/edit_search" : "/film";
			
			$data['param'] = array(
				"user_date" => $theDate,
				"product_dtl_id" => $sproduct_dtl_id,
				"lot_no" => $slot_no,
				"film_date" => $theDate,
				"thickness" => $sthickness
			);
			
			$this->template->write_view('content', 'film_result_view', $data);
			$this->template->render();
		}
	}
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */