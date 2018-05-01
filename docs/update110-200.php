<?php

include ('.././config.php');
include ('.././includes/functions.php');
include ('.././includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("path_to_mencoder", "/usr/bin/mencoder", "Path to mencoder tool. Default /usr/bin/mencoder")') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD location2 text') or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . LOCATION1_TABLE . ' (selector int, category text, sccounter int DEFAULT \'0\' NOT NULL, ssccounter int DEFAULT \'0\' NOT NULL, fcounter int DEFAULT \'0\' NOT NULL, top int DEFAULT \'0\' NOT NULL, ip text)') or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . LOCATION2_TABLE . ' (catsel int, catsubsel int, subcategory text, ssccounter int DEFAULT \'0\' NOT NULL, fcounter int DEFAULT \'0\' NOT NULL)') or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . LOCATION3_TABLE . ' (catsel int, catsubsel int, catsubsubsel int, subsubcategory text, fcounter int DEFAULT \'0\' NOT NULL)') or error('Critical Error', mysql_error () );

$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name2 varchar(50) DEFAULT NULL, ADD name3 varchar(50) DEFAULT NULL, ADD name4 varchar(50) DEFAULT NULL, ADD name5 varchar(50) DEFAULT NULL, ADD name6 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name2 varchar(50) DEFAULT NULL, ADD name3 varchar(50) DEFAULT NULL, ADD name4 varchar(50) DEFAULT NULL, ADD name5 varchar(50) DEFAULT NULL, ADD name6 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . LOCATIONS_TABLE . ' ADD name2 varchar(50) DEFAULT NULL, ADD name3 varchar(50) DEFAULT NULL, ADD name4 varchar(50) DEFAULT NULL, ADD name5 varchar(50) DEFAULT NULL, ADD name6 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name2 varchar(50) DEFAULT NULL, ADD name3 varchar(50) DEFAULT NULL, ADD name4 varchar(50) DEFAULT NULL, ADD name5 varchar(50) DEFAULT NULL, ADD name6 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name2 varchar(50) DEFAULT NULL, ADD name3 varchar(50) DEFAULT NULL, ADD name4 varchar(50) DEFAULT NULL, ADD name5 varchar(50) DEFAULT NULL, ADD name6 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name2 varchar(50) DEFAULT NULL, ADD name3 varchar(50) DEFAULT NULL, ADD name4 varchar(50) DEFAULT NULL, ADD name5 varchar(50) DEFAULT NULL, ADD name6 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name2 varchar(50) DEFAULT NULL, ADD name3 varchar(50) DEFAULT NULL, ADD name4 varchar(50) DEFAULT NULL, ADD name5 varchar(50) DEFAULT NULL, ADD name6 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name2 varchar(50) DEFAULT NULL, ADD name3 varchar(50) DEFAULT NULL, ADD name4 varchar(50) DEFAULT NULL, ADD name5 varchar(50) DEFAULT NULL, ADD name6 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name2 varchar(50) DEFAULT NULL, ADD name3 varchar(50) DEFAULT NULL, ADD name4 varchar(50) DEFAULT NULL, ADD name5 varchar(50) DEFAULT NULL, ADD name6 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo '<br><b>All the tables have been updated successfully.</b><br>';

$db->close();

?>