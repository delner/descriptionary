<?
	include_once('includes/checklogged.php');
	
	$postOperationAttempted = false;
	
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
	if(isset($_GET))
	{
		$elements = new FormElements($_GET);
		$GET = $elements->getElements();
	}
	
	// Get game
	if(!$failureOccured)
	{
		if(!is_null($GET['gameId']))
		{
			$currentGame = DescriptionaryGame::GetGame($GET['gameId']);
			if($currentGame instanceof DescriptionaryGame)
			{
				$gameId = $currentGame->GameID;
				
				// Game must not be finished
				if(!is_null($currentGame->Status->DateFinished))
				{
					$failureOccured = true;
					if(isset($errorMessage))
					{
						$errorMessage = $errorMessage . "<br />";
					}
					$errorMessage = $errorMessage . "This game has already completed.";
					if(!isset($result))
					{
						$result = $currentGame->Status->DateFinished;
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
				$errorMessage = $errorMessage . "Failed to load game to play.";
				if(!isset($result))
				{
					$result = $currentGame;
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
				$errorMessage = $errorMessage . "Could not find Game ID to load.";
				if(!isset($result))
				{
					$result = $GET['gameId'];
				}
		}
		
	}
	
	// Check if player is allowed to take a turn
	if(!$failureOccured)
	{
		// Must be a participant
		$isParticipant = $currentGame->IsUserParticipant($currentUser->id);
		if($isParticipant === true)
		{
			// Must be their turn
			$usersAssignedTurnNum = $currentGame->GetTurnNumOfUser($currentUser->id);
			if($usersAssignedTurnNum !== $currentGame->Status->CurrentTurnNum)
			{
				$failureOccured = true;
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "It is not your turn!";
				if(!isset($result))
				{
					$result = $usersAssignedTurnNum;
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
			$errorMessage = $errorMessage . "You are not a participant of this game!";
			if(!isset($result))
			{
				$result = $isParticipant;
			}
		}
	}
	
	// Get previous game turn
	if(!$failureOccured)
	{
		$previousGameTurn = $currentGame->GetMostRecentTurn();
		if(!($previousGameTurn instanceof DescriptionaryGameTurn))
		{
			$failureOccured = true;
			if(isset($errorMessage))
			{
				$errorMessage = $errorMessage . "<br />";
			}
			$errorMessage = $errorMessage . "Failed to retrieve last turn played.";
			if(!isset($result))
			{
				$result = $currentGame;
			}
		}
	}
		
	// Fill in fields from previous turn
	if(!$failureOccured)
	{
		if($previousGameTurn->Type == "PHRASE")
		{
			// Show the draw picture layer
			$drawTurnLayerPhrase = $previousGameTurn->Data;
			$loadFlashJs = "loadDrawingFlashObj();";
			
			$drawTurnLayerAttributes = 'style="display: block;"';
		}
		else
		{
			// Show the guess phrase layer
			$guessPhraseLayerImageUrl = $previousGameTurn->Data;
			
			$guessPhraseLayerAttributes = 'style="display: block;"';
		}
	}
	
	// Post: Add new game turn
	if(!$failureOccured)
	{
		if(isset($GLOBALS["HTTP_RAW_POST_DATA"]))
		{
			$postOperationAttempted = true;
			
			// Take drawing turn
			// Load JPG and determine its destination
			$jpg = $GLOBALS["HTTP_RAW_POST_DATA"];
			$jpgFilePath = ("user_drawings/" . uniqid() . ".jpg");
			
			// Save drawing to file
			$fileHandle = fopen($jpgFilePath, 'w');
			fwrite($fileHandle, $jpg);
			fclose($fileHandle);
			
			// Add the game turn
			$newTurn = $currentGame->TakeTurn("PICTURE", $jpgFilePath, $currentUser->id);
			if($newTurn instanceof DescriptionaryGameTurn)
			{
				// Hide take turn fields
				$drawTurnLayerAttributes = 'style="display: none;"';
				
				// Show success message and redirect
				$resultMessage = "Successfully took game turn!<br/>";
				$redirect = '<meta http-equiv="refresh" content="3;url=index.php">';
			}
			else
			{
				$failureOccured = true;
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "Failed to take turn.";
				if(!isset($result))
				{
					$result = $currentGame;
				}
			}
		}
		if(isset($POST['guessphrase']))
		{
			$postOperationAttempted = true;
			
			// Take guess turn
			// Check phrase input
			if(strlen(trim($POST['phrase_guess'])) > 0)
			{
				$phrase = trim($POST['phrase_guess']);
				
				// Add the game turn
				$newTurn = $currentGame->TakeTurn("PHRASE", $phrase, $currentUser->id);
				if($newTurn instanceof DescriptionaryGameTurn)
				{
					// Hide take turn fields
					$guessPhraseLayerAttributes = 'style="display: none;"';
					
					// Show success message and redirect
					$resultMessage = "Successfully took game turn!<br/>";
					$redirect = '<meta http-equiv="refresh" content="3;url=index.php">';
				}
				else
				{
					$failureOccured = true;
					if(isset($errorMessage))
					{
						$errorMessage = $errorMessage . "<br />";
					}
					$errorMessage = $errorMessage . "Failed to take turn.";
					if(!isset($result))
					{
						$result = $currentGame;
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
				$errorMessage = $errorMessage . "Invalid phrase; must not be blank.";
			}
			
			if($failureOccured)
			{
				$guessPhraseLayerPhrase = $POST['phrase_guess'];
			}
		}
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<? echo $redirect; ?>
	<title>Descriptionary ONLINE</title>
	<link href="css/default.css" type="text/css" rel="stylesheet" />
	<link href="css/footerstyle.css" type="text/css" rel="stylesheet" />
	<link href="css/gameplaystyle.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="scripts/default.js"></script>
	<script type="text/javascript" src="scripts/swfobject.js"></script>
	<script type="text/javascript">
		function loadDrawingFlashObj()
		{
			var flashvars = { id:<? echo $gameId; ?> };
			var params = {};
			var attributes = {};
			swfobject.embedSWF("paintprogram/paint.swf", "drawingCanvas", "400", "300", "9.0.0","", flashvars, params, attributes);
		}
		
		<? echo $loadFlashJs; ?>
	</script>
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
			<div id="pageheader"><h1>Play the Game!</h1></div>
			
			<!--Information for the page  -->
			<div id="pageinfo"></div>
     
			<!--   Gameplayblock  -->
			<div id="gameblock">
            	<div class="blockheader"><h2>Take Turn</h2></div>
				
					<div id="resultMessage" style="color: green;">
						<? echo $resultMessage; ?>
					</div>
					<div id="errorMessage" style="color: red;">
						<? echo $errorMessage; ?>
					</div>
					<br/>
				
            		<div id="draw_turn_layer" <? echo $drawTurnLayerAttributes; ?> >
                    	<div id="passed_phrase_header">Passed Phrase:</div>
                        <div id="passed_phrase"><? echo $drawTurnLayerPhrase; ?></div>
                        
                        <!-- Drawing canvas -->
                        <div id="drawingCanvas">
							<strong>This content requires Flash Player 9 (or a more recent version).
							<noscript>Make sure JavaScript is turned on. </noscript>
							You need to <a href="http://www.adobe.com/shockwave/download/index.cgi?p1_prod_version=shockwaveflash" target="_blank">
							<span style="text-decoration: underline;">upgrade your Flash Player</span></a></strong>
                        </div><!-- end drawingCanvas -->
                    </div><!-- end draw_turn_layer -->
                    
                    <div id="guess_phrase_layer" <? echo $guessPhraseLayerAttributes; ?> >
						<div id="sub_picture" >
							<img src="<? echo $guessPhraseLayerImageUrl; ?>"  />
						</div>
						
							<div id="form_container">
                            	<form method="post" onsubmit="desc_playgame.php?gameId=<? echo $gameId; ?>">
									<div class="field" id="guessphraseblock">
										<label>Guess Phrase:</label>
										<input type="text" name="phrase_guess" value="<? echo $guessPhraseLayerPhrase; ?>" size="60"/>
									</div><!-- end guessphraseblock -->
			
									<div id="submit">
										<input type="submit" name='guessphrase' value ='Submit' class='button'/>
									</div> 
                                </form>
							</div><!-- end form_container -->	
                    </div><!-- end guess_phrase_layer -->
			</div> <!-- end gameplayblock -->  

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