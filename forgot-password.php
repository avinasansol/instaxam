<?php
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "forgot-password.php";
	if(isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
	include("include/connect-database.php");
	include("include/login.php");
	$Err="please provide your ";
	if(isset($_POST['FormName']))
	{
		if((!isset($_POST['Email']))||($_POST['Email']=="")||($_POST['Email']==null))
		{
			$Err=$Err."email id";
		}
		else if(!(preg_match('/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]{2,4})$/', $_POST['Email'])))
		{
			$Err="please provide a valid email id.";
			$_POST['Email'] = "";
			$_POST['ConEmail'] = "";
		}
		else
		{
			$sql="SELECT `ud_first_name` FROM `user_det` WHERE `ud_user_id`='".$_POST['Email']."'";
			$result=mysqli_query($con, $sql);
			if(!$row=mysqli_fetch_array($result))
			{
				$Err="The email id '".$_POST['Email']."' is not registered with us.";
			}
			else
			{
				function gen_pass($len = 8)
				{
					return substr(md5(rand().rand()), 0, $len);
				}
				$pass=gen_pass();
				$email = mysqli_real_escape_string($con, substr(htmlentities(str_replace(' ', '', $_POST['Email'])),0,70));
				
				$sql="UPDATE `user_det` SET `ud_new_password` = '".$pass."'
					   WHERE `ud_user_id` = '".$email."'
					 ";
				mysqli_query($con, $sql);
	
				$to = $email;
				$subject = "CHANGE OF PASSWORD @ Instaxam.In";
				$txt = "Hi ".$row['ud_first_name']."! \n\nYour password has been updated @ Instaxam.In. Your email id ".$_POST['Email']." will serve as your username and your new password will be ".$pass."\n\nYou can change your password after loging in.\nPlease login and test your preparation for different examinations.\n\nBest Wishes,\nInstaxam.In";
				$headers = "From: support@instaxam.in";	
				mail($to,$subject,$txt,$headers);

				header("Location: index.php?NewPassEmail=".$email);
			}
		}
	}
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Auto generated a new password and get it on your registered email id.">
    <meta name="author" content="JhaAvinash">
    <meta name="keywords" content="Instaxam, Password, Recovery, registered, auto generated">

    <title>Instaxam.In - Password Recovery</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/instaxam-style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/login.css" />

  </head>

<body class="is-preload">
    <!-- Wrapper -->
    <div id="wrapper">

      <!-- Main -->
        <div id="main" style="background-image: url(assets/images/main-banner-short.png);background-repeat: no-repeat;background-position: center 80px; ">
          <div class="inner">
            <?php 
				include("include/header.php");
			?>

			<div id="login-box">
				<div class="container" style="">
					<section id="content">
					<?php
						if(!isset($_SESSION['LogdUsrDet']))
						{
					?>
						<form name="SignUpForm" action="forgot-password.php" method="post">
							<input type="hidden" name="FormName" value="ForgotPasswordForm" />
							<h2>Forgot Password?</h2>
							<div>
								<input type="text" placeholder="Email Id" required="" id="email" name="Email" value="<?php if(isset($_POST['Email'])){echo $_POST['Email'];}?>" />
							</div>
							<?php if($Err!="please provide your ") { echo "<div><span id='SignUpErr'>".$Err."</span></div>"; } ?>
							<div>
								<button type="submit" id="form-submit" class="button">Send New Password</button>
							</div>
							<div id="auto-msg">
								<span>
									An auto generated password will be mailed to the above email id.
									Further, you can change your password once you login.
								</span>
							</div>
						</form>
					<?php
						}
					?>
					</section><!-- content -->
				</div><!-- container -->
			</div><br />

            <!-- Top Image -->
            <section class="top-image">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="down-content">
                      <h1>Recovery of Your Password for Instaxam.In:</h1>
                      <p>Forgot your password?</p>
                      <p>There is no issue with its recovery. Just enter your email id registered with Instaxam.In and we will send an auto generated new password there.</p>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            
            
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
    <script src="https://www.instaxam.in/assets/js/jquery.min-bootstrap.bundle.min.js"></script>
    <script src="https://www.instaxam.in/assets/js/instaxam-all-script.js"></script>
</body>
</html>
	<?php if($Err!="please provide your ") { 
	?>
		<script>
		var seco = 0;
		$('document').ready(function(){
			alert("<?php echo $Err; ?>");
			location.replace("#SignUpErr");
		});
		</script>
	<?php 
	} ?>
