<?php

class Calendar extends CI_Controller 
{
	function __construct()
	{
		parent::__construct();	
	}
	
	function index() // For cancel lot no
	{
		check_permission("CALENDAR");
		
		$data = array(
			
		);
		
		$this->load->view("calendar/calendar_home_view", $data);
	}
	
	function addCalendar($st, $et, $sub, $ade){
		$ret = array();
		$ret['IsSuccess'] = true;
		$ret['Msg'] = 'add success';
		$ret['Data'] =rand();
		return $ret;
	}
	
	function addDetailedCalendar($st, $et, $sub, $ade, $dscr, $loc, $color, $tz){
		$ret = array();
		$ret['IsSuccess'] = true;
		$ret['Msg'] = 'add success';
		$ret['Data'] = rand();
		return $ret;
	}
	
	function listCalendarByRange($sd, $ed, $cnt){
		$ret = array();
		$ret['events'] = array();
		$ret["issort"] =true;
		$ret["start"] = php2JsTime($sd);
		$ret["end"] = php2JsTime($ed);
		$ret['error'] = null;
		$title = array('team meeting', 'remote meeting', 'project plan review', 'annual report', 'go to dinner');
		$location = array('Lodan', 'Newswer', 'Belion', 'Moore', 'Bytelin');
		for($i=0; $i<$cnt; $i++) {
			$rsd = rand($sd, $ed);
			$red = rand(3600, 10800);
			if(rand(0,10) > 8){
				$alld = 1;
			}else{
				$alld=0;
			}
			
			/*
			$ret['events'][] = array(
				rand(10000, 99999),
				$title[rand(0,4)],
				php2JsTime($rsd),
				php2JsTime($red),
				1, // all day
				0, // cross day
				0, //Recurring event
				rand(-1,13),
				1, //editable
				$location[rand(0,4)], 
				'',//$attends
			);
			*/
		}
		return $ret;
	}
	
	function listCalendar($day, $type){
		/*
		$this->load->helper("date_helper");
		
		$phpTime = js2PhpTime($day);
		//echo $phpTime . "+" . $type;
		switch($type){
			case "month":
				$st = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
				$et = mktime(0, 0, -1, date("m", $phpTime)+1, 1, date("Y", $phpTime));
				$cnt = 50;
			break;
			case "week":
				//suppose first day of a week is monday 
				$monday  =  date("d", $phpTime) - date('N', $phpTime) + 1;
				//echo date('N', $phpTime);
				$st = mktime(0,0,0,date("m", $phpTime), $monday, date("Y", $phpTime));
				$et = mktime(0,0,-1,date("m", $phpTime), $monday+7, date("Y", $phpTime));
				$cnt = 20;
			break;
			case "day":
				$st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
				$et = mktime(0, 0, -1, date("m", $phpTime), date("d", $phpTime)+1, date("Y", $phpTime));
				$cnt = 5;
			break;
		}
		//echo $st . "--" . $et;
		return $this->listCalendarByRange($st, $et, $cnt);
		*/
	}
	
	function updateCalendar($id, $st, $et){
		$ret = array();
		$ret['IsSuccess'] = true;
		$ret['Msg'] = 'Succefully';
		return $ret;
	}

	function updateDetailedCalendar($id, $st, $et, $sub, $ade, $dscr, $loc, $color, $tz){
		$ret = array();
		$ret['IsSuccess'] = true;
		$ret['Msg'] = 'Succefully';
		return $ret;
	}
	
	function removeCalendar($id){
		$ret = array();
		$ret['IsSuccess'] = true;
		$ret['Msg'] = 'Succefully';
		return $ret;
	}
	
	public function feed()
	{
		$this->load->helper('date_helper');

		$this->output->set_header("HTTP/1.0 200 OK");
		// $this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
		$this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
		$this->output->set_header("Cache-Control: post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->output->set_header('Content-type:text/javascript;charset=UTF-8');
		
		/*
		$ret = array();
		$method = $this->input->get("method");
		
		switch ($method) {
			case "add":
				$ret = $this->addCalendar($this->input->post("CalendarStartTime"), $this->input->post("CalendarEndTime"), $this->input->post("CalendarTitle"), $this->input->post("IsAllDayEvent"));
				break;
			case "list":
				$ret = $this->listCalendar($this->input->post("showdate"), $this->input->post("viewtype"));
				break;
			case "update":
				$ret = $this->updateCalendar($this->input->post("calendarId"), $this->input->post("CalendarStartTime"), $this->input->post("CalendarEndTime"));
				break; 
			case "remove":
				$ret = $this->removeCalendar($this->input->post("calendarId"));
				break;
			case "adddetails":
				$id = $this->input->get("id");
				$st = $this->input->post("stpartdate") . " " . $this->input->post("stparttime");
				$et = $this->input->post("etpartdate") . " " . $this->input->post("etparttime");
				if($id){
					$ret = $this->updateDetailedCalendar($id, $st, $et, 
						$this->input->post("Subject"), $this->input->post("IsAllDayEvent")?1:0, $this->input->post("Description"), 
						$this->input->post("Location"), $this->input->post("colorvalue"), $this->input->post("timezone"));
				}else{
					$ret = $this->addDetailedCalendar($st, $et,                    
						$this->input->post("Subject"), $this->input->post("IsAllDayEvent")?1:0, $this->input->post("Description"), 
						$this->input->post("Location"), $this->input->post("colorvalue"), $this->input->post("timezone"));
				}        
				break; 
			
		}
		*/
		
		$this->load->helper("date_helper");
		
		// List Month 
		$day = $this->input->post("showdate");
		$type = $this->input->post("viewtype");
		
		$phpTime = js2PhpTime($day);
		//echo $phpTime . "+" . $type;
		switch($type){
			case "month":
				$st = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
				$et = mktime(0, 0, -1, date("m", $phpTime)+1, 1, date("Y", $phpTime));
				//$cnt = 50;
			break;
			case "week":
				//suppose first day of a week is monday 
				$monday  =  date("d", $phpTime) - date('N', $phpTime) + 1;
				//echo date('N', $phpTime);
				$st = mktime(0,0,0,date("m", $phpTime), $monday, date("Y", $phpTime));
				$et = mktime(0,0,-1,date("m", $phpTime), $monday+7, date("Y", $phpTime));
				//$cnt = 20;
			break;
			case "day":
				$st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
				$et = mktime(0, 0, -1, date("m", $phpTime), date("d", $phpTime)+1, date("Y", $phpTime));
				//$cnt = 5;
			break;
		}
		
		// Query from database
		$start_date = date("Y-m-d", $st);
		$end_date = date("Y-m-d", $et);
		
		$this->load->model("report_model");
		$this->load->model("product_model");
		$result = $this->report_model->get_delivery_report_only_so($start_date, $end_date);
		
		// List 
		$ret = array();
		$ret['events'] = array();
		$ret["issort"] = true;
		$ret["start"] = php2JsTime($st);
		$ret["end"] = php2JsTime($et);
		$ret['error'] = null;
		// $title = array('team meeting', 'remote meeting', 'project plan review', 'annual report', 'go to dinner');
		// $location = array('Lodan', 'Newswer', 'Belion', 'Moore', 'Bytelin');
		
		for($i=0; $i< count($result) ; $i++) 
		{
			
			$alld = 1; // All day
			
			$rsd = rand($st, $et);
			$red = rand(3600, 10800);
			
			$r = $this->report_model->get_xppdor_detail($result[$i]["sono"], $result[$i]["ranking"], $result[$i]["product_dtl_id"]);
			$display  = "";
			if ($r)
			{
				$product = $this->product_model->get_product_by_display_id($r["product_dtl_id"]);
				if ($product)
				{
					$display = "Order Date : " . date("d/m/Y", strtotime($r["order_date"])) . "\n";
					$display .= "Customer Name : " . $r["custname"] . "\n";
					$display .= "Sono  : " . $r["sono"] . "\n";
					$display .= "Product : " . $product["product_name_th"] . "\n";
					$display .= "Order : " . number_format($r["item"], 0) . "\n";
					$display .= "Order Balance : " . number_format($r["order_bal"], 0) . "\n";
				}
				
				$ret['events'][] = array(
					rand(10000, 99999),
					$r["custname"] . "(" . number_format($r["item"], 0) . ")",
					php2JsTime(strtotime($result[$i]["delivery_date"])),
					php2JsTime(strtotime($result[$i]["delivery_date"])),
					1, // All Day Event ??
					0, //more than one day event
					0, //Recurring event
					($r["priority"]) ? $r["priority"] : 1, // Color
					0, //editable
					"", // Location
					'', //$attends,
					$display
				);
			}
			
			/*
			$ret['events'][] = array(
				rand(10000, 99999),
				$result[$i]["sono"],
				php2JsTime(strtotime($result[$i]["order_date"])),
				php2JsTime(strtotime($result[$i]["order_date"])),
				1, // All Day Event ??
				$alld, //more than one day event
				0,//Recurring event
				1, // Color
				0, //editable
				"", // Location
				''//$attends
			);
			*/
		}
		
		echo json_encode($ret);
		return;
	}
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */