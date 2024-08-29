<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "account-setting.php";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
	$loggedUserId = $_SESSION['LogdUsrDet'][1];
	if($loggedUserId!="support@instaxam.in")
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

    <title>Instaxam.In - Delete Temporary Files</title>
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
            <section class="tables" style="margin-top:-80px;">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h2>Deleting temporary files.... </h2>
                    </div>
                    <div class="alternate-table">
                      <?php 
                        if($loggedUserId=="support@instaxam.in")
                        {
                          $files = glob('ques-file-temp/*');
                          $fileCount = 0;
                          foreach($files as $file){
                            if(is_file($file)) {
                              $fileCount++;
                              echo "<p> Deleting... ".substr($file,15,strlen($file))." -->";
                              if(unlink($file)){
                                echo " success!";
                              } else {
                                echo " failurre!";
                              }
                              echo "</p>";
                            }
                          }
                          if($fileCount>0){
                        ?>
                          <p>All temporary files processed.</p>
                        <?php
                          } else {
                        ?>
                          <p>No temporary file found.</p>
                        <?php
                          }
                        }
                      ?>
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
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/transition.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>