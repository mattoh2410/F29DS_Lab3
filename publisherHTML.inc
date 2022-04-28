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
if(!isset($journalID)) $journalID = '';
if(!isset($publisherID)) $publisherID = '';
if(!isset($currentSubjectsList)) $currentSubjectsList = '';
if(!isset($pathFinderTitlesSelect)) $pathFinderTitlesSelect = '';
if(!isset($sendPwdsSchoolButton)) $sendPwdsSchoolButton = '';
if(!isset($contactName)) $contactName = '';
if(!isset($contactEmail)) $contactEmail = '';
if(!isset($prefixDOI)) $prefixDOI='';
if(!isset($linkjournals)) $linkjournals = '';
if($linkjournals != '') $linkjournals = '<tr><td align="left" valign="top" colspan="2">&nbsp;<br>'.$linkjournals.' </td></tr>';
if(!isset($homePage)) $homePage = '';
if(!isset($OPMLURL)) $OPMLURL = '';
if($homePage!='') $visitHomePage = ' <a href="'.$homePage.'" title="Visit the publisher homePage" target="_blank"><img src="images/webpagelink.gif" alt="Visit the publisher homePage" title="Visit the publisher homePage" border="0" /></a>';
if($OPMLURL!='') $visitFeedsPage = ' <a href="'.$OPMLURL.'" title="View Publisher Logo" target="_blank"><img src="images/webpagelink.gif" alt="View Publisher Logo" title="View Publisher Logo" border="0" /></a>';
if(!isset($deleteSchoolButton)) $deleteSchoolButton='';
$thisSchoolBody = <<<HTMLAddpublisher
<form action="index.php" method="post" name="newPublisher"><input type="hidden" name="action" value="$action" />
<table width="98%" border="0" cellpadding="0" cellspacing="8" bgcolor="#E6E6FA">
<tr>
<td align="left" valign="middle" colspan="3" style="color:#cc0000;">Name <span style="color:#cc0000; font-weight:bold">*</span> <i style="color:#cc0000;">(<b>*</b> Mandatory fields)</i><br><input type="text" name="publisher" value="$publisher" maxlength="250" size="68" />
</td>
</tr>
<tr>
<td align="left" valign="middle" colspan="3" style="color:#cc0000;">HomePage URL:  <span style="color:#cc0000; font-weight:bold">*</span> <input type="text" name="homePage" value="$homePage" maxlength="200" size="70" />
$visitHomePage
</td>
</tr>
<tr>
<td align="left" valign="middle" colspan="3">Publisher Logo URL:    <input type="text" name="OPMLURL" value="$OPMLURL" maxlength="200" size="70" />
$visitFeedsPage
</td>
</tr>
</table>

<br /> 
<fieldset>
<legend>Metadata</legend>
<table width="100%" border="0" cellpadding="1" cellspacing="2" align="center" bgcolor="#E6E6FA">
<tr>
<td align="left" valign="top" colspan="2"> Contact Name: <input type="text" name="contactName" value="$contactName" maxlength="100" size="60" /> </td>
</tr>
<tr>
<td align="left" valign="top" colspan="2"> Contact Email: <input type="text" name="contactEmail" value="$contactEmail" maxlength="200" size="60" /> </td>
</tr>
<tr>
<td align="left" valign="top" colspan="2">&nbsp; &nbsp; &nbsp; &nbsp; PrefixDOI: <input type="text" name="prefixDOI" value="$prefixDOI" maxlength="30" size="20" /> </td>
</tr>
$linkjournals
</table>
</fieldset>
<br /> &nbsp;<br />
<table width="98%" border="0" cellpadding="1" cellspacing="4" bgcolor="#E6E6FA">
<tr>
<td align="center"><input type="hidden" name="publisherID" value="$publisherID" />  <input type="submit" name="submit" value="Save" /> $sendPwdsSchoolButton &nbsp; $deleteSchoolButton</td>
</tr>
<tr>
<td align="left"> &nbsp; <i style="color:#cc0000;"><b>*</b> Mandatory fields</i></td>
</tr>
</table>
</form>
HTMLAddpublisher;
?>

