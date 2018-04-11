<?php

if ( isset($_GET['year']) && preg_match('/20[0-9][0-9]-20[0-9][0-9]/', $_GET['year']) ) {
  $events_year = $_GET['year'];
} else {
  $events_year = '2017-2018';
}

$message .= 'You are currently curating the events for '. $events_year. '. ';
if ( $events_year != '2017-2018' ) {
  $message .= '<a href="?year=2017-2018">Click here for 2017-2018</a>. ';
}
if ( $events_year != '2018-2019' ) {
  $message .= '<a href="?year=2018-2019">Click here for 2018-2019</a>. ';
}

if ( $events_year == '2017-2018' ) {
  $year_folder = 'c9674124c0a8022b686c5474de639b04';
  $pending_folder = 'c967458bc0a8022b686c5474c124a14e';
  $rejected_folder = 'c9674494c0a8022b686c5474449cc282';
  $deleted_folder = 'c9674514c0a8022b686c5474a12a169b';
  $previous_deleted_folder = '21885fb5c0a8022b048dce3c06a1d6f5';
  $acad_year = '2017-2018';
  $yearstart = '2017-09-01';
  $yearend = '2018-08-31';
} elseif ( $events_year == '2018-2019' ) {
  $year_folder = '713568b9c0a8022b0cc35af687a3ca8a';
  $pending_folder = '713572bec0a8022b0cc35af64a4aca7e';
  $rejected_folder = '713571e3c0a8022b0cc35af66793d191';
  $deleted_folder = '71357284c0a8022b0cc35af61d310b27';
  $previous_deleted_folder = 'c9674514c0a8022b686c5474a12a169b';
  $acad_year = '2018-2019';
  $yearstart = '2018-09-01';
  $yearend = '2019-08-31';
}


function simplexml_merge (SimpleXMLElement &$xml1, SimpleXMLElement $xml2) {
  // convert SimpleXML objects into DOM ones
  $dom1 = new DomDocument();
  $dom2 = new DomDocument();
  $dom1->loadXML($xml1->asXML());
  $dom2->loadXML($xml2->asXML());
  // pull all child elements of second XML
  $xpath = new domXPath($dom2);
  $xpathQuery = $xpath->query('/*/*');
  for ($i = 0; $i < $xpathQuery->length; $i++)
  {
    // and pump them into first one
    $dom1->documentElement->appendChild(
    $dom1->importNode($xpathQuery->item($i), true));
  }
  $xml1 = simplexml_import_dom($dom1);
}

/*

Variables are used by:

$yearstart and $yearend
  detect
  update

$acad_year
  curate
  detect
  
$deleted_folder
  curate
  detect
  
$rejected_folder
  import
  reject

$pending_folder
  curate
  import

$year_folder
  copy
  detect
  enable
  import
  update
  
 */
?>
