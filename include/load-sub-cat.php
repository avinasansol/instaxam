<?php 
	include("connect-database.php");
	if(isset($_POST['TestCategory'])){
		$testCategory1 = $_POST['TestCategory'];
		$sql="SELECT `esc2_sub_cat2_name`,
					 `esc2_sub_cat2_id`
				FROM `exam_sub_cat2`
			   WHERE `esc2_sub_cat1_id` = '".$testCategory1."'
			   ORDER BY `esc2_order` ASC
			 ";
		$result=mysqli_query($con, $sql);
		$rowCount = 0 ;
		while($row=mysqli_fetch_array($result))
		{
			$rowCount++;
			if($rowCount==1){
				echo "<select name='SubCategory2' id='category2' onChange='return loadSubCat3()'>";
				if(isset($_POST['SubCategory2'])){
					$Category2 = $_POST['SubCategory2'];
					$sql2="SELECT `esc2_sub_cat2_name`,
								  `esc2_sub_cat2_id`
							 FROM `exam_sub_cat2`
							WHERE `esc2_sub_cat2_id` = '".$Category2."'
						 ";
					$result2=mysqli_query($con, $sql2);
					if($row2=mysqli_fetch_array($result2))
					{
						echo "<option value='".$row2['esc2_sub_cat2_id']."'>".$row2['esc2_sub_cat2_name']."</option>";
					} else {
						echo "<option value=''>Select Sub Category</option>";
					}
				} else {
					echo "<option value=''>Select Sub Category</option>";
				}
			}
			
			echo "<option value='".$row['esc2_sub_cat2_id']."'>".$row['esc2_sub_cat2_name']."</option>";
		}
		if($rowCount>0){
			echo "</select>";
		}
	}
	
	if( isset($validCat) && ($validCat != "") ) { echo "</span><span id='subcat2'>"; }
	
	if(isset($_POST['SubCategory2'])){
		$testCategory2 = $_POST['SubCategory2'];
		$sql="SELECT `esc3_sub_cat3_name`,
					 `esc3_sub_cat3_id`
				FROM `exam_sub_cat3`
			   WHERE `esc3_sub_cat2_id` = '".$testCategory2."'
			   ORDER BY `esc3_order` ASC
			 ";
		$result=mysqli_query($con, $sql);
		$rowCount = 0 ;
		while($row=mysqli_fetch_array($result))
		{
			$rowCount++;
			if($rowCount==1){
				echo "<select name='SubCategory3' id='category3'>";
				if(isset($_POST['SubCategory3'])){
					$Category3 = $_POST['SubCategory3'];
					$sql2="SELECT `esc3_sub_cat3_name`,
								  `esc3_sub_cat3_id`
							 FROM `exam_sub_cat3`
							WHERE `esc3_sub_cat3_id` = '".$Category3."'
						 ";
					$result2=mysqli_query($con, $sql2);
					if($row2=mysqli_fetch_array($result2))
					{
						echo "<option value='".$row2['esc3_sub_cat3_id']."'>".$row2['esc3_sub_cat3_name']."</option>";
					} else {
						echo "<option value=''>Select Lower Level Sub Category</option>";
					}
				} else {
					echo "<option value=''>Select Lower Level Sub Category</option>";
				}
			}
			echo "<option value='".$row['esc3_sub_cat3_id']."'>".$row['esc3_sub_cat3_name']."</option>";
		}
		if($rowCount>0){
			echo "</select>";
		}
	}
	mysqli_close($con);
?>