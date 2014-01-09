<?
session_start();
ini_set('display_errors',0);

if(!isset($_SESSION["solutesDeclared"]))
{
	# this parameter monitors this. This is also unset if the user resets the arrays
	$_SESSION["solutesDeclared"]="True";

	$_SESSION["soluteName"] = array();
	$_SESSION["soluteDiffusivities"] = array();
	$_SESSION["soluteDomain"] = array();
	$_SESSION["soluteDecParams"] = array();
}
header('Location: AddSolutes.php') ;

?>

