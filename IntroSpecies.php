<?
session_start();
ini_set('display_errors',0);

include("CommonFunctions.php");

if(!isset($_SESSION["speciesDeclared"]))
{
	# this parameter monitors this. This is also unset if the user resets the arrays
	$_SESSION["speciesDeclared"]="True";

	$xmlFile = 'protocolparameters.xml';

	# Overall array for species - an array of arrays of values
	$_SESSION["species"] = array();
	# All parameters applicable for species
	$_SESSION["speciesParamNames"] = array();
	$_SESSION["speciesParamUnits"] = array();
	$_SESSION["speciesParamOptions"] = array();
	$_SESSION["speciesParamType"] = array();
	$_SESSION["speciesParamDescription"] = array();
	$_SESSION["speciesParamDefault"] = array();
	$_SESSION["speciesParamDependency"] = array();
	$_SESSION["speciesDependencyValue"] = array();

	# As a help for adding particles to the species (where we need to know the name and type of species
	# added already) we'll collect these separately too
	$_SESSION["speciesNames"] = array();
	$_SESSION["speciesClass"] = array();


	# READ IN THIS DOCUMENT
	$doc = new DOMDocument();
	$doc->load( $xmlFile );

	$allOutputs = $doc->getElementsByTagName("speciesparam");

	# NOW READ IN EACH OF THE SIMULATOR PARAMETERS, CREATING AN ARRAY FOR EACH
	foreach($allOutputs as $param)
	{
		array_push($_SESSION["speciesParamNames"],getTagValue($param,"name"));
		array_push($_SESSION["speciesParamUnits"],getTagValue($param,"unit"));
		array_push($_SESSION["speciesParamOptions"],getTagValue($param,"options"));
		array_push($_SESSION["speciesParamType"],getTagValue($param,"type"));
		array_push($_SESSION["speciesParamDescription"],getTagValue($param,"description"));
		array_push($_SESSION["speciesParamDefault"],getTagValue($param,"default"));
		array_push($_SESSION["speciesParamDependency"],getTagValue($param,"parameterDependency"));
		array_push($_SESSION["speciesDependencyValue"],getTagValue($param,"dependencyValue"));
	
	}

	$allOutputs = $doc->getElementsByTagName("speciesInitparam");

	# All parameters applicable for species initialisation
	$_SESSION["speciesInit"] = array();
	$_SESSION["speciesInitParamNames"] = array();
	$_SESSION["speciesInitParamUnits"] = array();
	$_SESSION["speciesInitParamOptions"] = array();
	$_SESSION["speciesInitParamType"] = array();
	$_SESSION["speciesInitParamDescription"] = array();
	$_SESSION["speciesInitParamDefault"] = array();
	$_SESSION["speciesInitParamDependency"] = array();
	$_SESSION["speciesInitDependencyValue"] = array();

	foreach($allOutputs as $param)
	{
		if(getTagValue($param,"parameterDependency")=="attachment")
		{
			# We only want to push the relevant parameters into the array. The user earlier chose either onetime or selfattach
			if(getTagValue($param,"dependencyValue")==$_SESSION["attachment"])
			{
				array_push($_SESSION["speciesInitParamNames"],getTagValue($param,"name"));
				array_push($_SESSION["speciesInitParamUnits"],getTagValue($param,"unit"));
				array_push($_SESSION["speciesInitParamOptions"],getTagValue($param,"options"));
				array_push($_SESSION["speciesInitParamType"],getTagValue($param,"type"));
				array_push($_SESSION["speciesInitParamDescription"],getTagValue($param,"description"));
				array_push($_SESSION["speciesInitParamDefault"],getTagValue($param,"default"));
				array_push($_SESSION["speciesInitParamDependency"],getTagValue($param,"parameterDependency"));
				array_push($_SESSION["speciesInitDependencyValue"],getTagValue($param,"dependencyValue"));
			}
		}
	}
}

header('Location: AddSpecies.php') ;

?>

