<table border="0" width="100%" class="ui-widget">
	<tr>
		<td>
			<input type="button" name="backButton" class="button backButton" value="ย้อนกลับ" />
			<input type="button" name="filterButton" class="filter_button" value="<?= $startDate ?> / <?= $endDate ?>" onclick="onDateFilterClick()" />
		</td>
		<td align="right">
			<?= form_open("/order/history", array("id" => "order_search_form")) ?>
			PO : <input type="text" id="searchText" name="searchText" value=""/>
			<input type="submit" id="searchButton" name="searchButton" class="button" value="ค้นหา"/>
			<?= form_close() ?>
		</td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center"><a href="#" class="sort" rel="po_id">PO ID</a></th>
			<th align="center"><a href="#" class="sort" rel="supplier_name">ผู้จัดส่ง</a></th>
			<th align="center"><a href="#" class="sort" rel="thickness">ความหนา</a></th>
			<th align="center"><a href="#" class="sort" rel="weight">น้ำหนัก</a></th>
			<th align="center"><a href="#" class="sort" rel="weight_remaining">น้ำหนักค้างส่ง</a></th>
			<th align="center"><a href="#" class="sort" rel="order_received_date">วันที่</a></th>
			<th align="center"><a href="#" class="sort" rel="order_status">สถานะ</a></th>
		</tr>
	</thead>
	<tbody>
	<? for($i = 0; $i < count($result); $i++) { 
	?>
		<tr class="<?= ($i % 2 == 0) ? "even" : "odd" ?>">
			<td><a href="<?= site_url("/order/order_detail/" . $this->convert->AsciiToHex($result[$i]->po_id) . "/1"); ?>" class="link"><?= $result[$i]->po_id ?></a></td>
			<td><a href="<?= site_url("/supplier/supplier_detail/" . $supplier_result[$result[$i]->supplier_name]) ?>" class="link"><?= $result[$i]->supplier_name ?></a></td>
			<td><?= number_format($result[$i]->thickness, 2) ?></td>
			<td><?= number_format($result[$i]->weight, 0) ?></td>
			<td><?= number_format($result[$i]->weight_remaining, 0) ?></td>
			<td><?= mysqldatetime_to_date($result[$i]->order_received_date, 'd/m/Y') ?></td>
			<td><?= $order_status[$result[$i]->order_status] ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>
<table border="0" width="100%">
	<tr>
		<td align="right"><input type="button" name="backButton" class="button backButton" value="ย้อนกลับ" /></td>
	</tr>
</table>

<?= form_open("/order/history", array("id" => "sort_form")) ?>
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<?= form_close() ?>
<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>
<div id="confirm-dialog" title="คำยืนยัน" style="display:none">คุณต้องการลบข้อมูลนี้ใช่หรือไม่</div>

<div id="search-dialog" title="ค้นหา" style="display:none">

<table border="0" width="100%">
	<tr>
		<td>PO ID</td>
		<td></td>
	</tr>
</table>

</div>

<div id="dateFilterPanel" title="Filter By Date" style="display:none;">
<?= form_open("order/history", array("id" => "FilterByDateForm")) ?>
<table border="0" class="ui-widget">
	<tr>
		<td>From :</td>
		<td><input type="text" id="startDate" name="startDate" value="<?= $startDate ?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td>To :</td>
		<td><input type="text" id="endDate" name="endDate" value="<?= $endDate ?>" readonly="readonly" /></td>
	</tr>
</table>
<?= form_close() ?>
</div>

<script type="text/javascript">
$(document).ready(function(){ 
	$(".button, .filter_button").button();
	$("#startDate, #endDate").datepicker({
		dateFormat : 'yy-mm-dd'
	});
	$(".backButton").click(onBackButton);
	$("#searchButton").click(onSearchButtonClick);
	$(".sort").click(onSortClick);
});
function onDateFilterClick()  {
	$("#dateFilterPanel").dialog({
		modal: true,
		buttons : {
			'ตกลง' : function() {
				$("#info").dialog({
					modal: true
				});
				$("#FilterByDateForm").submit();
			},
			'ปิด' : function() {
				$(this).dialog("close");
			}
		}
	});
}
function onBackButton(){
	document.location.replace("<?= site_url("/order") ?>");
	//$("#back_form").submit();
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
/*
function onSearchButtonClick() {
	$("#search-dialog").dialog({
		modal: true,
		buttons: {
			"ตกลง" : function() {
				$("#order_search_form").submit();
			},
			"ยกเลิก" : function() {
				$("#searchText").val("");
				$(this).dialog("close");
			}
		},
		open: function() {
			$("#searchText").select();
		}
	});
}
*/
</script>