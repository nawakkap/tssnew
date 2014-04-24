<?= form_open("/film/film_planning") ?>
<table border="0" width="100%" class="ui-widget">
	<tr>
		<td align="left">
			<input type="button" name="mainButton" class="button mainButton" value="ย้อนกลับหน้าแรก"/>&nbsp;&nbsp;
			<input type="button" name="historyButton" class="button historyButton" value="ข้อมูลเก่า" />
		</td>
		<td align="right">
			<?
				$dropdown = array("coil_group_code" => "Lot No",
											 "external_program_code" => "Ext. Program Code",
											 "thickness" => "ความหนา",
											 "product_name" => "ชื่อสินค้า"
											);
				echo form_dropdown("searchType", $dropdown, $searchType);
			?>
			<input type="text" name="searchText" value="<?= $searchText ?>" />&nbsp;<input type="submit" name="searchButton" class="button" value="ค้นหา" />
		</td>
	</tr>
</table>
<?= form_close() ?>
<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th align="center"><a href="#" class="sort" rel="coil_group_code">Lot No</a></th>
			<th align="center"><a href="#" class="sort" rel="slit_date">Slit Date</a></th>
			<th align="center"><a href="#" class="sort" rel="external_program_code">Ext. Code</a></th>
			<th align="center"><a href="#" class="sort" rel="thickness">ความหนา</a></th>
			<th align="center"><a href="#" class="sort" rel="width">ความกว้าง</a></th>
			<th align="center"><a href="#" class="sort" rel="unit"># แถบ</a></th>
			<th align="center"><a href="#" class="sort" rel="weight">น้ำหนัก</a></th>
			<th align="center"><a href="#" class="sort" rel="product_dtl_id">Product</a></th>
		</tr>
	</thead>
	<tbody>
		<?
			$coil_lot_no = "";
			$index = 0;
			foreach($film_result as $film_item) {
				foreach($film_item as $item) {
			
				$diff = FALSE;
				if (empty($coil_lot_no) || $coil_lot_no !== $item['coil_group_code']) {
					$coil_lot_no = $item['coil_group_code'];
					$diff = TRUE;
					
					if ($index === 0) {
						$diff = FALSE;
					}
				}
		?>
		<? if ($diff === TRUE) { ?>
		<tr class="even">
			<td colspan="8">&nbsp;</td>
		</tr>
		<? } ?>
		<tr class="odd">
			<td><a href="<?= site_url("/group/group_detail/" . $this->convert->AsciiToHex($item['coil_group_code'])) ?>" class="link"><?= $item['coil_group_code']  ?></a></td>
			<td><?= mysqldatetime_to_date($item['slit_date'], 'd/m/Y') ?></td>
			<td><a href="<?= site_url("/program/program_detail/" . $this->convert->AsciiToHex($item['program_code']) . "/" . $this->convert->AsciiToHex($item['product_dtl_id'])) ?>" class="link"><?= $item['external_program_code'] ?></a></td>
			<td><?= number_format($item['thickness'], 2) ?></td>
			<td><?= number_format($item['width'], 2) ?></td>
			<td><?= number_format($item['unit'], 0) ?></td>
			<td><?= number_format($item['weight'], 0) ?></td>
			<td><?= (isset($product_result[$item['product_dtl_id']])) ? $product_result[$item['product_dtl_id']] : "&nbsp;" ?></td>
		</tr>
		<? 	$index++;
				}
			} ?>
	</tbody>
</table>
<br/>
<? if (count($film_result) == 0) { ?>
<table border="0" width="100%" class="table-data table-ui-widget">
	<tr height="40" class="ui-state-active">
		<td align="center">ไม่มีข้อมูลในส่วนนี้</td>
	</tr>
</table>
<? } ?>
<table border="0" width="100%">
	<tr>
		<td align="right"><input type="button" id="mainButton" name="mainButton" class="button mainButton" value="ย้อนกลับหน้าแรก"/></td>
	</tr>
</table>
<?= form_open("/film", array("id" => "sort_form")) ?>
<input type="hidden" name="searchType" value="<?= $searchType ?>" />
<input type="hidden" name="searchText" value="<?= $searchText ?>" />
<input type="hidden" id="sort_column" name="sort_column" value="<?= $sort_column ?>"/>
<input type="hidden" id="sort_by" name="sort_by" value="<?= $sort_by ?>" />
<?= form_close() ?>
<div id="info" title="info" style="display:none;">กรุณารอสักครู่</div>
<br/><br/><br/>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$(".mainButton").click(onMainButtonClick);
	$(".historyButton").click(onHistoryButtonClick);
	$(".minus").toggle(onMinusClick, onPlusClick).css("cursor", "pointer");
	$(".sort").click(onSortClick);
});
function onSortClick() {
	$("#info").dialog({
		modal: true
	});
	var rel = $(this).attr("rel");
	if (rel != $("#sort_column").val()) {
		$("#sort_by").val("asc");
	}
	$("#sort_column").val(rel);
	$("#sort_form").submit();
}
function onMainButtonClick() {
	document.location.replace("<?= site_url("/main") ?>");
}
function onMinusClick() {
	var ref = $(this).attr("rel");
	$(ref).hide("blind");
}
function onPlusClick() {
	var ref = $(this).attr("rel");
	$(ref).show("blind");
}
function onHistoryButtonClick() {
	document.location.replace("<?= site_url("/film/history") ?>");
}
</script>
