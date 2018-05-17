<?php
date_default_timezone_set('America/New_York');
$title = 'Test the existence of PDFs in Cascade that are related to *.pdf files found on the server';

$lines = file('../indexes/stu-pdf-list.txt', FILE_IGNORE_NEW_LINES);


// $type_override = 'page';
$start_asset = 'None';

// Optionally override the container/child types
// $asset_type = 'assetFactoryContainer';
// $asset_children_type = 'assetFactory';

function pagetest($child) {
  return false;
}
function foldertest($child) {
  return false;
}
function edittest($asset) {
  return false;
}

function changes(&$asset) {
  global $changed;
  $changed = false;
}

if (!$cron) {include('../header.php');}

$publishableFolders = [];
$unpublishableFolders = [];

$read = 0;
$publishable = 0;
$ok = 0;
$unpub = 0;
$absent = 0;
$ignored = 0;
$inUnpubFolder = 0;
// file_put_contents('../indexes/stu-pdf-unpublishable.txt', "");
// file_put_contents('../indexes/stu-pdf-unpublishable-folder.txt', "");
// file_put_contents('../indexes/stu-pdf-missing.txt', "");

foreach ($lines as $path) {
  $cascadePath = substr($path, 2);

  // if ( preg_match('/^(faculty-bak|media|communications)\//', $cascadePath) ) {
  if ( !preg_match('/^communications/', $cascadePath) ) {
    $ignored++;
    continue;
  }

  if (preg_match('/^news-events\//', $cascadePath) ) {
    $siteName = CASCADE_SITE_PREFIX.'news-events';
    $cascadePath = substr($cascadePath, 12);
    echo '<div class="k">Reading '.$cascadePath.' in '.$siteName.'</div>';
  } elseif (preg_match('/^faculty\//', $cascadePath) ) {
    $siteName = CASCADE_SITE_PREFIX.'faculty';
    $cascadePath = substr($cascadePath, 8);
    echo '<div class="k">Reading '.$cascadePath.' in '.$siteName.'</div>';
  } else {
    $siteName = MAIN_SITE_NAME;
    echo '<div class="k">Reading '.$cascadePath.'</div>';
  }

  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => array(type => 'file', path => array( path => $cascadePath, siteName => $siteName) ) ) );
  // echo '<pre>';
  // print_r($reply);
  // echo '</pre>';

  
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->file;
    $read++;
    if ( $asset['shouldBePublished'] == 1 ) {
      $publishable++;
      $parentFolders = split( '/', $cascadePath );
      $filename = array_pop($parentFolders);
      $allAncestorFoldersArePublishable = true;
      foreach($parentFolders as $folder) {
        if (preg_match('/^'.$folder.'\//', $cascadePath)) {
          $folderPath = $folder;
        } else {
          $parents = explode('/'.$folder.'/', $cascadePath);
          $folderPath = $parents[0].'/'.$folder;
        }
        // echo '<div>'.$folderPath.'</div>';

        if ( in_array($folderPath, $publishableFolders) ) {
          // Do nothing. All is fine, so continue thru the foreach.
          // echo '<div class="s">publishable</div>';
        } elseif ( in_array($folderPath, $unpublishableFolders) ) {
          // File is in an unpublishable folder
          file_put_contents('../indexes/stu-pdf-unpublishable-folder.txt', $folderPath.' in '.$cascadePath."\n", FILE_APPEND);
          echo '<div class="f">The '.$folderPath.' folder is unpublishable.</div>';
          $inUnpubFolder++;
          $allAncestorFoldersArePublishable = false;
        } else {
          echo '<div class="k">Reading '.$folderPath.'</div>';
          $reply = $client->read ( array ('authentication' => $auth, 'identifier' => array(type => 'folder', path => array( path => $folderPath, siteName => $siteName) ) ) );
          if ($reply->readReturn->success == 'true') {
            if ( $reply->readReturn->asset->folder->shouldBePublished ) {
              array_push($publishableFolders, $folderPath);
            } else {
              array_push($unpublishableFolders, $folderPath);
              // File is in an unpublishable folder
              file_put_contents('../indexes/stu-pdf-unpublishable-folder.txt', $folderPath.' in '.$cascadePath."\n", FILE_APPEND);
              echo '<div class="f">The '.$folderPath.' folder is unpublishable.</div>';
              $inUnpubFolder++;
              $allAncestorFoldersArePublishable = false;
            }
          } else {
            echo '<div class="f">Folder cannot be read: '.$folderPath.' in '.$cascadePath.'</div>';
            print_r($reply);
          }
        }
      }
      if ( $allAncestorFoldersArePublishable ) {
        $ok++;
        echo '<div class="s">All is OK</div> '.$cascadePath.'<br>';
      }

    } else {
      // File exists in Cascade but isn't publishable
      file_put_contents('../indexes/stu-pdf-unpublishable.txt', $cascadePath."\n", FILE_APPEND);
      echo '<div class="f">The '.$cascadePath.' PDF is unpublishable, yet is published.</div>';
      $unpub++;
      echo '<div>Unpublishable: '.$unpub .'</div>';
    }
    

  } else {
    // File doesn't exist in Cascade
    file_put_contents('../indexes/stu-pdf-missing.txt', $cascadePath."\n", FILE_APPEND);
    echo '<div class="f">The '.$cascadePath.' PDF in site '.$siteName.' is absent from Cascade.</div>';
    $absent++;
    echo '<div>Absent: '.$absent .'</div>';
  }
  // echo '<pre>';
  // print_r($publishableFolders);
  // echo '</pre>';
  // if ( count($unpublishableFolders) > 0) {
  //   echo '<pre>';
  //   print_r($unpublishableFolders);
  //   echo '</pre>';
  // }
}

echo '<h2>Stats</h2>';
echo '<div>Lines: '.count($lines) .'</div>';
echo '<div>Read: '.$read .'</div>';
echo '<div>Publishable: '.$publishable .'</div>';
echo '<div>OK: '.$ok .'</div>';
echo '<div>Unpublishable: '.$unpub .'</div>';
echo '<div>Absent: '.$absent .'</div>';
echo '<div>Ignored: '.$ignored .'</div>';
echo '<div>Unpublishable folder: '.$inUnpubFolder.'</div>';

sort($publishableFolders);
echo '<h2>Publishable Folders:</h2><pre>';
print_r($publishableFolders);
echo '</pre>';

sort($unpublishableFolders);
echo '<h2>Unpublishable Folders:</h2><pre>';
print_r($unpublishableFolders);
echo '</pre>';

?>
