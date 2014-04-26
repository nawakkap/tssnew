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
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="custname">ชื่อลูกค้า</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="who">ผู้นัด</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="sono">SO NO</a></th>
			<!-- <th align="center" nowrap="nowrap"><a href="#" class="sort" rel="product_id">Product ID</a></th> -->
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="prod_name">สินค้า</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="delivery_date">วันที่นัดส่ง</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="priority">ความสำคัญ</a></th>
			<th align="center" nowrap="nowrap">จำนวนนัด</th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="order_date">วันที่สั่งซื้อ</a></th>
		</tr>
	</thead>
	<tbody>
	<?
		$i = 0;
		foreach($report_result as $item) {
			//$reportPeriod = 7;
			
	?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?> datarow">
			<td nowrap="nowrap"><?= $item["custname"] ?></td>
			<td><?= $item["who"] ?></td>
			<td><?= $item["sono"] ?></td>
			<!-- <td><?= $item["product_id"] ?></td> -->
			<td nowrap="nowrap"><?= $item["prod_name"] ?></td>
			<td nowrap="nowrap"><?= $item["delivery_date"] ?></td>
			<td><?= $item["priority"] ?></td>
			<td><?= $item["item"] ?></td>
			<td nowrap="nowrap"><?= $item["order_date"] ?></td>	
		</tr>
	<? $i++;} ?>
	</tbody>
</table>
<input type="hidden" id="rowCount" name="rowCount" value="<?= $i ?>"/>
<?= form_open("/report/delivery_report", array("id" => "sort_form")) ?>
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<?= form_close() ?>

<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>

<br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button();
	$("#mainButton").click(onMainButtonClick);
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