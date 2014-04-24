<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr>
		<td align="center" colspan="2"><input type="button" name="all" class="button" value="ดูทั้งหมด" /></td>
	</tr>
	<? for($i = 0; $i < count($pp_result); $i++) { 
	?>
	<tr>
		<td align="center">
		<? if (isset($pp_result[$i][0])) { ?>
		<input type="button" name="b1" class="button" value="<?= form_prep($pp_result[$i][0]) ?>"/>
		<? } ?>
		</td>
		<td align="center">
		<? if (isset($pp_result[$i][1])) { ?>
		<input type="button" name="b1" class="button" value="<?= form_prep($pp_result[$i][1]) ?>"/>
		<? } ?>
		</td>
	</tr>
	<? } ?>
</table>
<?= form_open($back_page, array("id" =>"program_form")) ?>
<input type="hidden" name="searchType" value="product_name" />
<input type="hidden" id="searchText" name="searchText" value="" />
<?= form_close() ?>
<br/><br/><br/>
<style type="text/css" rel="stylesheet">
.button { width : 390px; height: 75px;}
</style>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$(".button").click(onButtonClick);
});
function onButtonClick() {
	if ($(this).attr("name") == "all")
	{
		document.location.replace("<?= site_url("/program/now_page") ?>");
	}
	else
	{
		$("#searchText").val($(this).val());
		$("#program_form").submit();
	}
}
</script>