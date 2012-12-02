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

dbQuery(sprintf("UPDATE `%s11_ngg_pictures` SET `username` = (FLOOR(100000000 * RAND()) * `pid`)", $table_prefix));

dbQuery(sprintf("ALTER TABLE  `%s11_ngg_pictures` ADD UNIQUE (`username`)", $table_prefix)) or die(mysqli_error($dbConnection));

dbQuery(sprintf("ALTER TABLE  `%s11_ngg_pictures` ADD  `password` VARCHAR( 32 ) NOT NULL AFTER  `username`", $table_prefix)) or die(mysqli_error($dbConnection));

$companies = dbFetch(dbQuery(sprintf("SELECT `pid`, `organisation`, `fullname`, `email` FROM `%s11_ngg_pictures`", $table_prefix)));

if(!is_array($companies)) die('No organisations registered');

$csv = array();

foreach($companies as $key => $company){
	$username = preg_replace("/[^a-zA-Z0-9]+/", "", $company['organisation']);
	$username = strlen($username) ? $username : preg_replace("/[^a-zA-Z0-9]+/", "", $company['alttext']);
	$username = strlen($username) ? $username : preg_replace("/[^a-zA-Z0-9]+/", "", $company['fullname']);
	$username = strtolower($username);
	$password = randomString(8);
	if(!strlen($username)) continue;
	dbQuery(sprintf("UPDATE IGNORE `%s11_ngg_pictures` SET `username` = '%s', `password` = '%s' WHERE `pid` = %d", $table_prefix, $username, md5($password), $company['pid']));
	$affectedRows = dbAffectedRows();
	if(!$affectedRows) {
		$username = $username.'-'.$company['pid'];
		dbQuery(sprintf("UPDATE IGNORE `%s11_ngg_pictures` SET `username` = '%s', `password` = '%s' WHERE `pid` = %d", $table_prefix, $username, md5($password), $company['pid']));
		$affectedRows = dbAffectedRows();
	}
	$company['password'] = $password;
	$company['username'] = $username;
	$company['organisation'] = str_replace(',', '', $company['organisation']);
	$company['fullname'] = str_replace(',', '', $company['fullname']);
	$csv[] = implode(', ', $company);
}

print implode(', ', array_keys($company));

print "\n";

print implode("\n", $csv);