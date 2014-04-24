<? if (!$edit_mode) { 
		echo form_open("order/add_method", array('id' => 'order_form'));
	} else {
		echo form_open("order/edit_method", array('id' => 'order_form'));
	}
?>
<input type="hidden" name="order_status" value="<?= $result['order_status'] ?>" />
<table border="1" width="53%" class="table-data ui-widget" cellpadding="3" align="center">
	<tr>
		<td class="ui-widget-header" align="right" width="40%">PO ID</td>
		<td><input type="text" id="po_id" name="po_id" class="validate[required]" value="<?= $po_id ?>" <? if($edit_mode) { echo 'readonly="readonly"'; } ?> /></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">วันที่สั่ง</td>
		<td><input type="text" id="my_date" name="order_received_date" readonly="readonly" value="<?= mysqldatetime_to_date($result['order_received_date'], 'd/m/Y') ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">ผู้จัดส่งสินค้า</td>
		<td><?= form_dropdown('supplier_id', $supplier_result, $result['supplier_id'], 'id="supplier_id"') ?></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">ความหนา</td>
		<td><?= form_dropdown("thickness", $thickness_result, $result["thickness"], 'id="thickness" style="width: 150px;"') ?></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">ความกว้าง</td>
		<td><?= form_dropdown("width", $width_result, $result["width"], 'id="width" style="width: 150px;"') ?></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">น้ำหนัก</td>
		<td><input type="text" id="weight" name="weight" class="numeric validate[required]" value="<?= $result['weight'] ?>" /> กิโลกรัม</td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">ราคา (ต่อกิโลกรัม)</td>
		<td><input type="text" id="price" name="price" class="numeric validate[required]" value="<?= $result['price'] ?>" /> บาท</td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">Base Price</td>
		<td><input type="text" id="price_base" name="price_base" class="numeric validate[required]" value="<?= $result['price_base'] ?>" /> บาท</td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">ระยะเวลาชำระเงิน</td>
		<td><?= form_dropdown('payment_term', $payment_result, $result['payment_term'], 'id="payment_term"');  ?></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">Vat.</td>
		<td><?
			$checkbox = array( 
				'name' => 'vat_status',
				'id' => 'vat_status',
				'value' => '1',
				'checked' => ($result['vat_status'] == '1'),
			);
			
			if (!$edit_mode) 
			{
				$checkbox['checked'] = 1;
			}
			
			echo form_checkbox($checkbox) 
		?></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">ราคาเต็ม</td>
		<td>
			<input type="text" id="amt_current_invoice_show" name="amt_current_invoice_show" value="0.0" readonly="readonly"/>
			<input type="hidden" id="amt_current_invoice" name="amt_current_invoice" value="0.0" readonly="readonly" />
		</td>
	</tr>
</table>
<table border="0" width="75%" align="center">
	<tr>
		<td align="center">
			<input type="button" id="submitButton" name="TSUBMIT" class="button" value="ตกลง" />
			<? if (!$edit_mode) { ?>
			<input type="reset" id="clearButton" name="TCLEAR" class="button" value="ล้าง" />
			<? } ?>
			<input type="button" id="cancelButton" name="TCANCEL" class="button" value="ยกเลิก" onclick='onCancelButtonClick()' />
		</td>
	</tr>
</table>
<?= form_close() ?>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#price, #weight").keyup(calculatePrice);
	$("#vat_status").click(calculatePrice);
	$("input").focus(function() {$(this).select(); });
	$("#my_date").datepicker({dateFormat:'dd/mm/yy', showAnim : ""});
	
	$("#po_id").select();
	$("#supplier_id").change(onSupplierIdSelect);
	$("#order_form").validationEngine({
		inlineValidation: false,
		scroll:false, 
		unbindEngine:true,
		success : false,
		promptPosition : "centerRight"
	});

	var z=dhtmlXComboFromSelect("thickness");
	var y=dhtmlXComboFromSelect("width");
	$(".dhx_combo_input").addClass("numeric");
	
	$("input[type!='hidden'], select").keypress(changeControlFocus);
	$("#submitButton").click(function() { $("#order_form").submit(); });
	
	$(".numeric").set_format({precision: 2,autofix:true,allow_negative:false});
	calculatePrice();
});
function changeControlFocus(event) {
	if (event.keyCode == 13) {
		if ($(this).attr("name") == "order_received_date") {
			$("#my_date").datepicker("hide");
			$("#supplier_id").focus();
		} else if ($(this).attr("name") == "amt_current_invoice_show") {
			$("#po_id").select();
		}else {
			if ($(this).hasClass("dhx_combo_input")) {
				if ($(this).next().attr("name") == "width") {
					$("#weight").select();
				} else {
					$(".dhx_combo_input:last").select();
				}
			} else {
				$(this).focusNextInputField();
			}
		}
	}
}
function onCancelButtonClick() {
	document.location.replace("<?= site_url('order') ?>");
}
function onSupplierIdSelect() {
	var default_payment_term = <?= json_encode($payment_map_result) ?>;
	$("#payment_term").val(default_payment_term[$(this).val()]);
}
function calculatePrice() {
	var price = parseFloat($("#price").val());
	if (isNaN(price)) { price = 0; }
	var fullprice = 0.0;
	var vat = $("#vat_status").attr("checked");
	if (vat) {
		fullprice = price * <?= $vat ?>;
	} else {
		fullprice = price * <?= $premium ?>;
	}
	$("#amt_current_invoice").val(fullprice);
	$("#amt_current_invoice_show").val(fullprice.toFixed(2));
}
</script>