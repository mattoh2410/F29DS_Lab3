<?php
///////////////////////////////////////////////////////////////////
function checkStr($string, $error, $badString, $minLen, $maxLen) {
	$clen = strlen($string);
	//if($clen>$maxLen) $string = substr($string,0,$maxLen);
	if($clen<$minLen || $clen>$maxLen) {
	   return $error;
  } else {
     if($string==$badString) return $error;
	   else return '';
	}
}
?>