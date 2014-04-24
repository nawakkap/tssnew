<?= form_open("/select_coil") ?>
<table border="0" width="100%" cellpadding="4" class="ui-widget">
	<tr>
		<td align="left" class="ui-state-highlight" width="20%" nowrap="nowrap"><b>1. เลือก Order</b>&nbsp;&nbsp;&nbsp;<input type="button" name="mainButton" class="button mainButton" value="กลับสู่หน้าหลัก"/></td>
		<td align="right">
			<select name="searchType">
				<option value="po_id">PO ID</option>
				<option value="thickness">ความหนา</option>
			</select>&nbsp;
			<input type="text" name="searchText" value="" />&nbsp;
			<input type="submit" name="Tsubmit" class="button" value="ค้นหา"/>
		</td>
	</tr>
</table><br/>
<?= form_close() ?>

<?= form_open("/select_coil/second_step", array("id"=>"my_form")) ?>
<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="30">&nbsp;</th>
			<th align="center"><a href="#" class="sort" rel="po_id">PO ID</a></th>
			<th align="center"><a href="#" class="sort" rel="thickness">ความหนา</a></th>
			<th align="center"><a href="#" class="sort" rel="weight_remaining">น้ำหนัก Coil ที่มีใน stock</a></th>
			<th align="center"><a href="#" class="sort" rel="price">ต้นทุน</a></th>
			<th align="center"><a href="#" class="sort" rel="vat_status">VAT?</a></th>
		</tr>
	</thead>
	<tbody>
	<? 
		$bool = FALSE;
		foreach($order_result as $order_result_item) {
			foreach($order_result_item as $item) {
				//$weight_remaining = $item["weight_received"] - $item["coil_sum_weight"];
	?>
		<tr class="<?= ($bool) ? "odd" : "even" ?>">
			<td align="center"><input type="radio" name="po_id" class="po_id" value="<?= $item["po_id"] ?>"/></td>
			<td>&nbsp;<?= $item["po_id"] ?></td>
			<td>&nbsp;<?= number_format($item["thickness"], 2) ?></td>
			<td>&nbsp;<?= number_format($item["weight_remaining"],  0) ?></td>
			<td>&nbsp;<?= number_format($item["price"], 2) ?></td>
			<td>&nbsp;<?= ($item["vat_status"] == "1") ? "Yes" : "No" ?></td>
		</tr>
	<? 		$bool = !$bool;
			}
		} ?>
	</tbody>
</table><br/>
<table border="0" width="100%">
	<tr>
		<td align="left"><input type="reset" name="TReset" class="button mainButton" value="กลับสู่หน้าหลัก" /></td>
		<td align="right"><input type="submit" name="Tsubmit" class="button" value="ถัดไป" /></td>
	</tr>
</table>
<?= form_close() ?>

<?= form_open("/select_coil", array("id" => "sort_form")) ?>
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<?= form_close() ?>
<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>
<div id="warning" title="info" style="display:none">กรุณาเลือก Order</div>
<br/><br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$(".mainButton").click(onMainButtonClick);
	$("#my_form").submit(onFormSubmit);
	$(".sort").click(onSortClick);
});
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function onSortClick() {
	$("#info").dialog({
		modal: true
	});
	var rel = $(this).attr("rel");
	if (rel != $("#sort_column").val()) {
		$("#sort_by").val("asc");
	}
	$("#sort_column").val(rel);
	$("#sort_form").submit();
}
function onFormSubmit() {
	if ($(".po_id:checked").size() < 1) {
		$("#warning").dialog({modal: true});
		return false;
	} 
	return true;
}
</script>