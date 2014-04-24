<?= form_open_multipart('/report/upload_xpddor') ?>
<table width="100%" border="0" class="ui-widget" align="center">
	<? if (!empty($error)) { ?>
	<tr>
		<td colspan="2" style="color: red;" align="center">ข้อผิดพลาด : <?= $error ?></td>
	</tr>
	<? } ?>
	<tr>
		<td align="right" width="35%">Excel File&nbsp;&nbsp;&nbsp;</td>
		<td align="left"><input type="file" name="userfile" size="50" /></td>
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