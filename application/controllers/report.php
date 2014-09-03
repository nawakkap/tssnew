<?php
include APPPATH . '../../../excel/excel_reader2.php';

class Report extends CI_Controller {

	private static $PERMISSION = "REPORT";

	function __construct()
	{
		parent::__construct();
		
		check_permission(self::$PERMISSION);
		
		$this->load->helper("file");
		$this->load->model("product_model");
		$this->load->model("report_model");
		$this->load->model("film_model");
		$this->load->model("program_model");
		$this->load->model("slit_model");
		$this->load->model("order_model");
		$this->load->model("coil_model");
		$this->load->model("machine_model");
	}
	
	function index()
	{
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call report page.");
	
		$selected = "Report";
		$navigator = array(
			"หน้าแรก" => "/main",
			$selected => "/report",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'report_main_view', $data);
		$this->template->render();
	}
	
	function production_upload()
	{
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call report production upload [ before delete all temporary file]");
		
		delete_files('upload/');
		
		$data = array("error" => "");
		
		$filenames = get_dir_file_info('./upload/');
		//$data["filenames"] = $filenames;
		
		$file_sort =array();
		foreach($filenames as $item)
		{
			$file_sort[$item["date"]] = $item;
		}
		krsort($file_sort);
		$data["filenames"] = $file_sort;
		
		$selected = "ทำการ upload ไฟล์";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "/report/production_upload",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'report/production_upload_view', $data);
		$this->template->render();
	}
	
	public function upload()
	{
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call upload file");
		
		$config['upload_path'] = './upload/';
		$config['allowed_types'] = 'xls';
		$config['max_size']	= '0';
		$config['max_width']  = '0';
		$config['max_height']  = '0';
		$config['remove_spaces'] = TRUE;
		$config['file_name'] = mktime();
		
		$this->load->library('upload', $config);

		// ppdor
		$this->upload->initialize($config);
		
		// File
		$ppdor = FALSE;
		$xppdor = FALSE;
		
		if ( ! $this->upload->do_upload("userfile"))
		{
			$data = array('error' => $this->upload->display_errors());
			$filenames = get_dir_file_info('./upload/');
			//$data["filenames"] = $filenames;
			
			$file_sort =array();
			foreach($filenames as $item)
			{
				$file_sort[$item["date"]] = $item;
			}
			krsort($file_sort);
			$data["filenames"] = $file_sort;
			
			$selected = "ทำการ upload ไฟล์";
			$navigator = array(
				"หน้าแรก" => "/main",
				"Report" => "/report",
				$selected => "/report/production_upload",
			);
			
			$data["navigation"] = build_navigation($selected, $navigator);
			
			$username = $this->session->userdata("USERNAME");
			user_log_message("INFO",  $username . " upload error : " . $this->upload->display_errors());
			
			$this->template->write_view('content', 'report/production_upload_view', $data);
			$this->template->render();
			return;
		}
		else
		{
			$ppdor = $this->upload->data();
		
		
			// Upload XPPDOR
			$path = "./upload/xppdor/";
			
			if (!file_exists($path))
			{
				mkdir($path);
				chmod($path, DIR_WRITE_MODE);
			}

			$config['upload_path'] = $path;
			$config['allowed_types'] = '*';
			$config['max_size']	= '0';
			$config['max_width']  = '0';
			$config['max_height']  = '0';
			$config['remove_spaces'] = TRUE;
			$config['file_name'] = mktime();
			
			$this->upload->initialize($config);
			
			if ( ! $this->upload->do_upload("xppdorfile"))
			{
				$data = array('error' => $this->upload->display_errors());
				$filenames = get_dir_file_info('./upload/');
				//$data["filenames"] = $filenames;
				
				$file_sort =array();
				foreach($filenames as $item)
				{
					$file_sort[$item["date"]] = $item;
				}
				krsort($file_sort);
				$data["filenames"] = $file_sort;
				
				$selected = "ทำการ upload ไฟล์";
				$navigator = array(
					"หน้าแรก" => "/main",
					"Report" => "/report",
					$selected => "/report/production_upload",
				);
				
				$data["navigation"] = build_navigation($selected, $navigator);
				
				$username = $this->session->userdata("USERNAME");
				user_log_message("INFO",  $username . " upload error : " . $this->upload->display_errors());
				
				$this->template->write_view('content', 'report/production_upload_view', $data);
				$this->template->render();
				return;
			}
			else
			{
				// Everything Finish
				$xppdor = $this->upload->data();
				
				// Process
				$r = $this->process_production_excel($ppdor, $xppdor);
				
				// For UI
				$data = array(
					"result" => $r["message"],
					"back_page" => "/report/production_report"
				);
				
				$this->template->write_view('content', 'order_result_view', $data);
				$this->template->render();
				return;
			}
		}
	}
	
	private function process_production_excel($pstock3, $xppdor)
	{
		$result = array(
			"success" => TRUE,
			"message" => "ทำการ upload เรียบร้อยแล้ว"
		);
		
		// pstock3
		$excel = new Spreadsheet_Excel_Reader($pstock3["full_path"]);
		// $excel->read($pstock3["full_path"]);
		
		$prodcode_column = FALSE;
		$wsendqty_column = FALSE;
		$totalqty_column = FALSE;
		$qty_column = FALSE;
		$numCols = $excel->colcount();
		for($i = 1; $i <= $numCols ; $i++)
		{
			// Find column name [prodcode]
			
			if ($excel->val(1, $i) == "prodcode")
			{
				$prodcode_column = $i;
			}
		
			// Find column name [wsendqty]
			if ($excel->val(1, $i) == "wsendqty")
			{
				$wsendqty_column = $i;
			}
			
			// Find column name [totalqty]
			if ($excel->val(1, $i) == "totalqty")
			{
				$totalqty_column = $i;
			}
			
			// Find column name [totalqty]
			if ($excel->val(1, $i) == "qty")
			{
				$qty_column = $i;
			}
			
		}
		
		
		$wsendqty_map = array();
		$totalqty_map = array();
		$qty_map = array();
		
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " get all data in excel");
	
		$row = $excel->rowcount();
		for($i = 2 ; $i < $row; $i++)
		{
			if ($prodcode_column !== FALSE)
			{
				
				$prodcode = $excel->val($i,$prodcode_column);
				if (!empty($prodcode))
				{
					if ($wsendqty_column !== FALSE)
					{
						
						$wsendqty = $excel->val($i, $wsendqty_column);
						$wsendqty_map[$prodcode] = $wsendqty;
					}
					
					if ($totalqty_column !== FALSE)
					{
						$totalqty = $excel->val($i, $totalqty_column);
						$totalqty_map[$prodcode] = $totalqty;
					}
					
					if ($qty_column !== FALSE)
					{
						$qty = $excel->val($i, $qty_column);
						$qty_map[$prodcode] = $qty;
					}
				}
			}
		}
		
		// print_r($totalqty_map);
		
		// XPPDOR
		$excel = new Spreadsheet_Excel_Reader($xppdor["full_path"]);
		// $excel->setOutputEncoding("utf-8");
		// $excel->setUTFEncoder("mb");
		// $excel->read($xppdor["full_path"]);
		
		$orderdate_column = FALSE;
		$sono_column = FALSE;
		$custname_column = FALSE;
		$order_column = FALSE;
		$orderbal_column= FALSE;
		$numCols = $excel->colcount();
		
		
		for($i = 1; $i <= $numCols ; $i++)
		{
			// echo $excel->sheets[0]['cells'][1][$i] . "\n";
			// Find column name [recedate]
			if ($excel->val(1, $i) == "recedate")
			{
				$orderdate_column = $i;
			}
		
			// Find column name [code]
			if ($excel->val(1, $i) == "code")
			{
				$sono_column = $i;
			}
			
			// Find column name [coorname]
			if ($excel->val(1, $i) == "coorname")
			{
				$custname_column = $i;
			}
			
			// Find column name [orderqty]
			if ($excel->val(1, $i) == "orderqty")
			{
				$order_column = $i;
			}
			
			// Find column name [backqty]
			if ($excel->val(1, $i) == "backqty")
			{
				$orderbal_column = $i;
			}
		}

		$product_dtl_id_column = 11;
		$row = $excel->rowcount();
		
		$this->load->model("product_model");
		$product_display_id = $this->product_model->get_all_product_display_id();
		
		// Get Send Date
		// echo $excel->sheets[0]['cells'][5][11];
		$raw_data = $excel->val(5, 11);
		$split = explode(" ", $raw_data);
		$sdate = explode("/", $split[1]);
		$edate = explode("/", $split[3]);
		
		// Convert to B.E. and back to G.E. (eg. 55 --> 2555 --> 2012)
		$sdate[2] = (2500 + $sdate[2]) - 543;
		$edate[2] = (2500 + $edate[2]) - 543;
		
		
		// Hour, Minute, Second, Month, Date, Year
		$start_date = date("Y-m-d", mktime(0, 0, 0, $sdate[1], $sdate[0], $sdate[2]));
		$end_date = date("Y-m-d", mktime(0, 0, 0, $edate[1], $edate[0], $edate[2]));
		
		
		$k = FALSE;
		$isFound = FALSE;
		
		// Expected Row Start At 10
		$xppdor_map = array(); // Clear Data
		for($i = 10; $i < $row; $i++)
		{
			// $k_val = $excel->sheets[0]['cells'][$i][$product_dtl_id_column];
			$k_val = $excel->val($i, $product_dtl_id_column);
			$k_items = explode(" ", $k_val);
			//echo $i . " = " . $k_val . "<br/>";
			
			if (in_array($k_items[0], $product_display_id))
			{
				// Have Product
				if ($k != $k_items[0])
				{
					$k = $k_items[0];
					$isFound = TRUE;
				}
				else
				{
					$isFound = FALSE;
				}
			}
			else
			{
				$isFound = FALSE;
			}
			
			//echo $k . ", " . ($isFound == FALSE) . "<br/>";
			// New Product
			$product_dtl_id = $k;
			if ($k && $isFound)
			{
				// Read Data
				$xppdor_map[$product_dtl_id] = array();
			}
			// 
			else if ($k && !$isFound)
			{
				//echo "TEST1<br/>";
				// Read SO				
				// echo mb_detect_encoding($excel->val($i, $custname_column), "auto"). "=" . $excel->val($i, $custname_column) . "<br/>";
				$temp = array(
					$excel->val($i, $orderdate_column), // Order Date
					$excel->val($i, $sono_column), // SONO
					$excel->val($i, $custname_column), // Customer Name
					$excel->val($i, $order_column), // Order
					$excel->val($i, $orderbal_column) // Order Balance
				);
				
				//print_r($temp);
				
				// echo mb_detect_encoding($excel->val($i, $custname_column)) . "<Br/>";
				
				// Check against pstock3
				$wsendqty = (isset($wsendqty_map[$product_dtl_id])) ? $wsendqty_map[$product_dtl_id] : FALSE;
				//echo $wsendqty . "<br/>";
				if ($wsendqty)
				{
					// Check with order balance
					if ($this->config->item("check_balance"))
					{
						if ($wsendqty !== $temp[4])
						{
							// Error
							$result["success"] = FALSE;
							$result["message"] = "Quantity are not match. [" . $product_dtl_id . "]";
							break;
						}
					}
				}
				$xppdor_map[$product_dtl_id][] = $temp;
				
				//print_r($xppdor_map);
			}
		}
		
		// print_r($xppdor_map);
		
		// Import to database.
		
		// pstock3
		$product_result = $this->product_model->get_all();
		$this->db->trans_begin();
		$this->report_model->clear($start_date, $end_date);
		foreach($product_result as $item)
		{
			$product_display_id = $item->product_display_id;
			
			$delivery = 0;
			if (isset($wsendqty_map[$product_display_id]))
			{
				$delivery = $wsendqty_map[$product_display_id];
			}
			
			$backlog = 0;
			if (isset($totalqty_map[$product_display_id]))
			{
				$backlog = $totalqty_map[$product_display_id];
			}
			
			$inventory = 0;
			if (isset($qty_map[$product_display_id]))
			{
				$inventory = $qty_map[$product_display_id];
			}
			
			$this->report_model->insert("ADD", $product_display_id, $delivery, $backlog, $inventory);
		}
		
		$sono_include = array();
		
		// xppdor
		$this->load->model("customer_model");
		foreach($xppdor_map as $product_dtl_id => $val)
		{
			$db_start_date = $start_date;
			$db_end_date = $end_date;
			for($i = 0; $i < count($val); $i++)
			{
				// print_r($val[$i][0]);
			
				$sdate = explode("/", $val[$i][0]);
				if (count($sdate) > 1) // Exclude " - -"
				{
					$order_date = date("Y-m-d", mktime(0, 0, 0, $sdate[0], $sdate[1], $sdate[2]));
					
					$sono = $val[$i][1];
					$custname = $val[$i][2];
					$order = $val[$i][3];
					$order_bal = $val[$i][4];
					
					$sono_include[] = $sono;
					
					$this->customer_model->add($custname);
		
					$this->report_model->insert_xppdor($db_start_date, $db_end_date, $order_date, $custname, $product_dtl_id, $sono, $order, $order_bal);
				}
			}
		}
		
		// Delete other sono
		$this->db->where_not_in($sono_include);
		$this->db->delete("imp_rpt_production_priority");
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
			
			$result["message"] = 'เกิดปัญหาขึ้นระหว่างการ upload หรือ ใส่ข้อมูลลง database<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$result["success"] = FALSE;
		}
		else
		{
			$this->db->trans_commit();
			
			$result["message"] = "ทำการ upload และ เก็บข้อมูลลง database เรียบร้อยแล้ว";
			$result["success"] = TRUE;
		}
		
		return $result;
	}
	
	/*
	function upload()
	{
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call upload file");
	
		$config['upload_path'] = './upload/';
		$config['allowed_types'] = 'xls';
		$config['max_size']	= '0';
		$config['max_width']  = '0';
		$config['max_height']  = '0';
		$config['remove_spaces'] = TRUE;
		$config['file_name'] = mktime();
		
		$this->load->library('upload', $config);
		
		if ( ! $this->upload->do_upload("userfile"))
		{
			$data = array('error' => $this->upload->display_errors());
			
			$filenames = get_dir_file_info('./upload/');
			//$data["filenames"] = $filenames;
			
			$file_sort =array();
			foreach($filenames as $item)
			{
				$file_sort[$item["date"]] = $item;
			}
			krsort($file_sort);
			$data["filenames"] = $file_sort;
			
			$selected = "ทำการ upload ไฟล์";
			$navigator = array(
				"หน้าแรก" => "/main",
				"Report" => "/report",
				$selected => "/report/production_upload",
			);
			
			$data["navigation"] = build_navigation($selected, $navigator);
			
			$username = $this->session->userdata("USERNAME");
			user_log_message("INFO",  $username . " upload error : " . $this->upload->display_errors());
			
			$this->template->write_view('content', 'report/production_upload_view', $data);
			$this->template->render();
		}	
		else
		{
			$excel_data = $this->upload->data();
			//print_r($excel_data);
			$excel = new Spreadsheet_Excel_Reader($excel_data["full_path"]);
			
			$prodcode_column = FALSE;
			$wsendqty_column = FALSE;
			$totalqty_column = FALSE;
			for($i = 'A'; $i <= 'Z' ; $i++)
			{
				
				// Find column name [prodcode]
				if ($excel->val(1, $i) == "prodcode")
				{
					$prodcode_column = $i;
				}
			
				// Find column name [wsendqty]
				if ($excel->val(1, $i) == "wsendqty")
				{
					$wsendqty_column = $i;
				}
				
				// Find column name [totalqty]
				if ($excel->val(1, $i) == "totalqty")
				{
					$totalqty_column = $i;
				}
				
			}
			
			
			$wsendqty_map = array();
			$totalqty_map = array();
			
			$username = $this->session->userdata("USERNAME");
			user_log_message("INFO",  $username . " get all data in excel");
		
			$row = $excel->rowcount(0);
			for($i = 2 ; $i < $row; $i++)
			{
				if ($prodcode_column !== FALSE)
				{
					$prodcode = $excel->val($i, $prodcode_column);
					if (!empty($prodcode))
					{
						if ($wsendqty_column !== FALSE)
						{
							$wsendqty = $excel->val($i , $wsendqty_column);
							$wsendqty_map[$prodcode] = $wsendqty;
						}
						
						if ($totalqty_column !== FALSE)
						{
							$totalqty = $excel->val($i, $totalqty_column);
							$totalqty_map[$prodcode] = $totalqty;
						}
					}
				}
			}
			
			$product_result = $this->product_model->get_all();
			
			$this->db->trans_begin();
			
			$this->report_model->clear();
			
			foreach($product_result as $item)
			{
				$product_display_id = $item->product_display_id;
				
				$delivery = 0;
				if (isset($wsendqty_map[$product_display_id]))
				{
					$delivery = $wsendqty_map[$product_display_id];
				}
				
				$backlog = 0;
				if (isset($totalqty_map[$product_display_id]))
				{
					$backlog = $totalqty_map[$product_display_id];
				}
				
				$this->report_model->insert("ADD", $product_display_id, $delivery, $backlog);
			}
			
			if ($this->db->trans_status() === FALSE)
			{
				$this->db->trans_rollback();
				
				$data['result'] = 'เกิดปัญหาขึ้นระหว่างการ upload หรือ ใส่ข้อมูลลง database<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
				$data['back_page'] = "/report";
				$this->template->write_view('content', 'order_result_view', $data);
				$this->template->render();
			}
			else
			{
				$this->db->trans_commit();
				
				$data['result'] = "ทำการ upload และ เก็บข้อมูลลง database เรียบร้อยแล้ว";
				$data['back_page'] = "/report";
				$this->template->write_view('content', 'order_result_view', $data);
				$this->template->render();
			}
		}
	}
	*/
	
	function production_read_file($file_path)
	{
		$path_to_file = $this->convert->HexToAscii($file_path);
		
		$data['upload_data']['full_path'] = $path_to_file;
		
		$this->production_report($data);
	}
	
	function production_report()
	{
		// Read Date
		// xppdor
		$filenames = get_dir_file_info('./upload/');
		$max_date = FALSE;
		foreach($filenames as $item)
		{
			if ($max_date === FALSE)
			{
				$max_date = $item["date"];
			}
			else
			{
				if ($max_date < $item["date"])
				{
					$max_date = $item["date"];
				}
			}
		}
		$data["max_date"] = unix_to_human($max_date);
		
		$filenames = get_dir_file_info('./upload/xppdor/');
		$max_date = FALSE;
		if ($filenames)
		{
			foreach($filenames as $item)
			{
				if ($max_date === FALSE)
				{
					$max_date = $item["date"];
				}
				else
				{
					if ($max_date < $item["date"])
					{
						$max_date = $item["date"];
					}
				}
			}
		}
		$data["xppdor_max_date"] = unix_to_human($max_date);
	
		$report_result = $this->report_model->get_vr_report();
		$data["report_result"] = $report_result;
		
		$film_percent = array();
		$program_ext = array();
		foreach($report_result as $item)
		{
			//$film_percent[$item["product_display_id"]] = $this->slit_model->get_sum_percent_film_by_product_id_and_thickness($item["product_dtl_id"], $item["thickness_rep"]);
			$program_ext[$item["product_dtl_id"]] =  $this->program_model->get_program_code_and_ext_by_product_dtl_id_with_normal_status($item["product_dtl_id"]);
		}
		//$data["film_percent"] = $film_percent;
		//print_r($program_ext);
		$data["program_ext"] = $program_ext;
	
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " print report to view");
		
		$selected = "Production report";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'report/production_report_view', $data);
		$this->template->render();
	}
	
	function slit_page($product_dtl_id = FALSE)
	{
		if ($product_dtl_id)
		{
			$this->load->model("slit_model");
			$slit_temp = $this->slit_model->get_by_product_dtl_id($product_dtl_id);
			
			if ($slit_temp === FALSE)
			{
				$slit_temp = array();
			}
			
			
			echo '<table border="1" width="100%" cellpadding="5" cellspacing="3" class="table-data ui-widget">';
			// echo '	<thead>';
			// echo '		<tr class="ui-widget-header">';
			// echo '			<th width="5%">&nbsp;</th>';
			// echo '			<th>&nbsp;&nbsp;Slit Spec</th>';
			// echo '		</tr>';
			// echo '	</thead>';
			echo '	<tbody>';
			for($i = 0; $i < count($slit_temp); $i++)
			{
			echo '		<tr>';
			echo '			<td align="center"><input type="radio" name="slit_spec_id" class="slit_spec_id" value="' . $slit_temp[$i]['product_ratio'] . '"/></td>';
			echo '			<td align="left" colspan="2">&nbsp;&nbsp;' . $slit_temp[$i]['slit_thickness'] . '&nbsp;&nbsp;&nbsp;&nbsp;' . $slit_temp[$i]['remark'] . '</td>';
			echo '		</tr>';
			}		
			echo '	</tbody>';
			echo '</table>';
		
			
		}
		else
		{
			echo "Nothing selected.";
		}
	}
	
	function finishgood_detail_report()
	{
		// Read Date
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		
		if (empty($sort_column)) {
			$sort_column = "sortDate";
		}
		if (empty($sort_by)) {
			$sort_by = "desc";
		}		
	
		$report_result = $this->report_model->get_vr_finishgood_detail_report($sort_column, $sort_by);
		$data["report_result"] = $report_result;
		$data["sort_column"] = $sort_column;
		$data["sort_by"] = ("asc" == $sort_by) ? "desc" : "asc";
	
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " print report to view");
		
		$selected = "Finished goods detail report";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'report/finishgood_detail_report_view', $data);
		$this->template->render();
	}
	
	function finishgood_report()
	{
		// Read Date

	
		$report_result = $this->report_model->get_vr_finishgood_report();
		$data["report_result"] = $report_result;
	
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " print report to view");
		
		$selected = "Finished good summary report";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'report/finishgood_report_view', $data);
		$this->template->render();
	}
	
	function coil_received_report()
	{
		// Read Date
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		
		if (empty($sort_column)) {
			$sort_column = "sortReceivedDate";
		}
		if (empty($sort_by)) {
			$sort_by = "desc";
		}
	
		$report_result = $this->report_model->get_vr_coil_received_report($sort_column, $sort_by);
		$data["report_result"] = $report_result;
		$data["sort_column"] = $sort_column;
		$data["sort_by"] = ("asc" == $sort_by) ? "desc" : "asc";
	
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " print report to view");
		
		$selected = "Coil Received report";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'report/coilreceived_report_view', $data);
		$this->template->render();
	}
	
	function slit_report()
	{
		// Read Date
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		
		if (empty($sort_column)) {
			$sort_column = "sortSlitDate";
		}
		if (empty($sort_by)) {
			$sort_by = "desc";
		}
		
		$machine_list = $this->machine_model->get_slitter();
		$machine = array();
		$machine[] = "";
		for($i = 0; $i < count($machine_list); $i++)
		{
			$machine[$machine_list[$i]["mc_id"]] = $machine_list[$i]["machine_name"];
		}
		$data["machine"] = $machine;
	
		$report_result = $this->report_model->get_vr_slit_report($sort_column, $sort_by);
		$data["report_result"] = $report_result;
		$data["sort_column"] = $sort_column;
		$data["sort_by"] = ("asc" == $sort_by) ? "desc" : "asc";
	
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " print report to view");
		
		$selected = "Slit report";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'report/slit_report_view', $data);
		$this->template->render();
	}
	
	function delivery_report()
	{
		// Read Date
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		
		if (empty($sort_column)) {
			$sort_column = "custname";
		}
		if (empty($sort_by)) {
			$sort_by = "desc";
		}
	
		$report_result = $this->report_model->get_vr_delivery($sort_column, $sort_by);
		$data["report_result"] = $report_result;
		$data["sort_column"] = $sort_column;
		$data["sort_by"] = ("asc" == $sort_by) ? "desc" : "asc";
	
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " print report to view");
		
		$selected = "Delivery report";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'report/delivery_report_view', $data);
		$this->template->render();
	}
	
	function slit_report_by_date()
	{
		// Read Date
		$sort_column = $this->input->post("sort_column");
		$sort_by = $this->input->post("sort_by");
		
		if (empty($sort_column)) {
			$sort_column = "sortSlitDate";
		}
		if (empty($sort_by)) {
			$sort_by = "desc";
		}
	
		$report_result = $this->report_model->get_vr_slit_report_by_date($sort_column, $sort_by);
		$data["report_result"] = $report_result;
		$data["sort_column"] = $sort_column;
		$data["sort_by"] = ("asc" == $sort_by) ? "desc" : "asc";
	
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " print report to view");
		
		$selected = "Slit report by date";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'report/slit_report_by_date_view', $data);
		$this->template->render();
	}
	
	function save_slitted_coil_report()
	{
		$report_result = $this->report_model->get_vr_slit_report();
		
		$this->db->trans_begin();
		
		$i = 0;
		foreach($report_result as $item)
		{
			
			$coil_group_code = $item["lot"];
			
			$slittedCoil = (int)$this->input->post("slittedCoil_" . $i);
			$slittedWeight = (int)$this->input->post("slittedWeight_" . $i);
			$pastSlittedCoil = (int)$this->input->post("pastSlittedCoil_" . $i);
			$machine = (int)$this->input->post("machine_" . $i);
			$currentDate = $this->input->post("currentDate");
			
			//echo "aaa-->".$maxDate."bb-->".$item["currentDate"];
			if ($slittedCoil>0){
				if($currentDate!= $item["maxDate"]){
					$this->report_model->insert_actual_slitted($currentDate, $coil_group_code, $slittedCoil, $slittedWeight, $machine);								
				}else{
					$this->report_model->insert_actual_slitted($currentDate, $coil_group_code, $slittedCoil, $slittedWeight, $machine);	
				}	
			}
			
			
			$i++;
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else
		{
			$this->db->trans_commit();
		}
		
		redirect("/report/slit_report");
	}
	
	function delete_all_file()
	{
		delete_files('upload/');
		redirect("/report/production_upload");
	}	
	
	function save_production_report() 
	{
		$report_result = $this->report_model->get_vr_report();
		
		$this->db->trans_begin();
		foreach($report_result as $item)
		{
			$product_display_id = $item["product_display_id"];
			$product_dtl_id = $item["product_dtl_id"];
			
			$stock = $this->input->post("stock_" . $product_dtl_id);
			$total_kg_need = $this->input->post("total_kg_need_" . $product_dtl_id);
			$total_kg_need = -1*$total_kg_need;
			
			$this->report_model->update_stock_kg_need($product_display_id, $stock, $total_kg_need);
			
		}
		
		if ($this->db->trans_status() === FALSE)
		{
			$this->db->trans_rollback();
		}
		else
		{
			$this->db->trans_commit();
		}
		
		redirect("/report/overall_stock");
		
	}
	
	function overall_stock()
	{
	
		$report = $this->report_model->get_vr_overall_stock_report();
		$data["report"] = $report;
		//print_r($report);
		
		$weight = array();
		$coil_weight = array();
		foreach($report as $item)
		{
			$thickness = $item["thickness_rep"];
			
			$sum_weight = $this->order_model->get_sum_weight_by_thickness((double)$thickness);
			
			if ($sum_weight === FALSE || empty($sum_weight))
			{
				$sum_weight = 0;
			}
			$weight[$thickness] = $sum_weight;
			
			$sum_coil_weight = $this->coil_model->get_sum_coil_weight_by_thickness((double)$thickness);
			if ($sum_coil_weight === FALSE || empty($sum_coil_weight))
			{
				$sum_coil_weight = 0;
			}
			$coil_weight[$thickness] = $sum_coil_weight;
			
		}
		$data["weight"] = $weight;
		$data["coil_weight"] = $coil_weight;
	
	
		$selected = "Overall report";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'report/overall_stock_report', $data);
		$this->template->render();
	}
	
	function diary_produce()
	{
		$report = $this->report_model->get_diary_produce();
		$data["report_result"] = $report;
		
		$product = $this->product_model->get_all();
		$product_result = array();
		foreach($product as $item)
		{
			$product_result[$item->product_dtl_id] = $item->product_name_th;
		}
		$data["product_result"] = $product_result;
		
	
	
		$selected = "รายงานการผลิตประจำวัน";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
				
		$this->template->write_view('content', 'report/diary_produce', $data);
		$this->template->render();
	}
	
	function performance_report()
	{
		$startDate = $this->input->post("startDate");
		$endDate = $this->input->post("endDate");
		
		if ($startDate === FALSE) {
			$startDate = date('Y-m-d', strtotime("-1 day"));
		}
		
		if ($endDate === FALSE) {
			$endDate = date('Y-m-d', strtotime("-1 day"));
		}
		
		$dateDiff = $this->DateDiff($startDate, $endDate);
		
		$data = array();
		$data["startDate"] = $startDate;
		$data["endDate"] = $endDate;
		// $data["dateDiff"] = $dateDiff;
		
		$selected = "Performance report";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		// Query
		$this->load->model("machine_model");
		
		$machines = $this->machine_model->get_sum_performance($startDate, $endDate);
		$data["machine"] = $machines;
		
		$config_result = array();
		$film_unit = array();
		
		$special_machine = array();
		$slitter_machine = array();
		
		$this->load->model("machine_manage_model");
		$this->load->model("film_history_model", "fh");
		for($i = 0; $i < count($machines); $i++) 
		{
		
			if ($machines[$i]["machine_type"] == "Y")
			{
				$special_machine[] = $machines[$i];
			}
			else if ($machines[$i]["machine_type"] == "S")
			{
				$slitter_machine[] = $machines[$i];
			}
		
			$mc_id = $machines[$i]["mc_id"];
			
			$result = $this->machine_manage_model->get_sum_performance($mc_id, $startDate, $endDate);
			
			for($j = 0; $j < count($result); $j++) {
				$config_result[$mc_id][$result[$j]["machine_config_id"]] = $result[$j]["duration"];
			}
			
			$film_unit[$mc_id] = $this->fh->get_unit($mc_id, $startDate, $endDate);
		}
		$data["config_result"] = $config_result;
		$data["film_unit"] = $film_unit;
		$data["special_machine"] = $special_machine;
		$data["slitter_machine"] = $slitter_machine;
		
		$slited_coil = $this->report_model->get_all_coil_by_date($startDate, $endDate);
		// print_r($slited_coil);
		$data["slited_coil"] = $slited_coil;
		
		$total_unit_temp = $this->program_model->get_sum_quantity_all_machine($startDate, $endDate);
		$total_unit = array();
		for($i = 0; $i < count($total_unit_temp); $i++) 
		{
			$total_unit[$total_unit_temp[$i]["mc_id"]] = $total_unit_temp[$i]["total_unit"];
		}
		$data["total_unit"] = $total_unit;
		
		$this->load->model("machine_config_model");
		$configs = $this->machine_config_model->get_all();
		$data["config"] = $configs;
		
		$dateDiffPerMachine = $this->report_model->get_date_diff_group_by_machine($startDate, $endDate);
		$dateDiff = array();
		for($i = 0; $i < count($dateDiffPerMachine); $i++)
		{
			$dateDiff[$dateDiffPerMachine[$i]["mc_id"]] = $dateDiffPerMachine[$i]["Total"];
		}
		$data["dateDiff"] = $dateDiff;

		$this->template->write_view('content', 'report/machine_performance_view', $data);
		$this->template->render();
	}
	
	public function upload_xpddor_excel_view()
	{	
		$selected = "ทำการ upload xppdor ไฟล์";
		$navigator = array(
			"หน้าแรก" => "/main",
			"Report" => "/report",
			$selected => "/report/upload_xpddor_excel_view",
		);
		
		$data["navigation"] = build_navigation($selected, $navigator);
		
		$this->template->write_view('content', 'report/upload_xpddor_excel_view', $data);
		$this->template->render();
	}
	
	public function upload_xpddor()
	{
		$username = $this->session->userdata("USERNAME");
		user_log_message("INFO",  $username . " call upload file");
		
		$upload_path = './upload/xppdor/';
		
		if (!file_exists($upload_path))
		{
			mkdir($upload_path);
			chmod($upload_path, DIR_WRITE_MODE);
		}
	
		$config['upload_path'] = $upload_path;
		$config['allowed_types'] = 'xls';
		$config['max_size']	= '0';
		$config['max_width']  = '0';
		$config['max_height']  = '0';
		$config['remove_spaces'] = TRUE;
		$config['file_name'] = mktime();
		
		$this->load->library('upload', $config);
		
		if ( ! $this->upload->do_upload())
		{
			// Upload incomplete
		}
		else
		{
			// Upload complete
			
			$excel_data = $this->upload->data();
			//print_r($excel_data);
			$excel = new Spreadsheet_Excel_Reader($excel_data["full_path"]);
			
			$orderdate_column = FALSE;
			$sono_column = FALSE;
			$custname_column = FALSE;
			$order_column = FALSE;
			$orderbal_column= FALSE;
			for($i = 'A'; $i <= 'Z' ; $i++)
			{
				
				// Find column name [orderdate]
				if ($excel->val(1, $i) == "orderdate")
				{
					$orderdate_column = $i;
				}
			
				// Find column name [sono]
				if ($excel->val(1, $i) == "sono")
				{
					$sono_column = $i;
				}
				
				// Find column name [custname]
				if ($excel->val(1, $i) == "custname")
				{
					$custname_column = $i;
				}
				
				// Find column name [order]
				if ($excel->val(1, $i) == "order")
				{
					$order_column = $i;
				}
				
				// Find column name [orderbal]
				if ($excel->val(1, $i) == "orderbal")
				{
					$orderbal_column = $i;
				}
				
			}
			
			
			$wsendqty_map = array();
			$totalqty_map = array();
			
			$username = $this->session->userdata("USERNAME");
			user_log_message("INFO",  $username . " get all data in excel");
			
			$product_dtl_id_column = 'K';
			$row = $excel->rowcount(0);
			
			$this->load->model("product_model");
			$product_display_id = $this->product_model->get_all_product_display_id();
			
			$k = FALSE;
			$isFound = FALSE;
			for($i = 2; $i < $row; $i++)
			{
				$k_val = $excel->val($i, $product_dtl_id_column);
				
				$k_items = explode(" ", $k_val);
				
				if (in_array($k_items[0], $product_display_id))
				{
					// Have Product
					if ($k != $k_items[0])
					{
						$k = $k_items[0];
						$isFound = TRUE;
					}
					else
					{
						$isFound = FALSE;
					}
				}
				else
				{
					$isFound = FALSE;
				}
				
				// New Product
				if ($k && $isFound)
				{
					$product_dtl_id = $k;
					
					
				}
				// 
				else if ($k && !$isFound)
				{
				}
			}
			
			/*
			for($i = 2 ; $i < $row; $i++)
			{
				if ($prodcode_column !== FALSE)
				{
					$prodcode = $excel->val($i, $prodcode_column);
					if (!empty($prodcode))
					{
						if ($wsendqty_column !== FALSE)
						{
							$wsendqty = $excel->val($i , $wsendqty_column);
							$wsendqty_map[$prodcode] = $wsendqty;
						}
						
						if ($totalqty_column !== FALSE)
						{
							$totalqty = $excel->val($i, $totalqty_column);
							$totalqty_map[$prodcode] = $totalqty;
						}
					}
				}
			}
			*/
		}
	}
	
	public function save_so()
	{
		$this->load->helper("date_helper");
		
		$sono = $this->input->post("sono");
		$product_dtl_id = $this->input->post("product_dtl_id");
		$delivery_date = $this->input->post("delivery_date");
		$priority = $this->input->post("priority");
		$item = $this->input->post("item");
		$ranking = $this->input->post("ranking");
		$user = $this->input->post("user");
	
		
		$this->load->model("report_model");
		
		for($i = 0; $i < count($sono); $i++)
		{
			$thedate = ($delivery_date[$i]) ? date_to_mysqldatetime($delivery_date[$i], FALSE) : FALSE;
			
			// echo "sono = " . $sono[$i] . ", product_dtl_id = " . $product_dtl_id[$i] . "<br/>";
			if ($thedate) 
			{
				//echo "sono = " . $sono[$i] . ", ";
				//echo "item = " . $item[$i] . ", ";
				//echo "ranking = " . $ranking[$i];
				$this->report_model->add_delivery($sono[$i], $product_dtl_id[$i], $thedate, $item[$i], $priority[$i], $ranking[$i], $user[$i]);
			}
		}
		
		$result = array(
			"success" => TRUE
		);
		
		echo json_encode($result);
		return;
	}
	
	public function so_page()
	{
		$product_dtl_id = $this->input->post("product_id");
	
		if ($product_dtl_id)
		{
			$this->load->model("report_model");
			$result = $this->report_model->get_so_data($product_dtl_id);
			
			$this->load->helper("date_helper");
			
			if (count($result) > 0)
			{
				
				$this->load->model("config_model");
				$p = $this->config_model->get_priority();
				
				$option = array();
				$option[""] = "";
				foreach($p as $key => $value)
				{
					$option[$key] = $value;
				}
				
				$start_date = "";
				$end_date = "";
				
				echo form_open("report/save_so", array("id" => "so_form"));
				echo '<table border="1" width="100%" cellpadding="5" cellspacing="3" class="table-data ui-widget">';
				echo '	<thead>';
				echo '		<tr class="ui-widget-header">';
				echo '    		<th>SO</th>';
				echo '			<th>Order Date</th>';
				echo '			<th>Customer Name</th>';
				echo '			<th>Order</th>';
				echo '			<th nowrap="nowrap">Order Balance</th>';
				echo ' 			<th nowrap="nowrap">Item</th>';
				echo ' 			<th nowrap="nowrap">Delivery Date</th>';
				echo ' 			<th nowrap="nowrap">Priority</th>';
				echo ' 			<th nowrap="nowrap">User</th>';
				echo '			<th nowrap="nowrap">&nbsp;</th>';
				echo '		</tr>';
				echo '	</thead>';
				echo '	<tbody>';
				
				$sono = (count($result) > 0) ? $result[0]["sono"] : FALSE;
				$index = 0;
				
				for($i = 0; $i < count($result); $i++)
				{
					$thedate = ($result[$i]["delivery_date"]) ? mysqldatetime_to_date($result[$i]["delivery_date"], "d/m/Y") : "";
					
					$index =  ($sono == $result[$i]["sono"]) ? (++$index) : 1;
					$sono = $result[$i]["sono"];
				
					echo '		<tr lt="' . base64_encode($result[$i]["sono"]) . '" data-index="' . $index . '" rel="' . base64_encode($sono) . '" class="' . (($i % 2 == 0) ? "even" : "odd") . '">';
					echo ' 			<td align="center">' . $result[$i]["sono"] . '</td>';
					echo '			<td align="center">' . date("d/m/Y", strtotime($result[$i]["order_date"])) . '</td>';
					echo '			<td align="center" nowrap="nowrap">' . $result[$i]["custname"] . '</td>';
					echo '			<td align="center">' . number_format($result[$i]["order"], 0) . '</td>';
					echo '			<td align="center">' . number_format($result[$i]["order_bal"], 0) . '</td>';
					echo ' 			<td align="center"><input type="text" size="5" name="item[]" class="item" value="' . $result[$i]["item"] . '" /></td>';
					echo ' 			<td align="center">
										<input type="hidden" name="sono[]" value="' . $result[$i]["sono"]  . '" />
										<input type="hidden" name="product_dtl_id[]" value="' . $result[$i]["product_dtl_id"] . '" />
										<input type="hidden" name="ranking[]" class="ranking" value="' . (($result[$i]["ranking"]) ? $result[$i]["ranking"] : 1) . '" />
										<input type="text" name="delivery_date[]" value="' . $thedate . '" class="datepicker" readonly="readonly" />
									</td>';
					echo '			<td align="center">';
					echo 			form_dropdown("priority[]", $option, $result[$i]["priority"], 'class="priority"');
					echo '			</td>';
					echo ' 			<td align="center"><input type="text" size="10" name="user[]" class="user" value="' . $result[$i]["user"] . '" /></td>';
					echo ' 			<td><input type="button" value="Add" onclick="onAddMore(this)" /></td>';
					echo '		</tr>';
					
					$start_date = date("d/m/Y", strtotime($result[$i]["start_date"]));
					$end_date = date("d/m/Y", strtotime($result[$i]["end_date"]));
				}		
				echo '	</tbody>';
				echo '</table>';
				echo form_close();
				echo '<br/>';
				echo '<strong> ข้อมูล วันที ' . $start_date . " ถึง " . $end_date . '</strong>';
			} 
			else
			{
				echo "Nothing.";
			}
		}
		else
		{
			echo "Nothing selected.";
		}
	}
	
	function DateDiff($strDate1,$strDate2)
	{
		return (strtotime($strDate2) - strtotime($strDate1))/  ( 60 * 60 * 24 );  // 1 day = 60*60*24
	}
}