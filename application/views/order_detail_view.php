<style type="text/css" rel="stylesheet">
.slitted {
	background-color: #CCCCCC;
}
</style>
<?= form_open('/order/update_status',  'id="order_form"') ?>
<input type="hidden" name="po_id" value="<?= $order_result['po_id'] ?>" />
<div id="showPanel">
<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td align="left">&nbsp;&nbsp;PO ID :&nbsp;<?= $order_result['po_id'] ?></td>
		<td align="right">
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />
		</td>
	</tr>
</table>
<br/>
<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>&nbsp;&nbsp;รายละเอียดของ Order</td>
		<td align="right"><?= image_asset("minus_sign.png", '', array('border' => 0, 'id' => 'order_detail_minus')) ?>&nbsp;</td>
	</tr>
</table>
<table id="order-detail-table" border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr>
		<th class="ui-state-highlight" width="25%">PO ID</th>
		<td width="25%"><?= $order_result['po_id'] ?></td>
		<th class="ui-state-highlight" width="25%">วันที่สั่งสินค้า</th>
		<td width="25%"><?= mysqldatetime_to_date($order_result['order_received_date'], 'd/m/Y') ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight">ผู้จัดส่งสินค้า</th>
		<td><a href="<?= site_url("/supplier/supplier_detail/" . $order_result['supplier_id'] ) ?>" class="link"><?= (isset($supplier_result[$order_result['supplier_id']])) ? $supplier_result[$order_result['supplier_id']] : "&nbsp;" ?></a></td>
		<th class="ui-state-highlight">เครดิต</th>
		<td><?= (isset($payment_result[$order_result['payment_term']])) ? $payment_result[$order_result['payment_term']] : "&nbsp;" ?></td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<th class="ui-state-highlight">ความหนา</th>
		<td><?= number_format($order_result['thickness'], 2) ?>&nbsp;มิลลิเมตร</td>
		<th class="ui-state-highlight">ความกว้าง</th>
		<td><?= number_format($order_result['width'], 2) ?>&nbsp;มิลลิเมตร</td>
	</tr>
	<tr>
		<th class="ui-state-highlight">น้ำหนัก</th>
		<td><?= number_format($order_result['weight'], 0) ?>&nbsp;กิโลกรัม</td>
		<th class="ui-state-highlight">น้ำหนักส่งมาแล้ว</th>
		<td><?= number_format($order_result['weight'] - $accrual_weight, 0) ?> กิโลกรัม</td>
	</tr>
	<tr>
		<th class="ui-state-highlight">น้ำหนักค้างส่ง</th>
		<td><b><?= number_format($accrual_weight, 0) ?> กิโลกรัม</b></td>
		<th>&nbsp;</th>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<th class="ui-state-highlight">ราคา (ต่อกิโลกรัม)</th>
		<td><?= number_format($order_result['price'], 2) ?>&nbsp;บาท</td>
		<th class="ui-state-highlight">Base price</th>
		<td><?= number_format($order_result['price_base'], 2) ?>&nbsp;บาท</td>
	</tr>
	<tr>
		<th class="ui-state-highlight">ราคาเต็ม</th>
		<td><?= number_format($order_result['amt_current_invoice'], 2) ?>&nbsp;บาท</td>
		<th class="ui-state-highlight">Vat Status</th>
		<td><?= ($order_result['vat_status'] == "1") ? "มี" : "ไม่มี" ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight">Invoice Amount</th>
		<td><b id="invoice">0.00</b><b>&nbsp;บาท</b></td>
		<th class="ui-state-highlight">สถานะ</th>
		<td>
			<?= form_dropdown('order_status', $order_status_result, $order_result['order_status']) ?>
			<input type="submit" id="changeButton" name="TSUBMIT" value="เปลี่ยน" />
		</td>
	</tr>
</table>
</div>
<?= form_close() ?>
<br/>
<?= form_open("/coil/add_method", array("id" => "coil_form")) ?>
<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>&nbsp;&nbsp;รายการ Coil</td>
		<td align="right">
		<? if (isset($order_result['po_id'])) { ?>
			<input type="button" id="coilEditButton" name="coilEditButton" class="button" value="แก้ไข" />
			<input type="button" id="coilSaveButton" name="TSUBMIT" class="button" value="บันทึก" />
			<input type="button" id="coilCancelButton" name="coilCancelButton" class="button" value="ยกเลิก" />
		<? } ?>
		</td>
	</tr>
</table>
<table border="1" width="100%" id="coil-table" class="table-data ui-widget">
	<thead>
	<tr class="ui-state-highlight">
		<th align="center" width="77">No.</th>
		<th align="center" width="168">ความหนา (มิลลิเมตร)</th>
		<th align="center" width="175">ความกว้าง (มิลลิเมตร)</th>
		<th align="center" width="114">COIL ID</th>
		<th align="center" width="142">น้ำหนัก (กิโลกรัม)</th>
		<th align="center" width="116">วันที่รับสินค้า</th>
		<th align="center" width="">สถานะ</th>
		<th width="55">&nbsp;</th>
	</tr>
	</thead>
	<tbody>
		<?
			$total_weight = 0.0;
			for($i = 0; $i < count($coil_result); $i++) { 
				$total_weight += $coil_result[$i]['weight'];
		?>
		<tr height="32" class="<?= ($i % 2 == 1) ? "even" : "odd" ?>">
			<td align="center"><?= $coil_result[$i]['coil_lot_no'] ?></td>
			<td align="center"><?= number_format($coil_result[$i]['thickness'], 2) ?></td>
			<td align="center"><?= number_format($coil_result[$i]['width'], 2) ?></td>
			<td align="center"><a href="<?= site_url("/coil/coil_detail/" . $this->convert->AsciiToHex($coil_result[$i]['coil_id']) . "/" . $coil_result[$i]['coil_lot_no'] . "/" . $this->convert->AsciiToHex($order_result['po_id'])) ?>" class="link"><?= $coil_result[$i]['coil_id'] ?></a></td>
			<td align="center"><?= number_format($coil_result[$i]['weight'], 0) ?></td>
			<td align="center"><?= mysqldatetime_to_date($coil_result[$i]['coil_received_date'], "d/m/Y") ?></td>
			<td align="center"><?= $coil_status_result[$coil_result[$i]['coil_status']] ?></td>
			<td align="center">&nbsp;</td>
		</tr>
		<? } ?>
	</tbody>
</table>
<table border="1" width="100%" id="coil-edit-table" class="table-data ui-widget">
	<thead>
	<tr class="ui-state-highlight">
		<th align="center" size="77">No.</th>
		<th align="center" size="168">ความหนา (มิลลิเมตร)</th>
		<th align="center" size="175">ความกว้าง (มิลลิเมตร)</th>
		<th align="center" size="114">COIL ID</th>
		<th align="center" size="142">น้ำหนัก (กิโลกรัีม)</th>
		<th align="center" size="116">วันที่รับสินค้า</th>
		<th align="center" size="">สถานะ</th>
		<th width="55">
			<input type="hidden" name="po_id" value="<?= $order_result['po_id'] ?>" />
			<input type="hidden" name="rowCount" class="rowCount" value="" />
		</th>
	</tr>
	</thead>
	<tbody id="sortable">
		<?
			for($i = 0; $i < count($coil_result); $i++) { 
				$slitted = "";
				if ($coil_result[$i]['coil_status'] == '2') { // Slitted => Input is readonly
					$slitted = 'readonly="readonly"';
				}
			
		?>
		<tr height="32" class="editrow <?= ($slitted) ? 'ui-state-disabled notmove' : '' ?>">
			<td align="center"><input type="text" <?= ($slitted) ? 'disabled="disabled"' : '' ?> name="coil_lot_no" value="<?= $coil_result[$i]['coil_lot_no'] ?>" size="5" class="coil_lot_no numeric"/></td>
			<td align="center"><input type="text" <?= ($slitted) ? 'disabled="disabled"' : '' ?> name="thickness" value="<?= $coil_result[$i]['thickness'] ?>" size="7" class="thickness numeric" /></td>
			<td align="center"><input type="text" <?= ($slitted) ? 'disabled="disabled"' : '' ?> name="width" value="<?= $coil_result[$i]['width'] ?>" size="7" class="width numeric" /></td>
			<td align="center"><input type="text" <?= ($slitted) ? 'disabled="disabled"' : '' ?> name="coil_id" value="<?= $coil_result[$i]['coil_id'] ?>" class="coil_id" size="10" /></td>
			<td align="center"><input type="text" <?= ($slitted) ? 'disabled="disabled"' : '' ?> name="weight" class="numeric_weight" value="<?= $coil_result[$i]['weight'] ?>" size="10" /></td>
			<td align="center"><input type="text" <?= ($slitted) ? 'disabled="disabled"' : '' ?> name="coil_received_date" readonly="readonly" class="coil_received_date_edit" size="10" value="<?= mysqldatetime_to_date($coil_result[$i]['coil_received_date'], "d/m/Y") ?>" /></td>
			<td align="center"><?= $coil_status_result[$coil_result[$i]['coil_status']] ?></td>
			<td align="center"><input type="button" name="deleteButton" class="deleteButton" value="ลบ" <?= ($slitted) ? 'disabled="disabled"' : '' ?> /></td>
		</tr>
		<? } ?>
		<tr height="32" class="editrow notmove">
			<td align="center"><input type="text" name="coil_lot_no" value="" size="5" class="coil_lot_no numeric" /></td>
			<td align="center"><input type="text" name="thickness" value="<?= $order_result['thickness'] ?>" size="7" class="thickness numeric" /></td>
			<td align="center"><input type="text" name="width" value="<?= $order_result['width'] ?>" size="7" class="width numeric" /></td>
			<td align="center"><input type="text" name="coil_id" value="" size="10" class="input coil_id" /></td>
			<td align="center"><input type="text" name="weight" class="input weight numeric_weight" value="0" size="10" /></td>
			<td align="center"><input type="text" name="coil_received_date" readonly="readonly" size="10" class="coil_received_date_edit" value="<?= date('d/m/Y') ?>" /></td>
			<td align="center">&nbsp;</td>
			<td align="center"><input type="button" name="deleteButton" class="deleteButton" value="ลบ" /></td>
		</tr>
	</tbody>
</table>
<br/>
<?= form_close() ?>
<style type="text/css" rel="stylesheet">
#changeButton {font-size:10pt;font-family:Tahoma;margin:0;padding:2px;line-height:20px;}
.deleteButton {font-size:10pt;font-family:Tahoma;margin:0;padding:0;line-height:20px;}
</style>
<br/><br/><br/>
<div id="pleasewait-panel" title="Information" style="display:none">กรุณารอสักครู่</div>
<script type="text/javascript">
var initEditTable = null;
$(document).ready(function() {
	$(".button, #changeButton").button();
	$("#coilSaveButton, #editForm, #coil-edit-table, #coilCancelButton").hide();
	$("#coilEditButton").click(onCoilEditPanel);
	$("#coilCancelButton").click(onCoilCancelButtonClick);
	$("#backButton").click(onBackButtonClick);
	$("#editButton").click(showEditPanel);
	$("#okButton, #cancelButton").click(showDetailPanel);
	$("#editButton").hide();
	$("#coil-edit-table").tableAutoAddRow({autoAddRow: true, displayRowCountTo : "rowCount", inputBoxAutoNumber: true}, onAddRow);
	$(".deleteButton").button().btnDelRow(onDeleteRow);
	$(".coil_received_date_edit").datepicker({dateFormat:'dd/mm/yy', showAnim : ""});
	$(".numeric_weight").set_format({precision: 0,allow_negative:false});
	$(".numeric").set_format({precision: 2,autofix:true,allow_negative:false});
	$("#order_detail_minus").toggle(onMinusClick, onPlusClick).css("cursor", "pointer");
	$("#order_form").submit(onOrderFormSubmit);
	initEditTable = $("#coil-edit-table").clone(true);
	$(".coil_lot_no:last").val($(".coil_lot_no").size());
	calculateInvoice();
	$("#coilSaveButton").click(onSaveButtonClick);
	
	$(".coil_received_date_edit").blur(onBlur);
	
	$(".coil_id, .numeric_weight").keypress(onChangeFocus);
});
function populateNo() {

	$.each($(".editrow"), function(index) {
		$(this).find(".coil_lot_no").val(index + 1); // Change Value
		$(this).find(".coil_lot_no").attr("name", "coil_lot_no" + (index + 1)); // Change Name
		$(this).find(".thickness").attr("name", "thickness" + (index + 1)); // Change Name
		$(this).find(".width").attr("name", "width" + (index + 1)); // Change Name
		$(this).find(".coil_id").attr("name", "coil_id" + (index + 1)); // Change Name
		$(this).find(".numeric_weight").attr("name", "weight" + (index + 1)); // Change Name
		$(this).find(".coil_received_date_edit").attr("name", "coil_received_date" + (index + 1)); // Change Name
		$(this).find(".deleteButton").attr("name", "deleteButton" + (index + 1)); // Change Name
	});

	/*
	$.each($(".coil_lot_no"), function(index) {
		$(this).val(index + 1);
		
		// Populate Control Name.
		
	});
	*/
}
function onBlur() {
	$(this).datepicker("hide");
}
function onSaveButtonClick() {

	// Remove all disabled controls
	$("input").removeAttr("disabled");

	var coil_id_no = check_coil_lot_no();
	//alert(coil_id_no);
	if (coil_id_no != true) {
		$("#pleasewait-panel").text("มีรายการ Coil บางรายการที่ No ซ้ำกัน.");
		$("#pleasewait-panel").dialog("destroy").dialog({
			modal: true,
		});
	
		$(".coil_lot_no").removeClass("ui-state-highlight");
		$(".coil_lot_no[value=" + coil_id_no + "]").addClass("ui-state-highlight");
		return false;
	}
	
	var coil_id = check_coil_id();
	//alert(coil_id);
	if (coil_id != true) {
		$("#pleasewait-panel").text("มีรายการ Coil บางรายการที่ Id ซ้ำกัน.");
		$("#pleasewait-panel").dialog("destroy").dialog({
			modal: true,
		});
		
		$(".coil_id").removeClass("ui-state-highlight");
		$(".coil_id[value=" + coil_id + "]").addClass("ui-state-highlight");
		return false;
	}

	$("#pleasewait-panel").text("โปรดรดสักครู่");
	$("#pleasewait-panel").dialog("destroy").dialog({
		modal: true
	});

	$("#coil_form").submit();
}
function onChangeFocus(event) {
	if (event.keyCode == 13) {
		if ($(this).hasClass("numeric_weight")) {
			var index = $(".numeric_weight").index($(this));
			$(".coil_id:eq(" + (index + 1) + ")").select();
		} else if ($(this).hasClass("coil_id")) {
			var index = $(".coil_id").index($(this));
			$(".numeric_weight:eq(" + index + ")").select();
		}
	}
}
function calculateInvoice() {
	var fullprice = <?= $order_result['amt_current_invoice'] ?>;
	if (isNaN(fullprice)) fullprice= 0.0;
	var amount = fullprice * <?= $total_weight ?>;
	$("#invoice").text(amount).format();
}
function onMinusClick() {
	$("#order-detail-table").hide("blind");
}
function onPlusClick() {
	$("#order-detail-table").show("blind");
}
function onOrderFormSubmit() {
	$("#pleasewait-panel").dialog({modal: true});
}
function onCoilFormSubmit() {
}
function check_coil_lot_no()  {
	var temp = new Array();
	var result_flag = true;
	$.each($(".coil_lot_no"), function(index) {
		var value = $.trim($(this).val());
		if (value != "") {
			if ($.inArray(value, temp) == -1) {
				temp.push(value);
				$.unique( temp );
			} else {
				result_flag = value;
			}
		}
	});
	return result_flag;
}
function check_coil_id() {
	var temp = new Array();
	var result_flag = true;
	$.each($(".coil_id"), function(index) {
		//alert($(this).val());
		var value = $.trim($(this).val());
		if (value != "") {
			if ($.inArray(value, temp) == -1) {
				temp.push(value);
				$.unique( temp );
			} else {
				result_flag = value;
			}
		}
	});
	return result_flag;
}
function onCoilEditPanel() {
	$("#coil-table").hide();
	$("#coilEditButton").hide();
	$("#coilSaveButton").show();
	$("#coilCancelButton").show();
	$("#coil-edit-table").show();
	
	$(".coil_id:not(:disabled):first").select();
	
	$("#sortable").sortable({
		items : "tr:not(.notmove)",
		stop: function(event, ui) {
			populateNo();
		}
	});
	$("#sortable").disableSelection();
}
function onCoilCancelButtonClick() {
	$("#coil-table").show();
	$("#coilEditButton").show();
	$("#coilSaveButton").hide();
	$("#coilCancelButton").hide();
	$("#coil-edit-table").hide();
	$("#coil-edit-table").remove();
	initEditTable.insertAfter($("#coil-table"));
	initEditTable = $("#coil-edit-table").clone(true);
}
function onBackButtonClick() {
	<? if ($history === "1") { ?>
		document.location.replace("<?= site_url("/order/history") ?>");
	<? } else { ?>
		document.location.replace("<?= site_url("/order") ?>");
	<? } ?>
}
function showDetailPanel() {
	$("#showPanel").show();
	$("#editForm").hide();
}
function showEditPanel() {
	$("#showPanel").hide();
	$("#editForm").show();
}
function onAddRow(row) {

	$(".numeric").set_format({precision: 2,autofix:true,allow_negative:false});
	$(".numeric_weight").set_format({precision: 0,allow_negative:false});
	$(".coil_received_date_edit").removeClass("hasDatepicker").datepicker({dateFormat:'dd/mm/yy', showAnim : ""});
	var val = parseFloat($(".coil_lot_no:eq(" + ($(".coil_lot_no").size() - 2) + ")").val());
	if (isNaN(val)) {
		val = 1;
	} else {
		val++;
	}
	$(".coil_lot_no:last").val(val);
	$(".thickness:last").val("<?= $order_result['thickness'] ?>");
	$(".width:last").val("<?= $order_result['width'] ?>");
	
	var index = $(".coil_received_date_edit").size();
	var date_value = $(".coil_received_date_edit:eq(" + (index - 2) + ")").val();
	
	$(".coil_received_date_edit:last").val(date_value);
	
	$('html, body').animate({scrollTop: $("html,body").attr("scrollHeight")}, 200);
	//$('html, body').animate({scrollTop: row.offset().top}, 200);
	
	$(".coil_id, .numeric_weight").unbind("keypress").keypress(onChangeFocus);
	
	
	// Populate Class Name for all 
	var size = $(".editrow").size();
	$.each($(".editrow"), function(index) {
		if ($(this).hasClass("ui-state-disabled")) {
			// Nothing to do.
		} else if ($(this).hasClass("notmove")) {
			if (index == size - 2) { // After add row, remove "notmove" class
				$(this).removeClass("notmove");
			}
		} else {
			// Nothing to do.
		}
	});
}
function onDeleteRow() {
	$.each($(".coil_lot_no"), function(index) {
		$(this).val(index + 1);
	});
}
</script>