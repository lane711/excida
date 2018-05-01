<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

// ----------------------------------------------------------------------
// ADMIN PANEL / EDIT FEATURED PACKAGES

// Title tag
$title = $lang['Admin_Packages'];

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {
  // Include navigation panel
  include ( PATH . '/admin/navigation.php' );
  $whereClause = "";
  if($session->fetch('role')=="SUPERUSER")
  $whereClause=" AND site_id=".$session->fetch('site_id');
  // Make sure this administrator have access to this script
  adminPermissionsCheck('manage_settings', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

  // If the form is submitted we save the data
  if (isset($_POST['submit_packages']) && $_POST['submit_packages'] == $lang['Admin_Packages_Submit'])
   {

    echo table_header ( $lang['Information'] );

    // Get the selected checkboxes from the form
    $form1 = array('name'=>'', 'price'=>'', 'days'=>'', 'position'=>'');

    $form2 = array_map ( 'safehtml', $_POST );

    $form = array_merge ($form1, $form2);

    // Initially we think that no errors were found
    $count_error = 0;

    // Check if privilege is already exist
    $sql = 'SELECT name FROM ' . PACKAGES_TABLE . ' WHERE name = "' . $form['name'] . '"'.$whereClause;
    $r = $db->query($sql) or error ('Critical Error', mysql_error () );

    if ($db->fetcharray($r) > 0 )
     { echo $lang['Admin_Packages_Used'] . '<br />'; $count_error++;}

    if (empty($form['name']) || strlen($form['name']) < 2 )
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Packages_Name'] . '</span><br />'; $count_error++;}

    if ($count_error == 0)

     {
      // Update the privileges record in the database
      $sql = 'INSERT INTO ' . PACKAGES_TABLE . '
              (name,site_id, price, days, position)
              VALUES 
              ("' . $form['name'] . '","' . $session->fetch('site_id') . '", "' . $form['price'] . '", "' . $form['days'] . '",
               "' . $form['2co'] . '")';

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
    $form1 = array('name'=>'', 'price'=>'', 'days'=>'', 'position'=>'');

    $form2 = array_map ( 'safehtml', $_POST );

    $form = array_merge ($form1, $form2);

    // Initially we think that no errors were found
    $count_error = 0;

    if (empty($form['name']) || strlen($form['name']) < 2 )
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Packages_Name'] . '</span><br />'; $count_error++;}

    if ($count_error == 0)

     {

      // Update the privileges record in the database
      $sql = 'UPDATE ' . PACKAGES_TABLE . ' SET
              name = "' . $form['name'] . '",
              price = "' . $form['price'] . '",
              days = "' . $form['days'] . '",
              position = "' . $form['2co'] . '"
              WHERE id = "' . $form['id'] . '"'.$whereClause;

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
      $sql = 'DELETE FROM ' . PACKAGES_TABLE . ' 
              WHERE id = "' . $form['id'] . '"'.$whereClause;

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Packages_Removed'];

     }

    echo table_footer();

   }

  // Fetching the privileges data from the database
  $sql = 'SELECT * FROM ' . PACKAGES_TABLE.' WHERE 1=1 '.$whereClause;
  $r = $db->query( $sql );

  // Generating the privileges form
  echo table_header ( $lang['Admin_Packages'] );

  echo '
  <a href="' . URL . '/admin/packages.php">' . $lang['Admin_Packages'] . '</a> | 
  <a href="' . URL . '/admin/agentpackages.php">' . $lang['Admin_Agent_Packages'] . '</a>
  <br /><br /><br />';
  
  while ($f = $db->fetcharray( $r ))
   {
   
    echo '<form action = "' . URL . '/admin/packages.php" method="POST"> ';
    echo '<div class="warning">' . $f['name'] . '</span> <input type="hidden" name="id" value="' . $f['id'] . '"></div>';

    echo packageform ($lang['Admin_Packages_Name'], 'name', $f['name']);
    echo packageform ($lang['Admin_Packages_Price'], 'price', $f['price']);
    echo packageform ($lang['Admin_Packages_Days'], 'days', $f['days']);
    echo packageform ('2co product ID', '2co', $f['position']);
    
    echo '<br clear="both">';

    // Update / Remove Buttons
    echo '<input type="submit" name="update_packages" value="' . $lang['Admin_Packages_Update'] . '">  ';
    echo '<input type="submit" name="remove_packages" value="' . $lang['Admin_Packages_Remove'] . '">';

    echo '</form>';
    
    echo '<br clear="both"><br /><br />';
  }
   
  echo '<br />';

  echo '<form action = "' . URL . '/admin/packages.php" method="POST"> ';
  echo '<div class="warning">' . $lang['Admin_Packages_Submit'] . '</div>';

  echo packageform ($lang['Admin_Packages_Name'], 'name', $f['name']);
  echo packageform ($lang['Admin_Packages_Price'], 'price', $f['price']);
  echo packageform ($lang['Admin_Packages_Days'], 'days', $f['days']);
  echo packageform ('2co product ID', '2co', $f['position']);
  
  echo '<br clear="both">';

  // Submit Button
  echo '<input type="submit" name="submit_packages" value="' . $lang['Admin_Packages_Submit'] . '">';
  echo '</form>'; 
  
  echo '<br clear="both"><br /><br />';

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