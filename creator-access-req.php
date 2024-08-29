<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "creator-access-req.php";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
	$loggedUserId = $_SESSION['LogdUsrDet'][1];
	include("include/connect-database.php");
	$accessReq = "";
	$accessReqDt = "";
	$accessReqCmnt = "";
	$Err="";
	$sql="SELECT `ud_creator_access`,
				 `ud_creator_access_req`,
				 `ud_req_dt`,
				 `ud_req_cmnt`,
				 `ud_first_name`,
				 `ud_last_name`,
				 `ud_contact_no`
			FROM `user_det` 
		   WHERE `ud_user_id`='".$loggedUserId."'
		 ";
	$result=mysqli_query($con, $sql);
	if($row=mysqli_fetch_array($result))
	{
		if($row['ud_creator_access']=="Y"){
			mysqli_close($con);
			header("Location: create-test.php");
		} else {
			$accessReq = $row['ud_creator_access_req'];
			$accessReqDt = $row['ud_req_dt'];
			$accessReqCmnt = $row['ud_req_cmnt'];
			if( isset($_POST['RequestAccess']) && ($_POST['RequestAccess']=="9999999") ) {
				if( ($accessReq == "N") || ($accessReq == "") || ((($accessReq == "Y") || ($accessReq == "R")) && (strtotime($accessReqDt) < strtotime('-5 days'))) ) {
					$sql99 = "UPDATE `user_det` SET `ud_creator_access_req` =  'Y', `ud_req_dt` = NOW()
							   WHERE `ud_user_id`='".$loggedUserId."'
							 ";
					if (!mysqli_query($con, $sql99)) {
						$Err="Error: Failure while requesting test creation access.";
					} else {
						$Err="Your test creation access request has been sent successfully.";
						$accessReqDt = date("Y-m-d");
						$accessReq = "Y";

						$email = $loggedUserId;		
						$to ="support@instaxam.in";
						$subject = "Access Request for Test Creation @ Instaxam.In";
						$txt = "User Details:\n\tName: ".$row['ud_first_name']." ".$row['ud_last_name']."\n\tEmail Id: ".$email."\n\tPhone Number: ".$row['ud_contact_no']." ";
						$headers = "From: ". $email;	
						mail($to,$subject,$txt,$headers);
					}
				}
			}
		}
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

    <title>Instaxam.In - Test Creation - Access Request</title>
	<link rel="icon" type="image/png" href="assets/images/favicon.ico">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/all-style.css">
    <link rel="stylesheet" href="assets/css/owl.css">

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
			
<style>
#access-req-box {
	margin-top:-70px;
}
#access-req-box button {
	margin-top: 15px;
}
@media screen and (max-width: 845px) {
	#access-req-box {
        margin-top: 40px;
	}
}
</style>
			<div id="access-req-box">
			<?php 
				if($accessReq == "Y") {
			?>
				<h4>Currently you don't have the test creation access.</h4>
				<h4>You have already raised the access request on <?php echo $accessReqDt; ?>, which is yet to be approved by the admin.</h4>
				<?php 
					if(strtotime($accessReqDt) > strtotime('-5 days')) {
				?>
				<p>The expected time limit for the approval is 5 days. You can raise another request if not approved within the specified time limit.</p>
				<?php 
					} else {
				?>
				<form name="RequestAccessForm" method="post" action="<?php echo $page; ?>" onSubmit="">
					<button name="RequestAccess" type="submit" id="form-submit" class="button" value="9999999">Request Access Again</button>
				</form>
				<?php 
					}
				?>
			<?php 
				} else if($accessReq == "R") {
			?>
				<h4>Your test creation access request has been rejected by the admin along with the following comments:</h4>
              	<p>&quot;<b style="color:red;"><?php echo $accessReqCmnt; ?></b>&quot;</p>
				<p><b>-&gt; Rejected on:</b> <?php echo $accessReqDt; ?></p>
				<?php 
					if(strtotime($accessReqDt) > strtotime('-5 days')) {
				?>
				<p>Please do the needful and try again 5 days after the rejection.</p>
				<?php 
					} else {
				?>
				<form name="RequestAccessForm" method="post" action="<?php echo $page; ?>" onSubmit="">
					<button name="RequestAccess" type="submit" id="form-submit" class="button" value="9999999">Request Access Again</button>
				</form>
				<?php 
					}
				?>
			<?php 
				} else {
			?>
				<h4>Currently you don't have the test creation access.</h4>
				<p>Please click 'Request Access' button below to get the access approval from admin.</p>
				<form name="RequestAccessForm" method="post" action="<?php echo $page; ?>" onSubmit="">
					<button name="RequestAccess" type="submit" id="form-submit" class="button" value="9999999">Request Access</button>
				</form>
			<?php 
				}
			?>
			</div>
			
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
	<?php if($Err!="") { 
	?>
		<script>
		var seco = 0;
		$('document').ready(function(){
			alert("<?php echo $Err; ?>");
			location.replace("#access-req-box");
		});
		</script>
	<?php 
	} ?>
</body>
</html>
