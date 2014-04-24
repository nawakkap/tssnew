<?php

class Product extends CI_Controller {

	private static $PERMISSION = "PRODUCT";

	function __construct()
	{
		parent::__construct();
		
		check_permission(self::$PERMISSION);

		$this->load->model('config_model');
		$this->load->model("product_model");
		$this->load->model('film_model');
		$this->load->model('coil_film_product_model', 'cfp_model');
		$this->load->model('group_model');
		$this->load->model('product_produce_model', 'pp_model');

	}

	function index()
	{
		$filter = $this->input->post("filter");
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		
		if (empty($sort_column) && empty($sort_by)) 
		{
			$sort_column = $this->session->userdata("product_sort_column");
			$sort_by = $this->session->userdata("product_sort_by");
			
			if (empty($sort_column)) {
				$sort_column = "product_display_id";
			}
			if (empty($sort_by)) {
				$sort_by = "asc";
			}
		}
		
		$data["sort_column"] = $sort_column;
		$data["sort_by"] = ("asc" == $sort_by) ? "desc" : "asc";
	
		if ($filter=== FALSE)
		{
			$filter = "Y";
		}
	
		
		$result = $this->product_model->get_all($sort_column, $sort_by, $filter);
		$data['result'] = $result;
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " is in the product page.");

		// Navigation
		$selected = "Product";

		$navigator = array(
			"หน้าแรก" => "/main",
			$selected => "/product"
		);
		
		$data["filter"] = $filter;
		$data["navigation"] = build_navigation($selected, $navigator);

		$this->template->write_view('content', 'product_list_view', $data);
		$this->template->render();
	}

	function add_page()
	{
		$product_dtl_id = $this->input->post('product_dtl_id');
		$edit_button = $this->input->post('editButton');

		$edit_mode = !empty($edit_button);
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " is in the product add page with " . (($edit_mode) ? "EDIT mode" : "ADD mode"));

		$data['edit_mode'] = $edit_mode;
		$data['product_dtl_id'] = ($product_dtl_id) ? $product_dtl_id : utime();

		$thickness_query = $this->config_model->get_thickness();

		$thickness_result = array();
		$i = 0;
		foreach($thickness_query as $value) {
			$thickness_result[$i] = $value;
			$i++;
		}
		$data['thickness_result'] = $thickness_result;

		if ($edit_mode === TRUE)
		{
			$query = $this->product_model->get_by_id($product_dtl_id);
			$data['product_result'] = $query;
			
			//$username = $this->session->userdata("USERNAME");
			user_log_message("INFO",  $username . " want to edit the product with product id is " . $product_dtl_id);
		}
		else
		{
			$data['product_result'] = $this->product_model->get_empty_data();
			
			user_log_message("INFO",  $username . " want to add new product");
		}
		
		
		// Navigation
		$selected = "เพิ่มข้อมูล Product";
		if ($edit_mode) {
			$selected = "แก้ไขข้อมูล Product";
		}

		$navigator = array(
			"หน้าแรก" => "/main",
			"Product" => "/product",
			$selected => ""
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);

		$this->template->write_view('content', 'product_add_view', $data);
		$this->template->render();
	}

	function add_method()
	{
		$product_dtl_id =  $this->input->post("product_dtl_id");
		$product_display_id = $this->input->post("production_code");
		$product_name_th =  $this->input->post("product_name_th");
		$product_name_en =  $this->input->post("product_name_en");
		$product_name_initial = $this->input->post("product_name_initial");
		$color =  $this->input->post("color");
		$thickness =  $this->input->post("thickness");
		$thickness_rep =  $this->input->post("thickness_rep");
		$thickness_min =  $this->input->post("thickness_min");
		$film_size =  $this->input->post("film_size");
		$est_weight =  $this->input->post("est_weight");
		$size_detail = $this->input->post("size_detail");
		$est_weight_min =  $this->input->post("est_weight_min");
		$actual_weight =  $this->input->post("actual_weight");
		$accounting_weight =  $this->input->post("accounting_weight");
		$weight_display =  $this->input->post("weight_display");
		$piece_per_pack = $this->input->post("piece_per_pack");
		$piece_per_truck = $this->input->post("piece_per_truck");
		$wage_per_kilo =  $this->input->post("wage_per_kilo");
		$perct_of_films = $this->input->post("perct_of_films");
		$in_production = $this->input->post("in_production");
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		$this->db->trans_begin();
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call add method in product");

		$this->product_model->insert("ADD", $product_dtl_id, $product_display_id, $product_name_th, $product_name_en, $color, $thickness, $thickness_rep, $thickness_min, 
							$film_size, $size_detail, $est_weight, $est_weight_min, $actual_weight, $accounting_weight, $weight_display, $piece_per_pack,
							$piece_per_truck, $wage_per_kilo, $perct_of_films, $in_production, $record_change_by, $record_change_date, $product_name_initial) ;
							
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			user_log_message("ERROR",  $data["result"]);
			
			$this->db->trans_rollback();
			$data['back_page'] = "/product";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$this->db->trans_commit();
			
			user_log_message("INFO",  $username . " add new product [". $product_dtl_id . "]");
			
			$data['result'] = 'เพิ่มข้อมูลเรียบร้อยแล้ว';
			$data['back_page'] = "/product";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}

	function edit_method()
	{
		$product_dtl_id =  $this->input->post("product_dtl_id");
		$product_display_id = $this->input->post("production_code");
		$product_name_th =  $this->input->post("product_name_th");
		$product_name_en =  $this->input->post("product_name_en");
		$product_name_initial = $this->input->post("product_name_initial");
		$color =  $this->input->post("color");
		$thickness =  $this->input->post("thickness");
		$thickness_rep =  $this->input->post("thickness_rep");
		$thickness_min =  $this->input->post("thickness_min");
		$film_size =  $this->input->post("film_size");
		$est_weight =  $this->input->post("est_weight");
		$size_detail = $this->input->post("size_detail");
		$est_weight_min =  $this->input->post("est_weight_min");
		$actual_weight =  $this->input->post("actual_weight");
		$accounting_weight =  $this->input->post("accounting_weight");
		$weight_display =  $this->input->post("weight_display");
		$piece_per_pack = $this->input->post("piece_per_pack");
		$piece_per_truck = $this->input->post("piece_per_truck");
		$wage_per_kilo =  $this->input->post("wage_per_kilo");
		$perct_of_films = $this->input->post("perct_of_films");
		$in_production = $this->input->post("in_production");
		$record_change_by = $this->session->userdata("USERNAME");
		$record_change_date = date_to_mysqldatetime();
		
		$this->db->trans_begin();
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call edit method in product");

		$this->product_model->insert("EDIT", $product_dtl_id, $product_display_id, $product_name_th, $product_name_en, $color, $thickness, $thickness_rep, $thickness_min, 
							$film_size, $size_detail, $est_weight, $est_weight_min, $actual_weight, $accounting_weight, $weight_display, $piece_per_pack,
							$piece_per_truck, $wage_per_kilo, $perct_of_films,  $in_production, $record_change_by, $record_change_date, $product_name_initial) ;
							
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			user_log_message("INFO",  $data["result"]);
			
			$this->db->trans_rollback();
			$data['back_page'] = "/product";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$data['result'] = 'แก้ไขข้อมูลเรียบร้อยแล้ว';
			
			user_log_message("INFO",  $username . " edit product = " . $product_dtl_id);
			
			$data['back_page'] = "/product";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}

	function delete_method()
	{
		$product_dtl_id = $this->input->post("product_dtl_id");
		
		$this->db->trans_begin();

		$this->product_model->delete($product_dtl_id);
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call delete method in product");
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถลบได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			
			user_log_message("INFO",  $data["result"]);
			
			$this->db->trans_rollback();
			$data['back_page'] = "/product";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$data['result'] = 'ลบข้อมูลเรียบร้อยแล้ว';
			
			user_log_message("INFO",  $username . " deletes product = " . $product_dtl_id);
			
			$data['back_page'] = "/product";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
	}

	function product_detail($product_dtl_id)
	{
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call product detail with product id = " . $product_dtl_id);
		
		// $coil_group_code = $this->pp_model->get_coil_group_code_by_product_id($product_dtl_id);
		
		$product_result = $this->product_model->get_by_id($product_dtl_id);
		$data['product_result'] = $product_result;
		
		$data['group_status'] = $this->config_model->get_group_status();
		
		// $data['unit'] = $this->product_model->get_unit_by_product_id($product_dtl_id);
		
		/*
		$group_result = array();
		for($i = 0; $i < count($coil_group_code); $i++) 
		{
			$group_result[] = $this->group_model->get_group_by_group_code_and_product_id($coil_group_code[$i], $product_dtl_id);
		}
		$data['group_result'] = $group_result;
		*/
		// Navigation
		$selected = "รายละเอียด Product";

		$navigator = array(
			"หน้าแรก" => "/main",
			"Product" => "/product",
			$selected => "/product/product_detail/" . $product_dtl_id
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);

		$this->template->write_view('content', 'product_detail_view', $data);
		$this->template->render();
	}
	
	public function product_group_detail($product_dtl_id)
	{
		$group_status = $this->config_model->get_group_status();
	
		$coil_group_code = $this->pp_model->get_coil_group_code_by_product_id($product_dtl_id);
		
		$group_result = array();
		for($i = 0; $i < count($coil_group_code); $i++) 
		{
			$group_result[] = $this->group_model->get_group_by_group_code_and_product_id($coil_group_code[$i], $product_dtl_id);
		}
		$data['group_result'] = $group_result;
		
		$str = "";
		for($i = 0; $i < count($group_result); $i++) {
			$str .= '<tr class="' . ($i % 2) ? "odd" : "even" . '">';
			$str .= '	<td><a href="' . site_url("/group/group_detail_by_product/" . $this->convert->AsciiToHex($group_result[$i]['coil_group_code']) . "/" . $group_result[$i]['product_dtl_id']) . '" class="link">' . $group_result[$i]['coil_group_code'] . '</a></td>';
			$str .= '	<td>' . number_format($group_result[$i]['thickness'], 2) . '</td>';
			$str .= '	<td>' . number_format($group_result[$i]['weight'], 0) . '</td>';
			$str .= '	<td>' . number_format($group_result[$i]['cost_price'], 2) . '</td>';
			$str .= '	<td>' . $group_status[$group_result[$i]['populate_flag']] . '</td>';
			$str .= '</tr>\n';
		}
		
		$this->output->set_output($str);
	}
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */