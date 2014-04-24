<table border="0" width="100%">	
	<tr>
		<td>
			<input type="button" id="addButton" name="addButton" class="button" value="เพิ่มข้อมูล" />
			<input type="button" id="editButton" name="editButton" class="button" value="แก้ไขข้อมูล" />
			<input type="button" id="deleteButton" name="deleteButton" class="button" value="ลบข้อมูล" />
		</td>
		<td align="right">
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />
		</td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="25" align="center">&nbsp;</th>
			<th align="center">ชื่อ Config </th>
		</tr>
	</thead>
	<tbody>
	<? for($i = 0; $i < count($result); $i++) {  ?>
		<tr class="<?= ($i % 2 == 0) ? "even" : "odd" ?>">
			<td><input type="radio" name="machine_config_id" class="checkbox" value="<?= $result[$i]["machine_config_id"] ?>" machine_config_name="<?= $result[$i]["machine_config_name"] ?>" /></td>
			<td><?= $result[$i]["machine_config_name"] ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>

<div id="addPanel" title="เพิ่ม" style="display:none;">
<?= form_open("/machine/config_add", array("id"=>"addForm")) ?>
ชื่อ Config : <input type="text" id="add_machine_config_name" name="machine_config_name" value="" />
<?= form_close() ?>
</div>

<div id="editPanel" title="แก้ไข" style="display:none;">
<?= form_open("/machine/config_edit", array("id"=>"editForm")) ?>
<input type="hidden" id="edit_machine_config_id" name="machine_config_id" value="" class="machine_config_id" />
ชื่อ :<input type="text" id="edit_machine_config_name" name="machine_config_name" value="" />
<?= form_close() ?>
</div>

<div id="deletePanel" title="ลบ" style="display:none;">
<?= form_open("/machine/config_delete", array("id"=>"deleteForm")) ?>
คุณต้องการลบข้อมูลนี้ใช่หรือไม่
<input type="hidden" id="del_machine_config_id" name="machine_config_id" value="" class="machine_config_id" />
<?= form_close(); ?>
</div>

<script type="text/javascript">
$(document).ready(function(){ 
	$(".button").button();
	$("#backButton").click(function() {
		document.location.replace("<?= site_url("/machine") ?>");
	});
	
	$("input[name=machine_config_id]").click(function() {
		$(".machine_config_id").val($(this).val());
		$("#edit_machine_config_name").val($(this).attr("machine_config_name"));
	});
	
	$("#addButton").click(function() {
		$("#addPanel").dialog({
			modal: true,
			open : function() {
				$("#add_machine_config_name").val("").focus().select();
			},
			buttons:  {
				"ปิด" : function() {
					$(this).dialog("close");
				},
				"บันทึก" : function() {
						
					if (!$("#add_machine_config_name").val()) {
						$("#add_machine_config_name").focus().select();
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
					if (!$("#edit_machine_config_name").val()) {
						$("#edit_machine_config_name").focus().select();
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
	
	$("input[name=machine_config_id]:first").click();
});
</script>
<br/><br/><br/>