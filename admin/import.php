<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

@set_time_limit(1800); 
ini_set('auto_detect_line_endings',true); 

// Title tag content
$title = 'CSV Import';

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {

 // Navigation menu
 include ( PATH . '/admin/navigation.php' );

 // Make sure this administrator have access to this script
 adminPermissionsCheck('manage_settings', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');
$whereClause = "";
    if($session->fetch('role')=="SUPERUSER")
      $whereClause=" 1=1 AND site_id=".$session->fetch('site_id');
 if (isset($_FILES['file']['tmp_name'])) 

  {

   $in = file ($_FILES['file']['tmp_name']) or die ('<font color="red">CSV file can not be found</font> Exiting..');

   echo '<div align="left">';

   echo '<b><font color="green">CSV FILE UPLOADED SUCCESSFULLY</font></b><br /><br />';

   echo '<b><font color="green">PROCESSING...</font><br /></b>';

   foreach ($in as $key => $value)
    {

     $value = trim($value);

     // We use ; as a delimiter
     $csv = explode (';', $value);

     // 3 level locations

     $location1 = $csv[16];
     $location2 = $csv[17];
     $location3 = $csv[18];

     // 1ST LEVEL

     // Check if the 1st level location exists
     $r11 = $db->query('SELECT selector FROM ' . LOCATION1_TABLE . ' WHERE category = "' . $location1 . '" AND '.$whereClause);
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

       $db->query('INSERT INTO ' . LOCATION1_TABLE . ' (selector, category) VALUES (' . $selector . ', "' . safehtml($csv[16]) . '")');
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

       if ($csv[17] != '') {
        $r_select = $db->query ('SELECT MAX(catsubsel) AS maxselector FROM ' . LOCATION2_TABLE);
        $f_select = $db->fetcharray ($r_select);
        $selector=$f_select['maxselector'] + 1;
 
        $db->query('INSERT INTO ' . LOCATION2_TABLE . ' (catsel, catsubsel, subcategory) VALUES ("' . $location1_ID . '", ' . $selector . ', "' . safehtml($csv[17]) . '")');
        $location2_ID = $selector;
        echo '<b><font color="green">NEW 2ND LEVEL LOCATION ADDED TO ' . $location1 . ', NAME - ' . safehtml($location2) . '(' . $location2_ID . ')</font></b><br />';
       }
       else
        $location2_ID = '0';

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
       if ($csv[18] != '') {
        $r_select = $db->query ('SELECT MAX(catsubsubsel) AS maxselector FROM ' . LOCATION3_TABLE);
        $f_select = $db->fetcharray ($r_select);
        $selector=$f_select['maxselector'] + 1;

        $db->query('INSERT INTO ' . LOCATION3_TABLE . ' (catsel, catsubsel, catsubsubsel, subsubcategory) VALUES ("' . $location1_ID . '", "' . $location2_ID . '", ' . $selector . ', "' . safehtml($csv[18]) . '")');
        $location3_ID = $selector;
        echo '<b><font color="green">NEW 3RD LEVEL LOCATION ADDED TO ' . $location1 . '/' . $location2 . ', NAME - ' . safehtml($location3) . '(' . $location3_ID . ')</font></b><br />';
       }
       else
        $location3_ID = '0';

      }

      $location2_IN = $location1_ID . '#' . $location2_ID . '#' . $location3_ID;

     // INSERT TYPE ($type)

     // Check if this property type already exists
     $sql = 'SELECT id FROM ' . TYPES_TABLE . ' WHERE name = "' . safehtml($csv[0]) . '" AND '.$whereClause.' LIMIT 1';
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
        $sql = 'INSERT INTO ' . TYPES_TABLE . ' (name,site_id) VALUES ("' . safehtml($csv[0]) . '","' . $session->fetch('site_id') . '")';
        mysql_query($sql);
        $type = mysql_insert_id();
        echo '<b><font color="green">NEW TYPE ADDED ' . safehtml($csv[0]) . '(' . $type . ')</font></b><br />';
      }

     // INSERT FEATURES ($feature)

     $feature = 'X';
 
     // Parse the list of features
     $features_list = explode(",", $csv[3]);

     foreach ($features_list as $key => $value)
      {

       // Remove quotes
       $feature_in = str_replace("\"", "", trim($value));
  
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
         $sql = 'INSERT INTO ' . FEATURES_TABLE . ' (name, site_id) VALUES ("' . safehtml($feature_in) . '","'.$session->fetch('site_id').'")';
         mysql_query($sql);
         $feature .= ':' . mysql_insert_id();
         echo '<b><font color="green">FEATURE ADDED ' . safehtml($feature_in) . '<br /></font></b>';
        }
      }

  
     // INSERT LOCATION ($location)
  
     $sql = 'SELECT id FROM ' . LOCATIONS_TABLE . ' WHERE name = "' . safehtml($csv[19]) . '" AND '.$whereClause.' LIMIT 1';
     $r = mysql_query($sql);
  
     if (mysql_numrows($r) > 0)
      {
       $location_array = mysql_fetch_array($r);
       $location = $location_array['id'];
      }
     else
      {
       $sql = 'INSERT INTO ' . LOCATIONS_TABLE . ' (name,site_id) VALUES ("' . safehtml($csv[19]) . '","'.$session->fetch('site_id').'")';
       mysql_query($sql);
       $location = mysql_insert_id();
       echo '<b><font color="green">LOCATION ADDED ' . safehtml($csv[19]) . '(' . $location . ')</font></b><br />';
      }

     // INSERT LISTING TYPE ($type2)
 
     $sql = 'SELECT id FROM ' . TYPES2_TABLE . ' WHERE name = "' . safehtml($csv[10]) . '" AND '.$whereClause.' LIMIT 1';
     $r = mysql_query($sql);
  
     if (mysql_numrows($r) > 0)
      {
       $type2_array = mysql_fetch_array($r);
       $type2 = $type2_array['id'];
      }
     else
      {
       $sql = 'INSERT INTO ' . TYPES2_TABLE . ' (name,site_id) VALUES ("' . safehtml($csv[10]) . '","'.$session->fetch('site_id').'")';
       mysql_query($sql);
       $type2 = mysql_insert_id();
       echo '<b><font color="green">LISTING TYPE ADDED ' . safehtml($csv[10]) . '(' . $type2 . ')</font></b><br />';
      }

     // INSERT USER IF NOT EXIST OR GET THIS USER ID
  
     $sql = 'SELECT id FROM ' . USERS_TABLE . ' WHERE first_name = "' . safehtml($csv[12]) . '" AND company_name = "' . safehtml($csv[11]) . '" AND phone = "' . safehtml($csv[13]) . '" AND '.$whereClause.' LIMIT 1';
     $r = mysql_query($sql);
  
     if (mysql_numrows($r) > 0)
      {
       $user_array = mysql_fetch_array($r);
       $user = $user_array['id'];
      }
       else
      {
       $sql = 'INSERT INTO ' . USERS_TABLE . ' (approved,site_id first_name, company_name, phone, login, password, date_added, ip_added) VALUES (1,"'.$session->fetch('site_id').'" "' . safehtml($csv[12]) . '", "' . safehtml($csv[11]) . '", "' . safehtml($csv[13]) . '", "login", "' . md5('password') . '", "' . date('Y-m-d') . '", "127.0.0.1")';
       mysql_query($sql);
       $user = mysql_insert_id();
       $sql = 'UPDATE ' . USERS_TABLE . ' SET login = "login' . $user. '" WHERE id = ' . $user . ' AND '.$whereClause;
       mysql_query($sql);
       echo '<b><font color="green">NEW USER ADDED ' . safehtml($csv[12]) . '(' . $user . ')</font></b><br />';
      }

     // INSERT INTO PROPERTY

     $sql = 'INSERT INTO ' . PROPERTIES_TABLE . ' (
     display_address,
     site_id,
     approved,
     title, 
     description, 
     bedrooms, 
     bathrooms,
     address1,
     address2,
     price, 
     features, 
     type,
     type2, 
     dimensions, 
     userid, 
     location2, 
     size,
     date_added,
     ip_added
     )
      VALUES (
     "YES",
     "'.$session->fetch('site_id').'",
     1,
     "' . safehtml(trim($csv[1])) . '", 
     "' . safehtml(trim($csv[2])) . '", 
     "' . safehtml(trim($csv[4])) . '", 
     "' . safehtml(trim($csv[5])) . '", 
     "' . safehtml(trim($csv[6])) . '", 
     "' . safehtml(trim($csv[7])) . '", 
     "' . trim($csv[8]) . '",
     "' . $feature . '",
     ' . $type . ', 
     ' . $type2 . ', 
     "' . safehtml(trim($csv[9])) . '", 
     ' . $user . ', 
     "' . $location2_IN . '", 
     "' . safehtml(trim($csv[9])) . '",
     "' . date('Y-m-d') . '",
     "127.0.0.1"
     )';

     mysql_query($sql) or die (mysql_error());

     $property = mysql_insert_id();

     update_categories ('' , $type);
     
     echo '<b><font color="green">LISTING ADDED: ' . safehtml($csv[1]) . '(' . $property . ')</font></b><br />';

     // INSERT MAIN IMAGE

     if ($csv[15] != '')
      {
       // UPLOAD MAIN IMAGE & GENERATE THUMBNAIL

      $mainimage_URL = str_replace("\"", "", trim($csv[15]));

     // Copy the uploaded fullsize image into the $folder
     if (@!copy ( $mainimage_URL, PATH . '/images/' . $property . '.jpg' )  || @exif_imagetype($mainimage_URL) != IMAGETYPE_JPEG)
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

       echo '<font color="green"> - MAIN IMAGE ADDED FOR ' . safehtml($csv[1]) . '(' . $property . ')</font><br />';

       $sql = 'UPDATE ' . PROPERTIES_TABLE . ' SET image_uploaded = 1 WHERE id = ' . $property .' AND '.$whereClause.')';
       mysql_query($sql);

      }
      }

     // INSERT IMAGES INTO PHOTO GALLERY

     $images = explode(",", $csv[14]);

     foreach ($images as $key => $value)
      {
       $image_URL = str_replace("\"", "", trim($value));
       $sql = 'INSERT INTO ' . GALLERY_TABLE . ' (userid, listingid, title, date_added)
        VALUES (' . $user . ', ' . $property . ', "property photo", "' .  date('Y-m-d') . '")';

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

       echo '<font color="green"> - GALLERY IMAGE ADDED FOR ' . safehtml($csv[1]) . '(' . $image . ')</font><br />';

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

    }

   echo '</div>';

  }

 else

  {

   echo table_header ('CSV Import Tool');

  echo '
  <a href="' . URL . '/admin/import.php">CSV Import</a> | 
  <a href="' . URL . '/admin/export.php">CSV Export</a> | 
  <a href="' . URL . '/admin/tools.php">3-level Locations Import</a> | 
  <a href="' . URL . '/admin/zipimport.php">ZIP Codes Radius Search Import</a> | 
  <a href="' . URL . '/admin/idx.php">IDX Import</a>
  <br /><br /><br />
  ';

?>

<form action = "<?php echo URL;?>/admin/import.php" method="POST" enctype="multipart/form-data">

CSV File: <input type="file" name="file"><input type="submit" value="Start"><br />

</form>

<br /><br />
Notes:<br /> Use CSV text files only. Do not upload XLS or any other BINARY files. Please note that you MUST use a UTF-8 complaint text editor and file in order to process accent marks and other languages properly.<br /><br />

1. Do a complete database and your image folders backup before using this tool!<br />
2. New listings will not affect the existing records<br />
3. Add one listing per line in the CSV source<br />
4. Delimit CSV values with ;<br />
5. Add URLs to only JPEG images, check the images size;<br />
6. All new agents created will have login: loginXX, where XX is the agent ID in the database and password: password<br />
7. The exact format for the CSV (if you need more options, please, contact PMR support to have that added):<br />

<br /><div style="word-break: break-all; width: 100%; font-size:10px;">PropertyType;Title;Description;Feature1,Feature2;Bedrooms;Bathrooms;Address1;Address2;Price;Size;ListingType;AgentCompanyName;AgentName;AgentPhone;gallerypic1,gallerypic2;mainimage;Location1;Location2;Location3;AgentLocation(1level)</div><br /><br />

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