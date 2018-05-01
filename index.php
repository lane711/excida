<?php

define( 'PMR', true );

include 'config.php';

if ( $_REQUEST['version'] != '' )
{
	die( VERSION . '-' . BUILD );
}

if ( $page != 'install' )
{
	// Check if constants are defined
	$config_fields = array(
		'URL', 'LICENSE', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD', 'PATH'
	);
	if ( is_array( $config_fields ) )
	{
		foreach( $config_fields AS $constant )
		{
			if ( !defined( $constant ) )
			{
				header( 'Location: install/' );
				exit();
			}	
		}
	}

	// Check if constants have values
	$config_fields = array(
		URL, LICENSE, DB_NAME, DB_USERNAME, DB_PASSWORD, PATH
	);
	if ( is_array( $config_fields ) )
	{
		foreach( $config_fields AS $constant )
		{
			if ( $constant == '' )
			{
				header( 'Location: install/' );
				exit();
			}	
		}
	}
}

include PATH . '/defaults.php';

// Check the installation folders and ask to remove those if exist
if ( file_exists( PATH . '/install/index.php' ) )
{
	die( 'Initialization Error: The /install folder must be removed after you have done the install.' );
}

if ( file_exists( PATH . '/docs/version' ) )
{
	die( 'Initialization Error: The /docs folder must be removed after you have done the install or upgrade.'  );
}

// Title tag content
$title = $conf['website_name_short'];

// Template header
include PATH . '/templates/' . $cookie_template . '/header.php';

// Include index content
include PATH . '/templates/' . $cookie_template . '/tpl/index.tpl';

// Include cron jobs
include PATH . '/includes/functions/cron.php';

// Template footer
include PATH . '/templates/' . $cookie_template . '/footer.php';

?>