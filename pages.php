<?php

define( 'PMR', 'true' );

include 'config.php';
include PATH . '/defaults.php';

if ( preg_match('/[0-9]+/', $_GET['id']) || ( preg_match('/[0-9A-Z\-\_]+/i', $_GET['string'] ) ) )
{
	// Language to show article in
	$menu2 = str_replace( 'name', 'menu', $language_in );
	$text2 = str_replace( 'name', 'text', $language_in );

	if ($menu2 == '')
	{
		$menu2 = 'menu';
	}

	if ($text2 == '')
	{
		$text2 = 'text';
	}

	if ($_GET['id'] != '')
	{
		$whereSQL = ' WHERE id = "' . safehtml($_GET['id']) . '" ';
	}
	elseif ($_GET['string'] != '')
	{
		$whereSQL = ' WHERE string = "' . safehtml($_GET['string']) . '" ';
	}

	$sql = "
	SELECT " . $menu2 . ", " . $text2 . ", id, status, string, date, menu, text 
	FROM " . PAGES_TABLE . "
	" . $whereSQL . "
	";
	$r_page = $db->query( $sql ) or error ( 'Critical Error', mysql_error () );
	$f_page = $db->fetcharray( $r_page );

	// Default
	if ($f_page[0] == '')
	{
		$f_page[0] = $f_page[6];
	}

	if ($f_page[1] == '')
	{
		$f_page[1] = $f_page[7];
	}

	// Title tag content
	$title = $conf['website_name_short'] . ' - ' . $f_page[0];

	// Template header
	include PATH . '/templates/' . $cookie_template . '/header.php';

	if ( $f_page['status'] == 0 )
	{
		$content = $lang['CMS_View_Error'];
	}
	else
	{
		$content = unsafehtml($f_page[1]);
	}

	$tpl = PATH . '/templates/' . $cookie_template . '/tpl/pages.tpl';
	$template = new Template;
	$template->load ( $tpl );

	$template->set( 'title', $f_page[0] );
	$template->set( 'content', $content );
	
	$template->publish();
}

// Template footer
include PATH . '/templates/' . $cookie_template . '/footer.php';

?>