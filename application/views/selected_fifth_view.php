<table border="0" width="100%" cellpadding="4" class="ui-widget">
	<tr>
		<td align="left" class="ui-state-highlight" width="20%" nowrap="nowrap"><b>5. ข้อมูลรายละเอียด</b>&nbsp;&nbsp;&nbsp;<input type="button" name="mainButton" class="button mainButton" value="กลับสู่หน้าหลัก"/></td>
		<td>&nbsp;</td>
	</tr>
</table><br/>

<?= form_open("/select_coil/sixth_step", array("id" => "my_form")) ?>
<!-- <input type="hidden" name="width" value="<?php /* echo $width; */ ?>" /> -->
<input type="hidden" name="thickness" value="<?= $thickness ?>"/>
<input type="hidden" name="po_id" value="<?= $po_id ?>"/>
<? foreach($product_result as $item) { if (empty($item)) continue; ?>
<input type="hidden" name="product_dtl_id[]" value="<?= $item["product_dtl_id"] ?>" />
<? } ?>
<input type="hidden" name="slit_spec_id" value="<?= $slit_spec_id ?>"/>
<? for($i = 0; $i < count($coil_id); $i++) { ?>
<input type="hidden" name="coil_id[]" value="<?= $coil_id[$i] ?>"/>
<? } ?>

<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center">Product</th>
			<th align="center">Vat</th>
			<th align="center">ค่าแรง</th>
			<th align="center">Ext. Program code</th>
			<th align="center">เครื่องรีด</th>
		</tr>
	</thead>
	<tbody>
	<?
		$i = 0;
		foreach($product_result as $item) { 
	?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
			<td><?= $item["product_name_th"] ?></td>
			<td><input type="checkbox" name="vat<?= $item["product_dtl_id"] ?>" value="VAT" <?= ($item["default_vat"]==1) ? "checked=\"checked\"" : "" ?> /></td>
			<td><input type="text" id="wage<?= $item["product_dtl_id"] ?>" name="wage<?= $item["product_dtl_id"] ?>" class="wage numeric data validate[required]" value="<?= $item["wage_per_kilo"] ?>" size="7" /></td>
			<td>
				<input type="text" name="external_program_code<?= $item["product_dtl_id"] ?>" value="<?= $external_program_code[$item["product_dtl_id"]] ?>" <? if ($external_program_code[$item["product_dtl_id"]] !== "XXX") { echo 'readonly="readonly"'; } ?> />
				<input type="hidden" name="program<?= $item["product_dtl_id"] ?>" value="<?= $internal_program_code[$item["product_dtl_id"]] ?>" />
			</td>
			<td>
			<select name="machine<?= $item["product_dtl_id"] ?>">
				<option value=""></option>
			<? for($i = 0; $i < count($machine); $i++) { ?>
				<option value="<?= $machine[$i]["mc_id"] ?>"><?= $machine[$i]["machine_name"] ?></option>
			<? } ?>
			</select>
			</td>
		</tr>
	<? $i++; } ?>
	</tbody>
</table><br/>
<table border="0" width="100%">
	<tr>
		<td align="left"><input type="reset" id="backButton" name="TReset" class="button" value="ย้อนกลับ" /></td>
		<td align="right"><input type="button" id="submitButton" name="Tsubmit" class="button" value="ถัดไป" /></td>
	</tr>
</table>
<?= form_close() ?>

<?= form_open("/select_coil/fourth_step", array("id" => "backform")) ?>
<input type="hidden" name="thickness" value="<?= $thickness ?>"/>
<input type="hidden" name="po_id" value="<?= $po_id ?>"/>
<? for($i =0 ; $i < count($product_dtl_id); $i++) { if (empty($product_dtl_id[$i])) continue; ?>
<input type="hidden" name="product_dtl_id[]" value="<?= $product_dtl_id[$i] ?>" />
<? } ?>
<input type="hidden" name="slit_spec_id" value="<?= $slit_spec_id ?>"/>
<? for($i = 0; $i < count($coil_id); $i++) { ?>
<input type="hidden" name="coil_id[]" value="<?= $coil_id[$i] ?>"/>
<? } ?>
<?= form_close() ?>
<div id="warning" title="info" style="display:none">กรุณาใส่ข้อมุลให้ครบ</div>
<br/><br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#backButton").click(onBackButtonClick);
	$(".program:first").select();
	$(".numeric").set_format({precision: 2,autofix:true,allow_negative:false});
	$(".mainButton").click(onMainButtonClick);
	
	$("#my_form").validationEngine({
		inlineValidation: false,
		scroll:false, 
		unbindEngine:true,
		promptPosition : "centerRight"
	});
	
	$("#submitButton").click(function() { $("#my_form").submit(); });
	
	$("input").keypress(changeControlFocus);
});
function changeControlFocus(event) {
	if (event.keyCode == 13) {
		if ($(this).attr("name") == $(".program:last").attr("name")) {
			$(".data:first").select();
		} else {
			$(this).focusNextInputField();
		}
	}
}
function onMainButtonClick(){
	document.location.replace("<?= site_url("/main") ?>");
}
function onBackButtonClick() {
	$("#backform").submit();
}
</script>