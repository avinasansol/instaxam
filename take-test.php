<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
    }
    if(!isset($_POST['TestId']))
    {
		header("Location: index.php");
    }
	include("include/connect-database.php");
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">

    <title>Instaxam.In - Test</title>
	<link rel="icon" type="image/png" href="assets/images/favicon.ico">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/all-style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/take-test.css">

<script type="text/javascript">
function raiseAccessReq(){
	var TestId = $("#TestId").val();
	$.post('test-access-req.php', { TestId: TestId}, function(newitems){
		alert(newitems);
	});
	return false;
};
function finishTest(){
	if (confirm('Once done, cannot be undone!\nAre you sure you want to finish test?')) {
		$('#status-load-area span').text('Loading.....');
		$('#status-load-area').css({'height':'200px','background':'url(assets/images/spinner.gif) no-repeat center'});
		var TestId = $("#TestId").val();
		$.post('include/take-test-load.php', { 
				TestId: TestId,
				FinishTest: "9999999"
			}, 
			function(newitems){
				$('#test-load-area').text('');
				$('#test-load-area').append(newitems);
				updateStatus();
			}
		);
		location.replace("#header");
	}
	return false;
};
function clearAnswer(ques){
	$('input[name=ansOpt]').attr('checked',false);
    $("#optA").prop("checked", false);
    $("#optB").prop("checked", false);
    $("#optC").prop("checked", false);
    $("#optD").prop("checked", false);
	loadTest(ques);
	return false;
};
function updateStatus(){
	$('#status-load-area span').text('');
	$('#status-load-area').css({'background':'none','height':'0px'});
};
var MarkLater = 'N';
function markLater(ques){
	$('input[name=ansOpt]').attr('checked',false);
    $("#optA").prop("checked", false);
    $("#optB").prop("checked", false);
    $("#optC").prop("checked", false);
    $("#optD").prop("checked", false);
	MarkLater = 'Y';
	loadTest(ques);
	MarkLater = 'N';
	return false;
};
function loadTest(ques){
	var TestId = $("#TestId").val();
	var ansNo = $("#ansNo").val();
	var QuesNo = ques;
	
	let ansOpt;
	const rbs = document.querySelectorAll('input[name="ansOpt"]');
	for (const rb of rbs) {
		if (rb.checked) {
			ansOpt = rb.value;
			break;
		}
	}
	
	$.post('include/take-test-load.php', { 
			TestId: TestId,
			QuesNo: QuesNo,
			ansNo: ansNo,
			ansOpt: ansOpt,
			MarkLater: MarkLater
		}, 
		function(newitems){
			$('#test-load-area').text('');
			$('#test-load-area').append(newitems);
		}
	);
	location.replace("#header");
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
				<p id="status-load-area"><span ></span></p>
				<div id="test-load-area">
				
				<?php 
				include("include/take-test-load.php");
				?>
				
				</div>
			
			</div>
			
			<?php 
			include("include/connect-database.php");
			include("include/footer.php");
			?>
		
		</div>
		
	</div>

  <!-- Scripts -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/transition.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/custom.js"></script>
	<script>
	var seco = 0;
	$('document').ready(function(){
		setTimeout('decTime();', (1000));
	});
	function updateTime(){
		var TestId = $("#TestId").val();
		$.post('include/update-test-time.php', { 
				TestId: TestId
			}, 
			function(newitems){
			}
		);
	};
	function decTime(){
		seco++;
		if(seco==1){
			updateTime();
			seco = 0;
		}
		var existingTime = $("#remainingTime").text();
		var existingHr = 0;
		var existingMin = 0;
		var existingSec = 0;
		existingHr = parseInt(existingTime.substr(0,2));
		existingMin = parseInt(existingTime.substr(3,2));
		existingSec = parseInt(existingTime.substr(6,2));
		
		var remHr = 0;
		var remMin = 0;
		var remSec = 0;
		if(existingSec > 0) {
			remSec = (existingSec - 1);
			remMin = existingMin;
			remHr = existingHr;
		} else {
			remSec = (existingSec + 60 - 1);
			if(existingMin > 0) {
				remMin = (existingMin - 1);
				remHr = existingHr;
			} else {
				remMin = (existingMin + 60 - 1);
				remHr = (existingHr - 1);
			}
		}
		if( (remHr==0) && (remMin==0) && (remSec==0) ){
			loadTest('1');
		}
		
		if(remHr<10){
			remHr = "0"+remHr;
		}
		if(remMin<10){
			remMin = "0"+remMin;
		}
		if(remSec<10){
			remSec = "0"+remSec;
		}
		$("#remainingTime").text(remHr+":"+remMin+":"+remSec);
		setTimeout('decTime();', (1000));
	};
	</script>
</body>
</html>
