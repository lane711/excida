<?php

include ('.././config.php');
include ('.././includes/functions.php');
include ('.././includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("banner_width", "468", "Banner Ad maximum width allowed")') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("banner_height", "60", "Banner Ad maximum height allowed")') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("banner_size", "51200", "Banner Ad maximum size in Bytes")') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("banner_featured", "OFF", "Show banners for featured listings only. (ON/OFF)")') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD banner varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );

// ZIP CODES TABLE

$db->query('DROP TABLE IF EXISTS ' . ZIP_TABLE) or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . ZIP_TABLE . ' (

	zip varchar(5) DEFAULT NULL,
	city varchar(255) DEFAULT NULL,
	state varchar(3) DEFAULT NULL,
	county varchar(255) DEFAULT NULL,
	latitude varchar(255),
	longitude varchar(255)

)') or error('Critical Error', mysql_error () );

echo '<br><b>All the tables have been updated successfully.</b><br>';

$db->close();

?>