<table id="menuTable" border="0" width="100%">
	<tr>
		<td align="left"><!--<button id="print">Print</button>--></td>
		<td align="right"><button id="mainButton">กลับสู่เมนูหลัก</button></td>
	</tr>
</table>
<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center">&nbsp;</th>
			<th align="center">Coils Coming</th>
			<th align="center">Coils Coming Metallic</th>
			<th align="center">Coil Stock</th>
			<th align="center">Coil Stock Metallic</th>
			<th align="center">Coils needed</th>
			<th align="center">Coil Balance</th>
			<th align="center">Inventory</th>
			<th align="center">Delivering</th>
			<th align="center">Delivering(P)</th>
			<th align="center">Delivering(P*)</th>
			<th align="center">In Production</th>
			<th align="center">Grand Total Stock</th>
			<th align="center">CCH Inventory</th>
			<th align="center">CTC Inventory</th>
			<th align="center">Pipe Inventory</th>
		</tr>
	</thead>
	<tbody>
		<? 
		$i = 0;
		$total_weight_metallic = 0;
		$total_weight = 0;
		$total_coil_weight_metallic = 0;
		$total_coil_weight = 0;
		$total_coil_need = 0;
		$total_coil_balance = 0;
		$total_inventory = 0;
		$total_delivery = 0;
		$total_delivery_p = 0;
		$total_delivery_p1 = 0;
		$total_in_production = 0;
		$total_grand = 0;
		$total_cch_inventory = 0;
		$total_ctc_inventory = 0;
		$total_pipe_inventory = 0;
		for($i = 0; $i < count($report); $i++) { 
		
			//print_r($report[$i]);
			//print_r($coil_weight[$report[$i]]);
			//print_r($coil_weight[$report[$i]["thickness_rep"]]);
			//print_r($coil_weight[$report[$i]]);
			//print_r($weight[$report[$i]["thickness_rep"]] );
			//print_r($coil_weight[$report[$i]]);
			
			//$coil_balance = ($report[$i]["coil_coming"]+ $weight[$report[$i]["thickness_rep"]] + $coil_weight[$report[$i]["thickness_rep"]]) - $report[$i]["coil_needed"];
			$coil_balance = ($report[$i]["coil_coming"] + $coil_weight[$report[$i]["thickness_rep"]]) - $report[$i]["coil_needed"];
			
			
			$grand_total = $coil_balance + $report[$i]["coil_needed"] + $report[$i]["inventory"] - $report[$i]["delivering"] + $report[$i]["in_production"]; 
		
			$total_weight_metallic += $report[$i]["coil_coming_metallic"];
			$total_weight += $report[$i]["coil_coming"];
			$total_coil_weight_metallic += $coil_weight_metallic[$report[$i]["thickness_rep"]];
			$total_coil_weight += $coil_weight[$report[$i]["thickness_rep"]];
			$total_coil_need += $report[$i]["coil_needed"];
			$total_coil_balance += $coil_balance;
			$total_inventory += $report[$i]["inventory"];
			$total_delivery += $report[$i]["delivering"];
			$total_delivery_p += $report[$i]["delivering_p"];
			$total_delivery_p1 += $report[$i]["delivering_p1"];
			$total_in_production += $report[$i]["in_production"];
			$total_grand += $grand_total;
			$total_cch_inventory += $report[$i]["cch_inventory"];
			$total_ctc_inventory += $report[$i]["ctc_inventory"];
			$total_pipe_inventory += $report[$i]["inventory"] - $report[$i]["cch_inventory"] - $report[$i]["ctc_inventory"];
		
		?>
		<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
			<td><?= number_format($report[$i]["thickness_rep"], 2) ?></th>
			<td><?= number_format($report[$i]["coil_coming"], 0) ?></th>
			<td><?= number_format($report[$i]["coil_coming_metallic"], 0) ?></th>
			<td><?= number_format($coil_weight[$report[$i]["thickness_rep"]], 0) ?></th>
			<td><?= number_format($coil_weight_metallic[$report[$i]["thickness_rep"]], 0) ?></th>
			<td><?= number_format($report[$i]["coil_needed"], 0) ?></th>
			<td><?= number_format($coil_balance, 0) ?></th>
			<td><?= number_format($report[$i]["inventory"], 0) ?></th>
			<td><?= number_format($report[$i]["delivering"], 0) ?></th>
			<td><?= number_format($report[$i]["delivering_p"], 0) ?></th>
			<td><?= number_format($report[$i]["delivering_p1"], 0) ?></th>
			<td><?= number_format($report[$i]["in_production"], 0) ?></th>
			<td><?= number_format($grand_total, 0) ?></th>
			<td><?= number_format($report[$i]["cch_inventory"], 0) ?></th>
			<td><?= number_format($report[$i]["ctc_inventory"], 0) ?></th>
			<td><?= number_format($report[$i]["inventory"] - $report[$i]["cch_inventory"] - $report[$i]["ctc_inventory"], 0) ?></th>
		</tr>
		<? } ?>
		<tr class="ui-state-highlight odd" style="font-weight: bold">
			<td>Total</th>
			<td><?= number_format($total_weight, 0) ?></th>
			<td><?= number_format($total_weight_metallic, 0) ?></th>
			<td><?= number_format($total_coil_weight, 0) ?></th>
			<td><?= number_format($total_coil_weight_metallic, 0) ?></th>
			<td><?= number_format($total_coil_need, 0) ?></th>
			<td><?= number_format($total_coil_balance, 0) ?></th>
			<td><?= number_format($total_inventory, 0) ?></th>
			<td><?= number_format($total_delivery, 0) ?></th>
			<td><?= number_format($total_delivery_p, 0) ?></th>
			<td><?= number_format($total_delivery_p1, 0) ?></th>
			<td><?= number_format($total_in_production, 0) ?></th>
			<td><?= number_format($total_grand, 0) ?></th>
			<td><?= number_format($total_cch_inventory, 0) ?></th>
			<td><?= number_format($total_ctc_inventory, 0) ?></th>
			<td><?= number_format($total_pipe_inventory, 0) ?></th>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button();
	$("#mainButton").click(onMainButtonClick);
});
function onMainButtonClick() {
	document.location.href="<?= site_url("/main") ?>";
}
</script>