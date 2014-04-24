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
		<button id="saveButton" title="Summary Report">Summary Report</button>&nbsp;&nbsp;
		<button id="backButton">ย้อนกลับ</button>&nbsp;&nbsp;
		<button id="mainButton">กลับสู่เมนูหลัก</button></td>
	</tr>
</table>
<table id="my-content-table" border="1" width="900" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center" ><a href="#" class="sort" rel="sortDate">Date</th>
			<th align="center" ><a href="#" class="sort" rel="machine">Machine</th>
			<th align="center" ><a href="#" class="sort" rel="extProgramCode">External Program Code</th>
			<th align="center" ><a href="#" class="sort" rel="productName">Product Name</th>
			<th align="center" ><a href="#" class="sort" rel="grade">Grade</th>
			<th align="center" ><a href="#" class="sort" rel="unit">Units Produced</th>
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
			<td><?= $item["machine"] ?></td>
			<td><?= $item["extProgramCode"] ?></td>
			<td nowrap="nowrap"><?= $item["productName"] ?></td>
			<td><?= $item["grade"] ?></td>
			<td><?= number_format($item["unit"], 0) ?></td>
		</tr>
	<? $i++;} ?>
	</tbody>
</table>

<?= form_open("/report/finishgood_detail_report", array("id" => "sort_form")) ?>
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<?= form_close() ?>

<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>

<br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button();
	$("#mainButton").click(onMainButtonClick);
	//$(".datarow").click(onDataRowClick);
	$("#backButton").click(onBackButton);
	$(".sort").click(onSortClick);
	$("#saveButton").click(onSaveButton);
});
function onBackButton() {
	document.location.href="<?= site_url("/report") ?>";
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function onSaveButton() {
	document.location.href="<?= site_url("/report/finishgood_report") ?>";
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