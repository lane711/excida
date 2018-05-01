<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

@set_time_limit(1800); 
ini_set('auto_detect_line_endings',true); 

// Title tag content
$title = 'IDX Import';

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {
$whereClause = "";
    if($session->fetch('role')=="SUPERUSER")
      $whereClause=" 1=1 AND site_id=".$session->fetch('site_id');
 // Navigation menu
 include ( PATH . '/admin/navigation.php' );

 // Make sure this administrator have access to this script
 adminPermissionsCheck('manage_settings', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

 if (isset($_FILES['file']['tmp_name'])) 

  {

   $in = file ($_FILES['file']['tmp_name']) or die ('<font color="red">CSV file can not be found</font> Exiting..');

   echo '<div align="left">';

   echo '<b><font color="green">CSV FILE UPLOADED SUCCESSFULLY</font></b><br /><br />';

   echo '<b><font color="green">PROCESSING...</font><br /></b>';

   foreach ($in as $key => $value)
    {

     $value = trim($value);

     $csv = explode ('","', $value);

     // Parsing values

     $idx['mls'] = $csv[2];
     $idx['company_name'] = $csv[4];
     $idx['company_phone'] = $csv[5];

     $agent_name = explode(",", $csv[7]);
     @$idx['agent_first_name'] = $agent_name[1];
     $idx['agent_last_name'] = $agent_name[0];

     $idx['agent_phone'] = $csv[8];

     if ($csv[9] != '') {
     $date_added = explode ("/", $csv[9]);
     $idx['date_added'] = $date_added[2] . '-' . $date_added[0] . '-' . $date_added[1];
     }
     else $idx['date_added'] = date("Y-m-d");

     $idx['date_updated'] = $csv[69];

     if ($csv[13] == 'S')
      $idx['property_type'] = 'Single Family';
     elseif ($csv[13] == 'M')
      $idx['property_type'] = 'Condo\Town Home';
     elseif ($csv[13] == 'B')
      $idx['property_type'] = 'Mobile Home';
     elseif ($csv[13] == 'V')
      $idx['property_type'] = 'Vacant Land';
     elseif ($csv[13] == 'C')
      $idx['property_type'] = 'Commercial';
     elseif ($csv[13] == 'D')
      $idx['property_type'] = 'Multi-Family';
     elseif ($csv[13] == 'R')
      $idx['property_type'] = 'Rental';
     else
      $idx['property_type'] = 'Other';

     if ($csv[16] == 'A')
      $idx['listing_type'] = 'Active';
     elseif ($csv[16] == 'P')
      $idx['listing_type'] = 'Pending';
     elseif ($csv[16] == 'S')
      $idx['listing_type'] = 'Sold';
     elseif ($csv[16] == 'W')
      $idx['listing_type'] = 'Withdrawn';
     elseif ($csv[16] == 'E')
      $idx['listing_type'] = 'Expired';

     $idx['title'] = $csv[14];
     $idx['description'] = $csv[15];
     $idx['price'] = $csv[17];

     $location1 = $csv[19];

     $idx['address1'] = $csv[22] . ' ' . $csv[21] . ' ' . $csv[20] . ' ' . $csv[24];
  
     $location2 = $csv[27];

     $idx['zip'] = $csv[29];

     $location3 = $csv[32];

     $idx['year_built'] = $csv[36];
     $idx['size'] = $csv[37];
     $idx['dimensions'] = $csv[38];

     if ($csv[42] == '')
      $idx['bedrooms'] = 0;
     else
      $idx['bedrooms'] = $csv[42];

     if ($csv[44] == '')
      $idx['bathrooms'] = 0;
     else
      $idx['bathrooms'] = $csv[44];

     if ($csv[45] == '')
      $idx['half_bathrooms'] = 0;
     else
      $idx['half_bathrooms'] = $csv[45];

     $idx['features'] = $csv[84];

     $idx['video'] = $csv[87];

     $idx['main_image'] = $csv[89];

     $idx['number_images'] = $csv[88];

     // 1ST LEVEL

     // Check if the 1st level location exists
     $r11 = $db->query('SELECT selector FROM ' . LOCATION1_TABLE . ' WHERE category = "' . $location1 . '"');
     $f11 = $db->fetcharray($r11);

     // If it is already there we just set the ID
     if ($db->numrows($r11) > 0)
      {
       $location1_ID = $f11['selector'];
      }
     // If we have no such location we add it
     else
      {

       $r_select = $db->query ('SELECT MAX(selector) AS maxselector FROM ' . LOCATION1_TABLE);
       $f_select = $db->fetcharray ($r_select);
       $selector=$f_select['maxselector'] + 1;

       $db->query('INSERT INTO ' . LOCATION1_TABLE . ' (selector, category) VALUES (' . $selector . ', "' . $location1 . '")');
       $location1_ID = $selector;
       echo '<b><font color="green">NEW 1ST LEVEL LOCATION ADDED ' . safehtml($location1) . '(' . $location1_ID . ')</font></b><br />';
      }

     // 2ND LEVEL

     // Check if the 2nd level location exists with this 1st level location
     $r22 = $db->query('SELECT catsubsel FROM ' . LOCATION2_TABLE . ' WHERE subcategory = "' . $location2 . '" AND catsel = ' . $location1_ID . '');
     $f22 = $db->fetcharray($r22);

     // If it is already there we just set the ID
     if ($db->numrows($r22) > 0)
      {
       $location2_ID = $f22['catsubsel'];
      }
     // If we have no such location we add it
     else
      {

       $r_select = $db->query ('SELECT MAX(catsubsel) AS maxselector FROM ' . LOCATION2_TABLE);
       $f_select = $db->fetcharray ($r_select);
       $selector=$f_select['maxselector'] + 1;

       $db->query('INSERT INTO ' . LOCATION2_TABLE . ' (catsel, catsubsel, subcategory) VALUES ("' . $location1_ID . '", ' . $selector . ', "' . $location2 . '")');
       $location2_ID = $selector;
       echo '<b><font color="green">NEW 2ND LEVEL LOCATION ADDED TO ' . $location1 . ', NAME - ' . safehtml($location2) . '(' . $location2_ID . ')</font></b><br />';
      }

     // 3RD LEVEL

     // Check if the 3rd level location exists with this 2nd level location
     $r33 = $db->query('SELECT catsubsubsel FROM ' . LOCATION3_TABLE . ' WHERE subsubcategory = "' . $location3 . '" AND catsel = ' . $location1_ID . ' AND catsubsel = ' . $location2_ID . '');
     $f33 = $db->fetcharray($r33);

     // If it is already there we just set the ID
     if ($db->numrows($r33) > 0)
      {
       $location3_ID = $f33['catsubsubsel'];
      }
     // If we have no such location we add it
     else
      {

       $r_select = $db->query ('SELECT MAX(catsubsubsel) AS maxselector FROM ' . LOCATION3_TABLE);
       $f_select = $db->fetcharray ($r_select);
       $selector=$f_select['maxselector'] + 1;

       $db->query('INSERT INTO ' . LOCATION3_TABLE . ' (catsel, catsubsel, catsubsubsel, subsubcategory) VALUES ("' . $location1_ID . '", "' . $location2_ID . '", ' . $selector . ', "' . $location3 . '")');
       $location3_ID = $selector;
       echo '<b><font color="green">NEW 3RD LEVEL LOCATION ADDED TO ' . $location1 . '/' . $location2 . ', NAME - ' . safehtml($location3) . '(' . $location3_ID . ')</font></b><br />';
      }

      $location2_IN = $location1_ID . '#' . $location2_ID . '#' . $location3_ID;


     // INSERT TYPE ($type)

     // Check if this property type already exists
     $sql = 'SELECT id FROM ' . TYPES_TABLE . ' WHERE name = "' . $idx['property_type'] . '" AND '.$whereClause.' LIMIT 1';
     $r = mysql_query($sql);

     if (mysql_numrows($r) > 0)
      {
       // If exists, we just get it's ID
       $type_array = mysql_fetch_array($r);
       $type = $type_array['id'];
      }
     else
      {
       // If not, we add this type and get the new ID
        $sql = 'INSERT INTO ' . TYPES_TABLE . ' (name,site_id) VALUES ("' . $idx['property_type'] . '","'.$session->fetch('site_id').'")';
        mysql_query($sql);
        $type = mysql_insert_id();
        echo '<b><font color="green">NEW PROPERTY TYPE ADDED ' . $idx['property_type'] . '(' . $type . ')</font></b><br />';
      }

     // INSERT FEATURES ($feature)

     $feature = 'X';
 
     // Parse the list of features
     $features_list = explode(",", $idx['features']);

     foreach ($features_list as $key1 => $value1)
      {

       // Remove quotes
       $feature_in = str_replace("\"", "", trim($value1));
  
       // Check if this feature already exists
       $sql = 'SELECT id FROM ' . FEATURES_TABLE . ' WHERE name = "' . safehtml($feature_in) . '" AND '.$whereClause.' LIMIT 1';
       $r = mysql_query($sql);
  
       // If exists, we just get the ID
       if (mysql_numrows($r) > 0)
        {
         $feature_array = mysql_fetch_array($r);
         $feature .= ':' . $feature_array['id'];
        }
         else
        {
         $sql = 'INSERT INTO ' . FEATURES_TABLE . ' (name,site_id) VALUES ("' . safehtml($feature_in) . '","'.$session->fetch('site_id').'")';
         mysql_query($sql);
         $feature .= ':' . mysql_insert_id();
         echo '<b><font color="green">FEATURE ADDED ' . safehtml($feature_in) . '<br /></font></b>';
        }
      }

     // INSERT LOCATION ($location)
  
     $sql = 'SELECT id FROM ' . LOCATIONS_TABLE . ' WHERE name = "' . $location1 . '/' . $location3 . '" AND '.$whereClause.' LIMIT 1';
     $r = mysql_query($sql);
  
     if (mysql_numrows($r) > 0)
      {
       $location_array = mysql_fetch_array($r);
       $location = $location_array['id'];
      }
     else
      {
       $sql = 'INSERT INTO ' . LOCATIONS_TABLE . ' (name,site_id) VALUES ("' . $location1 . '/' . $location3 . '","'.$session->fetch('site_id').'")';
       mysql_query($sql);
       $location = mysql_insert_id();
       echo '<b><font color="green">LOCATION ADDED ' . $location1 . '/' . $location3 . '(' . $location . ')</font></b><br />';
      }

     // INSERT LISTING TYPE ($type2)
 
     $sql = 'SELECT id FROM ' . TYPES2_TABLE . ' WHERE name = "' . $idx['listing_type'] . '" AND '.$whereClause.' LIMIT 1';
     $r = mysql_query($sql);
  
     if (mysql_numrows($r) > 0)
      {
       $type2_array = mysql_fetch_array($r);
       $type2 = $type2_array['id'];
      }
     else
      {
       $sql = 'INSERT INTO ' . TYPES2_TABLE . ' (name,site_id) VALUES ("' . $idx['listing_type'] . '","'.$session->fetch('site_id').'")';
       mysql_query($sql);
       $type2 = mysql_insert_id();
       echo '<b><font color="green">LISTING TYPE ADDED ' . $idx['listing_type'] . '(' . $type2 . ')</font></b><br />';
      }



     // INSERT USER IF NOT EXIST OR GET THIS USER ID
  
     $sql = 'SELECT id FROM ' . USERS_TABLE . ' WHERE first_name = "' . $idx['agent_first_name'] . '" AND last_name = "' . $idx['agent_last_name'] . '" AND company_name = "' . $idx['company_name'] . '" AND phone = "' . $idx['agent_phone'] . '" AND '.$whereClause.' LIMIT 1';
     $r = mysql_query($sql);
  
     if (mysql_numrows($r) > 0)
      {
       $user_array = mysql_fetch_array($r);
       $user = $user_array['id'];
      }
       else
      {
       $sql = 'INSERT INTO ' . USERS_TABLE . ' (approved,site_id, first_name, last_name, company_name, phone, login, password, date_added, ip_added) VALUES (1,"'.$session->fetch('site_id').'" "' . $idx['agent_first_name'] . '", "' . $idx['agent_last_name'] . '",  "' . $idx['company_name'] . '", "' . $idx['agent_phone'] . '", "login", "' . md5('password') . '", "' . date('Y-m-d') . '", "127.0.0.1")';
       mysql_query($sql);
       $user = mysql_insert_id();
       $sql = 'UPDATE ' . USERS_TABLE . ' SET login = "login' . $user. '" WHERE id = ' . $user . ' AND '.$whereClause;
       mysql_query($sql);
       echo '<b><font color="green">NEW USER ADDED ' . $idx['agent_first_name'] . ' ' . $idx['agent_last_name'] . '(' . $user . ')</font></b><br />';
      }


     // INSERT INTO PROPERTY

     $sql = 'INSERT INTO ' . PROPERTIES_TABLE . ' (
     userid,site_id, display_address, approved,
     mls, type, type2, 
     title, description, 
     bedrooms, bathrooms, half_bathrooms,
     location2, address1, address2, zip,
     price, 
     features, 
     size, dimensions, year_built,
     video,
     date_added, ip_added
     )
      VALUES (
     ' . $user . ','.$session->fetch('site_id').', "YES", 1,
     "' . $idx['mls'] . '", "' . $type . '", "' . $type2 . '",
     "' . $idx['title'] . '", "' . $idx['description'] . '",
     "' . $idx['bedrooms'] . '", "' . $idx['bathrooms'] . '", "' . $idx['half_bathrooms'] . '",
     "' . $location2_IN . '", "' . $idx['address1'] . '", "", "' . $idx['zip'] . '",
     "' . $idx['price'] . '",
     "' . $idx['features'] . '",
     "' . $idx['size'] . '", "' . $idx['dimensions'] . '", "' . $idx['year_built'] . '",
     "' . $idx['video'] . '",
     "' . $idx['date_added'] . '", "127.0.0.1"
     )';

     mysql_query($sql) or die (mysql_error());

     $property = mysql_insert_id();

     update_categories ('' , $type);
     
     echo '<b><font color="green">LISTING ADDED: ' . $idx['title'] . '(' . $property . ')</font></b><br />';

     ob_flush();
     flush();

     // INSERT MAIN IMAGE

     if ($idx['main_image'] != '' && (isset($_POST['main']) && $_POST['main'] == 'true'))
      {

      // UPLOAD MAIN IMAGE & GENERATE THUMBNAIL
      $mainimage_URL = $idx['main_image'];

     // Copy the uploaded fullsize image into the $folder
     if (@!copy ( $mainimage_URL, PATH . '/images/' . $property . '.jpg' ) || @exif_imagetype($mainimage_URL) != IMAGETYPE_JPEG)
      echo '<font color="red">The main image seems to be incorrect / does not exist for property id - ' . $property . '</font><br />';
     else
      {
       // Change file permissions to 777
       chmod ( PATH . '/images/' . $property . '.jpg' , 0777 );
     
       // Creating a resized image using GD
       $image_path = PATH . '/images/' . $property . '.jpg';
       $image_info = getimagesize ( $image_path);
     
       // Creating a new image
       $image_new = imagecreatefromjpeg ( $image_path ) or error ('Critical Error', 'GD library is not installed, can\'t upload image');
     
       // Calculating a new height of an image
       $new = imagesy ( $image_new ) / ( imagesx ( $image_new ) / $conf['image_resampled_width'] );
     
       if ( function_exists ( 'imagecreatetruecolor' ) )
        $resampled_image = imagecreateTrueColor ( $conf['image_resampled_width'], $new );
       else
        $resampled_image = imagecreate ( $conf['image_resampled_width'], $new);

       // Creating a new image on the disk
       ImageCopyResampled ( $resampled_image, $image_new, 0, 0, 0, 0, imagesx( $resampled_image ), imagesy ( $resampled_image ), imagesx ( $image_new ), imagesy ( $image_new ) );
       Imagejpeg( $resampled_image, PATH . '/images/' . $property . '-resampled.jpg' , 80);
     
       chmod (PATH . '/images/' . $property . '-resampled.jpg', 0777);

       echo '<font color="green"> - MAIN IMAGE ADDED FOR ' . $idx['title'] . '(' . $property . ')</font><br />';

       ob_flush();
       flush();

       $sql = 'UPDATE ' . PROPERTIES_TABLE . ' SET image_uploaded = 1 WHERE id = ' . $property .' AND '.$whereClause.')';

       mysql_query($sql);

      }



      }

     // INSERT IMAGES INTO PHOTO GALLERY
     if ($idx['number_images'] > 1 && (isset($_POST['gallery']) && $_POST['gallery'] == 'true')) {

     $file_parsed = explode ("/", $idx['main_image']);
     $last_element = count($file_parsed) - 1;
     $file_name = explode(".", $file_parsed[$last_element]);

     array_pop($file_parsed);
     $file_path = $file_parsed;
     
     for ($i = 1; $i < $idx['number_images']; $i++){
     if ($i == 1) $images[$i] = $idx['main_image'];
     else $images[$i] = implode("/", $file_path) . '/' . $file_name[0] . '_' . $i . '.' . $file_name[1];
     }

     foreach ($images as $key2 => $value2)
      {
       $image_URL = $value2;

       $sql = 'INSERT INTO ' . GALLERY_TABLE . ' (userid, listingid, title, date_added)
        VALUES (' . $user . ', ' . $property . ', "Additional Property Photo", "' .  date('Y-m-d') . '")';

       mysql_query($sql);

       // GET IMAGE ID

       $image = mysql_insert_id();

     // UPLOAD IMAGE & GENERATE THUMBNAIL
     // Copy the uploaded fullsize image into the $folder
     if (@!copy ( $image_URL, PATH . '/gallery/' . $image . '.jpg' )  || @exif_imagetype($image_URL) != IMAGETYPE_JPEG)
      {
       echo '<font color="red">The gallery image seems to be incorrect / does not exist for property id - ' . $property . ', image id - ' . $image . '</font><br />';
       $sql = 'DELETE FROM ' . GALLERY_TABLE . ' WHERE id = ' . $image. ')';
       mysql_query($sql);
      }
     else
      {
       copy ( $image_URL, PATH . '/gallery/' . $image . '.jpg' )
       or error ( 'Critical Error', 'Can\'t upload JPG file into the /gallery, please, check folder permissions.');

       // Change file permissions to 777
       chmod ( PATH . '/gallery/' . $image . '.jpg' , 0777 );

       // Creating a resized image using GD
       $image_path = PATH . '/gallery/' . $image . '.jpg';
       $image_info = getimagesize ( $image_path);

       // Creating a new image
       $image_new = imagecreatefromjpeg ( $image_path ) or error ('Critical Error', 'GD library is not installed, can\'t upload image');
      
       // Calculating a new height of an image
       $new = imagesy ( $image_new ) / ( imagesx ( $image_new ) / $conf['gallery_resampled_width'] );

       if ( function_exists ( 'imagecreatetruecolor' ) )
        $resampled_image = imagecreateTrueColor ( $conf['gallery_resampled_width'], $new );
       else
        $resampled_image = imagecreate ( $conf['gallery_resampled_width'], $new);

       // Creating a new image on the disk
       ImageCopyResampled ( $resampled_image, $image_new, 0, 0, 0, 0, imagesx( $resampled_image ), imagesy ( $resampled_image ), imagesx ( $image_new ), imagesy ( $image_new ) );
       Imagejpeg( $resampled_image, PATH . '/gallery/' . $image . '-resampled.jpg' , 80);

       chmod (PATH . '/gallery/' . $image . '-resampled.jpg', 0777);

       echo '<font color="green"> - GALLERY IMAGE ADDED FOR ' . $idx['title'] . '(' . $image . ')</font><br />';

       ob_flush();
       flush();

      }
      }
      }


    }

     $locator = '';

     $z = 0; $x = 0;

     $r_cat = $db->query('SELECT * FROM ' . LOCATION1_TABLE . ' ORDER BY category');

     while ($f_cat = $db->fetcharray($r_cat)) {
      $z++;
      $r_subcat = $db->query('SELECT * FROM ' . LOCATION2_TABLE . ' WHERE catsel = "' . $f_cat['selector'] . '" ORDER BY subcategory');
    
      while ($f_subcat = $db->fetcharray($r_subcat)) {
       $r_subsubcat = $db->query('SELECT * FROM ' . LOCATION3_TABLE . ' WHERE catsubsel = "' . $f_subcat['catsubsel'] . '" ORDER BY subsubcategory');
       $r_subsubcat3 = $db->query('SELECT * FROM ' . LOCATION3_TABLE . ' ORDER BY subsubcategory');
  
       while ($f_subsubcat = $db->fetcharray($r_subsubcat)) {
        $x++;
        if ($db->numrows($r_cat) == $z && $db->numrows($r_subsubcat3) == $x)
         $locator .= 'new Array(true,"' . $f_cat['selector'] . '|' . unsafehtml($f_cat['category']) . '", "' . $f_subcat['catsubsel']. '|' . unsafehtml($f_subcat['subcategory']) . '", "' . $f_subsubcat['catsubsubsel']. '|' . unsafehtml($f_subsubcat['subsubcategory']) . '") ' . "\n";
        else                                                                                                                                                                        
         $locator .= 'new Array(true,"' . $f_cat['selector'] . '|' . unsafehtml($f_cat['category']) . '", "' . $f_subcat['catsubsel']. '|' . unsafehtml($f_subcat['subcategory']) . '", "' . $f_subsubcat['catsubsubsel']. '|' . unsafehtml($f_subsubcat['subsubcategory']) . '"), ' . "\n";
       }
   
      }
   
     }
   
     $filename = '../locations.txt';
     $handle = fopen($filename, 'w+');
     fwrite($handle, $locator);
     fclose($handle);

     echo '<font color="green"> - 3 LEVEL LOCATIONS CACHE RECREATED</font><br />';

   echo '</div>';

  }

 else

  {

   echo table_header ('IDX Import Tool');
   
  echo '
  <a href="' . URL . '/admin/import.php">CSV Import</a> | 
  <a href="' . URL . '/admin/export.php">CSV Export</a> | 
  <a href="' . URL . '/admin/tools.php">3-level Locations Import</a> | 
  <a href="' . URL . '/admin/zipimport.php">ZIP Codes Radius Search Import</a> | 
  <a href="' . URL . '/admin/idx.php">IDX Import</a>
  <br /><br /><br />
  ';

?>

<form action = "<?php echo URL;?>/admin/idx.php" method="POST" enctype="multipart/form-data">

IDX CSV File: <input type="file" name="file"><br />
Fetch Main Images: <input type="checkbox" name="main" value="true" CHECKED><br />
Fetch Gallery Images: <input type="checkbox" name="gallery" value="true"><br />

<input type="submit" value="Start"><br />

</form>

<br /><br />
Notes:<br /> Use CSV text files only. Do not upload XLS or any other BINARY files. Please note that you MUST use a UTF-8 complaint text editor and file in order to process accent marks and other languages properly.<br /><br />

1. Do a complete database and your image folders backup before using this tool!<br />
2. New listings will not affect the existing records<br />
3. All new agents created will have login: loginXX, where XX is the agent ID in the database and password: password<br />
4. This tool downloads all the main and gallery images, resizes it, creates the thumbnails and stores 
this in its own manner, this can make the import process very slow, you can 
disable main or gallery images import by unchecking the checkboxes above.<br />

<?php

   echo table_footer ();

  }

 }

else
{
	header( 'Location: index.php' );
	exit();
}

// Template footer
include ( PATH . '/admin/template/footer.php' );

?>