<?php
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "register.php";
	if(isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
	include("include/connect-database.php");
	include("include/login.php");
	$Err="please provide your ";
	if(isset($_POST['FormName']))
	{
		if((!isset($_POST['FirstName']))||($_POST['FirstName']=="")||($_POST['FirstName']==null))
		{
			$Err=$Err."first name, ";
		}
		if((!isset($_POST['LastName']))||($_POST['LastName']=="")||($_POST['LastName']==null))
		{
			$Err=$Err."last name, ";
		}
		if((!isset($_POST['Email']))||($_POST['Email']=="")||($_POST['Email']==null))
		{
			$Err=$Err."email, ";
		}
		if((!isset($_POST['ConEmail']))||($_POST['ConEmail']=="")||($_POST['ConEmail']==null))
		{
			$Err=$Err."confirm email, ";
		}
		if((!isset($_POST['PhNo']))||($_POST['PhNo']=="")||($_POST['PhNo']==null))
		{
			$Err=$Err."phone number, ";
		}
		if($Err!="please provide your ")
		{
			$Err[(strlen($Err)-2)]=".";
		}
		else if(!(preg_match('/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]{2,4})$/', $_POST['Email'])))
		{
			$Err="please provide a valid email id.";
			$_POST['Email'] = "";
			$_POST['ConEmail'] = "";
		}
		else if( (!preg_match('/^[0-9]{1,}$/', preg_replace('/'.preg_quote('+', '/').'/', '', str_replace("-", "", $_POST['PhNo']), 1))) || (strlen($_POST['PhNo']) < 10) || (strlen($_POST['PhNo']) > 14) )
		{
			$Err="please provide a valid phone number.";
			$_POST['PhNo'] = "";
		}
		else if($_POST['Email']!=$_POST['ConEmail'])
		{
			$Err="confirm email id doesn't match with email id.";
			$_POST['Email'] = "";
			$_POST['ConEmail'] = "";
		}
		else
		{
			$sql="SELECT `ud_user_id` FROM `user_det` WHERE `ud_user_id`='".$_POST['Email']."'";
			$result=mysqli_query($con, $sql);
			if($row=mysqli_fetch_array($result))
			{
				$Err="The email id '".$_POST['Email']."' is already registered with us.";
			}
			else
			{
				function gen_pass($len = 8)
				{
					return substr(md5(rand().rand()), 0, $len);
				}
				$pass=gen_pass();
				$firstName = mysqli_real_escape_string($con, substr(htmlentities($_POST['FirstName']),0,70));
				$lastName = mysqli_real_escape_string($con, substr(htmlentities($_POST['LastName']),0,20));
				$email = mysqli_real_escape_string($con, substr(htmlentities(str_replace(' ', '', $_POST['Email'])),0,70));
				$phNo = mysqli_real_escape_string($con, substr(htmlentities(str_replace(' ', '', $_POST['PhNo'])),0,15));
				
				$sql="INSERT INTO `user_det` (`ud_user_id`, `ud_creator_access`, `ud_password`, `ud_new_password`, `ud_first_name`, `ud_last_name`, `ud_gender`, `ud_dob`, `ud_country`, `ud_contact_no`, `ud_reg_ts`, `ud_update_ts`, `ud_last_login_ts`) VALUES ('".$email."', 'N', NULL, '".$pass."', '".$firstName."', '".$lastName."', NULL, NULL, NULL, '".$phNo."', current_timestamp(), NULL, NULL)";
				mysqli_query($con, $sql);
	
				$to = $email;
				$subject = "REGISTRATION @ Instaxam.In";
				$txt = "Hi ".$_POST['FirstName']."! \n\nWe are glad to inform you that you have been added to the users list of Instaxam.In. We would like to invite you to login and test your preparations for different examinations.\n\nYour email id ".$_POST['Email']." will serve as your username and your initial password will be ".$pass."\n\nYou can change your password after loging in.\n\nBest Wishes,\nInstaxam.In";
				$headers = "From: support@instaxam.in";	
				mail($to,$subject,$txt,$headers);

				$to ="support@instaxam.in";
				$subject = "REGISTRATION @ Instaxam.In";
				$txt = "User Details:\n\tName: ".$firstName." ".$lastName."\n\tEmail Id: ".$email."\n\tPhone Number: ".$phNo." ";
				$headers = "From: ". $email;	
				mail($to,$subject,$txt,$headers);

				header("Location: index.php?Email=".$email);
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
    <meta name="description" content="Register with Instaxam.In to get free and unlimted access online tests.">
    <meta name="author" content="JhaAvinash">
    <meta name="keywords" content="Instaxam, Registration, Register, registered, Tests, sign up, free">

    <title>Instaxam.In - Registration</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/instaxam-style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/login.css" />

  </head>

<body class="is-preload">
    <!-- Wrapper -->
    <div id="wrapper">

      <!-- Main -->
        <div id="main" style="background-image: url(assets/images/main-banner-long.png);background-repeat: no-repeat;background-position: center 80px; ">
          <div class="inner">
            <?php 
				include("include/header.php");
			?>

			<div id="login-box">
				<div class="container">
					<section id="content">
					<?php
						if(!isset($_SESSION['LogdUsrDet']))
						{
					?>
						<form name="SignUpForm" action="register.php" method="post">
							<input type="hidden" name="FormName" value="SignUpForm" />
							<h2>Sign Up</h2>
							<div>
								<input type="text" placeholder="First Name" required="" id="firstname" name="FirstName" value="<?php if(isset($_POST['FirstName'])){echo $_POST['FirstName'];}?>" />
							</div>
							<div>
								<input type="text" placeholder="Last Name" required="" id="lastname" name="LastName" value="<?php if(isset($_POST['LastName'])){echo $_POST['LastName'];}?>" />
							</div>
							<div>
								<input type="text" placeholder="Email Id" required="" id="email" name="Email" value="<?php if(isset($_POST['Email'])){echo $_POST['Email'];}?>" />
							</div>
							<div>
								<input type="text" placeholder="Confirm E-Mail" required="" id="cemail" name="ConEmail" value="<?php if(isset($_POST['ConEmail'])){echo $_POST['ConEmail'];}?>" />
							</div>
							<div>
								<input type="text" placeholder="Phone Number" required="" id="phn" name="PhNo" value="<?php if(isset($_POST['PhNo'])){echo $_POST['PhNo'];}?>" />
							</div>
							<?php if($Err!="please provide your ") { echo "<div><span id='SignUpErr'>".$Err."</span></div>"; } ?>
							<div>
								<button type="submit" id="form-submit" class="button">Sign Up</button>
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
                      <h1>Registration @ Instaxam.In:</h1>
                      <p>Want to take test? or Looking for a platfrom to create online multiple-choice mock tests? </p>
                      <p>Register with Instaxam.In to get free and unlimted access online tests.</p>
                      <p>If you're a teacher looking for an online platform to conduct a mock test with multiple-choice questions, we can let you create your own tests after your profile verification. Your test creation access request is just a single click away once you sign up with Instaxam.In</p>
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
	<?php if($Err!="please provide your ") { 
	?>
		<script>
		$('document').ready(function(){
			alert("<?php echo $Err; ?>");
			location.replace("#SignUpErr");
		});
		</script>
	<?php 
	} ?>
</body>
</html>
