<?php 
	include("connect-database.php");
	$searchCreator = "";
	if(isset($_REQUEST['searchCreator'])){
		$searchCreator = $_REQUEST['searchCreator'];
		if($searchCreator != ""){
			$sql1 = "SELECT `ud_user_id`, `ud_first_name`, `ud_last_name`
					   FROM `user_det`
					  WHERE ( `ud_user_id` LIKE '%".$searchCreator."%' OR
					  		   `ud_first_name` LIKE '%".$searchCreator."%' OR
							    `ud_last_name` LIKE '%".$searchCreator."%'  OR
							    CONCAT(`ud_first_name`, ' ', `ud_last_name`) LIKE '%".$searchCreator."%' )
					    AND (`ud_creator_access` = 'Y')
					  ORDER BY
					  CASE
						WHEN `ud_user_id` LIKE '".$searchCreator."' THEN 1
						WHEN `ud_first_name` LIKE '".$searchCreator."' THEN 2
						WHEN `ud_last_name` LIKE '".$searchCreator."' THEN 3
						
						WHEN `ud_user_id` LIKE '".$searchCreator."%' THEN 4
						WHEN `ud_first_name` LIKE '".$searchCreator."%' THEN 5
						WHEN `ud_last_name` LIKE '".$searchCreator."%' THEN 6
						
						WHEN `ud_user_id` LIKE '%".$searchCreator."%' THEN 7
						WHEN `ud_first_name` LIKE '%".$searchCreator."%' THEN 8
						WHEN `ud_last_name` LIKE '%".$searchCreator."%' THEN 9
						
						WHEN `ud_user_id` LIKE '%".$searchCreator."' THEN 10
						WHEN `ud_first_name` LIKE '%".$searchCreator."' THEN 11
						WHEN `ud_last_name` LIKE '%".$searchCreator."' THEN 12
						ELSE 13
					  END
					  LIMIT 0, 10";
			$result1=mysqli_query($con, $sql1);
			$rowCount1 = 0;
			$buildJavaScript = "";
			while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
			{
				$rowCount1++;
				$userId = "";
				$userName = "";
				$userId = $row1['ud_user_id'];
				$userName = $row1['ud_first_name']." ".$row1['ud_last_name'];
				if(strlen($userName)>250) {
					$userName = substr($userName,0,250)."...";
				}
				if($rowCount1 == 1) {
				?>
				
				<form id='contact' action='https://www.instaxam.in/browse-test.php' method='get' onSubmit="return initiateLoadCreatorTests()">
					<div class='row' id="creatorRow">
						<h2>Please select one creator from below:</h2>
							
				<?php 
				}
				?>

						<div class='circle-item' style="width:100%">
							<input name='creator-id' type='radio' id='<?php echo $userId; ?>' value='<?php echo $userId; ?>'  onclick="return initiateLoadCreatorTests()">
							<label for='<?php echo $userId; ?>' id="creator-det">[<?php echo $userId; ?>] - <?php echo $userName; ?></label>
						</div>

				<?php 
			}
			if($rowCount1 > 0){
				?>

					</div>
				</form>
			<section class="tables" id="creator-tests" style="border-bottom:0; padding-bottom:10px;">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                    </div>
                    <div class="alternate-table">
					
						<table><thead><tr><th style='width:30%'>Test Category</th><th style='width:70%'>Test Description</th></tr></thead></table>
						<div id="creatorTestsLoadArea">
						</div>
						<p id="statusCreatorTests"><span ></span></p>
						<form action="https://www.instaxam.in/browse-test.php" method="post" onSubmit="return loadCreatorTests()">
							<div id="load-more-creator" class="load-more">
								<input type="hidden" id="creatorId" name="creatorId" value="" />
								<button type="submit">Load More</button>
							</div>
						</form>

                    </div>
                  </div>
                </div>
              </div>
            </section>

				<?php 
			} else {
				$msg = "Sorry, No such creator found.";
				echo "<p id='endCreatorList'><span >".$msg."</span></p>";
			}
		}
	}
	mysqli_close($con);
?>
