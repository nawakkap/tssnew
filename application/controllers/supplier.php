<?php

class Supplier extends CI_Controller {
	
	private static $PERMISSION = "SUPPLIER";

	function __construct()
	{
		parent::__construct();	
		
		check_permission(self::$PERMISSION);
		
		$this->load->model("supplier_model");
		$this->load->model("order_model");
		$this->load->model('slit_model');
		$this->load->model('config_model');
		
	}
	
	function index()
	{
		$search = $this->input->post("search");
		$search_type = $this->input->post("search_type");
		
		if (empty($search)) $search = 'supplier_name';
		if (empty($search_type)) $search_type = 'asc';
		
		$data['search'] = $search;
		// Reverse the search type
		$data['search_type'] = ('asc' == $search_type) ? 'desc' : 'asc';
	
		$data['result'] = $this->supplier_model->get_all($search, $search_type);
		$data['payment_result'] = $this->config_model->get_payment_term();
		
		// Navigation
		$selected = "Supplier";
		
		$navigator = array(
			"หน้าแรก" => "/main",
			$selected => "/supplier",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		
		$this->template->write_view('content', 'supplier_list_view', $data);
		$this->template->render();
	}
	
	function add_page() 
	{
		$supplier_id = $this->input->post('supplier_id');
		$edit_button = $this->input->post('editButton');
		
		$edit_mode = !empty($edit_button);
		
		$result = $this->supplier_model->get_by_id($supplier_id);
		if ($result === FALSE) 
		{
			$result = $this->supplier_model->get_empty_data();
		}
		$data['result'] = $result;
		$data['edit_mode'] = $edit_mode;
		$data['supplier_id'] = ($supplier_id) ? $supplier_id : utime();
		$data['register_date'] = date('d/m/Y');
		
		$data['payment_result'] = $this->config_model->get_payment_term();
		
		// Navigation
		$selected = "เพิ่มข้อมูล Supplier";
		if ($edit_mode) {
			$selected = "แก้ไขข้อมูล Supplier";
		}
		
		$navigator = array(
			"หน้าแรก" => "/main",
			"supplier" => "/supplier",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
	
		$this->template->write_view('content', 'supplier_add_view.php', $data);
		$this->template->render();
	}
	
	function add_method()
	{	
		$supplier_id = $this->input->post("supplier_id");
		$supplier_name = $this->input->post("supplier_name");
		$default_payment_term = $this->input->post("default_payment_term");
		$address_1 = $this->input->post("address_1");
		$address_2 = $this->input->post("address_2");
		$address_postcode = $this->input->post("address_postcode");
		$tel_phone = $this->input->post("tel_phone");
		$tel_mobile = $this->input->post("tel_mobile");
		$email = $this->input->post("email");
		$register_date = date_to_mysqldatetime($this->input->post("register_date"));
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		$this->db->trans_begin();
		
		$this->supplier_model->insert("ADD", $supplier_id, $supplier_name, $default_payment_term, $address_1, $address_2, $address_postcode,
							$tel_phone, $tel_mobile, $email, $register_date, $record_change_by, $record_change_date);
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/supplier";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			$data['result'] = "เพิ่มข้อมูลเรียบร้อยแล้ว";
			$data['back_page'] = "/supplier";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	function edit_method() 
	{
		$supplier_id = $this->input->post("supplier_id");
		$supplier_name = $this->input->post("supplier_name");
		$default_payment_term = $this->input->post("default_payment_term");
		$address_1 = $this->input->post("address_1");
		$address_2 = $this->input->post("address_2");
		$address_postcode = $this->input->post("address_postcode");
		$tel_phone = $this->input->post("tel_phone");
		$tel_mobile = $this->input->post("tel_mobile");
		$email = $this->input->post("email");
		$register_date = date_to_mysqldatetime($this->input->post("register_date"));
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		$this->db->trans_begin();
		
		$this->supplier_model->insert("EDIT", $supplier_id, $supplier_name, $default_payment_term, $address_1, $address_2, $address_postcode,
							$tel_phone, $tel_mobile, $email, $register_date, $record_change_by, $record_change_date);
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/supplier";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			$data['result'] = "แก้ไขข้อมูลเรียบร้อยแล้ว";
			$data['back_page'] = "/supplier";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	function delete_method()
	{
		$supplier_id = $this->input->post("supplier_id");
		
		$this->db->trans_begin();
		
		$this->supplier_model->delete($supplier_id);
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/supplier";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			$data['result'] = "ลบข้อมูลเรียบร้อยแล้ว";
			$data['back_page'] = "/supplier";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	function supplier_detail($supplier_id) 
	{
		$data['supplier_id'] = $supplier_id;
		
		$supplier_result = $this->supplier_model->get_by_id($supplier_id);
		$data['supplier_result'] = $supplier_result;
		
		$data['supplier_name'] = $supplier_result['supplier_name'];
		
		$payment_result = $this->config_model->get_payment_term();
		$data['payment_result'] = $payment_result;
		
		$order_status = $this->config_model->get_order_status();
		$data['order_status'] = $order_status;
		
		if ($this->agent->is_referral())
		{
			$data['referer'] = $this->agent->referrer();
		}
		else
		{
			$data['referer'] = "/order";
		}
		
		$data['order_result'] = $this->order_model->get_by_supplier_name($supplier_result['supplier_name']);
		
		
		// Navigation
		$selected = "Supplier Detail";

		$navigator = array(
			"หน้าแรก" => "/main",
			"Supplier" => "/supplier",
			$selected => "/supplier/supplier_detail/" . $supplier_id
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'supplier_detail_view', $data);
		$this->template->render();
	}
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */