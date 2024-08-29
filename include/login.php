<?php 
	$LogErr="";
	if(isset($_POST['Operation']))
	{
		if($_POST['Operation']=="UserLogIn")
		{
			if((!isset($_POST['txtUsrId']))&&(!isset($_POST['txtPswd'])))
			{
				$LogErr="please enter your email id and password";
			}
			else if(!isset($_POST['txtUsrId']))
			{
				$LogErr="please enter your email id";
			}
			else if(!isset($_POST['txtPswd']))
			{
				$LogErr="please enter your password";
			}
			else if((($_POST['txtUsrId']=="")||($_POST['txtUsrId']==null))&&(($_POST['txtPswd']=="")||($_POST['txtPswd']==null)))
			{
				$LogErr="please enter your email id and password";
			}
			else if(($_POST['txtUsrId']=="")||($_POST['txtUsrId']==null))
			{
				$LogErr="please enter your email id";
			}
			else if(($_POST['txtPswd']=="")||($_POST['txtPswd']==null))
			{
				$LogErr="please enter your password";
			}
			else if(preg_match('/[\'\"]/', $_POST['txtPswd']))
			{
				$LogErr="email id or password doesn't match";
			}
			else
			{
				$sql="SELECT `ud_creator_access` 
					    FROM `user_det` 
					   WHERE ((`ud_password` IS NOT NULL) AND (`ud_user_id`='".$_POST['txtUsrId']."' && `ud_password`='".md5($_POST['txtPswd'])."'))
					      OR ((`ud_new_password` IS NOT NULL) AND (`ud_user_id`='".$_POST['txtUsrId']."' && `ud_new_password`='".$_POST['txtPswd']."'))
					 ";
				$result=mysqli_query($con, $sql);
				if($row=mysqli_fetch_array($result))
				{
					if($row['ud_creator_access']=="Y"){
						$_SESSION['LogdUsrDet'][0]="CreatorUsr";
					} else {
						$_SESSION['LogdUsrDet'][0]="GenUsr";
					}
					$_SESSION['LogdUsrDet'][1]=$_POST['txtUsrId'];
					$sql1 = "UPDATE `user_det` SET `ud_last_login_ts`=now() WHERE `ud_user_id`='".$_POST['txtUsrId']."'";
					mysqli_query($con, $sql1);
					if(!isset($_POST['CurrPage'])) {
						header("Location: user-dashboard.php");
					} else {
						header("Location: ".htmlspecialchars($_REQUEST['CurrPage']));
					}
				}
				else
				{
					$LogErr="email id or password doesn't match";
				}
			}
		}
	}
?>