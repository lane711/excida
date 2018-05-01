<?php

define( 'PMR', 'true' );
$page = 'send_message';

include 'config.php';
include PATH . '/defaults.php';

// Title of page
$title = $conf['website_name_short'] . ' - ' . $lang['Mailer'];

// Header template
include PATH . '/templates/' . $cookie_template . '/header.php';

$output_message = '';
$error_message = '';
$contact_form = '';
$custom['display_form'] = true;

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/sendmessage.tpl';
$template = new Template;
$template->load ( $tpl );

if ( $_REQUEST['u_id'] != '' || $_REQUEST['listing_id'] != '' )
{	
	if ( $_POST['submit'] == true )
	{
		$form = $_POST;
		
		$errors = 0;
		
		// Email field
		if ( empty( $form['email'] ) || strlen( $form['email'] ) < 4  || !valid_email( $form['email'] ) )
		{
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Mailer_e_mail'];
			$errors++;
		}
		
		// Check if an account is required to contact a seller
		
		// At least free account required, but they don't have an account
		if ( $conf['contact_agents'] == '2' )
		{
			// They need at least a free account
		  	if ( !auth_check( $session->fetch( 'login' ), $session->fetch( 'password' ) ) )
		  	{
				$error_message = $lang['Agent_Details_Account_Only'];
				$errors++;
			}
		}
		// Paid account only, but they don't have a paid account
		elseif ( $conf['contact_agents'] == '3' )
		{
			// They need a paid account
		  	if ( auth_check( $session->fetch( 'login' ), $session->fetch( 'password' ) ) )
		  	{
				// Fetching the user ID from the user's table
				$sql = 'SELECT package FROM ' . USERS_TABLE . ' WHERE login = "' . $session->fetch('login') . '"';
				$r = $db->query( $sql );
				$f = $db->fetcharray( $r );
				
				if ( $f['package'] == 0 )
				{
					$error_message = $lang['Agent_Details_Pay_Only'];
					$errors++;
				}
			}
			else
			{
				$error_message = $lang['Agent_Details_Pay_Only'];
				$errors++;	
			}
		}
		
		// Check if email is banned
		$sql = 'SELECT * FROM ' . BANS_TABLE . ' WHERE name = "' . $form['e_mail'] . '" LIMIT 1';
		$r = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		if ( $db->numrows( $r ) > 0 )
		{
			$error_message = $lang['e_mail_Banned'];
			$errors++;
		}
		
		// Captcha
		// Only validate captcha if we're on the contact page (otherwise, skip it)
		if ( $_REQUEST['external'] == false && $conf['captcha_private_key'] != '' && $conf['captcha_public_key'] != '' && $conf['captcha_status'] == 'ON' )
		{
			if ( captcha_check() == false )
			{
				$error_message = $lang['Captcha_Fail'];
				$errors++;
			}
		}
		
		// Protect against flooding/abuse of this form
		if ( (time() - $session->fetch( 'mail_time') ) < 30 )
		{ 
			$error_message = $lang['Flood_Detected'];
			$errors++;
		}
		
		// Body of message is required and must be at least 4 characters long
		if ( empty( $form['message'] ) || strlen( $form['message'] ) < 4 )
		{
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Mailer_Message'];
			$errors++;
		}
		
		// Name field
		if ( empty( $form['name'] ) || strlen( $form['name'] ) < 2 )
		{
			$error_message = $lang['Field_Empty'] . ': ' . $lang['Mailer_Name'];
			$errors++;
		}
		
		if ( $errors > 0 )
		{
			$output_message = error( $lang['Error'], $error_message, true );
		}
		else
		{
			$lang['Mailer_Subject'] = str_replace( '{website_name}', $conf['website_name'], $lang['Mailer_Subject'] );
			$lang['Mailer_Subject'] = str_replace( '{user_e_mail}', $form['email'], $lang['Mailer_Subject'] );
			
			// Get the details of this listing and the seller
			if ( $_REQUEST['u_id'] != '' && $_REQUEST['listing_id'] == '' )
			{
				$sql = "
				SELECT
					first_name, email, last_name
				FROM " . USERS_TABLE . "
				WHERE
					approved = 1
					AND u_id = '" . $db->makeSafe( $_REQUEST['u_id'] ) . "'
				";			
			}
			elseif ( $_REQUEST['listing_id'] != '' )
			{
				$sql = "
				SELECT
					listing_id, title, first_name, email, last_name, type
				FROM " . PROPERTIES_TABLE  . " AS p
				LEFT JOIN " . USERS_TABLE . " u ON u.u_id = p.userid
				WHERE 
					u.approved = 1 
					AND listing_id = '" . $db->makeSafe( $_REQUEST['listing_id'] ) . "'
				";
			}
			$q = $db->query( $sql );
			if ( $db->numrows( $q ) > 0 )
			{
				$f = $db->fetcharray( $q );
				
				if ( $_REQUEST['listing_id'] != '' )
				{
					// We're sending about a specific listing
					$link = generate_link( 'listing', $f );
					
					$lang['Contact_Seller'] = str_replace( '{link}', $link, $lang['Contact_Seller'] );
					$lang['Contact_Seller'] = str_replace( '{title}', $f['title'], $lang['Contact_Seller'] );
				}
				else
				{
					// General request, this information isn't available
					$lang['Contact_Seller'] = str_replace( '{link}', '', $lang['Contact_Seller'] );
					$lang['Contact_Seller'] = str_replace( '{title}', '', $lang['Contact_Seller'] );
				}
				
				$lang['Contact_Seller'] = str_replace( '{name}', $f['first_name'], $lang['Contact_Seller'] );
				$lang['Contact_Seller'] = str_replace( '{website}', $conf['website_name'], $lang['Contact_Seller'] );
				$lang['Contact_Seller'] = str_replace( '{message}', $form['message'], $lang['Contact_Seller'] );				
				
				send_mailing( 
					$form['email'], 
					$form['email'], 
					$f['email'], 
					$lang['Mailer_Subject'], 
					$lang['Contact_Seller'] 
				);
				
				// Show the Thank you message
				$lang['Mailer_Sent'] = str_replace( '{name}', $f['first_name'] . ' ' . $f['last_name'], $lang['Mailer_Sent'] );
				
				$output_message = success( $lang['Success'], $lang['Mailer_Sent'], true );
	
				// Prevent flooding/abuse of this mail form by saving when this was last submitted successfully
				$session->set( 'mail_time', time() );
			
				// Don't load the contact form template again
				$custom['display_form'] = false;	
			}
			else
			{
				$output_message = error( $lang['Error'], $lang['No_Listing'], true );
			}
		}
	}
}
else
{
	$output_message = error( $lang['Error'], $lang['No_Listing'], true );
}

$template->set( '@email', $lang['Realtor_e_mail'] );
$template->set( '@phone', $lang['Realtor_Phone'] );
$template->set( '@address', $lang['Realtor_Address'] );
$template->set( 'contact_form', $contact_form );
$template->set( 'output_message', $output_message );
$template->set( 'contact_text', $lang['Contact_Text'] );
$template->set( 'header', $lang['Mailer'] );
$template->set( 'latitude', $conf['latitude'] );
$template->set( 'longitude', $conf['longitude'] );

// Contact Form
$template->set( '@name', $lang['Mailer_Name'] );
$template->set( '@email', $lang['Realtor_e_mail'] );
$template->set( '@message', $lang['Mail_Friend_Message'] );
$template->set( 'name', $_REQUEST['name'] );
$template->set( 'email', $_REQUEST['email'] );
$template->set( 'message', $_REQUEST['message'] );
$template->set( 'send', $lang['Admin_Mailer_Submit'] );
$template->set( 'math', $_REQUEST['math'] );
$template->set( '@math', $lang['Math'] . ' ' . $_SESSION['rand_1'] . ' + ' . $_SESSION['rand_2'] );

$template->set( 'conf_address1', $conf['address1'] );
$template->set( 'conf_city', $conf['city'] );
$template->set( 'conf_state', $conf['state'] );
$template->set( 'conf_country', $conf['country'] );
$template->set( 'conf_phone', $conf['phone'] );
$template->set( 'conf_email', $conf['general_e_mail'] );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>