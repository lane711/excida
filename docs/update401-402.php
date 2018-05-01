<?php

include '../config.php';
include PATH . '/includes/functions/template.php';
include PATH . '/includes/functions.php';
include PATH . '/includes/functions/db.php';

$db = new Dbaccess;
$db->connect ( DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME ) or error('Critical Error', mysql_error () );

// Configuration table
$db->query( "INSERT INTO " . CONFIGURATION_TABLE . " ( name, val, descr ) VALUES ( 'captcha_status', 'OFF', 'Enable or disable reCAPTCHA to protect against spam.' )" ) or error( 'Critical Error', mysql_error () );
$db->query( "INSERT INTO " . CONFIGURATION_TABLE . " ( name, val, descr ) VALUES ( 'captcha_private_key', '', 'Captcha private key from Google reCAPTCHA: https://www.google.com/recaptcha' )" ) or error( 'Critical Error', mysql_error () );
$db->query( "INSERT INTO " . CONFIGURATION_TABLE . " ( name, val, descr ) VALUES ( 'captcha_public_key', '', 'Captcha public key from Google reCAPTCHA: https://www.google.com/recaptcha' )" ) or error( 'Critical Error', mysql_error () );
$db->query( "INSERT INTO " . CONFIGURATION_TABLE . " ( name, val, descr ) VALUES ( 'allow_registration', 'ON', 'Enable or disable the ability for user\'s to register for an account.' )" ) or error( 'Critical Error', mysql_error () );

echo '<br><b>Configuration table has been upgraded successfully.</b><br>';

// Featured listing table
$db->query( "ALTER TABLE " . FEATURED_TABLE . " CHANGE start_date start_date DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );
$db->query( "ALTER TABLE " . FEATURED_TABLE . " CHANGE end_date end_date DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );

echo '<br><b>Featured listings table has been upgraded successfully.</b><br>';

// Featured agents table
$db->query( "ALTER TABLE " . FEATURED_AGENTS_TABLE . " CHANGE start_date start_date DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );
$db->query( "ALTER TABLE " . FEATURED_AGENTS_TABLE . " CHANGE end_date end_date DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );

echo '<br><b>Featured agents table has been upgraded successfully.</b><br>';

// Gallery table
$db->query( "ALTER TABLE " . GALLERY_TABLE . " CHANGE date_added date_added DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );
$db->query( "ALTER TABLE " . GALLERY_TABLE . " CHANGE date_updated date_updated DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );

echo '<br><b>Gallery table has been upgraded successfully.</b><br>';

// Listings table
$db->query( "ALTER TABLE " . PROPERTIES_TABLE . " CHANGE date_added date_added DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );
$db->query( "ALTER TABLE " . PROPERTIES_TABLE . " CHANGE date_approved date_approved DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );
$db->query( "ALTER TABLE " . PROPERTIES_TABLE . " CHANGE date_updated date_updated DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );
$db->query( "ALTER TABLE " . PROPERTIES_TABLE . " CHANGE date_upgraded date_upgraded DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );

echo '<br><b>Listings table has been upgraded successfully.</b><br>';

// Pages table
$db->query( "ALTER TABLE " . PAGES_TABLE . " CHANGE `date` date DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );

echo '<br><b>Pages table has been upgraded successfully.</b><br>';

// Ratings table
$db->query( "ALTER TABLE " . RATINGS_TABLE . " CHANGE `date` date DATE DEFAULT NULL " ) or error( 'Critical Error', mysql_error() );

echo '<br><b>Ratings table has been upgraded successfully.</b><br>';

echo '<br><b>All the tables have been upgraded successfully.</b><br>';

$db->close();

?>