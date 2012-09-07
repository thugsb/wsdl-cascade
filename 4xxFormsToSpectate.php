<?php
$title = 'Changes the formProcessor of 4xx pages to "4xx Form" and turn Sharing off';

// $type_override = 'page';
$start_asset = '2f7dcabc7f00000101f92de527bf1fa7';

function pagetest($child) {
  if ($child->type == "page" && preg_match('/^__/',$child->path->path) && $child->path->path != '__library-404')
    return true;
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  return true;
}


function changes(&$asset) {
  if ($asset["structuredData"]->structuredDataNodes->structuredDataNode[9]->structuredDataNodes->structuredDataNode[4]->structuredDataNodes->structuredDataNode[0]->identifier != 'formProcessor') {
    echo '<div class="f">This should say formProcessor: '.$asset["structuredData"]->structuredDataNodes->structuredDataNode[9]->structuredDataNodes->structuredDataNode[4]->structuredDataNodes->structuredDataNode[0]->identifier."</div>"; // View your edited content
  }
  $asset["structuredData"]->structuredDataNodes->structuredDataNode[9]->structuredDataNodes->structuredDataNode[4]->structuredDataNodes->structuredDataNode[0]->text = '4xx Form';
  
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $field) {
    if ($field->name == 'enable-sharing') {
      $field->fieldValues->fieldValue->value = 'No';
    }
  }
}

if (!$cron)
  include('header.php');

?>