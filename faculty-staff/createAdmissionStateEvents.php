<?php
$title = 'Create Event blocks for each of the Admission on-the-road State events';

// $type_override = 'page';
$start_asset = '1c07f8bb7f0000024124dc483414e60f';



include('html_header.php');

function readFolder($client, $auth, $id) {
  global $total;
  $assetToCopy = array ('type' => 'block', 'id' => '1c0516bd7f0000024124dc482e1b1e9b' );
  $events = array ( 'Interior West' => array ( 'Arizona', 'Colorado', 'Idaho', 'Montana', 'Nevada', 'New Mexico', 'Utah', 'Wyoming' ), 'Midwest' => array ( 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Michigan', 'Minnesota', 'Missouri', 'Nebraska', 'North Dakota', 'Ohio', 'South Dakota', 'Wisconsin' ), 'Mid-Atlantic' => array ( 'Delaware', 'Maryland', 'Virginia', 'Washington D.C.' ), 'New England' => array ( 'Connecticut', 'Maine', 'Massachusetts', 'New Hampshire', 'Rhode Island', 'Vermont'), 'Northeast' => array ( 'New Jersey', 'New York', 'Pennsylvania' ), 'South' => array ( 'Alabama', 'Arkansas', 'Florida', 'Georgia', 'Kentucky', 'Louisiana', 'Mississippi', 'North Carolina', 'Oklahoma', 'South Carolina', 'Tennessee', 'Texas', 'West Virginia' ), 'West Coast' => array ( 'Alaska', 'California', 'Hawaii', 'Oregon', 'Washington' ));
  // print_r($events);


  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder->children;
    // print_r($asset);

    foreach($asset["child"] as $child) {
      if ($child->type == "folder") {
      
        foreach ($events as $region => $states) {
          $pos = strpos($child->path->path,$region);
          if ($pos != false) {
            echo '<h2>'.$region.'</h2>';
            echo '<h4>'.$child->id.'</h4>';
            $destFolder = array ('type' => 'folder', 'id' => $child->id);
          
            foreach ($states as $state) {
              echo $state.'<br>';
              $copyParams = array ('destinationContainerIdentifier' => $destFolder, "doWorkflow" => false, "newName" => $state);
              $params = array ('authentication' => $auth, 'identifier' => $assetToCopy, 'copyParameters' => $copyParams );
            
            
              if ($_POST['action'] == 'edit') {
                $reply = $client->copy ( $params );
              }

              if ($reply->copyReturn->success == 'true') {
                echo '<div class="s">Copy successful!</div>';
                $total['s']++;
              } else {
                echo '<div class="f">Copy failed: '.$child->path->path.'<div>'.extractMessage($result).'</div></div>';
                $total['f']++;
              }
            }
          }
        }
      }
    }
  } else {
    echo '<h1 class="f">Failed to read folder</h1>';
  }
}

?>
</body>
</html>