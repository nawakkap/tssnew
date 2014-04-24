<?= form_open("/user/add_page", array('id' => 'user_form')) ?>
<table border="0" width="100%">	
	<tr>
		<td align="right">
			<input type="button" id="addButton" name="addButton" class="button" value="เพิ่มข้อมูล" />
			<input type="submit" id="editButton" name="editButton" class="button" value="แก้ไขข้อมูล" />
			<input type="button" id="deleteButton" name="deleteButton" class="button" value="ลบข้อมูล" />
		</td>
	</tr>
</table>
<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center" width="30">&nbsp;</th>
			<th align="center" width="70%">ชื่อ - นามสกุล</th>
			<th align="center" width="30%">User Type</th>
		</tr>
	</thead>
	<tbody>
		<? for($i = 0; $i < count($result) ; $i++) { ?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
			<td><input type="radio" name="username" value="<?= $result[$i]->username ?>" class="checkbox" /></td>
			<td><?= $result[$i]->first_name . " " . $result[$i]->last_name ?></td>
			<td><?= ucfirst($result[$i]->user_type) ?></td>
		</tr>
		<? } ?>
	</tbody>
</table>
<?= form_close() ?>
<table border="0" width="100%">	
	<tr>
		<td align="right">
			<input type="button" id="mainButton" name="mainButton" class="button" value="ย้อนกลับสู่หน้าหลัก" />
		</td>
	</tr>
</table>
<div id="confirm-dialog" title="คำยืนยัน" style="display:none">คุณต้องการลบข้อมูลนี้ใช่หรือไม่</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#mainButton").click(onMainButtonClick);
	$("#addButton").click(onAddButtonClick);
	$("#user_form").submit(onFormSubmit);
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
});
function onAddButtonClick() {
	document.location.replace("<?= site_url("/user/add_page") ?>");
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function onBeforeDeleteButtonClick() {
	if (onFormSubmit()) {
		showConfirmDialog();
	}
}
function onDeleteButtonClick() {
	$("#user_form").attr("action", "<?= site_url('/user/delete_method') ?>").submit();
}
function onFormSubmit() {
	return ($(".checkbox:checked").size() > 0);
}
function showConfirmDialog() {
	$("#confirm-dialog").dialog("open");
}
</script>