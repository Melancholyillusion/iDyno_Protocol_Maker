<?

function getTagValue($tagSet,$tagName)
{
	$tagValue = $tagSet->getElementsByTagName($tagName)->item(0);
	if(!empty($tagValue))
	{
		return $tagValue->nodeValue;
	}
	else
	{
		return null;
	}
	
}

# EXAMINE A BOUNDARY - DETERMINE IF IT IS GAS OR BULK, AND ADD THE RELEVANT PARAMETERS TO THE PROTOCOL FILE
function addBulkAndGasTags($paramDetailsToAppendTo,$domainProcessing,$domainParam,$boundaryNum,$boundaryArrayRef)
{
	if($_SESSION["computationDomains"][$domainProcessing][$domainParam][$boundaryNum][$boundaryArrayRef]=="BoundaryBulk")
	{
		$activeForSolute = $paramDetailsToAppendTo->addChild("param",$_SESSION["computationDomains"][$domainProcessing][$domainParam][$boundaryNum][3]);
		$activeForSolute->AddAttribute("name","activeForSolute");

		$bulk = $paramDetailsToAppendTo->addChild("param",$_SESSION["computationDomains"][$domainProcessing][$domainParam][$boundaryNum][4]);
		$bulk->AddAttribute("name","bulk");
	}
	else if($_SESSION["computationDomains"][$domainProcessing][$domainParam][$boundaryNum][$boundaryArrayRef]=="BoundaryGasMembrane")
	{
		$isPermeableTo = $paramDetailsToAppendTo->addChild("param",$_SESSION["computationDomains"][$domainProcessing][$domainParam][$boundaryNum][5]);
		$isPermeableTo->AddAttribute("name","isPermeableTo");
		$isPermeableTo->AddAttribute("detail",$_SESSION["computationDomains"][$domainProcessing][$domainParam][$boundaryNum][3]);

		$bulk = $paramDetailsToAppendTo->addChild("param",$_SESSION["computationDomains"][$domainProcessing][$domainParam][$boundaryNum][4]);
		$bulk->AddAttribute("name","bulk");
	}

}

?>
