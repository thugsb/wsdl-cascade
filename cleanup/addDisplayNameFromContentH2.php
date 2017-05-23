<?php
$title = 'Add a Display Name to pages that do not have them, from the WYSIWYG content H2s';

// $type_override = 'page';
$start_asset = '3beb3e397f00000209340e79b7d2e67f';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
  if (preg_match('/^[a-z]/', $child->path->path))
    return true;
}
function edittest($asset) {
  if (preg_match('/[a-z]/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  global $changed;
  $changed = false;
  
  if (!is_array($asset["structuredData"]->structuredDataNodes->structuredDataNode))
  $asset["structuredData"]->structuredDataNodes->structuredDataNode = array ($asset["structuredData"]->structuredDataNodes->structuredDataNode);
  
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $field) {
    if ($field->identifier == 'content') {
      if ($asset["metadata"]->displayName == '') {
        $changed = true;
        $matches = array();
        preg_match('/<h2\b[^>]*>(.*?)<\/h2>/', $field->text, $matches);
        $h2 = strip_tags($matches[0]);
        echo '<div>'.$h2.'</div>';
        $asset["metadata"]->displayName = $h2;
      } else {
        $changed = false;
        echo '<div>Display Name: '.$asset["metadata"]->displayName.'</div>';
      }
    }
  }
}


if (!$cron)
  include('../header.php');

?>