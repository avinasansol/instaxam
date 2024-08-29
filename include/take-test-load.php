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
			$status = "";
			$availableFor = "";
			$availableForUser = 0;
			$negatieMark = "";
			$questionsCount = 0;
			$timePer10Ques = 0;
			$totalTime = 0;
			$marksPerQues = 0;
			$totalMarks = 0;
			$activeExam = 0;
			$maxRetakeAllowed = 0;
			$examTakenCount = 0;
			$examAllowedCountForUser = 0;
			$examDesc = "";
			$sql1 = "SELECT `exam_det`.`ed_avlbl_for`,
							`exam_det`.`ed_exam_desc`,
							`exam_det`.`ed_status`,
							`exam_det`.`ed_negatie_mark`,
							`exam_det`.`ed_marks_per_ques`,
							`exam_det`.`ed_time_per10_ques`,
							`exam_det`.`ed_max_retake`
					   FROM `exam_det`
					  WHERE `exam_det`.`ed_exam_id` ='".$examId."'
					 ";
			$result1=mysqli_query($con, $sql1);
			if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
			{
                $examDesc = $row1['ed_exam_desc'];
				
				if($row1['ed_negatie_mark'] == "Y") {
					$negatieMark = "Yes";
				} else if($row1['ed_negatie_mark'] == "N") {
					$negatieMark = "No";
				} else {
					$negatieMark = "Unknown";
				}
				$timePer10Ques = (int)$row1['ed_time_per10_ques'];
				$marksPerQues  = (int)$row1['ed_marks_per_ques'];
				
				$sql6 = "SELECT COUNT(`ques_det`.`qd_ques_no`) AS `ques_count` 
						   FROM `ques_det`
						  WHERE `ques_det`.`qd_exam_id` = '".$examId."'
							AND `qd_del_ind` != 'Y'
						 ";
				$result6=mysqli_query($con, $sql6);
				if($row6=mysqli_fetch_array($result6, MYSQLI_ASSOC))
				{
					$questionsCount = (int)$row6['ques_count'];
				}
				
				$totalTime = ( $questionsCount * $timePer10Ques / 10 );
				$totalMarks = ( $questionsCount * $marksPerQues );
				$totalHr = 0;
				$totalMin = 0;
				$totalSec = 0;
				$totalHrC = "";
				$totalMinC = "";
				$totalSecC = "";
				$totalHr = floor($totalTime/60);
				if($totalHr<10){
					$totalHrC = "0".$totalHr;
				} else {
					$totalHrC = "".$totalHr;
				}
				$totalMin = ($totalTime%60);
				if($totalMin<10){
					$totalMinC = "0".$totalMin;
				} else {
					$totalMinC = "".$totalMin;
				}
				$totalSecC = "00";
				$totalTimeDisplay = $totalHrC.":".$totalMinC.":".$totalSecC;
		
				if($row1['ed_avlbl_for'] == "A") {
					$availableFor = "A";
				} else if($row1['ed_avlbl_for'] == "S") {
					$availableFor = "S";
					$sql5 = "SELECT `eaf_key_no`
							   FROM `exam_avlbl_for`
							  WHERE `eaf_exam_id` = '".$examId."'
								AND `eaf_user_id` = '".$loggedUserId."'
								AND `eaf_avlblty` = 'Y'
							";
					$result5=mysqli_query($con, $sql5);
					if($row5=mysqli_fetch_array($result5, MYSQLI_ASSOC))
					{
						$availableForUser = 1;
					} else {
						$availableForUser = 0;
					}
				} else {
					$availableFor = "U";
				}
				
				$maxRetakeAllowed = (int)$row1['ed_max_retake'];
				$examTakenCount = 0;
				$sql2 = "SELECT `et_end_ts`
						   FROM `exam_taken`
						  WHERE `et_exam_id` = '".$examId."'
							AND `et_user_id` = '".$loggedUserId."'
							AND `et_end_ts` IS NOT NULL
						  ORDER BY `et_end_ts` ASC
						";
				$result2=mysqli_query($con, $sql2);
				while($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
				{
					$examTakenCount++;
				}
				$examAllowedCountForUser = ($maxRetakeAllowed - $examTakenCount);
		
				$activeExam = 0;
				$activeExamNo = 0;
				$spentTime = "";
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
					$activeExam++;
					$activeExamNo = (int)$row3['et_exam_no'];
					$spentTime = substr($row3['et_time_spent'],0,8);
				
					$spentHr = 0;
					$spentMin = 0;
					$spentSec = 0;
					$spentHr = (int)substr($spentTime,0,2);
					$spentMin = (int)substr($spentTime,3,2);
					$spentSec = (int)substr($spentTime,6,2);
					
					$timeOver = 0;
					if( ($spentHr>$totalHr) || (($spentHr==$totalHr) && ($spentMin>$totalMin)) || (($spentHr==$totalHr) && ($spentMin==$totalMin) && ($spentSec>=$totalSec)) ) {
						$timeOver = 1;
					}
					
					if( (isset($_POST['FinishTest'])) && ($_POST['FinishTest']=="9999999") ) {
						$correctCount = 0;
						$inCorrecCtount = 0;
						$correctMarks = 0;
						$inCorrectMarks = 0;
						$scoredMarks = 0;
						$sql99 = "   SELECT COUNT(`ans_det`.`ad_ques_no`) AS `correct_ans_count`
									   FROM `ques_det`, `ans_det`
									  WHERE `ques_det`.`qd_exam_id` = '".$examId."'
										AND `ans_det`.`ad_taken_exam_no` = '".$activeExamNo."'
										AND `qd_del_ind` != 'Y'
										AND `ans_det`.`ad_ques_no` = `ques_det`.`qd_ques_no`
										AND ( (`ans_det`.`ad_ans_opt` IS NOT NULL) OR (`ans_det`.`ad_ans_opt` != '') )
										AND ( `ans_det`.`ad_ans_opt` = `ques_det`.`qd_correct_opt` )
										";
						$result99=mysqli_query($con, $sql99);
						if($row99=mysqli_fetch_array($result99, MYSQLI_ASSOC))
						{
							$correctCount = (int)$row99['correct_ans_count'];
						}
						$sql99 = "   SELECT COUNT(`ans_det`.`ad_ques_no`) AS `incorrect_ans_count`
									   FROM `ques_det`, `ans_det`
									  WHERE `ques_det`.`qd_exam_id` = '".$examId."'
										AND `ans_det`.`ad_taken_exam_no` = '".$activeExamNo."'
										AND `qd_del_ind` != 'Y'
										AND `ans_det`.`ad_ques_no` = `ques_det`.`qd_ques_no`
										AND ( (`ans_det`.`ad_ans_opt` IS NOT NULL) OR (`ans_det`.`ad_ans_opt` != '') )
										AND ( `ans_det`.`ad_ans_opt` != `ques_det`.`qd_correct_opt` )
								";
						$result99=mysqli_query($con, $sql99);
						if($row99=mysqli_fetch_array($result99, MYSQLI_ASSOC))
						{
							$inCorrecCtount = (int)$row99['incorrect_ans_count'];
						}
						$correctMarks = ($correctCount * $marksPerQues);
						if($negatieMark == "Yes"){
							$inCorrectMarks = ($inCorrecCtount * $marksPerQues / 3);
						}
						$scoredMarks = round(( $correctMarks - $inCorrectMarks ),2);
						
						$sql99 = "UPDATE `exam_taken` SET `et_marks` =  '".$scoredMarks."', `et_end_ts` = NOW()
								  WHERE `et_exam_no` = '".$activeExamNo."'";
						
						if (!mysqli_query($con, $sql99)) {
							echo "<h2>Error: Failure while finishing test.</h2>";
						} else {
                            $sql5="SELECT `ud_first_name`
                                            FROM `user_det` 
                                        WHERE `ud_user_id`='".$loggedUserId."'
                                    ";
                            $result5=mysqli_query($con, $sql5);
                            if($row5=mysqli_fetch_array($result5))
                            {
                                $to = $loggedUserId;
                                $subject = "Test Completion @ Instaxam.In";
                                $txt = "Hi ".$row5['ud_first_name']."! \n\nYou have successfully completed a test @ Instaxam.In. Below is the details for the same:\n\tTest Name: ".substr($examDesc,0,200)."\n\tTotal Questions: ".$questionsCount."\n\tCorrect Attempts: ".$correctCount."\n\tIncorrect Attempts: ".$inCorrecCtount."\n\tNot Attempted: ".($questionsCount-($correctCount+$inCorrecCtount))."\n\tTotal Marks: ".$totalMarks."\n\tMarks Scored:".$scoredMarks."\n\tTotal Time: ".$totalTimeDisplay."\n\tTime Taken: ".$spentTime."\n\nYou can login and download Complete Results and Solutions from the Test History tab.\nThank You,\nInstaxam.In";
                                $headers = "From: support@instaxam.in";	
                                mail($to,$subject,$txt,$headers);
                            }
						?>
						<h2>The test has been submitted successfully. Click 'Complete Results &amp; Solution' below to view the results&amp; solution.</h2>
						<form action="test-results.php" method="post">
							<div id="load-more" class="load-more">
								<button type="submit" name="TestNumber" value="<?php echo $activeExamNo; ?>">Complete Results &amp; Solution</button>
							</div>
						</form>
						<?php 
						}
						
						
					} else if( ($row1['ed_status'] == "D") || ($timeOver == 1) ) {
						$status = "D";
						// status deactivated or time over finish test
						if($row1['ed_status'] == "D"){
						?>
						<h2>The test has been deactivated by the creator. Please click finish test to submit your answers and view the results.</h2>
						<?php 
						} else if($timeOver == 1){
						?>
						<h2>Your time is over. Please click finish test to submit your answers and view the results.</h2>
						<?php 
						}
						?>
						<form id='contact' action='take-test.php' method='post' onSubmit="return false">
						<div id="load-more" class="load-more">
							<input type="hidden" name="TestId" id="TestId" value="<?php echo $examId; ?>" />
							<button name="FinishTest" value="9999999" type="submit" onclick="return finishTest()">Finish Test</button>
						</div>
						</form>
						<?php 
					} else {
						$status = "A or U";
						// load 1 ques
						$correctQuesNoSet = 0;
						$QuesNo = 0;
						$quesDesc = "";
						$optA = "";
						$optB = "";
						$optC = "";
						$optD = "";
						$noQuesAvlbl = 0;
						if(isset($_POST['QuesNo'])){
							$ansSent = "";
							if(isset($_POST['ansNo'])){
								if(isset($_POST['ansOpt'])) {
									if($_POST['ansOpt']=="A"){$ansSent = "A";}
									if($_POST['ansOpt']=="B"){$ansSent = "B";}
									if($_POST['ansOpt']=="C"){$ansSent = "C";}
									if($_POST['ansOpt']=="D"){$ansSent = "D";}
								}
								if( ($ansSent == "A") || ($ansSent == "B") || ($ansSent == "C") || ($ansSent == "D") || ($ansSent == "") ){
									$sql4 = "SELECT `qd_ques_no`
											   FROM `ques_det`
											  WHERE `qd_exam_id` = '".$examId."'
												AND `qd_ques_no` = '".$_POST['ansNo']."'
												AND `qd_del_ind` != 'Y'
											 ";
									$result4=mysqli_query($con, $sql4);
									if($row4=mysqli_fetch_array($result4, MYSQLI_ASSOC))
									{
											$markLaterSent = "N";
											$markLaterSentWhereClause = "";
											if(isset($_POST['MarkLater'])){
												if($_POST['MarkLater']=="Y"){
													$markLaterSent = "Y";
													$markLaterSentWhereClause = ", `ad_mark_later` = 'Y' ";
												}
											}
											$sql8 = "SELECT `ad_ans_no`
													   FROM `ans_det`
													  WHERE `ad_taken_exam_no` = '".$activeExamNo."'
														AND `ad_ques_no` = '".$_POST['ansNo']."'
													";
											$result8=mysqli_query($con, $sql8);
											if($row8=mysqli_fetch_array($result8, MYSQLI_ASSOC))
											{
												if($ansSent == ""){
													$sql9 = "UPDATE `ans_det` SET `ad_ans_opt` =  NULL".$markLaterSentWhereClause."
															  WHERE `ad_ans_no` = '".$row8['ad_ans_no']."'";
													
													if (!mysqli_query($con, $sql9)) {
														echo "<h2>Error: Failure while updating answer.</h2>";
													}
												} else {
													$sql9 = "UPDATE `ans_det` SET `ad_ans_opt` = '".$ansSent."',
																	`ad_mark_later` = NULL
															  WHERE `ad_ans_no` = '".$row8['ad_ans_no']."'";
													
													if (!mysqli_query($con, $sql9)) {
														echo "<h2>Error: Failure while updating answer.</h2>";
													}
												}
											}
											else {
												if($ansSent == ""){
													if($markLaterSent == "Y") {
														$sql9 = "INSERT INTO `ans_det` (`ad_taken_exam_no`, `ad_ques_no`, `ad_ans_opt`, `ad_mark_later`) 
																				VALUES ('".$activeExamNo."', '".$_POST['ansNo']."', NULL, 'Y')";
													} else {
														$sql9 = "INSERT INTO `ans_det` (`ad_taken_exam_no`, `ad_ques_no`, `ad_ans_opt`) 
																				VALUES ('".$activeExamNo."', '".$_POST['ansNo']."', NULL)";
													}
													if (!mysqli_query($con, $sql9)) {
														echo "<h2>Error: Failure while updating answer.</h2>";
													}
												} else {
													$sql9 = "INSERT INTO `ans_det` (`ad_taken_exam_no`, `ad_ques_no`, `ad_ans_opt`) 
																			VALUES ('".$activeExamNo."', '".$_POST['ansNo']."', '".$ansSent."')";
													
													if (!mysqli_query($con, $sql9)) {
														echo "<h2>Error: Failure while updating answer.</h2>";
													}
												}
											}
									}
								}
							}
							$sql4 = "SELECT `ques_det`.`qd_ques_desc`,
											`ques_det`.`qd_opt_a`,
											`ques_det`.`qd_opt_b`,
											`ques_det`.`qd_opt_c`,
											`ques_det`.`qd_opt_d`
									   FROM `ques_det`
									  WHERE `ques_det`.`qd_exam_id` = '".$examId."'
									    AND `ques_det`.`qd_ques_no` = '".$_POST['QuesNo']."'
										AND `qd_del_ind` != 'Y'
									 ";
							$result4=mysqli_query($con, $sql4);
							if($row4=mysqli_fetch_array($result4, MYSQLI_ASSOC))
							{
								$correctQuesNoSet = 1;
								$QuesNo = (int)$_POST['QuesNo'];
								$quesDesc = $row4['qd_ques_desc'];
								$optA = $row4['qd_opt_a'];
								$optB = $row4['qd_opt_b'];
								$optC = $row4['qd_opt_c'];
								$optD = $row4['qd_opt_d'];
							}
						}
						if($correctQuesNoSet == 0){
							$whereClause = "";
							$sql5 = "SELECT MAX(`ad_ques_no`) AS `max_attempt_ques_no`
									   FROM `ans_det`
									  WHERE `ad_taken_exam_no` = '".$activeExamNo."'
									";
							$result5=mysqli_query($con, $sql5);
							if($row5=mysqli_fetch_array($result5, MYSQLI_ASSOC))
							{
								$sql7 = "SELECT `qd_ques_no`
										   FROM `ques_det`
										  WHERE `qd_exam_id` = '".$examId."'
											AND `qd_del_ind` != 'Y'
										    AND `qd_ques_no` > '".$row5['max_attempt_ques_no']."'
										  LIMIT 0, 1
										 ";
								$result7=mysqli_query($con, $sql7);
								if($row7=mysqli_fetch_array($result7, MYSQLI_ASSOC))
								{
									$whereClause = "AND `ques_det`.`qd_ques_no` = '".$row7['qd_ques_no']."'";
								}
							}
							$sql6 = "SELECT `ques_det`.`qd_ques_no`,
											`ques_det`.`qd_ques_desc`,
											`ques_det`.`qd_opt_a`,
											`ques_det`.`qd_opt_b`,
											`ques_det`.`qd_opt_c`,
											`ques_det`.`qd_opt_d`
									   FROM `ques_det`
									  WHERE `ques_det`.`qd_exam_id` = '".$examId."'
										AND `qd_del_ind` != 'Y'
									  ".$whereClause."
									  ORDER BY `ques_det`.`qd_ques_no` ASC
									  LIMIT 0, 1
									 ";
							$result6=mysqli_query($con, $sql6);
							if($row6=mysqli_fetch_array($result6, MYSQLI_ASSOC))
							{
								$correctQuesNoSet = 1;
								$QuesNo = (int)$row6['qd_ques_no'];
								$quesDesc = $row6['qd_ques_desc'];
								$optA = $row6['qd_opt_a'];
								$optB = $row6['qd_opt_b'];
								$optC = $row6['qd_opt_c'];
								$optD = $row6['qd_opt_d'];
							} else {
								$noQuesAvlbl = 1;
							}
						}
						if($noQuesAvlbl == 1){
							echo "<h2>Sorry! No question available for the test.</h2>";
						} else {
						
							$markedAns = "";
							$jumpQuesHTML = "";
							$attemptedQuesCount = 0;
							$skippedQuesCount = 0;
							$sql4 = "SELECT `ques_det`.`qd_ques_no`
									   FROM `ques_det`
									  WHERE `ques_det`.`qd_exam_id` = '".$examId."'
										AND `qd_del_ind` != 'Y'
									  ORDER BY `ques_det`.`qd_ques_no` ASC
									 ";
							$eachQuesNo = 0;
							$eachQuesCount = 0;
							$currentDisplayQuesNo = 0;
							
							$prevQuesStore = "Y";
							$prevQuesAvlbl = "N";
							$prevQuesTemp = 0;
							$prevQuesNo = 0;
							
							$nextQuesStore = "N";
							$nextQuesAvlbl = "N";
							$nextQuesNo = 0;
							$result4=mysqli_query($con, $sql4);
							while($row4=mysqli_fetch_array($result4, MYSQLI_ASSOC))
							{
								$eachQuesNo = (int)$row4['qd_ques_no'];
								$eachQuesCount++;
								
								if($nextQuesStore == "Y"){
									$nextQuesAvlbl = "Y";
									$nextQuesNo = $eachQuesNo;
									$nextQuesStore = "N";
								}
								if($eachQuesNo==$QuesNo){
									$currentDisplayQuesNo = $eachQuesCount;
									if($prevQuesTemp==0){
										$prevQuesAvlbl = "N";
									} else {
										$prevQuesAvlbl = "Y";
										$prevQuesNo = $prevQuesTemp;
									}
									$prevQuesStore = "N";
									$nextQuesStore = "Y";
								}
								if($prevQuesStore == "Y"){
									$prevQuesTemp = $eachQuesNo;
								}
								
								$quesAnswered = 0;
								$markedForLater = 0;
								$sql8 = "SELECT `ad_ans_opt`,
												`ad_mark_later`
										   FROM `ans_det`
										  WHERE `ad_taken_exam_no` = '".$activeExamNo."'
											AND `ad_ques_no` = '".$eachQuesNo."'
										";
								$result8=mysqli_query($con, $sql8);
								if($row8=mysqli_fetch_array($result8, MYSQLI_ASSOC))
								{
									if($row8['ad_ans_opt'] != "") {
										$quesAnswered = 1;
										$attemptedQuesCount++;
										if($eachQuesNo==$QuesNo){
											$markedAns = $row8['ad_ans_opt'];
										}
									} else {
										$skippedQuesCount++;
									}
									if($row8['ad_mark_later'] == "Y") {
										$markedForLater = 1;
									}
								}
								$jumpQuesHTML = $jumpQuesHTML."<div class='";
								if($quesAnswered==1){
									$jumpQuesHTML = $jumpQuesHTML."color"; 
								}else if($markedForLater==1){
									$jumpQuesHTML = $jumpQuesHTML."later";
								} else{
									$jumpQuesHTML = $jumpQuesHTML."empty";
								}
								$jumpQuesHTML = $jumpQuesHTML."-rounded-button'><button name='QuesNo' value='".$eachQuesNo."' type='submit' onclick=\"return loadTest('".$eachQuesNo."')\">".$eachQuesCount."</button></div>";
							}
							?>
						  <div class="divTable" id="questHeader">
								 <div class="headRow">
									<form id='contact' action='take-test.php' method='post' onSubmit="return false">
									<div class="divCel1">
										<h3>Question No: <?php echo $currentDisplayQuesNo;?></h3>
										<p><?php echo str_replace("\n","<br />",preg_replace( "/[\r\n]+/", "\n", $quesDesc));?></p>
										<div class='circle-item'>
											<input type="hidden" name="TestId" id="TestId" value="<?php echo $examId; ?>" />
											<input type="hidden" name="ansNo" id="ansNo" value="<?php echo $QuesNo;?>" />
											<input name='ansOpt' type='radio' id='optA' value='A' <?php if($markedAns=="A"){echo "checked";} ?> onclick="return loadTest('<?php echo $QuesNo; ?>')" />
											<label for='optA'>(A) <?php echo $optA; ?></label>
											<input name='ansOpt' type='radio' id='optB' value='B' <?php if($markedAns=="B"){echo "checked";} ?> onclick="return loadTest('<?php echo $QuesNo; ?>')" />
											<label for='optB'>(B) <?php echo $optB; ?></label>
											<input name='ansOpt' type='radio' id='optC' value='C' <?php if($markedAns=="C"){echo "checked";} ?> onclick="return loadTest('<?php echo $QuesNo; ?>')" />
											<label for='optC'>(C) <?php echo $optC; ?></label>
											<input name='ansOpt' type='radio' id='optD' value='D' <?php if($markedAns=="D"){echo "checked";} ?> onclick="return loadTest('<?php echo $QuesNo; ?>')" />
											<label for='optD'>(D) <?php echo $optD; ?></label>
										</div>
										<div id="clear-mark-row">
											<button name="QuesNo" value="<?php echo $QuesNo; ?>" type="submit" onclick="clearAnswer('<?php echo $QuesNo; ?>')">Clear Your Answer</button>
											<button name="QuesNo" value="<?php echo $QuesNo; ?>" type="submit" onclick="markLater('<?php echo $QuesNo; ?>')">Mark For Later</button>
										</div>
										<div id="prev-next-row">
											<div id="prev-next-col1">
											<?php
											if($prevQuesAvlbl == "Y"){
											?>
												<button name="QuesNo" value="<?php echo $prevQuesNo; ?>" type="submit" onclick="return loadTest('<?php echo $prevQuesNo; ?>')">previous question</button>
											<?php
											}
											?>
											</div>
											<div id="prev-next-col2">
											<?php
											if($nextQuesAvlbl == "Y"){
											?>
												<button name="QuesNo" value="<?php echo $nextQuesNo; ?>" type="submit" onclick="return loadTest('<?php echo $nextQuesNo; ?>')">next question</button>
											<?php
											}
											?>
											</div>
										</div>
									</div>
									<div class="divCel2">
										<div id="t-det">
											<div class="t-det-row">
												<div class="t-det-col1">
													Total Time:
												</div>
												<div class="t-det-col2">
													<?php 
														echo $totalTimeDisplay;
													?>
												</div>
											</div>
											<div class="t-det-row">
												<div class="t-det-col1">
													Remaining:
												</div>
												<div class="t-det-col2" style="font-weight:bold; color:#FF6600;">
													<?php 
														$spentHr = 0;
														$spentMin = 0;
														$spentSec = 0;
														$spentHr = (int)substr($spentTime,0,2);
														$spentMin = (int)substr($spentTime,3,2);
														$spentSec = (int)substr($spentTime,6,2);
														
														$remHr = 0;
														$remMin = 0;
														$remSec = 0;
														$remHrC = "";
														$remMinC = "";
														$remSecC = "";
														
														if($totalSec >= $spentSec) {
															$remSec = ($totalSec - $spentSec);
															if($totalMin >= $spentMin) {
																$remMin = ($totalMin - $spentMin);
																$remHr = ($totalHr - $spentHr);
															} else {
																$remMin = ($totalMin + 60 - $spentMin);
																$remHr = ($totalHr - 1 - $spentHr);
															}
														} else {
															$remSec = ($totalSec + 60 - $spentSec);
															if($totalMin > $spentMin) {
																$remMin = ($totalMin - 1 - $spentMin);
																$remHr = ($totalHr - $spentHr);
															} else {
																$remMin = ($totalMin - 1 + 60 - $spentMin);
																$remHr = ($totalHr - 1 - $spentHr);
															}
														}
														
														if($remHr<10){
															$remHrC = "0".$remHr;
														} else {
															$remHrC = "".$remHr;
														}
														if($remMin<10){
															$remMinC = "0".$remMin;
														} else {
															$remMinC = "".$remMin;
														}
														if($remSec<10){
															$remSecC = "0".$remSec;
														} else {
															$remSecC = "".$remSec;
														}
													?>
													<span id="remainingTime"><?php echo $remHrC.":".$remMinC.":".$remSecC;?></span>
												</div>
											</div>
											<div class="t-det-row">
												<div class="t-det-col1">
													Total Questions:
												</div>
												<div class="t-det-col2">
													<?php echo $questionsCount;?>
												</div>
											</div>
											<div class="t-det-row">
												<div class="t-det-col1">
													Attempted:
												</div>
												<div class="t-det-col2" style="font-weight:bold; color:#535ba0;">
													<?php echo $attemptedQuesCount;?>
												</div>
											</div>
											<div class="t-det-row">
												<div class="t-det-col1">
													Skipped:
												</div>
												<div class="t-det-col2" style="font-weight:bold; color:#FF6600;">
													<?php echo $skippedQuesCount;?>
												</div>
											</div>
										</div>
										<h4>Question List:</h4>
										<div id="jump-ques">
											<?php echo $jumpQuesHTML; ?>
										</div>
										<div id="finish-test" style="text-align:center; margin-top:20px; margin-bottom:-100px;">
											<button name="FinishTest" value="9999999" type="submit" onclick="return finishTest()">Finish Test</button>
										</div>
									</div>
									</form>
							   </div>
						  </div>	
						<?php 
						}
					}
				} else {
					$activeExam = 0;
					if($row1['ed_status'] == "D") {
						$status = "D";
						// status deactivated
						echo "<h2>The test has been deactivated by the creator.</h2>";
					} else if($row1['ed_status'] == "A") {
						$status = "A";
						if ( ($availableFor == "A") || ($availableFor == "S" && $availableForUser == 1) ) {
							if($examAllowedCountForUser > 0) {
						// start test
								$sql9 = "INSERT INTO `exam_taken` (`et_exam_id`, `et_user_id`) 
														   VALUES ('".$examId."', '".$loggedUserId."')";
								
								if (mysqli_query($con, $sql9)) {
								?>
									<h2>Instructions</h2>
									<ul>
										<li>Give test only after you have prepared well for it.</li>
										<li>If time expires, Test will be automatically submitted for evaluation.</li>
										<li>After completion of test, you will be shown your performance.</li>
										<li>After completion, you can view 'Solutions' under 'Test History' in your Dashboard.</li>
										<li>The test consists of total <?php echo $questionsCount;?> questions to be completed in <?php echo $totalTime;?> mins.</li>
										<li>Each question carries <?php echo $marksPerQues;?> marks. Total Marks -> <?php echo $totalMarks;?> </li>
										<li><?php if($negatieMark == "Yes") { ?>Each wrong answer will be penalized with 1/3rd negative marking.<?php } else { ?>There will be no negative marking for wrong answers.<?php } ?> </li>
									</ul>
									<form action="take-test.php" method="post" onSubmit="">
									<div id="load-more" class="load-more">
										<input type="hidden" name="TestId" id="TestId" value="<?php echo $examId; ?>" />
										<button type="submit">Start Test</button>
									</div>
									</form>
								<?php 
								} else {
									echo "<h2>Error: Failure while startig test.</h2>";
								}
							} else {
						// no more attempts available
								echo "<h2>No more attempts available for the test.</h2>";
							}
						} else if ($availableFor == "S" && $availableForUser == 0){
						// not available for you raise access req
								?>
								<h2>The test is available for selected users only. You need to request access for the same.</h2>
								<form action="test-access-req.php" method="post" onSubmit="return raiseAccessReq()">
								<div id="load-more" class="load-more">
									<input type="hidden" name="TestId" id="TestId" value="<?php echo $examId; ?>" />
									<button type="submit">Raise Access Request</button>
								</div>
								</form>
								<?php 
						} else {
						// availability unknown
								echo "<h2>Error: Test availability unknown.</h2>";
						}
					} else {
						$status = "U";
						// status unknown
						echo "<h2>Error: Test status unknown.</h2>";
					}
				}
				
			} else {
			//  Invalid Exam ID
				echo "<h2>Error: Invalid Exam ID.</h2>";
				mysqli_close($con);
			}
			mysqli_close($con);

		}
	}
?>
