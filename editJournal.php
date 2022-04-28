<?php
require_once('config.php');
require_once("journalSearch.php");



function editJournal($db, $action, $q, $submit, $browsestatus, $subAction, $journalID=0, $fkPublisherID=0, $userType='user', $enforce=0, $br='', $dj='', $folljid=0, $delete='', $arch='', $reason=0) {
    $msg = false; $noerror = true; $output = '';
	$arrayOutput= array();
        $msg = validateEntryJ();
	    if($msg=='') {
		    $arrayOutput = UpdateJournal($db, $browsestatus, $journalID, $enforce);
			$journalID = $arrayOutput[0];
			$msg = $arrayOutput[1];
		    if($journalID >0) {
			    $noerror = true;
			    $msg = 'New Journal has been added!';
				if($action=='EditJournal' && $browsestatus!=3) $msg = 'Journal has been updated';
			} else {
			    if($action=='EditJournal') $msg .= '- A value for journalID is missing<br>';
				else $msg = '<b>Following errors prevented the creation of a new Journal:</b><br>'.$msg.'<b> Please correct the errors and try to save again</b>';
				$msg = '<p style="color:#cc0000;">'.$msg.'</p>';
			}
		} 
    return $arrayOutput;
}

function UpdateJournal($db, $browsestatus, $journalID = 0, $enforce=0) {
    $msg = '';
    $arrayOutput = $journalSubjects = $array = array();
    require ('lib/postData.php');
    $journalClassification = '';
    if (sizeof($journalSubjects) > 0) foreach ($journalSubjects as $val) $journalClassification .= trim($val) . ' ';
    $journalISSNonline = trim($journalISSNonline);
    $journalISSNprint = trim($journalISSNprint);
    $journalISSNonline = str_replace('--', '-', trim(str_replace(' ', '-', trim($journalISSNonline))));
    $journalISSNprint = str_replace('--', '-', trim(str_replace(' ', '-', trim($journalISSNprint))));
    if (!isset($journalRights)) $journalRights = 0;
    $journalISSNonline = trim(str_replace('', '', str_replace('&#1061;', 'X', str_replace('&#8208;', '-', str_replace('&#183;', '-', str_replace('&#8211;', '-', trim($journalISSNonline)))))));
    $journalISSNprint = trim(str_replace('', '', str_replace('&#1061;', 'X', str_replace('&#8208;', '-', str_replace('&#183;', '-', str_replace('&#8211;', '-', trim($journalISSNprint)))))));
    $journalTitle = substr(htmlspecialchars(trim(strip_tags($journalTitle))) , 0, 200);
    $journalOtherTitle = substr(htmlspecialchars(trim(strip_tags($journalOtherTitle))) , 0, 200);
    $journalHtmlURL = str_replace(' ', '', substr(trim(strip_tags($journalHtmlURL)) , 0, 250));
    $journalXmlURL = str_replace(' ', '', substr(trim(strip_tags($journalXmlURL)) , 0, 250));
    $journalLogoURL = str_replace(' ', '', substr(trim(strip_tags($journalLogoURL)) , 0, 250));
    $journalDescription = str_replace(' ', '', substr(trim(strip_tags($journalDescription)) , 0, 250));
    $journalComments = substr(htmlspecialchars(trim(strip_tags($journalComments))) , 0, 400);
    $journalLogoURL = trim($journalLogoURL);
    $journalDescription = trim($journalDescription);
    if (!findme($journalHtmlURL, 'http')) $msg .= "- ERROR: <b>$journalHtmlURL</b> is missing http or https<br>";
    if (!findme($journalXmlURL, 'http')) $msg .= "- ERROR: <b>$journalXmlURL</b> is missing http or https<br>";
    if ($journalLogoURL != '') if (!findme($journalLogoURL, 'http')) $msg .= "- ERROR: <b>$journalLogoURL</b> is missing http or https<br>";
    if ($journalDescription != '') if (!findme($journalDescription, 'http')) $msg .= "- ERROR: <b>$journalDescription</b> is missing http or https<br>";
    $jsubj = '';
    if ($oldJournalDescription != $journalDescription) $jsubj = 'journalSubjects="",';
    $sql = $jsubj . ' journalISSNonline="' . trim($journalISSNonline) . '", journalISSNprint="' . trim($journalISSNprint) . '", journalOtherTitle="' . addslashes($journalOtherTitle) . '",journalTitle="' . addslashes($journalTitle) . '", journalComments="' . addslashes($journalComments) . '",  journalHtmlURL="' . trim($journalHtmlURL) . '", journalXmlURL="' . addslashes(trim($journalXmlURL)) . '", fkPublisherID="' . $fkPublisherID . '",journalLogoURL="' . $journalLogoURL . '", journalDescription="' . $journalDescription . '", journalClassification="' . trim($journalClassification) . '", journalRights = ' . $journalRights . ' ';
    $sql .= " , journalShortTitle = ''";
	if ($journalID > 0) {
        $oldsql = 'SELECT dateItemsUpdated FROM sdJournals WHERE journalID = ' . $journalID . ' AND journalHtmlURL="' . trim($journalHtmlURL) . '" AND journalXmlURL="' . trim($journalXmlURL) . '" AND journalISSNonline="' . trim($journalISSNonline) . '" AND journalISSNprint = "' . trim($journalISSNprint) . '" AND fkPublisherID = "' . $fkPublisherID . '" AND journalDescription = "' . $journalDescription . '"';
        $res = $db->query($oldsql);
        if ($db->numrows($res) == 0) {
            $sql .= ', dateItemsUpdated = CURDATE()';
            if ($browsestatus == 4) $sql .= ", journalSubjects = '', journalComments = '' ";
        }
        $sql = 'UPDATE sdJournals SET ' . $sql . ' WHERE journalID=' . $journalID; //.' AND fkPublisherID="'.$fkPublisherID.'"';
        $res = $db->query($sql);
    } 
    $arrayOutput[0] = $journalID;
    $arrayOutput[1] = $msg;
    return $arrayOutput;
}

function validateEntryJ() {
    $journalSubjects = $array = array();
    require ('lib/postData.php');
    $noerror = true;
    $msgError = '';
    $msgError = checkStr($journalTitle, '- Missing or Invalid Journal Title<br>', '', 3, 250);
    $msgError .= checkStr($fkPublisherID, '- Missing Publisher<br>', '', 1, 10);
    $msgError .= checkStr($journalHtmlURL, '- Missing or Invalid journal homepage URL <br>', '', 10, 500);
    $msgError .= checkStr($journalXmlURL, '- Missing or Invalid Current Issue RSS feed URL<br>', '', 10, 500);
    if(!isset($journalDescription)) $journalDescription = '';
    if($journalLogoURL != '') $msgError .= checkStr($journalLogoURL, '- Invalid OnlineFirst RSS feed URL<br>', '', 10, 500);
    $msgError .= checkStr($journalDescription, '- Missing or Invalid Journal Cover URL<br>', '', 10, 500);
    if($journalISSNonline != '') $msgError .= checkStr($journalISSNonline, '- Invalid online ISSN number<br>', '', 7, 18);
    if($journalISSNprint != '') $msgError .= checkStr($journalISSNprint, '- Invalid print ISSN number<br>', '', 7, 18);
    if(trim($journalISSNonline) == '' && trim($journalISSNprint) == '') $msgError .= '- ISSN numbers are missing<br>';
    if(sizeof($journalSubjects) == 0) $msgError .= '- Subjects are missing<br>';
    return $msgError;
}
?>