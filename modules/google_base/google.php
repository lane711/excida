<?php

define( 'PMR', true );

include '../../config.php';
include PATH . '/defaults.php';

$in = '
<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" xmlns:c="http://base.google.com/cns/1.0"> 
<channel>  
<title>' . $conf['website_name_short'] . '</title> 
<description>' . $conf['website_name'] . '</description>
<link>' . URL . '/google.xml</link>
';

$sql = '
SELECT 
	' . PROPERTIES_TABLE . '.*, 
	company_name, 
	first_name, 
	last_name 
FROM ' . PROPERTIES_TABLE  . ' 
LEFT JOIN 
	' . USERS_TABLE . ' ON ' . PROPERTIES_TABLE . '.userid = ' . USERS_TABLE . '.id 
WHERE 
	' . PROPERTIES_TABLE . '.approved = 1 
ORDER BY title
';
$r = $db->query ( $sql );
if ( $db->numrows( $r ) > 0 )
{
	while( $f = $db->fetcharray( $r ) )
	{
		if( file_exists( PATH . '/images/' . $f['id'] . '.jpg') )
		{
			$image = URL . '/images/' . $f['id'] . '.jpg';
		}
		else
		{
			$image = '';
		}
		
		$in .= '
		<item>
			<g:bathrooms>' . $f['bathrooms'] . '</g:bathrooms>
			<g:bedrooms>' . $f['bedrooms'] . '</g:bedrooms>
			<g:brokerage_company>' . removehtml(unsafehtml_xml($f['company_name '])) . '</g:brokerage_company>
			<g:broker_name>' . removehtml(unsafehtml_xml($f['first_name'])) . ' ' . removehtml(unsafehtml_xml($f['last_name'])) . '</g:broker_name>
			<description>' . removehtml(unsafehtml_xml($f['description'])) . '</description> 
			<guid>' . $f['id'] . '</guid>
			<g:image_link>' . $image . '</g:image_link>' . "\n";
			
			$sql3 = 'SELECT * FROM ' . GALLERY_TABLE . ' WHERE listingid = "' .  $f['id'] . '" ORDER BY id';
			$r_gallery = $db->query( $sql3 ) or error ('Critical Error', mysql_error  ());
			if ($db->numrows($r_gallery) > 0)
			{
				while ($f_gallery = $db->fetcharray($r_gallery))
				{
					$in .= '   <g:image_link>' . URL . '/gallery/' . $f_gallery['id'] . '.jpg</g:image_link>' . "\n";
				}
			}
			
			$in .= '
			<link>' . URL . '/viewlisting.php?id=' . $f['id'] . '</link>
			<g:listing_status>active</g:listing_status>
			<g:listing_type>' . getnamebyid(TYPES2_TABLE, $f['type2'] ) . '</g:listing_type>
			<g:location>' . $f['address1'] . ' ' . $f['address2'] . ', ' . $f11['category'] . ', ' . $f22['subcategory'] . ', ' . $f33['subsubcategory'] . ', ' . $f['zip'] . '</g:location>
			<g:mls_listing_id>' . $f['mls'] . '</g:mls_listing_id>
			<g:mls_name>CTMLS</g:mls_name>
			<g:price>' . number_format($f['price'], 0, '.', ',') . '</g:price>
			<g:currency>' . $conf['currency'] . '</g:currency>
			<g:property_type>' . getnamebyid ( TYPES_TABLE, $f['type'] ) . '</g:property_type>
			<g:provider_class>broker</g:provider_class>
			<g:square_footage>' . $f['size'] . '</g:square_footage>
			<g:year>' . $f['year_built'] . '</g:year>
			<title>' . $f['title'] . '</title> 
		</item>
		';
	}
}

$in .= '
</channel>
</rss>
';

$filename = 'google.xml';
$handle = fopen( $filename, 'w+' );
fwrite( $handle, unsafehtml_xml( $in ) );
fclose( $handle);

$db->close();

?>