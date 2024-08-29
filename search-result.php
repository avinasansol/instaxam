<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	include("include/connect-database.php");
	include("include/login.php");
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Search exams and mock tests on Instaxam.In">
    <meta name="author" content="JhaAvinash">
    <meta name="keywords" content="Instaxam, Search, Results, Tests">

    <title>Instaxam.In - Search Results</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/instaxam-style.css">

<script type="text/javascript" src="assets/js/jquery-1.3.2.js" ></script>
<script type="text/javascript">
function updateStatusResults(){
	$('#statusResults span').text('');
	$('#statusResults').css({'background':'none','height':'0px'});
};
function hideLoadMoreResults(){
	var endResults = $("#endResultsItems").val();
	if(endResults != null) {
		$('#load-more-result').css({'display':'none'});
	}
};
function loadMoreResults(totalResultsItems){
	var srchTxt = $("#srchT").text();
	$('#statusResults span').text('Loading more tests...');
	$('#statusResults').css({'height':'130px','background':'url(assets/images/spinner.gif) no-repeat center'});
	$.get('include/search-result-load.php', { 
		startFrom: totalResultsItems,
		searchTxt: srchTxt
	}, function(newitems){
		document.getElementById("resultLoadArea").innerHTML=newitems;
		updateStatusResults();
		hideLoadMoreResults();
	});
	return false;
};
</script>
<style>
.srchT {
	background-color:#FFFF66;
}
</style>

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

            <section class="tables" style="margin-top:-80px;">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h1>Search Results for '<b id="srchT" class="srchT"><?php if(isset($_REQUEST['searchTxt'])){ echo strtoupper(htmlspecialchars($_REQUEST['searchTxt']));}?></b>':</h1>
                    </div>
                    <div class="alternate-table">
					
						<div id="resultLoadArea">
							<table><thead><tr><th style='width:30%'>Test Category</th><th style='width:70%'>Test Description</th></tr></thead></table>
						<?php 
							include("include/search-result-load.php");
							include("include/connect-database.php");
						?>
						</div>
						<p id="statusResults"><span ></span></p>
					
                    </div>
                  </div>
                </div>
              </div>
            </section>
			
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
