<?php 
	$loggedUserId = "";
	$sentUserId = "";
	$message = "";
	if(isset($_SESSION['LogdUsrDet']))
	{
		$loggedUserId = $_SESSION['LogdUsrDet'][1];
		$examId = "";
		if(isset($_POST['UserId']))
		{
			$sql0 = "SELECT `ud_user_id`
					   FROM `user_det`
					  WHERE `ud_user_id` ='".$_POST['UserId']."'
					 ";
			$result0=mysqli_query($con, $sql0);
			if($row0=mysqli_fetch_array($result0, MYSQLI_ASSOC))
			{
				$sentUserId = $_POST['UserId'];
				if(isset($_POST['TestId']))
				{
					$examId = $_POST['TestId'];
					$availableFor = "";
					$availableForUser = 0;
					$sql1 = "SELECT `ed_avlbl_for`
							   FROM `exam_det`
							  WHERE `ed_exam_id` ='".$examId."'
								AND `ed_created_by` = '".$loggedUserId."'
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
										AND `eaf_user_id` = '".$sentUserId."'
									";
							$result2=mysqli_query($con, $sql2);
							if($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								if($row2['eaf_avlblty'] == "Y") {
									$availableForUser = 1;
									$message = "The test is already available for the user.";
								} else if($row2['eaf_avlblty'] == "R") {
									$message = "The access request has already been rejected for the user.";
								} else {
									if(isset($_POST['ActType']))
									{
										if($_POST['ActType']=="A") {
											$sql3 = "UPDATE `exam_avlbl_for`
														SET `eaf_avlblty` = 'Y', 
															`eaf_ts` = current_timestamp()
													  WHERE `eaf_exam_id` = '".$examId."'
														AND `eaf_user_id` = '".$sentUserId."'
													";
											if (mysqli_query($con, $sql3)) {
												$message = "Access request approved.";
                                                $sql5="SELECT `ud_first_name`
                                                                FROM `user_det` 
                                                            WHERE `ud_user_id`='".$sentUserId."'
                                                        ";
                                                $result5=mysqli_query($con, $sql5);
                                                if($row5=mysqli_fetch_array($result5))
                                                {
                                                    $to = $sentUserId;
                                                    $subject = "Test Access Request Approved @ Instaxam.In";
                                                    $txt = "Hi ".$row5['ud_first_name']."! \n\nYour access request for the below linked exam has been approved:\nLink: http://www.instaxam.in/test-details/".$examId."\n\nNow, you can login and test yourself.\nThank You,\nInstaxam.In";
                                                    $headers = "From: support@instaxam.in";	
                                                    mail($to,$subject,$txt,$headers);
                                                }
											} else {
												$message = "Failure in approving access request.";
											}
										} else if($_POST['ActType']=="R") {
											$sql3 = "UPDATE `exam_avlbl_for`
														SET `eaf_avlblty` = 'R', 
															`eaf_ts` = current_timestamp()
													  WHERE `eaf_exam_id` = '".$examId."'
														AND `eaf_user_id` = '".$sentUserId."'
													";
											if (mysqli_query($con, $sql3)) {
												$message = "Access request rejected.";
                                                $sql5="SELECT `ud_first_name`
                                                                FROM `user_det` 
                                                            WHERE `ud_user_id`='".$sentUserId."'
                                                        ";
                                                $result5=mysqli_query($con, $sql5);
                                                if($row5=mysqli_fetch_array($result5))
                                                {
                                                    $to = $sentUserId;
                                                    $subject = "Test Access Request Rejected @ Instaxam.In";
                                                    $txt = "Hi ".$row5['ud_first_name']."! \n\nWe are sorry to inform that your access request for Test Id ".$examId." has been rejected.\n\nYou can still browse and test yourself for other exams.\nThank You,\nInstaxam.In";
                                                    $headers = "From: support@instaxam.in";	
                                                    mail($to,$subject,$txt,$headers);
                                                }
											} else {
												$message = "Failure in rejecting access request.";
											}
										} else {
											$message = "Invalid action specified.";
										}
									} else {
										$message = "Action not specified.";
									}
								}
							} else {
								$message = "Access request yet not raised for the user.";
							}
						} else {
							$availableFor = "U";
							$message = "The test availability is unknown.";
						}
					} else {
						$message = "Invalid Exam ID.";
					}
				}
			} else {
				$message = "Invalid User ID.";
			}
		}
	}
	$Err = $message;
?>
