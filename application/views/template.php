<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?= $title ?></title>
	<META NAME="robots" CONTENT="noindex,nofollow">
	<META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=UTF-8">
	<?= css_asset("ui-lightness/jquery-ui-1.8.1.custom.css") ?>
	<?= css_asset("validationEngine.jquery.css") ?>
	<?= js_asset("jquery-1.4.2.js") ?>
	<?= js_asset("jquery-ui.js") ?>
	<?= js_asset("jquery.format.1.03.js") ?>
	<?= js_asset("jquery.numberformatter.js") ?>
	<?= js_asset("jquery.table.addrow.js") ?>
	<?= js_asset("jquery.validationEngine-en.js") ?>
	<?= js_asset("jquery.validationEngine.js") ?>
	<?= js_asset("jquery.tablesorter.min.js") ?>
	<?= js_asset("jquery.json-2.2.min.js") ?>
	<?= js_asset("dhtmlxcommon.js") ?>
	<?= js_asset("dhtmlxcombo.js") ?>
	<?= js_asset("util.js") ?>
	<?= css_asset("dhtmlxcombo.css") ?>
	<script type="text/javascript">
		window.dhx_globalImgPath = "<?= base_url() ?>assets/image/";
	</script>
	<style type="text/css" rel="stylesheet">
		html,body{margin:0; padding:0;font-family:Tahoma;font-size:10pt;width:100%;height:100%;}
		.table-data{border-collapse: collapse;border-width:1px;border-color:#CCCCCC;border-style:solid;}
		.table-data thead th.header { cursor: pointer;} 
		.table-data tbody tr.even{ background-color: #FFFFCC;text-align:center;}
		.table-data tbody tr.odd{background-color: #FFFFFF;text-align:center;}
		.remark-text{font-weight:bolder;color:red;}
		a.link,a.link:visited, a.link:active {text-decoration:none;color:black;}
		a.link:hover{color:#F6A828;}
		.button { width: 140px;font-size:11pt;font-family:Tahoma;}
		#mainButton { width: 200px;}
		.ui-datepicker{font-size:10pt;}
	</style>
</head>
<body>
	<table id="header-panel" width="850" border="0" align="center" cellspacing="0" cellpadding="0"><tr><td align="left" valign="top"><?= image_asset("banner.gif", '', array('width' => 850)) ?></td></tr></table>
	<br/>
	<?php if (isset($navigation) && $navigation) { ?>
	<table id="navigation-panel" border="0" width="850" align="center" cellspacing="0" cellpadding="4" class="ui-widget"><tr class="ui-state-highlight"><td><?= $navigation ?></td></tr></table>
	<br/>
	<?php } ?>
	<table id="content-panel"  width="850" border="0" align="center" cellspacing="0" cellpadding="0"><tr><td align="left" valign="top"><?= $content ?></td></tr></table>
	<br/><br/><br/>
</body>
</html>