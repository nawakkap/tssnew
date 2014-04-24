<?= form_open("/login/login_method", array('id' => 'login_form')) ?>
<center><b style="color:red"><?= $error_message ?></b></center><br/>
<table border="0" align="center" cellpadding="6" class="table-data ui-widget">
	<tr>
		<td colspan="2" class="ui-widget-header">Login panel</td>
	</tr>
	<tr>
		<td>Username</td>
		<td align="right"><input type="text" id="username" name="username" value="" autocomplete="off"/></td>
	</tr>
	<tr>
		<td>Password</td>
		<td align="right"><input type="password" id="password" name="password" value="" autocomplete="off" /></td>
	</tr>
	<tr>
		<td align="center" colspan="2" nowrap="nowrap">
			<!--<input type="button" id="autoSigninButton" name="autoSigninButton" class="button" value="Auto-Signin" /><br/>--><br/>
			<input type="submit" name="TSUBMIT" class="button" value="เข้าสู่ระบบ" />
			<input type="reset" name="TRESET" class="button" value="ล้าง" />
		</td>
	</tr>
</table>
<table border="0" align="center" cellpadding="6" class="ui-widget">
	<tr>
		<td>version 1.1</td>
	</tr>
</table>
<?= form_close() ?>
<div id="information"></div>
<div id="pleasewait" style="display:none">กรุณารอสักครู่</div>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#username").select();
	$("#login_form").submit(onFormSubmit);
	$("input:reset").click(onFormReset);
	$("#autoSigninButton").click(onAutoSigninButton);
	$("#information").dialog({
		modal : true,
		autoOpen : false
	});
});
function onFormSubmit() {
	if (!$("#username").val()) {
		$("#information").text("กรุณาใส่ Username").dialog("open");
		return false;
	}
	if (!$("#password").val()) {
		$("#information").text("กรุณาใส่ รหัสผ่าน").dialog("open");
		return false;
	}
	$("#pleasewait").dialog({modal: true});
	return true;
}
function onFormReset() {
	$("#username").select();
	return true;
}
function onAutoSigninButton(){
	$("#username").val("autthapon");
	$("#password").val("notenote");
	$("#login_form").submit();
}
</script>