<?php
$testCat = "";
$invalidCatSent = 0;
$noTestFound = 0;
$whereClause = "";
if(isset($_REQUEST['exam_sub_cat3'])){
	$testCat = $_REQUEST['exam_sub_cat3'];
	$sql1 =   "SELECT `exam_sub_cat2`.`esc2_sub_cat2_name`, `exam_sub_cat3`.`esc3_sub_cat3_name`\n"
			. "  FROM `exam_sub_cat2`, `exam_sub_cat3`\n"
			. " WHERE `exam_sub_cat2`.`esc2_sub_cat2_id` = `exam_sub_cat3`.`esc3_sub_cat2_id`\n"
			. "   AND `exam_sub_cat3`.`esc3_sub_cat3_id` = '".$testCat."'";
	$result1=mysqli_query($con, $sql1);
	if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{
		$testCat = $row1['esc2_sub_cat2_name']." -> ".$row1['esc3_sub_cat3_name'];
		$whereClause = "AND `exam_det`.`ed_sub_cat3_id` = '".$_REQUEST['exam_sub_cat3']."'\n";
	} else {
		$invalidCatSent = 1;
	}
} else if(isset($_REQUEST['exam_sub_cat2'])){
	$testCat = $_REQUEST['exam_sub_cat2'];
	$sql1 =   "SELECT `exam_sub_cat2`.`esc2_sub_cat2_name`\n"
			. "  FROM `exam_sub_cat2`\n"
			. " WHERE `exam_sub_cat2`.`esc2_sub_cat2_id` = '".$testCat."'";
	$result1=mysqli_query($con, $sql1);
	if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{
		$testCat = $row1['esc2_sub_cat2_name'];
		$whereClause = "AND `exam_det`.`ed_sub_cat2_id` = '".$_REQUEST['exam_sub_cat2']."'\n";
	} else {
		$invalidCatSent = 1;
	}
} else if(isset($_REQUEST['exam_sub_cat1'])){
	$testCat = $_REQUEST['exam_sub_cat1'];
	$sql1 =   "SELECT `exam_sub_cat1`.`esc1_sub_cat1_name`\n"
			. "  FROM `exam_sub_cat1`\n"
			. " WHERE `exam_sub_cat1`.`esc1_sub_cat1_id` = '".$testCat."'";
	$result1=mysqli_query($con, $sql1);
	if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{
		$testCat = $row1['esc1_sub_cat1_name'];
		$whereClause = "AND `exam_det`.`ed_sub_cat1_id` = '".$_REQUEST['exam_sub_cat1']."'\n";
	} else {
		$invalidCatSent = 1;
	}
} else if(isset($_REQUEST['exam_cat'])){
	$testCat = $_REQUEST['exam_cat'];
	$sql1 =   "SELECT `exam_cat`.`ec_cat_name`\n"
			. "  FROM `exam_cat`\n"
			. " WHERE `exam_cat`.`ec_cat_id` = '".$testCat."'";
	$result1=mysqli_query($con, $sql1);
	if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{
		$testCat = $row1['ec_cat_name'];
		$sql2 =   "SELECT `esc1_sub_cat1_id`\n"
				. "  FROM `exam_sub_cat1`\n"
				. " WHERE `esc1_cat_id` = '".$_REQUEST['exam_cat']."'";
		$result2=mysqli_query($con, $sql2);
		$row2Count = 0;
		while($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
		{
			$row2Count++;
			if($row2Count==1){
				$whereClause = "AND ( `exam_det`.`ed_sub_cat1_id` = '".$row2['esc1_sub_cat1_id']."'";
			} else {
				$whereClause = $whereClause." OR `exam_det`.`ed_sub_cat1_id` = '".$row2['esc1_sub_cat1_id']."'";
			}
		}
		if($row2Count>0){
			$whereClause = $whereClause." )\n";
		} else {
			$noTestFound = 1;
		}
	} else {
		$invalidCatSent = 1;
	}
}
?>