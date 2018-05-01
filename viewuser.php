<?php

define( 'PMR', true );

include 'config.php';
include PATH . '/defaults.php';

// Check that this user ID exists
if ( preg_match( '/^[0-9]+$/', $_REQUEST['id'] ) )
{
	$sql = "
	SELECT
		u.*, 
		l1.location_name AS country,
		l2.location_name AS state,
		l3.location_name AS city
	FROM " . USERS_TABLE . " u
	LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = u.location_1
	LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = u.location_2
	LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = u.location_3
	WHERE
		u_id = '" . $db->makeSafe( $_REQUEST['id'] ) . "'
	";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		$f = $db->fetcharray( $q );

		$title = $conf['website_name_short'] . ' - ' . $f['first_name'] . ' ' . $f['last_name'];
		
		$meta_title = $conf['website_name_short'] . ' - ' . $f['first_name'] . ' ' . $f['last_name'];
		$meta_description = removehtml(unsafehtml_xml($f['description']));
		$meta_description = trim(substr($meta_description, 0, 160));
	}
}

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/viewuser.tpl';
$template = new Template;
$template->load ( $tpl );

// Rating a profile
if ( $_REQUEST['vote'] != '' && $_REQUEST['id'] != '' )
{
	$_REQUEST['vote'] = (int)$_REQUEST['vote'];

	if ( is_int( $_REQUEST['vote'] ) )
	{
		// Make sure this user hasn't voted
		$sql = "
		SELECT id 
		FROM " . RATINGS_TABLE . " 
		WHERE 
			ip = '" . $db->makeSafe( $_SERVER['REMOTE_ADDR'] ) . "'
			AND userid = '" . $db->makeSafe( $_REQUEST['id'] ) . "'
		";
		$q2 = $db->query( $sql );
		if ( $db->numrows( $q2 ) == 0 )
		{
			// Record this vote
			$sql = "
			INSERT INTO " . RATINGS_TABLE . "
			( 
				ip, 
				userid, 
				rating, 
				date
			) 
			VALUES 
			( 
				'" . $db->makeSafe( $_SERVER['REMOTE_ADDR'] ) . "',
				'" . $db->makeSafe( $_REQUEST['id'] ) . "',
				'" . $db->makeSafe( $_REQUEST['vote'] ) . "',
				'" . date( 'Y-m-d' ) . "'
			)
			";
			$q2 = $db->query( $sql );
			
			// Update the user's rating
			$sql = "
			UPDATE " . USERS_TABLE . " 
			SET 
				rating = rating + " . $db->makeSafe( $_REQUEST['vote'] ) . ", 
				votes = votes + 1 
			WHERE 
				u_id = '" . $db->makeSafe( $_REQUEST['id'] ) . "'
			";
			$q2 = $db->query( $sql );
		}
	}
}

// Check that this user ID exists
if ( preg_match( '/^[0-9]+$/', $_REQUEST['id'] ) )
{
	$sql = "
	SELECT
		u.*, 
		l1.location_name AS country,
		l2.location_name AS state,
		l3.location_name AS city
	FROM " . USERS_TABLE . " u
	LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = u.location_1
	LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = u.location_2
	LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = u.location_3
	WHERE
		u_id = '" . $db->makeSafe( $_REQUEST['id'] ) . "'
	";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		$custom['show_profile'] = true;
		$custom['show_profile_listings'] = true;
	
		$f = $db->fetchassoc( $q );
		
		// Update hit counter
		if ( $session->fetch( 'hits_timeout_' . $_REQUEST['id'] ) <= time() )
		{
			// Update hit acount
			$sql = "
			UPDATE " . USERS_TABLE  . "
			SET 
				hits = hits + 1
			WHERE 
				u_id = '" . $db->makeSafe( $_REQUEST['id'] ) . "'
			";
			$q2 = $db->query( $sql );
			
			// Set a new timeout
			$timeout = time() + ( 60*60*24 );
			$session->set( 'hits_timeout_' . $_REQUEST['id'], $timeout );
		}
   
		// Look up what package they have so we know what features to allow
		$f_package = package_check( $f['u_id'], 'seller' );
  
		// If anyone can view seller details, registered users only, or paid users only
		if ( $conf['contact_agents'] == '2' && check_logged_in() == false )
		{
			$custom['show_seller_details'] = false;
			
			$template->set( 'seller_details_restriction', $lang['Agent_Details_Account_Only'] );
		}
		elseif ( $conf['contact_agents'] == '3' && check_paid_account() == false )
		{
			$custom['show_seller_details'] = false;
			
			$template->set( 'seller_details_restriction', $lang['Agent_Details_Pay_Only'] );
		}
		else
		{
			$custom['show_seller_details'] = true;
		}

	    if ( $f_package['photo'] == 'ON' )
	    {
	    	$seller_images = get_images( 'photos', $f['u_id'], 200, 140, 1, 1 );
	    }
	    else
	    {
	    	$seller_images = get_images( 'hidden', $f['u_id'], 200, 140, 1, 1 );
	    }
	    
	    $template->set( 'photo', $images[0] );
		
		if ( $f_package['address'] == 'ON' )
		{
			$address = $f['address'];
			$city = $f['city'];
			$state = $f['state'];
			$country = $f['country'];
			$zip = $f['zip'];
		}
		
		if ( $f_package['phone'] == 'ON' )
		{
			$phone = $f['phone'];
			$fax = $f['fax'];
			$mobile = $f['mobile'];
		}
		
		$template->set( 'send_message', URL . '/sendmessage.php?u_id=' . $f['u_id'] );

		// Show their listings
		
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
		
		// Get a list of this user's listings
		$sql = "
		SELECT
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
			p.approved = '1'
			AND users.u_id = '" . $db->makeSafe( $f['u_id'] ) . "'
		ORDER BY " . $order_by . "
		LIMIT " . $limit . "
		";
		$q2 = $db->query( $sql );
		if ( $db->numrows( $q2 ) > 0 )
		{
			while( $f2 = $db->fetchassoc( $q2 ) )
			{
				$tpl = PATH . '/templates/' . $cookie_template . '/tpl/viewuser_listings.tpl';
				$template_listings = new Template;
				$template_listings->load( $tpl );
			
				$template_listings->set( 'link', URL . '/viewlisting.php?id=' . $f2['listing_id'] );
				
				$images = get_images( 'gallery', $f2['listing_id'], 352, 232, 1, 1 );
				$template_listings->set( 'image', $images[0] );
				
				$template_listings->set( 'mls', $f2['mls'] );
				$template_listings->set( 'title', $f2['title'] );
				$template_listings->set( 'type', getnamebyid ( TYPES_TABLE, $f2['type'] ) );
				$template_listings->set( 'type2', getnamebyid ( TYPES2_TABLE, $f2['type2'] ) );
				$template_listings->set( 'style', getnamebyid ( STYLES_TABLE, $f2['style'] ) );
				
				$description = substr( $f2['description'], 0, $conf['search_description'] );
				$description = substr($description, 0, strrpos($description, ' ')) . ' ... ';
				$template_listings->set( 'description', $description );
				$template_listings->set( 'lot_size', $f2['size'] );
				$template_listings->set( 'dimensions', $f2['dimensions'] );
			
				$template_listings->set( 'bathrooms', $f2['bathrooms'] );
				$template_listings->set( 'half_bathrooms', $f2['half_bathrooms'] );
				$template_listings->set( 'bedrooms', $f2['bedrooms'] );
				$template_listings->set( 'garage_cars', $f2['garage_cars'] );
				
				$template_listings->set( 'address1', $f2['address1'] );
				$template_listings->set( 'address2', $f2['address2'] );
				$template_listings->set( 'zip', $f2['zip'] );
				$template_listings->set( 'city', $f2['city'] );
				$template_listings->set( 'state', $f2['state'] );
				
				$template_listings->set( 'price', pmr_number_format($f2['price']) );
				$template_listings->set( 'currency', $conf['currency'] );
				$template_listings->set( 'directions', $f2['directions'] );
				$template_listings->set( 'year_built', $f2['year_built'] );
				$template_listings->set( 'buildings', show_multiple ( BUILDINGS_TABLE, $f2['buildings'] ) );
				$template_listings->set( 'appliances', show_multiple ( APPLIANCES_TABLE, $f2['appliances'] ) );
				$template_listings->set( 'features', show_multiple ( FEATURES_TABLE, $f2['features'] ) );
				$template_listings->set( 'garage', getnamebyid ( GARAGE_TABLE, $f2['garage'] ) );
				$template_listings->set( 'basement', getnamebyid ( BASEMENT_TABLE, $f2['basement'] ) );
				$template_listings->set( 'date_added', printdate($f2['date_added']) );
				$template_listings->set( 'date_updated', printdate($f2['date_updated']) );
				$template_listings->set( 'date_upgraded', printdate($f2['date_upgraded']) );
				$template_listings->set( 'ip_added', $f2['ip_added'] );
				$template_listings->set( 'ip_updated', $f2['ip_updated'] );
				$template_listings->set( 'ip_upgraded', $f2['ip_upgraded'] );
				$template_listings->set( 'hits', $f2['hits'] );
				$template_listings->set( 'new', newitem ( PROPERTIES_TABLE, $f2['id'], $conf['new_days']) );
				$template_listings->set( 'updated', updateditem ( PROPERTIES_TABLE, $f2['id'], $conf['updated_days']) );
				$template_listings->set( 'featured', featureditem ( $f2['featured'] ) );
				
			    // Names
			    $template_listings->set( '@mls', $lang['Listing_MLS'] );
			    $template_listings->set( '@title', $lang['Listing_Title'] );
			    $template_listings->set( '@type', $lang['Listing_Property_Type'] );
			    $template_listings->set( '@type2', $lang['Module_Listing_Type'] );
			    $template_listings->set( '@style', $lang['Listing_Style'] );
			    $template_listings->set( '@description', $lang['Listing_Description'] );
			    $template_listings->set( '@lot_size', $lang['Listing_Lot_Size'] );
			    $template_listings->set( '@dimensions', $lang['Listing_Dimensions'] );
			    $template_listings->set( '@bathrooms', $lang['Listing_Bathrooms'] );
			    $template_listings->set( '@half_bathrooms', $lang['Listing_Half_Bathrooms'] );
			    $template_listings->set( '@bedrooms', $lang['Listing_Bedrooms'] );
			    $template_listings->set( '@location', $lang['Location'] );
			    $template_listings->set( '@city', $lang['City'] );
			    $template_listings->set( '@address1', $lang['Listing_Address1'] );
			    $template_listings->set( '@address2', $lang['Listing_Address2'] );
			    $template_listings->set( '@zip', $lang['Zip_Code'] );
			    $template_listings->set( '@price', $lang['Listing_Price'] );
			    $template_listings->set( '@directions', $lang['Listing_Directions'] );
			    $template_listings->set( '@year_built', $lang['Listing_Year_Built'] );
			    $template_listings->set( '@buildings', $lang['Listing_Additional_Out_Buildings'] );
			    $template_listings->set( '@appliances', $lang['Listing_Appliances_Included'] );
			    $template_listings->set( '@features', $lang['Listing_Features'] );
			    $template_listings->set( '@garage', $lang['Listing_Garage'] );
			    $template_listings->set( '@garage_cars', $lang['Listing_Garage_Cars'] );
			    $template_listings->set( '@basement', $lang['Listing_Basement'] );
			    $template_listings->set( '@date_added', $lang['Date_Added'] );
			    $template_listings->set( '@date_updated', $lang['Date_Updated'] );
			    $template_listings->set( '@date_upgraded', $lang['Date_Upgraded'] );
			    $template_listings->set( '@hits', $lang['Hits'] );
			    $template_listings->set( '@view_realtor', $lang['View_Realtor'] );
			    $template_listings->set( '@image_url', URL . '/templates/' . $cookie_template . '/images' );
			
				// Publish template
				$user_listings .= $template_listings->publish( true );
			}
		}
		else
		{
			$user_listings = error( $lang['Error'], $lang['No_Results'], true );
		}
		
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
			p.approved = '1'
			AND users.u_id = '" . $db->makeSafe( $f['u_id'] ) . "'
		";
		$q2 = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		$f2 = $db->fetcharray( $q2 );
		$total_results = $f2['total_results'];
		
		$custom['pagination'] = pagination( URL . '/viewuser.php', $_REQUEST['page'], $total_results, $conf['search_results'] );
	}
	else
	{
		$output_message = error( $lang['Error'], $lang['No_Seller'], true );
	}
}
else
{
	$output_message = error( $lang['Error'], $lang['No_Seller'], true );
}

// Language-specific values
$template->set( '@heading', $f['first_name'] . ' ' . $f['last_name'] );
$template->set( '@heading2', $lang['Realtor_Listings'] );
$template->set( 'output_message', $output_message );

// Labels
$template->set( '@firstname', $lang['Realtor_First_Name'] );
$template->set( '@lastname', $lang['Realtor_Last_Name'] );
$template->set( '@company', $lang['Realtor_Company_Name'] );
$template->set( '@description', $lang['Realtor_Description'] );
$template->set( '@address', $lang['Realtor_Address'] );
$template->set( '@phone', $lang['Realtor_Phone'] );
$template->set( '@fax', $lang['Realtor_Fax'] );
$template->set( '@url', $lang['Realtor_Website'] );
$template->set( '@updated', $lang['Listing_Updated_Date'] );
$template->set( '@added', $lang['Listing_Added_Date'] );
$template->set( '@hits', $lang['Hits'] );
$template->set( 'rate_listing', $lang['Rate_This_Listing'] );
$template->set( 'vote_5', $lang['Realtor_Vote_5'] );
$template->set( 'vote_4', $lang['Realtor_Vote_4'] );
$template->set( 'vote_3', $lang['Realtor_Vote_3'] );
$template->set( 'vote_2', $lang['Realtor_Vote_2'] );
$template->set( 'vote_1', $lang['Realtor_Vote_1'] );
$template->set( '@send_message', $lang['Realtor_Send_Message'] );

// Values
$template->set( 'total_listings', $total_listings );
$template->set( 'firstname', $f['firstname'] );
$template->set( 'lastname', $f['lastname'] );
$template->set( 'company_name', $f['company_name'] );
$template->set( 'description', unsafehtml( $f['description'] ) );
$template->set( 'address', $address );
$template->set( 'fax', $fax );
$template->set( 'url', $f['website'] );
$template->set( 'phone', $phone );
$template->set( 'city', $city );
$template->set( 'state', $state );
$template->set( 'country', $country );
$template->set( 'mobile', $mobile );
$template->set( 'zip', $zip );
$template->set( 'show_image', $seller_images[0] );
$template->set( 'hits', $f['hits'] );
$template->set( 'rating', rating( $f['rating'], $f['votes'] ) );
$template->set( 'user_listings', $user_listings );
$template->set( 'updated', printdate( $f['date_updated'] ) );
$template->set( 'added', printdate( $f['date_added'] ) );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>