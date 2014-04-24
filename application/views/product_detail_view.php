<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td align="left">&nbsp;&nbsp;<?= $product_result['product_name_th'] ?></td>
		<td align="right">
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />
		</td>
	</tr>
</table>
<br/>
<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>รายละเอียดของ Product</td>
		<td align="right"><?= image_asset("minus_sign.png", '', array('border' => 0, 'id' => 'product_detail_minus')) ?>&nbsp;</td>
	</tr>
</table>
<table id="product-detail-table" border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr>
		<th class="ui-state-highlight" width="25%">Product ID</th>
		<td width="25%"><?= $product_result['product_dtl_id'] ?></td>
		<th class="ui-state-highlight" width="25%">Prod code.</th>
		<td width="25%"><?= $product_result["product_display_id"] ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">ชื่อภาษาไทย</th>
		<td width="25%" colspan="3"><?= $product_result['product_name_th'] ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">ชื่อภาษาอังกฤษ</th>
		<td width="25%" colspan="3"><?= $product_result['product_name_en'] ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">ชื่อย่อ</th>
		<td width="25%" colspan="3"><?= $product_result['product_name_initial'] ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">Thickness</th>
		<td width="25%"><?= number_format($product_result['thickness'], 2) ?>&nbsp;มิลลิเมตร</td>
		<th class="ui-state-highlight" width="25%">Thickness min</th>
		<td width="25%"><?= number_format($product_result['thickness_min'], 2) ?>&nbsp;มิลลิเมตร</td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">Thickness Rep.</th>
		<td width="25%"><?= number_format($product_result['thickness_rep'], 2) ?>&nbsp;มิลลิเมตร</td>
		<th class="ui-state-highlight" width="25%">สี</th>
		<td width="25%"><?= $product_result['color'] ?></td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">Size detail</th>
		<td width="25%"><?= $product_result['size_detail'] ?></td>
		<th class="ui-state-highlight" width="25%">Film size</th>
		<td width="25%"><?= $product_result['film_size'] ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">Estimate Weight</th>
		<td width="25%"><?= $product_result['est_weight'] ?>&nbsp;กิโลกรัม</td>
		<th class="ui-state-highlight" width="25%">Estimate Weight Min</th>
		<td width="25%"><?= $product_result['est_weight_min'] ?>&nbsp;กิโลกรัม</td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">Actual Weight</th>
		<td width="25%"><?= $product_result['actual_weight'] ?>&nbsp;กิโลกรัม</td>
		<th class="ui-state-highlight" width="25%">Accounting Weight</th>
		<td width="25%"><?= $product_result['accounting_weight'] ?>&nbsp;กิโลกรัม</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">Piece per pack</th>
		<td width="25%"><?= number_format($product_result['piece_per_pack'], 0) ?></td>
		<th class="ui-state-highlight" width="25%">Piece per truck</th>
		<td width="25%"><?= number_format($product_result['piece_per_truck'], 0) ?></td>
	</tr>
	<tr>
		<th class="ui-state-highlight" width="25%">In Production</th>
		<td width="25%"><?= ($product_result['in_production'] == "Y") ? "Yes" : "No" ?>&nbsp;</td>
		<th class="ui-state-highlight" width="25%">Weight Display</th>
		<td width="25%"><?= $product_result['weight_display'] ?>&nbsp;</td>
	</tr>
</table>
<br/>
<table id="group_table" border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center">Group Code</th>
			<th align="center">ความหนา</th>
			<th align="center">น้ำหนักรวม</th>
			<th align="center">ราคารวม (บาท)</th>
			<th align="center">สถานะ</th>
		</tr>
	</thead>
	<tbody id="detail">
		<tr id="loadingRow">
			<td id="loadingColumn" colspan="5" align="center">
				<input id="loadingButton" type="button" class="button" value="เรียกดูข้อมูล" onclick="openGroupDetail()" />
			</td>
		</tr>
	
	
	
		<?php /* for($i = 0; $i < count($group_result); $i++) { ?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
			<td><a href="<?= site_url("/group/group_detail_by_product/" . $this->convert->AsciiToHex($group_result[$i]['coil_group_code']) . "/" . $group_result[$i]['product_dtl_id']) ?>" class="link"><?= $group_result[$i]['coil_group_code'] ?></a></td>
			<td><?= number_format($group_result[$i]['thickness'], 2) ?></td>
			<td><?= number_format($group_result[$i]['weight'], 0) ?></td>
			<td><?= number_format($group_result[$i]['cost_price'], 2) ?></td>
			<td><?= $group_status[$group_result[$i]['populate_flag']] ?></td>
		</tr>
		<?php } */ ?>
	</tbody>
</table><br/><br/><br/>
<script type="text/javascript">
$(document).ready(function(){	
	$(".button").button();
	$("#backButton").click(onBackButtonClick);
	$("#product_detail_minus").toggle(onMinusClick, onPlusClick).css("cursor", "pointer");
	$("#group_table").tablesorter();

});
function onBackButtonClick()	{
	document.location.replace("<?= site_url("/product") ?>");
}
function onMinusClick() {
	$("#product-detail-table").hide("blind");
}
function onPlusClick() {
	$("#product-detail-table").show("blind");
}
function openGroupDetail() {
	$("#loadingColumn").html("Loading....");
	$.get("<?php echo site_url("product/product_group_detail/" . $product_result['product_dtl_id']); ?>", function(data) {
		$("#detail").html(data);
	});
}
</script>