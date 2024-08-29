<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "edit-test.php";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	} 
	
	$testId = "";
	if(!isset($_POST['TestId'])){
		header("Location: edit-test-list.php");
	} else {
		$testId = $_POST['TestId'];
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
		} else {
			$sql="SELECT `ed_exam_id` 
					FROM `exam_det` 
				   WHERE `ed_created_by`='".$loggedUserId."'
					 AND `ed_exam_id` = '".$testId."'
				 ";
			$result=mysqli_query($con, $sql);
			if(mysqli_fetch_array($result))
			{
				$sql="UPDATE `exam_det` SET `ed_status` = 'D'
					   WHERE `ed_created_by`='".$loggedUserId."'
						 AND `ed_exam_id` = '".$testId."'
					 ";
				mysqli_query($con, $sql);
			} else {
				mysqli_close($con);
				header("Location: edit-test-list.php");
			}
		}
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

    <title>Instaxam.In - Edit Test</title>
	<link rel="icon" type="image/png" href="assets/images/favicon.ico">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/all-style.css">
    <link rel="stylesheet" href="assets/css/owl.css">

<script type="text/javascript" src="assets/js/jquery-1.3.2.js" ></script>
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
function editTest(){
	$('#loadMsg').text('Updating test...');
	$('#statusEditLoad').css({'height':'130px','background':'url(assets/images/spinner.gif) no-repeat center'});
	$.post('include/edit-test-load.php', { 
		FormName: "EditTestForm",
		TestCategory: $("#category").val(),
		SubCategory2: $("#category2").val(),
		SubCategory3: $("#category3").val(),
		TestDesc: $("#desc").val(),
		Syllabus: $("#syll").val(),
		AvailableFor: $("#available").val(),
		RetakeCount: $("#retake").val(),
		TimePer10Questions: $("#time").val(),
		MarksPerQuestions: $("#marks").val(),
		NegatieMarking: $("#negatiemark").val(),
		TestId: "<?php echo $testId; ?>"
	}, function(newitems){
		document.getElementById("editTestLoadArea").innerHTML=newitems;
		$('#loadMsg').text('');
		$('#statusEditLoad').css({'background':'none','height':'0px'});
	});
	return false;
};
function delQues(){
	if (confirm('Once deleted, cannot be restored.\nAre you sure you want to delete the question?')) {
		return true;
	}
	return false;
};
function othrQues(quesno){
	$('#loadQues').text('Updating question...');
	$('#statusQuesLoad').css({'height':'130px','background':'url(assets/images/spinner.gif) no-repeat center'});
	$.post('include/edit-ques-load.php', {
		QuesNo: quesno,
		TestId: "<?php echo $testId; ?>"
	}, function(newitems){
		document.getElementById("editQuesLoadArea").innerHTML=newitems;
		$('#loadQues').text('');
		$('#statusQuesLoad').css({'background':'none','height':'0px'});
		location.replace("#editQuesLoadArea");
	});
	return false;
};
function editQues(){
	$('#loadQues').text('Updating question...');
	$('#statusQuesLoad').css({'height':'130px','background':'url(assets/images/spinner.gif) no-repeat center'});
	
	let ansOpt;
	const rbs = document.querySelectorAll('input[name="ansOpt"]');
	for (const rb of rbs) {
		if (rb.checked) {
			ansOpt = rb.value;
			break;
		}
	}
	
	$.post('include/edit-ques-load.php', {
		UpdateTest: $("#updateques").val(),
		CurrQuesNo: $("#currques").val(),
		QuesNo: $("#currques").val(),
		QuesDesc: $("#quesdesc").val(),
		OptA: $("#opt-a").val(),
		OptB: $("#opt-b").val(),
		OptC: $("#opt-c").val(),
		OptD: $("#opt-d").val(),
		ansOpt: ansOpt,
		QuesSol: $("#soln").val(),
		TestId: "<?php echo $testId; ?>"
	}, function(newitems){
		document.getElementById("editQuesLoadArea").innerHTML=newitems;
		$('#loadQues').text('');
		$('#statusQuesLoad').css({'background':'none','height':'0px'});
		var quesErr = $("#errInQues").text();
		if(quesErr != null) {
			alert(quesErr);
		}
	});
	return false;
};
</script>

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
#Err{
 color:#FF3300;
 font-size:12px;
}
#ErrQ{
 color:#FF3300;
 font-size:12px;
}
.ques-label {
 line-height:20px;
 font-weight:bold;
 display:block;
 clear:both;
}
input[type="checkbox"],
input[type="radio"] {
    float: left;
    width: 0px;
    display: inline-block;
    margin-left: -15%;
}
input[type="checkbox"] + label,
input[type="radio"] + label {
    position: relative;
    min-width: 75px;
    width: 24%;
    display: inline-block;
}
input[type="checkbox"] + label:before,
input[type="radio"] + label:before {
    content: '';
    display: inline-block;
    line-height: 30px;
    position: absolute;
    text-align: center;
    width: 30px;
    height: 30px;
    top: 50%;
    right: 10px;
}
#editTestLoadArea tr,
#editQuesLoadArea tr {
	background-color:#ffffff;
}
#editQuesLoadArea ul li {
	margin: 2px;
	font-weight:bold;
}
#editQuesLoadArea ul li a {
	font-size:12px;
}
</style>
            <section class="tables" style="margin-top:-80px;">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h2>Edit Test - <?php echo $testId;?>: </h2>
                    </div>
                    <div class="alternate-table">
						<p id="statusEditLoad"><span id="loadMsg"></span></p>
						<div id="editTestLoadArea">
						<?php 
							include("include/edit-test-load.php");
							include("include/connect-database.php")
						?>
						</div>
				    </div>
                  </div>
                </div>
              </div>
            </section>
			<section class="tables">
			  <div class="container-fluid">
				<div class="row">
				  <div class="col-md-12">
					<div class="alternate-table">
						<p id="statusQuesLoad"><span id="loadQues"></span></p>
						<div id="editQuesLoadArea">
						<?php 
							include("include/edit-ques-load.php");
							include("include/connect-database.php")
						?>
						</div>
					</div>
				  </div>
				</div>
			  </div>
			</section>	
          </div>
		  <div style="width:100%; text-align:center; margin-bottom:100px;">
                <form action="edit-test-from-file.php" method="post">
                    <button type="submit"  name="TestId" value="<?php echo $testId; ?>">Load Questions From File</button>
                </form>
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
	<?php if($ErrQ !="") { 
	?>
		<script>
		$('document').ready(function(){
			alert("<?php echo $ErrQ; ?>");
			location.replace("#editQuesLoadArea");
		});
		</script>
	<?php 
	} ?>
</body>
</html>
