<?php

session_start();
ini_set('display_errors',0);

if(isset($_POST['addAnother']))
{
	# Create an array to store the details on this reaction
	$individualReaction = array();

	#$xmlFile = 'protocolparameters.xml';
	#$doc = new DOMDocument();
	#$doc->load( $xmlFile );

	#$allOutputs = $doc->getElementsByTagName("reactionParam");

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
			# Push the form input into the array if not dealing with yield
			array_push($individualReaction,$_POST[$paramName]);

			# Store the names - this is useful when specifying species
			if($paramName=="name")
			{
				array_push($_SESSION["namesOfSpecifiedReactions"],$_POST[$paramName]);
			}
	
		}
		else
		{
			# Now the entry screen gave the user the list of declared solutes and particles, where they can enter the consumption or production of that solute
			# or particle
			# So we need to go through these and declare them in the protocol file if there is any production or consumption
			# The reaction array will contain an array, called yieldArray. This will be an array of all solutes and particles that are yielded by the reaction
			$yieldArray = array();

			$s=0;
			while($s<count($_SESSION['soluteName']))
			{
				# The IntroReactions.php script set up an array for each of the declared solutes or particles. Enter the consumption or production figure in 
				# this array. If there is no value, 0 is assumed
				$yieldOfSolute = array(); # Array contains the name of the solute and the yield from the form
				array_push($yieldOfSolute,$_SESSION['soluteName'][$s]);
			
				if(strlen($_POST[$_SESSION['soluteName'][$s]])>0)
				{
					array_push($yieldOfSolute,$_POST[$_SESSION['soluteName'][$s]]);
				}
				else
				{
					array_push($yieldOfSolute,0);
				}
				
				# Push this into the yield array
				array_push($yieldArray,$yieldOfSolute);

				$s=$s+1;
			}

			# Now do the same for particles
			$p=0;				
			while($p<count($_SESSION['particles'.$_SESSION['particleParamNames'][0]]))
			{
				$yieldOfSolute = array(); # Array contains the name of the particle and the yield from the form
				array_push($yieldOfSolute,$_SESSION['particles'.$_SESSION["particleParamNames"][0]][$p]);

				if(strlen($_POST[$_SESSION['particles'.$_SESSION["particleParamNames"][0]][$p]])>0)
				{
					array_push($yieldOfSolute,$_POST[$_SESSION['particles'.$_SESSION["particleParamNames"][0]][$p]]);
				}
				else
				{
					# Push 0 into the array. The XML file maker will deal with whether this is printed or not
					array_push($yieldOfSolute,0);	
					
				}

				# Push this into the yield array
				array_push($yieldArray,$yieldOfSolute);

				$p=$p+1;

			}
			

			# Now push the yield array into the reaction
			array_push($individualReaction,$yieldArray);


		}
		$reactionParamCount=$reactionParamCount+1;
	}

	# Now deal with the kinetics
	# Firstly get the number of kinetics that the user declared
	$numKinetics = $_POST["KineticCount"];
	
	# A reaction can have more than one kinetic. As this is the case, each is stored in an array, and the total for 
	# the reaction stored in an array of arrays, and then added to the reaction array
	$totalKineticsForThisReaction = array();

	$k=0;
	while($k<$numKinetics)
	{
		# Need a new array for this kinetic
		$individualKinetic = array();
		
		# Now push the kinetic being used
		array_push($individualKinetic,$_POST["kinetic".$k]);

		# Now if this is not FirstOrderKinetic, we need to deal with the parameters too.
		if($_POST["kinetic".$k]!="FirstOrderKinetic")
		{
			# Now get the solute name from the form
			array_push($individualKinetic,$_POST["solute".$k]);

			# Now to get the parameters for the different types of kinetics
			if($_POST["kinetic".$k]=="MonodKinetic" || $_POST["kinetic".$k]=="HaldaneKinetic" || $_POST["kinetic".$k]=="HillKinetic")
			{
				# Get parameter Ks
				array_push($individualKinetic,$_POST["ks".$k]);
			}
			if($_POST["kinetic".$k]=="SimpleInhibition" || $_POST["kinetic".$k]=="HaldaneKinetic")
			{
				# Get parameter Ki
				array_push($individualKinetic,$_POST["ki".$k]);
			}
			if($_POST["kinetic".$k]=="HillKinetic")
			{
				# Get parameter h
				array_push($individualKinetic,$_POST["h".$k]);
			}

		}

		# Now push this individual array into the total array
		array_push($totalKineticsForThisReaction,$individualKinetic);
		$k=$k+1;
	}

	# Now push the kinetics into the array for this reaction
	array_push($individualReaction,$totalKineticsForThisReaction);

	# Now push this reaction into the overall set of reactions for this simulation
	array_push($_SESSION["reactions"],$individualReaction);
	
	header('Location: AddReaction.php');

}
else
{
	# NEEDS CHANGING TO WHERE WE'RE GOING AFTER REACTIONS
	header('Location: IntroAgentGrid.php') ;
	
}


	

