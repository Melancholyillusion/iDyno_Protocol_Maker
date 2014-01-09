<?
session_start();
ini_set('display_errors',0);

if(!isset($_SESSION["gridDeclared"]))
{
	# this parameter monitors this. This is also unset if the user resets the arrays
	$_SESSION["gridDeclared"]="True";

	include("CommonFunctions.php");

	$_SESSION["agentGrid"] = array();
	$_SESSION["agentGridParams"] = array();
	$_SESSION["agentGridDescription"] = array();
	$_SESSION["agentGridDefaults"] = array();
	$_SESSION["agentGridType"] = array();
	$_SESSION["agentGridOptions"] = array();
	$_SESSION["agentGridUnit"] = array();

	$xmlFile = 'protocolparameters.xml';
	$doc = new DOMDocument();
	$doc->load( $xmlFile );
	$allOutputs = $doc->getElementsByTagName("agentGridParam");

	foreach($allOutputs as $param)
	{
		array_push($_SESSION["agentGridParams"],getTagValue($param,"name"));
		array_push($_SESSION["agentGridDescription"],getTagValue($param,"description"));
		array_push($_SESSION["agentGridDefaults"],getTagValue($param,"default"));
		array_push($_SESSION["agentGridType"],getTagValue($param,"type"));
		array_push($_SESSION["agentGridOptions"],getTagValue($param,"options"));
		array_push($_SESSION["agentGridUnit"],getTagValue($param,"unit"));

	}
}

header('Location: Define_AgentGrid.php') ;

?>

