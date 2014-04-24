<? if (!$edit_mode) { 
		echo form_open("user/add_method", array('id' => 'user_form'));
	} else {
		echo form_open("user/edit_method", array('id' => 'user_form'));
	}
?>
<input type="hidden" name="old_username" value="<?= $old_username ?>" />
<input type="hidden" id="permission" name="permission" value="<?= $result['permission'] ?>" />
<table border="1" width="40%" cellpadding="3" class="table-data ui-widget" align="center">
	<tr>
		<td width="40%" class="ui-widget-header">ชื่อ</td>
		<td width="60%"><input type="text" id="first_name" name="first_name" value="<?= $result['first_name'] ?>" class="validate[required]" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">นามสกุล</td>
		<td><input type="text" id="last_name" name="last_name" value="<?= $result['last_name'] ?>" class="validate[required]" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Username</td>
		<td><input type="text" id="username" name="username" value="<?= $result['username'] ?>" class="validate[required]" <?= ($edit_mode) ? 'readonly="readonly"' : '' ?>  /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Password</td>
		<td><input type="text" id="password" name="password" value="<?= $result['password'] ?>" class="validate[required]" /></td>
	</tr>
	<tr>
		<td class="ui-widget-header">Type</td>
		<td>
			<select id="user_type" name="user_type">
				<option value="user">User</option>
				<option value="admin">Administrator</option>
			</select>
		</td>
	</tr>
</table>
<br/>

<table border="1" cellpadding="5" class="table-data ui-widget" align="center">
	<tr>
		<td class="ui-widget-header" align="center" width="15%">Order</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="ORDER" /> Can Access</td>
		<td class="ui-widget-header" align="center" width="15%">Film</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="FILM" /> Can Access</td>
		<td class="ui-widget-header" align="center" width="15%">Product</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="PRODUCT" /> Can Access</td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="center">Slit Spec</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="SLIT_SPEC" /> Can Access</td>
		<td class="ui-widget-header" align="center">User</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="USER" /> Can Access</td>
		<td class="ui-widget-header" align="center">Config</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="CONFIG" /> Can Access</td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="center">Coil</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="GROUP_COIL" /> Can Access</td>
		<td class="ui-widget-header" align="center">Supplier</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="SUPPLIER" /> Can Access</td>
		<td class="ui-widget-header" align="center">Slit Coil</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="SELECT_COIL" /> Can Access</td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="center">Cancel Lot</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="CANCLE_LOT" /> Can Access</td>
		<td class="ui-widget-header" align="center">Report</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="REPORT" /> Can Access</td>
		<td class="ui-widget-header" align="center">Program</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="PROGRAM" /> Can Access</td>
	</tr>
	<tr>
		<td class="ui-widget-header" align="center">Group Detail</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="GROUP_DETAIL" /> Can Access</td>
		<td class="ui-widget-header" align="center">Machine Config</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="MACHINE_CONFIG" /> Can Access</td>
		<td class="ui-widget-header" align="center">Calendar</td>
		<td><input type="checkbox" name="my_permission" class="permission_check" value="CALENDAR" /> Can Access</td>
	</tr>
</table>
<br/>
<table border="0" width="75%" align="center">
	<tr>
		<td align="center">
			<input type="button" id="submitButton" name="TSUBMIT" class="button" value="ตกลง" />
			<? if (!$edit_mode) { ?>
			<input type="reset" id="clearButton" name="TCLEAR" class="button" value="ล้าง" />
			<? } ?>
			<input type="button" id="cancelButton" name="TCANCEL" class="button" value="ยกเลิก" onclick='onCancelButtonClick()' />
		</td>
	</tr>
</table>
<?= form_close() ?>
<script type="text/javascript">
$(document).ready(function(){
	$(".button").button();
	$("#cancelButton").click(onCancelButtonClick);
	$(".permission_check").click(onPermissionCheck);
	$("#user_type").change(onUserTypeChange);
	$("#user_type").val("<?= $result['user_type']  ?>");
	$("#user_form").validationEngine({
		scroll:false, 
		promptPosition : "centerRight",
		inlineValidation: false,
	});
	check_default_permission();
	
	$("#first_name").select();
	$("input, select").keypress(changeControlFocus);
	$("#submitButton").click(function(){ $("#user_form").submit(); });
});
function changeControlFocus(event) {
	if (event.keyCode == 13) {
		if ($(this).val() == "GROUP") {
			$("#first_name").select();
		} else {
			$(this).focusNextInputField();
		}
	}
}
function check_default_permission() {
	if ($("#user_type").val() == "admin") {
		onUserTypeChange();
	} else {
		var perm = $("#permission").val();
		var split = perm.split(" ");
		for(var i= 0; i < split.length;i++) {
			$(".permission_check[value=" + split[i] + "]").attr("checked", true);
		}
	}
}
function onCancelButtonClick(){ 
	document.location.replace("<?= site_url("/user") ?>");
}
function onUserTypeChange() {
	if ("admin" == $("#user_type").val()) {
		$(".permission_check").attr("checked", "checked");
	} else {
		$("#permission").val("");
		$(".permission_check").removeAttr("checked");
	}
	onPermissionCheck();
}
function onPermissionCheck() {
	var perm = "";
	var check_all = true;
	$.each($(".permission_check"), function(index) {
		if ($(this).attr("checked") == true) {
			perm = perm + " " + $(this).val();
		} else {
			check_all= false;
		}
	});
	
	if (check_all == false) {
		$("#user_type").val("user");
	}
	
	perm = $.trim(perm);
	$("#permission").val(perm);
}
</script>