<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$examId = "";
	if(isset($_REQUEST['test_id'])){
		$examId = $_REQUEST['test_id'];
	} else {
		header("Location: https://www.instaxam.in/index.php");
	}
	
	$loggedUserId = "";
	if(isset($_SESSION['LogdUsrDet']))
	{
		$loggedUserId = $_SESSION['LogdUsrDet'][1];
	}
	$Err="";
	include("include/change-test-status.php");
	include("include/connect-database.php");
	include("include/login.php");
	if( isset($_POST['UserId']) && isset($_POST['TestId']) && isset($_POST['ActType']) )
	{
		include("include/access-approve.php");
	}
	$testID = "";
	$testCat = "";
	$upsc = 0;
	$createdOn = "";
	$createdById = "";
	$createdBy = "";
	$description = "";
	$syllabus = "";
	$availableFor = "";
	$availableForUser = "NO";
	$status = "";
	$negatieMark = "";
	$questionsCount = 0;
	$timePer10Ques = 0;
	$totalTime = 0;
	$marksPerQues = 0;
	$totalMarks = 0;
	$participantsCount = 0;
	$maxRetakeAllowed = 0;
	$examTakenCount = 0;
	$examAllowedCountForUser = 0;
	$sql1 = "SELECT `exam_det`.`ed_exam_id`,
					`exam_sub_cat1`.`esc1_sub_cat1_name`,
					`exam_det`.`ed_sub_cat2_id`,
					`exam_det`.`ed_sub_cat3_id`,
					`exam_det`.`ed_created_on`,
					`exam_det`.`ed_created_by`,
					`user_det`.`ud_first_name`,
					`user_det`.`ud_last_name`,
					`exam_det`.`ed_exam_desc`,
					`exam_det`.`ed_syllabus`,
					`exam_det`.`ed_avlbl_for`,
					`exam_det`.`ed_negatie_mark`,
					`exam_det`.`ed_status`,
					`exam_det`.`ed_marks_per_ques`,
					`exam_det`.`ed_time_per10_ques`,
					`exam_det`.`ed_max_retake`
			   FROM `exam_det`,
			   		`user_det`,
			   		`exam_sub_cat1`
			  WHERE `exam_det`.`ed_sub_cat1_id` = `exam_sub_cat1`.`esc1_sub_cat1_id`
			    AND `exam_det`.`ed_created_by` = `user_det`.`ud_user_id`
			    AND `exam_det`.`ed_exam_id` ='".$examId."'
			 ";
	$result1=mysqli_query($con, $sql1);
	if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{
		$testID = $row1['ed_exam_id'];
		$createdById = $row1['ed_created_by'];
		$createdOn = substr($row1['ed_created_on'],0,10);
		$createdBy = $row1['ud_first_name']." ".$row1['ud_last_name'];
		$description = $row1['ed_exam_desc'];
		$syllabus = $row1['ed_syllabus'];
		$maxRetakeAllowed = (int)$row1['ed_max_retake'];
		$timePer10Ques = (int)$row1['ed_time_per10_ques'];
		$marksPerQues  = (int)$row1['ed_marks_per_ques'];
		
		$testCat = $row1['esc1_sub_cat1_name'];
		$sql2 = "SELECT `exam_sub_cat2`.`esc2_sub_cat2_name`\n"
			  . "   ,`exam_sub_cat2`.`esc2_sub_cat2_id`\n"
			  . "  FROM `exam_sub_cat2`\n"
			  . " WHERE `exam_sub_cat2`.`esc2_sub_cat2_id` = '".$row1['ed_sub_cat2_id']."'";
		$result2=mysqli_query($con, $sql2);
		if($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
		{
			$testCat = $row2['esc2_sub_cat2_name'];
          	if($row2['esc2_sub_cat2_id']=="CNTJOBUPSC"){
              $upsc = 1;
            }
		}
		$sql3 = "SELECT `exam_sub_cat3`.`esc3_sub_cat3_name`\n"
			  . "  FROM `exam_sub_cat3`\n"
			  . " WHERE `exam_sub_cat3`.`esc3_sub_cat3_id` = '".$row1['ed_sub_cat3_id']."'";
		$result3=mysqli_query($con, $sql3);
		if($row3=mysqli_fetch_array($result3, MYSQLI_ASSOC))
		{
			$testCat = $testCat." -> ".$row3['esc3_sub_cat3_name'];
		}
		$sql4 = "SELECT COUNT(DISTINCT `exam_taken`.`et_user_id`) AS `taken_count` 
				   FROM `exam_taken`
				  WHERE `exam_taken`.`et_exam_id` = '".$row1['ed_exam_id']."'
				 ";
		$result4=mysqli_query($con, $sql4);
		if($row4=mysqli_fetch_array($result4, MYSQLI_ASSOC))
		{
			$participantsCount = (int)$row4['taken_count'];
		}
		$sql6 = "SELECT COUNT(`ques_det`.`qd_ques_no`) AS `ques_count` 
				   FROM `ques_det`
				  WHERE `ques_det`.`qd_exam_id` = '".$row1['ed_exam_id']."'
					AND `qd_del_ind` != 'Y'
				 ";
		$result6=mysqli_query($con, $sql6);
		if($row6=mysqli_fetch_array($result6, MYSQLI_ASSOC))
		{
			$questionsCount = (int)$row6['ques_count'];
		}
		
		if($row1['ed_avlbl_for'] == "A") {
			$availableFor = "All Users";
		} else if($row1['ed_avlbl_for'] == "S") {
			if(isset($_SESSION['LogdUsrDet'])) { 
				$loggedUserId = $_SESSION['LogdUsrDet'][1];
				$sql5 = "SELECT `eaf_avlblty`
						   FROM `exam_avlbl_for`
						  WHERE `eaf_exam_id` = '".$testID."'
							AND `eaf_user_id` = '".$loggedUserId."'
							AND (`eaf_avlblty` = 'Y' OR `eaf_avlblty` = 'N' OR `eaf_avlblty` = 'R')
						";
				$result5=mysqli_query($con, $sql5);
				if($row5=mysqli_fetch_array($result5, MYSQLI_ASSOC))
				{
					if($row5['eaf_avlblty']=="Y"){
						$availableForUser = "YES";
					}
					if($row5['eaf_avlblty']=="N"){
						$availableForUser = "RSD";
					}
					if($row5['eaf_avlblty']=="R"){
						$availableForUser = "REJ";
					}
				} else {
					$availableForUser = "NO";
				}
			}
			$availableFor = "Selected Users";
		} else {
			$availableFor = "Unknown";
		}
		
		if($row1['ed_status'] == "A") {
			$status = "Active";
		} else if($row1['ed_status'] == "D") {
			$status = "Deactivated";
		} else {
			$status = "Unknown";
		}
		
		if($row1['ed_negatie_mark'] == "Y") {
			$negatieMark = "Yes [1/3rd per incorrect answer]";
		} else if($row1['ed_negatie_mark'] == "N") {
			$negatieMark = "No";
		} else {
			$negatieMark = "Unknown";
		}
		
		if($syllabus == "") {
			$syllabus = "Not Provided";
		}
	} else {
		header("Location: https://www.instaxam.in/index.php");
	}
	$totalTime = ( $questionsCount * $timePer10Ques / 10 );
	$totalTimeHr = "";
	$totalTimeMn = "";
	if(floor($totalTime/60)==0){
		$totalTimeHr = "00";
	} else if(floor($totalTime/60)<10){
		$totalTimeHr = "0".floor($totalTime/60);
	} else {
		$totalTimeHr = floor($totalTime/60);
	}
	if(floor($totalTime%60)==0){
		$totalTimeMn = "00";
	} else if(floor($totalTime%60)<10){
		$totalTimeMn = "0".floor($totalTime%60);
	} else {
		$totalTimeMn = floor($totalTime%60);
	}
	$totalTimeFrmtd = $totalTimeHr.":".$totalTimeMn;
	$totalMarks = ( $questionsCount * $marksPerQues );


/*
    Create Keyword Tags from Text
*/
function srch_keywords($string, $min_word_length = 3, $min_word_occurrence = 2, $as_array = false, $max_words = 8, $restrict = false) {
   function keyword_count_sort($first, $sec) {
     return $sec[1] - $first[1];
   }
   $string = preg_replace('/[^\p{L}0-9 ]/', ' ', $string);
   $string = trim(preg_replace('/\s+/', ' ', $string));
   $words = explode(' ', $string);
   /*
    Only compare to common words if $restrict is set to false
    Tags are returned based on any word in text
    If we don't restrict tag usage, we'll remove common words from array
   */
   if ($restrict === false) {
      $commonWords = array('a','rarr','amp','bull','ndash','able','about','above','abroad','according','accordingly','across','actually','adj','after','afterwards','again','against','ago','ahead','ain\'t','all','allow','allows','almost','alone','along','alongside','already','also','although','always','am','amid','amidst','among','amongst','an','and','another','any','anybody','anyhow','anyone','anything','anyway','anyways','anywhere','apart','appear','appreciate','appropriate','are','aren\'t','around','as','a\'s','aside','ask','asking','associated','at','available','away','awfully','b','back','backward','backwards','be','became','because','become','becomes','becoming','been','before','beforehand','begin','behind','being','believe','below','beside','besides','best','better','between','beyond','both','brief','but','by','c','came','can','cannot','cant','can\'t','caption','cause','causes','certain','certainly','changes','clearly','c\'mon','co','co.','com','come','comes','concerning','consequently','consider','considering','contain','containing','contains','corresponding','could','couldn\'t','course','c\'s','currently','d','dare','daren\'t','definitely','described','despite','did','didn\'t','different','directly','do','does','doesn\'t','doing','done','don\'t','down','downwards','during','e','each','edu','eg','eight','eighty','either','else','elsewhere','end','ending','enough','entirely','especially','et','etc','even','ever','evermore','every','everybody','everyone','everything','everywhere','ex','exactly','example','except','f','fairly','far','farther','few','fewer','fifth','first','five','followed','following','follows','for','forever','former','formerly','forth','forward','found','four','from','further','furthermore','g','get','gets','getting','given','gives','go','goes','going','gone','got','gotten','greetings','h','had','hadn\'t','half','happens','hardly','has','hasn\'t','have','haven\'t','having','he','he\'d','he\'ll','hello','help','hence','her','here','hereafter','hereby','herein','here\'s','hereupon','hers','herself','he\'s','hi','him','himself','his','hither','home','hopefully','how','howbeit','however','hundred','i','i\'d','ie','if','ignored','i\'ll','i\'m','immediate','in','inasmuch','inc','inc.','indeed','indicate','indicated','indicates','inner','inside','insofar','instead','into','inward','is','isn\'t','it','it\'d','it\'ll','its','it\'s','itself','i\'ve','j','just','k','keep','keeps','kept','know','known','knows','l','last','lately','later','latter','latterly','least','less','lest','let','let\'s','like','liked','likely','likewise','little','look','looking','looks','low','lower','ltd','m','made','mainly','make','makes','many','may','maybe','mayn\'t','me','mean','meantime','meanwhile','merely','might','mightn\'t','mine','minus','miss','more','moreover','most','mostly','mr','mrs','much','must','mustn\'t','my','myself','n','name','namely','nd','near','nearly','necessary','need','needn\'t','needs','neither','never','neverf','neverless','nevertheless','new','next','nine','ninety','no','nobody','non','none','nonetheless','noone','no-one','nor','normally','not','nothing','notwithstanding','novel','now','nowhere','o','obviously','of','off','often','oh','ok','okay','old','on','once','one','ones','one\'s','only','onto','opposite','or','other','others','otherwise','ought','oughtn\'t','our','ours','ourselves','out','outside','over','overall','own','p','particular','particularly','past','per','perhaps','placed','please','plus','possible','presumably','probably','provided','provides','q','que','quite','qv','r','rather','rd','re','really','reasonably','recent','recently','regarding','regardless','regards','relatively','respectively','right','round','s','said','same','saw','say','saying','says','second','secondly','see','seeing','seem','seemed','seeming','seems','seen','self','selves','sensible','sent','serious','seriously','seven','several','shall','shan\'t','she','she\'d','she\'ll','she\'s','should','shouldn\'t','since','six','so','some','somebody','someday','somehow','someone','something','sometime','sometimes','somewhat','somewhere','soon','sorry','specified','specify','specifying','still','sub','such','sup','sure','t','take','taken','taking','tell','tends','th','than','thank','thanks','thanx','that','that\'ll','thats','that\'s','that\'ve','the','their','theirs','them','themselves','then','thence','there','thereafter','thereby','there\'d','therefore','therein','there\'ll','there\'re','theres','there\'s','thereupon','there\'ve','these','they','they\'d','they\'ll','they\'re','they\'ve','thing','things','think','third','thirty','this','thorough','thoroughly','those','though','three','through','throughout','thru','thus','till','to','together','too','took','toward','towards','tried','tries','truly','try','trying','t\'s','twice','two','u','un','under','underneath','undoing','unfortunately','unless','unlike','unlikely','until','unto','up','upon','upwards','us','use','used','useful','uses','using','usually','v','value','various','versus','very','via','viz','vs','w','want','wants','was','wasn\'t','way','we','we\'d','welcome','well','we\'ll','went','were','we\'re','weren\'t','we\'ve','what','whatever','what\'ll','what\'s','what\'ve','when','whence','whenever','where','whereafter','whereas','whereby','wherein','where\'s','whereupon','wherever','whether','which','whichever','while','whilst','whither','who','who\'d','whoever','whole','who\'ll','whom','whomever','who\'s','whose','why','will','willing','wish','with','within','without','wonder','won\'t','would','wouldn\'t','x','y','yes','yet','you','you\'d','you\'ll','your','you\'re','yours','yourself','yourselves','you\'ve','z','zero');
      $words = array_udiff($words, $commonWords,'strcasecmp');
   }
   /* Restrict Keywords based on values in the $allowedWords array */
   if ($restrict !== false) {
      $allowedWords =  array('engine','boeing','electrical','pneumatic','ice');
      $words = array_uintersect($words, $allowedWords,'strcasecmp');
   }
   $keywords = array();
   while(($c_word = array_shift($words)) !== null) {
     if (strlen($c_word) < $min_word_length) continue;
     $c_word = strtolower($c_word);
        if (array_key_exists($c_word, $keywords)) $keywords[$c_word][1]++;
        else $keywords[$c_word] = array($c_word, 1);
   }
   usort($keywords, 'keyword_count_sort');
   $final_keywords = array();
   foreach ($keywords as $keyword_det) {
     if ($keyword_det[1] < $min_word_occurrence) break;
     array_push($final_keywords, $keyword_det[0]);
   }
  $final_keywords = array_slice($final_keywords, 0, $max_words);
 return $as_array ? $final_keywords : implode(', ', $final_keywords);
}

?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Test Details for <?php if(strlen($description)>60){echo substr($description,0,60).".";} else {echo $description;} ?>">
    <meta name="author" content="JhaAvinash">
    <meta name="keywords" content="<?php 
        $srchstr = $testCat.str_replace("\n","<br />",$description).str_replace("\n","<br />",$syllabus);
  		echo srch_keywords($srchstr, $min_word_length = 3, $min_word_occurrence = 2, $as_array = false, $max_words = 10, $restrict = false);
                                   ?>">

    <title>Instaxam.In - <?php if(strlen($description)>30){echo substr($description,0,strpos($description," ")).".. ".substr($description,(strlen($description)-25),25);} else {echo $description;} ?></title>
    <link rel="icon" type="image/png" href="https://www.instaxam.in/assets/images/favicon.ico">

    <link rel="stylesheet" href="https://www.instaxam.in/assets/css/instaxam-style.css">
    <link rel="stylesheet" href="https://www.instaxam.in/assets/css/test-details.css">

<script type="text/javascript">
function raiseAccessReq(){
	var TestId = $("#TestId").val();
	$.post('https://www.instaxam.in/test-access-req.php', { TestId: TestId}, function(newitems){
		alert(newitems);
	});
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
				
				if(isset($_SESSION['LogdUsrDet']))
				{
					$examTakenCount = 0;
					$sqlUserResults = "SELECT `et_exam_no`, 
									`et_end_ts`, 
									`et_marks`
							   FROM `exam_taken`
							  WHERE `et_exam_id` = '".$testID."'
								AND `et_user_id` = '".$loggedUserId."'
								AND `et_end_ts` IS NOT NULL
							  ORDER BY `et_end_ts` ASC
							";
					$result1=mysqli_query($con, $sqlUserResults);
					while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{
						$examTakenCount++;
					}
					$examAllowedCountForUser = ($maxRetakeAllowed- $examTakenCount);
				}
			?>

			<section class="tables" style="margin-top:-80px;">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h1><?php if(strlen($description)>70){echo substr($description,0,70)."......";} else {echo $description.":";} ?></h1>
                    </div>
                    <div class="alternate-table">
						
										
						<div id="browseCatLoadArea">
						<table>
							<tbody>
								<tr>
									<td>Test ID: </td>
									<td><?php echo $testID; ?></td>
								</tr>
								<tr>
									<td>Created On: </td>
									<td><?php echo $createdOn; ?></td>
								</tr>
								<tr>
									<td>Created By: </td>
									<td><?php echo $createdBy; ?></td>
								</tr>
								<tr>
									<td>Category: </td>
									<td><?php echo $testCat; ?></td>
								</tr>
								<tr>
									<td>Details: </td>
									<td><?php echo str_replace("\n","<br />",$description); ?></td>
								</tr>
								<tr>
									<td>Syllabus: </td>
									<td>
                                      	<?php 
                                      		echo str_replace("\n","<br />",$syllabus); 
                                      		if($upsc==1){
                                              	echo "<br /><br /><a href='https://www.instaxam.in/upsc-reference-materials.php'>Reference Materials for UPSC Civil Services Exam</a>";
                                            }
                                      	?>
                                  	</td>
								</tr>
								<tr>
									<td>Available For: </td>
									<td>
										<?php 
											echo $availableFor;
											if(isset($_SESSION['LogdUsrDet'])) { 
												if($availableFor == "Selected Users") {
													if($availableForUser == "YES") {
														echo " [Available for you]";
													} else if($availableForUser == "RSD") {
														echo " [Access request raised]";
													} else if($availableForUser == "REJ") {
														echo " [Your access request has been rejected]";
													} else {
														echo " [Not available for you]";
													}
												}
											}
										?>
									</td>
								</tr>
								<tr>
									<td>Status: </td>
									<td><?php echo $status; ?></td>
								</tr>
								<tr>
									<td>Questions Count: </td>
									<td><?php echo $questionsCount; ?></td>
								</tr>
								<tr>
									<td>Total Time: </td>
									<td><?php echo $totalTimeFrmtd; ?></td>
								</tr>
								<tr>
									<td>Total Marks: </td>
									<td><?php echo $totalMarks; ?></td>
								</tr>
								<tr>
									<td>Negatie Marking: </td>
									<td><?php echo $negatieMark; ?></td>
								</tr>
								<tr>
									<td>Participants Count: </td>
									<td><?php echo $participantsCount; ?></td>
								</tr>
								<tr>
									<td>Available Attempts: </td>
									<td>
										<?php 
											if(isset($_SESSION['LogdUsrDet'])) { 
												if($examAllowedCountForUser > 0) {
													echo $examAllowedCountForUser."/"; 
												} else {
													echo "0/";
												}
											}
											echo $maxRetakeAllowed;
											$takeTest = 0;
											if(isset($_SESSION['LogdUsrDet'])) { 
												$sql1 = "SELECT `et_exam_no`
														   FROM `exam_taken`
														  WHERE `et_exam_id` = '".$testID."'
															AND `et_user_id` = '".$loggedUserId."'
															AND `et_end_ts` IS NULL
														";
												$result1=mysqli_query($con, $sql1);
												if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
												{
													$takeTest = 3;
												} else if($examAllowedCountForUser > 0) {
													if( ($availableFor == "Selected Users") && ($availableForUser == "NO") ) {
														$takeTest = 4;
													} else if( ($availableFor == "Selected Users") && ( ($availableForUser == "RSD") || ($availableForUser == "REJ") ) ) {
														$takeTest = 5;
													} else {
														if($examTakenCount>0){
															$takeTest = 2;
														} else {
															$takeTest = 1;
														}
													}
												} else {
													echo " [No More Attempts Left]";
												}
											} else {
												$takeTest = 1;
											}
										?>
									</td>
								</tr>
							</tbody>
						</table>
						<?php 
							if($loggedUserId == $createdById){
						?>
							<form action="<?php echo $page; ?>" method="post" onSubmit="return changeTestStatus(<?php if($status == "Active"){ echo "'D'"; } else { echo "'A'"; }?>)" >
								<input type="hidden" name="changeAct" value="<?php if($status == "Active"){ echo "D"; } else { echo "A"; }?>" />
								<div id="load-more" class="load-more">
								<button type="submit" style="width:40%;" name="TestId" value="<?php echo $testID; ?>"><?php if($status == "Active"){ echo "Deactivate"; } else { echo "Activate"; }?></button>
								</div>
							</form>
							<form action="https://www.instaxam.in/edit-test.php" method="post"<?php if($status == "Active"){?> onSubmit="return editTest()"<?php }?>>
								<div id="load-more" class="load-more">
								<button style="width:40%;" type="submit" name="TestId" value="<?php echo $testID; ?>">Edit Test</button>
								</div>
							</form>
						<?php 
							}
							if( ($questionsCount>0) && ($status == "Active") && ( ($takeTest == 1) || ($takeTest == 2) || ($takeTest == 3) ) ){
						?>
						
							<form action="https://www.instaxam.in/take-test.php" method="post"<?php if( (!isset($_SESSION['LogdUsrDet'])) && ( ($takeTest == 1) || ($takeTest == 2) ) ){ ?> onSubmit="return loginErr()"<?php } ?>>
								<div id="load-more" class="load-more">
									<input type="hidden" name="TestId" value="<?php echo $testID; ?>" />
									<button type="submit" style="width:40%;">
									<?php 
										if($takeTest == 1) {
											echo "Take Test";
										} else if($takeTest == 2) {
											echo "Retake Test"; 
										} else if($takeTest == 3) {
											echo "Resume Test"; 
										}
									?>
									</button>
								</div>
							</form>
						
						<?php 
							} else if(($takeTest == 4)||($takeTest == 5)) {
						?>
						
							<form action="https://www.instaxam.in/test-access-req.php" method="post" onSubmit="return raiseAccessReq()">
								<div id="load-more" class="load-more">
									<input type="hidden" name="TestId" id="TestId" value="<?php echo $testID; ?>" />
									<button type="submit" style="width:40%;">Raise Access Request<?php if($takeTest == 5) { echo " Again"; } ?></button>
								</div>
							</form>
						
						<?php 
							}
						?>
						
						</div>

					 </div>
                  </div>
                </div>
              </div>
            </section>
			<?php 
			if(isset($_SESSION['LogdUsrDet']))
			{
				if($loggedUserId == $createdById)
				{
					if($availableFor == "Selected Users") 
					{
					?>	
					<?php 
					include("include/access-req-list.php");
					?>
					<?php 
					}
				}
				$rowCount1 = 0;
				$selectedTestNumber = 0;
				$result1=mysqli_query($con, $sqlUserResults);
				while($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
				{
					$rowCount1++;
					$selectedTestNumber = (int)$row1['et_exam_no'];
					$examTakenOn = substr($row1['et_end_ts'],0,10);
					$marks = (float)$row1['et_marks'];
					$rank = 0;
					$sql2 = "SELECT (COUNT(`DEVT`.`et_exam_no`)+1) AS `rank`
							   FROM (
									 SELECT `et_exam_no`,
											`et_marks`
									   FROM `exam_taken`
									  WHERE `et_exam_id` = '".$testID."'
										AND `et_user_id` != '".$loggedUserId."'
										AND `et_end_ts` IS NOT NULL
									  GROUP BY `et_user_id`
									 HAVING MIN(`et_end_ts`)
							  		) AS `DEVT`
							  WHERE `DEVT`.`et_marks` > ".$marks."
							";
					$result2=mysqli_query($con, $sql2);
					if($row2=mysqli_fetch_array($result2, MYSQLI_ASSOC))
					{
						$rank = (int)$row2['rank'];
					}
			?>
				<section class="tables">
				  <div class="container-fluid">
					<div class="row">
					  <div class="col-md-12">
						<div class="section-heading">
						  <h2>Test Attempt<?php if($examTakenCount>1){ echo " ".$rowCount1;}?>: </h2>
						</div>
						<div class="alternate-table">
							
							<table>
								<tbody>
									<tr>
										<td>Taken On: </td>
										<td><?php echo $examTakenOn; ?></td>
									</tr>
									<tr>
										<td>Marks: </td>
										<td><?php echo $marks."/".$totalMarks; ?></td>
									</tr>
									<?php if($rowCount1==1){ ?>
									<tr>
										<td>Rank: </td>
										<td><?php echo $rank."/".$participantsCount; ?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							<form action="https://www.instaxam.in/test-results.php" method="post">
								<div id="load-more" class="load-more">
									<button type="submit" name="TestNumber" value="<?php echo $selectedTestNumber; ?>">Complete Results &amp; Solution</button>
								</div>
							</form>
						 </div>
					  </div>
					</div>
				  </div>
				</section>
			<?php 
				}
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
<?php if($Err !="") { 
?>
	<script>
	$('document').ready(function(){
		alert("<?php echo $Err; ?>");
	});
	</script>
<?php 
} ?>
<?php if( (!isset($_SESSION['LogdUsrDet'])) && ( ($takeTest == 1) || ($takeTest == 2) ) ){ ?>
<script>
function loginErr(){
	alert('Please login to take test.');
	location.replace("#user-msg");
	return false;
};
</script>
<?php } ?>
<?php if($status == "Active"){?>
<script>
function editTest(){
	if (confirm('The test will be deactivated!\nThis may force some users to submit incomplete test.\nAre you sure you want to edit test?')) {
		return true;
	}
	return false;
};
</script>
<script>
function changeTestStatus(changeAct){
	if(changeAct=='D'){
		if (!confirm('This may force some users to submit incomplete test.\nAre you sure you want to deactivate the test?')) {
			return false;
		}
	}
	return true;
};
</script>
<?php }?>
</body>

</html>
