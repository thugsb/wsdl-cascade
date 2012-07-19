<?php
$title = 'For news items, copy the thumbnail, avatar and summary from the structured data to metadata';

// $type_override = 'page';
$start_asset = '52bcf8e07f000002001344a89985168b';

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
  if (preg_match('/News Types\/News/', $asset["contentTypePath"]))
    return true;
}

function changes(&$asset) {
  global $changed, $data, $total;
  $changed = false;
  // Grab the correct fields
  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
    if ($sdnode->identifier == "thumbnail") {
      $news_thumbnail = $sdnode;
    } elseif ($sdnode->identifier == "avatar") {
      $news_avatar = $sdnode;
    } elseif ($sdnode->identifier == "summary") {
      $news_summary = $sdnode;
    } elseif ($sdnode->identifier == "content") {
      if (count($asset["structuredData"]->structuredDataNodes->structuredDataNode) < 7) {
        $news_summary = $sdnode;
      }
    }
  }
  // echo $news_thumbnail->filePath;
  // echo $news_avatar->filePath;
  // echo $news_summary->text;
  
  foreach ($asset["metadata"]->dynamicFields->dynamicField as $dyn) {
    if ($dyn->name == "thumbnail") {
      if ($dyn->fieldValues->fieldValue->value != $news_thumbnail->filePath) {
        $changed = true;
        $dyn->fieldValues->fieldValue->value = $news_thumbnail->filePath;
      }
    } elseif ($dyn->name == "avatar") {
      if ($dyn->fieldValues->fieldValue->value != $news_avatar->filePath) {
        $changed = true;
        $dyn->fieldValues->fieldValue->value = $news_avatar->filePath;
      }
    }
  }
  if ($asset["metadata"]->teaser != $news_summary->text) {
    $changed = true;
    $asset["metadata"]->teaser = $news_summary->text;
  }
}


include('header.php');

?>