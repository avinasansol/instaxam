<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "create-test.php";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	} 
	$loggedUserId = $_SESSION['LogdUsrDet'][1];
	include("include/connect-database.php");
	$sql="SELECT `ud_creator_access` 
			FROM `user_det` 
		   WHERE `ud_user_id`='".$loggedUserId."'
		 ";
	$result=mysqli_query($con, $sql);
	if($row=mysqli_fetch_array($result))
	{
		if($row['ud_creator_access']!="Y"){
			mysqli_close($con);
			header("Location: creator-access-req.php");
		}
	}
	
	$Err = "Please provide a valid ";
	$validCat = "";
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
	
	$testAlreadyCreated = 0;
	$alreadyCreatedTestId = "";
	$sql="SELECT `ed_exam_id` 
		    FROM `exam_det` 
		   WHERE `ed_created_by` = '".$loggedUserId."' 
		     AND DATE(`ed_created_on`) = CURDATE()
		 ";
	$result=mysqli_query($con, $sql);
	if($row=mysqli_fetch_array($result)) {
		$testAlreadyCreated = 1;
		$alreadyCreatedTestId = $row['ed_exam_id'];
	} else if( (isset($_POST['FormName'])) && ($_POST['FormName']=="CreateNewTestForm") && (isset($_POST['TestCategory'])) && (isset($_POST['TestDesc'])) && (isset($_POST['Syllabus'])) && (isset($_POST['AvailableFor'])) && (isset($_POST['RetakeCount'])) && (isset($_POST['TimePer10Questions'])) && (isset($_POST['MarksPerQuestions'])) && (isset($_POST['NegatieMarking'])) )
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
				$validCat = $row['esc1_sub_cat1_name'];
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
				$examId = "";
				$catClause = "NULL, NULL,";
				$sql1 = "";
				if( $maxTestCountCat3 > 0 ) {
					$examId = $TestCategory3.sprintf('%05d', $maxTestCountCat3);
					$catClause = "'".$TestCategory2."', '".$TestCategory3."',";
					$sql1 = "UPDATE `exam_sub_cat3` SET `esc3_max_test_count` = (`esc3_max_test_count` + 1 ) WHERE `esc3_sub_cat3_id` = '".$TestCategory3."'";
				} else if( $maxTestCountCat2 > 0 ) {
					$examId = $TestCategory2.sprintf('%07d', $maxTestCountCat2);
					$catClause = "'".$TestCategory2."', NULL,";
					$sql1 = "UPDATE `exam_sub_cat2` SET `esc2_max_test_count` = (`esc2_max_test_count` + 1 ) WHERE `esc2_sub_cat2_id` = '".$TestCategory2."'";
				} else if( $maxTestCountCat1 > 0 ) {
					$examId = $TestCategory.sprintf('%09d', $maxTestCountCat1);
					$catClause = "NULL, NULL,";
					$sql1 = "UPDATE `exam_sub_cat1` SET `esc1_max_test_count` = (`esc1_max_test_count` + 1 ) WHERE `esc1_sub_cat1_id` = '".$TestCategory."'";
				}
				
				$sql99 = "INSERT INTO `exam_det` (`ed_exam_id`, `ed_sub_cat1_id`, `ed_sub_cat2_id`, `ed_sub_cat3_id`, `ed_created_by`, `ed_status`, `ed_avlbl_for`, `ed_max_retake`, `ed_marks_per_ques`, `ed_time_per10_ques`, `ed_negatie_mark`, `ed_syllabus`, `ed_exam_desc`, `ed_created_on`) 
						  VALUES ('".$examId."', '".$TestCategory."', ".$catClause." '".$loggedUserId."', 'D', '".$AvailableFor."', '".$RetakeCount."', '".$MarksPerQuestions."', '".$TimePer10Questions."', '".$NegatieMarking."', '".$Syllabus."', '".$TestDesc."', current_timestamp())
						 ";
				if (mysqli_query($con, $sql99)) {
					$Err = "The new test has been created successfully. <a href='test-details.php?test_id=".$examId."'>View Test Details</a>";
					mysqli_query($con, $sql1);
					$validCat = "";
					$_POST = array();
				} else  {
					$Err = "Error: Failure while creating the new test.";
				}
			}
		}
	}
		
	if($Err=="Please provide a valid ")
	{
		$Err = "";
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

    <title>Instaxam.In - Test Creation</title>
	<link rel="icon" type="image/png" href="assets/images/favicon.ico">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/all-style.css">
    <link rel="stylesheet" href="assets/css/owl.css">

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

<style>
input, select {
 border:#000000 solid 1px;
 width:220px;
}
#category {
 border:#000000 solid 1px;
 width:100%;
}
#category2, #category3 {
 border:#000000 solid 1px;
 width:100%;
 margin-top: 10px;
}
textarea {
 border:#000000 solid 1px;
 width:100%;
 min-width:100%;
 max-width:100%;
 height:80px;
 min-height:80px;
 max-height:300px;
}
.left-col {
 width:30%;
 white-space:nowrap;
}
#Err{
 color:#FF3300;
 font-size:12px;
}

#meter_wrapper
{
 border:none;
 margin:0px;
 width:220px;
 height:15px;
}
#meter
{
 width:0px;
 height:15px;
}
#pass_type
{
 font-size:12px;
 margin:0px;
 text-align:center;
 color:grey;
}
#conf_pass
{
 font-size:12px;
 margin:0px;
 text-align:center;
 color:grey;
}
</style>
<?php if($testAlreadyCreated == 1){ ?>
            <section class="tables" style="margin-top:-80px;">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h2>You Have Already Created A Test Today.</h2>
                    </div>
                    <p>More than one test creation is not allowed in a single day. However you can edit an already created test.</p>
                    <p><a href='edit-test-list.php'>Click here</a> to view the list of tests that you have created.</p>
                    <p><a href='test-details.php?test_id=<?php echo $alreadyCreatedTestId;?>'>Click here</a> to view the test that you have created today.</p>
                  </div>
                </div>
              </div>
            </section>
<?php } else { ?>
            <section class="tables" style="margin-top:-80px;">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h2>Create a New Test: </h2>
                    </div>
                    <div class="alternate-table">
						<table>
						<form name="CreateNewTestForm" id="CreateNewTestForm" action="<?php echo $page; ?>" method="post" onSubmit='return createTest()'>
							<input type="hidden" name="FormName" value="CreateNewTestForm" />
							<tbody>
								<tr>
									<td class="left-col">Test Category: </td>
									<td>
									<select name='TestCategory' id='category' onChange='return loadSubCat2()'>
										<option value='<?php if($validCat != "") { echo $_POST['TestCategory']; } ?>'><?php if($validCat != "") { echo $validCat; } else { echo "Select Test Category"; } ?></option>
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
									<?php if($validCat != "") { include("include/load-sub-cat.php"); include("include/connect-database.php"); } else { ?></span><span id='subcat2'><?php } ?>
									</span>
									</td>
								</tr>
								<tr>
									<td class="left-col">Description: </td>
									<td><textarea name="TestDesc" placeholder="Test Description" required="" id="desc"><?php if( (isset($_POST['TestDesc'])) && ($_POST['TestDesc']!="") ) { echo $_POST['TestDesc']; } ?></textarea></td>
								</tr>
								<tr>
									<td class="left-col">Syllabus: </td>
									<td><textarea name="Syllabus" placeholder="Test Syllabus" required="" id="syll"><?php if( (isset($_POST['Syllabus'])) && ($_POST['Syllabus']!="") ) { echo $_POST['Syllabus']; } ?></textarea></td>
								</tr>
								<tr>
									<td class="left-col">Available For: </td>
									<td>
									<select name="AvailableFor" id="available">
										<?php if( (isset($_POST['AvailableFor'])) && ($_POST['AvailableFor']=="S") ) { ?>
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
										<?php if(isset($_POST['RetakeCount'])) { ?><option value="<?php echo $_POST['RetakeCount'];?>"><?php echo $_POST['RetakeCount'];?></option><?php } ?>
										<?php if(!(isset($_POST['RetakeCount']) && ($_POST['RetakeCount']=="1"))) { ?><option value="1">1</option><?php } ?>
										<?php if(!(isset($_POST['RetakeCount']) && ($_POST['RetakeCount']=="2"))) { ?><option value="2">2</option><?php } ?>
										<?php if(!(isset($_POST['RetakeCount']) && ($_POST['RetakeCount']=="3"))) { ?><option value="3">3</option><?php } ?>
										<?php if(!(isset($_POST['RetakeCount']) && ($_POST['RetakeCount']=="4"))) { ?><option value="4">4</option><?php } ?>
										<?php if(!(isset($_POST['RetakeCount']) && ($_POST['RetakeCount']=="5"))) { ?><option value="5">5</option><?php } ?>
										<?php if(!(isset($_POST['RetakeCount']) && ($_POST['RetakeCount']=="10"))) { ?><option value="10">10</option><?php } ?>
									</select>
									</td>
								</tr>
								<tr>
									<td class="left-col">Time Per 10 Questions: </td>
									<td>
									<select name="TimePer10Questions" id="time">
										<?php if(isset($_POST['TimePer10Questions'])) { ?><option value="<?php echo $_POST['TimePer10Questions'];?>"><?php echo $_POST['TimePer10Questions'];?></option><?php } ?>
										<?php if(!(isset($_POST['TimePer10Questions']) && ($_POST['TimePer10Questions']=="6"))) { ?><option value="6">6 mins</option><?php } ?>
										<?php if(!(isset($_POST['TimePer10Questions']) && ($_POST['TimePer10Questions']=="8"))) { ?><option value="8">8 mins</option><?php } ?>
										<?php if(!(isset($_POST['TimePer10Questions']) && ($_POST['TimePer10Questions']=="9"))) { ?><option value="9">9 mins</option><?php } ?>
										<?php if(!(isset($_POST['TimePer10Questions']) && ($_POST['TimePer10Questions']=="12"))) { ?><option value="12">12 mins</option><?php } ?>
										<?php if(!(isset($_POST['TimePer10Questions']) && ($_POST['TimePer10Questions']=="15"))) { ?><option value="15">15 mins</option><?php } ?>
										<?php if(!(isset($_POST['TimePer10Questions']) && ($_POST['TimePer10Questions']=="18"))) { ?><option value="18">18 mins</option><?php } ?>
									</select>
									</td>
								</tr>
								<tr>
									<td class="left-col">Marks Per Questions: </td>
									<td>
									<select name="MarksPerQuestions" id="marks">
										<?php if(isset($_POST['MarksPerQuestions'])) { ?><option value="<?php echo $_POST['MarksPerQuestions'];?>"><?php echo $_POST['MarksPerQuestions'];?></option><?php } ?>
										<?php if(!(isset($_POST['MarksPerQuestions']) && ($_POST['MarksPerQuestions']=="1"))) { ?><option value="1">1</option><?php } ?>
										<?php if(!(isset($_POST['MarksPerQuestions']) && ($_POST['MarksPerQuestions']=="2"))) { ?><option value="2">2</option><?php } ?>
										<?php if(!(isset($_POST['MarksPerQuestions']) && ($_POST['MarksPerQuestions']=="5"))) { ?><option value="5">5</option><?php } ?>
										<?php if(!(isset($_POST['MarksPerQuestions']) && ($_POST['MarksPerQuestions']=="10"))) { ?><option value="10">10</option><?php } ?>
									</select>
									</td>
								</tr>
								<tr>
									<td class="left-col">Negatie Marking: </td>
									<td>
									<select name="NegatieMarking" id="negatiemark">
										<?php if( (isset($_POST['NegatieMarking'])) && ($_POST['NegatieMarking']=="N") ) { ?>
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
								<tr>
									<td colspan="2" align="center">
										<button type="submit"  id="Create-Test" name="CreateNewTest" value="Create Test">Create New Test</button>
									</td>
								</tr>
								<tr>
									<td colspan="2" align="center" id="htl">
									</td>
								</tr>
							</tbody>
						</form>
						</table>
				    </div>
                  </div>
                </div>
              </div>
            </section>
<?php } ?>		

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
			alert("<?php $arr = explode("<", $Err, 2); echo $arr[0]; ?>");
			location.replace("#Err");
		});
		</script>
	<?php 
	} ?>
	
<script type="text/javascript">
function loadSubCat2(){
	var category = $("#category").val();
	document.getElementById("subcat2").innerHTML="";
	$.post('include/load-sub-cat.php', { 
		TestCategory: category
		}, function(newitems){
			document.getElementById("subcat").innerHTML=newitems;
	});
	return false;
};
function loadSubCat3(){
	var category2 = $("#category2").val();
	$.post('include/load-sub-cat.php', { 
		SubCategory2: category2
		}, function(newitems){
			document.getElementById("subcat2").innerHTML=newitems;
	});
	return false;
};
function createTest(){
	var redirectUrl = "create-test.php";
	var form = $('<form action="' + redirectUrl + '" method="post">' +
	'<input type="hidden" name="FormName" value="CreateNewTestForm" />' +
	'<input type="hidden" name="TestCategory" value="' + $("#category").val() + '" />' +
	'<input type="hidden" name="TestDesc" value="' + $("#desc").val() + '" />' +
	'<input type="hidden" name="Syllabus" value="' + $("#syll").val() + '" />' +
	'<input type="hidden" name="AvailableFor" value="' + $("#available").val() + '" />' +
	'<input type="hidden" name="RetakeCount" value="' + $("#retake").val() + '" />' +
	'<input type="hidden" name="TimePer10Questions" value="' + $("#time").val() + '" />' +
	'<input type="hidden" name="MarksPerQuestions" value="' + $("#marks").val() + '" />' +
	'<input type="hidden" name="NegatieMarking" value="' + $("#negatiemark").val() + '" />' +
	'<input type="hidden" name="SubCategory2" value="' + $("#category2").val() + '" />' +
	'<input type="hidden" name="SubCategory3" value="' + $("#category3").val() + '" />' +
	'</form>');
	$('body').append(form);
	$(form).submit();
	return false;
};
</script>
</body>
</html>
