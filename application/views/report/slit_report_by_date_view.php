<style type="text/css" rel="stylesheet" media="print">
html,body {font-size: 8pt;font-family:Tahoma; }
#menuTable, #header-panel, #navigation-panel { display: none; }
.stock { border: none;}
#dateProduced { width: 20px; }
#extProgramCode { width: 100px; }
#productName { width: 200px; }
#grade { width: 20px; }
#unit { width: 25px; }
#my-content-table { border-width: 1px; border-style:solid; border-color: black;} 
#my-content-table thead tr {background-color: #cccccc; }
</style>
<table id="menuTable" border="0" width="100%" class="ui-widget">
	<tr>
		<td align="right">
		<button id="backButton">ย้อนกลับ</button>&nbsp;&nbsp;
		<button id="mainButton">กลับสู่เมนูหลัก</button></td>
	</tr>
</table>
<table id="my-content-table" border="1" width="1100" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center"><a href="#" class="sort" rel="sortSlitDate">Slit Date</a></th>
			<th align="center"><a href="#" class="sort" rel="lot">LOT ID</a></th>
			<th align="center"><a href="#" class="sort" rel="thickness">Thickness</a></th>
			<th align="center"><a href="#" class="sort" rel="coil">#Coil</a></th>
			<th align="center"><a href="#" class="sort" rel="weight">Weight</a></th>
			<th align="center"><a href="#" class="sort" rel="slitDesc">Slit Description</a></th>
		</tr>
	</thead>
	<tbody>
	<?
		$i = 0;
		foreach($report_result as $item) {
			//$reportPeriod = 7;
			
	?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?> datarow">
			<td><?= $item["slitted_date"] ?></td>
			<td><?= $item["coil_group_code"] ?></td>
			<td><?= $item["thickness"] ?></td>
			<td><?= $item["coils"] ?></td>
			<td><?= $item["weight"] ?></td>
			<td><?= $item["remark"] ?></td>
		</tr>
	<? $i++;} ?>
	</tbody>
</table>
<input type="hidden" id="rowCount" name="rowCount" value="<?= $i ?>"/>
<?= form_open("/report/slit_report", array("id" => "sort_form")) ?>
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<?= form_close() ?>

<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>

<br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button();
	$("#mainButton").click(onMainButtonClick);
	$("#saveButton").click(onSaveButton);
	$(".slittedCoil").keyup(onSlittedCoilKeyUp);
	//$(".datarow").click(onDataRowClick);
	$("input").keypress(onChangeFocus);
	$(".numeric").set_format({precision: 0,autofix:true,allow_negative:false});
	$("#backButton").click(onBackButton);
	$(".sort").click(onSortClick);
	$(".currentDate").datepicker({dateFormat:'yy-mm-dd', showAnim : ""});
});
function onBackButton() {
	document.location.href="<?= site_url("/report") ?>";
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function onSlittedCoilKeyUp() {

	if (!$(this).val()) {
		$(this).val(0);
		$(this).select();
	}

	var i = $(this).attr("rel1");
	var coil = $(this).attr("rel");
	var weight = $(this).attr("rev");
	var pastSlittedCoil = $(this).attr("rel2");
	var slittedCoil = parseFloat($(this).val());

	var slittedWeight = weight/coil*slittedCoil;

	if(slittedCoil > coil-pastSlittedCoil){
		$(this).val(0);
		alert("The slitted coils amount exceeds actual number of coil");	
	}else{
		$("#slittedWeight_"+i).val(slittedWeight);
	}
	
}
function onChangeFocus(event) {
	if (event.keyCode == 13) {
		if ($(".stock").index($(this)) == ($(".stock").size() - 1)) {
			$(".stock:first").focus();
		} else {
			$(this).focusNextInputField();
		}
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
</script>