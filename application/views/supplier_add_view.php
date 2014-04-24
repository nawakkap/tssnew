<? if (!$edit_mode) { 
		echo form_open("supplier/add_method", array('id' => 'supplier_form'));
	} else {
		echo form_open("supplier/edit_method", array('id' => 'supplier_form'));
	}
?>
<input type="hidden" name="register_date" value="<?= $register_date ?>" />
<input type="hidden" name="supplier_id" value="<?= $supplier_id ?>" />
<table border="1" width="53%" class="table-data ui-widget" cellpadding="3" align="center">
	<tr>
		<td class="ui-widget-header" align="right">ชื่อ - นามสกุล</td>
		<td><input type="text" id="supplier_name" name="supplier_name" class="validate[required]" value="<?= $result['supplier_name'] ?>" /></td>
	</tr>
	<tr>
	<tr>
		<td class="ui-widget-header" align="right">เครดิต</td>
		<td><?= form_dropdown('default_payment_term', $payment_result, $result['default_payment_term']);?></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">ที่อยู่ 1</td>
		<td><input type="text" id="address_1" name="address_1" value="<?= $result['address_1'] ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">ที่อยู่ 2</td>
		<td><input type="text" id="address_2" name="address_2" value="<?= $result['address_2'] ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">รหัสไปรษณีย์</td>
		<td><input type="text" id="address_postcode" name="address_postcode" value="<?= $result['address_postcode'] ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">เบอร์โทรศัพท์</td>
		<td><input type="text" id="tel_phone" name="tel_phone" class="validate[required]" value="<?= $result['tel_phone'] ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">เบอร์โทรศัพท์มือถือ</td>
		<td><input type="text" id="tel_mobile" name="tel_mobile" value="<?= $result['tel_mobile'] ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="right">E-Mail</td>
		<td><input type="text" id="email" name="email" value="<?= $result['email'] ?>" /></td>
	</tr>
</table>
<table border="0" width="75%" align="center">
	<tr>
		<td align="center">
			<input type="button" id="submitButton" name="TSUBMIT" class="button" value="ตกลง" />
			<? if (!$edit_mode) { ?>
			<input type="reset" id="clearButton" name="TCLEAR" class="button" value="ล้าง" />
			<? } ?>
			<input type="button" id="cancelButton" name="TCANCEL" class="button" value="ยกเลิก" />
		</td>
	</tr>
</table>
<?= form_close() ?>
<script type="text/javascript">
$(document).ready(function() {
	$(".button").button();
	$("#supplier_form").validationEngine({
		scroll:false, 
		promptPosition : "centerRight",
		inlineValidation: false,
	});
	$("#cancelButton").click(onCancelButtonClick);
	
	$("#supplier_name").select();
	$("input, select").keypress(changeControlFocus);

	$("#submitButton").click(function() { $("#supplier_form").submit(); });
});

function changeControlFocus(event) {
	if (event.keyCode == 13) {
		if ($(this).attr("name") == "email") {
			$("#supplier_name").select();
		} else {
			$(this).focusNextInputField();
		}
	}
}
function onCancelButtonClick() {
	document.location.replace("<?= site_url("/supplier") ?>");
}
</script>