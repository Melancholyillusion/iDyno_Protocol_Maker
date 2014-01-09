<?php

session_start();
?>
<html>
<head>
<script language="javascript">

// Remove an element from the page by ID. Used to change between kinetic parameters
function remove(id)
{
    return (elem=document.getElementById(id)).parentNode.removeChild(elem);
}

function kineticSelected(kineticSelected, kineticName)
{
	// kineticName is the name of the kinetic selection box. This is useful as this contains the
	// row number of the table, necessary to add text boxes
	// So get the last character
	var rowNum = kineticName.charAt(kineticName.length-1);

	var table=document.getElementById("dataTable");
	var tblBodyObj = document.getElementById("dataTable").tBodies[0];

	// Now deal with the different kinetics - each requests different user input

	if(kineticSelected=="MonodKinetic")
	{
		deleteKineticOptions(rowNum,tblBodyObj);
		add_Solute_List_Box(rowNum,tblBodyObj);
		add_Ks_Option(rowNum,tblBodyObj,2,3);
	}

	// Simple inhibition instead specifies a Ki parameter:
	if(kineticSelected=="SimpleInhibition")
	{
		deleteKineticOptions(rowNum,tblBodyObj);
		add_Solute_List_Box(rowNum,tblBodyObj);
		add_Ki_Option(rowNum,tblBodyObj,2,3);
	}

	// Now to deal with Hill and Haldane Kinetics, that specify a second parameter
	if(kineticSelected=="HillKinetic")
	{
		deleteKineticOptions(rowNum,tblBodyObj);
		add_Solute_List_Box(rowNum,tblBodyObj);
		add_Ks_Option(rowNum,tblBodyObj,2,3);
		add_h_Option(rowNum,tblBodyObj,4,5);
	}

	if(kineticSelected=="HaldaneKinetic")
	{
		deleteKineticOptions(rowNum,tblBodyObj);
		add_Solute_List_Box(rowNum,tblBodyObj);
		add_Ks_Option(rowNum,tblBodyObj,2,3);
		add_Ki_Option(rowNum,tblBodyObj,4,5);
	}

	// Now just to cover the chance that the user chooses a kinetic and then changes this to FirstOrderKinetic, we need to
	// delete the options that were placed on the form
	if(kineticSelected=="FirstOrderKinetic")
	{
		deleteKineticOptions(rowNum,tblBodyObj);
	}

}

function add_Solute_List_Box(rowNum,tblBodyObj)
{
	// Create a name for this option box - solute plus the number of the kinetic
	var optionName = "solute"+rowNum;

	// The array of solutes in this simulation, translated to a javascript array	
	var soluteArray= <?php echo json_encode($_SESSION["soluteName"]); ?>;
	// The number of solutes in the array
	var numSolutes = <?php echo count($_SESSION["soluteName"]); ?>;

	// Now to create the select box with this information
	var soluteSelector = document.createElement("select");
	soluteSelector.setAttribute("name", optionName);
	soluteSelector.setAttribute("id", optionName);
	soluteSelector.setAttribute("size",5);

	// Now to add values for each solute the user has specified
	for(var i=0;i<numSolutes;i++)
	{
		var option;
		option = document.createElement("option");
		option.setAttribute("value", soluteArray[i]);
		option.innerHTML = soluteArray[i];
		soluteSelector.appendChild(option);
	}

	// Now we need to add a column into the solutes table and insert this selection box in the right place
	tblBodyObj.rows[rowNum].insertCell(-1);
	document.getElementById("dataTable").rows[rowNum].cells[1].id = "SoluteOption"+rowNum;
	document.getElementById("SoluteOption"+rowNum).appendChild(soluteSelector); 

}

function add_Ks_Option(rowNum,tblBodyObj,labelCol,inputCol)
{
	var ksOptionName = "ks"+rowNum;

	// Need a data entry box for parameter KS
	var ksLabel = tblBodyObj.rows[rowNum].insertCell(-1);
	ksLabel.innerHTML = "Ks:";
	tblBodyObj.rows[rowNum].cells[2].id = "ksLabel"+rowNum;
	var ksBox = tblBodyObj.rows[rowNum].insertCell(-1);
	tblBodyObj.rows[rowNum].cells[3].id = "ksOption"+rowNum;

	var ks = document.createElement("input");
	ks.setAttribute("name", ksOptionName);
	ks.setAttribute("id", ksOptionName);
	ks.setAttribute("size",5);

	document.getElementById("ksOption"+rowNum).appendChild(ks);

}

function add_Ki_Option(rowNum,tblBodyObj,labelCol,inputCol)
{
	var kiOptionName = "ki"+rowNum;

	var kiLabel = tblBodyObj.rows[rowNum].insertCell(-1);
	kiLabel.innerHTML = "Ki:";
	document.getElementById("dataTable").rows[rowNum].cells[labelCol].id = "kiLabel"+rowNum;
	var kiBox = tblBodyObj.rows[rowNum].insertCell(-1);
	document.getElementById("dataTable").rows[rowNum].cells[inputCol].id = "kiOption"+rowNum;

	var ki = document.createElement("input");
	ki.setAttribute("name", kiOptionName);
	ki.setAttribute("id", kiOptionName);
	ki.setAttribute("size",5);

	document.getElementById("kiOption"+rowNum).appendChild(ki);
}

function add_h_Option(rowNum,tblBodyObj,labelCol,inputCol)
{
	var hOptionName = "h"+rowNum;

	var hLabel = tblBodyObj.rows[rowNum].insertCell(-1);
	hLabel.innerHTML = "h:";
	document.getElementById("dataTable").rows[rowNum].cells[labelCol].id = "hLabel"+rowNum;
	var hBox = tblBodyObj.rows[rowNum].insertCell(-1);
	document.getElementById("dataTable").rows[rowNum].cells[inputCol].id = "hOption"+rowNum;

	var h = document.createElement("input");
	h.setAttribute("name", hOptionName);
	h.setAttribute("id", hOptionName);
	h.setAttribute("size",5);

	document.getElementById("hOption"+rowNum).appendChild(h); 
}

function deleteKineticOptions(rowNum,tblBodyObj)
{
	if(document.getElementById("SoluteOption"+rowNum)!=null)
	{
		remove("SoluteOption"+rowNum);
	}
	if(document.getElementById("kiLabel"+rowNum)!=null)
	{
		remove("kiLabel"+rowNum);
	}
	if(document.getElementById("kiOption"+rowNum)!=null)
	{
		remove("kiOption"+rowNum);
	}
	if(document.getElementById("ksLabel"+rowNum)!=null)
	{
		remove("ksLabel"+rowNum);
	}
	if(document.getElementById("ksOption"+rowNum)!=null)
	{
		remove("ksOption"+rowNum);
	}
	if(document.getElementById("hLabel"+rowNum)!=null)
	{
		remove("hLabel"+rowNum);
	}
	if(document.getElementById("hOption"+rowNum)!=null)
	{
		remove("hOption"+rowNum);
	}
	var columnCount = tblBodyObj.rows[rowNum].cells.length;
	for(var i=2;i<columnCount;i++)
	{
		tblBodyObj.rows[rowNum].deleteCell(i);
	}
}

function addRow(tableID) 
{
	x=eval(document.getElementById("KineticCount").value);

	var optionName = "kinetic"+x;

	var table=document.getElementById("dataTable");
	var row=table.insertRow(x);
	var cell1=row.insertCell(0);
	document.getElementById("dataTable").rows[x].cells[0].id = "kineticCell"+x;
	//var cell2=row.insertCell(1);
	
	var select = document.createElement("select");
	select.setAttribute("name", optionName);
	select.setAttribute("id", optionName);
	select.setAttribute("size",5);
	select.onchange = function() {kineticSelected(this.value,this.name)};

	var option;
	option = document.createElement("option");
	option.setAttribute("value", "FirstOrderKinetic");
	option.innerHTML = "FirstOrderKinetic";
	select.appendChild(option);

	var option;
	option = document.createElement("option");
	option.setAttribute("value", "MonodKinetic");
	option.innerHTML = "MonodKinetic";
	select.appendChild(option);

	var option;
	option = document.createElement("option");
	option.setAttribute("value", "HillKinetic");
	option.innerHTML = "HillKinetic";
	select.appendChild(option);

	var option;
	option = document.createElement("option");
	option.setAttribute("value", "HaldaneKinetic");
	option.innerHTML = "HaldaneKinetic";
	select.appendChild(option);

	var option;
	option = document.createElement("option");
	option.setAttribute("value", "SimpleInhibition");
	option.innerHTML = "SimpleInhibition";
	select.appendChild(option);


	document.getElementById("kineticCell"+x).appendChild(select); 

	document.getElementById("KineticCount").value=x+1;
}
 


function deleteRow(tableID) 
{
	try 
	{
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;

		for(var i=0; i<rowCount; i++) 
		{
			var row = table.rows[i];
			var chkbox = row.cells[0].childNodes[0];
			if(null != chkbox && true == chkbox.checked) 
			{
				table.deleteRow(i);
				rowCount--;
  				i--;
			}
		}
	}
	catch(e) 
	{
		window.alert(e);
	}
}

function setBoundaryClass(conditionChosen)
{
	if(conditionChosen!="FirstOrderKinetic")
	{
		document.getElementById("solutes[]").style.display = "";
	}
	else
	{
		document.getElementById("solutes[]").style.display = "none";
	}
	
}

function offOnLoad()
{
	
	//document.getElementById("solutes").style.display = "none";
}

</script>
<?php

##################################################################################################################
############################ PRINT THE MENU ######################################################################
##################################################################################################################

include("components/interfaceSettings.php");
include("components/menu.php");

PrintHeader("Simulation Experiment Specification",5);

################################################# START OF PHP PROTOCOL FILE MAKER SCRIPT ########################
## XMLFILE - THE FILE CONTAINING INFORMATION ON IDYNOMICS PARAMETERS
$xmlFile = 'protocolparameters.xml';

# READ IN THIS DOCUMENT
$doc = new DOMDocument();
$doc->load( $xmlFile );


print "<H2 ALIGN='LEFT'>Defining Reactions</H2>

<p align='left'>Reactions in which solutes or biomass are produced or consumed mediate the dynamics of the entire iDynoMiCS simulation. Any reaction may be defined via both stoichiometric and kinetic forms, and both of these representations are used in defining reactions in the protocol file. In general, reactions are specified using the ReactionFactor class because this allows the most general multi-factor reaction expression. Like for the solute and particle names, you may choose any name you would like for the reaction name attribute. You must also specify which particle type facilitates the reaction using the catalyzedBy attribute; during the simulation, this reaction will only occur if this particle type is present. <a href='https://github.com/kreft/iDynoMiCS/wiki/Reactions'>The tutorial explains each section of the reaction specification in detail</a></p>";

################################################################################################################
################################################################################################################
################################### REACTION DISPLAY SECTION ###################################################
################################################################################################################
################################################################################################################

if(count($_SESSION["reactions"])>0)
{
	print '<H2 align="left">Reactions Declared:</H2>';

	print '<div id="displayTable"><TABLE align="left"><TR>';

	# Do the headers first
	for($reactionParamCount=0;$reactionParamCount<count($_SESSION["reactionParamNames"])-2;$reactionParamCount++)
	{
		print "<TD align='center'><b>".$_SESSION["reactionParamNames"][$reactionParamCount]."</b></TD>";
	}
	# Add the solutes
	for($h=0;$h<count($_SESSION['soluteName']);$h++)
	{
		print "<TD align='center'><b>".$_SESSION['soluteName'][$h]."</b></TD>";
	}
	# Add the particles
	for($i=0;$i<count($_SESSION['particles'.$_SESSION['particleParamNames'][0]]);$i++)
	{
		print "<TD align='center'><b>".$_SESSION['particles'.$_SESSION['particleParamNames'][0]][$i]."</b></TD>";
	}
	# Add one for the kinetics
	print "<TD align='center'><b>Kinetics</b></TD></TR>";

	for($r=0;$r<count($_SESSION["reactions"]);$r++)
	{
		print "<TR>";
	
		for($reactionParamCount=0;$reactionParamCount<count($_SESSION["reactionParamNames"])-1;$reactionParamCount++)
		{
			# For ease of reading:
			$paramName = $_SESSION["reactionParamNames"][$reactionParamCount];
			if($paramName!="yield")
			{
				print "<TD align='center'>".$_SESSION["reactions"][$r][$reactionParamCount]."</TD>";

			}
			else
			{	
				for($s=0;$s<count($_SESSION["reactions"][$r][$reactionParamCount]);$s++)
				{
					# Get the yield value - we are going to check whether this is zero
					print "<TD align='center'>".$_SESSION["reactions"][$r][$reactionParamCount][$s][1]."</TD>";
				}
			}
		}

		# Now to add the kinetics - lets display just the names
		$kinetics = $_SESSION["reactions"][$r][$reactionParamCount];
		$kineticsChosen = "";

		foreach ($kinetics as $kin)
		{
			$kineticsChosen = $kineticsChosen.$kin[0]." ";
		}
		print "<TD align='center'>".$kineticsChosen."</TD>";
		print "</TR>";
	}
	print '</TABLE></div>';

	# Reset Button
	print '<form method="post" action="ResetInput.php">
	<INPUT type="submit" value="Reset Reactions" name="Reset">
	</form>';

	# Add a spacer to make page look better
	print '<div id="spacer"></div>';
}



 
################################################################################################################
################################################################################################################
################################### REACTION DECLARATION SECTION ###############################################
################################################################################################################
################################################################################################################


# SET THE FORM UP THAT THE USER WILL COMPLETE
print '<form name="Particles" action="process_reactions.php" method="post">';

print "<hr><p align='left'><b>Complete the details for the Reaction and press 'Add Reaction'. You must add all Reactions before pressing the 'Next Step' button</b></p>
<div id='displayTable'>
<TABLE align='left' width=70%>";

if(count($_SESSION["reactionParamNames"])>0)
{
	$reactionParamCount=0;
	# Note the -1, as we don't process kinetics - we do this differently
	while($reactionParamCount<count($_SESSION["reactionParamNames"])-1)
	{
		# For ease of reading:
		$paramName = $_SESSION["reactionParamNames"][$reactionParamCount];
		$paramDescription = $_SESSION["reactionParamDescription"][$reactionParamCount];
		$paramDefault = $_SESSION["reactionParamDefault"][$reactionParamCount];
		$paramType = $_SESSION["reactionParamType"][$reactionParamCount];
		$paramOptions = $_SESSION["reactionParamOptions"][$reactionParamCount];
		$paramUnit = $_SESSION["reactionParamUnits"][$reactionParamCount];

		if($paramName!="yield")
		{
			if(strlen($paramOptions)==0)
			{
				# IF ITS A BOOLEAN PARAMETER, WE'LL USE A CHECKBOX
				if($paramType=="boolean")
				{
					print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=40%>".$paramDescription."</TD><TD WIDTH=13%><input type='checkbox' id='".$paramName."' name='".$paramName."' value='1'></TD></TR>";

				}
				# NOT A BOOLEAN PARAMETER:
				else
				{
					print "<TR><TD WIDTH=13% >".$paramName."</TD><TD WIDTH=40%>".$paramDescription."</TD><TD WIDTH=13%><input type='text' size='25' id='".$paramName."' name='".$paramName."' value='".$paramDefault."'></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
				}
			}
			else
			{
				# Particles can only be biomass, inert, or capsule - as defined in the settings file. Thus give the options as stated there.
				print "<TR><TD WIDTH=13% >".$paramName."</TD><TD WIDTH=40%>".$paramDescription."</TD><TD WIDTH=13%><select name='".$paramName."' SIZE=5>";

				$options = strtok($paramOptions, ",");
		
				while ($options !== false) 
				{
					print "<option value=\"$options\">$options</option>";
					$options = strtok(",");	
				}
				print "</select></TD></TR>";
		
			}
		}
		else
		{
			print '</TABLE></div>

			<div id="spacer"></div>
			
			<H3 align="left">Reaction Yield</H3>
			<p align="left">Below, you should specify the yield coefficients of all components affected by this reaction. Negative values represent consumption of the component, and positive values represent production. Note that there is NO control on the chemical balance of the chosen stoichiometric coefficients: it is up to YOU to ensure that your model simulation matches reality, and that there is a mass balance in the equations.</p>
			
			<div id="displayTable"><TABLE align="left" width=20%>';

			# Yield is different, as we need to list the production and consumption of solutes and particles in this reaction. 
			# The solutes and particles have already been declared, so we can list these, and the user can fill in this information

			$h=0;
			$i=0;
			while($h<count($_SESSION['soluteName']))
			{
				# Write out the name of the solute
				print "<TR><TD WIDTH=13%>".$_SESSION['soluteName'][$h]."</TD>";
				# Now a text box for entering the production or consumption
				print "<TD WIDTH=13%><INPUT TYPE='TEXT' SIZE='5' ID='".$_SESSION['soluteName'][$h]."' NAME='".$_SESSION['soluteName'][$h]."'></TD>";
				# Now the unit
				print "<TD>".$paramUnit."</TD></TR>";

				$h=$h+1;
			}

			while($i<count($_SESSION['particles'.$_SESSION['particleParamNames'][0]]))
			{
				# Write out the name of the particle
				print "<TR><TD>".$_SESSION['particles'.$_SESSION["particleParamNames"][0]][$i]."</TD>";
				# Now a text box for entering the production or consumption
				print "<TD><INPUT TYPE='TEXT' SIZE='5' ID='".$_SESSION['particles'.$_SESSION["particleParamNames"][0]][$i]."' NAME='".$_SESSION['particles'.$_SESSION["particleParamNames"][0]][$i]."'></TD>";
				# Now the unit
				print "<TD>".$paramUnit."</TD></TR>";

			
				$i=$i+1;
			}
		}

		$reactionParamCount=$reactionParamCount+1;

	}
}
print "</TABLE></div>
<div id='spacer'></div>";


# Hidden field will store the number of kinetics - useful for processing and laying out the form
print "<input type='hidden' id='KineticCount' name='KineticCount' value='0'>

<H3 align='left'>Reaction Kinetics</H3>
<p align='left'>In this section, define the kinetic factors, which comprise the multiplicative terms that make up the entire reaction kinetic. Press the Add Kinetic button for each kinetic you wish to declare as part of this reaction</p>";

print "<INPUT type='button' value='Add Kinetic' onclick=\"addRow('dataTable')\">";


	# Now we need to deal with Kinetic Factors - the user can enter any number of these, and these all have different parameters. So this is fairly complex
	print "<div id='displayTable'><TABLE id='dataTable' border='0' align='left'>";
        # Javascript takes care of this section completely - but the table still must be declared
    print "</TABLE></div><BR>";


print '</TABLE>
<div id="spacer"></div>
<hr><input type="submit" name="addAnother" value="Add Reaction"><input type="submit" value="Next Step: Declare Agent Grid"></p>';

?>
