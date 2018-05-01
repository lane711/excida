<?php

define( 'PMR', 'true' );
$page = 'contact';

include './config.php';
include PATH . '/defaults.php';

// Title of page
$title = $conf['website_name_short'] . ' - ' . $lang['Mailer'];

// Header template
include PATH . '/templates/' . $cookie_template . '/header.php';

$output_message = '';
$error_message = '';
$contact_form = '';
$custom['display_form'] = true;

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/contact.tpl';
$template = new Template;
$template->load ( $tpl );

if ( $_POST['submit'] == true )
{
	$form = array();
	
	// Strip anything that shouldn't be submitted
	$form = array_map( 'safehtml', $_POST );
	
	$errors = 0;
	
	// Email field
	if ( empty( $form['email'] ) || strlen( $form['email'] ) < 4  || !valid_email( $form['email'] ) )
	{
		$error_message = $lang['Field_Empty'] . ': ' . $lang['Mailer_e_mail'];
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
		// Replace some variables in the subject
		$lang['Mailer_Subject'] = str_replace( '{website_name}', $conf['website_name'], $lang['Mailer_Subject'] );
		$lang['Mailer_Subject'] = str_replace( '{user_e_mail}', $form['email'] , $lang['Mailer_Subject'] );
		
		// Body of email
		$lang['Main_Site_Contact_Form'] = str_replace( '{message}', $form['message'], $lang['Main_Site_Contact_Form'] );
		$lang['Main_Site_Contact_Form'] = str_replace( '{name}', $form['name'], $lang['Main_Site_Contact_Form'] );
		$lang['Main_Site_Contact_Form'] = str_replace( '{email}', $form['email'], $lang['Main_Site_Contact_Form'] );

		send_mailing( 
			$conf['general_e_mail'], 
			$conf['general_e_mail_name'], 
			$conf['general_e_mail'], 
			$lang['Mailer_Subject'], 
			$lang['Main_Site_Contact_Form'] 
		);
		
		// Thank the user
		$lang['Mailer_Sent'] = str_replace( '{name}', $conf['website_name'], $lang['Mailer_Sent'] );
		
		$output_message = success( $lang['Success'], $lang['Mailer_Sent'], true );
		
		// Prevent flooding/abuse of this mail form by saving when this was last submitted successfully
		$session->set( 'mail_time', time() );
		
		// Don't load the contact form template again
		$custom['display_form'] = false;
	}
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

// Footer template
include PATH . '/templates/' . $cookie_template . '/footer.php';

?>