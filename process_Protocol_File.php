<?php

session_start();
ini_set('display_errors',0);

include("CommonFunctions.php");

$xmlFile = 'protocolparameters.xml';
$doc = new DOMDocument();
$doc->load( $xmlFile );

$xmlProtocolFile = new SimpleXMLElement('<xml/>');

$idynomics = $xmlProtocolFile->addChild('idynomics');

################################################################################################################
#### A: SIMULATOR SECTION OF PROTOCOL FILE
$simulator = $idynomics->addChild('simulator');
$allOutputs = $doc->getElementsByTagName("simulatorparam");

foreach($allOutputs as $param)
{
	$paramName = getTagValue($param,"name");
	$paramType = getTagValue($param,"type");

	if($paramName == "adaptive")
	{
		$timesteps = $simulator->addChild("timeStep");
	}

	if($paramType!="boolean")
	{
		if($paramName!="timeStepIni" and $paramName!="timeStepMin" and $paramName!="timeStepMax" and $paramName!="endOfSimulation")
		{
			#$paramDetails = $simulator->addChild('param',$_POST[$paramName]);
			$paramDetails = $simulator->addChild('param',$_SESSION[$paramName]);
		}
		else
		{
			#$paramDetails = $timesteps->addChild('param',$_POST[$paramName]);
			$paramDetails = $timesteps->addChild('param',$_SESSION[$paramName]);
		}
	}
	else
	{
		
		if($_SESSION[$paramName]==1)
		{
			if($paramName == "adaptive")
			{
				$paramDetails = $timesteps->addChild('param',"true");
			}
			else
			{
				#print $paramName." ".$_SESSION[$paramName]."<BR>";
				$paramDetails = $simulator->addChild('param',"true");
			}
		}
		else
		{
			if($paramName == "adaptive")
			{
				$paramDetails = $timesteps->addChild('param',"false");
			}
			else
			{
				$paramDetails = $simulator->addChild('param',"false");
			}
		}
	}
	
	$paramUnit = getTagValue($param,"unit");
	
	
	

	$paramDetails->addAttribute("name",$paramName);

	if(!empty($paramUnit))
	{
		$paramDetails->addAttribute("unit",$paramUnit);
	}
}

####################################################################################################################################################
####################################################################################################################################################
###### B: INPUT SECTION

$input = $idynomics->addChild('input');
$inputSection = $doc->getElementsByTagName("inputparam");

# NOW READ IN EACH OF THE SIMULATOR PARAMETERS, CREATING AN ENTRY FOR EACH

foreach($inputSection as $param)
{
	$paramName = getTagValue($param,"name");

	$paramDetails = $input->addChild('param',$_SESSION[$paramName]);
	$paramDetails->addAttribute("name",$paramName);
}	


################################################################################################################
#### C: SOLUTES AND BIOMASS TYPES SECTION OF PROTOCOL FILE
$h=0;
while($h<count($_SESSION['soluteName']))
{
	# Declare a new node for this solute (this exists under the main node rather than in a node of its own
	$solute = $idynomics->addChild('solute');
	$solute->addAttribute("name",$_SESSION['soluteName'][$h]);
	#$solute->addAttribute("domain",$_SESSION['soluteDomain'][$h]);

	# Now we haven't asked the user to declare the domain of the solute - mainly as they had not declared domains yet
	# So we add the name of the computation domain
	# This could prove troublesome if there are more than one computation domain - but iDynoMiCS isn't really set up
	# for that to be the case
	$solute->addAttribute("domain",$_SESSION["computationDomains"][0][0]);
	

	# For help later, set a flag to true if pressure has been declared as a solute. If so a pressure solver is 
	# added in a later section
	$pressureIncluded="false";
	if($_SESSION['soluteName'][$h]=="pressure")
	{
		$pressureIncluded="true";
	}
	
	# Then create the parameter tag for this solute	
	$paramDetails = $solute->addChild('param',$_SESSION['soluteDiffusivities'][$h]);
	$paramDetails->addAttribute("name","diffusivity");
	$paramDetails->addAttribute("unit","m2.day-1");

	$h=$h+1;
}

$p=0;


while($p<count($_SESSION['particles'.$_SESSION['particleParamNames'][0]]))
{
	# Declare a new node for this particle (this exists under the main node rather than in a node of its own
	$particle = $idynomics->addChild('particle');

	# Add the attribute. As this has to be declared, we know this is in the array particles.name
	$particle->addAttribute("name",$_SESSION["particles".$_SESSION['particleParamNames'][0]][$p]);

	# Now add all associated parameters of this particles (excluding name as this has already been done)

	$h=0;
	while($h<count($_SESSION['particleParamArrays']))
	{
		$paramName = $_SESSION["particleParamNames"][$h];
		$paramUnit = $_SESSION["particleParamUnits"][$h];
		
		if($paramName != "name")
		{
			$paramDetails = $particle->addChild('param',$_SESSION['particles'.$_SESSION["particleParamNames"][$h]][$p]);
			$paramDetails->AddAttribute("unit",$paramUnit);
			$paramDetails->AddAttribute("name",$paramName);
		}
	
		$h=$h+1;
	}
	$p=$p+1;
}

################################################################################################################
#### E: WORLD SECTION OF PROTOCOL FILE

# For the worlds, these are stored in two arrays, bulks and computationDomains, with parameters and units also stored
# in arrays. See IntroWorlds.php for this info

$world = $idynomics->addChild('world');

#############################################################################
#############################################################################
########################### BULK ############################################
#############################################################################
#############################################################################
if(count($_SESSION["bulks"])>0)
{
	$bulkCount=0;
	while($bulkCount<count($_SESSION["bulks"]))
	{
		# First do the name, which will be under index 0, and goes in the top level 'bulk'
		$bulk = $world->addChild('bulk');
		$bulk->addAttribute("name",$_SESSION["bulks"][$bulkCount][0]);
		
		$p=1;
		//$paramDetails = $input->addChild('param',$_SESSION[$paramName]);	
		while($p<count($_SESSION["bulkParamNames"]))
		{
			$paramDetails = $bulk->addChild('param',$_SESSION["bulks"][$bulkCount][$p]);
			$paramDetails->AddAttribute("name",$_SESSION["bulkParamNames"][$p]);
			if($_SESSION["bulkParamUnit"]!=null)
			{
				$paramDetails->AddAttribute("unit",$_SESSION["bulkParamUnit"][$p]);
			}
			$p=$p+1;
		}

		# Now we need to do the solutes for this bulk
		$bulkSoluteCount = 0;

		while($bulkSoluteCount<count($_SESSION["bulks"][$bulkCount][$p]))
		{
			# KA - start here tomorrow - add solute tags to the bulk section
			$soluteDetails = $bulk->addChild('solute');
			$soluteDetails->AddAttribute('name',$_SESSION["bulks"][$bulkCount][$p][$bulkSoluteCount][0]);

			# Now to add the parameters related to this solute as children. 1 to start as name done already
			$r=1;
			while($r<count($_SESSION["bulks"][$bulkCount][$p][$bulkSoluteCount]))
			{
				$paramDetails = $soluteDetails->addChild('param',$_SESSION["bulks"][$bulkCount][$p][$bulkSoluteCount][$r]);
				$paramDetails->AddAttribute("name",$_SESSION["bulkSoluteParamNames"][$r-1]);
				if(!empty($_SESSION["bulkSoluteParamUnit"][$r-1]))
				{
					$paramDetails->AddAttribute("unit",$_SESSION["bulkSoluteParamUnit"][$r-1]);
				}
				$r=$r+1;
			}

			$bulkSoluteCount = $bulkSoluteCount+1;
		}
		
	
		$bulkCount=$bulkCount+1;

	}
}

#############################################################################
#############################################################################
#################### COMPUTATION DOMAIN #####################################
#############################################################################
#############################################################################
if(count($_SESSION["computationDomains"])>0)
{
	$cdCount=0;
	while($cdCount<count($_SESSION["computationDomains"]))
	{
		# Firstly process the computation domain parameters (size, resolution, etc)

		# First do the name, which will be under index 0, and goes in the top level 'bulk'
		$cd = $world->addChild('computationDomain');
		$cd->addAttribute("name",$_SESSION["computationDomains"][$cdCount][0]);
		
		$p=1;
		
		while($p<count($_SESSION["computationDomainParamNames"]))
		{
			$paramDetails = $cd->addChild('param',$_SESSION["computationDomains"][$cdCount][$p]);
			$paramDetails->AddAttribute("name",$_SESSION["computationDomainParamNames"][$p]);

			# Useful here to store the X, Y and Z dimensions of the domain, as we need these later when doing boundary conditions
			if($_SESSION["computationDomainParamNames"][$p]=="nI")
				$nI=$_SESSION["computationDomains"][$cdCount][$p];
			if($_SESSION["computationDomainParamNames"][$p]=="nJ")
				$nJ=$_SESSION["computationDomains"][$cdCount][$p];		
			if($_SESSION["computationDomainParamNames"][$p]=="nK")
				$nK=$_SESSION["computationDomains"][$cdCount][$p];

			if(strlen($_SESSION["computationDomainParamUnit"][$p])>0)
			{
				$paramDetails->AddAttribute("unit",$_SESSION["computationDomainParamUnit"][$p]);
			}
			$p=$p+1;
		}

		# Now for the boundary conditions for this domain - these will all be stored within an array, within the computation domains array
		# These should be under the index p

		# Now we use the fact that there are always 6 boundaries to process these, as if there are cyclic boundaries, these go in the two tags
		# We start with the bottom, which is always at the first index, and the selected boundary condition is always at the second
		
		# Add the bottom:
		
		$paramDetails = $cd->addChild('boundaryCondition');
		# Class of this boundary condition
		$paramDetails->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][0][1]);
		# Name of this boundary condition
		$paramDetails->AddAttribute('name',$_SESSION["computationDomains"][$cdCount][$p][0][0]);
		# Now a tag for the shape of this boundary
		$shape = $paramDetails->addChild('shape');
		$shape->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][0][2]);

		# Now add the PointIn and VectorOut variables for the bottom. Rather than ask the user to do this, we assume this to be the default
		# PointIn: x=-1, y=0, z=0; vectorOut: x=-1,y=0,z=0.
		# Need to tell the user that if this is not what they want, they should change the protocol file
		$pointIn = $shape->addChild('param');
		$pointIn->AddAttribute('name','pointIn');
		$pointIn->AddAttribute('x','-1');
		$pointIn->AddAttribute('y','0');
		$pointIn->AddAttribute('z','0');
		$vectorOut = $shape->addChild('param');
		$vectorOut->AddAttribute('name','vectorOut');
		$vectorOut->AddAttribute('x','-1');
		$vectorOut->AddAttribute('y','0');
		$vectorOut->AddAttribute('z','0');

		# Now determine what to do with the top: if this is cyclic it exists in the same tag, if not it needs its own tag
		if($_SESSION["computationDomains"][$cdCount][$p][0][1]=="BoundaryCyclic")
		{
			# Add a second shape tag for this cyclic boundary
			$shape = $paramDetails->addChild('shape');
			$shape->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][1][2]);
		}
		else
		{
			// As this is the case, both the bottom and the top will not be cyclic, so we need to add the top separately
			// But first we need to add parameters for the bottom if either BoundaryBulk or BoundaryGasMembrane
			addBulkAndGasTags($paramDetails,$cdCount,$p,0,1);

			$paramDetails = $cd->addChild('boundaryCondition');
			$paramDetails->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][1][1]);
			$paramDetails->AddAttribute('name',$_SESSION["computationDomains"][$cdCount][$p][1][0]);
			$shape = $paramDetails->addChild('shape');
			$shape->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][1][2]);

			addBulkAndGasTags($paramDetails,$cdCount,$p,1,1);
		}

		# Now pointIn and vectorOut for the top - this time we need to get the dimension for the top from previously on this section
		$pointIn = $shape->addChild('param');
		$pointIn->AddAttribute('name','pointIn');
		$pointIn->AddAttribute('x',$nI);
		$pointIn->AddAttribute('y','0');
		$pointIn->AddAttribute('z','0');
		$vectorOut = $shape->addChild('param');
		$vectorOut->AddAttribute('name','vectorOut');
		$vectorOut->AddAttribute('x','1');
		$vectorOut->AddAttribute('y','0');
		$vectorOut->AddAttribute('z','0');

		# Now do the left and right
		# Left first
		$paramDetails = $cd->addChild('boundaryCondition');
		# Class of this boundary condition
		$paramDetails->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][2][1]);
		# Name of this boundary condition
		$paramDetails->AddAttribute('name',$_SESSION["computationDomains"][$cdCount][$p][2][0]);
		# Now a tag for the shape of this boundary
		$shape = $paramDetails->addChild('shape');
		$shape->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][2][2]);

		# Now point in and vector out for the left
		$pointIn = $shape->addChild('param');
		$pointIn->AddAttribute('name','pointIn');
		$pointIn->AddAttribute('x','0');
		$pointIn->AddAttribute('y','-1');
		$pointIn->AddAttribute('z','0');
		$vectorOut = $shape->addChild('param');
		$vectorOut->AddAttribute('name','vectorOut');
		$vectorOut->AddAttribute('x','0');
		$vectorOut->AddAttribute('y','-1');
		$vectorOut->AddAttribute('z','0');

		# now determine what to do with the right - if cyclic same tag, if not different
		if($_SESSION["computationDomains"][$cdCount][$p][2][1]=="BoundaryCyclic")
		{
			# Cyclic so add shape class
			$shape = $paramDetails->addChild('shape');
			$shape->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][3][2]);
		}
		else
		{
			// As this is the case, both the left and the right will not be cyclic, and both can thus be added to the protocol file
			addBulkAndGasTags($paramDetails,$cdCount,$p,2,1);
			$paramDetails = $cd->addChild('boundaryCondition');
			$paramDetails->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][3][1]);
			$paramDetails->AddAttribute('name',$_SESSION["computationDomains"][$cdCount][$p][3][0]);
			$shape = $paramDetails->addChild('shape');
			$shape->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][3][2]);

			addBulkAndGasTags($paramDetails,$cdCount,$p,3,1);
		}

		$pointIn = $shape->addChild('param');
		$pointIn->AddAttribute('name','pointIn');
		$pointIn->AddAttribute('x','0');
		$pointIn->AddAttribute('y',$nJ);
		$pointIn->AddAttribute('z','0');
		$vectorOut = $shape->addChild('param');
		$vectorOut->AddAttribute('name','vectorOut');
		$vectorOut->AddAttribute('x','0');
		$vectorOut->AddAttribute('y','1');
		$vectorOut->AddAttribute('z','0');

		# Now point in and vector out for the right

		# Now do the front and back
		# Front first
		$paramDetails = $cd->addChild('boundaryCondition');
		# Class of this boundary condition
		$paramDetails->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][4][1]);
		# Name of this boundary condition
		$paramDetails->AddAttribute('name',$_SESSION["computationDomains"][$cdCount][$p][4][0]);
		# Now a tag for the shape of this boundary
		$shape = $paramDetails->addChild('shape');
		$shape->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][4][2]);

		# Now point in and vector out for the left
		$pointIn = $shape->addChild('param');
		$pointIn->AddAttribute('name','pointIn');
		$pointIn->AddAttribute('x','0');
		$pointIn->AddAttribute('y','0');
		$pointIn->AddAttribute('z','-1');
		$vectorOut = $shape->addChild('param');
		$vectorOut->AddAttribute('name','vectorOut');
		$vectorOut->AddAttribute('x','0');
		$vectorOut->AddAttribute('y','0');
		$vectorOut->AddAttribute('z','-1');

		# now determine what to do with the back - if cyclic same tag, if not different
		if($_SESSION["computationDomains"][$cdCount][$p][4][1]=="BoundaryCyclic")
		{
			$shape = $paramDetails->addChild('shape');
			$shape->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][5][2]);
		}
		else
		{
			// As this is the case, both the front and the back will not be cyclic, and both can thus be added to the protocol file
			addBulkAndGasTags($paramDetails,$cdCount,$p,4,1);
			$paramDetails = $cd->addChild('boundaryCondition');
			$paramDetails->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][5][1]);
			$paramDetails->AddAttribute('name',$_SESSION["computationDomains"][$cdCount][$p][5][0]);
			$shape = $paramDetails->addChild('shape');
			$shape->AddAttribute('class',$_SESSION["computationDomains"][$cdCount][$p][5][2]);

			addBulkAndGasTags($paramDetails,$cdCount,$p,5,1);
		}

		# Now point in and vector out for the back
		$pointIn = $shape->addChild('param');
		$pointIn->AddAttribute('name','pointIn');
		$pointIn->AddAttribute('x','0');
		$pointIn->AddAttribute('y','0');
		$pointIn->AddAttribute('z',$nK);
		$vectorOut = $shape->addChild('param');
		$vectorOut->AddAttribute('name','vectorOut');
		$vectorOut->AddAttribute('x','0');
		$vectorOut->AddAttribute('y','0');
		$vectorOut->AddAttribute('z','1');
			
		$cdCount=$cdCount+1;

	}
}



################################################################################################################
#### D: REACTION SECTION OF PROTOCOL FILE

$r=0;

while($r<count($_SESSION["reactions"]))
{
	# Declare a new node for this particle (this exists under the main node rather than in a node of its own)
	$reaction = $idynomics->addChild('reaction');

	# Now to go through all the parameters - except kinetics - these are done differently
	$reactionParamCount=0;
	# Note the -1, as we don't process kinetics - we do this differently
	while($reactionParamCount<count($_SESSION["reactionParamNames"])-1)
	{
		# For ease of reading:
		$paramName = $_SESSION["reactionParamNames"][$reactionParamCount];
		$paramUnit = $_SESSION["reactionParamUnits"][$reactionParamCount];

		# Now check if this is yield. If it is, we want to process it differently
		if($paramName!="yield")
		{
			if($paramName == "catalysedBy" || $paramName == "class" || $paramName == "name")
			{
				# These parameter values get added on to the main reaction tag
				$reaction->addAttribute($paramName,$_SESSION["reactions"][$r][$reactionParamCount]);
				
			}
			else
			{
				# Reaction parameters that are a subtag of the reaction tag (e.g. muMax and kinetic factors)
				$paramDetails = $reaction->addChild('param',$_SESSION["reactions"][$r][$reactionParamCount]);
				$paramDetails->AddAttribute("name",$paramName);			
				$paramDetails->AddAttribute("unit",$paramUnit);
				
			
			}

		}
		else
		{
			# Now we need to process the yield for this reaction. This is more complex.
			# Firstly add the yield tag to the reaction
			$yield = $reaction->addChild('yield');

			# Now the entry screen gave the user the list of declared solutes and particles, where they can enter the consumption or production of that solute
			# or particle
			$s=0;
			# The solute and particle yields are in an array within the reaction
			while($s<count($_SESSION["reactions"][$r][$reactionParamCount]))
			{
				# Get the yield value - we are going to check whether this is zero
				$yieldValue = $_SESSION["reactions"][$r][$reactionParamCount][$s][1];
				if($yieldValue>0)
				{
					# Include in the parameter file
					$yieldParam = $yield->addChild('param',$yieldValue);

					# Firstly add the solute name
					$yieldParam->addAttribute("name",$_SESSION["reactions"][$r][$reactionParamCount][$s][0]);
					
					# Can add the unit as we have already recovered this
					$yieldParam->addAttribute("unit",$paramUnit);
				}
				$s=$s+1;
			}
		}

		$reactionParamCount=$reactionParamCount+1;
	}

	# Now to deal with the kinetics - all of which are in an array in the last element of the reaction array
	$kinetics = $_SESSION["reactions"][$r][$reactionParamCount];

	foreach ($kinetics as $kin)
	{
		# $kin is an array containing the values of the kinetic
		$kinetic = $reaction->addChild('kineticFactor');
		$kinetic->addAttribute("class",$kin[0]);

		# Now we need to determine what kinetic this is and add the relevant values
		if($kin[0]!="FirstOrderKinetic")
		{
			# We add the solute associated with this kinetic
			$kinetic->addAttribute("solute",$kin[1]);

			# Then the relevant values
			if($kin[0]=="MonodKinetic" || $kin[0]=="HaldaneKinetic" || $kin[0]=="HillKinetic")
			{
				# Add ks
				$kineticParam = $kinetic->addChild('param',$kin[2]);
				# Not ideal, but we're going to specify the unit here
				$kineticParam->AddAttribute("name","Ks");
				$kineticParam->AddAttribute("unit","g.L-1");

				# Now add the further parameters if this kinetic is Haldane or Hill Kinetic
				if($kin[0]=="HaldaneKinetic")
				{
					# Add KI - but it is pushed in after KS
					$kineticParam = $kinetic->addChild('param',$kin[3]);
					# Not ideal, but we're going to specify the unit here
					$kineticParam->AddAttribute("name","Ki");
					$kineticParam->AddAttribute("unit","g.L-1");
				}
				if($kin[0]=="HillKinetic")
				{
					# Add h
					$kineticParam = $kinetic->addChild('param',$kin[3]);
					$kineticParam->AddAttribute("name","h");
				}
			}
			if($kin[0]=="SimpleInhibition")
			{
				# Add ki
				$kineticParam = $kinetic->addChild('param',$kin[2]);
				# Not ideal, but we're going to specify the unit here
				$kineticParam->AddAttribute("name","Ki");
				$kineticParam->AddAttribute("unit","g.L-1");
			}
		}
	}


	$r=$r+1;
}
	




################################################################################################################
#### F: AGENT GRID SECTION OF PROTOCOL FILE

# This only occurs if this is not a chemostat simulation
if($_SESSION["chemostat_sim"]!="True")
{
	$agentGrid = $idynomics->addChild('agentGrid');
	for($gridParam=0;$gridParam<count($_SESSION["agentGridParams"]);$gridParam++)
	{
		$paramName = $_SESSION["agentGridParams"][$gridParam];
		$paramUnit = $_SESSION["agentGridUnit"][$gridParam];
		$paramType = $_SESSION["agentGridType"][$gridParam];
		$paramValue= $_SESSION["agentGrid"][$gridParam];
						
		if($paramName == "detachmentClass")
		{
			$detachment = $agentGrid->addChild("detachment");
			$detachment->addAttribute("class",$paramValue);
		}

		if($paramName!="kDet" && $paramName!="maxTh")
		{
			$paramDetails = $agentGrid->addChild('param',$paramValue);
		}
		else
		{
			$paramDetails = $detachment->addChild('param',$paramValue);
		}
	
		$paramDetails->addAttribute("name",$paramName);

		if(!empty($paramUnit))
		{
			$paramDetails->addAttribute("unit",$paramUnit);
		}
	}
}
else
{
	// Else we set the name of the computation domain to chemostat and just add the name tag
	$agentGrid = $idynomics->addChild('agentGrid');
	$paramDetails = $agentGrid->addChild('param','chemostat');
	$paramDetails->addAttribute('name','computationDomain');

}

################################################################################################################
#### G: SPECIES SECTION OF PROTOCOL FILE

for ($speciCount = 0; $speciCount<count($_SESSION["species"]); $speciCount++)
{
	# Declare a new node for this particle (this exists under the main node rather than in a node of its own
	$species = $idynomics->addChild('species');

	# Add the attribute. We know this is the first attribute
	$species->addAttribute("name",$_SESSION["species"][$speciCount][1]);

	# Now add all associated parameters of this particles (excluding name as this has already been done)

	for ($speciesParamCount = 0; $speciesParamCount<count($_SESSION["speciesParamNames"]); $speciesParamCount++)
	{
		$paramName = $_SESSION["speciesParamNames"][$speciesParamCount];
		$paramUnit = $_SESSION["speciesParamUnits"][$speciesParamCount];
		$paramValue = $_SESSION["species"][$speciCount][$speciesParamCount];
	
		# First check if the value has been set
		if(!empty($paramValue))
		{
			if($paramName != "name")
			{
				if($paramName == "class")
				{
					# Class gets dealt with differently - goes in the species tag rather than have its own tag
					$species->addAttribute("class",$paramValue);
					# Store the class as this is useful later in the routine
					$classProcessing = $paramValue;
				}
				else
				{
					$paramDetails = $species->addChild('param',$paramValue);
					if(!empty($paramUnit))
					{
						$paramDetails->AddAttribute("unit",$paramUnit);
					}
					$paramDetails->AddAttribute("name",$paramName);
				}
			}
		}

	}

	# Now initial setup is at the end of the loop - at $speciesParamCount
	# Determine what initial set-up we are using
	if($_SESSION["attachment"] == "selfattach")
	{
		# This is easier than the below - we can just grab the seven parameters
		for ($i = 0; $i < count($_SESSION["species"][$speciCount][$speciesParamCount]); $i++)
		{
			$paramDetails = $species->addChild('param',$_SESSION["species"][$speciCount][$speciesParamCount][$i]);
			$paramDetails->AddAttribute("name",$_SESSION["speciesInitParamNames"][$i]);
			$paramDetails->AddAttribute("unit",$_SESSION["speciesInitParamUnits"][$i]);
	
		}
	}
	else
	{
		# No check here - we're going to use onetime if for some reason self attach has not been selected
		# First we need to check whether we are defining by number or by length/area. This will be the first element of the array
		# Add the initArea tag 
		$paramDetails = $species->addChild('initArea');

		if($_SESSION["species"][$speciCount][$speciesParamCount][0]=="Region")
		{
			# Now the number of cells. This is in parameter 2
			$paramDetails->AddAttribute("number",$_SESSION["species"][$speciCount][$speciesParamCount][1]);
		}
		else
		{
			# Now for the length or area spec. This depends on 2D vs 3D
			if($_SESSION["is3D"]=="True")
			{
				$paramDetails->AddAttribute("cellsperMM2",$_SESSION["species"][$speciCount][$speciesParamCount][2]);
			}
			else
			{
				$paramDetails->AddAttribute("cellsperMM",$_SESSION["species"][$speciCount][$speciesParamCount][3]);
			}

		}

		# Now add the birthday
		$birthday = $paramDetails->addChild('param',$_SESSION["species"][$speciCount][$speciesParamCount][4]);
		$birthday->AddAttribute("name","birthday");
		$birthday->AddAttribute("unit","hour");

		# Now to add the coords min and max. At the moment these exist as a string separated by commas, so will need spliting
		$coords = $paramDetails->addChild('coordinates');

		$points = strtok($_SESSION["species"][$speciCount][$speciesParamCount][5], ",");
		$coords->AddAttribute("x",$points);
		$points = strtok(",");
		$coords->AddAttribute("y",$points);
		$points = strtok(",");
		$coords->AddAttribute("z",$points);

		# Now the same for max coordinates
		$coords = $paramDetails->addChild('coordinates');

		$points = strtok($_SESSION["species"][$speciCount][$speciesParamCount][6], ",");
		$coords->AddAttribute("x",$points);
		$points = strtok(",");
		$coords->AddAttribute("y",$points);
		$points = strtok(",");
		$coords->AddAttribute("z",$points);

	}

	# Now we need to deal with the particles - these are in an array at the end of species +1
	
	for ($i = 0; $i < count($_SESSION["species"][$speciCount][$speciesParamCount+1]); $i++)
	{
		$particle = $species->addChild('particle');
		$particle->addAttribute("name",$_SESSION["species"][$speciCount][$speciesParamCount+1][$i][0]);
		
		# Now we need to determine if this has an associated EPS object
		# In this case, this is easy, as the array will have 3 elements if so, and 2 if not
		if(count($_SESSION["species"][$speciCount][$speciesParamCount+1][$i])==3)
		{
			# Associated EPS particle
			$particle->addAttribute("class",$_SESSION["species"][$speciCount][$speciesParamCount+1][$i][1]);
			
			# Now create the mass parameter tag
			$mass = $particle->addChild('param',$_SESSION["species"][$speciCount][$speciesParamCount+1][$i][2]);
			$mass->AddAttribute("name","mass");
			$mass->AddAttribute("unit","fg");
			
		}
		else
		{
			# Just a mass tag
			$mass = $particle->addChild('param',$_SESSION["species"][$speciCount][$speciesParamCount+1][$i][1]);
			$mass->AddAttribute("name","mass");
			$mass->AddAttribute("unit","fg");
		}
	}

	# Now deal with the reactions that this speci is part of
	for ($i = 0; $i < count($_SESSION["species"][$speciCount][$speciesParamCount+2]); $i++)
	{
		$reactionTag = $species->addChild('reaction');
		# The 0th element contains the name of the reaction
		$reactionTag->addAttribute("name",$_SESSION["species"][$speciCount][$speciesParamCount+2][$i]);
		$reactionTag->addAttribute("status","active");
		
	}

	# Now deal with the reaction switch if this is a bact adaptable
	if($classProcessing=="BactAdaptable")
	{
		# Set up a reaction switch tag
		$reactionSwitchTag = $species->addChild('reactionSwitch');

		$switchOff = $reactionSwitchTag->addChild("whenOff");
		# Now add the reactions that are on and off, from the array that is part of the species
		for ($i = 0; $i < count($_SESSION["species"][$speciCount][$speciesParamCount+3]); $i++)
		{
			$offReaction = $switchOff->addChild('reaction');
			$offReaction->addAttribute('name',$_SESSION["species"][$speciCount][$speciesParamCount+3][$i]);
			$offReaction->addAttribute("status","active");
		}

		# Now we make the assumption that the colour of the Off reaction is WHITE
		$offReactionParams = $switchOff->addChild('param','white');
		$offReactionParams->addAttribute('name','color');

		# Now for the switch lag - this is the first element of the next array
		$offReactionParams = $switchOff->addChild('param',$_SESSION["species"][$speciCount][$speciesParamCount+5][0]);
		$offReactionParams->addAttribute('name','switchLag');
		$offReactionParams->addAttribute('unit','hour');
		
		# NOW FOR SWITCH ON
		$switchOn = $reactionSwitchTag->addChild("whenOn");
		# Now add the reactions that are on and off, from the array that is part of the species
		
		for ($i = 0; $i < count($_SESSION["species"][$speciCount][$speciesParamCount+4]); $i++)
		{	
			$onReaction = $switchOn->addChild('reaction');
			$onReaction->addAttribute('name',$_SESSION["species"][$speciCount][$speciesParamCount+4][$i]);
			$onReaction->addAttribute("status","active");
		}

		# Now we make the assumption that the colour of the Off reaction is WHITE
		$onReactionParams = $switchOn->addChild('param','black');
		$onReactionParams->addAttribute('name','color');

		# Now for the switch lag - this is the second element of the next array
		$onReactionParams = $switchOn->addChild('param',$_SESSION["species"][$speciCount][$speciesParamCount+5][1]);
		$onReactionParams->addAttribute('name','switchLag');
		$onReactionParams->addAttribute('unit','hour');

		# Now for the condition that switches the reactions
		$onCondition = $reactionSwitchTag->addChild("onCondition");
		$onCondition->addAttribute('name',$_SESSION["species"][$speciCount][$speciesParamCount+5][2]);
		if($_SESSION["species"][$speciCount][$speciesParamCount+5][2]=="biomass" || $_SESSION["species"][$speciCount][$speciesParamCount+5][2]=="inert" || $_SESSION["species"][$speciCount][$paramCount+5][2]=="capsule")
		{
			$onCondition->addAttribute('type','biomass');
		}
		else
		{
			$onCondition->addAttribute('type','solute');
		}

		# Now for the parameters for the switch
		$onConditionParams = $onCondition->addChild('param',$_SESSION["species"][$speciCount][$speciesParamCount+5][3]);
		$onConditionParams->addAttribute('name','switch');

		if($_SESSION["species"][$speciCount][$speciesParamCount+5][2]=="biomass" || $_SESSION["species"][$speciCount][$speciesParamCount+5][2]=="inert" || $_SESSION["species"][$speciCount][$paramCount+5][2]=="capsule")
		{
			$onConditionParams = $onCondition->addChild('param',$_SESSION["species"][$speciCount][$speciesParamCount+5][4]);
			$onConditionParams->addAttribute('name','mass');
			$onConditionParams->addAttribute('unit','fg');
		}
		else
		{
			$onConditionParams = $onCondition->addChild('param',$_SESSION["species"][$speciCount][$speciesParamCount+5][4]);
			$onConditionParams->addAttribute('name','concentration');
			$onConditionParams->addAttribute('unit','g.L-1');
		}
		
	}

}

################################################################################################################
#### H: SOLVER SECTION OF PROTOCOL FILE

####### THIS IS A DEFAULT SECTION - THE USER WILL CHANGE ALL THIS IN THE PROTOCOL FILE IF THEY DESIRE
#### However we do need this for each computation domain declared

if($_SESSION["chemostat_sim"]!="True")
{
	$cdCount=0;
	while($cdCount<count($_SESSION["computationDomains"]))
	{
		$solver = $idynomics->addChild('solver');
		$solver->AddAttribute("class","Solver_multigrid");
		$solver->AddAttribute("name","solutes");
		# Get the computation domain name
		$solver->AddAttribute("domain",$_SESSION["computationDomains"][$cdCount][0]);

		# Now add the relevant parameters
		$paramDetails = $solver->addChild("param","true");
		$paramDetails->AddAttribute("name","active");
		$paramDetails = $solver->addChild("param","150");
		$paramDetails->AddAttribute("name","preStep");
		$paramDetails = $solver->addChild("param","150");
		$paramDetails->AddAttribute("name","postStep");
		$paramDetails = $solver->addChild("param","1500");
		$paramDetails->AddAttribute("name","courseStep");
		$paramDetails = $solver->addChild("param","5");
		$paramDetails->AddAttribute("name","nCycles");
	
		$r=0;
		# Now we list the names of all the reactions entered in the protocol file
		while($r<count($_SESSION["reactions"]))
		{
			$paramDetails = $solver->addChild("reaction");
			$paramDetails->AddAttribute("name",$_SESSION["reactions"][$r][0]);

			$r=$r+1;
		}

		# Now determine if pressure has been declared - if so we need a pressure solver
		# We can tell this from the flag set when solutes were read in
		if($pressureIncluded=="true")
		{
			$solver = $idynomics->addChild('solver');
			$solver->AddAttribute("class","Solver_pressure");
			$solver->AddAttribute("name","pressure");
			# Get the computation domain name
			$solver->AddAttribute("domain",$_SESSION["computationDomains"][$cdCount][0]);

			$paramDetails = $solver->addChild("param","true");
			$paramDetails->AddAttribute("name","active");
		}
		$cdCount = $cdCount+1;
	}
}
else
{
	# Chemostat solver
	$solver = $idynomics->addChild('solver');
	$solver->AddAttribute("class","Solver_chemostat");
	$solver->AddAttribute("name","solver1");
	$solver->AddAttribute("domain","chemostat");

	# Now add the relevant parameters
	$paramDetails = $solver->addChild("param","true");
	$paramDetails->AddAttribute("name","active");
	$paramDetails = $solver->addChild("param","1e-2");
	$paramDetails->AddAttribute("name","rtol");
	$paramDetails = $solver->addChild("param","1e-3");
	$paramDetails->AddAttribute("name","hmax");

	# Now we list the names of all the reactions entered in the protocol file
	for($r=0;$r<count($_SESSION["reactions"]);$r++)
	{
		$paramDetails = $solver->addChild("reaction");
		$paramDetails->AddAttribute("name",$_SESSION["reactions"][$r][0]);
	}
}




## LAST STEP - WE NEED TO LET THE USER DOWNLOAD THE RESULTANT PROTOCOL FILE
# SO SET THE HEADER CORRECTLY TO ALLOW ATTACHMENTS, THEN ECHO THE RESULTANT FILE

header('Content-Disposition: attachment;filename='.$_SESSION["protocolFileName"]);
echo $xmlProtocolFile->saveXML();

?>
