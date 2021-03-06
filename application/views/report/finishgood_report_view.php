<style type="text/css" rel="stylesheet" media="print">
html,body {font-size: 8pt;font-family:Tahoma; }
#menuTable, #header-panel, #navigation-panel { display: none; }
.stock { border: none;}
#dateProduced { width: 20px; }
#totalUnit { width: 100px; }
#totalWeight { width: 200px; }
#border1 { width: 50px;}
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
<table id="my-content-table" border="1" width="1200" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center" id="dateProduced" width="150">Date</th>
			<th align="center" id="totalSlittedCoil">Total Slitted Coil</th>
			<th align="center" id="totalUnit">Total Units Produced(C)</th>
			<th align="center" id="totalUnit">Total Units Produced(C*)</th>
			<th align="center" id="totalUnitP">Total Units Produced(P)</th>
			<th align="center" id="totalUnitP1">Total Units Produced(P*)</th>
			<th align="center"></th>
			<th align="center" id="totalSlittedWeight">Total Slitted Weight</th>
			<th align="center" id="totalWeight">Total Weight Produced(C)</th>
			<th align="center" id="totalWeight">Total Weight Produced(C*)</th>
			<th align="center" id="totalWeightP">Total Weight Produced(P)</th>
			<th align="center" id="totalWeightP1">Total Weight Produced(P*)</th>
		</tr>
	</thead>
	<tbody>
	<?
		$i = 0;
		foreach($report_result as $item) {
			//$reportPeriod = 7;
			
	?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?> datarow">
			<td><?= $item["dateProduced"] ?></td>
			<td><?= number_format($item["totalSlittedCoil"], 0) ?></td>
			<td><?= number_format($item["totalUnit"], 0) ?></td>
			<td><?= number_format($item["totalUnitC1"], 0) ?></td>
			<td><?= number_format($item["totalUnitP"], 0) ?></td>
			<td><?= number_format($item["totalUnitP1"], 0) ?></td>
			<td></td>
			<td><?= number_format($item["totalSlittedWeight"], 0) ?></td>
			<td><?= number_format($item["totalWeight"], 0) ?></td>
			<td><?= number_format($item["totalWeightC1"], 0) ?></td>
			<td><?= number_format($item["totalWeightP"], 0) ?></td>
			<td><?= number_format($item["totalWeightP1"], 0) ?></td>
		</tr>
	<? $i++;} ?>
	</tbody>
</table>
<br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button();
	$("#mainButton").click(onMainButtonClick);
	$(".numeric").set_format({precision: 0,autofix:true,allow_negative:false});
	//$(".datarow").click(onDataRowClick);
	$("#backButton").click(onBackButton);
	$("#saveButton").click(onSaveButton);
});
function onSaveButton() {
	document.location.href="<?= site_url("/report/finishgood_report") ?>";
}
function onBackButton() {
	document.location.href="<?= site_url("/report") ?>";
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
</script>