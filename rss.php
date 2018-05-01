<?php

header( 'Content-type: text/xml ');

define( 'PMR', true );

include 'config.php';
include PATH . '/defaults.php';

// RSS header information
echo '<?xml version="1.0" encoding="utf-8" ?>
<rss version="2.0">
<channel>  
<title>' . unsafehtml($conf['website_name_short']) . '</title> 
<link>' . URL . '/rss.php</link> 
<description>' . unsafehtml($conf['website_name']) . '</description>
<pubDate>' . date("D, j M Y G:i:s O") . '</pubDate>
<generator>RealtyScript.com built-in RSS/XML Feed, v.1.1</generator> 
<copyright>RealtyScript.com</copyright> 
<managingEditor>' . $conf['general_e_mail'] . ' (' .unsafehtml($conf['general_e_mail_name']) . ') </managingEditor> 
<webmaster>' . $conf['general_e_mail'] . ' (' .unsafehtml($conf['general_e_mail_name']) . ') </webmaster>
';

// Fetch 10 latest / new listings
$sql = "
SELECT * 
FROM " . PROPERTIES_TABLE  . "
WHERE 
	approved = 1 
ORDER BY listing_id DESC 
LIMIT 10
";
$q = $db->query ( $sql ) or die( mysql_error() );
if ( $db->numrows( $q ) > 0 )
{
	while ( $f = $db->fetcharray( $q ) )
	{
		echo '
		<item>
			<title><![CDATA[' . unsafehtml($f['title']) . ' (' . unsafehtml($conf['currency']) . ' ' . pmr_number_format($f['price']) .')]]></title> 
			<link>' . unsafehtml(URL . '/viewlisting.php?id=' . $f['id']) . '</link> 
			<description><![CDATA[' . removehtml(unsafehtml($f['description'])) . ']]></description> 
			<pubDate>' . date("D, j M Y G:i:s O") . '</pubDate>
			<author>' . $conf['general_e_mail'] . '</author>
			<category>Latest Property Listings</category>
			<guid isPermaLink="true">' . unsafehtml(URL . '/viewlisting.php?id=' . $f['id']) . '</guid>
		</item>
		';
	}
}

// Fetch 10 latest / new agents
$sql = "
SELECT * 
FROM " . USERS_TABLE  . "
WHERE 
	approved = 1 
ORDER BY u_id DESC
LIMIT 10
";
$q = $db->query( $sql ) or die( mysql_error() );
if ( $db->numrows( $q ) > 0 )
{
	while ( $f = $db->fetcharray( $q ) )
	{
		echo '
		<item>
			<title><![CDATA[' . unsafehtml($f['first_name']) . ' ' . unsafehtml($f['last_name']) . ' (' . unsafehtml($f['company_name']) . ')]]></title> 
			<link>' . unsafehtml(URL . '/viewuser.php?id=' . $f['u_id']) . '</link> 
			<description><![CDATA[' . removehtml(unsafehtml($f['description'])) . ']]></description> 
			<pubDate>' . date("D, j M Y G:i:s O") . '</pubDate>
			<author>' . $conf['general_e_mail'] . '</author>
			<category>Latest Sellers</category>
			<guid isPermaLink="true">' . unsafehtml(URL . '/viewuser.php?id=' . $f['u_id']) . '</guid>
		</item>
		';
	}
}

echo '
</channel>
</rss>
';

$db->close();

?>