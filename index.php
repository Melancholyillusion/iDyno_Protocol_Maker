<?php
session_start();
ini_set('display_errors',0);
include("CommonFunctions.php");

print '<html>
<head>
<script>';

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
	if(parameterName=="adaptive")
	{
		if(document.getElementById(parameterName).checked != 0)
        	{	
			document.getElementById("timeStepMin").removeAttribute("disabled");
			document.getElementById("timeStepMax").removeAttribute("disabled");
		
		}

		else
		{
			document.getElementById("timeStepMin").setAttribute("disabled","disabled");
			document.getElementById("timeStepMax").setAttribute("disabled","disabled");
		}
	}
	else if(parameterName=="useAgentFile")
	{
		if(document.getElementById(parameterName).checked != 0)
		{
			document.getElementById("inputAgentFileURL").disabled=false;
		}
		else
		{
			document.getElementById("inputAgentFileURL").setAttribute("disabled","disabled");
		}
	}
	else if(parameterName=="useBulkFile")
	{
		if(document.getElementById(parameterName).checked != 0)
		{
			document.getElementById("inputBulkFileURL").disabled=false;
		}
		else
		{
			document.getElementById("inputBulkFileURL").setAttribute("disabled","disabled");
		}
	}
}

</script></head>';

##################################################################################################################
############################ PRINT THE MENU ######################################################################
##################################################################################################################

include("components/interfaceSettings.php");
include("components/menu.php");

PrintHeader("Simulation Experiment Specification",1);

print "<H1 ALIGN='LEFT'>iDynoMiCS Protocol File Maker</H1>";

print "<p align='left'>iDynoMiCS uses plain-text XML files in order to define the simulation to run; these files are called <i>Protocol Files</i>. In a protocol file, an XML mark-up is used to define the simulation parameters, including details such as run time, simulated solutes, simulated agents, and included reactions. This tool goes through the creation of a protocol file step by step, creating a protocol file that can be downloaded after the final step.<BR><BR> Although this tool makes generating protocol files much easier, it is recommended that you still study the iDynoMiCS tutorial in detail to ensure you understand the role of each parameter in the simulation</p><hr>";

################################################# START OF PHP PROTOCOL FILE MAKER SCRIPT ########################
## XMLFILE - THE FILE CONTAINING INFORMATION ON IDYNOMICS PARAMETERS
$xmlFile = 'protocolparameters.xml';

# READ IN THIS DOCUMENT
$doc = new DOMDocument();
$doc->load( $xmlFile );

# SET THE FORM UP THAT THE USER WILL COMPLETE
print '<form name="ExpSetup" action="process_SimSetup.php" method="post">';

################################################################################################################
#### A: SIMULATOR SECTION OF PROTOCOL FILE

print "<H2 ALIGN='LEFT'>Simulation Setup Parameters</H2>";

$allOutputs = $doc->getElementsByTagName("simulatorparam");

print '<TABLE>';
# NOW READ IN EACH OF THE SIMULATOR PARAMETERS, CREATING AN ENTRY FOR EACH
foreach($allOutputs as $param)
{
	$paramName = getTagValue($param,"name");
	$paramDescription = getTagValue($param,"description");
	$paramDefault = getTagValue($param,"default");
	$paramType = getTagValue($param,"type");
	$paramOptions = getTagValue($param,"options");
	$paramUnit = getTagValue($param,"unit");

	# TEST IF WE'RE NEEDING A SELECTION BOX
	if (empty($paramOptions)) 
	{
		# IF ITS A BOOLEAN PARAMETER, WE'LL USE A CHECKBOX
		if($paramType=="boolean")
		{
			# NOW FOR THE ADAPTIVE TIMESTEP PARAMETER, WE'LL USE EXTRA PART OF THE CHECKBOX WHICH TURNS TIMESTEPINI AND TIMESTEPMAX ON AND OFF DEPENDENT
			# ON WHETHER THE BOX IS SELECTED
			if($paramName=="adaptive")
			{
				print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='checkbox' id='".$paramName."' name='".$paramName."' value='1' onClick='disable_enable(\"".$paramName."\")'";
			}
			else
			{
				print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='checkbox' id='".$paramName."' name='".$paramName."' value='1'";
			}

			if(!isset($_SESSION[$paramName]))
			{
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
				# The user has returned here from a previous page - show their selection
				if($_SESSION[$paramName]=="true")
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
			# DISABLE THE INPUT FOR TIMESTEPINI AND TIMESTEP MAX UNTIL TIMESTEP ADAPTIVE IS TRUE
			if($paramName=="timeStepMin" or $paramName=="timeStepMax" )
			{
				if(!isset($_SESSION[$paramName]))
				{
					print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='text' size='5' id='".$paramName."' name='".$paramName."' value='".$paramDefault."' disabled></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
				}
				else
				{
					print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='text' size='5' id='".$paramName."' name='".$paramName."' value='".$_SESSION[$paramName]."' disabled></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
				}
			}
			# NO NOT DISABLED
			else
			{
				if(!isset($_SESSION[$paramName]))
				{
					if($paramName=="protocolFileName")
					{
						print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='text' size='20' id='".$paramName."' name='".$paramName."' value='".$paramDefault."'></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
					}
					else
					{
						print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='text' size='5' id='".$paramName."' name='".$paramName."' value='".$paramDefault."'></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
					}
				}
				else
				{
					if($paramName=="protocolFileName")
					{
						print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='text' size='20' id='".$paramName."' name='".$paramName."' value='".$_SESSION[$paramName]."'></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
					}
					else
					{
						print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=8%><input type='text' size='5' id='".$paramName."' name='".$paramName."' value='".$_SESSION[$paramName]."'></TD><TD WIDTH=13%>".$paramUnit."</TD></TR>";
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
		print "<TR><TD WIDTH=13%>".$paramName."</TD><TD WIDTH=60%>".$paramDescription."</TD><TD WIDTH=13%><select name='".$paramName."' SIZE=5>";

		while ($options !== false) 
		{
			if(isset($_SESSION[$paramName]))
			{
				if($_SESSION[$paramName]==$options)
				{
					print "<option value=\"$options\" selected>$options</option>";
				}
				else
				{
					print "<option value=\"$options\">$options</option>";
				}
			}
			else
			{
				print "<option value=\"$options\">$options</option>";
			}
			$options = strtok(",");	
		}
		print "</select></TD></TR>";
	}
}
print "</TABLE><HR>";

####################################################################################################################################################
####################################################################################################################################################
###### B: INPUT SECTION

print "<H2 ALIGN='LEFT'>Starting From Previous State Files</H2>";

print "<p align='left'>This section allows you to specify existing state files that should be used as the initial condition for a new simulation. If you are not restarting a simulation, you can leave this section blank. Note this is different from the restartPreviousRun parameter above, because these inputs are meant for use in a new simulation starting from time 0 rather than for restarting a previous simulation. Setting the flag to true or false will use the input file as needed; just be sure that the path to the input file is correct (it is best to copy the input file to the local directory). Note that the input files MUST be consistent with the agent and solute descriptions defined later in the protocol file, or otherwise the simulation will give an error. </p>";

print '<TABLE WIDTH="100%">';
$inputSection = $doc->getElementsByTagName("inputparam");

# NOW READ IN EACH OF THE SIMULATOR PARAMETERS, CREATING AN ENTRY FOR EACH
foreach($inputSection as $param)
{
	$paramName = getTagValue($param,"name");
	$paramDefault = getTagValue($param,"default");
	$paramType = getTagValue($param,"type");
	$paramOptions = getTagValue($param,"options");
	$paramUnit = getTagValue($param,"unit");

	if($paramType=="boolean")
	{
		# NOW FOR THE USE BULK AND USE AGENT FILE BOOLEANS, WE'LL USE EXTRA PART OF THE CHECKBOX WHICH TURNS THE AGENT AND BULK FILES ON AND OFF DEPENDENT
		# ON WHETHER THE BOX IS SELECTED
		if($paramName=="useAgentFile" or $paramName=="useBulkFile")
		{
			print "<TR><TD WIDTH='20%'>".$paramName."</TD><TD><input type='checkbox' id='".$paramName."' name='".$paramName."' value='1' onClick='disable_enable(\"".$paramName."\")'";
		}
		else
		{
			print "<TR><TD>".$paramName."</TD><TD><input type='checkbox' id='".$paramName."' name='".$paramName."' value='1'";
		}

		# SET THE CHECKBOX TICKED IF THE DEFAULT IS TO BE TICKED
		if(!isset($_SESSION[$paramName]))
		{
			if($paramDefault=="true")
			{
				print "checked></TD><TD>".$paramUnit."</TD></TR>";
			}
			else
			{
				print "unchecked></TD><TD>".$paramUnit."</TD></TR>";
			}
		}
		else
		{
			if($_SESSION[$paramName]=="true")
			{
				print "checked></TD><TD>".$paramUnit."</TD></TR>";
			}
			else
			{
				print "unchecked></TD><TD>".$paramUnit."</TD></TR>";
			}

		}
	}
	# NOT A BOOLEAN PARAMETER:
	else
	{
		# DISABLE THE INPUT FOR AGENT AND BULK FILE LOCATIONS UNTIL THE USE OF THESE FILES IS SET TO TRUE
		if($paramName=="inputAgentFileURL" or $paramName=="inputBulkFileURL")
		{
			if(!isset($_SESSION[$paramName]))
			{
				print "<TR><TD>".$paramName."</TD><TD><input type='text' size='25' id='".$paramName."' name='".$paramName."' value='".$paramDefault."' disabled></TD><TD>".$paramUnit."</TD></TR>";
			}
			else
			{
				print "<TR><TD>".$paramName."</TD><TD><input type='text' size='25' id='".$paramName."' name='".$paramName."' value='".$_SESSION[$paramName]."' disabled></TD><TD>".$paramUnit."</TD></TR>";
			}
		}
		# NO NOT DISABLED
		else
		{
			if(!isset($_SESSION[$paramName]))
			{
				print "<TR><TD>".$paramName."</TD><TD><input type='text' size='25' id='".$paramName."' name='".$paramName."' value='".$paramDefault."'></TD><TD>".$paramUnit."</TD></TR>";
			}
			else
			{
				print "<TR><TD>".$paramName."</TD><TD><input type='text' size='25' id='".$paramName."' name='".$paramName."' value='".$_SESSION[$paramName]."'></TD><TD>".$paramUnit."</TD></TR>";
			}
		}

	}
}

print "</TABLE><HR>";


print "<input type='submit' value='Next Step: Declare Solutes'></p>
</form>";
?>
