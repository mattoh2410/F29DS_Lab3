<?php 
	if(isset($_POST)) {  
	   reset($_POST);
	   foreach($_POST as $key=>$val) {
		 if(is_array($val)) {
		    foreach($val as $subid) {
			   $array[] = $subid;
		    }
		    $$key = $array;
		 } else {
		   $$key = trim(strip_tags($val));
		 }
	   }
	}
?>
