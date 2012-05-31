<?php
$title = 'Adding an Applicable Group to Asset Factories in their AFcontainer';

$type_override = 'assetfactorycontainer';
$start_asset = '859908207f00000101f92de53e7c4d71';
/* Choose the AssetFactoryContainer with this ID */

$asset_type = 'assetFactoryContainer';
$asset_children_type = 'assetFactory';

function assetfactorytest($child) {
  return true;
}

function edittest($asset) {
  return true;
}


function changes(&$asset) {
  // applicableGroups is a semi-colon-separated list, just add them to the end
  $asset["applicableGroups"] = $asset["applicableGroups"] . ";WWWDevelopers";
  echo 'Applicable Groups would now be: '.$asset["applicableGroups"]."<br>";
}


include('header.php');

?>
