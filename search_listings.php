<?php

define( 'PMR', true );

$page = 'search';

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'] . ' - ' . $lang['Property_Search'];

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/search_listings.tpl';
$template = new Template;
$template->load ( $tpl );

// Values
$template->set( 'address1', $_REQUEST['address1'] );
$template->set( 'zip', $_REQUEST['zip'] );
$template->set( 'listing_type', $_REQUEST['listing_type'] );
$template->set( 'status', $_REQUEST['status'] );
$template->set( 'location1', get_locations() );
$template->set( 'lot_size', $_REQUEST['lot_size'] );
$template->set( 'living_area', $_REQUEST['living_area'] );
$template->set( 'mls', $_REQUEST['mls'] );
$template->set( 'price', $_REQUEST['price'] );
$template->set( 'title', $_REQUEST['title'] );
$template->set( 'keyword', $_REQUEST['keyword'] );
$template->set( 'address', $_REQUEST['address'] );

// Labels
$template->set( '@radius', $lang['Your_Zip_Code_Radius'] );
$template->set( '@from_price', $lang['From_Price'] );
$template->set( '@to_price', $lang['To_Price'] );
$template->set( '@from_price_rental', $lang['From_Price_Rental'] );
$template->set( '@to_price_rental', $lang['To_Price_Rental'] );
$template->set( '@title', $lang['Listing_Title'] );
$template->set( '@keyword', $lang['Search_Keyword'] );
$template->set( '@address', $lang['Realtor_Address'] );
$template->set( '@image_uploaded', $lang['Search_Images_Only'] );
$template->set( 'submit', $lang['Listing_Submit'] );
$template->set( '@listing_type', $lang['Module_Listing_Type'] );
$template->set( '@status', $lang['Listing_Status'] );
$template->set( '@property_type', $lang['Listing_Property_Type'] );
$template->set( '@mls', $lang['Listing_MLS'] );
$template->set( '@style', $lang['Listing_Style'] );
$template->set( '@location', $lang['Location'] );
$template->set( '@bedrooms', $lang['Listing_Bedrooms'] );
$template->set( '@bathrooms', $lang['Listing_Bathrooms'] );
$template->set( '@half_bathrooms', $lang['Listing_Half_Bathrooms'] );
$template->set( '@price', $lang['Listing_Price'] );
$template->set( '@address1', $lang['Listing_Address1'] );
$template->set( '@zip', $lang['Zip_Code'] );
$template->set( '@lot_size', $lang['Listing_Lot_Size'] );
$template->set( '@basement', $lang['Listing_Basement'] );
$template->set( '@living_area', $lang['Listing_Dimensions'] );
$template->set( '@longitude', $lang['Listing_Longitude'] );
$template->set( '@latitude', $lang['Listing_Latitude'] );
$template->set( '@garage_cars', $lang['Listing_Garage_Cars'] );
$template->set( '@garage', $lang['Listing_Garage'] );
$template->set( '@year_built', $lang['Listing_Year_Built'] );
$template->set( '@amenities', $lang['Listing_Additional_Out_Buildings'] );
$template->set( '@appliances', $lang['Listing_Appliances_Included'] );
$template->set( '@features', $lang['Listing_Features'] );
$template->set( 'select', $lang['Select'] );
$template->set( 'search', $lang['Menu_Search'] );

$template->set( 'output_message', $output_message );
$template->set( 'header', $lang['Property_Search'] );
$template->set( '@locations', $lang['Location'] );

$template->set( '@calendar', $lang['Availability_Calendar'] );
$template->set( '@listing_details', $lang['Listing_Details'] );
$template->set( '@additional_criteria', $lang['Additional_Criteria'] );

$template->publish();

// Template Footer
include PATH . '/templates/' . $cookie_template . '/footer.php';

?>