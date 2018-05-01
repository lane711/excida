<?php

// Output buffering to allow for header() redirects, etc. throughout the script, among other things
ob_start();

// Specify default timezone
date_default_timezone_set( @date_default_timezone_get() );

// Prevent scripts from being accessed in unauthorized ways
if ( !defined( 'PMR' ) || ( defined( 'PMR' ) && PMR != true ) )
{
	die( 'You cannot access this file directly.' );
}

// Install/docs dir check
if ( $page != 'install' )
{
	if ( file_exists( PATH . '/docs/version' ) || file_exists( PATH . '/install/index.php' ) )
	{
		die( 'You must remove the /install and /docs directories before proceeding.' );		
	}
}

// Include all necessary function/class files
include PATH . '/includes/functions.php';
include PATH . '/includes/functions/db.php';
include PATH . '/includes/functions/sessions.php';
include PATH . '/includes/functions/zip.php';
include PATH . '/includes/functions/packages.php';
include PATH . '/includes/phpmailer/class.phpmailer.php';
include PATH . '/includes/functions/template.php';
include PATH . '/includes/functions/captcha.php';
include PATH . '/includes/functions/favorites.php';
include PATH . '/includes/functions/filter.php';
include PATH . '/includes/functions/form.php';
include PATH . '/includes/functions/images.php';
include PATH . '/includes/functions/ratings.php';
include PATH . '/includes/functions/system.php';
include PATH . '/includes/functions/auth.php';
include PATH . '/includes/functions/simpleGMapAPI.php';

// We use this object throughout the script for any map-based functionality
$map = new simpleGMapAPI();

// Initialise a new user session or start an existing one
$session = new Session();

// Allow Back button in IE
header( 'Cache-control: private' );

// Start a new sql connection
$db = new Dbaccess();

if ( !$db->connect( DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME ) ) 
{
	if ( mysql_error() == '' )
	{
		$database_incorrect = 'Database name is incorrect or doesn\'t exist'; 
	}
	else 
	{
		$database_incorrect = '';
	}
	die( 'Initialization Error' . mysql_error() . $database_incorrect );
}

// Get all configuration data as set in the administration panel
$confClause = " site_id=0";
if($session->fetch('u_site_id')!=""){
	$confClause= " site_id=".$session->fetch('u_site_id');
}
$sql = "SELECT name, val FROM " . CONFIGURATION_TABLE . " WHERE ".$confClause;
$q = $db->query( $sql ) or die( 'Continue Installation: Database tables do not exist. Please run the installer to complete the installation process: <a href="' . URL . '/install/index.php">' . URL . '/install/index.php</a>' );
$conf = array();
while ( $conf_array = $db->fetcharray( $q ) )
{
	$conf[$conf_array['name']] = $conf_array['val'];
}

// Template selection

// First priority is a user trying to change it
if ( isset( $_POST['option_template'] ) )
{
	$session->set( 'intemplate', $_POST["option_template"] );
	$cookie_template = $_POST["option_template"];
}
// Also check if they are passing it via an HTTP request
elseif ( isset( $_GET['template'] ) )
{
	$session->set( 'intemplate', $_GET["template"] );
	$cookie_template = $_GET["template"];
}
// Check if they previously set a template
elseif ( isset( $_SESSION['intemplate'] ) )
{
	$cookie_template = $_SESSION['intemplate'];
}
// Default template as set in admin
else
{
	if ( !empty( $conf['template'] ) )
	{
		$session->set( 'intemplate', $conf['template'] );
		$cookie_template = $conf['template'];
	}
	else
	{
		$session->set( 'intemplate', 'default' );
		$cookie_template = 'responsive';
	}
} 

// Language selection

// First priority is a user trying to change it
if ( isset( $_POST['option_language'] ) )
{
	$session->set( 'language', $_POST["option_language"] );
	$cookie_language = $_POST["option_language"];
}
// Also check if they are passing it via an HTTP request
elseif ( isset( $_GET['lang'] ) )
{
	$session->set( 'language', $_GET["lang"] );
	$cookie_language = $_GET["lang"];
}
// Check if they previously set a template
elseif ( isset( $_SESSION['language'] ) )
{
	$cookie_language = $_SESSION['language'];
}
// Default language as set in admin
else
{
	if ( !empty( $conf['language'] ) )
	{
		$session->set( 'language', $conf['language'] );
		$cookie_language = $conf['language'];
	}
	else
	{
		$session->set( 'language', 'english' );
		$cookie_language = 'english';
	}
} 

// Include language file containging in $cookie_language
$lang = array();

if ( file_exists( PATH . '/languages/' . $cookie_language . '.lng.php' ) )
{
	include PATH . '/languages/' . $cookie_language . '.lng.php';
}
else
{
	include PATH . '/languages/english.lng.php';
}

include PATH . '/languages/settings.php';

// General clean up

// Clean the Favorite Listings Cookie
favoriteListingsClean();

// Clean the Visited Listings Cookie
visitedListingsClean();

?>