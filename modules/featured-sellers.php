<?php

die( 'Script not in use.' );

if ( !defined( 'PMR' ) || (defined( 'PMR' ) && PMR != 'true' ) )
{
	die();
}

$i = 0;

// Fetch all approved listings with featured variable
// set to 'A' in random order
$sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE approved = 1 AND package > 0 ORDER BY RAND() LIMIT ' . $conf['featured_limit'];
$r = $db->query ( $sql );

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/realtor_search_short_header.tpl';
$template = new Template;
$template->load ( $tpl );
$template->publish();

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/realtor_search_short.tpl';

while ($f = $db->fetcharray( $r ))
{

$sql = 'SELECT * FROM ' . USERS_TABLE . ' WHERE id = ' . $f['u_id'] . ' LIMIT 1';
$r_user = $db->query ( $sql );
$f_user = $db->fetcharray ( $r_user );

if ($f_user['package'] != '0' && $f_user['package'] != '')
{
	$sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE . ' WHERE id = ' . $f_user['package'] . ' LIMIT 1';
	$r_package = $db->query ( $sql );
	$f_package = $db->fetcharray ( $r_package );
}
else
{
	$f_package['listings'] = $conf['free_listings'];
	$f_package['gallery'] = $conf['free_gallery'];
	$f_package['mainimage'] = $conf['free_mainimage'];
	$f_package['photo'] = $conf['free_photo'];
	$f_package['phone'] = $conf['free_phone'];
	$f_package['address'] = $conf['free_address'];
}

// Starting a new template
$template = new Template;

// Load user short search results template
$template->load ( $tpl );

// VALUES

if ($conf['rewrite'] == 'ON')
$template->set ( 'link', URL . '/Realtor/' . $f['u_id'] . '.html' );
else
$template->set ( 'link', URL . '/viewuser.php?id=' . $f['u_id']);

if ($f_package['photo'] == 'ON')
$template->set ( 'photo', show_image( 'photos', $f['u_id'], 270, 200 ) );
else
$template->set ( 'photo', '' );

$template->set ( 'first_name', $f['first_name'] );
$template->set ( 'last_name', $f['last_name'] );
$template->set ( 'company_name', $f['company_name'] );

$description = substr($f['description'], 0, $conf['search_description']);
$description = substr($description, 0, strrpos($description, ' ')) . ' ... ';

$template->set ( 'description', $description );

unset ($description);

$template->set ( 'location', getnamebyid ( LOCATIONS_TABLE, $f['location'] ) );

if ($f_package['address'] == 'ON')
{
$template->set ( 'address', $f['address'] );
$template->set ( 'city', $f['city'] );
$template->set ( 'zip', $f['zip'] );
}
else
{
$template->set ( 'address', '' );
$template->set ( 'city', '' );
$template->set ( 'zip', '' );
}


if ($f_package['phone'] == 'ON')
{
$template->set ( 'phone', $f['phone'] );
$template->set ( 'fax', $f['fax'] );
$template->set ( 'mobile', $f['mobile'] );
}
else
{
$template->set ( 'phone', '' );
$template->set ( 'fax', '' );
$template->set ( 'mobile', '' );
}


$template->set ( 'email', validateemail ( $f['u_id'], $f['email'] ) );
$template->set ( 'website', validatewebsite ( $f['u_id'], $f['website'] ) );

$template->set ( 'view_user_listings', viewuserlistings ( $f['u_id'] ) );

$template->set ( 'date_added', $f['date_added'] );
$template->set ( 'date_updated', $f['date_updated'] );

$template->set ( 'ip_added', $f['ip_added'] );
$template->set ( 'ip_updated', $f['ip_updated'] );

$template->set ( 'hits', $f['hits'] );

$template->set ( 'new', newitem ( USERS_TABLE, $f['u_id'], $conf['new_days']) );
$template->set ( 'updated', updateditem ( USERS_TABLE, $f['u_id'], $conf['updated_days']) );
$template->set ( 'top', topitem ( $f['rating'], $f['votes'] ) );

$template->set ( 'rating', rating ( $f['rating'], $f['votes'] ) );

// Set background color
$bgcolor = ''; // Background color for all odd listings
$bgcolor2 = $conf['list_background_color_even']; // Background color for all even listings

if ( $i%2 == 0 )
$template->set ( 'bgcolor', $bgcolor );
else
$template->set ( 'bgcolor', $bgcolor2 );

// Names

$template->set ( '@first_name', $lang['Realtor_First_Name'] );
$template->set ( '@last_name', $lang['Realtor_Last_Name'] );
$template->set ( '@company_name', $lang['Realtor_Company_Name'] );
$template->set ( '@description', $lang['Realtor_Description'] );
$template->set ( '@location', $lang['Location'] );
$template->set ( '@city', $lang['City'] );
$template->set ( '@address', $lang['Realtor_Address'] );
$template->set ( '@zip', $lang['Zip_Code'] );
$template->set ( '@phone', $lang['Realtor_Phone'] );
$template->set ( '@fax', $lang['Realtor_Fax'] );
$template->set ( '@mobile', $lang['Realtor_Mobile'] );
$template->set ( '@email', $lang['Realtor_e_mail'] );
$template->set ( '@website', $lang['Realtor_Website'] );
$template->set ( '@date_added', $lang['Date_Added'] );
$template->set ( '@date_updated', $lang['Date_Updated'] );
$template->set ( '@hits', $lang['Hits'] );
$template->set ( '@image_url', URL . '/templates/' . $cookie_template . '/images' );

//

// Set background color
$bgcolor = ''; // Background color for all odd listings
$bgcolor2 = $conf['list_background_color_even']; // Background color for all even listings

if ( $i%2 == 0 )
$template->set ( 'bgcolor', $bgcolor );
else
$template->set ( 'bgcolor', $bgcolor2 );

// Publish template
$template->publish();

$i++;

}

// If no featured listing were found we print the message
if ($db->numrows($r) == 0)
{
	//echo '<tr><td align="center"> - - - </td></tr>';
}

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/property_search_short_simple_footer.tpl';
$template = new Template;
$template->load ( $tpl );
$template->publish();

?>