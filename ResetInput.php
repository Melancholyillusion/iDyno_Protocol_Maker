<?php
ini_set('display_errors',0);
session_start();

# Unset the variable
if($_POST["Reset"]=="Reset Solutes")
{
	unset($_SESSION["solutesDeclared"]);
	header('Location: IntroSolutes.php') ;
}
else if($_POST["Reset"]=="Reset Particles")
{
	unset($_SESSION["particlesDeclared"]);
	header('Location: IntroParticles.php') ;
}
else if($_POST["Reset"]=="Reset Worlds")
{
	unset($_SESSION["worldsDeclared"]);
	header('Location: IntroWorlds.php') ;	
}
else if($_POST["Reset"]=="Reset Reactions")
{
	unset($_SESSION["reactionsDeclared"]);
	header('Location: IntroReactions.php') ;
}
else if($_POST["Reset"]=="Reset Species")
{
	unset($_SESSION["speciesDeclared"]);
	header('Location: IntroSpecies.php') ;
}


?>

