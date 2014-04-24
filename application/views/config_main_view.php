<table border="0" cellpadding="4" align="center">
	<tr>
		<td align="center"><button rel="<?= site_url("/config/payment") ?>">วิธีการชำระเงิน</button></td>
		<td align="center"><button rel="<?= site_url("/config/premium") ?>">ค่า Premium</button></td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/config/width") ?>">ความกว้าง</button></td>
		<td align="center"><button rel="<?= site_url("/config/thickness") ?>">ความหนา</button></td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/config/priority") ?>">ความสำคัญ</button></td>
		<td align="center">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<? if ("ADMIN" == $user_type) { ?>
	<tr>
		<!-- <td align="center"><button rel="<?= site_url("/config/reset_all") ?>">reset all</button></td>-->
		<td align="center"><button rel="<?= site_url("/config/backup_database") ?>">Backup Database</button></td>
	</tr>
	<? } ?>
	<tr>
		<td align="center" colspan="2"><button rel="<?= site_url("/main") ?>">ย้อนกลับหน้าหลัก</button></td>
	</tr>
</table>
<div id="confirm" title="คำยืนยัน" style="display:none">
คุณต้องการ reset data ทั้งหมดใช่หรือไม่
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button().css({'width' : 300, 'height' : 40}).click(function(){
		if ($(this).attr("rel")) {
			
			var rel = $(this).attr("rel");
			
			if (rel == '<?= site_url("/config/reset_all") ?>')
			{
				$("#confirm").dialog({
					modal : true,
					buttons : {
						"ตกลง" : function() {
							document.location.href= rel;
						}, 
						"ยกเลิก" : function() {
							$(this).dialog("close");
						}
					}
				});
			}
			else
			{
				document.location.href= $(this).attr("rel");
			}
		}
	});
});
</script>