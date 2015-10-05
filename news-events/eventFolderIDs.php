<?php
$year_folder = '85a826eec0a8022b3d7ce269ce9477fa';
$pending_folder = '39e9a9bcc0a8022b3e74b75175234a8f';
$rejected_folder = '85a91e39c0a8022b3d7ce269adaec728';
$deleted_folder = 'f65414a5c0a8022b36e21ad719081b4f';
$acad_year = '2015-2016';
$yearstart = '2015-09-01';
$yearend = '2016-08-31';

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
?>