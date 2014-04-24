<?php

class Coil extends CI_Controller {
	
	private static $PERMISSION = "GROUP_COIL";

	function __construct()
	{
		parent::__construct();	
		
		check_permission(self::$PERMISSION);
		
		$this->load->model('order_model');
		$this->load->model('coil_model');
		$this->load->model('slit_model');
		$this->load->model('film_model');
		$this->load->model('config_model');
		$this->load->model("coil_film_product_model", "cfp_model");
		
	}
	
	function add_method()
	{

		$referrer = $this->agent->referrer();
		$po_id = $this->input->post("po_id");
		$rowCount = $this->input->post("rowCount");
		
		$coil_status_map = array();
		
		$this->db->trans_begin();
		for($i = 1; $i <= $rowCount; $i++) 
		{
			$coil_lot_no = $this->input->post("coil_lot_no" . $i);
			$coil_id = $this->input->post("coil_id". $i);
			
			$coil_result = $this->coil_model->get_status($coil_id, $coil_lot_no, $po_id);
			
			$coil_status_map[$coil_id . $coil_lot_no] = ($coil_result === FALSE) ? 1 : $coil_result['coil_status'];
		}
		
		if (empty($po_id)) 
		{
			user_log_message("ERROR", "Add Coil Method has not PO ID");
			$data['result'] = "ไม่สามารถทำงานต่อได้เนื่องจากข้อมูลไม่ครบ";
			$data['back_page'] = "/main";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			user_log_message("INFO", "DELETE COIL GROUP BY PO_ID : " . $po_id);
			$this->coil_model->delete($po_id);
		
			$order_price = $this->order_model->get_price_by_po_id($po_id);
			if (!isset($order_price)) {
				$order_price = 0;
			}
			user_log_message("INFO", "PO ID price[" . $order_price . "] ");
			
			$index = 1; // For increase the coil_lot_no
			for($i = 1; $i <= $rowCount; $i++) 
			{
				$coil_lot_no = $this->input->post("coil_lot_no" . $i);
				$coil_id = $this->input->post("coil_id". $i);
				$thickness = $this->input->post("thickness". $i);
				$width = $this->input->post("width". $i);
				$weight = $this->input->post("weight" . $i);
				$coil_received_date = date_to_mysqldatetime($this->input->post("coil_received_date" . $i));
				$coil_status = 1; // Normal
				
				// Check Coil Status for previous coil 
				if (isset($coil_status_map[$coil_id . $coil_lot_no])) {
					$coil_status = $coil_status_map[$coil_id . $coil_lot_no];
				}
				
				$weight = round($weight);
				
				// Coil Price
				$coil_price = $order_price * $weight;
				
				$record_change_by = $this->session->userdata("USERNAME");
				$record_change_date = date_to_mysqldatetime();
				
				if (!empty($coil_id) && !empty($coil_lot_no) && !empty($thickness) && !empty($width) && !empty($weight) && !empty($coil_received_date)) 
				{
				
					$this->coil_model->insert("ADD", $coil_id, $index, $po_id, $thickness, $width, $weight, $coil_received_date, 
															$coil_status, $coil_price, $record_change_by, $record_change_date);
					$index++; // Increase it after add.
				}
			}
			
			if ($this->db->trans_status() === FALSE)
			{
				$data['result'] = 'ไม่สามารถจัดเก็บ Coil ได้เพราะมีเลข Coil Id ซ้ำกัน<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
				$this->db->trans_rollback();
				$data['back_page'] = "/order/order_detail/" . $this->convert->AsciiToHex($po_id);
				$this->template->write_view('content', 'order_result_view', $data);
				$this->template->render();
			}
			else
			{
				$this->db->trans_commit();
				redirect($referrer);
			}
		}
	}
	
	function coil_detail($coil_id, $coil_lot_no, $po_id) 
	{
		$coil_id = $this->convert->HexToAscii($coil_id);
		$po_id = $this->convert->HexToAscii($po_id);
		
	
		$result = $this->coil_model->get_by_id($coil_id, $coil_lot_no, $po_id);
		$data['coil_result'] = $result;
		$data['po_id'] = $po_id;
		$data['coil_id'] = $coil_id;
		$data['coil_lot_no'] = $coil_lot_no;

		$data['referer'] = site_url("/order/order_detail/" . $this->convert->AsciiToHex($po_id));
		
		$coil_group_result  = $this->cfp_model->get_group_name_by_id($coil_id, $coil_lot_no);
		if ($coil_group_result !== FALSE) 
		{
			$data['coil_group_code'] = $coil_group_result['coil_group_code'];
		}
		else
		{
			$data['coil_group_code'] = "";
		}
		
		$coil_status_result  = $this->config_model->get_coil_status();
		$data['coil_status_result'] = $coil_status_result;
		
		// Navigation
		$selected = "รายละเอียด Coil";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			"Order" => "/order",
			$selected => "/coil/coil_detail/" . $coil_id . "/" . $coil_lot_no . "/" . $po_id
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'coil_detail_view', $data);
		$this->template->render();
	}
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */