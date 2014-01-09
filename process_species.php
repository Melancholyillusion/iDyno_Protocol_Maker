<?php

session_start();
ini_set('display_errors',0);

if(isset($_POST['addAnother']))
{
	#$xmlFile = 'protocolparameters.xml';
	#$doc = new DOMDocument();
	#$doc->load( $xmlFile );

	#$allOutputs = $doc->getElementsByTagName("speciesparam");

	# The species array is an array of arrays of species. So we need an individual array here
	$individualSpecies = array();

	for($speciesParamCount=0; $speciesParamCount<count($_SESSION["speciesParamNames"]); $speciesParamCount++)
	{
		# Now we can push the attributes into the individual array
		if($_SESSION["speciesParamType"][$speciesParamCount]=="boolean")
		{
			if(isset($_POST[$_SESSION['speciesParamNames'][$speciesParamCount]]))
			{
				array_push($individualSpecies,"true");
			}
			else
			{
				array_push($individualSpecies,"false");
			}
		}
		else
		{
			array_push($individualSpecies,$_POST[$_SESSION['speciesParamNames'][$speciesParamCount]]);

			# If this is name or class, store separately to make processing particles easier
			if($_SESSION['speciesParamNames'][$speciesParamCount]=="name")
			{
				array_push($_SESSION["speciesNames"],$_POST[$_SESSION['speciesParamNames'][$speciesParamCount]]);
			}
			if($_SESSION['speciesParamNames'][$speciesParamCount]=="class")
			{
				array_push($_SESSION["speciesClass"],$_POST[$_SESSION['speciesParamNames'][$speciesParamCount]]);
				# Also useful for later in this document too
				$speciesClassProcessing = $_POST[$_SESSION['speciesParamNames'][$speciesParamCount]];
			}
		}
	}

	# Now to do the initialisation of the species for either one time or self attachment
	# These are in the speciesInit array
	$speciesInitialSetup = array();

	for($speciesInitParamCount=0; $speciesInitParamCount<count($_SESSION["speciesInitParamNames"]); $speciesInitParamCount++)
	{
		array_push($speciesInitialSetup,$_POST[$_SESSION["speciesInitParamNames"][$speciesInitParamCount]]);
	}

	# Now add this to the species array
	array_push($individualSpecies,$speciesInitialSetup);

	# Now we have to process particles
	# Firstly get the number of particles that the user declared
	$numParticles = $_POST["particleCount"];
	
	# A species can have more than one particle. As this is the case, each is stored in an array, and the total for 
	# the reaction stored in an array of arrays, and then added to the reaction array
	$totalParticlesForThisSpecies = array();

	$p=0;
	while($p<$numParticles)
	{
		# Need a new array for this kinetic
		$individualParticle = array();

		# Now push the name of the particle into the array
		array_push($individualParticle,$_POST["particle".$p]);
		

		# Now we need to determine if this has an EPS species associated with it
		if($_POST["particle".$p]=="capsule" && ($speciesClassProcessing=="Bacterium" || $speciesClassProcessing=="BactAdaptable" || $speciesClassProcessing=="BactEPS"))
		{
			# There should be an associated EPS particle declared - get this
			array_push($individualParticle,$_POST["eps".$p]);
		
		}


		# Now get the mass assigned to this particle
		array_push($individualParticle,$_POST["mass".$p]);
		

		# Now push this into the totalParticlesForThisSpecies array
		array_push($totalParticlesForThisSpecies,$individualParticle);

		$p=$p+1;
	}

	# Push all the particles for this species into into array for this Species
	array_push($individualSpecies,$totalParticlesForThisSpecies);

	# Now to deal with reactions that this species is involved in
	$reactionsForThisSpeci = array();

	$switchOff = array();
	$switchOn = array();

	for($reactionCount=0;$reactionCount<count($_SESSION["namesOfSpecifiedReactions"]);$reactionCount++)
	{
		# Push the name of the reaction into the array. We make the assumption that the reaction is active
		if($_POST["Chk".$_SESSION["namesOfSpecifiedReactions"][$reactionCount]]=="True")
		{
			array_push($reactionsForThisSpeci,$_SESSION["namesOfSpecifiedReactions"][$reactionCount]);

			# Now to deal with Bact Adaptable, with the switch system
			if($speciesClassProcessing=="BactAdaptable")
			{
				# See if the reaction is on when the switch is off
				if($_POST["Chk_off_".$_SESSION["namesOfSpecifiedReactions"][$reactionCount]]=="True")
				{
					print 'OFF TRUE<BR>';
					# Push the name of the reaction into the switchOff array
					array_push($switchOff,$_SESSION["namesOfSpecifiedReactions"][$reactionCount]);
				}
				
				# See if the reaction is on when the switch is on
				if($_POST["Chk_on_".$_SESSION["namesOfSpecifiedReactions"][$reactionCount]]=="True")
				{
					# Push the name of the reaction into the switchOff array
					array_push($switchOn,$_SESSION["namesOfSpecifiedReactions"][$reactionCount]);
				}

			}
		}
	}

	# Now we also have to deal with the switch lag and the condition under which this switch is changed
	if($speciesClassProcessing=="BactAdaptable")
	{
		# Store the reaction switch conditions in an array
		$reactionSwitchConditions = array();
		
		# Now push the switch lag on time, off time, solute or biomass, and the concentration mass into the array
		array_push($reactionSwitchConditions,$_POST["switchLagBoxoff"]);
		print $_POST["switchLagBoxoff"]."<BR>";

		array_push($reactionSwitchConditions,$_POST["switchLagBoxon"]);
		print $_POST["switchLagBoxon"]."<BR>";

		array_push($reactionSwitchConditions,$_POST["switchConditionSelector"]);

		array_push($reactionSwitchConditions,$_POST["criteriaSelector"]);

		array_push($reactionSwitchConditions,$_POST["switchTriggerInput"]);

	}


	# Push the reactions into the individual array
	array_push($individualSpecies,$reactionsForThisSpeci);

	# Now push the OFF into the speci array
	array_push($individualSpecies, $switchOff);

	# Now push the ON into the speci array
	array_push($individualSpecies, $switchOn);

	# Now for the switch conditions
	array_push($individualSpecies, $reactionSwitchConditions);

	# Finally, add the individual to the species array
	array_push($_SESSION["species"],$individualSpecies);

	header('Location: AddSpecies.php') ;
}
else
{
	# NEEDS CHANGING TO WHERE WE'RE GOING AFTER SOLUTES
	header('Location: process_Protocol_File.php') ;
	
}


	

