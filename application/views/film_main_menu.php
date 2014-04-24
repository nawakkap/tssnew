<table border="0" width="100%" class="ui-widget" cellpadding="5">
	<tr>
		<td><input type="button" class="button" rel="<?php echo site_url("film") ?>" value="เพิ่มข้อมูล" /></td>
		<td><input type="button" class="button" rel="<?php echo site_url("film/edit_film") ?>" value="แก้ไขข้อมูล" /></td>
	</tr>
	<tr>
		<td colspan="2"><input type="button" class="button" rel="<?php echo site_url("main") ?>" value="กลับสู่หน้าแรก" /></td>
	</tr>
</table>
<script type="text/javascript">
$(document).ready(function() {
	$(".button").button().css('width', "100%").css("height", "75px").click(function() {
		document.location.href= $(this).attr("rel");
	});
});
</script>