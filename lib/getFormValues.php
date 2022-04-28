<?php
if(isset($_POST)) {  
   foreach($_POST AS $key => $val) {
	   $$key = $val;
   }
}	 
?>