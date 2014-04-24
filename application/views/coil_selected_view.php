<?= form_open("/coil/slit_method", array("id" => "coil_selected_form")) ?>
<table border="0" cellpadding="2" width="100%" class="table-data ui-widget">
	<tr class="ui-widget-header">
		<td>Coil ที่ถูกเลือกทั้งหมด</td>
		<td align="right">
			<input type="button" id="backButton" name="backButton" class="button" value="เลือกเพิ่มเติม" />
			<input type="button" id="deleteButton" name="deleteButton" class="button" value="ลบ" />
			<input type="submit" name="TSUMBIT" class="button" value="ทำการ Slit" />
		</td>
	</tr>
</table>
<br/>
<div id="loading-panel" style="background-color:#FFFFCC;font-weight:bolder;padding:8px;display:none;"><?= image_asset("ajax-loader.gif") ?>&nbsp;&nbsp;Loading...</div>
<br/>
<table border="1" cellpadding="2" width="100%" class="table-data ui-widget">
	<tr>
		<td class="ui-widget-header">Slit Spec: </td>
		<td>
			<select id="slitspecification" name="slit_spec_id">
			<? for($i =0; $i < count($slit_result); $i++) { ?>
				<option value="<?= $slit_result[$i]['slit_spec_id'] ?>"><?= $slit_result[$i]['slit_detail'] ?></option>
			<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="ui-widget-header">ชื่อกลุ่ม </td>
		<td><input type="text" name="coil_group_code" value="" /></td>
	</tr>
</table>
<br/>
<table border="1" cellpadding="2" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-widget-header">
			<th width="30">&nbsp;</th>
			<th align="center">Coil ID</th>
			<th align="center">Coil No</th>
			<th align="center">ความหนา</th>
			<th align="center">ความกว้าง</th>
			<th align="center">น้ำหนัก</th>
			<th align="center">รับมาวันที่</th>
		</tr>
	</thead>
	<tbody>
		<? for($i = 0; $i < count($result); $i++) { 
				
				$checkbox = array(
					'name' => 'coil_id[]',
					'class' => 'checkbox',
					'value' => $result[$i]['coil_id'],
					'coil_id' =>$result[$i]['coil_id'],
					'coil_lot_no' => $result[$i]['coil_lot_no'],
					'po_id' => $result[$i]['po_id']
				);
		
		?>
		<tr class="<?= ($i % 2) ? "even" : "odd" ?>">
			<td align="center">
				<?= form_checkbox($checkbox) ?><?= form_hidden("coil_lot_no[]", $result[$i]['coil_lot_no']) ?><?= form_hidden("po_id[]", $result[$i]['po_id']) ?>
			</td>
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
<?= form_close() ?>
<?= form_open("/coil/selected_coil", array('id' => 'search_form')) ?>
<input type="hidden" id="search" name="search" value="" />
<input type="hidden" id="search_type" name="search_type" value="<?= $search_type ?>" />
<?= form_close() ?>
<script type="text/javascript">
var ajax = null;
$(document).ready(function(){
	$(".button").button();
	$(".search").click(onSearchClick);
	$("#backButton").click(onBackButton);
	$("#deleteButton").click(onDeleteButton);
	$("#coil_selected_form").submit(function(){
		$(".checkbox").attr("checked", "checked");
	});
});
function onBackButton() {
	document.location.replace("<?= site_url("/coil") ?>");
}
function onDeleteButton() {
	
	var param = new Array();
	$.each($(".checkbox:not(:checked)"), function(index) {
		
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
		$(".checkbox:checked").parent().parent().remove();
		$(".checkbox").removeAttr("checked");
		$("#loading-panel").hide();
	}, "json");
}
function onSearchClick() {
	var search_by = $(this).attr("rel");
	$("#search").val(search_by);
	$("#search_form").submit();
}
</script>