<?php

class Config extends CI_Controller {

	private static $PERMISSION = "CONFIG";

	function __construct()
	{
		parent::__construct();	
		
		check_permission(self::$PERMISSION);
		
		$this->load->model('config_model');
	}
	
	function index()
	{
		$user_type = $this->session->userdata("USER_TYPE");
		$data['user_type']  = $user_type;
		$this->template->write_view('content', 'config_main_view', $data);
		$this->template->render();
	}
	
	function payment()
	{
		$data['result'] = $this->config_model->get_payment_term();
		
		$data['config_header'] = 'วิธีการชำระเงิน';
		$data['config_mode'] = 'PAYMENT_TERM';
		
		$this->template->write_view('content', 'config_show_view' , $data);
		$this->template->render();
	}
	
	function premium()
	{
		$data = $this->config_model->get_vat_premium();
		
		$data['config_header'] = 'ค่า Premium';
		$data['config_mode'] = 'VAT_ACCOUNTING';
		
		$this->template->write_view('content', 'config_premium_view' , $data);
		$this->template->render();
	}
	
	function width() 
	{
		$data['result'] = $this->config_model->get_width();
		
		asort($data["result"]);
		
		$data['config_header'] = 'ความกว้าง';
		$data['config_mode'] = 'WIDTH';
		
		$this->template->write_view('content', 'config_show_view' , $data);
		$this->template->render();
	}
	
	function thickness()
	{
		$data['result'] = $this->config_model->get_thickness();
		
		asort($data["result"]);
		
		$data['config_header'] = 'ความหนา';
		$data['config_mode'] = 'THICKNESS';
		
		$this->template->write_view('content', 'config_show_view' , $data);
		$this->template->render();
	}
	
	function order_status()
	{
		$data['result'] = $this->config_model->get_order_status();
		
		$data['config_header'] = 'สถานะของ Order';
		$data['config_mode'] = 'ORDER_STATUS';
		
		$this->template->write_view('content', 'config_show_view' , $data);
		$this->template->render();
	}
	
	function coil_status()
	{
		$data['result'] = $this->config_model->get_coil_status();
		
		$data['config_header'] = 'สถานะของ Coil';
		$data['config_mode'] = 'COIL_STATUS';
		
		$this->template->write_view('content', 'config_show_view' , $data);
		$this->template->render();
	}
	
	function film_status()
	{
		$data['result'] = $this->config_model->get_film_status();
		
		$data['config_header'] = 'สถานะของ Film';
		$data['config_mode'] = 'FILM_STATUS';
		
		$this->template->write_view('content', 'config_show_view' , $data);
		$this->template->render();
	}
	
	function machine_status()
	{
		$data['result'] = $this->config_model->get_machine_status();
		
		$data['config_header'] = "สถานะของ Machine";
		$data['config_mode'] = "MACHINE_STATUS";
		
		$this->template->write_view('content', 'config_show_view', $data);
		$this->template->render();
	}
	
	function save_method()
	{
	
		$mode = $this->input->post("mode");
		$key = $this->input->post("key");
		$data = $this->input->post("data");
		
		$rowCount = $this->input->post("rowCount");
		
		$this->db->trans_begin();
		$this->config_model->delete($mode);
		
		// $gen_key = date("YmdHis") . rand(1000, 9999);
		for($i = 0; $i <= $rowCount; $i++) 
		{
			$gen_key = date("YmdHis") . rand(1000, 9999);
			$key = $this->input->post("key" . $i);
			$data =  $this->input->post("data". $i);
			
			if ((!empty($key)) && (!empty($data))) 
			{
				$data = array(
					'general_param_id' => $gen_key,
					'param_code' => $key,
					'param_value' => $data,
					'param_type' => $mode,
					'seq_no' => $key,
					'record_change_by' => $this->session->userdata("USERNAME"),
					'record_change_date' => date_to_mysqldatetime(),
				);				
				$this->config_model->insert($mode, $data);
				
				usleep(1000);
			}
		}
		$this->db->trans_complete();
		
		if ($this->db->trans_status() === FALSE)
		{
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$this->db->trans_rollback();
			$data['back_page'] = "/config";
			$this->template->write_view('content', 'order_result_view', $data);
			$this->template->render();
		}
		else
		{
			$data['result'] = 'เปลี่ยนแปลงเรียบร้อยแล้ว';
			$data['back_page'] = "/config";
			$this->template->write_view('content', 'order_result_view', $data);
			
			$this->db->trans_commit();
			
			$this->template->render();
		}
	}
	
	function save_premium()
	{
		$mode = $this->input->post("mode");
		$vat_premium = $this->input->post("VAT_PREMIUM");
		$vat_normal = $this->input->post("VAT_NORMAL");
		
		$vat_premium = ($vat_premium  / 100) + 1;
		$vat_normal = ($vat_normal / 100) + 1;
	
		$this->db->trans_begin();
		$this->config_model->delete('VAT_ACCOUNTING');
	
		$data = array(
			'general_param_id' => utime(),
			'param_code' => 'VAT_NORMAL',
			'param_value' => $vat_normal,
			'param_type' => 'VAT_ACCOUNTING',
			'seq_no' => 1,
			'record_change_by' => $this->session->userdata("USERNAME"),
			'record_change_date' => date_to_mysqldatetime(),
		);
		
		$this->config_model->insert('VAT_ACCOUNTING', $data);
		
		usleep(5000);
		$data = array(
			'general_param_id' => utime(),
			'param_code' => 'VAT_PREMIUM',
			'param_value' => $vat_premium,
			'param_type' => 'VAT_ACCOUNTING',
			'seq_no' => 2,
			'record_change_by' => $this->session->userdata("USERNAME"),
			'record_change_date' => date_to_mysqldatetime(),
		);
		$this->config_model->insert('VAT_ACCOUNTING', $data);
		
		if ($this->db->trans_status() === FALSE)
		{
			
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$data['back_page'] = "/config";
			$this->template->write_view('content', 'order_result_view', $data);
			
			$this->db->trans_rollback();
			
			$this->template->render();
		}
		else
		{
			
			$data['result'] = 'เปลี่ยนแปลงเรียบร้อยแล้ว';
			$data['back_page'] = "/config";
			$this->template->write_view('content', 'order_result_view', $data);
			
			$this->db->trans_commit();
			
			$this->template->render();
			
			
		}
	}
	
	function reset_all()
	{
		$this->db->trans_begin();
		
		$this->config_model->reset_all();
		
		if ($this->db->trans_status() === FALSE)
		{
			
			$data['result'] = 'ไม่สามารถเปลี่ยนแปลงได้ เพราะเกิดปัญหาบางอย่าง<br/>Error Number : ' . $this->db->_error_number() . "<br/>" .$this->db->_error_message();
			$data['back_page'] = "/config";
			$this->template->write_view('content', 'order_result_view', $data);
			
			$this->db->trans_rollback();
			
			$this->template->render();
		}
		else
		{
			
			$data['result'] = 'ทำการ reset data เรียบร้อยแล้ว';
			$data['back_page'] = "/config";
			$this->template->write_view('content', 'order_result_view', $data);
			
			$this->db->trans_commit();
			
			$this->template->render();
			
			
		}
	}
	
	function backup_database()
	{
		$this->load->dbutil();
		$this->load->helper('download');
		
		$prefs = array(
                'ignore'      => array(),           // List of tables to omit from the backup
                'format'      => 'zip',             // gzip, zip, txt
                'filename'    => date('d_m_Y') . '.sql',    // File name - NEEDED ONLY WITH ZIP FILES
                'add_drop'    => TRUE,              // Whether to add DROP TABLE statements to backup file
                'add_insert'  => TRUE,              // Whether to add INSERT data to backup file
                'newline'     => "\n"               // Newline character used in backup file
		);
		
		$backup =& $this->dbutil->backup($prefs);
		
		force_download(date('d_m_Y'). '.zip', $backup); 
		
		$this->index();
	}
	
	public function priority()
	{
		$data['result'] = $this->config_model->get_priority();
		
		$data['config_header'] = 'ระดับความสำคัญ';
		$data['config_mode'] = 'PRIORITY';
		
		$this->template->write_view('content', 'config_show_view' , $data);
		$this->template->render();
	}
}

/* End of file coil.php */
/* Location: ./system/application/controllers/coil.php */