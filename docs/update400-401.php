<?php

include '../config.php';
include PATH . '/includes/functions.php';
include PATH . '/includes/functions/db.php';

$db = new Dbaccess;
$db->connect ( DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME ) or error('Critical Error', mysql_error () );

echo '<br><b>All the tables have been upgraded successfully.</b><br>';

$db->close();

?>