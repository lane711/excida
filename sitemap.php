<?php

define( 'PMR', true );

$page = 'sitemap';

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'];

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/sitemap_header.tpl';
$template = new Template;
$template->load ( $tpl );
$template->set( 'header', $lang['Menu_Site_Map'] );
$template->publish();

$title = str_replace( 'name', 'title', $language_in );
$description = str_replace( 'name', 'description', $language_in );

// Get all listings
$sql = '
SELECT 
	' . $title . ', 
	' . $description . ', 
	' . PROPERTIES_TABLE . '.*
FROM ' . PROPERTIES_TABLE  . '  
ORDER BY title ASC
';
$q = $db->query( $sql );
if ( $db->numrows( $q ) > 0 )
{
	while( $f = $db->fetcharray( $q ) )
	{
		$tpl = PATH . '/templates/' . $cookie_template . '/tpl/sitemap.tpl';
		$template = new Template;
		$template->load ( $tpl );	
	
		// Default
		if ( $f[0] == '' )
		{
			$f['title'] = $f['title'];
		}
		else
		{
			$f['title'] = $f[0];
		}
		
		if ( $f[1] == '' )
		{
			$f['description'] = $f['description'];
		}
		else
		{
			$f['description'] = $f[1];
		}

		$template->set( 'link', generate_link( 'listing', $f ) );
		$template->set( 'name', $f['title'] );
		
		$template->publish();
	}
}

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/sitemap_footer.tpl';
$template = new Template;
$template->load ( $tpl );
$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>