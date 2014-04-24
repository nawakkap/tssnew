<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td align="left">&nbsp;Coil ID&nbsp;:&nbsp;<?= $coil_result['coil_id'] ?>&nbsp;&nbsp;ลำดับที่&nbsp;<?= $coil_result['coil_lot_no'] ?></td>
		<td align="right">
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />
		</td>
	</tr>
</table>
<br/>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr>
		<th colspan="4" class="ui-widget-header">รายละเอียดของ Coil</th>
	</tr>
	<tr>
		<th class="ui-state-highlight"  width="25%">Coil Id</th>
		<td width="25%"><?= $coil_result['coil_id'] ?></td>
		<th class="ui-state-highlight" width="25%">วันที่รับสินค้า</th>
		<td width="25%"><?= mysqldatetime_to_date($coil_result['coil_received_date'], "d/m/Y") ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight">ความหนา</th>
		<td><?= number_format($coil_result['thickness'], 2) ?>&nbsp;มิลลิเมตร</td>
		<th class="ui-state-highlight">ความกว้าง</th>
		<td><?= number_format($coil_result['width'], 2) ?>&nbsp;มิลลิเมตร</td>
	</tr>
	<tr>
		<th class="ui-state-highlight">น้ำหนัก</th>
		<td><?= number_format($coil_result['weight'], 0) ?>&nbsp;กิโลกรัม</td>
		<th class="ui-state-highlight">สถานะ</th>
		<td><b><?= $coil_status_result[$coil_result['coil_status']] ?></b></td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<th class="ui-state-highlight">PO ID</th>
		<td><a href="<?= site_url('/order/order_detail/' . $this->convert->AsciiToHex($coil_result['po_id'])) ?>" class="link"><?= $coil_result['po_id'] ?></a></td>
		<th class="ui-state-highlight">Group ID</th>
		<td><a href="<?= site_url("/group/group_detail/" . $this->convert->AsciiToHex($coil_group_code)) ?>" class="link"><?= $coil_group_code ?></a></td>
	</tr>
</table>
<br/>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#backButton").click(onBackButtonClick);
});
function onBackButtonClick() {
	document.location.replace("<?= $referer; ?>");
}
</script>