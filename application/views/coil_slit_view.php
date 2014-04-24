<table border="1" width="100%" class="table-data ui-widget">
	<thead>
		<tr class="ui-state-highlight">
			<th width="30" align="center">&nbsp;</th>
			<th width="30%" align="center">ความหนา (มิลลิเมตร)</th>
			<th width="30%" align="center">ความกว้าง (มิลลิเมตร)</th>
			<th width="30%" align="center">จำนวน</th>
		</tr>
	</thead>
	<tbody>
	<? for($i = 0; $i < count($result); $i++) { ?>
		<tr class="<?= ($i %2) ? "even" : "odd" ?>">
			<td>#<?= ($i +1) ?></td>
			<td><?= number_format($result[$i]['slit_thickness'], 2) ?></td>
			<td><?= number_format($result[$i]['slit_width'], 2) ?></td>
			<td><?= number_format($result[$i]['slit_qty'], 0) ?></td>
		</tr>
	<? } ?>
	</tbody>
</table>