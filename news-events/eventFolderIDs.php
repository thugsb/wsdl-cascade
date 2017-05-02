<?php

if ( isset($_GET['year']) ) {
  $events_year = $_GET['year'];
} else {
  $events_year = '2016-2017';
}

$message .= 'You are currently curating the events for '. $events_year. '. ';
if ( $events_year != '2016-2017' ) {
  $message .= '<a href="?year=2016-2017">Click here for 2016-2017</a>. ';
}
if ( $events_year != '2017-2018' ) {
  $message .= '<a href="?year=2017-2018">Click here for 2017-2018</a>. ';
}

if ( $events_year == '2016-2017' ) {
  $year_folder = '21885c37c0a8022b048dce3cddbe9449';
  $pending_folder = '2188600ec0a8022b048dce3cbbf35c30';
  $rejected_folder = '21885e09c0a8022b048dce3c8fffcd32';
  $deleted_folder = '21885fb5c0a8022b048dce3c06a1d6f5';
  $acad_year = '2016-2017';
  $yearstart = '2016-09-01';
  $yearend = '2017-08-31';
} elseif ( $events_year == '2017-2018' ) {
  $year_folder = 'c9674124c0a8022b686c5474de639b04';
  $pending_folder = 'c967458bc0a8022b686c5474c124a14e';
  $rejected_folder = 'c9674494c0a8022b686c5474449cc282';
  $deleted_folder = 'c9674514c0a8022b686c5474a12a169b';
  $acad_year = '2017-2018';
  $yearstart = '2017-09-01';
  $yearend = '2018-08-31';
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
