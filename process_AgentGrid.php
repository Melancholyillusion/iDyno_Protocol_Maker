<?php
session_start();
ini_set('display_errors',0);

include("CommonFunctions.php");

if(count($_SESSION["agentGridParams"])>0)
{
	$_SESSION["agentGrid"] = array();

	for($gridParam=0;$gridParam<count($_SESSION["agentGridParams"]);$gridParam++)
	{
		# Push the entry into the array
		if($_SESSION["agentGridType"][$gridParam]=="boolean")
		{
			if(isset($_POST[$_SESSION["agentGridParams"][$gridParam]]))
			{
				array_push($_SESSION["agentGrid"],"true");
			}
			else
			{
				array_push($_SESSION["agentGrid"],"false");
			}
		}
		else
		{
			array_push($_SESSION["agentGrid"],$_POST[$_SESSION["agentGridParams"][$gridParam]]);
		}
	}
}


header('Location: IntroSpecies.php') ;

?>
