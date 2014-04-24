<table border="0" width="100%">	
	<tr>
		<td align="left">
			<input type="button" id="clearAllButton" name="clearAllButton" class="button" value="ยกเลิกทั้งหมด" />
			<input type="button" id="viewSelectedButton" name="viewSelectedButton" class="button" value="รายที่เลือกแล้ว" />
		</td>
		<td align="center">
			<span id="loading-panel" style="background-color:#FFFFCC;font-weight:bolder;padding:8px;display:none;"><?= image_asset("ajax-loader.gif") ?>&nbsp;&nbsp;Loading...</span>
		</td>
		<td align="right" valign="bottom">
			<div id="radio" style="text-align:right">
				<?
					$data = array(
						"name" => "filterby",
						"id" => "radio1",
						"value" => "show_all",
						"checked" => ("show_all" == $filter_by)
					);
					
					echo form_radio($data) . form_label('Show All', 'radio1');
					
					$data = array(
						"name" => "filterby",
						"id" => "radio3",
						"value" => "search_by_po_id",
						"checked" => ("search_by_po_id" == $filter_by)
					);
					
					echo form_radio($data) . form_label('Search by PO ID', 'radio3');
				
				
				?>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="3" align="right" valign="bottom" class="remark-text">* ความหนา หน่วย มิลลิเมตร และ น้ำหนัก หน่วย กิโลกรัม</td>
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
		
				$key = $result[$i]->coil_id . $result[$i]->coil_lot_no . $result[$i]->po_id;
				
				$checked =  FALSE;
				if (isset($selected_list[$key])) {
					$checked = TRUE;
				}
				
				$checkbox = array(
					'name' => 'coil_id',
					'class' => 'checkbox',
					'value' => $result[$i]->coil_id,
					'coil_id' =>$result[$i]->coil_id,
					'coil_lot_no' => $result[$i]->coil_lot_no,
					'po_id' => $result[$i]->po_id,
					'checked' => $checked
				);
		
		?>
		<tr class="<?= ($i % 2) ? "even" : "odd" ?>">
			<td align="center"><?= form_checkbox($checkbox) ?></td>
			<td><a href="<?= site_url("/coil/coil_detail/" . $this->convert->AsciiToHex($result[$i]->coil_id) . "/" . $result[$i]->coil_lot_no . "/" . $this->convert->AsciiToHex($result[$i]->po_id)) ?>" class="link"><?= $result[$i]->coil_id ?></a></td>
			<td><?= $result[$i]->coil_lot_no ?></td>
			<td><?= number_format($result[$i]->thickness,2) ?></td>
			<td><?= number_format($result[$i]->width, 2) ?></td>
			<td><?= number_format($result[$i]->weight, 0) ?></td>
			<td><?= mysqldatetime_to_date($result[$i]->coil_received_date , 'd/m/Y') ?></td>
		</tr>
		<? } ?>
	</tbody>
</table>
<table border="0" width="100%">
	<tr>
		<td align="right"><input type="button" id="mainButton" name="mainButton" class="button" value="ย้อนกลับสู่หน้าหลัก" /></td>
	</tr>
</table>
<?= form_open("/coil", array('id' => 'search_form')) ?>
<input type="hidden" id="search" name="search" value="" />
<input type="hidden" id="search_type" name="search_type" value="<?= $search_type ?>" />
<?= form_close() ?>
<?= form_open("/coil", array("id" => "search_po_id_form")) ?>
<input type="hidden" id="filterby" name="filterby" value="search_by_po_id" />
<input type="hidden" id="my_po_id" name="po_id" value="" />
<?= form_close() ?>
<div id="search-panel" title="Search By PO ID">
<table width="100%" border="0">
	<tr>
		<td>PO ID</td>
		<td><input type="text" id="search_text" name="po_id" value=""/></td>
	</tr>
</table>
</div>
<div id="pleasewait-panel" style="display:none">โปรดรอสักครู่</div>
<script type="text/javascript">
var selected = 0;
var ajax = null;
$(document).ready(function(){
	$(".button").button();
	$("#radio").buttonset();
	$("#mainButton").click(onMainButtonClick);
	$(".search").click(onSearchClick);
	$(".checkbox").click(onCoilClick);
	$("#clearAllButton").click(onClearAllButton);
	$("#viewSelectedButton").click(onViewButton);
	$("#search-panel").dialog({
		modal: true,
		autoOpen: false,
		open: function() {
			$("#search_text").select();
		},
		buttons : {
			"ตกลง" : function() {
				if ($.trim($("#search_text").val()) == "") {
					$("#search_text").val("");
					$(this).dialog("close");
					return ;
				}
				onSearchByPOIDClick();
			},
			"ปิด" : function() {
				$("#radio1").next().attr("aria-pressed", "true").addClass("ui-state-active");
				$("#radio3").next().removeAttr("aria-pressed").removeClass("ui-state-active");
				$("#search_text").val("");
				$(this).dialog("close");
			}
		}
	});
	$("#radio1").click(onRadioShowAllClick);
	$("#radio3").click(onRadioSearchClick);
});
function onMainButtonClick(){
	document.location.replace("<?= site_url("/main") ?>");
}
function onViewButton() {
	document.location.replace("<?= site_url("/coil/selected_coil") ?>");
}
function onRadioShowAllClick() {
	$("#filterby").val("show_all");
	$("#pleasewait-panel").dialog({
		modal: true
	});
	$("#search_po_id_form").submit();
}
function onRadioSearchClick() {
	$("#search-panel").dialog("open");
	$("#filterby").val("search_by_po_id");
}
function onSearchByPOIDClick() {
	var val = $("#search_text").val();
	$("#my_po_id").val(val);
	$("#pleasewait-panel").dialog({
		modal : true
	});
	$("#search_po_id_form").submit();
	
}
function onSearchClick() {
	var search_by = $(this).attr("rel");
	$("#search").val(search_by);
	$("#search_form").submit();
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