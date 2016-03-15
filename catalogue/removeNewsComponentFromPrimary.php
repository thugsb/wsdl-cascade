<?php
date_default_timezone_set('America/New_York');
$title = 'Test';

// $type_override = 'page';
$start_asset = 'c99c8e7fc0a8022b20b4e6099ff0c3e1';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/^undergraduate\/[a-z]/', $child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/tpl-discipline/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  /* If you wish to use $changed, make sure it's global, and set it to false. 
   * When something is changed, it becomes true: */
  global $changed;
  $changed = false;
  $primaryCount = 0;
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $field) {
    if ($field->identifier == 'group-primary') {
      $primaryCount++;
    }
  }
  echo $primaryCount;
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $field) {
    if ($field->identifier == 'group-primary') {
      foreach ($field->structuredDataNodes->structuredDataNode as $group) {
        if ($group->identifier == 'type' && $group->text == 'News') {
          $group->text = '';
          $changed = true;

          foreach ($field->structuredDataNodes->structuredDataNode as $node) {
            if ($node->identifier == 'status' && $node->text == 'On') {
              $node->text = 'Off';
              break;
            }
          }
          break;
        }
      }
      break;
    }
  }
  if ($changed == true && $primaryCount == 1) {
    foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $field) {
      if ($field->identifier == 'group-settings') {
        foreach ($field->structuredDataNodes->structuredDataNode as $group) {
          if ($group->identifier == 'primary') {
            $group->text = '::CONTENT-XML-CHECKBOX::';
          }
        }
      }
    }
  }
}

if (!$cron) {include('../header.php');}

?>