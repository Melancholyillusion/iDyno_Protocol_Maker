<?php
session_start();
ini_set('display_errors',0);

include("CommonFunctions.php");

if($_SESSION["chemostat_sim"] == "True")
{
	# Skip straight to the species, as agent grid does not need to be defined for the chemostat
	echo "<script>
	alert('As this is a chemostat simulation, the Agent Grid does not need to be defined. Skipping to definition of Species');
	window.location.href='Setup_Species_Storage.php';
	</script>";

}
else
{
	print '<html>
	<head>
	<script>';

	// Function to check all boundaries have been chosen - this stops PHP errors later
	print 'function ValidateForm()
	{
		// get selection object
		var sel = document.getElementById(computationDomain);
		// check if its index is not 0 (the first selection is invalid).
		if (sel.selectedIndex == -1)
		{
			alert ("Select a computation domain); 
			return false;
		}
	}';

	# Function to hide the parameters for receptors that are not selected for inclusion in the simulation. Once the checkbox is ticked for the
	# receptor to be included, parameters appear.
	print 'function toggle(table) {
	 if( document.getElementById(table).style.display=="none" ){
	   document.getElementById(table).style.display = "";
	 }else{
	   document.getElementById(table).style.display = "none";
	 }
	}';

	# Function to grey out sensitivity analysis boxes where the parameter is not to be included in the analysis
	print 'function disable_enable(parameterName)
	{
	
		if(document.getElementById(parameterName).checked != 0)
		{	
			document.getElementById("timeStepMin").removeAttribute("disabled");
			document.getElementById("timeStepMax").removeAttribute("disabled");';
		
			# If activates / deactivates increment entry box if this is a robustness analysis experiment
		print '}

		else
		{
			document.getElementById("timeStepMin").setAttribute("disabled","disabled");
			document.getElementById("timeStepMax").setAttribute("disabled","disabled");';
		print '}
	}

	</script></head>';

	##################################################################################################################
	############################ PRINT THE MENU ######################################################################
	##################################################################################################################

	include("components/interfaceSettings.php");
	include("components/menu.php");

	PrintHeader("Simulation Experiment Specification",6);

	# SET THE FORM UP THAT THE USER WILL COMPLETE
	print '<form name="ExpSetup" action="process_AgentGrid.php" method="post">';

	print "<H2 ALIGN='LEFT'>Defining the Agent Grid</H2>

	<p align='left'>All agents in biofilm simulations are stored and tracked using a grid called the agentGrid, which is similar to the grid for the solutes but serves a different purpose unique to agents. There are several parameters that need to be specified for the grid. <a href='https://github.com/kreft/iDynoMiCS/wiki/agentGrid'>A detailed description of each parameter is included in the iDynoMiCS tutorial</a></p>";

	print '<div id="spacer"></div><hr>';
	print '<TABLE>';
	for($gridParam=0;$gridParam<count($_SESSION["agentGridParams"]);$gridParam++)
	{

		# For ease of reading:
		$paramName = $_SESSION["agentGridParams"][$gridParam];
		$paramDescription = $_SESSION["agentGridDescription"][$gridParam];
		$paramDefault = $_SESSION["agentGridDefaults"][$gridParam];
		$paramType = $_SESSION["agentGridType"][$gridParam];
		$paramOptions = $_SESSION["agentGridOptions"][$gridParam];
		$paramUnit = $_SESSION["agentGridUnit"][$gridParam];
		
		# TEST IF WE'RE NEEDING A SELECTION BOX
		if (empty($paramOptions)) 
		{
			if($paramName!="computationDomain")
			{
				# IF ITS A BOOLEAN PARAMETER, WE'LL USE A CHECKBOX
				if($paramType=="boolean")
				{
					if(!isset($_SESSION["agentGrid"][$gridParam]))
					{
			
						print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='checkbox' id='".$paramName."' name='".$paramName."' value='true'";
			
						# SET THE CHECKBOX TICKED IF THE DEFAULT IS TO BE TICKED
						if($paramDefault=="true")
						{
							print "checked></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
						}
						else
						{
							print "unchecked></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
						}
					}
					else
					{
						print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='checkbox' id='".$paramName."' name='".$paramName."' value='true'";

						if($_SESSION["agentGrid"][$gridParam]=="true")
						{
							print "checked></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";

						}
						else
						{
							print "unchecked></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
						}


					}
				}
				# NOT A BOOLEAN PARAMETER:
				else
				{
					if(!isset($_SESSION["agentGrid"][$gridParam]))
					{
						print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='text' size='5' id='".$paramName."' name='".$paramName."' value='".$paramDefault."'></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
					}
					else
					{
						# Put the value that has been entered in this field
						print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='text' size='5' id='".$paramName."' name='".$paramName."' value='".$_SESSION["agentGrid"][$gridParam]."'></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
					}
			

				}
			}
			else
			{
				# Domain names have already been defined - so we can give the user the option of those that already exist in the protocol file
				print "<TR><TD WIDTH=13% >".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=13%><select name='".$paramName."' SIZE=5>";

				$h=0;
				while($h<count($_SESSION["computationDomains"]))
				{
					# Name of the computation domain is the first element of each computation domain array
					$worldName = $_SESSION["computationDomains"][$h][0];
					#print $worldName."<BR>";
					print "<option value=\"$worldName\">$worldName</option>";
					$h=$h+1;


				}
				print "</select></TD></TR>";

			}
		}
		# SELECT BOX OPTION
		else
		{
			# THE OPTIONS ARE SEPARATED BY A COMMA. USE A STRING TOKENIZER TO SEPARATE THE OPTIONS AND PRINT THESE TO A BOX
			$options = strtok($paramOptions, ",");
			print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=13%><select name='".$paramName."' SIZE=5>";

			while ($options !== false) 
			{
				print "<option value=\"$options\" selected>$options</option>";
				$options = strtok(",");	
			}
			print "</select></TD></TR>";
		}
	}
	print "</TABLE><HR>";

	print "<input type='submit' value='Next Step: Define Species' onclick='return ValidateForm()'></p>
	</form>";
}
?>
