<?

include ('.././config.php');
include ('.././includes/functions.php');
include ('.././includes/functions/db.php');

$db = new Dbaccess;
$db->connect ( $dbi['sql_host'], $dbi['sql_username'], $dbi['sql_password'], $dbi['sql_dbname'] ) or error('Critial Error', mysql_error () );

// Unset all sql datails for the security purposes
unset ( $dbi );


$db->query('CREATE TABLE ' . PAGES_TABLE . ' (menu text, text text, id int(5) UNSIGNED auto_increment, PRIMARY KEY (id))') or error('Critical Error', mysql_error () );

$db->query('ALTER TABLE ' . PACKAGES_TABLE . ' CHANGE position position varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PACKAGES_AGENT_TABLE . ' CHANGE position position varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );

 $db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("2co_user_id", "", "2co User ID")') or error('Critical Error', mysql_error () );
 $db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("2co_secret_word", "", "2co Secret Word")') or error('Critical Error', mysql_error () );
 $db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("2co_gateway", "https://www2.2checkout.com/2co/buyer/purchase", "2co Default Gateway URL")') or error('Critical Error', mysql_error () );
 $db->query('INSERT INTO ' . CONFIGURATION_TABLE . ' (name, val, descr) VALUES ("gateway", "1", "Default Payment Gateway (1 for PayPal, 2 for 2checkout)")') or error('Critical Error', mysql_error () );

// $db->query('DROP TABLE IF EXISTS ' . TYPES2_TABLE) or error('Critical Error', mysql_error () );
$db->query('CREATE TABLE ' . TYPES2_TABLE . ' (name varchar(50) DEFAULT NULL, id int(5) UNSIGNED auto_increment, PRIMARY KEY (id))') or error('Critical Error', mysql_error () );

 $db->query('INSERT INTO ' . TYPES2_TABLE . ' (name, id) VALUES ("Sell", 1)') or error('Critical Error', mysql_error () );
 $db->query('INSERT INTO ' . TYPES2_TABLE . ' (name, id) VALUES ("Buy", 2)') or error('Critical Error', mysql_error () );
 $db->query('INSERT INTO ' . TYPES2_TABLE . ' (name, id) VALUES ("Rent", 3)') or error('Critical Error', mysql_error () );

$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD type2 smallint(5) UNSIGNED DEFAULT \'0\'') or error('Critical Error', mysql_error () );
$db->query('ALTER TABLE ' . PROPERTIES_TABLE . ' ADD video varchar(255) DEFAULT NULL') or error('Critical Error', mysql_error () );

$db->query('UPDATE ' . PROPERTIES_TABLE . ' SET type2 = "1"') or error('Critical Error', mysql_error () );

echo '<br><b>All the tables have been updated successfully.</b><br>';

$db->close();

?>