<?php

define( 'PMR', true );
$page = 'search';

include 'config.php';
include PATH . '/defaults.php';

$title = $conf['website_name_short'] . ' - ' . $lang['Realtor_Search'];

include PATH . '/templates/' . $cookie_template . '/header.php';

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/search_sellers_results_header.tpl';
$template = new Template;
$template->load ( $tpl );
$template->set( 'header', $lang['Realtor_Search'] );
$template->publish();

$search = $_REQUEST;

// Only these specific fields are searchable
$allowed_search_fields = array(
	'first_name', 'last_name', 'company_name', 'description', 'keyword', 'location_1', 'location_2', 'location_3', 'zip', 'phone', 'fax', 'mobile', 'email', 'url', 'login', 'image_uploaded', 'votes', 'hits'
);
foreach( $allowed_search_fields AS $key )
{
	if ( $search[$key] != '' )
	{
		if ( 
			$key == 'description' 
			)
		{
			// Fields that are textual and use a fulltext search (MySQL capability)
			$whereSQL .= " AND MATCH( description ) AGAINST ( '" . $db->makeSafe( $search[$key] ) . "' )";
		} 
		elseif ( 
			$key == 'keyword' 
			)
		{
			// Fields that are textual and use a fulltext search (MySQL capability)
			$whereSQL .= " AND MATCH( first_name, last_name, company_name, description, address ) AGAINST ( '" . $db->makeSafe( $search[$key] ) . "' )";
		} 
		elseif ( $key == 'zip' ) 
		{
			// If we need to find properties within a certain distance
			if ( $search['radius'] != '' && $search['radius'] != 0 )
			{
				$found_zip_codes = get_zips_in_range( $search['zip'], $search['radius'] );
				
				if ( count( $found_zip_codes ) > 0 )
				{
					if ( is_array( $found_zip_codes ) )
					{
						$zip_list = '';
						
						foreach ( $found_zip_codes AS $zip_code => $distance )
						{
							$zip_list .= $zip_code . ", ";
						}
						
						$zip_list = trim( $zip_list, ', ' );
					
						$whereSQL .= " AND " . $key . " IN ( " . $db->makeSafe( $zip_list ) . " )";
					}
				}
			}
		}
		elseif ( 
			$key == 'first_name' 
			|| $key == 'last_name'
			|| $key == 'company_name'
			|| $key == 'address'
			)
		{
			// Fields that are textual and use a fulltext search (MySQL capability)
			$whereSQL .= " AND MATCH( first_name, last_name, company_name, address ) AGAINST ( '" . $db->makeSafe( $search[$key] ) . "' )";
		}
		elseif ( 
			$key == 'votes' 
			|| $key == 'hits' 
			|| $key == 'rating'
			)
		{
			// Fields that can be greater than or equal to the values entered (e.g., 200+ views)			
			$whereSQL .= " AND " . $key . " >= '" . $db->makeSafe( $search[$key] ) . "'";
		}
		else
		{
			// Straight match
			$whereSQL .= " AND " . $key . " = '" . $db->makeSafe( $search[$key] ) . "'";
		}
	}
}

// Order by
$allowed_order_by = array(
	'u.first_name', 'u.last_name', 'u.date_added', 'u.featured', 'l1.country', 'l2.state', 'l3.city', 'u.company_name', 'u.zip', 'u.login', 'u.hits', 'u.rating', 'u.votes'
);

if ( $search['order_by_type'] != '' && ( $search['order_by_type'] == 'ASC' || $search['order_by_type'] == 'DESC' ) )
{
	$order_by_type = $search['order_by_type'];
}
else
{
	$order_by_type = 'DESC';
}

if ( $search['order_by'] != '' && in_array( $search['order_by'], $allowed_order_by ) ) 
{
	$order_by = $search['order_by'] . ' ' . $order_by_type;
}
else
{
	$order_by = 'u.last_name DESC';
}

// Limit & Pagination
$page = ( $search['page'] != '' ) ? (int)$search['page'] : 1;

if ( $page == 1 )
{
	$limit = '0, ' . $conf['search_results'];
}
else
{
	$prev_page = $page - 1;
	$limit = $prev_page * $conf['search_results'] . ', ' . $conf['search_results'];
}

// Grab the listing data
$sql = "
SELECT
	u.*,
	l1.location_name AS country,
	l2.location_name AS state,
	l3.location_name AS city
FROM " . USERS_TABLE  . " AS u
LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = u.location_1
LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = u.location_2
LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = u.location_3
WHERE 
	u.approved = 1 
	" . $whereSQL . "
ORDER BY " . $order_by . "
LIMIT " . $limit . "
";
$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
if ( $db->numrows( $q ) > 0 )
{
	while( $f = $db->fetchassoc( $q ) )
	{
		$tpl = PATH . '/templates/' . $cookie_template . '/tpl/search_sellers_results.tpl';
		$template = new Template;
		$template->load( $tpl );
	
		// Check a seller's package, if any, to determine if we can show pictures, address, etc.
		$f_package = package_check( $f['u_id'], 'seller' );
		
		// Starting a new template
		$template = new Template;
		$template->load ( $tpl );
		
		// Replace the template variables
		$template->set( 'link', generate_link( 'seller', $f ) );	
			
		if ( $f_package['photo'] == 'ON' )
		{
			$images = get_images( 'photos', $f['u_id'], 352, 232, 1, 1 );
		}
		else
		{
			$images = get_images( 'hidden', $f['u_id'], 352, 232, 1, 1 );
		}
		
		$template->set( 'image', $images[0] );
		
		$template->set( 'first_name', $f['first_name'] );
		$template->set( 'last_name', $f['last_name'] );
		$template->set( 'company_name', $f['company_name'] );
		
		$description = substr(removehtml(unsafehtml($f['description'])), 0, $conf['search_description']);
		$description = substr($description, 0, strrpos($description, ' ')) . ' ... ';
		$template->set( 'description', $description );
		
		if ($f_package['address'] == 'ON')
		{
			$template->set( 'address', $f['address'] );
			$template->set( 'city', $f['city'] );
			$template->set( 'zip', $f['zip'] );
			$template->set( 'state', $f['state'] );
			$template->set( 'country', $f['country'] );
		}
		else
		{
			$template->set( 'address', '' );
			$template->set( 'city', '' );
			$template->set( 'zip', '' );
			$template->set( 'state', $f['state'] );
			$template->set( 'country', $f['country'] );
		}		
		
		if ($f_package['phone'] == 'ON')
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
		
		$template->set( 'email', validateemail ( $f['id'], $f['email'] ) );
		$template->set( 'website', validatewebsite ( $f['id'], $f['website'] ) );
		
		$template->set( 'view_user_listings', viewuserlistings ( $f['id'] )  );
		
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
		$template->set( '@profile', $lang['Profile'] );
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
		
		// Publish template
		$template->publish();
		
		$i++;
	}
}
else
{
	$output_message = error( $lang['Error'], $lang['No_Results'], true );
}

$tpl = PATH . '/templates/' . $cookie_template . '/tpl/search_sellers_results_footer.tpl';
$template = new Template;
$template->load( $tpl );

// Pagination
$sql = "
SELECT
	COUNT(*) AS total_results
FROM " . USERS_TABLE  . " AS u
LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = u.location_1
LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = u.location_2
LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = u.location_3
WHERE 
	u.approved = 1 
	" . $whereSQL . "
ORDER BY " . $order_by . "
";
$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
$f = $db->fetcharray( $q );
$total_results = $f['total_results'];

$custom['pagination'] = pagination( URL . '/search_sellers_results.php', $_REQUEST['page'], $total_results, $conf['search_results'] );

$template->set( 'output_message', $output_message );

$template->publish();

include PATH . '/templates/' . $cookie_template . '/footer.php';

?>