<?php
	session_start();
	if(isset($_SESSION['LogdUsrDet']))
	{
		unset($_SESSION['LogdUsrDet']);
		session_destroy();
	}
	if(!isset($_POST['CurrPage'])) {
		header("Location: index.php");
	} else {
		header("Location: ".htmlspecialchars($_REQUEST['CurrPage']));
	}
?>