<?php
include("../web_services_util.php");

// This is broken. Wrong server, as well as other problems

$client = new SoapClient ( "http://dev.cascadeserver.com/ws/services/AssetOperationService?wsdl", array ('trace' => 1 ) );  
$auth = array ('username' => '', 'password' => '' );
$id = array ('type' => 'folder', 'id' => '989d52710a00016c5e4c03d41c153ed5' );  
$params = array ('authentication' => $auth, 'identifier' => $id );




$aclReadEntry = array('level' => 'read', 'type' => 'group', 'name' => 'hh-team');
$aclEntries = array($aclReadEntry);

$accessRightsInfo = array('identifier' => $id, 'aclEntries' => $aclEntries, 'allLevel' => 'read');
    
addGroupToFolderAndDescendants($id, $auth, $client);

// Read asset
function addGroupToFolderAndDescendants($id, $auth, $client)
{
    $reply = $client->read(array('authentication' => $auth, 'identifier' => $id));
    $asset = (array) $reply->readReturn->asset->folder;
    $children = $asset['children']->child;
    
    if (!is_array($children))
        $children=array($children);
    
    foreach($children as $child) {
        addGroup($child, $auth, $client);
        if ($child->type=='folder')
            addGroupToFolderAndDescendants($child, $auth, $client);
    }
}





exit();

function addGroup($id, $auth, $client)
{
    $reply = $client->readAccessRights (array('authentication' => $auth, 'identifier' => $id));
    if ($reply->readAccessRightsReturn->success == 'true') {
        
        $aclReadEntry = array('level' => 'read', 'type' => 'group', 'name' => 'loadtester-group');
        $accessRightsInformation = $reply->readAccessRightsReturn->accessRightsInformation;
        
        $aclEntries = $accessRightsInformation->aclEntries->aclEntry;
        
        if (!is_array($aclEntries))
            $aclEntries=array($aclEntries);
        
        
        
        $aclEntries[] = $aclReadEntry;
        $accessRightsInformation->aclEntries->aclEntry=$aclEntries;
        
            // Edit page
        $params = array ('authentication' => $auth, 'accessRightsInformation' => $accessRightsInformation, 'applyToChildren' => false );
        try
        {
            $reply = $client->editAccessRights( $params );
        }
        catch(Exception $e)
        {
            echo "\r\nProblem: {$e->getMessage()}\n";
        }       
        $result = $client->__getLastResponse();
        if (!isSuccess($result))
        {
            echo "\r\nError occured:";
            echo "\r\n".extractMessage($result)."\r\n";
        }
        else    
            echo "\r\nAsset updated successfully\r\n";
        echo "Done";
    }
    else
    {
        print_r($reply);
        echo "Problem occured\n";
    }
}



?>