<?php
//error_reporting(E_ALL);       // comment for production
//ini_set("display_errors", 1); // comment for production
header('Content-Type: text/html; charset=utf-8');
require_once ('config.php');
$loginForm = 'loginForm.html';
$anno = date("Y");
$begYear = $anno - 38;
$endYear = $anno + 1;
$url = $URL . $indexphp;
$inputDir = $serviceDir . 'inputFiles';
$outputDir = $serviceDir . 'outputFiles';
$tmpDir = $serviceDir . 'tmpFiles';
$delDir = $serviceDir . 'deletedFiles';
if (!isset($action)) $action = '';
if (!isset($journalID)) $journalID = 0;
if (!isset($q)) $q = '';
if(isset($on_issn) && $q=='') $q = $on_issn;
if (!isset($submit)) $submit = '';
if (!isset($browsestatus)) $browsestatus = 0;
if(!isset($fkPublisherID)) $fkPublisherID = 0;
if(!isset($badstatus)) $badstatus=0;
if(!isset($subAction)) $subAction='';
if(isset($findJournal) && $q!='') $action = 'findJournal';
if(!isset($enforce)) $enforce=0;
if(!isset($br)) $br='';
if(!isset($dj)) $dj='';
if(!isset($folljid)) $folljid=0;
if(!isset($delete)) $delete='';
if(!isset($arch)) $arch='';
if(!isset($reason)) $reason=0;
$res = $db->query("SET NAMES UTF8");
require_once ($libDir . 'fix_data.php');
require_once("journalSearch.php");
require_once('editJournal.php');
$thisfile = '';
////// SESSION MANAGEMENT START ///////////////
include $libDir . 'sess.php';
$sess = new session;
if ($action == 'login') {
    $sess->start();
    $val = $sess->register();
    $SESSION_UNAME = $sess->read();
    $action = '';
    doAction($db, $indexphp, $action, $loginForm, $SESSION_UNAME);
    exit();
}
if ($action == 'signIn') {
    $sess->destroy();
    displayPage($db, $indexphp, $action, $loginForm);
}
$SESSION_UNAME = $sess->read();
$IMPORTANTEMSG = '';
if ($action == 'logout') {
    $sess->destroy();
    $newIP = $sess->fetchip();
    $newIP = ip2long($newIP);
    $queryString = 'DELETE FROM ' . $sessionTable . ' WHERE username = "' . $SESSION_UNAME . '" AND IP = ' . $newIP;
    if (!($result = $db->query($queryString))) print_error_local($db->error());
    displayPage($db, $indexphp, $action, $loginForm);
    exit();
}
////////// SESSION MANAGEMENT END ////////////
if (!isset($userType)) $userType = 'user';
doAction($db, $indexphp, $action, $loginForm, $SESSION_UNAME, $q, $submit, $browsestatus, $journalID, $fkPublisherID, $badstatus, $subAction, $enforce, $br, $dj, $folljid, $delete, $userType, $arch, $reason);
exit;

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}
///////////////////////////////////////////////////////////////////
function doAction($db, $indexphp, $action, $loginForm, $SESSION_UNAME, $q='', $submit='', $browsestatus=0, $journalID=0, $fkPublisherID=0, $badstatus=0, $subAction='', $enforce=0, $br='', $dj='', $folljid=0, $delete='', $userType='user', $arch='', $reason=0) {
    switch ($action) {
        case "AddJournal":
            $webPage = journalDataManager($db, $action, $q, $submit, $browsestatus, $subAction, 0, $fkPublisherID, $userType, $enforce);
            break;
        case "EditJournal":
            if($submit=='Save'){ 
            $output = editjournal($db, $action, $q, $submit, $browsestatus, $subAction, $journalID, $fkPublisherID, $userType, 0,$br, $dj, $folljid, $delete, $arch, $reason);
            debug_to_console($output[0]);
            $webPage = getJournalRecord($db, $browsestatus, $output[0], false, $subAction, '', $output[1], $br, $dj, $folljid, $delete, $arch, $enforce, $reason);
            }else {$webPage = getJournalRecord($db, $browsestatus, $journalID, false, $subAction, '', '', $userType, $br, $dj, $folljid, $delete, $arch, $enforce, $reason);
            }
            break;
        case "findJournal":
            $output = journalSearch($db, $q);
			$numJournals = sizeof($output);
			if($numJournals==0) $webPage = getJournalRecord($db, $browsestatus, 0, false, $subAction, $q, $msg);
			else if($numJournals==1) $webPage = getJournalRecord($db, $browsestatus, 0, $output, $subAction, $q);
			else $webPage = browseJournals($db, $output, $action, 0, $subAction, $q, $fkPublisherID);
            break;
        case "BrowseSchools":
			$webPage = browseJournals($db, false, $action, $badstatus, $subAction, $q, $fkPublisherID);
            break;
        case "removeJournal":
            $array_result = removeJournal($db, $journalID, $browsestatus, $badstatus, $subAction);
            $badstatus = $array_result[0];
            $subAction = $array_result[1];
            $webPage = browseJournals($db, false, $action, $badstatus, 'queryFound', $q, $fkPublisherID);
            break;
        default:
            $webPage = '';
    }
	displayPage($db, $indexphp, $action, $loginForm, $SESSION_UNAME, $subAction, $webPage, $browsestatus);
    exit(0);
}

////////////////////////////////////////////////////////////////////////
function removeJournal($db, $journalID, $browsestatus, $badstatus, $subAction) {
	$array_result = array();
	if(!isset($journalID)) $journalID=0;
	if(!isset($badstatus)) $badstatus=0;
	if($journalID>0) {
	    if($badstatus==1) {
		    $sql = "UPDATE sdJournals SET journalComments = 'Archived because its RSS was removed by publisher', journalStatus=5, journalLastUpdated=CURDATE() WHERE journalID = $journalID AND journalStatus=4";
        } 
		if($browsestatus==3 || $badstatus==3) {
	        if($badstatus==0) $badstatus = $browsestatus;
			$subAction='byStatus';
		}
		$up = $db->query($sql);
	}
	$array_result[0] = $badstatus;
	$array_result[1] = $subAction;
	return $array_result;
}
/////////////////////////////////////////////////////////////////// 
function browseJournals($db, $res=false, $action, $badstatus=0, $subAction='', $q='', $fkPublisherID=0, $indexphp='index.php', $archived=0, $fixissn='', $pubsort='', $orderpubsort=0) {
    if(!isset($pubsort)) $pubsort='';
    if(!isset($orderpubsort)) $orderpubsort=0;
    $tabla = $thisSchoolFind = $msg = $schoolSelect = $sqaSelect = $publisherSelect = $thisSchoolName = '';
    if(!isset($subAction)) $subAction='';
    if(!isset($fixissn)) $fixissn = '';
    $publisherSelect = getSelectPublishers($db, 'publisherID','publisher','sdPublishers', 'ORDER BY publisher', $fkPublisherID,50);
    $addThisTxt = '';
    if(!isset($q)) $q='';
    if(trim($q)!='' && $fkPublisherID==0) $subAction='queryFound';
	else if($fkPublisherID>0) $subAction = '';
    if($subAction=='queryFound') {
		

        if(is_array($res)) {
            $numHits = sizeof($res);		
            if($numHits>0) {
                $thisSchoolName = 'Found <b>'.$numHits.'</b> journals for '.$q;
                $thisSchoolBody = getBrowseRows($db, $res, $action, $fkPublisherID, $subAction, $badstatus, $orderpubsort, $pubsort, $indexphp, $q);
                $addThisTxt = '<a href="'.$indexphp.'?action=AddJournal" style="color:#ffffff;font-weight:bold;" title="Add a New Journal">Add Journal</a>';
            } else $thisSchoolBody = '<p>Found no journals</p>';			
			



	    } else {
		
		    if($q!=='' && !$res) $res = getJournalByQuery($db, $q, 'title');
            if($res) {
                if($db->numrows($res)>0) {
                    $thisSchoolName = 'Found <b>'.$db->numrows($res).'</b> journals for '.$q;
                    $thisSchoolBody = getBrowseRows($db, $res, $action, $fkPublisherID, $subAction, $badstatus, $orderpubsort, $pubsort, $indexphp, $q);
                    $addThisTxt = '<a href="'.$indexphp.'?action=AddJournal" style="color:#ffffff;font-weight:bold;" title="Add a New Journal">Add Journal</a>';
                } else $thisSchoolBody = '<p>Found no journals</p>';
            } else $thisSchoolBody = '<p>You need to specify a search query</p>';
        }
    } else if($subAction=='byStatus') {
	    $newJournals = false;
	    switch ($badstatus) {
            case "1":
				$thisreg = 1; 
				if(!isset($archived)) $archived=0;
				$thisSchoolName = ' <a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=2"><b style="color:#99FFFF">[ISSN Errors]</b></a> &nbsp; &nbsp; <a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=0"><b style="color:#99FFFF">[Subject Errors]</b></a> &nbsp; &nbsp; RSS Errors (<a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=5" style="color:#CC9999;">Sort by Followers</a>)'; 
				$thisSchoolName .= " &nbsp;"; 
				$sql = 'SELECT journalID, journalStatus, journalTitle, journalISSNonline, journalComments, journalISSNprint, journalHtmlURL, journalXmlURL, rssFeedsVersion, fkPublisherID, journalClassification, journalSubjects, journalRights, publisher, journalDescription FROM sdJournals, sdPublishers WHERE journalStatus!=1 AND fkPublisherID= publisherID ';
				if($archived==0) $sql .= ' AND journalStatus!=5 ';
				$sql = addSorting($sql, $pubsort, $orderpubsort,'publisher','journalStatus','journalTitle');
                break;
            case "2":
			    $thisreg = 1; 
			    if($fixissn=='yes') fixISSNs($db);
		 	    $thisSchoolName = ' ISSN Errors &nbsp; <a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=2&fixissn=yes"><b style="color:#CC9999;">(Try to Fix Them)</b></a> &nbsp; &nbsp; &nbsp; &nbsp;
				<a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=0"><b style="color:#99FFFF">[Subject Errors]</b></a> &nbsp; &nbsp; <a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=1"><b style="color:#99FFFF">[RSS Errors]</b></a>'; 
			    $sql   = 'SELECT * FROM sdJournals WHERE (((journalISSNonline = "" AND journalISSNprint = "") OR journalISSNonline LIKE "%&#%" OR journalISSNprint LIKE "%&#%" OR journalISSNonline LIKE "% %" OR journalISSNprint LIKE "% %" OR journalISSNonline LIKE "%.%" OR journalISSNprint LIKE "%.%"  OR journalISSNonline LIKE "%urn%" OR journalISSNprint LIKE "%urn%") AND journalStatus =1 AND journalXmlURL != "") OR ((journalISSNonline not like "%-%" and journalISSNonline !="") or (journalISSNprint not like "%-%" AND journalISSNprint !="")) ORDER by journalTitle';
                break;
            case "3":
			    $thisreg = 0; 
			    $newJournals = true;
		        $thisSchoolName = ' New Journals:'; 
		        $sql = "SELECT * FROM sdJournals WHERE journalLastUpdated!='0000-00-00' ORDER BY sdJournals.journalID DESC LIMIT 0,300";
                break;		
            case "4":
		        $thisreg = 0; 
		        $newJournals = true;
	            $thisSchoolName = " journals without covers or wrong covers &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; [<a href=\"$indexphp?action=BrowseSchools&subAction=byStatus&badstatus=7\" title=\"No Covers\" style=\"color:#FFFFFF;\"><u>'No_Covers'</u></a>]"; 
		        $sql = "SELECT * FROM sdJournals WHERE journalStatus!=5 AND (journalSubjects = '404') and journalComments != ''"; //journalSubjects = '400' OR 
				$sql = addSorting($sql, $pubsort, $orderpubsort,'fkPublisherID','journalStatus','journalTitle');	
                $sql .= ' LIMIT 0,500';
                break;
            case "6":
		        $thisreg = 0; 
		        $newJournals = true;
	            $thisSchoolName = ' archived journals'; 
		        $sql = "SELECT journalID, sdJournals.journalTitle, journalComments, journalISSNonline, journalISSNprint, journalHtmlURL, journalXmlURL, fkPublisherID, rssFeedsVersion, journalDescription, journalRights, journalStatus, count(fkJournalID) as followers FROM sdJournals, sdFollowers WHERE journalID=fkJournalID AND journalStatus=5 AND journalComments NOT LIKE '%ceased%' GROUP BY fkJournalID "; 
                $sql = addSorting($sql, $pubsort, $orderpubsort);				
                $sql .= ' LIMIT 0,2000';
                break;
            case "7":
		        $thisreg = 0; 
		        $newJournals = true;
	            $thisSchoolName = " journals with 'no_cover' placeholder &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; [<a href=\"$indexphp?action=BrowseSchools&subAction=byStatus&badstatus=4\" title=\"Cover Errors\" style=\"color:#FFFFFF;\"><u>Cover Errors</u></a>]"; 
		        $sql = "SELECT journalID, sdJournals.journalTitle, journalComments, journalISSNonline, journalISSNprint, journalHtmlURL, journalXmlURL, rssFeedsVersion, fkPublisherID, journalDescription, journalRights, journalStatus, count(fkJournalID) as followers FROM sdJournals, sdFollowers WHERE journalID=fkJournalID AND journalStatus!=5 AND journalDescription LIKE '%no_cover%' GROUP BY fkJournalID ";  
				$sql = addSorting($sql, $pubsort, $orderpubsort);
                $sql .= ' LIMIT 0,1000';
                break;				
            case "5":
			    $thisreg = 0; 
			    $newJournals = true;
		        $thisSchoolName = ' RSS Errors with followers &nbsp; &nbsp; <a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=2"><b style="color:#99FFFF">[ISSN |Errors]</b></a> &nbsp; &nbsp; <a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=0"><b style="color:#99FFFF">[Subject Errors]</b></a> &nbsp; &nbsp; <a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=1"><b style="color:#99FFFF">[RSS Errors]</b></a> '; 
		        $sql   = 'SELECT journalID, journalISSNonline, journalISSNprint, journalHtmlURL, journalComments, rssFeedsVersion, journalClassification, sdJournals.journalTitle, journalXmlURL, fkPublisherID, foundNewItemsDate, journalDescription, journalLastUpdated, count(fkJournalID) as followers FROM sdJournals, sdFollowers WHERE journalStatus=4 and journalID=fkJournalID group by fkJournalID '; 
				$sql = addSorting($sql, $pubsort, $orderpubsort);
                break;
            default:
			    $thisreg = 0; 
			    $thisSchoolName = ' <a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=2"><b style="color:#99FFFF">[ISSN Errors]</b></a>  &nbsp; &nbsp; Subject Errors &nbsp; &nbsp; <a href="'.$indexphp.'?action=BrowseSchools&subAction=byStatus&badstatus=1"><b style="color:#99FFFF">[RSS Errors]</b></a>'; 
			    $sql   = 'SELECT * FROM sdJournals WHERE journalClassification="" ORDER by journalTitle LIMIT 0,1000';
        }			
		$res  = $db->query($sql) or die("<p>$sql</p>".$db->error());
		if($db->numrows($res)>0) {
		    $foundthis = 'Found <b>';
		    if($badstatus>4) $foundthis = 'Top <b>';
			else if($badstatus==3) $foundthis = 'Latest <b>';
		    if($badstatus==8) { $foundthis = 'First <b>'; $thisSchoolBody = getDOAJrows($db, $res); 
            }else $thisSchoolBody = getBrowseRows($db, $res, $action, $fkPublisherID, $subAction, $badstatus, $orderpubsort, $pubsort, $indexphp);
			$thisSchoolName = $foundthis.$db->numrows($res).'</b> &nbsp; '.$thisSchoolName;
		} else $thisSchoolBody = '<p>Found No Journal</p>';
	} else if($fkPublisherID>0) {
	    $websites = $homePage = $OPMLURL = '';
	    $sql   = 'SELECT homePage, OPMLURL, publisher AS thisPublisherName FROM sdPublishers WHERE publisherID='.$fkPublisherID;
		$res  = $db->query($sql) or die("<p>$sql</p>".$db->error());
		if($db->numrows($res)>0) {
		    extract($db->fetcharray($res));	
	        if($homePage!='') $websites = ' &nbsp; [<a href="'.$homePage.'" title="'.$homePage.'" target="_blank"><span style="color:#ffffff">Website</span></a>]';
			if($OPMLURL!='') $websites .= ' [<a href="'.$OPMLURL.'" title="'.$OPMLURL.'" target="_blank"><span style="color:#ffffff">Logo</span></a>]';
		}
		$sql   = 'SELECT * FROM sdJournals WHERE fkPublisherID='.$fkPublisherID.' ORDER by journalTitle';
		$res  = $db->query($sql) or die("<p>$sql</p>".$db->error());
		if($db->numrows($res)>0) {
			$thisSchoolName = 'Found <b>'.$db->numrows($res).'</b> journals for ';
            $thisSchoolBody = getBrowseRows($db, $res, $action, $fkPublisherID, $subAction, $badstatus, $orderpubsort, $pubsort, $indexphp); 
			if($thisPublisherName!='') $thisSchoolName .= $thisPublisherName.' '.$websites;
			else $thisSchoolName .= ' this publisher';
		} else $thisSchoolBody = '<p>Found No Journal for this Publisher</p>';
	}
	require_once('School.inc');
	return $schoolHTML;
}
///////////////////////////////////////////////////////////////////
function addSorting($sql, $pubsort, $orderpubsort, $field1 = 'fkPublisherID', $field2 = 'journalTitle', $field3 = 'followers') {
    if($pubsort=='p') {
        $sql .= " ORDER by $field1 ";
        if($orderpubsort==1) $sql .= ' DESC ';
    } else if($pubsort=='s') {
        $sql .= " ORDER by $field2 ";
        if($orderpubsort==1) $sql .= ' DESC ';
    } else $sql .= " ORDER BY $field3 DESC ";
	return $sql;
}

///////////////////////////////////////////////////////////////////
function getJournals($db) {
    $arrayJournals = $arrayPISSN = $arrayEISSN = $bigArray = array();
    $arrayPublishers = getPublishers($db);
    $arrayRights = array(
            "0" => "Subscription",
            "1" => "Free",
            "2" => "Partially Free",
            "3" => "OA",
            "4" => "Unknown",
            "5" => "Hybrid");
    $arrayStatus = array(
            "0" => "Unknown",
            "1" => "OK",
            "2" => "Unknown",
            "3" => "Unknown",
            "4" => "RSS ERROR",
            "5" => "Archived");
    $sql = 'SELECT `journalID`, `journalTitle`, `journalISSNonline`, `journalISSNprint`, `journalHtmlURL`, `journalXmlURL`, `fkPublisherID`, `journalRights`, `journalStatus` FROM `sdJournals` ORDER BY journalID';
    $res = $db->query($sql);
    if ($db->numrows($res) > 0) {
        while ($row = $db->fetcharray($res)) { //1
            extract($row);
            $arrayJournals[$journalID]['title'] = $journalTitle;
            $arrayEISSN[$journalID] = trim($journalISSNonline);
            $arrayPISSN[$journalID] = trim($journalISSNprint);
            $arrayJournals[$journalID]['URL'] = $journalHtmlURL;
            $arrayJournals[$journalID]['RSS'] = $journalXmlURL;
            $arrayJournals[$journalID]['publisher'] = '';
            if (isset($arrayPublishers[$fkPublisherID])) $arrayJournals[$journalID]['publisher'] = $arrayPublishers[$fkPublisherID];
            $arrayJournals[$journalID]['rights'] = $arrayRights[$journalRights];
            $arrayJournals[$journalID]['status'] = $arrayStatus[$journalStatus];
        }
    }
    $bigArray = array(
        $arrayJournals,
        $arrayPISSN,
        $arrayEISSN);
    return $bigArray;
}
///////////////////////////////////////////////////////////////////
function getDOAJrows($db, $res, $action, $fkPublisherID, $publisher, $badstatus, $indexphp) {
    $bigArray = getJournals($db);
    if (!isset($publisher)) $publisher = '';
    $thisAJAX = <<<ajax
<script language="JavaScript" type="text/javascript">
function move2JTOCS(id, doajid, jid) {
    var xmlHttpReq = false;
    var self = this;
    // Mozilla/Safari
    if (window.XMLHttpRequest) {
        self.xmlHttpReq = new XMLHttpRequest();
    }
    // IE
    else if (window.ActiveXObject) {
        self.xmlHttpReq = new ActiveXObject("Microsoft.XMLHTTP");
    }
    self.xmlHttpReq.open('GET', "move2jtocs.php?jid="+jid+"&doajid="+doajid, true);
    self.xmlHttpReq.onreadystatechange = function() {
        if (self.xmlHttpReq.readyState == 4) {
            var response = self.xmlHttpReq.responseText;
			id.innerHTML=response;
        }
    }
    self.xmlHttpReq.send(null);
}
</script>
ajax;
    $thisSchoolBody = $thisAJAX . '<table width="98%" border="0" align="center" cellpadding="5" cellspacing="1" bgcolor="#E6E6FA">
					 <tr style="background-color:#898989;font-weight:bold">
					 <td valign="top" align="center" style="color:#ffffff">Title</td>
					 <td valign="top" align="center" style="color:#ffffff">e-ISSN</td>
					 <td valign="top" align="center" style="color:#ffffff">p-ISSN</td>
					 <td valign="top" align="center" style="color:#ffffff">Publisher</td>
					 <td valign="top" align="center" style="color:#ffffff">Subjects</td>
					 <td valign="top" align="center" style="color:#ffffff">Actions</td>
					 <td valign="top" align="center" style="color:#ffffff">Links</td></tr>';
    $bgcolor1 = '#EEEEEE';
    $bgcolor2 = '#F0E68C';
    while ($row = $db->fetcharray($res)) { //1
        $journalISSNonline = $journalISSNprint = $journalTitleLink = $publisher = $journalHtmlURL = $journalXmlURL = $journalTitle = $journalClassification = $visit = $journalComments = '';
        $journalRights = 0;
        $red = '<img src="images/red.gif" alt="URL missing. Needs attention" title="URL missing. Needs attention" border="0" />';
        $fkPublisherID = $journalStatus = 0;
        extract($row);
        $back_color = '';
        $thisJournalID = 0;
        $thisTitle = $thisEISSN = $thisPISSN = $thisPublisher = $thisRight = $thisStatus = $thisURL = $thisRSS = $FoundRow = '';
        $printISSN = trim($journalISSNprint);
        $eISSN = trim($journalISSNonline);
        if ($printISSN != '') $thisJournalID = array_search($printISSN, $bigArray[1]);
        else if ($eISSN != '') $thisJournalID = array_search($eISSN, $bigArray[2]);
        $ajax_link = "<span onclick=\"move2JTOCS(this, '$doajID', '11111111')\" style=\"color:#990000; font-weight:bold;\">Discard</span>";
        if ($thisJournalID > 0) {
            $bgcolor1 = '#c2f0c2';
            $back_color = " bgcolor='$bgcolor1'";
            $thisTitle = $bigArray[0][$thisJournalID]['title'];
            $thisEISSN = $eISSN;
            $thisPISSN = $printISSN;
            $thisPublisher = $bigArray[0][$thisJournalID]['publisher'];
            $thisRight = $bigArray[0][$thisJournalID]['rights'];
            $thisStatus = $bigArray[0][$thisJournalID]['status'];
            $thisURL = $bigArray[0][$thisJournalID]['URL'];
            $links = '<a href="' . $thisURL . '" title="HomePage" target="_blank"><img src="images/webpagelink.gif" alt="HomePage" title="HomePage" border="0" /></a><br>';
            $thisRSS = $bigArray[0][$thisJournalID]['RSS'];
            $links .= '<a href="' . $thisRSS . '" title="RSS" target="_blank"><img src="images/webpagelink.gif" alt="RSS" title="RSS" border="0" /></a>';
            $FoundRow = "<tr$back_color>
				<td valign=\"top\" align=\"left\" style=\"color:#606060\">$thisTitle</td>
				<td valign=\"top\" align=\"center\" style=\"color:#606060\">$thisEISSN</td>
				<td valign=\"top\" align=\"center\" style=\"color:#606060\">$thisPISSN</td>
				<td valign=\"top\" align=\"left\" style=\"color:#606060\">$thisPublisher</td>
  				<td valign=\"top\" align=\"left\" style=\"color:#606060\">$thisRight / $thisStatus<br>$thisJournalID</td>
				<td valign=\"top\" align=\"center\" style=\"color:#606060\"><span onclick=\"move2JTOCS(this, '$doajID', '$thisJournalID')\" style=\"color:#004d99; font-weight:bold;\">in JTOCs</span></td>
				<td valign=\"top\" align=\"center\" style=\"color:#606060\"> $links </td>
				</tr>";
            $ajax_link = "<span onclick=\"move2JTOCS(this, '$doajID', '$thisJournalID')\" style=\"color:#004d99; font-weight:bold;\">Mark as Found</span>";
        }
        $journalTitleLink = $journalTitle = str_replace('International ', 'Int. ', $journalTitle);
        if (strlen($journalTitle) > 40) $journalTitleLink = substr($journalTitle, 0, 38) . '...';
        $name = '<a href="' . $indexphp . '?action=AddJournal&subAction=frombrowse&browsestatus=' . $badstatus . '&doajID=' . $doajID . '&journalTitle=' . urlencode($journalTitle) . '&journalOtherTitle=' . urlencode($journalTitle) . '&journalHtmlURL=' . $journalHtmlURL . '&journalISSNonline=' . $journalISSNonline . '&journalISSNprint=' . $journalISSNprint . '&journalRights=3" title="' . $journalTitle . '" target="_blank">' . $journalTitleLink . '</a>';
        if (trim($journalHtmlURL) != '') $visit = ' <a href="' . $journalHtmlURL . '" title="HomePage" target="_blank"><img src="images/webpagelink.gif" alt="HomePage" title="HomePage" border="0" /></a>';
        $niceDate = getNiceDate($creationDate);
        $thisSchoolBody .= "<tr $back_color onmouseout=\"this.bgColor='$bgcolor1'\"  onmouseover=\"this.bgColor='$bgcolor2'\" onmousedown=\"this.bgColor = '$bgcolor2'\">
					<td valign=\"top\" align=\"left\" style=\"color:#606060\">$name</td>
					<td valign=\"top\" align=\"center\" style=\"color:#606060\">$journalISSNonline</td>
					<td valign=\"top\" align=\"center\" style=\"color:#606060\">$journalISSNprint</td>
					<td valign=\"top\" align=\"left\" style=\"color:#606060\">$doajPublisher</td>
					<td valign=\"top\" align=\"left\" style=\"color:#606060\">$doajSubjects</td>
					<td valign=\"top\" align=\"center\" style=\"color:#606060\">$ajax_link</td>
					<td valign=\"top\" align=\"center\" style=\"color:#606060\"> $visit </td>
					</tr>" . $FoundRow;
        $bgcolor1 = '#EEEEEE';
        $bgcolor2 = '#F0E68C';
    } //1
    return $thisSchoolBody;
}
///////////////////////////////////////////////////////////////////
function getBrowseRows($db, $res, $action, $fkPublisherID, $subAction, $badstatus, $orderpubsort, $pubsort, $indexphp, $q='') {
    $colum6_head = '';
    $colum1_head = 'Title';
    $colum1_value = 'journalTitle';
    $fkCrossRefJournalID = 0;
    if ($fkPublisherID > 0) {
        $colum4_head = 'LastHarvest';
        $colum4_value = 'journalLastUpdated';
        $colum7_head = "Status";
    } else {
        if ($orderpubsort == 1) {
            $orderpubsort = 0;
            $arr = '&darr;';
        } else {
            $orderpubsort = 1;
            $arr = '&uarr;';
        }
        $colum4_head = "Publisher <a href=\"$indexphp?action=$action&subAction=$subAction&badstatus=$badstatus&pubsort=p&orderpubsort=$orderpubsort\" style=\"font-weight:bold;color:#fff;text-decoration:none;\">$arr</a>";
        $colum4_value = 'publisher';
        $colum6_head = ''; 
        $colum7_head = "Status <a href=\"$indexphp?action=$action&subAction=$subAction&badstatus=$badstatus&pubsort=s&orderpubsort=$orderpubsort\" style=\"font-weight:bold;color:#fff;text-decoration:none;\">$arr</a>";
        if ($badstatus == 5 || $badstatus == 6 || $badstatus == 7) {
            $colum7_head = "Followers <a href=\"$indexphp?action=$action&subAction=$subAction&badstatus=$badstatus\" style=\"font-weight:bold;color:#fff;text-decoration:none;\">$arr</a>";
            $colum1_head = "Title <a href=\"$indexphp?action=$action&subAction=$subAction&badstatus=$badstatus&pubsort=s&orderpubsort=$orderpubsort\" style=\"font-weight:bold;color:#fff;text-decoration:none;\">$arr</a>";
            if ($badstatus == 6) $colum6_head = '';
        } else if ($badstatus == 3) {
            $colum4_head = 'creationDate';
            $colum4_value = 'journalCreatedDate';
        }
    }
    if (!isset($publisher)) $publisher = '';
    $thisSchoolBody = '<table width="98%" border="0" align="center" cellpadding="5" cellspacing="1" bgcolor="#E6E6FA">
		 <tr style="background-color:#898989;font-weight:bold">
		 <td valign="top" align="center" style="color:#ffffff">' . $colum1_head . '</td>
		 <td valign="top" align="center" style="color:#ffffff">e-ISSN</td>
		 <td valign="top" align="center" style="color:#ffffff"> p-ISSN</td>
		 <td valign="top" align="center" style="color:#ffffff">' . $colum4_head . '</td>
		 <td valign="top" align="center" style="color:#ffffff">Websites</td>
  		 <td valign="top" align="center" style="color:#ffffff">' . $colum6_head . '</td>
		 <td valign="top" align="center" style="color:#ffffff" width="76px">' . $colum7_head . '</td></tr>';
    $bgcolor1 = '#EEEEEE';
    $bgcolor2 = '#F0E68C';
    $arrayJournalStatus = array(
            '0' => '<span style="color:#CC6600">Missing</span>',
            '1' => '<b style=color:#006600>OK</b>',
            '2' => 'Unknown',
            '3' => 'Unknown',
            '4' => '<b style=color:#cc0000>Error</b>',
            '5' => '<i style=color:#666699>Archived</i>');
			
			
			
	if(is_array($res)) {
        $output = $res;
    } else {
	    while($row=$db->fetcharray($res)) {
            $output[] = $row;
        }
    }
    foreach($output as $thisres) {

    //while ($row = $db->fetcharray($res)) { //1
        $journalISSNonline = $journalISSNprint = $journalTitleLink = $publisher = $journalHtmlURL = $journalXmlURL = $journalTitle = $journalClassification = $visit = $journalComments = '';
        $journalRights = 0;
        $red = '<img src="images/red.gif" alt="URL missing. Needs attention" title="URL missing. Needs attention" border="0" />';
        $fkPublisherID = $journalStatus = 0;


        //extract($row);
	    foreach($thisres as $key => $val) {
	        if(is_numeric($key)) continue;
	        ${$key} = $val;
	    }			
		
		
        if ($rssFeedsVersion == 'JSON') $fetcherurl = 'json';
        else $fetcherurl = 'rss';
        $JournalPrice = "<span onclick=\"changeaccess(this, '$journalID')\"><img src=\"images/nofree.gif\" alt=\"Subscription/PPV\" title=\"Subscription/Pay Per View\" border=\0\"></span>";
        if ($journalRights == 1) $JournalPrice = '<img src="images/free.gif" alt="Free" title="Free" border="0">';
        else if ($journalRights == 2) $JournalPrice = "<span onclick=\"changeaccess(this, '$journalID')\"><img src=\"images/icon_partial.jpg\" alt=\"Partially Free\" title=\"Partially Free\" border=\"0\"></span>";
        else if ($journalRights == 3) $JournalPrice = '<b style="color:#006600">OA</b>';
        else if ($journalRights == 5) $JournalPrice = "<span onclick=\"changeaccess(this, '$journalID')\"><img src=\"images/icon_hybrid.gif\" alt=\"Hybrid\" title=\"Hybrid\" border=\"0\"></span>";
        $journalTitleLink = $journalTitle = str_replace('International ', 'Int. ', $journalTitle);
        if (strlen($journalTitle) > 40) $journalTitleLink = substr($journalTitle, 0, 38) . '...';
        $name = '<a href="' . $indexphp . '?action=EditJournal&subAction=frombrowse&browsestatus=' . $badstatus . '&journalID=' . $journalID . '&fkPublisherID=' . $fkPublisherID . '" title="' . $journalTitle . '">' . $journalTitleLink . '</a>';
        if ($badstatus == 6) $colum6_value = '';
        else  {
            $colum6_value = '';
            if ($badstatus == 5) $colum6_value .= '';
        } if ($fkPublisherID > 0) {
            $publisher = getPublisherByID($db, $fkPublisherID);
            if (strlen($publisher) > 36) $publisher = substr($publisher, 0, 32) . '...';
            $publisher = "<a href=\"$indexphp?action=BrowseSchools&fkPublisherID=$fkPublisherID\">$publisher</a>";
        }
        if ($badstatus == 5 || $badstatus == 6 || $badstatus == 7) {
            $span_title = trim(str_replace('x', '', $journalComments));
            if ($span_title != '') $reg = "<span title=\"$span_title\">$followers</span>";
            else $reg = $followers;
        } else $reg = $arrayJournalStatus[$journalStatus];
        if (trim($journalHtmlURL) != '') $visit = ' <a href="' . $journalHtmlURL . '" title="HomePage" target="_blank"><img src="images/webpagelink.gif" alt="HomePage" title="HomePage" border="0" /></a>';
        else $visit = $red;
        if (trim($journalXmlURL) != '') $visit .= ' <a href="' . $journalXmlURL . '" title="RSS feeds" target="_blank"><img src="images/rsslink.png" alt="RSS feeds" title="RSS feeds" border="0" /></a>';
        else $visit .= ' ' . $red;
        if (trim($journalDescription) != '') $visit .= ' <a href="' . $journalDescription . '" title="Cover" target="_blank"><img src="images/webpagelink.gif" alt="cover" title="cover" border="0" /></a>';
        else $visit .= ' ' . $red;
        $todo = 'archive';
        if ((!isset($badstatus) || $badstatus == '' || $badstatus == 0) && $fkPublisherID > 0) {
            $badstatus = 1;
            $todo = 'archive';
        }
        $removelink = "<a href=\"$indexphp?action=removeJournal&journalID=$journalID&fkPublisherID=$fkPublisherID&subAction=$subAction&browsestatus=$badstatus&badstatus=$badstatus&q=$q\" onclick=\"return confirm('Are you sure to $todo $journalTitleLink?');\"><img src=\"images/x.png\" alt=\"Archive this journal\" title=\"Archive this journal (remove it if you are browsing 'new journals')\" border=\"0\" /></a>";
        if ($fkCrossRefJournalID == 50000) {
            $bgcolor1 = '#FFCCCC';
            $bgcolor2 = '#FFCCCC';
            $removelink = '';
        }
        $tickbox = "<span onclick=\"changetext(this, '$journalID')\"><b>F</b></span>";
        $thisSchoolBody .= "<tr onmouseout=\"this.bgColor='$bgcolor1'\"  onmouseover=\"this.bgColor='$bgcolor2'\" onmousedown=\"this.bgColor = '$bgcolor2'\">
							<td valign=\"top\" align=\"left\" style=\"color:#606060\">$name $removelink $tickbox</td>
							<td valign=\"top\" align=\"center\" style=\"color:#606060\">$journalISSNonline</td>
							<td valign=\"top\" align=\"center\" style=\"color:#606060\">$journalISSNprint</td>
							<td valign=\"top\" align=\"left\" style=\"color:#606060\">" . ${$colum4_value} . "</td>
  							<td valign=\"top\" align=\"left\" style=\"color:#606060\">$visit $JournalPrice</td>
							<td valign=\"top\" align=\"center\" style=\"color:#606060\"> $colum6_value </td>
							<td valign=\"top\" align=\"center\" style=\"color:#606060\"> $reg </td>
							</tr>";
        $bgcolor1 = '#EEEEEE';
        $bgcolor2 = '#F0E68C';
    } //1
    return $thisSchoolBody;
}
///////////////////////////////////////////////////////////////////
function getPublishers($db) {
	$arrayPublishers = array();
	$sql   = 'SELECT `publisherID`, `publisher` FROM `sdPublishers` ORDER BY `publisherID`';
	$res  = $db->query($sql) or die("<p>$sql</p>".$db->error());
	if($db->numrows($res)>0) {
		while($row=$db->fetcharray($res)) { //1
			extract($row);
			$arrayPublishers[$publisherID] = $publisher;
		}
	}
	return $arrayPublishers;
}
///////////////////////////////////////////////////////////////////
function getArrayFromSQL($db, $key,$val,$tbl, $where, $sort) {
    $array = array();    
    if($res=$db->query("SELECT $key, $val FROM $tbl $where $sort"))
    while ($row = $db->fetcharray($res)) {
        extract($row);
        if(${$val}!='') $array[${$key}] = ${$val};
    }
    return $array;
}

///////////////////////////////////////////////////////////////////
function journalDataManager($db, $action, $q, $submit, $browsestatus, $subAction, $journalID=0, $fkPublisherID=0, $userType='user', $enforce=0, $br='', $dj='', $folljid=0, $delete='', $arch='', $reason=0) {
    $msg = $putCheck=false; $noerror = true; $output = '';
	$arrayOutput= array();
	if($submit=='Save') {
        $msg = validateEntryJournal();
	    if($msg=='') {
		    $arrayOutput = AddUpdateJournal($db, $browsestatus, $journalID, $enforce);
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
		} else $msg = '<p style="color:#cc0000;"><b>Following errors prevented the execution of your request:</b><br>'.$msg.'<b> Please correct the errors and try to save again</b></p>';
		$output = getJournalRecord($db, $browsestatus, $journalID, false, $subAction,$q, $msg, $userType);
   
	} else {
        $putCheck = true;
        $output = getJournalRecord($db, $browsestatus, $journalID, false, $subAction, '', '', $userType, $br, $dj, $folljid, $delete, $arch, $enforce, $reason);	
    }
    return $output;
}

/*
///////////////////////////////////////////////////////////////////
function journalSearch($db, $action, $q='', $submit, $browsestatus, $journalID=0, $fkPublisherID=0) {
    $msg = $putCheck=false; $noerror = true; $output = '';
	$arrayOutput= array();
	if($q!='') {
	    $q = trim($q);
		$subAction='queryFound';
	    if((findme($q,'-') && is_numeric(str_replace('X','',strtoupper(str_replace('-','',$q)))) && strlen($q)==9) || 
            (is_numeric(str_replace('X','',strtoupper($q))) && strlen($q)==8 && !findme($q,'-'))) {
            $thisissn = trim(str_replace('-','',$q));
            $res = getJournalByQuery($db, $thisissn, 'issn');
            $output = journalDataManagerOutput($db, $res, $browsestatus, $subAction, $q, $fkPublisherID);
		} else if(is_numeric($q) && $q>0 && $q<50000) {
		    $journalID = $q;
            $res = getJournalByQuery($db, $journalID, 'id');
            $output = journalDataManagerOutput($db, $res, $browsestatus, $subAction, $q, $fkPublisherID);
        } else {
            $res = getJournalByQuery($db, $q, 'title');
            $output = journalDataManagerOutput($db, $res, $browsestatus, $subAction, $q, $fkPublisherID);
        }
	} else {
        $putCheck = true;
        $output = getJournalRecord($db, $browsestatus, $journalID, false);
    }
    return $output;
}
///////////////////////////////////////////////////////////////////
function journalDataManagerOutput($db, $res, $browsestatus, $subAction, $q, $fkPublisherID) {
    if($db->numrows($res)==1) $output = getJournalRecord($db, $browsestatus, 0, $res, $subAction, $q);
    else if($db->numrows($res)>1) {
        $output = browseJournals($db, $res, 0, $subAction, $q, $fkPublisherID);
    } else {
        $output = getJournalRecord($db, $browsestatus, 0, false, $subAction, $q, $msg);
    }
    return $output;
}
*/

///////////////////////////////////////////////////////////////////
function getPublisherByID($db, $publisherID) {
    $publisher = '';
    if($publisherID > 0) {
        $qString = "SELECT publisher FROM sdPublishers WHERE publisherID = '$publisherID'";
        $res = $db->query($qString);
        if ($db->numrows($res) > 0) extract($db->fetcharray($res));
    }
    return $publisher;
}
///////////////////////////////////////////////////////////////////
function validateEntryJournal() {
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
/////////////////////////////////////////////////////////////
function AddUpdateJournal($db, $browsestatus, $journalID = 0, $enforce=0) {
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
    } else if ($journalTitle != '') {
        if (!findme($msg, 'ERROR')) {
            $sql .= ', journalCreatedDate = CURDATE()';
            $array_result = insertNewJournal($db, $sql, $journalTitle, $journalISSNprint, $journalISSNonline, $journalHtmlURL, $journalXmlURL, $fkPublisherID, $enforce);
            $journalID = $array_result[0];
            $msg .= $array_result[1];
        }
    }
    $arrayOutput[0] = $journalID;
    $arrayOutput[1] = $msg;
    return $arrayOutput;
}
/////////////////////////////////////////////////////////////
function insertNewJournal($db, $sql, $journalTitle, $journalISSNprint, $journalISSNonline, $journalHtmlURL, $journalXmlURL, $fkPublisherID, $enforce=0) {
    if (!isset($enforce)) $enforce = 0;
    $journalID = false;
	$array_result = newJournal($db, $journalTitle, $journalISSNprint, $journalISSNonline, $journalHtmlURL, $journalXmlURL, $fkPublisherID);
	$newJournal = $array_result[0];
	$msg .= $array_result[1];
    if ($newJournal || $enforce == 1) {
        $sql1 = 'INSERT INTO sdJournals SET ' . $sql . ', dateItemsUpdated = CURDATE()';
        $res = $db->query($sql1) or die("$sql1<br>" . $db->error());
        $journalID = $db->insertid();
    } else $msg .= "- It seems that <b>$journalTitle</b> is ALREADY in the Directory or the provided metadata is invalid.<br>";
	$array_result[0] = $journalID;
	$array_result[1] = $msg;
    return $array_result;
}
/////////////////////////////////////////////////////////////
function newJournal($db, $title, $jissnp, $jissne, $htmlUrl, $xmlUrl, $publisherID) {
    $indexphp = basename($_SERVER['PHP_SELF']);
    $array_return = array(false,'');
    if(($title != '' || $htmlUrl != '') && (findme($xmlUrl, 'http://') || findme($xmlUrl, 'https://'))) { //2
        $thisTitle = str_replace('&amp;', '&', trim($title));
        $thisTitle = trim(str_replace('latest papers', '', $thisTitle));
        $otherTitle = trim(str_replace(':', '', $thisTitle));
        $otherTitle = trim(str_replace('- AOP', '', $otherTitle));
        $title = trim($title);
        $htmlUrl = trim($htmlUrl);
        if(substr($htmlUrl, -1) == '/') $thisURL = substr($htmlUrl, 0, -1);
        else $thisURL = $htmlUrl;
        $oldHtmlUrl = $htmlUrl;
        $sql = 'SELECT fkPublisherID, journalID, journalTitle, journalHtmlURL, journalXmlURL, journalISSNonline, journalISSNprint, publisher FROM sdJournals, sdPublishers  WHERE (journalTitle ="' . trim($otherTitle) . '" OR journalTitle = "' . trim($thisTitle) . '" OR journalHtmlURL LIKE "' . $thisURL . '%") and publisherID=fkPublisherID';
        $res = $db->query($sql);
        if($db->numrows($res) == 0 && ($jissne != '' || $jissnp != '')) {
            $where = '(1=0 ';
            if ($jissnp != '') {
                $thisissn_p = trim(str_replace('-', '', $jissnp));
                $where .= " OR trim( replace( journalISSNonline , '-', '' ) ) = '$thisissn_p' OR  trim( replace( journalISSNprint , '-', '' ) )  = '$thisissn_p'";
            } if ($jissne != '') {
                $thisissn_e = trim(str_replace('-', '', $jissne));
                $where .= " OR trim( replace( journalISSNonline , '-', '' ) ) = '$thisissn_e' OR  trim( replace( journalISSNprint , '-', '' ) )  = '$thisissn_e'";
            }
            $sql = 'SELECT fkPublisherID, journalID, journalTitle, journalHtmlURL, journalXmlURL, journalISSNonline, journalISSNprint, publisher FROM sdJournals, sdPublishers  WHERE ' . $where . ') and publisherID=fkPublisherID';
			$res = $db->query($sql);
        }
        if($db->numrows($res) == 0) { //3
            $array_return[0] = true;
        } else {
            $msg = "<b>Found these journals:</b><ul>\n";
            while ($row = $db->fetcharray($res)) {
                extract($row);
                $msg .= "<li> $journalID, <a href=\"$indexphp?action=EditJournal&journalID=$journalID&fkPublisherID=$fkPublisherID\">$journalTitle</a> (" . substr($publisher, 0, 30) . ")</li>\n";
            }
            $msg .= "</ul>\n";
			$array_return[1] = $msg;
        }
    }
    return $array_return;
}
///////////////////////////////////////////////////////////////////
function getNiceDate($uglyDate) {
    if (isset($uglyDate)) $niceDate = date("j-M-y", strtotime($uglyDate));
    else $niceDate = ' Not yet';
    return $niceDate;
}
///////////////////////////////////////////////////////////////////
// displays Web Page
///////////////////////////////////////////////////////////////////
function displayPage($db, $indexphp, $action, $loginForm ='', $SESSION_UNAME='', $subAction='', $webPage='', $browsestatus='')  {
    $currentPage = $webPage;
    $addToTopBarr = $menuBar = $loginStatus = $lefMenuOptions = $leftMenuTitle = $showAction = '';
    if ($action != 'logout' && ($action == '' || $action == 'Home')) $webPage = getHomePage($db, $indexphp);
    if ($SESSION_UNAME == '' || $action == 'login' || $action == 'logout') {
        $str = getTemplate($loginForm);
        $leftMenuTitle = '&nbsp;<br>Please login to start.<br>&nbsp;<br><a href="mailto:journaltocs@hw.ac.uk">Email me</a> if you forgot your username or password.<br>&nbsp;';
    } else {
        if (!findme($webPage, 'ERROR')) $currentPage = '<table cellSpacing="0" cellPadding="0" width="98%" border="0"><tr><td vAlign="top" align="left"><p>' . $webPage . '</p></td></tr></table>';
        else $currentPage = '<p style="color:#cc0000">' . $webPage . '</p><!-- <form><input type="button" value="Back" onclick="history.back()"></form> -->';
        $str = getTemplate($currentPage);
        $menuBar = getTopMenu($action, $subAction, $indexphp, $browsestatus);
        $loginStatus = '&nbsp; <b class="important">Logged in as ' . $SESSION_UNAME . ' [<a href="' . $indexphp . '?action=logout" class="subnav2">Logout</a>]</b>';
    }
    $str = fillWebPage($str, $menuBar, $loginStatus, $leftMenuTitle, $lefMenuOptions, $showAction);
    $str = str_replace('index.php', $indexphp, $str);
    echo "$str";
    exit;
}
// displays Home Page
///////////////////////////////////////////////////////////////////
function getHomePage($db, $indexphp) {
    $publisherSelect = getSelectPublishers($db, 'publisherID', 'publisher', 'sdPublishers', 'ORDER BY publisher', 0, '50');
    $customerSelect = '';
    require_once ('Home.php');
    return $homeHTML;
}
///////////////////////////////////////////////////////////////////
function getJournalRecord($db, $browsestatus, $journalID=0, $thisres=false, $subAction='', $q='', $msg='', $userType='user', $br='', $dj='', $folljid=0, $delete='', $arch='', $enforce=0, $reason=0) {
	$indexphp = basename($_SERVER['PHP_SELF']);
	$domain = 'https://www.journaltocs.ac.uk';
	$leaBrowse = $leaSelect = $subjectsSelect = $publisherSelect = $schoolSelect = $sqaSelect = $thisSchoolName = $thisRegistrationStatus = '';
	$thisPublisherName = '';
	$arrayFree = array('0'=>'Subscription','1'=>'Free','2'=>'Partially Free','3'=>'OA','4'=>'Unknown','5'=>'Hybrid');
	if(!isset($fkPublisherID)) $fkPublisherID=0;
	$publisherSelect = getSelectPublishers($db, 'publisherID','publisher','sdPublishers', 'ORDER BY publisher', $fkPublisherID,'50');
    $journalOtherTitle = $journalHtmlURL = $journalXmlURL = $journalLogoURL = $journalDescription = $journalComments = '';
	$journalRights = ''; 
	require_once('findSchool.inc');
	$delTxt = $archTxt = '';
	if(!isset($browsestatus)) $browsestatus = 0;
	if(!isset($publisher)) $publisher = '';
	if(!isset($journalClassification)) $journalClassification = '';
	$arraySubjects = array();
	if(!isset($currentSubjectsList)) $currentSubjectsList = '';
	$showArch = true;
    if($journalID>0) {
	    if($arch=='yes') {
		    if(archiveJournal($db, $journalID, 'Archive this Journal', $reason)) {
	            $archTxt = "<span style=\"color:#0B3B0B;\"><b>** Journal with ID $journalID has been archived **</b></span><br>\n";
				$showArch = false;
			} else {
			    $archTxt = "<span style=\"color:#cc0000;\"><b>** Oops! I couldn't archive journal with ID: $journalID **</b></span><br>\n";
			}
	    }
    }
    $journalTitle=$journalISSNonline=$journalISSNprint = '';
    $addThisTxt = '<a href="'.$indexphp.'?action=AddJournal" style="color:#ffffff;font-weight:bold;" title="Add a New Journal">Add Journal</a>';
	$fields_select = ' journalID, journalTitle, journalOtherTitle, journalISSNonline, journalISSNprint, journalHtmlURL, journalXmlURL, fkPublisherID, journalLogoURL, journalDescription, foundNewItemsDate, journalClassification, journalSubjects AS coverError, journalRights, journalLastUpdated, journalStatus, rssFeedsVersion, rssFeedsModules, rssCount, journalComments, journalCreatedDate, dictStatus, luceneStatus, pubItem, qualityRSS, richMetadata, dateItemsUpdated ';

    if(is_array($thisres)) {
	    foreach($thisres[0] as $key => $val) {
	        if(is_numeric($key)) continue;
	        ${$key} = $val;
	    }
	} else if($thisres) {
	    extract($db->fetcharray($thisres));
	} else if($journalID>0) {
	    if($subAction=='frombrowse' && $browsestatus==3) {
		    $qqs = $db->query("SELECT * FROM sdJournals WHERE journalID='$journalID'") or die($db->error());
			if($db->numrows($qqs)>0) extract($db->fetcharray($qqs));
			else if($journalID>20000) {
		        $qqs = $db->query("SELECT $fields_select FROM sdJournals WHERE journalID='$journalID'") or die($db->error());
	            extract($db->fetcharray($qqs));
				$fkCrossRefJournalID = 0;
			}
		} else {
		    $qqs = $db->query("SELECT $fields_select FROM sdJournals WHERE journalID='$journalID'") or die($db->error());
	        if($db->numrows($qqs)>0) extract($db->fetcharray($qqs));
			else $msg = '<p>Journal Not Found';
			$fkCrossRefJournalID = 0;
	    }
	}
	
	if($journalID>0) {
	    if(trim($journalClassification)!='') {
		    $array = explode (' ',$journalClassification);
			foreach($array as $val) {
			   $val = trim($val);
		       if($val!='') $arraySubjects[] = $val;
			}
		}
	    $thisSchoolName = ' '.$journalTitle;
	} else {
		foreach($_GET as $key => $val) $$key = $val;
		require('lib/postData.php');
		if(!isset($journalISSNonline)) $journalISSNonline='';   if(!isset($journalISSNprint)) $journalISSNprint='';  if(!isset($journalTitle)) $journalTitle='';
	    $journalISSNonline = trim($journalISSNonline);
	    $journalISSNprint = trim($journalISSNprint);
	    $journalISSNonline = str_replace('--','-',trim(str_replace(' ','-',trim($journalISSNonline)))); 
	    $journalISSNprint = str_replace('--','-',trim(str_replace(' ','-',trim($journalISSNprint)))); 
	    $journalISSNonline = trim(str_replace('','',str_replace('&#1061;','X',str_replace('&#8208;','-',str_replace('&#183;','-',str_replace('&#8211;','-',trim($journalISSNonline)))))));	
	    $journalISSNprint = trim(str_replace('','',str_replace('&#1061;','X',str_replace('&#8208;','-',str_replace('&#183;','-',str_replace('&#8211;','-',trim($journalISSNprint)))))));	
	    $journalTitle = substr(htmlspecialchars(trim(strip_tags($journalTitle))), 0, 200); 
	    $journalOtherTitle = substr(htmlspecialchars(trim(strip_tags($journalOtherTitle))), 0, 200); 
	    $journalHtmlURL = str_replace(' ','',substr(trim(strip_tags($journalHtmlURL)), 0, 250)); 
	    $journalXmlURL = str_replace(' ','',substr(trim(strip_tags($journalXmlURL)), 0, 250)); 
	    $journalLogoURL = str_replace(' ','',substr(trim(strip_tags($journalLogoURL)), 0, 250));
	    $journalDescription = str_replace(' ','',substr(trim(strip_tags($journalDescription)), 0, 250));
	    $journalComments = substr(htmlspecialchars(trim(strip_tags($journalComments))), 0, 400); 
	    $journalLogoURL = trim($journalLogoURL);
	    $journalDescription = trim($journalDescription);
	     $thisSchoolName = 'New Journal';
		 $addThisTxt = '';
	}
	if($userType=='user' || $userType=='dev') $enforceNewJournalCreation = '';
	else $enforceNewJournalCreation = '<br><input type="checkbox" name="enforce" value="1" /> Force creation';
    if($journalID>0) {
        $enforceNewJournalCreation = '';
	    $delTxt = "<!--Move followers  to <input type=\"text\" name=\"folljid\" value=\"\" size=\"6\" /> and <a href=\"$indexphp?action=EditJournal&subAction=$subAction&br=si&browsestatus=$browsestatus&journalID=$journalID&fkPublisherID=$fkPublisherID\" title=\"Delete this Journal\">delete this Journal</a>\n -->";
		if($br=='si') {
	        if($dj=='yes') {
			    if(deleteJournal($db, $journalID, $folljid, $delete)) {
		            $delTxt = "<span style=\"color:#0B3B0B;\"><b>** OK, Journal \"<b>$journalTitle</b>\" (ID: $journalID) has been removed from JournalTOCs **</b></span><br>\n";
				} else {
				    $delTxt = "<span style=\"color:#cc0000;\"><b>** Oops! I couldn't remove journal \"<b>$journalTitle</b>\" (ID: $journalID) **</b></span><br>\n".$delTxt;
				}
		    } else {
	            $delTxt = "<span style=\"color:#cc0000;\">Are you sure you want to permanently delete journal \"<b>$journalTitle</b>\" (ID: $journalID)?<br>If, yes click on <a href=\"$indexphp?action=EditJournal&subAction=$subAction&br=si&dj=yes&browsestatus=$browsestatus&journalID=$journalID&fkPublisherID=$fkPublisherID\" title=\"Delete this Journal\">Delete this Journal</a></span>\n";
	        }
		}
	}
	$color = '#E8E8E8';
	$array = array();  
	$sql = 'SELECT headID, headName FROM sdSubjectHeadings WHERE headID = papaHead AND headID>0 ORDER BY headName ASC';
	if($res=$db->query($sql)) {
	    while ($row = $db->fetcharray($res)) {
            extract($row);
	        $subjectsSelect .='<option value="'.$headID.'"';
	        if(in_array($headID,$arraySubjects)) {
		        $subjectsSelect .=' selected';
			    $currentSubjectsList .= "<li> $headName ($headID)</li>\n";
		    }
			if($color=='#dbf3f3') $color = '#E8E8E8';
			else $color = '#dbf3f3';
		    $subjectsSelect .=' style="background-color:'.$color.'; font-weight: bold;"> '.$headName.'</option>'."\n";			
			$sql = 'SELECT headID, headName FROM sdSubjectHeadings WHERE headID != papaHead AND papaHead = '.$headID.' ORDER BY headName ASC';
	        $sec=$db->query($sql);
			if($db->numrows($sec)>0) {
	            while ($rsec = $db->fetcharray($sec)) {
                    extract($rsec);
					$headName = ucfirst(strtolower($headName));
					$subjectsSelect .='<option value="'.$headID.'"';
	                if(in_array($headID,$arraySubjects)) {
		                $subjectsSelect .=' selected';
				        $currentSubjectsList .= "<li> $headName ($headID)</li>\n";
		            }
		            $subjectsSelect .=' style="background-color:'.$color.';"> '.$headName.'</option>'."\n";	
			    }
			}
	    }
	}

	if($currentSubjectsList!='') $currentSubjectsList = "<ol>$currentSubjectsList</ol>\n";
	$indexStatus = "<b>OK</b>";
	$qqs = $db->query("SELECT * FROM sdDictionary WHERE rec_id='$journalID'") or die($db->error());
	if($db->numrows($qqs)==0) $indexStatus = "<span style=\"color:#cc0000\">missing</span>";
	$JournalPrice = "<span onclick=\"changeaccess(this, '$journalID')\"><img src=\"images/nofree.gif\" alt=\"Subscription/PPV\" title=\"Subscription/Pay Per View\" border=\0\"></span>"; 
	$qqs = $db->query("SELECT * FROM sdFollowers  WHERE fkJournalID='$journalID'") or die($db->error());
	$numfollowers = $db->numrows($qqs);
	if($numfollowers>1) $numfollowers = $numfollowers+1;
	$followStatus = "- Followers: <b>$numfollowers</b>";	 	
	$qqs = $db->query("SELECT * FROM sdDictionary WHERE rec_id='$journalID'") or die($db->error());
	if($db->numrows($qqs)==0) $indexStatus = "<span style=\"color:#cc0000\">missing</span>";	 
	if($journalRights==1) $JournalPrice = '<img src="images/free.gif" alt="Free" title="Free" border="0">';
	else if($journalRights==2) $JournalPrice = "<span onclick=\"changeaccess(this, '$journalID')\"><img src=\"/images/icon_partial.jpg\" alt=\"Partially Free\" title=\"Partially Free\" border=\"0\"></span>";
	else if($journalRights==3) $JournalPrice = '<b style="color:#006600">OA</b>';
	else if($journalRights==5) $JournalPrice = "<span onclick=\"changeaccess(this, '$journalID')\"><img src=\"images/icon_hybrid.gif\" alt=\"Hybrid\" title=\"Hybrid\" border=\"0\"></span>";
	$harvestTxt = "<p><b>Journal Metadata:</b><br>\n";
	$arrayJournalStatus = array('0'=>'<b style="color:#A0A0A0">New Journal</b>','1'=>'<b>OK</b>','2'=>'Wait','3'=>'','4'=>'<b style="color:#cc0000">Error</b>','5'=>'<b style="color:#666699">Archived</b>');
	if(!isset($journalStatus)) $journalStatus = '3';
	if(!isset($rssFeedsVersion)) $rssFeedsVersion = '';
	if(!isset($rssFeedsModules)) $rssFeedsModules = '';
	if(!isset($journalLastUpdated)) $journalLastUpdated = '';
	if($journalLastUpdated=='' || $journalLastUpdated=='0000-00-00') $journalLastUpdated= 'Never';
	else $journalLastUpdated = getNiceDate($journalLastUpdated);
	if(!isset($rssCount)) $rssCount = '';
	if(!isset($journalComments)) $journalComments = '';
	$harvestStatus = $arrayJournalStatus[$journalStatus];
	$harvestTxt .= "- Harvest Status: $harvestStatus<br>\n";
	$harvestTxt .= "- Search Index: $indexStatus<br>\n";
	$harvestTxt .= "- Access Rights: $JournalPrice<br>\n";
	if($rssFeedsVersion!='') $harvestTxt .= "- Feed Format: $rssFeedsVersion<br>\n";
	if($rssFeedsModules!='') $harvestTxt .= "- Modules: $rssFeedsModules<br>\n";
	if($rssCount!='') $harvestTxt .= "- Items in Feed: $rssCount<br>\n";
	$harvestTxt .= "- Last Updated: ".$journalLastUpdated."<br>\n";
	if($rssFeedsVersion=='JSON') $fetcherurl = 'json';
	else $fetcherurl = 'rss';
	$tickbox = ''; 
	if($userType=='user' || $userType=='dev') $tickbox='';
	$harvestTxt .= "- Journal ID: <b>".$journalID."</b> $tickbox<br>\n";
	$harvestTxt .= $followStatus."<br>\n";
	$harvestTxt .= "<b>Comments:</b><br><textarea name=\"journalComments\" rows=\"4\" cols=\"15\">$journalComments</textarea><br>\n";
	$freeOptions = 'Access Rights:<br><select name="journalRights">';
	for($h=0;$h<6;$h++) {
	   $freeOptions .= '<option value="'.$h.'"';
		 if($h==$journalRights) $freeOptions .= ' selected';
		 $freeOptions .= '>'.$arrayFree[$h].'</option>';
	}
    $freeOptions .= '</select>';
	$harvestTxt .= "$freeOptions</p>\n";
	$publisherSelect = getSelectPublishers($db, 'publisherID','publisher','sdPublishers', 'ORDER BY publisher', $fkPublisherID);
    $harvestTxt = "$delTxt $archTxt $harvestTxt";
	$harvestTxt .= "$enforceNewJournalCreation";
	$jtocsPage = "<a href=\"$domain/index.php?action=tocs&journalID=$journalID\" target=\"_blank\"><img src=\"$domain/images/journaltocs_small_icon_color.gif\" border=\"0\" style=\"margin:0px; padding: 0; vertical-align: middle;\" /></a>";
	require_once('schoolHTML.php');
	require_once('School.inc');
	return $schoolHTML;
}
///////////////////////////////////////////////////////////////////
function getSelectPublishers($db, $fieldID, $fieldName, $selectTable, $order, $fkPublisherID = - 1, $strlen = 90)  {
    $publisherSelect = $thisPublisherName = '';
    $array = getArrayFromSQL($db, $fieldID, $fieldName, $selectTable, '', $order);
    if(sizeof($array) > 0)  {
        foreach($array as $key => $val) {
            $publisherSelect .= '<option value="' . $key . '"';
            if($fkPublisherID == $key) {
                $publisherSelect .= ' selected';
                $thisPublisherName = $val;
            }
            if(strlen($val) > $strlen) $val = substr($val, 0, ($strlen - 2)) . '...';
            $publisherSelect .= '> ' . $val . '</option>' . "\n";
        }
    }
    return $publisherSelect;
}
///////////////////////////////////////////////////////////////////
function fillWebPage($str,$menuBar,$loginStatus,$leftMenuTitle,$lefMenuOptions,$showAction) {
    $filled=str_replace('THISMENUBAR',$menuBar,$str);
    $filled=str_replace('THISLOGINSTATUS',$loginStatus,$filled);
    $filled=str_replace('LEFTMENUTITLE',$leftMenuTitle,$filled);
    $filled=str_replace('LEFTMENUOPTIONS',$lefMenuOptions,$filled);
    $filled=str_replace('SHOWACTION',$showAction,$filled);
    return $filled;
}
///////////////////////////////////////////////////////////////////
function getTemplate($filler) {
    $serviceTemplate = "scholar.html";
    $filler = trim($filler);
    $str=load_file($serviceTemplate);
    if(findme($filler,' ')) {
        $str=str_replace('THISPAGEBODY',$filler,$str);
    } else if ($filler!='') {
	    if(file_exists($filler)) $filler = load_file($filler);
        $str=str_replace('THISPAGEBODY',$filler,$str);
    }
    return $str;
}
 ///////////////////////////////////////////////////////////////////
function archiveJournal($db, $journalID, $archive='', $reason='') {
    $array = array(
            '0' => 'Unknown reason',
            '1' => 'RSS feed has been removed by publisher',
            '2' => 'RSS feed is returning 403 Forbidden because publisher has put NOINDEX in their RSS page',
            '3' => 'The journal ceased publication',
            '4' => 'RSS feed is giving errors',
            '5' => 'RSS feed is timing out',
            '6' => 'Journal website is giving errors',
            '7' => 'Journal website is timing out',
            '8' => 'RSS feed is temporary inaccessible',
            '9' => 'Journal has no feed but has been added to support QOAM'
    );
    if($journalID > 0 && $archive == 'Archive this Journal' && $reason != '') {
        if (!is_numeric($reason) || $reason > 8) $reason = 0;
        $reasontxt = $array[$reason];
        if(!isset($array[$reason])) $reasontxt = $array[0];
        $sql = 'SELECT journalTitle FROM sdJournals WHERE journalID =' . $journalID; 
        $res = $db->query($sql);
        if ($db->numrows($res) > 0) {
            $sql = "UPDATE sdJournals SET journalStatus=5, journalLastUpdated=curdate(), journalComments='$reasontxt' WHERE journalID=$journalID";
            $res = $db->query($sql);
        } else return false;
    } else return false;
    return true;
}
///////////////////////////////////////////////////////////////////
function deleteJournal($db, $journalID, $folljid=0, $delete='') {
    if(!is_numeric($folljid)) $folljid = 0;
    if($folljid > 0 && $delete == 'Delete this Journal') {
        $sql = 'SELECT journalTitle FROM sdJournals WHERE journalID =' . $folljid;
        $res = $db->query($sql);
        if($db->numrows($res) > 0) {
            extract($db->fetcharray($res));
            $sql = "UPDATE sdFollowers SET fkJournalID=$folljid, journalTitle ='$journalTitle' WHERE fkJournalID = $journalID";
            $res = $db->query($sql);
        }
    }
    $sql = 'DELETE FROM sdJournals WHERE journalID =' . $journalID;
    $res = $db->query($sql);
    if($db->affectedrows() > 0) {
        $db->query('OPTIMIZE TABLE sdJournals');
        $res = $db->query('DELETE FROM sdDictionary WHERE rec_id =' . $journalID);
        return true;
    } else return false;
}
///////////////////////////////////////////////////////////////////
function getTopMenu($action, $subAction, $indexphp, $browsestatus, $sortby=0, $colsort='') {
    $extraPars = '';
    if($sortby > 0 || $colsort != '') $extraPars = "&sortby=$sortby&colsort=$colsort";
    if(!isset($subSubAction)) $subSubAction = '';
    $actionLink = $subSubActionLink = $subSubActionLink = '';
    $arrayValidActions = array(
            'Fetcher' => 'Fetcher',
            'Home' => 'Home',
            'AddJournal' => 'New Journal',
            'EditJournal' => ' Edit/View Journals',
            'findJournal' => ' Search Journals',
            'Processing' => 'Data Processing',
            'BrowsePublishers' => 'Browse Publishers',
            'AddPublisher' => 'Add Publisher',
            'EditPublisher' => 'Edit Publisher',
            'clean' => 'Clean DB',
            'removeJournal' => 'removeJournal',
            'BrowseSchools' => 'Browse Journals');
    $arrayValidSubActions = array(
            'Delete',
            'Search',
            'byStatus');
    $arrayValidSubSubActions = array('none' => 'none');
    $topMenu = 'Home';
    if($action == '' || $action == 'login' || $action == 'Home') {
        $action = 'Home';
    } else {
        $homeLink = '<a href="' . $indexphp . '">Home</a>';
        $actionLink = '<a href="' . $indexphp . '?action=' . $action . $extraPars . '">' . $arrayValidActions[$action] . '</a>';
        $subActionLink = '<a href="' . $indexphp . '?action=' . $action . '&subAction=' . $subAction . '">' . $subAction . '</a>';
        if(array_key_exists($action, $arrayValidActions)) {
            $topMenu = $homeLink;
            if(in_array($subAction, $arrayValidSubActions) || ($subSubAction == '' && $subAction == '')) $topMenu .= '&nbsp; &gt; &nbsp;' . $arrayValidActions[$action];
            else {
                if($subAction == 'frombrowse') $topMenu .= '&nbsp; &gt; &nbsp;<a href="' . $indexphp . '?action=BrowseSchools&subAction=byStatus&badstatus=' . $browsestatus . '">' . $arrayValidActions['BrowseSchools'] . '</a>';
                else {
                    $topMenu .= '&nbsp; &gt; &nbsp;' . $actionLink;
                    if($subSubAction == '') $topMenu .= '&nbsp; &gt; &nbsp;' . $subAction;
                    else if (array_key_exists($subSubAction, $arrayValidSubSubActions)) $topMenu .= '&nbsp; &gt; &nbsp;' . $subActionLink . '&nbsp; &gt; &nbsp;' . $subSubAction;
                }
            }
        }
    }
    return $topMenu;
}
?>
