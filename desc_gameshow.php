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
			}
			else
			{
				$failureOccured = true;
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "Failed to load game to view.";
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
	
	// Check if user is allowed to view
	if(!$failureOccured)
	{
		// Must be a participant
		$isParticipant = $currentGame->IsUserParticipant($currentUser->id);
		if($isParticipant !== true)
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
	
	// Post: add new comment
	if(!$failureOccured && isset($POST['commentsubmit']))
	{
		// Check input
		if(strlen(trim($POST['commentinput'])) > 0)
		{
			$message = trim($POST['commentinput']);
			$newComment = $currentGame->AddComment($message, $currentUser->id);
			if(!($newComment instanceof DescriptionaryComment))
			{
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "Failed to add comment.";
				if(!isset($result))
				{
					$result = $newComment;
				}
				
				// Fill in comment field
				$commentText = $POST['commentinput'];
			}
		}
		else
		{
			if(isset($errorMessage))
			{
				$errorMessage = $errorMessage . "<br />";
			}
			$errorMessage = $errorMessage . "Comment cannot be blank.";
			if(!isset($result))
			{
				$result = $POST['commentinput'];
			}
			
			// Fill in comment field
			$commentText = $POST['commentinput'];
		}
	}
	
	// Get game turns
	if(!$failureOccured)
	{
		$gameTurns = $currentGame->GetGameTurns();
		if(is_array($gameTurns))
		{
			if(count($gameTurns) > 0)
			{
				$noGameTurns = false;
			}
			else
			{
				$noGameTurns = true;
			}
		}
		else
		{
			$failureOccured = true;
			$noGameTurns = true;
			
			if(isset($errorMessage))
			{
				$errorMessage = $errorMessage . "<br />";
			}
			$errorMessage = $errorMessage . "Failed to get game turns!";
			if(!isset($result))
			{
				$result = $isParticipant;
			}
		}
	}
	
	// Get comment thread
	if(!$failureOccured)
	{
		$commentThread = $currentGame->GetDiscussion();
		if($commentThread instanceof DescriptionaryCommentThread)
		{
			if(count($commentThread->Comments) > 0)
			{
				$noComments = false;
			}
			else
			{
				$noComments = true;
			}
		}
		else
		{
			$failureOccured = true;
			$noComments = true;
			
			if(isset($errorMessage))
			{
				$errorMessage = $errorMessage . "<br />";
			}
			$errorMessage = $errorMessage . "Failed to get comment thread!";
			if(!isset($result))
			{
				$result = $isParticipant;
			}
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
	<link href="css/showgame.css" type="text/css" rel="stylesheet" />
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
                    <li class="nav_help" onclick="javascript:show_link('desc_help.php')"></li> 
                </ul>
            </div> <!-- End Navigation -->
		</div> <!-- End Header -->
	
		<!-- CONTENT BLOCK  -->
		<div id="content">
			<!-- Page Header -->
			<div id="pageheader"><h1>Game Display</h1></div>
			<br/>
			<div id="resultMessage" style="color: green;">
				<? echo $resultMessage; ?>
			</div>
			<div id="errorMessage" style="color: red;">
				<? echo $errorMessage; ?>
			</div>
			<br/>
			
			<!--  Game Turn block  -->
			<div id="gameturnblock">
				
					<div class="blockheader"><h2>Turns</h2></div>
                    <!-- LOOP HERE FOR EACH TURN  -->
					<?
						if($noGameTurns != true)
						{
							foreach($gameTurns as $gameTurn)
							{
								$userWhoTookTurn = DescriptionaryUser::GetUser($gameTurn->TakenByUserID);
								if($userWhoTookTurn instanceof DescriptionaryUser)
								{
									$userWhoTookTurnName = $userWhoTookTurn->alias;
								}
								else
								{
									$userWhoTookTurnName = 'Unknown';
								}
							
								echo '<div class="indiv_gameturn">';
								if($gameTurn->Type === 'PHRASE')
								{
									echo '<div class="turn_title">[' . $gameTurn->DateTaken . '] ' . $userWhoTookTurnName . '\'s phrase:</div>';
									echo '<div class="turn_content">' . $gameTurn->Data . '</div>';
								}
								else
								{
									echo '<div class="turn_title">[' . $gameTurn->DateTaken . '] ' . $userWhoTookTurnName . '\'s drawing:</div>';
									echo '<div class="turn_content"><img src="' . $gameTurn->Data . '" /></div>';
								}
								echo '<hr class="separator" />';
								echo '</div>';
							}
						}
						else
						{
							echo '<div class="indiv_gameturn">';
							echo '<div class="turn_title" style="font-style: italic;">(No turns)</div>';
							echo '<hr class="separator" />';
							echo '</div>';
						}
					?>
					<!-- end indiv_gameturn -->
					
                    
                    <!--  END  TURN LOOP  -->
			
			</div> <!-- end gameturnblock -->
            
            <div id="commentblock">
				<div class="blockheader"><h2>Comments</h2></div>
		
		<!-- LOOP HERE FOR EACH Comment  -->
		
				<?
					if($noComments != true)
					{
						foreach($commentThread->Comments as $comment)
						{
							$author = DescriptionaryUser::GetUser($comment->MadeByUserID);
							if($author instanceof DescriptionaryUser)
							{
								$authorName = $author->alias;
							}
							else
							{
								$authorName = 'Unknown';
							}
						
							echo '<div class="indiv_comment">';
							echo '<div class="comment_alias">' . $authorName . '</div>';
							echo '<div class="comment_timestamp">' . $comment->DateMade . '</div>';
							echo '<div class="comment">' . $comment->Message . '</div>';
							echo '<hr class="separator" />';
							echo '</div>';
						}
					}
					else
					{
						echo '<div class="indiv_comment">';
						echo '<div class="comment" style="font-style: italic;">(No comments)</div>';
						echo '<hr class="separator" />';
						echo '</div>';
					}
				?>
				</div><!-- end indiv_comment --> 
				
				<!--  END  COMMENT  LOOP  -->
                
                <div id="insert_comment_block">
                	<form name="commentform" onsubmit="desc_gameshow.php?gameId=<? echo $gameId; ?>" method="post">
                		<div id="textbox"><textarea rows ="4" cols="50" name="commentinput"><? echo $commentText; ?></textarea></div>
                        <div id="comment_submit_button"><input type="submit" name='commentsubmit' value='Submit Comment' class='button'/></div>
                	 </form><!-- end comment input form -->
                </div><!-- end insert comment block -->
            
            </div><!-- end commentblock -->
		</div><!-- end content  -->  

		<div id="chalkboardseparator">

		</div><!-- end chalkboardseparator -->
	

   
		<!--  FOOTER  -->
		<div id="footer">
			<?php include "includes/footer.php"; ?>
		</div><!-- end footer -->

    </div><!-- end Main Container -->

</body>
</html>