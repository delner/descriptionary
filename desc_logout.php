<?php
include_once('../classes/Login.php');
  $login = new Login();
  $login->logout();
  
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Descriptionary ONLINE</title>
	<link href="css/default.css" type="text/css" rel="stylesheet" />
	<link href="css/footerstyle.css" type="text/css" rel="stylesheet" />
	<link href="css/logoutstyle.css" type="text/css" rel="stylesheet" />
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
                    <li class="nav_login" onclick="javascript:show_link('desc_login.php')"></li>
                    <li class="nav_help" onclick="javascript:show_link('desc_help.php')"></li> 
                </ul>
            </div> <!-- End Navigation -->
		</div> <!-- End Header -->
	
		<!-- CONTENT BLOCK  -->
		<div id="content">
			<!-- Page Header -->
			<div id="pageheader"><h1>Logged Out</h1></div>
			
			
			<!--Information for the page  -->
			<div id="pageinfo"> <p>You have successfully logged out of Descritptionary ONLINE.  We hope that you had fun and will be back soon.</p></div>
 
 			<!--   PHOTO block  -->
			<div id="photo_block">    <!-- place stuff in blocks like this  -->

			</div> <!-- end photo_block --> 
    
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