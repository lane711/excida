<?php

define( 'PMR', 'true' );
$page = 'register';

include './config.php';
include PATH . '/defaults.php';

// Check if registrations are allowed
if ( $conf['allow_registration'] == 'OFF' )
{
	header( 'Location: ' . URL . '/login.php' );
	exit();	
}

// Title of page
$title = $conf['website_name_short'] . ' - ' . $lang['Menu_Submit_Listing'];

// Header template
include PATH . '/templates/' . $cookie_template . '/header.php';

$output_message = '';
$error_message = '';
$errors = 0;

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/register.tpl';
$template = new Template;
$template->load ( $tpl );

if ( $_POST['submit'] == true )
{
	$form = array();
	
	// Strip anything that shouldn't be submitted
	$form = array_map( 'safehtml', $_POST );
	
	$errors = 0;
	
	if ( $conf['captcha_private_key'] != '' && $conf['captcha_public_key'] != '' && $conf['captcha_status'] == 'ON' )
	{
		if ( captcha_check() == false )
		{
			$error_message = $lang['Captcha_Fail'];
			$errors++;
		}
	}
	
	// Check for the empty or incorrect required fields
	/*
	if ( empty( $form['first_name'] ) || strlen( $form['first_name'] ) < 2 )
	{
		$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_First_name'];
		$errors++;
	}
	
	if ( empty( $form['last_name'] ) || strlen( $form['last_name'] ) < 2 )
	{
		$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_Last_Name'];
		$errors++;
	}
	
	if ( empty( $form['location1'] ) )
	{ 
		$error_message = $lang['Field_Empty'] . ': ' . $lang['City']; 
		$errors++;
	}
	
	if ( empty( $form['address'] ) || strlen( $form['address'] ) < 4 )
	{ 
		$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_Address']; 
		$errors++;
	}
	
	if ( empty( $form['phone'] ) || strlen( $form['phone'] ) < 4 )
	{ 
		$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_Phone']; 
		$errors++;
	}
	*/
	
	if ( empty( $form['email'] ) || strlen( $form['email'] ) < 4  || !valid_email( $form['email'] ) )
	{ 
		$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_e_mail']; 
		$errors++;
	}
	
	if ( empty( $form['login'] ) || strlen( $form['login'] ) < 4 )
	{ 
		$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_Login']; 
		$errors++;
	}
	
	if ( empty( $form['password'] ) || strlen( $form['password'] ) < 4 )
	{ 
		$error_message = $lang['Field_Empty'] . ': ' . $lang['Realtor_Password']; 
		$errors++;
	}
	
	// Check if login is already exist
	$sql = 'SELECT login FROM ' . USERS_TABLE . ' WHERE login = "' . safehtml( $form['login'] ) . '"';
	$r = $db->query( $sql ) or error ('Critical Error', mysql_error() );
	
	if ( $db->numrows( $r ) > 0 )
	{ 
		$error_message = $lang['Login_Used']; 
		$errors++;
	}
	
	// Check if email is banned
	$sql = 'SELECT * FROM ' . BANS_TABLE . ' WHERE name = "' . $form['email'] . '" LIMIT 1';
	$r = $db->query( $sql ) or error ('Critical Error', mysql_error() );
	if ($db->numrows($r) > 0 )
	{ 
		$error_message = $lang['e_mail_Banned']; 
		$errors++;
	}
	
	// Check if this email is already used
	if ( strcasecmp( $conf['allow_same_e_mail'], 'OFF' ) )
	{
		$sql = 'SELECT id FROM ' . USERS_TABLE . ' WHERE email = "' . $form['email'] . '"';
		$r = $db->query($sql) or error ('Critical Error', mysql_error() );
		if ( $db->fetcharray( $r ) > 0 )
		{ 
			$error_message = $lang['Email_User'];
			$errors++;
		}
	}

	// Check if both passwords are equal
	if ( $form['password'] != $form['password_confirm'] )
	{ 
		$error_message = $lang['Passwords_Missmatch']; 
		$errors++;
	}
	
	if ( $errors > 0 )
	{
		$output_message = error( $lang['Error'], $error_message, true );
	}
	else
	{
		if ( $conf['approve_realtors'] == 'ON' )
		{
			$approved = 0;
		}
		else
		{
			$approved = 1;
		}

		// Generate random number for the email validation link
		$number = rand( 1000000, 9999999 );
		
		// Cut the description size to the allowed minimum set in config
		$form['description'] = substr( $form['description'], 0, $conf['realtor_description_size'] );
	
		// Strip characters from the phone, etc. fields (numbers only)
		$form['phone'] = preg_replace( '/[^0-9]+/', '', $form['phone'] );
		$form['fax'] = preg_replace( '/[^0-9]+/', '', $form['fax'] );
		$form['mobile'] = preg_replace( '/[^0-9]+/', '', $form['mobile'] );
	
		$sql = "
		INSERT INTO " . USERS_TABLE . "
		(
			approved, 
			first_name, 
			last_name, 
			company_name,
			description, 
			location_1, 
			location_2,
			location_3, 
			zip, 
			address,
			phone, 
			fax, 
			mobile, 
			email, 
			website, 
			date_added, 
			ip_added, 
			login, 
			password, 
			number
		) 
		VALUES
		(
			'" . $approved . "',
			'" . $db->makeSafe( $form['first_name'] ) . "', 
			'" . $db->makeSafe( $form['last_name'] ) . "', 
			'" . $db->makeSafe( $form['company'] ) . "', 
			'" . $db->makeSafe( $form['description'] ) . "', 
			'" . $db->makeSafe( $form['location1'] ) . "', 
			'" . $db->makeSafe( $form['location2'] ) . "', 
			'" . $db->makeSafe( $form['location3'] ) . "', 
			'" . $db->makeSafe( $form['zip'] ) . "', 
			'" . $db->makeSafe( $form['address'] ) . "', 
			'" . $db->makeSafe( $form['phone'] ) . "', 
			'" . $db->makeSafe( $form['fax'] ) . "', 
			'" . $db->makeSafe( $form['mobile'] ) . "', 
			'" . $db->makeSafe( $form['email'] ) . "',
			'" . $db->makeSafe( $form['url'] ) . "', 
			'" . date( 'Y-m-d' ) . "',
			'" . $_SERVER['REMOTE_ADDR'] . "',
			'" . $db->makeSafe( $form['login'] ) . "',
			'" . md5( $form['password'] ) . "',
			'" . $number . "'
		)
		";
		$db->query( $sql ) or error( 'Critical Error', mysql_error() );
		$id = $db->getLastID();

		if ( strtoupper( $_POST['security'] ) == $_SESSION['random'] )
		{
			$session->varunset( 'random' );
		}
		
		if ($conf[ 'approve_realtors'] == 'ON' )
		{
			$output_message = success( $lang['Success'], $lang['Realtor_Listing_Submitted_Approve'], true );
			
			$email_values = array(
				'first_name' => $form['first_name'],
				'last_name' => $form['last_name'],
				'company' => $form['company'],
				'address' => $form['address'],
				'website_name' => $form['website_name']
			);
			
			$lang['Admin_Realtor_Notification_Mail'] = prepare_mailing( $lang['Admin_Realtor_Notification_Mail'], $email_values );
			
			send_mailing( 
				$conf['general_e_mail'], 
				$conf['general_e_mail_name'], 
				$conf['general_e_mail'], 
				$lang['Admin_Realtor_Notification_Subject'], 
				$lang['Admin_Realtor_Notification_Mail'] 
			);
		}
		else
		{
			$output_message = success( $lang['Success'], $lang['Realtor_Listing_Submitted'], true );
		}
		
		if ( $conf['approve_realtors'] == 'OFF' )
		{
			$url = '<a href="' . URL . '/validate.php?id=' . $number . '">' . URL . '/validate.php?id=' . $number . '</a>';
			$verify = $lang['verify'] . "<br />\r\n" . $url;
		}
		
		$email_values = array(
			'website_name' => $conf['website_name'],
			'first_name' => $form['first_name'],
			'last_name' => $form['last_name'],
			'company' => $form['company'],
			'address' => $form['address'],
			'login' => $form['login'],
			'password' => $form['password'],
			'verify' => $verify
		);

		$lang['Realtor_Notification_Subject'] = prepare_mailing( $lang['Realtor_Notification_Subject'], $email_values );
		$lang['Realtor_Notification_Mail'] = prepare_mailing( $lang['Realtor_Notification_Mail'], $email_values );

		send_mailing( 
			$conf['general_e_mail'], 
			$conf['general_e_mail_name'], 
			$form['email'], 
			$lang['Realtor_Notification_Subject'], 
			$lang['Realtor_Notification_Mail'] 
		);

		$custom['hide_form'] = true;
		
		// Fetch all packages if site is running in portal mode
		if ( $conf['site_mode'] == 2 )
		{
			$custom['show_packages'] = true;
			$package_list = payment_gateway( 'account', $id, $form['login'] );
		}
	}
}

// General
$template->set( 'contact_form', $contact_form );
$template->set( 'output_message', $output_message );
$template->set( 'register_text', $lang['Register_Text'] );
$template->set( 'header', $lang['Menu_Submit_Listing'] );
$template->set( 'register', $lang['Realtor_Submit'] );
$template->set( 'package_list', $package_list );
$template->set( 'packages_header', $lang['Upgrade_Account'] );

// Contact field labels
$template->set( '@email', $lang['Realtor_e_mail'] );
$template->set( '@first_name', $lang['Realtor_First_Name'] );
$template->set( '@last_name', $lang['Realtor_Last_Name'] );
$template->set( '@company', $lang['Realtor_Company_Name'] );
$template->set( '@description', $lang['Realtor_Description'] );
$template->set( '@address', $lang['Realtor_Address'] );
$template->set( '@location', $lang['Location'] );
$template->set( '@zip', $lang['Zip_Code'] );
$template->set( '@phone', $lang['Realtor_Phone'] );
$template->set( '@mobile', $lang['Realtor_Mobile'] );
$template->set( '@fax', $lang['Realtor_Fax'] );
$template->set( '@url', $lang['Realtor_Website'] );
$template->set( '@login', $lang['Realtor_Login'] );
$template->set( '@password', $lang['Realtor_Password'] );
$template->set( '@password_confirm', $lang['Realtor_Password_Repeat'] );
$template->set( '@math', $lang['Math'] . ' ' . $_SESSION['rand_1'] . ' + ' . $_SESSION['rand_2'] );

// Contact field values
$template->set( 'email', $_REQUEST['email'] );
$template->set( 'first_name', $_REQUEST['first_name'] );
$template->set( 'last_name', $_REQUEST['last_name'] );
$template->set( 'company', $_REQUEST['company'] );
$template->set( 'description', $_REQUEST['description'] );
$template->set( 'address', $_REQUEST['address'] );
$template->set( 'location1', get_locations() );
$template->set( 'zip', $_REQUEST['zip'] );
$template->set( 'phone', $_REQUEST['phone'] );
$template->set( 'mobile', $_REQUEST['mobile'] );
$template->set( 'fax', $_REQUEST['fax'] );
$template->set( 'url', $_REQUEST['url'] );
$template->set( 'login', $_REQUEST['login'] );
$template->set( 'password', $_REQUEST['password'] );
$template->set( 'password_confirm', $_REQUEST['password_confirm'] );
$template->set( 'math', $_REQUEST['math'] );

$template->publish();

// Footer template
include PATH . '/templates/' . $cookie_template . '/footer.php';

?>