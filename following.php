<?php
require_once('config.php');
$journalID = 0;
if(isset($_GET['journalID'])) {
   $journalID = $_GET['journalID'];
   $sql = trim("SELECT * FROM $thisdb.sdFollowers WHERE fkJournalID=$journalID");
   $result=$db->query($sql) or die($sql."<br>\n".$db->error());
   $num = $db->numrows($result);
   echo "<b>$num</b>";
} else echo "0";
?>
