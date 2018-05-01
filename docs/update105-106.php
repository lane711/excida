<?

include ('.././config.php');
include ('.././includes/functions.php');
include ('.././includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );

$db->query('ALTER TABLE ' . USERS_TABLE . ' ADD number varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );

echo '<br><b>All the tables have been updated successfully.</b><br>';

$db->close();

?>