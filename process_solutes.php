<?php

session_start();
ini_set('display_errors',0);

if(isset($_POST['addAnother']))
{
	$xmlFile = 'protocolparameters.xml';
	$doc = new DOMDocument();
	$doc->load( $xmlFile );

	array_push($_SESSION["soluteName"],($_POST[$_SESSION["soluteDecParams"][0]]));
	array_push($_SESSION["soluteDiffusivities"],($_POST[$_SESSION["soluteDecParams"][1]]));
	
	header('Location: AddSolutes.php') ;
}
else
{
	# NEEDS CHANGING TO WHERE WE'RE GOING AFTER SOLUTES
	header('Location: IntroParticles.php') ;
	
}


	

