<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td align="left">&nbsp;&nbsp;<?= $supplier_name ?></td>
		<td align="right">
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />
		</td>
	</tr>
</table>
<br/>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr>
		<th colspan="4" class="ui-widget-header"><?= image_asset("minus_sign.png", '', array('border' => 0, 'id' => 'supplier_detail_minus')) ?>&nbsp;&nbsp;รายละเอียดของ Supplier</th>
	</tr>
</table>
<table id="supplier_detail_table" border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr>
		<th class="ui-state-highlight">ชื่อ - นามสกุล</th>
		<td width="25%"><?= $supplier_result['supplier_name'] ?></td>
		<th class="ui-state-highlight">เครดิต</th>
		<td width="25%"><?= (isset($payment_result[$supplier_result['default_payment_term']])) ? $payment_result[$supplier_result['default_payment_term']] : "&nbsp;" ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight">ที่อยู่ 1</th>
		<td colspan="3"><?= $supplier_result['address_1'] ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight">ที่อยู่ 2</th>
		<td colspan="3"><?= $supplier_result['address_2'] ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">รหัสไปรษณีย์</th>
		<td width="25%"><?= $supplier_result['address_postcode'] ?></td>
		<th class="ui-state-highlight" width="25%">E-Mail</th>
		<td width="25%"><?= $supplier_result['email'] ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">เบอร์โทรศัพท์</th>
		<td width="25%"><?= $supplier_result['tel_phone'] ?></td>
		<th class="ui-state-highlight" width="25%">เบอร์โทรศัพท์มือถือ</th>
		<td width="25%"><?= $supplier_result['tel_mobile'] ?></td>
	</tr>
</table>
<br/>
<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td align="left">รายละเอียด Order</td>
	</tr>
</table>
<table id="order_table" border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr>
			<th class="ui-state-highlight" align="center">PO ID</th>
			<th class="ui-state-highlight" align="center">ความหนา</th>
			<th class="ui-state-highlight" align="center">น้ำหนัก</th>
			<th class="ui-state-highlight" align="center">น้ำหนักค้างส่ง</th>
			<th class="ui-state-highlight" align="center">วันที่</th>
			<th class="ui-state-highlight" align="center">สถานะ</th>
		</tr>
	</thead>
	<tbody>
	<? for($i= 0; $i < count($order_result); $i++) { ?>
		<tr class="<?= ($i %2) ? "even" : "odd" ?>">
			<td><a href="<?= site_url("/order/order_detail/" . $this->convert->AsciiToHex($order_result[$i]['po_id']) ); ?>" class="link"><?= $order_result[$i]['po_id'] ?></a></td>
			<td><?= number_format($order_result[$i]['thickness'], 2) ?></td>
			<td><?= number_format($order_result[$i]['weight'], 0) ?></td>
			<td><?= number_format($order_result[$i]['weight_remaining'], 0) ?></td>
			<td><?= mysqldatetime_to_date($order_result[$i]['order_received_date'], 'd/m/Y') ?></td>
			<td><?= $order_status[$order_result[$i]['order_status']] ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#backButton").click(onBackButtonClick);
	$("#order_table").tablesorter();
	$("#supplier_detail_minus").toggle(onMinusClick, onPlusClick).css("cursor", "pointer");
});
function onBackButtonClick() {
	document.location.replace("<?= $referer ?>");
}
function onMinusClick() {
	$("#supplier_detail_table").hide("blind");
}
function onPlusClick() {
	$("#supplier_detail_table").show("blind");
}
</script>