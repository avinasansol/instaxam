<?php
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "index.php";
	include("include/connect-database.php");
	include("include/login.php");
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Prepare for an exam with Mock Tests and Previous Year Question Papers at Instaxam.In">
    <meta name="author" content="JhaAvinash">
    <meta name="keywords" content="Instaxam, exam, exams, Prepare, mock, test, Question, Papers">

    <title>Instaxam.In - Single Place for All Exams</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/instaxam-style.css">
    <link rel="stylesheet" type="text/css" href="assets/css/login.css" />

  </head>

<body class="is-preload">
    <!-- Wrapper -->
    <div id="wrapper">

      <!-- Main -->
        <div id="main" style="background-image: url(assets/images/main-banner<?php if(isset($_SESSION['LogdUsrDet'])) { ?>-short<?php } ?>.png);background-repeat: no-repeat;background-position: center 80px; ">
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
						<form name="UserLogIn" method="post" action="https://www.instaxam.in/">
							<input type="hidden" name="Operation" value="UserLogIn" />
							<h2>User - Login</h2>
							<div>
								<input type="text" placeholder="Email Id" required="" id="username" name="txtUsrId" value="<?php if(isset($_POST['txtUsrId'])){echo $_POST['txtUsrId'];} else if(isset($_REQUEST['Email'])) { echo $_REQUEST['Email']; } else if(isset($_REQUEST['NewPassEmail'])) { echo $_REQUEST['NewPassEmail']; }?>" />
							</div>
							<div>
								<input type="password" placeholder="Password" required="" id="password" name="txtPswd" />
							</div>
							<div>
								<span id="LogErr" style="color:#FF0000; font-size:12px; font-weight:normal;"><?php if($LogErr!=""){echo $LogErr;}?></span>							</div>
							<div>
								<button type="submit" id="form-submit" class="button">Log In</button>
								<a id="forgot" href="forgot-password.php">Forgot password?</a>
								<a href="register.php">Sign Up</a>
							</div>
						</form>
					<?php
						}
						else {
					?>
						<br /><br />
						<h2>Welcome <?php echo substr($loggedUserName,0,10); ?>...!</h2>
						<div>
							<a href="user-dashboard.php">click here to go to your dashboard</a>
						</div>
						<br /><br /><br /><br />
					<?php
						}
					?>
					</section><!-- content -->
				</div>
				<!-- container -->
			</div><br /><br /><br /><br />

            <!-- Top Image -->
            <section class="top-image">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="down-content">
                      <h1>Prepare for an exam with Instaxam.In</h1>
		      <p>Welcome to Instaxam.In! You can check your performance with mock tests at Instaxam.In completely <b>free</b> of cost. Browse our tests and if you find a match with your exam, do sign up and login to take test.</p>
		      <p>If you're a teacher looking for an online platform to conduct a mock test with multiple-choice questions, we can let you create your own tests after your profile verification. Your test creation access request is just a single click away once you sign up with Instaxam.In</p>
                      <div class="primary-button">
                        <a href="https://www.instaxam.in/about-us.php">About Us</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>

            <!-- Left Image -->
            <section class="left-image">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-6">
                    <img src="assets/images/prev-paper.jpg" alt="Previous Year Question Papers">
                  </div>
                  <div class="col-md-6">
                    <div class="right-content">
                      <h2>Previous Year Question Papers</h2>
                      <p>Check previous year question papers for different exams and find out how much you could score in it.</p>
		      <p>Such an exposure to previous year examination papers can let you understand the difficulty level of the exam and help you prepare for the same.</p>
		      <p>Currently Instaxam.In provides you the previous year question papers for UPSC Civil Services Prelims Examinations. We're looking forward to expand our scope in order to make it a single place for all exams.</p>
                      <div class="primary-button">
                        <a href="browse-test/exam-sub-category-level-3/UPSCPREVYR">Previous Year Question Papers for UPSC</a><br /><br />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>

            <!-- Right Image -->
            <section class="right-image">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-6">
                    <div class="left-content">
                      <h2>Mock Tests</h2>
                      <p>Mock Tests forms an integral part of your serious preparation for any exam. It not only gives you a hands on experience with different kinds of questions but also acquaints you with real time exam hall scenario.</p>
		      <p>Mock Tests increases your problem solving ability and analytical skills. It lets you check your preparation for the exam and track your advances.</p>
		      <p>Instaxam.In lets you appear in different mock tests <b>for free</b>. You can simply browse for mock tests belonging to different categories or search for a test created by some specific creator. Details of the tests along with syllabus, question count, marks, time and creator id are shown in the description. After checking the syllabus you can take your time to prepare for the same. Once done with your preparations, you just need to sign up and login to take test.</p>
                      <div class="primary-button">
                        <a href="browse-test/exam-sub-category-level-3/UPSCMOCKTS">Mock Tests for UPSC Preliminary Exam</a>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <img src="assets/images/mock-test.jpg" alt="Mock Tests">
                  </div>
                </div>
              </div>
            </section><br /><br />


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
<?php 
	if(isset($_REQUEST['Email']))
	{
	?>
		<script type="text/javascript">
			alert("You have signed up successfully.\nYour password has been mailed to <?php echo $_REQUEST['Email'];?>\nPlease login using that password.");
		</script>
	<?php
	}
	if(isset($_REQUEST['NewPassEmail']))
	{
	?>
		<script type="text/javascript">
			alert("Your password has been updated successfully.\nThe new password has been mailed to <?php echo $_REQUEST['NewPassEmail'];?>\nPlease login using that password.");
		</script>
	<?php
	}
?>
