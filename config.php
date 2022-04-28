<?php 
$serviceDir = '/home/gla/mjo1/public_html/case/';
$libDir = $serviceDir.'lib/';
// common functions
require_once($libDir.'commonFunctions.php');
require_once($libDir.'getURLquery.php');
require_once($libDir.'getFormValues.php');
require_once($libDir.'userDataCheck2.inc');
// to stablish a MySQL connection:
$thisdb = $database = 'mjo1';
$db = connectDB("$thisdb");
if(!$db) echo "DB-conn error";

$baseURL = $URL = 'https://www2.macs.hw.ac.uk/~mjo1/case/';
$domain = 'https://www.journaltocs.ac.uk'; 
$indexphp = 'index.php';
$inputBatchDir = '/home/gla/mjo1/public_html/case/inputFiles';
$outputBatchDir = '/home/gla/mjo1/public_html/case/outputFiles';
?>
