<?
session_start();
ini_set('display_errors',0);

if(!isset($_SESSION["particlesDeclared"]))
{
	# this parameter monitors this. This is also unset if the user resets the arrays
	$_SESSION["particlesDeclared"]="True";

	$xmlFile = 'protocolparameters.xml';

	$_SESSION["particleParamArrays"] = array();
	$_SESSION["particleParamNames"] = array();
	$_SESSION["particleParamUnits"] = array();

	# READ IN THIS DOCUMENT
	$doc = new DOMDocument();
	$doc->load( $xmlFile );

	$allOutputs = $doc->getElementsByTagName("particleDeclarationParam");

	print '<TABLE>';
	# NOW READ IN EACH OF THE SIMULATOR PARAMETERS, CREATING AN ARRAY FOR EACH
	foreach($allOutputs as $param)
	{
		$paramName = $param->getElementsByTagName("name")->item(0)->nodeValue;
		$paramUnit = $param->getElementsByTagName("unit")->item(0)->nodeValue;

		print $paramUnit."<BR>";
		$_SESSION["particles".$paramName] = array();
	
		array_push($_SESSION["particleParamNames"],$paramName);
		array_push($_SESSION["particleParamUnits"],$paramUnit);

		array_push($_SESSION["particleParamArrays"],$_SESSION["particles".$paramName]);
	}

}
header('Location: AddParticles.php') ;

?>

