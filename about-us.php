<?php
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "about-us.php";
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
    <meta name="description" content="Instaxam.In - About Us - Single Platform for Multiple Online Tests">
    <meta name="author" content="JhaAvinash">
    <meta name="keywords" content="Instaxam, Prepare, exam, Online, tests, free">

    <title>Instaxam.In -  About Us</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/instaxam-style.css">

  </head>

<body class="is-preload">
    <!-- Wrapper -->
    <div id="wrapper">

      <!-- Main -->
        <div id="main" style="background-image: url(assets/images/Instaxam.In-background.png);">
          <div class="inner">
            <?php 
		include("include/header.php");
	    ?>

            <!-- Top Image -->
            <section class="top-image" style="background-color: rgba(255,255,255,0.9); padding: 50px;">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="down-content">
                      <h1>Prepare for an exam with Instaxam.In</h1>
				      <p>Welcome to Instaxam.In! You can check your performance with mock tests at Instaxam.In completely <b>free</b> of cost. Browse our tests and if you find a match with your exam, do sign up and login to take test and prepare well.</p>
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

</body>
</html>
