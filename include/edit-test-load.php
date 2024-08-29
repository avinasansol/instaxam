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
					$Err = "Please provide a valid ";
					$maxTestCountCat1 = 0;
					$maxTestCountCat2 = 0;
					$maxTestCountCat3 = 0;
					$TestCategory = "";
					$TestCategory2 = "";
					$TestCategory3 = "";
					$TestDesc = "";
					$Syllabus = "";
					$AvailableFor = "";
					$RetakeCount = "";
					$TimePer10Questions = "";
					$MarksPerQuestions = "";
					$NegatieMarking = "";
					
					if( (isset($_POST['FormName'])) && ($_POST['FormName']=="EditTestForm") && (isset($_POST['TestCategory'])) && (isset($_POST['TestDesc'])) && (isset($_POST['Syllabus'])) && (isset($_POST['AvailableFor'])) && (isset($_POST['RetakeCount'])) && (isset($_POST['TimePer10Questions'])) && (isset($_POST['MarksPerQuestions'])) && (isset($_POST['NegatieMarking'])) )
					{
						if($_POST['TestCategory']=="") {
							$Err = $Err."test category, ";
						} else {
							$sql="SELECT `esc1_sub_cat1_name`,
										 `esc1_max_test_count`
									FROM `exam_sub_cat1` 
								   WHERE `esc1_sub_cat1_id` = '".$_POST['TestCategory']."'
								 ";
							$result=mysqli_query($con, $sql);
							if($row=mysqli_fetch_array($result))
							{
								$maxTestCountCat1 = ( (int)$row['esc1_max_test_count'] + 1 );
								$TestCategory = $_POST['TestCategory'];
								if( (isset($_POST['SubCategory2'])) && ($_POST['SubCategory2']!="") && ($_POST['SubCategory2']!="undefined") ) {
									$sql="SELECT `esc2_max_test_count` 
											FROM `exam_sub_cat2` 
										   WHERE `esc2_sub_cat2_id` = '".$_POST['SubCategory2']."'
										 ";
									$result=mysqli_query($con, $sql);
									if($row=mysqli_fetch_array($result))
									{
										$TestCategory2 = $_POST['SubCategory2'];
										$maxTestCountCat2 = ( (int)$row['esc2_max_test_count'] + 1 );
										if( (isset($_POST['SubCategory3'])) && ($_POST['SubCategory3']!="") && ($_POST['SubCategory3']!="undefined") ) {
											$sql="SELECT `esc3_max_test_count` 
													FROM `exam_sub_cat3` 
												   WHERE `esc3_sub_cat3_id` = '".$_POST['SubCategory3']."'
												 ";
											$result=mysqli_query($con, $sql);
											if($row=mysqli_fetch_array($result))
											{
												$maxTestCountCat3 = ( (int)$row['esc3_max_test_count'] + 1 );
												$TestCategory3 = $_POST['SubCategory3'];
											} else {
												$Err = $Err."lower level sub category, ";
											}
										}
									} else 
									{
										$Err = $Err."sub category, ";
									}
								}
							} else 
							{
								$Err = $Err."test category, ";
							}
						}
						
						if($_POST['TestDesc']=="") {
							$Err = $Err."test description, ";
						}
						
						if($_POST['Syllabus']=="") {
							$Err = $Err."syllabus, ";
						} else {
							$Syllabus = mysqli_real_escape_string($con, substr(htmlentities($_POST['Syllabus']),0,2000));
						}
						
						if( ($_POST['AvailableFor']!="A") && ($_POST['AvailableFor']!="S") ) {
							$Err = $Err."available for, ";
						} else {
							$AvailableFor = $_POST['AvailableFor'];
						}
						
						if( ($_POST['RetakeCount']!="1") && ($_POST['RetakeCount']!="2") && ($_POST['RetakeCount']!="3") && ($_POST['RetakeCount']!="4") && ($_POST['RetakeCount']!="5") && ($_POST['RetakeCount']!="10") ) {
							$Err = $Err."retake count, ";
						} else {
							$RetakeCount = $_POST['RetakeCount'];
						}
						
						if( ($_POST['TimePer10Questions']!="6") && ($_POST['TimePer10Questions']!="8") && ($_POST['TimePer10Questions']!="9") && ($_POST['TimePer10Questions']!="12") && ($_POST['TimePer10Questions']!="15") && ($_POST['TimePer10Questions']!="18") ) {
							$Err = $Err."time, ";
						} else {
							$TimePer10Questions = $_POST['TimePer10Questions'];
						}
						
						if( ($_POST['MarksPerQuestions']!="1") && ($_POST['MarksPerQuestions']!="2") && ($_POST['MarksPerQuestions']!="5") && ($_POST['MarksPerQuestions']!="10") ) {
							$Err = $Err."marks per questions, ";
						} else {
							$MarksPerQuestions = $_POST['MarksPerQuestions'];
						}
						
						if( ($_POST['NegatieMarking']!="N") && ($_POST['NegatieMarking']!="Y") ) {
							$Err = $Err."negatie marking, ";
						} else {
							$NegatieMarking = $_POST['NegatieMarking'];
						}
						
						if($Err!="Please provide a valid ")
						{
							$Err[(strlen($Err)-2)]=".";
						} else {
							if(strlen($_POST['TestDesc'])<25) {
								$Err = "Test Description must contain at least 25 letters.";
							} else {
								$TestDesc = mysqli_real_escape_string($con, substr(htmlentities($_POST['TestDesc']),0,2000));
								$sql100 = "";
								if( $maxTestCountCat3 > 0 ) {
									$sql100 = "UPDATE `exam_det` SET `ed_sub_cat2_id` = '".$TestCategory2."', `ed_sub_cat3_id` = '".$TestCategory3."' WHERE `ed_exam_id` = '".$testId."'";
								} else if( $maxTestCountCat2 > 0 ) {
									$sql100 = "UPDATE `exam_det` SET `ed_sub_cat2_id` = '".$TestCategory2."', `ed_sub_cat3_id` = NULL WHERE `ed_exam_id` = '".$testId."'";
								} else if( $maxTestCountCat1 > 0 ) {
									$sql100 = "UPDATE `exam_det` SET `ed_sub_cat2_id` = NULL, `ed_sub_cat3_id` = NULL WHERE `ed_exam_id` = '".$testId."'";
								}
								
								$sql99 = "UPDATE `exam_det` 
											 SET `ed_sub_cat1_id` = '".$TestCategory."', `ed_avlbl_for` = '".$AvailableFor."', `ed_max_retake` = '".$RetakeCount."', `ed_marks_per_ques` = '".$MarksPerQuestions."', `ed_time_per10_ques` = '".$TimePer10Questions."', `ed_negatie_mark` = '".$NegatieMarking."', `ed_syllabus` = '".$Syllabus."', `ed_exam_desc` = '".$TestDesc."'
										   WHERE `ed_exam_id` = '".$testId."'
										 ";
								if (mysqli_query($con, $sql99) && mysqli_query($con, $sql100)) {
									$Err = "The test has been updated successfully. <a href='test-details/".$testId."'>View Test Details</a>";
									$_POST = array();
								} else  {
									$Err = "Error: Failure while updating the test.";
								}
							}
						}
					}
						
					if($Err=="Please provide a valid ")
					{
						$Err = "";
					}
					
					$sql="SELECT `ed_sub_cat1_id`,
								 `ed_sub_cat2_id`,
								 `ed_sub_cat3_id`,
								 `ed_avlbl_for`,
								 `ed_max_retake`,
								 `ed_marks_per_ques`,
								 `ed_time_per10_ques`,
								 `ed_negatie_mark`,
								 `ed_syllabus`,
								 `ed_exam_desc`
							FROM `exam_det` 
						   WHERE `ed_created_by`='".$loggedUserId."'
							 AND `ed_exam_id` = '".$testId."'
						 ";
					$result=mysqli_query($con, $sql);
					if($row=mysqli_fetch_array($result))
					{
						$TestCategory = $row['ed_sub_cat1_id'];
						$TestCategoryName = "";
						if($TestCategory!="") {
							$sql1="SELECT `esc1_sub_cat1_name`
									 FROM `exam_sub_cat1` 
								    WHERE `esc1_sub_cat1_id` = '".$TestCategory."'
								  ";
							$result1=mysqli_query($con, $sql1);
							if($row1=mysqli_fetch_array($result1)){
								$TestCategoryName = $row1['esc1_sub_cat1_name'];
							}
						}
						
						$TestCategory2 = $row['ed_sub_cat2_id'];
						$TestCategory2Name = "";
						if($TestCategory2!="") {
							$sql1="SELECT `esc2_sub_cat2_name`
									 FROM `exam_sub_cat2` 
								    WHERE `esc2_sub_cat2_id` = '".$TestCategory2."'
								  ";
							$result1=mysqli_query($con, $sql1);
							if($row1=mysqli_fetch_array($result1)){
								$TestCategory2Name = $row1['esc2_sub_cat2_name'];
							}
						}
						
						$TestCategory3 = $row['ed_sub_cat3_id'];
						$TestCategory3Name = "";
						if($TestCategory3!="") {
							$sql1="SELECT `esc3_sub_cat3_name`
									 FROM `exam_sub_cat3` 
								    WHERE `esc3_sub_cat3_id` = '".$TestCategory3."'
								  ";
							$result1=mysqli_query($con, $sql1);
							if($row1=mysqli_fetch_array($result1)){
								$TestCategory3Name = $row1['esc3_sub_cat3_name'];
							}
						}
						
						$TestDesc = $row['ed_exam_desc'];
						$Syllabus = $row['ed_syllabus'];
						$AvailableFor = $row['ed_avlbl_for'];
						$RetakeCount = $row['ed_max_retake'];
						$TimePer10Questions = $row['ed_time_per10_ques'];
						$MarksPerQuestions = $row['ed_marks_per_ques'];
						$NegatieMarking = $row['ed_negatie_mark'];
					}
?>
						<form name="EditTestForm" id="EditTestForm" action="<?php echo $page; ?>" method="post" onSubmit='return editTest()'>
						<table>
							<input type="hidden" name="FormName" value="EditTestForm" />
							<input type="hidden" name="TestId" value="<?php echo $testId; ?>" />
							<tbody>
								<tr>
									<td class="left-col">Test Category: </td>
									<td>
									<select name='TestCategory' id='category' onChange='return loadSubCat2()'>
										<option value='<?php echo $TestCategory; ?>'><?php echo $TestCategoryName; ?></option>
										<?php 
											$sql="SELECT `ec_cat_name`,
														 `esc1_sub_cat1_name`,
														 `esc1_sub_cat1_id`
													FROM `exam_cat`,
														 `exam_sub_cat1`
												   WHERE `esc1_cat_id` = `ec_cat_id`
												   ORDER BY `ec_order` ASC, `esc1_order` ASC
												 ";
											$result=mysqli_query($con, $sql);
											while($row=mysqli_fetch_array($result))
											{
												$catDesc = "";
												if($row['ec_cat_name']==$row['esc1_sub_cat1_name']) {
													$catDesc = $row['esc1_sub_cat1_name'];
												} else {
													$catDesc = $row['ec_cat_name']." -> ".$row['esc1_sub_cat1_name'];
												}
												echo "<option value='".$row['esc1_sub_cat1_id']."'>".$catDesc."</option>";
											}
										?>
									</select>
									<span id='subcat'>
										<?php 
											$sql="SELECT `esc2_sub_cat2_name`,
														 `esc2_sub_cat2_id`
													FROM `exam_sub_cat2`
												   WHERE `esc2_sub_cat1_id` = '".$TestCategory."'
												   ORDER BY `esc2_order` ASC
												 ";
											$result=mysqli_query($con, $sql);
											$rowCount = 0;
											while($row=mysqli_fetch_array($result))
											{
												$rowCount++;
												if($rowCount==1){
													echo "<select name='SubCategory2' id='category2' onChange='return loadSubCat3()'>";
													if($TestCategory2!=""){
														echo "<option value='".$TestCategory2."'>".$TestCategory2Name."</option>";
													} else {
														echo "<option value=''>Select Sub Category</option>";
													}
												}
												if($TestCategory2!=$row['esc2_sub_cat2_id']){
													echo "<option value='".$row['esc2_sub_cat2_id']."'>".$row['esc2_sub_cat2_name']."</option>";
												}
											}
											if($rowCount>0){
												echo "</select>";
											}
										?>
									</span>
									<span id='subcat2'>
										<?php 
											$sql="SELECT `esc3_sub_cat3_name`,
														 `esc3_sub_cat3_id`
													FROM `exam_sub_cat3`
												   WHERE `esc3_sub_cat2_id` = '".$TestCategory2."'
												   ORDER BY `esc3_order` ASC
												 ";
											$result=mysqli_query($con, $sql);
											$rowCount = 0;
											while($row=mysqli_fetch_array($result))
											{
												$rowCount++;
												if($rowCount==1){
													echo "<select name='SubCategory3' id='category3'>";
													if($TestCategory3!=""){
														echo "<option value='".$TestCategory3."'>".$TestCategory3Name."</option>";
													} else {
														echo "<option value=''>Select Lower Level Sub Category</option>";
													}
												}
												if($TestCategory3!=$row['esc3_sub_cat3_id']){
													echo "<option value='".$row['esc3_sub_cat3_id']."'>".$row['esc3_sub_cat3_name']."</option>";
												}
											}
											if($rowCount>0){
												echo "</select>";
											}
										?>
									</span>
									</td>
								</tr>
								<tr>
									<td class="left-col">Description: </td>
									<td><textarea name="TestDesc" placeholder="Test Description" required="" id="desc"><?php echo $TestDesc; ?></textarea></td>
								</tr>
								<tr>
									<td class="left-col">Syllabus: </td>
									<td><textarea name="Syllabus" placeholder="Test Syllabus" required="" id="syll"><?php echo $Syllabus; ?></textarea></td>
								</tr>
								<tr>
									<td class="left-col">Available For: </td>
									<td>
									<select name="AvailableFor" id="available">
										<?php if($AvailableFor=="S") { ?>
										<option value="S">Selected Users</option>
										<option value="A">All Users</option>
										<?php } else { ?>
										<option value="A">All Users</option>
										<option value="S">Selected Users</option>
										<?php } ?>
									</select>
									</td>
								</tr>
								<tr>
									<td class="left-col">Max Retake Count: </td>
									<td>
									<select name="RetakeCount" id="retake">
										<option value="<?php echo $RetakeCount;?>"><?php echo $RetakeCount;?></option>
										<?php if($RetakeCount!="1") { ?><option value="1">1</option><?php } ?>
										<?php if($RetakeCount!="2") { ?><option value="2">2</option><?php } ?>
										<?php if($RetakeCount!="3") { ?><option value="3">3</option><?php } ?>
										<?php if($RetakeCount!="4") { ?><option value="4">4</option><?php } ?>
										<?php if($RetakeCount!="5") { ?><option value="5">5</option><?php } ?>
										<?php if($RetakeCount!="10") { ?><option value="10">10</option><?php } ?>
									</select>
									</td>
								</tr>
								<tr>
									<td class="left-col">Time Per 10 Questions: </td>
									<td>
									<select name="TimePer10Questions" id="time">
										<option value="<?php echo $TimePer10Questions;?>"><?php echo $TimePer10Questions;?></option>
										<?php if($TimePer10Questions!="6") { ?><option value="6">6 mins</option><?php } ?>
										<?php if($TimePer10Questions!="8") { ?><option value="8">8 mins</option><?php } ?>
										<?php if($TimePer10Questions!="9") { ?><option value="9">9 mins</option><?php } ?>
										<?php if($TimePer10Questions!="12") { ?><option value="12">12 mins</option><?php } ?>
										<?php if($TimePer10Questions!="15") { ?><option value="15">15 mins</option><?php } ?>
										<?php if($TimePer10Questions!="18") { ?><option value="18">18 mins</option><?php } ?>
									</select>
									</td>
								</tr>
								<tr>
									<td class="left-col">Marks Per Questions: </td>
									<td>
									<select name="MarksPerQuestions" id="marks">
										<option value="<?php echo $MarksPerQuestions;?>"><?php echo $MarksPerQuestions;?></option>
										<?php if($MarksPerQuestions!="1") { ?><option value="1">1</option><?php } ?>
										<?php if($MarksPerQuestions!="2") { ?><option value="2">2</option><?php } ?>
										<?php if($MarksPerQuestions!="5") { ?><option value="5">5</option><?php } ?>
										<?php if($MarksPerQuestions!="10") { ?><option value="10">10</option><?php } ?>
									</select>
									</td>
								</tr>
								<tr>
									<td class="left-col">Negatie Marking: </td>
									<td>
									<select name="NegatieMarking" id="negatiemark">
										<?php if($NegatieMarking=="N") { ?>
										<option value="N">No</option>
										<option value="Y">Yes</option>
										<?php } else { ?>
										<option value="Y">Yes</option>
										<option value="N">No</option>
										<?php } ?>
									</select>
									</td>
								</tr>
								<?php if($Err!="") { echo "<tr><td colspan='2' align='center' id='Err'>".$Err."</td></tr>"; } ?>
							</tbody>
						</table>
						<table>
							<tbody>
								<tr>
									<td colspan="2" align="center">
										<button type="submit"  id="Edit-Test" name="EditTest" value="Edit Test">Update Test</button>
									</td>
								</tr>
							</tbody>
						</table>
						</form>
<?php 
				}
			}
		}
		mysqli_close($con);
	}
}
?>
