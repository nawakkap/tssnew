<?= form_open("/config/save_method", array('id' => 'config_form')) ?>
<input type="hidden" name="mode" value="<?= $config_mode ?>"/>

<table border="0" class="table-data ui-widget" width="100%">
	<tr class="ui-widget-header">
		<td><?= $config_header ?></td>
		<td align="right"><button type="submit" id="saveButton" style="width: 100px">บันทึก</button></td>
	</tr>
</table>
<table border="1" cellpadding="4" width="100%" class="table-data ui-widget">
	<tr>
		<td align="left">
			<input type="hidden" name="rowCount" id="rowCount" class="rowCount" value=""/>
			<button type="button" id="backButton">ย้อนกลับ</button>
		</td>
		<td><button type="button" class="addButton" style="width: 100px">เพิ่ม</button></td>
	</tr>
<? foreach($result as $key => $value) { ?>
	<tr class="even">
		<td align="center"><input type="hidden" class="rowNumber" name="key" value="<?= $key ?>" /><input type="text" class="data" name="data" value="<?= $value ?>" size="50" /></td>
		<td width="10%"><button type="button" class="deleteButton" style="width: 100px">ลบ</button></td>
	</tr>
<? } ?>
</table>
<?= form_close() ?>
<br/><br/><br/>
<div id="pleasewait-dialog" style="display:none">โปรดรอสักครู่</div>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button();
	/*
	$("#config_form").submit(function(){
		return false;
	});
	*/
	$("#backButton").click(function(){
		document.location.replace("<?= site_url("/config") ?>");
	});
	$(".addButton").btnAddRow({inputBoxAutoNumber: true, displayRowCountTo: "rowCount"}, function(row){
		populateRowNumber();
	});
	$(".deleteButton").btnDelRow({displayRowCountTo: "rowCount"}, function(row){
		populateRowNumber();
	});
	$("#saveButton").click(function() {
		$("#config_form").submit();
	});
	$(".data:first").focus();
});
function populateRowNumber(){
	$.each($(".rowNumber"), function(index) {
		$(this).val((index + 1));
	});
}
</script>