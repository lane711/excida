<?php

include ('../config.php');
include ( PATH . '/includes/functions.php');
include ( PATH . '/includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

// New in 3.2
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name13 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name14 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name15 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name16 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name17 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name18 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name19 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name20 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name21 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name22 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name23 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name24 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name25 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name26 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name27 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name28 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name29 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES_TABLE . ' ADD name30 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property types table has been upgraded.<br />';

$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name13 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name14 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name15 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name16 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name17 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name18 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name19 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name20 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name21 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name22 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name23 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name24 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name25 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name26 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name27 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name28 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name29 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STYLES_TABLE . ' ADD name30 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property styles types table has been upgraded.<br />';

$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name13 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name14 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name15 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name16 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name17 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name18 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name19 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name20 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name21 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name22 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name23 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name24 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name25 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name26 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name27 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name28 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name29 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BUILDINGS_TABLE . ' ADD name30 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property buildings table has been upgraded.<br />';

$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name13 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name14 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name15 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name16 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name17 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name18 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name19 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name20 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name21 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name22 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name23 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name24 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name25 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name26 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name27 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name28 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name29 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . APPLIANCES_TABLE . ' ADD name30 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property appliances table has been upgraded.<br />';

$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name13 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name14 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name15 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name16 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name17 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name18 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name19 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name20 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name21 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name22 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name23 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name24 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name25 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name26 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name27 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name28 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name29 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . FEATURES_TABLE . ' ADD name30 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property features table has been upgraded.<br />';

$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name13 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name14 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name15 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name16 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name17 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name18 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name19 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name20 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name21 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name22 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name23 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name24 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name25 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name26 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name27 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name28 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name29 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . BASEMENT_TABLE . ' ADD name30 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property basement table has been upgraded.<br />';

$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name13 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name14 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name15 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name16 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name17 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name18 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name19 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name20 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name21 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name22 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name23 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name24 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name25 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name26 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name27 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name28 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name29 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . GARAGE_TABLE . ' ADD name30 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property garage table has been upgraded.<br />';

$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name13 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name14 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name15 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name16 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name17 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name18 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name19 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name20 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name21 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name22 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name23 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name24 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name25 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name26 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name27 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name28 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name29 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . TYPES2_TABLE . ' ADD name30 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property types2 table has been upgraded.<br />';

$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu2 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu3 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu4 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu5 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu6 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu7 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu8 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu9 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu10 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu11 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu12 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu13 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu14 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu15 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu16 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu17 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu18 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu19 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu20 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu21 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu22 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu23 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu24 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu25 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu26 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu27 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu28 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu29 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD menu30 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );

$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text2 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text3 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text4 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text5 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text6 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text7 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text8 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text9 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text10 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text11 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text12 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text13 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text14 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text15 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text16 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text17 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text18 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text19 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text20 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text21 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text22 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text23 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text24 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text25 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text26 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text27 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text28 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text29 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD text30 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );

$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD date datetime') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD status ENUM(\'1\', \'0\') NOT NULL DEFAULT \'1\'') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PAGES_TABLE . ' ADD string varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'CMS table has been upgraded.<br />';

$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title2 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title3 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title4 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title5 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title6 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title7 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title8 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title9 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title10 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title11 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title12 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title13 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title14 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title15 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title16 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title17 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title18 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title19 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title20 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title21 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title22 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title23 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title24 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title25 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title26 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title27 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title28 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title29 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD title30 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );

$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description2 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description3 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description4 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description5 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description6 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description7 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description8 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description9 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description10 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description11 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description12 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description13 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description14 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description15 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description16 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description17 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description18 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description19 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description20 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description21 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description22 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description23 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description24 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description25 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description26 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description27 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description28 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description29 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD description30 TEXT DEFAULT NULL') or error('Critical Error', mysql_error () );

$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD video2 varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property listings table has been upgraded.<br />';

$db->query('DROP TABLE IF EXISTS ' . STATUS_TABLE) or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . STATUS_TABLE . ' (

	id int(10) UNSIGNED auto_increment, 
	counter int(10) DEFAULT \'0\',
	PRIMARY KEY (id)

)') or error('Critical Error', mysql_error () );

$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name2 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name3 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name4 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name5 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name6 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name7 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name8 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name9 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name10 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name11 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name12 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name13 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name14 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name15 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name16 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name17 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name18 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name19 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name20 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name21 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name22 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name23 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name24 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name25 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name26 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name27 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name28 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name29 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . STATUS_TABLE . ' ADD name30 varchar(50) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo 'Property status types table has been upgraded.<br />';

$db->query('INSERT INTO ' . STATUS_TABLE . ' (name, id) VALUES ("Available", 1)') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . STATUS_TABLE . ' (name, id) VALUES ("Sale Pending", 2)') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . STATUS_TABLE . ' (name, id) VALUES ("Under Agreement", 3)') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . STATUS_TABLE . ' (name, id) VALUES ("Sold", 4)') or error('Critical Error', mysql_error () );
$db->query('INSERT INTO ' . STATUS_TABLE . ' (name, id) VALUES ("Unavailable", 5)') or error('Critical Error', mysql_error () );

echo 'Default property status types have been added.<br />';

// Set default status type for all listings
$db->query('UPDATE ' . PROPERTIES_TABLE . ' SET status = "1"') or error('Critical Error', mysql_error () );

echo 'All listings have been defaulted to default property status (\'Available\').<br />';

$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("mencoder", "OFF", "Enable MEncoder for video conversion. Most servers do not support this by default. If disabled, you can still upload FLV files for video tours. (ON/OFF)")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("quick_search", "ON", "Enable or disable the \'Quick Search\' from displaying on the left-hand side of the site (ON/OFF)")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("featured_listings", "ON", "Enable or disable featured listings from displaying on the index page (ON/OFF)")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("all_listings", "OFF", "Enable or disable showing all listings, not just featured, randomly on the index page (ON/OFF)")') or error('(2) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("recent_listings", "ON", "Enable or disable recent listings/agents and most visited listings on the index page at the bottom of the page (ON/OFF)")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("main_search", "ON", "Enable or disable the main search (WHAT / WHERE / OPTIONS) at the top of the index page (ON/OFF)")') or error('(3) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("main_map", "OFF", "Enable or disable the map from appearing on the index page (Must have Google Maps API key installed in order to show) (ON/OFF)")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("featured_agents", "OFF", "Enable or disable featured agents from displaying on the index page (ON/OFF)")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("settings_box", "ON", "Enable or disable the \'Settings\' box from displaying with Language/Template options (ON/OFF)")') or error('(4) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("online_box", "ON", "Enable or disable the \'Online\' box from displaying with logged-in agents currently online (ON/OFF)")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("all_agents", "OFF", "Enable or disable showing all agents, not just featured, randomly on the index page (ON/OFF)")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("expired_listings", "1", "What should happen to listings that have expired? (1 - Disable Listing, 2 - Delete Listing)")') or error('(5) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("contact_agents", "1", "Specify who has access to listing contact information. (1 - Everyone, 2 - Registered account holders only, 3 - Paid account holders only)")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("slideshow_main", "ON", "Enable or disable the \'Slideshow\' box from displaying on the main page (ON/OFF)")') or error('(7) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("slideshow_type", "1", "Specify whether the slideshow should grab all listings or featured listings only (1 - Featured only (random), 2 - All listings (random))")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("slideshow_speed", "5000", "Specify the slideshow scroll speed. Note: This is in milliseconds (5000 = 5 seconds. 10000 = 10 seconds)")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("slideshow_visibility", "2", "Specify how many properties should be visible in the slideshow at a time")') or error('(6) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("map_zoom", "5", "Specify how zoomed out the map should be on pages that have a map. (1 - zoomed in all the way. 10 - zoomed out all the way. Can be anything from 1 - 10.")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("map_height", "400", "Specify the Google Map height on all pages that have a map. (E.g., 400 - 400 pixels wide")') or error('(8) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("map_width", "500", "Specify the Google Map width on all pages that have a map. (E.g., 500 - 500 pixels in height")') or error('(9) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("expire_notice", "5", "Specify the number of days in advance a seller should be e-mailed that their package is going to expire (5 - 5 days, 14 - 2 weeks")') or error('(1) critical Error', mysql_error () );
$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("show_calendar", "ON", "Enable or disable showing the availability calendar (ON/OFF")') or error('(1) critical Error', mysql_error () );

echo 'Configuration table has been upgraded.<br />';

echo '<br><b>All the tables have been upgraded successfully.</b><br>';

$db->close();

?>