<table border="0" width="100%" class="ui-widget">
	<tr>
		<td align="right"><input type="button" id="backButton" name="backButton" value="ย้อนกลับ" class="button" /></td>
	</tr>
</table>
<table border="1" width="100%" class="ui-widget table-data">
	<tr>
		<td class="ui-widget-header" width="50%" align="center">ชื่อเครื่อง</td>
		<td class="ui-state-highlight" align="center" colspan="2"><?= $machine_info["machine_name"] ?></td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td class="ui-state-highlight" width="50%" align="center">เริ่มเก็บข้อมูลวันแรก</td>
		<td align="center" colspan="2"><?= $first_date ?></td>
	</tr>
	<tr>
		<td class="ui-state-highlight" width="50%" align="center">เวลาทั้งหมดที่ถูกใช้งาน</td>
		<td align="center" colspan="2"><?= number_format($work_hour, 0) ?> นาที</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<? 
		$worst_duration = 0;
		for($i = 0; $i < count($machine_config); $i++) { 
	
			$percent = 0;
			$duration = (isset($machine_manage[$machine_config[$i]["machine_config_id"]])) ? $machine_manage[$machine_config[$i]["machine_config_id"]] : 0;
			
			if ($work_hour != 0) {
				$percent = $duration / $work_hour;
			} else {
				$percent = 0;
			}
			
			$worst_duration += $duration;
	?>
	<tr>
		<td class="ui-state-highlight" width="50%" align="center"><?= $machine_config[$i]["machine_config_name"] ?></td>
		<td align="center" width="25%"><?= (isset($machine_manage[$machine_config[$i]["machine_config_id"]])) ? number_format($machine_manage[$machine_config[$i]["machine_config_id"]] ,0) : 0 ?> นาที</td>
		<td align="center"><?= number_format($percent, 2) ?> %</td>
	</tr>
	<? } ?>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<?
		$utilize_time = $work_hour - $worst_duration;

		$utilize_percent = 0;
		if ($work_hour == 0)
		{
			$utilize_percent = 0;
		}
		else
		{
			$utilize_percent = ($utilize_time / $work_hour);
		}
	?>
	<tr>
		<td class="ui-state-highlight" width="50%" align="center">ประสิทธิภาพ</td>
		<td align="center" colspan="2"><?= number_format($utilize_percent, 2) ?> %</td>
	</tr>
	<tr>
		<td class="ui-state-highlight" width="50%" align="center">จำนวน <b>แถบ </b> ที่ผลิตไปแล้วทั้งหมด</td>
		<td align="center" colspan="2"><?= number_format($film_unit, 0) ?> แถบ</td>
	</tr>
	<tr>
		<td class="ui-state-highlight" width="50%" align="center">จำนวน <b>เส้น</b> ที่ผลิตไปแล้วทั้งหมด</td>
		<td align="center" colspan="2"><?= number_format($total_unit, 0) ?> เส้น</td>
	</tr>
</table>
<script type="text/javascript">
$(document).ready(function() {
	$(".button").button();

	$("#backButton").click(function() {
		document.location.replace("<?= site_url("/machine") ?>");
	});
});
</script>