<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

// ----------------------------------------------------------------------
// ADMIN PANEL / EDIT USER

// Title tag
$title = $lang['Menu_Submit_Listing'];

// Template header
include ( PATH . '/admin/template/header.php' );

// Grab all data for this seller
$sql = "
SELECT 
	u.*,	
	l1.location_name AS location1_name,
	l2.location_name AS location2_name,
	l3.location_name AS location3_name,
	l1.location_id AS location1_id,
	l2.location_id AS location2_id,
	l3.location_id AS location3_id
FROM " . USERS_TABLE . " u
LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = u.location_1
LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = u.location_2
LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = u.location_3
WHERE 
	u.u_id = '" . $db->makeSafe( $_REQUEST['u_id'] ) . "'
";    
$q_user = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
$f_user = $db->fetcharray( $q_user );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {
  // Include navigation panel
  include ( PATH . '/admin/navigation.php' );

	// Delete logo
	if ( $_REQUEST['action'] == 'remove_logo' && $_REQUEST['u_id'] != '' )
	{
		// Get their image name to make sure this is the authorized image to remove
		$sql = "
		SELECT image
		FROM " . USERS_TABLE . "
		WHERE u_id = '" . $db->makeSafe( $_REQUEST['u_id'] ) . "'
		";
		$q2 = $db->query( $sql );
		$f2 = $db->fetcharray( $q2 );
		
		// Remove from DB
		$sql = "
		UPDATE " . USERS_TABLE . "
		SET	
			image = ''
		WHERE
			u_id = '" . $db->makeSafe( $_REQUEST['u_id'] ) . "'
		";
		$q2 = $db->query( $sql );
		
		// Delete their image
		remove_image( 'photos' , $f['image'] );
	}
	
	// If the user logo/photo was uploaded we start this routine
	if ( $_FILES['logo_file']['tmp_name'] != '' )
	{
		// Upload and resize the image
		if ( upload_image( 'photos', $_REQUEST['u_id'], $_FILES['logo_file'] ) )
		{
			$uploaded = true;
		}
		else
		{
			$uploaded = false;
		}
	}

  // Check the id variable passed to the script
  if ( $_REQUEST['u_id'] != '' )

   {
    // Make sure this administrator have access to this script
    adminPermissionsCheck('manage_users', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

    // Fetching the user ID from the user's table
    $sql = 'SELECT id, password FROM ' . USERS_TABLE . ' WHERE u_id = "' . intval($_GET['u_id']) . '" LIMIT 1';
    $r2 = $db->query( $sql );
    $f2 = $db->fetcharray( $r2 );

    // If the Submit button was pressed we start this routine
    if (isset($_POST['submit_realtor'])
    && $_POST['submit_realtor'] == $lang['Realtor_Submit'])
     {

      $form = array();

      // safehtml() all the POST variables
      // to insert into the database or
      // print the form again if errors
      // found
      $form = array_map('safehtml', $_POST);

      // Keep newlines.
      $form['realtor_description'] = safehtml_cms(@$_POST['realtor_description']);

	  	// Check if password was changed
		if ( $f_user['password'] == $_POST['realtor_password'] )
		{
			$password = $f_user['password'];
		}
		elseif ( $f_user['password'] != $_POST['realtor_password'] && $_POST['realtor_password'] != '' )
		{
			$password = md5( $_POST['realtor_password'] );
		}

      // Cut the description to the size specified in config if the Java Script
      // is disabled in the user browser
      $form['realtor_description'] = substr ($form['realtor_description'], 0, $conf['realtor_description_size']);

      echo table_header ( $lang['Information'] );

      // Initially we think that no errors were found
      $count_error = 0;

      // Check for the empty or incorrect required fields
      /*
      if (empty($form['realtor_first_name']) || strlen($form['realtor_first_name']) < 2 )
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Realtor_First_Name'] . '</span><br />'; $count_error++;}

      if (empty($form['realtor_last_name']) || strlen($form['realtor_last_name']) < 2 )
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Realtor_Last_Name'] . '</span><br />'; $count_error++;}

      if (empty($form['realtor_address']) || strlen($form['realtor_address']) < 4 )
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Realtor_Address'] . '</span><br />'; $count_error++;}

      if (empty($form['realtor_phone']) || strlen($form['realtor_phone']) < 4 )
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Realtor_Phone'] . '</span><br />'; $count_error++;}
	   */

      if (empty($form['realtor_e_mail']) || strlen($form['realtor_e_mail']) < 4  || !valid_email($form['realtor_e_mail']))
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Realtor_e_mail'] . '</span><br />'; $count_error++;}

      if (empty($form['realtor_password']) || strlen($form['realtor_password']) < 4 )
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Realtor_Password'] . '</span><br />'; $count_error++;}

      // Check if both passwords are equal
      if ($form['realtor_password'] != $form['realtor_password_2'])
       { echo $lang['Passwords_Missmatch'] . '<br />'; $count_error++;}

      // Check if this email is already used
      if (strcasecmp($conf['allow_same_e_mail'], 'OFF')) {
         $sql = 'SELECT u_id FROM ' . USERS_TABLE . ' WHERE email = "' . $form['realtor_e_mail'] . '" AND u_id != "' . intval($_REQUEST['u_id']) . '"';
         $r = $db->query($sql) or error ('Critical Error', mysql_error () );

         if ($db->fetcharray($r) > 0 )
          { echo $lang['Email_User'] . '<br />'; $count_error++;}
      }

      if ($count_error > '0')
       echo '<br /><span class="warning">' . $lang['Errors_Found'] . ': ' . $count_error . '</span><br />';

      // If no errors were found during the above checks we continue
      if ($count_error == '0')
       {

        // Update user details in the database

        // Get the user IP address
        $user_ip = $_SERVER['REMOTE_ADDR'];
        // If there is more than one IP
        // get the first one from the
        // comma separated list
        if ( strstr($user_ip, ', ') )
         {
          $ips = explode(', ', $user_ip);
          $user_ip = $ips[0];
         }

        // Create a mysql query
        $sql = 'UPDATE '. USERS_TABLE .
	       ' SET package = "' . $form['package'] . '",
		     first_name = "' . $form['realtor_first_name'] . '",
            	     last_name = "' . $form['realtor_last_name']. '",
	             company_name = "' . $form['realtor_company_name'] . '",
	             description = "' . $form['realtor_description'] . '",
	             location_1 = "' . $form['location1'] . '",
	             location_2 = "' . $form['location2'] . '",
	             location_3 = "' . $form['location3'] . '",
	             zip = "' . $form['realtor_zip_code'] . '",
	             address = "' . $form['realtor_address'] . '",
	             phone = "' . $form['realtor_phone'] . '",
	             fax = "' . $form['realtor_fax'] . '",
	             mobile = "' . $form['realtor_mobile'] . '",
	             email = "' . $form['realtor_e_mail'] . '",
	             website = "' . $form['realtor_website'] . '",
	             date_updated = "' . date('Y-m-d') . '",
	             ip_updated = "' . $user_ip . '",
	             password = "' . $password . '"
	           WHERE u_id = "' . intval($_GET['u_id']) . '"';
        $db->query($sql) or error ('Critical Error', mysql_error ());

        $sql = 'SELECT * FROM ' . FEATURED_AGENTS_TABLE . ' WHERE id = ' . $_GET['u_id'] . ' LIMIT 1';
        $r_featured = $db->query ($sql) or error ('Critical Error', mysql_error () );

        // If it is already paid
        if ($db->numrows($r_featured) > 0)
         {

          $f_featured = $db->fetcharray($r_featured);

          if ($form['package'] != $f_featured['package'] && $form['package'] != '' && $form['package'] != '0')
    	   update_agents_package ( intval($_GET['u_id']), $form['package'] );

          // If was featured and we change it to FREE

          if ($form['package'] == '')
           {

            // Updating the featured table
            $sql = 'DELETE FROM ' . FEATURED_AGENTS_TABLE . ' WHERE id = ' . $_GET['u_id'] . ' LIMIT 1';
            $db->query($sql) or error ('Critical Error', mysql_error ());

            // Updating the users table
            $sql = 'UPDATE ' . USERS_TABLE . ' SET package = ""  WHERE u_id = ' . $_GET['u_id'] . ' LIMIT 1';
            $db->query($sql) or error ('Critical Error', mysql_error ());

            //

            $sql = 'SELECT listing_id, type FROM ' . PROPERTIES_TABLE . ' WHERE approved = 1 AND userid = ' . $_GET['u_id'];
            $rl = $db->query($sql);
            $listings_number = $db->numrows($rl);

            if ($listings_number > $conf['free_listings'])
             {
              while ($fl = $db->fetcharray($rl))
                // If set to delete
                if ($conf['expired_listings'] == '2')
                    removeuserlisting($fl['listing_id']);
                else
                {
                    update_categories($fl['type'], '');

                    // Set to 'expired' which is approved = 2
                    $sql = "UPDATE " . PROPERTIES_TABLE . "
                            SET date_approved = NULL,
                                approved = '2'
                            WHERE listing_id = '" . $fl['listing_id'] . "'
                            LIMIT 1";
                    $ul = $db->query($sql);
                }
             }
             $db->freeresult($rl);

            $sql = 'SELECT * FROM ' . PROPERTIES_TABLE . ' WHERE approved = 1 AND userid = ' . $_GET['u_id'];
            $rl = $db->query($sql);

            while ($fl = $db->fetcharray($rl))
             {
              $sql = 'SELECT * FROM ' . GALLERY_TABLE . ' WHERE listingid = ' . $fl['id'];
              $rg = $db->query($sql);

              if ($db->numrows($rg) > $conf['free_gallery'])
               {

                while ($fg = $db->fetcharray($rg))
                 removelistingimage( 'gallery', $fg['image_name'] );

                $sql = 'DELETE FROM ' . GALLERY_TABLE . ' WHERE listingid = ' . $fl['id'];
                $db->query($sql);
               }

             }
             $db->freeresult($rl);

           }

         }
        else
	 {
          if ($form['package'] != '')
    	   update_agents_package ( intval($_GET['u_id']), $form['package'] );

         }

        // Output the 'Thank you' message
        echo $lang['Realtor_Listing_Updated'];
       }

      echo table_footer ( );
     }

	// Grab all data for this seller
	$sql = "
	SELECT 
		u.*,	
		l1.location_name AS location1_name,
		l2.location_name AS location2_name,
		l3.location_name AS location3_name,
		l1.location_id AS location1_id,
		l2.location_id AS location2_id,
		l3.location_id AS location3_id
	FROM " . USERS_TABLE . " u
	LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = u.location_1
	LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = u.location_2
	LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = u.location_3
	WHERE 
		u.u_id = '" . $db->makeSafe( $_REQUEST['u_id'] ) . "'
	";    
    $q = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
    $f = $db->fetcharray( $q );

    // Upload logo/photo form

    echo table_header ( $lang['Realtor_Logo'] );

    // Show user logo/picture if exist
   if ( $f['image'] != '' )
   {
   		$images = get_images( 'photos', $f['u_id'], 200, 150, 1, 1 );
   		$image = $images[0];
   }
    
    if ( $f['image'] != '' )
    {
    	echo '<p align="center"><img src="' . $image . '" border="0"></p>';
    	
    	$del_link = '<a href="' . URL . '/admin/editusers.php?action=remove_logo&u_id=' . $_REQUEST['u_id'] . '">Delete</a>';
	}

    echo '
     <form action="' . URL . '/admin/editusers.php?u_id=' . intval($_REQUEST['u_id']) . '" method="POST" enctype="multipart/form-data">
      <table width="100%" cellpadding="5" cellspacing="0" border="0">
         ';

    echo userform ($lang['Realtor_Logo_File'], '<input type="file" size="40" name="logo_file" maxlength="50">', '1');
    echo userform ('', '<input type="Submit" name="submit_logo" value="' . $lang['Realtor_Submit_Logo'] . '"> ' . $del_link );

    echo '
      </table>
     </form>
         ';

    // If image was uploaded
    if (isset($uploaded) && $uploaded)
     echo '<p align="center"><span class="warning">' . $lang['Realtor_Image_Uploaded'] . '</span></p>';

    // If image was not uploaded because of the image
    // size problems etc.
    if (isset($uploaded) && !$uploaded)
     echo '<p align="center"><span class="warning">' . $lang['Realtor_Image_NOT_Uploaded'] . '</span></p>';

    echo table_footer ();

    // Main form

    echo table_header ( $lang['Menu_Submit_Listing'] );

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
    
    // Define the form variables if the form was not updated
    if (!isset($form))
     {

      $form = array();
      $form['package'] = $f['package'];
      $form['realtor_first_name'] = $f['first_name'];
      $form['realtor_last_name'] = $f['last_name'];
      $form['realtor_company_name'] = $f['company_name'];
      $form['realtor_description'] = $f['description'];
      $form['realtor_location'] = $f['location'];
      $form['realtor_city'] = $f['city'];
      $form['realtor_address'] = $f['address'];
      $form['realtor_zip_code'] = $f['zip'];
      $form['realtor_phone'] = $f['phone'];
      $form['realtor_fax'] = $f['fax'];
      $form['realtor_mobile'] = $f['mobile'];
      $form['realtor_e_mail'] = $f['email'];
      $form['realtor_website'] = $f['website'];
      $form['realtor_password'] = $f['password'];
     }
    else
     $form['realtor_password'] = $password;

    // Output the form
    echo '
     <form action="' . URL . '/admin/editusers.php?u_id=' . intval($_GET['u_id']) . '" method="post">
      <table width="100%" cellpadding="5" cellspacing="0" border="0">
         ';

    echo userform ('ID', $f['u_id'] );
    echo userform ('Username', $f['login'] );
    echo userform ($lang['Admin_Packages_Name'], '<select name="package"><option value="0">Free</option>' . generate_agents_packages_list($form['package']) . '</select>');
    echo userform ($lang['Realtor_First_Name'], '<input type="text" size="45" name="realtor_first_name" value="' . $form['realtor_first_name'] . '" maxlength="255">', false );
    echo userform ($lang['Realtor_Last_Name'], '<input type="text" size="45" name="realtor_last_name" value="' . $form['realtor_last_name'] . '" maxlength="255">', false );
    echo userform ($lang['Realtor_Company_Name'], '<input type="text" size="45" name="realtor_company_name" value="' . $form['realtor_company_name'] . '" maxlength="255">');
    echo userform ($lang['Realtor_Description'], '<textarea wrap="soft" class="ckeditor" cols="45" rows="10"  name="realtor_description">' . html_entity_decode($form['realtor_description']) . '</textarea>');
   
	// Defaults
	if ( $f['location1_id'] != '' && $f['location1_name'] != '' )
	{
		$location1_default = '<option value="' . $f['location1_id'] . '">' . $f['location1_name'] . '</option>';
	}

	if ( $f['location2_id'] != '' && $f['location2_name'] != '' )
	{
		$location2_default = '<option value="' . $f['location2_id'] . '">' . $f['location2_name'] . '</option>';
	}

	if ( $f['location3_id'] != '' && $f['location3_name'] != '' )
	{
		$location3_default = '<option value="' . $f['location3_id'] . '">' . $f['location3_name'] . '</option>';
	}

	$locations = '
	<select name="location1" id="location1">' . $location1_default . '' . get_locations() . '</select><br />
	<select name="location2" id="location2">' . $location2_default . '</select><br />
	<select name="location3" id="location3">' . $location3_default . '</select>
	';

   echo userform( $lang['Location'], $locations );
   
    echo userform ($lang['Realtor_Address'], '<input type="text" size="45" name="realtor_address" value="' . $form['realtor_address'] . '" maxlength="255">', false );
    if (strcasecmp(@$conf['show_postal_code'], 'OFF'))
        echo userform ($lang['Zip_Code'], '<input type="text" size="45" name="realtor_zip_code" value="' . $form['realtor_zip_code'] . '" maxlength="20">', false);
    echo userform ($lang['Realtor_Phone'], '<input type="text" size="45" name="realtor_phone" value="' . $form['realtor_phone'] . '" maxlength="50">', false );
    echo userform ($lang['Realtor_Fax'], '<input type="text" size="45" name="realtor_fax" value="' . $form['realtor_fax'] . '" maxlength="50">');
    echo userform ($lang['Realtor_Mobile'], '<input type="text" size="45" name="realtor_mobile" value="' . $form['realtor_mobile'] . '" maxlength="50">');
    echo userform ($lang['Realtor_e_mail'], '<input type="text" size="45" name="realtor_e_mail" value="' . $form['realtor_e_mail'] . '" maxlength="50">', '1');
    echo userform ($lang['Realtor_Website'], '<input type="text" size="45" name="realtor_website" value="' . $form['realtor_website'] . '" maxlength="255">');
    echo userform ($lang['Realtor_Password'], '<input type="password" size="45" name="realtor_password" value="' . $form['realtor_password'] . '" maxlength="50">', '1');
    echo userform ($lang['Realtor_Password_Repeat'], '<input type="password" size="45" name="realtor_password_2" value="' . $form['realtor_password'] . '" maxlength="50">', '1');

    echo userform ('', '<input type="Submit" name="submit_realtor" value="' . $lang['Realtor_Submit'] . '">');

    echo '
      </table>
     </form>
         ';

    echo table_footer ();

    // Statistics

    echo table_header ( $lang['Information'] );

    echo '<span class="bold">' . $lang['Listing_Added_Date'] . ':</span> ' . printdate($f['date_added']) . ' (' . $f['ip_added'] . ', ' . @gethostbyaddr($f['ip_added']) . ') <br />';

    if (!empty($f['date_updated']))
     echo '<span class="bold">' . $lang['Listing_Updated_Date'] . ':</span> ' . printdate($f['date_updated']) . ' (' . $f['ip_updated'] . ', ' . @gethostbyaddr($f['ip_updated']) . ') <br />';

    echo '<span class="bold">' . $lang['Hits'] . ':</span> ' . $f['hits'] . ' <br />';

    echo table_footer ();

   }

  else echo 'No user selected';

 }

// if (auth_check) end

else
{
	header( 'Location: index.php' );
	exit();
}

// Template footer
include ( PATH . '/admin/template/footer.php' );

?>