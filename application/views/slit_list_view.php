<?= form_open("/slit") ?>
<table border="0" width="100%" class="ui-widget">	
	<tr>
		<td>
			<input type="button" id="addButton" name="addButton" class="button" value="เพิ่มข้อมูล" />
			<input type="button" id="editButton" name="editButton" class="button" value="แก้ไขข้อมูล" />
			<input type="button" id="deleteButton" name="deleteButton" class="button" value="ลบข้อมูล" />
		</td>
		<td align="right" valign="bottom">
			ความหนา
			<input type="text" id="searchText" name="searchText" class="numeric" value="" />
			<input type="submit" id="searchButton" name="searchButton" class="button" value="ค้นหา" />
		</td>
	</tr>
</table>
<?= form_close() ?>
<?= form_open("/slit/add_page", array('id' => 'slit_form')) ?>
<input type="hidden" id="editButtonMode" name="editButton" value="" />
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="25" align="center">&nbsp;</th>
			<th align="center"><a href="#" class="search" rel="slit_thickness">ความหนา</a></th>
			<th align="center">รูปแบบ (มิลลิเมตร x จำนวน)</th>
			<th align="center">Product</th>
		</tr>
	</thead>
	<tbody>
	<? for($i = 0; $i < count($result); $i++) { ?>
	<?php 
	
		// $isValid = isset($product_result[$result[$i]->product_dtl_1]) && isset($product_result[$result[$i]->product_dtl_2]) && isset($product_result[$result[$i]->product_dtl_3]);
	
		$isValid = TRUE;
		if (isset($result[$i]->product_dtl_1) && $isValid) {
			$isValid = isset($product_result[$result[$i]->product_dtl_1]);
		}
		if (isset($result[$i]->product_dtl_2) && $isValid) {
			$isValid = isset($product_result[$result[$i]->product_dtl_2]);
		}
		if (isset($result[$i]->product_dtl_3) && $isValid) {
			$isValid = isset($product_result[$result[$i]->product_dtl_3]);
		}
		
		if ($isValid) {
	?>
		<tr class="<?= ($i % 2 == 0) ? "even" : "odd" ?>">
			<td><input type="radio" name="slit_spec_id" class="checkbox" value="<?= $result[$i]->slit_spec_id ?>" /></td>
			<td><?= number_format($result[$i]->slit_thickness, 2) ?></td>
			<td><a href="<?= site_url("/slit/slit_detail/" . $result[$i]->slit_spec_id) ?>" class="link"><?
				if (isset($result[$i]->slit_dtl_1)) {
					echo "(" . $result[$i]->slit_dtl_1 . ")&nbsp;";
				}
				if (isset($result[$i]->slit_dtl_2)) {
					echo "(" . $result[$i]->slit_dtl_2 . ")&nbsp;";
				}
				if (isset($result[$i]->slit_dtl_3)) {
					echo "(" . $result[$i]->slit_dtl_3 . ")";
				}
			?></a></td>
			<td><?
				$is_product = FALSE;
				if (isset($result[$i]->product_dtl_1)) {
					echo $product_result[$result[$i]->product_dtl_1];
					$is_product = TRUE;
				}
				if (isset($result[$i]->product_dtl_2)) {
					if ($is_product) { echo " x "; $is_product = FALSE; }
					echo $product_result[$result[$i]->product_dtl_2];
					$is_product = TRUE;
				}
				if (isset($result[$i]->product_dtl_3)) {
					if ($is_product) { echo " x "; $is_product = FALSE; }
					echo $product_result[$result[$i]->product_dtl_3];
				}
			?></td>
		</tr>
		<? } ?>
	<? } ?>
	</tbody>
</table>
<table border="0" width="100%">
	<tr>
		<td align="right"><input type="button" id="mainButton" name="mainButton" class="button" value="ย้อนกลับสู่หน้าหลัก" /></td>
	</tr>
</table>
<?= form_close() ?>
<br/><br/><br/>
<?= form_open("/slit", array('id' => 'order_by_form')) ?>
<input type="hidden" class="searchText" name="searchText" value="<?= $searchText ?>" />
<input type="hidden" id="order_by" name="order_by" value="" />
<input type="hidden" id="order_by_type" name="order_by_type" value="<?= $order_by_type ?>" />
<?= form_close() ?>
<?= form_open("/slit", array('id' => 'search_form')) ?>
<input type="hidden" class="searchText" name="searchText" value="<?= $searchText ?>" />
<?= form_close() ?>
<div id="confirm-dialog" title="คำยืนยัน" style="display:none">คุณต้องการลบข้อมูลนี้ใช่หรือไม่</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#slit_form").submit(onFormSubmit);
	$("#addButton").click(onAddButtonClick);
	$("#editButton").click(onEditButtonClick);
	$("#deleteButton").click(onBeforeDeleteButtonClick);
	$("#mainButton").click(onMainButtonClick);
	$("#searchButton").click(onSearchButton);
	$(".numeric").set_format({precision: 2,autofix:true,allow_negative:false});
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
	$(".search").click(onOrderByClick);
});
function onSearchButton() {
	$(".searchText").val($("#searchText").val());
	$("#search_form").submit();
}
function onAddButtonClick() {
	document.location.replace("<?= site_url('slit/add_page') ?>");
}
function onBeforeDeleteButtonClick() {
	if (onFormSubmit()) {
		showConfirmDialog();
	}
}
function onMainButtonClick(){
	document.location.replace("<?= site_url("/main") ?>");
}
function onEditButtonClick() {
	$("#editButtonMode").val("editButton");
	$("#slit_form").submit();
}
function onDeleteButtonClick() {
	$("#slit_form").attr("action", "<?= site_url('slit/delete_method') ?>").submit();
}
function onFormSubmit() {
	return ($(".checkbox:checked").size() > 0);
}
function showConfirmDialog() {
	$("#confirm-dialog").dialog("open");
}
function onOrderByClick() {
	var order_by = $(this).attr("rel");
	$("#order_by").val(order_by);
	$("#order_by_type").val();
	$("#order_by_form").submit();
}
</script>
