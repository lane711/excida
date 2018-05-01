<?php

define( 'PMR', true );

include 'config.php';
include PATH . '/defaults.php';

// Title tag content
$title = $conf['website_name_short'] . ' - ' . $lang['Menu_User_Login'];

// Destroy user/admin session if we logout
if ( $_GET['action'] == 'logout' )
{
	$session->destroy();
}

// If they are trying to log in
if ( $_REQUEST['submit'] == true )
{
	if ( $_REQUEST['login'] != '' && $_REQUEST['password'] != '' )
	{
		$session->set( 'login', $_REQUEST['login'] );
		$session->set( 'password', md5( $_REQUEST['password'] ) );
		
		// If they are logged in, redirect them to the user console
		if ( auth_check( $session->fetch( 'login' ), $session->fetch( 'password' ) ) )
		{
			// Grab their user ID as well
			$sql = "SELECT u_id,site_id FROM " . USERS_TABLE . " WHERE login = '" . $db->makeSafe( $_REQUEST['login'] ) . "'";
			$q = $db->query( $sql );
			
			if ( $db->numrows( $q ) > 0 )
			{
				$f = $db->fetcharray( $q );
				
				$session->set( 'u_id', $f['u_id'] );
				$session->set( 'site_id', $f['site_id'] );
				$session->set( 'name', $f['first_name'].' '.$f['last_name'] );
			}
		
			header( 'Location: ' . URL . '/user.php' );
			exit();
		}
		else
		{
			$output_message = error( $lang['Error'], $lang['Auth_Error'], true );
		}
	}
	else
	{
		$output_message = error( $lang['Error'], $lang['Auth_Error'], true );
	}
}

// Template header
include PATH . '/templates/' . $cookie_template . '/header.php';

// User isn't logged in
$tpl = PATH . '/templates/' . $cookie_template . '/tpl/login.tpl';
$template = new Template;
$template->load ( $tpl );
	
$template->set( 'heading', $lang['Menu_User_Login'] );
$template->set( '@login_text', $lang['Login_Text'] );
$template->set( '@register', $lang['Realtor_Submit'] );
$template->set( '@login', $lang['Seller_Control_Panel'] );
$template->set( '@password', $lang['Realtor_Password'] );
$template->set( 'login', $_REQUEST['login'] );
$template->set( 'password', $_REQUEST['password'] );
$template->set( 'password_reminder', $lang['Password_Reminder'] );
$template->set( 'output_message', $output_message );
	
$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>