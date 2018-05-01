<?php

define( 'PMR', true );

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'] . ' - ' . $lang['Edit_Listings'];

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/viewuserlistings_header.tpl';
$template = new Template;
$template->load ( $tpl );

$template->set( 'list_text', $list_text );

$template->set( 'header', $lang['Edit_Listings']  );
$template->set( '@add_listing', $lang['Menu_Submit_Property'] );
$template->set( '@control_panel', $lang['Menu_User_Login'] );
$template->set( '@edit_listings', $lang['Edit_Listings'] );

$template->publish();

// Authenticate user
if ( auth_check( $session->fetch( 'login' ), $session->fetch( 'password' ) ) )
{
	// Fetching the user data 
	$sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE login = "' . $session->fetch('login') . '" LIMIT 1';
	$res = $db->query( $sql );
	$f_res = $db->fetcharray( $res );
	
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
	$page = ( $_REQUEST['page'] != '' ) ? (int)$_REQUEST['page'] : 1;
	
	if ( $page == 1 )
	{
		$limit = '0, ' . $conf['search_results'];
	}
	else
	{
		$prev_page = $page - 1;
		$limit = $prev_page * $conf['search_results'] . ', ' . $conf['search_results'];
	}

	// Title/descr language to use (if available)
	$title = str_replace( 'name', 'title', $language_in );
	$description = str_replace( 'name', 'description', $language_in );

	// Grab the listing data
	$sql = "
	SELECT
		p." . $title . ", 
		p." . $description . ", 
		p.*,
		l1.location_name AS country,
		l2.location_name AS state,
		l3.location_name AS city,
		users.*
	FROM " . PROPERTIES_TABLE  . " AS p
	LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = p.location_1
	LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = p.location_2
	LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = p.location_3
	LEFT JOIN " . USERS_TABLE . " AS users ON users.u_id = p.userid
	WHERE 
		users.u_id = '" . $db->makeSafe( $f_res['u_id'] ) . "'
		" . $whereSQL . "
	ORDER BY " . $order_by . "
	LIMIT " . $limit . "
	";
	$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	if ( $db->numrows( $q ) > 0 )
	{
		while( $f = $db->fetchassoc( $q ) )
		{
			$tpl = PATH . '/templates/' . $cookie_template . '/tpl/viewuserlistings.tpl';
			$template = new Template;
			$template->load( $tpl );

			$template->set( 'link', URL . '/edituserlistings.php?listing_id=' . $f['listing_id'] );
			
			$images = get_images( 'gallery', $f['listing_id'], 352, 232, 1, 1 );
			$template->set( 'image', $images[0] );

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
			
			if ( $f['address1'] == '' )
			{
				$f['address1'] = 'N/A';
			}
			
			$template->set( 'address1', $f['address1'] );
			$template->set( 'address2', $f['address2'] );
			$template->set( 'zip', $f['zip'] );
			$template->set( 'city', $f['city'] );
			$template->set( 'state', $f['state'] );
			
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
			$template->set( 'hits', $f['hits'] );
			$template->set( 'new', newitem ( PROPERTIES_TABLE, $f['id'], $conf['new_days']) );
			$template->set( 'updated', updateditem ( PROPERTIES_TABLE, $f['id'], $conf['updated_days']) );
			$template->set( 'featured', featureditem ( $f['featured'] ) );
			
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
		    $template->set( '@location', $lang['Location'] );
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
		}
	}
}
else
{
	$output_message = error( $lang['Error'], $lang['Not_Logged_In'], true );
	$listing_form = '';
	$list_text = '';
}

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/viewuserlistings_footer.tpl';
$template = new Template;
$template->load ( $tpl );

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
	users.u_id = '" . $db->makeSafe( $f_res['u_id'] ) . "'
	" . $whereSQL . "
";
$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
$f = $db->fetcharray( $q );
$total_results = $f['total_results'];

$custom['pagination'] = pagination( URL . '/viewuserlistings.php', $_REQUEST['page'], $total_results, $conf['search_results'] );

$template->set( 'output_message', $output_message );

$template->publish();

// Template footer
include ( PATH . '/templates/' . $cookie_template . '/footer.php' );

?>