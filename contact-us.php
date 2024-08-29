<?php
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "contact-us.php";
	include("include/connect-database.php");
	include("include/login.php");

	$Err="";
	if(isset($_POST['Name']) && isset($_POST['EmailID']) && isset($_POST['MsgSub']) && isset($_POST['Msg']))
	{
		$Err="Please provide your ";
		if((str_replace(" ","",$_POST['Name'])=="")||($_POST['Name']==null))
		{
			$Err=$Err."name, ";
          	unset($_POST['Name']);
		}
		if((str_replace(" ","",$_POST['EmailID'])=="")||($_POST['EmailID']==null))
		{
			$Err=$Err."email id, ";
          	unset($_POST['EmailID']);
		}
		if((str_replace(" ","",$_POST['MsgSub'])=="")||($_POST['MsgSub']==null))
		{
			$Err=$Err."message subject, ";
          	unset($_POST['MsgSub']);
		}
		if((str_replace(" ","",$_POST['Msg'])=="")||($_POST['Msg']==null))
		{
			$Err=$Err."message, ";
          	unset($_POST['Msg']);
		}
		if($Err!="Please provide your ")
		{
			$Err[(strlen($Err)-2)]=".";
		}
		else if(!(preg_match('/^[_A-z0-9-]+((\.|\+)[_A-z0-9-]+)*@[A-z0-9-]+(\.[A-z0-9-]+)*(\.[A-z]{2,4})$/', $_POST['EmailID'])))
		{
			$Err="Please provide a valid email id.";
		}
		else
		{
			$Err="Message sent successfully.";
		 	$to = "support@instaxam.in";
			$subject = $_POST['MsgSub'];
			$txt = $_POST['Msg'];	
			$headers = "From: ".$_POST['EmailID'];	
			mail($to,$subject,$txt,$headers);
          	$_POST = array();
		}
	}
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Instaxam.In - Contact Us">
    <meta name="author" content="JhaAvinash">
    <meta name="keywords" content="Instaxam, Prepare, exam, Online, tests, free">

    <title>Instaxam.In -  Contact Us</title>
    <link rel="icon" type="image/png" href="assets/images/favicon.ico">

    <link rel="stylesheet" href="assets/css/instaxam-style.css">

  </head>

<body class="is-preload">
    <!-- Wrapper -->
    <div id="wrapper">

      <!-- Main -->
        <div id="main" style="background-image: url(assets/images/Instaxam.In-background.png);">
          <div class="inner">
            <?php 
		include("include/header.php");
	    ?>

              <div style="background-color: rgba(255,255,255,0.9); padding: 50px; margin:0;">
                    <h1>Contact Us:</h1>
					<p style="margin:0px; padding:0;">
						<label style="color:#990000; font-family:Georgia, 'Times New Roman', Times, serif; font-weight:bold; font-size:20px; padding-left:30px;">
						Instaxam.In
                      	</label><br />
						<label style="color:#6a8516; font-family:Georgia, 'Times New Roman', Times, serif; font-weight:bold; font-size:15px; padding-left:60px;">
						Single Place for All Exams.....!!!!
						</label>
					</p>
					<hr style="margin-bottom:10px;" />
					<table align="center">
						<tr>
							<td>
                              <p style="color:#990000; font-weight:bold;">
                                  Our Postal Address:
                              </p>
                              <p style="padding-top:5px; padding-left:30px;">
                                  Instaxam.In,<br />
                                  Asansol - 25,<br />
                                  West Bengal,<br />
                                  India
                              </p>
							</td>
							<td>
                              <p style="color:#990000; font-weight:bold;">
                                  Call Us At:
                              </p>
                              <p style="padding-top:5px;">
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                  +91 8101042280<br />
                              </p>
                              <p style="color:#990000; font-weight:bold;">
                                  Mail Us At:
                              </p>
                              <p style="padding-top:5px;">
                                  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                  support@instaxam.in
                              </p>
							</td>
						</tr>
					</table>
					<form name="contactform"  method="post" action="contact-us.php">
<table align="center" style="font-family:Georgia, 'Times New Roman', Times, serif; font-size:16px;" cellpadding="5" cellspacing="5">
	<tr>
						<td id='sendMsg' style="color:#990000; font-weight:bold; text-align:center;">
							Send Us A Message:
						</td>
	</tr>
	<tr>
		<td align="center">
			<input type="text" name="Name" placeholder="Name" required="" value="<?php if(isset($_POST['Name'])){echo $_POST['Name'];}?>" maxlength="160" style="border:1px solid gray;width:285px;" /><br />
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="text" name="EmailID" placeholder="E-mail" required="" value="<?php if(isset($_POST['EmailID'])){echo $_POST['EmailID'];}?>" maxlength="160" style="border:1px solid gray; width:285px;" /><br />
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="text" name="MsgSub" placeholder="Subject" required="" value="<?php if(isset($_POST['MsgSub'])){echo $_POST['MsgSub'];}?>" maxlength="160" style="border:1px solid gray; width:285px;" /><br />
		</td>
	</tr>
	<tr>
		<td align="center">
			<textarea name="Msg" placeholder="Message" required="" style="border:1px solid gray; max-width:285px; min-width:285px; max-height:70px; min-height:70px;"><?php if(isset($_POST['Msg'])){echo $_POST['Msg'];}?></textarea>
		</td>
	</tr>
	<tr>
		<td align="center" style="color:#FF0000; font-size:14px;">
          <b><?php if($Err!=""){echo $Err;}?></b>
		</td>
	</tr>
	<tr>
		<td align="center">
			<input type="submit" value="SEND MESSAGE" name="send" style="border:#666666 solid 1px; height:25px; width:200px;" />
		</td>
	</tr>
</table>
					</form>


                    </div>

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
<?php 
	if($Err!="")
	{
		?>
		<script type="text/javascript">
			alert("<?php echo $Err?>");
			location.replace("#sendMsg");
		</script>
		<?php
	}
?>
