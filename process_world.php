<?php

# ALL PHP SCRIPTS FOR THIS FUNCTIONALITY ARE USING SESSION VARIABLES, SO MUST START THE SESSION EACH TIME
session_start();
ini_set('display_errors',0);

# LOCATION OF THE FILE WITH iDYNOMICS PROTOCOL FILE PARAMETER DETAILS
$xmlFile = 'protocolparameters.xml';

#$allOutputs = $doc->getElementsByTagName("worldParam");

if(isset($_POST['addAnother']))
{
	#############################################################################
	#############################################################################
	########################### BULK ############################################
	#############################################################################
	#############################################################################
	if($_POST["worldType"]=="Bulk")
	{
		$newBulk = array();
	
		# Check that the bulk is called chemostat, if not set it to be chemostat
		if($_SESSION["chemostat_sim"]=="True")
		{
			if($_POST["worldName"]!="chemostat")
			{
				# We're going to show the user a message showing we changed the name once the world page reloads. This is done at the top of
				# AddWorlds.php
				$_SESSION["chemoNameMessage"]="True";
			}
			array_push($newBulk,"chemostat");

			# Also store in list of bulk names - useful for processing later
			array_push($_SESSION["bulkNames"],"chemostat");
		}
		else
		{
			array_push($newBulk,$_POST["worldName"]);
			array_push($_SESSION["bulkNames"],$_POST["worldName"]);
		}

		# Now we need to process the bulk parameters
		$h=1;
		while($h<count($_SESSION["bulkParamNames"]))
		{
			# Check for the isConstant parameter - this is not entered for chemostats
			if($_SESSION["bulkParamNames"][$h]=="isConstant" && $_SESSION["chemostat_sim"]=="True")
			{
				array_push($newBulk,"false");
			}
			else
			{
				array_push($newBulk,$_POST[$_SESSION["bulkParamNames"][$h]]);
			}
			$h=$h+1;
		}

		#bulkSolutesArray - an array of arrays - one for each solute in this bulk
		$bulkSolutesArray = array();		
		
		for ($solute = 0; $solute<count($_SESSION["soluteName"]); $solute++)
		{
			# Now see if the solute has been selected - if so we need to store details of that solute for this bulk
			if(isset($_POST["Chk_".$_SESSION["soluteName"][$solute]]))
			{
				# Now store the value for each parameter for this selected solute
				# We need an array just for the values of this solute
				$soluteValueArray = array();
				array_push($soluteValueArray,$_SESSION["soluteName"][$solute]);

				$p=0;
				while($p<count($_SESSION["bulkSoluteParamNames"]))
				{
					if($_SESSION["bulkSoluteParamType"][$p]!="boolean")
					{
						# Push the parameter into the array if not boolean
						array_push($soluteValueArray,$_POST[$_SESSION["soluteName"][$solute].$_SESSION["bulkSoluteParamNames"][$p]]);
					}
					else
					{
						# If boolean, we need to set this to false if not selected
						if(empty($_POST[$_SESSION["soluteName"][$solute].$_SESSION["bulkSoluteParamNames"][$p]]))
						{
							# If not ticked, set the parameter value to false, if not set to true
							array_push($soluteValueArray,"false");
						}
						else
						{
							array_push($soluteValueArray,"true");
						}
			
					}
					$p=$p+1;
				}

				# Now push this solute into the array of solutes for this bulk
				array_push($bulkSolutesArray,$soluteValueArray);
			}
		}

		# Now add the solutes for this bulk to the storage for this bulk
		array_push($newBulk,$bulkSolutesArray);

		# Now push this bulk into the array of bulks
		array_push($_SESSION["bulks"],$newBulk);

	}
	else
	{
		#############################################################################
		#############################################################################
		#################### COMPUTATION DOMAIN #####################################
		#############################################################################
		#############################################################################

		$newCD = array();

		# Check that the domain is called chemostat, if not set it to be chemostat
		if($_SESSION["chemostat_sim"]=="True")
		{
			if($_POST["worldName"]!="chemostat")
			{
				# We're going to show the user a message showing we changed the name once the world page reloads. This is done at the top of
				# AddWorlds.php
				$_SESSION["chemoNameMessage"]="True";
			}
			array_push($newCD,"chemostat");
		}
		else
		{
			array_push($newCD,$_POST["worldName"]);
		}

		# Now we need to process the cd parameters
		$h=1;
		while($h<count($_SESSION["computationDomainParamNames"]))
		{
			array_push($newCD,$_POST[$_SESSION["computationDomainParamNames"][$h]]);
			
			# A small cheat here - we're going to store whether the sim is 3D. This
			# is very useful for later
			if($_SESSION["computationDomainParamNames"][$h]=="nK")
			{
				if($_POST[$_SESSION["computationDomainParamNames"][$h]]==1)
				{
					$_SESSION["is3D"] = "False";
				}	
				else
				{
					$_SESSION["is3D"] = "True";
				}
			}
			$h=$h+1;

		}

		

		#############################################################################
		#############################################################################
		######### COMPUTATION DOMAIN BOUNDARY CONDITIONS ############################
		#############################################################################
		#############################################################################

		# Set up an array for all boundary conditions
		$cdBoundaryConditions = array();

		# Now to get the info on all the arrays
		$b=0;
		while($b<count($_SESSION["boundaries"]))
		{
			# Now we need an array for each boundary
			$individualBoundary = array();

			# FIRSTLY, PUSH THE NAME OF THE BOUNDARY TO THE ARRAY
			array_push($individualBoundary,$_SESSION["boundaries"][$b]);

			# NOW WE NEED THE BOUNDARY CONDITION THAT WAS SELECTED
			array_push($individualBoundary,$_POST[$_SESSION["boundaries"][$b]."_Condition"]);
			
			# NOW THE SHAPE
			array_push($individualBoundary,$_POST[$_SESSION["boundaries"][$b]."_Shape"]);

			# Now get the relevant parameters if these exist
			if($_POST[$_SESSION["boundaries"][$b]."_Condition"]=="BoundaryBulk" || $_POST[$_SESSION["boundaries"][$b]."_Condition"]=="BoundaryGasMembrane")
			{
				if(isset($_POST[$_SESSION["boundaries"][$b]."_Param1"]))
					array_push($individualBoundary,$_POST[$_SESSION["boundaries"][$b]."_Param1"]);

				if(isset($_POST[$_SESSION["boundaries"][$b]."_Param2"]))
					array_push($individualBoundary,$_POST[$_SESSION["boundaries"][$b]."_Param2"]);

				if(isset($_POST[$_SESSION["boundaries"][$b]."_Param3"]))
					array_push($individualBoundary,$_POST[$_SESSION["boundaries"][$b]."_Param3"]);
			}

			# Now add to the array of boundary conditions for this bulk
			array_push($cdBoundaryConditions,$individualBoundary);
			
			$b=$b+1;

		}

		# Now add the boundaries to the definition of this bulk
		array_push($newCD,$cdBoundaryConditions);
		
		# Now push this bulk into the array of bulks
		array_push($_SESSION["computationDomains"],$newCD);
				
	}


	header('Location: AddWorlds.php') ;
}
else
{
	# NEEDS CHANGING TO WHERE WE'RE GOING AFTER SOLUTES
	header('Location: IntroReactions.php') ;
	
}


	

