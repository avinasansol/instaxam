<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "edit-test-from-file.php";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	} 
	$ErrQ = "";
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

    <title>Instaxam.In - Edit Test - Upload Questions From File</title>
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

			<section class="tables">
			  <div class="container-fluid">
				<div class="row">
				  <div class="col-md-12">
					<div class="alternate-table">
						<p id="statusQuesLoad"><span id="loadQues"></span></p>
						<div id="editQuesLoadArea">

<?php 

$error="";
if(isset($_FILES["file"]["name"]))
{
	if((!$_FILES["file"]["name"])||($_FILES["file"]["name"]==""||$_FILES["file"]["name"]==null))
	{
	 	$error="Please choose a file.";
	}
	else if (strtolower(end(explode('.', $_FILES['file']['name'])))!="txt")
	{
	 	$error="Please choose a text file.";
	}
	else if ($_FILES["file"]["size"] > 5120000)
	{
	 	$error="Please choose a file of size less than 5MB.";
	}
	else if ($_FILES["file"]["error"] > 0)
	{
	   	$error="Return Code: " . $_FILES["file"]["error"] . "<br />";
	}
	else
	{
							move_uploaded_file($_FILES["file"]["tmp_name"], "ques-file-bkp/" . $testId.".txt");
                            if($file = fopen("ques-file-bkp/".$testId.".txt","r")){
/*------------------------------------------------------START INSERT----------------------------------------------------- */
function insert_ques($testId, $quesDesc, $optA, $optB, $optC, $optD, $ansOpt, $solN)
{
    include("include/connect-database.php");
    $ErrQ = "Please provide a valid ";

	if(str_replace(" ","",$quesDesc)=="") {
		$ErrQ = $ErrQ."Question, ";
	}
	
	if(str_replace(" ","",$optA)=="") {
		$ErrQ = $ErrQ."Option A, ";
	} else {
		$optA = mysqli_real_escape_string($con, substr(htmlentities($optA),0,2000));
	}
	
	if(str_replace(" ","",$optB)=="") {
		$ErrQ = $ErrQ."Option B, ";
	} else {
		$optB = mysqli_real_escape_string($con, substr(htmlentities($optB),0,2000));
	}
	
	if(str_replace(" ","",$optC)=="") {
		$ErrQ = $ErrQ."Option C, ";
	} else {
		$optC = mysqli_real_escape_string($con, substr(htmlentities($optC),0,2000));
	}
	
	if(str_replace(" ","",$optD)=="") {
		$ErrQ = $ErrQ."Option D, ";
	} else {
		$optD = mysqli_real_escape_string($con, substr(htmlentities($optD),0,2000));
	}
	
	if(isset($ansOpt)) {
		if( ($ansOpt!="A") && ($ansOpt!="B") && ($ansOpt!="C") && ($ansOpt!="D") ) {
			$ErrQ = $ErrQ."Correct Option, ";
		}
	} else {
		$ErrQ = $ErrQ."Correct Option, ";
	}
	
	if($ErrQ!="Please provide a valid ") {
		$ErrQ[(strlen($ErrQ)-2)]=".";
	} else {
		if(strlen($quesDesc)<10) {
			$ErrQ = "Question must contain at least 10 letters.";
		} else {
			$quesDesc = mysqli_real_escape_string($con, substr(htmlentities($quesDesc),0,2000));
			$solN = mysqli_real_escape_string($con, substr(htmlentities($solN),0,2000));
            $sql8 = "SELECT `qd_ques_no` FROM `ques_det` 
                        WHERE `qd_exam_id` = '".$testId."'
                        AND `qd_del_ind` != 'Y'
                        AND `qd_ques_desc` = '".$quesDesc."'
                        AND `qd_opt_a` = '".$optA."'
                        AND `qd_opt_b` = '".$optB."'
                        AND `qd_opt_c` = '".$optC."'
                        AND `qd_opt_d` = '".$optD."'
                        ";
            $result8=mysqli_query($con, $sql8);
            if($row8=mysqli_fetch_array($result8, MYSQLI_ASSOC)) {
                $ErrQ = "Question already added.";
            } else {
                $sql9 = "INSERT INTO `ques_det2` (`qd_exam_id`, `qd_ques_desc`, `qd_opt_a`, `qd_opt_b`, `qd_opt_c`, `qd_opt_d`, `qd_correct_opt`, `qd_soln`) 
                                        VALUES ('".$testId."', '".$quesDesc."', '".$optA."', '".$optB."', '".$optC."', '".$optD."', '".$ansOpt."', '".$solN."')";
                if (!mysqli_query($con, $sql9)) {
                    $ErrQ = "Error: Failure while adding question.";
                } else {
                    $ErrQ = "Question added.";
                }
            }
		}
	}

    if($ErrQ=="Please provide a valid ") {
        $ErrQ = "";
    }
    if($ErrQ == "Question added.") {
        echo "<p style='color: #2fda1f;'>".$ErrQ."</p>";
    } else if($ErrQ == "Question already added.") {
        echo "<p style='color: #535ba0;'>".$ErrQ."</p>";
    } else {
        echo "<p style='color: #FF3300;'>".$ErrQ."</p>";
    }
    echo "<br />";
    echo "<br />";
    mysqli_close($con);
}
/*-------------------------------------------------------END DELETE/UPDATE/INSERT----------------------------------------------------- */
                                $quesCnt = 0;
                                $continuation =  "";
                                $newLine = 0;
                                $quesDesc =  "";
                                $optA =  "";
                                $optB =  "";
                                $optC =  "";
                                $optD =  "";
                                $ansOpt =  "";
                                $solN =  "";
                                while(! feof($file))
                                {
                                    $sentence = fgets($file);
                                    $stripped = ltrim(preg_replace('/\s+/', ' ', $sentence));
                                    //echo $stripped."<br />";

                                    if(
                                    ((strlen($quesCnt)==1) && (substr($stripped,0,3)==(string)$quesCnt."Q ")) ||
                                    ((strlen($quesCnt)==2) && (substr($stripped,0,4)==(string)$quesCnt."Q ")) ||
                                    ((strlen($quesCnt)==3) && (substr($stripped,0,5)==(string)$quesCnt."Q "))
                                    )
                                    {
                                        $prevQues = $quesCnt-1;
                                        if($prevQues>0) {
                                            $quesDesc = trim($quesDesc);
                                            $optA = trim($optA);
                                            $optB = trim($optB);
                                            $optC = trim($optC);
                                            $optD = trim($optD);
                                            $solN = trim($solN);
                                            echo "<b>Question No #".$prevQues.":</b><br />";
                                            echo str_replace("\n","<br />",preg_replace( "/[\r\n]+/", "\n", $quesDesc))."<br />";
                                            echo "<b>Option A: </b>".str_replace("\n","<br />",$optA)."<br />";
                                            echo "<b>Option B: </b>".str_replace("\n","<br />",$optB)."<br />";
                                            echo "<b>Option C: </b>".str_replace("\n","<br />",$optC)."<br />";
                                            echo "<b>Option D: </b>".str_replace("\n","<br />",$optD)."<br />";
                                            echo "<b>Answer: </b>".str_replace("\n","<br />",$ansOpt)."<br />";
                                            echo "<b>Solution: </b>".str_replace("\n","<br />",$solN)."<br />";
                                            insert_ques($testId, $quesDesc, $optA, $optB, $optC, $optD, $ansOpt, $solN);
                                        }
                                        if((strlen($quesCnt)==1) && (substr($stripped,0,3)==(string)$quesCnt."Q ")){
                                            $stripped = substr($stripped,3);
                                        }
                                        if((strlen($quesCnt)==2) && (substr($stripped,0,4)==(string)$quesCnt."Q ")){
                                            $stripped = substr($stripped,4);
                                        }
                                        if((strlen($quesCnt)==3) && (substr($stripped,0,5)==(string)$quesCnt."Q ")){
                                            $stripped = substr($stripped,5);
                                        }
                                        $quesCnt++;
                                        $quesDesc = "";
                                        $optA =  "";
                                        $optB =  "";
                                        $optC =  "";
                                        $optD =  "";
                                        $ansOpt =  "";
                                        $solN =  "";
                                        $continuation =  "QD";
                                        $newLine = 0;
                                    } else if(strtoupper(substr($stripped,0,3))=="(A)") {
                                        $continuation =  "OA";
                                        $newLine = 0;
                                        $stripped = substr($stripped,3);
                                    } else if(strtoupper(substr($stripped,0,3))=="(B)") {
                                        $continuation =  "OB";
                                        $newLine = 0;
                                        $stripped = substr($stripped,3);
                                    } else if(strtoupper(substr($stripped,0,3))=="(C)") {
                                        $continuation =  "OC";
                                        $newLine = 0;
                                        $stripped = substr($stripped,3);
                                    } else if(strtoupper(substr($stripped,0,3))=="(D)") {
                                        $continuation =  "OD";
                                        $newLine = 0;
                                        $stripped = substr($stripped,3);
                                    }   else if(strtoupper(substr($stripped,0,3))=="A. ") {
                                        $continuation =  "OA";
                                        $newLine = 0;
                                        $stripped = substr($stripped,3);
                                    } else if(strtoupper(substr($stripped,0,3))=="B. ") {
                                        $continuation =  "OB";
                                        $newLine = 0;
                                        $stripped = substr($stripped,3);
                                    } else if(strtoupper(substr($stripped,0,3))=="C. ") {
                                        $continuation =  "OC";
                                        $newLine = 0;
                                        $stripped = substr($stripped,3);
                                    } else if(strtoupper(substr($stripped,0,3))=="D. ") {
                                        $continuation =  "OD";
                                        $newLine = 0;
                                        $stripped = substr($stripped,2);
                                    }  else if(strtoupper(substr($stripped,0,4))=="ANS:"){
                                        $continuation =  "AN";
                                        $newLine = 0;
                                        $stripped = substr($stripped,4);
                                    }  else if(strtoupper(substr($stripped,0,4))=="SOL:"){
                                        $continuation =  "SO";
                                        $newLine = 0;
                                        $stripped = substr($stripped,4);
                                    }
                                    $newLine++;
                                    
                                    $stripped = trim($stripped);
                                    if($newLine>1){
                                        $stripped = "\n".$stripped;
                                    }
                                    if($continuation ==  "QD") {
                                        $quesDesc = $quesDesc.$stripped;
                                    } else if($continuation ==  "OA") {
                                        $optA = $optA.$stripped;
                                    } else if($continuation ==  "OB") {
                                        $optB = $optB.$stripped;
                                    } else if($continuation ==  "OC") {
                                        $optC = $optC.$stripped;
                                    } else if($continuation ==  "OD") {
                                        $optD = $optD.$stripped;
                                    } else if($continuation ==  "AN") {
                                        if(strtoupper(substr($stripped,0,3))=="(A)") {
                                            $ansOpt = "A";
                                        } else if(strtoupper(substr($stripped,0,3))=="(B)") {
                                            $ansOpt = "B";
                                        } else if(strtoupper(substr($stripped,0,3))=="(C)") {
                                            $ansOpt = "C";
                                        } else if(strtoupper(substr($stripped,0,3))=="(D)") {
                                            $ansOpt = "D";
                                        }   else if(strtoupper(substr($stripped,0,2))=="A.") {
                                            $ansOpt = "A";
                                        } else if(strtoupper(substr($stripped,0,2))=="B.") {
                                            $ansOpt = "B";
                                        } else if(strtoupper(substr($stripped,0,2))=="C.") {
                                            $ansOpt = "C";
                                        } else if(strtoupper(substr($stripped,0,2))=="D.") {
                                            $ansOpt = "D";
                                        }   else if(strtoupper(substr($stripped,0,1))=="A") {
                                            $ansOpt = "A";
                                        } else if(strtoupper(substr($stripped,0,1))=="B") {
                                            $ansOpt = "B";
                                        } else if(strtoupper(substr($stripped,0,1))=="C") {
                                            $ansOpt = "C";
                                        } else if(strtoupper(substr($stripped,0,1))=="D") {
                                            $ansOpt = "D";
                                        }
                                    } else if($continuation ==  "SO") {
                                        $solN = $solN.$stripped;
                                    }
                                    
                                }
                                if($quesCnt>0) {
                                    $prevQues = $quesCnt-1;
                                    $quesDesc = trim($quesDesc);
                                    $optA = trim($optA);
                                    $optB = trim($optB);
                                    $optC = trim($optC);
                                    $optD = trim($optD);
                                    $solN = trim($solN);
                                    echo "<b>Question No #".$prevQues.":</b><br />";
                                    echo str_replace("\n","<br />",preg_replace( "/[\r\n]+/", "\n", $quesDesc))."<br />";
                                    echo "<b>Option A: </b>".str_replace("\n","<br />",$optA)."<br />";
                                    echo "<b>Option B: </b>".str_replace("\n","<br />",$optB)."<br />";
                                    echo "<b>Option C: </b>".str_replace("\n","<br />",$optC)."<br />";
                                    echo "<b>Option D: </b>".str_replace("\n","<br />",$optD)."<br />";
                                    echo "<b>Answer: </b>".str_replace("\n","<br />",$ansOpt)."<br />";
                                    echo "<b>Solution: </b>".str_replace("\n","<br />",$solN)."<br />";
                                    insert_ques($testId, $quesDesc, $optA, $optB, $optC, $optD, $ansOpt, $solN);
                                    $quesCnt++;
                                    $quesDesc = "";
                                    $optA =  "";
                                    $optB =  "";
                                    $optC =  "";
                                    $optD =  "";
                                    $ansOpt =  "";
                                    $solN =  "";
                                } else {
                                  	$error = "Uploaded file is not in the desired format.";
                                  	echo "<h2>No questions found in the uploaded file. Please upload a file with questions as per the instructions. You can also refer to the sample file provided below.</h2><br /><br />";
                                }
                                fclose($file);
                            }
				
	}
}
if((!isset($_FILES["file"]["name"]))||($error!=""))
{
?>
	<h2>Upload questions from file:</h2>
	<form name="UploadTextFile" action="<?php echo $page;?>" method="post" enctype="multipart/form-data">
      	<input type="file" name="file" id="file" value="" onChange='fileCheck()' /><br />
		<?php if($error!=""){echo "<label style='color:#FF0000; font-size:12px;'>".$error."</label><br />";}?>
      	<input type="hidden"  name="TestId" value="<?php echo $testId; ?>" /><br />
      	<button type="submit" name="UpFl" value="Upload File">Upload File</button>
	</form><br /><br />
	<h2>Instructions for uploading file:</h2>
	<label style="color:#444444; font-weight:bold;">1)</label> The file must be a text file with extensions '.txt'.<br />
	<label style="color:#444444; font-weight:bold;">2)</label> The file size must be less than 5MB.<br />
<?php 
}
                        ?> 
						</div>
					</div>
				  </div>
				</div>
			  </div>
			</section>	
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
    <script>
      var uploadField = document.getElementById("file");
      uploadField.onchange = function() {
        if(this.files[0].size > 5120000){
          alert("Please choose a file of size less than 5MB.");
          this.value = "";
        } else {
          var parts = $('#file').val().split('.');
          if((parts[parts.length - 1])!='txt'){
          	alert("Please choose a text file with .txt extension.");
          	this.value = "";
          };
        };
      };
    </script>
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
<?php 
	if($error!=""){echo "<script>alert('".$error."')</script>";}
?>