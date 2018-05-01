<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

// ----------------------------------------------------------------------
// ADMIN PANEL / EDIT FEATURED AGENTS PACKAGES

// Title tag
$title = $lang['Admin_Agent_Packages'];

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {
  // Include navigation panel
  include ( PATH . '/admin/navigation.php' );

  // Make sure this administrator have access to this script
  adminPermissionsCheck('manage_settings', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

  // If the form is submitted we save the data
  if (isset($_POST['update_packages_free']) && $_POST['update_packages_free'] == $lang['Admin_Packages_Update'])
   {

    echo table_header ( $lang['Information'] );

    $form = array_map ( 'safehtml', $_POST );

    $db->query ('UPDATE ' . CONFIGURATION_TABLE . ' SET val = "' . $form['free_listings'] . '" WHERE name = "free_listings"');
    $db->query ('UPDATE ' . CONFIGURATION_TABLE . ' SET val = "' . $form['free_gallery'] . '" WHERE name = "free_gallery"');
    $db->query ('UPDATE ' . CONFIGURATION_TABLE . ' SET val = "' . $form['free_mainimage'] . '" WHERE name = "free_mainimage"');
    $db->query ('UPDATE ' . CONFIGURATION_TABLE . ' SET val = "' . $form['free_photo'] . '" WHERE name = "free_photo"');
    $db->query ('UPDATE ' . CONFIGURATION_TABLE . ' SET val = "' . $form['free_phone'] . '" WHERE name = "free_phone"');
    $db->query ('UPDATE ' . CONFIGURATION_TABLE . ' SET val = "' . $form['free_address'] . '" WHERE name = "free_address"');

    echo $lang['Admin_Packages_Added'];

    echo table_footer ( );

   }

  if (isset($_POST['submit_packages']) && $_POST['submit_packages'] == $lang['Admin_Packages_Submit'])
   {

    echo table_header ( $lang['Information'] );

    // Get the selected checkboxes from the form
    $form1 = array('name'=>'', 'price'=>'', 'days'=>'', 'position'=>'', 'listings'=>'', 'gallery'=>'', 'mainimage'=>'', 'photo'=>'', 'phone'=>'', 'address'=>'');

    $form2 = array_map ( 'safehtml', $_POST );

    $form = array_merge ($form1, $form2);

    // Initially we think that no errors were found
    $count_error = 0;

    // Check if privilege is already exist
    $sql = 'SELECT name FROM ' . PACKAGES_AGENT_TABLE . ' WHERE name = "' . $form['name'] . '"';
    $r = $db->query($sql) or error ('Critical Error', mysql_error () );

    if ($db->fetcharray($r) > 0 )
     { echo $lang['Admin_Packages_Used'] . '<br />'; $count_error++;}

    if (empty($form['name']) || strlen($form['name']) < 2 )
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Packages_Name'] . '</span><br />'; $count_error++;}

    if ($count_error == 0)

     {
      // Update the privileges record in the database
      $sql = 'INSERT INTO ' . PACKAGES_AGENT_TABLE . '
              (name, price, days, position, listings, gallery, 
	       mainimage, photo,
               phone, address)
              VALUES 
              ("' . $form['name'] . '", "' . $form['price'] . '", "' . $form['days'] . '",
               "' . $form['2co'] . '", "' . $form['listings'] . '", "' . $form['gallery'] . '", 
	       "' . $form['mainimage'] . '", "' . $form['photo'] . '", 
               "' . $form['phone'] . '", "' . $form['address'] . '")';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Packages_Added'];

     }

    echo table_footer ( );

   }

  // If the form was updated we save the data
  if (isset($_POST['update_packages']) && $_POST['update_packages'] == $lang['Admin_Packages_Update'])
   {

    echo table_header ( $lang['Information'] );

    // Get the selected checkboxes from the form
    $form1 = array('name'=>'', 'price'=>'', 'days'=>'', 'position'=>'', 'listings'=>'', 'gallery'=>'', 'mainimage'=>'', 'photo'=>'', 'phone'=>'', 'address'=>'');

    $form2 = array_map ( 'safehtml', $_POST );

    $form = array_merge ($form1, $form2);

    // Initially we think that no errors were found
    $count_error = 0;

    if (empty($form['name']) || strlen($form['name']) < 2 )
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Packages_Name'] . '</span><br />'; $count_error++;}

    if ($count_error == 0)

     {

      // Update the privileges record in the database
      $sql = 'UPDATE ' . PACKAGES_AGENT_TABLE . ' SET
              name = "' . $form['name'] . '",
              price = "' . $form['price'] . '",
              days = "' . $form['days'] . '",
              position = "' . $form['2co'] . '",
              listings = "' . $form['listings'] . '",
              gallery = "' . $form['gallery'] . '",
              mainimage = "' . $form['mainimage'] . '",
              photo = "' . $form['photo'] . '",
              phone = "' . $form['phone'] . '",
              address = "' . $form['address'] . '"
              WHERE id = "' . $form['id'] . '"';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Packages_Updated'];

     }

    echo table_footer ();

   }

  // If the privilege was  removed we clean the data
  if (isset($_POST['remove_packages']) && $_POST['remove_packages'] == $lang['Admin_Packages_Remove'])
   {

    echo table_header ( $lang['Information'] );

    // Get the selected checkboxes from the form
    $form = array_map ( 'safehtml', $_POST );

    $count_error = 0;

    if ($count_error == 0)

     {

      // Update the privileges record in the database
      $sql = 'DELETE FROM ' . PACKAGES_AGENT_TABLE . ' 
              WHERE id = "' . $form['id'] . '"';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Packages_Removed'];

     }

    echo table_footer();

   }

  // Fetching the privileges data from the database
  $sql = 'SELECT * FROM ' . PACKAGES_AGENT_TABLE;
  $r = $db->query( $sql );

  // Generating the privileges form
  echo table_header ( $lang['Admin_Agent_Packages'] );

  echo '
  <a href="' . URL . '/admin/packages.php">' . $lang['Admin_Packages'] . '</a> | 
  <a href="' . URL . '/admin/agentpackages.php">' . $lang['Admin_Agent_Packages'] . '</a>
  <br /><br /><br />';

// FREE PACKAGE

    echo '<form action = "' . URL . '/admin/agentpackages.php" method="POST"> ';
    echo '<div class="col12 warning">FREE</div>';

    // Reread configuration from the database
    $sql = 'SELECT name, val FROM ' . CONFIGURATION_TABLE . ' WHERE name LIKE "free_%"';
    $r2 = $db->query ($sql) or error ( 'Critical Error', mysql_error () );

    $conf2 = array ();
  
    while ( $conf_array = $db->fetcharray ($r2) ) 
     {
      $conf2[$conf_array['name']] = $conf_array['val'];
     }

    echo packageform ('FREE ' . $lang['Admin_Agent_Packages_Listings'], 'free_listings', $conf2['free_listings']);
    echo packageform ('FREE ' . $lang['Admin_Agent_Packages_Gallery'], 'free_gallery', $conf2['free_gallery']);
    echo packageform ('FREE ' . $lang['Admin_Agent_Packages_Mainimage'], 'free_mainimage', $conf2['free_mainimage']);
    echo packageform ('FREE ' . $lang['Admin_Agent_Packages_Photo'], 'free_photo', $conf2['free_photo']);
    echo packageform ('FREE ' . $lang['Admin_Agent_Packages_Phone'], 'free_phone', $conf2['free_phone']);
    echo packageform ('FREE ' . $lang['Admin_Agent_Packages_Address'], 'free_address', $conf2['free_address']);

    echo '<br clear="both" />';

    // Update / Remove Buttons
    echo '<input type="submit" name="update_packages_free" value="' . $lang['Admin_Packages_Update'] . '"><br /><br /> ';

    echo '</form>';

  while ($f = $db->fetcharray( $r ))

   {

    echo '<form action = "' . URL . '/admin/agentpackages.php" method="POST"> ';
    echo '<div class="col12 warning">' . $f['name'] . '</div> <input type="hidden" name="id" value="' . $f['id'] . '">';

    echo packageform ($lang['Admin_Packages_Name'], 'name', $f['name']);
    echo packageform ($lang['Admin_Agent_Packages_Price'], 'price', $f['price']);
    echo packageform ($lang['Admin_Packages_Days'], 'days', $f['days']);
    echo packageform ($lang['Admin_Agent_Packages_Listings'], 'listings', $f['listings']);
    echo packageform ($lang['Admin_Agent_Packages_Gallery'], 'gallery', $f['gallery']);
    echo packageform ($lang['Admin_Agent_Packages_Mainimage'], 'mainimage', $f['mainimage']);
    echo packageform ($lang['Admin_Agent_Packages_Photo'], 'photo', $f['photo']);
    echo packageform ($lang['Admin_Agent_Packages_Phone'], 'phone', $f['phone']);
    echo packageform ($lang['Admin_Agent_Packages_Address'], 'address', $f['address']);

    echo packageform ('2co product ID', '2co', $f['position']);

    echo ' <br />';

    // Update / Remove Buttons
    echo '<input type="submit" name="update_packages" value="' . $lang['Admin_Packages_Update'] . '"> ';
    echo '  <input type="submit" name="remove_packages" value="' . $lang['Admin_Packages_Remove'] . '"><br /><br />';

    echo '</form>';

   }

  echo '<br />';

  echo '<form action = "' . URL . '/admin/agentpackages.php" method="POST"> ';
  echo '<div class="col12 warning">' . $lang['Admin_Packages_Submit'] . '</div>';

  echo packageform ($lang['Admin_Packages_Name'], 'name', $f['name']);
  echo packageform ($lang['Admin_Agent_Packages_Price'], 'price', $f['price']);
  echo packageform ($lang['Admin_Packages_Days'], 'days', $f['days']);
  echo packageform ($lang['Admin_Agent_Packages_Listings'], 'listings', $f['listings']);
  echo packageform ($lang['Admin_Agent_Packages_Gallery'], 'gallery', $f['gallery']);
  echo packageform ($lang['Admin_Agent_Packages_Mainimage'], 'mainimage', $f['mainimage']);
  echo packageform ($lang['Admin_Agent_Packages_Photo'], 'photo', $f['photo']);
  echo packageform ($lang['Admin_Agent_Packages_Phone'], 'phone', $f['phone']);
  echo packageform ($lang['Admin_Agent_Packages_Address'], 'address', $f['address']);

  echo packageform ('2co product ID', '2co', $f['position']);
 
  echo '<br />';

  // Submit Button
  echo '<input type="submit" name="submit_packages" value="' . $lang['Admin_Packages_Submit'] . '">';

  echo ' </form>
       '; 

  echo table_footer ();

 }

else
{
	header( 'Location: index.php' );
	exit();
}

// Template footer
include ( PATH . '/admin/template/footer.php' );

?>