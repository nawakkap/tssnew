<!-- Select the group code -->
<table border="1" width="100%" cellpadding="4" class="table-data ui-widget" align="center">
	<thead>
		<tr class="ui-widget-header">
			<th align="center">ชื่อกลุ่ม</th>
		</tr>
	</thead>
	<tbody>
	<tr>
		<td align="center"><?= $group_code ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<!-- Deprecated
	<tr>
		<td class="ui-widget-header" align="center">ค่าความกว้างที่ใช้หาร</td>
	</tr>
	<tr>
		<td align="center"><input type="text" id="width_temp" name="width" class="numeric" value="<?php /* echo $width; */ ?>" size="5" /></td>
	</tr>
	-->
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr class="ui-widget-header">
		<td>กรุณาเลือก Slit Spec</td>
	</tr>
	<tr>
		<td align="center">
			<select id="slit_spec_choose">
				<option value=""></option>
			<? 
				$slit_spec_mapping = array();
				
				for($i = 0; $i < count($slit_spec_result) ; $i++) { 
					if (!$slit_spec_result[$i]['remark']) continue;
				
				/*
					$slit_id = $slit_spec_result[$i]->slit_spec_id;
					
					$slit_message = "";
					
					$slit_thickness = FALSE;
					if (isset($slit_spec_result[$i]->slit_thickness)) {
						$slit_message .= number_format($slit_spec_result[$i]->slit_thickness, 2);
						$slit_thickness = TRUE;
					}
					
					if (isset($slit_spec_result[$i]->slit_dtl_1)) {
						if ($slit_thickness) {
							$slit_message .= " | ";
							$slit_thickness = FALSE;
						}
						$slit_message .= " ( " . $slit_spec_result[$i]->slit_dtl_1 ." ) ";
					}
					
					if (isset($slit_spec_result[$i]->slit_dtl_2)) {
						if ($slit_thickness) {
							$slit_message .= " | ";
							$slit_thickness = FALSE;
						}
						$slit_message .= " ( " . $slit_spec_result[$i]->slit_dtl_2 . " ) ";
					}
					
					if (isset($slit_spec_result[$i]->slit_dtl_3)) {
						if ($slit_thickness) {
							$slit_message .= " | ";
							$slit_thickness = FALSE;
						}
						$slit_message .= " ( " . $slit_spec_result[$i]->slit_dtl_3 . " ) ";
					}
					
					$slit_message .= "  |  ";
					
					// Slit Product array
					$slit_spec_product_array = array();
					if (isset($slit_spec_result[$i]->product_dtl_1)) {
						$slit_message .= $product_result[$slit_spec_result[$i]->product_dtl_1];
						$slit_spec_product_array[] = $slit_spec_result[$i]->product_dtl_1;
					}
					
					if (isset($slit_spec_result[$i]->product_dtl_2)) {
						$slit_message .= " x " . $product_result[$slit_spec_result[$i]->product_dtl_2];
						$slit_spec_product_array[] = $slit_spec_result[$i]->product_dtl_2;
					}
					
					if (isset($slit_spec_result[$i]->product_dtl_3)) {
						$slit_message .= " x " . $product_result[$slit_spec_result[$i]->product_dtl_3];
						$slit_spec_product_array[] = $slit_spec_result[$i]->product_dtl_3;
					}
					
					$slit_spec_mapping[$slit_spec_result[$i]->slit_spec_id] = $slit_spec_product_array;
					*/
			?>
				<option value="<?= $slit_spec_result[$i]['slit_spec_id'] ?>"><?= $slit_spec_result[$i]['remark']  ?></option>
			<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center" valign="top">
			<table border="1" width="100%" align="center" class="table-data">
				<thead>
					<tr class="ui-widget-header">
						<th align="center">Product</th>
						<th align="center">ค่าแรง</th>
						<th align="center">โปรแกรมการผลิต</th>
					</tr>
				</thead>
				<tbody>
				<? 
					$product_id_temp = array();
					if ($slit_spec_selected === FALSE) $slit_spec_selected = array();
					for($i = 0; $i < count($slit_spec_selected); $i++) {
						if (in_array( $slit_spec_selected[$i]['product_dtl_id'], $product_id_temp, TRUE)) {
							continue;
						} else {
							array_push($product_id_temp, $slit_spec_selected[$i]['product_dtl_id']);
						}
						$product_id_temp = array_unique($product_id_temp);
						
						
						$wage = $wage_price[$slit_spec_selected[$i]['product_dtl_id']];
						
				?>
					<tr>
						<td align="center" width="30%"><?= $product_result[$slit_spec_selected[$i]['product_dtl_id']] ?></td>
						<td align="center" width="40%"><input type="text" id="wage<?= $slit_spec_selected[$i]['product_dtl_id'] ?>temp" name="wage<?= $slit_spec_selected[$i]['product_dtl_id'] ?>" class="numeric wage" value="<?= number_format($wage, 2) ?>" /></td>
						<td align="center" width="40%"><input type="text" id="program<?= $slit_spec_selected[$i]['product_dtl_id'] ?>temp" name="program<?= $slit_spec_selected[$i]['product_dtl_id'] ?>" class="program" value="" /></td>
					</tr>
				<? } ?>
				</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<input type="button" id="submitButton" name="TSUBMIT" class="button" value="ตกลง" />
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />
		</td>
	</tr>
	</tbody>
</table>

<div id="info" title="Information"></div>

<?= form_open("/select_coil/selected_slit", array('id' => 'select_form')) ?>
<input type="hidden" id="row_num" name="row_num" value="<?= count($selected_coil) ?>" />
<input type="hidden" id="group_code" name="group_code" value="<?= $group_code ?>" />
<input type="hidden" id="slit_spec_id" name="slit_spec_id" value="<?= $slit_spec_id ?>" />
<!-- <input type="hidden" id="width" name="width" value="<?php /* echo $width; */ ?>"/> -->
<? for($i = 0; $i < count($product_id_temp); $i++) { ?>
<input type="hidden" id="wage<?= $product_id_temp[$i] ?>" name="wage<?= $product_id_temp[$i] ?>" value=""/>
<? } ?>
<? for($i = 0; $i < count($product_id_temp); $i++) { ?>
<input type="hidden" id="program<?= $product_id_temp[$i] ?>" name="program<?= $product_id_temp[$i] ?>" value=""/>
<? } ?>
<? for($i = 0; $i < count($selected_coil); $i++) { ?>
<input type="hidden" name="select_coil_id<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['coil_id'] ?>" />
<input type="hidden" name="select_coil_lot_no<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['coil_lot_no'] ?>" />
<input type="hidden" name="select_po_id<?= ($i + 1) ?>" row_num="<?= $i + 1?>" value="<?= $selected_coil[$i]['po_id'] ?>" />
<? } ?>
<?= form_close() ?>

<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	
	$("#slit_spec_choose").val("<?= $slit_spec_id ?>");
	$("#slit_spec_choose").change(onSlitSpecChange);
	
	$(".numeric").set_format({precision: 2,autofix:true,allow_negative:false});
	
	$("#backButton").click(onBackButtonClick);
	$("#submitButton").click(onSlitFormSubmit);
	
	$("#width_temp").blur(function(){
		if (!$(this).val()) {
			$(this).val("1228");
		}
	});
	
	initDialog();

});
function initDialog() {
	$("#info").dialog({
		modal: true,
		autoOpen: false
	});
}
function onBackButtonClick() {
	$("#select_form").attr("action", "<?= site_url("/select_coil/selected_slit") ?>");
	$("#select_form").submit();
}
function onSlitSpecChange() {
	$("#width").val($("#width_temp").val());
	$("#group_code").val($(".coil_group_code:checked").val());
	$("#slit_spec_id").val($("#slit_spec_choose").val());
	$("#select_form").attr("action", "<?= site_url("/select_coil/slit") ?>");
	$("#select_form").submit();
}
function onSlitFormSubmit() {

	var product = <?= json_encode($product_id_temp) ?>

	// Validate Form Before Submit
	var slit_spec_id = $("#slit_spec_choose").val();
	if (slit_spec_id == "") {
		$("#info").text("กรุณาเลือก Slit Spec").dialog("open");
		return false;
	}
	
	var error = false;
	$.each($(".wage"), function(idx) {
		if ($.trim($(this).val()) == "") {
			$("#info").text("กรุณาใส่โปรแกรมการผลิตให้เรียบร้อย").dialog("open");
			error = true;
			return false;
		}
	});
	
	$.each($(".program"), function(idx) {
		if ($.trim($(this).val()) == "") {
			$("#info").text("กรุณาใส่โปรแกรมการผลิตให้เรียบร้อย").dialog("open");
			error = true;
			return false;
		}
	});
	
	if (error) {
		return false;
	}
	
	for(var i = 0; i < product.length; i++) {
		$("#wage" + product[i]).val($("#wage" + product[i] + "temp").val());
		$("#program" + product[i]).val($("#program" + product[i] + "temp").val());
	}
	
	$("#width").val($("#width_temp").val());
	$("#group_code").val($(".coil_group_code:checked").val());
	$("#slit_spec_id").val($("#slit_spec_choose").val());
	$("#select_form").attr("action", "<?= site_url("/select_coil/slit_method") ?>");
	$("#select_form").submit();
}
</script>