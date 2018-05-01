<?php

define( 'PMR', true );
define( 'PMRADMIN', true );

include ( '.././config.php' );
include ( PATH . '/defaults.php' );

// ----------------------------------------------------------------------
// ADMIN PANEL / EDIT ADMINISTRATORS PRIVILEGES

// Title tag
$title = $lang['Admin_Edit_Privileges'];

// Template header
include ( PATH . '/admin/template/header.php' );

// If logged we can start the page output
if (adminAuth($session->fetch('adminlogin'), $session->fetch('adminpassword')))

 {
  // Include navigation panel
  include ( PATH . '/admin/navigation.php' );

  // Make sure this administrator have access to this script
  adminPermissionsCheck('manage_administrators', $session->fetch('adminlogin')) or error ('Critical Error', 'Incorrect privileges');

  // If the form is submitted we save the data
  if (isset($_POST['submit_levels']) && $_POST['submit_levels'] == $lang['Admin_Privileges_Submit'])
   {

    echo table_header ( $lang['Information'] );

    // Get the selected checkboxes from the form
    $form1 = array('privilege'=>'', 'manage_users'=>'', 'manage_listings'=>'', 'manage_gallery'=>'',
		'manage_types'=>'', 'manage_settings'=>'', 'manage_administrators'=>'');

    $form2 = array_map ( 'safehtml', $_POST );

    $form = array_merge ($form1, $form2);

    // Initially we think that no errors were found
    $count_error = 0;

    // Check if privilege is already exist
    $sql = 'SELECT privilege FROM ' . PRIVILEGES_TABLE . ' WHERE privilege = "' . $form['privilege'] . '"';
    $r = $db->query($sql) or error ('Critical Error', mysql_error () );

    if ($db->fetcharray($r) > 0 )
     { echo $lang['Admin_Privileges_Used'] . '<br />'; $count_error++;}

    if (empty($form['privilege']) || strlen($form['privilege']) < 2 )
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Privileges_Name'] . '</span><br />'; $count_error++;}

    if ($form['privilege'] == 'SUPERUSER')
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Privileges_Name'] . ' SUPERUSER</span><br />'; $count_error++;}
 
    if ($count_error == 0)

     {
      // Update the privileges record in the database
      $sql = 'INSERT INTO ' . PRIVILEGES_TABLE . '
              (privilege, manage_users, manage_listings, manage_gallery,
               manage_types, manage_settings, manage_administrators)
              VALUES 
              ("' . $form['privilege'] . '", "' . $form['manage_users'] . '", "' . $form['manage_listings'] . '",
               "' . $form['manage_gallery'] . '", "' . $form['manage_types'] . '",
               "' . $form['manage_settings'] . '", "' . $form['manage_administrators'] . '")';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Privileges_Added'];

     }

    echo table_footer ( );

   }

  // If the form was updated we save the data
  if (isset($_POST['update_levels']) && $_POST['update_levels'] == $lang['Admin_Privileges_Update'])
   {

    echo table_header ( $lang['Information'] );

    // Get the selected checkboxes from the form
    $form1 = array('privilege'=>'', 'manage_users'=>'', 'manage_listings'=>'', 'manage_gallery'=>'',
		   'manage_types'=>'', 'manage_settings'=>'', 'manage_administrators'=>'');

    $form2 = array_map ( 'safehtml', $_POST );

    $form = array_merge ($form1, $form2);

    // Initially we think that no errors were found
    $count_error = 0;

    if (empty($form['privilege']) || strlen($form['privilege']) < 2 )
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Privileges_Name'] . '</span><br />'; $count_error++;}

    if ($form['privilege'] == 'SUPERUSER')
     { echo $lang['Field_Empty'] . ' - <span class="warning">' . $lang['Admin_Privileges_Name'] . ' SUPERUSER</span><br />'; $count_error++;}

    if ($count_error == 0)

     {

      // Update the privileges record in the database
      $sql = 'UPDATE ' . PRIVILEGES_TABLE . ' SET
              privilege = "' . $form['privilege'] . '",
              manage_users = "' . $form['manage_users'] . '",
              manage_listings = "' . $form['manage_listings'] . '",
              manage_gallery = "' . $form['manage_gallery'] . '",
              manage_types = "' . $form['manage_types'] . '",
              manage_settings = "' . $form['manage_settings'] . '",
              manage_administrators = "' . $form['manage_administrators'] . '"
              WHERE id = "' . $form['id'] . '"';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Privileges_Updated'];

     }

    echo table_footer ();

   }

  // If the privilege was  removed we clean the data
  if (isset($_POST['remove_levels']) && $_POST['remove_levels'] == $lang['Admin_Privileges_Remove'])
   {

    echo table_header ( $lang['Information'] );

    // Get the selected checkboxes from the form
    $form = array_map ( 'safehtml', $_POST );

    $count_error = 0;

    if ($count_error == 0)

     {

      // Update the privileges record in the database
      $sql = 'DELETE FROM ' . PRIVILEGES_TABLE . ' 
              WHERE id = "' . $form['id'] . '"';

      $db->query( $sql ) or error ('Critical Error', mysql_error () );

      echo $lang['Admin_Privileges_Removed'];

     }

    echo table_footer();

   }

  // Fetching the privileges data from the database
  $sql = 'SELECT * FROM ' . PRIVILEGES_TABLE;
  $r = $db->query( $sql );

  // Generating the privileges form
  echo table_header ( $lang['Admin_Edit_Privileges'] );
  
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

  echo ' <table cellpadding="5" cellspacing="0" border="0">';

  echo ' <tr><td align="left" valign="top"> <span class="warning">"SUPERUSER" - Super User Priveleges</span> </td></tr>';
  echo ' <tr><td align="left" valign="top"> ' . $lang['Admin_Privileges_Super_Enabled'] . ' </td></tr>';

  echo ' <tr><td align="left" valign="top"> <br /> </td></tr> ';

  while ($f = $db->fetcharray( $r ))

   {

    echo ' <tr><td align="left" valign="top"></td></tr>';
    echo '<form action = "' . URL . '/admin/privileges.php" method="POST"> ';
    echo ' <tr><td align="left" valign="top"> <span class="warning">' . $f['privilege'] . '</span> </td></tr>';
    echo ' <tr><td align="left" valign="top">Privilege Name <input type="text" name="privilege" value="' . $f['privilege'] . '" maxlength="20"><input type="hidden" name="id" value="' . $f['id'] . '"></td></tr>';

    echo privilegeform ($lang['Admin_Privileges_Users'], 'manage_users', 'YES', $f['manage_users']);
    echo privilegeform ($lang['Admin_Privileges_Listings'], 'manage_listings', 'YES', $f['manage_listings']);
    echo privilegeform ($lang['Admin_Privileges_Gallery'], 'manage_gallery', 'YES', $f['manage_gallery']);
    echo privilegeform ($lang['Admin_Privileges_Types'], 'manage_types', 'YES', $f['manage_types']);
    echo privilegeform ($lang['Admin_Privileges_Settings'], 'manage_settings', 'YES', $f['manage_settings']);
    echo privilegeform ($lang['Admin_Privileges_Administrators'], 'manage_administrators', 'YES', $f['manage_administrators']);

    echo ' <tr><td align="left" align="top"> <br /> </td></tr>';

    // Update / Remove Buttons
    echo ' <tr><td align="left" valign="top"> <input type="submit" name="update_levels" value="' . $lang['Admin_Privileges_Update'] . '"> ';
    echo '  <input type="submit" name="remove_levels" value="' . $lang['Admin_Privileges_Remove'] . '"></td></tr>';

    echo '</form>';

   }

  echo ' <tr><td align="left" align="top"> <br /> </td></tr>';

  echo '<form action = "' . URL . '/admin/privileges.php" method="POST"> ';
  echo ' <tr><td align="left" valign="top"> <span class="warning">' . $lang['Admin_Privileges_Submit'] . '</span> </td></tr>';
  echo ' <tr><td align="left" valign="top">' . $lang['Admin_Privileges_Name'] . ' <input type="text" name="privilege" value="" maxlength="20"></td></tr>';

  echo privilegeform ($lang['Admin_Privileges_Users'], 'manage_users', 'YES', '');
  echo privilegeform ($lang['Admin_Privileges_Listings'], 'manage_listings', 'YES', '');
  echo privilegeform ($lang['Admin_Privileges_Gallery'], 'manage_gallery', 'YES', '');
  echo privilegeform ($lang['Admin_Privileges_Types'], 'manage_types', 'YES', '');
  echo privilegeform ($lang['Admin_Privileges_Settings'], 'manage_settings', 'YES', '');
  echo privilegeform ($lang['Admin_Privileges_Administrators'], 'manage_administrators', 'YES', '');
 
  echo ' <tr><td align="left" valign="top"> <br /> </td></tr>';

  // Submit Button
  echo ' <tr><td align="left" valign="top"> <input type="submit" name="submit_levels" value="' . $lang['Admin_Privileges_Submit'] . '"></td></tr>';

  echo ' </form>
	</table>
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