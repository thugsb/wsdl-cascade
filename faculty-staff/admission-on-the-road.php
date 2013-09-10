<?php
$title = 'Putting the Data into the Admissions events';

// $type_override = 'page';
$start_asset = '1c07f8bb7f0000024124dc483414e60f';

$asset_children_type = 'xhtmlDataDefinitionBlock';

class CSVData{
  public $file;
  public $data;
  public $fp;
  public $caption=true;
  public function CSVData($file=''){
    if ($file!='') getData($file);
  }
  function getData($file){
    if (strpos($file, 'tp://')!==false){
      copy ($file, '/tmp/csvdata.csv');
      if ($this->fp=fopen('/tmp/csvdata.csv', 'r')!==FALSE){
        $this->readCSV();
        unlink('tmp/csvdata.csv');
      }
    } else {
      $this->fp=fopen($file, 'r');
      $this->readCSV();
    }
    fclose($this->fp);
  }
  private function readCSV(){
    if ($this->caption==true){
      if (($captions=fgetcsv($this->fp, 1000, ","))==false) return false;
    }
    $row=0;
    while (($data = fgetcsv($this->fp, 1000, ",")) !== FALSE) {
      for ($c=0; $c < count($data); $c++) {
        $this->data[$row][$c]=$data[$c];
        if ($this->caption==true){
          $this->data[$row][$captions[$c]]=$data[$c];
        }
      }
      $row++;
    }
  }
}

$o=new CSVData();
$o->getData('events2.csv');
$data=$o->data;
// echo $data[0]["City"]."<br>";


date_default_timezone_set('America/Toronto');
for($i = 0; $i < count($data); $i++) {
  $data[$i]["Start"] = strtotime($data[$i]["Start"]).'000';
  $data[$i]["End"] = strtotime($data[$i]["End"]).'000';
}


function readFolder($client, $auth, $id) {
  global $data;
  $folder = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($folder->readReturn->success == 'true') {
    $asset = ( array ) $folder->readReturn->asset->folder;
    $children = (array) $asset["children"];
    if ($_POST['folder'] == 'on') {
      echo "<h1>Folder: ".$asset["path"]."</h1>";
    }
    if ($_POST['children'] == 'on') {
      echo '<button class="btn" href="#cModal'.$asset['id'].'" data-toggle="modal">View Children</button><div id="cModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
        print_r($asset["children"]); // Shows all the children of the folder
      echo '</div></div>';
    }
    indexFolder($client, $auth, $children);
  } else {
    echo '<div class="f">Failed to read folder: '.$asset["name"].'</div>';
  }
}

function indexFolder($client, $auth, $asset) {
  global $data;
  foreach($asset["child"] as $child) {
    // print_r($child);
    if ($child->type == "block_XHTML_DATADEFINITION") {
      readPage($client, $auth, array ('type' => 'block_XHTML_DATADEFINITION', 'id' => $child->id));
    } elseif ($child->type == "folder" && $child->name != "West Coast") {
      readFolder($client, $auth, array ('type' => 'folder', 'id' => $child->id));
    }
  }
}

// Read asset
function readPage($client, $auth, $id) {
  global $data;
  $reply = $client->read ( array ('authentication' => $auth, 'identifier' => $id ) );
  if ($reply->readReturn->success == 'true') {
    $asset = ( array ) $reply->readReturn->asset->xhtmlDataDefinitionBlock;
    if ($_POST['asset'] == 'on') {
      echo '<h4>'.$asset['path']."</h4>";
    }
    
    editPage($client, $auth, $asset);
    
  } else {
    echo '<div class="f">Failed to read page: '.$id.'</div>';
  }
}


function editPage($client, $auth, $asset) {
  global $total, $asset_type, $asset_children_type, $data;
  echo '<div class="page">';
  if ($_POST['before'] == 'on') {
    echo '<button class="btn" href="#bModal'.$asset['id'].'" data-toggle="modal">View Before</button><div id="bModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page in all its glory
    echo '</div></div>';
  }
  
  echo "<script type='text/javascript'>var page_".$asset['id']." = ";
  print_r(json_encode($asset));
  echo '; console.log(page_'.$asset['id'].')';
  echo "</script>";
  
  foreach ($data as $event) {
    if ($event["State"] == $asset["name"]) {
      $match = $event["State"];
      
      if (gettype($asset["structuredData"]->structuredDataNodes->structuredDataNode) == "array") {
        $already = count($asset["structuredData"]->structuredDataNodes->structuredDataNode);
        addEvent($asset, $already, $event);
      } else {
        // Now test if there's already some content (i.e. test if there's a counselor)
        if ($asset["structuredData"]->structuredDataNodes->structuredDataNode->structuredDataNodes->structuredDataNode[0]->text == '') {
          $asset["structuredData"]->structuredDataNodes->structuredDataNode=array($asset["structuredData"]->structuredDataNodes->structuredDataNode);
          addEvent($asset, 0, $event);
        } else {
          $asset["structuredData"]->structuredDataNodes->structuredDataNode=array($asset["structuredData"]->structuredDataNodes->structuredDataNode);
          addEvent($asset, 1, $event);
        }
      }
      
    }
  }
  

  
  if ($_POST['after'] == 'on') {
    echo '<button class="btn" href="#aModal'.$asset['id'].'" data-toggle="modal">View After</button><div id="aModal'.$asset['id'].'" class="modal hide" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-body">';
      print_r($asset); // Shows the page in all its glory
    echo '</div></div>';
  }
  
  if ($_POST['action'] == 'edit') {
    $edit = $client->edit ( array ('authentication' => $auth, 'asset' => array($asset_children_type => $asset) ) );
  }
  
  if ($edit->editReturn->success == 'true') {
    echo '<div class="s">Edit success</div>';
    $total['s']++;
  } else {
    echo '<div class="f">Edit failed: '.$asset['path'].'<div>'.extractMessage($result).'</div></div>';
    $total['f']++;
  }
  echo '</div>';
}



function addEvent($id, $pos, $event) {
  $id["structuredData"]->structuredDataNodes->structuredDataNode[$pos]->type = "group";
  $ev = $id["structuredData"]->structuredDataNodes->structuredDataNode[$pos];
  $ev->identifier = "event";
  $ev->structuredDataNodes->structuredDataNode[0]->type = "text";
  $ev->structuredDataNodes->structuredDataNode[0]->identifier = "counselor";
  $ev->structuredDataNodes->structuredDataNode[0]->text = $event["Rep"];
  $ev->structuredDataNodes->structuredDataNode[1]->type = "text";
  $ev->structuredDataNodes->structuredDataNode[1]->identifier = "venue-event";
  $ev->structuredDataNodes->structuredDataNode[1]->text = $event["Event"];
  $ev->structuredDataNodes->structuredDataNode[2]->type = "text";
  $ev->structuredDataNodes->structuredDataNode[2]->identifier = "city";
  $ev->structuredDataNodes->structuredDataNode[2]->text = $event["City"];
  $ev->structuredDataNodes->structuredDataNode[3]->type = "group";
  $ev->structuredDataNodes->structuredDataNode[3]->identifier = "date";
  $ev->structuredDataNodes->structuredDataNode[3]->structuredDataNodes->structuredDataNode[0]->type = "text";
  $ev->structuredDataNodes->structuredDataNode[3]->structuredDataNodes->structuredDataNode[0]->identifier = "start";
  $ev->structuredDataNodes->structuredDataNode[3]->structuredDataNodes->structuredDataNode[0]->text = $event["Start"];
  $ev->structuredDataNodes->structuredDataNode[3]->structuredDataNodes->structuredDataNode[1]->type = "text";
  $ev->structuredDataNodes->structuredDataNode[3]->structuredDataNodes->structuredDataNode[1]->identifier = "end";
  $ev->structuredDataNodes->structuredDataNode[3]->structuredDataNodes->structuredDataNode[1]->text = $event["End"];
}

include('../html_header.php');
?>