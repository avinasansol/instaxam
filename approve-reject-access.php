<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$loggedUserId = "";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
	$loggedUserId = $_SESSION['LogdUsrDet'][1];
	include("include/connect-database.php");
	$Err="";
	if(isset($_POST['UserId']) && isset($_POST['TestId']) && isset($_POST['ActType']))
	{
		include("include/access-approve.php");
	}
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">

    <title>Instaxam.In - Approve/Reject Access</title>
	<link rel="icon" type="image/png" href="assets/images/favicon.ico">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/all-style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/test-history.css">

  </head>

<body class="is-preload">

    <!-- Wrapper -->
    <div id="wrapper">

      <!-- Main -->
        <div id="main">
          <div class="inner">

			<?php 
				include("include/header.php");
			?>
			
			<?php 
			include("include/access-req-list.php");
			?>
			
          </div>
		  
		  <?php 
			include("include/footer.php");
		  ?>

        </div>
		<?php 
			include("include/sidebar.php");
		?>
    </div>

  <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/transition.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/custom.js"></script>
	<?php if($Err !="") { 
	?>
		<script>
		$('document').ready(function(){
			alert("<?php echo $Err; ?>");
		});
		</script>
	<?php 
	} ?>

</body>
</html>
