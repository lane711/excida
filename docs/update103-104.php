<?php

include ('.././config.php');
include ('.././includes/functions.php');
include ('.././includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("free_listings", "0", "Free Agent Package Number of Listings Allowed")') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("free_gallery", "0", "Free Agent Package Number of Photo Gallery Images Allowed")') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("free_mainimage", "OFF", "Free Agent Package Show Main Property Image (ON/OFF)")') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("free_photo", "OFF", "Free Agent Package Show Agent Photo/Logo (ON/OFF)")') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("free_phone", "OFF", "Free Agent Package Show Agent Phone/Fax/Mobile (ON/OFF)")') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("free_address", "OFF", "Free Agent Package Show Agent and Property Address (ON/OFF)")') or error('Critical Error', mysql_error () );

$db->query('ALTER TABLE ' . USERS_TABLE . ' ADD package int(10) UNSIGNED DEFAULT \'0\'') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . USERS_TABLE . ' ADD date_upgraded varchar(10) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . USERS_TABLE . ' ADD ip_upgraded text') or error('Critical Error', mysql_error () );

// FEATURED_PACKAGES_AGENT TABLE

$db->query('DROP TABLE IF EXISTS ' . PACKAGES_AGENT_TABLE) or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . PACKAGES_AGENT_TABLE . ' (

	id int(10) UNSIGNED auto_increment, 
	name varchar(255) DEFAULT NULL,
	price varchar(255) DEFAULT NULL,
	days int(10) UNSIGNED DEFAULT \'0\',
	position int(10) UNSIGNED DEFAULT \'0\',
	listings varchar(255) DEFAULT NULL,
	gallery varchar(255) DEFAULT NULL,
	mainimage varchar(255) DEFAULT NULL,
	photo varchar(255) DEFAULT NULL,
	phone varchar(255) DEFAULT NULL,
	address varchar(255) DEFAULT NULL,
	PRIMARY KEY (id)

)') or error('Critical Error', mysql_error () );


// AGENTS ONLINE TABLE

$db->query('DROP TABLE IF EXISTS ' . ONLINE_TABLE) or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . ONLINE_TABLE . ' (

	id int(10) UNSIGNED auto_increment, 
	username varchar(255) DEFAULT NULL,
	time varchar(255) DEFAULT NULL,
	PRIMARY KEY (id)

)') or error('Critical Error', mysql_error () );

$db->query('DROP TABLE IF EXISTS ' . FEATURED_AGENTS_TABLE) or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . FEATURED_AGENTS_TABLE . ' (id int(10) UNSIGNED, start_date varchar(10) DEFAULT NULL, end_date varchar(10) DEFAULT NULL, featured tinyint(1) UNSIGNED DEFAULT \'0\' NOT NULL, package int(10) UNSIGNED DEFAULT \'0\', PRIMARY KEY (id), KEY featured (featured))') or error('Critical Error', mysql_error () );

// LISTING_ALERTS TABLE

$db->query('DROP TABLE IF EXISTS ' . ALERTS_TABLE) or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . ALERTS_TABLE . ' (

	approved varchar(255) DEFAULT NULL,
	email varchar(255) DEFAULT NULL,
	name varchar(255) DEFAULT NULL,
	type smallint(5) UNSIGNED DEFAULT \'0\',
	zip varchar(255) DEFAULT NULL,
	city varchar(255) DEFAULT NULL,
	code varchar(255) DEFAULT NULL

)') or error('Critical Error', mysql_error () );


echo '<br><b>All the tables have been updated successfully.</b><br>';

$db->close();

?>