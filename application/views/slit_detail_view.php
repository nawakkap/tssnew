<table border="0" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>รายละเอียดของ Slit Spec</td>
		<td align="right"><input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" /></td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr>
		<th colspan="2" class="ui-state-highlight">ความหนา</th>
		<td colspan="3"><?= $slit_thickness ?></td>
	</tr>
	<tr>
		<td colspan="5">&nbsp;</td>
	</tr>
	<tr class="ui-state-highlight">
		<th width="10%" align="center">ลำดับที่</th>
		<th width="15%" align="center">ความกว้าง</th>
		<th width="10%" align="center">จำนวน</th>
		<th width="45%" align="center">Product</th>
		<th width="1h0%" align="center">Ratio</th>
		<th width="10%" align="center">Type</th>
	</tr>
	<? 
		$index = 0;
		foreach($slit_result as $item) { ?>
	<tr class="<?= ($index % 2) ? "even" : "odd" ?>">
		<td><?= $item['slit_sub_no'] ?></td>
		<td><?= number_format($item['slit_width'], 2) ?></td>
		<td><?= number_format($item['slit_qty'], 0) ?></td>
		<td><?= $product_mapping[$item['product_dtl_id']] ?></td>
		<td><?= number_format($item["ratio"] , 1) ?>%</td>
		<td><?= number_format($item["type"] , 0) ?></td>
	</tr>
	<? $index++;} ?>
	<tr>
		<td colspan="5">&nbsp;<?= $slit_result[0]['remark'] ?></td>
	</tr>
</table>
<br/>
<table border="0" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr>
		<td class="ui-widget-header">รายละเอียดของ Group</td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-state-highlight">
			<th align="center">Group Code</th>
			<th align="center">ราคาต้นทุน</th>
			<th align="center">น้ำหนัก</th>
			<th align="center">จำนวนแถบ</th>
		</tr>
	</thead>
	<tbody>
	<? for($i = 0; $i < count($group_result); $i++) { ?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
			<td><a href="<?= site_url("/group/group_detail/" . $this->convert->AsciiToHex($group_result[$i]['coil_group_code'])) ?>" class="link"><?= $group_result[$i]['coil_group_code'] ?></a></td>
			<td><?= number_format($group_result[$i]['cost_price'], 2) ?></td>
			<td><?= number_format($group_result[$i]['weight'], 0) ?></td>
			<td><?= number_format($group_result[$i]['unit'], 0) ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	
	$("#backButton").click(onBackButtonClick);
});
function onBackButtonClick(){
	document.location.replace("<?= site_url("/slit") ?>");
}
</script>