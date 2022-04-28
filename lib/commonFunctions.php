<?php 
////  //// ticTOCs common PHP functions  ////
if(preg_match("/commonFunctions\.php/", $_SERVER['PHP_SELF'])) { die("Access denied"); }
///////////////////////////////////////////////////////////////////
function load_file($file){
  $str = '';
  if ($fp=fopen($file, 'r')) {
     while(!feof($fp)) $str.=fgets($fp, 1024);
	 fclose($fp);
	 return $str;
  } 
  return false;
}
//////////////////////////////////////////////////////////////////////////
function connectDB($database='') {
  //global $db;
  require_once('credentials.php');
  // initiate a new database connection
  if($database=='') $database = 'puref';
  $db = new db_connection("mysql");
  if($db->connect("mysql-server-1.macs.hw.ac.uk", "", $dbUsername, $dbPassword, 0,$database)) return $db;
  else return false;
}

class db_connection {
	var $connection;

	function __construct($type="") { }
	// connect to the database server
	
	function connect($host, $port, $login, $password, $pconnect, $database="") {
		if($port) { $host .= ":$port"; }		
		if( !($this->connection = mysqli_connect($host, $login, $password)) ) return false;
		if($database!='') if(!mysqli_select_db($this->connection, $database)) return false;
		return true;
	}

	function query($query) {
		return mysqli_query($this->connection,$query);
	}

	function error(){
		return mysqli_error($this->connection);
	}
	
	function numrows($result){
		return mysqli_num_rows($result);
	}
	
	function dbquery($database = "", $query){
		//deprecated
		return mysqli_db_query($database, $query);
	}
	
	function fetcharray($result){
		return mysqli_fetch_array($result);
	}
	
	function fetchassoc($result){
		return mysqli_fetch_assoc($result);
	}
	
	function fetchrow($result){
		return mysqli_fetch_row($result);
	}	
	
	function affectedrows(){
		return mysqli_affected_rows($this->connection);
	}
	
	function realescapestring($string){
		return mysqli_real_escape_string($string);
	}
	
	function insertid(){
		return mysqli_insert_id($this->connection);
	}
	
	function dataseek($result, $rownum){
		return mysqli_data_seek($result, $rownum);
	}
	
	function numcols($result){
		return mysqli_num_fields($result);
	}
	
	function fetchcol($result){
		return mysqli_fetch_field($result);
	}
	
	function selectdb($database){
		return mysqli_select_db($database, $this->connection);
	}
	function closec() {
	    return mysqli_close($this->connection);	
	}
}

//////////////////////////////////////////////////////////////// 
function findme($mystring,$findme) {
  $pos = strpos($mystring, $findme);
	if ($pos === false) return false;
  else return true;
}
?>
