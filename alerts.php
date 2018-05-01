<?php

define( 'PMR', true );

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'] . ' - ' . $lang['Alert'];

include PATH . '/templates/' . $cookie_template . '/header.php';

// Defaults for the page
$output_message = '';
$error_message = '';
$custom['display_form'] = true;

// Activate a new alert
if ( $_GET['action'] == 'activate' && $_GET['code'] != '' )
{
	$sql = "UPDATE " . ALERTS_TABLE . " SET approved = 1 WHERE code = '" . $db->makeSafe( $_GET['code'] ) . "' LIMIT 1";
	$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	
	$output_message = success( $lang['Success'], $lang['Alert_Approved'], true );
	
	$custom['display_form'] = false;
}

// Deactivate a new alert
if ( $_GET['action'] == 'deactivate' && $_GET['code'] != '' )
{
	$sql = "DELETE FROM " . ALERTS_TABLE . " WHERE code = '" . $db->makeSafe( $_GET['code'] ) . "' LIMIT 1";
	$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );

	$output_message = success( $lang['Success'], $lang['Alert_Unsubscribed'], true );
	
	$custom['display_form'] = false;
}

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/alerts.tpl';
$template = new Template;
$template->load ( $tpl );

if ( $_REQUEST['submit'] == true )
{
	$errors = 0;
	
	// All fields that they can set criteria for
	$request_list = array(
		'keyword', 'listing_type', 'property_type', 'style', 'location_1', 'location_2', 'location_3', 'bedrooms', 'bathrooms', 'half_bathrooms', 'year_built', 'garage', 'garage_type', 'basement', 'living_area', 'lot_size', 'amenities', 'appliances', 'features', 'custom1', 'custom2', 'custom3', 'custom4', 'custom5', 'custom6', 'custom7', 'custom8', 'custom9', 'custom10', 'price_range_rent_min', 'price_range_rent_max', 'price_range_purchase_max', 'price_range_purchase_max', 'email', 'zip'
	);
	$total_set = 0;
	
	foreach ( $request_list AS $key )
	{	
		// At least two fields are required in order to save this alert				
		if ( $_REQUEST[$key] != '' && $_REQUEST[$key] != '0' )
		{			
			if ( $key == 'email' )
			{
				if ( ( strlen( $_REQUEST[$key] ) < 4 ) || !valid_email( $_REQUEST[$key] ) )
				{
					$error_message = $lang['Alert_email']; 
					$errors++;
				}
				else
				{
					$total_set++;
				}
			}
			else
			{
				$total_set++;
			}
		}
	}
	
	if ( $total_set < 2 )
	{
		$error_message = $lang['Alert_Minimum']; 
		$errors++;
	}
	
	// Check if email is banned
	$sql = 'SELECT * FROM ' . BANS_TABLE . ' WHERE name = "' . $db->makeSafe( $_REQUEST['email'] ) . '" LIMIT 1';
	$r = $db->query($sql) or error ('Critical Error', mysql_error () );
	if ($db->numrows($r) > 0 )
	{
		$error_message = $lang['e_mail_Banned']; 
		$errors++;
	}
	
	if ( $errors > 0 )
	{
		$output_message = error( $lang['Error'], $error_message, true );
	}
	else
	{
		// Random approval code in order for these alerts to start (must confirm their email)
		$code = rand( 11111, 99999 );
		
		// Certain fields need to be stored in a specific way
		if ( $_REQUEST['amenities'] != '' )
		{
			$_REQUEST['amenities'] = implode( ':', $_REQUEST['amenities'] );
		}
		
		if ( $_REQUEST['appliances'] != '' )
		{
			$_REQUEST['appliances'] = implode( ':', $_REQUEST['appliances'] );
		}
		
		if ( $_REQUEST['features'] != '' )
		{
			$_REQUEST['features'] = implode( ':', $_REQUEST['features'] );
		}
		
		// Format price
		if ( $_REQUEST['price_range_rent_min'] != '' && $_REQUEST['price_range_rent_min'] != '0' )
		{
			$from_price = $_REQUEST['price_range_rent_min'];
		}
		
		if ( $_REQUEST['price_range_purchase_min'] != '' && $_REQUEST['price_range_purchase_min'] != '0' )
		{
			$from_price = $_REQUEST['price_range_purchase_min'];
		}

		if ( $_REQUEST['price_range_rent_max'] != '' && $_REQUEST['price_range_rent_max'] != '0' )
		{
			$to_price = $_REQUEST['price_range_rent_max'];
		}
		
		if ( $_REQUEST['price_range_purchase_max'] != '' && $_REQUEST['price_range_purchase_max'] != '0' )
		{
			$to_price = $_REQUEST['price_range_purchase_max'];
		}
	
		$sql = "
		INSERT INTO " . ALERTS_TABLE . "
		(
			code,
			email,
			approved,
			date,
			keyword,
			listing_type,
			property_type,
			style,
			from_price,
			to_price,
			location_1,
			location_2,
			location_3,
			zip,
			radius,
			bedrooms,
			bathrooms,
			half_bathrooms,
			year_built,
			garage,
			garage_cars,
			basement,
			living_area,
			lot_size,
			amenities,
			appliances,
			features,
			custom1,
			custom2,
			custom3,
			custom4,
			custom5,
			custom6,
			custom7,
			custom8,
			custom9,
			custom10
		) 
		VALUES
		(
			'" . $code . "',
			'" . $db->makeSafe( $_REQUEST['email'] ) . "',
			'0',
			'" . date( 'Y-m-d' ) . "',
			'" . $db->makeSafe( $_REQUEST['keyword'] ) . "',
			'" . $db->makeSafe( $_REQUEST['listing_type'] ) . "',
			'" . $db->makeSafe( $_REQUEST['property_type'] ) . "',
			'" . $db->makeSafe( $_REQUEST['style'] ) . "',
			'" . $db->makeSafe( $from_price ) . "',
			'" . $db->makeSafe( $to_price ) . "',
			'" . $db->makeSafe( $_REQUEST['location1'] ) . "',
			'" . $db->makeSafe( $_REQUEST['location2'] ) . "',
			'" . $db->makeSafe( $_REQUEST['location3'] ) . "',
			'" . $db->makeSafe( $_REQUEST['zip'] ) . "',
			'" . $db->makeSafe( $_REQUEST['radius'] ) . "',
			'" . $db->makeSafe( $_REQUEST['bedrooms'] ) . "',
			'" . $db->makeSafe( $_REQUEST['bathrooms'] ) . "',
			'" . $db->makeSafe( $_REQUEST['half_bathrooms'] ) . "',
			'" . $db->makeSafe( $_REQUEST['year_built'] ) . "',
			'" . $db->makeSafe( $_REQUEST['garage'] ) . "',
			'" . $db->makeSafe( $_REQUEST['garage_cars'] ) . "',
			'" . $db->makeSafe( $_REQUEST['basement'] ) . "',
			'" . $db->makeSafe( $_REQUEST['living_area'] ) . "',
			'" . $db->makeSafe( $_REQUEST['lot_size'] ) . "',
			'" . $db->makeSafe( $_REQUEST['amenities'] ) . "',
			'" . $db->makeSafe( $_REQUEST['appliances'] ) . "',
			'" . $db->makeSafe( $_REQUEST['features'] ) . "',
			'" . $db->makeSafe( $_REQUEST['custom1'] ) . "',
			'" . $db->makeSafe( $_REQUEST['custom2'] ) . "',
			'" . $db->makeSafe( $_REQUEST['custom3'] ) . "',
			'" . $db->makeSafe( $_REQUEST['custom4'] ) . "',
			'" . $db->makeSafe( $_REQUEST['custom5'] ) . "',
			'" . $db->makeSafe( $_REQUEST['custom6'] ) . "',
			'" . $db->makeSafe( $_REQUEST['custom7'] ) . "',
			'" . $db->makeSafe( $_REQUEST['custom8'] ) . "',
			'" . $db->makeSafe( $_REQUEST['custom9'] ) . "',
			'" . $db->makeSafe( $_REQUEST['custom10'] ) . "'
		)
		";
		$q = $db->query( $sql ) or error ( 'Critical Error', mysql_error() );
		
		$lang['Alert_Notification_Subject'] = str_replace('{website}', $conf['website_name'], $lang['Alert_Notification_Subject']);
		
		// Replacing the variable names
		$lang['Alert_Notification_Mail'] = str_replace('{name}', $_REQUEST['name'], $lang['Alert_Notification_Mail']);
		$lang['Alert_Notification_Mail'] = str_replace('{email}', $_REQUEST['email'], $lang['Alert_Notification_Mail']);
		$lang['Alert_Notification_Mail'] = str_replace('{link}', URL . '/alerts.php?action=activate&code=' . $code, $lang['Alert_Notification_Mail']);
		$lang['Alert_Notification_Mail'] = str_replace('{website}', $conf['website_name'], $lang['Alert_Notification_Mail']);
		
		send_mailing( 
			$conf['general_e_mail'], 
			$conf['general_e_mail_name'], 
			$_REQUEST['email'], 
			$lang['Alert_Notification_Subject'], 
			$lang['Alert_Notification_Mail'] 
		);
		
		$output_message = success( $lang['Success'], $lang['Alert_Added'], true );
	
		// Don't load the contact form template again
		$custom['display_form'] = false;
	}
}

// Labels
$template->set( '@radius', $lang['Your_Zip_Code_Radius'] );
$template->set( '@from_price', $lang['From_Price'] );
$template->set( '@to_price', $lang['To_Price'] );
$template->set( '@from_price_rental', $lang['From_Price_Rental'] );
$template->set( '@to_price_rental', $lang['To_Price_Rental'] );
$template->set( '@keyword', $lang['Search_Keyword'] );
$template->set( 'submit', $lang['Listing_Submit'] );
$template->set( '@listing_type', $lang['Module_Listing_Type'] );
$template->set( '@status', $lang['Listing_Status'] );
$template->set( '@property_type', $lang['Listing_Property_Type'] );
$template->set( '@style', $lang['Listing_Style'] );
$template->set( '@location', $lang['Location'] );
$template->set( '@bedrooms', $lang['Listing_Bedrooms'] );
$template->set( '@bathrooms', $lang['Listing_Bathrooms'] );
$template->set( '@half_bathrooms', $lang['Listing_Half_Bathrooms'] );
$template->set( '@price', $lang['Listing_Price'] );
$template->set( '@zip', $lang['Zip_Code'] );
$template->set( '@lot_size', $lang['Listing_Lot_Size'] );
$template->set( '@basement', $lang['Listing_Basement'] );
$template->set( '@living_area', $lang['Listing_Dimensions'] );
$template->set( '@garage_cars', $lang['Listing_Garage_Cars'] );
$template->set( '@garage', $lang['Listing_Garage'] );
$template->set( '@year_built', $lang['Listing_Year_Built'] );
$template->set( '@amenities', $lang['Listing_Additional_Out_Buildings'] );
$template->set( '@appliances', $lang['Listing_Appliances_Included'] );
$template->set( '@features', $lang['Listing_Features'] );
$template->set( 'select', $lang['Select'] );
$template->set( 'search', $lang['Menu_Search'] );
$template->set( '@alert', $lang['Alert_Submit'] );
$template->set( '@email', $lang['Realtor_e_mail'] );

// Values
$template->set( 'zip', $_REQUEST['zip'] );
$template->set( 'location1', get_locations() );
$template->set( 'lot_size', $_REQUEST['lot_size'] );
$template->set( 'living_area', $_REQUEST['living_area'] );
$template->set( 'keyword', $_REQUEST['keyword'] );
$template->set( 'email', $_REQUEST['email'] );

// Main
$template->set( 'alerts_form', $alerts_form );
$template->set( 'output_message', $output_message );
$template->set( 'alerts_about', $lang['Alert_Text'] );
$template->set( 'header', $lang['Alert'] );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>