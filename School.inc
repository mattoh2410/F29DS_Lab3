<?php
if(!isset($addThisTxt)) $addThisTxt='';
if(!isset($thisSchoolFind)) $thisSchoolFind='';
if(!isset($msg)) $msg='';
if(!isset($thisSchoolName)) $thisSchoolName='';
$schoolHTML = <<<HTMLschool
$thisSchoolFind
$msg <table cellspacing="10" cellpadding="6" width="100%" border="0" bgcolor="#F0F8FF">
  <tr><td valign="top" align="left"  width="100%">	  
			<table cellspacing="0" cellpadding="5" border="0" bgcolor="#F0F8FF" align="center" height="100%" width="98%">
					<tr>
          <td class="quote" style="background: #3E68A6" valign="top">
					<span style="color:#ffffff;font-weight:bold;"> $thisSchoolName</span>
					</td><td class="quote" style="background: #3E68A6" valign="top" align="right"><span>  $addThisTxt </span></td></tr>
					<tr><td class="quote" style="background: #E6E6FA" valign="top" colspan="2">
					$thisSchoolBody
					</td>
					</tr>
					</table>		
	</td>
	</tr>
</table>
HTMLschool;
?>

