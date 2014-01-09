<?
session_start();
ini_set('display_errors',0);

include("CommonFunctions.php");

if(!isset($_SESSION["worldsDeclared"]))
{
	# this parameter monitors this. This is also unset if the user resets the arrays
	$_SESSION["worldsDeclared"]="True";

	# ARRAY OF ARRAYS - BULK AND COMPUTATION DOMAIN DATA ENTERED BY THE USER
	$_SESSION["bulks"] = array();

	# Useful to keep names of bulks separately - great for boundary conditions later
	$_SESSION["bulkNames"] = array();
	$_SESSION["computationDomains"] = array();

	# BULK PARAMETER INFORMATION
	$_SESSION["bulkParamNames"] = array();
	$_SESSION["bulkParamDescription"] = array();
	$_SESSION["bulkParamDefaults"] = array();
	$_SESSION["bulkParamType"] = array();
	$_SESSION["bulkParamOptions"] = array();
	$_SESSION["bulkParamUnit"] = array();

	# BULK SOLUTE PARAMETER INFORMATION
	$_SESSION["bulkSoluteParamNames"] = array();
	$_SESSION["bulkSoluteParamDescription"] = array();
	$_SESSION["bulkSoluteParamDefaults"] = array();
	$_SESSION["bulkSoluteParamType"] = array();
	$_SESSION["bulkSoluteParamOptions"] = array();
	$_SESSION["bulkSoluteParamUnit"] = array();


	# COMPUTATION DOMAIN PARAMETER INFORMATION
	$_SESSION["computationDomainParamNames"] = array();
	$_SESSION["computationDomainParamDefaults"] = array();
	$_SESSION["computationDomainParamDescription"] = array();
	$_SESSION["computationDomainParamType"] = array();
	$_SESSION["computationDomainParamOptions"] = array();
	$_SESSION["computationDomainParamUnit"] = array();

	# COMPUTATION DOMAIN BOUNDARY CONDITION PARAMETERS
	$_SESSION["boundaries"]=array();
	array_push($_SESSION["boundaries"],"y0z");
	array_push($_SESSION["boundaries"],"yNz");
	array_push($_SESSION["boundaries"],"x0z");
	array_push($_SESSION["boundaries"],"xNz");
	array_push($_SESSION["boundaries"],"x0y");
	array_push($_SESSION["boundaries"],"xNy");

	$_SESSION["boundaryClasses"]=array();

	# NOW THE PARAMETERS FOR EACH BOUNDARY
	$_SESSION["boundaryShapes"]=array();

	# Now push all the other parameter details in - firstly for bulks

	$xmlFile = 'protocolparameters.xml';
	$doc = new DOMDocument();
	$doc->load( $xmlFile );
	$allOutputs = $doc->getElementsByTagName("bulkparam");

	foreach($allOutputs as $param)
	{
		array_push($_SESSION["bulkParamNames"],getTagValue($param,"name"));
		array_push($_SESSION["bulkParamDescription"],getTagValue($param,"description"));
		array_push($_SESSION["bulkParamDefaults"],getTagValue($param,"default"));
		array_push($_SESSION["bulkParamType"],getTagValue($param,"type"));
		array_push($_SESSION["bulkParamOptions"],getTagValue($param,"options"));
		array_push($_SESSION["bulkParamUnit"],getTagValue($param,"unit"));

	}

	# Now for the bulk, there are solute parameters. Read these in, so these can be referred to on the input screen
	$allOutputs = $doc->getElementsByTagName("bulksoluteparam");

	foreach($allOutputs as $param)
	{
		array_push($_SESSION["bulkSoluteParamNames"],getTagValue($param,"name"));
		array_push($_SESSION["bulkSoluteParamDescription"],getTagValue($param,"description"));
		array_push($_SESSION["bulkSoluteParamDefaults"],getTagValue($param,"default"));
		array_push($_SESSION["bulkSoluteParamType"],getTagValue($param,"type"));
		array_push($_SESSION["bulkSoluteParamOptions"],getTagValue($param,"options"));
		array_push($_SESSION["bulkSoluteParamUnit"],getTagValue($param,"unit"));
	}

	# Now the same for computation domains
	$allOutputs = $doc->getElementsByTagName("computationDomainParam");

	foreach($allOutputs as $param)
	{
		array_push($_SESSION["computationDomainParamNames"],getTagValue($param,"name"));
		array_push($_SESSION["computationDomainParamDefaults"],getTagValue($param,"default"));
		array_push($_SESSION["computationDomainParamDescription"],getTagValue($param,"description"));
		array_push($_SESSION["computationDomainParamType"],getTagValue($param,"type"));
		array_push($_SESSION["computationDomainParamOptions"],getTagValue($param,"options"));
		array_push($_SESSION["computationDomainParamUnit"],getTagValue($param,"unit"));

	}

	# Now get the range of possible boundary condition classes
	$allOutputs = $doc->getElementsByTagName("computationDomainBoundaryClasses");

	# The boundary classes are specified as a string - we can break this down using a string tokenizer.
	# Do this and store each condition
	foreach($allOutputs as $param)
	{
		$options = strtok(getTagValue($param,"options"), ",");
		while ($options !== false) 
		{
			array_push($_SESSION["boundaryClasses"],$options);
			$options = strtok(",");	
		}
	}

	# Now the boundary parameters
	$allOutputs = $doc->getElementsByTagName("computationDomainBoundaryParam");
	foreach($allOutputs as $param)
	{
		$options = strtok(getTagValue($param,"options"), ",");
		while ($options !== false) 
		{
			array_push($_SESSION["boundaryShapes"],$options);
			$options = strtok(",");	
		}
	}	

	# Parameter for flag for error message on domain name
	$_SESSION["chemoNameMessage"]="False";
}

# Now relocate to the screen to add worlds to the domain
header('Location: AddWorlds.php') ;

?>
