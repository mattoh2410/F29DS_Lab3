<?php
if(!isset($publisherSelect)) $publisherSelect = '';
if(!isset($customerSelect)) $customerSelect = '';
if(!isset($publisherSelect2)) $publisherSelect2 = '';
if(!isset($homeMsg)) $homeMsg = '';
$homeHTML = <<<HTMLhome
$homeMsg
<table cellspacing="10" cellpadding="6" width="100%" border="0" bgcolor="#F0F8FF">
  <tr><td valign="top" align="left"  width="90%">	  
	<table cellspacing="0" cellpadding="5" border="1" bgcolor="#F0F8FF" align="center" height="100%" width="100%">
	<tr>
        <td class="quote" style="background: #3E68A6" valign="top">
		<p style="color:#ffffff;font-weight:bold;"> &nbsp; Journals</p>
	</td></tr><tr><td class="quote" style="background: #CADFFE" valign="top">
		<ul style="padding:0;margin-left:0px;text-indent: 0em;">
			 <li style="list-style-type:none;margin-left:10px;"> New Journals
			     <ul style="list-style-type: square">
				   <li style="margin-left: -10px;margin-top: 4px;"> <a href="index.php?action=AddJournal" title="Add a New Journal">Create new Journal</a> &nbsp; <br> <br></li>
			     </ul>
			 </li>
			 <li style="list-style-type:none;margin-left:10px;"> Find Journal
			    <ul style="list-style-type: square">
			       <form action="index.php?action=EditJournal" method="post" name="EditJournalForm" style="border: 0px none; padding-top: 0px; padding-bottom: 0px;margin-top: 0px;">
			       <li style="margin-left: -10px;margin-top: 4px;"> by Title 
				 <input type="text" name="q" value="" maxlength="100" size="40" />
			       <li style="margin-left: -10px;margin-top: 4px;"> or by ISSN number <input type="text" name="on_issn" value="" maxlength="20" size="15" /> &nbsp;  
			       <li style="margin-left: -10px;margin-top: 4px;"> or by journalID <input type="text" name="journalID" value="" maxlength="10" size="8" />
						 <input type="submit" name="findJournal" value="Go!" />
						 <br> <br>
			       </form>
		  	    </ul>
			 </li>
			 <li style="list-style-type:none;margin-left:10px;"> Browse Journals by Publisher 
				 <select name="fkPublisherID" onChange="javascript:location.href='index.php?action=BrowseSchools&fkPublisherID=' + options[selectedIndex].value"><option value=""> </option>
				 $publisherSelect
				 </select>
			 </li>
	       </ul>
	</td>
	</tr>
	</table>		
	</td>
	</tr>
</table>
HTMLhome;
?>
