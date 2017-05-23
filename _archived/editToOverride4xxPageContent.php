<?php
$title = 'Override 4xx Page Content';

// $type_override = 'page';
$start_asset = '2f7dcabc7f00000101f92de527bf1fa7';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  if (preg_match('/404/', $child->path->path) && $child->id != 'b76e54c57f00000100279c886a1e047e')
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
  global $client, $auth;
  $reply404 = $client->read ( array ('authentication' => $auth, 'identifier' => array('type' => 'page', 'id' => 'b76e54c57f00000100279c886a1e047e') ) );
  $asset404text = (array) $reply404->readReturn->asset->page->structuredData->structuredDataNodes->structuredDataNode[1]->structuredDataNodes->structuredDataNode[3];
  // echo '<input type="checkbox" class="right"><div class="fullpage">';
  //   print_r($asset404text["text"]);
  // echo '</div>';
  $data = $asset["structuredData"];
  $nodes = $data->structuredDataNodes->structuredDataNode;

  foreach($nodes as $node){
    if($node->identifier == 'main_column'){
      $subnodes = $node->structuredDataNodes->structuredDataNode;
    
      foreach($subnodes as $subnode){
        if($subnode->identifier == 'syndicate-from'){
          $subnode->pageId = "b76e54c57f00000100279c886a1e047e";
          $subnode->assetType = "page";
          $subnode->pagePath = "__404";
          // echo '<input type="checkbox" class="right"><div class="fullpage">';
          //   print_r($subnode);
          // echo '</div>';
        }
        if ($subnode->identifier == 'content') {
          // This is just seeing what pages have the same content as the __404 page
          if ($subnode->text != $asset404text["text"]) {
            echo "Content doesn't match for ".$asset["path"];
          } else {
            echo '<div class="s">Content matches: '.$asset["path"].'</div>';
          }
        }
      }
    }
  }
}


if (!$cron)
  include('../header.php');

?>