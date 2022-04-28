<?php
require_once ('config.php');
///////////////////////////////////////////////////////////////////
function journalSearch($db, $q='') {
	$arrayOutput= array();
	if($q!='') {
	    $q = trim($q);
	    if((findme($q,'-') && is_numeric(str_replace('X','',strtoupper(str_replace('-','',$q)))) && strlen($q)==9) || 
            (is_numeric(str_replace('X','',strtoupper($q))) && strlen($q)==8 && !findme($q,'-'))) {
            $thisissn = trim(str_replace('-','',$q));
            $res = getJournalByQuery($db, $thisissn, 'issn');
		} else if(is_numeric($q) && $q>0 && $q<50000) {
		    $journalID = $q;
            $res = getJournalByQuery($db, $journalID, 'id');
        } else {
            $res = getJournalByQuery($db, $q, 'title');
        }
		$arrayOutput = journalDataManagerOutput($db, $res);
	}
    return $arrayOutput;
}
///////////////////////////////////////////////////////////////////
function journalDataManagerOutput($db, $res) {
	$output = array();
	if($db->numrows($res)>0) {
		while($row=$db->fetcharray($res)) {
			$output[] = $row;
		}
	}
	//print_r($output); exit;
    return $output;
}
///////////////////////////////////////////////////////////////////
function getJournalByQuery($db, $queryStr, $queryType) {
	$q_where = '1=0';
    if($queryType=='issn') {
		$q_where = "trim( replace( journalISSNonline , '-', '' ) ) = '$queryStr' OR  trim( replace( journalISSNprint , '-', '' ) )  = '$queryStr'";
	} else if($queryType=='id') {
        $q_where = "journalID  = '$queryStr'";
    } else {
        $q = str_replace(':','',str_replace('(','',str_replace(')','',$queryStr)));
        $qwords=explode(" ",$q);
        $num_user_words=count($qwords);
        if($num_user_words>0) {
	        $q_where='';
            for ($i=0;$i<$num_user_words;$i++) {
                if (issdStopword($db, $qwords[$i])==0) {
                    if(findme($qwords[$i],"'")) $q_where.='journalTitle like "%'.$qwords[$i].'%" AND ';
                    else $q_where.="journalTitle like '%".$qwords[$i]."%' AND ";
                }
            }
            $q_where=substr($q_where,0,-5);
	    }
	}
	$qString = "SELECT * FROM sdJournals WHERE $q_where";
	$res=$db->query($qString);
	if($db->numrows($res)>0) $return = $res;
	else $return = false;
	return $return;
}
////////////////////////////////////////////////////////////////////////
function issdStopword($db, $word) {
    $res = $db->query('SELECT word FROM sdStopword WHERE word="' . $word . '"');
    return $db->numrows($res);
}
?>