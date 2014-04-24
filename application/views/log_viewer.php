<style type="text/css" rel="stylesheet">
#log_display{width:100%;height: 100%;}
.pleasewait{font-size:12pt;font-weight:bolder;color:black;text-align:center;}
.error{font-size:12pt;font-weight:bolder;color:red;text-align:center;}
</style>
<table border="0" width="100%" height="100%">
	<tr>
		<td valign="top" align="center" width="20%">
			<div id="system_date"></div><br/>
			<select id="log_type">
				<option value="USER">USER</option>
				<option value="SYSTEM">SYSTEM</option>
			</select><br/><br/>
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" /><br/><br/>
			<input type="button" id="backupButton" name="backupButton" class="button" value="Download log"><br/>
		</td>
			
		<td valign="top" align="left"><div id="log_display"></div></td>
	</tr>
</table>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#system_date").datepicker({
		onSelect : clickOnDatePicker,
		dateFormat : 'yy-mm-dd'
	});
	$("#backButton").click(function(){
		document.location.replace("<?= site_url("/main") ?>");
	});
	$("#backupButton").click(function(){
		var d = $("#system_date").datepicker("getDate");
		d = d.format("yyyy-mm-dd");
		
		var log_type = $("#log_type").val();
		
		document.location.replace("<?= site_url("/log/get_log/") ?>" + "/" + d + "/" + log_type);
	});
});
function clickOnDatePicker(input, inst) {
	var log_type = $("#log_type").val();
	$("#log_display").text("");
	$("#log_display").html('<div class="pleasewait">กรุณารอสักครู่</div>');
	$("#log_display").load("<?= site_url("/log/get_log_detail") ?>", {log_date : input, log_type : log_type} );
}
</script>