<?php 
	$examId = "";
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	if(isset($_SESSION['LogdUsrDet']))
	{
		$loggedUserId = $_SESSION['LogdUsrDet'][1];
		if(isset($_POST['TestId']))
		{
			$examId = $_POST['TestId'];
			include("connect-database.php");
			
			$sql1 = "SELECT `exam_det`.`ed_exam_id`
					   FROM `exam_det`
					  WHERE `exam_det`.`ed_exam_id` ='".$examId."'
					 ";
			$result1=mysqli_query($con, $sql1);
			if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
			{
				$sql3 = "SELECT `et_exam_no`,
								`et_time_spent`
						   FROM `exam_taken`
						  WHERE `et_exam_id` = '".$examId."'
							AND `et_user_id` = '".$loggedUserId."'
							AND `et_end_ts` IS NULL
						";
				$result3=mysqli_query($con, $sql3);
				if($row3=mysqli_fetch_array($result3, MYSQLI_ASSOC))
				{
					$sql9 = "UPDATE `exam_taken` SET `et_time_spent` =  ADDTIME(`et_time_spent`, '00:00:01')"
						   ." WHERE `et_exam_id` = '".$examId."'" 
						   ."   AND `et_user_id` = '".$loggedUserId."'" 
						   ."   AND `et_end_ts` IS NULL" 
						   ." ";
					if (!mysqli_query($con, $sql9)) {
						echo "Error: Failure while updatnig time.";
					}
				}
				
			}
			mysqli_close($con);
		}
	}
?>