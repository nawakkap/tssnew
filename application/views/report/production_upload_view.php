<?= form_open_multipart('/report/upload') ?>
<table width="100%" border="0" class="ui-widget" align="center">
	<? if (!empty($error)) { ?>
	<tr>
		<td colspan="2" style="color: red;" align="center">ข้อผิดพลาด : <?= $error ?></td>
	</tr>
	<? } ?>
	<tr>
		<td align="right" width="35%">PSTOCK3 File&nbsp;&nbsp;&nbsp;</td>
		<td align="left"><input type="file" name="userfile" size="50" /></td>
	</tr>
	<tr>
		<td align="right" width="35%">XPPDOR Excel File&nbsp;&nbsp;&nbsp;</td>
		<td align="left"><input type="file" name="xppdorfile" size="50" /></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><button type="submit">Upload</button>&nbsp;&nbsp;<button type="button" id="backButton">ย้อนกลับ</button></td>
	</tr>
</table>
<?= form_close() ?>
<br/>
<!--
<table border="0" width="100%" class="ui-widget">
	<tr>
		<td valign="bottom">รายการไฟล์ทั้งหมดที่เคย upload</td>
		<td align="right"><button id="deleteAllButton">ลบทั้งหมด</button></td>
	</tr>
</table>
<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center">File name</th>
			<th align="center">File Date</th>
		</tr>
	</thead>
	<? foreach($filenames as $file) { 
	//print_r($file);?>
	<tr>
		<td align="center"><a href="<?= site_url("/report/production_read_file/" . $this->convert->AsciiToHex($file["server_path"]) ) ?>" class="link"><?= $file["name"] ?></a></td>
		<td align="center"><?= unix_to_human($file["date"]) ?></td>
	</tr>
	<? } ?>
</table>
-->
<?= form_open("/report/delete_all_file", array("id" => "delete_form")) ?><?= form_close() ?>
<style type="text/css" rel="stylesheet">
button {
	width: 47%;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button();
	$("#backButton").click(onBackButtonClick);
	$("#deleteAllButton").click(onDeleteAllButtonClick);
});
function onDeleteAllButtonClick() {
	$("#delete_form").submit();
}
function onBackButtonClick() {
	document.location.replace("<?= site_url("/report") ?>");
}
</script>