<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if ( ! function_exists('check_permission'))
{
	function check_permission($perm) 
	{
		$CI =& get_instance();
		$permission = $CI->session->userdata("PERMISSION");
		$user_type = $CI->session->userdata("USER_TYPE");
		if ("ADMIN" == $user_type) {
			return TRUE;
		}
		
		$perm_temp = strtoupper($perm);
		$pos = strpos($permission, $perm_temp);
		if ($pos === false)
		{
			redirect('/unperm');
			return FALSE;
		}
		return TRUE;
	}
}

/* End of file login_helper.php */
/* Location: ./system/application/helpers/login_helper.php */