<style type="text/css" rel="stylesheet">
.table-data { border-collapse: collapse;border-width:1px;border-color:#cccccc; }
.config-textbox { width: 400px;}
.submit-button { width: 200px;}
.add-button { width: 150px;}
</style>
<table border="0" width="100%">
	<tr>
		<td align="right"><input type="button" id="mainButton" name="mainButton" class="button" value="ย้อนกลับหน้าแรก" /></td>
	</tr>
</table>
<br/>
<form name="payment_config_form" action="<?= site_url('config/update') ?>"  method="post">
<input type="hidden" name="mode" value="PAYMENT_TERM" />
<table width="100%" border="0" cellpadding="4" cellspacing="2" class="table-data ui-widget ui-widget-content">
	<tr class="ui-widget-header">
		<td>วิธีการชำระเงิน</td>
		<td align="right"><input type="button" id="add-button-payment" name="add_button_payment" value="เพิ่ม" class="add-button" /></td>
	</tr>
	<?php foreach($payment_term_list as $payment_key => $payment_value) { ?>
	<tr>
		<td align="right" width="30%"><input type="text" name="param_code[]" value="<?= $payment_key ?>" size="7" /></td>
		<td align="center" width="70%"><input type="text" name="param_value[]" value="<?= $payment_value ?>" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?> </td>
	</tr>
	<?php } ?>
	<tr id="last-payment-term" style="display:none">
		<td align="right"><input type="text" name="param_code[]" value="" size="7" /></td>
		<td align="center"><input type="text" name="param_value[]" value="" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="TSUBMIT" value="จัดเก็บ" class="submit-button" /></td>
	</tr>
</table>
</form>
<br/>
<form name="order_status_config_form" action="<?= site_url('config/update') ?>"  method="post">
<input type="hidden" name="mode" value="ORDER_STATUS" />
<table width="100%" border="0" cellpadding="4" cellspacing="2" class="table-data ui-widget ui-widget-content">
	<tr class="ui-widget-header">
		<td>สถานะของ order</td>
		<td align="right"><input type="button" id="add-button-order-status" name="add_button_order_status" value="เพิ่ม" class="add-button" /></td>
	</tr>
	<?php foreach($order_status_list as $order_status_key => $order_status_value) { ?>
	<tr>
		<td align="right" width="30%"><input type="text" name="param_code[]" value="<?= $order_status_key ?>" size="7" /></td>
		<td align="center" width="70%"><input type="text" name="param_value[]" value="<?= $order_status_value ?>" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?> </td>
	</tr>
	<?php } ?>
	<tr id="last-order-status" style="display:none">
		<td align="right"><input type="text" name="param_code[]" value="" size="7" /></td>
		<td align="center"><input type="text" name="param_value[]" value="" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="TSUBMIT" value="จัดเก็บ" class="submit-button" /></td>
	</tr>
</table>
</form>
<br/>
<form name="coil_status_config_form" action="<?= site_url('config/update') ?>"  method="post">
<input type="hidden" name="mode" value="COIL_STATUS" />
<table width="100%" border="0" cellpadding="4" cellspacing="2" class="table-data ui-widget ui-widget-content">
	<tr class="ui-widget-header">
		<td>สถานะของ Coil</td>
		<td align="right"><input type="button" id="add-button-coil-status" name="add_button_coil_status" value="เพิ่ม" class="add-button" /></td>
	</tr>
	<?php foreach($coil_status_list as $coil_status_key => $coil_status_value) { ?>
	<tr>
		<td align="right" width="30%"><input type="text" name="param_code[]" value="<?= $coil_status_key ?>" size="7" /></td>
		<td align="center" width="70%"><input type="text" name="param_value[]" value="<?= $coil_status_value ?>" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?> </td>
	</tr>
	<?php } ?>
	<tr id="last-coil-status" style="display:none">
		<td align="right"><input type="text" name="param_code[]" value="" size="7" /></td>
		<td align="center"><input type="text" name="param_value[]" value="" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="TSUBMIT" value="จัดเก็บ" class="submit-button" /></td>
	</tr>
</table>
</form>
<br/>
<form name="film_status_config_form" action="<?= site_url('config/update') ?>"  method="post">
<input type="hidden" name="mode" value="FILM_STATUS" />
<table width="100%" border="0" cellpadding="4" cellspacing="2" class="table-data ui-widget ui-widget-content">
	<tr class="ui-widget-header">
		<td>สถานะของ Film</td>
		<td align="right"><input type="button" id="add-button-film-status" name="add_button_film_status" value="เพิ่ม" class="add-button" /></td>
	</tr>
	<?php foreach($film_status_list as $film_status_key => $film_status_value) { ?>
	<tr>
		<td align="right" width="30%"><input type="text" name="param_code[]" value="<?= $film_status_key ?>" size="7" /></td>
		<td align="center" width="70%"><input type="text" name="param_value[]" value="<?= $film_status_value ?>" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?> </td>
	</tr>
	<?php } ?>
	<tr id="last-film-status" style="display:none">
		<td align="right"><input type="text" name="param_code[]" value="" size="7" /></td>
		<td align="center"><input type="text" name="param_value[]" value="" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="TSUBMIT" value="จัดเก็บ" class="submit-button" /></td>
	</tr>
</table>
</form>
<br/>
<form name="thickness_config_form" action="<?= site_url('config/update') ?>"  method="post">
<input type="hidden" name="mode" value="THICKNESS" />
<table width="100%" border="0" cellpadding="4" cellspacing="2" class="table-data ui-widget ui-widget-content">
	<tr class="ui-widget-header">
		<td>ความหนา</td>
		<td align="right"><input type="button" id="add-button-thickness" name="add_button_thickness" value="เพิ่ม" class="add-button" /></td>
	</tr>
	<?php foreach($thickness_list as $thickness_key => $thickness_value) { ?>
	<tr>
		<td align="right" width="30%"><input type="text" name="param_code[]" value="<?= $thickness_key ?>" size="7" readonly="readonly" /></td>
		<td align="center" width="70%"><input type="text" name="param_value[]" value="<?= $thickness_value ?>" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?> </td>
	</tr>
	<?php } ?>
	<tr id="last-thickness" style="display:none">
		<td align="right"><input type="text" name="param_code[]" value="" size="7" /></td>
		<td align="center"><input type="text" name="param_value[]" value="" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="TSUBMIT" value="จัดเก็บ" class="submit-button" /></td>
	</tr>
</table>
</form>
<br/>
<form name="width_config_form" action="<?= site_url('config/update') ?>"  method="post">
<input type="hidden" name="mode" value="WIDTH" />
<table width="100%" border="0" cellpadding="4" cellspacing="2" class="table-data ui-widget ui-widget-content">
	<tr class="ui-widget-header">
		<td>ความกว้าง</td>
		<td align="right"><input type="button" id="add-button-width" name="add_button_width" value="เพิ่ม" class="add-button" /></td>
	</tr>
	<?php foreach($width_list as $width_key => $width_value) { ?>
	<tr>
		<td align="right" width="30%"><input type="text" name="param_code[]" value="<?= $width_key ?>" size="7" readonly="readonly" /></td>
		<td align="center" width="70%"><input type="text" name="param_value[]" value="<?= $width_value ?>" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?> </td>
	</tr>
	<?php } ?>
	<tr id="last-width" style="display:none">
		<td align="right"><input type="text" name="param_code[]" value="" size="7" /></td>
		<td align="center"><input type="text" name="param_value[]" value="" class="config-textbox" />&nbsp;<?= image_asset('delete.gif', '' , array('width' => 22, 'height' => 22, 'class' => 'delete-button')); ?></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" name="TSUBMIT" value="จัดเก็บ" class="submit-button" /></td>
	</tr>
</table>
</form>
<br/>
<div id="pleasewait-dialog" title="Information">โปรดรอสักครู่</div>
<script type="text/javascript">
$(document).ready(function() {
	$(".button").button();
	$("#mainButton").click(onMainButtonClick);
	$("#add-button-payment, #add-button-order-status, #add-button-film-status, #add-button-coil-status, #add-button-thickness, #add-button-width, .submit-button").button();
	$(".submit-button").click(onFormSubmit);
	$("#add-button-payment").click(onPaymentAddButtonClick);
	$("#add-button-order-status").click(onOrderStatusAddButtonClick);
	$("#add-button-coil-status").click(onCoilStatusAddButtonClick);
	$("#add-button-film-status").click(onFilmStatusAddButtonClick);
	$("#add-button-thickness").click(onThickNessAddButtonClick);
	$("#add-button-width").click(onWidthAddButtonClick);
	$(".delete-button").click(onDeleteButtonClick);
	$("#pleasewait-dialog").dialog({
		autoOpen:false,
		modal: true,
		closeOnEscape : false,
		open: function(event, ui) { $("#pleasewait-dialog").prev().find(".ui-dialog-titlebar-close").hide(); }
	});
});
function onPaymentAddButtonClick() {
	var obj = $("#last-payment-term").clone();
	obj.removeAttr("id").removeAttr("style");
	obj.insertBefore($("#last-payment-term"));
	$(".delete-button").die().click(onDeleteButtonClick);
}
function onOrderStatusAddButtonClick() {
	var obj = $("#last-order-status").clone();
	obj.removeAttr("id").removeAttr("style");
	obj.insertBefore($("#last-order-status"));
	$(".delete-button").die().click(onDeleteButtonClick);
}
function onCoilStatusAddButtonClick() {
	var obj = $("#last-coil-status").clone();
	obj.removeAttr("id").removeAttr("style");
	obj.insertBefore($("#last-coil-status"));
	$(".delete-button").die().click(onDeleteButtonClick);
}
function onFilmStatusAddButtonClick() {
	var obj = $("#last-film-status").clone();
	obj.removeAttr("id").removeAttr("style");
	obj.insertBefore($("#last-film-status"));
	$(".delete-button").die().click(onDeleteButtonClick);
}
function onThickNessAddButtonClick() {
	var obj = $("#last-thickness").clone();
	obj.removeAttr("id").removeAttr("style");
	obj.insertBefore($("#last-thickness"));
	$(".delete-button").die().click(onDeleteButtonClick);
}
function onWidthAddButtonClick() {
	var obj = $("#last-width").clone();
	obj.removeAttr("id").removeAttr("style");
	obj.insertBefore($("#last-width"));
	$(".delete-button").die().click(onDeleteButtonClick);
}
function onDeleteButtonClick() {
	$(this).parent().parent().remove();
}
function onFormSubmit() {
	$("#pleasewait-dialog").dialog("open");
	return true;
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main"); ?>");
}
</script>