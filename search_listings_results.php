<?php

define( 'PMR', true );
$page = 'search';

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'] . ' - ' . $lang['Property_Search'];

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/search_listings_results_header.tpl';
$template = new Template;
$template->load ( $tpl );
$template->set( 'header', $lang['Property_Search'] );
$template->publish();

$search = $_REQUEST;

// Format fields for price range
if ( $search['price_range_purchase_min'] != '' || $search['price_range_purchase_max'] != '' )
{
	$search['price_min'] = $search['price_range_purchase_min'];
	$search['price_max'] = $search['price_range_purchase_max'];
}
elseif ( $search['price_range_rent_min'] != '' || $search['price_range_rent_min'] != '' )
{
	$search['price_min'] = $search['price_range_rent_min'];
	$search['price_max'] = $search['price_range_rent_max'];
}

// Only these specific fields are searchable
$allowed_search_fields = array(
	'userid', 'id', 'mls', 'title', 'type', 'type2', 'style', 'status', 'keyword', 'description', 'zip', 'location1', 'location2', 'location3', 'size', 'dimensions', 'bathrooms', 'half_bathrooms', 'bedrooms', 'address1', 'address2', 'price_min', 'price_max', 'directions', 'year_built', 'garage', 'garage_cars', 'basement', 'image_uploaded', 'custom1', 'custom2', 'custom3', 'custom4', 'custom5', 'custom6', 'custom7', 'custom8', 'custom9', 'custom10', 'features', 'appliances', 'buildings'
);
foreach( $allowed_search_fields AS $key )
{
	if ( $search[$key] != '' )
	{
		// String search is global and searches title, description, locations, MLS ids, listing IDs, etc.
		if ( $key == 'keyword' )
		{
			$whereSQL .= " 
			AND ( 
				p.title LIKE '%" . $db->makeSafe( $search[$key] ) . "%' 
				OR p.description LIKE '%" . $db->makeSafe( $search[$key] ) . "%' 
				OR l1.location_name LIKE '%" . $db->makeSafe( $search[$key] ) . "%' 
				OR l1.location_name LIKE '%" . $db->makeSafe( $search[$key] ) . "%' 
				OR l1.location_name LIKE '%" . $db->makeSafe( $search[$key] ) . "%'
				OR l2.location_name LIKE '%" . $db->makeSafe( $search[$key] ) . "%' 
				OR l2.location_name LIKE '%" . $db->makeSafe( $search[$key] ) . "%' 
				OR l2.location_name LIKE '%" . $db->makeSafe( $search[$key] ) . "%'
				OR l3.location_name LIKE '%" . $db->makeSafe( $search[$key] ) . "%' 
				OR l3.location_name LIKE '%" . $db->makeSafe( $search[$key] ) . "%' 
				OR l3.location_name LIKE '%" . $db->makeSafe( $search[$key] ) . "%'
				OR address1 LIKE '%" . $db->makeSafe( $search[$key] ) . "%'
				OR mls = '" . $db->makeSafe( $search[$key] ) . "'
				OR p.listing_id = '" . $db->makeSafe( $search[$key] ) . "'
			)
			";	
		}
		elseif ( 
			$key == 'title' 
			|| $key == 'description' 
			)
		{
			// Fields that are textual and use a fulltext search (MySQL capability)
			$whereSQL .= " AND MATCH( p.title, p.description ) AGAINST ( '" . $db->makeSafe( $search[$key] ) . "' )";
		}
		// 
		elseif ( $key == 'zip' ) 
		{
			// If we need to find properties within a certain distance
			if ( $search['radius'] != '' && $search['radius'] != 0 )
			{
				$found_zip_codes = get_zips_in_range( $search['zip'], $search['radius'] );
				
				if ( count( $found_zip_codes ) > 0 )
				{
					if ( is_array( $found_zip_codes ) )
					{
						$zip_list = '';
						
						foreach ( $found_zip_codes AS $zip_code => $distance )
						{
							$zip_list .= $zip_code . ", ";
						}
						
						$zip_list = trim( $zip_list, ', ' );
					
						$whereSQL .= " AND " . $key . " IN ( " . $db->makeSafe( $zip_list ) . " )";
					}
				}
			}
		}
		elseif ( 
			$key == 'size' 
			|| $key == 'bathrooms' 
			|| $key == 'half_bathrooms' 
			|| $key == 'bedrooms' 
			|| $key == 'garage_cars' 
			|| $key == 'year_built' 
			|| $key == 'price_min'
			)
		{
			// Fields that can be greater than or equal to the values entered (e.g., 2 or more bathrooms)			
			$search_key = ( $key == 'price_min' ) ? 'price' : $key;
			$whereSQL .= " AND " . $search_key . " >= '" . $db->makeSafe( $search[$key] ) . "'";
		}
		elseif ( $key == 'price_max' )
		{
			// Fields that can be less than or equal to (e.g., maximum price)
			$search_key = ( $key == 'price_max' ) ? 'price' : $key;
			$whereSQL .= " AND " . $search_key . " <= '" . $db->makeSafe( $search[$key] ) . "'";
		}
		elseif ( strpos( $key, 'custom' ) !== false )
		{
			// All custom fields need to be checked to determine what type of custom field it is (textarea, input, select, etc.)
		
			// Check if this is an input field
			$sql = "SELECT type FROM " . FIELDS_TABLE . " WHERE field = '" . $key . "' ";
			$q2 = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
			$f2 = $db->fetcharray( $q2 );
			if ( $f2['type'] == 'input' )
			{      				
				// Make sure we search for {INPUT}value rather than just value
				$search[$key] = '{INPUT}' . $search[$key];	
			}			

			$whereSQL .= " AND " . $key . " = '" . $db->makeSafe( $search[$key] ) . "'";
		}
		elseif ( 
			$key == 'appliances' 
			|| $key == 'buildings'
			|| $key == 'features'
			)
		{
			// Features and other fields that are delimited by a colon (:) or an array
			if ( isset( $search[$key] ) && !empty( $search[$key] ) )
			{
				// If we're passing a list of features via a $_GET request, convert it into an array
				if ( is_array( $search[$key] ) )
				{
					$search_list = $search[$key];
				}
				else
				{
					if ( strstr( $search[$key], ':' ) )
					{
						$search_list = explode( ':', $search[$key] );
					}
				}
				
				foreach( $search_list AS $feature )
				{
					$feature_list .= $feature . ", ";
				}
				
				$feature_list = trim( $feature_list, ', ' );
				
				$whereSQL .= " AND " . $key . " IN (" . $db->makeSafe( $feature_list ) . ")";
			}
		}
		elseif ( $key == 'location1' || $key == 'location2' || $key == 'location3' ) 
		{
			if ( $key == 'location1' )
			{
				$search_key = 'p.location_1';
			}
			elseif ( $key == 'location2' )
			{
				$search_key = 'p.location_2';
			}
			elseif ( $key == 'location3' )
			{
				$search_key = 'p.location_3';
			}
			$whereSQL .= " AND " . $search_key . " = '" . $db->makeSafe( $search[$key] ) . "'";
		}
		else
		{
			// Straight match
			$whereSQL .= " AND " . $key . " = '" . $db->makeSafe( $search[$key] ) . "'";
		}
	}
}

// Title/descr language to use (if available)
$title = str_replace( 'name', 'title', $language_in );
$description = str_replace( 'name', 'description', $language_in );

// Order by
$allowed_order_by = array(
	'p.price', 'p.date_added', 'p.bedrooms', 'p.bathrooms', 'p.featured', 'p.style', 'p.type', 'p.year_built', 'p.hits', 'p.location_1', 'location_2', 'p.location_3', 'p.size', 'p.status', 'p.garage_cars', 'l1.country', 'l2.state', 'l3.city'
);

if ( $search['order_by_type'] != '' && ( $search['order_by_type'] == 'ASC' || $search['order_by_type'] == 'DESC' ) )
{
	$order_by_type = $search['order_by_type'];
}
else
{
	$order_by_type = 'DESC';
}

if ( $search['order_by'] != '' && in_array( $search['order_by'], $allowed_order_by ) ) 
{
	$order_by = $search['order_by'] . ' ' . $order_by_type;
}
else
{
	$order_by = 'p.date_added DESC';
}

// Limit & Pagination
$page = ( $search['page'] != '' ) ? (int)$search['page'] : 1;

if ( $page == 1 )
{
	$limit = '0, ' . $conf['search_results'];
}
else
{
	$prev_page = $page - 1;
	$limit = $prev_page * $conf['search_results'] . ', ' . $conf['search_results'];
}

// Grab the listing data

$sql = "
SELECT
	p." . $title . ", 
	p." . $description . ", 
	p.*,
	l1.location_name AS country,
	l2.location_name AS state,
	l3.location_name AS city,
	users.u_id, users.first_name, users.last_name
FROM " . PROPERTIES_TABLE  . " AS p
LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = p.location_1
LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = p.location_2
LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = p.location_3
LEFT JOIN " . USERS_TABLE . " AS users ON users.u_id = p.userid
WHERE 
	p.approved = 1 
	" . $whereSQL . "
ORDER BY " . $order_by . "
LIMIT " . $limit . "
";
$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
if ( $db->numrows( $q ) > 0 )
{
	while( $f = $db->fetchassoc( $q ) )
	{
		$tpl = PATH . '/templates/' . $cookie_template . '/tpl/search_listings_results.tpl';
		$template = new Template;
		$template->load( $tpl );
		
		// Check a seller's package, if any, to determine if we can show pictures, address, etc.
		$f_package = package_check( $f['u_id'], 'seller' );

		if (favoriteListingsCheck($f['listing_id']))
		{
			$template->set( 'favorites', '<div id="favorites-' . $f['listing_id'] . '" name="favorites-' . $f['listing_id'] . '" style="display: inline"><span style="cursor: pointer; display: block;" class="favorites" onclick="javascript:xajax_favorites_remove(\'' . $f['listing_id'] . '\');">' . $lang['Favorites_Remove'] . '</span></div>');
		}
		else
		{
			$template->set( 'favorites', '<div id="favorites-' . $f['listing_id'] . '" name="favorites-' . $f['listing_id'] . '" style="display: inline"><span style="cursor: pointer; display: block;" class="favorites" onclick="javascript:xajax_favorites(\'' . $f['listing_id'] . '\');">' . $lang['Favorites_Add'] . '</span></div>');
		}
		
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

		$template->set( 'city', $f['city'] );
		$template->set( 'state', $f['state'] );
		$template->set( 'country', $f['country'] );
		
		$template->set( 'mls', $f['mls'] );
		$template->set( 'title', $f['title'] );
		$template->set( 'type', getnamebyid ( TYPES_TABLE, $f['type'] ) );
		$template->set( 'type2', getnamebyid ( TYPES2_TABLE, $f['type2'] ) );
		$template->set( 'style', getnamebyid ( STYLES_TABLE, $f['style'] ) );
		
		$description = substr(removehtml(unsafehtml($f['description'])), 0, $conf['search_description']);
		$description = substr($description, 0, strrpos($description, ' ')) . ' ... ';
		$template->set( 'description', $description );
		
		$template->set( 'lot_size', $f['size'] );
		$template->set( 'dimensions', $f['dimensions'] );
		$template->set( 'bathrooms', $f['bathrooms'] );
		$template->set( 'half_bathrooms', $f['half_bathrooms'] );
		$template->set( 'bedrooms', $f['bedrooms'] );
		$template->set( 'garage_cars', $f['garage_cars'] );
		
		if ($f['display_address'] == 'YES' && $f_package['address'] == 'ON' )
		{
			$template->set( 'address1', $f['address1'] );
			$template->set( 'address2', $f['address2'] );
			$template->set( 'zip', $f['zip'] );
		}
		elseif ($f['display_address'] != 'YES' || $f_package['address'] != 'ON' )
		{
			$template->set( 'address1', '' );
			$template->set( 'address2', '' );
			$template->set( 'zip', '' );
		}
	
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
	    
	    $template->set( 'new', newitem ( PROPERTIES_TABLE, $f['listing_id'], $conf['new_days']) );
	    $template->set( 'updated', updateditem ( PROPERTIES_TABLE, $f['listing_id'], $conf['updated_days']) );
	    $template->set( 'featured', featureditem ( $f['featured'] ) );
	    
	    $template->set( 'hits', $f['hits'] );
	
	    $template->set( 'view_realtor', '<a href="' . URL . '/viewuser.php?id=' . $f['userid'] . '">' . $f['first_name'] . ' ' . $f['last_name'] . '</a>');
	
	    $template->set( '@mls', $lang['Listing_MLS'] );
	    $template->set( '@title', $lang['Listing_Title'] );
	    $template->set( '@type', $lang['Listing_Property_Type'] );
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
	    
		// Publish template
		$template->publish();
		
		$i++;
	}
}
else
{
	$output_message = error( $lang['Error'], $lang['No_Results'], true );
}

// Add map
/*

	$map->setZoomLevel( $conf['map_zoom'] );
	$map->setWidth( $conf['map_width'] );
	$map->setHeight( $conf['map_height'] );
	$map->setBackgroundColor('#d0d0d0');
	$map->setMapDraggable(true);
	$map->setDoubleclickZoom(false);
	$map->setScrollwheelZoom(true);
	
	$map->showDefaultUI(false);
	$map->showMapTypeControl(true, 'DROPDOWN_MENU');
	$map->showNavigationControl(true, 'DEFAULT');
	$map->showScaleControl(true);
	$map->showStreetViewControl(true);
	
	$map->setInfoWindowBehaviour('SINGLE_CLOSE_ON_MAPCLICK');
	$map->setInfoWindowTrigger('CLICK');

	while ($f = $db->fetcharray( $r2 ))
	{
		if (empty($f['latitude']) || empty($f['longitude'])) 
		{
			$map->addMarkerByAddress($f['address1'] . ' ' . $f['address2'] . ' ' . $f33['subsubcategory'] . ' ' . $f22['subcategory'] . ' ' . $f11['category'] . ' ' . $f['zip'], $link, $link);
		} 
		else
		{
		  	$map->addMarker( $f['latitude'], $f['longitude'], $link, $link);
		}
	}

	$map->showMap( false );
	
	echo '</div>';

*/
 
$tpl = PATH . '/templates/' . $cookie_template . '/tpl/search_listings_results_footer.tpl';
$template = new Template;
$template->load( $tpl );

// Pagination
$sql = "
SELECT
	COUNT(*) AS total_results
FROM " . PROPERTIES_TABLE  . " AS p
LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = p.location_1
LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = p.location_2
LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = p.location_3
LEFT JOIN " . USERS_TABLE . " AS users ON users.u_id = p.userid
WHERE 
	p.approved = 1 
	" . $whereSQL . "
";
$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
$f = $db->fetcharray( $q );
$total_results = $f['total_results'];

$custom['pagination'] = pagination( URL . '/search_listings_results.php', $_REQUEST['page'], $total_results, $conf['search_results'] );

$template->set( 'output_message', $output_message );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>