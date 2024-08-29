<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$pageFolder = "browse-test";
	include("include/connect-database.php");
	include("include/login.php");
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Search Tests by Creator, Browse Tests by Category, Find Recently Added & Most Famous Tests">
    <meta name="author" content="JhaAvinash">
    <meta name="keywords" content="Instaxam, Browse, Test, Category, Creator">

    <title>Instaxam.In - Browse Tests<?php 
      if(isset($_REQUEST['exam_cat'])||isset($_REQUEST['exam_sub_cat1'])||isset($_REQUEST['exam_sub_cat2'])||isset($_REQUEST['exam_sub_cat3'])) 
      {
          include("include/browse-cat-where-clause.php");
          if ($invalidCatSent == 1) {
              echo " - Invalid Test Category";
          } else if ($noTestFound == 1) {
              echo " - No tests found";
          } else {
              echo " - ".$testCat;
          }
      }
    ?></title>
    <link rel="icon" type="image/png" href="https://www.instaxam.in/assets/images/favicon.ico">

    <link rel="stylesheet" href="https://www.instaxam.in/assets/css/instaxam-style.css">
    <link rel="stylesheet" href="https://www.instaxam.in/assets/css/test-category.css">

<script type="text/javascript" src="https://www.instaxam.in/assets/js/jquery-1.3.2.js" ></script>
<script type="text/javascript">
var totalRecentItems = 0;
var totalCreatorItems = 0;
var totalFamousItems = 0;
var totalBrowseCatItems = 0;
$('document').ready(function(){
	updateStatusRecent();
	hideLoadMoreRecent();
	updateStatusCreatorTests();
	hideLoadMoreCreatorTests();
	updateStatusFamous();
	hideLoadMoreFamous();
	updateStatusBrowseCat();
	hideLoadMoreBrowseCat();
});
function updateStatusRecent(){
	$('#statusRecent span').text('');
	$('#statusRecent').css({'background':'none','height':'0px'});
};
function hideLoadMoreRecent(){
	var endRecent = $("#endRecentItems").val();
	if(endRecent != null) {
		$('#load-more-recent').css({'display':'none'});
	}
};
function updateStatusCreatorTests(){
	$('#statusCreatorTests span').text('');
	$('#statusCreatorTests').css({'background':'none','height':'0px'});
};
function hideLoadMoreCreatorTests(){
	var endCreatorTests = $("#endCreatorTestsItems").val();
	if(endCreatorTests != null) {
			$('#load-more-creator').css({'display':'none'});
	}
};
function updateStatusFamous(){
	$('#statusFamous span').text('');
	$('#statusFamous').css({'background':'none','height':'0px'});
};
function hideLoadMoreFamous(){
	var endFamous = $("#endFamousItems").val();
	if(endFamous != null) {
		$('#load-more-famous').css({'display':'none'});
	}
};
function updateStatusBrowseCat(){
	$('#statusBrowseCat span').text('');
	$('#statusBrowseCat').css({'background':'none','height':'0px'});
};
function hideLoadMoreBrowseCat(){
	var endBrowseCat = $("#endBrowseCatItems").val();
	if(endBrowseCat != null) {
		$('#load-more-browse-cat').css({'display':'none'});
	}
};
function loadRecentTests(){
	totalRecentItems = totalRecentItems + 5;
	$('#statusRecent span').text('Loading more tests...');
	$('#statusRecent').css({'height':'130px','background':'url(https://www.instaxam.in/assets/images/spinner.gif) no-repeat center'});
	$.get('https://www.instaxam.in/include/recently-added-tests.php', { startFrom: totalRecentItems}, function(newitems){
		$('#recentLoadArea').append(newitems);
		updateStatusRecent();
		hideLoadMoreRecent();
	});
	return false;
};
let selectedValue;
function initiateLoadCreatorTests(){
	totalCreatorItems = 0;
	$('#creatorTestsLoadArea').css({'display':'block'});
	$('#creatorTestsLoadArea').text('');
	const rbs = document.querySelectorAll('input[name="creator-id"]');
	for (const rb of rbs) {
		if (rb.checked) {
			selectedValue = rb.value;
			break;
		}
	}
	loadCreatorTests();
	location.replace("#creatorTestsLoadArea");
	return false;
}
function loadCreatorTests(){
	$('#statusCreatorTests span').text('Loading more tests...');
	$('#statusCreatorTests').css({'height':'130px','background':'url(https://www.instaxam.in/assets/images/spinner.gif) no-repeat center'});
	$.get('https://www.instaxam.in/include/recently-added-tests.php', { 
		startFrom: totalCreatorItems,
		creatorId: selectedValue
		}, function(newitems){
			$('#creatorTestsLoadArea').append(newitems);
			$('#creator-tests').css({'display':'block'});
			$('#load-more-creator').css({'display':'block'});
			updateStatusCreatorTests();
			hideLoadMoreCreatorTests();
	});
	totalCreatorItems = totalCreatorItems + 5;
	return false;
};
function loadFamousTests(){
	totalFamousItems = totalFamousItems + 5;
	$('#statusFamous span').text('Loading more tests...');
	$('#statusFamous').css({'height':'130px','background':'url(https://www.instaxam.in/assets/images/spinner.gif) no-repeat center'});
	$.get('https://www.instaxam.in/include/most-famous-tests.php', { startFrom: totalFamousItems}, function(newitems){
		$('#famousLoadArea').append(newitems);
		updateStatusFamous();
		hideLoadMoreFamous();
	});
	return false;
};
function loadBrowseCatTests(){
	totalBrowseCatItems = totalBrowseCatItems + 5;
	$('#statusBrowseCat span').text('Loading more tests...');
	$('#statusBrowseCat').css({'height':'130px','background':'url(https://www.instaxam.in/assets/images/spinner.gif) no-repeat center'});
	$.get('https://www.instaxam.in/include/browse-cat.php', {
		startFrom: totalBrowseCatItems
		<?php
			if(isset($_REQUEST['exam_sub_cat3'])){
				echo ", exam_sub_cat3: '".$_REQUEST['exam_sub_cat3']."'";
			} else if(isset($_REQUEST['exam_sub_cat2'])){
				echo ", exam_sub_cat2: '".$_REQUEST['exam_sub_cat2']."'";
			} else if(isset($_REQUEST['exam_sub_cat1'])){
				echo ", exam_sub_cat1: '".$_REQUEST['exam_sub_cat1']."'";
			} else if(isset($_REQUEST['exam_cat'])){
				echo ", exam_cat: '".$_REQUEST['exam_cat']."'";
			}
		?>
		
	}, function(newitems){
		$('#browseCatLoadArea').append(newitems);
		updateStatusBrowseCat();
		hideLoadMoreBrowseCat();
	});
	return false;
};
function loadSearchCreator(){
	var creatorName = $("#creatorName").val();
	if(creatorName.length < 5){
        $('#creatorListLoadArea').text('');
        $('#creatorListLoadArea').append("<i style='color:red;'>Please enter a longer creator id / name to search for.</i>");
        alert("Please enter a longer creator id / name to search for.");
    } else if(creatorName != null) {
		$('#statusCreatorList span').text('Loading creators...');
		$('#statusCreatorList').css({'height':'130px','background':'url(https://www.instaxam.in/assets/images/spinner.gif) no-repeat center'});
		$.post('https://www.instaxam.in/include/creator-search.php', { searchCreator: creatorName}, function(newitems){
			$('#creatorListLoadArea').text('');
			$('#creatorListLoadArea').append(newitems);
			$('#statusCreatorList span').text('');
			$('#creator-tests').css({'display':'none'});
			$('#statusCreatorList').css({'background':'none','height':'0px'});
		});
	}
	location.replace("#creatorRow");
	return false;
};
</script>

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

			<?php 
				if(isset($_REQUEST['exam_cat'])||isset($_REQUEST['exam_sub_cat1'])||isset($_REQUEST['exam_sub_cat2'])||isset($_REQUEST['exam_sub_cat3'])) {
					include("include/browse-cat-where-clause.php");
			?>

            <section class="tables" id="browse-category">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h2>Tests for <?php echo $testCat; ?>:</h2>
                    </div>
                    <div class="alternate-table">
						
					<?php 
					if ($invalidCatSent == 1) {
						echo "<p><strong>Invalid Test Category - ".$testCat."</strong>. Please select a valid test category.</p>";
					} else if ($noTestFound == 1) {
						echo "<p><strong>Sorry! No tests found</strong> for ".$testCat."</p>";
					} else {
					?>
					
						<div id="browseCatLoadArea">
							<table><thead><tr><th style='width:30%'>Test Category</th><th style='width:70%'>Test Description</th></tr></thead></table>
						<?php 
							include("include/browse-cat.php");
							include("include/connect-database.php");
						?>
						</div>
						<p id="statusBrowseCat"><span ></span></p>
						<form action="<?php echo $page; ?>" method="get" onSubmit="return loadBrowseCatTests()">
							<div id="load-more-browse-cat" class="load-more">
                            	<button type="submit">Load More</button>
							</div>
                    	</form>

					<?php 
					}
					?>
                    </div>
                  </div>
                </div>
              </div>
            </section>


			<?php 
                }
			?>

            <div class="page-heading" id="test-category">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <h1>Browse Tests by Category:</h1>
					<?php 
					// exam category fetch -> starts
						$sql1 = "SELECT DISTINCT(`exam_cat`.`ec_cat_id`),
										`exam_cat`.`ec_cat_name`
								   FROM `exam_cat`,
								   		`exam_sub_cat1`,
								   		`exam_det`
								  WHERE `exam_sub_cat1`.`esc1_cat_id` = `exam_cat`.`ec_cat_id`
								    AND `exam_det`.`ed_sub_cat1_id` = `exam_sub_cat1`.`esc1_sub_cat1_id`
								  ORDER BY `exam_cat`.`ec_order` ASC
								";
						$result1=mysqli_query($con, $sql1);
						$rowCount1 = 0;
						while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
						{
							$rowCount1++;
							if($rowCount1==1){ echo "<ul>";}
							echo "<li><a  href='https://www.instaxam.in/".$pageFolder."/exam-category/".$row1['ec_cat_id']."'>";
							echo $row1['ec_cat_name'];
							echo "</a>";
							
						// exam sub category 1 fetch -> starts
							$sql2 = "SELECT DISTINCT(`exam_sub_cat1`.`esc1_sub_cat1_id`), `exam_sub_cat1`.`esc1_sub_cat1_name`
									   FROM `exam_sub_cat1`,
									   		`exam_det`
									  WHERE `exam_sub_cat1`.`esc1_cat_id`='".$row1['ec_cat_id']."'
									    AND `exam_det`.`ed_sub_cat1_id` = `exam_sub_cat1`.`esc1_sub_cat1_id`
									  ORDER BY `exam_sub_cat1`.`esc1_order` ASC
									";
							$result2=mysqli_query($con, $sql2);
							$rowCount2 = 0;
							while($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
							{
								$rowCount2++;
								if($rowCount2==1){ echo "<ul>";}
								echo "<li><a  href='https://www.instaxam.in/".$pageFolder."/exam-sub-category-level-1/".$row2['esc1_sub_cat1_id']."'>";
								echo $row2['esc1_sub_cat1_name'];
								echo "</a>";
								
							// exam sub category 2 fetch -> starts
								$sql3 = "SELECT DISTINCT(`exam_sub_cat2`.`esc2_sub_cat2_id`), `exam_sub_cat2`.`esc2_sub_cat2_name`
										   FROM `exam_sub_cat2`,
									   			`exam_det`
										  WHERE `exam_sub_cat2`.`esc2_sub_cat1_id`='".$row2['esc1_sub_cat1_id']."'
										    AND `exam_det`.`ed_sub_cat2_id` = `exam_sub_cat2`.`esc2_sub_cat2_id`
										  ORDER BY `exam_sub_cat2`.`esc2_order` ASC";
								$result3=mysqli_query($con, $sql3);
								$rowCount3 = 0;
								while($row3=mysqli_fetch_array($result3, MYSQLI_ASSOC))
								{
									$rowCount3++;
									if($rowCount3==1){ echo "<ul>";}
									echo "<li><a  href='https://www.instaxam.in/".$pageFolder."/exam-sub-category-level-2/".$row3['esc2_sub_cat2_id']."'>";
									echo $row3['esc2_sub_cat2_name'];
									echo "</a>";
									
								// exam sub category 3 fetch -> starts
									$sql4 = "SELECT DISTINCT(`exam_sub_cat3`.`esc3_sub_cat3_id`), `exam_sub_cat3`.`esc3_sub_cat3_name`
											   FROM `exam_sub_cat3`,
									   				`exam_det`
											  WHERE `exam_sub_cat3`.`esc3_sub_cat2_id`='".$row3['esc2_sub_cat2_id']."'
											    AND `exam_det`.`ed_sub_cat3_id` = `exam_sub_cat3`.`esc3_sub_cat3_id`
											  ORDER BY `exam_sub_cat3`.`esc3_order` ASC";
									$result4=mysqli_query($con, $sql4);
									$rowCount4 = 0;
									while($row4=mysqli_fetch_array($result4, MYSQLI_ASSOC))
									{
										$rowCount4++;
										if($rowCount4==1){ echo "<ul>";}
										echo "<li><a  href='https://www.instaxam.in/".$pageFolder."/exam-sub-category-level-3/".$row4['esc3_sub_cat3_id']."'>";
										echo $row4['esc3_sub_cat3_name'];
										echo "</a>";
										
										
										
										echo "</li>";
									}
									if($rowCount4>0){ echo "</ul>";}
								// exam sub category 3 fetch -> ends
									
									echo "</li>";
								}
								if($rowCount3>0){ echo "</ul>";}
							// exam sub category 2 fetch -> ends
								
								echo "</li>";
							}
							if($rowCount2>0){ echo "</ul>";}
						// exam sub category 1 fetch -> ends
							
							echo "</li>";
						}
						if($rowCount1>0){ echo "</ul>";} else {
							echo "<p id='endRecentItems'><span >No tests are available as of now.</span></p>";
						}
					// exam category fetch -> ends
					?>
					
                  </div>
                </div>
              </div>
            </div>
            
			
			<?php 
				if(!((isset($_REQUEST['exam_cat'])||isset($_REQUEST['exam_sub_cat1'])||isset($_REQUEST['exam_sub_cat2'])||isset($_REQUEST['exam_sub_cat3'])))) {
			?>
            <section class="tables" id="recently-added">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h2>Recently Added Tests:</h2>
                    </div>
                    <div class="alternate-table">
					
						<div id="recentLoadArea">
							<table><thead><tr><th style='width:30%'>Test Category</th><th style='width:70%'>Test Description</th></tr></thead></table>
						<?php 
							include("include/recently-added-tests.php");
							include("include/connect-database.php");
						?>
						</div>
						<p id="statusRecent"><span ></span></p>
						<form action="<?php echo $page; ?>" method="get" onSubmit="return loadRecentTests()">
							<div id="load-more-recent" class="load-more">
                            	<button type="submit">Load More</button>
							</div>
                    	</form>
					
                    </div>
                  </div>
                </div>
              </div>
            </section>

			
			<section class="tables" id="most-famous">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h2>Most Famous Tests:</h2>
                    </div>
                    <div class="alternate-table">
					
						<div id="famousLoadArea">
							<table><thead><tr><th style='width:30%'>Test Category</th><th style='width:70%'>Test Description</th></tr></thead></table>
						<?php 
							include("include/most-famous-tests.php");
							include("include/connect-database.php");
						?>
						</div>
						<p id="statusFamous"><span ></span></p>
						<form action="<?php echo $page; ?>" method="get" onSubmit="return loadFamousTests()">
							<div id="load-more-famous" class="load-more">
                            	<button type="submit">Load More</button>
							</div>
                    	</form>

                    </div>
                  </div>
                </div>
              </div>
            </section>


            <div class="page-heading" id="test-by-creator">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
				  	<br /><br />
                    <h2>Search Tests by Creator:</h2>

                    <form id="contact" action="<?php echo $page; ?>" method="post" onSubmit="return loadSearchCreator()">
                        <div id="creator-row">
							<div id="creator-col1">
                            	<input name="creatorName" type="text" class="form-control" id="creatorName" placeholder="Creator Id / Name ....." required="">
							</div>
							<div id="creator-col2">
                            	<button type="submit" id="form-submit" class="button">Search Creator</button>
							</div>
                        </div>
                    </form>
					<div id="creatorListLoadArea">
					</div>
					<p id="statusCreatorList"><span ></span></p>
					
                  </div>
                </div>
              </div>
            </div>		


			<?php 
				}
			?>

			
          </div>
		  
		  <?php 
			include("include/footer.php");
		  ?>

        </div>
		<?php 
			include("include/sidebar.php");
		?>
    </div>

  <!-- Scripts -->
    <script src="https://www.instaxam.in/assets/js/jquery.min-bootstrap.bundle.min.js"></script>
    <script src="https://www.instaxam.in/assets/js/instaxam-all-script.js"></script>
</body>

</html>
