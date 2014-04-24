<?php

class Group extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model("group_model");
		$this->load->model("order_model");
		$this->load->model('coil_film_product_model', 'cfp_model');
		$this->load->model('coil_model');
		$this->load->model('slit_model');
		$this->load->model('film_model');
		$this->load->model('product_model');
		$this->load->model('config_model');
		$this->load->model('product_produce_model', "pp_model");
		$this->load->model("program_model");
		$this->load->model("machine_model");
		$this->load->model("film_history_model", "fh_model");
		$this->load->model("machine_iron_model", "mr_model");
	}
	
	function index() // For cancel lot no
	{
		check_permission("CANCEL_LOT");
		
		
		$group_temp = $this->group_model->get_all();
		if ($group_temp === FALSE)
		{
			$group_temp = array();
		}
		
		$group_map = array();
		
		foreach($group_temp as $item)
		{
			if (isset($group_map[$item["coil_group_code"]]))
			{
				$temp = $group_map[$item["coil_group_code"]];
				
				$temp["program_code"] = $temp["program_code"] . "," . $item["program_code"];
				$temp["program_code_ext"] = $temp["program_code_ext"] . "," . $item["program_code_ext"];
				
				$group_map[$item["coil_group_code"]] = $temp;
			}
			else
			{
				$temp["program_code"] = $item["program_code"];
				$temp["program_code_ext"] = $item["program_code_ext"];
				
				$group_map[$item["coil_group_code"]] = $temp;
			}
		}
		
		$data["group_result"] = $group_map;
		// Navigation
		$selected = "ส่วนจัดการยกเลิก Lot No";
		$navigator = array(
			"หน้าหลัก" => "/main",
			$selected => "/group"
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'group_list_view', $data);
		$this->template->render();

		/*
		$group_temp = $this->group_model->get_distinct_coil_group();
		if ($group_temp === FALSE) 
		{
			$group_temp = array();
		}
		
		//print_r($group_temp);
		
		//$data["group_result"] = $group_temp;
		
		$group_result = array();
		foreach($group_temp as $item) 
		{
			$program_temp = $this->pp_model->get_program_code_by_coil_group_code($item["coil_group_code"]);
			//echo $item["coil_group_code"]. "<br/>";
			//print_r($program_temp);
			
			$exist = FALSE;
			foreach($program_temp as $program_item) 
			{
				$checked_result = $this->program_model->check_program_code_exist($program_item["program_code"], $program_item["product_dtl_id"]);
				//echo $checked_result === TRUE;
				if ($checked_result === TRUE)
				{
					$exist = TRUE;
				}
				
				if ($exist === FALSE)
				{
					$checked_result = $this->program_model->check_program_status($program_item["program_code"], $program_item["product_dtl_id"]);
					if ($checked_result === TRUE)
					{
						$exist = TRUE;
					}
				}
			}	

			if ($exist === FALSE) 
			{

				$external_program_code = "";
				foreach($program_temp as $program_item)
				{
					$external_program_code .= "&nbsp;";
					$external_program_code .= $this->program_model->get_external_program_code_by_program_code_and_product_dtl_id($program_item["program_code"], $program_item["product_dtl_id"]);
					$external_program_code .= "&nbsp;,";
				}
				$external_program_code = substr($external_program_code, 0, strlen($external_program_code) - 1);
				
			
			
			
				$group_result[$item["coil_group_code"]] = array("internal_program_code" => $program_temp, "external_program_code" => $external_program_code);
			}
			
			//echo "======================= <br/>";
		}
		//$group_result = array_unique($group_result);
		
		$data["group_result"] = $group_result;
		
		// Navigation
		$selected = "ส่วนจัดการยกเลิก Lot No";
		$navigator = array(
			"หน้าหลัก" => "/main",
			$selected => "/group"
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'group_list_view', $data);
		$this->template->render();
		*/
	}
	
	function cancel_group() 
	{
		check_permission("CANCEL_LOT");
	
		$coil_group_code = $this->input->post("coil_group_code");
		
		if (!isset($coil_group_code)) 
		{
			$coil_group_code = "";
		}
		
		if (empty($coil_group_code))
		{
			$data['result'] = "ไม่สามารถ ยกเลิก Lot No นี้ได้";
			
			$this->db->trans_rollback();
			$data['back_page'] = "/group";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$cfp_result = $this->cfp_model->get_by_group_code($coil_group_code);
			
			foreach($cfp_result as $item)
			{
				$film_id = $item["film_id"];
				
				$coil_id = $item["coil_id"];
				$coil_lot_no = $item["coil_lot_no"];			
				$po_id = $item["po_id"];
						
				$pp_result = $this->pp_model->get_program_code_by_coil_group_code($coil_group_code);
				if ($pp_result === FALSE) $pp_result = array();
				
				for($i = 0; $i < count($pp_result); $i++)
				{
					$program_code = $pp_result[$i]["program_code"];
					$product_dtl_id = $pp_result[$i]["product_dtl_id"];
					
					//echo $program_code . "<>" . $product_dtl_id . "<br/>";
					
					$parameter = array("product_dtl_id" => $product_dtl_id, "program_code" => $program_code);
					
					$query = $this->db->query("SELECT * FROM prd_product_produce WHERE product_dtl_id = ? AND program_code = ?", $parameter);
					
					$temp = $query->result_array();
					if (count($temp) == 1) 
					{
						$this->program_model->delete_all($program_code, $product_dtl_id);
					}
					else
					{
						$this->program_model->delete($program_code, $product_dtl_id);
					}
					
					// Delete All Film detail
					$where = array(
						"coil_group_code" => $coil_group_code,
						"product_dtl_id" => $product_dtl_id
					);
					
					$this->db->delete("prd_film_information_detail", $where);
					
				}
				
				
				$this->film_model->delete($film_id);
				$this->coil_model->update_status($coil_id, $coil_lot_no, $po_id, "1"); // Update status to normal
				$this->pp_model->delete($coil_group_code);				
			}
			
			$this->cfp_model->delete_by_coil_group_code($coil_group_code);
				
		}
		
		if ($this->db->trans_status() === FALSE)
		{		
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			$this->db->trans_rollback();
			$data['back_page'] = "/group";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
		
			$this->db->trans_commit();
			$data['result'] = 'ทำการ ยกเลิก lot no เรียบร้อยแล้ว';
			$data['back_page'] = "/group";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}

	function group_detail($coil_group_code)
	{
	
		check_permission("GROUP_DETAIL");
	
		$group_code = $this->convert->HexToAscii($coil_group_code);
		$data['group_code'] = $group_code;
		
		$film_result = $this->film_model->get_all_by_group_code($group_code);
		$coil_list = array();
		$po_id = "";
		if ($film_result !== FALSE)
		{
			foreach($film_result as $item)
			{
				$coil_list[$item['coil_id']] = $item['coil_lot_no'];
				$po_id = $item['po_id'];
			}
		}
		
		$coil_result = array();
		foreach($coil_list as $coil_id => $coil_lot_no)
		{
			$coil = $this->coil_model->get_by_id($coil_id, $coil_lot_no, $po_id);
			if ($coil !== FALSE)
			{
				$coil_result[] = $coil;
			}
		}
		
		$coil_status_result = $this->config_model->get_coil_status();
		$data['coil_status_result'] = $coil_status_result;
		
		$data['po_id'] = $po_id;
		$data['coil_result'] = $coil_result;
		
		// Get Thickness By PO ID
		// $thickness = $this->order_model->get_thickness_by_po_id($po_id);
		
		// Get Product and Program code
		$group_list = $this->pp_model->get_program_code_by_coil_group_code($group_code);
		
		$temp_slit_spec = array();
		$quantity_list = array();
		$ironing_machine = array(); // TRUE or FALSE
		// Get Ext Code.
		for($i = 0; $i < count($group_list); $i++)
		{
			$ext_code = $this->program_model->get_external_program_code_by_program_code_and_product_dtl_id($group_list[$i]["program_code"], $group_list[$i]["product_dtl_id"]);
			$group_list[$i]["ext_code"] = $ext_code;
			
			$temp_slit_spec[$group_list[$i]["product_dtl_id"]] = $this->pp_model->get_by_group_code_and_product_id($group_code, $group_list[$i]["product_dtl_id"]);
		
			$quantity_list[$group_list[$i]["product_dtl_id"]] = $this->fh_model->get_all($group_code, $group_list[$i]["product_dtl_id"]);
		
			$isExist = $this->mr_model->check_exist($group_code, $group_list[$i]["product_dtl_id"]);
			
			if ($isExist) {
				$ironing_machine[$group_list[$i]["product_dtl_id"]] = array("result" => TRUE, "mc_id" => $isExist["mc_id"]);
			} else {
				$ironing_machine[$group_list[$i]["product_dtl_id"]] = array("result" => FALSE, "mc_id" => "");
			}
		}
		
		$data["group_list"] = $group_list;
		$data["quantity_list"] = $quantity_list;
		$data["ironing_machine"] = $ironing_machine;
		
		//$machine = $this->machine_model->get_all();
		//$data["machine"] = $machine;

		$width_list = array();
		foreach($temp_slit_spec as $prd_id => $item)
		{
			$slit_spec_id = $item["slit_spec_id"];
			$slit_sub_no = $item["slit_sub_no"];
			$width = $this->slit_model->get_width_by_id_and_sub_no($slit_spec_id, $slit_sub_no);
			
			if ($width !== FALSE)
			{
				$width_list[$prd_id] = $width["slit_width"];
			}
		}
		
		// print_r($width_list);
		
		$temp = array();
		foreach($width_list as $prd_id => $width)
		{
			$temp[$prd_id] = $this->product_model->get_product_by_width_minmax5($width);
		}
		
		$products = $this->product_model->get_all();
		$temp_products = array();
		foreach($products as $item)
		{
			$temp_products[$item->product_dtl_id] = $item->product_name_th;
		}
		$data["products"] = $temp_products;
		
		$product_list = array();
		foreach($temp as $prd_id => $item)
		{
			$item_list = array();
			for($i = 0; $i < count($item); $i++) {
				$item_list[$item[$i]["product_dtl_id"]] = $item[$i]["product_name_th"];
			}
			
			$product_list[$prd_id] = $item_list;
		}
		$data["product_list"] = $product_list;
		
		$this->load->model("machine_model");
		$machine_temp = $this->machine_model->list_all();
		$machine = array();
		for($i = 0; $i < count($machine_temp); $i++)
		{
			$machine[$machine_temp[$i]["mc_id"]] = array(
				"name" => $machine_temp[$i]["machine_name"],
				"type" => $machine_temp[$i]["machine_type"]
			);
		}
		$data["machine"] = $machine;
		
		// Navigation
		$selected = "รายละเอียด Lot";
		$navigator = array(
			"หน้าแรก" => "/main",
			$selected => "/group/group_detail/" . $coil_group_code
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);

		
		$this->template->write_view('content', 'group_detail_view', $data);
		$this->template->render();
	}
	
	function change_product() 
	{
		$new_product_dtl_id = $this->input->post("new_product_dtl_id");
		$new_program_code = $this->input->post("new_program_code");
		$coil_group_code = $this->input->post("coil_group_code");
		
		
		$old_program_code = $this->input->post("old_program_code");
		$old_product_dtl_id = $this->input->post("old_product_dtl_id");
		
		
		$this->db->trans_begin();
		if ((strlen($new_product_dtl_id) > 0) AND (strlen($new_program_code) > 0)) 
		{
			// Check Program code is existed
			if ($this->pp_model->count_by_program_code($new_program_code) == 0) 
			{
				$data["result"] = "หาเลขโปรแกรมภายใน [" . $new_program_code ."] ไม่เจอในระบบ";
			}
			else
			{
				$data = array("result" => "");
			
				// Success Case
				$isError = FALSE;

				// Update product_dtl_id and program_code to New value
				$result = $this->pp_model->update_product_dtl_id_and_program_code_by_program_code($new_product_dtl_id, $new_program_code, $old_program_code, $coil_group_code);
				if ($result === FALSE) 
				{
					$data["result"] = "ไม่สามารถเปลียนแปลง Product และ Program Code ได้ (1)";
					$isError = TRUE;
					break;
				}
				
				$list_coil_group_code = array($coil_group_code);
				
				// Delete old data from film model
				
				$result = $this->film_model->change_product_dtl_id($new_product_dtl_id, $old_product_dtl_id,  $list_coil_group_code);
				if ($result === FALSE) 
				{
					$data["result"] = "ไม่สามารถเปลียนแปลง Product และ Program Code ได้ (2)";
					$isError = TRUE;
				}
				
				
				// Update Film Quantity to new Product
				foreach($list_coil_group_code as $item)
				{
					$result = $this->fh_model->change_to_new_product($item, $old_product_dtl_id, $new_product_dtl_id);
					if ($result === FALSE) 
					{
						$data["result"] = "ไม่สามารถเปลียนแปลง Product และ Program Code ได้ (3)";
						$isError = TRUE;
						break;
					}
				}
				
				// Insert Temporary Program Info
				$program_status = $this->program_model->get_program_status_by_program_code_and_product_dtl_id($new_program_code, $new_product_dtl_id);
				$ext_program_code = $this->program_model->get_external_program_code_by_program_code_and_product_dtl_id($new_program_code, $new_product_dtl_id);
				
				$this->program_model->insert("ADD", $new_program_code, $new_product_dtl_id, -1, "A", date_to_mysqldatetime() , $program_status, $ext_program_code);
				
				if ($this->db->trans_status() === FALSE || $isError === TRUE)
				{
					$this->db->trans_rollback();
					
					user_log_message("INFO",  "Cannot Change Product : " . $data["result"]);
					$data['back_page'] = "/group/group_detail/" . $this->convert->AsciiToHex($coil_group_code);
					$this->template->write_view('content', 'order_result_view', $data);
					$this->template->render();
				}
				else
				{
					$this->db->trans_commit();
					
					$data['result'] = "เปลี่ยนแปลงสินค้าและเลขที่โปรแกรมภายในเรียบร้อย";

					user_log_message("INFO",  "add new program : " . $data["result"]);
					$data['back_page'] = "/group/group_detail/" . $this->convert->AsciiToHex($coil_group_code);
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
		$data['back_page'] = "/group/group_detail/" . $this->convert->AsciiToHex($coil_group_code);
		$this->template->write_view('content', 'order_result_view', $data);
		$this->template->render();
	}
	
	function populate_iron_machine()
	{
		$coil_group_code = $this->input->post("group_code");
		$product_dtl_id = $this->input->post("product_dtl_id");
		$mc_id = $this->input->post("mc_id");
		$old_machine = $this->input->post("old_machine");
		$mode = $this->input->post("mode");
		
		$isError = FALSE;
		
		$this->db->trans_begin();
		if ($mc_id)
		{
			$this->load->model("machine_iron_model", "mr_model");
			if ($mode == "ADD")
			{
				$this->mr_model->insert($coil_group_code, $product_dtl_id, $mc_id);
			}
			else if ($mode == "DELETE")
			{
				
				$unit = $this->fh_model->get_all_unit($old_machine, $coil_group_code, $product_dtl_id);
				
				if ($unit == 0)
				{
					$this->mr_model->delete($coil_group_code, $product_dtl_id);
				}
				else
				{
					$isError = TRUE;
				}
			}
		}
		
		if ($this->db->trans_status() === FALSE || $isError === TRUE)
		{
			$this->db->trans_rollback();

			$data["result"] = "ไม่สามารถทำรายการได้สำเร็จ กรุณาลองใหม่อีกครั้ง";
			user_log_message("INFO",  "Cannot " . $mode . " Iron Maching ");
			$data['back_page'] = "/group/group_detail/" . $this->convert->AsciiToHex($coil_group_code);
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			
			if ($mode == "ADD")
			{
				$data['result'] = "ทำการใช้งานเครื่องรีดเรียบร้อย";
				
				user_log_message("INFO",  "Use Ironing Machine : " . $data["result"]);
			}			
			else if ($mode == "DELETE")
			{
				$data['result'] = "ทำการเลิกใช้เครื่องรีดเรียบร้อย";
				
				user_log_message("INFO",  "UnUse Ironing Machine : " . $data["result"]);
			}
	
			$data['back_page'] = "/group/group_detail/" . $this->convert->AsciiToHex($coil_group_code);
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	// Return JSON
	function get_program_code_by_product_dtl_id($product_dtl_id)
	{
		$result = $this->pp_model->get_program_code_by_prd_id($product_dtl_id);
		
		$program_list = array();
		for($i = 0; $i < count($result); $i++) 
		{
			if ($this->program_model->get_program_status_by_program_code_and_product_dtl_id($result[$i], $product_dtl_id) == 1) 
			{
				$program_list[] = $result[$i];
			}
		}
		
		$program_list = array_unique($program_list);
		
		echo json_encode($program_list);
		return;
	}
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */