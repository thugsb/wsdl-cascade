<?php


function removeCheckboxItem($val, &$array) {
  global $changed, $cron;
  $el = new StdClass();
  $el->value = $val;
  if ( in_array($el, $array) ) {
    $index = array_search($el, $array);
    array_splice($array, $index, $index+1);
    $changed = true;
    if (!$cron) {echo "<div class='k'>$val removed.</div>";}
  }
}
function addCheckboxItem($val, &$array) {
  global $changed, $cron;
  $el = new StdClass();
  $el->value = $val;
  if ( !in_array($el, $array) ) {
    array_push($array, $el);
    $changed = true;
    if (!$cron) {echo "<div class='k'>$val added.</div>";}
  }
  $array = array_values(array_filter($array));
}

function editNode($value, $layers, $field, $array) {
  global $changed;
  foreach ($array->structuredDataNodes->structuredDataNode as $node) {
    if ($node->identifier == $layers[0] ) {
      array_shift($layers);
      $count = count($layers);
      if ( $count == 0 ) {
        if ($node->$field != $value) {
          $node->$field = $value;
          $changed = true;
          $edited  = 'true';
          echo '<div class="k">Editing <code>' . $node->identifier . '</code></div>';
          return true;
        }
      } else {
        $returnValue = editNode($value, $layers, $field, $node);
      }
    }
  }
  return $returnValue;
}
function getNode($layers, $field, $array) {
  foreach ($array->structuredDataNodes->structuredDataNode as $node) {
    if ($node->identifier == $layers[0] ) {
      array_shift($layers);
      $count = count($layers);
      if ( $count == 0 ) {
        return $node->$field;
      } else {
        $returnValue = getNode($layers, $field, $node);
      }
    }
  }
  return $returnValue;
}

include_once(__DIR__.'/rollbar-init.php');

include(__DIR__."/web_services_util.php");
$asset_types = array("folder", "page", "assetfactory", "assetfactorycontainer", "block", "block_FEED", "block_INDEX", "block_TEXT", "block_XHTML_DATADEFINITION", "block_XML", "connectorcontainer", "twitterconnector", "facebookconnector", "wordpressconnector", "googleanalyticsconnector", "contenttype", "contenttypecontainer", "destination", "file", "group", "message", "metadataset", "metadatasetcontainer", "pageconfigurationset", "pageconfiguration", "pageregion", "pageconfigurationsetcontainer", "publishset", "publishsetcontainer", "reference", "role", "datadefinition", "datadefinitioncontainer", "format", "format_XSLT", "format_SCRIPT", "site", "sitedestinationcontainer", "symlink", "target", "template", "transport", "transport_fs", "transport_ftp", "transport_db", "transportcontainer", "user", "workflow", "workflowdefinition", "workflowdefinitioncontainer");
$total = array('s' => 0, 'f' => 0, 'k' => 0);

$client = '';
if ($_POST['client']) {$client = $_POST['client'];} else if ($_GET['client']) {$client = $_GET['client'];}

// If it's not a folder, you need to set the correct $asset_type (camelCase)
if (!isset($asset_type)) {
  $asset_type = 'folder';
  $asset_children_type = 'page';
}
if (!isset($data)) {$data = '';}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta content="text/html; charset=UTF-8" http-equiv="Content-Type"/>
  <title>WSDL - <?php echo $title; ?></title>
  <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap-combined.no-icons.min.css" rel="stylesheet">
  <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
  <?php 
  if ( file_exists(dirname($_SERVER["SCRIPT_FILENAME"]) . '/html_header.php') ) {
    echo '<link href="styling.css" rel="stylesheet">';
  } else {
    echo '<link href="../styling.css" rel="stylesheet">';
  }
  ?>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/js/bootstrap.min.js"></script>
  <script>
  $(function() {
    $('input[name=action]').change(function() {
      if ($('#edit').is(':checked')) {
        $('.submit').addClass('btn-warning')
      } else {
        $('.submit').removeClass('btn-warning')
      }
    });
  });
  </script>
  
  <?php if (isset($script)) {echo '<script>'.$script.'</script>';} ?>
  <?php if (isset($htmlHead)) {echo $htmlHead;} ?>
</head>
<body>
  <nav>
    <h5 style="margin:0"><?php echo $title; ?></h5>
    <form id="options" method="POST" class="form-inline">
      <a href="./">./</a>
      <input name="login" type="text" placeholder="username" size="8" value="<?php echo $_POST['login']; ?>">
      <input name="password" type="password" size="8" placeholder="password" value="<?php echo $_POST['password']; ?>">
      <fieldset>
        <span class="radio-label">Cascade Instance:</span>
        <?php if (!$_POST['client'] || strpos($_POST['client'], '8443') ) { ?>
          <label for="live_client" class="radio inline"><input name="client" type="radio" id="live_client" checked="checked" value="https://cms.slc.edu:8443/ws/services/AssetOperationService?wsdl">Live</label>
          <label for="dev_client" class="radio inline"><input name="client" type="radio" id="dev_client" value="https://cms.slc.edu:7443/ws/services/AssetOperationService?wsdl">Dev</label>
        <?php } else { ?>
          <label for="live_client" class="radio inline"><input name="client" type="radio" id="live_client" value="https://cms.slc.edu:8443/ws/services/AssetOperationService?wsdl">Live</label>
          <label for="dev_client" class="radio inline"><input name="client" type="radio" id="dev_client" checked="checked" value="https://cms.slc.edu:7443/ws/services/AssetOperationService?wsdl">Dev</label>
        <?php } ?>
      </fieldset>
      <select name="type">
        <?php foreach($asset_types as $type) {
          if ($_POST['type'] == $type) {
            echo '<option value="'.$type.'" selected="selected">'.$type.'</option>';
          } elseif ($type_override == $type) {
            echo '<option value="'.$type.'" selected="selected">'.$type.'</option>';
          } else {
            echo '<option value="'.$type.'">'.$type.'</option>';
          }
        } ?>
      </select>
      <input name="id" id="id" type="text" placeholder="Asset ID" value="<?php if ($_POST['id'] == null) {echo $start_asset;} else {echo $_POST['id'];} ?>">
      <div class="advanced">
        <label for="children" class="checkbox inline">
          <input type="checkbox" name="children" id="children" accesskey="c" <?php if ($_POST['children'] == 'on') {echo "checked";} ?>> Show <u>C</u>hildren
        </label>
        <label for="folder" class="checkbox inline">
          <input type="checkbox" name="folder" id="folder" accesskey="f" <?php if ($_POST['folder'] == 'on') {echo "checked";} ?>> Show <u>F</u>older Names
        </label>
        <label for="asset" class="checkbox inline">
          <input type="checkbox" name="asset" id="asset" accesskey="n" <?php if ($_POST['asset'] == 'on' || !array_key_exists('submit',$_POST)) {echo "checked";} ?>> Show Asset <u>N</u>ames
        </label>
        <label for="before" class="checkbox inline">
          <input type="checkbox" name="before" id="before" accesskey="b" <?php if ($_POST['before'] == 'on') {echo "checked";} ?>> Show <u>B</u>efore
        </label>
        <label for="after" class="checkbox inline">
          <input type="checkbox" name="after" id="after" accesskey="a" <?php if ($_POST['after'] == 'on') {echo "checked";} ?>> Show <u>A</u>fter
        </label>
        <label for="debug" class="checkbox inline">
          <input type="checkbox" name="debug" id="debug" accesskey="d" <?php if ($_POST['debug'] == 'on') {echo "checked";} ?>> <u>D</u>ebug
        </label>
        <label for="read" class="radio inline">
          <input name="action" id="read" type="radio" value="read" accesskey="r" checked="checked"> <u>R</u>ead only
        </label>
        <label for="edit" class="radio inline">
          <input name="action" id="edit" type="radio" value="edit" accesskey="e"> <u>E</u>dit
        </label>
        <button name="submit" accesskey="s" class="btn submit"><u>S</u>ubmit</button>
      </div>
    </form>
  </nav>
  <?php 
  if (isset($message)) {echo '<div class="k">'.$message.'</div>';}
  if (array_key_exists('submit',$_POST)) { //If form was submitted 
    $client = new SoapClient ( $_POST['client'], array ('trace' => 1 ) );	
    $auth = array ('username' => $_POST['login'], 'password' => $_POST['password'] );
    $ids = explode(',',$_POST['id']);
    ?>
    <label for="expandAll" class="checkbox inline">E<u>x</u>pand All</label>
    <input type="checkbox" id="expandAll" accesskey="x">
    <section class="output">
      <?php if ($_POST['type'] == 'folder' || preg_match('/container/', $_POST['type']) ) {
        foreach($ids as $id) {
          $asset = array ('type' => $_POST['type'], 'id' => $id );
          readFolder($client, $auth, $asset);
        }
        echo '<div class="totals">Successes: '.$total['s'].' Failures: '.$total['f'].' Skipped: '.$total['k'].'</div>';
      } else {
        foreach($ids as $id) {
          $asset = array ('type' => $_POST['type'], 'id' => $id );
          readPage($client, $auth, $asset, $_POST['type']);
        }
      } ?>
    </section>
  <?php } ?>
</body>
</html>