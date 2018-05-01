<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include '../config.php';
include PATH . '/defaults.php';

$title = $lang['Realtor_Search'];

include PATH . '/admin/template/header.php';

// Access check
if ( adminAuth( $session->fetch( 'adminlogin' ), $session->fetch( 'adminpassword' ) ) )
{
	include PATH . '/admin/navigation.php';
 	$whereClause = "";
  if($session->fetch('role')=="SUPERUSER")
  $whereClause=" 1=1 AND site_id=".$session->fetch('site_id');
	// Make sure this administrator have access to this script
	adminPermissionsCheck('manage_users', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');
	
	// Remove user
	if ( $_GET['req'] == 'remove' && $_GET['u_id'] != '' )
	{
		echo table_header ( $lang['Information'] );
	
		$sql = 'SELECT email FROM ' . USERS_TABLE  . ' WHERE u_id = ' . $_GET['u_id'] . ' AND '.$whereClause.' LIMIT 1';
		$r_user = $db->query ( $sql ) or error ('Critical Error' , mysql_error());
		$f_user = $db->fetcharray ($r_user);
		
		removeuser( intval( $_GET['u_id'] ) );
		
		echo $lang['Admin_Realtor_Removed'];
		
		$lang['User_Rejected_Notification_Subject'] = str_replace( '{website_name}', $conf['website_name'], $lang['User_Rejected_Notification_Subject'] );
		
		// Replacing the variable names
		$lang['User_Rejected_Notification_Mail'] = str_replace( '{website_name}', $conf['website_name'], $lang['User_Rejected_Notification_Mail'] );
		
		echo table_footer();
	}
	
	// Approve user
	if ( $_GET['req'] == 'approve' && $_GET['u_id'] != '' )
	{
		echo table_header ( $lang['Information'] );
		
		$sql = 'UPDATE ' . USERS_TABLE . ' SET approved = 1 WHERE u_id = ' . $_GET['u_id'] . ' AND '.$whereClause.' LIMIT 1';  
		$db->query($sql) or error ( 'Critical Error', mysql_error() );
		
		echo $lang['Admin_User_Approved'];
		
		$sql = 'SELECT * FROM ' . USERS_TABLE  . ' WHERE u_id = ' . $_GET['u_id'] . ' AND '.$whereClause.' LIMIT 1';
		$r_user = $db->query ( $sql ) or error( 'Critical Error', mysql_error());
		$f_user = $db->fetcharray ($r_user);
		
		$lang['User_Realtor_Notification_Subject'] = str_replace( '{website_name}', $conf['website_name'], $lang['User_Realtor_Notification_Subject'] );
		
		// Replacing the variable names
		$lang['User_Realtor_Notification_Mail'] = str_replace( '{website_name}', $conf['website_name'], $lang['User_Realtor_Notification_Mail'] );
		$lang['User_Realtor_Notification_Mail'] = str_replace( '{first_name}', $f_user['first_name'], $lang['User_Realtor_Notification_Mail'] );
		$lang['User_Realtor_Notification_Mail'] = str_replace( '{last_name}', $f_user['last_name'], $lang['User_Realtor_Notification_Mail'] );
		$lang['User_Realtor_Notification_Mail'] = str_replace( '{company}', $f_user['company_name'], $lang['User_Realtor_Notification_Mail'] );
		$lang['User_Realtor_Notification_Mail'] = str_replace( '{address}', $f_user['address'] . ' ' . $f_user['city'] . ' ' . $f_user['zip'] . ' ' . getnamebyid( LOCATIONS_TABLE, $f_user['location'] ), $lang['User_Realtor_Notification_Mail'] );
		
		send_mailing( 
			$conf['general_e_mail'], 
			$conf['general_e_mail_name'], 
			$f_user['email'], 
			$lang['User_Realtor_Notification_Subject'], 
			$lang['User_Realtor_Notification_Mail']
		);
		
		echo table_footer();	
	}
	
	echo table_header ( $lang['Realtor_Search'] );
	
	// Only these specific fields are searchable
	$search = $_REQUEST;
	
	$allowed_search_fields = array(
		'u_id', 'first_name', 'last_name', 'company_name', 'description', 'address', 'zip', 'phone', 'mobile', 'email', 'website', 'login', 'realtor_updated', 'realtor_approved', 'location1', 'location2', 'location3'
	);
	foreach( $allowed_search_fields AS $key )
	{
		if ( $search[$key] != '' )
		{
			// String search is global and searches first name, last name, company name, and their description
			if ( $key == 'first_name' || $key == 'last_name' || $key == 'company_name' || $key == 'description' || $key == 'address'  || $key == 'website' || $key == 'login' )
			{
				$whereSQL .= " 
				AND ( 
					" . $key . " LIKE '%" . $db->makeSafe( $search[$key] ) . "' 
					OR " . $key . " LIKE '" . $db->makeSafe( $search[$key] ) . "%' 
					OR " . $key . " LIKE '%" . $db->makeSafe( $search[$key] ) . "%'
				)
				";	
			}
			elseif ( $key == 'realtor_updated' && $search['realtor_updated_days'] != '' )
			{
				$whereSQL .= " AND ( ( TO_DAYS( NOW() ) - TO_DAYS( date_updated ) ) <= '" . $search['realtor_updated_days'] . "' ) ";
			}
			elseif ( $key == 'realtor_approved' )
			{
				$whereSQL .= " AND approved = '0' ";	
			}
			elseif ( $key == 'location1' || $key == 'location2' || $key == 'location3' ) 
			{
				if ( $key == 'location1' )
				{
					$search_key = 'u.location_1';
				}
				elseif ( $key == 'location2' )
				{
					$search_key = 'u.location_2';
				}
				elseif ( $key == 'location3' )
				{
					$search_key = 'u.location_3';
				}
				$whereSQL .= " AND " . $search_key . " = '" . $db->makeSafe( $search[$key] ) . "'";
			}
			else
			{
				// Straight match
				$whereSQL .= " AND " . $key . " = '" . $db->makeSafe( $search[$key] ) . "'";
			}
		}
	}
	
	// Limit & Pagination
	$page = ( $_REQUEST['page'] != '' ) ? (int)$_REQUEST['page'] : 1;
	
	if ( $page == 1 )
	{
		$limit = '0, ' . $conf['search_results'];
	}
	else
	{
		$prev_page = $page - 1;
		$limit = $prev_page * $conf['search_results'] . ', ' . $conf['search_results'];
	}
	if($session->fetch('role')=="SUPERUSER")
	$whereSQL.=" AND u.site_id=".$session->fetch('site_id');
	 $sql = "
	SELECT 
		u.*,	
		l1.location_name AS country,
		l2.location_name AS state,
		l3.location_name AS city
	FROM " . USERS_TABLE . " u
	LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = u.location_1
	LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = u.location_2
	LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = u.location_3
	WHERE 
		1 = 1
		" . $whereSQL . "
	LIMIT " . $limit . "
	"; 
	$r = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	if ( $db->numrows( $r ) > 0 )
	{
		while ( $f = $db->fetcharray( $r ) )
		{
			$tpl = PATH . '/admin/template/tpl/user-short.tpl';
			$template = new Template;
			$template->load ( $tpl );
			
			// Replace the template variables
			$template->set( 'edit',  '<a href="' . URL . '/admin/editusers.php?u_id=' . $f['u_id'] . '">' . $lang['Admin_Edit_This'] . '</a>' );
			$template->set( 'remove', '<a href="' . URL . '/admin/users.php?req=remove&u_id=' . $f['u_id'] . '&' . $session->fetch( 'usersearchvariables' ) . '" onClick="return confirmDelete(\'Are you sure you want to remove this user?\')"><span class="warning">' . $lang['Admin_Remove_This'] . '</span></a>' );
			$template->set( 'link', URL . '/admin/editusers.php?u_id=' . $f['u_id'] );
			
			if ( $f['approved'] == 0 && adminPermissionsCheck( 'manage_users', $session->fetch( 'adminlogin' ) ) )
			{
				$template->set( 'approve', '<a href="' . URL . '/admin/users.php?req=approve&u_id=' . $f['u_id'] . '&' . $session->fetch( 'usersearchvariables' ) . '">' . $lang['Admin_Approve_This'] . '</a>' );
			}
			else
			{
				$template->set( 'approve', $lang['Admin_Approve_This'] );
			}
			
			$sql = "
			SELECT COUNT(*) AS total_listings
			FROM " . PROPERTIES_TABLE . "
			WHERE u_id = '" . $f['u_id'] . "'
			";
			$res = $db->query( $sql );
			if ( $db->numrows( $res ) > 0 )
			{
				$listing_results = $db->fetcharray( $res );
				$num_listings = $listing_results['total_listings'];	
			}
			else
			{
				$num_listings = 0;
			}
			
			if ( adminPermissionsCheck( 'manage_listings', $session->fetch( 'adminlogin' ) ) && $f['approved'] == 1 )
			{
				$template->set( 'add_listings', '<a href="' . URL . '/admin/addlistings.php?u_id=' . $f['u_id'] . '">' . $lang['Admin_Add_Listings'] . '</a>');
			}
			else
			{
				$template->set( 'add_listings', $lang['Admin_Add_Listings'] );
			}
			
			if ( adminPermissionsCheck( 'manage_listings', $session->fetch( 'adminlogin' ) ) && $num_listings > 0 && $f['approved'] == 1 )
			{
				$template->set( 'edit_listings', '<a href="' . URL . '/admin/listings.php?u_id=' . $f['u_id'] . '">' . $lang['Admin_Edit_Listings'] . '</a> (' . $listings . ')');
			}
			else
			{
				$template->set( 'edit_listings', $lang['Admin_Edit_Listings'] );
			}
			
			if ( $f['image'] != '' )
			{
				$images = get_images( 'photos', $f['u_id'], 200, 150, 1, 1 );
				$image = $images[0];
			}
			else
			{
				$images = get_images( 'hidden', $f['u_id'], 200, 150, 1, 1 );
				$image = $images[0];
			}
			
			$template->set( 'photo', $image );
			
			$template->set( 'first_name', $f['first_name'] );
			$template->set( 'last_name', $f['last_name'] );
			$template->set( 'company_name', $f['company_name'] );
			
			// Trim description if JS is disabled or didn't work
			$description = substr( $f['description'], 0, $conf['search_description'] );
			$description = substr( $description, 0, strrpos( $description, ' ' ) ) . ' ...';
			$description = strip_tags( html_entity_decode( $description ) );
			
			$template->set( 'description', $description );
			
			$template->set( 'location1', $f['country'] );
			$template->set( 'location2', $f['state'] );
			$template->set( 'location3', $f['city'] );
			
			$template->set( 'address', $f['address'] );
			$template->set( 'zip', $f['zip'] );
			$template->set( 'phone', $f['phone'] );
			$template->set( 'fax', $f['fax'] );
			$template->set( 'mobile', $f['mobile'] );
			$template->set( 'email', validateemail( $f['u_id'], $f['email'] ) );
			$template->set( 'website', validatewebsite( $f['u_id'], $f['website'] ) );
			$template->set( 'view_user_listings', viewuserlistings( $f['u_id'] )  );
			
			$template->set( 'date_added', printdate( $f['date_added'] ) );
			$template->set( 'date_updated', printdate( $f['date_updated'] ) );
			
			$template->set( 'ip_added', $f['ip_added'] );
			$template->set( 'ip_updated', $f['ip_updated'] );
			
			$template->set( 'hits', $f['hits'] );
			
			$template->set( 'new', newitem( USERS_TABLE, $f['u_id'], $conf['new_days'] ) );
			$template->set( 'updated', updateditem( USERS_TABLE, $f['u_id'], $conf['updated_days'] ) );
			$template->set( 'top', topitem( $f['rating'], $f['votes'] ) );
			
			$voting_form = '<form action="' . URL . '/viewuser.php?req=rating&u_id=' . $f['u_id'] . '" method="POST"> <select name="vote" onChange="this.form.submit()">   <option value=""> --- </option>   <option value="5">' . $lang['Realtor_Vote_5'] . '</option>    <option value="4">' . $lang['Realtor_Vote_4'] . '</option>    <option value="3">' . $lang['Realtor_Vote_3'] . '</option>    <option value="2">' . $lang['Realtor_Vote_2'] . '</option>    <option value="1">' . $lang['Realtor_Vote_1'] . '</option> </select> </form>';
			
			$template->set( 'voting_form', $voting_form );
			
			$template->set( 'rating', rating ( $f['rating'], $f['votes'] ) );
			
			// Names
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
			
			$template->publish();
		}
		
		// Pagination
		$sql = "
		SELECT COUNT(*) AS total_results
		FROM " . USERS_TABLE . "
		WHERE 
			1 = 1
			" . $whereSQL . "
		";
		$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
		$f = $db->fetcharray( $q );
		$total_results = $f['total_results'];
		
		$custom['pagination'] = pagination( URL . '/admin/users.php', $_REQUEST['page'], $total_results, $conf['search_results'] );
	
	    if ( is_array( $custom['pagination'] ) )
	    {	
	    	$num = 1;
	    	echo '<br clear="both">';
	        foreach ( $custom['pagination'] AS $page )
	        {				        	
	        	if ( $_REQUEST['page'] == $page['page'] || ( $_REQUEST['page'] == '' && $num == 1 ) )
	        	{
	        		$bold = 'bold';
	        	}
	        	else
	        	{
	        		$bold = 'normal';
	        	}
	        	
	        	echo '<a href="' . $page['url'] . '" style="font-weight:' . $bold . '">' . $page['page'] . '</a>&nbsp;&nbsp;';
	        
				$num++;
	        }
	        echo '<br clear="both"><br clear="both">';
	    }
	}
	else
	{
		echo '<p align="center"><span class="warning">' . $lang['Nothing_Found'] . '</span></p>';
	}
	
	echo table_header ( $lang['Realtor_Search'] );
	
	echo '
	<a href="' . URL . '/admin/addusers.php">' . $lang['Admin_Add_Users'] . '</a> | 
	<a href="' . URL . '/admin/users.php">' . $lang['Admin_Edit_Users'] . '</a> | 
	<a href="' . URL . '/admin/users.php?realtor_approved=YES">' . $lang['Admin_Approve_New_Users'] . '</a> | 
	<a href="' . URL . '/admin/users.php?realtor_approved=YES&realtor_updated_days=5">' . $lang['Admin_Approve_Updated_Users'] . '</a> | ';
	if($session->fetch('role')!="SUPERUSER"){
	echo '<a href="' . URL . '/admin/editadmins.php">' . $lang['Admin_Edit_Administrators'] . '</a> | 
	<a href="' . URL . '/admin/privileges.php">' . $lang['Admin_Edit_Privileges'] . '</a> | ';
	}
	echo '<a href="' . URL . '/admin/banemails.php">' . $lang['Admin_Ban_e_mails'] . '</a>
	<br /><br /><br />
	';
	
	echo '<form action="' . URL . '/admin/users.php" method="post">';
	echo userform( $lang['Realtor_Approved'], '<input type="checkbox" name="realtor_approved" value="YES">');
	echo userform( $lang['Realtor_Show_Updated'], '<input type="checkbox" name="realtor_updated" value="YES"> ' . $lang['Realtor_Show_Updated_For_The_Last'] . ' <input type="text" size="3" name="realtor_updated_days" value="5" maxlength="3">' . $lang['days'] );
	echo userform( $lang['Realtor_First_Name'], '<input type="text" size="45" name="first_name" maxlength="20">');
	echo userform( $lang['Realtor_Last_Name'], '<input type="text" size="45" name="last_name" maxlength="20">');
	echo userform( $lang['Realtor_Company_Name'], '<input type="text" size="45" name="company_name" maxlength="20">');
	echo userform( $lang['Search_Keyword'], '<input type="text" size="45" name="description" maxlength="100">');
	
	$locations = '
	<select name="location1" id="location1">' . get_locations() . '</select><br />
	<select name="location2" id="location2"></select><br />
	<select name="location3" id="location3"></select>
	';
	
	echo userform( $lang['Location'], $locations );
	echo userform( $lang['Realtor_Address'], '<input type="text" size="45" name="address" maxlength="50">');
	if (strcasecmp($conf['show_postal_code'], 'OFF'))
	echo userform( $lang['Zip_Code'], '<input type="text" size="45" name="zip" maxlength="10">');
	echo userform( $lang['Realtor_Phone'], '<input type="text" size="45" name="phone" maxlength="15">');
	echo userform( $lang['Realtor_Fax'], '<input type="text" size="45" name="fax" maxlength="15">');
	echo userform( $lang['Realtor_Mobile'], '<input type="text" size="45" name="mobile" maxlength="15">');
	echo userform( $lang['Realtor_e_mail'], '<input type="text" size="45" name="email" maxlength="30">');
	echo userform( $lang['Realtor_Website'], '<input type="text" size="45" name="website" maxlength="50">');
	echo userform( $lang['Realtor_Login'], '<input type="text" size="45" name="login" maxlength="50">');
	echo userform ('', '<input type="Submit" name="realtor_search" value="' . $lang['Realtor_Search'] . '">');
	echo '</form>';

	echo table_footer();	
}	
else
{
	header( 'Location: index.php' );
	exit();
}
	
include PATH . '/admin/template/footer.php';

?>