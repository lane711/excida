<?php

if ( !defined( 'PMRADMIN' ) || ( defined( 'PMRADMIN' ) && PMRADMIN == false ) )
{
	die();
} 

// Warn them if the license is going to expire soon
if (licenseExpiration() <= 30 )
{
	echo htmlErrorBox( '<b>Warning: Your License will expire in <a href="http://www.realtyscript.com/pricing.php" target="_blank">' . licenseExpiration() . ' day(s)</a>. Why wait? <a href="http://www.realtyscript.com/pricing.php" target="_blank">Order</a> a license to RealtyScript today!</b>' );
}

// Warn them if their administrator password should be changed
if ( $session->fetch( 'adminlogin' ) == 'admin' && $session->fetch( 'adminpassword' ) == md5( 'admin' ) )
{
	echo htmlErrorBox('<b>Please update your default administrator password and email to secure your web site</b>');
}

echo table_header ( $lang['Module_Admin'] );

echo '<ul class="admin-nav">';

$admin_nav = array();
$admin_nav[] = '<li><a href="' . URL . '/admin/index.php"><div class="icon_datareport_alt"></div>' . $lang['Admin_Top_Menu_Home'] . '</a></li>';
$admin_nav[] = '<li><a href="' . URL . '/admin/packages.php"><div class="icon_grid-2x2"></div>' . $lang['Admin_Top_Menu_Packages'] . '</a></li>';

if ( adminPermissionsCheck( 'manage_users', $session->fetch( 'adminlogin' ) ) )
{
	$admin_nav[] = '<li><a href="' . URL . '/admin/users.php"><div class="icon_group"></div>' . $lang['Admin_Top_Menu_Manage_Users'] . '</a></li>';
}

if ( adminPermissionsCheck( 'manage_listings', $session->fetch( 'adminlogin' ) ) )
{
	$admin_nav[] = '<li><a href="' . URL . '/admin/listings.php"><div class="icon_documents"></div>' . $lang['Admin_Top_Menu_Manage_Listings'] . '</a></li>';
}
	
if ( adminPermissionsCheck( 'manage_types', $session->fetch( 'adminlogin' ) ) )
{
	$admin_nav[] = '<li><a href="' . URL . '/admin/locations.php"><div class="icon_cog"></div>' . $lang['Admin_Top_Menu_Manage_Types_Locations'] . '</a></li>';
}
	
if ( adminPermissionsCheck( 'manage_settings', $session->fetch( 'adminlogin' ) ) )
{
	$admin_nav[] = '<li><a href="' . URL . '/admin/settings.php"><div class="icon_tools"></div>' . $lang['Admin_Top_Menu_Configuration_Settings'] . '</a></li>';
}

$admin_nav[] = '<li><a href="' . URL . '/admin/pages.php"><div class="icon_pencil"></div>' . $lang['Page_Manager'] . '</a></li>';
$admin_nav[] = '<li><a href="' . URL . '/admin/mailer.php"><div class="icon_mail"></div>' . $lang['Admin_Mailer'] . '</a></li>';
$admin_nav[] = '<li><a href="' . URL . '/admin/tools.php"><div class="icon_folder_download"></div>' . $lang['Admin_Tools'] . '</a></li>';
$admin_nav[] = '<li><a href="' . URL . '/admin/alerts.php"><div class="icon_star"></div>' . $lang['Alert'] . '</a></li>';
$admin_nav[] = '<li><a href="' . URL . '/admin/index.php?action=logout"><div class="icon_lock"></div>' . $lang['Admin_Top_Menu_Logout'] . '</a></li>';
	 
foreach ( $admin_nav AS $nav )
{
	echo str_replace( '<li>', '<li style="width: ' . floor( 100 / count( $admin_nav ) ) . '%">', $nav );
}

echo '</ul>';

echo table_footer();

?>