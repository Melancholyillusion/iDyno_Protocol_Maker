<?
session_start();
ini_set('display_errors',0);

# Only initialise all the reactions if these have not been set already. The user may have used the back button to come here
if(!isset($_SESSION["reactionsDeclared"]))
{
	# this parameter monitors this. This is also unset if the user resets the arrays
	$_SESSION["reactionsDeclared"]="True";

	include("CommonFunctions.php");

	$xmlFile = 'protocolparameters.xml';

	$_SESSION["reactions"] = array();
	$_SESSION["reactionParamNames"] = array();
	$_SESSION["reactionParamUnits"] = array();
	$_SESSION["reactionParamOptions"] = array();
	$_SESSION["reactionParamType"] = array();
	$_SESSION["reactionParamDescription"] = array();
	$_SESSION["reactionParamDefault"] = array();
	$_SESSION["namesOfSpecifiedReactions"] = array();


	# READ IN THIS DOCUMENT
	$doc = new DOMDocument();
	$doc->load( $xmlFile );

	$allOutputs = $doc->getElementsByTagName("reactionParam");

	print '<TABLE>';
	# NOW READ IN EACH OF THE SIMULATOR PARAMETERS, CREATING AN ARRAY FOR EACH
	foreach($allOutputs as $param)
	{
		array_push($_SESSION["reactionParamNames"],getTagValue($param,"name"));
		array_push($_SESSION["reactionParamUnits"],getTagValue($param,"unit"));
		array_push($_SESSION["reactionParamOptions"],getTagValue($param,"options"));
		array_push($_SESSION["reactionParamType"],getTagValue($param,"type"));
		array_push($_SESSION["reactionParamDescription"],getTagValue($param,"description"));
		array_push($_SESSION["reactionParamDefault"],getTagValue($param,"default"));

	
		#$_SESSION["reaction".$paramName] = array();
	
	

		#array_push($_SESSION["reactionParamArrays"],$_SESSION["reaction".$paramName]);
	}

	# Now we're also going to have an array for production or consumption of each of the solutes and particles
	# This makes processing much easier later on.
	$h=0;
	while($h<count($_SESSION['soluteName']))
	{
		$_SESSION["reaction".$_SESSION['soluteName'][$h]] = array();
		$h=$h+1;
	}

	$i=0;
	while($i<count($_SESSION['particles'.$_SESSION['particleParamNames'][0]]))
	{
		$_SESSION["reaction".$_SESSION['particles'.$_SESSION["particleParamNames"][0]][$i]] = array();
		$i=$i+1;
	}

	# Array for kinetic factor types
	$_SESSION["kineticFactors"] = array();

	# Populate this array from the string in the XML file
	$allOutputs = $doc->getElementsByTagName("kineticFactorParam");
	foreach($allOutputs as $param)
	{
		$options = strtok(getTagValue($param,"options"), ",");
		while ($options !== false) 
		{
			array_push($_SESSION["kineticFactors"],$options);
			$options = strtok(",");	
		}
	}	
}

header('Location: AddReaction.php') ;
?>

