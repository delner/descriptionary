<?php
	include_once('includes/user.php');
	include_once('includes/dal.php');

	// Check for redirect cases
	$curr_user = new DescriptionaryUser();

	if ($curr_user->isLoggedIn) 
	{
		$desc_user=DescriptionaryUser::GetUser($curr_user->id);

		if ($desc_user instanceof DescriptionaryUser)
		{
			echo '<html>';
			echo '<head>';
			echo '<meta http-equiv="refresh" content="0;url=index.php" />';
			echo '</head>';
			echo'</html>';
			die;
		}
	}
	else
	{	
			echo '<html>';
			echo '<head>';
			echo '<meta http-equiv="refresh" content="0;url=desc_login.php" />';
			echo '</head>';
			echo'</html>';
			die;
	}
	
	// Check for form submission
	if(isset($_POST['submit']))
	{
		// Build a DescriptionaryUserSettings object
		$settings = new DescriptionaryUserSettings(	$_POST['game_invite'],
													$_POST['private_turn'],
													$_POST['game_complete'],
													$_POST['game_created_complete'],
													$_POST['game_pub_commented'],
													$_POST['game_priv_commented'],
													$_POST['game_invite_allow']);
		
		// Add user to Descriptionary
		$desc_user = DescriptionaryUser::AddNewUser($curr_user->id, $settings);
		
		// If successful
		if($desc_user instanceof DescriptionaryUser)
		{
			// Redirect to the Dashboard
			echo '<html>';
			echo '<head>';
			echo '<meta http-equiv="refresh" content="0;url=index.php" />';
			echo '</head>';
			echo'</html>';
			die;
		}
		else
		{
			$error = $desc_user;
		}
	}
?> 



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<title>Descriptionary ONLINE</title>

	<link href="css/default.css" type="text/css" rel="stylesheet" />

	<link href="css/footerstyle.css" type="text/css" rel="stylesheet" />

	<link href="css/profilestyle.css" type="text/css" rel="stylesheet" />

	<script type="text/javascript" src="scripts/default.js"></script>

</head>



<body onload="javascript:external()">

	<div id="art_abcde"></div>

    <div id="art_rhymes"></div>

	

    <div id="main_container">

		<div id="header">

    		<div id="logo"></div>

            <div id="navigation">

                <ul>

                    <li class="nav_dash" onclick="javascript:show_link('index.php')"></li>

                    <li class="nav_profile" onclick="javascript:show_link('desc_myprofile.php')"></li>
                    <li class="nav_logout" onclick="javascript:show_link('desc_logout.php')"></li>
                    <li class="nav_help" onclick="javascript:show_link('desc_help.php')"></li> 

                </ul>

            </div> <!-- End Navigation -->

		</div> <!-- End Header -->

	

		<!-- CONTENT BLOCK  -->

		<div id="content">

			<!-- Page Header -->

			<div id="pageheader"><h1>Initial Descriptionary Profile Setup</h1></div>

			

			<!--Information for the page  -->

			<div id="pageinfo"> <p>Thanks for registering for Descriptionary ONLINE.<br /> 

			Please take the time to adjust your settings below.<br />Usual choices have been set as default.  </p></div>

     

			<!--  Registration block  -->

			<div id="profileblock">

				<p>

					<h2>New  Descriptionary User Profile Setup</h2>

					<form method="post" onsubmit="desc_setupmyprofile.php">
					
					<div id="errorMessage">
						<? echo $error; ?>
					</div>

					<div id="form_container">
						<div class="field">
							<label>Notify me by email when a friend invites me to play a game:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_invite" value="1" />Yes</label>
							<label><input type="radio" name="game_invite" value="0" checked />No</label>
						</div>
		
						<div class="field">
							<label>Notify me by email when it is my turn in a private game:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="private_turn" value="1" checked  />Yes</label>
							<label><input type="radio" name="private_turn" value="0" />No</label>
						</div>
						
						<div class="field">
							<label>Notify me by email when a game I participated in is complete:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_complete" value="1" checked />Yes</label>
							<label><input type="radio" name="game_complete" value="0" />No</label>
						</div>
						
						<div class="field">
							<label>Notify me by email when a game I created in is complete:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_created_complete" value="1" checked />Yes</label>
							<label><input type="radio" name="game_created_complete" value="0" />No</label>
						</div>			
						
						<div class="field">
							<label>Notify me by email when someone comments in a public game I participated in:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_pub_commented" value="1" />Yes</label>
							<label><input type="radio" name="game_pub_commented" value="0" checked />No</label>
						</div>
						
						<div class="field">
							<label>Notify me by email when someone comments in a private game I participated in:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_priv_commented" value="1" />Yes</label>
							<label><input type="radio" name="game_priv_commented" value="0" checked />No</label>
						</div>
						
						<div class="field">
							<label>Allow other users to invite me to games:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_invite_allow" value="1" checked />Yes</label>
							<label><input type="radio" name="game_invite_allow" value="0" />No</label>
						</div>
						<div id="submit">
							<input type="submit" name = 'submit' value ='Submit Selections' class='button'/>
						</div> 

					</div><!-- end form_container -->

					</form>

				</p>

			</div> <!-- end block -->  



    

		</div><!-- end content  -->



		<div id="chalkboardseparator">

	

		</div>

 



 

		<!--  FOOTER  -->

		<div id="footer">

			<?php include "includes/footer.php"; ?>

		</div>

		<!-- end footer -->

    </div><!-- end Main Container -->



</body>

</html>