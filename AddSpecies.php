<?php
ini_set('display_errors',0);
session_start();
?>

<html>
<head>
<script>

// We store the species the user selects as a global variable - thus this can be used 
// when adding particles later
var speciesSelected;

function valueselect(sel,name) 
{
	if(name=="class")
	{

		if(sel == "ParticulateEPS")
		{
			document.getElementById("epsMax").setAttribute("disabled","disabled");
			document.getElementById("kHyd").setAttribute("disabled","disabled");
		}
		else if(sel == "Bacterium")
		{
			document.getElementById("epsMax").removeAttribute("disabled");
			document.getElementById("kHyd").setAttribute("disabled","disabled");
		}
		else if(sel == "BactEPS" || sel == "BactAdaptable")
		{
			document.getElementById("epsMax").removeAttribute("disabled");
			document.getElementById("kHyd").removeAttribute("disabled");
		}

		// Now to deal with the reactions and reaction switch
		// The array of reactions in this simulation, translated to a javascript array	
		var reactionArray= <?php echo json_encode($_SESSION["namesOfSpecifiedReactions"]); ?>;

		// The number of reactions in the array
		var numReactions = <?php echo count($_SESSION["namesOfSpecifiedReactions"]); ?>;

		var table=document.getElementById("reactions");

		if(document.getElementById("reactions").rows.length>0)
		{
			clearTable("reactions");
		}
		clearTable("reactionSwitch");

		// Now to add a table of reactions to the page - the user selects the reactions that the species is involved in
		if(numReactions>0)
		{
			addReactionName(0,table,"<b>Reaction</b>");	

			table.rows[0].insertCell(-1);
			table.rows[0].cells[1].id = "reactionLabel";
			table.rows[0].cells[1].align="center";
			var reactionLabel = document.createElement('label');
			reactionLabel.innerHTML = "<b>Reaction Active for This Species</b>";
			document.getElementById("reactionLabel").appendChild(reactionLabel);

			if(sel == "BactAdaptable")
			{
				// Add labels for the bact adaptable species (when on, when off)
				table.rows[0].insertCell(-1);
				table.rows[0].cells[2].id = "offLabel";
				table.rows[0].cells[2].align="center";
				var reactionOffLabel = document.createElement('label');
				reactionOffLabel.innerHTML = "<b>Reaction Off When Switch Off</b>";
				document.getElementById("offLabel").appendChild(reactionOffLabel);

				table.rows[0].insertCell(-1);
				table.rows[0].cells[3].id = "onLabel";
				table.rows[0].cells[3].align="center";
				var reactionOnLabel = document.createElement('label');
				reactionOnLabel.innerHTML = "<b>Reaction Off When Switch On</b>";
				document.getElementById("onLabel").appendChild(reactionOnLabel);
			}
		}	

		for(var i=0;i<numReactions;i++)
		{
			// First add the reaction name
			addReactionName(i+1,table,reactionArray[i]);
		
			// Now add a checkbox for this reaction
			addReactionSelectionBox(i+1,table,reactionArray[i]);

			if(sel == "BactAdaptable")
			{
				// Add selection for off, and a switch lag for off
				addWhenOfforOnSelectionBox(i+1,table,reactionArray[i],2,"off");
			

				// Add the same for on
				addWhenOfforOnSelectionBox(i+1,table,reactionArray[i],3,"on");
				//addSwitchLagInputBox(i,table,reactionArray[i],5,"on");
			}

		}

		if(sel == "BactAdaptable")
		{
			clearTable("reactionSwitch");
			addReactionSwitchCondition();
		}

		// Store the species selected
		speciesSelected=sel;
	}
	else if(name=="initialLocationDeclaration")
	{
		// This is the initial location settings of the cells. The user has chosen either to place a number of these in a set area, or 
		// To place a number of these by length (2D) or area (3D)
		if(sel=="Region")
		{
			var is3D = <?php echo json_encode($_SESSION["is3D"]) ?>;
			if(is3D=="True")
			{
				document.getElementById("cellsperMM2").setAttribute("disabled","disabled");		
			}
			else
			{
				document.getElementById("cellsperMM").setAttribute("disabled","disabled");	
			}
			
			//document.getElementById("initAreaNumber").setAttribute("disabled",false);
			document.getElementById("initAreaNumber").disabled = false;	
		
		}
		else
		{
			// Disable the input boxes for the above and enable the boxes for area
			document.getElementById("initAreaNumber").setAttribute("disabled","disabled");

			if(is3D=="True")
			{
				document.getElementById("cellsperMM2").disabled = false;		
			}
			else
			{
				document.getElementById("cellsperMM").disabled = false;	
			}
			
		}
	}

}

function clearTable(table)
{
	for(var i = document.getElementById(table).rows.length; i > 0;i--)
	{
		document.getElementById(table).deleteRow(i -1);
	}
}

function addReactionSwitchCondition()
{
	////////////////////////////////////////////////////////////////////////////
	/////////////////////////// OFF SWITCH LAG /////////////////////////////////
	////////////////////////////////////////////////////////////////////////////
	/// Label:
	var reactionSwitchTable=document.getElementById("reactionSwitch");
	var row=reactionSwitchTable.insertRow(0);
	var cell1=row.insertCell(0);
	document.getElementById("reactionSwitch").rows[0].cells[0].id = "offSwitchLag";
	var switchLagOffLabel = document.createElement('label');
	switchLagOffLabel.innerHTML = "Off Switch Lag:";
	document.getElementById("offSwitchLag").appendChild(switchLagOffLabel);

	/// Input Box:
	addSwitchLagInputBox(0,reactionSwitchTable,1,"off");

	// Unit
	reactionSwitchTable.rows[0].insertCell(-1);
	reactionSwitchTable.rows[0].cells[2].id = "offUnitLabel";
	reactionSwitchTable.rows[0].cells[2].align="left";
	var offUnitLabel = document.createElement('label');
	offUnitLabel.innerHTML = "hour";
	document.getElementById("offUnitLabel").appendChild(offUnitLabel);
	

	////////////////////////////////////////////////////////////////////////////
	/////////////////////////// ON SWITCH LAG //////////////////////////////////
	////////////////////////////////////////////////////////////////////////////
	/// Label:
	reactionSwitchTable.rows[0].insertCell(-1);
	document.getElementById("reactionSwitch").rows[0].cells[3].id = "onSwitchLag";
	var switchLagOnLabel = document.createElement('label');
	switchLagOnLabel.innerHTML = "On Switch Lag:";
	document.getElementById("onSwitchLag").appendChild(switchLagOnLabel);

	/// Input Box:
	addSwitchLagInputBox(0,reactionSwitchTable,4,"on");

	// Unit Label
	reactionSwitchTable.rows[0].insertCell(-1);
	reactionSwitchTable.rows[0].cells[5].id = "onUnitLabel";
	reactionSwitchTable.rows[0].cells[5].align="left";
	var onUnitLabel = document.createElement('label');
	onUnitLabel.innerHTML = "hour";
	document.getElementById("onUnitLabel").appendChild(onUnitLabel);

	// Now we need to give the user the options that turn the reaction on and off
	var soluteArray= <?php echo json_encode($_SESSION["soluteName"]); ?>;
	// The number of solutes in the array
	var numSolutes = <?php echo count($_SESSION["soluteName"]); ?>;

	// Now to create the select box with this information
	var soluteSelector = document.createElement("select");
	soluteSelector.setAttribute("name", "switchConditionSelector");
	soluteSelector.setAttribute("id", "switchConditionSelector");
	soluteSelector.setAttribute("size",5);
	soluteSelector.onchange = function() {switchSelected(this.value,this.name)};

	// Now to add values for each solute the user has specified
	for(var i=0;i<numSolutes;i++)
	{
		var option;
		option = document.createElement("option");
		option.setAttribute("value", soluteArray[i]);
		option.innerHTML = soluteArray[i];
		soluteSelector.appendChild(option);
	}

	// Now to add the particles
	// Convert to array as previously
	var particlesArray = <?php echo json_encode($_SESSION['particles'.$_SESSION["particleParamNames"][0]]); ?>;
	var numparticles = <?php echo count($_SESSION['particles'.$_SESSION["particleParamNames"][0]]); ?>;
	for(var i=0;i<numparticles;i++)
	{
		var option;
		option = document.createElement("option");
		option.setAttribute("value", particlesArray[i]);
		option.innerHTML = particlesArray[i];
		soluteSelector.appendChild(option);
	}

	// Now we need to add a column into the solutes table and insert this selection box in the right place
	var reactionSwitchTable=document.getElementById("reactionSwitch");
	reactionSwitchTable.rows[0].insertCell(-1);
	document.getElementById("reactionSwitch").rows[0].cells[6].id = "reactionSwitchCondition";
	document.getElementById("reactionSwitchCondition").appendChild(soluteSelector); 
	

	// Now add a second box - but this just needs the options "less than" or "greater than"
	var criteriaSelector = document.createElement("select");
	criteriaSelector.setAttribute("name", "criteriaSelector");
	criteriaSelector.setAttribute("id", "criteriaSelector");
	criteriaSelector.setAttribute("size",5);

	var option;
	option = document.createElement("option");
	option.setAttribute("value", "lessThan");
	option.innerHTML = "lessThan";
	criteriaSelector.appendChild(option);

	option = document.createElement("option");
	option.setAttribute("value", "greaterThan");
	option.innerHTML = "greaterThan";
	criteriaSelector.appendChild(option);

	// Add to the table
	var reactionSwitchTable=document.getElementById("reactionSwitch");
	reactionSwitchTable.rows[0].insertCell(-1);
	document.getElementById("reactionSwitch").rows[0].cells[7].id = "criteriaCondition";
	document.getElementById("criteriaCondition").appendChild(criteriaSelector); 

}

function switchSelected(switchSelected, switchName)
{
	var reactionSwitchTable=document.getElementById("reactionSwitch");

	// Clear the cells if an option was previously chosen
	if(reactionSwitchTable.rows[0].cells.length>2)
	{
		removeReactionSwitchInput(reactionSwitchTable);
	}

	// Add a cell for the label
	var criteriaLabel = reactionSwitchTable.rows[0].insertCell(-1);

	// Add a cell for the input box
	reactionSwitchTable.rows[0].insertCell(-1);
	reactionSwitchTable.rows[0].cells[8].id = "switchTrigger";
	var trig = document.createElement("input");
	trig.setAttribute("name", "switchTriggerInput");
	trig.setAttribute("id", "switchTriggerInput");
	trig.setAttribute("size",5);
	document.getElementById("switchTrigger").appendChild(trig);

	// Now a cell for the unit
	var unitLabel = reactionSwitchTable.rows[0].insertCell(-1);

	// Now add the correct labels
	if(switchSelected!="biomass" && switchSelected!="inert" && switchSelected!="capsule")
	{
		// This must be a solute, so the criteria is concentration, measured in g.L-1
		criteriaLabel.innerHTML = "Concentration:";

		// Now the input box for the concentration
		unitLabel.innerHTML = "g.L-1";
	}
	else
	{
		// Criteria is mass, measured in fg
		criteriaLabel.innerHTML = "Mass:";

		unitLabel.innerHTML = "fg";

	}
}

function removeReactionSwitchInput(table)
{
	var tblBodyObj = document.getElementById("reactionSwitch").tBodies[0];
	
	tblBodyObj.rows[0].deleteCell(2);
	
}


// FUNCTION TO ADD THE NAME OF THE REACTION TO THE REACTION TABLE
function addReactionName(rowNum,table,nameOfReaction)
{
	var row=table.insertRow(rowNum);
	var cell1=row.insertCell(0);
	document.getElementById("reactions").rows[rowNum].cells[0].id = "reactionLabel"+rowNum;
	table.rows[rowNum].cells[0].align="center";

	var reactionName = document.createElement('label');
	reactionName.innerHTML = nameOfReaction;

	document.getElementById("reactionLabel"+rowNum).appendChild(reactionName); 
}

// FUNCTION TO ADD A SELECTION BOX FOR EACH REACTION, FOR THIS SPECIES
function addReactionSelectionBox(rowNum,table,nameOfReaction)
{
	table.rows[rowNum].insertCell(-1);
	document.getElementById("reactions").rows[rowNum].cells[1].id = "reactionsBox"+rowNum;
	table.rows[rowNum].cells[1].align="center";

	var checkbox = document.createElement('input');
	checkbox.type = "checkbox";
	checkbox.name = "Chk"+nameOfReaction;
	checkbox.value = "True";
	checkbox.id = "Chk"+nameOfReaction;

	document.getElementById("reactionsBox"+rowNum).appendChild(checkbox);
}

// FUNCTION TO ADD A "WHEN OFF" SELECTION BOX FOR EACH REACTION, FOR A BACTADAPTABLE SPECIES
function addWhenOfforOnSelectionBox(rowNum,table,nameOfReaction,columnNum,offOrOn)
{
	table.rows[rowNum].insertCell(-1);
	document.getElementById("reactions").rows[rowNum].cells[columnNum].id = "reactionsBox"+offOrOn+"_"+rowNum;
	table.rows[rowNum].cells[columnNum].align="center";

	var checkbox = document.createElement('input');
	checkbox.type = "checkbox";
	checkbox.name = "Chk_"+offOrOn+"_"+nameOfReaction;
	checkbox.value = "True";
	checkbox.id = "Chk_"+offOrOn+"_"+nameOfReaction;

	document.getElementById("reactionsBox"+offOrOn+"_"+rowNum).appendChild(checkbox);
}

function addSwitchLagInputBox(rowNum,table,columnNum,offOrOn)
{
	table.rows[rowNum].insertCell(-1);
	table.rows[rowNum].cells[columnNum].id = "switchLag"+offOrOn;

	var lagIn = document.createElement("input");
	lagIn.setAttribute("name", "switchLagBox"+offOrOn);
	lagIn.setAttribute("id", "switchLagBox"+offOrOn);
	lagIn.setAttribute("size",5);

	document.getElementById("switchLag"+offOrOn).appendChild(lagIn);
}


// Remove an element from the page by ID. Used to change between kinetic parameters
function remove(id)
{
    return (elem=document.getElementById(id)).parentNode.removeChild(elem);
}

function deleteMassOptions(rowNum,tblBodyObj)
{
	if(document.getElementById("massLabel"+rowNum)!=null)
	{
		remove("massLabel"+rowNum);
	}
	if(document.getElementById("massOption"+rowNum)!=null)
	{
		remove("massOption"+rowNum);
	}
	if(document.getElementById("massUnit"+rowNum)!=null)
	{
		remove("massUnit"+rowNum);
	}

	var columnCount = tblBodyObj.rows[rowNum].cells.length;
	
	for(var i=1;i<columnCount;i++)
	{
		tblBodyObj.rows[rowNum].deleteCell(i);
	}
}

function addRow(tableID) 
{
	x=eval(document.getElementById("particleCount").value);
	var optionName = "particle"+x;

	// The array of particles in this simulation, translated to a javascript array	
	var particleArray= <?php echo json_encode($_SESSION["particles".$_SESSION['particleParamNames'][0]]); ?>;

	// The number of particles in the array
	var numSolutes = <?php echo count($_SESSION["particles".$_SESSION['particleParamNames'][0]]); ?>;

	var table=document.getElementById("dataTable");
	var row=table.insertRow(x);
	var cell1=row.insertCell(0);
	document.getElementById("dataTable").rows[x].cells[0].id = "particleCell"+x;
	
	// NOW CREATE THE BIOMASS / INERT / CAPSULE SELECTION BOX
	var particleSelector = document.createElement("select");
	particleSelector.setAttribute("name", optionName);
	particleSelector.setAttribute("id", optionName);
	particleSelector.setAttribute("size",3);
	particleSelector.onchange = function() {particleSelected(this.value,this.name)};

	for(var i=0;i<numSolutes;i++)
	{
		var option;
		option = document.createElement("option");
		option.setAttribute("value", particleArray[i]);
		option.innerHTML = particleArray[i];
		particleSelector.appendChild(option);
	}

	document.getElementById("particleCell"+x).appendChild(particleSelector); 

	document.getElementById("particleCount").value=x+1;
}

function particleSelected(particleSelected, particleName)
{
	// kineticName is the name of the kinetic selection box. This is useful as this contains the
	// row number of the table, necessary to add text boxes
	// So get the last character
	var rowNum = particleName.charAt(particleName.length-1);

	var table=document.getElementById("dataTable");
	var tblBodyObj = document.getElementById("dataTable").tBodies[0];

	// Now we need to display a list of EPS capsule types if the user has selected capsule, and the class is Bacterium, BactAdaptable, or
	// BactEPS
	if(particleSelected=="capsule" && (speciesSelected=="Bacterium" || speciesSelected=="BactAdaptable" || speciesSelected=="BactEPS"))
	{
		// Going to do the same as with Reactions here - remove what was on the screen before, incase the user has 
		// chosen Capsule and then changed their mind
		if(tblBodyObj.rows[rowNum].cells.length>1)
		{
			deleteMassOptions(rowNum,tblBodyObj);
		}

		// Now we need to show a list of ParticulateEPS species here (if any declared)
		// When we process the species input, we keep the name and class in a separate array to make this easier
		var speciesNamesArray= <?php echo json_encode($_SESSION["speciesNames"]); ?>;
		var speciesClassArray= <?php echo json_encode($_SESSION["speciesClass"]); ?>;
		// The number of particles in the array
		var numSpecies = <?php echo count($_SESSION["speciesNames"]); ?>;

		// Now to process these
		if(numSpecies>0)
		{
			var optionName = "eps"+rowNum;
			var epsSelector = document.createElement("select");
			epsSelector.setAttribute("name", optionName);
			epsSelector.setAttribute("id", optionName);
			epsSelector.setAttribute("size",4);
			//epsSelector.onchange = function() {particleSelected(this.value,this.name)};

			// Add the options
			for(var i=0;i<numSpecies;i++)
			{
				if(speciesClassArray[i]=="ParticulateEPS")
				{
					// We should display this as a selectable option
					var option;
					option = document.createElement("option");
					option.setAttribute("value", speciesNamesArray[i]);
					option.innerHTML = speciesNamesArray[i];
					epsSelector.appendChild(option);
				}
			}

			var massBox = table.rows[rowNum].insertCell(-1);
			table.rows[rowNum].cells[1].id = "epsOption"+rowNum;
			document.getElementById("epsOption"+rowNum).appendChild(epsSelector);

			// Now add the rest of the input boxes
			add_Mass_Label(rowNum,table,2);
			add_Mass_Input(rowNum,table,3);
			add_Mass_Unit(rowNum,table,4);
		}
		else
		{

			// No Particulate EPS - just add the other boxes	
			add_Mass_Label(rowNum,table,1);
			add_Mass_Input(rowNum,table,2);
			add_Mass_Unit(rowNum,table,3);	
		}
		
	}
	else
	{
		// If not capsule, carry on as previously
		// Going to do the same as with Reactions here - remove what was on the screen before, incase the user has 
		// chosen Capsule and then changed their mind
		if(tblBodyObj.rows[rowNum].cells.length>1)
		{
			deleteMassOptions(rowNum,tblBodyObj);
		}

		add_Mass_Label(rowNum,tblBodyObj,1);
		add_Mass_Input(rowNum,tblBodyObj,2);
		add_Mass_Unit(rowNum,tblBodyObj,3);
		
	}
}

function add_Mass_Input(rowNum,table,column)
{
	var massOptionName = "mass"+rowNum;
	var massBox = table.rows[rowNum].insertCell(-1);
	table.rows[rowNum].cells[2].id = "massOption"+rowNum;

	var mass = document.createElement("input");
	mass.setAttribute("name", massOptionName);
	mass.setAttribute("id", massOptionName);
	mass.setAttribute("size",5);
	document.getElementById("massOption"+rowNum).appendChild(mass);
}

function add_Mass_Label(rowNum,table,column)
{
	var massLabel = table.rows[rowNum].insertCell(-1);
	massLabel.innerHTML = "Mass:";
	table.rows[rowNum].cells[column].id = "massLabel"+rowNum;
}

function add_Mass_Unit(rowNum,table,column)
{
	var massUnit = table.rows[rowNum].insertCell(-1);
	massUnit.innerHTML = "(fg):";
	table.rows[rowNum].cells[column].id = "massUnit"+rowNum;
}

</script>
</head>

<?php

function addSpeciesArraysToForm($arrayToUse)
{
	$speciesParamCount=0;
	while($speciesParamCount<count($_SESSION[$arrayToUse."ParamNames"]))
	{
		# For ease of reading:
		$paramName = $_SESSION[$arrayToUse."ParamNames"][$speciesParamCount];
		$paramDescription = $_SESSION[$arrayToUse."ParamDescription"][$speciesParamCount];
		$paramDefault = $_SESSION[$arrayToUse."ParamDefault"][$speciesParamCount];
		$paramType = $_SESSION[$arrayToUse."ParamType"][$speciesParamCount];
		$paramOptions = $_SESSION[$arrayToUse."ParamOptions"][$speciesParamCount];
		$paramUnit = $_SESSION[$arrayToUse."ParamUnits"][$speciesParamCount];
		$paramDependency = $_SESSION[$arrayToUse."ParamDependency"][$speciesParamCount];
		$dependencyValue = $_SESSION[$arrayToUse."DependencyValue"][$speciesParamCount];

		# TEST IF WE'RE NEEDING A SELECTION BOX
		if (empty($paramOptions)) 
		{
			# IF ITS A BOOLEAN PARAMETER, WE'LL USE A CHECKBOX
			if($paramType=="boolean")
			{
				print "<TR><TD>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD><input type='checkbox' id='".$paramName."' name='".$paramName."' value='1'";
		
				# SET THE CHECKBOX TICKED IF THE DEFAULT IS TO BE TICKED
				if($paramDefault=="true")
				{
					print "checked></TD><TD>".$paramUnit."</TD></TR>";
				}
				else
				{
					print "unchecked></TD><TD>".$paramUnit."</TD></TR>";
				}
			}
			# NOT A BOOLEAN PARAMETER:
			else
			{
				if($paramName=="computationDomain")
				{
					# Domain names have already been defined - so we can give the user the option of those that already exist in the protocol file
					print "<TR><TD WIDTH=13% >".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=13%><select name='".$paramName."' SIZE=5>";

					$h=0;
					while($h<count($_SESSION["computationDomains"]))
					{
						# Name of the computation domain is the first element of each computation domain array
						$worldName = $_SESSION["computationDomains"][$h][0];
						print "<option value=\"$worldName\">$worldName</option>";
						$h=$h+1;


					}
					print "</select></TD></TR>";
				}
				else if($paramName=="cellsperMM2" || $paramName=="cellsperMM")
				{
					# We only want to display one of these
					if(($paramName=="cellsperMM2" && $_SESSION["is3D"]=="True") || ($paramName=="cellsperMM" && $_SESSION["is3D"]=="False"))
					{
						print "<TR><TD>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=13%><input type='text' size='20' id='".$paramName."' name='".$paramName."' value='".$paramDefault."'></TD><TD>".$paramUnit."</TD></TR>";
					}
					
				}
				else
				{
					if(empty($paramDependency))
					{
						print "<TR><TD>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=13%><input type='text' size='20' id='".$paramName."' name='".$paramName."' value='".$paramDefault."'></TD><TD>".$paramUnit."</TD></TR>";
					}
					else
					{
						# The display of this parameter is dependent on the value of another
						if($_SESSION[$paramDependency]==$dependencyValue)
						{
							# Display the parameter option
							print "<TR><TD>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD><input type='text' size='5' id='".$paramName."' name='".$paramName."' value='".$paramDefault."'></TD><TD>".$paramUnit."</TD></TR>";
						}
					}
				}
			}
		}
		# SELECT BOX OPTION
		else
		{
			# THE OPTIONS ARE SEPARATED BY A COMMA. USE A STRING TOKENIZER TO SEPARATE THE OPTIONS AND PRINT THESE TO A BOX
			$options = strtok($paramOptions, ",");
			print "<TR><TD>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD><select name='".$paramName."' SIZE=5 onchange='valueselect(this.value,this.name)'>";

			while ($options !== false) 
			{
				print "<option value=\"$options\">$options</option>";
				$options = strtok(",");	
			}
			print "</select></TD></TR>";
		}

		$speciesParamCount=$speciesParamCount+1;
	}
}


##################################################################################################################
############################ PRINT THE MENU ######################################################################
##################################################################################################################

include("components/interfaceSettings.php");
include("components/menu.php");

PrintHeader("Simulation Experiment Specification",7);


print "<H2 ALIGN='LEFT'>Defining Species</H2>

<p align='left'>The final part of the protocol file is used to define the species involved in the simulation. Each species is defined from one of several different classes, and each is given a name and has some defining parameters. In the iDynoMiCS source it is possible to introduce nearly any type of species one would like, but at this point there are a few select species types that are possible to use: Bacterium, BactEPS, BactAdaptable, or ParticulateEPS, all found in the package simulator.agent.zoo in the iDynoMiCS source. <a href='https://github.com/kreft/iDynoMiCS/wiki/Species'>Each species class is described in detail in the iDynoMiCS Tutorial</a></p>";

################################################################################################################
################################################################################################################
################################### SPECIES DISPLAY SECTION ####################################################
################################################################################################################
################################################################################################################

if(count($_SESSION["speciesNames"])>0)
{
	print '<H2 align="left">Species Declared:</H2>
	<div id="displayTable"><TABLE align="left" cellspacing=7><TR>';

	$h=0;
	# KA - decision made here to only summarise the first 10 - we could change this later if wanted
	while($h<10)
	{
		print '<TD align="center"><b>'.$_SESSION["speciesParamNames"][$h].'</b></TD>';
		$h=$h+1;
	}

	print '</TR><TR>';

	# Now to output this data for the species so far
	for ($speciCount = 0; $speciCount<count($_SESSION["species"]); $speciCount++)
	{
		for ($paramCount = 0; $paramCount<10; $paramCount++)
		{
			print "<TD align='center'>".$_SESSION["species"][$speciCount][$paramCount]."</TD>";
		}
		print "<TR>";
		
	}


	print '</TABLE></div>';

	# Reset Button
	print '<form method="post" action="ResetInput.php">
	<INPUT type="submit" value="Reset Species" name="Reset">
	</form>';

	# Add a spacer to make page look better
	print '<div id="spacer"></div>';
}

 

################################################################################################################
################################################################################################################
################################### SPECIES DECLARATION SECTION ################################################
################################################################################################################
################################################################################################################

# SET THE FORM UP THAT THE USER WILL COMPLETE
print '<form name="ExpSetup" action="process_species.php" method="post">';

print '<HR><div id="displayTable"><TABLE align="left" WIDTH=60%>';
# NOW READ IN EACH OF THE SIMULATOR PARAMETERS, CREATING AN ENTRY FOR EACH
if(count($_SESSION["speciesParamNames"])>0)
{
	# Display the species parameters
	addSpeciesArraysToForm("species");
	
	# Now the initialisation parameters
	addSpeciesArraysToForm("speciesInit");
	
}

print "</TABLE></div>
<div id='bigSpacer'></div>";

# Now for the particles that can be declared as part of this species
# Hidden field will store the number of particles - useful for processing and laying out the form
print "<input type='hidden' id='particleCount' name='particleCount' value='0'>

<H3 align='left'>Particles</H3>
<p align='left'>This is the list of <i>particle</i> types that make up this species. Bacteria species will generally have biomass, inert, and capsule particles, while EPS will have only capsule. You may specify the initial mass of the compartment, or set it to 0 to allow a random value. Press Add Particle for each particle that you wish to add </p>";


print "<INPUT type='button' value='Add Particle' onclick=\"addRow('dataTable')\"><BR>";

print "<div id='displayTable'><TABLE id='dataTable' align='left'>";
        # Javascript takes care of this section completely - but the table still must be declared
    print "</TABLE></div><div id='spacer'></div><BR>

<H3 align='left' id='reactionsTitle'>Reactions</H3>
<p align='left' id='reactionsText'>Here, you should specify which of the reactions you declared previously involves this species. For Bacterium, BactEPS, and ParticulateEPS, you do this by ticking the box next to the name of the reaction. For BactAdaptable, more options are presented, allowing you to specify when reactions affect this species when a reaction switch is either on or off. To do this, tick the reaction to state that it affects this species, and then tick whether the reaction occurs when the switch is on or off. Finally, complete the condition that changes the setting of the switch. <a href='https://github.com/kreft/iDynoMiCS/wiki/BactAdaptable'>More information is available in the iDynoMiCS tutorial</a></p>";

# Now to consider reactions - what reactions this species is part of must be selected. 
print "<div id='displayTable'><TABLE id='reactions' align='left' cellpadding=10>";
        # Javascript takes care of this section completely - but the table still must be declared
    print "</TABLE></div><div id='spacer'></div><BR>";

# Now if this is a BactAdaptable species, we need to specify a condition when the reaction switches
print "<div id='displayTable'><TABLE id='reactionSwitch' align='left' cellpadding=10>";
        # Javascript takes care of this section completely - but the table still must be declared
    print "</TABLE></div><div id='spacer'></div><BR>

<HR>
<input type='submit' value='Add Species' name='addAnother'><input type='submit' value='Complete: Make Protocol File'>";

?>
