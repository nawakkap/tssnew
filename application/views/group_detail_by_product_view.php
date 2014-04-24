<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td align="left">&nbsp;&nbsp;Group Id : <?= $group_code ?> : <?= $product_result['product_name_en'] ?> ( <?= $product_result['product_name_th'] ?> )</td>
		<td align="right">
			<input type="button" id="groupButton" name="groupButton" value="ดูข้อมูล Group นี้ทั้งหมด"/>
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />
		</td>
	</tr>
</table>
<br/>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr>
		<th colspan="4" class="ui-widget-header">รายละเอียดของ Group</th>
	</tr>
	<tr>
		<th class="ui-state-highlight"  width="25%">Group Id</th>
		<td width="25%"><?= $group_code ?></td>
		<th class="ui-state-highlight" width="25%">วันที่ทำการ Slit</th>
		<td width="25%"><?= $group_result['slit_date'] ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight">น้ำหนักรวม</th>
		<td><?= number_format($group_result['weight'], 0) ?>&nbsp;กิโลกรัม</td>
		<th class="ui-state-highlight">ราคาต้นทุนรวม</th>
		<td><?= number_format($group_result['cost_price'], 2)  ?>&nbsp;บาท</td>
	</tr>
	<tr>
		<th class="ui-state-highlight">วันที่ทำการ Populate</th>
		<td><?= $group_result['populate_date'] ?></td>
		<th class="ui-state-highlight">สถานะ</th>
		<td><?= $group_status[$group_result['populate_flag']] ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight">จำนวนแถบ Film ทั้งหมด</th>
		<td><?= number_format($group_result['unit'], 0) ?></td>
		<th class="ui-state-highlight">ผู้ทำการผลิต</th>
		<td><?= $group_result['machine_by'] ?></td>
	</tr>
</table>
<br/>
<table border="1" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>Slit Spec ที่ใช้</td>
	</tr>
	<tr>
		<td align="center"><?= $slit_result ?></td>
	</tr>
</table>
<br/>
<table border="1" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>Product</td>
	</tr>
	<tr>
		<td align="center"><?= $product_result['product_name_en'] ?> ( <?= $product_result['product_name_th'] ?> )</td>
	</tr>
</table>
<br/>
<table border="1" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>รายการ Coil</td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="2" class="table-data ui-widget">
	<thead>
		<tr class="ui-state-highlight">
			<th align="center">Coil ID</th>
			<th align="center">Coil No</th>
			<th align="center">ความหนา</th>
			<th align="center">ความกว้าง</th>
			<th align="center">น้ำหนัก</th>
			<th align="center">รับมาวันที่</th>
		</tr>
	</thead>
	<tbody>
	<? for($i = 0; $i < count($coil_result); $i++) { ?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
			<td><a href="<?= site_url("/coil/coil_detail/" . $this->convert->AsciiToHex($coil_result[$i]['coil_id']) . "/" . $coil_result[$i]['coil_lot_no'] . "/" . $this->convert->AsciiToHex($coil_result[$i]['po_id'])) ?>" class="link"><?= $coil_result[$i]['coil_id'] ?></a></td>
			<td><?= $coil_result[$i]['coil_lot_no'] ?></td>
			<td><?= number_format($coil_result[$i]['thickness'], 2) ?></td>
			<td><?= number_format($coil_result[$i]['width'], 2) ?></td>
			<td><?= number_format($coil_result[$i]['weight'], 0) ?></td>
			<td><?= mysqldatetime_to_date($coil_result[$i]['coil_received_date'], 'd/m/Y') ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>
<?= form_open("/group/group_detail/" . $this->convert->AsciiToHex($group_code), array('id' => "groupForm")) ?>
<?= form_close() ?>
<script type="text/javascript">
$(document).ready(function(){
	$(".button, #groupButton").button();
	$("#backButton").click(onBackButtonClick);
	$("#groupButton").click(onGroupButtonClick);
});
function onBackButtonClick(){
	document.location.replace("<?= site_url("/group") ?>");
}
function onGroupButtonClick(){
	$("#groupForm").submit();
}
</script>