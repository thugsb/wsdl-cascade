<?php
$title = 'Find pages with customized page regions';

// $type_override = 'page';
// $start_asset = '5272debc7f00000101f92de5f336e998'; // ugrad (only courses)
// $start_asset = '047ea2a27f00000201d3ecae371474a9'; // grad (courses and faculty)
$start_asset = '817373157f00000101f92de5bea1554a'; // area landing pages

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  return true;
}
function foldertest($child) {
  if (!preg_match('/\//', $child->path->path) and preg_match('/^[a-z]/', $child->path->path) )
    return true;
}
function edittest($asset) {
  // if (preg_match('/^[-a-z]+\/index/', $asset['path']) ) // only courses for ugrad
    return true;
  // This will miss womens-history/faculty/index
}

function changes(&$asset) {
  global $changed;
  $changed = false;
  $blockId = '';
  $formatId = '';
  if(!is_array($asset['pageConfigurations']->pageConfiguration)) {
    $asset['pageConfigurations']->pageConfiguration = array($asset['pageConfigurations']->pageConfiguration);
  }
  foreach ($asset['pageConfigurations']->pageConfiguration as $conf) {
    if ( $conf->name == 'HTML' ) {
      if(!is_array($conf->pageRegions->pageRegion)) {
        $conf->pageRegions->pageRegion = array($conf->pageRegions->pageRegion);
      }
      foreach ($conf->pageRegions->pageRegion as $region) {
        if ($region->name == 'DEFAULT') {
          $blockId = $region->blockId;
          $formatId = $region->formatId;
          echo $blockId;
        }
      }
    }
  }
  foreach ($asset['pageConfigurations']->pageConfiguration as $conf) {
    if ( $conf->name == 'MobileIA' ) {
      if(!is_array($conf->pageRegions->pageRegion)) {
        $conf->pageRegions->pageRegion = array($conf->pageRegions->pageRegion);
      }
      foreach ($conf->pageRegions->pageRegion as $region) {
        if ($region->name == 'CONTENT') {
          if ($region->blockId != $blockId) {
            $region->blockId = $blockId;
            $region->blockPath = '';
            $changed = true;
          }
          if ($region->formatId != $formatId) {
            $region->formatId = $formatId;
            $region->formatPath = '';
            $changed = true;
          }
        }
      }
    }
  }
}


if (!$cron)
  include('../header.php');

?>