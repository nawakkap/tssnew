<table border="0" cellpadding="2" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>
			<input type="button" id="selectedButton" name="selectedButton" class="button" value="รายการที่เลือก" />
		</td>
		<td align="right">
			<div id="searchPanel">
				<input type="radio" id="searchAll" name="searchButton" class="button" value="แสดงทั้งหมด" checked="checked" /><label id="searchAllLabel" for="searchAll">แสดงทั้งหมด</label>
				<input type="radio" id="searchByPo" name="searchButton" class="button" value="ค้นหาจาก PO ID" /><label id="searchByPoLabel" for="searchByPo">ค้นหาจาก PO ID</label>
			</div>
		</td>
	</tr>
</table>
<br/>
<table width="100%" border="0" class="table-data ui-widget">
	<tr>
		<td class="ui-state-active">แสดงทั้งหมด</td>
	</tr>
</table>
<table border="1" cellpadding="2" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="30">&nbsp;</th>
			<th align="center"><a href="#" class="search" rel="coil_id">Coil ID</a></th>
			<th align="center"><a href="#" class="search" rel="coil_lot_no">Coil No</a></th>
			<th align="center"><a href="#" class="search" rel="thickness">ความหนา</a></th>
			<th align="center"><a href="#" class="search" rel="width">ความกว้าง</a></th>
			<th align="center"><a href="#" class="search" rel="weight">น้ำหนัก</a></th>
			<th align="center"><a href="#" class="search" rel="coil_received_date">รับมาวันที่</a></th>
		</tr>
	</thead>
	<tbody>
	<? 
	
	$index = 1;
	for($i = 0; $i < count($coil_result) ; $i++) {  
		
			$checked = FALSE;
			if (in_array($coil_result[$i], $selected_coil)) {
				$checked = TRUE;
			}
			
			
			$checkbox = array(
				'name' => 'checkbox',
				'class' => 'checkbox',
				'value' => $coil_result[$i]['coil_id'],
				'coil_lot_no' => $coil_result[$i]['coil_lot_no'],
				'po_id' => $coil_result[$i]['po_id'],
				'checked' => $checked,
			);
			
			if ($checked) {
				$checkbox['row_num'] = $index;
				$index++;
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
<table border="0" width="100%">
	<tr>
		<td align="right"><input type="button" id="mainButton" name="mainButton" class="button" value="กลับสู่หน้าหลัก" /></td>
	</tr>
</table>
<?= form_open("/select_coil", array('id' => 'search_form')) ?>
<input type="hidden" id="search" name="search" value="" />
<input type="hidden" id="search_type" name="search_type" value="<?= $search_type ?>" />
<?= form_close() ?>
<?= form_open("/select_coil", array('id' => 'select_form')) ?>
<input type="hidden" id="po_id" name="po_id" value="" />
<input type="hidden" id="page" name="page" value="selected_all_view" />
<input type="hidden" id="row_num" name="row_num" value="" />
<? for($i = 0; $i < count($selected_coil); $i++) { ?>
<input type="hidden" name="select_coil_id<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['coil_id'] ?>" />
<input type="hidden" name="select_coil_lot_no<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['coil_lot_no'] ?>" />
<input type="hidden" name="select_po_id<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['po_id'] ?>" />
<? } ?>
<?= form_close() ?>
<div id="searchDialog" title="ค้นหา" style="display:none">
PO ID&nbsp;&nbsp;:&nbsp;&nbsp;<input  type="text" id="po_id_search" name="po_id" value="" />
</div>
<script type="text/javascript">
var ajax;
$(document).ready(function(){
	$("#row_num").val(<?= count($selected_coil) ?>);
	$(".button").button();
	$("#searchPanel").buttonset();
	$("#searchByPo").click(onSearchByPOClick);
	$("#mainButton").click(onMainButtonClick);
	$("#selectedButton").click(function(){
		//$("#page").val("selected_show_view");
		//$("#select_form").submit();
	});
	$(".checkbox").click(onCheckboxClick);
	$(".search").click(onSearchClick);
	$("#searchDialog").dialog({
		modal: true,
		autoOpen: false,
		buttons : {
			'ตกลง' : function() {
				onSearchByPOChange();
			}, 
		},
		close : function() {
			$("#po_id_search").val("");
			$("#searchAllLabel").addClass("ui-state-active");
			$("#searchByPoLabel").removeClass("ui-state-active");
		},
		open: function() {
			$("#po_id_search").select();
		}
	});
});
function onSearchByPOChange() {
	$("#po_id").val($("#po_id_search").val());
	$("#page").val("select_by_po_id");
	$("#select_form").submit();
}
function onSearchByPOClick() {
	$("#searchDialog").dialog("open");
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function onSearchClick() {
	var search_by = $(this).attr("rel");
	$("#search").val(search_by);
	$("#search_form").submit();
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
}
</script>