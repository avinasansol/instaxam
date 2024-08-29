<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$loggedUserId = "";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		$loggedUserId = $_SESSION['LogdUsrDet'][1];
		header("Location: index.php");
	}
	if(!isset($_POST['TestNumber']))
	{
		header("Location: index.php");
	}
	include("include/connect-database.php");
	
	$correctCount = 0;
	$inCorrectCount = 0;
	$skippedCount = 0;
	$totalMarks = 0;
	$marksObt = 0;
	$questionsCount = 0;
	$totalMarks = 0;
	$marksPerQues = 0;
	$examId = "";
	$description = "";
	$examTakenOn = "";
	$examTakenOnTS = "";
	$selectedExamNo = $_POST['TestNumber'];
	
	$sqlUserResults = "  SELECT `exam_taken`.`et_exam_no`,
								`exam_taken`.`et_exam_id`,
								`exam_det`.`ed_exam_desc`,
								`exam_taken`.`et_end_ts`,
								`exam_taken`.`et_marks`
						   FROM `exam_det`, `exam_taken`
						  WHERE `exam_taken`.`et_exam_no` = '".$selectedExamNo."'
						    AND `exam_det`.`ed_exam_id` = `exam_taken`.`et_exam_id`
					  ";
	$result1=mysqli_query($con, $sqlUserResults);
	if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{
		if(strlen($row1['ed_exam_desc'])>200) {
			$description = substr($row1['ed_exam_desc'],0,200).".......";
		} else {
			$description = $row1['ed_exam_desc'];
		}
		$examTakenOnTS = $row1['et_end_ts'];
		$examTakenOn = substr($row1['et_end_ts'],0,10);
		$examId = $row1['et_exam_id'];
		$sql99 = "SELECT `ed_marks_per_ques` 
				   FROM `exam_det`
				  WHERE `exam_det`.`ed_exam_id` = '".$examId."'
				 ";
		$result99=mysqli_query($con, $sql99);
		if($row99=mysqli_fetch_array($result99, MYSQLI_ASSOC))
		{
			$marksPerQues = (int)$row99['ed_marks_per_ques'];
		}
		
		$sql99 = "SELECT COUNT(`ques_det`.`qd_ques_no`) AS `ques_count` 
				   FROM `ques_det`
				  WHERE `ques_det`.`qd_exam_id` = '".$examId."'
					AND `qd_del_ind` != 'Y'
				 ";
		$result99=mysqli_query($con, $sql99);
		if($row99=mysqli_fetch_array($result99, MYSQLI_ASSOC))
		{
			$questionsCount = (int)$row99['ques_count'];
		}
		
		$totalMarks = ( $questionsCount * $marksPerQues );
		
		$marksObt = (float)$row1['et_marks'];
		$sql99 = "   SELECT COUNT(`ans_det`.`ad_ques_no`) AS `correct_ans_count`
					   FROM `ques_det`, `ans_det`, `exam_taken`
					  WHERE `exam_taken`.`et_exam_no` = '".$selectedExamNo."'
						AND `ans_det`.`ad_taken_exam_no` = `exam_taken`.`et_exam_no`
						AND `ques_det`.`qd_exam_id` = `exam_taken`.`et_exam_id`
						AND `ans_det`.`ad_ques_no` = `ques_det`.`qd_ques_no`
						AND `qd_del_ind` != 'Y'
						AND ( (`ans_det`.`ad_ans_opt` IS NOT NULL) OR (`ans_det`.`ad_ans_opt` != '') )
						AND ( `ans_det`.`ad_ans_opt` = `ques_det`.`qd_correct_opt` )
						";
		$result99=mysqli_query($con, $sql99);
		if($row99=mysqli_fetch_array($result99, MYSQLI_ASSOC))
		{
			$correctCount = (int)$row99['correct_ans_count'];
		}
		
		$sql99 = "   SELECT COUNT(`ans_det`.`ad_ques_no`) AS `incorrect_ans_count`
					   FROM `ques_det`, `ans_det`, `exam_taken`
					  WHERE `exam_taken`.`et_exam_no` = '".$selectedExamNo."'
						AND `ans_det`.`ad_taken_exam_no` = `exam_taken`.`et_exam_no`
						AND `ques_det`.`qd_exam_id` = `exam_taken`.`et_exam_id`
						AND `ans_det`.`ad_ques_no` = `ques_det`.`qd_ques_no`
						AND `qd_del_ind` != 'Y'
						AND ( (`ans_det`.`ad_ans_opt` IS NOT NULL) OR (`ans_det`.`ad_ans_opt` != '') )
						AND ( `ans_det`.`ad_ans_opt` != `ques_det`.`qd_correct_opt` )
				";
		$result99=mysqli_query($con, $sql99);
		if($row99=mysqli_fetch_array($result99, MYSQLI_ASSOC))
		{
			$inCorrectCount = (int)$row99['incorrect_ans_count'];
		}
		
		$skippedCount = ($questionsCount - ($correctCount + $inCorrectCount));
		
	} else {
		mysqli_close($con);
		header("Location: index.php");
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

    <title>Instaxam.In - Test Result</title>
	<link rel="icon" type="image/png" href="assets/images/favicon.ico">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/all-style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/test-results.css">

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
				<form action="test-results.php" method="post" onSubmit="return false">
					<div id="download-box">
						<button type="submit" name="TestNumber" value="<?php echo $selectedExamNo; ?>" onClick="downloadSolution()">Download Solution</button>
					</div>
				</form>
				<div id="result-table">
                    <h2>Test Result for <?php echo $description; ?> taken on <?php echo $examTakenOn; ?>:</h2>
					<table>
					<thead>
						<tr>
							<th>Total Questions</th>
							<th>Correct Attempts</th>
							<th>Incorrect Attempts</th>
							<th>Not Attempted</th>
							<th>Total Marks</th>
							<th>Marks Scored</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?php echo $questionsCount; ?></td>
							<td><?php echo $correctCount; ?></td>
							<td><?php echo $inCorrectCount; ?></td>
							<td><?php echo $skippedCount; ?></td>
							<td><?php echo $totalMarks; ?></td>
							<td><?php echo $marksObt; ?></td>
						</tr>
					</tbody>
					</table>
				<?php 
					$quesNo = 0;
					$sql1 = "SELECT `ques_det`.`qd_ques_no`,
									`ques_det`.`qd_ques_desc`,
									`ques_det`.`qd_opt_a`,
									`ques_det`.`qd_opt_b`,
									`ques_det`.`qd_opt_c`,
									`ques_det`.`qd_opt_d`,
									`ques_det`.`qd_soln`,
									`ques_det`.`qd_correct_opt`
							   FROM `ques_det`
							  WHERE `ques_det`.`qd_exam_id` = '".$examId."'
								AND `qd_del_ind` != 'Y'
							  ORDER BY `ques_det`.`qd_ques_no` ASC
							 ";
					$result1=mysqli_query($con, $sql1);
					while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{
						$quesNo++;
						echo "<h4>Question No: ".$quesNo."</h4>";
						echo "<p class='top-break-para'>".str_replace("\n","</p><p>",preg_replace( "/[\r\n]+/", "\n", $row1['qd_ques_desc']))."</p><p class='top-break-para'>";
						echo "(A) ".$row1['qd_opt_a'];
						echo "</p><p>";
						echo "(B) ".$row1['qd_opt_b'];
						echo "</p><p>";
						echo "(C) ".$row1['qd_opt_c'];
						echo "</p><p>";
						echo "(D) ".$row1['qd_opt_d'];
						$yourAns = "";
						$sql2 = "SELECT `ad_ans_opt`
								   FROM `ans_det`
								  WHERE `ad_taken_exam_no` = '".$selectedExamNo."'
									AND `ad_ques_no` = '".$row1['qd_ques_no']."'
								";
						$result2=mysqli_query($con, $sql2);
						if($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
						{
							if($row2['ad_ans_opt'] != ""){
                                $yourAns = $row2['ad_ans_opt'];
							}
						} 
						if($yourAns == ""){
                            echo "</p><p class='top-break-para'>";
						} else if($yourAns==$row1['qd_correct_opt']){
                            echo "</p><p class='top-break-para' style='width:150px; background: url(assets/images/tck-crs.png) no-repeat;background-position: 102px 8px !important;'>";
						} else  if($yourAns!=$row1['qd_correct_opt']){
                            echo "</p><p class='top-break-para' style='width:150px; background: url(assets/images/tck-crs.png) no-repeat;background-position: 102px -53px !important;'>";
						} 
						echo "Your Answer: ";
                        echo $yourAns;
                        echo "</p><p>";
						echo "Correct Answer: ";
						echo $row1['qd_correct_opt'];
						echo "</p><p class='top-break-para'>";
						echo "<b>Answer Justification:</b> ".str_replace("\n","</p><p>",preg_replace( "/[\r\n]+/", "\n", $row1['qd_soln']));
						echo "</p>";
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
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/transition.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/custom.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
	<script>
    function downloadSolution() {
        var pdf = new jsPDF('p', 'pt', 'letter');
        source = $('#result-table')[0];
        specialElementHandlers = {
            '#bypassme': function (element, renderer) {
                return true
            }
        };
        margins = {
            top: 80,
            bottom: 60,
            left: 40,
            width: 522
        };
        pdf.fromHTML(
        source, 
        margins.left, 
        margins.top, { 
            'width': margins.width, 
            'elementHandlers': specialElementHandlers
        },

        function (dispose) {
            pdf.save('Test_Result_<?php echo str_replace("__","_",str_replace("__","_",str_replace("-","_",str_replace(" ","_",$description)))); ?>_<?php echo $examTakenOn; ?>.pdf');
        }, margins);
    }
 </script>
</body>
</html>
