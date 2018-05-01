<?php

define( 'PMR', true );

$page = 'search';

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'] . ' - ' . $lang['Realtor_Search'];

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/search_sellers.tpl';
$template = new Template;
$template->load ( $tpl );

// Values
$template->set( 'address1', $_REQUEST['address1'] );
$template->set( 'zip', $_REQUEST['zip'] );
$template->set( 'location1', get_locations() );
$template->set( 'title', $_REQUEST['title'] );
$template->set( 'keyword', $_REQUEST['keyword'] );
$template->set( 'address', $_REQUEST['address'] );
$template->set( 'first_name', $_REQUEST['first_name'] );
$template->set( 'last_name', $_REQUEST['last_name'] );
$template->set( 'company_name', $_REQUEST['company_name'] );
$template->set( 'email', $_REQUEST['email'] );
$template->set( 'phone', $_REQUEST['phone'] );
$template->set( 'mobile', $_REQUEST['mobile'] );

// Labels
$template->set( '@email', $lang['Realtor_e_mail'] );
$template->set( '@first_name', $lang['Realtor_First_Name'] );
$template->set( '@last_name', $lang['Realtor_Last_Name'] );
$template->set( '@company_name', $lang['Realtor_Company_Name'] );
$template->set( '@keyword', $lang['Search_Keyword'] );
$template->set( '@address', $lang['Realtor_Address'] );
$template->set( '@phone', $lang['Realtor_Phone'] );
$template->set( '@mobile', $lang['Realtor_Mobile'] );
$template->set( 'submit', $lang['Listing_Submit'] );
$template->set( '@location', $lang['Location'] );
$template->set( '@address1', $lang['Listing_Address1'] );
$template->set( '@zip', $lang['Zip_Code'] );
$template->set( 'select', $lang['Select'] );
$template->set( 'search', $lang['Menu_Search'] );

$template->set( 'output_message', $output_message );
$template->set( 'header', $lang['Realtor_Search']  );
$template->set( '@locations', $lang['Location'] );

$template->set( '@by_location', $lang['By_Location'] );
$template->set( '@by_name', $lang['By_Name'] );
$template->set( '@advanced', $lang['Advanced_Search'] );

$template->publish();

// Template Footer
include PATH . '/templates/' . $cookie_template . '/footer.php';

?>