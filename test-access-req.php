<?php 
	$loggedUserId = "";
	$message = "";
	session_start();
	if(isset($_SESSION['LogdUsrDet']))
	{
		$loggedUserId = $_SESSION['LogdUsrDet'][1];
		$examId = "";
		if(isset($_POST['TestId']))
		{
			$examId = $_POST['TestId'];
			include("include/connect-database.php");
			$availableFor = "";
			$availableForUser = 0;
			$sql1 = "SELECT `exam_det`.`ed_avlbl_for`
					   FROM `exam_det`
					  WHERE `exam_det`.`ed_exam_id` ='".$examId."'
					 ";
			$result1=mysqli_query($con, $sql1);
			if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
			{
				if($row1['ed_avlbl_for'] == "A") {
					$availableFor = "A";
					$message = "The test is available for all users.";
				} else if($row1['ed_avlbl_for'] == "S") {
					$availableFor = "S";
					$sql2 = "SELECT `eaf_avlblty`, 
									`eaf_ts`
							   FROM `exam_avlbl_for`
							  WHERE `eaf_exam_id` = '".$examId."'
								AND `eaf_user_id` = '".$loggedUserId."'
							";
					$result2=mysqli_query($con, $sql2);
					if($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
					{
						if($row2['eaf_avlblty'] == "Y") {
							$availableForUser = 1;
							$message = "The test is already available for you.";
						} else if( ($row2['eaf_avlblty'] == "N") || ($row2['eaf_avlblty'] == "R") ) {
							$availableForUser = 0;
							$accessReqDt = substr($row2['eaf_ts'],0,10);
							if(strtotime($accessReqDt) < strtotime('-1 days')){
								$sql3 = "UPDATE `exam_avlbl_for`
											SET `eaf_avlblty` = 'N', 
												`eaf_ts` = current_timestamp()
										  WHERE `eaf_exam_id` = '".$examId."'
											AND `eaf_user_id` = '".$loggedUserId."'
										";
								if (mysqli_query($con, $sql3)) {
									$message = "Another access request sent.";
                                    $sql5="SELECT `ud_first_name`,
                                                                `ud_last_name`,
                                                                `ud_contact_no`
                                                    FROM `user_det` 
                                                WHERE `ud_user_id`='".$loggedUserId."'
                                            ";
                                    $result5=mysqli_query($con, $sql5);
                                    if($row5=mysqli_fetch_array($result5))
                                    {
                                        $email = $loggedUserId;
                                        $subject = "Test Access Request @ Instaxam.In";
                                        $txt = "User Details:\n\tName: ".$row5['ud_first_name']." ".$row5['ud_last_name']."\n\tEmail Id: ".$email."\n\tPhone Number: ".$row5['ud_contact_no']."\n\n";
                                        $sql6="SELECT `ed_exam_desc`,
                                                                    `ed_created_by`,
                                                                    `ed_created_on`
                                                        FROM `exam_det` 
                                                    WHERE `ed_exam_id`='".$examId."'
                                                ";
                                        $result6=mysqli_query($con, $sql6);
                                        if($row6=mysqli_fetch_array($result6))
                                        {
                                            $txt = $txt."Test Details:\n\tTest Id: ".$examId."\n\tCreated On: ".substr($row6['ed_created_on'],0,10)."\n\tDescription: ".substr($row6['ed_exam_desc'],0,50)."\n\tLink: http://www.instaxam.in/test-details.php?test_id=".$examId."\n";
                                            $to =$row6['ed_created_by'];
                                            $headers = "From: ". $email;	
                                            mail($to,$subject,$txt,$headers);
                                        }
                                        
                                        $to = $email;
                                        $subject = "Test Access Request Sent Again @ Instaxam.In";
                                        $txt = "Hi ".$row5['ud_first_name']."! \n\nWe have again notified your access request to the creator of the concerned test. We'll let you know once the request is processed.\n\nBest Wishes,\nInstaxam.In";
                                        $headers = "From: support@instaxam.in";	
                                        mail($to,$subject,$txt,$headers);
                                    }
								} else {
									$message = "Failure in raising another access request.";
								}
							} else {
								if($row2['eaf_avlblty'] == "N") {
									$message = "An access request was already sent today. Please try again tomorrow.";
								}
								if($row2['eaf_avlblty'] == "R") {
									$message = "The access request has been rejected today. Please try again tomorrow.";
								}
							}
						} else {
							$availableForUser = 0;
							$message = "The test availability is unknown.";
						}
					} else {
						$availableForUser = 0;
						$sql4 = "INSERT INTO `exam_avlbl_for` (`eaf_exam_id`, `eaf_user_id`) 
								                       VALUES ('".$examId."', '".$loggedUserId."')";
						
						if (mysqli_query($con, $sql4)) {
							$message = "Access request raised for the test.";
                            $sql5="SELECT `ud_first_name`,
                                                        `ud_last_name`,
                                                        `ud_contact_no`
                                            FROM `user_det` 
                                        WHERE `ud_user_id`='".$loggedUserId."'
                                    ";
                            $result5=mysqli_query($con, $sql5);
                            if($row5=mysqli_fetch_array($result5))
                            {
                                $email = $loggedUserId;
                                $subject = "Test Access Request @ Instaxam.In";
                                $txt = "User Details:\n\tName: ".$row5['ud_first_name']." ".$row5['ud_last_name']."\n\tEmail Id: ".$email."\n\tPhone Number: ".$row5['ud_contact_no']."\n\n";
                                $sql6="SELECT `ed_exam_desc`,
                                                            `ed_created_by`,
                                                            `ed_created_on`
                                                FROM `exam_det` 
                                            WHERE `ed_exam_id`='".$examId."'
                                        ";
                                $result6=mysqli_query($con, $sql6);
                                if($row6=mysqli_fetch_array($result6))
                                {
                                    $txt = $txt."Test Details:\n\tTest Id: ".$examId."\n\tCreated On: ".substr($row6['ed_created_on'],0,10)."\n\tDescription: ".substr($row6['ed_exam_desc'],0,50)."\n\tLink: http://www.instaxam.in/test-details.php?test_id=".$examId."\n";
                                    $to =$row6['ed_created_by'];
                                    $headers = "From: ". $email;	
                                    mail($to,$subject,$txt,$headers);
                                }
                                
                                $to = $email;
                                $subject = "Test Access Request Sent @ Instaxam.In";
                                $txt = "Hi ".$row5['ud_first_name']."! \n\nWe are happy to find you interested in our exams. We have sent your access request to the creator of the concerned test. We'll notify you once the request is processed.\n\nBest Wishes,\nInstaxam.In";
                                $headers = "From: support@instaxam.in";	
                                mail($to,$subject,$txt,$headers);
                            }
						} else {
							$message = "Failure in raising access request.";
						}
					}
				} else {
					$availableFor = "U";
					$message = "The test availability is unknown.";
				}
				
			} else {
				$message = "Invalid Exam ID.";
			}
			mysqli_close($con);
			echo $message;
		}
	}
?>
