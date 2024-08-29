<?php
	$strt = 0;
	$end = 5;
	if(isset($_REQUEST['startFrom'])){
		$strt = (int)$_REQUEST['startFrom'];
	} else {
		$strt = 0;
	}
	include("connect-database.php");
	$sql1 = "SELECT `exam_det`.`ed_exam_id`, `exam_sub_cat1`.`esc1_sub_cat1_name`, `exam_det`.`ed_sub_cat2_id`, `exam_det`.`ed_sub_cat3_id`, `exam_det`.`ed_created_by`, `exam_det`.`ed_exam_desc`, COUNT(`exam_taken`.`et_exam_id`) AS `taken_count` \n"
			. "  FROM `exam_det`, `exam_sub_cat1`, `exam_taken`\n"
			. " WHERE `exam_det`.`ed_sub_cat1_id` = `exam_sub_cat1`.`esc1_sub_cat1_id`\n"
			. "   AND `exam_det`.`ed_exam_id` = `exam_taken`.`et_exam_id`\n"
			. " GROUP BY `exam_taken`.`et_exam_id`\n"
			. "HAVING COUNT(`exam_taken`.`et_exam_id`)>0\n"
			. " ORDER BY COUNT(`exam_taken`.`et_exam_id`) DESC\n"
			. " LIMIT ".$strt.", ".$end;
	$result1=mysqli_query($con, $sql1);
	$rowCount1 = 0;
	while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{
		$rowCount1++;
		if($rowCount1==1){
			echo "<table><tbody>";
		}
		$cat = "";
		$cat = $cat.$row1['esc1_sub_cat1_name'];
		$sql2 = "SELECT `exam_sub_cat2`.`esc2_sub_cat2_name`\n"
			  . "  FROM `exam_sub_cat2`\n"
			  . " WHERE `exam_sub_cat2`.`esc2_sub_cat2_id` = '".$row1['ed_sub_cat2_id']."'";
		$result2=mysqli_query($con, $sql2);
		if($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
		{
			$cat = $row2['esc2_sub_cat2_name'];
		}
		$sql3 = "SELECT `exam_sub_cat3`.`esc3_sub_cat3_name`\n"
			  . "  FROM `exam_sub_cat3`\n"
			  . " WHERE `exam_sub_cat3`.`esc3_sub_cat3_id` = '".$row1['ed_sub_cat3_id']."'";
		$result3=mysqli_query($con, $sql3);
		if($row3=mysqli_fetch_array($result3, MYSQLI_ASSOC))
		{
			$cat = $cat." -> ".$row3['esc3_sub_cat3_name'];
		}
		$testDesc = "";
		if(strlen($row1['ed_exam_desc'])>200) {
			$testDesc = substr($row1['ed_exam_desc'],0,200).".......";
		} else {
			$testDesc = $row1['ed_exam_desc'];
		}
		echo "<tr><td style='width:30%'>".$cat."</td><td style='width:70%'><a href='https://www.instaxam.in/test-details/".$row1['ed_exam_id']."'>".$testDesc."</a></td></tr>";
	}
	if($rowCount1>0){ 
		echo "</tbody></table>";
	} else {
		echo "<p id='endFamousItems'><span >No more famous tests are available.</span></p>";
	}
	mysqli_close($con);
?>
