<?php

// --------------------------------------------------------------------------
// debug()
// Outputs test data that can be turned ON and OFF based on the DEBUG constant
function debug( $text )
{
	if ( DEBUG == true )
	{
		echo $text . '<br /><br />';	
	}
}

// --------------------------------------------------------------------------
// check_alerts();
// Check to see if any alerts match this newly added listing and send it off if so
function check_alerts( $listing_id, $listing_data )
{
	global $db, $conf, $lang;
	
	// Account for amenities, appliances, and features
	if ( $listing_data['buildings'] != '' && is_string( $listing_data['buildings'] ) )
	{
		$amenities = explode( ':', $listing_data['buildings'] );
	}

	if ( $listing_data['appliances'] != '' && is_string( $listing_data['appliances'] ) )
	{	
		$appliances = explode( ':', $listing_data['appliances'] );
	}
	
	if ( $listing_data['features'] != '' && is_string( $listing_data['features'] ) )
	{
		$features = explode( ':', $listing_data['features'] );
	}
	
	if ( is_array( $features ) && $features[0] != '' )
	{
		foreach ( $features AS $feature )
		{
			$feature_list .= $feature . ", ";
		}
		$feature_list = trim( $feature_list, ', ' );	
		$whereSQL .= " AND features IN (" . $db->makeSafe( $feature_list ) . ")";
	}
	
	if ( is_array( $amenities ) && $amenities[0] != '' )
	{
		foreach ( $amenities AS $amenity )
		{
			$amenities_list .= $amenity . ", ";
		}
		$amenities_list = trim( $amenities_list, ', ' );	
		$whereSQL .= " AND amenities IN (" . $db->makeSafe( $amenities_list ) . ")";
	}

	if ( is_array( $appliances ) && $appliances[0] != '' )
	{
		foreach ( $appliances AS $appliance )
		{
			$appliances_list .= $appliance . ", ";
		}
		$appliances_list = trim( $appliances_list, ', ' );	
		$whereSQL .= " AND appliances IN (" . $db->makeSafe( $appliances_list ) . ")";
	}
	
	if ( $listing_data['price'] != '' )
	{
		$whereSQL .= " 
			AND from_price <= '" . $db->makeSafe( $listing_data['price'] ) . "' 
			AND to_price >= '" . $db->makeSafe( $listing_data['price'] ) . "'
		";
	}
	
	// Fields that are an exact match
	$fields = array(
		'style', 'type', 'type2', 'location_1', 'location_2', 'location_3'
	);
	foreach ( $fields AS $field )
	{
		if ( $listing_data[$field] != '' )
		{
			$key = $field;
			
			if ( $key == 'type' )
			{
				$key = 'property_type';	
			}
			elseif ( $key == 'type2' )
			{
				$key = 'listing_type';
			}
			$whereSQL .= " AND " . $key . " = '" . $db->makeSafe( $listing_data[$field] ) . "'";	
		}	
	}
	
	if ( $listing_data['style'] != '' )
	{
		$whereSQL .= " AND style = '" . $db->makeSafe( $listing_data['style'] ) . "'";	
	}
	
	// Fields that are less than or equal to
	$fields = array(
		'bedrooms', 'bathrooms', 'year_built', 'garage', 'garage_cars', 'basement', 'living_area', 'lot_size', 'half_bathrooms'	
	);
	foreach ( $fields AS $field )
	{
		if ( $listing_data[$field] != '' )
		{
			$whereSQL .= " AND " . $field . " <= '" . $db->makeSafe( $listing_data[$field] ) . "'";	
		}	
	}
	
	// Send email alerts to all subscribers
	$sql = "
	SELECT email, code
	FROM " . ALERTS_TABLE . "
	WHERE 
		approved = '1'
		AND property_type = '" . $db->makeSafe( $listing_data['type'] ) . "'
		" . $whereSQL . "
	";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		while ( $f = $db->fetcharray( $q ) )
		{
			$temp_alert = $lang['Alert_Mail'];
			
			$lang['Alert_Subject'] = str_replace( '{website}', $conf['website_name'], $lang['Alert_Subject'] );
			
			// Replacing the variable names
			$temp_alert = str_replace( '{link}', URL . '/viewlisting.php?id=' . $listing_id, $temp_alert );
			$temp_alert = str_replace( '{unsubscribe}', URL . '/alerts.php?action=deactivate&code=' . $f['code'], $temp_alert );
			$temp_alert = str_replace( '{website}', $conf['website_name'], $temp_alert );
			
			// Send off the alert
			send_mailing( 
				$conf['general_e_mail'], 
				$conf['general_e_mail_name'], 
				$f['email'], 
				$lang['Alert_Subject'], 
				$temp_alert 
			);
		}
	}
}

// --------------------------------------------------------------------------
// num_gallery_images_check()
// Checks the number of images that exist for a given listing ID/temp listing ID
function num_gallery_images_check( $user_id, $listing_id, $temp_id = false )
{
	global $db, $conf;
	
	if ( $temp_id == false )
	{
		$listingSQL = " AND listingid = '" . $db->makeSafe( $listing_id ) . "' ";
	}
	else
	{
		$listingSQL = " AND temp_id = '" . $db->makeSafe( $listing_id ) . "' ";
	}
	
	$sql = "
	SELECT COUNT(*) AS total
	FROM " . GALLERY_TABLE . "
	WHERE 
		userid = '" . $db->makeSafe( $user_id ) . "' 
		" . $listingSQL . "
	";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		$f = $db->fetcharray( $q );
		return $f['total'];
	}
	
	return 0;
}

// --------------------------------------------------------------------------
// get_location_name()
// Returns the name of a location based on its ID
function get_location_name( $id = 0 )
{
	global $db;
	
	if ( $id != '' && $id != 0 )
	{
		$sql = "
		SELECT
			location_name, 
			location_parent
		FROM " . LOCATIONS_TABLE . "
		WHERE
			location_id = '" . $db->makeSafe( $id ) . "' ";
		$q = $db->query( $sql );
		if ( $db->numrows( $q ) > 0 )
		{
			$f = $db->fetchassoc( $q );
			return $f['location_name'];
		}
	}
	
	return false;
}

// --------------------------------------------------------------------------
// print_pdf_widget()
// Optimizes and displays content for printing/PDFing on-the-fly (courtesy printfriendly.com)
function print_pdf_widget()
{
	echo '
<script>var pfHeaderImgUrl = \'\';var pfHeaderTagline = \'\';var pfdisableClickToDel = 0;var pfHideImages = 0;var pfImageDisplayStyle = \'right\';var pfDisablePDF = 0;var pfDisableEmail = 0;var pfDisablePrint = 0;var pfCustomCSS = \'\';var pfBtVersion=\'1\';(function(){var js, pf;pf = document.createElement(\'script\');pf.type = \'text/javascript\';if(\'https:\' == document.location.protocol){js=\'https://pf-cdn.printfriendly.com/ssl/main.js\'}else{js=\'http://cdn.printfriendly.com/printfriendly.js\'}pf.src=js;document.getElementsByTagName(\'head\')[0].appendChild(pf)})();</script><a href="http://www.printfriendly.com" style="color:#6D9F00;text-decoration:none;" class="printfriendly" onclick="window.print();return false;" title="Printer Friendly and PDF"><img style="border:none;-webkit-box-shadow:none;box-shadow:none;margin:0 6px"  src="http://cdn.printfriendly.com/pf-print-icon.gif" width="16" height="15" alt="Print Friendly Version of this page" />Print <img style="border:none;-webkit-box-shadow:none;box-shadow:none;margin:0 6px" src="http://cdn.printfriendly.com/pf-pdf-icon.gif" width="12" height="12" alt="Get a PDF version of this webpage" />PDF</a>
	';
}

// --------------------------------------------------------------------------
// email_page()
// Pops up a box for emailing listings to a friend (courtesy instaemail.net)
function email_page()
{
	echo '
<script> (function(){var js, pf;pf = document.createElement(\'script\');pf.type = \'text/javascript\';if(\'https:\' == document.location.protocol){js=\'https://pf-cdn.printfriendly.com/javascripts/email/app.js\'}else{js=\'http://cdn.instaemail.net/js/app.js\'}pf.src=js;document.getElementsByTagName(\'head\')[0].appendChild(pf)})(); </script> <a href=\'#\' onclick=\'pfEmail.init()\' title=\'Email this page\' data-button-img=\'email-button\' id=\'instaemail-button\'>Email this page</a>
	';
}

// --------------------------------------------------------------------------
// check_logged_in()
// Verifies if the user is currently logged into their account (i.e., has an account)
function check_logged_in( $u_id = 0 )
{
	global $db, $session;
	
	if ( auth_check( $session->fetch( 'login' ), $session->fetch( 'password' ) ) )
	{
		return true;
	}
	
	return false;
}

// --------------------------------------------------------------------------
// check_paid_account()
// Verifies if the passed user ID belongs to a paid account holder (i.e. has a paid package)
function check_paid_account( $u_id = 0 )
{
	global $db, $session;
	
	$sql = "SELECT approved, package FROM " . USERS_TABLE . " WHERE u_id = '" . $db->makeSafe( $u_id ) . "'";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		$f = $db->fetchassoc( $q );
		
		if ( $f['package'] > 0 )
		{
			return true;
		}
	}
	
	return false;
}

// --------------------------------------------------------------------------
// get_locations()
function get_locations( $parent_id = 0 )
{
	global $db, $lang;
	global $session;
	$sid = $session->fetch('site_id');
  	$clause = isset($sid)? " site_id=".$sid:"1=1";

	$sql = "
	SELECT location_id, location_name
	FROM " . LOCATIONS_TABLE . "
	WHERE
		location_parent = '" . $parent_id . "' AND ".$clause."
	ORDER BY location_name ASC
	";
	$location_list = '<option value="">' . $lang['Select'] . '</option>';
	$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	if ( $db->numrows( $q ) > 0 )
	{
		
		while( $f = $db->fetcharray( $q ) )
		{
			$location_list .= '<option value="' . $f['location_id'] . '">' . $f['location_name'] . '</option>';
		}
	}
	return $location_list;
}

// --------------------------------------------------------------------------
// installed_languages()
// Returns the total number of languages that are currently installed in the /languages dir
function installed_languages()
{
	global $installed_languages;
	
	return count( $installed_languages );
}

// --------------------------------------------------------------------------
// generate_link()
// Returns a properly formatted link for listings/seller profiles based on input/SEO settings
function generate_link( $type = 'listing', $values )
{
	global $conf;
	
	if ( $type == 'listing' )
	{
		if ( is_array( $values ) )
		{
			$id = $values['listing_id'];
		
			if ( $conf['rewrite'] == 'ON' )
			{
				// Values needed for SEO-friendly URLs
				$type = rewrite( getnamebyid( TYPES_TABLE, $values['type'] ) );
				$title = rewrite( $values['title'] );
				
				// Generate the SEO-friendly URL
				$link = URL . '/Listing/' . $type . '/' . $id . '_' . $title . '.html';
			}
			else
			{
				$link = URL . '/viewlisting.php?id=' . $id;
			}
		}
	}
	elseif ( $type == 'seller' )
	{
		if ( is_array( $values ) )
		{
			$id = $values['u_id'];
			
			if ( $conf['rewrite'] == 'ON' )
			{
				$link = URL . '/Seller/' . $id . '.html';
			}
			else
			{
				$link = URL . '/viewuser.php?id=' . $id;
			}
		}
	}
	
	return $link;
}

// --------------------------------------------------------------------------
// payment_gateway()
// generates the form code for selecting packages, upgrades, etc. for a given CC merchant
function payment_gateway( $package_type, $user_id, $user_login )
{
	global $conf, $db, $session;
	global $session;
	$sid = $session->fetch('site_id');
  	$clause = isset($sid)? " site_id=".$sid:"1=1";
	$package_list = '';

	if ( $package_type == 'account' )
	{
		// Fetch all packages to show the paypal forms
		$sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE.' WHERE '.$clause;
		$r_packages = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		if ( $db->numrows( $r_packages ) > 0 )
		{		
			switch( $conf['paypal_mode'] )
			{
				case 'TEST':
					$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
				break;
			
				case 'LIVE':
					$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
				break;
			
				default:
					error('Configuration Error', 'paypal_mode can only be either LIVE or TEST. Check configuration screen.');
			}
		
			while( $f_packages = $db->fetcharray( $r_packages ) )
			{
				if ($conf['gateway'] == '2')
				{
					$package_list .= '
					<form method="post" action="' . $conf['2co_gateway'] . '">
					<input type="submit" class="submit" value="' . $f_packages['name'] . ' (' . $f_packages['price'] . ')">
					<input type="hidden" name="sid" value="' . $conf['2co_user_id'] . '">
					<input type="hidden" name="product_id" value="' . $f_packages['position'] . '">
					<input type="hidden" name="quantity" value="1">
					<input type="hidden" name="merchant_order_id" value="USER-' . $session->fetch('login') . '-' . $user_id . '-' . $f_packages['id'] . '">
					</form>
					<br />
					';
				}
				elseif ($conf['gateway'] == '1')
				{
					$package_list .= '
					<form action="'.$paypal_url.'" method="post">
					<input type="hidden" name="cmd" value="_xclick">
					<input type="hidden" name="business" value="' . $conf['paypal_email'] . '">
					<input type="hidden" name="return" value="' . URL . '/paypal-agents.php">
					<input type="hidden" name="notify_url" value="' . URL . '/paypal-agents.php">
					<input type="hidden" name="cancel_return" value="' . URL . '"> 
					<input type="hidden" name="invoice" value="' . date ( "YmdHis" ) . '">
					<input type="hidden" name="custom" value="' . $user_id . ':' . $f_packages['id'] . '">
					<input type="hidden" name="currency_code" value="' . $conf['paypal_currency'] . '">
					<input type="hidden" name="item_name" value="' . $f_packages['name'] . ' (' . $user_login . ')">
					<input type="hidden" name="quantity" value="1">
					<input type="hidden" name="amount" value="' . $f_packages['price'] . '">
					<input type="hidden" name="no_shipping" value="1">
					<input type="hidden" name="rm" value="2">
					<input type="submit" class="submit" value="' . $f_packages['name'] . '">
					</form>
					<br />
					';
				}
			}
		}
	}
	elseif ( $package_type == 'listing' )
	{	
    	// Fetch all listing packages
		$sql = "SELECT * FROM " . PACKAGES_TABLE .' WHERE '.$clause;
		$r_packages = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		if ( $db->numrows( $r_packages ) > 0 )
		{
			switch( $conf['paypal_mode'] )
			{
				case 'TEST':
					$paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
				break;
				case 'LIVE':
					$paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
				break;
				
				default:
					error('Configuration Error', 'paypal_mode can only be either LIVE or TEST. Check configuration screen.');
			}
			
			while ( $f_packages = $db->fetcharray( $r_packages ) )
			{			
				if ($conf['gateway'] == '2')
				{
					$package_list .= '
					<form method="post" action="' . $conf['2co_gateway'] . '">
					<input type="submit" class="submit" value="' . $f_packages['name'] . ' (' . $f_packages['price'] . ')">
					<input type="hidden" name="sid" value="' . $conf['2co_user_id'] . '">
					<input type="hidden" name="product_id" value="' . $f_packages['position'] . '">
					<input type="hidden" name="quantity" value="1">
					<input type="hidden" name="merchant_order_id" value="LISTING-' . $session->fetch('login') . '-' . $f['id'] . '-' . $f_packages['id'] . '">
					</form>
					<br />
					';
				}
				elseif ($conf['gateway'] == '1')
				{
					$package_list .= '		
					<form action="'.$paypal_url.'" method="post">
					<input type="hidden" name="cmd" value="_xclick">
					<input type="hidden" name="business" value="' . $conf['paypal_email'] . '">
					<input type="hidden" name="return" value="' . URL . '/paypal.php">
					<input type="hidden" name="notify_url" value="' . URL . '/paypal.php">
					<input type="hidden" name="cancel_return" value="' . URL . '">
					<input type="hidden" name="invoice" value="' . date ( "YmdHis" ) . '">
					<input type="hidden" name="custom" value="' . intval($_GET['id']) . ':' . $f_packages['id'] . '">
					<input type="hidden" name="currency_code" value="' . $conf['paypal_currency'] . '">
					<input type="hidden" name="item_name" value="' . $f_packages['name'] . ' (' . $session->fetch('login') . ')">
					<input type="hidden" name="quantity" value="1">
					<input type="hidden" name="amount" value="' . $f_packages['price'] . '">
					<input type="hidden" name="no_shipping" value="1">
					<input type="hidden" name="rm" value="2">
					<input type="submit" class="submit" value="' . $f_packages['name'] . '">
					</form>
					<br />
					';
				}
			}
		}
	}
	
	return $package_list;
}

// --------------------------------------------------------------------------
// price_ranges()
// Price range, outputs the values according to the configuration settings
// price_range_min TO price_range_max with the step of price_range_step
function price_ranges( $type = 'purchase' )
{
	global $conf;
	
	$price_ranges = array();

	if ( $type == 'purchase' )
	{
		$price_range_min = $conf['price_range_min'];
		$price_range_max = $conf['price_range_max'];
		$price_range_step = $conf['price_range_step'];
	}
	elseif ( $type == 'rent' )
	{
		$price_range_min = $conf['price_monthly_range_min'];
		$price_range_max = $conf['price_monthly_range_max'];
		$price_range_step = $conf['price_monthly_range_step'];
	}	
	
	// Loop through all possible values and put them into an array
	// Don't worry about putting them in an <option> list -- that's what the templates are for
	for ( $i = $price_range_min; $i <= $price_range_max; $i = $i + $price_range_step )
	{
		$price_ranges[] = $i;
	}
	
	return $price_ranges;
}

// --------------------------------------------------------------------------
// prepare_mailing()
// substitutes all placeholders with their appropriate values
function prepare_mailing( $text, $substitution_values = '' )
{
	global $db, $conf, $lang;
	
	// Loop through and replace all form values that may exist
	if ( is_array( $substitution_values ) && isset( $substitution_values ) )
	{		
		foreach( $substitution_values AS $key => $value )
		{
			// {key} is the placeholder in the language file
			$text = str_replace( '{' . $key . '}', $value, $text );
		}
	}
	
	return $text;
}

// --------------------------------------------------------------------------
// send_mailing()
// will pass off to PHPMailer class to send a mailing with the appropriate settings
function send_mailing( $from, $from_name, $to, $subject, $message, $bcc = '' )
{
	global $smtp, $conf, $lang;

	$mail = new PHPMailer( true );
	
	try
	{
		if ( PHPMAILER == '3' )
		{
			$mail->IsSMTP();
			$mail->Host = $smtp['host'];
			$mail->SMTPAuth = true;
			$mail->Username = $smtp['login'];
			$mail->Password = $smtp['password'];
		}
		elseif ( PHPMAILER == '2' )
		{
			$mail->IsSendmail();
		}
		else
		{
			// Just send via PHP's mail();
			// PHPMailer class will take care of this
		}
		
		$mail->From = $from;
		$mail->FromName = $from_name;
		$mail->AddAddress( $to );
		
		// BCC Recipients
		if ( $bcc != '' )
		{
			if ( is_array( $bcc ) )
			{
				foreach( $bcc AS $email )
				{
					$mail->AddBCC( $email );
				}
			}
			else
			{
				$mail->AddBCC( $bcc );
			}
		}
		
		$mail->Subject = $subject;
		$mail->Body = $message;
		$mail->AltBody = removehtml( $message );
		$mail->IsHTML( true );
	
		$mail->Send();
	}
	catch ( phpmailerException $e )
	{
		echo $e->errorMessage();
	}
	catch ( Exception $e )
	{
		echo $e->getMessage();
	}
}

// --------------------------------------------------------------------------
// error()
// error function outputs the error type and description
// ex. error ('Critical Error', 'Unable to connect to mysql database');
function error( $title, $message, $return = true, $kill_page = false )
{
	global $cookie_template;
	
	$tpl = PATH . '/templates/' . $cookie_template . '/tpl/error.tpl';
	$template = new Template;
	$template->load ( $tpl );
	
	$template->set( 'title', $title );
	$template->set( 'message', $message );
	
	if ( $return == true )
	{
		return $template->publish( true );
	}
	else
	{
		$template->publish();
	}
	
	if ( $kill_page == true )
	{
		die();
	}
}

// --------------------------------------------------------------------------
// success()
// success function outputs the success message
// ex. success( 'Your contact form has been submitted!' );
function success( $title, $message, $return = true, $kill_page = false )
{
	global $cookie_template;
	
	$tpl = PATH . '/templates/' . $cookie_template . '/tpl/success.tpl';
	$template = new Template;
	$template->load ( $tpl );
	
	$template->set( 'title', $title );
	$template->set( 'message', $message );
	
	if ( $return == true )
	{
		return $template->publish( true );
	}
	else
	{
		$template->publish();
	}
	
	if ( $kill_page == true )
	{
		die();
	}
}

// --------------------------------------------------------------------------
// VALID_EMAIL()
// Validates the email

 function valid_email( $address )
 {
  if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $address))
   return false;
  else
   return true;

 }


// --------------------------------------------------------------------------
// GETNAMEBYID()
// This function returns the name field according to the
// ID, suitable for simple tables like LOCATIONS, TYPES etc.

function getnamebyid( $name, $id )
 {

  global $db;
  global $cookie_language;
  global $language_in;

  if ( isset($id) && $id != 0 && !empty($id) )

   {

    $sql = 'SELECT ' . $language_in . ', name FROM ' . $name . ' WHERE id = "' . $id . '" LIMIT 1';
    $r = $db->query ($sql) or error ('Critical Error', mysql_error () );
    $f = $db->fetcharray ($r);
    
    if ($f[0] == '')
    	return $f['name'];
   	else
    	return $f[0];

   }

  else

   return '';

 }

 // --------------------------------------------------------------------------
// show_custom_value()
// This function returns the name field according to the
// ID, suitable for custom fields and values.
function show_custom_value ( $db, $id, $table )
{
  if ( $id == '' )
   {
       return '';
   }

   if ($table == VALUES_TABLE) {
   	$whereColumn = 'id';
   } else {
   	$whereColumn = 'field';
   }
   global $language_in;
   $field = $language_in;

    $sql = 'SELECT name, '.$field.' FROM ' . $table . ' WHERE ' . $whereColumn . ' = "' . $id . '" LIMIT 1';
    $r = $db->query ($sql) or error ('Critical Error', mysql_error () );
    $f = $db->fetcharray ($r);
    return (strlen(trim($f[$field])) == 0) ? $f['name'] : $f[$field];

}

// --------------------------------------------------------------------------
// VALIDATEWEBSITE()
// This function returns the URL with http:// prefix added
// if it's not there.

function validatewebsite ( $id, $url = '' )
 {

  global $lang;

  if ( !empty($url) && $url != 'n/a' )
   {
    if ( preg_match( "/http:\/\//", $url ) )
     $output = '<a href="' . $url . '" target="new" >';
    elseif ( preg_match( "/https:\/\//", $url ) )
     $output = '<a href="' . $url . '" target="new" >';
    elseif ( preg_match( "/ftp:\/\//", $url ) )
     $output = '<a href="' . $url . '" target="new" >';
    else
     $output = '<a href="http://' . $url . '" target="new">';
   }

  else

   $output = '<a href="#" target="new">';

  return $output;

 }

// --------------------------------------------------------------------------
// VALIDATEEMAIL()
// This function generates URL to the email form

function validateemail ( $id, $email = '', $listing = '' )
 {

  global $lang, $conf;

  if (empty($listing)) {
  if ($conf['rewrite'] == 'ON')
   $output = '<a href="' . URL . '/Mail/' . $id . '.html">';
  else
   $output = '<a href="' . URL . '/sendmessage.php?id=' . $id . '">';
  }
  else {
  if ($conf['rewrite'] == 'ON')
   $output = '<a href="' . URL . '/Mail/' . $id . '-' . $listing. '.html">';
  else
   $output = '<a href="' . URL . '/sendmessage.php?id=' . $id . '&listing=' . $listing . '">';
  }

  return $output;

 }

// --------------------------------------------------------------------------
// VIEWUSERLISTINGS()
// This function generates URLs to the user's listings

function viewuserlistings ( $id )
 {

  global $db;
  global $lang;
  global $conf;

  $sql = 'SELECT * FROM ' . PROPERTIES_TABLE . ' WHERE userid = "' . $id . '" AND approved = 1 ORDER BY title';
  $r = $db->query ( $sql ) or error ('Critical Error', mysql_error () );

  if ($db->numrows($r) > 0)
   $output = $lang['Realtor_Listings'];
  else
   $output = '';

  while ($f = $db->fetcharray ($r))
   {

    if ($conf['rewrite'] == 'ON')
     $output.= ' - <a href="' . URL . '/Listing/' . rewrite ( getnamebyid ( TYPES_TABLE, $f['type'] ) ) . '/' . $f['id'] . '_' . rewrite($f['title']) . '.html">' . $f['title'] . '</a><br />';
    else
     $output.= ' - <a href="' . URL . '/viewlisting.php?id=' . $f['id'] . '">' . $f['title'] . '</a><br />';

   }

  if ($db->numrows($r) > 0)
   $output.= '<br /> (<a href="' . URL . '/search.php?userid=' . $id . '"> ' . $lang['Realtor_View_All_Listings'] . '</a> )';

  return $output;

 }


// --------------------------------------------------------------------------
// SHOW_MULTIPLE()
// This function generates list of all the items
// in (x:y:z) format taken from the database 

function show_multiple ( $table_name, $selected = '' ){

 global $db;
 global $session;
 global $cookie_language;
 global $language_in;
 
 if ((isset($selected)) and (!is_array($selected)))
  $selected = explode (':' , $selected);
	$clause = "";
	if($session->fetch('site_id')!="")$clause = " WHERE  site_id=".$session->fetch('u_site_id');
  $sql = 'SELECT id, ' . $language_in . ', name FROM ' . $table_name . ' '.$clause.' ORDER BY ' . $language_in;
 $r = $db->query ($sql) or error ('Critical Error', mysql_error () );
  $output = '';
 $i = 1;
  while ($f = $db->fetcharray ($r) ) {
  if ((!is_array($selected) && $f['id'] == $selected) OR (is_array($selected) && in_array($f['id'], $selected))) {
   
   	if ($f[1] == '')
   		$f[1] = $f['name'];
   
   if (count($selected) == $i)
   {
   	$output.= $f[1] . ' ';
   } else {
    $output.= $f[1] . ', ';
    $i++;
   }
   }
  }
return $output;

}

// --------------------------------------------------------------------------
// UPDATE_CATEGORIES()
// This function increments the counter in the property_types
// table if listing was just added or changes the counters
// according to the type change if listing was updated or removes

function update_categories ( $was = '', $is = '' )
 {

  global $db;

  // if listing was just added $was variable is not set
  if (empty($was) & !empty($is))
   {
    $sql = 'UPDATE ' . TYPES_TABLE . ' SET counter=counter+1 WHERE id = "' . $is . '" LIMIT 1';
    $db->query ($sql) or error ('Critical Error' , mysql_error ());
   }

  // if listing was updated, all variables are set
  if (!empty($was) & !empty($is))
   {

    $sql = 'UPDATE ' . TYPES_TABLE . ' SET counter=counter-1 WHERE id = "' . $was . '" LIMIT 1';
    $db->query ($sql) or error ('Critical Error' , mysql_error ());

    $sql = 'UPDATE ' . TYPES_TABLE . ' SET counter=counter+1 WHERE id = "' . $is . '" LIMIT 1';
    $db->query ($sql) or error ('Critical Error' , mysql_error ());

   }

  // if listing was removed, $is is not set
  if (!empty($was) & empty($is))
   {
    $sql = 'UPDATE ' . TYPES_TABLE . ' SET counter=counter-1 WHERE id = "' . $was . '" LIMIT 1';
    $db->query ($sql) or error ('Critical Error' , mysql_error ());
   }

 }

// --------------------------------------------------------------------------
// NEWITEM()
// This function returns TRUE if this is a new listing
// (added during last $days days)

function newitem ( $table, $id, $days )
 {

  global $db, $lang;

  $sql = 'SELECT id FROM ' . $table . ' WHERE id = "' . $id . '" AND (TO_DAYS(NOW()) - TO_DAYS(date_added)) <= "' . $days . '" ';
  $r = $db->query ( $sql ) or error ('Critical Error', mysql_error () );

  if ($db->numrows ($r) > 0)
  return $lang['Listing_New_Mark'];

 }

// --------------------------------------------------------------------------
// UPDATEDITEM()
// This function returns TRUE if this is an updated listing
// (updated during last X days)

function updateditem ( $table, $id, $days )
 {

  global $db, $lang;

  $sql = 'SELECT id FROM ' . $table . ' WHERE id = "' . $id . '" AND (TO_DAYS(NOW()) - TO_DAYS(date_updated)) <= "' . $days . '" ';
  $r = $db->query ( $sql ) or error ('Critical Error', mysql_error () );

  if ($db->numrows ($r) > 0)
  return $lang['Listing_Updated_Mark'];

 }

// --------------------------------------------------------------------------
// TOPITEM()
// This function returns TRUE if this is a TOP user
// (have rating => 4.5 with at least 5 votes)

function topitem ( $rating, $votes )
 {

  global $lang;

  if ($votes > 0)
   {
    if (($rating/$votes) >= 4.5 && $votes >= 5)
    return $lang['Realtor_Top_Mark'];
   }

 }

// --------------------------------------------------------------------------
// FEATUREDITEM()
// This function returns TRUE if this is a featured listing

function featureditem ( $featured )
 {

  global $lang;

  if ($featured == 'A')
   {
    return $lang['Listing_Featured_Mark'];
   }

 }

// ----------------------------------------------------------------------
// REMOVEUSERLISTING()
// removes listing and all associated information from the database
//
function removeuserlisting( $listing_id )
{
	global $db;
	
	// Make sure this listing_id exists
	$sql = "
	SELECT listing_id
	FROM " . PROPERTIES_TABLE . "
	WHERE listing_id = '" . $listing_id . "'
	";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		$f = $db->fetcharray( $q );
		
		// Fetch the image names to remove them from the directory
		$sql = "SELECT image_name FROM " . GALLERY_TABLE . " WHERE listingid = '" . $listing_id . "'";
		$q2 = $db->query( $sql );
		if ( $db->numrows( $q2 ) > 0 )
		{
			while ( $f2 = $db->fetcharray( $q2 ) )
			{
				removelistingimage( 'gallery', $f2['image_name'] );
			}
		}
		
		// Delete images from the gallery table
		$sql = "DELETE FROM " . GALLERY_TABLE . " WHERE listingid = '" . $listing_id . "'";
		$q = $db->query( $sql );
		
		// Remove featured status
		$sql = "DELETE FROM " . FEATURED_TABLE . " WHERE id = '" . $listing_id . "'";
		$q = $db->query( $sql );
		
		// Delete the listing
		$sql = "DELETE FROM " . PROPERTIES_TABLE . " WHERE listing_id = '" . $listing_id . "'";
		$q = $db->query( $sql );
	}
}

//

function removelistingimage( $dir, $image_name )
{
	global $db;
	
	if ( file_exists( MEDIA_PATH . '/' . $dir . '/' . $image_name ) )
	{
		@unlink( MEDIA_PATH . '/' . $dir . '/' . $image_name );
	}
	
	if ($folder == 'images')
	{
		$sql = 'UPDATE ' . PROPERTIES_TABLE . ' SET image_uploaded = 0 WHERE id = "' . $id . '" LIMIT 1';
		$db->query($sql) or error ('Critical Error', mysql_error());
	}
}

function pmr_number_format($number, $decimals = 0)
{
    global $conf;
 
    $dec_point = $conf['decimal_point'];
    $thousands_sep = $conf['1000_separator'];
    
    return number_format($number, $decimals, $dec_point, $thousands_sep);
}

//------------------------------------------------------//
function pagination( $page_url, $page = 1, $total_results, $results_per_page )
//------------------------------------------------------//
{
	$total_pages = ceil( $total_results / $results_per_page );
	$pagination = array();

	if ( $page == 0 || $page == '' )
	{
		$page = 1;
	}
	
	// Add all parameters except page=
	// Account for any parameters that might be an array 
	if ( is_array( $_REQUEST ) )
	{
		$parameters = '';
		foreach ( $_REQUEST AS $key => $value )
		{
			if ( $value != '' && $key != 'page' && $key != 'submit' )
			{
				if ( is_array( $value ) )
				{
					$value = implode( ':', $value );	
				}
				$parameters .= '&' . $key . '=' . $value;
			}
		}
	}
		
	// Output all pages
	$start = $page - 4;
	$end = $page + 4;
	$num = 1;
	for ( $i = $start; $i <= $end; $i++ )
	{
		if ( $i > 0 && $i <= $total_pages )
		{
			$pagination[$num]['page'] = $i;
			$pagination[$num]['url'] = $page_url . '?page=' . $i . $parameters;
		}
		$num++;
	}
	
	return $pagination;
}

//---------------------------------------------------------//
function print_rf( $array, $formatted = true, $kill = false, $non_empty_only = false )
//---------------------------------------------------------//
{
	if ( $formatted == true )
	{
		echo '<pre>';
	}
	
	if ( $non_empty_only == true )
	{
		// Rebuild array so it only contains elements with values
		$new_array = array();
		foreach ( $array AS $key => $value )
		{
			if ( $value != '' )
			{
				$new_array[$key] = $value;
			}
		}
		$array = $new_array;
	}
	
	// Output the array
	print_r( $array );
	
	if ( $formatted == true )
	{
		echo '</pre>';
	}
	
	if ( $kill == true )
	{
		die();
	}
}

?>
