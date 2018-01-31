<?php
date_default_timezone_set('America/New_York');

include_once(__DIR__.'/rollbar-init.php');
use \Rollbar\Rollbar;
use \Rollbar\Payload\Level;


$title = 'Test';

// $type_override = 'page';
// $start_asset = 'e59cc45a7f00000100c46dcf503b2144';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/^_[a-z]/', $child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/[a-z]/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $asd = $asset['structuredData'];
  $changed = false;
  // if ($asset["metadata"]->teaser != 'test') {
  //    $changed = true;
  //    $asset["metadata"]->teaser = 'test';
  // }
  // foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
  //   if ($dyn->name == "xxx") {
  //     // Do stuff
  //   }
  // }
  //
  // $wys = getNode(['group-primary','wysiwyg'],'text', $asd);
  // editNode('::CONTENT-XML-CHECKBOX::On', ['group-settings', 'primary'], 'text', $asd);
  //
  // foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
  //   if ($sdnode->identifier == "xxx") {
  //     // Do stuff
  //   }
  // }
}

if (!$cron) {include(__DIR__.'/header.php');}

?>