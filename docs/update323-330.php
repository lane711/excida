<?php

include ('../config.php');
include ( PATH . '/includes/functions.php');
include ( PATH . '/includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

// No database changes made from 3.2.3 -> 3.3.0

echo '<br><b>All the tables have been upgraded successfully.</b><br>';

$db->close();

?>