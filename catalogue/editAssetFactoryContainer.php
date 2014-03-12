<?php
$title = 'Adding an Applicable Group to Asset Factories in their AFcontainer';

$type_override = 'assetfactorycontainer';

// Note, also change the foldertest to TRUE for undergrad and FALSE for grad
$start_asset = '859908207f00000101f92de53e7c4d71,8598eabf7f00000101f92de5a7354dea,8562da157f00000101f92de5a835ef7d,859869be7f00000101f92de5fdd29102,8598b47d7f00000101f92de5f0838d0e'; // Undergrad
// $start_asset = '4e9e12dc7f000001015d84e0032be71f'; // Grad

$asset_type = 'assetFactoryContainer';
$asset_children_type = 'assetFactory';

function assetfactorytest($child) {
  return true;
}

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($asset) {
  // return false;
  return true;
}
function edittest($asset) {
  return true;
}


function changes(&$asset) {
  global $changed;
  $changed = false;
  if (!preg_match('/2014-2015/', $asset['placementFolderPath'])) {
    $asset['placementFolderId'] = '';
    $asset['placementFolderPath'] = preg_replace('/2013-2014/','2014-2015',$asset['placementFolderPath']);
    $changed = true;
  }
}


if (!$cron)
  include('../header.php');

?>
