<!-- <table> -->
	<!-- <tr>
		<td>手机号</td>
		<td>地址</td> -->
	<?php
	/*
	foreach ($sale_count as $row) {
		echo "<td>".substr($row['createDate'], 5)."</td>";
	} ?>
	</tr>
	<tr>
		<td>数</td>
		<td>量</td>
	<?php
	foreach ($sale_count as $row) {
		echo "<td>{$row['c_sale_id']}</td>";
	}
	?>
	</tr>
	<tr>
		<td>总</td>
		<td>量</td>
	<?php
	foreach ($money as $row) {
		echo "<td>".$row."</td>";
	}
	?>
	</tr>
	<tr>
		<td>人数</td>
		<td>增量</td>
	<?php
	foreach ($daily_inc as $row) {
		echo "<td>".$row."</td>";
	}
	?>
	</tr>
	<?php
	echo "<tr>";
	$old = "";
	foreach($tel_sale_detail as $row) {
		if ($old != $row['user_l_tel']) {
			echo "</tr><tr><td>{$row['user_l_tel']}</td><td>{$row['user_addr']}</td>";
			$old = $row['user_l_tel'];
			$all_ready_print = 0;
		}
		$day = 0;
		foreach ($sale_count as $row_2) {
			$day++;
			if ($row_2['createDate'] == $row['createDate'])
				break;
		}
		for ($i = $day - $all_ready_print; $i > 1; $i--) {
			$all_ready_print++;
			echo "<td>0</td>";
		}
		echo "<td>{$row['c_sale_id']}</td>";
		$all_ready_print++;
	}
	echo "</tr>";
	*/
?>
<!-- </table> -->
<div class='m_right'>
<?php
	foreach ($pic as $row) { ?>
		<h5><?=$row['title']?></h5>
		<img src="<?=$row['path']?>" />
	<?php
	}
?>

</div>
</div>
</div>
