<?php

include ('.././config.php');
include ('.././includes/functions.php');
include ('.././includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD calendar text') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD latitude varchar(255)') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD longitude varchar(255)') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("visited_limit", "3", "Number of Recently Visited Listings to store and show")') or error('Critical Error', mysql_error () );

// New in 3.0
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name7 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name8 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name9 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name10 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name11 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name12 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property types table has been upgraded.<br />';

$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name7 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name8 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name9 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name10 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name11 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name12 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property styles types table has been upgraded.<br />';

$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name7 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name8 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name9 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name10 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name11 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name12 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property buildings table has been upgraded.<br />';

$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name7 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name8 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name9 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name10 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name11 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name12 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property appliances table has been upgraded.<br />';

$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name7 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name8 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name9 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name10 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name11 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name12 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property features table has been upgraded.<br />';

$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name7 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name8 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name9 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name10 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name11 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name12 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property basement table has been upgraded.<br />';

$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name7 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name8 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name9 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name10 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name11 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name12 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property garage table has been upgraded.<br />';

$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name7 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name8 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name9 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name10 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name11 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name12 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property types2 table has been upgraded.<br />';
echo '<br><b>All the tables have been updated successfully.</b><br>';

$db->close();

?>