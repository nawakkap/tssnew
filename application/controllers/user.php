<?php

class User extends CI_Controller {

	private static $PERMISSION = "USER";

	function __construct()
	{
		parent::__construct();	
		
		check_permission(self::$PERMISSION);
		
		$this->load->model("user_model");
		
	}
	
	function index()
	{
		$search = $this->input->post("search");
		$search_type = $this->input->post("search_type");
		
		if (empty($search)) $search = 'first_name';
		if (empty($search_type)) $search_type = 'asc';
		
		$data['search'] = $search;
		// Reverse the search type
		$data['search_type'] = ('asc' == $search_type) ? 'desc' : 'asc';
		
		$result = $this->user_model->get_all($search, $search_type);
		$data['result'] = $result;
		
		// Navigation
		$selected = "User";

		$navigator = array(
			"หน้าแรก" => "/main",
			$selected => "/user",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'user_list_view', $data);
		$this->template->render();
	}
	
	function add_page()
	{
		$username = $this->input->post('username');
		$edit_button = $this->input->post('editButton');
		
		$data['old_username'] = $username;
		
		$edit_mode = !empty($edit_button);
		
		$data['edit_mode'] = $edit_mode;
		$data['username'] = $username;
		
		
		if ($edit_mode) 
		{
			$result = $this->user_model->get_by_id($username);
			$data['result']  = $result;
		}
		else
		{
			$data['result'] = $this->user_model->get_empty_data();
		}
		
		// Navigation
		$selected = "เพิ่มข้อมูล User";
		if ($edit_mode) {
			$selected = "แก้ไขข้อมูล User";
		}
		
		$navigator = array(
			"หน้าแรก" => "/main",
			"User" => "/user",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'user_add_view.php', $data);
		$this->template->render();
	
	}
	
	function add_method()
	{
		$old_username = $this->input->post("old_username");
		$username = $this->input->post("username");
		$password = $this->input->post("password");
		$first_name = $this->input->post("first_name");
		$last_name = $this->input->post("last_name");
		$user_type = $this->input->post("user_type");
		$permission = $this->input->post("permission");

		$this->db->trans_begin();
	
		$this->user_model->insert("ADD", $username, $password, $first_name, $last_name, $user_type, $permission);
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/user";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			$data['result'] = "เพิ่มข้อมูลเรียบร้อยแล้ว";
			$data['back_page'] = "/user";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	function edit_method()
	{
		$old_username = $this->input->post("old_username");
		$username = $this->input->post("username");
		$password = $this->input->post("password");
		$first_name = $this->input->post("first_name");
		$last_name = $this->input->post("last_name");
		$user_type = $this->input->post("user_type");
		$permission = $this->input->post("permission");
		
		$this->db->trans_begin();
		
		if ($old_username == $username) {
			$this->user_model->insert("EDIT", $username, $password, $first_name, $last_name, $user_type, $permission);
		} else {
			$this->user_model->delete($username);
			$this->user_model->insert("ADD", $username, $password, $first_name, $last_name, $user_type, $permission);
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/user";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			$data['result'] = "แก้ไขข้อมูลเรียบร้อยแล้ว";
			$data['back_page'] = "/user";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	function delete_method()
	{
		$username = $this->input->post("username");
		$this->user_model->delete($username);
		
		$this->db->trans_begin();
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถลบข้อมูลได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/user";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			$data['result'] = "ลบข้อมูลเรียบร้อยแล้ว";
			$data['back_page'] = "/user";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}
	
	function user_detail($username)
	{
		$username = $this->convert->HexToAscii($username);
		
		// Navigation
		$selected = "User Detail";

		$navigator = array(
			"หน้าแรก" => "/main",
			$selected => "/user",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'user_detail_view', $data);
		$this->template->render();
	}
	
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */