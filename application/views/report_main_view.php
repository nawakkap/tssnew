<table border="0" width="100%" class="ui-widget">
	<tr>
		<td colspan="2" align="center"><button rel="<?= site_url("/report/production_upload") ?>" style="width: 100%;">Upload Excel</button></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/report/production_report") ?>">Production report</button></td>
		<td align="center"><button rel="<?= site_url("/report/finishgood_detail_report") ?>">Finish Goods report</button></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/report/coil_received_report") ?>">Coil Received report</button></td>
		<td align="center"><button rel="<?= site_url("/report/slit_report") ?>">Slit Report</button></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
    <tr>
    	<td align="center"><button rel="<?= site_url("/report/slit_report_by_date") ?>">Slit By Date Report</button></td>
		<td align="center"><button rel="<?= site_url("/report/performance_report") ?>">Machine's Performance Report</button></td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	 <tr>
    	<td align="center"><button rel="<?= site_url("/calendar") ?>" dir="new">Calendar</button></td>
		<td align="center">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="2" align="center"><button rel="<?= site_url("/main") ?>" style="width: 100%;">กลับสู่เมนูหลัก</button></td>
	</tr>
</table>
<style type="text/css" rel="stylesheet">
button { width: 400px; height: 75px; }
</style>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button().click(function(){
		var isNewPage = $(this).attr("dir") == "new";
		if (isNewPage) {
			window.open($(this).attr("rel"), "_blank");
		} else {
			document.location.replace($(this).attr("rel"));
		}
	});
});
</script>