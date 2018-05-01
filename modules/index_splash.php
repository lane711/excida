<?php

if ( !defined( 'PMR' ) || ( defined( 'PMR') && PMR != true ) ) die();

// Title/descr language to use (if available)
$title = str_replace( 'name', 'title', $language_in );
$description = str_replace( 'name', 'description', $language_in );

// Select all featured listings
$sql = "
SELECT
	" . $title . ", 
	" . $description . ", 
	p.*,
	l1.location_name AS country,
	l2.location_name AS state,
	l3.location_name AS city
FROM " . PROPERTIES_TABLE  . " AS p
LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = p.location_1
LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = p.location_2
LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = p.location_3
WHERE 
	approved = 1 
	AND featured = 'A'
ORDER BY RAND()
LIMIT " . $conf['featured_limit'] . "
";
$q = $db->query ( $sql );
$custom['num_listings'] = $db->numrows( $q );
if ( $custom['num_listings'] > 0 )
{
	while( $f = $db->fetcharray( $q ) )
	{
		$tpl = PATH . '/templates/' . $cookie_template . '/tpl/index_splash_loop.tpl';
		$loop_tpl = new Template;
		$loop_tpl->load( $tpl );
		
		// Check a seller's package, if any, to determine if we can show pictures, address, etc.
		$f_package = package_check( $f['userid'], 'seller' );
		
		if ( $custom['num_listings'] > 1 )
		{
			// Same height as the search box
			$images = get_images( 'gallery', $f['listing_id'], 870, 457, 1, 1 );
		}
		else
		{
			// Make the height slightly smaller to accommodate text
			$images = get_images( 'gallery', $f['listing_id'], 870, 400, 1, 1 );
		}
		
		$loop_tpl->set( 'image', $images[0] );
		
		$title = $f['title'];
		$link = generate_link( 'listing', $f );
		
		$loop_tpl->set( 'title', $f['title'] );
		$loop_tpl->set( 'currency', $conf['currency'] );
		$loop_tpl->set( 'link', generate_link( 'listing', $f ) );
		$loop_tpl->set( 'bathrooms', $f['bathrooms'] );
		$loop_tpl->set( 'bedrooms', $f['bedrooms'] );
		
		if ( $f['display_address'] == 'YES' && $f_package['address'] == 'ON' )
		{
			$loop_tpl->set( 'address1', $f['address1'] );
			$loop_tpl->set( 'address2', $f['address2'] );
			$loop_tpl->set( 'zip', $f['zip'] );
			
			$loop_tpl->set( 'location', $f['address1'] ); 
		}
		elseif ( $f['display_address'] != 'YES' || $f_package['address'] = 'OFF' )
		{
			$loop_tpl->set( 'address1', ' ' );
			$loop_tpl->set( 'address2', ' ' );
			$loop_tpl->set( 'zip', ' ' );
			
			$loop_tpl->set( 'location', $lang['View_Listing_Details'] );
		}
		
		$loop_tpl->set( 'price', pmr_number_format( $f['price'] ) );

		// Save this data to be published in the main template
		$featured_listings .= $loop_tpl->publish( true );
	}
}

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/index_splash.tpl';
$template = new Template;
$template->load( $tpl );

// Labels
$template->set( '@type', $lang['Listing_Property_Type'] );
$template->set( '@bathrooms', $lang['Listing_Bathrooms'] );
$template->set( '@bedrooms', $lang['Listing_Bedrooms'] );
$template->set( '@location', $lang['Search_Location'] );
$template->set( '@price', $lang['Listing_Price'] );
$template->set( '@search', $lang['Menu_Search'] );
$template->set( 'select', $lang['Select'] );

// Values
$template->set( 'featured_listings', $featured_listings );
$template->set( 'location1', get_locations() );
$template->set( 'link', $link );
$template->set( 'title', $title );

// Publish template
$template->publish();

?>