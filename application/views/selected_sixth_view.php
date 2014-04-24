<table border="0" width="100%" cellpadding="4" class="ui-widget">
	<tr>
		<td align="left" class="ui-state-highlight" width="20%" nowrap="nowrap"><b>6. สรุป</b>&nbsp;&nbsp;&nbsp;<input type="button" name="mainButton" class="button mainButton" value="กลับสู่หน้าหลัก"/></td>
		<td>&nbsp;</td>
	</tr>
</table><br/>

<?= form_open("/select_coil/slit_method") ?>
<input type="hidden" name="group_code" value="<?= $group_code ?>" />
<input type="hidden" name="slit_spec_id" value="<?= $slit_spec_id ?>" />
<!-- <input type="hidden" name="width" value="<?php /* echo $width; */ ?>"/> -->
<? foreach($wage as $key => $value) {?>
<input type="hidden" name="wage<?= $key ?>" value="<?= $value ?>" />
<? } ?>
<? foreach($program as $key => $value) {?>
<input type="hidden" name="program<?= $key ?>" value="<?= $value ?>" />
<? } ?>
<? foreach($external_program_code as $key => $value) {?>
<input type="hidden" name="external_program_code<?= $key ?>" value="<?= $value ?>"/>
<? } ?>
<? foreach($vat as $key => $value) { ?>
<input type="hidden" name="vat<?= $key ?>" value="<?= $value  ?>" />
<? } ?>
<input type="hidden" name="row_num" value="<?= count($coil_result) ?>"/>
<? for($i = 0; $i< count($coil_result) ; $i++) {?>
<input type="hidden" name="select_coil_id<?= $i ?>" value="<?= $coil_result[$i]['coil_id'] ?>" />
<input type="hidden" name="select_coil_lot_no<?= $i ?>" value="<?= $coil_result[$i]['coil_lot_no'] ?>" />
<input type="hidden" name="select_po_id<?= $i ?>" value="<?= $coil_result[$i]['po_id'] ?>" />
<? } ?>
<? foreach($machine as $key => $value) { ?>
<input type="hidden" name="machine<?= $key ?>" value="<?= $value ?>" />
<? } ?>


<table border="1" width="100%" class="table-data ui-widget">
	<tbody>
		<tr>
			<td class="ui-widget-header" width="25%">&nbsp;&nbsp;PO ID</td>
			<td width="25%">&nbsp;&nbsp;<?= $po_id ?></td>
			<td class="ui-widget-header" width="25%">&nbsp;&nbsp;Lot No</td>
			<td class="ui-state-highlight" width="25%" align="center"><b id="group"><?= $group_code ?></b></td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4">
				<table border="1" cellpadding="5" width="100%" class="table-data ui-widget">
					<thead>
						<tr class="ui-widget-header">
							<th align="center">Coil ID</th>
							<th align="center">Coil No</th>
							<th align="center">ความหนา</th>
							<th align="center">ความกว้าง</th>
							<th align="center">น้ำหนัก</th>
							<th align="center">รับมาวันที่</th>
						</tr>
					</thead>
					<tbody>
					<? 
					$total_weight  = 0;
					for($i = 0; $i < count($coil_result); $i++) { 
						$total_weight += $coil_result[$i]['weight'];
					?>
					<tr class="<?= ($i % 2) ? "even" : "odd" ?>">
						<td><?= $coil_result[$i]["coil_id"] ?></td>
						<td><?= $coil_result[$i]["coil_lot_no"] ?></td>
						<td><?= number_format($coil_result[$i]["thickness"] ,2) ?></td>
						<td><?= number_format($coil_result[$i]["width"], 2) ?></td>
						<td><?= number_format($coil_result[$i]["weight"], 0) ?></td>
						<td><?= mysqldatetime_to_date($coil_result[$i]["coil_received_date"] , 'd/m/Y') ?></td>
					</tr>
					<?php
					/*
						$nfilm = $new_film_id[$coil_result[$i]["coil_id"]];
						
						if (count($nfilm) > 0) {
					?>
					<?php for ($j = 0; $j < count($nfilm); $j++) { ?>
					<tr>
						<td align="right" colspan="2"><span style="font-size: 8pt;">Film Id :</span> <?php echo $nfilm[$j]["id"]; ?></td>
						<!-- <td><?php echo number_format($nfilm[$j]["weight"], 0); ?></td> -->
						<td colspan="4">&nbsp;</td>
					</tr>
					<?php } ?>
					<?php  } */ 
					?>
					
					<? } ?>
					<tr>
						<td colspan="4" class="ui-state-highlight" align="right"><b>รวม&nbsp;</b></td>
						<td align="center"><b><?= number_format($total_weight, 0) ?></b></td>
						<td>&nbsp;</td>
					</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td class="ui-widget-header">&nbsp;&nbsp;Slit Spec</td>
			<td colspan="3">&nbsp;&nbsp;<?= $slit_spec_remark ?></td>
		</tr>
		<tr>
			<td colspan="4">&nbsp;</td>
		</tr>
		<tr>
			<td colspan="4">
				<table border="1" width="100%" class="table-data ui-widget">
				<thead>
					<tr class="ui-widget-header">
						<th align="center">Product</th>
						<th align="center">ค่าแรง</th>
						<th align="center">น้ำหนัก Film</th>
						<th align="center">Ext. Program code</th>
						<th align="center">เครื่องรีด</th>
					</tr>
				</thead>
				<tbody>
				<?
					$i = 0;
					foreach($product_result as $item) { 
				?>
					<tr class="<?= ($i % 2) ? "odd" : "even" ?>">
						<td><?= $item["product_name_th"] ?></td>
						<td><?= $wage[$item["product_dtl_id"]] ?></td>
						<td><?= number_format($film_weight[$item["product_dtl_id"]] , 0) ?></td>
						<td><?= $external_program_code[$item["product_dtl_id"]] ?></td>
						<td><?= (isset($machine_list[$machine[$item["product_dtl_id"]]])) ? $machine_list[$machine[$item["product_dtl_id"]]] : "" ?></td>
					</tr>
				<? $i++; } ?>
				</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table><br/>
<table border="0" width="100%">
	<tr>
		<td align="center"><input type="submit" name="Tsubmit" class="button" value="ตกลง" />&nbsp;&nbsp;<input type="reset" id="backButton" name="TReset" class="button" value="ย้อนกลับ" /></td>
	</tr>
</table>
<?= form_close() ?>

<?= form_open("/select_coil/fifth_step", array("id" => "backform")) ?>
<input type="hidden" name="thickness" value="<?= $thickness ?>"/>
<input type="hidden" name="po_id" value="<?= $po_id ?>"/>
<!-- <input type="hidden" name="width" value="<?php /* echo $width; */ ?>"/> -->
<? for($i =0 ; $i < count($product_dtl_id); $i++) { ?>
<input type="hidden" name="product_dtl_id[]" value="<?= $product_dtl_id[$i] ?>" />
<? } ?>
<input type="hidden" name="slit_spec_id" value="<?= $slit_spec_id ?>"/>
<? for($i = 0; $i < count($coil_id); $i++) { ?>
<input type="hidden" name="coil_id[]" value="<?= $coil_id[$i] ?>"/>
<? } ?>
<? foreach($wage as $key => $value) {?>
<input type="hidden" name="wage<?= $key ?>" value="<?= $value ?>"/>
<? } ?>
<? foreach($vat as $key => $value) { ?>
<input type="hidden" name="vat<?= $key ?>" value="<?= $value  ?>" />
<? } ?>
<? foreach($program as $key => $value) {?>
<input type="hidden" name="program<?= $key ?>" value="<?= $value ?>"/>
<? } ?>
<? foreach($machine as $key => $value) { ?>
<input type="hidden" name="machine<?= $key ?>" value="<?= $value ?>" />
<? } ?>
<?= form_close() ?>
<br/><br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#backButton").click(onBackButtonClick);
	$("#group").hide("slow").show("slow");
	$(".mainButton").click(onMainButtonClick);
});
function onBackButtonClick() {
	$("#backform").submit();
}
function onMainButtonClick(){
	document.location.replace("<?= site_url("/main") ?>");
}
</script>