<?php
date_default_timezone_set('America/New_York');
$title = 'Find Related Content, Right Sidebar, SSP and Videos';

// $type_override = 'page';
$start_asset = '2891e3f87f00000101b7715d1ba2a7fb';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/[a-z]/', $child->path->path))
    return true;
}
function foldertest($child) {
  return false;
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

  foreach ($asset["structuredData"]->structuredDataNodes->structuredDataNode as $sdnode) {
    if ($sdnode->identifier == "related") {
      foreach ($sdnode->structuredDataNodes->structuredDataNode as $lvl2node) {
        if ($lvl2node->identifier == 'include' && $lvl2node->text == 'Yes') {
          echo "<div class='k'>Found some Related Content!</div>";
        }
        if ($lvl2node->identifier == 'related-page' && $lvl2node->pageID != '') {
          echo "<div class='k'>Found a Related Page!</div>";
        }
        if ($lvl2node->identifier == 'collection' && $lvl2node->blockID != '') {
          echo "<div class='k'>Found a Related Collection!</div>";
        }
        if ($lvl2node->identifier == 'related-publication' && $lvl2node->pageID != '') {
          echo "<div class='k'>Found a Related Publication!</div>";
        }
        if ($lvl2node->identifier == 'meta' && $lvl2node->text != 'None') {
          echo "<div class='k'>Found some Related Meta Content!</div>";
        }
        if ($lvl2node->identifier == 'external' && $lvl2node->text == 'Yes') {
          foreach ($lvl2node->structuredDataNodes->structuredDataNode as $lvl3node) {
            if ($lvl3node->identifier == 'url' && $lvl3node->text != '') {
              echo "<div class='k'>Found a Related External URL!</div>";
            }
          }
        }
      }
    }
    if ($sdnode->identifier == "right_sidebar") {
      foreach ($sdnode->structuredDataNodes->structuredDataNode as $lvl2node) {
        if ($lvl2node->identifier == 'include' && $lvl2node->text == 'Yes') {
          echo "<div class='k'>Found a Right Sidebar!</div>";
        }
      }
    }
    if ($sdnode->identifier == "video-container") {
      foreach ($sdnode->structuredDataNodes->structuredDataNode as $lvl2node) {
        if ($lvl2node->identifier == 'video') {
          foreach ($lvl2node->structuredDataNodes->structuredDataNode as $lvl3node) {
            if ($lvl3node->identifier == 'id' && $lvl3node->text != '') {
              echo "<div class='k'>Found a Video ID!</div>";
            }
            if ($lvl3node->identifier == 'desktop') {
              foreach ($lvl3node->structuredDataNodes->structuredDataNode as $lvl4node) {
                if ($lvl4node->identifier == 'path' && $lvl4node->text != '') {
                  echo "<div class='k'>Found a Video Path!</div>";
                }
              }
            }
          }
        }
      }
    }
    if ($sdnode->identifier == "ssp") {
      foreach ($sdnode->structuredDataNodes->structuredDataNode as $lvl2node) {
        if ($lvl2node->identifier == 'embed' && $lvl2node->text == 'Yes') {
          echo "<div class='k'>Found a SlideShow!</div>";
        }
      }
    }
  }
}

if (!$cron) {include('../header.php');}

?>