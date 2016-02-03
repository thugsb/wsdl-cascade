<?php
date_default_timezone_set('America/New_York');
$title = 'Migrate related discipline checkboxes into multi-dropdowns';

// $type_override = 'page';
$start_asset = '817373157f00000101f92de5bea1554a';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

$year = '2015-2016';

function pagetest($child) {
  global $year;
  if (preg_match('/^[a-z][-a-z\/]+\/'.$year.'\/[a-zA-Z0-9]/',$child->path->path))
    return true;
}
function foldertest($child) {
  global $year;
  if (preg_match('/^[a-z][-a-z\/]+$/',$child->path->path) || preg_match('/^[a-z][-a-z\/]+\/'.$year.'$/',$child->path->path) )
    return true;
}
function edittest($asset) {
  if (preg_match('/^slc-catalogue-undergraduate\/Disicipline Course Pages/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  $disciplines = [];
  $new = [];
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $field) {
    if (preg_match('/CONTENT-XML-CHECKBOX/', $field->text) ) {
      $related = array_filter(explode('::CONTENT-XML-CHECKBOX::', $field->text) );
      foreach ($related as $disc) {
        $disciplines[] = $disc;
      }
    } elseif ($field->identifier == 'related') {
      $new[] = $field->text;
    }
  }
  // print_r($disciplines);
  // print_r($new);
  foreach ($disciplines as $disc) {
    if (!in_array($disc, $new)) {
      $newnode = new StdClass();
      $newnode->type = 'text';
      $newnode->identifier = 'related';
      $newnode->structuredDataNodes = false;
      $newnode->text = $disc;
      $asset["structuredData"]->structuredDataNodes->structuredDataNode[] = $newnode;
      echo "<div class='k'>An entry for $disc will be created.</div>";
      $changed = true;
    }
  }
}

if (!$cron) {include('../header.php');}

?>