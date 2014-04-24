<?= form_open("/program/now_page") ?>
<table border="0" width="100%" class="ui-widget">
	<tr>
		<td align="left">
			<input type="button" id="backButton" name="backButton" class="button2" value="ย้อนกลับสู่หน้าแรก" />
			<input type="button" id="historyButton" name="historyButton" class="button" value="ข้อมูลเก่า" />
		</td>
		<td align="right">
			<input type="button" id="saveButton" name="saveButton" class="button" value="บันทึก" onclick="onChangeMachine()" />
		</td>
	</tr>
</table>
<?= form_close() ?>
<style type="text/css" rel="stylesheet">
.red_color {
	font-weight: bolder;
	color : white;
	background-color: red;
}
</style>

<?= form_open("program/change_machine_alot", array("id" => "myform")) ?>
<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center">Ext Code</th>
			<th align="center">จำนวนเส้นที่ผลิต</th>
			<th align="center">น้ำหนักรวม</th>
			<th align="center">% เกรด B</th>
			<th align="center">Film คงเหลือ</th>
			<th align="center">ชื่อสินค้า</th>
			<th align="center">เครื่อง</th>
		</tr>
	</thead>
	<tbody>
	<?
		$bool = FALSE;
		//print_r($program_result);
		$bool = FALSE;
		foreach($program_result as $item) { 
			$film_remaining = $item['weight'] - ($item["est_weight"] * $item["total_unit"]);
	
			$grade_b_red = FALSE;
			if ($item["grade_b"] > 1) {
				$grade_b_red = TRUE;
			}
			
			$weight_red = FALSE;
			// if ($item[""] 
			
			$class = "";
			if ($bool) {
				$class = "odd";
			} else {
				$class = "even";
			}

			if ($grade_b_red) {
				$grade_b_class = "red_color";
			} else {
				$grade_b_class = "";
			}
			
			/*
			if ($item['weight'] < $item["est_weight_min"] || $item["weight"] > $item["accounting_weight"])
			{
				$weight_class = "red_color";
			}
			else
			{
				$weight_class = "";
			}
			*/
			
			$weight_class = "";
	?>
	
		<tr class="<?= $class ?>">
			<td><a href="<?= site_url("/program/program_detail/" . $this->convert->AsciiToHex($item['program_code']) . "/" . $this->convert->AsciiToHex($item['product_dtl_id']) . "/0") ?>" class="link"><?= $item['program_code_ext'] ?></a></td>
			<td><?= number_format($item['total_unit'], 0) ?></td>
			<td class="<?= $weight_class ?>"><?= number_format($item['weight'], 0) ?></td>
			<td class="<?= $grade_b_class ?>"><?= number_format($item['grade_b'], 2) ?></td>
			<td><?= number_format($film_remaining, 0) ?></td>
			<td nowrap="nowrap"><?= $item["product_name_th"] ?></td>
			<td><?php
				$option = array();
				$option[""] = "";
				foreach($machine as $mc_id => $mc_name)
				{
					$option[$mc_id] = $mc_name;
				}
			
				echo form_hidden("program_code[]", $item["program_code"]);
				echo form_hidden("product_dtl_id[]", $item["product_dtl_id"]);
				echo form_dropdown("machine[]", $option, $item["mc_id"]);
			
			?>
			</td>
		</tr>
	<? 		$bool = !$bool;
		} ?>
	</tbody>
</table>
<?= form_close() ?>

<script type="text/javascript">
$(document).ready(function(){
	$(".button, .button2").button();
	$("#backButton").click(onBackButtonClick);
	$("#historyButton").click(onHistoryButtonClick);
});
function onBackButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function onHistoryButtonClick() {
	document.location.href= "<?= site_url("/program/history") ?>";
}
function onChangeMachine() {
	$("#myform").submit();
}
</script>