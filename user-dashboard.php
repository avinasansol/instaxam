<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "user-dashboard.php";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
	include("include/connect-database.php");
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">

    <title>Instaxam.In - User Dashboard</title>
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

            <!-- Services -->
			<style>.col-md-4{cursor:pointer;}</style>
            <section class="services">
              <div class="container-fluid">
                <div class="row">
                
                <?php 
                    $sql1 = "SELECT `tm_id`
                                FROM `task_mgr`
                                WHERE `tm_user_id`='".$loggedUserId."'
                                LIMIT 0, 1
                            ";
                    $result1 = mysqli_query($con, $sql1);
                    if($row1 = mysqli_fetch_array($result1))
                    {
                ?>
                  <div class="col-md-4" onClick="location.replace('task-tracker.php')">
                    <div class="service-item first-item">
                      <div class="icon"></div>
                      <h4><a href="task-tracker.php">Task Tracker</a></h4>
                      <p><a href="task-tracker.php">Manage your tasks and track your daily activities.</a></p>
                    </div>
                  </div>
                <?php 
                    } else if($_SESSION['LogdUsrDet'][1]=="support@instaxam.in")
                    {
                ?>
                  <div class="col-md-4" onClick="location.replace('delete-all-temp-files.php')">
                    <div class="service-item first-item">
                      <div class="icon"></div>
                      <h4><a href="delete-all-temp-files.php">File Deletion</a></h4>
                      <p><a href="delete-all-temp-files.php">Delete all temporary files from the server.</a></p>
                    </div>
                  </div>
                <?php 
                    } else {
                ?>
                  <div class="col-md-4" onClick="location.replace('create-test.php')">
                    <div class="service-item first-item">
                      <div class="icon"></div>
                      <h4><a href="create-test.php">Test Creation</a></h4>
                      <p><a href="create-test.php">Create your own test and let others participate in the same.</a></p>
                    </div>
                  </div>
                <?php 
                    }
                ?>
                  
                  <div class="col-md-4" onClick="location.replace('upsc-reference-materials.php')">
                    <div class="service-item second-item">
                      <div class="icon"></div>
                      <h4><a href="upsc-reference-materials.php">UPSC Reference Materials</a></h4>
                      <p><a href="upsc-reference-materials.php">Check you progress for UPSC Civil Sevices examination.</a></p>
                    </div>
                  </div>
                  
                  <div class="col-md-4" onClick="location.replace('account-setting.php')">
                    <div class="service-item third-item">
                      <div class="icon"></div>
                      <h4><a href="account-setting.php">Account Setting</a></h4>
                      <p><a href="account-setting.php">Provide your account details and change your password.</a></p>
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
  <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/transition.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
