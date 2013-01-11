<?
	class DescriptionaryCommentThread
	{
		// MEMBERS
		public $ThreadID;	// int
		public $Comments;	// array(DescriptionaryComment*)
		
		// CONSTRUCTORS
		function __construct($threadId, $comments)
		{
			$this->ThreadID = $threadId;
			$this->Comments = $comments;
		}
		
		// INSTANCE FUNCTIONS
		public function AddComment($message, $madeByUserId)
		{
			$comment  = DescriptionaryDAL::AddCommentToThread($this->ThreadID, $message, $madeByUserId);
			$this->Comments[] = $comment;
			return $comment;
		}
		
		// STATIC FUNCTIONS
		public static function GetThread($threadId)
		{
			return DescriptionaryDAL::GetCommentThread($threadId);
		}
		public static function CreateThread($firstMessage, $madeByUserId)
		{
			return DescriptionaryDAL::CreateNewCommentThread($firstMessage, $madeByUserId);
		}
	}
	class DescriptionaryComment
	{
		// MEMBERS
		public $CommentID;		// int
		public $MadeByUserID; 	// int
		public $Message;		// string
		public $DateMade; 		// datetime string
		
		// CONSTRUCTORS
		function __construct($commentId, $madeByUserId, $message, $dateMade)
		{
			$this->CommentID = $commentId;
			$this->MadeByUserID = $madeByUserId;
			$this->Message = $message;
			$this->DateMade = $dateMade;
		}
		
		// INSTANCE FUNCTIONS
		public function GetAuthoringUser()
		{
			return DescriptionaryUser::GetUser($this->MadeByUserID);
		}
	}
?>