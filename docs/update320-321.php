<?php

include ('../config.php');
include ( PATH . '/includes/functions.php');
include ( PATH . '/includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

// New in 3.2.1
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name13 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name14 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name15 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name16 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name17 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name18 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name19 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name20 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name21 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name22 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name23 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name24 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name25 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name26 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name27 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name28 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name29 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name30 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Locations table has been upgraded.<br />';

echo '<br><b>All the tables have been upgraded successfully.</b><br>';

$db->close();

?>