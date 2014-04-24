<?= form_open("/order/add_page", array('id' => 'order_form')) ?>
<table border="0" width="100%">	
	<tr>
		<td>
			<input type="button" id="addButton" name="addButton" class="button" value="เพิ่มข้อมูล" />
			<input type="submit" id="editButton" name="editButton" class="button" value="แก้ไขข้อมูล" />
			<input type="button" id="deleteButton" name="deleteButton" class="button" value="ลบข้อมูล" />
		</td>
		<td align="right">
			<input type="button" id="historyButton" name="historyButton" class="button" value="ข้อมูลเก่า"/>
			<input type="button" id="searchButton" name="searchButton" class="button" value="ค้นหา"/>
		</td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="25" align="center">&nbsp;</th>
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
			<td><input type="radio" name="po_id" class="checkbox" value="<?= $result[$i]->po_id ?>" /></td>
			<td><a href="<?= site_url("/order/order_detail/" . $this->convert->AsciiToHex($result[$i]->po_id) . "/0" ); ?>" class="link"><?= $result[$i]->po_id ?></a></td>
			<td><a href="<?= site_url("/supplier/supplier_detail/" . $supplier_result[$result[$i]->supplier_name]) ?>" class="link"><?= $result[$i]->supplier_name ?></a></td>
			<td><?= number_format($result[$i]->thickness, 2) ?></td>
			<td><?= number_format($result[$i]->weight, 0) ?></td>
			<td><?= ($result[$i]->weight_remaining == NULL) ? number_format($result[$i]->weight, 0) : number_format($result[$i]->weight_remaining, 0) ?></td>
			<td><?= mysqldatetime_to_date($result[$i]->order_received_date, 'd/m/Y') ?></td>
			<td><?= $order_status[$result[$i]->order_status] ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>
<table border="0" width="100%">
	<tr>
		<td align="right"><input type="button" id="mainButton" name="mainButton" class="button" value="ย้อนกลับสู่หน้าหลัก" /></td>
	</tr>
</table>
<?= form_close() ?>
<?= form_open("/order", array("id" => "sort_form")) ?>
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<?= form_close() ?>
<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>

<div id="confirm-dialog" title="คำยืนยัน" style="display:none">คุณต้องการลบข้อมูลนี้ใช่หรือไม่</div>

<div id="search-dialog" title="ค้นหา" style="display:none">
<?= form_open("/order", array("id" => "order_search_form")) ?>
<table border="0" width="100%">
	<tr>
		<td>PO ID</td>
		<td><input type="text" id="searchText" name="searchText" value=""/></td>
	</tr>
</table>
<?= form_close() ?>
</div>
<br/><br/><br/>

<script type="text/javascript">
$(document).ready(function(){ 
	$(".button").button();
	$("#order_form").submit(onFormSubmit);
	$("#addButton").click(onAddButtonClick);
	$("#deleteButton").click(onBeforeDeleteButtonClick);
	$("#mainButton").click(onMainButtonClick);
	$("#searchButton").click(onSearchButtonClick);
	$("#confirm-dialog").dialog({
		modal:true, autoOpen:false,
		buttons: {
			'ตกลง': function() {
				onDeleteButtonClick();
			},
			'ยกเลิก': function() {
				$(this).dialog('close');
			}
		}
	});
	$(".sort").click(onSortClick);
	$("#historyButton").click(onHistoryClick);
});
function onAddButtonClick() {
	document.location.replace("<?= site_url('order/add_page') ?>");
}
function onBeforeDeleteButtonClick() {
	if (onFormSubmit()) {
		showConfirmDialog();
	}
}
function onMainButtonClick(){
	document.location.replace("<?= site_url("/main") ?>");
}
function onDeleteButtonClick() {
	$("#order_form").attr("action", "<?= site_url('order/delete_method') ?>").submit();
}
function onFormSubmit() {
	return ($(".checkbox:checked").size() > 0);
}
function showConfirmDialog() {
	$("#confirm-dialog").dialog("open");
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
function onHistoryClick() {
	document.location.replace("<?= site_url("/order/history") ?>");
	//$("#history_form").submit();
}
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
</script>