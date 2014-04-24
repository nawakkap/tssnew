<?= form_open("/film") ?>
<input type="hidden" name="theDate" value="<?= $theDate ?>"/>
<table border="0" width="100%" class="ui-widget">
	<tr>
		<td align="left">
			<input type="button" name="mainButton" class="button mainButton" value="ย้อนกลับหน้าแรก"/>&nbsp;&nbsp;
			<input type="button" name="historyButton" class="button historyButton" value="ข้อมูลเก่า" />
		</td>
		<td align="right">
			<?
				$dropdown = array("coil_group_code" => "Lot No",
											 "external_program_code" => "Ext. Program Code",
											 "thickness" => "ความหนา",
											 "product_name" => "ชื่อสินค้า"
											);
				echo form_dropdown("searchType", $dropdown, $searchType);
			?>
			<input type="text" name="searchText" value="<?= $searchText ?>" />&nbsp;<input type="submit" name="searchButton" class="button" value="ค้นหา" />
		</td>
	</tr>
</table>
<?= form_close() ?>
<br/>
<?= form_open("/film/", array("id"=>"viewForm")) ?>
<input type="hidden" id="otherDate" name="theDate" value="<?= $theDate ?>"/>
<?= form_close() ?>
<?= form_open("/film/update_quantity", array("id" => "quantityForm")) ?>
<table border="0" width="100%" class="ui-widget">
	<tr>
		<td>ข้อมูล ณ วันที่ : <input type="text" id="theDate" name="theDate" value="<?= $theDate ?>" class="button" />&nbsp;&nbsp;</td>
		<td align="right"><input type="button" name="saveButton" value="บันทึก" class="button" onclick="onSaveClick()" /></td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="coil_group_code">Lot No</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="slit_date">Slit Date</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="external_program_code">Ext. Code</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="thickness">ความหนา</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="width">ความกว้าง</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="unit"># แถบ</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="total_quantity"># ที่ผลิต</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="remaining_quantity"># ที่เหลือ</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="weight">น้ำหนัก</a></th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="product_dtl_id">Product</a></th>
			<th>&nbsp;</th>
			<th align="center" nowrap="nowrap"><a href="#" class="sort" rel="mc_id">เครื่อง</th>
		</tr>
	</thead>
	<tbody>
		<?
			$coil_lot_no = "";
			$index = 0;
			foreach($film_result as $film_item) {
				foreach($film_item as $item) {

				$diff = FALSE;
				if (empty($coil_lot_no) || $coil_lot_no !== $item['coil_group_code']) {
					$coil_lot_no = $item['coil_group_code'];
					$diff = TRUE;
					
					if ($index === 0) {
						$diff = FALSE;
					}
				}
				
				$has_ironing = FALSE;
				$ironing_machine = $item["ironing_machine"];
				
				// echo $item['product_dtl_id'];
				$suffix = ($this->convert->AsciiToHex($item['coil_group_code'] . "#" . $item['product_dtl_id'] . "#" . $item["q_index"]));
			
				if ($ironing_machine)
				{
					$has_ironing = TRUE;
				}
				
		?>
		<? if ($diff === TRUE) { ?>
		<tr class="even">
			<td colspan="12">&nbsp;</td>
		</tr>
		<? } ?>
		<?
		// Check quantity for disable text input
		$disabled = "";
		if (($item['unit'] <= $item['total_quantity']) AND ($item["quantity"] == 0))
		{
			$disabled= 'disabled="disabled"';
		}
		?>
		<tr class="<?= ($disabled) ? "limit" : "odd" ?>">
			<td><a href="<?= site_url("/group/group_detail/" . $this->convert->AsciiToHex($item['coil_group_code'])) ?>" class="link"><?= $item['coil_group_code']  ?></a></td>
			<td><?= mysqldatetime_to_date($item['slit_date'], 'd/m/Y') ?></td>
			<td><a href="<?= site_url("/program/program_detail/" . $this->convert->AsciiToHex($item['program_code']) . "/" . $this->convert->AsciiToHex($item['product_dtl_id'])) ?>" class="link"><?= $item['external_program_code'] ?></a></td>
			<td><?= number_format($item['thickness'], 2) ?></td>
			<td><?= number_format($item['width'], 2) ?></td>
			<td><?= number_format($item['unit'], 0) ?></td>
			<td><?= number_format($item['total_quantity'], 0)  ?></td>
			<td><?= number_format($item['remaining_quantity'], 0)  ?></td>
			<td><?= number_format($item['weight'], 2)  ?></td>
			<td><?= $item["q_index"] ?></td>
			<td nowrap="nowrap"><?= (isset($product_result[$item['product_dtl_id']])) ? $product_result[$item['product_dtl_id']] : "&nbsp;" ?></td>
			<td align="center">

				<input type="hidden" name="temp_code[]" value="<?= $suffix ?>" />
				<input type="text" name="quantity_<?= $suffix ?>" class="<?= ($disabled) ? "" : "quantity" ?> numeric" value="<?= ($item["quantity"]) ? $item["quantity"] : 0 ?>" size="5" max_unit="<?= number_format($item["unit"]) ?>" rel="<?= number_format($item['total_quantity'], 0) ?>" today_unit="<?= ($item["quantity"]) ? $item["quantity"] : 0 ?>" <?= $disabled ?> />
			</td>
			<td nowrap="nowrap">
			<? if ($has_ironing) { ?>
				<!-- Has Ironing -->
				<?= (isset($machine_special[$ironing_machine])) ? $machine_special[$ironing_machine] . "<br/>(เครื่องรีด)" : "เกิดข้อผิดพลาด" ?>
				<input type="hidden" name="machine_<?= $suffix ?>" value="<?= $ironing_machine ?>" />
			<? } else { ?>
				<?= isset($machine[$item["mc_id"]]) ? $machine[$item["mc_id"]] : "&nbsp;" ?>
				<input type="hidden" name="machine_<?= $suffix ?>" value="<?= $item["mc_id"] ?>" />
			<? } ?>
			</td>
		</tr>
		<? 			
					$index++;
				}
			} ?>
	</tbody>
</table>
<?= form_close() ?>
<br/>
<? if (count($film_result) == 0) { ?>
<table border="0" width="100%" class="table-data table-ui-widget">
	<tr height="40" class="ui-state-active">
		<td align="center">ไม่มีข้อมูลในส่วนนี้</td>
	</tr>
</table>
<? } ?>
<table border="0" width="100%">
	<tr>
		<td align="left"><input type="button" id="mainButton" name="mainButton" class="button mainButton" value="ย้อนกลับหน้าแรก"/></td>
		<td align="right"><input type="button" name="saveButton" value="บันทึก" class="button" onclick="onSaveClick()" /></td>
	</tr>
</table>
<?= form_open("/film", array("id" => "sort_form")) ?>
<input type="hidden" name="searchType" value="<?= $searchType ?>" />
<input type="hidden" name="searchText" value="<?= $searchText ?>" />
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<input type="hidden" name="theDate" value="<?= $theDate ?>"/>
<?= form_close() ?>
<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>
<div id="warning" title="warning" style="display: none;">กรอกค่าเกินกว่าจำนวนแถบที่เหลือ กรุณากรอกใหม่อีกครั้ง</div>
<br/><br/><br/>
<style type="text/css">
.limit { background-color: #D3D3D3;text-align:center; }
</style>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#theDate").datepicker({
		dateFormat : 'yy-mm-dd',
		altField : "#otherDate",
		altFormat : 'yy-mm-dd',
		onSelect : function() {
			$("#info").dialog({
				modal: true
			});
			$("#viewForm").submit();
		}
	});
	$(".mainButton").click(onMainButtonClick);
	$(".historyButton").click(onHistoryButtonClick);
	$(".minus").toggle(onMinusClick, onPlusClick).css("cursor", "pointer");
	$(".sort").click(onSortClick);
	
	$(".quantity").keypress(onChangeFocus);
	$(".quantity").keyup(checkQuantity);
	$(".machine").keypress(onChangeFocus);
	$(".machine").change(populateMachine);
	$(".machine").blur(populateMachine);
	$(".quantity:first").focus().select();
	$(".numeric").set_format({precision: 0,autofix:true,allow_negative:false});
});

function populateMachine() {
	var program_code = $(this).attr("rel");
	var sel_value = $(this).val();
	
	$(".machine[rel=" + program_code + "]").val(sel_value);
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
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function onMinusClick() {
	var ref = $(this).attr("rel");
	$(ref).hide("blind");
}
function onPlusClick() {
	var ref = $(this).attr("rel");
	$(ref).show("blind");
}
function onHistoryButtonClick() {
	document.location.replace("<?= site_url("/film/history") ?>");
}
function checkQuantity() {
	var index = $(".quantity").index($(this));
	// index--;
	if (index < 0) {
		index = $(".quantity").size() - 1;
	}
	var obj = $(".quantity:eq(" + (index) + ")");
	var total = obj.attr("rel");
	var max = obj.attr("max_unit");
	var val = obj.val();
	var today = obj.attr("today_unit");
	
	if (!val) {
		val = 0;
	} else {
		val = parseInt(val);
	}
	
	total = parseInt(total);
	max = parseInt(max);
	today = parseInt(today);
	
	if ((total - today + val)> max) {
		$("#warning").dialog({
			modal: true,
			buttons : {
				'ปิด' : function() {
					$(this).dialog("close");
				}
			},
			open : function() {
				$('.ui-dialog-buttonpane > button:last').focus();
			},
			close : function() {
				obj.focus().select();
			},
			resizable : false
		});
		obj.focus().select();
		obj.scrollTop(obj.position.top);
	}
}
function onChangeFocus(event) {
	if (event.keyCode == 13) {
		if ($(this).hasClass("quantity")) {
			$(this).parent().next().children(0).focus();
		} else if($(this).hasClass("machine")) {
		
			var program_code = $(this).attr("rel");
			var sel_value = $(this).val();
			
			$(".machine[rel=" + program_code + "]").val(sel_value);
		
			var index = $(".machine").index($(this));
			if (index == ($(".machine").size() - 1)) {
				$(".quantity:first").focus().select();
			} else {
				$(".quantity:eq(" + (index + 1) + ")").focus().select();
			}
		}
	}
}
function onSaveClick() {
	var isError = false;

	$.each($(".quantity"), function(index) {
		if (!$(this).val()) {
			$(this).val("0");
		}
			
		var total = $(this).attr("rel");
		var max = $(this).attr("max_unit");
		var val = $(this).val();
		var today = $(this).attr("today_unit");

		val = parseInt(val);
		total = parseInt(total);
		max = parseInt(max);
		today = parseInt(today);
		
		var obj = $(this);
		
		if ((total - today + val) > max) {
			$("#warning").dialog({
				modal: true,
				buttons : {
					'ปิด' : function() {
						$(this).dialog("close");
					}
				},
				open : function() {
					$('.ui-dialog-buttonpane > button:last').focus();
				},
				close : function() {
					obj.focus().select();
				},
				resizable : false
			});	
			
			isError = true;
		}
	});
	
	if (!isError) {
		$("#quantityForm").submit();
	}
}
</script>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                   