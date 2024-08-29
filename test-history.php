<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$loggedUserId = "";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		$loggedUserId = $_SESSION['LogdUsrDet'][1];
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

    <title>Instaxam.In - Tests History</title>
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
			
				<div id="history-table">
                    <h2>Tests History:</h2>

					<?php 
					$selectedTestNumber = 0;
					$sql1 = "SELECT `exam_taken`.`et_exam_no`,
									`exam_det`.`ed_exam_id`,
									`exam_det`.`ed_exam_desc`,
									`exam_taken`.`et_end_ts`,
									`exam_taken`.`et_start_ts`,
									`exam_taken`.`et_marks`,
									`exam_det`.`ed_marks_per_ques`
							   FROM `exam_det`,
									`exam_taken`
							  WHERE `exam_det`.`ed_exam_id` = `exam_taken`.`et_exam_id`
								AND `exam_taken`.`et_user_id` ='".$loggedUserId."'
							  ORDER BY `exam_taken`.`et_start_ts` DESC
							 ";
					$result1=mysqli_query($con, $sql1);
					$rowCount1 = 0;
					while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{
						$rowCount1++;
						$selectedTestNumber = (int)$row1['et_exam_no'];
						$testID = $row1['ed_exam_id'];
						$description = $row1['ed_exam_desc'];
						$examTakenOn = substr($row1['et_end_ts'],0,10);
						$examStartedOn = substr($row1['et_start_ts'],0,10);
						$marks = (float)$row1['et_marks'];
						$marksPerQues = (int)$row1['ed_marks_per_ques'];
						$questionsCount = 0;
						$totalMarks = 0;
						$sql6 = "SELECT COUNT(`ques_det`.`qd_ques_no`) AS `ques_count` 
								   FROM `ques_det`
								  WHERE `ques_det`.`qd_exam_id` = '".$row1['ed_exam_id']."'
									AND `qd_del_ind` != 'Y'
								 ";
						$result6=mysqli_query($con, $sql6);
						if($row6=mysqli_fetch_array($result6, MYSQLI_ASSOC))
						{
							$questionsCount = (int)$row6['ques_count'];
						}
						$totalMarks = ( $questionsCount * $marksPerQues );
						
						if($rowCount1==1){ 
							echo "<table><thead><tr><th>TestID</th><th class='test-desc'>Description</th><th>Date</th><th>Marks</th><th></th></tr></thead><tbody>";
						}
						?>
						<tr>
							<td><a href='test-details.php?test_id=<?php echo $testID; ?>'><?php echo $testID; ?></a></td>
							<td class='test-desc'><?php if(strlen($description)>100) { echo substr($description,0,100)."......."; } else { echo $description; } ?></td>
							<?php 
							if($examTakenOn==""){ 
							?>
								<td style="white-space:nowrap;"><?php echo $examStartedOn; ?></td>
								<td>Incomplete</td>
								<td colspan="2">
									<form action="take-test.php" method="post" onSubmit="">
										<input type="hidden" name="TestId" value="<?php echo $testID; ?>" />
										<button type="submit">Resume Test</button>
									</form>
								</td>
							<?php 
							} else {
							?>
								<td style="white-space:nowrap;"><?php echo $examTakenOn; ?></td>
								<td><?php echo $marks; ?>/<?php echo $totalMarks; ?></td>
								<td>
									<form action="test-results.php" method="post">
										<button type="submit" name="TestNumber" value="<?php echo $selectedTestNumber; ?>">View Results</button>
									</form>
								</td>
							<?php 
							}
							?>
						</tr>
						<?php
					}
					if($rowCount1>0){ 
						echo "</tbody></table>";
					} else {
						echo "<p>You are yet to take a test.</p>";
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
</body>
</html>
