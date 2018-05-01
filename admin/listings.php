<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include '../config.php';
include PATH . '/defaults.php';

$title = $lang['Property_Search'];

include PATH . '/admin/template/header.php';

if ( adminAuth( $session->fetch( 'adminlogin'), $session->fetch( 'adminpassword' ) ) )
{
	include PATH . '/admin/navigation.php';

	adminPermissionsCheck( 'manage_listings', $session->fetch( 'adminlogin' ) ) or error( 'Critical Error', 'Incorrect privileges' );
	$site_where_clause = "";
	if($session->fetch('role')=="SUPERUSER")
	$site_where_clause =" AND u.site_id=".$session->fetch('site_id');
	// Approve listing
	if ( $_GET['req'] == 'approve' && $_GET['listing_id'] != '' )
	{
		echo table_header( $lang['Information'] );
	
		// Fetch the data for the listing
		$sql = "
		SELECT p.*
		FROM " . PROPERTIES_TABLE . " AS p, ".USERS_TABLE." AS u
		WHERE 
			listing_id = '" . $db->makeSafe( $_GET['listing_id'] ) . "'".$site_where_clause;
		$r = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		if ( $db->numrows( $r ) > 0 )
		{
			$f = $db->fetcharray($r);
	
			// If this listing was not approved yet we start
			if ( $f['approved'] != 1 )
			{
				// Approve the listing
				$sql = "
				UPDATE " . PROPERTIES_TABLE . "
				SET 
					date_approved = NOW(), 
					approved = '1'
				WHERE 
					listing_id = '" . $db->makeSafe( $_GET['listing_id'] ) . "'
				LIMIT 1
				";  
				$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
				
				// Let the user know this was approved
				$sql = "
				SELECT * 
				FROM " . USERS_TABLE  . "
				WHERE u_id = '" . $f['userid'] . "'
				LIMIT 1
				";
				$r_user = $db->query( $sql ) or error( 'Critical Error' , mysql_error() );
				if ( $db->numrows( $r_user ) > 0 )
				{
					$f_user = $db->fetcharray( $r_user );
					
					$lang['User_Listing_Notification_Subject'] = str_replace( '{website_name}', $conf['website_name'], $lang['User_Listing_Notification_Subject'] );
		
					$lang['User_Listing_Notification_Mail'] = str_replace( '{website_name}', $conf['website_name'], $lang['User_Listing_Notification_Mail'] );
					$lang['User_Listing_Notification_Mail'] = str_replace( '{title}', $f['title'], $lang['User_Listing_Notification_Mail'] );
					
					send_mailing( 
						$conf['general_e_mail'], 
						$conf['general_e_mail_name'], 
						$f_user['email'], 
						$lang['User_Listing_Notification_Subject'], 
						$lang['User_Listing_Notification_Mail']
					);
				}
						
				// Listing alerts now that this listing is approved
				check_alerts( $_GET['listing_id'], $f );
			}
		}
		
		echo $lang['Admin_Listing_Approved'];
		
		echo table_footer();
	}
	
	// Remove listing
	if ( $_GET['req'] == 'remove' && $_GET['listing_id'] != '' )
	{
		echo table_header( $lang['Information'] );
		
		// Grab the details of this listing
		$sql = "
		SELECT p.approved AS listing_approved, u.approved AS user_approved, type, userid, email
		FROM " . PROPERTIES_TABLE . " p
		LEFT JOIN " . USERS_TABLE . " u ON u.u_id = p.userid 
		WHERE listing_id = '" . $db->makeSafe( $_GET['listing_id'] ) . "'".$site_where_clause;
		$r = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		if ( $db->numrows( $r ) > 0 )
		{
			$f = $db->fetcharray( $r );
			
			removeuserlisting( $_GET['listing_id'] );
			
			echo $lang['Admin_Listing_Removed'];
			
			$lang['User_Rejected_Notification_Subject'] = str_replace( '{website_name}', $conf['website_name'], $lang['User_Rejected_Notification_Subject'] );
			
			$lang['User_Rejected_Notification_Mail'] = str_replace( '{website_name}', $conf['website_name'], $lang['User_Rejected_Notification_Mail'] );
			
			send_mailing( 
				$conf['general_e_mail'], 
				$conf['general_e_mail_name'], 
				$f['email'], 
				$lang['User_Rejected_Notification_Subject'], 
				$lang['User_Rejected_Notification_Mail']
			);
		}
		echo table_footer();		
	}
	
	echo table_header( $lang['Property_Search'] );
	
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
		'userid', 'id', 'mls', 'title', 'type', 'type2', 'style', 'status', 'keyword', 'description', 'zip', 'location1', 'location2', 'location3', 'size', 'dimensions', 'bathrooms', 'half_bathrooms', 'bedrooms', 'address1', 'address2', 'price_min', 'price_max', 'directions', 'year_built', 'garage', 'garage_cars', 'basement', 'image_uploaded', 'custom1', 'custom2', 'custom3', 'custom4', 'custom5', 'custom6', 'custom7', 'custom8', 'custom9', 'custom10', 'features', 'appliances', 'buildings', 'listing_approved', 'listing_expired', 'listing_updated', 'listing_id'
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
					OR l1.location_name '%" . $db->makeSafe( $search[$key] ) . "%'
					OR address1 LIKE '%" . $db->makeSafe( $search[$key] ) . "%'
					OR mls = '" . $db->makeSafe( $search[$key] ) . "'
					OR id = '" . $db->makeSafe( $search[$key] ) . "'
				)
				";	
			}
			elseif ( $key == 'listing_approved' )
			{
				$whereSQL .= " AND p.approved = '0' ";
			}
			elseif ( $key == 'listing_expired' )
			{
				$whereSQL .= " AND p.approved = '2' ";	
			}
			elseif ( $key == 'listing_updated' && $search['listing_updated_days'] != '' )
			{
				$whereSQL .= " AND p.date_updated BETWEEN '" . date( 'Y-m-d', strtotime( 'now -' . $search['listing_updated_days'] . ' days' ) ) . "' AND '" . date( 'Y-m-d' ) . "' ";
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
	if($session->fetch('role')=="SUPERUSER")
	$whereSQL.=" AND users.site_id=".$session->fetch('site_id');
	// Grab the listing data
	$sql = "
	SELECT
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
		1 = 1
		" . $whereSQL . "
	ORDER BY " . $order_by . "
	LIMIT " . $limit . "
	";
	$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	if ( $db->numrows( $q ) > 0 )
	{
		while ( $f = $db->fetcharray( $q ) )
		{
			$tpl = PATH . '/admin/template/tpl/listing-short.tpl';
			$template = new Template;
			$template->load ( $tpl );
			
			if ( ( $f['approved'] == 0 || $f['approved'] == 2 ) && adminPermissionsCheck( 'manage_listings', $session->fetch( 'adminlogin' ) ) )
			{
				$template->set( 'approve', '<a href="' . URL . '/admin/listings.php?req=approve&listingid=' . $f['listing_id'] . '&' . $session->fetch( 'listingsearchvariables' ) . '">' . $lang['Admin_Approve_This'] . '</a>' );
			}
			else
			{
				$template->set( 'approve', $lang['Admin_Approve_This'] );
			}
			
			$template->set( 'edit',  '<a href="' . URL . '/admin/editlistings.php?listing_id=' . $f['listing_id'] . '&' . $session->fetch( 'listingsearchvariables' ) . '">' . $lang['Admin_Edit_This'] . '</a>' );
			$template->set( 'remove', '<a href="' . URL . '/admin/listings.php?req=remove&listing_id=' . $f['listing_id'] . '&' . $session->fetch( 'listingsearchvariables' ) . '"><span class="warning">' . $lang['Admin_Remove_This'] . '</span></a>' );
			$template->set( 'link', URL . '/admin/editlistings.php?listing_id=' . $f['listing_id'] );
			
			$sql = 'SELECT COUNT(*) FROM ' . GALLERY_TABLE . ' WHERE listingid = ' . $f['listing_id'];
			$res = $db->query( $sql ); 
			$gallery = mysql_result( $res, 0, 0 );
			
			$images = get_images( 'gallery', $f['listing_id'], 200, 150, 1, 1 );
			$template->set( 'image', $images[0] );
			
			$template->set( 'mls', $f['mls'] );
			$template->set( 'title', $f['title'] );
			$template->set( 'type', getnamebyid ( TYPES_TABLE, $f['type'] ) );
			$template->set( 'type2', getnamebyid ( TYPES2_TABLE, $f['type2'] ) );
			$template->set( 'style', getnamebyid ( STYLES_TABLE, $f['style'] ) );
			
			$description = substr( $f['description'], 0, $conf['search_description'] );
			$description = substr( $description, 0, strrpos( $description, ' ' ) ) . ' ... ';
			
			$template->set( 'description', removehtml( unsafehtml( $description ) ) );
			
			$template->set( 'lot_size', $f['size'] );
			$template->set( 'dimensions', $f['dimensions'] );
			$template->set( 'bathrooms', $f['bathrooms'] );
			$template->set( 'half_bathrooms', $f['half_bathrooms'] );
			$template->set( 'bedrooms', $f['bedrooms'] );
			
			$template->set( 'location1', $f['country'] );
			$template->set( 'location2', $f['state'] );
			$template->set( 'location3', $f['city'] );
			
			$template->set( 'address1', $f['address1'] );
			$template->set( 'address2', $f['address2'] );
			$template->set( 'zip', $f['zip'] );
			
			$template->set( 'price', pmr_number_format( $f['price'] ) );
			$template->set( 'currency', $conf['currency'] );
			$template->set( 'directions', $f['directions'] );
			$template->set( 'year_built', $f['year_built'] );
			$template->set( 'buildings', show_multiple( BUILDINGS_TABLE, $f['buildings'] ) );
			$template->set( 'appliances', show_multiple( APPLIANCES_TABLE, $f['appliances'] ) );
			$template->set( 'features', show_multiple( FEATURES_TABLE, $f['features'] ) );
			$template->set( 'garage', getnamebyid( GARAGE_TABLE, $f['garage'] ) );
			$template->set( 'garage_cars', $f['garage_cars'] );
			$template->set( 'basement', getnamebyid( BASEMENT_TABLE, $f['basement'] ) );
			
			$template->set( 'date_added', printdate( $f['date_added'] ) );
			$template->set( 'date_updated', printdate( $f['date_updated'] ) );
			$template->set( 'date_upgraded', printdate( $f['date_upgraded'] ) );
			
			$template->set( 'ip_added', $f['ip_added'] );
			$template->set( 'ip_updated', $f['ip_updated'] );
			$template->set( 'ip_upgraded', $f['ip_upgraded'] );
			
			$template->set( 'hits', $f['hits'] );
			
			$template->set( 'new', newitem( PROPERTIES_TABLE, $f['listing_id'], $conf['new_days'] ) );
			$template->set( 'updated', updateditem( PROPERTIES_TABLE, $f['listing_id'], $conf['updated_days'] ) );
			$template->set( 'featured', featureditem( $f['featured'] ) );
			
			$sql = 'SELECT * FROM ' . USERS_TABLE  . ' WHERE approved = 1 AND id = ' . $f['userid'] . ' LIMIT 1';
			$r_user = $db->query( $sql ) or error( 'Critical Error' , mysql_error());
			$f_user = $db->fetcharray( $r_user);
			
			$template->set( 'view_realtor', '<a href="' . URL . '/admin/users.php?u_id=' . $f['userid'] . '">' . $f['first_name'] . ' ' . $f['last_name'] . '</a>' );

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
			
			$template->publish();
		}
		
		// Pagination
		 $sql = "
		SELECT COUNT(*) AS total_results
		FROM " . PROPERTIES_TABLE . "
		WHERE 
			1 = 1
			" . $whereSQL . "
		";
		$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		$f = $db->fetcharray( $q );
		$total_results = $f['total_results'];
		
		$custom['pagination'] = pagination( URL . '/admin/listings.php', $_REQUEST['page'], $total_results, $conf['search_results'] );

        if ( is_array( $custom['pagination'] ) )
        {	
        	$num = 1;
        	echo '<br clear="both">';
	        foreach ( $custom['pagination'] AS $page )
	        {				        	
	        	if ( $_REQUEST['page'] == $page['page'] || ( $_REQUEST['page'] == '' && $num == 1 ) )
	        	{
	        		$bold = 'bold';
	        	}
	        	else
	        	{
	        		$bold = 'normal';
	        	}
	        	
	        	echo '<a href="' . $page['url'] . '" style="font-weight:' . $bold . '">' . $page['page'] . '</a>&nbsp;&nbsp;';
	        
				$num++;
	        }
	        echo '<br clear="both"><br />';
        }
	}
	else
	{
		echo '<p align="center"><span class="warning">' . $lang['Nothing_Found'] . '</span></p>';	
	}

	echo table_header( $lang['Property_Search'] );
	
	echo '
	<a href="' . URL . '/admin/listings.php">' . $lang['Admin_Edit_Listings'] . '</a> | 
	<a href="' . URL . '/admin/listings.php?listing_expired=YES">' . $lang['Admin_Expired_Listings'] . '</a> | 
	<a href="' . URL . '/admin/listings.php?listing_updated=YES&listing_updated_days=5">' . $lang['Admin_Approve_Updated_Listings'] . '</a> | 
	<a href="' . URL . '/admin/listings.php?listing_approved=YES">' . $lang['Admin_Approve_New_Listings'] . '</a>
	<br /><br /><br />
	';
	
	echo '<form action="' . URL . '/admin/listings.php" method="POST" id="form">';
	
	echo userform( $lang['Listing_Approved'], '<input type="checkbox" name="listing_approved" value="YES">' );
	echo userform( $lang['Listing_Show_Updated'], '<input type="checkbox" name="listing_updated" value="YES"> ' . $lang['Listing_Show_Updated_For_The_Last'] . ' <input type="text" size="3" name="listing_updated_days" value="5" maxlength="3">' . $lang['days'] );
	
	$order_options = '
	<option value="mls">' . $lang['Listing_MLS'] . '</option>
	<option value="title">' . $lang['Listing_Title'] . '</option>
	<option value="price">' . $lang['Listing_Price'] . '</option>
	<option value="size">' . $lang['Listing_Lot_Size'] . '</option>
	<option value="dimensions">' . $lang['Listing_Dimensions'] . '</option>
	<option value="bathrooms">' . $lang['Listing_Bathrooms'] . '</option>
	<option value="half_bathrooms">' . $lang['Listing_Half_Bathrooms'] . '</option>
	<option value="bedrooms">' . $lang['Listing_Bedrooms'] . '</option>
	<option value="city">' . $lang['City'] . '</option>
	<option value="zip">' . $lang['Zip_Code'] . '</option>
	<option value="year_built">' . $lang['Listing_Year_Built'] . '</option>
	<option value="garage_cars">' . $lang['Listing_Garage_Cars'] . '</option>
	<option value="date_added">' . $lang['Date_Added'] . '</option>
	<option value="date_updated">' . $lang['Date_Updated'] . '</option>
	';
	
	$order_type_options = '
	<option value="ASC">' . $lang['Listing_Ascending'] . '</option>
	<option value="DESC">' . $lang['Listing_Descending'] . '</option>
	';
	
	echo userform( $lang['Order_By'], '<select name="order_by"><option value="">' . $lang['Search_Any'] . '</option>' . $order_options . '</select> <select name="order_by_type"><option value="">' . $lang['Search_Any'] . '</option>' . $order_type_options . '</select>' );
	echo userform( $lang['Module_Listing_Type'], '<select name="type2"><option value="">' . $lang['Search_Any'] . '</option>' . generate_options_list( TYPES2_TABLE ) . '</select>' );
	
	if ( strcasecmp( @$conf['show_mls'], 'OFF' ) != 0 )
	{
		echo userform( $lang['Listing_MLS'], '<input type="text" size="45" name="mls" maxlength="46" />' );
	}
	
	echo userform( $lang['Listing_Title'], '<input type="text" size="45" name="title" maxlength="50">' );
	echo userform( $lang['Listing_Property_Type'], '<select name="type"><option value="">' . $lang['Search_Any'] . '</option>' . generate_options_list( TYPES_TABLE ) . '</select>' );
	echo userform( $lang['Listing_Style'], '<select name="style"><option value="">' . $lang['Search_Any'] . '</option>' . generate_options_list( STYLES_TABLE ) . '</select>' );
	echo userform( $lang['Search_Keyword'], '<input type="text" size="45" name="keyword" maxlength="50">' );
	echo userform( $lang['Listing_Lot_Size'], '<input type="text" size="45" name="size" maxlength="50">' );
	echo userform( $lang['Listing_Dimensions'], '<input type="text" size="45" name="dimensions" maxlength="50">' );
	echo userform( $lang['Listing_Bathrooms'], '<input type="text" size="45" name="bathrooms" maxlength="2">' );
	echo userform( $lang['Listing_Half_Bathrooms'], '<input type="text" size="45" name="half_bathrooms" maxlength="2">' );
	echo userform( $lang['Listing_Bedrooms'], '<input type="text" size="45" name="bedrooms" maxlength="2">' );
	
	$locations = '
	<select name="location1" id="location1">' . get_locations() . '</select><br />
	<select name="location2" id="location2"></select><br />
	<select name="location3" id="location3"></select>
	';
	
	echo userform( $lang['Location'], $locations );
	
	if ( strcasecmp( @$conf['show_postal_code'], 'OFF' ) != 0 )
	{
		echo userform( $lang['Zip_Code'], '<input type="text" size="45" name="zip" maxlength="50">' );
	}
	
	echo userform( $lang['Listing_Address1'], '<input type="text" size="45" name="address1" maxlength="50">' );
	echo userform( $lang['Listing_Address2'], '<input type="text" size="45" name="address2" maxlength="50">' );
	echo userform( $lang['Listing_Directions'], '<input type="text" size="45" name="directions" maxlength="50">' );
	echo userform( $lang['Listing_Year_Built'], '<input type="text" size="45" name="year_built" maxlength="4">' );
	echo userform( $lang['Listing_Additional_Out_Buildings'], admin_generate_checkbox_list( BUILDINGS_TABLE, 'buildings' ) );
	echo userform( $lang['Listing_Appliances_Included'], admin_generate_checkbox_list( APPLIANCES_TABLE, 'appliances' ) );
	echo userform( $lang['Listing_Features'], admin_generate_checkbox_list( FEATURES_TABLE, 'features' ) );
	echo userform( $lang['Listing_Garage'], '<select name="garage"><option value="">' . $lang['Search_Any'] . '</option>' . generate_options_list( GARAGE_TABLE ) . '</select>' );
	echo userform( $lang['Listing_Garage_Cars'], '<input type="text" size="45" name="garage_cars" maxlength="2">' );
	echo userform( $lang['Listing_Basement'], '<select name="basement"><option value="">' . $lang['Search_Any'] . '</option>' . generate_options_list( BASEMENT_TABLE ) . '</select>' );
	
	$price_range = '';
	for ( $i = $conf['price_range_min']; $i <= $conf['price_range_max']; $i = $i + $conf['price_range_step'] )
	{
		$price_range.= '<option value="' . $i . '">' . $conf['currency'] . ' ' . $i . '</option>' . "\n";
	}
	
	echo userform( $lang['Search_Price_Range'], '<select name="price_min"><option value="">' . $lang['Search_Any'] . '</option>' . $price_range . '</select>' );
	
	echo userform( '', '<select name="price_max"><option value="">' . $lang['Search_Any'] . '</option>' . $price_range . '</select>' );
	
	echo userform( '', '<input type="Submit" name="property_search" value="' . $lang['Property_Search'] . '">' );
	echo '</form>';

	echo table_footer();
} 
else
{
	header( 'Location: index.php' );
	exit();
}

include PATH . '/admin/template/footer.php';

?>