<section class="tables"<?php if(!isset($testID)){ echo " style='margin-top:-80px;'"; } ?>>

  <div class="container-fluid">
	<div class="row">
	  <div class="col-md-12">
		<div class="section-heading">
		  <h2>Access Requests: </h2>
		</div>
		<div class="alternate-table">
		  <?php 
		  if(isset($testID)){
			$sqlAccReq = "SELECT `eaf_user_id`,
								 `eaf_exam_id`,
								 `eaf_ts`,
								 `ud_first_name`,
								 `ud_last_name`,
								 `ud_gender`,
								 `ud_country`,
								 `ud_contact_no`
							FROM `exam_avlbl_for`,
								 `user_det`
						   WHERE `eaf_exam_id` = '".$testID."'
							 AND `eaf_user_id` = `ud_user_id`
							 AND `eaf_avlblty` = 'N'
						   ORDER BY `eaf_ts` DESC
						 ";
			} else {
			$sqlAccReq = "SELECT `eaf_user_id`,
								 `eaf_exam_id`,
								 `eaf_ts`,
								 `ud_first_name`,
								 `ud_last_name`,
								 `ud_gender`,
								 `ud_country`,
								 `ud_contact_no`
							FROM `exam_avlbl_for`,
								 `exam_det`,
								 `user_det`
						   WHERE `ed_created_by` ='".$loggedUserId."'
							 AND `eaf_exam_id` = `ed_exam_id`
							 AND `eaf_user_id` = `ud_user_id`
							 AND `eaf_avlblty` = 'N'
						   ORDER BY `eaf_ts` DESC
						 ";
			}
			$reqCount = 0;
			$result1=mysqli_query($con, $sqlAccReq);
			while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
			{
				$reqCount++;
				$examId = $row1['eaf_exam_id'];
				$gender = $row1['ud_gender'];
			  ?>
			  <style>
				  table {padding:5px; margin-bottom:30px;}
			  </style>
				<table>
				<tbody>
				<tr>
					<?php 
						if(!isset($testID)){
							echo "<td rowspan='6' align='center'>Test ID: <a href='https://www.instaxam.in/test-details/".$examId."'>".$examId."</a></td>";
						} 
					?>
					<td>UserID</td>
					<td><?php echo $row1['eaf_user_id']; ?></td>
					<td rowspan="6" align="center">
						<form action="<?php echo $page; ?>" method="post">
							<input type="hidden" name="UserId" value="<?php echo $row1['eaf_user_id']; ?>" />
							<input type="hidden" name="TestId" value="<?php echo $examId; ?>" />
							<button type="submit" style="width:80px;" name="ActType" value="A">Approve</button>
						</form>
					</td>
					<td rowspan="6" align="center">
						<form action="<?php echo $page; ?>" method="post">
							<input type="hidden" name="UserId" value="<?php echo $row1['eaf_user_id']; ?>" />
							<input type="hidden" name="TestId" value="<?php echo $examId; ?>" />
							<button type="submit" style="width:80px;" name="ActType" value="R">Reject</button>
						</form>
					</td>
				</tr>
				<tr>
					<td>RequestDate</td>
					<td><?php echo substr($row1['eaf_ts'],0,10); ?></td>
				</tr>
				<tr>
					<td>Name</td>
					<td><?php echo $row1['ud_first_name']." ".$row1['ud_last_name']; ?></td>
				</tr>
				<tr>
					<td>Gender</td>
					<td><?php if($gender=="F"){?>Female<?php } else if($gender=="M"){?>Male<?php } else if($gender=="O"){?>Other<?php } else {?>Not Specified<?php }?></td>
				</tr>
				<tr>
					<td>Country</td>
					<td><?php if(str_replace(" ","",$row1['ud_country'])=="") {echo "Not Specified";} else { echo $row1['ud_country'];} ?></td>
				</tr>
				<tr>
					<td>Contact</td>
					<td><?php echo $row1['ud_contact_no']; ?></td>
				</tr>
				</tbody>
				</table>
			  <?php 
			}
			if($reqCount == 0){
				if(!isset($testID)){
				?>
					<h3 style="font-size:16px;">There are no pending access request.</h3>
				<?php 
				} else {
				?>
					<h3 style="font-size:16px;">There are no pending access request for the test.</h3>
				<?php 
				}
			}
		  ?>
		 </div>
	  </div>
	</div>
  </div>
</section>
