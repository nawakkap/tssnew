<?php

class Select_coil extends CI_Controller {

	private static $PERMISSION = "SELECT_COIL";


	function __construct()
	{
		parent::__construct();

		check_permission(self::$PERMISSION);

		$this->load->model('order_model');
		$this->load->model('coil_model');
		$this->load->model('slit_model');
		$this->load->model('film_model');
		$this->load->model('config_model');
		$this->load->model('product_model');
		$this->load->model("coil_film_product_model", "cfp_model");
		$this->load->model("product_produce_model", "pp_model");
		$this->load->model("next_product_model", "np_model");
		$this->load->model("program_model");
		$this->load->model("config_model");

	}

	function index()
	{
	
		$searchType = $this->input->post("searchType");
		$searchText = $this->input->post("searchText");
		if (!isset($searchType)) {
			$searchType = "";
			$searchText = "";
		}
		if (!isset($searchText)) {
			$searchText = "";
		}
		
		$po_id = "";
		$thickness = "";
		
		if ("po_id" == $searchType) {
			$po_id = $searchText;
		}
		if ("thickness" == $searchType) {
			$thickness = $searchText;
		}
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call selected coil first step with searchText = " . $searchText . ", searchType = ". $searchType);
	
		$order_result_temp = $this->order_model->get_all_by_thickness($po_id, $thickness);
		if ($order_result_temp === FALSE)
		{
			$order_result_temp = array();
		}
		$order_result = array();
		foreach($order_result_temp as $item)
		{
			$result = $this->coil_model->check_coil_by_po_id($item["po_id"]);
			if ($result !== FALSE)
			{
				if ($result["total"]  > 0) 
				{
					$order_result[] = $item;
				}
			}
		}
		
		for($i= 0 ; $i < count($order_result); $i++)
		{
			$result = $this->coil_model->get_summary_weight_slit_by_po_id($order_result[$i]["po_id"]);
			$order_result[$i]["coil_sum_weight"] = $result;
			
			$weight_remaining = $order_result[$i]["weight_received"] - $order_result[$i]["coil_sum_weight"];
			$order_result[$i]["weight_remaining"] = $weight_remaining;
		}
		
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		$sort_list = array();
		if (!empty($sort_column))
		{
			foreach($order_result as $item)
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
			foreach($order_result as $item)
			{
				$sort_list[$item["po_id"]][] = $item;
			}
			ksort($sort_list);
			$data["sort_column"] = "po_id";
			$data["sort_by"] = "desc";
		}
		
		//print_r($sort_list);
		
		$data["order_result"]  = $sort_list;
		
		$this->template->write_view('content', "selected_first_view" , $data);
		$this->template->render();
	
	}
	
	function second_step()
	{
		$po_id = $this->input->post("po_id");
		
		$search = $this->input->post("search");
		$search_type = $this->input->post("search_type");
		
		if (empty($search)) $search = 'coil_lot_no';
		if (empty($search_type)) $search_type = 'desc';
		if ($search_type == "desc") $search_type = "asc";
		else $search_type = "desc";
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call selected coil second step with searchText = " . $search . ", searchType = ". $search);
		
		$coil_result = $this->coil_model->get_all_by_id_normal_status($po_id, $search, $search_type);
		if ($coil_result === FALSE)
		{
			$coil_result = array();
		}
		
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		$sort_list = array();
		if (!empty($sort_column))
		{
			foreach($coil_result as $item)
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
			foreach($coil_result as $item)
			{
				$sort_list[$item["coil_lot_no"]][] = $item;
			}
			ksort($sort_list);
			$data["sort_column"] = "coil_lot_no";
			$data["sort_by"] = "desc";
		}
		
		$data["search"] = $search;
		$data["search_type"] = $search_type;
		$data["coil_result"] = $sort_list;
		$data['po_id'] = $po_id;
		$this->template->write_view('content', "selected_second_view" , $data);
		$this->template->render();
	
		
	}
	
	function third_step()
	{
		$po_id = $this->input->post("po_id");
		$coil_id = $this->input->post("coil_id");
		$thickness = $this->input->post("thickness");
		if (!isset($thickness)) {
			$thickness = "";
		}
		
		
		if (empty($thickness))
		{
			$thickness  = $this->order_model->get_thickness_by_po_id($po_id);
			if ($thickness === FALSE)
			{
				$thickness = "";
			}
		}
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call selected coil third step with po_id = " . $po_id . ", coil_id = " . $coil_id . ", thickness = " . $thickness);
		
		
		if (empty($thickness)) 
		{
			$product_result = $this->product_model->get_all("product_display_id", "asc");
		}
		else
		{
			//$thickness = number_format($thickness, 2);
			$thickness = floatval($thickness);
			$product_result = $this->product_model->get_all_by_thickness($thickness);
		}
		if ($product_result === FALSE) {
			$product_result = array();
		}
		
		$product_result_temp = array();
		for($i = 0; $i < count($product_result); $i+=2) 
		{
			$temp = array();
			$temp[] = $product_result[$i];
			if (isset($product_result[$i + 1])) {
				$temp[] = $product_result[$i + 1];
			}
			
			$product_result_temp[] = $temp;
		}
		
		$data['product_result'] = $product_result_temp;
		
		
		$data["po_id"] = $po_id;
		$data["coil_id"] = $coil_id;
		$data["thickness"] = $thickness;
		$this->template->write_view('content', "selected_third_view" , $data);
		$this->template->render();
	
	}
	
	function fourth_step()
	{
		$po_id = $this->input->post("po_id");
		$coil_id = $this->input->post("coil_id");
		$thickness = $this->input->post("thickness");
		$product_dtl_id = $this->input->post("product_dtl_id");

		if (!isset($thickness)) {
			$thickness = "";
		}		
		
		/*
		$width = $this->input->post("width");
		if (!isset($width) || empty($width)) {
			$width = "1232";
		}
		*/
		
		if (!isset($thickness)) $thickness = "";
		if (!empty($thickness))
		{
			$thickness = floatval($thickness);
		}
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call selected coil fourth step with po_id = " . $po_id . ", coil_id = " . $coil_id . ", thickness = " . $thickness);
		if (is_array($product_dtl_id)) 
		{
			foreach($product_dtl_id as $item) {
				user_log_message("INFO", "product id = " . $item);
			}
		}
		else
		{
			user_log_message("INFO", "product id = " . $product_dtl_id);
		}
		
		$slit_temp = $this->slit_model->get_by_thickness_and_product_dtl_id($thickness, $product_dtl_id, TRUE);
		if ($slit_temp === FALSE)
		{
			$slit_temp = array();
		}
		
		$data['slit_spec_result']= $slit_temp;
		// $data["width"] = FALSE; // $width; Deprecated
		$data["po_id"] = $po_id;
		$data["coil_id"] = $coil_id;
		$data['thickness'] = floatval($thickness);
		$data['product_dtl_id'] = $product_dtl_id;
		$this->template->write_view('content', "selected_fourth_view" , $data);
		$this->template->render();

	}
	
	function fifth_step()
	{
		$thickness = $this->input->post("thickness");
		// $width = $this->input->post("width"); // Deprecated
		$slit_spec_id = $this->input->post("slit_spec_id");
		$product_dtl_id = $this->input->post("product_dtl_id");
		$po_id = $this->input->post("po_id");
		$coil_id = $this->input->post("coil_id");
		
		$product_result = array();
		$internal_program_code = array();
		$external_program_code = array();
		$slit_temp = $this->slit_model->get_by_id($slit_spec_id);
		$internal_program_code_list = array();
		foreach($slit_temp as $item)
		{
			$product_result[$item['product_dtl_id']] = $this->product_model->get_by_id($item['product_dtl_id']);
			// echo $this->db->last_query();
			$internal_code = $this->program_model->call_function_get_program_code($item["product_dtl_id"]);
			// echo $item["product_dtl_id"] . " = " .  $internal_code . "<br/>";
			// echo $this->db->last_query();
			if ($internal_code === FALSE)
			{
				$internal_program_code[$item['product_dtl_id']] = "ERROR";
			}
			else
			{
				// echo $item["product_dtl_id"] . " = " .  $internal_code . "<br/>";
				if (in_array($internal_code, $internal_program_code_list))
				{
					$max = max($internal_program_code_list);
					
					$temp = substr($max, 2);
					$temp++;
					$temp_internal_program_code = "PC". str_pad($temp, 6, "0", STR_PAD_LEFT);
					
					$internal_code = $temp_internal_program_code;
				}
				
				if (isset($internal_program_code[$item['product_dtl_id']]))
				{
					$internal_code = $internal_program_code[$item['product_dtl_id']];
				}
				
				
				$internal_program_code_list[] = $internal_code;
				// print_r($internal_program_code_list);
				
				array_unique($internal_program_code_list);
				
				$internal_program_code[$item['product_dtl_id']] = $internal_code;
				//echo $internal_code . " = " . $item['product_dtl_id'] . "<br/>";
				$ext_code = $this->program_model->get_external_program_code_normal_status_by_program_code_and_product_dtl_id($internal_code, $item["product_dtl_id"]);
		
				// echo $this->db->last_query();
				// echo $ext_code;
				if (!isset($external_program_code[$item['product_dtl_id']]))
				{
					if ($ext_code === FALSE)
					{
						$external_program_code[$item['product_dtl_id']] = "XXX";
					}
					else
					{
						$external_program_code[$item['product_dtl_id']] = $ext_code;
					}
				}
			}
			//
			
		}
		// print_r($internal_program_code);
		//print_r($external_program_code);
		$data["product_result"] = $product_result;
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call selected coil fifth step with po_id = " . $po_id . ", coil_id = " . $coil_id . ", thickness = " . $thickness);
		if (is_array($product_dtl_id)) 
		{
			foreach($product_dtl_id as $item) {
				user_log_message("INFO", "product id = " . $item);
			}
		}
		else
		{
			user_log_message("INFO", "product id = " . $product_dtl_id);
		}
		// user_log_message("INFO", "width = " . $width); // Deprecated
		
		$this->load->model("machine_model");
		$machine = $this->machine_model->get_special();
		$data["machine"] = $machine;
		
		$data["internal_program_code"] = $internal_program_code;
		$data["external_program_code"] = $external_program_code;
		$data['coil_id'] = $coil_id;
		$data["po_id"] = $po_id;
		$data['thickness'] = floatval($thickness);
		// $data["width"] = $width; // Deprecated
		$data["slit_spec_id"] = $slit_spec_id;
		$data['product_dtl_id'] = $product_dtl_id;
		$this->template->write_view('content', "selected_fifth_view" , $data);
		$this->template->render();
	}
	
	function sixth_step()
	{
		$thickness = $this->input->post("thickness");
		// $width = $this->input->post("width"); // Deprecated
		$slit_spec_id = $this->input->post("slit_spec_id");
		$product_dtl_id = $this->input->post("product_dtl_id");
		$po_id = $this->input->post("po_id");
		$coil_id = $this->input->post("coil_id");
				
		// Get Selected Coil
		$coil_temp = $this->coil_model->get_all_by_id_normal_status($po_id);
		$coil_result = array();
		foreach($coil_temp as $item)
		{
			if (in_array($item["coil_id"], $coil_id)) 
			{
				$coil_result[] = $item;
			}
		}
		$data["coil_result"] =  $coil_result;
		
		$slit_temp = $this->slit_model->get_by_id($slit_spec_id);
		$slit_spec_remark = $slit_temp[0]["remark"];
		$data["slit_spec_remark"] = $slit_spec_remark;
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call selected coil fifth step with po_id = " . $po_id . ", coil_id = " . $coil_id . ", thickness = " . $thickness);
		if (is_array($product_dtl_id)) 
		{
			foreach($product_dtl_id as $item) {
				user_log_message("INFO", "product id = " . $item);
			}
		}
		else
		{
			user_log_message("INFO", "product id = " . $product_dtl_id);
		}
		// user_log_message("INFO", "width = " . $width); // Deprecated
		user_log_message("INFO", "slit_spec_id = " . $slit_spec_id);
		
		
		$product_result = array();
		$slit_temp = $this->slit_model->get_by_id($slit_spec_id);
		$rim = 0;
		$zz_mapping = array();
		$max_zz = 0;
		$strip_list = array();
		
		foreach($slit_temp as $item)
		{
			$product_result[$item['product_dtl_id']] = $this->product_model->get_by_id($item['product_dtl_id']);
			$rim = $item["rim"];
		}
		
		$film_weight = array();
		foreach($slit_temp as $item)
		{
			$slit_width = $item["slit_width"];
			$slit_qty = $item["slit_qty"];
			$max_zz += $item["slit_qty"];
			
			$film_weight_sum = 0;
			foreach($coil_result as $coil_item)
			{
				// Phase 2 (Slit-width / coil-width)
				$film_weight_sum += round((($slit_width * $slit_qty) / $coil_item["width"]) * $coil_item['weight'], 3);
			}
			
			$film_weight[$item['product_dtl_id']] = $film_weight_sum;
		}
		$data["film_weight"] = $film_weight;
		//print_r($film_weight);
		//asort($product_result);
		$data["product_result"] = $product_result;
		
		
		$wage = array();
		$program = array();
		$external_program_code = array();
		$machine = array();
		foreach($product_dtl_id as $item)
		{
			$vat[$item] = $this->input->post("vat". $item);
			$wage[$item] = $this->input->post("wage". $item);
			$program[$item] = $this->input->post("program". $item);
			$external_program_code[$item] = $this->input->post("external_program_code" . $item);
			$machine[$item] = $this->input->post("machine" . $item);
		}
		
		$po_id_next = $this->np_model->get_next_name($po_id);
		$group_code = $po_id_next;
		// Increment Group code
		$temp = substr($group_code, -2);
		$temp++;
		$temp = str_pad($temp, 2, "0", STR_PAD_LEFT);
		$group_code_next = substr($group_code, 0, count($group_code) -3) . $temp;
		
		
		// Convert RIM 
		settype($rim, "integer"); // Convert To Integer
		$left_rim = $rim / 2;
		for($i = 0; $i < $max_zz; $i++)
		{
			if ($i < $left_rim) 
			{
				$strip_list[] = $i;
			}
			else if (($max_zz - $i) <=  $left_rim)
			{
				$strip_list[] = $i;
			}
		}
		
		// $selected_coil_list = self::_get_selected_coil();
		$selected_coil_list = $coil_result;
		$zz_mapping = array();
		$coil_slit_map = array();
		foreach($slit_temp as $item)
		{
			$slit_qty = $item['slit_qty'];
			$product_dtl_id = $item['product_dtl_id'];
			$slit_width = $item['slit_width'];
			
			settype($slit_qty, "integer");
			for($j = 0; $j < count($selected_coil_list); $j++)
			{
				$coil_id = $selected_coil_list[$j]['coil_id'];
				$coil_lot_no = $selected_coil_list[$j]['coil_lot_no'];
				$width = $selected_coil_list[$j]["width"];
				
				for($x = 0; $x < $slit_qty; $x++)
				{
					$film_weight =  round((($slit_width) / $width) * $selected_coil_list[$j]['weight'], 3);
					
					$zz = 0;
					if (isset($zz_mapping[$group_code_next . "_" . $coil_lot_no]))
					{
						$zz = $zz_mapping[$group_code_next . "_" . $coil_lot_no];
					}
				
					$strip = (in_array($zz, $strip_list)) ? STRIP_YES : STRIP_NO;
					// $strip = ($x == 0 || $x == ($max_zz -1)) ? STRIP_YES : STRIP_NO;
					$pad_coil_lot_no = str_pad($coil_lot_no, COIL_LOT_NO_PAD_LENGTH, "0", STR_PAD_LEFT);
					$pad_zz = str_pad(($zz + 1), ZZ_PAD_LENGTH, "0", STR_PAD_LEFT);
					$new_film_id = $group_code_next . $pad_coil_lot_no . $pad_zz . $strip . IRON_NO;
					
					
					// Increase zz
					if (!isset($zz_mapping[$group_code_next . "_" . $coil_lot_no])) {
						$zz_mapping[$group_code_next . "_" . $coil_lot_no] = 0;
					}
					$zz_mapping[$group_code_next . "_" . $coil_lot_no]++;
					
					
					if (! isset($coil_slit_map[$coil_id])) {
						$coil_slit_map[$coil_id] = array();
					}
					$coil_slit_map[$coil_id][] = array(
						"id" => $new_film_id,
						"weight" => $film_weight
					);
				}
			}
		}
		
		
		$this->load->model("machine_model");
		
		$machine_temp = $this->machine_model->get_special();
		$machine_list = array();
		for($i = 0; $i < count($machine_temp); $i++) 
		{
			$machine_list[$machine_temp[$i]["mc_id"]] = $machine_temp[$i]["machine_name"];
		}
		$data["machine_list"] = $machine_list;
		
		$data["group_code"] = $group_code_next;
		$data["wage"] = $wage;
		$data["vat"] = $vat;
		$data["program"] = $program;
		$data["external_program_code"] = $external_program_code;
		$data["machine"] = $machine;
		$data['coil_id'] = $coil_id;
		$data["po_id"] = $po_id;
		$data['thickness'] = floatval($thickness);
		$data["new_film_id"] = $coil_slit_map;
		// $data["width"] = $width; // Deprecated
		$data["slit_spec_id"] = $slit_spec_id;
		$data['product_dtl_id'] = $product_dtl_id;
		$this->template->write_view('content', "selected_sixth_view" , $data);
		$this->template->render();
	}

	function selected_slit()
	{
		$po_id = strtoupper($this->input->post("po_id"));

		$data['po_id'] = $po_id;

		$selected_coil_list = self::_get_selected_coil();

		$po_id_list= array();
		for($i = 0; $i < count($selected_coil_list) ; $i++) {
			array_push($po_id_list, $selected_coil_list[$i]['po_id']);
		}
		$po_id_list = array_unique($po_id_list);
		$data['po_id_list'] = $po_id_list;

		$total_weight = 0;
		for($i = 0; $i < count($selected_coil_list); $i++) {
			$total_weight += $selected_coil_list[$i]['weight'];
		}
		
		
		$data['total_weight'] = $total_weight;

		$data['selected_coil'] = $selected_coil_list;
		$this->template->write_view('content', "selected_coil_selected_view" , $data);
		$this->template->render();

	}


	function slit()
	{
		$selected_coil_list = self::_get_selected_coil();
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO", $username . " call slit.");

		$po_id_now = $selected_coil_list[0]['po_id'];

		$po_id_next = $this->np_model->get_next_name($po_id_now);
		$group_code = $po_id_next;

		$slit_spec_id = $this->input->post("slit_spec_id");
		if (!isset($slit_spec_id)) {
			$slit_spec_id = "";
		}
		$data["slit_spec_id"] = $slit_spec_id;
		
		/* Deprecated
		$width = $this->input->post("width");
		if (!isset($width) || empty($width) || $width <= 0) {
			$width = 1232;
		}
		$data['width'] = $width;
		*/

		// Increment Group code
		$temp = substr($group_code, -2);
		$temp++;
		$group_code_next = substr($group_code, 0, count($group_code) -2) . $temp;

		$data['slit_spec_result'] = $this->slit_model->get_by_thickness($selected_coil_list[0]['thickness']);

		$product_list = $this->product_model->get_all();
		$product = array();
		for($i = 0; $i < count($product_list); $i++)
		{
			$product[$product_list[$i]->product_dtl_id] = $product_list[$i]->product_name_en;
		}
		$data['product_result'] = $product;

		// Get for wage
		$slit_spec_result = $this->slit_model->get_by_id($slit_spec_id);
		$wage_product_list = array();
		$wage_price_list = array();
		if ($slit_spec_result !== FALSE) {
			for($i = 0; $i < count($slit_spec_result); $i++) {
				$wage_product_list[] = $slit_spec_result[$i]['product_dtl_id'];
			}
		}
		$data['slit_spec_selected'] = $slit_spec_result;
		
		$wage_product_list = array_unique($wage_product_list);
		for($i = 0; $i< count($wage_product_list); $i++) {
			$wage_price_list[$wage_product_list[$i]] = $this->product_model->get_wage_by_product_id($wage_product_list[$i]);
		}
		$data['wage_product'] = $wage_product_list;
		$data['wage_price'] = $wage_price_list;

		$data['group_code'] = $group_code_next;
		$data['selected_coil'] = $selected_coil_list;
		$this->template->write_view('content', "selected_coil_name_select_view" , $data);
		$this->template->render();

	}

	function slit_method()
	{
		$group_code = $this->input->post("group_code");
		$slit_spec_id = $this->input->post("slit_spec_id");
		$selected_coil_list = self::_get_selected_coil();
		// $width = $this->input->post("width"); // Deprecated
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO", $username . " call slit method with group code =" . $group_code);
		// user_log_message("INFO", $username . " call slit method with width =" . $width);
	
		$this->db->trans_begin();
		
		$error_flag = FALSE;
		$error_message = "";

		// Record Change
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();

		$slit_spec_mapping= array();
		$group_weight_mapping = array();
		$slit_spec_result = $this->slit_model->get_by_id($slit_spec_id);
		
		$zz_mapping = array();
		$max_zz = 0;
		$strip_list = array();
		
		$rim = 0;
		for($i = 0; $i < count($slit_spec_result); $i++)
		{
			$max_zz += $slit_spec_result[$i]["slit_qty"];
			$rim = $slit_spec_result[0]["rim"];
		}
		
		// Convert RIM 
		settype($rim, "integer"); // Convert To Integer
		$left_rim = $rim / 2;
		for($i = 0; $i < $max_zz; $i++)
		{
			if ($i < $left_rim) 
			{
				$strip_list[] = $i;
			}
			else if (($max_zz - $i) <=  $left_rim)
			{
				$strip_list[] = $i;
			}
		}
		
		
		$this->load->helper("string");
		for($i = 0; $i < count($slit_spec_result); $i++)
		{
			$slit_sub_no = $slit_spec_result[$i]['slit_sub_no'];
			$slit_qty = $slit_spec_result[$i]['slit_qty'];
			$product_dtl_id = $slit_spec_result[$i]['product_dtl_id'];
			$slit_width = $slit_spec_result[$i]['slit_width'];
			$slit_date = date_to_mysqldatetime();
			// $rim = $slit_spec_result[0]["rim"]; // New in 1.4
			// if (! $rim) $rim = 0;

			$cost_price = 0;
		
			$total_weight = 0;
			$zz = $slit_sub_no * $slit_qty;
			for($j = 0; $j < count($selected_coil_list); $j++)
			{
				// Insert into Film Information
				// $film_id = utime();
				$film_id = utime();
				$thickness = $selected_coil_list[$j]['thickness'];
				$coil_lot_no = $selected_coil_list[$j]['coil_lot_no'];
				$coil_id = $selected_coil_list[$j]['coil_id'];
				$po_id = $selected_coil_list[$j]['po_id'];
				
				// Add in Phase 2 (Get Coil Width from Coil Information instead of Get from Avg Width
				$width = $selected_coil_list[$j]["width"];
				
				if ($width)
				{
					$film_status = '1'; // Normal Film Status
					
					// Calculate Film weight
					$film_weight =  round((($slit_width * $slit_qty) / $width) * $selected_coil_list[$j]['weight'], 3);
					$total_weight += $film_weight;

					$order_price = $this->order_model->get_price_by_po_id($selected_coil_list[$j]['po_id']);
					if ($order_price > 0) 
					{
						$film_price = $film_weight * $order_price;
					
						$cost_price += $film_price;

						$populate_flag = 'N';
						$populate_date = "";
						
						//echo "A";
						$this->film_model->insert(	"ADD", $film_id, $thickness, $slit_width, $film_weight, $slit_qty, $film_status, $slit_date, $populate_flag, $populate_date,
									$product_dtl_id, $film_price, $record_change_by, $record_change_date);

						/// Add in 1.4
						/*
						if ($rim > 0)
						{
							settype($rim, "integer"); // Convert To Integer
							$left_type = ceil($rim / 2);
							$right_type = $slit_qty - $left_type;
							
							$my_slit_qty = $slit_qty;
							settype($my_slit_qty, "double");
							$mid_slit_qty = $my_slit_qty / 2;
							
							
							if ($i == 0 || $i == 1)
							{
								for($x = 0; $x < $slit_qty; $x++)
								{
									if ($x < $mid_slit_qty)
									{
										if ($x < $left_type)
										{
											$strip_list[] = $x;
											// echo "LEFT : " . $x . " " ;
										}
									}
									else
									{
										if ($x >= $right_type)
										{
											$strip_list[] = $x;
											// echo "RIGHT : " . $x . " " ;
										}
									}
								}
							}
							else if ($i == 2)
							{
								for($x = $slit_qty - 1; $x >= 0; $x--)
								{
									if ($x <= $mid_slit_qty)
									{
										if ($x < $left_type)
										{
											$strip_list[] = $x;
										}
									}
									else
									{
										if ($x >= $right_type)
										{
											$strip_list[] = $x;
										}
									}
								}
							}
						}
						*/
						
						// version 1.4
						for($x = 0; $x < $slit_qty; $x++)
						{
							$zz = 0;
							if (isset($zz_mapping[$group_code . "_" . $coil_lot_no]))
							{
								$zz = $zz_mapping[$group_code . "_" . $coil_lot_no];
							}
						
							$strip = (in_array($zz, $strip_list)) ? STRIP_YES : STRIP_NO;
							// $strip = ($x == 0 || $x == ($max_zz -1)) ? STRIP_YES : STRIP_NO;
							$pad_coil_lot_no = str_pad($coil_lot_no, COIL_LOT_NO_PAD_LENGTH, "0", STR_PAD_LEFT);
							$pad_zz = str_pad(($zz + 1), ZZ_PAD_LENGTH, "0", STR_PAD_LEFT);
							$new_film_id = $group_code . $pad_coil_lot_no . $pad_zz . $strip . IRON_NO;
							
							$insert_data = array(
								"film_id" => $film_id,
								"new_film_id" => $new_film_id,
								"coil_group_code" => $group_code,
								"coil_lot_no" => $coil_lot_no,
								"product_dtl_id" => $product_dtl_id,
								"iron_machine" => IRON_NO, // Default is NO (for Iron Machine)
								"strip" => $strip,
								"update_date" => date("Y-m-d H:i:s")
							);
							$this->db->insert("prd_film_information_detail", $insert_data);
							
							// Increase zz
							if (!isset($zz_mapping[$group_code . "_" . $coil_lot_no])) {
								$zz_mapping[$group_code . "_" . $coil_lot_no] = 0;
							}
							$zz_mapping[$group_code . "_" . $coil_lot_no]++;
						}
						
									
						// Insert into Product Coil Film Mapping
						$this->cfp_model->insert("ADD", $coil_id, $coil_lot_no, $po_id, $film_id, $slit_spec_id, $slit_sub_no, $group_code, $record_change_by, $record_change_date);

						$this->coil_model->update_status($coil_id, $coil_lot_no, $po_id, '2');

					} else {
						$error_flag = TRUE;
						$error_message = "ค่าราคาต้นทุนต่อกิโลกรัมของ Order มีค่าไม่ถูกต้อง กรุณาตรวจสอบ Order [PO ID : " . $po_id . "]";
						break;
					} // order price
				}
				else
				{
					$error_flag = TRUE;
					$error_message = "ค่าความกว้างของ Order มีค่าไม่ถูกต้อง กรุณาตรวจสอบ Order [PO ID : " . $po_id . "]";
					break;
				} // order width

				
				// echo "D"; 
				if ($error_flag == TRUE) 
				{
					break;
				}

			}
			$slit_spec_mapping[$slit_spec_id . $slit_sub_no . $product_dtl_id] = $cost_price;
			$group_weight_mapping[$slit_spec_id . $slit_sub_no . $product_dtl_id] = $total_weight;
						
		}
		if ($error_flag == TRUE)
		{
			$data['result'] = $error_message;
			$this->db->trans_rollback();
			
			user_log_message("ERROR", $username . " call slit method error . " . $error_message);
			
			$data['back_page'] = "/select_coil";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
			return;
		}


		$this->load->model("machine_iron_model", "mrm");
		//Insert into Product Produce
		$machine_by = "";
		$program_code_list = array();
		for($i = 0; $i < count($slit_spec_result); $i++)
		{
			$slit_sub_no = $slit_spec_result[$i]['slit_sub_no'];
			$product_dtl_id = $slit_spec_result[$i]['product_dtl_id'];

			$cost_price = $slit_spec_mapping[$slit_spec_id . $slit_sub_no . $product_dtl_id];
			$total_weight = $group_weight_mapping[$slit_spec_id . $slit_sub_no . $product_dtl_id];
			
			$vat = $this->input->post("vat" . $product_dtl_id);
			$program_code = $this->input->post("program" . $product_dtl_id);
			
			$wage = $this->input->post("wage". $product_dtl_id);
			
			$vat_code = "";
			$vat_price = "";
			
			
			if (isset($vat) && !empty($vat))
			{
				$vat_code = "VAT_NORMAL";
				$vat_price = $this->config_model->get_vat();
			}
			else
			{
				$vat_code = "VAT_PREMIUM";
				$vat_price = $this->config_model->get_premium();
			}
			
			$machine_by = $this->pp_model->get_machine_by($slit_spec_id, $product_dtl_id, $program_code);
			if ($machine_by === FALSE || !isset($machine_by))
			{
				$machine_by = "";
			}
			
			$mc_id = $this->pp_model->get_machine($slit_spec_id, $product_dtl_id, $program_code);
			
			$program_code_list[$program_code] = $product_dtl_id;

			$this->pp_model->insert("ADD", $group_code, $machine_by, $cost_price, $slit_spec_id, $slit_sub_no, $product_dtl_id, $program_code, $total_weight, $wage, $vat_code, $vat_price, $record_change_by, $record_change_date);
		
			// Update Machine
			$this->pp_model->update_machine($program_code, $product_dtl_id, $mc_id);
		
			// Machine for Ironing
			$machine = $this->input->post("machine" . $product_dtl_id);
			if ($machine)
			{
				$this->mrm->insert($group_code, $product_dtl_id, $machine);
				
				// Update prd_film_information_detail for Iron machine
				
				$update_data = array(
					"iron_machine" => IRON_YES,
				);
				
				$where = array(
					"coil_group_code" => $group_code,
					"product_dtl_id" => $product_dtl_id,
				);
				
				
				// $this->db->set("new_film_id", "CONCAT(SUBSTRING(new_film_id, 1, CHAR_LENGTH(new_film_id) - 1), '" . IRON_YES . "')", FALSE);
				$this->db->update("prd_film_information_detail", $update_data, $where);
				
				// End Update 1.4
			}
		}
		
		// Insert into Program Information
		foreach($program_code_list as $program_code => $product_dtl_id)
		{
			$processing_date = date_to_mysqldatetime();
			$program_status = "1"; // Normal
			
			$external_program_code = $this->input->post("external_program_code". $product_dtl_id);
			if (empty($external_program_code)) {
				$external_program_code = "XXX";
			}
		
			$this->program_model->insert("ADD", $program_code, $product_dtl_id, -1, "A", $processing_date, $program_status,  $external_program_code);
			$this->program_model->update_program_ext_code($program_code, $product_dtl_id, $external_program_code);
		}

		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			user_log_message("ERROR", $username . " call slit method error . " . $data["result"]);
			
			$this->db->trans_rollback();
			$data['back_page'] = "/select_coil";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			
			$data['result'] = "ทำการ Slit เรียบร้อยแล้ว ชื่อกลุ่ม คือ " . $group_code;
			
			user_log_message("ERROR", $username . " call slit method error . " . $data["result"]);
			
			$data['back_page'] = "/select_coil";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}

	function _get_selected_coil()
	{

		// Check
		$row_num = $this->input->post("row_num");
		$selected_coil_list = array();

		if (!isset($row_num) || empty($row_num)) {
			$row_num = 0;
		}


		for($i = 0; $i <= $row_num; $i++)
		{
			$coil_id = $this->input->post("select_coil_id". $i);
			$coil_lot_no = $this->input->post("select_coil_lot_no" . $i);
			$po_id = $this->input->post("select_po_id" . $i);
			if (isset($coil_id) && !empty($coil_id))
			{
				$result = $this->coil_model->get_by_id($coil_id, $coil_lot_no, $po_id);
				if ($result)
				{
					if (!in_array($result, $selected_coil_list)) {
						array_push($selected_coil_list, $result);
					}
				}
			}
		}

		return $selected_coil_list;
	}

}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */