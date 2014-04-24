<?php

class Slit extends CI_Controller {

	private static $PERMISSION = "SLIT_SPEC";

	function __construct()
	{
		parent::__construct();	
		
		check_permission(self::$PERMISSION);
		
		$this->load->model("slit_model");
		$this->load->model("config_model");
		$this->load->model("product_model");
		$this->load->model("group_model");
		$this->load->model("coil_film_product_model", "cfp_model");
	}
	
	function index()
	{
		$order_by = $this->input->post("order_by");
		$order_by_type = $this->input->post("order_by_type");
		
		if (empty($order_by)) $order_by = 'slit_thickness';
		if (empty($order_by_type)) $order_by_type = 'asc';
		
		$data['order_by'] = $order_by;
		// Reverse the search type
		$data['order_by_type'] = ('asc' == $order_by_type) ? 'desc' : 'asc';
		
		$searchText = $this->input->post("searchText");
		if (!isset($searchText)) $searchText = "";
		if (!empty($searchText)) {
			$searchText = floatval($searchText);
		}	
		$data['result'] = $this->slit_model->get_all($order_by, $order_by_type, $searchText);
		$data["searchText"] = $searchText;
		
		$product_list = $this->product_model->get_all();
		$product = array();
		for($i = 0; $i < count($product_list); $i++) 
		{
			$product[$product_list[$i]->product_dtl_id] = $product_list[$i]->product_name_initial;
		}
		$data['product_result'] = $product;
		
		// Navigation
		$selected = "Slit specification";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			$selected => "/slit",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'slit_list_view.php', $data);
		$this->template->render();
	}
	
	function add_page() 
	{
		$slit_spec_id = $this->input->post("slit_spec_id");
		$edit_button = $this->input->post('editButton');
		
		$edit_mode = !empty($edit_button);
		
		$data['edit_mode'] = $edit_mode;
		
		$data['remark'] = '';
		
		$data['product_result'] = $this->product_model->get_all();
		
		if ($edit_mode) 
		{
			$result = $this->slit_model->get_by_id($slit_spec_id);
			$data['result']  = $result;
			$data['slit_thickness'] = $result[0]['slit_thickness'];
			
		}
		else
		{
			// Phase 2 Get lastest id and add 1
			$lastest_id = $this->slit_model->get_lastest_id();

			settype($lastest_id, "integer");
			$lastest_id++;
			
			$slit_spec_id = $lastest_id;
			$data['result'] = $this->slit_model->get_empty_data();
			$data['slit_thickness'] = 0;
		}
		
		$data['slit_spec_id'] = $slit_spec_id;
		
		$thickness_query = $this->config_model->get_thickness();
		
		$thickness_result = array();
		$i = 0;
		foreach($thickness_query as $value) {
			$thickness_result[$i] = $value;
			$i++;
		}
		
		$width_query = $this->config_model->get_width();
		$width_result = array();
		$i = 0;
		foreach($width_query as $value) {
			$width_result[$i] = $value;
			$i++;
		}
		
		$data['thickness_result'] = $thickness_result;
		$data['width_result'] = $width_result;
		
		$this->template->write_view('content', 'slit_add_view', $data);
		$this->template->render();
		
	}
	
	function load_slit_page() // Use in Coil Detail
	{
		$slit_spec_id = $this->input->post("slit_spec_id");
	
		$result = $this->slit_model->get_by_id($slit_spec_id);
		if ($result == FALSE) {
			$data['result'] = array();
		} else {
			$data['result'] = $result;
		}
		
		$this->load->view("coil_slit_view", $data);
	}
	
	function add_method() 
	{
		//echo "ADD METHOD";
		$slit_spec_id = $this->input->post("slit_spec_id");
		$slit_thickness = $this->input->post("slit_thickness");
		
		$slit_width_1 = $this->input->post("slit_width_1");
		$slit_qty_1 = $this->input->post("slit_qty_1");
		$product_dtl_id1 = $this->input->post("product_dtl_id1");
		$ratio_1 = $this->input->post("ratio_1");
		
		/*
		echo "WIDTH : " . $slit_width_1 . "<br/>";
		echo "QTY : " . $slit_qty_1 . "<br/>";
		echo "Product DTL ID : " . $product_dtl_id1 . "<br/>";
		echo "RATIO : " . $ratio_1 . "<br/>";
		*/
		
		$slit_width_2 = $this->input->post("slit_width_2");
		$slit_qty_2 = $this->input->post("slit_qty_2");
		$product_dtl_id2 = $this->input->post("product_dtl_id2");
		$ratio_2 = $this->input->post("ratio_2");
		
		$slit_width_3 = $this->input->post("slit_width_3");
		$slit_qty_3 = $this->input->post("slit_qty_3");
		$product_dtl_id3 = $this->input->post("product_dtl_id3");
		$ratio_3 = $this->input->post("ratio_3");
		
		$rim = $this->input->post("rim");
		$description = $this->input->post("description");	
		
		$this->db->trans_begin();
		
		$slit_list = array();
		
		if (!empty($slit_width_1) && $slit_width_1 != 0 && !empty($slit_qty_1) && $slit_qty_1 != 0 && !empty($product_dtl_id1))
		{
			$temp = array(
				'slit_width' => $slit_width_1,
				'slit_qty' => $slit_qty_1,
				'product_dtl_id' => $product_dtl_id1,
				'ratio' => $ratio_1,
				"rim" => $rim,
				"remark" => $description
			);
		
			array_push($slit_list, $temp);
		}
		
		if (!empty($slit_width_2) && $slit_width_2 != 0 && !empty($slit_qty_2) && $slit_qty_2 != 0 && !empty($product_dtl_id2)) 
		{
			$temp = array(
				'slit_width' => $slit_width_2,
				'slit_qty' => $slit_qty_2,
				'product_dtl_id' => $product_dtl_id2,
				'ratio' => $ratio_2,
				"rim" => $rim,
				"remark" => $description
			);
		
			array_push($slit_list, $temp);
		}
		
		if (!empty($slit_width_3) && $slit_width_3 != 0 && !empty($slit_qty_3) && $slit_qty_3 != 0 && !empty($product_dtl_id3)) 
		{
			$temp = array(
				'slit_width' => $slit_width_3,
				'slit_qty' => $slit_qty_3,
				'product_dtl_id' => $product_dtl_id3,
				'ratio' => $ratio_3,
				"rim" => $rim,
				"remark" => $description
			);
		
			array_push($slit_list, $temp);
		}
		
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		//print_r($slit_list);
		
		for($i = 0; $i < count($slit_list); $i++) 
		{
			
			$temp = $slit_list[$i];
			
			$this->slit_model->insert("ADD", $slit_spec_id, $i+1, $slit_thickness, $temp['slit_qty'], $temp['slit_width'], $temp['product_dtl_id'], $temp['ratio'],
						$temp["rim"], $description, $record_change_by, $record_change_date) ;
						
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/slit";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{		
			$this->db->trans_commit();
			$data['result'] = "เพิ่มข้อมูลเรียบร้อยแล้ว";
			$data['back_page'] = "/slit";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	function edit_method() 
	{
		$slit_spec_id = $this->input->post("slit_spec_id");
		$slit_thickness = $this->input->post("slit_thickness");
		
		$slit_width_1 = $this->input->post("slit_width_1");
		$slit_qty_1 = $this->input->post("slit_qty_1");
		$product_dtl_id1 = $this->input->post("product_dtl_id1");
		$ratio_1 = $this->input->post("ratio_1");
		
		$slit_width_2 = $this->input->post("slit_width_2");
		$slit_qty_2 = $this->input->post("slit_qty_2");
		$product_dtl_id2 = $this->input->post("product_dtl_id2");
		$ratio_2 = $this->input->post("ratio_2");
		
		$slit_width_3 = $this->input->post("slit_width_3");
		$slit_qty_3 = $this->input->post("slit_qty_3");
		$product_dtl_id3 = $this->input->post("product_dtl_id3");
		$ratio_3 = $this->input->post("ratio_3");
		
		$rim = $this->input->post("rim");
		$description = $this->input->post("description");
		
		$this->db->trans_begin();
		
		$slit_list = array();
		
		if (!empty($slit_width_1) && $slit_width_1 != 0 && !empty($slit_qty_1) && $slit_qty_1 != 0 && !empty($product_dtl_id1) && $product_dtl_id1 != 0) 
		{
			$temp = array(
				'slit_width' => $slit_width_1,
				'slit_qty' => $slit_qty_1,
				'product_dtl_id' => $product_dtl_id1,
				'ratio' => $ratio_1,
				'rim' => $rim
			);
		
			array_push($slit_list, $temp);
		}
		
		if (!empty($slit_width_2) && $slit_width_2 != 0 && !empty($slit_qty_2) && $slit_qty_2 != 0 && !empty($product_dtl_id2) && $product_dtl_id2 != 0) 
		{
			$temp = array(
				'slit_width' => $slit_width_2,
				'slit_qty' => $slit_qty_2,
				'product_dtl_id' => $product_dtl_id2,
				'ratio' => $ratio_2,
				'rim' => $rim
			);
		
			array_push($slit_list, $temp);
		}
		
		if (!empty($slit_width_3) && $slit_width_3 != 0 && !empty($slit_qty_3) && $slit_qty_3 != 0 && !empty($product_dtl_id3) && $product_dtl_id3 != 0) 
		{
			$temp = array(
				'slit_width' => $slit_width_3,
				'slit_qty' => $slit_qty_3,
				'product_dtl_id' => $product_dtl_id3,
				'ratio' => $ratio_3,
				'rim' => $rim
			);
		
			array_push($slit_list, $temp);
		}
		
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		if (count($slit_list) > 0) 
		{
			$this->slit_model->delete($slit_spec_id);
		}
		
		for($i = 0; $i < count($slit_list); $i++) 
		{
			$temp = $slit_list[$i];

			$this->slit_model->insert("ADD", $slit_spec_id, $i+1, $slit_thickness, $temp['slit_qty'], $temp['slit_width'], $temp['product_dtl_id'], $temp['ratio'],
						$description, $temp['rim'], $record_change_by, $record_change_date) ;
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/slit";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{				
			$this->db->trans_commit();
			$data['result'] = "แก้ไขข้อมูลเรียบร้อยแล้ว";
			$data['back_page'] = "/slit";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		
	}
	
	function delete_method() 
	{
		$slit_spec_id = $this->input->post("slit_spec_id");
		
		$this->db->trans_begin();
		
		$this->slit_model->delete($slit_spec_id);
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/slit";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{	
			$this->db->trans_commit();
			$data['result'] = "ลบข้อมูลเรียบร้อยแล้ว";
			$data['back_page'] = "/slit";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		
	}
	
	function slit_detail($slit_spec_id)
	{
		$slit_result = $this->slit_model->get_by_id($slit_spec_id);
		$data['slit_result'] = $slit_result;
		
		$slit_thickness = $slit_result[0]['slit_thickness'];
		
		$data['slit_thickness'] = $slit_thickness;
		
		$product_mapping = array();
		foreach($slit_result as $item)
		{
			$temp = $this->product_model->get_by_id($item['product_dtl_id']);
			$product_mapping[$item['product_dtl_id']] = $temp['product_name_th'];
		}
		
		$data['product_mapping'] = $product_mapping;
		
		$cfp_result = $this->cfp_model->get_group_code_by_slit_spec_id($slit_spec_id);
		
		$group_result = array();
		if ($cfp_result !== FALSE) 
		{
			foreach($cfp_result as $item)
			{
				$group_result[] = $this->group_model->get_summary_of_group_code($item['coil_group_code']);
			}
		}

		$data['group_result'] = $group_result;
		
		// Navigation
		$selected = "Slit specification detail";

		$navigator = array(
			"หน้าแรก" => "/main",
			"Slit specification" => "/slit",
			$selected => "/slit/slit_detail/" . $slit_spec_id
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		
		$this->template->write_view('content', 'slit_detail_view', $data);
		$this->template->render();
	}
	
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */