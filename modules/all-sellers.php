<?php

if (!defined('PMR') || (defined('PMR') && PMR != 'true')) die();

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/seller_list_header.tpl';
$template = new Template;
$template->load( $tpl );
$template->set( '@header', $lang['Module_Featured_Agents'] );
$template->publish();

$sql = "
SELECT *
FROM " . USERS_TABLE . "
WHERE 
	approved = 1 
ORDER BY RAND() 
LIMIT 2
";
$q = $db->query( $sql );
if ( $db->numrows( $q ) > 0 )
{
	while ( $f = $db->fetchassoc( $q ) )
	{
		$tpl = PATH . '/templates/' . $cookie_template . '/tpl/seller_list.tpl';
		$template = new Template;
		$template->load( $tpl );

		// Check a seller's package, if any, to determine if we can show pictures, address, etc.
		$f_package = package_check( $f['u_id'], 'seller' );

		$template->set( 'link', generate_link( 'seller', $f ) );
    
	    if ( $f_package['photo'] == 'ON' )
	    {
	    	$images = get_images( 'photos', $f['u_id'], 100, 74, 1, 1 );
	    }
	    else
	    {
	    	$images = get_images( 'hidden', $f['u_id'], 100, 74, 1, 1 );
	    }
	    
	    $template->set( 'photo', $images[0] );
		
		$template->set( 'first_name', $f['first_name'] );
		$template->set( 'last_name', $f['last_name'] );
		$template->set( 'company_name', $f['company_name'] );
		
		$description = substr($f['description'], 0, $conf['search_description']);
		$description = substr($description, 0, strrpos($description, ' ')) . ' ... ';
		
		$template->set( 'description', $description );
		
		$template->set( 'location', getnamebyid ( LOCATIONS_TABLE, $f['location'] ) );
		
		if ( $f_package['address'] == 'ON' )
		{
			$template->set( 'address', $f['address'] );
			$template->set( 'city', $f['city'] );
			$template->set( 'zip', $f['zip'] );
		}
		else
		{
			$template->set( 'address', '' );
			$template->set( 'city', '' );
			$template->set( 'zip', '' );
		}
		
		if ( $f_package['phone'] == 'ON' )
		{
			$template->set( 'phone', $f['phone'] );
			$template->set( 'fax', $f['fax'] );
			$template->set( 'mobile', $f['mobile'] );
		}
		else
		{
			$template->set( 'phone', '' );
			$template->set( 'fax', '' );
			$template->set( 'mobile', '' );
		}

		$template->set( 'email', validateemail ( $f['u_id'], $f['email'] ) );
		$template->set( 'website', validatewebsite ( $f['u_id'], $f['website'] ) );
		
		$template->set( 'view_user_listings', viewuserlistings ( $f['u_id'] ) );
		$template->set( 'date_added', $f['date_added'] );
		$template->set( 'date_updated', $f['date_updated'] );
		$template->set( 'ip_added', $f['ip_added'] );
		$template->set( 'ip_updated', $f['ip_updated'] );
		$template->set( 'hits', $f['hits'] );
		
		$template->set( 'new', newitem ( USERS_TABLE, $f['id'], $conf['new_days']) );
		$template->set( 'updated', updateditem ( USERS_TABLE, $f['id'], $conf['updated_days']) );
		$template->set( 'top', topitem ( $f['rating'], $f['votes'] ) );
		
		$template->set( 'rating', rating ( $f['rating'], $f['votes'] ) );

		// Labels
		$template->set( '@first_name', $lang['Realtor_First_Name'] );
		$template->set( '@last_name', $lang['Realtor_Last_Name'] );
		$template->set( '@company_name', $lang['Realtor_Company_Name'] );
		$template->set( '@description', $lang['Realtor_Description'] );
		$template->set( '@location', $lang['Location'] );
		$template->set( '@city', $lang['City'] );
		$template->set( '@address', $lang['Realtor_Address'] );
		$template->set( '@zip', $lang['Zip_Code'] );
		$template->set( '@phone', $lang['Realtor_Phone'] );
		$template->set( '@fax', $lang['Realtor_Fax'] );
		$template->set( '@mobile', $lang['Realtor_Mobile'] );
		$template->set( '@email', $lang['Realtor_e_mail'] );
		$template->set( '@website', $lang['Realtor_Website'] );
		$template->set( '@date_added', $lang['Date_Added'] );
		$template->set( '@date_updated', $lang['Date_Updated'] );
		$template->set( '@hits', $lang['Hits'] );
		$template->set( '@image_url', URL . '/templates/' . $cookie_template . '/images' );

		$template->publish();
	}
}

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/seller_list_footer.tpl';
$template = new Template;
$template->load( $tpl );
$template->publish();

?>