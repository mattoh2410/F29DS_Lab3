<?php
if(!isset($checkedBookDelivery )) {
  $checkedBookDelivery  = '';
  $bookDelMsg = ' <font color="#cc0000">No</font>';
} else {
  $bookDelMsg = ' <font color="#006600">Yes</font>';
}
$visitHomePage = $visitFeedsPage = $visitLogoPage = '';
if(!isset($thisRegistrationStatus)) $thisRegistrationStatus  = '';
if(!isset($subjectsSelect)) $subjectsSelect = '';
$subjectsSelect = '<option value=""> </option>'.$subjectsSelect;
if(!isset($fkCrossRefJournalID)) $fkCrossRefJournalID = 0;
if($fkCrossRefJournalID>0) $subjectsSelect = str_replace('<option value=""> </option>','<option value="" selected> </option>',$subjectsSelect);
//if(!isset($journalID)) $journalID = '';
if(!isset($journalID)) $journalID = 0;
if(!isset($journalOtherTitle)) $journalOtherTitle = '';
if(!isset($currentSubjectsList)) $currentSubjectsList = '';
if(!isset($harvestTxt)) $harvestTxt = '';
if(!isset($journalDescription)) $journalDescription = '';
if(!isset($coverError)) $coverError = '';
$cover_label = 'Journal Cover URL:';
$bcolo='#ffffff';
if($coverError=='404') {
    $cover_label = '<b style="color:#cc0000;">COVER ERROR!</b>';
    $bcolo = '#fee69b';
}
if($journalHtmlURL!='') $visitHomePage = ' <a href="'.$journalHtmlURL.'" title="Visit the journal homePage" target="_blank"><img src="images/webpagelink.gif" alt="Visit the journal homePage" title="Visit the journal homePage" border="0" /></a>';
if($journalXmlURL!='') $visitFeedsPage = ' <a href="'.$journalXmlURL.'" title="Visit TOC Current Issue RSS feeds" target="_blank"><img src="images/webpagelink.gif" alt="Visit the Current Issue RSS feeds" title="Visit the Current Issue RSS feeds" border="0" /></a>';
if($journalLogoURL!='') $visitLogoPage = ' <a href="'.$journalLogoURL.'" title="View the OnlineFirst RSS feeds" target="_blank"><img src="images/webpagelink.gif" alt="View the OnlineFirst RSS feeds" title="View the OnlineFirst RSS feeds" border="0" /></a>';
if($journalDescription!='') $visitDescripPage = ' <a href="'.$journalDescription.'" title="View the Journal Cover" target="_blank"><img src="images/webpagelink.gif" alt="View the OnlineFirst RSS feeds" title="View the Journal Cover" border="0" /></a>';
$thisRemoveButton  = <<<HTMLRemoveButton
<tr>
<td align="left"> &nbsp;
<form action="index.php?action=EditJournal&subAction=$subAction&br=si&browsestatus=$browsestatus&journalID=$journalID&fkPublisherID=$fkPublisherID" onsubmit="return confirm('Are you sure to delete $journalTitle?');" method="post" name="deleteJournalForm" style="border: 0px none; padding-top: 0px; padding-bottom: 0px;margin-top: 0px;">
or move followers  to <input style="background-color:#CCDDCC;" type="text" name="folljid" onclick="this.value='';" value="journalID (optional)" size="15" /> and <input type="submit" name="delete" value="Delete this Journal" />
<input type="hidden" name="dj" value="yes" />
</form>
<br>
</form>
HTMLRemoveButton;
$thisArchiveButton  = <<<HTMLArchiveButton
<form action="index.php?action=EditJournal&subAction=$subAction&browsestatus=$browsestatus&journalID=$journalID&fkPublisherID=$fkPublisherID" onsubmit="return confirm('Are you sure to archive $journalTitle?');" method="post" name="archiveJournalForm" style="border: 0px none; padding-top: 0px; padding-bottom: 0px;margin-top: 0px;">
or archive it because <select style="background-color:#CCDDCC;" name="reason">
<option value="0">Unknown reason</option>
<option value="1" selected>RSS feed has been removed by publisher</option>
<option value="2">RSS feed is returning 403 Forbidden because publisher has put NOINDEX in their RSS page</option>
<option value="3">The journal ceased publication</option>
<option value="4">RSS feed is giving errors</option>
<option value="5">RSS feed is timing out</option>
<option value="6">Journal website is giving errors</option>
<option value="7">Journal website is timing out</option>
<option value="8">RSS feed is temporary inaccessible</option>
<option value="9">Journal has no feed but has been added to support QOAM</option>
</select>
 <input type="submit" name="archive" value="Archive this Journal" />
<input type="hidden" name="arch" value="yes" />
</form>
</td>
</tr>
HTMLArchiveButton;
$nocoverhelp=$nofeedhelp='';
if(!isset($sendPwdsSchoolButton)) $sendPwdsSchoolButton = '';
if(!isset($deleteSchoolButton)) $deleteSchoolButton = '';
if(!isset($visitDescripPage )) $visitDescripPage  = '';
if($journalDescription=='' || $coverError=='404') $nocoverhelp = "<br><span style=\"color:#989898;\">If the journal doesn't have a cover use &nbsp; http://www.journaltocs.ac.uk/images/no_cover.jpg</span><br>";
if($journalXmlURL=='') $nofeedhelp = "<br><span style=\"color:#989898;\">If journal has no feed but has to be added use &nbsp; http://www.journaltocs.ac.uk/data/feeds/no_feed.xml</span><br>";
//if($userType=='user') $thisRemoveButton = '';
if($userType=='user' || $journalID==0) $thisRemoveButton = '';
if(!$showArch) $thisRemoveButton = $thisArchiveButton  = '';
$subjectFields = '';
$archiveFields =<<<fieldsArchive
<table width="98%" border="0" cellpadding="1" cellspacing="4" bgcolor="#E6E6FA">
<tr>
<td align="center"><input type="submit" name="submit" value="Save" /></td>
</form>
</tr>
</table>
fieldsArchive;
$disabled = 'disabled';
if($userType!='guest') {
$disabled = '';
$subjectFields =<<<fieldsSubject
<br /> 
<fieldset>
<legend>Subject Classification</legend>
<table width="100%" border="0" cellpadding="1" cellspacing="2" align="center" bgcolor="#E6E6FA">
<tr>
<td align="left" valign="top"> <select name="journalSubjects[]" size="24" multiple="true">$subjectsSelect</select></td>
<td align="left" valign="top"><span style="text-align:left"><b>Current Subjects:</b>$currentSubjectsList</span> $harvestTxt</td>
</tr>
</table>
</fieldset>

<br />
fieldsSubject;
$archiveFields =<<<fieldsArchive
<table width="98%" border="0" cellpadding="1" cellspacing="4" bgcolor="#E6E6FA">
<tr>
<td align="right"><input type="submit" name="submit" value="Save" /> $sendPwdsSchoolButton &nbsp; $deleteSchoolButton</td>
</form>
</tr>
$thisRemoveButton $thisArchiveButton
</table>
fieldsArchive;
}
$thisSchoolBody = <<<HTMLAddschool
<form action="" method="post" name="newSchool">
<table width="98%" border="0" cellpadding="0" cellspacing="8" bgcolor="#E6E6FA">
<tr>
<td align="left" valign="middle" colspan="3" style="color:#cc0000;">Title <span style="color:#cc0000; font-weight:bold">*</span> <i style="color:#cc0000;">(<b>*</b> Mandatory fields)</i><br><input type="text" name="journalTitle" value="$journalTitle" maxlength="250" size="68" />
<input type="hidden" name="journalID" value="$journalID" /> $jtocsPage  <br />
<span style="color:#000000;">Other Title</span><br><input type="text" name="journalOtherTitle" value="$journalOtherTitle" maxlength="250" size="68" />
</td>
</tr>
<tr>
<td align="left" valign="middle" colspan="3" style="color:#cc0000;">Publisher <span style="color:#cc0000; font-weight:bold">*</span> <select name="fkPublisherID"><option value=""> </option>$publisherSelect</select>
</td>
</tr>
<tr>
<td align="left" valign="middle" colspan="3" style="color:#cc0000;">HomePage URL:  <span style="color:#cc0000; font-weight:bold">*</span> <input type="text" name="journalHtmlURL" value="$journalHtmlURL" maxlength="500" size="70" />
$visitHomePage
</td>
</tr>
<tr>
<td align="left" valign="middle" colspan="3" style="color:#cc0000;">Current Issue RSS URL:   <span style="color:#cc0000; font-weight:bold">*</span> <input type="text" name="journalXmlURL" value="$journalXmlURL" maxlength="500" size="70" $disabled />
$visitFeedsPage
</td>
</tr>
<tr>
<td align="left" valign="middle" colspan="3">OnlineFirst RSS URL:  <input type="text" name="journalLogoURL" value="$journalLogoURL" maxlength="300" size="70" />
$visitLogoPage $nofeedhelp
</td>
</tr>
<tr>
<td align="left" valign="middle" colspan="3">$cover_label  <input type="text" name="journalDescription" value="$journalDescription" maxlength="300" size="70" style="background-color: $bcolo" />
<input type="hidden" name="oldJournalDescription" value="$journalDescription" />
$visitDescripPage $nocoverhelp
</td>
</tr>
<tr>
<td width="20%"> &nbsp;</td>
<td align="left" valign="middle"  colspan="2">e-ISSN: <span style="color:#cc0000; font-weight:bold">*</span>  <input type="text" name="journalISSNonline" value="$journalISSNonline" maxlength="20" size="15" /> 
&nbsp; p-ISSN: <span style="color:#cc0000; font-weight:bold">*</span>  <input type="text" name="journalISSNprint" value="$journalISSNprint" maxlength="20" size="15" /><br>(<i>enter both or any of the two ISSN numbers</i>)
</td>
</tr>
</table>

$subjectFields
&nbsp;<br />


$archiveFields
HTMLAddschool;
?>

