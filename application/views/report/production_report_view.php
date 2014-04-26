<?php
	function check_negative($num, $digit = 0)
	{
		if ($num < 0)
		{
			echo '<span class="negative">(' . number_format(abs($num), $digit) . ')</span>';
		}
		else
		{
			echo number_format($num, $digit);
		}
	}
?>
<style type="text/css" rel="stylesheet" media="print">
html,body {font-size: 8pt;font-family:Tahoma; }
#menuTable, #header-panel, #navigation-panel { display: none; }
.stock { border: none;}
#no { width : 30px; }
#productname { width: 200px; }
#detailsize { width: 100px; }
#trucksize { width: 20px; }
#inventory { width: 20px; }
#delivery { width: 25px; }
#backlog { width: 25px; }
#invleft { width: 100px; }
#stock { width: 15px;}
#stockunit { width: 5px;}
#inproduction { width: 20px;}
#totalkgneed { width: 25px;}
#percentfilm { width: 25px;}
#totalcoilneed { width: 25px;}
#my-content-table { border-width: 1px; border-style:solid; border-color: black;} 
#my-content-table thead tr {background-color: #cccccc; }
</style>
<style type="text/css" rel="stylesheet">
.negative{color:red;}
</style>
<?= form_open("/report/save_production_report", array("id" => "report_form")) ?>
<table id="menuTable" border="0" width="100%" class="ui-widget">
	<tr>
		<td align="left">
		ข้อมูล Excel <strong>PSTOCK3</strong> ของวันที่ <?= $max_date ?><br/>
		ข้อมูล Excel <strong>XPPDOR</strong> ของวันที่ <?= $xppdor_max_date ?>
		</td>
		<!--<button id="overal_stock_report">Overal Stock Report</button>-->
		
		<!--<button id="print">Print</button>-->
		<td align="right">
		<button id="saveButton" title="บันทึกเฉพาะค่า Stock เท่านั้น">เรียกดู Overall Stock</button>&nbsp;&nbsp;
		<button id="backButton">ย้อนกลับ</button>&nbsp;&nbsp;
		<button id="mainButton">กลับสู่เมนูหลัก</button></td>
	</tr>
</table>
<table id="my-content-table" border="1" width="900" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center" id="no">No</th>
			<th align="center" id="productname">Production Name</th>
			<!-- <th align="center" id="detailsize">Detail Size</th> -->
			<th align="center" id="trucksize">Truck Size</th>
			<th align="center" id="inventory">Inventory</th>
			<th align="center" id="delivery">Delivery</th>
			<th align="center" id="backlog">Backlog</th>
			<th align="center" id="invleft" nowrap="nowrap">Inventory_left</th>
			<th align="center" id="stock">Stock</th>
			<th align="center" id="stockunit" nowrap="nowrap">Stock Unit</th>
			<th align="center" id="inproduction" nowrap="nowrap">In Production</th>
			<th align="center" id="totalkgneed" nowrap="nowrap">Total Kg<br/>Need</th>
			<th align="center" id="percentfilm" nowrap="nowrap">% of Film</th>
			<th align="center" id="totalcoilneed" nowrap="nowrap">Total Coil<br/>Needed</th>
		</tr>
	</thead>
	<tbody>
	<?
		$i = 0;
		foreach($report_result as $item) {
			$total_coil_need = 0;
			
			$total_kg_need = -1*(((-$item["backlog"] + 0) * $item["est_weight"]) - $item["in_production"]);

			if ($item["perct_of_films"] != 0) {
				$total_coil_need = $total_kg_need / 0.99 / ($item["perct_of_films"] / 100);
			}
			
	?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?> datarow">
			<td><?= ($i + 1) ?></td>
			<td nowrap="nowrap" class="product_name" rel="<?= $item["product_display_id"] ?>" style="cursor:pointer;"><?= $item["product_name_th"] ?></td>
			<!-- <td nowrap="nowrap"><?= $item["detail_size"] ?></td>-->
			<td><?= number_format($item["truck_size"], 0) ?></td>
			<td><?= check_negative($item["delivery"] + $item["backlog"]) ?></td>
			<td><?= check_negative($item["delivery"]) ?></td>
			<td><?= check_negative($item["backlog"]) ?></td>
			<td align="left" nowrap="nowrap"><?= check_negative($item["stock_left"]) ?> + <?= number_format($item["stock_expect"], 0) ?></td>
			<td><input type="text" name="stock_<?= $item["product_dtl_id"] ?>" class="stock numeric" value="0" size="4" rel="<?= $item["truck_size"] ?>" rev="<?= $item["product_dtl_id"] ?>" /></td>
			<td id="<?= $item["product_dtl_id"] ?>">0</td>
			<td id="inproduction<?= $item["product_dtl_id"] ?>" rel="<?= $item["in_production"] ?>">
			<? if ($item["in_production"]) { ?>
				<a href="<?= site_url("/program/program_detail/" . $this->convert->AsciiToHex($program_ext[$item["product_dtl_id"]]["program_code"]) . "/" . $this->convert->AsciiToHex($item['product_dtl_id']) . "/0") ?>" class="link"><?= number_format($item["in_production"], 0)?></a>
			<? } else { ?>
				<?= number_format($item["in_production"], 0)?>
			<? } ?>
			</td>
			<td id="backlog<?= $item["product_dtl_id"] ?>" rel="<?= $item["backlog"] ?>" rev="<?= $item["est_weight"] ?>">
				<span><?= check_negative($total_kg_need)?></span>
				<input type="hidden" id="total_kg_need_<?= $item["product_dtl_id"] ?>" name="total_kg_need_<?= $item["product_dtl_id"] ?>" value="<?= $total_kg_need ?>" />
			</td>
			<td rel="<?= $item["perct_of_films"] ?>" nowrap="nowrap" valign="middle">
				<input type="text" size="5" id="film<?= $item["product_dtl_id"] ?>" rev="<?= $item["product_dtl_id"] ?>" rel="<?= $item["truck_size"] ?>" class="film_p" name="film_p" value="<?= number_format($item["perct_of_films"], 1) ?>" />
				<a style="cursor: pointer;" onclick="onSlitMenuClick('<?php echo $item["product_dtl_id"] ?>')"><?php echo image_asset("menu-th.png"); ?></a>
			</td>
			<td id="coilneed<?= $item["product_dtl_id"] ?>"><?= check_negative($total_coil_need) ?></td>
		</tr>
	<? $i++;} ?>
	</tbody>
</table>
<?= form_close() ?>
<div id="slit_dialog" title="Slit Spec" style="display: none;"></div>
<div id="so_dialog" title="SO" style="display:none;"></div>
<div id="p_dialog" title="" style="display: none;">Saving... Please wait.</div>
<br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button();
	$(".stock").keyup(onStockKeyUp);
	$(".stock:first").focus().select();
	$(".stock").focus(onStockFocus);
	$(".film_p").keyup(onFilmKeyUP);
	$(".film_p").focus(onStockFocus);
	$("input").keypress(onChangeFocus);
	$("#mainButton").click(onMainButtonClick);
	$(".numeric").set_format({precision: 0,autofix:true,allow_negative:false});
	$("#print").click(onPrintClick);
	//$(".datarow").click(onDataRowClick);
	$("#production_report").click(onProductionReportClick);
	$("#overal_stock_report").click(onOveralStockReportClick);
	$("#backButton").click(onBackButton);
	$("#saveButton").click(onSaveButton);
	$(".product_name").click(onProductClicked);
});
function onProductClicked() {
	var product_id = $(this).attr("rel");
	
	if (product_id)
	{
		$("#so_dialog").html("Please wait.").dialog({
			modal: true,
			width : 150
		});
		
		var param = {
			"product_id" : product_id
		};
	
		$.post("<?php echo site_url("report/so_page") ?>", param, function(data) {
			$("#so_dialog").dialog("close");
		
			$("#so_dialog").html(data).dialog({
				modal: true,
				width : 1000,
				open: function( event, ui ) {
					$(".datepicker").datepicker({
						dateFormat : "dd/mm/yy"
					});
				},
				buttons : {
					"บันทึก" : function() {
						
						$("#p_dialog").dialog({
							modal: true
						});
						
						var param = $("#so_form").serialize();
						
						$(this).dialog("close");
						
						// Saving...
						$.post("<?php echo site_url("report/save_so"); ?>", param, function(data){
							$("#p_dialog").html("Save Completed");
							
							window.setTimeout(function() {
								$("#p_dialog").dialog("close");
							}, 500);
							
						}, "json");
						
					},
					"ปิด" : function() {
						$(this).dialog("close");
					}
				}
			});
		});
	}
}
function onSaveButton() {
	$("#report_form").submit();
}
function onBackButton() {
	document.location.href="<?= site_url("/report") ?>";
}
function onProductionReportClick() {
	//$("#my-content-table").width($("#overal_stock_table").width());
	$("#overal_stock_table").hide();
	$("#my-content-table").show();
}
function onOveralStockReportClick() {
	$("#overal_stock_table").width($("#my-content-table").width());
	$("#my-content-table").hide();
	$("#overal_stock_table").show();
}
function onDataRowClick() {
	var child = $(this).children(":eq(7)").children(":input");
	child.select();
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
function onStockFocus() {
	$('html, body').animate({scrollTop: $(this).offset().top - 400}, 100);
}
function onFilmKeyUP() {
	var my_id = $(this).attr("rev");
	
	if (!$(this).val()) {
		$(this).val(0);
		$(this).select();
	}
	
	var index = $(".film_p").index($(this));
	//alert(index);
	
	var size = parseFloat($(this).attr("rel"));
	var unit = parseFloat($(".stock:eq(" + index + ")").val());
	
	var stockunit  = 0;
	
	if (isNaN(size) || isNaN(unit)) {
	} else {
		stockunit  = size * unit;
		$("#" + my_id).text(stockunit).format({format: "#,##0"});
	}
	calculate(my_id, stockunit);
}

function onStockKeyUp(event) {
	var my_id = $(this).attr("rev");
	
	if (!$(this).val()) {
		$(this).val(0);
		$(this).select();
	}
	
	var size = parseFloat($(this).attr("rel"));
	var unit = parseFloat($(this).val());
	//alert(size);
	//alert(unit);
	var stockunit  = 0;
	
	if (isNaN(size) || isNaN(unit)) {
	} else {
		stockunit  = size * unit;
		$("#" + my_id).text(stockunit).format({format: "#,##0"});
	}
	
	calculate(my_id, stockunit);
}
function calculate(my_id, stockunit) {
	//alert(my_id + " " + stockunit);
	//Total KG Need
	var backlog = parseFloat($("#backlog" + my_id).attr("rel"));
	var est_weight = parseFloat($("#backlog" + my_id).attr("rev"));
	var in_production = parseFloat($("#inproduction" + my_id).attr("rel"));
	if (isNaN(backlog)) {
		backlog = 0;
	}
	if (isNaN(est_weight)) {
		est_weight = 0;
	}
	if (isNaN(in_production)) {
		in_production = 0;
	}
	//alert("backlog" + backlog + " , est_weight = " + est_weight + " , in_production = " + in_production);
	
	var total_kg_need = -1*(((-backlog + stockunit) * est_weight) - in_production);
	$("#total_kg_need_" + my_id).val(total_kg_need);
	
	if (total_kg_need < 0)
	{
		total_kg_need_temp = Math.abs(total_kg_need);
		var span = $("#backlog" + my_id).children("span");
		if (span.hasClass("negative"))
		{
			span.text(total_kg_need_temp).format({format: "#,##0"});
			span.text("(" + span.text() + ")");
		}
		else
		{
			span.addClass("negative").text(total_kg_need_temp).format({format: "#,##0"});
			span.text("(" + span.text() + ")");
		}
	}
	else
	{
		$("#backlog" + my_id).children("span").removeClass("negative").text(total_kg_need).format({format: "#,##0"});
	}
	
	var film_percent = $("#film" + my_id).val();
	//alert(film_percent);
	var total_coil_need = 0;
	if (film_percent != 0) {
		total_coil_need = total_kg_need / 0.99 / (film_percent / 100);
	}
	
	if (total_coil_need < 0)
	{
		total_coil_need = Math.abs(total_coil_need);
		$("#coilneed" + my_id).addClass("negative").text(total_coil_need).format({format: "#,##0"});
		$("#coilneed" + my_id).text("(" + $("#coilneed" + my_id).text() + ")");
	}
	else
	{
		$("#coilneed" + my_id).removeClass("negative").text(total_coil_need).format({format: "#,##0"});
	}
	
}
function onPrintClick() {
	window.print();
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function onSlitMenuClick(product_id)  {
	$.get("<?php echo site_url("report/slit_page") ?>/" + product_id, function(data) {
		// $("#slit_dialog").html(data);
		$("#slit_dialog").html(data).dialog({
			modal: true,
			width : 550,
			buttons : {
				"ตกลง" : function() {
				
					var obj = $("#slit_dialog").find("input:checked");
					
					$("#film" + product_id).val(obj.val());
					$("#film" + product_id).keyup();
					$(this).dialog("close");
				},
				"ปิด" : function() {
					$(this).dialog("close");
				}
			}
		});
	});
}
function onAddMore(obj) {
	var o = $(obj);
	
	var newItem = o.parent().parent().clone(true);
	var sono = o.parent().parent().attr("rel");
	
	var obj = $("tr[lt=" + sono + "]");
	var index = 1;
	if (obj.size() > 0)
	{
		var maxRanking = 0;
		$.each(obj, function(i) {
			var ii = parseInt($(this).attr("data-index"));
			if (ii > maxRanking) {
				maxRanking = ii;
			}
		});
		
		index = maxRanking + 1;
	}
	else
	{
		index = 1;
	}
	
	newItem.insertAfter(o.parent().parent());
	$(".datepicker").datepicker( "destroy" );
	$(".datepicker").removeClass("hasDatepicker").removeAttr('id');
	newItem.find(".priority").val(0);
	newItem.find(".item").val(0);
	newItem.find(".ranking").val(index);
	newItem.attr("data-index", index);
	$(".datepicker").datepicker({
		dateFormat : "dd/mm/yy"
	});
}
</script>