<?php
$thisSchoolFind = <<<HTMLfindSchool
<div id="niceFind">
<form action="$indexphp?action=EditPublisher" method="post" name="editSchoolForm" style="border: 0px none; padding-top: 0px; padding-bottom: 0px;margin-top: 0px;">
Search Publisher by keyword  
<input type="text" name="q" value="$q" size="30" maxlength="80"> <input type="submit" name="findPublisher" value="Go!" />
or Browse <select name="fkPublisherID" onChange="javascript:location.href='index.php?action=BrowseSchools&fkPublisherID=' + options[selectedIndex].value"><option value=""> </option>
									 $publisherSelect
									 </select>				
									</form>
</div>
HTMLfindSchool;
?>
