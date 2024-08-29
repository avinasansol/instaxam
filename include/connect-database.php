<?php
	$con = mysqli_connect("${HOST}","${USER}","${PASSWORD}!");
	if (!$con)
	{
		die('Could not connect: ' . mysql_error());
	}
	mysqli_select_db($con, "${DB}");
?>
