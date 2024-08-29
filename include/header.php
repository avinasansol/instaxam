            <!-- Header -->
            <header id="header">
              <div class="logo">
                <a href="https://www.instaxam.in/index.php"><img src='https://www.instaxam.in/assets/images/instaxam-logo-small.jpg' alt='Ix' style='width:40px; height:40px; margin-right:5px;' />Instaxam.In</a>
              </div>
            </header>
			<?php
				$loggedUserId = "";
				$loggedUserName = "";
				if(isset($_SESSION['LogdUsrDet']))
				{
					$loggedUserId = $_SESSION['LogdUsrDet'][1];
					$sql1 = "SELECT `user_det`.`ud_first_name`,
									`user_det`.`ud_last_name`
							   FROM `user_det`
							  WHERE `user_det`.`ud_user_id` = '".$loggedUserId."'
							 ";
					$result1=mysqli_query($con, $sql1);
					if($row1=mysqli_fetch_array($result1, MYSQLI_ASSOC))
					{
						$loggedUserName = $row1['ud_first_name']." ".$row1['ud_last_name'];
					}
					if($page!="take-test.php"){
					?>
						<div id="user-area">
							<div id="user-msg">
								<div style="float:right;">
									<form name="UserLogIn" method="post" action="https://www.instaxam.in/log-out.php">
										<input type="hidden" name="CurrPage" value="<?php echo $page; ?>" />
										<button type="submit" id="form-submit" class="button">Log Out</button>
									</form>
								</div>
									<?php if($page!="user-dashboard.php"){?>
								<div style="float:right;">
									<form name="UserLogIn" method="get" action="https://www.instaxam.in/user-dashboard.php" onsubmit="return false">
										<button type="submit" id="form-submit" class="button" onClick="location.replace('https://www.instaxam.in/user-dashboard.php')">Dashboard</button>
									</form>
								</div>
									<?php }?>
								<div style="float:right; padding-top:5px; padding-right:10px;">
									<span><?php echo $loggedUserName; ?>...! </span>
								</div>
							</div>
						</div>
					<?php
					}
				} else  if($page!="index.php") {
				?>
					<div id="user-area">
						<div id="user-msg" style="height:<?php if($LogErr!=""){echo "100";} else {echo "70";}?>px;">
							<div style="height:30px;">
								<form name="UserLogIn" method="post" action="<?php echo $page;?>">
									<input type="hidden" name="Operation" value="UserLogIn" />
									<input type="hidden" name="CurrPage" value="<?php echo $page; ?>" />
									<input type="text" placeholder="Email Id" required="" id="username" name="txtUsrId" value="<?php if(isset($_POST['txtUsrId'])){echo $_POST['txtUsrId'];}?>" />
									<input type="password" placeholder="Password" required="" id="password" name="txtPswd" />
									<button type="submit" id="form-submit" class="button">Log In</button>
								</form>
							</div>
							<?php if($LogErr!=""){ ?>
							<div style="height:20px;">
								<span id='LogErr'><?php echo $LogErr; ?></span>
							</div>
							<?php }?>
							<div style="height:40px;">
								<?php if($page!="register.php") { ?><a href="https://www.instaxam.in/register.php">Sign Up</a><?php } ?>
								<?php if($page!="forgot-password.php") { ?><a id="forgot" href="https://www.instaxam.in/forgot-password.php">Forgot password?</a><?php } ?>
							</div>
						</div>
					</div>
				<?php
				}
			?>
