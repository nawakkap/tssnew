<?= form_open("/config/save_premium", array('id' => 'config_form')) ?>
<input type="hidden" name="mode" value="<?= $config_mode ?>"/>

<table border="0" class="table-data ui-widget" width="100%">
	<tr class="ui-widget-header">
		<td><?= $config_header ?></td>
		<td align="right"><button type="button" id="backButton">ย้อนกลับ</button>&nbsp;&nbsp;<button type="submit" id="saveButton" style="width: 100px">บันทึก</button></td>
	</tr>
</table>

<table border="1" class="table-data ui-widget" cellpadding="4" width="100%">
	<tr>
		<td align="right">ค่า Premium&nbsp;&nbsp;</td>
		<td><input type="text" id="vat_premium" name="VAT_PREMIUM" class="validate[required]" value="<?= (isset($VAT_PREMIUM))  ? number_format(($VAT_PREMIUM - 1) * 100, 2) : ""; ?>" />&nbsp;%</td>
	</tr>
	<tr>
		<td align="right">ค่า VAT&nbsp;&nbsp;</td>
		<td><input type="text" id="vat_normal" name="VAT_NORMAL" class="validate[required]" value="<?= (isset($VAT_NORMAL)) ? number_format(($VAT_NORMAL - 1) * 100, 2) : "" ?>" />&nbsp;%</td>
	</tr>
</table>
<?= form_close() ?>
<br/><br/>
<div id="pleasewait-dialog" style="display:none">โปรดรอสักครู่</div>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button();
	$("#config_form").validationEngine({scroll:false, promptPosition : "centerRight"});
	$("#backButton").click(function(){
		document.location.replace("<?= site_url("/config") ?>");
	});
});
</script>