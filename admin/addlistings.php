<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include '../config.php';
include PATH . '/defaults.php';

$title = $lang['Menu_Submit_Property'];

include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))
{

 // Navigation menu
 include ( PATH . '/admin/navigation.php' );

 // Check if this admin have a privilege to manage listings
 adminPermissionsCheck('manage_listings', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

 if ( preg_match( '/[0-9]+$/', $_REQUEST['u_id'] ) )
 {

	// Default for the image uploader to keep track of bulk image uploads
	if ( $_SESSION['image_session'] == '' )
	{
		$_SESSION['image_session'] = rand( 111111, 999999 );
	}
	
	$_SESSION['admin'] = true;
	$_SESSION['user_id'] = $_REQUEST['u_id'];
	$_SESSION['listing_id'] = '';
  
   // If the Submit button was pressed we start this routine
   if (isset($_POST['submit_listing'])
   && $_POST['submit_listing'] == $lang['Listing_Submit'])
    {

     $form = array();

     // Change checkbox arrays into a string, with ':' as a separator
     if (isset($_POST['buildings']) && is_array($_POST['buildings'])) $_POST['buildings'] = implode (':', $_POST['buildings']); else $_POST['buildings'] = '';
     if (isset($_POST['appliances']) && is_array($_POST['appliances'])) $_POST['appliances'] = implode (':', $_POST['appliances']); else $_POST['appliances'] = '';
     if (isset($_POST['features']) && is_array($_POST['features'])) $_POST['features'] = implode (':', $_POST['features']); else $_POST['features'] = '';

     if (!isset($_POST['display_address'])) $_POST['display_address'] = 'NO';

     $form = array_map('safehtml', $_POST);
     // Cut the description to the required length
     // if JavaScript is disabled
     $form['description'] = substr ($form['description'], 0, $conf['listing_description_size']);

     echo table_header ( $lang['Information'] );

     // Initially we think that no errors were found
     $count_error = 0;

     // Check for the empty or incorrect required fields

     if ( $form['location1'] == '')
     {
     	echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Location'] . '</span><br />'; $count_error++;
     }

     if (empty($form['title']) || strlen($form['title']) < 4 )
      { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Title'] . '</span><br />'; $count_error++;}

/*
     if ( $form['bathrooms'] != '' && preg_match( '/[^0-9]+$/', $form['bathrooms'] ) )
      { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Bathrooms'] . '</span><br />'; $count_error++;}

     if ( $form['half_bathrooms'] != '' && preg_match( '/[^0-9]+$/', $form['half_bathrooms']))
      { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Half_Bathrooms'] . '</span><br />'; $count_error++;}

     if ( $form['bedrooms'] != '' && preg_match( '/[^0-9]+$/', $form['bedrooms'] ) )
      { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Bedrooms'] . '</span><br />'; $count_error++;}

     if ( $form['year_built'] != '' && preg_match( '/[^0-9]+$/', $form['year_built'] ) )
      { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Year_Built'] . '</span><br />'; $count_error++;}

     if ( $form['status'] != '' && preg_match( '/[^0-9]+$/', $form['status'] ) )
      { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Status'] . '</span><br />'; $count_error++;}
*/

     if ( $form['price'] == '' )
      { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Listing_Price'] . '</span><br />'; $count_error++;}

     // Make sure the featured dates are okay
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

       $approved = 1;

        if ($form['approved'] != 1) {
            // If listing is not approved
            $approved_date = 'NULL';
        } else {
            // Newly approved
            $approved_date = 'NOW()';
        }
        
		$form['price'] = preg_replace( '/[^0-9]+/', '', $form['price'] );
        
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
       
       $form['video'] = htmlentities( $form['video'] );

		// Create a mysql query
		$sql = '
		INSERT INTO '. PROPERTIES_TABLE . '
		(
			userid, approved, mls, type, style, title, title2, title3, title4,
			title5, title6, title7, title8, title9, title10, title11, title12,
			title13, title14, title15, title16, title17, title18, title19, title20,
			title21, title22, title23, title24, title25, title26, title27, title28,
			title29, title30, description, description2, description3, description4,
			description5, description6, description7, description8, description9,
			description10, description11, description12, description13,
			description14, description15, description16, description17,
			description18, description19, description20, description21,
			description22, description23, description24, description25,
			description26, description27, description28, description29,
			description30, size, dimensions, bathrooms, half_bathrooms, bedrooms,
			location_1, location_2, location_3, zip, address1, address2, 
			display_address, price, directions, year_built, buildings, appliances, features, garage, garage_cars,
			basement, date_added, ip_added, type2, video, calendar, latitude,
			longitude, status, custom1, custom2, custom3, custom4, custom5, custom6,
			custom7, custom8, custom9, custom10, date_approved
		) 
		VALUES
		(
			"' . $form['u_id'] . '", 
			"' . $form['approved'] . '", 
			"' . $form['mls'] . '", 
			"' . $form['type']. '", 
			"' . $form['style'] . '",
			"' . $form['title']. '",
			"' . $form['title2'] . '",
			"' . $form['title3'] . '",
			"' . $form['title4'] . '",
			"' . $form['title5'] . '",
			"' . $form['title6'] . '",
			"' . $form['title7'] . '",
			"' . $form['title8'] . '",
			"' . $form['title9'] . '",
			"' . $form['title10'] . '",
			"' . $form['title11'] . '",
			"' . $form['title12'] . '",
			"' . $form['title13'] . '",
			"' . $form['title14'] . '",
			"' . $form['title15'] . '",
			"' . $form['title16'] . '",
			"' . $form['title17'] . '",
			"' . $form['title18'] . '",
			"' . $form['title19'] . '",
			"' . $form['title20'] . '",
			"' . $form['title21'] . '",
			"' . $form['title22'] . '",
			"' . $form['title23'] . '",
			"' . $form['title24'] . '",
			"' . $form['title25'] . '",
			"' . $form['title26'] . '",
			"' . $form['title27'] . '",
			"' . $form['title28'] . '",
			"' . $form['title29'] . '",
			"' . $form['title30'] . '",
			"' . $form['description'] . '",
			"' . $form['description2'] . '",
			"' . $form['description3'] . '",
			"' . $form['description4'] . '",
			"' . $form['description5'] . '",
			"' . $form['description6'] . '",
			"' . $form['description7'] . '",
			"' . $form['description8'] . '",
			"' . $form['description9'] . '",
			"' . $form['description10'] . '",
			"' . $form['description11'] . '",
			"' . $form['description12'] . '",
			"' . $form['description13'] . '",
			"' . $form['description14'] . '",
			"' . $form['description15'] . '",
			"' . $form['description16'] . '",
			"' . $form['description17'] . '",
			"' . $form['description18'] . '",
			"' . $form['description19'] . '",
			"' . $form['description20'] . '",
			"' . $form['description21'] . '",
			"' . $form['description22'] . '",
			"' . $form['description23'] . '",
			"' . $form['description24'] . '",
			"' . $form['description25'] . '",
			"' . $form['description26'] . '",
			"' . $form['description27'] . '",
			"' . $form['description28'] . '",
			"' . $form['description29'] . '",
			"' . $form['description30'] . '",
			"' . $form['size'] . '", 
			"' . $form['dimensions'] . '", 
			"' . $form['bathrooms'] . '", 
			"' . $form['half_bathrooms'] . '",
			"' . $form['bedrooms'] . '",
			"' . $form['location1'] . '",
			"' . $form['location2'] . '",
			"' . $form['location3'] . '", 
			"' . $form['zip'] . '",
			"' . $form['address1'] . '", 
			"' . $form['address2'] . '",
			"' . $form['display_address'] . '", 
			"' . $form['price'] . '",
			"' . $form['directions'] . '", 
			"' . $form['year_built'] . '", 
			"' . $form['buildings'] . '",
			"' . $form['appliances'] . '", 
			"' . $form['features'] . '",
			"' . $form['garage'] . '", 
			"' . $form['garage_cars'] . '",  
			"' . $form['basement'] . '",
			"' . date('Y-m-d') . '",
			"' . $user_ip . '", 
			"' . $form['type2'] . '", 
			"' . $form['video'] . '", 
			"' . $form['calendar'] . '",
			"' . $form['latitude'] . '", 
			"' . $form['longitude'] . '", 
			"' . $form['status'] . '",
			"' . $form['custom1'] . '", 
			"' . $form['custom2'] . '", 
			"' . $form['custom3'] . '",
			"' . $form['custom4'] . '", 
			"' . $form['custom5'] . '", 
			"' . $form['custom6'] . '",
			"' . $form['custom7'] . '", 
			"' . $form['custom8'] . '", 
			"' . $form['custom9'] . '",
			"' . $form['custom10'] . '", 
			' . $approved_date . '
		)';
       $r_listing = $db->query($sql) or error ('Critical Error', mysql_error ());
       $id = $db->getLastID();
       
		// Update the gallery table if any have a temporary ID
		$sql = "
		UPDATE " . GALLERY_TABLE . " 
		SET 
			listingid = '" . $id . "',
			temp_id = '0'
		WHERE 
			temp_id = '" . $db->makeSafe( $_SESSION['image_session'] ) . "'";
		$q = $db->query( $sql );

       // Make Listing Featured
       if ($form['package'] > 0)
        update_package ($id , $form['package']);

       // Output the 'Thank you' message
       echo $lang['Realtor_Listing_Added'];

	   // Send email alerts to all subscribers
	   check_alerts( $id, $form );
	   
      }

	// Image upload status
     if (isset($uploaded) && $uploaded)
      echo '<p align="center"><span class="warning">' . $lang['Listing_Image_Uploaded'] . '</span></p>';

     // If image was not uploaded because of the image
     // size problems etc.
     if (isset($uploaded) && !$uploaded)
      echo '<p align="center"><span class="warning">' . $lang['Listing_Image_NOT_Uploaded'] . '</span></p>';

     echo table_footer ( );

    }

   // Main form

   echo table_header ( $lang['Menu_Submit_Property'] );

	  // Default for approval
	  if ($form['approved'] == '0')
	  	$approved0 = 'selected';
	  elseif ($form['approved'] == '1' || $form['approved'] == '')
	  	$approved1 = 'selected';
	  elseif ($form['approved'] == '2')
	  	$approved2 = 'selected';

   // Output the form
   echo '
	<div id="fileuploader">Upload</div>
	<div id="status"></div>
	<br /><br />
   ';
   
   echo '
   <form action="' . URL . '/admin/addlistings.php" method="post" name="form" enctype="multipart/form-data">
   <input type="hidden" name="u_id" value="' . $_REQUEST['u_id'] . '">
   ';

   echo userform ($lang['Listing_Approval'], '<select name="approved" class="formEditSelect"><option value="1" ' . $approved1 . '>Active</option><option value="0" ' . $approved0 . '>Inactive</option><option value="2" ' . $approved2 . '>Expired</option></select>');
   echo userform ($lang['Admin_Packages_Name'], '<select name="package"><option value="0">Free</option>' . generate_packages_list($form['package']) . '</select>');
   echo userform ($lang['Module_Listing_Type'], '<select name="type2">' . generate_options_list(TYPES2_TABLE, $form['type2']) . '</select>', '1');
   echo userform ($lang['Listing_Status'], '<select name="status" class="formEditSelect">' . generate_options_list(STATUS_TABLE, $form['status']) . '</select>', '1');
   if (strcasecmp(@$conf['show_mls'], 'OFF') != 0) {
       echo userform ($lang['Listing_MLS'], '<input type="text" size="45" name="mls" value="' . $form['mls'] . '" maxlength="10">');
   }

	// Show all available languages
	foreach ($installed_languages AS $language1 => $key1)
	{
		$key1 = str_replace( 'name', 'title', $key1 );
		$title_html .= '<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').toggle();">' . ucwords($language1) . '</a>&nbsp;&nbsp;';
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
		$description_html .= '<a href="javascript:void(0);" onclick="jQuery(\'#' . $key1 . '\').toggle();">' . ucwords($language1) . '</a>&nbsp;&nbsp;';
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
		<textarea class="ckeditor" cols="100" rows="20" class="formSubmitArea" name="' . $key1 . '" id="' . $key1 . '">' . $form[$strip] . '</textarea>
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

	$locations = '
	<select name="location1" id="location1">' . get_locations() . '</select><br />
	<select name="location2" id="location2"></select><br />
	<select name="location3" id="location3"></select>
	';

   echo userform( $lang['Location'], $locations, '1' );

   if (strcasecmp(@$conf['show_postal_code'], 'OFF') != 0) {
      echo userform ($lang['Zip_Code'], '<input type="text" size="45" name="zip" value="' . $form['zip'] . '" maxlength="20">');
   }
   echo userform ($lang['Listing_Address1'], '<input type="text" size="45" name="address1" value="' . $form['address1'] . '" maxlength="50">');
   echo userform ($lang['Listing_Address2'], '<input type="text" size="45" name="address2" value="' . $form['address2'] . '" maxlength="50">');

   if (isset($form['display_address']) && $form['display_address'] == 'YES') $display_address_checked = 'CHECKED'; else $display_address_checked = '';
    echo userform ($lang['Listing_Display_Address'], '<input type="checkbox" name="display_address" value="YES" ' . $display_address_checked . '>');

   echo userform ($lang['Listing_Latitude'], '<input type="text" name="latitude" value="' . $form['latitude'] . '" maxlength="50" class="formSubmitInput">');
   echo userform ($lang['Listing_Longitude'], '<input type="text" name="longitude" value="' . $form['longitude'] . '" maxlength="50" class="formSubmitInput">');

   echo userform ($lang['Listing_Price'], '<input type="text" size="45" name="price" value="' . $form['price'] . '" maxlength="13">');
   echo userform ($lang['Listing_Directions'], '<textarea class="ckeditor" wrap="soft" cols="45" rows="4"  name="directions">' . unsafehtml($form['directions']) . '</textarea>');
   echo userform ($lang['Listing_Year_Built'], '<input type="text" size="45" name="year_built" value="' . $form['year_built'] . '" maxlength="4">');

   if (!isset($form['buildings']))
    $form['buildings'] = '';

   if (!isset($form['appliances']))
    $form['appliances'] = '';

   if (!isset($form['features']))
    $form['features'] = '';

   echo userform ($lang['Listing_Additional_Out_Buildings'], admin_generate_checkbox_list(BUILDINGS_TABLE, 'buildings', explode(':', $form['buildings'])));
   echo userform ($lang['Listing_Appliances_Included'], admin_generate_checkbox_list(APPLIANCES_TABLE, 'appliances', explode(':', $form['appliances'])));
   echo userform ($lang['Listing_Features'], admin_generate_checkbox_list(FEATURES_TABLE, 'features', explode(':', $form['features'])));
   echo userform ($lang['Listing_Garage'], '<select name="garage">' . generate_options_list(GARAGE_TABLE, $form['garage']) . '</select>');
   echo userform ($lang['Listing_Garage_Cars'], '<input type="text" size="45" name="garage_cars" value="' . $form['garage_cars'] . '">');
   echo userform ($lang['Listing_Basement'], '<select name="basement">' . generate_options_list(BASEMENT_TABLE, $form['basement']) . '</select>');
   echo userform ($lang['Video_Tour'], '<input type="text" size="45" name="video" value=\'' . $form['video'] . '\'>');

	// Custom fields
	$query = "SELECT * FROM " . FIELDS_TABLE . " ORDER BY name ASC";
	$result = $db->query($query) OR error( 'Critical Error:' . $query);
	if ($db->numrows($result) > 0) {
		while($row = $db->fetcharray($result)) {
			// Type of input
			if ($row['type'] != '') {
				if ($row['type'] == 'input') {
					echo userform ($row['name'], '<input type="text" name="' . $row['field'] . '" class="formSubmitInput">');
				} elseif ($row['type'] == 'select') {
					// Grab all options for this select
					$options = "SELECT * FROM " . VALUES_TABLE . " WHERE f_id = '" . addslashes($row['id']) . "'";
					$get_options = $db->query($options) OR error( 'Critical Error:' . $options);
					if ($db->numrows($get_options) > 0) {
						$option_list = '';
						while($row2 = $db->fetcharray($get_options)) {
							$option_list .= "<option value=\"" . $row2['id'] . "\">" . $row2['name'] . "</option>";
						}
						echo userform ($row['name'], '<select name="' . $row['field'] . '" class="formSubmitSelect"><option value="">Please Select</option>' . $option_list . '</select>');
					}
				}
			}
		}
	}
	// If showing calendar
	if ($conf['show_calendar'] == 'ON')
	{	
        $dates = explode(',', $form['calendar']);
        foreach ($dates as $k => $date) {
            if (strlen(trim($date)) == 0) {
                unset($dates[$k]);
                continue;
            }
            list($month, $day, $year) = explode('/');
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

   // Submit button
   echo userform ('', '<input type="Submit" name="submit_listing" value="' . $lang['Listing_Submit'] . '">');

   echo '</form>';

   echo table_footer ();
  }

 else
 	echo 'No user selected';
 }
else
{
	header( 'Location: index.php' );
	exit();
}

include ( PATH . '/admin/template/footer.php' );

?>