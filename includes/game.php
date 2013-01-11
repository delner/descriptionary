<?
	class DescriptionaryGame
	{
		// MEMBERS
		public $GameID;				// int
		public $CreatorID;			// int
		public $Settings;			// DescriptionaryGameSettings
		public $Status;				// DescriptionaryGameStatus
		public $CommentThreadID;	// int
		public $DateCreated;		// datetime string
		
		// CONSTRUCTORS
		function __construct($gameId, $creatorId, $settings, $status, $commentThreadID, $dateCreated)
		{
			$this->GameID = $gameId;
			$this->CreatorID = $creatorId;
			$this->Settings = $settings;
			$this->Status = $status;
			$this->CommentThreadID = $commentThreadID;
			$this->DateCreated = $dateCreated;
		}
		
		// INSTANCE FUNCTIONS
		public function GetCreatorUser()
		{
			return DescriptionaryUser::GetUser($this->CreatorID);
		}
		public function GetInvites()
		{
			return DescriptionaryDAL::GetDescriptionaryGameInvites($this->GameID);
		}
		public function GetInvitedUsers()
		{
			return DescriptionaryDAL::GetDescriptionaryGameInvitees($this->GameID);
		}
		public function GetParticipantUsers()
		{
			return DescriptionaryDAL::GetDescriptionaryGameParticipants($this->GameID);
		}
		public function GetGameTurns()
		{
			return DescriptionaryDAL::GetDescriptionaryGameTurns($this->GameID);
		}
		public function GetTurnNumOfUser($userId)
		{
			return DescriptionaryDAL::GetUsersTurnNum($this->GameID, $userId);
		}
		public function GetDiscussion()
		{
			if(!is_null($this->CommentThreadID))
			{
				return DescriptionaryDAL::GetCommentThread($this->CommentThreadID);
			}
			else
			{
				return new DescriptionaryCommentThread(null, array());
			}
		}
		
		public function IsUserParticipant($userId)
		{
			$participants = $this->GetParticipantUsers();
			if(is_array($participants))
			{
				foreach($participants as $participant)
				{
					if($participant->id == $userId)
					{
						return true;
					}
				}
				return false;
			}
			else
			{
				// Error
				return $participants;
			}
		}
		public function AddParticipant($userId)
		{
			return DescriptionaryDAL::AddDescriptionaryGameParticipant($this->GameID, $userId);
		}
		public function AddComment($message, $madeByUserId)
		{
			// Check if discussion has been started
			if(!is_null($this->CommentThreadID))
			{
				// Add to existing discussion thread
				$commentThread = DescriptionaryCommentThread::GetThread($this->CommentThreadID);
				return $commentThread->AddComment($message, $madeByUserId);
			}
			else
			{
				// It hasn't
				// Create new discussion thread
				$commentThread = DescriptionaryCommentThread::CreateThread($message, $madeByUserId);
				if($commentThread instanceof DescriptionaryCommentThread)
				{
					$this->CommentThreadID = $commentThread->ThreadID;
					DescriptionaryDAL::SetDescriptionaryGameCommentThread($this->GameID, $this->CommentThreadID);
					
					return $commentThread->Comments[0];
				}
				else
				{
					// Failure
					return $commentThread;
				}
			}
			
			return;
		}
		public function TakeTurn($type, $data, $userId)
		{
			return DescriptionaryDAL::AddDescriptionaryGameTurn($this->GameID, $type, $data, $userId);
		}
		public function GetMostRecentTurn()
		{
			$turns = $this->GetGameTurns();
			
			if(is_array($turns))
			{
				// Turns are already ordered: select the last one.
				return $turns[(count($turns)-1)];
			}
			else
			{
				// Error
				return $turns;
			}
		}
		
		// STATIC FUNCTIONS
		public static function GetGame($id)
		{
			return DescriptionaryDAL::GetDescriptionaryGame($id);
		}
		public static function CreateGame($gameSettings, $creatorUserId)
		{
			return DescriptionaryDAL::AddDescriptionaryGame($creatorUserId, $gameSettings);
		}
		public static function GetActiveOpenPublicGames()
		{
			return DescriptionaryDAL::GetActiveOpenPublicDescriptionaryGames();
		}
	}
	
	class DescriptionaryGameSettings
	{
		// MEMBERS
		public $IsPrivate;		// bool
		public $MaxNumOfUsers;	// int
		public $MaxNumOfTurns;	// int
		
		// CONSTRUCTORS
		function __construct($isPrivate, $maxNumOfUsersAndTurns)
		{
			$this->IsPrivate = $isPrivate;
			$this->MaxNumOfUsers = $maxNumOfUsersAndTurns;
			$this->MaxNumOfTurns = $maxNumOfUsersAndTurns;
		}
	}
	
	class DescriptionaryGameStatus
	{
		// MEMBERS
		public $NumOfUsers;			// int
		public $CurrentTurnNum;		// int
		public $DateLastTurnTaken;	// datetime string
		public $DateFinished;		// datetime string
		
		// CONSTRUCTORS
		function __construct($numOfUsers, $currentTurnNum, $dateLastTurnTaken, $dateFinished)
		{
			$this->NumOfUsers = $numOfUsers;
			$this->CurrentTurnNum = $currentTurnNum;
			$this->DateLastTurnTaken = $dateLastTurnTaken;
			$this->DateFinished = $dateFinished;
		}
	}
	
	class DescriptionaryGameTurn
	{
		// MEMBERS
		public $TurnID;			// int
		public $Type;			// string
		public $Data;			// string
		public $TakenByUserID;	// int
		public $DateTaken;		// datetime string
		
		// CONSTRUCTORS
		function __construct($turnID, $type, $data, $takenByUserID, $dateTaken)
		{
			$this->TurnID = $turnID;
			$this->Type = $type;
			$this->Data = $data;
			$this->TakenByUserID = $takenByUserID;
			$this->DateTaken = $dateTaken;
		}		
	}
	
	class DescriptionaryGameInvite
	{
		// MEMBERS
		public $GameID;			// int
		public $InviteeUserID;	// int
		public $InviterUserID;	// int
		public $InviteAuthCode; // string
		public $DateInvited;	// datetime string
		public $Accepted;		// bool?
		public $DateAccepted;	// datetime string
		
		// CONSTRUCTORS
		function __construct($gameID, $inviteeUserID, $inviterUserID, $inviteAuthCode, $dateInvited, $accepted, $dateAccepted)
		{
			$this->GameID = $gameID;
			$this->InviteeUserID = $inviteeUserID;
			$this->InviterUserID = $inviterUserID;
			$this->InviteAuthCode = $inviteAuthCode;
			$this->DateInvited = $dateInvited;
			$this->Accepted = $accepted;
			$this->DateAccepted = $dateAccepted;
		}
		
		// FUNCTIONS
		public function Respond($response, $authCode)
		{
			if(is_null($this->Accepted))
			{
				if($response == 1 || $response == 0)
				{
					$output = DescriptionaryDAL::RespondToDescriptionaryGameInvite($this->GameID, $this->InviteeUserID, $authCode, $response);
					$this->Accepted = $response;
					$this->DateAccepted = getdate();
				}
				else
				{
					return 'ERROR: Cannot respond to invite; invalid response ' . response . ' (Must be 0 or 1).';
				}
			}
			else
			{
				return 'ERROR: Cannot respond to invite; already gave response ' . $this->Accepted . '.';
			}
			
			return $output;
		}
		
		// STATIC FUNCTIONS
		public static function GetInvite($gameId, $inviteeId)
		{
			return DescriptionaryDAL::GetDescriptionaryGameInvite($gameId, $inviteeId);
		}
	}
?>