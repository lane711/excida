<?php

define( 'PMR', 'true' );
$page = 'contact';

include './config.php';
include PATH . '/defaults.php';

// Title of page
$title = $conf['website_name_short'] . ' - ' . $lang['Password_Reminder'];

// Header template
include PATH . '/templates/' . $cookie_template . '/header.php';

$output_message = '';
$error_message = '';
$contact_form = '';
$custom['display_form'] = true;

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/reminder.tpl';
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
	
	// Check if this user exist
	$sql = "SELECT * FROM " . USERS_TABLE . " WHERE email = '" . $db->makeSafe( $form['email'] ) . "'";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) == 0 && $errors == 0 )
	{
		$error_message = $lang['Password_Reminder_Not_Found'];
		$errors++;
	}
	
	if ( $conf['captcha_private_key'] != '' && $conf['captcha_public_key'] != '' && $conf['captcha_status'] == 'ON' )
	{
		if ( captcha_check() == false )
		{
			$error_message = $lang['Captcha_Fail'];
			$errors++;
		}
	}
	
	if ( $errors > 0 )
	{
		$output_message = error( $lang['Error'], $error_message, true );
	}
	else
	{
		$f = $db->fetcharray( $q );
	
		// Update their password
		$new_password = rand( 11111, 99999 );
		
		$sql = "
		UPDATE " . USERS_TABLE . "
		SET password = '" . md5( $db->makeSafe( $new_password ) ) . "'
		WHERE
			email = '" . $db->makeSafe( $_REQUEST['email'] ) . "'
		LIMIT 1
		";
		$q = $db->query( $sql );
		
		// Replacing the variable names
		$lang['Password_Reminder_Mail'] = str_replace( '{name}', $f['first_name'] . ' ' . $f['last_name'], $lang['Password_Reminder_Mail'] );
		$lang['Password_Reminder_Mail'] = str_replace( '{login}', $f['login'], $lang['Password_Reminder_Mail'] );
		$lang['Password_Reminder_Mail'] = str_replace( '{password}', $new_password, $lang['Password_Reminder_Mail'] );
		$lang['Password_Reminder_Mail'] = str_replace( '{website_name}', $conf['website_name'], $lang['Password_Reminder_Mail'] );
		
		send_mailing( 
			$conf['general_e_mail'], 
			$conf['general_e_mail_name'], 
			$f['email'], 
			$lang['Password_Reminder'], 
			$lang['Password_Reminder_Mail'] 
		);
		
		// Thank the user
		$lang['Password_Reminder_Approved'] = str_replace( '{email}', $f['email'], $lang['Password_Reminder_Approved'] );
		
		$output_message = success( $lang['Success'], $lang['Password_Reminder_Approved'], true );
		
		// Prevent flooding/abuse of this mail form by saving when this was last submitted successfully
		$session->set( 'mail_time', time() );
		
		// Don't load the form template again
		$custom['display_form'] = false;
	}
}

// Contact Form
$template->set( '@name', $lang['Mailer_Name'] );
$template->set( '@email', $lang['Realtor_e_mail'] );
$template->set( 'email', $_REQUEST['email'] );
$template->set( 'send', $lang['Admin_Mailer_Submit'] );

$template->set( '@email', $lang['Realtor_e_mail'] );
$template->set( 'contact_form', $contact_form );
$template->set( 'output_message', $output_message );
$template->set( 'reminder_text', $lang['Reminder_Text'] );
$template->set( 'header', $lang['Password_Reminder'] );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>