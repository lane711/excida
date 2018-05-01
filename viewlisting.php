<?php

define( 'PMR', true );

include 'config.php';
include PATH . '/defaults.php';

$template = new Template;
$tpl = PATH . '/templates/' . $cookie_template . '/tpl/viewlisting.tpl';
$template->load( $tpl );

// Multi-language support
$title = str_replace( 'name', 'title', $language_in );
$description = str_replace( 'name', 'description', $language_in );

// Grab the listing data
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
	AND listing_id = '" . $db->makeSafe( $_GET['id'] ) . "'
LIMIT 1
";
$q = $db->query( $sql ) or error('Critical Error' , mysql_error() );
if ( $db->numrows( $q ) > 0 )
{
	$custom['show_listing'] = true;

	$f = $db->fetcharray( $q );
	$title = $conf['website_name_short'] . ' - ' . $f[0];
	
	$meta_title = trim(removehtml(unsafehtml_xml($f[0])));
	$meta_description = removehtml(unsafehtml_xml($f[1]));
	$meta_description = trim(substr($meta_description, 0, 160));
	$meta_keywords = trim(removehtml(unsafehtml_xml($f[0]) . ', ' . getnamebyid(TYPES2_TABLE, $f['type2'] ) . ', ' . getnamebyid( TYPES_TABLE, $f['type'] ) . ', ' . $f['zip'] . ', ' . $f['mls'] ) );
	
	include PATH . '/templates/' . $cookie_template . '/header.php';

	// Add to recently visited listings session
	visitedListingsAdd( $_GET['id'] );

	// Update the view count
	$sql = "
	UPDATE " . PROPERTIES_TABLE  . "
	SET 
		hits = hits + 1
	WHERE 
		approved = 1 
		AND listing_id = '" . $db->makeSafe( $_GET['id'] ) . "'
	";
	$q2 = $db->query( $sql );
	
	// Get the details of the user that submitted this listing 
	$sql = 'SELECT * FROM ' . USERS_TABLE  . ' WHERE u_id = ' . $f_listing['userid'] . ' LIMIT 1';
	$r_user = $db->query( $sql );
	$f_user = $db->fetcharray( $r_user );
	
	// Get the package data for this listing
	$f_package = package_check( $f_listing['userid'], 'seller' );

	if ( $f_package['mainimage'] == 'ON' )
	{
		$custom['show_image'] = true;

		$images = get_images( 'gallery', $f['listing_id'], 870, 420, 1, 1 );
		$template->set( 'image', $images[0] );
	}
	else
	{
		$custom['show_image'] = false;

		$images = get_images( 'hidden', $f['listing_id'], 870, 420, 1, 1 );
		$template->set( 'image', $images[0] );
	}

    $f['status'] = getnamebyid ( STATUS_TABLE, $f['status'] );
    
    // Hide MLS if it is disabled
    if ( $conf['show_mls'] == 'OFF' ) 
    {
	    $f['mls'] = '';
    }
    
    $f['type_link'] = $f['type'];
    
	$f['type'] = getnamebyid( TYPES_TABLE, $f['type'] );
	$f['type2'] = getnamebyid( TYPES2_TABLE, $f['type2'] );
	$f['style'] = getnamebyid( STYLES_TABLE, $f['style'] );
	$f['garage'] = getnamebyid( GARAGE_TABLE, $f['garage'] );
	$f['basement'] = getnamebyid( BASEMENT_TABLE, $f['basement'] );

	// General amenities
	$features[] = explode( ',', show_multiple( BUILDINGS_TABLE, $f['buildings'] ) );
	$features[] = explode( ',', show_multiple( APPLIANCES_TABLE, $f['appliances'] ) );
	$features[] =  explode( ',', show_multiple( FEATURES_TABLE, $f['features'] ) );
	
	$custom['features'] = array();
	
	foreach ( $features AS $key => $value )
	{
		if ( is_array( $value ) )
		{
			foreach ( $value AS $key2 => $value2 )
			{
				if ( $value2 != '' )
				{
					$custom['features'] = array_merge( $custom['features'], array( $key2 => $value2 ) );
				}
			}
		}
	}
	
	$location = $f['address1'].' '.$f['address2'].' '.$f['city'].' '.$f['state'].' '.$f['zip'].' '.$f['country'];
	
	// Do not show address if it is not allowed in the listing
	if ($f['display_address'] == 'YES' &&  $f_package['address'] == 'ON')
	{
		$template->set( 'address1', $f['address1'] );
		$template->set( 'address2', $f['address2'] . '<br />' );
		$template->set( 'zip', $f['zip'] );
		
		$custom['latitude'] = $f['latitude'];
		$custom['longitude'] = $f['longitude'];
	}
	elseif ($f['display_address'] != 'YES' || $f_package['address'] != 'ON')
	{
		$template->set( 'address1', '' );
		$template->set( 'address2', '' );
		$template->set( 'zip', ' ' );
	}

	// View realtor name
	$sql = 'SELECT * FROM ' . USERS_TABLE  . ' WHERE approved = 1 AND u_id = ' . $f['userid'] . ' LIMIT 1';
	$r_user = $db->query ( $sql ) or error ('Critical Error' , mysql_error());
	$f_user = $db->fetcharray ($r_user);
	
	if ( $conf['rewrite'] == 'ON' )
	{
		$template->set( 'view_realtor', '<a href="' . URL . '/Realtor/' . $f['userid'] . '.html">' . $f_user['first_name'] . ' ' . $f_user['last_name'] . '</a>'  );
	}
	else
	{
		$template->set( 'view_realtor', '<a href="' . URL . '/viewuser.php?id=' . $f['userid'] . '">' . $f_user['first_name'] . ' ' . $f_user['last_name'] . '</a>');
	}
	
	if ( favoriteListingsCheck($f['listing_id'] ) )
	{
		$template->set( 'favorites', '<div id="favorites-' . $f['listing_id'] . '" name="favorites-' . $f['listing_id'] . '" style="display: inline"><span style="cursor: pointer; display: block;" class="favorites" onclick="javascript:xajax_favorites_remove(\'' . $f['listing_id'] . '\');">' . $lang['Favorites_Remove'] . '</span></div>');
	}
	else
	{
		$template->set( 'favorites', '<div id="favorites-' . $f['listing_id'] . '" name="favorites-' . $f['listing_id'] . '" style="display: inline"><span style="cursor: pointer; display: block;" class="favorites" onclick="javascript:xajax_favorites(\'' . $f['listing_id'] . '\');">' . $lang['Favorites_Add'] . '</span></div>');
	}

	// Relator Details
	$template->set( 'realtor_first_name', $f_user['first_name'] );
	$template->set( 'realtor_last_name', $f_user['last_name'] );
	$template->set( 'realtor_company_name', $f_user['company_name'] );
	
	if ( $f_package['phone'] == 'ON' )
	{
		$template->set( 'realtor_phone', $f_user['phone'] );
		$template->set( 'realtor_fax', $f_user['fax'] );
		$template->set( 'realtor_mobile', $f_user['mobile'] );
	}
	else
	{
		$template->set( 'realtor_phone', '' );
		$template->set( 'realtor_fax', '' );
		$template->set( 'realtor_mobile', '' );
	}
	
	// Calendar
	if ( $conf['show_calendar'] == 'ON' )
	{
		$template->set( 'show_calendar', '<p><a href="#" rel="tab4"><span class="tab_title">{@calendar}</span></a></p>' );
	}
	else
	{
		$template->set( 'show_calendar', '' );
	}

	$calendar_tpl = '
	<br />
	<div id="calendar-container">
	<script type="text/javascript">
	var selectedDate = new Array("' . implode( '", "', explode(',', $f['calendar'] ) ) . '", "01/01/2099", "01/02/2099");
	
	function dateSelected(year, month, day) {
	var datein = selectedDate;
	
	month = month+1; if (month < 10) month = "0"+month;
	
	day = day+1; if (day < 10) day = "0" + day;
	
	for (var i = 0; i < datein.length; i++) {
	 if (datein[i] == month + "/" + day + "/" + year) {
	  return true;
	 }
	}
	return false;
	};
	
	Calendar.setup(
	{
	  flat         : "calendar-container", // ID of the parent element
	  dateStatusFunc : function(date, y, m, d) {
	                     if (dateSelected(y, m, d)) return "special";
	                     else return true;
	                   }
	}
	);
	</script>
	</div>
	';
	
	$template->set( '@calendar', $lang['Availability_Calendar'] );
	$template->set( 'calendar', $calendar_tpl );

	// Defaults for their account type
	$paid_account = false;
	$account = false;
	$agent_details = '';

	// If they have an account
	if (auth_check($session->fetch('login'), $session->fetch('password')))
	{
		// Fetching the user ID from the user's table
		$sql = 'SELECT approved, u_id, package FROM ' . USERS_TABLE . ' WHERE login = "' . $session->fetch('login') . '" LIMIT 1';
		$r_agent = $db->query( $sql );
		$f_agent = $db->fetcharray( $r_agent );
	
		$account = true;
		
		if ($f_agent['package'] > 0)
		{
			$paid_account = true;
		}
		else
		{
			$paid_account = false;
		}
	}
	else
	{
		$paid_account = false;
		$account = false;
	}

	// Agent details (whether this displays is configurable via admin panel > configuration settings)

	// If anyone can view agent details or registered only but they are logged in or paid only but they are paid
	if ($conf['contact_agents'] == '1' || $paid_account == true || ($conf['contact_agents'] == '2' && $account == true))
	{
		if ($conf['rewrite'] == 'ON')
			$view_realtor = '<a href="' . URL . '/Realtor/' . $f['userid'] . '.html">' . $f_user['first_name'] . ' ' . $f_user['last_name'] . '</a>';
		else
			$view_realtor = '<a href="' . URL . '/viewuser.php?id=' . $f['userid'] . '">' . $f_user['first_name'] . ' ' . $f_user['last_name'] . '</a>';

		$template->set( 'realtor_website', $f_user['website'] );

		$agent_details .= '
		<tr>
			<th width="30%"><span class="nameDetailed">' . $lang['View_Realtor'] . ':</span></th>
			<td width="70%"><span class="valueDetailed">' . $view_realtor . '</span></td>
		</tr>
		<tr>
			<th><span class="nameDetailed">' . $lang['Realtor_Company_Name'] . ':</span></th>
			<td><span class="valueDetailed">' . $f_user['company_name'] . '</span></td>
		</tr>
		';

		if ($f_package['phone'] == 'ON')
		{
			$agent_details .= '
			<tr>
				<th><span class="nameDetailed">' . $lang['Realtor_Phone'] . ':</span></th>
				<td><span class="valueDetailed">' . $f_user['phone'] . '</span></td>
			</tr>
			<tr>
				<th><span class="nameDetailed">' . $lang['Realtor_Fax'] . ':</span></th>
				<td><span class="valueDetailed">' . $f_user['fax'] . '</span></td>
			</tr>
			<tr>
				<th><span class="nameDetailed">' . $lang['Realtor_Mobile'] . ':</span></th>
				<td><span class="valueDetailed">' . $f_user['mobile'] . '</span></td>
			</tr>
			';
		}
		else
		{
			$agent_details .= '
			<tr>
				<th><span class="nameDetailed">' . $lang['Realtor_Phone'] . ':</span></th>
				<td><span class="valueDetailed"></span></td>
			</tr>
			<tr>
				<th><span class="nameDetailed">' . $lang['Realtor_Fax'] . ':</span></th>
				<td><span class="valueDetailed"></span></td>
			</tr>
			<tr>
				<th><span class="nameDetailed">' . $lang['Realtor_Mobile'] . ':</span></th>
				<td><span class="valueDetailed"></span></td>
			</tr>
			';
		}
	}
	// Account only, but they don't have an account
	elseif ($conf['contact_agents'] == '2' && $account == false)
	{
		$agent_details .= $lang['Agent_Details_Account_Only'];

		$template->set( 'realtor_website', '#' );
	}
	// Paid account only, but they don't have a paid account
	elseif ($conf['contact_agents'] == '3' && $paid_account == false)
	{
		$agent_details .= $lang['Agent_Details_Pay_Only'];
		
		$template->set( 'realtor_website', '#' ) ;
	}
	
	$template->set( '@realtor_website', $lang['Realtor_Visit_Website'] ) ;
	$template->set( 'agent_details', $agent_details );
	
	// Values
	$template->set( 'realtor_mail', URL . '/sendmessage.php?u_id=' . $f['userid'] . '&listing_id=' . $f['listing_id'] );
	$template->set( '@realtor_mail', $lang['Realtor_Send_Message'] );
		
	$template->set( 'new', newitem ( PROPERTIES_TABLE, $f['listing_id'], $conf['new_days']) );
	$template->set( 'updated', updateditem ( PROPERTIES_TABLE, $f['listing_id'], $conf['updated_days']) );
	$template->set( 'featured', featureditem ( $f['featured'] ) );
	
	$template->set( 'date_added2', printdate( $f['date_added'] ) );
	$template->set( 'date_updated2', printdate( $f['date_updated'] ) );
	$template->set( 'date_upgraded2', printdate( $f['date_upgraded'] ) );
	
	$template->set( 'city', $f['city'] );
	$template->set( 'state', $f['state'] );
	$template->set( 'country', $f['country'] );
	$template->set( 'location', $location );
	
	$template->set( 'phone1', $f_user['phone'] );
	
	$f['title'] = ( $f[0] == '' ) ? $f['title'] : $f[0];
	$f['description'] = ( $f[1] == '' ) ? $f['description'] : $f[1];
	
	$template->set( 'status', $f['status'] );
	$template->set( 'mls', $f['mls'] );
	$template->set( 'title', $f['title'] );
	$template->set( 'type', $f['type'] );
	$template->set( 'type2', $f['type2'] );
	$template->set( 'style', $f['style'] );
	$template->set( 'description', unsafehtml($f['description']) );
	$template->set( 'lot_size', $f['size'] );
	$template->set( 'dimensions', $f['dimensions'] );
	
	$template->set( 'bedrooms', $f['bedrooms'] );
	$template->set( 'bathrooms', $f['bathrooms'] );
	$template->set( 'garage_cars', $f['garage_cars'] );
	$template->set( 'half_bathrooms', $f['half_bathrooms'] );
	
	$template->set( 'price', pmr_number_format( $f['price'] ) );
	$template->set( 'currency', $conf['currency'] );
	$template->set( 'directions', unsafehtml( $f['directions'] ) );
	
	$template->set( 'year_built', $f['year_built'] );
	$template->set( 'buildings', $f['buildings'] );
	$template->set( 'appliances', $f['appliances'] );
	$template->set( 'features', $f['features'] );
	$template->set( 'garage', $f['garage'] );
	$template->set( 'basement', $f['basement'] );
	$template->set( 'date_added', printdate($f['date_added']) );
	$template->set( 'date_updated', printdate($f['date_updated']) );
	$template->set( 'date_upgraded', printdate($f['date_upgraded']) );
	$template->set( 'ip_added', $f['ip_added'] );
	$template->set( 'ip_updated', $f['ip_updated'] );
	$template->set( 'ip_upgraded', $f['ip_upgraded'] );
	$template->set( 'hits', $f['hits'] );
	$template->set( 'listing_id', $f['listing_id'] );
	
	// Labels
	$template->set( '@status', $lang['Listing_Status'] );
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
	$template->set( '@video', $lang['Video_Tour'] );
	$template->set( '@seller_details', $lang['View_Realtor'] );
	$template->set( '@properties', $lang['Module_Featured_Listings'] );
	$template->set( '@agents', $lang['Module_Featured_Agents'] );
	
	// Send message
	$template->set( '@name', $lang['Mailer_Name'] );
	$template->set( '@message', $lang['Mail_Friend_Message'] );
	
	$template->set( '@first_name', $lang['Realtor_First_Name'] );
	$template->set( '@last_name', $lang['Realtor_Last_Name'] );
	$template->set( '@company_name', $lang['Realtor_Company_Name'] );
	$template->set( '@phone', $lang['Realtor_Phone'] );
	$template->set( '@fax', $lang['Realtor_Fax'] );
	$template->set( '@mobile', $lang['Realtor_Mobile'] );
	$template->set( '@email', $lang['Realtor_e_mail'] );
	$template->set( '@website', $lang['Realtor_Website'] );
	$template->set( 'favorite', $lang['Favorites_Add'] );
	$template->set( '@send', $lang['Realtor_Send_Message'] );
	
	$template->set( '@slideshow', $lang['Listing_Slideshow'] );
	$template->set( '@details', $lang['Listing_Details'] );
	$template->set( '@map', $lang['Listing_Map'] );
	$template->set( '@overview', $lang['Overview'] );
	$template->set( '@general_amenities', $lang['General_Amenities'] );
	
	// Custom fields
	
	// Check if custom field is input, if it is, it will have {INPUT}{value} in the database
	// otherwise, it will have a primary key reference
	$custom_fields = array(
		'custom1' => $f['custom1'],
		'custom2' => $f['custom2'],
		'custom3' => $f['custom3'],
		'custom4' => $f['custom4'],
		'custom5' => $f['custom5'],
		'custom6' => $f['custom6'],
		'custom7' => $f['custom7'],
		'custom8' => $f['custom8'],
		'custom9' => $f['custom9'],
		'custom10' => $f['custom10'],
	);
	$num = 0;
	foreach ( $custom_fields AS $key => $value )
	{
		if ( $value != '' )
		{
			$custom['show_custom_fields'] = true;
		
			if ( strpos( $value, '{INPUT}' ) !== false )
			{
				// Input box
				$f[$key] = str_replace( '{INPUT}', '', $value );
			}
			
			$custom['custom_fields'][$num]['field'] = show_custom_value( $db, $key, FIELDS_TABLE );
			$custom['custom_fields'][$num]['value'] = $f[$key];
			
			$num++;
		}
	}
	
	// Photo Gallery
	$thumbs = get_images( 'gallery', $f['listing_id'], 155, 75, 1 );
	
	if ( $thumbs[0] != '' )
	{
		// Get the full size images as well
		$full = get_images( 'gallery', $f['listing_id'], 870, 420, 1 );
	
		$custom['show_images'] = true;
		
		$custom['full'] = $full;
		$custom['thumbs'] = $thumbs;
	}
	
	// Embeddable video
	if ( $f['video'] != '' )
	{	
		$custom['video'] = true;
		$custom['video_embed'] = $f['video'];
		$template->set( 'video', html_entity_decode( $f['video'] ) );
	}
}
else
{
	include PATH . '/templates/' . $cookie_template . '/header.php';
	
	$template->set( 'title', '' );

	$custom['show_listing'] = false;
	$output_message = error( $lang['Error'], $lang['No_Listing'], true );
}

$template->set( 'output_message', $output_message );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>