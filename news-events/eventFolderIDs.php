<?php

if ( isset($_GET['year']) ) {
  $events_year = $_GET['year'];
} else {
  $events_year = '2016-2017';
}

$message .= 'You are currently curating the events for '. $events_year. '. ';
if ( $events_year != '2015-2016' ) {
  $message .= '<a href="?year=2015-2016">Click here for 2015-2016</a>. ';
}
if ( $events_year != '2016-2017' ) {
  $message .= '<a href="?year=2016-2017">Click here for 2016-2017</a>. ';
}

if ( $events_year == '2015-2016' ) {
  $year_folder = '85a826eec0a8022b3d7ce269ce9477fa';
  $pending_folder = '39e9a9bcc0a8022b3e74b75175234a8f';
  $rejected_folder = '85a91e39c0a8022b3d7ce269adaec728';
  $deleted_folder = 'f65414a5c0a8022b36e21ad719081b4f';
  $acad_year = '2015-2016';
  $yearstart = '2015-09-01';
  $yearend = '2017-08-31';
} elseif ( $events_year == '2016-2017' ) {
  $year_folder = '21885c37c0a8022b048dce3cddbe9449';
  $pending_folder = '2188600ec0a8022b048dce3cbbf35c30';
  $rejected_folder = '21885e09c0a8022b048dce3c8fffcd32';
  $deleted_folder = '21885fb5c0a8022b048dce3c06a1d6f5';
  $acad_year = '2016-2017';
  $yearstart = '2016-09-01';
  $yearend = '2017-08-31';
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
