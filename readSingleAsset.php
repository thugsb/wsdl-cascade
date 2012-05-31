<?php
$title = 'Displays the Access Rights and full asset data';

$type_override = 'page';
$start_asset = '3f3e4a5a7f00000100224002bb0b7c87';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  return true;
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  return true;
}

function changes(&$asset) {
}



include('header.php');

// Read asset Access
$reply = $client->readAccessRights ( array ('authentication' => $auth, 'identifier' => $id ) );
if ($reply->readAccessRightsReturn->success == 'true') {
  $asset = ( array ) $reply;
  echo "<script type='text/javascript'>var access = ";
  print_r(json_encode($asset));
  echo "; console.log('access')";
  echo "</script>";
  echo '<input type="checkbox" class="hidden" id="Aexpand'.$asset['id'].'"><label class="fullpage" for="Aexpand'.$asset['id'].'">';
    print_r($asset);
  echo '</label>';
} else {
  echo '<div class="f">Access Read failed</div>';
}

?>
