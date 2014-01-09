<?php
ini_set('display_errors',0);
session_start();

include("CommonFunctions.php");

print '<html>
<head>
<script></script></head>';

##################################################################################################################
############################ PRINT THE MENU ######################################################################
##################################################################################################################

include("components/interfaceSettings.php");
include("components/menu.php");

PrintHeader("Simulation Experiment Specification",3);


################################################# START OF PHP PROTOCOL FILE MAKER SCRIPT ########################
## XMLFILE - THE FILE CONTAINING INFORMATION ON IDYNOMICS PARAMETERS
$xmlFile = 'protocolparameters.xml';

# READ IN THIS DOCUMENT
$doc = new DOMDocument();
$doc->load( $xmlFile );

print "<H2 ALIGN='LEFT'>Defining Particles</H2>";

print "<p align='left'>The particle mark-ups refer to the different compartments used to describe agent species (which is done later in the protocol file). When defining a species, one has to list which of the possible compartments are used by that species. At this level of the protocol file, the compartments are defined independently of any species and described by their name and density. These three particle types are very common (active biomass, inert biomass, and capsular compounds, respectively), and most simulations employ all three.<b>However, the definition of one type of biomass is sufficient</b><br></p>";

################################################################################################################
################################################################################################################
################################### PARTICLE DISPLAY SECTION ###################################################
################################################################################################################
################################################################################################################


if(count($_SESSION["particlesname"])>0)
{
	print '<div id="displayTable">';
	print '<H2 align="left">Particles Declared:</H2>';

	print '<TABLE align="left" width=30%><TR>';

	$h=0;
	while($h<count($_SESSION['particleParamArrays']))
	{
		print '<TD  align="center"><b>'.$_SESSION["particleParamNames"][$h].'</b></TD>';
		$h=$h+1;
	}

	print '</TR><TR>';

	$i=0;

	while($i<count($_SESSION['particles'.$_SESSION['particleParamNames'][0]]))
	{
		$h=0;
		while($h<count($_SESSION['particleParamNames']))
		{
			print "<TD  align='center'>".$_SESSION['particles'.$_SESSION["particleParamNames"][$h]][$i]."</TD>";
		

			$h=$h+1;
		}
		print "<TR>";
		$i=$i+1;
	}


	print '</TABLE></div>';

	# Reset Button
	print '<form method="post" action="ResetInput.php">
	<INPUT type="submit" value="Reset Particles" name="Reset">
	</form>';

	# Add a spacer to make page look better
	print '<div id="spacer"></div>';
}
 
################################################################################################################
################################################################################################################
################################### PARTICLE DECLARATION SECTION ###############################################
################################################################################################################
################################################################################################################

# SET THE FORM UP THAT THE USER WILL COMPLETE
print '<form name="Particles" action="process_particles.php" method="post">';

$allOutputs = $doc->getElementsByTagName("particleDeclarationParam");

print "<hr><p align='left'><b>Complete the details for the Particle and press 'Add Particle'. You must add all Particles before pressing the 'Next Step' button</b></p>";

print '<div id="displayTable">';
print '<TABLE WIDTH=70% ALIGN="LEFT">';
# NOW READ IN EACH OF THE SIMULATOR PARAMETERS, CREATING AN ENTRY FOR EACH
foreach($allOutputs as $param)
{
	$paramName = getTagValue($param,"name");
	$paramDescription = getTagValue($param,"description");
	$paramDefault = getTagValue($param,"default");
	$paramType = getTagValue($param,"type");
	$paramOptions = getTagValue($param,"options");
	$paramUnit = getTagValue($param,"unit");

	if($paramName!="name")
	{

		# IF ITS A BOOLEAN PARAMETER, WE'LL USE A CHECKBOX
		if($paramType=="boolean")
		{
			# NOW FOR THE ADAPTIVE TIMESTEP PARAMETER, WE'LL USE EXTRA PART OF THE CHECKBOX WHICH TURNS TIMESTEPINI AND TIMESTEPMAX ON AND OFF DEPENDENT
			# ON WHETHER THE BOX IS SELECTED
			print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=13%><input type='checkbox' id='".$paramName."' name='".$paramName."' value='1'></TD></TR>";

		}
		# NOT A BOOLEAN PARAMETER:
		else
		{
			print "<TR><TD WIDTH=13% >".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=13%><input type='text' size='5' id='".$paramName."' name='".$paramName."' value='".$paramDefault."'></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
		}
	}
	else
	{
		# Particles can only be biomass, inert, or capsule - as defined in the settings file. Thus give the options as stated there.
		print "<TR><TD WIDTH=13% >".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=13%><select name='".$paramName."' SIZE=5>";

		$options = strtok($paramOptions, ",");
		
		while ($options !== false) 
		{
			print "<option value=\"$options\">$options</option>";
			$options = strtok(",");	
		}
		print "</select></TD></TR>";
		
	}
	
}

print '</TABLE></div><hr><input type="submit" name="addAnother" value="Add Particle"><input type="submit" value="Next Step: Declare Worlds"></p>';

?>
