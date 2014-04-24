<style type="text/css" rel="stylesheet">
#chgMachine {font-size:10pt;font-family:Tahoma;margin:0;padding:2px;line-height:20px; width: 70px; }
#historyButton { font-size: 10pt; font-family:Tahoma;margin:0;padding:2px;line-height:20px; width: 150px;}
</style>
<table border="0" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td align="left">&nbsp;&nbsp;Lot Id : <?= $group_code ?></td>
		<td align="right">
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />
		</td>
	</tr>
</table>
<br/>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr>
		<td colspan="4" class="ui-widget-header">รายละเอียดของ Lot</td>
	</tr>
	<tr>
		<th class="ui-state-highlight"  width="25%">Lot Id</th>
		<td width="25%"><?= $group_code ?></td>
		<th class="ui-state-highlight" width="25%">น้ำหนักรวม</th>
		<td width="25%"><b id="total_weight">0</b>&nbsp;กิโลกรัม</td>
	</tr>
</table>
<br/>
<table border="1" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>รายการสินค้า</td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="2" class="table-data ui-widget">
	<thead>
	<tr class="ui-state-highlight">
		<th align="center">ชื่อสินค้า</th>
		<th align="center">Internal Code.</th>
		<th align="center">Ext. Code</th>
		<th align="center" width="10%">&nbsp;</th>
		<th align="center" width="10%">เครื่องรีด</th>
	</tr>
	</thead>
	<tbody>
		<? for($i = 0; $i < count($group_list); $i++) { ?>
		<tr height="32" class="<?= ($i % 2 == 1) ? "even" : "odd" ?>">
			<td align="center"><a href="<?= site_url("product/product_detail/" . $group_list[$i]["product_dtl_id"]) ?>" class="link"><?= $products[$group_list[$i]["product_dtl_id"]] ?></a></td>
			<td align="center"><?= $group_list[$i]["program_code"] ?></td>
			<td align="center"><?= $group_list[$i]["ext_code"] ?></td>
			<td align="center"><input type="button" name="changeProductButton" value="เปลี่ยนสินค้า" class="button" onclick="changeProductPanel('<?= $group_list[$i]["program_code"] ?>', '<?= $group_list[$i]["product_dtl_id"] ?>')" /></td>
			<td align="center">
			<? if ($ironing_machine[$group_list[$i]["product_dtl_id"]]["result"]) { ?>
				<input type="button" name="ironButton" value="ไม่ใช้งาน" class="button" onclick="populateIronMachine('<?= $group_list[$i]["product_dtl_id"] ?>', 'DELETE', '<?= $ironing_machine[$group_list[$i]["product_dtl_id"]]["mc_id"] ?>')" />
			<? } else { ?>
				<input type="button" name="ironButton" value="ใช้งาน" class="button" onclick="populateIronMachine('<?= $group_list[$i]["product_dtl_id"] ?>', 'ADD', '')" />
			<? } ?>
			</td>
		</tr>
		<? } ?>
	</tbody>
</table>
<br/>
<table border="1" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>รายการ Coil ย่อย</td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="2" class="table-data ui-widget">
	<thead>
	<tr class="ui-state-highlight">
		<th align="center">COIL ID</th>
		<th align="center">No.</th>
		<th align="center">ความหนา (มิลลิเมตร)</th>
		<th align="center">ความกว้าง (มิลลิเมตร)</th>
		<th align="center">น้ำหนัก (กิโลกรัม)</th>
		<th align="center">วันที่รับสินค้า</th>
		<th align="center">สถานะ</th>
	</tr>
	</thead>
	<tbody>
	<? 
		$total_weight = 0;
		for($i = 0; $i < count($coil_result); $i++) {
			$total_weight += $coil_result[$i]['weight'];
	?>
		<tr height="32" class="<?= ($i % 2 == 1) ? "even" : "odd" ?>">
			<td align="center"><a href="<?= site_url("/coil/coil_detail/" . $this->convert->AsciiToHex($coil_result[$i]['coil_id']) . "/" . $coil_result[$i]['coil_lot_no'] . "/" . $this->convert->AsciiToHex($po_id)) ?>" class="link"><?= $coil_result[$i]['coil_id'] ?></a></td>
			<td align="center"><?= $coil_result[$i]['coil_lot_no'] ?></td>
			<td align="center"><?= number_format($coil_result[$i]['thickness'], 2) ?></td>
			<td align="center"><?= number_format($coil_result[$i]['width'], 2) ?></td>
			<td align="center"><?= number_format($coil_result[$i]['weight'], 0) ?></td>
			<td align="center"><?= mysqldatetime_to_date($coil_result[$i]['coil_received_date'], "d/m/Y") ?></td>
			<td align="center"><?= $coil_status_result[$coil_result[$i]['coil_status']] ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>
<br/>
<? for($i = 0; $i < count($group_list); $i++) { ?>
<table border="1" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>ประวัติการผลิตแผ่น Film ของ  <?= $products[$group_list[$i]["product_dtl_id"]] ?></td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="2" class="table-data ui-widget">
	<thead>
	<tr class="ui-state-highlight">
		<th align="center" width="33%">วันที่</th>
		<th align="center" width="33%">จำนวนแถบ</th>
		<th align="center" width="33%">ชื่อเครื่อง</th>
	</tr>
	</thead>
	<tbody>
	<?
		$q_list = $quantity_list[$group_list[$i]["product_dtl_id"]];

		if (count($q_list) > 0) {
			for($j = 0; $j < count($q_list); $j++) {
			
				if ($q_list[$j]["quantity"] == 0) {
					continue;
				}
	?>
		<tr>
			<td align="center"><?= $q_list[$j]["film_date"] ?></td>
			<td align="center"><?= $q_list[$j]["quantity"] ?></td>
			<td align="center"><?	
			
				if (isset($machine[$q_list[$j]["mc_id"]])) {
					if ($machine[$q_list[$j]["mc_id"]]["type"] == "Y") {
						echo $machine[$q_list[$j]["mc_id"]]["name"] . " ( เครื่องรีด )";
					} else {	
						echo $machine[$q_list[$j]["mc_id"]]["name"];
					}
					
				} else {
					echo "&nbsp;";
				}
			?>
			</td>
		</tr>
	<?
			}
		} else {
	?>
		<tr>
			<td colspan="3" align="center" class="ui-state-highlight">ไม่ข้อมูลในส่วนนี้</td>
		</tr>
	<? 	} ?>
	</tbody>
</table>
<br/>
<? } ?>
<div id="ironPanel" style="display: none;" title="แสดงรายชื่อเครื่องยืด">
<?= form_open("group/populate_iron_machine", array("id" => "ironForm")) ?>
<input type="hidden" name="group_code" value="<?= $group_code ?>" />
<input type="hidden" name="product_dtl_id" id="product_dtl_id" class="product_dtl_id" value="" />
<input type="hidden" name="old_machine" id="old_machine" value="" />
<input type="hidden" name="mode" value="ADD" />
ชื่อเครื่อง : <select name="mc_id">
<? foreach($machine as $key => $item) { 
	
	if ($item["type"] != "Y") {
		continue;
	}
?>
	<option value="<?= $key ?>"><?= $item["name"] ?></option>
<? } ?>
</select>
<?= form_close() ?>
</div>
<div id="deleteIronPanel" style="display: none;" title="คำยืนยัน">
คุณต้องการเลิกใช้งานเครื่องรีด ใช่หรือไม่?
<?= form_open("group/populate_iron_machine", array("id" => "deleteIronForm")) ?>
<input type="hidden" name="group_code" value="<?= $group_code ?>" />
<input type="hidden" name="product_dtl_id" class="product_dtl_id" value="" />
<input type="hidden" name="old_machine" class="old_machine" value="" />
<input type="hidden" name="mc_id" value="1" />
<input type="hidden" name="mode" value="DELETE" />
<?= form_close() ?>
</div>
<script type="text/javascript">

var products = <?= json_encode($product_list) ?>;

$(document).ready(function(){
	$(".button").button();
	$("#backButton").click(onBackButtonClick);
	$("#total_weight").text("<?= number_format($total_weight, 0) ?>");
	$("#loading").hide();
});
function onBackButtonClick(){
	document.location.replace("<?= site_url("/film") ?>");
}
function populateIronMachine(product_dtl_id, mode, oldMachine)
{
	$(".product_dtl_id").val(product_dtl_id);
	$(".old_machine").val(oldMachine);
	
	if (mode == "ADD")
	{
		$("#ironPanel").dialog({
			modal: true,
			buttons: {
				'ตกลง' : function() {
					$("#ironForm").submit();
					$("#pleasewaitPanel").dialog({
						modal: true,
						open: function() {
							$(".ui-dialog-titlebar-close").hide();
						},
						closeOnEscape : false,
						resizable: false,
					});
				},
				'ยกเลิก' : function() {
					$(this).dialog("close");
				}
			}
		});
	}
	else 
	{
		$("#deleteIronPanel").dialog({
			modal: true,
			buttons: {
				'ตกลง' : function() {
					$("#deleteIronForm").submit();
					$("#pleasewaitPanel").dialog({
						modal: true,
						open: function() {
							$(".ui-dialog-titlebar-close").hide();
						},
						closeOnEscape : false,
						resizable: false,
					});
				},
				'ยกเลิก' : function() {
					$(this).dialog("close");
				}
			}
		});
	}
}
function changeProductPanel(program_code, product_dtl_id) {
	
	$("#old_program_code").val(program_code);
	$("#old_product_dtl_id").val(product_dtl_id);

	$("#changeProductPanel").dialog({
		modal: true,
		width: 550,
		open: function() {
		
			// Populate Select
			var dom = $("#new_product_dtl_id").get(0);
			dom.options.length = 0;
			
			var temp_product = products[product_dtl_id];
			for(var key in temp_product)
			{
				dom.options[dom.options.length] = new Option(temp_product[key], key);
			}
			
			
			$("#new_program_code").val("").select().focus();
			if ($("#new_product_dtl_id").val()) {
				getProgramCode($("#new_product_dtl_id").val());
			}
			
			$("#loading").hide();
		},
		buttons : {
			'ปิด' : function() {
				$(this).dialog("close");
			},
			'เปลี่ยน' : function() {
			
				if (!$("#new_program_code").val()) {
					$("#warningPanel").html("กรุณาใส่เลขโปรแกรมภายในให้เรียบร้อย");
					$("#warningPanel").dialog({
						modal: true,
						buttons : {
							'ปิด' : function() {
								$(this).dialog("close");
							}
						},
						close: function() {
							$("#new_program_code").select().focus();
						}
					});
					return;
				}
			
				showConfirmation();
				$(this).dialog("close");
			}
		}
	});
}
function showConfirmation() {
	$("#confirmPanel").dialog({
		modal: true,
		buttons : { 
			'ปิด' : function() {
				$(this).dialog("close");
			},
			'ยืนยัน' : function() {
				$("#changeProductForm").submit();
				$("#pleasewaitPanel").dialog({
					modal: true,
					open: function() {
						$(".ui-dialog-titlebar-close").hide();
					},
					closeOnEscape : false,
					resizable: false,
				});
			}
		}
	});
}

function getProgramCode(product_dtl_id) {
	if (product_dtl_id == "") {
		return;
	}
	$("#loading").html("Loading").show();
	$("#new_product_dtl_id").attr("disabled", "disabled");
	$("#new_program_code").attr("disabled", "disabled");
	var sel = document.getElementById("new_program_code");
	sel.options.length = 0;
	$.get("<?= site_url("group/get_program_code_by_product_dtl_id/") ?>/" + product_dtl_id, function(data) {
		$("#new_product_dtl_id").removeAttr("disabled");
		$("#new_program_code").removeAttr("disabled");

		sel.options.length = 0;
		
		sel.options.length=data.length;
		for(var i=0;i<data.length;i++)
		{
			sel.options[i].text=data[i];
			sel.options[i].value=data[i];
		}
		
		if (data.length == 0) {
			$("#loading").html("No data.");
		} else {
			$("#loading").hide();
		}
		
	}, "json");
}

function historyButtonClick() {
	$("#historyPanel").dialog({
		modal: true,
		width: 550
	});
}
</script>
<br/><br/><br/>
<div id="warningPanel" title="Information" style="display:none;">
กรุณาใส่เลขโปรแกรมภายในให้เรียบร้อย
</div>
<div id="pleasewaitPanel" title="Information" style="display:none;">
กรุณารอสักครู่
</div>
<div id="changeProductPanel" title="เปลี่ยนสินค้า" style="display:none;">
<?= form_open("group/change_product", array("id" => "changeProductForm")) ?>
<input type="hidden" id="old_program_code" name="old_program_code" value="" />
<input type="hidden" id="old_product_dtl_id" name="old_product_dtl_id" value="" />
<input type="hidden" id="coil_group_code" name="coil_group_code" value="<?= $group_code ?>" />
<table border="0" width="100%" class="ui-widget">
	<tr>
		<td>ชื่อสินค้า</td>
		<td>
			<select id="new_product_dtl_id" name="new_product_dtl_id" onchange="getProgramCode(this.value)">
			<? foreach($product_list as $key => $value) { ?>
				<option value="<?= $key ?>"><?= $value ?></option>
			<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>เลขโปรแกรมภายใน</td>
		<td>
			<select id="new_program_code" name="new_program_code">
				<option value=""></option>
			</select><span id="loading">Loading</span>
		</td>
	</tr>
</table>
<?= form_close() ?>
</div>
<div id="confirmPanel" title="คำยืนยัน" style="display:none;">
คุณแน่ใจแล้วใช่หรือไม่ที่ต้องการเปลี่ยนสินค้า
</div>
<div id="historyPanel" title="ประวัติการเปลี่ยนแปลงเครื่อง" style="display:none;">
<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th>วันที่</th>
			<th>ชื่อเครื่อง</th>
		</tr>
	</thead>
	<tbody>
	<? for($i = 0; $i < 20; $i++) { ?>
		<tr class="<?= (($i %2 ) == 0) ? "odd" : "even" ?>">
			<td align="center">2011-20-20 12:23:23</td>
			<td align="center">CFU1</td>
		</tr>
	<? }?>
	</tbody>
</table>
</div>