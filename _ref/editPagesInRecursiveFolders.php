<!DOCTYPE html>
<html>
<head>
  <title>Editing all pages in a folder and its subfolders</title>
  <style type="text/css">
  body {white-space:pre;}
  .s {color:#090;}
  .f {color:#c00;}
  </style>
</head>
<body>
<?php
include("../web_services_util.php");
require_once('../_config.php');

$client = new SoapClient ( CMS_PATH, array ('trace' => 1 ) );	
$auth = array ('username' => '', 'password' => '' );
$id = array ('type' => 'folder', 'id' => '3f3b23827f00000100224002c11c3350' );

readFolder($client, $auth, $id);

function readFolder($client, $auth, $id) {
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
    echo "<h1>Folder: ".$asset["name"]."</h1>";
    print_r($asset["children"]); // Shows all the children of the folder
    indexFolder($client, $auth, $asset);
  } else {
    echo '<div class="f">Failed to read folder: '.$asset["name"].'</div>';
  }
}

function indexFolder($client, $auth, $asset) {
  foreach($asset["children"]->child as $child) {
    if ($child->type == "page") {
      readPage($client, $auth, array ('type' => 'page', 'id' => $child->id));
    } elseif ($child->type == "folder") {
      readFolder($client, $auth, array ('type' => 'folder', 'id' => $child->id));
    }
  }
}

// Read asset
function readPage($client, $auth, $id) {
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->page;
    echo '<h3>'.$asset['name']."</h3>";
    
    // If you want to only edit some pages, choose them here:
    // echo $asset["contentTypePath"]."<br>";
    // if ($asset["contentTypePath"] == CASCADE_SITE_PREFIX."about/Level 2 Page") {
      
      editPage($client, $auth, $asset);
      
    // }
    
  } else {
    echo '<div class="f">Failed to read page: '.$id.'</div>';
  }
}

function editPage($client, $auth, $id) {
  // print_r($id); // Shows the page in all its glory
  
  $id["metadata"]->teaser = "New Teaser Content"; // Set what you want to change
  echo 'Teaser: '.$id["metadata"]->teaser."<br>"; // View your edited content
  
  // print_r($id); // Shows the page as it will be
  
  // Once you are happy that your edits are correct, uncomment this line:
  // $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array('page' => $id) ) );
  
  if ($edit->editReturn->success == 'true') {
    echo '<div class="s">Edit success</div>';
  } else {
    echo '<div class="f">Edit failed</div>';
  }
}

?>
</body>
</html>