<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$loggedUserId = "";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
	$loggedUserId = $_SESSION['LogdUsrDet'][1];
	$Err = "";
	include("include/change-test-status.php");
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

    <title>Instaxam.In - List of Created Tests</title>
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
                    <h2>Tests Created by You:</h2>

					<?php 
					$status = "";
					$sql1 = "SELECT `exam_det`.`ed_exam_id`,
									`exam_det`.`ed_status`,
									`exam_det`.`ed_created_on`,
									`exam_det`.`ed_exam_desc`
							   FROM `exam_det`
							  WHERE `exam_det`.`ed_created_by` ='".$loggedUserId."'
							  ORDER BY `exam_det`.`ed_created_on` DESC
							 ";
					$result1=mysqli_query($con, $sql1);
					$rowCount1 = 0;
					while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{
						$rowCount1++;
						$testID = $row1['ed_exam_id'];
						$status = $row1['ed_status'];
						$description = $row1['ed_exam_desc'];
						$examCreatedOn = substr($row1['ed_created_on'],0,10);
						$questionsCount = 0;
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
						
						if($rowCount1==1){ 
							echo "<table><thead><tr><th>TestID</th><th class='test-desc'>Description</th><th>Creation Date</th><th class='test-desc'>Question Count</th><th class='test-desc'>Status</th><th></th><th></th></tr></thead><tbody>";
						}
						?>
						<tr>
							<td><a href='test-details.php?test_id=<?php echo $testID; ?>'><?php echo $testID; ?></a></td>
							<td class='test-desc'><?php if(strlen($description)>100) { echo substr($description,0,100)."......."; } else { echo $description; } ?></td>
							<td style="white-space:nowrap;"><?php echo $examCreatedOn; ?></td>
							<td class='test-desc'><?php echo $questionsCount; ?></td>
							<td class='test-desc'><?php if($status == "A"){ echo "Active"; } else { echo "Deactive"; }?></td>
							<td align="center">
								<form action="<?php echo $page; ?>" method="post" onSubmit="return changeTestStatus(<?php if($status == "A"){ echo "'D'"; } else { echo "'A'"; }?>)" >
									<input type="hidden" name="changeAct" value="<?php if($status == "A"){ echo "D"; } else { echo "A"; }?>" />
									<button type="submit" style="width:90px;" name="TestId" value="<?php echo $testID; ?>"><?php if($status == "A"){ echo "Deactivate"; } else { echo "Activate"; }?></button>
								</form>
							</td>
							<td>
								<form action="edit-test.php" method="post"<?php if($status == "A"){?> onSubmit="return editTest()"<?php }?>>
									<button type="submit" style="width:90px;" name="TestId" value="<?php echo $testID; ?>">Edit Test</button>
								</form>
							</td>
						</tr>
						<?php
					}
					if($rowCount1>0){ 
						echo "</tbody></table>";
					} else {
						echo "<p>You are yet to create a test.</p>";
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
	<?php if($Err !="") { 
	?>
		<script>
		$('document').ready(function(){
			alert("<?php echo $Err; ?>");
		});
		</script>
	<?php 
	} ?>
<script>
function editTest(){
	if (confirm('The test will be deactivated!\nThis may force some users to submit incomplete test.\nAre you sure you want to edit test?')) {
		return true;
	}
	return false;
};
function changeTestStatus(changeAct){
	if(changeAct=='D'){
		if (!confirm('This may force some users to submit incomplete test.\nAre you sure you want to deactivate the test?')) {
			return false;
		}
	}
	return true;
};
</script>

</body>
</html>
