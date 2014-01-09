<?php

session_start();
ini_set('display_errors',0);

include("CommonFunctions.php");

$xmlFile = 'protocolparameters.xml';
$doc = new DOMDocument();
$doc->load( $xmlFile );

$xmlProtocolFile = new SimpleXMLElement('<xml/>');

$idynomics = $xmlProtocolFile->addChild('idynomics');

################################################################################################################
#### A: SIMULATOR SECTION OF PROTOCOL FILE
#$simulator = $idynomics->addChild('simulator');
$allOutputs = $doc->getElementsByTagName("simulatorparam");

foreach($allOutputs as $param)
{
	$paramName = getTagValue($param,"name");
	$paramType = getTagValue($param,"type");

	if($paramType=="boolean")
	{
		if(isset($_POST[$paramName]))
		{
			$_SESSION[$paramName] = "true";
		}
		else
		{
			$_SESSION[$paramName] = "false";
		}
	}
	else
	{
		# Use isset for a text field as there are some fields on the form which are disabled - this stops the notice popping up
		# with potential error
		if(isset($_POST[$paramName]))
		{
			$_SESSION[$paramName] = $_POST[$paramName];
		}
		else
		{
			$_SESSION[$paramName] = 0;
		}
	}

	# Useful to store whether this is a chemostat scenario now, as this makes things easier yet
	if($paramName=="chemostat")
	{
		if(isset($_POST[$paramName]))
		{
			$_SESSION["chemostat_sim"] = "True";
		}
		else
		{
			$_SESSION["chemostat_sim"] = "False";
		}
	}

}

####################################################################################################################################################
####################################################################################################################################################
###### B: INPUT SECTION

$inputSection = $doc->getElementsByTagName("inputparam");

foreach($inputSection as $param)
{
	$paramName = getTagValue($param,"name");
	$paramType = getTagValue($param,"type");

	if($paramName=="useAgentFile" || $paramName=="useBulkFile")
	{
		if(isset($_POST[$paramName]))
		{
			$_SESSION[$paramName] = "true";
		}
		else
		{
			$_SESSION[$paramName] = "false";
		}
	}
	else
	{
		if(isset($_POST[$paramName]))
		{
			$_SESSION[$paramName]=$_POST[$paramName];
		}
		else
		{
			# Push the empty string in this case
			$_SESSION[$paramName]="";
		}
	}

}	

# Redirect to the next pages of the interface form
header( 'Location: IntroSolutes.php' );

?>
