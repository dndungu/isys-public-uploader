<?php
<?php

header("Content-Type: text/plain");

require_once('../../../wp-config.php');

function dbConnect(){
	global $dbConnection, $config;
	if(is_object($dbConnection)) return;
	$dbConnection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or die(mysqli_connect_error());
}
function dbQuery($query = NULL){
	dbConnect();
	global $dbConnection;
	$result = mysqli_query($dbConnection, $query);
	return $result;
}

function dbMultiQuery($query = NULL){
	dbConnect();
	global $dbConnection;
	$response = mysqli_multi_query($dbConnection, $query);
	do {
		$result = mysqli_store_result($dbConnection);
		if(!$result) continue;
		$rows = mysqli_affected_rows($dbConnection);
		mysqli_free_result($result);
	} while (mysqli_next_result($dbConnection));
	return $response;
}

function dbFetch($results){
	if(!($results instanceof \mysqli_result)) return NULL;
	while($row = $results->fetch_assoc()){
		$rows[] = $row;
	}
	return isset($rows) ? $rows : NULL;
}

function dbAffectedRows(){
	dbConnect();
	global $dbConnection;
	return mysqli_affected_rows($dbConnection);
}

function randomString($size = 8){
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz023456789";
	$i = 0;
	$s = array();
	while ($i++ <= $size) {
		$s[] = substr($chars, (rand() % 59), 1);
	}
	return join("", $s);
}

print_r(dbFetch(dbQuery(sprintf("SELECT * FROM `wp_11_ngg_pictures`"))));
?>