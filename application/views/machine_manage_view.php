<?
	$all_config = count($machine_config) + 1;
	
	$percent = 80 / $all_config;
?>
<?= form_open("/machine_manage", array("id" => "selectForm")) ?>
<table border="0" width="100%" class="ui-widget">
	<tr>
		<td>
			เลือกวันที่ : <input type="text" id="date_input" name="date_input" value="<?= $select_date ?>" /><input type="submit" name="changeDateButton" value="เรียกดู" class="button" />
		</td>
		<td align="right">
			<input type="button" name="saveButton" value="บันทึก" class="button" />
			<input type="button" name="backButton" value="ย้อนกลับ" class="button" />
		</td>
	</tr>
</table>
<?= form_close() ?>
<?= form_open("/machine_manage/save", array("id" => "saveForm")) ?>
<input type="hidden" id="alt_date_input" name="date_input" value="<?= $select_date ?>" />
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center" width="15%">ชื่อเครื่อง</th>
			<th align="center" width="5%">OT</th>
			<th align="center" width="<?= $percent ?>%">เวลาทั้งหมด</th>
		<? for($i = 0; $i < count($machine_config); $i++) { ?>
			<th align="center" width="<?= $percent ?>%" nowrap="nowrap"><?= $machine_config[$i]['machine_config_name'] ?></th>
		<? } ?>
		</tr>
	</thead>
	<tbody>
	<? for($i = 0; $i < count($machine); $i++) { 
	
			$mc_id = $machine[$i]["mc_id"];
			
			$all_work = "480";
			$ot = "N";
			$comment = "";
			
			if (isset($work_hour[$mc_id])) 
			{
				$all_work = $work_hour[$mc_id]["duration"];
				$ot = $work_hour[$mc_id]["ot"];
				$comment = $work_hour[$mc_id]["comment"];
			}
	?>
		<tr class="even">
			<td align="center"><?= $machine[$i]["machine_name"] ?></td>
			<td align="center"><input type="checkbox" name="ot_<?= $machine[$i]["mc_id"] ?>" value="Y" rel="all_work<?= $i ?>" class="ot" <?= ($ot === "Y") ? 'checked="checked"' : '' ?>  /></td>
			<td align="center"><input type="text" id="all_work<?= $i ?>" name="all_work_<?= $machine[$i]["mc_id"] ?>" value="<?= $all_work ?>" size="3" class="machine_name" rel="ot_<?= $machine[$i]["mc_id"] ?>" /></td>
		<? for($j = 0; $j < count($machine_config); $j++) { 
		
				$config_id = $machine_config[$j]["machine_config_id"];
				
				$duration = 0;
				
				if (isset($machine_config_hour[$mc_id][$config_id])) 
				{
					$duration = $machine_config_hour[$mc_id][$config_id];
				}
		?>
			<td align="center"><input type="text" name="config_<?= $machine[$i]["mc_id"] ?>_<?= $machine_config[$j]['machine_config_id'] ?>" value="<?= $duration ?>" size="4" class="config" /></td>
		<? } ?>
		</tr>
		<tr>
			<td align="center" valign="middle">หมายเหตุ</td><td colspan="<?=  $all_config + 2 ?>"><textarea style="width:98%;height:50px;" name="comment_<?= $machine[$i]["mc_id"] ?>"><?= $comment ?></textarea></td>
		</tr>
	<? } ?>
	</tbody>
</table>
<?= form_close() ?>

<script type="text/javascript">
$(document).ready(function() {
	$(".button").button();
	$("#date_input").datepicker({
		dateFormat: 'yy-mm-dd',
		altField : "#alt_date_input",
		onSelect : function() {
			$("#selectForm").submit();
		}
	});
	
	$(".ot").click(function() {
		if ($(this).attr("checked")) {
			$("#" + $(this).attr("rel")).val("720");
		} else {
			$("#" + $(this).attr("rel")).val("480");
		}
	});
	
	$("input[type=text]").set_format({precision: 0,allow_negative:false});
	
	$("input[type!='hidden']").keypress(changeControlFocus);
	
	$("input[name=backButton]").click(function() {
		document.location.replace("<?= site_url("main") ?>");
	});
	
	$("input[name=saveButton]").click(onSave);
});

function onSave() {
	$.each($("input[type=text]"), function(index,value) {
		if (!$(this).val()) {
			if ($(this).hasClass("machine_name")) {
				$(this).val("480");
				$("input[name=" + $(this).attr("rel") + "]").removeAttr("checked");
			} else {
				$(this).val("0");
			}
		}
	});
	
	$("#saveForm").submit();
}

function changeControlFocus(event) {
	if (event.keyCode == 13) {
		$(this).focusNextInputField();
	}
}


</script>
<br/><br/><br/>