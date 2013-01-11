<?
	include_once('includes/checklogged.php');
	
	$postOperationAttempted = false;
	$failureOccured = false;
	
	$currentUser = new DescriptionaryUser();
	if(!($currentUser instanceof DescriptionaryUser))
	{
		$failureOccured = true;
		$errorMessage = "Failed to get the current user.";
		$result = $currentUser;
	}
	
	// Clean post input if present
	if(isset($_POST))
	{
		$elements = new FormElements($_POST);
		$POST = $elements->getElements();
	}

	// Post: Create private game
	if((isset($POST['createprivate'])) && (!$failureOccured))
	{
		$postOperationAttempted = true;
	
		// Get invitees
		// Parse & lookup e-mail addresses
		// Split by any white space, ',' or ';' characters
		$emails = preg_split("/[\s,;]+/", $POST['invitees'], -1, PREG_SPLIT_NO_EMPTY);
		
		if(count($emails) < ($POST['max_players'] - 1))
		{
			$failureOccured = true;
			if(isset($errorMessage))
			{
				$errorMessage = $errorMessage . "<br />";
			}
			$errorMessage = $errorMessage . "# of invitees must be at least the number of maximum players.";
		}
		else
		{
			$invitees = array();
			foreach($emails as $email)
			{
				$lookupResult = DescriptionaryUser::GetUserByEmail($email);
				if(($email != $currentUser->email) && ($lookupResult instanceof DescriptionaryUser))
				{
					$invitees[] = $lookupResult;
				}
				else
				{
					$failureOccured = true;
					if(isset($errorMessage))
					{
						$errorMessage = $errorMessage . "<br />";
					}
					$errorMessage = $errorMessage . "Invalid e-mail or user: " . $email;
				}
			}
		}
		
		// Get inital phrase
		// Check length after whitespace strip
		if(strlen(trim($POST['initphrase'])) > 0)
		{
			$initphrase = trim($POST['initphrase']);
		}
		else
		{
			$failureOccured = true;
			if(isset($errorMessage))
			{
				$errorMessage = $errorMessage . "<br />";
			}
			$errorMessage = $errorMessage . "Invalid phrase; must not be blank.";
		}
		
		if(!$failureOccured)
		{
			// Create game settings
			$gameSettings = new DescriptionaryGameSettings(1, $POST['max_players']);
		
			// Create game
			$newGame = DescriptionaryGame::CreateGame($gameSettings, $currentUser->id);
			if(!($newGame instanceof DescriptionaryGame))
			{
				$failureOccured = true;
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "Failed to create game.";
				if(!isset($result))
				{
					$result = $newGame;
				}
			}
		}
		if(!$failureOccured)
		{
			// Take turn
			$firstTurn = $newGame->TakeTurn('PHRASE', $initphrase, $currentUser->id);
			if(!($firstTurn instanceof DescriptionaryGameTurn))
			{
				$failureOccured = true;
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "Failed to add first game turn.";
				if(!isset($result))
				{
					$result = $firstTurn;
				}
			}
		}
		if(!$failureOccured)
		{
			// Extend invites (and send e-mails)
			foreach($invitees as $invitee)
			{
				$authCode = uniqid();
				$invite = $invitee->InviteToGame($newGame->GameID, $currentUser->id, $authCode);
				if(!($invite instanceof DescriptionaryGameInvite))
				{
					$failureOccured = true;
					if(isset($errorMessage))
					{
						$errorMessage = $errorMessage . "<br />";
					}
					$errorMessage = $errorMessage . "Failed to invite " . $invitee->email . ".";
					if(!isset($result))
					{
						$result = $invite;
					}
				}
				
				// TODO: Send emails here
			}
		}
		if(!$failureOccured)
		{
			// Display success message, redirect
			$showCreateGameBlock = 'style="display: none;"';
			$resultMessage = "Game successfully created!<br/>";
			$redirect = '<meta http-equiv="refresh" content="3;url=index.php">';
		}
		else
		{
			// Fill in the private game field again with values
			// Display it
			$createPrivateGameButtonAttributes = "checked";
			$createPrivateGameLayerAttributes = 'style="display: block;"';
			$createPrivateGameLayerInitPhrase = $POST['initphrase'];
			if($POST['max_players'] == 3)
			{
				$createPrivateGameLayerThreePlayers = "selected";
			}
			if($POST['max_players'] == 5)
			{
				$createPrivateGameLayerFivePlayers = "selected";
			}
			if($POST['max_players'] == 7)
			{
				$createPrivateGameLayerSevenPlayers = "selected";
			}
			if($POST['max_players'] == 9)
			{
				$createPrivateGameLayerNinePlayers = "selected";
			}
			$createPrivateGameLayerInvitees = $POST['invitees'];
		}
	}
	
	// Post: Create public game
	if(isset($POST['createpublic']) && (!$failureOccured))
	{
		$postOperationAttempted = true;
	
		// Get inital phrase
		// Check length after whitespace strip
		if(strlen(trim($POST['initphrase'])) > 0)
		{
			$initphrase = trim($POST['initphrase']);
		}
		else
		{
			$failureOccured = true;
			if(isset($errorMessage))
			{
				$errorMessage = $errorMessage . "<br />";
			}
			$errorMessage = $errorMessage . "Invalid phrase; must not be blank.";
		}
		
		if(!$failureOccured)
		{
			// Create game settings
			$gameSettings = new DescriptionaryGameSettings(0, 7);
		
			// Create game
			$newGame = DescriptionaryGame::CreateGame($gameSettings, $currentUser->id);
			if(!($newGame instanceof DescriptionaryGame))
			{
				$failureOccured = true;
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "Failed to create game.";
				if(!isset($result))
				{
					$result = $newGame;
				}
			}
		}
		if(!$failureOccured)
		{
			// Take turn
			$firstTurn = $newGame->TakeTurn('PHRASE', $initphrase, $currentUser->id);
			if(!($firstTurn instanceof DescriptionaryGameTurn))
			{
				$failureOccured = true;
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "Failed to add first game turn.";
				if(!isset($result))
				{
					$result = $firstTurn;
				}
			}
		}
		if(!$failureOccured)
		{
			// Display success message, redirect
			$showCreateGameBlock = 'style="display: none;"';
			$resultMessage = "Game successfully created!<br/>";
			$redirect = '<meta http-equiv="refresh" content="3;url=index.php">';
		}
		else
		{
			// Fill in the private game field again with values
			// Display it
			$createPublicGameButtonAttributes = "checked";
			$createPublicGameLayerAttributes = 'style="display: block;"';
			$createPublicGameLayerInitPhrase = $POST['initphrase'];
		}
	}
	
	// Post: Join public game
	if(isset($POST['joinpublic']) && (!$failureOccured))
	{
		$postOperationAttempted = true;
		
		// Find preferred public game
		$preferredGames = $currentUser->GetPreferredGames();
		if(is_array($preferredGames))
		{
			if(count($preferredGames) >= 1)
			{
				$preferredGame = $preferredGames[0];
			}
			else
			{
				$failureOccured = true;
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "There are no available public games to join; create a new one, or please try again later.";
				if(!isset($result))
				{
					$result = $preferredGames;
				}
			}
		}
		else
		{
			$failureOccured = true;
			if(isset($errorMessage))
			{
				$errorMessage = $errorMessage . "<br />";
			}
			$errorMessage = $errorMessage . "Could not retrieve list of preferred public games to join.";
			if(!isset($result))
			{
				$result = $preferredGames;
			}
		}
		
		if(!$failureOccured)
		{
			// Join Game
			$response = $preferredGame->AddParticipant($currentUser->id);
			if(!(strpos($response, "SUCCESS") === false))
			{
				$showCreateGameBlock = 'style="display: none;"';
				$resultMessage = "Successfully joined random public game!<br/>";
				$redirect = '<meta http-equiv="refresh" content="3;url=index.php">';
			}
			else
			{
				$failureOccured = true;
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "Failed to join random public game.";
				if(!isset($result))
				{
					$result = $response;
				}
			}
		}
	}
	
	// Check 'create public game eligibility'
	$availableGames = DescriptionaryGame::GetActiveOpenPublicGames();
	if(is_array($availableGames))
	{
		$maxNumOfActiveGames = "10";
		if(count($availableGames) == 0)
		{
			$joinPublicGameButtonAttributes = "disabled";
		}
		if(count($availableGames) >= $maxNumOfActiveGames)
		{
			$createPublicGameButtonAttributes = "disabled";
		}
	}
	else
	{
		$failureOccured = true;
		if(isset($errorMessage))
		{
			$errorMessage = $errorMessage . "<br />";
		}
		$errorMessage = $errorMessage . "Failed to check create game availability.";
		if(!isset($result))
		{
			$result = $games;
		}
		
		// Disable game buttons
		$createPrivateGameButtonAttributes = "disabled";
		$createPublicGameButtonAttributes = "disabled";
		$joinPublicGameButtonAttributes = "disabled";
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<? echo $redirect; ?>
		<link href="css/default.css" type="text/css" rel="stylesheet" />
		<link href="css/footerstyle.css" type="text/css" rel="stylesheet" />
		<link href="css/newgamestyle.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript" src="scripts/default.js"></script>
		<title>Descriptionary ONLINE</title>
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
				<div id="pageheader"><h1>Create a New Game</h1></div>
				
				<!--Information for the page  -->
				<div id="pageinfo" <? echo $showCreateGameBlock; ?> >
				<p>
					Create a new game on this page.  Choose been creating a public game, private game, or joining a public game.<br />
				 </p>
				 </div>
				 
				<div id="resultMessage" style="color: green;">
					<? echo $resultMessage; ?>
				</div>
				<div id="errorMessage" style="color: red;">
					<? echo $errorMessage; ?>
				</div>
		 
				<!--    Create Game block  -->
				<div id="create_game_block" <? echo $showCreateGameBlock; ?> >
					<div id="game_type_choice_block">
						<div class="field">
							<label><h2>Would you like to create a PUBLIC or PRIVATE game?</h2></label>
						</div>
						
						<div id="game_type_choices">
							<div class="fieldinput">
								<label><input type="radio" name="gametypechoice" value="createprivate" onchange="show_private()" <? echo $createPrivateGameButtonAttributes; ?> />Create Private</label>
								<label><input type="radio" name="gametypechoice" value="createpublic" onchange="show_public()" <? echo $createPublicGameButtonAttributes; ?> />Create Public</label>
								<label><input type="radio" name="gametypechoice" value="joinpublic" onchange="show_public_join()" <? echo $joinPublicGameButtonAttributes; ?> />Join Public</label>
							</div>
						</div><!-- end game_choices -->
					</div><!-- end game choice_block -->
					
					<div id="private_layer" <? echo $createPrivateGameLayerAttributes; ?> >
						<p>
							<h2>Private Game Creation</h2>
							<p>
								Private games...<br/ >
								<ul>
									<li>...are invite only; only those who you invite by e-mail address when the game is created can join.</li>
									<li>...have a variable maximum # of players; at least three (3) and no more than nine (9).</li>
								</ul><br />
							</p>
							<form method="post" name="createprivate" onsubmit="desc_newgame.php">
								<div id="form_container">

									<div class="field" id="initialphraseblock" >
										<label>Initial Phrase:</label>
										<input type="text" name="initphrase" value="<? echo $createPrivateGameLayerInitPhrase; ?>" size="60"/>
									</div>

									<div class="field" id="max_players_block" >
										<label>Maximum # of Players:</label>
										<select name="max_players">
											<option <? echo $createPrivateGameLayerThreePlayers; ?> >3</option>
											<option <? echo $createPrivateGameLayerFivePlayers; ?> >5</option>
											<option <? echo $createPrivateGameLayerSevenPlayers; ?> >7</option>
											<option <? echo $createPrivateGameLayerNinePlayers; ?> >9</option>
										</select>
									</div>															

									<div class="field" id="invitees_block" >
										<label>Invitees (E-mail addresses):</label>
										<textarea name="invitees" cols="60" rows="2"><? echo $createPrivateGameLayerInvitees; ?></textarea>
									</div>
									
									<div id="submit">
										<input type="submit" name='createprivate' value ='Create Private Game' class='button'/>
									</div> <!-- end submit  -->
								</div><!-- end form_container -->
							</form><!-- end private form -->
						</p>
					</div> <!-- end private_layer --> 
					
					<div id="public_layer" <? echo $createPublicGameLayerAttributes; ?> >
						
							<h2>Public Game Creation</h2>
								<p>
									Public games...<br/ >
									<ul>
										<li>...are open to any user on the site.</li>
										<li>...have a maximum of 7 players.</li>
										<li>...do not allow users to invite others.</li>
									</ul><br />
								</p>
								<div id="form_container">
									<form method="post" name="createpublic" onsubmit="desc_newgame.php">
										<div class="field" id="initialphraseblock" >
											<label>Initial Phrase:</label>
											<input type="text" name="initphrase" value="<? echo $createPublicGameLayerInitPhrase; ?>" size="60"/>
										</div><!-- end initialphrase block-->

									<div id="submit">
										<input type="submit" name='createpublic' value ='Create Public Game' class='button'/></div><!-- end submit -->
									 </form><!-- end public form -->
								</div><!--  end form container  -->
						
					</div> <!-- end public_layer -->
					
					<div id="public_join_layer" <? echo $joinPublicGameLayerAttributes; ?> >
						<h2>Join Random Public Game</h2>
						<p>
							Joining public games...<br/ >
							<ul>
								<li>...places you in a random public game with other site users.</li>
							</ul><br />
						</p>
						<div id="form_container">
							<form method="post" name="joinpublic" onsubmit="desc_newgame.php">
								<div id="submit">
									<input type="submit" name='joinpublic' value ='Join Public Game' class='button'/>
								</div><!-- end submit -->
							 </form><!-- end public form -->
						</div><!--  end form container  -->
					</div> <!-- end public_layer --> 
				</div> <!-- end create_game_block -->    
			</div><!-- end content  -->

			<div id="chalkboardseparator">
		
			</div><!-- end chalkboardseparator -->
	 
			<!--  FOOTER  -->
			<div id="footer">
				<?php include "includes/footer.php"; ?>
			</div>
			<!-- end footer -->

		</div><!-- end Main Container -->

	</body>
</html>