<?php
$myquery = $_SERVER['QUERY_STRING']; 
if(findme($myquery,'=')) {
   $queryArray = explode("&",$myquery);
   foreach ($queryArray as $queryItem) {
      $querySegment = explode("=",$queryItem);
   		list ($key, $val) = $querySegment;
   		if ($val) $$key = $val;
   }
}
?>