<table border="0" width="100%" class="ui-widget">
	<tr>
		<td align="center"><button rel="<?= site_url("/report/production_upload") ?>">Upload Excel</button></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/report/production_report") ?>">Production report</button></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/report/finishgood_detail_report") ?>">Finish Goods report</button></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/main") ?>">กลับสู่เมนูหลัก</button></td>
	</tr>
</table>
<style type="text/css" rel="stylesheet">
button { width: 500px; height: 50px; }
</style>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button().click(function(){
		document.location.replace($(this).attr("rel"));
	});
});
</script>