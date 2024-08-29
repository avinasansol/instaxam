<?php 
	session_start();
	$page = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$page = "account-setting.php";
	if(!isset($_SESSION['LogdUsrDet']))
	{
		header("Location: index.php");
	}
	include("include/connect-database.php");
	$loggedUserId = $_SESSION['LogdUsrDet'][1];
	
	$updateErr = "please provide a valid ";
	if( (isset($_POST['FormName'])) && ($_POST['FormName']=="UpdateDetailsForm") && (isset($_POST['Gender'])) && (isset($_POST['Birthday'])) && (isset($_POST['Country'])) && (isset($_POST['PhNo'])) ) 
	{
		if( ($_POST['Gender']=="M") || ($_POST['Gender']=="F") || ($_POST['Gender']=="O") ) {
			$sql1 = "UPDATE `user_det` SET `ud_gender` = '".$_POST['Gender']."'
					  WHERE `ud_user_id` = '".$loggedUserId."'
					 ";
			if(!mysqli_query($con, $sql1))
			{
				$updateErr = $updateErr."gender, ";
			}
		} else {
			$updateErr = $updateErr."gender, ";
		}
		
		if($_POST['Birthday']=="") {
			$updateErr = $updateErr."date of birth, ";
		} else {
			$birthdate = mysqli_real_escape_string($con, substr(htmlentities($_POST['Birthday']),0,10));
			$sql1 = "UPDATE `user_det` SET `ud_dob` = '".$birthdate."'
					  WHERE `ud_user_id` = '".$loggedUserId."'
					 ";
			if(!mysqli_query($con, $sql1))
			{
				$updateErr = $updateErr."date of birth, ";
			}
		}
		
		if($_POST['Country']=="") {
			$updateErr = $updateErr."country, ";
		} else {
			$country = mysqli_real_escape_string($con, substr(htmlentities($_POST['Country']),0,70));
			$sql1 = "UPDATE `user_det` SET `ud_country` = '".$country."'
					  WHERE `ud_user_id` = '".$loggedUserId."'
					 ";
			if(!mysqli_query($con, $sql1))
			{
				$updateErr = $updateErr."country, ";
			}
		}
		
		if( (!preg_match('/^[0-9]{1,}$/', $_POST['PhNo'])) || (strlen($_POST['PhNo'])<10) )
		{
			$updateErr = $updateErr."phone number, ";
			$_POST['PhNo'] = "";
		} else {
			$phNo = mysqli_real_escape_string($con, substr(htmlentities(str_replace(' ', '', $_POST['PhNo'])),0,15));
			$sql1 = "UPDATE `user_det` SET `ud_contact_no` = '".$phNo."'
					  WHERE `ud_user_id` = '".$loggedUserId."'
					 ";
			if(!mysqli_query($con, $sql1))
			{
				$updateErr = $updateErr."phone number, ";
			}
		}
		
		if($updateErr!="please provide a valid ")
		{
			$updateErr[(strlen($updateErr)-2)]=".";
		} else {
			$updateErr = "your details have been updated successfully.";
		}
	}
	
	$changePassErr = "";
	if( (isset($_POST['FormName'])) && ($_POST['FormName']=="ChangePasswordForm") && (isset($_POST['NewPassword'])) && (isset($_POST['ConfirmPassword'])) ) 
	{
		if($_POST['NewPassword']!=$_POST['ConfirmPassword']){
			$changePassErr = "passwords doesn't match";
		} else {
			$sql1 = "UPDATE `user_det` SET `ud_password` = '".md5($_POST['NewPassword'])."', `ud_new_password` = NULL
					  WHERE `ud_user_id` = '".$loggedUserId."'
					";
			if(!mysqli_query($con, $sql1))
			{
				$changePassErr = "please provide a valid password";
			} else {
				$changePassErr = "your password has been changed successfully.";
			}
		}
	}
	
	$firstName = "";
	$lastName = "";
	$gender = "";
	$dob = "";
	$country = "";
	$contactNo = "";
	$sql1 = "SELECT `ud_user_id`, 
					`ud_first_name`, 
					`ud_last_name`, 
					`ud_gender`, 
					`ud_dob`, 
					`ud_country`, 
					`ud_contact_no` 
			   FROM `user_det` 
			  WHERE `ud_user_id` = '".$loggedUserId."'
			 ";
	$result1=mysqli_query($con, $sql1);
	if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
	{
		$firstName = $row1['ud_first_name'];
		$lastName = $row1['ud_last_name'];
		$gender = $row1['ud_gender'];
		$dob = $row1['ud_dob'];
		$country = $row1['ud_country'];
		$contactNo = $row1['ud_contact_no'];
	}
?>
<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">

    <title>Instaxam.In - Account Setting</title>
	<link rel="icon" type="image/png" href="assets/images/favicon.ico">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/all-style.css">
    <link rel="stylesheet" href="assets/css/owl.css">

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

<style>
input, select {
 border:#000000 solid 1px;
 width:220px;
} 
.UpdtErr{
 color:#FF3300;
 font-size:12px;
}

#meter_wrapper
{
 border:none;
 margin:0px;
 width:220px;
 height:15px;
}
#meter
{
 width:0px;
 height:15px;
}
#pass_type
{
 font-size:12px;
 margin:0px;
 text-align:center;
 color:grey;
}
#conf_pass
{
 font-size:12px;
 margin:0px;
 text-align:center;
 color:grey;
}
</style>
            <section class="tables" style="margin-top:-80px;">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h2>Your Account Details: </h2>
                    </div>
                    <div class="alternate-table">
						<table>
						<form name="UpdateDetailsForm" action="<?php echo $page; ?>" method="post">
							<input type="hidden" name="FormName" value="UpdateDetailsForm" />
							<tbody>
								<tr>
									<td width="30%">Email Id: </td>
									<td width="70%"><?php echo $loggedUserId; ?></td>
								</tr>
								<tr>
									<td>First Name: </td>
									<td><?php echo $firstName; ?></td>
								</tr>
								<tr>
									<td>Last Name: </td>
									<td><?php echo $lastName; ?></td>
								</tr>
								<tr>
									<td>Gender: </td>
									<td>
									<select name="Gender" id="gender">
										<?php if($gender=="F"){?>
											<option value="F">Female</option>
											<option value="M">Male</option>
											<option value="O">Other</option>
										<?php } else if($gender=="O"){?>
											<option value="O">Other</option>
											<option value="M">Male</option>
											<option value="F">Female</option>
										<?php } else if($gender=="M"){?>
											<option value="M">Male</option>
											<option value="F">Female</option>
											<option value="O">Other</option>
										<?php } else {?>
											<option value="">Select Your Gender</option>
											<option value="M">Male</option>
											<option value="F">Female</option>
											<option value="O">Other</option>
										<?php }?>
									</select>
									</td>
								</tr>
								<tr>
									<td>Date of Birth: </td>
									<td>
									<input type="date" id="birthday" name="Birthday" value="<?php echo $dob; ?>">
									</td>
								</tr>
								<tr>
									<td>Country: </td>
									<td>
<select id="country" name="Country">
   <?php if($country!=""){ ?><option value="<?php echo $country; ?>"><?php echo $country; ?></option><?php } else { ?><option value="">Select Your Country</option><?php } ?>
   <?php if($country!="India"){ ?><option value="India">India</option><?php } ?>
   <option value="Afganistan">Afghanistan</option>
   <option value="Albania">Albania</option>
   <option value="Algeria">Algeria</option>
   <option value="American Samoa">American Samoa</option>
   <option value="Andorra">Andorra</option>
   <option value="Angola">Angola</option>
   <option value="Anguilla">Anguilla</option>
   <option value="Antigua & Barbuda">Antigua & Barbuda</option>
   <option value="Argentina">Argentina</option>
   <option value="Armenia">Armenia</option>
   <option value="Aruba">Aruba</option>
   <option value="Australia">Australia</option>
   <option value="Austria">Austria</option>
   <option value="Azerbaijan">Azerbaijan</option>
   <option value="Bahamas">Bahamas</option>
   <option value="Bahrain">Bahrain</option>
   <option value="Bangladesh">Bangladesh</option>
   <option value="Barbados">Barbados</option>
   <option value="Belarus">Belarus</option>
   <option value="Belgium">Belgium</option>
   <option value="Belize">Belize</option>
   <option value="Benin">Benin</option>
   <option value="Bermuda">Bermuda</option>
   <option value="Bhutan">Bhutan</option>
   <option value="Bolivia">Bolivia</option>
   <option value="Bonaire">Bonaire</option>
   <option value="Bosnia & Herzegovina">Bosnia & Herzegovina</option>
   <option value="Botswana">Botswana</option>
   <option value="Brazil">Brazil</option>
   <option value="British Indian Ocean Ter">British Indian Ocean Ter</option>
   <option value="Brunei">Brunei</option>
   <option value="Bulgaria">Bulgaria</option>
   <option value="Burkina Faso">Burkina Faso</option>
   <option value="Burundi">Burundi</option>
   <option value="Cambodia">Cambodia</option>
   <option value="Cameroon">Cameroon</option>
   <option value="Canada">Canada</option>
   <option value="Canary Islands">Canary Islands</option>
   <option value="Cape Verde">Cape Verde</option>
   <option value="Cayman Islands">Cayman Islands</option>
   <option value="Central African Republic">Central African Republic</option>
   <option value="Chad">Chad</option>
   <option value="Channel Islands">Channel Islands</option>
   <option value="Chile">Chile</option>
   <option value="China">China</option>
   <option value="Christmas Island">Christmas Island</option>
   <option value="Cocos Island">Cocos Island</option>
   <option value="Colombia">Colombia</option>
   <option value="Comoros">Comoros</option>
   <option value="Congo">Congo</option>
   <option value="Cook Islands">Cook Islands</option>
   <option value="Costa Rica">Costa Rica</option>
   <option value="Cote DIvoire">Cote DIvoire</option>
   <option value="Croatia">Croatia</option>
   <option value="Cuba">Cuba</option>
   <option value="Curaco">Curacao</option>
   <option value="Cyprus">Cyprus</option>
   <option value="Czech Republic">Czech Republic</option>
   <option value="Denmark">Denmark</option>
   <option value="Djibouti">Djibouti</option>
   <option value="Dominica">Dominica</option>
   <option value="Dominican Republic">Dominican Republic</option>
   <option value="East Timor">East Timor</option>
   <option value="Ecuador">Ecuador</option>
   <option value="Egypt">Egypt</option>
   <option value="El Salvador">El Salvador</option>
   <option value="Equatorial Guinea">Equatorial Guinea</option>
   <option value="Eritrea">Eritrea</option>
   <option value="Estonia">Estonia</option>
   <option value="Ethiopia">Ethiopia</option>
   <option value="Falkland Islands">Falkland Islands</option>
   <option value="Faroe Islands">Faroe Islands</option>
   <option value="Fiji">Fiji</option>
   <option value="Finland">Finland</option>
   <option value="France">France</option>
   <option value="French Guiana">French Guiana</option>
   <option value="French Polynesia">French Polynesia</option>
   <option value="French Southern Ter">French Southern Ter</option>
   <option value="Gabon">Gabon</option>
   <option value="Gambia">Gambia</option>
   <option value="Georgia">Georgia</option>
   <option value="Germany">Germany</option>
   <option value="Ghana">Ghana</option>
   <option value="Gibraltar">Gibraltar</option>
   <option value="Great Britain">Great Britain</option>
   <option value="Greece">Greece</option>
   <option value="Greenland">Greenland</option>
   <option value="Grenada">Grenada</option>
   <option value="Guadeloupe">Guadeloupe</option>
   <option value="Guam">Guam</option>
   <option value="Guatemala">Guatemala</option>
   <option value="Guinea">Guinea</option>
   <option value="Guyana">Guyana</option>
   <option value="Haiti">Haiti</option>
   <option value="Hawaii">Hawaii</option>
   <option value="Honduras">Honduras</option>
   <option value="Hong Kong">Hong Kong</option>
   <option value="Hungary">Hungary</option>
   <option value="Iceland">Iceland</option>
   <option value="Indonesia">Indonesia</option>
   <option value="Iran">Iran</option>
   <option value="Iraq">Iraq</option>
   <option value="Ireland">Ireland</option>
   <option value="Isle of Man">Isle of Man</option>
   <option value="Israel">Israel</option>
   <option value="Italy">Italy</option>
   <option value="Jamaica">Jamaica</option>
   <option value="Japan">Japan</option>
   <option value="Jordan">Jordan</option>
   <option value="Kazakhstan">Kazakhstan</option>
   <option value="Kenya">Kenya</option>
   <option value="Kiribati">Kiribati</option>
   <option value="Korea North">Korea North</option>
   <option value="Korea Sout">Korea South</option>
   <option value="Kuwait">Kuwait</option>
   <option value="Kyrgyzstan">Kyrgyzstan</option>
   <option value="Laos">Laos</option>
   <option value="Latvia">Latvia</option>
   <option value="Lebanon">Lebanon</option>
   <option value="Lesotho">Lesotho</option>
   <option value="Liberia">Liberia</option>
   <option value="Libya">Libya</option>
   <option value="Liechtenstein">Liechtenstein</option>
   <option value="Lithuania">Lithuania</option>
   <option value="Luxembourg">Luxembourg</option>
   <option value="Macau">Macau</option>
   <option value="Macedonia">Macedonia</option>
   <option value="Madagascar">Madagascar</option>
   <option value="Malaysia">Malaysia</option>
   <option value="Malawi">Malawi</option>
   <option value="Maldives">Maldives</option>
   <option value="Mali">Mali</option>
   <option value="Malta">Malta</option>
   <option value="Marshall Islands">Marshall Islands</option>
   <option value="Martinique">Martinique</option>
   <option value="Mauritania">Mauritania</option>
   <option value="Mauritius">Mauritius</option>
   <option value="Mayotte">Mayotte</option>
   <option value="Mexico">Mexico</option>
   <option value="Midway Islands">Midway Islands</option>
   <option value="Moldova">Moldova</option>
   <option value="Monaco">Monaco</option>
   <option value="Mongolia">Mongolia</option>
   <option value="Montserrat">Montserrat</option>
   <option value="Morocco">Morocco</option>
   <option value="Mozambique">Mozambique</option>
   <option value="Myanmar">Myanmar</option>
   <option value="Nambia">Nambia</option>
   <option value="Nauru">Nauru</option>
   <option value="Nepal">Nepal</option>
   <option value="Netherland Antilles">Netherland Antilles</option>
   <option value="Netherlands">Netherlands (Holland)</option>
   <option value="Nevis">Nevis</option>
   <option value="New Caledonia">New Caledonia</option>
   <option value="New Zealand">New Zealand</option>
   <option value="Nicaragua">Nicaragua</option>
   <option value="Niger">Niger</option>
   <option value="Nigeria">Nigeria</option>
   <option value="Niue">Niue</option>
   <option value="Norfolk Island">Norfolk Island</option>
   <option value="Norway">Norway</option>
   <option value="Oman">Oman</option>
   <option value="Pakistan">Pakistan</option>
   <option value="Palau Island">Palau Island</option>
   <option value="Palestine">Palestine</option>
   <option value="Panama">Panama</option>
   <option value="Papua New Guinea">Papua New Guinea</option>
   <option value="Paraguay">Paraguay</option>
   <option value="Peru">Peru</option>
   <option value="Phillipines">Philippines</option>
   <option value="Pitcairn Island">Pitcairn Island</option>
   <option value="Poland">Poland</option>
   <option value="Portugal">Portugal</option>
   <option value="Puerto Rico">Puerto Rico</option>
   <option value="Qatar">Qatar</option>
   <option value="Republic of Montenegro">Republic of Montenegro</option>
   <option value="Republic of Serbia">Republic of Serbia</option>
   <option value="Reunion">Reunion</option>
   <option value="Romania">Romania</option>
   <option value="Russia">Russia</option>
   <option value="Rwanda">Rwanda</option>
   <option value="St Barthelemy">St Barthelemy</option>
   <option value="St Eustatius">St Eustatius</option>
   <option value="St Helena">St Helena</option>
   <option value="St Kitts-Nevis">St Kitts-Nevis</option>
   <option value="St Lucia">St Lucia</option>
   <option value="St Maarten">St Maarten</option>
   <option value="St Pierre & Miquelon">St Pierre & Miquelon</option>
   <option value="St Vincent & Grenadines">St Vincent & Grenadines</option>
   <option value="Saipan">Saipan</option>
   <option value="Samoa">Samoa</option>
   <option value="Samoa American">Samoa American</option>
   <option value="San Marino">San Marino</option>
   <option value="Sao Tome & Principe">Sao Tome & Principe</option>
   <option value="Saudi Arabia">Saudi Arabia</option>
   <option value="Senegal">Senegal</option>
   <option value="Seychelles">Seychelles</option>
   <option value="Sierra Leone">Sierra Leone</option>
   <option value="Singapore">Singapore</option>
   <option value="Slovakia">Slovakia</option>
   <option value="Slovenia">Slovenia</option>
   <option value="Solomon Islands">Solomon Islands</option>
   <option value="Somalia">Somalia</option>
   <option value="South Africa">South Africa</option>
   <option value="Spain">Spain</option>
   <option value="Sri Lanka">Sri Lanka</option>
   <option value="Sudan">Sudan</option>
   <option value="Suriname">Suriname</option>
   <option value="Swaziland">Swaziland</option>
   <option value="Sweden">Sweden</option>
   <option value="Switzerland">Switzerland</option>
   <option value="Syria">Syria</option>
   <option value="Tahiti">Tahiti</option>
   <option value="Taiwan">Taiwan</option>
   <option value="Tajikistan">Tajikistan</option>
   <option value="Tanzania">Tanzania</option>
   <option value="Thailand">Thailand</option>
   <option value="Togo">Togo</option>
   <option value="Tokelau">Tokelau</option>
   <option value="Tonga">Tonga</option>
   <option value="Trinidad & Tobago">Trinidad & Tobago</option>
   <option value="Tunisia">Tunisia</option>
   <option value="Turkey">Turkey</option>
   <option value="Turkmenistan">Turkmenistan</option>
   <option value="Turks & Caicos Is">Turks & Caicos Is</option>
   <option value="Tuvalu">Tuvalu</option>
   <option value="Uganda">Uganda</option>
   <option value="United Kingdom">United Kingdom</option>
   <option value="Ukraine">Ukraine</option>
   <option value="United Arab Erimates">United Arab Emirates</option>
   <option value="United States of America">United States of America</option>
   <option value="Uraguay">Uruguay</option>
   <option value="Uzbekistan">Uzbekistan</option>
   <option value="Vanuatu">Vanuatu</option>
   <option value="Vatican City State">Vatican City State</option>
   <option value="Venezuela">Venezuela</option>
   <option value="Vietnam">Vietnam</option>
   <option value="Virgin Islands (Brit)">Virgin Islands (Brit)</option>
   <option value="Virgin Islands (USA)">Virgin Islands (USA)</option>
   <option value="Wake Island">Wake Island</option>
   <option value="Wallis & Futana Is">Wallis & Futana Is</option>
   <option value="Yemen">Yemen</option>
   <option value="Zaire">Zaire</option>
   <option value="Zambia">Zambia</option>
   <option value="Zimbabwe">Zimbabwe</option>
</select>
									</td>
								</tr>
								<tr>
									<td>Contact No: </td>
									<td><input type="text" placeholder="Phone Number" required="" id="phn" name="PhNo" value="<?php echo $contactNo; ?>" /></td>
								</tr>
								<?php if($updateErr!="please provide a valid ") { echo "<tr><td colspan='2' align='center' class='UpdtErr'>".$updateErr."</td></tr>"; } ?>
								<tr>
									<td colspan="2" align="center">
										<button type="submit"  id="Update-Details" name="UpdateDetails" value="Update Details">Update Details</button>
									</td>
								</tr>
							</tbody>
						</form>
						</table>
				    </div>
                  </div>
                </div>
              </div>
            </section>

            <section class="tables">
              <div class="container-fluid">
                <div class="row">
                  <div class="col-md-12">
                    <div class="section-heading">
                      <h2>Change Your Password: </h2>
                    </div>
                    <div class="alternate-table">
						<table>
						<form name="ChangePasswordForm" action="<?php echo $page; ?>" method="post" onSubmit="return subPassForm()">
							<input type="hidden" name="FormName" value="ChangePasswordForm" />
							<tr>
								<td width="30%" align="right">
								<label for="New-Password">New Password: </label>
                    			</td>
								<td width="70%" align="left">
								<input type="password" required="" id="New-Password" name="NewPassword" value="" />
								<span id="pass_type"></span>
								<div id="meter_wrapper">
								 <div id="meter"></div>
								</div>
                    			</td>
                    		</tr>
							<tr>
								<td width="30%" align="right">
								<label for="New-Password">Confirm Password: </label>
                    			</td>
								<td width="70%" align="left">
								<input type="password" required="" id="Confirm-Password" name="ConfirmPassword" value="" />
								<span id="conf_pass"></span>
                    			</td>
                    		</tr>
							<?php if($changePassErr != "") { echo "<tr><td colspan='2' align='center' class='UpdtErr'>".$changePassErr."</td></tr>"; } ?>
							<tr>
								<td colspan="2" align="center">
								<button type="submit"  id="Change-Password" name="ChangePassword" value="Change Password">Change Password</button>
								</td>
                    		</tr>
						</form>
						</table>
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
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/transition.js"></script>
    <script src="assets/js/owl-carousel.js"></script>
    <script src="assets/js/custom.js"></script>
	<?php if($updateErr!="please provide a valid ") { 
	?>
		<script>
		$('document').ready(function(){
			alert("<?php echo $updateErr; ?>");
			location.replace("#gender");
		});
		</script>
	<?php 
	} ?>
	<?php if($changePassErr !="") { 
	?>
		<script>
		$('document').ready(function(){
			alert("<?php echo $changePassErr ; ?>");
			location.replace("#New-Password");
		});
		</script>
	<?php 
	} ?>
	
<script type="text/javascript">
$(document).ready(function(){
 $("#New-Password").keyup(function(){
  check_pass();
 });
 $("#Confirm-Password").keyup(function(){
  checkConfPass();
 });
});

function subPassForm()
{
 var newPass=document.getElementById("New-Password").value;
 var confPass=document.getElementById("Confirm-Password").value;
 if(newPass!=confPass){
 	alert("passwords doesn't match");
	return false;
 } else {
 	var strength = check_pass();
	if(strength<3)
	{
		alert("passwords is weak");
		return false;
	}
 }
}

function checkConfPass()
{
 var newPass=document.getElementById("New-Password").value;
 var confPass=document.getElementById("Confirm-Password").value;
 if(newPass!=confPass){
   document.getElementById("conf_pass").innerHTML="doesn't match with the password";
 } else {
   document.getElementById("conf_pass").innerHTML="";
 }
}

function check_pass()
{
 var val=document.getElementById("New-Password").value;
 var meter=document.getElementById("meter");
 var no=0;
 if(val!="")
 {
  // If the password length is less than or equal to 6
  if(val.length<=6)no=1;

  // If the password length is greater than 6 and contain any lowercase alphabet or any number or any special character
  if(val.length>6 && (val.match(/[a-z]/) || val.match(/\d+/) || val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)))no=2;

  // If the password length is greater than 6 and contain alphabet,number,special character respectively
  if(val.length>6 && ((val.match(/[a-z]/) && val.match(/\d+/)) || (val.match(/\d+/) && val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) || (val.match(/[a-z]/) && val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))))no=3;

  // If the password length is greater than 6 and must contain alphabets,numbers and special characters
  if(val.length>6 && val.match(/[a-z]/) && val.match(/\d+/) && val.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/))no=4;

  if(no==1)
  {
   $("#meter").animate({width:'70px'},300);
   meter.style.backgroundColor="red";
   document.getElementById("pass_type").innerHTML="Very Weak";
  }

  if(no==2)
  {
   $("#meter").animate({width:'120px'},300);
   meter.style.backgroundColor="#F5BCA9";
   document.getElementById("pass_type").innerHTML="Weak";
  }

  if(no==3)
  {
   $("#meter").animate({width:'170px'},300);
   meter.style.backgroundColor="#FF8000";
   document.getElementById("pass_type").innerHTML="Good";
  }

  if(no==4)
  {
   $("#meter").animate({width:'220px'},300);
   meter.style.backgroundColor="#00FF40";
   document.getElementById("pass_type").innerHTML="Strong";
  }
 }

 else
 {
  meter.style.backgroundColor="white";
  document.getElementById("pass_type").innerHTML="";
 }
 return no;
}
</script>

</body>
</html>