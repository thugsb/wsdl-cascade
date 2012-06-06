<?php


include("web_services_util.php");
$asset_types = array("folder", "page", "assetfactory", "assetfactorycontainer", "block", "block_FEED", "block_INDEX", "block_TEXT", "block_XHTML_DATADEFINITION", "block_XML", "connectorcontainer", "twitterconnector", "facebookconnector", "wordpressconnector", "googleanalyticsconnector", "contenttype", "contenttypecontainer", "destination", "file", "group", "message", "metadataset", "metadatasetcontainer", "pageconfigurationset", "pageconfiguration", "pageregion", "pageconfigurationsetcontainer", "publishset", "publishsetcontainer", "reference", "role", "datadefinition", "datadefinitioncontainer", "format", "format_XSLT", "format_SCRIPT", "site", "sitedestinationcontainer", "symlink", "target", "template", "transport", "transport_fs", "transport_ftp", "transport_db", "transportcontainer", "user", "workflow", "workflowdefinition", "workflowdefinitioncontainer");
if (array_key_exists('submit',$_POST)) {
  $client = new SoapClient ( $_POST['client'], array ('trace' => 1 ) );	
  $auth = array ('username' => $_POST['login'], 'password' => $_POST['password'] );
  $id = array ('type' => $_POST['type'], 'id' => $_POST['id'] );
}
$total = array('s' => 0, 'f' => 0, 'k' => 0);

// If it's not a folder, you need to set the correct $asset_type (camelCase)
if (!isset($asset_type)) {
  $asset_type = 'folder';
  $asset_children_type = 'page';
}
if (!isset($data)) {$data = '';}

?>
<!DOCTYPE html>
<html>
<head>
  <title>WSDL - <?php echo $title; ?></title>
  <style type="text/css">
  .right {float:right;}
  .hidden {display:none;}
  .s {color:#090;}
  .k {color:#009;}
  .f {padding:1em;font-size:1em;color:#fff;background:#c00;}
  
  .advanced {font-size:0.8em;}
  .totals {position:fixed;top:0;right:0;padding:0.5em;box-shadow:0 0 5px #000;background:#fff;}
  
  .output {margin-top:1em;border-top:1px solid #ccc;}
  .page {border-bottom:2px solid #ccc;margin-bottom:0.2em;}
  h4 {margin:0;}
  
  .fullpage {display:block;white-space:pre;height:4.5em;padding:0.2em;overflow:hidden;box-shadow:inset 0 0 5px #000;}
  input:checked + .fullpage {height:auto;}
  #expandAll:checked + section .fullpage {height:auto;}
  
  .odd {background:#eee;}
  .asset {padding:1em;}
  </style>
</head>
<body>
  <nav>
    <form id="options" method="POST">
      <input name="login" placeholder="username" size="8" value="<?php echo $_POST['login']; ?>">
      <input name="password" type="password" size="8" placeholder="password" value="<?php echo $_POST['password']; ?>">
      <input name="client" placeholder="client" size="8" value="<?php echo $_POST['client']; ?>">
      <select name="type">
        <?php foreach($asset_types as $type) {
          if ($type_override == $type || ($_POST['type'] == $type && $type_override == null)) {
            echo '<option value="'.$type.'" selected="selected">'.$type.'</option>';
          } else {
            echo '<option value="'.$type.'">'.$type.'</option>';
          }
        } ?>
      </select>
      <input name="id" placeholder="Asset ID" value="<?php if ($_POST['id'] == null) {echo $start_asset;} else {echo $_POST['id'];} ?>">
      <label for="read">
        <input name="action" id="read" type="radio" value="read" accesskey="r" checked="checked"> <u>R</u>ead only
      </label>
      <label for="edit">
        <input name="action" id="edit" type="radio" value="edit" accesskey="e"> <u>E</u>dit
      </label>
      <button name="submit" accesskey="s"><u>S</u>ubmit</button>
      <div class="advanced">
        <label for="children">
          <input type="checkbox" name="children" id="children" accesskey="c" <?php if ($_POST['children'] == 'on') {echo "checked";} ?>> Show <u>C</u>hildren
        </label>
        <label for="folder">
          <input type="checkbox" name="folder" id="folder" accesskey="f" <?php if ($_POST['folder'] == 'on') {echo "checked";} ?>> Show <u>F</u>older Names
        </label>
        <label for="asset">
          <input type="checkbox" name="asset" id="asset" accesskey="n" <?php if ($_POST['asset'] == 'on' || !array_key_exists('submit',$_POST)) {echo "checked";} ?>> Show Asset <u>N</u>ames
        </label>
        <label for="before">
          <input type="checkbox" name="before" id="before" accesskey="b" <?php if ($_POST['before'] == 'on') {echo "checked";} ?>> Show <u>B</u>efore
        </label>
        <label for="after">
          <input type="checkbox" name="after" id="after" accesskey="a" <?php if ($_POST['after'] == 'on') {echo "checked";} ?>> Show <u>A</u>fter
        </label>
      </div>
    </form>
  </nav>
  <?php if (array_key_exists('submit',$_POST)) { //If form was submitted 
    if ($_POST['before'] == 'on' || $_POST['after'] == 'on')
      echo 'E<u>x</u>pand All <input type="checkbox" id="expandAll" accesskey="x">';
    ?>
    <section class="output">
      <?php if ($_POST['type'] == 'folder' || $_POST['type'] == 'assetfactorycontainer') {
        readFolder($client, $auth, $id);
        echo '<div class="totals">Successes: '.$total['s'].' Failures: '.$total['f'].' Skipped: '.$total['k'].'</div>';
      } elseif ($_POST['type'] == 'page') {
        readPage($client, $auth, $id);
      } ?>
    </section>
  <?php } ?>
</body>
</html>