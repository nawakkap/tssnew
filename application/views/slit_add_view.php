<? if (!$edit_mode) { 
		echo form_open("slit/add_method", array('id' => 'slit_form'));
	} else {
		echo form_open("slit/edit_method", array('id' => 'slit_form'));
	}
?>
<input type="hidden" name="slit_spec_id" value="<?= $slit_spec_id ?>" />
<table border="1" width="60%" class="table-data ui-widget" cellpadding="3" align="center">	
	<tr>
		<td class="ui-widget-header" align="right">ความหนา</td>
		<td><input type="text" id="slit_thickness" name="slit_thickness" class="thickness validate[required] numeric" value="<?= $slit_thickness ?>" /></td>
	</tr>
	<tr>
</table>
<br />
<table border="1" width="60%" class="table-data ui-widget" align="center">
	<thead>
		<tr class="ui-widget-header">
			<th align="center">ความกว้าง</th>
			<th align="center">จำนวน</th>
			<th align="center">Product</th>
			<th align="center">Ratio</th>
		</tr>
	</thead>
	<tbody>
		<tr class="odd">
			<td align="center"><input type="text" id="slit_width_1" name="slit_width_1" class="width validate[required] numeric calculate" value="<?= $result[0]['slit_width'] ?>" /></td>
			<td align="center"><input type="text" id="slit_qty_1" name="slit_qty_1" class="validate[required] numeric calculate" value="<?= $result[0]['slit_qty'] ?>" /></td>
			<td align="center">
				<select id="product_dtl_id1" name="product_dtl_id1" class="validate[required]">
					<option value=""></option>
				<? for($i = 0; $i < count($product_result); $i++) { ?>
					<option value="<?= $product_result[$i]->product_dtl_id ?>"><?= $product_result[$i]->product_name_en ?></option>
				<? } ?>
				</select>
			</td>
			<td><input type="text" id="ratio_1" name="ratio_1" value="" /></td>
		</tr>
		<tr class="even">
			<td align="center"><input type="text" id="slit_width_2" name="slit_width_2" class="width numeric calculate" value="<?= (isset($result[1])) ?  $result[1]['slit_width'] : "0" ?>" /></td>
			<td align="center"><input type="text" id="slit_qty_2" name="slit_qty_2" class="numeric calculate" value="<?= (isset($result[1])) ? $result[1]['slit_qty'] : "0" ?>" /></td>
			<td align="center">
				<select id="product_dtl_id2" name="product_dtl_id2">
					<option value=""></option>
				<? for($i = 0; $i < count($product_result); $i++) { ?>
					<option value="<?= $product_result[$i]->product_dtl_id ?>"><?= $product_result[$i]->product_name_en ?></option>
				<? } ?>
				</select>
			</td>
			<td><input type="text" id="ratio_2" name="ratio_2" value="" /></td>
		</tr>
		<tr class="odd">
			<td align="center"><input type="text" id="slit_width_3" name="slit_width_3" class="width numeric calculate" value="<?= (isset($result[2])) ? $result[2]['slit_width'] : "0" ?>" /></td>
			<td align="center"><input type="text" id="slit_qty_3" name="slit_qty_3" class="numeric calculate" value="<?= (isset($result[2])) ? $result[2]['slit_qty'] : "0" ?>" /></td>
			<td align="center">
				<select id="product_dtl_id3" name="product_dtl_id3">
					<option value=""></option>
				<? for($i = 0; $i < count($product_result); $i++) { ?>
					<option value="<?= $product_result[$i]->product_dtl_id ?>"><?= $product_result[$i]->product_name_en ?></option>
				<? } ?>
				</select>
			</td>
			<td><input type="text" id="ratio_3" name="ratio_3" value="" /></td>
		</tr>
	</tbody>
</table>
<br/>
<table border="1" width="60%" class="table-data ui-widget" align="center">
<tr>
	<td class="ui-widget-header">RIM</td>
	<td><input type="text" name="rim" class="validate[required]" value="<?= $result[0]['rim'] ?>"  /></td>
</tr>
</table>
<br/>
<table border="1" width="60%" class="table-data ui-widget" align="center">	
	<tr>
		<td class="ui-widget-header">Description</td>
	</tr>
	<tr>
		<td align="center"><textarea id="description" name="description" style="width: 495px " class="validate[required]"><?= $result[0]['remark'] ?></textarea></td>
	</tr>
</table>
<br/>
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
	initAutoComplete();
	$(".numeric").set_format({precision: 2,autofix:true,allow_negative:false});
	$("#slit_form").validationEngine({
		scroll:false, 
		promptPosition : "topRight", 
		inlineValidation: false,
	});
	$("#product_dtl_id1").val("<?= (isset($result[0])) ? $result[0]['product_dtl_id'] : "" ?>");
	$("#product_dtl_id2").val("<?= (isset($result[1])) ? $result[1]['product_dtl_id'] : "" ?>");
	$("#product_dtl_id3").val("<?= (isset($result[2])) ? $result[2]['product_dtl_id'] : "" ?>");
	$(".calculate").keyup(calculatePrice);
	calculatePrice();
	
	$("input, select").keypress(changeControlFocus);
	
	$("#slit_thickness").select();
	
	$("#submitButton").click(function() { $("#slit_form").submit(); });
});
function changeControlFocus(event) {
	if (event.keyCode == 13) {
		$(this).focusNextInputField();
	}
}


function initAutoComplete() {
	var thickness_list = <?= json_encode($thickness_result) ?>;
	$(".thickness").autocomplete({source: thickness_list});
	var width_list = <?= json_encode($width_result) ?>;
	$(".width").autocomplete({source: width_list});
}
function onCancelButtonClick() {
	document.location.replace("<?= site_url('/slit') ?>");
}
function calculatePrice() {
	var slit_width_1 = isNaN(parseFloat($("#slit_width_1").val())) ? 0 : parseFloat($("#slit_width_1").val());
	var slit_qty_1 = isNaN(parseFloat($("#slit_qty_1").val())) ? 0 : parseFloat($("#slit_qty_1").val());
	$("#ratio_1").val( (((slit_width_1 * slit_qty_1) / 1228) * 100).toFixed(1) );
	
	var slit_width_2 = isNaN(parseFloat($("#slit_width_2").val())) ? 0 : parseFloat($("#slit_width_2").val());
	var slit_qty_2 = isNaN(parseFloat($("#slit_qty_2").val())) ? 0 : parseFloat($("#slit_qty_2").val());
	$("#ratio_2").val( (((slit_width_2 * slit_qty_2) / 1228) * 100).toFixed(1) );
	
	var slit_width_3 = isNaN(parseFloat($("#slit_width_3").val())) ? 0 : parseFloat($("#slit_width_3").val());
	var slit_qty_3 = isNaN(parseFloat($("#slit_qty_3").val())) ? 0 : parseFloat($("#slit_qty_3").val());
	$("#ratio_3").val( (((slit_width_3 * slit_qty_3) / 1228) * 100).toFixed(1) );
}
</script>