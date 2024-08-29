<?php
	$strt = 0;
	$end = 5;
	$srchStr = "";
	if(isset($_REQUEST['searchTxt'])){
		$srchStr = htmlspecialchars($_REQUEST['searchTxt']);
	}
	if(isset($_REQUEST['startFrom'])){
		$strt = (int)$_REQUEST['startFrom'];
	} else {
		$strt = 0;
	}
	include("connect-database.php");
	$sql1 =   "SELECT `exam_det`.`ed_exam_id`, 
					  `exam_sub_cat1`.`esc1_sub_cat1_name`,
					  `exam_det`.`ed_sub_cat2_id`,
					  `exam_det`.`ed_sub_cat3_id`,
					  `exam_det`.`ed_created_by`,
					  `exam_det`.`ed_exam_desc` 
				 FROM `exam_det`, 
				 	  `exam_sub_cat1`
				WHERE `exam_det`.`ed_sub_cat1_id` = `exam_sub_cat1`.`esc1_sub_cat1_id`
				  AND ( `ed_exam_desc` LIKE '%".$srchStr."%' OR
						`esc1_sub_cat1_name` LIKE '%".$srchStr."%' )
			    ORDER BY
				  CASE
					WHEN `ed_exam_desc` LIKE '".$srchStr."' THEN 1
					WHEN `ed_exam_desc` LIKE '".$srchStr."%' THEN 2
					WHEN `ed_exam_desc` LIKE '%".$srchStr."%' THEN 3
					WHEN `ed_exam_desc` LIKE '%".$srchStr."' THEN 4
					
					WHEN `esc1_sub_cat1_name` LIKE '".$srchStr."' THEN 5
					WHEN `esc1_sub_cat1_name` LIKE '".$srchStr."%' THEN 6
					WHEN `esc1_sub_cat1_name` LIKE '%".$srchStr."%' THEN 7
					WHEN `esc1_sub_cat1_name` LIKE '%".$srchStr."' THEN 8
					ELSE 9
				  END
				LIMIT ".$strt.", ".$end;
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
			$testDesc = str_ireplace($srchStr,"<b class='srchT'>".strtoupper($srchStr)."</b>",$row1['ed_exam_desc']);
		}
		$cat = str_ireplace($srchStr,"<b class='srchT'>".strtoupper($srchStr)."</b>",$cat);
		echo "<tr><td style='width:30%'>".$cat."</td><td style='width:70%'><a href='test-details/".$row1['ed_exam_id']."'>".$testDesc."</a></td></tr>";
	}
	if($rowCount1>0){ 
		echo "</tbody></table>";
	} else {
		$more = "";
		if($strt>0){
			$more = " more";
		}
		if($srchStr == ""){
			echo "<p id='endResultsItems'><span >No".$more." recent tests are available.</span></p>";
		} else {
			echo "<p id='endCreatorTestsItems'><span >No".$more." tests found for the searched text.</span></p>";
		}
	}
	$totalResultCount=0;
	$sql1 =   "SELECT COUNT(`ed_exam_id`) AS `totalResultCount`
				 FROM `exam_det`, 
				 	  `exam_sub_cat1`
				WHERE `exam_det`.`ed_sub_cat1_id` = `exam_sub_cat1`.`esc1_sub_cat1_id`
				  AND ( `ed_exam_desc` LIKE '%".$srchStr."%' OR
						`esc1_sub_cat1_name` LIKE '%".$srchStr."%' )
			  ";
	$result1=mysqli_query($con, $sql1);
	if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC)) {
		$totalResultCount=$row1['totalResultCount'];
	}
	mysqli_close($con);
?>
<form action="<?php echo $page; ?>" method="get" onSubmit="return loadMoreResults()">
	<ul class="table-pagination">
	<?php 
		$count = 0;
		$pageNo = 0;
		?>
			<li<?php if(($strt-5)<0){echo " style='display:none;'";} ?>><a href="#" onClick="return loadMoreResults(<?php if(($strt-5)<0){echo "0";} else{echo ($strt-5);} ?>)">Previous</a></li>
		<?php 
		while($count<$totalResultCount)
		{
			$pageNo++;
			if( ($pageNo==1) || ($pageNo==2) || ($count==$strt) || ($pageNo==(ceil($totalResultCount/5/4)+ceil($totalResultCount/5/2))) || ($pageNo==ceil($totalResultCount/5/4)) || ($pageNo==ceil($totalResultCount/5/2)) || ($pageNo==ceil($totalResultCount/5)) || ($pageNo==(ceil($totalResultCount/5)-1)) )
			{
			?>
				<li<?php if($count==$strt){ ?> class="active"<?php } ?>>
				<a href="#" onClick="return loadMoreResults(<?php echo $count; ?>)">
				<?php 
					if( ($pageNo==1) || ($pageNo==2) || ($count==$strt) || ($pageNo==ceil($totalResultCount/5/2)) || ($pageNo==ceil($totalResultCount/5)) || ($pageNo==(ceil($totalResultCount/5)-1)) ){
						echo $pageNo;
					} else 
					if( ($pageNo==(ceil($totalResultCount/5/4)+ceil($totalResultCount/5/2))) || ($pageNo==ceil($totalResultCount/5/4)) ){
						echo "..";
					} else {
						echo $pageNo;
					} 
				?>
				</a>
				</li>
			<?php 
			}
			$count = $count + 5;
		}
		?>
			<li<?php if(($strt+5)>=$totalResultCount){echo " style='display:none;'";}?>><a href="#" onClick="return loadMoreResults(<?php if(($strt+5)>=$totalResultCount){echo $strt;} else {echo ($strt+5);} ?>)">Next</a></li>
		<?php 
		?>
	</ul>
</form>