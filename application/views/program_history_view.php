<style type="text/css" rel="stylesheet">
.red_color {
	font-weight: bolder;
	color : white;
	background-color: red;
}
</style>
<?= form_open("/program/history") ?>
<table border="0" width="900" class="ui-widget">
	<tr>
		<td align="left">
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />&nbsp;
			<input type="button" id="filterButton" name="filterButton" value="<?= $startDate ?> / <?= $endDate ?>" onclick="onDateFilterClick()" />
		</td>
		<td align="right">
			<?
				$dropdown = array("p_program.program_code_ext" => "Ext. Program Code",
											 "p_detail.product_name_th" => "ชื่อสินค้า");
											 
				echo form_dropdown("searchType", $dropdown, $searchType);
			?>
		&nbsp;<input type="text" name="searchText" value="<?= $searchText ?>" />&nbsp;<input type="submit" name="searchButton" class="button" value="ค้นหา" /></td>
	</tr>
	<tr id="filterRow">
		<td colspan="2">&nbsp;
		</td>
	</tr>
</table>
<?= form_close() ?>
<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center"><a href="#" class="sort" rel="p_program.program_code_ext">Ext. Code</a></th>
			<th align="center"><a href="#" class="sort" rel="total_unit">จำนวนเส้นที่ผลิต</a></th>
			<th align="center"><a href="#" class="sort" rel="weight">น้ำหนักรวม</a></th>
			<th align="center"><a href="#" class="sort" rel="avg_weight">น้ำหนักเฉลี่ย</a></th>
			<th align="center"><a href="#" class="sort" rel="grade_b">% เกรด B</a></th>
			<th align="center"><a href="#" class="sort" rel="p_program.product_dtl_id">ชื่อสินค้า</a></th>
			<th align="center"><a href="#" class="sort" rel="p_program.processing_date">วันที่</a></th>
			<th align="center"><a href="#" class="sort" rel="p_program.program_status">สถานะ</a></th>
		</tr>
	</thead>
	<tbody>
	<?
		$bool = FALSE;
		foreach($program_result as $item) { 
		
			$red = FALSE;
			if ($item["grade_b"] > 1) {
				$red = TRUE;
			}
			
			$w_red = FALSE;
			if (/* $item["avg_weight"] < $item["est_weight_min"] || */ $item["actual_weight"] < $item["avg_weight"] )
			{
				$w_red = TRUE;
			}
			
			$class = "";
			if ($bool) {
				$class = "odd";
			} else {
				$class = "even";
			}
			
			$grade_b_class = "";
			if ($red) {
				$grade_b_class = "red_color";
			} else {
				$grade_b_class = "";
			}
			
			$w_class = "";
			if ($w_red) {
				$w_class = "red_color";
			} else {
				$w_class = "";
			}
			
			/*
			if ($item['weight'] < $item["est_weight_min"] || $item["weight"] > $item["accounting_weight"])
			{
				$weight_class = "red_color";
			}
			else
			{
				$weight_class = "";
			}
			*/
			
			$weight_class = "";
			
	?>
		<tr class="<?= $class ?>">
			<td><a href="<?= site_url("/program/program_detail/" . $this->convert->AsciiToHex($item['program_code']) . "/" . $this->convert->AsciiToHex($item['product_dtl_id']) . "/1") ?>" class="link"><?= $item['program_code_ext'] ?></a></td>
			<td><?= number_format($item['total_unit'], 0) ?></td>
			<td><?= number_format($item["weight"], 0) ?></td>
			<td class="<?php echo $w_class; ?>"><?= number_format($item["avg_weight"] ,3) ?></td>
			<td class="<?= $grade_b_class ?>"><?= number_format($item['grade_b'], 2) ?></td>
			<td nowrap="nowrap"><?= $item["product_name_th"] ?></td>
			<td><a href="#" class="link" onclick="onDateClick('<?= mysqldatetime_to_date($item['processing_date'], 'Y-m-d') ?>')"><?= mysqldatetime_to_date($item['processing_date'], 'd/m/Y') ?></a></td>
			<td nowrap="nowrap"><?= $program_status_result[$item["program_status"]] ?></td>
		</tr>
	<? 		$bool = !$bool;
		} 
	?>
	</tbody>
</table>
<?= form_open("/program/history", array("id" => "sort_form")) ?>
<input type="hidden" name="searchType" value="<?= $searchType ?>" />
<input type="hidden" name="searchText" value="<?= $searchText ?>" />
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" name="startDate" value="<?= $startDate ?>" />
<input type="hidden" name="endDate" value="<?= $endDate ?>" />
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<?= form_close() ?>
<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>
<div id="dateFilterPanel" title="Filter By Date" style="display:none;">
<?= form_open("program/history", array("id" => "FilterByDateForm")) ?>
<table border="0" class="ui-widget">
	<tr>
		<td>From :</td>
		<td><input type="text" id="startDate" name="startDate" value="<?= $startDate ?>" readonly="readonly" /></td>
	</tr>
	<tr>
		<td>To :</td>
		<td><input type="text" id="endDate" name="endDate" value="<?= $endDate ?>" readonly="readonly" /></td>
	</tr>
</table>
<?= form_close() ?>
</div>
<br/><br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$(".button, #filterButton").button();
	$("#filterRow").hide();
	$("#backButton").click(onBackButtonClick);
	$(".sort").click(onSortClick);
	$("#startDate, #endDate").datepicker({
		dateFormat : 'yy-mm-dd'
	});
});

function onDateClick(theDate) {
	$("#startDate").val(theDate);
	$("#endDate").val(theDate);
	$("#info").dialog({
		modal: true
	});
	$("#FilterByDateForm").submit();
}
function onDateFilterClick()  {
	$("#dateFilterPanel").dialog({
		modal: true,
		buttons : {
			'ตกลง' : function() {
				$("#info").dialog({
					modal: true
				});
				$("#FilterByDateForm").submit();
			},
			'ปิด' : function() {
				$(this).dialog("close");
			}
		}
	});
}
function onSortClick() {
	$("#info").dialog({
		modal: true
	});
	var rel = $(this).attr("rel");
	if (rel != $("#sort_column").val()) {
		$("#sort_by").val("asc");
	} else {
		$("#sort_by").val(($("#sort_by").val() ==  "asc") ? "desc" : "asc");
	}
	$("#sort_column").val(rel);
	$("#sort_form").submit();
}
function onBackButtonClick() {
	history.back();
	document.location.href="<?= site_url("/program/now_page") ?>";
}
</script>