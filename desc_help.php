<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Descriptionary ONLINE</title>
	<link href="css/default.css" type="text/css" rel="stylesheet" />
	<link href="css/footerstyle.css" type="text/css" rel="stylesheet" />
	<link href="css/instructionstyle.css" type="text/css" rel="stylesheet" />
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
			<div id="pageheader"><h1>Help Page</h1></div>
			
			
			<!--Information for the page  -->
			<div id="pageinfo"> <p>Look here for information on how to play the game and any problems you may have.</p></div>
 
 			<!--   Instruction block  -->
			<div id="instruction_block">    <!-- place stuff in blocks like this  -->
				<div id="login_help">
                	<div id="login_caption">First, you need to register with Dave, ETC at <a href="www.daveetc.com//login/index.php" >Dave, ETC</a><br/>Then, go to the decriptionary login page <a href="desc_login.php">here</a></div>
                    <div id="login_pic"><img alt="Login Page" src="images/loginPage.JPG" width="400" height="200" /></div>
                </div><!-- end login help -->
                
                <!--   Examples block  -->
                <div id="example_block">    <!-- place stuff in blocks like this  -->
    				<div id="example_caption">
Once you create an account, you will come to your dashboard and chose what to play.
                        You can choose to either join a public game, which you will be thrown into randomly, or you can create a Private game.
                       
                            In a Private, you can choose the players you want to play.
                        
                        Once you are on the play screen, you will be faced with either a phrase or a picture.
                        To play, you must provide the next turn.
                         Io enter a phrase, you enter text in the box provided.
                         To enter a picture, you must use the drawing tool box with the color of your choice and draw
                            when done drawing, you press the save button
                        When the game is completed, you will receive an email and will be able to view all the guesses
                        
                    
                    </div>
                    <div id="example_pic1"><img alt="view game" src="images/view_game.jpg" width="433" height="400" /></div>
                </div> <!-- end example_block -->       
			</div> <!-- end instruction_block --> 
            
 
            
            
			<!--   Help block  -->
			<div id="helpblock">    <!-- place stuff in blocks like this  -->

			</div> <!-- end helpblock --> 
    
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