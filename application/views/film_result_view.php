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
<form id="backForm" name="backForm" action="<?= site_url($back_page) ?>" method="POST" style="display:none;">
<?php foreach($param as $key => $value) { ?>
<input type="hidden" name="<?php echo $key; ?>" value="<?= $value ?>" />
<?php } ?>
</form>
<script type="text/javascript">
$(document).ready(function(){
	$("#backButton").click(function(){
		$("#backForm").submit();
	}).button();
});
</script>