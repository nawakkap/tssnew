<table border="0" cellpadding="2" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>
			<input type="button" id="selectedButton" name="selectedButton" class="button" value="รายการที่เลือก" />
		</td>
		<td align="right">
				<input type="button" name="mainButton" class="button mainButton" value="กลับสู่หน้าหลัก" />
				<input type="button" id="searchButton" name="searchButton" class="button" value="ค้นหาจาก PO ID" />
		</td>
	</tr>
</table>
<br/>
<table width="100%" border="0" class="table-data ui-widget">
	<tr>
		<td class="ui-state-active">แสดงตาม PO ID : <?= $po_id ?></td>
	</tr>
</table>
<table border="1" cellpadding="2" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="30">&nbsp;</th>
			<th align="center"><a href="#" class="sort" rel="coil_id">Coil ID</a></th>
			<th align="center"><a href="#" class="sort" rel="coil_lot_no">Coil No</a></th>
			<th align="center"><a href="#" class="sort" rel="thickness">ความหนา</a></th>
			<th align="center"><a href="#" class="sort" rel="width">ความกว้าง</a></th>
			<th align="center"><a href="#" class="sort" rel="weight">น้ำหนัก</a></th>
			<th align="center"><a href="#" class="sort" rel="coil_received_date">รับมาวันที่</a></th>
		</tr>
	</thead>
	<tbody>
	<? 
	
	$index = 1;
	for($i = 0; $i < count($coil_result) ; $i++) {  
		
			$checked = FALSE;
			for($j = 0; $j < count($selected_coil) ; $j++) {
				if ($coil_result[$i] == $selected_coil[$j]) {
					$checked = TRUE;
					$index = $j;
				}
			}
			
			$checkbox = array(
				'name' => 'checkbox',
				'class' => 'checkbox',
				'value' => $coil_result[$i]['coil_id'],
				'coil_lot_no' => $coil_result[$i]['coil_lot_no'],
				'po_id' => $coil_result[$i]['po_id'],
				'checked' => $checked,
			);
			
			if ($checked === TRUE) {
				$checkbox['row_num'] = ($index + 1);
			}
			
	?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
			<td><?= form_checkbox($checkbox) ?></td>
			<td><?= $coil_result[$i]['coil_id'] ?></td>
			<td><?= $coil_result[$i]['coil_lot_no'] ?></td>
			<td><?= number_format($coil_result[$i]['thickness'] , 2) ?></td>
			<td><?= number_format($coil_result[$i]['width'] , 2) ?></td>
			<td><?= number_format($coil_result[$i]['weight'] , 0) ?></td>
			<td><?=  mysqldatetime_to_date($coil_result[$i]['coil_received_date'] , 'd/m/Y') ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>
<?= form_open("/select_coil", array('id' => 'select_form')) ?>
<input type="hidden" id="po_id" name="po_id" value="<?= $po_id ?>" />
<input type="hidden" id="row_num" name="row_num" value="<?= count($selected_coil) ?>" />
<input type="hidden" id="search" name="search" value="" />
<input type="hidden" id="search_type" name="search_type" value="<?= $search_type ?>" />
<? for($i = 0; $i < count($selected_coil); $i++) { ?>
<input type="hidden" name="select_coil_id<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['coil_id'] ?>" />
<input type="hidden" name="select_coil_lot_no<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['coil_lot_no'] ?>" />
<input type="hidden" name="select_po_id<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['po_id'] ?>" />
<? } ?>
<?= form_close() ?>
<div id="searchDialog" title="ค้นหา" style="display:none">
PO ID&nbsp;&nbsp;:&nbsp;&nbsp;<input  type="text" id="po_id_in_search_dialog" name="po_id" value="" />
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$(".mainButton").click(onMainButtonClick);
	$("#searchButton").click(onSearchButtonClick);
	
	initSearchDialog();
	
	$(".sort").click(onSortCLick);
	$(".checkbox").click(onCheckboxClick);
	$("#selectedButton").click(onSelectedSlitButtonClick);
	checkCheckbox();
	
	
});
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function initSearchDialog() {
	$("#searchDialog").dialog({
		modal: true,
		autoOpen : false,
		open : function() {
			$("#search_type").val("");
			$("#po_id_in_search_dialog").val("").select();
		},
		buttons : { 
			'ตกลง' : function () {
				onSearchDialogOK();
			}
		}
	});
	
	$("#po_id_in_search_dialog").keyup(function(event) {
		if (event.keyCode == 13) {
			onSearchDialogOK();
		}
	});
}
function onSearchButtonClick() {
	$("#searchDialog").dialog("open");
}
function onSearchDialogOK() {
	if ($("#po_id_in_search_dialog").val() == "") {
		$("#searchDialog").dialog("close");
		return false;
	}
	
	$("#po_id").val($("#po_id_in_search_dialog").val());
	$("#page").val("select_by_po_id");
	$("#select_form").attr("action", "<?= site_url("/select_coil") ?>");
	$("#select_form").submit();
	return true;
}
function onSortCLick() {
	var sort_by = $(this).attr("rel");
	$("#search").val(sort_by);
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
	
	
	checkCheckbox();
}
function checkCheckbox(){
	if ($(".checkbox:checked").size() > 0) {
		$("#searchButton").hide();
	} else {
		$("#searchButton").show();
	}
}
function onSelectedSlitButtonClick() {
	$("#select_form").attr("action", "<?= site_url("/select_coil/selected_slit") ?>");
	$("#select_form").submit();
}
</script>