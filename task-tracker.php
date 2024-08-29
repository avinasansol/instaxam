<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "task-tracker.php";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
	include("include/connect-database.php");
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">

    <title>Instaxam.In - Task Tracker</title>
	<link rel="icon" type="image/png" href="assets/images/favicon.ico">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/all-style.css">
    <link rel="stylesheet" href="assets/css/owl.css">
    <link rel="stylesheet" href="assets/css/test-history.css">
    <style>
    table {
    text-align: left;
    position: relative;
    border-collapse: collapse; 
    }
    th {
    background: #e7e7e7;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }
    td {
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
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
            <!-- Services -->
				<div id="history-table">
                    <form action="task-tracker.php" method="post">
                    <h2 style="font-size:18px;">
                        Task Tracker:
                        <select name="Month" style="font-size:14px;" onchange="this.form.submit()">
                            <?php if( (isset($_POST['Month'])) && ( ($_POST['Month']==date('Y-m-01')) || ($_POST['Month']==date('Y-m-01', strtotime('-1 MONTH'))) || ($_POST['Month']==date('Y-m-01', strtotime('-2 MONTH'))) || ($_POST['Month']==date('Y-m-01', strtotime('-3 MONTH'))) || ($_POST['Month']==date('Y-m-01', strtotime('-4 MONTH'))) || ($_POST['Month']==date('Y-m-01', strtotime('-5 MONTH'))) ) ){?>
                                <option value="<?php echo $_POST['Month'];?>"><?php echo date('M, Y', strtotime($_POST['Month']));?></option>
                            <?php }?>
                            <?php if( date('M, Y', strtotime($_POST['Month'])) != date('M, Y') ){ ?>
                            <option value="<?php echo date('Y-m-01');?>"><?php echo date('M, Y');?></option>
                            <?php } ?>
                            <?php if( date('M, Y', strtotime($_POST['Month'])) != date('M, Y', strtotime('-1 MONTH')) ){ ?>
                            <option value="<?php echo date('Y-m-01', strtotime('-1 MONTH'));?>"><?php echo date('M, Y', strtotime('-1 MONTH'));?></option>
                            <?php } ?>
                            <?php if( date('M, Y', strtotime($_POST['Month'])) != date('M, Y', strtotime('-2 MONTH')) ){ ?>
                            <option value="<?php echo date('Y-m-01', strtotime('-2 MONTH'));?>"><?php echo date('M, Y', strtotime('-2 MONTH'));?></option>
                            <?php } ?>
                            <?php if( date('M, Y', strtotime($_POST['Month'])) != date('M, Y', strtotime('-3 MONTH')) ){ ?>
                            <option value="<?php echo date('Y-m-01', strtotime('-3 MONTH'));?>"><?php echo date('M, Y', strtotime('-3 MONTH'));?></option>
                            <?php } ?>
                            <?php if( date('M, Y', strtotime($_POST['Month'])) != date('M, Y', strtotime('-4 MONTH')) ){ ?>
                            <option value="<?php echo date('Y-m-01', strtotime('-4 MONTH'));?>"><?php echo date('M, Y', strtotime('-4 MONTH'));?></option>
                            <?php } ?>
                            <?php if( date('M, Y', strtotime($_POST['Month'])) != date('M, Y', strtotime('-5 MONTH')) ){ ?>
                            <option value="<?php echo date('Y-m-01', strtotime('-5 MONTH'));?>"><?php echo date('M, Y', strtotime('-5 MONTH'));?></option>
                            <?php } ?>
                        </select>
                    </h2>
                    </form>
                    <form action="task-tracker-update.php" method="get">
                        <button type="submit">Open In Edit Mode</button>
                    </form><br />
					<div>
					<table>
                        <thead>
                        <tr>
                            <th>Date</th>
                            <?php 
                                $sql1 = "SELECT `tm_task_name`,
                                                `tm_task_type`,
                                                `tm_remark`,
                                                `tm_remark_type`,
                                                `tm_remark_name`
                                            FROM `task_mgr`
                                           WHERE `tm_user_id`='".$loggedUserId."'
                                           ORDER BY `tm_task_no`
                                        ";
                                $result1 = mysqli_query($con, $sql1);
                                while($row1 = mysqli_fetch_array($result1))
                                {
                                    if($row1['tm_task_type']=="AN"){
                                        echo "<th style='min-width:400px;'>".$row1['tm_task_name']."</th>";
                                    } else {
                                        echo "<th>".$row1['tm_task_name']."</th>";
                                    }
                                    if($row1['tm_remark']=="Y"){
                                        if($row1['tm_remark_type']=="AN"){
                                            echo "<th style='min-width:400px;'>".$row1['tm_remark_name']."</th>";
                                        } else {
                                            echo "<th>".$row1['tm_remark_name']."</th>";
                                        }
                                    }
                                }
                            ?>
                        </tr>
                        </thead>
                        <tbody>
                            <?php 
                                for($i=0;$i<10;$i++){
                                    $task[$i][0] = 0;
                                    $task[$i][1] = 0;
                                    $taskCnt[$i][0] = 0;
                                    $taskCnt[$i][1] = 0;
                                    $taskRowspan[$i] = 0;
                                }
                                
                                if( (isset($_POST['Month'])) && ( ($_POST['Month']==date('Y-m-01', strtotime('-1 MONTH'))) || ($_POST['Month']==date('Y-m-01', strtotime('-2 MONTH'))) || ($_POST['Month']==date('Y-m-01', strtotime('-3 MONTH'))) || ($_POST['Month']==date('Y-m-01', strtotime('-4 MONTH'))) || ($_POST['Month']==date('Y-m-01', strtotime('-5 MONTH'))) ) ){
                                    $begin = new DateTime($_POST['Month']);
                                    if($_POST['Month']==date('Y-m-01', strtotime('-1 MONTH'))){
                                        $end = new DateTime(date('Y-m-01'));
                                    } else if($_POST['Month']==date('Y-m-01', strtotime('-2 MONTH'))){
                                        $end = new DateTime(date('Y-m-01', strtotime('-1 MONTH')));
                                    } else if($_POST['Month']==date('Y-m-01', strtotime('-3 MONTH'))){
                                        $end = new DateTime(date('Y-m-01', strtotime('-2 MONTH')));
                                    } else if($_POST['Month']==date('Y-m-01', strtotime('-4 MONTH'))){
                                        $end = new DateTime(date('Y-m-01', strtotime('-3 MONTH')));
                                    } else if($_POST['Month']==date('Y-m-01', strtotime('-5 MONTH'))){
                                        $end = new DateTime(date('Y-m-01', strtotime('-4 MONTH')));
                                    }
                                } else {
                                    $begin = new DateTime(date('Y-m-01'));
                                    $end = new DateTime(date('Y-m-d',strtotime('+15 days')));
                                }

                                $interval = DateInterval::createFromDateString('1 day');
                                $period = new DatePeriod($begin, $interval, $end);
                                $dateCount = 0;

                                foreach ($period as $dt) {
                                ?>
                                <tr>
                                    <td><?php echo $dt->format("d/m/Y, D");?></td>
                                    <?php 
                                        $dateCount++;
                                        $sql1 = "SELECT `tm_id`,
                                                        `tm_task_type`,
                                                        `tm_remark`,
                                                        `tm_remark_type`
                                                    FROM `task_mgr`
                                                    WHERE `tm_user_id`='".$loggedUserId."'
                                                    ORDER BY `tm_task_no`
                                                ";
                                        $result1 = mysqli_query($con, $sql1);
                                        $taskCount = 0;
                                        while($row1 = mysqli_fetch_array($result1))
                                        {
                                            $taskCount++;
                                            if($dateCount==1){
                                                if($row1['tm_task_type']=="T"){
                                                    $task[$taskCount][0] = "00:00";
                                                } else if($row1['tm_task_type']=="N"){
                                                    $task[$taskCount][0] = 0;
                                                } else {
                                                    $task[$taskCount][0] = "";
                                                }
                                                
                                                if($row1['tm_remark_type']=="T"){
                                                    $task[$taskCount][1] = "00:00";
                                                } else if($row1['tm_remark_type']=="N"){
                                                    $task[$taskCount][1] = 0;
                                                } else {
                                                    $task[$taskCount][1] = "";
                                                }
                                            }
                                            
                                            $diffFromPrevDay = 1;
                                            $timeColor = 0;
                                            $sql2 = "SELECT `td_value`
                                                        FROM `task_dtl`
                                                       WHERE `td_task_id`='".$row1['tm_id']."'
                                                         AND `td_type` = 'T'
                                                         AND `td_date` = '".($dt->format("Y-m-d"))."'
                                                    ";
                                            $result2 = mysqli_query($con, $sql2);
                                            if($row2 = mysqli_fetch_array($result2)){
                                                if($taskRowspan[$taskCount]>0){
                                                    $taskRowspan[$taskCount]--;
                                                    $diffFromPrevDay = 0;
                                                } else if($row1['tm_task_type']=="AN"){
                                                    $i=1;
                                                    while(1){
                                                        $sql3 = "SELECT `td_value`
                                                                   FROM `task_dtl`
                                                                  WHERE `td_task_id`='".$row1['tm_id']."'
                                                                    AND `td_value`='".$row2['td_value']."'
                                                                    AND `td_type` = 'T'
                                                                    AND `td_date` = '".date('Y-m-d', strtotime(($dt->format("Y-m-d")). " + ".$i." days"))."'
                                                                ";
                                                        $result3 = mysqli_query($con, $sql3);
                                                        if($row3 = mysqli_fetch_array($result3)){
                                                            $taskRowspan[$taskCount]++;
                                                        } else {
                                                            break;
                                                        }
                                                        $i++;
                                                    }
                                                }
                                                if($row1['tm_task_type']=="T"){
                                                    $hr = 0;
                                                    $min = 0;
                                                    $hr1 = 0;
                                                    $min1 = 0;
                                                    $hr2 = 0;
                                                    $min2 = 0;
                                                    $hr1 = (int)substr($row2['td_value'],0,2);
                                                    $min1 = (int)substr($row2['td_value'],(strpos($row2['td_value'],":",0)+1),2);
                                                    $hr2 = (int)substr($task[$taskCount][0],0,strpos($task[$taskCount][0],":",0));
                                                    $min2 = (int)substr($task[$taskCount][0],(strpos($task[$taskCount][0],":",0)+1),2);
                                                    $min = (($min1 + $min2)%60);
                                                    $hr = $hr1 + $hr2 + floor(($min1 + $min2)/60);
                                                    
                                                    if($min<10){
                                                        $task[$taskCount][0] = $hr.":0".$min;
                                                    } else {
                                                        $task[$taskCount][0] = $hr.":".$min;
                                                    }
                                                    if($hr1>7){
                                                        $timeColor = 1;
                                                    } else if($hr1>3){
                                                        $timeColor = 2;
                                                    } else {
                                                        $timeColor = 3;
                                                    }
                                                    $taskCnt[$taskCount][0]++;
                                                } else if($row1['tm_task_type']=="N"){
                                                    $task[$taskCount][0] = $task[$taskCount][0]+$row2['td_value'];
                                                    $taskCnt[$taskCount][0]++;
                                                }
                                                $remarkColor = 0;
                                                if($row1['tm_remark']=="Y"){
                                                    if($row1['tm_remark_type']=="YN"){
                                                        $sql3 = "SELECT `td_value`
                                                                    FROM `task_dtl`
                                                                WHERE `td_task_id`='".$row1['tm_id']."'
                                                                    AND `td_type` = 'R'
                                                                    AND `td_date` = '".($dt->format("Y-m-d"))."'
                                                                ";
                                                        $result3 = mysqli_query($con, $sql3);
                                                        if($row3 = mysqli_fetch_array($result3)){
                                                            if($row3['td_value']=="Y"){
                                                                $remarkColor = 1;
                                                            } else if($row3['td_value']=="N"){
                                                                $remarkColor = 3;
                                                            }
                                                        }
                                                    }
                                                }
                                                if($diffFromPrevDay == 1){
                                                ?>
                                                <td style="background:<?php 
                                                    if( ($row2['td_value']=="Y") || ($timeColor == 1) || ($remarkColor == 1) ){
                                                        echo "#81d41a";
                                                    } else if( ($row2['td_value']=="N") || ($timeColor == 3) || ($remarkColor == 3) ){
                                                        echo "#f17b1a";
                                                    }else if( ($timeColor == 2) ){
                                                        echo "#fff200";
                                                    }else{
                                                        echo "none";
                                                    }
                                                    ?>;"<?php if($taskRowspan[$taskCount]>0){echo " rowspan='".($taskRowspan[$taskCount]+1)."'";}?>>
                                                    <span class='task-val'><?php if($row2['td_value']==""){echo "NA";}else{echo $row2['td_value'];} ?></span>
                                                </td>
                                                <?php 
                                                }
                                            } else {
                                                ?>
                                                <td>
                                                    <span class='task-val'>NA</span>
                                                </td>
                                                <?php 
                                            }
                                            if(($row1['tm_remark']=="Y") && ($diffFromPrevDay == 1)){
                                                if($row1['tm_task_type']=="YN" && $row2['td_value']!="Y"){
                                                    if($row2['td_value']=="N"){
                                                        echo "<td style='background:#f17b1a;'></td>";
                                                    } else {
                                                        echo "<td></td>";
                                                    }
                                                } else {
                                                    $sql3 = "SELECT `td_value`
                                                                FROM `task_dtl`
                                                            WHERE `td_task_id`='".$row1['tm_id']."'
                                                                AND `td_type` = 'R'
                                                                AND `td_date` = '".($dt->format("Y-m-d"))."'
                                                            ";
                                                    $result3 = mysqli_query($con, $sql3);
                                                    if($row3 = mysqli_fetch_array($result3)){
                                                        $task[$taskCount][1] = $task[$taskCount][1]+$row3['td_value'];
                                                        $taskCnt[$taskCount][1]++;
                                                        ?>
                                                        <td style="background:<?php 
                                                            if($row1['tm_task_type']=="YN" && $row2['td_value']=="Y"){
                                                                echo "#81d41a";
                                                            } else if($row3['td_value']=="Y"){
                                                                echo "#81d41a";
                                                            } else if($row3['td_value']=="N"){
                                                                echo "#f17b1a";
                                                            } else {
                                                                echo "none";
                                                            }
                                                        ?>;"<?php if($taskRowspan[$taskCount]>0){echo " rowspan='".($taskRowspan[$taskCount]+1)."'";}?>>
                                                            <span class='task-val'><?php if($row3['td_value']==""){echo "NA";}else{echo $row3['td_value'];} ?></span>
                                                        </td>
                                                        <?php 
                                                    } else {
                                                        ?>
                                                        <td<?php if($taskRowspan[$taskCount]>0){echo " rowspan='".($taskRowspan[$taskCount]+1)."'";}?>>
                                                            <span class='task-val'>NA</span>
                                                        </td>
                                                        <?php 
                                                    }
                                                }
                                            }
                                        }
                                    ?>
                                </tr>
                                <?php 
                                }
                            ?>
                                <tr>
                                    <td>Avergare:</td>
                                    <?php 
                                        $sql1 = "SELECT `tm_id`,
                                                        `tm_task_type`,
                                                        `tm_remark`,
                                                        `tm_remark_type`
                                                    FROM `task_mgr`
                                                    WHERE `tm_user_id`='".$loggedUserId."'
                                                    ORDER BY `tm_task_no`
                                                ";
                                        $result1 = mysqli_query($con, $sql1);
                                        $taskCount = 0;
                                        while($row1 = mysqli_fetch_array($result1))
                                        {
                                            $taskCount++;
                                            if($row1['tm_task_type']=="T"){
                                                $hr = 0;
                                                $min = 0;
                                                $cnt = 0;
                                                $hr = (int)substr($task[$taskCount][0],0,strpos($task[$taskCount][0],":",0));
                                                $min = (int)substr($task[$taskCount][0],(strpos($task[$taskCount][0],":",0)+1),2);
                                                $cnt = $taskCnt[$taskCount][0];
                                                
                                                if($cnt>0){
                                                    $min = ((($hr*60)+$min)/$cnt);
                                                    $hr = floor($min/60);
                                                    $min = ($min%60);
                                                    if($min<10){
                                                        echo "<td>".$hr.":0".$min."</td>";
                                                    } else {
                                                        echo "<td>".$hr.":".$min."</td>";
                                                    }
                                                } else {
                                                    echo "<td>NA</td>";
                                                }
                                            } else if($row1['tm_task_type']=="N"){
                                                if($taskCnt[$taskCount][0]>0){
                                                    echo "<td>".round(($task[$taskCount][0]/$taskCnt[$taskCount][0]),2)."</td>";
                                                } else {
                                                    echo "<td>NA</td>";
                                                }
                                            } else {
                                                echo "<td></td>";
                                            }
                                            if($row1['tm_remark']=="Y"){
                                                if($row1['tm_remark_type']=="N"){
                                                    if($taskCnt[$taskCount][1]>0){
                                                        echo "<td>".round(($task[$taskCount][1]/$taskCnt[$taskCount][1]),2)."</td>";
                                                    } else {
                                                        echo "<td>NA</td>";
                                                    }
                                                } else {
                                                    echo "<td></td>";
                                                }
                                            }
                                        }
                                    ?>
                                </tr>
                        </tbody>
                    </table>
                    </div>
          		</div>
          </div>
		  
		  <?php 
			include("include/footer.php");
		  ?>

        </div>
		<?php 
			//include("include/sidebar.php");
		?>
    </div>

  <!-- Scripts -->
  <!-- Bootstrap core JavaScript -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/transition.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
