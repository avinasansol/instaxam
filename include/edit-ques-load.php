<?php 
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}
$page = "edit-test.php";
if(isset($_SESSION['LogdUsrDet']))
{
	$testId = "";
	if(isset($_POST['TestId']))
	{
		$testId = $_POST['TestId'];
		
		$loggedUserId = $_SESSION['LogdUsrDet'][1];
		include("connect-database.php");
		
		$sql="SELECT `ud_creator_access` 
				FROM `user_det` 
			   WHERE `ud_user_id`='".$loggedUserId."'
			 ";
		$result=mysqli_query($con, $sql);
		if($row=mysqli_fetch_array($result))
		{
			if($row['ud_creator_access']=="Y")
			{
				$sql="SELECT `ed_exam_id` 
						FROM `exam_det` 
					   WHERE `ed_created_by`='".$loggedUserId."'
						 AND `ed_exam_id` = '".$testId."'
					 ";
				$result=mysqli_query($con, $sql);
				if(mysqli_fetch_array($result))
				{
/*------------------------------------------------------START DELETE/UPDATE/INSERT----------------------------------------------------- */

$ErrQ = "Please provide a valid ";
$QuesDesc = "";
$OptA = "";
$OptB = "";
$OptC = "";
$OptD = "";
$ansOpt = "";
$QuesSol = "";

if( (isset($_POST['DelQues'])) && (isset($_POST['CurrQuesNo'])) && ($_POST['DelQues']=="DeleteQues") ) {
	$sql1 = "SELECT `ques_det`.`qd_ques_no`
			   FROM `ques_det`
			  WHERE `ques_det`.`qd_exam_id` = '".$testId."'
				AND `qd_del_ind` != 'Y'
				AND `ques_det`.`qd_ques_no` = '".$_POST['CurrQuesNo']."'
			 ";
	$result1=mysqli_query($con, $sql1);
	if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{
		$sql2 = "SELECT `ad_ques_no`
				   FROM `ans_det`
				  WHERE `ad_ques_no` = '".$_POST['CurrQuesNo']."'
				 ";
		$result2=mysqli_query($con, $sql2);
		if($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
		{
			$sql9 = "UPDATE `ques_det` 
						SET `qd_del_ind` = 'Y'
					  WHERE `qd_exam_id` = '".$testId."'
						AND `ques_det`.`qd_ques_no` = '".$_POST['CurrQuesNo']."'
					";
			if (!mysqli_query($con, $sql9)) {
				$ErrQ = "Error: Failure while deleting question.";
			} else {
				$ErrQ = "Question deleted.";
			}
		} else {
			$sql9 = "DELETE FROM `ques_det`
					  WHERE `qd_exam_id` = '".$testId."'
						AND `ques_det`.`qd_ques_no` = '".$_POST['CurrQuesNo']."'
					";
			if (!mysqli_query($con, $sql9)) {
				$ErrQ = "Error: Failure while deleting question.";
			} else {
				$ErrQ = "Question deleted.";
			}
		}
	} else {
		$ErrQ = "Invalid Question No.";
	}
}
if( (isset($_POST['UpdateTest'])) && (isset($_POST['CurrQuesNo'])) && (isset($_POST['QuesDesc'])) && (isset($_POST['OptA'])) && (isset($_POST['OptB'])) && (isset($_POST['OptC'])) && (isset($_POST['OptD'])) && (isset($_POST['QuesSol'])) )
{
	if(str_replace(" ","",$_POST['QuesDesc'])=="") {
		$ErrQ = $ErrQ."Question, ";
	}
	
	if(str_replace(" ","",$_POST['OptA'])=="") {
		$ErrQ = $ErrQ."Option A, ";
	} else {
		$OptA = mysqli_real_escape_string($con, substr(htmlentities($_POST['OptA']),0,2000));
	}
	
	if(str_replace(" ","",$_POST['OptB'])=="") {
		$ErrQ = $ErrQ."Option B, ";
	} else {
		$OptB = mysqli_real_escape_string($con, substr(htmlentities($_POST['OptB']),0,2000));
	}
	
	if(str_replace(" ","",$_POST['OptC'])=="") {
		$ErrQ = $ErrQ."Option C, ";
	} else {
		$OptC = mysqli_real_escape_string($con, substr(htmlentities($_POST['OptC']),0,2000));
	}
	
	if(str_replace(" ","",$_POST['OptD'])=="") {
		$ErrQ = $ErrQ."Option D, ";
	} else {
		$OptD = mysqli_real_escape_string($con, substr(htmlentities($_POST['OptD']),0,2000));
	}
	
	if(isset($_POST['ansOpt'])) {
		if( ($_POST['ansOpt']!="A") && ($_POST['ansOpt']!="B") && ($_POST['ansOpt']!="C") && ($_POST['ansOpt']!="D") ) {
			$ErrQ = $ErrQ."Correct Option, ";
		} else {
			$ansOpt = $_POST['ansOpt'];
		}
	} else {
		$ErrQ = $ErrQ."Correct Option, ";
	}
	
	if($ErrQ!="Please provide a valid ") {
		$ErrQ[(strlen($ErrQ)-2)]=".";
	} else {
		if(strlen($_POST['QuesDesc'])<10) {
			$ErrQ = "Question must contain at least 10 letters.";
		} else {
			$QuesDesc = mysqli_real_escape_string($con, substr(htmlentities($_POST['QuesDesc']),0,2000));
			$QuesSol = mysqli_real_escape_string($con, substr(htmlentities($_POST['QuesSol']),0,2000));
			if( (isset($_POST['UpdateTest'])) && (isset($_POST['CurrQuesNo'])) ) {
				if($_POST['UpdateTest'] == "EditQues"){
					$sql1 = "SELECT `ques_det`.`qd_ques_no`
							   FROM `ques_det`
							  WHERE `ques_det`.`qd_exam_id` = '".$testId."'
								AND `qd_del_ind` != 'Y'
								AND `ques_det`.`qd_ques_no` = '".$_POST['CurrQuesNo']."'
							 ";
					$result1=mysqli_query($con, $sql1);
					if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{
						$sql9 = "UPDATE `ques_det` 
									SET `qd_ques_desc` = '".$QuesDesc."',
										`qd_opt_a` = '".$OptA."',
										`qd_opt_b` = '".$OptB."',
										`qd_opt_c` = '".$OptC."',
										`qd_opt_d` = '".$OptD."',
										`qd_correct_opt` = '".$ansOpt."',
										`qd_soln` =  '".$QuesSol."'
								  WHERE `qd_exam_id` = '".$testId."'
									AND `qd_del_ind` != 'Y'
								    AND `ques_det`.`qd_ques_no` = '".$_POST['CurrQuesNo']."'
								";
						if (!mysqli_query($con, $sql9)) {
							$ErrQ = "Error: Failure while updating question.";
						} else {
							unset($_POST['QuesDesc']);
							unset($_POST['OptA']);
							unset($_POST['OptB']);
							unset($_POST['OptC']);
							unset($_POST['OptD']);
							unset($_POST['ansOpt']);
							unset($_POST['QuesSol']);
							$_POST['QuesNo'] = $_POST['CurrQuesNo'];
							$ErrQ = "Question updated.";
						}
					} else {
						$ErrQ = "Invalid Question No.";
					}
				}
				if( ($_POST['UpdateTest'] == "AddQues") && ($_POST['CurrQuesNo'] == "999999999999") ) {
					$sql8 = "SELECT `qd_ques_no` FROM `ques_det` 
							  WHERE `qd_exam_id` = '".$testId."'
                        		AND `qd_del_ind` != 'Y'
							    AND REPLACE(REPLACE(`qd_ques_desc`,' ',''),'\n','') = REPLACE(REPLACE('".$QuesDesc."',' ',''),'\n','')
								AND REPLACE(REPLACE(`qd_opt_a`,' ',''),'\n','') = REPLACE(REPLACE('".$OptA."',' ',''),'\n','')
								AND REPLACE(REPLACE(`qd_opt_b`,' ',''),'\n','') = REPLACE(REPLACE('".$OptB."',' ',''),'\n','')
								AND REPLACE(REPLACE(`qd_opt_c`,' ',''),'\n','') = REPLACE(REPLACE('".$OptC."',' ',''),'\n','')
								AND REPLACE(REPLACE(`qd_opt_d`,' ',''),'\n','') = REPLACE(REPLACE('".$OptD."',' ',''),'\n','')
							 ";
					$result8=mysqli_query($con, $sql8);
					if($row8=mysqli_fetch_array($result8, MYSQLI_ASSOC)) {
						$ErrQ = "Question already added.";
					} else {
						$sql9 = "INSERT INTO `ques_det` (`qd_exam_id`, `qd_ques_desc`, `qd_opt_a`, `qd_opt_b`, `qd_opt_c`, `qd_opt_d`, `qd_correct_opt`, `qd_soln`) 
												VALUES ('".$testId."', '".$QuesDesc."', '".$OptA."', '".$OptB."', '".$OptC."', '".$OptD."', '".$ansOpt."', '".$QuesSol."')";
						if (!mysqli_query($con, $sql9)) {
							$ErrQ = "Error: Failure while adding question.";
						} else {
							unset($_POST['QuesDesc']);
							unset($_POST['OptA']);
							unset($_POST['OptB']);
							unset($_POST['OptC']);
							unset($_POST['OptD']);
							unset($_POST['ansOpt']);
							unset($_POST['QuesSol']);
							$sql1 = "SELECT MAX(`qd_ques_no`) AS `max_ques_no`
									   FROM `ques_det`
									  WHERE `qd_exam_id` = '".$testId."'
										AND `qd_del_ind` != 'Y'
									 ";
							$result1=mysqli_query($con, $sql1);
							if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
								$_POST['QuesNo'] = $row1['max_ques_no'];
							}
							$ErrQ = "Question added.";
						}
					}
				}
			}
		}
	}
}
						
if($ErrQ=="Please provide a valid ") {
	$ErrQ = "";
} else {
	if(($ErrQ != "Question added.")&&($ErrQ != "Question updated.")&&($ErrQ != "Question deleted.")){
		$_POST['QuesNo'] = $_POST['CurrQuesNo'];
	}
}
/*-------------------------------------------------------END DELETE/UPDATE/INSERT----------------------------------------------------- */
					$quesDesc = "";
					$optA = "";
					$optB = "";
					$optC = "";
					$optD = "";
					$correctOpt = "";
					$soln = "";
					
					$sentQuesNo = 0;
					if(isset($_POST['QuesNo']))
					{
						$sql1 = "SELECT `ques_det`.`qd_ques_no`
								   FROM `ques_det`
								  WHERE `ques_det`.`qd_exam_id` = '".$testId."'
								    AND `ques_det`.`qd_ques_no` = '".$_POST['QuesNo']."'
									AND `qd_del_ind` != 'Y'
								 ";
						$result1=mysqli_query($con, $sql1);
						if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
						{
							$sentQuesNo = $row1['qd_ques_no'];
						} else if($_POST['QuesNo']=="999999999999") {
							$sentQuesNo = "999999999999";
						}
					}
					
					$maxQuesCount = 0;
					$firstQues = 0;
					
					$sql1 = "SELECT COUNT(`ques_det`.`qd_ques_no`) AS `ques_count` 
							   FROM `ques_det`
							  WHERE `ques_det`.`qd_exam_id` = '".$testId."'
								AND `qd_del_ind` != 'Y'
							 ";
					$result1=mysqli_query($con, $sql1);
					if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{
						$maxQuesCount = (int)$row1['ques_count'];
					}
					
					$storePrev = "Y";
					$storeNext = "N";
					$prevQuesNo = 0;
					$nextQuesNo = 0;
					
					$QuesNo = 0;
					$quesCount = 0;
					$selectedQuesCount = 0;
					$quesList = "";
					$quesListStart = "";
						
					$sql1 = "SELECT `ques_det`.`qd_ques_no`
							   FROM `ques_det`
							  WHERE `ques_det`.`qd_exam_id` = '".$testId."'
								AND `qd_del_ind` != 'Y'
							 ";
					$result1=mysqli_query($con, $sql1);
					while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{
						$quesCount++;
						$QuesNo = (int)$row1['qd_ques_no'];
						if( ($sentQuesNo == 0) && ($quesCount == 1) ){
							$sentQuesNo = $QuesNo;
						}
						
						if($sentQuesNo == $QuesNo)
						{
							$storePrev = "N";
						}
						if( ($storePrev == "Y") || ($quesCount == 1) ){
							$prevQuesNo = $QuesNo;
						}
						
						if($storeNext == "Y"){
							$nextQuesNo  = $QuesNo;
							$storeNext = "N";
						}
						if(($nextQuesNo==0) && ($quesCount == $maxQuesCount)){
							$nextQuesNo  = "999999999999";
							$storeNext = "N";
						}
						if($sentQuesNo == $QuesNo)
						{
							$storeNext = "Y";
						}
						
						if($quesCount == 1){
							$firstQues = $QuesNo;
						}
						if($quesCount == 1){
							$firstQues = $QuesNo;
						}
						
						if( ($quesCount == 1) || ($quesCount == 2) || ($quesCount == ($maxQuesCount-1)) || ($quesCount == $maxQuesCount) || ($sentQuesNo == $QuesNo) || ($quesCount == floor($maxQuesCount/2)) || ($quesCount == (ceil((($maxQuesCount/2))/2))) || ($quesCount == (floor($maxQuesCount/2)+floor((($maxQuesCount)-floor($maxQuesCount/2))/2))) ){
							$quesList = $quesList."<li";
							if($sentQuesNo == $QuesNo)
							{
								$quesList = $quesList." class='active'";
							}
							$quesList = $quesList."><a href='".$page."' onclick='return othrQues(".$QuesNo.")'>";
							if( ($quesCount == 1) || ($quesCount == 2) || ($quesCount == ($maxQuesCount-1)) || ($quesCount == $maxQuesCount) || ($sentQuesNo == $QuesNo) || ($quesCount == floor($maxQuesCount/2)) ){
								$quesList = $quesList.$quesCount;
							} else {
								$quesList = $quesList."..";
							}
								$quesList = $quesList."</a></li>";
						}
						
						if($quesCount == $maxQuesCount){
							$quesListStart = $quesListStart."<ul class='table-pagination'><li";
							if($sentQuesNo == $firstQues){
								$quesListStart = $quesListStart." style='display:none;'";
							}
							$quesListStart = $quesListStart."><a href='".$page."' onclick='return othrQues(".$prevQuesNo.")'>Previous</a></li>";
							
							$quesList = $quesListStart.$quesList."<li";
							if($sentQuesNo == "999999999999"){
								$quesList = $quesList." style='display:none;'";
							}
							$quesList = $quesList."><a href='".$page."' onclick='return othrQues(999999999999)'>Add Question</a></li>";
							
							$quesList = $quesList."<li";
							if($sentQuesNo == "999999999999"){
								$quesList = $quesList." style='display:none;'";
							}
							$quesList = $quesList."><a href='".$page."' onclick='return othrQues(".$nextQuesNo.")'>Next</a></li></ul>";
						}
						
						if($sentQuesNo == $QuesNo)
						{
							$selectedQuesCount = $quesCount;
							$sql2 = "SELECT `ques_det`.`qd_ques_desc`,
											`ques_det`.`qd_opt_a`,
											`ques_det`.`qd_opt_b`,
											`ques_det`.`qd_opt_c`,
											`ques_det`.`qd_opt_d`,
											`ques_det`.`qd_correct_opt`,
											`ques_det`.`qd_soln`
									   FROM `ques_det`
									  WHERE `ques_det`.`qd_exam_id` = '".$testId."'
										AND `ques_det`.`qd_ques_no` = '".$QuesNo."'
										AND `qd_del_ind` != 'Y'
									 ";
							$result2=mysqli_query($con, $sql2);
							if($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								$quesDesc = $row2['qd_ques_desc'];
								$optA = $row2['qd_opt_a'];
								$optB = $row2['qd_opt_b'];
								$optC = $row2['qd_opt_c'];
								$optD = $row2['qd_opt_d'];
								$correctOpt = $row2['qd_correct_opt'];
								$soln = $row2['qd_soln'];
							}
						}
					}
					$quesEditAdd = "E";
					if($quesCount == 0){
						$selectedQuesCount = 1;
						$quesEditAdd = "A";
						$sentQuesNo = "999999999999";
					} else if($sentQuesNo == "999999999999"){
						$selectedQuesCount = ($quesCount + 1);
						$quesEditAdd = "A";
					}
					?>
						<form name="FormName" id="<?php if($quesEditAdd=="A"){echo "Add";}else{echo "Edit";} ?>QuesForm" action="<?php echo $page; ?>" method="post" onSubmit='return editQues()'>
						<input type="hidden" name="QuesNo" value="<?php echo $QuesNo; ?>" />
						<input type="hidden" name="CurrQuesNo" id="currques" value="<?php echo $sentQuesNo; ?>" />
						<input type="hidden" name="TestId" value="<?php echo $testId; ?>" />
						<table>
							<tbody>
								<tr style="background-color:#e7e7e7;">
									<td colspan="2">
										<h4>Question #<?php echo $selectedQuesCount;?>: </h4>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<label class="ques-label" for="quesdesc">Question: </label>
										<textarea style="height:200px;" name="QuesDesc" placeholder="Question" required="" id="quesdesc"><?php if(isset($_POST['QuesDesc'])){echo $_POST['QuesDesc'];}else{echo $quesDesc;} ?></textarea>
									</td>
								</tr>
								<tr>
									<td>
										<label class="ques-label" for="opt-a">Option A: </label>
										<textarea name="OptA" placeholder="Option A" required="" id="opt-a"><?php if(isset($_POST['OptA'])){echo $_POST['OptA'];}else{echo $optA;} ?></textarea>
									</td>
									<td>
										<label class="ques-label" for="opt-b">Option B: </label>
										<textarea name="OptB" placeholder="Option B" required="" id="opt-b"><?php if(isset($_POST['OptB'])){echo $_POST['OptB'];}else{echo $optB;} ?></textarea>
									</td>
								</tr>
								<tr>
									<td>
										<label class="ques-label" for="opt-c">Option C: </label>
										<textarea name="OptC" placeholder="Option C" required="" id="opt-c"><?php if(isset($_POST['OptC'])){echo $_POST['OptC'];}else{echo $optC;} ?></textarea>
									</td>
									<td>
										<label class="ques-label" for="opt-d">Option D: </label>
										<textarea name="OptD" placeholder="Option D" required="" id="opt-d"><?php if(isset($_POST['OptD'])){echo $_POST['OptD'];}else{echo $optD;} ?></textarea>
									</td>
								</tr>
								<tr>
									<td colspan="2" align="left">
										<label class="ques-label">Correct Option: </label>
										<input name='ansOpt' type='radio' id='optA' value='A'<?php if(isset($_POST['ansOpt'])) { if($_POST['ansOpt']=="A") {echo " checked";} } else {if($correctOpt=="A"){echo " checked";}} ?> />
										<label for='optA'>(A) </label>
										<input name='ansOpt' type='radio' id='optB' value='B'<?php if(isset($_POST['ansOpt'])) { if($_POST['ansOpt']=="B") {echo " checked";} } else {if($correctOpt=="B"){echo " checked";}} ?> />
										<label for='optB'>(B) </label>
										<input name='ansOpt' type='radio' id='optC' value='C'<?php if(isset($_POST['ansOpt'])) { if($_POST['ansOpt']=="C") {echo " checked";} } else {if($correctOpt=="C"){echo " checked";}} ?> />
										<label for='optC'>(C) </label>
										<input name='ansOpt' type='radio' id='optD' value='D'<?php if(isset($_POST['ansOpt'])) { if($_POST['ansOpt']=="D") {echo " checked";} } else {if($correctOpt=="D"){echo " checked";}} ?> />
										<label for='optD'>(D) </label>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<label class="ques-label" for="soln">Solution: </label>
										<textarea name="QuesSol" placeholder="Solution" id="soln"><?php if(isset($_POST['QuesSol'])){echo $_POST['QuesSol'];}else{echo $soln;} ?></textarea>
									</td>
								</tr>
								<?php if($ErrQ!="") { echo "<tr><td colspan='2' align='center' id='ErrQ'>".$ErrQ."</td></tr>"; } ?>
							</tbody>
							</table>
							<table>
							<tbody>
								<tr>
									<td colspan="2" align="center">
										<button type="submit"  id="updateques" name="UpdateTest" value="<?php if($quesEditAdd=="A"){echo "Add";}else{echo "Edit";} ?>Ques"><?php if($quesEditAdd=="A"){echo "Add";}else{echo "Update";} ?> Question</button>
									</td>
								</tr>
							</tbody>
							</table>
							</form>
							<?php if($quesEditAdd=="E"){ ?>
							<table>
							<tbody>
								<tr>
									<td colspan="2" align="center">
									<form name="FormName" id="DelQuesForm" action="<?php echo $page; ?>" method="post" onSubmit='return delQues()'>
										<input type="hidden" name="QuesNo" value="<?php echo $prevQuesNo; ?>" />
										<input type="hidden" name="CurrQuesNo" id="currques" value="<?php echo $sentQuesNo; ?>" />
										<input type="hidden" name="TestId" value="<?php echo $testId; ?>" />
										<button type="submit"  id="delques" name="DelQues" value="DeleteQues">Delete Question</button>
									</form>
									</td>
								</tr>
							</tbody>
							</table>
							<?php } ?>
					<?php 
					echo $quesList;
				}
			}
		}
		mysqli_close($con);
	}
}
?>
<?php if($ErrQ !="") { 
?>
	<span style="display:none;" id="errInQues"><?php echo $ErrQ; ?></span>
<?php 
} ?>