<?php 
	include("include/connect-database.php");
	$testId = "";
	$Err = "";
	if(isset($_POST['TestId'])){
		$testId = $_POST['TestId'];
		$sql="SELECT `ud_creator_access` 
				FROM `user_det` 
			   WHERE `ud_user_id`='".$loggedUserId."'
			 ";
		$result=mysqli_query($con, $sql);
		if($row=mysqli_fetch_array($result))
		{
			if($row['ud_creator_access']!="Y"){
				$Err = "You don't have creator access any more.";
			} else {
				if(isset($_POST['changeAct'])){
					$sql="SELECT `ed_status` 
							FROM `exam_det` 
						   WHERE `ed_created_by`='".$loggedUserId."'
							 AND `ed_exam_id` = '".$testId."'
						 ";
					$result=mysqli_query($con, $sql);
					if($row=mysqli_fetch_array($result)) {
						if($_POST['changeAct']=="D") {
							if($row['ed_status']=="D"){
								$Err = "The test has already been deactivated.";
							} else {
								$sql="UPDATE `exam_det` SET `ed_status` = 'D'
									   WHERE `ed_created_by`='".$loggedUserId."'
										 AND `ed_exam_id` = '".$testId."'
									 ";
								if(mysqli_query($con, $sql)){
									$Err = "The test has been deactivated now.";
								} else {
									$Err = "Failure updating status.";
								}
							}
						} else if($_POST['changeAct']=="A") {
							if($row['ed_status']=="A"){
								$Err = "The test is already activated.";
							} else {
								$quesCount = 0;
								$sql1 = "SELECT COUNT(`qd_ques_no`) AS `max_ques_no`
										   FROM `ques_det`
										  WHERE `qd_exam_id` = '".$testId."'
											AND `qd_del_ind` != 'Y'
										 ";
								$result1=mysqli_query($con, $sql1);
								if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
								{
									$quesCount = (int)$row1['max_ques_no'];
								}
								if($quesCount == 0) {
									$Err = "The test can not be activated as there are no questions added to the test.";
								} else if( ($quesCount%5) != 0) {
									$Err = "The test can not be activated as the questions count is not a multiple of 5.";
								} else {
									$sql="UPDATE `exam_det` SET `ed_status` = 'A'
										   WHERE `ed_created_by`='".$loggedUserId."'
											 AND `ed_exam_id` = '".$testId."'
										 ";
									if(mysqli_query($con, $sql)){
										$Err = "The test is activated now.";
									} else {
										$Err = "Failure updating status.";
									}
								}
							}
						}
					} else {
						$Err = "Failure updating status: Invalid test id.";
					}
				}
			}
		}
	}
	mysqli_close($con);
?>