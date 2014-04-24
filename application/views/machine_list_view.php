<table border="0" width="100%">	
	<tr>
		<td>
			<input type="button" id="addButton" name="addButton" class="button" value="เพิ่มข้อมูล" />
			<input type="button" id="editButton" name="editButton" class="button" value="แก้ไขข้อมูล" />
			<input type="button" id="deleteButton" name="deleteButton" class="button" value="ลบข้อมูล" />
		</td>
		<td align="right">
			<input type="button" id="configButton" name="configButton" class="button" value="Config Data" onclick="configButtonClick()" />
			<input type="button" id="mainButton" name="mainButton" class="button" value="กลับสู่เมนูหลัก" />
		</td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="25" align="center">&nbsp;</th>
			<th align="center">ชื่อเครื่อง</th>
			<!--<th align="center">เวลาการทำงาน</th>-->
			<!-- <th align="center">สถานะการใช้งาน</th> -->
		</tr>
	</thead>
	<tbody>
	<? for($i = 0; $i < count($result); $i++) {  ?>
		<tr class="<?= ($i % 2 == 0) ? "even" : "odd" ?>">
			<td><input type="radio" name="mc_id" class="checkbox" value="<?= $result[$i]["mc_id"] ?>" machine_name="<?= $result[$i]["machine_name"] ?>" machine_type="<?= $result[$i]["machine_type"] ?>" /></td>
			<td><a href="<?= site_url("/machine/machine_detail/" . $result[$i]["mc_id"]) ?>" class="link"><?= $result[$i]["machine_name"] ?></a></td>
			<!--<td>0</td>-->
			<!-- <td><?= $result[$i]["machine_status_value"] ?></td> -->
		</tr>
	<? } ?>
	</tbody>
</table>

<div id="addPanel" title="เพิ่มชื่อเครื่อง" style="display:none;">
<?= form_open("/machine/add", array("id"=>"addForm")) ?>
<table border="0" width="100%">
	<tr>
		<td>ชื่อเครื่อง : </td><td><input type="text" id="add_machine_name" name="machine_name" value="" /></td>
	</tr>
	<tr>	
		<td>ชนิด : </td><td>
			<select name="machine_type" id="add_machine_type">
				<option value=""></option>
				<option value="Y">เครื่องรีด</option>
				<option value="S">Slitter</option>
			</select>
		</td>
	</tr>
</table>
<?= form_close() ?>
</div>

<div id="editPanel" title="แก้ไข" style="display:none;">
<?= form_open("/machine/edit", array("id"=>"editForm")) ?>
<input type="hidden" id="edit_mc_id" name="mc_id" value="" class="mc_id" />
<table border="0" width="100%">
	<tr>
		<td>ชื่อเครื่อง :</td><td><input type="text" id="edit_machine_name" name="machine_name" value="" /></td>
	</tr>
	<tr>
		<td>ชนิด : </td>
		<td>
			<select name="machine_type" id="edit_machine_type">
				<option value=""></option>
				<option value="Y">เครื่องรีด</option>
				<option value="S">Slitter</option>
			</select>
		</td>
	</tr>
</table>
<?= form_close() ?>
</div>

<div id="deletePanel" title="ลบเครื่อง" style="display:none;">
<?= form_open("/machine/delete", array("id"=>"deleteForm")) ?>
คุณต้องการลบข้อมูลนี้ใช่หรือไม่
<input type="hidden" id="del_mc_id" name="mc_id" value="" class="mc_id" />
<?= form_close(); ?>
</div>

<script type="text/javascript">
function configButtonClick() {
	document.location.replace("<?= site_url("/machine/config") ?>");
}

$(document).ready(function(){ 
	$(".button").button();
	$("input[name=mc_id]").click(function() {
		$(".mc_id").val($(this).val());
		$("#edit_machine_name").val($(this).attr("machine_name"));
		//if ($(this).attr("machine_type") == "Y") {
			$("#edit_machine_type").val($(this).attr("machine_type"));
		//} else {
		//	$("#edit_machine_type").removeAttr("checked");
		//}
	});
	$("#addButton").click(function() {
		$("#addPanel").dialog({
			modal: true,
			open : function() {
				$("#add_machine_name").val("").focus().select();
				// $("#add_machine_type").removeAttr("checked");
			},
			buttons:  {
				"ปิด" : function() {
					$(this).dialog("close");
				},
				"บันทึก" : function() {
						
					if (!$("#add_machine_name").val()) {
						$("#add_machine_name").focus().select();
						return false;
					}
					$("#addForm").submit();
				} 
			},
			resizable: false
		});
	});
	
	$("#editButton").click(function() {
		$("#editPanel").dialog({
			modal: true,
			buttons: {
				'ปิด' : function() {
					$(this).dialog("close");
				},
				'บันทึก' : function() {
					if (!$("#edit_machine_name").val()) {
						$("#edit_machine_name").focus().select();
						return false;
					}
					
					$("#editForm").submit();
				}
			},
			resizable : false
		});
	});
	
	$("#deleteButton").click(function() {
		$("#deletePanel").dialog({
			modal: true,
			buttons : {
				'ปิด' : function() {
					$(this).dialog("close");
				},
				'ตกลง' : function() {
					$("#deleteForm").submit();
				}
			},
			resizable : false
		});
	});
	
	$("#mainButton").click(function() {
		document.location.replace("<?= site_url("/main") ?>");
	});
	
	$("input[name=mc_id]:first").click();
});
</script>
<br/><br/><br/>