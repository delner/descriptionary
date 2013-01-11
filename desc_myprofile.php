<?php
include_once('includes/checklogged.php');
	
	$currentUser = new DescriptionaryUser();
	
	// Check for form submission
	if(isset($_POST['submit']))
	{
		// Change user's settings
		$currentUser->Settings->NotifyOnFriendInvite = $_POST['game_invite'];
		$currentUser->Settings->NotifyOnTurnPrivateGame = $_POST['private_turn'];
		$currentUser->Settings->NotifyOnGameParticipatedComplete = $_POST['game_complete'];
		$currentUser->Settings->NotifyOnGameCreatedComplete = $_POST['game_created_complete'];
		$currentUser->Settings->NotifyOnCommentInPublicGame = $_POST['game_pub_commented'];
		$currentUser->Settings->NotifyOnCommentInPrivateGame = $_POST['game_priv_commented'];
		$currentUser->Settings->AcceptsInvitations = $_POST['game_invite_allow'];
		$result = $currentUser->SaveUserSettings();
		
		// If successful
		if(strpos($result, "ERROR") === false)
		{
			$messageColor = "green";
			$message = "Settings successfully saved.";
		}
		else
		{
			$messageColor = "red";
			$message = $result;
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
                    <li class="nav_logout" onclick="javascript:show_link('desc_logout.php')"></li>
                    <li class="nav_help" onclick="javascript:show_link('help.php')"></li> 
                </ul>
            </div> <!-- End Navigation -->
		</div> <!-- End Header -->
	
		<!-- CONTENT BLOCK  -->
		<div id="content">
			<!-- Page Header -->
			<div id="pageheader"><h1>Email Preferences</h1></div>
			
			<!--Information for the page  -->
			<div id="pageinfo"> <p>Select your email preferences and other settings for playing Descriptionary ONLINE. </p></div>
     
			<!--  My Profile Form block  -->
			<div id="profileblock">
				
					<h2>My Profile</h2>
										
					<div id="message" style="<? echo 'color: ' . $messageColor; ?> ">
						<? echo $message; ?>
					</div>
					<form method="post" onsubmit="desc_myprofile.php">
					<div id="form_container">
						<div class="field">
							<label>Notify me by email when a friend invites me to play a game:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_invite" value="1" <? if($currentUser->Settings->NotifyOnFriendInvite == "1"){ echo "checked"; } ?> />Yes</label>
							<label><input type="radio" name="game_invite" value="0" <? if($currentUser->Settings->NotifyOnFriendInvite == "0"){ echo "checked"; } ?> />No</label>
						</div>
		
						<div class="field">
							<label>Notify me by email when it is my turn in a private game:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="private_turn" value="1" <? if($currentUser->Settings->NotifyOnTurnPrivateGame == "1"){ echo "checked"; } ?> />Yes</label>
							<label><input type="radio" name="private_turn" value="0" <? if($currentUser->Settings->NotifyOnTurnPrivateGame == "0"){ echo "checked"; } ?> />No</label>
						</div>
						
						<div class="field">
							<label>Notify me by email when a game I participated in is complete:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_complete" value="1" <? if($currentUser->Settings->NotifyOnGameParticipatedComplete == "1"){ echo "checked"; } ?> />Yes</label>
							<label><input type="radio" name="game_complete" value="0" <? if($currentUser->Settings->NotifyOnGameParticipatedComplete == "0"){ echo "checked"; } ?> />No</label>
						</div>
						
						<div class="field">
							<label>Notify me by email when a game I created in is complete:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_created_complete" value="1" <? if($currentUser->Settings->NotifyOnGameCreatedComplete == "1"){ echo "checked"; } ?> />Yes</label>
							<label><input type="radio" name="game_created_complete" value="0" <? if($currentUser->Settings->NotifyOnGameCreatedComplete == "0"){ echo "checked"; } ?> />No</label>
						</div>			
						
						<div class="field">
							<label>Notify me by email when someone comments in a public game I participated in:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_pub_commented" value="1" <? if($currentUser->Settings->NotifyOnCommentInPublicGame == "1"){ echo "checked"; } ?> />Yes</label>
							<label><input type="radio" name="game_pub_commented" value="0" <? if($currentUser->Settings->NotifyOnCommentInPublicGame == "0"){ echo "checked"; } ?> />No</label>
						</div>
						
						<div class="field">
							<label>Notify me by email when someone comments in a private game I participated in:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_priv_commented" value="1" <? if($currentUser->Settings->NotifyOnCommentInPrivateGame == "1"){ echo "checked"; } ?> />Yes</label>
							<label><input type="radio" name="game_priv_commented" value="0" <? if($currentUser->Settings->NotifyOnCommentInPrivateGame == "0"){ echo "checked"; } ?> />No</label>
						</div>
						
						<div class="field">
							<label>Allow other users to invite me to games:</label>
						</div>
						<div class="fieldinput">
							<label><input type="radio" name="game_invite_allow" value="1" <? if($currentUser->Settings->AcceptsInvitations == "1"){ echo "checked"; } ?> />Yes</label>
							<label><input type="radio" name="game_invite_allow" value="0" <? if($currentUser->Settings->AcceptsInvitations == "0"){ echo "checked"; } ?> />No</label>
						</div>
						<div id="submit">
							<input type="submit" name = 'submit' value ='Submit Selections' class='button'/>
						</div> 

					</div><!-- end form_container -->
					</form>
			</div> <!-- end profileblock -->  

	  
    
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