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
<?= form_open("/report/save_slitted_coil_report", array("id" => "report_form")) ?>
<table id="menuTable" border="0" width="100%" class="ui-widget">
	<tr>
		<td align="right">
		Slitted Date
		<input type="text" name="currentDate"  size="10" class="currentDate" value="<?= date('Y-m-d', strtotime("-1 day")) ?>" />
		<button id="saveButton">Save coil slitted</button>&nbsp;&nbsp;
		<button id="backButton">ย้อนกลับ</button>&nbsp;&nbsp;
		<button id="mainButton">กลับสู่เมนูหลัก</button></td>
	</tr>
</table>
<table id="my-content-table" border="1" width="1100" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center"><a href="#" class="sort" rel="sortSlitDate">Slit Date</a></th>
			<th align="center"><a href="#" class="sort" rel="thickness">Thickness</a></th>
			<th align="center"><a href="#" class="sort" rel="lot">LOT ID</a></th>
			<th align="center"><a href="#" class="sort" rel="slitDesc">Slit Description</a></th>
			<th align="center"><a href="#" class="sort" rel="coil">#Coil</a></th>
			<th align="center"><a href="#" class="sort" rel="weight">Weight</a></th>
			<th align="center">Slitted Coils</th>
			<th align="center">Slitted Weight</th>
			<th align="center">Today Slitted Coils</th>
			<th align="center">Today Slitted Weight</th>
			<th align="center">Machine</th>
		</tr>
	</thead>
	<tbody>
	<?
		$i = 0;
		foreach($report_result as $item) {
			//$reportPeriod = 7;
			
	?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?> datarow">
			<td><?= $item["slitDate"] ?></td>
			<td><?= $item["thickness"] ?></td>
			<td><?= $item["lot"] ?></td>
			<td><?= $item["slitDesc"] ?></td>
			<td><?= $item["coil"] ?></td>
			<td><?= $item["weight"] ?></td>
			<td><?= $item["slitted"] ?></td>
			<td><?= $item["slittedWeight"] ?></td>
			<td><input type="text" class="slittedCoil" name="slittedCoil_<?=$i?>" size="2" value="0" rel2=<?= $item["slitted"] ?> rel1=<?=$i?> rel="<?= $item["coil"] ?>" rev="<?= $item["weight"] ?>"/></td>
			<td><input type="text" id="slittedWeight_<?=$i ?>" name="slittedWeight_<?=$i?>" value="0" readonly="readonly"/></td>
			<td><?= form_dropdown("machine_".$i , $machine) ?></td>
			<input type="hidden" id="pastSlittedCoil_<?=$i ?>" name="pastSlittedCoil_<?=$i ?>" value="<?= $item["slitted"] ?>"/>
		</tr>
	<? $i++;} ?>
	</tbody>
</table>
<input type="hidden" id="rowCount" name="rowCount" value="<?= $i ?>"/>
<?= form_close() ?>
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
function onSaveButton() {
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