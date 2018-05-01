<?php

define( 'PMR', true );

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'] . ' - ' . $lang['Add_Listings'];

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/adduserlistings.tpl';
$template = new Template;
$template->load ( $tpl );

$custom['show_listing_form'] = true;
$custom['languages'] = $installed_languages;

// Authenticate user
if ( auth_check( $session->fetch( 'login' ), $session->fetch( 'password' ) ) )
{
	// Fetching the user data
	$sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE login = "' . $session->fetch( 'login' ) . '" LIMIT 1';
	$res = $db->query( $sql );
	$f_res = $db->fetcharray( $res );
	$sid = $session->fetch('site_id');
	$clause = isset($sid)? " site_id=".$sid:"1=1";
	// Look up their listing package so we know how many listings they have available to list
	if ($f_res['package'] != '' && $f_res['package'] != '0')
	{
		// Fetch Packages
		$sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE  . ' WHERE id = "' . $f_res['package'] . '" LIMIT 1';
		$r_package = $db->query ( $sql );
		$f_package = $db->fetcharray ( $r_package );
	}
	else
	{
		$f_package['listings'] = $conf['free_listings'];
	}

	// Submit a new listing into the system
	if ( $_POST['submit'] == true )
	{	
		$custom = array_merge( $custom, $_POST );
	
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
		
		// Check if they are at the maximum listings for their package
		$sql = 'SELECT * FROM ' . PROPERTIES_TABLE . ' WHERE userid = "' . $f_res['id'] . '" ';
		$r = $db->query($sql) or error ('Critical Error', mysql_error () );
		if ( $db->numrows($r) >= $f_package['listings'] )
		{
			$error_message = $lang['Maximum_Number_Of_Listings_Reached'];
			$errors++;
		}

		if ( $errors > 0 )
		{
			$output_message = error( $lang['Error'], $error_message, true );
		}
		else
		{			
			// If listing needs to be approved by the administrator
			if ($conf['approve_listings'] == 'ON')
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
				
					$field_labels .= $key . ', ';
					$field_values .= "'" . $db->makeSafe( $value ) . "', ";
				}
			}
			
			if ( $field_labels != '' && $field_values != '' )
			{
				$field_labels = rtrim( $field_labels, ', ' );
				$field_values = rtrim( $field_values, ', ' );
			}

			$sql = "
			INSERT INTO " . PROPERTIES_TABLE . "
			(
				userid,
				site_id,
				approved,
				featured,
				display_address,
				date_added,
				date_approved,
				ip_added,
				image_uploaded,
				" . $field_labels . "
			)
			VALUES
			(
				'" . $f_res['u_id'] . "',
				'" . $f_res['site_id'] . "',
				'" . $approved . "',
				'" . $featured . "',
				'" . $display_address . "',
				'" . date( 'Y-m-d' ) . "',
				'" . $date_approved . "',
				'" . $db->makeSafe( $_SERVER['REMOTE_ADDR'] ) . "',
				'" . $image_uploaded . "',
				" . $field_values . "
			)
			";
			$q = $db->query( $sql );
 			$id = $db->getLastID();
			
			$custom['show_listing_form'] = false;
			
			// Update the gallery table if any have a temporary ID
			$sql = "
			UPDATE " . GALLERY_TABLE . " 
			SET 
				listingid = '" . $id . "',
				temp_id = '0'
			WHERE 
				temp_id = '" . $db->makeSafe( $_SESSION['image_session'] ) . "'";
			$q = $db->query( $sql );
			
			// Output the 'Thank you' message
			if ($conf['approve_listings'] == 'ON')
			{
				$output_message = success( $lang['Success'], $lang['Realtor_Listing_Added_Approve'], true );
				
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
				$output_message = success( $lang['Success'], $lang['Realtor_Listing_Added'], true );
				
				check_alerts( $id, $custom );	
			}
		}
	}
	
	// Get the total number of listings they currently have
	$sql = 'SELECT id FROM ' . PROPERTIES_TABLE . ' WHERE userid = "' . $f_res['id'] . '" AND '.$clause;
	$r_listings = $db->query( $sql );
	$num_listings = $db->numrows( $r_listings );
	
	// Custom fields
	
	$custom['show_custom_fields'] = false;
	
	$query = "SELECT * FROM " . FIELDS_TABLE . " WHERE ".$clause." ORDER BY name ASC";		
	$result = $db->query($query) OR error( 'Critical Error:' . $query);
	if ( $db->numrows( $result ) > 0 ) 
	{
		$custom['show_custom_fields'] = true;
	}
	
	// Show availability calendar if enabled
	if ( $conf['show_calendar'] == 'ON' )
	{	
		$dates = explode( ',', $custom['calendar'] );
		
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
	
	if ( $custom['show_listing_form'] == true )
	{
		// Default for the image uploader to keep track of bulk image uploads
		if ( $_SESSION['image_session'] == '' )
		{
			$_SESSION['image_session'] = rand( 111111, 999999 );
		}
		
		$_SESSION['admin'] = false;
		$_SESSION['user_id'] = $session->fetch( 'u_id' );
		$_SESSION['listing_id'] = '';
	
		// Default values for the form
		if ( $_POST )
		{
			$custom = array_merge( $custom, $_POST );

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
			// Features
			$custom['buildings'] = array(); 
			$custom['features'] = array();
			$custom['appliances'] = array();	
		}

		// To save their values in case of an error
		// Title/description has several fields because of multi-language support
		for ( $i = 0; $i <= 30; $i++ )
		{
			$i = ( $i == 0 ) ? '' : $i;
			$template->set( 'title' . $i, $_REQUEST["title$i"] );
			$template->set( 'description' . $i, $_REQUEST["description$i"] );
		}
		$template->set( 'address1', $_REQUEST['address1'] );
		$template->set( 'address2', $_REQUEST['address2'] );
		$template->set( 'zip', $_REQUEST['zip'] );
		$template->set( 'listing_type', $_REQUEST['listing_type'] );
		$template->set( 'status', $_REQUEST['status'] );
		$template->set( 'location1', get_locations() );
		$template->set( 'video', $_REQUEST['video'] );
		$template->set( 'directions', $_REQUEST['directions'] );
		$template->set( 'lot_size', $_REQUEST['size'] );
		$template->set( 'living_area', $_REQUEST['dimensions'] );
		$template->set( 'longitude', $_REQUEST['longitude'] );
		$template->set( 'latitude', $_REQUEST['latitude'] );
		$template->set( 'mls', $_REQUEST['mls'] );
		$template->set( 'price', $_REQUEST['price'] );
		
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
		$template->set( 'upload', $lang['Upload'] );
		$template->set( '@bulk_upload', $lang['Photo_Gallery_Bulk'] );
		
		$list_text = $lang['Listing_Text'];
	}
}
else
{
	header( 'Location: ' . URL . '/login.php' );
	exit();
}

$template->set( 'list_text', $list_text );
$template->set( 'output_message', $output_message );
$template->set( 'header', $lang['Menu_List_Now'] );

$template->set( '@add_listing', $lang['Menu_Submit_Property'] );
$template->set( '@control_panel', $lang['Menu_User_Login'] );
$template->set( '@edit_listings', $lang['Edit_Listings'] );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>