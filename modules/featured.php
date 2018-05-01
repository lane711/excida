<?php

if ( !defined( 'PMR' ) || (defined( 'PMR' ) && PMR != 'true' ) )
{
	die();
}

$i = 0;

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/featured_listings_header.tpl';
$template = new Template;
$template->load( $tpl );
$template->publish();

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/featured_listings.tpl';

// Title/descr language to use (if available)
$title = str_replace( 'name', 'title', $language_in );
$description = str_replace( 'name', 'description', $language_in );

// Fetch all approved, featured listings
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
$r = $db->query( $sql );
if ( $db->numrows( $q ) > 0 )
{
	while ( $f = $db->fetcharray( $r ) )
	{
		// Check a seller's package, if any, to determine if we can show pictures, address, etc.
		$f_package = package_check( $f['userid'], 'seller' );
	
	    // Starting a new template
	    $template = new Template;
	    $template->load ( $tpl );
		
		$template->set( 'link', generate_link( 'listing', $f ) );
	    
	    if ( $f_package['mainimage'] == 'ON' )
	    {
	    	$images = get_images( 'gallery', $f['listing_id'], 352, 232, 1, 1 );
	    }
	    else
	    {
	    	$images = get_images( 'hidden', $f['listing_id'], 352, 232, 1, 1 );
	    }
	    
	    $template->set( 'image', $images[0] );
	    
	    $template->set( 'mls', $f['mls'] );
	    $template->set( 'title', $f['title'] );
	    $template->set( 'type', getnamebyid ( TYPES_TABLE, $f['type'] ) );
	    $template->set( 'type2', getnamebyid ( TYPES2_TABLE, $f['type2'] ) );
	    $template->set( 'style', getnamebyid ( STYLES_TABLE, $f['style'] ) );
	
	    $description = substr(removehtml(unsafehtml($f['description'])), 0, $conf['search_description']);
	    $description = substr($description, 0, strrpos($description, ' ')) . ' ... ';
	    $template->set( 'description', $description );
	    unset($description);
	    
	    $template->set( 'lot_size', $f['size'] );
	    $template->set( 'dimensions', $f['dimensions'] );
	
		if ($f['bathrooms'] < 1)
			$template->set( 'bathrooms', '-' );
		else
			$template->set( 'bathrooms', $f['bathrooms'] );
		
		$template->set( 'half_bathrooms', $f['half_bathrooms'] );
		
		if ($f['bedrooms'] < 1)
			$template->set( 'bedrooms', '-' );
		else
			$template->set( 'bedrooms', $f['bedrooms'] );
		
		if ($f['garage_cars'] < 1)
			$template->set( 'garage_cars', '-' );
		else
			$template->set( 'garage_cars', $f['garage_cars'] );
		
		if ($f['display_address'] == 'YES' && $f_package['address'] == 'ON' )
		{
			$template->set( 'address1', $f['address1'] );
			$template->set( 'address2', $f['address2'] );
			$template->set( 'zip', $f['zip'] );
		}
		elseif ($f['display_address'] != 'YES' || $f_package['address'] != 'ON' )
		{
			$template->set( 'address1', ' ' );
			$template->set( 'address2', ' ' );
			$template->set( 'zip', ' ' );
		}
		
		$template->set( 'location1', $f['country'] );
		$template->set( 'location2', $f['state'] );
		$template->set( 'location3', $f['city'] );
				
		$template->set( 'price', pmr_number_format($f['price']) );
		$template->set( 'currency', $conf['currency'] );
		$template->set( 'directions', $f['directions'] );
		$template->set( 'year_built', $f['year_built'] );
		$template->set( 'buildings', show_multiple ( BUILDINGS_TABLE, $f['buildings'] ) );
		$template->set( 'appliances', show_multiple ( APPLIANCES_TABLE, $f['appliances'] ) );
		$template->set( 'features', show_multiple ( FEATURES_TABLE, $f['features'] ) );
		$template->set( 'garage', getnamebyid ( GARAGE_TABLE, $f['garage'] ) );
		$template->set( 'basement', getnamebyid ( BASEMENT_TABLE, $f['basement'] ) );
		
		$template->set( 'date_added', printdate($f['date_added']) );
		$template->set( 'date_updated', printdate($f['date_updated']) );
		$template->set( 'date_upgraded', printdate($f['date_upgraded']) );
		
		$template->set( 'ip_added', $f['ip_added'] );
		$template->set( 'ip_updated', $f['ip_updated'] );
		$template->set( 'ip_upgraded', $f['ip_upgraded'] );
		
		$template->set( 'new', newitem ( PROPERTIES_TABLE, $f['listing_id'], $conf['new_days']) );
		$template->set( 'updated', updateditem ( PROPERTIES_TABLE, $f['listing_id'], $conf['updated_days']) );
		$template->set( 'featured', featureditem ( $f['featured'] ) );
		
		$template->set( 'hits', $f['hits'] );
		
		$sql = 'SELECT * FROM ' . USERS_TABLE  . ' WHERE approved = 1 AND id = ' . $f['userid'] . ' LIMIT 1';
		$r_user = $db->query ( $sql ) or error ('Critical Error' , mysql_error());
		$f_user = $db->fetcharray ($r_user);
		
		$template->set( 'view_realtor', '<a href="' . URL . '/viewuser.php?id=' . $f['userid'] . '">' . $f_user['first_name'] . ' ' . $f_user['last_name'] . '</a>');
		
		// Names
		$template->set( '@mls', $lang['Listing_MLS'] );
		$template->set( '@title', $lang['Listing_Title'] );
		$template->set( '@type', $lang['Listing_Property_Type'] );
		$template->set( '@type2', $lang['Module_Listing_Type'] );
		$template->set( '@style', $lang['Listing_Style'] );
		$template->set( '@description', $lang['Listing_Description'] );
		$template->set( '@lot_size', $lang['Listing_Lot_Size'] );
		$template->set( '@dimensions', $lang['Listing_Dimensions'] );
		$template->set( '@bathrooms', $lang['Listing_Bathrooms'] );
		$template->set( '@half_bathrooms', $lang['Listing_Half_Bathrooms'] );
		$template->set( '@bedrooms', $lang['Listing_Bedrooms'] );
		$template->set( '@location', $lang['Search_Location'] );
		$template->set( '@city', $lang['City'] );
		$template->set( '@address1', $lang['Listing_Address1'] );
		$template->set( '@address2', $lang['Listing_Address2'] );
		$template->set( '@zip', $lang['Zip_Code'] );
		$template->set( '@price', $lang['Listing_Price'] );
		$template->set( '@directions', $lang['Listing_Directions'] );
		$template->set( '@year_built', $lang['Listing_Year_Built'] );
		$template->set( '@buildings', $lang['Listing_Additional_Out_Buildings'] );
		$template->set( '@appliances', $lang['Listing_Appliances_Included'] );
		$template->set( '@features', $lang['Listing_Features'] );
		$template->set( '@garage', $lang['Listing_Garage'] );
		$template->set( '@garage_cars', $lang['Listing_Garage_Cars'] );
		$template->set( '@basement', $lang['Listing_Basement'] );
		
		$template->set( '@date_added', $lang['Date_Added'] );
		$template->set( '@date_updated', $lang['Date_Updated'] );
		$template->set( '@date_upgraded', $lang['Date_Upgraded'] );
		
		$template->set( '@hits', $lang['Hits'] );
		
		$template->set( '@view_realtor', $lang['View_Realtor'] );
		
		$template->set( '@image_url', URL . '/templates/' . $cookie_template . '/images' );
		
		$template->set( '@featured', $lang['Listing_Featured_Mark'] );
		
		// Publish template
		$template->publish();
		
		$i++;
		}
}
else
{
	// No listings
}

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/featured_listings_footer.tpl';
$template = new Template;
$template->load ( $tpl );
$template->publish();

?>