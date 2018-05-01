<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

// Title tag content
$title = $lang['Menu_Submit_Property'];

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))
 {
  // Include navigation panel
  include ( PATH . '/admin/navigation.php' );

  // Make sure this administrator can access this script
  adminPermissionsCheck('manage_listings', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

	// Delete gallery image
	if ( $_REQUEST['action'] == 'delete_image' && $_REQUEST['id'] != '' )
	{
		// Delete the image
		$sql = "
		SELECT image_name 
		FROM " . GALLERY_TABLE . "
		WHERE
			id = '" . $db->makeSafe( $_REQUEST['id'] ) . "'
		";
		$q = $db->query( $sql );
		if ( $db->numrows( $q ) > 0 )
		{
			$f = $db->fetcharray( $q );
			
			// Delete from DB
			$sql = "DELETE FROM " . GALLERY_TABLE . " WHERE id = '" . $db->makeSafe( $_REQUEST['id'] ) . "'";
			$q2 = $db->query( $sql );
			
			remove_image( 'gallery', $f['image_name'] );
		}
	}

  // Check the id variable passed to the script
  if ( $_REQUEST['listing_id'] != '' )
   {

    // If user removed the image we run the following
    if (isset($_POST['submit_image_remove'])
    && $_POST['submit_image_remove'] == $lang['Listing_Submit_Image_Remove'])
     remove_image ( 'gallery' , $_REQUEST['listing_id']);

    // If the Submit button was pressed we start this routine
    if (isset($_POST['submit_listing'])
    && $_POST['submit_listing'] == $lang['Listing_Submit'])
     {

      $form = array();

      // Change checkbox arrays into a string, with ':' as a separator
      if (isset($_POST['buildings']) && is_array($_POST['buildings'])) $_POST['buildings'] = implode (':', $_POST['buildings']); else $_POST['buildings'] = '';
      if (isset($_POST['appliances']) && is_array($_POST['appliances'])) $_POST['appliances'] = implode (':', $_POST['appliances']); else $_POST['appliances'] = '';
      if (isset($_POST['features']) && is_array($_POST['features'])) $_POST['features'] = implode (':', $_POST['features']); else $_POST['features'] = '';

      if (empty($_POST['display_address'])) $_POST['display_address'] = 'NO';

      // safehtml() all the POST variables
      // to insert into the database or
      // print the form again if errors
      // found

      $form = array_map('safehtml', $_POST);
      // Cut the description size to the one set in the configuration
      // just in case the java Script is disabled in user browser
      $form['description'] = substr ($form['description'], 0, $conf['listing_description_size']);

      echo table_header ( $lang['Information'] );

      // Initially we think that no errors were found
      $count_error = 0;

     if ( $form['location1'] == '')
     {
     	echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Location'] . '</span><br />'; $count_error++;
     }

      // Check for the empty or incorrect required fields
      if (empty($form['title']) || strlen($form['title']) < 4 )
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Title'] . '</span><br />'; $count_error++;}

/*
      if ((!empty($form['bathrooms'])) && !preg_match( '/[0-9]+/', $form['bathrooms']))
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Bathrooms'] . '</span><br />'; $count_error++;}

      if ((!empty($form['half_bathrooms'])) && !preg_match( '/[0-9]+/', $form['half_bathrooms']))
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Half_Bathrooms'] . '</span><br />'; $count_error++;}

      if ((!empty($form['bedrooms'])) && !preg_match( '/[0-9]+/', $form['bedrooms']))
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Bedrooms'] . '</span><br />'; $count_error++;}

      if ((!empty($form['year_built'])) && !preg_match( '/[0-9]+/', $form['year_built']))
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Year_Built'] . '</span><br />'; $count_error++;}

      if ((!empty($form['status'])) && !preg_match( '/[0-9]+/', $form['status']))
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Status'] . '</span><br />'; $count_error++;}
*/

      if ( $form['price'] == '' )
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Price'] . '</span><br />'; $count_error++;}

      if ( (!empty($form['start_date']) && !empty($form['end_date']) && $form['start_date'] >= $form['end_date'])
      || (!empty($form['start_date']) && empty($form['end_date']) )
      || (empty($form['start_date']) && !empty($form['end_date']) ) )
       { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Featured_Dates'] . '</span><br />'; $count_error++;}

      if ($count_error > '0')
       echo '<br /><span class="warning">' . $lang['Errors_Found'] . ': ' . $count_error . '</span><br />';

      // If no errors were found during the above checks we continue
      if ($count_error == '0')
       {

        $user_ip = $_SERVER['REMOTE_ADDR'];

        if ($form['approved'] != 1) {
            // If listing is not approved anymore, reset the approval date
            $approved_date = 'NULL';
        } elseif ($approved_date && strncmp($approved_date, '0000-00-00', 10)) {
            // If already approved, don't change the date
            $approved_date = '"'.$approved_date.'"';
        } else {
            // Newly approved
            $approved_date = 'NOW()';
        }
        
       // Add custom value if text input field
       $custom_fields = array(
			'custom1' => $form['custom1'],
			'custom2' => $form['custom2'],
			'custom3' => $form['custom3'],
			'custom4' => $form['custom4'],
			'custom5' => $form['custom5'],
			'custom6' => $form['custom6'],
			'custom7' => $form['custom7'],
			'custom8' => $form['custom8'],
			'custom9' => $form['custom9'],
			'custom10' => $form['custom10'],
       );
       foreach ( $custom_fields AS $key => $value )
       {
       		if ( $value != '' )
       		{
       			// Check if this is an input field
       			$sql = "SELECT id, type FROM " . FIELDS_TABLE . " WHERE field = '" . $key . "' ";
       			$r = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
       			$f = $db->fetcharray( $r );
       			if ( $f['type'] == 'input' )
       			{      				
       				// Override value of this custom input box with the primary key of the VALUES table
       				$form[$key] = '{INPUT}' . $value;
       			}
       		}
       }

		$form['price'] = preg_replace( '/[^0-9]+/', '', $form['price'] );
		$form['video'] = htmlentities( $form['video'] );

        // Create a mysql query
        $sql =  '
        UPDATE '. PROPERTIES_TABLE . ' SET
             mls = "' . $form['mls'] . '",
          	 type = "' . $form['type']. '",
	         style = "' . $form['style']. '",
			title = "' . $form['title'] . '", title2 = "' . $form['title2'] . '", title3 = "' . $form['title3'] . '", title4 = "' . $form['title4'] . '", title5 = "' . $form['title5'] . '",
			title6 = "' . $form['title6'] . '", title7 = "' . $form['title7'] . '", title8 = "' . $form['title8'] . '", title9 = "' . $form['title9'] . '", title10 = "' . $form['title10'] . '",
			title11 = "' . $form['title11'] . '", title12 = "' . $form['title12'] . '", title13 = "' . $form['title13'] . '", title14 = "' . $form['title14'] . '", title15 = "' . $form['title15'] . '",
			title16 = "' . $form['title16'] . '", title17 = "' . $form['title17'] . '", title18 = "' . $form['title18'] . '", title19 = "' . $form['title19'] . '", title20 = "' . $form['title20'] . '",
			title21 = "' . $form['title21'] . '", title22 = "' . $form['title22'] . '", title23 = "' . $form['title23'] . '", title24 = "' . $form['title24'] . '", title25 = "' . $form['title25'] . '",
			title26 = "' . $form['title26'] . '", title27 = "' . $form['title27'] . '", title28 = "' . $form['title28'] . '", title29 = "' . $form['title29'] . '", title30 = "' . $form['title30'] . '",
			description = "' . $form['description'] . '", description2 = "' . $form['description2'] . '", description3 = "' . $form['description3'] . '", description4 = "' . $form['description4'] . '", description5 = "' . $form['description5'] . '",
			description6 = "' . $form['description6'] . '", description7 = "' . $form['description7'] . '", description8 = "' . $form['description8'] . '", description9 = "' . $form['description9'] . '", description10 = "' . $form['description10'] . '",
			description11 = "' . $form['description11'] . '", description12 = "' . $form['description12'] . '", description13 = "' . $form['description13'] . '", description14 = "' . $form['description14'] . '", description15 = "' . $form['description15'] . '",
			description16 = "' . $form['description16'] . '", description17 = "' . $form['description17'] . '", description18 = "' . $form['description18'] . '", description19 = "' . $form['description19'] . '", description20 = "' . $form['description20'] . '",
			description21 = "' . $form['description21'] . '", description22 = "' . $form['description22'] . '", description23 = "' . $form['description23'] . '", description24 = "' . $form['description24'] . '", description25 = "' . $form['description25'] . '",
			description26 = "' . $form['description26'] . '", description27 = "' . $form['description27'] . '",	description28 = "' . $form['description28'] . '", description29 = "' . $form['description29'] . '",	description30 = "' . $form['description30'] . '",
	         size = "' . $form['size'] . '",
	         dimensions = "' . $form['dimensions'] . '",
	         bathrooms = "' . $form['bathrooms'] . '",
	         half_bathrooms = "' . $form['half_bathrooms'] . '",
	         bedrooms = "' . $form['bedrooms'] . '",
		     location_1 = "' . $form['location1'] . '",
		     location_2 = "' . $form['location2'] . '",
		     location_3 = "' . $form['location3'] . '",
             zip = "' . $form['zip'] . '",
	         address1 = "' . $form['address1'] . '",
	         address2 = "' . $form['address2'] . '",
	         display_address = "' . $form['display_address'] . '",
	         price = "' . $form['price'] . '",
	         directions = "' . $form['directions'] . '",
	         year_built = "' . $form['year_built'] . '",
	         buildings = "' . $form['buildings'] . '",
	         appliances = "' . $form['appliances'] . '",
	         features = "' . $form['features'] . '",
	         garage = "' . $form['garage'] . '",
	         garage_cars = "' . $form['garage_cars'] . '",
	         basement = "' . $form['basement'] . '",
	         date_updated = "' . date('Y-m-d') . '",
	         ip_updated = "' . $user_ip . '",
		     type2 = "' . $form['type2'] . '",
		     video = "' . $form['video'] . '",
		     latitude = "' . $form['latitude'] . '",
		     longitude = "' . $form['longitude'] . '",
		     status = "' . $form['status'] . '",
		     approved = "' . $form['approved'] . '",
		     calendar = "' . $form['calendar'] . '",
	    	 custom1 = "' . $form['custom1'] . '",
			 custom2 = "' . $form['custom2'] . '",
			 custom3 = "' . $form['custom3'] . '",
			 custom4 = "' . $form['custom4'] . '",
			 custom5 = "' . $form['custom5'] . '",
			 custom6 = "' . $form['custom6'] . '",
			 custom7 = "' . $form['custom7'] . '",
			 custom8 = "' . $form['custom8'] . '",
			 custom9 = "' . $form['custom9'] . '",
			 custom10 = "' . $form['custom10'] . '"
	         WHERE listing_id = "' . $db->makeSafe( $_REQUEST['listing_id'] ) . '"';
        $db->query($sql) or error ('Critical Error', mysql_error ());

        $sql = 'SELECT * FROM ' . FEATURED_TABLE . ' WHERE id = "' . $_REQUEST['listing_id'] . '" LIMIT 1';
        $r_featured = $db->query ($sql) or error ('Critical Error', mysql_error () );
        $f_featured = $db->fetcharray($r_featured);

        if ( $form['package'] != $f_featured['package'] )
        {
			update_package( intval( $_REQUEST['listing_id'] ), $form['package'] );
		}

		echo $lang['Listing_Updated'];

       }

      echo table_footer ( );

     }

    // Fetching the listing data
	$sql = "
	SELECT
		p.*,
		l1.location_name AS location1_name,
		l2.location_name AS location2_name,
		l3.location_name AS location3_name,
		l1.location_id AS location1_id,
		l2.location_id AS location2_id,
		l3.location_id AS location3_id,
		users.u_id,
		featured.package
	FROM " . PROPERTIES_TABLE  . " AS p
	LEFT JOIN " . LOCATIONS_TABLE . " AS l1 ON l1.location_id = p.location_1
	LEFT JOIN " . LOCATIONS_TABLE . " AS l2 ON l2.location_id = p.location_2
	LEFT JOIN " . LOCATIONS_TABLE . " AS l3 ON l3.location_id = p.location_3
	LEFT JOIN " . USERS_TABLE . " AS users ON users.u_id = p.userid
	LEFT JOIN " . FEATURED_TABLE . " AS featured ON featured.id = p.listing_id
	WHERE 
		p.listing_id = '" . $db->makeSafe( $_REQUEST['listing_id'] ) . "'
	";
	$q = $db->query( $sql );
    $f = $db->fetcharray( $q );
    
	$_SESSION['admin'] = true;
	$_SESSION['user_id'] = $f['userid'];
	$_SESSION['listing_id'] = $_REQUEST['listing_id'];
	$_SESSION['image_session'] = '';

    // Upload image form

    echo table_header ( $lang['Listing_Image'] );

	// Image gallery
	$custom['gallery_list'] = array();
	
	$sql = "
	SELECT * 
	FROM " . GALLERY_TABLE . "
	WHERE 
		listingid = '" . $db->makeSafe( $f['listing_id'] ) . "'
	ORDER BY id ASC
	";
	$q2 = $db->query( $sql ) or error( 'Critical Error', mysql_error() );
	if ( $db->numrows( $q2 ) > 0 )
	{
		$num = 0;
	
		while( $f2 = $db->fetcharray( $q2 ) )
		{
			$custom['gallery_list'][$num]['thumb'] = show_image( 'gallery', $f2['image_name'], 110, 75 );
			$custom['gallery_list'][$num]['full'] = show_image( 'gallery', $f2['image_name'], 870, 420 );
			$custom['gallery_list'][$num]['id'] = $f2['id'];
			
			$num++;
		}	
	}
	
	if ( is_array( $custom['gallery_list'] ) && $custom['gallery_list'][0]['id'] != '' )
	{
		foreach ( $custom['gallery_list'] AS $image )
		{
			if ( $image['thumb'] == '' )
			{
				$image['thumb'] = MEDIA_URL . '/error.png';
			}
	
			echo '<a href="' . URL . '/admin/editlistings.php?action=delete_image&id=' . $image['id'] . '&listing_id=' . $f['listing_id'] . '"><img src="' . $image['thumb'] . '" border="0" title="Delete Image"></a>&nbsp;';
		}
	}
	
	echo '
	<br /><br />
	<div id="fileuploader">Upload</div>
	<div id="status"></div>
	';

    echo table_footer ();

    // Main form

    echo table_header ( $lang['Menu_Submit_Property'] );

    $sql = 'SELECT * FROM ' . FEATURED_TABLE . ' WHERE id = ' . $f['listing_id'] . ' LIMIT 1';
    $r_featured = $db->query ($sql) or error ('Critical Error', mysql_error () );
    $f_featured = $db->fetcharray($r_featured);

    $form = $f;
      
	// Location multi-drop down
	$form['location1_name'] = $f['location1_name'];
	$form['location1_id'] = $f['location1_id'];
	
	$form['location2_name'] = $f['location2_name'];
	$form['location2_id'] = $f['location2_id'];
	
	$form['location3_name'] = $f['location3_name'];
	$form['location3_id'] = $f['location3_id'];

      // Custom fields
      $form['custom1'] = str_replace( '{INPUT}', '', $f['custom1'] );
      $form['custom2'] = str_replace( '{INPUT}', '', $f['custom2'] );
      $form['custom3'] = str_replace( '{INPUT}', '', $f['custom3'] );
      $form['custom4'] = str_replace( '{INPUT}', '', $f['custom4'] );
      $form['custom5'] = str_replace( '{INPUT}', '', $f['custom5'] );
      $form['custom6'] = str_replace( '{INPUT}', '', $f['custom6'] );
      $form['custom7'] = str_replace( '{INPUT}', '', $f['custom7'] );
      $form['custom8'] = str_replace( '{INPUT}', '', $f['custom8'] );
      $form['custom9'] = str_replace( '{INPUT}', '', $f['custom9'] );
      $form['custom10'] = str_replace( '{INPUT}', '', $f['custom10'] );

 	  // Default for approval
 	  if ($form['approved'] == '' || $form['approved'] == '0')
 	  	$approved0 = 'selected';
 	  elseif ($form['approved'] == '1')
 	  	$approved1 = 'selected';
 	  else
 	  	$approved2 = 'selected';
 	  	
		$f['appliances'] = explode( ':', $f['appliances'] );
		$f['features'] = explode( ':', $f['features'] );
		$f['buildings'] = explode( ':', $f['buildings'] ); 

    // Output the form
    echo '
     <form action="' . URL . '/admin/editlistings.php?listing_id=' . intval($f['listing_id']) . '" method="post" name="form">
      <table width="100%" cellpadding="5" cellspacing="0" border="0">
         ';

    echo userform ('ID', $f['listing_id']);

    echo userform( $lang['Listing_Approval'], '<select name="approved" class="formEditSelect"><option value="1" ' . $approved1 . '>Active</option><option value="0" ' . $approved0 . '>Inactive</option><option value="2" ' . $approved2 . '>Expired</option></select>');
    
    echo userform( 
    	$lang['Admin_Packages_Name'], 
    	'<select name="package"><option value="0">Free</option>' . generate_packages_list( $f['package'] ) . '</select>
    ');

    if ( $form['package'] != 0 )
    {
		echo userform( $lang['Admin_Listing_Expire'], printdate( $f_featured['end_date'] ) );
	}

    echo userform ($lang['Module_Listing_Type'], '<select name="type2">' . generate_options_list(TYPES2_TABLE, $form['type2']) . '</select>', '1');
    echo userform ($lang['Listing_Status'], '<select name="status" class="formEditSelect">' . generate_options_list(STATUS_TABLE, $form['status']) . '</select>', '1');
    if (strcasecmp(@$conf['show_mls'], 'OFF') != 0) {
        echo userform ($lang['Listing_MLS'], '<input type="text" size="45" name="mls" value="' . $form['mls'] . '" maxlength="10">');
    }

	// Show all available languages
	foreach ($installed_languages AS $language1 => $key1)
	{
		$key1 = str_replace( 'name', 'title', $key1 );
		$title_html .= '<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').toggle();">' . ucwords($language1) . '</a>&nbsp; ';
	}

	// Display text boxes for every language
	$num = 1;
	foreach ($installed_languages AS $language1 => $key1)
	{
		// Grab the right text for each textarea
		$strip = str_replace( 'name', 'title', $key1 );
		$key1 = str_replace( 'name', 'title', $key1 );

		if ($num == 1)
			$display = 'normal';
		else
			$display = 'none';

		$title_html .= '
		<div style="display: ' . $display . ';" id="' . $key1 . '">
		<input type="text" size="100" maxlength="255" name="' . $key1 . '" id="' . $key1 . '" class="formSubmitInput" value="' . $form[$strip] . '">
		</div>
		';

		$num++;
	}

    echo userform ($lang['Listing_Title'], $title_html, '1');

    echo userform ($lang['Listing_Property_Type'], '<select name="type">' . generate_options_list(TYPES_TABLE, $form['type']) . '</select>', '1');
    echo userform ($lang['Listing_Style'], '<select name="style">' . generate_options_list(STYLES_TABLE, $form['style']) . '</select>', '1');

	// Show all available languages
	foreach ($installed_languages AS $language1 => $key1)
	{
		// Grab the right text for each textarea
		$key1 = str_replace( 'name', 'description', $key1 );
		$description_html .= '<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').toggle();">' . ucwords($language1) . '</a>&nbsp; ';
	}

	// Display text boxes for every language
	$num = 1;
	foreach ($installed_languages AS $language1 => $key1)
	{
		// Grab the right text for each textarea
		$strip = str_replace( 'name', 'description', $key1 );
		$key1 = str_replace( 'name', 'description', $key1 );

		if ($num == 1)
			$display = 'normal';
		else
			$display = 'none';

		$description_html .= '
		<div style="display: ' . $display . ';" id="' . $key1 . '">
		<textarea class="ckeditor" cols="100" rows="20" class="formSubmitArea" name="' . $key1 . '" id="' . $key1 . '">' . unsafehtml($form[$strip]) . '</textarea>
		</div>
		';

		$num++;
	}

   echo userform ($lang['Listing_Description'], $description_html, '1');

    echo userform ($lang['Listing_Lot_Size'], '<input type="text" size="45" name="size" value="' . $form['size'] . '" maxlength="50">');
    echo userform ($lang['Listing_Dimensions'], '<input type="text" size="45" name="dimensions" value="' . $form['dimensions'] . '" maxlength="50">');
    echo userform ($lang['Listing_Bathrooms'], '<input type="text" size="45" name="bathrooms" value="' . $form['bathrooms'] . '" maxlength="2">');
    echo userform ($lang['Listing_Half_Bathrooms'], '<input type="text" size="45" name="half_bathrooms" value="' . $form['half_bathrooms'] . '" maxlength="2">');
    echo userform ($lang['Listing_Bedrooms'], '<input type="text" size="45" name="bedrooms" value="' . $form['bedrooms'] . '" maxlength="2">');

	// Defaults
	if ( $form['location1_id'] != '' && $form['location1_name'] != '' )
	{
		$location1_default = '<option value="' . $form['location1_id'] . '">' . $form['location1_name'] . '</option>';
	}

	if ( $form['location2_id'] != '' && $form['location2_name'] != '' )
	{
		$location2_default = '<option value="' . $form['location2_id'] . '">' . $form['location2_name'] . '</option>';
	}

	if ( $form['location3_id'] != '' && $form['location3_name'] != '' )
	{
		$location3_default = '<option value="' . $form['location3_id'] . '">' . $form['location3_name'] . '</option>';
	}

	$locations = '
	<select name="location1" id="location1">' . $location1_default . '' . get_locations() . '</select><br />
	<select name="location2" id="location2">' . $location2_default . '</select><br />
	<select name="location3" id="location3">' . $location3_default . '</select>
	';

   echo userform( $lang['Location'], $locations, '1' );

    if (strcasecmp(@$conf['show_postal_code'], 'OFF') != 0) {
        echo userform ($lang['Zip_Code'], '<input type="text" size="45" name="zip" value="' . $form['zip'] . '" maxlength="20">');
    }
    echo userform ($lang['Listing_Address1'], '<input type="text" size="45" name="address1" value="' . $form['address1'] . '" maxlength="50">');
    echo userform ($lang['Listing_Address2'], '<input type="text" size="45" name="address2" value="' . $form['address2'] . '" maxlength="50">');

    if ($form['display_address'] == 'YES') $display_address_checked = 'CHECKED'; else $display_address_checked = '';

    echo userform ($lang['Listing_Latitude'], '<input type="text" name="latitude" value="' . $form['latitude'] . '" maxlength="50" class="formSubmitInput">');
    echo userform ($lang['Listing_Longitude'], '<input type="text" name="longitude" value="' . $form['longitude'] . '" maxlength="50" class="formSubmitInput">');

    echo userform ($lang['Listing_Display_Address'], '<input type="checkbox" name="display_address" value="YES" ' . $display_address_checked . '>');
    echo userform ($lang['Listing_Price'], '<input type="text" size="45" name="price" value="' . $form['price'] . '" maxlength="13">');
    echo userform ($lang['Listing_Directions'], '<textarea class="ckeditor" wrap="soft" cols="45" rows="4"  name="directions">' . unsafehtml($form['directions']) . '</textarea>');
    echo userform ($lang['Listing_Year_Built'], '<input type="text" size="45" name="year_built" value="' . $form['year_built'] . '" maxlength="4">');
   
	$list = generate_checkbox_list( BUILDINGS_TABLE, 'buildings', $f['buildings'], 1 );
	$list_output = '';
	foreach ( $list AS $data )
	{
		$checked = ( in_array( $data['id'], $f['buildings'] ) ) ? ' checked' : '';
		$list_output .= '<input type="checkbox" name="buildings[]" value="' . $data['id'] . '"' . $checked . '>' . $data['name'] . '<br /><br />';
	}
	
    echo userform ($lang['Listing_Additional_Out_Buildings'], $list_output );
    
	$list = generate_checkbox_list( APPLIANCES_TABLE, 'appliances', $f['appliances'], 1 );
	$list_output = '';
	foreach ( $list AS $data )
	{
		$checked = ( in_array( $data['id'], $f['appliances'] ) ) ? ' checked' : '';
		$list_output .= '<input type="checkbox" name="appliances[]" value="' . $data['id'] . '"' . $checked . '>' . $data['name'] . '<br /><br />';
	}
    
    echo userform ($lang['Listing_Appliances_Included'], $list_output );
    
	$list = generate_checkbox_list( FEATURES_TABLE, 'features', $f['features'], 1 );
	$list_output = '';
	foreach ( $list AS $data )
	{
		$checked = ( in_array( $data['id'], $f['features'] ) ) ? ' checked' : '';
		$list_output .= '<input type="checkbox" name="features[]" value="' . $data['id'] . '"' . $checked . '>' . $data['name'] . '<br /><br />';
	}
    
    echo userform ($lang['Listing_Features'], $list_output );
 
    echo userform ($lang['Listing_Garage'], '<select name="garage">' . generate_options_list(GARAGE_TABLE, $form['garage']) . '</select>');
    echo userform ($lang['Listing_Garage_Cars'], '<input type="text" size="45" name="garage_cars" value="' . $form['garage_cars'] . '">');
    echo userform ($lang['Listing_Basement'], '<select name="basement">' . generate_options_list(BASEMENT_TABLE, $form['basement']) . '</select>');
    echo userform ($lang['Video_Tour'], '<input type="text" size="45" name="video" value=\'' . $form['video'] . '\'>');
    
	// Custom fields
	$query = "SELECT * FROM " . FIELDS_TABLE . " ORDER BY name ASC";
	$result = $db->query($query) OR error($query);
	if ($db->numrows($result) > 0) {
		while($row = $db->fetcharray($result)) {
			// Type of input
			if ($row['type'] != '') {
				if ($row['type'] == 'input') {
					echo userform ($row['name'], '<input type="text" name="' . $row['field'] . '" value="' . $form[$row['field']] . '" class="formSubmitInput">');
				} elseif ($row['type'] == 'select') {
					// Grab all options for this select
					$options = "SELECT * FROM " . VALUES_TABLE . " WHERE f_id = '" . addslashes($row['id']) . "'";
					$get_options = $db->query($options) OR error($options);
					if ($db->numrows($get_options) > 0) {
						$option_list = '';
						while($row2 = $db->fetcharray($get_options)) {
							if ($row2['id'] == $form[$row['field']]) {
								$selected = ' selected';
							} else {
								$selected = '';
							}
							$option_list .= "<option value=\"" . $row2['id'] . "\" " . $selected . ">" . $row2['name'] . "</option>";
						}
						echo userform ($row['name'], '<select name="' . $row['field'] . '" class="formSubmitSelect"><option value="">Please Select</option>' . $option_list . '</select>');
					}
				}
			}
		}
	}
	// Show calendar if enabled
	if ($conf['show_calendar'] == 'ON')
	{	
        $dates = explode(',', $form['calendar']);
        foreach ($dates as $k => $date) {
            if (strlen(trim($date)) == 0) {
                unset($dates[$k]);
                continue;
            }
            list($month, $day, $year) = explode('/', $date);
            if (date('Y-m-d') > $year.'-'.$month.'-'.$day) {
                unset($dates[$k]);
            }
        }
        $form['calendar'] = implode(',', $dates);
        if ($form['calendar']) {
            $form['calendar'] .= ',';
        }

        $calendar_output .= '
    <textarea name="calendar" id="calendar_output" style="display:none;">' . unsafehtml($form['calendar']) . '</textarea>
    <input type="hidden" name="dateTemp" id="dateTemp" value="" />
    <div id="calendar-container"></div>

    <script type="text/javascript">
    function dateAvailable(y, m, d) {
        m = (++m < 10) ? "0" + m : m;
        d = (d < 10) ? "0" + d : d;
        return document.getElementById("calendar_output").value.indexOf("" + m + "/" + d + "/" + y + ",");
    }

    function toggleDateAvailable(cal) {
        var storage = document.getElementById("calendar_output");
        var d = document.getElementById("dateTemp").value + ",";
        var dateParts = document.getElementById("dateTemp").value.split("/");
        var date = new Date(dateParts[2], dateParts[0] - 1, dateParts[1]);
        var list = storage.value;
        var pos = list.indexOf(d);
        if (pos >= 0) {
            if (list.length == 11) {
                storage.value = "";
            } else if (pos == 0) {
                storage.value = list.substring(pos + 11);
            } else {
                storage.value = list.substring(0, pos) + list.substring(pos + 11);
            }
       } else {
            storage.value += d;
       }
       cal._init(cal.firstDayOfWeek, date);
    }

    var today = new Date();
    today.setHours(0, 0, 0 ,0);
    Calendar.setup(
      {
        flat         : "calendar-container", // ID of the parent element
        dateStatusFunc : function(date, y, m, d) {
                           if (date < today) {
                            return true;
                           } else if (dateAvailable(y, m, d) >= 0) {
                            return "special";
                           }
                           return false;
                         },
          inputField  : "dateTemp",
          ifFormat    : "%m/%d/%Y",
          weekNumbers : false,
          onUpdate    : function (cal) {
            if (cal.dateClicked) {
                toggleDateAvailable(cal);
            }
          },
          range : new Array('.date('Y').', '.(date('Y') + 1).')
       }
    );
    </script>

    <script type="text/javasscript">
    (function($){
        $(document).ready(function () {
            $("#calendarBox").show();
        });
    })(jQuery);
    </script>
    <div id="calendar-legend">
        <p class="calendarAvailable"><span>'.$lang['Availability_Calendar_Vacancy'].'</span></p>
        <p class="calendarUnavailable"><span>'.$lang['Availability_Calendar_No_Vacancy'].'</span></p>
    </div>
    ';
    
    	echo userform( $lang['Availability_Calendar'], $calendar_output );
    }

    echo userform ('', '<input type="Submit" name="submit_listing" value="' . $lang['Listing_Submit'] . '">');

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

  else

   echo 'Listing ID not specified';

 }

else
{
	header( 'Location: index.php' );
	exit();
}

// Template footer
include ( PATH . '/admin/template/footer.php' );

?>