<?php

define( 'PMR', true );

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'] . ' - ' . $lang['Edit_Listings'];

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/edituserlistings.tpl';
$template = new Template;
$template->load ( $tpl );

$custom['languages'] = $installed_languages;
$custom['show_listing_form'] = true;

// Authenticate user
if ( auth_check( $session->fetch( 'login' ), $session->fetch( 'password' ) ) )
{	
	$sid = $session->fetch('site_id');
  	$clause = isset($sid)? " site_id=".$sid:"1=1";
	// Delete gallery image
	if ( $_REQUEST['action'] == 'delete_image' && $_REQUEST['id'] != '' )
	{
		// Delete the image
		$sql = "
		SELECT image_name 
		FROM " . GALLERY_TABLE . "
		WHERE
			id = '" . $db->makeSafe( $_REQUEST['id'] ) . "'
			AND userid = '" . $db->makeSafe( $_SESSION['u_id'] ) . "'
		";
		$q = $db->query( $sql );
		if ( $db->numrows( $q ) > 0 )
		{
			$f = $db->fetcharray( $q );
			
			// Delete from DB
			$sql = "DELETE FROM " . GALLERY_TABLE . " WHERE id = '" . $db->makeSafe( $_REQUEST['id'] ) . "'";
			$q2 = $db->query( $sql );
			
			// Delete the image
			remove_image( 'gallery', $f['image_name'] );
		}
	}

	// Deleting the listing
	if ( $_REQUEST['action'] == 'delete_listing' && $_REQUEST['listing_id'] != '' )
	{	
		removeuserlisting( $_REQUEST['listing_id'] );
		
		header( 'Location: ' . URL . '/viewuserlistings.php' );
		exit();
	}

	// Updating the listing
	if ( $_POST['submit'] == true )
	{
		$custom = array_merge( $custom, $_POST );
	
		// Fetching the user data
		$sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE login = "' . $session->fetch( 'login' ) . '" AND '.$clause.' LIMIT 1';
		$res = $db->query( $sql );
		$f_res = $db->fetcharray( $res );
		
		// Look up their listing package so we know how many listings they have available to list
		if ($f_res['package'] != '' && $f_res['package'] != '0')
		{
			// Fetch Packages
			$sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE  . ' WHERE id = "' . $f_res['package'] . '" AND '.$clause.' LIMIT 1';
			$r_package = $db->query ( $sql );
			$f_package = $db->fetcharray ( $r_package );
		}
		else
		{
			$f_package['listings'] = $conf['free_listings'];
		}
		
		// Change checkbox arrays into a string, with ':' as a delimiter
		if (isset($_POST['buildings']) && is_array($_POST['buildings']))
		{
			$_POST['buildings'] = implode (':', $_POST['buildings']); 
		}
		else 
		{
			$_POST['buildings'] = '';
		}
			
		if (isset($_POST['appliances']) && is_array($_POST['appliances'])) 
		{
			$_POST['appliances'] = implode (':', $_POST['appliances']); 
		}
		else 
		{
			$_POST['appliances'] = '';
		}
		
		if (isset($_POST['features']) && is_array($_POST['features'])) 
		{
			$_POST['features'] = implode (':', $_POST['features']); 
		}
		else 
		{
			$_POST['features'] = '';
		}
		
		if ( !isset( $_POST['display_address'] ) )
		{
			$_POST['display_address'] = 'NO';
		}
				
		// Cut the decription size in case JS is disabled/unavailable
		$_POST['description'] = substr( $_POST['description'], 0, $conf['listing_description_size'] );
	
		$errors = 0;
	
		// At least the first level of locations is required
		if ( $custom['location1'] == '' )
		{
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Location'];
			$errors++;
		}
		
		if (empty($custom['title']) || strlen($custom['title']) < 4 )
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Listing_Title']; 
			$errors++;
		}

		if (empty($custom['description']) || strlen($custom['description']) < 4 )
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Listing_Description']; 
			$errors++;
		}
		
		/*
		if ((!empty($custom['bathrooms'])) && !preg_match( '/^[0-9]+$/i', $custom['bathrooms']))
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Listing_Bathrooms']; 
			$errors++;
		}
		
		if ((!empty($custom['half_bathrooms'])) && !preg_match( '/^[0-9]+$/i', $custom['half_bathrooms']))
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Listing_Half_Bathrooms']; 
			$errors++;
		}
		
		if ((!empty($custom['bedrooms'])) && !preg_match( '/^[0-9]+$/i', $custom['bedrooms']))
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Listing_Bedrooms']; 
			$errors++;
		}
		
		if ((!empty($custom['year_built'])) && !preg_match( '/^[0-9]+$/i', $custom['year_built']))
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Listing_Year_Built']; 
			$errors++;
		}
		
		if ((!empty($custom['status'])) && !preg_match( '/^[0-9]+$/i', $custom['status']))
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Listing_Status']; 
			$errors++;
		}
		*/
		
		if ( $custom['price'] == '' )
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Listing_Price']; 
			$errors++;
		}
	
		if ( $errors > 0 )
		{
			$output_message = error( $lang['Error'], $error_message, true );
		}
		else
		{			
			// If listing needs to be approved by the administrator
			if ( $conf['approve_listings'] == 'ON' )
			{
				$approved = 0;
				$approved_date = 'NULL';
			} 
			else 
			{
				$approved = 1;
				$approved_date = date( 'Y-m-d' );
			}
			
			// Add custom value if text input field
			$custom_fields = array(
				'custom1' => $custom['custom1'],
				'custom2' => $custom['custom2'],
				'custom3' => $custom['custom3'],
				'custom4' => $custom['custom4'],
				'custom5' => $custom['custom5'],
				'custom6' => $custom['custom6'],
				'custom7' => $custom['custom7'],
				'custom8' => $custom['custom8'],
				'custom9' => $custom['custom9'],
				'custom10' => $custom['custom10'],
			);
			foreach ( $custom_fields AS $key => $value )
			{
				if ( $value != '' )
				{
					// Check if this is an input field
					$sql = "SELECT id, type FROM " . FIELDS_TABLE . " WHERE field = '" . $key . "' ";
					$r = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
					$f = $db->fetcharray( $r );
					if ( $f['type'] == 'input' )
					{      				
						// Override value of this custom input box with the primary key of the VALUES table
						$custom[$key] = '{INPUT}' . $value;
					}
				}
			}
			
			// Strip characters from fields that should be numbers only
			$_POST['price'] = preg_replace( '/[^0-9]+/', '', $_POST['price'] );
	
			// Figure out which fields are populated so we can build the SQL query			
			$field_labels = '';
			$field_values = '';
			
			$featured = 'B';
			
			// Regular fields
			$valid_fields = array(
				'mls', 'type', 'style', 'status', 'title', 'description', 'size', 'dimensions', 'bedrooms', 'bathrooms', 'half_bathrooms', 'garage', 'garage_cars', 'basement', 'location1', 'location2', 'location3', 'zip', 'address1', 'address2', 'price', 'directions', 'year_built', 'buildings', 'appliances', 'features', 'video', 'type2', 'calendar', 'latitude', 'longitude', 'video2', 'title2', 'title3', 'title4', 'title5', 'title6', 'title7', 'title8', 'title9', 'title10', 'description2', 'description3', 'description4', 'description5', 'description6', 'description7', 'description8', 'description9', 'description10', 'custom1', 'custom2', 'custom3', 'custom4', 'custom5', 'custom6', 'custom7', 'custom8', 'custom9', 'custom10'
			);
			foreach ( $_POST AS $key => $value )
			{
				if ( $value != '' && $value != '0' && in_array( $key, $valid_fields ) )
				{
					if ( $key == 'location1' )
					{
						$key = 'location_1';
					}
					elseif ( $key == 'location2' )
					{
						$key = 'location_2';
					}
					elseif ( $key == 'location3' )
					{
						$key = 'location_3';
					}
					elseif ( $key == 'video' )
					{
						$value = htmlentities( $value );	
					}

					$update_list .= $key . " = '" . $db->makeSafe( $value ) . "', ";
				}
			}
			
			if ( $update_list != '' )
			{
				$update_list = rtrim( $update_list, ', ' );
			}
	
			$sql = "
			UPDATE " . PROPERTIES_TABLE . "
			SET
				approved = '" . $approved . "',
				featured = '" . $featured . "',
				display_address = '" . $display_address . "',
				date_updated = '" . date( 'Y-m-d' ) . "',
				ip_updated = '" . $db->makeSafe( $_SERVER['REMOTE_ADDR'] ) . "',
				" . $update_list . "
			WHERE
				userid = '" . $db->makeSafe( $session->fetch( 'u_id' ) ) . "'
				AND listing_id = '" . $db->makeSafe( $_REQUEST['listing_id'] ) . "'
			";
			$q = $db->query( $sql );
			
			// Output the 'Thank you' message
			if ($conf['approve_listings'] == 'ON')
			{
				$output_message = success( $lang['Success'], $lang['Listing_Updated_Approve'], true );
				
				$lang['Admin_Listing_Notification_Mail'] = str_replace('{title}', $custom['title'], $lang['Admin_Listing_Notification_Mail']);
				
				send_mailing( 
					$conf['general_e_mail'], 
					$conf['general_e_mail_name'], 
					$conf['general_e_mail'], 
					$lang['Admin_Listing_Notification_Subject'], 
					$lang['Admin_Listing_Notification_Mail'] 
				);
			}
			else
			{
				$output_message = success( $lang['Success'], $lang['Listing_Updated'], true );
			}
		}
	}

	// Grab the listing data
	$sql = "
	SELECT
		p.*,
		l1.location_name AS country,
		l2.location_name AS state,
		l3.location_name AS city,
		l1.location_id AS country_id,
		l2.location_id AS state_id,
		l3.location_id AS city_id,
		users.u_id
	FROM " . PROPERTIES_TABLE  . " AS p
	LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = p.location_1
	LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = p.location_2
	LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = p.location_3
	LEFT JOIN " . USERS_TABLE . " AS users ON users.u_id = p.userid
	WHERE 
		p.listing_id = '" . $db->makeSafe( $_REQUEST['listing_id'] ) . "'
	";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		$f = $db->fetchassoc( $q );
		
		// Used in the gallery bulk importer
		$_SESSION['listing_id'] = $f['listing_id'];
		$_SESSION['user_id'] = $session->fetch( 'u_id' );
		$_SESSION['admin'] = false;
		$_SESSION['image_session'] = '';
		
		// Default values for the form
		if ( $_POST )
		{
			$custom = array_merge( $f, $custom, $_POST );
			
			// Features
			$custom['buildings'] = explode( ':', $_POST['buildings'] ); 
			$custom['features'] = explode( ':', $_POST['features'] ); 
			$custom['appliances'] = explode( ':', $_POST['appliances'] ); 
			
			// Location multi-drop down
			$custom['location1_name'] = get_location_name( $custom['location1'] );
			$custom['location1_id'] = $custom['location1'];
			
			$custom['location2_name'] = get_location_name( $custom['location2'] );
			$custom['location2_id'] = $custom['location2'];
			
			$custom['location3_name'] = get_location_name( $custom['location3'] );
			$custom['location3_id'] = $custom['location3'];
		}
		else
		{
			$custom = array_merge( $f, $custom );
			
			$custom['appliances'] = explode( ':', $f['appliances'] );
			$custom['features'] = explode( ':', $f['features'] );
			$custom['buildings'] = explode( ':', $f['buildings'] ); 
		
			// Location multi-drop down
			$custom['location1_name'] = $f['country'];
			$custom['location1_id'] = $f['country_id'];
	
			$custom['location2_name'] = $f['state'];
			$custom['location2_id'] = $f['state_id'];
	
			$custom['location3_name'] = $f['city'];
			$custom['location3_id'] = $f['city_id'];
		}
		
		// Image gallery
		$custom['gallery_list'] = array();
		
		$sql = "
		SELECT * 
		FROM " . GALLERY_TABLE . "
		WHERE 
			listingid = '" . $db->makeSafe( $f['listing_id'] ) . "'
		ORDER BY id ASC
		";
		$q2 = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		if ( $db->numrows( $q2 ) > 0 )
		{
			$num = 0;
		
			while( $f2 = $db->fetcharray( $q2 ) )
			{
				$custom['gallery_list'][$num]['thumb'] = show_image( 'gallery', $f2['image_name'], 155, 75 );
				$custom['gallery_list'][$num]['full'] = show_image( 'gallery', $f2['image_name'], 870, 420 );
				$custom['gallery_list'][$num]['id'] = $f2['id'];
				
				$num++;
			}	
		}
		
		// Upgrade options
		
		$upgrade_options = payment_gateway( 'listing', $f['u_id'], $session->fetch( 'login' ) );

		if ( $upgrade_options != '' )
		{
			$custom['show_upgrade'] = true;
		}

		// Custom fields

		// Check if we have any custom fields
		$custom['show_custom_fields'] = false;

		$query = "SELECT * FROM " . FIELDS_TABLE . " ORDER BY name ASC";		
		$result = $db->query($query) OR error( 'Critical Error:' . $query);
		if ( $db->numrows( $result ) > 0 ) 
		{
			$custom['show_custom_fields'] = true;
		}

		// Show availability calendar if enabled
		if ( $conf['show_calendar'] == 'ON' )
		{		
			$dates = explode(',', $custom['calendar']);

			foreach ($dates as $k => $date)
			{
				if (strlen(trim($date)) == 0)
				{
					unset($dates[$k]);
					continue;
				}

				list( $month, $day, $year ) = explode( '/', $date );

				if ( date('Y-m-d') > $year . '-' . $month . '-' . $day )
				{
					unset($dates[$k]);
				}
			}

			$custom['calendar'] = implode( ',', $dates );

			if ($custom['calendar'])
			{
				$custom['calendar'] .= ',';
			}
		}

		// Default values for drop-downs

		// Title/description has several fields because of multi-language support
		for( $i = 0; $i <= 30; $i++ )
		{
			$i = ( $i == 0 ) ? '' : $i;
			$template->set( 'title' . $i, $custom["title$i"] );
			$template->set( 'description' . $i, $custom["description$i"] );
		}

		$template->set( 'address1', $custom['address1'] );
		$template->set( 'address2', $custom['address2'] );
		$template->set( 'zip', $custom['zip'] );
		$template->set( 'listing_type', $custom['listing_type'] );
		$template->set( 'status', $custom['status'] );
		$template->set( 'location1', get_locations() );
		$template->set( 'video', $custom['video'] );
		$template->set( 'directions', $custom['directions'] );
		$template->set( 'lot_size', $custom['size'] );
		$template->set( 'living_area', $custom['dimensions'] );
		$template->set( 'longitude', $custom['longitude'] );
		$template->set( 'latitude', $custom['latitude'] );
		$template->set( 'mls', $custom['mls'] );
		$template->set( 'price', $custom['price'] );
		$template->set( 'listing_id', $_REQUEST['listing_id'] );

		// Statistics
		$template->set( '@date_added', $lang['Listing_Added_Date'] );
		$template->set( 'date_added', $f['date_added'] );

		$template->set( '@date_updated', $lang['Listing_Updated_Date'] );
		$template->set( 'date_updated', $f['date_updated'] );
		
		$template->set( '@ip_added', $lang['Listing_IP_Added'] );
		$template->set( 'ip_added', @gethostbyaddr( $f['ip_added'] ) );

		$template->set( '@ip_updated', $lang['Listing_IP_Updated'] );
		$template->set( 'ip_updated', @gethostbyaddr( $f['ip_updated'] ) );	

		$template->set( '@hits', $lang['Hits'] );
		$template->set( 'hits', $f['hits'] );

		// Labels
		$template->set( 'submit', $lang['Listing_Submit'] );
		$template->set( '@listing_step1', $lang['Listing_Step1'] );
		$template->set( '@listing_step2', $lang['Listing_Step2'] );
		$template->set( '@listing_step3', $lang['Listing_Step3'] );
		$template->set( '@listing_step4', $lang['Listing_Step4'] );
		$template->set( '@listing_step5', $lang['Listing_Step5'] );
		$template->set( '@title', $lang['Listing_Title'] );
		$template->set( '@listing_type', $lang['Module_Listing_Type'] );
		$template->set( '@status', $lang['Listing_Status'] );
		$template->set( '@property_type', $lang['Listing_Property_Type'] );
		$template->set( '@mls', $lang['Listing_MLS'] );
		$template->set( '@style', $lang['Listing_Style'] );
		$template->set( '@description', $lang['Listing_Description'] );
		$template->set( '@location', $lang['Location'] );
		$template->set( '@bedrooms', $lang['Listing_Bedrooms'] );
		$template->set( '@bathrooms', $lang['Listing_Bathrooms'] );
		$template->set( '@half_bathrooms', $lang['Listing_Half_Bathrooms'] );
		$template->set( '@price', $lang['Listing_Price'] );
		$template->set( '@address1', $lang['Listing_Address1'] );
		$template->set( '@address2', $lang['Listing_Address2'] );
		$template->set( '@zip', $lang['Zip_Code'] );
		$template->set( '@image', $lang['Image_Info'] );
		$template->set( '@directions', $lang['Listing_Directions'] );
		$template->set( '@lot_size', $lang['Listing_Lot_Size'] );
		$template->set( '@basement', $lang['Listing_Basement'] );
		$template->set( '@living_area', $lang['Listing_Dimensions'] );
		$template->set( '@longitude', $lang['Listing_Longitude'] );
		$template->set( '@latitude', $lang['Listing_Latitude'] );
		$template->set( '@garage_cars', $lang['Listing_Garage_Cars'] );
		$template->set( '@garage', $lang['Listing_Garage'] );
		$template->set( '@year_built', $lang['Listing_Year_Built'] );
		$template->set( '@amenities', $lang['Listing_Additional_Out_Buildings'] );
		$template->set( '@appliances', $lang['Listing_Appliances_Included'] );
		$template->set( '@features', $lang['Listing_Features'] );
		$template->set( '@video', $lang['Video_Tour'] );
		$template->set( 'select', $lang['Select'] );
		$template->set( '@show_address', $lang['Listing_Display_Address'] );
		$template->set( '@bulk_upload', $lang['Photo_Gallery_Bulk'] );
		$template->set( 'remove_listing', $lang['Listing_Remove'] );
		$template->set( 'upload', $lang['Upload'] );
		$template->set( '@gallery', $lang['Photo_Gallery'] );
		$template->set( 'gallery_text', $lang['Gallery_Remove'] );
		
		$list_text = $lang['Listing_Text'];
	}
	else
	{
		$output_message = error( $lang['Error'], $lang['No_Listing'], true );
		$custom['show_listing_form'] = false;
	}
}
else
{
	$output_message = error( $lang['Error'], $lang['Not_Logged_In'], true );
	$custom['show_listing_form'] = false;
	$listing_form = '';
	$list_text = '';
	
	$custom['hide_nav'] = true;
}

$template->set( 'list_text', $list_text );
$template->set( 'output_message', $output_message );
$template->set( 'header', $lang['Menu_List_Now'] );
$template->set( '@statistics', $lang['Seller_Panel_Statistics'] );
$template->set( '@upgrade_options', $lang['Upgrade_Listing'] );
$template->set( 'upgrade_options', $upgrade_options );

$template->set( '@add_listing', $lang['Menu_Submit_Property'] );
$template->set( '@control_panel', $lang['Menu_User_Login'] );
$template->set( '@edit_listings', $lang['Edit_Listings'] );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>