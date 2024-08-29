<?php 
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
    $err = "";
	include("include/connect-database.php");
	if( (isset($_POST['Task-Rem'])) && (isset($_POST['Task-Date'])) && (isset($_POST['Task-Id'])) && (isset($_POST['Task-Val'])) && ($_POST['Task-Rem']=="T" || $_POST['Task-Rem']=="R") ){
        $sql1 = "SELECT `td_id`
                    FROM `task_dtl`
                WHERE `td_task_id`='".$_POST['Task-Id']."'
                    AND `td_type` = '".$_POST['Task-Rem']."'
                    AND `td_date` = '".$_POST['Task-Date']."'
                ";
        $result1 = mysqli_query($con, $sql1);
        if($row1 = mysqli_fetch_array($result1)){
            if($_POST['Task-Val']==""){
                $sql2 = "DELETE FROM `task_dtl` 
                         WHERE `td_id`='".$row1['td_id']."'
                        ";
                if(!mysqli_query($con, $sql2)){
                    $err = "Failure while updating task.";
                }
            } else {
                $sql2 = "UPDATE `task_dtl` 
                            SET `td_value` = '".mysqli_real_escape_string($con, substr(htmlentities($_POST['Task-Val']),0,2000))."'
                        WHERE `td_id`='".$row1['td_id']."'
                        ";
                if(!mysqli_query($con, $sql2)){
                    $err = "Failure while updating task.";
                }
                if($_POST['Task-Rem']=="T"){
                    $sql2 = "   DELETE FROM `task_dtl` 
                                WHERE `td_task_id`='".$_POST['Task-Id']."'
                                AND `td_type` = 'R'
                                AND `td_date` = '".$_POST['Task-Date']."'
                            ";
                    if(!mysqli_query($con, $sql2)){
                        $err = "Failure while updating task.";
                    }
                }
            }
        } else {
            $sql2 = "INSERT INTO `task_dtl` (`td_id`, `td_date`, `td_task_id`, `td_type`, `td_value`) 
                     VALUES (NULL, '".$_POST['Task-Date']."', '".$_POST['Task-Id']."', '".$_POST['Task-Rem']."', '".mysqli_real_escape_string($con, substr(htmlentities($_POST['Task-Val']),0,2000))."')
                    ";
            if(!mysqli_query($con, $sql2)){
                $err = "Failure while updating task.";
            }
        }
    }
	mysqli_close($con);
?>
