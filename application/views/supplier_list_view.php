<?= form_open("/supplier/add_page", array('id' => 'supplier_form')) ?>
<table border="0" width="100%">	
	<tr>
		<td>
			<input type="button" id="addButton" name="addButton" class="button" value="เพิ่มข้อมูล" />
			<input type="submit" id="editButton" name="editButton" class="button" value="แก้ไขข้อมูล" />
			<input type="button" id="deleteButton" name="deleteButton" class="button" value="ลบข้อมูล" />
		</td>
		<td align="right" valign="bottom" class="remark-text">&nbsp;</td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="25" align="center">&nbsp;</th>
			<th align="center"><a href="#" class="search" rel="supplier_name">ชื่อ</a></th>
			<th align="center"><a href="#" class="search" rel="payment_type">เครดิต</a></th>
		</tr>
	</thead>
	<tbody>
		<? for($i = 0; $i < count($result); $i++) { ?>
		<tr class="<?= ($i % 2 == 0) ? "even" : "odd" ?>">
			<td align="center"><input type="radio" name="supplier_id" class="checkbox" value="<?= $result[$i]->supplier_id ?>" /></td>
			<td><a href="<?= site_url("/supplier/supplier_detail/". $result[$i]->supplier_id) ?>" class="link"><?= $result[$i]->supplier_name ?></a></td>
			<td align="center" width="20%"><?= $payment_result[$result[$i]->payment_type] ?></td>
		</tr>
		<? } ?>
	</tbody>
</table>
<?= form_close() ?>
<?= form_open("/supplier", array('id' => 'search_form')) ?>
<input type="hidden" id="search" name="search" value="" />
<input type="hidden" id="search_type" name="search_type" value="<?= $search_type ?>" />
<?= form_close() ?>
<table border="0" width="100%">
	<tr>
		<td align="right"><input type="button" id="mainButton" name="mainButton" class="button" value="ย้อนกลับสู่หน้าหลัก" /></td>
	</tr>
</table>
<br/><br/><br/>
<div id="confirm-dialog" title="คำยืนยัน" style="display:none">คุณต้องการลบข้อมูลนี้ใช่หรือไม่</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#addButton").click(onAddButtonClick);
	$("#supplier_form").submit(onFormSubmit);
	$("#deleteButton").click(onBeforeDeleteButtonClick);
	$("#mainButton").click(onMainButtonClick);
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
	$(".search").click(onSearchClick);
});
function onAddButtonClick() {
	document.location.replace("<?= site_url("supplier/add_page") ?>");
}
function onEditButtonClick(){
}
function onBeforeDeleteButtonClick() {
	if (onFormSubmit()) {
		showConfirmDialog();
	}
}
function onMainButtonClick(){
	document.location.replace("<?= site_url("/main") ?>");
}
function onFormSubmit() {
	return ($(".checkbox:checked").size() > 0);
}
function onDeleteButtonClick() {
	$("#supplier_form").attr("action", "<?= site_url('supplier/delete_method') ?>").submit();
}
function showConfirmDialog() {
	$("#confirm-dialog").dialog("open");
}
function onSearchClick() {
	var search_by = $(this).attr("rel");
	$("#search").val(search_by);
	$("#search_type").val();
	$("#search_form").submit();
}
</script>