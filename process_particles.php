<?php

session_start();
ini_set('display_errors',0);

if(isset($_POST['addAnother']))
{
	for($particleParam=0;$particleParam<count($_SESSION['particleParamArrays']);$particleParam++)
	{
		array_push($_SESSION["particles".$_SESSION['particleParamNames'][$particleParam]],($_POST[$_SESSION['particleParamNames'][$particleParam]]));
	}

	header('Location: AddParticles.php') ;
}
else
{
	# NEEDS CHANGING TO WHERE WE'RE GOING AFTER SOLUTES
	header('Location: IntroWorlds.php') ;
	
}


	

