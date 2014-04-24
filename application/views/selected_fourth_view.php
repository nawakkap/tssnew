<table border="0" width="100%" cellpadding="4" class="ui-widget">
	<tr>
		<td align="left" class="ui-state-highlight" width="20%" nowrap="nowrap"><b>4. เลือก Slit Spec</b>&nbsp;&nbsp;&nbsp;<input type="button" name="mainButton" class="button mainButton" value="กลับสู่หน้าหลัก"/></td>
		<td>&nbsp;</td>
	</tr>
</table><br/>
<?= form_open("/select_coil/fifth_step", array("id" => "my_form")) ?>

<input type="hidden" name="po_id" value="<?= $po_id ?>" />
<!-- <input type="hidden" name="width" value="<?php /* echo $width; */ ?>" /> -->
<input type="hidden" name="thickness" value="<?= $thickness ?>"/>
<? for($i =0 ; $i < count($coil_id); $i++) { ?>
<input type="hidden" name="coil_id[]" value="<?= $coil_id[$i] ?>" />
<? } ?>
<? for($i =0 ; $i < count($product_dtl_id); $i++) { if (empty($product_dtl_id[$i])) continue; ?>
<input type="hidden" name="product_dtl_id[]" value="<?= $product_dtl_id[$i] ?>" />
<? } ?>

<table border="1" width="100%" cellpadding="5" cellspacing="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="5%">&nbsp;</th>
			<th>&nbsp;&nbsp;Slit Spec</th>
			<!-- <th align="right">ความกว้างที่ใช้หาร&nbsp;<input type="text" id="width" name="width" value="<?php /* echo $width; */ ?>" size="5" />&nbsp;มิลลิเมตร&nbsp; </th> -->
		</tr>
	</thead>
	<tbody>
	<? for($i = 0; $i < count($slit_spec_result); $i++) { ?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
			<td align="center"><input type="radio" name="slit_spec_id" class="slit_spec_id" value="<?= $slit_spec_result[$i]['slit_spec_id'] ?>"/></td>
			<td align="left" colspan="2">&nbsp;&nbsp;<?= $slit_spec_result[$i]['slit_thickness'] ?>&nbsp;&nbsp;&nbsp;&nbsp;<?= $slit_spec_result[$i]['remark'] ?></td>
		</tr>
	<? } ?>
	</tbody>
</table><br/>
<table border="0" width="100%">
	<tr>
		<td align="left"><input type="reset" id="backButton" name="TReset" class="button" value="ย้อนกลับ" /></td>
		<td align="right"><input type="submit" name="Tsubmit" class="button" value="ถัดไป" /></td>
	</tr>
</table>
<?= form_close() ?>
<?= form_open("/select_coil/third_step", array("id" => "backform")) ?>
<input type="hidden" name="po_id" value="<?= $po_id ?>" />
<!-- <input type="hidden" name="width" value="<?php /* echo $width; */ ?>" /> -->
<input type="hidden" name="thickness" value="<?= $thickness ?>"/>
<? for($i =0 ; $i < count($coil_id); $i++) { ?>
<input type="hidden" name="coil_id[]" value="<?= $coil_id[$i] ?>" />
<? } ?>
<? for($i =0 ; $i < count($product_dtl_id); $i++) { if (empty($product_dtl_id[$i])) continue;?>
<input type="hidden" name="product_dtl_id[]" value="<?= $product_dtl_id[$i] ?>" />
<? } ?>
<?= form_close() ?>
<div id="warning" title="info" style="display:none">กรุณาเลือก Slit Spec</div>
<br/><br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#backButton").click(onBackButtonClick);
	$("#width").select();
	$("#my_form").submit(onFormSubmit);
	$(".mainButton").click(onMainButtonClick);
});
function onBackButtonClick() {
	$("#backform").submit();
}
function onMainButtonClick(){
	document.location.replace("<?= site_url("/main") ?>");
}
function onFormSubmit() {
	if ($(".slit_spec_id:checked").size() < 1)
	{
		$("#warning").dialog({modal: true});
		return false;
	}
	return true;
}
</script>