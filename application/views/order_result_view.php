<table border="0" align="center" class="table-data ui-widget" width="50%">
	<tr class="ui-state-hover" height="100">
		<td align="center"><?= $result ?></td>
	</tr>
	<tr>
		<td align="center">
			<input  type="button" id="backButton" name="backButton" value="ตกลง"/>
		</td>
	</tr>
</table>
<script type="text/javascript">
$(document).ready(function(){
	$("#backButton").click(function(){
		document.location.replace("<?= site_url($back_page) ?>");
	}).button();
});
</script>