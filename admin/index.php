<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include '../config.php';
include PATH . '/defaults.php';

$title = $lang['Module_Admin'];

include PATH . '/admin/template/header.php';

// Reset their password
if ( $_GET['reset_hash'] != '' && strlen( $_GET['reset_hash'] ) == 32 )
{
	// Validate this hash
	$sql = "SELECT login FROM " . ADMINS_TABLE . " WHERE reset_hash = '" . $db->makeSafe( $_GET['reset_hash'] ) . "' ";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		$f = $db->fetchassoc( $q );
	
		// Generate a random password
		$rand_pass = rand( 111111, 999999 );
	
		$sql = "
		UPDATE " . ADMINS_TABLE . "
		SET 
			password = '" . md5( $rand_pass ) . "',
			reset_hash = ''
		WHERE
			reset_hash = '" . $db->makeSafe( $_GET['reset_hash'] ) . "'
		";
		$q2 = $db->query( $sql );
	
		// Replacing the variable names
		$lang['Password_Reset_Mail2'] = str_replace( '{name}', $conf['general_e_mail_name'], $lang['Password_Reset_Mail2'] );
		$lang['Password_Reset_Mail2'] = str_replace( '{login}', $f['login'], $lang['Password_Reset_Mail2'] );
		$lang['Password_Reset_Mail2'] = str_replace( '{password}', $rand_pass, $lang['Password_Reset_Mail2'] );
		$lang['Password_Reset_Mail2'] = str_replace( '{url}', URL . '/admin', $lang['Password_Reset_Mail2'] );
		$lang['Password_Reset_Mail2'] = str_replace( '{website_name}', $conf['website_name'], $lang['Password_Reset_Mail2'] );
	
		send_mailing( 
			$conf['general_e_mail'], 
			$conf['general_e_mail_name'], 
			$conf['general_e_mail'], 
			$lang['Password_Reset_Subject'] , 
			$lang['Password_Reset_Mail2'] 
		);
		
		echo $lang['Password_Reset_Request'] . '<br /><br />';
	}
}

// If they are requesting a new admin password, email them a link to do so
if ( $_GET['request_reset'] == true )
{
	// Generate reset hash and store it
	$reset_hash = md5( rand( 11111, 99999 ) );
	
	$sql = "
	UPDATE " . ADMINS_TABLE . "
	SET
		reset_hash = '" . $reset_hash . "'
	WHERE
		id = 1
	";
	$q = $db->query( $sql );

	// Reset URL
	$link = URL . '/admin/index.php?reset_hash=' . $reset_hash;

	// Replacing the variable names
	$lang['Password_Reset_Mail'] = str_replace( '{name}', $conf['general_e_mail_name'], $lang['Password_Reset_Mail'] );
	$lang['Password_Reset_Mail'] = str_replace( '{website_name}', $conf['website_name'], $lang['Password_Reset_Mail'] );
	$lang['Password_Reset_Mail'] = str_replace( '{ipaddr}', $_SERVER['REMOTE_ADDR'], $lang['Password_Reset_Mail'] );
	$lang['Password_Reset_Mail'] = str_replace( '{link}', $link, $lang['Password_Reset_Mail'] );

	send_mailing( 
		$conf['general_e_mail'], 
		$conf['general_e_mail_name'], 
		$conf['general_e_mail'], 
		$lang['Password_Reset_Subject'] , 
		$lang['Password_Reset_Mail'] 
	);
	
	echo $lang['Password_Reset_Sent'] . '<br /><br />'; 
}

// Destroy user/admin session if we logout
if ( $_GET['action'] == 'logout' )
{
	$session->destroy();
}

if (!isset($_SESSION['adminlogin']) && isset($_POST['login']))
 {
  $session->set('adminlogin', safehtml(strtolower($_POST['login'])));
  $session->set('adminpassword', md5($_POST['password']));
  $userlogin = FALSE;
 }

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))
 {
  $session->varunset('navigation');
  $sql = "SELECT * FROM " . ADMINS_TABLE . " WHERE login = '" . $session->fetch('adminlogin'). "' AND password = '".$session->fetch('adminpassword')."' ";
	$q = $db->query( $sql );
	if ( $db->numrows( $q ) > 0 )
	{
		$f = $db->fetchassoc( $q );
		$_SESSION['site_id'] = $f['site_id'];
		$session->set('site_id',$f['site_id']);
		$_SESSION['role'] = $f['level'];
		$session->set('role',$f['level']);
	}
  include ( PATH . '/admin/navigation.php' );
  
 }
else
 {

  // IF NOT LOGGED

  // If this form was already submitted and 
  // login / password are not correct
  // we destroy the session

  if (isset($_SESSION['adminlogin'])
  && !adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')) 
  && isset($_POST['login'])) 

   $session->destroy();

  echo table_header ( $lang['Module_Admin'] );

  // Output the form
  echo '
   <form action="' . URL . '/admin/index.php" method="POST">
    <table width="100%" cellpadding="5" cellspacing="0" border="0">
       ';

  echo userform ($lang['Admin_Login'], '<input type="text" size="45" name="login" maxlength="50">');
  echo userform ($lang['Admin_Password'], '<input type="password" size="45" name="password" maxlength="50">');
  echo userform ('', '<input type="submit" value="' . $lang['Admin_Login_Submit'] . '">&nbsp;<a href="' . URL . '/admin/index.php?request_reset=true">' . $lang['Reset_Password'] . '</a>');
 
  echo '
    </table>
   </form>
       ';

  echo table_footer ();

  if (isset($userlogin) && !$userlogin = FALSE) 
   echo '<span class="warning">' . $lang['Auth_Error'] . '</span><br /><br />';

 }

$version_check = TRUE;
$announcement_check = TRUE;

include ( PATH . '/admin/template/footer.php' );

?>