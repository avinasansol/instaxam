<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$pageFolder = "browse-test";
	include("include/connect-database.php");
	include("include/login.php");
	
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Reference Materials for UPSC Civil Services Exam">
    <meta name="author" content="JhaAvinash">
    <meta name="keywords" content="Instaxam, Syllabus, Test, UPSC">

    <title>Instaxam.In - Syllabus &amp; References for UPSC Civil Services Exam</title>
    <link rel="icon" type="image/png" href="https://www.instaxam.in/assets/images/favicon.ico">

    <link rel="stylesheet" href="https://www.instaxam.in/assets/css/instaxam-style.css">
    <link rel="stylesheet" href="https://www.instaxam.in/assets/css/test-category.css">

<script type="text/javascript" src="https://www.instaxam.in/assets/js/jquery-1.3.2.js" ></script>

  </head>

<body class="is-preload">

    <!-- Wrapper -->
    <div id="wrapper">

      <!-- Main -->
        <div id="main">
          <div class="inner">

			<?php 
				include("include/header.php");
                if( (isset($_POST['refbkid'])) && (isset($_POST['compltnstat'])) )
                {
                  if(!isset($_SESSION['LogdUsrDet'])) {
                    echo "<script>alert('Please login to update status.');</script>";
                  } else
                  {
                      $sql4 = "SELECT `srs_status`
                                 FROM `syl_ref_stat`
                                WHERE `srs_sr_id` = '".$_POST['refbkid']."'
                                  AND `srs_usr_id` = '".$loggedUserId."'
                              ";
                      $result4=mysqli_query($con, $sql4);
                      if($row4=mysqli_fetch_array($result4, MYSQLI_ASSOC))
                      {
                          if($row4['srs_status']!=$_POST['compltnstat']){
                              if(($_POST['compltnstat']=="Y") || ($_POST['compltnstat']=="N")){
                                  $sql2 = "UPDATE `syl_ref_stat` 
                                              SET `srs_status` = '".$_POST['compltnstat']."'
                                          WHERE `srs_sr_id`='".$_POST['refbkid']."'
                                            AND `srs_usr_id` = '".$loggedUserId."'
                                          ";
                                  if(!mysqli_query($con, $sql2)){
                                      $err = "Failure while updating status.";
                                  }
                              } else if($_POST['compltnstat']==""){
                                  $sql2 = "DELETE FROM `syl_ref_stat` 
                                           WHERE `srs_sr_id`='".$_POST['refbkid']."'
                                            AND `srs_usr_id` = '".$loggedUserId."'
                                          ";
                                  if(!mysqli_query($con, $sql2)){
                                      $err = "Failure while updating status.";
                                  }
                              }
                          }
                      } else {
                          $sql4 = "SELECT `sr_id`
                                     FROM `syl_ref`
                                    WHERE `sr_id` = '".$_POST['refbkid']."'
                                  ";
                          $result4=mysqli_query($con, $sql4);
                          if($row4=mysqli_fetch_array($result4, MYSQLI_ASSOC))
                          {
                              $sql2 = "INSERT INTO `syl_ref_stat` (`srs_id`, `srs_sr_id`, `srs_usr_id`, `srs_status`) 
                                       VALUES (NULL, '".$_POST['refbkid']."', '".$loggedUserId."', '".$_POST['compltnstat']."');";
                              if(!mysqli_query($con, $sql2)){
                                $err = "Failure while updating status.";
                              }
                          }
                      }
                  }
                }
			?>

            <section class="tables" id="browse-category">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h2>Reference Materials for UPSC Civil Services Exam:</h2>
                    </div>
                    <div class="alternate-table">
					
						<div id="browseCatLoadArea">
                        
                            <?php 
                                $sql1 = "SELECT `ses_id`,
                                                `ses_sub`
                                           FROM `syl_exam_sub`
                                          WHERE `ses_sub_cat2_id`='CNTJOBUPSC'
                                       ORDER BY `ses_id`
                                        ";
                                $result1 = mysqli_query($con, $sql1);
                          		$subCnt = 0;
                                while($row1 = mysqli_fetch_array($result1))
                                {
                                  	$subCnt++;
                                	echo "<p>".$subCnt.") <strong>".$row1['ses_sub']."</strong></p><ul>";
                                    $sql2 = "SELECT `srt_type`
                                               FROM `syl_ref_type`
                                           ORDER BY `srt_id`
                                            ";
                                    $result2 = mysqli_query($con, $sql2);
                                    while($row2 = mysqli_fetch_array($result2))
                                    {
                                        $sql3 = "SELECT `sr_id`,
                                        				`sr_ref`
                                                   FROM `syl_ref`
                                          		  WHERE `sr_sub_id`= '".$row1['ses_id']."'
                                                    AND `sr_type` = '".$row2['srt_type']."'
                                               ORDER BY `sr_id`
                                                ";
                                        $result3 = mysqli_query($con, $sql3);
                          				$refCnt = 0;
                                        while($row3 = mysqli_fetch_array($result3))
                                        {
                                          	$refCnt++;
                                          	if($refCnt==1){
                                          		echo "<li> <strong>".$row2['srt_type'].": </strong><ul>";
                                            }
                                          
                                          	$taskComplt = "";
                                            if(isset($_SESSION['LogdUsrDet']))
                                            {
                                                $sql4 = "SELECT `srs_status`
                                                           FROM `syl_ref_stat`
                                                          WHERE `srs_sr_id` = '".$row3['sr_id']."'
                                                            AND `srs_usr_id` = '".$loggedUserId."'
                                                        ";
                                                $result4=mysqli_query($con, $sql4);
                                                if($row4=mysqli_fetch_array($result4, MYSQLI_ASSOC))
                                                {
                                                  	$taskComplt = $row4['srs_status'];
                                                }
                                            }
                                          
                                          	echo "<li><table><tr><td";
                                          	if($taskComplt == "Y"){
                                          		echo " style='background-color:#00FF00;'";
                                            } else if($taskComplt == "N"){
                                          		echo " style='background-color:#FFA500;'";
                                            }
                                          	echo ">".$row3['sr_ref']."";
                                            echo "</td><td style='width:25%;'>";
                                            if(isset($_SESSION['LogdUsrDet']))
                                            {
                                              	?>
                          						<form method="post" action="<?php echo $page;?>">
                                                  <input type="hidden" name="refbkid" value="<?php echo $row3['sr_id'];?>" />
                                                  <label for="compltnstat">Complete? </label>
                                                  <select name="compltnstat" onchange="this.form.submit()">
                                                      <?php if($taskComplt=="Y") { ?>
                                                      <option value="Y">Yes</option>
                                                      <option value="N">No</option>
                                                      <option value="">NA</option>
                                                      <?php } else if($taskComplt=="N") { ?>
                                                      <option value="N">No</option>
                                                      <option value="Y">Yes</option>
                                                      <option value="">NA</option>
                                                      <?php } else { ?>
                                                      <option value="">NA</option>
                                                      <option value="Y">Yes</option>
                                                      <option value="N">No</option>
                                                      <?php } ?>
                                                  </select>
                      							</form>
                          						<?php 
                                            } else {
                                              	?>
                          						<form method="post" action="<?php echo $page;?>">
                                                  <input type="hidden" name="refbkid" value="<?php echo $row3['sr_id'];?>" />
                                                  <label for="compltnstat">Complete? </label>
                                                  <select name="compltnstat" onchange="this.form.submit()">
                                                      <option value="">NA</option>
                                                      <option value="Y">Yes</option>
                                                      <option value="N">No</option>
                                                  </select>
                      							</form>
                          						<?php 
                                            }
                                          	echo "</td></tr></table></li>";
                                        }
                                        if($refCnt>0){
                                          	echo "</ul></li>";
                                        }
                                    }
                                	echo "</ul>";
                                }
                          ?>



                          
						</div>

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
