<?php

global $authID;
global $ID;
global $urlServer;

function initConnexion(){
	$GLOBALS['urlServer']="https://zabbixserver/api_jsonrpc.php";
	$username="zabbix_api";
	$password="";				//z
	authJSONRequest($GLOBALS['urlServer'],$username,$password);	
}

function JSONrequest($json){
	$curl = curl_init($GLOBALS['urlServer']);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
        	array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
	$json_response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	if ( ($status != 201 && $status != 200)) {
	    die("Error: call to URL ".$GLOBALS['urlServer']." failed with status $status, response $json_
response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
	}	
	curl_close($curl);
	return json_decode($json_response, true);

}

function authJSONRequest($urlServer,$username,$password){
	
	$connectionStr= '{
	    "jsonrpc": "2.0",
	    "method": "user.login",
	    "params": {
        	"user": "'.$username.'",
	        "password": "'.$password.'"
	    },
	    "id": 1,
	    "auth": null
	}';

	$response = JSONrequest($connectionStr);
	$GLOBALS['authID']=$response["result"];
	$GLOBALS['ID']=$response["id"];

}

?>
