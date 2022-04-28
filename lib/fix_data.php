<?php 
/*   Fix data associated with JournalTOCs */
///////////////////////////////////////////////////////////////////
function fixISSNs($db) {
	$sql = "UPDATE  sdJournals SET journalISSNprint = trim(replace(replace(journalISSNprint,'&#8211;','-'),' ','')), journalISSNonline = trim(replace(replace(journalISSNonline,'&#8211;','-'),' ','')) WHERE journalISSNonline like '%&#%' or journalISSNprint like '%&#%'"; 
	$result=$db->query($sql) or die("$sql <BR />".$db->error());
	$sql = "UPDATE  sdJournals SET journalISSNprint = trim(replace(replace(journalISSNprint,'&#8211;','-'),' ','')), journalISSNonline = trim(replace(replace(journalISSNonline,'&#8211;','-'),' ','')) WHERE journalISSNonline like '%&#%' or journalISSNprint like '%&#%'"; 
	$result=$db->query($sql) or die("$sql <BR />".$db->error());
	$sql = "UPDATE  sdJournals SET journalISSNprint = trim(replace(replace(journalISSNprint,'&#183;','-'),' ','')), journalISSNonline = trim(replace(replace(journalISSNonline,'&#183;','-'),' ','')) WHERE journalISSNonline like '%&#%' or journalISSNprint like '%&#%'"; 
	$result=$db->query($sql) or die("$sql <BR />".$db->error());		
	$sql = "UPDATE  sdJournals SET journalISSNprint = trim(replace(replace(journalISSNprint,'&#8208;','-'),' ','')), journalISSNonline = trim(replace(replace(journalISSNonline,'&#8208;','-'),' ','')) WHERE journalISSNonline like '%&#%' or journalISSNprint like '%&#%'"; 
	$result=$db->query($sql) or die("$sql <BR />".$db->error());		
	$sql = "UPDATE  sdJournals SET journalISSNprint = trim(replace(replace(journalISSNprint,'&#1061;','X'),' ','')), journalISSNonline = trim(replace(replace(journalISSNonline,'&#1061;','X'),' ','')) WHERE journalISSNonline like '%&#%' or journalISSNprint like '%&#%'"; 
	$result=$db->query($sql) or die("$sql <BR />".$db->error());
	$sql = "UPDATE  sdJournals SET journalISSNprint = trim(replace(journalISSNprint,' - ','-')), journalISSNonline = trim(replace(journalISSNonline,' - ','-')) WHERE journalISSNonline like '% - %' or journalISSNprint like '% - %'"; 
	$result=$db->query($sql) or die("$sql <BR />".$db->error());	
	$sql = "UPDATE  sdJournals SET journalISSNprint = trim(replace(journalISSNprint,' ','')), journalISSNonline = trim(replace(journalISSNonline,' ','')) WHERE journalISSNonline like '% %' or journalISSNprint like '% %'"; 
	$result=$db->query($sql) or die("$sql <BR />".$db->error());	
	$sql = "UPDATE  sdJournals SET journalISSNprint = trim(replace(journalISSNprint,'urn:issn:','')), journalISSNonline = trim(replace(journalISSNonline,'urn:issn:','')) WHERE journalISSNonline like '%urn:issn:%' or journalISSNprint like '%urn:issn:%'"; 
	$result=$db->query($sql) or die("$sql <BR />".$db->error());		
	$sql   = 'SELECT journalID, journalISSNprint, journalISSNonline FROM '. '.sdJournals WHERE ((TRIM(journalISSNonline) = "" AND TRIM(journalISSNprint) = "") OR journalISSNonline LIKE "%&#%" OR journalISSNprint LIKE "%&#%") AND journalStatus =1 AND journalXmlURL != "" ORDER by journalTitle';
    $res  = $db->query($sql) or die("<p>$sql</p>".$db->error());
	if($db->numrows($res)>0) {
	   while($row=$db->fetcharray($res)) { 
	      extract($row);
		  $new_eissn =  trim(preg_replace("/[^Xx0-9\-]/",'',$journalISSNonline));
		  $new_pissn =  trim(preg_replace("/[^Xx0-9\-]/",'',$journalISSNprint));
		  if(strlen($new_eissn)!=9 && strlen($new_eissn!=16)) $new_eissn='';
		  if(strlen($new_pissn)!=9 && strlen($new_pissn!=16)) $new_pissn='';
		  $sql = "UPDATE  sdJournals SET journalISSNprint = '$new_pissn', journalISSNonline = '$new_eissn' WHERE journalID = $journalID AND journalISSNonline like '%&#%' or journalISSNprint like '%&#%'"; 
	      $result=$db->query($sql) or die("$sql <BR />".$db->error());		
       }
	}
	return;	
}
?>
