<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include '../config.php';
include PATH . '/defaults.php';

$title = $lang['Realtor_Submit'];

include PATH . '/admin/template/header.php';

if ( adminAuth( $session->fetch( 'adminlogin' ), $session->fetch( 'adminpassword' ) ) )
{
	include PATH . '/admin/navigation.php';

	// Make sure this administrator have access to this function
	adminPermissionsCheck( 'manage_users', $session->fetch( 'adminlogin' ) ) or error( 'Critical Error', 'Incorrect privileges' );

	if ( $_POST['submit_realtor'] == true )
	{
		$form = array();
		
		$form = array_map('safehtml', $_POST);
		
		// Keep newlines.
		$form['realtor_description'] = safehtml_cms( $_POST['realtor_description'] );
		
		// Make login and password lower case
		$login2 = strtolower( $_POST['realtor_login'] );
		$password2 = $_POST['realtor_password'];
		
		// Trim description in case JavaScript is disabled
		$form['realtor_description'] = substr ($form['realtor_description'], 0, $conf['realtor_description_size']);
		
		echo table_header( $lang['Information'] );
		
		$count_error = 0;
		
		if (empty($form['realtor_e_mail']) || strlen($form['realtor_e_mail']) < 4  || !valid_email($form['realtor_e_mail']))
		{ echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Realtor_e_mail'] . '</span><br />'; $count_error++;}
		
		if (empty($form['realtor_login']) || strlen($form['realtor_login']) < 4 )
		{ echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Realtor_Login'] . '</span><br />'; $count_error++;}
		
		if (empty($form['realtor_password']) || strlen($form['realtor_password']) < 4 )
		{ echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Realtor_Password'] . '</span><br />'; $count_error++;}
		
		// Check if login is already exist
		$sql = 'SELECT login FROM ' . USERS_TABLE . ' WHERE login = "' . safehtml($login2) . '"';
		$r = $db->query($sql) or error ('Critical Error', mysql_error () );
		if ($db->numrows($r) > 0 )
		{ echo $lang['Login_Used'] . '<br />'; $count_error++;}
		
		// Check if email is banned
		$sql = 'SELECT * FROM ' . BANS_TABLE . ' WHERE name = "' . safehtml($form['realtor_e_mail']) . '" LIMIT 1';
		$r = $db->query($sql) or error ('Critical Error', mysql_error () );
		if ($db->numrows($r) > 0 )
		{ echo $lang['e_mail_Banned'] . '<br />'; $count_error++;}
		
		// Check if both passwords are equal
		if ($form['realtor_password'] != $form['realtor_password_2'])
		{ echo $lang['Passwords_Missmatch'] . '<br />'; $count_error++;}
		
		if ($count_error > '0')
		echo '<br /><span class="warning">' . $lang['Errors_Found'] . ': ' . $count_error . '</span><br />';
		
		// If no errors were found during the above checks we continue
		if ($count_error == '0')
		{
			$approved = 1;
			$user_ip = $_SERVER['REMOTE_ADDR'];
			
			$sql = "
			INSERT INTO " . USERS_TABLE . "
			(
				package,
				site_id, 
				approved, 
				first_name, 
				last_name, 
				company_name, 
				description, 
				location_1, 
				location_2, 
				location_3, 
				zip, 
				address, 
				phone, 
				fax, 
				mobile, 
				email, 
				website, 
				rating, 
				votes, 
				date_added, 
				ip_added, 
				login, 
				password
			)
			VALUES
			(
				'" . $form['package'] . "', 
				'".$session->fetch('site_id')."',
				'" . $approved . "', 
				'" . $form['realtor_first_name'] . "', 
				'" . $form['realtor_last_name']. "',
				'" . $form['realtor_company_name'] . "', 
				'" . $form['realtor_description'] . "', 
				'" . $form['location1'] . "', 
				'" . $form['location2'] . "',
				'" . $form['location3'] . "', 
				'" . $form['realtor_zip_code'] . "', 
				'" . $form['realtor_address'] . "', 
				'" . $form['realtor_phone'] . "', 
				'" . $form['realtor_fax'] . "', 
				'" . $form['realtor_mobile'] . "', 
				'" . $form['realtor_e_mail'] . "',
				'" . $form['realtor_website'] . "', 
				0,
				0, 
				'" . date ('Y-m-d') . "', 
				'" . $user_ip . "', 
				'" . $login2 . "', 
				'" . md5($password2) . "'
			)
			";
			$q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
			$id = mysql_insert_id();
			
			if ($form['package'] != '')
			{
				update_agents_package( $id , $form['package'] );
			}
			
			echo $lang['Admin_Realtor_Listing_Submitted'];
		}
		
		echo table_footer ( );
	}
		
	if ( !isset( $count_error ) || $count_error > 0 )
	{
		echo table_header( $lang['Menu_Submit_Listing'] );
	
		echo '
		<a href="' . URL . '/admin/addusers.php">' . $lang['Admin_Add_Users'] . '</a> | 
		<a href="' . URL . '/admin/users.php">' . $lang['Admin_Edit_Users'] . '</a> | 
		<a href="' . URL . '/admin/users.php?realtor_approved=YES">' . $lang['Admin_Approve_New_Users'] . '</a> | 
		<a href="' . URL . '/admin/users.php?realtor_approved=YES&realtor_updated_days=5">' . $lang['Admin_Approve_Updated_Users'] . '</a> | 
		<a href="' . URL . '/admin/editadmins.php">' . $lang['Admin_Edit_Administrators'] . '</a> | 
		<a href="' . URL . '/admin/privileges.php">' . $lang['Admin_Edit_Privileges'] . '</a> | 
		<a href="' . URL . '/admin/banemails.php">' . $lang['Admin_Ban_e_mails'] . '</a>
		<br /><br /><br />
		';
		
		// Define the form variables if the form is loaded for the first time
		if ( !isset( $form ) || ( isset( $count_error ) && $count_error == 0 ) )
		{
			$form = array();
			$form['package'] = '0';
			$form['realtor_first_name'] = '';
			$form['realtor_last_name'] = '';
			$form['realtor_company_name'] = '';
			$form['realtor_description'] = '';
			$form['realtor_address'] = '';
			$form['realtor_zip_code'] = '';
			$form['realtor_phone'] = '';
			$form['realtor_fax'] = '';
			$form['realtor_mobile'] = '';
			$form['realtor_e_mail'] = '';
			$form['realtor_website'] = '';
			$form['realtor_login'] = '';
			$form['realtor_password'] = '';
		}
		
		// Output the form
		echo '<form action="' . URL . '/admin/addusers.php" method="post">';
		echo userform ($lang['Admin_Packages_Name'], '<select name="package"><option value="0">Free</option>' . generate_agents_packages_list($form['package']) . '</select>');
		echo userform ($lang['Realtor_First_Name'], '<input type="text" size="45" name="realtor_first_name" value="' . $form['realtor_first_name'] . '" maxlength="255">', false );
		echo userform ($lang['Realtor_Last_Name'], '<input type="text" size="45" name="realtor_last_name" value="' . $form['realtor_last_name'] . '" maxlength="255">', false);
		echo userform ($lang['Realtor_Company_Name'], '<input type="text" size="45" name="realtor_company_name" value="' . $form['realtor_company_name'] . '" maxlength="255">');
		echo userform ($lang['Realtor_Description'], '<textarea wrap="soft" class="ckeditor" cols="45" rows="10"  name="realtor_description">' . html_entity_decode($form['realtor_description']) . '</textarea>');
		
		$locations = '
		<select name="location1" id="location1">' . get_locations() . '</select><br />
		<select name="location2" id="location2"></select><br />
		<select name="location3" id="location3"></select>
		';
		
		echo userform( $lang['Location'], $locations );
		
		echo userform ($lang['Realtor_Address'], '<input type="text" size="45" name="realtor_address" value="' . $form['realtor_address'] . '" maxlength="255">', false);
		if (strcasecmp($conf['show_postal_code'], 'OFF'))
		echo userform ($lang['Zip_Code'], '<input type="text" size="45" name="realtor_zip_code" value="' . $form['realtor_zip_code'] . '" maxlength="20">', false);
		echo userform ($lang['Realtor_Phone'], '<input type="text" size="45" name="realtor_phone" value="' . $form['realtor_phone'] . '" maxlength="50">', false);
		echo userform ($lang['Realtor_Fax'], '<input type="text" size="45" name="realtor_fax" value="' . $form['realtor_fax'] . '" maxlength="50">');
		echo userform ($lang['Realtor_Mobile'], '<input type="text" size="45" name="realtor_mobile" value="' . $form['realtor_mobile'] . '" maxlength="50">');
		echo userform ($lang['Realtor_e_mail'], '<input type="text" size="45" name="realtor_e_mail" value="' . $form['realtor_e_mail'] . '" maxlength="50">', '1');
		echo userform ($lang['Realtor_Website'], '<input type="text" size="45" name="realtor_website" value="' . $form['realtor_website'] . '" maxlength="255">');
		echo userform ($lang['Realtor_Login'], '<input type="text" size="45" name="realtor_login" value="' . $form['realtor_login'] . '"maxlength="50">', '1');
		echo userform ($lang['Realtor_Password'], '<input type="password" size="45" name="realtor_password" maxlength="50">', '1');
		echo userform ($lang['Realtor_Password_Repeat'], '<input type="password" size="45" name="realtor_password_2" maxlength="50">', '1');
		// Submit button
		echo userform ('', '<input type="Submit" name="submit_realtor" value="' . $lang['Realtor_Submit'] . '">');
		
		echo '</form>';

		echo table_footer();
	}
}
else
{
	header( 'Location: index.php' );
	exit();
}

include PATH . '/admin/template/footer.php';

?>