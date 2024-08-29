<?php 
$ErrAQ = "Please provide a valid ";
$QuesDesc = "";
$OptA = "";
$OptB = "";
$OptC = "";
$OptD = "";
$ansOpt = "";
$QuesSol = "";
$fileQuesParaPost = "";
$fileQuesParaPost = "Q ".$_POST['QuesDesc']."\n(A)".$_POST['OptA']."\n(B)".$_POST['OptB']."\n(C)".$_POST['OptC']."\n(D)".$_POST['OptD']."\nANS: ".$_POST['ansOpt']."\nSOL:\n".$_POST['QuesSol']."\n";

if( (isset($_POST['QuesDesc'])) && (isset($_POST['OptA'])) && (isset($_POST['OptB'])) && (isset($_POST['OptC'])) && (isset($_POST['OptD'])) && (isset($_POST['QuesSol'])) )
{
	if(str_replace(" ","",$_POST['QuesDesc'])=="") {
		$ErrAQ = $ErrAQ."Question, ";
	}
	
	if(str_replace(" ","",$_POST['OptA'])=="") {
		$ErrAQ = $ErrAQ."Option A, ";
	} else {
		$OptA = mysqli_real_escape_string($con, substr(htmlentities($_POST['OptA']),0,2000));
	}
	
	if(str_replace(" ","",$_POST['OptB'])=="") {
		$ErrAQ = $ErrAQ."Option B, ";
	} else {
		$OptB = mysqli_real_escape_string($con, substr(htmlentities($_POST['OptB']),0,2000));
	}
	
	if(str_replace(" ","",$_POST['OptC'])=="") {
		$ErrAQ = $ErrAQ."Option C, ";
	} else {
		$OptC = mysqli_real_escape_string($con, substr(htmlentities($_POST['OptC']),0,2000));
	}
	
	if(str_replace(" ","",$_POST['OptD'])=="") {
		$ErrAQ = $ErrAQ."Option D, ";
	} else {
		$OptD = mysqli_real_escape_string($con, substr(htmlentities($_POST['OptD']),0,2000));
	}
	
	if(isset($_POST['ansOpt'])) {
		if( ($_POST['ansOpt']!="A") && ($_POST['ansOpt']!="B") && ($_POST['ansOpt']!="C") && ($_POST['ansOpt']!="D") ) {
			$ErrAQ = $ErrAQ."Correct Option, ";
		} else {
			$ansOpt = $_POST['ansOpt'];
		}
	} else {
		$ErrAQ = $ErrAQ."Correct Option, ";
	}	
  
	if($ErrAQ!="Please provide a valid ") {
		$ErrAQ[(strlen($ErrAQ)-2)]=".";
	} else {
		if(strlen($_POST['QuesDesc'])<10) {
			$ErrAQ = "Question must contain at least 10 letters.";
		} else {
			$QuesDesc = mysqli_real_escape_string($con, substr(htmlentities($_POST['QuesDesc']),0,2000));
			$QuesSol = mysqli_real_escape_string($con, substr(htmlentities($_POST['QuesSol']),0,2000));
            include("include/connect-database.php");
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
              $ErrAQ = "Question already added.";
            } else {
              $sql9 = "INSERT INTO `ques_det` (`qd_exam_id`, `qd_ques_desc`, `qd_opt_a`, `qd_opt_b`, `qd_opt_c`, `qd_opt_d`, `qd_correct_opt`, `qd_soln`) 
                                                  VALUES ('".$testId."', '".$QuesDesc."', '".$OptA."', '".$OptB."', '".$OptC."', '".$OptD."', '".$ansOpt."', '".$QuesSol."')";
              if (!mysqli_query($con, $sql9)) {
                $ErrAQ = "Error: Failure while adding question.";
              } else {
                $ErrAQ = "Question added.";
              }
            }
            mysqli_close($con);
		}
	}
}
if($ErrAQ=="Please provide a valid ") {
	$ErrAQ = "";
}
?>
