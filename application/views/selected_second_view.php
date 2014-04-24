<table border="0" width="100%" cellpadding="4" class="ui-widget">
	<tr>
		<td align="left" class="ui-state-highlight" width="20%" nowrap="nowrap"><b>2. เลือก Coil</b>&nbsp;&nbsp;&nbsp;<input type="button" name="mainButton" class="button mainButton" value="กลับสู่หน้าหลัก"/></td>
		<td align="right" class="ui-state-highlight">จำนวน&nbsp;<b id="total_unit">0</b>&nbsp;Coil(s)&nbsp;&nbsp;&nbsp;น้ำหนักรวม&nbsp;<b id="total_weight">0</b>&nbsp;กิโลกรัม</td>
	</tr>
</table>
<table border="0" width="100%" cellpadding="4" class="ui-widget">
	<tr>
		<td width="20%"><b>&nbsp;PO ID&nbsp;:&nbsp;<?= $po_id ?></b></td>
		<td align="right"><input type="button" id="reset_checkbox" class="button" name="reset_checkbox" value="เลือกใหม่อีกครั้ง" /></td>
	</tr>
</table>
<?= form_open("/select_coil/third_step", array("id" => "my_form")) ?>
<input type="hidden" name="po_id" value="<?= $po_id ?>"/>

<table id="coil_table_header" border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="30">&nbsp;</th>
			<th align="center" width="20%"><a href="#" class="sort" rel="coil_id">Coil ID</a></th>
			<th align="center" width="10%"><a href="#" class="sort" rel="coil_lot_no">Coil No</a></th>
			<th align="center" width="15%"><a href="#" class="sort" rel="thickness">ความหนา</a></th>
			<th align="center" width="15%"><a href="#" class="sort" rel="width">ความกว้าง</a></th>
			<th align="center" width="15%"><a href="#" class="sort" rel="weight">น้ำหนัก</a></th>
			<th align="center" width="20%"><a href="#" class="sort" rel="coil_received_date">รับมาวันที่</a></th>
		</tr>
	</thead>
</table>
<table id="coil_table" border="1" width="100%" class="table-data ui-widget">
	<tbody>
		<?
			$bool = FALSE;
			foreach($coil_result as $coil_item) {
				foreach($coil_item as $item) {
				$key = $item["coil_id"] . $item["coil_lot_no"] . $item["po_id"];
				
				$checked =  FALSE;
				if (isset($selected_list[$key])) {
					$checked = TRUE;
				}
				
				$checkbox = array(
					'name' => 'coil_id[]',
					'class' => 'checkbox',
					'value' => $item["coil_id"],
					'weight' => $item["weight"],
					'checked' => $checked
				);
		
		?>
		<tr class="<?= ($bool) ? "even" : "odd" ?>">
			<td align="center" width="30"><?= form_checkbox($checkbox) ?></td>
			<td width="20%"><?= $item["coil_id"] ?></td>
			<td width="10%"><?= $item["coil_lot_no"] ?></td>
			<td width="15%"><?= number_format($item["thickness"] ,2) ?></td>
			<td width="15%"><?= number_format($item["width"], 2) ?></td>
			<td width="15%"><?= number_format($item["weight"], 0) ?></td>
			<td width="20%"><?= mysqldatetime_to_date($item["coil_received_date"] , 'd/m/Y') ?></td>
		</tr>
		<?
				$bool = !$bool;}
			} ?>
	</tbody>
</table><br/>
<table border="0" width="100%">
	<tr>
		<td align="left"><input type="reset" id="backButton" name="TReset" class="button" value="ย้อนกลับ" /></td>
		<td align="right"><input type="submit" name="Tsubmit" class="button" value="ถัดไป" /></td>
	</tr>
</table>
<?= form_close() ?>


<?= form_open("/select_coil/second_step", array("id" => "sort_form")) ?>
<input type="hidden" name="po_id" value="<?= $po_id ?>"/>
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<?= form_close() ?>

<?= form_open("/select_coil/", array("id" => "backform")) ?>
<input type="hidden" name="po_id" value="<?= $po_id ?>"/>
<?= form_close() ?>
<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>
<div id="warning" title="info" style="display:none">กรุณาเลือก Coil</div>
<br/><br/><br/>
<style type="text/css" rel="stylesheet">
#coil_table .ui-selecting { background: #FECA40; }
</style>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#backButton").click(onBackButtonClick);
	$(".checkbox").click(onCheckBoxClick);
	$("#my_form").submit(onFormSubmit);
	$(".mainButton").click(onMainButtonClick);
	$("#reset_checkbox").click(onResetCheckboxClick);
	
	$("#coil_table").selectable({
		selected: function(event, ui) {
			if (ui.selected.tagName == "TR")
			{
				var obj = $(ui.selected);
				obj.children("td:first").children("input").attr("checked", "checked");
				onCheckBoxClick();
			}
		}
	});
	
	$(".sort").click(onSortClick);
});
function onResetCheckboxClick() {
	$(".checkbox").removeAttr("checked");
	onCheckBoxClick();
}
function onBackButtonClick() {
	$("#backform").submit();
}
function onMainButtonClick(){
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
	if ($(".checkbox:checked").size() < 1) {
		$("#warning").dialog({modal : true});
		return false;
	}
	return true;
}
function onCheckBoxClick() {
	var total_weight =  0;
	$.each($(".checkbox:checked"), function(index) {
		total_weight += parseFloat($(this).attr("weight"));
	});
	
	$("#total_unit").text($(".checkbox:checked").size()).format({format: "#,###"});
	$("#total_weight").text(total_weight).format({format: "#,###"});
}
</script>