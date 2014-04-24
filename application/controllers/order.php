<?php

class Order extends CI_Controller 
{
	private static $PERMISSION = "ORDER";

	function __construct()
	{
		parent::__construct();	
		
		check_permission(self::$PERMISSION);
		
		$this->load->model('order_model');
		$this->load->model('coil_model');
		$this->load->model('supplier_model');
		$this->load->model('config_model');
		// $this->load->model('slit_model');
		
	}
	
	function index()
	{
		$search = $this->input->post("searchText");
	
		$username = $this->session->userdata("USERNAME");
		// user_log_message("INFO",  $username . " is in the order page.");
		
		// $this->load->model("logging_model");
		// $this->logging_model->info($username . " access 'ORDER' page.", $username);

		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		
		if (empty($sort_column) && empty($sort_by)) 
		{
			$sort_column = $this->session->userdata("order_sort_column");
			$sort_by = $this->session->userdata("order_sort_by");
			
			if (empty($sort_column)) {
				$sort_column = "order_received_date";
			}
			if (empty($sort_by)) {
				$sort_by = "desc";
			}
		}
		
		$data["sort_column"] = $sort_column;
		$data["sort_by"] = ("asc" == $sort_by) ? "desc" : "asc";
		
		$data['search'] = $search;
		
		// $this->logging_model->info("Search " . $search . " , Column : " . $sort_column . " , By : " . $sort_by, $username);
		
		$result = $this->order_model->get_all($sort_column, $sort_by, $search);
		$supplier_result = $this->supplier_model->get_nameid_list();
		
		$data['result'] = $result;
		$data['supplier_result'] = $supplier_result;
		
		$data['order_status'] = $this->config_model->get_order_status();
		
		// Save search to session
		//$this->session->userdata("ordersort", "");
		$order_sort = array(
			"order_sort_column" => $sort_column,
			"order_sort_by" => $sort_by
		);
		$this->session->set_userdata($order_sort);
		
		// Navigation
		$navigator = array(
			"หน้าแรก" => "/main",
			"Order" => "/order"
		);
		
		$data["navigation"] = build_navigation("Order", $navigator);
		
		$this->template->write_view('content', 'order_list_view', $data);
		$this->template->render();
	}
	
	function history()
	{
		$search  = $this->input->post("searchText");
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " is in the order history page.");
		
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		
		$startDate = $this->input->post("startDate");
		$endDate = $this->input->post("endDate");
		// If cannot set startDate and endDate , set them one month
		if ($startDate === FALSE AND $endDate === FALSE)
		{
			$endDate = date('Y-m-d', strtotime("now"));
			$startDate = date('Y-m-d', strtotime("-30 day"));
		}
		$data["startDate"] = $startDate;
		$data["endDate"] = $endDate;
		
		if (empty($sort_column) && empty($sort_by)) 
		{
			$sort_column = $this->session->userdata("order_history_sort_column");
			$sort_by = $this->session->userdata("order_history_sort_by");
			
			if (empty($sort_column)) {
				$sort_column = "order_received_date";
			}
			if (empty($sort_by)) {
				$sort_by = "desc";
			}
		}
		
		$data["sort_column"] = $sort_column;
		$data["sort_by"] = ("asc" == $sort_by) ? "desc" : "asc";
		
		$data['search'] = $search;

		$result = $this->order_model->get_history_all($sort_column, $sort_by, $search, $startDate, $endDate);
		$supplier_result = $this->supplier_model->get_nameid_list();
		
		$data['result'] = $result;
		$data['supplier_result'] = $supplier_result;
		
		$data['order_status'] = $this->config_model->get_order_status();
		
		// Save to session
		$order_sort = array(
			"order_history_sort_column" => $sort_column,
			"order_history_sort_by" => $sort_by
		);
		$this->session->set_userdata($order_sort);
		
		// Navigation
		$navigator = array(
			"หน้าแรก" => "/main",
			"Order" => "/order",
			"Order (ข้อมูลเก่า)" => "/order/history"
		);
		
		$data["navigation"] = build_navigation("Order (ข้อมูลเก่า)", $navigator);
	
		$this->template->write_view('content', 'order_history_list_view', $data);
		$this->template->render();
	}
	
	function add_page() 
	{
		check_permission(self::$PERMISSION);
		
		$username = $this->session->userdata("USERNAME");
	
		$po_id = $this->input->post('po_id');
		$edit_button = $this->input->post('editButton');

		
		$edit_mode = !empty($edit_button);
		
		$data['edit_mode'] = $edit_mode;
		$data['po_id'] = $po_id;
		
		$result = FALSE;
		if ($edit_mode) 
		{
			user_log_message("INFO",  $username . " is in the EDIT Order page with PO ID is " . $po_id);
		
			$result = $this->order_model->get_by_id($po_id);
			$data['result']  = $result;
		}
		else
		{
			user_log_message("INFO",  $username . " is in the ADD Order page.");
			
			$data['result'] = $this->order_model->get_empty_data();
		}
		
		$supplier_result = $this->supplier_model->get_idname_list();
		$data['supplier_result'] = $supplier_result;
		
		$payment_result = $this->config_model->get_payment_term();
		$data['payment_result'] = $payment_result;
		
		$thickness_query = $this->config_model->get_thickness();
		
		$thickness_result = array();
		foreach($thickness_query as $value) {
			$thickness_result[$value] = $value;
		}
		
		// Add Specail Thickness Value
		if ($result !== FALSE) 
		{
			$thickness_temp = $result["thickness"];
			if (!isset($thickness_result[$thickness_temp])) {
				$temp = array();
				$temp[$thickness_temp] = $thickness_temp;
				array_push($thickness_result, $temp);
			}
		}
		
		
		$width_query = $this->config_model->get_width();
		$width_result = array();
		foreach($width_query as $value) {
			$width_result[$value] = $value;
		}
		
		// Add Specail Width Value
		if ($result !== FALSE) 
		{
			$width_temp = $result["width"];
			if (!isset($width_result[$width_temp])) {
				$temp = array();
				$temp[$width_temp] = $width_temp;
				array_push($width_result, $temp);
			}
		}
		
		$data['thickness_result'] = $thickness_result;
		$data['width_result'] = $width_result;
		
		$payment_map_result = $this->supplier_model->get_payment_term_map();
		$data['payment_map_result'] = $payment_map_result;
		
		$premium = $this->config_model->get_premium();
		$data['premium'] = $premium;
		
		$vat = $this->config_model->get_vat();
		$data['vat'] = $vat;
		
		
		// Navigation
		$selected = "เพิ่มข้อมูล Order";
		if ($edit_mode) {
			$selected = "แก้ไขข้อมูล Order";
		}
		
		user_log_message("INFO",  $username . " select " . $selected);
		
		$navigator = array(
			"หน้าแรก" => "/main",
			"Order" => "/order",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
	
		$this->template->write_view('content', 'order_add_view', $data);
		$this->template->render();
	}
	
	function add_method() 
	{
		check_permission(self::$PERMISSION);
		
		$username = $this->session->userdata("USERNAME");
		
		user_log_message("INFO",  $username . " calls add order method.");

		$po_id = $this->input->post("po_id");
		$order_received_date = date_to_mysqldatetime($this->input->post("order_received_date"));
		$supplier_id = $this->input->post("supplier_id");
		$thickness = $this->input->post("thickness");
		$width = $this->input->post("width");
		$weight = $this->input->post("weight");
		$payment_term = $this->input->post("payment_term");
		$amt_current_invoice = $this->input->post("amt_current_invoice");
		$vat_status = $this->input->post("vat_status");
		$order_status = $this->input->post("order_status");
		$price_base = $this->input->post("price_base");
		$price = $this->input->post("price");
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		//Remove comma 
		$amt_current_invoice = str_replace(",", "", $amt_current_invoice);
		
		$this->db->trans_begin();
		$this->order_model->insert("ADD", $po_id, $order_received_date, $supplier_id, $thickness, $width, $weight, $payment_term, 
												 $amt_current_invoice, $vat_status, $order_status, $price_base, $price, $record_change_by, $record_change_date);
												 
		if ($this->db->trans_status() === FALSE)
		{
			user_log_message("INFO",  $username . " cannot add order because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
			
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			if ($this->db->_error_number() == "1062") 
			{
				$data['result'] = 'ไม่สามารถเพิ่มข้อมูํลได้ เพราะเลข PO_ID ซ้ำแล้ว<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			}
			
			$this->db->trans_rollback();
			$data['back_page'] = "/order";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			user_log_message("INFO",  $username . " can add order with PO ID " . $po_id);
			
			$this->db->trans_commit();
			$data['result'] = 'เพิ่มข้อมูลเรียบร้อยแล้ว';
			$data['back_page'] = "/order";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}		
	}
	
	function edit_method() 
	{
	
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " calls edit order method.");
	
		$po_id = $this->input->post("po_id");
		$order_received_date = date_to_mysqldatetime($this->input->post("order_received_date"));
		$supplier_id = $this->input->post("supplier_id");
		$thickness = $this->input->post("thickness");
		$width = $this->input->post("width");
		$weight = $this->input->post("weight");
		$payment_term = $this->input->post("payment_term");
		$amt_current_invoice = $this->input->post("amt_current_invoice");
		$vat_status = $this->input->post("vat_status");
		$order_status = $this->input->post("order_status");
		$price_base = $this->input->post("price_base");
		$price = $this->input->post("price");
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		//Remove comma 
		$amt_current_invoice = str_replace(",", "", $amt_current_invoice);
		
		$this->db->trans_begin();
		$this->order_model->insert("EDIT", $po_id, $order_received_date, $supplier_id, $thickness, $width, $weight, $payment_term, 
												 $amt_current_invoice, $vat_status, $order_status, $price_base, $price, $record_change_by, $record_change_date);

		if ($this->db->trans_status() === FALSE)
		{
			user_log_message("INFO",  $username . " cannot edit order because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
		
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/order";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			user_log_message("INFO",  $username . " can edit order with PO ID " . $po_id);
		
			$this->db->trans_commit();
			$data['result'] = 'แก้ไขข้อมูลเรียบร้อยแล้ว';
			$data['back_page'] = "/order";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	function delete_method()
	{
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " calls delete order method.");
	
		$po_id = $this->input->post("po_id");
		
		$this->db->trans_begin();
		
		$coil_result = $this->coil_model->get_all_by_id($po_id);
		
		if (count($coil_result) > 0)
		{
			user_log_message("INFO",  $username . " cannot delete order because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
			
			$data['result'] = 'ไม่สามารถลบได้ เพราะ Order [' . $po_id . '] มี Coil อยู่ใน Order.';
			$this->db->trans_rollback();
			$data['back_page'] = "/order";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
			return;
		}
		else
		{
			$this->order_model->delete($po_id);
			
			if ($this->db->trans_status() === FALSE)
			{
				user_log_message("INFO",  $username . " cannot delete order because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
			
				$data['result'] = 'ไม่สามารถลบได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
				$this->db->trans_rollback();
				$data['back_page'] = "/order";
				$this->template->write_view('content', 'order_result_view', $data);
				$this->template->render();
				return;
			}
			else
			{		
				user_log_message("INFO",  $username . " can delete order with PO ID " . $po_id);
			
				$this->db->trans_commit();
				$data['result'] = "ลบข้อมูลเรียบร้อยแล้ว";
				$data['back_page'] = "/order";
				$this->template->write_view('content', 'order_result_view', $data);
				$this->template->render();
				return;
			}
		}
	}
	
	function update_status() 
	{
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " calls update status order method.");
	
		$po_id = $this->input->post("po_id");
		$order_status = $this->input->post("order_status");
		
		if ($this->agent->is_referral())
		{
			$data['referer'] = $this->agent->referrer();
		}
		else
		{
			$data['referer'] = "/order";
		}
		
		$this->db->trans_begin();
		$this->order_model->update_status($po_id, $order_status); 
		
		if ($this->db->trans_status() === FALSE)
		{
			user_log_message("INFO",  $username . " cannot update status of  order because ERROR ID : " . $this->db->_error_number() . " MESSAGE : " . $this->db->_error_message());
		
			$data['result'] = 'ไม่สามารถเปลี่ยนสถานะของ Order ได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$data['back_page'] = "/order";
			$this->db->trans_rollback();
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
			return;
		}
		else
		{
			user_log_message("INFO",  $username . " can update order status with PO ID " . $po_id);
		
			$this->db->trans_commit();
			redirect($data['referer']);
			return;
		}
	}
	
	function order_detail($po_id, $history = 0)
	{	
		$po_id = $this->convert->HexToAscii($po_id);
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " calls order detail method with PO ID " . $po_id);
	
		$data['po_id'] = $po_id;
		
		$query = $this->order_model->get_by_id($po_id);
		if ($query == FALSE) 
		{
			user_log_message("INFO",  "Cannot query POID[" . $po_id ."]");
		
			$data['result'] = "ไม่มีข้อมูล PO ID เบอร์ " . $po_id . " อยู่ในระบบ<br/>กรุณาลองใหม่อีกครั้ง";
			$data['back_page'] = "/main";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$data['order_result'] = $temp_order = $query;
			$data["history"] = $history;
			
			$query = $this->coil_model->get_all_by_id($po_id);
			$data['coil_result'] = $query;
			
			$received_weight = 0;
			
			//น้ำหนักค้างรับ
			foreach($query as $item)
			{
				$received_weight += $item['weight'];
			}
			
			$data['accrual_weight'] = $temp_order['weight'] - $received_weight;
			
			$query = $this->supplier_model->get_idname_list();
			$data['supplier_result'] = $query;
			
			$payment_result = $this->config_model->get_payment_term();
			$data['payment_result'] = $payment_result;
			
			$thickness_query = $this->config_model->get_thickness();
			
			$thickness_result = array();
			$i = 0;
			foreach($thickness_query as $value) {
				$thickness_result[$i] = $value;
				$i++;
			}
			
			$coil_status_result = $this->config_model->get_coil_status();
			$data['coil_status_result'] = $coil_status_result;
			
			$order_status_result = $this->config_model->get_order_status();
			$data['order_status_result'] = $order_status_result;
			
			$width_query = $this->config_model->get_width();
			$width_result = array();
			$i = 0;
			foreach($width_query as $value) {
				$width_result[$i] = $value;
				$i++;
			}
			
			$data['thickness_result'] = $thickness_result;
			$data['width_result'] = $width_result;
			
			// Navigation
			$selected = "รายละเอียด Order";
			
			$navigator = array(
				"หน้าแรก" => "/main",
				"Order" => "/order",
				$selected => "/order/order_detail/" . $this->convert->AsciiToHex($po_id) . "/" . $history
			);
			
			$data["navigation"] = build_navigation($selected, $navigator);
			
			$this->template->write_view('content', 'order_detail_view', $data);
			$this->template->render();
		}
	}
}

/* End of file order.php */
/* Location: ./system/application/controllers/order.php */