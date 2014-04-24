<?= form_open("/select_coil/third_step") ?>

<input type="hidden" name="po_id" value="<?= $po_id ?>"/>
<? for($i = 0; $i < count($coil_id); $i++) { ?>
<input type="hidden" name="coil_id[]" value="<?= $coil_id[$i] ?>" />
<? } ?>
<input type="hidden" name="thickness" value="<?= $thickness?>"/>

<table border="0" width="100%" class="ui-widget">
	<tr>
		<td align="left" class="ui-state-highlight" width="20%" nowrap="nowrap"><b>3. เลือกสินค้า</b>&nbsp;&nbsp;&nbsp;<input type="button" name="mainButton" class="button mainButton" value="กลับสู่หน้าหลัก"/></td>
		<td align="right">ความหนา&nbsp;<input type="text" id="thickness" name="thickness" value="<?= $thickness ?>" />&nbsp;&nbsp;<input type="submit" name="Tsubmit" class="button" value="ค้นหา" /></td>
	</tr>
</table><br/>
<?= form_close() ?>

<?= form_open("/select_coil/fourth_step") ?>

<input type="hidden" name="po_id" value="<?= $po_id ?>"/>
<? for($i = 0; $i < count($coil_id); $i++) { ?>
<input type="hidden" name="coil_id[]" value="<?= $coil_id[$i] ?>" />
<? } ?>
<input type="hidden" name="thickness" value="<?= $thickness?>"/>

<table border="1" width="100%" cellpadding="5" cellspacing="3" class="table-data ui-widget">
	<? for($i = 0; $i < count($product_result); $i++) {  ?>
	<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
	<? 	foreach($product_result[$i] as $item) { 
				$product_id = $item->product_dtl_id;
				$name = $item->product_name_th;
	?>
		<td align="left"><input type="checkbox" name="product_dtl_id[]" class="product_checkbox" value="<?= $product_id ?>" />&nbsp;<?= $name ?></td>
	<? 	} ?>
	</tr>
	<? } ?>
</table><br/>
<table border="0" width="100%">
	<tr>
		<td align="left"><input type="reset" id="backButton" name="TReset" class="button" value="ย้อนกลับ" /></td>
		<td align="right"><input type="submit" name="Tsubmit" class="button" value="ถัดไป" /></td>
	</tr>
</table>
<?= form_close() ?>

<?= form_open("/select_coil/second_step", array("id" => "backform")) ?>
<input type="hidden" name="po_id" value="<?= $po_id ?>"/>
<? for($i = 0; $i < count($coil_id); $i++) { ?>
<input type="hidden" name="coil_id[]" value="<?= $coil_id[$i] ?>" />
<? } ?>
<input type="hidden" name="thickness" value="<?= $thickness?>"/>
<?= form_close() ?>

<br/><br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$(".product_checkbox").click(onProductCheckBoxClick);
	$("#backButton").click(onBackButtonClick);
	$("#thickness").select();
	$(".mainButton").click(onMainButtonClick);
});
function onBackButtonClick() {
	$("#backform").submit();
}
function onMainButtonClick(){
	document.location.replace("<?= site_url("/main") ?>");
}
function onProductCheckBoxClick() {
	if ($(".product_checkbox:checked").size() > 3) {	
		$(this).removeAttr("checked");
	}
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
</script>