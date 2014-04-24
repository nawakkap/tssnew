<table border="0" width="100%" class="table-data ui-widget">
	<tr>
		<td align="center" class="ui-state-active"><br/>คุณไม่มีสิทธิ์เข้าใช้งานในส่วนนี้<br/><br/><br/></td>
	</tr>
	<tr>
		<td align="center"><br/>
			<input type="button" id="backButton" name="backButton" class="button" value="ย้อนกลับ" />
		</td>
	</tr>
</table>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#backButton").click(onBackButtonClick);
});
function onBackButtonClick() { 
	document.location.replace("<?= $refer ?>");
}
</script>