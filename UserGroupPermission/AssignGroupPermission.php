<?php
include_once('utils.php');
initConnexion();

$jsonStr= '{
            "jsonrpc": "2.0",
            "method": "hostgroup.get",
            "params": {
            },
            "id":'.$GLOBALS['ID'].',
            "auth": "'.$GLOBALS['authID'].'"
        }';

$groupArray=JSONrequest($jsonStr);
#echo var_dump($groupArray);


foreach($groupArray["result"] as $group){

	$jsonUpdate= '{
            "jsonrpc": "2.0",
            "method": "usergroup.massadd",
            "params": {
		"usrgrpids": [ "xxxxxxxxxxxxxxxxxx" ],
		"rights": {
	            "permission": 3,
	            "id": "'.$group["groupid"].'"
		}
            },
            "id":'.$GLOBALS['ID'].',
            "auth": "'.$GLOBALS['authID'].'"
        }';
	
	JSONrequest($jsonUpdate);
}
?>
