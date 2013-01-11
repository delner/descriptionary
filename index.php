<?php
	include_once('includes/checklogged.php');
	
	$postOperationAttempted = false;
	$failureOccured = false;
	
	// Get active user
	$currentUser = new DescriptionaryUser();
	if(!($currentUser instanceof DescriptionaryUser))
	{
		$failureOccured = true;
		$errorMessage = "Failed to get the current user.";
		$result = $currentUser;
	}

	// Post: Accept/Decline invite
	if(isset($_POST['accept']) || isset($_POST['decline']))
	{
		$postOperationAttempted = true;
		
		$elements = new FormElements($_POST);
		$POST = $elements->getElements();
		
		$invite = DescriptionaryGameInvite::GetInvite($POST['GameID'], $currentUser->id);
		if($invite instanceof DescriptionaryGameInvite)
		{
			if(isset($POST['accept']))
			{
				$response = '1';
			}
			else
			{
				$response = '0';
			}
			
			$response = $invite->Respond($response, $POST['InviteAuthCode']);
			if(!(strpos($response, "SUCCESS") === false))
			{
				$resultMessage = "Accepted game invitation.";
				if(!isset($result))
				{
					$result = $response;
				}
			}
			else
			{
				$failureOccured = true;
				if(isset($errorMessage))
				{
					$errorMessage = $errorMessage . "<br />";
				}
				$errorMessage = $errorMessage . "Failed to respond to game invite.";
				if(!isset($result))
				{
					$result = $invite;
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
			$errorMessage = $errorMessage . "Failed to get invite to respond to.";
			if(!isset($result))
			{
				$result = $invite;
			}
		}
	}
	
	// Get invites
	$invites = $currentUser->GetInvites();
	if(!is_array($invites))
	{
		$failureOccured = true;
		if(isset($errorMessage))
		{
			$errorMessage = $errorMessage . "<br />";
		}
		$errorMessage = $errorMessage . "Failed to get invites.";
		if(!isset($result))
		{
			$result = $invites;
		}
	}
	
	// Get games
	$games = $currentUser->GetGames();
	if(!is_array($games))
	{
		$failureOccured = true;
		if(isset($errorMessage))
		{
			$errorMessage = $errorMessage . "<br />";
		}
		$errorMessage = $errorMessage . "Failed to get games.";
		if(!isset($result))
		{
			$result = $games;
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
	<link href="css/dashboardstyle.css" type="text/css" rel="stylesheet" />
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
                    <li class="nav_profile" onclick="javascript:show_link('desc_myprofile.php')"></li>
                    <li class="nav_logout" onclick="javascript:show_link('desc_logout.php')"></li>
                    <li class="nav_help" onclick="javascript:show_link('desc_help.php')"></li> 
                </ul>
            </div> <!-- End Navigation -->
		</div> <!-- End Header -->
	
		<!-- CONTENT BLOCK  -->
		<div id="content">
			<!-- Page Header -->
			<div id="pageheader"><h1>My Dashboard</h1></div>
			
			
			<!--Information for the page  -->
			<div id="pageinfo"><p>The Dashboard has all the information for the games you are involved in.  Click on a 
			game to be taken there to take your turn or see your results of finished games.</p></div>
     
			<!--  Dashboard block  -->
			<div id="dashboardblock">
            	<div class="blockheader">
					<h2>New Games</h2>
				</div>
                <div id="new_games_link">
              		 <div id="newgame_info">Click this button to create new games or join a public game.</div>
                     <div id="newgame_button"><a href="desc_newgame.php"><img src="images/desc_newgame_button.png" border="0" /></a></div><!-- end button  -->
                </div><!-- end new games link -->
                
                
                
				<div class="blockheader">
					<h1>My Games</h1>
				</div>
				<p>
					<div id="invites_layer" >
						
						<h2>Invites</h2>
							<div id="resultMessage" style="color: green;">
								<? echo $resultMessage; ?>
							</div>
							<div id="errorMessage" style="color: red;">
								<? echo $errorMessage; ?>
							</div>
						
							<div class="p_table">
								<table >
								  <tr>
									<th>Invited By</th>
									<th>Date</th>
									<th>Action</th>
								  </tr>
						  <?
								if(isset($invites) && count($invites) > 0)
								{
									foreach($invites as $invite)
									{
										$inviter = DescriptionaryUser::GetUser($invite->InviterUserID);
										
										echo ('<form name="accept-' . $invite->GameID  . '" action="index.php" method="post">');
											echo '<tr>';
												echo ('<td>' . $inviter->alias . '</td>');
												echo ('<td>' . $invite->DateInvited . '</td>');
												echo ('<input type="hidden" name="GameID" value="' . $invite->GameID  . '" />');
												echo ('<input type="hidden" name="InviteAuthCode" value="' . $invite->InviteAuthCode  . '" />');
												echo '<td><input type="submit" name="accept" value="Accept" />&nbsp;<input type="submit" name="decline" value="Decline" /></td>';
											echo '</tr>';
										echo '</form>';
									}
								}
								else
								{
									echo '<tr>';
										echo '<td style="font-style: italic;">No active invites</td>';
										echo '<td>&nbsp;</td>';
										echo '<td>&nbsp;</td>';
									echo '</tr>';
								}
						  ?>
								  
								</table>
							</div><!-- end private_table -->
					</div><!-- end invites_layer -->
				</p>		
				<p>
					<div id="mygames_layer" >                 
                        	<h2>Games I Am In</h2>
							<div class="p_table">
								<table >
									<tr>
										<th>Game</th>
										<th>Date</th>
										<th>Turn Status</th>
										<th>Action</th>
									</tr>
									<?
										if(isset($games) && count($games) > 0)
										{
											foreach($games as $game)
											{
												$creator = DescriptionaryUser::GetUser($game->CreatorID);
												if($creator instanceof DescriptionaryUser)
												{
													$creatorAlias = $creator->alias;
												}
												else
												{
													$creatorAlias = "Unknown User";
												}
												
												$turnNum = $game->GetTurnNumOfUser($currentUser->id);
												if(strpos($response, "ERROR") === false)
												{
													if($turnNum < $game->Status->CurrentTurnNum)
													{
														// View game
														$action = '<td><input type="submit" name="viewgame" value="View Game" /></td>';
														$postAction = 'desc_gameshow.php?gameId=' . $game->GameID;
													}
													if($turnNum == $game->Status->CurrentTurnNum)
													{
														if(is_null($game->Status->DateFinished))
														{
															// Play game
															$action = '<td><input type="submit" name="playgame" value="Play Game" /></td>';
															$postAction = 'desc_playgame.php?gameId=' . $game->GameID;
														}
														else
														{
															// View game
															$action = '<td><input type="submit" name="viewgame" value="View Game" /></td>';
															$postAction = 'desc_gameshow.php?gameId=' . $game->GameID;
														}
													}
													if($turnNum > $game->Status->CurrentTurnNum)
													{
														// Waiting message
														$action = '<td style="font-style: italic;">Waiting for turn...</td>';
													}
												}
												else
												{
													$action = '<td style="font-style: italic;">None available.</td>';
												}
												
												echo ('<form name="game-' . $game->GameID  . '" action="' . $postAction . '" method="post">');
													echo '<tr>';
														echo ('<td>' . $creatorAlias . '\'s Game</td>');
														echo ('<td>' . $game->DateCreated . '</td>');
														echo ('<td>' . $game->Status->CurrentTurnNum . '/' . $game->Settings->MaxNumOfTurns . '</td>');
														echo ('<input type="hidden" name="GameID" value="' . $invite->GameID  . '" />');
														echo $action;
													echo '</tr>';
												echo '</form>';
											}
										}
										else
										{
											echo '<tr>';
												echo '<td style="font-style: italic;">No games.</td>';
												echo '<td>&nbsp;</td>';
												echo '<td>&nbsp;</td>';
												echo '<td>&nbsp;</td>';
											echo '</tr>';
										}
									?>
								</table>
							</div><!-- end private_table -->
					</div><!-- end mygames_layer -->
				</p>
			</div> <!-- end dashboardblock -->  
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