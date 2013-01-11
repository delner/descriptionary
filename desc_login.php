<?php
include_once('../classes/Login.php');
include_once('../classes/FormElements.php');

if(isset($_POST['loginsubmit']))
{
	$elements = new FormElements($_POST);
	$POST = $elements->getElements();

	$login = new Login();
	try
	{
		$login->validateUserCredentials($POST['email'], $POST['password'], 'rememberme');

		echo '<html><head><meta http-equiv="refresh" content="0;url=index.php"></head></html>';
		die();
	}
	catch(Exception $e)
	{
		$error = $e->getMessage();
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

	<link href="css/loginstyle.css" type="text/css" rel="stylesheet" />

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

                   <!-- <li class="nav_home" onclick="javascript:show_link('index.php')"></li> -->

                    <li class="nav_login" onclick="javascript:show_link('desc_login.php')"></li>

                    <li class="nav_help" onclick="javascript:show_link('desc_help.php')"></li> 

                </ul>

            </div> <!-- End Navigation -->

		</div> <!-- End Header -->

	

		<!-- CONTENT BLOCK  -->

		<div id="content">

			<!-- Page Header -->

			<div id="pageheader"><h1>Log In page</h1></div>
			

			<!--Information for the page  -->

			<div id="pageinfo"> <p>Descriptionary ONLINE is a game in which people can play a combination 

			of the games pictionary and broken telephone.  Players take turns drawing pictures or guessing the 

			phrase associated with the picture. </p></div>
			
			

     

			<!--  Log In block  -->

			<div id="loginblock">

				<p>

					<h2>Log In</h2>

					<form method="post" onsubmit="desc_login.php">

					<div id="form_container">
					
					
					<div class="field">

						<div style="color: red;"><? echo $error; ?></div>

					</div>


					<div class="field">

						<label>Alias or Email Address:</label>

						<input type="text" name="email" />

					</div>

    

					<div class="field">

						<label>Password:</label>

						<input type="password" name="password" />

					</div>

					<!--
					<div class="field">
						<label>Remember Me:</label>
						<input type="checkbox" name="rememberme" value="rememberme" />
					</div>
					-->



					<div id="submit">

						<input type="submit" name = 'loginsubmit' value ='Log Me In' class='button'/>

					</div> 

					</div><!-- end form_container -->

					</form>

				</p>

			</div> <!-- end loginblock -->  



	  

			<!-- Not Registered  -->

			<div id="registrationlink">

				<h2>Register</h2>

				<p>Not registered? Setup a DaveETC account to get started! <a href="../../login/index.php" >Register New User</a></p>

			</div>

    

		</div><!-- end content  -->



		<div id="chalkboardseparator">

	

		</div>

	



   

	<!--  FOOTER  -->

	<div id="footer">

	<?php include "../includes/footer.php"; ?>

	</div>

	<!-- end footer -->



    </div><!-- end Main Container -->



</body>

</html>