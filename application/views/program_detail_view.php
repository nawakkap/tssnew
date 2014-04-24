<style type="text/css" rel="stylesheet">
#statuschange, #productchange, #external_program , #machine_change{font-size:10pt;font-family:Tahoma;margin:0;padding:2px;line-height:20px; width: 70px; }
input.editButton, input.okButton {font-size:10pt;font-family:Tahoma;margin:0;padding:2px;line-height:20px; width: 70px; }
</style>
<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>&nbsp;รายละเอียดโปรแกรม &nbsp;<?= $external_program_code ?>&nbsp;(&nbsp;<?= $program_code ?>&nbsp;)</td>
		<td align="right"><input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ"/></td>
	</tr>
</table><br/>
<table border="1" width="100%" class="table-data ui-widget">
	<tr>
		<td class="ui-state-highlight" width="25%">&nbsp;ชื่อสินค้า</td>
		<td colspan="3"><?= $product_name ?>&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td class="ui-state-highlight" width="25%">&nbsp;เลขโปรแกรมภายใน</td>
		<td width="25%"><?= $program_code ?></td>
		<td class="ui-state-highlight" width="25%">&nbsp;จำนวนเส้นทั้งหมด</td>
		<td width="25%" id="total_unit">&nbsp;</td>
	</tr>
	<tr>
		<td class="ui-state-highlight">&nbsp;ผู้ผลิต/เครื่อง</td>
		<td><?= $machine_by ?></td>
		<td class="ui-state-highlight">&nbsp;สถานะ</td>
		<td>
			<?= form_open("/program/update_program_status", array("id" => "update_status_form")) ?>
			<input type="hidden" name="program_code" value="<?= $program_code ?>" />
			<input type="hidden" name="product_dtl_id" value="<?= $product_dtl_id ?>" />
			<?
				$data = array(
					"1" => "ปกติ",
					"2" => "เสร็จสิ้น",
					"3" => "ยกเลิก"
				);
			?><?= form_dropdown("program_status", $data, $program_status); ?>&nbsp;
			<input id="statuschange" type="submit" name="Tsubmit" class="button" value="เปลี่ยน" />
			<?= form_close() ?>
		</td>
	</tr>
	<tr>
		<td class="ui-state-highlight">&nbsp;น้ำหนักรวมทั้งหมด</td>
		<td><span id="total_weight">0</span>&nbsp;กิโลกรัม</td>
		<td class="ui-state-highlight">&nbsp;จำนวนแถบทั้งหมด</td>
		<td id="total_film"></td>
	</tr>
	<tr>
		<td class="ui-state-highlight">&nbsp;ค่าประมาณน้ำหนัก Film คงเหลือ</td>
		<td><b id="film_remaining">0</b>&nbsp;กิโลกรัม</td>
		<td class="ui-state-highlight">&nbsp;หมายเลขโปรแกรมภายนอก</td>
		<td>
			<?= form_open("/program/update_program_ext", array("id" => "update_form_ext_form")) ?>
			<input type="hidden" name="program_code" value="<?= $program_code ?>" />
			<input type="hidden" name="product_dtl_id" value="<?= $product_dtl_id ?>" />
			<input type="text" name="external_program_code" value="<?= $external_program_code ?>" style="width: 100px;" <?= ($history === "1") ? 'readonly="readonly"' : "" ?> />
			<input type="submit" id="external_program" name="TSUBMIT" class="button" value="เปลี่ยน" <?= ($history === "1") ? 'disabled="disabled"' : "" ?> />
			<?= form_close() ?>
		</td>
	</tr>
</table><br/>
<table border="1" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td colspan="4">&nbsp;สรุป</td>
	</tr>
	<tr>
		<td class="ui-state-highlight" width="25%">&nbsp;น้ำหนักเฉลี่ย</td>
		<td width="25%"><?= ($avg_weight < 0) ? 0 : number_format($avg_weight, 3) ?>&nbsp;กิโลกรัม</td>
		<td class="ui-state-highlight" width="25%">&nbsp;% ของเกรด B</td>
		<td width="25%"><?= number_format($grade_b, 2)."(".number_format($grade_b_cfu, 2).")" ?>&nbsp;%</td>
	</tr>
	<tr>
		<td class="ui-state-highlight" width="25%">&nbsp;ราคาต้นทุนเฉลี่ย</td>
		<td width="25%"><?= number_format($cost, 3) ?>&nbsp;(<?= ($avg_weight == 0) ? 0 :number_format($cost/$avg_weight, 3) ?>) [<?= $diff ?>]บาท</td>
		<td class="ui-state-highlight" width="25%">&nbsp;ราคาต้นทุนเฉลี่ย + ค่าแรง</td>
		<td width="25%"><?= number_format($cost_and_wage, 3) ?>&nbsp;(<?= ($avg_weight == 0) ? 0 :number_format($cost_and_wage/$avg_weight, 3) ?>) &nbsp;/<?= number_format($cost_and_wage/0.96, 3) ?> บาท</td>
	</tr>
</table><br/>
<?= form_open("/program/change_machine", array("id"=>"change_machine_form")) ?>
<input type="hidden" name="program_code" value="<?= $program_code ?>" />
<input type="hidden" name="product_dtl_id" value="<?= $product_dtl_id ?>" />
<input type="hidden" name="history" value="<?= $history ?>" />
<table border="1" width="100%" class="table-data ui-widget">
	<tr>
		<td class="ui-widget-header" width="50%">&nbsp;เครื่อง</td>
		<td>&nbsp;&nbsp;
			<?= form_dropdown("machine", $machine, $machine_default ,  ($history === "1") ? 'disabled="disabled"' : "") ?>
			<input type="submit" id="machine_change" name="TSUBMIT" value="เปลี่ยน" class="button" <?= ($history === "1") ? 'disabled="disabled"' : "" ?>  />
		</td>
	</tr>
</table>
<?= form_close() ?>
<br/>
<div id="tabs" style="display:block; width:100%">
	<ul>
		<li><a href="#tabs-1">รายการ Lot ที่ใช้</a></li>
		<li><a href="#tabs-2">รายละเอียดการผลิต</a></li>
	</ul>
	<div id="tabs-1">
	<table border="1" width="100%" class="table-data ui-widget">
		<thead>
			<tr class="ui-widget-header">
				<th align="center">Lot Id</th>
				<th align="center">ความหนา</th>
				<th align="center">ราคาวัตถุดิบ</th>
				<th align="center">Base Price</th>
				<th align="center">Vat</th>
				<th align="center">ค่าแรง</th>
				<th align="center">จำนวน</th>
				<th align="center">ราคาทุน / kg</th>
				<th align="center">น้ำหนัก  Film </th>
				<th align="center">จำนวนเส้นประมาณ</th>
				<th align="center">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<? 
			$total_weight = 0;
			$total_film = 0;
			
			for($i = 0; $i < count($film_result); $i++) {

				$cost_price = $film_result[$i]['net_price'];
				
				$total_weight += round($film_result[$i]['weight'], 2);
				$total_film += $film_result[$i]['unit'];
		?>
			<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
				<td><a href="<?= site_url("/group/group_detail/" . $this->convert->AsciiToHex($film_result[$i]['coil_group_code'])) ?>" class="link"><?= $film_result[$i]['coil_group_code'] ?></a></td>
				<td><?= number_format($film_result[$i]['thickness'], 2) ?></td>
				<td><?= number_format($film_result[$i]['price'], 2) ?></td>
				<td><?= number_format($film_result[$i]['base_price'], 2) ?></td>
				<td><?= ($film_result[$i]['vat_status']) ? "Yes" : "No" ?></td>
				<td><?= number_format($film_result[$i]['wage'], 2) ?></td>
				<td><a class="link" href="<?php echo site_url("film/item_detail/" . $this->convert->AsciiToHex($film_result[$i]['coil_group_code']) . "/" . $this->convert->AsciiToHex($film_result[$i]['product_dtl_id'])); ?>"><?= number_format($film_result[$i]['unit'], 0) ?></a></td>
				<td><?= number_format($cost_price, 3) ?></td>
				<td><?= number_format($film_result[$i]['weight'], 0) ?></td>
				<td><?= number_format($film_result[$i]['weight']/$totalfilmweight*($total_unit_2-$total_BCD_2), 0) ?></td>
				<td><input type="button" name="editButton" class="button editButton" rel="<?= $film_result[$i]['coil_group_code'] ?>" product_id="<?= $film_result[$i]['product_dtl_id'] ?>" vat_status="<?= $film_result[$i]['vat_status'] ?>" wage="<?= $film_result[$i]['wage'] ?>" value="แก้ไข" <?= ($history === "1") ? 'disabled="disabled"' : "" ?>  /></td>
			</tr>
		<? } ?>
		</tbody>
	</table>
	</div>
	<div id="tabs-2">
		<?= form_open("/program/add_method" , array("id" => "program_form")) ?>
		<input type="hidden" name="program_code" value="<?= $program_code ?>"/>
		<input type="hidden" name="program_code_ext" value="<?= $external_program_code ?>"/>
		<input type="hidden" name="product_dtl_id" value="<?= $product_dtl_id ?>"/>
		<table border="0" width="70%" align="center">
			<tr>
				<td align="center"><input type="submit" name="saveButton" class="button saveButton" value="บันทึก" style="width: 100%" <?= ($history) ? 'disabled="disalbed"' : "" ?> /></td>
			</tr>
		</table>
		<!--
		<table border="1" width="70%" align="center" class="table-data ui-widget">
			<tr>
				<td class="ui-widget-header">ผู้ผลิต/เครื่องที่ใช้ผลิต</td>
				<td><input type="text" id="machine_by" name="machine_by" value="<?= $machine_by ?>" style="width: 100%" /></td>
			</tr>
		</table><br/>
		-->
		<table id="program_table" border="1" width="85%" class="table-data ui-widget" align="center">
			<thead>
				<tr class="ui-widget-header">
					<th align="center" width="22.5%">วันที่<input type="hidden" class="rowCount" name="rowCount" value=""/></th>
					<th align="center" width="22.5%">จำนวนเส้น</th>
					<th align="center" width="22.5%">เกรด</th>
					<th align="center" width="22.5%">เครื่อง</th>
					<th width="10%">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?
				$total_unit = 0;
				$total_BCD = 0;
				$total_A2 = 0;
				$dropdown = array("A" => "A", "B" => "B" , "C" => "C", "D" => "D","A*" => "A*");
				$dropdown_style = 'style="width: 100px;"';
				foreach($program_code_result as $item) { 
					if ($item["total_unit"] < 0) continue;
				
					$total_unit += $item['total_unit'];
					if($item["product_grade"]!="A"&&$item["product_grade"]!="A*") $total_BCD += $item['total_unit'];
					if($item["product_grade"]=="A*") $total_A2 += $item['total_unit'];
			?>
				<tr>
					<td align="center"><input type="text" name="processing_date" class="processing_date" value="<?= mysqldatetime_to_date($item['processing_date'], 'd/m/Y') ?>" readonly="readonly" /></td>
					<td align="center"><input type="text" name="total_unit" class="numeric" value="<?= $item['total_unit'] ?>" /></td>
					<td align="center"><?= form_dropdown("grade", $dropdown, $item['product_grade'], $dropdown_style) ?></td>
					<td align="center"><?= form_dropdown("machine", $machine, $item["mc_id"]) ?></td>
					<td><button class="button deleteButton">ลบ</button></td>
				</tr>
			<? } ?>
				<tr>
					<td align="center"><input type="text" name="processing_date" class="processing_date" value="" readonly="readonly" /></td>
					<td align="center"><input type="text" name="total_unit" class="numeric" value="0" /></td>
					<td align="center"><?= form_dropdown("grade", $dropdown, "",  $dropdown_style) ?></td>
					<td align="center"><?= form_dropdown("machine", $machine, $machine_default) ?></td>
					<td><button class="button deleteButton">ลบ</button></td>
				</tr>
			</tbody>
		</table>
		<table border="0" width="70%" align="center">
			<tr>
				<td align="center"><input type="submit" name="saveButton" class="button saveButton" value="บันทึก" style="width: 100%" <?= ($history) ? 'disabled="disalbed"' : "" ?> /></td>
			</tr>
		</table>
		<?= form_close() ?>
	</div>
</div>
<br/>
<div id="pleasewait" title="โปรดรอสักครู่" style="display:none;">กรุณารอสักครู่</div>
<div id="warning_panel" title="Information" style="display:none;">กรุณา</div>
<?= form_open("/program/update_lot", array("id" => "update_lot_form")) ?>
<input type="hidden" name="coil_group_code" class="lot_no_text" value="" />
<input type="hidden" name="program_code" value="<?= $program_code ?>" />
<input type="hidden" name="product_dtl_id" value="<?= $product_dtl_id ?>" />
<input type="hidden" name="history" value="<?= $history ?>" />
<input type="hidden" id="vat_status" name="vat_status" value="" />
<input type="hidden" id="wage" name="wage" value="" />
<?= form_close() ?>
<div id="editPanel" title="แก้ไข" style="display:none;">
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr>
		<td class="ui-state-highlight" width="50%">Lot no</td>
		<td class="lot_no"></td>
	</tr>
	<tr>
		<td class="ui-state-highlight" width="50%">Vat</td>
		<td><input type="checkbox" id="vat_status_show" name="vat_status_show" value="VAT_NORMAL" /></td>
	</tr>
	<tr>
		<td class="ui-state-highlight" width="50%">ค่าแรง</td>
		<td><input type="text" id="wage_show" name="wage_show" class="decimal" value="" size="7" /></td>
	</tr>
</table>
</div>
<div id="changeProductPanel" title="กรุณาเลือกสินค้าและใส่เลขโปรแกรมภายในที่ต้องการ" style="display:none;">
<?= form_open("program/change_product_and_program_code", array("id" => "changeProductForm")) ?>
<input type="hidden" name="old_program_code" value="<?= $program_code ?>" />
<input type="hidden" name="old_product_dtl_id" value="<?= $product_dtl_id ?>" />
<input type="hidden" name="old_history" value="<?= $history ?>" />
<input type="hidden" name="program_ext_no" value="<?= $external_program_code ?>" />
<table border="0" class="ui-widget">
	<tr>	
		<td>สินค้า</td>
		<td>
			<select id="change_product_dtl_id" name="product_dtl_id">
			<? foreach($product_all as $item) { ?>
				<option value="<?= $item->product_dtl_id ?>"><?= $item->product_name_th ?></option>
			<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td nowrap="nowrap">เลขโปรแกรมภายใน</td>
		<td><input type="text" id="change_program_code" name="program_code" value="" /></td>
	</tr>
</table>
<?= form_close() ?>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#backButton").click(onBackButtonClick);
	$("#tabs").tabs();
	$(".numeric").set_format({precision: 0,autofix:true,allow_negative:false});
	$(".decimal").set_format({precision: 1,autofix:true,allow_negative:false});
	$("#program_table").tableAutoAddRow({autoAddRow: true, displayRowCountTo : "rowCount", inputBoxAutoNumber: true}, onAddRow);
	$(".deleteButton").btnDelRow();
	$("#total_unit").text("<?= number_format($total_unit_2-$total_BCD_2-$total_A2_2, 0)."(".number_format($total_BCD_2, 0).")"."(".number_format($total_A2_2, 0).")*=".number_format($total_unit_2, 0) ?>");
	$("#total_weight").text("<?= number_format($total_weight, 0)."(".number_format($totalfilmweight, 0).")" ?>");
	$("#total_film").text("<?= number_format($total_film, 0)."(".number_format($totalfilmunit, 0).")" ?>");
	$("#film_remaining").text("<?= number_format($total_weight - ($total_unit * $est_weight), 0) ?>");
	$("#update_status_form").submit(updatestatus);
	$(".processing_date").datepicker({dateFormat:'dd/mm/yy'});
	$(".editButton").click(onEditButtonClick);
	$(".saveButton").click(onSaveButtonClick);
});
function onBackButtonClick(){
	//document.location.replace("<?= site_url("/program/") ?>");
	history.back();
}
function onSaveButtonClick() {
	$("#pleasewait").dialog({
		modal: true,
		closeOnEscape: false,
		open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
	});
}
function onEditButtonClick() {
	var coil_group_code = $(this).attr("rel");
	$(".lot_no").text(coil_group_code);
	$(".lot_no_text").val(coil_group_code);

	var vat_status = $(this).attr("vat_status");
	var wage = $(this).attr("wage");
	
	if (vat_status == "1")
	{
		$("#vat_status_show").attr("checked", "checked");
		$("#vat_status").val("VAT_NORMAL");
	}
	else
	{
		$("#vat_status_show").removeAttr("checked");
		$("#vat_status").val("VAT_PREMIUM");
	}
	
	$("#wage_show, #wage").val(wage);
	
	$("#editPanel").dialog({
		modal: true,
		close: function() {
			$(".editButton").removeClass("ui-state-focus");
		},
		open: function() {
			$("#wage_show").select();
		},
		buttons : {
			"ปิด" : function() {
				$(this).dialog("close");
			},
			"ตกลง" : function() {
				
				if($("#vat_status_show").attr("checked"))
				{
					$("#vat_status").val("VAT_NORMAL");
				}
				else
				{
					$("#vat_status").val("VAT_PREMIUM");
				}
				
				$("#wage").val($("#wage_show").val());
				$("#update_lot_form").submit();
			}
		}
	});
}
function onAddRow(row) {
	$(".numeric").set_format({precision: 0,autofix:true,allow_negative:false});
	$(".processing_date").removeClass("hasDatepicker").datepicker({dateFormat:'dd/mm/yy'});
}
function updatestatus() {
	$("#pleasewait").dialog({
		modal: true,
		closeOnEscape: false,
		open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
	});
}
function showChangeProductDialog() {
	$("#changeProductPanel").dialog({
		modal: true,
		width: 550,
		buttons : {
			'ปิด' : function() {
				$(this).dialog("close");
			},
			'เปลี่ยน' : function() {
				/*
				if (!$("#change_product_dtl_id").val()) {
					$("#warning_panel").html("กรุณาเลือกสินค้าให้เรียบร้อย");
					$("#warning_panel").dialog({
						modal: true,
						buttons : {
							'ปิด' : function() {
								$(this).dialog("close");
							}
						}
					});
					
					return;
				}
			
				if (!$("#change_program_code").val()) {
					$("#warning_panel").html("กรุณาใส่เลขโปรแกรมภายในให้เรียบร้อย");
					$("#warning_panel").dialog({
						modal: true,
						close: function() {
							$("#change_program_code").focus().select();
						},
						buttons : {
							'ปิด' : function() {
								$(this).dialog("close");
							}
						}
					});
					
					return;
				}
				
				*/
				
				$("#changeProductForm").submit();
				
			}
		},
		resizable: false
	});
}
</script>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        