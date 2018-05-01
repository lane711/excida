<?php

include ('../config.php');
include ( PATH . '/includes/functions.php');
include ( PATH . '/includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME ) or error(' Critical Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

//$db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("mencoder", "OFF", "Enable MEncoder for video conversion. Most servers do not support this by default. If disabled, you can still upload FLV files for video tours. (ON/OFF)")') or error('(1) critical Error', mysql_error () );

echo '<br><b>All the tables have been upgraded successfully.</b><br>';

$db->close();

?>