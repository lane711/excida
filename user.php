<?php

define( 'PMR', true );

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'] . ' - ' . $lang['Menu_User_Login'];

// If they are logged in
if (!auth_check($session->fetch('login'), $session->fetch('password')))
{
	header( 'Location: login.php' );
	exit();
}

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/user.tpl';
$template = new Template;
$template->load ( $tpl );

$template->set( '@add_listing', $lang['Menu_Submit_Property'] );
$template->set( '@control_panel', $lang['Menu_User_Login'] );
$template->set( '@edit_listings', $lang['Edit_Listings'] );
$sid = $session->fetch('site_id');
$clause = isset($sid)? " site_id=".$sid:"1=1";
// Grab all data for this seller
$sql = "
SELECT 
	u.*,	
	l1.location_name AS country,
	l2.location_name AS state,
	l3.location_name AS city,
	l1.location_id AS country_id,
	l2.location_id AS state_id,
	l3.location_id AS city_id
FROM " . USERS_TABLE . " u
LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = u.location_1
LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = u.location_2
LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = u.location_3
WHERE 
	u.login = '" . $session->fetch( 'login' ) . "' AND ".$clause."
";
$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
$f = $db->fetcharray( $q );

// Delete logo
if ( $_REQUEST['action'] == 'remove_logo' )
{
	// Get their image name to make sure this is the authorized image to remove
	$sql = "
	SELECT image
	FROM " . USERS_TABLE . "
	WHERE login = '" . $_SESSION['login'] . "'
	";
	$q2 = $db->query( $sql );
	$f2 = $db->fetcharray( $q2 );
	
	// Remove from DB
	$sql = "
	UPDATE " . USERS_TABLE . "
	SET	
		image = ''
	WHERE
		login = '" . $db->makeSafe( $_SESSION['login'] ) . "'
	";
	$q2 = $db->query( $sql );
	
	// Delete their image
	remove_image( 'photos' , $f2['image'] );
}

// If this account is approved and approval is required, allow them to continue
if ( ( $conf['approve_realtors'] == 'ON' && $f['approved'] == 1 ) || $conf['approve_realtors'] == 'OFF' )
{
	if ( $_POST['submit'] == true )
	{
		$form = $_POST;
		$errors = 0;
		
		// Keep newlines
		$form['realtor_description'] = safehtml_cms(@$_POST['realtor_description']);

		// If password was not changed we do not update the password field
		if ($_SESSION['password'] != $_POST['realtor_password'] )
		{
			$passwordin = md5( $_POST['realtor_password'] );
		}
		else
		{
			$passwordin = $session->fetch('password');
		}

		// Cut the description if JS is disabled
		$form['realtor_description'] = substr ($form['realtor_description'], 0, $conf['realtor_description_size']);
		
		if (empty($form['realtor_first_name']) || strlen($form['realtor_first_name']) < 2 )
		{
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_First_Name']; 
			$errors++;
		}
		
		if (empty($form['realtor_last_name']) || strlen($form['realtor_last_name']) < 2 )
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_Last_Name']; 
			$errors++;
		}
		
		if ( empty( $form['location1'] ) )
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['City']; 
			$errors++;
		}
		
		if (empty($form['realtor_address']) || strlen($form['realtor_address']) < 4 )
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_Address']; 
			$errors++;
		}
		
		if (empty($form['realtor_phone']) || strlen($form['realtor_phone']) < 4 )
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_Phone']; 
			$errors++;
		}
		
		if (empty($form['realtor_e_mail']) || strlen($form['realtor_e_mail']) < 4 || !valid_email($form['realtor_e_mail']))
		{ 
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_e_mail']; 
			$errors++;
		}
		
		if (empty($form['realtor_password']) || strlen($form['realtor_password']) < 4 )
		{
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_Password']; 
			$errors++;
		}
		
		if ( preg_match('/[^A-Za-z0-9]+$/', $form['realtor_password']))
		{ 
			$error_message = $lang['Password_Incorrect']; 
			$errors++;
		}
		
		// Check if this email is already used
		if ( $conf['allow_same_e_mail'] == 'ON' )
		{
			$sql = 'SELECT id FROM ' . USERS_TABLE . ' WHERE email = "' . $form['realtor_e_mail'] . '" AND login != "' . $session->fetch('login') . '"';
			$r = $db->query($sql) or error ('Critical Error', mysql_error () );
			if ($db->fetcharray($r) > 0 )
			{
				$error_message = $lang['Field_Empty'] . ': ' . $lang['Email_User'];
				$errors++;
			}
		}

		// If errors found we print out the number of errors
		if ( $errors > 0 )
		{
			$output_message = error( $lang['Error'], $error_message, true );
		}
		else
		{
			$proceed = true;
		
			// If uploading a new logo
			if ( $_FILES['submit_logo']['tmp_name'] != '' )
			{
				if ( upload_image( 'photos', $session->fetch( 'u_id' ), $_FILES['submit_logo'] ) == false )
				{
					$output_message = error( $lang['Error'], $lang['Realtor_Image_NOT_Uploaded'], true );
					$proceed = false;	
				}
				else
				{
					$imageSQL = ", image_uploaded = '1' ";
				}
			}
			
			// Create a mysql query
			if ( $proceed == true )
			{
				// Strip characters from the phone, etc. fields (numbers only)
				$form['phone'] = preg_replace( '/[^0-9]+/', '', $form['phone'] );
				$form['fax'] = preg_replace( '/[^0-9]+/', '', $form['fax'] );
				$form['mobile'] = preg_replace( '/[^0-9]+/', '', $form['mobile'] );
			
				$sql = "
				UPDATE " . USERS_TABLE . "
				SET 
					first_name = '" . $db->makeSafe( $form['realtor_first_name'] ) . "', 
					last_name = '" . $db->makeSafe( $form['realtor_last_name'] ) . "',
					company_name = '" . $db->makeSafe( $form['realtor_company_name'] ) . "',
					description = '" . $db->makeSafe( $form['realtor_description'] ) . "',
					location_1 = '" . $db->makeSafe( $form['location1'] ) . "',
					location_2 = '" . $db->makeSafe( $form['location2'] ) . "',
					location_3 = '" . $db->makeSafe( $form['location3'] ) . "',
					zip = '" . $db->makeSafe( $form['realtor_zip_code'] ) . "',
					address = '" . $db->makeSafe( $form['realtor_address'] ) . "',
					phone = '" . $db->makeSafe( $form['realtor_phone'] ) . "',
					fax = '" . $db->makeSafe( $form['realtor_fax'] ) . "',
					mobile = '" . $db->makeSafe( $form['realtor_mobile'] ) . "',
					email = '" . $db->makeSafe( $form['realtor_e_mail'] ) . "',
					website = '" . $db->makeSafe( $form['realtor_website'] ) . "',
					date_updated = '" . date('Y-m-d') . "',
					ip_updated = '" . $_SERVER['REMOTE_ADDR'] . "',
					password = '" . $passwordin . "'
					" . $imageSQL . "
				WHERE
					login = '" . $session->fetch('login') . "'
				";
				$q2 = $db->query( $sql ) or error('Critical Error', mysql_error() );

				// Change current session password if user has changed his password in the form
				$session->varunset('password');
				$session->set('password', $passwordin);
				
				// Let them know these changes are pending review or updated depending upon configuration options
				if ($conf['approve_realtors'] == 'ON')
				{
					$output_message = success( $lang['Success'], $lang['Realtor_Listing_Updated_Approve'], true );
				}
				else
				{
					$output_message = success( $lang['Success'], $lang['Realtor_Listing_Updated'], true );
				}
			}
		}
	}
}
else
{
	$output_message = error( $lang['Not_Approved'], $lang['Not_Approved'], true );
}

// Grab all data for this seller
$sql = "
SELECT 
	u.*,	
	l1.location_name AS country,
	l2.location_name AS state,
	l3.location_name AS city,
	l1.location_id AS country_id,
	l2.location_id AS state_id,
	l3.location_id AS city_id
FROM " . USERS_TABLE . " u
LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = u.location_1
LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = u.location_2
LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = u.location_3
WHERE 
	u.login = '" . $session->fetch( 'login' ) . "'
";
$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
$f = $db->fetcharray( $q );

// Fetching the listings number from the table
$sql = 'SELECT listing_id FROM ' . PROPERTIES_TABLE . ' WHERE userid = "' . $f['u_id'] . '"';
$r_listings = $db->query( $sql );
$total_listings = $db->numrows( $r_listings );

// Fetch the package and the number of listings allowed
if ( $f['package'] != '' && $f['package'] != '0' )
{
	// Fetch Packages
	$sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE  . ' WHERE id = "' . $f['package'] . '" LIMIT 1';
	$r_package = $db->query ( $sql );
	$f_package = $db->fetcharray ( $r_package );
}
else
{
	$f_package['listings'] = $conf['free_listings'];
}

$package_selection = payment_gateway( 'account', $f['u_id'], $session->fetch('login') );

// Define the form variables if the form was not updated
if ( !isset( $form ) )
{
	$form = array();
	
	$form['realtor_first_name'] = $f['first_name'];
	$form['realtor_last_name'] = $f['last_name'];
	$form['realtor_company_name'] = $f['company_name'];
	$form['realtor_description'] = $f['description'];
	$form['realtor_city'] = $f['city'];
	$form['realtor_address'] = $f['address'];
	$form['realtor_zip_code'] = $f['zip'];
	$form['realtor_phone'] = $f['phone'];
	$form['realtor_fax'] = $f['fax'];
	$form['realtor_mobile'] = $f['mobile'];
	$form['realtor_e_mail'] = $f['email'];
	$form['realtor_website'] = $f['website'];
	$form['realtor_password'] = $f['password'];
	$form['realtor_state'] = $f['state'];
}
else
{
	// Set new password if the form was changed
	$form['realtor_password'] = $passwordin;
}

// Fetch all packages if site is running in portal mode
if ( $conf['site_mode'] == 2 )
{
	$custom['show_packages'] = true;
	$package_list = payment_gateway( 'account', $f['u_id'], $_SESSION['login'] );
}

// Look up any package selections they currently have
if ($f['package'] != '0' && $f['package'] != '')
{
	$sql = 'SELECT * FROM ' . FEATURED_AGENTS_TABLE . ' WHERE id = ' . $f['u_id'];
	$r_featured = $db->query($sql) or error ('Critical Error', mysql_error () );
	$f_featured = $db->fetcharray($r_featured) or error ('Critical Error', mysql_error () );
	
	$sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE . ' WHERE id = ' . $f['package'];
	$r_packages = $db->query($sql) or error ('Critical Error', mysql_error () );
	$f_packages = $db->fetcharray($r_packages) or error ('Critical Error', mysql_error () );
	
	$package_name = $f_packages['name'];
	$package_date = printdate( $f_featured['end_date'] );
}
else
{
	$package_name = 'FREE';
	$package_date = 'Never';
}

// Default profile image
if ( $f['image'] != '' )
{
	$custom['image'] = $f['image'];
	$images = get_images( 'photos', $f['u_id'], 100, 74, 1, 1 );
	$image = $images[0];
}
else
{
	$custom['image'] = '';
	$image = $lang['No_Image'];
}

// Location multi-drop down
$custom['location1_name'] = $f['country'];
$custom['location1_id'] = $f['country_id'];

$custom['location2_name'] = $f['state'];
$custom['location2_id'] = $f['state_id'];

$custom['location3_name'] = $f['city'];
$custom['location3_id'] = $f['city_id'];

// Language-specific values
$template->set( '@heading', $lang['Menu_User_Login'] );
$template->set( '@navigation', $navigation );
$template->set( '@output_message', $output_message );
$template->set( 'packages_header', $lang['Upgrade_Account'] );

$template->set( '@personal_info', $lang['Personal_Info'] );
$template->set( '@contact_info', $lang['Contact_Info'] );
$template->set( '@image_info', $lang['Image_Info'] );

$template->set( '@firstname', $lang['Realtor_First_Name'] );
$template->set( '@lastname', $lang['Realtor_Last_Name'] );
$template->set( '@submit_logo', $lang['Realtor_Submit_Logo'] );
$template->set( '@remove_logo', $lang['Realtor_Submit_Logo_Remove'] );
$template->set( '@company', $lang['Realtor_Company_Name'] );
$template->set( '@description', $lang['Realtor_Description'] );
$template->set( '@address', $lang['Realtor_Address'] );
$template->set( '@phone', $lang['Realtor_Phone'] );
$template->set( '@fax', $lang['Realtor_Fax'] );
$template->set( '@password', $lang['Realtor_Password'] );
$template->set( '@mobile', $lang['Realtor_Mobile'] );
$template->set( '@email', $lang['Realtor_e_mail'] );
$template->set( '@url', $lang['Realtor_Website'] );
$template->set( '@location', $lang['Location'] );
$template->set( '@zip', $lang['Zip_Code'] );
$template->set( '@submit', $lang['Save_Changes'] );
$template->set( '@description_limit', $lang['Characters_Left'] );
$template->set( '@updated', $lang['Listing_Updated_Date'] );
$template->set( '@added', $lang['Listing_Added_Date'] );
$template->set( '@hits', $lang['Hits'] );
$template->set( '@package_name', $lang['Admin_Packages_Name'] );
$template->set( '@package_date', $lang['Admin_Listing_Expire'] );

// Form contents
$template->set( 'total_listings', $total_listings );
$template->set( 'firstname', $form['realtor_first_name'] );
$template->set( 'lastname', $form['realtor_last_name'] );
$template->set( 'company', $form['realtor_company_name'] );
$template->set( 'description', $form['realtor_description'] );
$template->set( 'address', $form['realtor_address'] );
$template->set( 'fax', $form['realtor_fax'] );
$template->set( 'password', $form['realtor_password'] );
$template->set( 'mobile', $form['realtor_mobile'] );
$template->set( 'location1', get_locations() );
$template->set( 'email', $form['realtor_e_mail'] );
$template->set( 'url', $form['realtor_website'] );
$template->set( 'phone', $form['realtor_phone'] );
$template->set( 'zip', $form['realtor_zip_code'] );
$template->set( 'image', $image );
$template->set( 'description_limit', $conf['realtor_description_size'] );
$template->set( 'hits', $f['hits'] );
$template->set( 'updated', printdate( $f['date_updated'] ) );
$template->set( 'added', printdate( $f['date_added'] ) );
$template->set( 'package_name', $package_name );
$template->set( 'package_date', $package_date );
$template->set( 'navigation', $navigation );
$template->set( 'output_message', $output_message );
$template->set( 'package_selection', $package_selection );
$template->set( 'package_list', $package_list );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>