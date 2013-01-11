<?
	include_once('../classes/User.php');
	
	class DescriptionaryUser extends User
	{
		// MEMBERS
		public $Settings;							// DescriptionaryUserSettings
		
		// CONSTRUCTORS
			public function __construct() 
		{ 
			$a = func_get_args(); 
			$i = func_num_args(); 
			if (method_exists($this,$f='__construct'.$i))
			{ 
				call_user_func_array(array($this,$f),$a); 
			}
			else
			{
				// Default constructor
				parent::__construct();
				
				if(!is_null($this->id))
				{
					$this->Settings = DescriptionaryDAL::GetDescriptionaryUser($this->id)->Settings;
				}
			}
		}
		function __construct1($settings)
		{
			$this->Settings = $settings;
		}
		
		// INSTANCE FUNCTIONS
		public function GetGames()
		{
			return DescriptionaryDAL::GetDescriptionaryUsersGames($this->id);
		}
		public function GetPreferredGames()
		{
			return DescriptionaryDAL::GetDescriptionaryUsersPreferredGames($this->id);
		}
		public function GetInvites()
		{
			return DescriptionaryDAL::GetDescriptionaryUsersGameInvites($this->id);
		}
		public function SaveUserSettings()
		{
			return DescriptionaryDAL::UpdateDescriptionaryUserSettings($this->id, $this->Settings);
		}
		public function InviteToGame($gameId, $inviterUserId, $authCode)
		{
			return DescriptionaryDAL::AddDescriptionaryGameInvite($gameId, $inviterUserId, $this->id, $authCode);
		}
		
		// STATIC FUNCTIONS
		public static function GetUser($id)
		{
			return DescriptionaryDAL::GetDescriptionaryUser($id);
		}
		public static function GetUserByEmail($email)
		{
			return DescriptionaryDAL::GetDescriptionaryUserByEmail($email);
		}
		public static function AddNewUser($id, $settings)
		{
			return DescriptionaryDAL::AddNewDescriptionaryUser($id, $settings);
		}
	}
	
	class DescriptionaryUserSettings
	{
		// MEMBERS
		public $NotifyOnFriendInvite;				// bool
		public $NotifyOnTurnPrivateGame;			// bool
		public $NotifyOnGameParticipatedComplete;	// bool
		public $NotifyOnGameCreatedComplete;		// bool
		public $NotifyOnCommentInPublicGame;		// bool
		public $NotifyOnCommentInPrivateGame;		// bool
		public $AcceptsInvitations;					// bool
		
		// CONSTRUCTORS
		function __construct(	$notifyOnFriendInvite,
								$notifyOnTurnPrivateGame,
								$notifyOnGameParticipatedComplete,
								$notifyOnGameCreatedComplete,
								$notifyOnCommentInPublicGame,
								$notifyOnCommentInPrivateGame,
								$acceptsInvitations)
		{
			$this->NotifyOnFriendInvite = $notifyOnFriendInvite;
			$this->NotifyOnTurnPrivateGame = $notifyOnTurnPrivateGame;
			$this->NotifyOnGameParticipatedComplete = $notifyOnGameParticipatedComplete;
			$this->NotifyOnGameCreatedComplete = $notifyOnGameCreatedComplete;
			$this->NotifyOnCommentInPublicGame = $notifyOnCommentInPublicGame;
			$this->NotifyOnCommentInPrivateGame = $notifyOnCommentInPrivateGame;
			$this->AcceptsInvitations = $acceptsInvitations;
		}
	}
?>

