<?
	ob_start();
	session_start();
	include_once('includes/user.php');
	include_once('includes/comments.php');
	include_once('includes/game.php');
	include_once('includes/dal.php');
	include_once('../classes/FormElements.php');
	$curr_user = new DescriptionaryUser();
	// is user logged in already?
	if($curr_user->isLoggedIn)
	{
		$desc_user = DescriptionaryUser::GetUser($curr_user->id);

  		//if is not a valid desc user
		if(!($desc_user instanceof DescriptionaryUser))
		{
			// echo 'User is logged in, but not valid desc user';
			echo '<html><head><meta http-equiv="refresh" content="0;url=desc_setupmyprofile.php"></head></html>';
			die;
		}
	}
	else  // if no, direct to  descrip login page
	{ 	
		// echo 'The user is not logged in. Login first';
		// echo 'User is logged in but not valid desc user';
		echo "<html><head><meta http-equiv=\"refresh\" content=\"0;url=desc_login.php\"></head></html>";
		die;
	}
?>