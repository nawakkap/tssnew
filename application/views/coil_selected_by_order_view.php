<table border="0" width="100%">
	<tr>
		<td align="left">
			<input type="button" id="clearAllButton" name="clearAllButton" class="button" value="ยกเลิกทั้งหมด" />
			<input type="button" id="viewSelectedButton" name="viewSelectedButton" class="button" value="รายที่เลือกแล้ว" />
		</td>
		<td align="center">
			<span id="loading-panel" style="background-color:#FFFFCC;font-weight:bolder;padding:8px;display:none;"><?= image_asset("ajax-loader.gif") ?>&nbsp;&nbsp;Loading...</span>
		</td>
		<td align="right" valign="bottom">&nbsp;</td>
	</tr>
</table>
<table border="0" width="100%" cellpadding="3" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>Search By PO Id : <?= $po_id ?></td>
	</tr>
</table>
<table border="1" width="100%" cellpadding="3" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="30">&nbsp;</th>
			<th align="center"><a href="#" class="search" rel="coil_id">Coil ID</a></th>
			<th align="center"><a href="#" class="search" rel="coil_lot_no">Coil No</a></th>
			<th align="center"><a href="#" class="search" rel="thickness">ความหนา</a></th>
			<th align="center"><a href="#" class="search" rel="width">ความกว้าง</a></th>
			<th align="center"><a href="#" class="search" rel="weight">น้ำหนัก</a></th>
			<th align="center"><a href="#" class="search" rel="coil_received_date">รับมาวันที่</a></th>
		</tr>
	</thead>
	<tbody>
		<? for($i = 0; $i < count($result); $i++) { 
		
				$key = $result[$i]['coil_id'] . $result[$i]['coil_lot_no'] . $result[$i]['po_id'];
				
				$checked =  FALSE;
				if (isset($selected_list[$key])) {
					$checked = TRUE;
					unset($selected_list[$key]);
				}
				
				$checkbox = array(
					'name' => 'coil_id',
					'class' => 'checkbox',
					'value' => $result[$i]['coil_id'],
					'coil_id' =>$result[$i]['coil_id'],
					'coil_lot_no' => $result[$i]['coil_lot_no'],
					'po_id' => $result[$i]['po_id'],
					'checked' => $checked
				);
		
		?>
		<tr class="<?= ($i % 2) ? "even" : "odd" ?>">
			<td align="center"><?= form_checkbox($checkbox) ?></td>
			<td><a href="<?= site_url("/coil/coil_detail/" . $this->convert->AsciiToHex($result[$i]['coil_id']) . "/" . $result[$i]['coil_lot_no'] . "/" . $this->convert->AsciiToHex($result[$i]['po_id'])) ?>" class="link"><?= $result[$i]['coil_id'] ?></a></td>
			<td><?= $result[$i]['coil_lot_no'] ?></td>
			<td><?= number_format($result[$i]['thickness'],2) ?></td>
			<td><?= number_format($result[$i]['width'], 2) ?></td>
			<td><?= number_format($result[$i]['weight'], 0) ?></td>
			<td><?= mysqldatetime_to_date($result[$i]['coil_received_date'] , 'd/m/Y') ?></td>
		</tr>
		<? } ?>
	</tbody>
</table>
<?
// For Selected The Other Coil
foreach($selected_list as $key => $value) {
	
	$checkbox = array(
		'name' => 'coil_id',
		'class' => 'checkbox hiddencheckbox',
		'value' => $value['coil_id'],
		'coil_id' =>$value['coil_id'],
		'coil_lot_no' => $value['coil_lot_no'],
		'po_id' => $value['po_id'],
		'checked' => TRUE
	);
	
	echo form_checkbox($checkbox);
	
}
?>
<script type="text/javascript">
$(document).ready(function(){
	$(".checkbox").click(onCoilClick);
	$("#viewSelectedButton").click(onViewButton);
	$("#clearAllButton").click(onClearAllButton);
	$(".button").button();
	$(".hiddencheckbox").hide();
});
function onViewButton() {
	document.location.replace("<?= site_url("/coil/selected_coil") ?>");
}
function onClearAllButton() {
	$("#loading-panel").show();
	$.post("<?= site_url("/coil/clear_selected") ?>" , function(res) {
		$(".checkbox").removeAttr("checked");
		$("#loading-panel").hide();
	}, "json");
}
function onCoilClick() {

	var param = new Array();
	$.each($(".checkbox:checked"), function(index) {
		
		var coil_id = $(this).attr("coil_id");
		var coil_lot_no = $(this).attr("coil_lot_no");
		var po_id = $(this).attr("po_id");	
	
		var data = {
			"coil_id" : coil_id,
			"coil_lot_no" : coil_lot_no,
			"po_id" : po_id
		};	
		
		param[index] = $.toJSON(data);
		
	});
	
	$("#loading-panel").show();
	try { 
		if (window.console) window.console.info("Query Abort.") 
		ajax.abort(); 
	} catch(e) {}
	ajax = $.post("<?= site_url("/coil/add_to_selected") ?>" , {"p" : $.toJSON(param) }, function(res) {
			$("#loading-panel").hide();
	}, "json");
}
</script>