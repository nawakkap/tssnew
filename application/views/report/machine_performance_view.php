<?
	$all_config = count($config) + 1;
	
	$percent = 80 / $all_config;
?>
<?= form_open("report/performance_report", array("id" => "queryForm")) ?>
<table border="0" width="100%" class="ui-widget">
	<tr>
		<td>จาก <input type="text" id="startDate" name="startDate" value="<?= $startDate ?>" />&nbsp; ถึง <input type="text" id="endDate" name="endDate" value="<?= $endDate ?>" />&nbsp;&nbsp;&nbsp;<input type="submit" name="queryButton" value="เรียกดู" class="button" /></td>
		<td align="right">
			<input type="button" id="backButton" name="backButton" value="ย้อนกลับ" class="button" />
		</td>
	</tr>
</table>
<?= form_close() ?>
<br/>
<table border="1" width="100%" class="table-data ui-widget">	
	<thead>
		<tr class="ui-widget-header">
			<th width="10%" nowrap="nowrap">ชื่อเครื่อง</th>
			<th width="10%" nowrap="nowrap">เวลาเดินเครื่อง</th>
			<th width="10%" nowrap="nowrap">จำนวนเส้น</th>
			<th width="10%" nowrap="nowrap">จำนวนเส้น/นาที</th>
		<? for($i = 0; $i < count($config); $i++) { ?>
			<th nowrap="nowrap">% <?= $config[$i]["machine_config_name"] ?></th>
		<? } ?>
			<th nowrap="nowrap">%Utilize</th>
		</tr>
	</thead>
	<tbody>
	<? 
		for($i = 0; $i < count($machine); $i++) { 
			
			$machine_type = $machine[$i]["machine_type"];
			
			if ($machine_type != "") { // Ignore Other Machine
				continue;
			}
			
			$mc_id = $machine[$i]["mc_id"];
			
			$all_duration = $machine[$i]["duration"];
			
			$worst_duration = 0;
			
			$unit = 0;
			if (isset($total_unit[$machine[$i]["mc_id"]])) {
				$unit = $total_unit[$machine[$i]["mc_id"]];
			}
			
			for($j = 0; $j < count($config); $j++) { 
			
				$config_id = $config[$j]["machine_config_id"];
				
				$duration = 0;
				if (isset($config_result[$mc_id][$config_id])) {
					$duration = $config_result[$mc_id][$config_id];
				}
				
				$worst_duration += $duration;
			}
			
			$unit_per_minute = 0;
			if ($unit != 0) {
				$unit_per_minute = $unit / ($all_duration - $worst_duration);
			}
			
			$dateDiffMachine = (isset($dateDiff[$mc_id]) ? $dateDiff[$mc_id] : 0);
	?>
		<tr class="<?= ($i % 2) ? "even" : "odd" ?>">
			<td align="center"><?= $machine[$i]["machine_name"] ?></td>
			<td align="center" title="<?= $dateDiffMachine ?>วัน"><?= number_format($all_duration, 0) ?></td>
			<td align="center"><?= number_format($unit, 0) ?></td>
			<td align="center"><?= number_format($unit_per_minute, 4) ?></td>
		<? 
			for($j = 0; $j < count($config); $j++) { 
			
				$config_id = $config[$j]["machine_config_id"];
				
				$duration = 0;
				if (isset($config_result[$mc_id][$config_id])) {
					$duration = $config_result[$mc_id][$config_id];
				}
				
				//$worst_duration += $duration;
				
				$percent = number_format($duration / $all_duration, 2);
		?>
			<td align="center"><?= $percent ?></td>
		<? } ?>
		
		<?
			// Calculate %Utilize
			
			$utilize = ($all_duration - $worst_duration) / $all_duration;
		?>
			<td align="center"><?= number_format($utilize, 2) ?></td>
		</tr>
	<? }?>
	</tbody>
</table>
<br/>
	<!-- Ironing Machine -->
	<? if (count($special_machine) > 0) { ?>
<table border="1" width="100%" class="table-data ui-widget">	
	<thead>
		<tr class="ui-widget-header">
			<th width="10%" nowrap="nowrap">ชื่อเครื่อง</th>
			<th width="10%" nowrap="nowrap">เวลาเดินเครื่อง</th>
			<th width="10%" nowrap="nowrap">จำนวนแถบ</th>
			<th width="10%" nowrap="nowrap">แถบ/ชั่วโมง</th>
		<? for($i = 0; $i < count($config); $i++) { ?>
			<th nowrap="nowrap">% <?= $config[$i]["machine_config_name"] ?></th>
		<? } ?>
			<th nowrap="nowrap">%Utilize</th>
		</tr>
	</thead>
	<tbody>
	<? 	
		for($i = 0; $i < count($special_machine); $i++) { 
		
			$mc_id = $special_machine[$i]["mc_id"];
			
			$all_duration = $special_machine[$i]["duration"];
			
			$worst_duration = 0;
			
			$unit = 0;
			if (isset($film_unit[$special_machine[$i]["mc_id"]])) {
				$unit = $film_unit[$special_machine[$i]["mc_id"]];
			}
			
			$unit_per_minute = 0;
			if ($all_duration != 0) 
			{
				$unit_per_minute = $unit / $all_duration;
			}
			
			$unit_per_minute_per_day = 0;
			$dateDiffMachine = (isset($dateDiff[$mc_id]) ? $dateDiff[$mc_id] : 0);
			if ($dateDiffMachine != 0)
			{
				$unit_per_minute_per_day = ($unit_per_minute * 60) / $dateDiffMachine;
			}

	?>
		<tr class="<?= ($i % 2) ? "even" : "odd" ?>">
			<td align="center" title="<?= $dateDiffMachine ?>วัน"><?= $special_machine[$i]["machine_name"] ?></td>
			<td align="center"><?= number_format($all_duration, 0) ?></td>
			<td align="center"><?= number_format($unit, 0) ?></td>
			<td align="center" title="<?= $dateDiffMachine ?>วัน"><?= number_format($unit_per_minute_per_day , 4) ?></td>
		<? 	for($j = 0; $j < count($config); $j++) { 
		
			$config_id = $config[$j]["machine_config_id"];
			
			$duration = 0;
			if (isset($config_result[$mc_id][$config_id])) {
				$duration = $config_result[$mc_id][$config_id];
			}
			
			$worst_duration += $duration;
			
			$percent = 0;
			if ($all_duration != 0) {
				$percent = number_format($duration / $all_duration, 2);
			}

		?>
			<td align="center"><?= $percent ?></td>
		<?	} ?>
		<?
			// Calculate %Utilize
			
			if ($all_duration != 0) {
				$utilize = ($all_duration - $worst_duration) / $all_duration;
			}
		?>
			<td align="center"><?= number_format($utilize, 2) ?></td>
	<? } ?>
	<? } ?>
	</tbody>
</table>
<br/>	
	
	<!-- Sliter Machine -->
	<? if (count($slitter_machine) > 0) { ?>
<table border="1" width="100%" class="table-data ui-widget">	
	<thead>
		<tr class="ui-widget-header">
			<th width="10%" nowrap="nowrap">ชื่อเครื่อง</th>
			<th width="10%" nowrap="nowrap">เวลาเดินเครื่อง</th>
			<th width="10%" nowrap="nowrap">จำนวนม้วน</th>
			<th width="10%" nowrap="nowrap">ม้วน/ชั่วโมง</th>
		<? for($i = 0; $i < count($config); $i++) { ?>
			<th nowrap="nowrap">% <?= $config[$i]["machine_config_name"] ?></th>
		<? } ?>
			<th nowrap="nowrap">%Utilize</th>
		</tr>
	</thead>
	<tbody>
	<? 	
		for($i = 0; $i < count($slitter_machine); $i++) { 
		
			$mc_id = $slitter_machine[$i]["mc_id"];
			
			$all_duration = $slitter_machine[$i]["duration"];
			
			$worst_duration = 0;
			
			$percent = 0;
			if ($all_duration != 0) 
			{
				$percent = $slited_coil["slitted_coil"] / $all_duration;
			}
			
			$percent_per_day = 0;
			$dateDiffMachine = (isset($dateDiff[$mc_id]) ? $dateDiff[$mc_id] : 0);
			if ($dateDiffMachine != 0)
			{
				$percent_per_day = ($percent * 60) / $dateDiffMachine;
			}
	?>
	<tr class="<?= ($i % 2) ? "even" : "odd" ?>">
			<td align="center"><?= $slitter_machine[$i]["machine_name"] ?></td>
			<td align="center" title="<?= $dateDiffMachine ?>วัน"><?= number_format($all_duration, 0) ?></td>
			<td align="center"><?= number_format($slited_coil["slitted_coil"], 0) ?></td>
			<td align="center" title="<?= $dateDiffMachine ?>วัน"><?= number_format($percent_per_day, 4) ?></td>
		<? 	for($j = 0; $j < count($config); $j++) { 
		
			$config_id = $config[$j]["machine_config_id"];
			
			$duration = 0;
			if (isset($config_result[$mc_id][$config_id])) {
				$duration = $config_result[$mc_id][$config_id];
			}
			
			$worst_duration += $duration;
			
			$percent = 0;
			if ($all_duration != 0) {
				$percent = number_format($duration / $all_duration, 2);
			}
		?>
			<td align="center"><?= $percent ?></td>
		<?	} ?>
		<?
			// Calculate %Utilize
			
			$utilize = 0;
			if ($all_duration != 0) {
				$utilize = ($all_duration - $worst_duration) / $all_duration;
			}
		?>
			<td align="center"><?= number_format($utilize, 2) ?></td>
	<?	}?>
	<? 	} ?>
	</tbody>
</table>
<script type="text/javascript">
$(document).ready(function() {
	$("#startDate, #endDate").datepicker({dateFormat: 'yy-mm-dd'});
	$(".button").button();
	
	$("#backButton").click(function() {
		document.location.replace("<?= site_url("/report") ?>");
	});
});
</script>                                                                                                                                                                                                                                                               