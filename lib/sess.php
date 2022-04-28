<?php
$sessionTable='sdStaffSession';
$sessionUsers='sdStaffNames';

class session { 
   var $key; 
   var $timeout; 
   // start() will initialize the session by generating the session key or ID 
   function start($timeout = "") { 
      $letters = range("a","z"); 
      $key = ""; 
      // generate our session's key formatted such as #a#aa# 
      for($i = 0; $i < 6; $i++) { 
         if(($i == 0) || ($i == 2) || ($i == 5)) $key .= rand(0,9); 
	 if(($i == 1) || ($i == 3) || ($i == 4)) $key .= $letters[rand(0,25)]; 
      } 
      // store the session's key in a method of the class 
      $this->key = $key; 
      // perform a conditional to test if the user defined the timeout and if not store the default value. 
	if($timeout == "") $this->timeout = 300; // five minutes 
	else $this->timeout = $timeout; 
	return 0; 
   } 

   // this function will register a value to session. (only one value, see replace() to update the value) 
   function register() { 
       global $f_user, $f_pass,$action, $kuku, $sessionTable, $sessionUsers, $db, $userType;
       // if key is not generated run start() 
       $IP=$this->fetchip();
       $val=0;
       $userType='user';
       if($this->key == "") $this->start(); 
       //see if the user has gooten already a userID. If not, create one for him:
       $newIP=ip2long($_SERVER['REMOTE_ADDR']); 
       $newSession=$this->key;
       $queryStr='SELECT userID,username, userType FROM '.$sessionUsers.' WHERE username = "'.$f_user.'" AND password=OLD_PASSWORD("'.$f_pass.'")';
	$result=$db->query($queryStr) or die('query failed - line 89: '.$insert."\n<BR>".$db->error());
	if($db->numrows($result) == 0) { 
            $action=='signIn';
	    return $val;
        } else {
            $fetch = $db->fetcharray($result); 
            // store userID and username into the live session table:
            $userID = stripslashes($fetch["userID"]); 
            $userType = stripslashes($fetch["userType"]); 
	    $insert = 'INSERT INTO '.$sessionTable.' VALUES('.$userID.',"'.$f_user.'",'.$newIP.',"'.$newSession.'")';
	    $result=$db->query($insert) or die('query failed - line 89: '.$insert."\n<BR>".$db->error());
       	    $update = 'UPDATE '.$sessionUsers.' SET sessionID = "'.$newSession.'",IP='.$newIP.' WHERE username ="'.$f_user.'"'.
			           ' AND password=OLD_PASSWORD("'.$f_pass.'")'; 
			 	$result=$db->query($update) or die('Update failed: '.$update.'<BR>'.$db->error());
			 	setcookie('sess_key',$this->key,time()+3600*60);
			 	$kuku = $this->key; 
            return 1;
       }
  } 

	 function read() { 
        // set $sess_val global - the variable of the session value. 
   	    global $sess_val, $username,$kuku, $sessionTable, $sessionUsers, $db, $indexphp, $loginForm, $userType;
   	    // if the cookie doesn't exisit send them back to the login2 screen. 
		if($indexphp=='') $indexphp = 'index.php';
   	    if(!isset($_COOKIE['sess_key']) && $kuku=='') { 
            header("Location: $indexphp?action=signIn");
            exit; 
        } else if($kuku!='') {
	        $this->key = $kuku; 
	    } else {
            // fetch the session key from the cookie. 
            $this->key = $_COOKIE["sess_key"]; 
        }
   	    //find userID of this session: 
	 	 $queryString = "SELECT userID,username FROM ".$sessionTable." WHERE sessionID = '" . $this->key . "'";
	 	 $result=$db->query($queryString) or die('query failed - line 92: '.$queryString."\n<BR>".$db->error());
   	    if($db->numrows($result) == 0) { 
            header("Location: $indexphp?action=signIn");
            exit; 
        } 
   	    $fetch = $db->fetcharray($result); 
   	    // store the session value to $sess_val 
   	    $userID = stripslashes($fetch["userID"]); 
   	    $username = stripslashes($fetch["username"]); 
		$res=$db->query("SELECT userType FROM $sessionUsers WHERE userID = '$userID'");
        extract($db->fetcharray($res));		
   	    return $username;
   } 

	 // this function will test if the user has been inactive for the defined timeout 
	 function expire() { 
	    // fetch the last access and expirations from the database 
			global $indexphp, $db;
		 	if($indexphp=='') $indexphp = 'index.php';
			$query = $db->query("SELECT access, sec_expire, stamp_expire FROM sessions WHERE sess_key = '" . $this->key . "'") or die("query failed - line 78"); 
			$fetch = $db->fetcharray($query); 
			$access = $fetch["access"]; 
			$expire = $fetch["sec_expire"]; 
			$timeout = $fetch["stamp_expire"]; 

			// test if session is expired based on defined timeout 
 			if(($timeout - $access) <= ($expire - $expire)) { 
        $this->destroy(); 
    		header("Location: $indexphp" );
      } 
   } 

	 // this function will update the session value 
	 function replace($val) { 
	    global $db;
	    // fetch the user key from cookie 
		$this->key = $_COOKIE["sess_key"]; 
		// update the database with the new value 
		$query = $db->query("UPDATE ".$sessionTable." SET val = '" . $val . "' WHERE sess_key = '" . $this->key) or die("query failed - line 77"); 
   } 

	 // this function will kill the session 
	 function destroy($key = "") { 
	   global $sessionTable, $sessionUsers, $db;
		 // fetch the user key from cookie 
		 if(isset($_COOKIE["sess_key"])) $this->key = $_COOKIE["sess_key"]; 
		 if(isset($_COOKIE["single_sess_IP"])) $userIP = $_COOKIE["single_sess_IP"]; 
		 // delete session from database 
		 $queryStr="DELETE FROM ".$sessionTable." WHERE sessionID  = '" . $this->key . "'";
		 $query = $db->query($queryStr) or die("query $queryStr failed - line 86"); 
		 // remove cookie from the user's computer 
		 $delete = setcookie("sess_key" , $this->key, time()-3600*60);  
   } 

	 function fetchip() {
	    //get useful vars:
 	 	if(getenv('HTTP_X_FORWARDED_FOR')) $ip = getenv('HTTP_X_FORWARDED_FOR');
   	    else $ip = getenv('REMOTE_ADDR'); 
		return $ip;
    }

	 function registerSingleSession() { 
	    global $f_user, $f_pass, $db;
   		// if the cookie doesn't exist, start a new single session. 
   		if(!$_COOKIE['single_sess_key']) { 
         // if key is not generated run start() 
   		 	 if($this->key == "") $this->start(); 
			 	 $userIP=$this->fetchip();
   		 	 //echo "IP: $IP<br>\n";
   		 	 $insert = "INSERT INTO single_session (id, sess_key, queryHistory, ip, username,  password, access) VALUES (0,'" . 
               $this->key. "', '' , '" .$userIP. "' , '".$f_user."',OLD_PASSWORD('".$f_pass."')," . time() .")"; 
	 		   $result=$db->query($insert) or die('query failed - line 224: '.$insert."\n<BR>".$db->error());
			 	 setcookie('single_sess_key',$this->key,time()+3600*60); 
			 	 setcookie('single_sess_IP',$userIP,time()+3600*60);
      }  else {
         // fetch the session key from the cookie. 
       	 $this->key = $_COOKIE["single_sess_key"]; 
			 	 //get the IP of the user and its authorised data:
			 	 $query = $db->query('SELECT queryHistory,ip FROM single_session WHERE sess_key = "'. $this->key. '"') or die("query failed - line 221"); 
       	 if($db->numrows($query) == 0) { 
           //it is strange, but Ip was not found :-( 
      		 return;
         } 
  		 	 $fetch = $db->fetcharray($query); 
   		 	 $userIP = stripslashes($fetch["ip"]); 
   		 	 $userQueryHistory = stripslashes($fetch["queryHistory"]); 
      }
	 		return $userIP;
   } 
} 
?>
