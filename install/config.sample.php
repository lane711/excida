<?php

//---------------------------------------------------------------------------//
//
//			SUPPORT AVAILABLE AT: http://www.realtyscript.com
//
//---------------------------------------------------------------------------//



//----------------------------------------------------------------------------
//	DEBUG
//
//	FOR TROUBLESHOOTING INSTALLATION PROBLEMS
//	 
//	NOTE: IF YOU NEED TO SEE ERRORS, YOU CAN ENABLE THEM BY SETTING THIS TO TRUE INSTEAD OF FALSE.

define( 'DEGUB', false );

if ( 'DEBUG' == true )
{
	error_reporting( E_ALL & ~E_STRICT & ~E_NOTICE );
	ini_set( 'display_errors', true );
}
else
{
	error_reporting(0);
	ini_set( 'display_errors', false );
}



//----------------------------------------------------------------------------
//	DEMO
//
//	FOR DISABLING SENSITIVE ASPECTS OF THE SOFTWARE FOR DEMO PURPOSES

define( 'DEMO', false );



//----------------------------------------------------------------------------
//	LICENSE KEY
//
// 	NOTE: PLEASE MAKE SURE LICENSE KEY DOES NOT HAVE SPACES, LINE BREAKS, ETC.

define( 'LICENSE', '{LICENSE}' );


//----------------------------------------------------------------------------
//	COPYRIGHT LICENSE KEY
//
// 	NOTE: PLEASE MAKE SURE LICENSE KEY DOES NOT HAVE SPACES, LINE BREAKS, ETC.

define( 'COPYRIGHT_LICENSE', '{COPYRIGHT_LICENSE}' );



//----------------------------------------------------------------------------
//	URL & MEDIA_URL
//
//	NOTE: YOUR URL SHOULD BE IN THE FORMAT http://www.domain.com UNLESS YOU ARE
//			USING A SUB DOMAIN. THE http://www. is required. Please see examples.
//
//	CORRECT: http://www.domain.com
//	CORRECT: http://realestate.domain.com
// 	CORRECT: http://www.domain.com/realestate (if you are installing it in 'realestate' directory
// 	INCORRECT: http://domain.com
//  INCORRECT: http://www.domain.com/ (no trailing slash)
//
//	The MEDIA_URL is simply where the images are stored (useful for CDN providers, etc.)

define( 'URL' , '{URL}' );

define( 'MEDIA_URL' , '{MEDIA_URL}' );



//----------------------------------------------------------------------------
//	MYSQL DATABASE INFORMATION
//
//	PLEASE ENTER IN YOUR DATABASE INFORMATION BELOW.
//	YOUR WEB HOSTING PROVIDER WILL BE ABLE TO ASSIST YOU WITH THIS.

define( 'DB_NAME', '{DB_NAME}' );				// Name of the MySQL database
define( 'DB_USERNAME', '{DB_USERNAME}' );				// Username that has access to this DB
define( 'DB_PASSWORD', '{DB_PASSWORD}' );	// Password for the username that has access to this DB

// You shouldn't have to modify these, unless your server uses a specific host (e.g., mysql.domain.com)
define( 'DB_HOST', '{DB_HOST}' );			// Database host - normally, this is 'localhost'
define( 'DB_TYPE', 'mysql' );				// Database type - leave set to 'mysql'



//----------------------------------------------------------------------------
//	PATH TO YOUR DIRECTORY / MEDIA DIRECTORY
//
//	NORMALLY, YOU WILL NOT NEED TO CHANGE THIS. HOWEVER, IF YOU ARE RECEIVING NOT FOUND
// 		ERRORS, PLEASE LOOK AT THE EXAMPLES AND ENTER THIS IN MANUALLY.
//
// 	PLEASE NOTE, THESE ARE ONLY EXAMPLES. YOUR PATH WILL BE DIFFERENT.
// 
//	CORRECT: define('PATH', '/home/username/public_html');
//	CORRECT: define('PATH', '/home/username/www/domain.com');
//	INCORRECT: define('PATH', '/home/username/public_html/'); (the trailing / should not be there)

define( 'PATH', '{PATH}' );

define( 'MEDIA_PATH', '{PATH}/media' );







// THAT'S IT! PLEASE LOAD YOUR WEB SITE TO CONTINUE THE INSTALLATION PROCESS!










//---------------------------------------------------------------------------//
//	
//
//				OPTIONAL ADVANCED SETTINGS BELOW
//
//
//---------------------------------------------------------------------------//



//----------------------------------------------------------------------------
//	PHPMAILER CLASS SETTINGS
//
//	NORMALLY, YOU WILL NOT NEED TO CHANGE THIS. HOWEVER, IF YOU ARE HAVING PROBLEMS
//		SENDING OUT E-MAILS WITH YOUR WEB SITE, YOU MAY WANT TO TRY MODIFYING THIS.
//
//		NOTE: ONLY YOUR WEB HOSTING PROVIDER CAN TELL YOU WHAT SMTP SETTINGS TO USE!
//
//	1 - standard mail() function (default)
//	2 - sendmail
//	3 - SMTP or Gmail

define( 'PHPMAILER', 1 );

//	If you are using option '3', you must enter in the following:

$smtp['host'] = '';			// SMTP server host name
$smtp['login'] = '';		// SMTP server login
$smtp['password'] = '';		// SMTP server password



//----------------------------------------------------------------------------
//	ADMINISTRATION PANEL IP RESTRICTION
//
//	NORMALLY, YOU WILL NOT NEED TO CHANGE THIS. HOWEVER, IF YOU WISH TO PROTECT
//		YOUR ADMINISTRATION PANEL BY RESTRICTING IP ADDRESS ACCESS, DO SO HERE.
//
//	Leave empty to allow all IPs, delimit IPs with ;
//
//	CORRECT: $admin_ip = '127.0.0.1';
// 	CORRECT: $admin_ip = '127.0.0.1;192.168.0.1';
// 	CORRECT: $admin_ip = '127.0.0.*;192.168.*';
//	CORRECT: $admin_ip = 'XX.XX.*';
//	INCORRECT: $admin_ip = 'XX.XX.*.*';

$admin_ip = '';



//----------------------------------------------------------------------------
//	DATABASE TABLE PREFIX
//
//	Useful if you want to have more than one PMR installation running inside
//		the same database.
//

define( 'TABLE_PREFIX', '{TABLE_PREFIX}' );

// Please, do not edit code below.
define( 'CONFIGURATION_TABLE', TABLE_PREFIX . 'configuration');
define( 'USERS_TABLE', TABLE_PREFIX . 'users');
define( 'PROPERTIES_TABLE', TABLE_PREFIX . 'listings');
define( 'LOCATIONS_TABLE', TABLE_PREFIX . 'locations');
define( 'TYPES_TABLE', TABLE_PREFIX . 'types');
define( 'TYPES2_TABLE', TABLE_PREFIX . 'types2');
define( 'STYLES_TABLE', TABLE_PREFIX . 'styles');
define( 'GALLERY_TABLE', TABLE_PREFIX . 'gallery');
define( 'BUILDINGS_TABLE', TABLE_PREFIX . 'buildings');
define( 'APPLIANCES_TABLE', TABLE_PREFIX . 'appliances');
define( 'FEATURES_TABLE', TABLE_PREFIX . 'features');
define( 'BASEMENT_TABLE', TABLE_PREFIX . 'basement');
define( 'GARAGE_TABLE', TABLE_PREFIX . 'garage');
define( 'ADMINS_TABLE', TABLE_PREFIX . 'admins');
define( 'PRIVILEGES_TABLE', TABLE_PREFIX . 'privileges');
define( 'RATINGS_TABLE', TABLE_PREFIX . 'ratings');
define( 'FEATURED_TABLE', TABLE_PREFIX . 'featured');
define( 'FEATURED_AGENTS_TABLE', TABLE_PREFIX . 'featured_agents');
define( 'PACKAGES_TABLE', TABLE_PREFIX . 'packages');
define( 'PACKAGES_AGENT_TABLE', TABLE_PREFIX . 'packages_agents');
define( 'ONLINE_TABLE', TABLE_PREFIX . 'online');
define( 'CRON_TABLE', TABLE_PREFIX . 'cron');
define( 'BANS_TABLE', TABLE_PREFIX . 'ban');
define( 'ALERTS_TABLE', TABLE_PREFIX . 'alerts');
define( 'PAGES_TABLE', TABLE_PREFIX . 'pages');
define( 'ZIP_TABLE', TABLE_PREFIX . 'zip');
define( 'STATUS_TABLE', TABLE_PREFIX . 'status');
define( 'FIELDS_TABLE', TABLE_PREFIX . 'fields');
define( 'VALUES_TABLE', TABLE_PREFIX . 'values');

include 'version.php';

?>