<?php echo form_open("film/item_detail_save"); ?>
<?php echo form_hidden("coil_group_code", $coil_group_code); ?>
<?php echo form_hidden("product_dtl_id", $product_dtl_id); ?>
<table border="0" width="100%" class="ui-widget">
	<tr>
		<td align="left">
			<input type="button" name="mainButton" class="button mainButton" onclick="onMainClick()" value="ย้อนกลับ"/>&nbsp;&nbsp;
		</td>
		<td align="right">
			<input type="submit" name="saveButton" class="button" value="บันทึก" />
		</td>
	</tr>
</table>
<br/>
<table border="1" width="100%" cellpadding="5" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center" nowrap="nowrap">Film Id</th>
			<th align="center" nowrap="nowrap">เริ่มต้นยืด</th>
			<th align="center" nowrap="nowrap">ระยะเวลาการยืด (นาที)</th>
			<th align="center" nowrap="nowrap">เครื่องยืด</th>
			<th align="center" nowrap="nowrap">เริ่มต้นผลิต</th>
			<th align="center" nowrap="nowrap">ระยะเวลา (นาที)</th>
			<th align="center" nowrap="nowrap">เครื่องผลิต</th>
			<th align="center" nowrap="nowrap">สถานะ</th>
		</tr>
	</thead>
	<tbody>

	<?php 
	// What is the day of today?
	$d = date("N");
	
	$date_minus = -1;
	if ($d == 1) // Monday
	{
		$date_minus = -2;
	}
	
	for($i = 0; $i < count($item); $i++) { 
		
		$temp = $item[$i]["temp"];
		
		
		$default_start_date = date("d/m/Y", strtotime($date_minus ." day"));
		if (isset($item[$i]["start_date"]) && $item[$i]["start_date"]) {
			$default_start_date = date("d/m/Y", strtotime($item[$i]["start_date"]));
		}
		
		$default_start_date_iron = date("d/m/Y", strtotime($date_minus ." day"));
		if (isset($item[$i]["start_date_iron"]) && $item[$i]["start_date_iron"]) {
			$default_start_date_iron = date("d/m/Y", strtotime($item[$i]["start_date_iron"]));
		}
	
	?>
		<tr>
			<td width="60%" align="center">
			<?php echo form_hidden("temp[]", $item[$i]["temp"]); ?>
			<?php echo $item[$i]["new_film_id"]; ?></td>
			<td align="center"><input type="text" class="date" size="15" name="start_date_iron_<?php echo $temp; ?>" value="<?php echo $default_start_date_iron; ?>" /></td>
			<td align="center"><input type="text" size="7" name="duration_iron_<?php echo $temp; ?>" value="<?php echo $item[$i]["duration_iron"]; ?>" /></td>
			<td align="center">
			<?php 
			
				$option = array();
				$option[""] = "";
				for($x = 0; $x < count($iron_machine); $x++)
				{
					$option[$iron_machine[$x]["mc_id"]] = $iron_machine[$x]["machine_name"];
				}
				
				echo form_dropdown("iron_mc_" . $temp, $option, $item[$i]["iron_mc"]);
			
			?>
			</td>
			<td align="center"><input type="text" class="date" size="15" name="start_date_<?php echo $temp; ?>" value="<?php echo $default_start_date; ?>" /></td>
			<td align="center"><input type="text" size="7" name="duration_<?php echo $temp; ?>" value="<?php echo $item[$i]["duration"]; ?>" /></td>
			<td align="center">
			<?php 
			
				$option = array();
				$option[""] = "";
				for($x = 0; $x < count($machine); $x++)
				{
					$option[$machine[$x]["mc_id"]] = $machine[$x]["machine_name"];
				}
				
				echo form_dropdown("mc_" . $temp, $option, $item[$i]["mc"]);
			
			?>
			</td>
			
			<td align="center">
			<?php
				
				$isIron = ($item[$i]["iron_machine"] == IRON_YES) ? TRUE : FALSE;
				
				$option = array();
				if ($isIron)
				{
					$option = array(
						"1" => "ยังไม่ได้ผลิต",
						"2" => "ยืดแล้ว",
						"3" => "ขึ้นรูป finishgoods แล้ว",
						"4" => "ปิดล๊อต"
					);
				}
				else
				{
					$option = array(
						"1" => "ยังไม่ได้ผลิต",
						//  "2" => "ยืดแล้ว",
						"3" => "ขึ้นรูป finishgoods แล้ว",
						"4" => "ปิดล๊อต"
					);
				}
			
				
				
				echo form_dropdown("status_" . $temp, $option, $item[$i]["status"]);
			?>
			</td>
		</tr>
	<?php } ?>

	</tbody>
</table>
<?php echo form_close(); ?>
<br/><br/><br/>
<style type="text/css">
.limit { background-color: #D3D3D3;text-align:center; }
</style>
<script type="text/javascript">
$(document).ready(function() {
	$(".button").button();
	$(".date").datepicker({
		dateFormat : "dd/mm/yy"
	});
});
function onMainClick() {
	document.location.replace("<?= site_url("/film") ?>");
}
</script>
