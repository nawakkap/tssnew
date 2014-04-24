<table border="0" width="100%" class="ui-widget">
	<tr>
		<td class="ui-widget-header">&nbsp;กรุณาใส่ข้อมูลที่ต้องการค้นหา</td>
	</tr>
</table><br/>
<?php echo form_open("film/edit_search", array("id" => "myform")); ?>
<table border="0" width="100%" class="ui-widget">
	<tr>
		<td width="15%"><strong>1. Lot No</strong></td>
		<td><input type="text" name="lot_no" value="<?php echo $lot_no; ?>" /></td>
	</tr>
	<tr>
		<td width="15%"><strong>2. Product</strong></td>
		<td>
		<?php
			
			$option = array();
			$option[""] = "";
			for($i = 0; $i < count($products); $i++)
			{
				$option[$products[$i]["product_dtl_id"]] = $products[$i]["product_name_en"];
			}
		
			echo form_dropdown("product_dtl_id", $option, $product_dtl_id);
		?>
		</td>
	</tr>
	<tr>
		<td><strong>3. Slit date</strong></td>
		<td><input type="text" id="film_date" name="film_date" value="<?php echo $slit_date ?>" /></td>
	</tr>
	<tr>
		<td><strong>4. ความหนา</strong></td>
		<td><?= form_dropdown("thickness", $thickness_result, $thickness, 'id="thickness" style="width: 150px;"') ?></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td align="center" colspan="2"><input type="button" onclick="validate()" value="ค้นหา" class="button" style="width: 200px;height:75px;" /></td>
	</tr>
</table>
<?php echo form_close(); ?>
<script type="text/javascript">
$(document).ready(function() {
	$("#film_date").datepicker({
		dateFormat : "dd/mm/yy"
	});
	
	$(".button").button();
	
	var z=dhtmlXComboFromSelect("thickness");
	
	$("input[name=lot_no]").focus().select();
});
function validate() {
	if (!$("#film_date").val()) {
		alert("กรุณาระบุวันก่อนทำการค้นหา");
		return;
	}
	
	$("#myform").submit();
}
</script>