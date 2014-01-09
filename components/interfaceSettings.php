<?php

/*
Function used by all user-visible pages of the interface - creates the menu bar that runs along the top of the screen
*/

function PrintHeader ($title,$stepNum,$refreshlink="") {
	if(isset($title)) {
		if($title != "") { $title = "$title"; } 
	} else {
		$title = "";
	}
	
	if($refreshlink != ""){
		$meta = "<meta http-equiv='refresh' content='30;url=$refreshlink' />";
	}
	
	print '
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">
	<html>
	<head>
	<title>'.$title.'</title>
	<link href="components/main.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	<div align="center">
	  <table width="100%"  border="0" cellspacing="0" cellpadding="6">
		<tr>
		    <td align="right" background="components/bar.png">
		  <div id="nav">
			<a href="index.php">Protocol File Generator</a> | 
			<a href="http://www.birmingham.ac.uk/generic/idynomics/index.aspx">iDynoMiCS Website</a> |
			<a href="https://github.com/kreft/iDynoMiCS/wiki">iDynoMiCS Wiki</a> | 
			<a href="https://github.com/kreft/iDynoMiCS/wiki/iDynomics-Tutorial">iDynoMiCS Tutorial</a> |
			<a href="">Code Documentation</a> | 
			<a href="https://github.com/kreft/iDynoMiCS/issues">Log an Issue</a> | 
			<a href="http://www.birmingham.ac.uk/generic/idynomics/mailing-list.aspx">Mailing List Information</a> &nbsp;&nbsp;
		  </div>
		  </td>
		</tr>
	   </table>';
	// KA - broke into two tables to get the menu layout correct
	print '
	
	 <table width="100%"  border="0" cellspacing="0" cellpadding="6">
		<tr>
		<td><A HREF="protocol_File_Maker.php"><img src="components/iDynoMiCS_logo.gif" height="107" border="0"></A></td>';

		if($stepNum==1)
		{
			print '<td><A HREF="index.php"><img src="components/step1_on.png" height="95" border="0"></A></td>';
		}
		else
		{
			print '<td><A HREF="index.php"><img src="components/step1_off.png" height="95" border="0"></A></td>';
		}

		if($stepNum==2)
		{
			print '<td><A HREF="IntroSolutes.php"><img src="components/step2_on.png" height="95" border="0"></A></td>';
		}
		else
		{
			print '<td><A HREF="IntroSolutes.php"><img src="components/step2_off.png" height="95" border="0"></A></td>';
		}

		if($stepNum==3)
		{
			print '<td><A HREF="IntroParticles.php"><img src="components/step3_on.png" height="95" border="0"></A></td>';
		}
		else
		{
			print '<td><A HREF="IntroParticles.php"><img src="components/step3_off.png" height="95" border="0"></A></td>';
		}

		if($stepNum==4)
		{
			print '<td><A HREF="IntroWorlds.php"><img src="components/step4_on.png" height="95" border="0"></A></td>';
		}
		else
		{
			print '<td><A HREF="IntroWorlds.php"><img src="components/step4_off.png" height="95" border="0"></A></td>';
		}

		if($stepNum==5)
		{
			print '<td><A HREF="IntroReactions.php"><img src="components/step5_on.png" height="95" border="0"></A></td>';
		}
		else
		{
			print '<td><A HREF="IntroReactions.php"><img src="components/step5_off.png" height="95" border="0"></A></td>';
		}

		if($stepNum==6)
		{
			print '<td><A HREF="IntroAgentGrid.php"><img src="components/step6_on.png" height="95" border="0"></A></td>';
		}
		else
		{
			print '<td><A HREF="IntroAgentGrid.php"><img src="components/step6_off.png" height="95" border="0"></A></td>';
		}

		if($stepNum==7)
		{
			print '<td><A HREF="IntroSpecies.php"><img src="components/step7_on.png" height="95" border="0"></A></td>';
		}
		else
		{
			print '<td><A HREF="IntroSpecies.php"><img src="components/step7_off.png" height="95" border="0"></A></td>';
		}


		print '</tr></table><hr>';
}

?>
