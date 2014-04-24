<? if (!$edit_mode) { 
		echo form_open("product/add_method", array('id' => 'product_form'));
	} else {
		echo form_open("product/edit_method", array('id' => 'product_form'));
	}
	
?>
<table border="1" width="50%" class="table-data ui-widget" cellpadding="3" align="center">
	<tr>
		<td class="ui-widget-header">Product Id</td>
		<td><input type="text" id="product_dtl_id" name="product_dtl_id" class="validate[required]" value="<?= $product_result['product_dtl_id'] ?>" <?= ($edit_mode) ? 'readonly="readonly"' : '' ?>  /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Production Code</td>
		<td><input type="text" id="production_code" name="production_code" class="validate[required]" value="<?= $product_result['product_display_id'] ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header" width="55%">ชื่อภาษาไทย</td>
		<td><input type="text" id="product_name_th" name="product_name_th" value="<?= htmlspecialchars($product_result['product_name_th']) ?>" class="validate[required]" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">ชื่อภาษาอังกฤษ</td>
		<td><input type="text" id="product_name_en" name="product_name_en" value="<?= htmlspecialchars($product_result['product_name_en']) ?>" class="validate[required]" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">ชื่อย่อ</td>
		<td><input type="text" id="product_name_initial" name="product_name_initial" value="<?= htmlspecialchars($product_result['product_name_initial']) ?>" class="validate[required]" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">พ่นสี</td>
		<td><input type="text" id="color" name="color" value="<?= $product_result['color'] ?>" class="validate[required]" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Thickness (mm)</td>
		<td><input type="text" id="thickness" name="thickness" class="numeric thickness validate[required]" value="<?= number_format($product_result['thickness'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Thickness Rep (mm)</td>
		<td><input type="text" id="thickness_rep" name="thickness_rep" class="numeric thickness validate[required]"  value="<?= number_format($product_result['thickness_rep'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Thickness min (mm)</td>
		<td><input type="text" id="thickness_min" name="thickness_min" class="numeric thickness validate[required]"  value="<?= number_format($product_result['thickness_min'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Size Detail (mm)</td>
		<td><input type="text" id="size_detail" name="size_detail"  value="<?= $product_result['size_detail'] ?>" class="validate[required]" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Film size (mm)</td>
		<td><input type="text" id="film_size" name="film_size"  value="<?= number_format($product_result['film_size'], 2) ?>"  /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Est. Weight (kg)</td>
		<td><input type="text" id="est_weight" name="est_weight" class="numeric validate[required]"  value="<?= number_format($product_result['est_weight'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Est. Weight min (kg)</td>
		<td><input type="text" id="est_weight_min" name="est_weight_min" class="numeric validate[required]"  value="<?= number_format($product_result['est_weight_min'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Actual Weight (kg)</td>
		<td><input type="text" id="actual_weight" name="actual_weight" class="numeric validate[required]" value="<?= number_format($product_result['actual_weight'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Accounting Weight (kg)</td>
		<td><input type="text" id="accounting_weight" name="accounting_weight" class="numeric validate[required]" value="<?= number_format($product_result['accounting_weight'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Weight display</td>
		<td><input type="text" id="weight_display" name="weight_display" class="validate[required]" value="<?= $product_result['weight_display'] ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Piece per Pack</td>
		<td><input type="text" id="piece_per_pack" name="piece_per_pack" class="numeric validate[required]" value="<?= number_format($product_result['piece_per_pack'], 0) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Piece per Truck</td>
		<td><input type="text" id="piece_per_truck" name="piece_per_truck" class="numeric validate[required]" value="<?= number_format($product_result['piece_per_truck'], 0) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Wage / kilo</td>
		<td><input type="text" id="wage_per_kilo" name="wage_per_kilo"class="numeric validate[required]"  value="<?= number_format($product_result['wage_per_kilo'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">% Films</td>
		<td><input type="text" id="perct_of_films" name="perct_of_films"class="numeric validate[required]"  value="<?= number_format($product_result['perct_of_films'], 2) ?>" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">In Production</td>
		<td><input type="checkbox" id="in_production" name="in_production" value="Y"  <?= ($product_result['in_production'] == "Y") ? 'checked="checked"' : "" ?> /></td>
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
	$("#cancelButton").click(onCancelButtonClick);
	$(".numeric").set_format({
		precision: 2,
		autofix:true,
		allow_negative:false
	});
	$("#product_form").validationEngine({
		scroll:false, 
		promptPosition : "centerRight",
		inlineValidation: false,
	});
	initAutoComplete();
	
	$("input").keypress(changeControlFocus);
	
	$("#product_dtl_id").select();
	
	$("#submitButton").click(function(){ $("#product_form").submit(); });
});
function changeControlFocus(event) {
	if (event.keyCode == 13) {
		if ($(this).attr("name") == "in_production") {
			$("#product_dtl_id").select();
		} else {
			$(this).focusNextInputField();
		}
	}
}
function initAutoComplete() {
	var thickness_list = <?= json_encode($thickness_result) ?>;
	$(".thickness").autocomplete({source: thickness_list});
}
function onCancelButtonClick() {
	document.location.replace("<?= site_url('/product') ?>");
}
</script>