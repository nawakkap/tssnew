<?= form_open("/product/add_page", array('id' => 'product_form')) ?>
<table border="0" width="100%" class="ui-widget">	
	<tr>
		<td align="left">
			<input type="button" id="addButton" name="addButton" class="button" value="เพิ่มข้อมูล" />
			<input type="submit" id="editButton" name="editButton" class="button" value="แก้ไขข้อมูล" />
			<input type="button" id="deleteButton" name="deleteButton" class="button" value="ลบข้อมูล" />
		</td>
		<td align="right">
			Filter by : 
			<?php
				
				$option = array(
					"" => "",
					"Y" => "Active",
					"N" => "InActive"
				);
				
				$js = 'id="filter_type" onchange="onFilterChange()"';
				echo form_dropdown("filter_type", $option, $filter, $js);
			
			?>
			<input type="button" name="mainButton" class="button mainButton" style="width: 180px" value="ย้อนกลับหน้าหลัก" />
		</td>
	</tr>
</table>
<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center" width="30">&nbsp;</th>
			<th align="center"><a href="#" class="sort" rel="product_dtl_id">ชื่อไทย</a></th>
			<th align="center"><a href="#" class="sort" rel="product_name_initial">ชื่อย่อ</a></th>
			<th align="center"><a href="#" class="sort" rel="thickness_rep">ความหนา</a></th>
			<th align="center"><a href="#" class="sort" rel="color">สี</a></th>
			<th align="center"><a href="#" class="sort" rel="size_detail">Size detail</a></th>
			<th align="center"><a href="#" class="sort" rel="weight_display">Weight display</a></th>
		</tr>
	</thead>
	<tbody>
		<? for($i = 0; $i < count($result) ; $i++) { ?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
			<td><input type="radio" name="product_dtl_id" class="checkbox" value="<?= $result[$i]->product_dtl_id ?>" /></td>
			<td><a href="<?= site_url("/product/product_detail/{$result[$i]->product_dtl_id}") ?>" class="link"><?= $result[$i]->product_name_th ?></a></td>
			<td><?= $result[$i]->product_name_initial ?></td>
			<td><?= number_format($result[$i]->thickness_rep, 2) ?></td>
			<td><?= $result[$i]->color ?></td>
			<td><?= $result[$i]->size_detail ?></td>
			<td><?= $result[$i]->weight_display ?></td>
		</tr>
		<? } ?>
	</tbody>
</table>
<?= form_close() ?>
<table border="0" width="100%">
	<tr>
		<td align="right"><input type="button" id="mainButton" name="mainButton" class="button mainButton" value="ย้อนกลับหน้าหลัก" /></td>
	</tr>
</table>
<br/><br/><br/>
<?= form_open("/product", array("id" => "sort_form")) ?>
<input type="hidden" id="filter" name="filter" value="<?= $filter ?>" />
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<?= form_close() ?>
<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>
<div id="confirm-dialog" title="คำยืนยัน" style="display:none">คุณต้องการลบข้อมูลนี้ใช่หรือไม่</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#addButton").click(onAddButtonClick);
	$("#product_form").submit(onFormSubmit);
	$(".mainButton").click(onMainButtonClick);
	$("#deleteButton").click(onBeforeDeleteButtonClick);
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
});
function onAddButtonClick() {
	document.location.replace("<?= site_url("/product/add_page") ?>");
}
function onBeforeDeleteButtonClick() {
	if (onFormSubmit()) {
		showConfirmDialog();
	}
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
	return ($(".checkbox:checked").size() > 0);
}
function onFilterChange() {
	$("#filter").val($("#filter_type").val());
	onSortClick();
}
function showConfirmDialog() {
	$("#confirm-dialog").dialog("open");
}
function onDeleteButtonClick() {
	$("#product_form").attr("action", "<?= site_url('product/delete_method') ?>").submit();
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
</script>