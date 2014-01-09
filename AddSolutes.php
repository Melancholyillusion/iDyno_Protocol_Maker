<?php
ini_set('display_errors',0);
session_start();

include("CommonFunctions.php");

print '<html>
<head>
<script>

</script></head>';

##################################################################################################################
############################ PRINT THE MENU ######################################################################
##################################################################################################################

include("components/interfaceSettings.php");
include("components/menu.php");

PrintHeader("Simulation Experiment Specification",2);


################################################# START OF PHP PROTOCOL FILE MAKER SCRIPT ########################
## XMLFILE - THE FILE CONTAINING INFORMATION ON IDYNOMICS PARAMETERS
$xmlFile = 'protocolparameters.xml';

# READ IN THIS DOCUMENT
$doc = new DOMDocument();
$doc->load( $xmlFile );


print "<H2 ALIGN='LEFT'>Defining Solutes</H2>";

print "<p align='left'>The solute mark-ups list and describe all the solute compounds used by the model, along with their molecular diffusivity in water. At this point we do not specify concentration information; that information will be specified in the section for bulk compartments. The <i>pressure</i> solute is not actually a physical solute, but is used in computing biomass spreading effects, and should be declared here for biofilm simulations. However, during a chemostat run any solutes not involved in the reactions in which the agents participate must not be listed<br></p>";


################################################################################################################
################################################################################################################
################################### SOLUTES DISPLAY SECTION ####################################################
################################################################################################################
################################################################################################################

if(count($_SESSION['soluteName'])>0)
{
	print '<div id="displayTable">';
	print '<H2 align="left">Solutes Declared:</H2>';
	print '<TABLE align="left" width=30%><TR><TD align="center"><b>Name</b></TD><TD align="center"><b>Diffusivity</b></TD></TR>';

	$h=0;
	while($h<count($_SESSION['soluteName']))
	{
		print "<TR><TD WIDTH=50% align='center'>".$_SESSION['soluteName'][$h]."</TD><TD WIDTH=50% align='center'>".$_SESSION['soluteDiffusivities'][$h]."</TD>";

	$h=$h+1;
	}
	print '</TABLE></div>';

	# Reset Button
	print '<form method="post" action="ResetInput.php">
	<INPUT type="submit" value="Reset Solutes" name="Reset">
	</form>';

	# Add a spacer to make page look better
	print '<div id="spacer"></div>';
	#print '<TABLE><BR>Declare Next Solute or Press Next Step<BR>';
}




################################################################################################################
################################################################################################################
################################### SOLUTE DECLARATION SECTION #################################################
################################################################################################################
################################################################################################################

# SET THE FORM UP THAT THE USER WILL COMPLETE
print '<form name="Solutes" action="process_solutes.php" method="post">';
 
$allOutputs = $doc->getElementsByTagName("soluteDeclarationParam");

print "<hr><p align='left'><b>Complete the details for the Solute and press 'Add Solute'. You must add all Solutes before pressing the 'Next Step' button</b></p>";

print '<TABLE WIDTH=100%>';
# NOW READ IN EACH OF THE SIMULATOR PARAMETERS, CREATING AN ENTRY FOR EACH
foreach($allOutputs as $param)
{
	$paramName = getTagValue($param,"name");
	$paramDescription = getTagValue($param,"description");
	array_push($_SESSION["soluteDecParams"],$paramName);
	$paramDefault = getTagValue($param,"default");
	$paramType = getTagValue($param,"type");
	$paramOptions = getTagValue($param,"options");
	$paramUnit = getTagValue($param,"unit");

	if($paramName!="domain")
	{

		# IF ITS A BOOLEAN PARAMETER, WE'LL USE A CHECKBOX
		if($paramType=="boolean")
		{
			# NOW FOR THE ADAPTIVE TIMESTEP PARAMETER, WE'LL USE EXTRA PART OF THE CHECKBOX WHICH TURNS TIMESTEPINI AND TIMESTEPMAX ON AND OFF DEPENDENT
			# ON WHETHER THE BOX IS SELECTED
			print "<TR><TD WIDTH=10%>".$paramName."</TD><TD WIDTH=20%>".$paramDescription."</TD><TD WIDTH=13%><input type='checkbox' id='".$paramName."' name='".$paramName."' value='1'></TD></TR>";

		}
		# NOT A BOOLEAN PARAMETER:
		else
		{
			print "<TR><TD WIDTH=10% >".$paramName."</TD><TD WIDTH=20%>".$paramDescription."</TD><TD WIDTH=13%><input type='text' size='25' id='".$paramName."' name='".$paramName."' value='".$paramDefault."'></TD><TD WIDTH=67%>".$paramUnit."</TD></TR>";
		}
	}
	else
	{
		# Domain names have already been defined - so we can give the user the option of those that already exist in the protocol file
		print "<TR><TD WIDTH=10% >".$paramName."</TD><TD WIDTH=20%>".$paramDescription."</TD><TD WIDTH=13%><select name='".$paramName."' SIZE=5>";

		$h=0;
		while($h<count($_SESSION['worldNames']))
		{
			$worldName = $_SESSION['worldNames'][$h];
			#print $worldName."<BR>";
			print "<option value=\"$worldName\">$worldName</option>";
			$h=$h+1;


		}
		print "</select></TD></TR>";	
	}
}

print '</TABLE><HR><input type="submit" id="addAnother" name="addAnother" value="Add Solute"><input type="submit" value="Next Step: Declare Particles"></p>';

?>
