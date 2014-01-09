<?php
session_start();
ini_set('display_errors',0);


## Before we load the page, we're going to do a check on whether this is a chemostat
## simulation, and whether 1 bulk and 1 computation domain have been declared. If this
## is the case, this is deemed sufficient, and the user is alerted and redirected to the
## next stage in the protocol file generator
if($_SESSION["chemostat_sim"]=="True")
{
	if(count($_SESSION["bulks"])==1 && count($_SESSION["computationDomains"])==1)
	{
		# Firstly, check whether there is a message for the user about renaming the
		# domain if not chemostat - this won't reach that section if we redirect now
		if($_SESSION["chemoNameMessage"]=="True")
		{
			echo "<script>
			alert('As this is a chemostat simulation, the domain must be called chemostat. The name you entered for your domain has been changed to chemostat');
			</script>";
			# Reset the flag
			$_SESSION["chemoNameMessage"]=="False";
		}

		# Now tell the user they have finished declaration of worlds for a chemostat
		echo "<script>
			alert('As this is a chemostat simulation, and you have declared a bulk and a computation domain, we now skip to declaration of Reactions');
			window.location.href='IntroReactions.php';
			</script>";

	}
}
?>


<html>
<head>
<script>

var selectedDomain;

// Function to check all boundaries have been chosen - this stops PHP errors later
function ValidateForm()
{
	//var worldInputBox = document.getElementById("worldType");
	//var selectedValue = worldInputBox.options[worldInputBox.selectedIndex].value;

	if(selectedDomain=="ComputationDomain")
	{
		var boundaries=new Array("y0z_Condition","yNz_Condition","x0z_Condition","xNz_Condition","x0y_Condition","xNy_Condition");

		for (i=0;i<boundaries.length;i++)
		{
			// get selection object
			var sel = document.getElementById(boundaries[i]);
			// check if its index is not 0 (the first selection is invalid).
			if (sel.selectedIndex == -1)
			{
				alert ("Select boundary condition for "+boundaries[i]); 
				return false;
			}
		}
	}
	else
	{
		return true;
	}

	
} 

// Function to hide the Bulk and Computation Domain parameters until one is chosen
function offOnLoad()
{
	document.getElementById("Bulk").style.display = "none";
	document.getElementById("BulkSolutes").style.display = "none";
	document.getElementById("ComputationDomain").style.display = "none";
	document.getElementById("CompDomainTitle").style.display = "none";
	document.getElementById("Boundaries").style.display = "none";
	document.getElementById("SoluteText").style.display = "none";
	document.getElementById("SoluteHeader").style.display = "none";
	document.getElementById("BoundaryTitle").style.display = "none";
	document.getElementById("BoundaryDesc").style.display = "none";
}

// Toggles the tables, showing Computation Domain if that was selected, bulk if not
function toggle(table) 
{
	if(table=="Bulk")
	{
		document.getElementById(table).style.display = "";
		document.getElementById("BulkSolutes").style.display = "";
		document.getElementById("SoluteText").style.display = "";
		document.getElementById("ComputationDomain").style.display = "none";
		document.getElementById("CompDomainTitle").style.display = "none";
		document.getElementById("BoundaryTitle").style.display = "none";
		document.getElementById("BoundaryDesc").style.display = "none";
		document.getElementById("Boundaries").style.display = "none";
		document.getElementById("SoluteHeader").style.display = "";
	}
	if(table=="ComputationDomain")
	{
		document.getElementById(table).style.display = "";
		document.getElementById("Boundaries").style.display = "";
		document.getElementById("Bulk").style.display = "none";
		document.getElementById("BulkSolutes").style.display = "none";
		document.getElementById("SoluteText").style.display = "none";
		document.getElementById("SoluteHeader").style.display = "none";
		document.getElementById("CompDomainTitle").style.display = "";
		document.getElementById("BoundaryTitle").style.display = "";
		document.getElementById("BoundaryDesc").style.display = "";
	}

	selectedDomain = table;

}

// Function to automatically set the opposite boundary condition to Cyclic (or unset it) if cyclic has been 
// chosen for its respective boundary
function setBoundaryClass(conditionChosen,boundary)
{
	if(boundary=="x0z")
	{
		// Left boundary - check if cyclic has been selected
		if(conditionChosen=="BoundaryCyclic")
		{
			document.getElementById("xNz_Condition").options.length = 0;
			document.getElementById("xNz_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
		}
		// Need to cover the base here that boundary cyclic was selected but is changed - thus the opposite should display all options
		else
		{
			if(document.getElementById("xNz_Condition").options.length<4)
			{
				document.getElementById("xNz_Condition").options.length = 0;
				document.getElementById("xNz_Condition").options.add(new Option("BoundaryZeroFlux","BoundaryZeroFlux",true,true));
				document.getElementById("xNz_Condition").options.add(new Option("BoundaryBulk","BoundaryBulk",true,true));
				document.getElementById("xNz_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
				document.getElementById("xNz_Condition").options.add(new Option("BoundaryGasMembrane","BoundaryGasMembrane",true,true));
			}
		}

	}
	else if(boundary=="xNz")
	{
		// Right boundary - check if cyclic has been selected
		if(conditionChosen=="BoundaryCyclic")
		{
			document.getElementById("x0z_Condition").options.length = 0;
			document.getElementById("x0z_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
		}
		// Need to cover the base here that boundary cyclic was selected but is changed - thus the opposite should display all options
		else
		{
			if(document.getElementById("x0z_Condition").options.length<4)
			{
				document.getElementById("x0z_Condition").options.length = 0;
				document.getElementById("x0z_Condition").options.add(new Option("BoundaryZeroFlux","BoundaryZeroFlux",true,true));
				document.getElementById("x0z_Condition").options.add(new Option("BoundaryBulk","BoundaryBulk",true,true));
				document.getElementById("x0z_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
				document.getElementById("x0z_Condition").options.add(new Option("BoundaryGasMembrane","BoundaryGasMembrane",true,true));
			}
		}
	}
	else if(boundary=="x0y")
	{
		// Right boundary - check if cyclic has been selected
		if(conditionChosen=="BoundaryCyclic")
		{
			document.getElementById("xNy_Condition").options.length = 0;
			document.getElementById("xNy_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
		}
		// Need to cover the base here that boundary cyclic was selected but is changed - thus the opposite should display all options
		else
		{
			if(document.getElementById("xNy_Condition").options.length<4)
			{
				document.getElementById("xNy_Condition").options.length = 0;
				document.getElementById("xNy_Condition").options.add(new Option("BoundaryZeroFlux","BoundaryZeroFlux",true,true));
				document.getElementById("xNy_Condition").options.add(new Option("BoundaryBulk","BoundaryBulk",true,true));
				document.getElementById("xNy_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
				document.getElementById("xNy_Condition").options.add(new Option("BoundaryGasMembrane","BoundaryGasMembrane",true,true));
			}
		}
	}
	else if(boundary=="xNy")
	{
		// Right boundary - check if cyclic has been selected
		if(conditionChosen=="BoundaryCyclic")
		{
			document.getElementById("x0y_Condition").options.length = 0;
			document.getElementById("x0y_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
		}
		// Need to cover the base here that boundary cyclic was selected but is changed - thus the opposite should display all options
		else
		{
			if(document.getElementById("x0y_Condition").options.length<4)
			{
				document.getElementById("x0y_Condition").options.length = 0;
				document.getElementById("x0y_Condition").options.add(new Option("BoundaryZeroFlux","BoundaryZeroFlux",true,true));
				document.getElementById("x0y_Condition").options.add(new Option("BoundaryBulk","BoundaryBulk",true,true));
				document.getElementById("x0y_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
				document.getElementById("x0y_Condition").options.add(new Option("BoundaryGasMembrane","BoundaryGasMembrane",true,true));
			}
		}
	}
	else if(boundary=="yNz")
	{
		// Right boundary - check if cyclic has been selected
		if(conditionChosen=="BoundaryCyclic")
		{
			document.getElementById("y0z_Condition").options.length = 0;
			document.getElementById("y0z_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
		}
		// Need to cover the base here that boundary cyclic was selected but is changed - thus the opposite should display all options
		else
		{
			if(document.getElementById("y0z_Condition").options.length<4)
			{
				document.getElementById("y0z_Condition").options.length = 0;
				document.getElementById("y0z_Condition").options.add(new Option("BoundaryZeroFlux","BoundaryZeroFlux",true,true));
				document.getElementById("y0z_Condition").options.add(new Option("BoundaryBulk","BoundaryBulk",true,true));
				document.getElementById("y0z_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
				document.getElementById("y0z_Condition").options.add(new Option("BoundaryGasMembrane","BoundaryGasMembrane",true,true));
			}
		}
	}
	else if(boundary=="y0z")
	{
		// Right boundary - check if cyclic has been selected
		if(conditionChosen=="BoundaryCyclic")
		{
			document.getElementById("yNz_Condition").options.length = 0;
			document.getElementById("yNz_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
		}
		// Need to cover the base here that boundary cyclic was selected but is changed - thus the opposite should display all options
		else
		{
			if(document.getElementById("yNz_Condition").options.length<4)
			{
				document.getElementById("yNz_Condition").options.length = 0;
				document.getElementById("yNz_Condition").options.add(new Option("BoundaryZeroFlux","BoundaryZeroFlux",true,true));
				document.getElementById("yNz_Condition").options.add(new Option("BoundaryBulk","BoundaryBulk",true,true));
				document.getElementById("yNz_Condition").options.add(new Option("BoundaryCyclic","BoundaryCyclic",true,true));
				document.getElementById("yNz_Condition").options.add(new Option("BoundaryGasMembrane","BoundaryGasMembrane",true,true));
			}
		}
	}
	
	// Now to deal with parameters for some conditions
	if(conditionChosen=="BoundaryBulk")
	{
		
		removeBoundaryParameters(boundary);
		addBoundaryBulkParameters(boundary);
	}
	else if(conditionChosen=="BoundaryGasMembrane")
	{
		removeBoundaryParameters(boundary);	
		addGasMembraneParameters(boundary);
	}
	else
	{
		removeBoundaryParameters(boundary);
	}
}

function addBoundaryBulkParameters(boundary)
{
	document.getElementById(boundary+"_Param1Label").innerHTML ="activeForSolute:";

	var checkbox = document.createElement("input");
	checkbox.type = "checkbox";
	checkbox.name = boundary+"_Param1";
	checkbox.value = "yes";
	checkbox.id = boundary+"_Param1"
	document.getElementById(boundary+"_Param1").appendChild(checkbox);

	document.getElementById(boundary+"_Param2Label").innerHTML ="bulk:";

	// Now for the selection of bulks in the system
	var bulkArray= <?php echo json_encode($_SESSION["bulkNames"]); ?>;
	var numBulks = <?php echo count($_SESSION["bulkNames"]); ?>;
	var bulkSelector = document.createElement("select");
	bulkSelector.setAttribute("name", boundary+"_Param2");
	bulkSelector.setAttribute("id", boundary+"_Param2");
	bulkSelector.setAttribute("size",3);


	for(var i=0;i<numBulks;i++)
	{
		var option;
		option = document.createElement("option");
		option.setAttribute("value", bulkArray[i]);
		option.innerHTML = bulkArray[i];
		bulkSelector.appendChild(option);
	}	
	
	document.getElementById(boundary+"_Param2").appendChild(bulkSelector);

	
}

function addGasMembraneParameters(boundary)
{
	document.getElementById(boundary+"_Param1Label").innerHTML ="isPermeableTo:";

	// Now a list of all solutes that the membrane may be permeable to:
	var soluteArray= <?php echo json_encode($_SESSION["soluteName"]); ?>;
	var numSolute = <?php echo count($_SESSION["soluteName"]); ?>;
	var soluteSelector = document.createElement("select");
	soluteSelector.setAttribute("name", boundary+"_Param1");
	soluteSelector.setAttribute("id", boundary+"_Param1");
	soluteSelector.setAttribute("size",3);

	for(var i=0;i<numSolute;i++)
	{
		var option;
		option = document.createElement("option");
		option.setAttribute("value", soluteArray[i]);
		option.innerHTML = soluteArray[i];
		soluteSelector.appendChild(option);
	}	
	
	document.getElementById(boundary+"_Param1").appendChild(soluteSelector);

	// Now the bulk
	document.getElementById(boundary+"_Param2Label").innerHTML ="bulk:";

	var bulkArray= <?php echo json_encode($_SESSION["bulkNames"]); ?>;
	var numBulks = <?php echo count($_SESSION["bulkNames"]); ?>;
	var bulkSelector = document.createElement("select");
	bulkSelector.setAttribute("name", boundary+"_Param2");
	bulkSelector.setAttribute("id", boundary+"_Param2");
	bulkSelector.setAttribute("size",3);


	for(var i=0;i<numBulks;i++)
	{
		var option;
		option = document.createElement("option");
		option.setAttribute("value", bulkArray[i]);
		option.innerHTML = bulkArray[i];
		bulkSelector.appendChild(option);
	}	
	
	document.getElementById(boundary+"_Param2").appendChild(bulkSelector);

	// Now the amount
	document.getElementById(boundary+"_Param3Label").innerHTML ="Quantity:";

	// Entry box for this
	var quant = document.createElement("input");
	quant.setAttribute("name", boundary+"_Param3");
	quant.setAttribute("id", boundary+"_Param3");
	quant.setAttribute("size",5);

	document.getElementById(boundary+"_Param3").appendChild(quant);
}

function removeBoundaryParameters(boundary)
{
	document.getElementById(boundary+"_Param1Label").innerHTML = "";
	document.getElementById(boundary+"_Param1").innerHTML="";
	document.getElementById(boundary+"_Param2Label").innerHTML = "";
	document.getElementById(boundary+"_Param2").innerHTML="";
	document.getElementById(boundary+"_Param3Label").innerHTML = "";
	document.getElementById(boundary+"_Param3").innerHTML="";
}

</script></head>

<?php

##################################################################################################################
############################ PRINT THE MENU ######################################################################
##################################################################################################################

include("components/interfaceSettings.php");
include("components/menu.php");

PrintHeader("Simulation Experiment Specification",4);


print "<H2 ALIGN='LEFT'>Defining Worlds</H2>

<p align='left'>The world mark-up collects the description of all bulks and computation domains defined in the simulation. Only one world may be defined, but this world may contain several bulk compartments and computationDomain domains, each with a different name. Though when simulating a chemostat scenario, the name of the bulk MUST be <b>chemostat</b> regardless of the corresponding computationDomain name.<BR><BR>

A <b>bulk</b> is a perfectly mixed liquid compartment of the system, usually with a larger size than the extent of the simulated biofilm domain. For example, in a wastewater reactor the bulk would refer to the liquid volume being treated. The bulk will usually have a fixed volume, but the volume is not specified here; instead, the definition of the computationDomain addresses the bulk compartment volume.<BR><BR>

The <b>computationDomain</b> may be either 2D or 3D, as desired. (Note though that 3D simulations are more computationally intensive and will require more time to run.) The spatial domain is broken into rectilinear regions that each occupy a small space, and definition of the computationDomain* requires defining how these regions are set up. <b> We suggest that you read the <a href='https://github.com/kreft/iDynoMiCS/wiki/The-Computational-Domain'>Computational Domain section in the iDynoMiCS tutorial carefully</a>, as this explains the definition of the boundary conditions in detail</b></p>";

################################################################################################################
################################################################################################################
################################### WORLD DISPLAY SECTION ######################################################
################################################################################################################
################################################################################################################

if(count($_SESSION["bulks"])>0 || count($_SESSION["computationDomains"])>0)
{
	print '<H2 align="left">Worlds Declared:</H2>';
}

# First, see if the process screen has left the user a message, saying the bulk has been renamed if this
# is a chemostat simulation (and they did not call it chemostat)
if($_SESSION["chemoNameMessage"]=="True")
{
	echo "<script>
	alert('As this is a chemostat simulation, the domain must be called chemostat. The name you entered for your domain has been changed to chemostat');
	</script>";
	# Reset the flag
	$_SESSION["chemoNameMessage"]=="False";
}


if(count($_SESSION["bulks"])>0 || count($_SESSION["computationDomains"])>0)
{
	# Do the bulks first
	if(count($_SESSION["bulks"])>0)
	{
		print '<H3 align="left">Bulks:</H3>

		<div id="displayTable"><TABLE align="left"><TR>';
		$bulk=0;

		# Headers first
		for($p=0;$p<count($_SESSION["bulkParamNames"]);$p++)
		{
			print "<TD align='center'><b>".$_SESSION['bulkParamNames'][$p]."</b></TD>";
		}
		print "</TR>";

		while($bulk<count($_SESSION["bulks"]))
		{
			$p=0;
			while($p<count($_SESSION["bulkParamNames"]))
			{
				print "<TD align='center'>".$_SESSION["bulks"][$bulk][$p]."</TD>";
				$p=$p+1;
			}
			print "<TR>";
			$bulk=$bulk+1;

		}
		print "</TABLE></div>";

		# Add a spacer to make page look better
		print '<div id="spacer"></div>';
	}

	# Now the computation domains
	if(count($_SESSION["computationDomains"])>0)
	{
		print '<H3 align="left">Computation Domains:</H3>
		<div id="displayTable"><TABLE align="left"><TR>';

		$cd=0;
		# Headers first
		for($p=0;$p<count($_SESSION["computationDomainParamNames"]);$p++)
		{
			print "<TD align='center'><b>".$_SESSION["computationDomainParamNames"][$p]."</b></TD>";
		}
		print "</TR>";

	

		while($cd<count($_SESSION["computationDomains"]))
		{
			$p=0;
			while($p<count($_SESSION["computationDomainParamNames"]))
			{
				print "<TD align='center'>".$_SESSION["computationDomains"][$cd][$p]."</TD>";
				$p=$p+1;
			}
			print "<TR>";
			$cd=$cd+1;
		}
		print "</TABLE></div>";

		# Add a spacer to make page look better
		print '<div id="spacer"></div>';

	}

	# Reset Button
	print '<form method="post" action="ResetInput.php">
	<INPUT type="submit" value="Reset Worlds" name="Reset">
	</form>';

	
}


#############################################################################
#############################################################################
##################### DECLARE WORLDS ########################################
#############################################################################
#############################################################################

# SET THE FORM UP THAT THE USER WILL COMPLETE
print '<form name="Worlds" action="process_world.php" method="post">';
 
print "<hr><p align='left'><b>Complete the details for the Domain and press 'Add Domain'. You must add all Domains before pressing the 'Next Step' button. It is recommended that you declare the Bulks before the Computation Domains</b></p>";

# As the world is only a declaration of the name and the type (all other parameters are considered later), we don't use the protocol file here
# We just give the user those two options
print '<div id="displayTable">';
print "<TABLE WIDTH=40% ALIGN='LEFT'><TR><TD align='center'>Domain Name</TD><TD align='center'>Domain Type</TD></TR>
	<TR>
		<TD align='center'><input type='text' size='25' id='worldName' name='worldName'></TD>
		<TD align='center'><select name='worldType' SIZE=2 onchange='toggle(this.value)'>
		<OPTION VALUE='ComputationDomain'>Computation Domain</OPTION>";

	# Now here we are going to offer the two options if not a chemostat simulation
	# but if it is, we are only going to allow the user to enter one bulk
	if($_SESSION["chemostat_sim"]=="False" || ($_SESSION["chemostat_sim"]=="True" && count($_SESSION["bulks"])==0))
	{
		# Add the Bulk Option
		print "<OPTION VALUE='Bulk'>Bulk</OPTION></SELECT></TD>";
	}

	print "</TR></TABLE></div>";

# Add a spacer to make page look better
print '<div id="bigSpacer"></div>';


#############################################################################
#############################################################################
########################### BULK ############################################
#############################################################################
#############################################################################

print '<div id="displayTable"><TABLE id="Bulk" WIDTH=70% align="left">';

# Now all the parameter details have been processed already - we just need to read these out of the array rather than the XML file. Saves time for
# cases where there are multiple domains

if(count($_SESSION["bulkParamNames"])>0)
{
	# Can't do an iterator here as need a counter to reference multiple arrays
	# Note we start at 1 so the 'Name' parameter is ignored (as this has already been completed)
	$h=1;
	while($h<count($_SESSION["bulkParamNames"]))
	{
		# We do however have to catch the 'isConstant' parameter - this must be false for a chemostat simulation
		if($_SESSION["chemostat_sim"]=="True" && $_SESSION["bulkParamNames"][$h]=="isConstant")
		{
			print "<TR><TD WIDTH=13%>".$_SESSION["bulkParamNames"][$h]."</TD>
				<TD WIDTH=60%>".$_SESSION["bulkParamDescription"][$h]."</TD>
				<TD WIDTH=13%>false (Must be false for chemostat)</TD>
				<TD WIDTH=13%>".$_SESSION["bulkParamUnit"][$h]."</TD></TR>";
		}
		else
		{
			print "<TR><TD WIDTH=13%>".$_SESSION["bulkParamNames"][$h]."</TD>
				<TD WIDTH=60%>".$_SESSION["bulkParamDescription"][$h]."</TD>
				<TD WIDTH=13%><input type='text' size='5' id='".$_SESSION["bulkParamNames"][$h]."' name='".$_SESSION["bulkParamNames"][$h]."' value='".$_SESSION["bulkParamDefaults"][$h]."'></TD>
				<TD WIDTH=13%>".$_SESSION["bulkParamUnit"][$h]."</TD></TR>";
			
		}
		$h=$h+1;
	}

	print "</TABLE></div><BR>";

	# Add a spacer to make page look better
	print '<div id="spacer"></div>';


	######################### NOW TO ADD THE SOLUTES THAT WERE PREVIOUSLY DECLARED ###########################

	print "<H3 align='left' id='SoluteHeader'>Solutes in This Domain</H3>
	<p align='left' id='SoluteText'>The solutes listed in the bulk mark-up are taken from the previously-defined set of all solutes in the simulation. Here you should tick the solutes that are within this domain. Each solute is described by the Sbulk, Sin, isConstant and possibly the Spulse and pulseRate parameters. Sbulk sets the initial bulk concentration of that solute, and Sin the feed flow concentration of that solute. If isConstant is set to true the concentration of the solute will remain constant even if the bulk isConstant parameter is set to false. The pulseRate parameter may be used to periodically spike the concentration to Spulse at the given rate.</p>";

	print "<div id='displayTable'><TABLE id='BulkSolutes' align='left'>";
	# Print column headers for ease of input
	print "<TD></TD><TD>Solute</TD>";
	$p=0;
	while($p<count($_SESSION["bulkSoluteParamNames"]))
	{
		print "<TD>".$_SESSION["bulkSoluteParamNames"][$p]."</TD>";
		$p=$p+1;
	}

	# Now the user is given a list of solutes for this bulk (from those already declared)
	# They should complete the relevant parameters
	$s=0;
	while($s<count($_SESSION["soluteName"]))
	{
		print "<TR>
			<TD><input type='checkbox' id='Chk_".$_SESSION["soluteName"][$s]."' name='Chk_".$_SESSION["soluteName"][$s]."'></TD>
			<TD>".$_SESSION["soluteName"][$s]."</TD>";

		$p=0;
		while($p<count($_SESSION["bulkSoluteParamNames"]))
		{
			# Check if this is a true or false - if so add a checkbox
			if($_SESSION["bulkSoluteParamType"][$p]!="boolean")
			{
				print "<TD><input type='text' size='5' id='".$_SESSION["soluteName"][$s].$_SESSION["bulkSoluteParamNames"][$p]."' name='".$_SESSION["soluteName"][$s].$_SESSION["bulkSoluteParamNames"][$p]."' value='".$_SESSION["bulkSoluteParamDefaults"][$p]."'></TD>";
			}
			else
			{
				print "<TD><input type='checkbox' id='".$_SESSION["soluteName"][$s].$_SESSION["bulkSoluteParamNames"][$p]."' name='".$_SESSION["soluteName"][$s].$_SESSION["bulkSoluteParamNames"][$p]."' value='".$_SESSION["bulkSoluteParamDefaults"][$p]."'></TD>";
			}
			$p=$p+1;
		}
		print "<TR>";
		$s=$s+1;				

	}
	print "</TABLE></div>";

	# Add a spacer to make page look better
	print '<div id="spacer"></div>';
}


#############################################################################
#############################################################################
#################### COMPUTATION DOMAIN #####################################
#############################################################################
#############################################################################
print "<H3 id='CompDomainTitle' align='left'>Computation Domain Parameters</H3><div id='displayTable'><TABLE id='ComputationDomain' WIDTH=70% align='left'>";

if(count($_SESSION["computationDomainParamNames"])>0)
{
	# Can't do an iterator here as need a counter to reference multiple arrays
	# Note we start at 1 so the 'Name' parameter is ignored (as this has already been completed)
	$h=1;
	while($h<count($_SESSION["computationDomainParamNames"]))
	{
		print "<TR><TD WIDTH=13%>".$_SESSION["computationDomainParamNames"][$h]."</TD>
			<TD WIDTH=60%>".$_SESSION["computationDomainParamDescription"][$h]."</TD>
			<TD WIDTH=10%><input type='text' size='5' id='".$_SESSION["computationDomainParamNames"][$h]."' name='".$_SESSION["computationDomainParamNames"][$h]."' value='".$_SESSION["computationDomainParamDefaults"][$h]."'></TD>
			<TD WIDTH=13%>".$_SESSION["computationDomainParamUnit"][$h]."</TD></TR>";
		$h=$h+1;
	}
}
print "</TABLE></div>";

# Add a spacer to make page look better
print '<div id="spacer"></div>';

#############################################################################
#############################################################################
######### COMPUTATION DOMAIN BOUNDARY CONDITIONS ############################
#############################################################################
#############################################################################

print "<H3 id='BoundaryTitle' align='left'>Computation Domain Boundary Conditions</H3><div id='displayTable'><TABLE id='Boundaries' align='left' width='100%'><TR><TD>
<TABLE WIDTH=50%>";

print "<p align='left' id='BoundaryDesc'>Specify the boundary condition for each of the planes of this domain. See the tutorial for a detailed description of the effect this has on your simulation. Note that if you choose cyclic, the opposite boundary condition must also be cyclic. The boundary condition class should also be selected (although in version 1.3 of iDynoMiCS, Planar is the only class that is currently available)</p>";

$b=0;
while($b<count($_SESSION["boundaries"]))
{
	print "<TR><TD>".$_SESSION["boundaries"][$b]."</TD>";

	# Now give the user the option of the boundary condition that they would like for this boundary.
	# Note that for cyclic boundaries, the assumption is made that when this is selected, the opposite 
	# boundary condition is also cyclic, and thus completed automatically
	print "<TD><SELECT id='".$_SESSION["boundaries"][$b]."_Condition' NAME='".$_SESSION["boundaries"][$b]."_Condition' SIZE=4 onchange=\"setBoundaryClass(this.value,'".$_SESSION["boundaries"][$b]."')\">";
	# NOW COMPLETE THE OPTIONS
	$boundCondCount=0;
	while($boundCondCount<count($_SESSION["boundaryClasses"]))
	{
		$boundOption=$_SESSION['boundaryClasses'][$boundCondCount];
		print "<option value=\"$boundOption\">$boundOption</option>";
		$boundCondCount=$boundCondCount+1;
	}
	print "</SELECT></TD>";

	# NOW ADD A SELECTION BOX TO CHOOSE THE SHAPE CLASS OF THIS BOUNDARY
	# THESE ARE IN THE XML FILE SO THAT THESE CAN BE ADDED TO LATER
	print "<TD><SELECT id='".$_SESSION["boundaries"][$b]."_Shape' NAME='".$_SESSION["boundaries"][$b]."_Shape' SIZE=4>";
	$boundaryShapesCount=0;
	while($boundaryShapesCount<count($_SESSION["boundaryShapes"]))
	{
		$option=$_SESSION['boundaryShapes'][$boundaryShapesCount];
		print "<option selected='selected' value=\"$option\">$option</option>";
		$boundaryShapesCount=$boundaryShapesCount+1;
	}
	print "</SELECT></TD>";

	# Now we need two parameters, although these are only used for Gas Membranes and Boundary Bulks. These will be enabled by the javascript when selected
	# As these have two different names we only initialise the cells here
	print "<TD id='".$_SESSION["boundaries"][$b]."_Param1Label' name='".$_SESSION["boundaries"][$b]."_Param1Label'></TD>
	       <TD id='".$_SESSION["boundaries"][$b]."_Param1' name='".$_SESSION["boundaries"][$b]."_Param1'></TD>
               <TD id='".$_SESSION["boundaries"][$b]."_Param2Label' name='".$_SESSION["boundaries"][$b]."_Param2Label'></TD>
	       <TD id='".$_SESSION["boundaries"][$b]."_Param2' name='".$_SESSION["boundaries"][$b]."_Param2'></TD>
	       <TD id='".$_SESSION["boundaries"][$b]."_Param3Label' name='".$_SESSION["boundaries"][$b]."_Param3Label'></TD>
	       <TD id='".$_SESSION["boundaries"][$b]."_Param3' name='".$_SESSION["boundaries"][$b]."_Param3'></TD>

	</TR>";	
	$b=$b+1;
	
}
print "</TABLE></TD><TD WIDTH=50%><IMG SRC='components/Boundaries.png' height=330></TD></TR></TABLE></div>";

# Add a spacer to make page look better
print '<div id="spacer"></div>';

# Use the offOnLoad javascript function to hide the computation domain and bulk
# parameter tables until one has been set
echo "<script> offOnLoad(); </script>";
	

# Now add the two buttons, one to add a new world, the other to move on to the next step


print '<hr><input type="submit" name="addAnother" value="Add Domain" onclick="return ValidateForm()"><input type="submit" value="Next Step: Declare Reactions"></p>';

?>
