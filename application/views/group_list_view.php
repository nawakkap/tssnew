<?= form_open("/group/cancel_group", array("id" => "group_form")) ?>
<table border="1" width="50%" class="table-data ui-widget" cellpadding="5" align="center">
	<thead>
		<tr class="ui-widget-header">
			<th width="30">&nbsp;</th>
			<th align="center">Lot No</th>
			<th align="center">Program No</th>
			<th align="center">Ext. Program code</th>
		</tr>
	</thead>
	<tbody>
	<?
		$index = 0;
		foreach($group_result as $key => $item) {
	?>
		<tr class="<?= ($index % 2) ? "odd" : "even" ?>">
			<td><input type="radio" class="checkbox" name="coil_group_code" value="<?= $key ?>" /></td>
			<td><?= $key ?></td>
			<td><?= $item["program_code"] ?></td>
			<td><?= $item["program_code_ext"] ?></td>
		</tr>
	<? $index++; } ?>
	</tbody>
</table><br/>
<table border="0" width="100%">
	<tr>
		<td align="center"><input type="button" id="okButton" name="TSUBMIT" class="button" value="ตกลง" />&nbsp;&nbsp;&nbsp;<input type="button" id="mainButton" name="mainButton" class="button" value="กลับสู่หน้าหลัก" /></td>
	</tr>
</table>
<?= form_close() ?>

<div id="canceldialog" title="คำยืนยัน" style="display:none">
คุณแน่ใจที่ต้องการยกเลิก Lot นี้แล้วใช่หรือไม่
</div>


<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#okButton").click(onOKButtonClick);
	$("#mainButton").click(onMainButtonClick);
});
function onOKButtonClick() {
	if ($(".checkbox:checked").size() > 0) {

		$("#canceldialog").dialog({
			modal: true,
			buttons: {
				"ตกลง" : function (){
					$("#group_form").submit();
				},
				"ยกเลิก" : function() {
					$(this).dialog("close");
				}
			}
		});
	}
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
</script>