<table border="0" align="center" cellpadding="5">
	<tr>
		<td align="center"><button rel="<?= site_url("/order") ?>">ส่วนจัดการ Order</button></td>
		<td align="center"><button rel="<?= site_url("/select_coil") ?>">ส่วนนำ Coil ไป Slit</button></td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/film") ?>">Film Summary</button></td>
		<td align="center"><button rel="<?= site_url("/program/now_page") ?>">ส่วนจัดการโปรแกรมการผลิต</button></td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/machine_manage") ?>">ส่วนจัดการเวลาการทำงาน</button></td>
		<!-- <td align="center"><button rel="<?= site_url("/film/film_planning") ?>">Production Planning</button></td> -->
		<td align="center"><button rel="<?= site_url("/report") ?>">Report</button></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/slit") ?>">ระบบ Slit Spec</button></td>
		<td align="center"><button rel="<?= site_url("/product") ?>">ส่วนจัดการ Product</button></td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/user") ?>">ระบบจัดการ User</button></td>
		<td align="center"><button rel="<?= site_url("/supplier") ?>">ระบบจัดการ Supplier</button></td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/machine") ?>">ระบบจัดการ Machine</button></td>
		<td align="center"><button rel="<?= site_url("/config") ?>">ส่วนจัดการ ค่าของระบบ ต่างๆ</button></td>
	</tr>
	<tr>
		<td align="center"><button rel="<?= site_url("/group") ?>">ส่วนจัดการยกเลิก Lot</button></td>
		<td align="center"></td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<button rel="<?= site_url("login/logout_method") ?>">ออกจากระบบ</button>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
<br/><br/><br/>
<div id="order" title="PO ID" style="display:none;">
<table border="0" width="100%">
	<tr><td>PO ID</td><td><input type="text" id="po_id" name="po_id" value="" /></td></tr>
</table>
</div>
<script type="text/javascript">
$(document).ready(function(){
	$("button").button().css({'width' : 400, 'height' : 75}).click(function(){
		if ($(this).attr("rel")) {
			document.location.href= $(this).attr("rel");
		}
	});
	$("#order_received").click(onCoilButtonClick);
	$("#po_id").keyup(onPOIDKeyUp);
});
function onPOIDKeyUp(event) {
	if (event.keyCode == 13) {
		if ($("#po_id").val() == "") {
			$("#order").dialog("close");
		} else {
			changePage();
		}
	}
}
function onCoilButtonClick() {
	$("#order").dialog({
		modal: true,
		buttons : {
			"ปิด" : function () {
				$(this).dialog("close");
			},
			"ตกลง" : function() {
				changePage();
			}
		},
		open: function() {
			$("#po_id").val('').select();
		}
	});
}
function changePage() {
	if ($.trim($("#po_id").val())) {
		document.location.href="<?= site_url('/order/order_detail/') ?>" + "/" + DoAsciiHex($.trim($("#po_id").val()), "A2H");
	}
}
</script>