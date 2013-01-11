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
                    <li class="nav_home" onclick="javascript:show_link('index.php')"></li>
                    <li class="nav_register" onclick="javascript:show_link('desc_register.php')"></li>
                    <li class="nav_help" onclick="javascript:show_link('desc_help.php')"></li> 
                </ul>
            </div> <!-- End Navigation -->
		</div> <!-- End Header -->
	
		<!-- CONTENT BLOCK  -->
		<div id="content">
			<!-- Page Header -->
			<div id="pageheader"><h1></h1></div>
			
			
			<!--Information for the page  -->
			<div id="pageinfo"> <p></p></div>
 
 			<!--   Instruction block  -->
			<div id="instruction_block">    <!-- place stuff in blocks like this  -->

			</div> <!-- end instruction_block --> 
            
            <!--   Examples block  -->
			<div id="example_block">    <!-- place stuff in blocks like this  -->

			</div> <!-- end example_block --> 
            
            
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