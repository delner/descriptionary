// JavaScript Document

//Script to show external links
function external() {   
 	if (!document.getElementsByTagName) return;   
 		var anchors = document.getElementsByTagName("a");   
 		for (var i=0; i<anchors.length; i++) {   
   			var anchor = anchors[i];   
   			if (anchor.getAttribute("href") && anchor.getAttribute("rel") == "external")   
     			anchor.target = "_blank";   
 	}   
}   

//Script 
function show_link(tag_link){
	window.location.href = tag_link;
}

function show_link_nw(tag_link){
	window.open(tag_link);
}

//Scripts to show layers in create game
function show_public()
{
	document.getElementById('public_layer').style.display = "block";
	document.getElementById('public_join_layer').style.display = "none";
	document.getElementById('private_layer').style.display = "none";
}

function show_public_join()
{
	document.getElementById('public_layer').style.display = "none";
	document.getElementById('public_join_layer').style.display = "block";
	document.getElementById('private_layer').style.display = "none";
}

function show_private()
{
	document.getElementById('public_layer').style.display = "none";
	document.getElementById('public_join_layer').style.display = "none";
	document.getElementById('private_layer').style.display = "block";
}


//Scripts to show layers in playgame.php
function show_draw_turn(){
	document.getElementById('draw_turn_layer').style.display = "block";
	document.getElementById('guess_phrase_layer').style.display = "none";
	
}

function show_guess_phrase(){
	document.getElementById('guess_phrase_layer').style.display = "block";
	document.getElementById('draw_turn_layer').style.display = "none";
	
}