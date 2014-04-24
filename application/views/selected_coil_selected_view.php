<table border="0" cellpadding="2" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />
		</td>
		<td align="right">
			<? if (count($selected_coil) > 0) { ?>
			<input type="button" id="slitButton" name="slitButton" class="button" value="Slit" />
			<? } ?>
		</td>
	</tr>
</table>
<br/>
<table border="1" cellpadding="3" width="100%" class="table-data ui-widget">
	<tr>
		<td class="ui-widget-header" width="20%">&nbsp;น้ำหนักรวม</td>
		<td><?= number_format($total_weight) ?>&nbsp;กิโลกรัม</td>
	</tr>
</table>
<? if (count($po_id_list) > 1) { ?>
<br/>
<table border="1" cellpadding="3" width="100%" class="table-data ui-widget">
	<tr>
		<td align="center" class="ui-state-highlight"><b> มีการเลือก Coil จากคนละ PO ID กัน : 
			<? for ($i = 0; $i < count($po_id_list); $i++) {  echo $po_id_list[$i] . "&nbsp;"; } ?></b>
		</td>
	</tr>
</table>
<? } ?>
<br/>
<table border="1" cellpadding="2" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="30">&nbsp;</th>
			<th align="center">Coil ID</th>
			<th align="center">Coil No</th>
			<th align="center">ความหนา</th>
			<th align="center">ความกว้าง</th>
			<th align="center">น้ำหนัก</th>
			<th align="center">PO ID</th>
			<th align="center">รับมาวันที่</th>
		</tr>
	</thead>
	<tbody>
	<? 
	
	$index = 1;
	
	for($i = 0; $i < count($selected_coil) ; $i++) {  
	
		$checkbox = array(
			'name' => 'checkbox',
			'class' => 'checkbox',
			'value' => $selected_coil[$i]['coil_id'],
			'coil_lot_no' => $selected_coil[$i]['coil_lot_no'],
			'po_id' => $selected_coil[$i]['po_id'],
			'checked' => TRUE,
			'row_num' => ($index++)
		);
	?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
			<td><?= form_checkbox($checkbox) ?></td>
			<td><?= $selected_coil[$i]['coil_id'] ?></td>
			<td><?= $selected_coil[$i]['coil_lot_no'] ?></td>
			<td><?= number_format($selected_coil[$i]['thickness'] , 2) ?></td>
			<td><?= number_format($selected_coil[$i]['width'] , 2) ?></td>
			<td><?= number_format($selected_coil[$i]['weight']) ?></td>
			<td><?= $selected_coil[$i]['po_id'] ?></td>
			<td><?= mysqldatetime_to_date($selected_coil[$i]['coil_received_date'] , 'd/m/Y') ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>
<table border="0" width="100%">
	<tr>
		<td align="right"><input type="button" id="mainButton" name="mainButton" class="button" value="กลับสู่หน้าหลัก" /></td>
	</tr>
</table>
<?= form_open("/select_coil", array('id' => 'select_form')) ?>
<input type="hidden" id="po_id" name="po_id" value="<?= $po_id ?>" />
<input type="hidden" id="row_num" name="row_num" value="<?= count($selected_coil) ?>" />
<input type="hidden" id="search" name="search" value="" />
<input type="hidden" id="search_type" name="search_type" value="" />
<? for($i = 0; $i < count($selected_coil); $i++) { ?>
<input type="hidden" name="select_coil_id<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['coil_id'] ?>" />
<input type="hidden" name="select_coil_lot_no<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['coil_lot_no'] ?>" />
<input type="hidden" name="select_po_id<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['po_id'] ?>" />
<? } ?>
<?= form_close() ?>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	
	$(".checkbox").click(onCheckboxClick);
	$("#mainButton").click(onMainButtonClick);
	$("#backButton").click(onBackButtonClick);
	$("#slitButton").click(onSlitButtonClick);
	
	<? if (count($po_id_list) > 1) { ?>
	$("#slitButton").hide();
	<? } ?>
});
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function onBackButtonClick() {
	$("#select_form").attr("action", "<?= site_url("/select_coil") ?>");
	$("#select_form").submit();
}
function onSlitButtonClick() {
	$("#select_form").attr("action", "<?= site_url("/select_coil/slit") ?>");
	$("#select_form").submit();
}
function onCheckboxClick() {
	if ($(this).attr("checked")) { 
		$("#row_num").val(parseFloat($("#row_num").val()) + 1);
		var row_num = $("#row_num").val();
		
		var coil_id = $(this).val();
		var coil_lot_no = $(this).attr("coil_lot_no");
		var po_id = $(this).attr("po_id");
		
		$('<input type="hidden" name="select_coil_id" value="" />').attr("name", "select_coil_id" + row_num).attr("row_num", row_num).val(coil_id).appendTo($("#select_form"));
		$('<input type="hidden" name="select_coil_lot_no" value="" />').attr("name", "select_coil_lot_no" + row_num).attr("row_num", row_num).val(coil_lot_no).appendTo($("#select_form"));
		$('<input type="hidden" name="select_po_id" value="" />').attr("name", "select_po_id" + row_num).attr("row_num", row_num).val(po_id).appendTo($("#select_form"));
		$(this).attr("row_num", $("#row_num").val());
	} else {
		var row_num = $(this).attr("row_num");
		$("input[row_num=" + row_num + "]:not(:checkbox)").remove();
	}
	
	var po_id_list = new Array();
	$.each($(".checkbox:checked"), function(index) {
		po_id_list.push($(this).attr("po_id"));
	});
	po_id_list = po_id_list.unique();
	if (po_id_list.length <= 1) {
		$("#slitButton").show();
	} else {
		$("#slitButton").hide();
	}
}
</script>