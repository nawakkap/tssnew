<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


function build_navigation($selected, $navigation = array())
{

	$temp = "";
	foreach($navigation as $name => $site)
	{
		$temp .= "&gt;&gt;&nbsp;&nbsp;";
		if ($selected == $name)
		{
			$temp .= "<b>";
			if (empty($site)) 
			{
				$temp .= $name;
			}
			else
			{
				$temp .= anchor($site, $name, array("class" => "link"));
			}
			$temp .= "</b>";
		}
		else
		{
			if (empty($site)) 
			{
				$temp .= $name;
			}
			else
			{
				$temp .= anchor($site, $name, array("class" => "link"));
			}
		}
		$temp .= "&nbsp;&nbsp;";
	}
	
	return $temp;

}


/* End of file date_helper.php */
/* Location: ./system/application/helpers/date_helper.php */