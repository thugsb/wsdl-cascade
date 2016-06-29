<?php
date_default_timezone_set('America/New_York');
$title = 'Edit metadata tags of pages, without needing to disable required fields';

$type_override = 'page';
// $start_asset = '5d1dfdff7f000002310aff0e7f135fa5,5d1dd11c7f000002310aff0ee32de382,5d1d66787f000002310aff0ebb4b6b11,5d1d1ac37f000002310aff0ea3eb77e1,5d1b6dd27f000002310aff0ef1e03333'; // The five used asset factory pages
$start_asset = '5d1dfdff7f000002310aff0e7f135fa5';

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
  $changed = false;
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "audiences") {
      if (isset($dyn->fieldValues->fieldValue) ) {
        $el = new StdClass();
        $el->value = 'Press Release';
        if ( is_array($dyn->fieldValues->fieldValue) ) {
          if ( !in_array($el, $dyn->fieldValues->fieldValue) ) {
            array_push($dyn->fieldValues->fieldValue, $el);
            $changed = true;
            echo 'added to array';
          } else {
            echo 'Already one of the tags';
          }
        } else {
          if ( $dyn->fieldValues->fieldValue->value != 'Press Release' ) {
            $dyn->fieldValues->fieldValue = array ($dyn->fieldValues->fieldValue);
            array_push($dyn->fieldValues->fieldValue, $el);
            $changed = true;
            echo 'added second tag';
          } else {
            echo 'Already the only tag';
          }
        }
      } else {
        $dyn->fieldValues->fieldValue = new StdClass();
        $dyn->fieldValues->fieldValue->value = 'Press Release';
        $changed = true;
        echo 'added as only tag';
      }
    }
  }
}

if (!$cron) {include('../header.php');}

?>