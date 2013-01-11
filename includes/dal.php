<?
	class DescriptionaryDAL
	{
		// USER oriented
		public static function GetDescriptionaryUser($userId)
		{
			$database = Database::getInstance();
			
			$sql =
			"SELECT a.*, b.*
			FROM descriptionary_users a, users b
			WHERE
				a.id = " . $userId . "
			AND	b.id = a.id;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Could not find a Descriptionary user matching ID ' . $userId . '.';
			}
			
			$row = $database->fetch_row($sqlResult);
			$userSettings = new DescriptionaryUserSettings(	$row['NotifyOnFriendInvite'],
															$row['NotifyOnTurnPrivateGame'],
															$row['NotifyOnGameParticipatedComplete'],
															$row['NotifyOnGameCreatedComplete'],
															$row['NotifyOnCommentInPublicGame'],
															$row['NotifyOnCommentInPrivateGame'],
															$row['AcceptsInvitations']);
			$user = new DescriptionaryUser($userSettings);
			
			// Load User properties
			$user->id = $row['id'];
			$user->email = $row['email'];
			$user->alias = $row['alias'];
			
			return $user; // DescriptionaryUser
		}
		public static function GetDescriptionaryUserByEmail($email)
		{
			$database = Database::getInstance();
			
			$sql =
			"SELECT a.*, b.*
			FROM descriptionary_users a, users b
			WHERE
				b.email = '" . $email . "'
			AND	b.id = a.id;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Could not find a Descriptionary user matching email ' . $email . '.';
			}
			
			$row = $database->fetch_row($sqlResult);
			$userSettings = new DescriptionaryUserSettings(	$row['NotifyOnFriendInvite'],
															$row['NotifyOnTurnPrivateGame'],
															$row['NotifyOnGameParticipatedComplete'],
															$row['NotifyOnGameCreatedComplete'],
															$row['NotifyOnCommentInPublicGame'],
															$row['NotifyOnCommentInPrivateGame'],
															$row['AcceptsInvitations']);
			$user = new DescriptionaryUser($userSettings);
			
			// Load User properties
			$user->id = $row['id'];
			$user->email = $row['email'];
			$user->alias = $row['alias'];
			
			return $user; // DescriptionaryUser
		}
		public static function GetDescriptionaryUsersGames($userId)
		{
			$database = Database::getInstance();
			$sql =
			"SELECT b.* FROM descriptionary_game_participants a, descriptionary_games b
			WHERE a.GameID = b.ID
			AND   a.UserID = " . $userId . "
			ORDER BY b.DateCreated DESC;";
			$sqlResult = $database->get_result($sql);
			
			$games = array();
			while($row = $database->fetch_row($sqlResult))
			{
				$settings = new DescriptionaryGameSettings($row['IsPrivate'], $row['MaxNumOfUsers'], $row['MaxNumOfTurns']);
				$status = new DescriptionaryGameStatus($row['NumOfUsers'], $row['CurrentTurnNum'], $row['DateLastTurnTaken'], $row['DateFinished']);
				$game = new DescriptionaryGame(	$row['ID'],
												$row['CreatorUserId'],
												$settings,
												$status,
												$row['CommentThreadId'],
												$row['DateCreated']);
				$games[] = $game;
			}
			
			return $games; // array(DescriptionaryGame*)
		}
		public static function GetDescriptionaryUsersPreferredGames($userId)
		{
			$database = Database::getInstance();
			
			// Necessary conditions:
			//  - Are not finished
			//  - Are not full
			//  - Are not already in
			//  - Are public
			// Preferences:
			// 1. Fewest # of players to go before turn (CurrentTurn - NumOfUsers, DESC)
			// 2. As close as possible to being complete (MaxNumOfTurns - CurrentTurnNum, ASC)
			
			$sql =
			"SELECT a.* FROM descriptionary_games a 
			WHERE
				a.DateFinished IS NULL
			AND a.NumOfUsers < MaxNumOfUsers
			AND NOT EXISTS
			(
				SELECT GameID, UserID
				FROM descriptionary_game_participants
				WHERE
					GameID = a.ID
				AND UserID = " . $userId . "
			)
			AND a.IsPrivate = 0
			ORDER BY (a.CurrentTurnNum - a.NumOfUsers) DESC, (a.MaxNumOfTurns - a.CurrentTurnNum) DESC;";
			$sqlResult = $database->get_result($sql);
			
			$games = array();
			while($row = $database->fetch_row($sqlResult))
			{
				$settings = new DescriptionaryGameSettings($row['IsPrivate'], $row['MaxNumOfUsers'], $row['MaxNumOfTurns']);
				$status = new DescriptionaryGameStatus($row['NumOfUsers'], $row['CurrentTurnNum'], $row['DateLastTurnTaken'], $row['DateFinished']);
				$game = new DescriptionaryGame(	$row['ID'],
												$row['CreatorUserId'],
												$settings,
												$status,
												$row['CommentThreadId'],
												$row['DateCreated']);
				$games[] = $game;
			}
			
			return $games; // array(DescriptionaryGame*)
		}
		public static function GetDescriptionaryUsersGameInvites($userId)
		{
			$database = Database::getInstance();
			$sql =
			"SELECT *
			FROM descriptionary_game_invites
			WHERE InviteeUserID = " . $userId . "
			AND Response IS NULL;";
			$sqlResult = $database->get_result($sql);
			
			$invites = array();
			while($row = $database->fetch_row($sqlResult))
			{
				$invite = new DescriptionaryGameInvite(	$row['GameID'],
														$row['InviteeUserID'],
														$row['InviterUserID'],
														$row['InviteAuthCode'],
														$row['DateInvited'],
														$row['Response'],
														$row['DateResponded']);
				$invites[] = $invite;
			}
			
			return $invites;	// array(DescriptionaryGameInvite*)
		}
		public static function GetUsersTurnNum($gameId, $userId)
		{
			$database = Database::getInstance();
			$sql =
			"SELECT AssignedTurnID
			FROM descriptionary_game_participants
			WHERE
				GameID = " . $gameId . "
			AND	UserID = " . $userId . ";";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: User ID ' . $userId . ' is not a participant of Game ID ' . $gameId . '.';
			}
			$row = $database->fetch_row($sqlResult);
				
			return $row['AssignedTurnID']; // int
		}
		
		public static function AddNewDescriptionaryUser($id, $settings)
		{
			$database = Database::getInstance();
			
			// Ensure a user with this ID doesn't exist already
			$sql = "SELECT ID FROM descriptionary_users WHERE id = " . $id . ";";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) == 1)
			{
				return 'ERROR: A DescriptionaryUser already exists with ID ' . $id . '.';
			}
			
			// Ensure a DaveEtc User matches this ID
			$sql = "SELECT ID FROM users WHERE id = " . $id . ";";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: There is no matching DaveEtc user with ID ' . $id . '.';
			}
			
			// Add new user
			$sql = 
			"INSERT INTO descriptionary_users
			(
				ID,
				NotifyOnFriendInvite,
				NotifyOnTurnPrivateGame,
				NotifyOnGameParticipatedComplete,
				NotifyOnGameCreatedComplete,
				NotifyOnCommentInPublicGame,
				NotifyOnCommentInPrivateGame,
				AcceptsInvitations
			)
			VALUES
			(
				" . $id . ",
				" . $settings->NotifyOnFriendInvite . ",
				" . $settings->NotifyOnTurnPrivateGame . ",
				" . $settings->NotifyOnGameParticipatedComplete . ",
				" . $settings->NotifyOnGameCreatedComplete . ", 
				" . $settings->NotifyOnCommentInPublicGame . ",
				" . $settings->NotifyOnCommentInPrivateGame . ",
				" . $settings->AcceptsInvitations . "
			);";
			
			$result = $database->get_result($sql);
			
			return DescriptionaryDAL::GetDescriptionaryUser($id);
		}
		public static function UpdateDescriptionaryUserSettings($id, $settings)
		{
			$database = Database::getInstance();
			$sql =
			"UPDATE descriptionary_users
			SET
				NotifyOnFriendInvite = " . $settings->NotifyOnFriendInvite . ",
				NotifyOnTurnPrivateGame = " . $settings->NotifyOnTurnPrivateGame . ",
				NotifyOnGameParticipatedComplete = " . $settings->NotifyOnGameParticipatedComplete . ",
				NotifyOnGameCreatedComplete = " . $settings->NotifyOnGameCreatedComplete . ",
				NotifyOnCommentInPublicGame = " . $settings->NotifyOnCommentInPublicGame . ",
				NotifyOnCommentInPrivateGame = " . $settings->NotifyOnCommentInPrivateGame . ",
				AcceptsInvitations = " . $settings->AcceptsInvitations . "
			WHERE ID = " . $id . ";";
			$sqlResult = $database->get_result($sql);
			
			return 'SUCCESS: Updated settings for DescriptionaryUser with ID ' . $id . '.';
		}
		public static function RespondToDescriptionaryGameInvite($gameId, $inviteeUserId, $authCode, $response)
		{
			// Make sure the response is either a yes or no (1 or 0)
			if(!($response == 0 || $response == 1))
			{
				return 'ERROR: Response must be either 0 or 1.';
			}
		
			$database = Database::getInstance();
			
			// Check if there's room in the game
			$sql =
			"SELECT ID
			FROM descriptionary_games
			WHERE
				ID = " . $gameId . "
			AND NumOfUsers < MaxNumOfUsers;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Game is full.';
			}
			
			// Authenticate the game invite
			$sql =
			"SELECT Response
			FROM descriptionary_game_invites
			WHERE
				GameID = " . $gameId . "
			AND InviteeUserID = " . $inviteeUserId . "
			AND InviteAuthCode = '" . $authCode . "'
			AND Response IS NULL;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Could not authenticate the invite response.';
			}
			
			// Update the game invite
			$sql =
			"UPDATE descriptionary_game_invites
			SET
				Response = " . $response . ",
				DateResponded = now()
			WHERE
				GameID = " . $gameId . "
			AND InviteeUserID = " . $inviteeUserId . "
			AND InviteAuthCode = '" . $authCode . "';";
			
			$sqlResult = $database->get_result($sql);
			
			if($response != 1)
			{
				return 'SUCCESS; Declined invite.';
			}
			
			DescriptionaryDAL::AddDescriptionaryGameParticipant($gameId, $inviteeUserId);
			
			return 'SUCCESS; Accepted invite.';
		}
		
		// COMMENT oriented
		public static function GetCommentThread($threadId)
		{
			$database = Database::getInstance();
			
			// Ensure a comment thread matches this ID
			$sql = "SELECT DISTINCT ThreadID FROM descriptionary_comments WHERE ThreadID = " . $threadId . " LIMIT 1;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: There is no matching CommentThread with ID ' . $threadId . '.';
			}
			
			// Build comments
			$sql =
			"SELECT * FROM descriptionary_comments WHERE ThreadID = " . $threadId . " ORDER BY CommentID ASC;";
			$sqlResult = $database->get_result($sql);
			
			$comments = array();
			while($row = $database->fetch_row($sqlResult))
			{
				$comment = new DescriptionaryComment($row['CommentID'], $row['MadeByUserId'], $row['Message'], $row['DateMade']);
				$comments[] = $comment;
			}
			$commentThread = new DescriptionaryCommentThread($threadId, $comments);
			
			return $commentThread;	// DescriptionaryThread
		}
		public static function GetComment($threadId, $commentId)
		{
			$database = Database::getInstance();
			
			// Ensure a thread & comment ID have matching ID
			$sql = "SELECT DISTINCT ThreadID FROM descriptionary_comments WHERE ThreadID = " . $threadId . " AND CommentID = " . $commentId . " LIMIT 1;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: There is no matching Comment with Thread ID ' . $threadId . ' and Comment ID ' . $commentId . '.';
			}
			
			// Build comment
			$sql =
			"SELECT * FROM descriptionary_comments WHERE ThreadID = " . $threadId . " AND CommentID = " . $commentId . ";";
			$sqlResult = $database->get_result($sql);
			
			$row = $database->fetch_row($sqlResult);
			$comment = new DescriptionaryComment($row['CommentID'], $row['MadeByUserId'], $row['Message'], $row['DateMade']);
			
			return $comment;	// DescriptionaryComment
		}
		public static function CreateNewCommentThread($message, $madeByUserId)
		{
			$database = Database::getInstance();
			
			// Get the next ThreadID
			$sql =
			"SELECT DISTINCT ThreadID+1 as NextThreadId
			FROM descriptionary_comments
			ORDER BY ThreadID DESC
			LIMIT 1;";
			$sqlResult = $database->get_result($sql);
			if($database->num_rows($sqlResult) == 1)
			{
				$row = $database->fetch_row($sqlResult);
				$threadId = $row['NextThreadId'];
			}
			else
			{
				// No comment thread returned; this is the first comment thread.
				$threadId = 0;
			}
			
			
			// Add comment to new thread
			$sql =
			"INSERT INTO descriptionary_comments
			(
				ThreadID,
				CommentID,
				MadeByUserId,
				Message,
				DateMade
			)
			VALUES
			(
				" . $threadId . ",
				0,
				" . $madeByUserId . ",
				'" . str_replace("'", "''", $message) . "',
				now()
			);";
			$sqlResult = $database->get_result($sql);
			
			return DescriptionaryDAL::GetCommentThread($threadId);
		}
		public static function AddCommentToThread($threadId, $message, $madeByUserId)
		{
			$database = Database::getInstance();
			
			// Ensure a comment thread matches this ID
			$sql = "SELECT DISTINCT ThreadID FROM descriptionary_comments WHERE ThreadID = " . $threadId . " LIMIT 1;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: There is no matching CommentThread with ID ' . $threadId . '.';
			}
			
			// Get the next CommentID
			$sql =
			"SELECT DISTINCT CommentID+1 as NextCommentId
			FROM descriptionary_comments
			WHERE
				ThreadID = " . $threadId . " 
			ORDER BY CommentID DESC
			LIMIT 1;";
			$sqlResult = $database->get_result($sql);
			
			// There is always at least one comment on a thread; do not worry about case "if no comments in thread."
			$row = $database->fetch_row($sqlResult);
			$commentId = $row['NextCommentId'];
			
			// Add comment to new thread
			
			$sql =  // "CALL sp_Descriptionary_Comment_Insert(" . $threadId . "," . $commentId . "," . $madeByUserId . ",'" . str_replace("'", "''", $message) . "');";
			"INSERT INTO descriptionary_comments
			(
				ThreadID,
				CommentID,
				MadeByUserId,
				Message,
				DateMade
			)
			VALUES
			(
				" . $threadId . ",
				" . $commentId . ",
				" . $madeByUserId . ",
				'" . str_replace("'", "''", $message) . "',
				now()
			);";
			$sqlResult = $database->get_result($sql);
				
			return DescriptionaryDAL::GetComment($threadId, $commentId);
		}
		
		
		// GAME oriented
		public static function GetDescriptionaryGame($gameId)
		{
			$database = Database::getInstance();
			
			$sql =
			"SELECT DISTINCT * FROM descriptionary_games
			WHERE ID = " . $gameId . ";";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Could not find a game matching ID ' . $gameId . '.';
			}
			
			$row = $database->fetch_row($sqlResult);
			
			$settings = new DescriptionaryGameSettings($row['IsPrivate'], $row['MaxNumOfUsers'], $row['MaxNumOfTurns']);
			$status = new DescriptionaryGameStatus($row['NumOfUsers'], $row['CurrentTurnNum'], $row['DateLastTurnTaken'], $row['DateFinished']);
			$game = new DescriptionaryGame(	$row['ID'],
											$row['CreatorUserId'],
											$settings,
											$status,
											$row['CommentThreadId'],
											$row['DateCreated']);
											
			return $game;
		}
		public static function GetActiveOpenPublicDescriptionaryGames()
		{
			$database = Database::getInstance();
			
			// Get all public games that haven't filled and haven't completed.
			$sql =
			"SELECT * FROM descriptionary_games
			WHERE
				DateFinished IS NULL
			AND NumOfUsers < MaxNumOfUsers
			AND IsPrivate = 0;";
			$sqlResult = $database->get_result($sql);
			
			$games = array();
			while($row = $database->fetch_row($sqlResult))
			{
				$settings = new DescriptionaryGameSettings($row['IsPrivate'], $row['MaxNumOfUsers'], $row['MaxNumOfTurns']);
				$status = new DescriptionaryGameStatus($row['NumOfUsers'], $row['CurrentTurnNum'], $row['DateLastTurnTaken'], $row['DateFinished']);
				$game = new DescriptionaryGame(	$row['ID'],
												$row['CreatorUserId'],
												$settings,
												$status,
												$row['CommentThreadId'],
												$row['DateCreated']);
				$games[] = $game;
			}
											
			return $games;
		}
		public static function GetDescriptionaryGameTurn($gameId, $turnId)
		{
			$database = Database::getInstance();
			
			$sql =
			"SELECT DISTINCT * FROM descriptionary_game_turns
			WHERE
				GameID = " . $gameId . "
			AND TurnID = " . $turnId . ";";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Could not find turn ID ' . $turnId . ' under Game ID '. $gameId . '.';
			}
			
			$row = $database->fetch_row($sqlResult);
			$gameTurn = new DescriptionaryGameTurn(	$row['TurnID'],
													$row['Type'],
													$row['Data'],
													$row['TakenByUserId'],
													$row['DateTaken']);
													
			return $gameTurn;
		}
		public static function GetDescriptionaryGameTurns($gameId)
		{
			$database = Database::getInstance();
			
			$sql =
			"SELECT * FROM descriptionary_game_turns
			WHERE GameID = " . $gameId . "
			ORDER BY TurnID ASC;";
			$sqlResult = $database->get_result($sql);
			
			$gameTurns = array();
			while($row = $database->fetch_row($sqlResult))
			{
				$gameTurn = new DescriptionaryGameTurn(	$row['TurnID'],
														$row['Type'],
														$row['Data'],
														$row['TakenByUserId'],
														$row['DateTaken']);
				$gameTurns[] = $gameTurn;
			}
			
			return $gameTurns;
		}
		public static function GetDescriptionaryGameParticipants($gameId)
		{
			$database = Database::getInstance();
			
			$sql =
			"SELECT UserID
			FROM descriptionary_game_participants
			WHERE
				GameID = " . $gameId . ";";
			$sqlResult = $database->get_result($sql);
			
			$participants = array();
			while($row = $database->fetch_row($sqlResult))
			{
				$participant = DescriptionaryDAL::GetDescriptionaryUser($row['UserID']);
				$participants[] = $participant;
			}
			
			return $participants; // array(DescriptionaryUser*)
		}
		public static function GetDescriptionaryGameInvites($gameId)
		{
			$database = Database::getInstance();
			$sql =
			"SELECT *
			FROM descriptionary_game_invites
			WHERE GameID = " . $gameId . ";";
			$sqlResult = $database->get_result($sql);
			
			$invites = array();
			while($row = $database->fetch_row($sqlResult))
			{
				$invite = new DescriptionaryGameInvite(	$row['GameID'],
														$row['InviteeUserID'],
														$row['InviterUserID'],
														$row['InviteAuthCode'],
														$row['DateInvited'],
														$row['Response'],
														$row['DateResponded']);
				$invites[] = $invite;
			}
			
			return $invites;	// array(DescriptionaryGameInvite*)
		}
		public static function GetDescriptionaryGameInvite($gameId, $inviteeId)
		{
			$database = Database::getInstance();
			
			$sql =
			"SELECT *
			FROM descriptionary_game_invites
			WHERE
				GameID = " . $gameId . " 
			AND InviteeUserId = " . $inviteeId . ";";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Could not find invite for user ID ' . $inviteeId . ' under Game ID '. $gameId . '.';
			}
			
			$row = $database->fetch_row($sqlResult);
			$invite = new DescriptionaryGameInvite(	$row['GameID'],
													$row['InviteeUserID'],
													$row['InviterUserID'],
													$row['InviteAuthCode'],
													$row['DateInvited'],
													$row['Response'],
													$row['DateResponded']);
			
			return $invite;
		}
		public static function GetDescriptionaryGameInvitees($gameId)
		{
			$database = Database::getInstance();
			
			$sql =
			"SELECT InviteeUserID
			FROM descriptionary_game_invites
			WHERE
				GameID = " . $gameId . ";";
			$sqlResult = $database->get_result($sql);
			
			$invitees = array();
			while($row = $database->fetch_row($sqlResult))
			{
				$invitee = DescriptionaryDAL::GetDescriptionaryUser($row['InviteeUserID']);
				$invitees[] = $invitee;
			}
			
			return $invitees; // array(DescriptionaryUser*)
		}
		public static function SetDescriptionaryGameCommentThread($gameId, $commentThreadId)
		{
			$database = Database::getInstance();
			
			// Check if game exists
			$sql =
			"SELECT DISTINCT ID FROM descriptionary_games
			WHERE
				ID = " . $gameId . ";";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Could not find a game matching ID ' . $gameId . '.';
			}
			
			// Set game's comment thread ID
			// And update the game to show user joined
			$sql =
			"UPDATE descriptionary_games
			SET
				CommentThreadId = " . $commentThreadId . "
			WHERE
				ID = " . $gameId . ";";
			$sqlResult = $database->get_result($sql);
			
			return 'SUCCESS; Comment thread ID set to ' . $commentThreadId . ' for game ID ' . $gameId . '.';
		}
		public static function AddDescriptionaryGame($creatorUserId, $settings)
		{
			$database = Database::getInstance();
			
			// Get the next game ID
			$sql =
			"SELECT DISTINCT ID+1 as NextGameId
			FROM descriptionary_games 
			ORDER BY ID DESC
			LIMIT 1;";
			$sqlResult = $database->get_result($sql);
			if($database->num_rows($sqlResult) == 1)
			{
				$row = $database->fetch_row($sqlResult);
				$gameId = $row['NextGameId'];
			}
			else
			{
				// No games returned; this is the first game.
				$gameId = 0;
			}
			
			// Add new game
			$sql = 
			"INSERT INTO descriptionary_games
			(
				ID,
				IsPrivate,
				NumOfUsers,
				MaxNumOfUsers,
				CreatorUserId,
				CurrentTurnNum,
				MaxNumOfTurns,
				DateCreated
			)
			VALUES
			(
				" . $gameId . ",
				" . $settings->IsPrivate . ",
				0,
				" . $settings->MaxNumOfUsers . ",
				" . $creatorUserId . ", 
				1,
				" . $settings->MaxNumOfTurns . ",
				now()
			);";
			$result = $database->get_result($sql);
			
			DescriptionaryDAL::AddDescriptionaryGameParticipant($gameId, $creatorUserId);
			
			return DescriptionaryDAL::GetDescriptionaryGame($gameId);
		}
		public static function AddDescriptionaryGameTurn($gameId, $type, $data, $userId)
		{
			$database = Database::getInstance();
			
			// Check if game exists (and that it hasn't completed)
			$sql =
			"SELECT DISTINCT ID, CurrentTurnNum, MaxNumOfTurns FROM descriptionary_games
			WHERE
				ID = " . $gameId . "
			AND	DateFinished IS NULL;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Could not find a game matching ID ' . $gameId . '. (Or the game may have already completed.)';
			}
			else
			{
				$row = $database->fetch_row($sqlResult);
				$currentTurnNum = $row['CurrentTurnNum'];
				$maxNumOfTurns = $row['MaxNumOfTurns'];
			}
			
			// Get user's assigned turn number
			$sql =
			"SELECT AssignedTurnID
			FROM descriptionary_game_participants
			WHERE
				GameID = " . $gameId . "
			AND UserID = " . $userId . ";";
			$sqlResult = $database->get_result($sql);
			
			// Check if they're a participant
			if($database->num_rows($sqlResult) == 1)
			{
				$row = $database->fetch_row($sqlResult);
				$turnId = $row['AssignedTurnID'];
			}
			else
			{
				return 'ERROR: User ID ' . $userId . ' is not a participant of Game ID ' . $gameId . '.';
			}
			
			// Check if it's their turn
			if($turnId != $currentTurnNum)
			{
				return 'ERROR: User ID ' . $userId . ' (assigned to turn ' . $turnId . ') cannot take turn ' . $currentTurnNum . ' of Game ID ' . $gameId . '.';
			}
			
			// Add new game turn
			$sql = 
			"INSERT INTO descriptionary_game_turns
			(
				GameID,
				TurnID,
				Type,
				Data,
				TakenByUserId,
				DateTaken
			)
			VALUES
			(
				" . $gameId . ",
				" . $turnId . ",
				'" . str_replace("'", "''", $type) . "',
				'" . str_replace("'", "''", $data) . "',
				" . $userId . ", 
				now()
			);";
			$result = $database->get_result($sql);
			
			// Update the game to reflect a turn was taken
			if($maxNumOfTurns >= ($turnId+1))
			{
				$sql = 
				"UPDATE descriptionary_games
				SET
					CurrentTurnNum = " . ($turnId+1) . ",
					DateLastTurnTaken = now()
				WHERE
					ID = " . $gameId . ";";
			}
			else
			{
				// End of game: set date finished. (don't update turn number)
				$sql = 
				"UPDATE descriptionary_games
				SET
					DateLastTurnTaken = now(),
					DateFinished = now()
				WHERE
					ID = " . $gameId . ";";
			}
			$result = $database->get_result($sql);
			
			return DescriptionaryDAL::GetDescriptionaryGameTurn($gameId, $turnId);
		}
		public static function AddDescriptionaryGameParticipant($gameId, $userId)
		{
			$database = Database::getInstance();
			
			// Check if game exists (and that it hasn't completed, and it hasn't filled.)
			$sql =
			"SELECT DISTINCT ID, NumOfUsers, MaxNumOfUsers FROM descriptionary_games
			WHERE
				ID = " . $gameId . "
			AND	DateFinished IS NULL
			AND NumOfUsers < MaxNumOfUsers;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Could not find a game matching ID ' . $gameId . '. (Or the game may have already completed/filled.)';
			}
			else
			{
				$row = $database->fetch_row($sqlResult);
				$numOfUsers = $row['NumOfUsers'];
				$maxNumOfUsers = $row['MaxNumOfUsers'];
			}
		
			// Find the turn number to assign the new participant
			$sql =
			"SELECT AssignedTurnID+1 as NextTurnNum
			FROM descriptionary_game_participants
			WHERE
				GameID = " . $gameId . "
			ORDER BY AssignedTurnID DESC
			LIMIT 1;";
			$sqlResult = $database->get_result($sql);
			if($database->num_rows($sqlResult) == 1)
			{
				$row = $database->fetch_row($sqlResult);
				$turnNum = $row['NextTurnNum'];
			}
			else
			{
				// No participant returned; this is the first participant.
				$turnNum = 1;
			}
			
		
			// Add participant to the list of game participants
			$sql =
			"INSERT INTO descriptionary_game_participants
			(
				GameID,
				UserID,
				AssignedTurnID
			)
			VALUES
			(
				" . $gameId . ",
				" . $userId . ",
				" . $turnNum . "
			);";
			$sqlResult = $database->get_result($sql);
			
			// And update the game to show user joined
			$sql =
			"UPDATE descriptionary_games
			SET
				NumOfUsers = (NumOfUsers+1)
			WHERE
				ID = " . $gameId . ";";
			$sqlResult = $database->get_result($sql);
			
			// See if the game has filled to capacity
			if(($numOfUsers+1) >= $maxNumOfUsers)
			{
				// Mark all outstanding game invites for this game as invalid
				// Set responded to 0 (declined)
				$sql =
				"UPDATE descriptionary_game_invites
				SET
					Response = 0,
					DateResponded = now()
				WHERE
					ID = " . $gameId . "
				AND Response IS NULL;";
				$sqlResult = $database->get_result($sql);
			}
			
			return 'SUCCESS: Added DescriptionaryUser with ID ' . $userId . ' to DescriptionaryGame with ID ' . $gameId . '.';
		}
		public static function AddDescriptionaryGameInvite($gameId, $inviterUserId, $inviteeUserId, $authCode)
		{
			$database = Database::getInstance();
			
			// Check if game exists (and that it hasn't completed or filled)
			$sql =
			"SELECT DISTINCT ID FROM descriptionary_games
			WHERE
				ID = " . $gameId . "
			AND	DateFinished IS NULL
			AND NumOfUsers < MaxNumOfUsers
			LIMIT 1;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 1)
			{
				return 'ERROR: Could not find a game matching ID ' . $gameId . '. (Or the game may have already completed/filled.)';
			}
			
			// Check if users exist
			$sql =
			"SELECT DISTINCT ID FROM descriptionary_users
			WHERE
				ID = " . $inviterUserId . "
			OR	ID = " . $inviteeUserId . ";";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) != 2)
			{
				return 'ERROR: Could not find two users matching ID ' . $inviterUserId . ' and ' . $inviteeUserId . '.';
			}
			
			// Check if user has already been invited to the game
			$sql =
			"SELECT DISTINCT GameID, InviteeUserID
			FROM descriptionary_game_invites
			WHERE
				GameID = " . $gameId . "
			AND	InviteeUserID = " . $inviteeUserId . "
			LIMIT 1;";
			$sqlResult = $database->get_result($sql);
			
			if($database->num_rows($sqlResult) > 0)
			{
				return 'ERROR: User ID ' . $inviteeUserId . ' was already invited to Game ID ' . $inviteeUserId . '.';
			}
			
			// Add invite
			$sql =
			"INSERT INTO descriptionary_game_invites
			(
				GameID,
				InviteeUserID,
				InviterUserID,
				InviteAuthCode,
				DateInvited
			)
			VALUES
			(
				" . $gameId . ",
				" . $inviteeUserId . ",
				" . $inviterUserId . ",
				'" . $authCode . "',
				now()
			);";
			$sqlResult = $database->get_result($sql);
			
			return DescriptionaryDAL::GetDescriptionaryGameInvite($gameId, $inviteeUserId);
		}
	}
?>