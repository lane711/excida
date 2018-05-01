<?php

include ('.././config.php');
include ('.././includes/functions.php');
include ('.././includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("paypal_email", "me@domain.com", "Your PRIMARY paypal email address")') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("paypal_currency", "USD", "Paypal currency, USD, GBP etc.")') or error('Critical Error', mysql_error () );

// FEATURED_PACKAGES TABLE

$db->query('DROP TABLE IF EXISTS ' . PACKAGES_TABLE) or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . PACKAGES_TABLE . ' (

	id int(10) UNSIGNED auto_increment, 
	name varchar(255) DEFAULT NULL,
	price varchar(255) DEFAULT NULL,
	days int(10) UNSIGNED DEFAULT \'0\',
	position int(10) UNSIGNED DEFAULT \'0\',
	PRIMARY KEY (id)

)') or error('Critical Error', mysql_error () );

$db->query('ALTER TABLE ' . FEATURED_TABLE . ' ADD package int(10) UNSIGNED DEFAULT \'0\'') or error('Critical Error', mysql_error () );

echo '<br><b>All the tables have been updated successfully.</b><br>';

$db->close();

?>